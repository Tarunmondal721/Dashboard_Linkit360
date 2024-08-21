<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeeklyCaps extends Model
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
}
