<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Reject extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $table = 'reject';

    public function transaction(){
        return $this->belongsTo(Transaction::class, 'transaction_id','id');
    }

    public function status(){
        return $this->belongsTo(Status::class, 'status_id','id');
    }
}
