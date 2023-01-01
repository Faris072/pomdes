<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\User;
use App\Models\City;
use App\Models\PhotoProfile;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function photo(Request $request){
        try{
            $data = (object) [
                'original' => $request->file,
                'name' => $request->file->getClientOriginalName(),
                'extension' => $request->file->getClientOriginalExtension(),
                'is_image' => substr($request->file->getMimeType(), 0, 5) == 'image' ? true : false,
                'size' => $request->file->getSize(),
            ];

            if(!$data->is_image){
                return $this->getResponse([],'File harus berupa .jpg/.jpeg/.png',422);
            }

            $profile = Profile::firstWhere('user_id', auth()->user()->id);
            $photoProfile = PhotoProfile::firstWhere('profile_id',$profile->id);

            if(!$photoProfile){
                // $request-
            }
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

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

            $city = City::find($request->city_id);

            if(!$city){
                return $this->getResponse([],'City tidak ditemukan',404);
            }

            $query = Profile::firstWhere('user_id',auth()->user()->id)->update([
                'city_id' => $request->city_id,
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => strtolower($request->email)
            ]);

            if(!$query){
                return $this->getResponse([],'Profil gagal diubah.',500);
            }

            return $this->getResponse(User::with(['profile', 'profile.photo_profile'])->find(auth()->user()->id),'User profile berhasil diubah',200);
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }
}
