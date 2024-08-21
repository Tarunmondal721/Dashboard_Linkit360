<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Collection;
use Session;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\report_summarize;
use App\Models\Operator;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use App\Models\Company;
use App\Models\Country;
use App\Models\CompanyOperators;
use App\Models\MonthlyReportSummery;
use App\Models\PnlSummeryMonth;
use App\Models\UsersOperatorsServices;
use App\Models\User;
use App\Models\ReportsPnlsOperatorSummarizes;
use App\Models\ReportSummeriseUsers;
use App\common\Utility;
use App\common\UtilityReports;
use App\common\UtilityReportsMonthly;
use App\common\UtilityPercentage;
use App\common\UtilityAccountManager;


class MonthlyReportController extends Controller
{
    // get operator monthly report
    public function MonthlyReportOperator(Request $request)
    {
        if (\Auth::user()->can('Report Summary')) {
            $monthly = 1;
            // get filtre request
            $CountryId = $req_CountryId = $request->country;
            $CompanyId = $req_CompanyId = $request->company;
            $BusinessType = $req_BusinessType = $request->business_type;
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
                    $Operators = Operator::with('revenueshare', 'RevenushareByDate')->Status(1)->GetOperatorByOperatorId($Operators_company)->get();
                }
                $showAllOperator = false;
            }

            /*if($request->filled('country') && !$request->filled('operatorId'))
            {
                $Operators = Operator::with('revenueshare')->Status(1)->GetOperatorByCountryId($req_CountryId)->get();
                $showAllOperator = false;
            }*/

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
            // $companys = Company::get();
            $countries = array();
            $sumemry = array();

            if (!empty($Country)) {
                foreach ($Country as $CountryI) {
                    $countries[$CountryI['id']] = $CountryI;
                }
            }

            // $contains = Arr::hasAny($Country, "2");
            // $Operators = Operator::Status(1)->get();
            $arrayOperatorsIds = $Operators->pluck('id_operator')->toArray();

            // $firstDayoftheyear = Carbon::now()->startOfYear()->format('Y-m-d');
            // $start_date = $firstDayoftheyear;
            // $startColumnDateDisplay = $firstDayoftheyear;
            // $end_date = Carbon::now()->format('Y-m-d');
            // $month = Carbon::now()->format('F Y');

            /* $reports = report_summarize::filteroperator($arrayOperatorsIds)
            ->filterDateRange($start_date,$end_date)
            ->orderBy('operator_id')
            ->get()->toArray();*/


            $datesIndividual = Utility::getRangeDates($startColumnDateDisplay, $end_date);
            $no_of_months = Utility::getRangeMonthsNo($datesIndividual);

            $monthList = array();
            $FirstDayList = array();

            foreach ($no_of_months as $key => $no_of_month) {
                $firstDay = Carbon::createFromFormat('Y-m', $no_of_month['date'])->firstOfMonth()->format('Y-m-d');
                $month_key = $no_of_month['date'];
                $monthList[] = $month_key;
                $FirstDayList[] = $firstDay;
            }

            /* Admin Access All operator and Services */

            $QueryMonthlyReports = MonthlyReportSummery::filteroperator($arrayOperatorsIds)->Months($monthList);

            $user = Auth::user();

            $user_id = $user->id;



            $user_type = $user->type;
            $allowAllOperator = $user->WhowAccessAlOperator($user_type);

            if ($allowAllOperator) {

                $QueryMonthlyReports = $QueryMonthlyReports->User(0);
            } else {

                $QueryMonthlyReports = $QueryMonthlyReports->User($user_id);
            }



            $allMonthlyData = $QueryMonthlyReports->get()->toArray();

            $activeSubs = report_summarize::select('total', 'date', 'operator_id')->filteroperator($arrayOperatorsIds)->Dates($FirstDayList)->get()->toArray();

            $cost_campaign = PnlSummeryMonth::select('id_operator as operator_id', 'key', 'cost_campaign')->filteroperator($arrayOperatorsIds)->Months($monthList)->get()->toArray();

            $monthdata = $this->rearrangeOperatorMonth($allMonthlyData);
            $activesubsdata = $this->rearrangeOperatorDate($activeSubs);
            $costdata = $this->rearrangeOperatorMonth($cost_campaign);

