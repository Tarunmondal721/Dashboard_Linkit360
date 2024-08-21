<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Session;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\report_summarize;
use App\Models\Operator;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use App\Models\Company;
use App\Models\Country;
use App\Models\CompanyOperators;
use App\common\Utility;
use App\common\UtilityReports;


class ReportController_v2 extends Controller
{


    public function summary()
    {
        $Country = Country::all()->toArray();
        $companys = Company::get();
        $static_operator =array(1,2,3,4);
        $countries = array();
        if(!empty($Country))
        {

            foreach($Country as $CountryI)
            {
                $countries[$CountryI['id']]=$CountryI;
            }
                 }



        $contains = Arr::hasAny($Country, "2");


        $Operators = Operator::Status(1)->limit(4)->get();

       $sumemry = array();


      $start_date = Carbon::now()->startOfMonth()->subDays(35)->format('Y-m-d');
      $startColumnDateDisplay = Carbon::now()->startOfMonth()->format('Y-m-d');

      $end_date = Carbon::now()->format('Y-m-d');

      $month = Carbon::now()->format('F Y');

      $reports = report_summarize::filteroperator($static_operator)
      ->filterDateRange($start_date,$end_date)
      ->orderBy('operator_id')
      ->get()->toArray();

      $reportsByIDs = $this->getReportsOperatorID($reports);



      $datesIndividual =Utility::getRangeDates($startColumnDateDisplay,$end_date);

        $no_of_days = Utility::getRangeDateNo($datesIndividual);




                if(!empty($Operators))
                {

                    foreach($Operators as $operator)
                    {
                        $tmpOperators=array();






                    $tmpOperators['operator'] =$operator;
                   $country_id  =$operator->country_id;
                   $contain_id = Arr::exists($countries, $country_id);
                   $OperatorCountry = array();


                    if($contain_id )
                    {
                        /*
                        1 => array:9 [▼
                                    "id" => 1
                                    "country" => "Indonesia"
                                    "country_code" => "ID"
                                    "currency_code" => "IDR"
                                    "currency_value" => "1"
                                    "usd" => "0.000064000"
                                    "flag" => "flag-indonesia.png"
                                    "created_at" => null
                                    "updated_at" => "2022-10-25T11:30:04.000000Z"
                                ]
                                */

                        $tmpOperators['country']=$countries[$country_id];

                        $OperatorCountry = $countries[$country_id];

                    }


                    $reportsColumnData = $this->getReportsDateWise($operator,$no_of_days,$reportsByIDs,$OperatorCountry);



                     $tmpOperators['month_string']=$month;


                     $total_avg_t = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['tur'],$startColumnDateDisplay,$end_date);

                      $tmpOperators['tur']['dates']=$reportsColumnData['tur'];
                      $tmpOperators['tur']['total']=$total_avg_t['sum'];
                      $tmpOperators['tur']['t_mo_end']=$total_avg_t['T_Mo_End'];;
                      $tmpOperators['tur']['avg']=$total_avg_t['avg'];


                      $total_avg_rev = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['t_rev'],$startColumnDateDisplay,$end_date);


                      $tmpOperators['t_rev']['dates']=$reportsColumnData['t_rev'];
                      $tmpOperators['t_rev']['total']=$total_avg_rev['sum'];
                      $tmpOperators['t_rev']['t_mo_end']=$total_avg_rev['T_Mo_End'];;
                      $tmpOperators['t_rev']['avg']=$total_avg_rev['avg'];

                      $total_avg_trat = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['trat'],$startColumnDateDisplay,$end_date);


                      $tmpOperators['trat']['dates']=$reportsColumnData['trat'];
                      $tmpOperators['trat']['total']=$total_avg_trat['sum'];
                      $tmpOperators['trat']['t_mo_end']=$total_avg_trat['T_Mo_End'];
                      $tmpOperators['trat']['avg']=$total_avg_trat['avg'];

