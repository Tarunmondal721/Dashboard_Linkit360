<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Session;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\report_summarize;
use App\Models\ReportSummeriseUsers;
use App\Models\ReportsPnlsOperatorSummarizes;
use App\Models\Operator;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use App\Models\Company;
use App\Models\Country;
use App\Models\User;
use App\Models\ReportsSummarizeDashbroads;
use App\Models\role_operators;
use App\Models\CompanyOperators;
use App\common\Utility;
use App\common\UtilityReports;
use Config;

class DashboardController extends Controller
{
	public function index(Request $request)
    {
        if(Auth::check())
        {
            $Countrys=[];
            $country_ids=[];
            /* get filtre request */
            /* filtre  start ***************************/
            $CountryId = $req_CountryId = $request->country;

            $CompanyId = $req_CompanyId = $request->company;
            $filterOperator = $req_filterOperator = $request->operatorId;
            $CountryFlag = true;
            /*only company filter  */
            if($request->filled('company') && !$request->filled('country') && !$request->filled('operatorId') && $req_CompanyId !="allcompany"){

                $countrys=[];
                $country_ids=[];
                $country_operator=[];
                $Company = Company::GetCompanyByCompanyId([$CompanyId])->first();
                if($Company != null)
                {
                    $operators=CompanyOperators::GetOperator($CompanyId)->get();
                    foreach($operators as $key=>$operator){
                        $country=$operator->Operator;
                        if(!empty($country) && isset($country[0])){
                            if(in_array($country[0]->country_id,$country_ids))
                               continue;
                            array_push($country_ids,$country[0]->country_id);
                            // array_push($country_operator,$country[0]);
                        }
                    }
                    $Countrys= Country::with('operators')->GetCountryByCountryId($country_ids)->get();
                    $Operators = Operator::Status(1)->GetOperatorByOperatorId($operators->pluck('operator_id'))->get();
                    $CountryFlag = false;
                }else{
                    $Operators = Operator::filterOperatorID(null)->get();
                    $CountryFlag = false;
                }
            }
             /* country filter */
            (!$request->filled('company')) ? $req_CompanyId = "allcompany" : false; 
            if($request->filled('company') && $request->filled('country') && !$request->filled('operatorId')){
                $Countrys[0]= Country::with('operators')->Find($CountryId);
                $data=[
                    'id'=>$req_CountryId,
                    'company'=>$req_CompanyId,
                ];
                $requestobj = new Request($data);
                $ReportControllerobj= new ReportController;
                $Operators=$ReportControllerobj->userFilterOperator($requestobj);
                $CountryFlag = false;
            }

            /*operater filter*/
            $country_ids=[];
            if($request->filled('operatorId')){
                $Operators = Operator::with('country')->Status(1)->GetOperatorByOperatorId($filterOperator)->get();
                // dd($Operators);
                foreach($Operators as $key=>$operator){
                    $country=$operator->country;
                    // dd($country);
                    // if(in_array($country->country_id,$country_ids))
                    // {
                        // array_push($countrys,$country[0]);
                        if(!in_array($country,$Countrys))
                        array_push($Countrys,$country);
                    // }
                    // array_push($country_ids,$country->country_id);
                    // dd($Countrys);
                }
                $CountryFlag = false;
            }
            if($CountryFlag)
            {
                $Countrys = Country::with('operators')->get();
                $Operators = Operator::Status(1)->get();
            }

            // dd($Countrys);
            $sumemry = array();
            $countries = array();

            $arrayOperatorsIds = $Operators->pluck('id_operator')->toArray();
            $DashboardDatas = ReportsSummarizeDashbroads::filterOperator($arrayOperatorsIds)->SumOfDashboardSummeryData()->get()->toArray();                                    
            /*if($CountryFlag)
            {
                $DashboardDatas = ReportsSummarizeDashbroads::filterNotOperator($OperatorsNotActive)->SumOfDashboardSummeryData()->get()->toArray();
            }

            if(!$CountryFlag)
            {
                $DashboardDatas = ReportsSummarizeDashbroads::filterOperator($Operators->pluck('id_operator'))->SumOfDashboardSummeryData()->get()->toArray();
            }*/

            // $sql_country = $DashboardDatas->toSql();
            // dd( $DashboardDatas);
            $start_date = Carbon::yesterday()->subDays(30)->format('Y-m-d');
            $end_date = Carbon::yesterday()->format('Y-m-d');
            $reports = ReportsPnlsOperatorSummarizes::filterOperator($arrayOperatorsIds)
                ->OperatorNotNull()
                ->filterDateRange($start_date,$end_date)
                ->SumOfRoiDataCounty()
                ->get()->toArray();

            $active_subs = report_summarize::filteroperator($arrayOperatorsIds)
                ->filterDate($start_date,$end_date)
                ->TotalCountry()
                ->get()->toArray();

            $reportsByCountryIDs = $this->getReportsByCountryID($reports);
            $active_subsByCountryIDs = $this->getReportsByCountryID($active_subs);    
            // dd($active_subsByCountryIDs);

            $CountryByOperatorsSum = array();

            if(!empty($DashboardDatas))
            {
                // dd($DashboardData);
                foreach($DashboardDatas as $DashboardData)
                {
                    $CountryByOperatorsSum[$DashboardData['country_id']]=$DashboardData;
                    if(!isset($reportsByCountryIDs[$DashboardData['country_id']]))
                    continue;    
                    $CountryByOperatorsSum[$DashboardData['country_id']]['pnl_details'] = $reportsByCountryIDs[$DashboardData['country_id']];
                    if(!isset($active_subsByCountryIDs[$DashboardData['country_id']]))
                    continue;
                    $CountryByOperatorsSum[$DashboardData['country_id']]['total'] = $active_subsByCountryIDs[$DashboardData['country_id']];
                }
            }

            // dd($CountryByOperatorsSum);
            /* filtre  end ***************************/

            // $Countrys = Country::all();
            $records = array();
            $DataArray = [];
            
            if(!empty($Countrys))
            {
                foreach($Countrys as $country)
                {
                    $country_id = $country->id;

                    if(isset($CountryByOperatorsSum[$country_id]))
                    {
                        $tmpOperators = array();
                        $tmpOperators['country'] = $country;
                        // $tmpOperators['operators'] = $country->operators->pluck('id_operator');
                        $tmpOperators['operators'] = $this->getFilterOperator($country_id,$CompanyId,$filterOperator);
                        $tmpOperators['reports'] = $CountryByOperatorsSum[$country_id];
                        $records[] = $tmpOperators;
                    }
                    // dd($records);
                }

                // dd($records);
                $curr_num_days = date('d', strtotime('-1 day'));
                $curr_tot_days = date('t');
                $num_days_remaining = $curr_tot_days - date('d');
                $prev_num_days = date('t', strtotime('-2 months'));
                $last_num_days = date('t', strtotime('- 1 month'));

                if(isset($records) && !empty($records)){
                    foreach($records as $key=>$rec){

                        $cvalue = $rec['reports'];
                        $pnl_details = isset($rec['reports']['pnl_details'])?$rec['reports']['pnl_details']:[];
                        $total = isset($rec['reports']['total'])?$rec['reports']['total']['total']:0;
                        // dd($total);
                        // dd($rec['reports']);
                        $DataArray[$key]['country'] = $rec['country'];
                        $DataArray[$key]['operator_count'] = count($rec['operators']);
                        $DataArray[$key]['current_avg_revenue_usd'] = $cvalue['current_revenue_usd']/$curr_num_days;
                        $DataArray[$key]['current_mo'] = $cvalue['current_mo'];
                        $DataArray[$key]['current_cost'] = $cvalue['current_cost'];
                        $DataArray[$key]['current_avg_mo'] = $cvalue['current_mo']/$curr_num_days;
                        $DataArray[$key]['current_pnl'] = $cvalue['current_pnl'];
                        $DataArray[$key]['current_avg_pnl'] = $cvalue['current_pnl']/$curr_num_days;
                        $DataArray[$key]['current_revenue'] = $cvalue['current_revenue'];
                        $DataArray[$key]['current_revenue_usd'] = $cvalue['current_revenue_usd'];
                        $current_avg_revenue = $cvalue['current_revenue']/$curr_num_days;
                        $current_avg_revenue_usd = $cvalue['current_revenue_usd']/$curr_num_days;

                        /*gross rev start*/
                        $DataArray[$key]['current_gross_revenue'] = $cvalue['current_gross_revenue'];
                        $DataArray[$key]['current_gross_revenue_usd'] = $cvalue['current_gross_revenue_usd'];
                        $DataArray[$key]['current_avg_gross_revenue_usd'] = $cvalue['current_gross_revenue_usd']/$curr_num_days;

                        $current_avg_gross_revenue = $cvalue['current_gross_revenue']/$curr_num_days;
                        $current_avg_gross_revenue_usd = $cvalue['current_gross_revenue_usd']/$curr_num_days;
                        /*gross rev end*/

                        $estimated_revenue = $cvalue['current_revenue'] + $current_avg_revenue*$num_days_remaining;
                        $estimated_revenue_usd = $cvalue['current_revenue_usd'] + $current_avg_revenue_usd*$num_days_remaining;

                        /*gross rev add*/
                        $estimated_gross_revenue = $cvalue['current_gross_revenue'] + $current_avg_gross_revenue*$num_days_remaining;
                        $estimated_gross_revenue_usd = $cvalue['current_gross_revenue_usd'] + $current_avg_gross_revenue_usd*$num_days_remaining;
                        /*gross rev end*/

                        $DataArray[$key]['estimated_revenue'] = $estimated_revenue;
                        $DataArray[$key]['estimated_revenue_usd'] = $estimated_revenue_usd;
                        $DataArray[$key]['estimated_avg_revenue_usd'] = $estimated_revenue_usd/$curr_tot_days;

                        /*gross rev start*/
                        $DataArray[$key]['estimated_gross_revenue'] = $estimated_gross_revenue;
                        $DataArray[$key]['estimated_gross_revenue_usd'] = $estimated_gross_revenue_usd;
                        $DataArray[$key]['estimated_avg_gross_revenue_usd'] = $estimated_gross_revenue_usd/$curr_tot_days;
                        /*gross rev end*/

                        $current_avg_mo = $cvalue['current_mo']/$curr_num_days;
                        $estimated_mo = $cvalue['current_mo']+$current_avg_mo*$num_days_remaining;
                        $estimated_avg_mo = $estimated_mo/$curr_tot_days;
                        $DataArray[$key]['estimated_mo'] = $estimated_mo;
                        $DataArray[$key]['estimated_avg_mo'] = $estimated_avg_mo;
                        $current_avg_pnl = $cvalue['current_pnl']/$curr_num_days;
                        $estimated_pnl = $cvalue['current_pnl']+$current_avg_pnl*$num_days_remaining;
                        $estimated_avg_pnl = $estimated_pnl/$curr_tot_days;
                        $current_cost = $cvalue['current_cost'];
                        $current_avg_cost = $current_cost/$curr_num_days;
                        $estimated_cost = $current_cost + $current_avg_cost*$num_days_remaining;
                        $DataArray[$key]['estimated_cost'] = $estimated_cost;
                        $DataArray[$key]['estimated_pnl'] = $estimated_pnl;
                        $DataArray[$key]['estimated_avg_pnl'] = $estimated_avg_pnl;

                        $DataArray[$key]['last_avg_revenue_usd'] = $cvalue['last_revenue_usd']/$last_num_days;
                        $DataArray[$key]['last_mo'] = $cvalue['last_mo'];
                        $DataArray[$key]['last_cost'] = $cvalue['last_cost'];
                        $DataArray[$key]['last_avg_mo'] = $cvalue['last_mo']/$last_num_days;
                        $DataArray[$key]['last_revenue'] = $cvalue['last_revenue'];
                        $DataArray[$key]['last_revenue_usd'] = $cvalue['last_revenue_usd'];

                        /* gross rev start */ 
                        $DataArray[$key]['last_gross_revenue'] = $cvalue['last_gross_revenue'];
                        $DataArray[$key]['last_gross_revenue_usd'] = $cvalue['last_gross_revenue_usd'];
                        $DataArray[$key]['last_avg_gross_revenue_usd'] = $cvalue['last_gross_revenue_usd']/$last_num_days;
                        /* gross rev end */

                        $DataArray[$key]['last_pnl'] = $cvalue['last_pnl'];
                        $DataArray[$key]['last_avg_pnl'] = $cvalue['last_pnl']/$last_num_days;
                        $DataArray[$key]['prev_avg_revenue_usd'] = $cvalue['prev_revenue_usd']/$prev_num_days;
                        $DataArray[$key]['prev_mo'] = $cvalue['prev_mo'];
                        $DataArray[$key]['prev_cost'] = $cvalue['prev_cost'];
                        $DataArray[$key]['prev_avg_mo'] = $cvalue['prev_mo']/$prev_num_days;
                        $DataArray[$key]['prev_pnl'] = $cvalue['prev_pnl'];
                        $DataArray[$key]['prev_avg_pnl'] = $cvalue['prev_pnl']/$prev_num_days;

                        $DataArray[$key]['prev_revenue'] = $cvalue['prev_revenue'];
                        $DataArray[$key]['prev_revenue_usd'] = $cvalue['prev_revenue_usd'];

                        /*Gross rev start*/
                        $DataArray[$key]['prev_gross_revenue'] = $cvalue['prev_gross_revenue'];
                        $DataArray[$key]['prev_gross_revenue_usd'] = $cvalue['prev_gross_revenue_usd'];
                        $DataArray[$key]['prev_avg_gross_revenue_usd'] = $cvalue['prev_gross_revenue_usd']/$prev_num_days;
                        /*Gross rev end*/

                        $DataArray[$key]['current_reg_sub'] = $pnl_details['reg'];
                        $DataArray[$key]['current_usd_rev_share'] = $pnl_details['share'];
                        $DataArray[$key]['cost_campaign'] = $pnl_details['cost_campaign'];
                        $DataArray[$key]['current_roi_mo'] = $pnl_details['mo'] + $pnl_details['reg'];
                        $DataArray[$key]['total'] = $total;
                        $current_usd_arpu = ($pnl_details['reg'] == 0) ? 0 : ($pnl_details['share'] / ($pnl_details['reg']+$total));
                        $current_price_mo = ($pnl_details['mo'] == 0) ? 0 : ($pnl_details['cost_campaign'] / ($pnl_details['mo']+$pnl_details['reg']));
                        $current_roi  =  ($current_usd_arpu == 0) ? 0 : ($current_price_mo / $current_usd_arpu);
                        // curr roi
                        $DataArray[$key]['currentMonthROI'] = $current_roi;

                        $current_avg_roi = $current_roi/$curr_num_days;
                        $estimated_roi = $current_roi + $current_avg_roi*$num_days_remaining;
                        //estimated roi
                        $DataArray[$key]['estimatedMonthROI'] = $estimated_roi;

                        $DataArray[$key]['last_reg_sub'] = $cvalue['last_reg_sub'];
                        $DataArray[$key]['last_usd_rev_share'] = $cvalue['last_usd_rev_share'];
                        $last_usd_arpu = ($cvalue['last_reg_sub'] == 0) ? 0 : ($cvalue['last_usd_rev_share'] / $cvalue['last_reg_sub']);
                        $last_price_mo = ($cvalue['last_mo'] == 0) ? 0 : ($cvalue['last_cost'] / $cvalue['last_mo']);
                        $last_roi  =  ($last_usd_arpu == 0) ? 0 : ($last_price_mo / $last_usd_arpu);
                        //last roi
                        $DataArray[$key]['lastMonthROI'] = $last_roi;

                        $DataArray[$key]['previous_reg_sub'] = $cvalue['previous_reg_sub'];
                        $DataArray[$key]['previous_usd_rev_share'] = $cvalue['previous_usd_rev_share'];
                        $previous_usd_arpu = ($cvalue['previous_reg_sub'] == 0) ? 0 : ($cvalue['previous_usd_rev_share'] / $cvalue['previous_reg_sub']);
                        $previous_price_mo = ($cvalue['prev_mo'] == 0) ? 0 : ($cvalue['prev_cost'] / $cvalue['prev_mo']);
                        $previous_roi  =  ($previous_usd_arpu == 0) ? 0 : ($previous_price_mo / $previous_usd_arpu);
                        //prev roi
                        $DataArray[$key]['previousMonthROI'] = $previous_roi;

                        // $last_30day_data = UtilityReports::last30DayData($pnl_details);
                        // dd($last_30day_data);
                        
                        /*usd_arpu = last 30 days share / (last 30 days reg + current day active subs)
                        price_mo = cost campaign / mo
                        current roi = price_mo / usd_arpu*/


                        $DataArray[$key]['current_cost'] = $cvalue['current_cost'];
                        $DataArray[$key]['last_cost'] = $cvalue['last_cost'];
                        $DataArray[$key]['prev_cost'] = $cvalue['prev_cost'];

                        $DataArray[$key]['current_price_mo'] = $cvalue['current_price_mo'];
                        $DataArray[$key]['estimated_price_mo']=$cvalue['estimated_price_mo'];
                        $DataArray[$key]['last_price_mo'] = $cvalue['last_price_mo'];
                        $DataArray[$key]['prev_price_mo'] = $cvalue['prev_price_mo'];

                        $DataArray[$key]['current_30_arpu'] = $cvalue['current_30_arpu'];
                        $DataArray[$key]['estimated_30_arpu'] = $cvalue['estimated_30_arpu'];
                        $DataArray[$key]['last_30_arpu'] = $cvalue['last_30_arpu'];
                        $DataArray[$key]['prev_30_arpu'] = $cvalue['prev_30_arpu'];

                        $last_update = $cvalue['updated_at'];
                        $last_update_timestamp = Carbon::parse($last_update);
                        $last_update_timestamp->setTimezone('Asia/Jakarta');
                        $last_update_show = $last_update_timestamp->format("Y-m-d H:i:s"). " Asia/Jakarta";

                        $DataArray[$key]['updated_at'] = $last_update_show;
                    }
                }
            }

            // dd($DataArray);
            $sumemry = $DataArray;
            $allDataSum = UtilityReports::DashboardAllDataSum($sumemry);
            // dd($allDataSum);

            return view('admin.country_dashboard', compact('sumemry','allDataSum'));

        }else{
            return redirect()->route('login');
        }
    }

