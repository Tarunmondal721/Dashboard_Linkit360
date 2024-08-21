<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Country;
use Illuminate\Support\Facades\DB;

class report_summarize extends Model
{
    use HasFactory;

     protected $table = "report_summarize";

     protected $fillable = [
        'operator_id',
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

    public  function scopeTotalCountry($query)
    {
        # code...

        return $query->select('country_id', 
        DB::raw('SUM(total) as total'))
        ->groupBy('country_id');
    }

    public  function scopeTotalOperator($query)
    {
        # code...

        return $query->select('operator_id as id_operator', 
        DB::raw('SUM(total) as total'))
        ->groupBy('operator_id');
    }

    public  function scopeTotalCompany($query)
    {
        # code...

        return $query->select( 
        DB::raw('SUM(total) as total'));
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

    public function scopeNotDateInclude($query,$date)
    {
        return $query->whereNotIn('date', $date);
    }

    // data sum
    public  function scopeReportDataSumByDays($query,$day)
    {
        return $query->select('operator_id', 
            DB::raw('SUM(fmt_failed)/'.$day.' as fmt_failed'),
            DB::raw('SUM(fmt_success)/'.$day.' as fmt_success'),
            DB::raw('SUM(mt_failed)/'.$day.' as mt_failed'),
            DB::raw('SUM(mt_success)/'.$day.' as mt_success'),
            DB::raw('SUM(gros_rev)/'.$day.' as gros_rev'),
            DB::raw('SUM(total_reg)/'.$day.' as total_reg'),
            DB::raw('SUM(total_unreg)/'.$day.' as total_unreg'),
            DB::raw('SUM(total)/'.$day.' as total'),
            DB::raw('SUM(purge_total)/'.$day.' as purge_total'),
            DB::raw('SUM(fmt_success)/'.$day.' as fmt_success'),
            DB::raw('SUM(fmt_failed)/'.$day.' as fmt_failed'))
            ->groupBy('operator_id');
    }

}
