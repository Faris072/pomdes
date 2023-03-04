<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DiscrepancyBefore extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'discrepancy_before';
    protected $guarded = ['id'];

    public function transaction(){
        return $this->belongsTo(Transaction::class,'transaction_id','id');
    }

    public function discrepancy(){
        return $this->belongsTo(Discrepancy::class,'discrepancy_id','id');
    }
}
