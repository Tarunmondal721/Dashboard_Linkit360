<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FinalCostReports extends Model
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
        return $query->where("key",$month);
    }

    public function scopeKey($query,$key)
    {
        return $query->where('key', $key);
    }

    public static function scopefilterService($query,$id_service)
    {
        return $query->where("id_service",$id_service);
    }

    public  function scopeSumCost($query)
    {
        return $query->select('operator_id', 'key',
        DB::raw('SUM(final_cost_campaign) as final_cost_campaign'),
        DB::raw('SUM(rnd) as rnd'),
        DB::raw('SUM(content) as content'),
        DB::raw('SUM(fun_basket) as fun_basket'),
        DB::raw('SUM(bd) as bd'),
        DB::raw('SUM(platform) as platform'),
        DB::raw('SUM(hosting) as hosting'))
        ->groupBy('operator_id','key');
    }
}