                      $total_avg_turt = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['turt'],$startColumnDateDisplay,$end_date);


                      $tmpOperators['turt']['dates']=$reportsColumnData['turt'];
                      $tmpOperators['turt']['total']=$total_avg_turt['sum'];
                      $tmpOperators['turt']['t_mo_end']=$total_avg_turt['T_Mo_End'];;
                      $tmpOperators['turt']['avg']=$total_avg_turt['avg'];;


                      $total_avg_t_sub = UtilityReports::calculateTotalSubscribe($operator,$reportsColumnData['t_sub'],$startColumnDateDisplay,$end_date);


                      $tmpOperators['t_sub']['dates']=$reportsColumnData['t_sub'];
                      $tmpOperators['t_sub']['total']=$total_avg_t_sub['sum'];
                      $tmpOperators['t_sub']['t_mo_end']=$total_avg_t_sub['T_Mo_End'];
                      $tmpOperators['t_sub']['avg']=$total_avg_t_sub['avg'];

                      $total_avg_t_reg = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['reg'],$startColumnDateDisplay,$end_date);


                      $tmpOperators['reg']['dates']=$reportsColumnData['reg'];
                      $tmpOperators['reg']['total']= $total_avg_t_reg['sum'];
                      $tmpOperators['reg']['t_mo_end']=$total_avg_t_reg['T_Mo_End'];
                      $tmpOperators['reg']['avg']=$total_avg_t_reg['avg'];

                      $total_avg_t_unreg = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['unreg'],$startColumnDateDisplay,$end_date);


                      $tmpOperators['unreg']['dates']=$reportsColumnData['unreg'];
                      $tmpOperators['unreg']['total']=$total_avg_t_unreg['sum'];
                      $tmpOperators['unreg']['t_mo_end']=$total_avg_t_unreg['T_Mo_End'];;
                      $tmpOperators['unreg']['avg']=$total_avg_t_unreg['avg'];

                      $total_avg_t_purged = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['purged'],$startColumnDateDisplay,$end_date);


                      $tmpOperators['purged']['dates']=$reportsColumnData['purged'];
                      $tmpOperators['purged']['total']=$total_avg_t_purged['sum'];
                      $tmpOperators['purged']['t_mo_end']=$total_avg_t_purged['T_Mo_End'];
                      $tmpOperators['purged']['avg']=$total_avg_t_purged['avg'];

                      $tmpOperators['churn']['dates']=$reportsColumnData['churn'];
                      $tmpOperators['churn']['total']=0;
                      $tmpOperators['churn']['t_mo_end']=0;
                      $tmpOperators['churn']['avg']=0;

                      $total_avg_t_renewal = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['renewal'],$startColumnDateDisplay,$end_date);


                      $tmpOperators['renewal']['dates']=$reportsColumnData['renewal'];
                      $tmpOperators['renewal']['total']=$total_avg_t_renewal['sum'];
                      $tmpOperators['renewal']['t_mo_end']=$total_avg_t_renewal['T_Mo_End'];
                      $tmpOperators['renewal']['avg']=$total_avg_t_renewal['avg'];


                      $tmpOperators['bill']['dates']=$reportsColumnData['bill'];
                      $tmpOperators['bill']['total']=0;
                      $tmpOperators['bill']['t_mo_end']=0;
                      $tmpOperators['bill']['avg']=0;


                      $tmpOperators['first_push']['dates']=$reportsColumnData['first_push'];
                      $tmpOperators['first_push']['total']=0;
                      $tmpOperators['first_push']['t_mo_end']=0;
                      $tmpOperators['first_push']['avg']=0;

                      $tmpOperators['daily_push']['dates']=$reportsColumnData['daily_push'];
                      $tmpOperators['daily_push']['total']=0;
                      $tmpOperators['daily_push']['t_mo_end']=0;
                      $tmpOperators['daily_push']['avg']=0;

                      $tmpOperators['arpu7']['dates']=$reportsColumnData['arpu7'];
                      $tmpOperators['arpu7']['total']=0;
                      $tmpOperators['arpu7']['t_mo_end']=0;
                      $tmpOperators['arpu7']['avg']=0;

                      $tmpOperators['usarpu7']['dates']=$reportsColumnData['usarpu7'];
                      $tmpOperators['usarpu7']['total']=0;
                      $tmpOperators['usarpu7']['t_mo_end']=0;
                      $tmpOperators['usarpu7']['avg']=0;

                      $tmpOperators['arpu30']['dates']=$reportsColumnData['arpu30'];
                      $tmpOperators['arpu30']['total']=0;
                      $tmpOperators['arpu30']['t_mo_end']=0;
                      $tmpOperators['arpu30']['avg']=0;

                      $tmpOperators['usarpu30']['dates']=$reportsColumnData['usarpu30'];
                      $tmpOperators['usarpu30']['total']=0;
                      $tmpOperators['usarpu30']['t_mo_end']=0;
                      $tmpOperators['usarpu30']['avg']=0;




                      $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators,"tur");
                      $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators,"turt");
                      $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators,"t_rev");
                      $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators,"trat");
                      $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators,"reg");
                      $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators,"unreg");
                      $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators,"purged");
                      $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators,"churn");
                      $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators,"renewal");
                      $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators,"bill");
                      $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators,"first_push");
                      $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators,"daily_push");
                      $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators,"arpu7");
                      $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators,"usarpu7");
                      $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators,"arpu30");
                      $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators,"usarpu30");



                     // dd($tmpOperators);




                    $sumemry[] =$tmpOperators;


                    }
                }



            $allsummaryData = UtilityReports::allsummaryData($sumemry);
