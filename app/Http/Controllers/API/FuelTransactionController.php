<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\FuelTransaction;

class FuelTransactionController extends Controller
{
    public function store($request, $data){
        try{
            foreach($request->all()['fuels'] as $fuel){
                $fuel = (object) $fuel;

                $query = FuelTransaction::create([
                    'fuel_id' => $fuel->fuel_id,
                    'transaction_id' => $data->id,
                    'volume' => $fuel->volume,
                    'price' => $fuel->price,
                ]);

                if(!$query){
                    return $this->getResponse([], 'Data gagal disimpan', 500);
                }

            };

            return true;
        }
        catch(\Exception $e){
            return false;
        }
    }

    public function update($request, $id){
        try{
            FuelTransaction::where('transaction_id', $id)->delete();

            foreach($request->all()['fuels'] as $fuel){
                $fuel = (object) $fuel;

                $query = FuelTransaction::create([
                    'fuel_id' => $fuel->fuel_id,
                    'transaction_id' => $id,
                    'volume' => $fuel->volume,
                    'price' => $fuel->price,
                ]);

                if(!$query){
                    FuelTransaction::where('transaction_id', $id)->forceDelete();
                    FuelTransaction::onlyTrashed()->where('transaction_id',$id)->restore();
                    return $this->getResponse([], 'Data gagal disimpan', 500);
                }
            };

            return true;
        }
        catch(\Exception $e){
            return false;
        }
    }
}
