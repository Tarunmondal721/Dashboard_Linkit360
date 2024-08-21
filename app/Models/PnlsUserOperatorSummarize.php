<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PnlsUserOperatorSummarize extends Model
{
    use HasFactory;




    public function scopefilterDateRange($query,$from,$to)
    {
        return $query->whereBetween('date', [$from, $to]);
    }

    public function scopefilterOperator($query,$operator_ids)
    {
        return $query->whereIn('id_operator', $operator_ids);
    }

    public  function scopeSumOfAllPnlDataCounty($query)
    {
        return $query->select('date', 'country_id',
        DB::raw('SUM(rev_usd) as rev_usd'),
        DB::raw('SUM(rev) as rev'),
        DB::raw('SUM(share) as share'),
        DB::raw('SUM(lshare) as lshare'),
        DB::raw('SUM(cost_campaign) as cost_campaign'),
        DB::raw('SUM(other_cost) as other_cost'),
        DB::raw('SUM(hosting_cost) as hosting_cost'),
        DB::raw('SUM(content) as content'),
        DB::raw('SUM(rnd) as rnd'),
        DB::raw('SUM(bd) as bd'),
        DB::raw('SUM(platform) as platform'),
        DB::raw('SUM(pnl) as pnl'))
        ->groupBy('country_id');
    }

}
