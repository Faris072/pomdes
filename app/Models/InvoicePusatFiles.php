<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class InvoicePusatFiles extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'invoice_pusat_files';
    protected $guarded = ['id'];

    public function invoice_pusat(){
        return $this->belongsTo(InvoicePusat::class,'invoice_pusat_id','id');
    }
}
