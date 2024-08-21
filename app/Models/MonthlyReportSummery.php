<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyReportSummery extends Model
{

	protected $table = "report_summarize_monthly";
    use HasFactory;


    public function country(){
        return $this->hasOne(Country::class,'id', 'country_id');
    }

     public function scopefilterCountry($query,$country_id)
    {
        return $query->where('country_id', $country_id);

    }
    public function scopefilterDateRange($query,$from,$to)
    {
        return $query->whereBetween('date', [$from, $to]);

    }

    public function scopefilterDate($query,$date)
    {
        return $query->where('date', $date);

    }
    public function scopefilteroperator($query,$operator_ids)
    {
        return $query->whereIn('operator_id', $operator_ids);

    }

    public function scopeMonths($query,$months)
    {
        return $query->whereIn('key', $months);

    }
    public function scopeUser($query,$user_id)
    {
        return $query->where('user_id', $user_id);

    }
    public function scopefilteroperatorID($query,$operator_id)
    {
        return $query->where('operator_id', $operator_id);

    }
    

    public function scopefilterMonth($query,$month)
    {
        return $query->whereMonth('date', $month);
    }
    public function scopefilterYear($query,$year)
    {
        return $query->whereYear('date', $year);
    }

    public function scopeUserIn($query,$ids)
    {
        return $query->whereIn('user_id', $ids);
    }

    
    
}
