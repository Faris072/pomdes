<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\models\Transaction;
use App\models\Delivery;
use App\models\DeliveryFiles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeliveryController extends Controller
{
    public function save(Request $request, $id){
        try{
            $attributes = [
                'estimation_date' => 'Estimasi sampai',
            ];

            $messages = [
                'required' => ':attribute tidak boleh kosong.'
            ];

            $validatedData = Validator::make($request->all(),[
                'estimation_date' => 'required'
            ],$messages,$attributes);

            if($validatedData->fails()){
                return $this->getResponse([],$validatedData->getMessageBag(),422);
            }

            $transaction = Transaction::with(['delivery'])->find($id);
            if(!$transaction){
                return $this->getResponse([],'Data transaksi tidak ditemukan',404);
            }

            if(!$transaction->delivery){
                $delivery = Delivery::create([
                    'transaction_id' => $id,
                    'estimation_date' => $request->estimation_date,
                    'description' => $request->description
                ]);

                if(!$delivery){
                    return $this->getResponse([],'Data gagal disimpan',500);
                }

                $deleteFile = DeliveryFiles::where('delivery_id',$delivery->id)->delete();

                foreach($request->file as $f){
                    $data = (object) [
                        'original' => $f,
                        'delivery_id' => $delivery->id,
                        'name' => $f->getClientOriginalName(),
                        'extension' => $f->getClientOriginalExtension(),
                        'is_image' => substr($f->getMimeType(), 0, 5) == 'image' ? true : false,
                        'size' => $f->getSize(),
                    ];
                    $data->name = date('now').rand('10000','99999').'.'.$data->extension;

                    $file = DeliveryFiles::create((array) $data);
                    if(!$file){
                        DeliveryFiles::where('delivery_id',$delivery->id)->forceDelete();
                        DeliveryFiles::onlyTrashed()->where('delivery_id',$delivery->id)->restore();
                        return $this->getResponse([],'File gagal disimpan',500);
                    }

                    $data->original->storeAs('delivery', $data->name);
                }

                DeliveryFiles::onlyTrashed()->where('delivery_id',$delivery->id)->delete();
                return $this->getResponse(Delivery::with('delivery_files')->find($delivery->id),'Data berhasil disimpan',200);
            }

            $delivery = Delivery::firstWhere('transaction_id',$id);
            if(!$delivery){
                return $this->getResponse([],'Data tidak ditemukan',500);
            }

            $update = $delivery->update([
                'estimation_date' => $request->estimation_date,
                'description' => $request->description
            ]);
            $deleteFile = DeliveryFiles::where('delivery_id',$delivery->id)->delete();

            foreach($request->file as $f){
                $data = (object) [
                    'original' => $f,
                    'delivery_id' => $delivery->id,
                    'name' => $f->getClientOriginalName(),
                    'extension' => $f->getClientOriginalExtension(),
                    'is_image' => substr($f->getMimeType(), 0, 5) == 'image' ? true : false,
                    'size' => $f->getSize(),
                ];
                $data->name = date('now').rand('10000','99999').'.'.$data->extension;

                $file = DeliveryFiles::create((array) $data);
                if(!$file){
                    DeliveryFiles::where('delivery_id',$delivery->id)->forceDelete();
                    DeliveryFiles::onlyTrashed()->where('delivery_id',$delivery->id)->restore();
                    return $this->getResponse([],'File gagal disimpan',500);
                }

                $data->original->storeAs('delivery', $data->name);
            }

            DeliveryFiles::onlyTrashed()->where('delivery_id',$delivery->id)->forceDelete();
            return $this->getResponse(Delivery::with('delivery_files')->find($delivery->id),'Data berhasil disimpan',200);

        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }
}
