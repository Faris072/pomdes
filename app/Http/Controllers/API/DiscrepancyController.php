<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\DiscrepancyType;
use App\Models\Transaction;

class DiscrepancyController extends Controller
{
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
                'name' => 'Nama laporan ketidaksesuaian',
                'description' => 'Deskripsi',
                'price' => 'Total harga',
                'volume' => 'Total volume',
                'fuel_discrepancy.*.fuel_transaction_id' => 'Id transaksi BBM',
                'fuel_discrepancy.*.discrepancy_type_id' => 'Tipe ketidaksesuaian',
                'fuel_discrepancy.*.discrepancy_volume' => 'Volume ketidaksesuaian',
                'fuel_discrepancy.*.discrepancy_price' => 'Harga ketidaksesuaian',
            ];

            $messages = [
                'required' => ':attribute tidak boleh kosong.',
                'numeric' => ':attribute harus berupa angka.',
            ];

            $validatedData = Validator::make($request->all(),[
                'name' => 'required',
                'description' => '',
                'price' => 'required|numeric',
                'volume' => 'required|numeric',
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

            foreach($request->fuel_discrepancy as $fd){
                $totalPrice += (int) $fd['discrepancy_price'];
                $totalVolume += (int) $fd['discrepancy_volume'];
            }

            if((int) $request->price == $totalPrice ||(int) $request->volume == $totalVolume ){}
            else{
                return $this->getResponse([],'Total BBM tidak sesuai',403);
            }
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }
}