    public function operatorDashboard(Request $request)
    {
        if(Auth::check())
        {
            /* filtre  start ***************************/
            $CountryId = $req_CountryId = $request->country;

            $CompanyId = $req_CompanyId = $request->company;
            $filterOperator = $req_filterOperator = $request->operatorId;
            $showAllOperator = true;
            /*only company filter  */
            // if($request->filled('company') && !$request->filled('country') && !$request->filled('operatorId') && $req_CompanyId !="allcompany"){


            //     $countrys=[];
            //     $id_operators=[];
            //     $Operators=[];
            //     $CompanyOperators=CompanyOperators::GetOperator($CompanyId)->get();

            //     foreach($CompanyOperators as $key=>$CompanyOperator){
            //         $operator=$CompanyOperator->Operator;
            //             // dd($operator);
            //         if(in_array($operator[0]->id_operator,$id_operators))
            //             continue;
            //         array_push($id_operators,$operator[0]->id_operator);
            //         array_push($Operators,$operator[0]);
            //     }
            //     $operaterIds = $id_operators;
            //     $CountryFlag = false;
            // }
            $sumemry = array();
            if($request->filled('company') && !$request->filled('country') && !$request->filled('operatorId') && $req_CompanyId !="allcompany"){
                $companies= Company::Find($req_CompanyId);
                $Operators_company =array();
                if(!empty($companies))
                {
                    $Operators_company = $companies->company_operators->pluck('operator_id')->toArray();
                    $Operators = Operator::with('country','services')->Status(1)->GetOperatorByOperatorId($Operators_company)->get();
                    $showAllOperator = false;
                }else{
                    $Operators = Operator::filterOperatorID(null)->get();
                    $showAllOperator = false;
                }
            }
            (!$request->filled('company')) ? $req_CompanyId = "allcompany" : false;
            if($req_CompanyId && $request->filled('country') && !$request->filled('operatorId'))
            {
                $data=[
                    'id'=>$req_CountryId,
                    'company'=>$req_CompanyId,
                ];
                $requestobj = new Request($data);
                $ReportControllerobj= new ReportController;
                $Operators=$ReportControllerobj->userFilterOperator($requestobj);
                $showAllOperator = false;
            }
            if($request->filled('operatorId'))
            {
               $Operators = Operator::with('country','services')->Status(1)->GetOperatorByOperatorId($filterOperator)->get();

               $showAllOperator = false;
            }
            if($showAllOperator)
            {
                $Operators = Operator::with('country','services')->Status(1)->get();
            }
            $operaterIds =   $Operators->pluck('id_operator');
            // dd($CountryFlag);
            $DashboardDatas = ReportsSummarizeDashbroads::filterOperator($operaterIds)->get()->toArray();
            // dd($DashboardDatas);

            $start_date = Carbon::yesterday()->subDays(30)->format('Y-m-d');
            $end_date = Carbon::yesterday()->format('Y-m-d');
            $reports = ReportsPnlsOperatorSummarizes::filteroperator($operaterIds)
                ->OperatorNotNull()
                ->filterDateRange($start_date,$end_date)
                ->SumOfRoiDataOperator()
                ->get()->toArray();

            $active_subs = report_summarize::filteroperator($operaterIds)
                ->filterDate($start_date,$end_date)
                ->TotalOperator()
                ->get()->toArray();

            $reportsByOperatorIDs = $this->getReportsByOperatorID($reports);
            $active_subsByOperatorIDs = $this->getReportsByOperatorID($active_subs);
            // dd($active_subsByOperatorIDs);
            $OperatorsSum = array();

            if(!empty($DashboardDatas))
            {
                foreach($DashboardDatas as $DashboardData)
                {
                    $OperatorsSum[$DashboardData['operator_id']] = $DashboardData;
                    if(!isset($reportsByOperatorIDs[$DashboardData['operator_id']]))
                    continue;    
                    $OperatorsSum[$DashboardData['operator_id']]['pnl_details'] = $reportsByOperatorIDs[$DashboardData['operator_id']];
                    if(!isset($active_subsByOperatorIDs[$DashboardData['operator_id']]))
                    continue;
                    $OperatorsSum[$DashboardData['operator_id']]['total'] = $active_subsByOperatorIDs[$DashboardData['operator_id']];
                }
            }
            // dd($OperatorsSum);

            // $Operators = Operator::all();
            $records = array();
            $DataArray = [];

            if(!empty($Operators))
            {
                foreach($Operators as $operator)
                {
                    $operator_id = $operator->id_operator;
                    // dd($operator_id);
                    if(isset($OperatorsSum[$operator_id]))
                    {
                        $tmpOperators = array();
                        $tmpOperators['country'] = $operator->country;
                        $tmpOperators['operator'] = $operator;
                        $tmpOperators['services'] = $operator->services->pluck('id_service');
                        $tmpOperators['reports'] = $OperatorsSum[$operator_id];
                        $records[] = $tmpOperators;
                    }
                }

                // dd($records);
                $curr_num_days = date('d', strtotime('-1 day'));
                $curr_tot_days = date('t');
                $num_days_remaining = $curr_tot_days - date('d');
                $prev_num_days = date('t', strtotime('-2 months'));
                $last_num_days = date('t', strtotime('- 1 month'));

                if(isset($records) && !empty($records)){
                    foreach($records as $key=>$record){

                        $cvalue = $record['reports'];

                        $pnl_details = isset($record['reports']['pnl_details'])?$record['reports']['pnl_details']:[];
                        $total = isset($record['reports']['total'])?$record['reports']['total']['total']:0;
                        // dd($pnl_details);
                        $DataArray[$key]['country'] = $record['country'];
                        $DataArray[$key]['operator'] = $record['operator'];
                        $DataArray[$key]['service'] = count($record['services']);

                        //current
                        $DataArray[$key]['current_avg_revenue_usd'] = $cvalue['current_revenue_usd']/$curr_num_days;
                        $DataArray[$key]['current_mo'] = $cvalue['current_mo'];
                        $DataArray[$key]['current_total_mo'] = $cvalue['current_total_mo'];
                        $DataArray[$key]['current_avg_mo'] = $cvalue['current_total_mo']/$curr_num_days;
                        $DataArray[$key]['current_pnl'] = $cvalue['current_pnl'];
                        $DataArray[$key]['current_avg_pnl'] = $cvalue['current_pnl']/$curr_num_days;
                        $DataArray[$key]['current_revenue'] = $cvalue['current_revenue'];
                        $DataArray[$key]['current_revenue_usd'] = $cvalue['current_revenue_usd'];
                        $current_avg_revenue = $cvalue['current_revenue']/$curr_num_days;
                        $current_avg_revenue_usd = $cvalue['current_revenue_usd']/$curr_num_days;
                        $estimated_revenue = $cvalue['current_revenue'] + $current_avg_revenue*$num_days_remaining;
                        $estimated_revenue_usd = $cvalue['current_revenue_usd'] + $current_avg_revenue_usd*$num_days_remaining;

                        /*gross rev start*/
                        $DataArray[$key]['current_gross_revenue'] = $cvalue['current_gross_revenue'];
                        $DataArray[$key]['current_gross_revenue_usd'] = $cvalue['current_gross_revenue_usd'];
                        $DataArray[$key]['current_avg_gross_revenue_usd'] = $cvalue['current_gross_revenue_usd']/$curr_num_days;

                        $current_avg_gross_revenue = $cvalue['current_gross_revenue']/$curr_num_days;
                        $current_avg_gross_revenue_usd = $cvalue['current_gross_revenue_usd']/$curr_num_days;
                        /*gross rev end*/

                        //estimate
                        
                        /*gross rev add*/
                        $estimated_gross_revenue = $cvalue['current_gross_revenue'] + $current_avg_gross_revenue*$num_days_remaining;
                        $estimated_gross_revenue_usd = $cvalue['current_gross_revenue_usd'] + $current_avg_gross_revenue_usd*$num_days_remaining;
                        /*gross rev end*/

                        $DataArray[$key]['estimated_revenue'] = $estimated_revenue;
                        $DataArray[$key]['estimated_revenue_usd'] = $estimated_revenue_usd;
                        $DataArray[$key]['estimated_avg_revenue_usd'] = $estimated_revenue_usd/$curr_tot_days;

                        /*gross rev start*/
                        $DataArray[$key]['estimated_gross_revenue'] = $estimated_gross_revenue;
                        $DataArray[$key]['estimated_gross_revenue_usd'] = $estimated_gross_revenue_usd;
                        $DataArray[$key]['estimated_avg_gross_revenue_usd'] = $estimated_gross_revenue_usd/$curr_tot_days;
                        /*gross rev end*/

                        $current_avg_mo = $cvalue['current_mo']/$curr_num_days;
                        $estimated_mo = $cvalue['current_mo']+$current_avg_mo*$num_days_remaining;
                        $estimated_avg_mo = $cvalue['estimated_total_mo']/$curr_tot_days;
                        $DataArray[$key]['estimated_mo'] = $estimated_mo;
                        $DataArray[$key]['estimated_total_mo'] = $cvalue['estimated_total_mo'];
                        $DataArray[$key]['estimated_avg_mo'] = $estimated_avg_mo;
                        $current_avg_pnl = $cvalue['current_pnl']/$curr_num_days;
                        $estimated_pnl = $cvalue['current_pnl']+$current_avg_pnl*$num_days_remaining;
                        $estimated_avg_pnl = $estimated_pnl/$curr_tot_days;
                        $current_cost = $cvalue['current_cost'];
                        $current_avg_cost = $current_cost/$curr_num_days;
                        $estimated_cost = $current_cost + $current_avg_cost*$num_days_remaining;
                        $DataArray[$key]['estimated_cost'] = $estimated_cost;
                        $DataArray[$key]['estimated_pnl'] = $estimated_pnl;
                        $DataArray[$key]['estimated_avg_pnl'] = $estimated_avg_pnl;


                        //last
                        $DataArray[$key]['last_avg_revenue_usd'] = $cvalue['last_revenue_usd']/$last_num_days;
                        $DataArray[$key]['last_mo'] = $cvalue['last_mo'];
                        $DataArray[$key]['last_total_mo'] = $cvalue['last_total_mo'];
                        $DataArray[$key]['last_avg_mo'] = $cvalue['last_total_mo']/$last_num_days;
                        $DataArray[$key]['last_revenue'] = $cvalue['last_revenue'];
                        $DataArray[$key]['last_revenue_usd'] = $cvalue['last_revenue_usd'];

                        /* gross rev start */ 
                        $DataArray[$key]['last_gross_revenue'] = $cvalue['last_gross_revenue'];
                        $DataArray[$key]['last_gross_revenue_usd'] = $cvalue['last_gross_revenue_usd'];
                        $DataArray[$key]['last_avg_gross_revenue_usd'] = $cvalue['last_gross_revenue_usd']/$last_num_days;
                        /* gross rev end */

                        $DataArray[$key]['last_pnl'] = $cvalue['last_pnl'];
                        $DataArray[$key]['last_avg_pnl'] = $cvalue['last_pnl']/$last_num_days;


                        //previous
                        $DataArray[$key]['prev_mo'] = $cvalue['prev_mo'];
                        $DataArray[$key]['prev_total_mo'] = $cvalue['prev_total_mo'];
                        $DataArray[$key]['prev_avg_mo'] = $cvalue['prev_total_mo']/$prev_num_days;
                        $DataArray[$key]['prev_pnl'] = $cvalue['prev_pnl'];
                        $DataArray[$key]['prev_avg_pnl'] = $cvalue['prev_pnl']/$prev_num_days;
                        $DataArray[$key]['prev_revenue'] = $cvalue['prev_revenue'];
                        $DataArray[$key]['prev_revenue_usd'] = $cvalue['prev_revenue_usd'];
                        $DataArray[$key]['prev_avg_revenue_usd'] = $cvalue['prev_revenue_usd']/$prev_num_days;

                        /*Gross rev start*/
                        $DataArray[$key]['prev_gross_revenue'] = $cvalue['prev_gross_revenue'];
                        $DataArray[$key]['prev_gross_revenue_usd'] = $cvalue['prev_gross_revenue_usd'];
                        $DataArray[$key]['prev_avg_gross_revenue_usd'] = $cvalue['prev_gross_revenue_usd']/$prev_num_days;
                        /*Gross rev end*/

                        $DataArray[$key]['current_reg_sub'] = $pnl_details['reg'];
                        $DataArray[$key]['current_usd_rev_share'] = $pnl_details['share'];
                        $DataArray[$key]['cost_campaign'] = $pnl_details['cost_campaign'];
                        $DataArray[$key]['current_roi_mo'] = $pnl_details['mo'] + $pnl_details['reg'];
                        $DataArray[$key]['total'] = $total;
                        $current_usd_arpu = ($pnl_details['reg'] == 0) ? 0 : ($pnl_details['share'] / ($pnl_details['reg']+$total));
                        $current_price_mo = ($pnl_details['mo'] == 0) ? 0 : ($pnl_details['cost_campaign'] / ($pnl_details['mo']+$pnl_details['reg']));
                        $current_roi  =  ($current_usd_arpu == 0) ? 0 : ($current_price_mo / $current_usd_arpu);
                        // curr roi
                        $DataArray[$key]['currentMonthROI'] = $current_roi;

                        $current_avg_roi = $current_roi/$curr_num_days;
                        $estimated_roi = $current_roi + $current_avg_roi*$num_days_remaining;
                        //estimated roi
                        $DataArray[$key]['estimatedMonthROI'] = $estimated_roi;

                        $DataArray[$key]['last_reg_sub'] = $cvalue['last_reg_sub'];
                        $DataArray[$key]['last_usd_rev_share'] = $cvalue['last_usd_rev_share'];
                        $last_usd_arpu = ($cvalue['last_reg_sub'] == 0) ? 0 : ($cvalue['last_usd_rev_share'] / $cvalue['last_reg_sub']);
                        $last_price_mo = ($cvalue['last_mo'] == 0) ? 0 : ($cvalue['last_cost'] / $cvalue['last_mo']);
                        $last_roi  =  ($last_usd_arpu == 0) ? 0 : ($last_price_mo / $last_usd_arpu);
                        //last roi
                        $DataArray[$key]['lastMonthROI'] = $last_roi;

                        $DataArray[$key]['previous_reg_sub'] = $cvalue['previous_reg_sub'];
                        $DataArray[$key]['previous_usd_rev_share'] = $cvalue['previous_usd_rev_share'];
                        $previous_usd_arpu = ($cvalue['previous_reg_sub'] == 0) ? 0 : ($cvalue['previous_usd_rev_share'] / $cvalue['previous_reg_sub']);
                        $previous_price_mo = ($cvalue['prev_mo'] == 0) ? 0 : ($cvalue['prev_cost'] / $cvalue['prev_mo']);
                        $previous_roi  =  ($previous_usd_arpu == 0) ? 0 : ($previous_price_mo / $previous_usd_arpu);
                        //prev roi
                        $DataArray[$key]['previousMonthROI'] = $previous_roi;

                        $DataArray[$key]['current_cost'] = $cvalue['current_cost'];
                        $DataArray[$key]['last_cost'] = $cvalue['last_cost'];
                        $DataArray[$key]['prev_cost'] = $cvalue['prev_cost'];

                        $DataArray[$key]['current_price_mo'] = $cvalue['current_price_mo'];
                        $DataArray[$key]['estimated_price_mo']=$cvalue['estimated_price_mo'];
                        $DataArray[$key]['last_price_mo'] = $cvalue['last_price_mo'];
                        $DataArray[$key]['prev_price_mo'] = $cvalue['prev_price_mo'];

                        $DataArray[$key]['current_30_arpu'] = $cvalue['current_30_arpu'];
                        $DataArray[$key]['estimated_30_arpu'] = $cvalue['estimated_30_arpu'];
                        $DataArray[$key]['last_30_arpu'] = $cvalue['last_30_arpu'];
                        $DataArray[$key]['prev_30_arpu'] = $cvalue['prev_30_arpu'];

                        $last_update = $cvalue['updated_at'];
                        $last_update_timestamp = Carbon::parse($last_update);
                        $last_update_timestamp->setTimezone('Asia/Jakarta');
                        $last_update_show = $last_update_timestamp->format("Y-m-d H:i:s"). " Asia/Jakarta";

                        $DataArray[$key]['updated_at'] = $last_update_show;
                    }
                }
            }

            // dd($DataArray);
            $sumemry = $DataArray;
            $allDataSum = UtilityReports::DashboardAllDataSum($sumemry);

            return view('admin.operator_dashboard', compact('sumemry','allDataSum'));

        }else{
            return redirect()->route('login');
        }
    }

