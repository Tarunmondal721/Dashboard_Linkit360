<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Session;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\PnlsUserOperatorSummarize;
use App\Models\report_summarize;
use App\Models\ReportsPnlsOperatorSummarizes;
use App\Models\Operator;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use App\Models\Company;
use App\Models\Country;
use App\Models\UserPivot;
use App\Models\role_operators;
use App\Models\CompanyOperators;
use App\Models\User;
use App\common\Utility;
use App\common\UtilityReports;
use App\common\UtilityReportsMonthly;
use App\Models\ServiceHistory;
use App\Models\UsersOperatorsServices;
use App\common\UtilityMobifone;
use App\common\UtilityPercentage;
use Config;


class PivotReportSummery extends Controller
{   
    // get pnl report by operator id
    function getReportsByOperator($reports)
    {
        if(!empty($reports))
        {
            $reportsResult=array();
            $tempreport=array();
            foreach($reports as $report)
            {
                $tempreport[$report['id_operator']][$report['date']] = $report;
            }

            $reportsResult =  $tempreport;
            return $reportsResult;
        }
    }

    // get pivot report date wise
    function getPivotReportsDateWise($operator,$no_of_days,$reportsByIDs,$OperatorCountry,$service_historys=array())
    {
        // dd($reportsByIDs);
        $usdValue =isset($OperatorCountry['usd'])?$OperatorCountry['usd']:1;
        $shareDb = array();
        $merchent_share = 1;
        $operator_share = 1;
        $revenue_share = $operator->revenueshare;


        if(isset($revenue_share))
        {
            $merchent_share =$revenue_share->merchant_revenue_share;
            $operator_share =$revenue_share->operator_revenue_share;
        }

        $shareDb['merchent_share'] = $merchent_share;
        $shareDb['operator_share'] = $operator_share;

        if(!empty($no_of_days))
        {
            $allColumnData=array();
            $end_user_rev_usd_Arr = array();
            $end_user_rev_Arr = array();
            $gros_rev_usd_Arr = array();
            $gros_rev_Arr = array();
            $cost_campaign_Arr = array();
            $other_cost_Arr = array();
            $hosting_cost_Arr = array();
            $content_Arr = array();
            $rnd_Arr = array();
            $bd_Arr = array();
            $platform_Arr = array();
            $pnl_Arr = array();
            $bill =array();
            $first_push =array();
            $daily_push =array();
            $arpu7 = array();
            $usarpu7 = array();
            $arpu30 = array();
            $usarpu30 = array();
            $mtSuccess = array();
            $mtFailed = array();
            $fmtSuccess = array();
            $fmtFailed = array();
            $mo_Arr = array();
            $roi_Arr = array();
            $last_update="";
            $update =false;
            $id_operator = isset($operator->id_operator) ? $operator->id_operator : $operator->id;
            $testUSDSum = 0;

            foreach($no_of_days as $days)
            {
                /*"id" => 44424
                "date" => "2022-10-02"
                "type" => 1
                "id_operator" => 1
                "country_id" => 1
                "operator" => "telkomsel"
                "country_code" => "ID"
                "mo_received" => 358
                "mo_postback" => 72
                "cr_mo_received" => "9.15"
                "cr_mo_postback" => "1.87"
                "saaf" => "46.54"
                "sbaf" => "14.42"
                "cost_campaign" => "46.54"
                "clicks" => 72
                "ratio_for_cpa" => "4.97"
                "cpa_price" => "0.20"
                "cr_mo_clicks" => "497.22"
                "cr_mo_landing" => "7.55"
                "mo" => 358
                "landing" => 4743
                "reg" => 1
                "unreg" => 0
                "price_mo" => "0.1300"
                "active_subs" => 100682
                "rev_usd" => "0.2560"
                "rev" => "4000.0000"
                "share" => "0.1280"
                "lshare" => "2000.0000"
                "br" => "0.01"
                "br_success" => 3
                "br_failed" => 46143
                "fp" => "0.00"
                "rnd" => "0.01"
                "fp_success" => 0
                "fp_failed" => 1
                "dp" => "0.01"
                "dp_success" => 3
                "dp_failed" => 46143
                "other_cost" => "0.02"
                "other_tax" => "0.00"
                "misc_tax" => "0.00"
                "hosting_cost" => "0.01"
                "content" => "0.00"
                "bd" => "0.00"
                "platform" => "0.00"
                "excise_tax" => "0.00"
                "vat" => "0.00"
                "end_user_revenue_after_tax" => "0.00"
                "wht" => "0.00"
                "rev_after_makro_share" => "0.00"
                "discremancy_project" => "0.00"
                "arpu_7" => "0.00"
                "arpu_30" => "0.00"
                "net_revenue" => "0.00"
                "tax_operator" => "0.00"
                "bearer_cost" => "0.00"
                "shortcode_fee" => "0.00"
                "waki_messaging" => "0.00"
                "net_revenue_after_tax" => "0.00"
                "end_user_rev_local_include_tax" => "0.00"
                "end_user_rev_usd_include_tax" => "0.00"
                "gross_usd_rev_after_tax" => "0.00"
                "spec_tax" => "0.00"
                "net_after_tax" => "0.00"
                "government_cost" => "0.00"
                "dealer_commision" => "0.00"
                "uso" => "0.00"
                "verto" => "0.00"
                "agre_paxxa" => "0.00"
                "net_income_after_vat" => "0.00"
                "gross_revenue_share_linkit" => "0.00"
                "gross_revenue_share_paxxa" => "0.00"
                "pnl" => "-46.44"
                "created_at" => "2022-10-02T08:15:31.000000Z"
                "updated_at" => "2022-10-31T11:33:03.000000Z"*/

                /*End User Revenue (USD) = rev_usd
                End User Revenue (IDR) = rev
                Gross Revenue (USD) = share
                Gross Revenue (IDR) = lshare
                Cost Campaign (USD) = cost_campaign
                Other Cost = other_cost
                Hosting Cost = hosting_cost
                Content 2% = content
                RND 5% = rnd
                BD 3% = bd
                Vostok Platform Cost 10% = platform
                PNL = pnl*/

                $keys = $id_operator.".".$days['date'];
                $summariserow = Arr::get($reportsByIDs, $keys, 0);

                if($summariserow !=0  && !$update)
                {
                    // dd($summariserow);
                    $update =true;
                    $last_update = $summariserow['updated_at'];
                }   

                $end_user_rev_usd = 0;
                // $end_user_rev_usd = isset($summariserow['rev_usd']) ? $summariserow['rev_usd'] : 0;
                // $end_user_rev_usd = sprintf('%0.2f', $end_user_rev_usd);
                $total_subscriber = isset($summariserow['active_subs']) ? $summariserow['active_subs'] : 0;

                $end_user_rev = isset($summariserow['rev']) ? $summariserow['rev'] : 0;

                /*if($id_operator == 29)
                {
                    $end_user_rev = UtilityReports::getMobifoneRevenue($id_operator,$days,"daily");
                    $end_user_rev_usd = $end_user_rev * $usdValue;
                }*/

                if(isset($summariserow['rev']) && $id_operator!=29)
                {

                    $end_user_rev_usd = UtilityReports::UsdCalCriteria($end_user_rev,$usdValue,$summariserow,$OperatorCountry,$days);

                }


                // $gros_rev_usd = isset($summariserow['share']) ? $summariserow['share'] : 0;
                $gros_rev_usd = 0;

                $gros_rev = isset($summariserow['lshare']) ? $summariserow['lshare'] : 0;

                /*if($id_operator == 29)
                {
                    $gros_rev = UtilityReports::getMobifoneRevenue($id_operator,$days,"daily",$service_historys);
                    $gros_rev_usd = $gros_rev * $usdValue;
                }*/

                if(isset($summariserow['lshare']) && $id_operator!=29)
                {

                    $gros_rev_usd = UtilityReports::UsdCalCriteria($gros_rev,$usdValue,$summariserow,$OperatorCountry,$days);

                }


                $cost_campaign = isset($summariserow['cost_campaign']) ? $summariserow['cost_campaign'] : 0;


                $other_cost = isset($summariserow['other_cost']) ? $summariserow['other_cost'] : 0;


                $hosting_cost = isset($summariserow['hosting_cost']) ? $summariserow['hosting_cost'] : 0;


                $content = isset($summariserow['content']) ? $summariserow['content'] : 0;


                $rnd = isset($summariserow['rnd']) ? $summariserow['rnd'] : 0;


                $bd = isset($summariserow['bd']) ? $summariserow['bd'] : 0;


                $platform = isset($summariserow['platform']) ? $summariserow['platform'] : 0;


                $pnl = isset($summariserow['pnl']) ? $summariserow['pnl'] : 0;

                $mt_success = isset($summariserow['br_success']) ? $summariserow['br_success'] : 0;
                $mt_failed = isset($summariserow['br_failed']) ? $summariserow['br_failed'] : 0;
                $fmt_success = isset($summariserow['fp_success']) ? $summariserow['fp_success'] : 0;
                $fmt_failed = isset($summariserow['fp_failed']) ? $summariserow['fp_failed'] : 0;

                $mo = isset($summariserow['mo']) ? $summariserow['mo'] : 0;

                /* ROI Calculation */

                $current_mo = isset($summariserow['mo_received']) ? $summariserow['mo_received'] : 0;
                $current_cost = isset($summariserow['saaf']) ? $summariserow['saaf'] : 0;
                $current_price_mo = ($current_mo == 0) ? 0 : ($current_cost / $current_mo);
                $current_usd_rev_share = (((float)$end_user_rev_usd * (float)$merchent_share) / 100 );
                $current_reg =  isset($summariserow['reg']) ? $summariserow['reg'] : 0;
                $current_reg_sub = $current_reg + $total_subscriber;
                $current_30_arpu =   ($current_reg_sub == 0) ? 0 : ($current_usd_rev_share / $current_reg_sub);
                $roi = ($current_30_arpu == 0) ? 0 : ($current_price_mo / $current_30_arpu);

                /* End */

                $renewal_total = $mt_success+$mt_failed;

                $billRate = UtilityReports::billRate($mt_success,$mt_failed,$total_subscriber);

                $FirstPush = UtilityReports::FirstPush($fmt_success,$fmt_failed,$total_subscriber);
                
                $Dailypush = UtilityReports::Dailypush($mt_success,$mt_failed,$total_subscriber);

                $arpu7Data = UtilityReports::Arpu7($operator,$reportsByIDs,$days,$total_subscriber,$shareDb);
                $arpu7USD = $arpu7Data * $usdValue;


                $arpuRawdata = UtilityPercentage::Arpu7Raw($operator,$reportsByIDs,$days,$total_subscriber,$shareDb,$OperatorCountry,$service_historys);

                $arpu30Data = UtilityReports::Arpu30($operator,$reportsByIDs,$days,$total_subscriber,$shareDb);
                $arpu30USD =  $arpu30Data * $usdValue;

                $arpu30Rawdata = UtilityPercentage::Arpu30Raw($operator,$reportsByIDs,$days,$total_subscriber,$shareDb,$OperatorCountry,$service_historys);


                $end_user_rev_usd_Arr[$days['date']]['value'] = $end_user_rev_usd;
                $end_user_rev_usd_Arr[$days['date']]['class'] = "bg-hui";

                $end_user_rev_Arr[$days['date']]['value'] = $end_user_rev;
                $end_user_rev_Arr[$days['date']]['class'] = "bg-hui";

                $gros_rev_usd_Arr[$days['date']]['value'] = $gros_rev_usd;
                $gros_rev_usd_Arr[$days['date']]['class'] = "bg-hui";

                $gros_rev_Arr[$days['date']]['value'] = $gros_rev;
                $gros_rev_Arr[$days['date']]['class'] = "bg-hui";

                $cost_campaign_Arr[$days['date']]['value'] = $cost_campaign;
                $cost_campaign_Arr[$days['date']]['class'] = "bg-hui";

                $other_cost_Arr[$days['date']]['value'] = $other_cost;
                $other_cost_Arr[$days['date']]['class'] = "bg-hui";

                $hosting_cost_Arr[$days['date']]['value'] = $hosting_cost;
                $hosting_cost_Arr[$days['date']]['class'] = "bg-hui";

                $content_Arr[$days['date']]['value'] = $content;
                $content_Arr[$days['date']]['class'] = "bg-hui";

                $rnd_Arr[$days['date']]['value'] = $rnd;
                $rnd_Arr[$days['date']]['class'] = "bg-hui";

                $bd_Arr[$days['date']]['value'] = $bd;
                $bd_Arr[$days['date']]['class'] = "bg-hui";

                $platform_Arr[$days['date']]['value'] = $platform;
                $platform_Arr[$days['date']]['class'] = "bg-hui";

                $pnl_Arr[$days['date']]['value'] = $pnl;
                $pnl_Arr[$days['date']]['class'] = "bg-hui";

                $t_sub[$days['date']]['value']=$total_subscriber;
                $t_sub[$days['date']]['class']="bg-hui";

                $arpu7Raw[$days['date']]['value']= $arpuRawdata;
                $arpu7Raw[$days['date']]['class']="";

                $arpu30Raw[$days['date']]['value']= $arpu30Rawdata;
                $arpu30Raw[$days['date']]['class']="";

                $bill[$days['date']]['value']=$billRate;
                $bill[$days['date']]['class']="bg-hui";

                $first_push[$days['date']]['value']=$FirstPush;
                $first_push[$days['date']]['class']="bg-hui";

                $daily_push[$days['date']]['value']=$Dailypush;
                $daily_push[$days['date']]['class']="bg-hui";

                $arpu7[$days['date']]['value']=$arpu7Data;
                $arpu7[$days['date']]['class']="bg-hui";

                $usarpu7[$days['date']]['value']=$arpu7USD;
                $usarpu7[$days['date']]['class']="bg-hui";

                $arpu30[$days['date']]['value']= $arpu30Data;
                $arpu30[$days['date']]['class']="bg-hui";

                $usarpu30[$days['date']]['value']=$arpu30USD;
                $usarpu30[$days['date']]['class']="bg-hui";

                $mtSuccess[$days['date']]['value']=$mt_success;
                $mtSuccess[$days['date']]['class']="bg-hui";

                $mtFailed[$days['date']]['value']=$mt_failed;
                $mtFailed[$days['date']]['class']="bg-hui";

                $fmtSuccess[$days['date']]['value']=$fmt_success;
                $fmtSuccess[$days['date']]['class']="bg-hui";

                $fmtFailed[$days['date']]['value']=$fmt_failed;
                $fmtFailed[$days['date']]['class']="bg-hui";

                $mo_Arr[$days['date']]['value']=$mo;
                $mo_Arr[$days['date']]['class']="bg-hui";

                $roi_Arr[$days['date']]['value']=$roi;
                $roi_Arr[$days['date']]['class']="bg-hui";
            }

            $last_update_show = "Not updated last month";
            if($last_update!="")
            {
                $last_update_timestamp =Carbon::parse($last_update);
                $last_update_timestamp->setTimezone('Asia/Jakarta');
                $last_update_show = $last_update_timestamp->format("Y-m-d H:i:s"). " Asia/Jakarta";
            }

            $allColumnData['end_user_rev_usd'] = $end_user_rev_usd_Arr;
            $allColumnData['end_user_rev'] = $end_user_rev_Arr;
            $allColumnData['gros_rev_usd'] = $gros_rev_usd_Arr;
            $allColumnData['gros_rev'] = $gros_rev_Arr;
            $allColumnData['cost_campaign'] = $cost_campaign_Arr;
            $allColumnData['other_cost'] = $other_cost_Arr;
            $allColumnData['hosting_cost'] = $hosting_cost_Arr;
            $allColumnData['content'] = $content_Arr;
            $allColumnData['rnd'] = $rnd_Arr;
            $allColumnData['bd'] = $bd_Arr;
            $allColumnData['platform'] = $platform_Arr;
            $allColumnData['pnl'] = $pnl_Arr;
            $allColumnData['t_sub'] = $t_sub;
            $allColumnData['bill'] = $bill;
            $allColumnData['first_push'] = $first_push;
            $allColumnData['daily_push'] = $daily_push;
            $allColumnData['arpu7'] = $arpu7;
            $allColumnData['usarpu7'] = $usarpu7;
            $allColumnData['arpu30'] = $arpu30;
            $allColumnData['usarpu30'] = $usarpu30;
            $allColumnData['mt_success'] = $mtSuccess;
            $allColumnData['mt_failed'] = $mtFailed;
            $allColumnData['fmt_success'] = $fmtSuccess;
            $allColumnData['fmt_failed'] = $fmtFailed;
            $allColumnData['mo'] = $mo_Arr;
            $allColumnData['roi'] = $roi_Arr;
            $allColumnData['arpu7Raw'] = $arpu7Raw;
            $allColumnData['arpu30Raw'] = $arpu30Raw;
            $allColumnData['last_update'] = $last_update_show;

            return $allColumnData;
        }
    }

