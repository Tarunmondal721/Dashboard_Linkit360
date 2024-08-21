<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ReportsSummarizeDashbroads extends Model
{
    use HasFactory;

    protected $table = "reports_summarize_dashbroads";

    public function scopefilterCountryID($query,$country_id)
    {
        return $query->where('country_id', $country_id);
    }

    public function scopefilterMonth($query,$month)
    {
        return $query->whereMonth('date', $month);
    }

    public function scopefilterYear($query,$year)
    {
        return $query->whereYear('date', $year);
    }

    public function scopefilterDate($query,$date)
    {
        return $query->where('date', $date);
    }

    public function scopefilterCompanyID($query,$company_id)
    {
        return $query->whereYear('company_id', $company_id);
    }
    public function scopefilterCompanyIDs($query,$company_id)
    {
        return $query->whereIn('company_id', $company_id);
    }
    public function scopefilterOperator($query, $operator_ids)
    {
        return $query->whereIn('operator_id', $operator_ids);
    }

    public function scopefilterNotOperator($query, $operator_ids)
    {
        return $query->whereNotIn('operator_id', $operator_ids);
    }


    public function scopeSumOfDashboardSummeryData($query)
    {
        /*'count(operator_id) as total_operator',
            'SUM(current_revenue_usd) as current_revenue_usd',
            'SUM(current_mo) as current_mo',
            'SUM(current_cost) as current_cost',
            'SUM(current_pnl) as current_pnl',
            'SUM(last_revenue_usd) as last_revenue_usd',
            'SUM(last_mo) as last_mo',
            'SUM(last_cost) as last_cost',
            'SUM(last_pnl) as last_pnl',
            'SUM(prev_revenue_usd) as prev_revenue_usd',
            'SUM(prev_mo) as prev_mo',
            'SUM(prev_cost) as prev_cost',
            'SUM(prev_pnl) as prev_pnl',
            'SUM(current_price_mo) as current_price_mo',
            'SUM(current_30_arpu) as current_30arpu',
            'SUM(estimated_price_mo) as estimated_price_mo',
            'SUM(estimated_30_arpu) as estimated_30arpu',
            'SUM(last_price_mo) as last_price_mo',
            'SUM(last_30_arpu) as last_30arpu',
            'SUM(prev_price_mo) as prev_price_mo',
            'SUM(prev_30_arpu) as prev_30arpu'*/

        return $query->select('country_id','updated_at',
            DB::raw('SUM(current_revenue) as current_revenue'),
            DB::raw('SUM(current_revenue_usd) as current_revenue_usd'),
            DB::raw('SUM(current_gross_revenue) as current_gross_revenue'),
            DB::raw('SUM(current_gross_revenue_usd) as current_gross_revenue_usd'),
            DB::raw('SUM(current_mo) as current_mo'),
            DB::raw('SUM(current_cost) as current_cost'),
            DB::raw('SUM(current_reg_sub) as current_reg_sub'),
            DB::raw('SUM(current_usd_rev_share) as current_usd_rev_share'),
            DB::raw('SUM(current_pnl) as current_pnl'),
            DB::raw('SUM(current_roi) as current_roi'),
            DB::raw('SUM(last_revenue) as last_revenue'),
            DB::raw('SUM(last_revenue_usd) as last_revenue_usd'),
            DB::raw('SUM(last_gross_revenue) as last_gross_revenue'),
            DB::raw('SUM(last_gross_revenue_usd) as last_gross_revenue_usd'),
            DB::raw('SUM(last_mo) as last_mo'),
            DB::raw('SUM(last_cost) as last_cost'),
            DB::raw('SUM(last_usd_rev_share) as last_usd_rev_share'),
            DB::raw('SUM(last_reg_sub) as last_reg_sub'),
            DB::raw('SUM(last_pnl) as last_pnl'),
            DB::raw('SUM(last_roi) as last_roi'),
            DB::raw('SUM(prev_revenue) as prev_revenue'),
            DB::raw('SUM(prev_revenue_usd) as prev_revenue_usd'),
            DB::raw('SUM(prev_gross_revenue) as prev_gross_revenue'),
            DB::raw('SUM(prev_gross_revenue_usd) as prev_gross_revenue_usd'),
            DB::raw('SUM(prev_mo) as prev_mo'),
            DB::raw('SUM(prev_cost) as prev_cost'),
            DB::raw('SUM(previous_usd_rev_share) as previous_usd_rev_share'),
            DB::raw('SUM(previous_reg_sub) as previous_reg_sub'),
            DB::raw('SUM(prev_pnl) as prev_pnl'),
            DB::raw('SUM(prev_roi) as prev_roi'),
            DB::raw('SUM(current_price_mo) as current_price_mo'),
            DB::raw('SUM(current_30_arpu) as current_30_arpu'),
            DB::raw('SUM(estimated_price_mo) as estimated_price_mo'),
            DB::raw('SUM(estimated_30_arpu) as estimated_30_arpu'),
            DB::raw('SUM(estimated_roi) as estimated_roi'),
            DB::raw('SUM(last_price_mo) as last_price_mo'),
            DB::raw('SUM(last_30_arpu) as last_30_arpu'),
            DB::raw('SUM(prev_price_mo) as prev_price_mo'),
            DB::raw('SUM(prev_30_arpu) as prev_30_arpu'))
            ->groupBy('country_id')
            ->groupBy('updated_at');
    }

    public function scopeSumOfCompanyDashboardData($query)
    {
        return $query->select('company_id','operator_id','updated_at',
            DB::raw('SUM(current_revenue) as current_revenue'),
            DB::raw('SUM(current_revenue_usd) as current_revenue_usd'),
            DB::raw('SUM(current_gross_revenue) as current_gross_revenue'),
            DB::raw('SUM(current_gross_revenue_usd) as current_gross_revenue_usd'),
            DB::raw('SUM(current_mo) as current_mo'),
            DB::raw('SUM(current_total_mo) as current_total_mo'),
            DB::raw('SUM(current_cost) as current_cost'),
            DB::raw('SUM(current_reg_sub) as current_reg_sub'),
            DB::raw('SUM(current_usd_rev_share) as current_usd_rev_share'),
            DB::raw('SUM(current_pnl) as current_pnl'),
            DB::raw('SUM(current_roi) as current_roi'),
            DB::raw('SUM(last_revenue) as last_revenue'),
            DB::raw('SUM(last_revenue_usd) as last_revenue_usd'),
            DB::raw('SUM(last_gross_revenue) as last_gross_revenue'),
            DB::raw('SUM(last_gross_revenue_usd) as last_gross_revenue_usd'),
            DB::raw('SUM(estimated_total_mo) as estimated_total_mo'),
            DB::raw('SUM(last_mo) as last_mo'),
            DB::raw('SUM(last_total_mo) as last_total_mo'),
            DB::raw('SUM(last_cost) as last_cost'),
            DB::raw('SUM(last_usd_rev_share) as last_usd_rev_share'),
            DB::raw('SUM(last_reg_sub) as last_reg_sub'),
            DB::raw('SUM(last_pnl) as last_pnl'),
            DB::raw('SUM(last_roi) as last_roi'),
            DB::raw('SUM(prev_revenue) as prev_revenue'),
            DB::raw('SUM(prev_revenue_usd) as prev_revenue_usd'),
            DB::raw('SUM(prev_gross_revenue) as prev_gross_revenue'),
            DB::raw('SUM(prev_gross_revenue_usd) as prev_gross_revenue_usd'),
            DB::raw('SUM(prev_mo) as prev_mo'),
            DB::raw('SUM(prev_total_mo) as prev_total_mo'),
            DB::raw('SUM(prev_cost) as prev_cost'),
            DB::raw('SUM(previous_usd_rev_share) as previous_usd_rev_share'),
            DB::raw('SUM(previous_reg_sub) as previous_reg_sub'),
            DB::raw('SUM(prev_pnl) as prev_pnl'),
            DB::raw('SUM(prev_roi) as prev_roi'),
            DB::raw('SUM(current_price_mo) as current_price_mo'),
            DB::raw('SUM(current_30_arpu) as current_30_arpu'),
            DB::raw('SUM(estimated_price_mo) as estimated_price_mo'),
            DB::raw('SUM(estimated_30_arpu) as estimated_30_arpu'),
            DB::raw('SUM(estimated_roi) as estimated_roi'),
            DB::raw('SUM(last_price_mo) as last_price_mo'),
            DB::raw('SUM(last_30_arpu) as last_30_arpu'),
            DB::raw('SUM(prev_price_mo) as prev_price_mo'),
            DB::raw('SUM(prev_30_arpu) as prev_30_arpu'))
            ->groupBy('company_id')
            ->groupBy('operator_id')
            ->groupBy('updated_at');
    }
}
