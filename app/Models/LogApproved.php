<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LogApproved extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'log_approved';
    protected $guarded = ['id'];

    public function transaction(){
        return $this->belongsTo(Transaction::class,'transaction_id','id');
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function status(){
        return $this->belongsTo(Status::class,'status_id','id');
    }
}
