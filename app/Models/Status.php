<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'status';
    protected $guarded = ['id'];

    public function transactions(){
        return $this->hasMany(Transaction::class, 'status_id','id');
    }

    public function log_approveds(){
        return $this->hasMany(LogApproved::class,'status_id','id');
    }
}
