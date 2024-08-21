<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\common\UtilityAnalytic;
use App\Models\Company;
use App\Models\Country;
use App\Models\Operator;
use App\Models\Revenushare;
use App\Models\MonthlyReportSummery;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use App\common\Utility;
use App\Models\User;
use App\common\UtilityReports;
use Illuminate\Support\Facades\Auth;
use App\Models\PnlSummeryMonth;
use App\common\UtilityReportsMonthly;
use App\Models\ReportsPnlsOperatorSummarizes;

class MonthlyAnalyticController extends Controller
{
    //Monthly Operator Wise
    public function revenueMonitoringMonthly(Request $request)
    {
        if (\Auth::user()->can('Revenue Monitoring')) {
            $monthly = 1;

            $CountryId = $req_CountryId = $request->country;
            $CompanyId = $req_CompanyId = $request->company;
            $business_type = $req_BusinessType = $request->business_type;
            $filterOperator = $req_filterOperator = $request->operatorId;
            $Start_date = $req_Start_date = trim($request->from);
            $end_date = $req_end_date = trim($request->to);

            if ($end_date <= $Start_date) {
                $Start_date = $req_Start_date = trim($request->to);
                $end_date =  $req_end_date = $request->from;
            }

            $Country = Country::all()->toArray();
            $companys = Company::get();
            $countries = array();

            if (!empty($Country)) {
                foreach ($Country as $CountryI) {
                    $countries[$CountryI['id']] = $CountryI;
                    $countrys[$CountryI['id']] = [];
                }
            }

            $contains = Arr::hasAny($Country, "2");

            $sumemry = array();

            $start_date =Carbon::now()->startOfYear()->format('Y-m-d');
            $startColumnDateDisplay = Carbon::now()->startOfYear()->format('Y-m-d');
            $end_date = Carbon::now()->format('Y-m-d');
            $today = Carbon::now()->format('Y-m-d');
            $month = Carbon::now()->format('F Y');

            $showAllOperator = true;

            if($request->filled('to') && $request->filled('from'))
            {
                $display_date_input = new Carbon($req_Start_date);
                $start_date = $req_Start_date;
                $startColumnDateDisplay = $display_date_input->format('Y-m-d');
                $end_date = $req_end_date;
                $month = $display_date_input->format('F Y');
            }

            if ($request->filled('country') && !$request->filled('operatorId')) {
                $data = [
                    'id' => $req_CountryId,
                ];

                $requestobj = new Request($data);
                $ReportControllerobj = new ReportController;
                $Operators = $ReportControllerobj->userFilterOperator($requestobj);
                $showAllOperator = false;
            }

            if ($request->filled('business_type')) {
                $data = [
                    'business_type' => $req_BusinessType,
                    'country' => $req_CountryId,
                ];

                $requestobj = new Request($data);
                $ReportControllerobj = new ReportController;
                $Operators = $ReportControllerobj->userFilterBusinessTypeOperator($requestobj);
                $showAllOperator = false;
            }

            if ($request->filled('operatorId')) {
                $Operators = Operator::with('revenueshare')
                    ->Status(1)
                    ->when($req_CountryId != '', function ($q) use ($req_CountryId) {
                        $q->GetOperatorByCountryId($req_CountryId);
                    })
                    ->when($req_BusinessType != '', function ($q) use ($req_BusinessType) {
                        $q->GetOperatorByBusinessType($req_BusinessType);
                    })
                    ->GetOperatorByOperatorId($filterOperator)
                    ->get();
                $showAllOperator = false;
            }

            if ($showAllOperator) {
                $Operators = Operator::with('revenueshare')->Status(1)->get();
            }

            $datesIndividual = Utility::getRangeDates($startColumnDateDisplay, $end_date);
            $no_of_days = Utility::getRangeMonthsNo($datesIndividual);
            $monthList = array();

            foreach ($no_of_days as $key => $no_of_month) {
                $month_key = $no_of_month['date'];
                $monthList[] = $month_key;
            }

            $arrayOperatorsIds = $Operators->pluck('id_operator')->toArray();

            $Pnlreports = MonthlyReportSummery::select('country_id', 'operator_id as id_operator', 'key', 'gros_rev')->Months($monthList)->filteroperator($arrayOperatorsIds);

            $user = Auth::user();
            $user_id = $user->id;
            $user_type = $user->type;
            $allowAllOperator = $user->WhowAccessAlOperator($user_type);

            if ($allowAllOperator) {
                $Pnlreports = $Pnlreports->User(0);
            } else {
                $Pnlreports = $Pnlreports->User($user_id);
            }

            $allMonthlyData = $Pnlreports->get()->toArray();

            $reportsByIDs = $this->rearrangeOperatorMonth($allMonthlyData);

            if (!empty($Operators)) {
                foreach ($Operators as $operator) {
                    $tmpOperators = array();
                    $tmpOperators['operator'] = $operator->toArray();
                    $country_id = $operator->country_id;
                    $contain_id = Arr::exists($countries, $country_id);
                    $OperatorCountry = array();

                    if ($contain_id) {
                        $tmpOperators['country'] = $countries[$country_id];
                        $OperatorCountry = $countries[$country_id];
                    }

                    $reportsColumnData = $this->getReportsGrosRevMonthWise($operator, $no_of_days, $reportsByIDs, $OperatorCountry);
                    $tmpOperators['month_string'] = $month;

                    $total_avg_t = UtilityAnalytic::calculateTotalAVG($operator, $reportsColumnData, $startColumnDateDisplay, $end_date);
                    $tmpOperators['gros_rev']['dates'] = $reportsColumnData;
                    $tmpOperators['gros_rev']['total'] = $total_avg_t['sum'];
                    $tmpOperators['gros_rev']['t_mo_end'] = $total_avg_t['T_Mo_End'];
                    $tmpOperators['gros_rev']['avg'] = $total_avg_t['avg'];

                    array_push($countrys[$country_id], $tmpOperators);
                }
            }

            $countrys = array_filter($countrys);

            $totelCountryCosts = [];
            foreach ($countrys as $country => $operaters) {
                $content_arr = [];
                $totelCountryCosts[$country]['operator'] = $operaters;
                $class = '';
                $cost_campaignPrevious = 0;
                $flag = 5;
                $avg = 0;
                $country_totelcost_campaign = 0;

                foreach ($operaters as $key => $operater) {
                    foreach ($operater['gros_rev']['dates'] as $content_key => $content_value) {
                        if ($key == 0) {
                            $content_sum[$content_key] = 0;
                            $totelCountryCosts[$country]['country'] = $operater['country'];
                        }

                        if (!array_key_exists($content_key, $content_sum)) {
                            $content_sum[$content_key] = 0;
                        }

                        $content_sum[$content_key] = $content_sum[$content_key] + (float)$content_value['value'];

                        if (count($operaters) - 1 == $key) {
                            if ($flag == 5) {
                                $cost_campaignPrevious = $content_sum[$content_key];
                                $flag = 10;
                            }

                            $datacp = $this->classPercentage($cost_campaignPrevious, $content_sum[$content_key]);
                            $class = $datacp['class'];
                            $percentage = $datacp['percentage'];

                            if ($content_key != Carbon::now()->format('Y-m-d'))
                                $country_totelcost_campaign = $country_totelcost_campaign + (float)$content_sum[$content_key];
                            $content_arr[$content_key] = ['value' => $content_sum[$content_key], 'class' => $class, 'percentage' => $percentage];
                            $cost_campaignPrevious = $content_sum[$content_key];
                        }
                    }
                }

                $totelCountryCosts[$country]['gros_rev']['dates'] = $content_arr;
                $totelCountryCosts[$country]['gros_rev']['total'] = $country_totelcost_campaign;

                if (count($content_arr) > 0) {
                    $noofdays = count($content_arr)-1;
                    if($today > $end_date)
                    $noofdays = count($content_arr);

                    if (count($content_arr) > 1) {
                        $totelCountryCosts[$country]['gros_rev']['avg'] = $avg = $country_totelcost_campaign / $noofdays;
                    }

                    $reaming_day = Carbon::parse($end_date)->daysInMonth;
                    $reaming_day = $reaming_day - $noofdays;
                    $T_Mo_End = $country_totelcost_campaign + ($avg * $reaming_day);
                    $totelCountryCosts[$country]['gros_rev']['t_mo_end'] = $T_Mo_End;
                }
            }

            $content_cuntry_sum = [];
            $content_cuntry_arr = [];
            $count = 0;
            $flag = 5;
            $cost_campaignPrevious = 0;
            $avg = $T_Mo_End = $country_totelcost_campaign = 0;

            foreach ($totelCountryCosts as $key => $totelCountryCost) {
                foreach ($totelCountryCost['gros_rev']['dates'] as $content_key => $content_value) {
                    if (!array_key_exists($content_key, $content_cuntry_sum)) {
                        $content_cuntry_sum[$content_key] = 0;
                    }

                    $content_cuntry_sum[$content_key] = $content_cuntry_sum[$content_key] + (float)$content_value['value'];

                    if (count($totelCountryCosts) - 1 == $count) {
                        if ($flag == 5) {
                            $cost_campaignPrevious = $content_cuntry_sum[$content_key];
                            $flag = 10;
                        }

                        $datacp = $this->classPercentage($cost_campaignPrevious, $content_cuntry_sum[$content_key]);
                        $class = $datacp['class'];
                        $percentage = $datacp['percentage'];

                        if ($content_key != Carbon::now()->format('Y-m-d'))
                            $country_totelcost_campaign = $country_totelcost_campaign + (float)$content_cuntry_sum[$content_key];

                        if ($country_totelcost_campaign > 0) {
                            $noofdays = count($totelCountryCost['gros_rev']['dates'])-1;
                            if($today > $end_date)
                            $noofdays = count($totelCountryCost['gros_rev']['dates']);

                            if (count($totelCountryCost['gros_rev']['dates']) > 1)
                            $avg = $country_totelcost_campaign / $noofdays;

                            $reaming_day = Carbon::parse($end_date)->daysInMonth;
                            $reaming_day = $reaming_day - $noofdays;
                            $T_Mo_End = $country_totelcost_campaign + ($avg * $reaming_day);
                        }

                        $content_cuntry_arr[$content_key] = ['value' => $content_cuntry_sum[$content_key], 'class' => $class, 'percentage' => $percentage];

                        $cost_campaignPrevious = $content_cuntry_sum[$content_key];
                    }
                }

                $count++;
            }

            $AllCuntryGrosRev['gros_rev']['dates'] = $content_cuntry_arr;
            $AllCuntryGrosRev['gros_rev']['total'] = $country_totelcost_campaign;
            $AllCuntryGrosRev['gros_rev']['avg'] = $avg;
            $AllCuntryGrosRev['gros_rev']['t_mo_end'] = $T_Mo_End;
            $AllCuntryGrosRev['month_string'] = $month;

            return view('analytic.revenuemonitor', compact('totelCountryCosts', 'AllCuntryGrosRev', 'no_of_days','monthly'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    // Revenue Monitoring Country Monthly Wise Data
    public function revenueMonitoringCountryWiseMonthly(Request $request)
    {
        if (\Auth::user()->can('Revenue Monitoring')) {
            $CountryWise = 1;
            $monthly = 1;

            // get filtre request
            $CountryId = $req_CountryId = $request->country;
            $CompanyId = $req_CompanyId = $request->company;
            $business_type = $req_BusinessType = $request->business_type;
            $filterOperator = $req_filterOperator = $request->operatorId;
            $Start_date = $req_Start_date = trim($request->from);
            $end_date = $req_end_date = trim($request->to);

            if ($end_date <= $Start_date) {
                $Start_date = $req_Start_date = trim($request->to);
                $end_date = $req_end_date = $request->from;
            }

            $Country = Country::all()->toArray();
            $companys = Company::get();
            $countries = array();

            $showAllOperator = true;

            $start_date =Carbon::now()->startOfYear()->format('Y-m-d');
            $startColumnDateDisplay = Carbon::now()->startOfYear()->format('Y-m-d');
            $end_date = Carbon::now()->format('Y-m-d');
            $today = Carbon::now()->format('Y-m-d');
            $month = Carbon::now()->format('F Y');

            $showAllOperator = true;

            if($request->filled('to') && $request->filled('from'))
            {
                $display_date_input = new Carbon($req_Start_date);
                $start_date = $req_Start_date;
                $startColumnDateDisplay = $display_date_input->format('Y-m-d');
                $end_date = $req_end_date;
                $month = $display_date_input->format('F Y');
            }

            if ($request->filled('country') && !$request->filled('operatorId')) {
                $Country = Country::where(['id' => $req_CountryId])->get()->toArray();
                $Operators = Operator::with('revenueshare')->Status(1)->GetOperatorByCountryId($req_CountryId)->get();
                $showAllOperator = false;
            }

            if ($request->filled('business_type')) {
                $data = [
                    'business_type' => $req_BusinessType,
                    'country' => $req_CountryId,
                ];

                $requestobj = new Request($data);
                $ReportControllerobj = new ReportController;
                $Operators = $ReportControllerobj->userFilterBusinessTypeOperator($requestobj);
                $showAllOperator = false;
            }

            if ($request->filled('operatorId')) {
                $Operators = Operator::with('revenueshare')
                    ->Status(1)
                    ->when($req_CountryId != '', function ($q) use ($req_CountryId) {
                        $q->GetOperatorByCountryId($req_CountryId);
                    })
                    ->when($req_BusinessType != '', function ($q) use ($req_BusinessType) {
                        $q->GetOperatorByBusinessType($req_BusinessType);
                    })
                    ->GetOperatorByOperatorId($filterOperator)
                    ->get();

                $showAllOperator = false;
            }

            if ($showAllOperator) {
                $Operators = Operator::with('revenueshare')->Status(1)->get();
            }

            if (!empty($Country)) {
                foreach ($Country as $CountryI) {
                    $countries[$CountryI['id']] = $CountryI;
                    $countrys[$CountryI['id']] = [];
                }
            }

            $contains = Arr::hasAny($Country, "2");

            $sumemry = array();

            $arrayOperatorsIds = $Operators->pluck('id_operator')->toArray();

            $datesIndividual = Utility::getRangeDates($startColumnDateDisplay, $end_date);
            $no_of_days = Utility::getRangeMonthsNo($datesIndividual);
            $monthList = array();

            foreach ($no_of_days as $key => $no_of_month) {
                $month_key = $no_of_month['date'];
                $monthList[] = $month_key;
            }

            $arrayOperatorsIds = $Operators->pluck('id_operator')->toArray();

            $Pnlreports = MonthlyReportSummery::select('country_id', 'operator_id as id_operator', 'key', 'gros_rev')->Months($monthList)->filteroperator($arrayOperatorsIds);

            $user = Auth::user();
            $user_id = $user->id;
            $user_type = $user->type;
            $allowAllOperator = $user->WhowAccessAlOperator($user_type);

            if ($allowAllOperator) {
                $Pnlreports = $Pnlreports->User(0);
            } else {
                $Pnlreports = $Pnlreports->User($user_id);
            }

            $allMonthlyData = $Pnlreports->get()->toArray();

            $reportsByIDs = $this->rearrangeOperatorMonth($allMonthlyData);

            if (!empty($Operators)) {
                foreach ($Operators as $operator) {
                    $tmpOperators = array();
                    $tmpOperators['operator'] = $operator->toArray();
                    $country_id = $operator->country_id;
                    $contain_id = Arr::exists($countries, $country_id);
                    $OperatorCountry = array();

                    if ($contain_id) {
                        $tmpOperators['country'] = $countries[$country_id];
                        $OperatorCountry = $countries[$country_id];
                    }

                    $reportsColumnData = $this->getReportsGrosRevMonthWise($operator, $no_of_days, $reportsByIDs, $OperatorCountry);

                    $total_avg_t = UtilityAnalytic::calculateTotalAVG($operator, $reportsColumnData, $startColumnDateDisplay, $end_date);
                    $tmpOperators['gros_rev']['dates'] = $reportsColumnData;
                    $tmpOperators['gros_rev']['total'] = $total_avg_t['sum'];
                    $tmpOperators['gros_rev']['t_mo_end'] = $total_avg_t['T_Mo_End'];;
                    $tmpOperators['gros_rev']['avg'] = $total_avg_t['avg'];

                    array_push($countrys[$country_id], $tmpOperators);
                }
            }

            $countrys = array_filter($countrys);

            $totelCountryCosts = [];

            foreach ($countrys as $country => $operaters) {
                $content_arr = [];
                $totelCountryCosts[$country]['operator'] = [];
                $class = '';
                $cost_campaignPrevious = 0;
                $flag = 5;
                $avg = 0;
                $country_totelcost_campaign = 0;

                foreach ($operaters as $key => $operater) {
                    foreach ($operater['gros_rev']['dates'] as $content_key => $content_value) {
                        if ($key == 0) {
                            $content_sum[$content_key] = 0;
                            $totelCountryCosts[$country]['country'] = $operater['country'];
                        }

                        $content_sum[$content_key] = $content_sum[$content_key] + (float)$content_value['value'];

                        if (count($operaters) - 1 == $key) {
                            if ($flag == 5) {
                                $cost_campaignPrevious = $content_sum[$content_key];
                                $flag = 10;
                            }

                            $datacp = $this->classPercentage($cost_campaignPrevious, $content_sum[$content_key]);
                            $class = $datacp['class'];
                            $percentage = $datacp['percentage'];

                            if ($content_key != Carbon::now()->format('Y-m-d'))
                                $country_totelcost_campaign = $country_totelcost_campaign + (float)$content_sum[$content_key];

                            $content_arr[$content_key] = ['value' => $content_sum[$content_key], 'class' => $class, 'percentage' => $percentage];

                            $cost_campaignPrevious = $content_sum[$content_key];
                        }
                    }
                }

                $totelCountryCosts[$country]['gros_rev']['dates'] = $content_arr;
                $totelCountryCosts[$country]['gros_rev']['total'] = $country_totelcost_campaign;

                if (count($content_arr) > 0) {
                    if (count($content_arr) > 1) {
                        $totelCountryCosts[$country]['gros_rev']['avg'] = $avg = $country_totelcost_campaign / (count($content_arr) - 1);
                    }

                    $reaming_day = Carbon::now()->daysInMonth;
                    $reaming_day = $reaming_day - (count($content_arr) - 1);
                    $T_Mo_End = $country_totelcost_campaign + ($avg * $reaming_day);
                    $totelCountryCosts[$country]['gros_rev']['t_mo_end'] = $T_Mo_End;
                }
            }

            $content_cuntry_sum = [];
            $content_cuntry_arr = [];
            $count = 0;
            $flag = 5;
            $cost_campaignPrevious = 0;
            $avg = $T_Mo_End = $country_totelcost_campaign = 0;

            foreach ($totelCountryCosts as $key => $totelCountryCost) {
                foreach ($totelCountryCost['gros_rev']['dates'] as $content_key => $content_value) {
                    if (!array_key_exists($content_key, $content_cuntry_sum)) {
                        $content_cuntry_sum[$content_key] = 0;
                    }

                    $content_cuntry_sum[$content_key] = $content_cuntry_sum[$content_key] + (float)$content_value['value'];

                    if (count($totelCountryCosts) - 1 == $count) {
                        if ($flag == 5) {
                            $cost_campaignPrevious = $content_cuntry_sum[$content_key];
                            $flag = 10;
                        }

                        $datacp = $this->classPercentage($cost_campaignPrevious, $content_cuntry_sum[$content_key]);
                        $class = $datacp['class'];
                        $percentage = $datacp['percentage'];

                        if ($content_key != Carbon::now()->format('Y-m-d'))
                            $country_totelcost_campaign = $country_totelcost_campaign + (float)$content_cuntry_sum[$content_key];

                        if ($country_totelcost_campaign > 0) {
                            if (count($totelCountryCost['gros_rev']['dates']) > 1){
                                $avg = $country_totelcost_campaign / (count($totelCountryCost['gros_rev']['dates']) - 1);
                            }

                            $reaming_day = Carbon::now()->daysInMonth;
                            $reaming_day = $reaming_day - (count($totelCountryCost['gros_rev']['dates']) - 1);
                            $T_Mo_End = $country_totelcost_campaign + ($avg * $reaming_day);
                        }

                        $content_cuntry_arr[$content_key] = ['value' => $content_cuntry_sum[$content_key], 'class' => $class, 'percentage' => $percentage];

                        $cost_campaignPrevious = $content_cuntry_sum[$content_key];
                    }
                }

                $count++;
            }

            $AllCuntryGrosRev['gros_rev']['dates'] = $content_cuntry_arr;
            $AllCuntryGrosRev['gros_rev']['total'] = $country_totelcost_campaign;
            $AllCuntryGrosRev['gros_rev']['avg'] = $avg;
            $AllCuntryGrosRev['gros_rev']['t_mo_end'] = $T_Mo_End;
            $AllCuntryGrosRev['month_string'] = $month;

            return view('analytic.revenuemonitor', compact('totelCountryCosts', 'AllCuntryGrosRev', 'no_of_days' , 'monthly'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    // Revenue Monitoring Company Wise Data
    public function revenueMonitoringCompanyWiseMonthly(Request $request)
    {
        if (\Auth::user()->can('Revenue Monitoring')) {
            $CountryWise = 1;

            // get filtre request
            $CountryId = $req_CountryId = $request->country;
            $CompanyId = $req_CompanyId = $request->company;
            $business_type = $req_BusinessType = $request->business_type;
            $filterOperator = $req_filterOperator = $request->operatorId;
            $Start_date = $req_Start_date = trim($request->from);
            $end_date = $req_end_date = trim($request->to);

            if ($end_date <= $Start_date) {
                $Start_date = $req_Start_date = trim($request->to);
                $end_date = $req_end_date = $request->from;
            }

            $Country = Country::all()->toArray();
            $companys = Company::get();
            $countries = array();

            $showAllOperator = true;

            $start_date =Carbon::now()->startOfYear()->format('Y-m-d');
            $startColumnDateDisplay = Carbon::now()->startOfYear()->format('Y-m-d');
            $end_date = Carbon::now()->format('Y-m-d');
            $today = Carbon::now()->format('Y-m-d');
            $month = Carbon::now()->format('F Y');

            $showAllOperator = true;

            if($request->filled('to') && $request->filled('from'))
            {
                $display_date_input = new Carbon($req_Start_date);
                $start_date = $req_Start_date;
                $startColumnDateDisplay = $display_date_input->format('Y-m-d');
                $end_date = $req_end_date;
                $month = $display_date_input->format('F Y');
            }

            if ($request->filled('country') && !$request->filled('operatorId')) {
                $Operators = Operator::with('revenueshare', 'company_operators')->Status(1)->GetOperatorByCountryId($req_CountryId)->get();
                $showAllOperator = false;
            }

            if ($request->filled('business_type')) {
                $data = [
                    'business_type' => $req_BusinessType,
                    'country' => $req_CountryId,
                ];

                $requestobj = new Request($data);
                $ReportControllerobj = new ReportController;
                $Operators = $ReportControllerobj->userFilterBusinessTypeOperator($requestobj);
                $showAllOperator = false;
            }

            if ($request->filled('operatorId')) {
                $Operators = Operator::with('revenueshare', 'company_operators')
                    ->Status(1)
                    ->when($req_CountryId != '', function ($q) use ($req_CountryId) {
                        $q->GetOperatorByCountryId($req_CountryId);
                    })
                    ->when($req_BusinessType != '', function ($q) use ($req_BusinessType) {
                        $q->GetOperatorByBusinessType($req_BusinessType);
                    })
                    ->GetOperatorByOperatorId($filterOperator)
                    ->get();

                $showAllOperator = false;
            }

            if ($showAllOperator) {
                $Operators = Operator::with('revenueshare', 'company_operators')->Status(1)->get();
            }

            if (!empty($Country)) {
                foreach ($Country as $CountryI) {
                    $countries[$CountryI['id']] = $CountryI;
                    $countrys[$CountryI['id']] = [];
                }
            }

            $contains = Arr::hasAny($Country, "2");

            $sumemry = array();

            $arrayOperatorsIds = $Operators->pluck('id_operator')->toArray();

            $datesIndividual = Utility::getRangeDates($startColumnDateDisplay, $end_date);
            $no_of_days = Utility::getRangeMonthsNo($datesIndividual);
            $monthList = array();

            foreach ($no_of_days as $key => $no_of_month) {
                $month_key = $no_of_month['date'];
                $monthList[] = $month_key;
            }

            $arrayOperatorsIds = $Operators->pluck('id_operator')->toArray();

            $Pnlreports = MonthlyReportSummery::select('country_id', 'operator_id as id_operator', 'key', 'gros_rev')->Months($monthList)->filteroperator($arrayOperatorsIds);

            $user = Auth::user();
            $user_id = $user->id;
            $user_type = $user->type;
            $allowAllOperator = $user->WhowAccessAlOperator($user_type);

            if ($allowAllOperator) {
                $Pnlreports = $Pnlreports->User(0);
            } else {
                $Pnlreports = $Pnlreports->User($user_id);
            }

            $allMonthlyData = $Pnlreports->get()->toArray();

            $reportsByIDs = $this->rearrangeOperatorMonth($allMonthlyData);

            if (!empty($Operators)) {
                foreach ($Operators as $operator) {
                    $tmpOperators = array();
                    $tmpOperators['operator'] = $operator->toArray();
                    $country_id = $operator->country_id;
                    $contain_id = Arr::exists($countries, $country_id);
                    $OperatorCountry = array();

                    if ($contain_id) {
                        $tmpOperators['country'] = $countries[$country_id];
                        $OperatorCountry = $countries[$country_id];
                    }

                    $reportsColumnData = $this->getReportsGrosRevMonthWise($operator, $no_of_days, $reportsByIDs, $OperatorCountry);

                    $total_avg_t = UtilityAnalytic::calculateTotalAVG($operator, $reportsColumnData, $startColumnDateDisplay, $end_date);
                    $tmpOperators['gros_rev']['dates'] = $reportsColumnData;
                    $tmpOperators['gros_rev']['total'] = $total_avg_t['sum'];
                    $tmpOperators['gros_rev']['t_mo_end'] = $total_avg_t['T_Mo_End'];;
                    $tmpOperators['gros_rev']['avg'] = $total_avg_t['avg'];

                    if ($operator->company_operators != null) {
                        $tmpOperators['company'] = $operator->company_operators->Company;
                        array_push($countrys[$operator->company_operators->company_id], $tmpOperators);
                    }
                }
            }

            $countrys = array_filter($countrys);

            $totelCountryCosts = [];
            foreach ($countrys as $company => $operaters) {
                $content_arr = [];
                $totelCountryCosts[$company]['operator'] = $operaters;
                $class = '';
                $cost_campaignPrevious = 0;
                $flag = 5;
                $avg = 0;
                $country_totelcost_campaign = 0;

                foreach ($operaters as $key => $operater) {
                    foreach ($operater['gros_rev']['dates'] as $content_key => $content_value) {
                        if ($key == 0) {
                            $content_sum[$content_key] = 0;
                            $totelCountryCosts[$company]['country'] = $operater['country'];
                            $totelCountryCosts[$company]['country']['country'] = $operater['company']->name;
                        }

                        $content_sum[$content_key] = $content_sum[$content_key] + (float)$content_value['value'];

                        if (count($operaters) - 1 == $key) {
                            if ($flag == 5) {
                                $cost_campaignPrevious = $content_sum[$content_key];
                                $flag = 10;
                            } elseif ((float)$cost_campaignPrevious >= (float)$content_sum[$content_key]) {
                                $class = 'text-danger';
                            } else {
                                $class = 'text-success';
                            }

                            if ($content_key != Carbon::now()->format('Y-m-d'))
                                $country_totelcost_campaign = $country_totelcost_campaign + (float)$content_sum[$content_key];

                            $content_arr[$content_key] = ['value' => $content_sum[$content_key], 'class' => $class];

                            $cost_campaignPrevious = $content_sum[$content_key];
                        }
                    }
                }

                $totelCountryCosts[$company]['gros_rev']['dates'] = $content_arr;
                $totelCountryCosts[$company]['gros_rev']['total'] = $country_totelcost_campaign;

                if (count($content_arr) > 0) {
                    if(count($content_arr) > 1) {
                        $totelCountryCosts[$company]['gros_rev']['avg'] = $avg = $country_totelcost_campaign / (count($content_arr) - 1);
                    }

                    $reaming_day = Carbon::now()->daysInMonth;
                    $reaming_day = $reaming_day - (count($content_arr) - 1);
                    $T_Mo_End = $country_totelcost_campaign + ($avg * $reaming_day);
                    $totelCountryCosts[$company]['gros_rev']['t_mo_end'] = $T_Mo_End;
                }
            }

            $content_cuntry_sum = [];
            $content_cuntry_arr = [];
            $count = 0;
            $flag = 5;
            $cost_campaignPrevious = 0;
            $avg = $T_Mo_End = $country_totelcost_campaign = 0;

            foreach ($totelCountryCosts as $key => $totelCountryCost) {
                foreach ($totelCountryCost['gros_rev']['dates'] as $content_key => $content_value) {
                    if (!array_key_exists($content_key, $content_cuntry_sum)) {
                        $content_cuntry_sum[$content_key] = 0;
                    }

                    $content_cuntry_sum[$content_key] = $content_cuntry_sum[$content_key] + (float)$content_value['value'];

                    if (count($totelCountryCosts) - 1 == $count) {
                        if ($flag == 5) {
                            $cost_campaignPrevious = $content_cuntry_sum[$content_key];
                            $flag = 10;
                        }

                        $datacp = $this->classPercentage($cost_campaignPrevious, $content_cuntry_sum[$content_key]);
                        $class = $datacp['class'];
                        $percentage = $datacp['percentage'];

                        if ($content_key != Carbon::now()->format('Y-m-d'))
                            $country_totelcost_campaign = $country_totelcost_campaign + (float)$content_cuntry_sum[$content_key];

                        if ($country_totelcost_campaign > 0) {
                            if (count($totelCountryCost['gros_rev']['dates']) > 1){
                                $avg = $country_totelcost_campaign / (count($totelCountryCost['gros_rev']['dates']) - 1);
                            }

                            $reaming_day = Carbon::now()->daysInMonth;
                            $reaming_day = $reaming_day - (count($totelCountryCost['gros_rev']['dates']) - 1);
                            $T_Mo_End = $country_totelcost_campaign + ($avg * $reaming_day);
                        }

                        $content_cuntry_arr[$content_key] = ['value' => $content_cuntry_sum[$content_key], 'class' => $class, 'percentage' => $percentage];

                        $cost_campaignPrevious = $content_cuntry_sum[$content_key];
                    }
                }

                $count++;
            }

            $AllCuntryGrosRev['gros_rev']['dates'] = $content_cuntry_arr;
            $AllCuntryGrosRev['gros_rev']['total'] = $country_totelcost_campaign;
            $AllCuntryGrosRev['gros_rev']['avg'] = $avg;
            $AllCuntryGrosRev['gros_rev']['t_mo_end'] = $T_Mo_End;
            $AllCuntryGrosRev['month_string'] = $month;

            return view('analytic.revenuemonitor', compact('totelCountryCosts', 'AllCuntryGrosRev', 'no_of_days'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

     // Revenue Monitoring BUsiness Monthly Wise Data
     public function revenueMonitoringBusinessWiseMonthly(Request $request)
     {
         if (\Auth::user()->can('Revenue Monitoring')) {
            $BusinessWise = 1;
            $monthly = 1;

            // get filtre request
            $CountryId = $req_CountryId = $request->country;
            $CompanyId = $req_CompanyId = $request->company;
            $business_type = $req_BusinessType = $request->business_type;
            $filterOperator = $req_filterOperator = $request->operatorId;
            $Start_date = $req_Start_date = trim($request->from);
            $end_date = $req_end_date = trim($request->to);

            if ($end_date <= $Start_date) {
                $Start_date = $req_Start_date = trim($request->to);
                $end_date = $req_end_date = $request->from;
            }

            $Country = Country::all()->toArray();
            $companys = Company::get();
            $countries = array();

            $showAllOperator = true;

            $start_date =Carbon::now()->startOfYear()->format('Y-m-d');
            $startColumnDateDisplay = Carbon::now()->startOfYear()->format('Y-m-d');
            $end_date = Carbon::now()->format('Y-m-d');
            $today = Carbon::now()->format('Y-m-d');
            $month = Carbon::now()->format('F Y');

            $showAllOperator = true;

            if($request->filled('to') && $request->filled('from'))
            {
                $display_date_input = new Carbon($req_Start_date);
                $start_date = $req_Start_date;
                $startColumnDateDisplay = $display_date_input->format('Y-m-d');
                $end_date = $req_end_date;
                $month = $display_date_input->format('F Y');
            }

            if ($request->filled('country') && !$request->filled('operatorId')) {
                $Country = Country::where(['id' => $req_CountryId])->get()->toArray();
                $Operators = Operator::with('revenueshare')->Status(1)->GetOperatorByCountryId($req_CountryId)->get();
                $showAllOperator = false;
            }

            if ($request->filled('business_type')) {
                $data = [
                    'business_type' => $req_BusinessType,
                    'country' => $req_CountryId,
                ];

                $requestobj = new Request($data);
                $ReportControllerobj = new ReportController;
                $Operators = $ReportControllerobj->userFilterBusinessTypeOperator($requestobj);
                $showAllOperator = false;
            }

            if ($request->filled('operatorId')) {
                $Operators = Operator::with('revenueshare')
                    ->Status(1)
                    ->when($req_CountryId != '', function ($q) use ($req_CountryId) {
                        $q->GetOperatorByCountryId($req_CountryId);
                    })
                    ->when($req_BusinessType != '', function ($q) use ($req_BusinessType) {
                        $q->GetOperatorByBusinessType($req_BusinessType);
                    })
                    ->GetOperatorByOperatorId($filterOperator)
                    ->get();

                $showAllOperator = false;
            }

            if ($showAllOperator) {
                $Operators = Operator::with('revenueshare')->Status(1)->get();
            }

            if (!empty($Country)) {
                foreach ($Country as $CountryI) {
                    $countries[$CountryI['id']] = $CountryI;
                    $countrys[$CountryI['id']] = [];
                }
            }

            $contains = Arr::hasAny($Country, "2");

            $sumemry = array();

            $arrayOperatorsIds = $Operators->pluck('id_operator')->toArray();

            $datesIndividual = Utility::getRangeDates($startColumnDateDisplay, $end_date);
            $no_of_days = Utility::getRangeMonthsNo($datesIndividual);
            $monthList = array();

            foreach ($no_of_days as $key => $no_of_month) {
                $month_key = $no_of_month['date'];
                $monthList[] = $month_key;
            }

            $arrayOperatorsIds = $Operators->pluck('id_operator')->toArray();

            $Pnlreports = MonthlyReportSummery::select('country_id', 'operator_id as id_operator', 'key', 'gros_rev')->Months($monthList)->filteroperator($arrayOperatorsIds);

            $user = Auth::user();
            $user_id = $user->id;
            $user_type = $user->type;
            $allowAllOperator = $user->WhowAccessAlOperator($user_type);

            if ($allowAllOperator) {
                $Pnlreports = $Pnlreports->User(0);
            } else {
                $Pnlreports = $Pnlreports->User($user_id);
            }

            $allMonthlyData = $Pnlreports->get()->toArray();

            $reportsByIDs = $this->rearrangeOperatorMonth($allMonthlyData);
            $summary = [];

            if (!empty($Operators)) {
                foreach ($Operators as $operator) {
                    $tmpOperators = array();
                    $type = $operator->business_type;
                    $type = isset($type) ? $type : 'Unknown';
                    $tmpOperators['operator'] = $operator->toArray();
                    $country_id = $operator->country_id;
                    $contain_id = Arr::exists($countries, $country_id);
                    $OperatorCountry = array();

                    if ($contain_id) {
                        $tmpOperators['country'] = $countries[$country_id];
                        $OperatorCountry = $countries[$country_id];
                    }

                    $reportsColumnData = $this->getReportsGrosRevMonthWise($operator, $no_of_days, $reportsByIDs, $OperatorCountry);

                    $total_avg_t = UtilityAnalytic::calculateTotalAVG($operator, $reportsColumnData, $startColumnDateDisplay, $end_date);
                    $tmpOperators['gros_rev']['dates'] = $reportsColumnData;
                    $tmpOperators['gros_rev']['total'] = $total_avg_t['sum'];
                    $tmpOperators['gros_rev']['t_mo_end'] = $total_avg_t['T_Mo_End'];;
                    $tmpOperators['gros_rev']['avg'] = $total_avg_t['avg'];

                    $summary[$type][]=$tmpOperators;
                }
            }

            $countrys = array_filter($countrys);

            $totelCountryCosts = [];

            foreach ($summary as $country => $operaters) {
                $content_arr = [];
                $totelCountryCosts[$country]['operator'] = $operaters;
                $class = '';
                $cost_campaignPrevious = 0;
                $flag = 5;
                $avg = 0;
                $country_totelcost_campaign = 0;

                foreach ($operaters as $key => $operater) {
                    foreach ($operater['gros_rev']['dates'] as $content_key => $content_value) {
                        if ($key == 0) {
                            $content_sum[$content_key] = 0;
                            $totelCountryCosts[$country]['country']['country'] = isset($operater['operator']['business_type']) ? $operater['operator']['business_type'] : 'unknown';
                            $totelCountryCosts[$country]['country']['flag'] = '';

                        }

                        $content_sum[$content_key] = $content_sum[$content_key] + (float)$content_value['value'];

                        if (count($operaters) - 1 == $key) {
                            if ($flag == 5) {
                                $cost_campaignPrevious = $content_sum[$content_key];
                                $flag = 10;
                            }

                            $datacp = $this->classPercentage($cost_campaignPrevious, $content_sum[$content_key]);
                            $class = $datacp['class'];
                            $percentage = $datacp['percentage'];

                            if ($content_key != Carbon::now()->format('Y-m-d'))
                                $country_totelcost_campaign = $country_totelcost_campaign + (float)$content_sum[$content_key];

                            $content_arr[$content_key] = ['value' => $content_sum[$content_key], 'class' => $class, 'percentage' => $percentage];

                            $cost_campaignPrevious = $content_sum[$content_key];
                        }
                    }
                }

                $totelCountryCosts[$country]['gros_rev']['dates'] = $content_arr;
                $totelCountryCosts[$country]['gros_rev']['total'] = $country_totelcost_campaign;

                if (count($content_arr) > 0) {
                    if (count($content_arr) > 1) {
                        $totelCountryCosts[$country]['gros_rev']['avg'] = $avg = $country_totelcost_campaign / (count($content_arr) - 1);
                    }

                    $reaming_day = Carbon::now()->daysInMonth;
                    $reaming_day = $reaming_day - (count($content_arr) - 1);
                    $T_Mo_End = $country_totelcost_campaign + ($avg * $reaming_day);
                    $totelCountryCosts[$country]['gros_rev']['t_mo_end'] = $T_Mo_End;
                }
            }

            $content_cuntry_sum = [];
            $content_cuntry_arr = [];
            $count = 0;
            $flag = 5;
            $cost_campaignPrevious = 0;
            $avg = $T_Mo_End = $country_totelcost_campaign = 0;

            foreach ($totelCountryCosts as $key => $totelCountryCost) {
                foreach ($totelCountryCost['gros_rev']['dates'] as $content_key => $content_value) {
                    if (!array_key_exists($content_key, $content_cuntry_sum)) {
                        $content_cuntry_sum[$content_key] = 0;
                    }

                    $content_cuntry_sum[$content_key] = $content_cuntry_sum[$content_key] + (float)$content_value['value'];

                    if (count($totelCountryCosts) - 1 == $count) {
                        if ($flag == 5) {
                            $cost_campaignPrevious = $content_cuntry_sum[$content_key];
                            $flag = 10;
                        }

                        $datacp = $this->classPercentage($cost_campaignPrevious, $content_cuntry_sum[$content_key]);
                        $class = $datacp['class'];
                        $percentage = $datacp['percentage'];

                        if ($content_key != Carbon::now()->format('Y-m-d'))
                            $country_totelcost_campaign = $country_totelcost_campaign + (float)$content_cuntry_sum[$content_key];

                        if ($country_totelcost_campaign > 0) {
                            if (count($totelCountryCost['gros_rev']['dates']) > 1){
                                $avg = $country_totelcost_campaign / (count($totelCountryCost['gros_rev']['dates']) - 1);
                            }

                            $reaming_day = Carbon::now()->daysInMonth;
                            $reaming_day = $reaming_day - (count($totelCountryCost['gros_rev']['dates']) - 1);
                            $T_Mo_End = $country_totelcost_campaign + ($avg * $reaming_day);
                        }

                        $content_cuntry_arr[$content_key] = ['value' => $content_cuntry_sum[$content_key], 'class' => $class, 'percentage' => $percentage];

                        $cost_campaignPrevious = $content_cuntry_sum[$content_key];
                    }
                }

                $count++;
            }

            $AllCuntryGrosRev['gros_rev']['dates'] = $content_cuntry_arr;
            $AllCuntryGrosRev['gros_rev']['total'] = $country_totelcost_campaign;
            $AllCuntryGrosRev['gros_rev']['avg'] = $avg;
            $AllCuntryGrosRev['gros_rev']['t_mo_end'] = $T_Mo_End;
            $AllCuntryGrosRev['month_string'] = $month;

            return view('analytic.revenuemonitor', compact('totelCountryCosts', 'AllCuntryGrosRev', 'no_of_days' , 'monthly','BusinessWise'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    function rearrangeOperatorMonth($reports)
    {
        if (!empty($reports)) {
            $reportsResult = array();
            $tempreport = array();
            foreach ($reports as $report) {
                $tempreport[$report['id_operator']][$report['key']] = $report;
            }

            $reportsResult =  $tempreport;
            return $reportsResult;
        }
    }

    function getReportsGrosRevMonthWise($operator, $no_of_days, $reportsByIDs, $OperatorCountry)
    {
        $usdValue = $OperatorCountry['usd'];

        if (!empty($no_of_days)) {
            $allColumnData = array();
            $revenue = array();
            $id_operator = $operator->id_operator;
            $class = '';
            $revenuePrevious = 0;
            $flag = 5;

            foreach (array_reverse($no_of_days) as $days) {
                $keys = $id_operator . "." . $days['date'];
                $summariserow = Arr::get($reportsByIDs, $keys, 0);

                $revenuedata = isset($summariserow['gros_rev']) ? $summariserow['gros_rev'] : 0;
                $revenuedata = $revenuedata * $usdValue;

                if ($flag == 5) {
                    $revenuePrevious = $revenuedata;
                    $flag = 10;
                }

                $datacp = $this->classPercentage($revenuePrevious, $revenuedata);
                $class = $datacp['class'];
                $percentage = $datacp['percentage'];

                if ($days['date'] == Carbon::now()->format('Y-m-d')) {
                    $class = '';
                    $percentage = 0;
                }

                $revenue[$days['date']]['value'] = $revenuedata;
                $revenue[$days['date']]['class'] = $class;
                $revenue[$days['date']]['percentage'] = $percentage;
                $revenuePrevious = $revenuedata;
            }

            $allColumnData = $revenue;

            return $allColumnData;
        }
    }

    public function classPercentage($revenuePrevious, $revenuedata)
    {
        if ((float)$revenuePrevious > (float)$revenuedata) {
            $class = 'text-danger';
        } elseif ((float)$revenuePrevious == (float)$revenuedata) {
            $class = '';
        } else {
            $class = 'text-success';
        }

        $percentage = 0;

        if ($revenuePrevious > 0)
            $percentage = (((float)$revenuedata - (float)$revenuePrevious) * 100) / $revenuePrevious;

        $data = ['class' => $class, 'percentage' => round($percentage, 1)];

        return $data;
    }

    //analytic monitor operatorwise
    public function ReportMonitorOperatorWise(Request $request)
    {
        if (\Auth::user()->can('Monitor Operational')) {
            $monthly = 1;
            // get filtre request
            $CountryId = $req_CountryId = $request->country;
            $CompanyId = $req_CompanyId = $request->company;
            $filterOperator = $req_filterOperator = $request->operatorId;
            $Start_date = $req_Start_date = $request->to;
            $end_date =  $req_end_date = trim($request->from);

            /*If from is less than to*/
            if ($end_date <= $Start_date) {
                $Start_date = $req_Start_date = trim($request->from);
                $end_date =  $req_end_date = $request->to;
            }

            // dd(compact('Start_date','end_date'));

            $firstDayoftheyear = Carbon::now()->startOfYear()->format('Y-m-d');
            //$firstDayoftheyear = Carbon::now()->startOfMonth()->subMonths(24)->format('Y-m-d');
            $start_date = $firstDayoftheyear;
            $startColumnDateDisplay = $firstDayoftheyear;
            $end_date = Carbon::now()->format('Y-m-d');
            $month = Carbon::now()->format('F Y');
            /* filter Select box Display Data */
            $companys = Company::get();
            $today = Carbon::now()->format('Y-m-d');

            /* ENd */


            /* filter Search Section */

            $showAllOperator = true;

            if ($request->filled('to') && $request->filled('from')) {
                $display_date_input = new Carbon($req_Start_date);
                $start_date = $req_Start_date;
                $startColumnDateDisplay = $display_date_input->format('Y-m-d');
                $end_date = $req_end_date;
            }

            if ($request->filled('company') && $req_CompanyId != "allcompany"  && !$request->filled('operatorId')) {
                $companies = Company::Find($req_CompanyId);
                $Operators_company = array();
                if (!empty($companies)) {
                    $Operators_company = $companies->company_operators->pluck('operator_id')->toArray();
                    $Operators = Operator::with('revenueshare')->Status(1)->GetOperatorByOperatorId($Operators_company)->get();
                }
                $showAllOperator = false;
            }

            if ($request->filled('country') && !$request->filled('operatorId')) {
                $Operators = Operator::with('revenueshare')->Status(1)->GetOperatorByCountryId($req_CountryId)->get();
                $showAllOperator = false;
            }

            if ($request->filled('company') && $request->filled('country') && !$request->filled('operatorId')) {
                $data = [
                    'id' => $req_CountryId,
                    'company' => $req_CompanyId,
                ];
                $requestobj = new Request($data);
                $ReportControllerobj = new ReportController;
                $Operators = $ReportControllerobj->userFilterOperator($requestobj);
                $showAllOperator = false;
            }

            if ($request->filled('operatorId')) {
                $Operators = Operator::with('revenueshare')->Status(1)->GetOperatorByOperatorId($filterOperator)->get();
                $showAllOperator = false;
            }

            if ($showAllOperator) {
                $Operators = Operator::with('revenueshare')->Status(1)->get();
            }

            $Country = Country::all()->toArray();
            // $companys = Company::get();
            $countries = array();
            $sumemry = array();

            if (!empty($Country)) {
                foreach ($Country as $CountryI) {
                    $countries[$CountryI['id']] = $CountryI;
                    $countrys[$CountryI['id']] = [];
                }
            }

            $arrayOperatorsIds = $Operators->pluck('id_operator')->toArray();
            $datesIndividual = Utility::getRangeDates($startColumnDateDisplay, $end_date);
            $no_of_months = Utility::getRangeMonthsNo($datesIndividual);

            $monthList = array();

            foreach ($no_of_months as $key => $no_of_month) {
                $month_key = $no_of_month['date'];
                $monthList[] = $month_key;
            }

            /* Admin Access All operator and Services */


            $QueryMonthlyReports = PnlSummeryMonth::filteroperator($arrayOperatorsIds)->Months($monthList);

            $user = Auth::user();
            $user_id = $user->id;
            $user_type = $user->type;
            $allowAllOperator = $user->WhowAccessAlOperator($user_type);
            $data = new PnlMonthlyReportDetailsController;
            if ($allowAllOperator) {
                $QueryMonthlyReports = $QueryMonthlyReports->User(0);
            } else {
                $QueryMonthlyReports = $QueryMonthlyReports->User(0);
                $QueryMonthlyUserReports = MonthlyReportSummery::filteroperator($arrayOperatorsIds)->Months($monthList);
                $QueryMonthlyUserReports = $QueryMonthlyUserReports->User($user_id);
                $allMonthlyUserData = $QueryMonthlyUserReports->get()->toArray();

                $reportsMonthUserData = $data->rearrangeOperatorMonthUser($allMonthlyUserData);
            }

            $allMonthlyData = $QueryMonthlyReports->get()->toArray();

            $reportsMonthData = $data->rearrangeOperatorMonth($allMonthlyData);
            $monthdata = $reportsMonthData;

            $start_date_roi = Carbon::yesterday()->subDays(30)->format('Y-m-d');
            $end_date_roi = Carbon::yesterday()->format('Y-m-d');
            $date_roi = Carbon::now()->format('Y-m-d');
            $reports = ReportsPnlsOperatorSummarizes::filterOperator($arrayOperatorsIds)
                ->OperatorNotNull()
                ->filterDateRange($start_date_roi, $end_date_roi)
                ->SumOfRoiDataOperator()
                ->get()
                ->toArray();

            $active_subs = ReportsPnlsOperatorSummarizes::filteroperator($arrayOperatorsIds)
                ->where(['date' => $date_roi])
                ->TotalOperator()
                ->get()
                ->toArray();


            $reportsByOperatorIDs = $data->getReportsByOperatorID($reports);
            $active_subsByOperatorIDs = $data->getReportsByOperatorID($active_subs);

            $totelCountryCosts = [];
            if (!empty($Operators)) {
                foreach ($Operators as $operator) {
                    $id_operator = $operator->id_operator;
                    // dd($id_operator);
                    $tmpOperators = array();
                    $tmpOperators['operator'] = $operator;
                    if (!isset($reportsMonthData[$id_operator])) {
                        continue;
                    }

                    if (isset($operator->revenueshare)) {
                        $merchant_revenue_share = $operator->revenueshare->merchant_revenue_share;
                    } else {
                        $merchant_revenue_share = 100;
                    }

                    $tmpOperators['data'] = $monthdata;
                    //dd($tmpOperators);
                    $country_id  = $operator->country_id;
                    $contain_id = Arr::exists($countries, $country_id);
                    $OperatorCountry = array();

                    if ($contain_id) {
                        $tmpOperators['country'] = $countries[$country_id];
                        $OperatorCountry = $countries[$country_id];
                    }
                    if (isset($reportsMonthUserData)  && !empty($reportsMonthUserData)) {
                        foreach ($reportsMonthUserData as $key1 => $value1) {
                            if ($key1 == $id_operator) {
                                foreach ($value1 as $key2 => $value2) {
                                    $monthdata[$id_operator][$key2]['rev'] = $value2['gros_rev'];
                                    $monthdata[$id_operator][$key2]['rev_usd'] = $value2['gros_rev'] * $OperatorCountry['usd'];
                                    $monthdata[$id_operator][$key2]['lshare'] = $value2['gros_rev'] * ($merchant_revenue_share / 100);
                                    $monthdata[$id_operator][$key2]['share'] = $value2['gros_rev'] * $OperatorCountry['usd'] * ($merchant_revenue_share / 100);
                                }
                            }
                        }
                    }
                    $reportsColumnData = $data->getPNLReportsDateWise($operator, $no_of_months, $monthdata, $OperatorCountry, $reportsByOperatorIDs, $active_subsByOperatorIDs);

                    $tmpOperators['month_string'] = $month;

                    $total_avg_t = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['end_user_rev_usd'], $startColumnDateDisplay, $end_date, $no_of_months);
                    $tmpOperators['tur']['dates'] = $reportsColumnData['end_user_rev_usd'];
                    $tmpOperators['tur']['total'] = $total_avg_t['sum'];
                    $tmpOperators['tur']['t_mo_end'] = $total_avg_t['T_Mo_End'];
                    $tmpOperators['tur']['avg'] = $total_avg_t['avg'];

                    $total_avg_t_bill = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['bill'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['bill']['dates'] = $reportsColumnData['bill'];
                    $tmpOperators['bill']['total'] = $total_avg_t_bill['sum'];
                    $tmpOperators['bill']['t_mo_end'] = $total_avg_t_bill['T_Mo_End'];
                    $tmpOperators['bill']['avg'] = $total_avg_t_bill['avg'];

                    $total_avg_mo = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['mo'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['mo']['dates'] = $reportsColumnData['mo'];
                    $tmpOperators['mo']['total'] = $total_avg_mo['sum'];
                    $tmpOperators['mo']['t_mo_end'] = $total_avg_mo['T_Mo_End'];
                    $tmpOperators['mo']['avg'] = $total_avg_mo['avg'];

                    $total_avg_roi = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['roi'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['roi']['dates'] = $reportsColumnData['roi'];
                    $tmpOperators['roi']['total'] = $total_avg_roi['sum'];
                    $tmpOperators['roi']['t_mo_end'] = $total_avg_roi['T_Mo_End'];
                    $tmpOperators['roi']['avg'] = $total_avg_roi['avg'];

                    $total_avg_renewal = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['renewal'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['renewal']['dates'] = $reportsColumnData['renewal'];
                    $tmpOperators['renewal']['total'] = $total_avg_renewal['sum'];
                    $tmpOperators['renewal']['t_mo_end'] = $total_avg_renewal['T_Mo_End'];
                    $tmpOperators['renewal']['avg'] = $total_avg_renewal['avg'];

                    $total_avg_dp_success = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['dp_success'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['dp_success']['dates'] = $reportsColumnData['dp_success'];
                    $tmpOperators['dp_success']['total'] = $total_avg_dp_success['sum'];
                    $tmpOperators['dp_success']['t_mo_end'] = $total_avg_dp_success['T_Mo_End'];
                    $tmpOperators['dp_success']['avg'] = $total_avg_dp_success['avg'];


                    $total_avg_dp_failed = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['dp_failed'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['dp_failed']['dates'] = $reportsColumnData['dp_failed'];
                    $tmpOperators['dp_failed']['total'] = $total_avg_dp_failed['sum'];
                    $tmpOperators['dp_failed']['t_mo_end'] = $total_avg_dp_failed['T_Mo_End'];
                    $tmpOperators['dp_failed']['avg'] = $total_avg_dp_failed['avg'];

                    $total_avg_active_subs = UtilityReports::calculateTotalSubscribe($operator, $reportsColumnData['active_subs'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['active_subs']['dates'] = $reportsColumnData['active_subs'];
                    $tmpOperators['active_subs']['total'] = $total_avg_active_subs['sum'];
                    $tmpOperators['active_subs']['t_mo_end'] = $total_avg_active_subs['T_Mo_End'];
                    $tmpOperators['active_subs']['avg'] = $total_avg_active_subs['avg'];

                    $total_avg_cost_campaign = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['cost_campaign'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['cost_campaign']['dates'] = $reportsColumnData['cost_campaign'];
                    $tmpOperators['cost_campaign']['total'] = $total_avg_cost_campaign['sum'];
                    $tmpOperators['cost_campaign']['t_mo_end'] = $total_avg_cost_campaign['T_Mo_End'];
                    $tmpOperators['cost_campaign']['avg'] = $total_avg_cost_campaign['avg'];

                    $total_avg_gros_rev_usd = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['gros_rev_usd'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['gros_rev_usd']['dates'] = $reportsColumnData['gros_rev_usd'];
                    $tmpOperators['gros_rev_usd']['total'] = $total_avg_gros_rev_usd['sum'];
                    $tmpOperators['gros_rev_usd']['t_mo_end'] = $total_avg_gros_rev_usd['T_Mo_End'];
                    $tmpOperators['gros_rev_usd']['avg'] = $total_avg_gros_rev_usd['avg'];

                    $total_avg_last_30_gros_rev = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['last_30_gros_rev'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['last_30_gros_rev']['dates'] = $reportsColumnData['last_30_gros_rev'];
                    $tmpOperators['last_30_gros_rev']['total'] = $total_avg_last_30_gros_rev['sum'];
                    $tmpOperators['last_30_gros_rev']['t_mo_end'] = $total_avg_last_30_gros_rev['T_Mo_End'];
                    $tmpOperators['last_30_gros_rev']['avg'] = $total_avg_last_30_gros_rev['avg'];

                    $total_avg_last_30_reg = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['last_30_reg'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['last_30_reg']['dates'] = $reportsColumnData['last_30_reg'];
                    $tmpOperators['last_30_reg']['total'] = $total_avg_last_30_reg['sum'];
                    $tmpOperators['last_30_reg']['t_mo_end'] = $total_avg_last_30_reg['T_Mo_End'];
                    $tmpOperators['last_30_reg']['avg'] = $total_avg_last_30_reg['avg'];

                    $total_avg_price_mo_cost = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['price_mo_cost'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['price_mo_cost']['dates'] = $reportsColumnData['price_mo_cost'];
                    $tmpOperators['price_mo_cost']['total'] = $total_avg_price_mo_cost['sum'];
                    $tmpOperators['price_mo_cost']['t_mo_end'] = $total_avg_price_mo_cost['T_Mo_End'];
                    $tmpOperators['price_mo_cost']['avg'] = $total_avg_price_mo_cost['avg'];

                    $total_avg_price_mo_mo = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['price_mo_mo'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['price_mo_mo']['dates'] = $reportsColumnData['price_mo_mo'];
                    $tmpOperators['price_mo_mo']['total'] = $total_avg_price_mo_mo['sum'];
                    $tmpOperators['price_mo_mo']['t_mo_end'] = $total_avg_price_mo_mo['T_Mo_End'];
                    $tmpOperators['price_mo_mo']['avg'] = $total_avg_price_mo_mo['avg'];

                    $total_avg_gros_rev_usd = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['reg'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['reg']['dates'] = $reportsColumnData['reg'];
                    // $totelCountryCosts[] = $tmpOperators;
                    array_push($countrys[$country_id], $tmpOperators);
                }
            }

            $countrys = array_filter($countrys);

            $totelCountryCosts = [];
            foreach ($countrys as $country => $operaters) {

                $content_arr = [];
                $bill_arr = [];
                $mo_arr = [];
                $roi_arr = [];
                $renewal_arr = [];
                $gros_rev_usd_arr = [];
                $last_30_gros_rev_arr = [];
                $last_30_reg_arr = [];
                $price_mo_cost_arr = [];
                $price_mo_mo_arr = [];
                $reg_arr = [];
                $dp_success_arr = [];
                $dp_failed_arr = [];
                $cost_campaign_arr = [];
                $active_subs_arr = [];
                $totelCountryCosts[$country]['operator'] = $operaters;
                $class = '';
                $cost_campaignPrevious = 0;
                $flag = 5;
                $country_totelcost_campaign = 0;
                $country_totelcost_mo = 0;
                $country_totelcost_bill = 0;
                $country_totelcost_roi = 0;
                $country_totelcost_renewal = 0;
                $country_totelcost_active_subs = 0;
                $country_totelcost_dp_success = 0;
                $country_totelcost_dp_failed = 0;
                $country_totelcost_cost_campaign = 0;
                $country_totelcost_gros_rev_usd = 0;
                $country_totelcost_reg = 0;
                $country_totelcost_last_30_gros_rev = 0;
                $country_totelcost_last_30_reg = 0;
                $country_totelcost_price_mo_cost = 0;
                $country_totelcost_price_mo_mo = 0;
                $count_renewal = 0;
                $renewal_avg = 0;
                $content_sum = [];


                foreach ($operaters as $key => $operater) {

                    //for revenue
                    foreach ($operater['tur']['dates'] as $content_key => $content_value) {

                        if ($key == 0) {
                            $content_sum[$content_key] = 0;
                            $totelCountryCosts[$country]['country'] = $operater['country'];
                        }
                        if (!array_key_exists($content_key, $content_sum)) {
                            $content_sum[$content_key] = 0;
                        }
                        $content_sum[$content_key] = $content_sum[$content_key] + (float)$content_value['value'];


                        if (count($operaters) - 1 == $key) {

                            if ($flag == 5) {
                                $cost_campaignPrevious = $content_sum[$content_key];
                                $flag = 10;
                            }
                            if ($content_key != Carbon::now()->format('Y-m'))
                                $country_totelcost_campaign = $country_totelcost_campaign + (float)$content_sum[$content_key];
                            $content_arr[$content_key] = ['value' => $content_sum[$content_key]];
                            $cost_campaignPrevious = $content_sum[$content_key];
                        }
                        if (count($content_arr) > 1) {
                            $noofdays = count($content_arr) - 1;
                            if ($today > $end_date)
                                $noofdays = count($content_arr);

                            $totelCountryCosts[$country]['tur']['avg'] = $avg = $country_totelcost_campaign / $noofdays;

                            $reaming_day = Carbon::parse($end_date)->daysInMonth;
                            $reaming_day = $reaming_day - $noofdays;
                            $T_Mo_End = $avg * 12;
                            $totelCountryCosts[$country]['tur']['t_mo_end'] = $T_Mo_End;
                        }
                    }

                    //for mo
                    foreach ($operater['mo']['dates'] as $mo_key => $mo_value) {

                        if ($key == 0) {
                            $mo_sum[$mo_key] = 0;
                            $totelCountryCosts[$country]['country'] = $operater['country'];
                        }
                        if (!array_key_exists($mo_key, $mo_sum)) {
                            $mo_sum[$mo_key] = 0;
                        }
                        $mo_sum[$mo_key] = $mo_sum[$mo_key] + (float)$mo_value['value'];
                        if (count($operaters) - 1 == $key) {

                            if ($flag == 5) {
                                $moPrevious = $mo_sum[$mo_key];
                                $flag = 10;
                            }
                            if ($mo_key != Carbon::now()->format('Y-m'))
                                $country_totelcost_mo = $country_totelcost_mo + (float)$mo_sum[$mo_key];
                            $mo_arr[$mo_key] = ['value' => $mo_sum[$mo_key]];
                            $moPrevious = $mo_sum[$mo_key];
                        }
                        if (count($mo_arr) > 1) {

                            $noofdays = count($mo_arr) - 1;
                            if ($today > $end_date)
                                $noofdays = count($mo_arr);

                            $totelCountryCosts[$country]['mo']['avg'] = $avg = $country_totelcost_mo / $noofdays;

                            $reaming_day = Carbon::parse($end_date)->daysInMonth;
                            $reaming_day = $reaming_day - $noofdays;
                            $T_Mo_End = $avg * 12;
                            $totelCountryCosts[$country]['mo']['t_mo_end'] = $T_Mo_End;
                        }
                    }

                    //for active subs
                    foreach ($operater['active_subs']['dates'] as $active_subs_key => $active_subs_value) {

                        if ($key == 0) {
                            $active_subs_sum[$active_subs_key] = 0;
                            $totelCountryCosts[$country]['country'] = $operater['country'];
                        }
                        if (!array_key_exists($active_subs_key, $active_subs_sum)) {
                            $active_subs_sum[$active_subs_key] = 0;
                        }
                        $active_subs_sum[$active_subs_key] = $active_subs_sum[$active_subs_key] + (float)$active_subs_value['value'];
                        if (count($operaters) - 1 == $key) {

                            if ($flag == 5) {
                                $active_subsPrevious = $active_subs_sum[$active_subs_key];
                                $flag = 10;
                            }
                            if ($active_subs_key != Carbon::now()->format('Y-m'))
                                $country_totelcost_active_subs = $country_totelcost_active_subs + (float)$active_subs_sum[$active_subs_key];
                            $active_subs_arr[$active_subs_key] = ['value' => $active_subs_sum[$active_subs_key]];
                            $active_subsPrevious = $active_subs_sum[$active_subs_key];
                        }
                        if (count($active_subs_arr) > 1) {

                            $noofdays = count($active_subs_arr) - 1;
                            if ($today > $end_date)
                                $noofdays = count($active_subs_arr);

                            $totelCountryCosts[$country]['active_subs']['avg'] = $avg = $country_totelcost_active_subs / $noofdays;

                            $reaming_day = Carbon::parse($end_date)->daysInMonth;
                            $reaming_day = $reaming_day - $noofdays;
                            $T_Mo_End = $country_totelcost_mo + ($avg * $reaming_day);
                            $totelCountryCosts[$country]['active_subs']['t_mo_end'] = $T_Mo_End;
                        }
                    }

                    //for dp_success
                    foreach ($operater['dp_success']['dates'] as $dp_success_key => $dp_success_value) {

                        if ($key == 0) {
                            $dp_success_sum[$dp_success_key] = 0;
                            $totelCountryCosts[$country]['country'] = $operater['country'];
                        }
                        if (!array_key_exists($dp_success_key, $dp_success_sum)) {
                            $dp_success_sum[$dp_success_key] = 0;
                        }
                        $dp_success_sum[$dp_success_key] = $dp_success_sum[$dp_success_key] + (float)$dp_success_value['value'];
                        if (count($operaters) - 1 == $key) {

                            if ($flag == 5) {
                                $dp_successPrevious = $dp_success_sum[$dp_success_key];
                                $flag = 10;
                            }
                            if ($dp_success_key != Carbon::now()->format('Y-m'))
                                $country_totelcost_dp_success = $country_totelcost_dp_success + (float)$dp_success_sum[$dp_success_key];
                            $dp_success_arr[$dp_success_key] = ['value' => $dp_success_sum[$dp_success_key]];
                            $dp_successPrevious = $dp_success_sum[$dp_success_key];
                        }
                        if (count($dp_success_arr) > 1) {

                            $noofdays = count($dp_success_arr) - 1;
                            if ($today > $end_date)
                                $noofdays = count($dp_success_arr);

                            $totelCountryCosts[$country]['dp_success']['avg'] = $avg = $country_totelcost_dp_success / $noofdays;

                            $reaming_day = Carbon::parse($end_date)->daysInMonth;
                            $reaming_day = $reaming_day - $noofdays;
                            $T_Mo_End = $country_totelcost_mo + ($avg * $reaming_day);
                            $totelCountryCosts[$country]['dp_success']['t_mo_end'] = $T_Mo_End;
                        }
                    }

                    //for dp_failed
                    foreach ($operater['dp_failed']['dates'] as $dp_failed_key => $dp_failed_value) {

                        if ($key == 0) {
                            $dp_failed_sum[$dp_failed_key] = 0;
                            $totelCountryCosts[$country]['country'] = $operater['country'];
                        }
                        if (!array_key_exists($dp_failed_key, $dp_failed_sum)) {
                            $dp_failed_sum[$dp_failed_key] = 0;
                        }
                        $dp_failed_sum[$dp_failed_key] = $dp_failed_sum[$dp_failed_key] + (float)$dp_failed_value['value'];
                        if (count($operaters) - 1 == $key) {

                            if ($flag == 5) {
                                $dp_failedPrevious = $dp_failed_sum[$dp_failed_key];
                                $flag = 10;
                            }
                            if ($dp_failed_key != Carbon::now()->format('Y-m'))
                                $country_totelcost_dp_failed = $country_totelcost_dp_failed + (float)$dp_failed_sum[$dp_failed_key];
                            $dp_failed_arr[$dp_failed_key] = ['value' => $dp_failed_sum[$dp_failed_key]];
                            $dp_failedPrevious = $dp_failed_sum[$dp_failed_key];
                        }
                        if (count($dp_failed_arr) > 1) {

                            $noofdays = count($dp_failed_arr) - 1;
                            if ($today > $end_date)
                                $noofdays = count($dp_failed_arr);

                            $totelCountryCosts[$country]['dp_failed']['avg'] = $avg = $country_totelcost_dp_failed / $noofdays;

                            $reaming_day = Carbon::parse($end_date)->daysInMonth;
                            $reaming_day = $reaming_day - $noofdays;
                            $T_Mo_End = $country_totelcost_mo + ($avg * $reaming_day);
                            $totelCountryCosts[$country]['dp_failed']['t_mo_end'] = $T_Mo_End;
                        }
                    }

                    //for cost_campaign
                    foreach ($operater['cost_campaign']['dates'] as $cost_campaign_key => $cost_campaign_value) {

                        if ($key == 0) {
                            $cost_campaign_sum[$cost_campaign_key] = 0;
                            $totelCountryCosts[$country]['country'] = $operater['country'];
                        }
                        if (!array_key_exists($cost_campaign_key, $cost_campaign_sum)) {
                            $cost_campaign_sum[$cost_campaign_key] = 0;
                        }
                        $cost_campaign_sum[$cost_campaign_key] = $cost_campaign_sum[$cost_campaign_key] + (float)$cost_campaign_value['value'];
                        if (count($operaters) - 1 == $key) {

                            if ($flag == 5) {
                                $cost_campaignPrevious = $cost_campaign_sum[$cost_campaign_key];
                                $flag = 10;
                            }
                            if ($cost_campaign_key != Carbon::now()->format('Y-m'))
                                $country_totelcost_cost_campaign = $country_totelcost_cost_campaign + (float)$cost_campaign_sum[$cost_campaign_key];
                            $cost_campaign_arr[$cost_campaign_key] = ['value' => $cost_campaign_sum[$cost_campaign_key]];
                            $cost_campaignPrevious = $cost_campaign_sum[$cost_campaign_key];
                        }
                        if (count($cost_campaign_arr) > 1) {

                            $noofdays = count($cost_campaign_arr) - 1;
                            if ($today > $end_date)
                                $noofdays = count($cost_campaign_arr);

                            $totelCountryCosts[$country]['cost_campaign']['avg'] = $avg = $country_totelcost_cost_campaign / $noofdays;

                            $reaming_day = Carbon::parse($end_date)->daysInMonth;
                            $reaming_day = $reaming_day - $noofdays;
                            $T_Mo_End = $country_totelcost_mo + ($avg * $reaming_day);
                            $totelCountryCosts[$country]['cost_campaign']['t_mo_end'] = $T_Mo_End;
                        }
                    }


                    //for gros_rev_usd
                    foreach ($operater['gros_rev_usd']['dates'] as $gros_rev_usd_key => $gros_rev_usd_value) {

                        if ($key == 0) {
                            $gros_rev_usd_sum[$gros_rev_usd_key] = 0;
                            $totelCountryCosts[$country]['country'] = $operater['country'];
                        }
                        if (!array_key_exists($gros_rev_usd_key, $gros_rev_usd_sum)) {
                            $gros_rev_usd_sum[$gros_rev_usd_key] = 0;
                        }
                        $gros_rev_usd_sum[$gros_rev_usd_key] = $gros_rev_usd_sum[$gros_rev_usd_key] + (float)$gros_rev_usd_value['value'];
                        if (count($operaters) - 1 == $key) {

                            if ($flag == 5) {
                                $gros_rev_usdPrevious = $gros_rev_usd_sum[$gros_rev_usd_key];
                                $flag = 10;
                            }
                            if ($gros_rev_usd_key != Carbon::now()->format('Y-m'))
                                $country_totelcost_gros_rev_usd = $country_totelcost_gros_rev_usd + (float)$gros_rev_usd_sum[$gros_rev_usd_key];
                            $gros_rev_usd_arr[$gros_rev_usd_key] = ['value' => $gros_rev_usd_sum[$gros_rev_usd_key]];
                            $gros_rev_usdPrevious = $gros_rev_usd_sum[$gros_rev_usd_key];
                        }
                        if (count($gros_rev_usd_arr) > 1) {

                            $noofdays = count($gros_rev_usd_arr) - 1;
                            if ($today > $end_date)
                                $noofdays = count($gros_rev_usd_arr);

                            $totelCountryCosts[$country]['gros_rev_usd']['avg'] = $avg = $country_totelcost_gros_rev_usd / $noofdays;

                            $reaming_day = Carbon::parse($end_date)->daysInMonth;
                            $reaming_day = $reaming_day - $noofdays;
                            $T_Mo_End = $country_totelcost_mo + ($avg * $reaming_day);
                            $totelCountryCosts[$country]['gros_rev_usd']['t_mo_end'] = $T_Mo_End;
                        }
                    }

                    //for last_30_gros_rev
                    foreach ($operater['last_30_gros_rev']['dates'] as $last_30_gros_rev_key => $last_30_gros_rev_value) {

                        if ($key == 0) {
                            $last_30_gros_rev_sum[$last_30_gros_rev_key] = 0;
                            $totelCountryCosts[$country]['country'] = $operater['country'];
                        }
                        if (!array_key_exists($last_30_gros_rev_key, $last_30_gros_rev_sum)) {
                            $last_30_gros_rev_sum[$last_30_gros_rev_key] = 0;
                        }
                        $last_30_gros_rev_sum[$last_30_gros_rev_key] = $last_30_gros_rev_sum[$last_30_gros_rev_key] + (float)$last_30_gros_rev_value['value'];
                        if (count($operaters) - 1 == $key) {

                            if ($flag == 5) {
                                $last_30_gros_revPrevious = $last_30_gros_rev_sum[$last_30_gros_rev_key];
                                $flag = 10;
                            }
                            if ($last_30_gros_rev_key != Carbon::now()->format('Y-m'))
                                $country_totelcost_last_30_gros_rev = $country_totelcost_last_30_gros_rev + (float)$last_30_gros_rev_sum[$last_30_gros_rev_key];
                            $last_30_gros_rev_arr[$last_30_gros_rev_key] = ['value' => $last_30_gros_rev_sum[$last_30_gros_rev_key]];
                            $last_30_gros_revPrevious = $last_30_gros_rev_sum[$last_30_gros_rev_key];
                        }
                        if (count($last_30_gros_rev_arr) > 1) {

                            $noofdays = count($last_30_gros_rev_arr) - 1;
                            if ($today > $end_date)
                                $noofdays = count($last_30_gros_rev_arr);

                            $totelCountryCosts[$country]['last_30_gros_rev']['avg'] = $avg = $country_totelcost_last_30_gros_rev / $noofdays;

                            $reaming_day = Carbon::parse($end_date)->daysInMonth;
                            $reaming_day = $reaming_day - $noofdays;
                            $T_Mo_End = $country_totelcost_mo + ($avg * $reaming_day);
                            $totelCountryCosts[$country]['last_30_gros_rev']['t_mo_end'] = $T_Mo_End;
                        }
                    }

                    //for last_30_reg
                    foreach ($operater['last_30_reg']['dates'] as $last_30_reg_key => $last_30_reg_value) {

                        if ($key == 0) {
                            $last_30_reg_sum[$last_30_reg_key] = 0;
                            $totelCountryCosts[$country]['country'] = $operater['country'];
                        }
                        if (!array_key_exists($last_30_reg_key, $last_30_reg_sum)) {
                            $last_30_reg_sum[$last_30_reg_key] = 0;
                        }
                        $last_30_reg_sum[$last_30_reg_key] = $last_30_reg_sum[$last_30_reg_key] + (float)$last_30_reg_value['value'];
                        if (count($operaters) - 1 == $key) {

                            if ($flag == 5) {
                                $last_30_regPrevious = $last_30_reg_sum[$last_30_reg_key];
                                $flag = 10;
                            }
                            if ($last_30_reg_key != Carbon::now()->format('Y-m'))
                                $country_totelcost_last_30_reg = $country_totelcost_last_30_reg + (float)$last_30_reg_sum[$last_30_reg_key];
                            $last_30_reg_arr[$last_30_reg_key] = ['value' => $last_30_reg_sum[$last_30_reg_key]];
                            $last_30_regPrevious = $last_30_reg_sum[$last_30_reg_key];
                        }
                        if (count($last_30_reg_arr) > 1) {

                            $noofdays = count($last_30_reg_arr) - 1;
                            if ($today > $end_date)
                                $noofdays = count($last_30_reg_arr);

                            $totelCountryCosts[$country]['last_30_reg']['avg'] = $avg = $country_totelcost_last_30_reg / $noofdays;

                            $reaming_day = Carbon::parse($end_date)->daysInMonth;
                            $reaming_day = $reaming_day - $noofdays;
                            $T_Mo_End = $country_totelcost_mo + ($avg * $reaming_day);
                            $totelCountryCosts[$country]['last_30_reg']['t_mo_end'] = $T_Mo_End;
                        }
                    }

                    //for price_mo_cost
                    foreach ($operater['price_mo_cost']['dates'] as $price_mo_cost_key => $price_mo_cost_value) {

                        if ($key == 0) {
                            $price_mo_cost_sum[$price_mo_cost_key] = 0;
                            $totelCountryCosts[$country]['country'] = $operater['country'];
                        }
                        if (!array_key_exists($price_mo_cost_key, $price_mo_cost_sum)) {
                            $price_mo_cost_sum[$price_mo_cost_key] = 0;
                        }
                        $price_mo_cost_sum[$price_mo_cost_key] = $price_mo_cost_sum[$price_mo_cost_key] + (float)$price_mo_cost_value['value'];
                        if (count($operaters) - 1 == $key) {

                            if ($flag == 5) {
                                $price_mo_costPrevious = $price_mo_cost_sum[$price_mo_cost_key];
                                $flag = 10;
                            }
                            if ($price_mo_cost_key != Carbon::now()->format('Y-m'))
                                $country_totelcost_price_mo_cost = $country_totelcost_price_mo_cost + (float)$price_mo_cost_sum[$price_mo_cost_key];
                            $price_mo_cost_arr[$price_mo_cost_key] = ['value' => $price_mo_cost_sum[$price_mo_cost_key]];
                            $price_mo_costPrevious = $price_mo_cost_sum[$price_mo_cost_key];
                        }
                        if (count($price_mo_cost_arr) > 1) {

                            $noofdays = count($price_mo_cost_arr) - 1;
                            if ($today > $end_date)
                                $noofdays = count($price_mo_cost_arr);

                            $totelCountryCosts[$country]['price_mo_cost']['avg'] = $avg = $country_totelcost_price_mo_cost / $noofdays;

                            $reaming_day = Carbon::parse($end_date)->daysInMonth;
                            $reaming_day = $reaming_day - $noofdays;
                            $T_Mo_End = $country_totelcost_price_mo_cost + ($avg * $reaming_day);
                            $totelCountryCosts[$country]['price_mo_cost']['t_mo_end'] = $T_Mo_End;
                        }
                    }

                    //for price_mo_mo
                    foreach ($operater['price_mo_mo']['dates'] as $price_mo_mo_key => $price_mo_mo_value) {

                        if ($key == 0) {
                            $price_mo_mo_sum[$price_mo_mo_key] = 0;
                            $totelCountryCosts[$country]['country'] = $operater['country'];
                        }
                        if (!array_key_exists($price_mo_mo_key, $price_mo_mo_sum)) {
                            $price_mo_mo_sum[$price_mo_mo_key] = 0;
                        }
                        $price_mo_mo_sum[$price_mo_mo_key] = $price_mo_mo_sum[$price_mo_mo_key] + (float)$price_mo_mo_value['value'];
                        if (count($operaters) - 1 == $key) {

                            if ($flag == 5) {
                                $price_mo_moPrevious = $price_mo_mo_sum[$price_mo_mo_key];
                                $flag = 10;
                            }
                            if ($price_mo_mo_key != Carbon::now()->format('Y-m'))
                                $country_totelcost_price_mo_mo = $country_totelcost_price_mo_mo + (float)$price_mo_mo_sum[$price_mo_mo_key];
                            $price_mo_mo_arr[$price_mo_mo_key] = ['value' => $price_mo_mo_sum[$price_mo_mo_key]];
                            $price_mo_moPrevious = $price_mo_mo_sum[$price_mo_mo_key];
                        }
                        if (count($price_mo_mo_arr) > 1) {

                            $noofdays = count($price_mo_mo_arr) - 1;
                            if ($today > $end_date)
                                $noofdays = count($price_mo_mo_arr);

                            $totelCountryCosts[$country]['price_mo_mo']['avg'] = $avg = $country_totelcost_price_mo_mo / $noofdays;

                            $reaming_day = Carbon::parse($end_date)->daysInMonth;
                            $reaming_day = $reaming_day - $noofdays;
                            $T_Mo_End = $country_totelcost_price_mo_mo + ($avg * $reaming_day);
                            $totelCountryCosts[$country]['price_mo_mo']['t_mo_end'] = $T_Mo_End;
                        }
                    }

                    //for reg
                    foreach ($operater['reg']['dates'] as $reg_key => $reg_value) {

                        if ($key == 0) {
                            $reg_sum[$reg_key] = 0;
                            $totelCountryCosts[$country]['country'] = $operater['country'];
                        }
                        if (!array_key_exists($reg_key, $reg_sum)) {
                            $reg_sum[$reg_key] = 0;
                        }
                        $reg_sum[$reg_key] = $reg_sum[$reg_key] + (float)$reg_value['value'];
                        if (count($operaters) - 1 == $key) {

                            if ($flag == 5) {
                                $regPrevious = $reg_sum[$reg_key];
                                $flag = 10;
                            }
                            if ($reg_key != Carbon::now()->format('Y-m'))
                                $country_totelcost_reg = $country_totelcost_reg + (float)$reg_sum[$reg_key];
                            $reg_arr[$reg_key] = ['value' => $reg_sum[$reg_key]];
                            $regPrevious = $reg_sum[$reg_key];
                        }
                        if (count($reg_arr) > 1) {

                            $noofdays = count($reg_arr) - 1;
                            if ($today > $end_date)
                                $noofdays = count($reg_arr);

                            $totelCountryCosts[$country]['reg']['avg'] = $avg = $country_totelcost_reg / $noofdays;

                            $reaming_day = Carbon::parse($end_date)->daysInMonth;
                            $reaming_day = $reaming_day - $noofdays;
                            $T_Mo_End = $country_totelcost_mo + ($avg * $reaming_day);
                            $totelCountryCosts[$country]['reg']['t_mo_end'] = $T_Mo_End;
                        }
                    }

                    //for bill
                    foreach ($operater['bill']['dates'] as $bill_key => $bill_value) {

                        if ($key == 0) {
                            $bill_sum[$bill_key] = 0;
                            $totelCountryCosts[$country]['country'] = $operater['country'];
                        }
                        if (!array_key_exists($bill_key, $bill_sum)) {
                            $bill_sum[$bill_key] = 0;
                        }
                        $bill_sum[$bill_key] = $bill_sum[$bill_key] + (float)$bill_value['value'];
                        if ($bill_key != Carbon::now()->format('Y-m'))
                            $bill_sum[$bill_key] = $bill_sum[$bill_key] + (float)$bill_value['value'];
                        if (count($operaters) - 1 == $key) {

                            if ($flag == 5) {
                                $bilPrevious = $bill_sum[$bill_key];
                                $flag = 10;
                            }

                            $mt_success = $dp_success_arr[$bill_key]['value'];
                            $mt_failed = $dp_failed_arr[$bill_key]['value'];
                            $total_subscriber = $active_subs_arr[$bill_key]['value'];
                            $billing_rate = 0;

                            $sent = $mt_success + $mt_failed;

                            if ($sent == 0) {
                                if ($total_subscriber > 0) {
                                    $billing_rate = ($mt_success / $total_subscriber) * 100;
                                }
                            } else if ($mt_failed == 0) {
                                if ($total_subscriber > 0) {
                                    $billing_rate = ($mt_success / $total_subscriber) * 100;
                                }
                            } else {
                                if ($total_subscriber > 0) {
                                    $billing_rate = ($mt_success / $total_subscriber) * 100;
                                } else {
                                    $billing_rate = ($mt_success / $sent) * 100;
                                }
                            }

                            if ($bill_key != Carbon::now()->format('Y-m'))
                                $country_totelcost_bill = $country_totelcost_bill + (float)$billing_rate;
                            $bill_arr[$bill_key] = ['value' => $billing_rate];
                            $bilPrevious = $bill_sum[$bill_key];
                        }
                        if (count($bill_arr) > 1) {

                            $noofdays = count($bill_arr) - 1;
                            if ($today > $end_date)
                                $noofdays = count($bill_arr);

                            $totelCountryCosts[$country]['bill']['avg'] = $avg = $country_totelcost_bill / $noofdays;

                            $reaming_day = Carbon::parse($end_date)->daysInMonth;
                            $reaming_day = $reaming_day - $noofdays;
                            $T_Mo_End = $country_totelcost_bill + ($avg * $reaming_day);
                            $totelCountryCosts[$country]['bill']['t_mo_end'] = $T_Mo_End;
                        }
                    }

                    //for roi
                    foreach ($operater['roi']['dates'] as $roi_key => $roi_value) {

                        if ($key == 0) {
                            $roi_sum[$roi_key] = 0;
                            $totelCountryCosts[$country]['country'] = $operater['country'];
                        }
                        if (!array_key_exists($roi_key, $roi_sum)) {
                            $roi_sum[$roi_key] = 0;
                        }
                        $roi_sum[$roi_key] = $roi_sum[$roi_key] + (float)$roi_value['value'];
                        if ($roi_key != Carbon::now()->format('Y-m'))
                            $roi_sum[$roi_key] = $roi_sum[$roi_key] + (float)$roi_value['value'];
                        if (count($operaters) - 1 == $key) {

                            if ($flag == 5) {
                                $roiPrevious = $roi_sum[$roi_key];
                                $flag = 10;
                            }

                            if ($roi_key == date('Y-m')) {
                                $R1 = $last_30_gros_rev_arr[$roi_key]['value'];
                                $R2 = $last_30_reg_arr[$roi_key]['value'];
                                $R3 = $R2 + $active_subs_arr[$roi_key]['value'];
                                $R4 = $price_mo_cost_arr[$roi_key]['value'];
                                $R5 = $price_mo_mo_arr[$roi_key]['value'];

                                if ($R3 > 0) {
                                    $arpu_30 = $R1 / $R3;
                                }

                                if ($R5 > 0) {
                                    $price_mo = $R4 / $R5;
                                }

                                if ($arpu_30 > 0) {
                                    $roi = $price_mo / $arpu_30;
                                }
                                $country_totelcost_roi = $country_totelcost_roi + (float)$roi;
                                $roi_arr[$roi_key] = ['value' => $roi];
                                if ($roi_key == Carbon::now()->format('Y-m')) {

                                    $totelCountryCosts[$country]['roi']['avg'] = $roi;
                                }
                                $roiPrevious = $roi_sum[$roi_key];
                            } else {
                                $R1 = $gros_rev_usd_arr[$roi_key]['value'];
                                $R2 = $reg_arr[$roi_key]['value'];
                                $R3 = $R2 + $active_subs_arr[$roi_key]['value'];
                                $R4 = $cost_campaign_arr[$roi_key]['value'];
                                $R5 = $mo_arr[$roi_key]['value'];

                                if ($R3 > 0) {
                                    $arpu_30 = $R1 / $R3;
                                }

                                if ($R5 > 0) {
                                    $price_mo = $R4 / $R5;
                                }

                                if ($arpu_30 > 0) {
                                    $roi = $price_mo / $arpu_30;
                                }
                                $country_totelcost_roi = $country_totelcost_roi + (float)$roi;
                                $roi_arr[$roi_key] = ['value' => $roi];
                                $roiPrevious = $roi_sum[$roi_key];
                            }
                        }
                        if (count($roi_arr) > 1) {

                            $noofdays = count($roi_arr) - 1;
                            if ($today > $end_date)
                                $noofdays = count($roi_arr);

                            if ($roi_key == Carbon::now()->format('Y-m')) {

                                $totelCountryCosts[$country]['roi']['avg'] = $roi;
                            }


                            $reaming_day = Carbon::parse($end_date)->daysInMonth;
                            $reaming_day = $reaming_day - $noofdays;
                            $T_Mo_End = $country_totelcost_roi + ($avg * $reaming_day);
                            $totelCountryCosts[$country]['roi']['t_mo_end'] = $T_Mo_End;
                        }
                    }

                    //for renewal

                    foreach ($operater['renewal']['dates'] as $renewal_key => $renewal_value) {

                        if ($key == 0) {
                            $renewal_sum[$renewal_key] = 0;
                            $totelCountryCosts[$country]['country'] = $operater['country'];
                        }
                        if (!array_key_exists($renewal_key, $renewal_sum)) {
                            $renewal_sum[$renewal_key] = 0;
                        }
                        if ($renewal_key != Carbon::now()->format('Y-m-d'))
                            $renewal_sum[$renewal_key] = $renewal_sum[$renewal_key] + (float)$renewal_value['value'];
                        if (count($operaters) - 1 == $key) {

                            if ($flag == 5) {
                                $renewalPrevious = $renewal_sum[$renewal_key];
                                $flag = 10;
                            }
                            if ($renewal_key != Carbon::now()->format('Y-m')) {
                                $country_totelcost_renewal = $country_totelcost_renewal + (float)$renewal_sum[$renewal_key];
                                $count_renewal++;
                            }
                            if ($count_renewal > 0) {
                                $renewalPrevious = $renewal_sum[$renewal_key];
                                $renewal_avg = $country_totelcost_renewal / $count_renewal;
                            }
                            $renewal_t_mo_end = $renewal_avg * 12;
                            $renewal_arr[$renewal_key] = ['value' => $renewal_sum[$renewal_key]];
                        }
                    }
                }

                $totelCountryCosts[$country]['tur']['dates'] = $content_arr;
                $totelCountryCosts[$country]['tur']['total'] = $country_totelcost_campaign;


                $totelCountryCosts[$country]['mo']['dates'] = $mo_arr;
                $totelCountryCosts[$country]['mo']['total'] = $country_totelcost_mo;


                $totelCountryCosts[$country]['bill']['dates'] = $bill_arr;
                $totelCountryCosts[$country]['bill']['total'] = $country_totelcost_bill;


                $totelCountryCosts[$country]['roi']['dates'] = $roi_arr;
                $totelCountryCosts[$country]['roi']['total'] = $country_totelcost_roi;


                $totelCountryCosts[$country]['renewal']['dates'] = $renewal_arr;
                $totelCountryCosts[$country]['renewal']['total'] = $country_totelcost_renewal;
                $totelCountryCosts[$country]['renewal']['avg'] = $renewal_avg;
                $totelCountryCosts[$country]['renewal']['t_mo_end'] = $renewal_t_mo_end;
            }
            $no_of_days = $no_of_months;

            $AllCuntryGrosRev['month_string'] = $month;
            $date = Carbon::parse($end_date)->format('Y-m');

            return view('report.monitor_monthly_operator', compact('date', 'totelCountryCosts', 'AllCuntryGrosRev', 'no_of_days'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

     //analytic monitor countrywise

     public function ReportMonitorCountryWise(Request $request)
     {
         if (\Auth::user()->can('Monitor Operational')) {
             $monthly = 1;
             // get filtre request
             $CountryId = $req_CountryId = $request->country;
             $CompanyId = $req_CompanyId = $request->company;
             $filterOperator = $req_filterOperator = $request->operatorId;
             $Start_date = $req_Start_date = $request->to;
             $end_date =  $req_end_date = trim($request->from);

             /*If from is less than to*/
             if ($end_date <= $Start_date) {
                 $Start_date = $req_Start_date = trim($request->from);
                 $end_date =  $req_end_date = $request->to;
             }

             // dd(compact('Start_date','end_date'));

             $firstDayoftheyear = Carbon::now()->startOfYear()->format('Y-m-d');
             //$firstDayoftheyear = Carbon::now()->startOfMonth()->subMonths(24)->format('Y-m-d');
             $start_date = $firstDayoftheyear;
             $startColumnDateDisplay = $firstDayoftheyear;
             $end_date = Carbon::now()->format('Y-m-d');
             $month = Carbon::now()->format('F Y');
             /* filter Select box Display Data */
             $companys = Company::get();
             $today = Carbon::now()->format('Y-m-d');

             /* ENd */


             /* filter Search Section */

             $showAllOperator = true;

             if ($request->filled('to') && $request->filled('from')) {
                 $display_date_input = new Carbon($req_Start_date);
                 $start_date = $req_Start_date;
                 $startColumnDateDisplay = $display_date_input->format('Y-m-d');
                 $end_date = $req_end_date;
             }

             if ($request->filled('company') && $req_CompanyId != "allcompany"  && !$request->filled('operatorId')) {
                 $companies = Company::Find($req_CompanyId);
                 $Operators_company = array();
                 if (!empty($companies)) {
                     $Operators_company = $companies->company_operators->pluck('operator_id')->toArray();
                     $Operators = Operator::with('revenueshare')->Status(1)->GetOperatorByOperatorId($Operators_company)->get();
                 }
                 $showAllOperator = false;
             }

             if ($request->filled('country') && !$request->filled('operatorId')) {
                 $Operators = Operator::with('revenueshare')->Status(1)->GetOperatorByCountryId($req_CountryId)->get();
                 $showAllOperator = false;
             }

             if ($request->filled('company') && $request->filled('country') && !$request->filled('operatorId')) {
                 $data = [
                     'id' => $req_CountryId,
                     'company' => $req_CompanyId,
                 ];
                 $requestobj = new Request($data);
                 $ReportControllerobj = new ReportController;
                 $Operators = $ReportControllerobj->userFilterOperator($requestobj);
                 $showAllOperator = false;
             }

             if ($request->filled('operatorId')) {
                 $Operators = Operator::with('revenueshare')->Status(1)->GetOperatorByOperatorId($filterOperator)->get();
                 $showAllOperator = false;
             }

             if ($showAllOperator) {
                 $Operators = Operator::with('revenueshare')->Status(1)->get();
             }

             $Country = Country::all()->toArray();
             // $companys = Company::get();
             $countries = array();
             $sumemry = array();

             if (!empty($Country)) {
                 foreach ($Country as $CountryI) {
                     $countries[$CountryI['id']] = $CountryI;
                     $countrys[$CountryI['id']] = [];
                 }
             }

             $arrayOperatorsIds = $Operators->pluck('id_operator')->toArray();
             $datesIndividual = Utility::getRangeDates($startColumnDateDisplay, $end_date);
             $no_of_months = Utility::getRangeMonthsNo($datesIndividual);

             $monthList = array();

             foreach ($no_of_months as $key => $no_of_month) {
                 $month_key = $no_of_month['date'];
                 $monthList[] = $month_key;
             }

             /* Admin Access All operator and Services */


             $QueryMonthlyReports = PnlSummeryMonth::filteroperator($arrayOperatorsIds)->Months($monthList);

             $user = Auth::user();
             $user_id = $user->id;
             $user_type = $user->type;
             $allowAllOperator = $user->WhowAccessAlOperator($user_type);
             $data = new PnlMonthlyReportDetailsController;
             if ($allowAllOperator) {
                 $QueryMonthlyReports = $QueryMonthlyReports->User(0);
             } else {
                 $QueryMonthlyReports = $QueryMonthlyReports->User(0);
                 $QueryMonthlyUserReports = MonthlyReportSummery::filteroperator($arrayOperatorsIds)->Months($monthList);
                 $QueryMonthlyUserReports = $QueryMonthlyUserReports->User($user_id);
                 $allMonthlyUserData = $QueryMonthlyUserReports->get()->toArray();

                 $reportsMonthUserData = $data->rearrangeOperatorMonthUser($allMonthlyUserData);
             }

             $allMonthlyData = $QueryMonthlyReports->get()->toArray();

             $reportsMonthData = $data->rearrangeOperatorMonth($allMonthlyData);
             $monthdata = $reportsMonthData;

             $start_date_roi = Carbon::yesterday()->subDays(30)->format('Y-m-d');
             $end_date_roi = Carbon::yesterday()->format('Y-m-d');
             $date_roi = Carbon::now()->format('Y-m-d');
             $reports = ReportsPnlsOperatorSummarizes::filterOperator($arrayOperatorsIds)
                 ->OperatorNotNull()
                 ->filterDateRange($start_date_roi, $end_date_roi)
                 ->SumOfRoiDataOperator()
                 ->get()
                 ->toArray();

             $active_subs = ReportsPnlsOperatorSummarizes::filteroperator($arrayOperatorsIds)
                 ->where(['date' => $date_roi])
                 ->TotalOperator()
                 ->get()
                 ->toArray();


             $reportsByOperatorIDs = $data->getReportsByOperatorID($reports);
             $active_subsByOperatorIDs = $data->getReportsByOperatorID($active_subs);

             $totelCountryCosts = [];
             if (!empty($Operators)) {
                 foreach ($Operators as $operator) {
                     $id_operator = $operator->id_operator;
                     // dd($id_operator);
                     $tmpOperators = array();
                     $tmpOperators['operator'] = $operator;
                     if (!isset($reportsMonthData[$id_operator])) {
                         continue;
                     }

                     if (isset($operator->revenueshare)) {
                         $merchant_revenue_share = $operator->revenueshare->merchant_revenue_share;
                     } else {
                         $merchant_revenue_share = 100;
                     }

                     $tmpOperators['data'] = $monthdata;
                     //dd($tmpOperators);
                     $country_id  = $operator->country_id;
                     $contain_id = Arr::exists($countries, $country_id);
                     $OperatorCountry = array();

                     if ($contain_id) {
                         $tmpOperators['country'] = $countries[$country_id];
                         $OperatorCountry = $countries[$country_id];
                     }
                     if (isset($reportsMonthUserData)  && !empty($reportsMonthUserData)) {
                         foreach ($reportsMonthUserData as $key1 => $value1) {
                             if ($key1 == $id_operator) {
                                 foreach ($value1 as $key2 => $value2) {
                                     $monthdata[$id_operator][$key2]['rev'] = $value2['gros_rev'];
                                     $monthdata[$id_operator][$key2]['rev_usd'] = $value2['gros_rev'] * $OperatorCountry['usd'];
                                     $monthdata[$id_operator][$key2]['lshare'] = $value2['gros_rev'] * ($merchant_revenue_share / 100);
                                     $monthdata[$id_operator][$key2]['share'] = $value2['gros_rev'] * $OperatorCountry['usd'] * ($merchant_revenue_share / 100);
                                 }
                             }
                         }
                     }
                     $reportsColumnData = $data->getPNLReportsDateWise($operator, $no_of_months, $monthdata, $OperatorCountry, $reportsByOperatorIDs, $active_subsByOperatorIDs);

                     $tmpOperators['month_string'] = $month;

                     $total_avg_t = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['end_user_rev_usd'], $startColumnDateDisplay, $end_date, $no_of_months);
                     $tmpOperators['tur']['dates'] = $reportsColumnData['end_user_rev_usd'];
                     $tmpOperators['tur']['total'] = $total_avg_t['sum'];
                     $tmpOperators['tur']['t_mo_end'] = $total_avg_t['T_Mo_End'];
                     $tmpOperators['tur']['avg'] = $total_avg_t['avg'];

                     $total_avg_t_bill = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['bill'], $startColumnDateDisplay, $end_date);
                     $tmpOperators['bill']['dates'] = $reportsColumnData['bill'];
                     $tmpOperators['bill']['total'] = $total_avg_t_bill['sum'];
                     $tmpOperators['bill']['t_mo_end'] = $total_avg_t_bill['T_Mo_End'];
                     $tmpOperators['bill']['avg'] = $total_avg_t_bill['avg'];

                     $total_avg_mo = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['mo'], $startColumnDateDisplay, $end_date);
                     $tmpOperators['mo']['dates'] = $reportsColumnData['mo'];
                     $tmpOperators['mo']['total'] = $total_avg_mo['sum'];
                     $tmpOperators['mo']['t_mo_end'] = $total_avg_mo['T_Mo_End'];
                     $tmpOperators['mo']['avg'] = $total_avg_mo['avg'];

                     $total_avg_roi = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['roi'], $startColumnDateDisplay, $end_date);
                     $tmpOperators['roi']['dates'] = $reportsColumnData['roi'];
                     $tmpOperators['roi']['total'] = $total_avg_roi['sum'];
                     $tmpOperators['roi']['t_mo_end'] = $total_avg_roi['T_Mo_End'];
                     $tmpOperators['roi']['avg'] = $total_avg_roi['avg'];

                     $total_avg_renewal = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['renewal'], $startColumnDateDisplay, $end_date);
                     $tmpOperators['renewal']['dates'] = $reportsColumnData['renewal'];
                     $tmpOperators['renewal']['total'] = $total_avg_renewal['sum'];
                     $tmpOperators['renewal']['t_mo_end'] = $total_avg_renewal['T_Mo_End'];
                     $tmpOperators['renewal']['avg'] = $total_avg_renewal['avg'];

                     $total_avg_dp_success = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['dp_success'], $startColumnDateDisplay, $end_date);

                     $tmpOperators['dp_success']['dates'] = $reportsColumnData['dp_success'];
                     $tmpOperators['dp_success']['total'] = $total_avg_dp_success['sum'];
                     $tmpOperators['dp_success']['t_mo_end'] = $total_avg_dp_success['T_Mo_End'];
                     $tmpOperators['dp_success']['avg'] = $total_avg_dp_success['avg'];


                     $total_avg_dp_failed = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['dp_failed'], $startColumnDateDisplay, $end_date);

                     $tmpOperators['dp_failed']['dates'] = $reportsColumnData['dp_failed'];
                     $tmpOperators['dp_failed']['total'] = $total_avg_dp_failed['sum'];
                     $tmpOperators['dp_failed']['t_mo_end'] = $total_avg_dp_failed['T_Mo_End'];
                     $tmpOperators['dp_failed']['avg'] = $total_avg_dp_failed['avg'];

                     $total_avg_active_subs = UtilityReports::calculateTotalSubscribe($operator, $reportsColumnData['active_subs'], $startColumnDateDisplay, $end_date);

                     $tmpOperators['active_subs']['dates'] = $reportsColumnData['active_subs'];
                     $tmpOperators['active_subs']['total'] = $total_avg_active_subs['sum'];
                     $tmpOperators['active_subs']['t_mo_end'] = $total_avg_active_subs['T_Mo_End'];
                     $tmpOperators['active_subs']['avg'] = $total_avg_active_subs['avg'];

                     $total_avg_cost_campaign = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['cost_campaign'], $startColumnDateDisplay, $end_date);

                     $tmpOperators['cost_campaign']['dates'] = $reportsColumnData['cost_campaign'];
                     $tmpOperators['cost_campaign']['total'] = $total_avg_cost_campaign['sum'];
                     $tmpOperators['cost_campaign']['t_mo_end'] = $total_avg_cost_campaign['T_Mo_End'];
                     $tmpOperators['cost_campaign']['avg'] = $total_avg_cost_campaign['avg'];

                     $total_avg_gros_rev_usd = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['gros_rev_usd'], $startColumnDateDisplay, $end_date);

                     $tmpOperators['gros_rev_usd']['dates'] = $reportsColumnData['gros_rev_usd'];
                     $tmpOperators['gros_rev_usd']['total'] = $total_avg_gros_rev_usd['sum'];
                     $tmpOperators['gros_rev_usd']['t_mo_end'] = $total_avg_gros_rev_usd['T_Mo_End'];
                     $tmpOperators['gros_rev_usd']['avg'] = $total_avg_gros_rev_usd['avg'];

                     $total_avg_last_30_gros_rev = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['last_30_gros_rev'], $startColumnDateDisplay, $end_date);

                     $tmpOperators['last_30_gros_rev']['dates'] = $reportsColumnData['last_30_gros_rev'];
                     $tmpOperators['last_30_gros_rev']['total'] = $total_avg_last_30_gros_rev['sum'];
                     $tmpOperators['last_30_gros_rev']['t_mo_end'] = $total_avg_last_30_gros_rev['T_Mo_End'];
                     $tmpOperators['last_30_gros_rev']['avg'] = $total_avg_last_30_gros_rev['avg'];

                     $total_avg_last_30_reg = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['last_30_reg'], $startColumnDateDisplay, $end_date);

                     $tmpOperators['last_30_reg']['dates'] = $reportsColumnData['last_30_reg'];
                     $tmpOperators['last_30_reg']['total'] = $total_avg_last_30_reg['sum'];
                     $tmpOperators['last_30_reg']['t_mo_end'] = $total_avg_last_30_reg['T_Mo_End'];
                     $tmpOperators['last_30_reg']['avg'] = $total_avg_last_30_reg['avg'];

                     $total_avg_price_mo_cost = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['price_mo_cost'], $startColumnDateDisplay, $end_date);

                     $tmpOperators['price_mo_cost']['dates'] = $reportsColumnData['price_mo_cost'];
                     $tmpOperators['price_mo_cost']['total'] = $total_avg_price_mo_cost['sum'];
                     $tmpOperators['price_mo_cost']['t_mo_end'] = $total_avg_price_mo_cost['T_Mo_End'];
                     $tmpOperators['price_mo_cost']['avg'] = $total_avg_price_mo_cost['avg'];

                     $total_avg_price_mo_mo = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['price_mo_mo'], $startColumnDateDisplay, $end_date);

                     $tmpOperators['price_mo_mo']['dates'] = $reportsColumnData['price_mo_mo'];
                     $tmpOperators['price_mo_mo']['total'] = $total_avg_price_mo_mo['sum'];
                     $tmpOperators['price_mo_mo']['t_mo_end'] = $total_avg_price_mo_mo['T_Mo_End'];
                     $tmpOperators['price_mo_mo']['avg'] = $total_avg_price_mo_mo['avg'];

                     $total_avg_gros_rev_usd = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['reg'], $startColumnDateDisplay, $end_date);

                     $tmpOperators['reg']['dates'] = $reportsColumnData['reg'];
                     // $totelCountryCosts[] = $tmpOperators;
                     array_push($countrys[$country_id], $tmpOperators);
                 }
             }

             $countrys = array_filter($countrys);

             $totelCountryCosts = [];
             foreach ($countrys as $country => $operaters) {

                 $content_arr = [];
                 $bill_arr = [];
                 $mo_arr = [];
                 $roi_arr = [];
                 $renewal_arr = [];
                 $gros_rev_usd_arr = [];
                 $last_30_gros_rev_arr = [];
                 $last_30_reg_arr = [];
                 $price_mo_cost_arr = [];
                 $price_mo_mo_arr = [];
                 $reg_arr = [];
                 $dp_success_arr = [];
                 $dp_failed_arr = [];
                 $cost_campaign_arr = [];
                 $active_subs_arr = [];
                 $totelCountryCosts[$country]['operator'] = $operaters;
                 $class = '';
                 $cost_campaignPrevious = 0;
                 $flag = 5;
                 $country_totelcost_campaign = 0;
                 $country_totelcost_mo = 0;
                 $country_totelcost_bill = 0;
                 $country_totelcost_roi = 0;
                 $country_totelcost_renewal = 0;
                 $country_totelcost_active_subs = 0;
                 $country_totelcost_dp_success = 0;
                 $country_totelcost_dp_failed = 0;
                 $country_totelcost_cost_campaign = 0;
                 $country_totelcost_gros_rev_usd = 0;
                 $country_totelcost_reg = 0;
                 $country_totelcost_last_30_gros_rev = 0;
                 $country_totelcost_last_30_reg = 0;
                 $country_totelcost_price_mo_cost = 0;
                 $country_totelcost_price_mo_mo = 0;
                 $count_renewal = 0;
                 $renewal_avg = 0;
                 $content_sum = [];


                 foreach ($operaters as $key => $operater) {

                     //for revenue
                     foreach ($operater['tur']['dates'] as $content_key => $content_value) {

                         if ($key == 0) {
                             $content_sum[$content_key] = 0;
                             $totelCountryCosts[$country]['country'] = $operater['country'];
                         }
                         if (!array_key_exists($content_key, $content_sum)) {
                             $content_sum[$content_key] = 0;
                         }
                         $content_sum[$content_key] = $content_sum[$content_key] + (float)$content_value['value'];


                         if (count($operaters) - 1 == $key) {

                             if ($flag == 5) {
                                 $cost_campaignPrevious = $content_sum[$content_key];
                                 $flag = 10;
                             }
                             if ($content_key != Carbon::now()->format('Y-m'))
                                 $country_totelcost_campaign = $country_totelcost_campaign + (float)$content_sum[$content_key];
                             $content_arr[$content_key] = ['value' => $content_sum[$content_key]];
                             $cost_campaignPrevious = $content_sum[$content_key];
                         }
                         if (count($content_arr) > 1) {
                             $noofdays = count($content_arr) - 1;
                             if ($today > $end_date)
                                 $noofdays = count($content_arr);

                             $totelCountryCosts[$country]['tur']['avg'] = $avg = $country_totelcost_campaign / $noofdays;

                             $reaming_day = Carbon::parse($end_date)->daysInMonth;
                             $reaming_day = $reaming_day - $noofdays;
                             $T_Mo_End = $avg * 12;
                             $totelCountryCosts[$country]['tur']['t_mo_end'] = $T_Mo_End;
                         }
                     }

                     //for mo
                     foreach ($operater['mo']['dates'] as $mo_key => $mo_value) {

                         if ($key == 0) {
                             $mo_sum[$mo_key] = 0;
                             $totelCountryCosts[$country]['country'] = $operater['country'];
                         }
                         if (!array_key_exists($mo_key, $mo_sum)) {
                             $mo_sum[$mo_key] = 0;
                         }
                         $mo_sum[$mo_key] = $mo_sum[$mo_key] + (float)$mo_value['value'];
                         if (count($operaters) - 1 == $key) {

                             if ($flag == 5) {
                                 $moPrevious = $mo_sum[$mo_key];
                                 $flag = 10;
                             }
                             if ($mo_key != Carbon::now()->format('Y-m'))
                                 $country_totelcost_mo = $country_totelcost_mo + (float)$mo_sum[$mo_key];
                             $mo_arr[$mo_key] = ['value' => $mo_sum[$mo_key]];
                             $moPrevious = $mo_sum[$mo_key];
                         }
                         if (count($mo_arr) > 1) {

                             $noofdays = count($mo_arr) - 1;
                             if ($today > $end_date)
                                 $noofdays = count($mo_arr);

                             $totelCountryCosts[$country]['mo']['avg'] = $avg = $country_totelcost_mo / $noofdays;

                             $reaming_day = Carbon::parse($end_date)->daysInMonth;
                             $reaming_day = $reaming_day - $noofdays;
                             $T_Mo_End = $avg * 12;
                             $totelCountryCosts[$country]['mo']['t_mo_end'] = $T_Mo_End;
                         }
                     }

                     //for active subs
                     foreach ($operater['active_subs']['dates'] as $active_subs_key => $active_subs_value) {

                         if ($key == 0) {
                             $active_subs_sum[$active_subs_key] = 0;
                             $totelCountryCosts[$country]['country'] = $operater['country'];
                         }
                         if (!array_key_exists($active_subs_key, $active_subs_sum)) {
                             $active_subs_sum[$active_subs_key] = 0;
                         }
                         $active_subs_sum[$active_subs_key] = $active_subs_sum[$active_subs_key] + (float)$active_subs_value['value'];
                         if (count($operaters) - 1 == $key) {

                             if ($flag == 5) {
                                 $active_subsPrevious = $active_subs_sum[$active_subs_key];
                                 $flag = 10;
                             }
                             if ($active_subs_key != Carbon::now()->format('Y-m'))
                                 $country_totelcost_active_subs = $country_totelcost_active_subs + (float)$active_subs_sum[$active_subs_key];
                             $active_subs_arr[$active_subs_key] = ['value' => $active_subs_sum[$active_subs_key]];
                             $active_subsPrevious = $active_subs_sum[$active_subs_key];
                         }
                         if (count($active_subs_arr) > 1) {

                             $noofdays = count($active_subs_arr) - 1;
                             if ($today > $end_date)
                                 $noofdays = count($active_subs_arr);

                             $totelCountryCosts[$country]['active_subs']['avg'] = $avg = $country_totelcost_active_subs / $noofdays;

                             $reaming_day = Carbon::parse($end_date)->daysInMonth;
                             $reaming_day = $reaming_day - $noofdays;
                             $T_Mo_End = $country_totelcost_mo + ($avg * $reaming_day);
                             $totelCountryCosts[$country]['active_subs']['t_mo_end'] = $T_Mo_End;
                         }
                     }

                     //for dp_success
                     foreach ($operater['dp_success']['dates'] as $dp_success_key => $dp_success_value) {

                         if ($key == 0) {
                             $dp_success_sum[$dp_success_key] = 0;
                             $totelCountryCosts[$country]['country'] = $operater['country'];
                         }
                         if (!array_key_exists($dp_success_key, $dp_success_sum)) {
                             $dp_success_sum[$dp_success_key] = 0;
                         }
                         $dp_success_sum[$dp_success_key] = $dp_success_sum[$dp_success_key] + (float)$dp_success_value['value'];
                         if (count($operaters) - 1 == $key) {

                             if ($flag == 5) {
                                 $dp_successPrevious = $dp_success_sum[$dp_success_key];
                                 $flag = 10;
                             }
                             if ($dp_success_key != Carbon::now()->format('Y-m'))
                                 $country_totelcost_dp_success = $country_totelcost_dp_success + (float)$dp_success_sum[$dp_success_key];
                             $dp_success_arr[$dp_success_key] = ['value' => $dp_success_sum[$dp_success_key]];
                             $dp_successPrevious = $dp_success_sum[$dp_success_key];
                         }
                         if (count($dp_success_arr) > 1) {

                             $noofdays = count($dp_success_arr) - 1;
                             if ($today > $end_date)
                                 $noofdays = count($dp_success_arr);

                             $totelCountryCosts[$country]['dp_success']['avg'] = $avg = $country_totelcost_dp_success / $noofdays;

                             $reaming_day = Carbon::parse($end_date)->daysInMonth;
                             $reaming_day = $reaming_day - $noofdays;
                             $T_Mo_End = $country_totelcost_mo + ($avg * $reaming_day);
                             $totelCountryCosts[$country]['dp_success']['t_mo_end'] = $T_Mo_End;
                         }
                     }

                     //for dp_failed
                     foreach ($operater['dp_failed']['dates'] as $dp_failed_key => $dp_failed_value) {

                         if ($key == 0) {
                             $dp_failed_sum[$dp_failed_key] = 0;
                             $totelCountryCosts[$country]['country'] = $operater['country'];
                         }
                         if (!array_key_exists($dp_failed_key, $dp_failed_sum)) {
                             $dp_failed_sum[$dp_failed_key] = 0;
                         }
                         $dp_failed_sum[$dp_failed_key] = $dp_failed_sum[$dp_failed_key] + (float)$dp_failed_value['value'];
                         if (count($operaters) - 1 == $key) {

                             if ($flag == 5) {
                                 $dp_failedPrevious = $dp_failed_sum[$dp_failed_key];
                                 $flag = 10;
                             }
                             if ($dp_failed_key != Carbon::now()->format('Y-m'))
                                 $country_totelcost_dp_failed = $country_totelcost_dp_failed + (float)$dp_failed_sum[$dp_failed_key];
                             $dp_failed_arr[$dp_failed_key] = ['value' => $dp_failed_sum[$dp_failed_key]];
                             $dp_failedPrevious = $dp_failed_sum[$dp_failed_key];
                         }
                         if (count($dp_failed_arr) > 1) {

                             $noofdays = count($dp_failed_arr) - 1;
                             if ($today > $end_date)
                                 $noofdays = count($dp_failed_arr);

                             $totelCountryCosts[$country]['dp_failed']['avg'] = $avg = $country_totelcost_dp_failed / $noofdays;

                             $reaming_day = Carbon::parse($end_date)->daysInMonth;
                             $reaming_day = $reaming_day - $noofdays;
                             $T_Mo_End = $country_totelcost_mo + ($avg * $reaming_day);
                             $totelCountryCosts[$country]['dp_failed']['t_mo_end'] = $T_Mo_End;
                         }
                     }

                     //for cost_campaign
                     foreach ($operater['cost_campaign']['dates'] as $cost_campaign_key => $cost_campaign_value) {

                         if ($key == 0) {
                             $cost_campaign_sum[$cost_campaign_key] = 0;
                             $totelCountryCosts[$country]['country'] = $operater['country'];
                         }
                         if (!array_key_exists($cost_campaign_key, $cost_campaign_sum)) {
                             $cost_campaign_sum[$cost_campaign_key] = 0;
                         }
                         $cost_campaign_sum[$cost_campaign_key] = $cost_campaign_sum[$cost_campaign_key] + (float)$cost_campaign_value['value'];
                         if (count($operaters) - 1 == $key) {

                             if ($flag == 5) {
                                 $cost_campaignPrevious = $cost_campaign_sum[$cost_campaign_key];
                                 $flag = 10;
                             }
                             if ($cost_campaign_key != Carbon::now()->format('Y-m'))
                                 $country_totelcost_cost_campaign = $country_totelcost_cost_campaign + (float)$cost_campaign_sum[$cost_campaign_key];
                             $cost_campaign_arr[$cost_campaign_key] = ['value' => $cost_campaign_sum[$cost_campaign_key]];
                             $cost_campaignPrevious = $cost_campaign_sum[$cost_campaign_key];
                         }
                         if (count($cost_campaign_arr) > 1) {

                             $noofdays = count($cost_campaign_arr) - 1;
                             if ($today > $end_date)
                                 $noofdays = count($cost_campaign_arr);

                             $totelCountryCosts[$country]['cost_campaign']['avg'] = $avg = $country_totelcost_cost_campaign / $noofdays;

                             $reaming_day = Carbon::parse($end_date)->daysInMonth;
                             $reaming_day = $reaming_day - $noofdays;
                             $T_Mo_End = $country_totelcost_mo + ($avg * $reaming_day);
                             $totelCountryCosts[$country]['cost_campaign']['t_mo_end'] = $T_Mo_End;
                         }
                     }


                     //for gros_rev_usd
                     foreach ($operater['gros_rev_usd']['dates'] as $gros_rev_usd_key => $gros_rev_usd_value) {

                         if ($key == 0) {
                             $gros_rev_usd_sum[$gros_rev_usd_key] = 0;
                             $totelCountryCosts[$country]['country'] = $operater['country'];
                         }
                         if (!array_key_exists($gros_rev_usd_key, $gros_rev_usd_sum)) {
                             $gros_rev_usd_sum[$gros_rev_usd_key] = 0;
                         }
                         $gros_rev_usd_sum[$gros_rev_usd_key] = $gros_rev_usd_sum[$gros_rev_usd_key] + (float)$gros_rev_usd_value['value'];
                         if (count($operaters) - 1 == $key) {

                             if ($flag == 5) {
                                 $gros_rev_usdPrevious = $gros_rev_usd_sum[$gros_rev_usd_key];
                                 $flag = 10;
                             }
                             if ($gros_rev_usd_key != Carbon::now()->format('Y-m'))
                                 $country_totelcost_gros_rev_usd = $country_totelcost_gros_rev_usd + (float)$gros_rev_usd_sum[$gros_rev_usd_key];
                             $gros_rev_usd_arr[$gros_rev_usd_key] = ['value' => $gros_rev_usd_sum[$gros_rev_usd_key]];
                             $gros_rev_usdPrevious = $gros_rev_usd_sum[$gros_rev_usd_key];
                         }
                         if (count($gros_rev_usd_arr) > 1) {

                             $noofdays = count($gros_rev_usd_arr) - 1;
                             if ($today > $end_date)
                                 $noofdays = count($gros_rev_usd_arr);

                             $totelCountryCosts[$country]['gros_rev_usd']['avg'] = $avg = $country_totelcost_gros_rev_usd / $noofdays;

                             $reaming_day = Carbon::parse($end_date)->daysInMonth;
                             $reaming_day = $reaming_day - $noofdays;
                             $T_Mo_End = $country_totelcost_mo + ($avg * $reaming_day);
                             $totelCountryCosts[$country]['gros_rev_usd']['t_mo_end'] = $T_Mo_End;
                         }
                     }

                     //for last_30_gros_rev
                     foreach ($operater['last_30_gros_rev']['dates'] as $last_30_gros_rev_key => $last_30_gros_rev_value) {

                         if ($key == 0) {
                             $last_30_gros_rev_sum[$last_30_gros_rev_key] = 0;
                             $totelCountryCosts[$country]['country'] = $operater['country'];
                         }
                         if (!array_key_exists($last_30_gros_rev_key, $last_30_gros_rev_sum)) {
                             $last_30_gros_rev_sum[$last_30_gros_rev_key] = 0;
                         }
                         $last_30_gros_rev_sum[$last_30_gros_rev_key] = $last_30_gros_rev_sum[$last_30_gros_rev_key] + (float)$last_30_gros_rev_value['value'];
                         if (count($operaters) - 1 == $key) {

                             if ($flag == 5) {
                                 $last_30_gros_revPrevious = $last_30_gros_rev_sum[$last_30_gros_rev_key];
                                 $flag = 10;
                             }
                             if ($last_30_gros_rev_key != Carbon::now()->format('Y-m'))
                                 $country_totelcost_last_30_gros_rev = $country_totelcost_last_30_gros_rev + (float)$last_30_gros_rev_sum[$last_30_gros_rev_key];
                             $last_30_gros_rev_arr[$last_30_gros_rev_key] = ['value' => $last_30_gros_rev_sum[$last_30_gros_rev_key]];
                             $last_30_gros_revPrevious = $last_30_gros_rev_sum[$last_30_gros_rev_key];
                         }
                         if (count($last_30_gros_rev_arr) > 1) {

                             $noofdays = count($last_30_gros_rev_arr) - 1;
                             if ($today > $end_date)
                                 $noofdays = count($last_30_gros_rev_arr);

                             $totelCountryCosts[$country]['last_30_gros_rev']['avg'] = $avg = $country_totelcost_last_30_gros_rev / $noofdays;

                             $reaming_day = Carbon::parse($end_date)->daysInMonth;
                             $reaming_day = $reaming_day - $noofdays;
                             $T_Mo_End = $country_totelcost_mo + ($avg * $reaming_day);
                             $totelCountryCosts[$country]['last_30_gros_rev']['t_mo_end'] = $T_Mo_End;
                         }
                     }

                     //for last_30_reg
                     foreach ($operater['last_30_reg']['dates'] as $last_30_reg_key => $last_30_reg_value) {

                         if ($key == 0) {
                             $last_30_reg_sum[$last_30_reg_key] = 0;
                             $totelCountryCosts[$country]['country'] = $operater['country'];
                         }
                         if (!array_key_exists($last_30_reg_key, $last_30_reg_sum)) {
                             $last_30_reg_sum[$last_30_reg_key] = 0;
                         }
                         $last_30_reg_sum[$last_30_reg_key] = $last_30_reg_sum[$last_30_reg_key] + (float)$last_30_reg_value['value'];
                         if (count($operaters) - 1 == $key) {

                             if ($flag == 5) {
                                 $last_30_regPrevious = $last_30_reg_sum[$last_30_reg_key];
                                 $flag = 10;
                             }
                             if ($last_30_reg_key != Carbon::now()->format('Y-m'))
                                 $country_totelcost_last_30_reg = $country_totelcost_last_30_reg + (float)$last_30_reg_sum[$last_30_reg_key];
                             $last_30_reg_arr[$last_30_reg_key] = ['value' => $last_30_reg_sum[$last_30_reg_key]];
                             $last_30_regPrevious = $last_30_reg_sum[$last_30_reg_key];
                         }
                         if (count($last_30_reg_arr) > 1) {

                             $noofdays = count($last_30_reg_arr) - 1;
                             if ($today > $end_date)
                                 $noofdays = count($last_30_reg_arr);

                             $totelCountryCosts[$country]['last_30_reg']['avg'] = $avg = $country_totelcost_last_30_reg / $noofdays;

                             $reaming_day = Carbon::parse($end_date)->daysInMonth;
                             $reaming_day = $reaming_day - $noofdays;
                             $T_Mo_End = $country_totelcost_mo + ($avg * $reaming_day);
                             $totelCountryCosts[$country]['last_30_reg']['t_mo_end'] = $T_Mo_End;
                         }
                     }

                     //for price_mo_cost
                     foreach ($operater['price_mo_cost']['dates'] as $price_mo_cost_key => $price_mo_cost_value) {

                         if ($key == 0) {
                             $price_mo_cost_sum[$price_mo_cost_key] = 0;
                             $totelCountryCosts[$country]['country'] = $operater['country'];
                         }
                         if (!array_key_exists($price_mo_cost_key, $price_mo_cost_sum)) {
                             $price_mo_cost_sum[$price_mo_cost_key] = 0;
                         }
                         $price_mo_cost_sum[$price_mo_cost_key] = $price_mo_cost_sum[$price_mo_cost_key] + (float)$price_mo_cost_value['value'];
                         if (count($operaters) - 1 == $key) {

                             if ($flag == 5) {
                                 $price_mo_costPrevious = $price_mo_cost_sum[$price_mo_cost_key];
                                 $flag = 10;
                             }
                             if ($price_mo_cost_key != Carbon::now()->format('Y-m'))
                                 $country_totelcost_price_mo_cost = $country_totelcost_price_mo_cost + (float)$price_mo_cost_sum[$price_mo_cost_key];
                             $price_mo_cost_arr[$price_mo_cost_key] = ['value' => $price_mo_cost_sum[$price_mo_cost_key]];
                             $price_mo_costPrevious = $price_mo_cost_sum[$price_mo_cost_key];
                         }
                         if (count($price_mo_cost_arr) > 1) {

                             $noofdays = count($price_mo_cost_arr) - 1;
                             if ($today > $end_date)
                                 $noofdays = count($price_mo_cost_arr);

                             $totelCountryCosts[$country]['price_mo_cost']['avg'] = $avg = $country_totelcost_price_mo_cost / $noofdays;

                             $reaming_day = Carbon::parse($end_date)->daysInMonth;
                             $reaming_day = $reaming_day - $noofdays;
                             $T_Mo_End = $country_totelcost_price_mo_cost + ($avg * $reaming_day);
                             $totelCountryCosts[$country]['price_mo_cost']['t_mo_end'] = $T_Mo_End;
                         }
                     }

                     //for price_mo_mo
                     foreach ($operater['price_mo_mo']['dates'] as $price_mo_mo_key => $price_mo_mo_value) {

                         if ($key == 0) {
                             $price_mo_mo_sum[$price_mo_mo_key] = 0;
                             $totelCountryCosts[$country]['country'] = $operater['country'];
                         }
                         if (!array_key_exists($price_mo_mo_key, $price_mo_mo_sum)) {
                             $price_mo_mo_sum[$price_mo_mo_key] = 0;
                         }
                         $price_mo_mo_sum[$price_mo_mo_key] = $price_mo_mo_sum[$price_mo_mo_key] + (float)$price_mo_mo_value['value'];
                         if (count($operaters) - 1 == $key) {

                             if ($flag == 5) {
                                 $price_mo_moPrevious = $price_mo_mo_sum[$price_mo_mo_key];
                                 $flag = 10;
                             }
                             if ($price_mo_mo_key != Carbon::now()->format('Y-m'))
                                 $country_totelcost_price_mo_mo = $country_totelcost_price_mo_mo + (float)$price_mo_mo_sum[$price_mo_mo_key];
                             $price_mo_mo_arr[$price_mo_mo_key] = ['value' => $price_mo_mo_sum[$price_mo_mo_key]];
                             $price_mo_moPrevious = $price_mo_mo_sum[$price_mo_mo_key];
                         }
                         if (count($price_mo_mo_arr) > 1) {

                             $noofdays = count($price_mo_mo_arr) - 1;
                             if ($today > $end_date)
                                 $noofdays = count($price_mo_mo_arr);

                             $totelCountryCosts[$country]['price_mo_mo']['avg'] = $avg = $country_totelcost_price_mo_mo / $noofdays;

                             $reaming_day = Carbon::parse($end_date)->daysInMonth;
                             $reaming_day = $reaming_day - $noofdays;
                             $T_Mo_End = $country_totelcost_price_mo_mo + ($avg * $reaming_day);
                             $totelCountryCosts[$country]['price_mo_mo']['t_mo_end'] = $T_Mo_End;
                         }
                     }

                     //for reg
                     foreach ($operater['reg']['dates'] as $reg_key => $reg_value) {

                         if ($key == 0) {
                             $reg_sum[$reg_key] = 0;
                             $totelCountryCosts[$country]['country'] = $operater['country'];
                         }
                         if (!array_key_exists($reg_key, $reg_sum)) {
                             $reg_sum[$reg_key] = 0;
                         }
                         $reg_sum[$reg_key] = $reg_sum[$reg_key] + (float)$reg_value['value'];
                         if (count($operaters) - 1 == $key) {

                             if ($flag == 5) {
                                 $regPrevious = $reg_sum[$reg_key];
                                 $flag = 10;
                             }
                             if ($reg_key != Carbon::now()->format('Y-m'))
                                 $country_totelcost_reg = $country_totelcost_reg + (float)$reg_sum[$reg_key];
                             $reg_arr[$reg_key] = ['value' => $reg_sum[$reg_key]];
                             $regPrevious = $reg_sum[$reg_key];
                         }
                         if (count($reg_arr) > 1) {

                             $noofdays = count($reg_arr) - 1;
                             if ($today > $end_date)
                                 $noofdays = count($reg_arr);

                             $totelCountryCosts[$country]['reg']['avg'] = $avg = $country_totelcost_reg / $noofdays;

                             $reaming_day = Carbon::parse($end_date)->daysInMonth;
                             $reaming_day = $reaming_day - $noofdays;
                             $T_Mo_End = $country_totelcost_mo + ($avg * $reaming_day);
                             $totelCountryCosts[$country]['reg']['t_mo_end'] = $T_Mo_End;
                         }
                     }

                     //for bill
                     foreach ($operater['bill']['dates'] as $bill_key => $bill_value) {

                         if ($key == 0) {
                             $bill_sum[$bill_key] = 0;
                             $totelCountryCosts[$country]['country'] = $operater['country'];
                         }
                         if (!array_key_exists($bill_key, $bill_sum)) {
                             $bill_sum[$bill_key] = 0;
                         }
                         $bill_sum[$bill_key] = $bill_sum[$bill_key] + (float)$bill_value['value'];
                         if ($bill_key != Carbon::now()->format('Y-m'))
                             $bill_sum[$bill_key] = $bill_sum[$bill_key] + (float)$bill_value['value'];
                         if (count($operaters) - 1 == $key) {

                             if ($flag == 5) {
                                 $bilPrevious = $bill_sum[$bill_key];
                                 $flag = 10;
                             }

                             $mt_success = $dp_success_arr[$bill_key]['value'];
                             $mt_failed = $dp_failed_arr[$bill_key]['value'];
                             $total_subscriber = $active_subs_arr[$bill_key]['value'];
                             $billing_rate = 0;

                             $sent = $mt_success + $mt_failed;

                             if ($sent == 0) {
                                 if ($total_subscriber > 0) {
                                     $billing_rate = ($mt_success / $total_subscriber) * 100;
                                 }
                             } else if ($mt_failed == 0) {
                                 if ($total_subscriber > 0) {
                                     $billing_rate = ($mt_success / $total_subscriber) * 100;
                                 }
                             } else {
                                 if ($total_subscriber > 0) {
                                     $billing_rate = ($mt_success / $total_subscriber) * 100;
                                 } else {
                                     $billing_rate = ($mt_success / $sent) * 100;
                                 }
                             }

                             if ($bill_key != Carbon::now()->format('Y-m'))
                                 $country_totelcost_bill = $country_totelcost_bill + (float)$billing_rate;
                             $bill_arr[$bill_key] = ['value' => $billing_rate];
                             $bilPrevious = $bill_sum[$bill_key];
                         }
                         if (count($bill_arr) > 1) {

                             $noofdays = count($bill_arr) - 1;
                             if ($today > $end_date)
                                 $noofdays = count($bill_arr);

                             $totelCountryCosts[$country]['bill']['avg'] = $avg = $country_totelcost_bill / $noofdays;

                             $reaming_day = Carbon::parse($end_date)->daysInMonth;
                             $reaming_day = $reaming_day - $noofdays;
                             $T_Mo_End = $country_totelcost_bill + ($avg * $reaming_day);
                             $totelCountryCosts[$country]['bill']['t_mo_end'] = $T_Mo_End;
                         }
                     }

                     //for roi
                     foreach ($operater['roi']['dates'] as $roi_key => $roi_value) {

                         if ($key == 0) {
                             $roi_sum[$roi_key] = 0;
                             $totelCountryCosts[$country]['country'] = $operater['country'];
                         }
                         if (!array_key_exists($roi_key, $roi_sum)) {
                             $roi_sum[$roi_key] = 0;
                         }
                         $roi_sum[$roi_key] = $roi_sum[$roi_key] + (float)$roi_value['value'];
                         if ($roi_key != Carbon::now()->format('Y-m'))
                             $roi_sum[$roi_key] = $roi_sum[$roi_key] + (float)$roi_value['value'];
                         if (count($operaters) - 1 == $key) {

                             if ($flag == 5) {
                                 $roiPrevious = $roi_sum[$roi_key];
                                 $flag = 10;
                             }

                             if ($roi_key == date('Y-m')) {
                                 $R1 = $last_30_gros_rev_arr[$roi_key]['value'];
                                 $R2 = $last_30_reg_arr[$roi_key]['value'];
                                 $R3 = $R2 + $active_subs_arr[$roi_key]['value'];
                                 $R4 = $price_mo_cost_arr[$roi_key]['value'];
                                 $R5 = $price_mo_mo_arr[$roi_key]['value'];

                                 if ($R3 > 0) {
                                     $arpu_30 = $R1 / $R3;
                                 }

                                 if ($R5 > 0) {
                                     $price_mo = $R4 / $R5;
                                 }

                                 if ($arpu_30 > 0) {
                                     $roi = $price_mo / $arpu_30;
                                 }
                                 $country_totelcost_roi = $country_totelcost_roi + (float)$roi;
                                 $roi_arr[$roi_key] = ['value' => $roi];

                                 $totelCountryCosts[$country]['roi']['avg'] = $roi;

                                 $roiPrevious = $roi_sum[$roi_key];
                             } else {
                                 $R1 = $gros_rev_usd_arr[$roi_key]['value'];
                                 $R2 = $reg_arr[$roi_key]['value'];
                                 $R3 = $R2 + $active_subs_arr[$roi_key]['value'];
                                 $R4 = $cost_campaign_arr[$roi_key]['value'];
                                 $R5 = $mo_arr[$roi_key]['value'];

                                 if ($R3 > 0) {
                                     $arpu_30 = $R1 / $R3;
                                 }

                                 if ($R5 > 0) {
                                     $price_mo = $R4 / $R5;
                                 }

                                 if ($arpu_30 > 0) {
                                     $roi = $price_mo / $arpu_30;
                                 }
                                 $country_totelcost_roi = $country_totelcost_roi + (float)$roi;
                                 $roi_arr[$roi_key] = ['value' => $roi];
                                 $roiPrevious = $roi_sum[$roi_key];
                             }
                         }
                         if (count($roi_arr) > 1) {

                             $noofdays = count($roi_arr) - 1;
                             if ($today > $end_date)
                                 $noofdays = count($roi_arr);

                             if ($roi_key == Carbon::now()->format('Y-m')) {

                                 $totelCountryCosts[$country]['roi']['avg'] = $roi;
                             }


                             $reaming_day = Carbon::parse($end_date)->daysInMonth;
                             $reaming_day = $reaming_day - $noofdays;
                             $T_Mo_End = $country_totelcost_roi + ($avg * $reaming_day);
                             $totelCountryCosts[$country]['roi']['t_mo_end'] = $T_Mo_End;
                         }
                     }

                     //for renewal

                     foreach ($operater['renewal']['dates'] as $renewal_key => $renewal_value) {

                         if ($key == 0) {
                             $renewal_sum[$renewal_key] = 0;
                             $totelCountryCosts[$country]['country'] = $operater['country'];
                         }
                         if (!array_key_exists($renewal_key, $renewal_sum)) {
                             $renewal_sum[$renewal_key] = 0;
                         }
                         if ($renewal_key != Carbon::now()->format('Y-m-d'))
                             $renewal_sum[$renewal_key] = $renewal_sum[$renewal_key] + (float)$renewal_value['value'];
                         if (count($operaters) - 1 == $key) {

                             if ($flag == 5) {
                                 $renewalPrevious = $renewal_sum[$renewal_key];
                                 $flag = 10;
                             }
                             if ($renewal_key != Carbon::now()->format('Y-m')) {
                                 $country_totelcost_renewal = $country_totelcost_renewal + (float)$renewal_sum[$renewal_key];
                                 $count_renewal++;
                             }
                             if ($count_renewal > 0) {
                                 $renewalPrevious = $renewal_sum[$renewal_key];
                                 $renewal_avg = $country_totelcost_renewal / $count_renewal;
                             }
                             $renewal_t_mo_end = $renewal_avg * 12;
                             $renewal_arr[$renewal_key] = ['value' => $renewal_sum[$renewal_key]];
                         }
                     }
                 }

                 $totelCountryCosts[$country]['tur']['dates'] = $content_arr;
                 $totelCountryCosts[$country]['tur']['total'] = $country_totelcost_campaign;


                 $totelCountryCosts[$country]['mo']['dates'] = $mo_arr;
                 $totelCountryCosts[$country]['mo']['total'] = $country_totelcost_mo;


                 $totelCountryCosts[$country]['bill']['dates'] = $bill_arr;
                 $totelCountryCosts[$country]['bill']['total'] = $country_totelcost_bill;


                 $totelCountryCosts[$country]['roi']['dates'] = $roi_arr;
                 $totelCountryCosts[$country]['roi']['total'] = $country_totelcost_roi;


                 $totelCountryCosts[$country]['renewal']['dates'] = $renewal_arr;
                 $totelCountryCosts[$country]['renewal']['total'] = $country_totelcost_renewal;
                 $totelCountryCosts[$country]['renewal']['avg'] = $renewal_avg;
                 $totelCountryCosts[$country]['renewal']['t_mo_end'] = $renewal_t_mo_end;
             }
             $no_of_days = $no_of_months;

             $AllCuntryGrosRev['month_string'] = $month;
             $date = Carbon::parse($end_date)->format('Y-m');
             return view('report.monitor_monthly_country', compact('totelCountryCosts', 'AllCuntryGrosRev', 'no_of_days'));
         } else {
             return redirect()->back()->with('error', __('Permission Denied.'));
         }
     }
}
