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

    public function select(Request $request){
        try{
            $query = Role::with([]);

            if(isset($request->search)){
                $query = $query->whereRaw("LOWER(name) LIKE '%".strtolower($request->search)."%'");
            }
            if(isset($request->limit)){
                $query = $query->limit($request->limit);
            }

            $query = $query->get();

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
