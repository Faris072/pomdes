<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class HindranceFiles extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $table = 'hindrance_files';

    public function hindrance(){
        return $this->belongsTo(Hindrance::class, 'hindrance_id','id');
    }
}
