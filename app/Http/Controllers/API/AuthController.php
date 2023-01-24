<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Profile;

class AuthController extends Controller
{
    public function register(Request $request){
        try{
            if(auth()->user()->role_id != 1){
                return $this->getResponse([],'Anda tidak memiliki akses',403);
            }

            $messages = [
                'required' => ':attribute tidak boleh kosong.',
                'numeric' => ':attribute harus berupa angka.',
                'unique' => ':attribute sudah ada.',
                'username.min' => 'Username minimal 3 karakter.',
                'password.min' => 'Password minimal 8 karakter.',
                'regex' => ':attribute tidak boleh ada spasi, kapital dan angka di depan' //tidak boleh spasi, kapital, angka di depan
            ];

            $attributes = [
                'role_id' => 'Role',
                'pusat_id' => 'Pusat',
                'username' => 'Usename',
                'password' => 'Password',
            ];

            $validatedData = Validator::make($request->all(),[
                'pusat_id' => 'numeric|nullable',
                'role_id' => 'required|numeric',
                'username' => 'required|min:3|unique:users|regex:/^[a-z][0-9a-z_]{2,23}[0-9a-z]$/',
                'password' => 'required|min:8'
            ],$messages,$attributes);

            if($validatedData->fails()){
                return $this->getResponse([],$validatedData->getMessageBag(),422);
            }

            if($request->role_id == 3){
                $pusat = User::find($request->pusat_id);
                if(empty($request->pusat_id)){
                    return $this->getResponse([],'Id pusat wajib dipilih',422);
                }
                if(!$pusat){
                    return $this->getResponse([],'User tidak ditemukan',422);
                }
                if($pusat->role_id != 2){
                    return $this->getResponse([],'Id Pusat harus user yang mempunyai role id pusat',422);
                }
            }
            else{
                $request->pusat_id = null;
            }

            $query = User::create([
                'role_id' => $request->role_id,
                'pusat_id' => $request->pusat_id,
                'username' => $request->username,
                'password' => bcrypt($request->password)
            ]);

            $profile = Profile::create(['user_id'=>$query->id]);

            if(!$query){
                return $this->getResponse([],'Terjadi kesalahan koneksi',500);
            }
            else if(!$profile){
                $query->forceDelete();
                return $this->getResponse([],'Terjadi kesalahan koneksi',500);
            }
            else{
                return $this->getResponse($query,'Data user berhasil disimpan');
            }
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function get_users(){
        try{
            $user = User::with(['pusat', 'pomdes', 'role', 'profile', 'profile.photo_profile'])->get();
            if(!$user){
                return $this->getResponse([],'User tidak ditemukan',422);
            }

            return $this->getResponse($user,'User berhasil ditampilkan');
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function show_user($id){
        try{
            $user = User::with(['pusat', 'pomdes', 'role', 'profile', 'profile.photo_profile'])->find($id);
            if(!$user){
                return $this->getResponse([],'User tidak ditemukan',422);
            }

            return $this->getResponse($user,'User berhasil ditampilkan');
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function update(Request $request,$id){
        try{
            if(auth()->user()->role_id != 1){
                return $this->getResponse([],'Anda tidak memiliki akses.',403);
            }

            $user = User::find($id);
            if(!$user){
                return $this->getResponse([],'User tidak ditemukan',422);
            }

            $messages = [
                'required' => ':attribute tidak boleh kosong.',
                'numeric' => ':attribute harus berupa angka.',
                'unique' => ':attribute sudah ada.',
                'username.min' => 'Username minimal 3 karakter.',
                'regex' => ':attribute tidak boleh ada spasi, kapital dan angka di depan' //tidak boleh spasi, kapital, angka di depan
            ];

            $attributes = [
                'role_id' => 'Role',
                'pusat_id' => 'Pusat',
                'username' => 'Usename',
            ];

            $validatedData = Validator::make($request->all(),[
                'pusat_id' => 'numeric|nullable',
                'role_id' => 'required|numeric',
                'username' => 'required|min:3|unique:users|regex:/^[a-z][0-9a-z_]{2,23}[0-9a-z]$/',
            ],$messages,$attributes);

            if($validatedData->fails()){
                return $this->getResponse([],$validatedData->getMessageBag(),422);
            }

            if($request->role_id == 3){
                $pusat = User::find($request->pusat_id);
                if(empty($request->pusat_id)){
                    return $this->getResponse([],'Id pusat wajib dipilih',422);
                }
                if(!$pusat){
                    return $this->getResponse([],'User tidak ditemukan',422);
                }
                if($pusat->role_id != 2){
                    return $this->getResponse([],'Id Pusat harus user yang mempunyai role id pusat',422);
                }
            }
            else{
                $request->pusat_id = null;
            }

            $query = $user->update([
                'role_id' => $request->role_id,
                'pusat_id' => $request->pusat_id,
                'username' => $request->username,
            ]);

            if($query){
                return $this->getResponse($query,'Perubahan berhasil disimpan');
            }
            else{
                return $this->getResponse([],'Terjadi kesalahan koneksi',500);
            }

            return $this->getResponse($user,'User berhasil ditampilkan');
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function login()
    {
        $credentials = request(['username', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Login gagal. Username / Password tidak ditemukan'], 401);
        }

        $data = [
            'me' => User::with(['profile','profile.photo_profile', 'role'])->find(auth()->user()->id),
            'auth' => $this->respondWithToken($token)->original
        ];
        return $this->getResponse($data,'Login berhasil');
    }

    public function me()
    {
        $data = [
            'me' => User::with(['profile','profile.photo_profile', 'role'])->find(auth()->user()->id),
        ];
        return $this->getResponse($data,'Login berhasil');
    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        $data = [
            'me' => auth()->user(),
            'auth' => $this->respondWithToken(auth()->refresh())->original
        ];
        return $this->getResponse($data,'Refresh token berhasil');
    }

    public function delete($id){
        try{
            if(auth()->user()->role_id != 1){
                return $this->getResponse([],'Anda tidak memiliki akses.',403);
            }

            $user = User::find($id);

            if(!$user){
                return $this->getResponse([],'User tidak ditemukan',422);
            }

            $delete = $user->delete();

            if(!$delete){
                return $this->getResponse([],'User gagal dihapus',500);
            }

            return $this->getResponse($user,'User berhasil dihapus',200);
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function kill($id){
        try{
            if(auth()->user()->role_id != 1){
                return $this->getResponse([],'Anda tidak memiliki akses.',403);
            }

            $user = User::find($id);

            if(!$user){
                $userTrash = User::onlyTrashed()->find($id);
                if(!$userTrash){
                    return $this->getResponse([],'User tidak ditemukan',422);
                }
                $userTrashDelete = $userTrash->forceDelete();
                if(!$userTrashDelete){
                    return $this->getResponse([],'User gagal dihapus',500);
                }
                return $this->getResponse([],'User berhasil di destroy',200);
            }

            $delete = $user->forceDelete();

            if(!$delete){
                return $this->getResponse([],'User gagal di destroy',500);
            }

            return $this->getResponse($user,'User berhasil di destroy',200);
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function get_trashed(){
        try{
            if(auth()->user()->role_id != 1){
                return $this->getResponse([],'Anda tidak memiliki akses.',403);
            }

            $users = User::with(['profile', 'pomdes', 'role', 'pusat', 'profile.photo_profile'])->onlyTrashed()->get();

            if(!$users){
                return $this->getResponse([],'User tidak ditemukan',422);
            }

            return $this->getResponse($users,'User berhasil ditampilkan',200);
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function show_trashed($id){
        try{
            if(auth()->user()->role_id != 1){
                return $this->getResponse([],'Anda tidak memiliki akses.',403);
            }

            $user = User::with(['profile', 'pomdes', 'role', 'pusat', 'profile.photo_profile'])->onlyTrashed()->find($id);

            if(!$user){
                return $this->getResponse([],'User tidak ditemukan',422);
            }

            return $this->getResponse($user,'User berhasil ditampilkan',200);
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function restore($id){
        try{
            if(auth()->user()->role_id != 1){
                return $this->getResponse([],'Anda tidak memiliki akses.',403);
            }

            $user = User::onlyTrashed()->find($id);

            if(!$user){
                return $this->getResponse([],'User tidak ditemukan',422);
            }

            $query = $user->restore();

            if(!$query){
                return $this->getResponse([],'User gagal dikembalikan',500);
            }

            return $this->getResponse($user,'User berhasil dikembalikan',200);
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 6000
        ]);
    }

    public function select_pusat(Request $request){
        try{
            $query = User::with([])->where('role_id', 2);

            if(isset($request->search)){
                $query = $query->whereRaw("LOWER(username) LIKE '%".strtolower($request->search)."%'")
                    ->orWhereHas('profile',function($q) use ($request){
                        $q->whereRaw("LOWER(name) LIKE '%".strtolower($request->search)."%'");
                    })
                    ->orWhereHas('profile.city', function($q) use ($request){
                        $q->whereRaw("LOWER(name) LIKE '%".strtolower($request->search)."%'");
                    })
                    ->orWhereHas('profile.city.province', function($q) use ($request){
                        $q->whereRaw("LOWER(name) LIKE '%".strtolower($request->search)."%'");
                    });
            }

            $query = $query->get();

            if(!$query){
                return $this->getResponse([],'Data gagal ditemukan',500);
            }

            return $this->getResponse($query,'Data berhasil ditampilkan');
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }
}
