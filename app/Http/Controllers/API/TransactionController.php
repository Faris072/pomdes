<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Status;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function store(Request $request){
        try{
            if(auth()->user()->role_id != 1){
                $request->user_id = auth()->user()->id;
            }

            $attributes = [
                'user_id' => 'Id user',
                'status_id' => 'Id status',
                'name' => 'Nama transaksi',
                'start_date' => 'Tanggal mulai',
                'end_date' => 'Tanggal selesai',
                'description' => 'Deskripsi',
            ];

            $messages = [
                'required' => ':attribute wajib di isi.',
                'numeric' => ':attribute harus berupa angka.',
                'description.min' => 'Deskripsi minimal 5 karakter'
            ];

            $validatedData = Validator::make($request->all(),[
                'status_id' => 'required|numeric',
                'user_id' => 'required|numeric',
                'name' => 'required',
                'start_date' => 'required',
                'end_date' => 'required',
                'description' => 'required|min:5',

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

            return $this->getResponse($query,'Data berhasil disimpan.');
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function get(){
        try{
            $transactions = Transaction::with([
                    'user',
                    'status',
                    'invoice_pomdes',
                    'invoice_pusat',
                    'payment_to_pusat',
                    'payment_to_supplier',
                    'fuel_transactions',
                    'hindrance',
                    // 'hindrance.hindrance_files',
                    'discrepancy',
                    // 'discrepancy.fuel_discrepancies',
                    // 'discrepancy.fuel_discrepancies.discrepancy_files',
                    // 'discrepancy.fuel_discrepancies.discrepancy_type',
                    // 'discrepancy.fuel_discrepancies.fuel_transaction',
                ])->get();

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
                    'status',
                    'invoice_pomdes',
                    'invoice_pomdes.invoice_pomdes_files',
                    'invoice_pusat',
                    'invoice_pusat.invoice_pusat_files',
                    'payment_to_pusat',
                    'payment_to_pusat.payment_to_pusat_files',
                    'payment_to_supplier',
                    'payment_to_supplier.payment_to_supplier_files',
                    'fuel_transactions',
                    'fuel_transactions.fuel',
                    'fuel_transactions.fuel.supplier',
                    'hindrance',
                    'hindrance.hindrance_files',
                    'discrepancy',
                    'discrepancy.fuel_discrepancies',
                    'discrepancy.fuel_discrepancies.discrepancy_files',
                    'discrepancy.fuel_discrepancies.discrepancy_type',
                    'discrepancy.fuel_discrepancies.fuel_transaction',
                    'discrepancy.fuel_discrepancies.fuel_transaction.transaction',
                ])->find($id);

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
            }

            $attributes = [
                'user_id' => 'Id user',
                'status_id' => 'Id status',
                'name' => 'Nama transaksi',
                'start_date' => 'Tanggal mulai',
                'end_date' => 'Tanggal selesai',
                'description' => 'Deskripsi',
            ];

            $messages = [
                'required' => ':attribute wajib di isi.',
                'numeric' => ':attribute harus berupa angka.',
                'description.min' => 'Deskripsi minimal 5 karakter'
            ];

            $validatedData = Validator::make($request->all(),[
                'status_id' => 'required|numeric',
                'user_id' => 'required|numeric',
                'name' => 'required',
                'start_date' => 'required',
                'end_date' => 'required',
                'description' => 'required|min:5',

            ],$messages,$attributes);

            if($validatedData->fails()){
                return $this->getResponse([],$validatedData->getMessageBag(),422);
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

            $query = $transaction->update($request->all());

            if(!$query){
                return $this->getResponse([],'Transaksi gagal diubah',500);
            }

            return $this->getResponse(Transaction::find($transaction->id),'Transaksi berhasil diubah');
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
                'status_id' => 2
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
}
