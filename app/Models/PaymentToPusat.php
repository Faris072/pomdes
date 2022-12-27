<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PaymentToPusat extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'payment_to_pusat';
    protected $guarded = ['id'];

    public function transaction(){
        return $this->belongsTo(Transaction::class, 'transaction_id','id');
    }

    public function payment_to_pusat_files(){
        return $this->hasMany(PaymentToPusat::class,'payment_to_pusat_id','id');
    }
}
