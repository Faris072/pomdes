<?php

namespace App;

use App\Models\Transaction;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable, SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['password'];

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
