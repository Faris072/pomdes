<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function update(Request $request){
        try{
            $attributes = [
                'city_id' => 'Kota',
                'name' => 'Nama',
                'phone' => 'Nomor telpon',
                'email' => 'Email',
            ];

            $message = [
                'numeric' => ':attribute harus berupa angka.',
                'min' => ':attribute minimal 3 karakter',
                'email' => ':attribute tidak valid.'
            ];

            $validatedData = Validator::make($request->all(),[
                'city_id' => 'numeric|nullable',
                'name' => 'min:3|nullable',
                'phone' => 'nullable',
                'email' => 'nullable|email',
            ],$message,$attributes);

            if($validatedData->fails()){
                return $this->getResponse([],$validatedData->getMessageBag(),422);
            }

            $query = Profile::find('user_id',auth()->user()->id)->update([
                'city_id' => $request->city_id,
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email
            ]);

            if(!$query){
                return $this->getMessage([],'Profil gagal diubah.',500);
            }

            return $this->getMessage(User::with(['profile'])->find(auth()->user()->id),'User profile berhasil diubah',200);
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }
}
