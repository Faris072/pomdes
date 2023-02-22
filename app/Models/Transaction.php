<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'transaction';
    protected $guarded = ['id'];

    public function user(){
        return $this->belongsTo(User::class, 'user_id','id');
    }

    public function status(){
        return $this->belongsTo(Status::class, 'status_id','id');
    }

    public function invoice_pomdes(){
        return $this->hasOne(InvoicePomdes::class, 'transaction_id','id');
    }

    public function fuel_transactions(){
        return $this->hasMany(FuelTransaction::class,'transaction_id','id');
    }

    public function hindrance(){
        return $this->hasOne(Hindrance::class,'transaction_id','id');
    }

    public function discrepancy(){
        return $this->hasOne(Discrepancy::class, 'transaction_id','id');
    }

    public function submission_files(){
        return $this->hasMany(SubmissionFiles::class, 'transaction_id','id');
    }

    public function reject(){
        return $this->hasOne(Reject::class,'transaction_id','id');
    }
}
