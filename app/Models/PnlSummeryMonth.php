<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PnlSummeryMonth extends Model
{
    use HasFactory;

    public function country()
    {
        return $this->hasOne(Country::class,'id', 'country_id');
    }

    public function operator()
    {
        return $this->hasOne(Operator::class, 'id_operator', 'id_operator');
    }

    public function scopefilterCountry($query,$country_ids)
    {
        return $query->whereIn('country_id', $country_ids);
    }

    public function scopefilterDateRange($query,$from,$to)
    {
        return $query->whereBetween('date', [$from, $to]);
    }

    public function scopefilterOperator($query,$operator_ids)
    {
        return $query->whereIn('id_operator', $operator_ids);
    }

    
    public function scopeMonths($query,$month)
    {
        return $query->whereIn('key', $month);
    }
    

    public function scopefilteroperatorID($query,$operator_id)
    {
        return $query->where('id_operator', $operator_id);
    }

    public function scopefilterMonth($query,$month)
    {
        return $query->whereMonth('date', $month);
    }

    public function scopeWhendate($query,$date)
    {
        return $query->where('date', $date);
    }

    public function scopefilterYear($query,$year)
    {
        return $query->whereYear('date', $year);
    }

    public function scopeUser($query,$user_id)
    {
        return $query->where('user_id', $user_id);
    }
   

   
}
