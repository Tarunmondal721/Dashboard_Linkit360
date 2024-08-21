<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VatByDate extends Model
{
    use HasFactory;

    protected $fillable = [
        'operator_id',
        'vat',
        'year',
        'month',
        'key',
    ];
    public function scopefindVatByOperatorId($query,$operator_id)
    {
        return $query->where('operator_id','=',$operator_id);
    }
}
