<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Status;
use App\Models\User;
use App\Models\Reject;
use App\Http\Controllers\API\FuelTransactionController;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function store(Request $request){
        try{
            if(auth()->user()->role_id != 1){
                $request->user_id = auth()->user()->id;
                $request['user_id'] = auth()->user()->id;
            }

            $request->status_id = 1;
            $request['status_id'] = 1;

            $attributes = [
                'user_id' => 'Id user',
                'status_id' => 'Id status',
                'name' => 'Nama transaksi',
                'description' => 'Deskripsi',
                'fuels.*.fuel_id' => 'Id fuel',
                'fuels.*.volume' => 'Volume',
                'fuels.*.price' => 'Harga',
            ];

            $messages = [
                'required' => ':attribute wajib di isi.',
                'numeric' => ':attribute harus berupa angka.',
                'description.min' => 'Deskripsi minimal 5 karakter',
            ];

            $validatedData = Validator::make($request->all(),[
                'status_id' => 'required|numeric',
                'user_id' => 'required|numeric',
                'name' => 'required',
                'description' => 'required|min:5',
                'fuels.*.fuel_id' => 'required|numeric',
                'fuels.*.volume' => 'required|numeric',
                'fuels.*.price' => 'required|numeric',
            ],$messages,$attributes);

            if($validatedData->fails()){
                return $this->getResponse([],$validatedData->getMessageBag(),422);
            }

            $status = Status::find($request->status_id);

            if(!$status){
                return $this->getResponse([],'Status tidak ditemukan',404);
            }

            $user = User::find($request->user_id);

            if(!$user){
                return $this->getResponse([],'User tidak ditemukan',404);
            }
            else{
                if($user->role_id == 3 || $user->role_id == 1){}
                else{
                    return $this->getResponse([],'Selain pomdes tidak boleh mengajukan transaksi.',403);
                }
            }

            $query = Transaction::create($request->all());

            if(!$query){
                return $this->getResponse([],'Data gagal disimpan',500);
            }

            $fuelTransaction = (new FuelTransactionController)->store($request, $query);

            if(!$fuelTransaction){
                return $this->getResponse([],'Data bahan bakar gagal disimpan', 500);
            }

            $uploadSubmission = (new SubmissionFilesController)->upload($request, $query);

            if(!$uploadSubmission){
                return $this->getResponse([],'File gagal diupload',500);
            }

            return $this->getResponse(Transaction::with(['fuel_transactions','submission_files'])->find($query->id),'Data berhasil disimpan');
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function get(Request $request,$steps = 1){
        try{
            $transactions = Transaction::with([
                'user.profile',
                'status',
                'invoice_pomdes.additional_costs',
                'invoice_pusat',
                'payment_to_pusat',
                'payment_to_supplier',
                'fuel_transactions',
                'reject',
                'hindrance.hindrance_files',
                'discrepancy',
                'discrepancy.fuel_discrepancies.discrepancy_files',
                'discrepancy.fuel_discrepancies.discrepancy_type',
                'discrepancy.fuel_discrepancies.fuel_transaction',
            ]);

            switch($steps){
                case 1:
                    $transactions = $transactions->where('status_id',1)
                        ->orWhere('status_id',2)
                        ->orWhere('status_id',3);
                    break;
                case 2:
                    $transactions = $transactions->where('status_id',4)
                        ->orWhere('status_id',5);
                    break;
            }

            if(isset($request->search)){
                $transactions = $transactions->whereRaw("LOWER(name) LIKE '".strtolower($request->search)."'")
                    ->orWhereHas('user', function($q) use ($request){
                        $q->whereRaw("LOWER(username) LIKE '%".$request->search."%'");
                    })
                    ->orWhereHas('user.profile', function($q) use ($request){
                        $q->whereRaw("LOWER(name) LIKE '%".strtolower($request->search)."%'");
                    })
                    ->orWhereHas('fuel_transactions', function($q) use ($request){
                        $q->whereRaw("LOWER(name) LIKE '%".strtolower($request->search)."%'");
                    });
            }

            $transactions = $this->getDataTable($transactions, $request);

            if(!$transactions){
                return $this->getResponse([],'Terjadi kesalahan koneksi',500);
            }

            return $this->getResponse($transactions,'Data berhasil ditampilkan',200);
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function show($id){
        try{
            if(empty($id)){
                return $this->getResponse([],'Id wajib di isi.',422);
            }

            $transactions = Transaction::with([
                'user',
                'submission_files',
                'reject',
                'status',
                'invoice_pomdes.invoice_pomdes_files',
                'invoice_pomdes.additional_costs',
                'invoice_pusat.invoice_pusat_files',
                'payment_to_pusat.payment_to_pusat_files',
                'payment_to_supplier.payment_to_supplier_files',
                'fuel_transactions.fuel.supplier',
                'hindrance.hindrance_files',
                'discrepancy.fuel_discrepancies.fuel_transaction.transaction',
            ])->find($id);

            foreach($transactions->submission_files as $file){
                $file->link = route('render-submission-files',$file->id);
            }

            foreach($transactions->invoice_pomdes->invoice_pomdes_files as $file){
                $file->link = route('render-additional-cost-files',$file->id);
            }

            if(!$transactions){
                return $this->getResponse([],'Data tidak ditemukan',404);
            }

            return $this->getResponse($transactions,'Data berhasil ditampilkan',200);
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function update(Request $request, $id){
        try{
            if(empty($id)){
                return $this->getResponse([],'Transaction wajib dipilih',422);
            }

            $transaction = Transaction::find($id);

            if(!$transaction){
                return $this->getResponse([],'Transaksi tidak ditemukan',404);
            }

            if(auth()->user()->role_id != 1){
                $request->user_id = auth()->user()->id;
                $request['user_id'] = auth()->user()->id;
            }

            $request->status_id = 1;
            $request['status_id'] = 1;

            $attributes = [
                'user_id' => 'Id user',
                'status_id' => 'Id status',
                'name' => 'Nama transaksi',
                'description' => 'Deskripsi',
                'fuels.*.fuel_id' => 'Id fuel',
                'fuels.*.volume' => 'Volume',
                'fuels.*.price' => 'Harga',
            ];

            $messages = [
                'required' => ':attribute wajib di isi.',
                'numeric' => ':attribute harus berupa angka.',
                'description.min' => 'Deskripsi minimal 5 karakter',
            ];

            $validatedData = Validator::make($request->all(),[
                'status_id' => 'required|numeric',
                'user_id' => 'required|numeric',
                'name' => 'required',
                'description' => 'required|min:5',
                'fuels.*.fuel_id' => 'required|numeric',
                'fuels.*.volume' => 'required|numeric',
                'fuels.*.price' => 'required|numeric',
            ],$messages,$attributes);

            if($validatedData->fails()){
                return $this->getResponse([],$validatedData->getMessageBag(),422);
            }

            $user = User::find($request->user_id);

            $status = Status::find($request->status_id);

            if(!$status){
                return $this->getResponse([],'Status tidak ditemukan',404);
            }

            if(!$user){
                return $this->getResponse([],'User tidak ditemukan',404);
            }
            else{
                if($user->role_id == 3 || $user->role_id == 1){}
                else{
                    return $this->getResponse([],'Selain pomdes tidak boleh mengajukan transaksi.',403);
                }
            }

            $query = $transaction->update($request->all());

            if(!$query){
                return $this->getResponse([],'Transaksi gagal diubah',500);
            }

            $fuelTransaction = (new FuelTransactionController)->update($request, $query, $id);

            if(!$fuelTransaction){
                return $this->getResponse([],'Data bahan bakar gagal disimpan', 500);
            }

            $uploadSubmission = (new SubmissionFilesController)->upload($request, $query);

            if(!$uploadSubmission){
                return $this->getResponse([],'File gagal diupload',500);
            }

            return $this->getResponse(Transaction::with(['fuel_transactions','submission_files'])->find($id),'Data berhasil disimpan');
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function delete($id){
        try{
            if(empty($id)){
                return $this->getResponse([],'Id wajib di isi',500);
            }

            $transaction = Transaction::find($id);

            if(!$transaction){
                return $this->getResponse([],'Transaksi tidak ditemukan',404);
            }

            $delete = $transaction->delete();

            if(!$delete){
                return $this->getResponse([],'Transaksi gagal dihapus',500);
            }

            return $this->getResponse($transaction,'Transaksi berhasil dihapus',200);
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function get_trash(){
        try{
            $transactions = Transaction::with('user','status')->onlyTrashed()->get();

            if(!$transactions){
                return $this->getResponse([],'Transaksi gagal dimuat.',500);
            }

            return $this->getResponse($transactions,'Data berhasil ditampilkan.',200);
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function show_trash($id){
        try{
            if(empty($id)){
                return $this->getResponse([],'Transaksi wajib dipilih',422);
            }

            $transactions = Transaction::with('user','status')->onlyTrashed()->find($id);

            if(!$transactions){
                return $this->getResponse([],'Transaksi gagal dimuat.',500);
            }

            return $this->getResponse($transactions,'Data berhasil ditampilkan.',200);
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function restore_trash($id){
        try{
            $transaction = Transaction::onlyTrashed()->find($id);
            if(!$transaction){
                return $this->getResponse([],'Transaksi tidak ditemukan',404);
            }

            $restore =  $transaction->restore();

            if(!$restore){
                return $this->getResponse([],'Data transaksi gagal dikembalikan',500);
            }

            return $this->getResponse($transaction,'Data transaksi berhasil dikembalikan');
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function destroy($id){
        try{
            if(empty($id)){
                return $this->getResponse([],'Transaksi wajib dipilih',422);
            }

            $transactions = Transaction::onlyTrashed()->find($id);

            if(!$transactions){
                return $this->getResponse([],'Transaksi gagal dimuat.',500);
            }

            $destroy = $transactions->forceDelete();

            if(!$destroy){
                return $this->getResponse([],'Data gagal dihapus', 500);
            }

            return $this->getResponse($transactions,'Data berhasil dihapus.',200);
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function approve_submission($id){
        try{
            if(empty($id)){
                return $this->getResponse([],'Transaksi wajib dipilih',422);
            }

            $transaction = Transaction::find($id);

            if(!$transaction){
                return $this->getResponse([],'Transaksi tidak ditemukan',404);
            }

            if($transaction->status_id == 2){
                return $this->getResponse([],'Transaksi sudah disetujui',403);
            }

            $update = $transaction->update([
                'status_id' => 4
            ]);

            if(!$update){
                return $this->getResponse([],'Pengajuan transaksi gagal disetujui',500);
            }

            return $this->getResponse(Transaction::find($id),'Pengajuan transaksi berhasil disetujui',200);
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function reject(Request $request,$id){
        try{
            $transaction = Transaction::find($id);

            if(!$transaction){
                return $this->getResponse([], 'Transaksi tidak ditemukan',404);
            }

            $attributes = [
                'description' => 'Alasan penolakan'
            ];

            $messages = [
                'required' => ':attribute tidak boleh kosong.'
            ];

            $validatedData = Validator::make($request->all(),[
                'description' => 'required',
            ],$messages,$attributes);

            if($validatedData->fails()){
                return $this->getResponse([],$validatedData->getMessageBag(),422);
            }

            $status_list = [2,5];
            $status_id = $transaction->status_id;

            if(in_array($status_id, $status_list)){
                return $this->getResponse([],'Akses ditolak karena status tidak sesuai',403);
            }

            $check = Reject::where('transaction_id',$id)->where('status_id',$status_id)->first();

            if($check){
                $reject = $check->update(['descripion' => $request->description]);

                $change_status = Transaction::find($id)->update(['status_id' => (int) $status_id]);

                if(!$change_status){
                    Reject::find($reject->id)->forceDelete();
                    return $this->getResponse([],'Transaksi gagal ditolak',500);
                }

                if(!$reject){
                    return $this->getResponse([],'Transaksi gagal ditolak', 500);
                }

                return $this->getResponse(Reject::find($check->id), 'Transaksi berhasil ditolak');
            }

            $reject = Reject::create([
                'transaction_id' => $id,
                'status_id' => $status_id,
                'is_active' => 1,
                'description' => $request->description,
            ]);

            if(!$reject){
                return $this->getResponse([],'Transaksi gagal ditolak', 500);
            }

            $change_status = Transaction::find($id)->update(['status_id' => (int) $status_id%3 == 0 ? (int) $status_id-1 : (int) $status_id+1]);

            if(!$change_status){
                Reject::find($reject->id)->forceDelete();
                return $this->getResponse([],'Transaksi gagal ditolak',500);
            }

            return $this->getResponse(Transaction::with(['reject'])->find($id), 'Transaksi berhasil ditolak');
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function reason_reject(Request $request, $id){
        try{
            $find = Reject::where('transaction_id',$id)->where('status_id',$request->status_id)->first();
            if(!$find){
                return $this->getResponse([],'Data tidak ditemukan',404);
            }

            return $this->getResponse($find,'Data berhasil ditampilkan');
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function repair($id){
        try{
            $transaction = Transaction::find($id);

            if(!$transaction){
                return $this->getResponse([],'Transaksi tidak ditemukan',404);
            }

            $status_list = [2,5];

            if(!in_array($transaction->status_id, $status_list)){
                return $this->getResponse([],'Akses ditolak karena status tidak sesuai',403);
            }

            $repair = $transaction->update(['status_id' => (int) $transaction->status_id+1]);

            if(!$repair){
                return $this->getResponse([],'Transaksi gagal diperbaiki',500);
            }

            return $this->getResponse(Transaction::find($id),'Perbaikan berhasil diajukan');
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }
}
