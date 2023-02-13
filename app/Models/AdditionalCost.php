<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdditionalCost extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'additional_cost';
    protected $guarded = ['id'];

    public function invoice_pomdes(){
        return $this->belongsTo(InvoicePomdes::class, 'invoice_pomdes_id','id');
    }
}
