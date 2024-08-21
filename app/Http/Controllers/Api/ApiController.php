<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Operator;
use App\Models\Country;
use App\Models\report_summarize;
use Illuminate\Support\Carbon;
use App\common\Utility;
use Illuminate\Support\Arr;
use App\common\UtilityReports;
use App\Models\Service;
use Carbon\CarbonPeriod;
use App\Models\ServiceHistory;
use App\Models\RevenueReconciles;
use App\Models\User;

class ApiController extends Controller
{
    public function ArpuData(Request $request)
    {
        // dd($request->all());
        if (!empty($request)) {

            $sdate = $request->get('start_date');

            $edate = $request->get('end_date');

            $keyword = $request->get('country');

            $keyword = strtolower($keyword);

            if (empty($keyword)) {
                return [
                    'status' => 'Error',
                    'code' => 404,
                    'message' => 'Please enter country code'
                ];
            } elseif (empty($sdate)) {
                return [
                    'status' => 'Error',
                    'code' => 404,
                    'message' => 'Please enter start date (Y-m-d)'
                ];
            } elseif (empty($edate)) {
                return [
                    'status' => 'Error',
                    'code' => 404,
                    'message' => 'Please enter end date (Y-m-d)'
                ];
            } else {

                $Start_date = trim($sdate);
                $end_date =  trim($edate);

                if($end_date <= $Start_date)
                {
                    $Start_date = $end_date;
                    $end_date = $Start_date;

                }

                $start_date_input = new Carbon($sdate);
                $display_date_input = new Carbon($sdate);

                $start_date = $start_date_input->subDays(35)->format('Y-m-d');
                $startColumnDateDisplay = $display_date_input->format('Y-m-d');

                $end_date = $end_date;


                $Country = Country::GetByCountrycode($keyword)->first();

                $country_id = isset($Country['id']) ? $Country['id'] : null;

                $Operators = Operator::GetOperatorByCountryId($country_id)->with('revenueshare', 'services')->Status(1)->get();


                if (empty($Country)) {
                    return [
                        'status' => 'Error',
                        'code' => 404,
                        'message' => 'Please enter correct country code'
                    ];
                }

                $OperatorCountry = $Country;


                $arrayOperatorsIds = $Operators->pluck('id_operator')->toArray();

                $reports = report_summarize::filteroperator($arrayOperatorsIds)
                    ->filterDateRange($start_date, $end_date)
                    ->orderBy('operator_id')
                    ->get()
                    ->toArray();
                $reportsByIDs = $this->getReportsOperatorID($reports);

                $datesIndividual = Utility::getRangeDates($sdate, $edate);
                $no_of_days = Utility::getRangeDateNo($datesIndividual);


                if (!empty($Operators)) {
                    $reportsColumnData = [];
                    foreach ($Operators as $operator) {
                        $reportsColumnData[] = $this->getReportsDateWise($operator, $no_of_days, $reportsByIDs, $OperatorCountry);
                    }

                    if(empty($reportsColumnData)){
                        return [
                            'status' => 'Error',
                            'code' => 404,
                            'message' => 'No data found'
                        ];
                    }
                    return [
                        'data' => $reportsColumnData,
                    ];
                }
            }
        } else {
            return [
                'status' => 'Error',
                'code' => 404,
                'message' => 'No data found'
            ];
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
    function getReportsDateWise($operator, $no_of_days, $reportsByIDs, $OperatorCountry)
    {

        $usdValue = isset($OperatorCountry['usd']) ? $OperatorCountry['usd'] : 1;
        // dd($usdValue);
        $shareDb = array();
        $merchent_share = 1;
        $operator_share = 1;
        $operator_name    = $operator->operator_name;

        $revenue_share = $operator->revenueshare;
        $revenushare_by_dates = $operator->RevenushareByDate;
        $country = isset($OperatorCountry['country_code']) ? $OperatorCountry['country_code'] : null;



        if (isset($revenue_share)) {
            $merchent_share = $revenue_share->merchant_revenue_share;
            $operator_share = $revenue_share->operator_revenue_share;
        }

        // if (isset($revenushare_by_dates)) {
        //     foreach ($revenushare_by_dates as $key => $value) {
        //         unset($revenushare_by_dates[$key]);
        //         $revenushare_by_dates[$value['key']] = $value;
        //     }
        // }

        if (!empty($no_of_days)) {
            $allColumnData = array();

            $usarpu30 = array();
            $last_update = "";
            $id_operator = $operator->id_operator;
            // dd($id_operator);

            $testUSDSum = 0;
            $update = false;

            foreach ($no_of_days as $days) {

                $shareDb['merchent_share'] = $merchent_share;
                $shareDb['operator_share'] = $operator_share;

                $keys = $id_operator . "." . $days['date'];

                $key_date = new Carbon($days['date']);
                $key = $key_date->format("Y-m");


                $summariserow = Arr::get($reportsByIDs, $keys, 0);


                if ($summariserow != 0 && !$update) {
                    $update = true;
                    $last_update = $summariserow['updated_at'];
                }

                $total_subscriber = isset($summariserow['total']) ? $summariserow['total'] : 0;


                $arpu30Data = UtilityReports::Arpu30($operator, $reportsByIDs, $days, $total_subscriber, $shareDb);
                $arpu30USD =  $arpu30Data * $usdValue;

                $arpu30USD = number_format($arpu30USD, 4);

                $usarpu30[$days['date']]['country'] = $country;
                $usarpu30[$days['date']]['operator_name'] = $operator_name;
                $usarpu30[$days['date']]['value'] = $arpu30USD;
            }

            $allColumnData['usarpu30'] = $usarpu30;

            return $allColumnData;
        }
    }

    public function reconcileData(Request $request)
    {
        if (!empty($request)) {
            $operator_name = $request->get('operator_name');
            $year = $request->get('year');
            $month = $request->get('month');
            if (empty($operator_name)) {
                return [
                    'status' => false,
                    'code' => 404,
                    'message' => "Please enter operator name"
                ];
            } elseif (empty($year)) {
                return [
                    'status' => false,
                    'code' => 404,
                    'message' => "Please enter year"
                ];
            } elseif (empty($month)) {
                return [
                    'status' => false,
                    'code' => 404,
                    'message' => "Please enter month"
                ];
            } else {
                $operator = Operator::getOperatorByName($operator_name)->first();
                if (empty($operator)) {
                    return [
                        'status' => false,
                        'code' => 404,
                        'message' =>  'Invalid Operator Name!'
                    ];
                }
                $id = $operator['id_operator'];

                $month_name = [1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'];
                $services = RevenueReconciles::filterOperator($id)->filterYear($year)->filterMonth($month)->get()->toArray();

                if (empty($services)) {
                    return [
                        'status' => false,
                        'code' => 404,
                        'message' => "No data found",
                    ];
                }

                foreach ($services as $key => $value) {
                    $keyword = Service::select('keyword')->GetserviceById($value['id_service'])->first();

                    $services[$key]['keyword'] = isset($keyword['keyword']) ? $keyword['keyword'] : 'NULL';
                    // $services[$key]['month'] = $month_name[$value['month']];
                }
                return [
                    'status' => true,
                    'code' => 200,
                    'data' => $services
                ];
            }
        } else {
            return [
                'status' => false,
                'message' => "No data provided."
            ];
        }
    }

    public function useremail(Request $request){

        $user_id = $request->userid;

        $data = [];
        if(isset($user_id)){
            $email = User::Uid($user_id)->select('email')->first();
            $data = $email;
            return  ['status'=>true,'data'=>$data] ;
        }
        return ['status'=>false , 'data' => $data];
    }
    public function ArpuDataUsd(Request $request) {
        try {
            //code...
            if($request->filled("country_code")) {
                $country = Country::where('country_code', $request->country_code)->first();
                return response()->json([
                    "status" => 200,
                    "message" => "Success",
                    "data" => $country
                ]);
            }
            return response()->json([
                "status" => 500,
                "message" => "not found country code"
            ],500);
            
        } catch (\Throwable $th) {
            return response()->json([
                "status" => 500,
                "message" => $th->getMessage()
            ],500);
        }
    }
}
