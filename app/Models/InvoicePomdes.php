<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class InvoicePomdes extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'invoice_pomdes';
    protected $guarded = ['id'];

    public function transaction(){
        return $this->belongsTo(Transaction::class,'transaction_id','id');
    }

    public function invoice_pomdes_files(){
        return $this->hasMany(InvoicePomdesFiles::class, 'invoice_pomdes_id','id');
    }
}
