<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Discrepancy extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'discrepancy';
    protected $guarded = ['id'];

    public function discrepancy_files(){
        return $this->hasMany(DiscrepancyFiles::class, 'discrepancy_id', 'id');
    }

    public function transaction(){
        return $this->belongsTo(Transaction::class, 'transaction_id','id');
    }
}
