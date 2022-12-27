<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class InvoicePomdesFiles extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'invoice_pomdes_files';
    protected $guarded = ['id'];

    public function invoice_pomdes(){
        return $this->belongsTo(InvoicePomdes::class,'invoice_pomdes_id', 'id');
    }
}
