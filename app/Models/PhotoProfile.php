<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PhotoProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'photo_profile';
    protected $guarded = ['id'];

    public function profile(){
        return $this->belongsTo(Profile::class,'profile_id','id');
    }
}
