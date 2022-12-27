<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class DiscrepancyType extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'discrepancy_type';
    protected $guarded = ['id'];

    public function discrepancies(){
        return $this->hasMany(FuelDiscrepancy::class,'discrepancy_type_id','id');
    }
}
