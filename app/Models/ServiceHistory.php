<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ServiceHistory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'operator_id',
        'operator_name',
        'id_service',
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

    public  static function scopeSumByDateServiceData($query,$operator_id,$services,$date)
    {
        return $query->where('operator_id', $operator_id)
                    ->where('date', $date)
                     ->whereIn('id_service', $services)
                     ->select('operator_id',
                                DB::raw('SUM(fmt_failed) as total_fmt_failed'),
                                DB::raw('SUM(fmt_success) as total_fmt_success'),
                              DB::raw('SUM(mt_failed) as total_mt_failed'),

                             DB::raw('SUM(mt_success) as total_mt_success'),
                             DB::raw('SUM(gros_rev) as total_gros_rev'),
                              DB::raw('SUM(total_reg) as total_total_reg'),
                               DB::raw('SUM(total_unreg) as total_total_unreg'),
                                DB::raw('SUM(total) as total_total'),
                                 DB::raw('SUM(purge_total) as total_purge_total'),
                                  DB::raw('SUM(fmt_success) as total_fmt_success'),
                                  DB::raw('SUM(fmt_failed) as total_fmt_failed'),

                              )
                     ->groupBy('operator_id');
    }

    public  static function scopeGetFilterServiceData($query,$operator_id,$services,$date)
    {
        //->whereBetween('created_at', [$startDate, $endDate])
        dd($date);
        $query = $query->where('operator_id', $operator_id);
        $query = $query->where('id_service', $services);
                    $date = explode("_",$date);
                    if(count($date) > 1)
                    {
                        $from = $date[0];
                        $to = $date[1];
                        $query = $query->where('date', [$to,$from]);

                    }else{

                        $date = $date[0];
                        $query = $query->where('date', $date);
                    }
        return $query;
    }

    public function scopefilterService($query,$service_ids)
    {
        return $query->whereIn('id_service', $service_ids);
    }



    public function scopeFilterDateRange($query,$from,$to)
    {
        return $query->whereBetween('date', [$from, $to]);
    }
    public function scopeFilterOperator($query,$id)
    {
        return $query->where('operator_id', $id);
    }

    public function scopefilterDate($query,$date)
    {
        return $query->where('date', $date);
    }

    public function scopefilterMonth($query,$date)
    {
        return $query->where('date', 'like', '%'.$date.'%');
    }

    public function scopeNotDateInclude($query,$date)
    {
        return $query->whereNotIn('date', $date);
    }

    public function scopeMonth($query,$month)
    {
        return $query->whereMonth('date', $month);
    }

    public function scopeYear($query,$year)
    {
        return $query->whereYear('date', $year);
    }
    public function revenueshare(){
        return $this->hasOne(Revenushare::class, 'operator_id', 'operator_id');
    }

    public function services(){
        return $this->hasOne(Service::class, 'id_service', 'id_service');
    }
    public function scopefilteroperatorid($query,$operator_ids)
    {
        return $query->whereIn('operator_id', $operator_ids);

    }
    public function scopefilterserviceid($query,$service_id)
    {
        return $query->where('id_service', $service_id);

    }

}