            if (!empty($Operators)) {
                foreach ($Operators as $operator) {
                    $id_operator = $operator->id_operator;
                    // dd($id_operator);
                    $tmpOperators = array();
                    $tmpOperators['operator'] = $operator;

                    /* foreach ($no_of_months as $key => $no_of_month) {
                        $dataMonth=[];
                        $month = $no_of_month['month'];
                        $year = $no_of_month['year'];
                        $month_key =$no_of_month['date'];
                        //select * from `report_summarize` where `operator_id` = ? and month(`date`) = ? and year(`date`) = ?"
                        // $sql =  $monthdata[$month_key] = report_summarize::filteroperatorID($static_operator)->filterMonth($month)->filterYear($year)->SelectCustom()->toSql();
                        // dd($sql);
                        $monthdata[$month_key] = report_summarize::filteroperatorID($id_operator)->filterMonth($month)->filterYear($year)->SelectCustom()->get()->toArray();
                    } */

                    $tmpOperators['data'] = $monthdata;
                    //dd($tmpOperators);
                    $country_id  = $operator->country_id;
                    $contain_id = Arr::exists($countries, $country_id);
                    $OperatorCountry = array();

                    if ($contain_id) {
                        /*1 => array:9 [▼
                        "id" => 1
                        "country" => "Indonesia"
                        "country_code" => "ID"
                        "currency_code" => "IDR"
                        "currency_value" => "1"
                        "usd" => "0.000064000"
                        "flag" => "flag-indonesia.png"
                        "created_at" => null
                        "updated_at" => "2022-10-25T11:30:04.000000Z"
                        ]*/

                        $tmpOperators['country'] = $countries[$country_id];
                        $OperatorCountry = $countries[$country_id];
                    }

                    $reportsColumnData = $this->getReportsDateWise($operator, $no_of_months, $monthdata, $activesubsdata, $costdata, $OperatorCountry);

                    // dd($reportsColumnData);

                    $tmpOperators['month_string'] = $month;

                    $total_avg_t = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['tur'], $startColumnDateDisplay, $end_date, $no_of_months);

                    $tmpOperators['tur']['dates'] = $reportsColumnData['tur'];
                    $tmpOperators['tur']['total'] = $total_avg_t['sum'];
                    $tmpOperators['tur']['t_mo_end'] = $total_avg_t['T_Mo_End'];;
                    $tmpOperators['tur']['avg'] = $total_avg_t['avg'];

                    $total_avg_rev = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['t_rev'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['t_rev']['dates'] = $reportsColumnData['t_rev'];
                    $tmpOperators['t_rev']['total'] = $total_avg_rev['sum'];
                    $tmpOperators['t_rev']['t_mo_end'] = $total_avg_rev['T_Mo_End'];;
                    $tmpOperators['t_rev']['avg'] = $total_avg_rev['avg'];

                    $total_avg_trat = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['trat'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['trat']['dates'] = $reportsColumnData['trat'];
                    $tmpOperators['trat']['total'] = $total_avg_trat['sum'];
                    $tmpOperators['trat']['t_mo_end'] = $total_avg_trat['T_Mo_End'];
                    $tmpOperators['trat']['avg'] = $total_avg_trat['avg'];

                    $total_avg_turt = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['turt'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['turt']['dates'] = $reportsColumnData['turt'];
                    $tmpOperators['turt']['total'] = $total_avg_turt['sum'];
                    $tmpOperators['turt']['t_mo_end'] = $total_avg_turt['T_Mo_End'];;
                    $tmpOperators['turt']['avg'] = $total_avg_turt['avg'];;

                    $total_avg_net_rev = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['net_rev'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['net_rev']['dates'] = $reportsColumnData['net_rev'];
                    $tmpOperators['net_rev']['total'] = $total_avg_net_rev['sum'];
                    $tmpOperators['net_rev']['t_mo_end'] = $total_avg_net_rev['T_Mo_End'];
                    $tmpOperators['net_rev']['avg'] = $total_avg_net_rev['avg'];

                    $total_avg_t_sub = UtilityReportsMonthly::calculateTotalSubscribe($operator, $reportsColumnData['t_sub'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['t_sub']['dates'] = $reportsColumnData['t_sub'];
                    $tmpOperators['t_sub']['total'] = $total_avg_t_sub['sum'];
                    $tmpOperators['t_sub']['t_mo_end'] = $total_avg_t_sub['T_Mo_End'];
                    $tmpOperators['t_sub']['avg'] = $total_avg_t_sub['avg'];

                    $total_avg_t_reg = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['reg'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['reg']['dates'] = $reportsColumnData['reg'];
                    $tmpOperators['reg']['total'] = $total_avg_t_reg['sum'];
                    $tmpOperators['reg']['t_mo_end'] = $total_avg_t_reg['T_Mo_End'];
                    $tmpOperators['reg']['avg'] = $total_avg_t_reg['avg'];

                    $total_avg_t_unreg = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['unreg'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['unreg']['dates'] = $reportsColumnData['unreg'];
                    $tmpOperators['unreg']['total'] = $total_avg_t_unreg['sum'];
                    $tmpOperators['unreg']['t_mo_end'] = $total_avg_t_unreg['T_Mo_End'];;
                    $tmpOperators['unreg']['avg'] = $total_avg_t_unreg['avg'];

                    $total_avg_t_purged = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['purged'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['purged']['dates'] = $reportsColumnData['purged'];
                    $tmpOperators['purged']['total'] = $total_avg_t_purged['sum'];
                    $tmpOperators['purged']['t_mo_end'] = $total_avg_t_purged['T_Mo_End'];
                    $tmpOperators['purged']['avg'] = $total_avg_t_purged['avg'];

                    $total_avg_t_churn = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['churn'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['churn']['dates'] = $reportsColumnData['churn'];
                    $tmpOperators['churn']['total'] = $total_avg_t_churn['sum'];
                    $tmpOperators['churn']['t_mo_end'] = $total_avg_t_churn['T_Mo_End'];
                    $tmpOperators['churn']['avg'] = $total_avg_t_churn['avg'];

                    $total_avg_t_renewal = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['renewal'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['renewal']['dates'] = $reportsColumnData['renewal'];
                    $tmpOperators['renewal']['total'] = $total_avg_t_renewal['sum'];
                    $tmpOperators['renewal']['t_mo_end'] = $total_avg_t_renewal['T_Mo_End'];
                    $tmpOperators['renewal']['avg'] = $total_avg_t_renewal['avg'];

                    $total_avg_mt_success = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['daily_push_success'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['daily_push_success']['dates'] = $reportsColumnData['daily_push_success'];
                    $tmpOperators['daily_push_success']['total'] = $total_avg_mt_success['sum'];
                    $tmpOperators['daily_push_success']['t_mo_end'] = $total_avg_mt_success['T_Mo_End'];
                    $tmpOperators['daily_push_success']['avg'] = $total_avg_mt_success['avg'];

                    $total_avg_mt_failed = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['daily_push_failed'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['daily_push_failed']['dates'] = $reportsColumnData['daily_push_failed'];
                    $tmpOperators['daily_push_failed']['total'] = $total_avg_mt_failed['sum'];
                    $tmpOperators['daily_push_failed']['t_mo_end'] = $total_avg_mt_failed['T_Mo_End'];
                    $tmpOperators['daily_push_failed']['avg'] = $total_avg_mt_failed['avg'];

                    $total_avg_t_bill = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['bill'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['bill']['dates'] = $reportsColumnData['bill'];
                    $tmpOperators['bill']['total'] = $total_avg_t_bill['sum'];
                    $tmpOperators['bill']['t_mo_end'] = $total_avg_t_bill['T_Mo_End'];
                    $tmpOperators['bill']['avg'] = $total_avg_t_bill['avg'];

                    $total_avg_t_first_push = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['first_push'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['first_push']['dates'] = $reportsColumnData['first_push'];
                    $tmpOperators['first_push']['total'] = $total_avg_t_first_push['sum'];
                    $tmpOperators['first_push']['t_mo_end'] = $total_avg_t_first_push['T_Mo_End'];
                    $tmpOperators['first_push']['avg'] = $total_avg_t_first_push['avg'];

                    $total_avg_t_daily_push = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['daily_push'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['daily_push']['dates'] = $reportsColumnData['daily_push'];
                    $tmpOperators['daily_push']['total'] = $total_avg_t_daily_push['sum'];
                    $tmpOperators['daily_push']['t_mo_end'] = $total_avg_t_daily_push['T_Mo_End'];
                    $tmpOperators['daily_push']['avg'] = $total_avg_t_daily_push['avg'];

                    $total_avg_t_arpu7 = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['arpu7'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['arpu7']['dates'] = $reportsColumnData['arpu7'];
                    $tmpOperators['arpu7']['total'] = $total_avg_t_arpu7['sum'];
                    $tmpOperators['arpu7']['t_mo_end'] = $total_avg_t_arpu7['T_Mo_End'];
                    $tmpOperators['arpu7']['avg'] = $total_avg_t_arpu7['avg'];

                    $total_avg_t_usarpu7 = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['usarpu7'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['usarpu7']['dates'] = $reportsColumnData['usarpu7'];
                    $tmpOperators['usarpu7']['total'] = $total_avg_t_usarpu7['sum'];
                    $tmpOperators['usarpu7']['t_mo_end'] = $total_avg_t_usarpu7['T_Mo_End'];
                    $tmpOperators['usarpu7']['avg'] = $total_avg_t_usarpu7['avg'];

                    $total_avg_t_arpu30 = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['arpu30'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['arpu30']['dates'] = $reportsColumnData['arpu30'];
                    $tmpOperators['arpu30']['total'] = $total_avg_t_arpu30['sum'];
                    $tmpOperators['arpu30']['t_mo_end'] = $total_avg_t_arpu30['T_Mo_End'];
                    $tmpOperators['arpu30']['avg'] = $total_avg_t_arpu30['avg'];

                    $total_avg_t_usarpu30 = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['usarpu30'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['usarpu30']['dates'] = $reportsColumnData['usarpu30'];
                    $tmpOperators['usarpu30']['total'] = $total_avg_t_usarpu30['sum'];
                    $tmpOperators['usarpu30']['t_mo_end'] = $total_avg_t_usarpu30['T_Mo_End'];
                    $tmpOperators['usarpu30']['avg'] = $total_avg_t_usarpu30['avg'];

                    $total_avg_first_day_active = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['first_day_active'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['first_day_active']['dates'] = $reportsColumnData['first_day_active'];
                    $tmpOperators['first_day_active']['total'] = $total_avg_first_day_active['sum'];
                    $tmpOperators['first_day_active']['t_mo_end'] = $total_avg_first_day_active['T_Mo_End'];
                    $tmpOperators['first_day_active']['avg'] = $total_avg_first_day_active['avg'];

                    $total_avg_cost_campaign = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['cost_campaign'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['cost_campaign']['dates'] = $reportsColumnData['cost_campaign'];
                    $tmpOperators['cost_campaign']['total'] = $total_avg_cost_campaign['sum'];
                    $tmpOperators['cost_campaign']['t_mo_end'] = $total_avg_cost_campaign['T_Mo_End'];
                    $tmpOperators['cost_campaign']['avg'] = $total_avg_cost_campaign['avg'];

                    $total_avg_ltv = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['ltv'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['ltv']['dates'] = $reportsColumnData['ltv'];
                    $tmpOperators['ltv']['total'] = $total_avg_ltv['sum'];
                    $tmpOperators['ltv']['t_mo_end'] = $total_avg_ltv['T_Mo_End'];
                    $tmpOperators['ltv']['avg'] = $total_avg_ltv['avg'];

                    $tmpOperators['mt_success']['dates'] = $reportsColumnData['mt_success'];
                    $tmpOperators['mt_failed']['dates'] = $reportsColumnData['mt_failed'];

                    $tmpOperators['fmt_success']['dates'] = $reportsColumnData['fmt_success'];
                    $tmpOperators['fmt_failed']['dates'] = $reportsColumnData['fmt_failed'];

                    $tmpOperators['arpu30raw']['dates'] = $reportsColumnData['arpu30Raw'];
                    $tmpOperators['arpu30raw']['total'] = 0;
                    $tmpOperators['arpu30raw']['t_mo_end'] = 0;
                    $tmpOperators['arpu30raw']['avg'] = 0;

                    $tmpOperators['last_update'] = $reportsColumnData['last_update'];

                    $netRev = $tmpOperators['t_rev']['total'] * 0.20;
                    $subs = $tmpOperators['t_sub']['total'];
                    $reg = $tmpOperators['reg']['total'];
                    $ltv2 = ($subs != 0) ? $netRev / ($subs + $reg) : 0;
                    $tmpOperators['ltv2']['total'] = $ltv2;

                    $tmpOperators = UtilityReportsMonthly::ColorFirstDay($tmpOperators, "tur");
                    $tmpOperators = UtilityReportsMonthly::ColorFirstDay($tmpOperators, "turt");
                    $tmpOperators = UtilityReportsMonthly::ColorFirstDay($tmpOperators, "net_rev");
                    $tmpOperators = UtilityReportsMonthly::ColorFirstDay($tmpOperators, "t_rev");
                    $tmpOperators = UtilityReportsMonthly::ColorFirstDay($tmpOperators, "trat");
                    $tmpOperators = UtilityReportsMonthly::ColorFirstDay($tmpOperators, "reg");
                    $tmpOperators = UtilityReportsMonthly::ColorFirstDay($tmpOperators, "unreg");
                    $tmpOperators = UtilityReportsMonthly::ColorFirstDay($tmpOperators, "purged");
                    $tmpOperators = UtilityReportsMonthly::ColorFirstDay($tmpOperators, "churn");
                    $tmpOperators = UtilityReportsMonthly::ColorFirstDay($tmpOperators, "renewal");
                    $tmpOperators = UtilityReportsMonthly::ColorFirstDay($tmpOperators, "daily_push_success");
                    $tmpOperators = UtilityReportsMonthly::ColorFirstDay($tmpOperators, "daily_push_failed");
                    $tmpOperators = UtilityReportsMonthly::ColorFirstDay($tmpOperators, "bill");
                    $tmpOperators = UtilityReportsMonthly::ColorFirstDay($tmpOperators, "first_push");
                    $tmpOperators = UtilityReportsMonthly::ColorFirstDay($tmpOperators, "daily_push");
                    $tmpOperators = UtilityReportsMonthly::ColorFirstDay($tmpOperators, "arpu7");
                    $tmpOperators = UtilityReportsMonthly::ColorFirstDay($tmpOperators, "usarpu7");
                    $tmpOperators = UtilityReportsMonthly::ColorFirstDay($tmpOperators, "arpu30");
                    $tmpOperators = UtilityReportsMonthly::ColorFirstDay($tmpOperators, "usarpu30");
                    $tmpOperators = UtilityReportsMonthly::ColorFirstDay($tmpOperators, "ltv");


                    $sumemry[] = $tmpOperators;
                }
            }

            $allsummaryData = UtilityReportsMonthly::monthly_all_summary_data($sumemry);
            $allsummaryData = UtilityReportsMonthly::summeryPercentageCalculation($allsummaryData);
            $no_of_days = $no_of_months; // Both are same for display view section
            // dd($sumemry);
            // dd(12345);
            // allSummeryPercentageCalculation
            // dd($all_summary_Data);
            // dd($allsummaryData);
            // dd(compact('sumemry','all_summary_Data','allsummaryData'));

            return view('report.monthly_report', compact('sumemry', 'companys', 'no_of_days', 'allsummaryData', 'monthly'));
        } else {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    // get country monthly report
    public function MonthlyReportCountry(Request $request)
    {
        if (\Auth::user()->can('Report Summary')) {
            $CountryWise = 1;
            $monthly = 1;
            $sumemry = array();
            $monthList = array();
            $countries = array();
            $Operators_company = array();
            $showAllOperator = true;
            $countrys = [];
            $country_ids = [];
            $country_operator = [];

            // get filtre request
            $CountryId = $req_CountryId = $request->country;
            $CompanyId = $req_CompanyId = $request->company;
            $BusinessType = $req_BusinessType = $request->business_type;
            $filterOperator = $req_filterOperator = $request->operatorId;
            $Start_date = $req_Start_date = $request->to;
            $end_date =  $req_end_date = trim($request->from);

            /*If from is less than to*/
            if ($end_date <= $Start_date) {
                $Start_date = $req_Start_date = trim($request->from);
                $end_date =  $req_end_date = $request->to;
            }

            $firstDayoftheyear = Carbon::now()->startOfYear()->format('Y-m-d');
            // $firstDayoftheyear = Carbon::now()->startOfMonth()->subMonths(24)->format('Y-m-d');
            $start_date = $firstDayoftheyear;
            $startColumnDateDisplay = $firstDayoftheyear;
            $end_date = Carbon::now()->format('Y-m-d');
            $month = Carbon::now()->format('F Y');
            $year = Carbon::now()->format('Y');


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
                    $Operators = Operator::with('revenueshare', 'RevenushareByDate')->Status(1)->GetOperatorByOperatorId($Operators_company)->get();
                }
                $showAllOperator = false;
            }

            /*if($request->filled('country') && !$request->filled('operatorId'))
            {
                $Operators = Operator::with('revenueshare')->Status(1)->GetOperatorByCountryId($req_CountryId)->get();
                $showAllOperator = false;
            }*/
            if ($request->filled('company') && $request->filled('country') && !$request->filled('operatorId')) {
                $DataArr = [
                    'id' => $req_CountryId,
                    'company' => $req_CompanyId,
                ];
                $requestobj = new Request($DataArr);
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
            // $arrayOperatorsIds = $Operators->pluck('id_operator')->toArray();
            // $companys = Company::get();

            // $firstDayoftheyear = Carbon::now()->startOfYear()->format('Y-m-d');
            // $start_date = $firstDayoftheyear;
            // $startColumnDateDisplay = $firstDayoftheyear;
            // $end_date = Carbon::now()->format('Y-m-d');
            // $month = Carbon::now()->format('F Y');
            $year = Carbon::now()->format('Y');

            // $reports = report_summarize::filteroperator($static_operator)
            // ->filterDateRange($start_date,$end_date)
            // ->orderBy('operator_id')
            // ->get()->toArray();
            // $reportsByIDs = $this->getReportsOperatorID($reports);

            $datesIndividual = Utility::getRangeDates($startColumnDateDisplay, $end_date);
            $no_of_months = Utility::getRangeMonthsNo($datesIndividual);

            $monthList = array();
            $FirstDayList = array();

            foreach ($no_of_months as $key => $no_of_month) {
                $firstDay = Carbon::createFromFormat('Y-m', $no_of_month['date'])->firstOfMonth()->format('Y-m-d');
                $month_key = $no_of_month['date'];
                $monthList[] = $month_key;
                $FirstDayList[] = $firstDay;
            }

            $Country = Country::all();
            if (!empty($Country)) {
                foreach ($Country as $CountryI) {
                    $countries[$CountryI['id']] = $CountryI;
                }
            }

            //  $Operators = Operator::Status(1)->get();
            $arrayOperatorsIds = $Operators->pluck('id_operator')->toArray();
            // dd($arrayOperatorsIds);

            /* Admin Access All operator and Services */

            $QueryMonthlyReports = MonthlyReportSummery::filteroperator($arrayOperatorsIds)
                ->Months($monthList);

            $user = Auth::user();

            $user_id = $user->id;



            $user_type = $user->type;
            $allowAllOperator = $user->WhowAccessAlOperator($user_type);

            if ($allowAllOperator) {

                $QueryMonthlyReports = $QueryMonthlyReports->User(0);
            } else {

                $QueryMonthlyReports = $QueryMonthlyReports->User($user_id);
            }

            $allMonthlyData = $QueryMonthlyReports->get()->toArray();

            /* END */

            $activeSubs = report_summarize::select('total', 'date', 'operator_id')->filteroperator($arrayOperatorsIds)->Dates($FirstDayList)->get()->toArray();

            $cost_campaign = PnlSummeryMonth::select('id_operator as operator_id', 'key', 'cost_campaign')->filteroperator($arrayOperatorsIds)->Months($monthList)->get()->toArray();

            $monthdata = $this->rearrangeOperatorMonth($allMonthlyData);
            $activesubsdata = $this->rearrangeOperatorDate($activeSubs);
            $costdata = $this->rearrangeOperatorMonth($cost_campaign);


            if (!empty($Operators)) {
                $io = 0;
                foreach ($Operators as $operator) {
                    if ($operator->status == 0) continue;

                    $id_operator = $operator->id_operator;
                    if (!in_array($id_operator, $arrayOperatorsIds)) {
                        continue;
                    }

                    $tmpOperators = array();
                    $tmpOperators['operator'] = $operator;

                    $io++;

                    //dd($monthdata);

                    $tmpOperators['data'] = $monthdata;
                    //dd($tmpOperators);
                    $country_id  = $operator->country_id;
                    $contain_id = Arr::exists($countries, $country_id);
                    $OperatorCountry = array();
                    if ($contain_id) {
                        $tmpOperators['country'] = $countries[$country_id];
                        $OperatorCountry = $countries[$country_id];
                    }

                    $reportsColumnData = $this->getReportsDateWise($operator, $no_of_months, $monthdata, $activesubsdata, $costdata, $OperatorCountry);

                    $tmpOperators['month_string'] = $month;

                    $total_avg_t = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['tur'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['tur']['dates'] = $reportsColumnData['tur'];
                    $tmpOperators['tur']['total'] = $total_avg_t['sum'];
                    $tmpOperators['tur']['t_mo_end'] = $total_avg_t['T_Mo_End'];;
                    $tmpOperators['tur']['avg'] = $total_avg_t['avg'];

                    $total_avg_rev = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['t_rev'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['t_rev']['dates'] = $reportsColumnData['t_rev'];
                    $tmpOperators['t_rev']['total'] = $total_avg_rev['sum'];
                    $tmpOperators['t_rev']['t_mo_end'] = $total_avg_rev['T_Mo_End'];;
                    $tmpOperators['t_rev']['avg'] = $total_avg_rev['avg'];

                    $total_avg_trat = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['trat'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['trat']['dates'] = $reportsColumnData['trat'];
                    $tmpOperators['trat']['total'] = $total_avg_trat['sum'];
                    $tmpOperators['trat']['t_mo_end'] = $total_avg_trat['T_Mo_End'];
                    $tmpOperators['trat']['avg'] = $total_avg_trat['avg'];

                    $total_avg_turt = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['turt'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['turt']['dates'] = $reportsColumnData['turt'];
                    $tmpOperators['turt']['total'] = $total_avg_turt['sum'];
                    $tmpOperators['turt']['t_mo_end'] = $total_avg_turt['T_Mo_End'];;
                    $tmpOperators['turt']['avg'] = $total_avg_turt['avg'];;

                    $total_avg_net_rev = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['net_rev'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['net_rev']['dates'] = $reportsColumnData['net_rev'];
                    $tmpOperators['net_rev']['total'] = $total_avg_net_rev['sum'];
                    $tmpOperators['net_rev']['t_mo_end'] = $total_avg_net_rev['T_Mo_End'];
                    $tmpOperators['net_rev']['avg'] = $total_avg_net_rev['avg'];

                    $total_avg_t_sub = UtilityReportsMonthly::calculateTotalSubscribe($operator, $reportsColumnData['t_sub'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['t_sub']['dates'] = $reportsColumnData['t_sub'];
                    $tmpOperators['t_sub']['total'] = $total_avg_t_sub['sum'];
                    $tmpOperators['t_sub']['t_mo_end'] = $total_avg_t_sub['T_Mo_End'];
                    $tmpOperators['t_sub']['avg'] = $total_avg_t_sub['avg'];

                    $total_avg_t_reg = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['reg'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['reg']['dates'] = $reportsColumnData['reg'];
                    $tmpOperators['reg']['total'] = $total_avg_t_reg['sum'];
                    $tmpOperators['reg']['t_mo_end'] = $total_avg_t_reg['T_Mo_End'];
                    $tmpOperators['reg']['avg'] = $total_avg_t_reg['avg'];

                    $total_avg_t_unreg = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['unreg'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['unreg']['dates'] = $reportsColumnData['unreg'];
                    $tmpOperators['unreg']['total'] = $total_avg_t_unreg['sum'];
                    $tmpOperators['unreg']['t_mo_end'] = $total_avg_t_unreg['T_Mo_End'];;
                    $tmpOperators['unreg']['avg'] = $total_avg_t_unreg['avg'];

                    $total_avg_t_purged = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['purged'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['purged']['dates'] = $reportsColumnData['purged'];
                    $tmpOperators['purged']['total'] = $total_avg_t_purged['sum'];
                    $tmpOperators['purged']['t_mo_end'] = $total_avg_t_purged['T_Mo_End'];
                    $tmpOperators['purged']['avg'] = $total_avg_t_purged['avg'];

                    $total_avg_t_churn = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['churn'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['churn']['dates'] = $reportsColumnData['churn'];
                    $tmpOperators['churn']['total'] = $total_avg_t_churn['sum'];
                    $tmpOperators['churn']['t_mo_end'] = $total_avg_t_churn['T_Mo_End'];
                    $tmpOperators['churn']['avg'] = $total_avg_t_churn['avg'];

                    $total_avg_t_renewal = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['renewal'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['renewal']['dates'] = $reportsColumnData['renewal'];
                    $tmpOperators['renewal']['total'] = $total_avg_t_renewal['sum'];
                    $tmpOperators['renewal']['t_mo_end'] = $total_avg_t_renewal['T_Mo_End'];
                    $tmpOperators['renewal']['avg'] = $total_avg_t_renewal['avg'];

                    $total_avg_mt_success = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['daily_push_success'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['daily_push_success']['dates'] = $reportsColumnData['daily_push_success'];
                    $tmpOperators['daily_push_success']['total'] = $total_avg_mt_success['sum'];
                    $tmpOperators['daily_push_success']['t_mo_end'] = $total_avg_mt_success['T_Mo_End'];
                    $tmpOperators['daily_push_success']['avg'] = $total_avg_mt_success['avg'];

                    $total_avg_mt_failed = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['daily_push_failed'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['daily_push_failed']['dates'] = $reportsColumnData['daily_push_failed'];
                    $tmpOperators['daily_push_failed']['total'] = $total_avg_mt_failed['sum'];
                    $tmpOperators['daily_push_failed']['t_mo_end'] = $total_avg_mt_failed['T_Mo_End'];
                    $tmpOperators['daily_push_failed']['avg'] = $total_avg_mt_failed['avg'];

                    $total_avg_t_bill = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['bill'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['bill']['dates'] = $reportsColumnData['bill'];
                    $tmpOperators['bill']['total'] = $total_avg_t_bill['sum'];
                    $tmpOperators['bill']['t_mo_end'] = $total_avg_t_bill['T_Mo_End'];
                    $tmpOperators['bill']['avg'] = $total_avg_t_bill['avg'];

                    $total_avg_t_first_push = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['first_push'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['first_push']['dates'] = $reportsColumnData['first_push'];
                    $tmpOperators['first_push']['total'] = $total_avg_t_first_push['sum'];
                    $tmpOperators['first_push']['t_mo_end'] = $total_avg_t_first_push['T_Mo_End'];
                    $tmpOperators['first_push']['avg'] = $total_avg_t_first_push['avg'];

                    $total_avg_t_daily_push = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['daily_push'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['daily_push']['dates'] = $reportsColumnData['daily_push'];
                    $tmpOperators['daily_push']['total'] = $total_avg_t_daily_push['sum'];
                    $tmpOperators['daily_push']['t_mo_end'] = $total_avg_t_daily_push['T_Mo_End'];
                    $tmpOperators['daily_push']['avg'] = $total_avg_t_daily_push['avg'];

                    $total_avg_t_arpu7 = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['arpu7'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['arpu7']['dates'] = $reportsColumnData['arpu7'];
                    $tmpOperators['arpu7']['total'] = $total_avg_t_arpu7['sum'];
                    $tmpOperators['arpu7']['t_mo_end'] = $total_avg_t_arpu7['T_Mo_End'];
                    $tmpOperators['arpu7']['avg'] = $total_avg_t_arpu7['avg'];

                    $total_avg_t_usarpu7 = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['usarpu7'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['usarpu7']['dates'] = $reportsColumnData['usarpu7'];
                    $tmpOperators['usarpu7']['total'] = $total_avg_t_usarpu7['sum'];
                    $tmpOperators['usarpu7']['t_mo_end'] = $total_avg_t_usarpu7['T_Mo_End'];
                    $tmpOperators['usarpu7']['avg'] = $total_avg_t_usarpu7['avg'];

                    $total_avg_t_arpu30 = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['arpu30'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['arpu30']['dates'] = $reportsColumnData['arpu30'];
                    $tmpOperators['arpu30']['total'] = $total_avg_t_arpu30['sum'];
                    $tmpOperators['arpu30']['t_mo_end'] = $total_avg_t_arpu30['T_Mo_End'];
                    $tmpOperators['arpu30']['avg'] = $total_avg_t_arpu30['avg'];

                    $total_avg_t_usarpu30 = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['usarpu30'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['usarpu30']['dates'] = $reportsColumnData['usarpu30'];
                    $tmpOperators['usarpu30']['total'] = $total_avg_t_usarpu30['sum'];
                    $tmpOperators['usarpu30']['t_mo_end'] = $total_avg_t_usarpu30['T_Mo_End'];
                    $tmpOperators['usarpu30']['avg'] = $total_avg_t_usarpu30['avg'];

                    $total_avg_first_day_active = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['first_day_active'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['first_day_active']['dates'] = $reportsColumnData['first_day_active'];
                    $tmpOperators['first_day_active']['total'] = $total_avg_first_day_active['sum'];
                    $tmpOperators['first_day_active']['t_mo_end'] = $total_avg_first_day_active['T_Mo_End'];
                    $tmpOperators['first_day_active']['avg'] = $total_avg_first_day_active['avg'];

                    $total_avg_cost_campaign = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['cost_campaign'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['cost_campaign']['dates'] = $reportsColumnData['cost_campaign'];
                    $tmpOperators['cost_campaign']['total'] = $total_avg_cost_campaign['sum'];
                    $tmpOperators['cost_campaign']['t_mo_end'] = $total_avg_cost_campaign['T_Mo_End'];
                    $tmpOperators['cost_campaign']['avg'] = $total_avg_cost_campaign['avg'];

                    $total_avg_ltv = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['ltv'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['ltv']['dates'] = $reportsColumnData['ltv'];
                    $tmpOperators['ltv']['total'] = $total_avg_ltv['sum'];
                    $tmpOperators['ltv']['t_mo_end'] = $total_avg_ltv['T_Mo_End'];
                    $tmpOperators['ltv']['avg'] = $total_avg_ltv['avg'];

                    $tmpOperators['mt_success']['dates'] = $reportsColumnData['mt_success'];
                    $tmpOperators['mt_failed']['dates'] = $reportsColumnData['mt_failed'];

                    $tmpOperators['fmt_success']['dates'] = $reportsColumnData['fmt_success'];
                    $tmpOperators['fmt_failed']['dates'] = $reportsColumnData['fmt_failed'];

                    $tmpOperators['last_update'] = $reportsColumnData['last_update'];

                    $sumemry[] = $tmpOperators;
                }
            }

            //dd($sumemry);

            // Country Sum from Operator array
            $displayCountries = array();
            $SelectedCountries = array();
            $RowCountryData = array();

            if (!empty($sumemry)) {
                foreach ($sumemry as $key => $sumemries) {
                    // dd($sumemries);
                    $country_id = $sumemries['country']['id'];
                    $SelectedCountries[$country_id] = $sumemries['country'];
                    $displayCountries[$country_id][] = $sumemries;
                }
            }

            // dd($displayCountries);

            if (!empty($SelectedCountries)) {
                foreach ($SelectedCountries as $key => $SelectedCountry) {
                    $tempDataArr = array();
                    $country_id = $SelectedCountry['id'];
                    $dataRowSum = UtilityReportsMonthly::CountrySumOperator($displayCountries[$country_id]);
                    // dd($dataRowSum);
                    $dataRowSum = UtilityReportsMonthly::summeryPercentageCalculation($dataRowSum);
                    $tempDataArr['country'] = $SelectedCountry;
                    $tempDataArr['month_string'] = $month;
                    $tempDataArr['year'] = $year;
                    $tempDataArr = array_merge($tempDataArr, $dataRowSum);

                    $netRev = $tempDataArr['t_rev']['total'] * 0.20;
                    $subs = $tempDataArr['t_sub']['total'];
                    $reg = $tempDataArr['reg']['total'];
                    $ltv2 = ($subs != 0) ? $netRev / ($subs + $reg) : 0;
                    $tempDataArr['ltv2']['total'] = $ltv2;

                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "tur");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "turt");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "net_rev");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "t_rev");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "t_sub");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "trat");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "reg");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "unreg");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "purged");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "churn");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "renewal");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "daily_push_success");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "daily_push_failed");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "bill");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "first_push");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "daily_push");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "arpu7");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "usarpu7");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "arpu30");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "usarpu30");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "ltv");


                    $RowCountryData[] = $tempDataArr;
                }
            }

            $sumemry = $RowCountryData;
            $allsummaryData = UtilityReportsMonthly::monthly_all_summary_data($sumemry);
            $allsummaryData = UtilityReportsMonthly::summeryPercentageCalculation($allsummaryData);

            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "tur");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "turt");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "t_rev");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "t_sub");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "trat");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "reg");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "unreg");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "purged");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "churn");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "renewal");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "daily_push_success");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "daily_push_failed");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "bill");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "first_push");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "daily_push");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "arpu7");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "usarpu7");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "arpu30");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "usarpu30");
            $no_of_days = $no_of_months;

            // dd($sumemry);
            // dd($no_of_days);
            // dd($allsummaryData);
            // dd(compact('sumemry','allsummaryData'));

            return view('report.monthly_country_report', compact('sumemry', 'no_of_days', 'allsummaryData', 'monthly', 'CountryWise'));
        } else {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    // get manager monthly report
    public function MonthlyReportManager_OLD(Request $request)
    {
        if (\Auth::user()->can('Report Summary')) {
            $AccountManagerWise = 1;
            $monthly = 1;
            $sumemry = array();
            $monthList = array();
            $countries = array();
            $Operators_company = array();
            $showAllOperator = true;
            $countrys = [];
            $country_ids = [];
            $country_operator = [];

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

            $firstDayoftheyear = Carbon::now()->startOfYear()->format('Y-m-d');
            $start_date = $firstDayoftheyear;
            $startColumnDateDisplay = $firstDayoftheyear;
            $end_date = Carbon::now()->format('Y-m-d');
            $month = Carbon::now()->format('F Y');
            $year = Carbon::now()->format('Y');


            if ($request->filled('to') && $request->filled('from')) {
                $display_date_input = new Carbon($req_Start_date);
                $start_date = $req_Start_date;
                $startColumnDateDisplay = $display_date_input->format('Y-m-d');
                $end_date = $req_end_date;
            } else {
                $req_Start_date = date('Y-m-d', strtotime('-30 days'));
                $req_end_date = date('Y-m-d');

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
                    $Operators = Operator::with('revenueshare', 'RevenushareByDate')->Status(1)->GetOperatorByOperatorId($Operators_company)->get();
                }
                $showAllOperator = false;
            }

            if ($request->filled('country') && !$request->filled('operatorId')) {
                $Operators = Operator::with('revenueshare', 'RevenushareByDate')->Status(1)->GetOperatorByCountryId($req_CountryId)->get();
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

            $arrayOperatorsIds = $Operators->pluck('id_operator')->toArray();
            $datesIndividual = Utility::getRangeDates($startColumnDateDisplay, $end_date);
            $no_of_months = Utility::getRangeMonthsNo($datesIndividual);

            foreach ($no_of_months as $key => $no_of_month) {
                $month_key = $no_of_month['date'];
                $monthList[] = $month_key;
            }

            $QueryMonthlyReports = MonthlyReportSummery::filteroperator($arrayOperatorsIds)
                ->Months($monthList);

            $Country = Country::all();
            if (!empty($Country)) {
                foreach ($Country as $CountryI) {
                    $countries[$CountryI['id']] = $CountryI;
                }
            }

            $Users = User::all()->toArray();
            $users = array();
            // dd($Users);
            if (!empty($Users)) {
                foreach ($Users as $userI) {
                    if ($userI['type'] == "Account Manager")
                        $users[$userI['id']] = $userI;
                }
            }

            // dd($users);

            $UserOperators = UsersOperatorsServices::all()->toArray();
            $user_operators = array();

            if (!empty($UserOperators)) {
                foreach ($UserOperators as $key => $User_operator) {
                    $operator_id = $User_operator['id_operator'];
                    $User_id = $User_operator['user_id'];
                    if (!isset($users[$User_id])) {
                        // if The Operator not founds in that array
                        continue;
                    }
                    $user_operators[$operator_id] = $users[$User_id];
                }
            }

            $user = Auth::user();
            $user_id = $user->id;
            $user_type = $user->type;
            $allowAllOperator = $user->WhowAccessAlOperator($user_type);
            if ($allowAllOperator) {
                $QueryMonthlyReports = $QueryMonthlyReports->User(0);
            } else {
                $QueryMonthlyReports = $QueryMonthlyReports->User($user_id);
            }

            $allMonthlyData = $QueryMonthlyReports->get()->toArray();
            $monthdata = $this->rearrangeOperatorMonth($allMonthlyData);

            if (!empty($Operators)) {
                $io = 0;
                foreach ($Operators as $operator) {
                    if ($operator->status == 0) continue;

                    $id_operator = $operator->id_operator;
                    if (!in_array($id_operator, $arrayOperatorsIds)) {
                        continue;
                    }

                    $tmpOperators = array();
                    $tmpOperators['operator'] = $operator;
                    $io++;
                    //dd($monthdata);
                    $tmpOperators['data'] = $monthdata;
                    $country_id  = $operator->country_id;
                    $contain_id = Arr::exists($countries, $country_id);
                    $OperatorCountry = array();
                    if ($contain_id) {
                        $tmpOperators['country'] = $countries[$country_id];
                        $OperatorCountry = $countries[$country_id];
                    }
                    if (!isset($user_operators[$id_operator])) {
                        // if The Operator not founds in that array
                        continue;
                    }

                    $tmpOperators['account_manager'] = $user_operators[$id_operator];
                    // dd($tmpOperators);
                    $reportsColumnData = $this->getReportsDateWise($operator, $no_of_months, $monthdata, $OperatorCountry);
                    //dd($reportsColumnData);

                    $tmpOperators['month_string'] = $month;

                    $total_avg_t = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['tur'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['tur']['dates'] = $reportsColumnData['tur'];
                    $tmpOperators['tur']['total'] = $total_avg_t['sum'];
                    $tmpOperators['tur']['t_mo_end'] = $total_avg_t['T_Mo_End'];;
                    $tmpOperators['tur']['avg'] = $total_avg_t['avg'];

                    $total_avg_rev = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['t_rev'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['t_rev']['dates'] = $reportsColumnData['t_rev'];
                    $tmpOperators['t_rev']['total'] = $total_avg_rev['sum'];
                    $tmpOperators['t_rev']['t_mo_end'] = $total_avg_rev['T_Mo_End'];;
                    $tmpOperators['t_rev']['avg'] = $total_avg_rev['avg'];

                    $total_avg_trat = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['trat'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['trat']['dates'] = $reportsColumnData['trat'];
                    $tmpOperators['trat']['total'] = $total_avg_trat['sum'];
                    $tmpOperators['trat']['t_mo_end'] = $total_avg_trat['T_Mo_End'];
                    $tmpOperators['trat']['avg'] = $total_avg_trat['avg'];

                    $total_avg_turt = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['turt'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['turt']['dates'] = $reportsColumnData['turt'];
                    $tmpOperators['turt']['total'] = $total_avg_turt['sum'];
                    $tmpOperators['turt']['t_mo_end'] = $total_avg_turt['T_Mo_End'];;
                    $tmpOperators['turt']['avg'] = $total_avg_turt['avg'];;

                    $total_avg_t_sub = UtilityReportsMonthly::calculateTotalSubscribe($operator, $reportsColumnData['t_sub'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['t_sub']['dates'] = $reportsColumnData['t_sub'];
                    $tmpOperators['t_sub']['total'] = $total_avg_t_sub['sum'];
                    $tmpOperators['t_sub']['t_mo_end'] = $total_avg_t_sub['T_Mo_End'];
                    $tmpOperators['t_sub']['avg'] = $total_avg_t_sub['avg'];

                    $total_avg_t_reg = UtilityReportsMonthly::calculateTotalAVG($operator, $reportsColumnData['reg'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['reg']['dates'] = $reportsColumnData['reg'];
                    $tmpOperators['reg']['total'] = $total_avg_t_reg['sum'];
                    $tmpOperators['reg']['t_mo_end'] = $total_avg_t_reg['T_Mo_End'];
                    $tmpOperators['reg']['avg'] = $total_avg_t_reg['avg'];

                    $total_avg_t_unreg = UtilityReportsMonthly::calculateTotalAVG($operator, $reportsColumnData['unreg'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['unreg']['dates'] = $reportsColumnData['unreg'];
                    $tmpOperators['unreg']['total'] = $total_avg_t_unreg['sum'];
                    $tmpOperators['unreg']['t_mo_end'] = $total_avg_t_unreg['T_Mo_End'];;
                    $tmpOperators['unreg']['avg'] = $total_avg_t_unreg['avg'];

                    $total_avg_t_purged = UtilityReportsMonthly::calculateTotalAVG($operator, $reportsColumnData['purged'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['purged']['dates'] = $reportsColumnData['purged'];
                    $tmpOperators['purged']['total'] = $total_avg_t_purged['sum'];
                    $tmpOperators['purged']['t_mo_end'] = $total_avg_t_purged['T_Mo_End'];
                    $tmpOperators['purged']['avg'] = $total_avg_t_purged['avg'];

                    $total_avg_t_churn = UtilityReportsMonthly::calculateTotalAVG($operator, $reportsColumnData['churn'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['churn']['dates'] = $reportsColumnData['churn'];
                    $tmpOperators['churn']['total'] = $total_avg_t_churn['sum'];
                    $tmpOperators['churn']['t_mo_end'] = $total_avg_t_churn['T_Mo_End'];
                    $tmpOperators['churn']['avg'] = $total_avg_t_churn['avg'];

                    $total_avg_t_renewal = UtilityReportsMonthly::calculateTotalAVG($operator, $reportsColumnData['renewal'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['renewal']['dates'] = $reportsColumnData['renewal'];
                    $tmpOperators['renewal']['total'] = $total_avg_t_renewal['sum'];
                    $tmpOperators['renewal']['t_mo_end'] = $total_avg_t_renewal['T_Mo_End'];
                    $tmpOperators['renewal']['avg'] = $total_avg_t_renewal['avg'];

                    $total_avg_t_bill = UtilityReportsMonthly::calculateTotalAVG($operator, $reportsColumnData['bill'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['bill']['dates'] = $reportsColumnData['bill'];
                    $tmpOperators['bill']['total'] = $total_avg_t_bill['sum'];
                    $tmpOperators['bill']['t_mo_end'] = $total_avg_t_bill['T_Mo_End'];
                    $tmpOperators['bill']['avg'] = $total_avg_t_bill['avg'];

                    $total_avg_t_first_push = UtilityReportsMonthly::calculateTotalAVG($operator, $reportsColumnData['first_push'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['first_push']['dates'] = $reportsColumnData['first_push'];
                    $tmpOperators['first_push']['total'] = $total_avg_t_first_push['sum'];
                    $tmpOperators['first_push']['t_mo_end'] = $total_avg_t_first_push['T_Mo_End'];
                    $tmpOperators['first_push']['avg'] = $total_avg_t_first_push['avg'];

                    $total_avg_t_daily_push = UtilityReportsMonthly::calculateTotalAVG($operator, $reportsColumnData['daily_push'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['daily_push']['dates'] = $reportsColumnData['daily_push'];
                    $tmpOperators['daily_push']['total'] = $total_avg_t_daily_push['sum'];
                    $tmpOperators['daily_push']['t_mo_end'] = $total_avg_t_daily_push['T_Mo_End'];
                    $tmpOperators['daily_push']['avg'] = $total_avg_t_daily_push['avg'];

                    $total_avg_t_arpu7 = UtilityReportsMonthly::calculateTotalAVG($operator, $reportsColumnData['arpu7'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['arpu7']['dates'] = $reportsColumnData['arpu7'];
                    $tmpOperators['arpu7']['total'] = $total_avg_t_arpu7['sum'];
                    $tmpOperators['arpu7']['t_mo_end'] = $total_avg_t_arpu7['T_Mo_End'];
                    $tmpOperators['arpu7']['avg'] = $total_avg_t_arpu7['avg'];

                    $total_avg_t_usarpu7 = UtilityReportsMonthly::calculateTotalAVG($operator, $reportsColumnData['usarpu7'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['usarpu7']['dates'] = $reportsColumnData['usarpu7'];
                    $tmpOperators['usarpu7']['total'] = $total_avg_t_usarpu7['sum'];
                    $tmpOperators['usarpu7']['t_mo_end'] = $total_avg_t_usarpu7['T_Mo_End'];
                    $tmpOperators['usarpu7']['avg'] = $total_avg_t_usarpu7['avg'];

                    $total_avg_t_arpu30 = UtilityReportsMonthly::calculateTotalAVG($operator, $reportsColumnData['arpu30'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['arpu30']['dates'] = $reportsColumnData['arpu30'];
                    $tmpOperators['arpu30']['total'] = $total_avg_t_arpu30['sum'];
                    $tmpOperators['arpu30']['t_mo_end'] = $total_avg_t_arpu30['T_Mo_End'];
                    $tmpOperators['arpu30']['avg'] = $total_avg_t_arpu30['avg'];

                    $total_avg_t_usarpu30 = UtilityReportsMonthly::calculateTotalAVG($operator, $reportsColumnData['arpu30'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['usarpu30']['dates'] = $reportsColumnData['usarpu30'];
                    $tmpOperators['usarpu30']['total'] = $total_avg_t_usarpu30['sum'];
                    $tmpOperators['usarpu30']['t_mo_end'] = $total_avg_t_usarpu30['T_Mo_End'];
                    $tmpOperators['usarpu30']['avg'] = $total_avg_t_usarpu30['avg'];

                    $tmpOperators['mt_success']['dates'] = $reportsColumnData['mt_success'];
                    $tmpOperators['mt_failed']['dates'] = $reportsColumnData['mt_failed'];

                    $tmpOperators['fmt_success']['dates'] = $reportsColumnData['fmt_success'];
                    $tmpOperators['fmt_failed']['dates'] = $reportsColumnData['fmt_failed'];
                    $tmpOperators['last_update'] = $reportsColumnData['last_update'];

                    $sumemry[] = $tmpOperators;
                }
            }


            // Account Manager Sum from Operator array

            $displayAccountManagers = array();
            $SelectedAccountManagers = array();
            $RowUserData = array();

            if (!empty($sumemry)) {
                foreach ($sumemry as $key => $sumemries) {
                    // dd($sumemries);
                    $user_id = $sumemries['account_manager']['id'];
                    $SelectedAccountManagers[$user_id] = $sumemries['account_manager'];
                    $displayAccountManagers[$user_id][] = $sumemries;
                }
            }


            if (!empty($SelectedAccountManagers)) {
                foreach ($SelectedAccountManagers as $key => $SelectedAccountManager) {
                    $tempDataArr = array();
                    $manager_id = $SelectedAccountManager['id'];
                    $dataRowSum = UtilityReportsMonthly::CountrySumOperator($displayAccountManagers[$manager_id]);
                    // dd($dataRowSum);
                    $dataRowSum = UtilityReportsMonthly::summeryPercentageCalculation($dataRowSum);
                    $tempDataArr['account_manager'] = $SelectedAccountManager;
                    $tempDataArr['month_string'] = $month;
                    $tempDataArr['year'] = $year;
                    $tempDataArr = array_merge($tempDataArr, $dataRowSum);

                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "tur");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "turt");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "t_rev");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "t_sub");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "trat");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "reg");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "unreg");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "purged");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "churn");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "renewal");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "bill");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "first_push");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "daily_push");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "arpu7");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "usarpu7");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "arpu30");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "usarpu30");
                    $RowUserData[] = $tempDataArr;
                }
            }

            $sumemry = $RowUserData;
            $allsummaryData = UtilityReportsMonthly::monthly_all_summary_data($sumemry);
            $allsummaryData = UtilityReportsMonthly::summeryPercentageCalculation($allsummaryData);

            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "tur");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "turt");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "t_rev");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "t_sub");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "trat");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "reg");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "unreg");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "purged");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "churn");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "renewal");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "bill");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "first_push");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "daily_push");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "arpu7");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "usarpu7");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "arpu30");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "usarpu30");
            $no_of_days = $no_of_months;

            return view('report.monthly_manager_report', compact('sumemry', 'no_of_days', 'allsummaryData', 'monthly', 'AccountManagerWise'));
        } else {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    // get manager monthly report
    public function MonthlyReportManager(Request $request)
    {
        if (\Auth::user()->can('Report Summary')) {
            $AccountManagerWise = 1;
            $monthly = 1;
            $sumemry = array();
            $monthList = array();
            $countries = array();
            $Operators_company = array();
            $showAllOperator = true;
            $countrys = [];
            $country_ids = [];
            $country_operator = [];

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

            $firstDayoftheyear = Carbon::now()->startOfYear()->format('Y-m-d');
            $start_date = $firstDayoftheyear;
            $startColumnDateDisplay = $firstDayoftheyear;
            $end_date = Carbon::now()->format('Y-m-d');
            $month = Carbon::now()->format('F Y');
            $year = Carbon::now()->format('Y');


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
                    $Operators = Operator::with('revenueshare', 'RevenushareByDate')->Status(1)->GetOperatorByOperatorId($Operators_company)->get();
                }
                $showAllOperator = false;
            }

            if ($request->filled('country') && !$request->filled('operatorId')) {
                $Operators = Operator::with('revenueshare', 'RevenushareByDate')->Status(1)->GetOperatorByCountryId($req_CountryId)->get();
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

            $arrayOperatorsIds = $Operators->pluck('id_operator')->toArray();
            $datesIndividual = Utility::getRangeDates($startColumnDateDisplay, $end_date);
            $no_of_months = Utility::getRangeMonthsNo($datesIndividual);

            foreach ($no_of_months as $key => $no_of_month) {
                $month_key = $no_of_month['date'];
                $monthList[] = $month_key;
            }

            $Country = Country::all();
            if (!empty($Country)) {
                foreach ($Country as $CountryI) {
                    $countries[$CountryI['id']] = $CountryI;
                }
            }

            $Users = User::Type("Account Manager")->get();
            $allUserIds = $Users->pluck('id')->toArray();
            // dd($allUserIds);
            if (empty($allUserIds)) {
                dd("no account Manager in your system");
            }

            $QueryMonthlyReports = MonthlyReportSummery::UserIn($allUserIds)
                ->Months($monthList);

            $user = Auth::user();
            $user_id = $user->id;
            $user_type = $user->type;
            $allowAllOperator = $user->WhowAccessAlOperator($user_type);

            $allMonthlyData = $QueryMonthlyReports->get()->toArray();
            $monthdata = UtilityAccountManager::getReportsOperatorID($allMonthlyData);;
            // dd($monthdata);

            if (!empty($Users)) {
                foreach ($Users as $user) {
                    $userId = $user->id;
                    $OperatorsIds = UsersOperatorsServices::GetOperaterServiceByUserId($user->id)->get()->unique("id_operator");
                    $ids_operator = $OperatorsIds->pluck("id_operator");
                    $Operators = Operator::with('revenueshare', 'RevenushareByDate')->Status(1)->GetOperatorByOperatorId($ids_operator)->get();
                    $NameofUser = $user->name;

                    if (!empty($Operators)) {
                        foreach ($Operators as $operator) {
                            $tmpOperators = array();
                            $activesubsdata = array();
                            $costdata = array();
                            $tmpOperators['operator'] = $operator;
                            $operator_id = $operator->id_operator;
                            $country_id  = $operator->country_id;
                            $contain_id = Arr::exists($countries, $country_id);
                            $OperatorCountry = array();
                            if ($contain_id) {
                                $tmpOperators['country'] = $countries[$country_id];
                                $OperatorCountry = $countries[$country_id];
                            }
                            $tmpOperators['account_manager'] = $NameofUser;
                            $tmpOperators['month_string'] = $month;
                            // dd($tmpOperators);
                            $reportsColumnData = $this->getReportsDateWise($operator, $no_of_months, $monthdata[$userId], $activesubsdata, $costdata, $OperatorCountry);
                            // dd($reportsColumnData);

                            $total_avg_t = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['tur'], $startColumnDateDisplay, $end_date);

                            $tmpOperators['tur']['dates'] = $reportsColumnData['tur'];
                            $tmpOperators['tur']['total'] = $total_avg_t['sum'];
                            $tmpOperators['tur']['t_mo_end'] = $total_avg_t['T_Mo_End'];;
                            $tmpOperators['tur']['avg'] = $total_avg_t['avg'];

                            $total_avg_rev = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['t_rev'], $startColumnDateDisplay, $end_date);

                            $tmpOperators['t_rev']['dates'] = $reportsColumnData['t_rev'];
                            $tmpOperators['t_rev']['total'] = $total_avg_rev['sum'];
                            $tmpOperators['t_rev']['t_mo_end'] = $total_avg_rev['T_Mo_End'];;
                            $tmpOperators['t_rev']['avg'] = $total_avg_rev['avg'];

                            $total_avg_trat = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['trat'], $startColumnDateDisplay, $end_date);

                            $tmpOperators['trat']['dates'] = $reportsColumnData['trat'];
                            $tmpOperators['trat']['total'] = $total_avg_trat['sum'];
                            $tmpOperators['trat']['t_mo_end'] = $total_avg_trat['T_Mo_End'];
                            $tmpOperators['trat']['avg'] = $total_avg_trat['avg'];

                            $total_avg_turt = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['turt'], $startColumnDateDisplay, $end_date);

                            $tmpOperators['turt']['dates'] = $reportsColumnData['turt'];
                            $tmpOperators['turt']['total'] = $total_avg_turt['sum'];
                            $tmpOperators['turt']['t_mo_end'] = $total_avg_turt['T_Mo_End'];;
                            $tmpOperators['turt']['avg'] = $total_avg_turt['avg'];;

                            $total_avg_net_rev = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['net_rev'], $startColumnDateDisplay, $end_date);
                            $tmpOperators['net_rev']['dates'] = $reportsColumnData['net_rev'];
                            $tmpOperators['net_rev']['total'] = $total_avg_net_rev['sum'];
                            $tmpOperators['net_rev']['t_mo_end'] = $total_avg_net_rev['T_Mo_End'];
                            $tmpOperators['net_rev']['avg'] = $total_avg_net_rev['avg'];

                            $total_avg_t_sub = UtilityReportsMonthly::calculateTotalSubscribe($operator, $reportsColumnData['t_sub'], $startColumnDateDisplay, $end_date);

                            $tmpOperators['t_sub']['dates'] = $reportsColumnData['t_sub'];
                            $tmpOperators['t_sub']['total'] = $total_avg_t_sub['sum'];
                            $tmpOperators['t_sub']['t_mo_end'] = $total_avg_t_sub['T_Mo_End'];
                            $tmpOperators['t_sub']['avg'] = $total_avg_t_sub['avg'];

                            $total_avg_t_reg = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['reg'], $startColumnDateDisplay, $end_date);

                            $tmpOperators['reg']['dates'] = $reportsColumnData['reg'];
                            $tmpOperators['reg']['total'] = $total_avg_t_reg['sum'];
                            $tmpOperators['reg']['t_mo_end'] = $total_avg_t_reg['T_Mo_End'];
                            $tmpOperators['reg']['avg'] = $total_avg_t_reg['avg'];

                            $total_avg_t_unreg = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['unreg'], $startColumnDateDisplay, $end_date);

                            $tmpOperators['unreg']['dates'] = $reportsColumnData['unreg'];
                            $tmpOperators['unreg']['total'] = $total_avg_t_unreg['sum'];
                            $tmpOperators['unreg']['t_mo_end'] = $total_avg_t_unreg['T_Mo_End'];;
                            $tmpOperators['unreg']['avg'] = $total_avg_t_unreg['avg'];

                            $total_avg_t_purged = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['purged'], $startColumnDateDisplay, $end_date);

                            $tmpOperators['purged']['dates'] = $reportsColumnData['purged'];
                            $tmpOperators['purged']['total'] = $total_avg_t_purged['sum'];
                            $tmpOperators['purged']['t_mo_end'] = $total_avg_t_purged['T_Mo_End'];
                            $tmpOperators['purged']['avg'] = $total_avg_t_purged['avg'];

                            $total_avg_t_churn = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['churn'], $startColumnDateDisplay, $end_date);

                            $tmpOperators['churn']['dates'] = $reportsColumnData['churn'];
                            $tmpOperators['churn']['total'] = $total_avg_t_churn['sum'];
                            $tmpOperators['churn']['t_mo_end'] = $total_avg_t_churn['T_Mo_End'];
                            $tmpOperators['churn']['avg'] = $total_avg_t_churn['avg'];

                            $total_avg_t_renewal = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['renewal'], $startColumnDateDisplay, $end_date);

