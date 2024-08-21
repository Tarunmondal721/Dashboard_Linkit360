<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TargetOpex extends Model
{
    protected $table = "target_opex";
    
    use HasFactory;

    protected $fillable = ['company_id','year','month','key','opex','target_opex'];

    public static function scopefilterCompany($query,$company_id)
    {
        return $query->where("company_id",$company_id);
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

}
