<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Session;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\PnlsUserOperatorSummarize;
use App\Models\ReportsPnlsOperatorSummarizes;
use App\Models\ReportSummeriseUsers;
use App\Models\Operator;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use App\Models\Company;
use App\Models\Country;
use App\Models\User;
use App\Models\role_operators;
use App\Models\CompanyOperators;
use App\common\Utility;
use App\common\UtilityReports;
use Config;

class PnlDailtyReportDetailsController extends Controller
{
    //get pnl Operator report
    public function DailyPnlReportOperatorDetails(Request $request)
    {
        if(\Auth::user()->can('PNL Detail'))
        {
            // dd(12333);
            // get filtre request
            $CountryId = $req_CountryId = $request->country;
            $CompanyId = $req_CompanyId = $request->company;
            $BusinessType = $req_BusinessType = $request->business_type;
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
                // $end_date_input = new Carbon($req_end_date);

                $start_date = $start_date_input->subDays(35)->format('Y-m-d');
                $startColumnDateDisplay = $display_date_input->format('Y-m-d');
                //dd($startColumnDateDisplay."ppp");
                $end_date = $req_end_date;
                $month = $display_date_input->format('F Y');
            }else{
                $req_Start_date = date('Y-m-d', strtotime('-30 days'));
                $req_end_date = date('Y-m-d');

                $start_date_input = new Carbon($req_Start_date);
                $display_date_input = new Carbon($req_Start_date);

                $start_date = $start_date_input->subDays(35)->format('Y-m-d');
                $startColumnDateDisplay = $display_date_input->format('Y-m-d');

                $end_date = $req_end_date;
                $month = $display_date_input->format('F Y');
            }

            if($request->filled('company') && $req_CompanyId !="allcompany" && !$request->filled('operatorId'))
            {
                $companies = Company::Find($req_CompanyId);
                $Operators_company = array();

                if(!empty($companies))
                {
                    $Operators_company = $companies->company_operators->pluck('operator_id')->toArray();
                    $Operators = Operator::Status(1)->GetOperatorByOperatorId($Operators_company)->get();
                }

                $showAllOperator = false;
            }

            if($request->filled('company') && $request->filled('country') && !$request->filled('operatorId'))
            {
                $data=[
                    'id'=>$req_CountryId,
                    'company'=>$req_CompanyId,
                ];
                $requestobj = new Request($data);
                $ReportControllerobj= new ReportController;
                $Operators=$ReportControllerobj->userFilterOperator($requestobj);
                // $Operators = Operator::Status(1)->GetOperatorByCountryId($req_CountryId)->get();
                $showAllOperator = false;
            }

            if (isset($req_CompanyId) && !$request->filled('country') && $request->filled('business_type') && !$request->filled('operatorId')) {
                $Countrys[0] = Country::with('operators')->Find($CountryId);
                $data = [
                    'country' => $req_CountryId,
                    'company' => $req_CompanyId,
                    'business_type' => $req_BusinessType,
                ];

                $requestobj = new Request($data);
                $ReportControllerobj = new ReportController;
                $Operators = $ReportControllerobj->userFilterBusinessTypeOperator($requestobj);
                $CountryFlag = false;
                $showAllOperator = false;
            }

            if ($request->filled('company') && $request->filled('country') && $request->filled('business_type') && !$request->filled('operatorId')) {
                $Countrys[0] = Country::with('operators')->Find($CountryId);
                $data = [
                    'country' => $req_CountryId,
                    'company' => $req_CompanyId,
                    'business_type' => $req_BusinessType,
                ];

                $requestobj = new Request($data);
                $ReportControllerobj = new ReportController;
                $Operators = $ReportControllerobj->userFilterBusinessTypeOperator($requestobj);
                $CountryFlag = false;
                $showAllOperator = false;
            }

            if($request->filled('operatorId'))
            {
                $Operators = Operator::with('revenueshare','RevenushareByDate')->Status(1)->GetOperatorByOperatorId($filterOperator)
                            ->get();
                $showAllOperator = false;
            }

            if($showAllOperator)
            {
                $Operators = Operator::with('revenueshare','RevenushareByDate')->Status(1)->get();
            }

            $Country = Country::all()->toArray();
            $companys = Company::get();
            //$static_operator =array(1,2,3,4);
            $countries = array();
            if(!empty($Country))
            {
                foreach($Country as $CountryI)
                {
                    $countries[$CountryI['id']]=$CountryI;
                }
            }

            // $contains = Arr::hasAny($Country, "2");
            // $Operators = Operator::Status(1)->get();
            $staticOperators = $Operators->pluck('id_operator')->toArray();
            $sumemry = array();

            // $start_date = Carbon::now()->startOfMonth()->subDays(30)->format('Y-m-d');
            // $startColumnDateDisplay = Carbon::now()->startOfMonth()->format('Y-m-d');
            // $end_date = Carbon::now()->format('Y-m-d');
            // $month = Carbon::now()->format('F Y');

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

                $unknown_operator = ReportsPnlsOperatorSummarizes::UnmatchOperator();

            }else{

                $UserOperatorServices =Session::get('userOperatorService');
                if(empty($UserOperatorServices))
                {
                    dd("Please Contact to admin , add Operator to your account");
                }

                $arrayOperatorsIds = $UserOperatorServices['id_operators'];
                $arrayServicesIds = $UserOperatorServices['id_services'];

                $reports = ReportsPnlsOperatorSummarizes::filterOperator($arrayOperatorsIds)
                ->filterDateRange($start_date,$end_date)
                ->orderBy('country_id')
                ->orderBy('date', 'ASC')
                ->get()->toArray();

                $userOperators = Operator::with('revenueshare','RevenushareByDate','country')->filteroperator($arrayOperatorsIds)->get()->toArray();
                foreach ($userOperators as $key => $value) {
                    if (empty($value['revenueshare'])) {
                        $userOperators[$key]['revenueshare']['merchant_revenue_share'] = 100;
                    }
                }
                $userOperatorsIDs = $this->getReportsByOperatorID($userOperators);

                $userreports = ReportSummeriseUsers::filteroperator($arrayOperatorsIds)
                ->filterDateRange($start_date,$end_date)
                ->User($user_id)
                ->orderBy('country_id')
                ->orderBy('date', 'ASC')
                ->get()->toArray();

                $userreportsByIDs = $this->getUserReportsByOperator($userreports);

                $unknown_operator = [];
            }

            // $reports = ReportsPnlsOperatorSummarizes::filterOperator($staticOperators)
            // ->filterDateRange($start_date,$end_date)
            // ->orderBy('country_id')
            // ->orderBy('date', 'ASC')
            // ->get()->toArray();
            // dd($reports);

            $reportsByIDs = $this->getReportsByOperator($reports);
            $datesIndividual =Utility::getRangeDates($startColumnDateDisplay,$end_date);
            $no_of_days = Utility::getRangeDateNo($datesIndividual);

            if(isset($userreportsByIDs) && !empty($userreportsByIDs)){
                foreach ($userreportsByIDs as $key1 => $value1) {
                    foreach ($value1 as $key2 => $value2) {
                        $reportsByIDs[$key1][$key2]['rev'] = $value2['gros_rev'];
                        $reportsByIDs[$key1][$key2]['rev_usd'] = $value2['gros_rev']*$userOperatorsIDs[$key1]['country']['usd'];
                        $reportsByIDs[$key1][$key2]['share'] = $value2['gros_rev']*$userOperatorsIDs[$key1]['country']['usd']*($userOperatorsIDs[$key1]['revenueshare']['merchant_revenue_share']/100);
                        $reportsByIDs[$key1][$key2]['lshare'] = $value2['gros_rev']*($userOperatorsIDs[$key1]['revenueshare']['merchant_revenue_share']/100);
                    }
                }
            }
            // dd($no_of_days);

