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
}
