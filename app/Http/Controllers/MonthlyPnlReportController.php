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

class MonthlyPnlReportController extends Controller
{
    // get operator monthly pnl report
    public function MonthlyPnlSummaryOperator(Request $request)
    {
        if(\Auth::user()->can('PNL Summary'))
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

            if($request->filled('company') && $req_CompanyId != "allcompany" && !$request->filled('operatorId'))
            {
                $companies = Company::Find($req_CompanyId);
                $Operators_company = array();

                if(!empty($companies))
                {
                    $Operators_company = $companies->company_operators->pluck('operator_id')->toArray();
                    $Operators = Operator::with('revenueshare','RevenushareByDate')->Status(1)->GetOperatorByOperatorId($Operators_company)->get();
                }

                $showAllOperator = false;
            }

            if($request->filled('company') && $request->filled('country') && !$request->filled('operatorId'))
            {
                $dataArr = [
                    'id' => $req_CountryId,
                    'company' => $req_CompanyId,
                ];

                $requestobj = new Request($dataArr);
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
            }else
            {
                $QueryMonthlyReports = $QueryMonthlyReports->User(0);
                $QueryMonthlyUserReports = MonthlyReportSummery::filteroperator($staticOperators)->Months($monthList);
                $QueryMonthlyUserReports = $QueryMonthlyUserReports->User($user_id);
                $allMonthlyUserData = $QueryMonthlyUserReports->get()->toArray();

                $reportsMonthUserData = $this->rearrangeOperatorMonthUser($allMonthlyUserData);
            }

            $allMonthlyData = $QueryMonthlyReports->get()->toArray();

            $reportsMonthData = $this->rearrangeOperatorMonth($allMonthlyData);
            $monthdata = $reportsMonthData;

            if(!empty($Operators))
            {
                foreach($Operators as $operator)
                {
                    $tmpOperators = array();
                    $OperatorCountry = array();

                    $id_operator = $operator->id_operator;
                    $tmpOperators['operator'] = $operator;

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

                    $tmpOperators['data'] = $monthdata;

                    $country_id = $operator->country_id;
                    $contain_id = Arr::exists($countries, $country_id);

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

                    $reportsColumnData = $this->getPNLReportsDateWise($operator,$no_of_months,$monthdata,$OperatorCountry);

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

                    $total_avg_net_rev = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['net_rev'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['net_rev']['dates'] = $reportsColumnData['net_rev'];
                    $tmpOperators['net_rev']['total'] = $total_avg_net_rev['sum'];
                    $tmpOperators['net_rev']['t_mo_end'] = $total_avg_net_rev['T_Mo_End'];
                    $tmpOperators['net_rev']['avg'] = $total_avg_net_rev['avg'];

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

                    $total_avg_platform = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['platform'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['platform']['dates'] = $reportsColumnData['platform'];
                    $tmpOperators['platform']['total'] = $total_avg_platform['sum'];
                    $tmpOperators['platform']['t_mo_end'] = $total_avg_platform['T_Mo_End'];
                    $tmpOperators['platform']['avg'] = $total_avg_platform['avg'];

                    $total_avg_other_tax = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['other_tax'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['other_tax']['dates'] = $reportsColumnData['other_tax'];
                    $tmpOperators['other_tax']['total'] = $total_avg_other_tax['sum'];
                    $tmpOperators['other_tax']['t_mo_end'] = $total_avg_other_tax['T_Mo_End'];
                    $tmpOperators['other_tax']['avg'] = $total_avg_other_tax['avg'];

                    $total_avg_vat = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['vat'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['vat']['dates'] = $reportsColumnData['vat'];
                    $tmpOperators['vat']['total'] = $total_avg_vat['sum'];
                    $tmpOperators['vat']['t_mo_end'] = $total_avg_vat['T_Mo_End'];
                    $tmpOperators['vat']['avg'] = $total_avg_vat['avg'];

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

                    $total_avg_pnl = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['pnl'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['pnl']['dates'] = $reportsColumnData['pnl'];
                    $tmpOperators['pnl']['total'] = $total_avg_pnl['sum'];
                    $tmpOperators['pnl']['t_mo_end'] = $total_avg_pnl['T_Mo_End'];
                    $tmpOperators['pnl']['avg'] = $total_avg_pnl['avg'];

                    $sumemry[] = $tmpOperators;
                }
            }

            $no_of_days = $no_of_months;
            $sumOfSummaryData = UtilityReports::summaryDataSum($sumemry);

            return view('report.monthly_pnlsummary', compact('no_of_days','sumemry','sumOfSummaryData','data'));
        }else{
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    // get country monthly pnl report
    public function MonthlyPnlSummaryCountry(Request $request)
    {
        if(\Auth::user()->can('PNL Summary'))
        {
            $data['CountryWise'] = $CountryWise = 1;
            $data['Monthly'] = $monthly = 1;

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

            if($request->filled('company') && $req_CompanyId != "allcompany" && !$request->filled('operatorId'))
            {
                $companies = Company::Find($req_CompanyId);
                $Operators_company = array();

                if(!empty($companies))
                {
                    $Operators_company = $companies->company_operators->pluck('operator_id')->toArray();
                    $Operators = Operator::with('revenueshare','RevenushareByDate')->Status(1)->GetOperatorByOperatorId($Operators_company)->get();
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
            }else
            {
                $QueryMonthlyReports = $QueryMonthlyReports->User(0);
                $QueryMonthlyUserReports = MonthlyReportSummery::filteroperator($staticOperators)->Months($monthList);
                $QueryMonthlyUserReports = $QueryMonthlyUserReports->User($user_id);
                $allMonthlyUserData = $QueryMonthlyUserReports->get()->toArray();

                $reportsMonthUserData = $this->rearrangeOperatorMonthUser($allMonthlyUserData);
            }

            $allMonthlyData = $QueryMonthlyReports->get()->toArray();

            $reportsMonthData = $this->rearrangeOperatorMonth($allMonthlyData);
            $monthdata = $reportsMonthData;

            /*Start*/

            if(!empty($Operators))
            {
                foreach($Operators as $operator)
                {
                    $tmpOperators = array();
                    $OperatorCountry = array();

                    if($operator->status == 0)
                        continue;

                    $id_operator = $operator->id_operator;

                    $tmpOperators['operator'] = $operator;

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

                    $tmpOperators['data'] = $monthdata;
                    $country_id = $operator->country_id;
                    $contain_id = Arr::exists($countries, $country_id);

                    if($contain_id )
                    {
                        $tmpOperators['country'] = $countries[$country_id];
                        $tmpOperators['country_name'] = $countries[$country_id]['country'];
                        $OperatorCountry = $countries[$country_id];
                    }

                    if(isset($reportsMonthUserData) && !empty($reportsMonthUserData)){
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

                    $reportsColumnData = $this->getPNLReportsDateWise($operator,$no_of_months,$monthdata,$OperatorCountry);

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

                    $total_avg_net_rev = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['net_rev'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['net_rev']['dates'] = $reportsColumnData['net_rev'];
                    $tmpOperators['net_rev']['total'] = $total_avg_net_rev['sum'];
                    $tmpOperators['net_rev']['t_mo_end'] = $total_avg_net_rev['T_Mo_End'];
                    $tmpOperators['net_rev']['avg'] = $total_avg_net_rev['avg'];

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

                    $total_avg_platform = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['platform'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['platform']['dates'] = $reportsColumnData['platform'];
                    $tmpOperators['platform']['total'] = $total_avg_platform['sum'];
                    $tmpOperators['platform']['t_mo_end'] = $total_avg_platform['T_Mo_End'];
                    $tmpOperators['platform']['avg'] = $total_avg_platform['avg'];

                    $total_avg_other_tax = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['other_tax'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['other_tax']['dates'] = $reportsColumnData['other_tax'];
                    $tmpOperators['other_tax']['total'] = $total_avg_other_tax['sum'];
                    $tmpOperators['other_tax']['t_mo_end'] = $total_avg_other_tax['T_Mo_End'];
                    $tmpOperators['other_tax']['avg'] = $total_avg_other_tax['avg'];

                    $total_avg_vat = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['vat'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['vat']['dates'] = $reportsColumnData['vat'];
                    $tmpOperators['vat']['total'] = $total_avg_vat['sum'];
                    $tmpOperators['vat']['t_mo_end'] = $total_avg_vat['T_Mo_End'];
                    $tmpOperators['vat']['avg'] = $total_avg_vat['avg'];

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

                    $total_avg_pnl = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['pnl'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['pnl']['dates'] = $reportsColumnData['pnl'];
                    $tmpOperators['pnl']['total'] = $total_avg_pnl['sum'];
                    $tmpOperators['pnl']['t_mo_end'] = $total_avg_pnl['T_Mo_End'];
                    $tmpOperators['pnl']['avg'] = $total_avg_pnl['avg'];

                    $sumemry[] = $tmpOperators;
                }
            }

            // Country Sum from Operator array
            $displayCountries = array();
            $SelectedCountries = array();
            $RowCountryData = array();

            if(!empty($sumemry))
            {
                foreach ($sumemry as $key => $sumemries)
                {
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
                    $dataRowSum = UtilityReportsMonthly::summaryDataSum($displayCountries[$country_id]);
                    $tempDataArr['country'] = $SelectedCountry;
                    $tempDataArr['month_string'] = $month;
                    $tempDataArr['year'] = $year;
                    $tempDataArr = array_merge($tempDataArr,$dataRowSum);
                    $RowCountryData[] = $tempDataArr;
                }
            }

            $sumemry = $RowCountryData;
            $sumOfSummaryData = UtilityReportsMonthly::summaryDataSum($sumemry);
            $no_of_days = $no_of_months;

            return view('report.monthly_pnlsummary_country',compact('sumemry','no_of_days','sumOfSummaryData','CountryWise','monthly','data'));
        }else{
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    // get company monthly pnl report
    public function MonthlyPnlSummaryCompany(Request $request)
    {
        if(\Auth::user()->can('PNL Summary'))
        {
            $data['CompanyWise'] = $CompanyWise = 1;
            $data['Monthly'] = $monthly = 1;

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

            if($request->filled('company') && $req_CompanyId != "allcompany" && !$request->filled('operatorId'))
            {
                $companies = Company::Find($req_CompanyId);
                $Operators_company = array();

                if(!empty($companies))
                {
                    $Operators_company = $companies->company_operators->pluck('operator_id')->toArray();
                    $Operators = Operator::with('revenueshare','RevenushareByDate')->Status(1)->GetOperatorByOperatorId($Operators_company)->get();
                }

                $showAllOperator = false;
            }

            if($request->filled('company') && $request->filled('country') && !$request->filled('operatorId'))
            {
                $dataArr = [
                    'id' => $req_CountryId,
                    'company' => $req_CompanyId,
                ];

                $requestobj = new Request($dataArr);
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
                    $countries[$CountryI['id']] = $CountryI;
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


            $datesIndividual = Utility::getRangeDates($startColumnDateDisplay,$end_date);
            $no_of_months = Utility::getRangeMonthsNo($datesIndividual);

            $monthList = array();

            foreach ($no_of_months as $key => $no_of_month) {
                $month_key = $no_of_month['date'];
                $monthList[] = $month_key;
            }

            $QueryMonthlyReports = PnlSummeryMonth::filteroperator($operator_ids)->Months($monthList);

            $user = Auth::user();
            $user_id = $user->id;
            $user_type = $user->type;
            $allowAllOperator = $user->WhowAccessAlOperator($user_type);

            if($allowAllOperator)
            {
                $QueryMonthlyReports = $QueryMonthlyReports->User(0);
            }else
            {
                $QueryMonthlyReports = $QueryMonthlyReports->User(0);
                $QueryMonthlyUserReports = MonthlyReportSummery::filteroperator($operator_ids)->Months($monthList);
                $QueryMonthlyUserReports = $QueryMonthlyUserReports->User($user_id);
                $allMonthlyUserData = $QueryMonthlyUserReports->get()->toArray();

                $reportsMonthUserData = $this->rearrangeOperatorMonthUser($allMonthlyUserData);
            }

            $allMonthlyData = $QueryMonthlyReports->get()->toArray();

            $reportsMonthData = $this->rearrangeOperatorMonth($allMonthlyData);
            $monthdata = $reportsMonthData;

            if(!empty($Operators))
            {
                foreach($Operators as $operator)

                {
                    $tmpOperators = array();
                    $OperatorCountry = array();

                    $operator_id = $operator->id_operator;
                    $tmpOperators['operator'] = $operator;

                    if(!isset($com_operators[$operator_id]))
                    {
                        // if The Operator not founds in that array
                        continue;
                    }

                    if(isset($operator->revenueshare)){
                        $merchant_revenue_share = $operator->revenueshare->merchant_revenue_share;
                    }else{
                        $merchant_revenue_share = 100;
                    }

                    $tmpOperators['company'] = $com_operators[$operator_id];
                    $country_id  = $operator->country_id;
                    $contain_id = Arr::exists($countries, $country_id);

                    if($contain_id)
                    {
                        $tmpOperators['country'] = $countries[$country_id];
                        $OperatorCountry = $countries[$country_id];
                    }

                    if(isset($reportsMonthUserData) && !empty($reportsMonthUserData)){
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

                    $reportsColumnData = $this->getPNLReportsDateWise($operator,$no_of_months,$monthdata,$OperatorCountry);

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

                    $total_avg_net_rev = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['net_rev'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['net_rev']['dates'] = $reportsColumnData['net_rev'];
                    $tmpOperators['net_rev']['total'] = $total_avg_net_rev['sum'];
                    $tmpOperators['net_rev']['t_mo_end'] = $total_avg_net_rev['T_Mo_End'];
                    $tmpOperators['net_rev']['avg'] = $total_avg_net_rev['avg'];

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

                    $total_avg_platform = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['platform'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['platform']['dates'] = $reportsColumnData['platform'];
                    $tmpOperators['platform']['total'] = $total_avg_platform['sum'];
                    $tmpOperators['platform']['t_mo_end'] = $total_avg_platform['T_Mo_End'];
                    $tmpOperators['platform']['avg'] = $total_avg_platform['avg'];

                    $total_avg_other_tax = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['other_tax'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['other_tax']['dates'] = $reportsColumnData['other_tax'];
                    $tmpOperators['other_tax']['total'] = $total_avg_other_tax['sum'];
                    $tmpOperators['other_tax']['t_mo_end'] = $total_avg_other_tax['T_Mo_End'];
                    $tmpOperators['other_tax']['avg'] = $total_avg_other_tax['avg'];

                    $total_avg_vat = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['vat'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['vat']['dates'] = $reportsColumnData['vat'];
                    $tmpOperators['vat']['total'] = $total_avg_vat['sum'];
                    $tmpOperators['vat']['t_mo_end'] = $total_avg_vat['T_Mo_End'];
                    $tmpOperators['vat']['avg'] = $total_avg_vat['avg'];

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

                    $total_avg_pnl = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['pnl'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['pnl']['dates'] = $reportsColumnData['pnl'];
                    $tmpOperators['pnl']['total'] = $total_avg_pnl['sum'];
                    $tmpOperators['pnl']['t_mo_end'] = $total_avg_pnl['T_Mo_End'];
                    $tmpOperators['pnl']['avg'] = $total_avg_pnl['avg'];

                    $sumemry[] = $tmpOperators;
                }
            }

            // Company Sum from Operator array
            $displayCompanies = array();
            $SelectedCompanies = array();
            $RowCompanyData = array();

            if(!empty($sumemry))
            {
                foreach ($sumemry as $key => $sumemries)
                {
                    $company_id = $sumemries['company']['id'];
                    $SelectedCompanies[$company_id] = $sumemries['company'];
                    $displayCompanies[$company_id][] = $sumemries;
                }
            }

            if(!empty($SelectedCompanies))
            {
                foreach ($SelectedCompanies as $key => $SelectedCompany)
                {
                    $tempDataArr = array();

                    $company_id = $SelectedCompany['id'];
                    $dataRowSum = UtilityReports::summaryDataSum($displayCompanies[$company_id]);
                    $tempDataArr['company'] = $SelectedCompany;
                    $tempDataArr['month_string'] = $month;
                    $tempDataArr = array_merge($tempDataArr,$dataRowSum);
                    $RowCompanyData[] = $tempDataArr;
                }
            }

            $sumemry = $RowCompanyData;
            $sumOfSummaryData = UtilityReports::summaryDataSum($sumemry);
            $no_of_days = $no_of_months;

            return view('report.monthly_company_pnlreport', compact('no_of_days','sumemry','sumOfSummaryData','monthly','CompanyWise','data'));
        }else{
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    // get business monthly pnl report
    public function MonthlyPnlSummaryBusinessType(Request $request)
    {
        if(\Auth::user()->can('PNL Summary'))
        {
            $data['BusinessWise'] = $BusinessWise = 1;
            $data['Monthly'] = $monthly = 1;

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

            if($request->filled('company') && $req_CompanyId != "allcompany" && !$request->filled('operatorId'))
            {
                $companies = Company::Find($req_CompanyId);
                $Operators_company = array();

                if(!empty($companies))
                {
                    $Operators_company = $companies->company_operators->pluck('operator_id')->toArray();
                    $Operators = Operator::with('revenueshare','RevenushareByDate')->Status(1)->GetOperatorByOperatorId($Operators_company)->get();
                }

                $showAllOperator = false;
            }

            if($request->filled('company') && $request->filled('country') && !$request->filled('operatorId'))
            {
                $dataArr = [
                    'id' => $req_CountryId,
                    'company' => $req_CompanyId,
                ];

                $requestobj = new Request($dataArr);
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
            }else
            {
                $QueryMonthlyReports = $QueryMonthlyReports->User(0);
                $QueryMonthlyUserReports = MonthlyReportSummery::filteroperator($staticOperators)->Months($monthList);
                $QueryMonthlyUserReports = $QueryMonthlyUserReports->User($user_id);
                $allMonthlyUserData = $QueryMonthlyUserReports->get()->toArray();

                $reportsMonthUserData = $this->rearrangeOperatorMonthUser($allMonthlyUserData);
            }

            $allMonthlyData = $QueryMonthlyReports->get()->toArray();

            $reportsMonthData = $this->rearrangeOperatorMonth($allMonthlyData);
            $monthdata = $reportsMonthData;

            if(!empty($Operators))
            {
                foreach($Operators as $operator)

                {
                    $tmpOperators = array();
                    $OperatorCountry = array();

                    $operator_id = $operator->id_operator;
                    $tmpOperators['operator'] = $operator;
                    $business_type = $operator->business_type;
                    $business_type = isset($business_type) ? $business_type : 'Unknown';

                    /*if(!isset($business_type))
                    {
                        continue;
                    }*/

                    if(isset($operator->revenueshare)){
                        $merchant_revenue_share = $operator->revenueshare->merchant_revenue_share;
                    }else{
                        $merchant_revenue_share = 100;
                    }

                    $tmpOperators['company']['name'] = $business_type;
                    $country_id  = $operator->country_id;
                    $contain_id = Arr::exists($countries, $country_id);

                    if($contain_id)
                    {
                        $tmpOperators['country'] = $countries[$country_id];
                        $OperatorCountry = $countries[$country_id];
                    }

                    if(isset($reportsMonthUserData) && !empty($reportsMonthUserData)){
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

                    $reportsColumnData = $this->getPNLReportsDateWise($operator,$no_of_months,$monthdata,$OperatorCountry);

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

                    $total_avg_net_rev = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['net_rev'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['net_rev']['dates'] = $reportsColumnData['net_rev'];
                    $tmpOperators['net_rev']['total'] = $total_avg_net_rev['sum'];
                    $tmpOperators['net_rev']['t_mo_end'] = $total_avg_net_rev['T_Mo_End'];
                    $tmpOperators['net_rev']['avg'] = $total_avg_net_rev['avg'];

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

                    $total_avg_platform = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['platform'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['platform']['dates'] = $reportsColumnData['platform'];
                    $tmpOperators['platform']['total'] = $total_avg_platform['sum'];
                    $tmpOperators['platform']['t_mo_end'] = $total_avg_platform['T_Mo_End'];
                    $tmpOperators['platform']['avg'] = $total_avg_platform['avg'];

                    $total_avg_other_tax = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['other_tax'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['other_tax']['dates'] = $reportsColumnData['other_tax'];
                    $tmpOperators['other_tax']['total'] = $total_avg_other_tax['sum'];
                    $tmpOperators['other_tax']['t_mo_end'] = $total_avg_other_tax['T_Mo_End'];
                    $tmpOperators['other_tax']['avg'] = $total_avg_other_tax['avg'];

                    $total_avg_vat = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['vat'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['vat']['dates'] = $reportsColumnData['vat'];
                    $tmpOperators['vat']['total'] = $total_avg_vat['sum'];
                    $tmpOperators['vat']['t_mo_end'] = $total_avg_vat['T_Mo_End'];
                    $tmpOperators['vat']['avg'] = $total_avg_vat['avg'];

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

                    $total_avg_pnl = UtilityReportsMonthly::calculateRevTotalAVG($operator,$reportsColumnData['pnl'],$startColumnDateDisplay,$end_date);
                    $tmpOperators['pnl']['dates'] = $reportsColumnData['pnl'];
                    $tmpOperators['pnl']['total'] = $total_avg_pnl['sum'];
                    $tmpOperators['pnl']['t_mo_end'] = $total_avg_pnl['T_Mo_End'];
                    $tmpOperators['pnl']['avg'] = $total_avg_pnl['avg'];

                    $sumemry[] = $tmpOperators;
                }
            }

            // Company Sum from Operator array
            $displayCompanies = array();
            $SelectedCompanies = array();
            $RowCompanyData = array();

            if(!empty($sumemry))
            {
                foreach ($sumemry as $key => $sumemries)
                {
                    $business_type = $sumemries['company']['name'];
                    $SelectedCompanies[$business_type] = $sumemries['company'];
                    $displayCompanies[$business_type][] = $sumemries;
                }
            }

            if(!empty($SelectedCompanies))
            {
                foreach ($SelectedCompanies as $key => $SelectedCompany)
                {
                    $tempDataArr = array();

                    $business_type = $SelectedCompany['name'];
                    $dataRowSum = UtilityReports::summaryDataSum($displayCompanies[$business_type]);
                    $tempDataArr['company'] = $SelectedCompany;
                    $tempDataArr['month_string'] = $month;
                    $tempDataArr = array_merge($tempDataArr,$dataRowSum);
                    $RowCompanyData[] = $tempDataArr;
                }
            }

            $sumemry = $RowCompanyData;
            $sumOfSummaryData = UtilityReports::summaryDataSum($sumemry);
            $no_of_days = $no_of_months;

            return view('report.monthly_company_pnlreport', compact('no_of_days','sumemry','sumOfSummaryData','monthly','BusinessWise','data'));
        }else{
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    function getPNLReportsDateWise($operator,$no_of_months,$reportsByIDs,$OperatorCountry)
    {

        $usdValue = $OperatorCountry['usd'];
        $shareDb = array();
        $merchent_share = 1;
        $operator_share = 1;
        $vat = 0;
        $wht = 0;
        $VatByDate = $operator->VatByDate;
        $WhtByDate = $operator->WhtByDate;
        $misc_taxByDate = $operator->MiscTax;
        $revenue_share = $operator->revenueshare;
        $revenushare_by_dates = $operator->RevenushareByDate;
        $country_id = $OperatorCountry['id'];

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

        if(!empty($no_of_months))
        {
            $allColumnData = array();
            $end_user_rev_usd_Arr = array();
            $end_user_rev_Arr = array();
            $gros_rev_usd_Arr = array();
            $gros_rev_Arr = array();
            $net_rev_Arr = array();
            $cost_campaign_Arr = array();
            $other_cost_Arr = array();
            $hosting_cost_Arr = array();
            $content_Arr = array();
            $rnd_Arr = array();
            $bd_Arr = array();
            $market_Arr = array();
            $platform_Arr = array();
            $other_tax_Arr = array();
            $vat_Arr = array();
            $wht_Arr = array();
            $misc_tax_Arr = array();
            $pnl_Arr = array();
            $id_operator = $operator->id_operator;
            $country_id = isset($operator->country_id) ? $operator->country_id : '';
            $last_update= "";
            $update = false;

            foreach($no_of_months as $months)
            {
                $shareDb['merchent_share'] = $merchent_share;
                $shareDb['operator_share'] = $operator_share;

                $key_date = new Carbon($months['date']);
                $key = $key_date->format("Y-m");

                if(isset($revenushare_by_dates[$key]))
                {
                    $merchent_share_by_dates = $revenushare_by_dates[$key]->merchant_revenue_share;
                    $operator_share_by_dates = $revenushare_by_dates[$key]->operator_revenue_share;

                    $shareDb['merchent_share'] = $merchent_share_by_dates;
                    $shareDb['operator_share'] = $operator_share_by_dates;
                }

                $keys = $id_operator.".".$months['date'];
                $summariserow = Arr::get($reportsByIDs, $keys, 0);

                if(isset($summariserow[0]))
                {
                    $summariserow = $summariserow[0];
                }

                if($summariserow != 0 && !$update)
                {
                    $update = true;
                    $last_update = $summariserow['updated_at'];
                }

                $end_user_rev = isset($summariserow['rev']) ? $summariserow['rev'] : 0;
                $end_user_rev = sprintf('%0.2f', $end_user_rev);

                $end_user_rev_usd = $end_user_rev * $usdValue;
                $end_user_rev_usd = sprintf('%0.2f', $end_user_rev_usd);

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

                $bd = $gros_rev_usd * (2.5/100);

                $market_cost = $gros_rev_usd * (1.5/100);

                $misc_cost = !empty($operator->miscCost) ? $gros_rev_usd * ($operator->miscCost/100) : 0;

                $platform = isset($summariserow['platform']) ? $summariserow['platform'] : 0;
                $platform = sprintf('%0.2f', $platform);

                $vat = !empty($operator->vat) ? $gros_rev_usd * ($operator->vat/100) : 0;

                $wht = !empty($operator->wht) ? $gros_rev_usd * ($operator->wht/100) : 0;

                $miscTax = !empty($operator->miscTax) ? $gros_rev_usd * ($operator->miscTax/100) : 0;

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
                    $miscTax = !empty($Misc_tax) ? $gros_rev_usd * ($Misc_tax/100) : 0;

                }
                $other_tax = $vat + $wht + $miscTax;

                if($other_tax != 0){
                    $net_rev = $gros_rev_usd - $other_tax;
                }else{
                    $net_rev = $gros_rev_usd;
                }

                $other_cost = $bd + $hosting_cost + $content + $rnd + $market_cost + $misc_cost;

                $pnl = $net_rev - ($other_cost + $cost_campaign);

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

                $end_user_rev_usd_Arr[$months['date']]['value'] = $end_user_rev_usd;
                $end_user_rev_usd_Arr[$months['date']]['class'] = "bg-hui";

                $end_user_rev_Arr[$months['date']]['value'] = $end_user_rev;
                $end_user_rev_Arr[$months['date']]['class'] = "bg-hui";

                $gros_rev_usd_Arr[$months['date']]['value'] = $gros_rev_usd;
                $gros_rev_usd_Arr[$months['date']]['class'] = "bg-hui";

                $gros_rev_Arr[$months['date']]['value'] = $gros_rev;
                $gros_rev_Arr[$months['date']]['class'] = "bg-hui";

                $net_rev_Arr[$months['date']]['value'] = $net_rev;
                $net_rev_Arr[$months['date']]['class'] = "bg-hui";

                $cost_campaign_Arr[$months['date']]['value'] = $cost_campaign;
                $cost_campaign_Arr[$months['date']]['class'] = "bg-hui";

                $other_cost_Arr[$months['date']]['value'] = $other_cost;
                $other_cost_Arr[$months['date']]['class'] = "bg-hui";

                $hosting_cost_Arr[$months['date']]['value'] = $hosting_cost;
                $hosting_cost_Arr[$months['date']]['class'] = "bg-hui";

                $content_Arr[$months['date']]['value'] = $content;
                $content_Arr[$months['date']]['class'] = "bg-hui";

                $rnd_Arr[$months['date']]['value'] = $rnd;
                $rnd_Arr[$months['date']]['class'] = "bg-hui";

                $bd_Arr[$months['date']]['value'] = $bd;
                $bd_Arr[$months['date']]['class'] = "bg-hui";

                $market_Arr[$months['date']]['value'] = $market_cost;
                $market_Arr[$months['date']]['class'] = "bg-hui";

                $platform_Arr[$months['date']]['value'] = $platform;
                $platform_Arr[$months['date']]['class'] = "bg-hui";

                $other_tax_Arr[$months['date']]['value'] = $other_tax;
                $other_tax_Arr[$months['date']]['class'] = "bg-hui";

                $vat_Arr[$months['date']]['value'] = $vat;
                $vat_Arr[$months['date']]['class'] = "bg-hui";

                $wht_Arr[$months['date']]['value'] = $wht;
                $wht_Arr[$months['date']]['class'] = "bg-hui";

                $misc_tax_Arr[$months['date']]['value'] = $miscTax;
                $misc_tax_Arr[$months['date']]['class'] = "bg-hui";

                $pnl_Arr[$months['date']]['value'] = $pnl;
                $pnl_Arr[$months['date']]['class'] = "bg-hui";
            }

            $last_update_show = "Not updated last month";

            if($last_update != "")
            {
                $last_update_timestamp = Carbon::parse($last_update);
                $last_update_timestamp->setTimezone('Asia/Jakarta');
                $last_update_show = $last_update_timestamp->format("Y-m-d H:i:s"). " Asia/Jakarta";
            }

            $allColumnData['end_user_rev_usd'] = $end_user_rev_usd_Arr;
            $allColumnData['end_user_rev'] = $end_user_rev_Arr;
            $allColumnData['gros_rev_usd'] = $gros_rev_usd_Arr;
            $allColumnData['gros_rev'] = $gros_rev_Arr;
            $allColumnData['net_rev'] = $net_rev_Arr;
            $allColumnData['cost_campaign'] = $cost_campaign_Arr;
            $allColumnData['other_cost'] = $other_cost_Arr;
            $allColumnData['hosting_cost'] = $hosting_cost_Arr;
            $allColumnData['content'] = $content_Arr;
            $allColumnData['rnd'] = $rnd_Arr;
            $allColumnData['bd'] = $bd_Arr;
            $allColumnData['market_cost'] = $market_Arr;
            $allColumnData['platform'] = $platform_Arr;
            $allColumnData['other_tax'] = $other_tax_Arr;
            $allColumnData['vat'] = $vat_Arr;
            $allColumnData['wht'] = $wht_Arr;
            $allColumnData['misc_tax'] = $misc_tax_Arr;
            $allColumnData['pnl'] = $pnl_Arr;
            $allColumnData['last_update'] = $last_update_show;

            return $allColumnData;
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

            $reportsResult = $tempreport;

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

            $reportsResult = $tempreport;

            return $reportsResult;
        }
    }
}
