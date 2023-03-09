<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\DiscrepancyType;
use App\Models\Discrepancy;
use App\Models\FuelDiscrepancy;
use App\Models\DiscrepancyFiles;
use App\Models\Transaction;
use Illuminate\Support\Facades\Storage;

class DiscrepancyController extends Controller
{
    public function check_discrepancy(Request $request){
        try{
            if(auth()->user()->role_id == 1){
                $user_id = $request->user_id;
            }
            else if(auth()->user()->role_id == 3){
                $user_id = auth()->user()->id;
            }
            else{
                return $this->getResponse([],'Akses ditolak',403);
            }

            $transaction = Transaction::with(['fuel_transactions.fuel.supplier','discrepancy.fuel_discrepancies.fuel_transaction.fuel','discrepancy.discrepancy_files','discrepancy.fuel_discrepancies.discrepancy_type'])->where('user_id', $user_id)->whereHas('discrepancy', function($q){
                $q->where('is_active',true);
            })->first();

            if($transaction && $transaction->discrepancy && $transaction->discrepancy->discrepancy_files){
                foreach($transaction->discrepancy->discrepancy_files as $df){
                    $df->link = route('render-discrepancy-files', $df->id);
                }
            }

            return $this->getResponse($transaction,'Data berhasil ditampilkan');
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function select_discrepancy_type(Request $request){
        try{
            $discrepancyType = DiscrepancyType::with([]);
            if(isset($request->search)){
                $discrepancyType = $discrepancyType->whereRaw("LOWER(name) LIKE '%".strtolower($request->search)."%'");
            }
            if(isset($request->limit)){
                $discrepancyType = $discrepancyType->limit($request->limit);
            }

            $discrepancyType = $discrepancyType->get();

            if(!$discrepancyType){
                return $this->getResponse([],'Data gagal ditampilkan',500);
            }

            return $this->getResponse($discrepancyType, 'Data berhasil ditampilkan');
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function create(Request $request){
        try{
            if(auth()->user()->role_id == 1 || auth()->user()->role_id == 3){}
            else{
                return $this->getResponse([],'Akses ditolak.',403);
            }

            $transaction = Transaction::with(['status'])->find($request->transaction_id);
            if(!$transaction){
                return $this->getResponse([],'Transaksi tidak ditemukan', 404);
            }

            if($transaction->status_id != 10){
                return $this->getResponse([],'Akses ditolak. Status tidak sesuai', 403);
            }

            $attributes = [
                'transaction_id' => 'Transaksi',
                'description' => 'Deskripsi',
                'fuel_discrepancy.*.fuel_transaction_id' => 'Id transaksi BBM',
                'fuel_discrepancy.*.discrepancy_type_id' => 'Tipe ketidaksesuaian',
                'fuel_discrepancy.*.discrepancy_volume' => 'Volume ketidaksesuaian',
                'fuel_discrepancy.*.discrepancy_price' => 'Harga ketidaksesuaian',
            ];

            $messages = [
                'required' => ':attribute tidak boleh kosong.',
                'numeric' => ':attribute harus berupa angka.',
                'unique' => ':attribute sudah ada.'
            ];

            $validatedData = Validator::make($request->all(),[
                'transaction_id' => 'required|unique:discrepancy,transaction_id',
                'description' => '',
                'fuel_discrepancy.*.fuel_transaction_id' => 'required|numeric',
                'fuel_discrepancy.*.discrepancy_type_id' => 'required|numeric',
                'fuel_discrepancy.*.discrepancy_volume' => 'required|numeric',
                'fuel_discrepancy.*.discrepancy_price' => 'required|numeric',
            ],$messages,$attributes);

            if($validatedData->fails()){
                return $this->getResponse([],$validatedData->getMessageBag(),422);
            }

            $totalPrice = 0;
            $totalVolume = 0;

            if($request->fuel_discrepancy){
                foreach($request->fuel_discrepancy as $fd){
                    if($fd['discrepancy_type_id'] == 1){
                        $totalPrice += (int) $fd['discrepancy_price'];
                        $totalVolume += (int) $fd['discrepancy_volume'];
                    }
                    else{
                        $totalPrice -= (int) $fd['discrepancy_price'];
                        $totalVolume -= (int) $fd['discrepancy_volume'];
                    }
                }
            }
            else{
                return $this->getResponse([],'BBM yang tidak sesuai harus dipilih salah satu atau lebih',422);
            }

            $discrepancy = Discrepancy::create([
                'transaction_id' => $request->transaction_id,
                'description' => $request->description,
                'price' => $totalPrice,
                'volume' => $totalVolume,
                'is_active' => true
            ]);
            if(!$discrepancy){
                return $this->getResponse([],'Laporan ketidaksesuaian gagal disimpan.',500);
            }

            if($request->fuel_discrepancy){
                foreach($request->fuel_discrepancy as $fd){
                    $fuelDiscrepancy = FuelDiscrepancy::create([
                        'discrepancy_id' => $discrepancy->id,
                        'fuel_transaction_id' => $fd['fuel_transaction_id'],
                        'discrepancy_type_id' => $fd['discrepancy_type_id'],
                        'discrepancy_volume' => $fd['discrepancy_volume'],
                        'discrepancy_price' => $fd['discrepancy_price']
                    ]);

                    if(!$fuelDiscrepancy){
                        Discrepancy::find($discrepancy->id)->delete();
                        FuelDiscrepancy::firstWhere('discrepancy_id',$discrepancy->id)->delete();
                        return $this->getResponse([],'Data BBM ketidaksesuaian gagal disimpan',500);
                    }
                }
            }

            $updateStatus = $transaction->update(['status_id' => 11]);
            if(!$updateStatus){
                return $this->getResponse([],'Status gagal diubah',500);
            }

            return $this->getResponse(Discrepancy::with(['fuel_discrepancies','discrepancy_files'])->find($discrepancy->id), 'Laporan ketidaksesuaian berhasil dikirimkan', 200);

        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function upload_file(Request $request,$id){
        try{
            $discrepancy = Discrepancy::find($id);
            if(!$discrepancy){
                return $this->getResponse([],'Laporan ketidaksesuaian tidak ditemukan',404);
            }

            if(!$request->all()){
                return $this->getResponse([],'File tidak boleh kosong',422);
            }

            $discrepancyFiles = DiscrepancyFiles::where('discrepancy_id',$id);
            if($discrepancyFiles){
                foreach($discrepancyFiles->get() as $df){
                    Storage::delete('discrepancy/'.$df->name);
                };
                $discrepancyFiles->forceDelete();
            }

            foreach($request->all()['file'] as $f){
                $data = (object) [
                    'original' => $f,
                    'discrepancy_id' => $id,
                    'name' => $f->getClientOriginalName(),
                    'extension' => $f->getClientOriginalExtension(),
                    'is_image' => substr($f->getMimeType(), 0, 5) == 'image' ? true : false,
                    'size' => $f->getSize(),
                ];

                $data->name = date('now').rand('10000','99999').'.'.$data->extension;

                $query = DiscrepancyFiles::create((array) $data);
                if(!$query){
                    DiscrepancyFiles::firstWhere('discrepancy_id',$id)->forceDelete();
                    return $this->getResponse([],'File gagal diupload.',500);
                }

                $data->original->storeAs('discrepancy', $data->name);
            }

            return $this->getResponse([],'File berhasil disimpan',200);

        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function render_file($id){
        $query = DiscrepancyFiles::find($id);
        return response()->file(Storage::path('discrepancy/'.$query->name));
    }
}
