<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InvoicePomdes;
use App\Models\InvoicePomdesFiles;
use App\Models\InvoicePusat;
use App\Models\Transaction;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

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

            $exist = InvoicePomdes::firstWhere('transaction_id', $transaction->id);

            if($exist){
                return $this->getResponse([],'Invoice pomdes sudah ada',422);
            }

            $status = $transaction->update(['status_id' => 3]);

            if(!$status){
                return $this->getResponse([],'Tagihan gagal disimpan',500);
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

    public function get(){
        try{
            $invoice_pomdes = InvoicePomdes::with(['invoice_pomdes_files','transaction','transaction.user'])->get();

            if(!$invoice_pomdes){
                return $this->getResponse([],'Data gagal dimuat',500);
            }

            return $this->getResponse($invoice_pomdes, 'Data berhasil ditampilkan');
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function show($id){
        try{
            $invoice_pomdes = InvoicePomdes::with(['invoice_pomdes_files','transaction','transaction.user'])->find($id);

            if(!$invoice_pomdes){
                return $this->getResponse([],'Data invoice pomdes tidak ditemukan',404);
            }

            return $this->getResponse($invoice_pomdes, 'Data berhasil ditampilkan');
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function update(Request $request, $id){
        try{
            $invoice_pomdes = InvoicePomdes::find($id);
            if(!$invoice_pomdes){
                return $this->getResponse([],'Data tidak ditemukan',404);
            }

            $attributes = [
                'transaction_id' => 'Transaksi',
                'nominal' => 'Nominal'
            ];

            $messages = [
                'required' => ':attribute tidak boleh kosong.',
                'numeric' => ':attribute harus berupa angka.'
            ];

            $validatedData = Validator::make($request->all(),[
                // 'transaction_id' => 'required',
                'nominal' => 'required|numeric'
            ],$messages,$attributes);

            if($validatedData->fails()){
                return $this->getResponse([],$validatedData->getMessageBag(),422);
            }

            $update = $invoice_pomdes->update($request->all());

            if(!$update){
                return $this->getResponse([],'Data gagal diupdate',500);
            }

            return $this->getResponse(InvoicePomdes::find($id),'Data berhasil diupdate');
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function delete($id){
        try{
            $invoice = InvoicePomdes::find($id);
            if(!$invoice){
                return $this->getResponse([],'Tagihan pomdes tidak ditemukan',404);
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
            $invoice = InvoicePomdes::with(['invoice_pomdes_files','transaction','transaction.user'])->onlyTrashed()->get();
            if(!$invoice){
                return $this->getResponse([],'Tagihan pomdes gagal ditampilkan',500);
            }

            return $this->getResponse($invoice,'Data berhasil ditampilkan');
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function show_trash($id){
        try{
            $invoice = InvoicePomdes::with(['invoice_pomdes_files','transaction','transaction.user'])->onlyTrashed()->find($id);
            if(!$invoice){
                return $this->getResponse([],'Tagihan pomdes tidak ditemukan',404);
            }

            return $this->getResponse($invoice,'Data berhasil ditampilkan');
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function restore_trash($id){
        try{
            $invoice = InvoicePomdes::with(['invoice_pomdes_files','transaction','transaction.user'])->onlyTrashed()->find($id);
            if(!$invoice){
                return $this->getResponse([],'Tagihan pomdes tidak ditemukan',404);
            }

            $restore =  $invoice->restore();

            if(!$restore){
                return $this->getResponse([],'Data gagal dikembalikan',500);
            }

            return $this->getResponse($invoice,'Data berhasil dikembalikan');
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function destroy($id){
        try{
            $invoice = InvoicePomdes::find($id);
            if(!$invoice){
                $invoiceTrash = InvoicePomdes::onlyTrashed()->find($id);
                if(!$invoiceTrash){
                    return $this->getResponse([],'Data tagihan tidak ditemukan',404);
                }
            }

            $status = Transaction::find($invoice->transaction_id)->update(['status_id' => 2]);

            if(!$status){
                return $this->getResponse([],'Data tagihan gagal dihancurkan',500);
            }

            $delete = $invoice->forceDelete();

            if(!$delete){
                return $this->getResponse([],'Data gagal dihancurkan',500);
            }

            return $this->getResponse($invoice,'Data berhasil dihancurkan');
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function upload_files(Request $request, $id){
        try{
            $invoice = InvoicePomdes::find($id);

            if(!$invoice){
                return $this->getResponse([],'Data tagihan pomdes tidak ditemukan');
            }

            $exist = InvoicePomdesFiles::firstWhere('invoice_pomdes_id',$id);

            if($exist){
                $old = InvoicePomdesFiles::where('invoice_pomdes_id',$id)->get();
                $destroy = InvoicePomdesFiles::where('invoice_pomdes_id',$id)->delete();

                if(!$destroy){
                    return $this->getResponse([],'Data gagal diperbarui',500);
                }

                foreach($old as $o){
                    $storage = Storage::disk('public')->delete('invoice_pomdes/'.$o->name);

                    if(!$storage){
                        return $this->getResponse([],'Data gagal diperbarui',500);
                    }
                }
            }

            foreach($request->all()['file'] as $r){
                $data = (object) [
                    'invoice_pomdes_id' => $id,
                    'original' => $r,
                    'name' => $r->getClientOriginalName(),
                    'extension' => $r->getClientOriginalExtension(),
                    'is_image' => substr($r->getMimeType(),0,5) == 'image' ? true : false,
                    'size' => $r->getSize()
                ];

                $replace = $r->storeAs('invoice_pomdes',$data->name);

                if(!$replace){
                    return $this->getResponse([],'File gagal diupload',500);
                }

                $upload = InvoicePomdesFiles::create((array) $data);

                if(!$upload){
                    return $this->getResponse([],'Data gagal disimpan',500);
                }
            }

            $getTrash = InvoicePomdesFiles::onlyTrashed()->get();
            if(count($getTrash)){
                foreach($getTrash as $t){
                    $delete = $t->forceDelete();
                    if(!$delete){
                        return $this->getResponse([],'Data gagal diperbarui',500);
                    }
                }
            }

            return $this->getResponse([],'Data berhasil diupload');
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }
}
