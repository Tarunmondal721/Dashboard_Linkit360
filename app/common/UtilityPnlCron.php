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
use Illuminate\Support\Arr;
use App\Models\ServiceHistory;

class UtilityPnlCron
{
    // USD calculate for Specific Country and Operator Like Oman ,Kuwit , CellCard ,Mobifone
    public static function UsdCalCriteria($local_rev,$exchange_rate,$data,$country,$days,$type='daily')
    {
        $countryCode = $country['country_code'];

        $Gross_local_Revenue = $local_rev;

        $id_operator = isset($data['operator_id']) ? $data['operator_id'] : $data['id_operator'];

        $usdValue = $Gross_local_Revenue * $exchange_rate;

        return $usdValue;
    }

    /* revenue calculation for mobifone operator 29 */
    public static function getMobifoneRevenue($id_operator,$days,$type)
    {
        $summarydata = [];
        $sumGrossRev = 0;

        if($type == 'daily')
        {
            $service_historys = ServiceHistory::FilterOperator($id_operator)->filterDate($days)->get();
        }
        else if($type == 'monthly')
        {
            $month = $days['date'];
            $service_historys = ServiceHistory::FilterOperator($id_operator)->filterMonth($month)->get();
        }
        
        if(isset($service_historys) && !empty($service_historys))
        {
            foreach ($service_historys as $service) 
            {
                $mt_success = $service['mt_success'];
                $service_id = $service['id_service'];
                $gros_rev = $service['gros_rev'];

                if($service_id == 466)
                {
                    $gros_rev = $mt_success * 3000;
                }
                else if($service_id == 698)
                {
                    $gros_rev = $mt_success * 5000;
                }

                $sumGrossRev += $gros_rev;
            }

            return $sumGrossRev;
        }
    }

    public static function Columnvalueinit()
    {
        $record = array();

        $record['date'] = "";
        $record['type'] = 0;
        $record['id_operator'] = 0;
        $record['country_id'] = 0;
        $record['operator'] = 0;
        $record['country_code'] = 0;
        $record['mo_received'] = 0;
        $record['mo_postback'] = 0;
        $record['cr_mo_received'] = 0;
        $record['cr_mo_postback'] = 0;
        $record['saaf'] = 0;
        $record['sbaf'] = 0;
        $record['cost_campaign'] = 0;
        $record['clicks'] = 0;
        $record['ratio_for_cpa'] = 0;
        $record['cpa_price'] = 0;
        $record['cr_mo_clicks'] = 0;
        $record['cr_mo_clicks'] = 0;
        $record['cr_mo_landing'] = 0;
        $record['mo'] = 0;
        $record['landing'] = 0;
        $record['reg'] = 0;
        $record['unreg'] = 0;
        $record['price_mo'] = 0;
        $record['active_subs'] = 0;
        $record['rev_usd'] = 0;
        $record['rev'] = 0;
        $record['share'] = 0;
        $record['lshare'] = 0;
        $record['br'] = 0;
        $record['br_success'] = 0;
        $record['br_failed'] = 0;
        $record['fp'] = 0;
        $record['rnd'] = 0;
        $record['fp_success'] = 0;
        $record['fp_failed'] = 0;
        $record['dp'] = 0;
        $record['dp_success'] = 0;
        $record['dp_failed'] = 0;
        $record['other_cost'] = 0;
        $record['other_tax'] = 0;
        $record['misc_tax'] = 0;
        $record['hosting_cost'] = 0;
        $record['content'] = 0;
        $record['bd'] = 0;
        $record['platform'] = 0;
        $record['excise_tax'] = 0;
        $record['vat'] = 0;
        $record['end_user_revenue_after_tax'] = 0;
        $record['wht'] = 0;
        $record['rev_after_makro_share'] = 0;
        $record['discremancy_project'] = 0;
        $record['arpu_7'] = 0;
        $record['arpu_30'] = 0;
        $record['net_revenue'] = 0;
        $record['tax_operator'] = 0;
        $record['bearer_cost'] = 0;
        $record['shortcode_fee'] = 0;
        $record['waki_messaging'] = 0;
        $record['net_revenue_after_tax'] = 0;
        $record['end_user_rev_local_include_tax'] = 0;
        $record['end_user_rev_usd_include_tax'] = 0;
        $record['gross_usd_rev_after_tax'] = 0;
        $record['spec_tax'] = 0;
        $record['net_after_tax'] = 0;
        $record['government_cost'] = 0;
        $record['dealer_commision'] = 0;
        $record['uso'] = 0;
        $record['verto'] = 0;
        $record['agre_paxxa'] = 0;
        $record['net_income_after_vat'] = 0;
        $record['gross_revenue_share_linkit'] = 0;
        $record['gross_revenue_share_paxxa'] = 0;
        $record['pnl'] = 0;

        return $record;
    }
    
