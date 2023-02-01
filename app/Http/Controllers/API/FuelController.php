<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Fuel;
use App\Models\User;

class FuelController extends Controller
{
    public function create(Request $request){
        try{
            if(auth()->user()->role_id != 1){
                if(auth()->user()->role_id == 4){
                    $request->user_id = auth()->user()->id;
                    $checkName = Fuel::whereRaw("LOWER(name) = LOWER('".$request->name."')")
                        ->whereHas('supplier', function($q) use ($request){
                            $q->where('id',$request->user_id);
                        })->first();
                    if($checkName){
                        return $this->getResponse([],'Data sudah ada',422);
                    }
                }
                else{
                    return $this->getResponse([],'Akses ditolak',403);
                }
            }
            else{
                $checkName = Fuel::whereRaw("LOWER(name) = LOWER('".$request->name."')")
                    ->whereHas('supplier', function($q) use ($request){
                        $q->where('id',$request->user_id);
                    })->first();
                if($checkName){
                    return $this->getResponse([],'Data sudah ada',422);
                }
            }

            $user = User::find($request->user_id);

            if(!$user){
                return $this->getResponse([],'Supplier tidak ditemukan',404);
            }

            if($user->role_id != 4){
                return $this->getResponse([], 'Role user harus supplier',422);
            };

            $attributes = [
                'user_id' => 'Supplier',
                'name' => 'Nama bahan bakar'
            ];

            $messages = [
                'required' => ':attribute wajib dipilih.',
            ];

            $validatedData = Validator::make($request->all(),[
                'user_id' => 'required|numeric',
                'name' => 'required'
            ],$messages,$attributes);

            if($validatedData->fails()){
                return $this->getResponse([],$validatedData->getMessageBag(),422);
            }

            $query = Fuel::create($request->all());

            if(!$query){
                return $this->getResponse([],'Data gagal disimpan.',500);
            }

            return $this->getResponse($query,'Data bahan bakar berhasil ditambahkan');
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function select_supplier(Request $request){
        try{
            $query = User::with(['profile']);

            if(isset($request->search)){
                $query = $query->where('username',$request->search)->orWhereHas('profile', function($q) use ($request){
                    $q->where('name',$request->search);
                });
            }

            if(isset($request->limit)){
                $query = $query->limit($request->limit);
            }

            $query = $query->where('role_id',4)->get();

            if(!$query){
                return $this->getResponse([],'Data gagal dimuat.',500);
            }

            return $this->getResponse($query,'Data berhasil ditampilkan');

        }
        catch(\Exception $e){
            return $this->getMessage([],$e->getMessage(),500);
        }
    }

    public function get(Request $request){
        try{
            $query = Fuel::with(['supplier.profile']);

            if(isset($request->search)){
                $query = $query->whereRaw("LOWER(name) LIKE LOWER('%".$request->search."%')")
                    ->orWhereHas('supplier', function($q) use ($request){
                        $q->whereRaw("LOWER(username) LIKE LOWER('%".$request->search."%')");
                    })
                    ->orWhereHas('supplier.profile', function($q) use ($request){
                        $q->whereRaw("LOWER(name) LIKE LOWER('%".$request->search."%')");
                    });
            }

            $query = $this->getDataTable($query, $request);

            if(!$query){
                return $this->getResponse([],'Data bahan bakar gagal dimuat.',500);
            }

            return $this->getResponse($query, 'Data bahan bakar berhasil ditampilkan.');
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function update(Request $request, $id){
        try{
            if(auth()->user()->role_id != 1){
                if(auth()->user()->role_id == 4){
                    $request->user_id = auth()->user()->id;
                    $checkName = Fuel::whereRaw("LOWER(name) = LOWER('".$request->name."')")
                        ->whereHas('supplier', function($q) use ($request){
                            $q->where('id',$request->user_id);
                        })->first();
                    if($checkName && $checkName->id != $id){
                        return $this->getResponse([],'Data sudah ada',422);
                    }
                }
                else{
                    return $this->getResponse([],'Akses ditolak',403);
                }
            }
            else{
                $checkName = Fuel::whereRaw("LOWER(name) = LOWER('".$request->name."')")
                    ->whereHas('supplier', function($q) use ($request){
                        $q->where('id',$request->user_id);
                    })->first();
                if($checkName && $checkName->id != $id){
                    return $this->getResponse([],'Data sudah ada',422);
                }
            }

            $user = User::find($request->user_id);

            if(!$user){
                return $this->getResponse([],'Supplier tidak ditemukan',404);
            }

            if($user->role_id != 4){
                return $this->getResponse([], 'Role user harus supplier',422);
            };

            $attributes = [
                'user_id' => 'Supplier',
                'name' => 'Nama bahan bakar'
            ];

            $messages = [
                'required' => ':attribute wajib dipilih.',
            ];

            $validatedData = Validator::make($request->all(),[
                'user_id' => 'required|numeric',
                'name' => 'required'
            ],$messages,$attributes);

            if($validatedData->fails()){
                return $this->getResponse([],$validatedData->getMessageBag(),422);
            }

            $query = Fuel::find($id)->update($request->all());

            if(!$query){
                return $this->getResponse([],'Data gagal disimpan.',500);
            }

            return $this->getResponse($query,'Data bahan bakar berhasil diubah.');
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function delete($id){
        try{
            $fuel = Fuel::find($id);

            if(!$fuel){
                return $this->getResponse([],'Data tidak ditemukan', 404);
            }

            $destroy = $fuel->delete();

            if(!$destroy){
                return $this->getResponse([],'Data gagal dihapus.',500);
            }

            return $this->getResponse($fuel,'Data berhasil dihapus.');
        }
        catch(\Exception $e){
            return $this->getResponse([], $e->getMessage(), 500);
        }
    }
}
