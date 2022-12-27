<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class InvoicePusat extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'invoice_pusat';
    protected $guarded = ['id'];

    public function transaction(){
        return $this->belongsTo(Transaction::class,'transaction_id','id');
    }

    public function invoice_pusat_files(){
        return $this->hasMany(InvoicePusatFiles::class, 'invoice_pusat_id','id');
    }
}
