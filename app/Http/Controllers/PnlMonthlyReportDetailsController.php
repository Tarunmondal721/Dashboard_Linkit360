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
use App\Models\PnlSummeryMonth;
use App\Models\MonthlyReportSummery;
use App\Models\ReportsPnlsOperatorSummarizes;
use App\common\Utility;
use App\common\UtilityReports;
use App\common\UtilityReportsMonthly;

class PnlMonthlyReportDetailsController extends Controller
{
    // get operator monthly pnl report
    public function MonthlyPnlReportOperatorDetails(Request $request)
    {
        if(\Auth::user()->can('PNL Detail'))
        {
            $data['OperatorWise'] = $OperatorWise = 1;
            $data['Monthly'] = $Monthly = 1;

            $CountryId = $req_CountryId = $request->country;
            $CompanyId = $req_CompanyId = $request->company;
            $BusinessType = $req_BusinessType = $request->business_type;
            $filterOperator = $req_filterOperator = $request->operatorId;
            $Start_date = $req_Start_date = $request->to;
            $end_date = $req_end_date = trim($request->from);

            if($end_date <= $Start_date)
            {
                $Start_date = $req_Start_date = trim($request->from);
                $end_date = $req_end_date = $request->to;
            }

            $firstDayoftheyear = Carbon::now()->startOfYear()->format('Y-m-d');
            $start_date = $firstDayoftheyear;
            $startColumnDateDisplay = $firstDayoftheyear;
            $end_date = Carbon::now()->format('Y-m-d');
            $month = Carbon::now()->format('F Y');
            $companys = Company::get();

            /* filter Search Section */
            $showAllOperator = true;

            if($request->filled('to') && $request->filled('from'))
            {
                $display_date_input = new Carbon($req_Start_date);
                $start_date = $req_Start_date;
                $startColumnDateDisplay = $display_date_input->format('Y-m-d');
                $end_date = $req_end_date;
                $month = $display_date_input->format('F Y');
            }

            if($request->filled('company') && $req_CompanyId != "allcompany"  && !$request->filled('operatorId'))
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

            if(!isset($Operators))
            {
                $request->session()->flash('alert-success', 'User was successful added!');
                return redirect()->back();
            }

            $Country = Country::all()->toArray();
            $countries = array();
            $sumemry = array();

            if(!empty($Country))
            {
                foreach($Country as $CountryI)
                {
                    $countries[$CountryI['id']] = $CountryI;
                }
            }

            $staticOperators = $Operators->pluck('id_operator')->toArray();

            $datesIndividual = Utility::getRangeDates($startColumnDateDisplay,$end_date);
            $no_of_months = Utility::getRangeMonthsNo($datesIndividual);

            $monthList = array();

            foreach ($no_of_months as $key => $no_of_month) {
                $month_key = $no_of_month['date'];
                $monthList[] = $month_key;
            }

            $QueryMonthlyReports = PnlSummeryMonth::filteroperator($staticOperators)->Months($monthList);

            $user = Auth::user();
            $user_id = $user->id;
            $user_type = $user->type;
            $allowAllOperator = $user->WhowAccessAlOperator($user_type);

            if($allowAllOperator)
            {
                $QueryMonthlyReports = $QueryMonthlyReports->User(0);
            }else{
                $QueryMonthlyReports = $QueryMonthlyReports->User(0);
                $QueryMonthlyUserReports = MonthlyReportSummery::filteroperator($staticOperators)->Months($monthList);
                $QueryMonthlyUserReports = $QueryMonthlyUserReports->User($user_id);
                $allMonthlyUserData = $QueryMonthlyUserReports->get()->toArray();

                $reportsMonthUserData = $this->rearrangeOperatorMonthUser($allMonthlyUserData);
            }

            $allMonthlyData = $QueryMonthlyReports->get()->toArray();

            $reportsMonthData = $this->rearrangeOperatorMonth($allMonthlyData);
            $monthdata = $reportsMonthData;

            $start_date_roi = Carbon::yesterday()->subDays(30)->format('Y-m-d');
            $end_date_roi = Carbon::yesterday()->format('Y-m-d');
            $date_roi = Carbon::now()->format('Y-m-d');
            $reports = ReportsPnlsOperatorSummarizes::filterOperator($staticOperators)
            ->OperatorNotNull()
            ->filterDateRange($start_date_roi,$end_date_roi)
            ->SumOfRoiDataOperator()
            ->get()
            ->toArray();

            $active_subs = ReportsPnlsOperatorSummarizes::filteroperator($staticOperators)
            ->where(['date' => $date_roi])
            ->TotalOperator()
            ->get()
            ->toArray();

            $reportsByOperatorIDs = $this->getReportsByOperatorID($reports);
            $active_subsByOperatorIDs = $this->getReportsByOperatorID($active_subs);

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

                    if(isset($operator->revenueshare)){
                        $merchant_revenue_share = $operator->revenueshare->merchant_revenue_share;
                    }else{
                        $merchant_revenue_share = 100;
                    }

                    $tmpOperators['data'] = $monthdata;

                    $country_id = $operator->country_id;
                    $contain_id = Arr::exists($countries, $country_id);
                    $OperatorCountry = array();

                    if($contain_id )
                    {
                        $tmpOperators['country'] = $countries[$country_id];
                        $OperatorCountry = $countries[$country_id];
                    }

                    if(isset($reportsMonthUserData)  && !empty($reportsMonthUserData)){
                        foreach ($reportsMonthUserData as $key1 => $value1) {
                            if($key1 == $id_operator){
                                foreach ($value1 as $key2 => $value2) {
                                    $monthdata[$id_operator][$key2]['rev'] = $value2['gros_rev'];
                                    $monthdata[$id_operator][$key2]['rev_usd'] = $value2['gros_rev']*$OperatorCountry['usd'];
                                    $monthdata[$id_operator][$key2]['lshare'] = $value2['gros_rev']*($merchant_revenue_share/100);
                                    $monthdata[$id_operator][$key2]['share'] = $value2['gros_rev']*$OperatorCountry['usd']*($merchant_revenue_share/100);
                                }
                            }
                        }
                    }

                    $reportsColumnData = $this->getPNLReportsDateWise($operator,$no_of_months,$monthdata,$OperatorCountry,$reportsByOperatorIDs,$active_subsByOperatorIDs);

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

                    $total_avg_rev_usd = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['end_user_rev_usd'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['end_user_rev_usd']['dates'] = $reportsColumnData['end_user_rev_usd'];
                    $tmpOperators['end_user_rev_usd']['total'] = $total_avg_rev_usd['sum'];
                    $tmpOperators['end_user_rev_usd']['t_mo_end'] = $total_avg_rev_usd['T_Mo_End'];
                    $tmpOperators['end_user_rev_usd']['avg'] = $total_avg_rev_usd['avg'];

                    $total_avg_rev = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['end_user_rev'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['end_user_rev']['dates'] = $reportsColumnData['end_user_rev'];
                    $tmpOperators['end_user_rev']['total'] = $total_avg_rev['sum'];
                    $tmpOperators['end_user_rev']['t_mo_end'] = $total_avg_rev['T_Mo_End'];
                    $tmpOperators['end_user_rev']['avg'] = $total_avg_rev['avg'];

                    $total_avg_gros_rev_usd = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['gros_rev_usd'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['gros_rev_usd']['dates'] = $reportsColumnData['gros_rev_usd'];
                    $tmpOperators['gros_rev_usd']['total'] = $total_avg_gros_rev_usd['sum'];
                    $tmpOperators['gros_rev_usd']['t_mo_end'] = $total_avg_gros_rev_usd['T_Mo_End'];
                    $tmpOperators['gros_rev_usd']['avg'] = $total_avg_gros_rev_usd['avg'];

                    $total_avg_gros_rev = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['gros_rev'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['gros_rev']['dates'] = $reportsColumnData['gros_rev'];
                    $tmpOperators['gros_rev']['total'] = $total_avg_gros_rev['sum'];
                    $tmpOperators['gros_rev']['t_mo_end'] = $total_avg_gros_rev['T_Mo_End'];
                    $tmpOperators['gros_rev']['avg'] = $total_avg_gros_rev['avg'];

                    $total_avg_cost_campaign = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['cost_campaign'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['cost_campaign']['dates'] = $reportsColumnData['cost_campaign'];
                    $tmpOperators['cost_campaign']['total'] = $total_avg_cost_campaign['sum'];
                    $tmpOperators['cost_campaign']['t_mo_end'] = $total_avg_cost_campaign['T_Mo_End'];
                    $tmpOperators['cost_campaign']['avg'] = $total_avg_cost_campaign['avg'];

                    $total_avg_other_cost = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['other_cost'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['other_cost']['dates'] = $reportsColumnData['other_cost'];
                    $tmpOperators['other_cost']['total'] = $total_avg_other_cost['sum'];
                    $tmpOperators['other_cost']['t_mo_end'] = $total_avg_other_cost['T_Mo_End'];
                    $tmpOperators['other_cost']['avg'] = $total_avg_other_cost['avg'];

                    $total_avg_hosting_cost = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['hosting_cost'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['hosting_cost']['dates'] = $reportsColumnData['hosting_cost'];
                    $tmpOperators['hosting_cost']['total'] = $total_avg_hosting_cost['sum'];
                    $tmpOperators['hosting_cost']['t_mo_end'] = $total_avg_hosting_cost['T_Mo_End'];
                    $tmpOperators['hosting_cost']['avg'] = $total_avg_hosting_cost['avg'];

                    $total_avg_content = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['content'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['content']['dates'] = $reportsColumnData['content'];
                    $tmpOperators['content']['total'] = $total_avg_content['sum'];
                    $tmpOperators['content']['t_mo_end'] = $total_avg_content['T_Mo_End'];
                    $tmpOperators['content']['avg'] = $total_avg_content['avg'];

                    $total_avg_rnd = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['rnd'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['rnd']['dates'] = $reportsColumnData['rnd'];
                    $tmpOperators['rnd']['total'] = $total_avg_rnd['sum'];
                    $tmpOperators['rnd']['t_mo_end'] = $total_avg_rnd['T_Mo_End'];
                    $tmpOperators['rnd']['avg'] = $total_avg_rnd['avg'];

                    $total_avg_bd = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['bd'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['bd']['dates'] = $reportsColumnData['bd'];
                    $tmpOperators['bd']['total'] = $total_avg_bd['sum'];
                    $tmpOperators['bd']['t_mo_end'] = $total_avg_bd['T_Mo_End'];
                    $tmpOperators['bd']['avg'] = $total_avg_bd['avg'];

                    $total_avg_market_cost = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['market_cost'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['market_cost']['dates'] = $reportsColumnData['market_cost'];
                    $tmpOperators['market_cost']['total'] = $total_avg_market_cost['sum'];
                    $tmpOperators['market_cost']['t_mo_end'] = $total_avg_market_cost['T_Mo_End'];
                    $tmpOperators['market_cost']['avg'] = $total_avg_market_cost['avg'];

                    $total_avg_misc_cost = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['misc_cost'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['misc_cost']['dates'] = $reportsColumnData['misc_cost'];
                    $tmpOperators['misc_cost']['total'] = $total_avg_misc_cost['sum'];
                    $tmpOperators['misc_cost']['t_mo_end'] = $total_avg_misc_cost['T_Mo_End'];
                    $tmpOperators['misc_cost']['avg'] = $total_avg_misc_cost['avg'];

                    $total_avg_platform = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['platform'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['platform']['dates'] = $reportsColumnData['platform'];
                    $tmpOperators['platform']['total'] = $total_avg_platform['sum'];
                    $tmpOperators['platform']['t_mo_end'] = $total_avg_platform['T_Mo_End'];
                    $tmpOperators['platform']['avg'] = $total_avg_platform['avg'];

                    $total_avg_pnl = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['pnl'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['pnl']['dates'] = $reportsColumnData['pnl'];
                    $tmpOperators['pnl']['total'] = $total_avg_pnl['sum'];
                    $tmpOperators['pnl']['t_mo_end'] = $total_avg_pnl['T_Mo_End'];
                    $tmpOperators['pnl']['avg'] = $total_avg_pnl['avg'];

                    $total_avg_net_after_tax = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['net_after_tax'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['net_after_tax']['dates'] = $reportsColumnData['net_after_tax'];
                    $tmpOperators['net_after_tax']['total'] = $total_avg_net_after_tax['sum'];
                    $tmpOperators['net_after_tax']['t_mo_end'] = $total_avg_net_after_tax['T_Mo_End'];
                    $tmpOperators['net_after_tax']['avg'] = $total_avg_net_after_tax['avg'];

                    $total_avg_net_revenue_after_tax = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['net_revenue_after_tax'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['net_revenue_after_tax']['dates'] = $reportsColumnData['net_revenue_after_tax'];
                    $tmpOperators['net_revenue_after_tax']['total'] = $total_avg_net_revenue_after_tax['sum'];
                    $tmpOperators['net_revenue_after_tax']['t_mo_end'] = $total_avg_net_revenue_after_tax['T_Mo_End'];
                    $tmpOperators['net_revenue_after_tax']['avg'] = $total_avg_net_revenue_after_tax['avg'];

                    $total_avg_br = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['br'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['br']['dates'] = $reportsColumnData['br'];
                    $tmpOperators['br']['total'] = $total_avg_br['sum'];
                    $tmpOperators['br']['t_mo_end'] = $total_avg_br['T_Mo_End'];
                    $tmpOperators['br']['avg'] = $total_avg_br['avg'];

                    $total_avg_fp = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['fp'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['fp']['dates'] = $reportsColumnData['fp'];
                    $tmpOperators['fp']['total'] = $total_avg_fp['sum'];
                    $tmpOperators['fp']['t_mo_end'] = $total_avg_fp['T_Mo_End'];
                    $tmpOperators['fp']['avg'] = $total_avg_fp['avg'];

                    $total_avg_fp_success = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['fp_success'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['fp_success']['dates'] = $reportsColumnData['fp_success'];
                    $tmpOperators['fp_success']['total'] = $total_avg_fp_success['sum'];
                    $tmpOperators['fp_success']['t_mo_end'] = $total_avg_fp_success['T_Mo_End'];
                    $tmpOperators['fp_success']['avg'] = $total_avg_fp_success['avg'];

                    $total_avg_fp_failed = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['fp_failed'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['fp_failed']['dates'] = $reportsColumnData['fp_failed'];
                    $tmpOperators['fp_failed']['total'] = $total_avg_fp_failed['sum'];
                    $tmpOperators['fp_failed']['t_mo_end'] = $total_avg_fp_failed['T_Mo_End'];
                    $tmpOperators['fp_failed']['avg'] = $total_avg_fp_failed['avg'];

                    $total_avg_dp = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['dp'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['dp']['dates'] = $reportsColumnData['dp'];
                    $tmpOperators['dp']['total'] = $total_avg_dp['sum'];
                    $tmpOperators['dp']['t_mo_end'] = $total_avg_dp['T_Mo_End'];
                    $tmpOperators['dp']['avg'] = $total_avg_dp['avg'];

                    $total_avg_dp_success = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['dp_success'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['dp_success']['dates'] = $reportsColumnData['dp_success'];
                    $tmpOperators['dp_success']['total'] = $total_avg_dp_success['sum'];
                    $tmpOperators['dp_success']['t_mo_end'] = $total_avg_dp_success['T_Mo_End'];
                    $tmpOperators['dp_success']['avg'] = $total_avg_dp_success['avg'];

                    $total_avg_dp_failed = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['dp_failed'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['dp_failed']['dates'] = $reportsColumnData['dp_failed'];
                    $tmpOperators['dp_failed']['total'] = $total_avg_dp_failed['sum'];
                    $tmpOperators['dp_failed']['t_mo_end'] = $total_avg_dp_failed['T_Mo_End'];
                    $tmpOperators['dp_failed']['avg'] = $total_avg_dp_failed['avg'];

                    $total_avg_renewal = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['renewal'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['renewal']['dates'] = $reportsColumnData['renewal'];
                    $tmpOperators['renewal']['total'] = $total_avg_renewal['sum'];
                    $tmpOperators['renewal']['t_mo_end'] = $total_avg_renewal['T_Mo_End'];
                    $tmpOperators['renewal']['avg'] = $total_avg_renewal['avg'];

                    $total_avg_vat = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['vat'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['vat']['dates'] = $reportsColumnData['vat'];
                    $tmpOperators['vat']['total'] = $total_avg_vat['sum'];
                    $tmpOperators['vat']['t_mo_end'] = $total_avg_vat['T_Mo_End'];
                    $tmpOperators['vat']['avg'] = $total_avg_vat['avg'];

                    $total_avg_spec_tax = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['spec_tax'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['spec_tax']['dates'] = $reportsColumnData['spec_tax'];
                    $tmpOperators['spec_tax']['total'] = $total_avg_spec_tax['sum'];
                    $tmpOperators['spec_tax']['t_mo_end'] = $total_avg_spec_tax['T_Mo_End'];
                    $tmpOperators['spec_tax']['avg'] = $total_avg_spec_tax['avg'];

                    $total_avg_government_cost = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['government_cost'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['government_cost']['dates'] = $reportsColumnData['government_cost'];
                    $tmpOperators['government_cost']['total'] = $total_avg_government_cost['sum'];
                    $tmpOperators['government_cost']['t_mo_end'] = $total_avg_government_cost['T_Mo_End'];
                    $tmpOperators['government_cost']['avg'] = $total_avg_government_cost['avg'];

                    $total_avg_dealer_commision = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['dealer_commision'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['dealer_commision']['dates'] = $reportsColumnData['dealer_commision'];
                    $tmpOperators['dealer_commision']['total'] = $total_avg_dealer_commision['sum'];
                    $tmpOperators['dealer_commision']['t_mo_end'] = $total_avg_dealer_commision['T_Mo_End'];
                    $tmpOperators['dealer_commision']['avg'] = $total_avg_dealer_commision['avg'];

                    $total_avg_wht = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['wht'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['wht']['dates'] = $reportsColumnData['wht'];
                    $tmpOperators['wht']['total'] = $total_avg_wht['sum'];
                    $tmpOperators['wht']['t_mo_end'] = $total_avg_wht['T_Mo_End'];
                    $tmpOperators['wht']['avg'] = $total_avg_wht['avg'];

                    $total_avg_misc_tax = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['misc_tax'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['misc_tax']['dates'] = $reportsColumnData['misc_tax'];
                    $tmpOperators['misc_tax']['total'] = $total_avg_misc_tax['sum'];
                    $tmpOperators['misc_tax']['t_mo_end'] = $total_avg_misc_tax['T_Mo_End'];
                    $tmpOperators['misc_tax']['avg'] = $total_avg_misc_tax['avg'];

                    $total_avg_other_tax = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['other_tax'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['other_tax']['dates'] = $reportsColumnData['other_tax'];
                    $tmpOperators['other_tax']['total'] = $total_avg_other_tax['sum'];
                    $tmpOperators['other_tax']['t_mo_end'] = $total_avg_other_tax['T_Mo_End'];
                    $tmpOperators['other_tax']['avg'] = $total_avg_other_tax['avg'];

                    $total_avg_uso = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['uso'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['uso']['dates'] = $reportsColumnData['uso'];
                    $tmpOperators['uso']['total'] = $total_avg_uso['sum'];
                    $tmpOperators['uso']['t_mo_end'] = $total_avg_uso['T_Mo_End'];
                    $tmpOperators['uso']['avg'] = $total_avg_uso['avg'];

                    $total_avg_agre_paxxa = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['agre_paxxa'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['agre_paxxa']['dates'] = $reportsColumnData['agre_paxxa'];
                    $tmpOperators['agre_paxxa']['total'] = $total_avg_agre_paxxa['sum'];
                    $tmpOperators['agre_paxxa']['t_mo_end'] = $total_avg_agre_paxxa['T_Mo_End'];
                    $tmpOperators['agre_paxxa']['avg'] = $total_avg_agre_paxxa['avg'];

                    $total_avg_sbaf = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['sbaf'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['sbaf']['dates'] = $reportsColumnData['sbaf'];
                    $tmpOperators['sbaf']['total'] = $total_avg_sbaf['sum'];
                    $tmpOperators['sbaf']['t_mo_end'] = $total_avg_sbaf['T_Mo_End'];
                    $tmpOperators['sbaf']['avg'] = $total_avg_sbaf['avg'];

                    $total_avg_clicks = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['clicks'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['clicks']['dates'] = $reportsColumnData['clicks'];
                    $tmpOperators['clicks']['total'] = $total_avg_clicks['sum'];
                    $tmpOperators['clicks']['t_mo_end'] = $total_avg_clicks['T_Mo_End'];
                    $tmpOperators['clicks']['avg'] = $total_avg_clicks['avg'];

                    $total_avg_ratio_for_cpa = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['ratio_for_cpa'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['ratio_for_cpa']['dates'] = $reportsColumnData['ratio_for_cpa'];
                    $tmpOperators['ratio_for_cpa']['total'] = $total_avg_ratio_for_cpa['sum'];
                    $tmpOperators['ratio_for_cpa']['t_mo_end'] = $total_avg_ratio_for_cpa['T_Mo_End'];
                    $tmpOperators['ratio_for_cpa']['avg'] = $total_avg_ratio_for_cpa['avg'];

                    $total_avg_cpa_price = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['cpa_price'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['cpa_price']['dates'] = $reportsColumnData['cpa_price'];
                    $tmpOperators['cpa_price']['total'] = $total_avg_cpa_price['sum'];
                    $tmpOperators['cpa_price']['t_mo_end'] = $total_avg_cpa_price['T_Mo_End'];
                    $tmpOperators['cpa_price']['avg'] = $total_avg_cpa_price['avg'];

                    $total_avg_cr_mo_clicks = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['cr_mo_clicks'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['cr_mo_clicks']['dates'] = $reportsColumnData['cr_mo_clicks'];
                    $tmpOperators['cr_mo_clicks']['total'] = $total_avg_cr_mo_clicks['sum'];
                    $tmpOperators['cr_mo_clicks']['t_mo_end'] = $total_avg_cr_mo_clicks['T_Mo_End'];
                    $tmpOperators['cr_mo_clicks']['avg'] = $total_avg_cr_mo_clicks['avg'];

                    $total_avg_cr_mo_landing = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['cr_mo_landing'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['cr_mo_landing']['dates'] = $reportsColumnData['cr_mo_landing'];
                    $tmpOperators['cr_mo_landing']['total'] = $total_avg_cr_mo_landing['sum'];
                    $tmpOperators['cr_mo_landing']['t_mo_end'] = $total_avg_cr_mo_landing['T_Mo_End'];
                    $tmpOperators['cr_mo_landing']['avg'] = $total_avg_cr_mo_landing['avg'];

                    $total_avg_landing = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['landing'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['landing']['dates'] = $reportsColumnData['landing'];
                    $tmpOperators['landing']['total'] = $total_avg_landing['sum'];
                    $tmpOperators['landing']['t_mo_end'] = $total_avg_landing['T_Mo_End'];
                    $tmpOperators['landing']['avg'] = $total_avg_landing['avg'];

                    $total_avg_mo = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['mo'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['mo']['dates'] = $reportsColumnData['mo'];
                    $tmpOperators['mo']['total'] = $total_avg_mo['sum'];
                    $tmpOperators['mo']['t_mo_end'] = $total_avg_mo['T_Mo_End'];
                    $tmpOperators['mo']['avg'] = $total_avg_mo['avg'];

                    $total_avg_reg = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['reg'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['reg']['dates'] = $reportsColumnData['reg'];
                    $tmpOperators['reg']['total'] = $total_avg_reg['sum'];
                    $tmpOperators['reg']['t_mo_end'] = $total_avg_reg['T_Mo_End'];
                    $tmpOperators['reg']['avg'] = $total_avg_reg['avg'];

                    $total_avg_unreg = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['unreg'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['unreg']['dates'] = $reportsColumnData['unreg'];
                    $tmpOperators['unreg']['total'] = $total_avg_unreg['sum'];
                    $tmpOperators['unreg']['t_mo_end'] = $total_avg_unreg['T_Mo_End'];
                    $tmpOperators['unreg']['avg'] = $total_avg_unreg['avg'];

                    $total_avg_price_mo = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['price_mo'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['price_mo']['dates'] = $reportsColumnData['price_mo'];
                    $tmpOperators['price_mo']['total'] = $total_avg_price_mo['sum'];
                    $tmpOperators['price_mo']['t_mo_end'] = $total_avg_price_mo['T_Mo_End'];
                    $tmpOperators['price_mo']['avg'] = $total_avg_price_mo['avg'];

                    $total_avg_price_mo_cost = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['price_mo_cost'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['price_mo_cost']['dates'] = $reportsColumnData['price_mo_cost'];
                    $tmpOperators['price_mo_cost']['total'] = $total_avg_price_mo_cost['sum'];
                    $tmpOperators['price_mo_cost']['t_mo_end'] = $total_avg_price_mo_cost['T_Mo_End'];
                    $tmpOperators['price_mo_cost']['avg'] = $total_avg_price_mo_cost['avg'];

                    $total_avg_price_mo_mo = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['price_mo_mo'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['price_mo_mo']['dates'] = $reportsColumnData['price_mo_mo'];
                    $tmpOperators['price_mo_mo']['total'] = $total_avg_price_mo_mo['sum'];
                    $tmpOperators['price_mo_mo']['t_mo_end'] = $total_avg_price_mo_mo['T_Mo_End'];
                    $tmpOperators['price_mo_mo']['avg'] = $total_avg_price_mo_mo['avg'];

                    $total_avg_active_subs = UtilityReportsMonthly::calculateTotalSubscribe($operator,$reportsColumnData['active_subs'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['active_subs']['dates'] = $reportsColumnData['active_subs'];
                    $tmpOperators['active_subs']['total'] = $total_avg_active_subs['sum'];
                    $tmpOperators['active_subs']['t_mo_end'] = $total_avg_active_subs['T_Mo_End'];
                    $tmpOperators['active_subs']['avg'] = $total_avg_active_subs['avg'];

                    $total_avg_arpu_7 = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['arpu_7'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['arpu_7']['dates'] = $reportsColumnData['arpu_7'];
                    $tmpOperators['arpu_7']['total'] = $total_avg_arpu_7['sum'];
                    $tmpOperators['arpu_7']['t_mo_end'] = $total_avg_arpu_7['T_Mo_End'];
                    $tmpOperators['arpu_7']['avg'] = $total_avg_arpu_7['avg'];

                    $total_avg_arpu_7_usd = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['arpu_7_usd'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['arpu_7_usd']['dates'] = $reportsColumnData['arpu_7_usd'];
                    $tmpOperators['arpu_7_usd']['total'] = $total_avg_arpu_7_usd['sum'];
                    $tmpOperators['arpu_7_usd']['t_mo_end'] = $total_avg_arpu_7_usd['T_Mo_End'];
                    $tmpOperators['arpu_7_usd']['avg'] = $total_avg_arpu_7_usd['avg'];

                    $total_avg_arpu_30 = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['arpu_30'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['arpu_30']['dates'] = $reportsColumnData['arpu_30'];
                    $tmpOperators['arpu_30']['total'] = $total_avg_arpu_30['sum'];
                    $tmpOperators['arpu_30']['t_mo_end'] = $total_avg_arpu_30['T_Mo_End'];
                    $tmpOperators['arpu_30']['avg'] = $total_avg_arpu_30['avg'];

                    $total_avg_arpu_30_usd = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['arpu_30_usd'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['arpu_30_usd']['dates'] = $reportsColumnData['arpu_30_usd'];
                    $tmpOperators['arpu_30_usd']['total'] = $total_avg_arpu_30_usd['sum'];
                    $tmpOperators['arpu_30_usd']['t_mo_end'] = $total_avg_arpu_30_usd['T_Mo_End'];
                    $tmpOperators['arpu_30_usd']['avg'] = $total_avg_arpu_30_usd['avg'];

                    $total_avg_reg_sub = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['reg_sub'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['reg_sub']['dates'] = $reportsColumnData['reg_sub'];
                    $tmpOperators['reg_sub']['total'] = $total_avg_reg_sub['sum'];
                    $tmpOperators['reg_sub']['t_mo_end'] = $total_avg_reg_sub['T_Mo_End'];
                    $tmpOperators['reg_sub']['avg'] = $total_avg_reg_sub['avg'];

                    $total_avg_roi = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['roi'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['roi']['dates'] = $reportsColumnData['roi'];
                    $tmpOperators['roi']['total'] = $total_avg_roi['sum'];
                    $tmpOperators['roi']['t_mo_end'] = $total_avg_roi['T_Mo_End'];
                    $tmpOperators['roi']['avg'] = $total_avg_roi['avg'];

                    $total_avg_bill = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['bill'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['bill']['dates'] = $reportsColumnData['bill'];
                    $tmpOperators['bill']['total'] = 0;
                    $tmpOperators['bill']['t_mo_end'] = 0;
                    $tmpOperators['bill']['avg'] = $total_avg_bill['avg'];

                    $total_avg_firstpush = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['firstpush'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['firstpush']['dates'] = $reportsColumnData['firstpush'];
                    $tmpOperators['firstpush']['total'] = 0;
                    $tmpOperators['firstpush']['t_mo_end'] = 0;
                    $tmpOperators['firstpush']['avg'] = $total_avg_firstpush['avg'];

                    $total_avg_dailypush = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['dailypush'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['dailypush']['dates'] = $reportsColumnData['dailypush'];
                    $tmpOperators['dailypush']['total'] = 0;
                    $tmpOperators['dailypush']['t_mo_end'] = 0;
                    $tmpOperators['dailypush']['avg'] = $total_avg_dailypush['avg'];

                    $total_avg_last_7_gros_rev = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['last_7_gros_rev'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['last_7_gros_rev']['dates'] = $reportsColumnData['last_7_gros_rev'];
                    $tmpOperators['last_7_gros_rev']['total'] = $total_avg_last_7_gros_rev['sum'];
                    $tmpOperators['last_7_gros_rev']['t_mo_end'] = $total_avg_last_7_gros_rev['T_Mo_End'];
                    $tmpOperators['last_7_gros_rev']['avg'] = $total_avg_last_7_gros_rev['avg'];

                    $total_avg_last_7_reg = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['last_7_reg'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['last_7_reg']['dates'] = $reportsColumnData['last_7_reg'];
                    $tmpOperators['last_7_reg']['total'] = $total_avg_last_7_reg['sum'];
                    $tmpOperators['last_7_reg']['t_mo_end'] = $total_avg_last_7_reg['T_Mo_End'];
                    $tmpOperators['last_7_reg']['avg'] = $total_avg_last_7_reg['avg'];

                    $total_avg_last_30_gros_rev = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['last_30_gros_rev'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['last_30_gros_rev']['dates'] = $reportsColumnData['last_30_gros_rev'];
                    $tmpOperators['last_30_gros_rev']['total'] = $total_avg_last_30_gros_rev['sum'];
                    $tmpOperators['last_30_gros_rev']['t_mo_end'] = $total_avg_last_30_gros_rev['T_Mo_End'];
                    $tmpOperators['last_30_gros_rev']['avg'] = $total_avg_last_30_gros_rev['avg'];

                    $total_avg_last_30_reg = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['last_30_reg'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['last_30_reg']['dates'] = $reportsColumnData['last_30_reg'];
                    $tmpOperators['last_30_reg']['total'] = $total_avg_last_30_reg['sum'];
                    $tmpOperators['last_30_reg']['t_mo_end'] = $total_avg_last_30_reg['T_Mo_End'];
                    $tmpOperators['last_30_reg']['avg'] = $total_avg_last_30_reg['avg'];

                    $sumemry[] = $tmpOperators;
                }
            }

            $no_of_days = $no_of_months;
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

            if(!empty($sumOfSummaryData)){
                foreach ($sumOfSummaryData as $key1 => $value1) {
                    if($key1 == 'roi'){
                        foreach ($value1 as $key2 => $value2) {
                            if($key2 == 'dates'){
                                foreach ($value2 as $key3 => $value3) {
                                    $roi_arpu = ($sumOfSummaryData['arpu_30_usd']['dates'][$key3]['value'] == 0) ? (float)0 : $sumOfSummaryData['arpu_30_usd']['dates'][$key3]['value'];
                                    $price_mo = ($sumOfSummaryData['price_mo']['dates'][$key3]['value'] == 0) ? (float)0 : $sumOfSummaryData['price_mo']['dates'][$key3]['value'];
                                    $sumOfSummaryData['roi']['dates'][$key3]['value'] = ($roi_arpu == 0) ? (float)0 : ($price_mo / $roi_arpu);
                                }
                            }
                        }
                    }
                }
            }
            $date = Carbon::parse($end_date)->format('Y-m');

            return view('report.monthly_pnlsummary', compact('date','no_of_days','sumemry','sumOfSummaryData','data'));
        }else{
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    // get country monthly pnl report
    public function MonthlyPnlReportCountryDetails(Request $request)
    {
        if(\Auth::user()->can('PNL Detail'))
        {
            $data['CountryWise'] = $CountryWise = 1;
            $data['Monthly'] = $monthly = 1;


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

            $firstDayoftheyear = Carbon::now()->startOfYear()->format('Y-m-d');
            $start_date = $firstDayoftheyear;
            $startColumnDateDisplay = $firstDayoftheyear;
            $end_date = Carbon::now()->format('Y-m-d');
            $month = Carbon::now()->format('F Y');
            $year = Carbon::now()->format('Y');
            $companys = Company::get();

            /* filter Search Section */
            $showAllOperator = true;
            if($request->filled('to') && $request->filled('from'))
            {
                $display_date_input = new Carbon($req_Start_date);
                $start_date = $req_Start_date;
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
                    $Operators = Operator::with('revenueshare','RevenushareByDate')->Status(1)->GetOperatorByOperatorId($Operators_company)->get();
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
            //     $Operators = Operator::with('revenueshare')->Status(1)->GetOperatorByCountryId($req_CountryId)->get();
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
                $Operators = Operator::with('revenueshare','RevenushareByDate')->Status(1)->GetOperatorByOperatorId($filterOperator)->get();
                $showAllOperator = false;
            }

            if($showAllOperator)
            {
                $Operators = Operator::with('revenueshare','RevenushareByDate')->Status(1)->get();
            }

            if(!isset($Operators))
            {
                $request->session()->flash('alert-success', 'User was successful added!');
                return redirect()->back();
            }

            $Country = Country::all()->toArray();
            $countries = array();
            $sumemry = array();

            if(!empty($Country))
            {
                foreach($Country as $CountryI)
                {
                    $countries[$CountryI['id']]=$CountryI;
                }
            }

            $staticOperators = $Operators->pluck('id_operator')->toArray();

            $datesIndividual = Utility::getRangeDates($startColumnDateDisplay,$end_date);
            $no_of_months = Utility::getRangeMonthsNo($datesIndividual);

            $monthList=array();

            foreach ($no_of_months as $key => $no_of_month) {
                $month_key =$no_of_month['date'];
                $monthList[]=$month_key;
            }

            $QueryMonthlyReports = PnlSummeryMonth::filteroperator($staticOperators)
                            ->Months($monthList);

            $user = Auth::user();
            $user_id = $user->id;
            $user_type = $user->type;
            $allowAllOperator = $user->WhowAccessAlOperator($user_type);

            if($allowAllOperator)
            {
                $QueryMonthlyReports = $QueryMonthlyReports->User(0);
            }else{
                $QueryMonthlyReports = $QueryMonthlyReports->User(0);
                $QueryMonthlyUserReports = MonthlyReportSummery::filteroperator($staticOperators)->Months($monthList);
                $QueryMonthlyUserReports = $QueryMonthlyUserReports->User($user_id);
                $allMonthlyUserData = $QueryMonthlyUserReports
                            ->get()->toArray();

                $reportsMonthUserData = $this->rearrangeOperatorMonthUser($allMonthlyUserData);
            }

            $allMonthlyData = $QueryMonthlyReports->get()->toArray();

            $reportsMonthData = $this->rearrangeOperatorMonth($allMonthlyData);
            $monthdata = $reportsMonthData;

            $start_date_roi = Carbon::yesterday()->subDays(30)->format('Y-m-d');
            $end_date_roi = Carbon::yesterday()->format('Y-m-d');
            $date_roi = Carbon::now()->format('Y-m-d');
            $reports = ReportsPnlsOperatorSummarizes::filterOperator($staticOperators)
                ->OperatorNotNull()
                ->filterDateRange($start_date_roi,$end_date_roi)
                ->SumOfRoiDataOperator()
                ->get()->toArray();

            $active_subs = ReportsPnlsOperatorSummarizes::filteroperator($staticOperators)
                ->where(['date' => $date_roi])
                ->TotalOperator()
                ->get()->toArray();

            $reportsByOperatorIDs = $this->getReportsByOperatorID($reports);
            $active_subsByOperatorIDs = $this->getReportsByOperatorID($active_subs);

            /*Start*/

            if(!empty($Operators))
            {
                foreach($Operators as $operator)
                {
                    if($operator->status == 0)
                        continue;


                    $id_operator = $operator->id_operator;
                    $tmpOperators=array();
                    $tmpOperators['operator'] =$operator;
                    // $monthdata = $reportsMonthData[$id_operator];
                    if(!isset($reportsMonthData[$id_operator]))
                    {
                        // if The Operator not founds in that array
                        continue;
                    }

                    if(isset($operator->revenueshare)){
                        $merchant_revenue_share = $operator->revenueshare->merchant_revenue_share;
                    }else{
                        $merchant_revenue_share = 100;
                    }
                    //dd($monthdata);
                    $tmpOperators['data'] =$monthdata;
                    //dd($tmpOperators);
                    $country_id  =$operator->country_id;
                    $contain_id = Arr::exists($countries, $country_id);
                    $OperatorCountry = array();
                    if($contain_id )
                    {
                        $tmpOperators['country']=$countries[$country_id];
                        $tmpOperators['country_name']=$countries[$country_id]['country'];
                        $OperatorCountry = $countries[$country_id];
                    }

                    if(isset($reportsMonthUserData)  && !empty($reportsMonthUserData)){
                        foreach ($reportsMonthUserData as $key1 => $value1) {
                            if($key1 == $id_operator){
                                foreach ($value1 as $key2 => $value2) {
                                    $monthdata[$id_operator][$key2]['rev'] = $value2['gros_rev'];
                                    $monthdata[$id_operator][$key2]['rev_usd'] = $value2['gros_rev']*$OperatorCountry['usd'];
                                    $monthdata[$id_operator][$key2]['lshare'] = $value2['gros_rev']*($merchant_revenue_share/100);
                                    $monthdata[$id_operator][$key2]['share'] = $value2['gros_rev']*$OperatorCountry['usd']*($merchant_revenue_share/100);
                                }
                            }
                        }
                    }

                    $reportsColumnData = $this->getPNLReportsDateWise($operator,$no_of_months,$monthdata,$OperatorCountry,$reportsByOperatorIDs,$active_subsByOperatorIDs);
                    // dd($reportsColumnData);
                    $tmpOperators['month_string'] = $month;

                    $tmpOperators['last_update'] = $reportsColumnData['last_update'];
                    $tmpOperators['hostingCost'] = isset($operator->hostingCost) ? $operator->hostingCost : 0;
                    $tmpOperators['contentCost'] = isset($operator->content) ? $operator->content : 0;
                    $tmpOperators['rndCost'] = isset($operator->rnd) ? $operator->rnd : 0;
                    $tmpOperators['bdCost'] = 2.5;
                    $tmpOperators['marketCost'] = 1.5;
                    $tmpOperators['vatTax'] = isset($operator->vat) ? $operator->vat : 0;
                    $tmpOperators['whtTax'] = isset($operator->wht) ? $operator->wht : 0;
                    $tmpOperators['miscTax'] = isset($operator->miscTax) ? $operator->miscTax : 0;

                    $total_avg_rev_usd = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['end_user_rev_usd'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['end_user_rev_usd']['dates'] = $reportsColumnData['end_user_rev_usd'];
                    $tmpOperators['end_user_rev_usd']['total'] = $total_avg_rev_usd['sum'];
                    $tmpOperators['end_user_rev_usd']['t_mo_end'] = $total_avg_rev_usd['T_Mo_End'];
                    $tmpOperators['end_user_rev_usd']['avg'] = $total_avg_rev_usd['avg'];


                    $total_avg_rev = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['end_user_rev'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['end_user_rev']['dates'] = $reportsColumnData['end_user_rev'];
                    $tmpOperators['end_user_rev']['total'] = $total_avg_rev['sum'];
                    $tmpOperators['end_user_rev']['t_mo_end'] = $total_avg_rev['T_Mo_End'];
                    $tmpOperators['end_user_rev']['avg'] = $total_avg_rev['avg'];


                    $total_avg_gros_rev_usd = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['gros_rev_usd'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['gros_rev_usd']['dates'] = $reportsColumnData['gros_rev_usd'];
                    $tmpOperators['gros_rev_usd']['total'] = $total_avg_gros_rev_usd['sum'];
                    $tmpOperators['gros_rev_usd']['t_mo_end'] = $total_avg_gros_rev_usd['T_Mo_End'];
                    $tmpOperators['gros_rev_usd']['avg'] = $total_avg_gros_rev_usd['avg'];


                    $total_avg_gros_rev = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['gros_rev'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['gros_rev']['dates'] = $reportsColumnData['gros_rev'];
                    $tmpOperators['gros_rev']['total'] = $total_avg_gros_rev['sum'];
                    $tmpOperators['gros_rev']['t_mo_end'] = $total_avg_gros_rev['T_Mo_End'];
                    $tmpOperators['gros_rev']['avg'] = $total_avg_gros_rev['avg'];


                    $total_avg_cost_campaign = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['cost_campaign'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['cost_campaign']['dates'] = $reportsColumnData['cost_campaign'];
                    $tmpOperators['cost_campaign']['total'] = $total_avg_cost_campaign['sum'];
                    $tmpOperators['cost_campaign']['t_mo_end'] = $total_avg_cost_campaign['T_Mo_End'];
                    $tmpOperators['cost_campaign']['avg'] = $total_avg_cost_campaign['avg'];


                    $total_avg_other_cost = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['other_cost'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['other_cost']['dates'] = $reportsColumnData['other_cost'];
                    $tmpOperators['other_cost']['total'] = $total_avg_other_cost['sum'];
                    $tmpOperators['other_cost']['t_mo_end'] = $total_avg_other_cost['T_Mo_End'];
                    $tmpOperators['other_cost']['avg'] = $total_avg_other_cost['avg'];


                    $total_avg_hosting_cost = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['hosting_cost'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['hosting_cost']['dates'] = $reportsColumnData['hosting_cost'];
                    $tmpOperators['hosting_cost']['total'] = $total_avg_hosting_cost['sum'];
                    $tmpOperators['hosting_cost']['t_mo_end'] = $total_avg_hosting_cost['T_Mo_End'];
                    $tmpOperators['hosting_cost']['avg'] = $total_avg_hosting_cost['avg'];


                    $total_avg_content = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['content'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['content']['dates'] = $reportsColumnData['content'];
                    $tmpOperators['content']['total'] = $total_avg_content['sum'];
                    $tmpOperators['content']['t_mo_end'] = $total_avg_content['T_Mo_End'];
                    $tmpOperators['content']['avg'] = $total_avg_content['avg'];


                    $total_avg_rnd = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['rnd'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['rnd']['dates'] = $reportsColumnData['rnd'];
                    $tmpOperators['rnd']['total'] = $total_avg_rnd['sum'];
                    $tmpOperators['rnd']['t_mo_end'] = $total_avg_rnd['T_Mo_End'];
                    $tmpOperators['rnd']['avg'] = $total_avg_rnd['avg'];


                    $total_avg_bd = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['bd'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['bd']['dates'] = $reportsColumnData['bd'];
                    $tmpOperators['bd']['total'] = $total_avg_bd['sum'];
                    $tmpOperators['bd']['t_mo_end'] = $total_avg_bd['T_Mo_End'];
                    $tmpOperators['bd']['avg'] = $total_avg_bd['avg'];


                    $total_avg_market_cost = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['market_cost'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['market_cost']['dates'] = $reportsColumnData['market_cost'];
                    $tmpOperators['market_cost']['total'] = $total_avg_market_cost['sum'];
                    $tmpOperators['market_cost']['t_mo_end'] = $total_avg_market_cost['T_Mo_End'];
                    $tmpOperators['market_cost']['avg'] = $total_avg_market_cost['avg'];


                    $total_avg_misc_cost = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['misc_cost'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['misc_cost']['dates'] = $reportsColumnData['misc_cost'];
                    $tmpOperators['misc_cost']['total'] = $total_avg_misc_cost['sum'];
                    $tmpOperators['misc_cost']['t_mo_end'] = $total_avg_misc_cost['T_Mo_End'];
                    $tmpOperators['misc_cost']['avg'] = $total_avg_misc_cost['avg'];


                    $total_avg_platform = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['platform'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['platform']['dates'] = $reportsColumnData['platform'];
                    $tmpOperators['platform']['total'] = $total_avg_platform['sum'];
                    $tmpOperators['platform']['t_mo_end'] = $total_avg_platform['T_Mo_End'];
                    $tmpOperators['platform']['avg'] = $total_avg_platform['avg'];


                    $total_avg_pnl = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['pnl'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['pnl']['dates'] = $reportsColumnData['pnl'];
                    $tmpOperators['pnl']['total'] = $total_avg_pnl['sum'];
                    $tmpOperators['pnl']['t_mo_end'] = $total_avg_pnl['T_Mo_End'];
                    $tmpOperators['pnl']['avg'] = $total_avg_pnl['avg'];


                    $total_avg_net_after_tax = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['net_after_tax'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['net_after_tax']['dates'] = $reportsColumnData['net_after_tax'];
                    $tmpOperators['net_after_tax']['total'] = $total_avg_net_after_tax['sum'];
                    $tmpOperators['net_after_tax']['t_mo_end'] = $total_avg_net_after_tax['T_Mo_End'];
                    $tmpOperators['net_after_tax']['avg'] = $total_avg_net_after_tax['avg'];


                    $total_avg_net_revenue_after_tax = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['net_revenue_after_tax'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['net_revenue_after_tax']['dates'] = $reportsColumnData['net_revenue_after_tax'];
                    $tmpOperators['net_revenue_after_tax']['total'] = $total_avg_net_revenue_after_tax['sum'];
                    $tmpOperators['net_revenue_after_tax']['t_mo_end'] = $total_avg_net_revenue_after_tax['T_Mo_End'];
                    $tmpOperators['net_revenue_after_tax']['avg'] = $total_avg_net_revenue_after_tax['avg'];


                    $total_avg_br = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['br'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['br']['dates'] = $reportsColumnData['br'];
                    $tmpOperators['br']['total'] = $total_avg_br['sum'];
                    $tmpOperators['br']['t_mo_end'] = $total_avg_br['T_Mo_End'];
                    $tmpOperators['br']['avg'] = $total_avg_br['avg'];


                    $total_avg_fp = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['fp'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['fp']['dates'] = $reportsColumnData['fp'];
                    $tmpOperators['fp']['total'] = $total_avg_fp['sum'];
                    $tmpOperators['fp']['t_mo_end'] = $total_avg_fp['T_Mo_End'];
                    $tmpOperators['fp']['avg'] = $total_avg_fp['avg'];


                    $total_avg_fp_success = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['fp_success'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['fp_success']['dates'] = $reportsColumnData['fp_success'];
                    $tmpOperators['fp_success']['total'] = $total_avg_fp_success['sum'];
                    $tmpOperators['fp_success']['t_mo_end'] = $total_avg_fp_success['T_Mo_End'];
                    $tmpOperators['fp_success']['avg'] = $total_avg_fp_success['avg'];


                    $total_avg_fp_failed = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['fp_failed'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['fp_failed']['dates'] = $reportsColumnData['fp_failed'];
                    $tmpOperators['fp_failed']['total'] = $total_avg_fp_failed['sum'];
                    $tmpOperators['fp_failed']['t_mo_end'] = $total_avg_fp_failed['T_Mo_End'];
                    $tmpOperators['fp_failed']['avg'] = $total_avg_fp_failed['avg'];


                    $total_avg_dp = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['dp'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['dp']['dates'] = $reportsColumnData['dp'];
                    $tmpOperators['dp']['total'] = $total_avg_dp['sum'];
                    $tmpOperators['dp']['t_mo_end'] = $total_avg_dp['T_Mo_End'];
                    $tmpOperators['dp']['avg'] = $total_avg_dp['avg'];


                    $total_avg_dp_success = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['dp_success'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['dp_success']['dates'] = $reportsColumnData['dp_success'];
                    $tmpOperators['dp_success']['total'] = $total_avg_dp_success['sum'];
                    $tmpOperators['dp_success']['t_mo_end'] = $total_avg_dp_success['T_Mo_End'];
                    $tmpOperators['dp_success']['avg'] = $total_avg_dp_success['avg'];


                    $total_avg_dp_failed = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['dp_failed'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['dp_failed']['dates'] = $reportsColumnData['dp_failed'];
                    $tmpOperators['dp_failed']['total'] = $total_avg_dp_failed['sum'];
                    $tmpOperators['dp_failed']['t_mo_end'] = $total_avg_dp_failed['T_Mo_End'];
                    $tmpOperators['dp_failed']['avg'] = $total_avg_dp_failed['avg'];


                    $total_avg_renewal = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['renewal'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['renewal']['dates'] = $reportsColumnData['renewal'];
                    $tmpOperators['renewal']['total'] = $total_avg_renewal['sum'];
                    $tmpOperators['renewal']['t_mo_end'] = $total_avg_renewal['T_Mo_End'];
                    $tmpOperators['renewal']['avg'] = $total_avg_renewal['avg'];


                    $total_avg_vat = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['vat'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['vat']['dates'] = $reportsColumnData['vat'];
                    $tmpOperators['vat']['total'] = $total_avg_vat['sum'];
                    $tmpOperators['vat']['t_mo_end'] = $total_avg_vat['T_Mo_End'];
                    $tmpOperators['vat']['avg'] = $total_avg_vat['avg'];


                    $total_avg_spec_tax = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['spec_tax'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['spec_tax']['dates'] = $reportsColumnData['spec_tax'];
                    $tmpOperators['spec_tax']['total'] = $total_avg_spec_tax['sum'];
                    $tmpOperators['spec_tax']['t_mo_end'] = $total_avg_spec_tax['T_Mo_End'];
                    $tmpOperators['spec_tax']['avg'] = $total_avg_spec_tax['avg'];


                    $total_avg_government_cost = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['government_cost'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['government_cost']['dates'] = $reportsColumnData['government_cost'];
                    $tmpOperators['government_cost']['total'] = $total_avg_government_cost['sum'];
                    $tmpOperators['government_cost']['t_mo_end'] = $total_avg_government_cost['T_Mo_End'];
                    $tmpOperators['government_cost']['avg'] = $total_avg_government_cost['avg'];


                    $total_avg_dealer_commision = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['dealer_commision'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['dealer_commision']['dates'] = $reportsColumnData['dealer_commision'];
                    $tmpOperators['dealer_commision']['total'] = $total_avg_dealer_commision['sum'];
                    $tmpOperators['dealer_commision']['t_mo_end'] = $total_avg_dealer_commision['T_Mo_End'];
                    $tmpOperators['dealer_commision']['avg'] = $total_avg_dealer_commision['avg'];


                    $total_avg_wht = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['wht'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['wht']['dates'] = $reportsColumnData['wht'];
                    $tmpOperators['wht']['total'] = $total_avg_wht['sum'];
                    $tmpOperators['wht']['t_mo_end'] = $total_avg_wht['T_Mo_End'];
                    $tmpOperators['wht']['avg'] = $total_avg_wht['avg'];


                    $total_avg_misc_tax = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['misc_tax'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['misc_tax']['dates'] = $reportsColumnData['misc_tax'];
                    $tmpOperators['misc_tax']['total'] = $total_avg_misc_tax['sum'];
                    $tmpOperators['misc_tax']['t_mo_end'] = $total_avg_misc_tax['T_Mo_End'];
                    $tmpOperators['misc_tax']['avg'] = $total_avg_misc_tax['avg'];


                    $total_avg_other_tax = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['other_tax'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['other_tax']['dates'] = $reportsColumnData['other_tax'];
                    $tmpOperators['other_tax']['total'] = $total_avg_other_tax['sum'];
                    $tmpOperators['other_tax']['t_mo_end'] = $total_avg_other_tax['T_Mo_End'];
                    $tmpOperators['other_tax']['avg'] = $total_avg_other_tax['avg'];


                    $total_avg_uso = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['uso'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['uso']['dates'] = $reportsColumnData['uso'];
                    $tmpOperators['uso']['total'] = $total_avg_uso['sum'];
                    $tmpOperators['uso']['t_mo_end'] = $total_avg_uso['T_Mo_End'];
                    $tmpOperators['uso']['avg'] = $total_avg_uso['avg'];


                    $total_avg_agre_paxxa = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['agre_paxxa'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['agre_paxxa']['dates'] = $reportsColumnData['agre_paxxa'];
                    $tmpOperators['agre_paxxa']['total'] = $total_avg_agre_paxxa['sum'];
                    $tmpOperators['agre_paxxa']['t_mo_end'] = $total_avg_agre_paxxa['T_Mo_End'];
                    $tmpOperators['agre_paxxa']['avg'] = $total_avg_agre_paxxa['avg'];


                    $total_avg_sbaf = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['sbaf'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['sbaf']['dates'] = $reportsColumnData['sbaf'];
                    $tmpOperators['sbaf']['total'] = $total_avg_sbaf['sum'];
                    $tmpOperators['sbaf']['t_mo_end'] = $total_avg_sbaf['T_Mo_End'];
                    $tmpOperators['sbaf']['avg'] = $total_avg_sbaf['avg'];


                    $total_avg_clicks = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['clicks'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['clicks']['dates'] = $reportsColumnData['clicks'];
                    $tmpOperators['clicks']['total'] = $total_avg_clicks['sum'];
                    $tmpOperators['clicks']['t_mo_end'] = $total_avg_clicks['T_Mo_End'];
                    $tmpOperators['clicks']['avg'] = $total_avg_clicks['avg'];


                    $total_avg_ratio_for_cpa = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['ratio_for_cpa'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['ratio_for_cpa']['dates'] = $reportsColumnData['ratio_for_cpa'];
                    $tmpOperators['ratio_for_cpa']['total'] = $total_avg_ratio_for_cpa['sum'];
                    $tmpOperators['ratio_for_cpa']['t_mo_end'] = $total_avg_ratio_for_cpa['T_Mo_End'];
                    $tmpOperators['ratio_for_cpa']['avg'] = $total_avg_ratio_for_cpa['avg'];


                    $total_avg_cpa_price = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['cpa_price'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['cpa_price']['dates'] = $reportsColumnData['cpa_price'];
                    $tmpOperators['cpa_price']['total'] = $total_avg_cpa_price['sum'];
                    $tmpOperators['cpa_price']['t_mo_end'] = $total_avg_cpa_price['T_Mo_End'];
                    $tmpOperators['cpa_price']['avg'] = $total_avg_cpa_price['avg'];


                    $total_avg_cr_mo_clicks = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['cr_mo_clicks'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['cr_mo_clicks']['dates'] = $reportsColumnData['cr_mo_clicks'];
                    $tmpOperators['cr_mo_clicks']['total'] = $total_avg_cr_mo_clicks['sum'];
                    $tmpOperators['cr_mo_clicks']['t_mo_end'] = $total_avg_cr_mo_clicks['T_Mo_End'];
                    $tmpOperators['cr_mo_clicks']['avg'] = $total_avg_cr_mo_clicks['avg'];


                    $total_avg_cr_mo_landing = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['cr_mo_landing'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['cr_mo_landing']['dates'] = $reportsColumnData['cr_mo_landing'];
                    $tmpOperators['cr_mo_landing']['total'] = $total_avg_cr_mo_landing['sum'];
                    $tmpOperators['cr_mo_landing']['t_mo_end'] = $total_avg_cr_mo_landing['T_Mo_End'];
                    $tmpOperators['cr_mo_landing']['avg'] = $total_avg_cr_mo_landing['avg'];


                    $total_avg_landing = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['landing'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['landing']['dates'] = $reportsColumnData['landing'];
                    $tmpOperators['landing']['total'] = $total_avg_landing['sum'];
                    $tmpOperators['landing']['t_mo_end'] = $total_avg_landing['T_Mo_End'];
                    $tmpOperators['landing']['avg'] = $total_avg_landing['avg'];


                    $total_avg_mo = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['mo'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['mo']['dates'] = $reportsColumnData['mo'];
                    $tmpOperators['mo']['total'] = $total_avg_mo['sum'];
                    $tmpOperators['mo']['t_mo_end'] = $total_avg_mo['T_Mo_End'];
                    $tmpOperators['mo']['avg'] = $total_avg_mo['avg'];


                    $total_avg_reg = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['reg'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['reg']['dates'] = $reportsColumnData['reg'];
                    $tmpOperators['reg']['total'] = $total_avg_reg['sum'];
                    $tmpOperators['reg']['t_mo_end'] = $total_avg_reg['T_Mo_End'];
                    $tmpOperators['reg']['avg'] = $total_avg_reg['avg'];


                    $total_avg_unreg = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['unreg'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['unreg']['dates'] = $reportsColumnData['unreg'];
                    $tmpOperators['unreg']['total'] = $total_avg_unreg['sum'];
                    $tmpOperators['unreg']['t_mo_end'] = $total_avg_unreg['T_Mo_End'];
                    $tmpOperators['unreg']['avg'] = $total_avg_unreg['avg'];


                    $total_avg_price_mo = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['price_mo'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['price_mo']['dates'] = $reportsColumnData['price_mo'];
                    $tmpOperators['price_mo']['total'] = $total_avg_price_mo['sum'];
                    $tmpOperators['price_mo']['t_mo_end'] = $total_avg_price_mo['T_Mo_End'];
                    $tmpOperators['price_mo']['avg'] = $total_avg_price_mo['avg'];

                    $total_avg_price_mo_cost = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['price_mo_cost'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['price_mo_cost']['dates'] = $reportsColumnData['price_mo_cost'];
                    $tmpOperators['price_mo_cost']['total'] = $total_avg_price_mo_cost['sum'];
                    $tmpOperators['price_mo_cost']['t_mo_end'] = $total_avg_price_mo_cost['T_Mo_End'];
                    $tmpOperators['price_mo_cost']['avg'] = $total_avg_price_mo_cost['avg'];

                    $total_avg_price_mo_mo = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['price_mo_mo'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['price_mo_mo']['dates'] = $reportsColumnData['price_mo_mo'];
                    $tmpOperators['price_mo_mo']['total'] = $total_avg_price_mo_mo['sum'];
                    $tmpOperators['price_mo_mo']['t_mo_end'] = $total_avg_price_mo_mo['T_Mo_End'];
                    $tmpOperators['price_mo_mo']['avg'] = $total_avg_price_mo_mo['avg'];


                    $total_avg_active_subs = UtilityReportsMonthly::calculateTotalSubscribe($operator,$reportsColumnData['active_subs'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['active_subs']['dates'] = $reportsColumnData['active_subs'];
                    $tmpOperators['active_subs']['total'] = $total_avg_active_subs['sum'];
                    $tmpOperators['active_subs']['t_mo_end'] = $total_avg_active_subs['T_Mo_End'];
                    $tmpOperators['active_subs']['avg'] = $total_avg_active_subs['avg'];


                    $total_avg_arpu_7 = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['arpu_7'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['arpu_7']['dates'] = $reportsColumnData['arpu_7'];
                    $tmpOperators['arpu_7']['total'] = $total_avg_arpu_7['sum'];
                    $tmpOperators['arpu_7']['t_mo_end'] = $total_avg_arpu_7['T_Mo_End'];
                    $tmpOperators['arpu_7']['avg'] = $total_avg_arpu_7['avg'];


                    $total_avg_arpu_7_usd = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['arpu_7_usd'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['arpu_7_usd']['dates'] = $reportsColumnData['arpu_7_usd'];
                    $tmpOperators['arpu_7_usd']['total'] = $total_avg_arpu_7_usd['sum'];
                    $tmpOperators['arpu_7_usd']['t_mo_end'] = $total_avg_arpu_7_usd['T_Mo_End'];
                    $tmpOperators['arpu_7_usd']['avg'] = $total_avg_arpu_7_usd['avg'];


                    $total_avg_arpu_30 = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['arpu_30'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['arpu_30']['dates'] = $reportsColumnData['arpu_30'];
                    $tmpOperators['arpu_30']['total'] = $total_avg_arpu_30['sum'];
                    $tmpOperators['arpu_30']['t_mo_end'] = $total_avg_arpu_30['T_Mo_End'];
                    $tmpOperators['arpu_30']['avg'] = $total_avg_arpu_30['avg'];


                    $total_avg_arpu_30_usd = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['arpu_30_usd'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['arpu_30_usd']['dates'] = $reportsColumnData['arpu_30_usd'];
                    $tmpOperators['arpu_30_usd']['total'] = $total_avg_arpu_30_usd['sum'];
                    $tmpOperators['arpu_30_usd']['t_mo_end'] = $total_avg_arpu_30_usd['T_Mo_End'];
                    $tmpOperators['arpu_30_usd']['avg'] = $total_avg_arpu_30_usd['avg'];


                    $total_avg_reg_sub = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['reg_sub'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['reg_sub']['dates'] = $reportsColumnData['reg_sub'];
                    $tmpOperators['reg_sub']['total'] = $total_avg_reg_sub['sum'];
                    $tmpOperators['reg_sub']['t_mo_end'] = $total_avg_reg_sub['T_Mo_End'];
                    $tmpOperators['reg_sub']['avg'] = $total_avg_reg_sub['avg'];


                    $total_avg_roi = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['roi'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['roi']['dates'] = $reportsColumnData['roi'];
                    $tmpOperators['roi']['total'] = $total_avg_roi['sum'];
                    $tmpOperators['roi']['t_mo_end'] = $total_avg_roi['T_Mo_End'];
                    $tmpOperators['roi']['avg'] = $total_avg_roi['avg'];


                    $total_avg_bill = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['bill'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['bill']['dates']=$reportsColumnData['bill'];
                    $tmpOperators['bill']['total']=0;
                    $tmpOperators['bill']['t_mo_end']=0;
                    $tmpOperators['bill']['avg']=$total_avg_bill['avg'];


                    $total_avg_firstpush = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['firstpush'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['firstpush']['dates']=$reportsColumnData['firstpush'];
                    $tmpOperators['firstpush']['total']=0;
                    $tmpOperators['firstpush']['t_mo_end']=0;
                    $tmpOperators['firstpush']['avg']=$total_avg_firstpush['avg'];


                    $total_avg_dailypush = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['dailypush'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['dailypush']['dates']=$reportsColumnData['dailypush'];
                    $tmpOperators['dailypush']['total']=0;
                    $tmpOperators['dailypush']['t_mo_end']=0;
                    $tmpOperators['dailypush']['avg']=$total_avg_dailypush['avg'];


                    $total_avg_last_7_gros_rev = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['last_7_gros_rev'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['last_7_gros_rev']['dates'] = $reportsColumnData['last_7_gros_rev'];
                    $tmpOperators['last_7_gros_rev']['total'] = $total_avg_last_7_gros_rev['sum'];
                    $tmpOperators['last_7_gros_rev']['t_mo_end'] = $total_avg_last_7_gros_rev['T_Mo_End'];
                    $tmpOperators['last_7_gros_rev']['avg'] = $total_avg_last_7_gros_rev['avg'];


                    $total_avg_last_7_reg = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['last_7_reg'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['last_7_reg']['dates'] = $reportsColumnData['last_7_reg'];
                    $tmpOperators['last_7_reg']['total'] = $total_avg_last_7_reg['sum'];
                    $tmpOperators['last_7_reg']['t_mo_end'] = $total_avg_last_7_reg['T_Mo_End'];
                    $tmpOperators['last_7_reg']['avg'] = $total_avg_last_7_reg['avg'];


                    $total_avg_last_30_gros_rev = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['last_30_gros_rev'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['last_30_gros_rev']['dates'] = $reportsColumnData['last_30_gros_rev'];
                    $tmpOperators['last_30_gros_rev']['total'] = $total_avg_last_30_gros_rev['sum'];
                    $tmpOperators['last_30_gros_rev']['t_mo_end'] = $total_avg_last_30_gros_rev['T_Mo_End'];
                    $tmpOperators['last_30_gros_rev']['avg'] = $total_avg_last_30_gros_rev['avg'];


                    $total_avg_last_30_reg = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['last_30_reg'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['last_30_reg']['dates'] = $reportsColumnData['last_30_reg'];
                    $tmpOperators['last_30_reg']['total'] = $total_avg_last_30_reg['sum'];
                    $tmpOperators['last_30_reg']['t_mo_end'] = $total_avg_last_30_reg['T_Mo_End'];
                    $tmpOperators['last_30_reg']['avg'] = $total_avg_last_30_reg['avg'];


                    // dd($tmpOperators);
                    $sumemry[] = $tmpOperators;
                }
            }

            // dd($sumemry);

            // Country Sum from Operator array
            $displayCountries = array();
            $SelectedCountries = array();
            $RowCountryData = array();

            if(!empty($sumemry))
            {
                foreach ($sumemry as $key => $sumemries) {
                    // dd($sumemries);
                    $country_id = $sumemries['country']['id'];
                    $SelectedCountries[$country_id] = $sumemries['country'];
                    $displayCountries[$country_id][] = $sumemries;
                }
            }

            // dd($displayCountries);

            if(!empty($SelectedCountries))
            {
                foreach ($SelectedCountries as $key => $SelectedCountry)
                {
                    $tempDataArr = array();
                    $country_id = $SelectedCountry['id'];
                    $dataRowSum = UtilityReports::pnlDetailsDataSum($displayCountries[$country_id]);
                    // dd($dataRowSum);
                    $country_avg_arpu_7_usd = UtilityReports::calculateTotalAVG($operator,$dataRowSum['arpu_7_usd']['dates'],$startColumnDateDisplay,$end_date);
                    $dataRowSum['arpu_7_usd']['avg'] = $country_avg_arpu_7_usd['avg'];

                    $country_avg_arpu_30_usd = UtilityReports::calculateTotalAVG($operator,$dataRowSum['arpu_30_usd']['dates'],$startColumnDateDisplay,$end_date);
                    $dataRowSum['arpu_30_usd']['avg'] = $country_avg_arpu_30_usd['avg'];

                    $country_avg_bill = UtilityReports::calculateTotalAVG($operator,$dataRowSum['bill']['dates'],$startColumnDateDisplay,$end_date);

                    $country_avg_firstpush = UtilityReports::calculateTotalAVG($operator,$dataRowSum['firstpush']['dates'],$startColumnDateDisplay,$end_date);
                    $dataRowSum['firstpush']['avg']=$country_avg_firstpush['avg'];

                    $country_avg_dailypush = UtilityReports::calculateTotalAVG($operator,$dataRowSum['dailypush']['dates'],$startColumnDateDisplay,$end_date);

                    if(!empty($dataRowSum)){
                        foreach ($dataRowSum as $key1 => $value1) {
                            if($key1 == 'roi'){
                                foreach ($value1 as $key2 => $value2) {
                                    if($key2 == 'dates'){
                                        foreach ($value2 as $key3 => $value3) {
                                            $roi_arpu = ($dataRowSum['arpu_30_usd']['dates'][$key3]['value'] == 0) ? (float)0 : $dataRowSum['arpu_30_usd']['dates'][$key3]['value'];
                                            $price_mo = ($dataRowSum['price_mo']['dates'][$key3]['value'] == 0) ? (float)0 : $dataRowSum['price_mo']['dates'][$key3]['value'];
                                            $dataRowSum['roi']['dates'][$key3]['value'] = ($roi_arpu == 0) ? (float)0 : ($price_mo / $roi_arpu);
                                        }
                                    }
                                }
                            }
                        }
                    }

                    $dataRowSum['dailypush']['avg']=$country_avg_dailypush['avg'];
                    $dataRowSum['bill']['avg']=$country_avg_bill['avg'];
                    $tempDataArr['country'] = $SelectedCountry;
                    $tempDataArr['month_string'] = $month;
                    $tempDataArr['year'] = $year;
                    $tempDataArr = array_merge($tempDataArr,$dataRowSum);
                    $RowCountryData[] = $tempDataArr;
                }
            }

            $sumemry = $RowCountryData;
            $sumOfSummaryData = UtilityReports::pnlDetailsDataSum($sumemry);
            $no_of_days = $no_of_months;

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

            if(!empty($sumOfSummaryData)){
                foreach ($sumOfSummaryData as $key1 => $value1) {
                    if($key1 == 'roi'){
                        foreach ($value1 as $key2 => $value2) {
                            if($key2 == 'dates'){
                                foreach ($value2 as $key3 => $value3) {
                                    $roi_arpu = ($sumOfSummaryData['arpu_30_usd']['dates'][$key3]['value'] == 0) ? (float)0 : $sumOfSummaryData['arpu_30_usd']['dates'][$key3]['value'];
                                    $price_mo = ($sumOfSummaryData['price_mo']['dates'][$key3]['value'] == 0) ? (float)0 : $sumOfSummaryData['price_mo']['dates'][$key3]['value'];
                                    $sumOfSummaryData['roi']['dates'][$key3]['value'] = ($roi_arpu == 0) ? (float)0 : ($price_mo / $roi_arpu);
                                }
                            }
                        }
                    }
                }
            }

            $date = Carbon::parse($end_date)->format('Y-m');

            return view('report.monthly_pnlsummary_country',compact('date','sumemry','no_of_days','sumOfSummaryData','CountryWise','monthly','data'));
        }else{
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    // get company monthly pnl report
    public function MonthlyPnlReportCompanyDetails(Request $request)
    {
        if(\Auth::user()->can('PNL Detail'))
        {
            $data['CompanyWise'] = $CompanyWise = 1;
            $data['Monthly'] = $monthly = 1;

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

            $firstDayoftheyear = Carbon::now()->startOfYear()->format('Y-m-d');
            $start_date = $firstDayoftheyear;
            $startColumnDateDisplay = $firstDayoftheyear;
            $end_date = Carbon::now()->format('Y-m-d');
            $month = Carbon::now()->format('F Y');
            // $companys = Company::get();

            /* filter Search Section */
            $showAllOperator = true;
            if($request->filled('to') && $request->filled('from'))
            {
                $display_date_input = new Carbon($req_Start_date);
                $start_date = $req_Start_date;
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
                    $Operators = Operator::with('revenueshare','RevenushareByDate')->Status(1)->GetOperatorByOperatorId($Operators_company)->get();
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
            //     $Operators = Operator::with('revenueshare')->Status(1)->GetOperatorByCountryId($req_CountryId)->get();
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
                $Operators = Operator::with('revenueshare','RevenushareByDate')->Status(1)->GetOperatorByOperatorId($filterOperator)->get();
                $showAllOperator = false;
            }

            if($showAllOperator)
            {
                $Operators = Operator::with('revenueshare','RevenushareByDate')->Status(1)->get();
            }

            if(!isset($Operators))
            {
                $request->session()->flash('alert-success', 'User was successful added!');
                return redirect()->back();
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

            $datesIndividual = Utility::getRangeDates($startColumnDateDisplay,$end_date);
            $no_of_months = Utility::getRangeMonthsNo($datesIndividual);

            $monthList = array();

            foreach ($no_of_months as $key => $no_of_month) {
                $month_key = $no_of_month['date'];
                $monthList[] = $month_key;
            }

            $QueryMonthlyReports = PnlSummeryMonth::filteroperator($operator_ids)
                                ->Months($monthList);
            $user = Auth::user();
            $user_id = $user->id;
            $user_type = $user->type;
            $allowAllOperator = $user->WhowAccessAlOperator($user_type);

            if($allowAllOperator)
            {
                $QueryMonthlyReports = $QueryMonthlyReports->User(0);
            }else{
                $QueryMonthlyReports = $QueryMonthlyReports->User(0);
                $QueryMonthlyUserReports = MonthlyReportSummery::filteroperator($operator_ids)->Months($monthList);
                $QueryMonthlyUserReports = $QueryMonthlyUserReports->User($user_id);
                $allMonthlyUserData = $QueryMonthlyUserReports
                            ->get()->toArray();

                $reportsMonthUserData = $this->rearrangeOperatorMonthUser($allMonthlyUserData);
            }

            $allMonthlyData = $QueryMonthlyReports->get()->toArray();

            $reportsMonthData = $this->rearrangeOperatorMonth($allMonthlyData);
            $monthdata = $reportsMonthData;

            $start_date_roi = Carbon::yesterday()->subDays(30)->format('Y-m-d');
            $end_date_roi = Carbon::yesterday()->format('Y-m-d');
            $date_roi = Carbon::now()->format('Y-m-d');
            $reports = ReportsPnlsOperatorSummarizes::filterOperator($operator_ids)
                ->OperatorNotNull()
                ->filterDateRange($start_date_roi,$end_date_roi)
                ->SumOfRoiDataOperator()
                ->get()->toArray();

            $active_subs = ReportsPnlsOperatorSummarizes::filteroperator($operator_ids)
                ->where(['date' => $date_roi])
                ->TotalOperator()
                ->get()->toArray();

            $reportsByOperatorIDs = $this->getReportsByOperatorID($reports);
            $active_subsByOperatorIDs = $this->getReportsByOperatorID($active_subs);
            // dd($monthdata);

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

                    if(isset($operator->revenueshare)){
                        $merchant_revenue_share = $operator->revenueshare->merchant_revenue_share;
                    }else{
                        $merchant_revenue_share = 100;
                    }

                    // $tmpOperators['company'] = $com_operators[$operator_id];
                    $country_id  = $operator->country_id;
                    $contain_id = Arr::exists($countries, $country_id);

                    if($contain_id)
                    {
                        $tmpOperators['country']=$countries[$country_id];
                        $OperatorCountry = $countries[$country_id];
                    }

                    if(isset($reportsMonthUserData)  && !empty($reportsMonthUserData)){
                        foreach ($reportsMonthUserData as $key1 => $value1) {
                            if($key1 == $operator_id){
                                foreach ($value1 as $key2 => $value2) {
                                    $monthdata[$operator_id][$key2]['rev'] = $value2['gros_rev'];
                                    $monthdata[$operator_id][$key2]['rev_usd'] = $value2['gros_rev']*$OperatorCountry['usd'];
                                    $monthdata[$operator_id][$key2]['lshare'] = $value2['gros_rev']*($merchant_revenue_share/100);
                                    $monthdata[$operator_id][$key2]['share'] = $value2['gros_rev']*$OperatorCountry['usd']*($merchant_revenue_share/100);
                                }
                            }
                        }
                    }

                    $reportsColumnData = $this->getPNLReportsDateWise($operator,$no_of_months,$monthdata,$OperatorCountry,$reportsByOperatorIDs,$active_subsByOperatorIDs);
                    // dd($reportsColumnData);
                    $tmpOperators['month_string'] = $month;

                    $tmpOperators['last_update'] = $reportsColumnData['last_update'];
                    $tmpOperators['hostingCost'] = isset($operator->hostingCost) ? $operator->hostingCost : 0;
                    $tmpOperators['contentCost'] = isset($operator->content) ? $operator->content : 0;
                    $tmpOperators['rndCost'] = isset($operator->rnd) ? $operator->rnd : 0;
                    $tmpOperators['bdCost'] = 2.5;
                    $tmpOperators['marketCost'] = 1.5;
                    $tmpOperators['vatTax'] = isset($operator->vat) ? $operator->vat : 0;
                    $tmpOperators['whtTax'] = isset($operator->wht) ? $operator->wht : 0;
                    $tmpOperators['miscTax'] = isset($operator->miscTax) ? $operator->miscTax : 0;

                    $total_avg_rev_usd = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['end_user_rev_usd'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['end_user_rev_usd']['dates'] = $reportsColumnData['end_user_rev_usd'];
                    $tmpOperators['end_user_rev_usd']['total'] = $total_avg_rev_usd['sum'];
                    $tmpOperators['end_user_rev_usd']['t_mo_end'] = $total_avg_rev_usd['T_Mo_End'];
                    $tmpOperators['end_user_rev_usd']['avg'] = $total_avg_rev_usd['avg'];


                    $total_avg_rev = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['end_user_rev'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['end_user_rev']['dates'] = $reportsColumnData['end_user_rev'];
                    $tmpOperators['end_user_rev']['total'] = $total_avg_rev['sum'];
                    $tmpOperators['end_user_rev']['t_mo_end'] = $total_avg_rev['T_Mo_End'];
                    $tmpOperators['end_user_rev']['avg'] = $total_avg_rev['avg'];


                    $total_avg_gros_rev_usd = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['gros_rev_usd'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['gros_rev_usd']['dates'] = $reportsColumnData['gros_rev_usd'];
                    $tmpOperators['gros_rev_usd']['total'] = $total_avg_gros_rev_usd['sum'];
                    $tmpOperators['gros_rev_usd']['t_mo_end'] = $total_avg_gros_rev_usd['T_Mo_End'];
                    $tmpOperators['gros_rev_usd']['avg'] = $total_avg_gros_rev_usd['avg'];


                    $total_avg_gros_rev = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['gros_rev'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['gros_rev']['dates'] = $reportsColumnData['gros_rev'];
                    $tmpOperators['gros_rev']['total'] = $total_avg_gros_rev['sum'];
                    $tmpOperators['gros_rev']['t_mo_end'] = $total_avg_gros_rev['T_Mo_End'];
                    $tmpOperators['gros_rev']['avg'] = $total_avg_gros_rev['avg'];


                    $total_avg_cost_campaign = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['cost_campaign'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['cost_campaign']['dates'] = $reportsColumnData['cost_campaign'];
                    $tmpOperators['cost_campaign']['total'] = $total_avg_cost_campaign['sum'];
                    $tmpOperators['cost_campaign']['t_mo_end'] = $total_avg_cost_campaign['T_Mo_End'];
                    $tmpOperators['cost_campaign']['avg'] = $total_avg_cost_campaign['avg'];


                    $total_avg_other_cost = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['other_cost'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['other_cost']['dates'] = $reportsColumnData['other_cost'];
                    $tmpOperators['other_cost']['total'] = $total_avg_other_cost['sum'];
                    $tmpOperators['other_cost']['t_mo_end'] = $total_avg_other_cost['T_Mo_End'];
                    $tmpOperators['other_cost']['avg'] = $total_avg_other_cost['avg'];


                    $total_avg_hosting_cost = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['hosting_cost'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['hosting_cost']['dates'] = $reportsColumnData['hosting_cost'];
                    $tmpOperators['hosting_cost']['total'] = $total_avg_hosting_cost['sum'];
                    $tmpOperators['hosting_cost']['t_mo_end'] = $total_avg_hosting_cost['T_Mo_End'];
                    $tmpOperators['hosting_cost']['avg'] = $total_avg_hosting_cost['avg'];


                    $total_avg_content = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['content'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['content']['dates'] = $reportsColumnData['content'];
                    $tmpOperators['content']['total'] = $total_avg_content['sum'];
                    $tmpOperators['content']['t_mo_end'] = $total_avg_content['T_Mo_End'];
                    $tmpOperators['content']['avg'] = $total_avg_content['avg'];


                    $total_avg_rnd = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['rnd'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['rnd']['dates'] = $reportsColumnData['rnd'];
                    $tmpOperators['rnd']['total'] = $total_avg_rnd['sum'];
                    $tmpOperators['rnd']['t_mo_end'] = $total_avg_rnd['T_Mo_End'];
                    $tmpOperators['rnd']['avg'] = $total_avg_rnd['avg'];


                    $total_avg_bd = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['bd'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['bd']['dates'] = $reportsColumnData['bd'];
                    $tmpOperators['bd']['total'] = $total_avg_bd['sum'];
                    $tmpOperators['bd']['t_mo_end'] = $total_avg_bd['T_Mo_End'];
                    $tmpOperators['bd']['avg'] = $total_avg_bd['avg'];


                    $total_avg_market_cost = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['market_cost'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['market_cost']['dates'] = $reportsColumnData['market_cost'];
                    $tmpOperators['market_cost']['total'] = $total_avg_market_cost['sum'];
                    $tmpOperators['market_cost']['t_mo_end'] = $total_avg_market_cost['T_Mo_End'];
                    $tmpOperators['market_cost']['avg'] = $total_avg_market_cost['avg'];


                    $total_avg_misc_cost = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['misc_cost'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['misc_cost']['dates'] = $reportsColumnData['misc_cost'];
                    $tmpOperators['misc_cost']['total'] = $total_avg_misc_cost['sum'];
                    $tmpOperators['misc_cost']['t_mo_end'] = $total_avg_misc_cost['T_Mo_End'];
                    $tmpOperators['misc_cost']['avg'] = $total_avg_misc_cost['avg'];


                    $total_avg_platform = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['platform'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['platform']['dates'] = $reportsColumnData['platform'];
                    $tmpOperators['platform']['total'] = $total_avg_platform['sum'];
                    $tmpOperators['platform']['t_mo_end'] = $total_avg_platform['T_Mo_End'];
                    $tmpOperators['platform']['avg'] = $total_avg_platform['avg'];


                    $total_avg_pnl = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['pnl'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['pnl']['dates'] = $reportsColumnData['pnl'];
                    $tmpOperators['pnl']['total'] = $total_avg_pnl['sum'];
                    $tmpOperators['pnl']['t_mo_end'] = $total_avg_pnl['T_Mo_End'];
                    $tmpOperators['pnl']['avg'] = $total_avg_pnl['avg'];


                    $total_avg_net_after_tax = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['net_after_tax'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['net_after_tax']['dates'] = $reportsColumnData['net_after_tax'];
                    $tmpOperators['net_after_tax']['total'] = $total_avg_net_after_tax['sum'];
                    $tmpOperators['net_after_tax']['t_mo_end'] = $total_avg_net_after_tax['T_Mo_End'];
                    $tmpOperators['net_after_tax']['avg'] = $total_avg_net_after_tax['avg'];


                    $total_avg_net_revenue_after_tax = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['net_revenue_after_tax'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['net_revenue_after_tax']['dates'] = $reportsColumnData['net_revenue_after_tax'];
                    $tmpOperators['net_revenue_after_tax']['total'] = $total_avg_net_revenue_after_tax['sum'];
                    $tmpOperators['net_revenue_after_tax']['t_mo_end'] = $total_avg_net_revenue_after_tax['T_Mo_End'];
                    $tmpOperators['net_revenue_after_tax']['avg'] = $total_avg_net_revenue_after_tax['avg'];


                    $total_avg_br = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['br'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['br']['dates'] = $reportsColumnData['br'];
                    $tmpOperators['br']['total'] = $total_avg_br['sum'];
                    $tmpOperators['br']['t_mo_end'] = $total_avg_br['T_Mo_End'];
                    $tmpOperators['br']['avg'] = $total_avg_br['avg'];


                    $total_avg_fp = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['fp'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['fp']['dates'] = $reportsColumnData['fp'];
                    $tmpOperators['fp']['total'] = $total_avg_fp['sum'];
                    $tmpOperators['fp']['t_mo_end'] = $total_avg_fp['T_Mo_End'];
                    $tmpOperators['fp']['avg'] = $total_avg_fp['avg'];


                    $total_avg_fp_success = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['fp_success'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['fp_success']['dates'] = $reportsColumnData['fp_success'];
                    $tmpOperators['fp_success']['total'] = $total_avg_fp_success['sum'];
                    $tmpOperators['fp_success']['t_mo_end'] = $total_avg_fp_success['T_Mo_End'];
                    $tmpOperators['fp_success']['avg'] = $total_avg_fp_success['avg'];


                    $total_avg_fp_failed = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['fp_failed'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['fp_failed']['dates'] = $reportsColumnData['fp_failed'];
                    $tmpOperators['fp_failed']['total'] = $total_avg_fp_failed['sum'];
                    $tmpOperators['fp_failed']['t_mo_end'] = $total_avg_fp_failed['T_Mo_End'];
                    $tmpOperators['fp_failed']['avg'] = $total_avg_fp_failed['avg'];


                    $total_avg_dp = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['dp'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['dp']['dates'] = $reportsColumnData['dp'];
                    $tmpOperators['dp']['total'] = $total_avg_dp['sum'];
                    $tmpOperators['dp']['t_mo_end'] = $total_avg_dp['T_Mo_End'];
                    $tmpOperators['dp']['avg'] = $total_avg_dp['avg'];


                    $total_avg_dp_success = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['dp_success'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['dp_success']['dates'] = $reportsColumnData['dp_success'];
                    $tmpOperators['dp_success']['total'] = $total_avg_dp_success['sum'];
                    $tmpOperators['dp_success']['t_mo_end'] = $total_avg_dp_success['T_Mo_End'];
                    $tmpOperators['dp_success']['avg'] = $total_avg_dp_success['avg'];


                    $total_avg_dp_failed = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['dp_failed'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['dp_failed']['dates'] = $reportsColumnData['dp_failed'];
                    $tmpOperators['dp_failed']['total'] = $total_avg_dp_failed['sum'];
                    $tmpOperators['dp_failed']['t_mo_end'] = $total_avg_dp_failed['T_Mo_End'];
                    $tmpOperators['dp_failed']['avg'] = $total_avg_dp_failed['avg'];


                    $total_avg_renewal = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['renewal'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['renewal']['dates'] = $reportsColumnData['renewal'];
                    $tmpOperators['renewal']['total'] = $total_avg_renewal['sum'];
                    $tmpOperators['renewal']['t_mo_end'] = $total_avg_renewal['T_Mo_End'];
                    $tmpOperators['renewal']['avg'] = $total_avg_renewal['avg'];


                    $total_avg_vat = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['vat'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['vat']['dates'] = $reportsColumnData['vat'];
                    $tmpOperators['vat']['total'] = $total_avg_vat['sum'];
                    $tmpOperators['vat']['t_mo_end'] = $total_avg_vat['T_Mo_End'];
                    $tmpOperators['vat']['avg'] = $total_avg_vat['avg'];


                    $total_avg_spec_tax = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['spec_tax'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['spec_tax']['dates'] = $reportsColumnData['spec_tax'];
                    $tmpOperators['spec_tax']['total'] = $total_avg_spec_tax['sum'];
                    $tmpOperators['spec_tax']['t_mo_end'] = $total_avg_spec_tax['T_Mo_End'];
                    $tmpOperators['spec_tax']['avg'] = $total_avg_spec_tax['avg'];


                    $total_avg_government_cost = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['government_cost'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['government_cost']['dates'] = $reportsColumnData['government_cost'];
                    $tmpOperators['government_cost']['total'] = $total_avg_government_cost['sum'];
                    $tmpOperators['government_cost']['t_mo_end'] = $total_avg_government_cost['T_Mo_End'];
                    $tmpOperators['government_cost']['avg'] = $total_avg_government_cost['avg'];


                    $total_avg_dealer_commision = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['dealer_commision'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['dealer_commision']['dates'] = $reportsColumnData['dealer_commision'];
                    $tmpOperators['dealer_commision']['total'] = $total_avg_dealer_commision['sum'];
                    $tmpOperators['dealer_commision']['t_mo_end'] = $total_avg_dealer_commision['T_Mo_End'];
                    $tmpOperators['dealer_commision']['avg'] = $total_avg_dealer_commision['avg'];


                    $total_avg_wht = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['wht'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['wht']['dates'] = $reportsColumnData['wht'];
                    $tmpOperators['wht']['total'] = $total_avg_wht['sum'];
                    $tmpOperators['wht']['t_mo_end'] = $total_avg_wht['T_Mo_End'];
                    $tmpOperators['wht']['avg'] = $total_avg_wht['avg'];


                    $total_avg_misc_tax = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['misc_tax'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['misc_tax']['dates'] = $reportsColumnData['misc_tax'];
                    $tmpOperators['misc_tax']['total'] = $total_avg_misc_tax['sum'];
                    $tmpOperators['misc_tax']['t_mo_end'] = $total_avg_misc_tax['T_Mo_End'];
                    $tmpOperators['misc_tax']['avg'] = $total_avg_misc_tax['avg'];


                    $total_avg_other_tax = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['other_tax'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['other_tax']['dates'] = $reportsColumnData['other_tax'];
                    $tmpOperators['other_tax']['total'] = $total_avg_other_tax['sum'];
                    $tmpOperators['other_tax']['t_mo_end'] = $total_avg_other_tax['T_Mo_End'];
                    $tmpOperators['other_tax']['avg'] = $total_avg_other_tax['avg'];


                    $total_avg_uso = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['uso'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['uso']['dates'] = $reportsColumnData['uso'];
                    $tmpOperators['uso']['total'] = $total_avg_uso['sum'];
                    $tmpOperators['uso']['t_mo_end'] = $total_avg_uso['T_Mo_End'];
                    $tmpOperators['uso']['avg'] = $total_avg_uso['avg'];


                    $total_avg_agre_paxxa = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['agre_paxxa'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['agre_paxxa']['dates'] = $reportsColumnData['agre_paxxa'];
                    $tmpOperators['agre_paxxa']['total'] = $total_avg_agre_paxxa['sum'];
                    $tmpOperators['agre_paxxa']['t_mo_end'] = $total_avg_agre_paxxa['T_Mo_End'];
                    $tmpOperators['agre_paxxa']['avg'] = $total_avg_agre_paxxa['avg'];


                    $total_avg_sbaf = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['sbaf'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['sbaf']['dates'] = $reportsColumnData['sbaf'];
                    $tmpOperators['sbaf']['total'] = $total_avg_sbaf['sum'];
                    $tmpOperators['sbaf']['t_mo_end'] = $total_avg_sbaf['T_Mo_End'];
                    $tmpOperators['sbaf']['avg'] = $total_avg_sbaf['avg'];


                    $total_avg_clicks = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['clicks'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['clicks']['dates'] = $reportsColumnData['clicks'];
                    $tmpOperators['clicks']['total'] = $total_avg_clicks['sum'];
                    $tmpOperators['clicks']['t_mo_end'] = $total_avg_clicks['T_Mo_End'];
                    $tmpOperators['clicks']['avg'] = $total_avg_clicks['avg'];


                    $total_avg_ratio_for_cpa = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['ratio_for_cpa'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['ratio_for_cpa']['dates'] = $reportsColumnData['ratio_for_cpa'];
                    $tmpOperators['ratio_for_cpa']['total'] = $total_avg_ratio_for_cpa['sum'];
                    $tmpOperators['ratio_for_cpa']['t_mo_end'] = $total_avg_ratio_for_cpa['T_Mo_End'];
                    $tmpOperators['ratio_for_cpa']['avg'] = $total_avg_ratio_for_cpa['avg'];


                    $total_avg_cpa_price = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['cpa_price'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['cpa_price']['dates'] = $reportsColumnData['cpa_price'];
                    $tmpOperators['cpa_price']['total'] = $total_avg_cpa_price['sum'];
                    $tmpOperators['cpa_price']['t_mo_end'] = $total_avg_cpa_price['T_Mo_End'];
                    $tmpOperators['cpa_price']['avg'] = $total_avg_cpa_price['avg'];


                    $total_avg_cr_mo_clicks = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['cr_mo_clicks'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['cr_mo_clicks']['dates'] = $reportsColumnData['cr_mo_clicks'];
                    $tmpOperators['cr_mo_clicks']['total'] = $total_avg_cr_mo_clicks['sum'];
                    $tmpOperators['cr_mo_clicks']['t_mo_end'] = $total_avg_cr_mo_clicks['T_Mo_End'];
                    $tmpOperators['cr_mo_clicks']['avg'] = $total_avg_cr_mo_clicks['avg'];


                    $total_avg_cr_mo_landing = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['cr_mo_landing'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['cr_mo_landing']['dates'] = $reportsColumnData['cr_mo_landing'];
                    $tmpOperators['cr_mo_landing']['total'] = $total_avg_cr_mo_landing['sum'];
                    $tmpOperators['cr_mo_landing']['t_mo_end'] = $total_avg_cr_mo_landing['T_Mo_End'];
                    $tmpOperators['cr_mo_landing']['avg'] = $total_avg_cr_mo_landing['avg'];


                    $total_avg_landing = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['landing'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['landing']['dates'] = $reportsColumnData['landing'];
                    $tmpOperators['landing']['total'] = $total_avg_landing['sum'];
                    $tmpOperators['landing']['t_mo_end'] = $total_avg_landing['T_Mo_End'];
                    $tmpOperators['landing']['avg'] = $total_avg_landing['avg'];


                    $total_avg_mo = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['mo'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['mo']['dates'] = $reportsColumnData['mo'];
                    $tmpOperators['mo']['total'] = $total_avg_mo['sum'];
                    $tmpOperators['mo']['t_mo_end'] = $total_avg_mo['T_Mo_End'];
                    $tmpOperators['mo']['avg'] = $total_avg_mo['avg'];


                    $total_avg_reg = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['reg'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['reg']['dates'] = $reportsColumnData['reg'];
                    $tmpOperators['reg']['total'] = $total_avg_reg['sum'];
                    $tmpOperators['reg']['t_mo_end'] = $total_avg_reg['T_Mo_End'];
                    $tmpOperators['reg']['avg'] = $total_avg_reg['avg'];


                    $total_avg_unreg = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['unreg'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['unreg']['dates'] = $reportsColumnData['unreg'];
                    $tmpOperators['unreg']['total'] = $total_avg_unreg['sum'];
                    $tmpOperators['unreg']['t_mo_end'] = $total_avg_unreg['T_Mo_End'];
                    $tmpOperators['unreg']['avg'] = $total_avg_unreg['avg'];


                    $total_avg_price_mo = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['price_mo'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['price_mo']['dates'] = $reportsColumnData['price_mo'];
                    $tmpOperators['price_mo']['total'] = $total_avg_price_mo['sum'];
                    $tmpOperators['price_mo']['t_mo_end'] = $total_avg_price_mo['T_Mo_End'];
                    $tmpOperators['price_mo']['avg'] = $total_avg_price_mo['avg'];

                    $total_avg_price_mo_cost = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['price_mo_cost'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['price_mo_cost']['dates'] = $reportsColumnData['price_mo_cost'];
                    $tmpOperators['price_mo_cost']['total'] = $total_avg_price_mo_cost['sum'];
                    $tmpOperators['price_mo_cost']['t_mo_end'] = $total_avg_price_mo_cost['T_Mo_End'];
                    $tmpOperators['price_mo_cost']['avg'] = $total_avg_price_mo_cost['avg'];

                    $total_avg_price_mo_mo = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['price_mo_mo'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['price_mo_mo']['dates'] = $reportsColumnData['price_mo_mo'];
                    $tmpOperators['price_mo_mo']['total'] = $total_avg_price_mo_mo['sum'];
                    $tmpOperators['price_mo_mo']['t_mo_end'] = $total_avg_price_mo_mo['T_Mo_End'];
                    $tmpOperators['price_mo_mo']['avg'] = $total_avg_price_mo_mo['avg'];


                    $total_avg_active_subs = UtilityReportsMonthly::calculateTotalSubscribe($operator,$reportsColumnData['active_subs'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['active_subs']['dates'] = $reportsColumnData['active_subs'];
                    $tmpOperators['active_subs']['total'] = $total_avg_active_subs['sum'];
                    $tmpOperators['active_subs']['t_mo_end'] = $total_avg_active_subs['T_Mo_End'];
                    $tmpOperators['active_subs']['avg'] = $total_avg_active_subs['avg'];


                    $total_avg_arpu_7 = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['arpu_7'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['arpu_7']['dates'] = $reportsColumnData['arpu_7'];
                    $tmpOperators['arpu_7']['total'] = $total_avg_arpu_7['sum'];
                    $tmpOperators['arpu_7']['t_mo_end'] = $total_avg_arpu_7['T_Mo_End'];
                    $tmpOperators['arpu_7']['avg'] = $total_avg_arpu_7['avg'];


                    $total_avg_arpu_7_usd = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['arpu_7_usd'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['arpu_7_usd']['dates'] = $reportsColumnData['arpu_7_usd'];
                    $tmpOperators['arpu_7_usd']['total'] = $total_avg_arpu_7_usd['sum'];
                    $tmpOperators['arpu_7_usd']['t_mo_end'] = $total_avg_arpu_7_usd['T_Mo_End'];
                    $tmpOperators['arpu_7_usd']['avg'] = $total_avg_arpu_7_usd['avg'];


                    $total_avg_arpu_30 = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['arpu_30'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['arpu_30']['dates'] = $reportsColumnData['arpu_30'];
                    $tmpOperators['arpu_30']['total'] = $total_avg_arpu_30['sum'];
                    $tmpOperators['arpu_30']['t_mo_end'] = $total_avg_arpu_30['T_Mo_End'];
                    $tmpOperators['arpu_30']['avg'] = $total_avg_arpu_30['avg'];


                    $total_avg_arpu_30_usd = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['arpu_30_usd'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['arpu_30_usd']['dates'] = $reportsColumnData['arpu_30_usd'];
                    $tmpOperators['arpu_30_usd']['total'] = $total_avg_arpu_30_usd['sum'];
                    $tmpOperators['arpu_30_usd']['t_mo_end'] = $total_avg_arpu_30_usd['T_Mo_End'];
                    $tmpOperators['arpu_30_usd']['avg'] = $total_avg_arpu_30_usd['avg'];


                    $total_avg_reg_sub = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['reg_sub'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['reg_sub']['dates'] = $reportsColumnData['reg_sub'];
                    $tmpOperators['reg_sub']['total'] = $total_avg_reg_sub['sum'];
                    $tmpOperators['reg_sub']['t_mo_end'] = $total_avg_reg_sub['T_Mo_End'];
                    $tmpOperators['reg_sub']['avg'] = $total_avg_reg_sub['avg'];


                    $total_avg_roi = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['roi'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['roi']['dates'] = $reportsColumnData['roi'];
                    $tmpOperators['roi']['total'] = $total_avg_roi['sum'];
                    $tmpOperators['roi']['t_mo_end'] = $total_avg_roi['T_Mo_End'];
                    $tmpOperators['roi']['avg'] = $total_avg_roi['avg'];


                    $total_avg_bill = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['bill'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['bill']['dates']=$reportsColumnData['bill'];
                    $tmpOperators['bill']['total']=0;
                    $tmpOperators['bill']['t_mo_end']=0;
                    $tmpOperators['bill']['avg']=$total_avg_bill['avg'];


                    $total_avg_firstpush = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['firstpush'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['firstpush']['dates']=$reportsColumnData['firstpush'];
                    $tmpOperators['firstpush']['total']=0;
                    $tmpOperators['firstpush']['t_mo_end']=0;
                    $tmpOperators['firstpush']['avg']=$total_avg_firstpush['avg'];


                    $total_avg_dailypush = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['dailypush'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['dailypush']['dates']=$reportsColumnData['dailypush'];
                    $tmpOperators['dailypush']['total']=0;
                    $tmpOperators['dailypush']['t_mo_end']=0;
                    $tmpOperators['dailypush']['avg']=$total_avg_dailypush['avg'];


                    $total_avg_last_7_gros_rev = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['last_7_gros_rev'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['last_7_gros_rev']['dates'] = $reportsColumnData['last_7_gros_rev'];
                    $tmpOperators['last_7_gros_rev']['total'] = $total_avg_last_7_gros_rev['sum'];
                    $tmpOperators['last_7_gros_rev']['t_mo_end'] = $total_avg_last_7_gros_rev['T_Mo_End'];
                    $tmpOperators['last_7_gros_rev']['avg'] = $total_avg_last_7_gros_rev['avg'];


                    $total_avg_last_7_reg = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['last_7_reg'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['last_7_reg']['dates'] = $reportsColumnData['last_7_reg'];
                    $tmpOperators['last_7_reg']['total'] = $total_avg_last_7_reg['sum'];
                    $tmpOperators['last_7_reg']['t_mo_end'] = $total_avg_last_7_reg['T_Mo_End'];
                    $tmpOperators['last_7_reg']['avg'] = $total_avg_last_7_reg['avg'];


                    $total_avg_last_30_gros_rev = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['last_30_gros_rev'],$startColumnDateDisplay,$end_date);

                    $tmpOperators['last_30_gros_rev']['dates'] = $reportsColumnData['last_30_gros_rev'];
                    $tmpOperators['last_30_gros_rev']['total'] = $total_avg_last_30_gros_rev['sum'];
                    $tmpOperators['last_30_gros_rev']['t_mo_end'] = $total_avg_last_30_gros_rev['T_Mo_End'];
                    $tmpOperators['last_30_gros_rev']['avg'] = $total_avg_last_30_gros_rev['avg'];


                    $total_avg_last_30_reg = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['last_30_reg'],$startColumnDateDisplay,$end_date);

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
            $SelectedCompanies=array();
            $RowCompanyData = array();
            $displayUnknown = array();
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

            // dd($SelectedCompanies);

            if(!empty($SelectedCompanies))
            {
                foreach ($SelectedCompanies as $company_id => $SelectedCompany)
                {
                    $tempDataArr = array();
                    if ($company_id === 'unknown') {
                        $dataRows = $displayUnknown['unknown'];
                    } else {
                        $dataRows = $displayCompanies[$company_id];
                    }
                    $dataRowSum = UtilityReports::pnlDetailsDataSum($dataRows);

                    $company_avg_arpu_7_usd = UtilityReports::calculateTotalAVG($operator,$dataRowSum['arpu_7_usd']['dates'],$startColumnDateDisplay,$end_date);
                    $dataRowSum['arpu_7_usd']['avg'] = $company_avg_arpu_7_usd['avg'];

                    $company_avg_arpu_30_usd = UtilityReports::calculateTotalAVG($operator,$dataRowSum['arpu_30_usd']['dates'],$startColumnDateDisplay,$end_date);
                    $dataRowSum['arpu_30_usd']['avg'] = $company_avg_arpu_30_usd['avg'];

                    $company_avg_bill = UtilityReports::calculateTotalAVG($operator,$dataRowSum['bill']['dates'],$startColumnDateDisplay,$end_date);
                    $dataRowSum['bill']['avg']=$company_avg_bill['avg'];

                    $company_avg_firstpush = UtilityReports::calculateTotalAVG($operator,$dataRowSum['firstpush']['dates'],$startColumnDateDisplay,$end_date);
                    $dataRowSum['firstpush']['avg']=$company_avg_firstpush['avg'];

                    $company_avg_dailypush = UtilityReports::calculateTotalAVG($operator,$dataRowSum['dailypush']['dates'],$startColumnDateDisplay,$end_date);

                    if(!empty($dataRowSum)){
                        foreach ($dataRowSum as $key1 => $value1) {
                            if($key1 == 'roi'){
                                foreach ($value1 as $key2 => $value2) {
                                    if($key2 == 'dates'){
                                        foreach ($value2 as $key3 => $value3) {
                                            $roi_arpu = ($dataRowSum['arpu_30_usd']['dates'][$key3]['value'] == 0) ? (float)0 : $dataRowSum['arpu_30_usd']['dates'][$key3]['value'];
                                            $price_mo = ($dataRowSum['price_mo']['dates'][$key3]['value'] == 0) ? (float)0 : $dataRowSum['price_mo']['dates'][$key3]['value'];
                                            $dataRowSum['roi']['dates'][$key3]['value'] = ($roi_arpu == 0) ? (float)0 : ($price_mo / $roi_arpu);
                                        }
                                    }
                                }
                            }
                        }
                    }

                    $dataRowSum['dailypush']['avg']=$company_avg_dailypush['avg'];
                    $tempDataArr['company'] = $SelectedCompany;
                    $tempDataArr['month_string'] = $month;
                    $tempDataArr = array_merge($tempDataArr,$dataRowSum);
                    $RowCompanyData[] = $tempDataArr;
                }

            }

            // dd($RowCompanyData);

            $sumemry = $RowCompanyData;
            $sumOfSummaryData = UtilityReports::pnlDetailsDataSum($sumemry);
            $no_of_days = $no_of_months;

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

            if(!empty($sumOfSummaryData)){
                foreach ($sumOfSummaryData as $key1 => $value1) {
                    if($key1 == 'roi'){
                        foreach ($value1 as $key2 => $value2) {
                            if($key2 == 'dates'){
                                foreach ($value2 as $key3 => $value3) {
                                    $roi_arpu = ($sumOfSummaryData['arpu_30_usd']['dates'][$key3]['value'] == 0) ? (float)0 : $sumOfSummaryData['arpu_30_usd']['dates'][$key3]['value'];
                                    $price_mo = ($sumOfSummaryData['price_mo']['dates'][$key3]['value'] == 0) ? (float)0 : $sumOfSummaryData['price_mo']['dates'][$key3]['value'];
                                    $sumOfSummaryData['roi']['dates'][$key3]['value'] = ($roi_arpu == 0) ? (float)0 : ($price_mo / $roi_arpu);
                                }
                            }
                        }
                    }
                }
            }

            $date = Carbon::parse($end_date)->format('Y-m');

            return view('report.monthly_company_pnlreport', compact('date','no_of_days','sumemry','sumOfSummaryData','monthly','CompanyWise','data'));
        }else{
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

      // get Business monthly pnl report
      public function MonthlyPnlReportBusinessDetails(Request $request)
      {
          if(\Auth::user()->can('PNL Detail'))
          {
              $data['OperatorWise'] = $OperatorWise = 1;
              $data['Monthly'] = $Monthly = 1;

              $CountryId = $req_CountryId = $request->country;
              $CompanyId = $req_CompanyId = $request->company;
              $BusinessType = $req_BusinessType = $request->business_type;
              $filterOperator = $req_filterOperator = $request->operatorId;
              $Start_date = $req_Start_date = $request->to;
              $end_date = $req_end_date = trim($request->from);

              if($end_date <= $Start_date)
              {
                  $Start_date = $req_Start_date = trim($request->from);
                  $end_date = $req_end_date = $request->to;
              }

              $firstDayoftheyear = Carbon::now()->startOfYear()->format('Y-m-d');
              $start_date = $firstDayoftheyear;
              $startColumnDateDisplay = $firstDayoftheyear;
              $end_date = Carbon::now()->format('Y-m-d');
              $month = Carbon::now()->format('F Y');
              $companys = Company::get();

              /* filter Search Section */
              $showAllOperator = true;

              if($request->filled('to') && $request->filled('from'))
              {
                  $display_date_input = new Carbon($req_Start_date);
                  $start_date = $req_Start_date;
                  $startColumnDateDisplay = $display_date_input->format('Y-m-d');
                  $end_date = $req_end_date;
                  $month = $display_date_input->format('F Y');
              }

              if($request->filled('company') && $req_CompanyId != "allcompany"  && !$request->filled('operatorId'))
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

              if(!isset($Operators))
              {
                  $request->session()->flash('alert-success', 'User was successful added!');
                  return redirect()->back();
              }

              $Country = Country::all()->toArray();
              $countries = array();
              $sumemry = array();

              if(!empty($Country))
              {
                  foreach($Country as $CountryI)
                  {
                      $countries[$CountryI['id']] = $CountryI;
                  }
              }

              $staticOperators = $Operators->pluck('id_operator')->toArray();

              $datesIndividual = Utility::getRangeDates($startColumnDateDisplay,$end_date);
              $no_of_months = Utility::getRangeMonthsNo($datesIndividual);

              $monthList = array();

              foreach ($no_of_months as $key => $no_of_month) {
                  $month_key = $no_of_month['date'];
                  $monthList[] = $month_key;
              }

              $QueryMonthlyReports = PnlSummeryMonth::filteroperator($staticOperators)->Months($monthList);

              $user = Auth::user();
              $user_id = $user->id;
              $user_type = $user->type;
              $allowAllOperator = $user->WhowAccessAlOperator($user_type);

              if($allowAllOperator)
              {
                  $QueryMonthlyReports = $QueryMonthlyReports->User(0);
              }else{
                  $QueryMonthlyReports = $QueryMonthlyReports->User(0);
                  $QueryMonthlyUserReports = MonthlyReportSummery::filteroperator($staticOperators)->Months($monthList);
                  $QueryMonthlyUserReports = $QueryMonthlyUserReports->User($user_id);
                  $allMonthlyUserData = $QueryMonthlyUserReports->get()->toArray();

                  $reportsMonthUserData = $this->rearrangeOperatorMonthUser($allMonthlyUserData);
              }

              $allMonthlyData = $QueryMonthlyReports->get()->toArray();

              $reportsMonthData = $this->rearrangeOperatorMonth($allMonthlyData);
              $monthdata = $reportsMonthData;

              $start_date_roi = Carbon::yesterday()->subDays(30)->format('Y-m-d');
              $end_date_roi = Carbon::yesterday()->format('Y-m-d');
              $date_roi = Carbon::now()->format('Y-m-d');
              $reports = ReportsPnlsOperatorSummarizes::filterOperator($staticOperators)
              ->OperatorNotNull()
              ->filterDateRange($start_date_roi,$end_date_roi)
              ->SumOfRoiDataOperator()
              ->get()
              ->toArray();

              $active_subs = ReportsPnlsOperatorSummarizes::filteroperator($staticOperators)
              ->where(['date' => $date_roi])
              ->TotalOperator()
              ->get()
              ->toArray();

              $reportsByOperatorIDs = $this->getReportsByOperatorID($reports);
              $active_subsByOperatorIDs = $this->getReportsByOperatorID($active_subs);

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

                      if(isset($operator->revenueshare)){
                          $merchant_revenue_share = $operator->revenueshare->merchant_revenue_share;
                      }else{
                          $merchant_revenue_share = 100;
                      }

                      $tmpOperators['data'] = $monthdata;

                      $country_id = $operator->country_id;
                      $contain_id = Arr::exists($countries, $country_id);
                      $OperatorCountry = array();

                      if($contain_id )
                      {
                          $tmpOperators['country'] = $countries[$country_id];
                          $OperatorCountry = $countries[$country_id];
                      }

                      if(isset($reportsMonthUserData)  && !empty($reportsMonthUserData)){
                          foreach ($reportsMonthUserData as $key1 => $value1) {
                              if($key1 == $id_operator){
                                  foreach ($value1 as $key2 => $value2) {
                                      $monthdata[$id_operator][$key2]['rev'] = $value2['gros_rev'];
                                      $monthdata[$id_operator][$key2]['rev_usd'] = $value2['gros_rev']*$OperatorCountry['usd'];
                                      $monthdata[$id_operator][$key2]['lshare'] = $value2['gros_rev']*($merchant_revenue_share/100);
                                      $monthdata[$id_operator][$key2]['share'] = $value2['gros_rev']*$OperatorCountry['usd']*($merchant_revenue_share/100);
                                  }
                              }
                          }
                      }

                      $reportsColumnData = $this->getPNLReportsDateWise($operator,$no_of_months,$monthdata,$OperatorCountry,$reportsByOperatorIDs,$active_subsByOperatorIDs);

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

                      $total_avg_rev_usd = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['end_user_rev_usd'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['end_user_rev_usd']['dates'] = $reportsColumnData['end_user_rev_usd'];
                      $tmpOperators['end_user_rev_usd']['total'] = $total_avg_rev_usd['sum'];
                      $tmpOperators['end_user_rev_usd']['t_mo_end'] = $total_avg_rev_usd['T_Mo_End'];
                      $tmpOperators['end_user_rev_usd']['avg'] = $total_avg_rev_usd['avg'];

                      $total_avg_rev = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['end_user_rev'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['end_user_rev']['dates'] = $reportsColumnData['end_user_rev'];
                      $tmpOperators['end_user_rev']['total'] = $total_avg_rev['sum'];
                      $tmpOperators['end_user_rev']['t_mo_end'] = $total_avg_rev['T_Mo_End'];
                      $tmpOperators['end_user_rev']['avg'] = $total_avg_rev['avg'];

                      $total_avg_gros_rev_usd = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['gros_rev_usd'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['gros_rev_usd']['dates'] = $reportsColumnData['gros_rev_usd'];
                      $tmpOperators['gros_rev_usd']['total'] = $total_avg_gros_rev_usd['sum'];
                      $tmpOperators['gros_rev_usd']['t_mo_end'] = $total_avg_gros_rev_usd['T_Mo_End'];
                      $tmpOperators['gros_rev_usd']['avg'] = $total_avg_gros_rev_usd['avg'];

                      $total_avg_gros_rev = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['gros_rev'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['gros_rev']['dates'] = $reportsColumnData['gros_rev'];
                      $tmpOperators['gros_rev']['total'] = $total_avg_gros_rev['sum'];
                      $tmpOperators['gros_rev']['t_mo_end'] = $total_avg_gros_rev['T_Mo_End'];
                      $tmpOperators['gros_rev']['avg'] = $total_avg_gros_rev['avg'];

                      $total_avg_cost_campaign = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['cost_campaign'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['cost_campaign']['dates'] = $reportsColumnData['cost_campaign'];
                      $tmpOperators['cost_campaign']['total'] = $total_avg_cost_campaign['sum'];
                      $tmpOperators['cost_campaign']['t_mo_end'] = $total_avg_cost_campaign['T_Mo_End'];
                      $tmpOperators['cost_campaign']['avg'] = $total_avg_cost_campaign['avg'];

                      $total_avg_other_cost = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['other_cost'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['other_cost']['dates'] = $reportsColumnData['other_cost'];
                      $tmpOperators['other_cost']['total'] = $total_avg_other_cost['sum'];
                      $tmpOperators['other_cost']['t_mo_end'] = $total_avg_other_cost['T_Mo_End'];
                      $tmpOperators['other_cost']['avg'] = $total_avg_other_cost['avg'];

                      $total_avg_hosting_cost = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['hosting_cost'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['hosting_cost']['dates'] = $reportsColumnData['hosting_cost'];
                      $tmpOperators['hosting_cost']['total'] = $total_avg_hosting_cost['sum'];
                      $tmpOperators['hosting_cost']['t_mo_end'] = $total_avg_hosting_cost['T_Mo_End'];
                      $tmpOperators['hosting_cost']['avg'] = $total_avg_hosting_cost['avg'];

                      $total_avg_content = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['content'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['content']['dates'] = $reportsColumnData['content'];
                      $tmpOperators['content']['total'] = $total_avg_content['sum'];
                      $tmpOperators['content']['t_mo_end'] = $total_avg_content['T_Mo_End'];
                      $tmpOperators['content']['avg'] = $total_avg_content['avg'];

                      $total_avg_rnd = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['rnd'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['rnd']['dates'] = $reportsColumnData['rnd'];
                      $tmpOperators['rnd']['total'] = $total_avg_rnd['sum'];
                      $tmpOperators['rnd']['t_mo_end'] = $total_avg_rnd['T_Mo_End'];
                      $tmpOperators['rnd']['avg'] = $total_avg_rnd['avg'];

                      $total_avg_bd = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['bd'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['bd']['dates'] = $reportsColumnData['bd'];
                      $tmpOperators['bd']['total'] = $total_avg_bd['sum'];
                      $tmpOperators['bd']['t_mo_end'] = $total_avg_bd['T_Mo_End'];
                      $tmpOperators['bd']['avg'] = $total_avg_bd['avg'];

                      $total_avg_market_cost = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['market_cost'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['market_cost']['dates'] = $reportsColumnData['market_cost'];
                      $tmpOperators['market_cost']['total'] = $total_avg_market_cost['sum'];
                      $tmpOperators['market_cost']['t_mo_end'] = $total_avg_market_cost['T_Mo_End'];
                      $tmpOperators['market_cost']['avg'] = $total_avg_market_cost['avg'];

                      $total_avg_misc_cost = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['misc_cost'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['misc_cost']['dates'] = $reportsColumnData['misc_cost'];
                      $tmpOperators['misc_cost']['total'] = $total_avg_misc_cost['sum'];
                      $tmpOperators['misc_cost']['t_mo_end'] = $total_avg_misc_cost['T_Mo_End'];
                      $tmpOperators['misc_cost']['avg'] = $total_avg_misc_cost['avg'];

                      $total_avg_platform = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['platform'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['platform']['dates'] = $reportsColumnData['platform'];
                      $tmpOperators['platform']['total'] = $total_avg_platform['sum'];
                      $tmpOperators['platform']['t_mo_end'] = $total_avg_platform['T_Mo_End'];
                      $tmpOperators['platform']['avg'] = $total_avg_platform['avg'];

                      $total_avg_pnl = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['pnl'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['pnl']['dates'] = $reportsColumnData['pnl'];
                      $tmpOperators['pnl']['total'] = $total_avg_pnl['sum'];
                      $tmpOperators['pnl']['t_mo_end'] = $total_avg_pnl['T_Mo_End'];
                      $tmpOperators['pnl']['avg'] = $total_avg_pnl['avg'];

                      $total_avg_net_after_tax = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['net_after_tax'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['net_after_tax']['dates'] = $reportsColumnData['net_after_tax'];
                      $tmpOperators['net_after_tax']['total'] = $total_avg_net_after_tax['sum'];
                      $tmpOperators['net_after_tax']['t_mo_end'] = $total_avg_net_after_tax['T_Mo_End'];
                      $tmpOperators['net_after_tax']['avg'] = $total_avg_net_after_tax['avg'];

                      $total_avg_net_revenue_after_tax = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['net_revenue_after_tax'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['net_revenue_after_tax']['dates'] = $reportsColumnData['net_revenue_after_tax'];
                      $tmpOperators['net_revenue_after_tax']['total'] = $total_avg_net_revenue_after_tax['sum'];
                      $tmpOperators['net_revenue_after_tax']['t_mo_end'] = $total_avg_net_revenue_after_tax['T_Mo_End'];
                      $tmpOperators['net_revenue_after_tax']['avg'] = $total_avg_net_revenue_after_tax['avg'];

                      $total_avg_br = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['br'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['br']['dates'] = $reportsColumnData['br'];
                      $tmpOperators['br']['total'] = $total_avg_br['sum'];
                      $tmpOperators['br']['t_mo_end'] = $total_avg_br['T_Mo_End'];
                      $tmpOperators['br']['avg'] = $total_avg_br['avg'];

                      $total_avg_fp = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['fp'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['fp']['dates'] = $reportsColumnData['fp'];
                      $tmpOperators['fp']['total'] = $total_avg_fp['sum'];
                      $tmpOperators['fp']['t_mo_end'] = $total_avg_fp['T_Mo_End'];
                      $tmpOperators['fp']['avg'] = $total_avg_fp['avg'];

                      $total_avg_fp_success = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['fp_success'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['fp_success']['dates'] = $reportsColumnData['fp_success'];
                      $tmpOperators['fp_success']['total'] = $total_avg_fp_success['sum'];
                      $tmpOperators['fp_success']['t_mo_end'] = $total_avg_fp_success['T_Mo_End'];
                      $tmpOperators['fp_success']['avg'] = $total_avg_fp_success['avg'];

                      $total_avg_fp_failed = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['fp_failed'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['fp_failed']['dates'] = $reportsColumnData['fp_failed'];
                      $tmpOperators['fp_failed']['total'] = $total_avg_fp_failed['sum'];
                      $tmpOperators['fp_failed']['t_mo_end'] = $total_avg_fp_failed['T_Mo_End'];
                      $tmpOperators['fp_failed']['avg'] = $total_avg_fp_failed['avg'];

                      $total_avg_dp = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['dp'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['dp']['dates'] = $reportsColumnData['dp'];
                      $tmpOperators['dp']['total'] = $total_avg_dp['sum'];
                      $tmpOperators['dp']['t_mo_end'] = $total_avg_dp['T_Mo_End'];
                      $tmpOperators['dp']['avg'] = $total_avg_dp['avg'];

                      $total_avg_dp_success = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['dp_success'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['dp_success']['dates'] = $reportsColumnData['dp_success'];
                      $tmpOperators['dp_success']['total'] = $total_avg_dp_success['sum'];
                      $tmpOperators['dp_success']['t_mo_end'] = $total_avg_dp_success['T_Mo_End'];
                      $tmpOperators['dp_success']['avg'] = $total_avg_dp_success['avg'];

                      $total_avg_dp_failed = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['dp_failed'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['dp_failed']['dates'] = $reportsColumnData['dp_failed'];
                      $tmpOperators['dp_failed']['total'] = $total_avg_dp_failed['sum'];
                      $tmpOperators['dp_failed']['t_mo_end'] = $total_avg_dp_failed['T_Mo_End'];
                      $tmpOperators['dp_failed']['avg'] = $total_avg_dp_failed['avg'];

                      $total_avg_renewal = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['renewal'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['renewal']['dates'] = $reportsColumnData['renewal'];
                      $tmpOperators['renewal']['total'] = $total_avg_renewal['sum'];
                      $tmpOperators['renewal']['t_mo_end'] = $total_avg_renewal['T_Mo_End'];
                      $tmpOperators['renewal']['avg'] = $total_avg_renewal['avg'];

                      $total_avg_vat = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['vat'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['vat']['dates'] = $reportsColumnData['vat'];
                      $tmpOperators['vat']['total'] = $total_avg_vat['sum'];
                      $tmpOperators['vat']['t_mo_end'] = $total_avg_vat['T_Mo_End'];
                      $tmpOperators['vat']['avg'] = $total_avg_vat['avg'];

                      $total_avg_spec_tax = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['spec_tax'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['spec_tax']['dates'] = $reportsColumnData['spec_tax'];
                      $tmpOperators['spec_tax']['total'] = $total_avg_spec_tax['sum'];
                      $tmpOperators['spec_tax']['t_mo_end'] = $total_avg_spec_tax['T_Mo_End'];
                      $tmpOperators['spec_tax']['avg'] = $total_avg_spec_tax['avg'];

                      $total_avg_government_cost = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['government_cost'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['government_cost']['dates'] = $reportsColumnData['government_cost'];
                      $tmpOperators['government_cost']['total'] = $total_avg_government_cost['sum'];
                      $tmpOperators['government_cost']['t_mo_end'] = $total_avg_government_cost['T_Mo_End'];
                      $tmpOperators['government_cost']['avg'] = $total_avg_government_cost['avg'];

                      $total_avg_dealer_commision = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['dealer_commision'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['dealer_commision']['dates'] = $reportsColumnData['dealer_commision'];
                      $tmpOperators['dealer_commision']['total'] = $total_avg_dealer_commision['sum'];
                      $tmpOperators['dealer_commision']['t_mo_end'] = $total_avg_dealer_commision['T_Mo_End'];
                      $tmpOperators['dealer_commision']['avg'] = $total_avg_dealer_commision['avg'];

                      $total_avg_wht = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['wht'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['wht']['dates'] = $reportsColumnData['wht'];
                      $tmpOperators['wht']['total'] = $total_avg_wht['sum'];
                      $tmpOperators['wht']['t_mo_end'] = $total_avg_wht['T_Mo_End'];
                      $tmpOperators['wht']['avg'] = $total_avg_wht['avg'];

                      $total_avg_misc_tax = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['misc_tax'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['misc_tax']['dates'] = $reportsColumnData['misc_tax'];
                      $tmpOperators['misc_tax']['total'] = $total_avg_misc_tax['sum'];
                      $tmpOperators['misc_tax']['t_mo_end'] = $total_avg_misc_tax['T_Mo_End'];
                      $tmpOperators['misc_tax']['avg'] = $total_avg_misc_tax['avg'];

                      $total_avg_other_tax = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['other_tax'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['other_tax']['dates'] = $reportsColumnData['other_tax'];
                      $tmpOperators['other_tax']['total'] = $total_avg_other_tax['sum'];
                      $tmpOperators['other_tax']['t_mo_end'] = $total_avg_other_tax['T_Mo_End'];
                      $tmpOperators['other_tax']['avg'] = $total_avg_other_tax['avg'];

                      $total_avg_uso = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['uso'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['uso']['dates'] = $reportsColumnData['uso'];
                      $tmpOperators['uso']['total'] = $total_avg_uso['sum'];
                      $tmpOperators['uso']['t_mo_end'] = $total_avg_uso['T_Mo_End'];
                      $tmpOperators['uso']['avg'] = $total_avg_uso['avg'];

                      $total_avg_agre_paxxa = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['agre_paxxa'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['agre_paxxa']['dates'] = $reportsColumnData['agre_paxxa'];
                      $tmpOperators['agre_paxxa']['total'] = $total_avg_agre_paxxa['sum'];
                      $tmpOperators['agre_paxxa']['t_mo_end'] = $total_avg_agre_paxxa['T_Mo_End'];
                      $tmpOperators['agre_paxxa']['avg'] = $total_avg_agre_paxxa['avg'];

                      $total_avg_sbaf = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['sbaf'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['sbaf']['dates'] = $reportsColumnData['sbaf'];
                      $tmpOperators['sbaf']['total'] = $total_avg_sbaf['sum'];
                      $tmpOperators['sbaf']['t_mo_end'] = $total_avg_sbaf['T_Mo_End'];
                      $tmpOperators['sbaf']['avg'] = $total_avg_sbaf['avg'];

                      $total_avg_clicks = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['clicks'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['clicks']['dates'] = $reportsColumnData['clicks'];
                      $tmpOperators['clicks']['total'] = $total_avg_clicks['sum'];
                      $tmpOperators['clicks']['t_mo_end'] = $total_avg_clicks['T_Mo_End'];
                      $tmpOperators['clicks']['avg'] = $total_avg_clicks['avg'];

                      $total_avg_ratio_for_cpa = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['ratio_for_cpa'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['ratio_for_cpa']['dates'] = $reportsColumnData['ratio_for_cpa'];
                      $tmpOperators['ratio_for_cpa']['total'] = $total_avg_ratio_for_cpa['sum'];
                      $tmpOperators['ratio_for_cpa']['t_mo_end'] = $total_avg_ratio_for_cpa['T_Mo_End'];
                      $tmpOperators['ratio_for_cpa']['avg'] = $total_avg_ratio_for_cpa['avg'];

                      $total_avg_cpa_price = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['cpa_price'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['cpa_price']['dates'] = $reportsColumnData['cpa_price'];
                      $tmpOperators['cpa_price']['total'] = $total_avg_cpa_price['sum'];
                      $tmpOperators['cpa_price']['t_mo_end'] = $total_avg_cpa_price['T_Mo_End'];
                      $tmpOperators['cpa_price']['avg'] = $total_avg_cpa_price['avg'];

                      $total_avg_cr_mo_clicks = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['cr_mo_clicks'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['cr_mo_clicks']['dates'] = $reportsColumnData['cr_mo_clicks'];
                      $tmpOperators['cr_mo_clicks']['total'] = $total_avg_cr_mo_clicks['sum'];
                      $tmpOperators['cr_mo_clicks']['t_mo_end'] = $total_avg_cr_mo_clicks['T_Mo_End'];
                      $tmpOperators['cr_mo_clicks']['avg'] = $total_avg_cr_mo_clicks['avg'];

                      $total_avg_cr_mo_landing = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['cr_mo_landing'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['cr_mo_landing']['dates'] = $reportsColumnData['cr_mo_landing'];
                      $tmpOperators['cr_mo_landing']['total'] = $total_avg_cr_mo_landing['sum'];
                      $tmpOperators['cr_mo_landing']['t_mo_end'] = $total_avg_cr_mo_landing['T_Mo_End'];
                      $tmpOperators['cr_mo_landing']['avg'] = $total_avg_cr_mo_landing['avg'];

                      $total_avg_landing = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['landing'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['landing']['dates'] = $reportsColumnData['landing'];
                      $tmpOperators['landing']['total'] = $total_avg_landing['sum'];
                      $tmpOperators['landing']['t_mo_end'] = $total_avg_landing['T_Mo_End'];
                      $tmpOperators['landing']['avg'] = $total_avg_landing['avg'];

                      $total_avg_mo = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['mo'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['mo']['dates'] = $reportsColumnData['mo'];
                      $tmpOperators['mo']['total'] = $total_avg_mo['sum'];
                      $tmpOperators['mo']['t_mo_end'] = $total_avg_mo['T_Mo_End'];
                      $tmpOperators['mo']['avg'] = $total_avg_mo['avg'];

                      $total_avg_reg = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['reg'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['reg']['dates'] = $reportsColumnData['reg'];
                      $tmpOperators['reg']['total'] = $total_avg_reg['sum'];
                      $tmpOperators['reg']['t_mo_end'] = $total_avg_reg['T_Mo_End'];
                      $tmpOperators['reg']['avg'] = $total_avg_reg['avg'];

                      $total_avg_unreg = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['unreg'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['unreg']['dates'] = $reportsColumnData['unreg'];
                      $tmpOperators['unreg']['total'] = $total_avg_unreg['sum'];
                      $tmpOperators['unreg']['t_mo_end'] = $total_avg_unreg['T_Mo_End'];
                      $tmpOperators['unreg']['avg'] = $total_avg_unreg['avg'];

                      $total_avg_price_mo = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['price_mo'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['price_mo']['dates'] = $reportsColumnData['price_mo'];
                      $tmpOperators['price_mo']['total'] = $total_avg_price_mo['sum'];
                      $tmpOperators['price_mo']['t_mo_end'] = $total_avg_price_mo['T_Mo_End'];
                      $tmpOperators['price_mo']['avg'] = $total_avg_price_mo['avg'];

                      $total_avg_price_mo_cost = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['price_mo_cost'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['price_mo_cost']['dates'] = $reportsColumnData['price_mo_cost'];
                      $tmpOperators['price_mo_cost']['total'] = $total_avg_price_mo_cost['sum'];
                      $tmpOperators['price_mo_cost']['t_mo_end'] = $total_avg_price_mo_cost['T_Mo_End'];
                      $tmpOperators['price_mo_cost']['avg'] = $total_avg_price_mo_cost['avg'];

                      $total_avg_price_mo_mo = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['price_mo_mo'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['price_mo_mo']['dates'] = $reportsColumnData['price_mo_mo'];
                      $tmpOperators['price_mo_mo']['total'] = $total_avg_price_mo_mo['sum'];
                      $tmpOperators['price_mo_mo']['t_mo_end'] = $total_avg_price_mo_mo['T_Mo_End'];
                      $tmpOperators['price_mo_mo']['avg'] = $total_avg_price_mo_mo['avg'];

                      $total_avg_active_subs = UtilityReportsMonthly::calculateTotalSubscribe($operator,$reportsColumnData['active_subs'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['active_subs']['dates'] = $reportsColumnData['active_subs'];
                      $tmpOperators['active_subs']['total'] = $total_avg_active_subs['sum'];
                      $tmpOperators['active_subs']['t_mo_end'] = $total_avg_active_subs['T_Mo_End'];
                      $tmpOperators['active_subs']['avg'] = $total_avg_active_subs['avg'];

                      $total_avg_arpu_7 = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['arpu_7'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['arpu_7']['dates'] = $reportsColumnData['arpu_7'];
                      $tmpOperators['arpu_7']['total'] = $total_avg_arpu_7['sum'];
                      $tmpOperators['arpu_7']['t_mo_end'] = $total_avg_arpu_7['T_Mo_End'];
                      $tmpOperators['arpu_7']['avg'] = $total_avg_arpu_7['avg'];

                      $total_avg_arpu_7_usd = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['arpu_7_usd'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['arpu_7_usd']['dates'] = $reportsColumnData['arpu_7_usd'];
                      $tmpOperators['arpu_7_usd']['total'] = $total_avg_arpu_7_usd['sum'];
                      $tmpOperators['arpu_7_usd']['t_mo_end'] = $total_avg_arpu_7_usd['T_Mo_End'];
                      $tmpOperators['arpu_7_usd']['avg'] = $total_avg_arpu_7_usd['avg'];

                      $total_avg_arpu_30 = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['arpu_30'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['arpu_30']['dates'] = $reportsColumnData['arpu_30'];
                      $tmpOperators['arpu_30']['total'] = $total_avg_arpu_30['sum'];
                      $tmpOperators['arpu_30']['t_mo_end'] = $total_avg_arpu_30['T_Mo_End'];
                      $tmpOperators['arpu_30']['avg'] = $total_avg_arpu_30['avg'];

                      $total_avg_arpu_30_usd = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['arpu_30_usd'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['arpu_30_usd']['dates'] = $reportsColumnData['arpu_30_usd'];
                      $tmpOperators['arpu_30_usd']['total'] = $total_avg_arpu_30_usd['sum'];
                      $tmpOperators['arpu_30_usd']['t_mo_end'] = $total_avg_arpu_30_usd['T_Mo_End'];
                      $tmpOperators['arpu_30_usd']['avg'] = $total_avg_arpu_30_usd['avg'];

                      $total_avg_reg_sub = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['reg_sub'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['reg_sub']['dates'] = $reportsColumnData['reg_sub'];
                      $tmpOperators['reg_sub']['total'] = $total_avg_reg_sub['sum'];
                      $tmpOperators['reg_sub']['t_mo_end'] = $total_avg_reg_sub['T_Mo_End'];
                      $tmpOperators['reg_sub']['avg'] = $total_avg_reg_sub['avg'];

                      $total_avg_roi = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['roi'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['roi']['dates'] = $reportsColumnData['roi'];
                      $tmpOperators['roi']['total'] = $total_avg_roi['sum'];
                      $tmpOperators['roi']['t_mo_end'] = $total_avg_roi['T_Mo_End'];
                      $tmpOperators['roi']['avg'] = $total_avg_roi['avg'];

                      $total_avg_bill = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['bill'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['bill']['dates'] = $reportsColumnData['bill'];
                      $tmpOperators['bill']['total'] = 0;
                      $tmpOperators['bill']['t_mo_end'] = 0;
                      $tmpOperators['bill']['avg'] = $total_avg_bill['avg'];

                      $total_avg_firstpush = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['firstpush'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['firstpush']['dates'] = $reportsColumnData['firstpush'];
                      $tmpOperators['firstpush']['total'] = 0;
                      $tmpOperators['firstpush']['t_mo_end'] = 0;
                      $tmpOperators['firstpush']['avg'] = $total_avg_firstpush['avg'];

                      $total_avg_dailypush = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['dailypush'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['dailypush']['dates'] = $reportsColumnData['dailypush'];
                      $tmpOperators['dailypush']['total'] = 0;
                      $tmpOperators['dailypush']['t_mo_end'] = 0;
                      $tmpOperators['dailypush']['avg'] = $total_avg_dailypush['avg'];

                      $total_avg_last_7_gros_rev = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['last_7_gros_rev'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['last_7_gros_rev']['dates'] = $reportsColumnData['last_7_gros_rev'];
                      $tmpOperators['last_7_gros_rev']['total'] = $total_avg_last_7_gros_rev['sum'];
                      $tmpOperators['last_7_gros_rev']['t_mo_end'] = $total_avg_last_7_gros_rev['T_Mo_End'];
                      $tmpOperators['last_7_gros_rev']['avg'] = $total_avg_last_7_gros_rev['avg'];

                      $total_avg_last_7_reg = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['last_7_reg'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['last_7_reg']['dates'] = $reportsColumnData['last_7_reg'];
                      $tmpOperators['last_7_reg']['total'] = $total_avg_last_7_reg['sum'];
                      $tmpOperators['last_7_reg']['t_mo_end'] = $total_avg_last_7_reg['T_Mo_End'];
                      $tmpOperators['last_7_reg']['avg'] = $total_avg_last_7_reg['avg'];

                      $total_avg_last_30_gros_rev = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['last_30_gros_rev'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['last_30_gros_rev']['dates'] = $reportsColumnData['last_30_gros_rev'];
                      $tmpOperators['last_30_gros_rev']['total'] = $total_avg_last_30_gros_rev['sum'];
                      $tmpOperators['last_30_gros_rev']['t_mo_end'] = $total_avg_last_30_gros_rev['T_Mo_End'];
                      $tmpOperators['last_30_gros_rev']['avg'] = $total_avg_last_30_gros_rev['avg'];

                      $total_avg_last_30_reg = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['last_30_reg'],$startColumnDateDisplay,$end_date);
                      $tmpOperators['last_30_reg']['dates'] = $reportsColumnData['last_30_reg'];
                      $tmpOperators['last_30_reg']['total'] = $total_avg_last_30_reg['sum'];
                      $tmpOperators['last_30_reg']['t_mo_end'] = $total_avg_last_30_reg['T_Mo_End'];
                      $tmpOperators['last_30_reg']['avg'] = $total_avg_last_30_reg['avg'];

                      $sumemry[] = $tmpOperators;
                  }
              }


              $datas = [];
              $data1 = ['unknown' => ['operators' => [], 'operator_count' => 0]];
              $displayBusinessType = [];


              if (!empty($sumemry) && is_array($sumemry)) {
                  foreach ($sumemry as $sumemries) {
                      // Check if 'operator' key exists and 'business_type' is not NULL
                      if (isset($sumemries['operator']['business_type']) && $sumemries['operator']['business_type'] !== NULL) {
                          $business_type = $sumemries['operator']['business_type'];
                          $datas[$business_type]['operators'][] = $sumemries;
                      } else {
                          $data1['unknown']['operators'][] = $sumemries;
                      }
                  }
              }

              // Calculate operator count and summary for each business type
              foreach ($datas as $type => $business_data) {
                  $datas[$type]['operator_count'] = count($business_data['operators']);
                  $displayBusinessType[$type]['summary'] = UtilityReports::pnlDetailsDataSum($business_data['operators']);
              }

              // Calculate operator count and summary for unknown business type if there are any unknown operators
              if (!empty($data1['unknown']['operators'])) {
                  $data1['unknown']['operator_count'] = count($data1['unknown']['operators']);
                  $data1['unknown']['summary'] = UtilityReports::pnlDetailsDataSum($data1['unknown']['operators']);
              }

              // Merge known business types into a single array
              $result = [];

              foreach ($datas as $type => $business_data) {
                  $result[$type] = [
                      'operator_count' => $business_data['operator_count'],
                      'summary' => $displayBusinessType[$type]['summary']
                  ];
              }

              // Include 'unknown' key only if there are operators with unknown business type
              if (!empty($data1['unknown']['operators'])) {
                  $result['unknown'] = $data1['unknown'];
              }

              $no_of_days = $no_of_months;
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

              if(!empty($sumOfSummaryData)){
                  foreach ($sumOfSummaryData as $key1 => $value1) {
                      if($key1 == 'roi'){
                          foreach ($value1 as $key2 => $value2) {
                              if($key2 == 'dates'){
                                  foreach ($value2 as $key3 => $value3) {
                                      $roi_arpu = ($sumOfSummaryData['arpu_30_usd']['dates'][$key3]['value'] == 0) ? (float)0 : $sumOfSummaryData['arpu_30_usd']['dates'][$key3]['value'];
                                      $price_mo = ($sumOfSummaryData['price_mo']['dates'][$key3]['value'] == 0) ? (float)0 : $sumOfSummaryData['price_mo']['dates'][$key3]['value'];
                                      $sumOfSummaryData['roi']['dates'][$key3]['value'] = ($roi_arpu == 0) ? (float)0 : ($price_mo / $roi_arpu);
                                  }
                              }
                          }
                      }
                  }
              }

              $date = Carbon::parse($end_date)->format('Y-m');

              return view('report.monthly_business_summary', compact('date','no_of_days','result','sumOfSummaryData','data'));
          }else{
              return response()->json(['error' => __('Permission Denied.')], 401);
          }
      }

    // get pnl report date wise
    function getPNLReportsDateWise($operator,$no_of_days,$reportsByIDs,$OperatorCountry,$reportsByOperatorIDs=array(),$active_subsByOperatorIDs=array())
    {
        if(\Auth::user()->can('PNL Detail'))
        {
            // dd($reportsByIDs);
            $usdValue = $OperatorCountry['usd'];
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
            if(isset($revenue_share))
            {
                $merchent_share =$revenue_share->merchant_revenue_share;
                $operator_share =$revenue_share->operator_revenue_share;
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
                $market_Arr = array();
                $misc_cost_Arr = array();
                $platform_Arr = array();
                $other_tax_Arr = array();
                $pnl_Arr = array();
                $net_after_tax_Arr = array();
                $net_revenue_after_tax_Arr = array();
                $br_Arr = array();
                $fp_Arr = array();
                $fp_success_Arr = array();
                $dp_Arr = array();
                $dp_success_Arr = array();
                $fp_failed_Arr = array();
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

                    $roiData = Arr::get($reportsByOperatorIDs, $id_operator, 0);

                    $roiActiveSubsData = Arr::get($active_subsByOperatorIDs, $id_operator, 0);

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

                    if($key == date('Y-m')){
                        $price_mo = ($roiActiveSubsData['mo'] == 0) ? (float)0 : ($roiActiveSubsData['cost_campaign']/$roiActiveSubsData['mo']);
                    }else{
                        $price_mo = ($mo == 0) ? (float)0 : ($cost_campaign/$mo);
                    }

                    $price_mo_cost = $roiActiveSubsData['cost_campaign'];
                    $price_mo_mo = $roiActiveSubsData['mo'];

                    $active_subs = isset($summariserow['active_subs']) ? $summariserow['active_subs'] : 0;
                    $active_subs = sprintf('%0.2f', $active_subs);

                    $arpu_7 = isset($summariserow['arpu_7']) ? $summariserow['arpu_7'] : 0;
                    $arpu_7 = sprintf('%0.2f', $arpu_7);

                    $arpu_7_usd = $arpu_7*$usdValue;

                    $arpu_30 = UtilityReportsMonthly::Arpu30USD($operator,$reportsByIDs,$days,$active_subs,$shareDb);
                    $arpu_30 = sprintf('%0.2f', $arpu_30);

                    if($key == date('Y-m')){
                        $arpu_30_usd = (($roiData['reg']+$roiActiveSubsData['active_subs']) == 0) ? 0 : ($roiData['share'] / ($roiData['reg']+$roiActiveSubsData['active_subs']));
                    }else{
                        $arpu_30_usd = ($reg == 0) ? 0 : ($gros_rev_usd / ($reg + $active_subs));
                    }

                    $reg_sub = $reg + $active_subs;

                    $ROI = UtilityReportsMonthly::ROI($id_operator,$reportsByIDs,$days,$active_subs,$cost_campaign,$mo,$roiData,$roiActiveSubsData);

                    $roi = $roi = ($arpu_30_usd == 0) ? 0 : ($price_mo / $arpu_30_usd);

                    $billRate = UtilityReports::billRate($dp_success,$dp_failed,$active_subs);

                    $firstpush = UtilityReports::FirstPush($fp_success,$fp_failed,$active_subs);

                    $dailypush = UtilityReports::Dailypush($dp_success,$dp_failed,$active_subs);

                    $last_30_gros_rev = $ROI['last_30_gros_rev'];
                    $last_30_reg = $ROI['last_30_reg'];
                    $last_7_gros_rev = $ROI['last_7_gros_rev'];
                    $last_7_reg = $ROI['last_7_reg'];

                    $other_cost = $bd + $hosting_cost + $content + $rnd + $market_cost + $misc_cost;

                    $pnl = $net_after_tax - ($other_cost + $cost_campaign);

                    if(isset($id_operator) && $id_operator == 115){
                        $pnl = isset($end_user_rev_usd) ? $end_user_rev_usd*6/100 : 0;
                    }

                    if (isset($id_operator) && $id_operator == 102) {
                        $pnl = isset($end_user_rev_usd) ? $end_user_rev_usd*4/100 : 0;
                    }

                    if ($id_operator == 167 || $id_operator == 168 || $id_operator == 170 || $id_operator == 171 || $id_operator == 176) {
                        $pnl = isset($end_user_rev_usd) ? $end_user_rev_usd*5/100 : 0;
                    }

                    $pnl = sprintf('%0.2f', $pnl);

                    // $roi_arpu = ($reg == 0) ?  (float)0 : ($gros_rev_usd / $reg_sub);

                    // $roi = ($roi_arpu == 0) ? (float)0 : ($price_mo / $roi_arpu);

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
                $allColumnData['other_tax'] = $other_tax_Arr;
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

    // get report using operator id
    function rearrangeOperatorMonth($reports)
    {
        if(!empty($reports))
        {
            $reportsResult = array();
            $tempreport = array();
            foreach($reports as $report)
            {
                $tempreport[$report['id_operator']][$report['key']] = $report;
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

    function rearrangeOperatorMonthUser($reports)
    {
        if(!empty($reports))
        {
            $reportsResult = array();
            $tempreport = array();
            foreach($reports as $report)
            {
                $tempreport[$report['operator_id']][$report['key']] = $report;
            }

            $reportsResult =  $tempreport;
            return $reportsResult;
        }
    }
}
