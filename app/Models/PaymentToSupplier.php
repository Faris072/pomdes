<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PaymentToSupplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'payment_to_supplier';
    protected $guarded = ['id'];

    public function transaction(){
        return $this->belongsTo(Transaction::class, 'transaction_id','id');
    }

    public function payment_to_suppier_files(){
        return $this->hasMany(PaymentToSupplierFiles::class, 'payment_to_supplier_id','id');
    }
}