    public static function getReportsOperatorID($reports)
    {
        if(!empty($reports))
        {
            $reportsResult = array();
            $tempreport = array();

            foreach($reports as $report)
            {
                $tempreport[$report['operator_id']] = $report;
            }

            $reportsResult = $tempreport;
            return $reportsResult;
        }
    }

    public static function billRate($mt_success,$mt_failed,$total_subscriber)
    {
        $billing_rate = 0;

        $sent = $mt_success + $mt_failed;

        if($sent == 0)
        {
            if($total_subscriber > 0)
            {
                $billing_rate = ($mt_success/$total_subscriber)*100;  
            }
        }
        else if($mt_failed == 0)
        {
            if($total_subscriber > 0)
            {
                $billing_rate = ($mt_success/$total_subscriber)*100;
            }
        }else
        {
            $billing_rate = ($mt_success/$sent)*100;
        }

        return $billing_rate;        
    }

    public static function  Dailypush($mt_success,$mt_failed,$total_subscriber)
    {
        $Dailypush_rate = 0;

        $sent = $mt_success + $mt_failed;

        if($sent == 0)
        {
            if($total_subscriber > 0)
            {
                $Dailypush_rate = ($mt_success/$total_subscriber)*100;     
            }
        }
        else if($mt_failed == 0)
        {
            if($total_subscriber > 0)
            {
                $Dailypush_rate = ($mt_success/$total_subscriber)*100;
            }
        }else
        {
            $Dailypush_rate = ($mt_success/$sent)*100;
        }

        return $Dailypush_rate;      
    }

    public static function FirstPush($fmt_success,$fmt_failed,$total_subscriber)
    {
        $firstPushRate = 0;

        $sent = $fmt_success + $fmt_failed;

        if($sent == 0)
        {
            if($total_subscriber > 0)
            {
                $firstPushRate = ($fmt_success/$total_subscriber)*100;     
            }
        }
        else if($fmt_failed == 0)
        {
            if($total_subscriber > 0)
            {
                $firstPushRate = ($fmt_success/$total_subscriber)*100;
            }
        }else
        {
            $firstPushRate = ($fmt_success/$sent)*100;
        }

        return $firstPushRate;      
    }

    public static function dbColumnPrepare($reports)
    {
        $Records = array();

        if(!empty($reports))
        {
            foreach ($reports as $key => $report)
            {
                $reportTemp = Arr::except($report,['obj_operator',
                    'publisher',
                    'aggregator',
                    'and',
                    'client',
                    'country',
                    'payout',
                    'price_per_mo',
                    'service',
                    'id',
                    'url_campaign',
                    'created_at',
                    'updated_at',
                    'url_service',
                    'merchent_share',
                    'operator_share',
                ]);

                $Records[] = $reportTemp;
            }
        }

        return $Records;      
    }