            if(!empty($Operators))
            {
                foreach($Operators as $operator)
                {
                    $tmpOperators=array();
                    $tmpOperators['operator'] = $operator;
                    $country_id  = $operator->country_id;
                    $contain_id = Arr::exists($countries, $country_id);
                    $OperatorCountry = array();

                    if($contain_id )
                    {
                        $tmpOperators['country']=$countries[$country_id];
                        $OperatorCountry = $countries[$country_id];
                    }

                    $reportsColumnData = $this->getPNLReportsDateWise($operator,$no_of_days,$reportsByIDs,$OperatorCountry);
                    $tmpOperators['month_string'] = $month;
                    $tmpOperators['last_update'] = $reportsColumnData['last_update'];
                    $tmpOperators['hostingCost'] = isset($operator->hostingCost) ? $operator->hostingCost : 0;
                    $tmpOperators['contentCost'] = isset($operator->content) ? $operator->content : 0;
                    $tmpOperators['rndCost'] = isset($operator->rnd) ? $operator->rnd : 0;
                    $tmpOperators['bdCost'] = 2.5;
                    $tmpOperators['marketCost'] = 1.5;
                    $tmpOperators['miscCost'] = isset($operator->miscCost) ? $operator->miscCost : 0;
                    $tmpOperators['vatTax'] = isset($operator->vat) ? $operator->vat : 0;
                    $tmpOperators['whtTax'] = isset($operator->wht) ? $operator->wht : 0;
                    $tmpOperators['miscTax'] = isset($operator->miscTax) ? $operator->miscTax : 0;
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


                    $total_avg_market_cost = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['market_cost'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['market_cost']['dates'] = $reportsColumnData['market_cost'];
                    $tmpOperators['market_cost']['total'] = $total_avg_market_cost['sum'];
                    $tmpOperators['market_cost']['t_mo_end'] = $total_avg_market_cost['T_Mo_End'];
                    $tmpOperators['market_cost']['avg'] = $total_avg_market_cost['avg'];


                    $total_avg_misc_cost = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['misc_cost'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['misc_cost']['dates'] = $reportsColumnData['misc_cost'];
                    $tmpOperators['misc_cost']['total'] = $total_avg_misc_cost['sum'];
                    $tmpOperators['misc_cost']['t_mo_end'] = $total_avg_misc_cost['T_Mo_End'];
                    $tmpOperators['misc_cost']['avg'] = $total_avg_misc_cost['avg'];


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


                    $total_avg_net_after_tax = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['net_after_tax'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['net_after_tax']['dates'] = $reportsColumnData['net_after_tax'];
                    $tmpOperators['net_after_tax']['total'] = $total_avg_net_after_tax['sum'];
                    $tmpOperators['net_after_tax']['t_mo_end'] = $total_avg_net_after_tax['T_Mo_End'];
                    $tmpOperators['net_after_tax']['avg'] = $total_avg_net_after_tax['avg'];


                    $total_avg_net_revenue_after_tax = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['net_revenue_after_tax'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['net_revenue_after_tax']['dates'] = $reportsColumnData['net_revenue_after_tax'];
                    $tmpOperators['net_revenue_after_tax']['total'] = $total_avg_net_revenue_after_tax['sum'];
                    $tmpOperators['net_revenue_after_tax']['t_mo_end'] = $total_avg_net_revenue_after_tax['T_Mo_End'];
                    $tmpOperators['net_revenue_after_tax']['avg'] = $total_avg_net_revenue_after_tax['avg'];


                    $total_avg_br = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['br'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['br']['dates'] = $reportsColumnData['br'];
                    $tmpOperators['br']['total'] = $total_avg_br['sum'];
                    $tmpOperators['br']['t_mo_end'] = $total_avg_br['T_Mo_End'];
                    $tmpOperators['br']['avg'] = $total_avg_br['avg'];


                    $total_avg_fp = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['fp'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['fp']['dates'] = $reportsColumnData['fp'];
                    $tmpOperators['fp']['total'] = $total_avg_fp['sum'];
                    $tmpOperators['fp']['t_mo_end'] = $total_avg_fp['T_Mo_End'];
                    $tmpOperators['fp']['avg'] = $total_avg_fp['avg'];


                    $total_avg_fp_success = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['fp_success'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['fp_success']['dates'] = $reportsColumnData['fp_success'];
                    $tmpOperators['fp_success']['total'] = $total_avg_fp_success['sum'];
                    $tmpOperators['fp_success']['t_mo_end'] = $total_avg_fp_success['T_Mo_End'];
                    $tmpOperators['fp_success']['avg'] = $total_avg_fp_success['avg'];


                    $total_avg_fp_failed = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['fp_failed'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['fp_failed']['dates'] = $reportsColumnData['fp_failed'];
                    $tmpOperators['fp_failed']['total'] = $total_avg_fp_failed['sum'];
                    $tmpOperators['fp_failed']['t_mo_end'] = $total_avg_fp_failed['T_Mo_End'];
                    $tmpOperators['fp_failed']['avg'] = $total_avg_fp_failed['avg'];


                    $total_avg_dp = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['dp'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['dp']['dates'] = $reportsColumnData['dp'];
                    $tmpOperators['dp']['total'] = $total_avg_dp['sum'];
                    $tmpOperators['dp']['t_mo_end'] = $total_avg_dp['T_Mo_End'];
                    $tmpOperators['dp']['avg'] = $total_avg_dp['avg'];


                    $total_avg_dp_success = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['dp_success'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['dp_success']['dates'] = $reportsColumnData['dp_success'];
                    $tmpOperators['dp_success']['total'] = $total_avg_dp_success['sum'];
                    $tmpOperators['dp_success']['t_mo_end'] = $total_avg_dp_success['T_Mo_End'];
                    $tmpOperators['dp_success']['avg'] = $total_avg_dp_success['avg'];


                    $total_avg_dp_failed = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['dp_failed'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['dp_failed']['dates'] = $reportsColumnData['dp_failed'];
                    $tmpOperators['dp_failed']['total'] = $total_avg_dp_failed['sum'];
                    $tmpOperators['dp_failed']['t_mo_end'] = $total_avg_dp_failed['T_Mo_End'];
                    $tmpOperators['dp_failed']['avg'] = $total_avg_dp_failed['avg'];


                    $total_avg_renewal = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['renewal'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['renewal']['dates'] = $reportsColumnData['renewal'];
                    $tmpOperators['renewal']['total'] = $total_avg_renewal['sum'];
                    $tmpOperators['renewal']['t_mo_end'] = $total_avg_renewal['T_Mo_End'];
                    $tmpOperators['renewal']['avg'] = $total_avg_renewal['avg'];


                    $total_avg_vat = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['vat'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['vat']['dates'] = $reportsColumnData['vat'];
                    $tmpOperators['vat']['total'] = $total_avg_vat['sum'];
                    $tmpOperators['vat']['t_mo_end'] = $total_avg_vat['T_Mo_End'];
                    $tmpOperators['vat']['avg'] = $total_avg_vat['avg'];


                    $total_avg_spec_tax = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['spec_tax'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['spec_tax']['dates'] = $reportsColumnData['spec_tax'];
                    $tmpOperators['spec_tax']['total'] = $total_avg_spec_tax['sum'];
                    $tmpOperators['spec_tax']['t_mo_end'] = $total_avg_spec_tax['T_Mo_End'];
                    $tmpOperators['spec_tax']['avg'] = $total_avg_spec_tax['avg'];


                    $total_avg_government_cost = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['government_cost'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['government_cost']['dates'] = $reportsColumnData['government_cost'];
                    $tmpOperators['government_cost']['total'] = $total_avg_government_cost['sum'];
                    $tmpOperators['government_cost']['t_mo_end'] = $total_avg_government_cost['T_Mo_End'];
                    $tmpOperators['government_cost']['avg'] = $total_avg_government_cost['avg'];


                    $total_avg_dealer_commision = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['dealer_commision'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['dealer_commision']['dates'] = $reportsColumnData['dealer_commision'];
                    $tmpOperators['dealer_commision']['total'] = $total_avg_dealer_commision['sum'];
                    $tmpOperators['dealer_commision']['t_mo_end'] = $total_avg_dealer_commision['T_Mo_End'];
                    $tmpOperators['dealer_commision']['avg'] = $total_avg_dealer_commision['avg'];


                    $total_avg_wht = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['wht'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['wht']['dates'] = $reportsColumnData['wht'];
                    $tmpOperators['wht']['total'] = $total_avg_wht['sum'];
                    $tmpOperators['wht']['t_mo_end'] = $total_avg_wht['T_Mo_End'];
                    $tmpOperators['wht']['avg'] = $total_avg_wht['avg'];


                    $total_avg_misc_tax = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['misc_tax'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['misc_tax']['dates'] = $reportsColumnData['misc_tax'];
                    $tmpOperators['misc_tax']['total'] = $total_avg_misc_tax['sum'];
                    $tmpOperators['misc_tax']['t_mo_end'] = $total_avg_misc_tax['T_Mo_End'];
                    $tmpOperators['misc_tax']['avg'] = $total_avg_misc_tax['avg'];


                    $total_avg_other_tax = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['other_tax'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['other_tax']['dates'] = $reportsColumnData['other_tax'];
                    $tmpOperators['other_tax']['total'] = $total_avg_other_tax['sum'];
                    $tmpOperators['other_tax']['t_mo_end'] = $total_avg_other_tax['T_Mo_End'];
                    $tmpOperators['other_tax']['avg'] = $total_avg_other_tax['avg'];


                    $total_avg_uso = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['uso'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['uso']['dates'] = $reportsColumnData['uso'];
                    $tmpOperators['uso']['total'] = $total_avg_uso['sum'];
                    $tmpOperators['uso']['t_mo_end'] = $total_avg_uso['T_Mo_End'];
                    $tmpOperators['uso']['avg'] = $total_avg_uso['avg'];


                    $total_avg_agre_paxxa = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['agre_paxxa'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['agre_paxxa']['dates'] = $reportsColumnData['agre_paxxa'];
                    $tmpOperators['agre_paxxa']['total'] = $total_avg_agre_paxxa['sum'];
                    $tmpOperators['agre_paxxa']['t_mo_end'] = $total_avg_agre_paxxa['T_Mo_End'];
                    $tmpOperators['agre_paxxa']['avg'] = $total_avg_agre_paxxa['avg'];


                    $total_avg_sbaf = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['sbaf'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['sbaf']['dates'] = $reportsColumnData['sbaf'];
                    $tmpOperators['sbaf']['total'] = $total_avg_sbaf['sum'];
                    $tmpOperators['sbaf']['t_mo_end'] = $total_avg_sbaf['T_Mo_End'];
                    $tmpOperators['sbaf']['avg'] = $total_avg_sbaf['avg'];


                    $total_avg_clicks = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['clicks'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['clicks']['dates'] = $reportsColumnData['clicks'];
                    $tmpOperators['clicks']['total'] = $total_avg_clicks['sum'];
                    $tmpOperators['clicks']['t_mo_end'] = $total_avg_clicks['T_Mo_End'];
                    $tmpOperators['clicks']['avg'] = $total_avg_clicks['avg'];


                    $total_avg_ratio_for_cpa = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['ratio_for_cpa'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['ratio_for_cpa']['dates'] = $reportsColumnData['ratio_for_cpa'];
                    $tmpOperators['ratio_for_cpa']['total'] = $total_avg_ratio_for_cpa['sum'];
                    $tmpOperators['ratio_for_cpa']['t_mo_end'] = $total_avg_ratio_for_cpa['T_Mo_End'];
                    $tmpOperators['ratio_for_cpa']['avg'] = $total_avg_ratio_for_cpa['avg'];


                    $total_avg_cpa_price = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['cpa_price'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['cpa_price']['dates'] = $reportsColumnData['cpa_price'];
                    $tmpOperators['cpa_price']['total'] = $total_avg_cpa_price['sum'];
                    $tmpOperators['cpa_price']['t_mo_end'] = $total_avg_cpa_price['T_Mo_End'];
                    $tmpOperators['cpa_price']['avg'] = $total_avg_cpa_price['avg'];


                    $total_avg_cr_mo_clicks = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['cr_mo_clicks'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['cr_mo_clicks']['dates'] = $reportsColumnData['cr_mo_clicks'];
                    $tmpOperators['cr_mo_clicks']['total'] = $total_avg_cr_mo_clicks['sum'];
                    $tmpOperators['cr_mo_clicks']['t_mo_end'] = $total_avg_cr_mo_clicks['T_Mo_End'];
                    $tmpOperators['cr_mo_clicks']['avg'] = $total_avg_cr_mo_clicks['avg'];


                    $total_avg_cr_mo_landing = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['cr_mo_landing'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['cr_mo_landing']['dates'] = $reportsColumnData['cr_mo_landing'];
                    $tmpOperators['cr_mo_landing']['total'] = $total_avg_cr_mo_landing['sum'];
                    $tmpOperators['cr_mo_landing']['t_mo_end'] = $total_avg_cr_mo_landing['T_Mo_End'];
                    $tmpOperators['cr_mo_landing']['avg'] = $total_avg_cr_mo_landing['avg'];


                    $total_avg_landing = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['landing'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['landing']['dates'] = $reportsColumnData['landing'];
                    $tmpOperators['landing']['total'] = $total_avg_landing['sum'];
                    $tmpOperators['landing']['t_mo_end'] = $total_avg_landing['T_Mo_End'];
                    $tmpOperators['landing']['avg'] = $total_avg_landing['avg'];


                    $total_avg_mo = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['mo'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['mo']['dates'] = $reportsColumnData['mo'];
                    $tmpOperators['mo']['total'] = $total_avg_mo['sum'];
                    $tmpOperators['mo']['t_mo_end'] = $total_avg_mo['T_Mo_End'];
                    $tmpOperators['mo']['avg'] = $total_avg_mo['avg'];


                    $total_avg_reg = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['reg'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['reg']['dates'] = $reportsColumnData['reg'];
                    $tmpOperators['reg']['total'] = $total_avg_reg['sum'];
                    $tmpOperators['reg']['t_mo_end'] = $total_avg_reg['T_Mo_End'];
                    $tmpOperators['reg']['avg'] = $total_avg_reg['avg'];


                    $total_avg_unreg = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['unreg'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['unreg']['dates'] = $reportsColumnData['unreg'];
                    $tmpOperators['unreg']['total'] = $total_avg_unreg['sum'];
                    $tmpOperators['unreg']['t_mo_end'] = $total_avg_unreg['T_Mo_End'];
                    $tmpOperators['unreg']['avg'] = $total_avg_unreg['avg'];


                    $total_avg_price_mo = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['price_mo'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['price_mo']['dates'] = $reportsColumnData['price_mo'];
                    $tmpOperators['price_mo']['total'] = $total_avg_price_mo['sum'];
                    $tmpOperators['price_mo']['t_mo_end'] = $total_avg_price_mo['T_Mo_End'];
                    $tmpOperators['price_mo']['avg'] = $total_avg_price_mo['avg'];

                    $total_avg_price_mo_cost = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['price_mo_cost'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['price_mo_cost']['dates'] = $reportsColumnData['price_mo_cost'];
                    $tmpOperators['price_mo_cost']['total'] = $total_avg_price_mo_cost['sum'];
                    $tmpOperators['price_mo_cost']['t_mo_end'] = $total_avg_price_mo_cost['T_Mo_End'];
                    $tmpOperators['price_mo_cost']['avg'] = $total_avg_price_mo_cost['avg'];

                    $total_avg_price_mo_mo = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['price_mo_mo'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['price_mo_mo']['dates'] = $reportsColumnData['price_mo_mo'];
                    $tmpOperators['price_mo_mo']['total'] = $total_avg_price_mo_mo['sum'];
                    $tmpOperators['price_mo_mo']['t_mo_end'] = $total_avg_price_mo_mo['T_Mo_End'];
                    $tmpOperators['price_mo_mo']['avg'] = $total_avg_price_mo_mo['avg'];


                    $total_avg_active_subs = UtilityReports::calculateTotalSubscribe($operator,$reportsColumnData['active_subs'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['active_subs']['dates'] = $reportsColumnData['active_subs'];
                    $tmpOperators['active_subs']['total'] = $total_avg_active_subs['sum'];
                    $tmpOperators['active_subs']['t_mo_end'] = $total_avg_active_subs['T_Mo_End'];
                    $tmpOperators['active_subs']['avg'] = $total_avg_active_subs['avg'];


                    $total_avg_arpu_7 = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['arpu_7'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['arpu_7']['dates'] = $reportsColumnData['arpu_7'];
                    $tmpOperators['arpu_7']['total'] = $total_avg_arpu_7['sum'];
                    $tmpOperators['arpu_7']['t_mo_end'] = $total_avg_arpu_7['T_Mo_End'];
                    $tmpOperators['arpu_7']['avg'] = $total_avg_arpu_7['avg'];


                    $total_avg_arpu_7_usd = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['arpu_7_usd'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['arpu_7_usd']['dates'] = $reportsColumnData['arpu_7_usd'];
                    $tmpOperators['arpu_7_usd']['total'] = $total_avg_arpu_7_usd['sum'];
                    $tmpOperators['arpu_7_usd']['t_mo_end'] = $total_avg_arpu_7_usd['T_Mo_End'];
                    $tmpOperators['arpu_7_usd']['avg'] = $total_avg_arpu_7_usd['avg'];


                    $total_avg_arpu_30 = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['arpu_30'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['arpu_30']['dates'] = $reportsColumnData['arpu_30'];
                    $tmpOperators['arpu_30']['total'] = $total_avg_arpu_30['sum'];
                    $tmpOperators['arpu_30']['t_mo_end'] = $total_avg_arpu_30['T_Mo_End'];
                    $tmpOperators['arpu_30']['avg'] = $total_avg_arpu_30['avg'];


                    $total_avg_arpu_30_usd = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['arpu_30_usd'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['arpu_30_usd']['dates'] = $reportsColumnData['arpu_30_usd'];
                    $tmpOperators['arpu_30_usd']['total'] = $total_avg_arpu_30_usd['sum'];
                    $tmpOperators['arpu_30_usd']['t_mo_end'] = $total_avg_arpu_30_usd['T_Mo_End'];
                    $tmpOperators['arpu_30_usd']['avg'] = $total_avg_arpu_30_usd['avg'];


                    $total_avg_reg_sub = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['reg_sub'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['reg_sub']['dates'] = $reportsColumnData['reg_sub'];
                    $tmpOperators['reg_sub']['total'] = $total_avg_reg_sub['sum'];
                    $tmpOperators['reg_sub']['t_mo_end'] = $total_avg_reg_sub['T_Mo_End'];
                    $tmpOperators['reg_sub']['avg'] = $total_avg_reg_sub['avg'];


                    $total_avg_roi = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['roi'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['roi']['dates'] = $reportsColumnData['roi'];
                    $tmpOperators['roi']['total'] = $total_avg_roi['sum'];
                    $tmpOperators['roi']['t_mo_end'] = $total_avg_roi['T_Mo_End'];
                    $tmpOperators['roi']['avg'] = $total_avg_roi['avg'];


                    $total_avg_bill = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['bill'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['bill']['dates']=$reportsColumnData['bill'];
                    $tmpOperators['bill']['total']=0;
                    $tmpOperators['bill']['t_mo_end']=0;
                    $tmpOperators['bill']['avg']=$total_avg_bill['avg'];


                    $total_avg_firstpush = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['firstpush'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['firstpush']['dates']=$reportsColumnData['firstpush'];
                    $tmpOperators['firstpush']['total']=0;
                    $tmpOperators['firstpush']['t_mo_end']=0;
                    $tmpOperators['firstpush']['avg']=$total_avg_firstpush['avg'];


                    $total_avg_dailypush = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['dailypush'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['dailypush']['dates']=$reportsColumnData['dailypush'];
                    $tmpOperators['dailypush']['total']=0;
                    $tmpOperators['dailypush']['t_mo_end']=0;
                    $tmpOperators['dailypush']['avg']=$total_avg_dailypush['avg'];


                    $total_avg_last_7_gros_rev = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['last_7_gros_rev'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['last_7_gros_rev']['dates'] = $reportsColumnData['last_7_gros_rev'];
                    $tmpOperators['last_7_gros_rev']['total'] = $total_avg_last_7_gros_rev['sum'];
                    $tmpOperators['last_7_gros_rev']['t_mo_end'] = $total_avg_last_7_gros_rev['T_Mo_End'];
                    $tmpOperators['last_7_gros_rev']['avg'] = $total_avg_last_7_gros_rev['avg'];


                    $total_avg_last_7_reg = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['last_7_reg'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['last_7_reg']['dates'] = $reportsColumnData['last_7_reg'];
                    $tmpOperators['last_7_reg']['total'] = $total_avg_last_7_reg['sum'];
                    $tmpOperators['last_7_reg']['t_mo_end'] = $total_avg_last_7_reg['T_Mo_End'];
                    $tmpOperators['last_7_reg']['avg'] = $total_avg_last_7_reg['avg'];


                    $total_avg_last_30_gros_rev = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['last_30_gros_rev'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['last_30_gros_rev']['dates'] = $reportsColumnData['last_30_gros_rev'];
                    $tmpOperators['last_30_gros_rev']['total'] = $total_avg_last_30_gros_rev['sum'];
                    $tmpOperators['last_30_gros_rev']['t_mo_end'] = $total_avg_last_30_gros_rev['T_Mo_End'];
                    $tmpOperators['last_30_gros_rev']['avg'] = $total_avg_last_30_gros_rev['avg'];


                    $total_avg_last_30_reg = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['last_30_reg'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['last_30_reg']['dates'] = $reportsColumnData['last_30_reg'];
                    $tmpOperators['last_30_reg']['total'] = $total_avg_last_30_reg['sum'];
                    $tmpOperators['last_30_reg']['t_mo_end'] = $total_avg_last_30_reg['T_Mo_End'];
                    $tmpOperators['last_30_reg']['avg'] = $total_avg_last_30_reg['avg'];


                    // dd($tmpOperators);
                    $sumemry[] = $tmpOperators;
                }
            }

            $sumOfSummaryData = UtilityReports::pnlDetailsDataSum($sumemry);

            $all_avg_arpu_7_usd = UtilityReports::calculateTotalAVG($operator,$sumOfSummaryData['arpu_7_usd']['dates'],$startColumnDateDisplay,$end_date);
            $sumOfSummaryData['arpu_7_usd']['avg'] = $all_avg_arpu_7_usd['avg'];

            $all_avg_arpu_30_usd = UtilityReports::calculateTotalAVG($operator,$sumOfSummaryData['arpu_30_usd']['dates'],$startColumnDateDisplay,$end_date);
            $sumOfSummaryData['arpu_30_usd']['avg'] = $all_avg_arpu_30_usd['avg'];

            $all_avg_bill = UtilityReports::calculateTotalAVG($operator,$sumOfSummaryData['bill']['dates'],$startColumnDateDisplay,$end_date);
            $sumOfSummaryData['bill']['avg'] = $all_avg_bill['avg'];

            $all_avg_firstpush = UtilityReports::calculateTotalAVG($operator,$sumOfSummaryData['firstpush']['dates'],$startColumnDateDisplay,$end_date);
            $sumOfSummaryData['firstpush']['avg'] = $all_avg_firstpush['avg'];

            $all_avg_dailypush = UtilityReports::calculateTotalAVG($operator,$sumOfSummaryData['dailypush']['dates'],$startColumnDateDisplay,$end_date);
            $sumOfSummaryData['dailypush']['avg'] = $all_avg_dailypush['avg'];

            // dd($sumemry);
            // dd($sumOfSummaryData);
            // dd($data);
            $date = $end_date;
            return view('report.pnlsummary', compact('date','no_of_days','sumemry','sumOfSummaryData', 'unknown_operator'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    // get pnl country daily pnl report
    public function DailyPnlReportCountryDetails(Request $request)
    {
        if(\Auth::user()->can('PNL Detail'))
        {
            $CountryWise = 1;

            // new code start
            $CountryId = $req_CountryId = $request->country;
            $CompanyId = $req_CompanyId = $request->company;
            $BusinessType = $req_BusinessType = $request->business_type;
            $filterOperator = $req_filterOperator = $request->operatorId;
            $Start_date = $req_Start_date=$request->to;
            $end_date =  $req_end_date =trim($request->from);
            if($end_date <= $Start_date)
            {
                $Start_date = $req_Start_date = trim($request->from);
                $end_date =  $req_end_date = $request->to;
            }

            $start_date = Carbon::now()->startOfMonth()->subDays(35)->format('Y-m-d');
            $startColumnDateDisplay = Carbon::now()->startOfMonth()->format('Y-m-d');
            $end_date = Carbon::now()->format('Y-m-d');
            $month = Carbon::now()->format('F Y');
            $companys = Company::get();

            $showAllOperator = true;

            if($request->filled('to') && $request->filled('from'))
            {
                $display_date_input = new Carbon($req_Start_date);
                $start_date = $req_Start_date;
                $startColumnDateDisplay = $display_date_input->format('Y-m-d');
                $end_date = $req_end_date;
                $month = $display_date_input->format('F Y');
            }else{
                $req_Start_date = date('Y-m-d', strtotime('-30 days'));
                $req_end_date = date('Y-m-d');

                $start_date_input = new Carbon($req_Start_date);
                $display_date_input = new Carbon($req_Start_date);

                $start_date = $start_date_input->subDays(35)->format('Y-m-d');
                $startColumnDateDisplay = $display_date_input->format('Y-m-d');

                $end_date = $req_end_date;
                $month = $display_date_input->format('F Y');
            }

            if($request->filled('company') && $req_CompanyId !="allcompany"  && !$request->filled('operatorId'))
            {
                $companies= Company::Find($req_CompanyId);
                $Operators_company =array();
                if(!empty($companies))
                {
                    $Operators_company = $companies->company_operators->pluck('operator_id')->toArray();
                    $Operators = Operator::Status(1)->GetOperatorByOperatorId($Operators_company)->get();
                }
                $showAllOperator = false;
            }
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
            // if($request->filled('country') && !$request->filled('operatorId'))
            // {
            //     $Operators = Operator::Status(1)->GetOperatorByCountryId($req_CountryId)->get();
            //     $showAllOperator = false;
            // }

            if (isset($req_CompanyId) && !$request->filled('country') && $request->filled('business_type') && !$request->filled('operatorId')) {
                $Countrys[0] = Country::with('operators')->Find($CountryId);
                $data = [
                    'country' => $req_CountryId,
                    'company' => $req_CompanyId,
                    'business_type' => $req_BusinessType,
                ];

                $requestobj = new Request($data);
                $ReportControllerobj = new ReportController;
                $Operators = $ReportControllerobj->userFilterBusinessTypeOperator($requestobj);
                $CountryFlag = false;
                $showAllOperator = false;
            }

            if ($request->filled('company') && $request->filled('country') && $request->filled('business_type') && !$request->filled('operatorId')) {
                $Countrys[0] = Country::with('operators')->Find($CountryId);
                $data = [
                    'country' => $req_CountryId,
                    'company' => $req_CompanyId,
                    'business_type' => $req_BusinessType,
                ];

                $requestobj = new Request($data);
                $ReportControllerobj = new ReportController;
                $Operators = $ReportControllerobj->userFilterBusinessTypeOperator($requestobj);
                $CountryFlag = false;
                $showAllOperator = false;
            }

            if($request->filled('operatorId'))
            {
                $Operators = Operator::Status(1)->GetOperatorByOperatorId($filterOperator)->get();
                $showAllOperator = false;

            }

            if($showAllOperator)
            {
                $Operators = Operator::Status(1)->get();
            }

            if(!isset($Operators))
            {
                $request->session()->flash('alert-success', 'User was successful added!');
                return redirect()->back();
            }

            $Country = Country::all()->toArray();
            $countries = array();
            if(!empty($Country))
            {
                foreach($Country as $CountryI)
                {
                    $countries[$CountryI['id']]=$CountryI;
                }
            }

            $arrayOperatorsIds = $Operators->pluck('id_operator')->toArray();
            // new code end

            // $sumemry = array();
            // $companys = Company::get();
            // $Country = Country::all()->toArray();
            // $countries = array();

            // if(!empty($Country))
            // {
            //     foreach($Country as $CountryI)
            //     {
            //         $countries[$CountryI['id']]=$CountryI;
            //     }
            // }

            // $start_date = Carbon::now()->startOfMonth()->subDays(30)->format('Y-m-d');
            // $startColumnDateDisplay = Carbon::now()->startOfMonth()->format('Y-m-d');
            // $end_date = Carbon::now()->format('Y-m-d');
            // $month = Carbon::now()->format('F Y');
            // $Countrys = Country::limit(10)->get();
            // $country_ids = $Countrys->pluck('id')->toArray();

            //select *,SUM(pnl) as s_pnl from `reports_pnls_operator_summarizes` where `country_id` in (1) and `date` between '2022-12-01' and '2022-12-05' GROUP BY date order by `country_id` asc

            //SELECT *, SUM(pnl)  FROM `reports_pnls_operator_summarizes` WHERE `date` = '2022-12-01' AND country_id = 1


            // dd($reports);
                    /* Admin Access All operator and Services */

            $user = Auth::user();
            $user_id = $user->id;
            $user_type = $user->type;
            $allowAllOperator = $user->WhowAccessAlOperator($user_type);

            if($allowAllOperator)
            {
                $reports = ReportsPnlsOperatorSummarizes::filterOperator($arrayOperatorsIds)
                ->filterDateRange($start_date,$end_date)
                ->orderBy('id_operator')
                ->get()->toArray();
                // ->toSql();

            }else{

                $UserOperatorServices =Session::get('userOperatorService');
                if(empty($UserOperatorServices))
                {
                    dd("Please Contact to admin , add Operator to your account");
                }

                $arrayOperatorsIds = $UserOperatorServices['id_operators'];
                $arrayServicesIds = $UserOperatorServices['id_services'];

                $reports = ReportsPnlsOperatorSummarizes::filterOperator($arrayOperatorsIds)
                ->filterDateRange($start_date,$end_date)
                ->orderBy('id_operator')
                ->get()->toArray();

                $userOperators = Operator::with('revenueshare','RevenushareByDate','country')->filteroperator($arrayOperatorsIds)->get()->toArray();
                foreach ($userOperators as $key => $value) {
                    if (empty($value['revenueshare'])) {
                        $userOperators[$key]['revenueshare']['merchant_revenue_share'] = 100;
                    }
                }
                $userOperatorsIDs = $this->getReportsByOperatorID($userOperators);

                $userreports = ReportSummeriseUsers::filteroperator($arrayOperatorsIds)
                ->filterDateRange($start_date,$end_date)
                ->User($user_id)
                ->orderBy('country_id')
                ->orderBy('date', 'ASC')
                ->get()->toArray();

                $userreportsByIDs = $this->getUserReportsByOperator($userreports);
            }

            // dd($reports);

            $reportsByIDs = $this->getReportsByOperator($reports);
            $datesIndividual =Utility::getRangeDates($startColumnDateDisplay,$end_date);
            $no_of_days = Utility::getRangeDateNo($datesIndividual);

            if(isset($userreportsByIDs) && !empty($userreportsByIDs)){
                foreach ($userreportsByIDs as $key1 => $value1) {
                    foreach ($value1 as $key2 => $value2) {
                        $reportsByIDs[$key1][$key2]['rev'] = $value2['gros_rev'];
                        $reportsByIDs[$key1][$key2]['rev_usd'] = $value2['gros_rev']*$userOperatorsIDs[$key1]['country']['usd'];
                        $reportsByIDs[$key1][$key2]['share'] = $value2['gros_rev']*$userOperatorsIDs[$key1]['country']['usd']*($userOperatorsIDs[$key1]['revenueshare']['merchant_revenue_share']/100);
                        $reportsByIDs[$key1][$key2]['lshare'] = $value2['gros_rev']*($userOperatorsIDs[$key1]['revenueshare']['merchant_revenue_share']/100);
                    }
                }
            }
            // dd($reportsByIDs);

            if(!empty($Operators))
            {
                foreach($Operators as $operator)
                {
                    $tmpOperators=array();
                    $country_id  = $operator->country_id;
                    $tmpOperators['operator'] = $operator;
                    $contain_id = Arr::exists($countries, $country_id);
                    $countryDetails = [];

                    if($contain_id )
                    {
                        $tmpOperators['country'] = $countries[$country_id];
                        $countryDetails = $countries[$country_id];
                    }

                    $reportsColumnData = $this->getPNLReportsDateWise($operator,$no_of_days,$reportsByIDs,$countryDetails);

                    $tmpOperators['month_string'] = $month;
                    $tmpOperators['last_update'] = $reportsColumnData['last_update'];
                    $tmpOperators['hostingCost'] = isset($operator->hostingCost) ? $operator->hostingCost : 0;
                    $tmpOperators['contentCost'] = isset($operator->content) ? $operator->content : 0;
                    $tmpOperators['rndCost'] = isset($operator->rnd) ? $operator->rnd : 0;
                    $tmpOperators['bdCost'] = 2.5;
                    $tmpOperators['marketCost'] = 1.5;
                    $tmpOperators['miscCost'] = isset($operator->miscCost) ? $operator->miscCost : 0;
                    $tmpOperators['vatTax'] = isset($operator->vat) ? $operator->vat : 0;
                    $tmpOperators['whtTax'] = isset($operator->wht) ? $operator->wht : 0;
                    $tmpOperators['miscTax'] = isset($operator->miscTax) ? $operator->miscTax : 0;
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


                    $total_avg_market_cost = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['market_cost'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['market_cost']['dates'] = $reportsColumnData['market_cost'];
                    $tmpOperators['market_cost']['total'] = $total_avg_market_cost['sum'];
                    $tmpOperators['market_cost']['t_mo_end'] = $total_avg_market_cost['T_Mo_End'];
                    $tmpOperators['market_cost']['avg'] = $total_avg_market_cost['avg'];


                    $total_avg_misc_cost = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['misc_cost'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['misc_cost']['dates'] = $reportsColumnData['misc_cost'];
                    $tmpOperators['misc_cost']['total'] = $total_avg_misc_cost['sum'];
                    $tmpOperators['misc_cost']['t_mo_end'] = $total_avg_misc_cost['T_Mo_End'];
                    $tmpOperators['misc_cost']['avg'] = $total_avg_misc_cost['avg'];


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


                    $total_avg_net_after_tax = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['net_after_tax'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['net_after_tax']['dates'] = $reportsColumnData['net_after_tax'];
                    $tmpOperators['net_after_tax']['total'] = $total_avg_net_after_tax['sum'];
                    $tmpOperators['net_after_tax']['t_mo_end'] = $total_avg_net_after_tax['T_Mo_End'];
                    $tmpOperators['net_after_tax']['avg'] = $total_avg_net_after_tax['avg'];


                    $total_avg_net_revenue_after_tax = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['net_revenue_after_tax'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['net_revenue_after_tax']['dates'] = $reportsColumnData['net_revenue_after_tax'];
                    $tmpOperators['net_revenue_after_tax']['total'] = $total_avg_net_revenue_after_tax['sum'];
                    $tmpOperators['net_revenue_after_tax']['t_mo_end'] = $total_avg_net_revenue_after_tax['T_Mo_End'];
                    $tmpOperators['net_revenue_after_tax']['avg'] = $total_avg_net_revenue_after_tax['avg'];


                    $total_avg_br = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['br'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['br']['dates'] = $reportsColumnData['br'];
                    $tmpOperators['br']['total'] = $total_avg_br['sum'];
                    $tmpOperators['br']['t_mo_end'] = $total_avg_br['T_Mo_End'];
                    $tmpOperators['br']['avg'] = $total_avg_br['avg'];


                    $total_avg_fp = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['fp'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['fp']['dates'] = $reportsColumnData['fp'];
                    $tmpOperators['fp']['total'] = $total_avg_fp['sum'];
                    $tmpOperators['fp']['t_mo_end'] = $total_avg_fp['T_Mo_End'];
                    $tmpOperators['fp']['avg'] = $total_avg_fp['avg'];


                    $total_avg_fp_success = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['fp_success'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['fp_success']['dates'] = $reportsColumnData['fp_success'];
                    $tmpOperators['fp_success']['total'] = $total_avg_fp_success['sum'];
                    $tmpOperators['fp_success']['t_mo_end'] = $total_avg_fp_success['T_Mo_End'];
                    $tmpOperators['fp_success']['avg'] = $total_avg_fp_success['avg'];


                    $total_avg_fp_failed = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['fp_failed'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['fp_failed']['dates'] = $reportsColumnData['fp_failed'];
                    $tmpOperators['fp_failed']['total'] = $total_avg_fp_failed['sum'];
                    $tmpOperators['fp_failed']['t_mo_end'] = $total_avg_fp_failed['T_Mo_End'];
                    $tmpOperators['fp_failed']['avg'] = $total_avg_fp_failed['avg'];


                    $total_avg_dp = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['dp'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['dp']['dates'] = $reportsColumnData['dp'];
                    $tmpOperators['dp']['total'] = $total_avg_dp['sum'];
                    $tmpOperators['dp']['t_mo_end'] = $total_avg_dp['T_Mo_End'];
                    $tmpOperators['dp']['avg'] = $total_avg_dp['avg'];


                    $total_avg_dp_success = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['dp_success'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['dp_success']['dates'] = $reportsColumnData['dp_success'];
                    $tmpOperators['dp_success']['total'] = $total_avg_dp_success['sum'];
                    $tmpOperators['dp_success']['t_mo_end'] = $total_avg_dp_success['T_Mo_End'];
                    $tmpOperators['dp_success']['avg'] = $total_avg_dp_success['avg'];


                    $total_avg_dp_failed = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['dp_failed'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['dp_failed']['dates'] = $reportsColumnData['dp_failed'];
                    $tmpOperators['dp_failed']['total'] = $total_avg_dp_failed['sum'];
                    $tmpOperators['dp_failed']['t_mo_end'] = $total_avg_dp_failed['T_Mo_End'];
                    $tmpOperators['dp_failed']['avg'] = $total_avg_dp_failed['avg'];


                    $total_avg_renewal = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['renewal'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['renewal']['dates'] = $reportsColumnData['renewal'];
                    $tmpOperators['renewal']['total'] = $total_avg_renewal['sum'];
                    $tmpOperators['renewal']['t_mo_end'] = $total_avg_renewal['T_Mo_End'];
                    $tmpOperators['renewal']['avg'] = $total_avg_renewal['avg'];


                    $total_avg_vat = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['vat'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['vat']['dates'] = $reportsColumnData['vat'];
                    $tmpOperators['vat']['total'] = $total_avg_vat['sum'];
                    $tmpOperators['vat']['t_mo_end'] = $total_avg_vat['T_Mo_End'];
                    $tmpOperators['vat']['avg'] = $total_avg_vat['avg'];


                    $total_avg_spec_tax = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['spec_tax'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['spec_tax']['dates'] = $reportsColumnData['spec_tax'];
                    $tmpOperators['spec_tax']['total'] = $total_avg_spec_tax['sum'];
                    $tmpOperators['spec_tax']['t_mo_end'] = $total_avg_spec_tax['T_Mo_End'];
                    $tmpOperators['spec_tax']['avg'] = $total_avg_spec_tax['avg'];


                    $total_avg_government_cost = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['government_cost'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['government_cost']['dates'] = $reportsColumnData['government_cost'];
                    $tmpOperators['government_cost']['total'] = $total_avg_government_cost['sum'];
                    $tmpOperators['government_cost']['t_mo_end'] = $total_avg_government_cost['T_Mo_End'];
                    $tmpOperators['government_cost']['avg'] = $total_avg_government_cost['avg'];


                    $total_avg_dealer_commision = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['dealer_commision'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['dealer_commision']['dates'] = $reportsColumnData['dealer_commision'];
                    $tmpOperators['dealer_commision']['total'] = $total_avg_dealer_commision['sum'];
                    $tmpOperators['dealer_commision']['t_mo_end'] = $total_avg_dealer_commision['T_Mo_End'];
                    $tmpOperators['dealer_commision']['avg'] = $total_avg_dealer_commision['avg'];


                    $total_avg_wht = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['wht'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['wht']['dates'] = $reportsColumnData['wht'];
                    $tmpOperators['wht']['total'] = $total_avg_wht['sum'];
                    $tmpOperators['wht']['t_mo_end'] = $total_avg_wht['T_Mo_End'];
                    $tmpOperators['wht']['avg'] = $total_avg_wht['avg'];


                    $total_avg_misc_tax = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['misc_tax'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['misc_tax']['dates'] = $reportsColumnData['misc_tax'];
                    $tmpOperators['misc_tax']['total'] = $total_avg_misc_tax['sum'];
                    $tmpOperators['misc_tax']['t_mo_end'] = $total_avg_misc_tax['T_Mo_End'];
                    $tmpOperators['misc_tax']['avg'] = $total_avg_misc_tax['avg'];


                    $total_avg_other_tax = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['other_tax'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['other_tax']['dates'] = $reportsColumnData['other_tax'];
                    $tmpOperators['other_tax']['total'] = $total_avg_other_tax['sum'];
                    $tmpOperators['other_tax']['t_mo_end'] = $total_avg_other_tax['T_Mo_End'];
                    $tmpOperators['other_tax']['avg'] = $total_avg_other_tax['avg'];


                    $total_avg_uso = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['uso'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['uso']['dates'] = $reportsColumnData['uso'];
                    $tmpOperators['uso']['total'] = $total_avg_uso['sum'];
                    $tmpOperators['uso']['t_mo_end'] = $total_avg_uso['T_Mo_End'];
                    $tmpOperators['uso']['avg'] = $total_avg_uso['avg'];


                    $total_avg_agre_paxxa = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['agre_paxxa'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['agre_paxxa']['dates'] = $reportsColumnData['agre_paxxa'];
                    $tmpOperators['agre_paxxa']['total'] = $total_avg_agre_paxxa['sum'];
                    $tmpOperators['agre_paxxa']['t_mo_end'] = $total_avg_agre_paxxa['T_Mo_End'];
                    $tmpOperators['agre_paxxa']['avg'] = $total_avg_agre_paxxa['avg'];


                    $total_avg_sbaf = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['sbaf'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['sbaf']['dates'] = $reportsColumnData['sbaf'];
                    $tmpOperators['sbaf']['total'] = $total_avg_sbaf['sum'];
                    $tmpOperators['sbaf']['t_mo_end'] = $total_avg_sbaf['T_Mo_End'];
                    $tmpOperators['sbaf']['avg'] = $total_avg_sbaf['avg'];


                    $total_avg_clicks = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['clicks'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['clicks']['dates'] = $reportsColumnData['clicks'];
                    $tmpOperators['clicks']['total'] = $total_avg_clicks['sum'];
                    $tmpOperators['clicks']['t_mo_end'] = $total_avg_clicks['T_Mo_End'];
                    $tmpOperators['clicks']['avg'] = $total_avg_clicks['avg'];


                    $total_avg_ratio_for_cpa = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['ratio_for_cpa'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['ratio_for_cpa']['dates'] = $reportsColumnData['ratio_for_cpa'];
                    $tmpOperators['ratio_for_cpa']['total'] = $total_avg_ratio_for_cpa['sum'];
                    $tmpOperators['ratio_for_cpa']['t_mo_end'] = $total_avg_ratio_for_cpa['T_Mo_End'];
                    $tmpOperators['ratio_for_cpa']['avg'] = $total_avg_ratio_for_cpa['avg'];


                    $total_avg_cpa_price = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['cpa_price'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['cpa_price']['dates'] = $reportsColumnData['cpa_price'];
                    $tmpOperators['cpa_price']['total'] = $total_avg_cpa_price['sum'];
                    $tmpOperators['cpa_price']['t_mo_end'] = $total_avg_cpa_price['T_Mo_End'];
                    $tmpOperators['cpa_price']['avg'] = $total_avg_cpa_price['avg'];


                    $total_avg_cr_mo_clicks = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['cr_mo_clicks'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['cr_mo_clicks']['dates'] = $reportsColumnData['cr_mo_clicks'];
                    $tmpOperators['cr_mo_clicks']['total'] = $total_avg_cr_mo_clicks['sum'];
                    $tmpOperators['cr_mo_clicks']['t_mo_end'] = $total_avg_cr_mo_clicks['T_Mo_End'];
                    $tmpOperators['cr_mo_clicks']['avg'] = $total_avg_cr_mo_clicks['avg'];


                    $total_avg_cr_mo_landing = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['cr_mo_landing'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['cr_mo_landing']['dates'] = $reportsColumnData['cr_mo_landing'];
                    $tmpOperators['cr_mo_landing']['total'] = $total_avg_cr_mo_landing['sum'];
                    $tmpOperators['cr_mo_landing']['t_mo_end'] = $total_avg_cr_mo_landing['T_Mo_End'];
                    $tmpOperators['cr_mo_landing']['avg'] = $total_avg_cr_mo_landing['avg'];


                    $total_avg_landing = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['landing'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['landing']['dates'] = $reportsColumnData['landing'];
                    $tmpOperators['landing']['total'] = $total_avg_landing['sum'];
                    $tmpOperators['landing']['t_mo_end'] = $total_avg_landing['T_Mo_End'];
                    $tmpOperators['landing']['avg'] = $total_avg_landing['avg'];


                    $total_avg_mo = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['mo'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['mo']['dates'] = $reportsColumnData['mo'];
                    $tmpOperators['mo']['total'] = $total_avg_mo['sum'];
                    $tmpOperators['mo']['t_mo_end'] = $total_avg_mo['T_Mo_End'];
                    $tmpOperators['mo']['avg'] = $total_avg_mo['avg'];


                    $total_avg_reg = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['reg'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['reg']['dates'] = $reportsColumnData['reg'];
                    $tmpOperators['reg']['total'] = $total_avg_reg['sum'];
                    $tmpOperators['reg']['t_mo_end'] = $total_avg_reg['T_Mo_End'];
                    $tmpOperators['reg']['avg'] = $total_avg_reg['avg'];


                    $total_avg_unreg = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['unreg'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['unreg']['dates'] = $reportsColumnData['unreg'];
                    $tmpOperators['unreg']['total'] = $total_avg_unreg['sum'];
                    $tmpOperators['unreg']['t_mo_end'] = $total_avg_unreg['T_Mo_End'];
                    $tmpOperators['unreg']['avg'] = $total_avg_unreg['avg'];


                    $total_avg_price_mo = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['price_mo'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['price_mo']['dates'] = $reportsColumnData['price_mo'];
                    $tmpOperators['price_mo']['total'] = $total_avg_price_mo['sum'];
                    $tmpOperators['price_mo']['t_mo_end'] = $total_avg_price_mo['T_Mo_End'];
                    $tmpOperators['price_mo']['avg'] = $total_avg_price_mo['avg'];

                    $total_avg_price_mo_cost = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['price_mo_cost'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['price_mo_cost']['dates'] = $reportsColumnData['price_mo_cost'];
                    $tmpOperators['price_mo_cost']['total'] = $total_avg_price_mo_cost['sum'];
                    $tmpOperators['price_mo_cost']['t_mo_end'] = $total_avg_price_mo_cost['T_Mo_End'];
                    $tmpOperators['price_mo_cost']['avg'] = $total_avg_price_mo_cost['avg'];

                    $total_avg_price_mo_mo = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['price_mo_mo'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['price_mo_mo']['dates'] = $reportsColumnData['price_mo_mo'];
                    $tmpOperators['price_mo_mo']['total'] = $total_avg_price_mo_mo['sum'];
                    $tmpOperators['price_mo_mo']['t_mo_end'] = $total_avg_price_mo_mo['T_Mo_End'];
                    $tmpOperators['price_mo_mo']['avg'] = $total_avg_price_mo_mo['avg'];


                    $total_avg_active_subs = UtilityReports::calculateTotalSubscribe($operator,$reportsColumnData['active_subs'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['active_subs']['dates'] = $reportsColumnData['active_subs'];
                    $tmpOperators['active_subs']['total'] = $total_avg_active_subs['sum'];
                    $tmpOperators['active_subs']['t_mo_end'] = $total_avg_active_subs['T_Mo_End'];
                    $tmpOperators['active_subs']['avg'] = $total_avg_active_subs['avg'];


                    $total_avg_arpu_7 = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['arpu_7'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['arpu_7']['dates'] = $reportsColumnData['arpu_7'];
                    $tmpOperators['arpu_7']['total'] = $total_avg_arpu_7['sum'];
                    $tmpOperators['arpu_7']['t_mo_end'] = $total_avg_arpu_7['T_Mo_End'];
                    $tmpOperators['arpu_7']['avg'] = $total_avg_arpu_7['avg'];


                    $total_avg_arpu_7_usd = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['arpu_7_usd'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['arpu_7_usd']['dates'] = $reportsColumnData['arpu_7_usd'];
                    $tmpOperators['arpu_7_usd']['total'] = $total_avg_arpu_7_usd['sum'];
                    $tmpOperators['arpu_7_usd']['t_mo_end'] = $total_avg_arpu_7_usd['T_Mo_End'];
                    $tmpOperators['arpu_7_usd']['avg'] = $total_avg_arpu_7_usd['avg'];


                    $total_avg_arpu_30 = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['arpu_30'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['arpu_30']['dates'] = $reportsColumnData['arpu_30'];
                    $tmpOperators['arpu_30']['total'] = $total_avg_arpu_30['sum'];
                    $tmpOperators['arpu_30']['t_mo_end'] = $total_avg_arpu_30['T_Mo_End'];
                    $tmpOperators['arpu_30']['avg'] = $total_avg_arpu_30['avg'];


                    $total_avg_arpu_30_usd = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['arpu_30_usd'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['arpu_30_usd']['dates'] = $reportsColumnData['arpu_30_usd'];
                    $tmpOperators['arpu_30_usd']['total'] = $total_avg_arpu_30_usd['sum'];
                    $tmpOperators['arpu_30_usd']['t_mo_end'] = $total_avg_arpu_30_usd['T_Mo_End'];
                    $tmpOperators['arpu_30_usd']['avg'] = $total_avg_arpu_30_usd['avg'];


                    $total_avg_reg_sub = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['reg_sub'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['reg_sub']['dates'] = $reportsColumnData['reg_sub'];
                    $tmpOperators['reg_sub']['total'] = $total_avg_reg_sub['sum'];
                    $tmpOperators['reg_sub']['t_mo_end'] = $total_avg_reg_sub['T_Mo_End'];
                    $tmpOperators['reg_sub']['avg'] = $total_avg_reg_sub['avg'];


                    $total_avg_roi = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['roi'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['roi']['dates'] = $reportsColumnData['roi'];
                    $tmpOperators['roi']['total'] = $total_avg_roi['sum'];
                    $tmpOperators['roi']['t_mo_end'] = $total_avg_roi['T_Mo_End'];
                    $tmpOperators['roi']['avg'] = $total_avg_roi['avg'];


                    $total_avg_bill = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['bill'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['bill']['dates']=$reportsColumnData['bill'];
                    $tmpOperators['bill']['total']=0;
                    $tmpOperators['bill']['t_mo_end']=0;
                    $tmpOperators['bill']['avg']=$total_avg_bill['avg'];


                    $total_avg_firstpush = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['firstpush'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['firstpush']['dates']=$reportsColumnData['firstpush'];
                    $tmpOperators['firstpush']['total']=0;
                    $tmpOperators['firstpush']['t_mo_end']=0;
                    $tmpOperators['firstpush']['avg']=$total_avg_firstpush['avg'];


                    $total_avg_dailypush = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['dailypush'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['dailypush']['dates']=$reportsColumnData['dailypush'];
                    $tmpOperators['dailypush']['total']=0;
                    $tmpOperators['dailypush']['t_mo_end']=0;
                    $tmpOperators['dailypush']['avg']=$total_avg_dailypush['avg'];


                    $total_avg_last_7_gros_rev = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['last_7_gros_rev'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['last_7_gros_rev']['dates'] = $reportsColumnData['last_7_gros_rev'];
                    $tmpOperators['last_7_gros_rev']['total'] = $total_avg_last_7_gros_rev['sum'];
                    $tmpOperators['last_7_gros_rev']['t_mo_end'] = $total_avg_last_7_gros_rev['T_Mo_End'];
                    $tmpOperators['last_7_gros_rev']['avg'] = $total_avg_last_7_gros_rev['avg'];


                    $total_avg_last_7_reg = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['last_7_reg'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['last_7_reg']['dates'] = $reportsColumnData['last_7_reg'];
                    $tmpOperators['last_7_reg']['total'] = $total_avg_last_7_reg['sum'];
                    $tmpOperators['last_7_reg']['t_mo_end'] = $total_avg_last_7_reg['T_Mo_End'];
                    $tmpOperators['last_7_reg']['avg'] = $total_avg_last_7_reg['avg'];


                    $total_avg_last_30_gros_rev = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['last_30_gros_rev'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['last_30_gros_rev']['dates'] = $reportsColumnData['last_30_gros_rev'];
                    $tmpOperators['last_30_gros_rev']['total'] = $total_avg_last_30_gros_rev['sum'];
                    $tmpOperators['last_30_gros_rev']['t_mo_end'] = $total_avg_last_30_gros_rev['T_Mo_End'];
                    $tmpOperators['last_30_gros_rev']['avg'] = $total_avg_last_30_gros_rev['avg'];


                    $total_avg_last_30_reg = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['last_30_reg'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['last_30_reg']['dates'] = $reportsColumnData['last_30_reg'];
                    $tmpOperators['last_30_reg']['total'] = $total_avg_last_30_reg['sum'];
                    $tmpOperators['last_30_reg']['t_mo_end'] = $total_avg_last_30_reg['T_Mo_End'];
                    $tmpOperators['last_30_reg']['avg'] = $total_avg_last_30_reg['avg'];

                    // dd($tmpOperators);
                    $sumemry[] = $tmpOperators;
                }
            }

            // Country Sum from Operator array
            $displayCountries = array();
            $SelectedCountries = array();
            $RowCountryData = array();

            if(!empty($sumemry))
            {
                foreach ($sumemry as $key => $sumemries) {
                    //dd($sumemries);
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
                    $dataRowSum = UtilityReports::pnlDetailsDataSum($displayCountries[$country_id]);

                    $country_avg_arpu_7_usd = UtilityReports::calculateTotalAVG($operator,$dataRowSum['arpu_7_usd']['dates'],$startColumnDateDisplay,$end_date);
                    $dataRowSum['arpu_7_usd']['avg'] = $country_avg_arpu_7_usd['avg'];

                    $country_avg_arpu_30_usd = UtilityReports::calculateTotalAVG($operator,$dataRowSum['arpu_30_usd']['dates'],$startColumnDateDisplay,$end_date);
                    $dataRowSum['arpu_30_usd']['avg'] = $country_avg_arpu_30_usd['avg'];

                    $country_avg_bill = UtilityReports::calculateTotalAVG($operator,$dataRowSum['bill']['dates'],$startColumnDateDisplay,$end_date);
                    $dataRowSum['bill']['avg']=$country_avg_bill['avg'];

                    $country_avg_firstpush = UtilityReports::calculateTotalAVG($operator,$dataRowSum['firstpush']['dates'],$startColumnDateDisplay,$end_date);
                    $dataRowSum['firstpush']['avg']=$country_avg_firstpush['avg'];

                    $country_avg_dailypush = UtilityReports::calculateTotalAVG($operator,$dataRowSum['dailypush']['dates'],$startColumnDateDisplay,$end_date);
                    $dataRowSum['dailypush']['avg']=$country_avg_dailypush['avg'];
                    // if(!empty($dataRowSum)){
                    //     foreach ($dataRowSum as $key1 => $value1) {
                    //         if($key1 == 'roi'){
                    //             foreach ($value1 as $key2 => $value2) {
                    //                 if($key2 == 'dates'){
                    //                     foreach ($value2 as $key3 => $value3) {
                    //                         $roi_arpu = ($dataRowSum['reg_sub']['dates'][$key3]['value'] == 0) ? (float)0 : ($dataRowSum['gros_rev_usd']['dates'][$key3]['value'] / $dataRowSum['reg_sub']['dates'][$key3]['value']);
                    //                         $price_mo = ($dataRowSum['mo']['dates'][$key3]['value'] == 0) ? (float)0 : ($dataRowSum['cost_campaign']['dates'][$key3]['value'] / $dataRowSum['mo']['dates'][$key3]['value']);
                    //                         $dataRowSum['roi']['dates'][$key3]['value'] = ($roi_arpu == 0) ? (float)0 : ($price_mo / $roi_arpu);
                    //                     }
                    //                 }
                    //             }
                    //         }
                    //     }
                    // }
                    $tempDataArr['country'] = $SelectedCountry;
                    $tempDataArr['month_string'] = $month;
                    $tempDataArr = array_merge($tempDataArr,$dataRowSum);
                    $RowCountryData[] = $tempDataArr;
                }
            }

            $sumemry = $RowCountryData;
            $sumOfSummaryData = UtilityReports::pnlDetailsDataSum($sumemry);

            $all_avg_arpu_7_usd = UtilityReports::calculateTotalAVG($operator,$sumOfSummaryData['arpu_7_usd']['dates'],$startColumnDateDisplay,$end_date);
            $sumOfSummaryData['arpu_7_usd']['avg'] = $all_avg_arpu_7_usd['avg'];

            $all_avg_arpu_30_usd = UtilityReports::calculateTotalAVG($operator,$sumOfSummaryData['arpu_30_usd']['dates'],$startColumnDateDisplay,$end_date);
            $sumOfSummaryData['arpu_30_usd']['avg'] = $all_avg_arpu_30_usd['avg'];

            $all_avg_bill = UtilityReports::calculateTotalAVG($operator,$sumOfSummaryData['bill']['dates'],$startColumnDateDisplay,$end_date);
            $sumOfSummaryData['bill']['avg']=$all_avg_bill['avg'];

            $all_avg_firstpush = UtilityReports::calculateTotalAVG($operator,$sumOfSummaryData['firstpush']['dates'],$startColumnDateDisplay,$end_date);
            $sumOfSummaryData['firstpush']['avg']=$all_avg_firstpush['avg'];

            $all_avg_dailypush = UtilityReports::calculateTotalAVG($operator,$sumOfSummaryData['dailypush']['dates'],$startColumnDateDisplay,$end_date);
            $sumOfSummaryData['dailypush']['avg']=$all_avg_dailypush['avg'];
            // dd($sumemry);
            // dd($sumOfSummaryData);
            $date = $end_date;

            return view('report.daily_country_pnlreport', compact('date','no_of_days','sumemry','sumOfSummaryData','CountryWise'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    // get pnl Company daily pnl report
    public function DailyPnlReportCompanyDetails(Request $request)
    {
        if(\Auth::user()->can('PNL Detail'))
        {
            $CompanyWise = 1;

            // get filtre request
            $CountryId = $req_CountryId = $request->country;
            $CompanyId = $req_CompanyId = $request->company;
            $BusinessType = $req_BusinessType = $request->business_type;
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
                // $end_date_input = new Carbon($req_end_date);

                $start_date = $start_date_input->subDays(35)->format('Y-m-d');
                $startColumnDateDisplay = $display_date_input->format('Y-m-d');
                //dd($startColumnDateDisplay."ppp");
                $end_date = $req_end_date;
                $month = $display_date_input->format('F Y');
            }else{
                $req_Start_date = date('Y-m-d', strtotime('-30 days'));
                $req_end_date = date('Y-m-d');

                $start_date_input = new Carbon($req_Start_date);
                $display_date_input = new Carbon($req_Start_date);

                $start_date = $start_date_input->subDays(35)->format('Y-m-d');
                $startColumnDateDisplay = $display_date_input->format('Y-m-d');

                $end_date = $req_end_date;
                $month = $display_date_input->format('F Y');
            }

            if($request->filled('company') && $req_CompanyId !="allcompany")
            {
                $companies = Company::Find($req_CompanyId);
                $Operators_company = array();

                if(!empty($companies))
                {
                    $Operators_company = $companies->company_operators->pluck('operator_id')->toArray();
                    $Operators = Operator::Status(1)->GetOperatorByOperatorId($Operators_company)->get();
                }

                $showAllOperator = false;
            }

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

            if (isset($req_CompanyId) && !$request->filled('country') && $request->filled('business_type') && !$request->filled('operatorId')) {
                $Countrys[0] = Country::with('operators')->Find($CountryId);
                $data = [
                    'country' => $req_CountryId,
                    'company' => $req_CompanyId,
                    'business_type' => $req_BusinessType,
                ];

                $requestobj = new Request($data);
                $ReportControllerobj = new ReportController;
                $Operators = $ReportControllerobj->userFilterBusinessTypeOperator($requestobj);
                $CountryFlag = false;
                $showAllOperator = false;
            }

            if ($request->filled('company') && $request->filled('country') && $request->filled('business_type') && !$request->filled('operatorId')) {
                $Countrys[0] = Country::with('operators')->Find($CountryId);
                $data = [
                    'country' => $req_CountryId,
                    'company' => $req_CompanyId,
                    'business_type' => $req_BusinessType,
                ];

                $requestobj = new Request($data);
                $ReportControllerobj = new ReportController;
                $Operators = $ReportControllerobj->userFilterBusinessTypeOperator($requestobj);
                $CountryFlag = false;
                $showAllOperator = false;
            }

            if($request->filled('operatorId'))
            {
                $Operators = Operator::Status(1)->GetOperatorByOperatorId($filterOperator)
                            ->get();
                $showAllOperator = false;
            }
            // if($request->filled('country'))
            // {
            //     $Operators = Operator::Status(1)->GetOperatorByCountryId($req_CountryId)->get();
            //     $showAllOperator = false;
            // }
            if($showAllOperator)
            {
                $Operators = Operator::Status(1)->get();
                //$Operators = Operator::Status(1)->filterOperatorID(56)->get();
            }

            $sumemry = array();
            $Country = Country::all()->toArray();
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
                    if(!isset($companies[$company_id])) continue;
                    $com_operators[$operator_id] = $companies[$company_id];
                }
            }

            // $start_date = Carbon::now()->startOfMonth()->subDays(30)->format('Y-m-d');
            // $startColumnDateDisplay = Carbon::now()->startOfMonth()->format('Y-m-d');
            // $end_date = Carbon::now()->format('Y-m-d');
            // $month = Carbon::now()->format('F Y');

            // $Companys = Company::get();
            // $operator_ids = [];

            // if(!empty($Companys))
            // {
            //     foreach($Companys as $company)
            //     {
            //         $com_opt_ids = $company->company_operators;
            //         $operator_ids = array_merge($operator_ids,$com_opt_ids->pluck('operator_id')->toArray());
            //     }
            // }

            $operator_ids = $Operators->pluck('id_operator')->toArray();

            /* Admin Access All operator and Services */

            $user = Auth::user();
            $user_id = $user->id;
            $user_type = $user->type;
            $allowAllOperator = $user->WhowAccessAlOperator($user_type);

            if($allowAllOperator)
            {
                $reports = ReportsPnlsOperatorSummarizes::filterOperator($operator_ids)
                        ->filterDateRange($start_date,$end_date)
                        ->orderBy('id_operator')
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
                        ->orderBy('id_operator')
                        ->orderBy('date', 'ASC')
                        ->get()->toArray();

                $userOperators = Operator::with('revenueshare','RevenushareByDate','country')->filteroperator($arrayOperatorsIds)->get()->toArray();
                foreach ($userOperators as $key => $value) {
                    if (empty($value['revenueshare'])) {
                        $userOperators[$key]['revenueshare']['merchant_revenue_share'] = 100;
                    }
                }
                $userOperatorsIDs = $this->getReportsByOperatorID($userOperators);

                $userreports = ReportSummeriseUsers::filteroperator($arrayOperatorsIds)
                ->filterDateRange($start_date,$end_date)
                ->User($user_id)
                ->orderBy('country_id')
                ->orderBy('date', 'ASC')
                ->get()->toArray();

                $userreportsByIDs = $this->getUserReportsByOperator($userreports);

            }

            // $reports = ReportsPnlsOperatorSummarizes::filterOperator($operator_ids)
            // ->filterDateRange($start_date,$end_date)
            // ->orderBy('id_operator')
            // ->orderBy('date', 'ASC')
            // ->get()->toArray();
            // dd($reports);

            // $Operators = Operator::Status(1)->get();
            $reportsByIDs = $this->getReportsByOperator($reports);
            $datesIndividual =Utility::getRangeDates($startColumnDateDisplay,$end_date);
            $no_of_days = Utility::getRangeDateNo($datesIndividual);

            if(isset($userreportsByIDs) && !empty($userreportsByIDs)){
                foreach ($userreportsByIDs as $key1 => $value1) {
                    foreach ($value1 as $key2 => $value2) {
                        $reportsByIDs[$key1][$key2]['rev'] = $value2['gros_rev'];
                        $reportsByIDs[$key1][$key2]['rev_usd'] = $value2['gros_rev']*$userOperatorsIDs[$key1]['country']['usd'];
                        $reportsByIDs[$key1][$key2]['share'] = $value2['gros_rev']*$userOperatorsIDs[$key1]['country']['usd']*($userOperatorsIDs[$key1]['revenueshare']['merchant_revenue_share']/100);
                        $reportsByIDs[$key1][$key2]['lshare'] = $value2['gros_rev']*($userOperatorsIDs[$key1]['revenueshare']['merchant_revenue_share']/100);
                    }
                }
            }

            // dd($com_operators);

            if(!empty($Operators))
            {
                foreach($Operators as $operator)
                {
                    $tmpOperators = array();
                    $OperatorCountry = array();

                    $operator_id = $operator->id_operator;
                    $tmpOperators['operator'] = $operator;
                    if (!isset($com_operators[$operator_id])) {
                        // If the operator ID is not found in the com_operators array,
                        // store the operator ID in the 'unknown' key
                        $tmpOperators['unknown'] = $operator_id;
                    } else {
                        $tmpOperators['company'] = $com_operators[$operator_id];
                    }
                    $country_id  = $operator->country_id;
                    $contain_id = Arr::exists($countries, $country_id);

                    if($contain_id)
                    {
                        $tmpOperators['country']=$countries[$country_id];
                        $OperatorCountry = $countries[$country_id];
                    }

                    $reportsColumnData = $this->getPNLReportsDateWise($operator,$no_of_days,$reportsByIDs,$OperatorCountry);
                    // dd($reportsColumnData);
                    $tmpOperators['month_string'] = $month;
                    $tmpOperators['last_update'] = $reportsColumnData['last_update'];
                    $tmpOperators['hostingCost'] = isset($operator->hostingCost) ? $operator->hostingCost : 0;
                    $tmpOperators['contentCost'] = isset($operator->content) ? $operator->content : 0;
                    $tmpOperators['rndCost'] = isset($operator->rnd) ? $operator->rnd : 0;
                    $tmpOperators['bdCost'] = 2.5;
                    $tmpOperators['marketCost'] = 1.5;
                    $tmpOperators['miscCost'] = isset($operator->miscCost) ? $operator->miscCost : 0;
                    $tmpOperators['vatTax'] = isset($operator->vat) ? $operator->vat : 0;
                    $tmpOperators['whtTax'] = isset($operator->wht) ? $operator->wht : 0;
                    $tmpOperators['miscTax'] = isset($operator->miscTax) ? $operator->miscTax : 0;

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


                    $total_avg_market_cost = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['market_cost'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['market_cost']['dates'] = $reportsColumnData['market_cost'];
                    $tmpOperators['market_cost']['total'] = $total_avg_market_cost['sum'];
                    $tmpOperators['market_cost']['t_mo_end'] = $total_avg_market_cost['T_Mo_End'];
                    $tmpOperators['market_cost']['avg'] = $total_avg_market_cost['avg'];


                    $total_avg_misc_cost = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['misc_cost'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['misc_cost']['dates'] = $reportsColumnData['misc_cost'];
                    $tmpOperators['misc_cost']['total'] = $total_avg_misc_cost['sum'];
                    $tmpOperators['misc_cost']['t_mo_end'] = $total_avg_misc_cost['T_Mo_End'];
                    $tmpOperators['misc_cost']['avg'] = $total_avg_misc_cost['avg'];


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


                    $total_avg_net_after_tax = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['net_after_tax'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['net_after_tax']['dates'] = $reportsColumnData['net_after_tax'];
                    $tmpOperators['net_after_tax']['total'] = $total_avg_net_after_tax['sum'];
                    $tmpOperators['net_after_tax']['t_mo_end'] = $total_avg_net_after_tax['T_Mo_End'];
                    $tmpOperators['net_after_tax']['avg'] = $total_avg_net_after_tax['avg'];


                    $total_avg_net_revenue_after_tax = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['net_revenue_after_tax'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['net_revenue_after_tax']['dates'] = $reportsColumnData['net_revenue_after_tax'];
                    $tmpOperators['net_revenue_after_tax']['total'] = $total_avg_net_revenue_after_tax['sum'];
                    $tmpOperators['net_revenue_after_tax']['t_mo_end'] = $total_avg_net_revenue_after_tax['T_Mo_End'];
                    $tmpOperators['net_revenue_after_tax']['avg'] = $total_avg_net_revenue_after_tax['avg'];


                    $total_avg_br = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['br'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['br']['dates'] = $reportsColumnData['br'];
                    $tmpOperators['br']['total'] = $total_avg_br['sum'];
                    $tmpOperators['br']['t_mo_end'] = $total_avg_br['T_Mo_End'];
                    $tmpOperators['br']['avg'] = $total_avg_br['avg'];


                    $total_avg_fp = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['fp'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['fp']['dates'] = $reportsColumnData['fp'];
                    $tmpOperators['fp']['total'] = $total_avg_fp['sum'];
                    $tmpOperators['fp']['t_mo_end'] = $total_avg_fp['T_Mo_End'];
                    $tmpOperators['fp']['avg'] = $total_avg_fp['avg'];


                    $total_avg_fp_success = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['fp_success'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['fp_success']['dates'] = $reportsColumnData['fp_success'];
                    $tmpOperators['fp_success']['total'] = $total_avg_fp_success['sum'];
                    $tmpOperators['fp_success']['t_mo_end'] = $total_avg_fp_success['T_Mo_End'];
                    $tmpOperators['fp_success']['avg'] = $total_avg_fp_success['avg'];


                    $total_avg_fp_failed = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['fp_failed'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['fp_failed']['dates'] = $reportsColumnData['fp_failed'];
                    $tmpOperators['fp_failed']['total'] = $total_avg_fp_failed['sum'];
                    $tmpOperators['fp_failed']['t_mo_end'] = $total_avg_fp_failed['T_Mo_End'];
                    $tmpOperators['fp_failed']['avg'] = $total_avg_fp_failed['avg'];


                    $total_avg_dp = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['dp'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['dp']['dates'] = $reportsColumnData['dp'];
                    $tmpOperators['dp']['total'] = $total_avg_dp['sum'];
                    $tmpOperators['dp']['t_mo_end'] = $total_avg_dp['T_Mo_End'];
                    $tmpOperators['dp']['avg'] = $total_avg_dp['avg'];


                    $total_avg_dp_success = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['dp_success'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['dp_success']['dates'] = $reportsColumnData['dp_success'];
                    $tmpOperators['dp_success']['total'] = $total_avg_dp_success['sum'];
                    $tmpOperators['dp_success']['t_mo_end'] = $total_avg_dp_success['T_Mo_End'];
                    $tmpOperators['dp_success']['avg'] = $total_avg_dp_success['avg'];


                    $total_avg_dp_failed = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['dp_failed'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['dp_failed']['dates'] = $reportsColumnData['dp_failed'];
                    $tmpOperators['dp_failed']['total'] = $total_avg_dp_failed['sum'];
                    $tmpOperators['dp_failed']['t_mo_end'] = $total_avg_dp_failed['T_Mo_End'];
                    $tmpOperators['dp_failed']['avg'] = $total_avg_dp_failed['avg'];


                    $total_avg_renewal = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['renewal'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['renewal']['dates'] = $reportsColumnData['renewal'];
                    $tmpOperators['renewal']['total'] = $total_avg_renewal['sum'];
                    $tmpOperators['renewal']['t_mo_end'] = $total_avg_renewal['T_Mo_End'];
                    $tmpOperators['renewal']['avg'] = $total_avg_renewal['avg'];


                    $total_avg_vat = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['vat'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['vat']['dates'] = $reportsColumnData['vat'];
                    $tmpOperators['vat']['total'] = $total_avg_vat['sum'];
                    $tmpOperators['vat']['t_mo_end'] = $total_avg_vat['T_Mo_End'];
                    $tmpOperators['vat']['avg'] = $total_avg_vat['avg'];


                    $total_avg_spec_tax = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['spec_tax'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['spec_tax']['dates'] = $reportsColumnData['spec_tax'];
                    $tmpOperators['spec_tax']['total'] = $total_avg_spec_tax['sum'];
                    $tmpOperators['spec_tax']['t_mo_end'] = $total_avg_spec_tax['T_Mo_End'];
                    $tmpOperators['spec_tax']['avg'] = $total_avg_spec_tax['avg'];


                    $total_avg_government_cost = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['government_cost'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['government_cost']['dates'] = $reportsColumnData['government_cost'];
                    $tmpOperators['government_cost']['total'] = $total_avg_government_cost['sum'];
                    $tmpOperators['government_cost']['t_mo_end'] = $total_avg_government_cost['T_Mo_End'];
                    $tmpOperators['government_cost']['avg'] = $total_avg_government_cost['avg'];


                    $total_avg_dealer_commision = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['dealer_commision'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['dealer_commision']['dates'] = $reportsColumnData['dealer_commision'];
                    $tmpOperators['dealer_commision']['total'] = $total_avg_dealer_commision['sum'];
                    $tmpOperators['dealer_commision']['t_mo_end'] = $total_avg_dealer_commision['T_Mo_End'];
                    $tmpOperators['dealer_commision']['avg'] = $total_avg_dealer_commision['avg'];


                    $total_avg_wht = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['wht'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['wht']['dates'] = $reportsColumnData['wht'];
                    $tmpOperators['wht']['total'] = $total_avg_wht['sum'];
                    $tmpOperators['wht']['t_mo_end'] = $total_avg_wht['T_Mo_End'];
                    $tmpOperators['wht']['avg'] = $total_avg_wht['avg'];


                    $total_avg_misc_tax = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['misc_tax'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['misc_tax']['dates'] = $reportsColumnData['misc_tax'];
                    $tmpOperators['misc_tax']['total'] = $total_avg_misc_tax['sum'];
                    $tmpOperators['misc_tax']['t_mo_end'] = $total_avg_misc_tax['T_Mo_End'];
                    $tmpOperators['misc_tax']['avg'] = $total_avg_misc_tax['avg'];


                    $total_avg_other_tax = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['other_tax'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['other_tax']['dates'] = $reportsColumnData['other_tax'];
                    $tmpOperators['other_tax']['total'] = $total_avg_other_tax['sum'];
                    $tmpOperators['other_tax']['t_mo_end'] = $total_avg_other_tax['T_Mo_End'];
                    $tmpOperators['other_tax']['avg'] = $total_avg_other_tax['avg'];


                    $total_avg_uso = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['uso'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['uso']['dates'] = $reportsColumnData['uso'];
                    $tmpOperators['uso']['total'] = $total_avg_uso['sum'];
                    $tmpOperators['uso']['t_mo_end'] = $total_avg_uso['T_Mo_End'];
                    $tmpOperators['uso']['avg'] = $total_avg_uso['avg'];


                    $total_avg_agre_paxxa = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['agre_paxxa'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['agre_paxxa']['dates'] = $reportsColumnData['agre_paxxa'];
                    $tmpOperators['agre_paxxa']['total'] = $total_avg_agre_paxxa['sum'];
                    $tmpOperators['agre_paxxa']['t_mo_end'] = $total_avg_agre_paxxa['T_Mo_End'];
                    $tmpOperators['agre_paxxa']['avg'] = $total_avg_agre_paxxa['avg'];


                    $total_avg_sbaf = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['sbaf'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['sbaf']['dates'] = $reportsColumnData['sbaf'];
                    $tmpOperators['sbaf']['total'] = $total_avg_sbaf['sum'];
                    $tmpOperators['sbaf']['t_mo_end'] = $total_avg_sbaf['T_Mo_End'];
                    $tmpOperators['sbaf']['avg'] = $total_avg_sbaf['avg'];


                    $total_avg_clicks = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['clicks'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['clicks']['dates'] = $reportsColumnData['clicks'];
                    $tmpOperators['clicks']['total'] = $total_avg_clicks['sum'];
                    $tmpOperators['clicks']['t_mo_end'] = $total_avg_clicks['T_Mo_End'];
                    $tmpOperators['clicks']['avg'] = $total_avg_clicks['avg'];


                    $total_avg_ratio_for_cpa = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['ratio_for_cpa'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['ratio_for_cpa']['dates'] = $reportsColumnData['ratio_for_cpa'];
                    $tmpOperators['ratio_for_cpa']['total'] = $total_avg_ratio_for_cpa['sum'];
                    $tmpOperators['ratio_for_cpa']['t_mo_end'] = $total_avg_ratio_for_cpa['T_Mo_End'];
                    $tmpOperators['ratio_for_cpa']['avg'] = $total_avg_ratio_for_cpa['avg'];


                    $total_avg_cpa_price = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['cpa_price'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['cpa_price']['dates'] = $reportsColumnData['cpa_price'];
                    $tmpOperators['cpa_price']['total'] = $total_avg_cpa_price['sum'];
                    $tmpOperators['cpa_price']['t_mo_end'] = $total_avg_cpa_price['T_Mo_End'];
                    $tmpOperators['cpa_price']['avg'] = $total_avg_cpa_price['avg'];


                    $total_avg_cr_mo_clicks = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['cr_mo_clicks'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['cr_mo_clicks']['dates'] = $reportsColumnData['cr_mo_clicks'];
                    $tmpOperators['cr_mo_clicks']['total'] = $total_avg_cr_mo_clicks['sum'];
                    $tmpOperators['cr_mo_clicks']['t_mo_end'] = $total_avg_cr_mo_clicks['T_Mo_End'];
                    $tmpOperators['cr_mo_clicks']['avg'] = $total_avg_cr_mo_clicks['avg'];


                    $total_avg_cr_mo_landing = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['cr_mo_landing'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['cr_mo_landing']['dates'] = $reportsColumnData['cr_mo_landing'];
                    $tmpOperators['cr_mo_landing']['total'] = $total_avg_cr_mo_landing['sum'];
                    $tmpOperators['cr_mo_landing']['t_mo_end'] = $total_avg_cr_mo_landing['T_Mo_End'];
                    $tmpOperators['cr_mo_landing']['avg'] = $total_avg_cr_mo_landing['avg'];


                    $total_avg_landing = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['landing'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['landing']['dates'] = $reportsColumnData['landing'];
                    $tmpOperators['landing']['total'] = $total_avg_landing['sum'];
                    $tmpOperators['landing']['t_mo_end'] = $total_avg_landing['T_Mo_End'];
                    $tmpOperators['landing']['avg'] = $total_avg_landing['avg'];


                    $total_avg_mo = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['mo'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['mo']['dates'] = $reportsColumnData['mo'];
                    $tmpOperators['mo']['total'] = $total_avg_mo['sum'];
                    $tmpOperators['mo']['t_mo_end'] = $total_avg_mo['T_Mo_End'];
                    $tmpOperators['mo']['avg'] = $total_avg_mo['avg'];


                    $total_avg_reg = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['reg'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['reg']['dates'] = $reportsColumnData['reg'];
                    $tmpOperators['reg']['total'] = $total_avg_reg['sum'];
                    $tmpOperators['reg']['t_mo_end'] = $total_avg_reg['T_Mo_End'];
                    $tmpOperators['reg']['avg'] = $total_avg_reg['avg'];


                    $total_avg_unreg = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['unreg'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['unreg']['dates'] = $reportsColumnData['unreg'];
                    $tmpOperators['unreg']['total'] = $total_avg_unreg['sum'];
                    $tmpOperators['unreg']['t_mo_end'] = $total_avg_unreg['T_Mo_End'];
                    $tmpOperators['unreg']['avg'] = $total_avg_unreg['avg'];


                    $total_avg_price_mo = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['price_mo'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['price_mo']['dates'] = $reportsColumnData['price_mo'];
                    $tmpOperators['price_mo']['total'] = $total_avg_price_mo['sum'];
                    $tmpOperators['price_mo']['t_mo_end'] = $total_avg_price_mo['T_Mo_End'];
                    $tmpOperators['price_mo']['avg'] = $total_avg_price_mo['avg'];

                    $total_avg_price_mo_cost = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['price_mo_cost'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['price_mo_cost']['dates'] = $reportsColumnData['price_mo_cost'];
                    $tmpOperators['price_mo_cost']['total'] = $total_avg_price_mo_cost['sum'];
                    $tmpOperators['price_mo_cost']['t_mo_end'] = $total_avg_price_mo_cost['T_Mo_End'];
                    $tmpOperators['price_mo_cost']['avg'] = $total_avg_price_mo_cost['avg'];

                    $total_avg_price_mo_mo = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['price_mo_mo'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['price_mo_mo']['dates'] = $reportsColumnData['price_mo_mo'];
                    $tmpOperators['price_mo_mo']['total'] = $total_avg_price_mo_mo['sum'];
                    $tmpOperators['price_mo_mo']['t_mo_end'] = $total_avg_price_mo_mo['T_Mo_End'];
                    $tmpOperators['price_mo_mo']['avg'] = $total_avg_price_mo_mo['avg'];


                    $total_avg_active_subs = UtilityReports::calculateTotalSubscribe($operator,$reportsColumnData['active_subs'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['active_subs']['dates'] = $reportsColumnData['active_subs'];
                    $tmpOperators['active_subs']['total'] = $total_avg_active_subs['sum'];
                    $tmpOperators['active_subs']['t_mo_end'] = $total_avg_active_subs['T_Mo_End'];
                    $tmpOperators['active_subs']['avg'] = $total_avg_active_subs['avg'];


                    $total_avg_arpu_7 = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['arpu_7'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['arpu_7']['dates'] = $reportsColumnData['arpu_7'];
                    $tmpOperators['arpu_7']['total'] = $total_avg_arpu_7['sum'];
                    $tmpOperators['arpu_7']['t_mo_end'] = $total_avg_arpu_7['T_Mo_End'];
                    $tmpOperators['arpu_7']['avg'] = $total_avg_arpu_7['avg'];


                    $total_avg_arpu_7_usd = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['arpu_7_usd'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['arpu_7_usd']['dates'] = $reportsColumnData['arpu_7_usd'];
                    $tmpOperators['arpu_7_usd']['total'] = $total_avg_arpu_7_usd['sum'];
                    $tmpOperators['arpu_7_usd']['t_mo_end'] = $total_avg_arpu_7_usd['T_Mo_End'];
                    $tmpOperators['arpu_7_usd']['avg'] = $total_avg_arpu_7_usd['avg'];


                    $total_avg_arpu_30 = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['arpu_30'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['arpu_30']['dates'] = $reportsColumnData['arpu_30'];
                    $tmpOperators['arpu_30']['total'] = $total_avg_arpu_30['sum'];
                    $tmpOperators['arpu_30']['t_mo_end'] = $total_avg_arpu_30['T_Mo_End'];
                    $tmpOperators['arpu_30']['avg'] = $total_avg_arpu_30['avg'];


                    $total_avg_arpu_30_usd = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['arpu_30_usd'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['arpu_30_usd']['dates'] = $reportsColumnData['arpu_30_usd'];
                    $tmpOperators['arpu_30_usd']['total'] = $total_avg_arpu_30_usd['sum'];
                    $tmpOperators['arpu_30_usd']['t_mo_end'] = $total_avg_arpu_30_usd['T_Mo_End'];
                    $tmpOperators['arpu_30_usd']['avg'] = $total_avg_arpu_30_usd['avg'];


                    $total_avg_reg_sub = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['reg_sub'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['reg_sub']['dates'] = $reportsColumnData['reg_sub'];
                    $tmpOperators['reg_sub']['total'] = $total_avg_reg_sub['sum'];
                    $tmpOperators['reg_sub']['t_mo_end'] = $total_avg_reg_sub['T_Mo_End'];
                    $tmpOperators['reg_sub']['avg'] = $total_avg_reg_sub['avg'];


                    $total_avg_roi = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['roi'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['roi']['dates'] = $reportsColumnData['roi'];
                    $tmpOperators['roi']['total'] = $total_avg_roi['sum'];
                    $tmpOperators['roi']['t_mo_end'] = $total_avg_roi['T_Mo_End'];
                    $tmpOperators['roi']['avg'] = $total_avg_roi['avg'];


                    $total_avg_bill = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['bill'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['bill']['dates']=$reportsColumnData['bill'];
                    $tmpOperators['bill']['total']=0;
                    $tmpOperators['bill']['t_mo_end']=0;
                    $tmpOperators['bill']['avg']=$total_avg_bill['avg'];


                    $total_avg_firstpush = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['firstpush'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['firstpush']['dates']=$reportsColumnData['firstpush'];
                    $tmpOperators['firstpush']['total']=0;
                    $tmpOperators['firstpush']['t_mo_end']=0;
                    $tmpOperators['firstpush']['avg']=$total_avg_firstpush['avg'];


                    $total_avg_dailypush = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['dailypush'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['dailypush']['dates']=$reportsColumnData['dailypush'];
                    $tmpOperators['dailypush']['total']=0;
                    $tmpOperators['dailypush']['t_mo_end']=0;
                    $tmpOperators['dailypush']['avg']=$total_avg_dailypush['avg'];


                    $total_avg_last_7_gros_rev = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['last_7_gros_rev'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['last_7_gros_rev']['dates'] = $reportsColumnData['last_7_gros_rev'];
                    $tmpOperators['last_7_gros_rev']['total'] = $total_avg_last_7_gros_rev['sum'];
                    $tmpOperators['last_7_gros_rev']['t_mo_end'] = $total_avg_last_7_gros_rev['T_Mo_End'];
                    $tmpOperators['last_7_gros_rev']['avg'] = $total_avg_last_7_gros_rev['avg'];


                    $total_avg_last_7_reg = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['last_7_reg'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['last_7_reg']['dates'] = $reportsColumnData['last_7_reg'];
                    $tmpOperators['last_7_reg']['total'] = $total_avg_last_7_reg['sum'];
                    $tmpOperators['last_7_reg']['t_mo_end'] = $total_avg_last_7_reg['T_Mo_End'];
                    $tmpOperators['last_7_reg']['avg'] = $total_avg_last_7_reg['avg'];


                    $total_avg_last_30_gros_rev = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['last_30_gros_rev'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['last_30_gros_rev']['dates'] = $reportsColumnData['last_30_gros_rev'];
                    $tmpOperators['last_30_gros_rev']['total'] = $total_avg_last_30_gros_rev['sum'];
                    $tmpOperators['last_30_gros_rev']['t_mo_end'] = $total_avg_last_30_gros_rev['T_Mo_End'];
                    $tmpOperators['last_30_gros_rev']['avg'] = $total_avg_last_30_gros_rev['avg'];


                    $total_avg_last_30_reg = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['last_30_reg'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['last_30_reg']['dates'] = $reportsColumnData['last_30_reg'];
                    $tmpOperators['last_30_reg']['total'] = $total_avg_last_30_reg['sum'];
                    $tmpOperators['last_30_reg']['t_mo_end'] = $total_avg_last_30_reg['T_Mo_End'];
                    $tmpOperators['last_30_reg']['avg'] = $total_avg_last_30_reg['avg'];

                    // dd($tmpOperators);
                    $sumemry[] = $tmpOperators;
                }
            }

            // dd($sumemry);

            // Company Sum from Operator array
            $displayCompanies = array();
            $displayUnknown = array();
            $SelectedCompanies=array();
            $RowCompanyData = array();
            $SelectedUnknown = array();



            if (!empty($sumemry)) {
                foreach ($sumemry as $key => $sumemries) {
                    if (isset($sumemries['company']) && isset($sumemries['company']['id'])) {
                        $company_id = $sumemries['company']['id'];
                        $SelectedCompanies[$company_id] = $sumemries['company'];
                        $displayCompanies[$company_id][] = $sumemries;
                    } else {
                        $SelectedCompanies['unknown'] = ['id' => 'unknown'];
                        $displayUnknown['unknown'][] = $sumemries;
                    }
                }
            }
            // dd($displayUnknown);
            if (!empty($SelectedCompanies)) {
                foreach ($SelectedCompanies as $company_id => $SelectedCompany) {
                    $tempDataArr = array();

                    // Check if the company_id is 'unknown'
                    if ($company_id === 'unknown') {
                        $dataRows = $displayUnknown['unknown'];
                    } else {
                        $dataRows = $displayCompanies[$company_id];
                    }

                    $dataRowSum = UtilityReports::pnlDetailsDataSum($dataRows);

                    $company_avg_arpu_7_usd = UtilityReports::calculateTotalAVG($operator, $dataRowSum['arpu_7_usd']['dates'], $startColumnDateDisplay, $end_date);
                    $dataRowSum['arpu_7_usd']['avg'] = $company_avg_arpu_7_usd['avg'];

                    $company_avg_arpu_30_usd = UtilityReports::calculateTotalAVG($operator, $dataRowSum['arpu_30_usd']['dates'], $startColumnDateDisplay, $end_date);
                    $dataRowSum['arpu_30_usd']['avg'] = $company_avg_arpu_30_usd['avg'];

                    $company_avg_bill = UtilityReports::calculateTotalAVG($operator, $dataRowSum['bill']['dates'], $startColumnDateDisplay, $end_date);
                    $dataRowSum['bill']['avg'] = $company_avg_bill['avg'];

                    $company_avg_firstpush = UtilityReports::calculateTotalAVG($operator, $dataRowSum['firstpush']['dates'], $startColumnDateDisplay, $end_date);
                    $dataRowSum['firstpush']['avg'] = $company_avg_firstpush['avg'];

                    $company_avg_dailypush = UtilityReports::calculateTotalAVG($operator, $dataRowSum['dailypush']['dates'], $startColumnDateDisplay, $end_date);
                    $dataRowSum['dailypush']['avg'] = $company_avg_dailypush['avg'];

                    $tempDataArr['company'] = $SelectedCompany;
                    $tempDataArr['month_string'] = $month;
                    $tempDataArr = array_merge($tempDataArr, $dataRowSum);
                    $RowCompanyData[] = $tempDataArr;
                }
            }

            // dd($RowCompanyData);

            $sumemry = $RowCompanyData;
            $sumOfSummaryData = UtilityReports::pnlDetailsDataSum($sumemry);

            $all_avg_arpu_7_usd = UtilityReports::calculateTotalAVG($operator,$sumOfSummaryData['arpu_7_usd']['dates'],$startColumnDateDisplay,$end_date);
            $sumOfSummaryData['arpu_7_usd']['avg'] = $all_avg_arpu_7_usd['avg'];

            $all_avg_arpu_30_usd = UtilityReports::calculateTotalAVG($operator,$sumOfSummaryData['arpu_30_usd']['dates'],$startColumnDateDisplay,$end_date);
            $sumOfSummaryData['arpu_30_usd']['avg'] = $all_avg_arpu_30_usd['avg'];

            $all_avg_bill = UtilityReports::calculateTotalAVG($operator,$sumOfSummaryData['bill']['dates'],$startColumnDateDisplay,$end_date);
            $sumOfSummaryData['bill']['avg']=$all_avg_bill['avg'];

            $all_avg_firstpush = UtilityReports::calculateTotalAVG($operator,$sumOfSummaryData['firstpush']['dates'],$startColumnDateDisplay,$end_date);
            $sumOfSummaryData['firstpush']['avg']=$all_avg_firstpush['avg'];

            $all_avg_dailypush = UtilityReports::calculateTotalAVG($operator,$sumOfSummaryData['dailypush']['dates'],$startColumnDateDisplay,$end_date);
            $sumOfSummaryData['dailypush']['avg']=$all_avg_dailypush['avg'];

            // dd($sumemry);
            // dd($sumOfSummaryData);
            $date = $end_date;

            return view('report.daily_company_pnlreport', compact('date','no_of_days','sumemry','sumOfSummaryData','CompanyWise'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

     // get pnl Business daily pnl report
     public function DailyPnlReportBusinessDetails(Request $request)
     {
        if(\Auth::user()->can('PNL Detail'))
        {
            // dd(12333);
            // get filtre request
            // dd($request->all());
            $CountryId = $req_CountryId = $request->country;
            $CompanyId = $req_CompanyId = $request->company;
            $BusinessType = $req_BusinessType = $request->business_type;
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
                // $end_date_input = new Carbon($req_end_date);

                $start_date = $start_date_input->subDays(35)->format('Y-m-d');
                $startColumnDateDisplay = $display_date_input->format('Y-m-d');
                //dd($startColumnDateDisplay."ppp");
                $end_date = $req_end_date;
                $month = $display_date_input->format('F Y');
            }else{
                $req_Start_date = date('Y-m-d', strtotime('-30 days'));
                $req_end_date = date('Y-m-d');

                $start_date_input = new Carbon($req_Start_date);
                $display_date_input = new Carbon($req_Start_date);

                $start_date = $start_date_input->subDays(35)->format('Y-m-d');
                $startColumnDateDisplay = $display_date_input->format('Y-m-d');

                $end_date = $req_end_date;
                $month = $display_date_input->format('F Y');
            }

            if($request->filled('company') && $req_CompanyId !="allcompany" && !$request->filled('operatorId'))
            {
                $companies = Company::Find($req_CompanyId);
                $Operators_company = array();

                if(!empty($companies))
                {
                    $Operators_company = $companies->company_operators->pluck('operator_id')->toArray();
                    $Operators = Operator::Status(1)->GetOperatorByOperatorId($Operators_company)->get();
                }

                $showAllOperator = false;
            }

            if($request->filled('company') && $request->filled('country') && !$request->filled('operatorId'))
            {
                $data=[
                    'id'=>$req_CountryId,
                    'company'=>$req_CompanyId,
                ];
                $requestobj = new Request($data);
                $ReportControllerobj= new ReportController;
                $Operators=$ReportControllerobj->userFilterOperator($requestobj);
                // $Operators = Operator::Status(1)->GetOperatorByCountryId($req_CountryId)->get();
                $showAllOperator = false;
            }

            if (isset($req_CompanyId) && !$request->filled('country') && $request->filled('business_type') && !$request->filled('operatorId')) {
                $Countrys[0] = Country::with('operators')->Find($CountryId);
                $data = [
                    'country' => $req_CountryId,
                    'company' => $req_CompanyId,
                    'business_type' => $req_BusinessType,
                ];

                $requestobj = new Request($data);
                $ReportControllerobj = new ReportController;
                $Operators = $ReportControllerobj->userFilterBusinessTypeOperator($requestobj);
                $CountryFlag = false;
                $showAllOperator = false;
                // dd($Operators);
            }

            if ($request->filled('company') && $request->filled('country') && $request->filled('business_type') && !$request->filled('operatorId')) {
                $Countrys[0] = Country::with('operators')->Find($CountryId);
                $data = [
                    'country' => $req_CountryId,
                    'company' => $req_CompanyId,
                    'business_type' => $req_BusinessType,
                ];

                $requestobj = new Request($data);
                $ReportControllerobj = new ReportController;
                $Operators = $ReportControllerobj->userFilterBusinessTypeOperator($requestobj);
                $CountryFlag = false;
                $showAllOperator = false;
            }

            if($request->filled('operatorId'))
            {
                $Operators = Operator::with('revenueshare','RevenushareByDate','WhtByDate','VatByDate')->Status(1)->GetOperatorByOperatorId($filterOperator)
                            ->get();
                $showAllOperator = false;
            }

            if($showAllOperator)
            {
                $Operators = Operator::with('revenueshare','RevenushareByDate','VatByDate','WhtByDate')->Status(1)->get();
            }

            $Country = Country::all()->toArray();
            $companys = Company::get();
            //$static_operator =array(1,2,3,4);
            $countries = array();
            if(!empty($Country))
            {
                foreach($Country as $CountryI)
                {
                    $countries[$CountryI['id']]=$CountryI;
                }
            }

            // $contains = Arr::hasAny($Country, "2");
            // $Operators = Operator::Status(1)->get();
            $staticOperators = $Operators->pluck('id_operator')->toArray();
            $sumemry = array();

            // $start_date = Carbon::now()->startOfMonth()->subDays(30)->format('Y-m-d');
            // $startColumnDateDisplay = Carbon::now()->startOfMonth()->format('Y-m-d');
            // $end_date = Carbon::now()->format('Y-m-d');
            // $month = Carbon::now()->format('F Y');

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

                // $unknown_operator = ReportsPnlsOperatorSummarizes::UnmatchOperator();
                $unknown_operator = [];

            }else{

                $UserOperatorServices =Session::get('userOperatorService');
                if(empty($UserOperatorServices))
                {
                    dd("Please Contact to admin , add Operator to your account");
                }

                $arrayOperatorsIds = $UserOperatorServices['id_operators'];
                $arrayServicesIds = $UserOperatorServices['id_services'];

                $reports = ReportsPnlsOperatorSummarizes::filterOperator($arrayOperatorsIds)
                ->filterDateRange($start_date,$end_date)
                ->orderBy('country_id')
                ->orderBy('date', 'ASC')
                ->get()->toArray();

                $userOperators = Operator::with('revenueshare','RevenushareByDate','country','WhtByDate','VatByDate')->filteroperator($arrayOperatorsIds)->get()->toArray();
                foreach ($userOperators as $key => $value) {
                    if (empty($value['revenueshare'])) {
                        $userOperators[$key]['revenueshare']['merchant_revenue_share'] = 100;
                    }
                }
                $userOperatorsIDs = $this->getReportsByOperatorID($userOperators);

                $userreports = ReportSummeriseUsers::filteroperator($arrayOperatorsIds)
                ->filterDateRange($start_date,$end_date)
                ->User($user_id)
                ->orderBy('country_id')
                ->orderBy('date', 'ASC')
                ->get()->toArray();

                $userreportsByIDs = $this->getUserReportsByOperator($userreports);

                $unknown_operator = [];
            }

            // $reports = ReportsPnlsOperatorSummarizes::filterOperator($staticOperators)
            // ->filterDateRange($start_date,$end_date)
            // ->orderBy('country_id')
            // ->orderBy('date', 'ASC')
            // ->get()->toArray();
            // dd($reports);

            $reportsByIDs = $this->getReportsByOperator($reports);
            $datesIndividual =Utility::getRangeDates($startColumnDateDisplay,$end_date);
            $no_of_days = Utility::getRangeDateNo($datesIndividual);

            if(isset($userreportsByIDs) && !empty($userreportsByIDs)){
                foreach ($userreportsByIDs as $key1 => $value1) {
                    foreach ($value1 as $key2 => $value2) {
                        $reportsByIDs[$key1][$key2]['rev'] = $value2['gros_rev'];
                        $reportsByIDs[$key1][$key2]['rev_usd'] = $value2['gros_rev']*$userOperatorsIDs[$key1]['country']['usd'];
                        $reportsByIDs[$key1][$key2]['share'] = $value2['gros_rev']*$userOperatorsIDs[$key1]['country']['usd']*($userOperatorsIDs[$key1]['revenueshare']['merchant_revenue_share']/100);
                        $reportsByIDs[$key1][$key2]['lshare'] = $value2['gros_rev']*($userOperatorsIDs[$key1]['revenueshare']['merchant_revenue_share']/100);
                    }
                }
            }
            // dd($no_of_days);

            if(!empty($Operators))
            {
                foreach($Operators as $operator)
                {

                    $tmpOperators=array();
                    $tmpOperators['operator'] = $operator;
                    $country_id  = $operator->country_id;
                    $contain_id = Arr::exists($countries, $country_id);
                    $OperatorCountry = array();

                    if($contain_id )
                    {
                        $tmpOperators['country']=$countries[$country_id];
                        $OperatorCountry = $countries[$country_id];
                    }

                    $reportsColumnData = $this->getPNLReportsDateWise($operator,$no_of_days,$reportsByIDs,$OperatorCountry);
                    $tmpOperators['month_string'] = $month;
                    $tmpOperators['last_update'] = $reportsColumnData['last_update'];
                    $tmpOperators['hostingCost'] = isset($operator->hostingCost) ? $operator->hostingCost : 0;
                    $tmpOperators['contentCost'] = isset($operator->content) ? $operator->content : 0;
                    $tmpOperators['rndCost'] = isset($operator->rnd) ? $operator->rnd : 0;
                    $tmpOperators['bdCost'] = 2.5;
                    $tmpOperators['marketCost'] = 1.5;
                    $tmpOperators['miscCost'] = isset($operator->miscCost) ? $operator->miscCost : 0;
                    $tmpOperators['vatTax'] = isset($operator->vat) ? $operator->vat : 0;
                    $tmpOperators['whtTax'] = isset($operator->wht) ? $operator->wht : 0;
                    $tmpOperators['miscTax'] = isset($operator->miscTax) ? $operator->miscTax : 0;
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


                    $total_avg_market_cost = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['market_cost'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['market_cost']['dates'] = $reportsColumnData['market_cost'];
                    $tmpOperators['market_cost']['total'] = $total_avg_market_cost['sum'];
                    $tmpOperators['market_cost']['t_mo_end'] = $total_avg_market_cost['T_Mo_End'];
                    $tmpOperators['market_cost']['avg'] = $total_avg_market_cost['avg'];


                    $total_avg_misc_cost = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['misc_cost'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['misc_cost']['dates'] = $reportsColumnData['misc_cost'];
                    $tmpOperators['misc_cost']['total'] = $total_avg_misc_cost['sum'];
                    $tmpOperators['misc_cost']['t_mo_end'] = $total_avg_misc_cost['T_Mo_End'];
                    $tmpOperators['misc_cost']['avg'] = $total_avg_misc_cost['avg'];


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


                    $total_avg_net_after_tax = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['net_after_tax'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['net_after_tax']['dates'] = $reportsColumnData['net_after_tax'];
                    $tmpOperators['net_after_tax']['total'] = $total_avg_net_after_tax['sum'];
                    $tmpOperators['net_after_tax']['t_mo_end'] = $total_avg_net_after_tax['T_Mo_End'];
                    $tmpOperators['net_after_tax']['avg'] = $total_avg_net_after_tax['avg'];


                    $total_avg_net_revenue_after_tax = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['net_revenue_after_tax'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['net_revenue_after_tax']['dates'] = $reportsColumnData['net_revenue_after_tax'];
                    $tmpOperators['net_revenue_after_tax']['total'] = $total_avg_net_revenue_after_tax['sum'];
                    $tmpOperators['net_revenue_after_tax']['t_mo_end'] = $total_avg_net_revenue_after_tax['T_Mo_End'];
                    $tmpOperators['net_revenue_after_tax']['avg'] = $total_avg_net_revenue_after_tax['avg'];


                    $total_avg_br = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['br'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['br']['dates'] = $reportsColumnData['br'];
                    $tmpOperators['br']['total'] = $total_avg_br['sum'];
                    $tmpOperators['br']['t_mo_end'] = $total_avg_br['T_Mo_End'];
                    $tmpOperators['br']['avg'] = $total_avg_br['avg'];


                    $total_avg_fp = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['fp'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['fp']['dates'] = $reportsColumnData['fp'];
                    $tmpOperators['fp']['total'] = $total_avg_fp['sum'];
                    $tmpOperators['fp']['t_mo_end'] = $total_avg_fp['T_Mo_End'];
                    $tmpOperators['fp']['avg'] = $total_avg_fp['avg'];


                    $total_avg_fp_success = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['fp_success'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['fp_success']['dates'] = $reportsColumnData['fp_success'];
                    $tmpOperators['fp_success']['total'] = $total_avg_fp_success['sum'];
                    $tmpOperators['fp_success']['t_mo_end'] = $total_avg_fp_success['T_Mo_End'];
                    $tmpOperators['fp_success']['avg'] = $total_avg_fp_success['avg'];


                    $total_avg_fp_failed = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['fp_failed'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['fp_failed']['dates'] = $reportsColumnData['fp_failed'];
                    $tmpOperators['fp_failed']['total'] = $total_avg_fp_failed['sum'];
                    $tmpOperators['fp_failed']['t_mo_end'] = $total_avg_fp_failed['T_Mo_End'];
                    $tmpOperators['fp_failed']['avg'] = $total_avg_fp_failed['avg'];


                    $total_avg_dp = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['dp'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['dp']['dates'] = $reportsColumnData['dp'];
                    $tmpOperators['dp']['total'] = $total_avg_dp['sum'];
                    $tmpOperators['dp']['t_mo_end'] = $total_avg_dp['T_Mo_End'];
                    $tmpOperators['dp']['avg'] = $total_avg_dp['avg'];


                    $total_avg_dp_success = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['dp_success'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['dp_success']['dates'] = $reportsColumnData['dp_success'];
                    $tmpOperators['dp_success']['total'] = $total_avg_dp_success['sum'];
                    $tmpOperators['dp_success']['t_mo_end'] = $total_avg_dp_success['T_Mo_End'];
                    $tmpOperators['dp_success']['avg'] = $total_avg_dp_success['avg'];


                    $total_avg_dp_failed = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['dp_failed'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['dp_failed']['dates'] = $reportsColumnData['dp_failed'];
                    $tmpOperators['dp_failed']['total'] = $total_avg_dp_failed['sum'];
                    $tmpOperators['dp_failed']['t_mo_end'] = $total_avg_dp_failed['T_Mo_End'];
                    $tmpOperators['dp_failed']['avg'] = $total_avg_dp_failed['avg'];


                    $total_avg_renewal = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['renewal'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['renewal']['dates'] = $reportsColumnData['renewal'];
                    $tmpOperators['renewal']['total'] = $total_avg_renewal['sum'];
                    $tmpOperators['renewal']['t_mo_end'] = $total_avg_renewal['T_Mo_End'];
                    $tmpOperators['renewal']['avg'] = $total_avg_renewal['avg'];


                    $total_avg_vat = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['vat'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['vat']['dates'] = $reportsColumnData['vat'];
                    $tmpOperators['vat']['total'] = $total_avg_vat['sum'];
                    $tmpOperators['vat']['t_mo_end'] = $total_avg_vat['T_Mo_End'];
                    $tmpOperators['vat']['avg'] = $total_avg_vat['avg'];


                    $total_avg_spec_tax = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['spec_tax'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['spec_tax']['dates'] = $reportsColumnData['spec_tax'];
                    $tmpOperators['spec_tax']['total'] = $total_avg_spec_tax['sum'];
                    $tmpOperators['spec_tax']['t_mo_end'] = $total_avg_spec_tax['T_Mo_End'];
                    $tmpOperators['spec_tax']['avg'] = $total_avg_spec_tax['avg'];


                    $total_avg_government_cost = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['government_cost'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['government_cost']['dates'] = $reportsColumnData['government_cost'];
                    $tmpOperators['government_cost']['total'] = $total_avg_government_cost['sum'];
                    $tmpOperators['government_cost']['t_mo_end'] = $total_avg_government_cost['T_Mo_End'];
                    $tmpOperators['government_cost']['avg'] = $total_avg_government_cost['avg'];


                    $total_avg_dealer_commision = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['dealer_commision'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['dealer_commision']['dates'] = $reportsColumnData['dealer_commision'];
                    $tmpOperators['dealer_commision']['total'] = $total_avg_dealer_commision['sum'];
                    $tmpOperators['dealer_commision']['t_mo_end'] = $total_avg_dealer_commision['T_Mo_End'];
                    $tmpOperators['dealer_commision']['avg'] = $total_avg_dealer_commision['avg'];


                    $total_avg_wht = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['wht'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['wht']['dates'] = $reportsColumnData['wht'];
                    $tmpOperators['wht']['total'] = $total_avg_wht['sum'];
                    $tmpOperators['wht']['t_mo_end'] = $total_avg_wht['T_Mo_End'];
                    $tmpOperators['wht']['avg'] = $total_avg_wht['avg'];


                    $total_avg_misc_tax = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['misc_tax'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['misc_tax']['dates'] = $reportsColumnData['misc_tax'];
                    $tmpOperators['misc_tax']['total'] = $total_avg_misc_tax['sum'];
                    $tmpOperators['misc_tax']['t_mo_end'] = $total_avg_misc_tax['T_Mo_End'];
                    $tmpOperators['misc_tax']['avg'] = $total_avg_misc_tax['avg'];


                    $total_avg_other_tax = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['other_tax'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['other_tax']['dates'] = $reportsColumnData['other_tax'];
                    $tmpOperators['other_tax']['total'] = $total_avg_other_tax['sum'];
                    $tmpOperators['other_tax']['t_mo_end'] = $total_avg_other_tax['T_Mo_End'];
                    $tmpOperators['other_tax']['avg'] = $total_avg_other_tax['avg'];


                    $total_avg_uso = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['uso'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['uso']['dates'] = $reportsColumnData['uso'];
                    $tmpOperators['uso']['total'] = $total_avg_uso['sum'];
                    $tmpOperators['uso']['t_mo_end'] = $total_avg_uso['T_Mo_End'];
                    $tmpOperators['uso']['avg'] = $total_avg_uso['avg'];


                    $total_avg_agre_paxxa = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['agre_paxxa'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['agre_paxxa']['dates'] = $reportsColumnData['agre_paxxa'];
                    $tmpOperators['agre_paxxa']['total'] = $total_avg_agre_paxxa['sum'];
                    $tmpOperators['agre_paxxa']['t_mo_end'] = $total_avg_agre_paxxa['T_Mo_End'];
                    $tmpOperators['agre_paxxa']['avg'] = $total_avg_agre_paxxa['avg'];


                    $total_avg_sbaf = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['sbaf'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['sbaf']['dates'] = $reportsColumnData['sbaf'];
                    $tmpOperators['sbaf']['total'] = $total_avg_sbaf['sum'];
                    $tmpOperators['sbaf']['t_mo_end'] = $total_avg_sbaf['T_Mo_End'];
                    $tmpOperators['sbaf']['avg'] = $total_avg_sbaf['avg'];


                    $total_avg_clicks = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['clicks'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['clicks']['dates'] = $reportsColumnData['clicks'];
                    $tmpOperators['clicks']['total'] = $total_avg_clicks['sum'];
                    $tmpOperators['clicks']['t_mo_end'] = $total_avg_clicks['T_Mo_End'];
                    $tmpOperators['clicks']['avg'] = $total_avg_clicks['avg'];


                    $total_avg_ratio_for_cpa = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['ratio_for_cpa'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['ratio_for_cpa']['dates'] = $reportsColumnData['ratio_for_cpa'];
                    $tmpOperators['ratio_for_cpa']['total'] = $total_avg_ratio_for_cpa['sum'];
                    $tmpOperators['ratio_for_cpa']['t_mo_end'] = $total_avg_ratio_for_cpa['T_Mo_End'];
                    $tmpOperators['ratio_for_cpa']['avg'] = $total_avg_ratio_for_cpa['avg'];


                    $total_avg_cpa_price = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['cpa_price'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['cpa_price']['dates'] = $reportsColumnData['cpa_price'];
                    $tmpOperators['cpa_price']['total'] = $total_avg_cpa_price['sum'];
                    $tmpOperators['cpa_price']['t_mo_end'] = $total_avg_cpa_price['T_Mo_End'];
                    $tmpOperators['cpa_price']['avg'] = $total_avg_cpa_price['avg'];


                    $total_avg_cr_mo_clicks = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['cr_mo_clicks'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['cr_mo_clicks']['dates'] = $reportsColumnData['cr_mo_clicks'];
                    $tmpOperators['cr_mo_clicks']['total'] = $total_avg_cr_mo_clicks['sum'];
                    $tmpOperators['cr_mo_clicks']['t_mo_end'] = $total_avg_cr_mo_clicks['T_Mo_End'];
                    $tmpOperators['cr_mo_clicks']['avg'] = $total_avg_cr_mo_clicks['avg'];


                    $total_avg_cr_mo_landing = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['cr_mo_landing'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['cr_mo_landing']['dates'] = $reportsColumnData['cr_mo_landing'];
                    $tmpOperators['cr_mo_landing']['total'] = $total_avg_cr_mo_landing['sum'];
                    $tmpOperators['cr_mo_landing']['t_mo_end'] = $total_avg_cr_mo_landing['T_Mo_End'];
                    $tmpOperators['cr_mo_landing']['avg'] = $total_avg_cr_mo_landing['avg'];


                    $total_avg_landing = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['landing'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['landing']['dates'] = $reportsColumnData['landing'];
                    $tmpOperators['landing']['total'] = $total_avg_landing['sum'];
                    $tmpOperators['landing']['t_mo_end'] = $total_avg_landing['T_Mo_End'];
                    $tmpOperators['landing']['avg'] = $total_avg_landing['avg'];


                    $total_avg_mo = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['mo'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['mo']['dates'] = $reportsColumnData['mo'];
                    $tmpOperators['mo']['total'] = $total_avg_mo['sum'];
                    $tmpOperators['mo']['t_mo_end'] = $total_avg_mo['T_Mo_End'];
                    $tmpOperators['mo']['avg'] = $total_avg_mo['avg'];


                    $total_avg_reg = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['reg'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['reg']['dates'] = $reportsColumnData['reg'];
                    $tmpOperators['reg']['total'] = $total_avg_reg['sum'];
                    $tmpOperators['reg']['t_mo_end'] = $total_avg_reg['T_Mo_End'];
                    $tmpOperators['reg']['avg'] = $total_avg_reg['avg'];


                    $total_avg_unreg = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['unreg'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['unreg']['dates'] = $reportsColumnData['unreg'];
                    $tmpOperators['unreg']['total'] = $total_avg_unreg['sum'];
                    $tmpOperators['unreg']['t_mo_end'] = $total_avg_unreg['T_Mo_End'];
                    $tmpOperators['unreg']['avg'] = $total_avg_unreg['avg'];


                    $total_avg_price_mo = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['price_mo'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['price_mo']['dates'] = $reportsColumnData['price_mo'];
                    $tmpOperators['price_mo']['total'] = $total_avg_price_mo['sum'];
                    $tmpOperators['price_mo']['t_mo_end'] = $total_avg_price_mo['T_Mo_End'];
                    $tmpOperators['price_mo']['avg'] = $total_avg_price_mo['avg'];

                    $total_avg_price_mo_cost = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['price_mo_cost'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['price_mo_cost']['dates'] = $reportsColumnData['price_mo_cost'];
                    $tmpOperators['price_mo_cost']['total'] = $total_avg_price_mo_cost['sum'];
                    $tmpOperators['price_mo_cost']['t_mo_end'] = $total_avg_price_mo_cost['T_Mo_End'];
                    $tmpOperators['price_mo_cost']['avg'] = $total_avg_price_mo_cost['avg'];

                    $total_avg_price_mo_mo = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['price_mo_mo'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['price_mo_mo']['dates'] = $reportsColumnData['price_mo_mo'];
                    $tmpOperators['price_mo_mo']['total'] = $total_avg_price_mo_mo['sum'];
                    $tmpOperators['price_mo_mo']['t_mo_end'] = $total_avg_price_mo_mo['T_Mo_End'];
                    $tmpOperators['price_mo_mo']['avg'] = $total_avg_price_mo_mo['avg'];


                    $total_avg_active_subs = UtilityReports::calculateTotalSubscribe($operator,$reportsColumnData['active_subs'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['active_subs']['dates'] = $reportsColumnData['active_subs'];
                    $tmpOperators['active_subs']['total'] = $total_avg_active_subs['sum'];
                    $tmpOperators['active_subs']['t_mo_end'] = $total_avg_active_subs['T_Mo_End'];
                    $tmpOperators['active_subs']['avg'] = $total_avg_active_subs['avg'];


                    $total_avg_arpu_7 = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['arpu_7'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['arpu_7']['dates'] = $reportsColumnData['arpu_7'];
                    $tmpOperators['arpu_7']['total'] = $total_avg_arpu_7['sum'];
                    $tmpOperators['arpu_7']['t_mo_end'] = $total_avg_arpu_7['T_Mo_End'];
                    $tmpOperators['arpu_7']['avg'] = $total_avg_arpu_7['avg'];


                    $total_avg_arpu_7_usd = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['arpu_7_usd'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['arpu_7_usd']['dates'] = $reportsColumnData['arpu_7_usd'];
                    $tmpOperators['arpu_7_usd']['total'] = $total_avg_arpu_7_usd['sum'];
                    $tmpOperators['arpu_7_usd']['t_mo_end'] = $total_avg_arpu_7_usd['T_Mo_End'];
                    $tmpOperators['arpu_7_usd']['avg'] = $total_avg_arpu_7_usd['avg'];


                    $total_avg_arpu_30 = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['arpu_30'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['arpu_30']['dates'] = $reportsColumnData['arpu_30'];
                    $tmpOperators['arpu_30']['total'] = $total_avg_arpu_30['sum'];
                    $tmpOperators['arpu_30']['t_mo_end'] = $total_avg_arpu_30['T_Mo_End'];
                    $tmpOperators['arpu_30']['avg'] = $total_avg_arpu_30['avg'];


                    $total_avg_arpu_30_usd = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['arpu_30_usd'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['arpu_30_usd']['dates'] = $reportsColumnData['arpu_30_usd'];
                    $tmpOperators['arpu_30_usd']['total'] = $total_avg_arpu_30_usd['sum'];
                    $tmpOperators['arpu_30_usd']['t_mo_end'] = $total_avg_arpu_30_usd['T_Mo_End'];
                    $tmpOperators['arpu_30_usd']['avg'] = $total_avg_arpu_30_usd['avg'];


                    $total_avg_reg_sub = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['reg_sub'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['reg_sub']['dates'] = $reportsColumnData['reg_sub'];
                    $tmpOperators['reg_sub']['total'] = $total_avg_reg_sub['sum'];
                    $tmpOperators['reg_sub']['t_mo_end'] = $total_avg_reg_sub['T_Mo_End'];
                    $tmpOperators['reg_sub']['avg'] = $total_avg_reg_sub['avg'];


                    $total_avg_roi = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['roi'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['roi']['dates'] = $reportsColumnData['roi'];
                    $tmpOperators['roi']['total'] = $total_avg_roi['sum'];
                    $tmpOperators['roi']['t_mo_end'] = $total_avg_roi['T_Mo_End'];
                    $tmpOperators['roi']['avg'] = $total_avg_roi['avg'];


                    $total_avg_bill = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['bill'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['bill']['dates']=$reportsColumnData['bill'];
                    $tmpOperators['bill']['total']=0;
                    $tmpOperators['bill']['t_mo_end']=0;
                    $tmpOperators['bill']['avg']=$total_avg_bill['avg'];


                    $total_avg_firstpush = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['firstpush'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['firstpush']['dates']=$reportsColumnData['firstpush'];
                    $tmpOperators['firstpush']['total']=0;
                    $tmpOperators['firstpush']['t_mo_end']=0;
                    $tmpOperators['firstpush']['avg']=$total_avg_firstpush['avg'];


                    $total_avg_dailypush = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['dailypush'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['dailypush']['dates']=$reportsColumnData['dailypush'];
                    $tmpOperators['dailypush']['total']=0;
                    $tmpOperators['dailypush']['t_mo_end']=0;
                    $tmpOperators['dailypush']['avg']=$total_avg_dailypush['avg'];


                    $total_avg_last_7_gros_rev = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['last_7_gros_rev'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['last_7_gros_rev']['dates'] = $reportsColumnData['last_7_gros_rev'];
                    $tmpOperators['last_7_gros_rev']['total'] = $total_avg_last_7_gros_rev['sum'];
                    $tmpOperators['last_7_gros_rev']['t_mo_end'] = $total_avg_last_7_gros_rev['T_Mo_End'];
                    $tmpOperators['last_7_gros_rev']['avg'] = $total_avg_last_7_gros_rev['avg'];


                    $total_avg_last_7_reg = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['last_7_reg'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['last_7_reg']['dates'] = $reportsColumnData['last_7_reg'];
                    $tmpOperators['last_7_reg']['total'] = $total_avg_last_7_reg['sum'];
                    $tmpOperators['last_7_reg']['t_mo_end'] = $total_avg_last_7_reg['T_Mo_End'];
                    $tmpOperators['last_7_reg']['avg'] = $total_avg_last_7_reg['avg'];


                    $total_avg_last_30_gros_rev = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['last_30_gros_rev'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['last_30_gros_rev']['dates'] = $reportsColumnData['last_30_gros_rev'];
                    $tmpOperators['last_30_gros_rev']['total'] = $total_avg_last_30_gros_rev['sum'];
                    $tmpOperators['last_30_gros_rev']['t_mo_end'] = $total_avg_last_30_gros_rev['T_Mo_End'];
                    $tmpOperators['last_30_gros_rev']['avg'] = $total_avg_last_30_gros_rev['avg'];


                    $total_avg_last_30_reg = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['last_30_reg'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['last_30_reg']['dates'] = $reportsColumnData['last_30_reg'];
                    $tmpOperators['last_30_reg']['total'] = $total_avg_last_30_reg['sum'];
                    $tmpOperators['last_30_reg']['t_mo_end'] = $total_avg_last_30_reg['T_Mo_End'];
                    $tmpOperators['last_30_reg']['avg'] = $total_avg_last_30_reg['avg'];



                    $sumemry[] = $tmpOperators;

                }
            }
            $data = [];
            $data1 = ['unknown' => ['operators' => [], 'operator_count' => 0]];
            $displayBusinessType = [];


            if (!empty($sumemry) && is_array($sumemry)) {
                foreach ($sumemry as $sumemries) {
                    // Check if 'operator' key exists and 'business_type' is not NULL
                    if (isset($sumemries['operator']['business_type']) && $sumemries['operator']['business_type'] !== NULL) {
                        $business_type = $sumemries['operator']['business_type'];
                        $data[$business_type]['operators'][] = $sumemries;
                    } else {
                        $data1['unknown']['operators'][] = $sumemries;
                    }
                }
            }

            // Calculate operator count and summary for each business type
            foreach ($data as $type => $business_data) {
                $data[$type]['operator_count'] = count($business_data['operators']);
                $displayBusinessType[$type]['summary'] = UtilityReports::pnlDetailsDataSum($business_data['operators']);
            }

            // Calculate operator count and summary for unknown business type if there are any unknown operators
            if (!empty($data1['unknown']['operators'])) {
                $data1['unknown']['operator_count'] = count($data1['unknown']['operators']);
                $data1['unknown']['summary'] = UtilityReports::pnlDetailsDataSum($data1['unknown']['operators']);
            }

            // Merge known business types into a single array
            $result = [];

            foreach ($data as $type => $business_data) {
                $result[$type] = [
                    'operator_count' => $business_data['operator_count'],
                    'summary' => $displayBusinessType[$type]['summary']
                ];
            }

            // Include 'unknown' key only if there are operators with unknown business type
            if (!empty($data1['unknown']['operators'])) {
                $result['unknown'] = $data1['unknown'];
            }

            // dd($result);



            $sumOfSummaryData = UtilityReports::pnlDetailsDataSum($sumemry);

            $all_avg_arpu_7_usd = UtilityReports::calculateTotalAVG($operator,$sumOfSummaryData['arpu_7_usd']['dates'],$startColumnDateDisplay,$end_date);
            $sumOfSummaryData['arpu_7_usd']['avg'] = $all_avg_arpu_7_usd['avg'];

            $all_avg_arpu_30_usd = UtilityReports::calculateTotalAVG($operator,$sumOfSummaryData['arpu_30_usd']['dates'],$startColumnDateDisplay,$end_date);
            $sumOfSummaryData['arpu_30_usd']['avg'] = $all_avg_arpu_30_usd['avg'];

            $all_avg_bill = UtilityReports::calculateTotalAVG($operator,$sumOfSummaryData['bill']['dates'],$startColumnDateDisplay,$end_date);
            $sumOfSummaryData['bill']['avg'] = $all_avg_bill['avg'];

            $all_avg_firstpush = UtilityReports::calculateTotalAVG($operator,$sumOfSummaryData['firstpush']['dates'],$startColumnDateDisplay,$end_date);
            $sumOfSummaryData['firstpush']['avg'] = $all_avg_firstpush['avg'];

            $all_avg_dailypush = UtilityReports::calculateTotalAVG($operator,$sumOfSummaryData['dailypush']['dates'],$startColumnDateDisplay,$end_date);
            $sumOfSummaryData['dailypush']['avg'] = $all_avg_dailypush['avg'];

            // dd($sumemry);
            // dd($sumOfSummaryData);
            // dd($data);
            $date = $end_date;
            return view('report.pnlbusiness_summary', compact('date','no_of_days','sumOfSummaryData', 'result', 'unknown_operator'));
        }
         else
         {
             return redirect()->back()->with('error', __('Permission Denied.'));
         }
     }




    // get country
    public function country(Request $request)
    {
        if($request->id == 'allcompany'){
            $countrys=Country::select(['id AS country_id', 'country AS country_name'])->orderBy('country', 'ASC')->get()->toArray();
            $operator=Operator::orderBy('operator_name', 'ASC')->get()->toArray();
            $data=['countrys'=>$countrys,'operators'=>$operator];
            return $data;
        }

        $countrys=[];
        $country_ids=[];
        $country_operator=[];
        $operators=CompanyOperators::GetOperator($request->id)->get();

        foreach($operators as $key=>$operator){
            $country=$operator->Operator;
            if(!in_array($country[0]->country_id,$country_ids))
                {
                    array_push($countrys,$country[0]);
                }
            array_push($country_ids,$country[0]->country_id);
            array_push($country_operator,$country[0]);
        }

        $data=['countrys'=>$countrys,'operators'=>$country_operator];
        return $data;
    }

    // get operator
    public function operator(Request $request)
    {
        $operators=Operator::GetOperatorByCountryId($request->id)->get();
        return $operators;
    }

    // get service
    public function service(Request $request)
    {
        $services=Service::GetserviceByOperatorId($request->id)->get();
        return $services;
    }

    public function unknown_operator(Request $request)
    {
        if(\Auth::user()->can('PNL Detail'))
        {
            $data['OperatorWise'] = $OperatorWise = 1;
            $data['Daily'] = $Daily = 1;

            $filterOperator = $req_filterOperator = $request->operator;
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
            if (isset($req_Start_date) && !empty($req_Start_date)) {
                $month = '';
            }else{
                $month = Carbon::now()->format('F Y');
            }

            /* filter Search Section */
            $showAllOperator = true;

            if($request->filled('to') && $request->filled('from'))
            {
                $start_date_input = new Carbon($req_Start_date);
                $display_date_input = new Carbon($req_Start_date);
                // $end_date_input = new Carbon($req_end_date);

                $start_date = $start_date_input->subDays(35)->format('Y-m-d');
                $startColumnDateDisplay = $display_date_input->format('Y-m-d');
                //dd($startColumnDateDisplay."ppp");
                $end_date = $req_end_date;
                // $month = $display_date_input->format('F Y');
            }

            if($showAllOperator)
            {
                $Operators = ReportsPnlsOperatorSummarizes::Type();

                $operatorss = $Operators->toArray();
            }

            if (isset($filterOperator) && !empty($filterOperator)) {
                $Operators = ReportsPnlsOperatorSummarizes::filterDateRange($start_date,$end_date)
                ->where('operator', $filterOperator)
                ->where('type', '!=', 1)
                ->distinct('operator')
                ->get(['operator']);

            }


            $Country = Country::all()->toArray();
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

            /* Admin Access All operator and Services */

            $user = Auth::user();
            $user_id = $user->id;
            $user_type = $user->type;
            $allowAllOperator = $user->WhowAccessAlOperator($user_type);

            if($allowAllOperator)
            {
                $reports = ReportsPnlsOperatorSummarizes::where('type', '!=', 1)
                ->orderBy('operator')
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

            $reportsByIDs = $this->getReportsByUnkownOperator($reports);
            $datesIndividual =Utility::getRangeDates($startColumnDateDisplay,$end_date);
            $no_of_days = Utility::getRangeDateNo($datesIndividual);

            if(!empty($Operators))
            {
                foreach($Operators as $operator)
                {
                    $tmpOperators=array();
                    $tmpOperators['operator'] = $operator->operator;
                    $OperatorCountry = array();

                    $reportsColumnData = $this->getUnkownPNLReportsDateWise($operator,$no_of_days,$reportsByIDs,$OperatorCountry);
                    $tmpOperators['month_string'] = $month;

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

                    $sumemry[] = $tmpOperators;
                }
            }

            $sumOfSummaryData = UtilityReports::summaryUnknownDataSum($sumemry);

            return view('report.unknown_operator', compact('no_of_days','sumemry','sumOfSummaryData','data','operatorss'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    // get pnl report by country id
    function getReportsCountryID($reports)
    {
        if(!empty($reports))
        {
            $reportsResult = array();
            $tempreport = array();
            foreach($reports as $report)
            {
                $tempreport[$report['country_id']][$report['date']] = $report;
            }

            $reportsResult =  $tempreport;
            return $reportsResult;
        }
    }

    // get pnl report by operator id
    function getReportsOperatorID($reports)
    {
        if(!empty($reports))
        {
            $reportsResult=array();
            $tempreport=array();

            foreach($reports as $report)
            {
                $tempreport[$report['operator_id']][$report['date']] = $report;
            }

            $reportsResult =  $tempreport;
            return $reportsResult;
        }
    }

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

    function getReportsByUnkownOperator($reports)
    {
        if(!empty($reports))
        {
            $reportsResult=array();
            $tempreport=array();
            foreach($reports as $report)
            {
                $tempreport[$report['operator']][$report['date']] = $report;
            }

            $reportsResult =  $tempreport;
            return $reportsResult;
        }
    }

    // get pnl report date wise
    function getPNLReportsDateWise($operator,$no_of_days,$reportsByIDs,$OperatorCountry)
    {
        if(\Auth::user()->can('PNL Detail'))
        {
            // dd($reportsByIDs);
            $usdValue = $OperatorCountry['usd'];
            $shareDb = array();
            $merchent_share = 1;
            $operator_share = 1;
            $vat = 0;
            $wht = 0;
            $misc_taxByDate = $operator->MiscTax;
            $revenue_share = $operator->revenueshare;


            $revenushare_by_dates = $operator->RevenushareByDate;
            $VatByDate = $operator->VatByDate;
            $WhtByDate = $operator->WhtByDate;

            $country_id = $OperatorCountry['id'];

            if(isset($revenue_share))
            {
                $merchent_share =$revenue_share->merchant_revenue_share;
                $operator_share =$revenue_share->operator_revenue_share;
            }

            if(isset($WhtByDate))
            {
                foreach ($WhtByDate as $key => $value) {
                    unset($WhtByDate[$key]);
                    $WhtByDate[$value['key']] = $value;
                }
            }
            if(isset($VatByDate))
            {
                foreach ($VatByDate as $key => $value) {
                    unset($VatByDate[$key]);
                    $VatByDate[$value['key']] = $value;
                }
            }

            if(isset($misc_taxByDate))
            {
                foreach ($misc_taxByDate as $key => $value) {
                    unset($misc_taxByDate[$key]);
                    $misc_taxByDate[$value['key']] = $value;
                }
            }
            if(isset($revenushare_by_dates))
            {
                foreach ($revenushare_by_dates as $key => $value) {
                    unset($revenushare_by_dates[$key]);
                    $revenushare_by_dates[$value['key']] = $value;
                }
            }

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
                $other_tax_Arr = array();
                $pnl_Arr = array();
                $net_after_tax_Arr = array();
                $net_revenue_after_tax_Arr = array();
                $br_Arr = array();
                $market_Arr = array();
                $misc_cost_Arr = array();
                $fp_Arr = array();
                $fp_success_Arr = array();
                $fp_failed_Arr = array();
                $dp_Arr = array();
                $dp_success_Arr = array();
                $dp_failed_Arr = array();
                $renewal_Arr = array();
                $vat_Arr = array();
                $spec_tax_Arr = array();
                $government_cost_Arr = array();
                $dealer_commision_Arr = array();
                $wht_Arr = array();
                $misc_tax_Arr = array();
                $uso_Arr = array();
                $agre_paxxa_Arr = array();
                $sbaf_Arr = array();
                $clicks_Arr = array();
                $ratio_for_cpa_Arr = array();
                $cpa_price_Arr = array();
                $cr_mo_clicks_Arr = array();
                $cr_mo_landing_Arr = array();
                $landing_Arr = array();
                $mo_Arr = array();
                $reg_Arr = array();
                $unreg_Arr = array();
                $price_mo_Arr = array();
                $price_mo_cost_Arr = array();
                $price_mo_mo_Arr = array();
                $active_subs_Arr = array();
                $arpu_7_Arr = array();
                $arpu_7_usd_Arr = array();
                $arpu_30_Arr = array();
                $arpu_30_usd_Arr = array();
                $reg_sub_Arr = array();
                $roi_Arr = array();
                $bill_Arr = array();
                $firstpush_Arr = array();
                $dailypush_Arr = array();
                $last_7_gros_rev_Arr = array();
                $last_7_reg_Arr = array();
                $last_30_gros_rev_Arr = array();
                $last_30_reg_Arr = array();
                $update = false;
                $last_update = '';

                $id_operator = isset($operator->id_operator) ? $operator->id_operator : $operator->id;
                $country_id = isset($operator->country_id) ? $operator->country_id : '';

                foreach($no_of_days as $days)
                {

                    $shareDb['merchent_share'] = $merchent_share;
                    $shareDb['operator_share'] = $operator_share;

                    $key_date = new Carbon($days['date']);
                    $key = $key_date->format("Y-m");

                    if(isset($revenushare_by_dates[$key]))
                    {
                        $merchent_share_by_dates = $revenushare_by_dates[$key]->merchant_revenue_share;
                        $operator_share_by_dates = $revenushare_by_dates[$key]->operator_revenue_share;

                        $shareDb['merchent_share'] = $merchent_share_by_dates;
                        $shareDb['operator_share'] = $operator_share_by_dates;
                    }

                    $keys = $id_operator.".".$days['date'];
                    $summariserow = Arr::get($reportsByIDs, $keys, 0);

                    if($summariserow !=0  && !$update)
                    {
                        $update = true;
                        $last_update = $summariserow['updated_at'];
                    }

                    $end_user_rev = isset($summariserow['rev']) ? $summariserow['rev'] : 0;
                    $end_user_rev = sprintf('%0.2f', $end_user_rev);

                    $end_user_rev_usd = $end_user_rev * $usdValue;
                    $end_user_rev_usd = sprintf('%0.2f', $end_user_rev_usd);

                    /*$gros_rev = isset($summariserow['lshare']) ? $summariserow['lshare'] : 0;
                    $gros_rev = sprintf('%0.2f', $gros_rev);

                    $gros_rev_usd = $gros_rev * $usdValue;
                    $gros_rev_usd = sprintf('%0.2f', $gros_rev_usd);*/

                    $gros_rev = UtilityReports::trat($shareDb,$end_user_rev);

                    $gros_rev_usd = UtilityReports::turt($shareDb,$end_user_rev_usd);

                    $cost_campaign = isset($summariserow['cost_campaign']) ? $summariserow['cost_campaign'] : 0;
                    $cost_campaign = sprintf('%0.2f', $cost_campaign);

                    $other_cost = isset($summariserow['other_cost']) ? $summariserow['other_cost'] : 0;
                    $other_cost = sprintf('%0.2f', $other_cost);

                    $hosting_cost = isset($summariserow['hosting_cost']) ? $summariserow['hosting_cost'] : 0;
                    $hosting_cost = sprintf('%0.2f', $hosting_cost);

                    $content = isset($summariserow['content']) ? $summariserow['content'] : 0;
                    $content = sprintf('%0.2f', $content);

                    $rnd = isset($summariserow['rnd']) ? $summariserow['rnd'] : 0;
                    $rnd = sprintf('%0.2f', $rnd);

                    // $bd = isset($summariserow['bd']) ? $summariserow['bd'] : 0;
                    // $bd = sprintf('%0.2f', $bd);
                    $bd = $gros_rev_usd * (2.5/100);

                    $market_cost = $gros_rev_usd * (1.5/100);

                    $misc_cost = !empty($operator->miscCost) ? $gros_rev_usd * ($operator->miscCost/100) : 0;

                    $platform = isset($summariserow['platform']) ? $summariserow['platform'] : 0;
                    $platform = sprintf('%0.2f', $platform);

                    $net_after_tax = isset($summariserow['net_after_tax']) ? $summariserow['net_after_tax'] : 0;
                    $net_after_tax = sprintf('%0.2f', $net_after_tax);

                    $net_revenue_after_tax = isset($summariserow['net_revenue_after_tax']) ? $summariserow['net_revenue_after_tax'] : 0;
                    $net_revenue_after_tax = sprintf('%0.2f', $net_revenue_after_tax);

                    $br = isset($summariserow['br']) ? $summariserow['br'] : 0;
                    $br = sprintf('%0.2f', $br);

                    $fp = isset($summariserow['fp']) ? $summariserow['fp'] : 0;
                    $fp = sprintf('%0.2f', $fp);

                    $fp_success = isset($summariserow['fp_success']) ? $summariserow['fp_success'] : 0;
                    $fp_success = sprintf('%0.2f', $fp_success);

                    $fp_failed = isset($summariserow['fp_failed']) ? $summariserow['fp_failed'] : 0;
                    $fp_failed = sprintf('%0.2f', $fp_failed);

                    $dp = isset($summariserow['dp']) ? $summariserow['dp'] : 0;
                    $dp = sprintf('%0.2f', $dp);

                    $dp_success = isset($summariserow['dp_success']) ? $summariserow['dp_success'] : 0;
                    $dp_success = sprintf('%0.2f', $dp_success);

                    $dp_failed = isset($summariserow['dp_failed']) ? $summariserow['dp_failed'] : 0;
                    $dp_failed = sprintf('%0.2f', $dp_failed);

                    $renewal = $dp_success + $dp_failed;

                    /*$vat = isset($summariserow['vat']) ? $summariserow['vat'] : 0;
                    $vat = sprintf('%0.2f', $vat);*/

                    $spec_tax = isset($summariserow['spec_tax']) ? $summariserow['spec_tax'] : 0;
                    $spec_tax = sprintf('%0.2f', $spec_tax);

                    $government_cost = isset($summariserow['government_cost']) ? $summariserow['government_cost'] : 0;
                    $government_cost = sprintf('%0.2f', $government_cost);

                    $dealer_commision = isset($summariserow['dealer_commision']) ? $summariserow['dealer_commision'] : 0;
                    $dealer_commision = sprintf('%0.2f', $dealer_commision);

                    /*$wht = isset($summariserow['wht']) ? $summariserow['wht'] : 0;
                    $wht = sprintf('%0.2f', $wht);

                    $misc_tax = isset($summariserow['misc_tax']) ? $summariserow['misc_tax'] : 0;
                    $misc_tax = sprintf('%0.2f', $misc_tax);*/

                    $vat = !empty($operator->vat) ? $gros_rev_usd * ($operator->vat/100) : 0;

                    $wht = !empty($operator->wht) ? $gros_rev_usd * ($operator->wht/100) : 0;
                    $misc_tax = !empty($operator->miscTax) ? $gros_rev_usd * ($operator->miscTax/100) : 0;


                    if(isset($VatByDate[$key]))
                    {
                        $Vat = $VatByDate[$key]->vat;
                        $vat = !empty($Vat) ? $gros_rev_usd * ($Vat/100) : 0;

                    }
                    if(isset($WhtByDate[$key]))
                    {
                        $Wht = $WhtByDate[$key]->wht;
                        $wht = !empty($Wht) ? $gros_rev_usd * ($Wht/100) : 0;

                    }
                    if(isset($misc_taxByDate[$key]))
                    {
                        $Misc_tax = $misc_taxByDate[$key]->misc_tax;
                        $misc_tax = !empty($Misc_tax) ? $gros_rev_usd * ($Misc_tax/100) : 0;

                    }



                    $other_tax = $vat + $wht + $misc_tax;

                    if($other_tax != 0){
                        $net_after_tax = $gros_rev_usd - $other_tax;
                    }else{
                        $net_after_tax = $gros_rev_usd;
                    }

                    $uso = isset($summariserow['uso']) ? $summariserow['uso'] : 0;
                    $uso = sprintf('%0.2f', $uso);

                    $agre_paxxa = isset($summariserow['agre_paxxa']) ? $summariserow['agre_paxxa'] : 0;
                    $agre_paxxa = sprintf('%0.2f', $agre_paxxa);

                    $sbaf = isset($summariserow['sbaf']) ? $summariserow['sbaf'] : 0;
                    $sbaf = sprintf('%0.2f', $sbaf);

                    $clicks = isset($summariserow['clicks']) ? $summariserow['clicks'] : 0;
                    $clicks = sprintf('%0.2f', $clicks);

                    $ratio_for_cpa = isset($summariserow['ratio_for_cpa']) ? $summariserow['ratio_for_cpa'] : 0;
                    $ratio_for_cpa = sprintf('%0.2f', $ratio_for_cpa);

                    $cpa_price = isset($summariserow['cpa_price']) ? $summariserow['cpa_price'] : 0;
                    $cpa_price = sprintf('%0.2f', $cpa_price);

                    $cr_mo_clicks = isset($summariserow['cr_mo_clicks']) ? $summariserow['cr_mo_clicks'] : 0;
                    $cr_mo_clicks = sprintf('%0.2f', $cr_mo_clicks);

                    $cr_mo_landing = isset($summariserow['cr_mo_landing']) ? $summariserow['cr_mo_landing'] : 0;
                    $cr_mo_landing = sprintf('%0.2f', $cr_mo_landing);

                    $landing = isset($summariserow['landing']) ? $summariserow['landing'] : 0;
                    $landing = sprintf('%0.2f', $landing);

                    $mo = isset($summariserow['mo']) ? $summariserow['mo'] : 0;
                    $mo = sprintf('%0.2f', $mo);

                    $reg = isset($summariserow['reg']) ? $summariserow['reg'] : 0;
                    $reg = sprintf('%0.2f', $reg);

                    $unreg = isset($summariserow['unreg']) ? $summariserow['unreg'] : 0;
                    $unreg = sprintf('%0.2f', $unreg);

                    $price_mo = ($mo == 0) ? (float)0 : ($cost_campaign/$mo);

                    $price_mo_cost = $cost_campaign;
                    $price_mo_mo = $mo;

                    $active_subs = isset($summariserow['active_subs']) ? $summariserow['active_subs'] : 0;
                    $active_subs = sprintf('%0.2f', $active_subs);

                    $arpu_7 = UtilityReports::Arpu7USD($operator,$reportsByIDs,$days,$active_subs,$shareDb);
                    $arpu_7 = sprintf('%0.2f', $arpu_7);

                    $arpu_7_usd = $arpu_7*$usdValue;

                    $arpu_30 = UtilityReports::Arpu30USD($operator,$reportsByIDs,$days,$active_subs,$shareDb);
                    $arpu_30 = sprintf('%0.2f', $arpu_30);

                    $arpu_30_usd = $arpu_30*$usdValue;

                    $ROI = UtilityReports::ROI($id_operator,$reportsByIDs,$days,$active_subs,$cost_campaign,$mo);
                    $roi = $ROI['roi'];

                    $billRate = UtilityReports::billRate($dp_success,$dp_failed,$active_subs);

                    $firstpush = UtilityReports::FirstPush($fp_success,$fp_failed,$active_subs);

                    $dailypush = UtilityReports::Dailypush($dp_success,$dp_failed,$active_subs);

                    $last_30_gros_rev = $ROI['last_30_gros_rev'];
                    $last_30_reg = $ROI['last_30_reg'];
                    $last_7_gros_rev = $ROI['last_7_gros_rev'];
                    $last_7_reg = $ROI['last_7_reg'];

                    $reg_sub = $reg + $active_subs;

                    $other_cost = $bd + $hosting_cost + $content + $rnd + $market_cost + $misc_cost;

                    if(isset($operator) && $operator['operator_name'] == 'uae-etisalat-airpay'){
                        $pnl = isset($end_user_rev_usd) ? $end_user_rev_usd*4/100 : 0;
                        $pnl = sprintf('%0.2f', $pnl);
                    }elseif (isset($operator) && $operator['operator_name'] == 'omn-omantel-airpay') {
                        $pnl = isset($end_user_rev_usd) ? $end_user_rev_usd*10/100 : 0;
                        $pnl = sprintf('%0.2f', $pnl);
                    }else{
                        $pnl = $net_after_tax - ($other_cost + $cost_campaign);
                        $pnl = sprintf('%0.2f', $pnl);
                    }

                    if(isset($id_operator) && $id_operator == 115){
                        $pnl = isset($end_user_rev_usd) ? $end_user_rev_usd*6/100 : 0;
                    }

                    if (isset($id_operator) && $id_operator == 102) {
                        $pnl = isset($end_user_rev_usd) ? $end_user_rev_usd*4/100 : 0;
                    }

                    if ($id_operator == 167 || $id_operator == 168 || $id_operator == 170 || $id_operator == 171 || $id_operator == 176) {
                        $pnl = isset($end_user_rev_usd) ? $end_user_rev_usd*5/100 : 0;
                    }

                    /*$roi_arpu = ($reg == 0) ?  (float)0 : $gros_rev_usd / $reg;

                    $roi = ($roi_arpu == 0) ? (float)0 : ($price_mo / $roi_arpu);*/

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

                    $market_Arr[$days['date']]['value'] = $market_cost;
                    $market_Arr[$days['date']]['class'] = "bg-hui";

                    $misc_cost_Arr[$days['date']]['value'] = $misc_cost;
                    $misc_cost_Arr[$days['date']]['class'] = "bg-hui";

                    $platform_Arr[$days['date']]['value'] = $platform;
                    $platform_Arr[$days['date']]['class'] = "bg-hui";

                    $other_tax_Arr[$days['date']]['value'] = $other_tax;
                    $other_tax_Arr[$days['date']]['class'] = "bg-hui";

                    $pnl_Arr[$days['date']]['value'] = $pnl;
                    $pnl_Arr[$days['date']]['class'] = "bg-hui";

                    $net_after_tax_Arr[$days['date']]['value'] = $net_after_tax;
                    $net_after_tax_Arr[$days['date']]['class'] = "bg-hui";

                    $net_revenue_after_tax_Arr[$days['date']]['value'] = $net_revenue_after_tax;
                    $net_revenue_after_tax_Arr[$days['date']]['class'] = "bg-hui";

                    $br_Arr[$days['date']]['value'] = $br;
                    $br_Arr[$days['date']]['class'] = "bg-hui";

                    $fp_Arr[$days['date']]['value'] = $fp;
                    $fp_Arr[$days['date']]['class'] = "bg-hui";

                    $fp_success_Arr[$days['date']]['value'] = $fp_success;
                    $fp_success_Arr[$days['date']]['class'] = "bg-hui";

                    $fp_failed_Arr[$days['date']]['value'] = $fp_failed;
                    $fp_failed_Arr[$days['date']]['class'] = "bg-hui";

                    $dp_Arr[$days['date']]['value'] = $dp;
                    $dp_Arr[$days['date']]['class'] = "bg-hui";

                    $dp_success_Arr[$days['date']]['value'] = $dp_success;
                    $dp_success_Arr[$days['date']]['class'] = "bg-hui";

                    $dp_failed_Arr[$days['date']]['value'] = $dp_failed;
                    $dp_failed_Arr[$days['date']]['class'] = "bg-hui";

                    $renewal_Arr[$days['date']]['value'] = $renewal;
                    $renewal_Arr[$days['date']]['class'] = "bg-hui";

                    $vat_Arr[$days['date']]['value'] = $vat;
                    $vat_Arr[$days['date']]['class'] = "bg-hui";

                    $spec_tax_Arr[$days['date']]['value'] = $spec_tax;
                    $spec_tax_Arr[$days['date']]['class'] = "bg-hui";

                    $government_cost_Arr[$days['date']]['value'] = $government_cost;
                    $government_cost_Arr[$days['date']]['class'] = "bg-hui";

                    $dealer_commision_Arr[$days['date']]['value'] = $dealer_commision;
                    $dealer_commision_Arr[$days['date']]['class'] = "bg-hui";

                    $wht_Arr[$days['date']]['value'] = $wht;
                    $wht_Arr[$days['date']]['class'] = "bg-hui";

                    $misc_tax_Arr[$days['date']]['value'] = $misc_tax;
                    $misc_tax_Arr[$days['date']]['class'] = "bg-hui";

                    $uso_Arr[$days['date']]['value'] = $uso;
                    $uso_Arr[$days['date']]['class'] = "bg-hui";

                    $agre_paxxa_Arr[$days['date']]['value'] = $agre_paxxa;
                    $agre_paxxa_Arr[$days['date']]['class'] = "bg-hui";

                    $sbaf_Arr[$days['date']]['value'] = $sbaf;
                    $sbaf_Arr[$days['date']]['class'] = "bg-hui";

                    $clicks_Arr[$days['date']]['value'] = $clicks;
                    $clicks_Arr[$days['date']]['class'] = "bg-hui";

                    $ratio_for_cpa_Arr[$days['date']]['value'] = $ratio_for_cpa;
                    $ratio_for_cpa_Arr[$days['date']]['class'] = "bg-hui";

                    $cpa_price_Arr[$days['date']]['value'] = $cpa_price;
                    $cpa_price_Arr[$days['date']]['class'] = "bg-hui";

                    $cr_mo_clicks_Arr[$days['date']]['value'] = $cr_mo_clicks;
                    $cr_mo_clicks_Arr[$days['date']]['class'] = "bg-hui";

                    $cr_mo_landing_Arr[$days['date']]['value'] = $cr_mo_landing;
                    $cr_mo_landing_Arr[$days['date']]['class'] = "bg-hui";

                    $landing_Arr[$days['date']]['value'] = $landing;
                    $landing_Arr[$days['date']]['class'] = "bg-hui";

                    $mo_Arr[$days['date']]['value'] = $mo;
                    $mo_Arr[$days['date']]['class'] = "bg-hui";

                    $reg_Arr[$days['date']]['value'] = $reg;
                    $reg_Arr[$days['date']]['class'] = "bg-hui";

                    $unreg_Arr[$days['date']]['value'] = $unreg;
                    $unreg_Arr[$days['date']]['class'] = "bg-hui";

                    $price_mo_Arr[$days['date']]['value'] = $price_mo;
                    $price_mo_Arr[$days['date']]['class'] = "bg-hui";

                    $price_mo_cost_Arr[$days['date']]['value'] = $price_mo_cost;
                    $price_mo_cost_Arr[$days['date']]['class'] = "bg-hui";

                    $price_mo_mo_Arr[$days['date']]['value'] = $price_mo_mo;
                    $price_mo_mo_Arr[$days['date']]['class'] = "bg-hui";

                    $active_subs_Arr[$days['date']]['value'] = $active_subs;
                    $active_subs_Arr[$days['date']]['class'] = "bg-hui";

                    $arpu_7_Arr[$days['date']]['value'] = $arpu_7;
                    $arpu_7_Arr[$days['date']]['class'] = "bg-hui";

                    $arpu_7_usd_Arr[$days['date']]['value'] = $arpu_7_usd;
                    $arpu_7_usd_Arr[$days['date']]['class'] = "bg-hui";

                    $arpu_30_Arr[$days['date']]['value'] = $arpu_30;
                    $arpu_30_Arr[$days['date']]['class'] = "bg-hui";

                    $arpu_30_usd_Arr[$days['date']]['value'] = $arpu_30_usd;
                    $arpu_30_usd_Arr[$days['date']]['class'] = "bg-hui";

                    $reg_sub_Arr[$days['date']]['value'] = $reg_sub;
                    $reg_sub_Arr[$days['date']]['class'] = "bg-hui";

                    $roi_Arr[$days['date']]['value'] = $roi;
                    $roi_Arr[$days['date']]['class'] = "bg-hui";

                    $bill_Arr[$days['date']]['value'] = $billRate;
                    $bill_Arr[$days['date']]['class'] = "bg-hui";

                    $firstpush_Arr[$days['date']]['value'] = $firstpush;
                    $firstpush_Arr[$days['date']]['class'] = "bg-hui";

                    $dailypush_Arr[$days['date']]['value'] = $dailypush;
                    $dailypush_Arr[$days['date']]['class'] = "bg-hui";

                    $last_7_gros_rev_Arr[$days['date']]['value'] = $last_7_gros_rev;
                    $last_7_gros_rev_Arr[$days['date']]['class'] = "bg-hui";

                    $last_7_reg_Arr[$days['date']]['value'] = $last_7_reg;
                    $last_7_reg_Arr[$days['date']]['class'] = "bg-hui";

                    $last_30_gros_rev_Arr[$days['date']]['value'] = $last_30_gros_rev;
                    $last_30_gros_rev_Arr[$days['date']]['class'] = "bg-hui";

                    $last_30_reg_Arr[$days['date']]['value'] = $last_30_reg;
                    $last_30_reg_Arr[$days['date']]['class'] = "bg-hui";
                }

                $last_update_show = "Not updated last month";
                if($last_update!="")
                {
                    $last_update_timestamp = Carbon::parse($last_update);
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
                $allColumnData['market_cost'] = $market_Arr;
                $allColumnData['misc_cost'] = $misc_cost_Arr;
                $allColumnData['platform'] = $platform_Arr;
                $allColumnData['pnl'] = $pnl_Arr;
                $allColumnData['net_after_tax'] = $net_after_tax_Arr;
                $allColumnData['net_revenue_after_tax'] = $net_revenue_after_tax_Arr;
                $allColumnData['br'] = $br_Arr;
                $allColumnData['fp'] = $fp_Arr;
                $allColumnData['fp_success'] = $fp_success_Arr;
                $allColumnData['fp_failed'] = $fp_failed_Arr;
                $allColumnData['dp'] = $dp_Arr;
                $allColumnData['dp_success'] = $dp_success_Arr;
                $allColumnData['dp_failed'] = $dp_failed_Arr;
                $allColumnData['renewal'] = $renewal_Arr;
                $allColumnData['vat'] = $vat_Arr;
                $allColumnData['other_tax'] = $other_tax_Arr;
                $allColumnData['spec_tax'] = $spec_tax_Arr;
                $allColumnData['government_cost'] = $government_cost_Arr;
                $allColumnData['dealer_commision'] = $dealer_commision_Arr;
                $allColumnData['wht'] = $wht_Arr;
                $allColumnData['misc_tax'] = $misc_tax_Arr;
                $allColumnData['uso'] = $uso_Arr;
                $allColumnData['agre_paxxa'] = $agre_paxxa_Arr;
                $allColumnData['sbaf'] = $sbaf_Arr;
                $allColumnData['clicks'] = $clicks_Arr;
                $allColumnData['ratio_for_cpa'] = $ratio_for_cpa_Arr;
                $allColumnData['cpa_price'] = $cpa_price_Arr;
                $allColumnData['cr_mo_clicks'] = $cr_mo_clicks_Arr;
                $allColumnData['cr_mo_landing'] = $cr_mo_landing_Arr;
                $allColumnData['landing'] = $landing_Arr;
                $allColumnData['mo'] = $mo_Arr;
                $allColumnData['reg'] = $reg_Arr;
                $allColumnData['unreg'] = $unreg_Arr;
                $allColumnData['price_mo'] = $price_mo_Arr;
                $allColumnData['price_mo_cost'] = $price_mo_cost_Arr;
                $allColumnData['price_mo_mo'] = $price_mo_mo_Arr;
                $allColumnData['active_subs'] = $active_subs_Arr;
                $allColumnData['arpu_7'] = $arpu_7_Arr;
                $allColumnData['arpu_7_usd'] = $arpu_7_usd_Arr;
                $allColumnData['arpu_30'] = $arpu_30_Arr;
                $allColumnData['arpu_30_usd'] = $arpu_30_usd_Arr;
                $allColumnData['reg_sub'] = $reg_sub_Arr;
                $allColumnData['roi'] = $roi_Arr;
                $allColumnData['bill'] = $bill_Arr;
                $allColumnData['firstpush'] = $firstpush_Arr;
                $allColumnData['dailypush'] = $dailypush_Arr;
                $allColumnData['last_7_gros_rev'] = $last_7_gros_rev_Arr;
                $allColumnData['last_7_reg'] = $last_7_reg_Arr;
                $allColumnData['last_30_gros_rev'] = $last_30_gros_rev_Arr;
                $allColumnData['last_30_reg'] = $last_30_reg_Arr;
                $allColumnData['last_update'] = $last_update_show;

                return $allColumnData;
            }
        }
    }

    function getUnkownPNLReportsDateWise($operator,$no_of_days,$reportsByIDs,$OperatorCountry)
    {
        $usdValue =isset($OperatorCountry['usd'])?$OperatorCountry['usd']:1;
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
            $id_operator = isset($operator->operator) ? $operator->operator : $operator->id_operator;
            $testUSDSum = 0;

            foreach($no_of_days as $days)
            {
                $keys = $id_operator.".".$days['date'];
                $summariserow = Arr::get($reportsByIDs, $keys, 0);

                $end_user_rev_usd = 0;
                $end_user_rev = isset($summariserow['rev']) ? $summariserow['rev'] : 0;
                $gros_rev_usd = 0;
                $gros_rev = isset($summariserow['lshare']) ? $summariserow['lshare'] : 0;
                $cost_campaign = isset($summariserow['cost_campaign']) ? $summariserow['cost_campaign'] : 0;
                $other_cost = isset($summariserow['other_cost']) ? $summariserow['other_cost'] : 0;
                $hosting_cost = isset($summariserow['hosting_cost']) ? $summariserow['hosting_cost'] : 0;
                $content = isset($summariserow['content']) ? $summariserow['content'] : 0;
                $rnd = isset($summariserow['rnd']) ? $summariserow['rnd'] : 0;
                $bd = isset($summariserow['bd']) ? $summariserow['bd'] : 0;
                $platform = isset($summariserow['platform']) ? $summariserow['platform'] : 0;
                $pnl = isset($summariserow['pnl']) ? $summariserow['pnl'] : 0;
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
            return $allColumnData;
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

    function getUserReportsByOperator($reports)
    {
        if(!empty($reports))
        {
            $reportsResult=array();
            $tempreport=array();
            foreach($reports as $report)
            {
                $tempreport[$report['operator_id']][$report['date']] = $report;
            }

            $reportsResult =  $tempreport;
            return $reportsResult;
        }
    }

}