// dd($sumemry);

        return view('report.index',compact('sumemry','no_of_days','companys','allsummaryData'));
    }

    function getReportsDateWise($operator,$no_of_days,$reportsByIDs,$OperatorCountry)
    {

        $usdValue = $OperatorCountry['usd'];

        if(!empty($no_of_days))
        {
            $allColumnData=array();
            $tur=array();

            $t_rev =array();

            $trat =array();
            $turt =array();


            $t_sub =array();
            $reg =array();
            $unreg =array();
            $purged =array();
            $churn =array();
            $renewal =array();

            $bill =array();
            $first_push =array();
            $daily_push =array();

            $arpu7 = array();
            $usarpu7 = array();
            $arpu30 = array();

            $usarpu30 = array();

           $id_operator =$operator->id_operator;



            foreach($no_of_days as $days)
            {
                $keys = $id_operator.".".$days['date'];

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

                $summariserow = Arr::get($reportsByIDs, $keys, 0);

               $gros_rev = isset($summariserow['gros_rev']) ? $summariserow['gros_rev'] : 0;
               $total_subscriber = isset($summariserow['total']) ? $summariserow['total'] : 0;
               $gros_rev_Usd =$gros_rev*$usdValue;

               $total_reg = isset($summariserow['total_reg']) ? $summariserow['total_reg'] : 0;

               $total_unreg = isset($summariserow['total_unreg']) ? $summariserow['total_unreg'] : 0;
               $purge_total = isset($summariserow['purge_total']) ? $summariserow['purge_total'] : 0;

               $mt_success = isset($summariserow['mt_success']) ? $summariserow['mt_success'] : 0;
               $mt_failed = isset($summariserow['mt_failed']) ? $summariserow['mt_failed'] : 0;
               $fmt_success = isset($summariserow['fmt_success']) ? $summariserow['fmt_success'] : 0;
               $fmt_failed = isset($summariserow['fmt_failed']) ? $summariserow['fmt_failed'] : 0;

               if($total_subscriber > 0)
               {
                $churn_value = (( (int)$total_reg - (int)$total_unreg + (int)$purge_total) / (int)$total_subscriber) * 100;

                $churn_value =sprintf('%0.2f', $churn_value);
               }
               else
               {
                $churn_value=0;

               }
               $renewal_total = $mt_success+$mt_failed;


               $billRate = UtilityReports::billRate($mt_success,$mt_failed,$total_subscriber);

               $billRate =sprintf('%0.2f', $billRate);

               $FirstPush = UtilityReports::FirstPush($fmt_success,$fmt_failed,$total_subscriber);
               $FirstPush =sprintf('%0.2f', $FirstPush);
               $Dailypush = UtilityReports::Dailypush($mt_success,$mt_failed,$total_subscriber);
               $Dailypush =sprintf('%0.2f', $Dailypush);


               // dd($discount);
                $tur[$days['date']]['value']=$gros_rev_Usd;
                $tur[$days['date']]['class']="";

                $t_rev[$days['date']]['value']=$gros_rev;
                $t_rev[$days['date']]['class']="bg-hui";

                $trat[$days['date']]['value']=0;
                $trat[$days['date']]['class']="bg-hui";

                $turt[$days['date']]['value']=0;
                $turt[$days['date']]['class']="bg-hui";



                $t_sub[$days['date']]['value']=$total_subscriber;
                $t_sub[$days['date']]['class']="bg-hui";



                $reg[$days['date']]['value']=$total_reg;
                $reg[$days['date']]['class']="bg-hui";




                $unreg[$days['date']]['value']=$total_unreg;
                $unreg[$days['date']]['class']="bg-hui";




                $purged[$days['date']]['value']=$purge_total;
                $purged[$days['date']]['class']="bg-hui";



                $churn[$days['date']]['value'] = $churn_value;
                $churn[$days['date']]['class']="bg-hui";



                $renewal[$days['date']]['value']=$renewal_total;
                $renewal[$days['date']]['class']="bg-hui";

                $bill[$days['date']]['value']=$billRate;
                $bill[$days['date']]['class']="bg-hui";

                $first_push[$days['date']]['value']=$FirstPush;
                $first_push[$days['date']]['class']="bg-hui";

                $daily_push[$days['date']]['value']=$Dailypush;
                $daily_push[$days['date']]['class']="bg-hui";

                $arpu7[$days['date']]['value']=0;
                $arpu7[$days['date']]['class']="bg-hui";

                $usarpu7[$days['date']]['value']=0;
                $usarpu7[$days['date']]['class']="bg-hui";

                $arpu30[$days['date']]['value']=0;
                $arpu30[$days['date']]['class']="bg-hui";

                $usarpu30[$days['date']]['value']=0;
                $usarpu30[$days['date']]['class']="bg-hui";






            }

            $allColumnData['tur'] = $tur;
            $allColumnData['t_rev'] = $t_rev;
            $allColumnData['trat'] = $trat;
            $allColumnData['turt'] = $turt;
            $allColumnData['t_sub'] = $t_sub;
            $allColumnData['reg'] = $reg;
            $allColumnData['unreg'] = $unreg;
            $allColumnData['purged'] = $purged;
            $allColumnData['churn'] = $churn;
            $allColumnData['renewal'] = $renewal;
            $allColumnData['bill'] = $bill;
            $allColumnData['first_push'] = $first_push;
            $allColumnData['daily_push'] = $daily_push;
            $allColumnData['arpu7'] = $arpu7;
            $allColumnData['usarpu7'] = $usarpu7;
            $allColumnData['arpu30'] = $arpu30;
            $allColumnData['usarpu30'] = $usarpu30;



           return $allColumnData;

        }
    }

    function getReportsOperatorID($reports)
    {

        if(!empty($reports))
        {
            $reportsResult=array();
            $tempreport=array();
            foreach($reports as $report)
            {
                $tempreport[$report['country_id']][$report['date']] = $report;
            }
            $reportsResult =  $tempreport;
            return $reportsResult;
        }

    }

    public function reportingdetails()
    {
        $companys = Company::get();
        return view('report.reportdetails', compact('companys'));
    }

    public function pnlsummary()
    {
        return view('report.pnlsummary');
    }

    public function pnldetail()
    {
        return view('report.pnldetails');
    }

    public function adnetreport()
    {
        return view('report.index');
    }
    public function country(Request $request)
    {
      if($request->id == 'allcompany'){
        $countrys=Country::select(['id AS country_id', 'country AS country_name'])->get()->toArray();
        $operator=Operator::get()->toArray();
        $data=['countrys'=>$countrys,'operators'=>$operator];
        return $data;
      }
        $countrys=[];
        $country_ids=[];
        $country_operator=[];
        $operators=CompanyOperators::GetOperator($request->id)->get();
        foreach($operators as $key=>$operator){
            $country=$operator->Operator;
            if(!in_array($country[0]->country_id,$country_ids))
                {
                    array_push($countrys,$country[0]);
                }
            array_push($country_ids,$country[0]->country_id);
            array_push($country_operator,$country[0]);
        }
        $data=['countrys'=>$countrys,'operators'=>$country_operator];
        return $data;
    }
    public function operator(Request $request)
    {
        // $countrys=[];
        // $country_ids=[];
        $operators=Operator::GetOperatorByCountryId($request->id)->get();
        // foreach($operators as $key=>$operator){
        //     $country=$operator->Operator;
        //     if(!in_array($country[0]->country_id,$country_ids))
        //         {
        //             array_push($countrys,$country[0]);
        //         }
        //     array_push($country_ids,$country[0]->country_id);
        //     // array_push($countrys,$country);
        // }
        return $operators;
    }

    public function service(Request $request)
    {
        $services=Service::GetserviceByOperatorId($request->id)->get();
        return $services;
    }

    public function DailyReportCountry()
    {
        $Country = Country::all();
        $companys = Company::get();
        $sumemry = array();
        if(!empty($Country))
        {
            foreach ($Country as $ckey=>$country) {

                // $static_operator = $country->operators->pluck('id_operator');
                // $countries = array();

                // if(!empty($Country))
                // {
                //     foreach($Country as $CountryI)
                //     {
                //         $countries[$CountryI['id']]=$CountryI;
                //     }
                // }

                // $contains = Arr::hasAny($Country, "2");

                $start_date = Carbon::now()->startOfMonth()->subDays(35)->format('Y-m-d');
                $startColumnDateDisplay = Carbon::now()->startOfMonth()->format('Y-m-d');
                $end_date = Carbon::now()->format('Y-m-d');
                $month = Carbon::now()->format('F Y');

                $static_operator =array(1,2,3,4);
                $reports = report_summarize::filteroperator($static_operator)
                      ->filterDateRange($start_date,$end_date)
                      ->orderBy('operator_id')
                      ->get()->toArray();
                      

                $reports = report_summarize::filterCountry($country->id)
                ->filterDateRange($start_date,$end_date)
                ->orderBy('country_id')
                // ->SumOfCountryData()
                ->get()->toArray();
                dd($reports);

                

                $reportsByIDs = $this->getReportsOperatorID($reports);
                dd($reportsByIDs);
                
                $datesIndividual =Utility::getRangeDates($startColumnDateDisplay,$end_date);
                $no_of_days = Utility::getRangeDateNo($datesIndividual);
            
                $Operators= Operator::GetOperatorByOperatorId($country->operators->pluck('id_operator'))->get();
       
                if(!empty($Operators))
                {
                    foreach($Operators as $key=>$operator)
                    {
                        $tmpOperators=array();
                        $tmpOperators['operator'] =$operator;
                        $country_id  =$operator->country_id;
                        $contain_id = Arr::exists($countries, $country_id);
                        $OperatorCountry = array();

                        if($contain_id )
                        {
                            $tmpOperators['country']=$countries[$country_id];
                            $OperatorCountry = $countries[$country_id];
                        }

                        $reportsColumnData = $this->getReportsDateWise($operator,$no_of_days,$reportsByIDs,$OperatorCountry);

                        $tmpOperators['month_string']=$month;

                        $total_avg_t = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['tur'],$startColumnDateDisplay,$end_date);

                        $tmpOperators['tur']['dates']=$reportsColumnData['tur'];
                        $tmpOperators['tur']['total']=$total_avg_t['sum'];
                        $tmpOperators['tur']['t_mo_end']=$total_avg_t['T_Mo_End'];;
                        $tmpOperators['tur']['avg']=$total_avg_t['avg'];

                        $total_avg_rev = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['t_rev'],$startColumnDateDisplay,$end_date);

                        $tmpOperators['t_rev']['dates']=$reportsColumnData['t_rev'];
                        $tmpOperators['t_rev']['total']=$total_avg_rev['sum'];
                        $tmpOperators['t_rev']['t_mo_end']=$total_avg_rev['T_Mo_End'];;
                        $tmpOperators['t_rev']['avg']=$total_avg_rev['avg'];

                        $total_avg_trat = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['trat'],$startColumnDateDisplay,$end_date);

                        $tmpOperators['trat']['dates']=$reportsColumnData['trat'];
                        $tmpOperators['trat']['total']=$total_avg_trat['sum'];
                        $tmpOperators['trat']['t_mo_end']=$total_avg_trat['T_Mo_End'];
                        $tmpOperators['trat']['avg']=$total_avg_trat['avg'];

                        $total_avg_turt = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['turt'],$startColumnDateDisplay,$end_date);

                        $tmpOperators['turt']['dates']=$reportsColumnData['turt'];
                        $tmpOperators['turt']['total']=$total_avg_turt['sum'];
                        $tmpOperators['turt']['t_mo_end']=$total_avg_turt['T_Mo_End'];;
                        $tmpOperators['turt']['avg']=$total_avg_turt['avg'];;

                        $total_avg_t_sub = UtilityReports::calculateTotalSubscribe($operator,$reportsColumnData['t_sub'],$startColumnDateDisplay,$end_date);

                        $tmpOperators['t_sub']['dates']=$reportsColumnData['t_sub'];
                        $tmpOperators['t_sub']['total']=$total_avg_t_sub['sum'];
                        $tmpOperators['t_sub']['t_mo_end']=$total_avg_t_sub['T_Mo_End'];
                        $tmpOperators['t_sub']['avg']=$total_avg_t_sub['avg'];

                        $total_avg_t_reg = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['reg'],$startColumnDateDisplay,$end_date);

                        $tmpOperators['reg']['dates']=$reportsColumnData['reg'];
                        $tmpOperators['reg']['total']= $total_avg_t_reg['sum'];
                        $tmpOperators['reg']['t_mo_end']=$total_avg_t_reg['T_Mo_End'];
                        $tmpOperators['reg']['avg']=$total_avg_t_reg['avg'];

                        $total_avg_t_unreg = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['unreg'],$startColumnDateDisplay,$end_date);

                        $tmpOperators['unreg']['dates']=$reportsColumnData['unreg'];
                        $tmpOperators['unreg']['total']=$total_avg_t_unreg['sum'];
                        $tmpOperators['unreg']['t_mo_end']=$total_avg_t_unreg['T_Mo_End'];;
                        $tmpOperators['unreg']['avg']=$total_avg_t_unreg['avg'];

                        $total_avg_t_purged = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['purged'],$startColumnDateDisplay,$end_date);

                        $tmpOperators['purged']['dates']=$reportsColumnData['purged'];
                        $tmpOperators['purged']['total']=$total_avg_t_purged['sum'];
                        $tmpOperators['purged']['t_mo_end']=$total_avg_t_purged['T_Mo_End'];
                        $tmpOperators['purged']['avg']=$total_avg_t_purged['avg'];

                        $tmpOperators['churn']['dates']=$reportsColumnData['churn'];
                        $tmpOperators['churn']['total']=0;
                        $tmpOperators['churn']['t_mo_end']=0;
                        $tmpOperators['churn']['avg']=0;

                        $total_avg_t_renewal = UtilityReports::calculateTotalAVG($operator,$reportsColumnData['renewal'],$startColumnDateDisplay,$end_date);

                        $tmpOperators['renewal']['dates']=$reportsColumnData['renewal'];
                        $tmpOperators['renewal']['total']=$total_avg_t_renewal['sum'];
                        $tmpOperators['renewal']['t_mo_end']=$total_avg_t_renewal['T_Mo_End'];
                        $tmpOperators['renewal']['avg']=$total_avg_t_renewal['avg'];

                        $tmpOperators['bill']['dates']=$reportsColumnData['bill'];
                        $tmpOperators['bill']['total']=0;
                        $tmpOperators['bill']['t_mo_end']=0;
                        $tmpOperators['bill']['avg']=0;

                        $tmpOperators['first_push']['dates']=$reportsColumnData['first_push'];
                        $tmpOperators['first_push']['total']=0;
                        $tmpOperators['first_push']['t_mo_end']=0;
                        $tmpOperators['first_push']['avg']=0;

                        $tmpOperators['daily_push']['dates']=$reportsColumnData['daily_push'];
                        $tmpOperators['daily_push']['total']=0;
                        $tmpOperators['daily_push']['t_mo_end']=0;
                        $tmpOperators['daily_push']['avg']=0;

                        $tmpOperators['arpu7']['dates']=$reportsColumnData['arpu7'];
                        $tmpOperators['arpu7']['total']=0;
                        $tmpOperators['arpu7']['t_mo_end']=0;
                        $tmpOperators['arpu7']['avg']=0;

                        $tmpOperators['usarpu7']['dates']=$reportsColumnData['usarpu7'];
                        $tmpOperators['usarpu7']['total']=0;
                        $tmpOperators['usarpu7']['t_mo_end']=0;
                        $tmpOperators['usarpu7']['avg']=0;

                        $tmpOperators['arpu30']['dates']=$reportsColumnData['arpu30'];
                        $tmpOperators['arpu30']['total']=0;
                        $tmpOperators['arpu30']['t_mo_end']=0;
                        $tmpOperators['arpu30']['avg']=0;

                        $tmpOperators['usarpu30']['dates']=$reportsColumnData['usarpu30'];
                        $tmpOperators['usarpu30']['total']=0;
                        $tmpOperators['usarpu30']['t_mo_end']=0;
                        $tmpOperators['usarpu30']['avg']=0;

                        $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators,"tur");
                        $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators,"turt");
                        $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators,"t_rev");
                        $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators,"trat");
                        $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators,"reg");
                        $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators,"unreg");
                        $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators,"purged");
                        $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators,"churn");
                        $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators,"renewal");
                        $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators,"bill");
                        $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators,"first_push");
                        $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators,"daily_push");
                        $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators,"arpu7");
                        $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators,"usarpu7");
                        $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators,"arpu30");
                        $tmpOperators = UtilityReports::ColorFirstDay($tmpOperators,"usarpu30");

                         // dd($tmpOperators);
                        $sumemry[$country->id][$key] =$tmpOperators;
                        // dd();
                       
                    }
                }

                if(sizeof($Operators) > 0)
                {
                     
                    $sumemry_datas = UtilityReports::allsummaryData($sumemry[$country->id]);
                    $sumemry_country['country'] = $country;
                    $sumemry_month['month_string'] = $month;
                    $sumemry_data[] = array_merge($sumemry_country,$sumemry_month,$sumemry_datas);
                }
                
            }
        }

        $sumemry = $sumemry_data;
        $allsummaryData = UtilityReports::allsummaryData($sumemry);
        // dd($sumemry);
        // dd($allsummaryData);
        return view('report.daily_country_report',compact('sumemry','no_of_days','companys','allsummaryData'));
    }

}
