<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Session;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\common\UtilityAnalytic;
use App\Models\Company;
use App\Models\Country;
use App\Models\ReportsPnlsOperatorSummarizes;
use App\Models\Operator;
use App\Models\ReportsPnls;
use App\Models\Revenushare;
use App\Models\report_summarize;
use App\Models\WeeklyCaps;
use App\Models\ServiceHistory;
use App\Models\MonthlyReportSummery;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use App\common\Utility;
use App\Models\User;
use App\common\UtilityReports;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Service;

class AnalyticController extends Controller
{
    public function adsMonitoring(Request $request)
    {
        if (\Auth::user()->can('Ads Monitoring')) {
            // get filtre request
            $CountryId = $req_CountryId = $request->country;
            $CompanyId = $req_CompanyId = $request->company;
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

            $start_date = Carbon::now()->startOfMonth()->subDays(35)->format('Y-m-d');
            $startColumnDateDisplay = Carbon::now()->startOfMonth()->format('Y-m-d');
            $end_date = Carbon::now()->format('Y-m-d');
            $month = Carbon::now()->format('F Y');

            $showAllOperator = true;

            if ($request->filled('to') && $request->filled('from')) {
                $start_date_input = new Carbon($req_Start_date);
                $display_date_input = new Carbon($req_Start_date);
                // $end_date_input = new Carbon($req_end_date);

                $start_date = $req_Start_date;
                $startColumnDateDisplay = $display_date_input->format('Y-m-d');
                $end_date = $req_end_date;
                $month = $display_date_input->format('F Y');
            }

            if ($request->filled('country') && !$request->filled('operatorId')) {
                $data = [
                    'id' => $req_CountryId,
                    'company' => 'allcompany',
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

            $arrayOperatorsIds = $Operators->pluck('id_operator')->toArray();

            $Pnlreports = ReportsPnlsOperatorSummarizes::select('country_id', 'id_operator', 'date', 'cost_campaign')
                ->filteroperator($arrayOperatorsIds)
                ->filterDateRange($start_date, $end_date)
                ->get()->toArray();

            $reportsByIDs = $this->getReportsOperatorID($Pnlreports);
            $datesIndividual = Utility::getRangeDates($startColumnDateDisplay, $end_date);
            $no_of_days = Utility::getRangeDateNo($datesIndividual);

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

                    $reportsColumnData = $this->getReportsDateWise($operator, $no_of_days, $reportsByIDs, $OperatorCountry);

                    $tmpOperators['month_string'] = $month;

                    $total_avg_t = UtilityAnalytic::calculateTotalAVG($operator, $reportsColumnData, $startColumnDateDisplay, $end_date);
                    $tmpOperators['cost_campaign']['dates'] = $reportsColumnData;
                    $tmpOperators['cost_campaign']['total'] = $total_avg_t['sum'];
                    $tmpOperators['cost_campaign']['t_mo_end'] = $total_avg_t['T_Mo_End'];;
                    $tmpOperators['cost_campaign']['avg'] = $total_avg_t['avg'];

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
                $country_totelcost_campaign = 0;

                foreach ($operaters as $key => $operater) {
                    foreach ($operater['cost_campaign']['dates'] as $content_key => $content_value) {
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

                $totelCountryCosts[$country]['cost_campaign']['dates'] = $content_arr;
                $totelCountryCosts[$country]['cost_campaign']['total'] = $country_totelcost_campaign;

                if (count($content_arr) > 1) {
                    $totelCountryCosts[$country]['cost_campaign']['avg'] = $avg = $country_totelcost_campaign / (count($content_arr) - 1);

                    $reaming_day = Carbon::now()->daysInMonth;
                    $reaming_day = $reaming_day - (count($content_arr) - 1);
                    $T_Mo_End = $country_totelcost_campaign + ($avg * $reaming_day);
                    $totelCountryCosts[$country]['cost_campaign']['t_mo_end'] = $T_Mo_End;
                }
            }

            $content_cuntry_sum = [];
            $content_cuntry_arr = [];
            $count = 0;
            $flag = 5;
            $cost_campaignPrevious = 0;
            $avg = $T_Mo_End = $country_totelcost_campaign = 0;

            foreach ($totelCountryCosts as $key => $totelCountryCost) {
                foreach ($totelCountryCost['cost_campaign']['dates'] as $content_key => $content_value) {
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
                            if (count($totelCountryCost['cost_campaign']['dates']) > 1)
                                $avg = $country_totelcost_campaign / (count($totelCountryCost['cost_campaign']['dates']) - 1);

                            $reaming_day = Carbon::now()->daysInMonth;
                            $reaming_day = $reaming_day - (count($totelCountryCost['cost_campaign']['dates']) - 1);
                            $T_Mo_End = $country_totelcost_campaign + ($avg * $reaming_day);
                        }

                        $content_cuntry_arr[$content_key] = ['value' => $content_cuntry_sum[$content_key], 'class' => $class, 'percentage' => $percentage];

                        $cost_campaignPrevious = $content_cuntry_sum[$content_key];
                    }
                }

                $count++;
            }

            $AllCuntryCostCamp['cost_campaign']['dates'] = $content_cuntry_arr;
            $AllCuntryCostCamp['cost_campaign']['total'] = $country_totelcost_campaign;
            $AllCuntryCostCamp['cost_campaign']['avg'] = $avg;
            $AllCuntryCostCamp['cost_campaign']['t_mo_end'] = $T_Mo_End;
            $AllCuntryCostCamp['month_string'] = $month;

            return view('analytic.adsmonitor', compact('totelCountryCosts', 'AllCuntryCostCamp', 'no_of_days'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }
    // Revenue country Wise AdsMonitoring Data
    public function countryWiseAdsMonitoring(Request $request)
    {
        if (\Auth::user()->can('Ads Monitoring')) {
            $CountryWise = 1;

            // get filtre request
            $CountryId = $req_CountryId = $request->country;
            $CompanyId = $req_CompanyId = $request->company;
            $filterOperator = $req_filterOperator = $request->operatorId;
            $Start_date = $req_Start_date = trim($request->from);
            $end_date = $req_end_date = trim($request->to);

            if ($end_date <= $Start_date) {
                $Start_date = $req_Start_date = trim($request->to);
                $end_date = $req_end_date = $request->from;
            }

            $start_date = Carbon::now()->startOfMonth()->subDays(35)->format('Y-m-d');
            $startColumnDateDisplay = Carbon::now()->startOfMonth()->format('Y-m-d');
            $end_date = Carbon::now()->format('Y-m-d');
            $month = Carbon::now()->format('F Y');

            $Country = Country::all()->toArray();
            $companys = Company::get();
            $countries = array();

            $showAllOperator = true;

            if ($request->filled('to') && $request->filled('from')) {
                $display_date_input = new Carbon($req_Start_date);
                $start_date = $req_Start_date;
                $startColumnDateDisplay = $display_date_input->format('Y-m-d');
                $end_date = $req_end_date;
                $month = $display_date_input->format('F Y');
            } else {
                $req_Start_date = date('Y-m-d', strtotime('-30 days'));
                $req_end_date = date('Y-m-d');

                $start_date_input = new Carbon($req_Start_date);
                $display_date_input = new Carbon($req_Start_date);

                $start_date = $start_date_input->subDays(35)->format('Y-m-d');
                $startColumnDateDisplay = $display_date_input->format('Y-m-d');

                $end_date = $req_end_date;
                $month = $display_date_input->format('F Y');
            }

            if ($request->filled('country') && !$request->filled('operatorId')) {
                $Country = Country::where(['id' => $req_CountryId])->get()->toArray();
                $Operators = Operator::with('revenueshare')->Status(1)->GetOperatorByCountryId($req_CountryId)->get();
                $showAllOperator = false;
            }

            if ($request->filled('operatorId')) {
                $Operators = Operator::with('revenueshare')->Status(1)->GetOperatorByOperatorId($filterOperator)->get();

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

            $Pnlreports = ReportsPnlsOperatorSummarizes::select('country_id', 'id_operator', 'date', 'cost_campaign')
                ->filteroperator($arrayOperatorsIds)
                ->filterDateRange($start_date, $end_date)
                ->get()->toArray();

            $reportsByIDs = $this->getReportsOperatorID($Pnlreports);
            $datesIndividual = Utility::getRangeDates($startColumnDateDisplay, $end_date);
            $no_of_days = Utility::getRangeDateNo($datesIndividual);

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

                    $reportsColumnData = $this->getReportsDateWise($operator, $no_of_days, $reportsByIDs, $OperatorCountry);

                    $total_avg_t = UtilityAnalytic::calculateTotalAVG($operator, $reportsColumnData, $startColumnDateDisplay, $end_date);
                    $tmpOperators['cost_campaign']['dates'] = $reportsColumnData;
                    $tmpOperators['cost_campaign']['total'] = $total_avg_t['sum'];
                    $tmpOperators['cost_campaign']['t_mo_end'] = $total_avg_t['T_Mo_End'];;
                    $tmpOperators['cost_campaign']['avg'] = $total_avg_t['avg'];

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
                $country_totelcost_campaign = 0;

                foreach ($operaters as $key => $operater) {
                    foreach ($operater['cost_campaign']['dates'] as $content_key => $content_value) {
                        if ($key == 0) {
                            $content_sum[$content_key] = 0;
                            $totelCountryCosts[$country]['country'] = $operater['country'];
                        }

                        if (!array_key_exists($content_key, $content_sum)) {
                            $content_sum[$content_key] = 0;
                        }

                        if ($content_key != Carbon::now()->format('Y-m-d'))
                            $content_sum[$content_key] = $content_sum[$content_key] + (float)$content_value['value'];

                        if (count($operaters) - 1 == $key) {
                            if ($flag == 5) {
                                $cost_campaignPrevious = $content_sum[$content_key];
                                $flag = 10;
                            }

                            $datacp = $this->classPercentage($cost_campaignPrevious, $content_sum[$content_key]);
                            $class = $datacp['class'];
                            $percentage = $datacp['percentage'];

                            $country_totelcost_campaign = $country_totelcost_campaign + (float)$content_sum[$content_key];
                            $content_arr[$content_key] = ['value' => $content_sum[$content_key], 'class' => $class, 'percentage' => $percentage];

                            $cost_campaignPrevious = $content_sum[$content_key];
                        }
                    }
                }

                $totelCountryCosts[$country]['cost_campaign']['dates'] = $content_arr;
                $totelCountryCosts[$country]['cost_campaign']['total'] = $country_totelcost_campaign;

                if (count($content_arr) > 1) {
                    $totelCountryCosts[$country]['cost_campaign']['avg'] = $avg = $country_totelcost_campaign / (count($content_arr) - 1);

                    $reaming_day = Carbon::now()->daysInMonth;
                    $reaming_day = $reaming_day - (count($content_arr) - 1);
                    $T_Mo_End = $country_totelcost_campaign + ($avg * $reaming_day);
                    $totelCountryCosts[$country]['cost_campaign']['t_mo_end'] = $T_Mo_End;
                }
            }

            $content_cuntry_sum = [];
            $content_cuntry_arr = [];
            $count = 0;
            $flag = 5;
            $cost_campaignPrevious = 0;
            $avg = $T_Mo_End = $country_totelcost_campaign = 0;

            foreach ($totelCountryCosts as $key => $totelCountryCost) {
                foreach ($totelCountryCost['cost_campaign']['dates'] as $content_key => $content_value) {
                    if (!array_key_exists($content_key, $content_cuntry_sum)) {
                        $content_cuntry_sum[$content_key] = 0;
                    }

                    if ($content_key != Carbon::now()->format('Y-m-d'))
                        $content_cuntry_sum[$content_key] = $content_cuntry_sum[$content_key] + (float)$content_value['value'];

                    if (count($totelCountryCosts) - 1 == $count) {
                        if ($flag == 5) {
                            $cost_campaignPrevious = $content_cuntry_sum[$content_key];
                            $flag = 10;
                        }

                        $datacp = $this->classPercentage($cost_campaignPrevious, $content_sum[$content_key]);
                        $class = $datacp['class'];
                        $percentage = $datacp['percentage'];

                        $country_totelcost_campaign = $country_totelcost_campaign + (float)$content_cuntry_sum[$content_key];

                        if ($country_totelcost_campaign > 0) {
                            if (count($totelCountryCost['cost_campaign']['dates']) > 1)
                                $avg = $country_totelcost_campaign / (count($totelCountryCost['cost_campaign']['dates']) - 1);

                            $reaming_day = Carbon::now()->daysInMonth;
                            $reaming_day = $reaming_day - (count($totelCountryCost['cost_campaign']['dates']) - 1);
                            $T_Mo_End = $country_totelcost_campaign + ($avg * $reaming_day);
                        }

                        $content_cuntry_arr[$content_key] = ['value' => $content_cuntry_sum[$content_key], 'class' => $class, 'percentage' => $percentage];

                        $cost_campaignPrevious = $content_cuntry_sum[$content_key];
                    }
                }

                $count++;
            }

            $AllCuntryCostCamp['cost_campaign']['dates'] = $content_cuntry_arr;
            $AllCuntryCostCamp['cost_campaign']['total'] = $country_totelcost_campaign;
            $AllCuntryCostCamp['cost_campaign']['avg'] = $avg;
            $AllCuntryCostCamp['cost_campaign']['t_mo_end'] = $T_Mo_End;
            $AllCuntryCostCamp['month_string'] = $month;

            return view('analytic.adsmonitor', compact('totelCountryCosts', 'AllCuntryCostCamp', 'no_of_days'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    // Revenue company Wise AdsMonitoring  Data
    public function companyWiseAdsMonitoring(Request $request)
    {
        if (\Auth::user()->can('Ads Monitoring')) {
            $CountryId = $req_CountryId = $request->country;
            $CompanyId = $req_CompanyId = $request->company;
            $filterOperator = $req_filterOperator = $request->operatorId;
            $Start_date = $req_Start_date = trim($request->from);
            $end_date = $req_end_date = trim($request->to);
            $Country = Country::all()->toArray();
            $companys = Company::get();
            $countries = array();

            if ($end_date <= $Start_date) {
                $Start_date = $req_Start_date = trim($request->to);
                $end_date =  $req_end_date = $request->from;
            }
            if (!empty($Country)) {
                foreach ($Country as $CountryI) {
                    $countries[$CountryI['id']] = $CountryI;
                    $countrys[$CountryI['id']] = [];
                }
            }

            $contains = Arr::hasAny($Country, "2");

            $sumemry = array();

            $start_date = Carbon::now()->startOfMonth()->subDays(35)->format('Y-m-d');
            $startColumnDateDisplay = Carbon::now()->startOfMonth()->format('Y-m-d');
            $end_date = Carbon::now()->format('Y-m-d');
            $month = Carbon::now()->format('F Y');


            $showAllOperator = true;

            if ($request->filled('to') && $request->filled('from')) {
                $start_date_input = new Carbon($req_Start_date);
                $display_date_input = new Carbon($req_Start_date);
                // $end_date_input = new Carbon($req_end_date);
                $start_date = $req_Start_date;
                $startColumnDateDisplay = $display_date_input->format('Y-m-d');
                $end_date = $req_end_date;
                $month = $display_date_input->format('F Y');
            }

            if ($request->filled('country') && !$request->filled('operatorId')) {
                $data = [
                    'id' => $req_CountryId,
                    'company' => 'allcompany',
                ];

                $requestobj = new Request($data);
                $ReportControllerobj = new ReportController;
                $Operators = $ReportControllerobj->userFilterOperator($requestobj);
                $showAllOperator = false;
                // $Operators = Operator::with('revenueshare')->Status(1)->GetOperatorByCountryId($req_CountryId)->get();
                //   $showAllOperator = false;
            }

            if ($request->filled('operatorId')) {
                $Operators = Operator::with('revenueshare')->Status(1)->GetOperatorByOperatorId($filterOperator)->get();
                $showAllOperator = false;
            }

            if ($showAllOperator) {
                $Operators = Operator::with('revenueshare')->Status(1)->get();
            }
            $arrayOperatorsIds = $Operators->pluck('id_operator')->toArray();


            $Pnlreports = ReportsPnlsOperatorSummarizes::select('country_id', 'id_operator', 'date', 'cost_campaign')
            ->filteroperator($arrayOperatorsIds)
            ->filterDateRange($start_date, $end_date)
            ->get()->toArray();
            // dd($Pnlreports);
            $reportsByIDs = $this->getReportsOperatorID($Pnlreports);
            $datesIndividual = Utility::getRangeDates($startColumnDateDisplay, $end_date);
            $no_of_days = Utility::getRangeDateNo($datesIndividual);
            // $Operators = Operator::with('revenueshare', 'company_operators')->Status(1)->get();

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

                    $reportsColumnData = $this->getReportsDateWise($operator, $no_of_days, $reportsByIDs, $OperatorCountry);

                    $total_avg_t = UtilityAnalytic::calculateTotalAVG($operator, $reportsColumnData, $startColumnDateDisplay, $end_date);
                    $tmpOperators['cost_campaign']['dates'] = $reportsColumnData;
                    $tmpOperators['cost_campaign']['total'] = $total_avg_t['sum'];
                    $tmpOperators['cost_campaign']['t_mo_end'] = $total_avg_t['T_Mo_End'];;
                    $tmpOperators['cost_campaign']['avg'] = $total_avg_t['avg'];

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
                $country_totelcost_campaign = 0;

                foreach ($operaters as $key => $operater) {
                    foreach ($operater['cost_campaign']['dates'] as $content_key => $content_value) {
                        if ($key == 0) {
                            $content_sum[$content_key] = 0;
                            $totelCountryCosts[$company]['country'] = $operater['country'];
                            $totelCountryCosts[$company]['country']['country'] = $operater['company']->name;
                        }

                        if (!array_key_exists($content_key, $content_sum)) {
                            $content_sum[$content_key] = 0;
                        }

                        // if ($content_key != Carbon::now()->format('Y-m-d'))
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

                $totelCountryCosts[$company]['cost_campaign']['dates'] = $content_arr;
                $totelCountryCosts[$company]['cost_campaign']['total'] = $country_totelcost_campaign;

                if (count($content_arr) > 1) {
                    $totelCountryCosts[$company]['cost_campaign']['avg'] = $avg = $country_totelcost_campaign / (count($content_arr) - 1);

                    $reaming_day = Carbon::now()->daysInMonth;
                    $reaming_day = $reaming_day - (count($content_arr) - 1);
                    $T_Mo_End = $country_totelcost_campaign + ($avg * $reaming_day);
                    $totelCountryCosts[$company]['cost_campaign']['t_mo_end'] = $T_Mo_End;
                }
            }

            $content_cuntry_sum = [];
            $content_cuntry_arr = [];
            $count = 0;
            $flag = 5;
            $cost_campaignPrevious = 0;
            $avg = $T_Mo_End = $country_totelcost_campaign = 0;

            foreach ($totelCountryCosts as $key => $totelCountryCost) {
                foreach ($totelCountryCost['cost_campaign']['dates'] as $content_key => $content_value) {
                    if (!array_key_exists($content_key, $content_cuntry_sum)) {
                        $content_cuntry_sum[$content_key] = 0;
                    }

                    // if ($content_key != Carbon::now()->format('Y-m-d'))
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
                            if (count($totelCountryCost['cost_campaign']['dates']) > 1)
                                $avg = $country_totelcost_campaign / (count($totelCountryCost['cost_campaign']['dates']) - 1);

                            $reaming_day = Carbon::now()->daysInMonth;
                            $reaming_day = $reaming_day - (count($totelCountryCost['cost_campaign']['dates']) - 1);
                            $T_Mo_End = $country_totelcost_campaign + ($avg * $reaming_day);
                        }

                        $content_cuntry_arr[$content_key] = ['value' => $content_cuntry_sum[$content_key], 'class' => $class, 'percentage' => $percentage];

                        $cost_campaignPrevious = $content_cuntry_sum[$content_key];
                    }
                }

                $count++;
            }

            $AllCuntryCostCamp['cost_campaign']['dates'] = $content_cuntry_arr;
            $AllCuntryCostCamp['cost_campaign']['total'] = $country_totelcost_campaign;
            $AllCuntryCostCamp['cost_campaign']['avg'] = $avg;
            $AllCuntryCostCamp['cost_campaign']['t_mo_end'] = $T_Mo_End;
            $AllCuntryCostCamp['month_string'] = $month;

            return view('analytic.adsmonitor', compact('totelCountryCosts', 'AllCuntryCostCamp', 'no_of_days'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }


    // Revenue Business Wise AdsMonitoring  Data
    public function businessWiseAdsMonitoring(Request $request)
    {
        if (\Auth::user()->can('Ads Monitoring')) {
            $BusinessTypeWise = 1;
            $CountryId = $req_CountryId = $request->country;
            $CompanyId = $req_CompanyId = $request->company;
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

            $start_date = Carbon::now()->startOfMonth()->subDays(35)->format('Y-m-d');
            $startColumnDateDisplay = Carbon::now()->startOfMonth()->format('Y-m-d');
            $end_date = Carbon::now()->format('Y-m-d');
            $month = Carbon::now()->format('F Y');

            $showAllOperator = true;

            if ($request->filled('to') && $request->filled('from')) {
                $start_date_input = new Carbon($req_Start_date);
                $display_date_input = new Carbon($req_Start_date);
                // $end_date_input = new Carbon($req_end_date);

                $start_date = $req_Start_date;
                $startColumnDateDisplay = $display_date_input->format('Y-m-d');
                $end_date = $req_end_date;
                $month = $display_date_input->format('F Y');
            }

            if ($request->filled('country') && !$request->filled('operatorId')) {
                $data = [
                    'id' => $req_CountryId,
                    'company' => 'allcompany',
                ];

                $requestobj = new Request($data);
                $ReportControllerobj = new ReportController;
                $Operators = $ReportControllerobj->userFilterOperator($requestobj);
                $showAllOperator = false;
                // $Operators = Operator::with('revenueshare')->Status(1)->GetOperatorByCountryId($req_CountryId)->get();
                //   $showAllOperator = false;
            }

            if ($request->filled('operatorId')) {
                $Operators = Operator::with('revenueshare')->Status(1)->GetOperatorByOperatorId($filterOperator)->get();
                $showAllOperator = false;
            }

            if ($showAllOperator) {
                $Operators = Operator::with('revenueshare')->Status(1)->get();
            }

            $arrayOperatorsIds = $Operators->pluck('id_operator')->toArray();

            $Pnlreports = ReportsPnlsOperatorSummarizes::select('country_id', 'id_operator', 'date', 'cost_campaign')
                ->filteroperator($arrayOperatorsIds)
                ->filterDateRange($start_date, $end_date)
                ->get()->toArray();
                // dd($Pnlreports);

            $reportsByIDs = $this->getReportsOperatorID($Pnlreports);
            $datesIndividual = Utility::getRangeDates($startColumnDateDisplay, $end_date);
            $no_of_days = Utility::getRangeDateNo($datesIndividual);
            $summary = [];

            if (!empty($Operators)) {
                foreach ($Operators as $operator) {
                    $tmpOperators = array();
                    $type = $operator->business_type;
                    if($type == NULL){
                        $type = 'unknown';
                    }
                    // if(!isset($type)){
                    //     continue;
                    // }
                    $tmpOperators['operator'] = $operator->toArray();
                    $country_id = $operator->country_id;
                    $contain_id = Arr::exists($countries, $country_id);
                    $OperatorCountry = array();
                    $summarys[] = $type;
                    if ($contain_id) {
                        $tmpOperators['country'] = $countries[$country_id];
                        $OperatorCountry = $countries[$country_id];
                    }

                    $reportsColumnData = $this->getReportsDateWise($operator, $no_of_days, $reportsByIDs, $OperatorCountry);

                    $tmpOperators['month_string'] = $month;

                    $total_avg_t = UtilityAnalytic::calculateTotalAVG($operator, $reportsColumnData, $startColumnDateDisplay, $end_date);
                    $tmpOperators['cost_campaign']['dates'] = $reportsColumnData;
                    $tmpOperators['cost_campaign']['total'] = $total_avg_t['sum'];
                    $tmpOperators['cost_campaign']['t_mo_end'] = $total_avg_t['T_Mo_End'];;
                    $tmpOperators['cost_campaign']['avg'] = $total_avg_t['avg'];

                    $summary[$type][] = $tmpOperators;

                }
            }
            // dd($summary);
            $totelCountryCosts = [];
            foreach ($summary as $country => $operaters) {
                $content_arr = [];
                $totelCountryCosts[$country]['operator'] = $operaters;
                $class = '';
                $cost_campaignPrevious = 0;
                $flag = 5;
                $country_totelcost_campaign = 0;

                foreach ($operaters as $key => $operater) {

                    foreach ($operater['cost_campaign']['dates'] as $content_key => $content_value) {
                        if ($key == 0) {
                            $content_sum[$content_key] = 0;
                            $totelCountryCosts[$country]['country']['country'] = $country;
                            $totelCountryCosts[$country]['country']['flag'] = '';
                            // $totelCountryCosts[$country]['country']['id'] = 0;


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

                $totelCountryCosts[$country]['cost_campaign']['dates'] = $content_arr;
                $totelCountryCosts[$country]['cost_campaign']['total'] = $country_totelcost_campaign;


                if (count($content_arr) > 1) {
                    $totelCountryCosts[$country]['cost_campaign']['avg'] = $avg = $country_totelcost_campaign / (count($content_arr) - 1);

                    $reaming_day = Carbon::now()->daysInMonth;
                    $reaming_day = $reaming_day - (count($content_arr) - 1);
                    $T_Mo_End = $country_totelcost_campaign + ($avg * $reaming_day);
                    $totelCountryCosts[$country]['cost_campaign']['t_mo_end'] = $T_Mo_End;
                }
            }
            $content_cuntry_sum = [];
            $content_cuntry_arr = [];
            $count = 0;
            $flag = 5;
            $cost_campaignPrevious = 0;
            $avg = $T_Mo_End = $country_totelcost_campaign = 0;

            foreach ($totelCountryCosts as $key => $totelCountryCost) {
                foreach ($totelCountryCost['cost_campaign']['dates'] as $content_key => $content_value) {
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
                            if (count($totelCountryCost['cost_campaign']['dates']) > 1)
                                $avg = $country_totelcost_campaign / (count($totelCountryCost['cost_campaign']['dates']) - 1);

                            $reaming_day = Carbon::now()->daysInMonth;
                            $reaming_day = $reaming_day - (count($totelCountryCost['cost_campaign']['dates']) - 1);
                            $T_Mo_End = $country_totelcost_campaign + ($avg * $reaming_day);
                        }

                        $content_cuntry_arr[$content_key] = ['value' => $content_cuntry_sum[$content_key], 'class' => $class, 'percentage' => $percentage];

                        $cost_campaignPrevious = $content_cuntry_sum[$content_key];
                    }
                }

                $count++;
            }

            $AllCuntryCostCamp['cost_campaign']['dates'] = $content_cuntry_arr;
            $AllCuntryCostCamp['cost_campaign']['total'] = $country_totelcost_campaign;
            $AllCuntryCostCamp['cost_campaign']['avg'] = $avg;
            $AllCuntryCostCamp['cost_campaign']['t_mo_end'] = $T_Mo_End;
            $AllCuntryCostCamp['month_string'] = $month;

            return view('analytic.adsmonitor', compact('totelCountryCosts', 'BusinessTypeWise','AllCuntryCostCamp', 'no_of_days'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }
    // Campign details of operator wise AdsMonitoring  Data
    public function detailsAdsMonitoring(Request $request)
    {
        if (\Auth::user()->can('Ads Monitoring') && !empty($request->filled('operator'))) {
            $filterOperator = $req_filterOperator = $request->operator;
            $Start_date = $req_Start_date = trim($request->from);
            $end_date =  $req_end_date = trim($request->to);

            if ($end_date <= $Start_date) {
                $Start_date = $req_Start_date = trim($request->to);
                $end_date =  $req_end_date = $request->from;
            }

            $start_date = Carbon::now()->startOfMonth()->subDays(35)->format('Y-m-d');
            $startColumnDateDisplay = Carbon::now()->startOfMonth()->format('Y-m-d');
            $end_date = Carbon::now()->format('Y-m-d');
            $month = Carbon::now()->format('F Y');

            if ($request->filled('to') && $request->filled('from')) {
                $display_date_input = new Carbon($req_Start_date);
                $start_date = $req_Start_date;
                $startColumnDateDisplay = $display_date_input->format('Y-m-d');
                $end_date = $req_end_date;
                $month = $display_date_input->format('F Y');
            } else {
                $req_Start_date = date('Y-m-d', strtotime('-30 days'));
                $req_end_date = date('Y-m-d');

                $start_date_input = new Carbon($req_Start_date);
                $display_date_input = new Carbon($req_Start_date);

                $start_date = $start_date_input->subDays(35)->format('Y-m-d');
                $startColumnDateDisplay = $display_date_input->format('Y-m-d');

                $end_date = $req_end_date;
                $month = $display_date_input->format('F Y');
            }

            if ($request->filled('operator')) {
                $Adnet = ReportsPnls::select('publisher')->distinct()->GetRecordByOperator($filterOperator)->filterDateRange($startColumnDateDisplay, $end_date)->get()->toArray();
                $OperatorCampaignData = ReportsPnls::GetRecordByOperator($filterOperator)->filterDateRange($startColumnDateDisplay, $end_date)->get()->toArray();
            }

            $sumemry = array();

            $campaignByAdnet = $this->getCampaignOperatorAdnet($OperatorCampaignData);
            $datesIndividual = Utility::getRangeDates($startColumnDateDisplay, $end_date);
            $no_of_days = Utility::getRangeDateNo($datesIndividual);

            if (!empty($Adnet)) {
                foreach ($Adnet as $operator) {
                    $tmpOperators = array();
                    $tmpOperators['adnet'] = $operator['publisher'];

                    $reportsColumnData = $this->getCampaignDateWise($operator, $no_of_days, $campaignByAdnet);

                    $total_avg_cost_campaign = UtilityAnalytic::calculateTotalAVG($operator, $reportsColumnData['cost_campaign'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['cost_campaign']['dates'] = $reportsColumnData['cost_campaign'];
                    $tmpOperators['cost_campaign']['total'] = $total_avg_cost_campaign['sum'];
                    $tmpOperators['cost_campaign']['t_mo_end'] = $total_avg_cost_campaign['T_Mo_End'];;
                    $tmpOperators['cost_campaign']['avg'] = $total_avg_cost_campaign['avg'];

                    $total_avg_MO = UtilityAnalytic::calculateTotalAVG($operator, $reportsColumnData['MO'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['MO']['dates'] = $reportsColumnData['MO'];
                    $tmpOperators['MO']['total'] = $total_avg_MO['sum'];
                    $tmpOperators['MO']['t_mo_end'] = $total_avg_MO['T_Mo_End'];;
                    $tmpOperators['MO']['avg'] = $total_avg_MO['avg'];

                    $total_avg_price_mo = UtilityAnalytic::calculateTotalAVG($operator, $reportsColumnData['price_mo'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['price_mo']['dates'] = $reportsColumnData['price_mo'];
                    $tmpOperators['price_mo']['total'] = $total_avg_price_mo['sum'];
                    $tmpOperators['price_mo']['t_mo_end'] = $total_avg_price_mo['T_Mo_End'];;
                    $tmpOperators['price_mo']['avg'] = $total_avg_price_mo['avg'];

                    $total_avg_cr = UtilityAnalytic::calculateTotalAVG($operator, $reportsColumnData['cr'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['cr']['dates'] = $reportsColumnData['cr'];
                    $tmpOperators['cr']['total'] = $total_avg_cr['sum'];
                    $tmpOperators['cr']['t_mo_end'] = $total_avg_cr['T_Mo_End'];;
                    $tmpOperators['cr']['avg'] = $total_avg_cr['avg'];

                    $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators, "cost_campaign");
                    $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators, "MO");
                    $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators, "price_mo");
                    $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators, "cr");

                    $sumemry[] = $tmpOperators;
                }
            }

            return view('analytic.adsmonitorDetails', compact('sumemry', 'no_of_days'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    function getReportsOperatorID($Pnlreports)
    {
        if (!empty($Pnlreports)) {
            $pnlreportsResult = array();
            $tempreportpnl = array();

            foreach ($Pnlreports as $report) {
                $tempreportpnl[$report['id_operator']][$report['date']] = $report;
            }

            $reportsResult = $tempreportpnl;

            return $reportsResult;
        }
    }

    function getCampaignOperatorAdnet($oparator)
    {
        if (!empty($oparator)) {
            $pnlreportsResult = array();

            $tempreportpnl = array();

            foreach ($oparator as $report) {
                if (isset($tempreportpnl[$report['publisher']][$report['date']])) {
                    $tempreportpnl[$report['publisher']][$report['date']]['mo_received'] += $report['mo_received'];
                    $tempreportpnl[$report['publisher']][$report['date']]['saaf'] += $report['saaf'];
                    $tempreportpnl[$report['publisher']][$report['date']]['landing'] += $report['landing'];
                } else {
                    $tempreportpnl[$report['publisher']][$report['date']] = $report;
                }
            }

            $pnlreportsResult = $tempreportpnl;

            return $pnlreportsResult;
        }
    }

    function getReportsDateWise($operator, $no_of_days, $reportsByIDs, $OperatorCountry)
    {
        $usdValue = $OperatorCountry['usd'];

        if (!empty($no_of_days)) {
            $allColumnData = array();
            $cost_campaign = array();
            $id_operator = $operator->id_operator;
            $class = '';
            $cost_campaignPrevious = 0;
            $flag = 5;

            foreach (array_reverse($no_of_days) as $days) {
                $keys = $id_operator . "." . $days['date'];

                $summariserow = Arr::get($reportsByIDs, $keys, 0);

                $cost_campaigndata = isset($summariserow['cost_campaign']) ? $summariserow['cost_campaign'] : 0;

                if ($flag == 5) {
                    $cost_campaignPrevious = $cost_campaigndata;
                    $flag = 10;
                }

                $datacp = $this->classPercentage($cost_campaignPrevious, $cost_campaigndata);
                $class = $datacp['class'];
                $percentage = $datacp['percentage'];

                if ($days['date'] == Carbon::now()->format('Y-m-d')) {
                    $class = '';
                    $percentage = 0;
                }

                $cost_campaign[$days['date']]['value'] = $cost_campaigndata;
                $cost_campaign[$days['date']]['class'] = $class;
                $cost_campaign[$days['date']]['percentage'] = $percentage;

                $cost_campaignPrevious = $cost_campaigndata;
            }

            $allColumnData = $cost_campaign;

            return $allColumnData;
        }
    }

    function getCampaignDateWise($operator, $no_of_days, $campaignByAdnet)
    {
        if (!empty($no_of_days)) {
            $allColumnData = array();
            $cost_campaign = array();
            $class = '';
            $cost_campaignPrevious = 0;
            $flag = 5;

            foreach ($no_of_days as $days) {
                $keys = $operator['publisher'] . "." . $days['date'];

                $summariserow = Arr::get($campaignByAdnet, $keys, 0);

                $saaf = isset($summariserow['saaf']) ? $summariserow['saaf'] : 0;
                $mo_received = isset($summariserow['mo_received']) ? $summariserow['mo_received'] : 0;
                $landing = isset($summariserow['landing']) ? $summariserow['landing'] : 0;

                $priceMO = ($mo_received == 0) ? 0 : ($saaf / $mo_received);
                $CR = ($landing == 0) ? 0 : ($mo_received / $landing);

                $cost_campaign[$days['date']]['value'] = $saaf;
                $MO_data[$days['date']]['value'] = $mo_received;
                $PriceMO_data[$days['date']]['value'] = $priceMO;
                $CR_data[$days['date']]['value'] = $CR;
            }

            $allColumnData['cost_campaign'] = $cost_campaign;
            $allColumnData['MO'] = $MO_data;
            $allColumnData['price_mo'] = $PriceMO_data;
            $allColumnData['cr'] = $CR_data;

            return $allColumnData;
        }
    }

    public function classPercentage($cost_campaignPrevious, $cost_campaigndata)
    {
        if ((float)$cost_campaignPrevious > (float)$cost_campaigndata) {
            $class = 'text-danger';
        } elseif ((float)$cost_campaignPrevious == (float)$cost_campaigndata) {
            $class = '';
        } else {
            $class = 'text-success';
        }

        $percentage = 0;

        if ($cost_campaignPrevious > 0)
            $percentage = (((float)$cost_campaigndata - (float)$cost_campaignPrevious) * 100) / $cost_campaignPrevious;

        $data = ['class' => $class, 'percentage' => round($percentage, 1)];

        return $data;
    }

    public function revenueMonitoring(Request $request)
    {
        if (\Auth::user()->can('Revenue Monitoring')) {
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

            $start_date = Carbon::now()->startOfMonth()->subDays(35)->format('Y-m-d');
            $startColumnDateDisplay = Carbon::now()->startOfMonth()->format('Y-m-d');
            $end_date = Carbon::now()->format('Y-m-d');
            $today = Carbon::now()->format('Y-m-d');
            $month = Carbon::now()->format('F Y');

            $showAllOperator = true;

            if ($request->filled('to') && $request->filled('from')) {
                $start_date_input = new Carbon($req_Start_date);
                $display_date_input = new Carbon($req_Start_date);
                // $end_date_input = new Carbon($req_end_date);

                $start_date = $start_date_input;
                $startColumnDateDisplay = $display_date_input->format('Y-m-d');

                $end_date = $req_end_date;
                $month = $display_date_input->format('F Y');
            } else {
                $req_Start_date = date('Y-m-d', strtotime('-30 days'));
                $req_end_date = date('Y-m-d');

                $start_date_input = new Carbon($req_Start_date);
                $display_date_input = new Carbon($req_Start_date);

                $start_date = $start_date_input->subDays(35)->format('Y-m-d');
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

            $arrayOperatorsIds = $Operators->pluck('id_operator')->toArray();

            $Pnlreports = report_summarize::select('country_id', 'operator_id as id_operator', 'date', 'gros_rev')
                ->filterDateRange($start_date, $end_date)
                ->filteroperator($arrayOperatorsIds)
                ->get()
                ->toArray();

            $reportsByIDs = $this->getReportsOperatorID($Pnlreports);


            $datesIndividual = Utility::getRangeDates($startColumnDateDisplay, $end_date);
            $no_of_days = Utility::getRangeDateNo($datesIndividual);

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

                    $reportsColumnData = $this->getReportsGrosRevDateWise($operator, $no_of_days, $reportsByIDs, $OperatorCountry);
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

                if (count($content_arr) > 1) {
                    $noofdays = count($content_arr)-1;
                    if($today > $end_date)
                    $noofdays = count($content_arr);

                    $totelCountryCosts[$country]['gros_rev']['avg'] = $avg = $country_totelcost_campaign / $noofdays;

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

            return view('analytic.revenuemonitor', compact('totelCountryCosts', 'AllCuntryGrosRev', 'no_of_days'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function checkrevenue(Request $request)
    {
        $services = [];
        $db_revenue = [];
        $operator_name = '';
        $data = [];
        $revenue = 0;
        $getServiceRevenue = [];

        if ($request->filled('date') && $request->filled('id')) {
            $date = $request->get('date');
            $operator = $request->get('id');

            $service = Service::GetserviceByOperatorId($operator)->get()->toArray();
            $country = Operator::filterOperatorID($operator)->first();
            $usd = Country::GetById($country->country_id)->first();

            if (!empty($service)) {
                foreach ($service as $key => $val) {
                    $service_id = $val['id_service'];
                    $getRevenue = UtilityAnalytic::getDetailRevenueByService($service_id , $date);
                    $revenue += !empty($getRevenue[0]) ? $getRevenue[0]['gross_revenue'] : 0;
                    if (!empty($getRevenue)) {
                        $getServiceRevenue[$service_id] = $getRevenue[0];
                    }
                }

                $data['date'] = $date;
                $data['operator_id'] = $operator;
                $data['total_service'] = count($service);
                $data['revenue'] = $revenue;
                $data['usd_rate'] = $usd->usd;
                $data['usd_revenue'] = $revenue * $usd->usd;
                $data['service_details'] = $getServiceRevenue;
            }
            $operator_name = Operator::select('operator_name')->where('id_operator', $operator)->first();

            if (!empty($data)) {
                $services = $data['service_details'];
                foreach ($services as $key => $value) {
                    $db_revenue[$value['id_service']] = ServiceHistory::select('gros_rev', 'updated_at')
                        ->where('date', $date)
                        ->where('id_service', $value['id_service'])
                        ->first();
                }
            }
        }
        return view('analytic.checkrevenue', compact('data', 'services', 'operator_name', 'db_revenue'));
    }


    function getreportSummarizeGrosRevOperatorID($reportSummarizeGrosRev)
    {
        if (!empty($reportSummarizeGrosRev)) {
            $pnlreportsResult = array();
            $tempreportpnl = array();

            foreach ($reportSummarizeGrosRev as $GrosRev) {
                $tempreportpnl[$GrosRev['operator_id']][$GrosRev['date']] = $GrosRev;
            }

            $reportsResult = $tempreportpnl;
            return $reportsResult;
        }
    }

    function getReportsGrosRevDateWise($operator, $no_of_days, $reportsByIDs, $OperatorCountry)
    {
        $usdValue = $OperatorCountry['usd'];

        if (!empty($no_of_days)) {
            $allColumnData = array();
            $cost_campaign = array();
            $id_operator = $operator->id_operator;
            $class = '';
            $cost_campaignPrevious = 0;
            $flag = 5;

            foreach (array_reverse($no_of_days) as $days) {
                $keys = $id_operator . "." . $days['date'];
                $summariserow = Arr::get($reportsByIDs, $keys, 0);

                $cost_campaigndata = isset($summariserow['gros_rev']) ? $summariserow['gros_rev'] : 0;
                $cost_campaigndata = $cost_campaigndata * $usdValue;

                if ($flag == 5) {
                    $cost_campaignPrevious = $cost_campaigndata;
                    $flag = 10;
                }

                $datacp = $this->classPercentage($cost_campaignPrevious, $cost_campaigndata);
                $class = $datacp['class'];
                $percentage = $datacp['percentage'];

                if ($days['date'] == Carbon::now()->format('Y-m-d')) {
                    $class = '';
                    $percentage = 0;
                }

                $cost_campaign[$days['date']]['value'] = $cost_campaigndata;
                $cost_campaign[$days['date']]['class'] = $class;
                $cost_campaign[$days['date']]['percentage'] = $percentage;
                $cost_campaignPrevious = $cost_campaigndata;
            }

            $allColumnData = $cost_campaign;

            return $allColumnData;
        }
    }

    // Revenue Monitoring Country Wise Data
    public function revenueMonitoringCountryWise(Request $request)
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

            $start_date = Carbon::now()->startOfMonth()->subDays(35)->format('Y-m-d');
            $startColumnDateDisplay = Carbon::now()->startOfMonth()->format('Y-m-d');
            $end_date = Carbon::now()->format('Y-m-d');
            $month = Carbon::now()->format('F Y');

            $Country = Country::all()->toArray();
            $companys = Company::get();
            $countries = array();

            $showAllOperator = true;

            if ($request->filled('to') && $request->filled('from')) {
                $display_date_input = new Carbon($req_Start_date);
                $start_date = $req_Start_date;
                $startColumnDateDisplay = $display_date_input->format('Y-m-d');
                $end_date = $req_end_date;
                $month = $display_date_input->format('F Y');
            } else {
                $req_Start_date = date('Y-m-d', strtotime('-30 days'));
                $req_end_date = date('Y-m-d');

                $start_date_input = new Carbon($req_Start_date);
                $display_date_input = new Carbon($req_Start_date);

                $start_date = $start_date_input->subDays(35)->format('Y-m-d');
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

            $Pnlreports = report_summarize::select('country_id', 'operator_id as id_operator', 'date', 'gros_rev')
                ->filterDateRange($start_date, $end_date)
                ->filteroperator($arrayOperatorsIds)
                ->get()
                ->toArray();

            $reportsByIDs = $this->getReportsOperatorID($Pnlreports);
            $datesIndividual = Utility::getRangeDates($startColumnDateDisplay, $end_date);
            $no_of_days = Utility::getRangeDateNo($datesIndividual);

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

                    $reportsColumnData = $this->getReportsGrosRevDateWise($operator, $no_of_days, $reportsByIDs, $OperatorCountry);

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
                $country_totelcost_campaign = 0;

                foreach ($operaters as $key => $operater) {
                    foreach ($operater['gros_rev']['dates'] as $content_key => $content_value) {
                        if ($key == 0) {
                            $content_sum[$content_key] = 0;
                            $totelCountryCosts[$country]['country'] = $operater['country'];
                        }

                        // if(!array_key_exists($content_key, $content_sum)){
                        //     $content_sum[$content_key] = 0;
                        // }

                        // if($content_key != Carbon::now()->format('Y-m-d'))
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

                if (count($content_arr) > 1) {
                    $totelCountryCosts[$country]['gros_rev']['avg'] = $avg = $country_totelcost_campaign / (count($content_arr) - 1);

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

                    // if($content_key != Carbon::now()->format('Y-m-d'))
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
                            if (count($totelCountryCost['gros_rev']['dates']) > 1)
                                $avg = $country_totelcost_campaign / (count($totelCountryCost['gros_rev']['dates']) - 1);

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

    // Revenue Monitoring Company Wise Data
    public function revenueMonitoringCompanyWise(Request $request)
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

            $start_date = Carbon::now()->startOfMonth()->subDays(35)->format('Y-m-d');
            $startColumnDateDisplay = Carbon::now()->startOfMonth()->format('Y-m-d');
            $end_date = Carbon::now()->format('Y-m-d');
            $month = Carbon::now()->format('F Y');

            $Country = Country::all()->toArray();
            $companys = Company::get();
            $countries = array();

            $showAllOperator = true;

            if ($request->filled('to') && $request->filled('from')) {
                $display_date_input = new Carbon($req_Start_date);
                $start_date = $req_Start_date;
                $startColumnDateDisplay = $display_date_input->format('Y-m-d');
                $end_date = $req_end_date;
                $month = $display_date_input->format('F Y');
            } else {
                $req_Start_date = date('Y-m-d', strtotime('-30 days'));
                $req_end_date = date('Y-m-d');

                $start_date_input = new Carbon($req_Start_date);
                $display_date_input = new Carbon($req_Start_date);

                $start_date = $start_date_input->subDays(35)->format('Y-m-d');
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

            $Pnlreports = report_summarize::select('country_id', 'operator_id as id_operator', 'date', 'gros_rev')
                ->filterDateRange($start_date, $end_date)
                ->filteroperator($arrayOperatorsIds)
                ->get()
                ->toArray();

            $reportsByIDs = $this->getReportsOperatorID($Pnlreports);
            $datesIndividual = Utility::getRangeDates($startColumnDateDisplay, $end_date);
            $no_of_days = Utility::getRangeDateNo($datesIndividual);

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

                    $reportsColumnData = $this->getReportsGrosRevDateWise($operator, $no_of_days, $reportsByIDs, $OperatorCountry);

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
                $country_totelcost_campaign = 0;

                foreach ($operaters as $key => $operater) {
                    foreach ($operater['gros_rev']['dates'] as $content_key => $content_value) {
                        if ($key == 0) {
                            $content_sum[$content_key] = 0;
                            $totelCountryCosts[$company]['country'] = $operater['country'];
                            $totelCountryCosts[$company]['country']['country'] = $operater['company']->name;
                        }

                        // if(!array_key_exists($content_key, $content_sum)){
                        //     $content_sum[$content_key] = 0;
                        // }

                        // if($content_key != Carbon::now()->format('Y-m-d'))
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

                if (count($content_arr) > 1) {
                    $totelCountryCosts[$company]['gros_rev']['avg'] = $avg = $country_totelcost_campaign / (count($content_arr) - 1);

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

                    // if($content_key != Carbon::now()->format('Y-m-d'))
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
                            if (count($totelCountryCost['gros_rev']['dates']) > 1)
                                $avg = $country_totelcost_campaign / (count($totelCountryCost['gros_rev']['dates']) - 1);

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

     // Revenue Monitoring Business Wise Data
     public function revenueMonitoringBusinessWise(Request $request)
     {
         if (\Auth::user()->can('Revenue Monitoring')) {
             $BusinessWise = 1;

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

             $start_date = Carbon::now()->startOfMonth()->subDays(35)->format('Y-m-d');
             $startColumnDateDisplay = Carbon::now()->startOfMonth()->format('Y-m-d');
             $end_date = Carbon::now()->format('Y-m-d');
             $month = Carbon::now()->format('F Y');

             $Country = Country::all()->toArray();
             $companys = Company::get();
             $countries = array();

             $showAllOperator = true;

             if ($request->filled('to') && $request->filled('from')) {
                 $display_date_input = new Carbon($req_Start_date);
                 $start_date = $req_Start_date;
                 $startColumnDateDisplay = $display_date_input->format('Y-m-d');
                 $end_date = $req_end_date;
                 $month = $display_date_input->format('F Y');
             } else {
                 $req_Start_date = date('Y-m-d', strtotime('-30 days'));
                 $req_end_date = date('Y-m-d');

                 $start_date_input = new Carbon($req_Start_date);
                 $display_date_input = new Carbon($req_Start_date);

                 $start_date = $start_date_input->subDays(35)->format('Y-m-d');
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

             $Pnlreports = report_summarize::select('country_id', 'operator_id as id_operator', 'date', 'gros_rev')
                 ->filterDateRange($start_date, $end_date)
                 ->filteroperator($arrayOperatorsIds)
                 ->get()
                 ->toArray();

             $reportsByIDs = $this->getReportsOperatorID($Pnlreports);
             $datesIndividual = Utility::getRangeDates($startColumnDateDisplay, $end_date);
             $no_of_days = Utility::getRangeDateNo($datesIndividual);

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

                     $reportsColumnData = $this->getReportsGrosRevDateWise($operator, $no_of_days, $reportsByIDs, $OperatorCountry);

                     $total_avg_t = UtilityAnalytic::calculateTotalAVG($operator, $reportsColumnData, $startColumnDateDisplay, $end_date);
                     $tmpOperators['gros_rev']['dates'] = $reportsColumnData;
                     $tmpOperators['gros_rev']['total'] = $total_avg_t['sum'];
                     $tmpOperators['gros_rev']['t_mo_end'] = $total_avg_t['T_Mo_End'];;
                     $tmpOperators['gros_rev']['avg'] = $total_avg_t['avg'];

                     $sumemry[$type][] = $tmpOperators;
                 }
             }

            //  $countrys = array_filter($countrys);

             $totelCountryCosts = [];

             foreach ($sumemry as $country => $operaters) {
                 $content_arr = [];
                 $totelCountryCosts[$country]['operator'] = $operaters;
                 $class = '';
                 $cost_campaignPrevious = 0;
                 $flag = 5;
                 $country_totelcost_campaign = 0;

                 foreach ($operaters as $key => $operater) {
                     foreach ($operater['gros_rev']['dates'] as $content_key => $content_value) {
                         if ($key == 0) {
                             $content_sum[$content_key] = 0;
                             $totelCountryCosts[$country]['country']['country'] = isset($operater['operator']['business_type']) ? $operater['operator']['business_type'] : 'unknown';
                             $totelCountryCosts[$country]['country']['flag'] = '';
                         }

                         // if(!array_key_exists($content_key, $content_sum)){
                         //     $content_sum[$content_key] = 0;
                         // }

                         // if($content_key != Carbon::now()->format('Y-m-d'))
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

                 if (count($content_arr) > 1) {
                     $totelCountryCosts[$country]['gros_rev']['avg'] = $avg = $country_totelcost_campaign / (count($content_arr) - 1);

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

                     // if($content_key != Carbon::now()->format('Y-m-d'))
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
                             if (count($totelCountryCost['gros_rev']['dates']) > 1)
                                 $avg = $country_totelcost_campaign / (count($totelCountryCost['gros_rev']['dates']) - 1);

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

             return view('analytic.revenuemonitor', compact('totelCountryCosts', 'AllCuntryGrosRev', 'no_of_days','BusinessWise'));
         } else {
             return redirect()->back()->with('error', __('Permission Denied.'));
         }
     }
    // revenue alert
    public function revenueAlert(Request $request)
    {
        if (\Auth::user()->can('Revenue Alert')) {
            $today = Carbon::now()->format('Y-m-d');
            $yesterday = Carbon::yesterday()->format('Y-m-d');
            $previousday = Carbon::now()->subDays(2)->format('Y-m-d');
            $last_7day = Carbon::now()->subDays(7)->format('Y-m-d');
            $last_30day = Carbon::now()->subDays(30)->format('Y-m-d');
            $last_90day = Carbon::now()->subDays(90)->format('Y-m-d');

            $Countrys = Country::all()->toArray();
            $Operators = Operator::Status(1)->get();
            $arrayOperatorsIds = $Operators->pluck('id_operator')->toArray();
            $Revenushares = Revenushare::get();
            $operators = array();
            $countriesOpereter = array();
            $countries = array();
            $sumemry = array();
            $operator_shares = array();

            if (!empty($Revenushares)) {
                foreach ($Revenushares as $Revenushare) {
                    $operator_shares[$Revenushare->operator_id] = (float)$Revenushare->merchant_revenue_share / 100;
                }
            }

            if (!empty($Countrys)) {
                foreach ($Countrys as $CountryI) {
                    $countries[$CountryI['id']] = $CountryI;
                }
            }

            if (!empty($Operators)) {
                foreach ($Operators as $Operator) {
                    $countriesOpereter[$Operator['id_operator']] = $countries[$Operator->country_id];
                    $operators[$Operator['id_operator']] = $Operator;
                }
            }

            $yesterday_data = $this->DateWiseDataCalculation($yesterday, $yesterday, $arrayOperatorsIds, 1);

            $previousday_data = $this->DateWiseDataCalculation($previousday, $yesterday, $arrayOperatorsIds, 2);

            $last_7day_data = $this->DateWiseDataCalculation($last_7day, $yesterday, $arrayOperatorsIds, 7);

            $last_30day_data = $this->DateWiseDataCalculation($last_30day, $yesterday, $arrayOperatorsIds, 30);

            $last_90day_data = $this->DateWiseDataCalculation($last_90day, $yesterday, $arrayOperatorsIds, 90);
            $yesterdayData = $this->getReportsAlertData($yesterday_data, $countriesOpereter, $operators, $countries, $operator_shares);

            $previousdayData = $this->getReportsAlertData($previousday_data, $countriesOpereter, $operators, $countries, $operator_shares);

            $last_7dayData = $this->getReportsAlertData($last_7day_data, $countriesOpereter, $operators, $countries, $operator_shares);

            $last_30dayData = $this->getReportsAlertData($last_30day_data, $countriesOpereter, $operators, $countries, $operator_shares);

            $last_90dayData = $this->getReportsAlertData($last_90day_data, $countriesOpereter, $operators, $countries, $operator_shares);

            if (!empty($Operators)) {
                foreach ($Operators as $operator) {
                    $tmpOperators = array();
                    $id_operator = $operator->id_operator;
                    $country_id  = $operator->country_id;

                    $countriesOpereter[$id_operator] = $countries[$country_id];
                    $operators[$id_operator] = $operator;
                    $tmpOperators['operator'] = $operator;

                    $contain_id = Arr::exists($countries, $country_id);
                    $OperatorCountry = array();

                    if ($contain_id) {
                        $tmpOperators['country'] = $countries[$country_id];
                        $OperatorCountry = $countries[$country_id];
                    }

                    //$yesterday

                    $tmpOperators['yesterday']['gros_rev'] = (!empty($yesterdayData)) ? $yesterdayData['gros_rev'][$id_operator]['gros_rev'] : 0;
                    $tmpOperators['yesterday']['mo'] = (!empty($yesterdayData)) ? $yesterdayData['mo'][$id_operator]['mo'] : 0;
                    $tmpOperators['yesterday']['bill_rate'] = (!empty($yesterdayData)) ? $yesterdayData['bill_rate'][$id_operator]['bill_rate'] : 0;
                    $tmpOperators['yesterday']['renewal'] = (!empty($yesterdayData)) ? $yesterdayData['renewal'][$id_operator]['renewal'] : 0;
                    $tmpOperators['yesterday']['risk_status'] = 0;

                    //$previousday

                    $tmpOperators['previousday']['gros_rev'] = (!empty($previousdayData)) ? $previousdayData['gros_rev'][$id_operator]['gros_rev'] : 0;
                    $tmpOperators['previousday']['mo'] = (!empty($previousdayData)) ? $previousdayData['mo'][$id_operator]['mo'] : 0;
                    $tmpOperators['previousday']['bill_rate'] = (!empty($previousdayData)) ? $previousdayData['bill_rate'][$id_operator]['bill_rate'] : 0;
                    $tmpOperators['previousday']['renewal'] = (!empty($previousdayData)) ? $previousdayData['renewal'][$id_operator]['renewal'] : 0;
                    $tmpOperators['previousday']['risk_status'] = 0;

                    //$last_7day

                    $tmpOperators['last_7day']['gros_rev'] = (!empty($last_7dayData)) ? $last_7dayData['gros_rev'][$id_operator]['gros_rev'] : 0;
                    $tmpOperators['last_7day']['mo'] = (!empty($last_7dayData)) ? $last_7dayData['mo'][$id_operator]['mo'] : 0;
                    $tmpOperators['last_7day']['bill_rate'] = (!empty($last_7dayData)) ? $last_7dayData['bill_rate'][$id_operator]['bill_rate'] : 0;
                    $tmpOperators['last_7day']['renewal'] = (!empty($last_7dayData)) ? $last_7dayData['renewal'][$id_operator]['renewal'] : 0;
                    $tmpOperators['last_7day']['risk_status'] = 0;

                    //$last_30day

                    $tmpOperators['last_30day']['gros_rev'] = (!empty($last_30dayData)) ? $last_30dayData['gros_rev'][$id_operator]['gros_rev'] : 0;
                    $tmpOperators['last_30day']['mo'] = (!empty($last_30dayData)) ? $last_30dayData['mo'][$id_operator]['mo'] : 0;
                    $tmpOperators['last_30day']['bill_rate'] = (!empty($last_30dayData)) ? $last_30dayData['bill_rate'][$id_operator]['bill_rate'] : 0;
                    $tmpOperators['last_30day']['renewal'] = (!empty($last_30dayData)) ? $last_30dayData['renewal'][$id_operator]['renewal'] : 0;
                    $tmpOperators['last_30day']['risk_status'] = 0;

                    //$last_90day

                    $tmpOperators['last_90day']['gros_rev'] = (!empty($last_90dayData)) ? $last_90dayData['gros_rev'][$id_operator]['gros_rev'] : 0;
                    $tmpOperators['last_90day']['mo'] = (!empty($last_90dayData)) ? $last_90dayData['mo'][$id_operator]['mo'] : 0;
                    $tmpOperators['last_90day']['bill_rate'] = (!empty($last_90dayData)) ? $last_90dayData['bill_rate'][$id_operator]['bill_rate'] : 0;
                    $tmpOperators['last_90day']['renewal'] = (!empty($last_90dayData)) ? $last_90dayData['renewal'][$id_operator]['renewal'] : 0;
                    $tmpOperators['last_90day']['risk_status'] = 0;
                    $tmpOperators['last_90day']['percentage'] = 0;

                    // yesterday percentage calculation
                    if ($tmpOperators['yesterday']['gros_rev'] != 0 || $tmpOperators['previousday']['gros_rev'] != 0) {
                        $tmpOperators['yesterday']['percentage'] = ($tmpOperators['yesterday']['gros_rev'] != 0) ? (($tmpOperators['yesterday']['gros_rev'] - $tmpOperators['previousday']['gros_rev']) / $tmpOperators['previousday']['gros_rev']) * 100 : 100;
                    } else {
                        $tmpOperators['yesterday']['percentage'] = 0;
                    }

                    // previousday percentage calculation
                    if ($tmpOperators['previousday']['gros_rev'] != 0 || $tmpOperators['last_7day']['gros_rev'] != 0) {
                        $tmpOperators['previousday']['percentage'] = ($tmpOperators['previousday']['gros_rev'] != 0) ? (($tmpOperators['previousday']['gros_rev'] - $tmpOperators['last_7day']['gros_rev']) / $tmpOperators['last_7day']['gros_rev']) * 100 : 100;
                    } else {
                        $tmpOperators['previousday']['percentage'] = 0;
                    }

                    // last 7 day percentage calculation
                    if ($tmpOperators['last_7day']['gros_rev'] != 0 || $tmpOperators['last_30day']['gros_rev'] != 0) {
                        $tmpOperators['last_7day']['percentage'] = ($tmpOperators['last_7day']['gros_rev'] != 0) ? (($tmpOperators['last_7day']['gros_rev'] - $tmpOperators['last_30day']['gros_rev']) / $tmpOperators['last_30day']['gros_rev']) * 100 : 100;
                    } else {
                        $tmpOperators['last_7day']['percentage'] = 0;
                    }

                    // last 30 day percentage calculation
                    if ($tmpOperators['last_30day']['gros_rev'] != 0 || $tmpOperators['last_90day']['gros_rev'] != 0) {
                        $tmpOperators['last_30day']['percentage'] = ($tmpOperators['last_30day']['gros_rev'] != 0) ? (($tmpOperators['last_30day']['gros_rev'] - $tmpOperators['last_90day']['gros_rev']) / $tmpOperators['last_90day']['gros_rev']) * 100 : 100;
                    } else {
                        $tmpOperators['last_30day']['percentage'] = 0;
                    }

                    $sumemry[] = $tmpOperators;
                }
            }

            return view('analytic.revenueAlert', compact('sumemry'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    // roi
    public function roi(Request $request)
    {
        if (\Auth::user()->can('ROI Report')) {
            $requestData = $request->all();
            $data = [];
            $DataArray = [];
            $data['selected_month'] = $month = isset($requestData['month']) ? $requestData['month'] : '';
            $data['monthArray'] = [1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'];

            for ($i = 1; $i <= 12; $i++) {
                $data['months'][] = $i;
            }

            return view('analytic.ROI_Report', compact('data'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    // log performance
    public function logPerformance()
    {
        if (\Auth::user()->can('Log Performance')) {
            if (isset($_GET['date'])) {
                echo $_GET['date'];
            }
            return view('analytic.logperformance');
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    function DateWiseDataCalculation($start_date, $end_date, $reportsByIDs, $day)
    {
        $reportData = [];
        $reportPnlData = [];
        $DataArray = [];

        if (!empty($reportsByIDs)) {
            $reportData = report_summarize::filterDateRange($start_date, $end_date)
                ->filteroperator($reportsByIDs)
                ->ReportDataSumByDays($day)
                ->groupBy('operator_id')
                ->get()->toArray();
        }

        return $reportData;
    }

    function getReportsAlertData($reportData, $countriesOpereter, $operator, $country, $operator_shares)
    {
        $usdValue = isset($OperatorCountry['usd']) ? $OperatorCountry['usd'] : 1;

        $allColumnData = array();
        $gros_rev_arr = $mo_arr = $renewal_arr = $bill_rate_arr = [];

        if (!empty($reportData)) {
            foreach ($reportData as $key => $summariserow) {
                $operator_id = $summariserow['operator_id'];
                $country_id = $countriesOpereter[$summariserow['operator_id']]['id'];

                $gros_rev = isset($summariserow['gros_rev']) ? $summariserow['gros_rev'] : 0;
                $mo = isset($summariserow['mo']) ? $summariserow['mo'] : 0;
                $mt_success = isset($summariserow['mt_success']) ? $summariserow['mt_success'] : 0;
                $mt_failed = isset($summariserow['mt_failed']) ? $summariserow['mt_failed'] : 0;
                $total_subscriber = isset($summariserow['total']) ? $summariserow['total'] : 0;
                $renewal = $mt_success + $mt_failed;

                $billRate = UtilityReports::billRate($mt_success, $mt_failed, $total_subscriber);
                $billRate = sprintf('%0.2f', $billRate);

                if ($country_id == 7 || $country_id == 142) {
                    $gross_revenue = ($gros_rev / 1000) * $usdValue;
                } else {
                    $gross_revenue = $gros_rev * $usdValue;
                }

                $gros_rev_arr[$operator_id]['gros_rev'] = $gross_revenue;
                $mo_arr[$operator_id]['mo'] = $mo;
                $bill_rate_arr[$operator_id]['bill_rate'] = $billRate;
                $renewal_arr[$operator_id]['renewal'] = $renewal;
            }

            $allColumnData['gros_rev'] = $gros_rev_arr;
            $allColumnData['mo'] = $mo_arr;
            $allColumnData['bill_rate'] = $bill_rate_arr;
            $allColumnData['renewal'] = $renewal_arr;

            return $allColumnData;
        } else {
            return $allColumnData;
        }
    }

    // create target revenue
    public function createNewWeeklyCaps(Request $request)
    {
        $requestData = $request->all();
        $data = [];
        $DataArray = [];
        $WeeklyDataDetails = [];
        $Country = Country::all()->toArray();
        $countries = array();

        if (!empty($Country)) {
            foreach ($Country as $CountryI) {
                $countries[$CountryI['id']] = $CountryI;
            }
        }

        $data['selected_year'] = $year = isset($requestData['year']) ? $requestData['year'] : date('Y');
        $data['selected_month'] = $month = isset($requestData['month']) ? $requestData['month'] : date('m');
        $data['monthArray'] = [1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'];

        for ($i = date('Y'); $i >= 2020; $i--) {
            $data['years'][] = $i;
        }

        for ($i = 1; $i <= 12; $i++) {
            $data['months'][] = $i;
        }

        $Operators = Operator::Status(1)->get();

        if (!empty($Operators)) {
            foreach ($Operators as $operator) {
                $tmpOperators = array();
                $tmpOperators['operator'] = $operator;
                $country_id  = $operator->country_id;
                $tmpOperators['country'] = $countries[$country_id];
                $dataDetails[] = $tmpOperators;
            }
        }

        $key = $year . '-' . $month;

        foreach ($dataDetails as $detail) {
            $currency[$detail['country']['country']][$detail['operator']['operator_name']] = $detail['country']['currency_code'];

            $weeklyCaps = WeeklyCaps::filterCountry($detail['country']['id'])->filterOperator($detail['operator']['id_operator'])->filterMonth($key)->first();

            $DataArray[$detail['country']['country']][$detail['operator']['operator_name']]['country_id'] = $detail['country']['id'];

            $DataArray[$detail['country']['country']][$detail['operator']['operator_name']]['operator_id'] = $detail['operator']['id_operator'];

            $DataArray[$detail['country']['country']][$detail['operator']['operator_name']]['weekly_caps'] = isset($weeklyCaps['weekly_caps']) ? $weeklyCaps['weekly_caps'] : '';
        }

        $WeeklyDataDetails = $DataArray;

        return view('analytic.create_weekly_cap', compact('data', 'WeeklyDataDetails', 'currency'));
    }

    // store new weekly caps
    public function storeNewWeeklyCaps(Request $request)
    {
        $requestData = $request->all();

        $month = isset($requestData['month']) ? $requestData['month'] : '';
        $year = isset($requestData['year']) ? $requestData['year'] : '';

        $weekly_caps = isset($requestData['weekly_caps']) ? $requestData['weekly_caps'] : [];

        if (isset($weekly_caps) && !empty($weekly_caps)) {
            foreach ($weekly_caps as $rkey => $rvalue) {
                foreach ($rvalue as $country_id => $cvalue) {
                    foreach ($cvalue as $operator_id => $revenue) {
                        $weekly_cap = $weekly_caps[$rkey][$country_id][$operator_id];

                        $key = $year . '-' . $month;

                        if ($weekly_cap != '' && $weekly_cap != NULL)

                            $data[] = ['country_id' => $country_id, 'operator_id' => $operator_id, 'year' => $year, 'month' => $month, 'key' => $key, 'weekly_caps' => $weekly_cap];
                    }
                }
            }

            if (!empty($data)) {
                $response = array();
                $result = WeeklyCaps::upsert($data, ['country_id', 'operator_id', 'year', 'month'], ['key', 'weekly_caps']);

                if ($result > 0) {
                    $response['success'] = 1;
                    $response['error'] = 0;
                } else {
                    $response['success'] = 0;
                    $response['error'] = 1;
                }
            } else {
                $response['success'] = 0;
                $response['error'] = 1;
            }

            echo json_encode($response);
            exit(0);
        }
    }

     //analytic monitor daily operatorwise

     public function ReportMonitorOperatorWise(Request $request)
     {
         if (\Auth::user()->can('Monitor Operational')) {
             $CountryId = $req_CountryId = $request->country;
             $CompanyId = $req_CompanyId = $request->company;
             $BusinessType = $req_BusinessType = $request->business_type;
             $filterOperator = $req_filterOperator = $request->operatorId;
             $Start_date = $req_Start_date = trim($request->from);
             $end_date = $req_end_date = trim($request->to);

             if ($end_date <= $Start_date) {
                 $Start_date = $req_Start_date = trim($request->to);
                 $end_date = $req_end_date = $request->from;
             }

             $start_date = Carbon::now()->startOfMonth()->subDays(35)->format('Y-m-d');
             $startColumnDateDisplay = Carbon::now()->startOfMonth()->format('Y-m-d');
             $end_date = Carbon::now()->format('Y-m-d');
             $month = Carbon::now()->format('F Y');
             $today = Carbon::now()->format('Y-m-d');
             $companys = Company::get();

             /* filter Search Section */

             $showAllOperator = true;

             if ($request->filled('to') && $request->filled('from')) {
                 $start_date_input = new Carbon($req_Start_date);
                 $display_date_input = new Carbon($req_Start_date);

                 $start_date = $start_date_input->subDays(35)->format('Y-m-d');
                 $startColumnDateDisplay = $display_date_input->format('Y-m-d');

                 $end_date = $req_end_date;
                 $month = $display_date_input->format('F Y');
             } else {
                 $req_Start_date = date('Y-m-d', strtotime('-30 days'));
                 $req_end_date = date('Y-m-d');

                 $start_date_input = new Carbon($req_Start_date);
                 $display_date_input = new Carbon($req_Start_date);

                 $start_date = $start_date_input->subDays(35)->format('Y-m-d');
                 $startColumnDateDisplay = $display_date_input->format('Y-m-d');

                 $end_date = $req_end_date;
                 $month = $display_date_input->format('F Y');
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


             if (!empty($Country)) {
                 foreach ($Country as $CountryI) {
                     $countries[$CountryI['id']] = $CountryI;
                     $countrys[$CountryI['id']] = [];
                 }
             }

             $arrayOperatorsIds = $Operators->pluck('id_operator')->toArray();

             $Pnlreports = ReportsPnlsOperatorSummarizes::filterDateRange($start_date, $end_date)
                 ->filteroperator($arrayOperatorsIds)->orderBy('country_id')
                 ->orderBy('date', 'ASC')
                 ->get()
                 ->toArray();


                 $PnlDailtyReportDetailsController = new PnlDailtyReportDetailsController;
                 $reportsByIDs = $PnlDailtyReportDetailsController->getReportsByOperator($Pnlreports);
             // $reportsByIDs = $this->getReportsOperatorID($Pnlreports);
             $datesIndividual = Utility::getRangeDates($startColumnDateDisplay, $end_date);
             $no_of_days = Utility::getRangeDateNo($datesIndividual);
             $data = new ReportController;

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
                     $reportsColumnData = $data->getmonitorReportsDateWise($operator, $no_of_days, $reportsByIDs, $OperatorCountry);

                     // $reportsColumnData = $this->getReportsGrosRevDateWise($operator,$no_of_days,$reportsByIDs,$OperatorCountry);

                     $tmpOperators['month_string'] = $month;

                     $total_avg_t = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['tur'], $startColumnDateDisplay, $end_date);
                     $tmpOperators['tur']['dates'] = $reportsColumnData['tur'];
                     $tmpOperators['tur']['total'] = $total_avg_t['sum'];
                     $tmpOperators['tur']['t_mo_end'] = $total_avg_t['T_Mo_End'];;
                     $tmpOperators['tur']['avg'] = $total_avg_t['avg'];

                     $total_avg_t_bill = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['bill'], $startColumnDateDisplay, $end_date);
                     $tmpOperators['bill']['dates'] = $reportsColumnData['bill'];
                     $tmpOperators['bill']['total'] = $total_avg_t_bill['sum'];
                     $tmpOperators['bill']['t_mo_end'] = $total_avg_t_bill['T_Mo_End'];
                     $tmpOperators['bill']['avg'] = $total_avg_t_bill['avg'];

                     $total_avg_mo = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['mo'], $startColumnDateDisplay, $end_date);
                     $tmpOperators['mo']['dates'] = $reportsColumnData['mo'];
                     $tmpOperators['mo']['total'] = $total_avg_mo['sum'];
                     $tmpOperators['mo']['t_mo_end'] = $total_avg_mo['T_Mo_End'];
                     $tmpOperators['mo']['avg'] = $total_avg_mo['avg'];

                     $total_avg_roi = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['roi'], $startColumnDateDisplay, $end_date);
                     $tmpOperators['roi']['dates'] = $reportsColumnData['roi'];
                     $tmpOperators['roi']['total'] = $total_avg_roi['sum'];
                     $tmpOperators['roi']['t_mo_end'] = $total_avg_roi['T_Mo_End'];
                     $tmpOperators['roi']['avg'] = $total_avg_roi['avg'];

                     $total_avg_renewal = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['renewal'], $startColumnDateDisplay, $end_date);
                     $tmpOperators['renewal']['dates'] = $reportsColumnData['renewal'];
                     $tmpOperators['renewal']['total'] = $total_avg_renewal['sum'];
                     $tmpOperators['renewal']['t_mo_end'] = $total_avg_renewal['T_Mo_End'];
                     $tmpOperators['renewal']['avg'] = $total_avg_renewal['avg'];

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

                     $total_avg_active_subs = UtilityReports::calculateTotalSubscribe($operator,$reportsColumnData['active_subs'],$startColumnDateDisplay,$end_date);

                     $tmpOperators['active_subs']['dates'] = $reportsColumnData['active_subs'];
                     $tmpOperators['active_subs']['total'] = $total_avg_active_subs['sum'];
                     $tmpOperators['active_subs']['t_mo_end'] = $total_avg_active_subs['T_Mo_End'];
                     $tmpOperators['active_subs']['avg'] = $total_avg_active_subs['avg'];

                     $total_avg_cost_campaign = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['cost_campaign'],$startColumnDateDisplay,$end_date);

                     $tmpOperators['cost_campaign']['dates'] = $reportsColumnData['cost_campaign'];
                     $tmpOperators['cost_campaign']['total'] = $total_avg_cost_campaign['sum'];
                     $tmpOperators['cost_campaign']['t_mo_end'] = $total_avg_cost_campaign['T_Mo_End'];
                     $tmpOperators['cost_campaign']['avg'] = $total_avg_cost_campaign['avg'];

                     $total_avg_gros_rev_usd = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['gros_rev_usd'],$startColumnDateDisplay,$end_date);

                     $tmpOperators['gros_rev_usd']['dates'] = $reportsColumnData['gros_rev_usd'];
                     $tmpOperators['gros_rev_usd']['total'] = $total_avg_gros_rev_usd['sum'];
                     $tmpOperators['gros_rev_usd']['t_mo_end'] = $total_avg_gros_rev_usd['T_Mo_End'];
                     $tmpOperators['gros_rev_usd']['avg'] = $total_avg_gros_rev_usd['avg'];

                     $total_avg_gros_rev_usd = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['reg'],$startColumnDateDisplay,$end_date);

                     $tmpOperators['reg']['dates'] = $reportsColumnData['reg'];

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
                             if ($content_key != Carbon::now()->format('Y-m-d'))
                             $country_totelcost_campaign = $country_totelcost_campaign + (float)$content_sum[$content_key];
                             $content_arr[$content_key] = ['value' => $content_sum[$content_key]];
                             $cost_campaignPrevious = $content_sum[$content_key];

                         }
                         if (count($content_arr) > 1) {
                             $noofdays = count($content_arr)-1;
                             if($today > $end_date)
                             $noofdays = count($content_arr);

                             $totelCountryCosts[$country]['tur']['avg'] = $avg = $country_totelcost_campaign / $noofdays;

                             $reaming_day = Carbon::parse($end_date)->daysInMonth;
                             $reaming_day = $reaming_day - $noofdays;
                             $T_Mo_End = $country_totelcost_campaign + ($avg * $reaming_day);
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
                             if ($mo_key != Carbon::now()->format('Y-m-d'))
                             $country_totelcost_mo = $country_totelcost_mo + (float)$mo_sum[$mo_key];
                             $mo_arr[$mo_key] = ['value' => $mo_sum[$mo_key]];
                             $moPrevious = $mo_sum[$mo_key];

                         }
                         if (count($mo_arr) > 1) {

                             $noofdays = count($mo_arr)-1;
                             if($today > $end_date)
                             $noofdays = count($mo_arr);

                             $totelCountryCosts[$country]['mo']['avg'] = $avg = $country_totelcost_mo / $noofdays;

                             $reaming_day = Carbon::parse($end_date)->daysInMonth;
                             $reaming_day = $reaming_day - $noofdays;
                             $T_Mo_End = $country_totelcost_mo + ($avg * $reaming_day);
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
                             if ($active_subs_key != Carbon::now()->format('Y-m-d'))
                             $country_totelcost_active_subs = $country_totelcost_active_subs + (float)$active_subs_sum[$active_subs_key];
                             $active_subs_arr[$active_subs_key] = ['value' => $active_subs_sum[$active_subs_key]];
                             $active_subsPrevious = $active_subs_sum[$active_subs_key];

                         }
                         if (count($active_subs_arr) > 1) {

                             $noofdays = count($active_subs_arr)-1;
                             if($today > $end_date)
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
                             if ($dp_success_key != Carbon::now()->format('Y-m-d'))
                             $country_totelcost_dp_success = $country_totelcost_dp_success + (float)$dp_success_sum[$dp_success_key];
                             $dp_success_arr[$dp_success_key] = ['value' => $dp_success_sum[$dp_success_key]];
                             $dp_successPrevious = $dp_success_sum[$dp_success_key];

                         }
                         if (count($dp_success_arr) > 1) {

                             $noofdays = count($dp_success_arr)-1;
                             if($today > $end_date)
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
                             if ($dp_failed_key != Carbon::now()->format('Y-m-d'))
                             $country_totelcost_dp_failed = $country_totelcost_dp_failed + (float)$dp_failed_sum[$dp_failed_key];
                             $dp_failed_arr[$dp_failed_key] = ['value' => $dp_failed_sum[$dp_failed_key]];
                             $dp_failedPrevious = $dp_failed_sum[$dp_failed_key];

                         }
                         if (count($dp_failed_arr) > 1) {

                             $noofdays = count($dp_failed_arr)-1;
                             if($today > $end_date)
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
                             if ($cost_campaign_key != Carbon::now()->format('Y-m-d'))
                             $country_totelcost_cost_campaign = $country_totelcost_cost_campaign + (float)$cost_campaign_sum[$cost_campaign_key];
                             $cost_campaign_arr[$cost_campaign_key] = ['value' => $cost_campaign_sum[$cost_campaign_key]];
                             $cost_campaignPrevious = $cost_campaign_sum[$cost_campaign_key];

                         }
                         if (count($cost_campaign_arr) > 1) {

                             $noofdays = count($cost_campaign_arr)-1;
                             if($today > $end_date)
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
                             if ($gros_rev_usd_key != Carbon::now()->format('Y-m-d'))
                             $country_totelcost_gros_rev_usd = $country_totelcost_gros_rev_usd + (float)$gros_rev_usd_sum[$gros_rev_usd_key];
                             $gros_rev_usd_arr[$gros_rev_usd_key] = ['value' => $gros_rev_usd_sum[$gros_rev_usd_key]];
                             $gros_rev_usdPrevious = $gros_rev_usd_sum[$gros_rev_usd_key];

                         }
                         if (count($gros_rev_usd_arr) > 1) {

                             $noofdays = count($gros_rev_usd_arr)-1;
                             if($today > $end_date)
                             $noofdays = count($gros_rev_usd_arr);

                             $totelCountryCosts[$country]['gros_rev_usd']['avg'] = $avg = $country_totelcost_gros_rev_usd / $noofdays;

                             $reaming_day = Carbon::parse($end_date)->daysInMonth;
                             $reaming_day = $reaming_day - $noofdays;
                             $T_Mo_End = $country_totelcost_mo + ($avg * $reaming_day);
                             $totelCountryCosts[$country]['gros_rev_usd']['t_mo_end'] = $T_Mo_End;
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
                             if ($reg_key != Carbon::now()->format('Y-m-d'))
                             $country_totelcost_reg = $country_totelcost_reg + (float)$reg_sum[$reg_key];
                             $reg_arr[$reg_key] = ['value' => $reg_sum[$reg_key]];
                             $regPrevious = $reg_sum[$reg_key];

                         }
                         if (count($reg_arr) > 1) {

                             $noofdays = count($reg_arr)-1;
                             if($today > $end_date)
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
                         if ($bill_key != Carbon::now()->format('Y-m-d'))
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
                             }
                             else
                             {
                               if($total_subscriber > 0)
                               {
                                 $billing_rate = ($mt_success/$total_subscriber)*100;
                               }
                               else
                               {
                                 $billing_rate = ($mt_success/$sent)*100;
                               }
                             }

                             if ($bill_key != Carbon::now()->format('Y-m-d'))
                             $country_totelcost_bill = $country_totelcost_bill + (float)$billing_rate;
                             $bill_arr[$bill_key] = ['value' => $billing_rate];
                             $bilPrevious = $bill_sum[$bill_key];

                         }
                         if (count($bill_arr) > 1) {

                             $noofdays = count($bill_arr)-1;
                             if($today > $end_date)
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
                         if ($roi_key != Carbon::now()->format('Y-m-d'))
                             $roi_sum[$roi_key] = $roi_sum[$roi_key] + (float)$roi_value['value'];
                         if (count($operaters) - 1 == $key) {

                             if ($flag == 5) {
                                 $roiPrevious = $roi_sum[$roi_key];
                                 $flag = 10;
                             }

                             $R1 = $gros_rev_usd_arr[$roi_key]['value'];
                             $R2 = $reg_arr[$roi_key]['value'];
                             $R3 = $R2 + $active_subs_arr[$roi_key]['value'] ;
                             $R4 = $cost_campaign_arr[$roi_key]['value'];
                             $R5 = $mo_arr[$roi_key]['value'];

                             if($R3 > 0)
                             {
                                 $arpu_30 = $R1 / $R3 ;
                             }

                             if($R5 > 0)
                             {
                                 $price_mo = $R4 / $R5 ;
                             }

                             if($arpu_30 > 0)
                             {
                                 $roi = $price_mo / $arpu_30 ;
                             }
                             $country_totelcost_roi = $country_totelcost_roi + (float)$roi;
                             $roi_arr[$roi_key] = ['value' => $roi];
                             $roiPrevious = $roi_sum[$roi_key];
                         }
                         if (count($roi_arr) > 1) {

                             $noofdays = count($roi_arr)-1;
                             if($today > $end_date)
                             $noofdays = count($roi_arr);

                             if ($roi_key == Carbon::now()->format('Y-m-d')){

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
                         $renewal_sum[$renewal_key] = $renewal_sum[$renewal_key] + (float)$renewal_value['value'];

                         if (count($operaters) - 1 == $key) {

                             if ($flag == 5) {
                                 $renewalPrevious = $renewal_sum[$renewal_key];
                                 $flag = 10;
                             }
                             if ($renewal_key != Carbon::now()->format('Y-m-d'))
                             $country_totelcost_renewal = $country_totelcost_renewal + (float)$renewal_sum[$renewal_key];
                             $renewal_arr[$renewal_key] = ['value' => $renewal_sum[$renewal_key]];
                             $renewalPrevious = $renewal_sum[$renewal_key];
                         }
                         if (count($renewal_arr) > 1) {
                             $noofdays = count($renewal_arr)-1;
                             if($today > $end_date)
                             $noofdays = count($renewal_arr);

                             $totelCountryCosts[$country]['renewal']['avg'] = $avg = $country_totelcost_renewal / $noofdays;

                             $reaming_day = Carbon::parse($end_date)->daysInMonth;
                             $reaming_day = $reaming_day - $noofdays;
                             $T_Mo_End = $country_totelcost_renewal + ($avg * $reaming_day);
                             $totelCountryCosts[$country]['renewal']['t_mo_end'] = $T_Mo_End;
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

             }

             $content_cuntry_sum = [];
             $content_cuntry_arr = [];
             $count = 0;
             $flag = 5;
             $cost_campaignPrevious = 0;
             $avg = $T_Mo_End = $country_totelcost_campaign = 0;
             $AllCuntryGrosRev['month_string'] = $month;
             $date = Carbon::parse($end_date)->format('Y-m-d');
             return view('report.monitor_daily_operator', compact('date','totelCountryCosts', 'AllCuntryGrosRev', 'no_of_days'));
         }
         else {
             return redirect()->back()->with('error', __('Permission Denied.'));
         }
     }

     //analytic monitor daily countrywise

    public function ReportMonitorCountryWise(Request $request)
    {
        if (\Auth::user()->can('Monitor Operational')) {
            $CountryId = $req_CountryId = $request->country;
            $CompanyId = $req_CompanyId = $request->company;
            $BusinessType = $req_BusinessType = $request->business_type;
            $filterOperator = $req_filterOperator = $request->operatorId;
            $Start_date = $req_Start_date = trim($request->from);
            $end_date = $req_end_date = trim($request->to);

            if ($end_date <= $Start_date) {
                $Start_date = $req_Start_date = trim($request->to);
                $end_date = $req_end_date = $request->from;
            }

            $start_date = Carbon::now()->startOfMonth()->subDays(35)->format('Y-m-d');
            $startColumnDateDisplay = Carbon::now()->startOfMonth()->format('Y-m-d');
            $end_date = Carbon::now()->format('Y-m-d');
            $month = Carbon::now()->format('F Y');
            $today = Carbon::now()->format('Y-m-d');
            $companys = Company::get();

            /* filter Search Section */

            $showAllOperator = true;

            if ($request->filled('to') && $request->filled('from')) {
                $start_date_input = new Carbon($req_Start_date);
                $display_date_input = new Carbon($req_Start_date);

                $start_date = $start_date_input->subDays(35)->format('Y-m-d');
                $startColumnDateDisplay = $display_date_input->format('Y-m-d');

                $end_date = $req_end_date;
                $month = $display_date_input->format('F Y');
            } else {
                $req_Start_date = date('Y-m-d', strtotime('-30 days'));
                $req_end_date = date('Y-m-d');

                $start_date_input = new Carbon($req_Start_date);
                $display_date_input = new Carbon($req_Start_date);

                $start_date = $start_date_input->subDays(35)->format('Y-m-d');
                $startColumnDateDisplay = $display_date_input->format('Y-m-d');

                $end_date = $req_end_date;
                $month = $display_date_input->format('F Y');
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


            if (!empty($Country)) {
                foreach ($Country as $CountryI) {
                    $countries[$CountryI['id']] = $CountryI;
                    $countrys[$CountryI['id']] = [];
                }
            }

            $arrayOperatorsIds = $Operators->pluck('id_operator')->toArray();

            $Pnlreports = ReportsPnlsOperatorSummarizes::filterDateRange($start_date, $end_date)
                ->filteroperator($arrayOperatorsIds)
                ->get()
                ->toArray();


                $PnlDailtyReportDetailsController = new PnlDailtyReportDetailsController;
                $reportsByIDs = $PnlDailtyReportDetailsController->getReportsByOperator($Pnlreports);
            // $reportsByIDs = $this->getReportsOperatorID($Pnlreports);
            $datesIndividual = Utility::getRangeDates($startColumnDateDisplay, $end_date);
            $no_of_days = Utility::getRangeDateNo($datesIndividual);
            $data = new ReportController;
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
                    $reportsColumnData = $data->getmonitorReportsDateWise($operator, $no_of_days, $reportsByIDs, $OperatorCountry);

                    // $reportsColumnData = $this->getReportsGrosRevDateWise($operator,$no_of_days,$reportsByIDs,$OperatorCountry);

                    $tmpOperators['month_string'] = $month;

                    $total_avg_t = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['tur'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['tur']['dates'] = $reportsColumnData['tur'];
                    $tmpOperators['tur']['total'] = $total_avg_t['sum'];
                    $tmpOperators['tur']['t_mo_end'] = $total_avg_t['T_Mo_End'];;
                    $tmpOperators['tur']['avg'] = $total_avg_t['avg'];

                    $total_avg_t_bill = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['bill'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['bill']['dates'] = $reportsColumnData['bill'];
                    $tmpOperators['bill']['total'] = $total_avg_t_bill['sum'];
                    $tmpOperators['bill']['t_mo_end'] = $total_avg_t_bill['T_Mo_End'];
                    $tmpOperators['bill']['avg'] = $total_avg_t_bill['avg'];

                    $total_avg_mo = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['mo'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['mo']['dates'] = $reportsColumnData['mo'];
                    $tmpOperators['mo']['total'] = $total_avg_mo['sum'];
                    $tmpOperators['mo']['t_mo_end'] = $total_avg_mo['T_Mo_End'];
                    $tmpOperators['mo']['avg'] = $total_avg_mo['avg'];

                    $total_avg_roi = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['roi'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['roi']['dates'] = $reportsColumnData['roi'];
                    $tmpOperators['roi']['total'] = $total_avg_roi['sum'];
                    $tmpOperators['roi']['t_mo_end'] = $total_avg_roi['T_Mo_End'];
                    $tmpOperators['roi']['avg'] = $total_avg_roi['avg'];

                    $total_avg_renewal = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['renewal'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['renewal']['dates'] = $reportsColumnData['renewal'];
                    $tmpOperators['renewal']['total'] = $total_avg_renewal['sum'];
                    $tmpOperators['renewal']['t_mo_end'] = $total_avg_renewal['T_Mo_End'];
                    $tmpOperators['renewal']['avg'] = $total_avg_renewal['avg'];

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

                    $total_avg_active_subs = UtilityReports::calculateTotalSubscribe($operator,$reportsColumnData['active_subs'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['active_subs']['dates'] = $reportsColumnData['active_subs'];
                    $tmpOperators['active_subs']['total'] = $total_avg_active_subs['sum'];
                    $tmpOperators['active_subs']['t_mo_end'] = $total_avg_active_subs['T_Mo_End'];
                    $tmpOperators['active_subs']['avg'] = $total_avg_active_subs['avg'];

                    $total_avg_cost_campaign = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['cost_campaign'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['cost_campaign']['dates'] = $reportsColumnData['cost_campaign'];
                    $tmpOperators['cost_campaign']['total'] = $total_avg_cost_campaign['sum'];
                    $tmpOperators['cost_campaign']['t_mo_end'] = $total_avg_cost_campaign['T_Mo_End'];
                    $tmpOperators['cost_campaign']['avg'] = $total_avg_cost_campaign['avg'];

                    $total_avg_gros_rev_usd = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['gros_rev_usd'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['gros_rev_usd']['dates'] = $reportsColumnData['gros_rev_usd'];
                    $tmpOperators['gros_rev_usd']['total'] = $total_avg_gros_rev_usd['sum'];
                    $tmpOperators['gros_rev_usd']['t_mo_end'] = $total_avg_gros_rev_usd['T_Mo_End'];
                    $tmpOperators['gros_rev_usd']['avg'] = $total_avg_gros_rev_usd['avg'];

                    $total_avg_gros_rev_usd = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['reg'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['reg']['dates'] = $reportsColumnData['reg'];

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
                            if ($content_key != Carbon::now()->format('Y-m-d'))
                            $country_totelcost_campaign = $country_totelcost_campaign + (float)$content_sum[$content_key];
                            $content_arr[$content_key] = ['value' => $content_sum[$content_key]];
                            $cost_campaignPrevious = $content_sum[$content_key];

                        }
                        if (count($content_arr) > 1) {
                            $noofdays = count($content_arr)-1;
                            if($today > $end_date)
                            $noofdays = count($content_arr);

                            $totelCountryCosts[$country]['tur']['avg'] = $avg = $country_totelcost_campaign / $noofdays;

                            $reaming_day = Carbon::parse($end_date)->daysInMonth;
                            $reaming_day = $reaming_day - $noofdays;
                            $T_Mo_End = $country_totelcost_campaign + ($avg * $reaming_day);
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
                            if ($mo_key != Carbon::now()->format('Y-m-d'))
                            $country_totelcost_mo = $country_totelcost_mo + (float)$mo_sum[$mo_key];
                            $mo_arr[$mo_key] = ['value' => $mo_sum[$mo_key]];
                            $moPrevious = $mo_sum[$mo_key];

                        }
                        if (count($mo_arr) > 1) {

                            $noofdays = count($mo_arr)-1;
                            if($today > $end_date)
                            $noofdays = count($mo_arr);

                            $totelCountryCosts[$country]['mo']['avg'] = $avg = $country_totelcost_mo / $noofdays;

                            $reaming_day = Carbon::parse($end_date)->daysInMonth;
                            $reaming_day = $reaming_day - $noofdays;
                            $T_Mo_End = $country_totelcost_mo + ($avg * $reaming_day);
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
                            if ($active_subs_key != Carbon::now()->format('Y-m-d'))
                            $country_totelcost_active_subs = $country_totelcost_active_subs + (float)$active_subs_sum[$active_subs_key];
                            $active_subs_arr[$active_subs_key] = ['value' => $active_subs_sum[$active_subs_key]];
                            $active_subsPrevious = $active_subs_sum[$active_subs_key];

                        }
                        if (count($active_subs_arr) > 1) {

                            $noofdays = count($active_subs_arr)-1;
                            if($today > $end_date)
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
                            if ($dp_success_key != Carbon::now()->format('Y-m-d'))
                            $country_totelcost_dp_success = $country_totelcost_dp_success + (float)$dp_success_sum[$dp_success_key];
                            $dp_success_arr[$dp_success_key] = ['value' => $dp_success_sum[$dp_success_key]];
                            $dp_successPrevious = $dp_success_sum[$dp_success_key];

                        }
                        if (count($dp_success_arr) > 1) {

                            $noofdays = count($dp_success_arr)-1;
                            if($today > $end_date)
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
                            if ($dp_failed_key != Carbon::now()->format('Y-m-d'))
                            $country_totelcost_dp_failed = $country_totelcost_dp_failed + (float)$dp_failed_sum[$dp_failed_key];
                            $dp_failed_arr[$dp_failed_key] = ['value' => $dp_failed_sum[$dp_failed_key]];
                            $dp_failedPrevious = $dp_failed_sum[$dp_failed_key];

                        }
                        if (count($dp_failed_arr) > 1) {

                            $noofdays = count($dp_failed_arr)-1;
                            if($today > $end_date)
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
                            if ($cost_campaign_key != Carbon::now()->format('Y-m-d'))
                            $country_totelcost_cost_campaign = $country_totelcost_cost_campaign + (float)$cost_campaign_sum[$cost_campaign_key];
                            $cost_campaign_arr[$cost_campaign_key] = ['value' => $cost_campaign_sum[$cost_campaign_key]];
                            $cost_campaignPrevious = $cost_campaign_sum[$cost_campaign_key];

                        }
                        if (count($cost_campaign_arr) > 1) {

                            $noofdays = count($cost_campaign_arr)-1;
                            if($today > $end_date)
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
                            if ($gros_rev_usd_key != Carbon::now()->format('Y-m-d'))
                            $country_totelcost_gros_rev_usd = $country_totelcost_gros_rev_usd + (float)$gros_rev_usd_sum[$gros_rev_usd_key];
                            $gros_rev_usd_arr[$gros_rev_usd_key] = ['value' => $gros_rev_usd_sum[$gros_rev_usd_key]];
                            $gros_rev_usdPrevious = $gros_rev_usd_sum[$gros_rev_usd_key];

                        }
                        if (count($gros_rev_usd_arr) > 1) {

                            $noofdays = count($gros_rev_usd_arr)-1;
                            if($today > $end_date)
                            $noofdays = count($gros_rev_usd_arr);

                            $totelCountryCosts[$country]['gros_rev_usd']['avg'] = $avg = $country_totelcost_gros_rev_usd / $noofdays;

                            $reaming_day = Carbon::parse($end_date)->daysInMonth;
                            $reaming_day = $reaming_day - $noofdays;
                            $T_Mo_End = $country_totelcost_mo + ($avg * $reaming_day);
                            $totelCountryCosts[$country]['gros_rev_usd']['t_mo_end'] = $T_Mo_End;
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
                            if ($reg_key != Carbon::now()->format('Y-m-d'))
                            $country_totelcost_reg = $country_totelcost_reg + (float)$reg_sum[$reg_key];
                            $reg_arr[$reg_key] = ['value' => $reg_sum[$reg_key]];
                            $regPrevious = $reg_sum[$reg_key];

                        }
                        if (count($reg_arr) > 1) {

                            $noofdays = count($reg_arr)-1;
                            if($today > $end_date)
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
                        if ($bill_key != Carbon::now()->format('Y-m-d'))
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
                            }
                            else
                            {
                              if($total_subscriber > 0)
                              {
                                $billing_rate = ($mt_success/$total_subscriber)*100;
                              }
                              else
                              {
                                $billing_rate = ($mt_success/$sent)*100;
                              }
                            }

                            if ($bill_key != Carbon::now()->format('Y-m-d'))
                            $country_totelcost_bill = $country_totelcost_bill + (float)$billing_rate;
                            $bill_arr[$bill_key] = ['value' => $billing_rate];
                            $bilPrevious = $bill_sum[$bill_key];

                        }
                        if (count($bill_arr) > 1) {

                            $noofdays = count($bill_arr)-1;
                            if($today > $end_date)
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
                        if ($roi_key != Carbon::now()->format('Y-m-d'))
                            $roi_sum[$roi_key] = $roi_sum[$roi_key] + (float)$roi_value['value'];
                        if (count($operaters) - 1 == $key) {

                            if ($flag == 5) {
                                $roiPrevious = $roi_sum[$roi_key];
                                $flag = 10;
                            }

                            $R1 = $gros_rev_usd_arr[$roi_key]['value'];
                            $R2 = $reg_arr[$roi_key]['value'];
                            $R3 = $R2 + $active_subs_arr[$roi_key]['value'] ;
                            $R4 = $cost_campaign_arr[$roi_key]['value'];
                            $R5 = $mo_arr[$roi_key]['value'];

                            if($R3 > 0)
                            {
                                $arpu_30 = $R1 / $R3 ;
                            }

                            if($R5 > 0)
                            {
                                $price_mo = $R4 / $R5 ;
                            }

                            if($arpu_30 > 0)
                            {
                                $roi = $price_mo / $arpu_30 ;
                            }
                            $country_totelcost_roi = $country_totelcost_roi + (float)$roi;
                            $roi_arr[$roi_key] = ['value' => $roi];
                            $roiPrevious = $roi_sum[$roi_key];
                        }
                        if (count($roi_arr) > 1) {

                            $noofdays = count($roi_arr)-1;
                            if($today > $end_date)
                            $noofdays = count($roi_arr);

                            if ($roi_key == Carbon::now()->format('Y-m-d')){

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
                        $renewal_sum[$renewal_key] = $renewal_sum[$renewal_key] + (float)$renewal_value['value'];
                        if ($renewal_key != Carbon::now()->format('Y-m-d'))
                            $renewal_sum[$renewal_key] = $renewal_sum[$renewal_key] + (float)$renewal_value['value'];
                        if (count($operaters) - 1 == $key) {

                            if ($flag == 5) {
                                $renewalPrevious = $renewal_sum[$renewal_key];
                                $flag = 10;
                            }
                            if ($renewal_key != Carbon::now()->format('Y-m-d'))
                            $country_totelcost_renewal = $country_totelcost_renewal + (float)$renewal_sum[$renewal_key];
                            $renewal_arr[$renewal_key] = ['value' => $renewal_sum[$renewal_key]];
                            $renewalPrevious = $renewal_sum[$renewal_key];
                        }
                        if (count($renewal_arr) > 1) {
                            $noofdays = count($renewal_arr)-1;
                            if($today > $end_date)
                            $noofdays = count($renewal_arr);

                            $totelCountryCosts[$country]['renewal']['avg'] = $avg = $country_totelcost_renewal / $noofdays;

                            $reaming_day = Carbon::parse($end_date)->daysInMonth;
                            $reaming_day = $reaming_day - $noofdays;
                            $T_Mo_End = $country_totelcost_renewal + ($avg * $reaming_day);
                            $totelCountryCosts[$country]['renewal']['t_mo_end'] = $T_Mo_End;
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

            }

            $content_cuntry_sum = [];
            $content_cuntry_arr = [];
            $count = 0;
            $flag = 5;
            $cost_campaignPrevious = 0;
            $avg = $T_Mo_End = $country_totelcost_campaign = 0;
            $AllCuntryGrosRev['month_string'] = $month;
            $date = Carbon::parse($end_date)->format('Y-m-d');
            return view('report.monitor_daily_country', compact('totelCountryCosts', 'date','AllCuntryGrosRev', 'no_of_days'));
        }
        else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }
}
