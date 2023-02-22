<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Delivery extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'delivery';
    protected $guarded = ['id'];

    public function transaction(){
        return $this->belongsTo(Transaction::class, 'transaction_id', 'id');
    }

    public function delivery_files(){
        return $this->hasMany(DeliveryFiles::class,'delivery_id','id');
    }
}