    public static function formulaAccOperator($reports)  
    {
        $Records = array();

        $ZeroOperators = array('smartfren', 'safaricom', 'telkomsel', 'id-telkomsel-mks', 'id-smartfren-pass', 'id-smartfren-yatta', 'id-smartfren-kb', 'my-gtmh-linkit', 'mm-mytel-linkit', 'kh-metfone-linkit', 'za-mtn-mobixone', 'za-vodacom-mobixone', 'hti-natcom-linkit', 'th-ais-mks', 'ais', 'dtac', 'truemove', 'th-ais-qr', 'th-ais-gmob', 'th-true-qr', 'th-ais-old', 'vietnamobile', 'viettel', 'mobifone', 'vinaphone', 'ph-globe', 'smartp', 'id-tri-oxford', 'omn-omantel-linkit', 'srb-nth-linkit', 'se-all-linkit', 'waki-tsel-telesat', 'pass-tsel-telesat', 'kb-tsel-telesat');

        $wakimessageOperator = array('three', 'indosat', 'id-isat-yatta', 'id-isat-waki', 'id-isat-pass', 'uae-etisalat-linkit', 'ksa-stc', 'ksa-zein', 'ksa-mobily', 'eg-vodafone', 'eg-orange', 'eg-etisalat', 'ci-orange-linkit', 'id-xl-kb', 'id-xl-waki', 'id-xl-yatta', 'id-xl-pass', 'xlaxiata');

        if(!empty($reports))
        {
            $vat = 0;
            $spec_tax = 0;
            $government_cost = 0;
            $uso = 0;
            $verto = 0;
            $agre_paxxa = 0;
            $dealer_commision = 0;
            $wht = 0 ;

            foreach ($reports as $key => $report)
            {
                $operator = strtolower($report['operator']);
                $share = $report['share'];
                $cost_campaign = $report['cost_campaign'];
                $content = $report['content'];
                $merchent_share = $report['merchent_share'];
                $operator_share = $report['operator_share'];
                $rev_usd = $report['rev_usd'];
                $platform = $report['platform'];
                $rev = $report['rev'];
                $hosting_cost = $report['hosting_cost'];
                $rnd = $md = $report['rnd'];
                $bd = $report['bd'];
                $other_cost = $report['other_cost'];
                $id_operator = $report['id_operator'];

                if(in_array($operator,$ZeroOperators))
                {
                    $report['platform'] = 0;
                    $platform = 0;

                    $other_cost = $hosting_cost + $content + $md + $bd + $platform;

                    $pnl = $share - ($cost_campaign + $other_cost);
                     
                    $report['pnl'] = $pnl;
                    $report['other_cost'] = $other_cost;
                }

                if(in_array($operator,$wakimessageOperator))
                {
                    $rev_usd = $report['rev_usd'];

                    $report['platform'] = $platform = 0.05 * $rev_usd * ( $operator_share / 100) ;

                    $other_cost = $hosting_cost + $content + $md + $bd + $platform;

                    $report['other_cost'] = $other_cost;

                    $pnl = $share - ($cost_campaign + $other_cost);
                     
                    $report['pnl'] = $pnl;
                }

                if($operator == 'id-extravaganza-linkit')
                {
                    $hosting_cost = 0;
                    $content = 0;
                    $rndmd = 0;
                    $bd = 0;
                    $platform = 0;
                    $waki_messaging = 0.05 * $share;
                    $other_cost = $hosting_cost + $content + $rndmd + $bd + $platform + $waki_messaging;

                    $report['hosting_cost'] = 0;
                    $report['content'] = $content;
                    $report['other_cost'] = $other_cost;
                    $report['bd'] = $bd;
                    $report['platform'] = $platform;
                    $report['waki_messaging'] = $waki_messaging;
                    /*$report['saaf'] = 779.26;
                    $report['cost_campaign'] = 779.26;

                    $cost_campaign = 779.26;*/

                    $pnl = $share - ($cost_campaign + $other_cost);

                    $report['pnl'] = $pnl;
                }
                else if($operator == 'jazz')
                {
                    $net_revenue_after_tax = (($rev_usd * $merchent_share) / 100 ) - ((($rev_usd * $merchent_share) / 100 ) * 0.15 ) ;        
                    $hosting_cost = $net_revenue_after_tax * 0.08;
                    $content = $net_revenue_after_tax * 0.02;
                    $rndmd = $net_revenue_after_tax * 0.05;
                    $bd = $net_revenue_after_tax * 0.03;
                    $platform = $net_revenue_after_tax * 0.1;
                    $waki_messaging = 0.05 * $share;
                    $other_cost = $hosting_cost + $content + $rndmd + $bd + $platform ;
                
                    $pnl = $net_revenue_after_tax - ($cost_campaign + $platform + $content + $rndmd + $bd + $hosting_cost);

                    $report['net_revenue_after_tax'] = $net_revenue_after_tax;
                    $report['waki_messaging'] = $waki_messaging;
                    $report['hosting_cost'] = $hosting_cost;
                    $report['content'] = $content;
                    $report['other_cost'] = $other_cost;
                    $report['rnd'] = $rndmd;
                    $report['bd'] = $bd;
                    $report['platform'] = $platform;
                    $report['pnl'] = $pnl;
                }
                else if($operator == 'indosat')
                {
                    $hosting_cost = $share * 0.08;
                    $content = $share * 0.02;
                    $rnd = $share * 0.05;
                    $bd = $share * 0.03;
                    $other_cost = $hosting_cost + $content + $rnd + $bd + $platform ;
                    $pnl = $share - ($cost_campaign + $content + $rnd + $bd + $hosting_cost);

                    $report['hosting_cost'] = $hosting_cost;
                    $report['content'] = $content;
                    $report['other_cost'] = $other_cost;
                    $report['rnd'] = $rnd;
                    $report['bd'] = $bd;
                    $report['pnl'] = $pnl;
                }
                else if($operator == 'three')
                {
                    $platform = 0;
                    $hosting_cost = $share * 0.08;
                    $content = 0;
                    $rnd = $share * 0.05;
                    $bd = $share * 0.03;
                    $other_cost = $hosting_cost + $content + $rnd + $bd + $platform;
                    $pnl = $share - ($cost_campaign + $content + $rnd + $bd + $hosting_cost);

                    $report['other_cost'] = $other_cost;
                    $report['hosting_cost'] = $hosting_cost;
                    $report['content'] = $content;
                    $report['rnd'] = $rnd;
                    $report['bd'] = $bd;
                    $report['pnl'] = $pnl;
                }
                else if($operator == 'etisalat')
                {
                    $wht = $share * 0.1;
                    $net_revenue = $share - $wht;
                    $hosting_cost = ($net_revenue * 8) / 100 ;
                    $content =($net_revenue * 2) / 100 ;;
                    $rnd = ($net_revenue * 5) / 100 ;
                    $bd = ($net_revenue * 3) / 100 ;
                    $platform = ($net_revenue * 10) / 100 ;
                    $pnl = $net_revenue - ($cost_campaign + $platform + $content + $rnd + $bd + $hosting_cost);
                    $other_cost = $hosting_cost + $content + $rnd + $bd +$platform;

                    $report['other_cost'] = $other_cost;
                    $report['hosting_cost'] = $hosting_cost;
                    $report['content'] = $content;
                    $report['net_revenue'] = $net_revenue;
                    $report['platform'] = $platform;
                    $report['wht'] = $wht;
                    $report['rnd'] = $rnd;
                    $report['bd'] = $bd;
                    $report['pnl'] = $pnl;
                }
                else if($operator == 'smartfren')
                {
                    $hosting_cost = $share * 0.08;
                    $content = $share * 0.02;
                    $rnd = $share * 0.05;
                    $bd = $share * 0.03;
                    $pnl = $share - ($cost_campaign + $content + $rnd + $bd + $hosting_cost);
                     
                    $report['hosting_cost'] = $hosting_cost;
                    $report['content'] = $content;
                    $report['rnd'] = $rnd;
                    $report['bd'] = $bd;
                    $report['pnl'] = $pnl;
                }
                else if($operator == 'viva')
                {
                    $shortcode_fee = 163.52 / date('t');
                    $pnl = $share - ($cost_campaign + $content + $rnd + $bd + $hosting_cost + $shortcode_fee);

                    $report['pnl'] = $pnl;
                    $report['shortcode_fee'] = $shortcode_fee;
                }
                else if($operator == 'th-ais-mks')
                {
                    $rev_after_makro_share = $share - ($share * 0.1);
                    $discremancy_project = $rev_after_makro_share - ($rev_after_makro_share * 0.07);
                    $hosting_cost = ($discremancy_project * 8) / 100 ;
                    $content = ($discremancy_project * 2) / 100 ;;
                    $rnd = ($discremancy_project * 5) / 100 ;
                    $bd = ($discremancy_project * 3) / 100 ;
                    $other_cost = $hosting_cost + $content + $bd;
                    $pnl = $discremancy_project - ($cost_campaign + $content + $bd + $hosting_cost);
                   
                    $report['hosting_cost'] = $hosting_cost;
                    $report['content'] = $content;
                    $report['other_cost'] = $other_cost;
                    $report['rev_after_makro_share'] = $rev_after_makro_share;
                    $report['discremancy_project'] = $discremancy_project;
                    $report['bd'] = $bd;
                    $report['pnl'] = $pnl;
                }
                else if($operator == 'truemove')
                {
                    $pnl = (($share * $merchent_share) /100) - ($cost_campaign + $content + $rnd + $bd + $hosting_cost);

                    $report['pnl'] = $pnl;
                }
                else if($operator == 'ltc')
                {
                    $excise_tax = $rev_usd * 0.1;
                    $vat = ($rev_usd - $excise_tax) * 0.1;
                    $gross_usd_rev_after_tax = $rev_usd - $excise_tax - $vat;
                    $hosting_cost = (($gross_usd_rev_after_tax * $merchent_share) /100) * 0.08;
                    $content =(($gross_usd_rev_after_tax * $merchent_share) /100) * 0.02;
                    $rnd = (($gross_usd_rev_after_tax * $merchent_share) /100) * 0.05;
                    $bd = (($gross_usd_rev_after_tax * $merchent_share) /100) * 0.03;
                    $other_cost = (($gross_usd_rev_after_tax * $merchent_share) /100) * 0.07;
                    $pnl = (($gross_usd_rev_after_tax * $merchent_share) / 100) - ( $cost_campaign + $hosting_cost + $content + $rnd + $bd);

                    $report['hosting_cost'] = $hosting_cost;
                    $report['content'] = $content;
                    $report['other_cost'] = $other_cost;
                    $report['excise_tax'] = $excise_tax;
                    $report['vat'] = $vat;
                    $report['bd'] = $bd;
                    $report['rnd'] = $rnd;
                    $report['gross_usd_rev_after_tax'] = $gross_usd_rev_after_tax;
                    $report['pnl'] = $pnl;
                }
                else if($operator == 'unitel')
                {  
                    $hosting_cost = $share * 0.08;
                    $content = $share * 0.02;
                    $other_cost = $share * $hosting_cost;
                    $bd = $share * 0.03;
                    $pnl = $share - ($cost_campaign + $content + $bd + $hosting_cost);

                    $report['hosting_cost'] = $hosting_cost;
                    $report['content'] = $content;
                    $report['other_cost'] = $other_cost;
                    $report['bd'] = $bd;
                    $report['pnl'] = $pnl;
                }

                $country_name = $report['obj_operator']->country_name;

                $iscambodiaOperator = UtilityPnlCron::checkCambodiaOperator($country_name);

                if($iscambodiaOperator)
                {
                    $vat = ($rev_usd / 1.1) * 0.1;
                    $spec_tax = (($rev_usd - $vat) / 1.03 ) * 0.03;
                    $net_after_tax =  $rev_usd -$vat - $spec_tax;
                    $government_cost = ($rev_usd - $vat) * 0.07;
                    $dealer_commision = $rev_usd * 0.08;
                    $uso = $rev_usd * 0.03;
                    $net_revenue_after_tax = ($net_after_tax - ( $government_cost + $dealer_commision + $uso));

                    $report['vat'] = $vat;
                    $report['spec_tax'] = $spec_tax;
                    $report['net_after_tax'] = $net_after_tax;
                    $report['government_cost'] = $government_cost;
                    $report['dealer_commision'] = $dealer_commision;
                    $report['uso'] = $uso;
                    $report['net_revenue_after_tax'] = $net_revenue_after_tax;

                    if ($operator == 'kh-metfone-linkit') 
                    {
                        $verto = 0;
                        $net_revenue_after_tax = 0 ;
                        $agre_paxxa = 0;
                        $net_income_after_vat  = $rev_usd * 0.67;
                        $gross_revenue_share_linkit = $net_income_after_vat * 0.5;
                        $gross_revenue_share_paxxa = $gross_revenue_share_linkit;
                        $wht = $gross_revenue_share_linkit * 0.14;
                        $hosting_cost = $gross_revenue_share_linkit * 0.08;
                        $content = $gross_revenue_share_linkit * 0.02;
                        $rnd = $gross_revenue_share_linkit * 0.05;
                        $bd = $gross_revenue_share_linkit * 0.03;
                        $pnl = ($gross_revenue_share_linkit - $cost_campaign ) - ($wht + $hosting_cost + $content + $bd + $rnd);

                        $report['verto'] = $verto;
                        $report['net_revenue_after_tax'] = $net_revenue_after_tax;
                        $report['agre_paxxa'] = $agre_paxxa;
                        $report['net_income_after_vat'] = $net_income_after_vat;
                        $report['gross_revenue_share_linkit'] = $gross_revenue_share_linkit;
                        $report['gross_revenue_share_paxxa'] = $gross_revenue_share_paxxa;
                        $report['wht'] = $wht;
                        $report['hosting_cost'] = $hosting_cost;
                        $report['content'] = $content;
                        $report['rnd'] = $rnd;
                        $report['bd'] = $bd;
                        $report['pnl'] = $pnl;
                    }
                    else if ($operator == 'smart')
                    {
                        $agre_paxxa = 0;
                        $verto = $net_revenue_after_tax * 0.5;
                        $wht = $verto * 0.14;
                        $vminusw = ($verto - $wht);// on the fly
                        $platform =  $vminusw * 0.1;
                        $content =  $vminusw * 0.02;
                        $rnd = $vminusw * 0.05;
                        $bd = $vminusw * 0.03;
                        $hosting_cost = $vminusw * 0.08;
                        $pnl = $vminusw - ($cost_campaign + $platform + $hosting_cost + $content + $bd + $rnd);

                        $report['agre_paxxa'] = $agre_paxxa;
                        $report['verto'] = $verto;
                        $report['wht'] = $wht;
                        $report['platform'] = $platform;
                        $report['content'] = $content;
                        $report['rnd'] = $rnd;
                        $report['bd'] = $bd;
                        $report['hosting_cost'] = $hosting_cost;
                        $report['pnl'] = $pnl;
                    }
                    else
                    {
                        $agre_paxxa = 0;
                        $verto = $net_revenue_after_tax * 0.5;
                        $wht = $verto * 0.14;
                        $vminusw = $verto - $wht;
                        $platform = $vminusw * 0.1;
                        $content = $vminusw * 0.02;
                        $rnd = $md  = $vminusw * 0.05;
                        $bd = $vminusw * 0.03;
                        $pnl = $vminusw - ($cost_campaign + $platform + $content + $bd + $rnd);

                        $report['agre_paxxa'] = $agre_paxxa;
                        $report['verto'] = $verto;
                        $report['wht'] = $wht;
                        $report['platform'] = $platform;
                        $report['content'] = $content;
                        $report['rnd'] = $rnd;
                        $report['bd'] = $bd;
                        $report['hosting_cost'] = $hosting_cost;
                        $report['pnl'] = $pnl;
                    }
                }

                $other_tax = $vat + $spec_tax + $government_cost + $dealer_commision + $wht;
                $misc_tax  =  $uso + $verto + $agre_paxxa ;

                $report['other_tax'] = $other_tax;
                $report['misc_tax'] = $misc_tax;

                $Records[] = $report;
            }

        }

        return $Records;     
    }

    public static function checkCambodiaOperator($country_name)
    {
        $country_name = strtolower($country_name);
    
        if($country_name == "cambodia")
        {
            return true;
        }

        return false;
    }

    public static function columnNames($records)
    {
        $columns = array();

        if(!empty($records))
        {
            $records = collect($records[0]);

            $keys = $records->keys()->toArray();

            $columns = $keys;
        }

        return $columns;
    }
}
