<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhtByDate extends Model
{
    use HasFactory;

    protected $fillable = [
        'operator_id',
        'wht',
        'year',
        'month',
        'key',
    ];
    public function scopefindWhtByOperatorId($query,$operator_id)
    {
        return $query->where('operator_id','=',$operator_id);
    }
}
