<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InvoicePomdes;
use App\Models\Transaction;
use Illuminate\Support\Facades\Validator;

class InvoicePomdesController extends Controller
{
    public function store(Request $request){
        try{
            $attributes = [
                'transaction_id' => 'Transaksi',
                'nominal' => 'Nominal'
            ];

            $messages = [
                'required' => ':attribute tidak boleh kosong.',
                'numeric' => ':attribute harus berupa angka.'
            ];

            $validatedData = Validator::make($request->all(),[
                'transaction_id' => 'required',
                'nominal' => 'required|numeric'
            ],$messages,$attributes);

            if($validatedData->fails()){
                return $this->getResponse([],$validatedData->getMessageBag(),422);
            }

            $transaction =  Transaction::find($request->transaction_id);

            if(!$transaction){
                return $this->getResponse([],'Transaksi tidak ditemukan',404);
            }

            $invoice = InvoicePomdes::create($request->all());

            if(!$invoice){
                return $this->getResponse([],'Tagihan gagal disimpan',500);
            }

            return $this->getResponse($invoice,'Tagihan berhasil disimpan');
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }
}
