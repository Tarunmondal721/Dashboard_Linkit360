<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TargetRevenueReconciles extends Model
{
    use HasFactory;

    public static function scopefilterCountry($query,$country_id)
    {
        return $query->where("country_id",$country_id);
    }

    public static function scopefilterOperator($query,$operator_id)
    {
        return $query->where("operator_id",$operator_id);
    }

    public static function scopefilterYear($query,$year)
    {
        return $query->where("year",$year);
    }

    public static function scopefilterMonth($query,$month)
    {
        return $query->where("month",$month);
    }

    public function scopeKey($query,$key)
    {
        return $query->where('key', $key);
    }

    public static function scopefilterService($query,$id_service)
    {
        return $query->where("id_service",$id_service);
    }

    public  function scopeSumTarget($query)
    {
        return $query->select('operator_id', 'key',
        DB::raw('SUM(revenue) as revenue'),
        DB::raw('SUM(revenue_after_share) as revenue_after_share'),
        DB::raw('SUM(pnl) as pnl'),
        DB::raw('SUM(opex) as opex'),
        DB::raw('SUM(ebida) as ebida'))
        ->groupBy('operator_id','key');
    }
}
