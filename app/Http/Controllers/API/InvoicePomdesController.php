<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InvoicePomdes;
use App\Models\InvoicePomdesFiles;
use App\Models\InvoicePusat;
use App\Models\Transaction;
use App\Models\AdditionalCost;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class InvoicePomdesController extends Controller
{
    public function store(Request $request){
        try{
            $transaction =  Transaction::find($request->transaction_id);

            if(!$transaction){
                return $this->getResponse([],'Transaksi tidak ditemukan',404);
            }

            if($transaction->status_id == 4 || $transaction->status_id == 5){}
            else{
                return $this->getResponse([],'Akses ditolak karena status tidak sesuai', 403);
            }

            $attributes = [
                'transaction_id' => 'Transaksi',
                'total' => 'Total'
            ];

            $messages = [
                'required' => ':attribute tidak boleh kosong.',
                'numeric' => ':attribute harus berupa angka.'
            ];

            if(isset($request->additional_costs)){
                $rules = [
                    'transaction_id' => 'required',
                    'total' => 'required|numeric',
                    'additional_costs.*.name' => 'required',
                    'additional_costs.*.nominal' => 'required|numeric',
                ];
            }
            else{
                $rules = [
                    'transaction_id' => 'required',
                    'total' => 'required|numeric',
                ];
            }

            $validatedData = Validator::make($request->all(),$rules,$messages,$attributes);

            if($validatedData->fails()){
                return $this->getResponse([],$validatedData->getMessageBag(),422);
            }

            $exist = InvoicePomdes::firstWhere('transaction_id', $transaction->id);

            if($exist){
                AdditionalCost::where('invoice_pomdes_id', $exist->id)->delete();
                $invoice = InvoicePomdes::find($exist->id)->update($request->all());

                if(!$invoice){
                    AdditionalCost::where('invoice_pomdes_id', $exist->id)->restore();
                    return $this->getResponse([],'Tagihan gagal disimpan',500);
                }
                AdditionalCost::where('invoice_pomdes_id', $exist->id)->forceDelete();
            }
            else{
                $invoice = InvoicePomdes::create($request->all());

                if(!$invoice){
                    return $this->getResponse([],'Tagihan gagal disimpan',500);
                }
            }

            $status = $transaction->update(['status_id' => 5]);

            if(!$status){
                $status = $transaction->update(['status_id' => 4]);
                return $this->getResponse([],'Tagihan gagal disimpan',500);
            }

            $invoicePomdes = InvoicePomdes::firstWhere('transaction_id', $request->transaction_id);

            if(isset($request->additional_costs)){
                foreach($request->additional_costs as $ac){
                    AdditionalCost::create([
                        'invoice_pomdes_id' => $invoicePomdes->id,
                        'name' => $ac['name'],
                        'nominal' => $ac['nominal']
                    ]);
                }
            }

            return $this->getResponse(InvoicePomdes::with(['transaction','additional_costs','invoice_pomdes_files'])->firstWhere('transaction_id', $request->transaction_id),'Tagihan berhasil disimpan');
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

                $delete = $invoiceTrash->forceDelete();

                if(!$delete){
                    return $this->getResponse([],'Data gagal dihancurkan',500);
                }

                $status = Transaction::find($invoice->transaction_id)->update(['status_id' => 2]);

                if(!$status){
                    return $this->getResponse([],'Data tagihan gagal dihancurkan',500);
                }

                return $this->getResponse($invoiceTrash,'Data berhasil dihancurkan');
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

                $data->name = date('now').rand('10000','99999').'.'.$data->extension;

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

    public function store_upload(Request $request){
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

            $id = $invoice->id;

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

                $data->name = date('now').rand('10000','99999').'.'.$data->extension;

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

    public function update_upload(Request $request,$id){
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

                $data->name = date('now').rand('10000','99999').'.'.$data->extension;

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
