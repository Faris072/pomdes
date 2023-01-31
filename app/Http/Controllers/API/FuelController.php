<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Fuel;

class FuelController extends Controller
{
    public function get(Request $request){
        try{
            $attributes = [
                'user_id' => 'Supplier',
                'name' => 'Nama bahan bakar'
            ];

            $messages = [
                'required' => ':attribute wajib dipilih.',
            ];

            $validatedData = Validator::make($request->all(),[
                'user_id' => 'required'
            ],$messages,$attributes);
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }
}
