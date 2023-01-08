<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\InvoicePusat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Transaction;

class InvoicePusatController extends Controller
{
    public function store(Request $request){
        try{
            $attributes = [
                'transaction_id' => 'Transaksi',
                'nominal' => 'Nominal',
            ];

            $messages = [
                'required' => ':attribute tidak boleh kosong.',
                'numeric' => ':attribute tidak boleh kosong.'
            ];

            $validatedData = Validator::make($request->all(),[
                'trasaction_id' => 'required|numeric',
                'nominal' => 'required|numeric'
            ],$messages,$attributes);

            if($validatedData->fails()){
                return $this->getResponse([],$validatedData->getMessageBag(),422);
            }

            $transaction = Transaction::find($request->transaction_id);

            if(!$transaction){
                return $this->getResponse([],'Data transaksi tidak ditemukan',404);
            }

            $exist = InvoicePusat::firstWhere('transaction_id',$request->transaction_id);

            if($exist){
                return $this->getResponse([],'Tagihan pusat sudah ada.',422);
            }

            $query = InvoicePusat::create($request->all());

            if(!$query){
                return $this->getResponse([],'Data gagal disimpan',500);
            }

            return $this->getResponse($query,'Data berhasil disimpan');
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function get(){
        try{
            $invoice = InvoicePusat::with(['transaction','transaction.user','invoice_pusat_files'])->get();

            if(!$invoice){
                return $this->getResponse([],'Data gagal dimuat.',500);
            }

            return $this->getResponse($invoice,'Data berhasil ditampilkan');
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function show($id){
        try{
            $query = InvoicePusat::with(['transaction','transaction.user','invoice_pusat_files'])->find($id);
            if(!$query){
                return $this->getResponse([],'Data tidak ditemukan',404);
            }

            $this->getResponse($query,'Data berhasil ditampilkan');
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function update(Request $request, $id){
        try{
            $invoice = InvoicePusat::find($id);
            if(!$invoice){
                return $this->getResponse([],'Data tidak ditemukan',404);
            }

            $attributes = [
                'transaction_id' => 'Transaksi',
                'nominal' => 'Nominal',
            ];

            $messages = [
                'required' => ':attribute tidak boleh kosong.',
                'numeric' => ':attribute harus berupa angka.'
            ];

            $validatedData = Validator::make($request->all(),[
                'nominal' => 'required|numeric',
            ],$messages,$attributes);

            if($validatedData->fails()){
                return $this->getResponse([],$validatedData->getMessageBag(),422);
            }

            $query = $invoice->update($request->all());

            if(!$query){
                return $this->getResponse([],'Data gagal disimpan',500);
            }

            return $this->getResponse(InvoicePusat::find($id),'Data berhasil diubah.');
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function delete($id){
        try{
            $invoice = InvoicePusat::find($id);
            if(!$invoice){
                return $this->getResponse([],'Tagihan tidak ditemukan',404);
            }

            $delete = $invoice->delete();

            if(!$delete){
                return $this->getResponse([],'Data gagal dihapus',500);
            }

            return $this->getResponse($invoice,'Data berhasil dihapus');
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function get_trash(){
        try{
            $invoice = InvoicePusat::with(['transaction','transaction.user','invoice_pusat_files'])->onlyTrashed()->get();

            if(!$invoice){
                return $this->getResponse([],'Data gagal dimuat',500);
            }

            return $this->getResponse($invoice,'Data berhasil ditampilkan');
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function show_trash($id){
        try{
            $invoice = InvoicePusat::with(['transaction','transaction.user','invoice_pusat_files'])->onlyTrashed()->find($id);
            if(!$invoice){
                return $this->getResponse([],'Tagihan tidak ditemukan',404);
            }

            return $this->getResponse($invoice,'Data berhasil ditampilkan');
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function destroy($id){
        try{
            $invoice = InvoicePusat::find($id);
            if(!$invoice){
                $invoice_trash = InvoicePusat::onlyTrashed()->find($id);
                if(!$invoice_trash){
                    return $this->getResponse([],'Tagihan tidak ditemukan',404);
                }

                $destroy = $invoice_trash->forceDelete();

                if(!$destroy){
                    return $this->getResponse([],'Tagihan gagal dihapus.',500);
                }

                return $this->getResponse($invoice_trash,'Tagihan berhasil dihapus');
            }

            $destroy = $invoice->forceDelete();
            if(!$destroy){
                return $this->getResponse([],'Tagihan gagal dihapus',500);
            }

            return $this->getResponse($invoice,'Tagihan berhasil dihapus');
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }
}
