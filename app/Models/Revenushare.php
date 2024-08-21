<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Operator;

class Revenushare extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'operator_id',
        'operator_revenue_share',	
        'merchant_revenue_share',
    ];

    public function operators(){
        return $this->hasMany(Operator::class);
    }

    public function scopeGetOperatorId($query,$operator_id)
    {
        return $query->where('operator_id','=',$operator_id);
    }
}
