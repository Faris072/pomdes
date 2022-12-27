<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class FuelTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'fuel_transaction';
    protected $guarded = ['id'];

    public function fuel(){
        return $this->belongsTo(Fuel::class,'fuel_id','id');
    }

    public function transaction(){
        return $this->belongsTo(Transaction::class,'transaction_id','id');
    }

    public function fuel_discrepancy(){
        return $this->hasOne(FuelDiscrepancy::class,'fuel_transaction_id','di');
    }
}