    public function companyDashboard(Request $request)
    {
        if(Auth::check())
        {
            $Company = Company::all()->toArray();

            $companies = array();
            if(!empty($Company))
            {
                foreach($Company as $CompanyI)
                {
                    $companies[$CompanyI['id']] = $CompanyI;
                }
            }

            /* filtre  start ***************************/
            $CountryId = $req_CountryId = $request->country;
            $CompanyId = $req_CompanyId = $request->company;
            $BusinessType = $req_BusinessType = $request->business_type;
            $filterOperator = $req_filterOperator = $request->operatorId;
            $showAllOperator = true;
            $notCompany = true;
            if($request->filled('company') && !$request->filled('country') && !$request->filled('operatorId') && $req_CompanyId !="allcompany"){
                // $companies= Company::Find($req_CompanyId);
                // dd($companies);
                // $Operators_company =array();
                // if(!empty($companies))
                // {
                //     $Operators_company = $companies->company_operators->pluck('operator_id')->toArray();
                //     $Operators = Operator::Status(1)->GetOperatorByOperatorId($Operators_company)->get();
                // }
                $companies= Company::Find($req_CompanyId);
                $Operators_company =array();
                if(!empty($companies))
                {
                    $Operators_company = $companies->company_operators->pluck('operator_id')->toArray();
                    $Operators = Operator::with('country','services')->Status(1)->GetOperatorByOperatorId($Operators_company)->get();
                    $showAllOperator = false;
                }else{
                    $Operators = Operator::filterOperatorID(null)->get();
                    $showAllOperator = false;
                }
                // $DashboardDatas = ReportsSummarizeDashbroads::filterCompanyIDs([$CompanyId])->SumOfCompanyDashboardData()->get()->toArray();
                // $notCompany = false;
            }
            // if($request->filled('country') && !$request->filled('operatorId'))
            // {
            //     $Operators = Operator::with('country','services')->Status(1)->GetOperatorByCountryId($req_CountryId)->get();
            //     $showAllOperator = false;
            // }
            
            if($request->filled('company') && $request->filled('country') && !$request->filled('operatorId'))
            {
                $data=[
                    'id'=>$req_CountryId,
                    'company'=>$req_CompanyId,
                ];
                $requestobj = new Request($data);
                $ReportControllerobj= new ReportController;
                $Operators=$ReportControllerobj->userFilterOperator($requestobj);
                $showAllOperator = false;
            }

            if(isset($req_CompanyId) && !$request->filled('country') && $request->filled('business_type') && !$request->filled('operatorId')){
                $Countrys[0]= Country::with('operators')->Find($CountryId);
                $data=[
                    'country'=>$req_CountryId,
                    'company'=>$req_CompanyId,
                    'business_type'=>$req_BusinessType,
                ];
                $requestobj = new Request($data);
                $ReportControllerobj= new ReportController;
                $Operators=$ReportControllerobj->userFilterBusinessTypeOperator($requestobj);
                $CountryFlag = false;
                $showAllOperator = false;
            }

            if($request->filled('company') && $request->filled('country') && $request->filled('business_type') && !$request->filled('operatorId')){
                $Countrys[0]= Country::with('operators')->Find($CountryId);
                $data=[
                    'country'=>$req_CountryId,
                    'company'=>$req_CompanyId,
                    'business_type'=>$req_BusinessType,
                ];
                $requestobj = new Request($data);
                $ReportControllerobj= new ReportController;
                $Operators=$ReportControllerobj->userFilterBusinessTypeOperator($requestobj);
                $CountryFlag = false;
                $showAllOperator = false;
            }

            if($request->filled('operatorId'))
            {
               $Operators = Operator::with('country','services')->Status(1)->GetOperatorByOperatorId($filterOperator)->get();

               $showAllOperator = false;
            }
            if($showAllOperator)
            {
                $Operators = Operator::with('country','services')->Status(1)->get();
            }
            //  = Operator::Status(1)->get();

            /* filtre  end ***************************/
            if($notCompany){
                $DashboardDatas = ReportsSummarizeDashbroads::filterOperator($Operators->pluck('id_operator'))->SumOfCompanyDashboardData()->get()->toArray();
            }
            
            $compOperator = CompanyOperators::GetCompanyIds($Operators->pluck('id_operator'))->get()->groupBy('company_id');

            $reports = array();
            $active_subs = array();
            foreach ($compOperator as $key => $value) {
                $arrayOperatorsIds = $value->pluck('operator_id')->toArray();

                $start_date = Carbon::yesterday()->subDays(30)->format('Y-m-d');
                $end_date = Carbon::yesterday()->format('Y-m-d');
                $date = Carbon::now()->format('Y-m-d');
                $reports[$key] = ReportsPnlsOperatorSummarizes::filterOperator($arrayOperatorsIds)
                    ->OperatorNotNull()
                    ->filterDateRange($start_date,$end_date)
                    ->SumOfRoiDataCompany()
                    ->get()->toArray();

                /*$active_subs[$key] = report_summarize::filteroperator($arrayOperatorsIds)
                    ->filterDate($start_date,$end_date)
                    ->TotalCompany()
                    ->get()->toArray();*/
                $active_subs[$key] = ReportsPnlsOperatorSummarizes::filteroperator($arrayOperatorsIds)
                    ->where(['date' => $date])
                    ->TotalCompany()
                    ->get()->toArray();
            }
           
            $reportsByCompanyIDs = $reports;
            $active_subsByCoumpanyIDs = $active_subs;

            $CompanyByOperatorsSum = array();

            if(!empty($DashboardDatas))
            {
                // dd($DashboardData);
                foreach($DashboardDatas as $DashboardData)
                {
                    $CompanyByOperatorsSum[$DashboardData['company_id']] = $DashboardData;
                    if(!isset($reportsByCompanyIDs[$DashboardData['company_id']]))
                    continue;    
                    $CompanyByOperatorsSum[$DashboardData['company_id']]['pnl_details'] = $reportsByCompanyIDs[$DashboardData['company_id']];
                    if(!isset($active_subsByCoumpanyIDs[$DashboardData['company_id']]))
                    continue;
                    $CompanyByOperatorsSum[$DashboardData['company_id']]['total'] = $active_subsByCoumpanyIDs[$DashboardData['company_id']];
                }
            }

            $user = Auth::user();

            $user_id = $user->id;

            $user_type = $user->type;
            $allowAllOperator = $user->WhowAccessAlOperator($user_type);
            $DashboardOperators = array();
            if(!$allowAllOperator)
            {
                $current_start_date = Carbon::now()->startOfMonth()->format('Y-m-d');
                $current_end_date = Carbon::yesterday()->format('Y-m-d');

                $last_start_date = Carbon::now()->startOfMonth()->subMonth()->format('Y-m-d');
                $last_date = Carbon::now()->startOfMonth()->subMonthsNoOverflow();
                $last_end_date = $last_date->endOfMonth()->toDateString();

                $prev_start_date = Carbon::now()->startOfMonth()->subMonth()->subMonth()->format('Y-m-d');
                $firstDayofPreviousMonth = $last_date->startOfMonth()->subMonthsNoOverflow();
                $prev_end_date = $firstDayofPreviousMonth->endOfMonth()->toDateString();

                $UserOperatorServices =Session::get('userOperatorService');
                // dd($prev_start_date);

                $arrayOperatorsIds = $UserOperatorServices['id_operators'];

                $arrayServicesIds = $UserOperatorServices['id_services'];

                $compOperator = CompanyOperators::GetCompanyIds($arrayOperatorsIds)->get()->groupBy('company_id');
                foreach($compOperator as $key => $value){
                    $CompanyByOperatorsSum[$key]['current_revenue_usd']=0;
                    $CompanyByOperatorsSum[$key]['current_gross_revenue_usd']=0;
                    $CompanyByOperatorsSum[$key]['last_revenue_usd']=0;
                    $CompanyByOperatorsSum[$key]['last_gross_revenue_usd']=0;
                    $CompanyByOperatorsSum[$key]['prev_revenue_usd']=0;
                    $CompanyByOperatorsSum[$key]['prev_gross_revenue_usd']=0;
                    foreach ($value as $key1 => $value1) {
                        $company[$value1['operator_id']] = $key;
                    }
                }
                // dd($company);
                $userOperators = Operator::with('revenueshare','country')->filteroperator($arrayOperatorsIds)->get()->toArray();
                foreach ($userOperators as $key => $value) {
                    if (empty($value['revenueshare'])) {
                        $userOperators[$key]['revenueshare']['merchant_revenue_share'] = 100;
                    }
                }
                $userOperatorsIDs = $this->getReportsByOperatorID($userOperators);

                $currentuserreports = ReportSummeriseUsers::filteroperator($arrayOperatorsIds)
                    ->UserdataOperator()
                    ->filterDateRange($current_start_date,$current_end_date)
                    ->User($user_id)
                    ->orderBy('operator_id')
                    ->get()->toArray();

                $currentuserreportsByOperatorIDs = $this->getUserReportsByOperatorID($currentuserreports);

                if(!empty($currentuserreportsByOperatorIDs))
                {
                    foreach($currentuserreportsByOperatorIDs as $key => $value)
                    {
                        if($key == 16){
                            $CompanyByOperatorsSum[$company[$key]]['current_revenue_usd']+=($value['gros_rev']/1000)*$userOperatorsIDs[$key]['country']['usd'];
                            $CompanyByOperatorsSum[$company[$key]]['current_gross_revenue_usd']+=($value['gros_rev']/1000)*$userOperatorsIDs[$key]['country']['usd']*($userOperatorsIDs[$key]['revenueshare']['merchant_revenue_share']/100);
                        }else{
                            if(isset($company[$key])){
                                $CompanyByOperatorsSum[$company[$key]]['current_revenue_usd']+=$value['gros_rev']*$userOperatorsIDs[$key]['country']['usd'];
                                $CompanyByOperatorsSum[$company[$key]]['current_gross_revenue_usd']+=$value['gros_rev']*$userOperatorsIDs[$key]['country']['usd']*($userOperatorsIDs[$key]['revenueshare']['merchant_revenue_share']/100);
                                } 
                        }
                    }
                }
                // dd($CompanyByOperatorsSum);

                $prevuserreports = ReportSummeriseUsers::filteroperator($arrayOperatorsIds)
                    ->UserdataOperator()
                    ->filterDateRange($prev_start_date,$prev_end_date)
                    ->User($user_id)
                    ->orderBy('operator_id')
                    ->get()->toArray();

                $prevuserreportsByOperatorIDs = $this->getUserReportsByOperatorID($prevuserreports);

                if(!empty($prevuserreportsByOperatorIDs))
                {
                    foreach($prevuserreportsByOperatorIDs as $key => $value)
                    {
                        if($key == 16){
                            $CompanyByOperatorsSum[$company[$key]]['prev_revenue_usd']+=($value['gros_rev']/1000)*$userOperatorsIDs[$key]['country']['usd'];
                            $CompanyByOperatorsSum[$company[$key]]['prev_gross_revenue_usd']+=($value['gros_rev']/1000)*$userOperatorsIDs[$key]['country']['usd']*($userOperatorsIDs[$key]['revenueshare']['merchant_revenue_share']/100);
                        }else{
                            if(isset($company[$key])){
                                $CompanyByOperatorsSum[$company[$key]]['prev_revenue_usd']+=$value['gros_rev']*$userOperatorsIDs[$key]['country']['usd'];
                                $CompanyByOperatorsSum[$company[$key]]['prev_gross_revenue_usd']+=$value['gros_rev']*$userOperatorsIDs[$key]['country']['usd']*($userOperatorsIDs[$key]['revenueshare']['merchant_revenue_share']/100);
                            }
                        }
                    }
                }

                $lastuserreports = ReportSummeriseUsers::filteroperator($arrayOperatorsIds)
                    ->UserdataOperator()
                    ->filterDateRange($last_start_date,$last_end_date)
                    ->User($user_id)
                    ->orderBy('operator_id')
                    ->get()->toArray();

                $lastuserreportsByOperatorIDs = $this->getUserReportsByOperatorID($lastuserreports);

                if(!empty($lastuserreportsByOperatorIDs))
                {
                    foreach($lastuserreportsByOperatorIDs as $key => $value)
                    {
                        if($key == 16){
                            $CompanyByOperatorsSum[$company[$key]]['last_revenue_usd']+=($value['gros_rev']/1000)*$userOperatorsIDs[$key]['country']['usd'];
                            $CompanyByOperatorsSum[$company[$key]]['last_gross_revenue_usd']+=($value['gros_rev']/1000)*$userOperatorsIDs[$key]['country']['usd']*($userOperatorsIDs[$key]['revenueshare']['merchant_revenue_share']/100);
                        }else{
                            if(isset($company[$key])){
                                $CompanyByOperatorsSum[$company[$key]]['last_revenue_usd']+=$value['gros_rev']*$userOperatorsIDs[$key]['country']['usd'];
                                $CompanyByOperatorsSum[$company[$key]]['last_gross_revenue_usd']+=$value['gros_rev']*$userOperatorsIDs[$key]['country']['usd']*($userOperatorsIDs[$key]['revenueshare']['merchant_revenue_share']/100);
                            }
                        }
                    }
                }

                // dd($CompanyByOperatorsSum);
            } 
            // dd($Operators);
            // dd($DashboardDatas);
            $CompanyOperators = CompanyOperators::all()->toArray();
            $companies = array();

            if(!empty($CompanyOperators))
            {
                foreach($CompanyOperators as $company_operator)
                {
                    $company_id = $company_operator['company_id'];
                    // $companies[$company_id][] = $company_operator;
                }
            }

            $CountryOperators=CompanyOperators::GetCompanyIds($Operators->pluck('id_operator'))->get()->groupBy('company_id');
            // dd($CountryOperators);
            $Companys = Company::all();
            $records = array();
            $DataArray = [];

            if(!empty($Companys))
            {
                foreach($Companys as $company)
                {
                    $company_id = $company->id;
                    // dd($company_id);
                    if(isset($CompanyByOperatorsSum[$company_id]))
                    {
                        $tmpOperators = array();
                        $tmpOperators['company'] = $company;
                        $tmpOperators['reports'] = $CompanyByOperatorsSum[$company_id];
                        $records[] = $tmpOperators;
                    }
                }

                $curr_num_days = date('d', strtotime('-1 day'));
                $curr_tot_days = date('t');
                $num_days_remaining = $curr_tot_days - date('d') + 1;
                $prev_num_days = date('t', strtotime('-2 months'));
                $last_num_days = date('t', strtotime('- 1 month'));
                
                if(isset($records) && !empty($records)){
                    foreach($records as $key=>$record){

                        $cvalue = $record['reports'];
                        $DataArray[$key]['company'] = $record['company'];
                        $company_id = $record['company']['id'];
                        $pnl_details = isset($record['reports']['pnl_details'])?$record['reports']['pnl_details'][0]:[];
                        $total = isset($record['reports']['total'])?$record['reports']['total'][0]:[];
                        // $DataArray[$key]['operator_count'] = count($companies[$company_id]);
                        $DataArray[$key]['operator_count'] = isset($CountryOperators[$company_id])?count($CountryOperators[$company_id]):0;
                        //current
                        // if($company_id=='16'){dd($CountryOperators[$company_id]);}
                        $DataArray[$key]['current_avg_revenue_usd'] = (isset($cvalue['current_revenue_usd'])) ? $cvalue['current_revenue_usd']/$curr_num_days : 0;
                        $DataArray[$key]['current_mo'] = (isset($cvalue['current_mo'])) ? $cvalue['current_mo'] : 0;
                        $DataArray[$key]['current_total_mo'] = (isset($cvalue['current_total_mo'])) ? $cvalue['current_total_mo'] : 0;
                        $DataArray[$key]['current_avg_mo'] = (isset($cvalue['current_total_mo'])) ? $cvalue['current_total_mo']/$curr_num_days : 0;
                        $DataArray[$key]['current_pnl'] = (isset($cvalue['current_pnl'])) ? $cvalue['current_pnl'] : 0;
                        $DataArray[$key]['current_avg_pnl'] = (isset($cvalue['current_pnl'])) ? $cvalue['current_pnl']/$curr_num_days : 0;
                        $DataArray[$key]['current_revenue'] = (isset($cvalue['current_revenue'])) ? $cvalue['current_revenue'] : 0;
                        $DataArray[$key]['current_revenue_usd'] = (isset($cvalue['current_revenue_usd'])) ? $cvalue['current_revenue_usd'] : 0;
                        
                        /*gross rev start*/
                        $DataArray[$key]['current_gross_revenue'] = (isset($cvalue['current_gross_revenue'])) ? $cvalue['current_gross_revenue'] : 0;
                        $DataArray[$key]['current_gross_revenue_usd'] = (isset($cvalue['current_gross_revenue_usd'])) ? $cvalue['current_gross_revenue_usd'] : 0;
                        $DataArray[$key]['current_avg_gross_revenue_usd'] = (isset($cvalue['current_gross_revenue_usd'])) ? $cvalue['current_gross_revenue_usd']/$curr_num_days : 0;

                        $current_avg_gross_revenue = (isset($cvalue['current_gross_revenue'])) ? $cvalue['current_gross_revenue']/$curr_num_days : 0;
                        $current_avg_gross_revenue_usd = (isset($cvalue['current_gross_revenue_usd'])) ? $cvalue['current_gross_revenue_usd']/$curr_num_days : 0;
                        /*gross rev end*/

                        $current_avg_revenue = (isset($cvalue['current_revenue'])) ? $cvalue['current_revenue']/$curr_num_days : 0;
                        $current_avg_revenue_usd = (isset($cvalue['current_revenue_usd'])) ? $cvalue['current_revenue_usd']/$curr_num_days : 0;
                        $estimated_revenue = (isset($cvalue['current_revenue'])) ? $cvalue['current_revenue'] + $current_avg_revenue*$num_days_remaining : 0;
                        $estimated_revenue_usd = (isset($cvalue['current_revenue_usd'])) ? $cvalue['current_revenue_usd'] + $current_avg_revenue_usd*$num_days_remaining : 0;


                        //estimate

                        /*gross rev add*/
                        $estimated_gross_revenue = (isset($cvalue['current_gross_revenue'])) ? $cvalue['current_gross_revenue'] + $current_avg_gross_revenue*$num_days_remaining : 0;
                        $estimated_gross_revenue_usd = (isset($cvalue['current_gross_revenue_usd'])) ? $cvalue['current_gross_revenue_usd'] + $current_avg_gross_revenue_usd*$num_days_remaining : 0;
                        /*gross rev end*/

                        $DataArray[$key]['estimated_revenue'] = $estimated_revenue;
                        $DataArray[$key]['estimated_revenue_usd'] = $estimated_revenue_usd;
                        $DataArray[$key]['estimated_avg_revenue_usd'] = $estimated_revenue_usd/$curr_tot_days;

                        /*gross rev start*/
                        $DataArray[$key]['estimated_gross_revenue'] = $estimated_gross_revenue;
                        $DataArray[$key]['estimated_gross_revenue_usd'] = $estimated_gross_revenue_usd;
                        $DataArray[$key]['estimated_avg_gross_revenue_usd'] = $estimated_gross_revenue_usd/$curr_tot_days;
                        /*gross rev end*/

                        $current_avg_mo = (isset($cvalue['current_mo'])) ? $cvalue['current_mo']/$curr_num_days : 0;
                        $estimated_mo = (isset($cvalue['current_mo'])) ? $cvalue['current_mo']+$current_avg_mo*$num_days_remaining : 0;
                        $estimated_avg_mo = (isset($cvalue['estimated_total_mo'])) ? $cvalue['estimated_total_mo']/$curr_tot_days : 0;
                        $DataArray[$key]['estimated_mo'] = $estimated_mo;
                        $DataArray[$key]['estimated_total_mo'] = (isset($cvalue['estimated_total_mo'])) ? $cvalue['estimated_total_mo'] : 0;
                        $DataArray[$key]['estimated_avg_mo'] = $estimated_avg_mo;
                        $current_avg_pnl = (isset($cvalue['current_pnl'])) ? $cvalue['current_pnl']/$curr_num_days : 0;
                        $estimated_pnl = (isset($cvalue['current_pnl'])) ? $cvalue['current_pnl']+$current_avg_pnl*$num_days_remaining : 0;
                        $estimated_avg_pnl = $estimated_pnl/$curr_tot_days;
                        $current_cost = (isset($cvalue['current_cost'])) ? $cvalue['current_cost'] : 0;
                        $current_avg_cost = $current_cost/$curr_num_days;
                        $estimated_cost = $current_cost + $current_avg_cost*$num_days_remaining;
                        $DataArray[$key]['estimated_cost'] = $estimated_cost;
                        $DataArray[$key]['estimated_pnl'] = $estimated_pnl;
                        $DataArray[$key]['estimated_avg_pnl'] = $estimated_avg_pnl;


                        //last
                        $DataArray[$key]['last_avg_revenue_usd'] = (isset($cvalue['last_revenue_usd'])) ? $cvalue['last_revenue_usd']/$last_num_days : 0;
                        $DataArray[$key]['last_mo'] = (isset($cvalue['last_mo'])) ? $cvalue['last_mo'] : 0;
                        $DataArray[$key]['last_total_mo'] = (isset($cvalue['last_total_mo'])) ? $cvalue['last_total_mo'] : 0;
                        $DataArray[$key]['last_avg_mo'] = (isset($cvalue['last_total_mo'])) ? $cvalue['last_total_mo']/$last_num_days : 0;
                        $DataArray[$key]['last_revenue'] = (isset($cvalue['last_revenue'])) ? $cvalue['last_revenue'] : 0;
                        $DataArray[$key]['last_revenue_usd'] = (isset($cvalue['last_revenue_usd'])) ? $cvalue['last_revenue_usd'] : 0;

                        /* gross rev start */ 
                        $DataArray[$key]['last_gross_revenue'] = (isset($cvalue['last_gross_revenue'])) ? $cvalue['last_gross_revenue'] : 0;
                        $DataArray[$key]['last_gross_revenue_usd'] = (isset($cvalue['last_gross_revenue_usd'])) ? $cvalue['last_gross_revenue_usd'] : 0;
                        $DataArray[$key]['last_avg_gross_revenue_usd'] = (isset($cvalue['last_gross_revenue_usd'])) ? $cvalue['last_gross_revenue_usd']/$last_num_days : 0;
                        /* gross rev end */

                        $DataArray[$key]['last_pnl'] = (isset($cvalue['last_pnl'])) ? $cvalue['last_pnl'] : 0;
                        $DataArray[$key]['last_avg_pnl'] = (isset($cvalue['last_pnl'])) ? $cvalue['last_pnl']/$last_num_days : 0;


                        //previous
                        $DataArray[$key]['prev_mo'] = (isset($cvalue['prev_mo'])) ? $cvalue['prev_mo'] : 0;
                        $DataArray[$key]['prev_total_mo'] = (isset($cvalue['prev_total_mo'])) ? $cvalue['prev_total_mo'] : 0;
                        $DataArray[$key]['prev_avg_mo'] = (isset($cvalue['prev_total_mo'])) ? $cvalue['prev_total_mo']/$prev_num_days : 0;
                        $DataArray[$key]['prev_pnl'] = (isset($cvalue['prev_pnl'])) ? $cvalue['prev_pnl'] : 0;
                        $DataArray[$key]['prev_avg_pnl'] = (isset($cvalue['prev_pnl'])) ? $cvalue['prev_pnl']/$prev_num_days : 0;
                        $DataArray[$key]['prev_revenue'] = (isset($cvalue['prev_revenue'])) ? $cvalue['prev_revenue'] : 0;
                        $DataArray[$key]['prev_revenue_usd'] = (isset($cvalue['prev_revenue_usd'])) ? $cvalue['prev_revenue_usd'] : 0;
                        $DataArray[$key]['prev_avg_revenue_usd'] = (isset($cvalue['prev_revenue_usd'])) ? $cvalue['prev_revenue_usd']/$prev_num_days : 0;

                        /*Gross rev start*/
                        $DataArray[$key]['prev_gross_revenue'] = (isset($cvalue['prev_gross_revenue'])) ? $cvalue['prev_gross_revenue'] : 0;
                        $DataArray[$key]['prev_gross_revenue_usd'] = (isset($cvalue['prev_gross_revenue_usd'])) ? $cvalue['prev_gross_revenue_usd'] : 0;
                        $DataArray[$key]['prev_avg_gross_revenue_usd'] = (isset($cvalue['prev_gross_revenue_usd'])) ? $cvalue['prev_gross_revenue_usd']/$prev_num_days : 0;
                        /*Gross rev end*/

                        $DataArray[$key]['current_reg_sub'] = isset($pnl_details['reg']) ? $pnl_details['reg'] : 0;
                        $DataArray[$key]['current_usd_rev_share'] = isset($pnl_details['share']) ? $pnl_details['share'] :0;
                        $DataArray[$key]['cost_campaign'] = isset($total['cost_campaign']) ? $total['cost_campaign'] : 0;
                        $DataArray[$key]['current_roi_mo'] = isset($total['mo']) ? $total['mo'] : 0;
                        $DataArray[$key]['total'] = isset($total['active_subs']) ? $total['active_subs'] : 0;
                        if(isset($pnl_details['reg'])){
                            $current_usd_arpu = ($pnl_details['reg'] == 0) ? 0 : ($pnl_details['share'] / ($pnl_details['reg']+$total['active_subs']));
                        }else{
                            $current_usd_arpu = 0;
                        }

                        if(isset($total['mo'])){
                            $current_price_mo = ($total['mo'] == 0) ? 0 : ($total['cost_campaign'] / $total['mo']);
                        }else{
                            $current_price_mo = 0;
                        }
                        $current_roi  =  ($current_usd_arpu == 0) ? 0 : ($current_price_mo / $current_usd_arpu);
                        // curr roi
                        $DataArray[$key]['currentMonthROI'] = $current_roi;

                        $current_avg_roi = $current_roi/$curr_num_days;
                        // $estimated_roi = $current_roi + $current_avg_roi*$num_days_remaining;
                        $estimated_roi = $current_roi + $current_roi/$num_days_remaining;
                        //estimated roi
                        $DataArray[$key]['estimatedMonthROI'] = $estimated_roi;

                        $DataArray[$key]['last_reg_sub'] = (isset($cvalue['last_reg_sub'])) ? $cvalue['last_reg_sub'] : 0;
                        $DataArray[$key]['last_usd_rev_share'] = (isset($cvalue['last_usd_rev_share'])) ? $cvalue['last_usd_rev_share'] : 0;
                        $last_usd_arpu = (empty($cvalue['last_reg_sub']) || $cvalue['last_reg_sub'] == 0) ? 0 : ($cvalue['last_usd_rev_share'] / $cvalue['last_reg_sub']);
                        $last_price_mo = (empty($cvalue['last_mo']) || $cvalue['last_mo'] == 0) ? 0 : ($cvalue['last_cost'] / $cvalue['last_mo']);
                        $last_roi  =  ($last_usd_arpu == 0) ? 0 : ($last_price_mo / $last_usd_arpu);
                        //last roi
                        $DataArray[$key]['lastMonthROI'] = $last_roi;

                        $DataArray[$key]['previous_reg_sub'] = (isset($cvalue['previous_reg_sub'])) ? $cvalue['previous_reg_sub'] : 0;
                        $DataArray[$key]['previous_usd_rev_share'] = (isset($cvalue['previous_usd_rev_share'])) ? $cvalue['previous_usd_rev_share'] : 0;
                        $previous_usd_arpu = (empty($cvalue['previous_reg_sub']) || $cvalue['previous_reg_sub'] == 0) ? 0 : ($cvalue['previous_usd_rev_share'] / $cvalue['previous_reg_sub']);
                        $previous_price_mo = (empty($cvalue['prev_mo']) || $cvalue['prev_mo'] == 0) ? 0 : ($cvalue['prev_cost'] / $cvalue['prev_mo']);
                        $previous_roi  =  ($previous_usd_arpu == 0) ? 0 : ($previous_price_mo / $previous_usd_arpu);
                        //prev roi
                        $DataArray[$key]['previousMonthROI'] = $previous_roi;

                        $DataArray[$key]['current_cost'] = (isset($cvalue['current_cost'])) ? $cvalue['current_cost'] : 0;
                        $DataArray[$key]['last_cost'] = (isset($cvalue['last_cost'])) ? $cvalue['last_cost'] : 0;
                        $DataArray[$key]['prev_cost'] = (isset($cvalue['prev_cost'])) ? $cvalue['prev_cost'] : 0;

                        $DataArray[$key]['current_price_mo'] = (isset($cvalue['current_price_mo'])) ? $cvalue['current_price_mo'] : 0;
                        $DataArray[$key]['estimated_price_mo'] = (isset($cvalue['estimated_price_mo'])) ? $cvalue['estimated_price_mo'] : 0;
                        $DataArray[$key]['last_price_mo'] = (isset($cvalue['last_price_mo'])) ? $cvalue['last_price_mo'] : 0;
                        $DataArray[$key]['prev_price_mo'] = (isset($cvalue['prev_price_mo'])) ? $cvalue['prev_price_mo'] : 0;

                        $DataArray[$key]['current_30_arpu'] = $current_usd_arpu;
                        $DataArray[$key]['estimated_30_arpu'] = $current_usd_arpu;
                        $DataArray[$key]['last_30_arpu'] = $last_usd_arpu;
                        $DataArray[$key]['prev_30_arpu'] = $previous_usd_arpu;

                        if(isset($cvalue['operator_id']) && $cvalue['operator_id'] == 115){
                            $DataArray[$key]['current_pnl'] = $DataArray[$key]['current_revenue_usd'] * 0.06;
                            $DataArray[$key]['current_avg_pnl'] = $DataArray[$key]['current_pnl']/$curr_num_days;
                            $DataArray[$key]['estimated_pnl'] = $DataArray[$key]['estimated_revenue_usd'] * 0.06;
                            $DataArray[$key]['estimated_avg_pnl'] = $DataArray[$key]['estimated_pnl']/$num_days_remaining;
                            $DataArray[$key]['prev_pnl'] = $DataArray[$key]['last_revenue_usd'] * 0.06;
                            $DataArray[$key]['prev_avg_pnl'] = $DataArray[$key]['prev_pnl']/$prev_num_days;
                            $DataArray[$key]['last_pnl'] = $DataArray[$key]['last_revenue_usd'] * 0.06;
                            $DataArray[$key]['last_avg_pnl'] = $DataArray[$key]['last_pnl']/$last_num_days;
                        }

                        $last_update = (isset($cvalue['updated_at'])) ? $cvalue['updated_at'] : 0;
                        $last_update_timestamp = Carbon::parse($last_update);
                        $last_update_timestamp->setTimezone('Asia/Jakarta');
                        $last_update_show = $last_update_timestamp->format("Y-m-d H:i:s"). " Asia/Jakarta";

                        $DataArray[$key]['updated_at'] = $last_update_show;
                    }
                }
            }

            foreach ($DataArray as $key => $value) {

                $CurrentRevClass = $this->classPercentage($value['last_revenue_usd'],$value['current_revenue_usd']);
                $DataArray[$key]['current_revenue_usd_percentage'] = $CurrentRevClass['percentage'];
                $DataArray[$key]['current_revenue_usd_class'] = $CurrentRevClass['class'];
                $DataArray[$key]['current_revenue_usd_arrow'] = $CurrentRevClass['arrow'];

                $CurrentRevPercentage = $this->classPercentage($value['last_revenue_usd'],$value['estimated_revenue_usd']);
                $DataArray[$key]['estimated_revenue_usd_percentage'] = $CurrentRevPercentage['percentage'];
                $DataArray[$key]['estimated_revenue_usd_class'] = $CurrentRevPercentage['class'];
                $DataArray[$key]['estimated_revenue_usd_arrow'] = $CurrentRevPercentage['arrow'];

                $CurrentAvgRevPercentage = $this->classPercentage($value['last_avg_revenue_usd'],$value['estimated_avg_revenue_usd']);
                $DataArray[$key]['estimated_avg_revenue_usd_percentage'] = $CurrentAvgRevPercentage['percentage'];
                $DataArray[$key]['estimated_avg_revenue_usd_class'] = $CurrentAvgRevPercentage['class'];
                $DataArray[$key]['estimated_avg_revenue_usd_arrow'] = $CurrentAvgRevPercentage['arrow'];

                $CurrentGRevClass = $this->classPercentage($value['last_gross_revenue_usd'],$value['current_gross_revenue_usd']);
                $DataArray[$key]['current_gross_revenue_usd_percentage'] = $CurrentGRevClass['percentage'];
                $DataArray[$key]['current_gross_revenue_usd_class'] = $CurrentGRevClass['class'];
                $DataArray[$key]['current_gross_revenue_usd_arrow'] = $CurrentGRevClass['arrow'];

                $CurrentGRevPercentage = $this->classPercentage($value['last_gross_revenue_usd'],$value['estimated_gross_revenue_usd']);
                $DataArray[$key]['estimated_gross_revenue_usd_percentage'] = $CurrentGRevPercentage['percentage'];
                $DataArray[$key]['estimated_gross_revenue_usd_class'] = $CurrentGRevPercentage['class'];
                $DataArray[$key]['estimated_gross_revenue_usd_arrow'] = $CurrentGRevPercentage['arrow'];

                $CurrentAvgGRevPercentage = $this->classPercentage($value['last_avg_gross_revenue_usd'],$value['estimated_avg_gross_revenue_usd']);
                $DataArray[$key]['estimated_avg_gross_revenue_usd_percentage'] = $CurrentAvgGRevPercentage['percentage'];
                $DataArray[$key]['estimated_avg_gross_revenue_usd_class'] = $CurrentAvgGRevPercentage['class'];
                $DataArray[$key]['estimated_avg_gross_revenue_usd_arrow'] = $CurrentAvgGRevPercentage['arrow'];

                $CurrentMOClass = $this->classPercentage($value['last_total_mo'],$value['current_total_mo']);
                $DataArray[$key]['current_total_mo_percentage'] = $CurrentMOClass['percentage'];
                $DataArray[$key]['current_total_mo_class'] = $CurrentMOClass['class'];
                $DataArray[$key]['current_total_mo_arrow'] = $CurrentMOClass['arrow'];

                $CurrentRegClass = $this->classPercentage($value['last_mo'],$value['current_mo']);
                $DataArray[$key]['current_mo_percentage'] = $CurrentRegClass['percentage'];
                $DataArray[$key]['current_mo_class'] = $CurrentRegClass['class'];
                $DataArray[$key]['current_mo_arrow'] = $CurrentRegClass['arrow'];

                $CurrentMOPercentage = $this->classPercentage($value['last_total_mo'],$value['estimated_total_mo']);
                $DataArray[$key]['estimated_total_mo_percentage'] = $CurrentMOPercentage['percentage'];
                $DataArray[$key]['estimated_total_mo_class'] = $CurrentMOPercentage['class'];
                $DataArray[$key]['estimated_total_mo_arrow'] = $CurrentMOPercentage['arrow'];

                $CurrentAvgMOPercentage = $this->classPercentage($value['last_avg_mo'],$value['estimated_avg_mo']);
                $DataArray[$key]['estimated_avg_mo_percentage'] = $CurrentAvgMOPercentage['percentage'];
                $DataArray[$key]['estimated_avg_mo_class'] = $CurrentAvgMOPercentage['class'];
                $DataArray[$key]['estimated_avg_mo_arrow'] = $CurrentAvgMOPercentage['arrow'];

                $CurrentRegPercentage = $this->classPercentage($value['last_mo'],$value['estimated_mo']);
                $DataArray[$key]['estimated_mo_percentage'] = $CurrentRegPercentage['percentage'];
                $DataArray[$key]['estimated_mo_class'] = $CurrentRegPercentage['class'];
                $DataArray[$key]['estimated_mo_arrow'] = $CurrentRegPercentage['arrow'];

                $CurrentCostClass = $this->classPercentage($value['last_cost'],$value['current_cost']);
                $DataArray[$key]['current_cost_percentage'] = $CurrentCostClass['percentage'];
                $DataArray[$key]['current_cost_class'] = $CurrentCostClass['class'];
                $DataArray[$key]['current_cost_arrow'] = $CurrentCostClass['arrow'];

                $CurrentCostPercentage = $this->classPercentage($value['last_cost'],$value['estimated_cost']);
                $DataArray[$key]['estimated_cost_percentage'] = $CurrentCostPercentage['percentage'];
                $DataArray[$key]['estimated_cost_class'] = $CurrentCostPercentage['class'];
                $DataArray[$key]['estimated_cost_arrow'] = $CurrentCostPercentage['arrow'];

                $CurrentPriceMOClass = $this->classPercentage($value['last_price_mo'],$value['current_price_mo']);
                $DataArray[$key]['current_price_mo_percentage'] = $CurrentPriceMOClass['percentage'];
                $DataArray[$key]['current_price_mo_class'] = $CurrentPriceMOClass['class'];
                $DataArray[$key]['current_price_mo_arrow'] = $CurrentPriceMOClass['arrow'];

                $CurrentPriceMOPercentage = $this->classPercentage($value['last_price_mo'],$value['estimated_price_mo']);
                $DataArray[$key]['estimated_price_mo_percentage'] = $CurrentPriceMOPercentage['percentage'];
                $DataArray[$key]['estimated_price_mo_class'] = $CurrentPriceMOPercentage['class'];
                $DataArray[$key]['estimated_price_mo_arrow'] = $CurrentPriceMOPercentage['arrow'];

                $CurrentROIClass = $this->classPercentage($value['lastMonthROI'],$value['currentMonthROI']);
                $DataArray[$key]['currentMonthROI_percentage'] = $CurrentROIClass['percentage'];
                $DataArray[$key]['currentMonthROI_class'] = $CurrentROIClass['class'];
                $DataArray[$key]['currentMonthROI_arrow'] = $CurrentROIClass['arrow'];

                $CurrentROIPercentage = $this->classPercentage($value['lastMonthROI'],$value['estimatedMonthROI']);
                $DataArray[$key]['estimatedMonthROI_percentage'] = $CurrentROIPercentage['percentage'];
                $DataArray[$key]['estimatedMonthROI_class'] = $CurrentROIPercentage['class'];
                $DataArray[$key]['estimatedMonthROI_arrow'] = $CurrentROIPercentage['arrow'];

                $CurrentArpuClass = $this->classPercentage($value['last_30_arpu'],$value['current_30_arpu']);
                $DataArray[$key]['current_30_arpu_percentage'] = $CurrentArpuClass['percentage'];
                $DataArray[$key]['current_30_arpu_class'] = $CurrentArpuClass['class'];
                $DataArray[$key]['current_30_arpu_arrow'] = $CurrentArpuClass['arrow'];

                $CurrenArpuPercentage = $this->classPercentage($value['last_30_arpu'],$value['estimated_30_arpu']);
                $DataArray[$key]['estimated_30_arpu_percentage'] = $CurrenArpuPercentage['percentage'];
                $DataArray[$key]['estimated_30_arpu_class'] = $CurrenArpuPercentage['class'];
                $DataArray[$key]['estimated_30_arpu_arrow'] = $CurrenArpuPercentage['arrow'];

                $CurrentPnlClass = $this->classPercentage($value['last_pnl'],$value['current_pnl']);
                $DataArray[$key]['current_pnl_percentage'] = $CurrentPnlClass['percentage'];
                $DataArray[$key]['current_pnl_class'] = $CurrentPnlClass['class'];
                $DataArray[$key]['current_pnl_arrow'] = $CurrentPnlClass['arrow'];

                $CurrentPnlPercentage = $this->classPercentage($value['last_pnl'],$value['estimated_pnl']);
                $DataArray[$key]['estimated_pnl_percentage'] = $CurrentPnlPercentage['percentage'];
                $DataArray[$key]['estimated_pnl_class'] = $CurrentPnlPercentage['class'];
                $DataArray[$key]['estimated_pnl_arrow'] = $CurrentPnlPercentage['arrow'];

                $CurrentAvgPnlPercentage = $this->classPercentage($value['last_avg_pnl'],$value['estimated_avg_pnl']);
                $DataArray[$key]['estimated_avg_pnl_percentage'] = $CurrentAvgPnlPercentage['percentage'];
                $DataArray[$key]['estimated_avg_pnl_class'] = $CurrentAvgPnlPercentage['class'];
                $DataArray[$key]['estimated_avg_pnl_arrow'] = $CurrentAvgPnlPercentage['arrow'];


                $LastRevPercentage = $this->classPercentage($value['prev_revenue_usd'],$value['last_revenue_usd']);
                $DataArray[$key]['last_revenue_usd_percentage'] = $LastRevPercentage['percentage'];
                $DataArray[$key]['last_revenue_usd_class'] = $LastRevPercentage['class'];
                $DataArray[$key]['last_revenue_usd_arrow'] = $LastRevPercentage['arrow'];

                $LastAvgRevPercentage = $this->classPercentage($value['prev_avg_revenue_usd'],$value['last_avg_revenue_usd']);
                $DataArray[$key]['last_avg_revenue_usd_percentage'] = $LastAvgRevPercentage['percentage'];
                $DataArray[$key]['last_avg_revenue_usd_class'] = $LastAvgRevPercentage['class'];
                $DataArray[$key]['last_avg_revenue_usd_arrow'] = $LastAvgRevPercentage['arrow'];

                $LastGRevPercentage = $this->classPercentage($value['prev_gross_revenue_usd'],$value['last_gross_revenue_usd']);
                $DataArray[$key]['last_gross_revenue_usd_percentage'] = $LastGRevPercentage['percentage'];
                $DataArray[$key]['last_gross_revenue_usd_class'] = $LastGRevPercentage['class'];
                $DataArray[$key]['last_gross_revenue_usd_arrow'] = $LastGRevPercentage['arrow'];

                $LastAvgGRevPercentage = $this->classPercentage($value['prev_avg_gross_revenue_usd'],$value['last_avg_gross_revenue_usd']);
                $DataArray[$key]['last_avg_gross_revenue_usd_percentage'] = $LastAvgGRevPercentage['percentage'];
                $DataArray[$key]['last_avg_gross_revenue_usd_class'] = $LastAvgGRevPercentage['class'];
                $DataArray[$key]['last_avg_gross_revenue_usd_arrow'] = $LastAvgGRevPercentage['arrow'];

                $LastMOPercentage = $this->classPercentage($value['prev_total_mo'],$value['last_total_mo']);
                $DataArray[$key]['last_total_mo_percentage'] = $LastMOPercentage['percentage'];
                $DataArray[$key]['last_total_mo_class'] = $LastMOPercentage['class'];
                $DataArray[$key]['last_total_mo_arrow'] = $LastMOPercentage['arrow'];

                $LastAvgMOPercentage = $this->classPercentage($value['prev_avg_mo'],$value['last_avg_mo']);
                $DataArray[$key]['last_avg_mo_percentage'] = $LastAvgMOPercentage['percentage'];
                $DataArray[$key]['last_avg_mo_class'] = $LastAvgMOPercentage['class'];
                $DataArray[$key]['last_avg_mo_arrow'] = $LastAvgMOPercentage['arrow'];

                $LastRegPercentage = $this->classPercentage($value['prev_mo'],$value['last_mo']);
                $DataArray[$key]['last_mo_percentage'] = $LastRegPercentage['percentage'];
                $DataArray[$key]['last_mo_class'] = $LastRegPercentage['class'];
                $DataArray[$key]['last_mo_arrow'] = $LastRegPercentage['arrow'];

                $LastCostPercentage = $this->classPercentage($value['prev_cost'],$value['last_cost']);
                $DataArray[$key]['last_cost_percentage'] = $LastCostPercentage['percentage'];
                $DataArray[$key]['last_cost_class'] = $LastCostPercentage['class'];
                $DataArray[$key]['last_cost_arrow'] = $LastCostPercentage['arrow'];

                $LastPriceMOPercentage = $this->classPercentage($value['prev_price_mo'],$value['last_price_mo']);
                $DataArray[$key]['last_price_mo_percentage'] = $LastPriceMOPercentage['percentage'];
                $DataArray[$key]['last_price_mo_class'] = $LastPriceMOPercentage['class'];
                $DataArray[$key]['last_price_mo_arrow'] = $LastPriceMOPercentage['arrow'];

                $LastROIPercentage = $this->classPercentage($value['previousMonthROI'],$value['lastMonthROI']);
                $DataArray[$key]['lastMonthROI_percentage'] = $LastROIPercentage['percentage'];
                $DataArray[$key]['lastMonthROI_class'] = $LastROIPercentage['class'];
                $DataArray[$key]['lastMonthROI_arrow'] = $LastROIPercentage['arrow'];

                $LastArpuPercentage = $this->classPercentage($value['prev_30_arpu'],$value['last_30_arpu']);
                $DataArray[$key]['last_30_arpu_percentage'] = $LastArpuPercentage['percentage'];
                $DataArray[$key]['last_30_arpu_class'] = $LastArpuPercentage['class'];
                $DataArray[$key]['last_30_arpu_arrow'] = $LastArpuPercentage['arrow'];

                $LastPnlPercentage = $this->classPercentage($value['prev_pnl'],$value['last_pnl']);
                $DataArray[$key]['last_pnl_percentage'] = $LastPnlPercentage['percentage'];
                $DataArray[$key]['last_pnl_class'] = $LastPnlPercentage['class'];
                $DataArray[$key]['last_pnl_arrow'] = $LastPnlPercentage['arrow'];

                $LastAvgPnlPercentage = $this->classPercentage($value['prev_avg_pnl'],$value['last_avg_pnl']);
                $DataArray[$key]['last_avg_pnl_percentage'] = $LastAvgPnlPercentage['percentage'];
                $DataArray[$key]['last_avg_pnl_class'] = $LastAvgPnlPercentage['class'];
                $DataArray[$key]['last_avg_pnl_arrow'] = $LastAvgPnlPercentage['arrow'];
            }

            // dd($DataArray);
            $sumemry = $DataArray;
            $allDataSum = UtilityReports::DashboardAllDataSum($sumemry);

            $CurrentRevClass = $this->classPercentage($allDataSum['last_revenue_usd'],$allDataSum['current_revenue_usd']);
            $allDataSum['current_revenue_usd_percentage'] = $CurrentRevClass['percentage'];
            $allDataSum['current_revenue_usd_class'] = $CurrentRevClass['class'];
            $allDataSum['current_revenue_usd_arrow'] = $CurrentRevClass['arrow'];

            $CurrentRevPercentage = $this->classPercentage($allDataSum['last_revenue_usd'],$allDataSum['estimated_revenue_usd']);
            $allDataSum['estimated_revenue_usd_percentage'] = $CurrentRevPercentage['percentage'];
            $allDataSum['estimated_revenue_usd_class'] = $CurrentRevPercentage['class'];
            $allDataSum['estimated_revenue_usd_arrow'] = $CurrentRevPercentage['arrow'];

            $CurrentAvgRevPercentage = $this->classPercentage($allDataSum['last_avg_revenue_usd'],$allDataSum['estimated_avg_revenue_usd']);
            $allDataSum['estimated_avg_revenue_usd_percentage'] = $CurrentAvgRevPercentage['percentage'];
            $allDataSum['estimated_avg_revenue_usd_class'] = $CurrentAvgRevPercentage['class'];
            $allDataSum['estimated_avg_revenue_usd_arrow'] = $CurrentAvgRevPercentage['arrow'];

            $CurrentGRevClass = $this->classPercentage($allDataSum['last_gross_revenue_usd'],$allDataSum['current_gross_revenue_usd']);
            $allDataSum['current_gross_revenue_usd_percentage'] = $CurrentGRevClass['percentage'];
            $allDataSum['current_gross_revenue_usd_class'] = $CurrentGRevClass['class'];
            $allDataSum['current_gross_revenue_usd_arrow'] = $CurrentGRevClass['arrow'];

            $CurrentGRevPercentage = $this->classPercentage($allDataSum['last_gross_revenue_usd'],$allDataSum['estimated_gross_revenue_usd']);
            $allDataSum['estimated_gross_revenue_usd_percentage'] = $CurrentGRevPercentage['percentage'];
            $allDataSum['estimated_gross_revenue_usd_class'] = $CurrentGRevPercentage['class'];
            $allDataSum['estimated_gross_revenue_usd_arrow'] = $CurrentGRevPercentage['arrow'];

            $CurrentAvgGRevPercentage = $this->classPercentage($allDataSum['last_avg_gross_revenue_usd'],$allDataSum['estimated_avg_gross_revenue_usd']);
            $allDataSum['estimated_avg_gross_revenue_usd_percentage'] = $CurrentAvgGRevPercentage['percentage'];
            $allDataSum['estimated_avg_gross_revenue_usd_class'] = $CurrentAvgGRevPercentage['class'];
            $allDataSum['estimated_avg_gross_revenue_usd_arrow'] = $CurrentAvgGRevPercentage['arrow'];

            $CurrentMOClass = $this->classPercentage($allDataSum['last_total_mo'],$allDataSum['current_total_mo']);
            $allDataSum['current_total_mo_percentage'] = $CurrentMOClass['percentage'];
            $allDataSum['current_total_mo_class'] = $CurrentMOClass['class'];
            $allDataSum['current_total_mo_arrow'] = $CurrentMOClass['arrow'];

            $CurrentRegClass = $this->classPercentage($allDataSum['last_mo'],$allDataSum['current_mo']);
            $allDataSum['current_mo_percentage'] = $CurrentRegClass['percentage'];
            $allDataSum['current_mo_class'] = $CurrentRegClass['class'];
            $allDataSum['current_mo_arrow'] = $CurrentRegClass['arrow'];

            $CurrentMOPercentage = $this->classPercentage($allDataSum['last_total_mo'],$allDataSum['estimated_total_mo']);
            $allDataSum['estimated_total_mo_percentage'] = $CurrentMOPercentage['percentage'];
            $allDataSum['estimated_total_mo_class'] = $CurrentMOPercentage['class'];
            $allDataSum['estimated_total_mo_arrow'] = $CurrentMOPercentage['arrow'];

            $CurrentAvgMOPercentage = $this->classPercentage($allDataSum['last_avg_mo'],$allDataSum['estimated_avg_mo']);
            $allDataSum['estimated_avg_mo_percentage'] = $CurrentAvgMOPercentage['percentage'];
            $allDataSum['estimated_avg_mo_class'] = $CurrentAvgMOPercentage['class'];
            $allDataSum['estimated_avg_mo_arrow'] = $CurrentAvgMOPercentage['arrow'];

            $CurrentRegPercentage = $this->classPercentage($allDataSum['last_mo'],$allDataSum['estimated_mo']);
            $allDataSum['estimated_mo_percentage'] = $CurrentRegPercentage['percentage'];
            $allDataSum['estimated_mo_class'] = $CurrentRegPercentage['class'];
            $allDataSum['estimated_mo_arrow'] = $CurrentRegPercentage['arrow'];

            $CurrentCostClass = $this->classPercentage($allDataSum['last_cost'],$allDataSum['current_cost']);
            $allDataSum['current_cost_percentage'] = $CurrentCostClass['percentage'];
            $allDataSum['current_cost_class'] = $CurrentCostClass['class'];
            $allDataSum['current_cost_arrow'] = $CurrentCostClass['arrow'];

            $CurrentCostPercentage = $this->classPercentage($allDataSum['last_cost'],$allDataSum['estimated_cost']);
            $allDataSum['estimated_cost_percentage'] = $CurrentCostPercentage['percentage'];
            $allDataSum['estimated_cost_class'] = $CurrentCostPercentage['class'];
            $allDataSum['estimated_cost_arrow'] = $CurrentCostPercentage['arrow'];

            $CurrentPriceMOClass = $this->classPercentage($allDataSum['last_price_mo'],$allDataSum['current_price_mo']);
            $allDataSum['current_price_mo_percentage'] = $CurrentPriceMOClass['percentage'];
            $allDataSum['current_price_mo_class'] = $CurrentPriceMOClass['class'];
            $allDataSum['current_price_mo_arrow'] = $CurrentPriceMOClass['arrow'];

            $CurrentPriceMOPercentage = $this->classPercentage($allDataSum['last_price_mo'],$allDataSum['estimated_price_mo']);
            $allDataSum['estimated_price_mo_percentage'] = $CurrentPriceMOPercentage['percentage'];
            $allDataSum['estimated_price_mo_class'] = $CurrentPriceMOPercentage['class'];
            $allDataSum['estimated_price_mo_arrow'] = $CurrentPriceMOPercentage['arrow'];

            $CurrentROIClass = $this->classPercentage($allDataSum['lastMonthROI'],$allDataSum['currentMonthROI']);
            $allDataSum['currentMonthROI_percentage'] = $CurrentROIClass['percentage'];
            $allDataSum['currentMonthROI_class'] = $CurrentROIClass['class'];
            $allDataSum['currentMonthROI_arrow'] = $CurrentROIClass['arrow'];

            $CurrentROIPercentage = $this->classPercentage($allDataSum['lastMonthROI'],$allDataSum['estimatedMonthROI']);
            $allDataSum['estimatedMonthROI_percentage'] = $CurrentROIPercentage['percentage'];
            $allDataSum['estimatedMonthROI_class'] = $CurrentROIPercentage['class'];
            $allDataSum['estimatedMonthROI_arrow'] = $CurrentROIPercentage['arrow'];

            $CurrentArpuClass = $this->classPercentage($allDataSum['last_30_arpu'],$allDataSum['current_30_arpu']);
            $allDataSum['current_30_arpu_percentage'] = $CurrentArpuClass['percentage'];
            $allDataSum['current_30_arpu_class'] = $CurrentArpuClass['class'];
            $allDataSum['current_30_arpu_arrow'] = $CurrentArpuClass['arrow'];

            $CurrentArpuPercentage = $this->classPercentage($allDataSum['last_30_arpu'],$allDataSum['estimated_30_arpu']);
            $allDataSum['estimated_30_arpu_percentage'] = $CurrentArpuPercentage['percentage'];
            $allDataSum['estimated_30_arpu_class'] = $CurrentArpuPercentage['class'];
            $allDataSum['estimated_30_arpu_arrow'] = $CurrentArpuPercentage['arrow'];

            $CurrentPnlClass = $this->classPercentage($allDataSum['last_pnl'],$allDataSum['current_pnl']);
            $allDataSum['current_pnl_percentage'] = $CurrentPnlClass['percentage'];
            $allDataSum['current_pnl_class'] = $CurrentPnlClass['class'];
            $allDataSum['current_pnl_arrow'] = $CurrentPnlClass['arrow'];

            $CurrentPnlPercentage = $this->classPercentage($allDataSum['last_pnl'],$allDataSum['estimated_pnl']);
            $allDataSum['estimated_pnl_percentage'] = $CurrentPnlPercentage['percentage'];
            $allDataSum['estimated_pnl_class'] = $CurrentPnlPercentage['class'];
            $allDataSum['estimated_pnl_arrow'] = $CurrentPnlPercentage['arrow'];

            $CurrentAvgPnlPercentage = $this->classPercentage($allDataSum['last_avg_pnl'],$allDataSum['estimated_avg_pnl']);
            $allDataSum['estimated_avg_pnl_percentage'] = $CurrentAvgPnlPercentage['percentage'];
            $allDataSum['estimated_avg_pnl_class'] = $CurrentAvgPnlPercentage['class'];
            $allDataSum['estimated_avg_pnl_arrow'] = $CurrentAvgPnlPercentage['arrow'];


            $LastRevPercentage = $this->classPercentage($allDataSum['prev_revenue_usd'],$allDataSum['last_revenue_usd']);
            $allDataSum['last_revenue_usd_percentage'] = $LastRevPercentage['percentage'];
            $allDataSum['last_revenue_usd_class'] = $LastRevPercentage['class'];
            $allDataSum['last_revenue_usd_arrow'] = $LastRevPercentage['arrow'];

            $LastAvgRevPercentage = $this->classPercentage($allDataSum['prev_avg_revenue_usd'],$allDataSum['last_avg_revenue_usd']);
            $allDataSum['last_avg_revenue_usd_percentage'] = $LastAvgRevPercentage['percentage'];
            $allDataSum['last_avg_revenue_usd_class'] = $LastAvgRevPercentage['class'];
            $allDataSum['last_avg_revenue_usd_arrow'] = $LastAvgRevPercentage['arrow'];

            $LastGRevPercentage = $this->classPercentage($allDataSum['prev_gross_revenue_usd'],$allDataSum['last_gross_revenue_usd']);
            $allDataSum['last_gross_revenue_usd_percentage'] = $LastGRevPercentage['percentage'];
            $allDataSum['last_gross_revenue_usd_class'] = $LastGRevPercentage['class'];
            $allDataSum['last_gross_revenue_usd_arrow'] = $LastGRevPercentage['arrow'];

            $LastAvgGRevPercentage = $this->classPercentage($allDataSum['prev_avg_gross_revenue_usd'],$allDataSum['last_avg_gross_revenue_usd']);
            $allDataSum['last_avg_gross_revenue_usd_percentage'] = $LastAvgGRevPercentage['percentage'];
            $allDataSum['last_avg_gross_revenue_usd_class'] = $LastAvgGRevPercentage['class'];
            $allDataSum['last_avg_gross_revenue_usd_arrow'] = $LastAvgGRevPercentage['arrow'];

            $LastMOPercentage = $this->classPercentage($allDataSum['prev_total_mo'],$allDataSum['last_total_mo']);
            $allDataSum['last_total_mo_percentage'] = $LastMOPercentage['percentage'];
            $allDataSum['last_total_mo_class'] = $LastMOPercentage['class'];
            $allDataSum['last_total_mo_arrow'] = $LastMOPercentage['arrow'];

            $LastAvgMOPercentage = $this->classPercentage($allDataSum['prev_avg_mo'],$allDataSum['last_avg_mo']);
            $allDataSum['last_avg_mo_percentage'] = $LastAvgMOPercentage['percentage'];
            $allDataSum['last_avg_mo_class'] = $LastAvgMOPercentage['class'];
            $allDataSum['last_avg_mo_arrow'] = $LastAvgMOPercentage['arrow'];

            $LastRegPercentage = $this->classPercentage($allDataSum['prev_mo'],$allDataSum['last_mo']);
            $allDataSum['last_mo_percentage'] = $LastRegPercentage['percentage'];
            $allDataSum['last_mo_class'] = $LastRegPercentage['class'];
            $allDataSum['last_mo_arrow'] = $LastRegPercentage['arrow'];

            $LastCostPercentage = $this->classPercentage($allDataSum['prev_cost'],$allDataSum['last_cost']);
            $allDataSum['last_cost_percentage'] = $LastCostPercentage['percentage'];
            $allDataSum['last_cost_class'] = $LastCostPercentage['class'];
            $allDataSum['last_cost_arrow'] = $LastCostPercentage['arrow'];

            $LastPriceMOPercentage = $this->classPercentage($allDataSum['prev_price_mo'],$allDataSum['last_price_mo']);
            $allDataSum['last_price_mo_percentage'] = $LastPriceMOPercentage['percentage'];
            $allDataSum['last_price_mo_class'] = $LastPriceMOPercentage['class'];
            $allDataSum['last_price_mo_arrow'] = $LastPriceMOPercentage['arrow'];

            $LastROIPercentage = $this->classPercentage($allDataSum['previousMonthROI'],$allDataSum['lastMonthROI']);
            $allDataSum['lastMonthROI_percentage'] = $LastROIPercentage['percentage'];
            $allDataSum['lastMonthROI_class'] = $LastROIPercentage['class'];
            $allDataSum['lastMonthROI_arrow'] = $LastROIPercentage['arrow'];

            $LastArpuPercentage = $this->classPercentage($allDataSum['prev_30_arpu'],$allDataSum['last_30_arpu']);
            $allDataSum['last_30_arpu_percentage'] = $LastArpuPercentage['percentage'];
            $allDataSum['last_30_arpu_class'] = $LastArpuPercentage['class'];
            $allDataSum['last_30_arpu_arrow'] = $LastArpuPercentage['arrow'];

            $LastPnlPercentage = $this->classPercentage($allDataSum['prev_pnl'],$allDataSum['last_pnl']);
            $allDataSum['last_pnl_percentage'] = $LastPnlPercentage['percentage'];
            $allDataSum['last_pnl_class'] = $LastPnlPercentage['class'];
            $allDataSum['last_pnl_arrow'] = $LastPnlPercentage['arrow'];

            $LastAvgPnlPercentage = $this->classPercentage($allDataSum['prev_avg_pnl'],$allDataSum['last_avg_pnl']);
            $allDataSum['last_avg_pnl_percentage'] = $LastAvgPnlPercentage['percentage'];
            $allDataSum['last_avg_pnl_class'] = $LastAvgPnlPercentage['class'];
            $allDataSum['last_avg_pnl_arrow'] = $LastAvgPnlPercentage['arrow'];

            return view('admin.company_dashboard', compact('sumemry','allDataSum'));
        }else{
            return redirect()->route('login');
        }
    }

