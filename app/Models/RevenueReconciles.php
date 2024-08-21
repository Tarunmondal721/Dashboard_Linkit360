<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RevenueReconciles extends Model
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

    public function scopefilteroperators($query,$operator_ids)
    {
        return $query->whereIn('operator_id', $operator_ids);
    }

    public static function scopefilterYear($query,$year)
    {
        return $query->where("year",$year);
    }

    public static function scopefilterMonth($query,$month)
    {
        return $query->where("month",$month);
    }

    public static function scopefilterYearMonths($query,$keys)
    {
        return $query->whereIn("CONCAT(`year`, '-', `month`)",$keys);
    }

    public function scopeMonths($query,$months)
    {
        return $query->whereIn('key', $months);
    }

    public function scopeKey($query,$key)
    {
        return $query->where('key', $key);
    }

    public static function scopefilterService($query,$id_service)
    {
        return $query->where("id_service",$id_service);
    }

    public  function scopeSumReconcile($query)
    {
        return $query->select('operator_id', 'key',
        DB::raw('SUM(revenue) as revenue'),
        DB::raw('SUM(revenue_telco) as revenue_telco'),
        DB::raw('SUM(net_revenue) as net_revenue'))
        ->groupBy('operator_id','key');
    }
}
