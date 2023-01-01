<?php

namespace App\Models;

use App\Models\Transaction;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable, SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['password','remember_token'];

    public function pusat(){
        return $this->belongsTo(User::class,'pusat_id','id');
    }

    public function pomdes(){
        return $this->hasMany(User::class,'pusat_id','id');
    }

    public function profile(){
        return $this->hasOne(Profile::class,'user_id','id');
    }

    public function role(){
        return $this->belongsTo(Role::class,'role_id','id');
    }

    public function transactions(){
        return $this->hasMany(Transaction::class,'user_id','id');
    }

    public function fuels(){
        return $this->hasMany(Fuel::class,'user_id', 'id');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }


    public function getJWTCustomClaims()
    {
        return [];
    }
}
