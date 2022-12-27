<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Hindrance extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hindrance';
    protected $guarded = ['id'];

    public function hindrance_files(){
        return $this->hasMany(HindranceFiles::class, 'hindrance_id','id');
    }

    public function transaction(){
        return $this->belongsTo(Transaction::class,'transaction_id','id');
    }
}
