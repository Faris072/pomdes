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

    public function delete(){
        try{}
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }
}
