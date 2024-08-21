<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class ReportSummeriseUsers extends Model
{
    use HasFactory;
    protected $fillable = [
        'operator_id',
        'user_id',
        'operator_name',
        'country_id',
        'date',
        'fmt_success',
        'fmt_failed',
        'mt_success',
        'mt_failed',
        'gros_rev',
        'total_reg',
        'total_unreg',
        'total',
        'purge_total',
        'created_at',
        'updated_at',
    ];
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
    public function scopefilteroperatorID($query,$operator_id)
    {
        return $query->where('operator_id', $operator_id);

    }

     public function scopeUser($query,$user_id)
    {
        return $query->where('user_id', $user_id);

    }

    public function scopeUserIn($query,$ids)
    {
        return $query->whereIn('user_id', $ids);

    }


    public function scopefilterMonth($query,$month)
    {
        return $query->whereMonth('date', $month);
    }
    public function scopefilterYear($query,$year)
    {
        return $query->whereYear('date', $year);
    }

    public  function scopeSelectCustom($query)
    {
        # code...

        return $query->select('operator_id',
        DB::raw('SUM(fmt_failed) as fmt_failed'),
        DB::raw('SUM(fmt_success) as fmt_success'),
      DB::raw('SUM(mt_failed) as mt_failed'),

     DB::raw('SUM(mt_success) as mt_success'),
     DB::raw('SUM(gros_rev) as gros_rev'),
      DB::raw('SUM(total_reg) as total_reg'),
       DB::raw('SUM(total_unreg) as total_unreg'),
        DB::raw('SUM(total) as total'),
         DB::raw('SUM(purge_total) as purge_total'),
          DB::raw('SUM(fmt_success) as fmt_success'),
          DB::raw('SUM(fmt_failed) as fmt_failed'))
->groupBy('operator_id');
    }

    public  function scopeCountrySum($query)
    {
        # code...

        return $query->select('country_id',
        DB::raw('SUM(fmt_failed) as fmt_failed'),
        DB::raw('SUM(fmt_success) as fmt_success'),
      DB::raw('SUM(mt_failed) as mt_failed'),

     DB::raw('SUM(mt_success) as mt_success'),
     DB::raw('SUM(gros_rev) as gros_rev'),
      DB::raw('SUM(total_reg) as total_reg'),
       DB::raw('SUM(total_unreg) as total_unreg'),
        DB::raw('SUM(total) as total'),
         DB::raw('SUM(purge_total) as purge_total'),
          DB::raw('SUM(fmt_success) as fmt_success'),
          DB::raw('SUM(fmt_failed) as fmt_failed'))
->groupBy('country_id');
    }

    public  function scopeMonthlySumCron($query)
    {
        # code...

        return $query->select('operator_id',
        DB::raw('SUM(fmt_failed) as fmt_failed'),
        DB::raw('SUM(fmt_success) as fmt_success'),
      DB::raw('SUM(mt_failed) as mt_failed'),

     DB::raw('SUM(mt_success) as mt_success'),
     DB::raw('SUM(gros_rev) as gros_rev'),
      DB::raw('SUM(total_reg) as total_reg'),
       DB::raw('SUM(total_unreg) as total_unreg'),
         DB::raw('SUM(purge_total) as purge_total'),
          DB::raw('SUM(fmt_success) as fmt_success'),
          DB::raw('SUM(fmt_failed) as fmt_failed'))
->groupBy('operator_id');
    }

    public  function scopeUserdataOperator($query)
    {

        return $query->select('operator_id',
        DB::raw('SUM(gros_rev) as gros_rev'))
        ->groupBy('operator_id');
    }

    public  function scopeUserdataCountry($query)
    {

        return $query->select('country_id',
        DB::raw('SUM(gros_rev) as gros_rev'))
        ->groupBy('country_id');
    }

}
