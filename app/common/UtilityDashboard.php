<?php

namespace App\common;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Promise;
use GuzzleHttp\Psr7;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Exception\ClientException;
use Carbon\Carbon;
use function PHPSTORM_META\type;
use App\Models\Country;
use App\Models\ServiceHistory;
use Illuminate\Support\Arr;
use App\common\UtilityPercentage;

class UtilityDashboard
{
    public static function reArrangeDashboardData($sumemry)
    {
        $DataArray = array();

        $Sum_current_avg_revenue_usd = 0.0;
        $Sum_current_mo = 0.0;
        $Sum_current_total_mo = 0.0;
        $Sum_current_cost = 0.0;
        $Sum_current_avg_mo = 0.0;
        $Sum_current_pnl = 0.0;
        $Sum_current_avg_pnl = 0.0;
        $Sum_current_revenue = 0.0;
        $Sum_current_revenue_usd = 0.0;
        $Sum_current_gross_revenue = 0.0;
        $Sum_current_gross_revenue_usd = 0.0;
        $Sum_current_avg_gross_revenue_usd = 0.0;
        $Sum_current_avg_net_revenue_usd = 0.0;
        $Sum_current_avg_revenue = 0.0;
        $Sum_current_avg_gross_revenue = 0.0;
        $Sum_current_net_revenue = 0.0;
        $Sum_current_net_revenue_usd = 0.0;
        $Sum_estimated_revenue = 0.0;
        $Sum_estimated_revenue_usd = 0.0;
        $Sum_estimated_avg_revenue_usd = 0.0;
        $Sum_estimated_gross_revenue = 0.0;
        $Sum_estimated_gross_revenue_usd = 0.0;
        $Sum_estimated_net_revenue_usd = 0.0;
        $Sum_estimated_avg_gross_revenue_usd = 0.0;
        $Sum_estimated_avg_net_revenue_usd = 0.0;
        $Sum_estimated_mo = 0.0;
        $Sum_estimated_total_mo = 0.0;
        $Sum_estimated_avg_mo = 0.0;
        $Sum_estimated_cost = 0.0;
        $Sum_estimated_pnl = 0.0;
        $Sum_estimated_avg_pnl = 0.0;
        $Sum_last_avg_revenue_usd = 0.0;
        $Sum_last_mo = 0.0;
        $Sum_last_total_mo = 0.0;
        $Sum_last_cost = 0.0;
        $Sum_last_avg_mo = 0.0;
        $Sum_last_revenue = 0.0;
        $Sum_last_revenue_usd = 0.0;
        $Sum_last_gross_revenue = 0.0;
        $Sum_last_gross_revenue_usd = 0.0;
        $Sum_last_net_revenue_usd = 0.0;
        $Sum_last_avg_gross_revenue_usd = 0.0;
        $Sum_last_avg_net_revenue_usd = 0.0;
        $Sum_last_pnl = 0.0;
        $Sum_last_avg_pnl = 0.0;
        $Sum_prev_avg_revenue_usd = 0.0;
        $Sum_prev_mo = 0.0;
        $Sum_prev_total_mo = 0.0;
        $Sum_prev_cost = 0.0;
        $Sum_prev_avg_mo = 0.0;
        $Sum_prev_pnl = 0.0;
        $Sum_prev_avg_pnl = 0.0;
        $Sum_prev_revenue = 0.0;
        $Sum_prev_revenue_usd = 0.0;
        $Sum_prev_gross_revenue = 0.0;
        $Sum_prev_gross_revenue_usd = 0.0;
        $Sum_prev_net_revenue_usd = 0.0;
        $Sum_prev_avg_gross_revenue_usd = 0.0;
        $Sum_prev_avg_net_revenue_usd = 0.0;
        $Sum_current_reg_sub = 0.0;
        $Sum_current_usd_rev_share = 0.0;
        $Sum_cost_campaign = 0.0;
        $Sum_current_roi_mo = 0.0;
        $Sum_total = 0.0;
        $Sum_currentMonthROI = 0.0;
        $Sum_estimatedMonthROI = 0.0;
        $Sum_last_reg_sub = 0.0;
        $Sum_last_usd_rev_share = 0.0;
        $Sum_lastMonthROI = 0.0;
        $Sum_previous_reg_sub = 0.0;
        $Sum_previous_usd_rev_share = 0.0;
        $Sum_previousMonthROI = 0.0;
        $Sum_current_price_mo = 0.0;
        $Sum_estimated_price_mo = 0.0;
        $Sum_last_price_mo = 0.0;
        $Sum_prev_price_mo = 0.0;
        $Sum_current_30_arpu = 0.0;
        $Sum_estimated_30_arpu = 0.0;
        $Sum_last_30_arpu = 0.0;
        $Sum_prev_30_arpu = 0.0;
        $Sum_total = 0.0;
        $last_update_show = '';

        $curr_num_days = date('d', strtotime('-1 day'));
        $curr_tot_days = date('t');
        $num_days_remaining = $curr_tot_days - date('d') + 1;
        $prev_num_days = date('t', strtotime('-2 months'));
        $last_num_days = date('t', strtotime('- 1 month'));

        foreach($sumemry as $rec)
        {
            if(!isset($rec['reports']))
            continue;

            $cvalue = $rec['reports'];
            $operator = $rec['operator'];
            $country = $rec['country'];

            $pnl_details = isset($rec['reports']['pnl_details']) ? $rec['reports']['pnl_details'] : [];
            $total = isset($rec['reports']['total']) ? $rec['reports']['total'] : [];

            /* current */
            $current_avg_revenue_usd = $cvalue['current_revenue_usd']/$curr_num_days;
            $Sum_current_avg_revenue_usd += $current_avg_revenue_usd;

            $current_mo = $cvalue['current_mo'];
            $Sum_current_mo += $current_mo;

            $current_total_mo = $cvalue['current_total_mo'];
            $Sum_current_total_mo += $current_total_mo;

            $current_cost = $cvalue['current_cost'];
            $Sum_current_cost += $current_cost;

            $current_avg_mo = $cvalue['current_total_mo']/$curr_num_days;
            $Sum_current_avg_mo += $current_avg_mo;

            $current_revenue = $cvalue['current_revenue'];
            $Sum_current_revenue += $current_revenue;

            $current_revenue_usd = $cvalue['current_revenue_usd'];
            $Sum_current_revenue_usd += $current_revenue_usd;

            $current_avg_revenue = $cvalue['current_revenue']/$curr_num_days;
            $Sum_current_avg_revenue += $current_avg_revenue;

            $current_gross_revenue = $cvalue['current_gross_revenue'];

            $current_gross_revenue_usd = $cvalue['current_usd_rev_share'];

            $current_pnl = $cvalue['current_pnl'];

            $vat = !empty($operator->vat) ? $current_gross_revenue_usd * ($operator->vat/100) : 0;
            $wht = !empty($operator->wht) ? $current_gross_revenue_usd * ($operator->wht/100) : 0;
            $miscTax = !empty($operator->miscTax) ? $current_gross_revenue_usd * ($operator->miscTax/100) : 0;

            $other_tax = $vat + $wht + $miscTax;

            $current_net_revenue_usd = 0.0;
            $current_avg_net_revenue_usd = 0.0;
            $current_net_revenue = 0.0;

            if($other_tax != 0){

                // $current_gross_revenue_usd = $current_gross_revenue_usd - $other_tax;
                // $current_gross_revenue = $current_gross_revenue_usd / $country->usd;
                // $current_pnl = $current_pnl - $other_tax;
                $current_net_revenue_usd = $current_gross_revenue_usd - $other_tax;
                $current_net_revenue = $current_gross_revenue_usd / $country->usd;
                $current_pnl = $current_pnl - $other_tax;
            }
            else{
                $current_net_revenue_usd = $current_gross_revenue_usd;
            }

            $Sum_current_pnl += $current_pnl;

            $current_avg_pnl = $current_pnl/$curr_num_days;
            $Sum_current_avg_pnl += $current_avg_pnl;

            $Sum_current_gross_revenue += $current_gross_revenue;
            $Sum_current_gross_revenue_usd += $current_gross_revenue_usd;

            $Sum_current_net_revenue += $current_net_revenue;
            $Sum_current_net_revenue_usd += $current_net_revenue_usd;

            $current_avg_net_revenue_usd = $current_net_revenue_usd/$curr_num_days;
            $Sum_current_avg_net_revenue_usd += $current_avg_net_revenue_usd;

            $current_avg_gross_revenue_usd = $current_gross_revenue_usd/$curr_num_days;
            $Sum_current_avg_gross_revenue_usd += $current_avg_gross_revenue_usd;

            // $current_avg_net_revenue_usd = $current_net_revenue_usd/$curr_num_days;
            // $Sum_current_avg_net_revenue_usd += $current_avg_net_revenue_usd;

            $current_avg_gross_revenue = $current_gross_revenue/$curr_num_days;
            $Sum_current_avg_gross_revenue += $current_avg_gross_revenue;

            $Sum_current_reg_sub += 0;
            $Sum_current_usd_rev_share += 0;
            $Sum_current_roi_mo += 0;
            $Sum_currentMonthROI += 0;

            $Sum_last_price_mo = ($Sum_last_mo == 0) ? 0 : $Sum_last_cost / $Sum_last_mo;
            $Sum_prev_price_mo = ($Sum_prev_mo == 0) ? 0 : $Sum_prev_cost / $Sum_prev_mo;

            $current_price_mo = $cvalue['current_price_mo'];

            $current_30_arpu = $cvalue['current_30_arpu'];

            $Sum_total += isset($total['active_subs']) ? $total['active_subs'] : 0;

            if(isset($pnl_details['reg'])){
                $current_30_arpu = ($pnl_details['reg'] == 0) ? 0 : ($pnl_details['share'] / ($pnl_details['reg']+$total['active_subs']));
            }else{
                $current_30_arpu = 0;
            }

            if(isset($total['mo'])){
                $current_price_mo = ($total['mo'] == 0) ? 0 : ($total['cost_campaign'] / $total['mo']);
            }else{
                $current_price_mo = 0;
            }

            $current_roi = ($current_30_arpu == 0) ? 0 : ($current_price_mo / $current_30_arpu);

            $Sum_current_price_mo += $current_price_mo;
            $Sum_current_30_arpu += $current_30_arpu;

            $current_reg_sub = isset($pnl_details['reg']) ? $pnl_details['reg'] : 0;
            $Sum_current_reg_sub += $current_reg_sub;

            $current_usd_rev_share = isset($pnl_details['share']) ? $pnl_details['share'] :0;
            $Sum_current_usd_rev_share += $current_usd_rev_share;

            $current_roi_mo = isset($total['mo']) ? $total['mo'] : 0;
            $Sum_current_roi_mo += $current_roi_mo;

            $currentMonthROI = $current_roi;
            $Sum_currentMonthROI += $currentMonthROI;

            $cost_campaign = isset($total['cost_campaign']) ? $total['cost_campaign'] : 0;
            $Sum_cost_campaign += $cost_campaign;


            /* Estimate */
            $estimated_revenue = $cvalue['current_revenue'] + $current_avg_revenue*$num_days_remaining;
            $Sum_estimated_revenue += $estimated_revenue;

            $estimated_revenue_usd = $cvalue['current_revenue_usd'] + $current_avg_revenue_usd*$num_days_remaining;
            $Sum_estimated_revenue_usd += $estimated_revenue_usd;

            $estimated_gross_revenue = $current_gross_revenue + $current_avg_gross_revenue*$num_days_remaining;
            $Sum_estimated_gross_revenue += $estimated_gross_revenue;

            $estimated_gross_revenue_usd = $current_gross_revenue_usd + $current_avg_gross_revenue_usd*$num_days_remaining;
            $Sum_estimated_gross_revenue_usd += $estimated_gross_revenue_usd;

            $estimated_net_revenue_usd = $current_net_revenue_usd + $current_avg_net_revenue_usd*$num_days_remaining;
            $Sum_estimated_net_revenue_usd += $estimated_net_revenue_usd;

            $estimated_mo = $cvalue['current_mo']+($cvalue['current_mo']/$curr_num_days)*$num_days_remaining;
            $Sum_estimated_mo += $estimated_mo;

            $estimated_total_mo = $cvalue['current_total_mo']+$current_avg_mo*$num_days_remaining;
            $Sum_estimated_total_mo += $estimated_total_mo;

            $estimated_avg_mo = $estimated_total_mo/$curr_tot_days;
            $Sum_estimated_avg_mo += $estimated_avg_mo;

            $estimated_pnl = $current_pnl+$current_avg_pnl*$num_days_remaining;
            $Sum_estimated_pnl += $estimated_pnl;

            $estimated_avg_pnl = $estimated_pnl/$curr_tot_days;
            $Sum_estimated_avg_pnl += $estimated_avg_pnl;

            $current_avg_cost = $current_cost/$curr_num_days;
            $estimated_cost = $current_cost + $current_avg_cost*$num_days_remaining;
            $Sum_estimated_cost += $estimated_cost;

            $estimated_avg_revenue_usd = $estimated_revenue_usd/$curr_tot_days;
            $Sum_estimated_avg_revenue_usd += $estimated_avg_revenue_usd;

            $estimated_avg_gross_revenue_usd = $estimated_gross_revenue_usd/$curr_tot_days;
            $Sum_estimated_avg_gross_revenue_usd += $estimated_avg_gross_revenue_usd;

            $estimated_avg_net_revenue_usd = $estimated_net_revenue_usd/$curr_tot_days;
            $Sum_estimated_avg_net_revenue_usd += $estimated_avg_net_revenue_usd;

            $current_avg_roi = $current_roi/$curr_num_days;

            $estimated_roi = $current_roi + $current_roi/$num_days_remaining;

            $estimatedMonthROI = $estimated_roi;
            $Sum_estimatedMonthROI += $estimatedMonthROI;

            $estimated_price_mo = $current_price_mo;
            $Sum_estimated_price_mo += $estimated_price_mo;

            $estimated_30_arpu = $current_30_arpu;
            $Sum_estimated_30_arpu += $estimated_30_arpu;

            /* Last */
            $last_avg_revenue_usd = $cvalue['last_revenue_usd']/$last_num_days;
            $Sum_last_avg_revenue_usd += $last_avg_revenue_usd;

            $last_mo = $cvalue['last_mo'];
            $Sum_last_mo += $last_mo;

            $last_total_mo = $cvalue['last_total_mo'];
            $Sum_last_total_mo += $last_total_mo;

            $last_avg_mo = $cvalue['last_total_mo']/$last_num_days;
            $Sum_last_avg_mo += $last_avg_mo;

            $last_cost = $cvalue['last_cost'];
            $Sum_last_cost += $last_cost;

            $last_revenue = $cvalue['last_revenue'];
            $Sum_last_revenue += $last_revenue;

            $last_revenue_usd = $cvalue['last_revenue_usd'];
            $Sum_last_revenue_usd += $last_revenue_usd;

            $last_gross_revenue = $cvalue['last_gross_revenue'];
            $Sum_last_gross_revenue += $last_gross_revenue;

            $last_gross_revenue_usd = $cvalue['last_usd_rev_share'];

            $last_pnl = $cvalue['last_pnl'];

            $last_vat = !empty($operator->vat) ? $last_gross_revenue_usd * ($operator->vat/100) : 0;
            $last_wht = !empty($operator->wht) ? $last_gross_revenue_usd * ($operator->wht/100) : 0;
            $last_miscTax = !empty($operator->miscTax) ? $last_gross_revenue_usd * ($operator->miscTax/100) : 0;

            $last_other_tax = $last_vat + $last_wht + $last_miscTax;

            $last_net_revenue_usd = 0.0;

            if($last_other_tax != 0){
                // $last_gross_revenue_usd = $last_gross_revenue_usd - $last_other_tax;
                // $last_pnl = $last_pnl - $last_other_tax;

                $last_net_revenue_usd = $last_gross_revenue_usd - $last_other_tax;

                $last_pnl = $last_pnl - $last_other_tax;
            }
            else{
                $last_net_revenue_usd = $last_gross_revenue_usd;
            }
            $Sum_last_gross_revenue_usd += $last_gross_revenue_usd;

            $Sum_last_net_revenue_usd += $last_net_revenue_usd;


            $Sum_last_pnl += $last_pnl;

            $last_avg_pnl = $last_pnl/$last_num_days;
            $Sum_last_avg_pnl += $last_avg_pnl;

            $last_avg_gross_revenue_usd = $last_gross_revenue_usd/$last_num_days;
            $Sum_last_avg_gross_revenue_usd += $last_avg_gross_revenue_usd;

            $last_avg_net_revenue_usd = $last_net_revenue_usd/$last_num_days;
            $Sum_last_avg_net_revenue_usd += $last_avg_net_revenue_usd;

            $last_30_arpu = $cvalue['last_30_arpu'];
            $Sum_last_30_arpu += $last_30_arpu;

            $last_usd_arpu = ($cvalue['last_reg_sub'] == 0) ? 0 : ($cvalue['last_usd_rev_share'] / $cvalue['last_reg_sub']);
            $last_price_mo = ($cvalue['last_mo'] == 0) ? 0 : ($cvalue['last_cost'] / $cvalue['last_mo']);
            $last_roi = ($last_usd_arpu == 0) ? 0 : ($last_price_mo / $last_usd_arpu);

            $lastMonthROI = $last_roi;
            $Sum_lastMonthROI += $lastMonthROI;

            $last_usd_rev_share = $cvalue['last_usd_rev_share'];
            $Sum_last_usd_rev_share += $last_usd_rev_share;

            $last_reg_sub = $cvalue['last_reg_sub'];
            $Sum_last_reg_sub += $last_reg_sub;

            $last_price_MO = $cvalue['last_price_mo'];
            $Sum_last_price_mo += $last_price_MO;

            /* Prev */

            $prev_mo = $cvalue['prev_mo'];
            $Sum_prev_mo += $prev_mo;

            $prev_total_mo = $cvalue['prev_total_mo'];
            $Sum_prev_total_mo += $prev_total_mo;

            $prev_cost = $cvalue['prev_cost'];
            $Sum_prev_cost += $prev_cost;

            $prev_avg_mo = $cvalue['prev_total_mo']/$prev_num_days;
            $Sum_prev_avg_mo += $prev_avg_mo;

            $prev_revenue = $cvalue['prev_revenue'];
            $Sum_prev_revenue += $prev_revenue;

            $prev_revenue_usd = $cvalue['prev_revenue_usd'];
            $Sum_prev_revenue_usd += $prev_revenue_usd;

            $prev_avg_revenue_usd = $cvalue['prev_revenue_usd']/$prev_num_days;
            $Sum_prev_avg_revenue_usd += $prev_avg_revenue_usd;

            $prev_gross_revenue = $cvalue['prev_gross_revenue'];
            $Sum_prev_gross_revenue += $prev_gross_revenue;

            $prev_gross_revenue_usd = $cvalue['previous_usd_rev_share'];

            $prev_pnl = $cvalue['prev_pnl'];

            $prev_vat = !empty($operator->vat) ? $prev_gross_revenue_usd * ($operator->vat/100) : 0;
            $prev_wht = !empty($operator->wht) ? $prev_gross_revenue_usd * ($operator->wht/100) : 0;
            $prev_miscTax = !empty($operator->miscTax) ? $prev_gross_revenue_usd * ($operator->miscTax/100) : 0;

            $prev_other_tax = $prev_vat + $prev_wht + $prev_miscTax;

            $prev_net_revenue_usd = 0.0;
            if($prev_other_tax != 0){
                // $prev_gross_revenue_usd = $prev_gross_revenue_usd - $prev_other_tax;
                // $prev_pnl = $prev_pnl - $prev_other_tax;

                $prev_net_revenue_usd = $prev_gross_revenue_usd - $prev_other_tax;
                $prev_pnl = $prev_pnl - $prev_other_tax;
            }
            else{
                $prev_net_revenue_usd = $prev_gross_revenue_usd;
            }

            $Sum_prev_gross_revenue_usd += $prev_gross_revenue_usd;

            $Sum_prev_net_revenue_usd += $prev_net_revenue_usd;

            $Sum_prev_pnl += $prev_pnl;

            $prev_avg_pnl = $prev_pnl/$prev_num_days;
            $Sum_prev_avg_pnl += $prev_avg_pnl;

            $prev_avg_gross_revenue_usd = $prev_gross_revenue_usd/$prev_num_days;
            $Sum_prev_avg_gross_revenue_usd += $prev_avg_gross_revenue_usd;

            $prev_avg_net_revenue_usd = $prev_net_revenue_usd/$prev_num_days;
            $Sum_prev_avg_net_revenue_usd += $prev_avg_net_revenue_usd;

            $prev_30_arpu = $cvalue['prev_30_arpu'];

            $prev_price_mo = $cvalue['prev_price_mo'];

            $prev_30_arpu = ($cvalue['previous_reg_sub'] == 0) ? 0 : ($cvalue['previous_usd_rev_share'] / $cvalue['previous_reg_sub']);
            $prev_price_mo = ($cvalue['prev_mo'] == 0) ? 0 : ($cvalue['prev_cost'] / $cvalue['prev_mo']);
            $previous_roi = ($prev_30_arpu == 0) ? 0 : ($prev_price_mo / $prev_30_arpu);

            $previousMonthROI = $previous_roi;
            $Sum_previousMonthROI += $previousMonthROI;

            $Sum_prev_30_arpu += $prev_30_arpu;
            $Sum_prev_price_mo += $prev_price_mo;

            $previous_usd_rev_share = $cvalue['previous_usd_rev_share'];
            $Sum_previous_usd_rev_share += $previous_usd_rev_share;

            $previous_reg_sub = $cvalue['previous_reg_sub'];
            $Sum_previous_reg_sub += $previous_reg_sub;


            if($operator->id_operator == 115){
                $current_pnl = $current_revenue_usd * 0.06;
                $current_avg_pnl = $current_pnl/$curr_num_days;
                $estimated_pnl = $estimated_revenue_usd * 0.06;
                $estimated_avg_pnl = $current_avg_pnl;
                $prev_pnl = $prev_revenue_usd * 0.06;
                $prev_avg_pnl = $prev_pnl/$prev_num_days;
                $last_pnl = $last_revenue_usd * 0.06;
                $last_avg_mo = $last_pnl/$last_num_days;
            }

            if($operator->id_operator == 102){
                $current_pnl = $current_revenue_usd * 0.04;
                $current_avg_pnl = $current_pnl/$curr_num_days;
                $estimated_pnl = $estimated_revenue_usd * 0.04;
                $estimated_avg_pnl = $current_avg_pnl;
                $prev_pnl = $prev_revenue_usd * 0.04;
                $prev_avg_pnl = $prev_pnl/$prev_num_days;
                $last_pnl = $last_revenue_usd * 0.04;
                $last_avg_mo = $last_pnl/$last_num_days;
            }

            if($operator->id_operator == 167 || $operator->id_operator == 168  || $operator->id_operator == 170 || $operator->id_operator == 171 || $operator->id_operator == 176){
                $current_pnl = $current_revenue_usd * 0.05;
                $current_avg_pnl = $current_pnl/$curr_num_days;
                $estimated_pnl = $estimated_revenue_usd * 0.05;
                $estimated_avg_pnl = $current_avg_pnl;
                $prev_pnl = $prev_revenue_usd * 0.05;
                $prev_avg_pnl = $prev_pnl/$prev_num_days;
                $last_pnl = $last_revenue_usd * 0.05;
                $last_avg_mo = $last_pnl/$last_num_days;
            }

            $last_update = $cvalue['updated_at'];
            $last_update_timestamp = Carbon::parse($last_update);
            $last_update_timestamp->setTimezone('Asia/Jakarta');
            $last_update_show = $last_update_timestamp->format("Y-m-d H:i:s"). " Asia/Jakarta";
        }

        $DataArray['current_avg_revenue_usd'] = $Sum_current_avg_revenue_usd;
        $DataArray['current_mo'] = $Sum_current_mo;
        $DataArray['current_total_mo'] = $Sum_current_total_mo;
        $DataArray['current_cost'] = $Sum_current_cost;
        $DataArray['current_avg_mo'] = $Sum_current_avg_mo;
        $DataArray['current_pnl'] = $Sum_current_pnl;
        $DataArray['current_avg_pnl'] = $Sum_current_avg_pnl;
        $DataArray['current_revenue'] = $Sum_current_revenue;
        $DataArray['current_revenue_usd'] = $Sum_current_revenue_usd;
        $DataArray['current_gross_revenue'] = $Sum_current_gross_revenue;
        // $DataArray['current_net_revenue'] = $Sum_current_net_revenue;
        $DataArray['current_gross_revenue_usd'] = $Sum_current_gross_revenue_usd;
        $DataArray['current_net_revenue_usd'] = $Sum_current_net_revenue_usd;
        $DataArray['current_avg_gross_revenue_usd'] = $Sum_current_avg_gross_revenue_usd;
        $DataArray['current_avg_net_revenue_usd'] = $Sum_current_avg_net_revenue_usd;
        $DataArray['current_reg_sub'] = $Sum_current_reg_sub;
        $DataArray['current_usd_rev_share'] = $Sum_current_usd_rev_share;
        $DataArray['current_roi_mo'] = $Sum_current_roi_mo;
        $DataArray['currentMonthROI'] = $Sum_currentMonthROI;
        $DataArray['current_price_mo'] = $Sum_current_price_mo;
        $DataArray['current_30_arpu'] = $Sum_current_30_arpu;
        $DataArray['total'] = $Sum_total;
        $DataArray['cost_campaign'] = $Sum_cost_campaign;

        $DataArray['estimated_revenue'] = $Sum_estimated_revenue;
        $DataArray['estimated_revenue_usd'] = $Sum_estimated_revenue_usd;
        $DataArray['estimated_avg_revenue_usd'] = $Sum_estimated_avg_revenue_usd;
        $DataArray['estimated_gross_revenue'] = $Sum_estimated_gross_revenue;
        $DataArray['estimated_gross_revenue_usd'] = $Sum_estimated_gross_revenue_usd;
        $DataArray['estimated_avg_gross_revenue_usd'] = $Sum_estimated_avg_gross_revenue_usd;
        $DataArray['estimated_net_revenue_usd'] = $Sum_estimated_net_revenue_usd;
        $DataArray['estimated_avg_net_revenue_usd'] = $Sum_estimated_avg_net_revenue_usd;
        $DataArray['estimated_mo'] = $Sum_estimated_mo;
        $DataArray['estimated_total_mo'] = $Sum_estimated_total_mo;
        $DataArray['estimated_avg_mo'] = $Sum_estimated_avg_mo;
        $DataArray['estimated_cost'] = $Sum_estimated_cost;
        $DataArray['estimated_pnl'] = $Sum_estimated_pnl;
        $DataArray['estimated_avg_pnl'] = $Sum_estimated_avg_pnl;
        $DataArray['estimatedMonthROI'] = $Sum_estimatedMonthROI;
        $DataArray['estimated_price_mo'] = $Sum_estimated_price_mo;
        $DataArray['estimated_30_arpu'] = $Sum_estimated_30_arpu;

        $DataArray['last_avg_revenue_usd'] = $Sum_last_avg_revenue_usd;
        $DataArray['last_mo'] = $Sum_last_mo;
        $DataArray['last_total_mo'] = $Sum_last_total_mo;
        $DataArray['last_cost'] = $Sum_last_cost;
        $DataArray['last_avg_mo'] = $Sum_last_avg_mo;
        $DataArray['last_revenue'] = $Sum_last_revenue;
        $DataArray['last_revenue_usd'] = $Sum_last_revenue_usd;
        $DataArray['last_gross_revenue'] = $Sum_last_gross_revenue;
        $DataArray['last_gross_revenue_usd'] = $Sum_last_gross_revenue_usd;
        $DataArray['last_avg_gross_revenue_usd'] = $Sum_last_avg_gross_revenue_usd;
        $DataArray['last_net_revenue_usd'] = $Sum_last_net_revenue_usd;
        $DataArray['last_avg_net_revenue_usd'] = $Sum_last_avg_net_revenue_usd;
        $DataArray['last_pnl'] = $Sum_last_pnl;
        $DataArray['last_avg_pnl'] = $Sum_last_avg_pnl;
        $DataArray['last_reg_sub'] = $Sum_last_reg_sub;
        $DataArray['last_usd_rev_share'] = $Sum_last_usd_rev_share;
        $DataArray['lastMonthROI'] = $Sum_lastMonthROI;
        $DataArray['last_price_mo'] = $Sum_last_price_mo;
        $DataArray['last_30_arpu'] = $Sum_last_30_arpu;

        $DataArray['prev_avg_revenue_usd'] = $Sum_prev_avg_revenue_usd;
        $DataArray['prev_mo'] = $Sum_prev_mo;
        $DataArray['prev_total_mo'] = $Sum_prev_total_mo;
        $DataArray['prev_cost'] = $Sum_prev_cost;
        $DataArray['prev_avg_mo'] = $Sum_prev_avg_mo;
        $DataArray['prev_pnl'] = $Sum_prev_pnl;
        $DataArray['prev_avg_pnl'] = $Sum_prev_avg_pnl;
        $DataArray['prev_revenue'] = $Sum_prev_revenue;
        $DataArray['prev_revenue_usd'] = $Sum_prev_revenue_usd;
        $DataArray['prev_gross_revenue'] = $Sum_prev_gross_revenue;
        $DataArray['prev_gross_revenue_usd'] = $Sum_prev_gross_revenue_usd;
        $DataArray['prev_avg_gross_revenue_usd'] = $Sum_prev_avg_gross_revenue_usd;
        $DataArray['prev_net_revenue_usd'] = $Sum_prev_net_revenue_usd;
        $DataArray['prev_avg_net_revenue_usd'] = $Sum_prev_avg_net_revenue_usd;
        $DataArray['previous_reg_sub'] = $Sum_previous_reg_sub;
        $DataArray['previous_usd_rev_share'] = $Sum_previous_usd_rev_share;
        $DataArray['previousMonthROI'] = $Sum_previousMonthROI;
        $DataArray['prev_price_mo'] = $Sum_prev_price_mo;
        $DataArray['prev_30_arpu'] = $Sum_prev_30_arpu;
        $DataArray['updated_at'] = $last_update_show;

        return $DataArray;
    }

