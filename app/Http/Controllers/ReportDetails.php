<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Collection;
use Session;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\report_summarize;
use App\Models\ReportsPnlsOperatorSummarizes;
use App\Models\Operator;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use App\Models\Company;
use App\Models\Country;
use App\Models\Service;
use App\Models\CompanyOperators;
use App\Models\ServiceHistory;
use App\Models\Revenushare;
use App\common\Utility;
use App\common\UtilityReports;
use App\common\UtilityMobifone;
use App\common\UtilityPercentage;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ReportDetails extends Controller
{
    public function reportingdetails(Request $request, $operator_id = '')
    {
        if (\Auth::user()->can('Reporting Details')) {
            // Default Operator ID set
            $operator_id = 8;
            $notCountry = true;
            $user = Auth::user();
            $user_id = $user->id;
            $user_type = $user->type;
            $allowAllOperator = $user->WhowAccessAlOperator($user_type);

            if (!$allowAllOperator) {
                $UserOperatorServices = Session::get('userOperatorService');
                if (empty($UserOperatorServices)) {
                    dd("Please Contact to admin , add Operator to your account");
                }

                $operator_id = $UserOperatorServices['id_operators'][0];
            }

            // get filtre request
            $CountryId = $req_CountryId = $request->country;
            $serviceId = $req_service = $request->service;
            $menu = $request->menu;
            $filterOperator = $req_filterOperator = $request->operator;
            $Start_date = $req_Start_date = trim($request->from);
            $end_date = $req_end_date = trim($request->to);

            $start_date = Carbon::now()->startOfMonth()->subDays(35)->format('Y-m-d');
            $startColumnDateDisplay = Carbon::now()->startOfMonth()->format('Y-m-d');
            $end_date = Carbon::now()->format('Y-m-d');
            $month = Carbon::now()->format('F Y');
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

            if (
                $request->filled('country') &&
                !$request->filled('operator') &&
                !$request->filled('service') &&
                $menu !== 'monitoring'
            ) {
                $Country = Country::GetCountryByCountryId([$CountryId])->first();
                $operator = Operator::GetOperatorByCountryId($Country->id)->first();

                if (!isset($operator)) {
                    dd("Please try Again :Inavlid Country ID !!!");
                }

                $operator_id = $operator->id_operator;
                $notCountry = true;
            }

            if ($request->filled('operator')) {
                $operator_id = $filterOperator;
            }

            if ($menu !== 'monitoring') {
                $operator = Operator::with('revenueshare', 'services', 'country')->find($operator_id);
            }

            $ServicesIds = null;
            $operator_details['operators'] = [];
            $operator_details['services'] = [];

            if (isset($operator)) {
                $serviceObject = $operator->services;
                if (isset($serviceObject)) {
                    $ServicesIds = $serviceObject->pluck('id_service')->toArray();

                    if ($request->filled('service')) {
                        $serviceData = Service::GetserviceById($serviceId)->first();

                        if (!isset($serviceData)) {
                            dd("Please try Again :Inavlid Service ID !!!");
                        }

                        if (!empty($ServicesIds)) {
                            if (!in_array($serviceId, $ServicesIds)) {
                                dd("Please try Again :Inavlid Service ID for that operator!!!");
                            }
                        }

                        $serviceObject = [$serviceData];
                        $ServicesIds = [$serviceId];
                    }
                }

                if ($request->filled('country')) {
                    $operator_details['operators'] = Operator::GetOperatorByCountryId($CountryId)->get();
                    $operator_details['services'] = $operator->services;
                    $notCountry = false;
                }

                if ($notCountry) {
                    $operator_details['operators'] = Operator::GetOperatorByCountryId($operator->country->id)->get();
                    $operator_details['services'] = $operator->services;
                }
            } else {
                //indonesia get 3
                if ($CountryId == 1) {
                    $operatorId = DB::select("
                            SELECT
                                rs.operator_id,
                                SUM(rs.gros_rev * (
                                    SELECT usd
                                    FROM countries
                                    WHERE id = 1
                                )) AS total_gros_rev_in_usd
                            FROM
                                report_summarize rs
                            WHERE
                                country_id = 1
                                AND (rs.date = '$start_date' OR rs.date = '$end_date')
                            GROUP BY
                                rs.operator_id
                            ORDER BY
                                total_gros_rev_in_usd DESC
                            LIMIT 3
                        ");
                    $id = [];
                    foreach ($operatorId as $v) {
                        $id[] = $v->operator_id;
                    }
                    $operator = Operator::with('revenueshare', 'services', 'country')
                        ->whereIn('id_operator', $id)
                        ->where('country_id', 1)
                        ->get();
                } else {
                    $operator = Operator::with('revenueshare', 'services', 'country')
                        ->where('country_id', $CountryId)
                        ->get();
                }

                if ($request->filled('country')) {
                    $operator_details['operators'] = Operator::GetOperatorByCountryId($CountryId)->get();
                    $operator_details['services'] = Service::join('operators', 'operators.id', 'services.operator_id')
                        ->where('operators.country_id', $CountryId)
                        ->get();
                    $notCountry = false;
                }
            }

            if ($operator instanceof \Illuminate\Database\Eloquent\Collection) {
                $data = $this->reportingDetailsArrayOperation(
                    $operator,
                    $start_date,
                    $end_date,
                    $startColumnDateDisplay,
                    $month
                );
            } else {
                $data[] = $this->reportingDetailsData(
                    $operator,
                    $start_date,
                    $end_date,
                    $startColumnDateDisplay,
                    $serviceObject,
                    $ServicesIds,
                    $month
                );
            }

            // dd($data);

            // return view('report.reportdetails', compact('sumemry', 'no_of_days', 'allsummaryData', 'operator_details'));
            return view('report.reportdetails', compact('data', 'operator_details'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    private function reportingDetailsArrayOperation(
        $operator,
        $start_date,
        $end_date,
        $startColumnDateDisplay,
        $month
    ) {
        $data = [];
        foreach ($operator as $item) {
            $serviceObject = $item->services;

            $ServicesIds = $serviceObject->pluck('id_service')->toArray();


            $data[] = $this->reportingDetailsData(
                $item,
                $start_date,
                $end_date,
                $startColumnDateDisplay,
                $serviceObject,
                $ServicesIds,
                $month
            );
        }
        return $data;
    }

    private function reportingDetailsData(
        Operator $operator,
        $start_date,
        $end_date,
        $startColumnDateDisplay,
        $serviceObject,
        $ServicesIds,
        $month
    ) {
        $sumemry = array();

        $reports = report_summarize::filterOperatorID($operator->id_operator)
            ->filterDateRange($start_date, $end_date)
            ->orderBy('operator_id')
            ->orderBy('date', 'ASC')
            ->get()
            ->toArray();

        $reportsByIDs = $this->getReportsOperatorID($reports);
        $datesIndividual = Utility::getRangeDates($startColumnDateDisplay, $end_date);
        $no_of_days = Utility::getRangeDateNo($datesIndividual);
        array_multisort($no_of_days, SORT_DESC, $no_of_days);

        /* get All country and put in an array */
        $Country = Country::all()->toArray();
        $countries = array();

        if (!empty($Country)) {
            foreach ($Country as $CountryI) {
                $countries[$CountryI['id']] = $CountryI;
            }
        }

        $allServicedata = [];

        $tmpOperators = array();

        $tmpOperators['operator'] = $operator;
        $country_id = $operator->country_id;
        $contain_id = Arr::exists($countries, $country_id);

        $OperatorCountry = array();
        $tmpOperators['selected_service'] = $ServicesIds;

        $serviceRowData = array();

        if ($contain_id) {
            $tmpOperators['country'] = $countries[$country_id];
            $OperatorCountry = $countries[$country_id];

            $reportsColumnData = $this->getReportsDateWise($operator, $no_of_days, $reportsByIDs, $OperatorCountry);

            $tmpOperators['month_string'] = $month;
            $tmpOperators['last_update'] = $reportsColumnData['last_update'];

            $total_avg_t = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['tur'], $startColumnDateDisplay, $end_date);
            $tmpOperators['tur']['dates'] = $reportsColumnData['tur'];
            $tmpOperators['tur']['total'] = $total_avg_t['sum'];
            $tmpOperators['tur']['t_mo_end'] = $total_avg_t['T_Mo_End'];
            $tmpOperators['tur']['avg'] = $total_avg_t['avg'];

            $total_avg_rev = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['t_rev'], $startColumnDateDisplay, $end_date);
            $tmpOperators['t_rev']['dates'] = $reportsColumnData['t_rev'];
            $tmpOperators['t_rev']['total'] = $total_avg_rev['sum'];
            $tmpOperators['t_rev']['t_mo_end'] = $total_avg_rev['T_Mo_End'];
            $tmpOperators['t_rev']['avg'] = $total_avg_rev['avg'];

            $total_avg_trat = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['trat'], $startColumnDateDisplay, $end_date);
            $tmpOperators['trat']['dates'] = $reportsColumnData['trat'];
            $tmpOperators['trat']['total'] = $total_avg_trat['sum'];
            $tmpOperators['trat']['t_mo_end'] = $total_avg_trat['T_Mo_End'];
            $tmpOperators['trat']['avg'] = $total_avg_trat['avg'];

            $total_avg_turt = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['turt'], $startColumnDateDisplay, $end_date);
            $tmpOperators['turt']['dates'] = $reportsColumnData['turt'];
            $tmpOperators['turt']['total'] = $total_avg_turt['sum'];
            $tmpOperators['turt']['t_mo_end'] = $total_avg_turt['T_Mo_End'];
            $tmpOperators['turt']['avg'] = $total_avg_turt['avg'];

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
            $tmpOperators['unreg']['t_mo_end'] = $total_avg_t_unreg['T_Mo_End'];
            $tmpOperators['unreg']['avg'] = $total_avg_t_unreg['avg'];

            $total_avg_t_purged = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['purged'], $startColumnDateDisplay, $end_date);
            $tmpOperators['purged']['dates'] = $reportsColumnData['purged'];
            $tmpOperators['purged']['total'] = $total_avg_t_purged['sum'];
            $tmpOperators['purged']['t_mo_end'] = $total_avg_t_purged['T_Mo_End'];
            $tmpOperators['purged']['avg'] = $total_avg_t_purged['avg'];

            $tmpOperators['churn']['dates'] = $reportsColumnData['churn'];
            $tmpOperators['churn']['total'] = 0;
            $tmpOperators['churn']['t_mo_end'] = 0;
            $tmpOperators['churn']['avg'] = 0;

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

            $tmpOperators['mt_success']['dates'] = $reportsColumnData['mt_success'];
            $tmpOperators['mt_failed']['dates'] = $reportsColumnData['mt_failed'];

            $tmpOperators['fmt_success']['dates'] = $reportsColumnData['fmt_success'];
            $tmpOperators['fmt_failed']['dates'] = $reportsColumnData['fmt_failed'];

            $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators, "tur");
            $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators, "turt");
            $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators, "t_rev");
            $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators, "net_rev");
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

            $tmpOperators['services'] = $ServicesIds;
            $AllServicereportsTogether = ServiceHistory::FilterOperator($operator->id_operator)
                ->filterService($ServicesIds)
                ->filterDateRange($start_date, $end_date)
                ->orderBy('operator_id')
                ->get()
                ->toArray();

            $reportDataServicIDWise = $this->getReportsServiceID($AllServicereportsTogether);

            if (!empty($serviceObject)) {
                foreach ($serviceObject as $key => $service) {
                    $TempserviceRowData = array();

                    $TempserviceRowData['month_string'] = $tmpOperators['month_string'];
                    $TempserviceRowData['service'] = $service;

                    $DataPrepare = $this->getReportsDateServiceWise($service->id_service, $no_of_days, $reportDataServicIDWise, $OperatorCountry, $operator);

                    $total_avg_tur = UtilityReports::calculateTotalAVG($operator, $DataPrepare['tur'], $startColumnDateDisplay, $end_date);
                    $TempserviceRowData['tur']['dates'] = $DataPrepare['tur'];
                    $TempserviceRowData['tur']['total'] = $total_avg_tur['sum'];
                    $TempserviceRowData['tur']['t_mo_end'] = $total_avg_tur['T_Mo_End'];
                    $TempserviceRowData['tur']['avg'] = $total_avg_tur['avg'];

                    $total_avg_trat = UtilityReports::calculateTotalAVG($operator, $DataPrepare['trat'], $startColumnDateDisplay, $end_date);
                    $TempserviceRowData['trat']['dates'] = $DataPrepare['trat'];
                    $TempserviceRowData['trat']['total'] = $total_avg_trat['sum'];
                    $TempserviceRowData['trat']['t_mo_end'] = $total_avg_trat['T_Mo_End'];
                    $TempserviceRowData['trat']['avg'] = $total_avg_trat['avg'];

                    $total_avg_turt = UtilityReports::calculateTotalAVG($operator, $DataPrepare['turt'], $startColumnDateDisplay, $end_date);
                    $TempserviceRowData['turt']['dates'] = $DataPrepare['turt'];
                    $TempserviceRowData['turt']['total'] = $total_avg_turt['sum'];
                    $TempserviceRowData['turt']['t_mo_end'] = $total_avg_turt['T_Mo_End'];
                    $TempserviceRowData['turt']['avg'] = $total_avg_turt['avg'];

                    $total_avg_t_rev = UtilityReports::calculateTotalAVG($operator, $DataPrepare['t_rev'], $startColumnDateDisplay, $end_date);
                    $TempserviceRowData['t_rev']['dates'] = $DataPrepare['t_rev'];
                    $TempserviceRowData['t_rev']['total'] = $total_avg_t_rev['sum'];
                    $TempserviceRowData['t_rev']['t_mo_end'] = $total_avg_t_rev['T_Mo_End'];
                    $TempserviceRowData['t_rev']['avg'] = $total_avg_t_rev['avg'];

                    $total_avg_t_rev = UtilityReports::calculateTotalAVG($operator, $DataPrepare['net_rev'], $startColumnDateDisplay, $end_date);
                    $TempserviceRowData['net_rev']['dates'] = $DataPrepare['net_rev'];
                    $TempserviceRowData['net_rev']['total'] = $total_avg_t_rev['sum'];
                    $TempserviceRowData['net_rev']['t_mo_end'] = $total_avg_t_rev['T_Mo_End'];
                    $TempserviceRowData['net_rev']['avg'] = $total_avg_t_rev['avg'];


                    $total_avg_mt_success = UtilityReports::calculateTotalAVG($operator, $DataPrepare['mt_success'], $startColumnDateDisplay, $end_date);
                    $TempserviceRowData['mt_success']['dates'] = $DataPrepare['mt_success'];
                    $TempserviceRowData['mt_success']['total'] = $total_avg_mt_success['sum'];
                    $TempserviceRowData['mt_success']['t_mo_end'] = $total_avg_mt_success['T_Mo_End'];
                    $TempserviceRowData['mt_success']['avg'] = $total_avg_mt_success['avg'];

                    $total_avg_mt_failed = UtilityReports::calculateTotalAVG($operator, $DataPrepare['mt_failed'], $startColumnDateDisplay, $end_date);
                    $TempserviceRowData['mt_failed']['dates'] = $DataPrepare['mt_failed'];
                    $TempserviceRowData['mt_failed']['total'] = $total_avg_mt_failed['sum'];
                    $TempserviceRowData['mt_failed']['t_mo_end'] = $total_avg_mt_failed['T_Mo_End'];
                    $TempserviceRowData['mt_failed']['avg'] = $total_avg_mt_failed['avg'];

                    $total_avg_fmt_success = UtilityReports::calculateTotalAVG($operator, $DataPrepare['mt_success'], $startColumnDateDisplay, $end_date);
                    $TempserviceRowData['fmt_success']['dates'] = $DataPrepare['fmt_success'];
                    $TempserviceRowData['fmt_success']['total'] = $total_avg_fmt_success['sum'];
                    $TempserviceRowData['fmt_success']['t_mo_end'] = $total_avg_fmt_success['T_Mo_End'];
                    $TempserviceRowData['fmt_success']['avg'] = $total_avg_fmt_success['avg'];

                    $total_avg_fmt_failed = UtilityReports::calculateTotalAVG($operator, $DataPrepare['mt_failed'], $startColumnDateDisplay, $end_date);
                    $TempserviceRowData['fmt_failed']['dates'] = $DataPrepare['fmt_failed'];
                    $TempserviceRowData['fmt_failed']['total'] = $total_avg_fmt_failed['sum'];
                    $TempserviceRowData['fmt_failed']['t_mo_end'] = $total_avg_fmt_failed['T_Mo_End'];
                    $TempserviceRowData['fmt_failed']['avg'] = $total_avg_fmt_failed['avg'];

                    $total_avg_t_sub = UtilityReports::calculateTotalSubscribe($operator, $DataPrepare['t_sub'], $startColumnDateDisplay, $end_date);
                    $TempserviceRowData['t_sub']['dates'] = $DataPrepare['t_sub'];
                    $TempserviceRowData['t_sub']['total'] = $total_avg_t_sub['sum'];
                    $TempserviceRowData['t_sub']['t_mo_end'] = $total_avg_t_sub['T_Mo_End'];
                    $TempserviceRowData['t_sub']['avg'] = $total_avg_t_sub['avg'];

                    $total_avg_t_reg = UtilityReports::calculateTotalAVG($operator, $DataPrepare['reg'], $startColumnDateDisplay, $end_date);
                    $TempserviceRowData['reg']['dates'] = $DataPrepare['reg'];
                    $TempserviceRowData['reg']['total'] = $total_avg_t_reg['sum'];
                    $TempserviceRowData['reg']['t_mo_end'] = $total_avg_t_reg['T_Mo_End'];
                    $TempserviceRowData['reg']['avg'] = $total_avg_t_reg['avg'];

                    $total_avg_t_unreg = UtilityReports::calculateTotalAVG($operator, $DataPrepare['unreg'], $startColumnDateDisplay, $end_date);
                    $TempserviceRowData['unreg']['dates'] = $DataPrepare['unreg'];
                    $TempserviceRowData['unreg']['total'] = $total_avg_t_unreg['sum'];
                    $TempserviceRowData['unreg']['t_mo_end'] = $total_avg_t_unreg['T_Mo_End'];
                    $TempserviceRowData['unreg']['avg'] = $total_avg_t_unreg['avg'];

                    $total_avg_t_purged = UtilityReports::calculateTotalAVG($operator, $DataPrepare['purged'], $startColumnDateDisplay, $end_date);
                    $TempserviceRowData['purged']['dates'] = $DataPrepare['purged'];
                    $TempserviceRowData['purged']['total'] = $total_avg_t_purged['sum'];
                    $TempserviceRowData['purged']['t_mo_end'] = $total_avg_t_purged['T_Mo_End'];
                    $TempserviceRowData['purged']['avg'] = $total_avg_t_purged['avg'];

                    $total_avg_t_churn = UtilityReports::calculateTotalAVG($operator, $DataPrepare['churn'], $startColumnDateDisplay, $end_date);
                    $TempserviceRowData['churn']['dates'] = $DataPrepare['churn'];
                    $TempserviceRowData['churn']['total'] = $total_avg_t_churn['sum'];
                    $TempserviceRowData['churn']['t_mo_end'] = $total_avg_t_churn['T_Mo_End'];
                    $TempserviceRowData['churn']['avg'] = $total_avg_t_churn['avg'];

                    $total_avg_t_renewal = UtilityReports::calculateTotalAVG($operator, $DataPrepare['renewal'], $startColumnDateDisplay, $end_date);
                    $TempserviceRowData['renewal']['dates'] = $DataPrepare['renewal'];
                    $TempserviceRowData['renewal']['total'] = $total_avg_t_renewal['sum'];
                    $TempserviceRowData['renewal']['t_mo_end'] = $total_avg_t_renewal['T_Mo_End'];
                    $TempserviceRowData['renewal']['avg'] = $total_avg_t_renewal['avg'];

                    $total_avg_mt_success = UtilityReports::calculateTotalAVG($operator, $DataPrepare['daily_push_success'], $startColumnDateDisplay, $end_date);
                    $TempserviceRowData['daily_push_success']['dates'] = $DataPrepare['daily_push_success'];
                    $TempserviceRowData['daily_push_success']['total'] = $total_avg_mt_success['sum'];
                    $TempserviceRowData['daily_push_success']['t_mo_end'] = $total_avg_mt_success['T_Mo_End'];
                    $TempserviceRowData['daily_push_success']['avg'] = $total_avg_mt_success['avg'];

                    $total_avg_mt_failed = UtilityReports::calculateTotalAVG($operator, $DataPrepare['daily_push_failed'], $startColumnDateDisplay, $end_date);
                    $TempserviceRowData['daily_push_failed']['dates'] = $DataPrepare['daily_push_failed'];
                    $TempserviceRowData['daily_push_failed']['total'] = $total_avg_mt_failed['sum'];
                    $TempserviceRowData['daily_push_failed']['t_mo_end'] = $total_avg_mt_failed['T_Mo_End'];
                    $TempserviceRowData['daily_push_failed']['avg'] = $total_avg_mt_failed['avg'];

                    $total_avg_bill = UtilityReports::calculateTotalAVG($operator, $DataPrepare['bill'], $startColumnDateDisplay, $end_date);
                    $TempserviceRowData['bill']['dates'] = $DataPrepare['bill'];
                    $TempserviceRowData['bill']['total'] = $total_avg_bill['sum'];
                    $TempserviceRowData['bill']['t_mo_end'] = $total_avg_bill['T_Mo_End'];
                    $TempserviceRowData['bill']['avg'] = $total_avg_bill['avg'];

                    $total_avg_first_push = UtilityReports::calculateTotalAVG($operator, $DataPrepare['first_push'], $startColumnDateDisplay, $end_date);
                    $TempserviceRowData['first_push']['dates'] = $DataPrepare['first_push'];
                    $TempserviceRowData['first_push']['total'] = $total_avg_first_push['sum'];
                    $TempserviceRowData['first_push']['t_mo_end'] = $total_avg_first_push['T_Mo_End'];
                    $TempserviceRowData['first_push']['avg'] = $total_avg_first_push['avg'];

                    $total_avg_daily_push = UtilityReports::calculateTotalAVG($operator, $DataPrepare['daily_push'], $startColumnDateDisplay, $end_date);
                    $TempserviceRowData['daily_push']['dates'] = $DataPrepare['daily_push'];
                    $TempserviceRowData['daily_push']['total'] = $total_avg_daily_push['sum'];
                    $TempserviceRowData['daily_push']['t_mo_end'] = $total_avg_daily_push['T_Mo_End'];
                    $TempserviceRowData['daily_push']['avg'] = $total_avg_daily_push['avg'];

                    $total_avg_arpu7 = UtilityReports::calculateTotalAVG($operator, $DataPrepare['arpu7'], $startColumnDateDisplay, $end_date);
                    $TempserviceRowData['arpu7']['dates'] = $DataPrepare['arpu7'];
                    $TempserviceRowData['arpu7']['total'] = $total_avg_arpu7['sum'];
                    $TempserviceRowData['arpu7']['t_mo_end'] = $total_avg_arpu7['T_Mo_End'];
                    $TempserviceRowData['arpu7']['avg'] = $total_avg_arpu7['avg'];

                    $total_avg_usarpu7 = UtilityReports::calculateTotalAVG($operator, $DataPrepare['usarpu7'], $startColumnDateDisplay, $end_date);
                    $TempserviceRowData['usarpu7']['dates'] = $DataPrepare['usarpu7'];
                    $TempserviceRowData['usarpu7']['total'] = $total_avg_usarpu7['sum'];
                    $TempserviceRowData['usarpu7']['t_mo_end'] = $total_avg_usarpu7['T_Mo_End'];
                    $TempserviceRowData['usarpu7']['avg'] = $total_avg_usarpu7['avg'];

                    $total_avg_arpu30 = UtilityReports::calculateTotalAVG($operator, $DataPrepare['arpu30'], $startColumnDateDisplay, $end_date);
                    $TempserviceRowData['arpu30']['dates'] = $DataPrepare['arpu30'];
                    $TempserviceRowData['arpu30']['total'] = $total_avg_arpu30['sum'];
                    $TempserviceRowData['arpu30']['t_mo_end'] = $total_avg_arpu30['T_Mo_End'];
                    $TempserviceRowData['arpu30']['avg'] = $total_avg_arpu30['avg'];

                    $total_avg_usarpu30 = UtilityReports::calculateTotalAVG($operator, $DataPrepare['usarpu30'], $startColumnDateDisplay, $end_date);
                    $TempserviceRowData['usarpu30']['dates'] = $DataPrepare['usarpu30'];
                    $TempserviceRowData['usarpu30']['total'] = $total_avg_usarpu30['sum'];
                    $TempserviceRowData['usarpu30']['t_mo_end'] = $total_avg_usarpu30['T_Mo_End'];
                    $TempserviceRowData['usarpu30']['avg'] = $total_avg_usarpu30['avg'];

                    $total_avg_rev_after = UtilityReports::calculateTotalAVG($operator, $DataPrepare['rev_after_share_usd'], $startColumnDateDisplay, $end_date);
                    $TempserviceRowData['rev_after_share_usd']['dates'] = $DataPrepare['rev_after_share_usd'];
                    $TempserviceRowData['rev_after_share_usd']['total'] = $total_avg_rev_after['sum'];
                    $TempserviceRowData['rev_after_share_usd']['t_mo_end'] = $total_avg_rev_after['T_Mo_End'];
                    $TempserviceRowData['rev_after_share_usd']['avg'] = $total_avg_rev_after['avg'];

                    $total_avg_usd_rev_after = UtilityReports::calculateTotalAVG($operator, $DataPrepare['usd_rev_after_share'], $startColumnDateDisplay, $end_date);
                    $TempserviceRowData['usd_rev_after_share']['dates'] = $DataPrepare['usd_rev_after_share'];
                    $TempserviceRowData['usd_rev_after_share']['total'] = $total_avg_usd_rev_after['sum'];
                    $TempserviceRowData['usd_rev_after_share']['t_mo_end'] = $total_avg_usd_rev_after['T_Mo_End'];
                    $TempserviceRowData['usd_rev_after_share']['avg'] = $total_avg_usd_rev_after['avg'];

                    $serviceRowData[] = $TempserviceRowData;
                }
            }
        }

        $tmpOperators['services'] = $serviceRowData;
        $allServicedata = $serviceRowData;
        $sumemry[] = $tmpOperators;

        $allsummaryData = [];
        if (sizeof($allServicedata) > 0) {

            /*sum of all summery datas*/
            $allsummaryData = UtilityReports::alldetailsData($allServicedata);
            $allsummaryData = UtilityReports::allSummeryPerCal($allsummaryData);

            $allsummaryData['arpu7'] = $tmpOperators['arpu7'];
            $allsummaryData['usarpu7'] = $tmpOperators['usarpu7'];
            $allsummaryData['arpu30'] = $tmpOperators['arpu30'];
            $allsummaryData['usarpu30'] = $tmpOperators['usarpu30'];

            /*put color code into all summary data*/
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "tur");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "turt");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "t_rev");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "net_rev");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "t_sub");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "trat");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "reg");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "unreg");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "purged");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "churn");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "renewal");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "bill");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "first_push");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "daily_push");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "first_push");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "arpu7");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "usarpu7");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "arpu30");
            $allsummaryData = UtilityReports::ColorFirstDay($allsummaryData, "usarpu30");
        }

        return [
            'sumemry' => $sumemry,
            'no_of_days' => $no_of_days,
            'allsummaryData' => $allsummaryData
        ];
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

    // get reports by operator id
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

    // get reports by service id
    function getReportsServiceID($reports)
    {
        if (!empty($reports)) {
            $reportsResult = array();
            $tempreport = array();

            foreach ($reports as $report) {
                $tempreport[$report['id_service']][$report['date']] = $report;
            }

            $reportsResult = $tempreport;
            return $reportsResult;
        }
    }

    // get reports date wise
    function getReportsDateWise($operator, $no_of_days, $reportsByIDs, $OperatorCountry)
    {
        if (\Auth::user()->can('Reporting Details')) {
            $usdValue = isset($OperatorCountry['usd']) ? $OperatorCountry['usd'] : 1;
            $shareDb = array();
            $merchent_share = 1;
            $operator_share = 1;
            $country_id = $OperatorCountry['id'];

            $revenue_share = null;
            if ($operator) {
                $revenue_share = $operator->revenueshare;
            }

            if (isset($revenue_share)) {
                $merchent_share = $revenue_share->merchant_revenue_share;
                $operator_share = $revenue_share->operator_revenue_share;
            }
            $vat = 0;
            $wht = 0;
            $VatByDate = $operator->VatByDate;
            $WhtByDate = $operator->WhtByDate;
            $misc_taxByDate = $operator->MiscTax;

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
            $shareDb['merchent_share'] = $merchent_share;
            $shareDb['operator_share'] = $operator_share;

            if (!empty($no_of_days)) {
                $allColumnData = array();
                $tur = array();
                $t_rev = array();
                $trat = array();
                $turt = array();
                $t_sub = array();
                $net_rev = array();
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
                $id_operator = $operator->id_operator ?? 0;
                $testUSDSum = 0;
                $last_update = "";
                $update = false;

                foreach ($no_of_days as $days) {
                    $keys = $id_operator . "." . $days['date'];
                    $key_date = new Carbon($days['date']);
                    $key = $key_date->format("Y-m");
                    $summariserow = Arr::get($reportsByIDs, $keys, 0);

                    if ($summariserow != 0  && !$update) {
                        $update = true;
                        $last_update = $summariserow['updated_at'];
                    }

                    $gros_rev = isset($summariserow['gros_rev']) ? $summariserow['gros_rev'] : 0;
                    $total_subscriber = isset($summariserow['total']) ? $summariserow['total'] : 0;
                    $gros_rev_Usd = 0;

                    if ($country_id == 142) {
                        $gros_rev = $gros_rev / 1000;
                    }

                    $gros_rev_Usd = $gros_rev * $usdValue;

                    $testUSDSum =  $testUSDSum + $gros_rev_Usd;
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
                    $FirstPush = UtilityReports::FirstPush($fmt_success, $fmt_failed, $total_subscriber);
                    $Dailypush = UtilityReports::Dailypush($mt_success, $mt_failed, $total_subscriber);

                    $arpu7Data = UtilityReports::Arpu7($operator, $reportsByIDs, $days, $total_subscriber, $shareDb);
                    $arpu7USD = $arpu7Data * $usdValue;

                    $arpuRawdata = UtilityPercentage::Arpu7Raw($operator, $reportsByIDs, $days, $total_subscriber, $shareDb, $OperatorCountry);

                    $arpu30Data = UtilityReports::Arpu30($operator, $reportsByIDs, $days, $total_subscriber, $shareDb);
                    $arpu30USD = $arpu30Data * $usdValue;

                    if ($country_id == 142) {
                        $arpu7USD = $arpu7USD / 1000;
                        $arpu30USD = $arpu30USD / 1000;
                    }

                    $arpu30Rawdata = UtilityPercentage::Arpu30Raw($operator, $reportsByIDs, $days, $total_subscriber, $shareDb, $OperatorCountry);

                    $tratData = UtilityReports::trat($shareDb, $gros_rev);
                    $turtData = UtilityReports::turt($shareDb, $gros_rev_Usd);

                    $vat = !empty($operator->vat) ? $turtData * ($operator->vat / 100) : 0;
                    $wht = !empty($operator->wht) ? $turtData * ($operator->wht / 100) : 0;
                    $misc_tax = !empty($operator->miscTax) ? $turtData * ($operator->miscTax / 100) : 0;

                    if(isset($VatByDate[$key]))
                    {
                        $Vat = $VatByDate[$key]->vat;
                        $vat = !empty($Vat) ? $turtData * ($Vat/100) : 0;

                    }
                    if(isset($WhtByDate[$key]))
                    {
                        $Wht = $WhtByDate[$key]->wht;
                        $wht = !empty($Wht) ? $turtData * ($Wht/100) : 0;

                    }
                    if(isset($misc_taxByDate[$key]))
                {
                    $Misc_tax = $misc_taxByDate[$key]->misc_tax;
                    $misc_tax = !empty($Misc_tax) ? $turtData * ($Misc_tax/100) : 0;

                }
                    $other_tax = $vat + $wht + $misc_tax;

                    if ($other_tax != 0) {
                        $netRev = $turtData - $other_tax;
                    } else {
                        $netRev = $turtData;
                    }

                    $tur[$days['date']]['value'] = $gros_rev_Usd;
                    $tur[$days['date']]['class'] = "";

                    $t_rev[$days['date']]['value'] = $gros_rev;
                    $t_rev[$days['date']]['class'] = "bg-hui";

                    $net_rev[$days['date']]['value'] = $netRev;
                    $net_rev[$days['date']]['class'] = "bg-hui";

                    $trat[$days['date']]['value'] = $tratData;
                    $trat[$days['date']]['class'] = "bg-hui";

                    $turt[$days['date']]['value'] = $turtData;
                    $turt[$days['date']]['class'] = "bg-hui";

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

                    $arpu7Raw[$days['date']]['value'] = $arpuRawdata;
                    $arpu7Raw[$days['date']]['class'] = "bg-hui";

                    $arpu30Raw[$days['date']]['value'] = $arpu30Rawdata;
                    $arpu30Raw[$days['date']]['class'] = "bg-hui";
                }

                $last_update_show = "Not updated last month";

                if ($last_update != "") {
                    $last_update_timestamp = Carbon::parse($last_update);
                    $last_update_timestamp->setTimezone('Asia/Jakarta');
                    $last_update_show = $last_update_timestamp->format("Y-m-d H:i:s") . " Asia/Jakarta";
                }

                $allColumnData['tur'] = $tur;
                $allColumnData['t_rev'] = $t_rev;
                $allColumnData['net_rev'] = $net_rev;
                $allColumnData['trat'] = $trat;
                $allColumnData['turt'] = $turt;
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
                $allColumnData['last_update'] = $last_update_show;

                return $allColumnData;
            }
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    function getReportsDateServiceWise($serviceId, $no_of_days, $reportsByIDs, $OperatorCountry, $operator)
    {
        if (\Auth::user()->can('Reporting Details')) {
            $usdValue = $OperatorCountry['usd'];
            $merchent_share = 1;
            $operator_share = 1;
            $revenue_share = $operator->revenueshare;
            $country_id = $OperatorCountry['id'];

            if (isset($revenue_share)) {
                $merchent_share = $revenue_share->merchant_revenue_share;
                $operator_share = $revenue_share->operator_revenue_share;
            }

            $shareDb['merchent_share'] = $merchent_share;
            $shareDb['operator_share'] = $operator_share;

            if (!empty($no_of_days)) {
                $allColumnData = array();
                $tur = array();
                $t_rev = array();
                $net_rev = array();
                $trat = array();
                $turt = array();
                $mt_success = array();
                $mt_failed = array();
                $fmt_success = array();
                $fmt_failed = array();
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
                $rev_after_share_usd = array();
                $usd_rev_after_share = array();

                $id_operator = $operator->id_operator;

                foreach ($no_of_days as $days) {
                    $keys = $serviceId . "." . $days['date'];

                    $summariserow = Arr::get($reportsByIDs, $keys, 0);

                    $gros_rev = isset($summariserow['gros_rev']) ? $summariserow['gros_rev'] : 0;

                    if ($country_id == 142) {
                        $gros_rev = $gros_rev / 1000;
                    }

                    $total_subscriber = isset($summariserow['total']) ? $summariserow['total'] : 0;
                    $gros_rev_Usd = $gros_rev * $usdValue;
                    $total_reg = isset($summariserow['total_reg']) ? $summariserow['total_reg'] : 0;
                    $total_unreg = isset($summariserow['total_unreg']) ? $summariserow['total_unreg'] : 0;
                    $purge_total = isset($summariserow['purge_total']) ? $summariserow['purge_total'] : 0;
                    $mt_success = isset($summariserow['mt_success']) ? $summariserow['mt_success'] : 0;
                    $mt_failed = isset($summariserow['mt_failed']) ? $summariserow['mt_failed'] : 0;
                    $fmt_success = isset($summariserow['fmt_success']) ? $summariserow['fmt_success'] : 0;
                    $fmt_failed = isset($summariserow['fmt_failed']) ? $summariserow['fmt_failed'] : 0;

                    $RevAfterShareUsd = isset($operator->revenueshare->merchant_revenue_share) ? ($gros_rev * (float)$operator->revenueshare->merchant_revenue_share) / 100 : 0.00;

                    $UsdRevAfterShare = isset($operator->revenueshare->merchant_revenue_share) ? ($gros_rev_Usd * (float)$operator->revenueshare->merchant_revenue_share) / 100 : 0.00;

                    if ($total_subscriber > 0) {
                        $churn_value = ((int)$total_unreg  / (int)$total_subscriber) * 100;

                        $churn_value = sprintf('%0.2f', $churn_value);
                    } else {
                        $churn_value = 0;
                    }

                    $renewal_total = $mt_success + $mt_failed;
                    $billRate = UtilityReports::billRate($mt_success, $mt_failed, $total_subscriber);
                    $billRate = sprintf('%0.2f', $billRate);

                    $FirstPush = UtilityReports::FirstPush($fmt_success, $fmt_failed, $total_subscriber);
                    $FirstPush = sprintf('%0.2f', $FirstPush);

                    $Dailypush = UtilityReports::Dailypush($mt_success, $mt_failed, $total_subscriber);
                    $Dailypush = sprintf('%0.2f', $Dailypush);

                    $tratData = UtilityReports::trat($shareDb, $gros_rev);
                    $turtData = UtilityReports::turt($shareDb, $gros_rev_Usd);

                    $vat = !empty($operator->vat) ? $turtData * ($operator->vat / 100) : 0;

                    $wht = !empty($operator->wht) ? $turtData * ($operator->wht / 100) : 0;

                    $miscTax = !empty($operator->miscTax) ? $turtData * ($operator->miscTax / 100) : 0;

                    $other_tax = $vat + $wht + $miscTax;

                    if ($other_tax != 0) {
                        $netRev = $turtData - $other_tax;
                        $tratData = $turtData / $usdValue;
                    } else {
                        $netRev = $turtData;
                    }
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

                    $mt_succes[$days['date']]['value'] = $mt_success;
                    $mt_succes[$days['date']]['class'] = "bg-hui";

                    $mt_fail[$days['date']]['value'] = $mt_failed;
                    $mt_fail[$days['date']]['class'] = "bg-hui";

                    $fmt_succes[$days['date']]['value'] = $fmt_success;
                    $fmt_succes[$days['date']]['class'] = "bg-hui";

                    $fmt_fail[$days['date']]['value'] = $fmt_failed;
                    $fmt_fail[$days['date']]['class'] = "bg-hui";

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

                    $arpu7[$days['date']]['value'] = 0;
                    $arpu7[$days['date']]['class'] = "bg-hui";

                    $usarpu7[$days['date']]['value'] = 0;
                    $usarpu7[$days['date']]['class'] = "bg-hui";

                    $arpu30[$days['date']]['value'] = 0;
                    $arpu30[$days['date']]['class'] = "bg-hui";

                    $usarpu30[$days['date']]['value'] = 0;
                    $usarpu30[$days['date']]['class'] = "bg-hui";

                    $rev_after_share_usd[$days['date']]['value'] = $RevAfterShareUsd;
                    $rev_after_share_usd[$days['date']]['class'] = "bg-hui";

                    $usd_rev_after_share[$days['date']]['value'] = $UsdRevAfterShare;
                    $usd_rev_after_share[$days['date']]['class'] = "bg-hui";
                }

                $allColumnData['tur'] = $tur;
                $allColumnData['t_rev'] = $t_rev;
                $allColumnData['trat'] = $trat;
                $allColumnData['turt'] = $turt;
                $allColumnData['net_rev'] = $net_rev;
                $allColumnData['mt_success'] = $mt_succes;
                $allColumnData['mt_failed'] = $mt_fail;
                $allColumnData['fmt_success'] = $fmt_succes;
                $allColumnData['fmt_failed'] = $fmt_fail;
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
                $allColumnData['rev_after_share_usd'] = $rev_after_share_usd;
                $allColumnData['usd_rev_after_share'] = $usd_rev_after_share;

                return $allColumnData;
            }
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function getReportDataFilter(Request $request)
    {
        print_r($request->all());
    }

    function PrepareServiceHistoryData($tmpOperators, $operator, $OperatorCountry)
    {
        return $tmpOperators;
    }

    public function reportPnlDetails(Request $request, $operator_id = '')
    {
        if (\Auth::user()->can('PNL Detail')) {
            $operator_id = 1;
            $notCountry = true;
            $user = Auth::user();
            $user_id = $user->id;
            $user_type = $user->type;
            $allowAllOperator = $user->WhowAccessAlOperator($user_type);

            if (!$allowAllOperator) {
                $UserOperatorServices = Session::get('userOperatorService');

                if (empty($UserOperatorServices)) {
                    dd("Please Contact to admin , add Operator to your account");
                }

                $operator_id = $UserOperatorServices['id_operators'][0];
            }

            // get filtre request
            $CountryId = $req_CountryId = $request->country;
            $serviceId = $req_service = $request->service;
            $filterOperator = $req_filterOperator = $request->operator;
            $Start_date = $req_Start_date = trim($request->from);
            $end_date = $req_end_date = trim($request->to);

            $start_date = Carbon::now()->startOfMonth()->subDays(35)->format('Y-m-d');
            $startColumnDateDisplay = Carbon::now()->startOfMonth()->format('Y-m-d');
            $end_date = Carbon::now()->format('Y-m-d');
            $month = Carbon::now()->format('F Y');
            $showAllOperator = true;

            if ($request->filled('to') && $request->filled('from')) {
                $start_date_input = new Carbon($req_Start_date);
                $display_date_input = new Carbon($req_Start_date);

                $start_date = $start_date_input->subDays(35)->format('Y-m-d');
                $startColumnDateDisplay = $display_date_input->format('Y-m-d');

                $end_date = $req_end_date;
                $month = $display_date_input->format('F Y');
            }

            if ($request->filled('country') && !$request->filled('operator') && !$request->filled('service')) {
                $Country = Country::GetCountryByCountryId([$CountryId])->first();
                $operator_id = Operator::GetOperatorByCountryId($Country->id)->first();

                if (!isset($operator_id)) {
                    dd("Please try Again :Inavlid Country ID !!!");
                }

                $operator_id = $operator_id->id_operator;
                $notCountry = false;
            }

            if ($request->filled('operator')) {
                $operator_id = $filterOperator;
            }

            $operator = Operator::with('revenueshare', 'services', 'country')->FindOrFail($operator_id);

            $serviceObject = $operator->services;
            $ServicesIds = $serviceObject->pluck('id_service')->toArray();

            if ($request->filled('service')) {
                $serviceData = Service::GetserviceById($serviceId)->first();

                if (!isset($serviceData)) {
                    dd("Please try Again :Inavlid Service ID !!!");
                }

                if (!empty($ServicesIds)) {
                    if (!in_array($serviceId, $ServicesIds)) {
                        dd("Please try Again :Inavlid Service ID for that operator!!!");
                    }
                }

                $serviceObject = [$serviceData];
                $ServicesIds = [$serviceId];
            }

            if ($request->filled('country')) {
                $operator_details['operators'] = Operator::GetOperatorByCountryId($CountryId)->get();
                $operator_details['services'] = $operator->services;
                $notCountry = false;
            }

            if ($notCountry) {
                $operator_details['operators'] = Operator::GetOperatorByCountryId($operator->country->id)->get();
                $operator_details['services'] = $operator->services;
            }

            $sumemry = array();

            $reports = ReportsPnlsOperatorSummarizes::filterOperatorID($operator_id)
                ->filterDateRange($start_date, $end_date)
                ->orderBy('id_operator')
                ->orderBy('date', 'ASC')
                ->get()
                ->toArray();

            $reportsByIDs = $this->getReportsByOperator($reports);
            $datesIndividual = Utility::getRangeDates($startColumnDateDisplay, $end_date);
            $no_of_days = Utility::getRangeDateNo($datesIndividual);
            array_multisort($no_of_days, SORT_DESC, $no_of_days);

            /* get All country and put in an array */
            $Country = Country::all()->toArray();
            $countries = array();
            if (!empty($Country)) {
                foreach ($Country as $CountryI) {
                    $countries[$CountryI['id']] = $CountryI;
                }
            }

            if (!empty($operator)) {
                $tmpOperators = array();
                $tmpOperators['operator'] = $operator;
                $country_id = $operator->country_id;
                $contain_id = Arr::exists($countries, $country_id);
                $OperatorCountry = array();


                if ($contain_id) {
                    $tmpOperators['country'] = $countries[$country_id];
                    $OperatorCountry = $countries[$country_id];

                    $reportsColumnData = $this->getPNLReportsDateWise($operator, $no_of_days, $reportsByIDs, $OperatorCountry);
                    $tmpOperators['month_string'] = $month;
                    $tmpOperators['last_update'] = $reportsColumnData['last_update'];

                    $total_avg_rev_usd = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['end_user_rev_usd'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['end_user_rev_usd']['dates'] = $reportsColumnData['end_user_rev_usd'];
                    $tmpOperators['end_user_rev_usd']['total'] = $total_avg_rev_usd['sum'];
                    $tmpOperators['end_user_rev_usd']['t_mo_end'] = $total_avg_rev_usd['T_Mo_End'];
                    $tmpOperators['end_user_rev_usd']['avg'] = $total_avg_rev_usd['avg'];

                    $total_avg_rev = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['end_user_rev'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['end_user_rev']['dates'] = $reportsColumnData['end_user_rev'];
                    $tmpOperators['end_user_rev']['total'] = $total_avg_rev['sum'];
                    $tmpOperators['end_user_rev']['t_mo_end'] = $total_avg_rev['T_Mo_End'];
                    $tmpOperators['end_user_rev']['avg'] = $total_avg_rev['avg'];

                    $total_avg_gros_rev_usd = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['gros_rev_usd'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['gros_rev_usd']['dates'] = $reportsColumnData['gros_rev_usd'];
                    $tmpOperators['gros_rev_usd']['total'] = $total_avg_gros_rev_usd['sum'];
                    $tmpOperators['gros_rev_usd']['t_mo_end'] = $total_avg_gros_rev_usd['T_Mo_End'];
                    $tmpOperators['gros_rev_usd']['avg'] = $total_avg_gros_rev_usd['avg'];

                    $total_avg_gros_rev = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['gros_rev'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['gros_rev']['dates'] = $reportsColumnData['gros_rev'];
                    $tmpOperators['gros_rev']['total'] = $total_avg_gros_rev['sum'];
                    $tmpOperators['gros_rev']['t_mo_end'] = $total_avg_gros_rev['T_Mo_End'];
                    $tmpOperators['gros_rev']['avg'] = $total_avg_gros_rev['avg'];

                    $total_avg_cost_campaign = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['cost_campaign'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['cost_campaign']['dates'] = $reportsColumnData['cost_campaign'];
                    $tmpOperators['cost_campaign']['total'] = $total_avg_cost_campaign['sum'];
                    $tmpOperators['cost_campaign']['t_mo_end'] = $total_avg_cost_campaign['T_Mo_End'];
                    $tmpOperators['cost_campaign']['avg'] = $total_avg_cost_campaign['avg'];

                    $total_avg_other_cost = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['other_cost'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['other_cost']['dates'] = $reportsColumnData['other_cost'];
                    $tmpOperators['other_cost']['total'] = $total_avg_other_cost['sum'];
                    $tmpOperators['other_cost']['t_mo_end'] = $total_avg_other_cost['T_Mo_End'];
                    $tmpOperators['other_cost']['avg'] = $total_avg_other_cost['avg'];

                    $total_avg_hosting_cost = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['hosting_cost'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['hosting_cost']['dates'] = $reportsColumnData['hosting_cost'];
                    $tmpOperators['hosting_cost']['total'] = $total_avg_hosting_cost['sum'];
                    $tmpOperators['hosting_cost']['t_mo_end'] = $total_avg_hosting_cost['T_Mo_End'];
                    $tmpOperators['hosting_cost']['avg'] = $total_avg_hosting_cost['avg'];

                    $total_avg_content = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['content'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['content']['dates'] = $reportsColumnData['content'];
                    $tmpOperators['content']['total'] = $total_avg_content['sum'];
                    $tmpOperators['content']['t_mo_end'] = $total_avg_content['T_Mo_End'];
                    $tmpOperators['content']['avg'] = $total_avg_content['avg'];

                    $total_avg_rnd = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['rnd'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['rnd']['dates'] = $reportsColumnData['rnd'];
                    $tmpOperators['rnd']['total'] = $total_avg_rnd['sum'];
                    $tmpOperators['rnd']['t_mo_end'] = $total_avg_rnd['T_Mo_End'];
                    $tmpOperators['rnd']['avg'] = $total_avg_rnd['avg'];

                    $total_avg_bd = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['bd'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['bd']['dates'] = $reportsColumnData['bd'];
                    $tmpOperators['bd']['total'] = $total_avg_bd['sum'];
                    $tmpOperators['bd']['t_mo_end'] = $total_avg_bd['T_Mo_End'];
                    $tmpOperators['bd']['avg'] = $total_avg_bd['avg'];

                    $total_avg_platform = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['platform'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['platform']['dates'] = $reportsColumnData['platform'];
                    $tmpOperators['platform']['total'] = $total_avg_platform['sum'];
                    $tmpOperators['platform']['t_mo_end'] = $total_avg_platform['T_Mo_End'];
                    $tmpOperators['platform']['avg'] = $total_avg_platform['avg'];

                    $total_avg_pnl = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['pnl'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['pnl']['dates'] = $reportsColumnData['pnl'];
                    $tmpOperators['pnl']['total'] = $total_avg_pnl['sum'];
                    $tmpOperators['pnl']['t_mo_end'] = $total_avg_pnl['T_Mo_End'];
                    $tmpOperators['pnl']['avg'] = $total_avg_pnl['avg'];

                    $total_avg_net_after_tax = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['net_after_tax'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['net_after_tax']['dates'] = $reportsColumnData['net_after_tax'];
                    $tmpOperators['net_after_tax']['total'] = $total_avg_net_after_tax['sum'];
                    $tmpOperators['net_after_tax']['t_mo_end'] = $total_avg_net_after_tax['T_Mo_End'];
                    $tmpOperators['net_after_tax']['avg'] = $total_avg_net_after_tax['avg'];

                    $total_avg_net_revenue_after_tax = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['net_revenue_after_tax'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['net_revenue_after_tax']['dates'] = $reportsColumnData['net_revenue_after_tax'];
                    $tmpOperators['net_revenue_after_tax']['total'] = $total_avg_net_revenue_after_tax['sum'];
                    $tmpOperators['net_revenue_after_tax']['t_mo_end'] = $total_avg_net_revenue_after_tax['T_Mo_End'];
                    $tmpOperators['net_revenue_after_tax']['avg'] = $total_avg_net_revenue_after_tax['avg'];

                    $total_avg_br = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['br'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['br']['dates'] = $reportsColumnData['br'];
                    $tmpOperators['br']['total'] = $total_avg_br['sum'];
                    $tmpOperators['br']['t_mo_end'] = $total_avg_br['T_Mo_End'];
                    $tmpOperators['br']['avg'] = $total_avg_br['avg'];

                    $total_avg_fp = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['fp'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['fp']['dates'] = $reportsColumnData['fp'];
                    $tmpOperators['fp']['total'] = $total_avg_fp['sum'];
                    $tmpOperators['fp']['t_mo_end'] = $total_avg_fp['T_Mo_End'];
                    $tmpOperators['fp']['avg'] = $total_avg_fp['avg'];

                    $total_avg_fp_success = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['fp_success'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['fp_success']['dates'] = $reportsColumnData['fp_success'];
                    $tmpOperators['fp_success']['total'] = $total_avg_fp_success['sum'];
                    $tmpOperators['fp_success']['t_mo_end'] = $total_avg_fp_success['T_Mo_End'];
                    $tmpOperators['fp_success']['avg'] = $total_avg_fp_success['avg'];

                    $total_avg_dp = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['dp'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['dp']['dates'] = $reportsColumnData['dp'];
                    $tmpOperators['dp']['total'] = $total_avg_dp['sum'];
                    $tmpOperators['dp']['t_mo_end'] = $total_avg_dp['T_Mo_End'];
                    $tmpOperators['dp']['avg'] = $total_avg_dp['avg'];

                    $total_avg_dp_success = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['dp_success'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['dp_success']['dates'] = $reportsColumnData['dp_success'];
                    $tmpOperators['dp_success']['total'] = $total_avg_dp_success['sum'];
                    $tmpOperators['dp_success']['t_mo_end'] = $total_avg_dp_success['T_Mo_End'];
                    $tmpOperators['dp_success']['avg'] = $total_avg_dp_success['avg'];

                    $total_avg_vat = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['vat'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['vat']['dates'] = $reportsColumnData['vat'];
                    $tmpOperators['vat']['total'] = $total_avg_vat['sum'];
                    $tmpOperators['vat']['t_mo_end'] = $total_avg_vat['T_Mo_End'];
                    $tmpOperators['vat']['avg'] = $total_avg_vat['avg'];

                    $total_avg_spec_tax = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['spec_tax'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['spec_tax']['dates'] = $reportsColumnData['spec_tax'];
                    $tmpOperators['spec_tax']['total'] = $total_avg_spec_tax['sum'];
                    $tmpOperators['spec_tax']['t_mo_end'] = $total_avg_spec_tax['T_Mo_End'];
                    $tmpOperators['spec_tax']['avg'] = $total_avg_spec_tax['avg'];

                    $total_avg_government_cost = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['government_cost'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['government_cost']['dates'] = $reportsColumnData['government_cost'];
                    $tmpOperators['government_cost']['total'] = $total_avg_government_cost['sum'];
                    $tmpOperators['government_cost']['t_mo_end'] = $total_avg_government_cost['T_Mo_End'];
                    $tmpOperators['government_cost']['avg'] = $total_avg_government_cost['avg'];

                    $total_avg_dealer_commision = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['dealer_commision'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['dealer_commision']['dates'] = $reportsColumnData['dealer_commision'];
                    $tmpOperators['dealer_commision']['total'] = $total_avg_dealer_commision['sum'];
                    $tmpOperators['dealer_commision']['t_mo_end'] = $total_avg_dealer_commision['T_Mo_End'];
                    $tmpOperators['dealer_commision']['avg'] = $total_avg_dealer_commision['avg'];

                    $total_avg_wht = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['wht'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['wht']['dates'] = $reportsColumnData['wht'];
                    $tmpOperators['wht']['total'] = $total_avg_wht['sum'];
                    $tmpOperators['wht']['t_mo_end'] = $total_avg_wht['T_Mo_End'];
                    $tmpOperators['wht']['avg'] = $total_avg_wht['avg'];

                    $total_avg_misc_tax = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['misc_tax'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['misc_tax']['dates'] = $reportsColumnData['misc_tax'];
                    $tmpOperators['misc_tax']['total'] = $total_avg_misc_tax['sum'];
                    $tmpOperators['misc_tax']['t_mo_end'] = $total_avg_misc_tax['T_Mo_End'];
                    $tmpOperators['misc_tax']['avg'] = $total_avg_misc_tax['avg'];

                    $total_avg_uso = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['uso'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['uso']['dates'] = $reportsColumnData['uso'];
                    $tmpOperators['uso']['total'] = $total_avg_uso['sum'];
                    $tmpOperators['uso']['t_mo_end'] = $total_avg_uso['T_Mo_End'];
                    $tmpOperators['uso']['avg'] = $total_avg_uso['avg'];

                    $total_avg_agre_paxxa = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['agre_paxxa'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['agre_paxxa']['dates'] = $reportsColumnData['agre_paxxa'];
                    $tmpOperators['agre_paxxa']['total'] = $total_avg_agre_paxxa['sum'];
                    $tmpOperators['agre_paxxa']['t_mo_end'] = $total_avg_agre_paxxa['T_Mo_End'];
                    $tmpOperators['agre_paxxa']['avg'] = $total_avg_agre_paxxa['avg'];

                    $total_avg_sbaf = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['sbaf'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['sbaf']['dates'] = $reportsColumnData['sbaf'];
                    $tmpOperators['sbaf']['total'] = $total_avg_sbaf['sum'];
                    $tmpOperators['sbaf']['t_mo_end'] = $total_avg_sbaf['T_Mo_End'];
                    $tmpOperators['sbaf']['avg'] = $total_avg_sbaf['avg'];

                    $total_avg_clicks = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['clicks'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['clicks']['dates'] = $reportsColumnData['clicks'];
                    $tmpOperators['clicks']['total'] = $total_avg_clicks['sum'];
                    $tmpOperators['clicks']['t_mo_end'] = $total_avg_clicks['T_Mo_End'];
                    $tmpOperators['clicks']['avg'] = $total_avg_clicks['avg'];

                    $total_avg_ratio_for_cpa = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['ratio_for_cpa'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['ratio_for_cpa']['dates'] = $reportsColumnData['ratio_for_cpa'];
                    $tmpOperators['ratio_for_cpa']['total'] = $total_avg_ratio_for_cpa['sum'];
                    $tmpOperators['ratio_for_cpa']['t_mo_end'] = $total_avg_ratio_for_cpa['T_Mo_End'];
                    $tmpOperators['ratio_for_cpa']['avg'] = $total_avg_ratio_for_cpa['avg'];

                    $total_avg_cpa_price = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['cpa_price'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['cpa_price']['dates'] = $reportsColumnData['cpa_price'];
                    $tmpOperators['cpa_price']['total'] = $total_avg_cpa_price['sum'];
                    $tmpOperators['cpa_price']['t_mo_end'] = $total_avg_cpa_price['T_Mo_End'];
                    $tmpOperators['cpa_price']['avg'] = $total_avg_cpa_price['avg'];

                    $total_avg_cr_mo_clicks = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['cr_mo_clicks'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['cr_mo_clicks']['dates'] = $reportsColumnData['cr_mo_clicks'];
                    $tmpOperators['cr_mo_clicks']['total'] = $total_avg_cr_mo_clicks['sum'];
                    $tmpOperators['cr_mo_clicks']['t_mo_end'] = $total_avg_cr_mo_clicks['T_Mo_End'];
                    $tmpOperators['cr_mo_clicks']['avg'] = $total_avg_cr_mo_clicks['avg'];

                    $total_avg_cr_mo_landing = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['cr_mo_landing'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['cr_mo_landing']['dates'] = $reportsColumnData['cr_mo_landing'];
                    $tmpOperators['cr_mo_landing']['total'] = $total_avg_cr_mo_landing['sum'];
                    $tmpOperators['cr_mo_landing']['t_mo_end'] = $total_avg_cr_mo_landing['T_Mo_End'];
                    $tmpOperators['cr_mo_landing']['avg'] = $total_avg_cr_mo_landing['avg'];

                    $total_avg_landing = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['landing'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['landing']['dates'] = $reportsColumnData['landing'];
                    $tmpOperators['landing']['total'] = $total_avg_landing['sum'];
                    $tmpOperators['landing']['t_mo_end'] = $total_avg_landing['T_Mo_End'];
                    $tmpOperators['landing']['avg'] = $total_avg_landing['avg'];

                    $total_avg_mo = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['mo'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['mo']['dates'] = $reportsColumnData['mo'];
                    $tmpOperators['mo']['total'] = $total_avg_mo['sum'];
                    $tmpOperators['mo']['t_mo_end'] = $total_avg_mo['T_Mo_End'];
                    $tmpOperators['mo']['avg'] = $total_avg_mo['avg'];

                    $total_avg_unreg = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['unreg'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['unreg']['dates'] = $reportsColumnData['unreg'];
                    $tmpOperators['unreg']['total'] = $total_avg_unreg['sum'];
                    $tmpOperators['unreg']['t_mo_end'] = $total_avg_unreg['T_Mo_End'];
                    $tmpOperators['unreg']['avg'] = $total_avg_unreg['avg'];

                    $total_avg_price_mo = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['price_mo'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['price_mo']['dates'] = $reportsColumnData['price_mo'];
                    $tmpOperators['price_mo']['total'] = $total_avg_price_mo['sum'];
                    $tmpOperators['price_mo']['t_mo_end'] = $total_avg_price_mo['T_Mo_End'];
                    $tmpOperators['price_mo']['avg'] = $total_avg_price_mo['avg'];

                    $total_avg_active_subs = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['active_subs'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['active_subs']['dates'] = $reportsColumnData['active_subs'];
                    $tmpOperators['active_subs']['total'] = $total_avg_active_subs['sum'];
                    $tmpOperators['active_subs']['t_mo_end'] = $total_avg_active_subs['T_Mo_End'];
                    $tmpOperators['active_subs']['avg'] = $total_avg_active_subs['avg'];

                    $total_avg_arpu_7 = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['arpu_7'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['arpu_7']['dates'] = $reportsColumnData['arpu_7'];
                    $tmpOperators['arpu_7']['total'] = $total_avg_arpu_7['sum'];
                    $tmpOperators['arpu_7']['t_mo_end'] = $total_avg_arpu_7['T_Mo_End'];
                    $tmpOperators['arpu_7']['avg'] = $total_avg_arpu_7['avg'];

                    $total_avg_arpu_7_usd = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['arpu_7_usd'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['arpu_7_usd']['dates'] = $reportsColumnData['arpu_7_usd'];
                    $tmpOperators['arpu_7_usd']['total'] = $total_avg_arpu_7_usd['sum'];
                    $tmpOperators['arpu_7_usd']['t_mo_end'] = $total_avg_arpu_7_usd['T_Mo_End'];
                    $tmpOperators['arpu_7_usd']['avg'] = $total_avg_arpu_7_usd['avg'];

                    $total_avg_arpu_30 = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['arpu_30'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['arpu_30']['dates'] = $reportsColumnData['arpu_30'];
                    $tmpOperators['arpu_30']['total'] = $total_avg_arpu_30['sum'];
                    $tmpOperators['arpu_30']['t_mo_end'] = $total_avg_arpu_30['T_Mo_End'];
                    $tmpOperators['arpu_30']['avg'] = $total_avg_arpu_30['avg'];

                    $total_avg_arpu_30_usd = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['arpu_30_usd'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['arpu_30_usd']['dates'] = $reportsColumnData['arpu_30_usd'];
                    $tmpOperators['arpu_30_usd']['total'] = $total_avg_arpu_30_usd['sum'];
                    $tmpOperators['arpu_30_usd']['t_mo_end'] = $total_avg_arpu_30_usd['T_Mo_End'];
                    $tmpOperators['arpu_30_usd']['avg'] = $total_avg_arpu_30_usd['avg'];

                    $total_avg_roi = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['roi'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['roi']['dates'] = $reportsColumnData['roi'];
                    $tmpOperators['roi']['total'] = $total_avg_roi['sum'];
                    $tmpOperators['roi']['t_mo_end'] = $total_avg_roi['T_Mo_End'];
                    $tmpOperators['roi']['avg'] = $total_avg_roi['avg'];

                    $sumemry[] = $tmpOperators;
                }
            }

            return view('report.pnldetails', compact('sumemry', 'no_of_days', 'operator_details'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function reportCountryPnlDetails(Request $request, $operator_id = '')
    {
        if (\Auth::user()->can('PNL Detail')) {
            $data['Daily'] = $Daily = 1;
            $data['CountryWise'] = $CountryWise = 1;

            // new code start
            $CountryId = $req_CountryId = $request->country;
            $CompanyId = $req_CompanyId = $request->company;
            $filterOperator = $req_filterOperator = $request->operatorId;
            $Start_date = $req_Start_date = $request->to;
            $end_date = $req_end_date = trim($request->from);

            if ($end_date <= $Start_date) {
                $Start_date = $req_Start_date = trim($request->from);
                $end_date =  $req_end_date = $request->to;
            }

            $start_date = Carbon::now()->startOfMonth()->subDays(35)->format('Y-m-d');
            $startColumnDateDisplay = Carbon::now()->startOfMonth()->format('Y-m-d');
            $end_date = Carbon::now()->format('Y-m-d');
            $month = Carbon::now()->format('F Y');
            $companys = Company::get();

            $showAllOperator = true;

            if ($request->filled('to') && $request->filled('from')) {
                $display_date_input = new Carbon($req_Start_date);
                $start_date = $req_Start_date;
                $startColumnDateDisplay = $display_date_input->format('Y-m-d');
                $end_date = $req_end_date;
                $month = $display_date_input->format('F Y');
            }

            if ($request->filled('company') && $req_CompanyId != "allcompany"  && !$request->filled('operatorId')) {
                $companies = Company::Find($req_CompanyId);
                $Operators_company = array();

                if (!empty($companies)) {
                    $Operators_company = $companies->company_operators->pluck('operator_id')->toArray();
                    $Operators = Operator::Status(1)->GetOperatorByOperatorId($Operators_company)->get();
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

            if ($request->filled('operatorId')) {
                $Operators = Operator::Status(1)->GetOperatorByOperatorId($filterOperator)->get();
                $showAllOperator = false;
            }

            if ($showAllOperator) {
                $Operators = Operator::Status(1)->get();
            }

            if (!isset($Operators)) {
                $request->session()->flash('alert-success', 'User was successful added!');
                return redirect()->back();
            }

            $Country = Country::all()->toArray();
            $countries = array();

            if (!empty($Country)) {
                foreach ($Country as $CountryI) {
                    $countries[$CountryI['id']] = $CountryI;
                }
            }

            $arrayOperatorsIds = $Operators->pluck('id_operator')->toArray();
            // new code end

            /* Admin Access All operator and Services */
            $user = Auth::user();
            $user_id = $user->id;
            $user_type = $user->type;
            $allowAllOperator = $user->WhowAccessAlOperator($user_type);

            if ($allowAllOperator) {
                $reports = ReportsPnlsOperatorSummarizes::filterOperator($arrayOperatorsIds)
                    ->filterDateRange($start_date, $end_date)
                    ->orderBy('id_operator')
                    ->get()
                    ->toArray();
            } else {
                $UserOperatorServices = Session::get('userOperatorService');

                if (empty($UserOperatorServices)) {
                    dd("Please Contact to admin , add Operator to your account");
                }

                $arrayOperatorsIds = $UserOperatorServices['id_operators'];
                $arrayServicesIds = $UserOperatorServices['id_services'];

                $reports = ReportsPnlsOperatorSummarizes::filterOperator($arrayOperatorsIds)
                    ->filterDateRange($start_date, $end_date)
                    ->orderBy('id_operator')
                    ->get()
                    ->toArray();
            }

            $reportsByIDs = $this->getReportsByOperator($reports);
            $datesIndividual = Utility::getRangeDates($startColumnDateDisplay, $end_date);
            $no_of_days = Utility::getRangeDateNo($datesIndividual);

            if (!empty($Operators)) {
                foreach ($Operators as $operator) {
                    $tmpOperators = array();
                    $country_id = $operator->country_id;
                    $tmpOperators['operator'] = $operator;
                    $contain_id = Arr::exists($countries, $country_id);
                    $countryDetails = [];

                    if ($contain_id) {
                        $tmpOperators['country'] = $countries[$country_id];
                        $countryDetails = $countries[$country_id];
                    }

                    $reportsColumnData = $this->getPNLReportsDateWise($operator, $no_of_days, $reportsByIDs, $countryDetails);

                    $tmpOperators['month_string'] = $month;
                    $tmpOperators['last_update'] = $reportsColumnData['last_update'];

                    $total_avg_rev_usd = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['end_user_rev_usd'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['end_user_rev_usd']['dates'] = $reportsColumnData['end_user_rev_usd'];
                    $tmpOperators['end_user_rev_usd']['total'] = $total_avg_rev_usd['sum'];
                    $tmpOperators['end_user_rev_usd']['t_mo_end'] = $total_avg_rev_usd['T_Mo_End'];
                    $tmpOperators['end_user_rev_usd']['avg'] = $total_avg_rev_usd['avg'];

                    $total_avg_rev = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['end_user_rev'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['end_user_rev']['dates'] = $reportsColumnData['end_user_rev'];
                    $tmpOperators['end_user_rev']['total'] = $total_avg_rev['sum'];
                    $tmpOperators['end_user_rev']['t_mo_end'] = $total_avg_rev['T_Mo_End'];
                    $tmpOperators['end_user_rev']['avg'] = $total_avg_rev['avg'];

                    $total_avg_gros_rev_usd = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['gros_rev_usd'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['gros_rev_usd']['dates'] = $reportsColumnData['gros_rev_usd'];
                    $tmpOperators['gros_rev_usd']['total'] = $total_avg_gros_rev_usd['sum'];
                    $tmpOperators['gros_rev_usd']['t_mo_end'] = $total_avg_gros_rev_usd['T_Mo_End'];
                    $tmpOperators['gros_rev_usd']['avg'] = $total_avg_gros_rev_usd['avg'];

                    $total_avg_gros_rev = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['gros_rev'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['gros_rev']['dates'] = $reportsColumnData['gros_rev'];
                    $tmpOperators['gros_rev']['total'] = $total_avg_gros_rev['sum'];
                    $tmpOperators['gros_rev']['t_mo_end'] = $total_avg_gros_rev['T_Mo_End'];
                    $tmpOperators['gros_rev']['avg'] = $total_avg_gros_rev['avg'];

                    $total_avg_cost_campaign = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['cost_campaign'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['cost_campaign']['dates'] = $reportsColumnData['cost_campaign'];
                    $tmpOperators['cost_campaign']['total'] = $total_avg_cost_campaign['sum'];
                    $tmpOperators['cost_campaign']['t_mo_end'] = $total_avg_cost_campaign['T_Mo_End'];
                    $tmpOperators['cost_campaign']['avg'] = $total_avg_cost_campaign['avg'];

                    $total_avg_other_cost = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['other_cost'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['other_cost']['dates'] = $reportsColumnData['other_cost'];
                    $tmpOperators['other_cost']['total'] = $total_avg_other_cost['sum'];
                    $tmpOperators['other_cost']['t_mo_end'] = $total_avg_other_cost['T_Mo_End'];
                    $tmpOperators['other_cost']['avg'] = $total_avg_other_cost['avg'];

                    $total_avg_hosting_cost = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['hosting_cost'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['hosting_cost']['dates'] = $reportsColumnData['hosting_cost'];
                    $tmpOperators['hosting_cost']['total'] = $total_avg_hosting_cost['sum'];
                    $tmpOperators['hosting_cost']['t_mo_end'] = $total_avg_hosting_cost['T_Mo_End'];
                    $tmpOperators['hosting_cost']['avg'] = $total_avg_hosting_cost['avg'];

                    $total_avg_content = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['content'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['content']['dates'] = $reportsColumnData['content'];
                    $tmpOperators['content']['total'] = $total_avg_content['sum'];
                    $tmpOperators['content']['t_mo_end'] = $total_avg_content['T_Mo_End'];
                    $tmpOperators['content']['avg'] = $total_avg_content['avg'];

                    $total_avg_rnd = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['rnd'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['rnd']['dates'] = $reportsColumnData['rnd'];
                    $tmpOperators['rnd']['total'] = $total_avg_rnd['sum'];
                    $tmpOperators['rnd']['t_mo_end'] = $total_avg_rnd['T_Mo_End'];
                    $tmpOperators['rnd']['avg'] = $total_avg_rnd['avg'];

                    $total_avg_bd = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['bd'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['bd']['dates'] = $reportsColumnData['bd'];
                    $tmpOperators['bd']['total'] = $total_avg_bd['sum'];
                    $tmpOperators['bd']['t_mo_end'] = $total_avg_bd['T_Mo_End'];
                    $tmpOperators['bd']['avg'] = $total_avg_bd['avg'];

                    $total_avg_platform = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['platform'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['platform']['dates'] = $reportsColumnData['platform'];
                    $tmpOperators['platform']['total'] = $total_avg_platform['sum'];
                    $tmpOperators['platform']['t_mo_end'] = $total_avg_platform['T_Mo_End'];
                    $tmpOperators['platform']['avg'] = $total_avg_platform['avg'];

                    $total_avg_pnl = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['pnl'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['pnl']['dates'] = $reportsColumnData['pnl'];
                    $tmpOperators['pnl']['total'] = $total_avg_pnl['sum'];
                    $tmpOperators['pnl']['t_mo_end'] = $total_avg_pnl['T_Mo_End'];
                    $tmpOperators['pnl']['avg'] = $total_avg_pnl['avg'];

                    $total_avg_net_after_tax = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['net_after_tax'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['net_after_tax']['dates'] = $reportsColumnData['net_after_tax'];
                    $tmpOperators['net_after_tax']['total'] = $total_avg_net_after_tax['sum'];
                    $tmpOperators['net_after_tax']['t_mo_end'] = $total_avg_net_after_tax['T_Mo_End'];
                    $tmpOperators['net_after_tax']['avg'] = $total_avg_net_after_tax['avg'];

                    $total_avg_net_revenue_after_tax = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['net_revenue_after_tax'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['net_revenue_after_tax']['dates'] = $reportsColumnData['net_revenue_after_tax'];
                    $tmpOperators['net_revenue_after_tax']['total'] = $total_avg_net_revenue_after_tax['sum'];
                    $tmpOperators['net_revenue_after_tax']['t_mo_end'] = $total_avg_net_revenue_after_tax['T_Mo_End'];
                    $tmpOperators['net_revenue_after_tax']['avg'] = $total_avg_net_revenue_after_tax['avg'];

                    $total_avg_br = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['br'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['br']['dates'] = $reportsColumnData['br'];
                    $tmpOperators['br']['total'] = $total_avg_br['sum'];
                    $tmpOperators['br']['t_mo_end'] = $total_avg_br['T_Mo_End'];
                    $tmpOperators['br']['avg'] = $total_avg_br['avg'];

                    $total_avg_fp = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['fp'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['fp']['dates'] = $reportsColumnData['fp'];
                    $tmpOperators['fp']['total'] = $total_avg_fp['sum'];
                    $tmpOperators['fp']['t_mo_end'] = $total_avg_fp['T_Mo_End'];
                    $tmpOperators['fp']['avg'] = $total_avg_fp['avg'];

                    $total_avg_fp_success = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['fp_success'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['fp_success']['dates'] = $reportsColumnData['fp_success'];
                    $tmpOperators['fp_success']['total'] = $total_avg_fp_success['sum'];
                    $tmpOperators['fp_success']['t_mo_end'] = $total_avg_fp_success['T_Mo_End'];
                    $tmpOperators['fp_success']['avg'] = $total_avg_fp_success['avg'];

                    $total_avg_dp = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['dp'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['dp']['dates'] = $reportsColumnData['dp'];
                    $tmpOperators['dp']['total'] = $total_avg_dp['sum'];
                    $tmpOperators['dp']['t_mo_end'] = $total_avg_dp['T_Mo_End'];
                    $tmpOperators['dp']['avg'] = $total_avg_dp['avg'];

                    $total_avg_dp_success = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['dp_success'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['dp_success']['dates'] = $reportsColumnData['dp_success'];
                    $tmpOperators['dp_success']['total'] = $total_avg_dp_success['sum'];
                    $tmpOperators['dp_success']['t_mo_end'] = $total_avg_dp_success['T_Mo_End'];
                    $tmpOperators['dp_success']['avg'] = $total_avg_dp_success['avg'];

                    $total_avg_vat = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['vat'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['vat']['dates'] = $reportsColumnData['vat'];
                    $tmpOperators['vat']['total'] = $total_avg_vat['sum'];
                    $tmpOperators['vat']['t_mo_end'] = $total_avg_vat['T_Mo_End'];
                    $tmpOperators['vat']['avg'] = $total_avg_vat['avg'];

                    $total_avg_spec_tax = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['spec_tax'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['spec_tax']['dates'] = $reportsColumnData['spec_tax'];
                    $tmpOperators['spec_tax']['total'] = $total_avg_spec_tax['sum'];
                    $tmpOperators['spec_tax']['t_mo_end'] = $total_avg_spec_tax['T_Mo_End'];
                    $tmpOperators['spec_tax']['avg'] = $total_avg_spec_tax['avg'];

                    $total_avg_government_cost = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['government_cost'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['government_cost']['dates'] = $reportsColumnData['government_cost'];
                    $tmpOperators['government_cost']['total'] = $total_avg_government_cost['sum'];
                    $tmpOperators['government_cost']['t_mo_end'] = $total_avg_government_cost['T_Mo_End'];
                    $tmpOperators['government_cost']['avg'] = $total_avg_government_cost['avg'];

                    $total_avg_dealer_commision = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['dealer_commision'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['dealer_commision']['dates'] = $reportsColumnData['dealer_commision'];
                    $tmpOperators['dealer_commision']['total'] = $total_avg_dealer_commision['sum'];
                    $tmpOperators['dealer_commision']['t_mo_end'] = $total_avg_dealer_commision['T_Mo_End'];
                    $tmpOperators['dealer_commision']['avg'] = $total_avg_dealer_commision['avg'];

                    $total_avg_wht = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['wht'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['wht']['dates'] = $reportsColumnData['wht'];
                    $tmpOperators['wht']['total'] = $total_avg_wht['sum'];
                    $tmpOperators['wht']['t_mo_end'] = $total_avg_wht['T_Mo_End'];
                    $tmpOperators['wht']['avg'] = $total_avg_wht['avg'];

                    $total_avg_misc_tax = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['misc_tax'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['misc_tax']['dates'] = $reportsColumnData['misc_tax'];
                    $tmpOperators['misc_tax']['total'] = $total_avg_misc_tax['sum'];
                    $tmpOperators['misc_tax']['t_mo_end'] = $total_avg_misc_tax['T_Mo_End'];
                    $tmpOperators['misc_tax']['avg'] = $total_avg_misc_tax['avg'];

                    $total_avg_uso = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['uso'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['uso']['dates'] = $reportsColumnData['uso'];
                    $tmpOperators['uso']['total'] = $total_avg_uso['sum'];
                    $tmpOperators['uso']['t_mo_end'] = $total_avg_uso['T_Mo_End'];
                    $tmpOperators['uso']['avg'] = $total_avg_uso['avg'];

                    $total_avg_agre_paxxa = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['agre_paxxa'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['agre_paxxa']['dates'] = $reportsColumnData['agre_paxxa'];
                    $tmpOperators['agre_paxxa']['total'] = $total_avg_agre_paxxa['sum'];
                    $tmpOperators['agre_paxxa']['t_mo_end'] = $total_avg_agre_paxxa['T_Mo_End'];
                    $tmpOperators['agre_paxxa']['avg'] = $total_avg_agre_paxxa['avg'];

                    $total_avg_sbaf = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['sbaf'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['sbaf']['dates'] = $reportsColumnData['sbaf'];
                    $tmpOperators['sbaf']['total'] = $total_avg_sbaf['sum'];
                    $tmpOperators['sbaf']['t_mo_end'] = $total_avg_sbaf['T_Mo_End'];
                    $tmpOperators['sbaf']['avg'] = $total_avg_sbaf['avg'];

                    $total_avg_clicks = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['clicks'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['clicks']['dates'] = $reportsColumnData['clicks'];
                    $tmpOperators['clicks']['total'] = $total_avg_clicks['sum'];
                    $tmpOperators['clicks']['t_mo_end'] = $total_avg_clicks['T_Mo_End'];
                    $tmpOperators['clicks']['avg'] = $total_avg_clicks['avg'];

                    $total_avg_ratio_for_cpa = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['ratio_for_cpa'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['ratio_for_cpa']['dates'] = $reportsColumnData['ratio_for_cpa'];
                    $tmpOperators['ratio_for_cpa']['total'] = $total_avg_ratio_for_cpa['sum'];
                    $tmpOperators['ratio_for_cpa']['t_mo_end'] = $total_avg_ratio_for_cpa['T_Mo_End'];
                    $tmpOperators['ratio_for_cpa']['avg'] = $total_avg_ratio_for_cpa['avg'];

                    $total_avg_cpa_price = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['cpa_price'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['cpa_price']['dates'] = $reportsColumnData['cpa_price'];
                    $tmpOperators['cpa_price']['total'] = $total_avg_cpa_price['sum'];
                    $tmpOperators['cpa_price']['t_mo_end'] = $total_avg_cpa_price['T_Mo_End'];
                    $tmpOperators['cpa_price']['avg'] = $total_avg_cpa_price['avg'];

                    $total_avg_cr_mo_clicks = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['cr_mo_clicks'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['cr_mo_clicks']['dates'] = $reportsColumnData['cr_mo_clicks'];
                    $tmpOperators['cr_mo_clicks']['total'] = $total_avg_cr_mo_clicks['sum'];
                    $tmpOperators['cr_mo_clicks']['t_mo_end'] = $total_avg_cr_mo_clicks['T_Mo_End'];
                    $tmpOperators['cr_mo_clicks']['avg'] = $total_avg_cr_mo_clicks['avg'];

                    $total_avg_cr_mo_landing = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['cr_mo_landing'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['cr_mo_landing']['dates'] = $reportsColumnData['cr_mo_landing'];
                    $tmpOperators['cr_mo_landing']['total'] = $total_avg_cr_mo_landing['sum'];
                    $tmpOperators['cr_mo_landing']['t_mo_end'] = $total_avg_cr_mo_landing['T_Mo_End'];
                    $tmpOperators['cr_mo_landing']['avg'] = $total_avg_cr_mo_landing['avg'];

                    $total_avg_landing = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['landing'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['landing']['dates'] = $reportsColumnData['landing'];
                    $tmpOperators['landing']['total'] = $total_avg_landing['sum'];
                    $tmpOperators['landing']['t_mo_end'] = $total_avg_landing['T_Mo_End'];
                    $tmpOperators['landing']['avg'] = $total_avg_landing['avg'];

                    $total_avg_mo = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['mo'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['mo']['dates'] = $reportsColumnData['mo'];
                    $tmpOperators['mo']['total'] = $total_avg_mo['sum'];
                    $tmpOperators['mo']['t_mo_end'] = $total_avg_mo['T_Mo_End'];
                    $tmpOperators['mo']['avg'] = $total_avg_mo['avg'];

                    $total_avg_unreg = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['unreg'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['unreg']['dates'] = $reportsColumnData['unreg'];
                    $tmpOperators['unreg']['total'] = $total_avg_unreg['sum'];
                    $tmpOperators['unreg']['t_mo_end'] = $total_avg_unreg['T_Mo_End'];
                    $tmpOperators['unreg']['avg'] = $total_avg_unreg['avg'];

                    $total_avg_price_mo = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['price_mo'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['price_mo']['dates'] = $reportsColumnData['price_mo'];
                    $tmpOperators['price_mo']['total'] = $total_avg_price_mo['sum'];
                    $tmpOperators['price_mo']['t_mo_end'] = $total_avg_price_mo['T_Mo_End'];
                    $tmpOperators['price_mo']['avg'] = $total_avg_price_mo['avg'];

                    $total_avg_active_subs = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['active_subs'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['active_subs']['dates'] = $reportsColumnData['active_subs'];
                    $tmpOperators['active_subs']['total'] = $total_avg_active_subs['sum'];
                    $tmpOperators['active_subs']['t_mo_end'] = $total_avg_active_subs['T_Mo_End'];
                    $tmpOperators['active_subs']['avg'] = $total_avg_active_subs['avg'];

                    $total_avg_arpu_7 = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['arpu_7'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['arpu_7']['dates'] = $reportsColumnData['arpu_7'];
                    $tmpOperators['arpu_7']['total'] = $total_avg_arpu_7['sum'];
                    $tmpOperators['arpu_7']['t_mo_end'] = $total_avg_arpu_7['T_Mo_End'];
                    $tmpOperators['arpu_7']['avg'] = $total_avg_arpu_7['avg'];

                    $total_avg_arpu_7_usd = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['arpu_7_usd'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['arpu_7_usd']['dates'] = $reportsColumnData['arpu_7_usd'];
                    $tmpOperators['arpu_7_usd']['total'] = $total_avg_arpu_7_usd['sum'];
                    $tmpOperators['arpu_7_usd']['t_mo_end'] = $total_avg_arpu_7_usd['T_Mo_End'];
                    $tmpOperators['arpu_7_usd']['avg'] = $total_avg_arpu_7_usd['avg'];

                    $total_avg_arpu_30 = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['arpu_30'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['arpu_30']['dates'] = $reportsColumnData['arpu_30'];
                    $tmpOperators['arpu_30']['total'] = $total_avg_arpu_30['sum'];
                    $tmpOperators['arpu_30']['t_mo_end'] = $total_avg_arpu_30['T_Mo_End'];
                    $tmpOperators['arpu_30']['avg'] = $total_avg_arpu_30['avg'];

                    $total_avg_arpu_30_usd = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['arpu_30_usd'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['arpu_30_usd']['dates'] = $reportsColumnData['arpu_30_usd'];
                    $tmpOperators['arpu_30_usd']['total'] = $total_avg_arpu_30_usd['sum'];
                    $tmpOperators['arpu_30_usd']['t_mo_end'] = $total_avg_arpu_30_usd['T_Mo_End'];
                    $tmpOperators['arpu_30_usd']['avg'] = $total_avg_arpu_30_usd['avg'];

                    $total_avg_roi = UtilityReports::calculateTotalAVG($operator, $reportsColumnData['roi'], $startColumnDateDisplay, $end_date);
                    $tmpOperators['roi']['dates'] = $reportsColumnData['roi'];
                    $tmpOperators['roi']['total'] = $total_avg_roi['sum'];
                    $tmpOperators['roi']['t_mo_end'] = $total_avg_roi['T_Mo_End'];
                    $tmpOperators['roi']['avg'] = $total_avg_roi['avg'];

                    $sumemry[] = $tmpOperators;
                }
            }

            // Country Sum from Operator array
            $displayCountries = array();
            $SelectedCountries = array();
            $RowCountryData = array();

            if (!empty($sumemry)) {
                foreach ($sumemry as $key => $sumemries) {
                    $country_id = $sumemries['country']['id'];
                    $SelectedCountries[$country_id] = $sumemries['country'];
                    $displayCountries[$country_id][] = $sumemries;
                }
            }

            if (!empty($SelectedCountries)) {
                foreach ($SelectedCountries as $key => $SelectedCountry) {
                    $tempDataArr = array();
                    $country_id = $SelectedCountry['id'];
                    $dataRowSum = UtilityReports::pnlDetailsDataSum($displayCountries[$country_id]);
                    $tempDataArr['country'] = $SelectedCountry;
                    $tempDataArr['month_string'] = $month;
                    $tempDataArr = array_merge($tempDataArr, $dataRowSum);
                    $RowCountryData[] = $tempDataArr;
                }
            }

            $sumemry = $RowCountryData;
            $sumOfSummaryData = UtilityReports::pnlDetailsDataSum($sumemry);

            return view('report.daily_country_pnldetails', compact('sumemry', 'no_of_days', 'sumOfSummaryData', 'data'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    function getReportsByOperator($reports)
    {
        if (!empty($reports)) {
            $reportsResult = array();
            $tempreport = array();

            foreach ($reports as $report) {
                $tempreport[$report['id_operator']][$report['date']] = $report;
            }

            $reportsResult = $tempreport;
            return $reportsResult;
        }
    }

    function getPNLReportsDateWise($operator, $no_of_days, $reportsByIDs, $OperatorCountry)
    {
        if (\Auth::user()->can('Reporting Details')) {
            $usdValue = $OperatorCountry['usd'];

            if (!empty($no_of_days)) {
                $allColumnData = array();
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
                $net_after_tax_Arr = array();
                $net_revenue_after_tax_Arr = array();
                $br_Arr = array();
                $fp_Arr = array();
                $fp_success_Arr = array();
                $dp_Arr = array();
                $dp_success_Arr = array();
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
                $unreg_Arr = array();
                $price_mo_Arr = array();
                $active_subs_Arr = array();
                $arpu_7_Arr = array();
                $arpu_7_usd_Arr = array();
                $arpu_30_Arr = array();
                $arpu_30_usd_Arr = array();
                $roi_Arr = array();
                $update = false;
                $last_update = '';

                $id_operator = isset($operator->id_operator) ? $operator->id_operator : $operator->id;

                foreach ($no_of_days as $days) {
                    $keys = $id_operator . "." . $days['date'];
                    $summariserow = Arr::get($reportsByIDs, $keys, 0);

                    if ($summariserow != 0  && !$update) {
                        $update = true;
                        $last_update = $summariserow['updated_at'];
                    }

                    $end_user_rev_usd = isset($summariserow['rev_usd']) ? $summariserow['rev_usd'] : 0;
                    $end_user_rev_usd = sprintf('%0.2f', $end_user_rev_usd);

                    $end_user_rev = isset($summariserow['rev']) ? $summariserow['rev'] : 0;
                    $end_user_rev = sprintf('%0.2f', $end_user_rev);

                    $gros_rev_usd = isset($summariserow['share']) ? $summariserow['share'] : 0;
                    $gros_rev_usd = sprintf('%0.2f', $gros_rev_usd);

                    $gros_rev = isset($summariserow['lshare']) ? $summariserow['lshare'] : 0;
                    $gros_rev = sprintf('%0.2f', $gros_rev);

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

                    $bd = isset($summariserow['bd']) ? $summariserow['bd'] : 0;
                    $bd = sprintf('%0.2f', $bd);

                    $platform = isset($summariserow['platform']) ? $summariserow['platform'] : 0;
                    $platform = sprintf('%0.2f', $platform);

                    $pnl = isset($summariserow['pnl']) ? $summariserow['pnl'] : 0;
                    $pnl = sprintf('%0.2f', $pnl);

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

                    $dp = isset($summariserow['dp']) ? $summariserow['dp'] : 0;
                    $dp = sprintf('%0.2f', $dp);

                    $dp_success = isset($summariserow['dp_success']) ? $summariserow['dp_success'] : 0;
                    $dp_success = sprintf('%0.2f', $dp_success);

                    $vat = isset($summariserow['vat']) ? $summariserow['vat'] : 0;
                    $vat = sprintf('%0.2f', $vat);

                    $spec_tax = isset($summariserow['spec_tax']) ? $summariserow['spec_tax'] : 0;
                    $spec_tax = sprintf('%0.2f', $spec_tax);

                    $government_cost = isset($summariserow['government_cost']) ? $summariserow['government_cost'] : 0;
                    $government_cost = sprintf('%0.2f', $government_cost);

                    $dealer_commision = isset($summariserow['dealer_commision']) ? $summariserow['dealer_commision'] : 0;
                    $dealer_commision = sprintf('%0.2f', $dealer_commision);

                    $wht = isset($summariserow['wht']) ? $summariserow['wht'] : 0;
                    $wht = sprintf('%0.2f', $wht);

                    $misc_tax = isset($summariserow['misc_tax']) ? $summariserow['misc_tax'] : 0;
                    $misc_tax = sprintf('%0.2f', $misc_tax);

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

                    $unreg = isset($summariserow['unreg']) ? $summariserow['unreg'] : 0;
                    $unreg = sprintf('%0.2f', $unreg);

                    $price_mo = ($mo == 0) ? (float)0 : ($cost_campaign / $mo);

                    $active_subs = isset($summariserow['active_subs']) ? $summariserow['active_subs'] : 0;
                    $active_subs = sprintf('%0.2f', $active_subs);

                    $arpu_7 = isset($summariserow['arpu_7']) ? $summariserow['arpu_7'] : 0;
                    $arpu_7 = sprintf('%0.2f', $arpu_7);

                    $arpu_7_usd = $arpu_7 * $usdValue;

                    $arpu_30 = isset($summariserow['arpu_30']) ? $summariserow['arpu_30'] : 0;
                    $arpu_30 = sprintf('%0.2f', $arpu_30);

                    $arpu_30_usd = $arpu_30 * $usdValue;

                    $roi = ($arpu_30 == 0) ? (float)0 : ($price_mo / $arpu_30);

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

                    $dp_Arr[$days['date']]['value'] = $dp;
                    $dp_Arr[$days['date']]['class'] = "bg-hui";

                    $dp_success_Arr[$days['date']]['value'] = $dp_success;
                    $dp_success_Arr[$days['date']]['class'] = "bg-hui";

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

                    $unreg_Arr[$days['date']]['value'] = $unreg;
                    $unreg_Arr[$days['date']]['class'] = "bg-hui";

                    $price_mo_Arr[$days['date']]['value'] = $price_mo;
                    $price_mo_Arr[$days['date']]['class'] = "bg-hui";

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

                    $roi_Arr[$days['date']]['value'] = $roi;
                    $roi_Arr[$days['date']]['class'] = "bg-hui";
                }

                $last_update_show = "Not updated last month";

                if ($last_update != "") {
                    $last_update_timestamp = Carbon::parse($last_update);
                    $last_update_timestamp->setTimezone('Asia/Jakarta');
                    $last_update_show = $last_update_timestamp->format("Y-m-d H:i:s") . " Asia/Jakarta";
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
                $allColumnData['net_after_tax'] = $net_after_tax_Arr;
                $allColumnData['net_revenue_after_tax'] = $net_revenue_after_tax_Arr;
                $allColumnData['br'] = $br_Arr;
                $allColumnData['fp'] = $fp_Arr;
                $allColumnData['fp_success'] = $fp_success_Arr;
                $allColumnData['dp'] = $dp_Arr;
                $allColumnData['dp_success'] = $dp_success_Arr;
                $allColumnData['vat'] = $vat_Arr;
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
                $allColumnData['unreg'] = $unreg_Arr;
                $allColumnData['price_mo'] = $price_mo_Arr;
                $allColumnData['active_subs'] = $active_subs_Arr;
                $allColumnData['arpu_7'] = $arpu_7_Arr;
                $allColumnData['arpu_7_usd'] = $arpu_7_usd_Arr;
                $allColumnData['arpu_30'] = $arpu_30_Arr;
                $allColumnData['arpu_30_usd'] = $arpu_30_usd_Arr;
                $allColumnData['roi'] = $roi_Arr;
                $allColumnData['last_update'] = $last_update_show;

                return $allColumnData;
            }
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    // report service details
    public function reportServiceDetails(Request $request, $service_id = '')
    {
        $requestData = $request->all();
        $country_id = isset($requestData['country']) ? $requestData['country'] : '';

        $sumemry = array();
        $service_id = 8;
        $notCountry = true;
        $user = Auth::user();
        $user_id = $user->id;
        $user_type = $user->type;
        $allowAllOperator = $user->WhowAccessAlOperator($user_type);
        $DataArray = [];
        $mobifone_service = [466, 696, 697, 698, 879, 887, 888];

        if (!$allowAllOperator) {
            $UserOperatorServices = Session::get('userOperatorService');

            if (empty($UserOperatorServices)) {
                dd("Please Contact to admin, add Operator with service to your account");
            }

            $service_id = $UserOperatorServices['id_services'][0];
        }

        // get filtre request
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
        $showAllOperator = true;

        if ($request->filled('to') && $request->filled('from')) {
            $start_date_input = new Carbon($req_Start_date);
            $display_date_input = new Carbon($req_Start_date);

            $start_date = $start_date_input->subDays(35)->format('Y-m-d');
            $startColumnDateDisplay = $display_date_input->format('Y-m-d');

            $end_date = $req_end_date;
            $month = $display_date_input->format('F Y');
        }

        if ($request->filled('service')) {
            $service_id = $req_service = $request->service;
        }

        $reports = ServiceHistory::filterService([$service_id])
            ->filterDateRange($start_date, $end_date)
            ->get()
            ->toArray();

        $reportsByIDs = $this->getReportsServiceID($reports);
        $datesIndividual = Utility::getRangeDates($startColumnDateDisplay, $end_date);
        $no_of_days = Utility::getRangeDateNo($datesIndividual);
        array_multisort($no_of_days, SORT_DESC, $no_of_days);

        /* get service extra column details from API */
        $globalconfig = \config('globalconfig');
        $apiUrl = $globalconfig['apiUrl'];
        $serviceUrl = $globalconfig['serviceUrl'];
        $url = $apiUrl . $serviceUrl . $user_id . '&data=' . $service_id;

        $thirdpartyapi = \config('thirdpartyapi');
        $api_url = $thirdpartyapi['api_url'];
        $fp_url = $api_url['service_fmt_'];
        $dp_url = $api_url['service_mt_'];

        $utility = new Utility;
        $result = $utility->GetArrayResponseFromUrl($url);
        $getServiceDetails = (isset($result) && !empty($result)) ? $result : [];

        /*first push*/
        $serviceStatusUrl = $globalconfig['serviceStatusUrl'];
        $fpUrl = $fp_url . $service_id . '|' . $startColumnDateDisplay . '|' . $end_date;
        $first_push_data = $utility->GetArrayResponseFromUrl($fpUrl);

        if (isset($first_push_data) && !empty($first_push_data)) {
            foreach ($first_push_data as $fpvalue) {
                $subs = (isset($DataArray['subsactive'][$fpvalue['date']])) ? $DataArray['subsactive'][$fpvalue['date']] : 0;
                $fp_success = $fpvalue['mt_success'];
                $fp_failed = $fpvalue['mt_failed'];
                $fp_sent = $fp_success + $fp_failed;

                $DataArray['first_push_data']['sent'][$fpvalue['date']] = $fp_sent;
                $DataArray['first_push_data']['failed'][$fpvalue['date']] = $fp_failed;
                $DataArray['first_push_data']['delivered'][$fpvalue['date']] = $fp_success;

                if ($fp_sent == 0) {
                    if ($subs > 0) {
                        $fp = ($fp_success / $subs) * 100;
                    } else {
                        $fp = 0;
                    }
                } else {
                    if ($fp_failed == 0) {
                        if ($subs > 0) {
                            $fp = ($fp_success / $subs) * 100;
                        } else {
                            $fp = 0;
                        }
                    } else {
                        $fp = ($fp_success / $fp_sent) * 100;
                    }
                }

                $DataArray['first_push_data']['success_rate'][$fpvalue['date']] = $fp;
                $DataArray['first_push_data']['revenue'][$fpvalue['date']] = $fpvalue['gros_rev'];
            }
            $DataArray['first_push_data']['total'] = array_sum($DataArray['first_push_data']['revenue']);
        }

        $dpUrl = $dp_url . $service_id . '|' . $startColumnDateDisplay . '|' . $end_date;
        $daily_push_data = $utility->GetArrayResponseFromUrl($dpUrl);

        if (isset($daily_push_data) && !empty($daily_push_data)) {
            foreach ($daily_push_data as $dpvalue) {
                $subs = (isset($DataArray['subsactive'][$dpvalue['date']])) ? $DataArray['subsactive'][$dpvalue['date']] : 0;
                $dp_success = $dpvalue['mt_success'];
                $dp_failed = $dpvalue['mt_failed'];
                $dp_sent = $dp_success + $dp_failed;

                if (in_array($service_id, $mobifone_service)) {
                    if ($service_id == 466) {
                        $DataArray['revenue'][$dpvalue['date']] = $dp_success * 3000;
                    } else if ($service_id == 698) {
                        $DataArray['revenue'][$dpvalue['date']] = $dp_success * 5000;
                    } else {
                        $DataArray['revenue'][$dpvalue['date']] = $dp_success * $service['price'];
                    }
                }

                $DataArray['daily_push_data']['sent'][$dpvalue['date']] = $dp_sent;
                $DataArray['daily_push_data']['failed'][$dpvalue['date']] = $dp_failed;
                $DataArray['daily_push_data']['delivered'][$dpvalue['date']] = $dp_success;

                if ($dp_sent == 0) {
                    if ($subs > 0) {
                        $dp = ($dp_success / $subs) * 100;
                    } else {
                        $dp = 0;
                    }
                } else {
                    if ($dp_failed == 0) {
                        if ($subs > 0) {
                            $dp = ($dp_success / $subs) * 100;
                        } else {
                            $dp = 0;
                        }
                    } else {
                        $dp = ($dp_success / $dp_sent) * 100;
                    }
                }

                $DataArray['daily_push_data']['success_rate'][$dpvalue['date']] = $dp;
                $DataArray['daily_push_data']['revenue'][$dpvalue['date']] = $dpvalue['gros_rev'];
            }
        }

        $firstPushFailedUrl = $globalconfig['fpFailedUrl'];
        $fpFailedUrl = $apiUrl . $firstPushFailedUrl . $service_id . '|' . $startColumnDateDisplay . '|' . $end_date;
        $first_push_failed = $utility->GetArrayResponseFromUrl($fpFailedUrl);

        if (isset($first_push_failed) && !empty($first_push_failed)) {
            $DataArray['first_push_data']['reasons'] = [];
            foreach ($first_push_failed as $fvalue) {
                // $reason = $this->getStatusDescription($fvalue['status']);
                $reason = $fvalue['status'];

                if (!in_array($reason, $DataArray['first_push_data']['reasons'])) {
                    $DataArray['first_push_data']['reasons'][] = $reason;
                }

                $DataArray['first_push_data'][$reason][$fvalue['date']] = $fvalue['total'];
            }
        }

        $dailyPushFailedUrl = $globalconfig['dpFailedUrl'];
        $dpFailedUrl = $apiUrl . $dailyPushFailedUrl . $service_id . '|' . $startColumnDateDisplay . '|' . $end_date;
        $daily_push_failed = $utility->GetArrayResponseFromUrl($dpFailedUrl);

        if (isset($daily_push_failed) && !empty($daily_push_failed)) {
            $DataArray['daily_push_data']['reasons'] = [];
            foreach ($daily_push_failed as $dvalue) {
                // $reason = $this->getStatusDescription($dvalue['status']);
                $reason = $dvalue['status'];

                if (!in_array($reason, $DataArray['daily_push_data']['reasons'])) {
                    $DataArray['daily_push_data']['reasons'][] = $reason;
                }

                $DataArray['daily_push_data'][$reason][$dvalue['date']] = $dvalue['total'];
            }
        }

        $service = Service::with('service_history')->FindOrFail($service_id);

        /* get All country and put in an array */
        $Operators = Operator::all()->toArray();
        // if(!empty($country_id)){
        // $Operators = Operator::GetOperatorByCountryId($country_id)->get()->toArray();
        // }
        $operators = array();

        if (!empty($Operators)) {
            foreach ($Operators as $operator) {
                $operators[$operator['id_operator']] = $operator;
            }
        }

        if (!empty($service)) {
            $tmpOperators = array();
            $tmpOperators['service'] = $service;
            $operator_id = $service->operator_id;
            $contain_id = Arr::exists($operators, $operator_id);
            $ServiceOperator = array();

            if ($contain_id) {
                $tmpOperators['operator'] = $operators[$operator_id];
                $ServiceOperator = $operators[$operator_id];
                $reportsColumnData = $this->getServiceReportsDateWise($service, $no_of_days, $reportsByIDs, $ServiceOperator);

                $total_avg_rev = UtilityReports::calculateTotalAVG($service, $reportsColumnData['t_rev'], $startColumnDateDisplay, $end_date);
                $tmpOperators['t_rev']['dates'] = $reportsColumnData['t_rev'];
                $tmpOperators['t_rev']['total'] = $total_avg_rev['sum'];
                $tmpOperators['t_rev']['avg'] = $total_avg_rev['avg'];
                $tmpOperators['t_rev']['T_Mo_End'] = $total_avg_rev['T_Mo_End'];


                $total_avg_t_sub = UtilityReports::calculateTotalAVG($service, $reportsColumnData['t_sub'], $startColumnDateDisplay, $end_date);
                $tmpOperators['t_sub']['dates'] = $reportsColumnData['t_sub'];
                $tmpOperators['t_sub']['total'] = $total_avg_t_sub['sum'];
                $tmpOperators['t_sub']['avg'] = $total_avg_t_sub['avg'];
                $tmpOperators['t_sub']['T_Mo_End'] = $total_avg_t_sub['T_Mo_End'];

                $total_avg_reg = UtilityReports::calculateTotalAVG($service, $reportsColumnData['reg'], $startColumnDateDisplay, $end_date);
                $tmpOperators['reg']['dates'] = $reportsColumnData['reg'];
                $tmpOperators['reg']['total'] = $total_avg_reg['sum'];
                $tmpOperators['reg']['avg'] = $total_avg_reg['avg'];
                $tmpOperators['reg']['T_Mo_End'] = $total_avg_reg['T_Mo_End'];


                $total_avg_unreg = UtilityReports::calculateTotalAVG($service, $reportsColumnData['unreg'], $startColumnDateDisplay, $end_date);
                $tmpOperators['unreg']['dates'] = $reportsColumnData['unreg'];
                $tmpOperators['unreg']['total'] = $total_avg_unreg['sum'];
                $tmpOperators['unreg']['avg'] = $total_avg_unreg['avg'];
                $tmpOperators['unreg']['T_Mo_End'] = $total_avg_unreg['T_Mo_End'];


                $total_avg_purged = UtilityReports::calculateTotalAVG($service, $reportsColumnData['purged'], $startColumnDateDisplay, $end_date);
                $tmpOperators['purged']['dates'] = $reportsColumnData['purged'];
                $tmpOperators['purged']['total'] = $total_avg_purged['sum'];
                $tmpOperators['purged']['avg'] = $total_avg_purged['avg'];
                $tmpOperators['purged']['T_Mo_End'] = $total_avg_purged['T_Mo_End'];


                $total_avg_churn = UtilityReports::calculateTotalAVG($service, $reportsColumnData['churn'], $startColumnDateDisplay, $end_date);
                $tmpOperators['churn']['dates'] = $reportsColumnData['churn'];
                $tmpOperators['churn']['total'] = $total_avg_churn['sum'];
                $tmpOperators['churn']['avg'] = $total_avg_churn['avg'];
                $tmpOperators['churn']['T_Mo_End'] = $total_avg_churn['T_Mo_End'];


                $total_avg_renewal = UtilityReports::calculateTotalAVG($service, $reportsColumnData['renewal'], $startColumnDateDisplay, $end_date);
                $tmpOperators['renewal']['dates'] = $reportsColumnData['renewal'];
                $tmpOperators['renewal']['total'] = $total_avg_renewal['sum'];
                $tmpOperators['renewal']['avg'] = $total_avg_renewal['avg'];
                $tmpOperators['renewal']['T_Mo_End'] = $total_avg_renewal['T_Mo_End'];


                $total_avg_sent = UtilityReports::calculateTotalAVG($service, $reportsColumnData['sent'], $startColumnDateDisplay, $end_date);
                $tmpOperators['sent']['dates'] = $reportsColumnData['sent'];
                $tmpOperators['sent']['total'] = $total_avg_sent['sum'];
                $tmpOperators['sent']['avg'] = $total_avg_sent['avg'];
                $tmpOperators['sent']['T_Mo_End'] = $total_avg_sent['T_Mo_End'];


                $total_avg_mt_success = UtilityReports::calculateTotalAVG($service, $reportsColumnData['mt_success'], $startColumnDateDisplay, $end_date);
                $tmpOperators['mt_success']['dates'] = $reportsColumnData['mt_success'];
                $tmpOperators['mt_success']['total'] = $total_avg_mt_success['sum'];
                $tmpOperators['mt_success']['avg'] = $total_avg_mt_success['avg'];
                $tmpOperators['mt_success']['T_Mo_End'] = $total_avg_mt_success['T_Mo_End'];


                $total_avg_mt_failed = UtilityReports::calculateTotalAVG($service, $reportsColumnData['mt_failed'], $startColumnDateDisplay, $end_date);
                $tmpOperators['mt_failed']['dates'] = $reportsColumnData['mt_failed'];
                $tmpOperators['mt_failed']['total'] = $total_avg_mt_failed['sum'];
                $tmpOperators['mt_failed']['avg'] = $total_avg_mt_failed['avg'];
                $tmpOperators['mt_failed']['T_Mo_End'] = $total_avg_mt_failed['T_Mo_End'];


                $total_avg_fmt_success = UtilityReports::calculateTotalAVG($service, $reportsColumnData['fmt_success'], $startColumnDateDisplay, $end_date);
                $tmpOperators['fmt_success']['dates'] = $reportsColumnData['fmt_success'];
                $tmpOperators['fmt_success']['total'] = $total_avg_fmt_success['sum'];
                $tmpOperators['fmt_success']['avg'] = $total_avg_fmt_success['avg'];
                $tmpOperators['fmt_success']['T_Mo_End'] = $total_avg_fmt_success['T_Mo_End'];


                $total_avg_fmt_failed = UtilityReports::calculateTotalAVG($service, $reportsColumnData['fmt_failed'], $startColumnDateDisplay, $end_date);
                $tmpOperators['fmt_failed']['dates'] = $reportsColumnData['fmt_failed'];
                $tmpOperators['fmt_failed']['total'] = $total_avg_fmt_failed['sum'];
                $tmpOperators['fmt_failed']['avg'] = $total_avg_fmt_failed['avg'];
                $tmpOperators['fmt_failed']['T_Mo_End'] = $total_avg_fmt_failed['T_Mo_End'];


                $total_avg_success_rate = UtilityReports::calculateTotalAVG($service, $reportsColumnData['success_rate'], $startColumnDateDisplay, $end_date);
                $tmpOperators['success_rate']['dates'] = $reportsColumnData['success_rate'];
                $tmpOperators['success_rate']['total'] = $total_avg_success_rate['sum'];
                $tmpOperators['success_rate']['avg'] = $total_avg_success_rate['avg'];
                $tmpOperators['success_rate']['T_Mo_End'] = $total_avg_success_rate['T_Mo_End'];

                $total_avg_fp_success_rate = UtilityReports::calculateTotalAVG($service, $reportsColumnData['fp_success_rate'], $startColumnDateDisplay, $end_date);
                $tmpOperators['fp_success_rate']['dates'] = $reportsColumnData['fp_success_rate'];
                $tmpOperators['fp_success_rate']['total'] = $total_avg_fp_success_rate['sum'];
                $tmpOperators['fp_success_rate']['avg'] = $total_avg_fp_success_rate['avg'];
                $tmpOperators['fp_success_rate']['T_Mo_End'] = $total_avg_fp_success_rate['T_Mo_End'];


                $tmpOperators['last_update'] = $reportsColumnData['last_update'];
                $tmpOperators['data'] = $DataArray;
            }

            $sumemry[] = $tmpOperators;
        }

        return view('report.reportServiceDetails', compact('sumemry', 'no_of_days', 'Operators'));
    }

    function getServiceReportsDateWise($service, $no_of_days, $reportsByIDs, $ServiceOperator)
    {
        if (!empty($no_of_days)) {
            $allColumnData = array();
            $t_rev = array();
            $t_sub = array();
            $reg = array();
            $unreg = array();
            $purged = array();
            $churn = array();
            $renewal = array();
            $sent = array();
            $mtSuccess = array();
            $mtFailed = array();
            $fmtSuccess = array();
            $fmtFailed = array();
            $successRate = array();
            $fp_successRate = array();
            $id_service = $service->id_service;
            $update = false;
            $last_update = "";

            foreach ($no_of_days as $days) {
                $keys = $id_service . "." . $days['date'];
                $summariserow = Arr::get($reportsByIDs, $keys, 0);

                if ($summariserow != 0  && !$update) {
                    $update = true;
                    $last_update = $summariserow['updated_at'];
                }

                $gros_rev = isset($summariserow['gros_rev']) ? $summariserow['gros_rev'] : 0;
                $total_reg = isset($summariserow['total_reg']) ? $summariserow['total_reg'] : 0;
                $total_unreg = isset($summariserow['total_unreg']) ? $summariserow['total_unreg'] : 0;
                $total_subscriber = isset($summariserow['total']) ? $summariserow['total'] : 0;
                $purge_total = isset($summariserow['purge_total']) ? $summariserow['purge_total'] : 0;
                $mt_success = isset($summariserow['mt_success']) ? $summariserow['mt_success'] : 0;
                $mt_failed = isset($summariserow['mt_failed']) ? $summariserow['mt_failed'] : 0;
                $fmt_success = isset($summariserow['fmt_success']) ? $summariserow['fmt_success'] : 0;
                $fmt_failed = isset($summariserow['fmt_failed']) ? $summariserow['fmt_failed'] : 0;
                $mt_sent = $mt_success + $mt_failed;

                if ($total_subscriber > 0) {
                    $churn_value = (((int)$total_reg - (int)$total_unreg + (int)$purge_total) / (int)$total_subscriber) * 100;
                } else {
                    $churn_value = 0;
                }

                if ($mt_sent == 0) {
                    if ($total_subscriber > 0) {
                        $success_rate = ($mt_success / $total_subscriber) * 100;
                    } else {
                        $success_rate = 0;
                    }
                } else {
                    if ($mt_failed == 0) {
                        if ($total_subscriber > 0) {
                            $success_rate = ($mt_success / $total_subscriber) * 100;
                        } else {
                            $success_rate = 0;
                        }
                    } else {
                        $success_rate = ($mt_success / $mt_sent) * 100;
                    }
                }

                $fp_sent = $fmt_success + $fmt_failed;

                if ($fp_sent == 0) {
                    if ($total_subscriber > 0) {
                        $fp_success_rate = ($fmt_success / $total_subscriber) * 100;
                    } else {
                        $fp_success_rate = 0;
                    }
                } else {
                    if ($fmt_failed == 0) {
                        if ($total_subscriber > 0) {
                            $fp_success_rate = ($fmt_success / $total_subscriber) * 100;
                        } else {
                            $fp_success_rate = 0;
                        }
                    } else {
                        $fp_success_rate = ($fmt_success / $fp_sent) * 100;
                    }
                }

                $t_rev[$days['date']]['value'] = $gros_rev;
                $t_rev[$days['date']]['class'] = "bg-hui";

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

                $renewal[$days['date']]['value'] = $mt_sent;
                $renewal[$days['date']]['class'] = "bg-hui";

                $sent[$days['date']]['value'] = $fp_sent;
                $sent[$days['date']]['class'] = "bg-hui";

                $mtSuccess[$days['date']]['value'] = $mt_success;
                $mtSuccess[$days['date']]['class'] = "bg-hui";

                $mtFailed[$days['date']]['value'] = $mt_failed;
                $mtFailed[$days['date']]['class'] = "bg-hui";

                $fmtSuccess[$days['date']]['value'] = $fmt_success;
                $fmtSuccess[$days['date']]['class'] = "bg-hui";

                $fmtFailed[$days['date']]['value'] = $fmt_failed;
                $fmtFailed[$days['date']]['class'] = "bg-hui";

                $successRate[$days['date']]['value'] = $success_rate;
                $successRate[$days['date']]['class'] = "bg-hui";

                $fp_successRate[$days['date']]['value'] = $fp_success_rate;
                $fp_successRate[$days['date']]['class'] = "bg-hui";
            }

            $last_update_show = "Not updated last month";

            if ($last_update != "") {
                $last_update_timestamp = Carbon::parse($last_update);
                $last_update_timestamp->setTimezone('Asia/Jakarta');
                $last_update_show = $last_update_timestamp->format("Y-m-d H:i:s") . " Asia/Jakarta";
            }

            $allColumnData['t_rev'] = $t_rev;
            $allColumnData['t_sub'] = $t_sub;
            $allColumnData['reg'] = $reg;
            $allColumnData['unreg'] = $unreg;
            $allColumnData['purged'] = $purged;
            $allColumnData['churn'] = $churn;
            $allColumnData['renewal'] = $renewal;
            $allColumnData['sent'] = $sent;
            $allColumnData['mt_success'] = $mtSuccess;
            $allColumnData['mt_failed'] = $mtFailed;
            $allColumnData['fmt_success'] = $fmtSuccess;
            $allColumnData['fmt_failed'] = $fmtFailed;
            $allColumnData['success_rate'] = $successRate;
            $allColumnData['fp_success_rate'] = $fp_successRate;
            $allColumnData['last_update'] = $last_update_show;

            return $allColumnData;
        }
    }

    public function getStatusDescription($scode)
    {
        if (isset($scode) && !empty($scode)) {
            if ($this->startsWith($scode, "3:")) {
                $codeArr = array('3:101', '3:105', '3:3:21');
                if (!in_array($scode, $codeArr)) {
                    $code = "3+";
                } else {
                    $code = $scode;
                }
            } elseif ($this->startsWith($scode, "1:")) {
                $code = "1+";
            } elseif ($this->startsWith($scode, "4:2:")) {
                $code = "4:2+";
            }  /*elseif ($scode == '7' && ) {
               $code = "4:2+";
            } elseif (startsWith($scode,"4:2:")) {
               $code = "4:2+";
            }*/ else {
                $code = $scode;
            }

            switch ($code) {
                case "0:1":
                    return $this->getlastcharacters($code) . " - Default error code";
                    break;
                case "0:2":
                    return $this->getlastcharacters($code) . " - MT rejected due to storage partition is full";
                    break;
                case "1+":
                    return $this->getlastcharacters($scode) . " - Success";
                    break;
                case "2":
                    return $this->getlastcharacters($code) . " - Authentication failed (binding failed)";
                    break;
                case "3+":
                    return $this->getlastcharacters($scode) . " - Charging failed";
                    break;
                case "3:101":
                    return $this->getlastcharacters($code) . " - Charging timeout";
                    break;
                case "3:105":
                    return $this->getlastcharacters($code) . " - Invalid MSISDN (recipient)";
                    break;
                case "3:3:21":
                    return $this->getlastcharacters($code) . " - Not enough credit";
                    break;
                case "4:1":
                    return $this->getlastcharacters($code) . " - Invalid shortcode(sender)";
                    break;
                case "4:2+":
                    return $this->getlastcharacters($scode) . " - Mandatory parameter is missing";
                    break;
                case "4:3":
                    return $this->getlastcharacters($code) . " - MT rejected due to long message restriction";
                    break;
                case "4:4:1":
                    return $this->getlastcharacters($code) . " - Multiple tariff is not allowed, but 'tid' parameter is provided by CP";
                    break;
                case "4:4:2":
                    return $this->getlastcharacters($code) . " - The provider 'tid' by CP is not allowed";
                    break;
                case "5:997":
                    return $this->getlastcharacters($code) . " - Invalid trx_id";
                    break;
                case "5:1":
                    return $this->getlastcharacters($code) . " - MT rejected due to subscription quota is finished";
                    break;
                case "5:2":
                    return $this->getlastcharacters($code) . " - MT rejected due to subscriber doesn't have this subscription";
                    break;
                case "5:3":
                    return $this->getlastcharacters($code) . " - MT rejected due to subscription is disabled";
                    break;
                case "5:4":
                    return $this->getlastcharacters($code) . " - Throttling error";
                    break;
                case "3":
                    return "3 - MT message failed";
                    break;
                case "4":
                    return "4 - Insufficient balance";
                    break;
                case "5":
                    return "5 - Charging failed";
                    break;
                case "6":
                    return "6 - Subscriber number not found. Subscriber not registered to SPM service";
                    break;
                case "7":
                    return "7 - Invalid Service ID";
                    break;
                case "8":
                    return "8 - Invalid Transaction ID (for MO pull and MO+MT with session only)";
                    break;
                case "97":
                    return "97 - Subscription Hit Limit Exceeded (only for SPM Service)";
                    break;
                case "99":
                    return "99 - CP Throttle";
                    break;
                case "96":
                    return "96 - Subscribers is not registered in the service SPM";
                    break;
                case "1284":
                    return "1284 - Known error code return from SMSC (actually the MT is delivered to end user)";
                    break;
                case "0505":
                    return "0505 - MO/MT reach max limit";
                    break;
                case "2208":
                    return "2208 - linkID authenticate failed (DM send wrong linkID)";
                    break;
                case "2100":
                    return "2100 - Service is not exist (DM send wrong serviceID or service information)";
                    break;
                case "3306":
                    return "3306 - Charging failed (charging failed from billing)";
                    break;
                case "3309":
                    return "3309 - Invalid product (DM send invalid product ID or information)";
                    break;
                case "3101":
                    return "3101 - Insufficient balance";
                    break;
                case "1100":
                    return "1100 - Wrong service id";
                    break;
                case "1001":
                    return "1001 - User doesn’t exist";
                    break;
                case "1004":
                    return "1004 - User is in blacklist";
                    break;
                case "3103":
                    return "3103 - Charging failed";
                    break;
                case "3320":
                    return "3320 - Subscriber account error (return from billing)";
                    break;
                case "7":
                    return "7 - User doesn’t exist (return from billing)";
                    break;
                default:
                    return $this->getlastcharacters($code) . " - Unknown";
            }
        }
    }

    public function startsWith($string, $startString)
    {
        $len = strlen($startString);
        return (substr($string, 0, $len) === $startString);
    }

    public function getlastcharacters($string)
    {
        $replaced = $string;
        if ((strpos($string, ':') !== false) || (strpos($string, ',') !== false)) {
            $explode = explode(':', $string);
            return $replaced = end($explode);
        }
        return $replaced;
    }
}
