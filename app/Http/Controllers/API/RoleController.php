<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;

class RoleController extends Controller
{
    public function get(){
        try{
            $query = Role::all();
            if(!$query){
                return $this->getResponse([],'Data gagal ditampilkan', 500);
            }

            return $this->getResponse($query,'Data berhasil ditampilakn');

        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }
}
