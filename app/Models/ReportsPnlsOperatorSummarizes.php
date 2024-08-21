<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ReportsPnlsOperatorSummarizes extends Model
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

    public function scopefilterNotOperator($query, $operator_ids)
    {
        return $query->whereNotIn('id_operator', $operator_ids);
    }

    public function scopeOperatorNotNull($query)
    {
        return $query->where('id_operator', '!=', 0);
    }

    public  function scopeSumOfAllPnlData($query)
    {
        return $query->select('id_operator',
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
        ->groupBy('id_operator');
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

    public  function scopeSumOfRoiDataCounty($query)
    {
        return $query->select('country_id',
        DB::raw('SUM(rev_usd) as rev_usd'),
        DB::raw('SUM(rev) as rev'),
        DB::raw('SUM(share) as share'),
        DB::raw('SUM(lshare) as lshare'),
        DB::raw('SUM(mo) as mo'),
        DB::raw('SUM(cost_campaign) as cost_campaign'),
        DB::raw('SUM(reg) as reg'))
        ->groupBy('country_id');
    }

    public  function scopeSumOfRoiDataOperator($query)
    {
        return $query->select('id_operator',
        DB::raw('SUM(rev_usd) as rev_usd'),
        DB::raw('SUM(rev) as rev'),
        DB::raw('SUM(share) as share'),
        DB::raw('SUM(lshare) as lshare'),
        DB::raw('SUM(mo) as mo'),
        DB::raw('SUM(cost_campaign) as cost_campaign'),
        DB::raw('SUM(reg) as reg'))
        ->groupBy('id_operator');
    }

    public  function scopeSumOfRoiDataCompany($query)
    {
        return $query->select(
        DB::raw('SUM(rev_usd) as rev_usd'),
        DB::raw('SUM(rev) as rev'),
        DB::raw('SUM(share) as share'),
        DB::raw('SUM(lshare) as lshare'),
        DB::raw('SUM(mo) as mo'),
        DB::raw('SUM(cost_campaign) as cost_campaign'),
        DB::raw('SUM(reg) as reg'));
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

    public function scopeNotDateInclude($query,$date)
    {
        return $query->whereNotIn('date', $date);
    }

    public  function scopeSelectCustomSum($query)
    {
        return $query->select('id_operator',
        DB::raw('SUM(mo_received) as mo_received'),
        DB::raw('SUM(mo_postback) as mo_postback'),
        DB::raw('SUM(cr_mo_received) as cr_mo_received'),
        DB::raw('SUM(cr_mo_postback) as cr_mo_postback'),
        DB::raw('SUM(saaf) as saaf'),
        DB::raw('SUM(sbaf) as sbaf'),
        DB::raw('SUM(cost_campaign) as cost_campaign'),
        DB::raw('SUM(clicks) as clicks'),
        DB::raw('SUM(ratio_for_cpa) as ratio_for_cpa'),
        DB::raw('SUM(cpa_price) as cpa_price'),
        DB::raw('SUM(cr_mo_clicks) as cr_mo_clicks'),
        DB::raw('SUM(cr_mo_landing) as cr_mo_landing'),
        DB::raw('SUM(mo) as mo'),
        DB::raw('SUM(landing) as landing'),
        DB::raw('SUM(reg) as reg'),
        DB::raw('SUM(unreg) as unreg'),
        DB::raw('SUM(price_mo) as price_mo'),
        DB::raw('SUM(active_subs) as active_subs'),
        DB::raw('SUM(rev_usd) as rev_usd'),
        DB::raw('SUM(rev) as rev'),
        DB::raw('SUM(share) as share'),
        DB::raw('SUM(lshare) as lshare'),
        DB::raw('SUM(other_cost) as other_cost'),
        DB::raw('SUM(hosting_cost) as hosting_cost'),
        DB::raw('SUM(content) as content'),
        DB::raw('SUM(rnd) as rnd'),
        DB::raw('SUM(bd) as bd'),
        DB::raw('SUM(platform) as platform'),
        DB::raw('SUM(pnl) as pnl'))
        ->groupBy('id_operator');
    }

    public  function scopeSelectAllAttributeSum($query)
    {
        return $query->select('id_operator',
        DB::raw('SUM(mo_received) as mo_received'),
        DB::raw('SUM(mo_postback) as mo_postback'),
        DB::raw('SUM(cr_mo_received) as cr_mo_received'),
        DB::raw('SUM(cr_mo_postback) as cr_mo_postback'),
        DB::raw('SUM(saaf) as saaf'),
        DB::raw('SUM(sbaf) as sbaf'),
        DB::raw('SUM(cost_campaign) as cost_campaign'),
        DB::raw('SUM(clicks) as clicks'),
        DB::raw('SUM(ratio_for_cpa) as ratio_for_cpa'),
        DB::raw('SUM(cpa_price) as cpa_price'),
        DB::raw('SUM(cr_mo_clicks) as cr_mo_clicks'),
        DB::raw('SUM(cr_mo_landing) as cr_mo_landing'),
        DB::raw('SUM(mo) as mo'),
        DB::raw('SUM(landing) as landing'),
        DB::raw('SUM(reg) as reg'),
        DB::raw('SUM(unreg) as unreg'),
        DB::raw('SUM(price_mo) as price_mo'),
        DB::raw('SUM(rev_usd) as rev_usd'),
        DB::raw('SUM(rev) as rev'),
        DB::raw('SUM(share) as share'),
        DB::raw('SUM(lshare) as lshare'),
        DB::raw('SUM(other_cost) as other_cost'),
        DB::raw('SUM(hosting_cost) as hosting_cost'),
        DB::raw('SUM(content) as content'),
        DB::raw('SUM(rnd) as rnd'),
        DB::raw('SUM(bd) as bd'),
        DB::raw('SUM(platform) as platform'),
        DB::raw('SUM(pnl) as pnl'),
        DB::raw('SUM(br_success) as br_success'),
        DB::raw('SUM(br_failed) as br_failed'),
        DB::raw('SUM(fp) as fp'),
        DB::raw('SUM(fp_success) as fp_success'),
        DB::raw('SUM(fp_failed) as fp_failed'),
        DB::raw('SUM(dp) as dp'),
        DB::raw('SUM(dp_success) as dp_success'),
        DB::raw('SUM(dp_failed) as dp_failed'),
        DB::raw('SUM(other_tax) as other_tax'),
        DB::raw('SUM(misc_tax) as misc_tax'),
        DB::raw('SUM(excise_tax) as excise_tax'),
        DB::raw('SUM(vat) as vat'),
        DB::raw('SUM(end_user_revenue_after_tax) as end_user_revenue_after_tax'),
        DB::raw('SUM(wht) as wht'),
        DB::raw('SUM(rev_after_makro_share) as rev_after_makro_share'),
        DB::raw('SUM(discremancy_project) as discremancy_project'),
        DB::raw('SUM(arpu_7) as arpu_7'),
        DB::raw('SUM(arpu_30) as arpu_30'),
        DB::raw('SUM(net_revenue) as net_revenue'),
        DB::raw('SUM(tax_operator) as tax_operator'),
        DB::raw('SUM(bearer_cost) as bearer_cost'),
        DB::raw('SUM(shortcode_fee) as shortcode_fee'),
        DB::raw('SUM(waki_messaging) as waki_messaging'),
        DB::raw('SUM(net_revenue_after_tax) as net_revenue_after_tax'),
        DB::raw('SUM(end_user_rev_local_include_tax) as end_user_rev_local_include_tax'),
        DB::raw('SUM(end_user_rev_usd_include_tax) as end_user_rev_usd_include_tax'),
        DB::raw('SUM(gross_usd_rev_after_tax) as gross_usd_rev_after_tax'),
        DB::raw('SUM(spec_tax) as spec_tax'),
        DB::raw('SUM(net_after_tax) as net_after_tax'),
        DB::raw('SUM(government_cost) as government_cost'),
        DB::raw('SUM(dealer_commision) as dealer_commision'),
        DB::raw('SUM(uso) as uso'),
        DB::raw('SUM(verto) as verto'),
        DB::raw('SUM(agre_paxxa) as agre_paxxa'),
        DB::raw('SUM(net_income_after_vat) as net_income_after_vat'),
        DB::raw('SUM(gross_revenue_share_linkit) as gross_revenue_share_linkit'),
        DB::raw('SUM(gross_revenue_share_paxxa) as gross_revenue_share_paxxa'),
        DB::raw('SUM(pnl) as pnl'))
        ->groupBy('id_operator');
    }

    public  function scopeSelectMonthSum($query)
    {
        return $query->select('id_operator',
        DB::raw('SUM(rev) as rev'),
        DB::raw('SUM(lshare) as lshare'),
        DB::raw('SUM(reg) as reg'),
        DB::raw('SUM(saaf) as saaf'),
        DB::raw('SUM(mo_received) as mo_received'),
        DB::raw('SUM(hosting_cost) as hosting_cost'),
        DB::raw('SUM(content) as content'),
        DB::raw('SUM(rnd) as rnd'),
        DB::raw('SUM(pnl) as pnl'))
        ->groupBy('id_operator');
    }

    public  function scopePNLReportDataSumByDays($query,$day)
    {
        return $query->select('id_operator as operator_id', 
        DB::raw('SUM(pnl)/'.$day.' as pnl'),
        DB::raw('SUM(share)/'.$day.' as share'),
        DB::raw('SUM(lshare)/'.$day.' as lshare'),
        DB::raw('SUM(cost_campaign)/'.$day.' as cost_campaign'),
        DB::raw('SUM(active_subs)/'.$day.' as active_subs'),
        DB::raw('SUM(mo)/'.$day.' as mo'))
        ->groupBy('id_operator');
    }

    public  function scopeUnmatchOperator($query)
    {
        return $query
        ->where('type','!=', 1)
        ->distinct()
        ->count('operator');
    }

    public  function scopeType($query)
    {
        return $query->where('type', '!=', 1)
        ->distinct('operator')
        ->get(['operator','id_operator']);
    }

    public  function scopeTotalOperator($query)
    {
        return $query->select('id_operator', 
        DB::raw('SUM(cost_campaign)  as cost_campaign'),
        DB::raw('SUM(active_subs) as active_subs'),
        DB::raw('SUM(mo) as mo'))
        ->groupBy('id_operator');
    }

    public  function scopeTotalCompany($query)
    {
        return $query->select( 
        DB::raw('SUM(cost_campaign)  as cost_campaign'),
        DB::raw('SUM(active_subs) as active_subs'),
        DB::raw('SUM(mo) as mo'));
    }
}
