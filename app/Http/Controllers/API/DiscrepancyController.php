<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DiscrepancyType;

class DiscrepancyController extends Controller
{
    public function select_discrepancy_type(Request $request){
        try{
            $discrepancyType = DiscrepancyType::with([]);
            if(isset($request->search)){
                $discrepancyType = $discrepancyType->whereRaw("LOWER(name) LIKE '%".strtolower($request->search)."%'");
            }
            if(isset($request->limit)){
                $discrepancyType = $discrepancyType->limit($request->limit);
            }

            $discrepancyType = $discrepancyType->get();

            if(!$discrepancyType){
                return $this->getResponse([],'Data gagal ditampilkan',500);
            }

            return $this->getResponse($discrepancyType, 'Data berhasil ditampilkan');
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }
}
