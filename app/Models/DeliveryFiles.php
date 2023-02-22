<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryFiles extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'delivery_files';
    protected $guarded = ['id'];

    public function delivery(){
        return $this->belongsTo(Delivery::class, 'delivery_id', 'id');
    }
}
