<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RevenushareByDate extends Model
{
    use HasFactory;
    protected $fillable = [
        'operator_id',
        'operator_revenue_share',
        'merchant_revenue_share',
        'year',
        'month',
        'key',
    ];
    public function scopefindRevenushareByOperatorId($query,$operator_id)
    {
        return $query->where('operator_id','=',$operator_id);
    }
}
