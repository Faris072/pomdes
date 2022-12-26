<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class DiscrepancyFiles extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'discrepancy_files';
    protected $guarded = ['id'];

    public function discrepancy(){
        return $this->belongsTo(Discrepancy::class, 'discrepancy_id','id');
    }
}
