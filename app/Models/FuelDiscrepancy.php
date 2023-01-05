<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class FuelDiscrepancy extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'fuel_discrepancy';
    protected $guarded = ['id'];

    public function discrepancy(){
        return $this->belongsTo(Discrepancy::class,'discrepancy_id','id');
    }

    public function discrepancy_type(){
        return $this->belongsTo(DiscrepancyType::class, 'discrepancy_type_id','id');
    }

    public function discrepancy_files(){
        return $this->hasOne(DiscrepancyFiles::class,'fuel_discrepancy_id','id');
    }

    public function fuel_transaction(){
        return $this->belongsTo(FuelTransaction::class,'fuel_transaction_id','id');
    }
}
