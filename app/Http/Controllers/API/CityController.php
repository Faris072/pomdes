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
}
