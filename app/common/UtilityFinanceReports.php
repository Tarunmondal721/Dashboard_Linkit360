<?php

namespace App\common;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Promise;
use GuzzleHttp\Psr7;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Exception\ClientException;
use Carbon\Carbon;
use function PHPSTORM_META\type;
use App\Models\Country;
use App\Models\ServiceHistory;
use Illuminate\Support\Arr;

class UtilityFinanceReports
{
  public static function UsdCalCriteria($local_rev,$exchange_rate,$data,$country,$days,$type='daily')
  {
    $countryCode = $country['country_code'];

    $Gross_local_Revenue = $local_rev;

    // operator_id comes from ReportController
    // id_opeator comes from PNLReportController
    //$id_operator = isset($data['operator_id']) ? $data['operator_id'] : $data['id_operator'];

    /* Country Code Wise revenue Change */

    if($countryCode == "KW")
    {
      $Gross_local_Revenue = $local_rev / 1000 ;
    }
    
    /* Country  END  Code Wise revenue Change */

    $usdValue = $Gross_local_Revenue * $exchange_rate;

    return $usdValue;
  }

  /* revenue calculation for mobifone operator 29 */
  public static function getMobifoneRevenue($id_operator,$days,$type)
  {
    $summarydata = [];
    $sumGrossRev = 0;

    if($type == 'daily'){      
      $service_historys = ServiceHistory::FilterOperator($id_operator)->filterDate($days)->get();
    }else if($type == 'monthly')
    {
      $month = $days['date'];
      $service_historys = ServiceHistory::FilterOperator($id_operator)->filterMonth($month)->get();
    }
  
    if(isset($service_historys) && !empty($service_historys)){
      foreach ($service_historys as $service) {
        $mt_success = $service['mt_success'];
        $service_id = $service['id_service'];
        $gros_rev = $service['gros_rev'];

        if($service_id == 466){
          $gros_rev = $mt_success * 3000;
        }else if($service_id == 698){
          $gros_rev = $mt_success * 5000;
        }

        $sumGrossRev += $gros_rev;
      }

      return $sumGrossRev;
    }
  }

  /* revenue calculation for mobifone operator 29  for Days range */
  public static function getMobifoneRevDateRange($id_operator,$datesNotInclude,$month,$year)
  {
    $summarydata = [];
    $sumGrossRev = 0;
      
    $service_historysQuery = ServiceHistory::FilterOperator($id_operator)->Year($year)->Month($month);
             
    if(!empty($datesNotInclude))
    {
      $service_historysQuery = $service_historysQuery->NotDateInclude($datesNotInclude);
    }

    $service_historys = $service_historysQuery->get();
         
    if(isset($service_historys) && !empty($service_historys)){
      foreach ($service_historys as $service) {
        $mt_success = $service['mt_success'];
        $service_id = $service['id_service'];
        $gros_rev = $service['gros_rev'];
 
        if($service_id == 466){
          $gros_rev = $mt_success * 3000;
        }else if($service_id == 698){
          $gros_rev = $mt_success * 5000;
        }
 
        $sumGrossRev += $gros_rev;
      }
 
      return $sumGrossRev;
    }
  }

  public  static function trat($share,$gros_rev)
  {
    // G Rev Local Currency
    $merchent_share = $share['merchent_share'] / 100;

    $trat = $gros_rev * $merchent_share ;

    return $trat;
  }

  public  static function turt($share,$gros_rev_Usd)
  {
    // G Rev USD Converted  Currency
    $merchent_share = $share['merchent_share'] / 100;

    $turt = $gros_rev_Usd * $merchent_share ;

    return $turt;
  }

  public static function Arpu7($operator,$reportsByIDs,$days,$total_subscriber,$share)
  {
    $arpu = 0;

    $day = $days['date'];
    $merchent_share = $share['merchent_share'] / 100;
    
    $day_date = new Carbon($day);
    $last_date_cal = new Carbon($day);

    $first_day = $day_date->subDays(7)->format('Y-m-d');
    $last_day = $last_date_cal->subDays(1)->format('Y-m-d');

    $datesIndividual = Utility::getRangeDates($first_day,$last_day);

    $no_of_days = Utility::getRangeDateNo($datesIndividual);
    $id_operator = $operator->id_operator;

    $total_gros_rev = 0; // 7 days total revenue 
    $total_total_reg = 0;  // 7 days total reg 

    if(!empty($no_of_days))
    {
      foreach($no_of_days as $days)
      {
        $keys = $id_operator.".".$days['date'];

        $summariserow = Arr::get($reportsByIDs, $keys, 0);

        $gros_rev = isset($summariserow['gros_rev']) ? $summariserow['gros_rev'] : 0;

        $total_gros_rev =  $total_gros_rev +  $gros_rev;

        $total_reg = isset($summariserow['total_reg']) ? $summariserow['total_reg'] : 0;

        $total_total_reg  = $total_total_reg + $total_reg;
      }

      $R1 =  $total_gros_rev * $merchent_share ;
      $R2 = $total_total_reg;
      $R3 = $R2 + $total_subscriber ;

      if($R3 > 0)
      {
        $arpu = $R1 / $R3 ;
      }
    }

    return $arpu;
  }

  public static function Arpu30($operator,$reportsByIDs,$days,$total_subscriber,$share)
  {
    $arpu = 0;

    $day = $days['date'];
    $merchent_share = $share['merchent_share'] / 100;
      
    $day_date = new Carbon($day);
    $last_date_cal = new Carbon($day);

    $first_day = $day_date->subDays(30)->format('Y-m-d');
    $last_day = $last_date_cal->subDays(1)->format('Y-m-d');

    $datesIndividual = Utility::getRangeDates($first_day,$last_day);

    $no_of_days = Utility::getRangeDateNo($datesIndividual);
    $id_operator = $operator->id_operator;

    $total_gros_rev = 0;
    $total_total_reg = 0;

    if(!empty($no_of_days))
    {
      foreach($no_of_days as $days)
      {
        $keys = $id_operator.".".$days['date'];

        $summariserow = Arr::get($reportsByIDs, $keys, 0);

        $gros_rev = isset($summariserow['gros_rev']) ? $summariserow['gros_rev'] : 0;

        $total_gros_rev = $total_gros_rev +  $gros_rev;

        $total_reg = isset($summariserow['total_reg']) ? $summariserow['total_reg'] : 0;

        $total_total_reg = $total_total_reg + $total_reg;
      }

      $R1 =  $total_gros_rev * $merchent_share;
      $R2 = $total_total_reg;
      $R3 = $R2 + $total_subscriber ;

      if($R3 > 0)
      {
        $arpu = $R1 / $R3 ;
      }
    }

    return $arpu;
  }

  public static function billRate($mt_success,$mt_failed,$total_subscriber)
  {  
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
    }else
    {
      $billing_rate = ($mt_success/$sent)*100;
    }

