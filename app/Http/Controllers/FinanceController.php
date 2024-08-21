<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Session;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\RevenueReconciles;
use App\Models\TargetRevenueReconciles;
use App\Models\TargetOpex;
use App\Models\FinalCostReports;
use App\Models\report_summarize;
use App\Models\ReportsPnlsOperatorSummarizes;
use App\Models\Operator;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use App\Models\Company;
use App\Models\Country;
use App\Models\User;
use App\Models\UsersOperatorsServices;
use App\Models\ServiceHistory;
use App\Models\role_operators;
use App\Models\CompanyOperators;
use App\Models\MonthlyReportSummery;
use App\Models\PnlSummeryMonth;
use App\common\Utility;
use App\common\UtilityReports;
use App\common\UtilityFinanceReports;
use Config;
use App\common\UtilityReportsMonthly;
use Excel;
use App\Imports\UsersImport;

class FinanceController extends Controller
{
    public function revenueReconcile(Request $request)
    {
        if(\Auth::user()->can('Revenue Reconcile'))
        {
            $years = [];
            $months = [];
            $data = [];
            $countries = [];
            $currency_codes = [];
            $flags = [];
            $usd = [];
            $summary = [];
            $Country = Country::all()->toArray();
            $countries = array();
            $user = 0;
            $CountryId = $req_CountryId = $request->country;
            $CompanyId = $req_CompanyId = $request->company;
            $UserId = $req_UserId = $request->business_manager;
            $filterOperator = $req_filterOperator = $request->operatorId;

            $companys = Company::get();

            if(!empty($Country))
            {
                foreach($Country as $CountryI)
                {
                    $countries[$CountryI['id']] = $CountryI;
                }
            }

            $showAllOperator = true;

            if($request->filled('company') && $req_CompanyId != "allcompany" && !$request->filled('operatorId'))
            {
                $companies = Company::Find($req_CompanyId);
                $Operators_company = array();

                if(!empty($companies))
                {
                    $Operators_company = $companies->company_operators->pluck('operator_id')->toArray();
                    $Operators = Operator::with('revenueshare','RevenushareByDate')
                    ->Status(1)
                    ->GetOperatorByOperatorId($Operators_company)
                    ->get();
                }

                $showAllOperator = false;
            }

            if($request->filled('company') && $request->filled('country') && !$request->filled('operatorId'))
            {
                $data = [
                    'id' => $req_CountryId,
                    'company' => $req_CompanyId,
                ];

                $requestobj = new Request($data);
                $ReportControllerobj = new ReportController;
                $Operators = $ReportControllerobj->userFilterOperator($requestobj);
                $showAllOperator = false;
            }

            if (isset($req_CompanyId) && !$request->filled('country') && $request->filled('business_manager') && !$request->filled('operatorId')) {
                $Countrys[0] = Country::with('operators')->Find($CountryId);
                $data = [
                    'country' => $req_CountryId,
                    'company' => $req_CompanyId,
                    'business_manager' => $req_UserId,
                ];

                $requestobj = new Request($data);
                $FinanceControllerobj = new FinanceController;
                $Operators = $FinanceControllerobj->userFilterBusinessManagerOperator($requestobj);
                $CountryFlag = false;
                $showAllOperator = false;
                $user = $req_UserId;
            }

            if ($request->filled('company') && $request->filled('country') && $request->filled('business_manager') && !$request->filled('operatorId')) {
                $Countrys[0] = Country::with('operators')->Find($CountryId);
                $data = [
                    'country' => $req_CountryId,
                    'company' => $req_CompanyId,
                    'business_manager' => $req_UserId,
                ];

                $requestobj = new Request($data);
                $FinanceControllerobj = new FinanceController;
                $Operators = $FinanceControllerobj->userFilterBusinessManagerOperator($requestobj);
                $CountryFlag = false;
                $showAllOperator = false;
                $user = $req_UserId;
            }

            if($request->filled('operatorId'))
            {
                $Operators = Operator::with('revenueshare','RevenushareByDate')
                ->Status(1)
                ->GetOperatorByOperatorId($filterOperator)
                ->get();

                $showAllOperator = false;
            }

            if($showAllOperator)
            {
                $Operators = Operator::with('revenueshare','RevenushareByDate')->Status(1)->get();
            }

            $arrayOperatorsIds = $Operators->pluck('id_operator')->toArray();

            $selected_year = $request->year ? $request->year : date('Y');

            for($i = date('Y'); $i >= 2022; $i--){
                $years[] = $i;
            }

            $firstDayoftheyear = Carbon::create($selected_year)->startOfYear()->format('Y-m-d');
            $start_date = $firstDayoftheyear;
            $end_date = Carbon::create($selected_year)->endOfYear()->format('Y-m-d');

            if($selected_year == date('Y'))
            {
                $end_date = Carbon::now()->format('Y-m-d');
            }

            $datesIndividual = Utility::getRangeDates($firstDayoftheyear,$end_date);
            $no_of_months = Utility::getRangeMonthsNo($datesIndividual);
            $month = Carbon::now()->format('F Y');

            $monthList = array();

            foreach ($no_of_months as $key => $no_of_month) {
                $month_key = $no_of_month['date'];
                $monthList[] = $month_key;
            }

            $allMonthlyData = MonthlyReportSummery::select('gros_rev','operator_id','key')
            ->filteroperator($arrayOperatorsIds)
            ->Months($monthList)
            ->User($user)
            ->get()
            ->toArray();

            $RevenueReconciles = RevenueReconciles::filteroperators($arrayOperatorsIds)
            ->Months($monthList)
            ->SumReconcile()
            ->get()
            ->toArray();

            $reportsMonthData = $this->rearrangeOperatorMonth($allMonthlyData);
            $reportsMonthReconcileData = $this->rearrangeOperatorMonth($RevenueReconciles);
            $monthdata = $reportsMonthData;
            $monthreconciledata = $reportsMonthReconcileData;

            if(!empty($Operators))
            {
                foreach($Operators as $operator)
                {
                    $tmpOperators = array();

                    $id_operator = $operator->id_operator;
                    $country_id = $operator->country_id;

                    if(isset($operator['revenueshare']) && !empty($operator['revenueshare'])){
                        $operator['share'] = $operator['revenueshare']['merchant_revenue_share'];
                    }else{
                        $operator['share'] = '0';
                    }

                    $operator['total_tax'] = (int)$operator['vat'] + (int)$operator['wht'] + (int)$operator['miscTax'];

                    $tmpOperators['operator'] = $operator;

                    if(!isset($reportsMonthData[$id_operator]))
                    {
                        continue;
                    }

                    $country_id = $operator->country_id;
                    $contain_id = Arr::exists($countries, $country_id);
                    $OperatorCountry = array();

                    if($contain_id)
                    {
                        $tmpOperators['country'] = $countries[$country_id];
                        $OperatorCountry = $countries[$country_id];
                    }

                    $reportsColumnData = $this->getReportsMonthWise($operator,$no_of_months,$monthdata,$monthreconciledata,$OperatorCountry);

                    $tmpOperators['month_string'] = $month;

                    $total_dlr = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['dlr'],$firstDayoftheyear,$end_date);
                    $tmpOperators['dlr']['dates'] = $reportsColumnData['dlr'];
                    $tmpOperators['dlr']['total'] = $total_dlr['sum'];

                    $total_fir = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['fir'],$firstDayoftheyear,$end_date);
                    $tmpOperators['fir']['dates'] = $reportsColumnData['fir'];
                    $tmpOperators['fir']['total'] = $total_fir['sum'];

                    $total_fir_usd = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['fir_usd'],$firstDayoftheyear,$end_date);
                    $tmpOperators['fir_usd']['dates'] = $reportsColumnData['fir_usd'];
                    $tmpOperators['fir_usd']['total'] = $total_fir_usd['sum'];

                    $total_discrepency = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['discrepency'],$firstDayoftheyear,$end_date);
                    $tmpOperators['discrepency']['dates'] = $reportsColumnData['discrepency'];
                    $dlr = $tmpOperators['dlr']['total'];
                    $fir = $tmpOperators['fir']['total'];
                    $tmpOperators['discrepency']['total'] = ($dlr != 0 && $fir != 0) ? (($fir - $dlr)/$dlr)*100 : (float)0 ;