    public static function reArrangeContryDashboardData($sumemry)
    {
        $DataArray = array();

        $Sum_current_avg_revenue_usd = 0.0;
        $Sum_current_mo = 0.0;
        $Sum_current_total_mo = 0.0;
        $Sum_current_cost = 0.0;
        $Sum_current_avg_mo = 0.0;
        $Sum_current_pnl = 0.0;
        $Sum_current_avg_pnl = 0.0;
        $Sum_current_revenue = 0.0;
        $Sum_current_revenue_usd = 0.0;
        $Sum_current_gross_revenue = 0.0;
        $Sum_current_gross_revenue_usd = 0.0;
        $Sum_current_avg_gross_revenue_usd = 0.0;
        $Sum_current_net_revenue = 0.0;
        $Sum_current_net_revenue_usd = 0.0;
        $Sum_current_avg_net_revenue_usd = 0.0;
        $Sum_current_avg_revenue = 0.0;
        $Sum_current_avg_gross_revenue = 0.0;
        $Sum_estimated_revenue = 0.0;
        $Sum_estimated_revenue_usd = 0.0;
        $Sum_estimated_avg_revenue_usd = 0.0;
        $Sum_estimated_gross_revenue = 0.0;
        $Sum_estimated_gross_revenue_usd = 0.0;
        $Sum_estimated_net_revenue_usd = 0.0;
        $Sum_estimated_avg_gross_revenue_usd = 0.0;
        $Sum_estimated_avg_net_revenue_usd = 0.0;
        $Sum_estimated_mo = 0.0;
        $Sum_estimated_total_mo = 0.0;
        $Sum_estimated_avg_mo = 0.0;
        $Sum_estimated_cost = 0.0;
        $Sum_estimated_pnl = 0.0;
        $Sum_estimated_avg_pnl = 0.0;
        $Sum_last_avg_revenue_usd = 0.0;
        $Sum_last_mo = 0.0;
        $Sum_last_total_mo = 0.0;
        $Sum_last_cost = 0.0;
        $Sum_last_avg_mo = 0.0;
        $Sum_last_revenue = 0.0;
        $Sum_last_revenue_usd = 0.0;
        $Sum_last_gross_revenue = 0.0;
        $Sum_last_gross_revenue_usd = 0.0;
        $Sum_last_net_revenue_usd = 0.0;
        $Sum_last_avg_gross_revenue_usd = 0.0;
        $Sum_last_avg_net_revenue_usd = 0.0;
        $Sum_last_pnl = 0.0;
        $Sum_last_avg_pnl = 0.0;
        $Sum_prev_avg_revenue_usd = 0.0;
        $Sum_prev_mo = 0.0;
        $Sum_prev_total_mo = 0.0;
        $Sum_prev_cost = 0.0;
        $Sum_prev_avg_mo = 0.0;
        $Sum_prev_pnl = 0.0;
        $Sum_prev_avg_pnl = 0.0;
        $Sum_prev_revenue = 0.0;
        $Sum_prev_revenue_usd = 0.0;
        $Sum_prev_gross_revenue = 0.0;
        $Sum_prev_gross_revenue_usd = 0.0;
        $Sum_prev_net_revenue_usd = 0.0;
        $Sum_prev_avg_gross_revenue_usd = 0.0;
        $Sum_prev_avg_net_revenue_usd = 0.0;
        $Sum_current_reg_sub = 0.0;
        $Sum_current_usd_rev_share = 0.0;
        $Sum_cost_campaign = 0.0;
        $Sum_current_roi_mo = 0.0;
        $Sum_total = 0.0;
        $Sum_currentMonthROI = 0.0;
        $Sum_estimatedMonthROI = 0.0;
        $Sum_last_reg_sub = 0.0;
        $Sum_last_usd_rev_share = 0.0;
        $Sum_lastMonthROI = 0.0;
        $Sum_previous_reg_sub = 0.0;
        $Sum_previous_usd_rev_share = 0.0;
        $Sum_previousMonthROI = 0.0;
        $Sum_current_price_mo = 0.0;
        $Sum_estimated_price_mo = 0.0;
        $Sum_last_price_mo = 0.0;
        $Sum_prev_price_mo = 0.0;
        $Sum_current_30_arpu = 0.0;
        $Sum_estimated_30_arpu = 0.0;
        $Sum_last_30_arpu = 0.0;
        $Sum_prev_30_arpu = 0.0;
        $Sum_total = 0.0;
        $last_update_show = '';

        $curr_num_days = date('d', strtotime('-1 day'));
        $curr_tot_days = date('t');
        $num_days_remaining = $curr_tot_days - date('d') + 1;
        $prev_num_days = date('t', strtotime('-2 months'));
        $last_num_days = date('t', strtotime('- 1 month'));

        foreach($sumemry as $rec)
        {
            if(!isset($rec['reports']))
            continue;

            $cvalue = $rec['reports'];

            $operator = $rec['operators'];
            $country = $rec['country'];

            $pnl_details = isset($rec['reports']['pnl_details'])?$rec['reports']['pnl_details']:[];
            $total = isset($rec['reports']['total'])?$rec['reports']['total']:[];

            /* current */
            $current_avg_revenue_usd = $cvalue['current_revenue_usd']/$curr_num_days;
            $Sum_current_avg_revenue_usd += $current_avg_revenue_usd;

            $current_mo = $cvalue['current_mo'];
            $Sum_current_mo += $current_mo;

            $current_total_mo = $cvalue['current_total_mo'];
            $Sum_current_total_mo += $current_total_mo;

            $current_cost = $cvalue['current_cost'];
            $Sum_current_cost += $current_cost;

            $current_avg_mo = $cvalue['current_total_mo']/$curr_num_days;
            $Sum_current_avg_mo += $current_avg_mo;

            $current_revenue = $cvalue['current_revenue'];
            $Sum_current_revenue += $current_revenue;

            $current_revenue_usd = $cvalue['current_revenue_usd'];
            $Sum_current_revenue_usd += $current_revenue_usd;

            $current_avg_revenue = $cvalue['current_revenue']/$curr_num_days;
            $Sum_current_avg_revenue += $current_avg_revenue;

            $current_gross_revenue = $cvalue['current_gross_revenue'];

            $current_gross_revenue_usd = $cvalue['current_usd_rev_share'];

            $current_pnl = $cvalue['current_pnl'];

            $vat = !empty($operator->vat) ? $current_gross_revenue_usd * ($operator->vat/100) : 0;
            $wht = !empty($operator->wht) ? $current_gross_revenue_usd * ($operator->wht/100) : 0;
            $miscTax = !empty($operator->miscTax) ? $current_gross_revenue_usd * ($operator->miscTax/100) : 0;

            $other_tax = $vat + $wht + $miscTax;

            $current_net_revenue_usd = 0.0;
            $current_avg_net_revenue_usd = 0.0;
            $current_net_revenue = 0.0;

            if($other_tax != 0){
                // $current_gross_revenue_usd = $current_gross_revenue_usd - $other_tax;
                // $current_gross_revenue = $current_gross_revenue_usd / $country->usd;
                // $current_pnl = $current_pnl - $other_tax;
                $current_net_revenue_usd = $current_gross_revenue_usd - $other_tax;
                $current_net_revenue = $current_gross_revenue_usd / $country->usd;
                $current_pnl = $current_pnl - $other_tax;
            }
            else{
                $current_net_revenue_usd = $current_gross_revenue_usd;
            }

            $Sum_current_gross_revenue += $current_gross_revenue;
            $Sum_current_gross_revenue_usd += $current_gross_revenue_usd;

            $current_avg_gross_revenue_usd = $current_gross_revenue_usd/$curr_num_days;
            $Sum_current_avg_gross_revenue_usd += $current_avg_gross_revenue_usd;

            $Sum_current_net_revenue += $current_net_revenue;
            $Sum_current_net_revenue_usd += $current_net_revenue_usd;

            $current_avg_net_revenue_usd = $current_net_revenue_usd/$curr_num_days;
            $Sum_current_avg_net_revenue_usd += $current_avg_net_revenue_usd;

            $current_avg_gross_revenue = $current_gross_revenue/$curr_num_days;
            $Sum_current_avg_gross_revenue += $current_avg_gross_revenue;

            $Sum_current_reg_sub += 0;
            $Sum_current_usd_rev_share += 0;
            $Sum_current_roi_mo += 0;
            $Sum_currentMonthROI += 0;

            $current_price_mo = $cvalue['current_price_mo'];
            $Sum_current_price_mo += $current_price_mo;

            $current_30_arpu = $cvalue['current_30_arpu'];
            $Sum_current_30_arpu += $current_30_arpu;

            $Sum_total += isset($total['active_subs']) ? $total['active_subs'] : 0;

            $current_reg_sub = isset($pnl_details['reg']) ? $pnl_details['reg'] : 0;
            $Sum_current_reg_sub += $current_reg_sub;

            $current_usd_rev_share = isset($pnl_details['share']) ? $pnl_details['share'] : 0;
            $Sum_current_usd_rev_share += $current_usd_rev_share;

            $current_roi_mo = isset($total['mo']) ? $total['mo'] : 0;
            $Sum_current_roi_mo += $current_roi_mo;

            $cost_campaign = isset($total['cost_campaign']) ? $total['cost_campaign'] : 0;
            $Sum_cost_campaign += $cost_campaign;


            /* Estimate */
            $estimated_revenue = $cvalue['current_revenue'] + $current_avg_revenue*$num_days_remaining;
            $Sum_estimated_revenue += $estimated_revenue;

            $estimated_revenue_usd = $cvalue['current_revenue_usd'] + $current_avg_revenue_usd*$num_days_remaining;
            $Sum_estimated_revenue_usd += $estimated_revenue_usd;

            $estimated_gross_revenue = $current_gross_revenue + $current_avg_gross_revenue*$num_days_remaining;
            $Sum_estimated_gross_revenue += $estimated_gross_revenue;

            $estimated_gross_revenue_usd = $current_gross_revenue_usd + $current_avg_gross_revenue_usd*$num_days_remaining;
            $Sum_estimated_gross_revenue_usd += $estimated_gross_revenue_usd;

            $estimated_net_revenue_usd = $current_net_revenue_usd + $current_avg_net_revenue_usd*$num_days_remaining;
            $Sum_estimated_net_revenue_usd += $estimated_net_revenue_usd;

            $estimated_mo = $cvalue['current_mo']+($cvalue['current_mo']/$curr_num_days)*$num_days_remaining;
            $Sum_estimated_mo += $estimated_mo;

            $estimated_total_mo = $cvalue['current_total_mo']+$current_avg_mo*$num_days_remaining;
            $Sum_estimated_total_mo += $estimated_total_mo;

            $estimated_avg_mo = $estimated_total_mo/$curr_tot_days;
            $Sum_estimated_avg_mo += $estimated_avg_mo;

            $current_avg_cost = $current_cost/$curr_num_days;
            $estimated_cost = $current_cost + $current_avg_cost*$num_days_remaining;
            $Sum_estimated_cost += $estimated_cost;

            $estimated_avg_revenue_usd = $estimated_revenue_usd/$curr_tot_days;
            $Sum_estimated_avg_revenue_usd += $estimated_avg_revenue_usd;

            $estimated_avg_gross_revenue_usd = $estimated_gross_revenue_usd/$curr_tot_days;
            $Sum_estimated_avg_gross_revenue_usd += $estimated_avg_gross_revenue_usd;

            $estimated_avg_net_revenue_usd = $estimated_net_revenue_usd/$curr_tot_days;
            $Sum_estimated_avg_net_revenue_usd += $estimated_avg_net_revenue_usd;

            $estimated_price_mo = $cvalue['estimated_price_mo'];
            $Sum_estimated_price_mo += $estimated_price_mo;

            $estimated_30_arpu = $cvalue['estimated_30_arpu'];
            $Sum_estimated_30_arpu += $estimated_30_arpu;

            /* Last */
            $last_avg_revenue_usd = $cvalue['last_revenue_usd']/$last_num_days;
            $Sum_last_avg_revenue_usd += $last_avg_revenue_usd;

            $last_mo = $cvalue['last_mo'];
            $Sum_last_mo += $last_mo;

            $last_total_mo = $cvalue['last_total_mo'];
            $Sum_last_total_mo += $last_total_mo;

            $last_avg_mo = $cvalue['last_total_mo']/$last_num_days;
            $Sum_last_avg_mo += $last_avg_mo;

            $last_cost = $cvalue['last_cost'];
            $Sum_last_cost += $last_cost;

            $last_revenue = $cvalue['last_revenue'];
            $Sum_last_revenue += $last_revenue;

            $last_revenue_usd = $cvalue['last_revenue_usd'];
            $Sum_last_revenue_usd += $last_revenue_usd;

            $last_gross_revenue = $cvalue['last_gross_revenue'];
            $Sum_last_gross_revenue += $last_gross_revenue;

            $last_gross_revenue_usd = $cvalue['last_usd_rev_share'];

            $last_pnl = $cvalue['last_pnl'];

            $last_vat = !empty($operator->vat) ? $last_gross_revenue_usd * ($operator->vat/100) : 0;
            $last_wht = !empty($operator->wht) ? $last_gross_revenue_usd * ($operator->wht/100) : 0;
            $last_miscTax = !empty($operator->miscTax) ? $last_gross_revenue_usd * ($operator->miscTax/100) : 0;

            $last_other_tax = $last_vat + $last_wht + $last_miscTax;

            $last_net_revenue_usd = 0.0;

            if($last_other_tax != 0){
                // $last_gross_revenue_usd = $last_gross_revenue_usd - $last_other_tax;
                // $last_pnl = $last_pnl - $last_other_tax;

                $last_net_revenue_usd = $last_gross_revenue_usd - $last_other_tax;

                $last_pnl = $last_pnl - $last_other_tax;
            }else{
                $last_net_revenue_usd = $last_gross_revenue_usd;
            }

            $Sum_last_gross_revenue_usd += $last_gross_revenue_usd;

            $Sum_last_net_revenue_usd += $last_net_revenue_usd;


            $last_avg_gross_revenue_usd = $last_gross_revenue_usd/$last_num_days;
            $Sum_last_avg_gross_revenue_usd += $last_avg_gross_revenue_usd;

            $last_avg_net_revenue_usd = $last_net_revenue_usd/$last_num_days;
            $Sum_last_avg_net_revenue_usd += $last_avg_net_revenue_usd;

            $last_30_arpu = $cvalue['last_30_arpu'];
            $Sum_last_30_arpu += $last_30_arpu;

            $last_usd_arpu = ($cvalue['last_reg_sub'] == 0) ? 0 : ($cvalue['last_usd_rev_share'] / $cvalue['last_reg_sub']);
            $last_price_mo = ($cvalue['last_mo'] == 0) ? 0 : ($cvalue['last_cost'] / $cvalue['last_mo']);
            $last_roi = ($last_usd_arpu == 0) ? 0 : ($last_price_mo / $last_usd_arpu);

            $last_usd_rev_share = $cvalue['last_usd_rev_share'];
            $Sum_last_usd_rev_share += $last_usd_rev_share;

            $last_reg_sub = $cvalue['last_reg_sub'];
            $Sum_last_reg_sub += $last_reg_sub;

            $last_price_MO = $cvalue['last_price_mo'];
            $Sum_last_price_mo += $last_price_MO;

            /* Prev */

            $prev_mo = $cvalue['prev_mo'];
            $Sum_prev_mo += $prev_mo;

            $prev_total_mo = $cvalue['prev_total_mo'];
            $Sum_prev_total_mo += $prev_total_mo;

            $prev_cost = $cvalue['prev_cost'];
            $Sum_prev_cost += $prev_cost;

            $prev_avg_mo = $cvalue['prev_total_mo']/$prev_num_days;
            $Sum_prev_avg_mo += $prev_avg_mo;

            $prev_revenue = $cvalue['prev_revenue'];
            $Sum_prev_revenue += $prev_revenue;

            $prev_revenue_usd = $cvalue['prev_revenue_usd'];
            $Sum_prev_revenue_usd += $prev_revenue_usd;

            $prev_avg_revenue_usd = $cvalue['prev_revenue_usd']/$prev_num_days;
            $Sum_prev_avg_revenue_usd += $prev_avg_revenue_usd;

            $prev_gross_revenue = $cvalue['prev_gross_revenue'];
            $Sum_prev_gross_revenue += $prev_gross_revenue;

            $prev_gross_revenue_usd = $cvalue['previous_usd_rev_share'];

            $prev_pnl = $cvalue['prev_pnl'];

            $prev_vat = !empty($operator->vat) ? $prev_gross_revenue_usd * ($operator->vat/100) : 0;
            $prev_wht = !empty($operator->wht) ? $prev_gross_revenue_usd * ($operator->wht/100) : 0;
            $prev_miscTax = !empty($operator->miscTax) ? $prev_gross_revenue_usd * ($operator->miscTax/100) : 0;

            $prev_other_tax = $prev_vat + $prev_wht + $prev_miscTax;

            $prev_net_revenue_usd = 0.0;
            if($prev_other_tax != 0){
                // $prev_gross_revenue_usd = $prev_gross_revenue_usd - $prev_other_tax;
                // $prev_pnl = $prev_pnl - $prev_other_tax;

                $prev_net_revenue_usd = $prev_gross_revenue_usd - $prev_other_tax;
                $prev_pnl = $prev_pnl - $prev_other_tax;
            }else{
                $prev_net_revenue_usd = $prev_gross_revenue_usd;
            }

            $Sum_prev_gross_revenue_usd += $prev_gross_revenue_usd;
            $Sum_prev_net_revenue_usd += $prev_net_revenue_usd;

            $prev_avg_gross_revenue_usd = $prev_gross_revenue_usd/$prev_num_days;
            $Sum_prev_avg_gross_revenue_usd += $prev_avg_gross_revenue_usd;

            $prev_avg_net_revenue_usd = $prev_net_revenue_usd/$prev_num_days;
            $Sum_prev_avg_net_revenue_usd += $prev_avg_net_revenue_usd;

            $prev_30_arpu = $cvalue['prev_30_arpu'];
            $Sum_prev_30_arpu += $prev_30_arpu;

            $prev_price_mo = $cvalue['prev_price_mo'];
            $Sum_prev_price_mo += $prev_price_mo;

            $previous_usd_arpu = ($cvalue['previous_reg_sub'] == 0) ? 0 : ($cvalue['previous_usd_rev_share'] / $cvalue['previous_reg_sub']);
            $previous_price_mo = ($cvalue['prev_mo'] == 0) ? 0 : ($cvalue['prev_cost'] / $cvalue['prev_mo']);
            $previous_roi  =  ($previous_usd_arpu == 0) ? 0 : ($previous_price_mo / $previous_usd_arpu);

            $previous_usd_rev_share = $cvalue['previous_usd_rev_share'];
            $Sum_previous_usd_rev_share += $previous_usd_rev_share;

            $previous_reg_sub = $cvalue['previous_reg_sub'];
            $Sum_previous_reg_sub += $previous_reg_sub;

            $Sum_current_pnl += $current_pnl;

            $current_avg_pnl = $current_pnl/$curr_num_days;
            $Sum_current_avg_pnl += $current_avg_pnl;

            $estimated_pnl = $current_pnl+$current_avg_pnl*$num_days_remaining;
            $Sum_estimated_pnl += $estimated_pnl;

            $estimated_avg_pnl = $estimated_pnl/$curr_tot_days;
            $Sum_estimated_avg_pnl += $estimated_avg_pnl;

            $Sum_last_pnl += $last_pnl;

            $last_avg_pnl = $last_pnl/$last_num_days;
            $Sum_last_avg_pnl += $last_avg_pnl;

            $Sum_prev_pnl += $prev_pnl;

            $prev_avg_pnl = $prev_pnl/$prev_num_days;
            $Sum_prev_avg_pnl += $prev_avg_pnl;


            if($operator->id_operator == 115){
                $current_pnl = $current_revenue_usd * 0.06;
                $current_avg_pnl = $current_pnl/$curr_num_days;
                $estimated_pnl = $estimated_revenue_usd * 0.06;
                $estimated_avg_pnl = $current_avg_pnl;
                $prev_pnl = $prev_revenue_usd * 0.06;
                $prev_avg_pnl = $prev_pnl/$prev_num_days;
                $last_pnl = $last_revenue_usd * 0.06;
                $last_avg_mo = $last_pnl/$last_num_days;
            }

            if($operator->id_operator == 102){
                $current_pnl = $current_revenue_usd * 0.04;
                $current_avg_pnl = $current_pnl/$curr_num_days;
                $estimated_pnl = $estimated_revenue_usd * 0.04;
                $estimated_avg_pnl = $current_avg_pnl;
                $prev_pnl = $prev_revenue_usd * 0.04;
                $prev_avg_pnl = $prev_pnl/$prev_num_days;
                $last_pnl = $last_revenue_usd * 0.04;
                $last_avg_mo = $last_pnl/$last_num_days;
            }

            if($operator->id_operator == 167 || $operator->id_operator == 168  || $operator->id_operator == 170 || $operator->id_operator == 171 || $operator->id_operator == 176){
                $current_pnl = $current_revenue_usd * 0.05;
                $current_avg_pnl = $current_pnl/$curr_num_days;
                $estimated_pnl = $estimated_revenue_usd * 0.05;
                $estimated_avg_pnl = $current_avg_pnl;
                $prev_pnl = $prev_revenue_usd * 0.05;
                $prev_avg_pnl = $prev_pnl/$prev_num_days;
                $last_pnl = $last_revenue_usd * 0.05;
                $last_avg_mo = $last_pnl/$last_num_days;
            }



            $last_update = $cvalue['updated_at'];
            $last_update_timestamp = Carbon::parse($last_update);
            $last_update_timestamp->setTimezone('Asia/Jakarta');
            $last_update_show = $last_update_timestamp->format("Y-m-d H:i:s"). " Asia/Jakarta";
        }

        $Sum_current_price_mo = ($Sum_current_mo == 0) ? 0 : $Sum_current_cost / $Sum_current_mo;
        $Sum_estimated_price_mo = ($Sum_estimated_mo == 0) ? 0 : $Sum_estimated_cost / $Sum_estimated_mo;
        $Sum_last_price_mo = ($Sum_last_mo == 0) ? 0 : $Sum_last_cost / $Sum_last_mo;
        $Sum_prev_price_mo = ($Sum_prev_mo == 0) ? 0 : $Sum_prev_cost / $Sum_prev_mo;

        $Sum_current_30_arpu = ($Sum_current_reg_sub == 0) ? 0 : ($Sum_current_usd_rev_share / ($Sum_current_reg_sub+$Sum_total));
        $current_price_mo = ($Sum_current_roi_mo == 0) ? 0 : ($Sum_cost_campaign / $Sum_current_roi_mo);
        $current_roi  =  ($Sum_current_30_arpu == 0) ? 0 : ($Sum_current_price_mo / $Sum_current_30_arpu);

        $currentMonthROI = $current_roi;
        $Sum_currentMonthROI = $currentMonthROI;

        $current_avg_roi = $current_roi/$curr_num_days;

        $estimated_roi = $current_roi + $current_roi/$num_days_remaining;
        $estimatedMonthROI = $estimated_roi;
        $Sum_estimatedMonthROI = $estimatedMonthROI;
        $Sum_estimated_30_arpu = $Sum_current_30_arpu;

        $Sum_last_30_arpu = ($Sum_last_reg_sub == 0) ? 0 : ($Sum_last_usd_rev_share / $Sum_last_reg_sub);
        $last_price_mo = ($Sum_last_mo == 0) ? 0 : ($Sum_last_cost / $Sum_last_mo);
        $last_roi  =  ($Sum_last_30_arpu == 0) ? 0 : ($Sum_last_price_mo / $Sum_last_30_arpu);
        $lastMonthROI = $last_roi;
        $Sum_lastMonthROI = $lastMonthROI;

        $Sum_prev_30_arpu = ($Sum_previous_reg_sub == 0) ? 0 : ($Sum_previous_usd_rev_share / $Sum_previous_reg_sub);
        $previous_price_mo = ($Sum_prev_mo == 0) ? 0 : ($Sum_prev_cost / $Sum_prev_mo);
        $previous_roi  =  ($Sum_prev_30_arpu == 0) ? 0 : ($Sum_prev_price_mo / $Sum_prev_30_arpu);
        $previousMonthROI = $previous_roi;
        $Sum_previousMonthROI = $previousMonthROI;

        $DataArray['current_avg_revenue_usd'] = $Sum_current_avg_revenue_usd;
        $DataArray['current_mo'] = $Sum_current_mo;
        $DataArray['current_total_mo'] = $Sum_current_total_mo;
        $DataArray['current_cost'] = $Sum_current_cost;
        $DataArray['current_avg_mo'] = $Sum_current_avg_mo;
        $DataArray['current_pnl'] = $Sum_current_pnl;
        $DataArray['current_avg_pnl'] = $Sum_current_avg_pnl;
        $DataArray['current_revenue'] = $Sum_current_revenue;
        $DataArray['current_revenue_usd'] = $Sum_current_revenue_usd;
        $DataArray['current_gross_revenue'] = $Sum_current_gross_revenue;
        $DataArray['current_gross_revenue_usd'] = $Sum_current_gross_revenue_usd;
        $DataArray['current_net_revenue_usd'] = $Sum_current_net_revenue_usd;
        $DataArray['current_avg_gross_revenue_usd'] = $Sum_current_avg_gross_revenue_usd;
        $DataArray['current_avg_net_revenue_usd'] = $Sum_current_avg_net_revenue_usd;
        $DataArray['current_reg_sub'] = $Sum_current_reg_sub;
        $DataArray['current_usd_rev_share'] = $Sum_current_usd_rev_share;
        $DataArray['current_roi_mo'] = $Sum_current_roi_mo;
        $DataArray['currentMonthROI'] = $Sum_currentMonthROI;
        $DataArray['current_price_mo'] = $Sum_current_price_mo;
        $DataArray['current_30_arpu'] = $Sum_current_30_arpu;
        $DataArray['total'] = $Sum_total;
        $DataArray['cost_campaign'] = $Sum_cost_campaign;

        $DataArray['estimated_revenue'] = $Sum_estimated_revenue;
        $DataArray['estimated_revenue_usd'] = $Sum_estimated_revenue_usd;
        $DataArray['estimated_avg_revenue_usd'] = $Sum_estimated_avg_revenue_usd;
        $DataArray['estimated_gross_revenue'] = $Sum_estimated_gross_revenue;
        $DataArray['estimated_gross_revenue_usd'] = $Sum_estimated_gross_revenue_usd;
        $DataArray['estimated_net_revenue_usd'] = $Sum_estimated_net_revenue_usd;
        $DataArray['estimated_avg_gross_revenue_usd'] = $Sum_estimated_avg_gross_revenue_usd;
        $DataArray['estimated_avg_net_revenue_usd'] = $Sum_estimated_avg_net_revenue_usd;
        $DataArray['estimated_mo'] = $Sum_estimated_mo;
        $DataArray['estimated_total_mo'] = $Sum_estimated_total_mo;
        $DataArray['estimated_avg_mo'] = $Sum_estimated_avg_mo;
        $DataArray['estimated_cost'] = $Sum_estimated_cost;
        $DataArray['estimated_pnl'] = $Sum_estimated_pnl;
        $DataArray['estimated_avg_pnl'] = $Sum_estimated_avg_pnl;
        $DataArray['estimatedMonthROI'] = $Sum_estimatedMonthROI;
        $DataArray['estimated_price_mo'] = $Sum_estimated_price_mo;
        $DataArray['estimated_30_arpu'] = $Sum_estimated_30_arpu;

        $DataArray['last_avg_revenue_usd'] = $Sum_last_avg_revenue_usd;
        $DataArray['last_mo'] = $Sum_last_mo;
        $DataArray['last_total_mo'] = $Sum_last_total_mo;
        $DataArray['last_cost'] = $Sum_last_cost;
        $DataArray['last_avg_mo'] = $Sum_last_avg_mo;
        $DataArray['last_revenue'] = $Sum_last_revenue;
        $DataArray['last_revenue_usd'] = $Sum_last_revenue_usd;
        $DataArray['last_gross_revenue'] = $Sum_last_gross_revenue;
        $DataArray['last_gross_revenue_usd'] = $Sum_last_gross_revenue_usd;
        $DataArray['last_avg_gross_revenue_usd'] = $Sum_last_avg_gross_revenue_usd;
        $DataArray['last_net_revenue_usd'] = $Sum_last_net_revenue_usd;
        $DataArray['last_avg_net_revenue_usd'] = $Sum_last_avg_net_revenue_usd;
        $DataArray['last_pnl'] = $Sum_last_pnl;
        $DataArray['last_avg_pnl'] = $Sum_last_avg_pnl;
        $DataArray['last_reg_sub'] = $Sum_last_reg_sub;
        $DataArray['last_usd_rev_share'] = $Sum_last_usd_rev_share;
        $DataArray['lastMonthROI'] = $Sum_lastMonthROI;
        $DataArray['last_price_mo'] = $Sum_last_price_mo;
        $DataArray['last_30_arpu'] = $Sum_last_30_arpu;

        $DataArray['prev_avg_revenue_usd'] = $Sum_prev_avg_revenue_usd;
        $DataArray['prev_mo'] = $Sum_prev_mo;
        $DataArray['prev_total_mo'] = $Sum_prev_total_mo;
        $DataArray['prev_cost'] = $Sum_prev_cost;
        $DataArray['prev_avg_mo'] = $Sum_prev_avg_mo;
        $DataArray['prev_pnl'] = $Sum_prev_pnl;
        $DataArray['prev_avg_pnl'] = $Sum_prev_avg_pnl;
        $DataArray['prev_revenue'] = $Sum_prev_revenue;
        $DataArray['prev_revenue_usd'] = $Sum_prev_revenue_usd;
        $DataArray['prev_gross_revenue'] = $Sum_prev_gross_revenue;
        $DataArray['prev_gross_revenue_usd'] = $Sum_prev_gross_revenue_usd;
        $DataArray['prev_avg_gross_revenue_usd'] = $Sum_prev_avg_gross_revenue_usd;
        $DataArray['prev_net_revenue_usd'] = $Sum_prev_net_revenue_usd;
        $DataArray['prev_avg_net_revenue_usd'] = $Sum_prev_avg_net_revenue_usd;
        $DataArray['previous_reg_sub'] = $Sum_previous_reg_sub;
        $DataArray['previous_usd_rev_share'] = $Sum_previous_usd_rev_share;
        $DataArray['previousMonthROI'] = $Sum_previousMonthROI;
        $DataArray['prev_price_mo'] = $Sum_prev_price_mo;
        $DataArray['prev_30_arpu'] = $Sum_prev_30_arpu;
        $DataArray['updated_at'] = $last_update_show;

        return $DataArray;
    }

