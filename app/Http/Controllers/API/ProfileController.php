<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\User;
use App\Models\City;
use App\Models\PhotoProfile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function photo(Request $request){
        try{

            $profile = Profile::firstWhere('user_id', auth()->user()->id);
            $photoProfile = PhotoProfile::firstWhere('profile_id',$profile->id);
            $data = (object) [
                'original' => $request->file,
                'profile_id' => $profile->id,
                'name' => $request->file->getClientOriginalName(),
                'extension' => $request->file->getClientOriginalExtension(),
                'is_image' => substr($request->file->getMimeType(), 0, 5) == 'image' ? true : false,
                'size' => $request->file->getSize(),
            ];

            $data->name = date('now').rand('10000','99999').'.'.$data->extension;

            if(!$data->is_image){
                return $this->getResponse([],'File harus berupa .jpg/.jpeg/.png',422);
            }

            if(!$photoProfile){
                $create = PhotoProfile::create((array) $data);
                if(!$create){
                    return $this->getResponse([],'Foto gagal disimpan.',500);
                }
                $save = $request->file->storeAs('profile', $data->name);
                if(!$save){
                    $create->forceDelete();
                    return $this->getResponse([],'Foto gagal disimpan.');
                }

                return $this->getResponse(User::with(['profile','profile.photo_profile'])->find(auth()->user()->id),'Foto berhasil disimpan');
            }

            $delete = Storage::disk('public')->delete('profile/'.$photoProfile->name);

            if(!$delete){
                return $this->getResponse([],'Foto gagal diperbarui.',500);
            }

            $update = $photoProfile->update((array) $data);

            if(!$update){
                return $this->getResponse([],'Foto gagal diperbarui.',500);
            }

            $replace = $request->file->storeAs('profile', $data->name);

            if(!$replace){
                return $this->getResponse([],'Foto gagal diperbarui.',500);
            }

            return $this->getResponse(User::with(['profile','profile.photo_profile'])->find(auth()->user()->id),'Foto berhasil disimpan');
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
