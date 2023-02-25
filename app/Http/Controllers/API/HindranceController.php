<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Hindrance;
use App\Models\HindranceFiles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class HindranceController extends Controller
{
    public function save(Request $request, $id){
        try{
            $attributes = [
                'description' => 'Estimasi sampai',
            ];

            $messages = [
                'required' => ':attribute tidak boleh kosong.'
            ];

            $validatedData = Validator::make($request->all(),[
                'description' => 'required'
            ],$messages,$attributes);

            if($validatedData->fails()){
                return $this->getResponse([],$validatedData->getMessageBag(),422);
            }

            $transaction = Transaction::with(['hindrance'])->find($id);
            if(!$transaction){
                return $this->getResponse([],'Data transaksi tidak ditemukan',404);
            }

            if(!$transaction->hindrance){
                $hindrance = Hindrance::create([
                    'transaction_id' => $id,
                    'description' => $request->description
                ]);

                if(!$hindrance){
                    return $this->getResponse([],'Data gagal disimpan',500);
                }

                $deleteFile = HindranceFiles::where('hindrance_id',$hindrance->id)->delete();

                foreach($request->file as $f){
                    $data = (object) [
                        'original' => $f,
                        'hindrance_id' => $hindrance->id,
                        'name' => $f->getClientOriginalName(),
                        'extension' => $f->getClientOriginalExtension(),
                        'is_image' => substr($f->getMimeType(), 0, 5) == 'image' ? true : false,
                        'size' => $f->getSize(),
                    ];
                    $data->name = date('now').rand('10000','99999').'.'.$data->extension;

                    $file = HindranceFiles::create((array) $data);
                    if(!$file){
                        HindranceFiles::where('hindrance_id',$hindrance->id)->forceDelete();
                        HindranceFiles::onlyTrashed()->where('hindrance_id',$hindrance->id)->restore();
                        return $this->getResponse([],'File gagal disimpan',500);
                    }

                    $data->original->storeAs('hindrance', $data->name);
                }

                HindranceFiles::onlyTrashed()->where('hindrance_id',$hindrance->id)->delete();
                return $this->getResponse(Hindrance::with('hindrance_files')->find($hindrance->id),'Data berhasil disimpan',200);
            }

            $hindrance = Hindrance::firstWhere('transaction_id',$id);
            if(!$hindrance){
                return $this->getResponse([],'Data tidak ditemukan',500);
            }

            $update = $hindrance->update([
                'description' => $request->description
            ]);
            if(!$update){
                return $this->getResponse([],'Data gagal disimpan',500);
            }

            $deleteFile = HindranceFiles::where('hindrance_id',$hindrance->id)->delete();

            foreach($request->file as $f){
                $data = (object) [
                    'original' => $f,
                    'hindrance_id' => $hindrance->id,
                    'name' => $f->getClientOriginalName(),
                    'extension' => $f->getClientOriginalExtension(),
                    'is_image' => substr($f->getMimeType(), 0, 5) == 'image' ? true : false,
                    'size' => $f->getSize(),
                ];
                $data->name = date('now').rand('10000','99999').'.'.$data->extension;

                $file = HindranceFiles::create((array) $data);
                if(!$file){
                    HindranceFiles::where('hindrance_id',$hindrance->id)->forceDelete();
                    HindranceFiles::onlyTrashed()->where('hindrance_id',$hindrance->id)->restore();
                    return $this->getResponse([],'File gagal disimpan',500);
                }

                $data->original->storeAs('hindrance', $data->name);
            }

            HindranceFiles::onlyTrashed()->where('hindrance_id',$hindrance->id)->forceDelete();
            return $this->getResponse(Hindrance::with('hindrance_files')->find($hindrance->id),'Data berhasil disimpan',200);

        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function render_file($id){
        try{
            $file = HindranceFiles::find($id)->name;
            return response()->file(Storage::path('hindrance/'.$file));
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }
}