    public static function reArrangeCompanyDashboardData($sumemry)
    {
        $DataArray = array();

        $Sum_current_avg_revenue_usd = 0.0;
        $Sum_current_mo = 0.0;
        $Sum_current_total_mo = 0.0;
        $Sum_current_cost = 0.0;
        $Sum_current_avg_mo = 0.0;
        $Sum_current_pnl = 0.0;
        $Sum_current_avg_pnl = 0.0;
        $Sum_current_revenue = 0.0;
        $Sum_current_revenue_usd = 0.0;
        $Sum_current_gross_revenue = 0.0;
        $Sum_current_gross_revenue_usd = 0.0;
        $Sum_current_avg_gross_revenue_usd = 0.0;
        $Sum_current_net_revenue = 0.0;
        $Sum_current_net_revenue_usd = 0.0;
        $Sum_current_avg_net_revenue_usd = 0.0;
        $Sum_current_avg_revenue = 0.0;
        $Sum_current_avg_gross_revenue = 0.0;
        $Sum_estimated_revenue = 0.0;
        $Sum_estimated_revenue_usd = 0.0;
        $Sum_estimated_avg_revenue_usd = 0.0;
        $Sum_estimated_gross_revenue = 0.0;
        $Sum_estimated_gross_revenue_usd = 0.0;
        $Sum_estimated_avg_gross_revenue_usd = 0.0;
        $Sum_estimated_net_revenue_usd = 0.0;
        $Sum_estimated_avg_net_revenue_usd = 0.0;
        $Sum_estimated_mo = 0.0;
        $Sum_estimated_total_mo = 0.0;
        $Sum_estimated_avg_mo = 0.0;
        $Sum_estimated_cost = 0.0;
        $Sum_estimated_pnl = 0.0;
        $Sum_estimated_avg_pnl = 0.0;
        $Sum_last_avg_revenue_usd = 0.0;
        $Sum_last_mo = 0.0;
        $Sum_last_total_mo = 0.0;
        $Sum_last_cost = 0.0;
        $Sum_last_avg_mo = 0.0;
        $Sum_last_revenue = 0.0;
        $Sum_last_revenue_usd = 0.0;
        $Sum_last_gross_revenue = 0.0;
        $Sum_last_gross_revenue_usd = 0.0;
        $Sum_last_avg_gross_revenue_usd = 0.0;
        $Sum_last_net_revenue_usd = 0.0;
        $Sum_last_avg_net_revenue_usd = 0.0;
        $Sum_last_pnl = 0.0;
        $Sum_last_avg_pnl = 0.0;
        $Sum_prev_avg_revenue_usd = 0.0;
        $Sum_prev_mo = 0.0;
        $Sum_prev_total_mo = 0.0;
        $Sum_prev_cost = 0.0;
        $Sum_prev_avg_mo = 0.0;
        $Sum_prev_pnl = 0.0;
        $Sum_prev_avg_pnl = 0.0;
        $Sum_prev_revenue = 0.0;
        $Sum_prev_revenue_usd = 0.0;
        $Sum_prev_gross_revenue = 0.0;
        $Sum_prev_gross_revenue_usd = 0.0;
        $Sum_prev_avg_gross_revenue_usd = 0.0;
        $Sum_prev_net_revenue_usd = 0.0;
        $Sum_prev_avg_net_revenue_usd = 0.0;
        $Sum_current_reg_sub = 0.0;
        $Sum_current_usd_rev_share = 0.0;
        $Sum_cost_campaign = 0.0;
        $Sum_current_roi_mo = 0.0;
        $Sum_total = 0.0;
        $Sum_currentMonthROI = 0.0;
        $Sum_estimatedMonthROI = 0.0;
        $Sum_last_reg_sub = 0.0;
        $Sum_last_usd_rev_share = 0.0;
        $Sum_lastMonthROI = 0.0;
        $Sum_previous_reg_sub = 0.0;
        $Sum_previous_usd_rev_share = 0.0;
        $Sum_previousMonthROI = 0.0;
        $Sum_current_price_mo = 0.0;
        $Sum_estimated_price_mo = 0.0;
        $Sum_last_price_mo = 0.0;
        $Sum_prev_price_mo = 0.0;
        $Sum_current_30_arpu = 0.0;
        $Sum_estimated_30_arpu = 0.0;
        $Sum_last_30_arpu = 0.0;
        $Sum_prev_30_arpu = 0.0;
        $Sum_total = 0.0;
        $last_update_show = '';

        $curr_num_days = date('d', strtotime('-1 day'));
        $curr_tot_days = date('t');
        $num_days_remaining = $curr_tot_days - date('d') + 1;
        $prev_num_days = date('t', strtotime('-2 months'));
        $last_num_days = date('t', strtotime('- 1 month'));

        foreach($sumemry as $rec)
        {
            if(!isset($rec['reports']))
            continue;

            $cvalue = $rec['reports'];
            $operator = $rec['operators'];
            // $company = $rec['company'];

            $pnl_details = isset($rec['reports']['pnl_details'])?$rec['reports']['pnl_details']:[];
            $total = isset($rec['reports']['total'])?$rec['reports']['total']:[];

            /* current */
            $current_avg_revenue_usd = $cvalue['current_revenue_usd']/$curr_num_days;
            $Sum_current_avg_revenue_usd += $current_avg_revenue_usd;

            $current_mo = $cvalue['current_mo'];
            $Sum_current_mo += $current_mo;

            $current_total_mo = $cvalue['current_total_mo'];
            $Sum_current_total_mo += $current_total_mo;

            $current_cost = $cvalue['current_cost'];
            $Sum_current_cost += $current_cost;

            $current_avg_mo = $cvalue['current_total_mo']/$curr_num_days;
            $Sum_current_avg_mo += $current_avg_mo;

            $current_revenue = $cvalue['current_revenue'];
            $Sum_current_revenue += $current_revenue;

            $current_revenue_usd = $cvalue['current_revenue_usd'];
            $Sum_current_revenue_usd += $current_revenue_usd;

            $current_avg_revenue = $cvalue['current_revenue']/$curr_num_days;
            $Sum_current_avg_revenue += $current_avg_revenue;

            $current_gross_revenue = $cvalue['current_gross_revenue'];

            $current_gross_revenue_usd = $cvalue['current_usd_rev_share'];

            $current_pnl = $cvalue['current_pnl'];

            $vat = !empty($operator->vat) ? $current_gross_revenue_usd * ($operator->vat/100) : 0;
            $wht = !empty($operator->wht) ? $current_gross_revenue_usd * ($operator->wht/100) : 0;
            $miscTax = !empty($operator->miscTax) ? $current_gross_revenue_usd * ($operator->miscTax/100) : 0;

            $other_tax = $vat + $wht + $miscTax;

            $current_net_revenue_usd = 0.0;
            $current_avg_net_revenue_usd = 0.0;
            $current_net_revenue = 0.0;
            if($other_tax != 0){
                // $current_gross_revenue_usd = $current_gross_revenue_usd - $other_tax;
                // $current_pnl = $current_pnl - $other_tax;

                $current_net_revenue_usd = $current_gross_revenue_usd - $other_tax;
                $current_pnl = $current_pnl - $other_tax;
            }    else{
                $current_net_revenue_usd = $current_gross_revenue_usd;
            }

            $Sum_current_gross_revenue += $current_gross_revenue;
            $Sum_current_gross_revenue_usd += $current_gross_revenue_usd;

            $current_avg_gross_revenue_usd = $current_gross_revenue_usd/$curr_num_days;
            $Sum_current_avg_gross_revenue_usd += $current_avg_gross_revenue_usd;

            $Sum_current_net_revenue += $current_net_revenue;
            $Sum_current_net_revenue_usd += $current_net_revenue_usd;

            $current_avg_net_revenue_usd = $current_net_revenue_usd/$curr_num_days;
            $Sum_current_avg_net_revenue_usd += $current_avg_net_revenue_usd;


            $current_avg_gross_revenue = $current_gross_revenue/$curr_num_days;
            $Sum_current_avg_gross_revenue += $current_avg_gross_revenue;

            $Sum_current_reg_sub += 0;
            $Sum_current_usd_rev_share += 0;
            $Sum_current_roi_mo += 0;
            $Sum_currentMonthROI += 0;

            $current_price_mo = $cvalue['current_price_mo'];
            $Sum_current_price_mo += $current_price_mo;

            $current_30_arpu = $cvalue['current_30_arpu'];
            $Sum_current_30_arpu += $current_30_arpu;

            $Sum_total += isset($total['active_subs']) ? $total['active_subs'] : 0;

            $current_reg_sub = isset($pnl_details['reg']) ? $pnl_details['reg'] : 0;
            $Sum_current_reg_sub += $current_reg_sub;

            $current_usd_rev_share = isset($pnl_details['share']) ? $pnl_details['share'] : 0;
            $Sum_current_usd_rev_share += $current_usd_rev_share;

            $current_roi_mo = isset($total['mo']) ? $total['mo'] : 0;
            $Sum_current_roi_mo += $current_roi_mo;

            $cost_campaign = isset($total['cost_campaign']) ? $total['cost_campaign'] : 0;
            $Sum_cost_campaign += $cost_campaign;

            /* Estimate */
            $estimated_revenue = $cvalue['current_revenue'] + $current_avg_revenue*$num_days_remaining;
            $Sum_estimated_revenue += $estimated_revenue;

            $estimated_revenue_usd = $cvalue['current_revenue_usd'] + $current_avg_revenue_usd*$num_days_remaining;
            $Sum_estimated_revenue_usd += $estimated_revenue_usd;

            $estimated_gross_revenue = $current_gross_revenue + $current_avg_gross_revenue*$num_days_remaining;
            $Sum_estimated_gross_revenue += $estimated_gross_revenue;

            $estimated_gross_revenue_usd = $current_gross_revenue_usd + $current_avg_gross_revenue_usd*$num_days_remaining;
            $Sum_estimated_gross_revenue_usd += $estimated_gross_revenue_usd;

            $estimated_net_revenue_usd = $current_net_revenue_usd + $current_avg_net_revenue_usd*$num_days_remaining;
            $Sum_estimated_net_revenue_usd += $estimated_net_revenue_usd;

            $estimated_mo = $cvalue['current_mo']+($cvalue['current_mo']/$curr_num_days)*$num_days_remaining;
            $Sum_estimated_mo += $estimated_mo;

            $estimated_total_mo = $cvalue['current_total_mo']+$current_avg_mo*$num_days_remaining;
            $Sum_estimated_total_mo += $estimated_total_mo;

            $estimated_avg_mo = $estimated_total_mo/$curr_tot_days;
            $Sum_estimated_avg_mo += $estimated_avg_mo;

            $current_avg_cost = $current_cost/$curr_num_days;
            $estimated_cost = $current_cost + $current_avg_cost*$num_days_remaining;
            $Sum_estimated_cost += $estimated_cost;

            $estimated_avg_revenue_usd = $estimated_revenue_usd/$curr_tot_days;
            $Sum_estimated_avg_revenue_usd += $estimated_avg_revenue_usd;

            $estimated_avg_gross_revenue_usd = $estimated_gross_revenue_usd/$curr_tot_days;
            $Sum_estimated_avg_gross_revenue_usd += $estimated_avg_gross_revenue_usd;

            $estimated_avg_net_revenue_usd = $estimated_net_revenue_usd/$curr_tot_days;
            $Sum_estimated_avg_net_revenue_usd += $estimated_avg_net_revenue_usd;

            $estimated_price_mo = $cvalue['estimated_price_mo'];
            $Sum_estimated_price_mo += $estimated_price_mo;

            $estimated_30_arpu = $cvalue['estimated_30_arpu'];
            $Sum_estimated_30_arpu += $estimated_30_arpu;

            /* Last */
            $last_avg_revenue_usd = $cvalue['last_revenue_usd']/$last_num_days;
            $Sum_last_avg_revenue_usd += $last_avg_revenue_usd;

            $last_mo = $cvalue['last_mo'];
            $Sum_last_mo += $last_mo;

            $last_total_mo = $cvalue['last_total_mo'];
            $Sum_last_total_mo += $last_total_mo;

            $last_avg_mo = $cvalue['last_total_mo']/$last_num_days;
            $Sum_last_avg_mo += $last_avg_mo;

            $last_cost = $cvalue['last_cost'];
            $Sum_last_cost += $last_cost;

            $last_revenue = $cvalue['last_revenue'];
            $Sum_last_revenue += $last_revenue;

            $last_revenue_usd = $cvalue['last_revenue_usd'];
            $Sum_last_revenue_usd += $last_revenue_usd;

            $last_gross_revenue = $cvalue['last_gross_revenue'];
            $Sum_last_gross_revenue += $last_gross_revenue;

            $last_gross_revenue_usd = $cvalue['last_usd_rev_share'];

            $last_pnl = $cvalue['last_pnl'];

            $last_vat = !empty($operator->vat) ? $last_gross_revenue_usd * ($operator->vat/100) : 0;
            $last_wht = !empty($operator->wht) ? $last_gross_revenue_usd * ($operator->wht/100) : 0;
            $last_miscTax = !empty($operator->miscTax) ? $last_gross_revenue_usd * ($operator->miscTax/100) : 0;

            $last_other_tax = $last_vat + $last_wht + $last_miscTax;

            $last_net_revenue_usd = 0.0;

            if($last_other_tax != 0){
                // $last_gross_revenue_usd = $last_gross_revenue_usd - $last_other_tax;
                // $last_pnl = $last_pnl - $last_other_tax;

                $last_net_revenue_usd = $last_gross_revenue_usd - $last_other_tax;

                $last_pnl = $last_pnl - $last_other_tax;
            }else{
                $last_net_revenue_usd = $last_gross_revenue_usd;
            }

            $Sum_last_gross_revenue_usd += $last_gross_revenue_usd;

            $Sum_last_net_revenue_usd += $last_net_revenue_usd;

            $last_avg_gross_revenue_usd = $last_gross_revenue_usd/$last_num_days;
            $Sum_last_avg_gross_revenue_usd += $last_avg_gross_revenue_usd;

            $last_avg_net_revenue_usd = $last_net_revenue_usd/$last_num_days;
            $Sum_last_avg_net_revenue_usd += $last_avg_net_revenue_usd;


            $last_30_arpu = $cvalue['last_30_arpu'];
            $Sum_last_30_arpu += $last_30_arpu;

            $last_usd_arpu = ($cvalue['last_reg_sub'] == 0) ? 0 : ($cvalue['last_usd_rev_share'] / $cvalue['last_reg_sub']);
            $last_price_mo = ($cvalue['last_mo'] == 0) ? 0 : ($cvalue['last_cost'] / $cvalue['last_mo']);
            $last_roi = ($last_usd_arpu == 0) ? 0 : ($last_price_mo / $last_usd_arpu);

            $last_usd_rev_share = $cvalue['last_usd_rev_share'];
            $Sum_last_usd_rev_share += $last_usd_rev_share;

            $last_reg_sub = $cvalue['last_reg_sub'];
            $Sum_last_reg_sub += $last_reg_sub;

            $last_price_MO = $cvalue['last_price_mo'];
            $Sum_last_price_mo += $last_price_MO;

            /* Prev */

            $prev_mo = $cvalue['prev_mo'];
            $Sum_prev_mo += $prev_mo;

            $prev_total_mo = $cvalue['prev_total_mo'];
            $Sum_prev_total_mo += $prev_total_mo;

            $prev_cost = $cvalue['prev_cost'];
            $Sum_prev_cost += $prev_cost;

            $prev_avg_mo = $cvalue['prev_total_mo']/$prev_num_days;
            $Sum_prev_avg_mo += $prev_avg_mo;

            $prev_revenue = $cvalue['prev_revenue'];
            $Sum_prev_revenue += $prev_revenue;

            $prev_revenue_usd = $cvalue['prev_revenue_usd'];
            $Sum_prev_revenue_usd += $prev_revenue_usd;

            $prev_avg_revenue_usd = $cvalue['prev_revenue_usd']/$prev_num_days;
            $Sum_prev_avg_revenue_usd += $prev_avg_revenue_usd;

            $prev_gross_revenue = $cvalue['prev_gross_revenue'];
            $Sum_prev_gross_revenue += $prev_gross_revenue;

            $prev_gross_revenue_usd = $cvalue['previous_usd_rev_share'];

            $prev_pnl = $cvalue['prev_pnl'];

            $prev_vat = !empty($operator->vat) ? $prev_gross_revenue_usd * ($operator->vat/100) : 0;
            $prev_wht = !empty($operator->wht) ? $prev_gross_revenue_usd * ($operator->wht/100) : 0;
            $prev_miscTax = !empty($operator->miscTax) ? $prev_gross_revenue_usd * ($operator->miscTax/100) : 0;

            $prev_other_tax = $prev_vat + $prev_wht + $prev_miscTax;

            $prev_net_revenue_usd = 0.0;
            if($prev_other_tax != 0){
                // $prev_gross_revenue_usd = $prev_gross_revenue_usd - $prev_other_tax;
                // $prev_pnl = $prev_pnl - $prev_other_tax;

                $prev_net_revenue_usd = $prev_gross_revenue_usd - $prev_other_tax;
                $prev_pnl = $prev_pnl - $prev_other_tax;
            }else{
                $prev_net_revenue_usd = $prev_gross_revenue_usd;
            }

            $Sum_prev_gross_revenue_usd += $prev_gross_revenue_usd;
            $Sum_prev_net_revenue_usd += $prev_net_revenue_usd;


            $prev_avg_gross_revenue_usd = $prev_gross_revenue_usd/$prev_num_days;
            $Sum_prev_avg_gross_revenue_usd += $prev_avg_gross_revenue_usd;

            $prev_avg_net_revenue_usd = $prev_net_revenue_usd/$prev_num_days;
            $Sum_prev_avg_net_revenue_usd += $prev_avg_net_revenue_usd;


            $prev_30_arpu = $cvalue['prev_30_arpu'];
            $Sum_prev_30_arpu += $prev_30_arpu;

            $prev_price_mo = $cvalue['prev_price_mo'];
            $Sum_prev_price_mo += $prev_price_mo;

            $previous_usd_arpu = ($cvalue['previous_reg_sub'] == 0) ? 0 : ($cvalue['previous_usd_rev_share'] / $cvalue['previous_reg_sub']);
            $previous_price_mo = ($cvalue['prev_mo'] == 0) ? 0 : ($cvalue['prev_cost'] / $cvalue['prev_mo']);
            $previous_roi  =  ($previous_usd_arpu == 0) ? 0 : ($previous_price_mo / $previous_usd_arpu);

            $previous_usd_rev_share = $cvalue['previous_usd_rev_share'];
            $Sum_previous_usd_rev_share += $previous_usd_rev_share;

            $previous_reg_sub = $cvalue['previous_reg_sub'];
            $Sum_previous_reg_sub += $previous_reg_sub;

            $Sum_current_pnl += $current_pnl;

            $current_avg_pnl = $current_pnl/$curr_num_days;
            $Sum_current_avg_pnl += $current_avg_pnl;

            $estimated_pnl = $current_pnl+$current_avg_pnl*$num_days_remaining;
            $Sum_estimated_pnl += $estimated_pnl;

            $estimated_avg_pnl = $estimated_pnl/$curr_tot_days;
            $Sum_estimated_avg_pnl += $estimated_avg_pnl;

            $Sum_last_pnl += $last_pnl;

            $last_avg_pnl = $last_pnl/$last_num_days;
            $Sum_last_avg_pnl += $last_avg_pnl;

            $Sum_prev_pnl += $prev_pnl;

            $prev_avg_pnl = $prev_pnl/$prev_num_days;
            $Sum_prev_avg_pnl += $prev_avg_pnl;

            if($operator->id_operator == 115){
                $current_pnl = $current_revenue_usd * 0.06;
                $current_avg_pnl = $current_pnl/$curr_num_days;
                $estimated_pnl = $estimated_revenue_usd * 0.06;
                $estimated_avg_pnl = $current_avg_pnl;
                $prev_pnl = $prev_revenue_usd * 0.06;
                $prev_avg_pnl = $prev_pnl/$prev_num_days;
                $last_pnl = $last_revenue_usd * 0.06;
                $last_avg_mo = $last_pnl/$last_num_days;
            }

            if($operator->id_operator == 102){
                $current_pnl = $current_revenue_usd * 0.04;
                $current_avg_pnl = $current_pnl/$curr_num_days;
                $estimated_pnl = $estimated_revenue_usd * 0.04;
                $estimated_avg_pnl = $current_avg_pnl;
                $prev_pnl = $prev_revenue_usd * 0.04;
                $prev_avg_pnl = $prev_pnl/$prev_num_days;
                $last_pnl = $last_revenue_usd * 0.04;
                $last_avg_mo = $last_pnl/$last_num_days;
            }

            if($operator->id_operator == 167 || $operator->id_operator == 168  || $operator->id_operator == 170 || $operator->id_operator == 171 || $operator->id_operator == 176){
                $current_pnl = $current_revenue_usd * 0.05;
                $current_avg_pnl = $current_pnl/$curr_num_days;
                $estimated_pnl = $estimated_revenue_usd * 0.05;
                $estimated_avg_pnl = $current_avg_pnl;
                $prev_pnl = $prev_revenue_usd * 0.05;
                $prev_avg_pnl = $prev_pnl/$prev_num_days;
                $last_pnl = $last_revenue_usd * 0.05;
                $last_avg_mo = $last_pnl/$last_num_days;
            }



            $last_update = $cvalue['updated_at'];
            $last_update_timestamp = Carbon::parse($last_update);
            $last_update_timestamp->setTimezone('Asia/Jakarta');
            $last_update_show = $last_update_timestamp->format("Y-m-d H:i:s"). " Asia/Jakarta";
        }

        $Sum_current_price_mo = ($Sum_current_mo == 0) ? 0 : $Sum_current_cost / $Sum_current_mo;
        $Sum_estimated_price_mo = ($Sum_estimated_mo == 0) ? 0 : $Sum_estimated_cost / $Sum_estimated_mo;
        $Sum_last_price_mo = ($Sum_last_mo == 0) ? 0 : $Sum_last_cost / $Sum_last_mo;
        $Sum_prev_price_mo = ($Sum_prev_mo == 0) ? 0 : $Sum_prev_cost / $Sum_prev_mo;

        $Sum_current_30_arpu = ($Sum_current_reg_sub == 0) ? 0 : ($Sum_current_usd_rev_share / ($Sum_current_reg_sub+$Sum_total));
        $current_price_mo = ($Sum_current_roi_mo == 0) ? 0 : ($Sum_cost_campaign / $Sum_current_roi_mo);
        $current_roi  =  ($Sum_current_30_arpu == 0) ? 0 : ($Sum_current_price_mo / $Sum_current_30_arpu);

        $currentMonthROI = $current_roi;
        $Sum_currentMonthROI = $currentMonthROI;

        $current_avg_roi = $current_roi/$curr_num_days;

        $estimated_roi = $current_roi + $current_roi/$num_days_remaining;
        $estimatedMonthROI = $estimated_roi;
        $Sum_estimatedMonthROI = $estimatedMonthROI;
        $Sum_estimated_30_arpu = $Sum_current_30_arpu;

        $Sum_last_30_arpu = ($Sum_last_reg_sub == 0) ? 0 : ($Sum_last_usd_rev_share / $Sum_last_reg_sub);
        $last_price_mo = ($Sum_last_mo == 0) ? 0 : ($Sum_last_cost / $Sum_last_mo);
        $last_roi  =  ($Sum_last_30_arpu == 0) ? 0 : ($Sum_last_price_mo / $Sum_last_30_arpu);
        $lastMonthROI = $last_roi;
        $Sum_lastMonthROI = $lastMonthROI;

        $Sum_prev_30_arpu = ($Sum_previous_reg_sub == 0) ? 0 : ($Sum_previous_usd_rev_share / $Sum_previous_reg_sub);
        $previous_price_mo = ($Sum_prev_mo == 0) ? 0 : ($Sum_prev_cost / $Sum_prev_mo);
        $previous_roi  =  ($Sum_prev_30_arpu == 0) ? 0 : ($Sum_prev_price_mo / $Sum_prev_30_arpu);
        $previousMonthROI = $previous_roi;
        $Sum_previousMonthROI = $previousMonthROI;

        $DataArray['current_avg_revenue_usd'] = $Sum_current_avg_revenue_usd;
        $DataArray['current_mo'] = $Sum_current_mo;
        $DataArray['current_total_mo'] = $Sum_current_total_mo;
        $DataArray['current_cost'] = $Sum_current_cost;
        $DataArray['current_avg_mo'] = $Sum_current_avg_mo;
        $DataArray['current_pnl'] = $Sum_current_pnl;
        $DataArray['current_avg_pnl'] = $Sum_current_avg_pnl;
        $DataArray['current_revenue'] = $Sum_current_revenue;
        $DataArray['current_revenue_usd'] = $Sum_current_revenue_usd;
        $DataArray['current_gross_revenue'] = $Sum_current_gross_revenue;
        $DataArray['current_gross_revenue_usd'] = $Sum_current_gross_revenue_usd;
        $DataArray['current_avg_gross_revenue_usd'] = $Sum_current_avg_gross_revenue_usd;
        $DataArray['current_net_revenue_usd'] = $Sum_current_net_revenue_usd;
        $DataArray['current_avg_net_revenue_usd'] = $Sum_current_avg_net_revenue_usd;
        $DataArray['current_reg_sub'] = $Sum_current_reg_sub;
        $DataArray['current_usd_rev_share'] = $Sum_current_usd_rev_share;
        $DataArray['current_roi_mo'] = $Sum_current_roi_mo;
        $DataArray['currentMonthROI'] = $Sum_currentMonthROI;
        $DataArray['current_price_mo'] = $Sum_current_price_mo;
        $DataArray['current_30_arpu'] = $Sum_current_30_arpu;
        $DataArray['total'] = $Sum_total;
        $DataArray['cost_campaign'] = $Sum_cost_campaign;

        $DataArray['estimated_revenue'] = $Sum_estimated_revenue;
        $DataArray['estimated_revenue_usd'] = $Sum_estimated_revenue_usd;
        $DataArray['estimated_avg_revenue_usd'] = $Sum_estimated_avg_revenue_usd;
        $DataArray['estimated_gross_revenue'] = $Sum_estimated_gross_revenue;
        $DataArray['estimated_gross_revenue_usd'] = $Sum_estimated_gross_revenue_usd;
        $DataArray['estimated_avg_gross_revenue_usd'] = $Sum_estimated_avg_gross_revenue_usd;
        $DataArray['estimated_net_revenue_usd'] = $Sum_estimated_net_revenue_usd;
        $DataArray['estimated_avg_net_revenue_usd'] = $Sum_estimated_avg_net_revenue_usd;
        $DataArray['estimated_mo'] = $Sum_estimated_mo;
        $DataArray['estimated_total_mo'] = $Sum_estimated_total_mo;
        $DataArray['estimated_avg_mo'] = $Sum_estimated_avg_mo;
        $DataArray['estimated_cost'] = $Sum_estimated_cost;
        $DataArray['estimated_pnl'] = $Sum_estimated_pnl;
        $DataArray['estimated_avg_pnl'] = $Sum_estimated_avg_pnl;
        $DataArray['estimatedMonthROI'] = $Sum_estimatedMonthROI;
        $DataArray['estimated_price_mo'] = $Sum_estimated_price_mo;
        $DataArray['estimated_30_arpu'] = $Sum_estimated_30_arpu;

        $DataArray['last_avg_revenue_usd'] = $Sum_last_avg_revenue_usd;
        $DataArray['last_mo'] = $Sum_last_mo;
        $DataArray['last_total_mo'] = $Sum_last_total_mo;
        $DataArray['last_cost'] = $Sum_last_cost;
        $DataArray['last_avg_mo'] = $Sum_last_avg_mo;
        $DataArray['last_revenue'] = $Sum_last_revenue;
        $DataArray['last_revenue_usd'] = $Sum_last_revenue_usd;
        $DataArray['last_gross_revenue'] = $Sum_last_gross_revenue;
        $DataArray['last_gross_revenue_usd'] = $Sum_last_gross_revenue_usd;
        $DataArray['last_avg_gross_revenue_usd'] = $Sum_last_avg_gross_revenue_usd;
        $DataArray['last_net_revenue_usd'] = $Sum_last_net_revenue_usd;
        $DataArray['last_avg_net_revenue_usd'] = $Sum_last_avg_net_revenue_usd;
        $DataArray['last_pnl'] = $Sum_last_pnl;
        $DataArray['last_avg_pnl'] = $Sum_last_avg_pnl;
        $DataArray['last_reg_sub'] = $Sum_last_reg_sub;
        $DataArray['last_usd_rev_share'] = $Sum_last_usd_rev_share;
        $DataArray['lastMonthROI'] = $Sum_lastMonthROI;
        $DataArray['last_price_mo'] = $Sum_last_price_mo;
        $DataArray['last_30_arpu'] = $Sum_last_30_arpu;

        $DataArray['prev_avg_revenue_usd'] = $Sum_prev_avg_revenue_usd;
        $DataArray['prev_mo'] = $Sum_prev_mo;
        $DataArray['prev_total_mo'] = $Sum_prev_total_mo;
        $DataArray['prev_cost'] = $Sum_prev_cost;
        $DataArray['prev_avg_mo'] = $Sum_prev_avg_mo;
        $DataArray['prev_pnl'] = $Sum_prev_pnl;
        $DataArray['prev_avg_pnl'] = $Sum_prev_avg_pnl;
        $DataArray['prev_revenue'] = $Sum_prev_revenue;
        $DataArray['prev_revenue_usd'] = $Sum_prev_revenue_usd;
        $DataArray['prev_gross_revenue'] = $Sum_prev_gross_revenue;
        $DataArray['prev_gross_revenue_usd'] = $Sum_prev_gross_revenue_usd;
        $DataArray['prev_avg_gross_revenue_usd'] = $Sum_prev_avg_gross_revenue_usd;
        $DataArray['prev_net_revenue_usd'] = $Sum_prev_net_revenue_usd;
        $DataArray['prev_avg_net_revenue_usd'] = $Sum_prev_avg_net_revenue_usd;
        $DataArray['previous_reg_sub'] = $Sum_previous_reg_sub;
        $DataArray['previous_usd_rev_share'] = $Sum_previous_usd_rev_share;
        $DataArray['previousMonthROI'] = $Sum_previousMonthROI;
        $DataArray['prev_price_mo'] = $Sum_prev_price_mo;
        $DataArray['prev_30_arpu'] = $Sum_prev_30_arpu;
        $DataArray['updated_at'] = $last_update_show;

        return $DataArray;
    }
}