    public function classPercentage($PreviousData,$CurrentData){
        if((float)$PreviousData>(float)$CurrentData){
            $class='text-danger';
            $arrow='fa-arrow-down';
        }elseif((float)$PreviousData == (float)$CurrentData){
            $class='';
            $arrow='';
        }
           else{
            $class='text-success';
            $arrow='fa-arrow-up';
        }
        $percentage=0;
        if($PreviousData>0)
        $percentage=(((float)$CurrentData-(float)$PreviousData)*100)/$PreviousData;
        $data=['class'=>$class,'arrow'=>$arrow,'percentage'=>round($percentage,1)];
        return $data;
    }

    // get report by country id
    function getReportsByCountryID($reports)
    {
        if(!empty($reports))
        {
            $reportsResult=array();
            $tempreport=array();
            foreach($reports as $report)
            {
                $tempreport[$report['country_id']] = $report;
            }

            $reportsResult =  $tempreport;
            return $reportsResult;
        }
    }

    function getReportsByOperatorID($reports)
    {
        if(!empty($reports))
        {
            $reportsResult=array();
            $tempreport=array();
            foreach($reports as $report)
            {
                $tempreport[$report['id_operator']] = $report;
            }

            $reportsResult =  $tempreport;
            return $reportsResult;
        }
    }

