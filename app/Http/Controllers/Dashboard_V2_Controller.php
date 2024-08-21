<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Session;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\report_summarize;
use App\Models\ReportsPnlsOperatorSummarizes;
use App\Models\Operator;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use App\Models\Company;
use App\Models\Country;
use App\Models\User;
use App\Models\ReportsSummarizeDashbroads;
use App\Models\ReportSummeriseUsers;
use App\Models\role_operators;
use App\Models\CompanyOperators;
use App\common\Utility;
use App\common\UtilityReports;
use App\common\UtilityDashboard;
use App\Models\NotificationDeployment;
use App\Models\NotificationIncident;
use Config;

class Dashboard_V2_Controller extends Controller
{
    public function index(Request $request)
    {
        if (Auth::check()) {
            $displayCountries = array();
            $records = array();
            $DataArray = array();
            $sumemry = array();

            $CountryId = $req_CountryId = $request->country;
            $CompanyId = $req_CompanyId = $request->company;
            $BusinessType = $req_BusinessType = $request->business_type;
            $filterOperator = $req_filterOperator = $request->operatorId;
            $showAllOperator = true;

            if ($request->filled('company') && $req_CompanyId != "allcompany"  && !$request->filled('operatorId')) {
                $companies = Company::Find($req_CompanyId);
                $Operators_company = array();

                if (!empty($companies)) {
                    $Operators_company = $companies->company_operators->pluck('operator_id')->toArray();
                    $Operators = Operator::with('revenueshare')
                        ->Status(1)
                        ->GetOperatorByOperatorId($Operators_company)
                        ->get();
                }

                $showAllOperator = false;
            }

            (!$request->filled('company')) ? $req_CompanyId = "allcompany" : false;

            if ($request->filled('company') && $request->filled('country') && !$request->filled('operatorId')) {
                $Countrys[0] = Country::with('operators')->Find($CountryId);
                $data = [
                    'id' => $req_CountryId,
                    'company' => $req_CompanyId,
                ];

                $requestobj = new Request($data);
                $ReportControllerobj = new ReportController;
                $Operators = $ReportControllerobj->userFilterOperator($requestobj);
                $CountryFlag = false;
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

            if ($request->filled('operatorId')) {
                $Operators = Operator::with('revenueshare')
                    ->Status(1)
                    ->GetOperatorByOperatorId($filterOperator)
                    ->get();

                $showAllOperator = false;
            }

            if ($showAllOperator) {
                $Operators = Operator::with('revenueshare')->Status(1)->get();
            }

            if (!isset($Operators)) {
                $request->session()->flash('alert-success', 'User was successful added!');
                return redirect()->back();
            }

            $arrayOperatorsIds = $Operators->pluck('id_operator')->toArray();

            $DashboardDatas = ReportsSummarizeDashbroads::filterOperator($arrayOperatorsIds)->get()->toArray();

            $start_date = Carbon::yesterday()->subDays(30)->format('Y-m-d');
            $end_date = Carbon::yesterday()->format('Y-m-d');
            $date = Carbon::now()->format('Y-m-d');
            $reports = ReportsPnlsOperatorSummarizes::filterOperator($arrayOperatorsIds)
                ->OperatorNotNull()
                ->filterDateRange($start_date, $end_date)
                ->SumOfRoiDataOperator()
                ->get()
                ->toArray();

            $active_subs = ReportsPnlsOperatorSummarizes::filteroperator($arrayOperatorsIds)
                ->where(['date' => $date])
                ->TotalOperator()
                ->get()
                ->toArray();

            $DashboardOperators = $this->getDashboardByOperatorID($DashboardDatas);
            $reportsByOperatorIDs = $this->getReportsByOperatorID($reports);
            $active_subsByOperatorIDs = $this->getReportsByOperatorID($active_subs);

            $user = Auth::user();
            $user_id = $user->id;
            $user_type = $user->type;
            $allowAllOperator = $user->WhowAccessAlOperator($user_type);

            if (!$allowAllOperator) {
                $current_start_date = Carbon::now()->startOfMonth()->format('Y-m-d');
                $current_end_date = Carbon::yesterday()->format('Y-m-d');

                $last_start_date = Carbon::now()->startOfMonth()->subMonth()->format('Y-m-d');
                $last_date = Carbon::now()->startOfMonth()->subMonthsNoOverflow();
                $last_end_date = $last_date->endOfMonth()->toDateString();

                $prev_start_date = Carbon::now()->startOfMonth()->subMonth()->subMonth()->format('Y-m-d');
                $firstDayofPreviousMonth = $last_date->startOfMonth()->subMonthsNoOverflow();
                $prev_end_date = $firstDayofPreviousMonth->endOfMonth()->toDateString();

                $UserOperatorServices = Session::get('userOperatorService');

                $arrayOperatorsIds = $UserOperatorServices['id_operators'];

                $arrayServicesIds = $UserOperatorServices['id_services'];

                $userOperators = Operator::with('revenueshare', 'country')
                    ->filteroperator($arrayOperatorsIds)
                    ->get()
                    ->toArray();

                foreach ($userOperators as $key => $value) {
                    if (empty($value['revenueshare'])) {
                        $userOperators[$key]['revenueshare']['merchant_revenue_share'] = 100;
                    }
                }

                $userOperatorsIDs = $this->getReportsByOperatorID($userOperators);

                $currentuserreports = ReportSummeriseUsers::filteroperator($arrayOperatorsIds)
                    ->UserdataOperator()
                    ->filterDateRange($current_start_date, $current_end_date)
                    ->User($user_id)
                    ->orderBy('operator_id')
                    ->get()
                    ->toArray();

                $currentuserreportsByOperatorIDs = $this->getUserReportsByOperatorID($currentuserreports);

                if (!empty($currentuserreportsByOperatorIDs)) {
                    foreach ($currentuserreportsByOperatorIDs as $key => $value) {
                        $DashboardOperators[$key]['current_revenue'] = isset($value['gros_rev']) ? $value['gros_rev'] : 0;
                        $DashboardOperators[$key]['current_revenue_usd'] = isset($value['gros_rev']) ? $value['gros_rev'] * $userOperatorsIDs[$key]['country']['usd'] : 0;
                        $DashboardOperators[$key]['current_gross_revenue_usd'] = isset($value['gros_rev']) ? $value['gros_rev'] * $userOperatorsIDs[$key]['country']['usd'] * ($userOperatorsIDs[$key]['revenueshare']['merchant_revenue_share'] / 100) : 0;

                        $DashboardOperators[$key]['last_revenue'] = 0;
                        $DashboardOperators[$key]['last_revenue_usd'] = 0;
                        $DashboardOperators[$key]['last_gross_revenue_usd'] = 0;
                        $DashboardOperators[$key]['prev_revenue'] = 0;
                        $DashboardOperators[$key]['prev_revenue_usd'] = 0;
                        $DashboardOperators[$key]['prev_gross_revenue_usd'] = 0;
                    }
                }

                $prevuserreports = ReportSummeriseUsers::filteroperator($arrayOperatorsIds)
                    ->UserdataOperator()
                    ->filterDateRange($prev_start_date, $prev_end_date)
                    ->User($user_id)
                    ->orderBy('operator_id')
                    ->get()
                    ->toArray();

                $prevuserreportsByOperatorIDs = $this->getUserReportsByOperatorID($prevuserreports);

                if (!empty($prevuserreportsByOperatorIDs)) {
                    foreach ($prevuserreportsByOperatorIDs as $key => $value) {
                        $DashboardOperators[$key]['prev_revenue'] = isset($value['gros_rev']) ? $value['gros_rev'] : 0;
                        $DashboardOperators[$key]['prev_revenue_usd'] = isset($value['gros_rev']) ? $value['gros_rev'] * $userOperatorsIDs[$key]['country']['usd'] : 0;
                        $DashboardOperators[$key]['prev_gross_revenue_usd'] = isset($value['gros_rev']) ? $value['gros_rev'] * $userOperatorsIDs[$key]['country']['usd'] * ($userOperatorsIDs[$key]['revenueshare']['merchant_revenue_share'] / 100) : 0;
                    }
                }

                $lastuserreports = ReportSummeriseUsers::filteroperator($arrayOperatorsIds)
                    ->UserdataOperator()
                    ->filterDateRange($last_start_date, $last_end_date)
                    ->User($user_id)
                    ->orderBy('operator_id')
                    ->get()
                    ->toArray();

                $lastuserreportsByOperatorIDs = $this->getUserReportsByOperatorID($lastuserreports);

                if (!empty($lastuserreportsByOperatorIDs)) {
                    foreach ($lastuserreportsByOperatorIDs as $key => $value) {
                        $DashboardOperators[$key]['last_revenue'] = isset($value['gros_rev']) ? $value['gros_rev'] : 0;
                        $DashboardOperators[$key]['last_revenue_usd'] = isset($value['gros_rev']) ? $value['gros_rev'] * $userOperatorsIDs[$key]['country']['usd'] : 0;
                        $DashboardOperators[$key]['last_gross_revenue_usd'] = isset($value['gros_rev']) ? $value['gros_rev'] * $userOperatorsIDs[$key]['country']['usd'] * ($userOperatorsIDs[$key]['revenueshare']['merchant_revenue_share'] / 100) : 0;
                    }
                }
            }

            $Country = Country::all();
            $countries = array();

            if (!empty($Country)) {
                foreach ($Country as $CountryI) {
                    $countries[$CountryI->id] = $CountryI;
                }
            }

            if (!empty($Operators)) {
                foreach ($Operators as $operator) {
                    $tmpOperators = array();
                    $tmpOperators['operators'] = $operator;
                    $country_id = $operator->country_id;
                    $id_operator = $operator->id_operator;
                    $contain_id = Arr::exists($countries, $country_id);

                    if ($contain_id) {
                        $tmpOperators['country'] = $countries[$country_id];
                    }

                    if (isset($DashboardOperators[$id_operator])) {
                        $tmpOperators['reports'] = $DashboardOperators[$id_operator];
                    }

                    if (isset($reportsByOperatorIDs[$id_operator])) {
                        $tmpOperators['reports']['pnl_details'] = $reportsByOperatorIDs[$id_operator];
                    }

                    if (isset($active_subsByOperatorIDs[$id_operator])) {
                        $tmpOperators['reports']['total'] = $active_subsByOperatorIDs[$id_operator];
                    }

                    $sumemry[] = $tmpOperators;
                }
            }

            if (!empty($sumemry)) {
                foreach ($sumemry as $key => $sumemries) {
                    $country_id = $sumemries['country']['id'];
                    $displayCountries[$country_id]['country'] = $sumemries['country'];
                    $displayCountries[$country_id]['operators'][] = $sumemries;
                }
            }

            if (isset($displayCountries) && !empty($displayCountries)) {
                foreach ($displayCountries as $key => $records) {
                    $tempoperatorsCountry = array();
                    $tempoperatorsCountry['country'] = $records['country'];
                    $tempoperatorsCountry['operator_count'] = 0.0;
                    $AllOperators = $records['operators'];

                    if (isset($AllOperators) && !empty($AllOperators)) {
                        $tempoperatorsCountry['operator_count'] = count($AllOperators);
                        $reportsColumnData = UtilityDashboard::reArrangeContryDashboardData($AllOperators);
                        // dd($reportsColumnData);
                        foreach ($reportsColumnData as $key => $value) {
                            $tempoperatorsCountry[$key] = $value;
                        }

                        $DataArray[] = $tempoperatorsCountry;
                    }
                }
            }

            foreach ($DataArray as $key => $value) {
                $notificationsDeployment = NotificationDeployment::where('country_id', $value['country']['id'])->orderByDesc('maintenance_end')->count();
                $notificationsIncident = NotificationIncident::where('country_id', $value['country']['id'])->orderByDesc('time_incident')->count();

                // dd($notificationsDeployment);
                $CurrentRevClass = $this->classPercentage($value['last_revenue_usd'], $value['current_revenue_usd']);
                $DataArray[$key]['current_revenue_usd_percentage'] = $CurrentRevClass['percentage'];
                $DataArray[$key]['current_revenue_usd_class'] = $CurrentRevClass['class'];
                $DataArray[$key]['current_revenue_usd_arrow'] = $CurrentRevClass['arrow'];

                $CurrentRevPercentage = $this->classPercentage($value['last_revenue_usd'], $value['estimated_revenue_usd']);
                $DataArray[$key]['estimated_revenue_usd_percentage'] = $CurrentRevPercentage['percentage'];
                $DataArray[$key]['estimated_revenue_usd_class'] = $CurrentRevPercentage['class'];
                $DataArray[$key]['estimated_revenue_usd_arrow'] = $CurrentRevPercentage['arrow'];

                $CurrentAvgRevPercentage = $this->classPercentage($value['last_avg_revenue_usd'], $value['estimated_avg_revenue_usd']);
                $DataArray[$key]['estimated_avg_revenue_usd_percentage'] = $CurrentAvgRevPercentage['percentage'];
                $DataArray[$key]['estimated_avg_revenue_usd_class'] = $CurrentAvgRevPercentage['class'];
                $DataArray[$key]['estimated_avg_revenue_usd_arrow'] = $CurrentAvgRevPercentage['arrow'];

                $CurrentGRevClass = $this->classPercentage($value['last_gross_revenue_usd'], $value['current_gross_revenue_usd']);
                $DataArray[$key]['current_gross_revenue_usd_percentage'] = $CurrentGRevClass['percentage'];
                $DataArray[$key]['current_gross_revenue_usd_class'] = $CurrentGRevClass['class'];
                $DataArray[$key]['current_gross_revenue_usd_arrow'] = $CurrentGRevClass['arrow'];

                $CurrentNRevClass = $this->classPercentage($value['last_net_revenue_usd'], $value['current_net_revenue_usd']);
                $DataArray[$key]['current_net_revenue_usd_percentage'] = $CurrentNRevClass['percentage'];
                $DataArray[$key]['current_net_revenue_usd_class'] = $CurrentNRevClass['class'];
                $DataArray[$key]['current_net_revenue_usd_arrow'] = $CurrentNRevClass['arrow'];


                $CurrentGRevPercentage = $this->classPercentage($value['last_gross_revenue_usd'], $value['estimated_gross_revenue_usd']);
                $DataArray[$key]['estimated_gross_revenue_usd_percentage'] = $CurrentGRevPercentage['percentage'];
                $DataArray[$key]['estimated_gross_revenue_usd_class'] = $CurrentGRevPercentage['class'];
                $DataArray[$key]['estimated_gross_revenue_usd_arrow'] = $CurrentGRevPercentage['arrow'];

                $CurrentNRevPercentage = $this->classPercentage($value['last_net_revenue_usd'], $value['estimated_net_revenue_usd']);
                $DataArray[$key]['estimated_net_revenue_usd_percentage'] = $CurrentNRevPercentage['percentage'];
                $DataArray[$key]['estimated_net_revenue_usd_class'] = $CurrentNRevPercentage['class'];
                $DataArray[$key]['estimated_net_revenue_usd_arrow'] = $CurrentNRevPercentage['arrow'];

                $CurrentAvgGRevPercentage = $this->classPercentage($value['last_avg_gross_revenue_usd'], $value['estimated_avg_gross_revenue_usd']);
                $DataArray[$key]['estimated_avg_gross_revenue_usd_percentage'] = $CurrentAvgGRevPercentage['percentage'];
                $DataArray[$key]['estimated_avg_gross_revenue_usd_class'] = $CurrentAvgGRevPercentage['class'];
                $DataArray[$key]['estimated_avg_gross_revenue_usd_arrow'] = $CurrentAvgGRevPercentage['arrow'];

                $CurrentAvgNRevPercentage = $this->classPercentage($value['last_avg_net_revenue_usd'], $value['estimated_avg_net_revenue_usd']);
                $DataArray[$key]['estimated_avg_net_revenue_usd_percentage'] = $CurrentAvgNRevPercentage['percentage'];
                $DataArray[$key]['estimated_avg_net_revenue_usd_class'] = $CurrentAvgNRevPercentage['class'];
                $DataArray[$key]['estimated_avg_net_revenue_usd_arrow'] = $CurrentAvgNRevPercentage['arrow'];


                $CurrentMOClass = $this->classPercentage($value['last_total_mo'], $value['current_total_mo']);
                $DataArray[$key]['current_total_mo_percentage'] = $CurrentMOClass['percentage'];
                $DataArray[$key]['current_total_mo_class'] = $CurrentMOClass['class'];
                $DataArray[$key]['current_total_mo_arrow'] = $CurrentMOClass['arrow'];

                $CurrentRegClass = $this->classPercentage($value['last_mo'], $value['current_mo']);
                $DataArray[$key]['current_mo_percentage'] = $CurrentRegClass['percentage'];
                $DataArray[$key]['current_mo_class'] = $CurrentRegClass['class'];
                $DataArray[$key]['current_mo_arrow'] = $CurrentRegClass['arrow'];

                $CurrentMOPercentage = $this->classPercentage($value['last_total_mo'], $value['estimated_total_mo']);
                $DataArray[$key]['estimated_total_mo_percentage'] = $CurrentMOPercentage['percentage'];
                $DataArray[$key]['estimated_total_mo_class'] = $CurrentMOPercentage['class'];
                $DataArray[$key]['estimated_total_mo_arrow'] = $CurrentMOPercentage['arrow'];

                $CurrentAvgMOPercentage = $this->classPercentage($value['last_avg_mo'], $value['estimated_avg_mo']);
                $DataArray[$key]['estimated_avg_mo_percentage'] = $CurrentAvgMOPercentage['percentage'];
                $DataArray[$key]['estimated_avg_mo_class'] = $CurrentAvgMOPercentage['class'];
                $DataArray[$key]['estimated_avg_mo_arrow'] = $CurrentAvgMOPercentage['arrow'];

                $CurrentRegPercentage = $this->classPercentage($value['last_mo'], $value['estimated_mo']);
                $DataArray[$key]['estimated_mo_percentage'] = $CurrentRegPercentage['percentage'];
                $DataArray[$key]['estimated_mo_class'] = $CurrentRegPercentage['class'];
                $DataArray[$key]['estimated_mo_arrow'] = $CurrentRegPercentage['arrow'];

                $CurrentCostClass = $this->classPercentage($value['last_cost'], $value['current_cost']);
                $DataArray[$key]['current_cost_percentage'] = $CurrentCostClass['percentage'];
                $DataArray[$key]['current_cost_class'] = $CurrentCostClass['class'];
                $DataArray[$key]['current_cost_arrow'] = $CurrentCostClass['arrow'];

                $CurrentCostPercentage = $this->classPercentage($value['last_cost'], $value['estimated_cost']);
                $DataArray[$key]['estimated_cost_percentage'] = $CurrentCostPercentage['percentage'];
                $DataArray[$key]['estimated_cost_class'] = $CurrentCostPercentage['class'];
                $DataArray[$key]['estimated_cost_arrow'] = $CurrentCostPercentage['arrow'];

                $CurrentPriceMOClass = $this->classPercentage($value['last_price_mo'], $value['current_price_mo']);
                $DataArray[$key]['current_price_mo_percentage'] = $CurrentPriceMOClass['percentage'];
                $DataArray[$key]['current_price_mo_class'] = $CurrentPriceMOClass['class'];
                $DataArray[$key]['current_price_mo_arrow'] = $CurrentPriceMOClass['arrow'];

                $CurrentPriceMOPercentage = $this->classPercentage($value['last_price_mo'], $value['estimated_price_mo']);
                $DataArray[$key]['estimated_price_mo_percentage'] = $CurrentPriceMOPercentage['percentage'];
                $DataArray[$key]['estimated_price_mo_class'] = $CurrentPriceMOPercentage['class'];
                $DataArray[$key]['estimated_price_mo_arrow'] = $CurrentPriceMOPercentage['arrow'];

                $CurrentROIClass = $this->classPercentage($value['lastMonthROI'], $value['currentMonthROI']);
                $DataArray[$key]['currentMonthROI_percentage'] = $CurrentROIClass['percentage'];
                $DataArray[$key]['currentMonthROI_class'] = $CurrentROIClass['class'];
                $DataArray[$key]['currentMonthROI_arrow'] = $CurrentROIClass['arrow'];

                $CurrentROIPercentage = $this->classPercentage($value['lastMonthROI'], $value['estimatedMonthROI']);
                $DataArray[$key]['estimatedMonthROI_percentage'] = $CurrentROIPercentage['percentage'];
                $DataArray[$key]['estimatedMonthROI_class'] = $CurrentROIPercentage['class'];
                $DataArray[$key]['estimatedMonthROI_arrow'] = $CurrentROIPercentage['arrow'];

                $CurrentArpuClass = $this->classPercentage($value['last_30_arpu'], $value['current_30_arpu']);
                $DataArray[$key]['current_30_arpu_percentage'] = $CurrentArpuClass['percentage'];
                $DataArray[$key]['current_30_arpu_class'] = $CurrentArpuClass['class'];
                $DataArray[$key]['current_30_arpu_arrow'] = $CurrentArpuClass['arrow'];

                $CurrenArpuPercentage = $this->classPercentage($value['last_30_arpu'], $value['estimated_30_arpu']);
                $DataArray[$key]['estimated_30_arpu_percentage'] = $CurrenArpuPercentage['percentage'];
                $DataArray[$key]['estimated_30_arpu_class'] = $CurrenArpuPercentage['class'];
                $DataArray[$key]['estimated_30_arpu_arrow'] = $CurrenArpuPercentage['arrow'];

                $CurrentPnlClass = $this->classPercentage($value['last_pnl'], $value['current_pnl']);
                $DataArray[$key]['current_pnl_percentage'] = $CurrentPnlClass['percentage'];
                $DataArray[$key]['current_pnl_class'] = $CurrentPnlClass['class'];
                $DataArray[$key]['current_pnl_arrow'] = $CurrentPnlClass['arrow'];

                $CurrentPnlPercentage = $this->classPercentage($value['last_pnl'], $value['estimated_pnl']);
                $DataArray[$key]['estimated_pnl_percentage'] = $CurrentPnlPercentage['percentage'];
                $DataArray[$key]['estimated_pnl_class'] = $CurrentPnlPercentage['class'];
                $DataArray[$key]['estimated_pnl_arrow'] = $CurrentPnlPercentage['arrow'];

                $CurrentAvgPnlPercentage = $this->classPercentage($value['last_avg_pnl'], $value['estimated_avg_pnl']);
                $DataArray[$key]['estimated_avg_pnl_percentage'] = $CurrentAvgPnlPercentage['percentage'];
                $DataArray[$key]['estimated_avg_pnl_class'] = $CurrentAvgPnlPercentage['class'];
                $DataArray[$key]['estimated_avg_pnl_arrow'] = $CurrentAvgPnlPercentage['arrow'];


                $LastRevPercentage = $this->classPercentage($value['prev_revenue_usd'], $value['last_revenue_usd']);
                $DataArray[$key]['last_revenue_usd_percentage'] = $LastRevPercentage['percentage'];
                $DataArray[$key]['last_revenue_usd_class'] = $LastRevPercentage['class'];
                $DataArray[$key]['last_revenue_usd_arrow'] = $LastRevPercentage['arrow'];

                $LastAvgRevPercentage = $this->classPercentage($value['prev_avg_revenue_usd'], $value['last_avg_revenue_usd']);
                $DataArray[$key]['last_avg_revenue_usd_percentage'] = $LastAvgRevPercentage['percentage'];
                $DataArray[$key]['last_avg_revenue_usd_class'] = $LastAvgRevPercentage['class'];
                $DataArray[$key]['last_avg_revenue_usd_arrow'] = $LastAvgRevPercentage['arrow'];

                $LastGRevPercentage = $this->classPercentage($value['prev_gross_revenue_usd'], $value['last_gross_revenue_usd']);
                $DataArray[$key]['last_gross_revenue_usd_percentage'] = $LastGRevPercentage['percentage'];
                $DataArray[$key]['last_gross_revenue_usd_class'] = $LastGRevPercentage['class'];
                $DataArray[$key]['last_gross_revenue_usd_arrow'] = $LastGRevPercentage['arrow'];

                $LastNRevPercentage = $this->classPercentage($value['prev_net_revenue_usd'], $value['last_net_revenue_usd']);
                $DataArray[$key]['last_net_revenue_usd_percentage'] = $LastNRevPercentage['percentage'];
                $DataArray[$key]['last_net_revenue_usd_class'] = $LastNRevPercentage['class'];
                $DataArray[$key]['last_net_revenue_usd_arrow'] = $LastNRevPercentage['arrow'];

                $LastAvgGRevPercentage = $this->classPercentage($value['prev_avg_gross_revenue_usd'], $value['last_avg_gross_revenue_usd']);
                $DataArray[$key]['last_avg_gross_revenue_usd_percentage'] = $LastAvgGRevPercentage['percentage'];
                $DataArray[$key]['last_avg_gross_revenue_usd_class'] = $LastAvgGRevPercentage['class'];
                $DataArray[$key]['last_avg_gross_revenue_usd_arrow'] = $LastAvgGRevPercentage['arrow'];

                $LastAvgNRevPercentage = $this->classPercentage($value['prev_avg_net_revenue_usd'], $value['last_avg_net_revenue_usd']);
                $DataArray[$key]['last_avg_net_revenue_usd_percentage'] = $LastAvgNRevPercentage['percentage'];
                $DataArray[$key]['last_avg_net_revenue_usd_class'] = $LastAvgNRevPercentage['class'];
                $DataArray[$key]['last_avg_net_revenue_usd_arrow'] = $LastAvgNRevPercentage['arrow'];

                $LastMOPercentage = $this->classPercentage($value['prev_total_mo'], $value['last_total_mo']);
                $DataArray[$key]['last_total_mo_percentage'] = $LastMOPercentage['percentage'];
                $DataArray[$key]['last_total_mo_class'] = $LastMOPercentage['class'];
                $DataArray[$key]['last_total_mo_arrow'] = $LastMOPercentage['arrow'];

                $LastAvgMOPercentage = $this->classPercentage($value['prev_avg_mo'], $value['last_avg_mo']);
                $DataArray[$key]['last_avg_mo_percentage'] = $LastAvgMOPercentage['percentage'];
                $DataArray[$key]['last_avg_mo_class'] = $LastAvgMOPercentage['class'];
                $DataArray[$key]['last_avg_mo_arrow'] = $LastAvgMOPercentage['arrow'];

                $LastRegPercentage = $this->classPercentage($value['prev_mo'], $value['last_mo']);
                $DataArray[$key]['last_mo_percentage'] = $LastRegPercentage['percentage'];
                $DataArray[$key]['last_mo_class'] = $LastRegPercentage['class'];
                $DataArray[$key]['last_mo_arrow'] = $LastRegPercentage['arrow'];

                $LastCostPercentage = $this->classPercentage($value['prev_cost'], $value['last_cost']);
                $DataArray[$key]['last_cost_percentage'] = $LastCostPercentage['percentage'];
                $DataArray[$key]['last_cost_class'] = $LastCostPercentage['class'];
                $DataArray[$key]['last_cost_arrow'] = $LastCostPercentage['arrow'];

                $LastPriceMOPercentage = $this->classPercentage($value['prev_price_mo'], $value['last_price_mo']);
                $DataArray[$key]['last_price_mo_percentage'] = $LastPriceMOPercentage['percentage'];
                $DataArray[$key]['last_price_mo_class'] = $LastPriceMOPercentage['class'];
                $DataArray[$key]['last_price_mo_arrow'] = $LastPriceMOPercentage['arrow'];

                $LastROIPercentage = $this->classPercentage($value['previousMonthROI'], $value['lastMonthROI']);
                $DataArray[$key]['lastMonthROI_percentage'] = $LastROIPercentage['percentage'];
                $DataArray[$key]['lastMonthROI_class'] = $LastROIPercentage['class'];
                $DataArray[$key]['lastMonthROI_arrow'] = $LastROIPercentage['arrow'];

                $LastArpuPercentage = $this->classPercentage($value['prev_30_arpu'], $value['last_30_arpu']);
                $DataArray[$key]['last_30_arpu_percentage'] = $LastArpuPercentage['percentage'];
                $DataArray[$key]['last_30_arpu_class'] = $LastArpuPercentage['class'];
                $DataArray[$key]['last_30_arpu_arrow'] = $LastArpuPercentage['arrow'];

                $LastPnlPercentage = $this->classPercentage($value['prev_pnl'], $value['last_pnl']);
                $DataArray[$key]['last_pnl_percentage'] = $LastPnlPercentage['percentage'];
                $DataArray[$key]['last_pnl_class'] = $LastPnlPercentage['class'];
                $DataArray[$key]['last_pnl_arrow'] = $LastPnlPercentage['arrow'];

                $LastAvgPnlPercentage = $this->classPercentage($value['prev_avg_pnl'], $value['last_avg_pnl']);
                $DataArray[$key]['last_avg_pnl_percentage'] = $LastAvgPnlPercentage['percentage'];
                $DataArray[$key]['last_avg_pnl_class'] = $LastAvgPnlPercentage['class'];
                $DataArray[$key]['last_avg_pnl_arrow'] = $LastAvgPnlPercentage['arrow'];
                $DataArray[$key]['countNotification'] = $notificationsDeployment + $notificationsIncident;
            }

            $sumemry = $DataArray;
            $allDataSum = UtilityReports::DashboardAllDataSum($sumemry);

            $CurrentRevClass = $this->classPercentage($allDataSum['last_revenue_usd'], $allDataSum['current_revenue_usd']);
            $allDataSum['current_revenue_usd_percentage'] = $CurrentRevClass['percentage'];
            $allDataSum['current_revenue_usd_class'] = $CurrentRevClass['class'];
            $allDataSum['current_revenue_usd_arrow'] = $CurrentRevClass['arrow'];

            $CurrentRevPercentage = $this->classPercentage($allDataSum['last_revenue_usd'], $allDataSum['estimated_revenue_usd']);
            $allDataSum['estimated_revenue_usd_percentage'] = $CurrentRevPercentage['percentage'];
            $allDataSum['estimated_revenue_usd_class'] = $CurrentRevPercentage['class'];
            $allDataSum['estimated_revenue_usd_arrow'] = $CurrentRevPercentage['arrow'];

            $CurrentAvgRevPercentage = $this->classPercentage($allDataSum['last_avg_revenue_usd'], $allDataSum['estimated_avg_revenue_usd']);
            $allDataSum['estimated_avg_revenue_usd_percentage'] = $CurrentAvgRevPercentage['percentage'];
            $allDataSum['estimated_avg_revenue_usd_class'] = $CurrentAvgRevPercentage['class'];
            $allDataSum['estimated_avg_revenue_usd_arrow'] = $CurrentAvgRevPercentage['arrow'];

            $CurrentGRevClass = $this->classPercentage($allDataSum['last_gross_revenue_usd'], $allDataSum['current_gross_revenue_usd']);
            $allDataSum['current_gross_revenue_usd_percentage'] = $CurrentGRevClass['percentage'];
            $allDataSum['current_gross_revenue_usd_class'] = $CurrentGRevClass['class'];
            $allDataSum['current_gross_revenue_usd_arrow'] = $CurrentGRevClass['arrow'];


            $CurrentNRevClass = $this->classPercentage($allDataSum['last_net_revenue_usd'], $allDataSum['current_net_revenue_usd']);
            $allDataSum['current_net_revenue_usd_percentage'] = $CurrentNRevClass['percentage'];
            $allDataSum['current_net_revenue_usd_class'] = $CurrentNRevClass['class'];
            $allDataSum['current_net_revenue_usd_arrow'] = $CurrentNRevClass['arrow'];

            $CurrentGRevPercentage = $this->classPercentage($allDataSum['last_gross_revenue_usd'], $allDataSum['estimated_gross_revenue_usd']);
            $allDataSum['estimated_gross_revenue_usd_percentage'] = $CurrentGRevPercentage['percentage'];
            $allDataSum['estimated_gross_revenue_usd_class'] = $CurrentGRevPercentage['class'];
            $allDataSum['estimated_gross_revenue_usd_arrow'] = $CurrentGRevPercentage['arrow'];

            $CurrentAvgGRevPercentage = $this->classPercentage($allDataSum['last_avg_gross_revenue_usd'], $allDataSum['estimated_avg_gross_revenue_usd']);
            $allDataSum['estimated_avg_gross_revenue_usd_percentage'] = $CurrentAvgGRevPercentage['percentage'];
            $allDataSum['estimated_avg_gross_revenue_usd_class'] = $CurrentAvgGRevPercentage['class'];
            $allDataSum['estimated_avg_gross_revenue_usd_arrow'] = $CurrentAvgGRevPercentage['arrow'];


            $CurrentNRevPercentage = $this->classPercentage($allDataSum['last_net_revenue_usd'], $allDataSum['estimated_net_revenue_usd']);
            $allDataSum['estimated_net_revenue_usd_percentage'] = $CurrentNRevPercentage['percentage'];
            $allDataSum['estimated_net_revenue_usd_class'] = $CurrentNRevPercentage['class'];
            $allDataSum['estimated_net_revenue_usd_arrow'] = $CurrentNRevPercentage['arrow'];

            $CurrentAvgNRevPercentage = $this->classPercentage($allDataSum['last_avg_net_revenue_usd'], $allDataSum['estimated_avg_net_revenue_usd']);
            $allDataSum['estimated_avg_net_revenue_usd_percentage'] = $CurrentAvgNRevPercentage['percentage'];
            $allDataSum['estimated_avg_net_revenue_usd_class'] = $CurrentAvgNRevPercentage['class'];
            $allDataSum['estimated_avg_net_revenue_usd_arrow'] = $CurrentAvgNRevPercentage['arrow'];


            $CurrentMOClass = $this->classPercentage($allDataSum['last_total_mo'], $allDataSum['current_total_mo']);
            $allDataSum['current_total_mo_percentage'] = $CurrentMOClass['percentage'];
            $allDataSum['current_total_mo_class'] = $CurrentMOClass['class'];
            $allDataSum['current_total_mo_arrow'] = $CurrentMOClass['arrow'];

            $CurrentRegClass = $this->classPercentage($allDataSum['last_mo'], $allDataSum['current_mo']);
            $allDataSum['current_mo_percentage'] = $CurrentRegClass['percentage'];
            $allDataSum['current_mo_class'] = $CurrentRegClass['class'];
            $allDataSum['current_mo_arrow'] = $CurrentRegClass['arrow'];

            $CurrentMOPercentage = $this->classPercentage($allDataSum['last_total_mo'], $allDataSum['estimated_total_mo']);
            $allDataSum['estimated_total_mo_percentage'] = $CurrentMOPercentage['percentage'];
            $allDataSum['estimated_total_mo_class'] = $CurrentMOPercentage['class'];
            $allDataSum['estimated_total_mo_arrow'] = $CurrentMOPercentage['arrow'];

            $CurrentAvgMOPercentage = $this->classPercentage($allDataSum['last_avg_mo'], $allDataSum['estimated_avg_mo']);
            $allDataSum['estimated_avg_mo_percentage'] = $CurrentAvgMOPercentage['percentage'];
            $allDataSum['estimated_avg_mo_class'] = $CurrentAvgMOPercentage['class'];
            $allDataSum['estimated_avg_mo_arrow'] = $CurrentAvgMOPercentage['arrow'];

            $CurrentRegPercentage = $this->classPercentage($allDataSum['last_mo'], $allDataSum['estimated_mo']);
            $allDataSum['estimated_mo_percentage'] = $CurrentRegPercentage['percentage'];
            $allDataSum['estimated_mo_class'] = $CurrentRegPercentage['class'];
            $allDataSum['estimated_mo_arrow'] = $CurrentRegPercentage['arrow'];

            $CurrentCostClass = $this->classPercentage($allDataSum['last_cost'], $allDataSum['current_cost']);
            $allDataSum['current_cost_percentage'] = $CurrentCostClass['percentage'];
            $allDataSum['current_cost_class'] = $CurrentCostClass['class'];
            $allDataSum['current_cost_arrow'] = $CurrentCostClass['arrow'];

            $CurrentCostPercentage = $this->classPercentage($allDataSum['last_cost'], $allDataSum['estimated_cost']);
            $allDataSum['estimated_cost_percentage'] = $CurrentCostPercentage['percentage'];
            $allDataSum['estimated_cost_class'] = $CurrentCostPercentage['class'];
            $allDataSum['estimated_cost_arrow'] = $CurrentCostPercentage['arrow'];

            $CurrentPriceMOClass = $this->classPercentage($allDataSum['last_price_mo'], $allDataSum['current_price_mo']);
            $allDataSum['current_price_mo_percentage'] = $CurrentPriceMOClass['percentage'];
            $allDataSum['current_price_mo_class'] = $CurrentPriceMOClass['class'];
            $allDataSum['current_price_mo_arrow'] = $CurrentPriceMOClass['arrow'];

            $CurrentPriceMOPercentage = $this->classPercentage($allDataSum['last_price_mo'], $allDataSum['estimated_price_mo']);
            $allDataSum['estimated_price_mo_percentage'] = $CurrentPriceMOPercentage['percentage'];
            $allDataSum['estimated_price_mo_class'] = $CurrentPriceMOPercentage['class'];
            $allDataSum['estimated_price_mo_arrow'] = $CurrentPriceMOPercentage['arrow'];

            $CurrentROIClass = $this->classPercentage($allDataSum['lastMonthROI'], $allDataSum['currentMonthROI']);
            $allDataSum['currentMonthROI_percentage'] = $CurrentROIClass['percentage'];
            $allDataSum['currentMonthROI_class'] = $CurrentROIClass['class'];
            $allDataSum['currentMonthROI_arrow'] = $CurrentROIClass['arrow'];

            $CurrentROIPercentage = $this->classPercentage($allDataSum['lastMonthROI'], $allDataSum['estimatedMonthROI']);
            $allDataSum['estimatedMonthROI_percentage'] = $CurrentROIPercentage['percentage'];
            $allDataSum['estimatedMonthROI_class'] = $CurrentROIPercentage['class'];
            $allDataSum['estimatedMonthROI_arrow'] = $CurrentROIPercentage['arrow'];

            $CurrentArpuClass = $this->classPercentage($allDataSum['last_30_arpu'], $allDataSum['current_30_arpu']);
            $allDataSum['current_30_arpu_percentage'] = $CurrentArpuClass['percentage'];
            $allDataSum['current_30_arpu_class'] = $CurrentArpuClass['class'];
            $allDataSum['current_30_arpu_arrow'] = $CurrentArpuClass['arrow'];

            $CurrentArpuPercentage = $this->classPercentage($allDataSum['last_30_arpu'], $allDataSum['estimated_30_arpu']);
            $allDataSum['estimated_30_arpu_percentage'] = $CurrentArpuPercentage['percentage'];
            $allDataSum['estimated_30_arpu_class'] = $CurrentArpuPercentage['class'];
            $allDataSum['estimated_30_arpu_arrow'] = $CurrentArpuPercentage['arrow'];

            $CurrentPnlClass = $this->classPercentage($allDataSum['last_pnl'], $allDataSum['current_pnl']);
            $allDataSum['current_pnl_percentage'] = $CurrentPnlClass['percentage'];
            $allDataSum['current_pnl_class'] = $CurrentPnlClass['class'];
            $allDataSum['current_pnl_arrow'] = $CurrentPnlClass['arrow'];

            $CurrentPnlPercentage = $this->classPercentage($allDataSum['last_pnl'], $allDataSum['estimated_pnl']);
            $allDataSum['estimated_pnl_percentage'] = $CurrentPnlPercentage['percentage'];
            $allDataSum['estimated_pnl_class'] = $CurrentPnlPercentage['class'];
            $allDataSum['estimated_pnl_arrow'] = $CurrentPnlPercentage['arrow'];

            $CurrentAvgPnlPercentage = $this->classPercentage($allDataSum['last_avg_pnl'], $allDataSum['estimated_avg_pnl']);
            $allDataSum['estimated_avg_pnl_percentage'] = $CurrentAvgPnlPercentage['percentage'];
            $allDataSum['estimated_avg_pnl_class'] = $CurrentAvgPnlPercentage['class'];
            $allDataSum['estimated_avg_pnl_arrow'] = $CurrentAvgPnlPercentage['arrow'];


            $LastRevPercentage = $this->classPercentage($allDataSum['prev_revenue_usd'], $allDataSum['last_revenue_usd']);
            $allDataSum['last_revenue_usd_percentage'] = $LastRevPercentage['percentage'];
            $allDataSum['last_revenue_usd_class'] = $LastRevPercentage['class'];
            $allDataSum['last_revenue_usd_arrow'] = $LastRevPercentage['arrow'];

            $LastAvgRevPercentage = $this->classPercentage($allDataSum['prev_avg_revenue_usd'], $allDataSum['last_avg_revenue_usd']);
            $allDataSum['last_avg_revenue_usd_percentage'] = $LastAvgRevPercentage['percentage'];
            $allDataSum['last_avg_revenue_usd_class'] = $LastAvgRevPercentage['class'];
            $allDataSum['last_avg_revenue_usd_arrow'] = $LastAvgRevPercentage['arrow'];

            $LastGRevPercentage = $this->classPercentage($allDataSum['prev_gross_revenue_usd'], $allDataSum['last_gross_revenue_usd']);
            $allDataSum['last_gross_revenue_usd_percentage'] = $LastGRevPercentage['percentage'];
            $allDataSum['last_gross_revenue_usd_class'] = $LastGRevPercentage['class'];
            $allDataSum['last_gross_revenue_usd_arrow'] = $LastGRevPercentage['arrow'];

            $LastNRevPercentage = $this->classPercentage($allDataSum['prev_net_revenue_usd'], $allDataSum['last_net_revenue_usd']);
            $allDataSum['last_net_revenue_usd_percentage'] = $LastNRevPercentage['percentage'];
            $allDataSum['last_net_revenue_usd_class'] = $LastNRevPercentage['class'];
            $allDataSum['last_net_revenue_usd_arrow'] = $LastNRevPercentage['arrow'];

            $LastAvgGRevPercentage = $this->classPercentage($allDataSum['prev_avg_gross_revenue_usd'], $allDataSum['last_avg_gross_revenue_usd']);
            $allDataSum['last_avg_gross_revenue_usd_percentage'] = $LastAvgGRevPercentage['percentage'];
            $allDataSum['last_avg_gross_revenue_usd_class'] = $LastAvgGRevPercentage['class'];
            $allDataSum['last_avg_gross_revenue_usd_arrow'] = $LastAvgGRevPercentage['arrow'];


            $LastAvgNRevPercentage = $this->classPercentage($allDataSum['prev_avg_net_revenue_usd'], $allDataSum['last_avg_net_revenue_usd']);
            $allDataSum['last_avg_net_revenue_usd_percentage'] = $LastAvgNRevPercentage['percentage'];
            $allDataSum['last_avg_net_revenue_usd_class'] = $LastAvgNRevPercentage['class'];
            $allDataSum['last_avg_net_revenue_usd_arrow'] = $LastAvgNRevPercentage['arrow'];

            $LastMOPercentage = $this->classPercentage($allDataSum['prev_total_mo'], $allDataSum['last_total_mo']);
            $allDataSum['last_total_mo_percentage'] = $LastMOPercentage['percentage'];
            $allDataSum['last_total_mo_class'] = $LastMOPercentage['class'];
            $allDataSum['last_total_mo_arrow'] = $LastMOPercentage['arrow'];

            $LastAvgMOPercentage = $this->classPercentage($allDataSum['prev_avg_mo'], $allDataSum['last_avg_mo']);
            $allDataSum['last_avg_mo_percentage'] = $LastAvgMOPercentage['percentage'];
            $allDataSum['last_avg_mo_class'] = $LastAvgMOPercentage['class'];
            $allDataSum['last_avg_mo_arrow'] = $LastAvgMOPercentage['arrow'];

            $LastRegPercentage = $this->classPercentage($allDataSum['prev_mo'], $allDataSum['last_mo']);
            $allDataSum['last_mo_percentage'] = $LastRegPercentage['percentage'];
            $allDataSum['last_mo_class'] = $LastRegPercentage['class'];
            $allDataSum['last_mo_arrow'] = $LastRegPercentage['arrow'];

            $LastCostPercentage = $this->classPercentage($allDataSum['prev_cost'], $allDataSum['last_cost']);
            $allDataSum['last_cost_percentage'] = $LastCostPercentage['percentage'];
            $allDataSum['last_cost_class'] = $LastCostPercentage['class'];
            $allDataSum['last_cost_arrow'] = $LastCostPercentage['arrow'];

            $LastPriceMOPercentage = $this->classPercentage($allDataSum['prev_price_mo'], $allDataSum['last_price_mo']);
            $allDataSum['last_price_mo_percentage'] = $LastPriceMOPercentage['percentage'];
            $allDataSum['last_price_mo_class'] = $LastPriceMOPercentage['class'];
            $allDataSum['last_price_mo_arrow'] = $LastPriceMOPercentage['arrow'];

            $LastROIPercentage = $this->classPercentage($allDataSum['previousMonthROI'], $allDataSum['lastMonthROI']);
            $allDataSum['lastMonthROI_percentage'] = $LastROIPercentage['percentage'];
            $allDataSum['lastMonthROI_class'] = $LastROIPercentage['class'];
            $allDataSum['lastMonthROI_arrow'] = $LastROIPercentage['arrow'];

            $LastArpuPercentage = $this->classPercentage($allDataSum['prev_30_arpu'], $allDataSum['last_30_arpu']);
            $allDataSum['last_30_arpu_percentage'] = $LastArpuPercentage['percentage'];
            $allDataSum['last_30_arpu_class'] = $LastArpuPercentage['class'];
            $allDataSum['last_30_arpu_arrow'] = $LastArpuPercentage['arrow'];

            $LastPnlPercentage = $this->classPercentage($allDataSum['prev_pnl'], $allDataSum['last_pnl']);
            $allDataSum['last_pnl_percentage'] = $LastPnlPercentage['percentage'];
            $allDataSum['last_pnl_class'] = $LastPnlPercentage['class'];
            $allDataSum['last_pnl_arrow'] = $LastPnlPercentage['arrow'];

            $LastAvgPnlPercentage = $this->classPercentage($allDataSum['prev_avg_pnl'], $allDataSum['last_avg_pnl']);
            $allDataSum['last_avg_pnl_percentage'] = $LastAvgPnlPercentage['percentage'];
            $allDataSum['last_avg_pnl_class'] = $LastAvgPnlPercentage['class'];
            $allDataSum['last_avg_pnl_arrow'] = $LastAvgPnlPercentage['arrow'];
            // dd($allDataSum);
            return view('admin.country_dashboard', compact('sumemry', 'allDataSum'));
        } else {
            return redirect()->route('login');
        }
    }

    public function operatorDashboard(Request $request)
    {
        if (Auth::check()) {
            $displayCountries = array();
            $records = array();
            $DataArray = array();
            $sumemry = array();

            $CountryId = $req_CountryId = $request->country;
            $CompanyId = $req_CompanyId = $request->company;
            $BusinessType = $req_BusinessType = $request->business_type;
            $filterOperator = $req_filterOperator = $request->operatorId;
            $showAllOperator = true;

            if ($request->filled('company') && $req_CompanyId != "allcompany"  && !$request->filled('operatorId')) {
                $companies = Company::Find($req_CompanyId);
                $Operators_company = array();

                if (!empty($companies)) {
                    $Operators_company = $companies->company_operators->pluck('operator_id')->toArray();
                    $Operators = Operator::with('revenueshare')
                        ->Status(1)
                        ->GetOperatorByOperatorId($Operators_company)
                        ->get();
                }

                $showAllOperator = false;
            }

            (!$request->filled('company')) ? $req_CompanyId = "allcompany" : false;

            if ($request->filled('company') && $request->filled('country') && !$request->filled('operatorId')) {
                $Countrys[0] = Country::with('operators')->Find($CountryId);
                $data = [
                    'id' => $req_CountryId,
                    'company' => $req_CompanyId,
                ];

                $requestobj = new Request($data);
                $ReportControllerobj = new ReportController;
                $Operators = $ReportControllerobj->userFilterOperator($requestobj);
                $CountryFlag = false;
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

            if ($request->filled('operatorId')) {
                $Operators = Operator::with('revenueshare')
                    ->Status(1)
                    ->GetOperatorByOperatorId($filterOperator)
                    ->get();

                $showAllOperator = false;
            }

            if ($showAllOperator) {
                $Operators = Operator::with('revenueshare')->Status(1)->get();
            }

            if (!isset($Operators)) {
                $request->session()->flash('alert-success', 'User was successful added!');
                return redirect()->back();
            }

            $arrayOperatorsIds = $Operators->pluck('id_operator')->toArray();

            $DashboardDatas = ReportsSummarizeDashbroads::filterOperator($arrayOperatorsIds)->get()->toArray();

            $start_date = Carbon::yesterday()->subDays(30)->format('Y-m-d');
            $end_date = Carbon::yesterday()->format('Y-m-d');
            $date = Carbon::now()->format('Y-m-d');

            $reports = ReportsPnlsOperatorSummarizes::filterOperator($arrayOperatorsIds)
                ->OperatorNotNull()
                ->filterDateRange($start_date, $end_date)
                ->SumOfRoiDataOperator()
                ->get()
                ->toArray();

            $active_subs = ReportsPnlsOperatorSummarizes::filteroperator($arrayOperatorsIds)
                ->where(['date' => $date])
                ->TotalOperator()
                ->get()
                ->toArray();

            $DashboardOperators = $this->getDashboardByOperatorID($DashboardDatas);
            $reportsByOperatorIDs = $this->getReportsByOperatorID($reports);
            $active_subsByOperatorIDs = $this->getReportsByOperatorID($active_subs);

            $user = Auth::user();
            $user_id = $user->id;
            $user_type = $user->type;
            $allowAllOperator = $user->WhowAccessAlOperator($user_type);

            if (!$allowAllOperator) {
                $current_start_date = Carbon::now()->startOfMonth()->format('Y-m-d');
                $current_end_date = Carbon::yesterday()->format('Y-m-d');

                $last_start_date = Carbon::now()->startOfMonth()->subMonth()->format('Y-m-d');
                $last_date = Carbon::now()->startOfMonth()->subMonthsNoOverflow();
                $last_end_date = $last_date->endOfMonth()->toDateString();

                $prev_start_date = Carbon::now()->startOfMonth()->subMonth()->subMonth()->format('Y-m-d');
                $firstDayofPreviousMonth = $last_date->startOfMonth()->subMonthsNoOverflow();
                $prev_end_date = $firstDayofPreviousMonth->endOfMonth()->toDateString();

                $UserOperatorServices = Session::get('userOperatorService');

                $arrayOperatorsIds = $UserOperatorServices['id_operators'];
                $arrayServicesIds = $UserOperatorServices['id_services'];

                $userOperators = Operator::with('revenueshare', 'country')
                    ->filteroperator($arrayOperatorsIds)
                    ->get()
                    ->toArray();

                foreach ($userOperators as $key => $value) {
                    if (empty($value['revenueshare'])) {
                        $userOperators[$key]['revenueshare']['merchant_revenue_share'] = 100;
                    }
                }

                $userOperatorsIDs = $this->getReportsByOperatorID($userOperators);

                $currentuserreports = ReportSummeriseUsers::filteroperator($arrayOperatorsIds)
                    ->UserdataOperator()
                    ->filterDateRange($current_start_date, $current_end_date)
                    ->User($user_id)
                    ->orderBy('operator_id')
                    ->get()
                    ->toArray();

                $currentuserreportsByOperatorIDs = $this->getUserReportsByOperatorID($currentuserreports);

                if (!empty($currentuserreportsByOperatorIDs)) {
                    foreach ($currentuserreportsByOperatorIDs as $key => $value) {
                        $DashboardOperators[$key]['current_revenue'] = $value['gros_rev'];
                        $DashboardOperators[$key]['current_revenue_usd'] = $value['gros_rev'] * $userOperatorsIDs[$key]['country']['usd'];
                        $DashboardOperators[$key]['current_gross_revenue_usd'] = $value['gros_rev'] * $userOperatorsIDs[$key]['country']['usd'] * ($userOperatorsIDs[$key]['revenueshare']['merchant_revenue_share'] / 100);

                        $DashboardOperators[$key]['last_revenue'] = 0;
                        $DashboardOperators[$key]['last_revenue_usd'] = 0;
                        $DashboardOperators[$key]['last_gross_revenue_usd'] = 0;
                        $DashboardOperators[$key]['prev_revenue'] = 0;
                        $DashboardOperators[$key]['prev_revenue_usd'] = 0;
                        $DashboardOperators[$key]['prev_gross_revenue_usd'] = 0;
                    }
                }

                $prevuserreports = ReportSummeriseUsers::filteroperator($arrayOperatorsIds)
                    ->UserdataOperator()
                    ->filterDateRange($prev_start_date, $prev_end_date)
                    ->User($user_id)
                    ->orderBy('operator_id')
                    ->get()
                    ->toArray();

                $prevuserreportsByOperatorIDs = $this->getUserReportsByOperatorID($prevuserreports);

                if (!empty($prevuserreportsByOperatorIDs)) {
                    foreach ($prevuserreportsByOperatorIDs as $key => $value) {
                        $DashboardOperators[$key]['prev_revenue'] = $value['gros_rev'];
                        $DashboardOperators[$key]['prev_revenue_usd'] = $value['gros_rev'] * $userOperatorsIDs[$key]['country']['usd'];
                        $DashboardOperators[$key]['prev_gross_revenue_usd'] = $value['gros_rev'] * $userOperatorsIDs[$key]['country']['usd'] * ($userOperatorsIDs[$key]['revenueshare']['merchant_revenue_share'] / 100);
                    }
                }

                $lastuserreports = ReportSummeriseUsers::filteroperator($arrayOperatorsIds)
                    ->UserdataOperator()
                    ->filterDateRange($last_start_date, $last_end_date)
                    ->User($user_id)
                    ->orderBy('operator_id')
                    ->get()
                    ->toArray();

                $lastuserreportsByOperatorIDs = $this->getUserReportsByOperatorID($lastuserreports);

                if (!empty($lastuserreportsByOperatorIDs)) {
                    foreach ($lastuserreportsByOperatorIDs as $key => $value) {
                        $DashboardOperators[$key]['last_revenue'] = $value['gros_rev'];
                        $DashboardOperators[$key]['last_revenue_usd'] = $value['gros_rev'] * $userOperatorsIDs[$key]['country']['usd'];
                        $DashboardOperators[$key]['last_gross_revenue_usd'] = $value['gros_rev'] * $userOperatorsIDs[$key]['country']['usd'] * ($userOperatorsIDs[$key]['revenueshare']['merchant_revenue_share'] / 100);
                    }
                }
            }

            $Country = Country::all();
            $countries = array();

            if (!empty($Country)) {
                foreach ($Country as $CountryI) {
                    $countries[$CountryI->id] = $CountryI;
                }
            }

            if (!empty($Operators)) {
                foreach ($Operators as $operator) {
                    $tmpOperators = array();
                    $tmpOperators['operator'] = $operator;
                    $country_id  = $operator->country_id;
                    $id_operator =  $operator->id_operator;
                    $contain_id = Arr::exists($countries, $country_id);

                    if ($contain_id) {
                        $tmpOperators['country'] = $countries[$country_id];
                    }

                    if (isset($DashboardOperators[$id_operator])) {
                        $tmpOperators['reports'] = $DashboardOperators[$id_operator];
                    }

                    if (isset($reportsByOperatorIDs[$id_operator])) {
                        $tmpOperators['reports']['pnl_details'] = $reportsByOperatorIDs[$id_operator];
                    }

                    if (isset($active_subsByOperatorIDs[$id_operator])) {
                        $tmpOperators['reports']['total'] = $active_subsByOperatorIDs[$id_operator];
                    }

                    $sumemry[] = $tmpOperators;
                }
            }

            if (!empty($sumemry)) {
                foreach ($sumemry as $key => $sumemries) {
                    $country_id = $sumemries['country']['id'];
                    $operator_id = $sumemries['operator']['id_operator'];
                    $displayCountries[$operator_id]['country'] = $sumemries['country'];
                    $displayCountries[$operator_id]['operator'] = $sumemries['operator'];
                    $displayCountries[$operator_id]['operators'][] = $sumemries;
                }
            }

            if (isset($displayCountries) && !empty($displayCountries)) {
                foreach ($displayCountries as $key => $records) {
                    $tempoperatorsCountry = array();
                    $tempoperatorsCountry['country'] = $records['country'];
                    $tempoperatorsCountry['operator'] = $records['operator'];
                    $tempoperatorsCountry['service'] = 0.0;
                    $AllOperators = $records['operators'];

                    if (isset($AllOperators) && !empty($AllOperators)) {
                        $tempoperatorsCountry['service'] = 0.0;
                        $reportsColumnData = UtilityDashboard::reArrangeDashboardData($AllOperators);
                        // dd($reportsColumnData);
                        foreach ($reportsColumnData as $key => $value) {
                            $tempoperatorsCountry[$key] = $value;
                        }

                        $DataArray[] = $tempoperatorsCountry;
                    }
                }
            }

            foreach ($DataArray as $key => $value) {

                $CurrentRevClass = $this->classPercentage($value['last_revenue_usd'], $value['current_revenue_usd']);
                $DataArray[$key]['current_revenue_usd_percentage'] = $CurrentRevClass['percentage'];
                $DataArray[$key]['current_revenue_usd_class'] = $CurrentRevClass['class'];
                $DataArray[$key]['current_revenue_usd_arrow'] = $CurrentRevClass['arrow'];

                $CurrentRevPercentage = $this->classPercentage($value['last_revenue_usd'], $value['estimated_revenue_usd']);
                $DataArray[$key]['estimated_revenue_usd_percentage'] = $CurrentRevPercentage['percentage'];
                $DataArray[$key]['estimated_revenue_usd_class'] = $CurrentRevPercentage['class'];
                $DataArray[$key]['estimated_revenue_usd_arrow'] = $CurrentRevPercentage['arrow'];

                $CurrentAvgRevPercentage = $this->classPercentage($value['last_avg_revenue_usd'], $value['estimated_avg_revenue_usd']);
                $DataArray[$key]['estimated_avg_revenue_usd_percentage'] = $CurrentAvgRevPercentage['percentage'];
                $DataArray[$key]['estimated_avg_revenue_usd_class'] = $CurrentAvgRevPercentage['class'];
                $DataArray[$key]['estimated_avg_revenue_usd_arrow'] = $CurrentAvgRevPercentage['arrow'];

                $CurrentGRevClass = $this->classPercentage($value['last_gross_revenue_usd'], $value['current_gross_revenue_usd']);
                $DataArray[$key]['current_gross_revenue_usd_percentage'] = $CurrentGRevClass['percentage'];
                $DataArray[$key]['current_gross_revenue_usd_class'] = $CurrentGRevClass['class'];
                $DataArray[$key]['current_gross_revenue_usd_arrow'] = $CurrentGRevClass['arrow'];

                $CurrentNRevClass = $this->classPercentage($value['last_net_revenue_usd'], $value['current_net_revenue_usd']);
                $DataArray[$key]['current_net_revenue_usd_percentage'] = $CurrentNRevClass['percentage'];
                $DataArray[$key]['current_net_revenue_usd_class'] = $CurrentNRevClass['class'];
                $DataArray[$key]['current_net_revenue_usd_arrow'] = $CurrentNRevClass['arrow'];

                $CurrentGRevPercentage = $this->classPercentage($value['last_gross_revenue_usd'], $value['estimated_gross_revenue_usd']);
                $DataArray[$key]['estimated_gross_revenue_usd_percentage'] = $CurrentGRevPercentage['percentage'];
                $DataArray[$key]['estimated_gross_revenue_usd_class'] = $CurrentGRevPercentage['class'];
                $DataArray[$key]['estimated_gross_revenue_usd_arrow'] = $CurrentGRevPercentage['arrow'];

                $CurrentNRevPercentage = $this->classPercentage($value['last_net_revenue_usd'], $value['estimated_net_revenue_usd']);
                $DataArray[$key]['estimated_net_revenue_usd_percentage'] = $CurrentNRevPercentage['percentage'];
                $DataArray[$key]['estimated_net_revenue_usd_class'] = $CurrentNRevPercentage['class'];
                $DataArray[$key]['estimated_net_revenue_usd_arrow'] = $CurrentNRevPercentage['arrow'];

                $CurrentAvgGRevPercentage = $this->classPercentage($value['last_avg_gross_revenue_usd'], $value['estimated_avg_gross_revenue_usd']);
                $DataArray[$key]['estimated_avg_gross_revenue_usd_percentage'] = $CurrentAvgGRevPercentage['percentage'];
                $DataArray[$key]['estimated_avg_gross_revenue_usd_class'] = $CurrentAvgGRevPercentage['class'];
                $DataArray[$key]['estimated_avg_gross_revenue_usd_arrow'] = $CurrentAvgGRevPercentage['arrow'];

                $CurrentAvgNRevPercentage = $this->classPercentage($value['last_avg_net_revenue_usd'], $value['estimated_avg_net_revenue_usd']);
                $DataArray[$key]['estimated_avg_net_revenue_usd_percentage'] = $CurrentAvgNRevPercentage['percentage'];
                $DataArray[$key]['estimated_avg_net_revenue_usd_class'] = $CurrentAvgNRevPercentage['class'];
                $DataArray[$key]['estimated_avg_net_revenue_usd_arrow'] = $CurrentAvgNRevPercentage['arrow'];

                $CurrentMOClass = $this->classPercentage($value['last_total_mo'], $value['current_total_mo']);
                $DataArray[$key]['current_total_mo_percentage'] = $CurrentMOClass['percentage'];
                $DataArray[$key]['current_total_mo_class'] = $CurrentMOClass['class'];
                $DataArray[$key]['current_total_mo_arrow'] = $CurrentMOClass['arrow'];

                $CurrentRegClass = $this->classPercentage($value['last_mo'], $value['current_mo']);
                $DataArray[$key]['current_mo_percentage'] = $CurrentRegClass['percentage'];
                $DataArray[$key]['current_mo_class'] = $CurrentRegClass['class'];
                $DataArray[$key]['current_mo_arrow'] = $CurrentRegClass['arrow'];

                $CurrentMOPercentage = $this->classPercentage($value['last_total_mo'], $value['estimated_total_mo']);
                $DataArray[$key]['estimated_total_mo_percentage'] = $CurrentMOPercentage['percentage'];
                $DataArray[$key]['estimated_total_mo_class'] = $CurrentMOPercentage['class'];
                $DataArray[$key]['estimated_total_mo_arrow'] = $CurrentMOPercentage['arrow'];

                $CurrentAvgMOPercentage = $this->classPercentage($value['last_avg_mo'], $value['estimated_avg_mo']);
                $DataArray[$key]['estimated_avg_mo_percentage'] = $CurrentAvgMOPercentage['percentage'];
                $DataArray[$key]['estimated_avg_mo_class'] = $CurrentAvgMOPercentage['class'];
                $DataArray[$key]['estimated_avg_mo_arrow'] = $CurrentAvgMOPercentage['arrow'];

                $CurrentRegPercentage = $this->classPercentage($value['last_mo'], $value['estimated_mo']);
                $DataArray[$key]['estimated_mo_percentage'] = $CurrentRegPercentage['percentage'];
                $DataArray[$key]['estimated_mo_class'] = $CurrentRegPercentage['class'];
                $DataArray[$key]['estimated_mo_arrow'] = $CurrentRegPercentage['arrow'];

                $CurrentCostClass = $this->classPercentage($value['last_cost'], $value['current_cost']);
                $DataArray[$key]['current_cost_percentage'] = $CurrentCostClass['percentage'];
                $DataArray[$key]['current_cost_class'] = $CurrentCostClass['class'];
                $DataArray[$key]['current_cost_arrow'] = $CurrentCostClass['arrow'];

                $CurrentCostPercentage = $this->classPercentage($value['last_cost'], $value['estimated_cost']);
                $DataArray[$key]['estimated_cost_percentage'] = $CurrentCostPercentage['percentage'];
                $DataArray[$key]['estimated_cost_class'] = $CurrentCostPercentage['class'];
                $DataArray[$key]['estimated_cost_arrow'] = $CurrentCostPercentage['arrow'];

                $CurrentPriceMOClass = $this->classPercentage($value['last_price_mo'], $value['current_price_mo']);
                $DataArray[$key]['current_price_mo_percentage'] = $CurrentPriceMOClass['percentage'];
                $DataArray[$key]['current_price_mo_class'] = $CurrentPriceMOClass['class'];
                $DataArray[$key]['current_price_mo_arrow'] = $CurrentPriceMOClass['arrow'];

                $CurrentPriceMOPercentage = $this->classPercentage($value['last_price_mo'], $value['estimated_price_mo']);
                $DataArray[$key]['estimated_price_mo_percentage'] = $CurrentPriceMOPercentage['percentage'];
                $DataArray[$key]['estimated_price_mo_class'] = $CurrentPriceMOPercentage['class'];
                $DataArray[$key]['estimated_price_mo_arrow'] = $CurrentPriceMOPercentage['arrow'];

                $CurrentROIClass = $this->classPercentage($value['lastMonthROI'], $value['currentMonthROI']);
                $DataArray[$key]['currentMonthROI_percentage'] = $CurrentROIClass['percentage'];
                $DataArray[$key]['currentMonthROI_class'] = $CurrentROIClass['class'];
                $DataArray[$key]['currentMonthROI_arrow'] = $CurrentROIClass['arrow'];

                $CurrentROIPercentage = $this->classPercentage($value['lastMonthROI'], $value['estimatedMonthROI']);
                $DataArray[$key]['estimatedMonthROI_percentage'] = $CurrentROIPercentage['percentage'];
                $DataArray[$key]['estimatedMonthROI_class'] = $CurrentROIPercentage['class'];
                $DataArray[$key]['estimatedMonthROI_arrow'] = $CurrentROIPercentage['arrow'];

                $CurrentArpuClass = $this->classPercentage($value['last_30_arpu'], $value['current_30_arpu']);
                $DataArray[$key]['current_30_arpu_percentage'] = $CurrentArpuClass['percentage'];
                $DataArray[$key]['current_30_arpu_class'] = $CurrentArpuClass['class'];
                $DataArray[$key]['current_30_arpu_arrow'] = $CurrentArpuClass['arrow'];

                $CurrenArpuPercentage = $this->classPercentage($value['last_30_arpu'], $value['estimated_30_arpu']);
                $DataArray[$key]['estimated_30_arpu_percentage'] = $CurrenArpuPercentage['percentage'];
                $DataArray[$key]['estimated_30_arpu_class'] = $CurrenArpuPercentage['class'];
                $DataArray[$key]['estimated_30_arpu_arrow'] = $CurrenArpuPercentage['arrow'];

                $CurrentPnlClass = $this->classPercentage($value['last_pnl'], $value['current_pnl']);
                $DataArray[$key]['current_pnl_percentage'] = $CurrentPnlClass['percentage'];
                $DataArray[$key]['current_pnl_class'] = $CurrentPnlClass['class'];
                $DataArray[$key]['current_pnl_arrow'] = $CurrentPnlClass['arrow'];

                $CurrentPnlPercentage = $this->classPercentage($value['last_pnl'], $value['estimated_pnl']);
                $DataArray[$key]['estimated_pnl_percentage'] = $CurrentPnlPercentage['percentage'];
                $DataArray[$key]['estimated_pnl_class'] = $CurrentPnlPercentage['class'];
                $DataArray[$key]['estimated_pnl_arrow'] = $CurrentPnlPercentage['arrow'];

                $CurrentAvgPnlPercentage = $this->classPercentage($value['last_avg_pnl'], $value['estimated_avg_pnl']);
                $DataArray[$key]['estimated_avg_pnl_percentage'] = $CurrentAvgPnlPercentage['percentage'];
                $DataArray[$key]['estimated_avg_pnl_class'] = $CurrentAvgPnlPercentage['class'];
                $DataArray[$key]['estimated_avg_pnl_arrow'] = $CurrentAvgPnlPercentage['arrow'];


                $LastRevPercentage = $this->classPercentage($value['prev_revenue_usd'], $value['last_revenue_usd']);
                $DataArray[$key]['last_revenue_usd_percentage'] = $LastRevPercentage['percentage'];
                $DataArray[$key]['last_revenue_usd_class'] = $LastRevPercentage['class'];
                $DataArray[$key]['last_revenue_usd_arrow'] = $LastRevPercentage['arrow'];

                $LastAvgRevPercentage = $this->classPercentage($value['prev_avg_revenue_usd'], $value['last_avg_revenue_usd']);
                $DataArray[$key]['last_avg_revenue_usd_percentage'] = $LastAvgRevPercentage['percentage'];
                $DataArray[$key]['last_avg_revenue_usd_class'] = $LastAvgRevPercentage['class'];
                $DataArray[$key]['last_avg_revenue_usd_arrow'] = $LastAvgRevPercentage['arrow'];

                $LastGRevPercentage = $this->classPercentage($value['prev_gross_revenue_usd'], $value['last_gross_revenue_usd']);
                $DataArray[$key]['last_gross_revenue_usd_percentage'] = $LastGRevPercentage['percentage'];
                $DataArray[$key]['last_gross_revenue_usd_class'] = $LastGRevPercentage['class'];
                $DataArray[$key]['last_gross_revenue_usd_arrow'] = $LastGRevPercentage['arrow'];

                $LastNRevPercentage = $this->classPercentage($value['prev_net_revenue_usd'], $value['last_net_revenue_usd']);
                $DataArray[$key]['last_net_revenue_usd_percentage'] = $LastNRevPercentage['percentage'];
                $DataArray[$key]['last_net_revenue_usd_class'] = $LastNRevPercentage['class'];
                $DataArray[$key]['last_net_revenue_usd_arrow'] = $LastNRevPercentage['arrow'];

                $LastAvgGRevPercentage = $this->classPercentage($value['prev_avg_gross_revenue_usd'], $value['last_avg_gross_revenue_usd']);
                $DataArray[$key]['last_avg_gross_revenue_usd_percentage'] = $LastAvgGRevPercentage['percentage'];
                $DataArray[$key]['last_avg_gross_revenue_usd_class'] = $LastAvgGRevPercentage['class'];
                $DataArray[$key]['last_avg_gross_revenue_usd_arrow'] = $LastAvgGRevPercentage['arrow'];

                $LastAvgNRevPercentage = $this->classPercentage($value['prev_avg_net_revenue_usd'], $value['last_avg_net_revenue_usd']);
                $DataArray[$key]['last_avg_net_revenue_usd_percentage'] = $LastAvgNRevPercentage['percentage'];
                $DataArray[$key]['last_avg_net_revenue_usd_class'] = $LastAvgNRevPercentage['class'];
                $DataArray[$key]['last_avg_net_revenue_usd_arrow'] = $LastAvgNRevPercentage['arrow'];


                $LastMOPercentage = $this->classPercentage($value['prev_total_mo'], $value['last_total_mo']);
                $DataArray[$key]['last_total_mo_percentage'] = $LastMOPercentage['percentage'];
                $DataArray[$key]['last_total_mo_class'] = $LastMOPercentage['class'];
                $DataArray[$key]['last_total_mo_arrow'] = $LastMOPercentage['arrow'];

                $LastAvgMOPercentage = $this->classPercentage($value['prev_avg_mo'], $value['last_avg_mo']);
                $DataArray[$key]['last_avg_mo_percentage'] = $LastAvgMOPercentage['percentage'];
                $DataArray[$key]['last_avg_mo_class'] = $LastAvgMOPercentage['class'];
                $DataArray[$key]['last_avg_mo_arrow'] = $LastAvgMOPercentage['arrow'];

                $LastRegPercentage = $this->classPercentage($value['prev_mo'], $value['last_mo']);
                $DataArray[$key]['last_mo_percentage'] = $LastRegPercentage['percentage'];
                $DataArray[$key]['last_mo_class'] = $LastRegPercentage['class'];
                $DataArray[$key]['last_mo_arrow'] = $LastRegPercentage['arrow'];

                $LastCostPercentage = $this->classPercentage($value['prev_cost'], $value['last_cost']);
                $DataArray[$key]['last_cost_percentage'] = $LastCostPercentage['percentage'];
                $DataArray[$key]['last_cost_class'] = $LastCostPercentage['class'];
                $DataArray[$key]['last_cost_arrow'] = $LastCostPercentage['arrow'];

                $LastPriceMOPercentage = $this->classPercentage($value['prev_price_mo'], $value['last_price_mo']);
                $DataArray[$key]['last_price_mo_percentage'] = $LastPriceMOPercentage['percentage'];
                $DataArray[$key]['last_price_mo_class'] = $LastPriceMOPercentage['class'];
                $DataArray[$key]['last_price_mo_arrow'] = $LastPriceMOPercentage['arrow'];

                $LastROIPercentage = $this->classPercentage($value['previousMonthROI'], $value['lastMonthROI']);
                $DataArray[$key]['lastMonthROI_percentage'] = $LastROIPercentage['percentage'];
                $DataArray[$key]['lastMonthROI_class'] = $LastROIPercentage['class'];
                $DataArray[$key]['lastMonthROI_arrow'] = $LastROIPercentage['arrow'];

                $LastArpuPercentage = $this->classPercentage($value['prev_30_arpu'], $value['last_30_arpu']);
                $DataArray[$key]['last_30_arpu_percentage'] = $LastArpuPercentage['percentage'];
                $DataArray[$key]['last_30_arpu_class'] = $LastArpuPercentage['class'];
                $DataArray[$key]['last_30_arpu_arrow'] = $LastArpuPercentage['arrow'];

                $LastPnlPercentage = $this->classPercentage($value['prev_pnl'], $value['last_pnl']);
                $DataArray[$key]['last_pnl_percentage'] = $LastPnlPercentage['percentage'];
                $DataArray[$key]['last_pnl_class'] = $LastPnlPercentage['class'];
                $DataArray[$key]['last_pnl_arrow'] = $LastPnlPercentage['arrow'];

                $LastAvgPnlPercentage = $this->classPercentage($value['prev_avg_pnl'], $value['last_avg_pnl']);
                $DataArray[$key]['last_avg_pnl_percentage'] = $LastAvgPnlPercentage['percentage'];
                $DataArray[$key]['last_avg_pnl_class'] = $LastAvgPnlPercentage['class'];
                $DataArray[$key]['last_avg_pnl_arrow'] = $LastAvgPnlPercentage['arrow'];
            }

            $sumemry = $DataArray;
            $allDataSum = UtilityReports::DashboardAllDataSum($sumemry);

            $CurrentRevClass = $this->classPercentage($allDataSum['last_revenue_usd'], $allDataSum['current_revenue_usd']);
            $allDataSum['current_revenue_usd_percentage'] = $CurrentRevClass['percentage'];
            $allDataSum['current_revenue_usd_class'] = $CurrentRevClass['class'];
            $allDataSum['current_revenue_usd_arrow'] = $CurrentRevClass['arrow'];

            $CurrentRevPercentage = $this->classPercentage($allDataSum['last_revenue_usd'], $allDataSum['estimated_revenue_usd']);
            $allDataSum['estimated_revenue_usd_percentage'] = $CurrentRevPercentage['percentage'];
            $allDataSum['estimated_revenue_usd_class'] = $CurrentRevPercentage['class'];
            $allDataSum['estimated_revenue_usd_arrow'] = $CurrentRevPercentage['arrow'];

            $CurrentAvgRevPercentage = $this->classPercentage($allDataSum['last_avg_revenue_usd'], $allDataSum['estimated_avg_revenue_usd']);
            $allDataSum['estimated_avg_revenue_usd_percentage'] = $CurrentAvgRevPercentage['percentage'];
            $allDataSum['estimated_avg_revenue_usd_class'] = $CurrentAvgRevPercentage['class'];
            $allDataSum['estimated_avg_revenue_usd_arrow'] = $CurrentAvgRevPercentage['arrow'];

            $CurrentGRevClass = $this->classPercentage($allDataSum['last_gross_revenue_usd'], $allDataSum['current_gross_revenue_usd']);
            $allDataSum['current_gross_revenue_usd_percentage'] = $CurrentGRevClass['percentage'];
            $allDataSum['current_gross_revenue_usd_class'] = $CurrentGRevClass['class'];
            $allDataSum['current_gross_revenue_usd_arrow'] = $CurrentGRevClass['arrow'];

            $CurrentNRevClass = $this->classPercentage($allDataSum['last_net_revenue_usd'], $allDataSum['current_net_revenue_usd']);
            $allDataSum['current_net_revenue_usd_percentage'] = $CurrentNRevClass['percentage'];
            $allDataSum['current_net_revenue_usd_class'] = $CurrentNRevClass['class'];
            $allDataSum['current_net_revenue_usd_arrow'] = $CurrentNRevClass['arrow'];


            $CurrentGRevPercentage = $this->classPercentage($allDataSum['last_gross_revenue_usd'], $allDataSum['estimated_gross_revenue_usd']);
            $allDataSum['estimated_gross_revenue_usd_percentage'] = $CurrentGRevPercentage['percentage'];
            $allDataSum['estimated_gross_revenue_usd_class'] = $CurrentGRevPercentage['class'];
            $allDataSum['estimated_gross_revenue_usd_arrow'] = $CurrentGRevPercentage['arrow'];


            $CurrentNRevPercentage = $this->classPercentage($allDataSum['last_net_revenue_usd'], $allDataSum['estimated_net_revenue_usd']);
            $allDataSum['estimated_net_revenue_usd_percentage'] = $CurrentNRevPercentage['percentage'];
            $allDataSum['estimated_net_revenue_usd_class'] = $CurrentNRevPercentage['class'];
            $allDataSum['estimated_net_revenue_usd_arrow'] = $CurrentNRevPercentage['arrow'];

            $CurrentAvgNRevPercentage = $this->classPercentage($allDataSum['last_avg_net_revenue_usd'], $allDataSum['estimated_avg_net_revenue_usd']);
            $allDataSum['estimated_avg_net_revenue_usd_percentage'] = $CurrentAvgNRevPercentage['percentage'];
            $allDataSum['estimated_avg_net_revenue_usd_class'] = $CurrentAvgNRevPercentage['class'];
            $allDataSum['estimated_avg_net_revenue_usd_arrow'] = $CurrentAvgNRevPercentage['arrow'];

            $CurrentAvgGRevPercentage = $this->classPercentage($allDataSum['last_avg_gross_revenue_usd'], $allDataSum['estimated_avg_gross_revenue_usd']);
            $allDataSum['estimated_avg_gross_revenue_usd_percentage'] = $CurrentAvgGRevPercentage['percentage'];
            $allDataSum['estimated_avg_gross_revenue_usd_class'] = $CurrentAvgGRevPercentage['class'];
            $allDataSum['estimated_avg_gross_revenue_usd_arrow'] = $CurrentAvgGRevPercentage['arrow'];

            $CurrentMOClass = $this->classPercentage($allDataSum['last_total_mo'], $allDataSum['current_total_mo']);
            $allDataSum['current_total_mo_percentage'] = $CurrentMOClass['percentage'];
            $allDataSum['current_total_mo_class'] = $CurrentMOClass['class'];
            $allDataSum['current_total_mo_arrow'] = $CurrentMOClass['arrow'];

            $CurrentRegClass = $this->classPercentage($allDataSum['last_mo'], $allDataSum['current_mo']);
            $allDataSum['current_mo_percentage'] = $CurrentRegClass['percentage'];
            $allDataSum['current_mo_class'] = $CurrentRegClass['class'];
            $allDataSum['current_mo_arrow'] = $CurrentRegClass['arrow'];

            $CurrentMOPercentage = $this->classPercentage($allDataSum['last_total_mo'], $allDataSum['estimated_total_mo']);
            $allDataSum['estimated_total_mo_percentage'] = $CurrentMOPercentage['percentage'];
            $allDataSum['estimated_total_mo_class'] = $CurrentMOPercentage['class'];
            $allDataSum['estimated_total_mo_arrow'] = $CurrentMOPercentage['arrow'];

            $CurrentAvgMOPercentage = $this->classPercentage($allDataSum['last_avg_mo'], $allDataSum['estimated_avg_mo']);
            $allDataSum['estimated_avg_mo_percentage'] = $CurrentAvgMOPercentage['percentage'];
            $allDataSum['estimated_avg_mo_class'] = $CurrentAvgMOPercentage['class'];
            $allDataSum['estimated_avg_mo_arrow'] = $CurrentAvgMOPercentage['arrow'];

            $CurrentRegPercentage = $this->classPercentage($allDataSum['last_mo'], $allDataSum['estimated_mo']);
            $allDataSum['estimated_mo_percentage'] = $CurrentRegPercentage['percentage'];
            $allDataSum['estimated_mo_class'] = $CurrentRegPercentage['class'];
            $allDataSum['estimated_mo_arrow'] = $CurrentRegPercentage['arrow'];

            $CurrentCostClass = $this->classPercentage($allDataSum['last_cost'], $allDataSum['current_cost']);
            $allDataSum['current_cost_percentage'] = $CurrentCostClass['percentage'];
            $allDataSum['current_cost_class'] = $CurrentCostClass['class'];
            $allDataSum['current_cost_arrow'] = $CurrentCostClass['arrow'];

            $CurrentCostPercentage = $this->classPercentage($allDataSum['last_cost'], $allDataSum['estimated_cost']);
            $allDataSum['estimated_cost_percentage'] = $CurrentCostPercentage['percentage'];
            $allDataSum['estimated_cost_class'] = $CurrentCostPercentage['class'];
            $allDataSum['estimated_cost_arrow'] = $CurrentCostPercentage['arrow'];

            $CurrentPriceMOClass = $this->classPercentage($allDataSum['last_price_mo'], $allDataSum['current_price_mo']);
            $allDataSum['current_price_mo_percentage'] = $CurrentPriceMOClass['percentage'];
            $allDataSum['current_price_mo_class'] = $CurrentPriceMOClass['class'];
            $allDataSum['current_price_mo_arrow'] = $CurrentPriceMOClass['arrow'];

            $CurrentPriceMOPercentage = $this->classPercentage($allDataSum['last_price_mo'], $allDataSum['estimated_price_mo']);
            $allDataSum['estimated_price_mo_percentage'] = $CurrentPriceMOPercentage['percentage'];
            $allDataSum['estimated_price_mo_class'] = $CurrentPriceMOPercentage['class'];
            $allDataSum['estimated_price_mo_arrow'] = $CurrentPriceMOPercentage['arrow'];

            $CurrentROIClass = $this->classPercentage($allDataSum['lastMonthROI'], $allDataSum['currentMonthROI']);
            $allDataSum['currentMonthROI_percentage'] = $CurrentROIClass['percentage'];
            $allDataSum['currentMonthROI_class'] = $CurrentROIClass['class'];
            $allDataSum['currentMonthROI_arrow'] = $CurrentROIClass['arrow'];

            $CurrentROIPercentage = $this->classPercentage($allDataSum['lastMonthROI'], $allDataSum['estimatedMonthROI']);
            $allDataSum['estimatedMonthROI_percentage'] = $CurrentROIPercentage['percentage'];
            $allDataSum['estimatedMonthROI_class'] = $CurrentROIPercentage['class'];
            $allDataSum['estimatedMonthROI_arrow'] = $CurrentROIPercentage['arrow'];

            $CurrentArpuClass = $this->classPercentage($allDataSum['last_30_arpu'], $allDataSum['current_30_arpu']);
            $allDataSum['current_30_arpu_percentage'] = $CurrentArpuClass['percentage'];
            $allDataSum['current_30_arpu_class'] = $CurrentArpuClass['class'];
            $allDataSum['current_30_arpu_arrow'] = $CurrentArpuClass['arrow'];

            $CurrentArpuPercentage = $this->classPercentage($allDataSum['last_30_arpu'], $allDataSum['estimated_30_arpu']);
            $allDataSum['estimated_30_arpu_percentage'] = $CurrentArpuPercentage['percentage'];
            $allDataSum['estimated_30_arpu_class'] = $CurrentArpuPercentage['class'];
            $allDataSum['estimated_30_arpu_arrow'] = $CurrentArpuPercentage['arrow'];

            $CurrentPnlClass = $this->classPercentage($allDataSum['last_pnl'], $allDataSum['current_pnl']);
            $allDataSum['current_pnl_percentage'] = $CurrentPnlClass['percentage'];
            $allDataSum['current_pnl_class'] = $CurrentPnlClass['class'];
            $allDataSum['current_pnl_arrow'] = $CurrentPnlClass['arrow'];

            $CurrentPnlPercentage = $this->classPercentage($allDataSum['last_pnl'], $allDataSum['estimated_pnl']);
            $allDataSum['estimated_pnl_percentage'] = $CurrentPnlPercentage['percentage'];
            $allDataSum['estimated_pnl_class'] = $CurrentPnlPercentage['class'];
            $allDataSum['estimated_pnl_arrow'] = $CurrentPnlPercentage['arrow'];

            $CurrentAvgPnlPercentage = $this->classPercentage($allDataSum['last_avg_pnl'], $allDataSum['estimated_avg_pnl']);
            $allDataSum['estimated_avg_pnl_percentage'] = $CurrentAvgPnlPercentage['percentage'];
            $allDataSum['estimated_avg_pnl_class'] = $CurrentAvgPnlPercentage['class'];
            $allDataSum['estimated_avg_pnl_arrow'] = $CurrentAvgPnlPercentage['arrow'];


            $LastRevPercentage = $this->classPercentage($allDataSum['prev_revenue_usd'], $allDataSum['last_revenue_usd']);
            $allDataSum['last_revenue_usd_percentage'] = $LastRevPercentage['percentage'];
            $allDataSum['last_revenue_usd_class'] = $LastRevPercentage['class'];
            $allDataSum['last_revenue_usd_arrow'] = $LastRevPercentage['arrow'];

            $LastAvgRevPercentage = $this->classPercentage($allDataSum['prev_avg_revenue_usd'], $allDataSum['last_avg_revenue_usd']);
            $allDataSum['last_avg_revenue_usd_percentage'] = $LastAvgRevPercentage['percentage'];
            $allDataSum['last_avg_revenue_usd_class'] = $LastAvgRevPercentage['class'];
            $allDataSum['last_avg_revenue_usd_arrow'] = $LastAvgRevPercentage['arrow'];

            $LastGRevPercentage = $this->classPercentage($allDataSum['prev_gross_revenue_usd'], $allDataSum['last_gross_revenue_usd']);
            $allDataSum['last_gross_revenue_usd_percentage'] = $LastGRevPercentage['percentage'];
            $allDataSum['last_gross_revenue_usd_class'] = $LastGRevPercentage['class'];
            $allDataSum['last_gross_revenue_usd_arrow'] = $LastGRevPercentage['arrow'];

            $LastNRevPercentage = $this->classPercentage($allDataSum['prev_net_revenue_usd'], $allDataSum['last_net_revenue_usd']);
            $allDataSum['last_net_revenue_usd_percentage'] = $LastNRevPercentage['percentage'];
            $allDataSum['last_net_revenue_usd_class'] = $LastNRevPercentage['class'];
            $allDataSum['last_net_revenue_usd_arrow'] = $LastNRevPercentage['arrow'];

            $LastAvgGRevPercentage = $this->classPercentage($allDataSum['prev_avg_gross_revenue_usd'], $allDataSum['last_avg_gross_revenue_usd']);
            $allDataSum['last_avg_gross_revenue_usd_percentage'] = $LastAvgGRevPercentage['percentage'];
            $allDataSum['last_avg_gross_revenue_usd_class'] = $LastAvgGRevPercentage['class'];
            $allDataSum['last_avg_gross_revenue_usd_arrow'] = $LastAvgGRevPercentage['arrow'];


            $LastAvgNRevPercentage = $this->classPercentage($allDataSum['prev_avg_net_revenue_usd'], $allDataSum['last_avg_net_revenue_usd']);
            $allDataSum['last_avg_net_revenue_usd_percentage'] = $LastAvgNRevPercentage['percentage'];
            $allDataSum['last_avg_net_revenue_usd_class'] = $LastAvgNRevPercentage['class'];
            $allDataSum['last_avg_net_revenue_usd_arrow'] = $LastAvgNRevPercentage['arrow'];

            $LastMOPercentage = $this->classPercentage($allDataSum['prev_total_mo'], $allDataSum['last_total_mo']);
            $allDataSum['last_total_mo_percentage'] = $LastMOPercentage['percentage'];
            $allDataSum['last_total_mo_class'] = $LastMOPercentage['class'];
            $allDataSum['last_total_mo_arrow'] = $LastMOPercentage['arrow'];

            $LastAvgMOPercentage = $this->classPercentage($allDataSum['prev_avg_mo'], $allDataSum['last_avg_mo']);
            $allDataSum['last_avg_mo_percentage'] = $LastAvgMOPercentage['percentage'];
            $allDataSum['last_avg_mo_class'] = $LastAvgMOPercentage['class'];
            $allDataSum['last_avg_mo_arrow'] = $LastAvgMOPercentage['arrow'];

            $LastRegPercentage = $this->classPercentage($allDataSum['prev_mo'], $allDataSum['last_mo']);
            $allDataSum['last_mo_percentage'] = $LastRegPercentage['percentage'];
            $allDataSum['last_mo_class'] = $LastRegPercentage['class'];
            $allDataSum['last_mo_arrow'] = $LastRegPercentage['arrow'];

            $LastCostPercentage = $this->classPercentage($allDataSum['prev_cost'], $allDataSum['last_cost']);
            $allDataSum['last_cost_percentage'] = $LastCostPercentage['percentage'];
            $allDataSum['last_cost_class'] = $LastCostPercentage['class'];
            $allDataSum['last_cost_arrow'] = $LastCostPercentage['arrow'];

            $LastPriceMOPercentage = $this->classPercentage($allDataSum['prev_price_mo'], $allDataSum['last_price_mo']);
            $allDataSum['last_price_mo_percentage'] = $LastPriceMOPercentage['percentage'];
            $allDataSum['last_price_mo_class'] = $LastPriceMOPercentage['class'];
            $allDataSum['last_price_mo_arrow'] = $LastPriceMOPercentage['arrow'];

            $LastROIPercentage = $this->classPercentage($allDataSum['previousMonthROI'], $allDataSum['lastMonthROI']);
            $allDataSum['lastMonthROI_percentage'] = $LastROIPercentage['percentage'];
            $allDataSum['lastMonthROI_class'] = $LastROIPercentage['class'];
            $allDataSum['lastMonthROI_arrow'] = $LastROIPercentage['arrow'];

            $LastArpuPercentage = $this->classPercentage($allDataSum['prev_30_arpu'], $allDataSum['last_30_arpu']);
            $allDataSum['last_30_arpu_percentage'] = $LastArpuPercentage['percentage'];
            $allDataSum['last_30_arpu_class'] = $LastArpuPercentage['class'];
            $allDataSum['last_30_arpu_arrow'] = $LastArpuPercentage['arrow'];

            $LastPnlPercentage = $this->classPercentage($allDataSum['prev_pnl'], $allDataSum['last_pnl']);
            $allDataSum['last_pnl_percentage'] = $LastPnlPercentage['percentage'];
            $allDataSum['last_pnl_class'] = $LastPnlPercentage['class'];
            $allDataSum['last_pnl_arrow'] = $LastPnlPercentage['arrow'];

            $LastAvgPnlPercentage = $this->classPercentage($allDataSum['prev_avg_pnl'], $allDataSum['last_avg_pnl']);
            $allDataSum['last_avg_pnl_percentage'] = $LastAvgPnlPercentage['percentage'];
            $allDataSum['last_avg_pnl_class'] = $LastAvgPnlPercentage['class'];
            $allDataSum['last_avg_pnl_arrow'] = $LastAvgPnlPercentage['arrow'];

            return view('admin.operator_dashboard', compact('sumemry', 'allDataSum'));
        } else {
            return redirect()->route('login');
        }
    }

    public function companyDashboard(Request $request)
    {
        if (Auth::check()) {
            $displayCompanies = array();
            $records = array();
            $DataArray = array();
            $sumemry = array();

            $CountryId = $req_CountryId = $request->country;
            $CompanyId = $req_CompanyId = $request->company;
            $BusinessType = $req_BusinessType = $request->business_type;
            $filterOperator = $req_filterOperator = $request->operatorId;
            $showAllOperator = true;

            if ($request->filled('company') && $req_CompanyId != "allcompany"  && !$request->filled('operatorId')) {
                $companies = Company::Find($req_CompanyId);
                $Operators_company = array();

                if (!empty($companies)) {
                    $Operators_company = $companies->company_operators->pluck('operator_id')->toArray();
                    $Operators = Operator::with('revenueshare', 'company_operators')
                        ->Status(1)
                        ->GetOperatorByOperatorId($Operators_company)
                        ->get();
                }

                $showAllOperator = false;
            }

            (!$request->filled('company')) ? $req_CompanyId = "allcompany" : false;

            if ($request->filled('company') && $request->filled('country') && !$request->filled('operatorId')) {
                $Countrys[0] = Country::with('operators')->Find($CountryId);
                $data = [
                    'id' => $req_CountryId,
                    'company' => $req_CompanyId,
                ];

                $requestobj = new Request($data);
                $ReportControllerobj = new ReportController;
                $Operators = $ReportControllerobj->userFilterOperator($requestobj);
                $CountryFlag = false;
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

            if ($request->filled('operatorId')) {
                $Operators = Operator::with('revenueshare', 'company_operators')
                    ->Status(1)
                    ->GetOperatorByOperatorId($filterOperator)
                    ->get();

                $showAllOperator = false;
            }

            if ($showAllOperator) {
                $Operators = Operator::with('revenueshare', 'company_operators')->Status(1)->get();
            }

            if (!isset($Operators)) {
                $request->session()->flash('alert-success', 'User was successful added!');
                return redirect()->back();
            }

            $arrayOperatorsIds = $Operators->pluck('id_operator')->toArray();

            $DashboardDatas = ReportsSummarizeDashbroads::filterOperator($arrayOperatorsIds)->get()->toArray();

            $start_date = Carbon::yesterday()->subDays(30)->format('Y-m-d');
            $end_date = Carbon::yesterday()->format('Y-m-d');
            $date = Carbon::now()->format('Y-m-d');
            $reports = ReportsPnlsOperatorSummarizes::filterOperator($arrayOperatorsIds)
                ->OperatorNotNull()
                ->filterDateRange($start_date, $end_date)
                ->SumOfRoiDataOperator()
                ->get()
                ->toArray();

            $active_subs = ReportsPnlsOperatorSummarizes::filteroperator($arrayOperatorsIds)
                ->where(['date' => $date])
                ->TotalOperator()
                ->get()
                ->toArray();

            $DashboardOperators = $this->getDashboardByOperatorID($DashboardDatas);
            $reportsByOperatorIDs = $this->getReportsByOperatorID($reports);
            $active_subsByOperatorIDs = $this->getReportsByOperatorID($active_subs);

            $user = Auth::user();
            $user_id = $user->id;
            $user_type = $user->type;
            $allowAllOperator = $user->WhowAccessAlOperator($user_type);

            if (!$allowAllOperator) {
                $current_start_date = Carbon::now()->startOfMonth()->format('Y-m-d');
                $current_end_date = Carbon::yesterday()->format('Y-m-d');

                $last_start_date = Carbon::now()->startOfMonth()->subMonth()->format('Y-m-d');
                $last_date = Carbon::now()->startOfMonth()->subMonthsNoOverflow();
                $last_end_date = $last_date->endOfMonth()->toDateString();

                $prev_start_date = Carbon::now()->startOfMonth()->subMonth()->subMonth()->format('Y-m-d');
                $firstDayofPreviousMonth = $last_date->startOfMonth()->subMonthsNoOverflow();
                $prev_end_date = $firstDayofPreviousMonth->endOfMonth()->toDateString();

                $UserOperatorServices = Session::get('userOperatorService');

                $arrayOperatorsIds = $UserOperatorServices['id_operators'];
                $arrayServicesIds = $UserOperatorServices['id_services'];

                $userOperators = Operator::with('revenueshare', 'company_operators', 'country')
                    ->filteroperator($arrayOperatorsIds)
                    ->get()
                    ->toArray();

                foreach ($userOperators as $key => $value) {
                    if (empty($value['revenueshare'])) {
                        $userOperators[$key]['revenueshare']['merchant_revenue_share'] = 100;
                    }
                }

                $userOperatorsIDs = $this->getReportsByOperatorID($userOperators);

                $currentuserreports = ReportSummeriseUsers::filteroperator($arrayOperatorsIds)
                    ->UserdataOperator()
                    ->filterDateRange($current_start_date, $current_end_date)
                    ->User($user_id)
                    ->orderBy('operator_id')
                    ->get()
                    ->toArray();

                $currentuserreportsByOperatorIDs = $this->getUserReportsByOperatorID($currentuserreports);

                if (!empty($currentuserreportsByOperatorIDs)) {
                    foreach ($currentuserreportsByOperatorIDs as $key => $value) {
                        $DashboardOperators[$key]['current_revenue'] = isset($value['gros_rev']) ? $value['gros_rev'] : 0;
                        $DashboardOperators[$key]['current_revenue_usd'] = isset($value['gros_rev']) ? $value['gros_rev'] * $userOperatorsIDs[$key]['country']['usd'] : 0;
                        $DashboardOperators[$key]['current_gross_revenue_usd'] = isset($value['gros_rev']) ? $value['gros_rev'] * $userOperatorsIDs[$key]['country']['usd'] * ($userOperatorsIDs[$key]['revenueshare']['merchant_revenue_share'] / 100) : 0;

                        $DashboardOperators[$key]['last_revenue'] = 0;
                        $DashboardOperators[$key]['last_revenue_usd'] = 0;
                        $DashboardOperators[$key]['last_gross_revenue_usd'] = 0;
                        $DashboardOperators[$key]['prev_revenue'] = 0;
                        $DashboardOperators[$key]['prev_revenue_usd'] = 0;
                        $DashboardOperators[$key]['prev_gross_revenue_usd'] = 0;
                    }
                }

                $prevuserreports = ReportSummeriseUsers::filteroperator($arrayOperatorsIds)
                    ->UserdataOperator()
                    ->filterDateRange($prev_start_date, $prev_end_date)
                    ->User($user_id)
                    ->orderBy('operator_id')
                    ->get()
                    ->toArray();

                $prevuserreportsByOperatorIDs = $this->getUserReportsByOperatorID($prevuserreports);

                if (!empty($prevuserreportsByOperatorIDs)) {
                    foreach ($prevuserreportsByOperatorIDs as $key => $value) {
                        $DashboardOperators[$key]['prev_revenue'] = isset($value['gros_rev']) ? $value['gros_rev'] : 0;
                        $DashboardOperators[$key]['prev_revenue_usd'] = isset($value['gros_rev']) ? $value['gros_rev'] * $userOperatorsIDs[$key]['country']['usd'] : 0;
                        $DashboardOperators[$key]['prev_gross_revenue_usd'] = isset($value['gros_rev']) ? $value['gros_rev'] * $userOperatorsIDs[$key]['country']['usd'] * ($userOperatorsIDs[$key]['revenueshare']['merchant_revenue_share'] / 100) : 0;
                    }
                }

                $lastuserreports = ReportSummeriseUsers::filteroperator($arrayOperatorsIds)
                    ->UserdataOperator()
                    ->filterDateRange($last_start_date, $last_end_date)
                    ->User($user_id)
                    ->orderBy('operator_id')
                    ->get()
                    ->toArray();

                $lastuserreportsByOperatorIDs = $this->getUserReportsByOperatorID($lastuserreports);

                if (!empty($lastuserreportsByOperatorIDs)) {
                    foreach ($lastuserreportsByOperatorIDs as $key => $value) {
                        $DashboardOperators[$key]['last_revenue'] = isset($value['gros_rev']) ? $value['gros_rev'] : 0;
                        $DashboardOperators[$key]['last_revenue_usd'] = isset($value['gros_rev']) ? $value['gros_rev'] * $userOperatorsIDs[$key]['country']['usd'] : 0;
                        $DashboardOperators[$key]['last_gross_revenue_usd'] = isset($value['gros_rev']) ? $value['gros_rev'] * $userOperatorsIDs[$key]['country']['usd'] * ($userOperatorsIDs[$key]['revenueshare']['merchant_revenue_share'] / 100) : 0;
                    }
                }
            }

            $Company = Company::all();
            $companies = array();

            if (!empty($Company)) {
                foreach ($Company as $CompanyI) {
                    $companies[$CompanyI->id] = $CompanyI;
                }
            }

            if (!empty($Operators)) {
                foreach ($Operators as $operator) {
                    $tmpOperators = array();
                    $tmpOperators['operators'] = $operator;
                    $company_id  = isset($operator->company_operators) ? $operator->company_operators->company_id : '';
                    $id_operator = $operator->id_operator;
                    $contain_id = Arr::exists($companies, $company_id);

                    if ($contain_id) {
                        $tmpOperators['company'] = $companies[$company_id];
                    }

                    if (isset($DashboardOperators[$id_operator])) {
                        $tmpOperators['reports'] = $DashboardOperators[$id_operator];
                    }

                    if (isset($reportsByOperatorIDs[$id_operator])) {
                        $tmpOperators['reports']['pnl_details'] = $reportsByOperatorIDs[$id_operator];
                    }

                    if (isset($active_subsByOperatorIDs[$id_operator])) {
                        $tmpOperators['reports']['total'] = $active_subsByOperatorIDs[$id_operator];
                    }

                    $sumemry[] = $tmpOperators;
                }
            }

            if (!empty($sumemry)) {
                foreach ($sumemry as $key => $sumemries) {
                    $company_id = isset($sumemries['company']) ? $sumemries['company']['id'] : '';

                    if (empty($company_id)){
                        $displayCompanies['unknown']['operators'][] = $sumemries;
                    }
                    else{

                        $displayCompanies[$company_id]['company'] = $sumemries['company'];
                        $displayCompanies[$company_id]['operators'][] = $sumemries;
                    }

                }
            }
            // dd($displayCompanies);

             // Initialize to avoid undefined variable warnings

            if (isset($displayCompanies) && !empty($displayCompanies)) {
                foreach ($displayCompanies as $key => $records) {
                    $tempOperatorsCompany = array();

                    // Check if the company key exists
                    if (isset($records['company'])) {
                        $tempOperatorsCompany['company'] = $records['company'];
                    } else {
                        // Handle the case where the company information is missing
                        $tempOperatorsCompany['company'] = ['id' => 'unknown', 'name' => 'Unknown'];
                    }

                    $tempOperatorsCompany['operator_count'] = 0;
                    $AllOperators = $records['operators'];

                    if (isset($AllOperators) && !empty($AllOperators)) {
                        $tempOperatorsCompany['operator_count'] = count($AllOperators);
                        $reportsColumnData = UtilityDashboard::reArrangeCompanyDashboardData($AllOperators);

                        foreach ($reportsColumnData as $key => $value) {
                            $tempOperatorsCompany[$key] = $value;
                        }
                    }

                    $DataArray[] = $tempOperatorsCompany;
                }
            }

            // Debug output
            

            foreach ($DataArray as $key => $value) {

                $CurrentRevClass = $this->classPercentage($value['last_revenue_usd'], $value['current_revenue_usd']);
                $DataArray[$key]['current_revenue_usd_percentage'] = $CurrentRevClass['percentage'];
                $DataArray[$key]['current_revenue_usd_class'] = $CurrentRevClass['class'];
                $DataArray[$key]['current_revenue_usd_arrow'] = $CurrentRevClass['arrow'];

                $CurrentRevPercentage = $this->classPercentage($value['last_revenue_usd'], $value['estimated_revenue_usd']);
                $DataArray[$key]['estimated_revenue_usd_percentage'] = $CurrentRevPercentage['percentage'];
                $DataArray[$key]['estimated_revenue_usd_class'] = $CurrentRevPercentage['class'];
                $DataArray[$key]['estimated_revenue_usd_arrow'] = $CurrentRevPercentage['arrow'];

                $CurrentAvgRevPercentage = $this->classPercentage($value['last_avg_revenue_usd'], $value['estimated_avg_revenue_usd']);
                $DataArray[$key]['estimated_avg_revenue_usd_percentage'] = $CurrentAvgRevPercentage['percentage'];
                $DataArray[$key]['estimated_avg_revenue_usd_class'] = $CurrentAvgRevPercentage['class'];
                $DataArray[$key]['estimated_avg_revenue_usd_arrow'] = $CurrentAvgRevPercentage['arrow'];

                $CurrentGRevClass = $this->classPercentage($value['last_gross_revenue_usd'], $value['current_gross_revenue_usd']);
                $DataArray[$key]['current_gross_revenue_usd_percentage'] = $CurrentGRevClass['percentage'];
                $DataArray[$key]['current_gross_revenue_usd_class'] = $CurrentGRevClass['class'];
                $DataArray[$key]['current_gross_revenue_usd_arrow'] = $CurrentGRevClass['arrow'];

                $CurrentNRevClass = $this->classPercentage($value['last_net_revenue_usd'], $value['current_net_revenue_usd']);
                $DataArray[$key]['current_net_revenue_usd_percentage'] = $CurrentNRevClass['percentage'];
                $DataArray[$key]['current_net_revenue_usd_class'] = $CurrentNRevClass['class'];
                $DataArray[$key]['current_net_revenue_usd_arrow'] = $CurrentNRevClass['arrow'];

                $CurrentGRevPercentage = $this->classPercentage($value['last_gross_revenue_usd'], $value['estimated_gross_revenue_usd']);
                $DataArray[$key]['estimated_gross_revenue_usd_percentage'] = $CurrentGRevPercentage['percentage'];
                $DataArray[$key]['estimated_gross_revenue_usd_class'] = $CurrentGRevPercentage['class'];
                $DataArray[$key]['estimated_gross_revenue_usd_arrow'] = $CurrentGRevPercentage['arrow'];

                $CurrentNRevPercentage = $this->classPercentage($value['last_net_revenue_usd'], $value['estimated_net_revenue_usd']);
                $DataArray[$key]['estimated_net_revenue_usd_percentage'] = $CurrentNRevPercentage['percentage'];
                $DataArray[$key]['estimated_net_revenue_usd_class'] = $CurrentNRevPercentage['class'];
                $DataArray[$key]['estimated_net_revenue_usd_arrow'] = $CurrentNRevPercentage['arrow'];

                $CurrentAvgGRevPercentage = $this->classPercentage($value['last_avg_gross_revenue_usd'], $value['estimated_avg_gross_revenue_usd']);
                $DataArray[$key]['estimated_avg_gross_revenue_usd_percentage'] = $CurrentAvgGRevPercentage['percentage'];
                $DataArray[$key]['estimated_avg_gross_revenue_usd_class'] = $CurrentAvgGRevPercentage['class'];
                $DataArray[$key]['estimated_avg_gross_revenue_usd_arrow'] = $CurrentAvgGRevPercentage['arrow'];

                $CurrentAvgNRevPercentage = $this->classPercentage($value['last_avg_net_revenue_usd'], $value['estimated_avg_net_revenue_usd']);
                $DataArray[$key]['estimated_avg_net_revenue_usd_percentage'] = $CurrentAvgNRevPercentage['percentage'];
                $DataArray[$key]['estimated_avg_net_revenue_usd_class'] = $CurrentAvgNRevPercentage['class'];
                $DataArray[$key]['estimated_avg_net_revenue_usd_arrow'] = $CurrentAvgNRevPercentage['arrow'];

                $CurrentMOClass = $this->classPercentage($value['last_total_mo'], $value['current_total_mo']);
                $DataArray[$key]['current_total_mo_percentage'] = $CurrentMOClass['percentage'];
                $DataArray[$key]['current_total_mo_class'] = $CurrentMOClass['class'];
                $DataArray[$key]['current_total_mo_arrow'] = $CurrentMOClass['arrow'];

                $CurrentRegClass = $this->classPercentage($value['last_mo'], $value['current_mo']);
                $DataArray[$key]['current_mo_percentage'] = $CurrentRegClass['percentage'];
                $DataArray[$key]['current_mo_class'] = $CurrentRegClass['class'];
                $DataArray[$key]['current_mo_arrow'] = $CurrentRegClass['arrow'];

                $CurrentMOPercentage = $this->classPercentage($value['last_total_mo'], $value['estimated_total_mo']);
                $DataArray[$key]['estimated_total_mo_percentage'] = $CurrentMOPercentage['percentage'];
                $DataArray[$key]['estimated_total_mo_class'] = $CurrentMOPercentage['class'];
                $DataArray[$key]['estimated_total_mo_arrow'] = $CurrentMOPercentage['arrow'];

                $CurrentAvgMOPercentage = $this->classPercentage($value['last_avg_mo'], $value['estimated_avg_mo']);
                $DataArray[$key]['estimated_avg_mo_percentage'] = $CurrentAvgMOPercentage['percentage'];
                $DataArray[$key]['estimated_avg_mo_class'] = $CurrentAvgMOPercentage['class'];
                $DataArray[$key]['estimated_avg_mo_arrow'] = $CurrentAvgMOPercentage['arrow'];

                $CurrentRegPercentage = $this->classPercentage($value['last_mo'], $value['estimated_mo']);
                $DataArray[$key]['estimated_mo_percentage'] = $CurrentRegPercentage['percentage'];
                $DataArray[$key]['estimated_mo_class'] = $CurrentRegPercentage['class'];
                $DataArray[$key]['estimated_mo_arrow'] = $CurrentRegPercentage['arrow'];

                $CurrentCostClass = $this->classPercentage($value['last_cost'], $value['current_cost']);
                $DataArray[$key]['current_cost_percentage'] = $CurrentCostClass['percentage'];
                $DataArray[$key]['current_cost_class'] = $CurrentCostClass['class'];
                $DataArray[$key]['current_cost_arrow'] = $CurrentCostClass['arrow'];

                $CurrentCostPercentage = $this->classPercentage($value['last_cost'], $value['estimated_cost']);
                $DataArray[$key]['estimated_cost_percentage'] = $CurrentCostPercentage['percentage'];
                $DataArray[$key]['estimated_cost_class'] = $CurrentCostPercentage['class'];
                $DataArray[$key]['estimated_cost_arrow'] = $CurrentCostPercentage['arrow'];

                $CurrentPriceMOClass = $this->classPercentage($value['last_price_mo'], $value['current_price_mo']);
                $DataArray[$key]['current_price_mo_percentage'] = $CurrentPriceMOClass['percentage'];
                $DataArray[$key]['current_price_mo_class'] = $CurrentPriceMOClass['class'];
                $DataArray[$key]['current_price_mo_arrow'] = $CurrentPriceMOClass['arrow'];

                $CurrentPriceMOPercentage = $this->classPercentage($value['last_price_mo'], $value['estimated_price_mo']);
                $DataArray[$key]['estimated_price_mo_percentage'] = $CurrentPriceMOPercentage['percentage'];
                $DataArray[$key]['estimated_price_mo_class'] = $CurrentPriceMOPercentage['class'];
                $DataArray[$key]['estimated_price_mo_arrow'] = $CurrentPriceMOPercentage['arrow'];

                $CurrentROIClass = $this->classPercentage($value['lastMonthROI'], $value['currentMonthROI']);
                $DataArray[$key]['currentMonthROI_percentage'] = $CurrentROIClass['percentage'];
                $DataArray[$key]['currentMonthROI_class'] = $CurrentROIClass['class'];
                $DataArray[$key]['currentMonthROI_arrow'] = $CurrentROIClass['arrow'];

                $CurrentROIPercentage = $this->classPercentage($value['lastMonthROI'], $value['estimatedMonthROI']);
                $DataArray[$key]['estimatedMonthROI_percentage'] = $CurrentROIPercentage['percentage'];
                $DataArray[$key]['estimatedMonthROI_class'] = $CurrentROIPercentage['class'];
                $DataArray[$key]['estimatedMonthROI_arrow'] = $CurrentROIPercentage['arrow'];

                $CurrentArpuClass = $this->classPercentage($value['last_30_arpu'], $value['current_30_arpu']);
                $DataArray[$key]['current_30_arpu_percentage'] = $CurrentArpuClass['percentage'];
                $DataArray[$key]['current_30_arpu_class'] = $CurrentArpuClass['class'];
                $DataArray[$key]['current_30_arpu_arrow'] = $CurrentArpuClass['arrow'];

                $CurrenArpuPercentage = $this->classPercentage($value['last_30_arpu'], $value['estimated_30_arpu']);
                $DataArray[$key]['estimated_30_arpu_percentage'] = $CurrenArpuPercentage['percentage'];
                $DataArray[$key]['estimated_30_arpu_class'] = $CurrenArpuPercentage['class'];
                $DataArray[$key]['estimated_30_arpu_arrow'] = $CurrenArpuPercentage['arrow'];

                $CurrentPnlClass = $this->classPercentage($value['last_pnl'], $value['current_pnl']);
                $DataArray[$key]['current_pnl_percentage'] = $CurrentPnlClass['percentage'];
                $DataArray[$key]['current_pnl_class'] = $CurrentPnlClass['class'];
                $DataArray[$key]['current_pnl_arrow'] = $CurrentPnlClass['arrow'];

                $CurrentPnlPercentage = $this->classPercentage($value['last_pnl'], $value['estimated_pnl']);
                $DataArray[$key]['estimated_pnl_percentage'] = $CurrentPnlPercentage['percentage'];
                $DataArray[$key]['estimated_pnl_class'] = $CurrentPnlPercentage['class'];
                $DataArray[$key]['estimated_pnl_arrow'] = $CurrentPnlPercentage['arrow'];

                $CurrentAvgPnlPercentage = $this->classPercentage($value['last_avg_pnl'], $value['estimated_avg_pnl']);
                $DataArray[$key]['estimated_avg_pnl_percentage'] = $CurrentAvgPnlPercentage['percentage'];
                $DataArray[$key]['estimated_avg_pnl_class'] = $CurrentAvgPnlPercentage['class'];
                $DataArray[$key]['estimated_avg_pnl_arrow'] = $CurrentAvgPnlPercentage['arrow'];


                $LastRevPercentage = $this->classPercentage($value['prev_revenue_usd'], $value['last_revenue_usd']);
                $DataArray[$key]['last_revenue_usd_percentage'] = $LastRevPercentage['percentage'];
                $DataArray[$key]['last_revenue_usd_class'] = $LastRevPercentage['class'];
                $DataArray[$key]['last_revenue_usd_arrow'] = $LastRevPercentage['arrow'];

                $LastAvgRevPercentage = $this->classPercentage($value['prev_avg_revenue_usd'], $value['last_avg_revenue_usd']);
                $DataArray[$key]['last_avg_revenue_usd_percentage'] = $LastAvgRevPercentage['percentage'];
                $DataArray[$key]['last_avg_revenue_usd_class'] = $LastAvgRevPercentage['class'];
                $DataArray[$key]['last_avg_revenue_usd_arrow'] = $LastAvgRevPercentage['arrow'];

                $LastGRevPercentage = $this->classPercentage($value['prev_gross_revenue_usd'], $value['last_gross_revenue_usd']);
                $DataArray[$key]['last_gross_revenue_usd_percentage'] = $LastGRevPercentage['percentage'];
                $DataArray[$key]['last_gross_revenue_usd_class'] = $LastGRevPercentage['class'];
                $DataArray[$key]['last_gross_revenue_usd_arrow'] = $LastGRevPercentage['arrow'];

                $LastNRevPercentage = $this->classPercentage($value['prev_net_revenue_usd'], $value['last_net_revenue_usd']);
                $DataArray[$key]['last_net_revenue_usd_percentage'] = $LastNRevPercentage['percentage'];
                $DataArray[$key]['last_net_revenue_usd_class'] = $LastNRevPercentage['class'];
                $DataArray[$key]['last_net_revenue_usd_arrow'] = $LastNRevPercentage['arrow'];

                $LastAvgGRevPercentage = $this->classPercentage($value['prev_avg_gross_revenue_usd'], $value['last_avg_gross_revenue_usd']);
                $DataArray[$key]['last_avg_gross_revenue_usd_percentage'] = $LastAvgGRevPercentage['percentage'];
                $DataArray[$key]['last_avg_gross_revenue_usd_class'] = $LastAvgGRevPercentage['class'];
                $DataArray[$key]['last_avg_gross_revenue_usd_arrow'] = $LastAvgGRevPercentage['arrow'];

                $LastAvgNRevPercentage = $this->classPercentage($value['prev_avg_net_revenue_usd'], $value['last_avg_net_revenue_usd']);
                $DataArray[$key]['last_avg_net_revenue_usd_percentage'] = $LastAvgNRevPercentage['percentage'];
                $DataArray[$key]['last_avg_net_revenue_usd_class'] = $LastAvgNRevPercentage['class'];
                $DataArray[$key]['last_avg_net_revenue_usd_arrow'] = $LastAvgNRevPercentage['arrow'];

                $LastMOPercentage = $this->classPercentage($value['prev_total_mo'], $value['last_total_mo']);
                $DataArray[$key]['last_total_mo_percentage'] = $LastMOPercentage['percentage'];
                $DataArray[$key]['last_total_mo_class'] = $LastMOPercentage['class'];
                $DataArray[$key]['last_total_mo_arrow'] = $LastMOPercentage['arrow'];

                $LastAvgMOPercentage = $this->classPercentage($value['prev_avg_mo'], $value['last_avg_mo']);
                $DataArray[$key]['last_avg_mo_percentage'] = $LastAvgMOPercentage['percentage'];
                $DataArray[$key]['last_avg_mo_class'] = $LastAvgMOPercentage['class'];
                $DataArray[$key]['last_avg_mo_arrow'] = $LastAvgMOPercentage['arrow'];

                $LastRegPercentage = $this->classPercentage($value['prev_mo'], $value['last_mo']);
                $DataArray[$key]['last_mo_percentage'] = $LastRegPercentage['percentage'];
                $DataArray[$key]['last_mo_class'] = $LastRegPercentage['class'];
                $DataArray[$key]['last_mo_arrow'] = $LastRegPercentage['arrow'];

                $LastCostPercentage = $this->classPercentage($value['prev_cost'], $value['last_cost']);
                $DataArray[$key]['last_cost_percentage'] = $LastCostPercentage['percentage'];
                $DataArray[$key]['last_cost_class'] = $LastCostPercentage['class'];
                $DataArray[$key]['last_cost_arrow'] = $LastCostPercentage['arrow'];

                $LastPriceMOPercentage = $this->classPercentage($value['prev_price_mo'], $value['last_price_mo']);
                $DataArray[$key]['last_price_mo_percentage'] = $LastPriceMOPercentage['percentage'];
                $DataArray[$key]['last_price_mo_class'] = $LastPriceMOPercentage['class'];
                $DataArray[$key]['last_price_mo_arrow'] = $LastPriceMOPercentage['arrow'];

                $LastROIPercentage = $this->classPercentage($value['previousMonthROI'], $value['lastMonthROI']);
                $DataArray[$key]['lastMonthROI_percentage'] = $LastROIPercentage['percentage'];
                $DataArray[$key]['lastMonthROI_class'] = $LastROIPercentage['class'];
                $DataArray[$key]['lastMonthROI_arrow'] = $LastROIPercentage['arrow'];

                $LastArpuPercentage = $this->classPercentage($value['prev_30_arpu'], $value['last_30_arpu']);
                $DataArray[$key]['last_30_arpu_percentage'] = $LastArpuPercentage['percentage'];
                $DataArray[$key]['last_30_arpu_class'] = $LastArpuPercentage['class'];
                $DataArray[$key]['last_30_arpu_arrow'] = $LastArpuPercentage['arrow'];

                $LastPnlPercentage = $this->classPercentage($value['prev_pnl'], $value['last_pnl']);
                $DataArray[$key]['last_pnl_percentage'] = $LastPnlPercentage['percentage'];
                $DataArray[$key]['last_pnl_class'] = $LastPnlPercentage['class'];
                $DataArray[$key]['last_pnl_arrow'] = $LastPnlPercentage['arrow'];

                $LastAvgPnlPercentage = $this->classPercentage($value['prev_avg_pnl'], $value['last_avg_pnl']);
                $DataArray[$key]['last_avg_pnl_percentage'] = $LastAvgPnlPercentage['percentage'];
                $DataArray[$key]['last_avg_pnl_class'] = $LastAvgPnlPercentage['class'];
                $DataArray[$key]['last_avg_pnl_arrow'] = $LastAvgPnlPercentage['arrow'];
            }

            $sumemry = $DataArray;
            $allDataSum = UtilityReports::DashboardAllDataSum($sumemry);

            $CurrentRevClass = $this->classPercentage($allDataSum['last_revenue_usd'], $allDataSum['current_revenue_usd']);
            $allDataSum['current_revenue_usd_percentage'] = $CurrentRevClass['percentage'];
            $allDataSum['current_revenue_usd_class'] = $CurrentRevClass['class'];
            $allDataSum['current_revenue_usd_arrow'] = $CurrentRevClass['arrow'];

            $CurrentRevPercentage = $this->classPercentage($allDataSum['last_revenue_usd'], $allDataSum['estimated_revenue_usd']);
            $allDataSum['estimated_revenue_usd_percentage'] = $CurrentRevPercentage['percentage'];
            $allDataSum['estimated_revenue_usd_class'] = $CurrentRevPercentage['class'];
            $allDataSum['estimated_revenue_usd_arrow'] = $CurrentRevPercentage['arrow'];

            $CurrentAvgRevPercentage = $this->classPercentage($allDataSum['last_avg_revenue_usd'], $allDataSum['estimated_avg_revenue_usd']);
            $allDataSum['estimated_avg_revenue_usd_percentage'] = $CurrentAvgRevPercentage['percentage'];
            $allDataSum['estimated_avg_revenue_usd_class'] = $CurrentAvgRevPercentage['class'];
            $allDataSum['estimated_avg_revenue_usd_arrow'] = $CurrentAvgRevPercentage['arrow'];

            $CurrentGRevClass = $this->classPercentage($allDataSum['last_gross_revenue_usd'], $allDataSum['current_gross_revenue_usd']);
            $allDataSum['current_gross_revenue_usd_percentage'] = $CurrentGRevClass['percentage'];
            $allDataSum['current_gross_revenue_usd_class'] = $CurrentGRevClass['class'];
            $allDataSum['current_gross_revenue_usd_arrow'] = $CurrentGRevClass['arrow'];

            $CurrentNRevClass = $this->classPercentage($allDataSum['last_net_revenue_usd'], $allDataSum['current_net_revenue_usd']);
            $allDataSum['current_net_revenue_usd_percentage'] = $CurrentNRevClass['percentage'];
            $allDataSum['current_net_revenue_usd_class'] = $CurrentNRevClass['class'];
            $allDataSum['current_net_revenue_usd_arrow'] = $CurrentNRevClass['arrow'];

            $CurrentGRevPercentage = $this->classPercentage($allDataSum['last_gross_revenue_usd'], $allDataSum['estimated_gross_revenue_usd']);
            $allDataSum['estimated_gross_revenue_usd_percentage'] = $CurrentGRevPercentage['percentage'];
            $allDataSum['estimated_gross_revenue_usd_class'] = $CurrentGRevPercentage['class'];
            $allDataSum['estimated_gross_revenue_usd_arrow'] = $CurrentGRevPercentage['arrow'];

            $CurrentAvgGRevPercentage = $this->classPercentage($allDataSum['last_avg_gross_revenue_usd'], $allDataSum['estimated_avg_gross_revenue_usd']);
            $allDataSum['estimated_avg_gross_revenue_usd_percentage'] = $CurrentAvgGRevPercentage['percentage'];
            $allDataSum['estimated_avg_gross_revenue_usd_class'] = $CurrentAvgGRevPercentage['class'];
            $allDataSum['estimated_avg_gross_revenue_usd_arrow'] = $CurrentAvgGRevPercentage['arrow'];

            $CurrentNRevPercentage = $this->classPercentage($allDataSum['last_net_revenue_usd'], $allDataSum['estimated_net_revenue_usd']);
            $allDataSum['estimated_net_revenue_usd_percentage'] = $CurrentNRevPercentage['percentage'];
            $allDataSum['estimated_net_revenue_usd_class'] = $CurrentNRevPercentage['class'];
            $allDataSum['estimated_net_revenue_usd_arrow'] = $CurrentNRevPercentage['arrow'];

            $CurrentAvgNRevPercentage = $this->classPercentage($allDataSum['last_avg_net_revenue_usd'], $allDataSum['estimated_avg_net_revenue_usd']);
            $allDataSum['estimated_avg_net_revenue_usd_percentage'] = $CurrentAvgNRevPercentage['percentage'];
            $allDataSum['estimated_avg_net_revenue_usd_class'] = $CurrentAvgNRevPercentage['class'];
            $allDataSum['estimated_avg_net_revenue_usd_arrow'] = $CurrentAvgNRevPercentage['arrow'];

            $CurrentMOClass = $this->classPercentage($allDataSum['last_total_mo'], $allDataSum['current_total_mo']);
            $allDataSum['current_total_mo_percentage'] = $CurrentMOClass['percentage'];
            $allDataSum['current_total_mo_class'] = $CurrentMOClass['class'];
            $allDataSum['current_total_mo_arrow'] = $CurrentMOClass['arrow'];

            $CurrentRegClass = $this->classPercentage($allDataSum['last_mo'], $allDataSum['current_mo']);
            $allDataSum['current_mo_percentage'] = $CurrentRegClass['percentage'];
            $allDataSum['current_mo_class'] = $CurrentRegClass['class'];
            $allDataSum['current_mo_arrow'] = $CurrentRegClass['arrow'];

            $CurrentMOPercentage = $this->classPercentage($allDataSum['last_total_mo'], $allDataSum['estimated_total_mo']);
            $allDataSum['estimated_total_mo_percentage'] = $CurrentMOPercentage['percentage'];
            $allDataSum['estimated_total_mo_class'] = $CurrentMOPercentage['class'];
            $allDataSum['estimated_total_mo_arrow'] = $CurrentMOPercentage['arrow'];

            $CurrentAvgMOPercentage = $this->classPercentage($allDataSum['last_avg_mo'], $allDataSum['estimated_avg_mo']);
            $allDataSum['estimated_avg_mo_percentage'] = $CurrentAvgMOPercentage['percentage'];
            $allDataSum['estimated_avg_mo_class'] = $CurrentAvgMOPercentage['class'];
            $allDataSum['estimated_avg_mo_arrow'] = $CurrentAvgMOPercentage['arrow'];

            $CurrentRegPercentage = $this->classPercentage($allDataSum['last_mo'], $allDataSum['estimated_mo']);
            $allDataSum['estimated_mo_percentage'] = $CurrentRegPercentage['percentage'];
            $allDataSum['estimated_mo_class'] = $CurrentRegPercentage['class'];
            $allDataSum['estimated_mo_arrow'] = $CurrentRegPercentage['arrow'];

            $CurrentCostClass = $this->classPercentage($allDataSum['last_cost'], $allDataSum['current_cost']);
            $allDataSum['current_cost_percentage'] = $CurrentCostClass['percentage'];
            $allDataSum['current_cost_class'] = $CurrentCostClass['class'];
            $allDataSum['current_cost_arrow'] = $CurrentCostClass['arrow'];

            $CurrentCostPercentage = $this->classPercentage($allDataSum['last_cost'], $allDataSum['estimated_cost']);
            $allDataSum['estimated_cost_percentage'] = $CurrentCostPercentage['percentage'];
            $allDataSum['estimated_cost_class'] = $CurrentCostPercentage['class'];
            $allDataSum['estimated_cost_arrow'] = $CurrentCostPercentage['arrow'];

            $CurrentPriceMOClass = $this->classPercentage($allDataSum['last_price_mo'], $allDataSum['current_price_mo']);
            $allDataSum['current_price_mo_percentage'] = $CurrentPriceMOClass['percentage'];
            $allDataSum['current_price_mo_class'] = $CurrentPriceMOClass['class'];
            $allDataSum['current_price_mo_arrow'] = $CurrentPriceMOClass['arrow'];

            $CurrentPriceMOPercentage = $this->classPercentage($allDataSum['last_price_mo'], $allDataSum['estimated_price_mo']);
            $allDataSum['estimated_price_mo_percentage'] = $CurrentPriceMOPercentage['percentage'];
            $allDataSum['estimated_price_mo_class'] = $CurrentPriceMOPercentage['class'];
            $allDataSum['estimated_price_mo_arrow'] = $CurrentPriceMOPercentage['arrow'];

            $CurrentROIClass = $this->classPercentage($allDataSum['lastMonthROI'], $allDataSum['currentMonthROI']);
            $allDataSum['currentMonthROI_percentage'] = $CurrentROIClass['percentage'];
            $allDataSum['currentMonthROI_class'] = $CurrentROIClass['class'];
            $allDataSum['currentMonthROI_arrow'] = $CurrentROIClass['arrow'];

            $CurrentROIPercentage = $this->classPercentage($allDataSum['lastMonthROI'], $allDataSum['estimatedMonthROI']);
            $allDataSum['estimatedMonthROI_percentage'] = $CurrentROIPercentage['percentage'];
            $allDataSum['estimatedMonthROI_class'] = $CurrentROIPercentage['class'];
            $allDataSum['estimatedMonthROI_arrow'] = $CurrentROIPercentage['arrow'];

            $CurrentArpuClass = $this->classPercentage($allDataSum['last_30_arpu'], $allDataSum['current_30_arpu']);
            $allDataSum['current_30_arpu_percentage'] = $CurrentArpuClass['percentage'];
            $allDataSum['current_30_arpu_class'] = $CurrentArpuClass['class'];
            $allDataSum['current_30_arpu_arrow'] = $CurrentArpuClass['arrow'];

            $CurrentArpuPercentage = $this->classPercentage($allDataSum['last_30_arpu'], $allDataSum['estimated_30_arpu']);
            $allDataSum['estimated_30_arpu_percentage'] = $CurrentArpuPercentage['percentage'];
            $allDataSum['estimated_30_arpu_class'] = $CurrentArpuPercentage['class'];
            $allDataSum['estimated_30_arpu_arrow'] = $CurrentArpuPercentage['arrow'];

            $CurrentPnlClass = $this->classPercentage($allDataSum['last_pnl'], $allDataSum['current_pnl']);
            $allDataSum['current_pnl_percentage'] = $CurrentPnlClass['percentage'];
            $allDataSum['current_pnl_class'] = $CurrentPnlClass['class'];
            $allDataSum['current_pnl_arrow'] = $CurrentPnlClass['arrow'];

            $CurrentPnlPercentage = $this->classPercentage($allDataSum['last_pnl'], $allDataSum['estimated_pnl']);
            $allDataSum['estimated_pnl_percentage'] = $CurrentPnlPercentage['percentage'];
            $allDataSum['estimated_pnl_class'] = $CurrentPnlPercentage['class'];
            $allDataSum['estimated_pnl_arrow'] = $CurrentPnlPercentage['arrow'];

            $CurrentAvgPnlPercentage = $this->classPercentage($allDataSum['last_avg_pnl'], $allDataSum['estimated_avg_pnl']);
            $allDataSum['estimated_avg_pnl_percentage'] = $CurrentAvgPnlPercentage['percentage'];
            $allDataSum['estimated_avg_pnl_class'] = $CurrentAvgPnlPercentage['class'];
            $allDataSum['estimated_avg_pnl_arrow'] = $CurrentAvgPnlPercentage['arrow'];


            $LastRevPercentage = $this->classPercentage($allDataSum['prev_revenue_usd'], $allDataSum['last_revenue_usd']);
            $allDataSum['last_revenue_usd_percentage'] = $LastRevPercentage['percentage'];
            $allDataSum['last_revenue_usd_class'] = $LastRevPercentage['class'];
            $allDataSum['last_revenue_usd_arrow'] = $LastRevPercentage['arrow'];

            $LastAvgRevPercentage = $this->classPercentage($allDataSum['prev_avg_revenue_usd'], $allDataSum['last_avg_revenue_usd']);
            $allDataSum['last_avg_revenue_usd_percentage'] = $LastAvgRevPercentage['percentage'];
            $allDataSum['last_avg_revenue_usd_class'] = $LastAvgRevPercentage['class'];
            $allDataSum['last_avg_revenue_usd_arrow'] = $LastAvgRevPercentage['arrow'];

            $LastGRevPercentage = $this->classPercentage($allDataSum['prev_gross_revenue_usd'], $allDataSum['last_gross_revenue_usd']);
            $allDataSum['last_gross_revenue_usd_percentage'] = $LastGRevPercentage['percentage'];
            $allDataSum['last_gross_revenue_usd_class'] = $LastGRevPercentage['class'];
            $allDataSum['last_gross_revenue_usd_arrow'] = $LastGRevPercentage['arrow'];

            $LastNRevPercentage = $this->classPercentage($allDataSum['prev_net_revenue_usd'], $allDataSum['last_net_revenue_usd']);
            $allDataSum['last_net_revenue_usd_percentage'] = $LastNRevPercentage['percentage'];
            $allDataSum['last_net_revenue_usd_class'] = $LastNRevPercentage['class'];
            $allDataSum['last_net_revenue_usd_arrow'] = $LastNRevPercentage['arrow'];

            $LastAvgGRevPercentage = $this->classPercentage($allDataSum['prev_avg_gross_revenue_usd'], $allDataSum['last_avg_gross_revenue_usd']);
            $allDataSum['last_avg_gross_revenue_usd_percentage'] = $LastAvgGRevPercentage['percentage'];
            $allDataSum['last_avg_gross_revenue_usd_class'] = $LastAvgGRevPercentage['class'];
            $allDataSum['last_avg_gross_revenue_usd_arrow'] = $LastAvgGRevPercentage['arrow'];

            $LastAvgNRevPercentage = $this->classPercentage($allDataSum['prev_avg_net_revenue_usd'], $allDataSum['last_avg_net_revenue_usd']);
            $allDataSum['last_avg_net_revenue_usd_percentage'] = $LastAvgNRevPercentage['percentage'];
            $allDataSum['last_avg_net_revenue_usd_class'] = $LastAvgNRevPercentage['class'];
            $allDataSum['last_avg_net_revenue_usd_arrow'] = $LastAvgNRevPercentage['arrow'];

            $LastMOPercentage = $this->classPercentage($allDataSum['prev_total_mo'], $allDataSum['last_total_mo']);
            $allDataSum['last_total_mo_percentage'] = $LastMOPercentage['percentage'];
            $allDataSum['last_total_mo_class'] = $LastMOPercentage['class'];
            $allDataSum['last_total_mo_arrow'] = $LastMOPercentage['arrow'];

            $LastAvgMOPercentage = $this->classPercentage($allDataSum['prev_avg_mo'], $allDataSum['last_avg_mo']);
            $allDataSum['last_avg_mo_percentage'] = $LastAvgMOPercentage['percentage'];
            $allDataSum['last_avg_mo_class'] = $LastAvgMOPercentage['class'];
            $allDataSum['last_avg_mo_arrow'] = $LastAvgMOPercentage['arrow'];

            $LastRegPercentage = $this->classPercentage($allDataSum['prev_mo'], $allDataSum['last_mo']);
            $allDataSum['last_mo_percentage'] = $LastRegPercentage['percentage'];
            $allDataSum['last_mo_class'] = $LastRegPercentage['class'];
            $allDataSum['last_mo_arrow'] = $LastRegPercentage['arrow'];

            $LastCostPercentage = $this->classPercentage($allDataSum['prev_cost'], $allDataSum['last_cost']);
            $allDataSum['last_cost_percentage'] = $LastCostPercentage['percentage'];
            $allDataSum['last_cost_class'] = $LastCostPercentage['class'];
            $allDataSum['last_cost_arrow'] = $LastCostPercentage['arrow'];

            $LastPriceMOPercentage = $this->classPercentage($allDataSum['prev_price_mo'], $allDataSum['last_price_mo']);
            $allDataSum['last_price_mo_percentage'] = $LastPriceMOPercentage['percentage'];
            $allDataSum['last_price_mo_class'] = $LastPriceMOPercentage['class'];
            $allDataSum['last_price_mo_arrow'] = $LastPriceMOPercentage['arrow'];

            $LastROIPercentage = $this->classPercentage($allDataSum['previousMonthROI'], $allDataSum['lastMonthROI']);
            $allDataSum['lastMonthROI_percentage'] = $LastROIPercentage['percentage'];
            $allDataSum['lastMonthROI_class'] = $LastROIPercentage['class'];
            $allDataSum['lastMonthROI_arrow'] = $LastROIPercentage['arrow'];

            $LastArpuPercentage = $this->classPercentage($allDataSum['prev_30_arpu'], $allDataSum['last_30_arpu']);
            $allDataSum['last_30_arpu_percentage'] = $LastArpuPercentage['percentage'];
            $allDataSum['last_30_arpu_class'] = $LastArpuPercentage['class'];
            $allDataSum['last_30_arpu_arrow'] = $LastArpuPercentage['arrow'];

            $LastPnlPercentage = $this->classPercentage($allDataSum['prev_pnl'], $allDataSum['last_pnl']);
            $allDataSum['last_pnl_percentage'] = $LastPnlPercentage['percentage'];
            $allDataSum['last_pnl_class'] = $LastPnlPercentage['class'];
            $allDataSum['last_pnl_arrow'] = $LastPnlPercentage['arrow'];

            $LastAvgPnlPercentage = $this->classPercentage($allDataSum['prev_avg_pnl'], $allDataSum['last_avg_pnl']);
            $allDataSum['last_avg_pnl_percentage'] = $LastAvgPnlPercentage['percentage'];
            $allDataSum['last_avg_pnl_class'] = $LastAvgPnlPercentage['class'];
            $allDataSum['last_avg_pnl_arrow'] = $LastAvgPnlPercentage['arrow'];
            // dd($sumemry);
            return view('admin.company_dashboard', compact('sumemry', 'allDataSum'));
        } else {
            return redirect()->route('login');
        }
    }

    public function businessDashboard(Request $request)
    {
        if (Auth::check()) {
            $displayBusinessType = array();
            $records = array();
            $DataArray = array();
            $sumemry = array();

            $CountryId = $req_CountryId = $request->country;
            $CompanyId = $req_CompanyId = $request->company;
            $BusinessType = $req_BusinessType = $request->business_type;
            $filterOperator = $req_filterOperator = $request->operatorId;
            $showAllOperator = true;

            if ($request->filled('company') && $req_CompanyId != "allcompany"  && !$request->filled('operatorId')) {
                $companies = Company::Find($req_CompanyId);
                $Operators_company = array();

                if (!empty($companies)) {
                    $Operators_company = $companies->company_operators->pluck('operator_id')->toArray();
                    $Operators = Operator::with('revenueshare')
                        ->Status(1)
                        ->GetOperatorByOperatorId($Operators_company)
                        ->get();
                }

                $showAllOperator = false;
            }

            (!$request->filled('company')) ? $req_CompanyId = "allcompany" : false;

            if ($request->filled('company') && $request->filled('country') && !$request->filled('operatorId')) {
                $Countrys[0] = Country::with('operators')->Find($CountryId);
                $data = [
                    'id' => $req_CountryId,
                    'company' => $req_CompanyId,
                ];

                $requestobj = new Request($data);
                $ReportControllerobj = new ReportController;
                $Operators = $ReportControllerobj->userFilterOperator($requestobj);
                $CountryFlag = false;
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

            if ($request->filled('operatorId')) {
                $Operators = Operator::with('revenueshare')
                    ->Status(1)
                    ->GetOperatorByOperatorId($filterOperator)
                    ->get();

                $showAllOperator = false;
            }

            if ($showAllOperator) {
                $Operators = Operator::with('revenueshare')->Status(1)->get();
            }

            if (!isset($Operators)) {
                $request->session()->flash('alert-success', 'User was successful added!');
                return redirect()->back();
            }

            $arrayOperatorsIds = $Operators->pluck('id_operator')->toArray();

            $DashboardDatas = ReportsSummarizeDashbroads::filterOperator($arrayOperatorsIds)->get()->toArray();

            $start_date = Carbon::yesterday()->subDays(30)->format('Y-m-d');
            $end_date = Carbon::yesterday()->format('Y-m-d');
            $date = Carbon::now()->format('Y-m-d');
            $reports = ReportsPnlsOperatorSummarizes::filterOperator($arrayOperatorsIds)
                ->OperatorNotNull()
                ->filterDateRange($start_date, $end_date)
                ->SumOfRoiDataOperator()
                ->get()
                ->toArray();

            $active_subs = ReportsPnlsOperatorSummarizes::filteroperator($arrayOperatorsIds)
                ->where(['date' => $date])
                ->TotalOperator()
                ->get()
                ->toArray();

            $DashboardOperators = $this->getDashboardByOperatorID($DashboardDatas);
            $reportsByOperatorIDs = $this->getReportsByOperatorID($reports);
            $active_subsByOperatorIDs = $this->getReportsByOperatorID($active_subs);

            $user = Auth::user();
            $user_id = $user->id;
            $user_type = $user->type;
            $allowAllOperator = $user->WhowAccessAlOperator($user_type);

            if (!$allowAllOperator) {
                $current_start_date = Carbon::now()->startOfMonth()->format('Y-m-d');
                $current_end_date = Carbon::yesterday()->format('Y-m-d');

                $last_start_date = Carbon::now()->startOfMonth()->subMonth()->format('Y-m-d');
                $last_date = Carbon::now()->startOfMonth()->subMonthsNoOverflow();
                $last_end_date = $last_date->endOfMonth()->toDateString();

                $prev_start_date = Carbon::now()->startOfMonth()->subMonth()->subMonth()->format('Y-m-d');
                $firstDayofPreviousMonth = $last_date->startOfMonth()->subMonthsNoOverflow();
                $prev_end_date = $firstDayofPreviousMonth->endOfMonth()->toDateString();

                $UserOperatorServices = Session::get('userOperatorService');

                $arrayOperatorsIds = $UserOperatorServices['id_operators'];
                $arrayServicesIds = $UserOperatorServices['id_services'];

                $userOperators = Operator::with('revenueshare', 'country')
                    ->filteroperator($arrayOperatorsIds)
                    ->get()
                    ->toArray();

                $userOperatorsIDs = $this->getReportsByOperatorID($userOperators);

                $currentuserreports = ReportSummeriseUsers::filteroperator($arrayOperatorsIds)
                    ->UserdataOperator()
                    ->filterDateRange($current_start_date, $current_end_date)
                    ->User($user_id)
                    ->orderBy('operator_id')
                    ->get()
                    ->toArray();

                $currentuserreportsByOperatorIDs = $this->getUserReportsByOperatorID($currentuserreports);

                if (!empty($currentuserreportsByOperatorIDs)) {
                    foreach ($currentuserreportsByOperatorIDs as $key => $value) {
                        $DashboardOperators[$key]['current_revenue'] = $value['gros_rev'];
                        $DashboardOperators[$key]['current_revenue_usd'] = $value['gros_rev'] * $userOperatorsIDs[$key]['country']['usd'];
                        $DashboardOperators[$key]['current_gross_revenue_usd'] = $value['gros_rev'] * $userOperatorsIDs[$key]['country']['usd'] * ($userOperatorsIDs[$key]['revenueshare']['merchant_revenue_share'] / 100);

                        $DashboardOperators[$key]['last_revenue'] = 0;
                        $DashboardOperators[$key]['last_revenue_usd'] = 0;
                        $DashboardOperators[$key]['last_gross_revenue_usd'] = 0;
                        $DashboardOperators[$key]['prev_revenue'] = 0;
                        $DashboardOperators[$key]['prev_revenue_usd'] = 0;
                        $DashboardOperators[$key]['prev_gross_revenue_usd'] = 0;
                    }
                }

                $prevuserreports = ReportSummeriseUsers::filteroperator($arrayOperatorsIds)
                    ->UserdataOperator()
                    ->filterDateRange($prev_start_date, $prev_end_date)
                    ->User($user_id)
                    ->orderBy('operator_id')
                    ->get()
                    ->toArray();

                $prevuserreportsByOperatorIDs = $this->getUserReportsByOperatorID($prevuserreports);

                if (!empty($prevuserreportsByOperatorIDs)) {
                    foreach ($prevuserreportsByOperatorIDs as $key => $value) {
                        $DashboardOperators[$key]['prev_revenue'] = $value['gros_rev'];
                        $DashboardOperators[$key]['prev_revenue_usd'] = $value['gros_rev'] * $userOperatorsIDs[$key]['country']['usd'];
                        $DashboardOperators[$key]['prev_gross_revenue_usd'] = $value['gros_rev'] * $userOperatorsIDs[$key]['country']['usd'] * ($userOperatorsIDs[$key]['revenueshare']['merchant_revenue_share'] / 100);
                    }
                }

                $lastuserreports = ReportSummeriseUsers::filteroperator($arrayOperatorsIds)
                    ->UserdataOperator()
                    ->filterDateRange($last_start_date, $last_end_date)
                    ->User($user_id)
                    ->orderBy('operator_id')
                    ->get()
                    ->toArray();

                $lastuserreportsByOperatorIDs = $this->getUserReportsByOperatorID($lastuserreports);

                if (!empty($lastuserreportsByOperatorIDs)) {
                    foreach ($lastuserreportsByOperatorIDs as $key => $value) {
                        $DashboardOperators[$key]['last_revenue'] = $value['gros_rev'];
                        $DashboardOperators[$key]['last_revenue_usd'] = $value['gros_rev'] * $userOperatorsIDs[$key]['country']['usd'];
                        $DashboardOperators[$key]['last_gross_revenue_usd'] = $value['gros_rev'] * $userOperatorsIDs[$key]['country']['usd'] * ($userOperatorsIDs[$key]['revenueshare']['merchant_revenue_share'] / 100);
                    }
                }
            }

            $Country = Country::all();
            $countries = array();

            if (!empty($Country)) {
                foreach ($Country as $CountryI) {
                    $countries[$CountryI->id] = $CountryI;
                }
            }

            if (!empty($Operators)) {
                foreach ($Operators as $operator) {
                    $tmpOperators = array();
                    $tmpOperators['operators'] = $operator;
                    $country_id  = $operator->country_id;
                    $id_operator =  $operator->id_operator;
                    $contain_id = Arr::exists($countries, $country_id);

                    if ($contain_id) {
                        $tmpOperators['country'] = $countries[$country_id];
                    }

                    if (isset($DashboardOperators[$id_operator])) {
                        $tmpOperators['reports'] = $DashboardOperators[$id_operator];
                    }

                    if (isset($reportsByOperatorIDs[$id_operator])) {
                        $tmpOperators['reports']['pnl_details'] = $reportsByOperatorIDs[$id_operator];
                    }

                    if (isset($active_subsByOperatorIDs[$id_operator])) {
                        $tmpOperators['reports']['total'] = $active_subsByOperatorIDs[$id_operator];
                    }

                    $sumemry[] = $tmpOperators;
                }
            }


            if (!empty($sumemry)) {
                foreach ($sumemry as $key => $sumemries) {
                    $business_type = $sumemries['operators']['business_type'];

                    if ($business_type == NULL) {
                        // Handle the case where business_type is empty
                        $displayBusinessType['unknown']['country'] = $sumemries['country'];
                        $displayBusinessType['unknown']['operators'][] = $sumemries;
                    } else {
                        $displayBusinessType[$business_type]['country'] = $sumemries['country'];
                        $displayBusinessType[$business_type]['operators'][] = $sumemries;
                    }
                }
            }
            // dd($displayBusinessType);

            if (isset($displayBusinessType) && !empty($displayBusinessType)) {
                foreach ($displayBusinessType as $key => $records) {
                    $tempoperatorsBusinessType = array();
                    $tempoperatorsBusinessType['country']['country'] = $key;
                    $tempoperatorsBusinessType['operator_count'] = 0.0;
                    $AllOperators = $records['operators'];

                    if (isset($AllOperators) && !empty($AllOperators)) {
                        $tempoperatorsBusinessType['operator_count'] = count($AllOperators);
                        $reportsColumnData = UtilityDashboard::reArrangeContryDashboardData($AllOperators);

                        foreach ($reportsColumnData as $key => $value) {
                            $tempoperatorsBusinessType[$key] = $value;
                        }

                        $DataArray[] = $tempoperatorsBusinessType;
                    }
                }
            }
            // dd($DataArray);

            foreach ($DataArray as $key => $value) {

                $CurrentRevClass = $this->classPercentage($value['last_revenue_usd'], $value['current_revenue_usd']);
                $DataArray[$key]['current_revenue_usd_percentage'] = $CurrentRevClass['percentage'];
                $DataArray[$key]['current_revenue_usd_class'] = $CurrentRevClass['class'];
                $DataArray[$key]['current_revenue_usd_arrow'] = $CurrentRevClass['arrow'];

                $CurrentRevPercentage = $this->classPercentage($value['last_revenue_usd'], $value['estimated_revenue_usd']);
                $DataArray[$key]['estimated_revenue_usd_percentage'] = $CurrentRevPercentage['percentage'];
                $DataArray[$key]['estimated_revenue_usd_class'] = $CurrentRevPercentage['class'];
                $DataArray[$key]['estimated_revenue_usd_arrow'] = $CurrentRevPercentage['arrow'];

                $CurrentAvgRevPercentage = $this->classPercentage($value['last_avg_revenue_usd'], $value['estimated_avg_revenue_usd']);
                $DataArray[$key]['estimated_avg_revenue_usd_percentage'] = $CurrentAvgRevPercentage['percentage'];
                $DataArray[$key]['estimated_avg_revenue_usd_class'] = $CurrentAvgRevPercentage['class'];
                $DataArray[$key]['estimated_avg_revenue_usd_arrow'] = $CurrentAvgRevPercentage['arrow'];

                $CurrentGRevClass = $this->classPercentage($value['last_gross_revenue_usd'], $value['current_gross_revenue_usd']);
                $DataArray[$key]['current_gross_revenue_usd_percentage'] = $CurrentGRevClass['percentage'];
                $DataArray[$key]['current_gross_revenue_usd_class'] = $CurrentGRevClass['class'];
                $DataArray[$key]['current_gross_revenue_usd_arrow'] = $CurrentGRevClass['arrow'];

                $CurrentNRevClass = $this->classPercentage($value['last_net_revenue_usd'], $value['current_net_revenue_usd']);
                $DataArray[$key]['current_net_revenue_usd_percentage'] = $CurrentNRevClass['percentage'];
                $DataArray[$key]['current_net_revenue_usd_class'] = $CurrentNRevClass['class'];
                $DataArray[$key]['current_net_revenue_usd_arrow'] = $CurrentNRevClass['arrow'];

                $CurrentGRevPercentage = $this->classPercentage($value['last_gross_revenue_usd'], $value['estimated_gross_revenue_usd']);
                $DataArray[$key]['estimated_gross_revenue_usd_percentage'] = $CurrentGRevPercentage['percentage'];
                $DataArray[$key]['estimated_gross_revenue_usd_class'] = $CurrentGRevPercentage['class'];
                $DataArray[$key]['estimated_gross_revenue_usd_arrow'] = $CurrentGRevPercentage['arrow'];

                $CurrentNRevPercentage = $this->classPercentage($value['last_net_revenue_usd'], $value['estimated_net_revenue_usd']);
                $DataArray[$key]['estimated_net_revenue_usd_percentage'] = $CurrentNRevPercentage['percentage'];
                $DataArray[$key]['estimated_net_revenue_usd_class'] = $CurrentNRevPercentage['class'];
                $DataArray[$key]['estimated_net_revenue_usd_arrow'] = $CurrentNRevPercentage['arrow'];

                $CurrentAvgGRevPercentage = $this->classPercentage($value['last_avg_gross_revenue_usd'], $value['estimated_avg_gross_revenue_usd']);
                $DataArray[$key]['estimated_avg_gross_revenue_usd_percentage'] = $CurrentAvgGRevPercentage['percentage'];
                $DataArray[$key]['estimated_avg_gross_revenue_usd_class'] = $CurrentAvgGRevPercentage['class'];
                $DataArray[$key]['estimated_avg_gross_revenue_usd_arrow'] = $CurrentAvgGRevPercentage['arrow'];

                $CurrentAvgNRevPercentage = $this->classPercentage($value['last_avg_net_revenue_usd'], $value['estimated_avg_net_revenue_usd']);
                $DataArray[$key]['estimated_avg_net_revenue_usd_percentage'] = $CurrentAvgNRevPercentage['percentage'];
                $DataArray[$key]['estimated_avg_net_revenue_usd_class'] = $CurrentAvgNRevPercentage['class'];
                $DataArray[$key]['estimated_avg_net_revenue_usd_arrow'] = $CurrentAvgNRevPercentage['arrow'];


                $CurrentMOClass = $this->classPercentage($value['last_total_mo'], $value['current_total_mo']);
                $DataArray[$key]['current_total_mo_percentage'] = $CurrentMOClass['percentage'];
                $DataArray[$key]['current_total_mo_class'] = $CurrentMOClass['class'];
                $DataArray[$key]['current_total_mo_arrow'] = $CurrentMOClass['arrow'];

                $CurrentRegClass = $this->classPercentage($value['last_mo'], $value['current_mo']);
                $DataArray[$key]['current_mo_percentage'] = $CurrentRegClass['percentage'];
                $DataArray[$key]['current_mo_class'] = $CurrentRegClass['class'];
                $DataArray[$key]['current_mo_arrow'] = $CurrentRegClass['arrow'];

                $CurrentMOPercentage = $this->classPercentage($value['last_total_mo'], $value['estimated_total_mo']);
                $DataArray[$key]['estimated_total_mo_percentage'] = $CurrentMOPercentage['percentage'];
                $DataArray[$key]['estimated_total_mo_class'] = $CurrentMOPercentage['class'];
                $DataArray[$key]['estimated_total_mo_arrow'] = $CurrentMOPercentage['arrow'];

                $CurrentAvgMOPercentage = $this->classPercentage($value['last_avg_mo'], $value['estimated_avg_mo']);
                $DataArray[$key]['estimated_avg_mo_percentage'] = $CurrentAvgMOPercentage['percentage'];
                $DataArray[$key]['estimated_avg_mo_class'] = $CurrentAvgMOPercentage['class'];
                $DataArray[$key]['estimated_avg_mo_arrow'] = $CurrentAvgMOPercentage['arrow'];

                $CurrentRegPercentage = $this->classPercentage($value['last_mo'], $value['estimated_mo']);
                $DataArray[$key]['estimated_mo_percentage'] = $CurrentRegPercentage['percentage'];
                $DataArray[$key]['estimated_mo_class'] = $CurrentRegPercentage['class'];
                $DataArray[$key]['estimated_mo_arrow'] = $CurrentRegPercentage['arrow'];

                $CurrentCostClass = $this->classPercentage($value['last_cost'], $value['current_cost']);
                $DataArray[$key]['current_cost_percentage'] = $CurrentCostClass['percentage'];
                $DataArray[$key]['current_cost_class'] = $CurrentCostClass['class'];
                $DataArray[$key]['current_cost_arrow'] = $CurrentCostClass['arrow'];

                $CurrentCostPercentage = $this->classPercentage($value['last_cost'], $value['estimated_cost']);
                $DataArray[$key]['estimated_cost_percentage'] = $CurrentCostPercentage['percentage'];
                $DataArray[$key]['estimated_cost_class'] = $CurrentCostPercentage['class'];
                $DataArray[$key]['estimated_cost_arrow'] = $CurrentCostPercentage['arrow'];

                $CurrentPriceMOClass = $this->classPercentage($value['last_price_mo'], $value['current_price_mo']);
                $DataArray[$key]['current_price_mo_percentage'] = $CurrentPriceMOClass['percentage'];
                $DataArray[$key]['current_price_mo_class'] = $CurrentPriceMOClass['class'];
                $DataArray[$key]['current_price_mo_arrow'] = $CurrentPriceMOClass['arrow'];

                $CurrentPriceMOPercentage = $this->classPercentage($value['last_price_mo'], $value['estimated_price_mo']);
                $DataArray[$key]['estimated_price_mo_percentage'] = $CurrentPriceMOPercentage['percentage'];
                $DataArray[$key]['estimated_price_mo_class'] = $CurrentPriceMOPercentage['class'];
                $DataArray[$key]['estimated_price_mo_arrow'] = $CurrentPriceMOPercentage['arrow'];

                $CurrentROIClass = $this->classPercentage($value['lastMonthROI'], $value['currentMonthROI']);
                $DataArray[$key]['currentMonthROI_percentage'] = $CurrentROIClass['percentage'];
                $DataArray[$key]['currentMonthROI_class'] = $CurrentROIClass['class'];
                $DataArray[$key]['currentMonthROI_arrow'] = $CurrentROIClass['arrow'];

                $CurrentROIPercentage = $this->classPercentage($value['lastMonthROI'], $value['estimatedMonthROI']);
                $DataArray[$key]['estimatedMonthROI_percentage'] = $CurrentROIPercentage['percentage'];
                $DataArray[$key]['estimatedMonthROI_class'] = $CurrentROIPercentage['class'];
                $DataArray[$key]['estimatedMonthROI_arrow'] = $CurrentROIPercentage['arrow'];

                $CurrentArpuClass = $this->classPercentage($value['last_30_arpu'], $value['current_30_arpu']);
                $DataArray[$key]['current_30_arpu_percentage'] = $CurrentArpuClass['percentage'];
                $DataArray[$key]['current_30_arpu_class'] = $CurrentArpuClass['class'];
                $DataArray[$key]['current_30_arpu_arrow'] = $CurrentArpuClass['arrow'];

                $CurrenArpuPercentage = $this->classPercentage($value['last_30_arpu'], $value['estimated_30_arpu']);
                $DataArray[$key]['estimated_30_arpu_percentage'] = $CurrenArpuPercentage['percentage'];
                $DataArray[$key]['estimated_30_arpu_class'] = $CurrenArpuPercentage['class'];
                $DataArray[$key]['estimated_30_arpu_arrow'] = $CurrenArpuPercentage['arrow'];

                $CurrentPnlClass = $this->classPercentage($value['last_pnl'], $value['current_pnl']);
                $DataArray[$key]['current_pnl_percentage'] = $CurrentPnlClass['percentage'];
                $DataArray[$key]['current_pnl_class'] = $CurrentPnlClass['class'];
                $DataArray[$key]['current_pnl_arrow'] = $CurrentPnlClass['arrow'];

                $CurrentPnlPercentage = $this->classPercentage($value['last_pnl'], $value['estimated_pnl']);
                $DataArray[$key]['estimated_pnl_percentage'] = $CurrentPnlPercentage['percentage'];
                $DataArray[$key]['estimated_pnl_class'] = $CurrentPnlPercentage['class'];
                $DataArray[$key]['estimated_pnl_arrow'] = $CurrentPnlPercentage['arrow'];

                $CurrentAvgPnlPercentage = $this->classPercentage($value['last_avg_pnl'], $value['estimated_avg_pnl']);
                $DataArray[$key]['estimated_avg_pnl_percentage'] = $CurrentAvgPnlPercentage['percentage'];
                $DataArray[$key]['estimated_avg_pnl_class'] = $CurrentAvgPnlPercentage['class'];
                $DataArray[$key]['estimated_avg_pnl_arrow'] = $CurrentAvgPnlPercentage['arrow'];


                $LastRevPercentage = $this->classPercentage($value['prev_revenue_usd'], $value['last_revenue_usd']);
                $DataArray[$key]['last_revenue_usd_percentage'] = $LastRevPercentage['percentage'];
                $DataArray[$key]['last_revenue_usd_class'] = $LastRevPercentage['class'];
                $DataArray[$key]['last_revenue_usd_arrow'] = $LastRevPercentage['arrow'];

                $LastAvgRevPercentage = $this->classPercentage($value['prev_avg_revenue_usd'], $value['last_avg_revenue_usd']);
                $DataArray[$key]['last_avg_revenue_usd_percentage'] = $LastAvgRevPercentage['percentage'];
                $DataArray[$key]['last_avg_revenue_usd_class'] = $LastAvgRevPercentage['class'];
                $DataArray[$key]['last_avg_revenue_usd_arrow'] = $LastAvgRevPercentage['arrow'];

                $LastGRevPercentage = $this->classPercentage($value['prev_gross_revenue_usd'], $value['last_gross_revenue_usd']);
                $DataArray[$key]['last_gross_revenue_usd_percentage'] = $LastGRevPercentage['percentage'];
                $DataArray[$key]['last_gross_revenue_usd_class'] = $LastGRevPercentage['class'];
                $DataArray[$key]['last_gross_revenue_usd_arrow'] = $LastGRevPercentage['arrow'];

                $LastNRevPercentage = $this->classPercentage($value['prev_net_revenue_usd'], $value['last_net_revenue_usd']);
                $DataArray[$key]['last_net_revenue_usd_percentage'] = $LastNRevPercentage['percentage'];
                $DataArray[$key]['last_net_revenue_usd_class'] = $LastNRevPercentage['class'];
                $DataArray[$key]['last_net_revenue_usd_arrow'] = $LastNRevPercentage['arrow'];

                $LastAvgGRevPercentage = $this->classPercentage($value['prev_avg_gross_revenue_usd'], $value['last_avg_gross_revenue_usd']);
                $DataArray[$key]['last_avg_gross_revenue_usd_percentage'] = $LastAvgGRevPercentage['percentage'];
                $DataArray[$key]['last_avg_gross_revenue_usd_class'] = $LastAvgGRevPercentage['class'];
                $DataArray[$key]['last_avg_gross_revenue_usd_arrow'] = $LastAvgGRevPercentage['arrow'];

                $LastAvgNRevPercentage = $this->classPercentage($value['prev_avg_net_revenue_usd'], $value['last_avg_net_revenue_usd']);
                $DataArray[$key]['last_avg_net_revenue_usd_percentage'] = $LastAvgNRevPercentage['percentage'];
                $DataArray[$key]['last_avg_net_revenue_usd_class'] = $LastAvgNRevPercentage['class'];
                $DataArray[$key]['last_avg_net_revenue_usd_arrow'] = $LastAvgNRevPercentage['arrow'];


                $LastMOPercentage = $this->classPercentage($value['prev_total_mo'], $value['last_total_mo']);
                $DataArray[$key]['last_total_mo_percentage'] = $LastMOPercentage['percentage'];
                $DataArray[$key]['last_total_mo_class'] = $LastMOPercentage['class'];
                $DataArray[$key]['last_total_mo_arrow'] = $LastMOPercentage['arrow'];

                $LastAvgMOPercentage = $this->classPercentage($value['prev_avg_mo'], $value['last_avg_mo']);
                $DataArray[$key]['last_avg_mo_percentage'] = $LastAvgMOPercentage['percentage'];
                $DataArray[$key]['last_avg_mo_class'] = $LastAvgMOPercentage['class'];
                $DataArray[$key]['last_avg_mo_arrow'] = $LastAvgMOPercentage['arrow'];

                $LastRegPercentage = $this->classPercentage($value['prev_mo'], $value['last_mo']);
                $DataArray[$key]['last_mo_percentage'] = $LastRegPercentage['percentage'];
                $DataArray[$key]['last_mo_class'] = $LastRegPercentage['class'];
                $DataArray[$key]['last_mo_arrow'] = $LastRegPercentage['arrow'];

                $LastCostPercentage = $this->classPercentage($value['prev_cost'], $value['last_cost']);
                $DataArray[$key]['last_cost_percentage'] = $LastCostPercentage['percentage'];
                $DataArray[$key]['last_cost_class'] = $LastCostPercentage['class'];
                $DataArray[$key]['last_cost_arrow'] = $LastCostPercentage['arrow'];

                $LastPriceMOPercentage = $this->classPercentage($value['prev_price_mo'], $value['last_price_mo']);
                $DataArray[$key]['last_price_mo_percentage'] = $LastPriceMOPercentage['percentage'];
                $DataArray[$key]['last_price_mo_class'] = $LastPriceMOPercentage['class'];
                $DataArray[$key]['last_price_mo_arrow'] = $LastPriceMOPercentage['arrow'];

                $LastROIPercentage = $this->classPercentage($value['previousMonthROI'], $value['lastMonthROI']);
                $DataArray[$key]['lastMonthROI_percentage'] = $LastROIPercentage['percentage'];
                $DataArray[$key]['lastMonthROI_class'] = $LastROIPercentage['class'];
                $DataArray[$key]['lastMonthROI_arrow'] = $LastROIPercentage['arrow'];

                $LastArpuPercentage = $this->classPercentage($value['prev_30_arpu'], $value['last_30_arpu']);
                $DataArray[$key]['last_30_arpu_percentage'] = $LastArpuPercentage['percentage'];
                $DataArray[$key]['last_30_arpu_class'] = $LastArpuPercentage['class'];
                $DataArray[$key]['last_30_arpu_arrow'] = $LastArpuPercentage['arrow'];

                $LastPnlPercentage = $this->classPercentage($value['prev_pnl'], $value['last_pnl']);
                $DataArray[$key]['last_pnl_percentage'] = $LastPnlPercentage['percentage'];
                $DataArray[$key]['last_pnl_class'] = $LastPnlPercentage['class'];
                $DataArray[$key]['last_pnl_arrow'] = $LastPnlPercentage['arrow'];

                $LastAvgPnlPercentage = $this->classPercentage($value['prev_avg_pnl'], $value['last_avg_pnl']);
                $DataArray[$key]['last_avg_pnl_percentage'] = $LastAvgPnlPercentage['percentage'];
                $DataArray[$key]['last_avg_pnl_class'] = $LastAvgPnlPercentage['class'];
                $DataArray[$key]['last_avg_pnl_arrow'] = $LastAvgPnlPercentage['arrow'];
            }

            $sumemry = $DataArray;
            $allDataSum = UtilityReports::DashboardAllDataSum($sumemry);

            $CurrentRevClass = $this->classPercentage($allDataSum['last_revenue_usd'], $allDataSum['current_revenue_usd']);
            $allDataSum['current_revenue_usd_percentage'] = $CurrentRevClass['percentage'];
            $allDataSum['current_revenue_usd_class'] = $CurrentRevClass['class'];
            $allDataSum['current_revenue_usd_arrow'] = $CurrentRevClass['arrow'];

            $CurrentRevPercentage = $this->classPercentage($allDataSum['last_revenue_usd'], $allDataSum['estimated_revenue_usd']);
            $allDataSum['estimated_revenue_usd_percentage'] = $CurrentRevPercentage['percentage'];
            $allDataSum['estimated_revenue_usd_class'] = $CurrentRevPercentage['class'];
            $allDataSum['estimated_revenue_usd_arrow'] = $CurrentRevPercentage['arrow'];

            $CurrentAvgRevPercentage = $this->classPercentage($allDataSum['last_avg_revenue_usd'], $allDataSum['estimated_avg_revenue_usd']);
            $allDataSum['estimated_avg_revenue_usd_percentage'] = $CurrentAvgRevPercentage['percentage'];
            $allDataSum['estimated_avg_revenue_usd_class'] = $CurrentAvgRevPercentage['class'];
            $allDataSum['estimated_avg_revenue_usd_arrow'] = $CurrentAvgRevPercentage['arrow'];

            $CurrentGRevClass = $this->classPercentage($allDataSum['last_gross_revenue_usd'], $allDataSum['current_gross_revenue_usd']);
            $allDataSum['current_gross_revenue_usd_percentage'] = $CurrentGRevClass['percentage'];
            $allDataSum['current_gross_revenue_usd_class'] = $CurrentGRevClass['class'];
            $allDataSum['current_gross_revenue_usd_arrow'] = $CurrentGRevClass['arrow'];

            $CurrentNRevClass = $this->classPercentage($allDataSum['last_net_revenue_usd'], $allDataSum['current_net_revenue_usd']);
            $allDataSum['current_net_revenue_usd_percentage'] = $CurrentNRevClass['percentage'];
            $allDataSum['current_net_revenue_usd_class'] = $CurrentNRevClass['class'];
            $allDataSum['current_net_revenue_usd_arrow'] = $CurrentNRevClass['arrow'];

            $CurrentGRevPercentage = $this->classPercentage($allDataSum['last_gross_revenue_usd'], $allDataSum['estimated_gross_revenue_usd']);
            $allDataSum['estimated_gross_revenue_usd_percentage'] = $CurrentGRevPercentage['percentage'];
            $allDataSum['estimated_gross_revenue_usd_class'] = $CurrentGRevPercentage['class'];
            $allDataSum['estimated_gross_revenue_usd_arrow'] = $CurrentGRevPercentage['arrow'];

            $CurrentAvgGRevPercentage = $this->classPercentage($allDataSum['last_avg_gross_revenue_usd'], $allDataSum['estimated_avg_gross_revenue_usd']);
            $allDataSum['estimated_avg_gross_revenue_usd_percentage'] = $CurrentAvgGRevPercentage['percentage'];
            $allDataSum['estimated_avg_gross_revenue_usd_class'] = $CurrentAvgGRevPercentage['class'];
            $allDataSum['estimated_avg_gross_revenue_usd_arrow'] = $CurrentAvgGRevPercentage['arrow'];

            $CurrentNRevPercentage = $this->classPercentage($allDataSum['last_net_revenue_usd'], $allDataSum['estimated_net_revenue_usd']);
            $allDataSum['estimated_net_revenue_usd_percentage'] = $CurrentNRevPercentage['percentage'];
            $allDataSum['estimated_net_revenue_usd_class'] = $CurrentNRevPercentage['class'];
            $allDataSum['estimated_net_revenue_usd_arrow'] = $CurrentNRevPercentage['arrow'];

            $CurrentAvgNRevPercentage = $this->classPercentage($allDataSum['last_avg_net_revenue_usd'], $allDataSum['estimated_avg_net_revenue_usd']);
            $allDataSum['estimated_avg_net_revenue_usd_percentage'] = $CurrentAvgNRevPercentage['percentage'];
            $allDataSum['estimated_avg_net_revenue_usd_class'] = $CurrentAvgNRevPercentage['class'];
            $allDataSum['estimated_avg_net_revenue_usd_arrow'] = $CurrentAvgNRevPercentage['arrow'];

            $CurrentMOClass = $this->classPercentage($allDataSum['last_total_mo'], $allDataSum['current_total_mo']);
            $allDataSum['current_total_mo_percentage'] = $CurrentMOClass['percentage'];
            $allDataSum['current_total_mo_class'] = $CurrentMOClass['class'];
            $allDataSum['current_total_mo_arrow'] = $CurrentMOClass['arrow'];

            $CurrentRegClass = $this->classPercentage($allDataSum['last_mo'], $allDataSum['current_mo']);
            $allDataSum['current_mo_percentage'] = $CurrentRegClass['percentage'];
            $allDataSum['current_mo_class'] = $CurrentRegClass['class'];
            $allDataSum['current_mo_arrow'] = $CurrentRegClass['arrow'];

            $CurrentMOPercentage = $this->classPercentage($allDataSum['last_total_mo'], $allDataSum['estimated_total_mo']);
            $allDataSum['estimated_total_mo_percentage'] = $CurrentMOPercentage['percentage'];
            $allDataSum['estimated_total_mo_class'] = $CurrentMOPercentage['class'];
            $allDataSum['estimated_total_mo_arrow'] = $CurrentMOPercentage['arrow'];

            $CurrentAvgMOPercentage = $this->classPercentage($allDataSum['last_avg_mo'], $allDataSum['estimated_avg_mo']);
            $allDataSum['estimated_avg_mo_percentage'] = $CurrentAvgMOPercentage['percentage'];
            $allDataSum['estimated_avg_mo_class'] = $CurrentAvgMOPercentage['class'];
            $allDataSum['estimated_avg_mo_arrow'] = $CurrentAvgMOPercentage['arrow'];

            $CurrentRegPercentage = $this->classPercentage($allDataSum['last_mo'], $allDataSum['estimated_mo']);
            $allDataSum['estimated_mo_percentage'] = $CurrentRegPercentage['percentage'];
            $allDataSum['estimated_mo_class'] = $CurrentRegPercentage['class'];
            $allDataSum['estimated_mo_arrow'] = $CurrentRegPercentage['arrow'];

            $CurrentCostClass = $this->classPercentage($allDataSum['last_cost'], $allDataSum['current_cost']);
            $allDataSum['current_cost_percentage'] = $CurrentCostClass['percentage'];
            $allDataSum['current_cost_class'] = $CurrentCostClass['class'];
            $allDataSum['current_cost_arrow'] = $CurrentCostClass['arrow'];

            $CurrentCostPercentage = $this->classPercentage($allDataSum['last_cost'], $allDataSum['estimated_cost']);
            $allDataSum['estimated_cost_percentage'] = $CurrentCostPercentage['percentage'];
            $allDataSum['estimated_cost_class'] = $CurrentCostPercentage['class'];
            $allDataSum['estimated_cost_arrow'] = $CurrentCostPercentage['arrow'];

            $CurrentPriceMOClass = $this->classPercentage($allDataSum['last_price_mo'], $allDataSum['current_price_mo']);
            $allDataSum['current_price_mo_percentage'] = $CurrentPriceMOClass['percentage'];
            $allDataSum['current_price_mo_class'] = $CurrentPriceMOClass['class'];
            $allDataSum['current_price_mo_arrow'] = $CurrentPriceMOClass['arrow'];

            $CurrentPriceMOPercentage = $this->classPercentage($allDataSum['last_price_mo'], $allDataSum['estimated_price_mo']);
            $allDataSum['estimated_price_mo_percentage'] = $CurrentPriceMOPercentage['percentage'];
            $allDataSum['estimated_price_mo_class'] = $CurrentPriceMOPercentage['class'];
            $allDataSum['estimated_price_mo_arrow'] = $CurrentPriceMOPercentage['arrow'];

            $CurrentROIClass = $this->classPercentage($allDataSum['lastMonthROI'], $allDataSum['currentMonthROI']);
            $allDataSum['currentMonthROI_percentage'] = $CurrentROIClass['percentage'];
            $allDataSum['currentMonthROI_class'] = $CurrentROIClass['class'];
            $allDataSum['currentMonthROI_arrow'] = $CurrentROIClass['arrow'];

            $CurrentROIPercentage = $this->classPercentage($allDataSum['lastMonthROI'], $allDataSum['estimatedMonthROI']);
            $allDataSum['estimatedMonthROI_percentage'] = $CurrentROIPercentage['percentage'];
            $allDataSum['estimatedMonthROI_class'] = $CurrentROIPercentage['class'];
            $allDataSum['estimatedMonthROI_arrow'] = $CurrentROIPercentage['arrow'];

            $CurrentArpuClass = $this->classPercentage($allDataSum['last_30_arpu'], $allDataSum['current_30_arpu']);
            $allDataSum['current_30_arpu_percentage'] = $CurrentArpuClass['percentage'];
            $allDataSum['current_30_arpu_class'] = $CurrentArpuClass['class'];
            $allDataSum['current_30_arpu_arrow'] = $CurrentArpuClass['arrow'];

            $CurrentArpuPercentage = $this->classPercentage($allDataSum['last_30_arpu'], $allDataSum['estimated_30_arpu']);
            $allDataSum['estimated_30_arpu_percentage'] = $CurrentArpuPercentage['percentage'];
            $allDataSum['estimated_30_arpu_class'] = $CurrentArpuPercentage['class'];
            $allDataSum['estimated_30_arpu_arrow'] = $CurrentArpuPercentage['arrow'];

            $CurrentPnlClass = $this->classPercentage($allDataSum['last_pnl'], $allDataSum['current_pnl']);
            $allDataSum['current_pnl_percentage'] = $CurrentPnlClass['percentage'];
            $allDataSum['current_pnl_class'] = $CurrentPnlClass['class'];
            $allDataSum['current_pnl_arrow'] = $CurrentPnlClass['arrow'];

            $CurrentPnlPercentage = $this->classPercentage($allDataSum['last_pnl'], $allDataSum['estimated_pnl']);
            $allDataSum['estimated_pnl_percentage'] = $CurrentPnlPercentage['percentage'];
            $allDataSum['estimated_pnl_class'] = $CurrentPnlPercentage['class'];
            $allDataSum['estimated_pnl_arrow'] = $CurrentPnlPercentage['arrow'];

            $CurrentAvgPnlPercentage = $this->classPercentage($allDataSum['last_avg_pnl'], $allDataSum['estimated_avg_pnl']);
            $allDataSum['estimated_avg_pnl_percentage'] = $CurrentAvgPnlPercentage['percentage'];
            $allDataSum['estimated_avg_pnl_class'] = $CurrentAvgPnlPercentage['class'];
            $allDataSum['estimated_avg_pnl_arrow'] = $CurrentAvgPnlPercentage['arrow'];


            $LastRevPercentage = $this->classPercentage($allDataSum['prev_revenue_usd'], $allDataSum['last_revenue_usd']);
            $allDataSum['last_revenue_usd_percentage'] = $LastRevPercentage['percentage'];
            $allDataSum['last_revenue_usd_class'] = $LastRevPercentage['class'];
            $allDataSum['last_revenue_usd_arrow'] = $LastRevPercentage['arrow'];

            $LastAvgRevPercentage = $this->classPercentage($allDataSum['prev_avg_revenue_usd'], $allDataSum['last_avg_revenue_usd']);
            $allDataSum['last_avg_revenue_usd_percentage'] = $LastAvgRevPercentage['percentage'];
            $allDataSum['last_avg_revenue_usd_class'] = $LastAvgRevPercentage['class'];
            $allDataSum['last_avg_revenue_usd_arrow'] = $LastAvgRevPercentage['arrow'];

            $LastGRevPercentage = $this->classPercentage($allDataSum['prev_gross_revenue_usd'], $allDataSum['last_gross_revenue_usd']);
            $allDataSum['last_gross_revenue_usd_percentage'] = $LastGRevPercentage['percentage'];
            $allDataSum['last_gross_revenue_usd_class'] = $LastGRevPercentage['class'];
            $allDataSum['last_gross_revenue_usd_arrow'] = $LastGRevPercentage['arrow'];

            $LastNRevPercentage = $this->classPercentage($allDataSum['prev_net_revenue_usd'], $allDataSum['last_net_revenue_usd']);
            $allDataSum['last_net_revenue_usd_percentage'] = $LastNRevPercentage['percentage'];
            $allDataSum['last_net_revenue_usd_class'] = $LastNRevPercentage['class'];
            $allDataSum['last_net_revenue_usd_arrow'] = $LastNRevPercentage['arrow'];

            $LastAvgGRevPercentage = $this->classPercentage($allDataSum['prev_avg_gross_revenue_usd'], $allDataSum['last_avg_gross_revenue_usd']);
            $allDataSum['last_avg_gross_revenue_usd_percentage'] = $LastAvgGRevPercentage['percentage'];
            $allDataSum['last_avg_gross_revenue_usd_class'] = $LastAvgGRevPercentage['class'];
            $allDataSum['last_avg_gross_revenue_usd_arrow'] = $LastAvgGRevPercentage['arrow'];

            $LastAvgNRevPercentage = $this->classPercentage($allDataSum['prev_avg_net_revenue_usd'], $allDataSum['last_avg_net_revenue_usd']);
            $allDataSum['last_avg_net_revenue_usd_percentage'] = $LastAvgNRevPercentage['percentage'];
            $allDataSum['last_avg_net_revenue_usd_class'] = $LastAvgNRevPercentage['class'];
            $allDataSum['last_avg_net_revenue_usd_arrow'] = $LastAvgNRevPercentage['arrow'];

            $LastMOPercentage = $this->classPercentage($allDataSum['prev_total_mo'], $allDataSum['last_total_mo']);
            $allDataSum['last_total_mo_percentage'] = $LastMOPercentage['percentage'];
            $allDataSum['last_total_mo_class'] = $LastMOPercentage['class'];
            $allDataSum['last_total_mo_arrow'] = $LastMOPercentage['arrow'];

            $LastAvgMOPercentage = $this->classPercentage($allDataSum['prev_avg_mo'], $allDataSum['last_avg_mo']);
            $allDataSum['last_avg_mo_percentage'] = $LastAvgMOPercentage['percentage'];
            $allDataSum['last_avg_mo_class'] = $LastAvgMOPercentage['class'];
            $allDataSum['last_avg_mo_arrow'] = $LastAvgMOPercentage['arrow'];

            $LastRegPercentage = $this->classPercentage($allDataSum['prev_mo'], $allDataSum['last_mo']);
            $allDataSum['last_mo_percentage'] = $LastRegPercentage['percentage'];
            $allDataSum['last_mo_class'] = $LastRegPercentage['class'];
            $allDataSum['last_mo_arrow'] = $LastRegPercentage['arrow'];

            $LastCostPercentage = $this->classPercentage($allDataSum['prev_cost'], $allDataSum['last_cost']);
            $allDataSum['last_cost_percentage'] = $LastCostPercentage['percentage'];
            $allDataSum['last_cost_class'] = $LastCostPercentage['class'];
            $allDataSum['last_cost_arrow'] = $LastCostPercentage['arrow'];

            $LastPriceMOPercentage = $this->classPercentage($allDataSum['prev_price_mo'], $allDataSum['last_price_mo']);
            $allDataSum['last_price_mo_percentage'] = $LastPriceMOPercentage['percentage'];
            $allDataSum['last_price_mo_class'] = $LastPriceMOPercentage['class'];
            $allDataSum['last_price_mo_arrow'] = $LastPriceMOPercentage['arrow'];

            $LastROIPercentage = $this->classPercentage($allDataSum['previousMonthROI'], $allDataSum['lastMonthROI']);
            $allDataSum['lastMonthROI_percentage'] = $LastROIPercentage['percentage'];
            $allDataSum['lastMonthROI_class'] = $LastROIPercentage['class'];
            $allDataSum['lastMonthROI_arrow'] = $LastROIPercentage['arrow'];

            $LastArpuPercentage = $this->classPercentage($allDataSum['prev_30_arpu'], $allDataSum['last_30_arpu']);
            $allDataSum['last_30_arpu_percentage'] = $LastArpuPercentage['percentage'];
            $allDataSum['last_30_arpu_class'] = $LastArpuPercentage['class'];
            $allDataSum['last_30_arpu_arrow'] = $LastArpuPercentage['arrow'];

            $LastPnlPercentage = $this->classPercentage($allDataSum['prev_pnl'], $allDataSum['last_pnl']);
            $allDataSum['last_pnl_percentage'] = $LastPnlPercentage['percentage'];
            $allDataSum['last_pnl_class'] = $LastPnlPercentage['class'];
            $allDataSum['last_pnl_arrow'] = $LastPnlPercentage['arrow'];

            $LastAvgPnlPercentage = $this->classPercentage($allDataSum['prev_avg_pnl'], $allDataSum['last_avg_pnl']);
            $allDataSum['last_avg_pnl_percentage'] = $LastAvgPnlPercentage['percentage'];
            $allDataSum['last_avg_pnl_class'] = $LastAvgPnlPercentage['class'];
            $allDataSum['last_avg_pnl_arrow'] = $LastAvgPnlPercentage['arrow'];

            return view('admin.business_dashboard', compact('sumemry', 'allDataSum'));
        } else {
            return redirect()->route('login');
        }
    }

    public function classPercentage($PreviousData, $CurrentData)
    {
        if ((float)$PreviousData > (float)$CurrentData) {
            $class = 'text-danger';
            $arrow = 'fa-arrow-down';
        } elseif ((float)$PreviousData == (float)$CurrentData) {
            $class = '';
            $arrow = '';
        } else {
            $class = 'text-success';
            $arrow = 'fa-arrow-up';
        }

        $percentage = 0;

        if ($PreviousData > 0)
            $percentage = (((float)$CurrentData - (float)$PreviousData) * 100) / $PreviousData;

        $data = ['class' => $class, 'arrow' => $arrow, 'percentage' => round($percentage, 1)];

        return $data;
    }

    // get report by country id
    function getReportsByCountryID($reports)
    {
        if (!empty($reports)) {
            $reportsResult = array();
            $tempreport = array();

            foreach ($reports as $report) {
                $tempreport[$report['country_id']] = $report;
            }

            $reportsResult = $tempreport;
            return $reportsResult;
        }
    }

    function getDashboardByOperatorID($reports)
    {
        if (!empty($reports)) {
            $reportsResult = array();
            $tempreport = array();

            foreach ($reports as $report) {
                $tempreport[$report['operator_id']] = $report;
            }

            $reportsResult = $tempreport;
            return $reportsResult;
        }
    }

    function getReportsByOperatorID($reports)
    {
        if (!empty($reports)) {
            $reportsResult = array();
            $tempreport = array();

            foreach ($reports as $report) {
                $tempreport[$report['id_operator']] = $report;
            }

            $reportsResult = $tempreport;
            return $reportsResult;
        }
    }

    function getUserReportsByOperatorID($reports)
    {
        if (!empty($reports)) {
            $reportsResult = array();
            $tempreport = array();

            foreach ($reports as $report) {
                $tempreport[$report['operator_id']] = $report;
            }

            $reportsResult = $tempreport;
            return $reportsResult;
        }
    }
}
