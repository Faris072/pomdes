<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PaymentToSupplierFiles extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'payment_to_supplier_files';
    protected $guarded = ['id'];

    public function payment_to_supplier(){
        return $this->belongsTo(PaymentToSupplier::class,'payment_to_supplier_id','id');
    }
}
