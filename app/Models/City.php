<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'city';
    protected $guarded = ['id'];

    public function province(){
        return $this->belongsTo(Province::class, 'province_id','id');
    }

    public function profiles(){
        return $this->hasMany(Profile::class, 'profile_id','id');
    }
}

