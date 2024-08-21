<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MiscTax extends Model
{
    use HasFactory;

    protected $fillable = [
        'operator_id',
        'misc_tax',
        'year',
        'month',
        'key',
    ];
    public function scopefindWhtByOperatorId($query,$operator_id)
    {
        return $query->where('operator_id','=',$operator_id);
    }
}
