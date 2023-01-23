<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\City;
use Illuminate\Support\Facades\Validator;

class CityController extends Controller
{
    public function store(Request $request){
        try{
            $message = [
                'name.required' => 'Nama provinsi wajib di isi.',
                'name.min' => 'Nama provinsi minimal 3 karakter.',
                'province_id.required' => 'Provinsi wajib dipilih.',
                'province_id.numeric' => 'Id provinsi harus berupa angka.'
            ];

            $validatedData = Validator::make($request->all(),[
                'province_id' => 'required|numeric',
                'name' => 'required|min:3',
            ],$message);

            if($validatedData->fails()){
                return $this->getResponse([],$validatedData->getMessageBag(),422);
            }

            $query = City::create([
                'province_id' => $request->province_id,
                'name' => $request->name
            ]);

            if(!$query){
                return $this->getResponse([],'Data kota gagal ditambahkan.',500);
            }

            return $this->getResponse($query,'Data kota berhasil ditambahkan',200);
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function get(){
        try{
            $city = City::with(['province'])->get();

            if(!$city){
                return $this->getResponse([],'City gagal dimuat',404);
            }

            return $this->getResponse($city,'City berhasil ditampilkan');
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function show($id){
        try{
            $city = City::with(['province'])->find($id);

            if(!$city){
                return $this->getResponse([],'City tidak ditemukan',404);
            }

            return $this->getResponse($city,'City berhasil ditampilkan');
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function update(Request $request, $id){
        try{
            $city = City::find($id);

            if(!$city){
                return $this->getResponse([],'City tidak ditemukan',404);
            }

            $message = [
                'name.required' => 'Nama provinsi wajib di isi.',
                'name.min' => 'Nama provinsi minimal 3 karakter.',
                'province_id.required' => 'Provinsi wajib dipilih.',
                'province_id.numeric' => 'Id provinsi harus berupa angka.'
            ];

            $validatedData = Validator::make($request->all(),[
                'province_id' => 'required|numeric',
                'name' => 'required|min:3',
            ],$message);

            if($validatedData->fails()){
                return $this->getResponse([],$validatedData->getMessageBag(),422);
            }

            $query = $city->update([
                'province_id' => $request->province_id,
                'name' => $request->name
            ]);

            if(!$query){
                return $this->getResponse([],'Data kota gagal diubah.',500);
            }

            return $this->getResponse($query,'Data kota berhasil diubah',200);
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function delete($id){
        try{
            $city = City::find($id);

            if(!$city){
                return $this->getResponse([],'City tidak ditemukan',404);
            }

            $delete = $city->delete();

            if(!$delete){
                return $this->getResponse([],'City gagal dihapus',500);
            }

            return $this->getResponse($city, 'City berhasil dihapus');
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function get_trash(){
        try{
            $City = City::with(['province'])->onlyTrashed()->get();

            if(!$City){
                return $this->getResponse([],'City tidak ditemukan',404);
            }

            return $this->getResponse($City,'City trash berhasil ditampilkan');
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function show_trash($id){
        try{
            $City = City::with(['province'])->onlyTrashed()->find($id);

            if(!$City){
                return $this->getResponse([],'City tidak ditemukan',404);
            }

            return $this->getResponse($City,'City trash berhasil ditampilkan');
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function restore($id){
        try{
            $city = City::onlyTrashed()->find($id);

            if(!$city){
                return $this->getResponse([],'City tidak ditemukan',404);
            }

            $restore = $city->restore();

            if(!$restore){
                return $this->getResponse([],'City gagal dikembalikan',500);
            }

            return $this->getResponse($city, 'City berhasil dikembalikan');
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function destroy($id){
        try{
            $city = City::find($id);

            if(!$city){
                $cityTrash = City::onlyTrashed()->find($id);
                if(!$cityTrash){
                    return $this->getResponse([],'City tidak ditemukan',404);
                }
                $cityTrashDelete = $cityTrash->forceDelete();
                if(!$cityTrashDelete){
                    return $this->getResponse([],'City gagal dihapus',500);
                }
                return $this->getResponse($cityTrash,'City berhasil dihapus');
            }

            $cityDelete = $city->forceDelete();
            if(!$cityDelete){
                return $this->getResponse([],'City gagal dihapus',500);
            }
            return $this->getResponse($city,'City berhasil dihapus');
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function select(Request $request){
        try{
            // $city = City::all();
            $city = City::with([]);

            if(isset($request->province_id)){
                $city = $city->where('province_id',$request->province_id);
            }

            if(isset($request->search)){
                $city = $city->whereRaw("LOWER(name) LIKE '%".strtolower($request->search)."%'");
            }

            if(isset($request->limit)){
                $city = $city->take($request->limit);
            }

            $city = $city->get();

            if(!$city){
                return $this->getResponse([],'Data gagal ditampilkan',500);
            }

            return $this->getResponse($city, 'Data berhasil ditampilkan');
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }
}
