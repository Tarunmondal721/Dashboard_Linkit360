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
use App\Models\Operator;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use App\Models\Company;
use App\Models\Country;
use App\Models\User;
use App\Models\role_operators;
use App\Models\CompanyOperators;
use App\Models\ReportsPnlsOperatorSummarizes;
use App\common\Utility;
use App\common\UtilityReports;
use App\common\UtilityPercentage;
use App\common\UtilityReportsMonthly;
use App\Models\ServiceHistory;
use App\Models\UsersOperatorsServices;
use App\common\UtilityMobifone;
use App\common\UtilityAccountManager;
use Config;
use Redirect;
use App\Models\Reconcialiation;
use Excel;
use App\Imports\UsersImport;

class ReportController extends Controller
{
    public function summary(Request $request)
    {
        if (\Auth::user()->can('Report Summary')) {
            // get filtre request
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

            if ($request->filled('company') && $req_CompanyId != "allcompany" && !$request->filled('operatorId')) {
                $companies = Company::Find($req_CompanyId);
                $Operators_company = array();

                if (!empty($companies)) {
                    $Operators_company = $companies->company_operators->pluck('operator_id')->toArray();
                    $Operators = Operator::with('revenueshare', 'RevenushareByDate')
                        ->Status(1)
                        ->GetOperatorByOperatorId($Operators_company)
                        ->get();
                }

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
                $Operators = Operator::with('revenueshare', 'RevenushareByDate')
                    ->Status(1)
                    ->GetOperatorByOperatorId($filterOperator)
                    ->get();

                $showAllOperator = false;
            }

            if ($showAllOperator) {
                $Operators = Operator::with('revenueshare', 'RevenushareByDate')->Status(1)->get();
            }

            if (!isset($Operators)) {
                $request->session()->flash('alert-success', 'User was successful added!');
                return redirect()->back();
            }

            /* filter End Section */

            /* All country retrive becuase Avoid nested query for each operator country */

            $Country = Country::all()->toArray();
            $countries = array();

            if (!empty($Country)) {
                foreach ($Country as $CountryI) {
                    $countries[$CountryI['id']] = $CountryI;
                }
            }

            /* End All country retrive becuase Avoid nested query for each operator country */

            $arrayOperatorsIds = $Operators->pluck('id_operator')->toArray();

            $sumemry = array();

            /* Admin Access All operator and Services */

            $user = Auth::user();

            $user_id = $user->id;
            $user_type = $user->type;
            $allowAllOperator = $user->WhowAccessAlOperator($user_type);

            if ($allowAllOperator) {
                $reports = report_summarize::filteroperator($arrayOperatorsIds)
                    ->filterDateRange($start_date, $end_date)
                    ->orderBy('operator_id')
                    ->get()
                    ->toArray();
            } else // Normal User The operator and Service Set From Admin Panel
            {
                $UserOperatorServices = Session::get('userOperatorService');

                if (empty($UserOperatorServices)) {
                    dd("Please Contact to admin , add Operator to your account");
                }

                $arrayOperatorsIds = $UserOperatorServices['id_operators'];
                $arrayServicesIds = $UserOperatorServices['id_services'];

                $reports = ReportSummeriseUsers::filteroperator($arrayOperatorsIds)
                    ->filterDateRange($start_date, $end_date)
                    ->User($user_id)
                    ->orderBy('operator_id')
                    ->get()
                    ->toArray();
            }

            $firstDay = Carbon::createFromFormat('Y-m-d', $startColumnDateDisplay)->firstOfMonth()->format('Y-m-d');

            $activeSubs = report_summarize::select('total', 'date', 'operator_id')->filteroperator($arrayOperatorsIds)->filterDate($firstDay)->get()->toArray();

            $cost_campaign = ReportsPnlsOperatorSummarizes::select('id_operator as operator_id', 'date', 'cost_campaign')->filteroperator($arrayOperatorsIds)->filterDateRange($start_date, $end_date)->get()->toArray();

            $reportsByIDs = $this->getReportsOperatorID($reports);
            $activesubsdata = $this->getReportsOperatorID($activeSubs);
            $costdata = $this->getReportsOperatorID($cost_campaign);

            $datesIndividual = Utility::getRangeDates($startColumnDateDisplay, $end_date);
            $no_of_days = Utility::getRangeDateNo($datesIndividual);

            if (!empty($Operators)) {
                foreach ($Operators as $operator) {
                    $tmpOperators = array();

                    $tmpOperators['operator'] = $operator;
                    $country_id  = $operator->country_id;
                    $contain_id = Arr::exists($countries, $country_id);
                    $OperatorCountry = array();

                    if ($contain_id) {
                        $tmpOperators['country'] = $countries[$country_id];
                        $OperatorCountry = $countries[$country_id];
                    }

                    $reportsColumnData = $this->getReportsDateWise($operator, $no_of_days, $reportsByIDs, $activesubsdata, $costdata, $OperatorCountry);

                    $tmpOperators['month_string'] = $month;
                    $tmpOperators['last_update'] = $reportsColumnData['last_update'];

                    $total_avg_t = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['tur'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['tur']['dates'] = $reportsColumnData['tur'];
                    $tmpOperators['tur']['total'] = $total_avg_t['sum'];
                    $tmpOperators['tur']['t_mo_end'] = $total_avg_t['T_Mo_End'];;
                    $tmpOperators['tur']['avg'] = $total_avg_t['avg'];

                    $total_avg_rev = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['t_rev'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['t_rev']['dates'] = $reportsColumnData['t_rev'];
                    $tmpOperators['t_rev']['total'] = $total_avg_rev['sum'];
                    $tmpOperators['t_rev']['t_mo_end'] = $total_avg_rev['T_Mo_End'];;
                    $tmpOperators['t_rev']['avg'] = $total_avg_rev['avg'];

                    $total_avg_trat = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['trat'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['trat']['dates'] = $reportsColumnData['trat'];
                    $tmpOperators['trat']['total'] = $total_avg_trat['sum'];
                    $tmpOperators['trat']['t_mo_end'] = $total_avg_trat['T_Mo_End'];
                    $tmpOperators['trat']['avg'] = $total_avg_trat['avg'];

                    $total_avg_turt = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['turt'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['turt']['dates'] = $reportsColumnData['turt'];
                    $tmpOperators['turt']['total'] = $total_avg_turt['sum'];
                    $tmpOperators['turt']['t_mo_end'] = $total_avg_turt['T_Mo_End'];;
                    $tmpOperators['turt']['avg'] = $total_avg_turt['avg'];;

                    $total_avg_net_rev = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['net_rev'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['net_rev']['dates'] = $reportsColumnData['net_rev'];
                    $tmpOperators['net_rev']['total'] = $total_avg_net_rev['sum'];
                    $tmpOperators['net_rev']['t_mo_end'] = $total_avg_net_rev['T_Mo_End'];
                    $tmpOperators['net_rev']['avg'] = $total_avg_net_rev['avg'];

                    $total_avg_t_sub = UtilityReports::calculateTotalSubscribe($operator, $reportsColumnData['t_sub'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['t_sub']['dates'] = $reportsColumnData['t_sub'];
                    $tmpOperators['t_sub']['total'] = $total_avg_t_sub['sum'];
                    $tmpOperators['t_sub']['t_mo_end'] = $total_avg_t_sub['T_Mo_End'];
                    $tmpOperators['t_sub']['avg'] = $total_avg_t_sub['avg'];

                    $total_avg_t_reg = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['reg'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['reg']['dates'] = $reportsColumnData['reg'];
                    $tmpOperators['reg']['total'] = $total_avg_t_reg['sum'];
                    $tmpOperators['reg']['t_mo_end'] = $total_avg_t_reg['T_Mo_End'];
                    $tmpOperators['reg']['avg'] = $total_avg_t_reg['avg'];

                    $total_avg_t_unreg = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['unreg'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['unreg']['dates'] = $reportsColumnData['unreg'];
                    $tmpOperators['unreg']['total'] = $total_avg_t_unreg['sum'];
                    $tmpOperators['unreg']['t_mo_end'] = $total_avg_t_unreg['T_Mo_End'];;
                    $tmpOperators['unreg']['avg'] = $total_avg_t_unreg['avg'];

                    $total_avg_t_purged = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['purged'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['purged']['dates'] = $reportsColumnData['purged'];
                    $tmpOperators['purged']['total'] = $total_avg_t_purged['sum'];
                    $tmpOperators['purged']['t_mo_end'] = $total_avg_t_purged['T_Mo_End'];
                    $tmpOperators['purged']['avg'] = $total_avg_t_purged['avg'];

                    $total_avg_t_churn = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['churn'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['churn']['dates'] = $reportsColumnData['churn'];
                    $tmpOperators['churn']['total'] = 0;
                    $tmpOperators['churn']['t_mo_end'] = 0;
                    $tmpOperators['churn']['avg'] = $total_avg_t_churn['avg'];

                    $total_avg_t_renewal = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['renewal'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['renewal']['dates'] = $reportsColumnData['renewal'];
                    $tmpOperators['renewal']['total'] = $total_avg_t_renewal['sum'];
                    $tmpOperators['renewal']['t_mo_end'] = $total_avg_t_renewal['T_Mo_End'];
                    $tmpOperators['renewal']['avg'] = $total_avg_t_renewal['avg'];

                    $total_avg_mt_success = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['daily_push_success'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['daily_push_success']['dates'] = $reportsColumnData['daily_push_success'];
                    $tmpOperators['daily_push_success']['total'] = $total_avg_mt_success['sum'];
                    $tmpOperators['daily_push_success']['t_mo_end'] = $total_avg_mt_success['T_Mo_End'];
                    $tmpOperators['daily_push_success']['avg'] = $total_avg_mt_success['avg'];

                    $total_avg_mt_failed = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['daily_push_failed'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['daily_push_failed']['dates'] = $reportsColumnData['daily_push_failed'];
                    $tmpOperators['daily_push_failed']['total'] = $total_avg_mt_failed['sum'];
                    $tmpOperators['daily_push_failed']['t_mo_end'] = $total_avg_mt_failed['T_Mo_End'];
                    $tmpOperators['daily_push_failed']['avg'] = $total_avg_mt_failed['avg'];

                    $total_avg_t_bill = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['bill'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['bill']['dates'] = $reportsColumnData['bill'];
                    $tmpOperators['bill']['total'] = 0;
                    $tmpOperators['bill']['t_mo_end'] = 0;
                    $tmpOperators['bill']['avg'] = $total_avg_t_bill['avg'];

                    $total_avg_t_first_push = UtilityPercentage::PercentageDataAVG($operator, $reportsColumnData['first_push'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['first_push']['dates'] = $reportsColumnData['first_push'];
                    $tmpOperators['first_push']['total'] = 0;
                    $tmpOperators['first_push']['t_mo_end'] = 0;
                    $tmpOperators['first_push']['avg'] = $total_avg_t_first_push['avg'];

                    $total_avg_t_daily_push = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['daily_push'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['daily_push']['dates'] = $reportsColumnData['daily_push'];
                    $tmpOperators['daily_push']['total'] = 0;
                    $tmpOperators['daily_push']['t_mo_end'] = 0;
                    $tmpOperators['daily_push']['avg'] = $total_avg_t_daily_push['avg'];

                    $total_avg_t_arpu7 = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['arpu7'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['arpu7']['dates'] = $reportsColumnData['arpu7'];
                    $tmpOperators['arpu7']['total'] = 0;
                    $tmpOperators['arpu7']['t_mo_end'] = 0;
                    $tmpOperators['arpu7']['avg'] = $total_avg_t_arpu7['avg'];

                    $tmpOperators['arpu7raw']['dates'] = $reportsColumnData['arpu7Raw'];
                    $tmpOperators['arpu7raw']['total'] = 0;
                    $tmpOperators['arpu7raw']['t_mo_end'] = 0;
                    $tmpOperators['arpu7raw']['avg'] = 0;

                    $tmpOperators['arpu30raw']['dates'] = $reportsColumnData['arpu30Raw'];
                    $tmpOperators['arpu30raw']['total'] = 0;
                    $tmpOperators['arpu30raw']['t_mo_end'] = 0;
                    $tmpOperators['arpu30raw']['avg'] = 0;

                    $total_avg_t_usarpu7 = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['usarpu7'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['usarpu7']['dates'] = $reportsColumnData['usarpu7'];
                    $tmpOperators['usarpu7']['total'] = 0;
                    $tmpOperators['usarpu7']['t_mo_end'] = 0;
                    $tmpOperators['usarpu7']['avg'] = $total_avg_t_usarpu7['avg'];

                    $total_avg_t_arpu30 = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['arpu30'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['arpu30']['dates'] = $reportsColumnData['arpu30'];
                    $tmpOperators['arpu30']['total'] = 0;
                    $tmpOperators['arpu30']['t_mo_end'] = 0;
                    $tmpOperators['arpu30']['avg'] = $total_avg_t_arpu30['avg'];

                    $total_avg_t_usarpu30 = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['usarpu30'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['usarpu30']['dates'] = $reportsColumnData['usarpu30'];
                    $tmpOperators['usarpu30']['total'] = 0;
                    $tmpOperators['usarpu30']['t_mo_end'] = 0;
                    $tmpOperators['usarpu30']['avg'] = $total_avg_t_usarpu30['avg'];

                    $total_avg_first_day_active = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['first_day_active'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['first_day_active']['dates'] = $reportsColumnData['first_day_active'];
                    $tmpOperators['first_day_active']['total'] = $total_avg_first_day_active['sum'];
                    $tmpOperators['first_day_active']['t_mo_end'] = $total_avg_first_day_active['T_Mo_End'];
                    $tmpOperators['first_day_active']['avg'] = $total_avg_first_day_active['avg'];

                    $total_avg_cost_campaign = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['cost_campaign'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['cost_campaign']['dates'] = $reportsColumnData['cost_campaign'];
                    $tmpOperators['cost_campaign']['total'] = $total_avg_cost_campaign['sum'];
                    $tmpOperators['cost_campaign']['t_mo_end'] = $total_avg_cost_campaign['T_Mo_End'];
                    $tmpOperators['cost_campaign']['avg'] = $total_avg_cost_campaign['avg'];

                    $total_avg_ltv = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['ltv'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['ltv']['dates'] = $reportsColumnData['ltv'];
                    $tmpOperators['ltv']['total'] = $total_avg_ltv['sum'];
                    $tmpOperators['ltv']['t_mo_end'] = $total_avg_ltv['T_Mo_End'];
                    $tmpOperators['ltv']['avg'] = $total_avg_ltv['avg'];

                    $tmpOperators['mt_success']['dates'] = $reportsColumnData['mt_success'];
                    $tmpOperators['mt_failed']['dates'] = $reportsColumnData['mt_failed'];

                    $tmpOperators['fmt_success']['dates'] = $reportsColumnData['fmt_success'];
                    $tmpOperators['fmt_failed']['dates'] = $reportsColumnData['fmt_failed'];

                    $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators, "tur");
                    $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators, "turt");
                    $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators, "net_rev");
                    $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators, "t_rev");
                    $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators, "t_sub");
                    $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators, "trat");
                    $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators, "reg");
                    $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators, "unreg");
                    $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators, "purged");
                    $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators, "churn");
                    $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators, "renewal");
                    $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators, "daily_push_success");
                    $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators, "daily_push_failed");
                    $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators, "bill");
                    $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators, "first_push");
                    $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators, "daily_push");
                    $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators, "arpu7");
                    $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators, "usarpu7");
                    $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators, "arpu30");
                    $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators, "usarpu30");
                    $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators, "ltv");

                    $sumemry[] = $tmpOperators;
                }
            }

            $allsummaryData = [];

            if (sizeof($sumemry) > 0) {
                /*sum of all summery datas*/
                $allsummaryData = UtilityReports::allsummaryData($sumemry);
                $allsummaryData = UtilityReports::allSummeryPerCal($allsummaryData);

                /*put color code into all summary data*/
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "tur");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "turt");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "net_rev");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "t_rev");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "t_sub");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "trat");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "reg");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "unreg");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "purged");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "churn");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "renewal");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "daily_push_success");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "daily_push_failed");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "bill");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "first_push");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "daily_push");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "first_push");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "arpu7");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "usarpu7");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "arpu30");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "usarpu30");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "ltv");
            }

            return view('report.index', compact('sumemry', 'no_of_days', 'companys', 'allsummaryData'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function CountryWiseSummary(Request $request)
    {
        if (\Auth::user()->can('Report Summary')) {
            $CountryWise = 1;

            // get filtre request
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

            $companys = Company::get();

            /* filter Search Section */

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

            if ($request->filled('company') && $req_CompanyId != "allcompany" && !$request->filled('operatorId')) {
                $companies = Company::Find($req_CompanyId);
                $Operators_company = array();

                if (!empty($companies)) {
                    $Operators_company = $companies->company_operators->pluck('operator_id')->toArray();
                    $Operators = Operator::with('revenueshare', 'RevenushareByDate')
                        ->Status(1)
                        ->GetOperatorByOperatorId($Operators_company)
                        ->get();
                }

                $showAllOperator = false;
            }

            if ($request->filled('country') && !$request->filled('operatorId')) {
                $Operators = Operator::with('revenueshare', 'RevenushareByDate')
                    ->Status(1)
                    ->GetOperatorByCountryId($req_CountryId)
                    ->get();

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
                $Operators = Operator::with('revenueshare', 'RevenushareByDate')
                    ->Status(1)
                    ->GetOperatorByOperatorId($filterOperator)
                    ->get();

                $showAllOperator = false;
            }

            if ($showAllOperator) {
                $Operators = Operator::with('revenueshare', 'RevenushareByDate')->Status(1)->get();
            }

            if (!isset($Operators)) {
                $request->session()->flash('alert-success', 'User was successful added!');
                return redirect()->back();
            }

            /* filter End Section */

            $Country = Country::all()->toArray();
            $countries = array();

            if (!empty($Country)) {
                foreach ($Country as $CountryI) {
                    $countries[$CountryI['id']] = $CountryI;
                }
            }

            $arrayOperatorsIds = $Operators->pluck('id_operator')->toArray();

            /* Admin Access All operator and Services */

            $user = Auth::user();

            $user_id = $user->id;
            $user_type = $user->type;
            $allowAllOperator = $user->WhowAccessAlOperator($user_type);

            if ($allowAllOperator) {
                $reports = report_summarize::filteroperator($arrayOperatorsIds)
                    ->filterDateRange($start_date, $end_date)
                    ->orderBy('operator_id')
                    ->get()
                    ->toArray();
            } else // Normal User The operator and Service Set From Admin Panel
            {
                $UserOperatorServices = Session::get('userOperatorService');

                if (empty($UserOperatorServices)) {
                    dd("Please Contact to admin , add Operator to your account");
                }

                $arrayOperatorsIds = $UserOperatorServices['id_operators'];
                $arrayServicesIds = $UserOperatorServices['id_services'];

                $reports = ReportSummeriseUsers::filteroperator($arrayOperatorsIds)
                    ->filterDateRange($start_date, $end_date)
                    ->User($user_id)
                    ->orderBy('operator_id')
                    ->get()
                    ->toArray();
            }

            /* End */

            $firstDay = Carbon::createFromFormat('Y-m-d', $startColumnDateDisplay)->firstOfMonth()->format('Y-m-d');

            $activeSubs = report_summarize::select('total', 'date', 'operator_id')->filteroperator($arrayOperatorsIds)->filterDate($firstDay)->get()->toArray();

            $cost_campaign = ReportsPnlsOperatorSummarizes::select('id_operator as operator_id', 'date', 'cost_campaign')->filteroperator($arrayOperatorsIds)->filterDateRange($start_date, $end_date)->get()->toArray();

            $reportsByIDs = $this->getReportsOperatorID($reports);
            $activesubsdata = $this->getReportsOperatorID($activeSubs);
            $costdata = $this->getReportsOperatorID($cost_campaign);

            $datesIndividual = Utility::getRangeDates($startColumnDateDisplay, $end_date);
            $no_of_days = Utility::getRangeDateNo($datesIndividual);

            if (!empty($Operators)) {
                foreach ($Operators as $operator) {
                    $tmpOperators = array();
                    $tmpOperators['operator'] = $operator;

                    $country_id = $operator->country_id;
                    $contain_id = Arr::exists($countries, $country_id);
                    $OperatorCountry = array();

                    if ($contain_id) {
                        $tmpOperators['country'] = $countries[$country_id];
                        $OperatorCountry = $countries[$country_id];
                    }

                    $reportsColumnData = $this->getReportsDateWise($operator, $no_of_days, $reportsByIDs, $activesubsdata, $costdata, $OperatorCountry);

                    $tmpOperators['month_string'] = $month;
                    $tmpOperators['last_update'] = $reportsColumnData['last_update'];

                    $total_avg_t = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['tur'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['tur']['dates'] = $reportsColumnData['tur'];
                    $tmpOperators['tur']['total'] = $total_avg_t['sum'];
                    $tmpOperators['tur']['t_mo_end'] = $total_avg_t['T_Mo_End'];;
                    $tmpOperators['tur']['avg'] = $total_avg_t['avg'];

                    $tmpOperators['arpu7raw']['dates'] = $reportsColumnData['arpu7Raw'];
                    $tmpOperators['arpu7raw']['total'] = 0;
                    $tmpOperators['arpu7raw']['t_mo_end'] = 0;
                    $tmpOperators['arpu7raw']['avg'] = 0;

                    $tmpOperators['arpu30raw']['dates'] = $reportsColumnData['arpu30Raw'];
                    $tmpOperators['arpu30raw']['total'] = 0;
                    $tmpOperators['arpu30raw']['t_mo_end'] = 0;
                    $tmpOperators['arpu30raw']['avg'] = 0;

                    $total_avg_rev = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['t_rev'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['t_rev']['dates'] = $reportsColumnData['t_rev'];
                    $tmpOperators['t_rev']['total'] = $total_avg_rev['sum'];
                    $tmpOperators['t_rev']['t_mo_end'] = $total_avg_rev['T_Mo_End'];;
                    $tmpOperators['t_rev']['avg'] = $total_avg_rev['avg'];

                    $total_avg_trat = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['trat'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['trat']['dates'] = $reportsColumnData['trat'];
                    $tmpOperators['trat']['total'] = $total_avg_trat['sum'];
                    $tmpOperators['trat']['t_mo_end'] = $total_avg_trat['T_Mo_End'];
                    $tmpOperators['trat']['avg'] = $total_avg_trat['avg'];

                    $total_avg_turt = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['turt'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['turt']['dates'] = $reportsColumnData['turt'];
                    $tmpOperators['turt']['total'] = $total_avg_turt['sum'];
                    $tmpOperators['turt']['t_mo_end'] = $total_avg_turt['T_Mo_End'];;
                    $tmpOperators['turt']['avg'] = $total_avg_turt['avg'];;

                    $total_avg_net_rev = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['net_rev'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['net_rev']['dates'] = $reportsColumnData['net_rev'];
                    $tmpOperators['net_rev']['total'] = $total_avg_net_rev['sum'];
                    $tmpOperators['net_rev']['t_mo_end'] = $total_avg_net_rev['T_Mo_End'];
                    $tmpOperators['net_rev']['avg'] = $total_avg_net_rev['avg'];

                    $total_avg_t_sub = UtilityReports::calculateTotalSubscribe($operator, $reportsColumnData['t_sub'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['t_sub']['dates'] = $reportsColumnData['t_sub'];
                    $tmpOperators['t_sub']['total'] = $total_avg_t_sub['sum'];
                    $tmpOperators['t_sub']['t_mo_end'] = $total_avg_t_sub['T_Mo_End'];
                    $tmpOperators['t_sub']['avg'] = $total_avg_t_sub['avg'];

                    $total_avg_t_reg = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['reg'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['reg']['dates'] = $reportsColumnData['reg'];
                    $tmpOperators['reg']['total'] = $total_avg_t_reg['sum'];
                    $tmpOperators['reg']['t_mo_end'] = $total_avg_t_reg['T_Mo_End'];
                    $tmpOperators['reg']['avg'] = $total_avg_t_reg['avg'];

                    $total_avg_t_unreg = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['unreg'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['unreg']['dates'] = $reportsColumnData['unreg'];
                    $tmpOperators['unreg']['total'] = $total_avg_t_unreg['sum'];
                    $tmpOperators['unreg']['t_mo_end'] = $total_avg_t_unreg['T_Mo_End'];;
                    $tmpOperators['unreg']['avg'] = $total_avg_t_unreg['avg'];

                    $total_avg_t_purged = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['purged'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['purged']['dates'] = $reportsColumnData['purged'];
                    $tmpOperators['purged']['total'] = $total_avg_t_purged['sum'];
                    $tmpOperators['purged']['t_mo_end'] = $total_avg_t_purged['T_Mo_End'];
                    $tmpOperators['purged']['avg'] = $total_avg_t_purged['avg'];

                    $total_avg_t_churn = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['churn'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['churn']['dates'] = $reportsColumnData['churn'];
                    $tmpOperators['churn']['total'] = 0;
                    $tmpOperators['churn']['t_mo_end'] = 0;
                    $tmpOperators['churn']['avg'] = $total_avg_t_churn['avg'];

                    $total_avg_t_renewal = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['renewal'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['renewal']['dates'] = $reportsColumnData['renewal'];
                    $tmpOperators['renewal']['total'] = $total_avg_t_renewal['sum'];
                    $tmpOperators['renewal']['t_mo_end'] = $total_avg_t_renewal['T_Mo_End'];
                    $tmpOperators['renewal']['avg'] = $total_avg_t_renewal['avg'];

                    $total_avg_mt_success = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['daily_push_success'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['daily_push_success']['dates'] = $reportsColumnData['daily_push_success'];
                    $tmpOperators['daily_push_success']['total'] = $total_avg_mt_success['sum'];
                    $tmpOperators['daily_push_success']['t_mo_end'] = $total_avg_mt_success['T_Mo_End'];
                    $tmpOperators['daily_push_success']['avg'] = $total_avg_mt_success['avg'];

                    $total_avg_mt_failed = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['daily_push_failed'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['daily_push_failed']['dates'] = $reportsColumnData['daily_push_failed'];
                    $tmpOperators['daily_push_failed']['total'] = $total_avg_mt_failed['sum'];
                    $tmpOperators['daily_push_failed']['t_mo_end'] = $total_avg_mt_failed['T_Mo_End'];
                    $tmpOperators['daily_push_failed']['avg'] = $total_avg_mt_failed['avg'];

                    $total_avg_t_bill = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['bill'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['bill']['dates'] = $reportsColumnData['bill'];
                    $tmpOperators['bill']['total'] = 0;
                    $tmpOperators['bill']['t_mo_end'] = 0;
                    $tmpOperators['bill']['avg'] = $total_avg_t_bill['avg'];

                    $total_avg_t_first_push = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['first_push'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['first_push']['dates'] = $reportsColumnData['first_push'];
                    $tmpOperators['first_push']['total'] = 0;
                    $tmpOperators['first_push']['t_mo_end'] = 0;
                    $tmpOperators['first_push']['avg'] = $total_avg_t_first_push['avg'];

                    $total_avg_t_daily_push = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['daily_push'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['daily_push']['dates'] = $reportsColumnData['daily_push'];
                    $tmpOperators['daily_push']['total'] = 0;
                    $tmpOperators['daily_push']['t_mo_end'] = 0;
                    $tmpOperators['daily_push']['avg'] = $total_avg_t_daily_push['avg'];

                    $total_avg_t_arpu7 = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['arpu7'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['arpu7']['dates'] = $reportsColumnData['arpu7'];
                    $tmpOperators['arpu7']['total'] = 0;
                    $tmpOperators['arpu7']['t_mo_end'] = 0;
                    $tmpOperators['arpu7']['avg'] = $total_avg_t_arpu7['avg'];

                    $total_avg_t_usarpu7 = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['usarpu7'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['usarpu7']['dates'] = $reportsColumnData['usarpu7'];
                    $tmpOperators['usarpu7']['total'] = 0;
                    $tmpOperators['usarpu7']['t_mo_end'] = 0;
                    $tmpOperators['usarpu7']['avg'] = $total_avg_t_usarpu7['avg'];

                    $total_avg_t_arpu30 = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['arpu30'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['arpu30']['dates'] = $reportsColumnData['arpu30'];
                    $tmpOperators['arpu30']['total'] = 0;
                    $tmpOperators['arpu30']['t_mo_end'] = 0;
                    $tmpOperators['arpu30']['avg'] = $total_avg_t_arpu30['avg'];

                    $total_avg_t_usarpu30 = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['usarpu30'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['usarpu30']['dates'] = $reportsColumnData['usarpu30'];
                    $tmpOperators['usarpu30']['total'] = 0;
                    $tmpOperators['usarpu30']['t_mo_end'] = 0;
                    $tmpOperators['usarpu30']['avg'] =  $total_avg_t_usarpu30['avg'];

                    $total_avg_first_day_active = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['first_day_active'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['first_day_active']['dates'] = $reportsColumnData['first_day_active'];
                    $tmpOperators['first_day_active']['total'] = $total_avg_first_day_active['sum'];
                    $tmpOperators['first_day_active']['t_mo_end'] = $total_avg_first_day_active['T_Mo_End'];
                    $tmpOperators['first_day_active']['avg'] = $total_avg_first_day_active['avg'];

                    $total_avg_cost_campaign = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['cost_campaign'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['cost_campaign']['dates'] = $reportsColumnData['cost_campaign'];
                    $tmpOperators['cost_campaign']['total'] = $total_avg_cost_campaign['sum'];
                    $tmpOperators['cost_campaign']['t_mo_end'] = $total_avg_cost_campaign['T_Mo_End'];
                    $tmpOperators['cost_campaign']['avg'] = $total_avg_cost_campaign['avg'];

                    $total_avg_ltv = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['ltv'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['ltv']['dates'] = $reportsColumnData['ltv'];
                    $tmpOperators['ltv']['total'] = $total_avg_ltv['sum'];
                    $tmpOperators['ltv']['t_mo_end'] = $total_avg_ltv['T_Mo_End'];
                    $tmpOperators['ltv']['avg'] = $total_avg_ltv['avg'];

                    $tmpOperators['mt_success']['dates'] = $reportsColumnData['mt_success'];
                    $tmpOperators['mt_failed']['dates'] = $reportsColumnData['mt_failed'];

                    $tmpOperators['fmt_success']['dates'] = $reportsColumnData['fmt_success'];
                    $tmpOperators['fmt_failed']['dates'] = $reportsColumnData['fmt_failed'];

                    $sumemry[] = $tmpOperators;
                }
            }

            // Country Sum from Operator array

            $displayCountries = array();
            $SelectedCountries = array();

            if (!empty($sumemry)) {
                foreach ($sumemry as $key => $sumemries) {

                    $country_id = $sumemries['country']['id'];

                    $SelectedCountries[$country_id] = $sumemries['country'];

                    $displayCountries[$country_id][] = $sumemries;
                }
            }

            $RowCountryData = array();

            if (!empty($SelectedCountries)) {
                foreach ($SelectedCountries as $key => $SelectedCountry) {
                    $tempDataArr = array();

                    $country_id = $SelectedCountry['id'];

                    $dataRowSum = UtilityReports::CountrySumOperator($displayCountries[$country_id]);

                    $dataRowSum = UtilityReports::PercentageCountryWise($dataRowSum, $country_id);

                    $tempDataArr['country'] = $SelectedCountry;
                    $tempDataArr['month_string'] = $month;

                    $tempDataArr = array_merge($tempDataArr, $dataRowSum);

                    $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "tur");
                    $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "turt");
                    $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "net_rev");
                    $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "t_rev");
                    $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "t_sub");
                    $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "trat");
                    $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "reg");
                    $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "unreg");
                    $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "purged");
                    $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "churn");
                    $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "renewal");
                    $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "daily_push_success");
                    $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "daily_push_failed");
                    $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "bill");
                    $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "first_push");
                    $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "daily_push");
                    $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "arpu7");
                    $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "usarpu7");
                    $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "arpu30");
                    $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "usarpu30");
                    $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "ltv");

                    $RowCountryData[] = $tempDataArr;
                }
            }

            $allsummaryData = UtilityReports::allsummaryData($sumemry);
            $allsummaryData = UtilityReports::allSummeryPerCal($allsummaryData);

            /*put color code into all summary data*/
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "tur");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "turt");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "net_rev");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "t_rev");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "t_sub");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "trat");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "reg");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "unreg");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "purged");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "churn");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "renewal");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "daily_push_success");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "daily_push_failed");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "bill");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "first_push");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "daily_push");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "first_push");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "arpu7");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "usarpu7");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "arpu30");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "usarpu30");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "ltv");

            return view('report.daily_country_reportsummery', compact('RowCountryData', 'no_of_days', 'companys', 'allsummaryData', 'CountryWise'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function BusinessWiseSummary(Request $request)
    {
        if (\Auth::user()->can('Report Summary')) {
            $BusinessTypeWise = 1;
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

            if ($request->filled('company') && $req_CompanyId != "allcompany" && !$request->filled('operatorId')) {
                $companies = Company::Find($req_CompanyId);
                $Operators_company = array();

                if (!empty($companies)) {
                    $Operators_company = $companies->company_operators->pluck('operator_id')->toArray();
                    $Operators = Operator::with('revenueshare', 'RevenushareByDate')
                        ->Status(1)
                        ->GetOperatorByOperatorId($Operators_company)
                        ->get();
                }

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
                $Operators = Operator::with('revenueshare', 'RevenushareByDate')
                    ->Status(1)
                    ->GetOperatorByOperatorId($filterOperator)
                    ->get();

                $showAllOperator = false;
            }

            if ($showAllOperator) {
                $Operators = Operator::with('revenueshare', 'RevenushareByDate')->Status(1)->get();
            }

            if (!isset($Operators)) {
                $request->session()->flash('alert-success', 'User was successful added!');
                return redirect()->back();
            }

            /* filter End Section */

            /* All country retrive becuase Avoid nested query for each operator country */

            $Country = Country::all()->toArray();
            $countries = array();

            if (!empty($Country)) {
                foreach ($Country as $CountryI) {
                    $countries[$CountryI['id']] = $CountryI;
                }
            }

            /* End All country retrive becuase Avoid nested query for each operator country */

            $arrayOperatorsIds = $Operators->pluck('id_operator')->toArray();

            $sumemry = array();

            /* Admin Access All operator and Services */

            $user = Auth::user();

            $user_id = $user->id;
            $user_type = $user->type;
            $allowAllOperator = $user->WhowAccessAlOperator($user_type);

            if ($allowAllOperator) {
                $reports = report_summarize::filteroperator($arrayOperatorsIds)
                    ->filterDateRange($start_date, $end_date)
                    ->orderBy('operator_id')
                    ->get()
                    ->toArray();
            } else // Normal User The operator and Service Set From Admin Panel
            {
                $UserOperatorServices = Session::get('userOperatorService');

                if (empty($UserOperatorServices)) {
                    dd("Please Contact to admin , add Operator to your account");
                }

                $arrayOperatorsIds = $UserOperatorServices['id_operators'];
                $arrayServicesIds = $UserOperatorServices['id_services'];

                $reports = ReportSummeriseUsers::filteroperator($arrayOperatorsIds)
                    ->filterDateRange($start_date, $end_date)
                    ->User($user_id)
                    ->orderBy('operator_id')
                    ->get()
                    ->toArray();
            }

            $firstDay = Carbon::createFromFormat('Y-m-d', $startColumnDateDisplay)->firstOfMonth()->format('Y-m-d');

            $activeSubs = report_summarize::select('total', 'date', 'operator_id')->filteroperator($arrayOperatorsIds)->filterDate($firstDay)->get()->toArray();

            $cost_campaign = ReportsPnlsOperatorSummarizes::select('id_operator as operator_id', 'date', 'cost_campaign')->filteroperator($arrayOperatorsIds)->filterDateRange($start_date, $end_date)->get()->toArray();

            $reportsByIDs = $this->getReportsOperatorID($reports);
            $activesubsdata = $this->getReportsOperatorID($activeSubs);
            $costdata = $this->getReportsOperatorID($cost_campaign);

            $datesIndividual = Utility::getRangeDates($startColumnDateDisplay, $end_date);
            $no_of_days = Utility::getRangeDateNo($datesIndividual);

            if (!empty($Operators)) {
                foreach ($Operators as $operator) {
                    $tmpOperators = array();
                    $activesubsdata = array();
                    $costdata = array();
                    $tmpOperators['operator'] = $operator;
                    $operator_id = $operator->id_operator;
                    $country_id = $operator->country_id;
                    $contain_id = Arr::exists($countries, $country_id);
                    $OperatorCountry = array();

                    if ($contain_id) {
                        $tmpOperators['country'] = $countries[$country_id];
                        $OperatorCountry = $countries[$country_id];
                    }
                    $type = $operator->business_type;

                    // if (!isset($type))
                    //     continue;
                    if($type == NULL)
                    {
                        $type = 'unknown';
                    }

                    $reportsColumnData = $this->getReportsDateWise($operator, $no_of_days, $reportsByIDs, $activesubsdata, $costdata, $OperatorCountry);

                    $tmpOperators['account_manager'] = $type;
                    $tmpOperators['month_string'] = $month;
                    $tmpOperators['last_update'] = $reportsColumnData['last_update'];

                    $total_avg_t = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['tur'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['tur']['dates'] = $reportsColumnData['tur'];
                    $tmpOperators['tur']['total'] = $total_avg_t['sum'];
                    $tmpOperators['tur']['t_mo_end'] = $total_avg_t['T_Mo_End'];;
                    $tmpOperators['tur']['avg'] = $total_avg_t['avg'];

                    $tmpOperators['arpu7raw']['dates'] = $reportsColumnData['arpu7Raw'];
                    $tmpOperators['arpu7raw']['total'] = 0;
                    $tmpOperators['arpu7raw']['t_mo_end'] = 0;
                    $tmpOperators['arpu7raw']['avg'] = 0;

                    $tmpOperators['arpu30raw']['dates'] = $reportsColumnData['arpu30Raw'];
                    $tmpOperators['arpu30raw']['total'] = 0;
                    $tmpOperators['arpu30raw']['t_mo_end'] = 0;
                    $tmpOperators['arpu30raw']['avg'] = 0;

                    $total_avg_rev = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['t_rev'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['t_rev']['dates'] = $reportsColumnData['t_rev'];
                    $tmpOperators['t_rev']['total'] = $total_avg_rev['sum'];
                    $tmpOperators['t_rev']['t_mo_end'] = $total_avg_rev['T_Mo_End'];;
                    $tmpOperators['t_rev']['avg'] = $total_avg_rev['avg'];

                    $total_avg_trat = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['trat'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['trat']['dates'] = $reportsColumnData['trat'];
                    $tmpOperators['trat']['total'] = $total_avg_trat['sum'];
                    $tmpOperators['trat']['t_mo_end'] = $total_avg_trat['T_Mo_End'];
                    $tmpOperators['trat']['avg'] = $total_avg_trat['avg'];

                    $total_avg_turt = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['turt'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['turt']['dates'] = $reportsColumnData['turt'];
                    $tmpOperators['turt']['total'] = $total_avg_turt['sum'];
                    $tmpOperators['turt']['t_mo_end'] = $total_avg_turt['T_Mo_End'];;
                    $tmpOperators['turt']['avg'] = $total_avg_turt['avg'];;

                    $total_avg_net_rev = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['net_rev'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['net_rev']['dates'] = $reportsColumnData['net_rev'];
                    $tmpOperators['net_rev']['total'] = $total_avg_net_rev['sum'];
                    $tmpOperators['net_rev']['t_mo_end'] = $total_avg_net_rev['T_Mo_End'];
                    $tmpOperators['net_rev']['avg'] = $total_avg_net_rev['avg'];

                    $total_avg_t_sub = UtilityReports::calculateTotalSubscribe($operator, $reportsColumnData['t_sub'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['t_sub']['dates'] = $reportsColumnData['t_sub'];
                    $tmpOperators['t_sub']['total'] = $total_avg_t_sub['sum'];
                    $tmpOperators['t_sub']['t_mo_end'] = $total_avg_t_sub['T_Mo_End'];
                    $tmpOperators['t_sub']['avg'] = $total_avg_t_sub['avg'];

                    $total_avg_t_reg = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['reg'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['reg']['dates'] = $reportsColumnData['reg'];
                    $tmpOperators['reg']['total'] = $total_avg_t_reg['sum'];
                    $tmpOperators['reg']['t_mo_end'] = $total_avg_t_reg['T_Mo_End'];
                    $tmpOperators['reg']['avg'] = $total_avg_t_reg['avg'];

                    $total_avg_t_unreg = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['unreg'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['unreg']['dates'] = $reportsColumnData['unreg'];
                    $tmpOperators['unreg']['total'] = $total_avg_t_unreg['sum'];
                    $tmpOperators['unreg']['t_mo_end'] = $total_avg_t_unreg['T_Mo_End'];;
                    $tmpOperators['unreg']['avg'] = $total_avg_t_unreg['avg'];

                    $total_avg_t_purged = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['purged'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['purged']['dates'] = $reportsColumnData['purged'];
                    $tmpOperators['purged']['total'] = $total_avg_t_purged['sum'];
                    $tmpOperators['purged']['t_mo_end'] = $total_avg_t_purged['T_Mo_End'];
                    $tmpOperators['purged']['avg'] = $total_avg_t_purged['avg'];

                    $total_avg_t_churn = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['churn'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['churn']['dates'] = $reportsColumnData['churn'];
                    $tmpOperators['churn']['total'] = 0;
                    $tmpOperators['churn']['t_mo_end'] = 0;
                    $tmpOperators['churn']['avg'] = $total_avg_t_churn['avg'];

                    $total_avg_t_renewal = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['renewal'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['renewal']['dates'] = $reportsColumnData['renewal'];
                    $tmpOperators['renewal']['total'] = $total_avg_t_renewal['sum'];
                    $tmpOperators['renewal']['t_mo_end'] = $total_avg_t_renewal['T_Mo_End'];
                    $tmpOperators['renewal']['avg'] = $total_avg_t_renewal['avg'];

                    $total_avg_mt_success = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['daily_push_success'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['daily_push_success']['dates'] = $reportsColumnData['daily_push_success'];
                    $tmpOperators['daily_push_success']['total'] = $total_avg_mt_success['sum'];
                    $tmpOperators['daily_push_success']['t_mo_end'] = $total_avg_mt_success['T_Mo_End'];
                    $tmpOperators['daily_push_success']['avg'] = $total_avg_mt_success['avg'];

                    $total_avg_mt_failed = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['daily_push_failed'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['daily_push_failed']['dates'] = $reportsColumnData['daily_push_failed'];
                    $tmpOperators['daily_push_failed']['total'] = $total_avg_mt_failed['sum'];
                    $tmpOperators['daily_push_failed']['t_mo_end'] = $total_avg_mt_failed['T_Mo_End'];
                    $tmpOperators['daily_push_failed']['avg'] = $total_avg_mt_failed['avg'];

                    $total_avg_t_bill = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['bill'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['bill']['dates'] = $reportsColumnData['bill'];
                    $tmpOperators['bill']['total'] = 0;
                    $tmpOperators['bill']['t_mo_end'] = 0;
                    $tmpOperators['bill']['avg'] = $total_avg_t_bill['avg'];

                    $total_avg_t_first_push = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['first_push'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['first_push']['dates'] = $reportsColumnData['first_push'];
                    $tmpOperators['first_push']['total'] = 0;
                    $tmpOperators['first_push']['t_mo_end'] = 0;
                    $tmpOperators['first_push']['avg'] = $total_avg_t_first_push['avg'];

                    $total_avg_t_daily_push = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['daily_push'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['daily_push']['dates'] = $reportsColumnData['daily_push'];
                    $tmpOperators['daily_push']['total'] = 0;
                    $tmpOperators['daily_push']['t_mo_end'] = 0;
                    $tmpOperators['daily_push']['avg'] = $total_avg_t_daily_push['avg'];

                    $total_avg_t_arpu7 = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['arpu7'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['arpu7']['dates'] = $reportsColumnData['arpu7'];
                    $tmpOperators['arpu7']['total'] = 0;
                    $tmpOperators['arpu7']['t_mo_end'] = 0;
                    $tmpOperators['arpu7']['avg'] = $total_avg_t_arpu7['avg'];

                    $total_avg_t_usarpu7 = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['usarpu7'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['usarpu7']['dates'] = $reportsColumnData['usarpu7'];
                    $tmpOperators['usarpu7']['total'] = 0;
                    $tmpOperators['usarpu7']['t_mo_end'] = 0;
                    $tmpOperators['usarpu7']['avg'] = $total_avg_t_usarpu7['avg'];

                    $total_avg_t_arpu30 = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['arpu30'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['arpu30']['dates'] = $reportsColumnData['arpu30'];
                    $tmpOperators['arpu30']['total'] = 0;
                    $tmpOperators['arpu30']['t_mo_end'] = 0;
                    $tmpOperators['arpu30']['avg'] = $total_avg_t_arpu30['avg'];

                    $total_avg_t_usarpu30 = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['usarpu30'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['usarpu30']['dates'] = $reportsColumnData['usarpu30'];
                    $tmpOperators['usarpu30']['total'] = 0;
                    $tmpOperators['usarpu30']['t_mo_end'] = 0;
                    $tmpOperators['usarpu30']['avg'] =  $total_avg_t_usarpu30['avg'];

                    $total_avg_first_day_active = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['first_day_active'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['first_day_active']['dates'] = $reportsColumnData['first_day_active'];
                    $tmpOperators['first_day_active']['total'] = $total_avg_first_day_active['sum'];
                    $tmpOperators['first_day_active']['t_mo_end'] = $total_avg_first_day_active['T_Mo_End'];
                    $tmpOperators['first_day_active']['avg'] = $total_avg_first_day_active['avg'];

                    $total_avg_cost_campaign = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['cost_campaign'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['cost_campaign']['dates'] = $reportsColumnData['cost_campaign'];
                    $tmpOperators['cost_campaign']['total'] = $total_avg_cost_campaign['sum'];
                    $tmpOperators['cost_campaign']['t_mo_end'] = $total_avg_cost_campaign['T_Mo_End'];
                    $tmpOperators['cost_campaign']['avg'] = $total_avg_cost_campaign['avg'];

                    $total_avg_ltv = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['ltv'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['ltv']['dates'] = $reportsColumnData['ltv'];
                    $tmpOperators['ltv']['total'] = $total_avg_ltv['sum'];
                    $tmpOperators['ltv']['t_mo_end'] = $total_avg_ltv['T_Mo_End'];
                    $tmpOperators['ltv']['avg'] = $total_avg_ltv['avg'];

                    $tmpOperators['mt_success']['dates'] = $reportsColumnData['mt_success'];
                    $tmpOperators['mt_failed']['dates'] = $reportsColumnData['mt_failed'];

                    $tmpOperators['fmt_success']['dates'] = $reportsColumnData['fmt_success'];
                    $tmpOperators['fmt_failed']['dates'] = $reportsColumnData['fmt_failed'];

                    $sumemry[$type][] = $tmpOperators;
                }
            }
            // Business Type Sum from Operator array

            $displayBusinessType = array();
            $SelectedBusinessType = array();

            $RowUserData = array();

            if (!empty($sumemry)) {
                foreach ($sumemry as $key =>  $sumemries) {
                    $tempDataArr = array();
                    // $manager_id = $key;
                    $managerName = "NA";

                    $managerName = $key;

                    $dataRowSum = UtilityReports::CountrySumOperator($sumemries);
                    $dataRowSum = UtilityReports::PercentageCountryWise($dataRowSum);

                    $tempDataArr['account_manager']['name'] = $managerName;
                    $tempDataArr['month_string'] = $month;

                    $tempDataArr = array_merge($tempDataArr, $dataRowSum);

                    $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "tur");
                    $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "turt");
                    $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "net_rev");
                    $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "t_rev");
                    $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "t_sub");
                    $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "trat");
                    $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "reg");
                    $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "unreg");
                    $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "purged");
                    $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "churn");
                    $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "renewal");
                    $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "daily_push_success");
                    $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "daily_push_failed");
                    $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "bill");
                    $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "first_push");
                    $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "daily_push");
                    $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "arpu7");
                    $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "usarpu7");
                    $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "arpu30");
                    $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "usarpu30");

                    $RowUserData[] = $tempDataArr;
                }
            }

            $sumemry = $RowUserData;

            if (!empty($sumemry)) {
                $allsummaryData = UtilityReports::allsummaryData($sumemry);
                $allsummaryData = UtilityReports::allSummeryPerCal($allsummaryData);

                /*put color code into all summary data*/
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "tur");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "turt");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "net_rev");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "t_rev");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "t_sub");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "trat");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "reg");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "unreg");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "purged");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "churn");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "renewal");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "daily_push_success");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "daily_push_failed");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "bill");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "first_push");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "daily_push");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "first_push");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "arpu7");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "usarpu7");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "arpu30");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "usarpu30");
            } else {
                $allsummaryData = [];
            }

            return view('report.daily_manager_report', compact('BusinessTypeWise', 'companys', 'sumemry', 'no_of_days', 'allsummaryData'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }



    

    // get country
    public function country(Request $request)
    {
        if ($request->id == 'allcompany') {
            $countrys = Operator::select('country_name', 'country_id')
                ->Status(1)
                ->orderBy('country_name', 'ASC')
                ->distinct()
                ->get()
                ->toArray();

            $operator = Operator::orderBy('operator_name', 'ASC')->get()->toArray();

            $data = ['countrys' => $countrys, 'operators' => $operator];

            return $data;
        }

        $countrys = [];
        $country_ids = [];
        $country_operator = [];
        $operators = CompanyOperators::GetOperator($request->id)->get();

        foreach ($operators as $key => $operator) {
            $country = $operator->Operator;

            if (!empty($country) && isset($country[0])) {
                if (!in_array($country[0]->country_id, $country_ids)) {
                    array_push($countrys, $country[0]);
                }

                array_push($country_ids, $country[0]->country_id);
                array_push($country_operator, $country[0]);
            }
        }

        $data = ['countrys' => $countrys, 'operators' => $country_operator];

        return $data;
    }

    // get operator
    public function operator(Request $request)
    {
        if ($request->company == 'allcompany') {
            $operators = Operator::GetByCountryIds([$request->id])->get();

            return $operators;
        }

        $operators = Operator::where('operators.country_id', '=', $request->id);

        $operators = $operators->get(['operators.*']);

        return $operators;
    }

    // get country
    public function userFilterCountry(Request $request)
    {
        if ($request->id == 'allcompany') {
            $countrys = Operator::select('country_name', 'country_id')
                ->Status(1)
                ->orderBy('country_name', 'ASC')
                ->distinct()
                ->get()
                ->toArray();

            $operator = Operator::orderBy('operator_name', 'ASC')->get()->toArray();

            foreach ($operator as $key => $value) {
                $operator[$key]['operator_name'] = !empty($value['display_name']) ? $value['display_name'] : $value['operator_name'];
            }

            $data = ['countrys' => $countrys, 'operators' => $operator];

            return $data;
        }

        $countrys = [];
        $country_ids = [];
        $country_operator = [];
        $operators = CompanyOperators::GetOperator($request->id)->get();

        foreach ($operators as $key => $operator) {
            $country = $operator->Operator;

            if (!empty($country) && isset($country[0])) {
                if (!in_array($country[0]->country_id, $country_ids)) {
                    array_push($countrys, $country[0]);
                }

                array_push($country_ids, $country[0]->country_id);
                array_push($country_operator, $country[0]);
            }
        }

        foreach ($country_operator as $key => $value) {
            $country_operator[$key]['operator_name'] = !empty($value['display_name']) ? $value['display_name'] : $value['operator_name'];
        }

        $data = ['countrys' => $countrys, 'operators' => $country_operator];

        return $data;
    }

    // get operator
    public function userFilterOperator(Request $request)
    {
        if ($request->company == 'allcompany') {
            $operators = Operator::Status(1)->GetByCountryIds([$request->id])->get();

            foreach ($operators as $key => $value) {
                $operators[$key]['operator_name'] = !empty($value['display_name']) ? $value['display_name'] : $value['operator_name'];
            }

            return $operators;
        }

        $operators = Operator::Status(1)
            ->join('company_operators', 'company_operators.operator_id', '=', 'operators.id_operator')
            ->join('companies', 'companies.id', '=', 'company_operators.company_id')
            ->where('operators.country_id', '=', $request->id);
        if ($request->filled('company'))
            $operators = $operators->where('company_operators.company_id', '=', $request->company);

        $operators = $operators->get(['operators.*', 'company_operators.company_id']);


        foreach ($operators as $key => $value) {
            $operators[$key]['operator_name'] = !empty($value['display_name']) ? $value['display_name'] : $value['operator_name'];
        }

        return $operators;
    }

    // get business type operator
    public function userFilterBusinessTypeOperator(Request $request)
    {
        if ($request->company == 'allcompany' && $request->country != '') {
            $operators = Operator::Status(1)->where(['country_id' => $request->country, 'business_type' => $request->business_type])->get();

            foreach ($operators as $key => $value) {
                $operators[$key]['operator_name'] = !empty($value['display_name']) ? $value['display_name'] : $value['operator_name'];
            }

            return $operators;
        }

        if ($request->company == 'allcompany' && $request->country == '') {
            $operators = Operator::Status(1)->where(['business_type' => $request->business_type])->get();

            foreach ($operators as $key => $value) {
                $operators[$key]['operator_name'] = !empty($value['display_name']) ? $value['display_name'] : $value['operator_name'];
            }

            return $operators;
        }

        $operators = Operator::Status(1)
            ->join('company_operators', 'company_operators.operator_id', '=', 'operators.id_operator')
            ->join('companies', 'companies.id', '=', 'company_operators.company_id')
            ->where('operators.business_type', '=', $request->business_type);

        if ($request->filled('company'))
            $operators = $operators->where('company_operators.company_id', '=', $request->company);

        if ($request->filled('country'))
            $operators = $operators->where('operators.country_id', '=', $request->country);

        $operators = $operators->get(['operators.*', 'company_operators.company_id']);

        foreach ($operators as $key => $value) {
            $operators[$key]['operator_name'] = !empty($value['display_name']) ? $value['display_name'] : $value['operator_name'];
        }

        return $operators;
    }

    // get service
    public function service(Request $request)
    {
        $services = Service::join('operators', 'operators.id_operator', '=', 'services.operator_id');

        if ($request->filled('country'))
            $services = $services->where('operators.country_id', '=', $request->country);

        if ($request->filled('id'))
            $services = $services->where('services.operator_id', '=', $request->id);

        $services = $services->get(['services.*']);

        return $services;
    }

    // get manager daily report
    public function DailyReportManager(Request $request)
    {
        $AccountManagerWise = 1;
        $sumemry = [];
        // get filtre request
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
        $companys = Company::get();

        /* filter Search Section */

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

        if ($request->filled('company') && $req_CompanyId != "allcompany" && !$request->filled('operatorId')) {
            $companies = Company::Find($req_CompanyId);
            $Operators_company = array();

            if (!empty($companies)) {
                $Operators_company = $companies->company_operators->pluck('operator_id')->toArray();
                $Operators = Operator::with('revenueshare', 'RevenushareByDate')->Status(1)->GetOperatorByOperatorId($Operators_company)->get();
            }

            $showAllOperator = false;
        }

        if ($request->filled('country') && !$request->filled('operatorId')) {
            $Operators = Operator::with('revenueshare', 'RevenushareByDate')->Status(1)->GetOperatorByCountryId($req_CountryId)->get();
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
            $Operators = Operator::with('revenueshare', 'RevenushareByDate')->Status(1)->GetOperatorByOperatorId($filterOperator)->get();
            $showAllOperator = false;
        }

        if ($showAllOperator) {
            $Operators = Operator::with('revenueshare', 'RevenushareByDate')->Status(1)->get();
        }

        if (!isset($Operators)) {
            $request->session()->flash('alert-success', 'User was successful added!');
            return redirect()->back();
        }

        /* filter End Section */

        $Country = Country::all()->toArray();
        $countries = array();

        if (!empty($Country)) {
            foreach ($Country as $CountryI) {
                $countries[$CountryI['id']] = $CountryI;
            }
        }

        $Users = User::Type("Account Manager")->get();

        $allUserIds = $Users->pluck('id')->toArray();

        if (empty($allUserIds)) {
            dd("no account Manager in your system");
        }

        $reports = ReportSummeriseUsers::filterDateRange($start_date, $end_date)
            ->UserIn($allUserIds)
            ->orderBy('user_id')
            ->get()
            ->toArray();

        $reportsByIDs = UtilityAccountManager::getReportsOperatorID($reports);

        $datesIndividual = Utility::getRangeDates($startColumnDateDisplay, $end_date);
        $no_of_days = Utility::getRangeDateNo($datesIndividual);

        if (!empty($Users)) {
            foreach ($Users as $user) {
                $userId = $user->id;

                $OperatorsIds = UsersOperatorsServices::GetOperaterServiceByUserId($user->id)->get()->unique("id_operator");

                $ids_operator = $OperatorsIds->pluck("id_operator");

                $Operators = Operator::with('revenueshare', 'RevenushareByDate')->Status(1)->GetOperatorByOperatorId($ids_operator)->get();

                $Nameofuser = $user->name;

                if (!empty($Operators)) {
                    foreach ($Operators as $operator) {
                        $tmpOperators = array();
                        $activesubsdata = array();
                        $costdata = array();
                        $tmpOperators['operator'] = $operator;
                        $operator_id = $operator->id_operator;
                        $country_id = $operator->country_id;
                        $contain_id = Arr::exists($countries, $country_id);
                        $OperatorCountry = array();

                        if ($contain_id) {
                            $tmpOperators['country'] = $countries[$country_id];
                            $OperatorCountry = $countries[$country_id];
                        }

                        if (!isset($reportsByIDs[$userId]))
                            continue;

                        $reportsColumnData = $this->getReportsDateWise($operator, $no_of_days, $reportsByIDs[$userId], $activesubsdata, $costdata, $OperatorCountry);

                        $tmpOperators['account_manager'] = $Nameofuser;
                        $tmpOperators['month_string'] = $month;
                        $tmpOperators['last_update'] = $reportsColumnData['last_update'];

                        $total_avg_t = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['tur'], $startColumnDateDisplay, $end_date);
                        $tmpOperators['tur']['dates'] = $reportsColumnData['tur'];
                        $tmpOperators['tur']['total'] = $total_avg_t['sum'];
                        $tmpOperators['tur']['t_mo_end'] = $total_avg_t['T_Mo_End'];;
                        $tmpOperators['tur']['avg'] = $total_avg_t['avg'];

                        $tmpOperators['arpu7raw']['dates'] = $reportsColumnData['arpu7Raw'];
                        $tmpOperators['arpu7raw']['total'] = 0;
                        $tmpOperators['arpu7raw']['t_mo_end'] = 0;
                        $tmpOperators['arpu7raw']['avg'] = 0;

                        $tmpOperators['arpu30raw']['dates'] = $reportsColumnData['arpu30Raw'];
                        $tmpOperators['arpu30raw']['total'] = 0;
                        $tmpOperators['arpu30raw']['t_mo_end'] = 0;
                        $tmpOperators['arpu30raw']['avg'] = 0;

                        $total_avg_rev = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['t_rev'], $startColumnDateDisplay, $end_date);
                        $tmpOperators['t_rev']['dates'] = $reportsColumnData['t_rev'];
                        $tmpOperators['t_rev']['total'] = $total_avg_rev['sum'];
                        $tmpOperators['t_rev']['t_mo_end'] = $total_avg_rev['T_Mo_End'];;
                        $tmpOperators['t_rev']['avg'] = $total_avg_rev['avg'];

                        $total_avg_trat = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['trat'], $startColumnDateDisplay, $end_date);
                        $tmpOperators['trat']['dates'] = $reportsColumnData['trat'];
                        $tmpOperators['trat']['total'] = $total_avg_trat['sum'];
                        $tmpOperators['trat']['t_mo_end'] = $total_avg_trat['T_Mo_End'];
                        $tmpOperators['trat']['avg'] = $total_avg_trat['avg'];

                        $total_avg_turt = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['turt'], $startColumnDateDisplay, $end_date);
                        $tmpOperators['turt']['dates'] = $reportsColumnData['turt'];
                        $tmpOperators['turt']['total'] = $total_avg_turt['sum'];
                        $tmpOperators['turt']['t_mo_end'] = $total_avg_turt['T_Mo_End'];;
                        $tmpOperators['turt']['avg'] = $total_avg_turt['avg'];;

                        $total_avg_net_rev = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['net_rev'], $startColumnDateDisplay, $end_date);
                        $tmpOperators['net_rev']['dates'] = $reportsColumnData['net_rev'];
                        $tmpOperators['net_rev']['total'] = $total_avg_net_rev['sum'];
                        $tmpOperators['net_rev']['t_mo_end'] = $total_avg_net_rev['T_Mo_End'];
                        $tmpOperators['net_rev']['avg'] = $total_avg_net_rev['avg'];

                        $total_avg_t_sub = UtilityReports::calculateTotalSubscribe($operator, $reportsColumnData['t_sub'], $startColumnDateDisplay, $end_date);
                        $tmpOperators['t_sub']['dates'] = $reportsColumnData['t_sub'];
                        $tmpOperators['t_sub']['total'] = $total_avg_t_sub['sum'];
                        $tmpOperators['t_sub']['t_mo_end'] = $total_avg_t_sub['T_Mo_End'];
                        $tmpOperators['t_sub']['avg'] = $total_avg_t_sub['avg'];

                        $total_avg_t_reg = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['reg'], $startColumnDateDisplay, $end_date);
                        $tmpOperators['reg']['dates'] = $reportsColumnData['reg'];
                        $tmpOperators['reg']['total'] = $total_avg_t_reg['sum'];
                        $tmpOperators['reg']['t_mo_end'] = $total_avg_t_reg['T_Mo_End'];
                        $tmpOperators['reg']['avg'] = $total_avg_t_reg['avg'];

                        $total_avg_t_unreg = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['unreg'], $startColumnDateDisplay, $end_date);
                        $tmpOperators['unreg']['dates'] = $reportsColumnData['unreg'];
                        $tmpOperators['unreg']['total'] = $total_avg_t_unreg['sum'];
                        $tmpOperators['unreg']['t_mo_end'] = $total_avg_t_unreg['T_Mo_End'];;
                        $tmpOperators['unreg']['avg'] = $total_avg_t_unreg['avg'];

                        $total_avg_t_purged = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['purged'], $startColumnDateDisplay, $end_date);
                        $tmpOperators['purged']['dates'] = $reportsColumnData['purged'];
                        $tmpOperators['purged']['total'] = $total_avg_t_purged['sum'];
                        $tmpOperators['purged']['t_mo_end'] = $total_avg_t_purged['T_Mo_End'];
                        $tmpOperators['purged']['avg'] = $total_avg_t_purged['avg'];

                        $total_avg_t_churn = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['churn'], $startColumnDateDisplay, $end_date);
                        $tmpOperators['churn']['dates'] = $reportsColumnData['churn'];
                        $tmpOperators['churn']['total'] = 0;
                        $tmpOperators['churn']['t_mo_end'] = 0;
                        $tmpOperators['churn']['avg'] = $total_avg_t_churn['avg'];

                        $total_avg_t_renewal = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['renewal'], $startColumnDateDisplay, $end_date);
                        $tmpOperators['renewal']['dates'] = $reportsColumnData['renewal'];
                        $tmpOperators['renewal']['total'] = $total_avg_t_renewal['sum'];
                        $tmpOperators['renewal']['t_mo_end'] = $total_avg_t_renewal['T_Mo_End'];
                        $tmpOperators['renewal']['avg'] = $total_avg_t_renewal['avg'];

                        $total_avg_mt_success = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['daily_push_success'], $startColumnDateDisplay, $end_date);
                        $tmpOperators['daily_push_success']['dates'] = $reportsColumnData['daily_push_success'];
                        $tmpOperators['daily_push_success']['total'] = $total_avg_mt_success['sum'];
                        $tmpOperators['daily_push_success']['t_mo_end'] = $total_avg_mt_success['T_Mo_End'];
                        $tmpOperators['daily_push_success']['avg'] = $total_avg_mt_success['avg'];

                        $total_avg_mt_failed = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['daily_push_failed'], $startColumnDateDisplay, $end_date);
                        $tmpOperators['daily_push_failed']['dates'] = $reportsColumnData['daily_push_failed'];
                        $tmpOperators['daily_push_failed']['total'] = $total_avg_mt_failed['sum'];
                        $tmpOperators['daily_push_failed']['t_mo_end'] = $total_avg_mt_failed['T_Mo_End'];
                        $tmpOperators['daily_push_failed']['avg'] = $total_avg_mt_failed['avg'];

                        $total_avg_t_bill = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['bill'], $startColumnDateDisplay, $end_date);
                        $tmpOperators['bill']['dates'] = $reportsColumnData['bill'];
                        $tmpOperators['bill']['total'] = 0;
                        $tmpOperators['bill']['t_mo_end'] = 0;
                        $tmpOperators['bill']['avg'] = $total_avg_t_bill['avg'];

                        $total_avg_t_first_push = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['first_push'], $startColumnDateDisplay, $end_date);
                        $tmpOperators['first_push']['dates'] = $reportsColumnData['first_push'];
                        $tmpOperators['first_push']['total'] = 0;
                        $tmpOperators['first_push']['t_mo_end'] = 0;
                        $tmpOperators['first_push']['avg'] = $total_avg_t_first_push['avg'];

                        $total_avg_t_daily_push = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['daily_push'], $startColumnDateDisplay, $end_date);
                        $tmpOperators['daily_push']['dates'] = $reportsColumnData['daily_push'];
                        $tmpOperators['daily_push']['total'] = 0;
                        $tmpOperators['daily_push']['t_mo_end'] = 0;
                        $tmpOperators['daily_push']['avg'] = $total_avg_t_daily_push['avg'];

                        $total_avg_t_arpu7 = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['arpu7'], $startColumnDateDisplay, $end_date);
                        $tmpOperators['arpu7']['dates'] = $reportsColumnData['arpu7'];
                        $tmpOperators['arpu7']['total'] = 0;
                        $tmpOperators['arpu7']['t_mo_end'] = 0;
                        $tmpOperators['arpu7']['avg'] = $total_avg_t_arpu7['avg'];

                        $total_avg_t_usarpu7 = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['usarpu7'], $startColumnDateDisplay, $end_date);
                        $tmpOperators['usarpu7']['dates'] = $reportsColumnData['usarpu7'];
                        $tmpOperators['usarpu7']['total'] = 0;
                        $tmpOperators['usarpu7']['t_mo_end'] = 0;
                        $tmpOperators['usarpu7']['avg'] = $total_avg_t_usarpu7['avg'];

                        $total_avg_t_arpu30 = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['arpu30'], $startColumnDateDisplay, $end_date);
                        $tmpOperators['arpu30']['dates'] = $reportsColumnData['arpu30'];
                        $tmpOperators['arpu30']['total'] = 0;
                        $tmpOperators['arpu30']['t_mo_end'] = 0;
                        $tmpOperators['arpu30']['avg'] = $total_avg_t_arpu30['avg'];

                        $total_avg_t_usarpu30 = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['usarpu30'], $startColumnDateDisplay, $end_date);
                        $tmpOperators['usarpu30']['dates'] = $reportsColumnData['usarpu30'];
                        $tmpOperators['usarpu30']['total'] = 0;
                        $tmpOperators['usarpu30']['t_mo_end'] = 0;
                        $tmpOperators['usarpu30']['avg'] =  $total_avg_t_usarpu30['avg'];

                        $total_avg_first_day_active = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['first_day_active'], $startColumnDateDisplay, $end_date);

                        $tmpOperators['first_day_active']['dates'] = $reportsColumnData['first_day_active'];
                        $tmpOperators['first_day_active']['total'] = $total_avg_first_day_active['sum'];
                        $tmpOperators['first_day_active']['t_mo_end'] = $total_avg_first_day_active['T_Mo_End'];
                        $tmpOperators['first_day_active']['avg'] = $total_avg_first_day_active['avg'];

                        $total_avg_cost_campaign = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['cost_campaign'], $startColumnDateDisplay, $end_date);

                        $tmpOperators['cost_campaign']['dates'] = $reportsColumnData['cost_campaign'];
                        $tmpOperators['cost_campaign']['total'] = $total_avg_cost_campaign['sum'];
                        $tmpOperators['cost_campaign']['t_mo_end'] = $total_avg_cost_campaign['T_Mo_End'];
                        $tmpOperators['cost_campaign']['avg'] = $total_avg_cost_campaign['avg'];

                        $total_avg_ltv = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['ltv'], $startColumnDateDisplay, $end_date);

                        $tmpOperators['ltv']['dates'] = $reportsColumnData['ltv'];
                        $tmpOperators['ltv']['total'] = $total_avg_ltv['sum'];
                        $tmpOperators['ltv']['t_mo_end'] = $total_avg_ltv['T_Mo_End'];
                        $tmpOperators['ltv']['avg'] = $total_avg_ltv['avg'];

                        $tmpOperators['mt_success']['dates'] = $reportsColumnData['mt_success'];
                        $tmpOperators['mt_failed']['dates'] = $reportsColumnData['mt_failed'];

                        $tmpOperators['fmt_success']['dates'] = $reportsColumnData['fmt_success'];
                        $tmpOperators['fmt_failed']['dates'] = $reportsColumnData['fmt_failed'];

                        $sumemry[$userId][] = $tmpOperators;
                    }
                }
            }
        }

        // Account Manager Sum from Operator array

        $displayAccountManagers = array();
        $SelectedAccountManagers = array();

        $RowUserData = array();

        if (!empty($sumemry)) {
            foreach ($sumemry as $key => $sumemries) {
                $tempDataArr = array();
                $manager_id = $key;
                $managerName = "NA";

                if (isset($sumemries[0]['account_manager']));
                $managerName = $sumemries[0]['account_manager'];

                $dataRowSum = UtilityReports::CountrySumOperator($sumemries);
                $dataRowSum = UtilityReports::PercentageCountryWise($dataRowSum);

                $tempDataArr['account_manager']['name'] = $managerName;
                $tempDataArr['month_string'] = $month;

                $tempDataArr = array_merge($tempDataArr, $dataRowSum);

                $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "tur");
                $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "turt");
                $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "net_rev");
                $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "t_rev");
                $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "t_sub");
                $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "trat");
                $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "reg");
                $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "unreg");
                $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "purged");
                $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "churn");
                $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "renewal");
                $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "daily_push_success");
                $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "daily_push_failed");
                $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "bill");
                $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "first_push");
                $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "daily_push");
                $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "arpu7");
                $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "usarpu7");
                $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "arpu30");
                $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "usarpu30");

                $RowUserData[] = $tempDataArr;
            }
        }

        $sumemry = $RowUserData;

        if (!empty($sumemry)) {
            $allsummaryData = UtilityReports::allsummaryData($sumemry);
            $allsummaryData = UtilityReports::allSummeryPerCal($allsummaryData);

            /*put color code into all summary data*/
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "tur");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "turt");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "net_rev");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "t_rev");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "t_sub");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "trat");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "reg");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "unreg");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "purged");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "churn");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "renewal");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "daily_push_success");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "daily_push_failed");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "bill");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "first_push");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "daily_push");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "first_push");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "arpu7");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "usarpu7");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "arpu30");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "usarpu30");
        } else {
            $allsummaryData = [];
        }

        return view('report.daily_manager_report', compact('AccountManagerWise', 'companys', 'sumemry', 'no_of_days', 'allsummaryData'));
    }

    // get reconcialiation media daily report
    public function DailyOperatorReconcialiation(Request $request)
    {
        if (\Auth::user()->can('Reconcialiation Media')) {
            // get filtre request
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
            }

            if ($request->filled('company') && $req_CompanyId != "allcompany" && !$request->filled('operatorId')) {
                $companies = Company::Find($req_CompanyId);
                $Operators_company = array();

                if (!empty($companies)) {
                    $Operators_company = $companies->company_operators->pluck('operator_id')->toArray();
                    $Operators = Operator::with('revenueshare', 'RevenushareByDate')
                        ->Status(1)
                        ->GetOperatorByOperatorId($Operators_company)
                        ->get();
                }

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
                $Operators = Operator::with('revenueshare', 'RevenushareByDate')
                    ->Status(1)
                    ->GetOperatorByOperatorId($filterOperator)
                    ->get();

                $showAllOperator = false;
            }

            if ($showAllOperator) {
                $Operators = Operator::with('revenueshare', 'RevenushareByDate')->Status(1)->get();
            }

            if (!isset($Operators)) {
                $request->session()->flash('alert-success', 'User was successful added!');
                return redirect()->back();
            }

            /* filter End Section */

            /* All country retrive becuase Avoid nested query for each operator country */

            $Country = Country::all()->toArray();
            $countries = array();

            if (!empty($Country)) {
                foreach ($Country as $CountryI) {
                    $countries[$CountryI['id']] = $CountryI;
                }
            }

            /* End All country retrive becuase Avoid nested query for each operator country */

            $arrayOperatorsIds = $Operators->pluck('id_operator')->toArray();

            $sumemry = array();

            /* Admin Access All operator and Services */

            $user = Auth::user();

            $user_id = $user->id;
            $user_type = $user->type;
            $allowAllOperator = $user->WhowAccessAlOperator($user_type);

            if ($allowAllOperator) {
                $reports = ReportsPnlsOperatorSummarizes::select('id_operator as operator_id', 'date', 'cost_campaign', 'mo_received', 'updated_at')->filteroperator($arrayOperatorsIds)
                    ->filterDateRange($start_date, $end_date)
                    ->orderBy('id_operator')
                    ->get()
                    ->toArray();
            } else // Normal User The operator and Service Set From Admin Panel
            {
                $UserOperatorServices = Session::get('userOperatorService');

                if (empty($UserOperatorServices)) {
                    dd("Please Contact to admin , add Operator to your account");
                }

                $arrayOperatorsIds = $UserOperatorServices['id_operators'];
                $arrayServicesIds = $UserOperatorServices['id_services'];

                $reports = ReportsPnlsOperatorSummarizes::select('id_operator as operator_id', 'date', 'cost_campaign', 'mo_received', 'updated_at')->filteroperator($arrayOperatorsIds)
                    ->filterDateRange($start_date, $end_date)
                    ->orderBy('id_operator')
                    ->get()
                    ->toArray();
            }

            $reconcialiation = Reconcialiation::filteroperator($arrayOperatorsIds)
                ->filterDateRange($start_date, $end_date)
                ->get()
                ->toArray();

            $reportsByIDs = $this->getReportsOperatorID($reports);
            $reconcialiationByIDs = $this->getReportsOperatorID($reconcialiation);
            $datesIndividual = Utility::getRangeDates($startColumnDateDisplay, $end_date);
            $no_of_days = Utility::getRangeDateNo($datesIndividual);

            if (!empty($Operators)) {
                foreach ($Operators as $operator) {
                    $tmpOperators = array();

                    $tmpOperators['operator'] = $operator;
                    $country_id  = $operator->country_id;
                    $contain_id = Arr::exists($countries, $country_id);
                    $OperatorCountry = array();

                    if ($contain_id) {
                        $tmpOperators['country'] = $countries[$country_id];
                        $OperatorCountry = $countries[$country_id];
                    }

                    $reportsColumnData = $this->getReconcialiationDateWise($operator, $no_of_days, $reportsByIDs, $reconcialiationByIDs, $OperatorCountry);

                    $tmpOperators['month_string'] = $month;
                    $tmpOperators['last_update'] = $reportsColumnData['last_update'];

                    $total_avg_cost_campaign = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['cost_campaign'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['cost_campaign']['dates'] = $reportsColumnData['cost_campaign'];
                    $tmpOperators['cost_campaign']['total'] = $total_avg_cost_campaign['sum'];
                    $tmpOperators['cost_campaign']['t_mo_end'] = $total_avg_cost_campaign['T_Mo_End'];
                    $tmpOperators['cost_campaign']['avg'] = $total_avg_cost_campaign['avg'];

                    $total_avg_input_cost_campaign = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['input_cost_campaign'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['input_cost_campaign']['dates'] = $reportsColumnData['input_cost_campaign'];
                    $tmpOperators['input_cost_campaign']['total'] = $total_avg_input_cost_campaign['sum'];
                    $tmpOperators['input_cost_campaign']['t_mo_end'] = $total_avg_input_cost_campaign['T_Mo_End'];
                    $tmpOperators['input_cost_campaign']['avg'] = $total_avg_input_cost_campaign['avg'];

                    $total_avg_cost_campaign_disc = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['cost_campaign_disc'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['cost_campaign_disc']['dates'] = $reportsColumnData['cost_campaign_disc'];
                    $cost = $tmpOperators['cost_campaign']['total'];
                    $input_cost = $tmpOperators['input_cost_campaign']['total'];
                    $tmpOperators['cost_campaign_disc']['avg'] = ($cost != 0 && $input_cost != 0) ? (($input_cost - $cost) / $cost) * 100 : (float)0;

                    $total_avg_mo = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['mo'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['mo']['dates'] = $reportsColumnData['mo'];
                    $tmpOperators['mo']['total'] = $total_avg_mo['sum'];
                    $tmpOperators['mo']['t_mo_end'] = $total_avg_mo['T_Mo_End'];
                    $tmpOperators['mo']['avg'] = $total_avg_mo['avg'];

                    $total_avg_input_mo = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['input_mo'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['input_mo']['dates'] = $reportsColumnData['input_mo'];
                    $tmpOperators['input_mo']['total'] = $total_avg_input_mo['sum'];
                    $tmpOperators['input_mo']['t_mo_end'] = $total_avg_input_mo['T_Mo_End'];
                    $tmpOperators['input_mo']['avg'] = $total_avg_input_mo['avg'];

                    $total_avg_mo_disc = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['mo_disc'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['mo_disc']['dates'] = $reportsColumnData['mo_disc'];
                    $mo = $tmpOperators['mo']['total'];
                    $input_mo = $tmpOperators['input_mo']['total'];
                    $tmpOperators['mo_disc']['avg'] = ($mo != 0 && $input_mo != 0) ? (($input_mo - $mo) / $mo) * 100 : (float)0;

                    $total_avg_price_mo = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['price_mo'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['price_mo']['dates'] = $reportsColumnData['price_mo'];
                    $tmpOperators['price_mo']['total'] = $total_avg_price_mo['sum'];
                    $tmpOperators['price_mo']['t_mo_end'] = $total_avg_price_mo['T_Mo_End'];
                    $tmpOperators['price_mo']['avg'] = $total_avg_price_mo['avg'];

                    $total_avg_input_price_mo = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['input_price_mo'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['input_price_mo']['dates'] = $reportsColumnData['input_price_mo'];
                    $tmpOperators['input_price_mo']['total'] = $total_avg_input_price_mo['sum'];
                    $tmpOperators['input_price_mo']['t_mo_end'] = $total_avg_input_price_mo['T_Mo_End'];
                    $tmpOperators['input_price_mo']['avg'] = $total_avg_input_price_mo['avg'];

                    $total_avg_price_mo_disc = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['price_mo_disc'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['price_mo_disc']['dates'] = $reportsColumnData['price_mo_disc'];
                    $price_mo = $tmpOperators['price_mo']['total'];
                    $input_price_mo = $tmpOperators['input_price_mo']['total'];
                    $tmpOperators['price_mo_disc']['avg'] = ($price_mo != 0 && $input_price_mo != 0) ? (($input_price_mo - $price_mo) / $price_mo) * 100 : (float)0;

                    $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators, "cost_campaign");
                    $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators, "input_cost_campaign");
                    $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators, "cost_campaign_disc");
                    $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators, "mo");
                    $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators, "input_mo");
                    $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators, "mo_disc");
                    $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators, "price_mo");
                    $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators, "input_price_mo");
                    $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators, "price_mo_disc");

                    $sumemry[] = $tmpOperators;
                }
            }

            $allsummaryData = [];

            if (sizeof($sumemry) > 0) {
                /*sum of all summery datas*/
                $allsummaryData = UtilityReports::allreconcialiationData($sumemry);
                $allsummaryData = UtilityReports::allSummeryPerCal($allsummaryData);

                /*put color code into all summary data*/
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "cost_campaign");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "input_cost_campaign");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "cost_campaign_disc");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "mo");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "input_mo");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "mo_disc");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "price_mo");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "input_price_mo");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "price_mo_disc");
            }

            return view('report.daily_operator_reconcialiation', compact('sumemry', 'no_of_days', 'companys', 'allsummaryData'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function DailyCountryReconcialiation(Request $request)
    {
        if (\Auth::user()->can('Reconcialiation Media')) {
            $CountryWise = 1;

            // get filtre request
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

            $companys = Company::get();

            /* filter Search Section */

            $showAllOperator = true;

            if ($request->filled('to') && $request->filled('from')) {
                $display_date_input = new Carbon($req_Start_date);
                $start_date = $req_Start_date;
                $startColumnDateDisplay = $display_date_input->format('Y-m-d');
                $end_date = $req_end_date;
                $month = $display_date_input->format('F Y');
            }

            if ($request->filled('company') && $req_CompanyId != "allcompany" && !$request->filled('operatorId')) {
                $companies = Company::Find($req_CompanyId);
                $Operators_company = array();

                if (!empty($companies)) {
                    $Operators_company = $companies->company_operators->pluck('operator_id')->toArray();
                    $Operators = Operator::with('revenueshare', 'RevenushareByDate')
                        ->Status(1)
                        ->GetOperatorByOperatorId($Operators_company)
                        ->get();
                }

                $showAllOperator = false;
            }

            if ($request->filled('country') && !$request->filled('operatorId')) {
                $Operators = Operator::with('revenueshare', 'RevenushareByDate')
                    ->Status(1)
                    ->GetOperatorByCountryId($req_CountryId)
                    ->get();

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
                $Operators = Operator::with('revenueshare', 'RevenushareByDate')
                    ->Status(1)
                    ->GetOperatorByOperatorId($filterOperator)
                    ->get();

                $showAllOperator = false;
            }

            if ($showAllOperator) {
                $Operators = Operator::with('revenueshare', 'RevenushareByDate')->Status(1)->get();
            }

            if (!isset($Operators)) {
                $request->session()->flash('alert-success', 'User was successful added!');
                return redirect()->back();
            }

            /* filter End Section */

            $Country = Country::all()->toArray();
            $countries = array();

            if (!empty($Country)) {
                foreach ($Country as $CountryI) {
                    $countries[$CountryI['id']] = $CountryI;
                }
            }

            $arrayOperatorsIds = $Operators->pluck('id_operator')->toArray();

            /* Admin Access All operator and Services */

            $user = Auth::user();

            $user_id = $user->id;
            $user_type = $user->type;
            $allowAllOperator = $user->WhowAccessAlOperator($user_type);

            if ($allowAllOperator) {
                $reports = ReportsPnlsOperatorSummarizes::select('id_operator as operator_id', 'date', 'cost_campaign', 'mo_received', 'updated_at')->filteroperator($arrayOperatorsIds)
                    ->filterDateRange($start_date, $end_date)
                    ->orderBy('id_operator')
                    ->get()
                    ->toArray();
            } else // Normal User The operator and Service Set From Admin Panel
            {
                $UserOperatorServices = Session::get('userOperatorService');

                if (empty($UserOperatorServices)) {
                    dd("Please Contact to admin , add Operator to your account");
                }

                $arrayOperatorsIds = $UserOperatorServices['id_operators'];
                $arrayServicesIds = $UserOperatorServices['id_services'];

                $reports = ReportsPnlsOperatorSummarizes::select('id_operator as operator_id', 'date', 'cost_campaign', 'mo_received', 'updated_at')->filteroperator($arrayOperatorsIds)
                    ->filterDateRange($start_date, $end_date)
                    ->orderBy('id_operator')
                    ->get()
                    ->toArray();
            }

            $reconcialiation = Reconcialiation::filteroperator($arrayOperatorsIds)
                ->filterDateRange($start_date, $end_date)
                ->get()
                ->toArray();

            $reportsByIDs = $this->getReportsOperatorID($reports);
            $reconcialiationByIDs = $this->getReportsOperatorID($reconcialiation);
            $datesIndividual = Utility::getRangeDates($startColumnDateDisplay, $end_date);
            $no_of_days = Utility::getRangeDateNo($datesIndividual);

            if (!empty($Operators)) {
                foreach ($Operators as $operator) {
                    $tmpOperators = array();

                    $tmpOperators['operator'] = $operator;
                    $country_id  = $operator->country_id;
                    $contain_id = Arr::exists($countries, $country_id);
                    $OperatorCountry = array();

                    if ($contain_id) {
                        $tmpOperators['country'] = $countries[$country_id];
                        $OperatorCountry = $countries[$country_id];
                    }

                    $reportsColumnData = $this->getReconcialiationDateWise($operator, $no_of_days, $reportsByIDs, $reconcialiationByIDs, $OperatorCountry);

                    $tmpOperators['month_string'] = $month;
                    $tmpOperators['last_update'] = $reportsColumnData['last_update'];

                    $total_avg_cost_campaign = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['cost_campaign'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['cost_campaign']['dates'] = $reportsColumnData['cost_campaign'];
                    $tmpOperators['cost_campaign']['total'] = $total_avg_cost_campaign['sum'];
                    $tmpOperators['cost_campaign']['t_mo_end'] = $total_avg_cost_campaign['T_Mo_End'];
                    $tmpOperators['cost_campaign']['avg'] = $total_avg_cost_campaign['avg'];

                    $total_avg_input_cost_campaign = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['input_cost_campaign'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['input_cost_campaign']['dates'] = $reportsColumnData['input_cost_campaign'];
                    $tmpOperators['input_cost_campaign']['total'] = $total_avg_input_cost_campaign['sum'];
                    $tmpOperators['input_cost_campaign']['t_mo_end'] = $total_avg_input_cost_campaign['T_Mo_End'];
                    $tmpOperators['input_cost_campaign']['avg'] = $total_avg_input_cost_campaign['avg'];

                    $total_avg_cost_campaign_disc = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['cost_campaign_disc'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['cost_campaign_disc']['dates'] = $reportsColumnData['cost_campaign_disc'];
                    $cost = $tmpOperators['cost_campaign']['total'];
                    $input_cost = $tmpOperators['input_cost_campaign']['total'];
                    $tmpOperators['cost_campaign_disc']['avg'] = ($cost != 0 && $input_cost != 0) ? (($input_cost - $cost) / $cost) * 100 : (float)0;

                    $total_avg_mo = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['mo'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['mo']['dates'] = $reportsColumnData['mo'];
                    $tmpOperators['mo']['total'] = $total_avg_mo['sum'];
                    $tmpOperators['mo']['t_mo_end'] = $total_avg_mo['T_Mo_End'];
                    $tmpOperators['mo']['avg'] = $total_avg_mo['avg'];

                    $total_avg_input_mo = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['input_mo'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['input_mo']['dates'] = $reportsColumnData['input_mo'];
                    $tmpOperators['input_mo']['total'] = $total_avg_input_mo['sum'];
                    $tmpOperators['input_mo']['t_mo_end'] = $total_avg_input_mo['T_Mo_End'];
                    $tmpOperators['input_mo']['avg'] = $total_avg_input_mo['avg'];

                    $total_avg_mo_disc = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['mo_disc'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['mo_disc']['dates'] = $reportsColumnData['mo_disc'];
                    $mo = $tmpOperators['mo']['total'];
                    $input_mo = $tmpOperators['input_mo']['total'];
                    $tmpOperators['mo_disc']['avg'] = ($mo != 0 && $input_mo != 0) ? (($input_mo - $mo) / $mo) * 100 : (float)0;

                    $total_avg_price_mo = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['price_mo'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['price_mo']['dates'] = $reportsColumnData['price_mo'];
                    $tmpOperators['price_mo']['total'] = $total_avg_price_mo['sum'];
                    $tmpOperators['price_mo']['t_mo_end'] = $total_avg_price_mo['T_Mo_End'];
                    $tmpOperators['price_mo']['avg'] = $total_avg_price_mo['avg'];

                    $total_avg_input_price_mo = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['input_price_mo'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['input_price_mo']['dates'] = $reportsColumnData['input_price_mo'];
                    $tmpOperators['input_price_mo']['total'] = $total_avg_input_price_mo['sum'];
                    $tmpOperators['input_price_mo']['t_mo_end'] = $total_avg_input_price_mo['T_Mo_End'];
                    $tmpOperators['input_price_mo']['avg'] = $total_avg_input_price_mo['avg'];

                    $total_avg_price_mo_disc = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['price_mo_disc'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['price_mo_disc']['dates'] = $reportsColumnData['price_mo_disc'];
                    $price_mo = $tmpOperators['price_mo']['total'];
                    $input_price_mo = $tmpOperators['input_price_mo']['total'];
                    $tmpOperators['price_mo_disc']['avg'] = ($price_mo != 0 && $input_price_mo != 0) ? (($input_price_mo - $price_mo) / $price_mo) * 100 : (float)0;

                    $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators, "cost_campaign");
                    $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators, "input_cost_campaign");
                    $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators, "cost_campaign_disc");
                    $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators, "mo");
                    $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators, "input_mo");
                    $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators, "mo_disc");
                    $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators, "price_mo");
                    $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators, "input_price_mo");
                    $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators, "price_mo_disc");

                    $sumemry[] = $tmpOperators;
                }
            }

            // Country Sum from Operator array

            $displayCountries = array();
            $SelectedCountries = array();

            if (!empty($sumemry)) {
                foreach ($sumemry as $key => $sumemries) {

                    $country_id = $sumemries['country']['id'];

                    $SelectedCountries[$country_id] = $sumemries['country'];

                    $displayCountries[$country_id][] = $sumemries;
                }
            }

            $RowCountryData = array();

            if (!empty($SelectedCountries)) {
                foreach ($SelectedCountries as $key => $SelectedCountry) {
                    $tempDataArr = array();

                    $country_id = $SelectedCountry['id'];

                    $dataRowSum = UtilityReports::CountrySumReconcialiation($displayCountries[$country_id]);

                    // $dataRowSum = UtilityReports::PercentageCountryWise($dataRowSum,$country_id);

                    $tempDataArr['country'] = $SelectedCountry;
                    $tempDataArr['month_string'] = $month;

                    $tempDataArr = array_merge($tempDataArr, $dataRowSum);

                    $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "cost_campaign");
                    $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "input_cost_campaign");
                    $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "cost_campaign_disc");
                    $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "mo");
                    $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "input_mo");
                    $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "mo_disc");
                    $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "price_mo");
                    $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "input_price_mo");
                    $tempDataArr = UtilityReports::ColorFirstDay($tempDataArr, "price_mo_disc");

                    $RowCountryData[] = $tempDataArr;
                }
            }

            $allsummaryData = [];

            if (sizeof($sumemry) > 0) {
                /*sum of all summery datas*/
                $allsummaryData = UtilityReports::allreconcialiationData($sumemry);
                $allsummaryData = UtilityReports::allSummeryPerCal($allsummaryData);

                /*put color code into all summary data*/
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "cost_campaign");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "input_cost_campaign");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "cost_campaign_disc");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "mo");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "input_mo");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "mo_disc");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "price_mo");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "input_price_mo");
                $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "price_mo_disc");
            }

            return view('report.daily_country_reconcialiation', compact('RowCountryData', 'no_of_days', 'companys', 'allsummaryData', 'CountryWise'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function createReconcialiation(Request $request)
    {
        return view('report.create_reconcialiation');
    }

    public function storeReconcialiationExcel(Request $request)
    {
        $requestData = $request->all();
        $files = isset($requestData['file']) ? $requestData['file'] : [];

        if (empty($files)) {
            return redirect()->back()->with('error', __('please select a file first!'));
        }

        $file = Excel::toArray(new UsersImport, $files);

        $months = ['january' => '01', 'february' => '02', 'march' => '03', 'april' => '04', 'may' => '05', 'june' => '06', 'july' => '07', 'august' => '08', 'september' => '09', 'october' => '10', 'november' => '11', 'december' => '12'];

        foreach ($file[0] as $key => $value) {
            $cost_campaign = 0;
            $mo = 0;
            $price_mo = 0;

            $year = $value['year'];

            if (empty($value['operator']))
                break;

            $month = $months[$value['month']];

            $operator = Operator::where('operator_name', $value['operator'])->first()->toArray();

            $operator_id = $operator['id_operator'];

            $country_id = $operator['country_id'];

            $name = $value['name'];

            foreach ($value as $key1 => $value1) {
                $data = [];
                if (is_int($key1) && isset($value1) && $value1 != 0) {
                    $date = $year . '-' . $month . '-' . $key1;

                    $Reconcialiation = Reconcialiation::filterCountry($country_id)
                        ->filteroperatorID($operator_id)
                        ->filterDate($date)
                        ->first();

                    if ($Reconcialiation) {
                        $Reconcialiation = $Reconcialiation->toArray();
                    }

                    if ($name == 'cost campaign') {
                        $cost_campaign = $value1;

                        if (isset($Reconcialiation)) {
                            $mo = $Reconcialiation['mo'];
                            $price_mo = $Reconcialiation['price_mo'];
                        }
                    } elseif ($name == 'campaign mo') {
                        $mo = $value1;

                        if (isset($Reconcialiation)) {
                            $cost_campaign = $Reconcialiation['cost_campaign'];
                            $price_mo = $Reconcialiation['price_mo'];
                        }
                    } elseif ($name == 'price mo') {
                        $price_mo = $value1;

                        if (isset($Reconcialiation)) {
                            $cost_campaign = $Reconcialiation['cost_campaign'];
                            $mo = $Reconcialiation['mo'];
                        }
                    }

                    $data = ['country_id' => $country_id, 'operator_id' => $operator_id, 'year' => $year, 'month' => $month, 'date' => $date, 'cost_campaign' => $cost_campaign, 'mo' => $mo, 'price_mo' => $price_mo];

                    Reconcialiation::upsert($data, ['operator_id', 'date'], ['country_id', 'year', 'month', 'cost_campaign', 'mo', 'price_mo']);

                    Utility::user_activity('Update Reconcialiation Excel');
                }
            }
        }

        return view('report.reconcialiation_popup', compact('file'))->with('success', __('Reconcialiation successfully added!'));
    }

    // get report using country id
    function getReportsCountryID($reports)
    {
        if (!empty($reports)) {
            $reportsResult = array();
            $tempreport = array();

            foreach ($reports as $report) {
                $tempreport[$report['country_id']][$report['date']] = $report;
            }

            $reportsResult =  $tempreport;

            return $reportsResult;
        }
    }

    // get report using operator id
    function getReportsOperatorID($reports)
    {
        if (!empty($reports)) {
            $reportsResult = array();
            $tempreport = array();

            foreach ($reports as $report) {
                $tempreport[$report['operator_id']][$report['date']] = $report;
            }

            $reportsResult = $tempreport;

            return $reportsResult;
        }
    }

    // get report date wise
    function getReportsDateWise($operator, $no_of_days, $reportsByIDs, $activesubsdata = array(), $costdata = array(), $OperatorCountry)
    {
        $usdValue = isset($OperatorCountry['usd']) ? $OperatorCountry['usd'] : 1;
        $shareDb = array();
        $merchent_share = 1;
        $operator_share = 1;
        $revenue_share = $operator->revenueshare;
        $revenushare_by_dates = $operator->RevenushareByDate;
        $country_id = $OperatorCountry['id'];
        $vat = 0;
        $wht = 0;
        $VatByDate = $operator->VatByDate;
        $WhtByDate = $operator->WhtByDate;
        $misc_taxByDate = $operator->MiscTax;


        if (isset($WhtByDate)) {
            foreach ($WhtByDate as $key => $value) {
                unset($WhtByDate[$key]);
                $WhtByDate[$value['key']] = $value;
            }
        }
        if (isset($VatByDate)) {
            foreach ($VatByDate as $key => $value) {
                unset($VatByDate[$key]);
                $VatByDate[$value['key']] = $value;
            }
        }
        if (isset($misc_taxByDate)) {
            foreach ($misc_taxByDate as $key => $value) {
                unset($misc_taxByDate[$key]);
                $misc_taxByDate[$value['key']] = $value;
            }
        }
        if (isset($revenue_share)) {
            $merchent_share = $revenue_share->merchant_revenue_share;
            $operator_share = $revenue_share->operator_revenue_share;
        }

        if (isset($revenushare_by_dates)) {
            foreach ($revenushare_by_dates as $key => $value) {
                unset($revenushare_by_dates[$key]);
                $revenushare_by_dates[$value['key']] = $value;
            }
        }

        if (!empty($no_of_days)) {
            $allColumnData = array();
            $arpu7Raw = array();
            $arpu30Raw = array();
            $tur = array();
            $t_rev = array();
            $trat = array();
            $turt = array();
            $net_rev = array();
            $t_sub = array();
            $reg = array();
            $unreg = array();
            $purged = array();
            $churn = array();
            $renewal = array();
            $daily_push_success = array();
            $daily_push_failed = array();
            $bill = array();
            $first_push = array();
            $daily_push = array();
            $arpu7 = array();
            $usarpu7 = array();
            $arpu30 = array();
            $usarpu30 = array();
            $mtSuccess = array();
            $mtFailed = array();
            $fmtSuccess = array();
            $fmtFailed = array();
            $ltv = array();
            $last_update = "";
            $id_operator = $operator->id_operator;

            $testUSDSum = 0;
            $update = false;

            foreach ($no_of_days as $days) {
                $shareDb['merchent_share'] = $merchent_share;
                $shareDb['operator_share'] = $operator_share;

                $keys = $id_operator . "." . $days['date'];

                $key_date = new Carbon($days['date']);
                $key = $key_date->format("Y-m");

                if (isset($revenushare_by_dates[$key])) {
                    $merchent_share_by_dates = $revenushare_by_dates[$key]->merchant_revenue_share;
                    $operator_share_by_dates = $revenushare_by_dates[$key]->operator_revenue_share;

                    $shareDb['merchent_share'] = $merchent_share_by_dates;
                    $shareDb['operator_share'] = $operator_share_by_dates;
                }

                $summariserow = Arr::get($reportsByIDs, $keys, 0);

                $firstDayActive = Arr::get($activesubsdata, $keys, 0);

                $costCampaign = Arr::get($costdata, $keys, 0);

                if ($summariserow != 0 && !$update) {
                    $update = true;
                    $last_update = $summariserow['updated_at'];
                }

                $gros_rev_Usd = 0;

                $gros_rev = isset($summariserow['gros_rev']) ? $summariserow['gros_rev'] : 0;

                $total_subscriber = isset($summariserow['total']) ? $summariserow['total'] : 0;

                $gros_rev_Usd = $gros_rev * $usdValue;

                $dataPercentageTags = array();

                $testUSDSum = $testUSDSum + $gros_rev_Usd;

                $total_reg = isset($summariserow['total_reg']) ? $summariserow['total_reg'] : 0;
                $total_unreg = isset($summariserow['total_unreg']) ? $summariserow['total_unreg'] : 0;
                $purge_total = isset($summariserow['purge_total']) ? $summariserow['purge_total'] : 0;

                $mt_success = isset($summariserow['mt_success']) ? $summariserow['mt_success'] : 0;
                $mt_failed = isset($summariserow['mt_failed']) ? $summariserow['mt_failed'] : 0;
                $fmt_success = isset($summariserow['fmt_success']) ? $summariserow['fmt_success'] : 0;
                $fmt_failed = isset($summariserow['fmt_failed']) ? $summariserow['fmt_failed'] : 0;

                if ($total_subscriber > 0) {
                    $churn_value = ((int)$total_unreg  / (int)$total_subscriber) * 100;
                } else {
                    $churn_value = 0;
                }

                $renewal_total = $mt_success + $mt_failed;

                $billRate = UtilityReports::billRate($mt_success, $mt_failed, $total_subscriber);

                $FirstPush = 0;

                $FirstPush = UtilityReports::FirstPush($fmt_success, $fmt_failed, $total_subscriber);

                $Dailypush = UtilityReports::Dailypush($mt_success, $mt_failed, $total_subscriber);

                $arpu7Data = UtilityReports::Arpu7($operator, $reportsByIDs, $days, $total_subscriber, $shareDb);
                $arpu7USD = $arpu7Data * $usdValue;

                $arpuRawdata = UtilityPercentage::Arpu7Raw($operator, $reportsByIDs, $days, $total_subscriber, $shareDb, $OperatorCountry);

                $arpu30Data = UtilityReports::Arpu30($operator, $reportsByIDs, $days, $total_subscriber, $shareDb);
                $arpu30USD =  $arpu30Data * $usdValue;

                $arpu30Rawdata = UtilityPercentage::Arpu30Raw($operator, $reportsByIDs, $days, $total_subscriber, $shareDb, $OperatorCountry);

                $tratData = UtilityReports::trat($shareDb, $gros_rev);
                $turtData = UtilityReports::turt($shareDb, $gros_rev_Usd);

                $vat = !empty($operator->vat) ? $turtData * ($operator->vat / 100) : 0;
                $wht = !empty($operator->wht) ? $turtData * ($operator->wht / 100) : 0;
                $misc_tax = !empty($operator->miscTax) ? $turtData * ($operator->miscTax / 100) : 0;

                if (isset($VatByDate[$key])) {
                    $Vat = $VatByDate[$key]->vat;
                    $vat = !empty($Vat) ? $turtData * ($Vat / 100) : 0;
                }
                if (isset($WhtByDate[$key])) {
                    $Wht = $WhtByDate[$key]->wht;
                    $wht = !empty($Wht) ? $turtData * ($Wht / 100) : 0;
                }
                if (isset($misc_taxByDate[$key])) {
                    $Misc_tax = $misc_taxByDate[$key]->misc_tax;
                    $misc_tax = !empty($Misc_tax) ? $turtData * ($Misc_tax / 100) : 0;
                }
                $other_tax = $vat + $wht + $misc_tax;

                if ($other_tax != 0) {
                    $netRev = $turtData - $other_tax;
                } else {
                    $netRev = $turtData;
                }

                $activeSubsFirstDay = isset($firstDayActive['total']) ? $firstDayActive['total'] : 0;

                $campaignCost = isset($costCampaign['cost_campaign']) ? $costCampaign['cost_campaign'] : 0;

                $clv = UtilityReports::LTV($turtData, $total_subscriber, $total_unreg, $activeSubsFirstDay, $campaignCost);

                $arpu7Raw[$days['date']]['value'] = $arpuRawdata;
                $arpu7Raw[$days['date']]['class'] = "";

                $arpu30Raw[$days['date']]['value'] = $arpu30Rawdata;
                $arpu30Raw[$days['date']]['class'] = "";

                $tur[$days['date']]['value'] = $gros_rev_Usd;
                $tur[$days['date']]['class'] = "";

                $t_rev[$days['date']]['value'] = $gros_rev;
                $t_rev[$days['date']]['class'] = "bg-hui";

                $trat[$days['date']]['value'] = $tratData;
                $trat[$days['date']]['class'] = "bg-hui";

                $turt[$days['date']]['value'] = $turtData;
                $turt[$days['date']]['class'] = "bg-hui";

                $net_rev[$days['date']]['value'] = $netRev;
                $net_rev[$days['date']]['class'] = "bg-hui";

                $t_sub[$days['date']]['value'] = $total_subscriber;
                $t_sub[$days['date']]['class'] = "bg-hui";

                $reg[$days['date']]['value'] = $total_reg;
                $reg[$days['date']]['class'] = "bg-hui";

                $unreg[$days['date']]['value'] = $total_unreg;
                $unreg[$days['date']]['class'] = "bg-hui";

                $purged[$days['date']]['value'] = $purge_total;
                $purged[$days['date']]['class'] = "bg-hui";

                $churn[$days['date']]['value'] = $churn_value;
                $churn[$days['date']]['class'] = "bg-hui";

                $renewal[$days['date']]['value'] = $renewal_total;
                $renewal[$days['date']]['class'] = "bg-hui";

                $daily_push_success[$days['date']]['value'] = $mt_success;
                $daily_push_success[$days['date']]['class'] = "bg-hui";

                $daily_push_failed[$days['date']]['value'] = $mt_failed;
                $daily_push_failed[$days['date']]['class'] = "bg-hui";

                $bill[$days['date']]['value'] = $billRate;
                $bill[$days['date']]['class'] = "bg-hui";

                $first_push[$days['date']]['value'] = $FirstPush;
                $first_push[$days['date']]['class'] = "bg-hui";

                $daily_push[$days['date']]['value'] = $Dailypush;
                $daily_push[$days['date']]['class'] = "bg-hui";

                $arpu7[$days['date']]['value'] = $arpu7Data;
                $arpu7[$days['date']]['class'] = "bg-hui";

                $usarpu7[$days['date']]['value'] = $arpu7USD;
                $usarpu7[$days['date']]['class'] = "bg-hui";

                $arpu30[$days['date']]['value'] = $arpu30Data;
                $arpu30[$days['date']]['class'] = "bg-hui";

                $usarpu30[$days['date']]['value'] = $arpu30USD;
                $usarpu30[$days['date']]['class'] = "bg-hui";

                $mtSuccess[$days['date']]['value'] = $mt_success;
                $mtSuccess[$days['date']]['class'] = "bg-hui";

                $mtFailed[$days['date']]['value'] = $mt_failed;
                $mtFailed[$days['date']]['class'] = "bg-hui";

                $fmtSuccess[$days['date']]['value'] = $fmt_success;
                $fmtSuccess[$days['date']]['class'] = "bg-hui";

                $fmtFailed[$days['date']]['value'] = $fmt_failed;
                $fmtFailed[$days['date']]['class'] = "bg-hui";

                $first_day_active[$days['date']]['value'] = $activeSubsFirstDay;
                $first_day_active[$days['date']]['class'] = "bg-hui";

                $cost_campaign[$days['date']]['value'] = $campaignCost;
                $cost_campaign[$days['date']]['class'] = "bg-hui";

                $ltv[$days['date']]['value'] = $clv;
                $ltv[$days['date']]['class'] = "bg-hui";
            }

            $last_update_show  = "Not updated last month";

            if ($last_update != "") {
                $last_update_timestamp = Carbon::parse($last_update);

                $last_update_timestamp->setTimezone('Asia/Jakarta');

                $last_update_show = $last_update_timestamp->format("Y-m-d H:i:s") . " Asia/Jakarta";
            }

            $allColumnData['tur'] = $tur;
            $allColumnData['t_rev'] = $t_rev;
            $allColumnData['trat'] = $trat;
            $allColumnData['turt'] = $turt;
            $allColumnData['net_rev'] = $net_rev;
            $allColumnData['t_sub'] = $t_sub;
            $allColumnData['reg'] = $reg;
            $allColumnData['unreg'] = $unreg;
            $allColumnData['purged'] = $purged;
            $allColumnData['churn'] = $churn;
            $allColumnData['renewal'] = $renewal;
            $allColumnData['daily_push_success'] = $daily_push_success;
            $allColumnData['daily_push_failed'] = $daily_push_failed;
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
            $allColumnData['arpu7Raw'] = $arpu7Raw;
            $allColumnData['arpu30Raw'] = $arpu30Raw;
            $allColumnData['first_day_active'] = $first_day_active;
            $allColumnData['cost_campaign'] = $cost_campaign;
            $allColumnData['ltv'] = $ltv;
            $allColumnData['last_update'] = $last_update_show;

            return $allColumnData;
        }
    }

    function getmonitorReportsDateWise($operator, $no_of_days, $reportsByIDs, $OperatorCountry)
    {
        $usdValue = $OperatorCountry['usd'];

        if (!empty($no_of_days)) {
            $allColumnData = array();
            $tur_arr = array();
            $mo = array();
            $billRate = array();
            $roi_Arr = array();
            $renewal_arr = array();
            $cost_campaign_arr = array();
            $dp_success_arr = array();
            $active_subs_arr = array();
            $dp_failed_arr = array();
            $gros_rev_usd_arr = array();
            $reg_arr = array();
            $id_operator = $operator->id_operator;

            foreach ($no_of_days as $days) {

                $keys = $id_operator . "." . $days['date'];
                $summariserow = Arr::get($reportsByIDs, $keys, 0);

                //revenue part is here
                $tur_data = isset($summariserow['rev']) ? $summariserow['rev'] : 0;
                $tur_data = $tur_data * $usdValue;

                $tur_arr[$days['date']]['value'] = $tur_data;

                //mo part is here
                $modata = isset($summariserow['mo_received']) ? $summariserow['mo_received'] : 0;
                $modata = sprintf('%0.2f', $modata);
                $mo[$days['date']]['value'] = $modata;

                $total_subscriber = isset($summariserow['active_subs']) ? $summariserow['active_subs'] : 0;

                $mt_success = isset($summariserow['dp_success']) ? $summariserow['dp_success'] : 0;
                $dp_success_arr[$days['date']]['value'] = $mt_success;

                $mt_failed = isset($summariserow['dp_failed']) ? $summariserow['dp_failed'] : 0;
                $dp_failed_arr[$days['date']]['value'] = $mt_failed;

                //bill part is here
                $bill = UtilityReports::billRate($mt_success, $mt_failed, $total_subscriber);
                $billRate[$days['date']]['value'] = $bill;

                    $cost_campaign = isset($summariserow['cost_campaign']) ? $summariserow['cost_campaign'] : 0;
                    $cost_campaign = sprintf('%0.2f', $cost_campaign);
                    $cost_campaign_arr[$days['date']]['value'] = $cost_campaign;
                //renewal part here
                $renewal = $mt_success + $mt_failed;
                $renewal = sprintf('%0.2f', $renewal);

                $renewal_arr[$days['date']]['value'] = $renewal;


                //roi part is here
                $reg = isset($summariserow['reg']) ? $summariserow['reg'] : 0;
                $reg = sprintf('%0.2f', $reg);
                $reg_arr[$days['date']]['value'] = $reg;

                $active_subs = isset($summariserow['active_subs']) ? $summariserow['active_subs'] : 0;
                $active_subs = sprintf('%0.2f', $active_subs);

                $active_subs_arr[$days['date']]['value'] = $active_subs;

                $gros_rev_Usd = (isset($summariserow['share'])) ? $summariserow['share'] : 0;
                $gros_rev_Usd = sprintf('%0.2f', $gros_rev_Usd);
                $gros_rev_usd_arr[$days['date']]['value'] = $gros_rev_Usd;

                $arpu_30_usd = ($reg == 0) ? 0 : $gros_rev_Usd / ($reg + $active_subs);

                $cost_campaign = isset($summariserow['cost_campaign']) ? $summariserow['cost_campaign'] : 0;
                $cost_campaign = sprintf('%0.2f', $cost_campaign);

                $price_mo = ($modata == 0) ? (float)0 : ($cost_campaign / $modata);

                // $roi = ($arpu_30_usd == 0) ? 0 : ($price_mo / $arpu_30_usd);
                $ROI = UtilityReports::ROI($id_operator,$reportsByIDs,$days,$active_subs,$cost_campaign,$modata);
                $roi = $ROI['roi'];
                $roi_Arr[$days['date']]['value'] = $roi;
            }

            $allColumnData['tur'] = $tur_arr;
            $allColumnData['mo'] = $mo;
            $allColumnData['bill'] = $billRate;
            $allColumnData['roi'] = $roi_Arr;
            $allColumnData['active_subs'] = $active_subs_arr;
            $allColumnData['cost_campaign'] = $cost_campaign_arr;
            $allColumnData['dp_failed'] = $dp_failed_arr;
            $allColumnData['dp_success'] = $dp_success_arr;
            $allColumnData['renewal'] = $renewal_arr;
            $allColumnData['gros_rev_usd'] = $gros_rev_usd_arr;
            $allColumnData['reg'] = $reg_arr;

            // dd($allColumnData);

            return $allColumnData;
        }
    }

    // get reconcialiation media date wise
    function getReconcialiationDateWise($operator, $no_of_days, $reportsByIDs, $reconcialiationByIDs, $OperatorCountry)
    {
        $usdValue = isset($OperatorCountry['usd']) ? $OperatorCountry['usd'] : 1;
        $shareDb = array();
        $merchent_share = 1;
        $operator_share = 1;
        $revenue_share = $operator->revenueshare;
        $revenushare_by_dates = $operator->RevenushareByDate;
        $country_id = $OperatorCountry['id'];

        if (isset($revenue_share)) {
            $merchent_share = $revenue_share->merchant_revenue_share;
            $operator_share = $revenue_share->operator_revenue_share;
        }

        if (isset($revenushare_by_dates)) {
            foreach ($revenushare_by_dates as $key => $value) {
                unset($revenushare_by_dates[$key]);
                $revenushare_by_dates[$value['key']] = $value;
            }
        }

        if (!empty($no_of_days)) {
            $allColumnData = array();
            $cost_campaign = array();
            $input_cost_campaign = array();
            $cost_campaign_disc = array();
            $mo = array();
            $input_mo = array();
            $mo_disc = array();
            $price_mo = array();
            $input_price_mo = array();
            $price_mo_disc = array();
            $last_update = "";
            $id_operator = $operator->id_operator;

            $testUSDSum = 0;
            $update = false;

            foreach ($no_of_days as $days) {
                $shareDb['merchent_share'] = $merchent_share;
                $shareDb['operator_share'] = $operator_share;

                $keys = $id_operator . "." . $days['date'];

                $key_date = new Carbon($days['date']);
                $key = $key_date->format("Y-m");

                if (isset($revenushare_by_dates[$key])) {
                    $merchent_share_by_dates = $revenushare_by_dates[$key]->merchant_revenue_share;
                    $operator_share_by_dates = $revenushare_by_dates[$key]->operator_revenue_share;

                    $shareDb['merchent_share'] = $merchent_share_by_dates;
                    $shareDb['operator_share'] = $operator_share_by_dates;
                }

                $summariserow = Arr::get($reportsByIDs, $keys, 0);

                $reconcialiationrow = Arr::get($reconcialiationByIDs, $keys, 0);

                if ($summariserow != 0 && !$update) {
                    $update = true;
                    $last_update = $summariserow['updated_at'];
                }

                $cost = isset($summariserow['cost_campaign']) ? $summariserow['cost_campaign'] : 0;
                $input_cost = isset($reconcialiationrow['cost_campaign']) ? $reconcialiationrow['cost_campaign'] : 0;
                $cost_disc = ($cost != 0 && $input_cost != 0) ? (($input_cost - $cost) / $cost) * 100 : (float)0;

                $mo_received = isset($summariserow['mo_received']) ? $summariserow['mo_received'] : 0;
                $input_mo_received = isset($reconcialiationrow['mo']) ? $reconcialiationrow['mo'] : 0;
                $mo_received_disc = ($mo_received != 0 && $input_mo_received != 0) ? (($input_mo_received - $mo_received) / $mo_received) * 100 : (float)0;

                $priceMO = ($mo_received != 0) ? $cost / $mo_received : 0;
                $input_priceMO = isset($reconcialiationrow['price_mo']) ? $reconcialiationrow['price_mo'] : 0;
                $priceMO_disc = ($priceMO != 0 && $input_priceMO != 0) ? (($input_priceMO - $priceMO) / $priceMO) * 100 : (float)0;

                $cost_campaign[$days['date']]['value'] = $cost;
                $cost_campaign[$days['date']]['class'] = "bg-hui";

                $input_cost_campaign[$days['date']]['value'] = $input_cost;
                $input_cost_campaign[$days['date']]['class'] = "bg-hui";

                $cost_campaign_disc[$days['date']]['value'] = $cost_disc;
                $cost_campaign_disc[$days['date']]['class'] = "bg-hui";

                $mo[$days['date']]['value'] = $mo_received;
                $mo[$days['date']]['class'] = "bg-hui";

                $input_mo[$days['date']]['value'] = $input_mo_received;
                $input_mo[$days['date']]['class'] = "bg-hui";

                $mo_disc[$days['date']]['value'] = $mo_received_disc;
                $mo_disc[$days['date']]['class'] = "bg-hui";

                $price_mo[$days['date']]['value'] = $priceMO;
                $price_mo[$days['date']]['class'] = "bg-hui";

                $input_price_mo[$days['date']]['value'] = $input_priceMO;
                $input_price_mo[$days['date']]['class'] = "bg-hui";

                $price_mo_disc[$days['date']]['value'] = $priceMO_disc;
                $price_mo_disc[$days['date']]['class'] = "bg-hui";
            }

            $last_update_show  = "Not updated last month";

            if ($last_update != "") {
                $last_update_timestamp = Carbon::parse($last_update);

                $last_update_timestamp->setTimezone('Asia/Jakarta');

                $last_update_show = $last_update_timestamp->format("Y-m-d H:i:s") . " Asia/Jakarta";
            }

            $allColumnData['cost_campaign'] = $cost_campaign;
            $allColumnData['input_cost_campaign'] = $input_cost_campaign;
            $allColumnData['cost_campaign_disc'] = $cost_campaign_disc;
            $allColumnData['mo'] = $mo;
            $allColumnData['input_mo'] = $input_mo;
            $allColumnData['mo_disc'] = $mo_disc;
            $allColumnData['price_mo'] = $price_mo;
            $allColumnData['input_price_mo'] = $input_price_mo;
            $allColumnData['price_mo_disc'] = $price_mo_disc;
            $allColumnData['last_update'] = $last_update_show;

            return $allColumnData;
        }
    }
}