    public function pivotsummary(Request $request)
    {
        $data = [];
        $data['ReportType'] = '';
        $user = Auth::user();
        $user_id = $user->id;
        $UserPivot=UserPivot::GetByUserId($user_id)->first();
        $data['report_type'] =json_decode($UserPivot->report_type);
        $data['report_column'] =json_decode($UserPivot->report_column);
        $data['date_range'] =json_decode($UserPivot->description);

        return view('report.pivotsummary', compact('data'));
    }

    /*Daily pivot report by opeator*/
    public function DailyPivotReportOperator(Request $request)
    {
        $data = [];
        $data['ReportType'] = 'operator';
        $data['Daily'] = 1;
        $user = Auth::user();
        $user_id = $user->id;
        $UserPivot=UserPivot::GetByUserId($user_id)->first();
        $data['report_type'] =json_decode($UserPivot->report_type);
        $data['report_column'] =json_decode($UserPivot->report_column);
        $data['date_range'] =json_decode($UserPivot->description);

        $CountryId = $req_CountryId = $request->country;
        $CompanyId = $req_CompanyId = $request->company;
        $filterOperator = $req_filterOperator = $request->operatorId;
        $Start_date = $req_Start_date = $request->from;
        $end_date =  $req_end_date = trim($request->to);

        /*If from is less than to*/
        if($end_date <= $Start_date)
        {
            $Start_date = $req_Start_date = trim($request->to);
            $end_date =  $req_end_date = $request->from;
        }

        $start_date = Carbon::now()->startOfMonth()->subDays(35)->format('Y-m-d');
        $startColumnDateDisplay = Carbon::now()->startOfMonth()->format('Y-m-d');
        $end_date = Carbon::now()->format('Y-m-d');
        $month = Carbon::now()->format('F Y');

        /* filter Search Section */

        $showAllOperator = true;

        if($request->filled('to') && $request->filled('from'))
        {
            $start_date_input = new Carbon($req_Start_date);
            $display_date_input = new Carbon($req_Start_date);
            $start_date = $start_date_input->subDays(35)->format('Y-m-d');
            $startColumnDateDisplay = $display_date_input->format('Y-m-d');
            $end_date = $req_end_date;
        }

        if($request->filled('company') && $req_CompanyId !="allcompany")
        {
            $companies = Company::Find($req_CompanyId);
            $Operators_company = array();

            if(!empty($companies))
            {
                $Operators_company = $companies->company_operators->pluck('operator_id')->toArray();
                $Operators = Operator::with('revenueshare')->Status(1)->GetOperatorByOperatorId($Operators_company)->get();
            }

            $showAllOperator = false;
        }

        if($showAllOperator)
        {
            $Operators = Operator::with('revenueshare')->Status(1)->get();
        }

        if($request->filled('country'))
        {
            $Operators = Operator::with('revenueshare')->Status(1)->GetOperatorByCountryId($req_CountryId)->get();
            $showAllOperator = false;
        }


        $Country = Country::all()->toArray();
        $companys = Company::get();
        $countries = array();
        if(!empty($Country))
        {
            foreach($Country as $CountryI)
            {
                $countries[$CountryI['id']]=$CountryI;
            }
        }

        $staticOperators = $Operators->pluck('id_operator')->toArray();
        $sumemry = array();
        $pnl_report_summery = [];
        $report_summery = [];

        /* Admin Access All operator and Services */

        $user = Auth::user();
        $user_id = $user->id;
        $user_type = $user->type;
        $allowAllOperator = $user->WhowAccessAlOperator($user_type);

        if($allowAllOperator)
        {
            $reports = ReportsPnlsOperatorSummarizes::filterOperator($staticOperators)
            ->filterDateRange($start_date,$end_date)
            ->orderBy('country_id')
            ->orderBy('date', 'ASC')
            ->get()->toArray();

        }else{

            $UserOperatorServices =Session::get('userOperatorService');
            if(empty($UserOperatorServices))
            {
                dd("Please Contact to admin , add Operator to your account");
            }

            $arrayOperatorsIds = $UserOperatorServices['id_operators'];
            $arrayServicesIds = $UserOperatorServices['id_services'];

            $reports = PnlsUserOperatorSummarize::filterOperator($arrayOperatorsIds)
            ->filterDateRange($start_date,$end_date)
            ->orderBy('country_id')
            ->orderBy('date', 'ASC')
            ->get()->toArray();
        }



        $service_historys_result = ServiceHistory::FilterOperator(29)->filterDateRange($start_date,$end_date)->get();
        $service_historys = UtilityMobifone::ServiceRearrangeDate($service_historys_result);

        $reportsByIDs = $this->getReportsByOperator($reports);
        $datesIndividual =Utility::getRangeDates($startColumnDateDisplay,$end_date);
        $no_of_days = Utility::getRangeDateNo($datesIndividual);

        if(!empty($Operators))
        {
            foreach($Operators as $operator)
            {
                $tmpOperators=array();
                $tmpOperators['operator'] = $operator;
                $country_id  = $operator->country_id;
                $contain_id = Arr::exists($countries, $country_id);
                $OperatorCountry = array();
                if($operator->id_operator == 29)
                {
                    $operator->service_historys = $service_historys;
                }

                if($contain_id )
                {
                    $tmpOperators['country']=$countries[$country_id];
                    $OperatorCountry = $countries[$country_id];
                }

                $reportsColumnData = $this->getPivotReportsDateWise($operator,$no_of_days,$reportsByIDs,$OperatorCountry,$service_historys);
                $tmpOperators['month_string'] = $month;
                $tmpOperators['last_update'] = $reportsColumnData['last_update'];
                // dd($reportsColumnData);

                $total_avg_rev_usd = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['end_user_rev_usd'],$startColumnDateDisplay,$end_date);
                $tmpOperators['end_user_rev_usd']['dates'] = $reportsColumnData['end_user_rev_usd'];
                $tmpOperators['end_user_rev_usd']['total'] = $total_avg_rev_usd['sum'];
                $tmpOperators['end_user_rev_usd']['t_mo_end'] = $total_avg_rev_usd['T_Mo_End'];
                $tmpOperators['end_user_rev_usd']['avg'] = $total_avg_rev_usd['avg'];


                $total_avg_rev = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['end_user_rev'],$startColumnDateDisplay,$end_date);

                $tmpOperators['end_user_rev']['dates'] = $reportsColumnData['end_user_rev'];
                $tmpOperators['end_user_rev']['total'] = $total_avg_rev['sum'];
                $tmpOperators['end_user_rev']['t_mo_end'] = $total_avg_rev['T_Mo_End'];
                $tmpOperators['end_user_rev']['avg'] = $total_avg_rev['avg'];


                $total_avg_gros_rev_usd = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['gros_rev_usd'],$startColumnDateDisplay,$end_date);