    function getReportsByCompanyID($reports)
    {
        if(!empty($reports))
        {
            $reportsResult=array();
            $tempreport=array();
            foreach($reports as $report)
            {
                $tempreport[$report['country_id']] = $report;
            }

            $reportsResult =  $tempreport;
            return $reportsResult;
        }
    }

    function getUserReportsByOperatorID($reports)
    {
        if(!empty($reports))
        {
            $reportsResult=array();
            $tempreport=array();
            foreach($reports as $report)
            {
                $tempreport[$report['operator_id']] = $report;
            }

            $reportsResult =  $tempreport;
            return $reportsResult;
        }
    }

    public function getFilterOperator($countryid = '',$company_id = '',$operator_id ='')
    {
        $operators=Operator::Status(1)->join('company_operators', 'company_operators.operator_id','=','operators.id_operator' )->join('companies', 'companies.id','=','company_operators.company_id' );
     
        if($countryid)
        $operators= $operators->where('operators.country_id','=',$countryid);

        if($operator_id)
        $operators= $operators->whereIn('operators.id_operator',$operator_id);
      
        if($company_id != 'allcompany'){
            if($company_id)
            $operators=$operators->where('company_operators.company_id','=',$company_id);
        }
        $operators=$operators->get(['operators.id_operator as operator_id']);
        return $operators->pluck('operator_id');
    }
}