                    $total_dlr_after_telco = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['dlr_after_telco'],$firstDayoftheyear,$end_date);
                    $tmpOperators['dlr_after_telco']['dates'] = $reportsColumnData['dlr_after_telco'];
                    $tmpOperators['dlr_after_telco']['total'] = $total_dlr_after_telco['sum'];

                    $total_fir_after_telco = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['fir_after_telco'],$firstDayoftheyear,$end_date);
                    $tmpOperators['fir_after_telco']['dates'] = $reportsColumnData['fir_after_telco'];
                    $tmpOperators['fir_after_telco']['total'] = $total_fir_after_telco['sum'];

                    $total_fir_after_telco_usd = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['fir_after_telco_usd'],$firstDayoftheyear,$end_date);
                    $tmpOperators['fir_after_telco_usd']['dates'] = $reportsColumnData['fir_after_telco_usd'];
                    $tmpOperators['fir_after_telco_usd']['total'] = $total_fir_after_telco_usd['sum'];

                    $total_discrepency_after_telco = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['discrepency_after_telco'],$firstDayoftheyear,$end_date);
                    $tmpOperators['discrepency_after_telco']['dates'] = $reportsColumnData['discrepency_after_telco'];
                    $dlr_after_telco = $tmpOperators['dlr_after_telco']['total'];
                    $fir_after_telco = $tmpOperators['fir_after_telco']['total'];
                    $tmpOperators['discrepency_after_telco']['total'] = ($dlr_after_telco != 0 && $fir_after_telco != 0) ? (($fir_after_telco - $dlr_after_telco)/$dlr_after_telco)*100 : (float)0;

                    $total_net_revenue = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['net_revenue'],$firstDayoftheyear,$end_date);
                    $tmpOperators['net_revenue']['dates'] = $reportsColumnData['net_revenue'];
                    $tmpOperators['net_revenue']['total'] = $total_net_revenue['sum'];

                    $total_fir_net_revenue = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['fir_net_revenue'],$firstDayoftheyear,$end_date);
                    $tmpOperators['fir_net_revenue']['dates'] = $reportsColumnData['fir_net_revenue'];
                    $tmpOperators['fir_net_revenue']['total'] = $total_fir_net_revenue['sum'];

                    $total_fir_net_revenue_usd = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['fir_net_revenue_usd'],$firstDayoftheyear,$end_date);
                    $tmpOperators['fir_net_revenue_usd']['dates'] = $reportsColumnData['fir_net_revenue_usd'];
                    $tmpOperators['fir_net_revenue_usd']['total'] = $total_fir_net_revenue_usd['sum'];

                    $total_discrepency_net_revenue = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['discrepency_net_revenue'],$firstDayoftheyear,$end_date);
                    $tmpOperators['discrepency_net_revenue']['dates'] = $reportsColumnData['discrepency_net_revenue'];
                    $net_revenue = $tmpOperators['net_revenue']['total'];
                    $fir_net_revenue = $tmpOperators['fir_net_revenue']['total'];
                    $tmpOperators['discrepency_net_revenue']['total'] = ($net_revenue != 0 && $fir_net_revenue != 0) ? (($fir_net_revenue - $net_revenue)/$net_revenue)*100 : (float)0;

                    $total_gros_rev_usd = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['gros_rev_usd'],$firstDayoftheyear,$end_date);
                    $tmpOperators['gros_rev_usd']['dates'] = $reportsColumnData['gros_rev_usd'];
                    $tmpOperators['gros_rev_usd']['total'] = $total_gros_rev_usd['sum'];

                    $total_gros_rev_usd_after_telco = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['gros_rev_usd_after_telco'],$firstDayoftheyear,$end_date);
                    $tmpOperators['gros_rev_usd_after_telco']['dates'] = $reportsColumnData['gros_rev_usd_after_telco'];
                    $tmpOperators['gros_rev_usd_after_telco']['total'] = $total_gros_rev_usd_after_telco['sum'];

                    $total_net_revenue_usd = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['net_revenue_usd'],$firstDayoftheyear,$end_date);
                    $tmpOperators['net_revenue_usd']['dates'] = $reportsColumnData['net_revenue_usd'];
                    $tmpOperators['net_revenue_usd']['total'] = $total_net_revenue_usd['sum'];

                    $tmpOperators['file']['dates'] = $reportsColumnData['file'];

                    $summary[] = $tmpOperators;
                }
            }

            // All country's revenue reconcile sum
            $allsummaryData = UtilityFinanceReports::sumOfAllReconcileData($summary);

            // Country Sum from Operator array
            $displayCountries = array();
            $SelectedCountries = array();

            if(!empty($summary))
            {
                foreach ($summary as $key => $sumemries) {
                    $country_id = $sumemries['country']['id'];
                    $SelectedCountries[$country_id] = $sumemries['country'];
                    $displayCountries[$country_id]['country'] = $sumemries['country'];
                    $displayCountries[$country_id]['operator'][] = $sumemries;
                }
            }

            if(!empty($displayCountries))
            {
                foreach ($displayCountries as $c_id => $country_data) {
                    $countryReconcileSum = UtilityFinanceReports::sumOfCountryReconcileData($country_data['operator']);
                    $displayCountries[$c_id]['countrySum'] = $countryReconcileSum;
                }
            }

            $countryWiseData = $displayCountries;
            $no_of_days = $no_of_months;

            return view('finance.index', compact('years','no_of_days','countryWiseData','allsummaryData'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function targetRevenue(Request $request)
    {
        if(\Auth::user()->can('Target Revenue'))
        {
            $years = [];
            $months = [];
            $data = [];
            $countries = [];
            $currency_codes = [];
            $flags = [];
            $usd = [];
            $summary = [];
            $Country = Country::all()->toArray();
            $countries = array();
            $CountryId = $req_CountryId = $request->country;
            $CompanyId = $req_CompanyId = $request->company;
            $UserId = $req_UserId = $request->business_manager;
            $filterOperator = $req_filterOperator = $request->operatorId;

            $companys = Company::get();

            if(!empty($Country))
            {
                foreach($Country as $CountryI)
                {
                    $countries[$CountryI['id']] = $CountryI;
                }
            }

            $showAllOperator = true;

            if($request->filled('company') && $req_CompanyId != "allcompany" && !$request->filled('operatorId'))
            {
                $companies = Company::Find($req_CompanyId);
                $Operators_company = array();

                if(!empty($companies))
                {
                    $Operators_company = $companies->company_operators->pluck('operator_id')->toArray();
                    $Operators = Operator::with('revenueshare','RevenushareByDate')
                    ->Status(1)
                    ->GetOperatorByOperatorId($Operators_company)
                    ->get();
                }

                $showAllOperator = false;
            }

            if($request->filled('company') && $request->filled('country') && !$request->filled('operatorId'))
            {
                $data = [
                    'id' => $req_CountryId,
                    'company' => $req_CompanyId,
                ];

                $requestobj = new Request($data);
                $ReportControllerobj = new ReportController;
                $Operators = $ReportControllerobj->userFilterOperator($requestobj);
                $showAllOperator = false;
            }

            if (isset($req_CompanyId) && !$request->filled('country') && $request->filled('business_manager') && !$request->filled('operatorId')) {
                $Countrys[0] = Country::with('operators')->Find($CountryId);
                $data = [
                    'country' => $req_CountryId,
                    'company' => $req_CompanyId,
                    'business_manager' => $req_UserId,
                ];

                $requestobj = new Request($data);
                $FinanceControllerobj = new FinanceController;
                $Operators = $FinanceControllerobj->userFilterBusinessManagerOperator($requestobj);
                $CountryFlag = false;
                $showAllOperator = false;
            }

            if ($request->filled('company') && $request->filled('country') && $request->filled('business_manager') && !$request->filled('operatorId')) {
                $Countrys[0] = Country::with('operators')->Find($CountryId);
                $data = [
                    'country' => $req_CountryId,
                    'company' => $req_CompanyId,
                    'business_manager' => $req_UserId,
                ];

                $requestobj = new Request($data);
                $FinanceControllerobj = new FinanceController;
                $Operators = $FinanceControllerobj->userFilterBusinessManagerOperator($requestobj);
                $CountryFlag = false;
                $showAllOperator = false;
            }

            if($request->filled('operatorId'))
            {
                $Operators = Operator::with('revenueshare','RevenushareByDate')
                ->Status(1)
                ->GetOperatorByOperatorId($filterOperator)
                ->get();

                $showAllOperator = false;
            }

            if($showAllOperator)
            {
                $Operators = Operator::with('revenueshare','RevenushareByDate')->Status(1)->get();
            }

            $arrayOperatorsIds = $Operators->pluck('id_operator')->toArray();

            $requestData = $request->all();
            $selected_country = $data['selected_country'] = isset($requestData['country']) ? $requestData['country'] : '';
            $selected_year = $data['selected_year'] = isset($requestData['year']) ? $requestData['year'] : date('Y');

            for($i = date('Y'); $i >= 2022; $i--){
                $years[] = $i;
            }

            $firstDayoftheyear = Carbon::create($selected_year)->startOfYear()->format('Y-m-d');
            $start_date = $firstDayoftheyear;
            $end_date = Carbon::create($selected_year)->endOfYear()->format('Y-m-d');

            if($selected_year == date('Y'))
            {
                $end_date = Carbon::now()->format('Y-m-d');
            }

            $datesIndividual = Utility::getRangeDates($firstDayoftheyear,$end_date);
            $no_of_months = Utility::getRangeMonthsNo($datesIndividual);

            $monthList = array();

            foreach ($no_of_months as $key => $no_of_month) {
                $month_key = $no_of_month['date'];
                $monthList[] = $month_key;
            }

            $allMonthlyData = PnlSummeryMonth::filteroperator($arrayOperatorsIds)
            ->Months($monthList)
            ->User(0)
            ->get()
            ->toArray();

            $reportsMonthData = $this->rearrange_operator_month($allMonthlyData);
            $monthdata = $reportsMonthData;
            $month = Carbon::now()->format('F Y');

            if(!empty($Operators))
            {
                foreach($Operators as $operator)
                {
                    $tmpOperators = array();

                    $id_operator = $operator->id_operator;
                    $tmpOperators['operator'] = $operator;

                    if(!isset($reportsMonthData[$id_operator]))
                    {
                        continue;
                    }

                    $country_id = $operator->country_id;
                    $contain_id = Arr::exists($countries, $country_id);
                    $OperatorCountry = array();

                    if($contain_id)
                    {
                        $tmpOperators['country'] = $countries[$country_id];
                        $OperatorCountry = $countries[$country_id];
                    }

                    $reportsColumnData = $this->getTargetReportsMonthWise($operator,$no_of_months,$monthdata,$OperatorCountry);

                    $tmpOperators['month_string'] = $month;

                    $total_gross_rev = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['gross_rev'],$firstDayoftheyear,$end_date);
                    $tmpOperators['gross_rev']['dates'] = $reportsColumnData['gross_rev'];
                    $tmpOperators['gross_rev']['total'] = $total_gross_rev['sum'];

                    $total_target_rev = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['target_rev'],$firstDayoftheyear,$end_date);
                    $tmpOperators['target_rev']['dates'] = $reportsColumnData['target_rev'];
                    $tmpOperators['target_rev']['total'] = $total_target_rev['sum'];

                    $total_rev_disc = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['rev_disc'],$firstDayoftheyear,$end_date);
                    $tmpOperators['rev_disc']['dates'] = $reportsColumnData['rev_disc'];

                    $gross_revenue = $tmpOperators['gross_rev']['total'];
                    $target_input_revenue = $tmpOperators['target_rev']['total'];

                    $tmpOperators['rev_disc']['total'] = ($gross_revenue != 0 && $target_input_revenue != 0) ? (($target_input_revenue - $gross_revenue)/$gross_revenue)*100 : (float)0 ;

                    $total_rev_after_share = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['rev_after_share'],$firstDayoftheyear,$end_date);
                    $tmpOperators['rev_after_share']['dates'] = $reportsColumnData['rev_after_share'];
                    $tmpOperators['rev_after_share']['total'] = $total_rev_after_share['sum'];

                    $total_target_after_share = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['target_after_share'],$firstDayoftheyear,$end_date);
                    $tmpOperators['target_after_share']['dates'] = $reportsColumnData['target_after_share'];
                    $tmpOperators['target_after_share']['total'] = $total_target_after_share['sum'];

                    $total_target_rev_disc = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['target_rev_disc'],$firstDayoftheyear,$end_date);
                    $tmpOperators['target_rev_disc']['dates'] = $reportsColumnData['target_rev_disc'];

                    $rev_after_share = $tmpOperators['rev_after_share']['total'];
                    $target_after_share = $tmpOperators['target_after_share']['total'];

                    $tmpOperators['target_rev_disc']['total'] = ($rev_after_share != 0 && $target_after_share != 0) ? (($target_after_share - $rev_after_share)/$rev_after_share)*100 : (float)0 ;

                    $total_pnl = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['pnl'],$firstDayoftheyear,$end_date);
                    $tmpOperators['pnl']['dates'] = $reportsColumnData['pnl'];
                    $tmpOperators['pnl']['total'] = $total_pnl['sum'];

                    $total_target_pnl = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['target_pnl'],$firstDayoftheyear,$end_date);
                    $tmpOperators['target_pnl']['dates'] = $reportsColumnData['target_pnl'];
                    $tmpOperators['target_pnl']['total'] = $total_target_pnl['sum'];

                    $total_pnl_disc = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['pnl_disc'],$firstDayoftheyear,$end_date);
                    $tmpOperators['pnl_disc']['dates'] = $reportsColumnData['pnl_disc'];

                    $pnl = $tmpOperators['pnl']['total'];
                    $target_pnl = $tmpOperators['target_pnl']['total'];

                    $tmpOperators['pnl_disc']['total'] = ($pnl != 0 && $target_pnl != 0) ? (($target_pnl - $pnl)/$pnl)*100 : (float)0 ;

                    $total_opex = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['opex'],$firstDayoftheyear,$end_date);
                    $tmpOperators['opex']['dates'] = $reportsColumnData['opex'];
                    $tmpOperators['opex']['total'] = $total_opex['sum'];

                    $total_target_opex = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['target_opex'],$firstDayoftheyear,$end_date);
                    $tmpOperators['target_opex']['dates'] = $reportsColumnData['target_opex'];
                    $tmpOperators['target_opex']['total'] = $total_target_opex['sum'];

                    $total_opex_disc = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['opex_disc'],$firstDayoftheyear,$end_date);
                    $tmpOperators['opex_disc']['dates'] = $reportsColumnData['opex_disc'];

                    $opex = $tmpOperators['opex']['total'];
                    $target_opex = $tmpOperators['target_opex']['total'];

                    $tmpOperators['opex_disc']['total'] = ($opex != 0 && $target_opex != 0) ? (($target_opex - $opex)/$opex)*100 : (float)0 ;

                    $total_ebida = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['ebida'],$firstDayoftheyear,$end_date);
                    $tmpOperators['ebida']['dates'] = $reportsColumnData['ebida'];
                    $tmpOperators['ebida']['total'] = $total_ebida['sum'];

                    $total_target_ebida = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['target_ebida'],$firstDayoftheyear,$end_date);
                    $tmpOperators['target_ebida']['dates'] = $reportsColumnData['target_ebida'];
                    $tmpOperators['target_ebida']['total'] = $total_target_ebida['sum'];

                    $total_ebida_disc = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['ebida_disc'],$firstDayoftheyear,$end_date);
                    $tmpOperators['ebida_disc']['dates'] = $reportsColumnData['ebida_disc'];

                    $ebida = $tmpOperators['ebida']['total'];
                    $target_ebida = $tmpOperators['target_ebida']['total'];

                    $tmpOperators['ebida_disc']['total'] = ($ebida != 0 && $target_ebida != 0) ? (($target_ebida - $ebida)/$ebida)*100 : (float)0 ;

                    $total_gros_rev_usd = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['gros_rev_usd'],$firstDayoftheyear,$end_date);

                    $tmpOperators['gros_rev_usd']['dates'] = $reportsColumnData['gros_rev_usd'];
                    $tmpOperators['gros_rev_usd']['total'] = $total_gros_rev_usd['sum'];

                    $sumemry[] = $tmpOperators;
                }
            }

            // All country's revenue reconcile sum
            $allsummaryData = UtilityFinanceReports::sumOfAllTargetRevenueData($sumemry);

            // Country Sum from Operator array
            $displayCountries = array();
            $SelectedCountries = array();

            if(!empty($sumemry))
            {
                foreach ($sumemry as $key => $sumemries) {
                    $country_id = $sumemries['country']['id'];
                    $SelectedCountries[$country_id] = $sumemries['country'];
                    $displayCountries[$country_id]['country'] = $sumemries['country'];
                    $displayCountries[$country_id]['operator'][] = $sumemries;
                }
            }

            if(!empty($displayCountries))
            {
                foreach ($displayCountries as $c_id => $country_data) {
                    $countryReconcileSum = UtilityFinanceReports::sumOfCountryTargetRevenueData($country_data['operator']);
                    $displayCountries[$c_id]['countrySum'] = $countryReconcileSum;
                }
            }

            $countryWiseData = $displayCountries;
            $no_of_days = $no_of_months;

            return view('finance.target_revenue',compact('years','no_of_days','countryWiseData','allsummaryData','data'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    // Target Revenue  Company
    public function targetRevenueCompany(Request $request)
    {
        $years = [];
        $months = [];
        $data = [];
        $countries = [];
        $currency_codes = [];
        $flags = [];
        $usd = [];
        $summary = [];
        $Country = Country::all()->toArray();
        $countries = array();
        $CountryId = $req_CountryId = $request->country;
        $CompanyId = $req_CompanyId = $request->company;
        $UserId = $req_UserId = $request->business_manager;
        $filterOperator = $req_filterOperator = $request->operatorId;

        $companys = Company::get();

        if(!empty($Country))
        {
            foreach($Country as $CountryI)
            {
                $countries[$CountryI['id']] = $CountryI;
            }
        }

        $showAllOperator = true;

        if($request->filled('company') && $req_CompanyId != "allcompany" && !$request->filled('operatorId'))
        {
            $companies = Company::Find($req_CompanyId);
            $Operators_company = array();

            if(!empty($companies))
            {
                $Operators_company = $companies->company_operators->pluck('operator_id')->toArray();
                $Operators = Operator::with('revenueshare','RevenushareByDate')
                ->Status(1)
                ->GetOperatorByOperatorId($Operators_company)
                ->get();
            }

            $showAllOperator = false;
        }

        if($request->filled('company') && $request->filled('country') && !$request->filled('operatorId'))
        {
            $data = [
                'id' => $req_CountryId,
                'company' => $req_CompanyId,
            ];

            $requestobj = new Request($data);
            $ReportControllerobj = new ReportController;
            $Operators = $ReportControllerobj->userFilterOperator($requestobj);
            $showAllOperator = false;
        }

        if (isset($req_CompanyId) && !$request->filled('country') && $request->filled('business_manager') && !$request->filled('operatorId')) {
            $Countrys[0] = Country::with('operators')->Find($CountryId);
            $data = [
                'country' => $req_CountryId,
                'company' => $req_CompanyId,
                'business_manager' => $req_UserId,
            ];

            $requestobj = new Request($data);
            $FinanceControllerobj = new FinanceController;
            $Operators = $FinanceControllerobj->userFilterBusinessManagerOperator($requestobj);
            $CountryFlag = false;
            $showAllOperator = false;
        }

        if ($request->filled('company') && $request->filled('country') && $request->filled('business_manager') && !$request->filled('operatorId')) {
            $Countrys[0] = Country::with('operators')->Find($CountryId);
            $data = [
                'country' => $req_CountryId,
                'company' => $req_CompanyId,
                'business_manager' => $req_UserId,
            ];

            $requestobj = new Request($data);
            $FinanceControllerobj = new FinanceController;
            $Operators = $FinanceControllerobj->userFilterBusinessManagerOperator($requestobj);
            $CountryFlag = false;
            $showAllOperator = false;
        }

        if($request->filled('operatorId'))
        {
            $Operators = Operator::with('revenueshare','RevenushareByDate')
            ->Status(1)
            ->GetOperatorByOperatorId($filterOperator)
            ->get();

            $showAllOperator = false;
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

        if($showAllOperator)
        {
            $Operators = Operator::with('revenueshare','RevenushareByDate')->Status(1)->get();
        }

        $arrayOperatorsIds = $Operators->pluck('id_operator')->toArray();

        $selected_year = isset($request->year) ? $request->year: date('Y');

        for($i = date('Y'); $i >= 2022; $i--){
            $years[] = $i;
        }

        $firstDayoftheyear = Carbon::create($selected_year)->startOfYear()->format('Y-m-d');
        $start_date = $firstDayoftheyear;
        $end_date = Carbon::create($selected_year)->endOfYear()->format('Y-m-d');

        if($selected_year == date('Y'))
        {
            $end_date = Carbon::now()->format('Y-m-d');
        }

        $datesIndividual = Utility::getRangeDates($firstDayoftheyear,$end_date);
        $no_of_months = Utility::getRangeMonthsNo($datesIndividual);
        $month = Carbon::now()->format('F Y');

        $monthList = array();

        foreach ($no_of_months as $key => $no_of_month) {
            $month_key = $no_of_month['date'];
            $monthList[] = $month_key;
        }

        $allMonthlyData = PnlSummeryMonth::filteroperator($operator_ids)
        ->Months($monthList)
        ->User(0)
        ->get()
        ->toArray();

        $reportsMonthData = $this->rearrange_operator_month($allMonthlyData);
        $monthdata = $reportsMonthData;
        $month = Carbon::now()->format('F Y');

        if(!empty($Operators))
        {
            foreach($Operators as $operator)
            {
                $id_operator = $operator->id_operator;
                $tmpOperators = array();
                $tmpOperators['operator'] = $operator;
                if(!isset($com_operators[$id_operator]))
                {
                    // if The Operator not founds in that array
                    continue;
                }
                // dd($com_operators);
                $tmpOperators['country'] = $com_operators[$id_operator];

                if(!isset($reportsMonthData[$id_operator]))
                {
                    continue;
                }

                // $country_id = $operator->country_id;
                // $contain_id = Arr::exists($com_operators, $id_operator);
                $OperatorCountry = array();

                // if($contain_id)
                // {
                //     $tmpOperators['country'] = $com_operators[$id_operator];
                //     $OperatorCountry = $com_operators[$id_operator];
                // }

                $reportsColumnData = $this->getTargetReportsMonthWise($operator,$no_of_months,$monthdata,$OperatorCountry);

                    $tmpOperators['month_string'] = $month;

                    $total_gross_rev = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['gross_rev'],$firstDayoftheyear,$end_date);
                    $tmpOperators['gross_rev']['dates'] = $reportsColumnData['gross_rev'];
                    $tmpOperators['gross_rev']['total'] = $total_gross_rev['sum'];

                    $total_target_rev = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['target_rev'],$firstDayoftheyear,$end_date);
                    $tmpOperators['target_rev']['dates'] = $reportsColumnData['target_rev'];
                    $tmpOperators['target_rev']['total'] = $total_target_rev['sum'];

                    $total_rev_disc = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['rev_disc'],$firstDayoftheyear,$end_date);
                    $tmpOperators['rev_disc']['dates'] = $reportsColumnData['rev_disc'];

                    $gross_revenue = $tmpOperators['gross_rev']['total'];
                    $target_input_revenue = $tmpOperators['target_rev']['total'];

                    $tmpOperators['rev_disc']['total'] = ($gross_revenue != 0 && $target_input_revenue != 0) ? (($target_input_revenue - $gross_revenue)/$gross_revenue)*100 : (float)0 ;

                    $total_rev_after_share = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['rev_after_share'],$firstDayoftheyear,$end_date);
                    $tmpOperators['rev_after_share']['dates'] = $reportsColumnData['rev_after_share'];
                    $tmpOperators['rev_after_share']['total'] = $total_rev_after_share['sum'];

                    $total_target_after_share = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['target_after_share'],$firstDayoftheyear,$end_date);
                    $tmpOperators['target_after_share']['dates'] = $reportsColumnData['target_after_share'];
                    $tmpOperators['target_after_share']['total'] = $total_target_after_share['sum'];

                    $total_target_rev_disc = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['target_rev_disc'],$firstDayoftheyear,$end_date);
                    $tmpOperators['target_rev_disc']['dates'] = $reportsColumnData['target_rev_disc'];

                    $rev_after_share = $tmpOperators['rev_after_share']['total'];
                    $target_after_share = $tmpOperators['target_after_share']['total'];

                    $tmpOperators['target_rev_disc']['total'] = ($rev_after_share != 0 && $target_after_share != 0) ? (($target_after_share - $rev_after_share)/$rev_after_share)*100 : (float)0 ;

                    $total_pnl = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['pnl'],$firstDayoftheyear,$end_date);
                    $tmpOperators['pnl']['dates'] = $reportsColumnData['pnl'];
                    $tmpOperators['pnl']['total'] = $total_pnl['sum'];

                    $total_target_pnl = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['target_pnl'],$firstDayoftheyear,$end_date);
                    $tmpOperators['target_pnl']['dates'] = $reportsColumnData['target_pnl'];
                    $tmpOperators['target_pnl']['total'] = $total_target_pnl['sum'];

                    $total_pnl_disc = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['pnl_disc'],$firstDayoftheyear,$end_date);
                    $tmpOperators['pnl_disc']['dates'] = $reportsColumnData['pnl_disc'];

                    $pnl = $tmpOperators['pnl']['total'];
                    $target_pnl = $tmpOperators['target_pnl']['total'];

                    $tmpOperators['pnl_disc']['total'] = ($pnl != 0 && $target_pnl != 0) ? (($target_pnl - $pnl)/$pnl)*100 : (float)0 ;

                    $total_opex = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['opex'],$firstDayoftheyear,$end_date);
                    $tmpOperators['opex']['dates'] = $reportsColumnData['opex'];
                    $tmpOperators['opex']['total'] = $total_opex['sum'];

                    $total_target_opex = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['target_opex'],$firstDayoftheyear,$end_date);
                    $tmpOperators['target_opex']['dates'] = $reportsColumnData['target_opex'];
                    $tmpOperators['target_opex']['total'] = $total_target_opex['sum'];

                    $total_opex_disc = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['opex_disc'],$firstDayoftheyear,$end_date);
                    $tmpOperators['opex_disc']['dates'] = $reportsColumnData['opex_disc'];

                    $opex = $tmpOperators['opex']['total'];
                    $target_opex = $tmpOperators['target_opex']['total'];

                    $tmpOperators['opex_disc']['total'] = ($opex != 0 && $target_opex != 0) ? (($target_opex - $opex)/$opex)*100 : (float)0 ;

                    $total_ebida = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['ebida'],$firstDayoftheyear,$end_date);
                    $tmpOperators['ebida']['dates'] = $reportsColumnData['ebida'];
                    $tmpOperators['ebida']['total'] = $total_ebida['sum'];

                    $total_target_ebida = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['target_ebida'],$firstDayoftheyear,$end_date);
                    $tmpOperators['target_ebida']['dates'] = $reportsColumnData['target_ebida'];
                    $tmpOperators['target_ebida']['total'] = $total_target_ebida['sum'];

                    $total_ebida_disc = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['ebida_disc'],$firstDayoftheyear,$end_date);
                    $tmpOperators['ebida_disc']['dates'] = $reportsColumnData['ebida_disc'];

                    $ebida = $tmpOperators['ebida']['total'];
                    $target_ebida = $tmpOperators['target_ebida']['total'];

                    $tmpOperators['ebida_disc']['total'] = ($ebida != 0 && $target_ebida != 0) ? (($target_ebida - $ebida)/$ebida)*100 : (float)0 ;

                    $total_gros_rev_usd = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['gros_rev_usd'],$firstDayoftheyear,$end_date);

                    $tmpOperators['gros_rev_usd']['dates'] = $reportsColumnData['gros_rev_usd'];
                    $tmpOperators['gros_rev_usd']['total'] = $total_gros_rev_usd['sum'];

                    $sumemry[] = $tmpOperators;
            }
        }

        // All country's final cost campaign sum
        $allsummaryData = UtilityFinanceReports::sumOfAllTargetRevenueData($sumemry);

        // Country Sum from Operator array
        $displayCountries = array();
        $SelectedCountries = array();

        if(!empty($sumemry))
        {
            foreach ($sumemry as $key => $sumemries) {
                $country_id = $sumemries['country']['id'];
                $SelectedCountries[$country_id] = $sumemries['country'];
                $displayCountries[$country_id]['country'] = $sumemries['country'];
                $displayCountries[$country_id]['country']['country'] = $sumemries['country']['name'];
                $displayCountries[$country_id]['operator'][] = $sumemries;
            }
        }

        if(!empty($displayCountries)) {
            foreach ($displayCountries as $c_id => $country_data) {
                $opeax = 0;
                $target_opex = 0;

                // Get the country reconcile sum
                $countryReconcileSum = UtilityFinanceReports::sumOfCountryTargetRevenueData($country_data['operator']);

                // Initialize countrySum for the current country
                $displayCountries[$c_id]['countrySum'] = $countryReconcileSum;

                // Loop through each date key in opex dates
                foreach ($countryReconcileSum['opex']['dates'] as $key => $value) {
                    $data = TargetOpex::filterCompany($c_id)->Key($key)->select('opex')->first();
                    if(isset($data)){
                        if ($key != Carbon::now()->format('Y-m')){

                            $opeax += $data->opex;
                        }
                        $displayCountries[$c_id]['countrySum']['opex']['dates'][$key]['value'] = $data->opex;
                    }

                    $data1 = TargetOpex::filterCompany($c_id)->Key($key)->select('target_opex')->first();
                    if(isset($data1)){
                        $displayCountries[$c_id]['countrySum']['target_opex']['dates'][$key]['value'] = $data1->target_opex;
                        if ($key != Carbon::now()->format('Y-m')){
                            $target_opex += $data1->target_opex;
                        }

                    }
                }

                // Set the total values for opex and target_opex
                $displayCountries[$c_id]['countrySum']['opex']['total'] = $opeax;
                $displayCountries[$c_id]['countrySum']['target_opex']['total'] = $target_opex;
            }
        }


        // dd($displayCountries);
        $countryWiseData = $displayCountries;
        $no_of_days = $no_of_months;

        return view('finance.target_revenue',compact('years','no_of_days','countryWiseData','allsummaryData','data'));
    }

    // create revenue reconcile
    public function createRevenueReconcile(Request $request)
    {
        $requestData = $request->all();
        $data = [];
        $DataArray = [];
        $revdataDetails = [];
        $dataDetails[] = [];
        $currency = [];
        $Country = Country::all()->toArray();
        $countries = array();

        if(!empty($Country))
        {
            foreach($Country as $CountryI)
            {
                $countries[$CountryI['id']] = $CountryI;
            }
        }

        $data['year'] = $year = isset($requestData['year']) ? $requestData['year'] : date('Y');
        $data['country'] = isset($requestData['country']) ? $requestData['country'] : '';
        $data['operator'] = isset($requestData['operator']) ? $requestData['operator'] : '';
        $data['service'] = isset($requestData['service']) ? $requestData['service'] : '';

        for($i = date('Y'); $i >= 2022; $i--){
            $data['years'][] = $i;
        }

        $operator = Operator::with('revenueshare')->filterOperatorID($data['operator'])->first();

        if(!empty($operator))
        {
            $tmpOperators = array();
            $tmpOperators['operator'] = $operator;
            $country_id = isset($operator->country_id) ? $operator->country_id : '';
            $tmpOperators['country'] = !empty($country_id) ? $countries[$country_id] : '';
            $dataDetails[] = $tmpOperators;
        }

        foreach($dataDetails as $detail)
        {
            if(isset($detail['operator']['operator_name'])){
                $currency[$detail['country']['country']][$detail['operator']['operator_name']] = $detail['country']['currency_code'];

                $DataArray[$detail['country']['country']][$detail['operator']['operator_name']]['country_id'] = $detail['country']['id'];

                $DataArray[$detail['country']['country']][$detail['operator']['operator_name']]['operator_id'] = $detail['operator']['id_operator'];
            }
        }

        $revdataDetails = $DataArray;

        return view('finance.create_revenue_reconcile', compact('data','revdataDetails','currency'));
    }

    public function createRevenueReconcile_old(Request $request)
    {
        $requestData = $request->all();
        $data = [];
        $DataArray = [];
        $revdataDetails = [];
        $Country = Country::all()->toArray();
        $countries = array();

        if(!empty($Country))
        {
            foreach($Country as $CountryI)
            {
                $countries[$CountryI['id']] = $CountryI;
            }
        }

        $data['selected_year'] = $year = isset($requestData['year']) ? $requestData['year'] : date('Y');
        $data['selected_month'] = $month = isset($requestData['month']) ? $requestData['month'] : date('m');
        $data['monthArray'] = [1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'];

        for($i = date('Y'); $i >= 2022; $i--){
            $data['years'][] = $i;
        }

        for($i = 1; $i <= 12; $i++){
            $data['months'][] = $i;
        }

        $Operators = Operator::with('revenueshare')->Status(1)->get();

        if(!empty($Operators))
        {
            foreach($Operators as $operator)
            {
                $tmpOperators = array();
                $tmpOperators['operator'] = $operator;
                $country_id = $operator->country_id;
                $tmpOperators['country'] = $countries[$country_id];
                $dataDetails[] = $tmpOperators;
            }
        }

        foreach($dataDetails as $detail)
        {
            $currency[$detail['country']['country']][$detail['operator']['operator_name']] = $detail['country']['currency_code'];

            $reconcile = RevenueReconciles::filterCountry($detail['country']['id'])->filterOperator($detail['operator']['id_operator'])->filterYear($year)->filterMonth($month)->first();

            $DataArray[$detail['country']['country']][$detail['operator']['operator_name']]['country_id'] = $detail['country']['id'];

            $DataArray[$detail['country']['country']][$detail['operator']['operator_name']]['operator_id'] = $detail['operator']['id_operator'];

            $DataArray[$detail['country']['country']][$detail['operator']['operator_name']]['currency'] = isset($reconcile['currency']) ? $reconcile['currency'] : '';

            $DataArray[$detail['country']['country']][$detail['operator']['operator_name']]['revenue'] = isset($reconcile['revenue']) ? $reconcile['revenue'] : '';

            $DataArray[$detail['country']['country']][$detail['operator']['operator_name']]['revenue_telco'] = isset($reconcile['revenue_telco']) ? $reconcile['revenue_telco'] : '';

            $DataArray[$detail['country']['country']][$detail['operator']['operator_name']]['net_revenue'] = isset($reconcile['net_revenue']) ? $reconcile['net_revenue'] : '';

            $DataArray[$detail['country']['country']][$detail['operator']['operator_name']]['file'] = isset($reconcile['file']) ? $reconcile['file'] : '';
        }

        $revdataDetails = $DataArray;

        return view('finance.create_revenue_reconcile', compact('data','revdataDetails','currency'));
    }

    // store revenue reconcile
    public function storeRevenueReconcile(Request $request)
    {
        $requestData = $request->all();

        $year = isset($requestData['year']) ? $requestData['year'] : '';
        $country_id = isset($requestData['country_id']) ? $requestData['country_id'] : '';
        $operator_id = isset($requestData['operator_id']) ? $requestData['operator_id'] : '';
        $id_service = isset($requestData['id_service']) ? $requestData['id_service'] : '';
        $revenues = isset($requestData['revenue']) ? $requestData['revenue'] : [];
        $revenues_telco = isset($requestData['revenue_telco']) ? $requestData['revenue_telco'] : [];
        $net_revenues = isset($requestData['net_revenue']) ? $requestData['net_revenue'] : [];
        $files = isset($requestData['file']) ? $requestData['file'] : [];

        if(isset($revenues) && !empty($revenues)){
            foreach($revenues as $rkey => $revenue){
                $rev_details = RevenueReconciles::filterCountry($country_id)
                ->filterOperator($operator_id)
                ->filterService($id_service)
                ->filterYear($year)
                ->filterMonth($rkey)
                ->first();

                $oldFile = (isset($rev_details->file)) ? $rev_details->file : '';

                $newFile = isset($files) ? $files : '';

                if(!empty($newFile)){
                    $file = 'reconcile_'.rand().time().'.'.$newFile->extension();
                    $path = 'assets/reconciles/';
                    $newFile->storeAs($path, $file,['disk' => 'public_uploads']);
                }else{
                    $file = (isset($oldFile) && !empty($oldFile)) ? $oldFile : '';
                }

                $currency = isset($currencies[$rkey]) ? $currencies[$rkey] : 'USD';

                $revenue_telco = (isset($revenues_telco[$rkey]) && ($revenues_telco[$rkey]) != '') ? $revenues_telco[$rkey] : 0;

                $net_revenue = (isset($net_revenues[$rkey]) && ($net_revenues[$rkey]) != '') ? $net_revenues[$rkey] : 0;

                if(strlen($rkey) == 1){
                    $key = $year.'-0'.$rkey;
                }else{
                    $key = $year.'-'.$rkey;
                }

                if($revenue != '' && $revenue != NULL && $revenue_telco != '' && $revenue_telco != NULL)
                {
                    $data[] = ['country_id' => $country_id, 'operator_id' => $operator_id, 'id_service' => $id_service, 'year' => $year, 'month' => $rkey, 'key' => $key, 'currency' => $currency, 'revenue' => $revenue, 'revenue_telco' => $revenue_telco, 'net_revenue' => $net_revenue, 'file' => $file ];
                }
            }

            if(!empty($data))
            {
                $response = array();
                $result = RevenueReconciles::upsert($data,['id_service','year','month'],['country_id','operator_id','key','currency','revenue','revenue_telco','net_revenue','file']);

                Utility::user_activity('Update Revenue Reconcile');

                if($result > 0)
                {
                    $response['success'] = 1;
                    $response['error'] = 0;

                }else{
                    $response['success'] = 0;
                    $response['error'] = 1;
                }

                echo json_encode($response); exit(0);
            }else{
                $response['success'] = 0;
                $response['error'] = 1;
            }

            echo json_encode($response); exit(0);
        }
    }

    public function downloadFile(Request $request)
    {
        $data = $request->all();
        $file = $data['file'];

        $path = public_path('assets/reconciles/'.$file);

        if(file_exists($path)){
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($path).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: '.filesize($path));
            flush();
            readfile($path);
        }else{
            Session::flash('error', 'Reconcile file is not exists!');
        }

        return redirect()->to('/finance/revenueReconcile');
    }

    public function storeRevenueReconcileExcel(Request $request)
    {
        $serviceWise = 1;
        $requestData = $request->all();
        $files = isset($requestData['file']) ? $requestData['file'] : [];

        if(empty($files)){
            return redirect()->back()->with('error', __('please select a file first!'));
        }

        $file = Excel::toArray(new UsersImport, $files);

        $months = ['january' => '01', 'february' => '02', 'march' => '03', 'april' => '04', 'may' => '05', 'june' => '06', 'july' => '07', 'august' => '08', 'september' => '09', 'october' => '10', 'november' => '11', 'december' => '12'];

        foreach ($file[0] as $key => $value) {
            $revenue = 0;
            $revenue_telco = 0;
            $net_revenue = 0;

            $year = $value['year'];

            if(empty($value['operator']))
            break;

            $operator = Operator::with('country')->where('operator_name', $value['operator'])->first()->toArray();

            $operator_id = $operator['id_operator'];

            $service = Service::with('operator')->where('keyword', $value['service'])->where('operator_id', $operator_id)->first()->toArray();

            $id_service = $service['id_service'];

            $country_id = $service['operator']['country_id'];

            $name = $value['name'];

            $country = Country::select('currency_code')->where('id', $country_id)->first()->toArray();

            $currency = $country['currency_code'];

            $reconcile = ['12' => $value['december'], '11' => $value['november'], '10' => $value['october'], '09' => $value['september'], '08' => $value['august'], '07' => $value['july'], '06' => $value['june'], '05' => $value['may'], '04' => $value['april'], '03' => $value['march'], '02' => $value['february'], '01' => $value['january'],];

            foreach ($reconcile as $key1 => $value1) {
                $data = [];
                if (isset($value1) && $value1 != 0) {
                    $month = $key1;

                    $keys = $year.'-'.$month;

                    $RevenueReconciles = RevenueReconciles::filterCountry($country_id)
                    ->filterOperator($operator_id)
                    ->filterService($id_service)
                    ->filterYear($year)
                    ->filterMonth($month)
                    ->first();

                    if($RevenueReconciles){
                        $RevenueReconciles = $RevenueReconciles->toArray();
                    }

                    if($name == 'revenue'){
                        $revenue = $value1;

                        if(isset($RevenueReconciles)){
                            $revenue_telco = $RevenueReconciles['revenue_telco'];
                            $net_revenue = $RevenueReconciles['net_revenue'];
                        }
                    }elseif ($name == 'revenue after telco'){
                        $revenue_telco = $value1;

                        if(isset($RevenueReconciles)){
                            $revenue = $RevenueReconciles['revenue'];
                            $net_revenue = $RevenueReconciles['net_revenue'];
                        }
                    }elseif ($name == 'net revenue') {
                        $net_revenue = $value1;

                        if(isset($RevenueReconciles)){
                            $revenue = $RevenueReconciles['revenue'];
                            $revenue_telco = $RevenueReconciles['revenue_telco'];
                        }
                    }

                    $data = ['country_id' => $country_id, 'operator_id' => $operator_id, 'id_service' => $id_service, 'year' => $year, 'month' => $month, 'key' => $keys, 'currency' => $currency, 'revenue' => $revenue, 'revenue_telco' => $revenue_telco, 'net_revenue' => $net_revenue];

                    RevenueReconciles::upsert($data,['id_service','year','month'],['country_id','operator_id','key','currency','revenue','revenue_telco','net_revenue']);

                    Utility::user_activity('Update Revenue Reconcile Excel');
                }
            }
        }

        return view('finance.reconcile_popup', compact('file','serviceWise'))->with('success', __('Revenue Reconcile successfully added!'));
    }

    public function popup()
    {
        return view('finance.popup');
    }

    // get report date wise
    function getReportsMonthWise($operator, $no_of_days, $reportsByIDs, $monthreconciledata, $OperatorCountry)
    {
        $usdValue = $OperatorCountry['usd'];
        $shareDb = array();
        $merchent_share = 1;
        $operator_share = 1;
        $revenue_share = $operator->revenueshare;
        $revenushare_by_dates = $operator->RevenushareByDate;
        $usdValue = isset($OperatorCountry['usd']) ? $OperatorCountry['usd'] : 1;

        if(isset($revenue_share))
        {
            $merchent_share = $revenue_share->merchant_revenue_share;
            $operator_share = $revenue_share->operator_revenue_share;
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
            $allColumnData = array();
            $dlr = array();
            $fir = array();
            $fir_usd = array();
            $discrepency = array();
            $dlr_after_telco = array();
            $fir_after_telco = array();
            $fir_after_telco_usd = array();
            $discrepency_after_telco = array();
            $net_revenue = array();
            $net_revenue_after_telco = array();
            $net_revenue_after_telco_usd = array();
            $discrepency_net_revenue = array();
            $gros_rev_usd_arr = array();
            $gros_rev_usd_after_arr = array();
            $net_rev_usd_arr = array();
            $reconcile_file = array();

            $id_operator = $operator->id_operator;

            foreach($no_of_days as $days)
            {
                $shareDb['merchent_share'] = $merchent_share;
                $shareDb['operator_share'] = $operator_share;

                $month = $days['month'];
                $year = $days['year'];

                $keys = $id_operator.".".$days['date'];

                $summariserow = Arr::get($reportsByIDs, $keys, 0);

                $reconcilesummariserow = Arr::get($monthreconciledata, $keys, 0);

                $key_date = new Carbon($days['date']);
                $key = $key_date->format("Y-m");

                if(isset($revenushare_by_dates[$key]))
                {
                    $merchent_share_by_dates = $revenushare_by_dates[$key]->merchant_revenue_share;
                    $operator_share_by_dates = $revenushare_by_dates[$key]->operator_revenue_share;

                    $shareDb['merchent_share'] = $merchent_share_by_dates;
                    $shareDb['operator_share'] = $operator_share_by_dates;
                }

                $gros_rev = isset($summariserow['gros_rev']) ? $summariserow['gros_rev'] : 0;
                $gros_rev_usd =  $usdValue * $gros_rev;
                $dashboard_linkit_revenue = $gros_rev;
                $finance_input_revenue = isset($reconcilesummariserow['revenue']) ? $reconcilesummariserow['revenue'] : 0;
                $finance_input_revenue_usd = $finance_input_revenue * $usdValue;
                $disc = ($dashboard_linkit_revenue != 0 && $finance_input_revenue != 0) ? (($dashboard_linkit_revenue - $finance_input_revenue)/$dashboard_linkit_revenue)*100 : (float)0 ;

                if($disc < 10){
                    $disc_class = "text-success";
                }else if($disc > 10 && $disc < 50){
                    $disc_class = "text-warning";
                }else if($disc > 50){
                    $disc_class = "text-danger";
                }else{
                    $disc_class = "text-success";
                }

                $dlrAfterTelco = UtilityReports::trat($shareDb,$gros_rev);
                $gros_rev_usd_after_telco = UtilityReports::turt($shareDb,$gros_rev_usd);

                $firAfterTelco = isset($reconcilesummariserow['revenue_telco']) ? $reconcilesummariserow['revenue_telco'] : 0;
                $firAfterTelco_usd = $firAfterTelco * $usdValue;
                $discrepencyAfterTelco = ($dlrAfterTelco != 0 && $firAfterTelco != 0) ? (($firAfterTelco - $dlrAfterTelco)/$dlrAfterTelco)*100 : (float)0;

                if($discrepencyAfterTelco < 10){
                    $discrepencyAfterTelco_class = "text-success";
                }else if($discrepencyAfterTelco > 10 && $discrepencyAfterTelco < 50){
                    $discrepencyAfterTelco_class = "text-warning";
                }else if($discrepencyAfterTelco > 50){
                    $discrepencyAfterTelco_class = "text-danger";
                }else{
                    $discrepencyAfterTelco_class = "text-success";
                }

                $vat = !empty($operator->vat) ? $dlrAfterTelco * ($operator->vat/100) : 0;
                $wht = !empty($operator->wht) ? $dlrAfterTelco * ($operator->wht/100) : 0;
                $misc_tax = !empty($operator->miscTax) ? $dlrAfterTelco * ($operator->miscTax/100) : 0;
                $other_tax = $vat + $wht + $misc_tax;

                if($other_tax != 0){
                    $net_after_tax = $dlrAfterTelco - $other_tax;
                }else{
                    $net_after_tax = $dlrAfterTelco;
                }

                $net_after_tax_usd = $net_after_tax * $usdValue;
                $finance_input_net_after_tax = isset($reconcilesummariserow['net_revenue']) ? $reconcilesummariserow['net_revenue'] : 0;
                $finance_input_net_after_tax_usd = $finance_input_net_after_tax * $usdValue;
                $discrepencyNetRevenue = ($net_after_tax != 0 && $finance_input_net_after_tax != 0) ? (($finance_input_net_after_tax - $net_after_tax)/$net_after_tax)*100 : (float)0 ;

                if($discrepencyNetRevenue < 10){
                    $discrepencyNetRevenue_class = "text-success";
                }else if($discrepencyNetRevenue > 10 && $discrepencyNetRevenue < 50){
                    $discrepencyNetRevenue_class = "text-warning";
                }else if($discrepencyNetRevenue > 50){
                    $discrepencyNetRevenue_class = "text-danger";
                }else{
                    $discrepencyNetRevenue_class = "text-success";
                }

                $file = isset($reconcilesummariserow['file']) ? $reconcilesummariserow['file'] : '';

                $dlr[$days['date']]['value'] = $dashboard_linkit_revenue;
                $dlr[$days['date']]['class'] = "";

                $gros_rev_usd_arr[$days['date']]['value'] = $gros_rev_usd;
                $gros_rev_usd_arr[$days['date']]['class'] = "";

                $gros_rev_usd_after_arr[$days['date']]['value'] = $gros_rev_usd_after_telco;
                $gros_rev_usd_after_arr[$days['date']]['class'] = "";

                $fir[$days['date']]['value'] = $finance_input_revenue;
                $fir[$days['date']]['class'] = "";

                $fir_usd[$days['date']]['value'] = $finance_input_revenue_usd;
                $fir_usd[$days['date']]['class'] = "";

                $discrepency[$days['date']]['value'] = $disc;
                $discrepency[$days['date']]['class'] = $disc_class;

                $dlr_after_telco[$days['date']]['value'] = $dlrAfterTelco;
                $dlr_after_telco[$days['date']]['class'] = "";

                $fir_after_telco[$days['date']]['value'] = $firAfterTelco;
                $fir_after_telco[$days['date']]['class'] = "";

                $fir_after_telco_usd[$days['date']]['value'] = $firAfterTelco_usd;
                $fir_after_telco_usd[$days['date']]['class'] = "";

                $discrepency_after_telco[$days['date']]['value'] = $discrepencyAfterTelco;
                $discrepency_after_telco[$days['date']]['class'] = $discrepencyAfterTelco_class;

                $net_revenue[$days['date']]['value'] = $net_after_tax;
                $net_revenue[$days['date']]['class'] = "";

                $net_rev_usd_arr[$days['date']]['value'] = $net_after_tax_usd;
                $net_rev_usd_arr[$days['date']]['class'] = "";

                $net_revenue_after_telco[$days['date']]['value'] = $finance_input_net_after_tax;
                $net_revenue_after_telco[$days['date']]['class'] = "";

                $net_revenue_after_telco_usd[$days['date']]['value'] = $finance_input_net_after_tax_usd;
                $net_revenue_after_telco_usd[$days['date']]['class'] = "";

                $discrepency_net_revenue[$days['date']]['value'] = $discrepencyNetRevenue;
                $discrepency_net_revenue[$days['date']]['class'] = $discrepencyNetRevenue_class;

                $reconcile_file[$days['date']]['value'] = $file;
                $reconcile_file[$days['date']]['class'] = "";
            }

            $allColumnData['gros_rev_usd'] = $gros_rev_usd_arr;
            $allColumnData['gros_rev_usd_after_telco'] = $gros_rev_usd_after_arr;
            $allColumnData['net_revenue_usd'] = $net_rev_usd_arr;
            $allColumnData['dlr'] = $dlr;
            $allColumnData['fir'] = $fir;
            $allColumnData['fir_usd'] = $fir_usd;
            $allColumnData['discrepency'] = $discrepency;
            $allColumnData['dlr_after_telco'] = $dlr_after_telco;
            $allColumnData['fir_after_telco'] = $fir_after_telco;
            $allColumnData['fir_after_telco_usd'] = $fir_after_telco_usd;
            $allColumnData['discrepency_after_telco'] = $discrepency_after_telco;
            $allColumnData['net_revenue'] = $net_revenue;
            $allColumnData['fir_net_revenue'] = $net_revenue_after_telco;
            $allColumnData['fir_net_revenue_usd'] = $net_revenue_after_telco_usd;
            $allColumnData['discrepency_net_revenue'] = $discrepency_net_revenue;
            $allColumnData['file'] = $reconcile_file;

            return $allColumnData;
        }
    }

    // get target revenue report date wise
    function getTargetReportsMonthWise($operator,$no_of_days, $reportsByIDs, $OperatorCountry)
    {
        // $usdValue = $OperatorCountry['usd'];
        $shareDb = array();
        $merchent_share = 1;
        $operator_share = 1;
        $revenue_share = $operator->revenueshare;
        $usdValue = isset($OperatorCountry['usd']) ? $OperatorCountry['usd'] : 1;

        if(isset($revenue_share))
        {
            $merchent_share = $revenue_share->merchant_revenue_share;
            $operator_share = $revenue_share->operator_revenue_share;
        }

        $shareDb['merchent_share'] = $merchent_share;
        $shareDb['operator_share'] = $operator_share;

        if(!empty($no_of_days))
        {
            $allColumnData = array();
            $gross_rev = array();
            $gros_rev_usd_arr = array();
            $target_rev = array();
            $rev_disc = array();
            $rev_after_share = array();
            $target_after_share = array();
            $target_rev_disc = array();
            $pnl = array();
            $target_pnl = array();
            $pnl_disc = array();
            $opex = array();
            $target_opex = array();
            $opex_disc = array();
            $ebida = array();
            $target_ebida = array();
            $ebida_disc = array();

            $id_operator = $operator->id_operator;

            foreach($no_of_days as $days)
            {
                $month = $days['month'];
                $year = $days['year'];

                $keys = $id_operator.".".$days['date'];

                $key_date = new Carbon($days['date']);
                $key = $key_date->format("Y-m");

                $summariserow = Arr::get($reportsByIDs, $keys, 0);

                $target_revenue_value = TargetRevenueReconciles::filterOperator($id_operator)->filterMonth($month)->filterYear($year)->SumTarget()->first();

                $gross_revenue = isset($summariserow['rev']) ? $summariserow['rev'] : 0;

                $gros_rev_usd =  $usdValue * $gross_revenue;

                $target_input_revenue = isset($target_revenue_value->revenue) ? $target_revenue_value->revenue : 0;

                // revenue discrepency
                $revenue_discrepency = ($gross_revenue != 0 && $target_input_revenue != 0) ? (($target_input_revenue - $gross_revenue)/$gross_revenue)*100 : (float)0 ;

                // revenue after share
                $revenue_after_share = (float)0; // should be change

                // target revenue after share
                $target_revenue_after_share = isset($target_revenue_value->revenue_after_share) ? $target_revenue_value->revenue_after_share : 0;

                // target revenue discrepency
                $target_revenue_discrepency = ($gross_revenue != 0 && $target_revenue_after_share != 0) ? (($target_revenue_after_share - $gross_revenue)/$gross_revenue)*100 : (float)0 ;

                // pnl
                $pnl_data = isset($summariserow['pnl']) ? $summariserow['pnl'] : 0;

                // target pnl
                $target_pnl_data = isset($target_revenue_value->pnl) ? $target_revenue_value->pnl : 0;

                // target pnl discrepency
                $target_pnl_discrepency = ($pnl_data != 0 && $target_pnl_data != 0) ? (($target_pnl_data - $pnl_data)/$pnl_data)*100 : (float)0 ;

                // opex
                $opex_data = isset($summariserow['opex']) ? $summariserow['opex'] : 0;

                // target opex
                $target_opex_data = isset($target_revenue_value->opex) ? $target_revenue_value->opex : 0;

                // opex discrepency
                $opex_discrepency = ($opex_data != 0 && $target_opex_data != 0) ? (($target_opex_data - $opex_data)/$opex_data)*100 : (float)0 ;

                // ebida
                $ebida_data = $pnl_data - $opex_data;

                // target ebida
                // $target_ebida_data = isset($target_revenue_value->ebida) ? $target_revenue_value->ebida : 0;
                $target_ebida_data = $target_pnl_data - $target_opex_data;

                // ebida discrepency
                $ebida_discrepency = ($ebida_data != 0 && $target_ebida_data != 0) ? (($target_ebida_data - $ebida_data)/$pnl_data)*100 : (float)0 ;


                $gross_rev[$days['date']]['value'] = $gross_revenue;
                $gross_rev[$days['date']]['class'] = "";

                $gros_rev_usd_arr[$days['date']]['value'] = $gros_rev_usd;
                $gros_rev_usd_arr[$days['date']]['class'] = "";

                $target_rev[$days['date']]['value'] = $target_input_revenue;
                $target_rev[$days['date']]['class'] = "";

                $rev_disc[$days['date']]['value'] = $revenue_discrepency;
                $rev_disc[$days['date']]['class'] = "";

                $rev_after_share[$days['date']]['value'] = $revenue_after_share;
                $rev_after_share[$days['date']]['class'] = "";

                $target_after_share[$days['date']]['value'] = $target_revenue_after_share;
                $target_after_share[$days['date']]['class'] = "";

                $target_rev_disc[$days['date']]['value'] = $target_revenue_discrepency;
                $target_rev_disc[$days['date']]['class'] = "";

                $pnl[$days['date']]['value'] = $pnl_data;
                $pnl[$days['date']]['class'] = "";

                $target_pnl[$days['date']]['value'] = $target_pnl_data;
                $target_pnl[$days['date']]['class'] = "";

                $pnl_disc[$days['date']]['value'] = $target_pnl_discrepency;
                $pnl_disc[$days['date']]['class'] = "";

                $opex[$days['date']]['value'] = $opex_data;
                $opex[$days['date']]['class'] = "";

                $target_opex[$days['date']]['value'] = $target_opex_data;
                $target_opex[$days['date']]['class'] = "";

                $opex_disc[$days['date']]['value'] = $opex_discrepency;
                $opex_disc[$days['date']]['class'] = "";

                $ebida[$days['date']]['value'] = $ebida_data;
                $ebida[$days['date']]['class'] = "";

                $target_ebida[$days['date']]['value'] = $target_ebida_data;
                $target_ebida[$days['date']]['class'] = "";

                $ebida_disc[$days['date']]['value'] = $ebida_discrepency;
                $ebida_disc[$days['date']]['class'] = "";
            }

            $allColumnData['gross_rev'] = $gross_rev;
            $allColumnData['gros_rev_usd'] = $gros_rev_usd_arr;
            $allColumnData['target_rev'] = $target_rev;
            $allColumnData['rev_disc'] = $rev_disc;
            $allColumnData['rev_after_share'] = $rev_after_share;
            $allColumnData['target_after_share'] = $target_after_share;
            $allColumnData['target_rev_disc'] = $target_rev_disc;
            $allColumnData['pnl'] = $pnl;
            $allColumnData['target_pnl'] = $target_pnl;
            $allColumnData['pnl_disc'] = $pnl_disc;
            $allColumnData['opex'] = $opex;
            $allColumnData['target_opex'] = $target_opex;
            $allColumnData['opex_disc'] = $opex_disc;
            $allColumnData['ebida'] = $ebida;
            $allColumnData['target_ebida'] = $target_ebida;
            $allColumnData['ebida_disc'] = $ebida_disc;

            return $allColumnData;
        }
    }

    // get final revenue report date wise
    function getFinalReportsMonthWise($operator,$no_of_days, $reportsByIDs, $OperatorCountry)
    {
        // $usdValue = $OperatorCountry['usd'];
        $shareDb = array();
        $allColumnData = array();
        $merchent_share = 1;
        $operator_share = 1;
        $revenue_share = $operator->revenueshare;

        if(isset($revenue_share))
        {
            $merchent_share = $revenue_share->merchant_revenue_share;
            $operator_share = $revenue_share->operator_revenue_share;
        }

        $shareDb['merchent_share'] = $merchent_share;
        $shareDb['operator_share'] = $operator_share;

        if(!empty($no_of_days))
        {
            $cost_camp = array();
            $input_cost_campaign = array();
            $cost_campaign_disc = array();
            $app_content = array();
            $input_app_content = array();
            $app_content_disc = array();
            $cost_rnd = array();
            $input_cost_rnd = array();
            $cost_rnd_disc = array();
            $fun_busket = array();
            $input_fun_busket = array();
            $fun_busket_disc = array();
            $cost_bd = array();
            $input_cost_bd = array();
            $cost_bd_disc = array();

            $id_operator = $operator->id_operator;

            foreach($no_of_days as $days)
            {
                $month = $days['month'];
                $year = $days['year'];

                $keys = $id_operator.".".$days['date'];

                $summariserow = Arr::get($reportsByIDs, $keys, 0);

                $key = $year.'-'.$month;

                $final_cost_report = FinalCostReports::filterOperator($id_operator)->filterMonth($key)->SumCost()->first();

                $cost_campaign = isset($summariserow['cost_campaign']) ? $summariserow['cost_campaign'] : 0;
                $final_input_cost_campaign = isset($final_cost_report->final_cost_campaign) ? $final_cost_report->final_cost_campaign : 0;
                $cost_campaign_discrepency = ($cost_campaign != 0 && $final_input_cost_campaign != 0) ? (($final_input_cost_campaign - $cost_campaign)/$cost_campaign)*100 : (float)0 ;

                $gross_rev = isset($summariserow['lshare']) ? $summariserow['lshare'] : 0;

                $vat = !empty($operator->vat) ? isset($summariserow['lshare']) ? $summariserow['lshare'] * ($operator->vat/100) : 0 : 0;
                $wht = !empty($operator->wht) ? isset($summariserow['lshare']) ? $summariserow['lshare'] * ($operator->wht/100) : 0 : 0;
                $misc_tax = !empty($operator->miscTax) ? isset($summariserow['lshare']) ? $summariserow['lshare'] * ($operator->miscTax/100) : 0 : 0;

                $other_tax = $vat + $wht + $misc_tax;

                if($other_tax != 0){
                    $net_rev = $gross_rev - $other_tax;
                }else{
                    $net_rev = $gross_rev;
                }

                // $content = isset($summariserow['content']) ? $summariserow['content'] : 0;
                $content = !empty($operator->content) ? isset($net_rev) ? $net_rev * ($operator->content/100) : 0 : 0;
                $input_content = isset($final_cost_report->content) ? $final_cost_report->content : 0;
                $content_discrepency = ($content != 0 && $input_content != 0) ? (($input_content - $content)/$content)*100 : (float)0 ;

                // $rnd = isset($summariserow['rnd']) ? $summariserow['rnd'] : 0;
                $rnd = !empty($operator->rnd) ? isset($net_rev) ? $net_rev * ($operator->rnd/100) : 0 : 0;
                $input_rnd = isset($final_cost_report->rnd) ? $final_cost_report->rnd : 0;
                $rnd_discrepency = ($rnd != 0 && $input_rnd != 0) ? (($input_rnd - $rnd)/$rnd)*100 : (float)0 ;

                // $fun_basket = isset($summariserow['fun_basket']) ? $summariserow['fun_basket'] : 0;
                $fun_basket = !empty($operator->fun_basket) ? isset($net_rev) ? $net_rev * ($operator->fun_basket/100) : 0 : 0;
                $input_fun_basket = isset($final_cost_report->fun_basket) ? $final_cost_report->fun_basket : 0;
                $fun_basket_discrepency = ($fun_basket != 0 && $input_fun_basket != 0) ? (($input_fun_basket - $fun_basket)/$fun_basket)*100 : (float)0 ;

                // $bd = isset($summariserow['bd']) ? $summariserow['bd'] : 0;
                $bd = !empty($operator->bd) ? isset($net_rev) ? $net_rev * ($operator->bd/100) : 0 : 0;
                $input_bd = isset($final_cost_report->bd) ? $final_cost_report->bd : 0;
                $bd_discrepency = ($bd != 0 && $input_bd != 0) ? (($input_bd - $bd)/$bd)*100 : (float)0 ;

                $cost_camp[$days['date']]['value'] = $cost_campaign;
                $cost_camp[$days['date']]['class'] = "";
                $input_cost_campaign[$days['date']]['value'] = $final_input_cost_campaign;
                $input_cost_campaign[$days['date']]['class'] = "";
                $cost_campaign_disc[$days['date']]['value'] = $cost_campaign_discrepency;
                $cost_campaign_disc[$days['date']]['class'] = "";

                $app_content[$days['date']]['value'] = $content;
                $app_content[$days['date']]['class'] = "";
                $input_app_content[$days['date']]['value'] = $input_content;
                $input_app_content[$days['date']]['class'] = "";
                $app_content_disc[$days['date']]['value'] = $content_discrepency;
                $app_content_disc[$days['date']]['class'] = "";

                $cost_rnd[$days['date']]['value'] = $rnd;
                $cost_rnd[$days['date']]['class'] = "";
                $input_cost_rnd[$days['date']]['value'] = $input_rnd;
                $input_cost_rnd[$days['date']]['class'] = "";
                $cost_rnd_disc[$days['date']]['value'] = $rnd_discrepency;
                $cost_rnd_disc[$days['date']]['class'] = "";

                $fun_busket[$days['date']]['value'] = $fun_basket;
                $fun_busket[$days['date']]['class'] = "";
                $input_fun_busket[$days['date']]['value'] = $input_fun_basket;
                $input_fun_busket[$days['date']]['class'] = "";
                $fun_busket_disc[$days['date']]['value'] = $fun_basket_discrepency;
                $fun_busket_disc[$days['date']]['class'] = "";

                $cost_bd[$days['date']]['value'] = $bd;
                $cost_bd[$days['date']]['class'] = "";
                $input_cost_bd[$days['date']]['value'] = $input_bd;
                $input_cost_bd[$days['date']]['class'] = "";
                $cost_bd_disc[$days['date']]['value'] = $bd_discrepency;
                $cost_bd_disc[$days['date']]['class'] = "";
            }

            $allColumnData['cost_campaign'] = $cost_camp;
            $allColumnData['final_input_cost_campaign'] = $input_cost_campaign;
            $allColumnData['cost_campaign_discrepency'] = $cost_campaign_disc;
            $allColumnData['app_content'] = $app_content;
            $allColumnData['input_app_content'] = $input_app_content;
            $allColumnData['app_content_discrepency'] = $app_content_disc;
            $allColumnData['cost_rnd'] = $cost_rnd;
            $allColumnData['input_cost_rnd'] = $input_cost_rnd;
            $allColumnData['cost_rnd_discrepency'] = $cost_rnd_disc;
            $allColumnData['fun_busket'] = $fun_busket;
            $allColumnData['input_fun_busket'] = $input_fun_busket;
            $allColumnData['fun_busket_discrepency'] = $fun_busket_disc;
            $allColumnData['cost_bd'] = $cost_bd;
            $allColumnData['input_cost_bd'] = $input_cost_bd;
            $allColumnData['cost_bd_discrepency'] = $cost_bd_disc;

            return $allColumnData;
        }
    }

    // create target revenue
    public function createTargetRevenueReconcile(Request $request)
    {
        $requestData = $request->all();
        $data = [];
        $DataArray = [];
        $revdataDetails = [];
        $currency = [];
        $dataDetails[] = [];
        $Country = Country::all()->toArray();
        $countries = array();

        if(!empty($Country))
        {
            foreach($Country as $CountryI)
            {
                $countries[$CountryI['id']] = $CountryI;
            }
        }

        $data['year'] = $year = isset($requestData['year']) ? $requestData['year'] : date('Y');
        $data['country'] = isset($requestData['country']) ? $requestData['country'] : '';
        $data['operator'] = isset($requestData['operator']) ? $requestData['operator'] : '';
        $data['service'] = isset($requestData['service']) ? $requestData['service'] : '';

        for($i = date('Y'); $i >= 2022; $i--){
            $data['years'][] = $i;
        }

        $operator = Operator::with('revenueshare')->filterOperatorID($data['operator'])->first();

        if(!empty($operator))
        {
            $tmpOperators = array();
            $tmpOperators['operator'] = $operator;
            $country_id = isset($operator->country_id) ? $operator->country_id : '';
            $tmpOperators['country'] = !empty($country_id) ? $countries[$country_id] : '';
            $dataDetails[] = $tmpOperators;
        }

        foreach($dataDetails as $detail)
        {
            if(isset($detail['operator']['operator_name'])){
                $currency[$detail['country']['country']][$detail['operator']['operator_name']] = $detail['country']['currency_code'];

                $DataArray[$detail['country']['country']][$detail['operator']['operator_name']]['country_id'] = $detail['country']['id'];

                $DataArray[$detail['country']['country']][$detail['operator']['operator_name']]['operator_id'] = $detail['operator']['id_operator'];
            }
        }

        $revdataDetails = $DataArray;

        return view('finance.create_target_revenue', compact('data','revdataDetails','currency'));
    }

    // store target revenue
    public function storeTargetRevenueReconcile(Request $request)
    {
        $requestData = $request->all();

        $year = isset($requestData['year']) ? $requestData['year'] : '';
        $country_id = isset($requestData['country_id']) ? $requestData['country_id'] : '';
        $operator_id = isset($requestData['operator_id']) ? $requestData['operator_id'] : '';
        $id_service = isset($requestData['id_service']) ? $requestData['id_service'] : '';
        $revenues = isset($requestData['revenue']) ? $requestData['revenue'] : [];
        $revenue_after_shares = isset($requestData['revenue_after_share']) ? $requestData['revenue_after_share'] : [];
        $pnls = isset($requestData['pnl']) ? $requestData['pnl'] : [];
        $opexs = isset($requestData['opex']) ? $requestData['opex'] : [];
        $ebidas = isset($requestData['ebida']) ? $requestData['ebida'] : [];

        if(isset($revenues) && !empty($revenues)){
            foreach($revenues as $rkey => $revenue){
                $revenue_after_share = (isset($revenue_after_shares[$rkey]) && ($revenue_after_shares[$rkey]) != '') ? $revenue_after_shares[$rkey] : '';

                $pnl = (isset($pnls[$rkey]) && ($pnls[$rkey]) != '') ? $pnls[$rkey] : 0;

                $opex = (isset($opexs[$rkey]) && ($opexs[$rkey]) != '') ? $opexs[$rkey] : 0;

                $ebida = (isset($ebidas[$rkey]) && ($ebidas[$rkey]) != '') ? $ebidas[$rkey] : 0;

                if(strlen($rkey) == 1){
                    $key = $year.'-0'.$rkey;
                }else{
                    $key = $year.'-'.$rkey;
                }

                if($revenue != '' && $revenue != NULL && $revenue_after_share != '' && $revenue_after_share != NULL && $pnl != '' && $pnl != NULL)
                {
                    $data[] = ['country_id'=> $country_id, 'operator_id'=> $operator_id, 'id_service' => $id_service, 'year'=> $year, 'month'=> $rkey, 'key' => $key ,'revenue'=> $revenue, 'revenue_after_share'=> $revenue_after_share, 'pnl' => $pnl, 'opex' => $opex, 'ebida' => $ebida ];
                }
            }

            if(!empty($data))
            {
                $response = array();
                $result = TargetRevenueReconciles::upsert($data,['country_id','operator_id','id_service','year','month'],['key','revenue','revenue_after_share','pnl','opex','ebida']);

                Utility::user_activity('Update Target Revenue');

                if($result > 0)
                {
                    $response['success'] = 1;
                    $response['error'] = 0;

                }else{
                    $response['success'] = 0;
                    $response['error'] = 1;
                }
            }else{
                $response['success'] = 0;
                $response['error'] = 1;
            }

            echo json_encode($response); exit(0);
        }
    }

    public function storeTargetRevenueExcel(Request $request)
    {
        $serviceWise = 1;
        $requestData = $request->all();
        $files = isset($requestData['file']) ? $requestData['file'] : [];

        if(empty($files)){
            return redirect()->back()->with('error', __('please select a file first!'));
        }

        $file = Excel::toArray(new UsersImport, $files);

        $months = ['january' => '01', 'february' => '02', 'march' => '03', 'april' => '04', 'may' => '05', 'june' => '06', 'july' => '07', 'august' => '08', 'september' => '09', 'october' => '10', 'november' => '11', 'december' => '12'];

        foreach ($file[0] as $key => $value) {
            $year = $value['year'];

            if(empty($value['operator']))
            break;

            $operator = Operator::with('country')->where('operator_name', $value['operator'])->first()->toArray();

            $operator_id = $operator['id_operator'];

            $service = Service::with('operator')->where('keyword', $value['service'])->where('operator_id', $operator_id)->first()->toArray();

            $id_service = $service['id_service'];

            $country_id = $service['operator']['country_id'];

            $name = $value['name'];

            $country = Country::select('currency_code')->where('id', $country_id)->first()->toArray();

            $currency = $country['currency_code'];

            $reconcile = ['12' => $value['december'], '11' => $value['november'], '10' => $value['october'], '09' => $value['september'], '08' => $value['august'], '07' => $value['july'], '06' => $value['june'], '05' => $value['may'], '04' => $value['april'], '03' => $value['march'], '02' => $value['february'], '01' => $value['january'],];

            foreach ($reconcile as $key1 => $value1) {
                $data = [];
                $revenue = 0;
                $revenue_after_share = 0;
                $pnl = 0;
                $opex = 0;
                $ebida = 0;

                if (isset($value1) && $value1 != 0) {
                    $month = $key1;

                    $keys = $year.'-'.$month;

                    $TargetRevenueReconciles = TargetRevenueReconciles::filterOperator($operator_id)
                    ->filterService($id_service)
                    ->filterYear($year)
                    ->Key($keys)
                    ->first();

                    if($TargetRevenueReconciles){
                        $TargetRevenueReconciles = $TargetRevenueReconciles->toArray();
                    }

                    if($name == 'revenue'){
                        $revenue = $value1;

                        if(isset($TargetRevenueReconciles)){
                            $revenue_after_share = $TargetRevenueReconciles['revenue_after_share'];
                            $pnl = $TargetRevenueReconciles['pnl'];
                            $opex = $TargetRevenueReconciles['opex'];
                            $ebida = $TargetRevenueReconciles['ebida'];
                        }
                    }elseif ($name == 'revenue after share'){
                        $revenue_after_share = $value1;

                        if(isset($TargetRevenueReconciles)){
                            $revenue = $TargetRevenueReconciles['revenue'];
                            $pnl = $TargetRevenueReconciles['pnl'];
                            $opex = $TargetRevenueReconciles['opex'];
                            $ebida = $TargetRevenueReconciles['ebida'];
                        }
                    }elseif ($name == 'pnl') {
                        $pnl = $value1;

                        if(isset($TargetRevenueReconciles)){
                            $revenue = $TargetRevenueReconciles['revenue'];
                            $revenue_after_share = $TargetRevenueReconciles['revenue_after_share'];
                            $opex = $TargetRevenueReconciles['opex'];
                            $ebida = $TargetRevenueReconciles['ebida'];
                        }
                    }elseif ($name == 'opex') {
                        $opex = $value1;

                        if(isset($TargetRevenueReconciles)){
                            $revenue = $TargetRevenueReconciles['revenue'];
                            $revenue_after_share = $TargetRevenueReconciles['revenue_after_share'];
                            $pnl = $TargetRevenueReconciles['pnl'];
                            $ebida = $TargetRevenueReconciles['ebida'];
                        }
                    }elseif ($name == 'ebida') {
                        $ebida = $value1;

                        if(isset($TargetRevenueReconciles)){
                            $revenue = $TargetRevenueReconciles['revenue'];
                            $revenue_after_share = $TargetRevenueReconciles['revenue_after_share'];
                            $pnl = $TargetRevenueReconciles['pnl'];
                            $opex = $TargetRevenueReconciles['opex'];
                        }
                    }

                    $data = ['country_id'=> $country_id, 'operator_id'=> $operator_id, 'id_service' => $id_service, 'year'=> $year, 'month'=> $month, 'key' => $keys ,'revenue'=> $revenue, 'revenue_after_share'=> $revenue_after_share, 'pnl' => $pnl, 'opex' => $opex, 'ebida' => $ebida ];

                    TargetRevenueReconciles::upsert($data,['country_id','operator_id','id_service','year','month'],['key','revenue','revenue_after_share','pnl','opex','ebida']);

                    Utility::user_activity('Update Target Revenue Excel');
                }
            }
        }

        return view('finance.reconcile_popup', compact('file','serviceWise'))->with('success', __('Target Revenue successfully added!'));
    }

    public function createTargetOpex(Request $request)
    {
        $requestData = $request->all();
        $data = [];
        $DataArray = [];
        $revdataDetails = [];
        $dataDetails[] = [];
        $Country = Country::all()->toArray();
        $countries = array();

        if(!empty($Country))
        {
            foreach($Country as $CountryI)
            {
                $countries[$CountryI['id']] = $CountryI;
            }
        }

        $data['year'] = $year = isset($requestData['year']) ? $requestData['year'] : date('Y');
        $data['company'] = isset($requestData['company']) ? $requestData['company'] : '';

        for($i = date('Y'); $i >= 2022; $i--){
            $data['years'][] = $i;
        }

        $company = Company::GetById($data['company'])->first();

        if(!empty($company))
        {
            $tmpOperators = array();
            $tmpOperators = $company;
            $DataArray[] = $tmpOperators;
        }

        $revdataDetails = $DataArray;

        return view('finance.create_target_opex', compact('data','revdataDetails'));
    }

    public function storeTargetOpex(Request $request)
    {
        $requestData = $request->all();

        $year = isset($requestData['year']) ? $requestData['year'] : '';
        $company_id = isset($requestData['company_id']) ? $requestData['company_id'] : '';
        $opexs = isset($requestData['opex']) ? $requestData['opex'] : [];
        $target_opexs = isset($requestData['target_opex']) ? $requestData['target_opex'] : [];

        if(isset($opexs) && !empty($opexs)){
            foreach($opexs as $rkey => $opex){
                $target_opex = (isset($target_opexs[$rkey]) && ($target_opexs[$rkey]) != '') ? $target_opexs[$rkey] : 0;

                if(strlen($rkey) == 1){
                    $key = $year.'-0'.$rkey;
                }else{
                    $key = $year.'-'.$rkey;
                }

                if($opex != '' && $opex != NULL && $target_opex != '' && $target_opex != NULL)
                {
                    $data[] = ['company_id'=> $company_id, 'year'=> $year, 'month'=> $rkey, 'key' => $key , 'opex' => $opex, 'target_opex' => $target_opex ];
                }
            }

            if(!empty($data))
            {
                $response = array();
                $result = TargetOpex::upsert($data,['company_id','year','month'],['key','opex','target_opex']);

                Utility::user_activity('Update Target Opex');

                if($result > 0)
                {
                    $response['success'] = 1;
                    $response['error'] = 0;

                }else{
                    $response['success'] = 0;
                    $response['error'] = 1;
                }
            }else{
                $response['success'] = 0;
                $response['error'] = 1;
            }

            echo json_encode($response); exit(0);
        }
    }

    public function storeTargetOpexExcel(Request $request)
    {
        $requestData = $request->all();
        $files = isset($requestData['file']) ? $requestData['file'] : [];

        if(empty($files)){
            return redirect()->back()->with('error', __('please select a file first!'));
        }

        $file = Excel::toArray(new UsersImport, $files);

        $months = ['january' => '01', 'february' => '02', 'march' => '03', 'april' => '04', 'may' => '05', 'june' => '06', 'july' => '07', 'august' => '08', 'september' => '09', 'october' => '10', 'november' => '11', 'december' => '12'];

        foreach ($file[0] as $key => $value) {
            $year = $value['year'];

            if(empty($value['company']))
            break;

            $company = Company::where('name', $value['company'])->first()->toArray();

            $company_id = $company['id'];

            $name = $value['name'];

            $reconcile = ['12' => $value['december'], '11' => $value['november'], '10' => $value['october'], '09' => $value['september'], '08' => $value['august'], '07' => $value['july'], '06' => $value['june'], '05' => $value['may'], '04' => $value['april'], '03' => $value['march'], '02' => $value['february'], '01' => $value['january'],];

            foreach ($reconcile as $key1 => $value1) {
                $data = [];
                $opex = 0;
                $target_opex = 0;

                if (isset($value1) && $value1 != 0) {
                    $month = $key1;

                    $keys = $year.'-'.$month;

                    $TargetOpex = TargetOpex::filterCompany($company_id)
                    ->filterYear($year)
                    ->Key($keys)
                    ->first();

                    if($TargetOpex){
                        $TargetOpex = $TargetOpex->toArray();
                    }

                    if ($name == 'opex') {
                        $opex = $value1;

                        if(isset($TargetOpex)){
                            $target_opex = $TargetOpex['target_opex'];
                        }
                    }elseif ($name == 'target opex') {
                        $target_opex = $value1;

                        if(isset($TargetOpex)){
                            $opex = $TargetOpex['opex'];
                        }
                    }

                    $data = ['company_id'=> $company_id, 'year'=> $year, 'month'=> $month, 'key' => $keys , 'opex' => $opex, 'target_opex' => $target_opex ];

                    TargetOpex::upsert($data,['company_id','year','month'],['key','opex','target_opex']);

                    Utility::user_activity('Update Target Opex Excel');
                }
            }
        }

        return view('finance.reconcile_popup', compact('file'))->with('success', __('Target Opex successfully added!'));
    }

    // get report using operator id
    function rearrangeOperatorMonth($reports)
    {
        if(!empty($reports))
        {
            $reportsResult = array();
            $tempreport = array();

            foreach($reports as $report)
            {
                $tempreport[$report['operator_id']][$report['key']] = $report;
            }

            $reportsResult = $tempreport;

            return $reportsResult;
        }
    }

    // get report using operator id
    function rearrange_operator_month($reports)
    {
        if(!empty($reports))
        {
            $reportsResult = array();
            $tempreport = array();

            foreach($reports as $report)
            {
                $tempreport[$report['id_operator']][$report['key']] = $report;
            }

            $reportsResult = $tempreport;

            return $reportsResult;
        }
    }

    // Final Cost Report
    public function financeCostReport(Request $request)
    {
        $years = [];
        $months = [];
        $data = [];
        $countries = [];
        $currency_codes = [];
        $flags = [];
        $usd = [];
        $summary = [];
        $Country = Country::all()->toArray();
        $countries = array();
        $CountryId = $req_CountryId = $request->country;
        $CompanyId = $req_CompanyId = $request->company;
        $UserId = $req_UserId = $request->business_manager;
        $filterOperator = $req_filterOperator = $request->operatorId;

        $companys = Company::get();

        if(!empty($Country))
        {
            foreach($Country as $CountryI)
            {
                $countries[$CountryI['id']] = $CountryI;
            }
        }

        $showAllOperator = true;

        if($request->filled('company') && $req_CompanyId != "allcompany" && !$request->filled('operatorId'))
        {
            $companies = Company::Find($req_CompanyId);
            $Operators_company = array();

            if(!empty($companies))
            {
                $Operators_company = $companies->company_operators->pluck('operator_id')->toArray();
                $Operators = Operator::with('revenueshare','RevenushareByDate')
                ->Status(1)
                ->GetOperatorByOperatorId($Operators_company)
                ->get();
            }

            $showAllOperator = false;
        }

        if($request->filled('company') && $request->filled('country') && !$request->filled('operatorId'))
        {
            $data = [
                'id' => $req_CountryId,
                'company' => $req_CompanyId,
            ];

            $requestobj = new Request($data);
            $ReportControllerobj = new ReportController;
            $Operators = $ReportControllerobj->userFilterOperator($requestobj);
            $showAllOperator = false;
        }

        if (isset($req_CompanyId) && !$request->filled('country') && $request->filled('business_manager') && !$request->filled('operatorId')) {
            $Countrys[0] = Country::with('operators')->Find($CountryId);
            $data = [
                'country' => $req_CountryId,
                'company' => $req_CompanyId,
                'business_manager' => $req_UserId,
            ];

            $requestobj = new Request($data);
            $FinanceControllerobj = new FinanceController;
            $Operators = $FinanceControllerobj->userFilterBusinessManagerOperator($requestobj);
            $CountryFlag = false;
            $showAllOperator = false;
        }

        if ($request->filled('company') && $request->filled('country') && $request->filled('business_manager') && !$request->filled('operatorId')) {
            $Countrys[0] = Country::with('operators')->Find($CountryId);
            $data = [
                'country' => $req_CountryId,
                'company' => $req_CompanyId,
                'business_manager' => $req_UserId,
            ];

            $requestobj = new Request($data);
            $FinanceControllerobj = new FinanceController;
            $Operators = $FinanceControllerobj->userFilterBusinessManagerOperator($requestobj);
            $CountryFlag = false;
            $showAllOperator = false;
        }

        if($request->filled('operatorId'))
        {
            $Operators = Operator::with('revenueshare','RevenushareByDate')
            ->Status(1)
            ->GetOperatorByOperatorId($filterOperator)
            ->get();

            $showAllOperator = false;
        }

        if($showAllOperator)
        {
            $Operators = Operator::with('revenueshare','RevenushareByDate')->Status(1)->get();
        }

        $arrayOperatorsIds = $Operators->pluck('id_operator')->toArray();

        $selected_year = isset($request->year) ? $request->year: date('Y');

        for($i = date('Y'); $i >= 2022; $i--){
            $years[] = $i;
        }

        $firstDayoftheyear = Carbon::create($selected_year)->startOfYear()->format('Y-m-d');
        $start_date = $firstDayoftheyear;
        $end_date = Carbon::create($selected_year)->endOfYear()->format('Y-m-d');

        if($selected_year == date('Y'))
        {
            $end_date = Carbon::now()->format('Y-m-d');
        }

        $datesIndividual = Utility::getRangeDates($firstDayoftheyear,$end_date);
        $no_of_months = Utility::getRangeMonthsNo($datesIndividual);
        $month = Carbon::now()->format('F Y');

        $monthList = array();

        foreach ($no_of_months as $key => $no_of_month) {
            $month_key = $no_of_month['date'];
            $monthList[] = $month_key;
        }

        $allMonthlyData = PnlSummeryMonth::filteroperator($arrayOperatorsIds)
        ->Months($monthList)
        ->User(0)
        ->get()
        ->toArray();

        $reportsMonthData = $this->rearrange_operator_month($allMonthlyData);
        $monthdata = $reportsMonthData;
        $month = Carbon::now()->format('F Y');

        if(!empty($Operators))
        {
            foreach($Operators as $operator)
            {
                $id_operator = $operator->id_operator;
                $tmpOperators = array();
                $tmpOperators['operator'] = $operator;

                if(!isset($reportsMonthData[$id_operator]))
                {
                    continue;
                }

                $country_id = $operator->country_id;
                $contain_id = Arr::exists($countries, $country_id);
                $OperatorCountry = array();

                if($contain_id)
                {
                    $tmpOperators['country'] = $countries[$country_id];
                    $OperatorCountry = $countries[$country_id];
                }

                $reportsColumnData = $this->getFinalReportsMonthWise($operator,$no_of_months,$monthdata,$OperatorCountry);

                $tmpOperators['month_string'] = $month;

                $total_cost_campaign = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['cost_campaign'],$firstDayoftheyear,$end_date);
                $tmpOperators['cost_campaign']['dates'] = $reportsColumnData['cost_campaign'];
                $tmpOperators['cost_campaign']['total'] = $total_cost_campaign['sum'];
                $total_final_input_cost_campaign = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['final_input_cost_campaign'],$firstDayoftheyear,$end_date);
                $tmpOperators['final_input_cost_campaign']['dates'] = $reportsColumnData['final_input_cost_campaign'];
                $tmpOperators['final_input_cost_campaign']['total'] = $total_final_input_cost_campaign['sum'];
                $total_cost_campaign_discrepency = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['cost_campaign_discrepency'],$firstDayoftheyear,$end_date);
                $tmpOperators['cost_campaign_discrepency']['dates'] = $reportsColumnData['cost_campaign_discrepency'];

                $cost_campaign = $tmpOperators['cost_campaign']['total'];
                $final_input_cost_campaign = $tmpOperators['final_input_cost_campaign']['total'];
                $tmpOperators['cost_campaign_discrepency']['total'] = ($cost_campaign != 0 && $final_input_cost_campaign != 0) ? (($final_input_cost_campaign - $cost_campaign)/$cost_campaign)*100 : (float)0 ;

                $total_app_content = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['app_content'],$firstDayoftheyear,$end_date);
                $tmpOperators['app_content']['dates'] = $reportsColumnData['app_content'];
                $tmpOperators['app_content']['total'] = $total_app_content['sum'];
                $total_input_app_content = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['input_app_content'],$firstDayoftheyear,$end_date);
                $tmpOperators['input_app_content']['dates'] = $reportsColumnData['input_app_content'];
                $tmpOperators['input_app_content']['total'] = $total_input_app_content['sum'];
                $total_app_content_discrepency = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['app_content_discrepency'],$firstDayoftheyear,$end_date);
                $tmpOperators['app_content_discrepency']['dates'] = $reportsColumnData['app_content_discrepency'];

                $app_content = $tmpOperators['app_content']['total'];
                $input_app_content = $tmpOperators['input_app_content']['total'];
                $tmpOperators['app_content_discrepency']['total'] = ($app_content != 0 && $input_app_content != 0) ? (($input_app_content - $app_content)/$app_content)*100 : (float)0 ;

                $total_cost_rnd = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['cost_rnd'],$firstDayoftheyear,$end_date);
                $tmpOperators['cost_rnd']['dates'] = $reportsColumnData['cost_rnd'];
                $tmpOperators['cost_rnd']['total'] = $total_cost_rnd['sum'];
                $total_input_cost_rnd = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['input_cost_rnd'],$firstDayoftheyear,$end_date);
                $tmpOperators['input_cost_rnd']['dates'] = $reportsColumnData['input_cost_rnd'];
                $tmpOperators['input_cost_rnd']['total'] = $total_input_cost_rnd['sum'];
                $total_cost_rnd_discrepency = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['cost_rnd_discrepency'],$firstDayoftheyear,$end_date);
                $tmpOperators['cost_rnd_discrepency']['dates'] = $reportsColumnData['cost_rnd_discrepency'];

                $cost_rnd = $tmpOperators['cost_rnd']['total'];
                $input_cost_rnd = $tmpOperators['input_cost_rnd']['total'];
                $tmpOperators['cost_rnd_discrepency']['total'] = ($cost_rnd != 0 && $input_cost_rnd != 0) ? (($input_cost_rnd - $cost_rnd)/$cost_rnd)*100 : (float)0 ;

                $total_fun_busket = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['fun_busket'],$firstDayoftheyear,$end_date);
                $tmpOperators['fun_busket']['dates'] = $reportsColumnData['fun_busket'];
                $tmpOperators['fun_busket']['total'] = $total_fun_busket['sum'];
                $total_input_fun_busket = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['input_fun_busket'],$firstDayoftheyear,$end_date);
                $tmpOperators['input_fun_busket']['dates'] = $reportsColumnData['input_fun_busket'];
                $tmpOperators['input_fun_busket']['total'] = $total_input_fun_busket['sum'];
                $total_fun_busket_discrepency = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['fun_busket_discrepency'],$firstDayoftheyear,$end_date);
                $tmpOperators['fun_busket_discrepency']['dates'] = $reportsColumnData['fun_busket_discrepency'];

                $fun_busket = $tmpOperators['fun_busket']['total'];
                $input_fun_busket = $tmpOperators['input_fun_busket']['total'];
                $tmpOperators['fun_busket_discrepency']['total'] = ($fun_busket != 0 && $input_fun_busket != 0) ? (($input_fun_busket - $fun_busket)/$fun_busket)*100 : (float)0 ;

                $total_cost_bd = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['cost_bd'],$firstDayoftheyear,$end_date);
                $tmpOperators['cost_bd']['dates'] = $reportsColumnData['cost_bd'];
                $tmpOperators['cost_bd']['total'] = $total_cost_bd['sum'];
                $total_input_cost_bd = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['input_cost_bd'],$firstDayoftheyear,$end_date);
                $tmpOperators['input_cost_bd']['dates'] = $reportsColumnData['input_cost_bd'];
                $tmpOperators['input_cost_bd']['total'] = $total_input_cost_bd['sum'];
                $total_cost_bd_discrepency = UtilityReportsMonthly::calculateTotalAVG($operator,$reportsColumnData['cost_bd_discrepency'],$firstDayoftheyear,$end_date);
                $tmpOperators['cost_bd_discrepency']['dates'] = $reportsColumnData['cost_bd_discrepency'];

                $cost_bd = $tmpOperators['cost_bd']['total'];
                $input_cost_bd = $tmpOperators['input_cost_bd']['total'];
                $tmpOperators['cost_bd_discrepency']['total'] = ($cost_bd != 0 && $input_cost_bd != 0) ? (($input_cost_bd - $cost_bd)/$cost_bd)*100 : (float)0 ;

                $sumemry[] = $tmpOperators;
            }
        }

        // All country's final cost campaign sum
        $allsummaryData = UtilityFinanceReports::sumOfAllFinalCostData($sumemry);

        // Country Sum from Operator array
        $displayCountries = array();
        $SelectedCountries = array();

        if(!empty($sumemry))
        {
            foreach ($sumemry as $key => $sumemries) {
                $country_id = $sumemries['country']['id'];
                $SelectedCountries[$country_id] = $sumemries['country'];
                $displayCountries[$country_id]['country'] = $sumemries['country'];
                $displayCountries[$country_id]['operator'][] = $sumemries;
            }
        }

        if(!empty($displayCountries))
        {
            foreach ($displayCountries as $c_id => $country_data) {
                $countryReconcileSum = UtilityFinanceReports::sumOfAllFinalCostData($country_data['operator']);
                $displayCountries[$c_id]['countrySum'] = $countryReconcileSum;
            }
        }

        $countryWiseData = $displayCountries;
        // dd($displayCountries);
        $no_of_days = $no_of_months;

        return view('finance.final_cost_report', compact('years','no_of_days','countryWiseData','allsummaryData'));
    }



    // create finance cost report
    public function createFinanceCostReport(Request $request)
    {
        $requestData = $request->all();
        $data = [];
        $DataArray = [];
        $revdataDetails = [];
        $currency = [];
        $dataDetails[] = [];
        $Country = Country::all()->toArray();
        $countries = array();

        if(!empty($Country))
        {
            foreach($Country as $CountryI)
            {
                $countries[$CountryI['id']] = $CountryI;
            }
        }

        $data['year'] = isset($requestData['year']) ? $requestData['year'] : date('Y');
        $data['country'] = isset($requestData['country']) ? $requestData['country'] : '';
        $data['operator'] = isset($requestData['operator']) ? $requestData['operator'] :'';
        $data['service'] = isset($requestData['service']) ? $requestData['service'] : '';

        for($i = date('Y'); $i >= 2022; $i--){
            $data['years'][] = $i;
        }

        $operator = Operator::with('revenueshare')->filterOperatorID($data['operator'])->first();

        if(!empty($operator))
        {
            $tmpOperators = array();
            $tmpOperators['operator'] = $operator;
            $country_id = isset($operator->country_id) ? $operator->country_id : '';
            $tmpOperators['country'] = !empty($country_id) ? $countries[$country_id] : '';
            $dataDetails[] = $tmpOperators;
        }

        foreach($dataDetails as $detail)
        {
            if(isset($detail['operator']['operator_name'])){
                $currency[$detail['country']['country']][$detail['operator']['operator_name']] = $detail['country']['currency_code'];

                $DataArray[$detail['country']['country']][$detail['operator']['operator_name']]['country_id'] = $detail['country']['id'];

                $DataArray[$detail['country']['country']][$detail['operator']['operator_name']]['operator_id'] = $detail['operator']['id_operator'];
            }
        }

        $revdataDetails = $DataArray;

        return view('finance.create_final_cost_report', compact('data','revdataDetails','currency'));
    }

    // store finance cost report
    public function storeFinanceCostReport(Request $request)
    {
        $requestData = $request->all();

        $year = isset($requestData['year']) ? $requestData['year'] : '';
        $country_id = isset($requestData['country_id']) ? $requestData['country_id'] : '';
        $operator_id = isset($requestData['operator_id']) ? $requestData['operator_id'] : '';
        $id_service = isset($requestData['id_service']) ? $requestData['id_service'] : '';
        $final_cost_campaigns = isset($requestData['final_cost_campaign']) ? $requestData['final_cost_campaign'] : [];
        $rnds = isset($requestData['rnd']) ? $requestData['rnd'] : [];
        $contents = isset($requestData['content']) ? $requestData['content'] : [];
        $fun_baskets = isset($requestData['fun_basket']) ? $requestData['fun_basket'] : [];
        $bds = isset($requestData['bd']) ? $requestData['bd'] : [];
        $platforms = isset($requestData['platform']) ? $requestData['platform'] : [];
        $hostings = isset($requestData['hosting']) ? $requestData['hosting'] : [];

        if(isset($final_cost_campaigns) && !empty($final_cost_campaigns)){
            foreach($final_cost_campaigns as $rkey => $cost_campaign){
                $rnd = (isset($rnds[$rkey]) && ($rnds[$rkey]) != '') ? $rnds[$rkey] : 0;

                $content = (isset($contents[$rkey]) && ($contents[$rkey]) != '') ? $contents[$rkey] : 0;

                $fun_basket = (isset($fun_baskets[$rkey]) && ($fun_baskets[$rkey]) != '') ? $fun_baskets[$rkey] : 0;

                $bd = (isset($bds[$rkey]) && ($bds[$rkey]) != '') ? $bds[$rkey] : 0;

                $platform = (isset($platforms[$rkey]) && ($platforms[$rkey]) != '') ? $platforms[$rkey] : 0;

                $hosting = (isset($hostings[$rkey]) && ($hostings[$rkey]) != '') ? $hostings[$rkey] : 0;

                if(strlen($rkey) == 1){
                    $key = $year.'-0'.$rkey;
                }else{
                    $key = $year.'-'.$rkey;
                }

                if($cost_campaign != '' && $cost_campaign != NULL)
                {
                    $data[] = ['country_id'=> $country_id, 'operator_id'=> $operator_id, 'id_service' => $id_service, 'year'=> $year, 'month'=> $rkey, 'key' => $key, 'final_cost_campaign'=> $cost_campaign, 'rnd'=> $rnd, 'content'=> $content, 'fun_basket'=> $fun_basket, 'bd'=> $bd, 'platform'=> $platform, 'hosting'=> $hosting];
                }
            }

            if(!empty($data))
            {
                $response = array();
                $result = FinalCostReports::upsert($data,['country_id','operator_id','id_service','year','month'],['key','final_cost_campaign','rnd','content','fun_basket','bd','platform','hosting']);

                Utility::user_activity('Update Finance Cost');

                if($result > 0)
                {
                    $response['success'] = 1;
                    $response['error'] = 0;
                }else{
                    $response['success'] = 0;
                    $response['error'] = 1;
                }
            }else{
                $response['success'] = 0;
                $response['error'] = 1;
            }

            echo json_encode($response); exit(0);
        }
    }

    public function storeFinanceCostReportExcel(Request $request)
    {
        $requestData = $request->all();
        $files = isset($requestData['file']) ? $requestData['file'] : [];

        if(empty($files)){
            return redirect()->back()->with('error', __('please select a file first!'));
        }

        $file = Excel::toArray(new UsersImport, $files);

        $months = ['january' => '01', 'february' => '02', 'march' => '03', 'april' => '04', 'may' => '05', 'june' => '06', 'july' => '07', 'august' => '08', 'september' => '09', 'october' => '10', 'november' => '11', 'december' => '12'];

        foreach ($file[0] as $key => $value) {
            $year = $value['year'];

            if(empty($value['operator']))
            break;

            $operator = Operator::with('country')->where('operator_name', $value['operator'])->first()->toArray();

            $operator_id = $operator['id_operator'];

            $service = Service::with('operator')->where('keyword', $value['service'])->where('operator_id', $operator_id)->first()->toArray();

            $id_service = $service['id_service'];

            $country_id = $service['operator']['country_id'];

            $name = $value['name'];

            $country = Country::select('currency_code')->where('id', $country_id)->first()->toArray();

            $currency = $country['currency_code'];

            $reconcile = ['12' => $value['december'], '11' => $value['november'], '10' => $value['october'], '09' => $value['september'], '08' => $value['august'], '07' => $value['july'], '06' => $value['june'], '05' => $value['may'], '04' => $value['april'], '03' => $value['march'], '02' => $value['february'], '01' => $value['january'],];

            foreach ($reconcile as $key1 => $value1) {
                $data = [];
                $cost_campaign = 0;
                $rnd = 0;
                $content = 0;
                $fun_basket = 0;
                $bd = 0;
                $platform = 0;
                $hosting = 0;
                if (isset($value1) && $value1 != 0) {
                    $month = $key1;

                    $keys = $year.'-'.$month;

                    $FinalCostReports = FinalCostReports::filterOperator($operator_id)
                    ->filterService($id_service)
                    ->filterYear($year)
                    ->Key($keys)
                    ->first();

                    if($FinalCostReports){
                        $FinalCostReports = $FinalCostReports->toArray();
                    }

                    if($name == 'cost campaign'){
                        $cost_campaign = $value1;

                        if(isset($FinalCostReports)){
                            $rnd = $FinalCostReports['rnd'];
                            $content = $FinalCostReports['content'];
                            $fun_basket = $FinalCostReports['fun_basket'];
                            $bd = $FinalCostReports['bd'];
                            $platform = $FinalCostReports['platform'];
                            $hosting = $FinalCostReports['hosting'];
                        }
                    }elseif ($name == 'rnd'){
                        $rnd = $value1;

                        if(isset($FinalCostReports)){
                            $cost_campaign = $FinalCostReports['final_cost_campaign'];
                            $content = $FinalCostReports['content'];
                            $fun_basket = $FinalCostReports['fun_basket'];
                            $bd = $FinalCostReports['bd'];
                            $platform = $FinalCostReports['platform'];
                            $hosting = $FinalCostReports['hosting'];
                        }
                    }elseif ($name == 'content') {
                        $content = $value1;

                        if(isset($FinalCostReports)){
                            $cost_campaign = $FinalCostReports['final_cost_campaign'];
                            $rnd = $FinalCostReports['rnd'];
                            $fun_basket = $FinalCostReports['fun_basket'];
                            $bd = $FinalCostReports['bd'];
                            $platform = $FinalCostReports['platform'];
                            $hosting = $FinalCostReports['hosting'];
                        }
                    }elseif ($name == 'fun basket') {
                        $fun_basket = $value1;

                        if(isset($FinalCostReports)){
                            $cost_campaign = $FinalCostReports['final_cost_campaign'];
                            $rnd = $FinalCostReports['rnd'];
                            $content = $FinalCostReports['content'];
                            $bd = $FinalCostReports['bd'];
                            $platform = $FinalCostReports['platform'];
                            $hosting = $FinalCostReports['hosting'];
                        }
                    }elseif ($name == 'bd') {
                        $bd = $value1;

                        if(isset($FinalCostReports)){
                            $cost_campaign = $FinalCostReports['final_cost_campaign'];
                            $rnd = $FinalCostReports['rnd'];
                            $content = $FinalCostReports['content'];
                            $fun_basket = $FinalCostReports['fun_basket'];
                            $platform = $FinalCostReports['platform'];
                            $hosting = $FinalCostReports['hosting'];
                        }
                    }elseif ($name == 'platform') {
                        $platform = $value1;

                        if(isset($FinalCostReports)){
                            $cost_campaign = $FinalCostReports['final_cost_campaign'];
                            $rnd = $FinalCostReports['rnd'];
                            $content = $FinalCostReports['content'];
                            $fun_basket = $FinalCostReports['fun_basket'];
                            $bd = $FinalCostReports['bd'];
                            $hosting = $FinalCostReports['hosting'];
                        }
                    }elseif ($name == 'hosting') {
                        $hosting = $value1;

                        if(isset($FinalCostReports)){
                            $cost_campaign = $FinalCostReports['final_cost_campaign'];
                            $rnd = $FinalCostReports['rnd'];
                            $content = $FinalCostReports['content'];
                            $fun_basket = $FinalCostReports['fun_basket'];
                            $bd = $FinalCostReports['bd'];
                            $platform = $FinalCostReports['platform'];
                        }
                    }

                    $data = ['country_id'=> $country_id, 'operator_id'=> $operator_id, 'id_service' => $id_service, 'year'=> $year, 'month'=> $month, 'key' => $keys, 'final_cost_campaign'=> $cost_campaign, 'rnd'=> $rnd, 'content'=> $content, 'fun_basket'=> $fun_basket, 'bd'=> $bd, 'platform'=> $platform, 'hosting'=> $hosting];

                    FinalCostReports::upsert($data,['country_id','operator_id','id_service','year','month'],['key','final_cost_campaign','rnd','content','fun_basket','bd','platform','hosting']);

                    Utility::user_activity('Update Finance Cost Excel');
                }
            }
        }

        return view('finance.reconcile_popup', compact('file'))->with('success', __('Finance Budget successfully added!'));
    }

    public function serviceReconcileData($id)
    {
        $month_name = [1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'];
        $services = RevenueReconciles::filterOperator($id)->get()->toArray();

        foreach ($services as $key => $value) {
            $keyword = Service::select('keyword')->GetserviceById($value['id_service'])->first();

            $services[$key]['keyword'] = isset($keyword['keyword']) ? $keyword['keyword'] : 'NULL';
            $services[$key]['month'] = $month_name[$value['month']];
        }

        return view('finance.service_reconcile', compact('services'));
    }

    public function serviceTargetData($id)
    {
        $month_name = [1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'];
        $services = TargetRevenueReconciles::filterOperator($id)->get()->toArray();

        foreach ($services as $key => $value) {
            $keyword = Service::select('keyword')->GetserviceById($value['id_service'])->first();

            $services[$key]['keyword'] = isset($keyword['keyword']) ? $keyword['keyword'] : 'NULL';
            $services[$key]['month'] = $month_name[$value['month']];
        }

        return view('finance.service_target', compact('services'));
    }

    public function serviceCostData($id)
    {
        $month_name = [1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'];
        $services = FinalCostReports::filterOperator($id)->get()->toArray();

        foreach ($services as $key => $value) {
            $keyword = Service::select('keyword')->GetserviceById($value['id_service'])->first();

            $services[$key]['keyword'] = isset($keyword['keyword']) ? $keyword['keyword'] : 'NULL';
            $services[$key]['month'] = $month_name[$value['month']];
        }

        return view('finance.service_cost', compact('services'));
    }

    public function userFilterCountry(Request $request)
    {
        if($request->id == 'allcompany'){
            $countrys = Operator::select('country_name','country_id')
            ->Status(1)
            ->orderBy('country_name', 'ASC')
            ->distinct()
            ->get()
            ->toArray();

            $operator = Operator::orderBy('operator_name', 'ASC')->get();

            foreach ($operator as $key => $value) {
                $operator[$key]['operator_name'] = !empty($value['display_name']) ? $value['display_name'] : $value['operator_name'];
            }

            $arrayOperatorsIds = $operator->pluck('id_operator')->toArray();

            $users = UsersOperatorsServices::with('user')->select('user_id')->GetOperaterServiceByOperatorIds($arrayOperatorsIds)->distinct()->get();

            $data = ['countrys' => $countrys,'operators' => $operator, 'users' => $users];

            return $data;
        }

        $countrys = [];
        $country_ids = [];
        $country_operator = [];
        $operators = CompanyOperators::GetOperator($request->id)->get();

        foreach($operators as $key => $operator){
            $country = $operator->Operator;

            if(!empty($country) && isset($country[0])){
                if(!in_array($country[0]->country_id,$country_ids))
                {
                    array_push($countrys,$country[0]);
                }

                array_push($country_ids,$country[0]->country_id);
                array_push($country_operator,$country[0]);
            }
        }

        foreach ($country_operator as $key => $value) {
            $country_operator[$key]['operator_name'] = !empty($value['display_name']) ? $value['display_name'] : $value['operator_name'];
        }

        $arrayOperatorsIds = $operators->pluck('operator_id')->toArray();

        $users = UsersOperatorsServices::with('user')->select('user_id')->GetOperaterServiceByOperatorIds($arrayOperatorsIds)->distinct()->get();

        $data = ['countrys' => $countrys,'operators' => $country_operator, 'users' => $users];

        return $data;
    }

    public function userFilterOperator(Request $request)
    {
        if($request->company == 'allcompany'){
            $operators = Operator::Status(1)->GetByCountryIds([$request->id])->get();

            foreach ($operators as $key => $value) {
                $operators[$key]['operator_name'] = !empty($value['display_name']) ? $value['display_name'] : $value['operator_name'];
            }

            $arrayOperatorsIds = $operators->pluck('id_operator')->toArray();

            $users = UsersOperatorsServices::with('user')->select('user_id')->GetOperaterServiceByOperatorIds($arrayOperatorsIds)->distinct()->get();

            $data = ['users' => $users,'operators' => $operators];

            return $data;
        }

        $operators = Operator::Status(1)
        ->join('company_operators', 'company_operators.operator_id','=','operators.id_operator' )
        ->join('companies', 'companies.id','=','company_operators.company_id' )
        ->where('operators.country_id','=',$request->id);

        if($request->filled('company') )
        $operators = $operators->where('company_operators.company_id','=',$request->company);

        $operators = $operators->get(['operators.*','company_operators.company_id']);

        foreach ($operators as $key => $value) {
            $operators[$key]['operator_name'] = !empty($value['display_name']) ? $value['display_name'] : $value['operator_name'];
        }

        $arrayOperatorsIds = $operators->pluck('id_operator')->toArray();

        $users = UsersOperatorsServices::with('user')->select('user_id')->GetOperaterServiceByOperatorIds($arrayOperatorsIds)->distinct()->get();

        $data = ['users' => $users,'operators' => $operators];

        return $data;
    }

    public function userFilterBusinessManagerOperator(Request $request)
    {
        if($request->company == 'allcompany' && $request->country != ''){
            $userOperators = UsersOperatorsServices::select('id_operator')->GetOperaterServiceByUserId($request->business_manager)->distinct()->get();

            $arrayOperatorsIds = $userOperators->pluck('id_operator')->toArray();

            $operators = Operator::Status(1)->filteroperator($arrayOperatorsIds)->GetOperatorByCountryId($request->country)->get();

            foreach ($operators as $key => $value) {
                $operators[$key]['operator_name'] = !empty($value['display_name']) ? $value['display_name'] : $value['operator_name'];
            }

            return $operators;
        }

        if($request->company == 'allcompany' && $request->country == ''){
            $userOperators = UsersOperatorsServices::select('id_operator')->GetOperaterServiceByUserId($request->business_manager)->distinct()->get();

            $arrayOperatorsIds = $userOperators->pluck('id_operator')->toArray();

            $operators = Operator::Status(1)->filteroperator($arrayOperatorsIds)->get();

            foreach ($operators as $key => $value) {
                $operators[$key]['operator_name'] = !empty($value['display_name']) ? $value['display_name'] : $value['operator_name'];
            }

            return $operators;
        }

        $userOperators = UsersOperatorsServices::select('id_operator')->GetOperaterServiceByUserId($request->business_manager)->distinct()->get();

        $arrayOperatorsIds = $userOperators->pluck('id_operator')->toArray();

        $operators = Operator::Status(1)
        ->join('company_operators', 'company_operators.operator_id','=','operators.id_operator' )
        ->join('companies', 'companies.id','=','company_operators.company_id' )
        ->whereIn('operators.id_operator', '=', $arrayOperatorsIds);

        if($request->filled('company') )
        $operators = $operators->where('company_operators.company_id','=',$request->company);

        if($request->filled('country') )
        $operators = $operators->where('operators.country_id','=',$request->country);

        $operators = $operators->get(['operators.*','company_operators.company_id']);

        foreach ($operators as $key => $value) {
            $operators[$key]['operator_name'] = !empty($value['display_name']) ? $value['display_name'] : $value['operator_name'];
        }

        return $operators;
    }
}
