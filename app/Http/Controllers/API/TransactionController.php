<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Status;
use App\Models\User;
use App\Models\Reject;
use App\Http\Controllers\API\FuelTransactionController;
use App\Models\LogApproved;
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
                'fuel_transactions.fuel.supplier',
                'reject',
                'delivery.delivery_files',
                'hindrance.hindrance_files',
                'log_approveds',
                'discrepancy',
                'discrepancy.fuel_discrepancies.discrepancy_files',
                'discrepancy.fuel_discrepancies.discrepancy_type',
                'discrepancy.fuel_discrepancies.fuel_transaction',
            ]);

            if(auth()->user()->role_id == 1){}
            else if(auth()->user()->role_id == 2){
                $transactions = $transactions->whereHas('user', function($q){
                    $q->where('user_id',auth()->user()->id);
                });
            }
            else if(auth()->user()->role_id == 3){
                $transactions = $transactions->where('user_id', auth()->user()->id);
            }
            else if(auth()->user()->role_id == 4){
                $transactions = $transactions->whereHas('fuel_transactions', function($q){
                    $q->whereHas('fuel', function($q){
                        $q->where('user_id',auth()->user()->id);
                    });
                });
            }
            else{
                $transactions = $transactions->where('user_id', auth()->user()->id);
            }

            switch($steps){
                case 1://pengajuan
                    $transactions = $transactions->where('status_id',1)
                        ->orWhere('status_id',2)
                        ->orWhere('status_id',3);
                    break;
                case 2://penerbitan tagihan
                    $transactions = $transactions->where('status_id',4)
                        ->orWhere('status_id',5);
                    break;
                case 3://pembayaran tagihan
                    $transactions = $transactions->where('status_id',6);
                    break;
                case 4://pengiriman
                    $transactions = $transactions->where('status_id',7)
                    ->orWhere('status_id',8)
                    ->orWhere('status_id',9);
                    break;
                case 5://arrived (sampai)
                    $transactions = $transactions->where('status_id',10)
                    ->orWhere('status_id',11)
                    ->orWhere('status_id',12);
                    break;
            }

            if(isset($request->search)){
                $transactions = $transactions->whereRaw("LOWER(name) LIKE '".strtolower($request->search)."'")
                    ->orWhereHas('status', function($q) use ($request){
                        $q->whereRaw("LOWER(name) LIKE '%".strtolower($request->search)."%'");
                    })
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

            if(isset($request->sort_by)){
                $transactions = $transactions->orderBy($request->sort_by, $request->order_by);
            }

            if(isset($request->limit)){
                $transactions = $transactions->paginate($request->limit);
            }
            else{
                $transactions = $transactions->get();
            }

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
                'log_approveds',
                'delivery.delivery_files',
                'invoice_pomdes.invoice_pomdes_files',
                'invoice_pomdes.additional_costs',
                'fuel_transactions.fuel.supplier',
                'hindrance.hindrance_files',
                'discrepancy.fuel_discrepancies.fuel_transaction.transaction',
            ])->find($id);

            if($transactions->submission_files){
                foreach($transactions->submission_files as $file){
                    $file->link = route('render-submission-files',$file->id);
                }
            }

            if($transactions->invoice_pomdes && $transactions->invoice_pomdes->invoice_pomdes_files){
                foreach($transactions->invoice_pomdes->invoice_pomdes_files as $file){
                    $file->link = route('render-additional-cost-files',$file->id);
                }
            }

            if($transactions->delivery && $transactions->delivery->delivery_files){
                foreach($transactions->delivery->delivery_files as $file){
                    $file->link = route('render-delivery-files',$file->id);
                }
            }

            if($transactions->hindrance && $transactions->hindrance->hindrance_files){
                foreach($transactions->hindrance->hindrance_files as $file){
                    $file->link = route('render-hindrance-files',$file->id);
                }
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

            // if($transaction->status_id == 2){
            //     return $this->getResponse([],'Transaksi sudah disetujui',403);
            // }

            $update = $transaction->update([
                'status_id' => 4
            ]);

            if(!$update){
                return $this->getResponse([],'Pengajuan transaksi gagal disetujui',500);
            }

            $this->store_log_approved($id, auth()->user()->id, $transaction->status_id, 'Setujui pengajuan BBM');

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

            if($transaction->status_id == 1 || $transaction->status_id == 3){}
            else{
                return $this->getResponse([],'Akses ditolak. Status tidak sesuai',403);
            }

            $check = Reject::firstWhere('transaction_id',$id);
            if($check){
                $reject = $check->update(['description' => $request->description]);
                if(!$reject){
                    return $this->getResponse([],'Penolakan gagal.',500);
                }

                $change = Transaction::find($id)->update(['status_id' => 2]);
                if(!$change){
                    $check->forceDelete();
                    return $this->getResponse([],'Penolakan gagal.',500);
                }

                return $this->getResponse(Transaction::with(['reject'])->find($id),'Transaksi berhasil ditolak.',200);
            }

            $reject = Reject::create([
                'transaction_id' => $id,
                'description' => $request->description
            ]);

            if(!$reject){
                return $this->getResponse([],'Transaksi gagal ditolak',500);
            }

            $change = Transaction::find($id)->update(['status_id' => 2]);
            if(!$change){
                Reject::firstWhere('transaction_id',$id)->forceDelete();
                return $this->getResponse([],'Data gagal ditolak',500);
            }

            return $this->getResponse(Transaction::with(['reject'])->find($id),'Tramsalso berhasil ditolak',200);
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function reason_reject(Request $request, $id){
        try{
            $find = Reject::where('transaction_id',$id)->first();
            // ->where('status_id',$request->status_id)->first();
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

    //terbitkan penagihan
    public function publish_billing($id){
        try{
            if(auth()->user()->role_id == 1 || auth()->user()->role_id == 2){}
            else{
                return $this->getResponse([],'Akses ditolak',403);
            }

            $transaction = Transaction::with(['status','invoice_pomdes'])->find($id);

            if($transaction->status_id != 5){
                return $this->getResponse([],'Status tidak sesuai', 403);
            }

            $publish = $transaction->update(['status_id' => 6]);

            if(!$publish){
                return $this->getResponse([],'Penerbitan gagal. Silahkan coba kembali nanti..',500);
            }

            $this->store_log_approved($id, auth()->user()->id, $transaction->status_id, 'Penerbitan tagihan');

            return $this->getResponse([],'Penerbitan berhasil.');
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    //Setujui pembayaran
    public function approve_payment($id){
        try{
            if(auth()->user()->role_id == 1 || auth()->user()->role_id == 2){}
            else{
                return $this->getResponse([],'Akses ditolak',403);
            }

            $transaction = Transaction::with(['status'])->find($id);

            if($transaction->status_id != 6){
                return $this->getResponse([],'Status tidak sesuai', 403);
            }

            $publish = $transaction->update(['status_id' => 7]);

            if(!$publish){
                return $this->getResponse([],'Approve gagal. Silahkan coba kembali nanti..',500);
            }

            $this->store_log_approved($id, auth()->user()->id, $transaction->status_id, 'Approve pembayaran');

            return $this->getResponse([],'Approve berhasil.');
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function send_delivery($id){
        try{
            if(auth()->user()->role_id == 1 ||auth()->user()->role_id == 4){}
            else{
                return $this->getResponse([],'Akses ditolak',403);
            }

            $transaction = Transaction::with(['delivery.delivery_files','status'])->find($id);
            if(!$transaction){
                return $this->getResponse([],'Transaksi tidak ditemukan.',404);
            }

            if($transaction->status_id != 7){
                return $this->getResponse([],'Akses ditolak. Status tidak sesuai.',403);
            }

            $edit = $transaction->update(['status_id' => 8]);
            if(!$edit){
                return $this->getResponse([],'Transaksi gagal dikirimkan',500);
            }

            $this->store_log_approved($id, auth()->user()->id, $transaction->status_id, 'Kirim BBM');

            return $this->getResponse(Transaction::with(['delivery.delivery_files'])->find($id),'Transaksi berhasil dikirimkan.');
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function send_hindrance($id){
        try{
            if(auth()->user()->role_id == 1 ||auth()->user()->role_id == 4){}
            else{
                return $this->getResponse([],'Akses ditolak',403);
            }

            $transaction = Transaction::with(['hindrance.hindrance_files'])->find($id);
            if(!$transaction){
                return $this->getResponse([],'Transaksi tidak ditemukan.',404);
            }

            if($transaction->status_id != 8){
                return $this->getResponse([],'Akses ditolak. Status tidak sesuai.',403);
            }

            $edit = $transaction->update(['status_id' => 9]);
            if(!$edit){
                return $this->getResponse([],'Kendala gagal dikirimkan',500);
            }

            return $this->getResponse(Transaction::with(['hindrance.hindrance_files'])->find($id),'Transaksi berhasil dikirimkan.');
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function set_arrived($id){
        try{
            if(auth()->user()->role_id == 1 ||auth()->user()->role_id == 4){}
            else{
                return $this->getResponse([],'Akses ditolak',403);
            }

            $transaction = Transaction::with([])->find($id);
            if(!$transaction){
                return $this->getResponse([],'Transaksi tidak ditemukan.',404);
            }

            if($transaction->status_id == 8 || $transaction->status_id == 9){}
            else{
                return $this->getResponse([],'Akses ditolak. Status tidak sesuai.',403);
            }

            $edit = $transaction->update(['status_id' => 10]);
            if(!$edit){
                return $this->getResponse([],'Kendala gagal dikirimkan',500);
            }

            $this->store_log_approved($id, auth()->user()->id, $transaction->status_id, 'Set BBM Sampai');

            return $this->getResponse(Transaction::with(['hindrance.hindrance_files'])->find($id),'Transaksi berhasil dikirimkan.');
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function finish($id){
        try{
            if(auth()->user()->role_id == 1 || auth()->user()->role_id == 3){}
            else{
                return $this->getResponse([],'Akses ditolak',403);
            }

            $transaction = Transaction::with(['status'])->find($id);
            if(!$transaction){
                return $this->getResponse([],'Transaksi tidak ditemukan',404);
            }

            if($transaction->status_id == 10 || $transaction->status_id == 11){}
            else{
                return $this->getResponse([],'Akses ditolak, status tidak sesuai',403);
            }

            $set = $transaction->update(['status_id' => 12]);
            if(!$set){
                return $this->getResponse([],'Set finish gagal',500);
            }

            $this->store_log_approved($id, auth()->user()->id, $transaction->status_id, 'Transaksi selesai');

            return $this->getResponse(Transaction::with(['status'])->find($id),'Transaksi berhasil di set selesai',200);
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function store_log_approved($id, $user_id, $status_id, $name){
        try{
            LogApproved::create([
                'transaction_id' => $id,
                'user_id' => $user_id,
                'status_id' => $status_id,
                'name' => $name,
            ]);
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }
}
