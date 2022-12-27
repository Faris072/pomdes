<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PaymentToPusatFiles extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'payment_to_pusat_files';
    protected $guarded = ['id'];

    public function payment_to_pusat(){
        return $this->belongsTo(PaymentToPusat::class,'payment_to_pusat_id','id');
    }
}
