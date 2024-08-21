<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reconcialiation extends Model
{
    use HasFactory;

    protected $table = "reconcialiation_media";

    public function country()
    {
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

    public function scopeDates($query,$date)
    {
        return $query->whereIn('date', $date);
    }

    public function scopefilteroperator($query,$operator_ids)
    {
        return $query->whereIn('operator_id', $operator_ids);
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
}