    return $billing_rate;          
  }

  public static function Dailypush($mt_success,$mt_failed,$total_subscriber)
  {  
    $Dailypush_rate = 0;

    $sent = $mt_success + $mt_failed;

    if($sent == 0)
    {
      if($total_subscriber > 0)
      {
        $Dailypush_rate = ($mt_success/$total_subscriber)*100;         
      }
    }
    else if($mt_failed == 0)
    {
      if($total_subscriber > 0)
      {
        $Dailypush_rate = ($mt_success/$total_subscriber)*100;
      }
    }else
    {
      $Dailypush_rate = ($mt_success/$sent)*100;
    }

    return $Dailypush_rate;        
  }

  public static function FirstPush($fmt_success,$fmt_failed,$total_subscriber)
  {
    $firstPushRate = 0;

    $sent = $fmt_success + $fmt_failed;

    if($sent == 0)
    {
      if($total_subscriber > 0)
      { 
        $firstPushRate = ($fmt_success/$total_subscriber)*100;           
      }
    }
    else if($fmt_failed == 0)
    {
      if($total_subscriber > 0)
      {
        $firstPushRate = ($fmt_success/$total_subscriber)*100;
      }
    }else
    {
      $firstPushRate = ($fmt_success/$sent)*100;
    }

    return $firstPushRate;        
  }

  public static function ReamingDaysMonth()
  {
    $reaming_day = 0;
    $today = Carbon::now()->format('Y-m-d');
    $firstdayOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');

    $dayoftoday = (int)Carbon::now()->format('d');

    $totalDayofmonth = Carbon::now()->daysInMonth;

    $reamingday = $totalDayofmonth - $dayoftoday;

    return $reamingday;
  }

  public static function calculateTotalAVG($operator,$data,$start_date,$end_date)
  {   
    $result = array();
    $sum = 0;
    $avg = 0;
    $T_Mo_End = 0;
    $reaming_day = 0;
    $today = Carbon::now()->format('Y-m-d');

    $firstdayOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');

    $created = new Carbon($start_date);
    $created_format = $created->format('Y-m-d');

    $dayscount = ($created->diff($end_date));

    $noofDays = getDateDiff($start_date,$end_date);

    // if not select Date range 
    if($created_format == $firstdayOfMonth)
    {
      $reaming_day = Carbon::now()->daysInMonth;
      $reaming_day = $reaming_day-(count($data) - 1);
    }
    else
    {
      $reaming_day = $noofDays;
    }

    if(!empty($data))
    {
      $count = count($data)-1;

      if($today > $end_date)
      $count = count($data);

      foreach($data as $key => $value)
      {            
        if($today == $key)
        continue;

        $sum = $sum + $value['value'];             
      }
           
      if($count > 0 && $sum > 0)
      {
        $avg = $sum/$count;
      }

      //Total + average * remaining days

      if($count > 0)
      {
        $T_Mo_End = $sum+ ($avg * $reaming_day);

        if($today > $end_date)
        $T_Mo_End = $sum;
      }             
    }    

    $sum = sprintf('%0.2f', $sum);
    $avg = sprintf('%0.2f', $avg);
    $T_Mo_End = sprintf('%0.2f', $T_Mo_End);

    $result['sum'] = $sum;
    $result['avg'] = $avg;
    $result['T_Mo_End'] = $T_Mo_End;

    return $result;          
  }

  public static function calculateTotalSubscribe($operator,$data,$start_date,$end_date)
  {
    $result = array();
    $sum = 0;
    $avg = 0;
    $T_Mo_End = 0;
    $reaming_day = 0;
    $today = Carbon::now()->format('Y-m-d');
    $calculateDayforSubscription = Carbon::now()->subDays(1)->format('Y-m-d');

    $firstdayOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');
    $created = new Carbon($start_date);
    $created_format = $created->format('Y-m-d');
    $dayscount = ($created->diff($end_date));

    // if not select Date range 
    if($created_format == $firstdayOfMonth)
    {
      $reaming_day = Carbon::now()->daysInMonth;
      $reaming_day = $reaming_day-(count($data) - 1);
      // $reaming_day = count($data)-1;
    }
    else
    {
      $reaming_day = $dayscount->days;
      $reaming_day = 1;
    }
    
    if(!empty($data))
    {
      $count = count($data)-1;

      foreach($data as $key => $value)
      {
        if($today > $end_date)
        $calculateDayforSubscription = $end_date;
                        
        if($key == $calculateDayforSubscription)             
        {
          $sum = $value['value'];
        }              
      }
                    
      if($count > 0 && $sum > 0)
      {
        $avg = $sum/$count;
      }

      //Total + average * remaining days

      if($count > 0)
      {
        $T_Mo_End = $sum + ($avg * $reaming_day); 
      }             
    }    

    $sum = sprintf('%0.2f', $sum);
    $avg = sprintf('%0.2f', $avg);
    $T_Mo_End = sprintf('%0.2f', $T_Mo_End);

    $result['sum'] = $sum;
    $result['avg'] = $avg;
    $result['T_Mo_End'] = $T_Mo_End;           
  }

  /*
  ColorFirstDay method 
  Author : Matainja
  $opetaor ~ All operator array with all Details 
  $rowType ~ String Example tur ,Sub , Reg */

  public static function ColorFirstDay($operator,$rowType)
  {
    $today = Carbon::now()->format('Y-m-d');

    /* tur Row Color Set */
    $turRowData = $operator[$rowType]['dates'];
    $datesRebuild = array();

    if(!empty($turRowData))
    {
      $no_ofdays = count($turRowData);

      if($no_ofdays > 1)
      {
        $FirstDateOfcalculation = Carbon::now()->subDays(1)->format('Y-m-d');       
      }

      foreach($turRowData as $key => $value)
      {
        $currentdateValue = $value['value'];
        $class = "first-ffff-class";

        $datesRebuild[$key]['value'] = $currentdateValue;
        $datesRebuild[$key]['class'] = $class;

                
        if($today == $key)
        { 
        }        
        else
        {
          $class = "";
          $datePrevious = new Carbon($key);
          $PreviousDate = $datePrevious->subDays(1)->format('Y-m-d');

          $previousDateData = 0;

          if(isset($turRowData[$PreviousDate]['value']))
          {
            $previousDateData = $turRowData[$PreviousDate]['value'];
          }
             
          if($currentdateValue > $previousDateData)
          {
            $class = "text-success";

            if(isset($FirstDateOfcalculation) && $key == $FirstDateOfcalculation )
            {
              $class ="bg-success text-white";
            }

            $datesRebuild[$key]['value'] = $currentdateValue;
            $datesRebuild[$key]['class'] = $class;
          }
          else
          {
            $class = "text-danger";

            if(isset($FirstDateOfcalculation) && $key == $FirstDateOfcalculation )
            {
              $class = "bg-danger text-white";
            }

            $datesRebuild[$key]['value'] = $currentdateValue;
            $datesRebuild[$key]['class'] = $class;
          }
        }
      }  
            
      $operator[$rowType]['dates'] = $datesRebuild;
    }

    return $operator;
  }

  public static function allsummaryData($sumemry)
  {
    $tur_sum = $tur_arr = [];    
    $t_rev_sum = $t_rev_arr = [];    
    $trat_sum = $trat_arr = [];    
    $turt_sum = $turt_arr = [];    
    $t_sub_sum = $t_sub_arr = [];    
    $reg_sum = $reg_arr = [];    
    $unreg_sum = $unreg_arr = [];    
    $purged_sum = $purged_arr = [];    
    $churn_sum = $churn_arr = [];    
    $renewal_sum = $renewal_arr = [];
    $bill_sum = $bill_arr = [];
    $first_push_sum = $first_push_arr = [];    
    $daily_push_sum = $daily_push_arr = [];
    $arpu7_sum = $arpu7_arr = [];
    $usarpu7_sum = $usarpu7_arr = [];    
    $arpu30_sum = $arpu30_arr = [];    
    $usarpu30_sum = $usarpu30_arr = [];

    $tur_total = $tur_t_mo_end = $tur_avg = 0;    
    $t_rev_total = $t_rev_t_mo_end = $t_rev_avg = 0;    
    $trat_total = $trat_t_mo_end = $trat_avg = 0;    
    $turt_total = $turt_t_mo_end = $turt_avg = 0;    
    $t_sub_total = $t_sub_t_mo_end = $t_sub_avg = 0;    
    $reg_total = $reg_t_mo_end = $reg_avg = 0;    
    $unreg_total = $unreg_t_mo_end = $unreg_avg = 0;    
    $purged_total = $purged_t_mo_end = $purged_avg = 0;    
    $churn_total = $churn_t_mo_end = $churn_avg = 0; 
    $renewal_total = $renewal_t_mo_end = $renewal_avg = 0;
    $bill_total = $bill_t_mo_end = $bill_avg = 0;    
    $first_push_total = $first_push_t_mo_end = $first_push_avg = 0;
    $daily_push_total = $daily_push_t_mo_end = $daily_push_avg = 0;    
    $arpu7_total = $arpu7_t_mo_end = $arpu7_avg = 0;
    $usarpu7_total = $usarpu7_t_mo_end = $usarpu7_avg = 0; 
    $arpu30_total = $arpu30_t_mo_end = $arpu30_avg = 0;
    $usarpu30_total = $usarpu30_t_mo_end = $usarpu30_avg = 0;

    foreach ($sumemry as $sumemry_key => $sumemry_value)
    {
      $tur_total = $tur_total + (float)$sumemry_value['tur']['total'];
      $tur_t_mo_end = $tur_t_mo_end + (float)$sumemry_value['tur']['t_mo_end'];
      $tur_avg = $tur_avg + (float)$sumemry_value['tur']['avg'];

      $t_rev_total = $t_rev_total + (float)$sumemry_value['tur']['total']; // REV for all Country in USD
      $t_rev_t_mo_end = $t_rev_t_mo_end + (float)$sumemry_value['tur']['t_mo_end'];
      $t_rev_avg = $t_rev_avg + (float)$sumemry_value['tur']['avg'];

      $trat_total = $trat_total + (float)$sumemry_value['trat']['total'];
      $trat_t_mo_end = $trat_t_mo_end + (float)$sumemry_value['trat']['t_mo_end'];
      $trat_avg = $trat_avg + (float)$sumemry_value['trat']['avg'];

      $turt_total = $turt_total + (float)$sumemry_value['turt']['total'];
      $turt_t_mo_end = $turt_t_mo_end + (float)$sumemry_value['turt']['t_mo_end'];
      $turt_avg = $turt_avg + (float)$sumemry_value['turt']['avg'];

      $t_sub_total = $t_sub_total + (float)$sumemry_value['t_sub']['total'];
      $t_sub_t_mo_end = $t_sub_t_mo_end + (float)$sumemry_value['t_sub']['t_mo_end'];
      $t_sub_avg = $t_sub_avg + (float)$sumemry_value['t_sub']['avg'];

      $reg_total = $reg_total + (float)$sumemry_value['reg']['total'];
      $reg_t_mo_end = $reg_t_mo_end + (float)$sumemry_value['reg']['t_mo_end'];
      $reg_avg = $reg_avg + (float)$sumemry_value['reg']['avg'];

      $unreg_total = $unreg_total + (float)$sumemry_value['unreg']['total'];
      $unreg_t_mo_end = $unreg_t_mo_end + (float)$sumemry_value['unreg']['t_mo_end'];
      $unreg_avg = $unreg_avg + (float)$sumemry_value['unreg']['avg'];

      $purged_total = $purged_total + (float)$sumemry_value['purged']['total'];
      $purged_t_mo_end = $purged_t_mo_end + (float)$sumemry_value['purged']['t_mo_end'];
      $purged_avg = $purged_avg + (float)$sumemry_value['purged']['avg'];

      $churn_total = $churn_total + (float)$sumemry_value['churn']['total'];
      $churn_t_mo_end = $churn_t_mo_end + (float)$sumemry_value['churn']['t_mo_end'];
      $churn_avg = $churn_avg + (float)$sumemry_value['churn']['avg'];

      $renewal_total = $renewal_total + (float)$sumemry_value['renewal']['total'];
      $renewal_t_mo_end = $renewal_t_mo_end + (float)$sumemry_value['renewal']['t_mo_end'];
      $renewal_avg = $renewal_avg + (float)$sumemry_value['renewal']['avg'];

      $bill_total = $bill_total + (float)$sumemry_value['bill']['total'];
      $bill_t_mo_end = $bill_t_mo_end + (float)$sumemry_value['bill']['t_mo_end'];
      $bill_avg = $bill_avg + (float)$sumemry_value['bill']['avg'];

      $first_push_total = $first_push_total + (float)$sumemry_value['first_push']['total'];
      $first_push_t_mo_end = $first_push_t_mo_end + (float)$sumemry_value['first_push']['t_mo_end'];
      $first_push_avg = $first_push_avg + (float)$sumemry_value['first_push']['avg'];

      $daily_push_total = $daily_push_total + (float)$sumemry_value['daily_push']['total'];
      $daily_push_t_mo_end = $daily_push_t_mo_end + (float)$sumemry_value['daily_push']['t_mo_end'];
      $daily_push_avg = $daily_push_avg + (float)$sumemry_value['daily_push']['avg'];

      $arpu7_total = $arpu7_total + (float)$sumemry_value['arpu7']['total'];
      $arpu7_t_mo_end = $arpu7_t_mo_end + (float)$sumemry_value['arpu7']['t_mo_end'];
      $arpu7_avg = $arpu7_avg + (float)$sumemry_value['arpu7']['avg'];

      $usarpu7_total = $usarpu7_total + (float)$sumemry_value['usarpu7']['total'];
      $usarpu7_t_mo_end = $usarpu7_t_mo_end + (float)$sumemry_value['usarpu7']['t_mo_end'];
      $usarpu7_avg = $usarpu7_avg + (float)$sumemry_value['usarpu7']['avg'];

      $arpu30_total = $arpu30_total + (float)$sumemry_value['arpu30']['total'];
      $arpu30_t_mo_end = $arpu30_t_mo_end + (float)$sumemry_value['arpu30']['t_mo_end'];
      $arpu30_avg = $arpu30_avg + (float)$sumemry_value['arpu30']['avg'];

      $usarpu30_total = $usarpu30_total + (float)$sumemry_value['usarpu30']['total'];
      $usarpu30_t_mo_end = $usarpu30_t_mo_end + (float)$sumemry_value['usarpu30']['t_mo_end'];
      $usarpu30_avg = $usarpu30_avg + (float)$sumemry_value['usarpu30']['avg'];                 

      foreach ($sumemry_value['tur']['dates'] as $tur_key => $tur_value) {
        if($sumemry_key == 0){
          $tur_sum[$tur_key] = 0;
        }

        $tur_sum[$tur_key] = $tur_sum[$tur_key] + (float)$tur_value['value'];

        if(count($sumemry)-1 == $sumemry_key)
        {
          $tur_arr[$tur_key] = ['value' => $tur_sum[$tur_key], 'class' => $tur_value['class']];
        }
      }

      foreach ($sumemry_value['t_rev']['dates'] as $t_rev_key => $t_rev_value) {
        if($sumemry_key == 0){
          $t_rev_sum[$t_rev_key] = 0;
        }

        $t_rev_sum[$t_rev_key] = $t_rev_sum[$t_rev_key] + (float)$t_rev_value['value'];

        if(count($sumemry)-1 == $sumemry_key)
        {
          $t_rev_arr[$t_rev_key] = ['value' => $t_rev_sum[$t_rev_key], 'class' => $t_rev_value['class']];
        }
      }

      foreach ($sumemry_value['trat']['dates'] as $trat_key => $trat_value) {
        if($sumemry_key == 0){
          $trat_sum[$trat_key] = 0;
        }

        $trat_sum[$trat_key] = $trat_sum[$trat_key] + (float)$trat_value['value'];

        if(count($sumemry)-1 == $sumemry_key)
        {
          $trat_arr[$trat_key] = ['value' => $trat_sum[$trat_key], 'class' => $trat_value['class']];
        }
      }

      foreach ($sumemry_value['turt']['dates'] as $turt_key => $turt_value) {
        if($sumemry_key == 0){
          $turt_sum[$turt_key] = 0;
        }

        $turt_sum[$turt_key] = $turt_sum[$turt_key] + (float)$turt_value['value'];

        if(count($sumemry)-1 == $sumemry_key)
        {
          $turt_arr[$turt_key] = ['value' => $turt_sum[$turt_key], 'class' => $turt_value['class']];
        }
      }

      foreach ($sumemry_value['t_sub']['dates'] as $t_sub_key => $t_sub_value) {
        if($sumemry_key == 0){
          $t_sub_sum[$t_sub_key] = 0;
        }

        $t_sub_sum[$t_sub_key] = $t_sub_sum[$t_sub_key] + (float)$t_sub_value['value'];

        if(count($sumemry)-1 == $sumemry_key)
        {
          $t_sub_arr[$t_sub_key] = ['value' => $t_sub_sum[$t_sub_key], 'class' => $t_sub_value['class']];
        }
      }

      foreach ($sumemry_value['reg']['dates'] as $reg_key => $reg_value) {
        if($sumemry_key == 0){
          $reg_sum[$reg_key] = 0;
        }

        $reg_sum[$reg_key] = $reg_sum[$reg_key] + (float)$reg_value['value'];

        if(count($sumemry)-1 == $sumemry_key)
        {
          $reg_arr[$reg_key] = ['value' => $reg_sum[$reg_key], 'class' => $reg_value['class']];
        }
      }

      foreach ($sumemry_value['unreg']['dates'] as $unreg_key => $unreg_value) {
        if($sumemry_key == 0){
          $unreg_sum[$unreg_key] = 0;
        }

        $unreg_sum[$unreg_key] = $unreg_sum[$unreg_key] + (float)$unreg_value['value'];

        if(count($sumemry)-1 == $sumemry_key)
        {
          $unreg_arr[$unreg_key] = ['value' => $unreg_sum[$unreg_key], 'class' => $unreg_value['class']];
        }
      }                

      foreach ($sumemry_value['purged']['dates'] as $purged_key => $purged_value) {
        if($sumemry_key == 0){
          $purged_sum[$purged_key] = 0;
        }

        $purged_sum[$purged_key] = $purged_sum[$purged_key] + (float)$purged_value['value'];

        if(count($sumemry)-1 == $sumemry_key)
        {
          $purged_arr[$purged_key] = ['value' => $purged_sum[$purged_key], 'class' => $purged_value['class']];
        }
      } 

      foreach ($sumemry_value['churn']['dates'] as $churn_key => $churn_value) {
        if($sumemry_key == 0){
          $churn_sum[$churn_key] = 0;
        }

        $churn_sum[$churn_key] = $churn_sum[$churn_key] + (float)$churn_value['value'];

        if(count($sumemry)-1 == $sumemry_key)
        {
          $churn_arr[$churn_key] = ['value' => $churn_sum[$churn_key], 'class' => $churn_value['class']];
        }
      }

      foreach ($sumemry_value['renewal']['dates'] as $renewal_key => $renewal_value) {
        if($sumemry_key == 0){
          $renewal_sum[$renewal_key] = 0;
        }

        $renewal_sum[$renewal_key] = $renewal_sum[$renewal_key] + (float)$renewal_value['value'];

        if(count($sumemry)-1 == $sumemry_key)
        {
          $renewal_arr[$renewal_key] = ['value' => $renewal_sum[$renewal_key], 'class' => $renewal_value['class']];
        }
      }

      foreach ($sumemry_value['bill']['dates'] as $bill_key => $bill_value) {
        if($sumemry_key == 0){
          $bill_sum[$bill_key] = 0;
        }

        $bill_sum[$bill_key] = $bill_sum[$bill_key] + (float)$bill_value['value'];

        if(count($sumemry)-1 == $sumemry_key)
        {
          $bill_arr[$bill_key] = ['value' => $bill_sum[$bill_key], 'class' => $bill_value['class']];
        }
      }

      foreach ($sumemry_value['first_push']['dates'] as $first_push_key => $first_push_value) {
        if($sumemry_key == 0){
          $first_push_sum[$first_push_key] = 0;
        }

        $first_push_sum[$first_push_key] = $first_push_sum[$first_push_key] + (float)$first_push_value['value'];

        if(count($sumemry)-1 == $sumemry_key)
        {
          $first_push_arr[$first_push_key] = ['value' => $first_push_sum[$first_push_key], 'class' => $first_push_value['class']];
        }
      }

      foreach ($sumemry_value['daily_push']['dates'] as $daily_push_key => $daily_push_value) {
        if($sumemry_key == 0){
          $daily_push_sum[$daily_push_key] = 0;
        }

        $daily_push_sum[$daily_push_key] = $daily_push_sum[$daily_push_key] + (float)$daily_push_value['value'];

        if(count($sumemry)-1 == $sumemry_key)
        {
          $daily_push_arr[$daily_push_key] = ['value' => $daily_push_sum[$daily_push_key], 'class' => $daily_push_value['class']];
        }
      }

      foreach ($sumemry_value['arpu7']['dates'] as $arpu7_key => $arpu7_value) {
        if($sumemry_key == 0){
          $arpu7_sum[$arpu7_key] = 0;
        }

        $arpu7_sum[$arpu7_key] = $arpu7_sum[$arpu7_key] + (float)$arpu7_value['value'];

        if(count($sumemry)-1 == $sumemry_key)
        {
          $arpu7_arr[$arpu7_key] = ['value' => $arpu7_sum[$arpu7_key], 'class' => $arpu7_value['class']];
        }
      }

      foreach ($sumemry_value['usarpu7']['dates'] as $usarpu7_key => $usarpu7_value) {
        if($sumemry_key == 0){
          $usarpu7_sum[$usarpu7_key] = 0;
        }

        $usarpu7_sum[$usarpu7_key] = $usarpu7_sum[$usarpu7_key] + (float)$usarpu7_value['value'];

        if(count($sumemry)-1 == $sumemry_key)
        {
          $usarpu7_arr[$usarpu7_key] = ['value' => $usarpu7_sum[$usarpu7_key], 'class' => $usarpu7_value['class']];
        }
      }

      foreach ($sumemry_value['arpu30']['dates'] as $arpu30_key => $arpu30_value) {
        if($sumemry_key == 0){
          $arpu30_sum[$arpu30_key] = 0;
        }

        $arpu30_sum[$arpu30_key] = $arpu30_sum[$arpu30_key] + (float)$arpu30_value['value'];

        if(count($sumemry)-1 == $sumemry_key)
        {
          $arpu30_arr[$arpu30_key] = ['value' => $arpu30_sum[$arpu30_key], 'class' => $arpu30_value['class']];
        }
      }

      foreach ($sumemry_value['usarpu30']['dates'] as $usarpu30_key => $usarpu30_value) {
        if($sumemry_key == 0){
          $usarpu30_sum[$usarpu30_key] = 0;
        }

        $usarpu30_sum[$usarpu30_key] = $usarpu30_sum[$usarpu30_key] + (float)$usarpu30_value['value'];

        if(count($sumemry)-1 == $sumemry_key)
        {
          $usarpu30_arr[$usarpu30_key] = ['value' => $usarpu30_sum[$usarpu30_key], 'class' => $usarpu30_value['class']];
        }
      }
    }

    $dataArr['tur']['dates'] = $tur_arr;                
    $dataArr['tur']['total'] = $tur_total;                
    $dataArr['tur']['t_mo_end'] = $tur_t_mo_end;                
    $dataArr['tur']['avg'] = $tur_avg;

    $dataArr['t_rev']['dates'] = $t_rev_arr;
    $dataArr['t_rev']['total'] = $t_rev_total;                
    $dataArr['t_rev']['t_mo_end'] = $t_rev_t_mo_end;                
    $dataArr['t_rev']['avg'] = $t_rev_avg;

    $dataArr['trat']['dates'] = $trat_arr;
    $dataArr['trat']['total'] = $trat_total;                
    $dataArr['trat']['t_mo_end'] = $trat_t_mo_end;                
    $dataArr['trat']['avg'] = $trat_avg;

    $dataArr['turt']['dates'] = $turt_arr;
    $dataArr['turt']['total'] = $turt_total;                
    $dataArr['turt']['t_mo_end'] = $turt_t_mo_end;                
    $dataArr['turt']['avg'] = $turt_avg;

    $dataArr['t_sub']['dates'] = $t_sub_arr;
    $dataArr['t_sub']['total'] = $t_sub_total;                
    $dataArr['t_sub']['t_mo_end'] = $t_sub_t_mo_end;                
    $dataArr['t_sub']['avg'] = $t_sub_avg;

    $dataArr['reg']['dates'] = $reg_arr;
    $dataArr['reg']['total'] = $reg_total;                
    $dataArr['reg']['t_mo_end'] = $reg_t_mo_end;                
    $dataArr['reg']['avg'] = $reg_avg;

    $dataArr['unreg']['dates'] = $unreg_arr;
    $dataArr['unreg']['total'] = $unreg_total;                
    $dataArr['unreg']['t_mo_end'] = $unreg_t_mo_end;                
    $dataArr['unreg']['avg'] = $unreg_avg;

    $dataArr['purged']['dates'] = $purged_arr;
    $dataArr['purged']['total'] = $purged_total;                
    $dataArr['purged']['t_mo_end'] = $purged_t_mo_end;                
    $dataArr['purged']['avg'] = $purged_avg;

    $dataArr['churn']['dates'] = $churn_arr;
    $dataArr['churn']['total'] = $churn_total;                
    $dataArr['churn']['t_mo_end'] = $churn_t_mo_end;                
    $dataArr['churn']['avg'] = $churn_avg;

    $dataArr['renewal']['dates'] = $renewal_arr;
    $dataArr['renewal']['total'] = $renewal_total;                
    $dataArr['renewal']['t_mo_end'] = $renewal_t_mo_end;                
    $dataArr['renewal']['avg'] = $renewal_avg;

    $dataArr['bill']['dates'] = $bill_arr;
    $dataArr['bill']['total'] = $bill_total;                
    $dataArr['bill']['t_mo_end'] = $bill_t_mo_end;                
    $dataArr['bill']['avg'] = $bill_avg;    

    $dataArr['first_push']['dates'] = $first_push_arr;
    $dataArr['first_push']['total'] = $first_push_total;                
    $dataArr['first_push']['t_mo_end'] = $first_push_t_mo_end;                
    $dataArr['first_push']['avg'] = $first_push_avg;

    $dataArr['daily_push']['dates'] = $daily_push_arr;
    $dataArr['daily_push']['total'] = $daily_push_total;                
    $dataArr['daily_push']['t_mo_end'] = $daily_push_t_mo_end;                
    $dataArr['daily_push']['avg'] = $daily_push_avg;

    $dataArr['arpu7']['dates'] = $arpu7_arr;
    $dataArr['arpu7']['total'] = $arpu7_total;                
    $dataArr['arpu7']['t_mo_end'] = $arpu7_t_mo_end;                
    $dataArr['arpu7']['avg'] = $arpu7_avg;

    $dataArr['usarpu7']['dates'] = $usarpu7_arr;
    $dataArr['usarpu7']['total'] = $usarpu7_total;                
    $dataArr['usarpu7']['t_mo_end'] = $usarpu7_t_mo_end;                
    $dataArr['usarpu7']['avg'] = $usarpu7_avg;

    $dataArr['arpu30']['dates'] = $arpu30_arr;
    $dataArr['arpu30']['total'] = $arpu30_total;                
    $dataArr['arpu30']['t_mo_end'] = $arpu30_t_mo_end;                
    $dataArr['arpu30']['avg'] = $arpu30_avg;

    $dataArr['usarpu30']['dates'] = $usarpu30_arr;                
    $dataArr['usarpu30']['total'] = $usarpu30_total;                
    $dataArr['usarpu30']['t_mo_end'] = $usarpu30_t_mo_end;                
    $dataArr['usarpu30']['avg'] = $usarpu30_avg;

    return $dataArr;
  }

  public static function CountrySumOperator($sumemry)
  {
    $tur_sum = $tur_arr = [];    
    $t_rev_sum = $t_rev_arr = [];    
    $trat_sum = $trat_arr = [];    
    $turt_sum = $turt_arr = [];    
    $t_sub_sum = $t_sub_arr = [];    
    $reg_sum = $reg_arr = [];    
    $unreg_sum = $unreg_arr = [];    
    $purged_sum = $purged_arr = [];    
    $churn_sum = $churn_arr = [];    
    $renewal_sum = $renewal_arr = [];
    $bill_sum = $bill_arr = [];
    $first_push_sum = $first_push_arr = [];    
    $daily_push_sum = $daily_push_arr = [];
    $arpu7_sum = $arpu7_arr = [];
    $usarpu7_sum = $usarpu7_arr = [];    
    $arpu30_sum = $arpu30_arr = [];    
    $usarpu30_sum = $usarpu30_arr = [];

    $turSumTest = 0;

    $tur_total = $tur_t_mo_end = $tur_avg = 0;    
    $t_rev_total = $t_rev_t_mo_end = $t_rev_avg = 0;    
    $trat_total = $trat_t_mo_end = $trat_avg = 0;    
    $turt_total = $turt_t_mo_end = $turt_avg = 0;    
    $t_sub_total = $t_sub_t_mo_end = $t_sub_avg = 0;    
    $reg_total = $reg_t_mo_end = $reg_avg = 0;    
    $unreg_total = $unreg_t_mo_end = $unreg_avg = 0;    
    $purged_total = $purged_t_mo_end = $purged_avg = 0;    
    $churn_total = $churn_t_mo_end = $churn_avg = 0; 
    $renewal_total = $renewal_t_mo_end = $renewal_avg = 0;
    $bill_total = $bill_t_mo_end = $bill_avg = 0;    
    $first_push_total = $first_push_t_mo_end = $first_push_avg = 0;
    $daily_push_total = $daily_push_t_mo_end = $daily_push_avg = 0;    
    $arpu7_total = $arpu7_t_mo_end = $arpu7_avg = 0;
    $usarpu7_total = $usarpu7_t_mo_end = $usarpu7_avg = 0; 
    $arpu30_total = $arpu30_t_mo_end = $arpu30_avg = 0;
    $usarpu30_total = $usarpu30_t_mo_end = $usarpu30_avg = 0;

    foreach ($sumemry as $sumemry_key => $sumemry_value)
    {
      $country_id = $sumemry_value['country']['id'];

      $tur_total = $tur_total + (float)$sumemry_value['tur']['total'];
      $tur_t_mo_end = $tur_t_mo_end + (float)$sumemry_value['tur']['t_mo_end'];
      $tur_avg = $tur_avg + (float)$sumemry_value['tur']['avg'];

      $t_rev_total = $t_rev_total + (float)$sumemry_value['t_rev']['total'];
      $t_rev_t_mo_end = $t_rev_t_mo_end + (float)$sumemry_value['t_rev']['t_mo_end'];
      $t_rev_avg = $t_rev_avg + (float)$sumemry_value['t_rev']['avg'];

      $trat_total = $trat_total + (float)$sumemry_value['trat']['total'];
      $trat_t_mo_end = $trat_t_mo_end + (float)$sumemry_value['trat']['t_mo_end'];
      $trat_avg = $trat_avg + (float)$sumemry_value['trat']['avg'];

      $turt_total = $turt_total + (float)$sumemry_value['turt']['total'];
      $turt_t_mo_end = $turt_t_mo_end + (float)$sumemry_value['turt']['t_mo_end'];
      $turt_avg = $turt_avg + (float)$sumemry_value['turt']['avg'];

      $t_sub_total = $t_sub_total + (float)$sumemry_value['t_sub']['total'];
      $t_sub_t_mo_end = $t_sub_t_mo_end + (float)$sumemry_value['t_sub']['t_mo_end'];
      $t_sub_avg = $t_sub_avg + (float)$sumemry_value['t_sub']['avg'];

      $reg_total = $reg_total + (float)$sumemry_value['reg']['total'];
      $reg_t_mo_end = $reg_t_mo_end + (float)$sumemry_value['reg']['t_mo_end'];
      $reg_avg = $reg_avg + (float)$sumemry_value['reg']['avg'];

      $unreg_total = $unreg_total + (float)$sumemry_value['unreg']['total'];
      $unreg_t_mo_end = $unreg_t_mo_end + (float)$sumemry_value['unreg']['t_mo_end'];
      $unreg_avg = $unreg_avg + (float)$sumemry_value['unreg']['avg'];

      $purged_total = $purged_total + (float)$sumemry_value['purged']['total'];
      $purged_t_mo_end = $purged_t_mo_end + (float)$sumemry_value['purged']['t_mo_end'];
      $purged_avg = $purged_avg + (float)$sumemry_value['purged']['avg'];

      $churn_total = $churn_total + (float)$sumemry_value['churn']['total'];
      $churn_t_mo_end = $churn_t_mo_end + (float)$sumemry_value['churn']['t_mo_end'];
      $churn_avg = $churn_avg + (float)$sumemry_value['churn']['avg'];

      $renewal_total = $renewal_total + (float)$sumemry_value['renewal']['total'];
      $renewal_t_mo_end = $renewal_t_mo_end + (float)$sumemry_value['renewal']['t_mo_end'];
      $renewal_avg = $renewal_avg + (float)$sumemry_value['renewal']['avg'];

      $bill_total = $bill_total + (float)$sumemry_value['bill']['total'];
      $bill_t_mo_end = $bill_t_mo_end + (float)$sumemry_value['bill']['t_mo_end'];
      $bill_avg = $bill_avg + (float)$sumemry_value['bill']['avg'];

      $first_push_total = $first_push_total + (float)$sumemry_value['first_push']['total'];
      $first_push_t_mo_end = $first_push_t_mo_end + (float)$sumemry_value['first_push']['t_mo_end'];
      $first_push_avg = $first_push_avg + (float)$sumemry_value['first_push']['avg'];

      $daily_push_total = $daily_push_total + (float)$sumemry_value['daily_push']['total'];
      $daily_push_t_mo_end = $daily_push_t_mo_end + (float)$sumemry_value['daily_push']['t_mo_end'];
      $daily_push_avg = $daily_push_avg + (float)$sumemry_value['daily_push']['avg'];

      $arpu7_total = $arpu7_total + (float)$sumemry_value['arpu7']['total'];
      $arpu7_t_mo_end = $arpu7_t_mo_end + (float)$sumemry_value['arpu7']['t_mo_end'];
      $arpu7_avg = $arpu7_avg + (float)$sumemry_value['arpu7']['avg'];

      $usarpu7_total = $usarpu7_total + (float)$sumemry_value['usarpu7']['total'];
      $usarpu7_t_mo_end = $usarpu7_t_mo_end + (float)$sumemry_value['usarpu7']['t_mo_end'];
      $usarpu7_avg = $usarpu7_avg + (float)$sumemry_value['usarpu7']['avg'];

      $arpu30_total = $arpu30_total + (float)$sumemry_value['arpu30']['total'];
      $arpu30_t_mo_end = $arpu30_t_mo_end + (float)$sumemry_value['arpu30']['t_mo_end'];
      $arpu30_avg = $arpu30_avg + (float)$sumemry_value['arpu30']['avg'];

      $usarpu30_total = $usarpu30_total + (float)$sumemry_value['usarpu30']['total'];
      $usarpu30_t_mo_end = $usarpu30_t_mo_end + (float)$sumemry_value['usarpu30']['t_mo_end'];
      $usarpu30_avg = $usarpu30_avg + (float)$sumemry_value['usarpu30']['avg'];                 

      $t_rev_date_wise_sum = 0;

      if (!empty($sumemry_value['t_rev']['dates'])) 
      {
		    foreach ($sumemry_value['t_rev']['dates'] as $t_rev_key => $t_rev_value) 
		    {
          if(!isset($t_rev_arr[$t_rev_key]['value']))
          {
            $t_rev_arr[$t_rev_key]['value'] = 0;
          }

		      $t_rev_arr[$t_rev_key]['value'] =  $t_rev_arr[$t_rev_key]['value'] + $t_rev_value['value'] ;

		      $t_rev_arr[$t_rev_key]['class'] = "";
		    }
      }

      if (!empty($sumemry_value['tur']['dates'])) 
      {
        foreach ($sumemry_value['tur']['dates'] as $tur_key => $tur_value) 
        {
		      if(!isset($tur_arr[$tur_key]['value']))
          {
		        $tur_arr[$tur_key]['value'] = 0;
		      }

          $tur_arr[$tur_key]['value'] = $tur_arr[$tur_key]['value']+$tur_value['value'];

          $tur_arr[$tur_key]['class'] = "";
        }
      }

      if (!empty($sumemry_value['trat']['dates'])) 
      {
        foreach ($sumemry_value['trat']['dates'] as $trat_key => $trat_value) 
        {
		      if(!isset($trat_arr[$trat_key]['value']))
					{
		        $trat_arr[$trat_key]['value'] = 0;
		      }

          $trat_arr[$trat_key]['value'] = $trat_arr[$trat_key]['value']+$trat_value['value'];

          $trat_arr[$trat_key]['class'] = "";
        }
      }

      if (!empty($sumemry_value['turt']['dates'])) 
      {
        foreach ($sumemry_value['turt']['dates'] as $turt_key => $turt_value) 
        {
		      if(!isset($turt_arr[$turt_key]['value']))
					{
		        $turt_arr[$turt_key]['value'] = 0;
		      }

          $turt_arr[$turt_key]['value'] = $turt_arr[$turt_key]['value']+$turt_value['value'];

          $turt_arr[$turt_key]['class'] = "";
        }
      }

      if (!empty($sumemry_value['t_sub']['dates'])) 
      {
        foreach ($sumemry_value['t_sub']['dates'] as $t_sub_key => $t_sub_value) 
        {
		      if(!isset($t_sub_arr[$t_sub_key]['value']))
					{
		        $t_sub_arr[$t_sub_key]['value'] = 0;
		      }

          $t_sub_arr[$t_sub_key]['value'] = $t_sub_arr[$t_sub_key]['value']+$t_sub_value['value'];

          $t_sub_arr[$t_sub_key]['class'] = "";
        }
      }

      if (!empty($sumemry_value['reg']['dates'])) 
      {
        foreach ($sumemry_value['reg']['dates'] as $reg_key => $reg_value) 
        {
		      if(!isset($reg_arr[$reg_key]['value']))
					{
		        $reg_arr[$reg_key]['value'] = 0;
		      }

          $reg_arr[$reg_key]['value'] = $reg_arr[$reg_key]['value']+$reg_value['value'];

          $reg_arr[$reg_key]['class'] = "";
        }
      }
        
      if (!empty($sumemry_value['unreg']['dates'])) 
      {
        foreach ($sumemry_value['unreg']['dates'] as $unreg_key => $unreg_value) 
        {
		      if(!isset($unreg_arr[$unreg_key]['value']))
					{
		        $unreg_arr[$unreg_key]['value'] = 0;
		      }

          $unreg_arr[$unreg_key]['value'] = $unreg_arr[$unreg_key]['value']+$unreg_value['value'];

          $unreg_arr[$unreg_key]['class'] = "";
        }
      }

      if (!empty($sumemry_value['purged']['dates'])) 
      {
        foreach ($sumemry_value['purged']['dates'] as $purged_key => $purged_value)
        {
          if(!isset($purged_arr[$purged_key]['value']))
					{
            $purged_arr[$purged_key]['value'] = 0;
          }

          $purged_arr[$purged_key]['value'] = $purged_arr[$purged_key]['value']+$purged_value['value'];

          $purged_arr[$purged_key]['class'] = "";
        }
      }

      if (!empty($sumemry_value['churn']['dates'])) 
      {
        foreach ($sumemry_value['churn']['dates'] as $churn_key => $churn_value)
        {
          if(!isset($churn_arr[$churn_key]['value']))
          {
            $churn_arr[$churn_key]['value'] = 0;
          }

          $churn_arr[$churn_key]['value'] = $churn_arr[$churn_key]['value']+$churn_value['value'];

          $churn_arr[$churn_key]['class'] = "";
        }
      }

      if (!empty($sumemry_value['renewal']['dates'])) 
      {
        foreach ($sumemry_value['renewal']['dates'] as $renewal_key => $renewal_value)
        {
          if(!isset($renewal_arr[$renewal_key]['value']))
          {
            $renewal_arr[$renewal_key]['value'] = 0;
          }

          $renewal_arr[$renewal_key]['value'] = $renewal_arr[$renewal_key]['value']+$renewal_value['value'];

          $renewal_arr[$renewal_key]['class'] = "";
        }
      }

      if (!empty($sumemry_value['bill']['dates'])) 
      {
        foreach ($sumemry_value['bill']['dates'] as $bill_key => $bill_value)
        {
          if(!isset($bill_arr[$bill_key]['value']))
          {
            $bill_arr[$bill_key]['value'] = 0;
          }

          $bill_arr[$bill_key]['value'] = $bill_arr[$bill_key]['value']+$bill_value['value'];

          $bill_arr[$bill_key]['class'] = "";
        }
      }

      if (!empty($sumemry_value['first_push']['dates'])) 
      {
        foreach ($sumemry_value['first_push']['dates'] as $first_push_key => $first_push_value) 
        {
          if(!isset($first_push_arr[$first_push_key]['value']))
          {
            $first_push_arr[$first_push_key]['value'] = 0;
          }

          $first_push_arr[$first_push_key]['value'] = $first_push_arr[$first_push_key]['value']+$first_push_value['value'];

          $first_push_arr[$first_push_key]['class'] = "";
        }
      }

      if (!empty($sumemry_value['daily_push']['dates'])) 
      {
        foreach ($sumemry_value['daily_push']['dates'] as $daily_push_key => $daily_push_value)
        {
          if(!isset($daily_push_arr[$daily_push_key]['value']))
          {
            $daily_push_arr[$daily_push_key]['value'] = 0;
          }

          $daily_push_arr[$daily_push_key]['value'] = $daily_push_arr[$daily_push_key]['value']+$daily_push_value['value'];

          $daily_push_arr[$daily_push_key]['class'] = "";
        }
      }

      if (!empty($sumemry_value['arpu7']['dates'])) 
      {
        foreach ($sumemry_value['arpu7']['dates'] as $arpu7_key => $arpu7_value)
        {
          if(!isset($arpu7_arr[$arpu7_key]['value']))
          {
            $arpu7_arr[$arpu7_key]['value'] = 0;
          }
          $arpu7_arr[$arpu7_key]['value'] = $arpu7_arr[$arpu7_key]['value']+$arpu7_value['value'];

          $arpu7_arr[$arpu7_key]['class'] = "";
        }
      }

      if (!empty($sumemry_value['usarpu7']['dates'])) 
      {
        foreach ($sumemry_value['usarpu7']['dates'] as $usarpu7_key => $usarpu7_value)
        {
          if(!isset($usarpu7_arr[$usarpu7_key]['value']))
          {
            $usarpu7_arr[$usarpu7_key]['value'] = 0;
          }

          $usarpu7_arr[$usarpu7_key]['value'] = $usarpu7_arr[$usarpu7_key]['value']+$usarpu7_value['value'];

          $usarpu7_arr[$usarpu7_key]['class'] = "";
        }
      }
       
      if (!empty($sumemry_value['arpu30']['dates'])) 
      {
        foreach ($sumemry_value['arpu30']['dates'] as $arpu30 => $arpu30_value)
        {
          if(!isset($arpu30_arr[$arpu30]['value']))
          {
            $arpu30_arr[$arpu30]['value'] = 0;
          }
          $arpu30_arr[$arpu30]['value'] = $arpu30_arr[$arpu30]['value']+$arpu30_value['value'];

          $arpu30_arr[$arpu30]['class'] = "";
        }
      }

      if (!empty($sumemry_value['usarpu30']['dates'])) 
      {
        foreach ($sumemry_value['usarpu30']['dates'] as $usarpu30_key => $usarpu30_value)
        {
          if(!isset($usarpu30_arr[$usarpu30_key]['value']))
          {
            $usarpu30_arr[$usarpu30_key]['value'] = 0;
          }

          $usarpu30_arr[$usarpu30_key]['value'] = $usarpu30_arr[$usarpu30_key]['value']+$usarpu30_value['value'];

          $usarpu30_arr[$usarpu30_key]['class'] = "";
        }
      }
    }

    $dataArr['t_rev']['dates'] = $t_rev_arr;
    $dataArr['t_rev']['total'] = $t_rev_total;                
    $dataArr['t_rev']['t_mo_end'] = $t_rev_t_mo_end;                
    $dataArr['t_rev']['avg'] = $t_rev_avg;

    $dataArr['tur']['dates'] = $tur_arr;                
    $dataArr['tur']['total'] = $tur_total;                
    $dataArr['tur']['t_mo_end'] = $tur_t_mo_end;                
    $dataArr['tur']['avg'] = $tur_avg;

    $dataArr['trat']['dates'] = $trat_arr;
    $dataArr['trat']['total'] = $trat_total;                
    $dataArr['trat']['t_mo_end'] = $trat_t_mo_end;                
    $dataArr['trat']['avg'] = $trat_avg;

    $dataArr['turt']['dates'] = $turt_arr;
    $dataArr['turt']['total'] = $turt_total;                
    $dataArr['turt']['t_mo_end'] = $turt_t_mo_end;                
    $dataArr['turt']['avg'] = $turt_avg;

    $dataArr['t_sub']['dates'] = $t_sub_arr;
    $dataArr['t_sub']['total'] = $t_sub_total;                
    $dataArr['t_sub']['t_mo_end'] = $t_sub_t_mo_end;                
    $dataArr['t_sub']['avg'] = $t_sub_avg;

    $dataArr['reg']['dates'] = $reg_arr;
    $dataArr['reg']['total'] = $reg_total;                
    $dataArr['reg']['t_mo_end'] = $reg_t_mo_end;                
    $dataArr['reg']['avg'] = $reg_avg;

    $dataArr['unreg']['dates'] = $unreg_arr;
    $dataArr['unreg']['total'] = $unreg_total;                
    $dataArr['unreg']['t_mo_end'] = $unreg_t_mo_end;                
    $dataArr['unreg']['avg'] = $unreg_avg;

    $dataArr['purged']['dates'] = $purged_arr;
    $dataArr['purged']['total'] = $purged_total;                
    $dataArr['purged']['t_mo_end'] = $purged_t_mo_end;                
    $dataArr['purged']['avg'] = $purged_avg;

    $dataArr['churn']['dates'] = $churn_arr;
    $dataArr['churn']['total'] = $churn_total;                
    $dataArr['churn']['t_mo_end'] = $churn_t_mo_end;                
    $dataArr['churn']['avg'] = $churn_avg;

    $dataArr['renewal']['dates'] = $renewal_arr;
    $dataArr['renewal']['total'] = $renewal_total;                
    $dataArr['renewal']['t_mo_end'] = $renewal_t_mo_end;                
    $dataArr['renewal']['avg'] = $renewal_avg;

    $dataArr['bill']['dates'] = $bill_arr;
    $dataArr['bill']['total'] = $bill_total;                
    $dataArr['bill']['t_mo_end'] = $bill_t_mo_end;                
    $dataArr['bill']['avg'] = $bill_avg;

    $dataArr['first_push']['dates'] = $first_push_arr;
    $dataArr['first_push']['total'] = $first_push_total;                
    $dataArr['first_push']['t_mo_end'] = $first_push_t_mo_end;                
    $dataArr['first_push']['avg'] = $first_push_avg;

    $dataArr['daily_push']['dates'] = $daily_push_arr;
    $dataArr['daily_push']['total'] = $daily_push_total;                
    $dataArr['daily_push']['t_mo_end'] = $daily_push_t_mo_end;                
    $dataArr['daily_push']['avg'] = $daily_push_avg;

    $dataArr['arpu7']['dates'] = $arpu7_arr;
    $dataArr['arpu7']['total'] = $arpu7_total;                
    $dataArr['arpu7']['t_mo_end'] = $arpu7_t_mo_end;                
    $dataArr['arpu7']['avg'] = $arpu7_avg;

    $dataArr['usarpu7']['dates'] = $usarpu7_arr;
    $dataArr['usarpu7']['total'] = $usarpu7_total;                
    $dataArr['usarpu7']['t_mo_end'] = $usarpu7_t_mo_end;                
    $dataArr['usarpu7']['avg'] = $usarpu7_avg;

    $dataArr['arpu30']['dates'] = $arpu30_arr;
    $dataArr['arpu30']['total'] = $arpu30_total;                
    $dataArr['arpu30']['t_mo_end'] = $arpu30_t_mo_end;                
    $dataArr['arpu30']['avg'] = $arpu30_avg;

    $dataArr['usarpu30']['dates'] = $usarpu30_arr;                
    $dataArr['usarpu30']['total'] = $usarpu30_total;                
    $dataArr['usarpu30']['t_mo_end'] = $usarpu30_t_mo_end;                
    $dataArr['usarpu30']['avg'] = $usarpu30_avg;

    return $dataArr;
  }

  public static function monthly_all_summary_data($sumemry)
  {
    $tur_sum = $tur_arr = [];    
    $t_rev_sum = $t_rev_arr = [];    
    $trat_sum = $trat_arr = [];    
    $turt_sum = $turt_arr = [];    
    $t_sub_sum = $t_sub_arr = [];    
    $reg_sum = $reg_arr = [];    
    $unreg_sum = $unreg_arr = [];    
    $purged_sum = $purged_arr = [];    
    $churn_sum = $churn_arr = [];    
    $renewal_sum = $renewal_arr = [];
    $bill_sum = $bill_arr = [];
    $first_push_sum = $first_push_arr = [];    
    $daily_push_sum = $daily_push_arr = [];
    $arpu7_sum = $arpu7_arr = [];
    $usarpu7_sum = $usarpu7_arr = [];    
    $arpu30_sum = $arpu30_arr = [];    
    $usarpu30_sum = $usarpu30_arr = [];

    $tur_total = $tur_t_mo_end = $tur_avg = 0;    
    $t_rev_total = $t_rev_t_mo_end = $t_rev_avg = 0;    
    $trat_total = $trat_t_mo_end = $trat_avg = 0;    
    $turt_total = $turt_t_mo_end = $turt_avg = 0;    
    $t_sub_total = $t_sub_t_mo_end = $t_sub_avg = 0;    
    $reg_total = $reg_t_mo_end = $reg_avg = 0;    
    $unreg_total = $unreg_t_mo_end = $unreg_avg = 0;    
    $purged_total = $purged_t_mo_end = $purged_avg = 0;    
    $churn_total = $churn_t_mo_end = $churn_avg = 0; 
    $renewal_total = $renewal_t_mo_end = $renewal_avg = 0;
    $bill_total = $bill_t_mo_end = $bill_avg = 0;    
    $first_push_total = $first_push_t_mo_end = $first_push_avg = 0;
    $daily_push_total = $daily_push_t_mo_end = $daily_push_avg = 0;    
    $arpu7_total = $arpu7_t_mo_end = $arpu7_avg = 0;
    $usarpu7_total = $usarpu7_t_mo_end = $usarpu7_avg = 0; 
    $arpu30_total = $arpu30_t_mo_end = $arpu30_avg = 0;
    $usarpu30_total = $usarpu30_t_mo_end = $usarpu30_avg = 0;

    foreach ($sumemry as $sumemry_key => $sumemry_value)
    {
      $tur_total = $tur_total + (float)$sumemry_value['tur']['total'];
      $tur_t_mo_end = $tur_t_mo_end + (float)$sumemry_value['tur']['t_mo_end'];
      $tur_avg = $tur_avg + (float)$sumemry_value['tur']['avg'];

      $t_rev_total = $t_rev_total + (float)$sumemry_value['t_rev']['total'];
      $t_rev_t_mo_end = $t_rev_t_mo_end + (float)$sumemry_value['t_rev']['t_mo_end'];
      $t_rev_avg = $t_rev_avg + (float)$sumemry_value['t_rev']['avg'];

      $trat_total = $trat_total + (float)$sumemry_value['trat']['total'];
      $trat_t_mo_end = $trat_t_mo_end + (float)$sumemry_value['trat']['t_mo_end'];
      $trat_avg = $trat_avg + (float)$sumemry_value['trat']['avg'];

      $turt_total = $turt_total + (float)$sumemry_value['turt']['total'];
      $turt_t_mo_end = $turt_t_mo_end + (float)$sumemry_value['turt']['t_mo_end'];
      $turt_avg = $turt_avg + (float)$sumemry_value['turt']['avg'];

      $t_sub_total = $t_sub_total + (float)$sumemry_value['t_sub']['total'];
      $t_sub_t_mo_end = $t_sub_t_mo_end + (float)$sumemry_value['t_sub']['t_mo_end'];
      $t_sub_avg = $t_sub_avg + (float)$sumemry_value['t_sub']['avg'];

      $reg_total = $reg_total + (float)$sumemry_value['reg']['total'];
      $reg_t_mo_end = $reg_t_mo_end + (float)$sumemry_value['reg']['t_mo_end'];
      $reg_avg = $reg_avg + (float)$sumemry_value['reg']['avg'];

      $unreg_total = $unreg_total + (float)$sumemry_value['unreg']['total'];
      $unreg_t_mo_end = $unreg_t_mo_end + (float)$sumemry_value['unreg']['t_mo_end'];
      $unreg_avg = $unreg_avg + (float)$sumemry_value['unreg']['avg'];

      $purged_total = $purged_total + (float)$sumemry_value['purged']['total'];
      $purged_t_mo_end = $purged_t_mo_end + (float)$sumemry_value['purged']['t_mo_end'];
      $purged_avg = $purged_avg + (float)$sumemry_value['purged']['avg'];

      $churn_total = $churn_total + (float)$sumemry_value['churn']['total'];
      $churn_t_mo_end = $churn_t_mo_end + (float)$sumemry_value['churn']['t_mo_end'];
      $churn_avg = $churn_avg + (float)$sumemry_value['churn']['avg'];

      $renewal_total = $renewal_total + (float)$sumemry_value['renewal']['total'];
      $renewal_t_mo_end = $renewal_t_mo_end + (float)$sumemry_value['renewal']['t_mo_end'];
      $renewal_avg = $renewal_avg + (float)$sumemry_value['renewal']['avg'];

      $bill_total = $bill_total + (float)$sumemry_value['bill']['total'];
      $bill_t_mo_end = $bill_t_mo_end + (float)$sumemry_value['bill']['t_mo_end'];
      $bill_avg = $bill_avg + (float)$sumemry_value['bill']['avg'];

      $first_push_total = $first_push_total + (float)$sumemry_value['first_push']['total'];
      $first_push_t_mo_end = $first_push_t_mo_end + (float)$sumemry_value['first_push']['t_mo_end'];
      $first_push_avg = $first_push_avg + (float)$sumemry_value['first_push']['avg'];

      $daily_push_total = $daily_push_total + (float)$sumemry_value['daily_push']['total'];
      $daily_push_t_mo_end = $daily_push_t_mo_end + (float)$sumemry_value['daily_push']['t_mo_end'];
      $daily_push_avg = $daily_push_avg + (float)$sumemry_value['daily_push']['avg'];

      $arpu7_total = $arpu7_total + (float)$sumemry_value['arpu7']['total'];
      $arpu7_t_mo_end = $arpu7_t_mo_end + (float)$sumemry_value['arpu7']['t_mo_end'];
      $arpu7_avg = $arpu7_avg + (float)$sumemry_value['arpu7']['avg'];

      $usarpu7_total = $usarpu7_total + (float)$sumemry_value['usarpu7']['total'];
      $usarpu7_t_mo_end = $usarpu7_t_mo_end + (float)$sumemry_value['usarpu7']['t_mo_end'];
      $usarpu7_avg = $usarpu7_avg + (float)$sumemry_value['usarpu7']['avg'];

      $arpu30_total = $arpu30_total + (float)$sumemry_value['arpu30']['total'];
      $arpu30_t_mo_end = $arpu30_t_mo_end + (float)$sumemry_value['arpu30']['t_mo_end'];
      $arpu30_avg = $arpu30_avg + (float)$sumemry_value['arpu30']['avg'];

      $usarpu30_total = $usarpu30_total + (float)$sumemry_value['usarpu30']['total'];
      $usarpu30_t_mo_end = $usarpu30_t_mo_end + (float)$sumemry_value['usarpu30']['t_mo_end'];
      $usarpu30_avg = $usarpu30_avg + (float)$sumemry_value['usarpu30']['avg'];                 

      foreach ($sumemry_value['tur']['dates'] as $tur_key => $tur_value) {
        if($sumemry_key == 0){
          $tur_sum[$tur_key] = 0;
        }

        $tur_arr[$tur_key] = ['value' => (float)($tur_sum[$tur_key]+(float)$tur_value['value']) , 'class' => $tur_value['class']];
      }

      foreach ($sumemry_value['t_rev']['dates'] as $t_rev_key => $t_rev_value) {
        if($sumemry_key == 0){
          $t_rev_sum[$t_rev_key] = 0;
        }

        $t_rev_arr[$t_rev_key] = ['value' => (float)($t_rev_sum[$t_rev_key]+(float)$t_rev_value['value']) , 'class' => $t_rev_value['class']];
      }

      foreach ($sumemry_value['trat']['dates'] as $trat_key => $trat_value) {
        if($sumemry_key == 0){
          $trat_sum[$trat_key] = 0;
        }

        $trat_arr[$trat_key] = ['value' => (float)($trat_sum[$trat_key]+(float)$trat_value['value']) , 'class' => $trat_value['class']];
      }

      foreach ($sumemry_value['turt']['dates'] as $turt_key => $turt_value) {
        if($sumemry_key == 0){
          $turt_sum[$turt_key] = 0;
        }

        $turt_arr[$turt_key] = ['value' => ($turt_sum[$turt_key]+$turt_value['value']) , 'class' => $turt_value['class']];
      }

      foreach ($sumemry_value['t_sub']['dates'] as $t_sub_key => $t_sub_value) {
        if($sumemry_key == 0){
          $t_sub_sum[$t_sub_key] = 0;
        }

        $t_sub_arr[$t_sub_key] = ['value' => (float)($t_sub_sum[$t_sub_key]+(float)$t_sub_value['value']) , 'class' => $t_sub_value['class']];
      }

      foreach ($sumemry_value['reg']['dates'] as $reg_key => $reg_value) {
        if($sumemry_key == 0){
          $reg_sum[$reg_key] = 0;
        }

        $reg_arr[$reg_key] = ['value' => (float)($reg_sum[$reg_key]+(float)$reg_value['value']) , 'class' => $reg_value['class']];
      }

      foreach ($sumemry_value['unreg']['dates'] as $unreg_key => $unreg_value) {
        if($sumemry_key == 0){
          $unreg_sum[$unreg_key] = 0;
        }

        $unreg_arr[$unreg_key] = ['value' => (float)($unreg_sum[$unreg_key]+(float)$unreg_value['value']) , 'class' => $unreg_value['class']];
      }                

      foreach ($sumemry_value['purged']['dates'] as $purged_key => $purged_value) {
        if($sumemry_key == 0){
          $purged_sum[$purged_key] = 0;
        }

        $purged_arr[$purged_key] = ['value' => (float)($purged_sum[$purged_key]+(float)$purged_value['value']) , 'class' => $purged_value['class']];
      }

      foreach ($sumemry_value['churn']['dates'] as $churn_key => $churn_value) {
        if($sumemry_key == 0){
          $churn_sum[$churn_key] = 0;
        }

        $churn_arr[$churn_key] = ['value' => (float)($churn_sum[$churn_key]+(float)$churn_value['value']) , 'class' => $churn_value['class']];
      }

      foreach ($sumemry_value['renewal']['dates'] as $renewal_key => $renewal_value) {
        if($sumemry_key == 0){
          $renewal_sum[$renewal_key] = 0;
        }

        $renewal_arr[$renewal_key] = ['value' => (float)($renewal_sum[$renewal_key]+(float)$renewal_value['value']) , 'class' => $renewal_value['class']];
      }

      foreach ($sumemry_value['bill']['dates'] as $bill_key => $bill_value) {
        if($sumemry_key == 0){
          $bill_sum[$bill_key] = 0;
        }

        $bill_arr[$bill_key] = ['value' => (float)($bill_sum[$bill_key]+(float)$bill_value['value']) , 'class' => $bill_value['class']];
      }

      foreach ($sumemry_value['first_push']['dates'] as $first_push_key => $first_push_value) {
        if($sumemry_key == 0){
          $first_push_sum[$first_push_key] = 0;
        }

        $first_push_arr[$first_push_key] = ['value' => (float)($first_push_sum[$first_push_key]+(float)$first_push_value['value']) , 'class' => $first_push_value['class']];
      }

      foreach ($sumemry_value['daily_push']['dates'] as $daily_push_key => $daily_push_value) {
        if($sumemry_key == 0){
          $daily_push_sum[$daily_push_key] = 0;
        }

        $daily_push_arr[$daily_push_key] = ['value' => (float)($daily_push_sum[$daily_push_key]+(float)$daily_push_value['value']) , 'class' => $daily_push_value['class']];
      }

      foreach ($sumemry_value['arpu7']['dates'] as $arpu7_key => $arpu7_value) {
        if($sumemry_key == 0){
          $arpu7_sum[$arpu7_key] = 0;
        }

        $arpu7_arr[$arpu7_key] = ['value' => (float)($arpu7_sum[$arpu7_key]+(float)$arpu7_value['value']) , 'class' => $arpu7_value['class']];
      }

      foreach ($sumemry_value['usarpu7']['dates'] as $usarpu7_key => $usarpu7_value) {
        if($sumemry_key == 0){
          $usarpu7_sum[$usarpu7_key] = 0;
        }

        $usarpu7_arr[$usarpu7_key] = ['value' => (float)($usarpu7_sum[$usarpu7_key]+(float)$usarpu7_value['value']) , 'class' => $usarpu7_value['class']];
      }

      foreach ($sumemry_value['arpu30']['dates'] as $arpu30_key => $arpu30_value) {
        if($sumemry_key == 0){
          $arpu30_sum[$arpu30_key] = 0;
        }

        $arpu30_arr[$arpu30_key] = ['value' => (float)($arpu30_sum[$arpu30_key]+(float)$arpu30_value['value']) , 'class' => $arpu30_value['class']];
      }

      foreach ($sumemry_value['usarpu30']['dates'] as $usarpu30_key => $usarpu30_value) {
        if($sumemry_key == 0){
          $usarpu30_sum[$usarpu30_key] = 0;
        }

        $usarpu30_arr[$usarpu30_key] = ['value' => (float)($usarpu30_sum[$usarpu30_key]+(float)$usarpu30_value['value']) , 'class' => $usarpu30_value['class']];
      }
    }

    $dataArr['tur']['dates'] = $tur_arr;                
    $dataArr['tur']['total'] = $tur_total;                
    $dataArr['tur']['t_mo_end'] = $tur_t_mo_end;                
    $dataArr['tur']['avg'] = $tur_avg;

    $dataArr['t_rev']['dates'] = $t_rev_arr;
    $dataArr['t_rev']['total'] = $t_rev_total;                
    $dataArr['t_rev']['t_mo_end'] = $t_rev_t_mo_end;                
    $dataArr['t_rev']['avg'] = $t_rev_avg;

    $dataArr['trat']['dates'] = $trat_arr;
    $dataArr['trat']['total'] = $trat_total;                
    $dataArr['trat']['t_mo_end'] = $trat_t_mo_end;                
    $dataArr['trat']['avg'] = $trat_avg;

    $dataArr['turt']['dates'] = $turt_arr;
    $dataArr['turt']['total'] = $turt_total;                
    $dataArr['turt']['t_mo_end'] = $turt_t_mo_end;                
    $dataArr['turt']['avg'] = $turt_avg;

    $dataArr['t_sub']['dates'] = $t_sub_arr;
    $dataArr['t_sub']['total'] = $t_sub_total;                
    $dataArr['t_sub']['t_mo_end'] = $t_sub_t_mo_end;                
    $dataArr['t_sub']['avg'] = $t_sub_avg;

    $dataArr['reg']['dates'] = $reg_arr;
    $dataArr['reg']['total'] = $reg_total;                
    $dataArr['reg']['t_mo_end'] = $reg_t_mo_end;                
    $dataArr['reg']['avg'] = $reg_avg;

    $dataArr['unreg']['dates'] = $unreg_arr;
    $dataArr['unreg']['total'] = $unreg_total;                
    $dataArr['unreg']['t_mo_end'] = $unreg_t_mo_end;                
    $dataArr['unreg']['avg'] = $unreg_avg;

    $dataArr['purged']['dates'] = $purged_arr;
    $dataArr['purged']['total'] = $purged_total;                
    $dataArr['purged']['t_mo_end'] = $purged_t_mo_end;                
    $dataArr['purged']['avg'] = $purged_avg;

    $dataArr['churn']['dates'] = $churn_arr;
    $dataArr['churn']['total'] = $churn_total;                
    $dataArr['churn']['t_mo_end'] = $churn_t_mo_end;                
    $dataArr['churn']['avg'] = $churn_avg;

    $dataArr['renewal']['dates'] = $renewal_arr;
    $dataArr['renewal']['total'] = $renewal_total;                
    $dataArr['renewal']['t_mo_end'] = $renewal_t_mo_end;                
    $dataArr['renewal']['avg'] = $renewal_avg;

    $dataArr['bill']['dates'] = $bill_arr;
    $dataArr['bill']['total'] = $bill_total;                
    $dataArr['bill']['t_mo_end'] = $bill_t_mo_end;                
    $dataArr['bill']['avg'] = $bill_avg;    

    $dataArr['first_push']['dates'] = $first_push_arr;
    $dataArr['first_push']['total'] = $first_push_total;                
    $dataArr['first_push']['t_mo_end'] = $first_push_t_mo_end;                
    $dataArr['first_push']['avg'] = $first_push_avg;

    $dataArr['daily_push']['dates'] = $daily_push_arr;
    $dataArr['daily_push']['total'] = $daily_push_total;                
    $dataArr['daily_push']['t_mo_end'] = $daily_push_t_mo_end;                
    $dataArr['daily_push']['avg'] = $daily_push_avg;

    $dataArr['arpu7']['dates'] = $arpu7_arr;
    $dataArr['arpu7']['total'] = $arpu7_total;                
    $dataArr['arpu7']['t_mo_end'] = $arpu7_t_mo_end;                
    $dataArr['arpu7']['avg'] = $arpu7_avg;

    $dataArr['usarpu7']['dates'] = $usarpu7_arr;
    $dataArr['usarpu7']['total'] = $usarpu7_total;                
    $dataArr['usarpu7']['t_mo_end'] = $usarpu7_t_mo_end;                
    $dataArr['usarpu7']['avg'] = $usarpu7_avg;

    $dataArr['arpu30']['dates'] = $arpu30_arr;
    $dataArr['arpu30']['total'] = $arpu30_total;                
    $dataArr['arpu30']['t_mo_end'] = $arpu30_t_mo_end;                
    $dataArr['arpu30']['avg'] = $arpu30_avg;

    $dataArr['usarpu30']['dates'] = $usarpu30_arr;                
    $dataArr['usarpu30']['total'] = $usarpu30_total;                
    $dataArr['usarpu30']['t_mo_end'] = $usarpu30_t_mo_end;                
    $dataArr['usarpu30']['avg'] = $usarpu30_avg;

    return $dataArr;
  }

  public static function summaryDataSum($sumemry)
  {
    if(!empty($sumemry))
    {
      $dataArr = [];
      $end_user_rev_usd_arr = [];
      $end_user_rev_arr = [];
      $gros_rev_usd_arr = [];
      $gros_rev_arr = [];
      $cost_campaign_arr = [];
      $other_cost_arr = [];
      $hosting_cost_arr = [];
      $content_arr = [];
      $rnd_arr = [];
      $bd_arr = [];
      $platform_arr = [];
      $pnl_arr = [];

      $end_user_rev_usd_total = $end_user_rev_usd_t_mo_end = $end_user_rev_usd_avg = 0;
      $end_user_rev_total = $end_user_rev_t_mo_end = $end_user_rev_avg = 0;
      $gros_rev_usd_total = $gros_rev_usd_t_mo_end = $gros_rev_usd_avg = 0;
      $gros_rev_total = $gros_rev_t_mo_end = $gros_rev_avg = 0;
      $cost_campaign_total = $cost_campaign_t_mo_end = $cost_campaign_avg = 0;
      $other_cost_total = $other_cost_t_mo_end = $other_cost_avg = 0;
      $hosting_cost_total = $hosting_cost_t_mo_end = $hosting_cost_avg = 0;
      $content_total = $content_t_mo_end = $content_avg = 0;
      $rnd_total = $rnd_t_mo_end = $rnd_avg = 0;
      $bd_total = $bd_t_mo_end = $bd_avg = 0;
      $platform_total = $platform_t_mo_end = $platform_avg = 0;
      $pnl_total = $pnl_t_mo_end = $pnl_avg = 0;

      foreach ($sumemry as $sumemry_key => $sumemry_value)
      {
        $end_user_rev_usd_total = $end_user_rev_usd_total + (float)$sumemry_value['end_user_rev_usd']['total'];
        $end_user_rev_usd_t_mo_end = $end_user_rev_usd_t_mo_end + (float)$sumemry_value['end_user_rev_usd']['t_mo_end'];
        $end_user_rev_usd_avg = $end_user_rev_usd_avg + (float)$sumemry_value['end_user_rev_usd']['avg'];

        $end_user_rev_total = $end_user_rev_total + (float)$sumemry_value['end_user_rev']['total'];
        $end_user_rev_t_mo_end = $end_user_rev_t_mo_end + (float)$sumemry_value['end_user_rev']['t_mo_end'];
        $end_user_rev_avg = $end_user_rev_avg + (float)$sumemry_value['end_user_rev']['avg'];

        $gros_rev_usd_total = $gros_rev_usd_total + (float)$sumemry_value['gros_rev_usd']['total'];
        $gros_rev_usd_t_mo_end = $gros_rev_usd_t_mo_end + (float)$sumemry_value['gros_rev_usd']['t_mo_end'];
        $gros_rev_usd_avg = $gros_rev_usd_avg + (float)$sumemry_value['gros_rev_usd']['avg'];

        $gros_rev_total = $gros_rev_total + (float)$sumemry_value['gros_rev']['total'];
        $gros_rev_t_mo_end = $gros_rev_t_mo_end + (float)$sumemry_value['gros_rev']['t_mo_end'];
        $gros_rev_avg = $gros_rev_avg + (float)$sumemry_value['gros_rev']['avg'];

        $cost_campaign_total = $cost_campaign_total + (float)$sumemry_value['cost_campaign']['total'];
        $cost_campaign_t_mo_end = $cost_campaign_t_mo_end + (float)$sumemry_value['cost_campaign']['t_mo_end'];
        $cost_campaign_avg = $cost_campaign_avg + (float)$sumemry_value['cost_campaign']['avg'];

        $other_cost_total = $other_cost_total + (float)$sumemry_value['other_cost']['total'];
        $other_cost_t_mo_end = $other_cost_t_mo_end + (float)$sumemry_value['other_cost']['t_mo_end'];
        $other_cost_avg = $other_cost_avg + (float)$sumemry_value['other_cost']['avg'];

        $hosting_cost_total = $hosting_cost_total + (float)$sumemry_value['hosting_cost']['total'];
        $hosting_cost_t_mo_end = $hosting_cost_t_mo_end + (float)$sumemry_value['hosting_cost']['t_mo_end'];
        $hosting_cost_avg = $hosting_cost_avg + (float)$sumemry_value['hosting_cost']['avg'];

        $content_total = $content_total + (float)$sumemry_value['content']['total'];
        $content_t_mo_end = $content_t_mo_end + (float)$sumemry_value['content']['t_mo_end'];
        $content_avg = $content_avg + (float)$sumemry_value['content']['avg'];

        $rnd_total = $rnd_total + (float)$sumemry_value['rnd']['total'];
        $rnd_t_mo_end = $rnd_t_mo_end + (float)$sumemry_value['rnd']['t_mo_end'];
        $rnd_avg = $rnd_avg + (float)$sumemry_value['rnd']['avg'];

        $bd_total = $bd_total + (float)$sumemry_value['bd']['total'];
        $bd_t_mo_end = $bd_t_mo_end + (float)$sumemry_value['bd']['t_mo_end'];
        $bd_avg = $bd_avg + (float)$sumemry_value['bd']['avg'];

        $platform_total = $platform_total + (float)$sumemry_value['platform']['total'];
        $platform_t_mo_end = $platform_t_mo_end + (float)$sumemry_value['platform']['t_mo_end'];
        $platform_avg = $platform_avg + (float)$sumemry_value['platform']['avg'];

        $pnl_total = $pnl_total + (float)$sumemry_value['pnl']['total'];
        $pnl_t_mo_end = $pnl_t_mo_end + (float)$sumemry_value['pnl']['t_mo_end'];
        $pnl_avg = $pnl_avg + (float)$sumemry_value['pnl']['avg'];

        foreach ($sumemry_value['end_user_rev_usd']['dates'] as $end_user_rev_usd_key => $end_user_rev_usd_value) {
          if($sumemry_key == 0){
            $end_user_rev_usd_sum[$end_user_rev_usd_key] = 0;
          }
            
          $end_user_rev_usd_sum[$end_user_rev_usd_key] = $end_user_rev_usd_sum[$end_user_rev_usd_key] + (float)$end_user_rev_usd_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $end_user_rev_usd_arr[$end_user_rev_usd_key] = ['value' => $end_user_rev_usd_sum[$end_user_rev_usd_key], 'class' => $end_user_rev_usd_value['class']];
          }
        }

        foreach ($sumemry_value['end_user_rev']['dates'] as $end_user_rev_key => $end_user_rev_value) {
          if($sumemry_key == 0){
            $end_user_rev_sum[$end_user_rev_key] = 0;
          }
          
          $end_user_rev_sum[$end_user_rev_key] = $end_user_rev_sum[$end_user_rev_key] + (float)$end_user_rev_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $end_user_rev_arr[$end_user_rev_key] = ['value' => $end_user_rev_sum[$end_user_rev_key], 'class' => $end_user_rev_value['class']];
          }
        }

        foreach ($sumemry_value['gros_rev_usd']['dates'] as $gros_rev_usd_key => $gros_rev_usd_value) {
          if($sumemry_key == 0){
            $gros_rev_usd_sum[$gros_rev_usd_key] = 0;
          }
           
          $gros_rev_usd_sum[$gros_rev_usd_key] = $gros_rev_usd_sum[$gros_rev_usd_key] + (float)$gros_rev_usd_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $gros_rev_usd_arr[$gros_rev_usd_key] = ['value' => $gros_rev_usd_sum[$gros_rev_usd_key], 'class' => $gros_rev_usd_value['class']];
          }
        }

        foreach ($sumemry_value['gros_rev']['dates'] as $gros_rev_key => $gros_rev_value) {
          if($sumemry_key == 0){
            $gros_rev_sum[$gros_rev_key] = 0;
          }
          
          $gros_rev_sum[$gros_rev_key] = $gros_rev_sum[$gros_rev_key] + (float)$gros_rev_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $gros_rev_arr[$gros_rev_key] = ['value' => $gros_rev_sum[$gros_rev_key], 'class' => $gros_rev_value['class']];
          }
        }

        foreach ($sumemry_value['cost_campaign']['dates'] as $cost_campaign_key => $cost_campaign_value) {
          if($sumemry_key == 0){
            $cost_campaign_sum[$cost_campaign_key] = 0;
          }
          
          $cost_campaign_sum[$cost_campaign_key] = $cost_campaign_sum[$cost_campaign_key] + (float)$cost_campaign_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $cost_campaign_arr[$cost_campaign_key] = ['value' => $cost_campaign_sum[$cost_campaign_key], 'class' => $cost_campaign_value['class']];
          }
        }

        foreach ($sumemry_value['other_cost']['dates'] as $other_cost_key => $other_cost_value) {
          if($sumemry_key == 0){
            $other_cost_sum[$other_cost_key] = 0;
          }
          
          $other_cost_sum[$other_cost_key] = $other_cost_sum[$other_cost_key] + (float)$other_cost_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $other_cost_arr[$other_cost_key] = ['value' => $other_cost_sum[$other_cost_key], 'class' => $other_cost_value['class']];
          }
        }

        foreach ($sumemry_value['hosting_cost']['dates'] as $hosting_cost_key => $hosting_cost_value) {
          if($sumemry_key == 0){
            $hosting_cost_sum[$hosting_cost_key] = 0;
          }
          
          $hosting_cost_sum[$hosting_cost_key] = $hosting_cost_sum[$hosting_cost_key] + (float)$hosting_cost_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $hosting_cost_arr[$hosting_cost_key] = ['value' => $hosting_cost_sum[$hosting_cost_key], 'class' => $hosting_cost_value['class']];
          }
        }

        foreach ($sumemry_value['content']['dates'] as $content_key => $content_value) {
          if($sumemry_key == 0){
            $content_sum[$content_key] = 0;
          }
          
          $content_sum[$content_key] = $content_sum[$content_key] + (float)$content_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $content_arr[$content_key] = ['value' => $content_sum[$content_key], 'class' => $content_value['class']];
          }
        }

        foreach ($sumemry_value['rnd']['dates'] as $rnd_key => $rnd_value) {
          if($sumemry_key == 0){
            $rnd_sum[$rnd_key] = 0;
          }
          
          $rnd_sum[$rnd_key] = $rnd_sum[$rnd_key] + (float)$rnd_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $rnd_arr[$rnd_key] = ['value' => $rnd_sum[$rnd_key], 'class' => $rnd_value['class']];
          }
        }

        foreach ($sumemry_value['bd']['dates'] as $bd_key => $bd_value) {
          if($sumemry_key == 0){
            $bd_sum[$bd_key] = 0;
          }
          
          $bd_sum[$bd_key] = $bd_sum[$bd_key] + (float)$bd_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $bd_arr[$bd_key] = ['value' => $bd_sum[$bd_key], 'class' => $bd_value['class']];
          }
        }

        foreach ($sumemry_value['platform']['dates'] as $platform_key => $platform_value) {
          if($sumemry_key == 0){
            $platform_sum[$platform_key] = 0;
          }
          
          $platform_sum[$platform_key] = $platform_sum[$platform_key] + (float)$platform_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $platform_arr[$platform_key] = ['value' => $platform_sum[$platform_key], 'class' => $platform_value['class']];
          }
        }

        foreach ($sumemry_value['pnl']['dates'] as $pnl_key => $pnl_value) {
          if($sumemry_key == 0){
            $pnl_sum[$pnl_key] = 0;
          }
          
          $pnl_sum[$pnl_key] = $pnl_sum[$pnl_key] + (float)$pnl_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $pnl_arr[$pnl_key] = ['value' => $pnl_sum[$pnl_key], 'class' => $pnl_value['class']];
          }
        }
      }

      $dataArr['end_user_rev_usd']['dates'] = $end_user_rev_usd_arr;                
      $dataArr['end_user_rev_usd']['total'] = $end_user_rev_usd_total;                
      $dataArr['end_user_rev_usd']['t_mo_end'] = $end_user_rev_usd_t_mo_end;
      $dataArr['end_user_rev_usd']['avg'] = $end_user_rev_usd_avg;

      $dataArr['end_user_rev']['dates'] = $end_user_rev_arr;                
      $dataArr['end_user_rev']['total'] = $end_user_rev_total;                
      $dataArr['end_user_rev']['t_mo_end'] = $end_user_rev_t_mo_end;
      $dataArr['end_user_rev']['avg'] = $end_user_rev_avg;

      $dataArr['gros_rev_usd']['dates'] = $gros_rev_usd_arr;                
      $dataArr['gros_rev_usd']['total'] = $gros_rev_usd_total;                
      $dataArr['gros_rev_usd']['t_mo_end'] = $gros_rev_usd_t_mo_end;
      $dataArr['gros_rev_usd']['avg'] = $gros_rev_usd_avg;

      $dataArr['gros_rev']['dates'] = $gros_rev_arr;                
      $dataArr['gros_rev']['total'] = $gros_rev_total;                
      $dataArr['gros_rev']['t_mo_end'] = $gros_rev_t_mo_end;
      $dataArr['gros_rev']['avg'] = $gros_rev_avg;

      $dataArr['cost_campaign']['dates'] = $cost_campaign_arr;                
      $dataArr['cost_campaign']['total'] = $cost_campaign_total;                
      $dataArr['cost_campaign']['t_mo_end'] = $cost_campaign_t_mo_end;
      $dataArr['cost_campaign']['avg'] = $cost_campaign_avg;

      $dataArr['other_cost']['dates'] = $other_cost_arr;                
      $dataArr['other_cost']['total'] = $other_cost_total;                
      $dataArr['other_cost']['t_mo_end'] = $other_cost_t_mo_end;
      $dataArr['other_cost']['avg'] = $other_cost_avg;

      $dataArr['hosting_cost']['dates'] = $hosting_cost_arr;                
      $dataArr['hosting_cost']['total'] = $hosting_cost_total;                
      $dataArr['hosting_cost']['t_mo_end'] = $hosting_cost_t_mo_end;
      $dataArr['hosting_cost']['avg'] = $hosting_cost_avg;

      $dataArr['content']['dates'] = $content_arr;                
      $dataArr['content']['total'] = $content_total;                
      $dataArr['content']['t_mo_end'] = $content_t_mo_end;
      $dataArr['content']['avg'] = $content_avg;

      $dataArr['rnd']['dates'] = $rnd_arr;                
      $dataArr['rnd']['total'] = $rnd_total;                
      $dataArr['rnd']['t_mo_end'] = $rnd_t_mo_end;
      $dataArr['rnd']['avg'] = $rnd_avg;

      $dataArr['bd']['dates'] = $bd_arr;                
      $dataArr['bd']['total'] = $bd_total;                
      $dataArr['bd']['t_mo_end'] = $bd_t_mo_end;
      $dataArr['bd']['avg'] = $bd_avg;

      $dataArr['platform']['dates'] = $platform_arr;                
      $dataArr['platform']['total'] = $platform_total;                
      $dataArr['platform']['t_mo_end'] = $platform_t_mo_end;
      $dataArr['platform']['avg'] = $platform_avg;

      $dataArr['pnl']['dates'] = $pnl_arr;                
      $dataArr['pnl']['total'] = $pnl_total;                
      $dataArr['pnl']['t_mo_end'] = $pnl_t_mo_end;
      $dataArr['pnl']['avg'] = $pnl_avg;

      return $dataArr;
    }     
  }

  public static function sumOfCountryReconcileData($sumemryData)
  {
    $dataArr = [];
    $dlr_arr = [];
    $fir_arr = [];
    $discrepency_arr = [];
    $dlr_after_telco_arr = [];
    $fir_after_telco_arr = [];
    $discrepency_after_telco_arr = [];
    $net_rev_arr = [];
    $fir_net_rev_arr = [];
    $discrepency_net_rev_arr = [];

    $dlr_total = $dlr_after_telco_total = 0;
    $fir_total = $fir_after_telco_total = 0;
    $net_rev_total = $fir_net_rev_total = 0;
    $discrepency_total = $discrepency_after_telco_total = $discrepency_net_rev_total = 0;

    if(!empty($sumemryData))
    {
      foreach ($sumemryData as $sumemry_key => $sumemry)
      {
        $dlr_total = $dlr_total + (float)$sumemry['dlr']['total'];
        $dlr_after_telco_total = $dlr_after_telco_total + (float)$sumemry['dlr_after_telco']['total'];

        $fir_total = $fir_total + (float)$sumemry['fir']['total'];
        $fir_after_telco_total = $fir_after_telco_total + (float)$sumemry['fir_after_telco']['total'];

        $net_rev_total = $net_rev_total + (float)$sumemry['net_revenue']['total'];
        $fir_net_rev_total = $fir_net_rev_total + (float)$sumemry['fir_net_revenue']['total'];
        
        $discrepency_total = ($dlr_total != 0 && $fir_total != 0) ? (($fir_total - $dlr_total)/$dlr_total)*100 : (float)0;

        $discrepency_after_telco_total = ($dlr_after_telco_total != 0 && $fir_after_telco_total != 0) ? (($fir_after_telco_total - $dlr_after_telco_total)/$dlr_after_telco_total)*100 : (float)0;
        
        $discrepency_net_rev_total = ($net_rev_total != 0 && $fir_net_rev_total != 0) ? (($fir_net_rev_total - $net_rev_total)/$net_rev_total)*100 : (float)0;

        foreach ($sumemry['dlr']['dates'] as $dlr_key => $dlr_value)
        {
          if($sumemry_key == 0){
            $dlr_sum[$dlr_key] = 0;
          }

          $dlr_sum[$dlr_key] = $dlr_sum[$dlr_key] + (float)$dlr_value['value'];

          if(count($sumemryData)-1 == $sumemry_key)
          {
            $dlr_arr[$dlr_key] = ['value' => $dlr_sum[$dlr_key], 'class' => $dlr_value['class']];
          }
        }

        foreach ($sumemry['fir']['dates'] as $fir_key => $fir_value)
        {
          if($sumemry_key == 0){
            $fir_sum[$fir_key] = 0;
          }

          $fir_sum[$fir_key] = $fir_sum[$fir_key] + (float)$fir_value['value'];

          if(count($sumemryData)-1 == $sumemry_key)
          {
            $fir_arr[$fir_key] = ['value' => $fir_sum[$fir_key], 'class' => $fir_value['class']];
          }
        }

        foreach ($sumemry['discrepency']['dates'] as $discrepency_key => $discrepency_value) {
          if($sumemry_key == 0){
            $discrepency_sum[$discrepency_key] = 0;
          }

          $discrepency_sum[$discrepency_key] = ($dlr_sum[$discrepency_key] != 0 && $fir_sum[$discrepency_key] != 0) ? (($fir_sum[$discrepency_key] - $dlr_sum[$discrepency_key])/$dlr_sum[$discrepency_key])*100 : (float)0;

          if($discrepency_sum[$discrepency_key] < 10){
            $discrepency_class[$discrepency_key] = "text-success";
          }else if($discrepency_sum[$discrepency_key] > 10 && $discrepency_sum[$discrepency_key] < 50){
            $discrepency_class[$discrepency_key] = "text-warning";
          }else if($discrepency_sum[$discrepency_key] > 50){
            $discrepency_class[$discrepency_key] = "text-danger";
          }else{
            $discrepency_class[$discrepency_key] = "text-success";
          }

          if(count($sumemryData)-1 == $sumemry_key)
          {
            $discrepency_arr[$discrepency_key] = ['value' => $discrepency_sum[$discrepency_key], 'class' => $discrepency_class[$discrepency_key]];
          }
        }

        foreach ($sumemry['dlr_after_telco']['dates'] as $dlr_after_telco_key => $dlr_after_telco_value) {
          if($sumemry_key == 0){
            $dlr_after_telco_sum[$dlr_after_telco_key] = 0;
          }

          $dlr_after_telco_sum[$dlr_after_telco_key] = $dlr_after_telco_sum[$dlr_after_telco_key] + (float)$dlr_after_telco_value['value'];

          if(count($sumemryData)-1 == $sumemry_key)
          {
            $dlr_after_telco_arr[$dlr_after_telco_key] = ['value' => $dlr_after_telco_sum[$dlr_after_telco_key], 'class' => $dlr_after_telco_value['class']];
          }
        }

        foreach ($sumemry['fir_after_telco']['dates'] as $fir_after_telco_key => $fir_after_telco_value) {
          if($sumemry_key == 0){
            $fir_after_telco_sum[$fir_after_telco_key] = 0;
          }

          $fir_after_telco_sum[$fir_after_telco_key] = $fir_after_telco_sum[$fir_after_telco_key] + (float)$fir_after_telco_value['value'];

          if(count($sumemryData)-1 == $sumemry_key)
          {
            $fir_after_telco_arr[$fir_after_telco_key] = ['value' => $fir_after_telco_sum[$fir_after_telco_key], 'class' => $fir_after_telco_value['class']];
          }
        }

        foreach ($sumemry['discrepency_after_telco']['dates'] as $discrepency_after_telco_key => $discrepency_after_telco_value) {
          if($sumemry_key == 0){
            $discrepency_after_telco_sum[$discrepency_after_telco_key] = 0;
          }

          $discrepency_after_telco_sum[$discrepency_after_telco_key] = ($dlr_after_telco_sum[$discrepency_after_telco_key] != 0 && $fir_after_telco_sum[$discrepency_after_telco_key] != 0) ? (($fir_after_telco_sum[$discrepency_after_telco_key] - $dlr_after_telco_sum[$discrepency_after_telco_key])/$dlr_after_telco_sum[$discrepency_after_telco_key])*100 : (float)0;

          if($discrepency_after_telco_sum[$discrepency_after_telco_key] < 10){
            $discrepency_after_telco_class[$discrepency_after_telco_key] = "text-success";
          }else if($discrepency_after_telco_sum[$discrepency_after_telco_key] > 10 && $discrepency_after_telco_sum[$discrepency_after_telco_key] < 50){
            $discrepency_after_telco_class[$discrepency_after_telco_key] = "text-warning";
          }else if($discrepency_after_telco_sum[$discrepency_after_telco_key] > 50){
            $discrepency_after_telco_class[$discrepency_after_telco_key] = "text-danger";
          }else{
            $discrepency_after_telco_class[$discrepency_after_telco_key] = "text-success";
          }

          if(count($sumemryData)-1 == $sumemry_key)
          {
            $discrepency_after_telco_arr[$discrepency_after_telco_key] = ['value' => $discrepency_after_telco_sum[$discrepency_after_telco_key], 'class' => $discrepency_after_telco_class[$discrepency_after_telco_key]];
          }
        }

        foreach ($sumemry['net_revenue']['dates'] as $net_key => $net_value) {
          if($sumemry_key == 0){
            $net_sum[$net_key] = 0;
          }

          $net_sum[$net_key] = $net_sum[$net_key] + (float)$net_value['value'];

          if(count($sumemryData)-1 == $sumemry_key)
          {
            $net_rev_arr[$net_key] = ['value' => $net_sum[$net_key], 'class' => $net_value['class']];
          }
        }

        foreach ($sumemry['fir_net_revenue']['dates'] as $net_usd_key => $net_usd_value) {
          if($sumemry_key == 0){
            $net_usd_sum[$net_usd_key] = 0;
          }

          $net_usd_sum[$net_usd_key] = $net_usd_sum[$net_usd_key] + (float)$net_usd_value['value'];

          if(count($sumemryData)-1 == $sumemry_key)
          {
            $fir_net_rev_arr[$net_usd_key] = ['value' => $net_usd_sum[$net_usd_key], 'class' => $net_usd_value['class']];
          }
        }

        foreach ($sumemry['discrepency_net_revenue']['dates'] as $discrepency_net_key => $discrepency_net_value) {
          if($sumemry_key == 0){
            $discrepency_net_sum[$discrepency_net_key] = 0;
          }

          $discrepency_net_sum[$discrepency_net_key] = ($net_sum[$discrepency_net_key] != 0 && $net_usd_sum[$discrepency_net_key] != 0) ? (($net_usd_sum[$discrepency_net_key] - $net_sum[$discrepency_net_key])/$net_sum[$discrepency_net_key])*100 : (float)0;

          if($discrepency_net_sum[$discrepency_net_key] < 10){
            $discrepency_net_class[$discrepency_net_key] = "text-success";
          }else if($discrepency_net_sum[$discrepency_net_key] > 10 && $discrepency_net_sum[$discrepency_net_key] < 50){
            $discrepency_net_class[$discrepency_net_key] = "text-warning";
          }else if($discrepency_net_sum[$discrepency_net_key] > 50){
            $discrepency_net_class[$discrepency_net_key] = "text-danger";
          }else{
            $discrepency_net_class[$discrepency_net_key] = "text-success";
          }

          if(count($sumemryData)-1 == $sumemry_key)
          {
            $discrepency_net_rev_arr[$discrepency_net_key] = ['value' => $discrepency_net_sum[$discrepency_net_key], 'class' => $discrepency_net_class[$discrepency_net_key]];
          }
        }
      }

      $dataArr['dlr']['dates'] = $dlr_arr;                
      $dataArr['dlr']['total'] = $dlr_total;

      $dataArr['fir']['dates'] = $fir_arr;                
      $dataArr['fir']['total'] = $fir_total;

      $dataArr['discrepency']['dates'] = $discrepency_arr;                
      $dataArr['discrepency']['total'] = $discrepency_total;

      $dataArr['dlr_after_telco']['dates'] = $dlr_after_telco_arr;                
      $dataArr['dlr_after_telco']['total'] = $dlr_after_telco_total; 

      $dataArr['fir_after_telco']['dates'] = $fir_after_telco_arr;                
      $dataArr['fir_after_telco']['total'] = $fir_after_telco_total; 

      $dataArr['discrepency_after_telco']['dates'] = $discrepency_after_telco_arr; 
      $dataArr['discrepency_after_telco']['total'] = $discrepency_after_telco_total;

      $dataArr['net_revenue']['dates'] = $net_rev_arr;                
      $dataArr['net_revenue']['total'] = $net_rev_total; 

      $dataArr['fir_net_revenue']['dates'] = $fir_net_rev_arr;                
      $dataArr['fir_net_revenue']['total'] = $fir_net_rev_total; 

      $dataArr['discrepency_net_revenue']['dates'] = $discrepency_net_rev_arr; 
      $dataArr['discrepency_net_revenue']['total'] = $discrepency_net_rev_total;

      return $dataArr; 
    }
  }

  public static function sumOfAllReconcileData($sumemry)
  {
    if(!empty($sumemry))
    {
      $dataArr = [];
      $dlr_arr = [];
      $fir_arr = [];
      $fir_usd_arr = [];
      $discrepency_arr = [];
      $dlr_after_telco_arr = [];
      $fir_after_telco_arr = [];
      $fir_after_telco_usd_arr = [];
      $discrepency_after_telco_arr = [];
      $net_rev_arr = [];
      $fir_net_rev_arr = [];
      $discrepency_net_rev_arr = [];

      $dlr_total = $dlr_after_telco_total = 0;
      $fir_total = $fir_after_telco_total = 0;
      $fir_usd_total = $fir_after_telco_usd_total = 0;
      $net_rev_total = $fir_net_rev_total = 0;
      $discrepency_total = $discrepency_after_telco_total = $discrepency_net_rev_total = 0;

      foreach ($sumemry as $sumemry_key => $sumemry_value)
      {
        $dlr_total = $dlr_total + (float)$sumemry_value['gros_rev_usd']['total'];
        $dlr_after_telco_total = $dlr_after_telco_total + (float)$sumemry_value['gros_rev_usd_after_telco']['total'];

        $fir_total = $fir_total + (float)$sumemry_value['fir']['total'];
        $fir_after_telco_total = $fir_after_telco_total + (float)$sumemry_value['fir_after_telco']['total'];

        $fir_usd_total = $fir_usd_total + (float)$sumemry_value['fir_usd']['total'];
        $fir_after_telco_usd_total = $fir_after_telco_usd_total + (float)$sumemry_value['fir_after_telco_usd']['total'];

        $net_rev_total = $net_rev_total + (float)$sumemry_value['net_revenue_usd']['total'];
        $fir_net_rev_total = $fir_net_rev_total + (float)$sumemry_value['fir_net_revenue_usd']['total'];
        
        $discrepency_total = ($dlr_total != 0 && $fir_usd_total != 0) ? (($fir_usd_total - $dlr_total)/$dlr_total)*100 : (float)0;

        $discrepency_after_telco_total = ($dlr_after_telco_total != 0 && $fir_after_telco_usd_total != 0) ? (($fir_after_telco_usd_total - $dlr_after_telco_total)/$dlr_after_telco_total)*100 : (float)0;

        $discrepency_net_rev_total = ($net_rev_total != 0 && $fir_net_rev_total != 0) ? (($fir_net_rev_total - $net_rev_total)/$net_rev_total)*100 : (float)0;

        foreach ($sumemry_value['gros_rev_usd']['dates'] as $dlr_key => $dlr_value) {
          if($sumemry_key == 0){
            $dlr_sum[$dlr_key] = 0;
          }

          $dlr_sum[$dlr_key] = $dlr_sum[$dlr_key] + (float)$dlr_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $dlr_arr[$dlr_key] = ['value' => $dlr_sum[$dlr_key], 'class' => $dlr_value['class']];
          }
        }

        foreach ($sumemry_value['fir']['dates'] as $fir_key => $fir_value) {
          if($sumemry_key == 0){
            $fir_sum[$fir_key] = 0;
          }

          $fir_sum[$fir_key] = $fir_sum[$fir_key] + (float)$fir_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $fir_arr[$fir_key] = ['value' => $fir_sum[$fir_key], 'class' => $fir_value['class']];
          }
        }

        foreach ($sumemry_value['fir_usd']['dates'] as $fir_usd_key => $fir_usd_value) {
          if($sumemry_key == 0){
            $fir_usd_sum[$fir_usd_key] = 0;
          }

          $fir_usd_sum[$fir_usd_key] = $fir_usd_sum[$fir_usd_key] + (float)$fir_usd_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $fir_usd_arr[$fir_usd_key] = ['value' => $fir_usd_sum[$fir_usd_key], 'class' => $fir_usd_value['class']];
          }
        }

        foreach ($sumemry_value['discrepency']['dates'] as $discrepency_key => $discrepency_value) {
          if($sumemry_key == 0){
            $discrepency_sum[$discrepency_key] = 0;
          }

          $discrepency_sum[$discrepency_key] = ($dlr_sum[$discrepency_key] != 0 && $fir_usd_sum[$discrepency_key] != 0) ? (($fir_usd_sum[$discrepency_key] - $dlr_sum[$discrepency_key])/$dlr_sum[$discrepency_key])*100 : (float)0;

          if($discrepency_sum[$discrepency_key] < 10){
            $discrepency_class[$discrepency_key] = "text-success";
          }else if($discrepency_sum[$discrepency_key] > 10 && $discrepency_sum[$discrepency_key] < 50){
            $discrepency_class[$discrepency_key] = "text-warning";
          }else if($discrepency_sum[$discrepency_key] > 50){
            $discrepency_class[$discrepency_key] = "text-danger";
          }else{
            $discrepency_class[$discrepency_key] = "text-success";
          }

          if(count($sumemry)-1 == $sumemry_key)
          {
            $discrepency_arr[$discrepency_key] = ['value' => $discrepency_sum[$discrepency_key], 'class' => $discrepency_class[$discrepency_key]];
          }
        }

        foreach ($sumemry_value['gros_rev_usd_after_telco']['dates'] as $dlr_after_telco_key => $dlr_after_telco_value) {
          if($sumemry_key == 0){
            $dlr_after_telco_sum[$dlr_after_telco_key] = 0;
          }

          $dlr_after_telco_sum[$dlr_after_telco_key] = $dlr_after_telco_sum[$dlr_after_telco_key] + (float)$dlr_after_telco_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $dlr_after_telco_arr[$dlr_after_telco_key] = ['value' => $dlr_after_telco_sum[$dlr_after_telco_key], 'class' => $dlr_after_telco_value['class']];
          }
        }

        foreach ($sumemry_value['fir_after_telco']['dates'] as $fir_after_telco_key => $fir_after_telco_value) {
          if($sumemry_key == 0){
            $fir_after_telco_sum[$fir_after_telco_key] = 0;
          }

          $fir_after_telco_sum[$fir_after_telco_key] = $fir_after_telco_sum[$fir_after_telco_key] + (float)$fir_after_telco_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $fir_after_telco_arr[$fir_after_telco_key] = ['value' => $fir_after_telco_sum[$fir_after_telco_key], 'class' => $fir_after_telco_value['class']];
          }
        }

        foreach ($sumemry_value['fir_after_telco_usd']['dates'] as $fir_after_telco_usd_key => $fir_after_telco_usd_value) {
          if($sumemry_key == 0){
            $fir_after_telco_usd_sum[$fir_after_telco_usd_key] = 0;
          }

          $fir_after_telco_usd_sum[$fir_after_telco_usd_key] = $fir_after_telco_usd_sum[$fir_after_telco_usd_key] + (float)$fir_after_telco_usd_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $fir_after_telco_usd_arr[$fir_after_telco_usd_key] = ['value' => $fir_after_telco_usd_sum[$fir_after_telco_usd_key], 'class' => $fir_after_telco_usd_value['class']];
          }
        }

        foreach ($sumemry_value['discrepency_after_telco']['dates'] as $discrepency_after_telco_key => $discrepency_after_telco_value) {
          if($sumemry_key == 0){
            $discrepency_after_telco_sum[$discrepency_after_telco_key] = 0;
          }

          $discrepency_after_telco_sum[$discrepency_after_telco_key] = ($dlr_after_telco_sum[$discrepency_after_telco_key] != 0 && $fir_after_telco_usd_sum[$discrepency_after_telco_key] != 0) ? (($fir_after_telco_usd_sum[$discrepency_after_telco_key] - $dlr_after_telco_sum[$discrepency_after_telco_key])/$dlr_after_telco_sum[$discrepency_after_telco_key])*100 : (float)0;

          if($discrepency_after_telco_sum[$discrepency_after_telco_key] < 10){
            $discrepency_after_telco_class[$discrepency_after_telco_key] = "text-success";
          }else if($discrepency_after_telco_sum[$discrepency_after_telco_key] > 10 && $discrepency_after_telco_sum[$discrepency_after_telco_key] < 50){
            $discrepency_after_telco_class[$discrepency_after_telco_key] = "text-warning";
          }else if($discrepency_after_telco_sum[$discrepency_after_telco_key] > 50){
            $discrepency_after_telco_class[$discrepency_after_telco_key] = "text-danger";
          }else{
            $discrepency_after_telco_class[$discrepency_after_telco_key] = "text-success";
          }

          if(count($sumemry)-1 == $sumemry_key)
          {
            $discrepency_after_telco_arr[$discrepency_after_telco_key] = ['value' => $discrepency_after_telco_sum[$discrepency_after_telco_key], 'class' => $discrepency_after_telco_class[$discrepency_after_telco_key]];
          }
        }

        foreach ($sumemry_value['net_revenue_usd']['dates'] as $net_key => $net_value) {
          if($sumemry_key == 0){
            $net_sum[$net_key] = 0;
          }

          $net_sum[$net_key] = $net_sum[$net_key] + (float)$net_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $net_rev_arr[$net_key] = ['value' => $net_sum[$net_key], 'class' => $net_value['class']];
          }
        }

        foreach ($sumemry_value['fir_net_revenue_usd']['dates'] as $net_usd_key => $net_usd_value) {
          if($sumemry_key == 0){
            $net_usd_sum[$net_usd_key] = 0;
          }

          $net_usd_sum[$net_usd_key] = $net_usd_sum[$net_usd_key] + (float)$net_usd_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $fir_net_rev_arr[$net_usd_key] = ['value' => $net_usd_sum[$net_usd_key], 'class' => $net_usd_value['class']];
          }
        }

        foreach ($sumemry_value['discrepency_net_revenue']['dates'] as $discrepency_net_key => $discrepency_net_value) {
          if($sumemry_key == 0){
            $discrepency_net_sum[$discrepency_net_key] = 0;
          }

          $discrepency_net_sum[$discrepency_net_key] = ($net_sum[$discrepency_net_key] != 0 && $net_usd_sum[$discrepency_net_key] != 0) ? (($net_usd_sum[$discrepency_net_key] - $net_sum[$discrepency_net_key])/$net_sum[$discrepency_net_key])*100 : (float)0;

          if($discrepency_net_sum[$discrepency_net_key] < 10){
            $discrepency_net_class[$discrepency_net_key] = "text-success";
          }else if($discrepency_net_sum[$discrepency_net_key] > 10 && $discrepency_net_sum[$discrepency_net_key] < 50){
            $discrepency_net_class[$discrepency_net_key] = "text-warning";
          }else if($discrepency_net_sum[$discrepency_net_key] > 50){
            $discrepency_net_class[$discrepency_net_key] = "text-danger";
          }else{
            $discrepency_net_class[$discrepency_net_key] = "text-success";
          }

          if(count($sumemry)-1 == $sumemry_key)
          {
            $discrepency_net_rev_arr[$discrepency_net_key] = ['value' => $discrepency_net_sum[$discrepency_net_key], 'class' => $discrepency_net_class[$discrepency_net_key]];
          }
        }
      }

      $dataArr['dlr']['dates'] = $dlr_arr;                
      $dataArr['dlr']['total'] = $dlr_total;

      $dataArr['fir']['dates'] = $fir_arr;                
      $dataArr['fir']['total'] = $fir_total;

      $dataArr['fir_usd']['dates'] = $fir_usd_arr;                
      $dataArr['fir_usd']['total'] = $fir_usd_total;

      $dataArr['discrepency']['dates'] = $discrepency_arr;                
      $dataArr['discrepency']['total'] = $discrepency_total;

      $dataArr['dlr_after_telco']['dates'] = $dlr_after_telco_arr;                
      $dataArr['dlr_after_telco']['total'] = $dlr_after_telco_total; 

      $dataArr['fir_after_telco']['dates'] = $fir_after_telco_arr;                
      $dataArr['fir_after_telco']['total'] = $fir_after_telco_total; 

      $dataArr['fir_after_telco_usd']['dates'] = $fir_after_telco_usd_arr;                
      $dataArr['fir_after_telco_usd']['total'] = $fir_after_telco_usd_total; 

      $dataArr['discrepency_after_telco']['dates'] = $discrepency_after_telco_arr; 
      $dataArr['discrepency_after_telco']['total'] = $discrepency_after_telco_total;

      $dataArr['net_revenue_usd']['dates'] = $net_rev_arr;                
      $dataArr['net_revenue_usd']['total'] = $net_rev_total; 

      $dataArr['fir_net_revenue_usd']['dates'] = $fir_net_rev_arr;                
      $dataArr['fir_net_revenue_usd']['total'] = $fir_net_rev_total; 

      $dataArr['discrepency_net_revenue']['dates'] = $discrepency_net_rev_arr; 
      $dataArr['discrepency_net_revenue']['total'] = $discrepency_net_rev_total;

      return $dataArr;
    }
  }

  public static function sumOfCountryTargetRevenueData($sumemryData)
  {
    $dataArr = [];
    $gross_rev_arr = [];
    $target_rev_arr = [];
    $rev_disc_arr = [];
    $rev_after_share_arr = [];
    $target_after_share_arr = [];
    $target_rev_disc_arr = [];
    $pnl_arr = [];
    $target_pnl_arr = [];
    $pnl_disc_arr = [];
    $opex_arr = [];
    $target_opex_arr = [];
    $opex_disc_arr = [];
    $ebida_arr = [];
    $target_ebida_arr = [];
    $ebida_disc_arr = [];

    $gross_rev_total = $target_rev_total = 0;
    $rev_disc_total = $rev_after_share_total = 0;
    $target_after_share_total = $target_rev_disc_total = 0;
    $pnl_total = $target_pnl_total = $pnl_disc_total = 0;
    $opex_total = $target_opex_total = $opex_disc_total = 0;
    $ebida_total = $target_ebida_total = $ebida_disc_total = 0;

    if(!empty($sumemryData))
    {
      foreach ($sumemryData as $sumemry_key => $sumemry)
      {
        $gross_rev_total = $gross_rev_total + (float)$sumemry['gross_rev']['total'];
        $target_rev_total = $target_rev_total + (float)$sumemry['target_rev']['total'];

        $rev_disc_total = $rev_disc_total + (float)$sumemry['rev_disc']['total'];
        $rev_after_share_total = $rev_after_share_total + (float)$sumemry['rev_after_share']['total'];
        
        $target_after_share_total = $target_after_share_total + (float)$sumemry['target_after_share']['total'];
        $target_rev_disc_total = $target_rev_disc_total + (float)$sumemry['target_rev_disc']['total'];

        $pnl_total = $pnl_total + (float)$sumemry['pnl']['total'];
        $target_pnl_total = $target_pnl_total + (float)$sumemry['target_pnl']['total'];
        $pnl_disc_total = $pnl_disc_total + (float)$sumemry['pnl_disc']['total'];

        $opex_total = $opex_total + (float)$sumemry['opex']['total'];
        $target_opex_total = $target_opex_total + (float)$sumemry['target_opex']['total'];
        $opex_disc_total = $opex_disc_total + (float)$sumemry['opex_disc']['total'];

        $ebida_total = $ebida_total + (float)$sumemry['ebida']['total'];
        $target_ebida_total = $target_ebida_total + (float)$sumemry['target_ebida']['total'];
        $ebida_disc_total = $ebida_disc_total + (float)$sumemry['ebida_disc']['total'];

        foreach ($sumemry['gross_rev']['dates'] as $gross_rev_key => $gross_rev_value)
        {
          if($sumemry_key == 0){
            $gross_rev_sum[$gross_rev_key] = 0;
          }

          $gross_rev_sum[$gross_rev_key] = $gross_rev_sum[$gross_rev_key] + (float)$gross_rev_value['value'];

          if(count($sumemryData)-1 == $sumemry_key)
          {
            $gross_rev_arr[$gross_rev_key] = ['value' => $gross_rev_sum[$gross_rev_key], 'class' => $gross_rev_value['class']];
          }
        }

        foreach ($sumemry['target_rev']['dates'] as $target_rev_key => $target_rev_value)
        {
          if($sumemry_key == 0){
            $target_rev_sum[$target_rev_key] = 0;
          }

          $target_rev_sum[$target_rev_key] = $target_rev_sum[$target_rev_key] + (float)$target_rev_value['value'];

          if(count($sumemryData)-1 == $sumemry_key)
          {
            $target_rev_arr[$target_rev_key] = ['value' => $target_rev_sum[$target_rev_key], 'class' => $target_rev_value['class']];
          }
        }

        foreach ($sumemry['rev_disc']['dates'] as $rev_disc_key => $rev_disc_value) {
          if($sumemry_key == 0){
            $rev_disc_sum[$rev_disc_key] = 0;
          }

          $rev_disc_sum[$rev_disc_key] = $rev_disc_sum[$rev_disc_key] + (float)$rev_disc_value['value'];

          if(count($sumemryData)-1 == $sumemry_key)
          {
            $rev_disc_arr[$rev_disc_key] = ['value' => $rev_disc_sum[$rev_disc_key], 'class' => $rev_disc_value['class']];
          }
        }

        foreach ($sumemry['rev_after_share']['dates'] as $rev_after_share_key => $rev_after_share_value) {
          if($sumemry_key == 0){
            $rev_after_share_sum[$rev_after_share_key] = 0;
          }

          $rev_after_share_sum[$rev_after_share_key] = $rev_after_share_sum[$rev_after_share_key] + (float)$rev_after_share_value['value'];

          if(count($sumemryData)-1 == $sumemry_key)
          {
            $rev_after_share_arr[$rev_after_share_key] = ['value' => $rev_after_share_sum[$rev_after_share_key], 'class' => $rev_after_share_value['class']];
          }
        }

        foreach ($sumemry['target_after_share']['dates'] as $target_after_share_key => $target_after_share_value) {
          if($sumemry_key == 0){
            $target_after_share_sum[$target_after_share_key] = 0;
          }

          $target_after_share_sum[$target_after_share_key] = $target_after_share_sum[$target_after_share_key] + (float)$target_after_share_value['value'];

          if(count($sumemryData)-1 == $sumemry_key)
          {
            $target_after_share_arr[$target_after_share_key] = ['value' => $target_after_share_sum[$target_after_share_key], 'class' => $target_after_share_value['class']];
          }
        }

        foreach ($sumemry['target_rev_disc']['dates'] as $target_rev_disc_key => $target_rev_disc_value) {
          if($sumemry_key == 0){
            $target_rev_disc_sum[$target_rev_disc_key] = 0;
          }

          $target_rev_disc_sum[$target_rev_disc_key] = $target_rev_disc_sum[$target_rev_disc_key] + (float)$target_rev_disc_value['value'];

          if(count($sumemryData)-1 == $sumemry_key)
          {
            $target_rev_disc_arr[$target_rev_disc_key] = ['value' => $target_rev_disc_sum[$target_rev_disc_key], 'class' => $target_rev_disc_value['class']];
          }
        }

        foreach ($sumemry['pnl']['dates'] as $pnl_key => $pnl_value) {
          if($sumemry_key == 0){
            $pnl_sum[$pnl_key] = 0;
          }

          $pnl_sum[$pnl_key] = $pnl_sum[$pnl_key] + (float)$pnl_value['value'];

          if(count($sumemryData)-1 == $sumemry_key)
          {
            $pnl_arr[$pnl_key] = ['value' => $pnl_sum[$pnl_key], 'class' => $pnl_value['class']];
          }
        }

        foreach ($sumemry['target_pnl']['dates'] as $target_pnl_key => $target_pnl_value) {
          if($sumemry_key == 0){
            $target_pnl_sum[$target_pnl_key] = 0;
          }

          $target_pnl_sum[$target_pnl_key] = $target_pnl_sum[$target_pnl_key] + (float)$target_pnl_value['value'];

          if(count($sumemryData)-1 == $sumemry_key)
          {
            $target_pnl_arr[$target_pnl_key] = ['value' => $target_pnl_sum[$target_pnl_key], 'class' => $target_pnl_value['class']];
          }
        }

        foreach ($sumemry['pnl_disc']['dates'] as $pnl_disc_key => $pnl_disc_value) {
          if($sumemry_key == 0){
            $pnl_disc_sum[$pnl_disc_key] = 0;
          }

          $pnl_disc_sum[$pnl_disc_key] = $pnl_disc_sum[$pnl_disc_key] + (float)$pnl_disc_value['value'];

          if(count($sumemryData)-1 == $sumemry_key)
          {
            $pnl_disc_arr[$pnl_disc_key] = ['value' => $pnl_disc_sum[$pnl_disc_key], 'class' => $pnl_disc_value['class']];
          }
        }

        foreach ($sumemry['opex']['dates'] as $opex_key => $opex_value) {
          if($sumemry_key == 0){
            $opex_sum[$opex_key] = 0;
          }

          $opex_sum[$opex_key] = $opex_sum[$opex_key] + (float)$opex_value['value'];

          if(count($sumemryData)-1 == $sumemry_key)
          {
            $opex_arr[$opex_key] = ['value' => $opex_sum[$opex_key], 'class' => $opex_value['class']];
          }
        }

        foreach ($sumemry['target_opex']['dates'] as $target_opex_key => $target_opex_value) {
          if($sumemry_key == 0){
            $target_opex_sum[$target_opex_key] = 0;
          }

          $target_opex_sum[$target_opex_key] = $target_opex_sum[$target_opex_key] + (float)$target_opex_value['value'];

          if(count($sumemryData)-1 == $sumemry_key)
          {
            $target_opex_arr[$target_opex_key] = ['value' => $target_opex_sum[$target_opex_key], 'class' => $target_opex_value['class']];
          }
        }

        foreach ($sumemry['opex_disc']['dates'] as $opex_disc_key => $opex_disc_value) {
          if($sumemry_key == 0){
            $opex_disc_sum[$opex_disc_key] = 0;
          }

          $opex_disc_sum[$opex_disc_key] = $opex_disc_sum[$opex_disc_key] + (float)$opex_disc_value['value'];

          if(count($sumemryData)-1 == $sumemry_key)
          {
            $opex_disc_arr[$opex_disc_key] = ['value' => $opex_disc_sum[$opex_disc_key], 'class' => $opex_disc_value['class']];
          }
        }

        foreach ($sumemry['ebida']['dates'] as $ebida_key => $ebida_value) {
          if($sumemry_key == 0){
            $ebida_sum[$ebida_key] = 0;
          }

          $ebida_sum[$ebida_key] = $ebida_sum[$ebida_key] + (float)$ebida_value['value'];

          if(count($sumemryData)-1 == $sumemry_key)
          {
            $ebida_arr[$ebida_key] = ['value' => $ebida_sum[$ebida_key], 'class' => $ebida_value['class']];
          }
        }

        foreach ($sumemry['target_ebida']['dates'] as $target_ebida_key => $target_ebida_value) {
          if($sumemry_key == 0){
            $target_ebida_sum[$target_ebida_key] = 0;
          }

          $target_ebida_sum[$target_ebida_key] = $target_ebida_sum[$target_ebida_key] + (float)$target_ebida_value['value'];

          if(count($sumemryData)-1 == $sumemry_key)
          {
            $target_ebida_arr[$target_ebida_key] = ['value' => $target_ebida_sum[$target_ebida_key], 'class' => $target_ebida_value['class']];
          }
        }

        foreach ($sumemry['ebida_disc']['dates'] as $ebida_disc_key => $ebida_disc_value) {
          if($sumemry_key == 0){
            $ebida_disc_sum[$ebida_disc_key] = 0;
          }

          $ebida_disc_sum[$ebida_disc_key] = $ebida_disc_sum[$ebida_disc_key] + (float)$ebida_disc_value['value'];

          if(count($sumemryData)-1 == $sumemry_key)
          {
            $ebida_disc_arr[$ebida_disc_key] = ['value' => $ebida_disc_sum[$ebida_disc_key], 'class' => $ebida_disc_value['class']];
          }
        }
      }

      $dataArr['gross_rev']['dates'] = $gross_rev_arr;                
      $dataArr['gross_rev']['total'] = $gross_rev_total;

      $dataArr['target_rev']['dates'] = $target_rev_arr;                
      $dataArr['target_rev']['total'] = $target_rev_total;

      $dataArr['rev_disc']['dates'] = $rev_disc_arr;                
      $dataArr['rev_disc']['total'] = $rev_disc_total;

      $dataArr['rev_after_share']['dates'] = $rev_after_share_arr;                
      $dataArr['rev_after_share']['total'] = $rev_after_share_total; 

      $dataArr['target_after_share']['dates'] = $target_after_share_arr;                
      $dataArr['target_after_share']['total'] = $target_after_share_total; 

      $dataArr['target_rev_disc']['dates'] = $target_rev_disc_arr; 
      $dataArr['target_rev_disc']['total'] = $target_rev_disc_total;

      $dataArr['pnl']['dates'] = $pnl_arr; 
      $dataArr['pnl']['total'] = $pnl_total;

      $dataArr['target_pnl']['dates'] = $target_pnl_arr; 
      $dataArr['target_pnl']['total'] = $target_pnl_total;

      $dataArr['pnl_disc']['dates'] = $pnl_disc_arr; 
      $dataArr['pnl_disc']['total'] = $pnl_disc_total;

      $dataArr['opex']['dates'] = $opex_arr; 
      $dataArr['opex']['total'] = $opex_total;

      $dataArr['target_opex']['dates'] = $target_opex_arr; 
      $dataArr['target_opex']['total'] = $target_opex_total;

      $dataArr['opex_disc']['dates'] = $opex_disc_arr; 
      $dataArr['opex_disc']['total'] = $opex_disc_total;

      $dataArr['ebida']['dates'] = $ebida_arr; 
      $dataArr['ebida']['total'] = $ebida_total;

      $dataArr['target_ebida']['dates'] = $target_ebida_arr; 
      $dataArr['target_ebida']['total'] = $target_ebida_total;

      $dataArr['ebida_disc']['dates'] = $ebida_disc_arr; 
      $dataArr['ebida_disc']['total'] = $ebida_disc_total;            

      return $dataArr; 
    }
  }

  public static function sumOfAllTargetRevenueData($sumemry)
  {
    if(!empty($sumemry))
    {
      $dataArr = [];
      $gross_rev_arr = [];
      $target_rev_arr = [];
      $rev_disc_arr = [];
      $rev_after_share_arr = [];
      $target_after_share_arr = [];
      $target_rev_disc_arr = [];
      $pnl_arr = [];
      $target_pnl_arr = [];
      $pnl_disc_arr = [];
      $opex_arr = [];
      $target_opex_arr = [];
      $opex_disc_arr = [];
      $ebida_arr = [];
      $target_ebida_arr = [];
      $ebida_disc_arr = [];

      $gross_rev_total = $target_rev_total = 0;
      $rev_disc_total = $rev_after_share_total = 0;
      $target_after_share_total = $target_rev_disc_total = 0;
      $pnl_total = $target_pnl_total = $pnl_disc_total = 0;
      $opex_total = $target_opex_total = $opex_disc_total = 0;
      $ebida_total = $target_ebida_total = $ebida_disc_total = 0;

      foreach ($sumemry as $sumemry_key => $sumemry_value)
      {
        $gross_rev_total = $gross_rev_total + (float)$sumemry_value['gros_rev_usd']['total'];
        $target_rev_total = $target_rev_total + (float)$sumemry_value['target_rev']['total'];
        $rev_disc_total = ($gross_rev_total != 0 && $target_rev_total != 0) ? (($target_rev_total - $gross_rev_total)/$gross_rev_total)*100 : (float)0 ;

        $rev_after_share_total = $rev_after_share_total + (float)$sumemry_value['rev_after_share']['total'];
        $target_after_share_total = $target_after_share_total + (float)$sumemry_value['target_after_share']['total'];
        $target_rev_disc_total = ($rev_after_share_total != 0 && $target_after_share_total != 0) ? (($target_after_share_total - $rev_after_share_total)/$rev_after_share_total)*100 : (float)0 ;

        $pnl_total = $pnl_total + (float)$sumemry_value['pnl']['total'];
        $target_pnl_total = $target_pnl_total + (float)$sumemry_value['target_pnl']['total'];
        $pnl_disc_total = ($pnl_total != 0 && $target_pnl_total != 0) ? (($target_pnl_total - $pnl_total)/$pnl_total)*100 : (float)0 ;

        $opex_total = $opex_total + (float)$sumemry_value['opex']['total'];
        $target_opex_total = $target_opex_total + (float)$sumemry_value['target_opex']['total'];
        $opex_disc_total = ($opex_total != 0 && $target_opex_total != 0) ? (($target_opex_total - $opex_total)/$opex_total)*100 : (float)0 ;

        $ebida_total = $ebida_total + (float)$sumemry_value['ebida']['total'];
        $target_ebida_total = $target_ebida_total + (float)$sumemry_value['target_ebida']['total'];
        $ebida_disc_total = ($ebida_total != 0 && $target_ebida_total != 0) ? (($target_ebida_total - $ebida_total)/$ebida_total)*100 : (float)0 ;

        foreach ($sumemry_value['gros_rev_usd']['dates'] as $gross_rev_key => $gross_rev_value)
        {
          if($sumemry_key == 0){
            $gross_rev_sum[$gross_rev_key] = 0;
          }

          $gross_rev_sum[$gross_rev_key] = $gross_rev_sum[$gross_rev_key] + (float)$gross_rev_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $gross_rev_arr[$gross_rev_key] = ['value' => $gross_rev_sum[$gross_rev_key], 'class' => $gross_rev_value['class']];
          }
        }

        foreach ($sumemry_value['target_rev']['dates'] as $target_rev_key => $target_rev_value)
        {
          if($sumemry_key == 0){
            $target_rev_sum[$target_rev_key] = 0;
          }

          $target_rev_sum[$target_rev_key] = $target_rev_sum[$target_rev_key] + (float)$target_rev_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $target_rev_arr[$target_rev_key] = ['value' => $target_rev_sum[$target_rev_key], 'class' => $target_rev_value['class']];
          }
        }

        foreach ($sumemry_value['rev_disc']['dates'] as $rev_disc_key => $rev_disc_value) {
          if($sumemry_key == 0){
            $rev_disc_sum[$rev_disc_key] = 0;
          }

          $rev_disc_sum[$rev_disc_key] = $rev_disc_sum[$rev_disc_key] + (float)$rev_disc_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $rev_disc_arr[$rev_disc_key] = ['value' => $rev_disc_sum[$rev_disc_key], 'class' => $rev_disc_value['class']];
          }
        }

        foreach ($sumemry_value['rev_after_share']['dates'] as $rev_after_share_key => $rev_after_share_value) {
          if($sumemry_key == 0){
            $rev_after_share_sum[$rev_after_share_key] = 0;
          }

          $rev_after_share_sum[$rev_after_share_key] = $rev_after_share_sum[$rev_after_share_key] + (float)$rev_after_share_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $rev_after_share_arr[$rev_after_share_key] = ['value' => $rev_after_share_sum[$rev_after_share_key], 'class' => $rev_after_share_value['class']];
          }
        }

        foreach ($sumemry_value['target_after_share']['dates'] as $target_after_share_key => $target_after_share_value) {
          if($sumemry_key == 0){
            $target_after_share_sum[$target_after_share_key] = 0;
          }

          $target_after_share_sum[$target_after_share_key] = $target_after_share_sum[$target_after_share_key] + (float)$target_after_share_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $target_after_share_arr[$target_after_share_key] = ['value' => $target_after_share_sum[$target_after_share_key], 'class' => $target_after_share_value['class']];
          }
        }

        foreach ($sumemry_value['target_rev_disc']['dates'] as $target_rev_disc_key => $target_rev_disc_value) {
          if($sumemry_key == 0){
            $target_rev_disc_sum[$target_rev_disc_key] = 0;
          }

          $target_rev_disc_sum[$target_rev_disc_key] = $target_rev_disc_sum[$target_rev_disc_key] + (float)$target_rev_disc_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $target_rev_disc_arr[$target_rev_disc_key] = ['value' => $target_rev_disc_sum[$target_rev_disc_key], 'class' => $target_rev_disc_value['class']];
          }
        }

        foreach ($sumemry_value['pnl']['dates'] as $pnl_key => $pnl_value) {
          if($sumemry_key == 0){
            $pnl_sum[$pnl_key] = 0;
          }

          $pnl_sum[$pnl_key] = $pnl_sum[$pnl_key] + (float)$pnl_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $pnl_arr[$pnl_key] = ['value' => $pnl_sum[$pnl_key], 'class' => $pnl_value['class']];
          }
        }

        foreach ($sumemry_value['target_pnl']['dates'] as $target_pnl_key => $target_pnl_value) {
          if($sumemry_key == 0){
            $target_pnl_sum[$target_pnl_key] = 0;
          }

          $target_pnl_sum[$target_pnl_key] = $target_pnl_sum[$target_pnl_key] + (float)$target_pnl_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $target_pnl_arr[$target_pnl_key] = ['value' => $target_pnl_sum[$target_pnl_key], 'class' => $target_pnl_value['class']];
          }
        }

        foreach ($sumemry_value['pnl_disc']['dates'] as $pnl_disc_key => $pnl_disc_value) {
          if($sumemry_key == 0){
            $pnl_disc_sum[$pnl_disc_key] = 0;
          }

          $pnl_disc_sum[$pnl_disc_key] = $pnl_disc_sum[$pnl_disc_key] + (float)$pnl_disc_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $pnl_disc_arr[$pnl_disc_key] = ['value' => $pnl_disc_sum[$pnl_disc_key], 'class' => $pnl_disc_value['class']];
          }
        }

        foreach ($sumemry_value['opex']['dates'] as $opex_key => $opex_value) {
          if($sumemry_key == 0){
            $opex_sum[$opex_key] = 0;
          }

          $opex_sum[$opex_key] = $opex_sum[$opex_key] + (float)$opex_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $opex_arr[$opex_key] = ['value' => $opex_sum[$opex_key], 'class' => $opex_value['class']];
          }
        }

        foreach ($sumemry_value['target_opex']['dates'] as $target_opex_key => $target_opex_value) {
          if($sumemry_key == 0){
            $target_opex_sum[$target_opex_key] = 0;
          }

          $target_opex_sum[$target_opex_key] = $target_opex_sum[$target_opex_key] + (float)$target_opex_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $target_opex_arr[$target_opex_key] = ['value' => $target_opex_sum[$target_opex_key], 'class' => $target_opex_value['class']];
          }
        }

        foreach ($sumemry_value['opex_disc']['dates'] as $opex_disc_key => $opex_disc_value) {
          if($sumemry_key == 0){
            $opex_disc_sum[$opex_disc_key] = 0;
          }

          $opex_disc_sum[$opex_disc_key] = $opex_disc_sum[$opex_disc_key] + (float)$opex_disc_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $opex_disc_arr[$opex_disc_key] = ['value' => $opex_disc_sum[$opex_disc_key], 'class' => $opex_disc_value['class']];
          }
        }

        foreach ($sumemry_value['ebida']['dates'] as $ebida_key => $ebida_value) {
          if($sumemry_key == 0){
            $ebida_sum[$ebida_key] = 0;
          }

          $ebida_sum[$ebida_key] = $ebida_sum[$ebida_key] + (float)$ebida_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $ebida_arr[$ebida_key] = ['value' => $ebida_sum[$ebida_key], 'class' => $ebida_value['class']];
          }
        }

        foreach ($sumemry_value['target_ebida']['dates'] as $target_ebida_key => $target_ebida_value) {
          if($sumemry_key == 0){
            $target_ebida_sum[$target_ebida_key] = 0;
          }

          $target_ebida_sum[$target_ebida_key] = $target_ebida_sum[$target_ebida_key] + (float)$target_ebida_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $target_ebida_arr[$target_ebida_key] = ['value' => $target_ebida_sum[$target_ebida_key], 'class' => $target_ebida_value['class']];
          }
        }

        foreach ($sumemry_value['ebida_disc']['dates'] as $ebida_disc_key => $ebida_disc_value) {
          if($sumemry_key == 0){
            $ebida_disc_sum[$ebida_disc_key] = 0;
          }

          $ebida_disc_sum[$ebida_disc_key] = $ebida_disc_sum[$ebida_disc_key] + (float)$ebida_disc_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $ebida_disc_arr[$ebida_disc_key] = ['value' => $ebida_disc_sum[$ebida_disc_key], 'class' => $ebida_disc_value['class']];
          }
        }
      }

      $dataArr['gross_rev']['dates'] = $gross_rev_arr;                
      $dataArr['gross_rev']['total'] = $gross_rev_total;

      $dataArr['target_rev']['dates'] = $target_rev_arr;                
      $dataArr['target_rev']['total'] = $target_rev_total;

      $dataArr['rev_disc']['dates'] = $rev_disc_arr;                
      $dataArr['rev_disc']['total'] = $rev_disc_total;

      $dataArr['rev_after_share']['dates'] = $rev_after_share_arr;                
      $dataArr['rev_after_share']['total'] = $rev_after_share_total; 

      $dataArr['target_after_share']['dates'] = $target_after_share_arr;                
      $dataArr['target_after_share']['total'] = $target_after_share_total; 

      $dataArr['target_rev_disc']['dates'] = $target_rev_disc_arr; 
      $dataArr['target_rev_disc']['total'] = $target_rev_disc_total;

      $dataArr['pnl']['dates'] = $pnl_arr; 
      $dataArr['pnl']['total'] = $pnl_total;

      $dataArr['target_pnl']['dates'] = $target_pnl_arr; 
      $dataArr['target_pnl']['total'] = $target_pnl_total;

      $dataArr['pnl_disc']['dates'] = $pnl_disc_arr; 
      $dataArr['pnl_disc']['total'] = $pnl_disc_total;

      $dataArr['opex']['dates'] = $opex_arr; 
      $dataArr['opex']['total'] = $opex_total;

      $dataArr['target_opex']['dates'] = $target_opex_arr; 
      $dataArr['target_opex']['total'] = $target_opex_total;

      $dataArr['opex_disc']['dates'] = $opex_disc_arr; 
      $dataArr['opex_disc']['total'] = $opex_disc_total;

      $dataArr['ebida']['dates'] = $ebida_arr; 
      $dataArr['ebida']['total'] = $ebida_total;

      $dataArr['target_ebida']['dates'] = $target_ebida_arr; 
      $dataArr['target_ebida']['total'] = $target_ebida_total;

      $dataArr['ebida_disc']['dates'] = $ebida_disc_arr; 
      $dataArr['ebida_disc']['total'] = $ebida_disc_total;

      return $dataArr;
    }else{
      return [];
    }
  }

  public static function sumOfAllFinalCostData($sumemry)
  {
    if(!empty($sumemry))
    {
      $dataArr = []; 
      $cost_campaign_arr = [];
      $final_input_cost_camp_arr = [];
      $cost_campaign_disc_arr = [];
      $app_content_arr = [];
      $input_app_content_arr = [];
      $app_content_disc_arr = [];
      $cost_rnd_arr = [];
      $input_cost_rnd_arr = [];
      $cost_rnd_disc_arr = [];
      $fun_busket_arr = [];
      $input_fun_busket_arr = [];
      $fun_busket_disc_arr = [];
      $cost_bd_arr = [];
      $input_cost_bd_arr = [];
      $cost_bd_disc_arr = [];

      $cost_campaign_total = $final_input_cost_camp_total = $cost_campaign_disc_total = 0;
      $app_content_total = $input_app_content_total = $app_content_disc_total = 0;
      $cost_rnd_total = $input_cost_rnd_total = $cost_rnd_disc_total = 0;
      $fun_busket_total = $input_fun_busket_total = $fun_busket_disc_total = 0;
      $cost_bd_total = $input_cost_bd_total = $cost_bd_disc_total = 0;

      foreach ($sumemry as $sumemry_key => $sumemry_value)
      {
        $cost_campaign_total = $cost_campaign_total + (float)$sumemry_value['cost_campaign']['total'];
        $final_input_cost_camp_total = $final_input_cost_camp_total + (float)$sumemry_value['final_input_cost_campaign']['total'];
        $cost_campaign_disc_total = ($cost_campaign_total != 0 && $final_input_cost_camp_total != 0) ? (($final_input_cost_camp_total - $cost_campaign_total)/$cost_campaign_total)*100 : (float)0 ;

        $app_content_total = $app_content_total + (float)$sumemry_value['app_content']['total'];
        $input_app_content_total = $input_app_content_total + (float)$sumemry_value['input_app_content']['total'];
        $app_content_disc_total = ($app_content_total != 0 && $input_app_content_total != 0) ? (($input_app_content_total - $app_content_total)/$app_content_total)*100 : (float)0 ;

        $cost_rnd_total = $cost_rnd_total + (float)$sumemry_value['cost_rnd']['total'];
        $input_cost_rnd_total = $input_cost_rnd_total + (float)$sumemry_value['input_cost_rnd']['total'];
        $cost_rnd_disc_total = ($cost_rnd_total != 0 && $input_cost_rnd_total != 0) ? (($input_cost_rnd_total - $cost_rnd_total)/$cost_rnd_total)*100 : (float)0 ;

        $fun_busket_total = $fun_busket_total + (float)$sumemry_value['fun_busket']['total'];
        $input_fun_busket_total = $input_fun_busket_total + (float)$sumemry_value['input_fun_busket']['total'];
        $fun_busket_disc_total = ($fun_busket_total != 0 && $input_fun_busket_total != 0) ? (($input_fun_busket_total - $fun_busket_total)/$fun_busket_total)*100 : (float)0 ;

        $cost_bd_total = $cost_bd_total + (float)$sumemry_value['cost_bd']['total'];
        $input_cost_bd_total = $input_cost_bd_total + (float)$sumemry_value['input_cost_bd']['total'];
        $cost_bd_disc_total = ($cost_bd_total != 0 && $input_cost_bd_total != 0) ? (($input_cost_bd_total - $cost_bd_total)/$cost_bd_total)*100 : (float)0 ;

        foreach ($sumemry_value['cost_campaign']['dates'] as $cost_campaign_key => $cost_campaign_value)
        {
          if($sumemry_key == 0){
            $cost_campaign_sum[$cost_campaign_key] = 0;
          }

          $cost_campaign_sum[$cost_campaign_key] = $cost_campaign_sum[$cost_campaign_key] + (float)$cost_campaign_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $cost_campaign_arr[$cost_campaign_key] = ['value' => $cost_campaign_sum[$cost_campaign_key], 'class' => $cost_campaign_value['class']];
          }
        }

        foreach ($sumemry_value['final_input_cost_campaign']['dates'] as $final_input_cost_campaign_key => $final_input_cost_campaign_value)
        {
          if($sumemry_key == 0){
            $final_input_cost_campaign_sum[$final_input_cost_campaign_key] = 0;
          }

          $final_input_cost_campaign_sum[$final_input_cost_campaign_key] = $final_input_cost_campaign_sum[$final_input_cost_campaign_key] + (float)$final_input_cost_campaign_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $final_input_cost_camp_arr[$final_input_cost_campaign_key] = ['value' => $final_input_cost_campaign_sum[$final_input_cost_campaign_key], 'class' => $final_input_cost_campaign_value['class']];
          }
        }

        foreach ($sumemry_value['cost_campaign_discrepency']['dates'] as $cost_campaign_discrepency_key => $cost_campaign_discrepency_value) {
          if($sumemry_key == 0){
            $cost_campaign_discrepency_sum[$cost_campaign_discrepency_key] = 0;
          }

          $cost_campaign_discrepency_sum[$cost_campaign_discrepency_key] = $cost_campaign_discrepency_sum[$cost_campaign_discrepency_key] + (float)$cost_campaign_discrepency_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $cost_campaign_disc_arr[$cost_campaign_discrepency_key] = ['value' => $cost_campaign_discrepency_sum[$cost_campaign_discrepency_key], 'class' => $cost_campaign_discrepency_value['class']];
          }
        }

        foreach ($sumemry_value['app_content']['dates'] as $app_content_key => $app_content_value)
        {
          if($sumemry_key == 0){
            $app_content_sum[$app_content_key] = 0;
          }

          $app_content_sum[$app_content_key] = $app_content_sum[$app_content_key] + (float)$app_content_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $app_content_arr[$app_content_key] = ['value' => $app_content_sum[$app_content_key], 'class' => $app_content_value['class']];
          }
        }

        foreach ($sumemry_value['input_app_content']['dates'] as $input_app_content_key => $input_app_content_value)
        {
          if($sumemry_key == 0){
            $input_app_content_sum[$input_app_content_key] = 0;
          }

          $input_app_content_sum[$input_app_content_key] = $input_app_content_sum[$input_app_content_key] + (float)$input_app_content_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $input_app_content_arr[$input_app_content_key] = ['value' => $input_app_content_sum[$input_app_content_key], 'class' => $input_app_content_value['class']];
          }
        }

        foreach ($sumemry_value['app_content_discrepency']['dates'] as $app_content_discrepency_key => $app_content_discrepency_value) {
          if($sumemry_key == 0){
            $app_content_discrepency_sum[$app_content_discrepency_key] = 0;
          }

          $app_content_discrepency_sum[$app_content_discrepency_key] = $app_content_discrepency_sum[$app_content_discrepency_key] + (float)$app_content_discrepency_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $app_content_disc_arr[$app_content_discrepency_key] = ['value' => $app_content_discrepency_sum[$app_content_discrepency_key], 'class' => $app_content_discrepency_value['class']];
          }
        }

        foreach ($sumemry_value['cost_rnd']['dates'] as $cost_rnd_key => $cost_rnd_value)
        {
          if($sumemry_key == 0){
            $cost_rnd_sum[$cost_rnd_key] = 0;
          }

          $cost_rnd_sum[$cost_rnd_key] = $cost_rnd_sum[$cost_rnd_key] + (float)$cost_rnd_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $cost_rnd_arr[$cost_rnd_key] = ['value' => $cost_rnd_sum[$cost_rnd_key], 'class' => $cost_rnd_value['class']];
          }
        }

        foreach ($sumemry_value['input_cost_rnd']['dates'] as $input_cost_rnd_key => $input_cost_rnd_value)
        {
          if($sumemry_key == 0){
            $input_cost_rnd_sum[$input_cost_rnd_key] = 0;
          }

          $input_cost_rnd_sum[$input_cost_rnd_key] = $input_cost_rnd_sum[$input_cost_rnd_key] + (float)$input_cost_rnd_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $input_cost_rnd_arr[$input_cost_rnd_key] = ['value' => $input_cost_rnd_sum[$input_cost_rnd_key], 'class' => $input_cost_rnd_value['class']];
          }
        }

        foreach ($sumemry_value['cost_rnd_discrepency']['dates'] as $cost_rnd_discrepency_key => $cost_rnd_discrepency_value) {
          if($sumemry_key == 0){
            $cost_rnd_discrepency_sum[$cost_rnd_discrepency_key] = 0;
          }

          $cost_rnd_discrepency_sum[$cost_rnd_discrepency_key] = $cost_rnd_discrepency_sum[$cost_rnd_discrepency_key] + (float)$cost_rnd_discrepency_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $cost_rnd_disc_arr[$cost_rnd_discrepency_key] = ['value' => $cost_rnd_discrepency_sum[$cost_rnd_discrepency_key], 'class' => $cost_rnd_discrepency_value['class']];
          }
        }

        foreach ($sumemry_value['fun_busket']['dates'] as $fun_busket_key => $fun_busket_value)
        {
          if($sumemry_key == 0){
            $fun_busket_sum[$fun_busket_key] = 0;
          }

          $fun_busket_sum[$fun_busket_key] = $fun_busket_sum[$fun_busket_key] + (float)$fun_busket_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $fun_busket_arr[$fun_busket_key] = ['value' => $fun_busket_sum[$fun_busket_key], 'class' => $fun_busket_value['class']];
          }
        }

        foreach ($sumemry_value['input_fun_busket']['dates'] as $input_fun_busket_key => $input_fun_busket_value)
        {
          if($sumemry_key == 0){
            $input_fun_busket_sum[$input_fun_busket_key] = 0;
          }

          $input_fun_busket_sum[$input_fun_busket_key] = $input_fun_busket_sum[$input_fun_busket_key] + (float)$input_fun_busket_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $input_fun_busket_arr[$input_fun_busket_key] = ['value' => $input_fun_busket_sum[$input_fun_busket_key], 'class' => $input_fun_busket_value['class']];
          }
        }

        foreach ($sumemry_value['fun_busket_discrepency']['dates'] as $fun_busket_discrepency_key => $fun_busket_discrepency_value) {
          if($sumemry_key == 0){
            $fun_busket_discrepency_sum[$fun_busket_discrepency_key] = 0;
          }

          $fun_busket_discrepency_sum[$fun_busket_discrepency_key] = $fun_busket_discrepency_sum[$fun_busket_discrepency_key] + (float)$fun_busket_discrepency_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $fun_busket_disc_arr[$fun_busket_discrepency_key] = ['value' => $fun_busket_discrepency_sum[$fun_busket_discrepency_key], 'class' => $fun_busket_discrepency_value['class']];
          }
        }

        foreach ($sumemry_value['cost_bd']['dates'] as $cost_bd_key => $cost_bd_value)
        {
          if($sumemry_key == 0){
            $cost_bd_sum[$cost_bd_key] = 0;
          }

          $cost_bd_sum[$cost_bd_key] = $cost_bd_sum[$cost_bd_key] + (float)$cost_bd_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $cost_bd_arr[$cost_bd_key] = ['value' => $cost_bd_sum[$cost_bd_key], 'class' => $cost_bd_value['class']];
          }
        }

        foreach ($sumemry_value['input_cost_bd']['dates'] as $input_cost_bd_key => $input_cost_bd_value)
        {
          if($sumemry_key == 0){
            $input_cost_bd_sum[$input_cost_bd_key] = 0;
          }

          $input_cost_bd_sum[$input_cost_bd_key] = $input_cost_bd_sum[$input_cost_bd_key] + (float)$input_cost_bd_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $input_cost_bd_arr[$input_cost_bd_key] = ['value' => $input_cost_bd_sum[$input_cost_bd_key], 'class' => $input_cost_bd_value['class']];
          }
        }

        foreach ($sumemry_value['cost_bd_discrepency']['dates'] as $cost_bd_discrepency_key => $cost_bd_discrepency_value) {
          if($sumemry_key == 0){
            $cost_bd_discrepency_sum[$cost_bd_discrepency_key] = 0;
          }

          $cost_bd_discrepency_sum[$cost_bd_discrepency_key] = $cost_bd_discrepency_sum[$cost_bd_discrepency_key] + (float)$cost_bd_discrepency_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $cost_bd_disc_arr[$cost_bd_discrepency_key] = ['value' => $cost_bd_discrepency_sum[$cost_bd_discrepency_key], 'class' => $cost_bd_discrepency_value['class']];
          }
        }
      }

      $dataArr['cost_campaign']['dates'] = $cost_campaign_arr;                
      $dataArr['cost_campaign']['total'] = $cost_campaign_total;
      $dataArr['final_input_cost_camp']['dates'] = $final_input_cost_camp_arr;              
      $dataArr['final_input_cost_camp']['total'] = $final_input_cost_camp_total;
      $dataArr['cost_campaign_disc']['dates'] = $cost_campaign_disc_arr;                
      $dataArr['cost_campaign_disc']['total'] = $cost_campaign_disc_total;

      $dataArr['app_content']['dates'] = $app_content_arr;                
      $dataArr['app_content']['total'] = $app_content_total;
      $dataArr['input_app_content']['dates'] = $input_app_content_arr;              
      $dataArr['input_app_content']['total'] = $input_app_content_total;
      $dataArr['app_content_disc']['dates'] = $app_content_disc_arr;                
      $dataArr['app_content_disc']['total'] = $app_content_disc_total;

      $dataArr['cost_rnd']['dates'] = $cost_rnd_arr;                
      $dataArr['cost_rnd']['total'] = $cost_rnd_total;
      $dataArr['input_cost_rnd']['dates'] = $input_cost_rnd_arr;              
      $dataArr['input_cost_rnd']['total'] = $input_cost_rnd_total;
      $dataArr['cost_rnd_disc']['dates'] = $cost_rnd_disc_arr;                
      $dataArr['cost_rnd_disc']['total'] = $cost_rnd_disc_total;

      $dataArr['fun_busket']['dates'] = $fun_busket_arr;                
      $dataArr['fun_busket']['total'] = $fun_busket_total;
      $dataArr['input_fun_busket']['dates'] = $input_fun_busket_arr;              
      $dataArr['input_fun_busket']['total'] = $input_fun_busket_total;
      $dataArr['fun_busket_disc']['dates'] = $fun_busket_disc_arr;                
      $dataArr['fun_busket_disc']['total'] = $fun_busket_disc_total;

      $dataArr['cost_bd']['dates'] = $cost_bd_arr;                
      $dataArr['cost_bd']['total'] = $cost_bd_total;
      $dataArr['input_cost_bd']['dates'] = $input_cost_bd_arr;              
      $dataArr['input_cost_bd']['total'] = $input_cost_bd_total;
      $dataArr['cost_bd_disc']['dates'] = $cost_bd_disc_arr;                
      $dataArr['cost_bd_disc']['total'] = $cost_bd_disc_total;

      return $dataArr;
    }else{
      return [];
    }
  } 
}
