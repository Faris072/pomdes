<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Fuel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'fuel';
    protected $guarded = ['id'];

    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function fuel_transactions(){
        return $this->hasMany(FuelTransaction::class, 'fuel_id','id');
    }
}