                $tmpOperators['gros_rev_usd']['dates'] = $reportsColumnData['gros_rev_usd'];
                $tmpOperators['gros_rev_usd']['total'] = $total_avg_gros_rev_usd['sum'];
                $tmpOperators['gros_rev_usd']['t_mo_end'] = $total_avg_gros_rev_usd['T_Mo_End'];
                $tmpOperators['gros_rev_usd']['avg'] = $total_avg_gros_rev_usd['avg'];


                $total_avg_gros_rev = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['gros_rev'],$startColumnDateDisplay,$end_date);

                $tmpOperators['gros_rev']['dates'] = $reportsColumnData['gros_rev'];
                $tmpOperators['gros_rev']['total'] = $total_avg_gros_rev['sum'];
                $tmpOperators['gros_rev']['t_mo_end'] = $total_avg_gros_rev['T_Mo_End'];
                $tmpOperators['gros_rev']['avg'] = $total_avg_gros_rev['avg'];


                $total_avg_cost_campaign = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['cost_campaign'],$startColumnDateDisplay,$end_date);

                $tmpOperators['cost_campaign']['dates'] = $reportsColumnData['cost_campaign'];
                $tmpOperators['cost_campaign']['total'] = $total_avg_cost_campaign['sum'];
                $tmpOperators['cost_campaign']['t_mo_end'] = $total_avg_cost_campaign['T_Mo_End'];
                $tmpOperators['cost_campaign']['avg'] = $total_avg_cost_campaign['avg'];


                $total_avg_other_cost = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['other_cost'],$startColumnDateDisplay,$end_date);

                $tmpOperators['other_cost']['dates'] = $reportsColumnData['other_cost'];
                $tmpOperators['other_cost']['total'] = $total_avg_other_cost['sum'];
                $tmpOperators['other_cost']['t_mo_end'] = $total_avg_other_cost['T_Mo_End'];
                $tmpOperators['other_cost']['avg'] = $total_avg_other_cost['avg'];


                $total_avg_hosting_cost = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['hosting_cost'],$startColumnDateDisplay,$end_date);

                $tmpOperators['hosting_cost']['dates'] = $reportsColumnData['hosting_cost'];
                $tmpOperators['hosting_cost']['total'] = $total_avg_hosting_cost['sum'];
                $tmpOperators['hosting_cost']['t_mo_end'] = $total_avg_hosting_cost['T_Mo_End'];
                $tmpOperators['hosting_cost']['avg'] = $total_avg_hosting_cost['avg'];


                $total_avg_content = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['content'],$startColumnDateDisplay,$end_date);

                $tmpOperators['content']['dates'] = $reportsColumnData['content'];
                $tmpOperators['content']['total'] = $total_avg_content['sum'];
                $tmpOperators['content']['t_mo_end'] = $total_avg_content['T_Mo_End'];
                $tmpOperators['content']['avg'] = $total_avg_content['avg'];


                $total_avg_rnd = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['rnd'],$startColumnDateDisplay,$end_date);

                $tmpOperators['rnd']['dates'] = $reportsColumnData['rnd'];
                $tmpOperators['rnd']['total'] = $total_avg_rnd['sum'];
                $tmpOperators['rnd']['t_mo_end'] = $total_avg_rnd['T_Mo_End'];
                $tmpOperators['rnd']['avg'] = $total_avg_rnd['avg'];


                $total_avg_bd = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['bd'],$startColumnDateDisplay,$end_date);

                $tmpOperators['bd']['dates'] = $reportsColumnData['bd'];
                $tmpOperators['bd']['total'] = $total_avg_bd['sum'];
                $tmpOperators['bd']['t_mo_end'] = $total_avg_bd['T_Mo_End'];
                $tmpOperators['bd']['avg'] = $total_avg_bd['avg'];


                $total_avg_platform = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['platform'],$startColumnDateDisplay,$end_date);

                $tmpOperators['platform']['dates'] = $reportsColumnData['platform'];
                $tmpOperators['platform']['total'] = $total_avg_platform['sum'];
                $tmpOperators['platform']['t_mo_end'] = $total_avg_platform['T_Mo_End'];
                $tmpOperators['platform']['avg'] = $total_avg_platform['avg'];


                $total_avg_pnl = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['pnl'],$startColumnDateDisplay,$end_date);

                $tmpOperators['pnl']['dates'] = $reportsColumnData['pnl'];
                $tmpOperators['pnl']['total'] = $total_avg_pnl['sum'];
                $tmpOperators['pnl']['t_mo_end'] = $total_avg_pnl['T_Mo_End'];
                $tmpOperators['pnl']['avg'] = $total_avg_pnl['avg'];


                $total_avg_mo = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['mo'],$startColumnDateDisplay,$end_date);

                $tmpOperators['mo']['dates'] = $reportsColumnData['mo'];
                $tmpOperators['mo']['total'] = $total_avg_mo['sum'];
                $tmpOperators['mo']['t_mo_end'] = $total_avg_mo['T_Mo_End'];
                $tmpOperators['mo']['avg'] = $total_avg_mo['avg'];


                $total_avg_roi = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['roi'],$startColumnDateDisplay,$end_date);

                $tmpOperators['roi']['dates'] = $reportsColumnData['roi'];
                $tmpOperators['roi']['total'] = $total_avg_roi['sum'];
                $tmpOperators['roi']['t_mo_end'] = $total_avg_roi['T_Mo_End'];
                $tmpOperators['roi']['avg'] = $total_avg_roi['avg'];


                $total_avg_t_sub = UtilityReports::calculateTotalSubscribe($operator,$reportsColumnData['t_sub'],$startColumnDateDisplay,$end_date);

                $tmpOperators['t_sub']['dates']=$reportsColumnData['t_sub'];
                $tmpOperators['t_sub']['total']=$total_avg_t_sub['sum'];
                $tmpOperators['t_sub']['t_mo_end']=$total_avg_t_sub['T_Mo_End'];
                $tmpOperators['t_sub']['avg']=$total_avg_t_sub['avg'];


                $total_avg_t_bill = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['bill'],$startColumnDateDisplay,$end_date);

                $tmpOperators['bill']['dates']=$reportsColumnData['bill'];
                $tmpOperators['bill']['total']=0;
                $tmpOperators['bill']['t_mo_end']=0;
                $tmpOperators['bill']['avg']=$total_avg_t_bill['avg'];

                $total_avg_t_first_push = UtilityPercentage::PercentageDataAVG($operator,$reportsColumnData['first_push'],$startColumnDateDisplay,$end_date);

                $tmpOperators['first_push']['dates']=$reportsColumnData['first_push'];
                $tmpOperators['first_push']['total']=0;
                $tmpOperators['first_push']['t_mo_end']=0;
                $tmpOperators['first_push']['avg']=$total_avg_t_first_push['avg'];

                 $total_avg_t_daily_push = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['daily_push'],$startColumnDateDisplay,$end_date);

                $tmpOperators['daily_push']['dates']=$reportsColumnData['daily_push'];
                $tmpOperators['daily_push']['total']=0;
                $tmpOperators['daily_push']['t_mo_end']=0;
                $tmpOperators['daily_push']['avg']=$total_avg_t_daily_push['avg'];

                $total_avg_t_arpu7 = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['arpu7'],$startColumnDateDisplay,$end_date);

                $tmpOperators['arpu7']['dates']=$reportsColumnData['arpu7'];
                $tmpOperators['arpu7']['total']=0;
                $tmpOperators['arpu7']['t_mo_end']=0;
                $tmpOperators['arpu7']['avg']=$total_avg_t_arpu7['avg'];

                $tmpOperators['arpu7raw']['dates']=$reportsColumnData['arpu7Raw'];
                $tmpOperators['arpu7raw']['total']=0;
                $tmpOperators['arpu7raw']['t_mo_end']=0;
                $tmpOperators['arpu7raw']['avg']=0;

                $tmpOperators['arpu30raw']['dates']=$reportsColumnData['arpu30Raw'];
                $tmpOperators['arpu30raw']['total']=0;
                $tmpOperators['arpu30raw']['t_mo_end']=0;
                $tmpOperators['arpu30raw']['avg']=0;

                 $total_avg_t_usarpu7 = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['usarpu7'],$startColumnDateDisplay,$end_date);

                $tmpOperators['usarpu7']['dates']=$reportsColumnData['usarpu7'];
                $tmpOperators['usarpu7']['total']=0;
                $tmpOperators['usarpu7']['t_mo_end']=0;
                $tmpOperators['usarpu7']['avg']= $total_avg_t_usarpu7['avg'];


                $total_avg_t_arpu30 = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['arpu30'],$startColumnDateDisplay,$end_date);

                $tmpOperators['arpu30']['dates'] = $reportsColumnData['arpu30'];
                $tmpOperators['arpu30']['total'] = 0;
                $tmpOperators['arpu30']['t_mo_end'] = 0;
                $tmpOperators['arpu30']['avg'] = $total_avg_t_arpu30['avg'];

                 $total_avg_t_usarpu30 = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['usarpu30'],$startColumnDateDisplay,$end_date);

                $tmpOperators['usarpu30']['dates'] = $reportsColumnData['usarpu30'];
                $tmpOperators['usarpu30']['total'] = 0;
                $tmpOperators['usarpu30']['t_mo_end'] = 0;
                $tmpOperators['usarpu30']['avg'] =  $total_avg_t_usarpu30['avg'];

                $tmpOperators['mt_success']['dates'] = $reportsColumnData['mt_success'];
                $tmpOperators['mt_failed']['dates'] = $reportsColumnData['mt_failed'];

                $tmpOperators['fmt_success']['dates'] = $reportsColumnData['fmt_success'];
                $tmpOperators['fmt_failed']['dates'] = $reportsColumnData['fmt_failed'];


                // dd($tmpOperators);
                $sumemry[] = $tmpOperators;
            }
        }

        $sumOfSummaryData = UtilityReports::pivotSummaryDataSum($sumemry);
        $sumOfSummaryData = UtilityReports::allSummeryPerCal($sumOfSummaryData);

        // dd($sumemry);
        // dd($sumOfSummaryData);
        return view('report.pivot.daily_operator_pivotsummery', compact('no_of_days','sumemry','sumOfSummaryData','data'));
    }
    
    /*Daily pivot report by country*/
    public function DailyPivotReportCountry(Request $request)
    {
        $data = [];
        $data['ReportType'] = 'country';
        $data['Daily'] = 1;
        $user = Auth::user();
        $user_id = $user->id;
        $UserPivot=UserPivot::GetByUserId($user_id)->first();
        $data['report_type'] =json_decode($UserPivot->report_type);
        $data['report_column'] =json_decode($UserPivot->report_column);
        $data['date_range'] =json_decode($UserPivot->description);

        $CountryId = $req_CountryId = $request->country;
        $CompanyId = $req_CompanyId = $request->company;
        $filterOperator = $req_filterOperator = $request->operatorId;
        $Start_date = $req_Start_date = $request->from;
        $end_date =  $req_end_date = trim($request->to);

        /*If from is less than to*/
        if($end_date <= $Start_date)
        {
            $Start_date = $req_Start_date = trim($request->to);
            $end_date =  $req_end_date = $request->from;
        }

        $start_date = Carbon::now()->startOfMonth()->subDays(35)->format('Y-m-d');
        $startColumnDateDisplay = Carbon::now()->startOfMonth()->format('Y-m-d');
        $end_date = Carbon::now()->format('Y-m-d');
        $month = Carbon::now()->format('F Y');

        /* filter Search Section */

        $showAllOperator = true;

        if($request->filled('to') && $request->filled('from'))
        {
            $start_date_input = new Carbon($req_Start_date);
            $display_date_input = new Carbon($req_Start_date);
            $start_date = $start_date_input->subDays(35)->format('Y-m-d');
            $startColumnDateDisplay = $display_date_input->format('Y-m-d');
            $end_date = $req_end_date;
        }

        if($request->filled('company') && $req_CompanyId !="allcompany")
        {
            $companies = Company::Find($req_CompanyId);
            $Operators_company = array();

            if(!empty($companies))
            {
                $Operators_company = $companies->company_operators->pluck('operator_id')->toArray();
                $Operators = Operator::with('revenueshare')->Status(1)->GetOperatorByOperatorId($Operators_company)->get();
            }

            $showAllOperator = false;
        }

        if($showAllOperator)
        {
            $Operators = Operator::with('revenueshare')->Status(1)->get();
        }

        if($request->filled('country'))
        {
            $Operators = Operator::with('revenueshare')->Status(1)->GetOperatorByCountryId($req_CountryId)->get();
            $showAllOperator = false;
        }


        $Country = Country::all()->toArray();
        $companys = Company::get();
        $countries = array();
        if(!empty($Country))
        {
            foreach($Country as $CountryI)
            {
                $countries[$CountryI['id']]=$CountryI;
            }
        }

        $staticOperators = $Operators->pluck('id_operator')->toArray();
        $sumemry = array();
        $pnl_report_summery = [];
        $report_summery = [];

        /* Admin Access All operator and Services */

        $user = Auth::user();
        $user_id = $user->id;
        $user_type = $user->type;
        $allowAllOperator = $user->WhowAccessAlOperator($user_type);

        if($allowAllOperator)
        {
            $reports = ReportsPnlsOperatorSummarizes::filterOperator($staticOperators)
            ->filterDateRange($start_date,$end_date)
            ->orderBy('country_id')
            ->orderBy('date', 'ASC')
            ->get()->toArray();

        }else{

            $UserOperatorServices =Session::get('userOperatorService');
            if(empty($UserOperatorServices))
            {
                dd("Please Contact to admin , add Operator to your account");
            }

            $arrayOperatorsIds = $UserOperatorServices['id_operators'];
            $arrayServicesIds = $UserOperatorServices['id_services'];

            $reports = PnlsUserOperatorSummarize::filterOperator($arrayOperatorsIds)
            ->filterDateRange($start_date,$end_date)
            ->orderBy('country_id')
            ->orderBy('date', 'ASC')
            ->get()->toArray();
        }



        $service_historys_result = ServiceHistory::FilterOperator(29)->filterDateRange($start_date,$end_date)->get();
        $service_historys = UtilityMobifone::ServiceRearrangeDate($service_historys_result);

        $reportsByIDs = $this->getReportsByOperator($reports);
        $datesIndividual =Utility::getRangeDates($startColumnDateDisplay,$end_date);
        $no_of_days = Utility::getRangeDateNo($datesIndividual);

        if(!empty($Operators))
        {
            foreach($Operators as $operator)
            {
                $tmpOperators=array();
                $tmpOperators['operator'] = $operator;
                $country_id  = $operator->country_id;
                $contain_id = Arr::exists($countries, $country_id);
                $OperatorCountry = array();
                if($operator->id_operator == 29)
                {
                    $operator->service_historys = $service_historys;
                }

                if($contain_id )
                {
                    $tmpOperators['country']=$countries[$country_id];
                    $OperatorCountry = $countries[$country_id];
                }

                $reportsColumnData = $this->getPivotReportsDateWise($operator,$no_of_days,$reportsByIDs,$OperatorCountry,$service_historys);
                $tmpOperators['month_string'] = $month;
                $tmpOperators['last_update'] = $reportsColumnData['last_update'];
                // dd($reportsColumnData);

                $total_avg_rev_usd = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['end_user_rev_usd'],$startColumnDateDisplay,$end_date);
                $tmpOperators['end_user_rev_usd']['dates'] = $reportsColumnData['end_user_rev_usd'];
                $tmpOperators['end_user_rev_usd']['total'] = $total_avg_rev_usd['sum'];
                $tmpOperators['end_user_rev_usd']['t_mo_end'] = $total_avg_rev_usd['T_Mo_End'];
                $tmpOperators['end_user_rev_usd']['avg'] = $total_avg_rev_usd['avg'];


                $total_avg_rev = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['end_user_rev'],$startColumnDateDisplay,$end_date);

                $tmpOperators['end_user_rev']['dates'] = $reportsColumnData['end_user_rev'];
                $tmpOperators['end_user_rev']['total'] = $total_avg_rev['sum'];
                $tmpOperators['end_user_rev']['t_mo_end'] = $total_avg_rev['T_Mo_End'];
                $tmpOperators['end_user_rev']['avg'] = $total_avg_rev['avg'];


                $total_avg_gros_rev_usd = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['gros_rev_usd'],$startColumnDateDisplay,$end_date);

                $tmpOperators['gros_rev_usd']['dates'] = $reportsColumnData['gros_rev_usd'];
                $tmpOperators['gros_rev_usd']['total'] = $total_avg_gros_rev_usd['sum'];
                $tmpOperators['gros_rev_usd']['t_mo_end'] = $total_avg_gros_rev_usd['T_Mo_End'];
                $tmpOperators['gros_rev_usd']['avg'] = $total_avg_gros_rev_usd['avg'];


                $total_avg_gros_rev = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['gros_rev'],$startColumnDateDisplay,$end_date);

                $tmpOperators['gros_rev']['dates'] = $reportsColumnData['gros_rev'];
                $tmpOperators['gros_rev']['total'] = $total_avg_gros_rev['sum'];
                $tmpOperators['gros_rev']['t_mo_end'] = $total_avg_gros_rev['T_Mo_End'];
                $tmpOperators['gros_rev']['avg'] = $total_avg_gros_rev['avg'];


                $total_avg_cost_campaign = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['cost_campaign'],$startColumnDateDisplay,$end_date);

                $tmpOperators['cost_campaign']['dates'] = $reportsColumnData['cost_campaign'];
                $tmpOperators['cost_campaign']['total'] = $total_avg_cost_campaign['sum'];
                $tmpOperators['cost_campaign']['t_mo_end'] = $total_avg_cost_campaign['T_Mo_End'];
                $tmpOperators['cost_campaign']['avg'] = $total_avg_cost_campaign['avg'];


                $total_avg_other_cost = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['other_cost'],$startColumnDateDisplay,$end_date);

                $tmpOperators['other_cost']['dates'] = $reportsColumnData['other_cost'];
                $tmpOperators['other_cost']['total'] = $total_avg_other_cost['sum'];
                $tmpOperators['other_cost']['t_mo_end'] = $total_avg_other_cost['T_Mo_End'];
                $tmpOperators['other_cost']['avg'] = $total_avg_other_cost['avg'];


                $total_avg_hosting_cost = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['hosting_cost'],$startColumnDateDisplay,$end_date);

                $tmpOperators['hosting_cost']['dates'] = $reportsColumnData['hosting_cost'];
                $tmpOperators['hosting_cost']['total'] = $total_avg_hosting_cost['sum'];
                $tmpOperators['hosting_cost']['t_mo_end'] = $total_avg_hosting_cost['T_Mo_End'];
                $tmpOperators['hosting_cost']['avg'] = $total_avg_hosting_cost['avg'];


                $total_avg_content = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['content'],$startColumnDateDisplay,$end_date);

                $tmpOperators['content']['dates'] = $reportsColumnData['content'];
                $tmpOperators['content']['total'] = $total_avg_content['sum'];
                $tmpOperators['content']['t_mo_end'] = $total_avg_content['T_Mo_End'];
                $tmpOperators['content']['avg'] = $total_avg_content['avg'];


                $total_avg_rnd = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['rnd'],$startColumnDateDisplay,$end_date);

                $tmpOperators['rnd']['dates'] = $reportsColumnData['rnd'];
                $tmpOperators['rnd']['total'] = $total_avg_rnd['sum'];
                $tmpOperators['rnd']['t_mo_end'] = $total_avg_rnd['T_Mo_End'];
                $tmpOperators['rnd']['avg'] = $total_avg_rnd['avg'];


                $total_avg_bd = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['bd'],$startColumnDateDisplay,$end_date);

                $tmpOperators['bd']['dates'] = $reportsColumnData['bd'];
                $tmpOperators['bd']['total'] = $total_avg_bd['sum'];
                $tmpOperators['bd']['t_mo_end'] = $total_avg_bd['T_Mo_End'];
                $tmpOperators['bd']['avg'] = $total_avg_bd['avg'];


                $total_avg_platform = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['platform'],$startColumnDateDisplay,$end_date);

                $tmpOperators['platform']['dates'] = $reportsColumnData['platform'];
                $tmpOperators['platform']['total'] = $total_avg_platform['sum'];
                $tmpOperators['platform']['t_mo_end'] = $total_avg_platform['T_Mo_End'];
                $tmpOperators['platform']['avg'] = $total_avg_platform['avg'];


                $total_avg_pnl = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['pnl'],$startColumnDateDisplay,$end_date);

                $tmpOperators['pnl']['dates'] = $reportsColumnData['pnl'];
                $tmpOperators['pnl']['total'] = $total_avg_pnl['sum'];
                $tmpOperators['pnl']['t_mo_end'] = $total_avg_pnl['T_Mo_End'];
                $tmpOperators['pnl']['avg'] = $total_avg_pnl['avg'];


                $total_avg_mo = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['mo'],$startColumnDateDisplay,$end_date);

                $tmpOperators['mo']['dates'] = $reportsColumnData['mo'];
                $tmpOperators['mo']['total'] = $total_avg_mo['sum'];
                $tmpOperators['mo']['t_mo_end'] = $total_avg_mo['T_Mo_End'];
                $tmpOperators['mo']['avg'] = $total_avg_mo['avg'];


                $total_avg_roi = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['roi'],$startColumnDateDisplay,$end_date);

                $tmpOperators['roi']['dates'] = $reportsColumnData['roi'];
                $tmpOperators['roi']['total'] = $total_avg_roi['sum'];
                $tmpOperators['roi']['t_mo_end'] = $total_avg_roi['T_Mo_End'];
                $tmpOperators['roi']['avg'] = $total_avg_roi['avg'];


                $total_avg_t_sub = UtilityReports::calculateTotalSubscribe($operator,$reportsColumnData['t_sub'],$startColumnDateDisplay,$end_date);

                $tmpOperators['t_sub']['dates']=$reportsColumnData['t_sub'];
                $tmpOperators['t_sub']['total']=$total_avg_t_sub['sum'];
                $tmpOperators['t_sub']['t_mo_end']=$total_avg_t_sub['T_Mo_End'];
                $tmpOperators['t_sub']['avg']=$total_avg_t_sub['avg'];


                $total_avg_t_bill = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['bill'],$startColumnDateDisplay,$end_date);

                $tmpOperators['bill']['dates']=$reportsColumnData['bill'];
                $tmpOperators['bill']['total']=0;
                $tmpOperators['bill']['t_mo_end']=0;
                $tmpOperators['bill']['avg']=$total_avg_t_bill['avg'];

                $total_avg_t_first_push = UtilityPercentage::PercentageDataAVG($operator,$reportsColumnData['first_push'],$startColumnDateDisplay,$end_date);

                $tmpOperators['first_push']['dates']=$reportsColumnData['first_push'];
                $tmpOperators['first_push']['total']=0;
                $tmpOperators['first_push']['t_mo_end']=0;
                $tmpOperators['first_push']['avg']=$total_avg_t_first_push['avg'];

                 $total_avg_t_daily_push = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['daily_push'],$startColumnDateDisplay,$end_date);

                $tmpOperators['daily_push']['dates']=$reportsColumnData['daily_push'];
                $tmpOperators['daily_push']['total']=0;
                $tmpOperators['daily_push']['t_mo_end']=0;
                $tmpOperators['daily_push']['avg']=$total_avg_t_daily_push['avg'];

                $total_avg_t_arpu7 = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['arpu7'],$startColumnDateDisplay,$end_date);

                $tmpOperators['arpu7']['dates']=$reportsColumnData['arpu7'];
                $tmpOperators['arpu7']['total']=0;
                $tmpOperators['arpu7']['t_mo_end']=0;
                $tmpOperators['arpu7']['avg']=$total_avg_t_arpu7['avg'];

                $tmpOperators['arpu7raw']['dates']=$reportsColumnData['arpu7Raw'];
                $tmpOperators['arpu7raw']['total']=0;
                $tmpOperators['arpu7raw']['t_mo_end']=0;
                $tmpOperators['arpu7raw']['avg']=0;

                $tmpOperators['arpu30raw']['dates']=$reportsColumnData['arpu30Raw'];
                $tmpOperators['arpu30raw']['total']=0;
                $tmpOperators['arpu30raw']['t_mo_end']=0;
                $tmpOperators['arpu30raw']['avg']=0;

                 $total_avg_t_usarpu7 = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['usarpu7'],$startColumnDateDisplay,$end_date);

                $tmpOperators['usarpu7']['dates']=$reportsColumnData['usarpu7'];
                $tmpOperators['usarpu7']['total']=0;
                $tmpOperators['usarpu7']['t_mo_end']=0;
                $tmpOperators['usarpu7']['avg']= $total_avg_t_usarpu7['avg'];


                $total_avg_t_arpu30 = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['arpu30'],$startColumnDateDisplay,$end_date);

                $tmpOperators['arpu30']['dates'] = $reportsColumnData['arpu30'];
                $tmpOperators['arpu30']['total'] = 0;
                $tmpOperators['arpu30']['t_mo_end'] = 0;
                $tmpOperators['arpu30']['avg'] = $total_avg_t_arpu30['avg'];

                 $total_avg_t_usarpu30 = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['usarpu30'],$startColumnDateDisplay,$end_date);

                $tmpOperators['usarpu30']['dates'] = $reportsColumnData['usarpu30'];
                $tmpOperators['usarpu30']['total'] = 0;
                $tmpOperators['usarpu30']['t_mo_end'] = 0;
                $tmpOperators['usarpu30']['avg'] =  $total_avg_t_usarpu30['avg'];

                $tmpOperators['mt_success']['dates'] = $reportsColumnData['mt_success'];
                $tmpOperators['mt_failed']['dates'] = $reportsColumnData['mt_failed'];

                $tmpOperators['fmt_success']['dates'] = $reportsColumnData['fmt_success'];
                $tmpOperators['fmt_failed']['dates'] = $reportsColumnData['fmt_failed'];


                // dd($tmpOperators);
                $sumemry[] = $tmpOperators;
            }
        }

        // Country Sum from Operator array
        $displayCountries = array();
        $SelectedCountries= array();
        $RowCountryData = array();

        if(!empty($sumemry))
        {
            foreach ($sumemry as $key => $sumemries) {
                $country_id = $sumemries['country']['id'];
                $SelectedCountries[$country_id] = $sumemries['country'];
                $displayCountries[$country_id][] = $sumemries;
            }
        }


        if(!empty($SelectedCountries))
        {
            foreach ($SelectedCountries as $key => $SelectedCountry)
            {
                $tempDataArr = array();
                $country_id = $SelectedCountry['id'];

                $dataRowSum = UtilityReports::pivotSummaryDataSum($displayCountries[$country_id]);

                $dataRowSum =UtilityReports::allSummeryPerCal($dataRowSum);

                $tempDataArr['country']=$SelectedCountry;
                $tempDataArr['month_string']=$month;
                $tempDataArr = array_merge($tempDataArr,$dataRowSum);
                $RowCountryData[] = $tempDataArr;
            }
        }

        $sumemry = $RowCountryData;
        $sumOfSummaryData = UtilityReports::pivotSummaryDataSum($sumemry);
        $sumOfSummaryData = UtilityReports::allSummeryPerCal($sumOfSummaryData);

        // dd($sumemry);
        // dd($sumOfSummaryData);
        return view('report.pivot.daily_country_pivotsummery', compact('no_of_days','sumemry','sumOfSummaryData','data'));
    }

    /*Daily pivot report by company*/
    public function DailyPivotReportCompany(Request $request)
    {
        $data = [];
        $data['ReportType'] = 'company';
        $data['Daily'] = 1;
        $user = Auth::user();
        $user_id = $user->id;
        $UserPivot=UserPivot::GetByUserId($user_id)->first();
        $data['report_type'] =json_decode($UserPivot->report_type);
        $data['report_column'] =json_decode($UserPivot->report_column);
        $data['date_range'] =json_decode($UserPivot->description);

        $CountryId = $req_CountryId = $request->country;
        $CompanyId = $req_CompanyId = $request->company;
        $filterOperator = $req_filterOperator = $request->operatorId;
        $Start_date = $req_Start_date = $request->from;
        $end_date =  $req_end_date = trim($request->to);

        /*If from is less than to*/
        if($end_date <= $Start_date)
        {
            $Start_date = $req_Start_date = trim($request->to);
            $end_date =  $req_end_date = $request->from;
        }

        $start_date = Carbon::now()->startOfMonth()->subDays(35)->format('Y-m-d');
        $startColumnDateDisplay = Carbon::now()->startOfMonth()->format('Y-m-d');
        $end_date = Carbon::now()->format('Y-m-d');
        $month = Carbon::now()->format('F Y');

        /* filter Search Section */

        $showAllOperator = true;

        if($request->filled('to') && $request->filled('from'))
        {
            $start_date_input = new Carbon($req_Start_date);
            $display_date_input = new Carbon($req_Start_date);
            $start_date = $start_date_input->subDays(35)->format('Y-m-d');
            $startColumnDateDisplay = $display_date_input->format('Y-m-d');
            $end_date = $req_end_date;
        }

        if($request->filled('company') && $req_CompanyId !="allcompany")
        {
            $companies = Company::Find($req_CompanyId);
            $Operators_company = array();

            if(!empty($companies))
            {
                $Operators_company = $companies->company_operators->pluck('operator_id')->toArray();
                $Operators = Operator::with('revenueshare')->Status(1)->GetOperatorByOperatorId($Operators_company)->get();
            }

            $showAllOperator = false;
        }

        if($showAllOperator)
        {
            $Operators = Operator::with('revenueshare')->Status(1)->get();
        }

        if($request->filled('country'))
        {
            $Operators = Operator::with('revenueshare')->Status(1)->GetOperatorByCountryId($req_CountryId)->get();
            $showAllOperator = false;
        }


        $Country = Country::all()->toArray();
        $companys = Company::get();
        $countries = array();
        if(!empty($Country))
        {
            foreach($Country as $CountryI)
            {
                $countries[$CountryI['id']]=$CountryI;
            }
        }

        $Company = Company::all()->toArray();
        $companies = array();

        if(!empty($Company))
        {
            foreach($Company as $companyI)
            {
                $companies[$companyI['id']] = $companyI;
            }
        }

        $CompanyOperators = CompanyOperators::all()->toArray();
        $com_operators = array();

        if(!empty($CompanyOperators))
        {
            foreach($CompanyOperators as $company_operator)
            {
                $operator_id = $company_operator['operator_id'];
                $company_id = $company_operator['company_id'];
                $com_operators[$operator_id] = $companies[$company_id];
            }
        }

        $Companys = Company::get();
        $operator_ids = [];

        if(!empty($Companys))
        {
            foreach($Companys as $company)
            {
                $com_opt_ids = $company->company_operators;
                $operator_ids = array_merge($operator_ids,$com_opt_ids->pluck('operator_id')->toArray());
            }
        }

        $staticOperators = $operator_ids;
        $sumemry = array();
        $pnl_report_summery = [];
        $report_summery = [];

        /* Admin Access All operator and Services */

        $user = Auth::user();
        $user_id = $user->id;
        $user_type = $user->type;
        $allowAllOperator = $user->WhowAccessAlOperator($user_type);

        if($allowAllOperator)
        {
            $reports = ReportsPnlsOperatorSummarizes::filterOperator($staticOperators)
            ->filterDateRange($start_date,$end_date)
            ->orderBy('country_id')
            ->orderBy('date', 'ASC')
            ->get()->toArray();

        }else{

            $UserOperatorServices =Session::get('userOperatorService');
            if(empty($UserOperatorServices))
            {
                dd("Please Contact to admin , add Operator to your account");
            }

            $arrayOperatorsIds = $UserOperatorServices['id_operators'];
            $arrayServicesIds = $UserOperatorServices['id_services'];

            $reports = PnlsUserOperatorSummarize::filterOperator($arrayOperatorsIds)
            ->filterDateRange($start_date,$end_date)
            ->orderBy('country_id')
            ->orderBy('date', 'ASC')
            ->get()->toArray();
        }


        $service_historys_result = ServiceHistory::FilterOperator(29)->filterDateRange($start_date,$end_date)->get();
        $service_historys = UtilityMobifone::ServiceRearrangeDate($service_historys_result);

        $reportsByIDs = $this->getReportsByOperator($reports);
        $datesIndividual =Utility::getRangeDates($startColumnDateDisplay,$end_date);
        $no_of_days = Utility::getRangeDateNo($datesIndividual);

        if(!empty($Operators))
        {
            foreach($Operators as $operator)
            {
                $tmpOperators=array();
                $operator_id = $operator->id_operator;
                $tmpOperators['operator'] = $operator;
                $country_id  = $operator->country_id;
                $contain_id = Arr::exists($countries, $country_id);
                $OperatorCountry = array();
                if($operator->id_operator == 29)
                {
                    $operator->service_historys = $service_historys;
                }

                if(!isset($com_operators[$operator_id]))
                {
                    // if The Operator not founds in that array
                    continue;
                }
                $tmpOperators['company'] = $com_operators[$operator_id];

                if($contain_id )
                {
                    $tmpOperators['country']=$countries[$country_id];
                    $OperatorCountry = $countries[$country_id];
                }

                $reportsColumnData = $this->getPivotReportsDateWise($operator,$no_of_days,$reportsByIDs,$OperatorCountry,$service_historys);
                $tmpOperators['month_string'] = $month;
                $tmpOperators['last_update'] = $reportsColumnData['last_update'];
                // dd($reportsColumnData);

                $total_avg_rev_usd = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['end_user_rev_usd'],$startColumnDateDisplay,$end_date);
                $tmpOperators['end_user_rev_usd']['dates'] = $reportsColumnData['end_user_rev_usd'];
                $tmpOperators['end_user_rev_usd']['total'] = $total_avg_rev_usd['sum'];
                $tmpOperators['end_user_rev_usd']['t_mo_end'] = $total_avg_rev_usd['T_Mo_End'];
                $tmpOperators['end_user_rev_usd']['avg'] = $total_avg_rev_usd['avg'];


                $total_avg_rev = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['end_user_rev'],$startColumnDateDisplay,$end_date);

                $tmpOperators['end_user_rev']['dates'] = $reportsColumnData['end_user_rev'];
                $tmpOperators['end_user_rev']['total'] = $total_avg_rev['sum'];
                $tmpOperators['end_user_rev']['t_mo_end'] = $total_avg_rev['T_Mo_End'];
                $tmpOperators['end_user_rev']['avg'] = $total_avg_rev['avg'];


                $total_avg_gros_rev_usd = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['gros_rev_usd'],$startColumnDateDisplay,$end_date);

                $tmpOperators['gros_rev_usd']['dates'] = $reportsColumnData['gros_rev_usd'];
                $tmpOperators['gros_rev_usd']['total'] = $total_avg_gros_rev_usd['sum'];
                $tmpOperators['gros_rev_usd']['t_mo_end'] = $total_avg_gros_rev_usd['T_Mo_End'];
                $tmpOperators['gros_rev_usd']['avg'] = $total_avg_gros_rev_usd['avg'];


                $total_avg_gros_rev = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['gros_rev'],$startColumnDateDisplay,$end_date);

                $tmpOperators['gros_rev']['dates'] = $reportsColumnData['gros_rev'];
                $tmpOperators['gros_rev']['total'] = $total_avg_gros_rev['sum'];
                $tmpOperators['gros_rev']['t_mo_end'] = $total_avg_gros_rev['T_Mo_End'];
                $tmpOperators['gros_rev']['avg'] = $total_avg_gros_rev['avg'];


                $total_avg_cost_campaign = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['cost_campaign'],$startColumnDateDisplay,$end_date);

                $tmpOperators['cost_campaign']['dates'] = $reportsColumnData['cost_campaign'];
                $tmpOperators['cost_campaign']['total'] = $total_avg_cost_campaign['sum'];
                $tmpOperators['cost_campaign']['t_mo_end'] = $total_avg_cost_campaign['T_Mo_End'];
                $tmpOperators['cost_campaign']['avg'] = $total_avg_cost_campaign['avg'];


                $total_avg_other_cost = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['other_cost'],$startColumnDateDisplay,$end_date);

                $tmpOperators['other_cost']['dates'] = $reportsColumnData['other_cost'];
                $tmpOperators['other_cost']['total'] = $total_avg_other_cost['sum'];
                $tmpOperators['other_cost']['t_mo_end'] = $total_avg_other_cost['T_Mo_End'];
                $tmpOperators['other_cost']['avg'] = $total_avg_other_cost['avg'];


                $total_avg_hosting_cost = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['hosting_cost'],$startColumnDateDisplay,$end_date);

                $tmpOperators['hosting_cost']['dates'] = $reportsColumnData['hosting_cost'];
                $tmpOperators['hosting_cost']['total'] = $total_avg_hosting_cost['sum'];
                $tmpOperators['hosting_cost']['t_mo_end'] = $total_avg_hosting_cost['T_Mo_End'];
                $tmpOperators['hosting_cost']['avg'] = $total_avg_hosting_cost['avg'];


                $total_avg_content = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['content'],$startColumnDateDisplay,$end_date);

                $tmpOperators['content']['dates'] = $reportsColumnData['content'];
                $tmpOperators['content']['total'] = $total_avg_content['sum'];
                $tmpOperators['content']['t_mo_end'] = $total_avg_content['T_Mo_End'];
                $tmpOperators['content']['avg'] = $total_avg_content['avg'];


                $total_avg_rnd = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['rnd'],$startColumnDateDisplay,$end_date);

                $tmpOperators['rnd']['dates'] = $reportsColumnData['rnd'];
                $tmpOperators['rnd']['total'] = $total_avg_rnd['sum'];
                $tmpOperators['rnd']['t_mo_end'] = $total_avg_rnd['T_Mo_End'];
                $tmpOperators['rnd']['avg'] = $total_avg_rnd['avg'];


                $total_avg_bd = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['bd'],$startColumnDateDisplay,$end_date);

                $tmpOperators['bd']['dates'] = $reportsColumnData['bd'];
                $tmpOperators['bd']['total'] = $total_avg_bd['sum'];
                $tmpOperators['bd']['t_mo_end'] = $total_avg_bd['T_Mo_End'];
                $tmpOperators['bd']['avg'] = $total_avg_bd['avg'];


                $total_avg_platform = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['platform'],$startColumnDateDisplay,$end_date);

                $tmpOperators['platform']['dates'] = $reportsColumnData['platform'];
                $tmpOperators['platform']['total'] = $total_avg_platform['sum'];
                $tmpOperators['platform']['t_mo_end'] = $total_avg_platform['T_Mo_End'];
                $tmpOperators['platform']['avg'] = $total_avg_platform['avg'];


                $total_avg_pnl = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['pnl'],$startColumnDateDisplay,$end_date);

                $tmpOperators['pnl']['dates'] = $reportsColumnData['pnl'];
                $tmpOperators['pnl']['total'] = $total_avg_pnl['sum'];
                $tmpOperators['pnl']['t_mo_end'] = $total_avg_pnl['T_Mo_End'];
                $tmpOperators['pnl']['avg'] = $total_avg_pnl['avg'];


                $total_avg_mo = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['mo'],$startColumnDateDisplay,$end_date);

                $tmpOperators['mo']['dates'] = $reportsColumnData['mo'];
                $tmpOperators['mo']['total'] = $total_avg_mo['sum'];
                $tmpOperators['mo']['t_mo_end'] = $total_avg_mo['T_Mo_End'];
                $tmpOperators['mo']['avg'] = $total_avg_mo['avg'];


                $total_avg_roi = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['roi'],$startColumnDateDisplay,$end_date);

                $tmpOperators['roi']['dates'] = $reportsColumnData['roi'];
                $tmpOperators['roi']['total'] = $total_avg_roi['sum'];
                $tmpOperators['roi']['t_mo_end'] = $total_avg_roi['T_Mo_End'];
                $tmpOperators['roi']['avg'] = $total_avg_roi['avg'];


                $total_avg_t_sub = UtilityReports::calculateTotalSubscribe($operator,$reportsColumnData['t_sub'],$startColumnDateDisplay,$end_date);

                $tmpOperators['t_sub']['dates']=$reportsColumnData['t_sub'];
                $tmpOperators['t_sub']['total']=$total_avg_t_sub['sum'];
                $tmpOperators['t_sub']['t_mo_end']=$total_avg_t_sub['T_Mo_End'];
                $tmpOperators['t_sub']['avg']=$total_avg_t_sub['avg'];


                $total_avg_t_bill = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['bill'],$startColumnDateDisplay,$end_date);

                $tmpOperators['bill']['dates']=$reportsColumnData['bill'];
                $tmpOperators['bill']['total']=0;
                $tmpOperators['bill']['t_mo_end']=0;
                $tmpOperators['bill']['avg']=$total_avg_t_bill['avg'];

                $total_avg_t_first_push = UtilityPercentage::PercentageDataAVG($operator,$reportsColumnData['first_push'],$startColumnDateDisplay,$end_date);

                $tmpOperators['first_push']['dates']=$reportsColumnData['first_push'];
                $tmpOperators['first_push']['total']=0;
                $tmpOperators['first_push']['t_mo_end']=0;
                $tmpOperators['first_push']['avg']=$total_avg_t_first_push['avg'];

                 $total_avg_t_daily_push = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['daily_push'],$startColumnDateDisplay,$end_date);

                $tmpOperators['daily_push']['dates']=$reportsColumnData['daily_push'];
                $tmpOperators['daily_push']['total']=0;
                $tmpOperators['daily_push']['t_mo_end']=0;
                $tmpOperators['daily_push']['avg']=$total_avg_t_daily_push['avg'];

                $total_avg_t_arpu7 = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['arpu7'],$startColumnDateDisplay,$end_date);

                $tmpOperators['arpu7']['dates']=$reportsColumnData['arpu7'];
                $tmpOperators['arpu7']['total']=0;
                $tmpOperators['arpu7']['t_mo_end']=0;
                $tmpOperators['arpu7']['avg']=$total_avg_t_arpu7['avg'];

                $tmpOperators['arpu7raw']['dates']=$reportsColumnData['arpu7Raw'];
                $tmpOperators['arpu7raw']['total']=0;
                $tmpOperators['arpu7raw']['t_mo_end']=0;
                $tmpOperators['arpu7raw']['avg']=0;

                $tmpOperators['arpu30raw']['dates']=$reportsColumnData['arpu30Raw'];
                $tmpOperators['arpu30raw']['total']=0;
                $tmpOperators['arpu30raw']['t_mo_end']=0;
                $tmpOperators['arpu30raw']['avg']=0;

                 $total_avg_t_usarpu7 = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['usarpu7'],$startColumnDateDisplay,$end_date);

                $tmpOperators['usarpu7']['dates']=$reportsColumnData['usarpu7'];
                $tmpOperators['usarpu7']['total']=0;
                $tmpOperators['usarpu7']['t_mo_end']=0;
                $tmpOperators['usarpu7']['avg']= $total_avg_t_usarpu7['avg'];


                $total_avg_t_arpu30 = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['arpu30'],$startColumnDateDisplay,$end_date);

                $tmpOperators['arpu30']['dates'] = $reportsColumnData['arpu30'];
                $tmpOperators['arpu30']['total'] = 0;
                $tmpOperators['arpu30']['t_mo_end'] = 0;
                $tmpOperators['arpu30']['avg'] = $total_avg_t_arpu30['avg'];

                 $total_avg_t_usarpu30 = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['usarpu30'],$startColumnDateDisplay,$end_date);

                $tmpOperators['usarpu30']['dates'] = $reportsColumnData['usarpu30'];
                $tmpOperators['usarpu30']['total'] = 0;
                $tmpOperators['usarpu30']['t_mo_end'] = 0;
                $tmpOperators['usarpu30']['avg'] =  $total_avg_t_usarpu30['avg'];

                $tmpOperators['mt_success']['dates'] = $reportsColumnData['mt_success'];
                $tmpOperators['mt_failed']['dates'] = $reportsColumnData['mt_failed'];

                $tmpOperators['fmt_success']['dates'] = $reportsColumnData['fmt_success'];
                $tmpOperators['fmt_failed']['dates'] = $reportsColumnData['fmt_failed'];


                // dd($tmpOperators);
                $sumemry[] = $tmpOperators;
            }
        }

        // Company Sum from Operator array
        $displayCompanies = array();
        $SelectedCompanies=array();
        $RowCompanyData = array();

        if(!empty($sumemry))
        {
            foreach ($sumemry as $key => $sumemries) {
                // dd($sumemries);
                $company_id = $sumemries['company']['id'];
                $SelectedCompanies[$company_id] = $sumemries['company'];
                $displayCompanies[$company_id][] = $sumemries;
            }
        }

        if(!empty($SelectedCompanies))
        {
            foreach ($SelectedCompanies as $key => $SelectedCompany)
            {
                $tempDataArr = array();
                $company_id = $SelectedCompany['id'];
                $dataRowSum = UtilityReports::pivotSummaryDataSum($displayCompanies[$company_id]);
                $dataRowSum = UtilityReports::allSummeryPerCal($dataRowSum);
                $tempDataArr['company'] = $SelectedCompany;
                $tempDataArr['month_string'] = $month;
                $tempDataArr = array_merge($tempDataArr,$dataRowSum);
                $RowCompanyData[] = $tempDataArr;
            }
        }

        $sumemry = $RowCompanyData;
        $sumOfSummaryData = UtilityReports::pivotSummaryDataSum($sumemry);
        $sumOfSummaryData = UtilityReports::allSummeryPerCal($sumOfSummaryData);

        // dd($sumemry);
        // dd($sumOfSummaryData);
        return view('report.pivot.daily_company_pivotsummery', compact('no_of_days','sumemry','sumOfSummaryData','data'));
    }

    /*Daily pivot report by account manager*/
    public function DailyPivotReportManager(Request $request)
    {
        $data = [];
        $data['ReportType'] = 'account_manager';
        $data['Daily'] = 1;
        $user = Auth::user();
        $user_id = $user->id;
        $UserPivot=UserPivot::GetByUserId($user_id)->first();
        $data['report_type'] =json_decode($UserPivot->report_type);
        $data['report_column'] =json_decode($UserPivot->report_column);
        $data['date_range'] =json_decode($UserPivot->description);

        $CountryId = $req_CountryId = $request->country;
        $CompanyId = $req_CompanyId = $request->company;
        $filterOperator = $req_filterOperator = $request->operatorId;
        $Start_date = $req_Start_date = $request->from;
        $end_date =  $req_end_date = trim($request->to);

        /*If from is less than to*/
        if($end_date <= $Start_date)
        {
            $Start_date = $req_Start_date = trim($request->to);
            $end_date =  $req_end_date = $request->from;
        }

        $start_date = Carbon::now()->startOfMonth()->subDays(35)->format('Y-m-d');
        $startColumnDateDisplay = Carbon::now()->startOfMonth()->format('Y-m-d');
        $end_date = Carbon::now()->format('Y-m-d');
        $month = Carbon::now()->format('F Y');

        /* filter Search Section */

        $showAllOperator = true;

        if($request->filled('to') && $request->filled('from'))
        {
            $start_date_input = new Carbon($req_Start_date);
            $display_date_input = new Carbon($req_Start_date);
            $start_date = $start_date_input->subDays(35)->format('Y-m-d');
            $startColumnDateDisplay = $display_date_input->format('Y-m-d');
            $end_date = $req_end_date;
        }

        if($request->filled('company') && $req_CompanyId !="allcompany")
        {
            $companies = Company::Find($req_CompanyId);
            $Operators_company = array();

            if(!empty($companies))
            {
                $Operators_company = $companies->company_operators->pluck('operator_id')->toArray();
                $Operators = Operator::with('revenueshare')->Status(1)->GetOperatorByOperatorId($Operators_company)->get();
            }

            $showAllOperator = false;
        }

        if($showAllOperator)
        {
            $Operators = Operator::with('revenueshare')->with('revenueshare')->Status(1)->get();
        }

        if($request->filled('country'))
        {
            $Operators = Operator::with('revenueshare')->with('revenueshare')->Status(1)->GetOperatorByCountryId($req_CountryId)->get();
            $showAllOperator = false;
        }


        $Country = Country::all()->toArray();
        $companys = Company::get();
        $countries = array();
        if(!empty($Country))
        {
            foreach($Country as $CountryI)
            {
                $countries[$CountryI['id']]=$CountryI;
            }
        }

        $Users = User::all()->toArray();
        $users = array();
        // dd($Users);
        if(!empty($Users))
        {
            foreach($Users as $userI)
            {
                if($userI['type'] == "Account Manager")
                $users[$userI['id']] = $userI;
            }
        }

        // dd($users);

        $UserOperators = UsersOperatorsServices::all()->toArray();
        $user_operators = array();

        if(!empty($UserOperators))
        {
            foreach($UserOperators as $key=>$User_operator)
            {
                $operator_id = $User_operator['id_operator'];
                $User_id = $User_operator['user_id'];
                if(!isset($users[$User_id]))
                {
                    // if The Operator not founds in that array
                    continue;
                }
                $user_operators[$operator_id] = $users[$User_id];
            }
        }

        $staticOperators = $Operators->pluck('id_operator')->toArray();
        $sumemry = array();
        $pnl_report_summery = [];
        $report_summery = [];

        /* Admin Access All operator and Services */

        $user = Auth::user();
        $user_id = $user->id;
        $user_type = $user->type;
        $allowAllOperator = $user->WhowAccessAlOperator($user_type);

        if($allowAllOperator)
        {
            $reports = ReportsPnlsOperatorSummarizes::filterOperator($staticOperators)
            ->filterDateRange($start_date,$end_date)
            ->orderBy('country_id')
            ->orderBy('date', 'ASC')
            ->get()->toArray();

        }else{

            $UserOperatorServices =Session::get('userOperatorService');
            if(empty($UserOperatorServices))
            {
                dd("Please Contact to admin , add Operator to your account");
            }

            $arrayOperatorsIds = $UserOperatorServices['id_operators'];
            $arrayServicesIds = $UserOperatorServices['id_services'];

            $reports = PnlsUserOperatorSummarize::filterOperator($arrayOperatorsIds)
            ->filterDateRange($start_date,$end_date)
            ->orderBy('country_id')
            ->orderBy('date', 'ASC')
            ->get()->toArray();
        }



        $service_historys_result = ServiceHistory::FilterOperator(29)->filterDateRange($start_date,$end_date)->get();
        $service_historys = UtilityMobifone::ServiceRearrangeDate($service_historys_result);

        $reportsByIDs = $this->getReportsByOperator($reports);
        $datesIndividual =Utility::getRangeDates($startColumnDateDisplay,$end_date);
        $no_of_days = Utility::getRangeDateNo($datesIndividual);

        if(!empty($Operators))
        {
            foreach($Operators as $operator)
            {
                $tmpOperators=array();
                $tmpOperators['operator'] = $operator;
                $country_id  = $operator->country_id;
                $operator_id = $operator->id_operator;
                $contain_id = Arr::exists($countries, $country_id);
                $OperatorCountry = array();
                if($operator->id_operator == 29)
                {
                    $operator->service_historys = $service_historys;
                }

                if(!isset($user_operators[$operator_id]))
                {
                    // if The Operator not founds in that array
                    continue;
                }

                $tmpOperators['account_manager'] = $user_operators[$operator_id];

                if($contain_id )
                {
                    $tmpOperators['country']=$countries[$country_id];
                    $OperatorCountry = $countries[$country_id];
                }

                $reportsColumnData = $this->getPivotReportsDateWise($operator,$no_of_days,$reportsByIDs,$OperatorCountry,$service_historys);
                $tmpOperators['month_string'] = $month;
                $tmpOperators['last_update'] = $reportsColumnData['last_update'];
                // dd($reportsColumnData);

                $total_avg_rev_usd = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['end_user_rev_usd'],$startColumnDateDisplay,$end_date);
                $tmpOperators['end_user_rev_usd']['dates'] = $reportsColumnData['end_user_rev_usd'];
                $tmpOperators['end_user_rev_usd']['total'] = $total_avg_rev_usd['sum'];
                $tmpOperators['end_user_rev_usd']['t_mo_end'] = $total_avg_rev_usd['T_Mo_End'];
                $tmpOperators['end_user_rev_usd']['avg'] = $total_avg_rev_usd['avg'];


                $total_avg_rev = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['end_user_rev'],$startColumnDateDisplay,$end_date);

                $tmpOperators['end_user_rev']['dates'] = $reportsColumnData['end_user_rev'];
                $tmpOperators['end_user_rev']['total'] = $total_avg_rev['sum'];
                $tmpOperators['end_user_rev']['t_mo_end'] = $total_avg_rev['T_Mo_End'];
                $tmpOperators['end_user_rev']['avg'] = $total_avg_rev['avg'];


                $total_avg_gros_rev_usd = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['gros_rev_usd'],$startColumnDateDisplay,$end_date);

                $tmpOperators['gros_rev_usd']['dates'] = $reportsColumnData['gros_rev_usd'];
                $tmpOperators['gros_rev_usd']['total'] = $total_avg_gros_rev_usd['sum'];
                $tmpOperators['gros_rev_usd']['t_mo_end'] = $total_avg_gros_rev_usd['T_Mo_End'];
                $tmpOperators['gros_rev_usd']['avg'] = $total_avg_gros_rev_usd['avg'];


                $total_avg_gros_rev = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['gros_rev'],$startColumnDateDisplay,$end_date);

                $tmpOperators['gros_rev']['dates'] = $reportsColumnData['gros_rev'];
                $tmpOperators['gros_rev']['total'] = $total_avg_gros_rev['sum'];
                $tmpOperators['gros_rev']['t_mo_end'] = $total_avg_gros_rev['T_Mo_End'];
                $tmpOperators['gros_rev']['avg'] = $total_avg_gros_rev['avg'];


                $total_avg_cost_campaign = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['cost_campaign'],$startColumnDateDisplay,$end_date);

                $tmpOperators['cost_campaign']['dates'] = $reportsColumnData['cost_campaign'];
                $tmpOperators['cost_campaign']['total'] = $total_avg_cost_campaign['sum'];
                $tmpOperators['cost_campaign']['t_mo_end'] = $total_avg_cost_campaign['T_Mo_End'];
                $tmpOperators['cost_campaign']['avg'] = $total_avg_cost_campaign['avg'];


                $total_avg_other_cost = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['other_cost'],$startColumnDateDisplay,$end_date);

                $tmpOperators['other_cost']['dates'] = $reportsColumnData['other_cost'];
                $tmpOperators['other_cost']['total'] = $total_avg_other_cost['sum'];
                $tmpOperators['other_cost']['t_mo_end'] = $total_avg_other_cost['T_Mo_End'];
                $tmpOperators['other_cost']['avg'] = $total_avg_other_cost['avg'];


                $total_avg_hosting_cost = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['hosting_cost'],$startColumnDateDisplay,$end_date);

                $tmpOperators['hosting_cost']['dates'] = $reportsColumnData['hosting_cost'];
                $tmpOperators['hosting_cost']['total'] = $total_avg_hosting_cost['sum'];
                $tmpOperators['hosting_cost']['t_mo_end'] = $total_avg_hosting_cost['T_Mo_End'];
                $tmpOperators['hosting_cost']['avg'] = $total_avg_hosting_cost['avg'];


                $total_avg_content = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['content'],$startColumnDateDisplay,$end_date);

                $tmpOperators['content']['dates'] = $reportsColumnData['content'];
                $tmpOperators['content']['total'] = $total_avg_content['sum'];
                $tmpOperators['content']['t_mo_end'] = $total_avg_content['T_Mo_End'];
                $tmpOperators['content']['avg'] = $total_avg_content['avg'];


                $total_avg_rnd = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['rnd'],$startColumnDateDisplay,$end_date);

                $tmpOperators['rnd']['dates'] = $reportsColumnData['rnd'];
                $tmpOperators['rnd']['total'] = $total_avg_rnd['sum'];
                $tmpOperators['rnd']['t_mo_end'] = $total_avg_rnd['T_Mo_End'];
                $tmpOperators['rnd']['avg'] = $total_avg_rnd['avg'];


                $total_avg_bd = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['bd'],$startColumnDateDisplay,$end_date);

                $tmpOperators['bd']['dates'] = $reportsColumnData['bd'];
                $tmpOperators['bd']['total'] = $total_avg_bd['sum'];
                $tmpOperators['bd']['t_mo_end'] = $total_avg_bd['T_Mo_End'];
                $tmpOperators['bd']['avg'] = $total_avg_bd['avg'];


                $total_avg_platform = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['platform'],$startColumnDateDisplay,$end_date);

                $tmpOperators['platform']['dates'] = $reportsColumnData['platform'];
                $tmpOperators['platform']['total'] = $total_avg_platform['sum'];
                $tmpOperators['platform']['t_mo_end'] = $total_avg_platform['T_Mo_End'];
                $tmpOperators['platform']['avg'] = $total_avg_platform['avg'];


                $total_avg_pnl = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['pnl'],$startColumnDateDisplay,$end_date);

                $tmpOperators['pnl']['dates'] = $reportsColumnData['pnl'];
                $tmpOperators['pnl']['total'] = $total_avg_pnl['sum'];
                $tmpOperators['pnl']['t_mo_end'] = $total_avg_pnl['T_Mo_End'];
                $tmpOperators['pnl']['avg'] = $total_avg_pnl['avg'];


                $total_avg_mo = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['mo'],$startColumnDateDisplay,$end_date);

                $tmpOperators['mo']['dates'] = $reportsColumnData['mo'];
                $tmpOperators['mo']['total'] = $total_avg_mo['sum'];
                $tmpOperators['mo']['t_mo_end'] = $total_avg_mo['T_Mo_End'];
                $tmpOperators['mo']['avg'] = $total_avg_mo['avg'];


                $total_avg_roi = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['roi'],$startColumnDateDisplay,$end_date);

                $tmpOperators['roi']['dates'] = $reportsColumnData['roi'];
                $tmpOperators['roi']['total'] = $total_avg_roi['sum'];
                $tmpOperators['roi']['t_mo_end'] = $total_avg_roi['T_Mo_End'];
                $tmpOperators['roi']['avg'] = $total_avg_roi['avg'];


                $total_avg_t_sub = UtilityReports::calculateTotalSubscribe($operator,$reportsColumnData['t_sub'],$startColumnDateDisplay,$end_date);

                $tmpOperators['t_sub']['dates']=$reportsColumnData['t_sub'];
                $tmpOperators['t_sub']['total']=$total_avg_t_sub['sum'];
                $tmpOperators['t_sub']['t_mo_end']=$total_avg_t_sub['T_Mo_End'];
                $tmpOperators['t_sub']['avg']=$total_avg_t_sub['avg'];


                $total_avg_t_bill = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['bill'],$startColumnDateDisplay,$end_date);

                $tmpOperators['bill']['dates']=$reportsColumnData['bill'];
                $tmpOperators['bill']['total']=0;
                $tmpOperators['bill']['t_mo_end']=0;
                $tmpOperators['bill']['avg']=$total_avg_t_bill['avg'];

                $total_avg_t_first_push = UtilityPercentage::PercentageDataAVG($operator,$reportsColumnData['first_push'],$startColumnDateDisplay,$end_date);

                $tmpOperators['first_push']['dates']=$reportsColumnData['first_push'];
                $tmpOperators['first_push']['total']=0;
                $tmpOperators['first_push']['t_mo_end']=0;
                $tmpOperators['first_push']['avg']=$total_avg_t_first_push['avg'];

                 $total_avg_t_daily_push = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['daily_push'],$startColumnDateDisplay,$end_date);

                $tmpOperators['daily_push']['dates']=$reportsColumnData['daily_push'];
                $tmpOperators['daily_push']['total']=0;
                $tmpOperators['daily_push']['t_mo_end']=0;
                $tmpOperators['daily_push']['avg']=$total_avg_t_daily_push['avg'];

                $total_avg_t_arpu7 = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['arpu7'],$startColumnDateDisplay,$end_date);

                $tmpOperators['arpu7']['dates']=$reportsColumnData['arpu7'];
                $tmpOperators['arpu7']['total']=0;
                $tmpOperators['arpu7']['t_mo_end']=0;
                $tmpOperators['arpu7']['avg']=$total_avg_t_arpu7['avg'];

                $tmpOperators['arpu7raw']['dates']=$reportsColumnData['arpu7Raw'];
                $tmpOperators['arpu7raw']['total']=0;
                $tmpOperators['arpu7raw']['t_mo_end']=0;
                $tmpOperators['arpu7raw']['avg']=0;

                $tmpOperators['arpu30raw']['dates']=$reportsColumnData['arpu30Raw'];
                $tmpOperators['arpu30raw']['total']=0;
                $tmpOperators['arpu30raw']['t_mo_end']=0;
                $tmpOperators['arpu30raw']['avg']=0;

                 $total_avg_t_usarpu7 = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['usarpu7'],$startColumnDateDisplay,$end_date);

                $tmpOperators['usarpu7']['dates']=$reportsColumnData['usarpu7'];
                $tmpOperators['usarpu7']['total']=0;
                $tmpOperators['usarpu7']['t_mo_end']=0;
                $tmpOperators['usarpu7']['avg']= $total_avg_t_usarpu7['avg'];


                $total_avg_t_arpu30 = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['arpu30'],$startColumnDateDisplay,$end_date);

                $tmpOperators['arpu30']['dates'] = $reportsColumnData['arpu30'];
                $tmpOperators['arpu30']['total'] = 0;
                $tmpOperators['arpu30']['t_mo_end'] = 0;
                $tmpOperators['arpu30']['avg'] = $total_avg_t_arpu30['avg'];

                 $total_avg_t_usarpu30 = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['usarpu30'],$startColumnDateDisplay,$end_date);

                $tmpOperators['usarpu30']['dates'] = $reportsColumnData['usarpu30'];
                $tmpOperators['usarpu30']['total'] = 0;
                $tmpOperators['usarpu30']['t_mo_end'] = 0;
                $tmpOperators['usarpu30']['avg'] =  $total_avg_t_usarpu30['avg'];

                $tmpOperators['mt_success']['dates'] = $reportsColumnData['mt_success'];
                $tmpOperators['mt_failed']['dates'] = $reportsColumnData['mt_failed'];

                $tmpOperators['fmt_success']['dates'] = $reportsColumnData['fmt_success'];
                $tmpOperators['fmt_failed']['dates'] = $reportsColumnData['fmt_failed'];


                // dd($tmpOperators);
                $sumemry[] = $tmpOperators;
            }
        }

        // Account Manager Sum from Operator array

        $displayAccountManagers = array();
        $SelectedAccountManagers = array();
        $RowUserData = array();

        if(!empty($sumemry))
        {
            foreach($sumemry as $key => $sumemries)
            {
                // dd($sumemries);
                $user_id = $sumemries['account_manager']['id'];
                $SelectedAccountManagers[$user_id] = $sumemries['account_manager'];
                $displayAccountManagers[$user_id][] = $sumemries;
            }
        }

        if(!empty($SelectedAccountManagers))
        {
            foreach ($SelectedAccountManagers as $key => $SelectedManagers)
            {
                // dd($SelectedManagers);
                $tempDataArr = array();
                $manager_id = $SelectedManagers['id'];
                $dataRowSum = UtilityReports::pivotSummaryDataSum($displayAccountManagers[$manager_id]);
                $dataRowSum = UtilityReports::allSummeryPerCal($dataRowSum);
                // dd($dataRowSum);
                $tempDataArr['account_manager']=$SelectedManagers;
                $tempDataArr['month_string']=$month;
                $tempDataArr = array_merge($tempDataArr,$dataRowSum);
                $RowUserData[] = $tempDataArr;
            }
        }

        $sumemry = $RowUserData;
        $sumOfSummaryData = UtilityReports::pivotSummaryDataSum($sumemry);
        $sumOfSummaryData = UtilityReports::allSummeryPerCal($sumOfSummaryData);

        // dd($sumemry);
        // dd($sumOfSummaryData);
        return view('report.pivot.daily_manager_pivotsummery', compact('no_of_days','sumemry','sumOfSummaryData','data'));
    }
}
