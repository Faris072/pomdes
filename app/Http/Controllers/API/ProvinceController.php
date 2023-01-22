<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Province;
use Illuminate\Support\Facades\Validator;

class ProvinceController extends Controller
{
    public function store(Request $request){
        try{
            $message = [
                'name.required' => 'Nama provinsi wajib di isi.',
                'name.min' => 'Nama provinsi minimal 3 karakter'
            ];

            $validatedData = Validator::make($request->all(),[
                'name' => 'required|min:3',
            ],$message);

            if($validatedData->fails()){
                return $this->getResponse([],$validatedData->getMessageBag(),422);
            }

            $query = Province::create([
                'name' => $request->name
            ]);

            if(!$query){
                return $this->getResponse([],'Data provinsi gagal ditambahkan.',500);
            }

            return $this->getResponse($query,'Data provinsi berhasil ditambahkan',200);
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function get(){
        try{
            $query = Province::with(['cities'])->get();
            if(!$query){
                return $this->getResponse([],'Data provinsi tidak ditemukan.',422);
            }

            return $this->getResponse($query, 'Data provinsi berhasil ditampilkan.');
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function show($id){
        try{
            $query = Province::with(['cities'])->find($id);
            if(!$query){
                return $this->getResponse([],'Data provinsi tidak ditemukan.',422);
            }

            return $this->getResponse($query, 'Data provinsi berhasil ditampilkan.');
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function update(Request $request,$id){
        try{
            $province = Province::find($id);
            if(!$province){
                return $this->getResponse([],'Data provinsi tidak ditemukan.',422);
            }

            $message = [
                'name.required' => 'Nama provinsi wajib di isi.',
                'name.min' => 'Nama provinsi minimal 3 karakter'
            ];

            $validatedData = Validator::make($request->all(),[
                'name' => 'required|min:3',
            ],$message);

            if($validatedData->fails()){
                return $this->getResponse([],$validatedData->getMessageBag(),422);
            }

            $query = $province->update([
                'name' => $request->name
            ]);

            if(!$query){
                return $this->getResponse([],'Data provinsi gagal diubah.',500);
            }

            return $this->getResponse($province,'Data provinsi berhasil diubah.');
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function delete($id){
        try{
            $province = Province::find($id);

            if(!$province){
                return $this->getResponse([],'Provinsi tidak ditemukan',422);
            }

            $delete = $province->delete();

            if(!$delete){
                return $this->getResponse([],'Provinsi gagal dihapus.',500);
            }

            return $this->getResponse($province,'Provinsi berhasil dihapus',200);
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function get_trash(){
        try{
            $province = Province::with(['cities'])->onlyTrashed()->get();

            if(!$province){
                return $this->getResponse([],'Provinsi tidak ditemukan',404);
            }

            return $this->getResponse($province,'Provinsi trash berhasil ditampilkan');
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function show_trash($id){
        try{
            $province = Province::with(['cities'])->onlyTrashed()->find($id);

            if(!$province){
                return $this->getResponse([],'Provinsi tidak ditemukan',404);
            }

            return $this->getResponse($province,'Provinsi trash berhasil ditampilkan');
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function restore($id){
        try{
            $province = Province::onlyTrashed()->find($id);

            if(!$province){
                return $this->getResponse([],'Province tidak ditemukan',404);
            }

            $restore = $province->restore();

            if(!$restore){
                return $this->getResponse([],'Province gagal dikembalikan',500);
            }

            return $this->getResponse($province, 'Province berhasil dikembalikan');
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function destroy($id){
        try{
            $province = Province::find($id);

            if(!$province){
                $provinceTrash = Province::onlyTrashed()->find($id);
                if(!$provinceTrash){
                    return $this->getResponse([],'Provinsi tidak ditemukan',422);
                }
                $provinceTrashDelete = $provinceTrash->forceDelete();
                if(!$provinceTrashDelete){
                    return $this->getResponse([],'Provinsi gagal dihapus',500);
                }
                return $this->getResponse($provinceTrash,'Provinsi berhasil dihapus',200);
            }

            $provinceDelete = $province->forceDelete();

            if(!$provinceDelete){
                return $this->getResponse([],'Provinsi gagal dihapus',500);
            }

            return $this->getResponse($province,'Provinsi berhasil dihapus',200);
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function select(Request $request){
        try{
            $province = Province::with([]);
            if(isset($request->search)){
                $province = $province->where('LOWER(name)','LIKE','%'.strtolower($request->search).'%');
            }
            if(isset($request->limit)){
                $province = $province->limit($request->limit);
            }

            $province = $province->get();

            if(!$province){
                return $this->getResponse([],'Data gagal ditampilkan',500);
            }

            return $this->getResponse($province, 'Data berhasil ditampilkan');
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }
}