                            $tmpOperators['renewal']['dates'] = $reportsColumnData['renewal'];
                            $tmpOperators['renewal']['total'] = $total_avg_t_renewal['sum'];
                            $tmpOperators['renewal']['t_mo_end'] = $total_avg_t_renewal['T_Mo_End'];
                            $tmpOperators['renewal']['avg'] = $total_avg_t_renewal['avg'];

                            $total_avg_mt_success = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['daily_push_success'], $startColumnDateDisplay, $end_date);

                            $tmpOperators['daily_push_success']['dates'] = $reportsColumnData['daily_push_success'];
                            $tmpOperators['daily_push_success']['total'] = $total_avg_mt_success['sum'];
                            $tmpOperators['daily_push_success']['t_mo_end'] = $total_avg_mt_success['T_Mo_End'];
                            $tmpOperators['daily_push_success']['avg'] = $total_avg_mt_success['avg'];

                            $total_avg_mt_failed = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['daily_push_failed'], $startColumnDateDisplay, $end_date);

                            $tmpOperators['daily_push_failed']['dates'] = $reportsColumnData['daily_push_failed'];
                            $tmpOperators['daily_push_failed']['total'] = $total_avg_mt_failed['sum'];
                            $tmpOperators['daily_push_failed']['t_mo_end'] = $total_avg_mt_failed['T_Mo_End'];
                            $tmpOperators['daily_push_failed']['avg'] = $total_avg_mt_failed['avg'];

                            $total_avg_t_bill = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['bill'], $startColumnDateDisplay, $end_date);

                            $tmpOperators['bill']['dates'] = $reportsColumnData['bill'];
                            $tmpOperators['bill']['total'] = $total_avg_t_bill['sum'];
                            $tmpOperators['bill']['t_mo_end'] = $total_avg_t_bill['T_Mo_End'];
                            $tmpOperators['bill']['avg'] = $total_avg_t_bill['avg'];

                            $total_avg_t_first_push = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['first_push'], $startColumnDateDisplay, $end_date);

                            $tmpOperators['first_push']['dates'] = $reportsColumnData['first_push'];
                            $tmpOperators['first_push']['total'] = $total_avg_t_first_push['sum'];
                            $tmpOperators['first_push']['t_mo_end'] = $total_avg_t_first_push['T_Mo_End'];
                            $tmpOperators['first_push']['avg'] = $total_avg_t_first_push['avg'];

                            $total_avg_t_daily_push = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['daily_push'], $startColumnDateDisplay, $end_date);

                            $tmpOperators['daily_push']['dates'] = $reportsColumnData['daily_push'];
                            $tmpOperators['daily_push']['total'] = $total_avg_t_daily_push['sum'];
                            $tmpOperators['daily_push']['t_mo_end'] = $total_avg_t_daily_push['T_Mo_End'];
                            $tmpOperators['daily_push']['avg'] = $total_avg_t_daily_push['avg'];

                            $total_avg_t_arpu7 = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['arpu7'], $startColumnDateDisplay, $end_date);

                            $tmpOperators['arpu7']['dates'] = $reportsColumnData['arpu7'];
                            $tmpOperators['arpu7']['total'] = $total_avg_t_arpu7['sum'];
                            $tmpOperators['arpu7']['t_mo_end'] = $total_avg_t_arpu7['T_Mo_End'];
                            $tmpOperators['arpu7']['avg'] = $total_avg_t_arpu7['avg'];

                            $total_avg_t_usarpu7 = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['usarpu7'], $startColumnDateDisplay, $end_date);

                            $tmpOperators['usarpu7']['dates'] = $reportsColumnData['usarpu7'];
                            $tmpOperators['usarpu7']['total'] = $total_avg_t_usarpu7['sum'];
                            $tmpOperators['usarpu7']['t_mo_end'] = $total_avg_t_usarpu7['T_Mo_End'];
                            $tmpOperators['usarpu7']['avg'] = $total_avg_t_usarpu7['avg'];

                            $total_avg_t_arpu30 = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['arpu30'], $startColumnDateDisplay, $end_date);

                            $tmpOperators['arpu30']['dates'] = $reportsColumnData['arpu30'];
                            $tmpOperators['arpu30']['total'] = $total_avg_t_arpu30['sum'];
                            $tmpOperators['arpu30']['t_mo_end'] = $total_avg_t_arpu30['T_Mo_End'];
                            $tmpOperators['arpu30']['avg'] = $total_avg_t_arpu30['avg'];

                            $total_avg_t_usarpu30 = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['usarpu30'], $startColumnDateDisplay, $end_date);

                            $tmpOperators['usarpu30']['dates'] = $reportsColumnData['usarpu30'];
                            $tmpOperators['usarpu30']['total'] = $total_avg_t_usarpu30['sum'];
                            $tmpOperators['usarpu30']['t_mo_end'] = $total_avg_t_usarpu30['T_Mo_End'];
                            $tmpOperators['usarpu30']['avg'] = $total_avg_t_usarpu30['avg'];

                            $total_avg_first_day_active = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['first_day_active'], $startColumnDateDisplay, $end_date);

                            $tmpOperators['first_day_active']['dates'] = $reportsColumnData['first_day_active'];
                            $tmpOperators['first_day_active']['total'] = $total_avg_first_day_active['sum'];
                            $tmpOperators['first_day_active']['t_mo_end'] = $total_avg_first_day_active['T_Mo_End'];
                            $tmpOperators['first_day_active']['avg'] = $total_avg_first_day_active['avg'];

                            $total_avg_cost_campaign = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['cost_campaign'], $startColumnDateDisplay, $end_date);

                            $tmpOperators['cost_campaign']['dates'] = $reportsColumnData['cost_campaign'];
                            $tmpOperators['cost_campaign']['total'] = $total_avg_cost_campaign['sum'];
                            $tmpOperators['cost_campaign']['t_mo_end'] = $total_avg_cost_campaign['T_Mo_End'];
                            $tmpOperators['cost_campaign']['avg'] = $total_avg_cost_campaign['avg'];

                            $total_avg_ltv = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['ltv'], $startColumnDateDisplay, $end_date);

                            $tmpOperators['ltv']['dates'] = $reportsColumnData['ltv'];
                            $tmpOperators['ltv']['total'] = $total_avg_ltv['sum'];
                            $tmpOperators['ltv']['t_mo_end'] = $total_avg_ltv['T_Mo_End'];
                            $tmpOperators['ltv']['avg'] = $total_avg_ltv['avg'];

                            $tmpOperators['mt_success']['dates'] = $reportsColumnData['mt_success'];
                            $tmpOperators['mt_failed']['dates'] = $reportsColumnData['mt_failed'];

                            $tmpOperators['fmt_success']['dates'] = $reportsColumnData['fmt_success'];
                            $tmpOperators['fmt_failed']['dates'] = $reportsColumnData['fmt_failed'];
                            $tmpOperators['last_update'] = $reportsColumnData['last_update'];

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
                    // dd($sumemries);
                    $tempDataArr = array();
                    $managerName = "N/A";

                    if (isset($sumemries[0]['account_manager']))
                        $managerName = $sumemries[0]['account_manager'];
                    $dataRowSum = UtilityReportsMonthly::CountrySumOperator($sumemries);
                    // dd($dataRowSum);
                    $dataRowSum = UtilityReportsMonthly::summeryPercentageCalculation($dataRowSum);
                    $tempDataArr['account_manager']['name'] = $managerName;
                    $tempDataArr['month_string'] = $month;
                    $tempDataArr['year'] = $year;
                    $tempDataArr['last_update'] = isset($sumemries[0]['last_update']) ? $sumemries[0]['last_update'] : 'Not Updated';
                    $tempDataArr = array_merge($tempDataArr, $dataRowSum);

                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "tur");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "turt");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "net_rev");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "t_rev");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "t_sub");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "trat");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "reg");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "unreg");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "purged");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "churn");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "renewal");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "daily_push_success");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "daily_push_failed");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "bill");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "first_push");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "daily_push");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "arpu7");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "usarpu7");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "arpu30");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "usarpu30");
                    $RowUserData[] = $tempDataArr;
                }
            }

            $sumemry = $RowUserData;
            // dd($RowUserData);
            $allsummaryData = UtilityReportsMonthly::monthly_all_summary_data($sumemry);
            $allsummaryData = UtilityReportsMonthly::summeryPercentageCalculation($allsummaryData);

            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "tur");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "turt");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "t_rev");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "t_sub");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "trat");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "reg");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "unreg");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "purged");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "churn");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "renewal");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "daily_push_success");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "daily_push_failed");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "bill");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "first_push");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "daily_push");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "arpu7");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "usarpu7");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "arpu30");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "usarpu30");
            $no_of_days = $no_of_months;
            // dd($sumemry);

            return view('report.monthly_manager_report', compact('sumemry', 'no_of_days', 'allsummaryData', 'monthly', 'AccountManagerWise'));
        } else {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    // get Business monthly report
    public function MonthlyBusinessWise(Request $request)
    {
        if (\Auth::user()->can('Report Summary')) {
            $BusinessTypeWise = 1;
            $monthly = 1;
            $sumemry = array();
            $monthList = array();
            $countries = array();
            $Operators_company = array();
            $showAllOperator = true;
            $countrys = [];
            $country_ids = [];
            $country_operator = [];

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

            $firstDayoftheyear = Carbon::now()->startOfYear()->format('Y-m-d');
            $start_date = $firstDayoftheyear;
            $startColumnDateDisplay = $firstDayoftheyear;
            $end_date = Carbon::now()->format('Y-m-d');
            $month = Carbon::now()->format('F Y');
            $year = Carbon::now()->format('Y');


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
                    $Operators = Operator::with('revenueshare', 'RevenushareByDate')->Status(1)->GetOperatorByOperatorId($Operators_company)->get();
                }
                $showAllOperator = false;
            }

            if ($request->filled('country') && !$request->filled('operatorId')) {
                $Operators = Operator::with('revenueshare', 'RevenushareByDate')->Status(1)->GetOperatorByCountryId($req_CountryId)->get();
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

            $arrayOperatorsIds = $Operators->pluck('id_operator')->toArray();
            $datesIndividual = Utility::getRangeDates($startColumnDateDisplay, $end_date);
            $no_of_months = Utility::getRangeMonthsNo($datesIndividual);

            $monthList = array();
            $FirstDayList = array();

            foreach ($no_of_months as $key => $no_of_month) {
                $firstDay = Carbon::createFromFormat('Y-m', $no_of_month['date'])->firstOfMonth()->format('Y-m-d');
                $month_key = $no_of_month['date'];
                $monthList[] = $month_key;
                $FirstDayList[] = $firstDay;
            }

            $Country = Country::all();
            if (!empty($Country)) {
                foreach ($Country as $CountryI) {
                    $countries[$CountryI['id']] = $CountryI;
                }
            }



            $arrayOperatorsIds = $Operators->pluck('id_operator')->toArray();
            // dd($arrayOperatorsIds);

            /* Admin Access All operator and Services */

            $QueryMonthlyReports = MonthlyReportSummery::filteroperator($arrayOperatorsIds)
                ->Months($monthList);

            $user = Auth::user();

            $user_id = $user->id;



            $user_type = $user->type;
            $allowAllOperator = $user->WhowAccessAlOperator($user_type);

            if ($allowAllOperator) {

                $QueryMonthlyReports = $QueryMonthlyReports->User(0);
            } else {

                $QueryMonthlyReports = $QueryMonthlyReports->User($user_id);
            }

            $allMonthlyData = $QueryMonthlyReports->get()->toArray();

            /* END */

            $activeSubs = report_summarize::select('total', 'date', 'operator_id')->filteroperator($arrayOperatorsIds)->Dates($FirstDayList)->get()->toArray();

            $cost_campaign = PnlSummeryMonth::select('id_operator as operator_id', 'key', 'cost_campaign')->filteroperator($arrayOperatorsIds)->Months($monthList)->get()->toArray();

            $monthdata = $this->rearrangeOperatorMonth($allMonthlyData);
            $activesubsdata = $this->rearrangeOperatorDate($activeSubs);
            $costdata = $this->rearrangeOperatorMonth($cost_campaign);
            // dd($monthdata);


            if (!empty($Operators)) {
                foreach ($Operators as $operator) {
                    $tmpOperators = array();

                    $type = $operator->business_type;
                    // if(!isset($type)){
                    //   continue;
                    // }
                    if ($type == NULL) {
                        $type = 'unknown';
                    }
                    $tmpOperators['operator'] = $operator;
                    $operator_id = $operator->id_operator;
                    $country_id  = $operator->country_id;
                    $contain_id = Arr::exists($countries, $country_id);
                    $OperatorCountry = array();
                    if ($contain_id) {
                        $tmpOperators['country'] = $countries[$country_id];
                        $OperatorCountry = $countries[$country_id];
                    }
                    $tmpOperators['account_manager'] = $type;
                    $tmpOperators['month_string'] = $month;
                    // dd($tmpOperators);
                    $reportsColumnData = $this->getReportsDateWise($operator, $no_of_months, $monthdata, $activesubsdata, $costdata, $OperatorCountry);
                    // dd($reportsColumnData);

                    $total_avg_t = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['tur'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['tur']['dates'] = $reportsColumnData['tur'];
                    $tmpOperators['tur']['total'] = $total_avg_t['sum'];
                    $tmpOperators['tur']['t_mo_end'] = $total_avg_t['T_Mo_End'];;
                    $tmpOperators['tur']['avg'] = $total_avg_t['avg'];

                    $total_avg_rev = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['t_rev'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['t_rev']['dates'] = $reportsColumnData['t_rev'];
                    $tmpOperators['t_rev']['total'] = $total_avg_rev['sum'];
                    $tmpOperators['t_rev']['t_mo_end'] = $total_avg_rev['T_Mo_End'];;
                    $tmpOperators['t_rev']['avg'] = $total_avg_rev['avg'];

                    $total_avg_trat = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['trat'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['trat']['dates'] = $reportsColumnData['trat'];
                    $tmpOperators['trat']['total'] = $total_avg_trat['sum'];
                    $tmpOperators['trat']['t_mo_end'] = $total_avg_trat['T_Mo_End'];
                    $tmpOperators['trat']['avg'] = $total_avg_trat['avg'];

                    $total_avg_turt = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['turt'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['turt']['dates'] = $reportsColumnData['turt'];
                    $tmpOperators['turt']['total'] = $total_avg_turt['sum'];
                    $tmpOperators['turt']['t_mo_end'] = $total_avg_turt['T_Mo_End'];;
                    $tmpOperators['turt']['avg'] = $total_avg_turt['avg'];;

                    $total_avg_net_rev = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['net_rev'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['net_rev']['dates'] = $reportsColumnData['net_rev'];
                    $tmpOperators['net_rev']['total'] = $total_avg_net_rev['sum'];
                    $tmpOperators['net_rev']['t_mo_end'] = $total_avg_net_rev['T_Mo_End'];
                    $tmpOperators['net_rev']['avg'] = $total_avg_net_rev['avg'];

                    $total_avg_t_sub = UtilityReportsMonthly::calculateTotalSubscribe($operator, $reportsColumnData['t_sub'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['t_sub']['dates'] = $reportsColumnData['t_sub'];
                    $tmpOperators['t_sub']['total'] = $total_avg_t_sub['sum'];
                    $tmpOperators['t_sub']['t_mo_end'] = $total_avg_t_sub['T_Mo_End'];
                    $tmpOperators['t_sub']['avg'] = $total_avg_t_sub['avg'];

                    $total_avg_t_reg = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['reg'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['reg']['dates'] = $reportsColumnData['reg'];
                    $tmpOperators['reg']['total'] = $total_avg_t_reg['sum'];
                    $tmpOperators['reg']['t_mo_end'] = $total_avg_t_reg['T_Mo_End'];
                    $tmpOperators['reg']['avg'] = $total_avg_t_reg['avg'];

                    $total_avg_t_unreg = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['unreg'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['unreg']['dates'] = $reportsColumnData['unreg'];
                    $tmpOperators['unreg']['total'] = $total_avg_t_unreg['sum'];
                    $tmpOperators['unreg']['t_mo_end'] = $total_avg_t_unreg['T_Mo_End'];;
                    $tmpOperators['unreg']['avg'] = $total_avg_t_unreg['avg'];

                    $total_avg_t_purged = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['purged'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['purged']['dates'] = $reportsColumnData['purged'];
                    $tmpOperators['purged']['total'] = $total_avg_t_purged['sum'];
                    $tmpOperators['purged']['t_mo_end'] = $total_avg_t_purged['T_Mo_End'];
                    $tmpOperators['purged']['avg'] = $total_avg_t_purged['avg'];

                    $total_avg_t_churn = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['churn'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['churn']['dates'] = $reportsColumnData['churn'];
                    $tmpOperators['churn']['total'] = $total_avg_t_churn['sum'];
                    $tmpOperators['churn']['t_mo_end'] = $total_avg_t_churn['T_Mo_End'];
                    $tmpOperators['churn']['avg'] = $total_avg_t_churn['avg'];

                    $total_avg_t_renewal = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['renewal'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['renewal']['dates'] = $reportsColumnData['renewal'];
                    $tmpOperators['renewal']['total'] = $total_avg_t_renewal['sum'];
                    $tmpOperators['renewal']['t_mo_end'] = $total_avg_t_renewal['T_Mo_End'];
                    $tmpOperators['renewal']['avg'] = $total_avg_t_renewal['avg'];

                    $total_avg_mt_success = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['daily_push_success'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['daily_push_success']['dates'] = $reportsColumnData['daily_push_success'];
                    $tmpOperators['daily_push_success']['total'] = $total_avg_mt_success['sum'];
                    $tmpOperators['daily_push_success']['t_mo_end'] = $total_avg_mt_success['T_Mo_End'];
                    $tmpOperators['daily_push_success']['avg'] = $total_avg_mt_success['avg'];

                    $total_avg_mt_failed = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['daily_push_failed'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['daily_push_failed']['dates'] = $reportsColumnData['daily_push_failed'];
                    $tmpOperators['daily_push_failed']['total'] = $total_avg_mt_failed['sum'];
                    $tmpOperators['daily_push_failed']['t_mo_end'] = $total_avg_mt_failed['T_Mo_End'];
                    $tmpOperators['daily_push_failed']['avg'] = $total_avg_mt_failed['avg'];

                    $total_avg_t_bill = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['bill'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['bill']['dates'] = $reportsColumnData['bill'];
                    $tmpOperators['bill']['total'] = $total_avg_t_bill['sum'];
                    $tmpOperators['bill']['t_mo_end'] = $total_avg_t_bill['T_Mo_End'];
                    $tmpOperators['bill']['avg'] = $total_avg_t_bill['avg'];

                    $total_avg_t_first_push = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['first_push'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['first_push']['dates'] = $reportsColumnData['first_push'];
                    $tmpOperators['first_push']['total'] = $total_avg_t_first_push['sum'];
                    $tmpOperators['first_push']['t_mo_end'] = $total_avg_t_first_push['T_Mo_End'];
                    $tmpOperators['first_push']['avg'] = $total_avg_t_first_push['avg'];

                    $total_avg_t_daily_push = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['daily_push'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['daily_push']['dates'] = $reportsColumnData['daily_push'];
                    $tmpOperators['daily_push']['total'] = $total_avg_t_daily_push['sum'];
                    $tmpOperators['daily_push']['t_mo_end'] = $total_avg_t_daily_push['T_Mo_End'];
                    $tmpOperators['daily_push']['avg'] = $total_avg_t_daily_push['avg'];

                    $total_avg_t_arpu7 = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['arpu7'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['arpu7']['dates'] = $reportsColumnData['arpu7'];
                    $tmpOperators['arpu7']['total'] = $total_avg_t_arpu7['sum'];
                    $tmpOperators['arpu7']['t_mo_end'] = $total_avg_t_arpu7['T_Mo_End'];
                    $tmpOperators['arpu7']['avg'] = $total_avg_t_arpu7['avg'];

                    $total_avg_t_usarpu7 = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['usarpu7'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['usarpu7']['dates'] = $reportsColumnData['usarpu7'];
                    $tmpOperators['usarpu7']['total'] = $total_avg_t_usarpu7['sum'];
                    $tmpOperators['usarpu7']['t_mo_end'] = $total_avg_t_usarpu7['T_Mo_End'];
                    $tmpOperators['usarpu7']['avg'] = $total_avg_t_usarpu7['avg'];

                    $total_avg_t_arpu30 = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['arpu30'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['arpu30']['dates'] = $reportsColumnData['arpu30'];
                    $tmpOperators['arpu30']['total'] = $total_avg_t_arpu30['sum'];
                    $tmpOperators['arpu30']['t_mo_end'] = $total_avg_t_arpu30['T_Mo_End'];
                    $tmpOperators['arpu30']['avg'] = $total_avg_t_arpu30['avg'];

                    $total_avg_t_usarpu30 = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['usarpu30'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['usarpu30']['dates'] = $reportsColumnData['usarpu30'];
                    $tmpOperators['usarpu30']['total'] = $total_avg_t_usarpu30['sum'];
                    $tmpOperators['usarpu30']['t_mo_end'] = $total_avg_t_usarpu30['T_Mo_End'];
                    $tmpOperators['usarpu30']['avg'] = $total_avg_t_usarpu30['avg'];

                    $total_avg_first_day_active = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['first_day_active'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['first_day_active']['dates'] = $reportsColumnData['first_day_active'];
                    $tmpOperators['first_day_active']['total'] = $total_avg_first_day_active['sum'];
                    $tmpOperators['first_day_active']['t_mo_end'] = $total_avg_first_day_active['T_Mo_End'];
                    $tmpOperators['first_day_active']['avg'] = $total_avg_first_day_active['avg'];

                    $total_avg_cost_campaign = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['cost_campaign'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['cost_campaign']['dates'] = $reportsColumnData['cost_campaign'];
                    $tmpOperators['cost_campaign']['total'] = $total_avg_cost_campaign['sum'];
                    $tmpOperators['cost_campaign']['t_mo_end'] = $total_avg_cost_campaign['T_Mo_End'];
                    $tmpOperators['cost_campaign']['avg'] = $total_avg_cost_campaign['avg'];

                    $total_avg_ltv = UtilityReportsMonthly::calculateRevTotalAVG($operator, $reportsColumnData['ltv'], $startColumnDateDisplay, $end_date);

                    $tmpOperators['ltv']['dates'] = $reportsColumnData['ltv'];
                    $tmpOperators['ltv']['total'] = $total_avg_ltv['sum'];
                    $tmpOperators['ltv']['t_mo_end'] = $total_avg_ltv['T_Mo_End'];
                    $tmpOperators['ltv']['avg'] = $total_avg_ltv['avg'];

                    $tmpOperators['mt_success']['dates'] = $reportsColumnData['mt_success'];
                    $tmpOperators['mt_failed']['dates'] = $reportsColumnData['mt_failed'];

                    $tmpOperators['fmt_success']['dates'] = $reportsColumnData['fmt_success'];
                    $tmpOperators['fmt_failed']['dates'] = $reportsColumnData['fmt_failed'];
                    $tmpOperators['last_update'] = $reportsColumnData['last_update'];

                    $sumemry[$type][] = $tmpOperators;
                }
            }
            //   dd($sumemry);
            // Account Manager Sum from Operator array
            $displayAccountManagers = array();
            $SelectedAccountManagers = array();
            $RowUserData = array();

            if (!empty($sumemry)) {
                foreach ($sumemry as $key => $sumemries) {
                    // dd($sumemries);
                    $tempDataArr = array();
                    $managerName = "N/A";


                    $managerName = $key;
                    $dataRowSum = UtilityReportsMonthly::CountrySumOperator($sumemries);
                    //   dd($dataRowSum);
                    $dataRowSum = UtilityReportsMonthly::summeryPercentageCalculation($dataRowSum);
                    $tempDataArr['account_manager']['name'] = $managerName;
                    $tempDataArr['month_string'] = $month;
                    $tempDataArr['year'] = $year;
                    $tempDataArr['last_update'] = isset($sumemries[$key]['last_update']) ? $sumemries[$key]['last_update'] : 'Not Updated';
                    $tempDataArr = array_merge($tempDataArr, $dataRowSum);

                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "tur");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "turt");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "net_rev");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "t_rev");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "t_sub");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "trat");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "reg");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "unreg");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "purged");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "churn");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "renewal");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "daily_push_success");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "daily_push_failed");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "bill");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "first_push");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "daily_push");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "arpu7");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "usarpu7");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "arpu30");
                    $tempDataArr = UtilityReportsMonthly::ColorFirstDay($tempDataArr, "usarpu30");
                    $RowUserData[] = $tempDataArr;
                }
            }

            $sumemry = $RowUserData;
            // dd($RowUserData);
            $allsummaryData = UtilityReportsMonthly::monthly_all_summary_data($sumemry);
            $allsummaryData = UtilityReportsMonthly::summeryPercentageCalculation($allsummaryData);

            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "tur");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "turt");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "t_rev");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "t_sub");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "trat");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "reg");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "unreg");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "purged");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "churn");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "renewal");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "daily_push_success");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "daily_push_failed");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "bill");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "first_push");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "daily_push");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "arpu7");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "usarpu7");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "arpu30");
            $allsummaryData = UtilityReportsMonthly::ColorFirstDay($allsummaryData, "usarpu30");
            $no_of_days = $no_of_months;
            //   dd($allsummaryData);

            return view('report.monthly_manager_report', compact('sumemry', 'no_of_days', 'allsummaryData', 'monthly', 'BusinessTypeWise'));
        } else {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

   


    //report roi monitor operatorwise
    public function RoiMonitorOperatorWise(Request $request)
    {
        if (\Auth::user()->can('Monitor ROI')) {
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


                }

                $totelCountryCosts[$country]['roi']['dates'] = $roi_arr;
                $totelCountryCosts[$country]['roi']['total'] = $country_totelcost_roi;


            }
            $no_of_days = $no_of_months;

            $AllCuntryGrosRev['month_string'] = $month;
            $date = Carbon::parse($end_date)->format('Y-m');
            return view('report.roi_monitor_operator', compact('totelCountryCosts', 'date', 'AllCuntryGrosRev', 'no_of_days'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    //report roi monitor countrywise
    public function RoiMonitorCountryWise(Request $request)
    {
        if (\Auth::user()->can('Monitor ROI')) {
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


                }

                $totelCountryCosts[$country]['roi']['dates'] = $roi_arr;
                $totelCountryCosts[$country]['roi']['total'] = $country_totelcost_roi;


            }
            $no_of_days = $no_of_months;

            $AllCuntryGrosRev['month_string'] = $month;
            $date = Carbon::parse($end_date)->format('Y-m');
            return view('report.roi_monitor_country', compact('totelCountryCosts', 'AllCuntryGrosRev', 'no_of_days'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }
    // get report date wise
    function getReportsDateWise($operator, $no_of_months, $reportsByIDs, $activesubsdata = array(), $costdata = array(), $OperatorCountry, $service_historys = array())
    {
        if (\Auth::user()->can('Report Summary')) {
            // dd($operator);
            // dd($reportsByIDs);
            // dd(compact('operator','no_of_months','reportsByIDs','OperatorCountry'));
            $usdValue = isset($OperatorCountry['usd']) ? $OperatorCountry['usd'] : 1;
            $shareDb = array();
            $merchent_share = 1;
            $operator_share = 1;
            $revenue_share = $operator->revenueshare;
            $revenushare_by_dates = $operator->RevenushareByDate;
            $country_id = $OperatorCountry['id'];
            $vat = 0;
            $wht = 0;
            $misc_taxByDate = $operator->MiscTax;
            $VatByDate = $operator->VatByDate;
            $WhtByDate = $operator->WhtByDate;

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

            if (!empty($no_of_months)) {
                $allColumnData = array();
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
                $first_day_active = array();
                $campaignCost = array();
                $ltv = array();
                $id_operator = $operator->id_operator;
                $update = false;
                $last_update = "";

                foreach ($no_of_months as $months) {
                    $shareDb['merchent_share'] = $merchent_share;
                    $shareDb['operator_share'] = $operator_share;

                    $keys = $id_operator . "." . $months['date'];

                    //  dd($keys);

                    /*"id" => 335
                    "operator_id" => 1
                    "operator_name" => "telkomsel"
                    "country_id" => 1
                    "date" => "2022-11-01"
                    "fmt_success" => 0
                    "fmt_failed" => 0
                    "mt_success" => 0
                    "mt_failed" => 0
                    "gros_rev" => "0.00"
                    "total_reg" => 0
                    "total_unreg" => 0
                    "total" => 0
                    "purge_total" => 0
                    "created_at" => "2022-11-07T07:50:54.000000Z"
                    "updated_at" => "2022-11-07T07:50:54.000000Z"
                    */

                    $key_date = new Carbon($months['date']);
                    $key = $key_date->format("Y-m");

                    if (isset($revenushare_by_dates[$key])) {
                        $merchent_share_by_dates = $revenushare_by_dates[$key]->merchant_revenue_share;
                        $operator_share_by_dates = $revenushare_by_dates[$key]->operator_revenue_share;

                        $shareDb['merchent_share'] = $merchent_share_by_dates;
                        $shareDb['operator_share'] = $operator_share_by_dates;
                    }

                    $summariserowS = Arr::get($reportsByIDs, $keys, 0);

                    $firstDayActive = Arr::get($activesubsdata, $keys, 0);

                    $costCampaign = Arr::get($costdata, $keys, 0);


                    if (isset($summariserowS)) {
                        $summariserow = $summariserowS;
                    } else {
                        $summariserow = array();
                    }

                    if ($summariserow != 0  && !$update) {
                        // dd($summariserow);
                        $update = true;
                        $last_update = $summariserow['updated_at'];
                    }

                    //dd($summariserow['gros_rev']);

                    $gros_rev = isset($summariserow['gros_rev']) ? $summariserow['gros_rev'] : 0;
                    $total_subscriber = isset($summariserow['total']) ? $summariserow['total'] : 0;

                    $gros_rev_Usd = 0;

                    $gros_rev_Usd = $gros_rev * $usdValue;

                    $total_reg = isset($summariserow['total_reg']) ? $summariserow['total_reg'] : 0;
                    $total_unreg = isset($summariserow['total_unreg']) ? $summariserow['total_unreg'] : 0;
                    $purge_total = isset($summariserow['purge_total']) ? $summariserow['purge_total'] : 0;

                    $mt_success = isset($summariserow['mt_success']) ? $summariserow['mt_success'] : 0;
                    $mt_failed = isset($summariserow['mt_failed']) ? $summariserow['mt_failed'] : 0;
                    $fmt_success = isset($summariserow['fmt_success']) ? $summariserow['fmt_success'] : 0;
                    $fmt_failed = isset($summariserow['fmt_failed']) ? $summariserow['fmt_failed'] : 0;

                    if ($total_subscriber > 0) {
                        // $churn_value = (( (int)$total_reg - (int)$total_unreg + (int)$purge_total) / (int)$total_subscriber) * 100;

                        $churn_value = ((int)$total_unreg  / (int)$total_subscriber) * 100;

                        // $churn_value =sprintf('%0.2f', $churn_value);
                    } else {
                        $churn_value = 0;
                    }

                    $renewal_total = $mt_success + $mt_failed;

                    $billRate = UtilityReports::billRate($mt_success, $mt_failed, $total_subscriber);
                    // $billRate =sprintf('%0.2f', $billRate);

                    $FirstPush = UtilityReports::FirstPush($fmt_success, $fmt_failed, $total_subscriber);
                    // $FirstPush =sprintf('%0.2f', $FirstPush);

                    $Dailypush = UtilityReports::Dailypush($mt_success, $mt_failed, $total_subscriber);
                    // $Dailypush =sprintf('%0.2f', $Dailypush);

                    // $arpu7Data = UtilityReportsMonthly::Arpu7($operator,$reportsByIDs,$months,$total_subscriber,$shareDb);
                    // $arpu7USD = $arpu7Data * $usdValue;

                    $arpu30Data = UtilityReportsMonthly::Arpu30($operator, $reportsByIDs, $months, $total_subscriber, $shareDb);
                    $arpu30USD =  $arpu30Data * $usdValue;

                    $arpu30Rawdata = UtilityPercentage::Arpu30RawMonth($operator, $reportsByIDs, $months, $total_subscriber, $shareDb, $OperatorCountry, $service_historys);
                    // dd($arpu30Rawdata);

                    //$arpuRawdata = UtilityPercentage::Arpu30Raw($operator,$reportsByIDs,$months,$total_subscriber,$shareDb,$OperatorCountry);
                    // $arpuRawdata = [];

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

                    /*$vat = !empty($operator->vat) ? $turtData * ($operator->vat/100) : 0;

                    $wht = !empty($operator->wht) ? $turtData * ($operator->wht/100) : 0;

                    $miscTax = !empty($operator->miscTax) ? $turtData * ($operator->miscTax/100) : 0;

                    $other_tax = $vat + $wht + $miscTax;

                    if($other_tax != 0){
                        $turtData = $turtData - $other_tax;
                        $tratData = $turtData / $usdValue;
                    }*/

                    $arpu30Raw[$months['date']]['value'] = $arpu30Rawdata;
                    $arpu30Raw[$months['date']]['class'] = "";

                    $tur[$months['date']]['value'] = $gros_rev_Usd;
                    $tur[$months['date']]['class'] = "bg-hui";

                    $t_rev[$months['date']]['value'] = $gros_rev;
                    $t_rev[$months['date']]['class'] = "bg-hui";

                    $trat[$months['date']]['value'] = $tratData;
                    $trat[$months['date']]['class'] = "bg-hui";

                    $turt[$months['date']]['value'] = $turtData;
                    $turt[$months['date']]['class'] = "bg-hui";

                    $net_rev[$months['date']]['value'] = $netRev;
                    $net_rev[$months['date']]['class'] = "bg-hui";

                    $t_sub[$months['date']]['value'] = $total_subscriber;
                    $t_sub[$months['date']]['class'] = "bg-hui";

                    $reg[$months['date']]['value'] = $total_reg;
                    $reg[$months['date']]['class'] = "bg-hui";

                    $unreg[$months['date']]['value'] = $total_unreg;
                    $unreg[$months['date']]['class'] = "bg-hui";

                    $purged[$months['date']]['value'] = $purge_total;
                    $purged[$months['date']]['class'] = "bg-hui";

                    $churn[$months['date']]['value'] = $churn_value;
                    $churn[$months['date']]['class'] = "bg-hui";

                    $renewal[$months['date']]['value'] = $renewal_total;
                    $renewal[$months['date']]['class'] = "bg-hui";

                    $daily_push_success[$months['date']]['value'] = $mt_success;
                    $daily_push_success[$months['date']]['class'] = "bg-hui";

                    $daily_push_failed[$months['date']]['value'] = $mt_failed;
                    $daily_push_failed[$months['date']]['class'] = "bg-hui";

                    $bill[$months['date']]['value'] = $billRate;
                    $bill[$months['date']]['class'] = "bg-hui";

                    $first_push[$months['date']]['value'] = $FirstPush;
                    $first_push[$months['date']]['class'] = "bg-hui";

                    $daily_push[$months['date']]['value'] = $Dailypush;
                    $daily_push[$months['date']]['class'] = "bg-hui";

                    $arpu7[$months['date']]['value'] = 0;
                    $arpu7[$months['date']]['class'] = "bg-hui";

                    $usarpu7[$months['date']]['value'] = 0;
                    $usarpu7[$months['date']]['class'] = "bg-hui";

                    $arpu30[$months['date']]['value'] = $arpu30Data;
                    $arpu30[$months['date']]['class'] = "bg-hui";

                    $usarpu30[$months['date']]['value'] = $arpu30USD;
                    $usarpu30[$months['date']]['class'] = "bg-hui";

                    $mtSuccess[$months['date']]['value'] = $mt_success;
                    $mtSuccess[$months['date']]['class'] = "bg-hui";

                    $mtFailed[$months['date']]['value'] = $mt_failed;
                    $mtFailed[$months['date']]['class'] = "bg-hui";

                    $fmtSuccess[$months['date']]['value'] = $fmt_success;
                    $fmtSuccess[$months['date']]['class'] = "bg-hui";

                    $fmtFailed[$months['date']]['value'] = $fmt_failed;
                    $fmtFailed[$months['date']]['class'] = "bg-hui";

                    $first_day_active[$months['date']]['value'] = $activeSubsFirstDay;
                    $first_day_active[$months['date']]['class'] = "bg-hui";

                    $cost_campaign[$months['date']]['value'] = $campaignCost;
                    $cost_campaign[$months['date']]['class'] = "bg-hui";

                    $ltv[$months['date']]['value'] = $clv;
                    $ltv[$months['date']]['class'] = "bg-hui";
                }

                $last_update_show = "Not updated last month";
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
                $allColumnData['arpu30Raw'] = $arpu30Raw;
                $allColumnData['mt_success'] = $mtSuccess;
                $allColumnData['mt_failed'] = $mtFailed;
                $allColumnData['fmt_success'] = $fmtSuccess;
                $allColumnData['fmt_failed'] = $fmtFailed;
                $allColumnData['first_day_active'] = $first_day_active;
                $allColumnData['cost_campaign'] = $cost_campaign;
                $allColumnData['ltv'] = $ltv;
                $allColumnData['last_update'] = $last_update_show;

                //dd($allColumnData);
                return $allColumnData;
            } else {
                return [];
            }
        } else {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    function getReportsGrosRevDateWise($operator, $no_of_days, $reportsByIDs, $OperatorCountry, $reportsByOperatorIDs = array(), $active_subsByOperatorIDs = array())
    {

        $usdValue = $OperatorCountry['usd'];
        $shareDb = array();
        $revenue_share = $operator->revenueshare;
        $merchent_share = 1;
        $operator_share = 1;
        if (isset($revenue_share)) {
            $merchent_share = $revenue_share->merchant_revenue_share;
            $operator_share = $revenue_share->operator_revenue_share;
        }


        if (!empty($no_of_days)) {
            $allColumnData = array();
            $tur_arr = array();
            $mo = array();
            $billRate = array();
            $roi_Arr = array();
            $renewal_arr = array();
            $id_operator = $operator->id_operator;

            foreach (array_reverse($no_of_days) as $days) {
                // dd($merchent_share);
                $summariserGP = [];
                $shareDb['merchent_share'] = $merchent_share;



                $key_date = new Carbon($days['date']);
                $key = $key_date->format("Y-m");
                $keys = $id_operator . "." . $days['date'];

                $summariserow = Arr::get($reportsByIDs, $keys, 0);

                if (isset($QueryMonthlys) && !empty($QueryMonthlys)) {
                    $summariserGP = Arr::get($QueryMonthlys, $keys, 0);
                }

                $end_user_rev = isset($summariserGP['rev']) ? $summariserGP['rev'] : 0;
                $end_user_rev = sprintf('%0.2f', $end_user_rev);
                $end_user_rev_usd = $end_user_rev * $usdValue;
                $end_user_rev_usd = sprintf('%0.2f', $end_user_rev_usd);


                $tur_data = isset($summariserow['gros_rev']) ? $summariserow['gros_rev'] : 0;
                $tur_data = $tur_data * $usdValue;

                $modata = isset($summariserGP['mo_received']) ? $summariserGP['mo_received'] : 0;
                $modata = sprintf('%0.2f', $modata);

                $total_subscriber = isset($summariserow['total']) ? $summariserow['total'] : 0;

                $mt_success = isset($summariserow['mt_success']) ? $summariserow['mt_success'] : 0;
                $mt_failed = isset($summariserow['mt_failed']) ? $summariserow['mt_failed'] : 0;

                $bill = UtilityReports::billRate($mt_success, $mt_failed, $total_subscriber);
                $billRate[$days['date']]['value'] = $bill;

                $tur_arr[$days['date']]['value'] = $tur_data;
                $mo[$days['date']]['value'] = $modata;

                //renewal part here
                $renewal = $mt_success + $mt_failed;
                $renewal = sprintf('%0.2f', $renewal);

                $renewal_arr[$days['date']]['value'] = $renewal;


                //roi part is here
                $reg = isset($summariserGP['reg']) ? $summariserGP['reg'] : 0;
                $reg = sprintf('%0.2f', $reg);

                $active_subs = isset($summariserGP['active_subs']) ? $summariserGP['active_subs'] : 0;
                $active_subs = sprintf('%0.2f', $active_subs);

                $gros_rev_Usd = UtilityReports::turt($shareDb, $end_user_rev_usd);
                // $gros_rev_Usd = sprintf('%0.2f', $gros_rev_Usd);


                $arpu_30_usd = ($reg == 0) ? 0 : ($gros_rev_Usd / ($reg + $active_subs));


                $cost_campaign = isset($summariserGP['cost_campaign']) ? $summariserGP['cost_campaign'] : 0;
                $cost_campaign = sprintf('%0.2f', $cost_campaign);

                $price_mo = ($modata == 0) ? (float)0 : ($cost_campaign / $modata);

                $roi = ($arpu_30_usd == 0) ? 0 : ($price_mo / $arpu_30_usd);

                $roi_Arr[$days['date']]['value'] = $roi;
            }

            $allColumnData['tur'] = $tur_arr;
            $allColumnData['mo'] = $mo;
            $allColumnData['bill'] = $billRate;
            $allColumnData['roi'] = $roi_Arr;
            $allColumnData['renewal'] = $renewal_arr;
            // dd($roi_Arr);
            return $allColumnData;
        }
    }

    // get report by operator id
    function getReportsOperatorID($reports)
    {
        if (!empty($reports)) {
            $reportsResult = array();
            $tempreport = array();

            foreach ($reports as $report) {
                $tempreport[$report['operator_id']][$report['date']] = $report;
            }

            $reportsResult =  $tempreport;
            return $reportsResult;
        }
    }

    // get reports operator id
    function get_reports_operator_id($reports)
    {
        if (!empty($reports)) {
            $reportsResult = array();
            $tempreport = array();
            foreach ($reports as $report) {
                $tempreport[$report['id_operator']][$report['date']] = $report;
            }
            $reportsResult =  $tempreport;
            return $reportsResult;
        }
    }

    // get country
    public function country(Request $request)
    {
        $countrys = [];
        $country_ids = [];
        $country_operator = [];
        $operators = CompanyOperators::GetOperator($request->id)->get();

        foreach ($operators as $key => $operator) {
            $country = $operator->Operator;
            if (!in_array($country[0]->country_id, $country_ids)) {
                array_push($countrys, $country[0]);
            }
            array_push($country_ids, $country[0]->country_id);
            array_push($country_operator, $country[0]);
        }

        $data = ['countrys' => $countrys, 'operators' => $country_operator];
        return $data;
    }

    // get operator
    public function operator(Request $request)
    {
        $operators = Operator::GetOperatorByCountryId($request->id)->get();
        return $operators;
    }

    // get report using operator id
    function rearrangeOperatorMonth($reports)
    {
        if (!empty($reports)) {
            $reportsResult = array();
            $tempreport = array();
            foreach ($reports as $report) {
                $tempreport[$report['operator_id']][$report['key']] = $report;
            }

            $reportsResult =  $tempreport;
            return $reportsResult;
        }
    }

    function rearrangeOperatorDate($reports)
    {
        if (!empty($reports)) {
            $reportsResult = array();
            $tempreport = array();
            foreach ($reports as $report) {
                $date = date('Y-m', strtotime($report['date']));
                $tempreport[$report['operator_id']][$date] = $report;
            }

            $reportsResult =  $tempreport;
            return $reportsResult;
        }
    }
}
