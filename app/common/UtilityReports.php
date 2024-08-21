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
use App\common\UtilityPercentage;

class UtilityReports
{
  // USD calculate for Specific Country and Operator Like Oman ,Kuwit , CellCard ,Mobifone
  public static function UsdCalCriteria($local_rev,$exchange_rate,$data,$country,$days,$type = 'daily')
  {
    $countryCode = $country['country_code'];

    $Gross_local_Revenue = $local_rev;

    // operator_id comes from ReportController
    // id_opeator comes from PNLReportController
    $id_operator = isset($data['operator_id']) ? $data['operator_id'] : $data['id_operator'];

    $usdValue = $Gross_local_Revenue * $exchange_rate;

    return $usdValue;
  }

  /* revenue calculation for mobifone operator 29

  Author : Utpal */
  public static function getMobifoneRevenue($id_operator, $days, $type, $service_historys = array())
  {
    $summarydata = [];
    $sumGrossRev = 0;

    $day = $days['date'];

    if($type == 'daily'){
      if(empty($service_historys))
      {
        $service_historys = ServiceHistory::FilterOperator($id_operator)->filterDate($day)->get();
      }
    }
    else if($type == 'monthly')
    {
      $month = $days['date'];
      $service_historys = ServiceHistory::FilterOperator($id_operator)->filterMonth($month)->get();
    }

    $servicesData = [];
    if(!$service_historys)
    {
      if (!array_key_exists( $day,$service_historys)) {
        return 0;
      }

      $servicesData = $service_historys[$day];
    }

    if(isset($servicesData) && !empty($servicesData)){
      foreach ($servicesData as $service) {
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

        $total_gros_rev = $total_gros_rev +  $gros_rev;

        $total_reg = isset($summariserow['total_reg']) ? $summariserow['total_reg'] : 0;

        $total_total_reg = $total_total_reg + $total_reg;
      }

      $R1 = $total_gros_rev * $merchent_share ;
      $R2 = $total_total_reg;
      $R3 = $R2 + $total_subscriber ;

      if($R3 > 0)
      {
        $arpu = $R1 / $R3 ;
      }
    }

    return $arpu;
  }

  public static function Arpu7USD($operator,$reportsByIDs,$days,$total_subscriber,$share)
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

        $gros_rev = isset($summariserow['rev']) ? $summariserow['rev'] : 0;

        $total_gros_rev = $total_gros_rev +  $gros_rev;

        $total_reg = isset($summariserow['total_reg']) ? $summariserow['total_reg'] : 0;

        $total_total_reg = $total_total_reg + $total_reg;
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

        $total_gros_rev = $total_gros_rev + $gros_rev;

        $total_reg = isset($summariserow['total_reg']) ? $summariserow['total_reg'] : 0;

        $total_total_reg = $total_total_reg + $total_reg;
      }

      $R1 = $total_gros_rev * $merchent_share;
      $R2 = $total_total_reg;
      $R3 = $R2 + $total_subscriber;

      if($R3 > 0)
      {
        $arpu = $R1 / $R3 ;
      }
    }

    return $arpu;
  }

  public static function Arpu30USD($operator,$reportsByIDs,$days,$total_subscriber,$share)
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

        $gros_rev = isset($summariserow['rev']) ? $summariserow['rev'] : 0;

        $total_gros_rev = $total_gros_rev + $gros_rev;

        $total_reg = isset($summariserow['reg']) ? $summariserow['reg'] : 0;

        $total_total_reg = $total_total_reg + $total_reg;
      }

      $R1 = $total_gros_rev * $merchent_share ;
      $R2 = $total_total_reg;
      $R3 = $R2 + $total_subscriber ;

      if($R3 > 0)
      {
        $arpu = $R1 / $R3 ;
      }
    }

    return $arpu;
  }

  public static function ROI($id_operator,$reportsByIDs,$days,$total_subscriber,$cost_campaign,$mo)
  {
    $ROI = [];
    $roi = 0;
    $arpu_30 = 0;
    $price_mo = 0;

    $day = $days['date'];

    $day_date = new Carbon($day);
    $day_date_arpu = new Carbon($day);
    $last_date_cal = new Carbon($day);

    $first_day = $day_date->subDays(30)->format('Y-m-d');
    $last_day = $last_date_cal->subDays(1)->format('Y-m-d');

    $arpu_7_first_day = $day_date_arpu->subDays(7)->format('Y-m-d');

    $datesIndividual = Utility::getRangeDates($first_day,$last_day);
    $datesIndividualArpu = Utility::getRangeDates($arpu_7_first_day,$last_day);

    $no_of_days = Utility::getRangeDateNo($datesIndividual);
    $no_of_days_arpu = Utility::getRangeDateNo($datesIndividualArpu);

    $total_gros_rev_usd = 0;
    $total_reg = 0;
    $total_cost_campaign = 0;
    $total_mo = 0;

    if(!empty($no_of_days))
    {
      foreach($no_of_days as $days)
      {
        $keys = $id_operator.".".$days['date'];

        $summariserow = Arr::get($reportsByIDs, $keys, 0);

        $gros_rev_usd = isset($summariserow['share']) ? $summariserow['share'] : 0;

        $total_gros_rev_usd = $total_gros_rev_usd + $gros_rev_usd;

        $reg = isset($summariserow['reg']) ? $summariserow['reg'] : 0;

        $total_reg = $total_reg + $reg;
      }

      $R1 = $total_gros_rev_usd;
      $R2 = $total_reg;
      $R3 = $R2 + $total_subscriber ;
      $R4 = $cost_campaign;
      $R5 = $mo;

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

      $ROI['last_30_gros_rev'] = $R1;
      $ROI['last_30_reg'] = $R2;
      $ROI['roi'] = $roi;
    }

    $total_gros_rev_usd_arpu = 0;
    $total_reg_arpu = 0;

    if(!empty($no_of_days_arpu))
    {
      foreach($no_of_days_arpu as $days)
      {
        $keys = $id_operator.".".$days['date'];

        $summariserow = Arr::get($reportsByIDs, $keys, 0);

        $gros_rev_usd = isset($summariserow['share']) ? $summariserow['share'] : 0;

        $total_gros_rev_usd_arpu = $total_gros_rev_usd_arpu + $gros_rev_usd;

        $reg = isset($summariserow['reg']) ? $summariserow['reg'] : 0;

        $total_reg_arpu = $total_reg_arpu + $reg;
      }

      $ROI['last_7_gros_rev'] = $total_gros_rev_usd_arpu;
      $ROI['last_7_reg'] = $total_reg_arpu;
    }

    return $ROI;
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
    }
    else
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
    }
    else
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

    $dayoftodays = (int)Carbon::now()->format('d');
    $dayoftoday = $dayoftodays-1;

    $totalDayofmonth = Carbon::now()->daysInMonth;

    $reamingday = $totalDayofmonth - $dayoftoday;

    return  $reamingday;
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

    // Need to check when date filter applied

    $daysinMonth = Carbon::parse($end_date)->daysInMonth;

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

        $sum = $sum+$value['value'];
      }

      if($count>0)
      {
        $avg = $sum/$count;
      }

      if($count>0)
      {
        $T_Mo_End = $avg * $daysinMonth;
      }
    }

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
    $calculateDayforSubscription = Carbon::now()->format('Y-m-d');

    $firstdayOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');
    $created = new Carbon($start_date);
    $created_format = $created->format('Y-m-d');
    $dayscount = ($created->diff($end_date));

    // if not select Date range
    if($created_format == $firstdayOfMonth)
    {
      $reaming_day = Carbon::now()->daysInMonth;
      $reaming_day = $reaming_day-(count($data) - 1);
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

      if($count>0)
      {
        $avg = $sum/$count;
      }

      if($count>0)
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

    return $result;
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
              $class = "bg-success text-white";
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
    $net_rev_sum = $net_rev_arr = [];
    $t_sub_sum = $t_sub_arr = [];
    $reg_sum = $reg_arr = [];
    $unreg_sum = $unreg_arr = [];
    $purged_sum = $purged_arr = [];
    $churn_sum = $churn_arr = [];
    $renewal_sum = $renewal_arr = [];
    $daily_push_success_sum = $daily_push_success_arr = [];
    $daily_push_failed_sum = $daily_push_failed_arr = [];
    $bill_sum = $bill_arr = [];
    $first_push_sum = $first_push_arr = [];
    $daily_push_sum = $daily_push_arr = [];
    $arpu7_sum = $arpu7_arr = [];
    $usarpu7_sum = $usarpu7_arr = [];
    $arpu30_sum = $arpu30_arr = [];
    $usarpu30_sum = $usarpu30_arr = [];
    $mt_success_sum = $mt_success_arr = [];
    $mt_failed_sum = $mt_failed_arr = [];
    $fmt_success_sum = $fmt_success_arr = [];
    $fmt_failed_sum = $fmt_failed_arr = [];
    $first_day_active_sum = $first_day_active_arr = [];
    $cost_campaign_sum = $cost_campaign_arr = [];
    $ltv_sum = $ltv_arr = [];
    $arpu7raw_arr = [];
    $arpu30raw_arr = [];

    $tur_total = $tur_t_mo_end = $tur_avg = 0;
    $t_rev_total = $t_rev_t_mo_end = $t_rev_avg = 0;
    $trat_total = $trat_t_mo_end = $trat_avg = 0;
    $turt_total = $turt_t_mo_end = $turt_avg = 0;
    $net_rev_total = $net_rev_t_mo_end = $net_rev_avg = 0;
    $t_sub_total = $t_sub_t_mo_end = $t_sub_avg = 0;
    $reg_total = $reg_t_mo_end = $reg_avg = 0;
    $unreg_total = $unreg_t_mo_end = $unreg_avg = 0;
    $purged_total = $purged_t_mo_end = $purged_avg = 0;
    $churn_total = $churn_t_mo_end = $churn_avg = 0;
    $renewal_total = $renewal_t_mo_end = $renewal_avg = 0;
    $daily_push_success_total = $daily_push_success_t_mo_end = $daily_push_success_avg = 0;
    $daily_push_failed_total = $daily_push_failed_t_mo_end = $daily_push_failed_avg = 0;
    $bill_total = $bill_t_mo_end = $bill_avg = 0;
    $first_push_total = $first_push_t_mo_end = $first_push_avg = 0;
    $daily_push_total = $daily_push_t_mo_end = $daily_push_avg = 0;
    $arpu7_total = $arpu7_t_mo_end = $arpu7_avg = 0;
    $usarpu7_total = $usarpu7_t_mo_end = $usarpu7_avg = 0;
    $arpu30_total = $arpu30_t_mo_end = $arpu30_avg = 0;
    $usarpu30_total = $usarpu30_t_mo_end = $usarpu30_avg = 0;
    $first_day_active_total = $first_day_active_t_mo_end = $first_day_active_avg = 0;
    $cost_campaign_total = $cost_campaign_t_mo_end = $cost_campaign_avg = 0;
    $ltv_total = $ltv_t_mo_end = $ltv_avg = 0;

    if(!empty($sumemry)){
      foreach ($sumemry as $sumemry_key => $sumemry_value)
      {
        $tur_total = $tur_total + (float)$sumemry_value['tur']['total'];
        $tur_t_mo_end = $tur_t_mo_end + (float)$sumemry_value['tur']['t_mo_end'];
        $tur_avg = $tur_avg + (float)$sumemry_value['tur']['avg'];

        $t_rev_total = $t_rev_total + (float)$sumemry_value['t_rev']['total']; // REV for all Country in USD
        $t_rev_t_mo_end = $t_rev_t_mo_end + (float)$sumemry_value['t_rev']['t_mo_end'];
        $t_rev_avg = $t_rev_avg + (float)$sumemry_value['t_rev']['avg'];

        $trat_total = $trat_total + (float)$sumemry_value['trat']['total'];
        $trat_t_mo_end = $trat_t_mo_end + (float)$sumemry_value['trat']['t_mo_end'];
        $trat_avg = $trat_avg + (float)$sumemry_value['trat']['avg'];

        $turt_total = $turt_total + (float)$sumemry_value['turt']['total'];
        $turt_t_mo_end = $turt_t_mo_end + (float)$sumemry_value['turt']['t_mo_end'];
        $turt_avg = $turt_avg + (float)$sumemry_value['turt']['avg'];

        $net_rev_total = $net_rev_total + (float)$sumemry_value['net_rev']['total'];
        $net_rev_t_mo_end = $net_rev_t_mo_end + (float)$sumemry_value['net_rev']['t_mo_end'];
        $net_rev_avg = $net_rev_avg + (float)$sumemry_value['net_rev']['avg'];

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

        $daily_push_success_total = $daily_push_success_total + (float)$sumemry_value['daily_push_success']['total'];
        $daily_push_success_t_mo_end = $daily_push_success_t_mo_end + (float)$sumemry_value['daily_push_success']['t_mo_end'];
        $daily_push_success_avg = $daily_push_success_avg + (float)$sumemry_value['daily_push_success']['avg'];

        $daily_push_failed_total = $daily_push_failed_total + (float)$sumemry_value['daily_push_failed']['total'];
        $daily_push_failed_t_mo_end = $daily_push_failed_t_mo_end + (float)$sumemry_value['daily_push_failed']['t_mo_end'];
        $daily_push_failed_avg = $daily_push_failed_avg + (float)$sumemry_value['daily_push_failed']['avg'];

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

        $first_day_active_total = $first_day_active_total + (float)$sumemry_value['first_day_active']['total'];
        $first_day_active_t_mo_end = $first_day_active_t_mo_end + (float)$sumemry_value['first_day_active']['t_mo_end'];
        $first_day_active_avg = $first_day_active_avg + (float)$sumemry_value['first_day_active']['avg'];

        $cost_campaign_total = $cost_campaign_total + (float)$sumemry_value['cost_campaign']['total'];
        $cost_campaign_t_mo_end = $cost_campaign_t_mo_end + (float)$sumemry_value['cost_campaign']['t_mo_end'];
        $cost_campaign_avg = $cost_campaign_avg + (float)$sumemry_value['cost_campaign']['avg'];

        $ltv_total = $ltv_total + (float)$sumemry_value['ltv']['total'];
        $ltv_t_mo_end = $ltv_t_mo_end + (float)$sumemry_value['ltv']['t_mo_end'];
        $ltv_avg = $ltv_avg + (float)$sumemry_value['ltv']['avg'];

        foreach ($sumemry_value['tur']['dates'] as $tur_key => $tur_value) {
          if(!isset($tur_arr[$tur_key]['value']))
          {
            $tur_arr[$tur_key]['value'] = 0;
          }

          $tur_arr[$tur_key]['value'] = $tur_arr[$tur_key]['value'] + $tur_value['value'];

          $tur_arr[$tur_key]['class'] = "";
        }

        foreach ($sumemry_value['t_rev']['dates'] as $t_rev_key => $t_rev_value) {
          if(!isset($t_rev_arr[$t_rev_key]['value']))
          {
            $t_rev_arr[$t_rev_key]['value'] = 0;
          }

          $t_rev_arr[$t_rev_key]['value'] = $t_rev_arr[$t_rev_key]['value'] + $t_rev_value['value'];
          $t_rev_arr[$t_rev_key]['class'] = "";
        }

        foreach ($sumemry_value['trat']['dates'] as $trat_key => $trat_value) {
          if($sumemry_key == 0)
          {
            $trat_sum[$trat_key] = 0;
          }

          $trat_sum[$trat_key] = $trat_sum[$trat_key] + (float)$trat_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $trat_arr[$trat_key] = ['value' => $trat_sum[$trat_key], 'class' => $trat_value['class']];
          }
        }

        foreach ($sumemry_value['turt']['dates'] as $turt_key => $turt_value)
        {
          if(!isset($turt_arr[$turt_key]['value']))
          {
            $turt_arr[$turt_key]['value'] = 0;
          }

          $turt_arr[$turt_key]['value'] = $turt_arr[$turt_key]['value'] + $turt_value['value'];

          $turt_arr[$turt_key]['class'] = "";
        }

        foreach ($sumemry_value['net_rev']['dates'] as $net_rev_key => $net_rev_value)
        {
          if(!isset($net_rev_arr[$net_rev_key]['value']))
          {
            $net_rev_arr[$net_rev_key]['value'] = 0;
          }

          $net_rev_arr[$net_rev_key]['value'] = $net_rev_arr[$net_rev_key]['value'] + $net_rev_value['value'];

          $net_rev_arr[$net_rev_key]['class'] = "";
        }

        foreach ($sumemry_value['t_sub']['dates'] as $t_sub_key => $t_sub_value) {
          if($sumemry_key == 0)
          {
            $t_sub_sum[$t_sub_key] = 0;
          }

          $t_sub_sum[$t_sub_key] = $t_sub_sum[$t_sub_key] + (float)$t_sub_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $t_sub_arr[$t_sub_key] = ['value' => $t_sub_sum[$t_sub_key], 'class' => $t_sub_value['class']];
          }
        }

        foreach ($sumemry_value['reg']['dates'] as $reg_key => $reg_value) {
          if($sumemry_key == 0)
          {
            $reg_sum[$reg_key] = 0;
          }

          $reg_sum[$reg_key] = $reg_sum[$reg_key] + (float)$reg_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $reg_arr[$reg_key] = ['value' => $reg_sum[$reg_key], 'class' => $reg_value['class']];
          }
        }

        foreach ($sumemry_value['unreg']['dates'] as $unreg_key => $unreg_value) {
          if($sumemry_key == 0)
          {
            $unreg_sum[$unreg_key] = 0;
          }

          $unreg_sum[$unreg_key] = $unreg_sum[$unreg_key] + (float)$unreg_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $unreg_arr[$unreg_key] = ['value' => $unreg_sum[$unreg_key], 'class' => $unreg_value['class']];
          }
        }

        foreach ($sumemry_value['purged']['dates'] as $purged_key => $purged_value) {
          if($sumemry_key == 0)
          {
            $purged_sum[$purged_key] = 0;
          }

          $purged_sum[$purged_key] = $purged_sum[$purged_key] + (float)$purged_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $purged_arr[$purged_key] = ['value' => $purged_sum[$purged_key], 'class' => $purged_value['class']];
          }
        }

        foreach ($sumemry_value['churn']['dates'] as $churn_key => $churn_value) {
          if($sumemry_key == 0)
          {
            $churn_sum[$churn_key] = 0;
          }

          $churn_sum[$churn_key] = $churn_sum[$churn_key] + (float)$churn_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $churn_arr[$churn_key] = ['value' => $churn_sum[$churn_key], 'class' => $churn_value['class']];
          }
        }

        foreach ($sumemry_value['renewal']['dates'] as $renewal_key => $renewal_value) {
          if($sumemry_key == 0)
          {
            $renewal_sum[$renewal_key] = 0;
          }

          $renewal_sum[$renewal_key] = $renewal_sum[$renewal_key] + (float)$renewal_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $renewal_arr[$renewal_key] = ['value' => $renewal_sum[$renewal_key], 'class' => $renewal_value['class']];
          }
        }

        foreach ($sumemry_value['daily_push_success']['dates'] as $daily_push_success_key => $daily_push_success_value) {
          if($sumemry_key == 0)
          {
            $daily_push_success_sum[$daily_push_success_key] = 0;
          }

          $daily_push_success_sum[$daily_push_success_key] = $daily_push_success_sum[$daily_push_success_key] + (float)$daily_push_success_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $daily_push_success_arr[$daily_push_success_key] = ['value' => $daily_push_success_sum[$daily_push_success_key], 'class' => $daily_push_success_value['class']];
          }
        }

        foreach ($sumemry_value['daily_push_failed']['dates'] as $daily_push_failed_key => $daily_push_failed_value) {
          if($sumemry_key == 0)
          {
            $daily_push_failed_sum[$daily_push_failed_key] = 0;
          }

          $daily_push_failed_sum[$daily_push_failed_key] = $daily_push_failed_sum[$daily_push_failed_key] + (float)$daily_push_failed_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $daily_push_failed_arr[$daily_push_failed_key] = ['value' => $daily_push_failed_sum[$daily_push_failed_key], 'class' => $daily_push_failed_value['class']];
          }
        }

        foreach ($sumemry_value['bill']['dates'] as $bill_key => $bill_value) {
          if($sumemry_key == 0)
          {
            $bill_sum[$bill_key] = 0;
          }

          $bill_sum[$bill_key] = $bill_sum[$bill_key] + (float)$bill_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $bill_arr[$bill_key] = ['value' => $bill_sum[$bill_key], 'class' => $bill_value['class']];
          }
        }

        foreach ($sumemry_value['first_push']['dates'] as $first_push_key => $first_push_value) {
          if($sumemry_key == 0)
          {
            $first_push_sum[$first_push_key] = 0;
          }

          $first_push_sum[$first_push_key] = $first_push_sum[$first_push_key] + (float)$first_push_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $first_push_arr[$first_push_key] = ['value' => $first_push_sum[$first_push_key], 'class' => $first_push_value['class']];
          }
        }

        foreach ($sumemry_value['daily_push']['dates'] as $daily_push_key => $daily_push_value) {
          if($sumemry_key == 0)
          {
            $daily_push_sum[$daily_push_key] = 0;
          }

          $daily_push_sum[$daily_push_key] = $daily_push_sum[$daily_push_key] + (float)$daily_push_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $daily_push_arr[$daily_push_key] = ['value' => $daily_push_sum[$daily_push_key], 'class' => $daily_push_value['class']];
          }
        }

        foreach ($sumemry_value['arpu7']['dates'] as $arpu7_key => $arpu7_value) {
          if($sumemry_key == 0)
          {
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

        foreach ($sumemry_value['mt_success']['dates'] as $mt_success_key => $mt_success_value) {
          if($sumemry_key == 0){
            $mt_success_sum[$mt_success_key] = 0;
          }

          $mt_success_sum[$mt_success_key] = $mt_success_sum[$mt_success_key] + (float)$mt_success_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $mt_success_arr[$mt_success_key] = ['value' => $mt_success_sum[$mt_success_key], 'class' => $mt_success_value['class']];
          }
        }

        foreach ($sumemry_value['mt_failed']['dates'] as $mt_failed_key => $mt_failed_value) {
          if($sumemry_key == 0){
              $mt_failed_sum[$mt_failed_key] = 0;
          }

          $mt_failed_sum[$mt_failed_key] = $mt_failed_sum[$mt_failed_key] + (float)$mt_failed_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
              $mt_failed_arr[$mt_failed_key] = ['value' => $mt_failed_sum[$mt_failed_key], 'class' => $mt_failed_value['class']];
          }
        }

        foreach ($sumemry_value['fmt_success']['dates'] as $fmt_success_key => $fmt_success_value) {
          if($sumemry_key == 0){
              $fmt_success_sum[$fmt_success_key] = 0;
          }

          $fmt_success_sum[$fmt_success_key] = $fmt_success_sum[$fmt_success_key] + (float)$fmt_success_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
              $fmt_success_arr[$fmt_success_key] = ['value' => $fmt_success_sum[$fmt_success_key], 'class' => $fmt_success_value['class']];
          }
        }

        foreach ($sumemry_value['fmt_failed']['dates'] as $fmt_failed_key => $fmt_failed_value) {
          if($sumemry_key == 0){
            $fmt_failed_sum[$fmt_failed_key] = 0;
          }

          $fmt_failed_sum[$fmt_failed_key] = $fmt_failed_sum[$fmt_failed_key] + (float)$fmt_failed_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $fmt_failed_arr[$fmt_failed_key] = ['value' => $fmt_failed_sum[$fmt_failed_key], 'class' => $fmt_failed_value['class']];
          }
        }

        foreach ($sumemry_value['first_day_active']['dates'] as $first_day_active_key => $first_day_active_value) {
            if($sumemry_key==0){
                $first_day_active_sum[$first_day_active_key]=0;
            }

            $first_day_active_sum[$first_day_active_key] = $first_day_active_sum[$first_day_active_key] + (float)$first_day_active_value['value'];

            if(count($sumemry)-1 == $sumemry_key)
            {
                $first_day_active_arr[$first_day_active_key] = ['value' => $first_day_active_sum[$first_day_active_key], 'class' => $first_day_active_value['class']];
            }
        }

        foreach ($sumemry_value['cost_campaign']['dates'] as $cost_campaign_key => $cost_campaign_value) {
            if($sumemry_key==0){
                $cost_campaign_sum[$cost_campaign_key]=0;
            }

            $cost_campaign_sum[$cost_campaign_key] = $cost_campaign_sum[$cost_campaign_key] + (float)$cost_campaign_value['value'];

            if(count($sumemry)-1 == $sumemry_key)
            {
                $cost_campaign_arr[$cost_campaign_key] = ['value' => $cost_campaign_sum[$cost_campaign_key], 'class' => $cost_campaign_value['class']];
            }
        }

        foreach ($sumemry_value['ltv']['dates'] as $ltv_key => $ltv_value) {
            if($sumemry_key==0){
                $ltv_sum[$ltv_key]=0;
            }

            $ltv_sum[$ltv_key] = $ltv_sum[$ltv_key] + (float)$ltv_value['value'];

            if(count($sumemry)-1 == $sumemry_key)
            {
                $ltv_arr[$ltv_key] = ['value' => $ltv_sum[$ltv_key], 'class' => $ltv_value['class']];
            }
        }

        if(isset($sumemry_value['arpu7raw'])){
         foreach($sumemry_value['arpu7raw']['dates'] as $arpu7raw_key => $arpu7raw_value) {
            if(!isset($arpu7raw_arr[$arpu7raw_key]['value']))
            {
              $arpu7raw_arr[$arpu7raw_key]['value']['total_gross_revusd'] = 0;
              $arpu7raw_arr[$arpu7raw_key]['value']['total_total_reg'] = 0;
            }

            $arpu7raw_arr[$arpu7raw_key]['value']['total_gross_revusd'] = $arpu7raw_arr[$arpu7raw_key]['value']['total_gross_revusd'] + $arpu7raw_value['value']['total_gross_revusd'];

            $arpu7raw_arr[$arpu7raw_key]['value']['total_total_reg'] = $arpu7raw_arr[$arpu7raw_key]['value']['total_total_reg'] + $arpu7raw_value['value']['total_total_reg'];

            $arpu7raw_arr[$arpu7raw_key]['class'] = "";
          }
        }else{
          $arpu7raw_arr = [];
        }

        if(isset($sumemry_value['arpu30raw'])){
          foreach($sumemry_value['arpu30raw']['dates'] as $arpu30raw_key => $arpu30raw_value) {
            if(!isset($arpu30raw_arr[$arpu30raw_key]['value']))
            {
              $arpu30raw_arr[$arpu30raw_key]['value']['total_gross_revusd'] = 0;
              $arpu30raw_arr[$arpu30raw_key]['value']['total_total_reg'] = 0;
            }

            $arpu30raw_arr[$arpu30raw_key]['value']['total_gross_revusd'] = $arpu30raw_arr[$arpu30raw_key]['value']['total_gross_revusd'] + $arpu30raw_value['value']['total_gross_revusd'];

            $arpu30raw_arr[$arpu30raw_key]['value']['total_total_reg'] = $arpu30raw_arr[$arpu30raw_key]['value']['total_total_reg'] + $arpu30raw_value['value']['total_total_reg'];

            $arpu30raw_arr[$arpu30raw_key]['class'] = "";
          }
        }else{
          $arpu30raw_arr = [];
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

    $dataArr['net_rev']['dates'] = $net_rev_arr;
    $dataArr['net_rev']['total'] = $net_rev_total;
    $dataArr['net_rev']['t_mo_end'] = $net_rev_t_mo_end;
    $dataArr['net_rev']['avg'] = $net_rev_avg;

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

    $dataArr['daily_push_success']['dates'] = $daily_push_success_arr;
    $dataArr['daily_push_success']['total'] = $daily_push_success_total;
    $dataArr['daily_push_success']['t_mo_end'] = $daily_push_success_t_mo_end;
    $dataArr['daily_push_success']['avg'] = $daily_push_success_avg;

    $dataArr['daily_push_failed']['dates'] = $daily_push_failed_arr;
    $dataArr['daily_push_failed']['total'] = $daily_push_failed_total;
    $dataArr['daily_push_failed']['t_mo_end'] = $daily_push_failed_t_mo_end;
    $dataArr['daily_push_failed']['avg'] = $daily_push_failed_avg;

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

    $dataArr['arpu7raw']['dates'] = $arpu7raw_arr;
    $dataArr['arpu7raw']['total'] = 0;
    $dataArr['arpu7raw']['t_mo_end'] = 0;
    $dataArr['arpu7raw']['avg'] = 0;

    $dataArr['arpu30raw']['dates'] = $arpu30raw_arr;
    $dataArr['arpu30raw']['total'] = 0;
    $dataArr['arpu30raw']['t_mo_end'] = 0;
    $dataArr['arpu30raw']['avg'] = 0;

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

    $dataArr['first_day_active']['dates'] = $first_day_active_arr;
    $dataArr['first_day_active']['total'] = $first_day_active_total;
    $dataArr['first_day_active']['t_mo_end'] = $first_day_active_t_mo_end;
    $dataArr['first_day_active']['avg'] = $first_day_active_avg;

    $dataArr['cost_campaign']['dates'] = $cost_campaign_arr;
    $dataArr['cost_campaign']['total'] = $cost_campaign_total;
    $dataArr['cost_campaign']['t_mo_end'] = $cost_campaign_t_mo_end;
    $dataArr['cost_campaign']['avg'] = $cost_campaign_avg;

    $dataArr['ltv']['dates'] = $ltv_arr;
    $dataArr['ltv']['total'] = $ltv_total;
    $dataArr['ltv']['t_mo_end'] = $ltv_t_mo_end;
    $dataArr['ltv']['avg'] = $ltv_avg;

    $dataArr['month_string'] = $sumemry_value['month_string'];

    $dataArr['mt_success']['dates'] = $mt_success_arr;
    $dataArr['mt_failed']['dates'] = $mt_failed_arr;

    $dataArr['fmt_success']['dates'] = $fmt_success_arr;
    $dataArr['fmt_failed']['dates'] = $fmt_failed_arr;

    return $dataArr;
  }

  public static function alldetailsData($sumemry)
  {
    $tur_sum = $tur_arr = [];
    $t_rev_sum = $t_rev_arr = [];
    $net_rev_sum = $net_rev_arr = [];
    $trat_sum = $trat_arr = [];
    $turt_sum = $turt_arr = [];
    $t_sub_sum = $t_sub_arr = [];
    $reg_sum = $reg_arr = [];
    $unreg_sum = $unreg_arr = [];
    $purged_sum = $purged_arr = [];
    $churn_sum = $churn_arr = [];
    $renewal_sum = $renewal_arr = [];
    $daily_push_success_sum = $daily_push_success_arr = [];
    $daily_push_failed_sum = $daily_push_failed_arr = [];
    $bill_sum = $bill_arr = [];
    $first_push_sum = $first_push_arr = [];
    $daily_push_sum = $daily_push_arr = [];
    $arpu7_sum = $arpu7_arr = [];
    $usarpu7_sum = $usarpu7_arr = [];
    $arpu30_sum = $arpu30_arr = [];
    $usarpu30_sum = $usarpu30_arr = [];
    $mt_success_sum = $mt_success_arr = [];
    $mt_failed_sum = $mt_failed_arr = [];
    $fmt_success_sum = $fmt_success_arr = [];
    $fmt_failed_sum = $fmt_failed_arr = [];
    $first_day_active_sum = $first_day_active_arr = [];
    $cost_campaign_sum = $cost_campaign_arr = [];
    $ltv_sum = $ltv_arr = [];
    $arpu7raw_arr = [];
    $arpu30raw_arr = [];

    $tur_total = $tur_t_mo_end = $tur_avg = 0;
    $t_rev_total = $t_rev_t_mo_end = $t_rev_avg = 0;
    $net_rev_total = $net_rev_t_mo_end = $net_rev_avg = 0;

    $trat_total = $trat_t_mo_end = $trat_avg = 0;
    $turt_total = $turt_t_mo_end = $turt_avg = 0;
    $t_sub_total = $t_sub_t_mo_end = $t_sub_avg = 0;
    $reg_total = $reg_t_mo_end = $reg_avg = 0;
    $unreg_total = $unreg_t_mo_end = $unreg_avg = 0;
    $purged_total = $purged_t_mo_end = $purged_avg = 0;
    $churn_total = $churn_t_mo_end = $churn_avg = 0;
    $renewal_total = $renewal_t_mo_end = $renewal_avg = 0;
    $daily_push_success_total = $daily_push_success_t_mo_end = $daily_push_success_avg = 0;
    $daily_push_failed_total = $daily_push_failed_t_mo_end = $daily_push_failed_avg = 0;
    $bill_total = $bill_t_mo_end = $bill_avg = 0;
    $first_push_total = $first_push_t_mo_end = $first_push_avg = 0;
    $daily_push_total = $daily_push_t_mo_end = $daily_push_avg = 0;
    $arpu7_total = $arpu7_t_mo_end = $arpu7_avg = 0;
    $usarpu7_total = $usarpu7_t_mo_end = $usarpu7_avg = 0;
    $arpu30_total = $arpu30_t_mo_end = $arpu30_avg = 0;
    $usarpu30_total = $usarpu30_t_mo_end = $usarpu30_avg = 0;

    if(!empty($sumemry)){
      foreach ($sumemry as $sumemry_key => $sumemry_value)
      {
        $tur_total = $tur_total + (float)$sumemry_value['tur']['total'];
        $tur_t_mo_end = $tur_t_mo_end + (float)$sumemry_value['tur']['t_mo_end'];
        $tur_avg = $tur_avg + (float)$sumemry_value['tur']['avg'];

        $t_rev_total = $t_rev_total + (float)$sumemry_value['t_rev']['total']; // REV for all Country in USD
        $t_rev_t_mo_end = $t_rev_t_mo_end + (float)$sumemry_value['t_rev']['t_mo_end'];
        $t_rev_avg = $t_rev_avg + (float)$sumemry_value['t_rev']['avg'];

        $net_rev_total = $net_rev_total + (float)$sumemry_value['net_rev']['total']; // REV for all Country in USD
        $net_rev_t_mo_end = $net_rev_t_mo_end + (float)$sumemry_value['net_rev']['t_mo_end'];
        $net_rev_avg = $net_rev_avg + (float)$sumemry_value['net_rev']['avg'];

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

        $daily_push_success_total = $daily_push_success_total + (float)$sumemry_value['daily_push_success']['total'];
        $daily_push_success_t_mo_end = $daily_push_success_t_mo_end + (float)$sumemry_value['daily_push_success']['t_mo_end'];
        $daily_push_success_avg = $daily_push_success_avg + (float)$sumemry_value['daily_push_success']['avg'];

        $daily_push_failed_total = $daily_push_failed_total + (float)$sumemry_value['daily_push_failed']['total'];
        $daily_push_failed_t_mo_end = $daily_push_failed_t_mo_end + (float)$sumemry_value['daily_push_failed']['t_mo_end'];
        $daily_push_failed_avg = $daily_push_failed_avg + (float)$sumemry_value['daily_push_failed']['avg'];

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
          if(!isset($tur_arr[$tur_key]['value']))
          {
            $tur_arr[$tur_key]['value'] = 0;
          }

          $tur_arr[$tur_key]['value'] = $tur_arr[$tur_key]['value'] + $tur_value['value'];

          $tur_arr[$tur_key]['class'] = "";
        }

        foreach ($sumemry_value['t_rev']['dates'] as $t_rev_key => $t_rev_value) {
          if(!isset($t_rev_arr[$t_rev_key]['value']))
          {
            $t_rev_arr[$t_rev_key]['value'] = 0;
          }

          $t_rev_arr[$t_rev_key]['value'] = $t_rev_arr[$t_rev_key]['value'] + $t_rev_value['value'];
          $t_rev_arr[$t_rev_key]['class'] = "";
        }

        foreach ($sumemry_value['net_rev']['dates'] as $net_rev_key => $net_rev_value) {
            if(!isset($net_rev_arr[$net_rev_key]['value']))
            {
              $net_rev_arr[$net_rev_key]['value'] = 0;
            }

            $net_rev_arr[$net_rev_key]['value'] = $net_rev_arr[$net_rev_key]['value'] + $net_rev_value['value'];
            $net_rev_arr[$net_rev_key]['class'] = "";
          }

        foreach ($sumemry_value['trat']['dates'] as $trat_key => $trat_value) {
          if($sumemry_key == 0)
          {
            $trat_sum[$trat_key] = 0;
          }

          $trat_sum[$trat_key] = $trat_sum[$trat_key] + (float)$trat_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $trat_arr[$trat_key] = ['value' => $trat_sum[$trat_key], 'class' => $trat_value['class']];
          }
        }

        foreach ($sumemry_value['turt']['dates'] as $turt_key => $turt_value)
        {
          if(!isset($turt_arr[$turt_key]['value']))
          {
            $turt_arr[$turt_key]['value'] = 0;
          }

          $turt_arr[$turt_key]['value'] = $turt_arr[$turt_key]['value'] + $turt_value['value'];

          $turt_arr[$turt_key]['class'] = "";
        }

        foreach ($sumemry_value['t_sub']['dates'] as $t_sub_key => $t_sub_value) {
          if($sumemry_key == 0)
          {
            $t_sub_sum[$t_sub_key] = 0;
          }

          $t_sub_sum[$t_sub_key] = $t_sub_sum[$t_sub_key] + (float)$t_sub_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $t_sub_arr[$t_sub_key] = ['value' => $t_sub_sum[$t_sub_key], 'class' => $t_sub_value['class']];
          }
        }

        foreach ($sumemry_value['reg']['dates'] as $reg_key => $reg_value) {
          if($sumemry_key == 0)
          {
            $reg_sum[$reg_key] = 0;
          }

          $reg_sum[$reg_key] = $reg_sum[$reg_key] + (float)$reg_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $reg_arr[$reg_key] = ['value' => $reg_sum[$reg_key], 'class' => $reg_value['class']];
          }
        }

        foreach ($sumemry_value['unreg']['dates'] as $unreg_key => $unreg_value) {
          if($sumemry_key == 0)
          {
            $unreg_sum[$unreg_key] = 0;
          }

          $unreg_sum[$unreg_key] = $unreg_sum[$unreg_key] + (float)$unreg_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $unreg_arr[$unreg_key] = ['value' => $unreg_sum[$unreg_key], 'class' => $unreg_value['class']];
          }
        }

        foreach ($sumemry_value['purged']['dates'] as $purged_key => $purged_value) {
          if($sumemry_key == 0)
          {
            $purged_sum[$purged_key] = 0;
          }

          $purged_sum[$purged_key] = $purged_sum[$purged_key] + (float)$purged_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $purged_arr[$purged_key] = ['value' => $purged_sum[$purged_key], 'class' => $purged_value['class']];
          }
        }

        foreach ($sumemry_value['churn']['dates'] as $churn_key => $churn_value) {
          if($sumemry_key == 0)
          {
            $churn_sum[$churn_key] = 0;
          }

          $churn_sum[$churn_key] = $churn_sum[$churn_key] + (float)$churn_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $churn_arr[$churn_key] = ['value' => $churn_sum[$churn_key], 'class' => $churn_value['class']];
          }
        }

        foreach ($sumemry_value['renewal']['dates'] as $renewal_key => $renewal_value) {
          if($sumemry_key == 0)
          {
            $renewal_sum[$renewal_key] = 0;
          }

          $renewal_sum[$renewal_key] = $renewal_sum[$renewal_key] + (float)$renewal_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $renewal_arr[$renewal_key] = ['value' => $renewal_sum[$renewal_key], 'class' => $renewal_value['class']];
          }
        }

        foreach ($sumemry_value['daily_push_success']['dates'] as $daily_push_success_key => $daily_push_success_value) {
          if($sumemry_key == 0)
          {
            $daily_push_success_sum[$daily_push_success_key] = 0;
          }

          $daily_push_success_sum[$daily_push_success_key] = $daily_push_success_sum[$daily_push_success_key] + (float)$daily_push_success_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $daily_push_success_arr[$daily_push_success_key] = ['value' => $daily_push_success_sum[$daily_push_success_key], 'class' => $daily_push_success_value['class']];
          }
        }

        foreach ($sumemry_value['daily_push_failed']['dates'] as $daily_push_failed_key => $daily_push_failed_value) {
          if($sumemry_key == 0)
          {
            $daily_push_failed_sum[$daily_push_failed_key] = 0;
          }

          $daily_push_failed_sum[$daily_push_failed_key] = $daily_push_failed_sum[$daily_push_failed_key] + (float)$daily_push_failed_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $daily_push_failed_arr[$daily_push_failed_key] = ['value' => $daily_push_failed_sum[$daily_push_failed_key], 'class' => $daily_push_failed_value['class']];
          }
        }

        foreach ($sumemry_value['bill']['dates'] as $bill_key => $bill_value) {
          if($sumemry_key == 0)
          {
            $bill_sum[$bill_key] = 0;
          }

          $bill_sum[$bill_key] = $bill_sum[$bill_key] + (float)$bill_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $bill_arr[$bill_key] = ['value' => $bill_sum[$bill_key], 'class' => $bill_value['class']];
          }
        }

        foreach ($sumemry_value['first_push']['dates'] as $first_push_key => $first_push_value) {
          if($sumemry_key == 0)
          {
            $first_push_sum[$first_push_key] = 0;
          }

          $first_push_sum[$first_push_key] = $first_push_sum[$first_push_key] + (float)$first_push_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $first_push_arr[$first_push_key] = ['value' => $first_push_sum[$first_push_key], 'class' => $first_push_value['class']];
          }
        }

        foreach ($sumemry_value['daily_push']['dates'] as $daily_push_key => $daily_push_value) {
          if($sumemry_key == 0)
          {
            $daily_push_sum[$daily_push_key] = 0;
          }

          $daily_push_sum[$daily_push_key] = $daily_push_sum[$daily_push_key] + (float)$daily_push_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $daily_push_arr[$daily_push_key] = ['value' => $daily_push_sum[$daily_push_key], 'class' => $daily_push_value['class']];
          }
        }

        foreach ($sumemry_value['arpu7']['dates'] as $arpu7_key => $arpu7_value) {
          if($sumemry_key == 0)
          {
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

        foreach ($sumemry_value['mt_success']['dates'] as $mt_success_key => $mt_success_value) {
          if($sumemry_key == 0){
            $mt_success_sum[$mt_success_key] = 0;
          }

          $mt_success_sum[$mt_success_key] = $mt_success_sum[$mt_success_key] + (float)$mt_success_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $mt_success_arr[$mt_success_key] = ['value' => $mt_success_sum[$mt_success_key], 'class' => $mt_success_value['class']];
          }
        }

        foreach ($sumemry_value['mt_failed']['dates'] as $mt_failed_key => $mt_failed_value) {
          if($sumemry_key == 0){
              $mt_failed_sum[$mt_failed_key] = 0;
          }

          $mt_failed_sum[$mt_failed_key] = $mt_failed_sum[$mt_failed_key] + (float)$mt_failed_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
              $mt_failed_arr[$mt_failed_key] = ['value' => $mt_failed_sum[$mt_failed_key], 'class' => $mt_failed_value['class']];
          }
        }

        foreach ($sumemry_value['fmt_success']['dates'] as $fmt_success_key => $fmt_success_value) {
          if($sumemry_key == 0){
              $fmt_success_sum[$fmt_success_key] = 0;
          }

          $fmt_success_sum[$fmt_success_key] = $fmt_success_sum[$fmt_success_key] + (float)$fmt_success_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
              $fmt_success_arr[$fmt_success_key] = ['value' => $fmt_success_sum[$fmt_success_key], 'class' => $fmt_success_value['class']];
          }
        }

        foreach ($sumemry_value['fmt_failed']['dates'] as $fmt_failed_key => $fmt_failed_value) {
          if($sumemry_key == 0){
            $fmt_failed_sum[$fmt_failed_key] = 0;
          }

          $fmt_failed_sum[$fmt_failed_key] = $fmt_failed_sum[$fmt_failed_key] + (float)$fmt_failed_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $fmt_failed_arr[$fmt_failed_key] = ['value' => $fmt_failed_sum[$fmt_failed_key], 'class' => $fmt_failed_value['class']];
          }
        }

        if(isset($sumemry_value['arpu7raw'])){
         foreach($sumemry_value['arpu7raw']['dates'] as $arpu7raw_key => $arpu7raw_value) {
            if(!isset($arpu7raw_arr[$arpu7raw_key]['value']))
            {
              $arpu7raw_arr[$arpu7raw_key]['value']['total_gross_revusd'] = 0;
              $arpu7raw_arr[$arpu7raw_key]['value']['total_total_reg'] = 0;
            }

            $arpu7raw_arr[$arpu7raw_key]['value']['total_gross_revusd'] = $arpu7raw_arr[$arpu7raw_key]['value']['total_gross_revusd'] + $arpu7raw_value['value']['total_gross_revusd'];

            $arpu7raw_arr[$arpu7raw_key]['value']['total_total_reg'] = $arpu7raw_arr[$arpu7raw_key]['value']['total_total_reg'] + $arpu7raw_value['value']['total_total_reg'];

            $arpu7raw_arr[$arpu7raw_key]['class'] = "";
          }
        }else{
          $arpu7raw_arr = [];
        }

        if(isset($sumemry_value['arpu30raw'])){
          foreach($sumemry_value['arpu30raw']['dates'] as $arpu30raw_key => $arpu30raw_value) {
            if(!isset($arpu30raw_arr[$arpu30raw_key]['value']))
            {
              $arpu30raw_arr[$arpu30raw_key]['value']['total_gross_revusd'] = 0;
              $arpu30raw_arr[$arpu30raw_key]['value']['total_total_reg'] = 0;
            }

            $arpu30raw_arr[$arpu30raw_key]['value']['total_gross_revusd'] = $arpu30raw_arr[$arpu30raw_key]['value']['total_gross_revusd'] + $arpu30raw_value['value']['total_gross_revusd'];

            $arpu30raw_arr[$arpu30raw_key]['value']['total_total_reg'] = $arpu30raw_arr[$arpu30raw_key]['value']['total_total_reg'] + $arpu30raw_value['value']['total_total_reg'];

            $arpu30raw_arr[$arpu30raw_key]['class'] = "";
          }
        }else{
          $arpu30raw_arr = [];
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

    $dataArr['net_rev']['dates'] = $net_rev_arr;
    $dataArr['net_rev']['total'] = $net_rev_total;
    $dataArr['net_rev']['t_mo_end'] = $net_rev_t_mo_end;
    $dataArr['net_rev']['avg'] = $net_rev_avg;

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

    $dataArr['daily_push_success']['dates'] = $daily_push_success_arr;
    $dataArr['daily_push_success']['total'] = $daily_push_success_total;
    $dataArr['daily_push_success']['t_mo_end'] = $daily_push_success_t_mo_end;
    $dataArr['daily_push_success']['avg'] = $daily_push_success_avg;

    $dataArr['daily_push_failed']['dates'] = $daily_push_failed_arr;
    $dataArr['daily_push_failed']['total'] = $daily_push_failed_total;
    $dataArr['daily_push_failed']['t_mo_end'] = $daily_push_failed_t_mo_end;
    $dataArr['daily_push_failed']['avg'] = $daily_push_failed_avg;

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

    $dataArr['arpu7raw']['dates'] = $arpu7raw_arr;
    $dataArr['arpu7raw']['total'] = 0;
    $dataArr['arpu7raw']['t_mo_end'] = 0;
    $dataArr['arpu7raw']['avg'] = 0;

    $dataArr['arpu30raw']['dates'] = $arpu30raw_arr;
    $dataArr['arpu30raw']['total'] = 0;
    $dataArr['arpu30raw']['t_mo_end'] = 0;
    $dataArr['arpu30raw']['avg'] = 0;

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

    $dataArr['month_string'] = $sumemry_value['month_string'];

    $dataArr['mt_success']['dates'] = $mt_success_arr;
    $dataArr['mt_failed']['dates'] = $mt_failed_arr;

    $dataArr['fmt_success']['dates'] = $fmt_success_arr;
    $dataArr['fmt_failed']['dates'] = $fmt_failed_arr;

    return $dataArr;
  }

  public static function CountrySumOperator($sumemry)
  {
    $tur_sum = $tur_arr = [];
    $t_rev_sum = $t_rev_arr = [];
    $trat_sum = $trat_arr = [];
    $turt_sum = $turt_arr = [];
    $net_rev_sum = $net_rev_arr = [];
    $t_sub_sum = $t_sub_arr = [];
    $reg_sum = $reg_arr = [];
    $unreg_sum = $unreg_arr = [];
    $purged_sum = $purged_arr = [];
    $churn_sum = $churn_arr = [];
    $renewal_sum = $renewal_arr = [];
    $daily_push_success_sum = $daily_push_success_arr = [];
    $daily_push_failed_sum = $daily_push_failed_arr = [];
    $bill_sum = $bill_arr = [];
    $first_push_sum = $first_push_arr = [];
    $daily_push_sum = $daily_push_arr = [];
    $arpu7_sum = $arpu7_arr = [];
    $usarpu7_sum = $usarpu7_arr = [];
    $arpu30_sum = $arpu30_arr = [];
    $usarpu30_sum = $usarpu30_arr = [];
    $mt_failed_sum = $mt_failed_arr = [];
    $mt_success_sum = $mt_success_arr = [];
    $fmt_failed_sum = $fmt_failed_arr = [];
    $fmt_success_sum = $fmt_success_arr = [];
    $first_day_active_sum = $first_day_active_arr = [];
    $cost_campaign_sum = $cost_campaign_arr = [];
    $ltv_sum = $ltv_arr = [];

    $turSumTest = 0;
    $last_update = "";

    $tur_total = $tur_t_mo_end = $tur_avg = 0;
    $t_rev_total = $t_rev_t_mo_end = $t_rev_avg = 0;
    $trat_total = $trat_t_mo_end = $trat_avg = 0;
    $turt_total = $turt_t_mo_end = $turt_avg = 0;
    $net_rev_total = $net_rev_t_mo_end = $net_rev_avg = 0;
    $t_sub_total = $t_sub_t_mo_end = $t_sub_avg = 0;
    $reg_total = $reg_t_mo_end = $reg_avg = 0;
    $unreg_total = $unreg_t_mo_end = $unreg_avg = 0;
    $purged_total = $purged_t_mo_end = $purged_avg = 0;
    $churn_total = $churn_t_mo_end = $churn_avg = 0;
    $renewal_total = $renewal_t_mo_end = $renewal_avg = 0;
    $daily_push_success_total = $daily_push_success_t_mo_end = $daily_push_success_avg = 0;
    $daily_push_failed_total = $daily_push_failed_t_mo_end = $daily_push_failed_avg = 0;
    $bill_total = $bill_t_mo_end = $bill_avg = 0;
    $first_push_total = $first_push_t_mo_end = $first_push_avg = 0;
    $daily_push_total = $daily_push_t_mo_end = $daily_push_avg = 0;
    $arpu7_total = $arpu7_t_mo_end = $arpu7_avg = 0;
    $usarpu7_total = $usarpu7_t_mo_end = $usarpu7_avg = 0;
    $arpu30_total = $arpu30_t_mo_end = $arpu30_avg = 0;
    $usarpu30_total = $usarpu30_t_mo_end = $usarpu30_avg = 0;
    $first_day_active_total = $first_day_active_t_mo_end = $first_day_active_avg = 0;
    $cost_campaign_total = $cost_campaign_t_mo_end = $cost_campaign_avg = 0;
    $ltv_total = $ltv_t_mo_end = $ltv_avg = 0;

    if(!empty($sumemry)){
      foreach ($sumemry as $sumemry_key => $sumemry_value)
      {
        $operatorId = $sumemry_value['operator']['id_operator'];
        $country_id = $sumemry_value['country']['id'];
        $usd = $sumemry_value['country']['usd'];

        if(isset($sumemry_value['last_update']))
        $last_update = $sumemry_value['last_update'];

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

        $net_rev_total = $net_rev_total + (float)$sumemry_value['net_rev']['total'];
        $net_rev_t_mo_end = $net_rev_t_mo_end + (float)$sumemry_value['net_rev']['t_mo_end'];
        $net_rev_avg = $net_rev_avg + (float)$sumemry_value['net_rev']['avg'];

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

        $daily_push_success_total = $daily_push_success_total + (float)$sumemry_value['daily_push_success']['total'];
        $daily_push_success_t_mo_end = $daily_push_success_t_mo_end + (float)$sumemry_value['daily_push_success']['t_mo_end'];
        $daily_push_success_avg = $daily_push_success_avg + (float)$sumemry_value['daily_push_success']['avg'];

        $daily_push_failed_total = $daily_push_failed_total + (float)$sumemry_value['daily_push_failed']['total'];
        $daily_push_failed_t_mo_end = $daily_push_failed_t_mo_end + (float)$sumemry_value['daily_push_failed']['t_mo_end'];
        $daily_push_failed_avg = $daily_push_failed_avg + (float)$sumemry_value['daily_push_failed']['avg'];

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

        $first_day_active_total = $first_day_active_total + (float)$sumemry_value['first_day_active']['total'];
        $first_day_active_t_mo_end = $first_day_active_t_mo_end + (float)$sumemry_value['first_day_active']['t_mo_end'];
        $first_day_active_avg = $first_day_active_avg + (float)$sumemry_value['first_day_active']['avg'];

        $cost_campaign_total = $cost_campaign_total + (float)$sumemry_value['cost_campaign']['total'];
        $cost_campaign_t_mo_end = $cost_campaign_t_mo_end + (float)$sumemry_value['cost_campaign']['t_mo_end'];
        $cost_campaign_avg = $cost_campaign_avg + (float)$sumemry_value['cost_campaign']['avg'];

        $ltv_total = $ltv_total + (float)$sumemry_value['ltv']['total'];
        $ltv_t_mo_end = $ltv_t_mo_end + (float)$sumemry_value['ltv']['t_mo_end'];
        $ltv_avg = $ltv_avg + (float)$sumemry_value['ltv']['avg'];

        $t_rev_date_wise_sum = 0;

        if (!empty($sumemry_value['arpu7raw']['dates']))
        {
		      foreach ($sumemry_value['arpu7raw']['dates'] as $arpu7raw_key => $arpu7raw_value)
		      {
            if(!isset($arpu7raw_arr[$arpu7raw_key]['value']))
            {
              $arpu7raw_arr[$arpu7raw_key]['value']['total_gross_rev'] = 0;
              $arpu7raw_arr[$arpu7raw_key]['value']['total_total_reg'] = 0;
              $arpu7raw_arr[$arpu7raw_key]['value']['total_gross_revusd'] = 0;
            }

		        $arpu7raw_arr[$arpu7raw_key]['value']['total_gross_rev'] = $arpu7raw_arr[$arpu7raw_key]['value']['total_gross_rev'] + $arpu7raw_value['value']['total_gross_rev'] ;
            $arpu7raw_arr[$arpu7raw_key]['value']['total_total_reg'] = $arpu7raw_arr[$arpu7raw_key]['value']['total_total_reg'] + $arpu7raw_value['value']['total_total_reg'] ;
            $arpu7raw_arr[$arpu7raw_key]['value']['total_gross_revusd'] = $arpu7raw_arr[$arpu7raw_key]['value']['total_gross_revusd'] + $arpu7raw_value['value']['total_gross_revusd'] ;

		        $arpu7raw_arr[$arpu7raw_key]['class'] = "";
		      }
        }

        if (!empty($sumemry_value['arpu30raw']['dates']))
        {
          foreach ($sumemry_value['arpu30raw']['dates'] as $arpu30raw_key => $arpu30raw_value)
          {
            if(!isset($arpu30raw_arr[$arpu30raw_key]['value']))
            {
              $arpu30raw_arr[$arpu30raw_key]['value']['total_gross_rev'] = 0;
              $arpu30raw_arr[$arpu30raw_key]['value']['total_total_reg'] = 0;
              $arpu30raw_arr[$arpu30raw_key]['value']['total_gross_revusd'] = 0;
            }

            $arpu30raw_arr[$arpu30raw_key]['value']['total_gross_rev'] = $arpu30raw_arr[$arpu30raw_key]['value']['total_gross_rev'] + $arpu30raw_value['value']['total_gross_rev'] ;
            $arpu30raw_arr[$arpu30raw_key]['value']['total_total_reg'] = $arpu30raw_arr[$arpu30raw_key]['value']['total_total_reg'] + $arpu30raw_value['value']['total_total_reg'] ;
            $arpu30raw_arr[$arpu30raw_key]['value']['total_gross_revusd'] = $arpu30raw_arr[$arpu30raw_key]['value']['total_gross_revusd'] + $arpu30raw_value['value']['total_gross_revusd'] ;

            $arpu30raw_arr[$arpu30raw_key]['class'] = "";
          }
        }

        if (!empty($sumemry_value['t_rev']['dates']))
        {
		      foreach ($sumemry_value['t_rev']['dates'] as $t_rev_key => $t_rev_value)
		      {
            if(!isset($t_rev_arr[$t_rev_key]['value']))
            {
              $t_rev_arr[$t_rev_key]['value'] = 0;
            }

		        $t_rev_arr[$t_rev_key]['value'] = $t_rev_arr[$t_rev_key]['value'] + $t_rev_value['value'] ;
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

        if (!empty($sumemry_value['net_rev']['dates']))
        {
          foreach ($sumemry_value['net_rev']['dates'] as $net_rev_key => $net_rev_value)
          {
            if(!isset($net_rev_arr[$net_rev_key]['value']))
            {
              $net_rev_arr[$net_rev_key]['value'] = 0;
            }

            $net_rev_arr[$net_rev_key]['value'] = $net_rev_arr[$net_rev_key]['value']+$net_rev_value['value'];
            $net_rev_arr[$net_rev_key]['class'] = "";
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

        if (!empty($sumemry_value['daily_push_success']['dates']))
        {
          foreach ($sumemry_value['daily_push_success']['dates'] as $daily_push_success_key => $daily_push_success_value)
          {
            if(!isset($daily_push_success_arr[$daily_push_success_key]['value']))
            {
              $daily_push_success_arr[$daily_push_success_key]['value'] = 0;
            }

            $daily_push_success_arr[$daily_push_success_key]['value'] = $daily_push_success_arr[$daily_push_success_key]['value']+$daily_push_success_value['value'];
            $daily_push_success_arr[$daily_push_success_key]['class'] = "";
          }
        }

        if (!empty($sumemry_value['daily_push_failed']['dates']))
        {
          foreach ($sumemry_value['daily_push_failed']['dates'] as $daily_push_failed_key => $daily_push_failed_value)
          {
            if(!isset($daily_push_failed_arr[$daily_push_failed_key]['value']))
            {
              $daily_push_failed_arr[$daily_push_failed_key]['value'] = 0;
            }

            $daily_push_failed_arr[$daily_push_failed_key]['value'] = $daily_push_failed_arr[$daily_push_failed_key]['value']+$daily_push_failed_value['value'];
            $daily_push_failed_arr[$daily_push_failed_key]['class'] = "";
          }
        }

        if (!empty($sumemry_value['mt_success']['dates']))
        {
          foreach ($sumemry_value['mt_success']['dates'] as $mt_success_key => $mt_success_value)
          {
            if(!isset($mt_success_arr[$mt_success_key]['value']))
            {
              $mt_success_arr[$mt_success_key]['value'] = 0;
            }

            $mt_success_arr[$mt_success_key]['value'] = $mt_success_arr[$mt_success_key]['value']+$mt_success_value['value'];
            $mt_success_arr[$mt_success_key]['class'] = "";
          }
        }

        if (!empty($sumemry_value['fmt_success']['dates']))
        {
          foreach ($sumemry_value['fmt_success']['dates'] as $fmt_success_key => $fmt_success_value)
          {
            if(!isset($fmt_success_arr[$fmt_success_key]['value']))
            {
              $fmt_success_arr[$fmt_success_key]['value'] = 0;
            }

            $fmt_success_arr[$fmt_success_key]['value'] = $fmt_success_arr[$fmt_success_key]['value']+$fmt_success_value['value'];
            $fmt_success_arr[$fmt_success_key]['class'] = "";
          }
        }

        if (!empty($sumemry_value['mt_failed']['dates']))
        {
          foreach ($sumemry_value['mt_failed']['dates'] as $mt_failed_key => $mt_failed_value)
          {
            if(!isset($mt_failed_arr[$mt_failed_key]['value']))
            {
              $mt_failed_arr[$mt_failed_key]['value'] = 0;
            }

            $mt_failed_arr[$mt_failed_key]['value'] = $mt_failed_arr[$mt_failed_key]['value']+$mt_failed_value['value'];
            $mt_failed_arr[$mt_failed_key]['class'] = "";
          }
        }

        if (!empty($sumemry_value['fmt_failed']['dates']))
        {
          foreach ($sumemry_value['fmt_failed']['dates'] as $fmt_failed_key => $fmt_failed_value)
          {
            if(!isset($fmt_failed_arr[$fmt_failed_key]['value']))
            {
              $fmt_failed_arr[$fmt_failed_key]['value'] = 0;
            }

            $fmt_failed_arr[$fmt_failed_key]['value'] = $fmt_failed_arr[$fmt_failed_key]['value']+$fmt_failed_value['value'];
            $fmt_failed_arr[$fmt_failed_key]['class'] = "";
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
            $usarpu7_arr[$usarpu7_key]['class'] ="";
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

        if(!empty($sumemry_value['first_day_active']['dates']))
        {
            foreach ($sumemry_value['first_day_active']['dates'] as $first_day_active_key => $first_day_active_value)
            {
                if(!isset($first_day_active_arr[$first_day_active_key]['value']))
                {
                    $first_day_active_arr[$first_day_active_key]['value']=0;
                }

                $first_day_active_arr[$first_day_active_key]['value'] = $first_day_active_arr[$first_day_active_key]['value']+$first_day_active_value['value'];
                $first_day_active_arr[$first_day_active_key]['class'] = "";
            }
        }

        if(!empty($sumemry_value['cost_campaign']['dates']))
        {
            foreach ($sumemry_value['cost_campaign']['dates'] as $cost_campaign_key => $cost_campaign_value)
            {
                if(!isset($cost_campaign_arr[$cost_campaign_key]['value']))
                {
                    $cost_campaign_arr[$cost_campaign_key]['value']=0;
                }

                $cost_campaign_arr[$cost_campaign_key]['value'] = $cost_campaign_arr[$cost_campaign_key]['value']+$cost_campaign_value['value'];
                $cost_campaign_arr[$cost_campaign_key]['class'] = "";
            }
        }

        if(!empty($sumemry_value['ltv']['dates']))
        {
            foreach ($sumemry_value['ltv']['dates'] as $ltv_key => $ltv_value)
            {
                if(!isset($ltv_arr[$ltv_key]['value']))
                {
                    $ltv_arr[$ltv_key]['value']=0;
                }

                $ltv_arr[$ltv_key]['value'] = $ltv_arr[$ltv_key]['value']+$ltv_value['value'];
                $ltv_arr[$ltv_key]['class'] = "";
            }
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

    $dataArr['net_rev']['dates'] = $net_rev_arr;
    $dataArr['net_rev']['total'] = $net_rev_total;
    $dataArr['net_rev']['t_mo_end'] = $net_rev_t_mo_end;
    $dataArr['net_rev']['avg'] = $net_rev_avg;

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

    $dataArr['daily_push_success']['dates'] = $daily_push_success_arr;
    $dataArr['daily_push_success']['total'] = $daily_push_success_total;
    $dataArr['daily_push_success']['t_mo_end'] = $daily_push_success_t_mo_end;
    $dataArr['daily_push_success']['avg'] = $daily_push_success_avg;

    $dataArr['daily_push_failed']['dates'] = $daily_push_failed_arr;
    $dataArr['daily_push_failed']['total'] = $daily_push_failed_total;
    $dataArr['daily_push_failed']['t_mo_end'] = $daily_push_failed_t_mo_end;
    $dataArr['daily_push_failed']['avg'] = $daily_push_failed_avg;

    $dataArr['mt_success']['dates'] = $mt_success_arr;
    $dataArr['mt_success']['total'] = 0;
    $dataArr['mt_success']['t_mo_end'] = 0;
    $dataArr['mt_success']['avg'] = 0;

    $dataArr['fmt_success']['dates'] = $fmt_success_arr;
    $dataArr['fmt_success']['total'] = 0;
    $dataArr['fmt_success']['t_mo_end'] = 0;
    $dataArr['fmt_success']['avg'] = 0;

    $dataArr['bill']['dates'] = $bill_arr;
    $dataArr['bill']['total'] = $bill_total;
    $dataArr['bill']['t_mo_end'] = $bill_t_mo_end;
    $dataArr['bill']['avg'] = $bill_avg;

    $dataArr['mt_failed']['dates'] = $mt_failed_arr;
    $dataArr['mt_failed']['total'] = 0;
    $dataArr['mt_failed']['t_mo_end'] = 0;
    $dataArr['mt_failed']['avg'] = 0;

    $dataArr['fmt_failed']['dates'] = $fmt_failed_arr;
    $dataArr['fmt_failed']['total'] = 0;
    $dataArr['fmt_failed']['t_mo_end'] = 0;
    $dataArr['fmt_failed']['avg'] = 0;

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

    $dataArr['arpu7raw']['dates'] = $arpu7raw_arr;
    $dataArr['arpu7raw']['total'] = 0;
    $dataArr['arpu7raw']['t_mo_end'] = 0;
    $dataArr['arpu7raw']['avg'] = 0;

    $dataArr['arpu30raw']['dates'] = $arpu30raw_arr;
    $dataArr['arpu30raw']['total'] = 0;
    $dataArr['arpu30raw']['t_mo_end'] = 0;
    $dataArr['arpu30raw']['avg'] = 0;

    $dataArr['usarpu30']['dates'] = $usarpu30_arr;
    $dataArr['usarpu30']['total'] = $usarpu30_total;
    $dataArr['usarpu30']['t_mo_end'] = $usarpu30_t_mo_end;
    $dataArr['usarpu30']['avg'] = $usarpu30_avg;

    $dataArr['first_day_active']['dates'] = $first_day_active_arr;
    $dataArr['first_day_active']['total'] = $first_day_active_total;
    $dataArr['first_day_active']['t_mo_end'] = $first_day_active_t_mo_end;
    $dataArr['first_day_active']['avg'] = $first_day_active_avg;

    $dataArr['cost_campaign']['dates'] = $cost_campaign_arr;
    $dataArr['cost_campaign']['total'] = $cost_campaign_total;
    $dataArr['cost_campaign']['t_mo_end'] = $cost_campaign_t_mo_end;
    $dataArr['cost_campaign']['avg'] = $cost_campaign_avg;

    $dataArr['ltv']['dates'] = $ltv_arr;
    $dataArr['ltv']['total'] = $ltv_total;
    $dataArr['ltv']['t_mo_end'] = $ltv_t_mo_end;
    $dataArr['ltv']['avg'] = $ltv_avg;

    $dataArr['last_update'] = $last_update;
    $dataArr['usd'] = $usd;

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
      $net_rev_arr = [];
      $cost_campaign_arr = [];
      $other_cost_arr = [];
      $hosting_cost_arr = [];
      $content_arr = [];
      $rnd_arr = [];
      $bd_arr = [];
      $market_cost_arr = [];
      $platform_arr = [];
      $other_tax_arr = [];
      $vat_arr = [];
      $wht_arr = [];
      $misc_tax_arr = [];
      $pnl_arr = [];
      $last_update = "";

      $end_user_rev_usd_total = $end_user_rev_usd_t_mo_end = $end_user_rev_usd_avg = 0;
      $end_user_rev_total = $end_user_rev_t_mo_end = $end_user_rev_avg = 0;
      $gros_rev_usd_total = $gros_rev_usd_t_mo_end = $gros_rev_usd_avg = 0;
      $gros_rev_total = $gros_rev_t_mo_end = $gros_rev_avg = 0;
      $net_rev_total = $net_rev_t_mo_end = $net_rev_avg = 0;
      $cost_campaign_total = $cost_campaign_t_mo_end = $cost_campaign_avg = 0;
      $other_cost_total = $other_cost_t_mo_end = $other_cost_avg = 0;
      $hosting_cost_total = $hosting_cost_t_mo_end = $hosting_cost_avg = 0;
      $content_total = $content_t_mo_end = $content_avg = 0;
      $rnd_total = $rnd_t_mo_end = $rnd_avg = 0;
      $bd_total = $bd_t_mo_end = $bd_avg = 0;
      $market_cost_total = $market_cost_t_mo_end = $market_cost_avg = 0;
      $platform_total = $platform_t_mo_end = $platform_avg = 0;
      $other_tax_total = $other_tax_t_mo_end = $other_tax_avg = 0;
      $vat_total = $vat_t_mo_end = $vat_avg = 0;
      $wht_total = $wht_t_mo_end = $wht_avg = 0;
      $misc_tax_total = $misc_tax_t_mo_end = $misc_tax_avg = 0;
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

        $net_rev_total = $net_rev_total + (float)$sumemry_value['net_rev']['total'];
        $net_rev_t_mo_end = $net_rev_t_mo_end + (float)$sumemry_value['net_rev']['t_mo_end'];
        $net_rev_avg = $net_rev_avg + (float)$sumemry_value['net_rev']['avg'];

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

        $market_cost_total = $market_cost_total + (float)$sumemry_value['market_cost']['total'];
        $market_cost_t_mo_end = $market_cost_t_mo_end + (float)$sumemry_value['market_cost']['t_mo_end'];
        $market_cost_avg = $market_cost_avg + (float)$sumemry_value['market_cost']['avg'];

        $platform_total = $platform_total + (float)$sumemry_value['platform']['total'];
        $platform_t_mo_end = $platform_t_mo_end + (float)$sumemry_value['platform']['t_mo_end'];
        $platform_avg = $platform_avg + (float)$sumemry_value['platform']['avg'];

        $other_tax_total = $other_tax_total + (float)$sumemry_value['other_tax']['total'];
        $other_tax_t_mo_end = $other_tax_t_mo_end + (float)$sumemry_value['other_tax']['t_mo_end'];
        $other_tax_avg = $other_tax_avg + (float)$sumemry_value['other_tax']['avg'];

        $vat_total = $vat_total + (float)$sumemry_value['vat']['total'];
        $vat_t_mo_end = $vat_t_mo_end + (float)$sumemry_value['vat']['t_mo_end'];
        $vat_avg = $vat_avg + (float)$sumemry_value['vat']['avg'];

        $wht_total = $wht_total + (float)$sumemry_value['wht']['total'];
        $wht_t_mo_end = $wht_t_mo_end + (float)$sumemry_value['wht']['t_mo_end'];
        $wht_avg = $wht_avg + (float)$sumemry_value['wht']['avg'];

        $misc_tax_total = $misc_tax_total + (float)$sumemry_value['misc_tax']['total'];
        $misc_tax_t_mo_end = $misc_tax_t_mo_end + (float)$sumemry_value['misc_tax']['t_mo_end'];
        $misc_tax_avg = $misc_tax_avg + (float)$sumemry_value['misc_tax']['avg'];

        $pnl_total = $pnl_total + (float)$sumemry_value['pnl']['total'];
        $pnl_t_mo_end = $pnl_t_mo_end + (float)$sumemry_value['pnl']['t_mo_end'];
        $pnl_avg = $pnl_avg + (float)$sumemry_value['pnl']['avg'];

        if(isset($sumemry_value['last_update']))
        $last_update = $sumemry_value['last_update'];

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

        foreach ($sumemry_value['net_rev']['dates'] as $net_rev_key => $net_rev_value) {
          if($sumemry_key == 0){
            $net_rev_sum[$net_rev_key] = 0;
          }

          $net_rev_sum[$net_rev_key] = $net_rev_sum[$net_rev_key] + (float)$net_rev_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $net_rev_arr[$net_rev_key] = ['value' => $net_rev_sum[$net_rev_key], 'class' => $net_rev_value['class']];
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

        foreach ($sumemry_value['market_cost']['dates'] as $market_cost_key => $market_cost_value) {
          if($sumemry_key == 0){
            $market_cost_sum[$market_cost_key] = 0;
          }

          $market_cost_sum[$market_cost_key] = $market_cost_sum[$market_cost_key] + (float)$market_cost_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $market_cost_arr[$market_cost_key] = ['value' => $market_cost_sum[$market_cost_key], 'class' => $market_cost_value['class']];
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

        foreach ($sumemry_value['other_tax']['dates'] as $other_tax_key => $other_tax_value) {
          if($sumemry_key == 0){
            $other_tax_sum[$other_tax_key] = 0;
          }

          $other_tax_sum[$other_tax_key] = $other_tax_sum[$other_tax_key] + (float)$other_tax_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $other_tax_arr[$other_tax_key] = ['value' => $other_tax_sum[$other_tax_key], 'class' => $other_tax_value['class']];
          }
        }

        foreach ($sumemry_value['vat']['dates'] as $vat_key => $vat_value) {
          if($sumemry_key == 0){
            $vat_sum[$vat_key] = 0;
          }

          $vat_sum[$vat_key] = $vat_sum[$vat_key] + (float)$vat_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $vat_arr[$vat_key] = ['value' => $vat_sum[$vat_key], 'class' => $vat_value['class']];
          }
        }

        foreach ($sumemry_value['wht']['dates'] as $wht_key => $wht_value) {
          if($sumemry_key == 0){
            $wht_sum[$wht_key] = 0;
          }

          $wht_sum[$wht_key] = $wht_sum[$wht_key] + (float)$wht_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $wht_arr[$wht_key] = ['value' => $wht_sum[$wht_key], 'class' => $wht_value['class']];
          }
        }

        foreach ($sumemry_value['misc_tax']['dates'] as $misc_tax_key => $misc_tax_value) {
          if($sumemry_key == 0){
            $misc_tax_sum[$misc_tax_key] = 0;
          }

          $misc_tax_sum[$misc_tax_key] = $misc_tax_sum[$misc_tax_key] + (float)$misc_tax_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $misc_tax_arr[$misc_tax_key] = ['value' => $misc_tax_sum[$misc_tax_key], 'class' => $misc_tax_value['class']];
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

      $dataArr['net_rev']['dates'] = $net_rev_arr;
      $dataArr['net_rev']['total'] = $net_rev_total;
      $dataArr['net_rev']['t_mo_end'] = $net_rev_t_mo_end;
      $dataArr['net_rev']['avg'] = $net_rev_avg;

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

      $dataArr['market_cost']['dates'] = $market_cost_arr;
      $dataArr['market_cost']['total'] = $market_cost_total;
      $dataArr['market_cost']['t_mo_end'] = $market_cost_t_mo_end;
      $dataArr['market_cost']['avg'] = $market_cost_avg;

      $dataArr['platform']['dates'] = $platform_arr;
      $dataArr['platform']['total'] = $platform_total;
      $dataArr['platform']['t_mo_end'] = $platform_t_mo_end;
      $dataArr['platform']['avg'] = $platform_avg;

      $dataArr['other_tax']['dates'] = $other_tax_arr;
      $dataArr['other_tax']['total'] = $other_tax_total;
      $dataArr['other_tax']['t_mo_end'] = $other_tax_t_mo_end;
      $dataArr['other_tax']['avg'] = $other_tax_avg;

      $dataArr['vat']['dates'] = $vat_arr;
      $dataArr['vat']['total'] = $vat_total;
      $dataArr['vat']['t_mo_end'] = $vat_t_mo_end;
      $dataArr['vat']['avg'] = $vat_avg;

      $dataArr['wht']['dates'] = $wht_arr;
      $dataArr['wht']['total'] = $wht_total;
      $dataArr['wht']['t_mo_end'] = $wht_t_mo_end;
      $dataArr['wht']['avg'] = $wht_avg;

      $dataArr['misc_tax']['dates'] = $misc_tax_arr;
      $dataArr['misc_tax']['total'] = $misc_tax_total;
      $dataArr['misc_tax']['t_mo_end'] = $misc_tax_t_mo_end;
      $dataArr['misc_tax']['avg'] = $misc_tax_avg;

      $dataArr['pnl']['dates'] = $pnl_arr;
      $dataArr['pnl']['total'] = $pnl_total;
      $dataArr['pnl']['t_mo_end'] = $pnl_t_mo_end;
      $dataArr['pnl']['avg'] = $pnl_avg;
      $dataArr['last_update']= $last_update;

      return $dataArr;
    }
  }

  public static function summaryUnknownDataSum($sumemry)
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
      $last_update = "";

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

        if(isset($sumemry_value['last_update']))
        $last_update = $sumemry_value['last_update'];

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
      $dataArr['last_update']= $last_update;

      return $dataArr;
    }
  }

  public static function DashboardAllDataSum($sumemry)
  {
    $DataArray = [];

    $curr_num_days = date('d', strtotime('-1 day'));
    $curr_tot_days = date('t');
    $num_days_remaining = $curr_tot_days - date('d');

    $current_avg_revenue_usd = $current_mo = $current_total_mo = $current_avg_mo = 0;
    $current_pnl = $current_avg_pnl = $current_revenue = 0;
    $current_revenue_usd = $currentMonthROI = $current_roi_mo = 0;
    $current_cost = $current_reg_sub = $current_usd_rev_share = 0;

    $estimated_revenue = $estimated_revenue_usd = $estimated_avg_revenue_usd = 0;
    $estimated_mo = $estimated_total_mo = $estimated_avg_mo = $estimated_cost = 0;
    $estimated_pnl = $estimated_avg_pnl = $estimatedMonthROI = 0;

    $last_avg_revenue_usd = $last_mo = $last_total_mo = $last_avg_mo = 0;
    $last_revenue = $last_revenue_usd = $last_pnl = 0;
    $last_avg_pnl = $lastMonthROI = 0;
    $last_cost = $last_reg_sub = $last_usd_rev_share = 0;

    $prev_mo = $prev_total_mo = $prev_avg_mo = $prev_pnl = 0;
    $prev_avg_pnl = $prev_revenue = $prev_revenue_usd = 0;
    $prev_avg_revenue_usd = $previousMonthROI = 0;
    $prev_cost = $previous_reg_sub = $previous_usd_rev_share = 0;

    $cost_campaign = $last_cost = $prev_cost = 0;
    $current_price_mo = $estimated_price_mo = $last_price_mo = $prev_price_mo = 0;
    $current_30_arpu = $estimated_30_arpu = $last_30_arpu = $prev_30_arpu = 0;
    $operator_count = $total = 0;

    $current_gross_revenue = $current_gross_revenue_usd = 0;
    $current_avg_gross_revenue_usd = 0;

    $current_net_revenue = $current_net_revenue_usd = 0;
    $current_avg_net_revenue_usd = 0;


    $estimated_gross_revenue = $estimated_gross_revenue_usd = $estimated_avg_gross_revenue_usd = 0;

    $estimated_net_revenue = $estimated_net_revenue_usd = $estimated_avg_net_revenue_usd = 0;


    $last_gross_revenue = $last_gross_revenue_usd = $last_avg_gross_revenue_usd = 0;
    $prev_gross_revenue = $prev_gross_revenue_usd = $prev_avg_gross_revenue_usd = 0;

    $last_net_revenue = $last_net_revenue_usd = $last_avg_net_revenue_usd = 0;
    $prev_net_revenue = $prev_net_revenue_usd = $prev_avg_net_revenue_usd = 0;

    foreach ($sumemry as $key => $sumemry_value) {
      $current_revenue += $sumemry_value['current_revenue'];
      $current_revenue_usd += $sumemry_value['current_revenue_usd'];
      $current_avg_revenue_usd += $sumemry_value['current_avg_revenue_usd'];
      $current_gross_revenue += $sumemry_value['current_gross_revenue'];
      $current_gross_revenue_usd += $sumemry_value['current_gross_revenue_usd'];
      $current_avg_gross_revenue_usd += $sumemry_value['current_avg_gross_revenue_usd'];
      $current_net_revenue_usd += $sumemry_value['current_net_revenue_usd'];
      $current_avg_net_revenue_usd += $sumemry_value['current_avg_net_revenue_usd'];
      $current_mo += $sumemry_value['current_mo'];
      $current_total_mo += $sumemry_value['current_total_mo'];
      $current_cost += $sumemry_value['current_cost'];
      $current_avg_mo += $sumemry_value['current_avg_mo'];
      $current_pnl += $sumemry_value['current_pnl'];
      $current_avg_pnl += $sumemry_value['current_avg_pnl'];
      $current_reg_sub += $sumemry_value['current_reg_sub'];
      $current_usd_rev_share += $sumemry_value['current_usd_rev_share'];

      $estimated_revenue += $sumemry_value['estimated_revenue'];
      $estimated_revenue_usd += $sumemry_value['estimated_revenue_usd'];
      $estimated_avg_revenue_usd += $sumemry_value['estimated_avg_revenue_usd'];
      $estimated_gross_revenue += $sumemry_value['estimated_gross_revenue'];
      $estimated_gross_revenue_usd += $sumemry_value['estimated_gross_revenue_usd'];
      $estimated_avg_gross_revenue_usd += $sumemry_value['estimated_avg_gross_revenue_usd'];
      $estimated_net_revenue_usd += $sumemry_value['estimated_net_revenue_usd'];
      $estimated_avg_net_revenue_usd += $sumemry_value['estimated_avg_net_revenue_usd'];
      $estimated_mo += $sumemry_value['estimated_mo'];
      $estimated_total_mo += $sumemry_value['estimated_total_mo'];
      $estimated_avg_mo += $sumemry_value['estimated_avg_mo'];
      $estimated_cost += $sumemry_value['estimated_cost'];
      $estimated_pnl += $sumemry_value['estimated_pnl'];
      $estimated_avg_pnl += $sumemry_value['estimated_avg_pnl'];

      $last_revenue += $sumemry_value['last_revenue'];
      $last_revenue_usd += $sumemry_value['last_revenue_usd'];
      $last_avg_revenue_usd += $sumemry_value['last_avg_revenue_usd'];
      $last_gross_revenue += $sumemry_value['last_gross_revenue'];
      $last_gross_revenue_usd += $sumemry_value['last_gross_revenue_usd'];
      $last_avg_gross_revenue_usd += $sumemry_value['last_avg_gross_revenue_usd'];
      $last_net_revenue_usd += $sumemry_value['last_net_revenue_usd'];
      $last_avg_net_revenue_usd += $sumemry_value['last_avg_net_revenue_usd'];
      $last_mo += $sumemry_value['last_mo'];
      $last_total_mo += $sumemry_value['last_total_mo'];
      $last_cost += $sumemry_value['last_cost'];
      $last_avg_mo += $sumemry_value['last_avg_mo'];
      $last_pnl += $sumemry_value['last_pnl'];
      $last_avg_pnl += $sumemry_value['last_avg_pnl'];
      $last_reg_sub += $sumemry_value['last_reg_sub'];
      $last_usd_rev_share += $sumemry_value['last_usd_rev_share'];

      $prev_mo += $sumemry_value['prev_mo'];
      $prev_total_mo += $sumemry_value['prev_total_mo'];
      $prev_cost += $sumemry_value['prev_cost'];
      $prev_avg_mo += $sumemry_value['prev_avg_mo'];
      $prev_pnl += $sumemry_value['prev_pnl'];
      $prev_avg_pnl += $sumemry_value['prev_avg_pnl'];
      $prev_revenue += $sumemry_value['prev_revenue'];
      $prev_revenue_usd += $sumemry_value['prev_revenue_usd'];
      $prev_avg_revenue_usd += $sumemry_value['prev_avg_revenue_usd'];
      $prev_gross_revenue += $sumemry_value['prev_gross_revenue'];
      $prev_gross_revenue_usd += $sumemry_value['prev_gross_revenue_usd'];
      $prev_avg_gross_revenue_usd += $sumemry_value['prev_avg_gross_revenue_usd'];
      $prev_net_revenue_usd += $sumemry_value['prev_net_revenue_usd'];
      $prev_avg_net_revenue_usd += $sumemry_value['prev_avg_net_revenue_usd'];
      $previous_reg_sub += $sumemry_value['previous_reg_sub'];
      $previous_usd_rev_share += $sumemry_value['previous_usd_rev_share'];

      $current_price_mo += $sumemry_value['current_price_mo'];
      $estimated_price_mo += $sumemry_value['estimated_price_mo'];
      $last_price_mo += $sumemry_value['last_price_mo'];
      $prev_price_mo += $sumemry_value['prev_price_mo'];

      $current_30_arpu += $sumemry_value['current_30_arpu'];
      $estimated_30_arpu += $sumemry_value['estimated_30_arpu'];
      $last_30_arpu += $sumemry_value['last_30_arpu'];
      $prev_30_arpu += $sumemry_value['prev_30_arpu'];

      $total += $sumemry_value['total'];
      $cost_campaign += $sumemry_value['cost_campaign'];
      $current_roi_mo += $sumemry_value['current_roi_mo'];

      $operator_count += isset($sumemry_value['operator_count'])?$sumemry_value['operator_count']:0;
    }

    $current_30_arpu = ($current_reg_sub == 0) ? 0 : ($current_usd_rev_share / ($current_reg_sub+$total));
    $current_price_mo = ($current_roi_mo == 0) ? 0 : ($cost_campaign / $current_roi_mo);
    $current_roi  =  ($current_30_arpu == 0) ? 0 : ($current_price_mo / $current_30_arpu);

    $currentMonthROI = $current_roi;

    $current_avg_roi = $current_roi/$curr_num_days;

    $estimated_price_mo = $current_price_mo;

    if($num_days_remaining == 0){
      $estimated_roi = $current_roi;
    }else{
      $estimated_roi = $current_roi + $current_roi/$num_days_remaining;
    }

    $estimatedMonthROI = $estimated_roi;
    $estimated_30_arpu = $current_30_arpu;

    $last_30_arpu = ($last_reg_sub == 0) ? 0 : ($last_usd_rev_share / $last_reg_sub);
    $last_price_mo = ($last_mo == 0) ? 0 : ($last_cost / $last_mo);
    $last_roi  =  ($last_30_arpu == 0) ? 0 : ($last_price_mo / $last_30_arpu);
    $lastMonthROI = $last_roi;

    $prev_30_arpu = ($previous_reg_sub == 0) ? 0 : ($previous_usd_rev_share / $previous_reg_sub);
    $previous_price_mo = ($prev_mo == 0) ? 0 : ($prev_cost / $prev_mo);
    $previous_roi  =  ($prev_30_arpu == 0) ? 0 : ($previous_price_mo / $prev_30_arpu);
    $previousMonthROI = $previous_roi;

    $DataArray['current_revenue'] = $current_revenue;
    $DataArray['current_revenue_usd'] = $current_revenue_usd;
    $DataArray['current_avg_revenue_usd'] = $current_avg_revenue_usd;
    $DataArray['current_gross_revenue'] = $current_gross_revenue;
    $DataArray['current_gross_revenue_usd'] = $current_gross_revenue_usd;
    $DataArray['current_avg_gross_revenue_usd'] = $current_avg_gross_revenue_usd;
    $DataArray['current_net_revenue_usd'] = $current_net_revenue_usd;
    $DataArray['current_avg_net_revenue_usd'] = $current_avg_net_revenue_usd;
    $DataArray['current_mo'] = $current_mo;
    $DataArray['current_total_mo'] = $current_total_mo;
    $DataArray['current_avg_mo'] = $current_avg_mo;
    $DataArray['current_cost'] = $current_cost;
    $DataArray['current_price_mo'] = $current_price_mo;
    $DataArray['current_pnl'] = $current_pnl;
    $DataArray['current_avg_pnl'] = $current_avg_pnl;
    $DataArray['currentMonthROI'] = $currentMonthROI;
    $DataArray['current_30_arpu'] = $current_30_arpu;

    $DataArray['estimated_revenue'] = $estimated_revenue;
    $DataArray['estimated_revenue_usd'] = $estimated_revenue_usd;
    $DataArray['estimated_avg_revenue_usd'] = $estimated_avg_revenue_usd;
    $DataArray['estimated_gross_revenue'] = $estimated_gross_revenue;
    $DataArray['estimated_gross_revenue_usd'] = $estimated_gross_revenue_usd;
    $DataArray['estimated_avg_gross_revenue_usd'] = $estimated_avg_gross_revenue_usd;
    $DataArray['estimated_net_revenue_usd'] = $estimated_net_revenue_usd;
    $DataArray['estimated_avg_net_revenue_usd'] = $estimated_avg_net_revenue_usd;
    $DataArray['estimated_mo'] = $estimated_mo;
    $DataArray['estimated_total_mo'] = $estimated_total_mo;
    $DataArray['estimated_avg_mo'] = $estimated_avg_mo;
    $DataArray['estimated_cost'] = $estimated_cost;
    $DataArray['estimated_price_mo'] = $estimated_price_mo;
    $DataArray['estimated_pnl'] = $estimated_pnl;
    $DataArray['estimated_avg_pnl'] = $estimated_avg_pnl;
    $DataArray['estimatedMonthROI'] = $estimatedMonthROI;
    $DataArray['estimated_30_arpu'] = $estimated_30_arpu;

    $DataArray['last_avg_revenue_usd'] = $last_avg_revenue_usd;
    $DataArray['last_mo'] = $last_mo;
    $DataArray['last_total_mo'] = $last_total_mo;
    $DataArray['last_avg_mo'] = $last_avg_mo;
    $DataArray['last_revenue'] = $last_revenue;
    $DataArray['last_revenue_usd'] = $last_revenue_usd;
    $DataArray['last_gross_revenue'] = $last_gross_revenue;
    $DataArray['last_gross_revenue_usd'] = $last_gross_revenue_usd;
    $DataArray['last_avg_gross_revenue_usd'] = $last_avg_gross_revenue_usd;
    $DataArray['last_net_revenue_usd'] = $last_net_revenue_usd;
    $DataArray['last_avg_net_revenue_usd'] = $last_avg_net_revenue_usd;
    $DataArray['last_cost'] = $last_cost;
    $DataArray['last_price_mo'] = $last_price_mo;
    $DataArray['last_pnl'] = $last_pnl;
    $DataArray['last_avg_pnl'] = $last_avg_pnl;
    $DataArray['lastMonthROI'] = $lastMonthROI;
    $DataArray['last_30_arpu'] = $last_30_arpu;

    $DataArray['prev_mo'] = $prev_mo;
    $DataArray['prev_total_mo'] = $prev_total_mo;
    $DataArray['prev_avg_mo'] = $prev_avg_mo;
    $DataArray['prev_pnl'] = $prev_pnl;
    $DataArray['prev_avg_pnl'] = $prev_avg_pnl;
    $DataArray['prev_revenue'] = $prev_revenue;
    $DataArray['prev_revenue_usd'] = $prev_revenue_usd;
    $DataArray['prev_gross_revenue'] = $prev_gross_revenue;
    $DataArray['prev_gross_revenue_usd'] = $prev_gross_revenue_usd;
    $DataArray['prev_avg_gross_revenue_usd'] = $prev_avg_gross_revenue_usd;
    $DataArray['prev_net_revenue_usd'] = $prev_net_revenue_usd;
    $DataArray['prev_avg_net_revenue_usd'] = $prev_avg_net_revenue_usd;
    $DataArray['prev_cost'] = $prev_cost;
    $DataArray['prev_price_mo'] = $previous_price_mo;
    $DataArray['prev_avg_revenue_usd'] = $prev_avg_revenue_usd;
    $DataArray['previousMonthROI'] = $previousMonthROI;
    $DataArray['operator_count'] = $operator_count;
    $DataArray['prev_30_arpu'] = $prev_30_arpu;

    return $DataArray;
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

    $dlr_total = $dlr_after_telco_total = 0;
    $fir_total = $fir_after_telco_total = 0;
    $discrepency_total = $discrepency_after_telco_total = 0;

    if(!empty($sumemryData))
    {
      foreach ($sumemryData as $sumemry_key => $sumemry)
      {
        $dlr_total = $dlr_total + (float)$sumemry['dlr']['total'];
        $dlr_after_telco_total = $dlr_after_telco_total + (float)$sumemry['dlr_after_telco']['total'];

        $fir_total = $fir_total + (float)$sumemry['fir']['total'];
        $fir_after_telco_total = $fir_after_telco_total + (float)$sumemry['fir_after_telco']['total'];

        $discrepency_total = $discrepency_total + (float)$sumemry['discrepency']['total'];
        $discrepency_after_telco_total = $discrepency_after_telco_total + (float)$sumemry['discrepency_after_telco']['total'];

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

          $discrepency_sum[$discrepency_key] = $discrepency_sum[$discrepency_key] + (float)$discrepency_value['value'];

          if(count($sumemryData)-1 == $sumemry_key)
          {
            $discrepency_arr[$discrepency_key] = ['value' => $discrepency_sum[$discrepency_key], 'class' => $discrepency_value['class']];
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

          $discrepency_after_telco_sum[$discrepency_after_telco_key] = $discrepency_after_telco_sum[$discrepency_after_telco_key] + (float)$discrepency_after_telco_value['value'];

          if(count($sumemryData)-1 == $sumemry_key)
          {
            $discrepency_after_telco_arr[$discrepency_after_telco_key] = ['value' => $discrepency_after_telco_sum[$discrepency_after_telco_key], 'class' => $discrepency_after_telco_value['class']];
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
      $discrepency_arr = [];
      $dlr_after_telco_arr = [];
      $fir_after_telco_arr = [];
      $discrepency_after_telco_arr = [];

      $dlr_total = $dlr_after_telco_total = 0;
      $fir_total = $fir_after_telco_total = 0;
      $discrepency_total = $discrepency_after_telco_total = 0;

      foreach ($sumemry as $sumemry_key => $sumemry_value)
      {
        $dlr_total = $dlr_total + (float)$sumemry_value['dlr']['total'];
        $dlr_after_telco_total = $dlr_after_telco_total + (float)$sumemry_value['dlr_after_telco']['total'];

        $fir_total = $fir_total + (float)$sumemry_value['fir']['total'];
        $fir_after_telco_total = $fir_after_telco_total + (float)$sumemry_value['fir_after_telco']['total'];

        $discrepency_total = $discrepency_total + (float)$sumemry_value['discrepency']['total'];
        $discrepency_after_telco_total = $discrepency_after_telco_total + (float)$sumemry_value['discrepency_after_telco']['total'];

        foreach ($sumemry_value['dlr']['dates'] as $dlr_key => $dlr_value) {
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

        foreach ($sumemry_value['discrepency']['dates'] as $discrepency_key => $discrepency_value) {
          if($sumemry_key == 0){
            $discrepency_sum[$discrepency_key] = 0;
          }

          $discrepency_sum[$discrepency_key] = $discrepency_sum[$discrepency_key] + (float)$discrepency_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $discrepency_arr[$discrepency_key] = ['value' => $discrepency_sum[$discrepency_key], 'class' => $discrepency_value['class']];
          }
        }

        foreach ($sumemry_value['dlr_after_telco']['dates'] as $dlr_after_telco_key => $dlr_after_telco_value) {
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

        foreach ($sumemry_value['discrepency_after_telco']['dates'] as $discrepency_after_telco_key => $discrepency_after_telco_value) {
          if($sumemry_key == 0){
            $discrepency_after_telco_sum[$discrepency_after_telco_key] = 0;
          }

          $discrepency_after_telco_sum[$discrepency_after_telco_key] = $discrepency_after_telco_sum[$discrepency_after_telco_key] + (float)$discrepency_after_telco_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $discrepency_after_telco_arr[$discrepency_after_telco_key] = ['value' => $discrepency_after_telco_sum[$discrepency_after_telco_key], 'class' => $discrepency_after_telco_value['class']];
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

    $gross_rev_total = $target_rev_total = 0;
    $rev_disc_total = $rev_after_share_total = 0;
    $target_after_share_total = $target_rev_disc_total = 0;
    $pnl_total = $target_pnl_total = $pnl_disc_total = 0;

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

      $gross_rev_total = $target_rev_total = 0;
      $rev_disc_total = $rev_after_share_total = 0;
      $target_after_share_total = $target_rev_disc_total = 0;
      $pnl_total = $target_pnl_total = $pnl_disc_total = 0;

      foreach ($sumemry as $sumemry_key => $sumemry_value)
      {
        $gross_rev_total = $gross_rev_total + (float)$sumemry_value['gross_rev']['total'];
        $target_rev_total = $target_rev_total + (float)$sumemry_value['target_rev']['total'];

        $rev_disc_total = $rev_disc_total + (float)$sumemry_value['rev_disc']['total'];
        $rev_after_share_total = $rev_after_share_total + (float)$sumemry_value['rev_after_share']['total'];

        $target_after_share_total = $target_after_share_total + (float)$sumemry_value['target_after_share']['total'];
        $target_rev_disc_total = $target_rev_disc_total + (float)$sumemry_value['target_rev_disc']['total'];

        $pnl_total = $pnl_total + (float)$sumemry_value['pnl']['total'];
        $target_pnl_total = $target_pnl_total + (float)$sumemry_value['target_pnl']['total'];
        $pnl_disc_total = $pnl_disc_total + (float)$sumemry_value['pnl_disc']['total'];

        foreach ($sumemry_value['gross_rev']['dates'] as $gross_rev_key => $gross_rev_value)
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

      return $dataArr;
    }else{
      return [];
    }
  }

  public static function PercentageCountryWise($sumemry_value,$country_id='')
  {
    $sum = 0;
    $avg = 0;
    $T_Mo_End = 0;
    $reaming_day = 0;
    $days = 0;
    $total_churn_sum = 0;
    $bill_days = 0;
    $total_bill_sum = 0;
    $total_subscriber = 0;
    $firstPush_sum = 0;
    $total_ltv_sum = 0;

    $fpush_array = array();
    $bill_array = array();
    $churn_array = array();
    $first_push = array();
    $bill_arr = array();
    $churn_arr = array();
    $arpu7_arr = array();
    $arpu7_array = array();
    $ltv_array =array();
    $ltv_arr =array();

    $today = Carbon::now()->format('Y-m-d');
    $firstdayOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');

    $usd = $sumemry_value['usd'];

    if(!empty($sumemry_value['churn']['dates']))
    {
      foreach ($sumemry_value['churn']['dates'] as $churn_key => $churn_value)
      {
        if(!isset($churn_arr[$churn_key]['value']))
        {
          $churn_arr[$churn_key]['value'] = 0;
        }

        $total_reg = $sumemry_value['reg']['dates'][$churn_key]['value'];
        $total_unreg = $sumemry_value['unreg']['dates'][$churn_key]['value'];
        $total_subscriber = $sumemry_value['t_sub']['dates'][$churn_key]['value'];
        $total_purged = $sumemry_value['purged']['dates'][$churn_key]['value'];

        $total_churn = 0;

        if($total_reg != 0)
        $total_churn = ( $total_unreg / $total_reg);

        if($today != $churn_key)
        {
          $total_churn_sum = $total_churn_sum +$total_churn;
          $days++;
        }

        $churn_arr[$churn_key]['value'] = $total_churn * 100;
        $churn_arr[$churn_key]['class'] = "";
      }
    }

    if(!empty($sumemry_value['bill']['dates']))
    {
      foreach ($sumemry_value['bill']['dates'] as $bill_key => $bill_value)
      {
        if(!isset($bill_arr[$bill_key]['value']))
        {
          $bill_arr[$bill_key]['value'] = 0;
          $first_push[$bill_key]['value'] = 0;
        }

        $total_mt_success = $sumemry_value['mt_success']['dates'][$bill_key]['value'];
        $total_mt_failed = $sumemry_value['mt_failed']['dates'][$bill_key]['value'];
        $total_subscriber = $sumemry_value['t_sub']['dates'][$bill_key]['value'];

        $total_bill_sum_tmp =  UtilityPercentage::billRateWithoutPer($total_mt_success,$total_mt_failed,$total_subscriber);

        $total_first_push_sum_tmp =  UtilityPercentage::FirstPushWithoutPer($total_mt_success,$total_mt_failed,$total_subscriber);

        if($today != $bill_key)
        {
          $total_bill_sum = $total_bill_sum +$total_bill_sum_tmp;
          $firstPush_sum = $firstPush_sum +$total_first_push_sum_tmp;
          $bill_days++;
        }

        $bill_arr[$bill_key]['value'] = $total_bill_sum_tmp * 100 ;
        $first_push[$bill_key]['value']= $total_first_push_sum_tmp * 100;

        $bill_arr[$bill_key]['class'] = "";
        $first_push[$bill_key]['class']= "";
      }
    }

    /* First Push Value */
    $fpush_array_avg = 0;
    if($bill_days > 0)
    $fpush_array_avg = ($firstPush_sum / $bill_days ) * 100;

    $fpush_array['dates'] = $first_push;
    $fpush_array['total'] = 0;
    $fpush_array['t_mo_end'] = 0;
    $fpush_array['avg'] = $fpush_array_avg;

    /* bill Value */
    $bill_avg = 0;
    if($bill_days > 0)
    $bill_avg = ($total_bill_sum / $bill_days ) * 100;

    $bill_array['dates'] = $bill_arr;
    $bill_array['total'] = 0;
    $bill_array['t_mo_end'] = 0;
    $bill_array['avg'] = $bill_avg;

    /* charn value */
    $churn_avg = 0;

    if($days > 0)
    $churn_avg = ($total_churn_sum / $days ) * 100;

    $churn_array['dates'] = $churn_arr;
    $churn_array['total'] = 0;
    $churn_array['t_mo_end'] = 0;
    $churn_array['avg'] = $churn_avg;

    /* arpu 7 calculation */
    $total_R1_sum = 0;
    $total_R3_sum = 0;
    $arpu7_days = 0;
    $arpu7_avg = 0;
    $arpu7usd_arr = array();
    $arpu7usd_array = array();
    $total_R1_sum_usd = 0;

    if(!empty($sumemry_value['arpu7raw']['dates']))
    {
      foreach ($sumemry_value['arpu7raw']['dates'] as $arpu7raw_key => $arpu7raw_value)
      {
        if(!isset($arpu7_arr[$arpu7raw_key]['value']))
        {
          $arpu7_arr[$arpu7raw_key]['value'] = 0;
          $arpu7usd_arr[$arpu7raw_key]['value'] = 0;
        }

        $R1 = $arpu7raw_value['value']['total_gross_rev'];
        $R3 = $arpu7raw_value['value']['total_total_reg'];
        $R1_usd = $arpu7raw_value['value']['total_gross_revusd'];

        $arpu7 = 0;

        if($R3 > 0)
        {
          $arpu7 = $R1 / $R3 ;
        }

        $arpu7usd = 0;

        if($R3 > 0)
        {
          $arpu7usd = $R1_usd / $R3 ;
        }

        if($today != $arpu7raw_key)
        {
          $total_R1_sum = $total_R1_sum + $R1 ;
          $total_R1_sum_usd = $total_R1_sum_usd + $R1_usd;
          $total_R3_sum = $total_R3_sum + $R3 ;
          $arpu7_days++;
        }

        $arpu7_arr[$arpu7raw_key]['value'] = $arpu7;
        $arpu7_arr[$arpu7raw_key]['class'] = "";

        $arpu7usd_arr[$arpu7raw_key]['value'] = $arpu7usd;
        $arpu7usd_arr[$arpu7raw_key]['class'] = "";
      }
    }

    $R1_avg = $total_R1_sum;
    $R3_avg = $total_R3_sum;

    $R1_avg_usd = $total_R1_sum_usd;

    $arpu7_avg = 0;

    if( $R3_avg > 0)
    {
      $arpu7_avg = ($R1_avg / $R3_avg) / $arpu7_days ;
    }

    $arpu7us_avg = 0;

    if( $R3_avg > 0)
    {
      $arpu7us_avg = ($R1_avg_usd / $R3_avg) / $arpu7_days ;
    }

    $arpu7_array['dates'] = $arpu7_arr;
    $arpu7_array['total'] = 0;
    $arpu7_array['t_mo_end'] = 0;
    $arpu7_array['avg'] = $arpu7_avg;

    $arpu7usd_array['dates'] = $arpu7usd_arr;
    $arpu7usd_array['total'] = 0;
    $arpu7usd_array['t_mo_end'] = 0;
    $arpu7usd_array['avg'] = $arpu7us_avg;

    $sumemry_value['churn'] = $churn_array;
    $sumemry_value['bill'] = $bill_array;
    $sumemry_value['first_push'] = $fpush_array;
    $sumemry_value['arpu7'] = $arpu7_array;
    $sumemry_value['usarpu7'] = $arpu7usd_array;

    /* arpu 7 calculation */
    $total_R1_sum = 0;
    $total_R3_sum = 0;
    $arpu7_days = 0;
    $arpu7_avg = 0;

    if(!empty($sumemry_value['arpu7raw']['dates']))
    {
      foreach ($sumemry_value['arpu7raw']['dates'] as $arpu7raw_key => $arpu7raw_value)
      {
        if(!isset($arpu7_arr[$arpu7raw_key]['value']))
        {
          $arpu7_arr[$arpu7raw_key]['value'] = 0;
        }

        $R1 = $arpu7raw_value['value']['total_gross_revusd'];
        $R3 = $arpu7raw_value['value']['total_total_reg'];

        $arpu7 = 0;

        if($R3 > 0)
        {
          $arpu7 = $R1 / $R3 ;
        }

        if($today != $arpu7raw_key)
        {
          $total_R1_sum =$total_R1_sum + $R1 ;
          $total_R3_sum =$total_R3_sum + $R3 ;
          $arpu7_days++;
        }

        $arpu7_arr[$arpu7raw_key]['value'] = $arpu7;
        $arpu7_arr[$arpu7raw_key]['class'] = "";
      }
    }

    $R1_avg = $total_R1_sum;
    $R3_avg = $total_R3_sum;

    if( $R3_avg > 0)
    {
      $arpu7_avg = $R1_avg / $R3_avg ;
    }

    $arpu7_array['dates'] = $arpu7_arr;
    $arpu7_array['total'] = 0;
    $arpu7_array['t_mo_end'] = 0;
    $arpu7_array['avg'] = $arpu7_avg;

    if($country_id == 142){
      $arpu7_avg = $arpu7_avg*1000;
    }

    $sumemry_value['usarpu7'] = $arpu7_array;
    $sumemry_value['arpu7']['avg'] = $arpu7_avg/$usd;

    /* arpu 30 calculation */

    $total_R1_sum_arpu30 = 0;
    $total_R3_sum_arpu30 = 0;
    $arpu30_days = 0;
    $arpu30_avg = 0;

    if(!empty($sumemry_value['arpu30raw']['dates']))
    {
      foreach ($sumemry_value['arpu30raw']['dates'] as $arpu30raw_key => $arpu30raw_value)
      {
        if(!isset($arpu30_arr[$arpu30raw_key]['value']))
        {
          $arpu30_arr[$arpu30raw_key]['value'] = 0;
        }

        $arpu30_R1 = $arpu30raw_value['value']['total_gross_revusd'];
        $arpu30_R3 = $arpu30raw_value['value']['total_total_reg'];

        $arpu30 = 0;

        if($arpu30_R3 > 0)
        {
          $arpu30 = $arpu30_R1 / $arpu30_R3 ;
        }

        if($today != $arpu30raw_key)
        {
          $total_R1_sum_arpu30 = $total_R1_sum_arpu30 + $arpu30_R1 ;
          $total_R3_sum_arpu30 = $total_R3_sum_arpu30 + $arpu30_R3 ;
          $arpu30_days++;
        }

        $arpu30_arr[$arpu30raw_key]['value'] = $arpu30;
        $arpu30_arr[$arpu30raw_key]['class'] = "";

        if($country_id == 142){
          $arpu30 = $arpu30*1000;
        }

        $arpu30_local[$arpu30raw_key]['value'] = $arpu30/$usd;
        $arpu30_local[$arpu30raw_key]['class'] = "";
      }
    }

    $arpu30_R1_avg = $total_R1_sum_arpu30;
    $arpu30_R3_avg = $total_R3_sum_arpu30;

    if( $arpu30_R3_avg > 0)
    {
      $arpu30_avg = $arpu30_R1_avg / $arpu30_R3_avg ;
    }

    $arpu30_array['dates'] = $arpu30_arr;
    $arpu30_array['total'] = 0;
    $arpu30_array['t_mo_end'] = 0;
    $arpu30_array['avg'] = $arpu30_avg;

    if($country_id == 142){
      $arpu30_avg = $arpu30_avg*1000;
    }

    $arpu30_local_array['dates'] = $arpu30_local;
    $arpu30_local_array['total'] = 0;
    $arpu30_local_array['t_mo_end'] = 0;
    $arpu30_local_array['avg'] = $arpu30_avg/$usd;

    $sumemry_value['usarpu30'] = $arpu30_array;

    $sumemry_value['arpu30'] = $arpu30_local_array;

    if(!empty($sumemry_value['ltv']['dates']))
    {
        foreach($sumemry_value['ltv']['dates'] as $ltv_key => $ltv_value)
        {
            if(!isset($ltv_arr[$ltv_key]['value']))
            {
                $ltv_arr[$ltv_key]['value'] = 0;
            }

            $total_turt = $sumemry_value['turt']['dates'][$ltv_key]['value'];
            $total_first_day_active = $sumemry_value['first_day_active']['dates'][$ltv_key]['value'];
            $total_unreg = $sumemry_value['unreg']['dates'][$ltv_key]['value'];
            $total_subscriber = $sumemry_value['t_sub']['dates'][$ltv_key]['value'];
            $total_cost_campaign = $sumemry_value['cost_campaign']['dates'][$ltv_key]['value'];


            $average_subs = ($total_subscriber + $total_first_day_active) / 2;

            if($average_subs > 0)
            {
              $churn = ($total_unreg  / $average_subs) * 100;
            }else{
              $churn = 0;
            }

            $gross_margin = $total_turt - $total_cost_campaign;

            if($average_subs > 0)
            {
              $customer_margin = ($gross_margin  / $average_subs) * 100;
            }else{
              $customer_margin = 0;
            }

            if($churn > 0)
            {
              $ltv = $customer_margin / $churn;
            }else{
              $ltv = 0;
            }


            if($today != $churn_key)
            {
                $total_ltv_sum = $total_ltv_sum + $ltv;
                $days++;
            }

            $ltv_arr[$ltv_key]['value'] = $ltv;
            $ltv_arr[$ltv_key]['class'] = "";
        }
    }

    $ltv_avg = ($days != 0) ? ($total_ltv_sum / $days ) : 0;
    $ltv_array['dates']= $ltv_arr;
    $ltv_array['total']= 0;
    $ltv_array['t_mo_end'] = 0;
    $ltv_array['avg']= $ltv_avg;

    $sumemry_value['ltv'] = $ltv_array;

    return $sumemry_value;
  }

  public static function pivotSummaryDataSum($sumemry)
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
      $bill_sum = $bill_arr = [];
      $first_push_sum = $first_push_arr = [];
      $daily_push_sum = $daily_push_arr = [];
      $arpu7_sum = $arpu7_arr = [];
      $usarpu7_sum = $usarpu7_arr = [];
      $arpu30_sum = $arpu30_arr = [];
      $usarpu30_sum = $usarpu30_arr = [];
      $mt_success_sum = $mt_success_arr = [];
      $mt_failed_sum = $mt_failed_arr = [];
      $fmt_success_sum = $fmt_success_arr = [];
      $fmt_failed_sum = $fmt_failed_arr = [];
      $arpu7raw_arr = [];
      $arpu30raw_arr = [];
      $t_sub_sum = $t_sub_arr = [];
      $mo_sum = $mo_arr = [];
      $roi_sum = $roi_arr = [];

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
      $t_sub_total = $t_sub_t_mo_end = $t_sub_avg = 0;
      $bill_total = $bill_t_mo_end = $bill_avg = 0;
      $first_push_total = $first_push_t_mo_end = $first_push_avg = 0;
      $daily_push_total = $daily_push_t_mo_end = $daily_push_avg = 0;
      $arpu7_total = $arpu7_t_mo_end = $arpu7_avg = 0;
      $usarpu7_total = $usarpu7_t_mo_end = $usarpu7_avg = 0;
      $arpu30_total = $arpu30_t_mo_end = $arpu30_avg = 0;
      $usarpu30_total = $usarpu30_t_mo_end = $usarpu30_avg = 0;
      $mo_total = $mo_t_mo_end = $mo_avg = 0;
      $roi_total = $roi_t_mo_end = $roi_avg = 0;

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

        $t_sub_total = $t_sub_total + (float)$sumemry_value['t_sub']['total'];
        $t_sub_t_mo_end = $t_sub_t_mo_end + (float)$sumemry_value['t_sub']['t_mo_end'];
        $t_sub_avg = $t_sub_avg + (float)$sumemry_value['t_sub']['avg'];

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

        $mo_total = $mo_total + (float)$sumemry_value['mo']['total'];
        $mo_t_mo_end = $mo_t_mo_end + (float)$sumemry_value['mo']['t_mo_end'];
        $mo_avg = $mo_avg + (float)$sumemry_value['mo']['avg'];

        $roi_total = $roi_total + (float)$sumemry_value['roi']['total'];
        $roi_t_mo_end = $roi_t_mo_end + (float)$sumemry_value['roi']['t_mo_end'];
        $roi_avg = $roi_avg + (float)$sumemry_value['roi']['avg'];

        $last_update = "";

        if(isset($sumemry_value['last_update']))
        $last_update = $sumemry_value['last_update'];

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

        foreach ($sumemry_value['mt_success']['dates'] as $mt_success_key => $mt_success_value) {
          if($sumemry_key == 0){
            $mt_success_sum[$mt_success_key] = 0;
          }

          $mt_success_sum[$mt_success_key] = $mt_success_sum[$mt_success_key] + (float)$mt_success_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $mt_success_arr[$mt_success_key] = ['value' => $mt_success_sum[$mt_success_key], 'class' => $mt_success_value['class']];
          }
        }

        foreach ($sumemry_value['mt_failed']['dates'] as $mt_failed_key => $mt_failed_value) {
          if($sumemry_key == 0){
            $mt_failed_sum[$mt_failed_key] = 0;
          }

          $mt_failed_sum[$mt_failed_key] = $mt_failed_sum[$mt_failed_key] + (float)$mt_failed_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $mt_failed_arr[$mt_failed_key] = ['value' => $mt_failed_sum[$mt_failed_key], 'class' => $mt_failed_value['class']];
          }
        }

        foreach ($sumemry_value['fmt_success']['dates'] as $fmt_success_key => $fmt_success_value) {
          if($sumemry_key == 0){
            $fmt_success_sum[$fmt_success_key] = 0;
          }

          $fmt_success_sum[$fmt_success_key] = $fmt_success_sum[$fmt_success_key] + (float)$fmt_success_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $fmt_success_arr[$fmt_success_key] = ['value' => $fmt_success_sum[$fmt_success_key], 'class' => $fmt_success_value['class']];
          }
        }

        foreach ($sumemry_value['fmt_failed']['dates'] as $fmt_failed_key => $fmt_failed_value) {
          if($sumemry_key == 0){
            $fmt_failed_sum[$fmt_failed_key] = 0;
          }

          $fmt_failed_sum[$fmt_failed_key] = $fmt_failed_sum[$fmt_failed_key] + (float)$fmt_failed_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $fmt_failed_arr[$fmt_failed_key] = ['value' => $fmt_failed_sum[$fmt_failed_key], 'class' => $fmt_failed_value['class']];
          }
        }

        foreach($sumemry_value['arpu7raw']['dates'] as $arpu7raw_key => $arpu7raw_value) {
          if(!isset($arpu7raw_arr[$arpu7raw_key]['value']))
          {
            $arpu7raw_arr[$arpu7raw_key]['value']['total_gross_revusd'] = 0;
            $arpu7raw_arr[$arpu7raw_key]['value']['total_total_reg'] = 0;
          }

          $arpu7raw_arr[$arpu7raw_key]['value']['total_gross_revusd'] =  $arpu7raw_arr[$arpu7raw_key]['value']['total_gross_revusd'] + $arpu7raw_value['value']['total_gross_revusd'];

          $arpu7raw_arr[$arpu7raw_key]['value']['total_total_reg'] =  $arpu7raw_arr[$arpu7raw_key]['value']['total_total_reg'] + $arpu7raw_value['value']['total_total_reg'];

          $arpu7raw_arr[$arpu7raw_key]['class'] = "";
        }

        foreach($sumemry_value['arpu30raw']['dates'] as $arpu30raw_key => $arpu30raw_value) {
          if(!isset($arpu30raw_arr[$arpu30raw_key]['value']))
          {
            $arpu30raw_arr[$arpu30raw_key]['value']['total_gross_revusd'] = 0;
            $arpu30raw_arr[$arpu30raw_key]['value']['total_total_reg'] = 0;
          }

          $arpu30raw_arr[$arpu30raw_key]['value']['total_gross_revusd'] = $arpu30raw_arr[$arpu30raw_key]['value']['total_gross_revusd'] + $arpu30raw_value['value']['total_gross_revusd'];

          $arpu30raw_arr[$arpu30raw_key]['value']['total_total_reg'] = $arpu30raw_arr[$arpu30raw_key]['value']['total_total_reg'] + $arpu30raw_value['value']['total_total_reg'];

          $arpu30raw_arr[$arpu30raw_key]['class'] = "";
        }

        foreach ($sumemry_value['mo']['dates'] as $mo_key => $mo_value) {
          if($sumemry_key == 0){
            $mo_sum[$mo_key] = 0;
          }

          $mo_sum[$mo_key] = $mo_sum[$mo_key] + (float)$mo_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $mo_arr[$mo_key] = ['value' => $mo_sum[$mo_key], 'class' => $mo_value['class']];
          }
        }

        foreach ($sumemry_value['roi']['dates'] as $roi_key => $roi_value) {
          if($sumemry_key == 0){
            $roi_sum[$roi_key] = 0;
          }

          $roi_sum[$roi_key] = $roi_sum[$roi_key] + (float)$roi_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $roi_arr[$roi_key] = ['value' => $roi_sum[$roi_key], 'class' => $roi_value['class']];
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

      $dataArr['t_sub']['dates'] = $t_sub_arr;
      $dataArr['t_sub']['total'] = $t_sub_total;
      $dataArr['t_sub']['t_mo_end'] = $t_sub_t_mo_end;
      $dataArr['t_sub']['avg'] = $t_sub_avg;

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

      $dataArr['arpu7raw']['dates'] = $arpu7raw_arr;
      $dataArr['arpu7raw']['total'] = 0;
      $dataArr['arpu7raw']['t_mo_end'] = 0;
      $dataArr['arpu7raw']['avg'] = 0;

      $dataArr['arpu30raw']['dates'] = $arpu30raw_arr;
      $dataArr['arpu30raw']['total'] = 0;
      $dataArr['arpu30raw']['t_mo_end'] = 0;
      $dataArr['arpu30raw']['avg'] = 0;

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
      $dataArr['month_string'] = $sumemry_value['month_string'];

      $dataArr['mt_success']['dates'] = $mt_success_arr;
      $dataArr['mt_failed']['dates'] = $mt_failed_arr;

      $dataArr['fmt_success']['dates'] = $fmt_success_arr;
      $dataArr['fmt_failed']['dates'] = $fmt_failed_arr;

      $dataArr['mo']['dates'] = $mo_arr;
      $dataArr['mo']['total'] = $mo_total;
      $dataArr['mo']['t_mo_end'] = $mo_t_mo_end;
      $dataArr['mo']['avg'] = $mo_avg;

      $dataArr['roi']['dates'] = $roi_arr;
      $dataArr['roi']['total'] = $roi_total;
      $dataArr['roi']['t_mo_end'] = $roi_t_mo_end;
      $dataArr['roi']['avg'] = $roi_avg;

      $dataArr['last_update'] = $last_update;

      return $dataArr;
    }
  }

  // dashboard page calculation
  public static function last30DayData($pnl_details)
  {
    return true;
    $share = 0;
    $reg = 0;

    $first_day = Carbon::now()->startOfMonth()->subDays(35)->format('Y-m-d');
    $last_day = Carbon::now()->format('Y-m-d');
    $datesIndividual = Utility::getRangeDates($first_day,$last_day);
    $no_of_days = Utility::getRangeDateNo($datesIndividual);

    $total_gros_rev = 0; // 7 days total revenue
    $total_total_reg = 0;  // 7 days total reg

    if(!empty($pnl_details))
    {
      foreach($pnl_details as $pnl)
      {
        dd($pnl);
      }
    }

    if(!empty($no_of_days))
    {
      foreach($no_of_days as $days)
      {

      }
    }

    return true;
  }

  public static function pnlDetailsDataSum($sumemry)
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
      $market_cost_arr = [];
      $misc_cost_arr = [];
      $platform_arr = [];
      $pnl_arr = [];

      $net_after_tax_arr = array();
      $net_revenue_after_tax_arr = array();
      $br_arr = array();
      $fp_arr = array();
      $fp_success_arr = array();
      $fp_failed_arr = array();
      $dp_arr = array();
      $dp_success_arr = array();
      $dp_failed_arr = array();
      $renewal_arr = array();
      $vat_arr = array();
      $spec_tax_arr = array();
      $government_cost_arr = array();
      $dealer_commision_arr = array();
      $wht_arr = array();
      $misc_tax_arr = array();
      $other_tax_arr = array();
      $uso_arr = array();
      $agre_paxxa_arr = array();
      $sbaf_arr = array();
      $clicks_arr = array();
      $ratio_for_cpa_arr = array();
      $cpa_price_arr = array();
      $cr_mo_clicks_arr = array();
      $cr_mo_landing_arr = array();
      $landing_arr = array();
      $mo_arr = array();
      $reg_arr = array();
      $unreg_arr = array();
      $price_mo_arr = array();
      $price_mo_cost_arr = array();
      $price_mo_mo_arr = array();
      $active_subs_arr = array();
      $arpu_7_arr = array();
      $arpu_7_usd_arr = array();
      $arpu_30_arr = array();
      $arpu_30_usd_arr = array();
      $reg_sub_arr = array();
      $last_30_gros_rev_arr = array();
      $last_30_reg_arr = array();
      $roi_arr = array();
      $bill_arr = array();
      $firstpush_arr = array();
      $dailypush_arr = array();

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
      $market_cost_total = $market_cost_t_mo_end = $market_cost_avg = 0;
      $misc_cost_total = $misc_cost_t_mo_end = $misc_cost_avg = 0;
      $platform_total = $platform_t_mo_end = $platform_avg = 0;
      $pnl_total = $pnl_t_mo_end = $pnl_avg = 0;
      $net_after_tax_total = $net_after_tax_t_mo_end = $net_after_tax_avg = 0;
      $net_revenue_after_tax_total = $net_revenue_after_tax_t_mo_end = $net_revenue_after_tax_avg = 0;
      $br_total = $br_t_mo_end = $br_avg = 0;
      $fp_total = $fp_t_mo_end = $fp_avg = 0;
      $fp_success_total = $fp_success_t_mo_end = $fp_success_avg = 0;
      $fp_failed_total = $fp_failed_t_mo_end = $fp_failed_avg = 0;
      $dp_total = $dp_t_mo_end = $dp_avg = 0;
      $dp_success_total = $dp_success_t_mo_end = $dp_success_avg = 0;
      $dp_failed_total = $dp_failed_t_mo_end = $dp_failed_avg = 0;
      $renewal_total = $renewal_t_mo_end = $renewal_avg = 0;
      $vat_total = $vat_t_mo_end = $vat_avg = 0;
      $spec_tax_total = $spec_tax_t_mo_end = $spec_tax_avg = 0;
      $government_cost_total = $government_cost_t_mo_end = $government_cost_avg = 0;
      $dealer_commision_total = $dealer_commision_t_mo_end = $dealer_commision_avg = 0;
      $wht_total = $wht_t_mo_end = $wht_avg = 0;
      $misc_tax_total = $misc_tax_t_mo_end = $misc_tax_avg = 0;
      $other_tax_total = $other_tax_t_mo_end = $other_tax_avg = 0;
      $uso_total = $uso_t_mo_end = $uso_avg = 0;
      $agre_paxxa_total = $agre_paxxa_t_mo_end = $agre_paxxa_avg = 0;
      $sbaf_total = $sbaf_t_mo_end = $sbaf_avg = 0;
      $clicks_total = $clicks_t_mo_end = $clicks_avg = 0;
      $ratio_for_cpa_total = $ratio_for_cpa_t_mo_end = $ratio_for_cpa_avg = 0;
      $cpa_price_total = $cpa_price_t_mo_end = $cpa_price_avg = 0;
      $cr_mo_clicks_total = $cr_mo_clicks_t_mo_end = $cr_mo_clicks_avg = 0;
      $cr_mo_landing_total = $cr_mo_landing_t_mo_end = $cr_mo_landing_avg = 0;
      $landing_total = $landing_t_mo_end = $landing_avg = 0;
      $mo_total = $mo_t_mo_end = $mo_avg = 0;
      $reg_total = $reg_t_mo_end = $reg_avg = 0;
      $unreg_total = $unreg_t_mo_end = $unreg_avg = 0;
      $price_mo_total = $price_mo_t_mo_end = $price_mo_avg = 0;
      $active_subs_total = $active_subs_t_mo_end = $active_subs_avg = 0;
      $arpu_7_total = $arpu_7_t_mo_end = $arpu_7_avg = 0;
      $arpu_7_usd_total = $arpu_7_usd_t_mo_end = $arpu_7_usd_avg = 0;
      $arpu_30_total = $arpu_30_t_mo_end = $arpu_30_avg = 0;
      $arpu_30_usd_total = $arpu_30_usd_t_mo_end = $arpu_30_usd_avg = 0;
      $reg_sub_total = $reg_sub_t_mo_end = $reg_sub_avg = 0;
      $roi_total = $roi_t_mo_end = $roi_avg = 0;
      $bill_total = $bill_t_mo_end = $bill_avg = 0;
      $firstpush_total = $firstpush_t_mo_end = $firstpush_avg = 0;
      $dailypush_total = $dailypush_t_mo_end = $dailypush_avg = 0;
      $last_7_gros_rev_total = $last_7_gros_rev_t_mo_end = $last_7_gros_rev_avg = 0;
      $last_7_reg_total = $last_7_reg_t_mo_end = $last_7_reg_avg = 0;
      $last_30_gros_rev_total = $last_30_gros_rev_t_mo_end = $last_30_gros_rev_avg = 0;
      $last_30_reg_total = $last_30_reg_t_mo_end = $last_30_reg_avg = 0;


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

        $market_cost_total = $market_cost_total + (float)$sumemry_value['market_cost']['total'];
        $market_cost_t_mo_end = $market_cost_t_mo_end + (float)$sumemry_value['market_cost']['t_mo_end'];
        $market_cost_avg = $market_cost_avg + (float)$sumemry_value['market_cost']['avg'];

        $misc_cost_total = $misc_cost_total + (float)$sumemry_value['misc_cost']['total'];
        $misc_cost_t_mo_end = $misc_cost_t_mo_end + (float)$sumemry_value['misc_cost']['t_mo_end'];
        $misc_cost_avg = $misc_cost_avg + (float)$sumemry_value['misc_cost']['avg'];

        $platform_total = $platform_total + (float)$sumemry_value['platform']['total'];
        $platform_t_mo_end = $platform_t_mo_end + (float)$sumemry_value['platform']['t_mo_end'];
        $platform_avg = $platform_avg + (float)$sumemry_value['platform']['avg'];

        $pnl_total = $pnl_total + (float)$sumemry_value['pnl']['total'];
        $pnl_t_mo_end = $pnl_t_mo_end + (float)$sumemry_value['pnl']['t_mo_end'];
        $pnl_avg = $pnl_avg + (float)$sumemry_value['pnl']['avg'];

        $net_after_tax_total = $net_after_tax_total + (float)$sumemry_value['net_after_tax']['total'];
        $net_after_tax_t_mo_end = $net_after_tax_t_mo_end + (float)$sumemry_value['net_after_tax']['t_mo_end'];
        $net_after_tax_avg = $net_after_tax_avg + (float)$sumemry_value['net_after_tax']['avg'];

        $net_revenue_after_tax_total = $net_revenue_after_tax_total + (float)$sumemry_value['net_revenue_after_tax']['total'];
        $net_revenue_after_tax_t_mo_end = $net_revenue_after_tax_t_mo_end + (float)$sumemry_value['net_revenue_after_tax']['t_mo_end'];
        $net_revenue_after_tax_avg = $net_revenue_after_tax_avg + (float)$sumemry_value['net_revenue_after_tax']['avg'];

        $br_total = $br_total + (float)$sumemry_value['br']['total'];
        $br_t_mo_end = $br_t_mo_end + (float)$sumemry_value['br']['t_mo_end'];
        $br_avg = $br_avg + (float)$sumemry_value['br']['avg'];

        $fp_total = $fp_total + (float)$sumemry_value['fp']['total'];
        $fp_t_mo_end = $fp_t_mo_end + (float)$sumemry_value['fp']['t_mo_end'];
        $fp_avg = $fp_avg + (float)$sumemry_value['fp']['avg'];

        $fp_success_total = $fp_success_total + (float)$sumemry_value['fp_success']['total'];
        $fp_success_t_mo_end = $fp_success_t_mo_end + (float)$sumemry_value['fp_success']['t_mo_end'];
        $fp_success_avg = $fp_success_avg + (float)$sumemry_value['fp_success']['avg'];

        $fp_failed_total = $fp_failed_total + (float)$sumemry_value['fp_failed']['total'];
        $fp_failed_t_mo_end = $fp_failed_t_mo_end + (float)$sumemry_value['fp_failed']['t_mo_end'];
        $fp_failed_avg = $fp_failed_avg + (float)$sumemry_value['fp_failed']['avg'];

        $dp_total = $dp_total + (float)$sumemry_value['dp']['total'];
        $dp_t_mo_end = $dp_t_mo_end + (float)$sumemry_value['dp']['t_mo_end'];
        $dp_avg = $dp_avg + (float)$sumemry_value['dp']['avg'];

        $dp_success_total = $dp_success_total + (float)$sumemry_value['dp_success']['total'];
        $dp_success_t_mo_end = $dp_success_t_mo_end + (float)$sumemry_value['dp_success']['t_mo_end'];
        $dp_success_avg = $dp_success_avg + (float)$sumemry_value['dp_success']['avg'];

        $dp_failed_total = $dp_failed_total + (float)$sumemry_value['dp_failed']['total'];
        $dp_failed_t_mo_end = $dp_failed_t_mo_end + (float)$sumemry_value['dp_failed']['t_mo_end'];
        $dp_failed_avg = $dp_failed_avg + (float)$sumemry_value['dp_failed']['avg'];

        $renewal_total = $renewal_total + (float)$sumemry_value['renewal']['total'];
        $renewal_t_mo_end = $renewal_t_mo_end + (float)$sumemry_value['renewal']['t_mo_end'];
        $renewal_avg = $renewal_avg + (float)$sumemry_value['renewal']['avg'];

        $vat_total = $vat_total + (float)$sumemry_value['vat']['total'];
        $vat_t_mo_end = $vat_t_mo_end + (float)$sumemry_value['vat']['t_mo_end'];
        $vat_avg = $vat_avg + (float)$sumemry_value['vat']['avg'];

        $spec_tax_total = $spec_tax_total + (float)$sumemry_value['spec_tax']['total'];
        $spec_tax_t_mo_end = $spec_tax_t_mo_end + (float)$sumemry_value['spec_tax']['t_mo_end'];
        $spec_tax_avg = $spec_tax_avg + (float)$sumemry_value['spec_tax']['avg'];

        $government_cost_total = $government_cost_total + (float)$sumemry_value['government_cost']['total'];
        $government_cost_t_mo_end = $government_cost_t_mo_end + (float)$sumemry_value['government_cost']['t_mo_end'];
        $government_cost_avg = $government_cost_avg + (float)$sumemry_value['government_cost']['avg'];

        $dealer_commision_total = $dealer_commision_total + (float)$sumemry_value['dealer_commision']['total'];
        $dealer_commision_t_mo_end = $dealer_commision_t_mo_end + (float)$sumemry_value['dealer_commision']['t_mo_end'];
        $dealer_commision_avg = $dealer_commision_avg + (float)$sumemry_value['dealer_commision']['avg'];

        $wht_total = $wht_total + (float)$sumemry_value['wht']['total'];
        $wht_t_mo_end = $wht_t_mo_end + (float)$sumemry_value['wht']['t_mo_end'];
        $wht_avg = $wht_avg + (float)$sumemry_value['wht']['avg'];

        $misc_tax_total = $misc_tax_total + (float)$sumemry_value['misc_tax']['total'];
        $misc_tax_t_mo_end = $misc_tax_t_mo_end + (float)$sumemry_value['misc_tax']['t_mo_end'];
        $misc_tax_avg = $misc_tax_avg + (float)$sumemry_value['misc_tax']['avg'];

        $other_tax_total = $other_tax_total + (float)$sumemry_value['other_tax']['total'];
        $other_tax_t_mo_end = $other_tax_t_mo_end + (float)$sumemry_value['other_tax']['t_mo_end'];
        $other_tax_avg = $other_tax_avg + (float)$sumemry_value['other_tax']['avg'];

        $uso_total = $uso_total + (float)$sumemry_value['uso']['total'];
        $uso_t_mo_end = $uso_t_mo_end + (float)$sumemry_value['uso']['t_mo_end'];
        $uso_avg = $uso_avg + (float)$sumemry_value['uso']['avg'];

        $agre_paxxa_total = $agre_paxxa_total + (float)$sumemry_value['agre_paxxa']['total'];
        $agre_paxxa_t_mo_end = $agre_paxxa_t_mo_end + (float)$sumemry_value['agre_paxxa']['t_mo_end'];
        $agre_paxxa_avg = $agre_paxxa_avg + (float)$sumemry_value['agre_paxxa']['avg'];

        $sbaf_total = $sbaf_total + (float)$sumemry_value['sbaf']['total'];
        $sbaf_t_mo_end = $sbaf_t_mo_end + (float)$sumemry_value['sbaf']['t_mo_end'];
        $sbaf_avg = $sbaf_avg + (float)$sumemry_value['sbaf']['avg'];

        $clicks_total = $clicks_total + (float)$sumemry_value['clicks']['total'];
        $clicks_t_mo_end = $clicks_t_mo_end + (float)$sumemry_value['clicks']['t_mo_end'];
        $clicks_avg = $clicks_avg + (float)$sumemry_value['clicks']['avg'];

        $ratio_for_cpa_total = $ratio_for_cpa_total + (float)$sumemry_value['ratio_for_cpa']['total'];
        $ratio_for_cpa_t_mo_end = $ratio_for_cpa_t_mo_end + (float)$sumemry_value['ratio_for_cpa']['t_mo_end'];
        $ratio_for_cpa_avg = $ratio_for_cpa_avg + (float)$sumemry_value['ratio_for_cpa']['avg'];

        $cpa_price_total = $cpa_price_total + (float)$sumemry_value['cpa_price']['total'];
        $cpa_price_t_mo_end = $cpa_price_t_mo_end + (float)$sumemry_value['cpa_price']['t_mo_end'];
        $cpa_price_avg = $cpa_price_avg + (float)$sumemry_value['cpa_price']['avg'];

        $cr_mo_clicks_total = $cr_mo_clicks_total + (float)$sumemry_value['cr_mo_clicks']['total'];
        $cr_mo_clicks_t_mo_end = $cr_mo_clicks_t_mo_end + (float)$sumemry_value['cr_mo_clicks']['t_mo_end'];
        $cr_mo_clicks_avg = $cr_mo_clicks_avg + (float)$sumemry_value['cr_mo_clicks']['avg'];

        $cr_mo_landing_total = $cr_mo_landing_total + (float)$sumemry_value['cr_mo_landing']['total'];
        $cr_mo_landing_t_mo_end = $cr_mo_landing_t_mo_end + (float)$sumemry_value['cr_mo_landing']['t_mo_end'];
        $cr_mo_landing_avg = $cr_mo_landing_avg + (float)$sumemry_value['cr_mo_landing']['avg'];

        $landing_total = $landing_total + (float)$sumemry_value['landing']['total'];
        $landing_t_mo_end = $landing_t_mo_end + (float)$sumemry_value['landing']['t_mo_end'];
        $landing_avg = $landing_avg + (float)$sumemry_value['landing']['avg'];

        $mo_total = $mo_total + (float)$sumemry_value['mo']['total'];
        $mo_t_mo_end = $mo_t_mo_end + (float)$sumemry_value['mo']['t_mo_end'];
        $mo_avg = $mo_avg + (float)$sumemry_value['mo']['avg'];

        $reg_total = $reg_total + (float)$sumemry_value['reg']['total'];
        $reg_t_mo_end = $reg_t_mo_end + (float)$sumemry_value['reg']['t_mo_end'];
        $reg_avg = $reg_avg + (float)$sumemry_value['reg']['avg'];

        $unreg_total = $unreg_total + (float)$sumemry_value['unreg']['total'];
        $unreg_t_mo_end = $unreg_t_mo_end + (float)$sumemry_value['unreg']['t_mo_end'];
        $unreg_avg = $unreg_avg + (float)$sumemry_value['unreg']['avg'];

        $price_mo_total = ($mo_total != 0) ? $cost_campaign_total / $mo_total : 0;
        $price_mo_avg = ($mo_avg != 0) ? $cost_campaign_avg / $mo_avg : 0;
        $price_mo_t_mo_end = ($mo_t_mo_end != 0) ? $cost_campaign_t_mo_end / $mo_t_mo_end : 0;

        $active_subs_total = $active_subs_total + (float)$sumemry_value['active_subs']['total'];
        $active_subs_t_mo_end = $active_subs_t_mo_end + (float)$sumemry_value['active_subs']['t_mo_end'];
        $active_subs_avg = $active_subs_avg + (float)$sumemry_value['active_subs']['avg'];

        $arpu_7_total = $arpu_7_total + (float)$sumemry_value['arpu_7']['total'];
        $arpu_7_t_mo_end = $arpu_7_t_mo_end + (float)$sumemry_value['arpu_7']['t_mo_end'];
        $arpu_7_avg = $arpu_7_avg + (float)$sumemry_value['arpu_7']['avg'];

        $arpu_7_usd_total = $arpu_7_usd_total + (float)$sumemry_value['arpu_7_usd']['total'];
        $arpu_7_usd_t_mo_end = $arpu_7_usd_t_mo_end + (float)$sumemry_value['arpu_7_usd']['t_mo_end'];
        $arpu_7_usd_avg = $arpu_7_usd_avg + (float)$sumemry_value['arpu_7_usd']['avg'];

        $arpu_30_total = $arpu_30_total + (float)$sumemry_value['arpu_30']['total'];
        $arpu_30_t_mo_end = $arpu_30_t_mo_end + (float)$sumemry_value['arpu_30']['t_mo_end'];
        $arpu_30_avg = $arpu_30_avg + (float)$sumemry_value['arpu_30']['avg'];

        $arpu_30_usd_total = $arpu_30_usd_total + (float)$sumemry_value['arpu_30_usd']['total'];
        $arpu_30_usd_t_mo_end = $arpu_30_usd_t_mo_end + (float)$sumemry_value['arpu_30_usd']['t_mo_end'];
        $arpu_30_usd_avg = $arpu_30_usd_avg + (float)$sumemry_value['arpu_30_usd']['avg'];

        $reg_sub_total = $reg_sub_total + (float)$sumemry_value['reg_sub']['total'];
        $reg_sub_t_mo_end = $roi_t_mo_end + (float)$sumemry_value['reg_sub']['t_mo_end'];
        $reg_sub_avg = $reg_sub_avg + (float)$sumemry_value['reg_sub']['avg'];

        $roi_total = $roi_total + (float)$sumemry_value['roi']['total'];
        $roi_t_mo_end = $roi_t_mo_end + (float)$sumemry_value['roi']['t_mo_end'];
        $roi_avg = $roi_avg + (float)$sumemry_value['roi']['avg'];

        $bill_total = $bill_total + (float)$sumemry_value['bill']['total'];
        $bill_t_mo_end = $bill_t_mo_end + (float)$sumemry_value['bill']['t_mo_end'];
        $bill_avg = $bill_avg + (float)$sumemry_value['bill']['avg'];

        $firstpush_total = $firstpush_total + (float)$sumemry_value['firstpush']['total'];
        $firstpush_t_mo_end = $firstpush_t_mo_end + (float)$sumemry_value['firstpush']['t_mo_end'];
        $firstpush_avg = $firstpush_avg + (float)$sumemry_value['firstpush']['avg'];

        $dailypush_total = $dailypush_total + (float)$sumemry_value['dailypush']['total'];
        $dailypush_t_mo_end = $dailypush_t_mo_end + (float)$sumemry_value['dailypush']['t_mo_end'];
        $dailypush_avg = $dailypush_avg + (float)$sumemry_value['dailypush']['avg'];

        $last_7_gros_rev_total = $last_7_gros_rev_total + (float)$sumemry_value['last_7_gros_rev']['total'];
        $last_7_gros_rev_t_mo_end = $last_7_gros_rev_t_mo_end + (float)$sumemry_value['last_7_gros_rev']['t_mo_end'];
        $last_7_gros_rev_avg = $last_7_gros_rev_avg + (float)$sumemry_value['last_7_gros_rev']['avg'];

        $last_7_reg_total = $last_7_reg_total + (float)$sumemry_value['last_7_reg']['total'];
        $last_7_reg_t_mo_end = $last_7_reg_t_mo_end + (float)$sumemry_value['last_7_reg']['t_mo_end'];
        $last_7_reg_avg = $last_7_reg_avg + (float)$sumemry_value['last_7_reg']['avg'];

        $last_30_gros_rev_total = $last_30_gros_rev_total + (float)$sumemry_value['last_30_gros_rev']['total'];
        $last_30_gros_rev_t_mo_end = $last_30_gros_rev_t_mo_end + (float)$sumemry_value['last_30_gros_rev']['t_mo_end'];
        $last_30_gros_rev_avg = $last_30_gros_rev_avg + (float)$sumemry_value['last_30_gros_rev']['avg'];

        $last_30_reg_total = $last_30_reg_total + (float)$sumemry_value['last_30_reg']['total'];
        $last_30_reg_t_mo_end = $last_30_reg_t_mo_end + (float)$sumemry_value['last_30_reg']['t_mo_end'];
        $last_30_reg_avg = $last_30_reg_avg + (float)$sumemry_value['last_30_reg']['avg'];

        $last_update = "";

        if(isset($sumemry_value['last_update']))
        $last_update = $sumemry_value['last_update'];

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

        foreach ($sumemry_value['market_cost']['dates'] as $market_cost_key => $market_cost_value) {
          if($sumemry_key == 0){
            $market_cost_sum[$market_cost_key] = 0;
          }

          $market_cost_sum[$market_cost_key] = $market_cost_sum[$market_cost_key] + (float)$market_cost_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $market_cost_arr[$market_cost_key] = ['value' => $market_cost_sum[$market_cost_key], 'class' => $market_cost_value['class']];
          }
        }

        foreach ($sumemry_value['misc_cost']['dates'] as $misc_cost_key => $misc_cost_value) {
          if($sumemry_key == 0){
            $misc_cost_sum[$misc_cost_key] = 0;
          }

          $misc_cost_sum[$misc_cost_key] = $misc_cost_sum[$misc_cost_key] + (float)$misc_cost_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $misc_cost_arr[$misc_cost_key] = ['value' => $misc_cost_sum[$misc_cost_key], 'class' => $misc_cost_value['class']];
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

        foreach ($sumemry_value['other_tax']['dates'] as $other_tax_key => $other_tax_value) {
          if($sumemry_key == 0){
            $other_tax_sum[$other_tax_key] = 0;
          }

          $other_tax_sum[$other_tax_key] = $other_tax_sum[$other_tax_key] + (float)$other_tax_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $other_tax_arr[$other_tax_key] = ['value' => $other_tax_sum[$other_tax_key], 'class' => $other_tax_value['class']];
          }
        }

        foreach ($sumemry_value['vat']['dates'] as $vat_key => $vat_value) {
          if($sumemry_key == 0){
            $vat_sum[$vat_key] = 0;
          }

          $vat_sum[$vat_key] = $vat_sum[$vat_key] + (float)$vat_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $vat_arr[$vat_key] = ['value' => $vat_sum[$vat_key], 'class' => $vat_value['class']];
          }
        }

        foreach ($sumemry_value['wht']['dates'] as $wht_key => $wht_value) {
          if($sumemry_key == 0){
            $wht_sum[$wht_key] = 0;
          }

          $wht_sum[$wht_key] = $wht_sum[$wht_key] + (float)$wht_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $wht_arr[$wht_key] = ['value' => $wht_sum[$wht_key], 'class' => $wht_value['class']];
          }
        }

        foreach ($sumemry_value['misc_tax']['dates'] as $misc_tax_key => $misc_tax_value) {
          if($sumemry_key == 0){
            $misc_tax_sum[$misc_tax_key] = 0;
          }

          $misc_tax_sum[$misc_tax_key] = $misc_tax_sum[$misc_tax_key] + (float)$misc_tax_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $misc_tax_arr[$misc_tax_key] = ['value' => $misc_tax_sum[$misc_tax_key], 'class' => $misc_tax_value['class']];
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

        foreach ($sumemry_value['net_after_tax']['dates'] as $net_after_tax_key => $net_after_tax_value) {
          if($sumemry_key == 0){
            $net_after_tax_sum[$net_after_tax_key] = 0;
          }

          $net_after_tax_sum[$net_after_tax_key] = $net_after_tax_sum[$net_after_tax_key] + (float)$net_after_tax_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $net_after_tax_arr[$net_after_tax_key] = ['value' => $net_after_tax_sum[$net_after_tax_key], 'class' => $net_after_tax_value['class']];
          }
        }

        foreach ($sumemry_value['net_revenue_after_tax']['dates'] as $net_revenue_after_tax_key => $net_revenue_after_tax_value) {
          if($sumemry_key == 0){
            $net_revenue_after_tax_sum[$net_revenue_after_tax_key] = 0;
          }

          $net_revenue_after_tax_sum[$net_revenue_after_tax_key] = $net_revenue_after_tax_sum[$net_revenue_after_tax_key] + (float)$net_revenue_after_tax_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $net_revenue_after_tax_arr[$net_revenue_after_tax_key] = ['value' => $net_revenue_after_tax_sum[$net_revenue_after_tax_key], 'class' => $net_revenue_after_tax_value['class']];
          }
        }

        foreach ($sumemry_value['mo']['dates'] as $mo_key => $mo_value) {
          if($sumemry_key == 0){
            $mo_sum[$mo_key] = 0;
          }

          $mo_sum[$mo_key] = $mo_sum[$mo_key] + (float)$mo_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $mo_arr[$mo_key] = ['value' => $mo_sum[$mo_key], 'class' => $mo_value['class']];
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

        foreach ($sumemry_value['price_mo']['dates'] as $price_mo_key => $price_mo_value) {
          if($sumemry_key == 0){
            $price_mo_sum[$price_mo_key] = 0;
          }

          if(count($sumemry)-1 == $sumemry_key)
          {

            $price_mo_sum[$price_mo_key] = ($mo_arr[$price_mo_key]['value'] != 0) ? $cost_campaign_arr[$price_mo_key]['value'] / $mo_arr[$price_mo_key]['value'] : 0;

            $price_mo_arr[$price_mo_key] = ['value' => $price_mo_sum[$price_mo_key], 'class' => $price_mo_value['class']];
          }
        }

        foreach ($sumemry_value['price_mo_cost']['dates'] as $price_mo_cost_key => $price_mo_cost_value) {
          if($sumemry_key == 0){
            $price_mo_cost_sum[$price_mo_cost_key] = 0;
          }

          $price_mo_cost_sum[$price_mo_cost_key] = $price_mo_cost_sum[$price_mo_cost_key] + (float)$price_mo_cost_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $price_mo_cost_arr[$price_mo_cost_key] = ['value' => $price_mo_cost_sum[$price_mo_cost_key], 'class' => $price_mo_cost_value['class']];
          }
        }

        foreach ($sumemry_value['price_mo_mo']['dates'] as $price_mo_mo_key => $price_mo_mo_value) {
          if($sumemry_key == 0){
            $price_mo_mo_sum[$price_mo_mo_key] = 0;
          }

          $price_mo_mo_sum[$price_mo_mo_key] = $price_mo_mo_sum[$price_mo_mo_key] + (float)$price_mo_mo_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $price_mo_mo_arr[$price_mo_mo_key] = ['value' => $price_mo_mo_sum[$price_mo_mo_key], 'class' => $price_mo_mo_value['class']];
          }
        }

        foreach ($sumemry_value['active_subs']['dates'] as $active_subs_key => $active_subs_value) {
          if($sumemry_key == 0){
            $active_subs_sum[$active_subs_key] = 0;
          }

          $active_subs_sum[$active_subs_key] = $active_subs_sum[$active_subs_key] + (float)$active_subs_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $active_subs_arr[$active_subs_key] = ['value' => $active_subs_sum[$active_subs_key], 'class' => $active_subs_value['class']];
          }
        }

        foreach ($sumemry_value['arpu_7']['dates'] as $arpu_7_key => $arpu_7_value) {
          if($sumemry_key == 0){
            $arpu_7_sum[$arpu_7_key] = 0;
          }

          $arpu_7_sum[$arpu_7_key] = $arpu_7_sum[$arpu_7_key] + (float)$arpu_7_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $arpu_7_arr[$arpu_7_key] = ['value' => $arpu_7_sum[$arpu_7_key], 'class' => $arpu_7_value['class']];
          }
        }

        foreach ($sumemry_value['arpu_30']['dates'] as $arpu_30_key => $arpu_30_value) {
          if($sumemry_key == 0){
            $arpu_30_sum[$arpu_30_key] = 0;
          }

          $arpu_30_sum[$arpu_30_key] = $arpu_30_sum[$arpu_30_key] + (float)$arpu_30_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $arpu_30_arr[$arpu_30_key] = ['value' => $arpu_30_sum[$arpu_30_key], 'class' => $arpu_30_value['class']];
          }
        }

        foreach ($sumemry_value['reg_sub']['dates'] as $reg_sub_key => $reg_sub_value) {
          if($sumemry_key == 0){
            $reg_sub_sum[$reg_sub_key] = 0;
          }

          $reg_sub_sum[$reg_sub_key] = $reg_sub_sum[$reg_sub_key] + (float)$reg_sub_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $reg_sub_arr[$reg_sub_key] = ['value' => $reg_sub_sum[$reg_sub_key], 'class' => $reg_sub_value['class']];
          }
        }

        foreach ($sumemry_value['last_7_gros_rev']['dates'] as $last_7_gros_rev_key => $last_7_gros_rev_value) {
          if($sumemry_key == 0){
            $last_7_gros_rev_sum[$last_7_gros_rev_key] = 0;
          }

          $last_7_gros_rev_sum[$last_7_gros_rev_key] = $last_7_gros_rev_sum[$last_7_gros_rev_key] + (float)$last_7_gros_rev_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $last_7_gros_rev_arr[$last_7_gros_rev_key] = ['value' => $last_7_gros_rev_sum[$last_7_gros_rev_key], 'class' => $last_7_gros_rev_value['class']];
          }
        }

        foreach ($sumemry_value['last_7_reg']['dates'] as $last_7_reg_key => $last_7_reg_value) {
          if($sumemry_key == 0){
            $last_7_reg_sum[$last_7_reg_key] = 0;
          }

          $last_7_reg_sum[$last_7_reg_key] = $last_7_reg_sum[$last_7_reg_key] + (float)$last_7_reg_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $last_7_reg_arr[$last_7_reg_key] = ['value' => $last_7_reg_sum[$last_7_reg_key], 'class' => $last_7_reg_value['class']];
          }
        }

        foreach ($sumemry_value['last_30_gros_rev']['dates'] as $last_30_gros_rev_key => $last_30_gros_rev_value) {
          if($sumemry_key == 0){
            $last_30_gros_rev_sum[$last_30_gros_rev_key] = 0;
          }

          $last_30_gros_rev_sum[$last_30_gros_rev_key] = $last_30_gros_rev_sum[$last_30_gros_rev_key] + (float)$last_30_gros_rev_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $last_30_gros_rev_arr[$last_30_gros_rev_key] = ['value' => $last_30_gros_rev_sum[$last_30_gros_rev_key], 'class' => $last_30_gros_rev_value['class']];
          }
        }

        foreach ($sumemry_value['last_30_reg']['dates'] as $last_30_reg_key => $last_30_reg_value) {
          if($sumemry_key == 0){
            $last_30_reg_sum[$last_30_reg_key] = 0;
          }

          $last_30_reg_sum[$last_30_reg_key] = $last_30_reg_sum[$last_30_reg_key] + (float)$last_30_reg_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $last_30_reg_arr[$last_30_reg_key] = ['value' => $last_30_reg_sum[$last_30_reg_key], 'class' => $last_30_reg_value['class']];
          }
        }

        foreach ($sumemry_value['roi']['dates'] as $roi_key => $roi_value) {
          if($sumemry_key == 0){
              $roi_sum[$roi_key] = 0;
          }

          if(count($sumemry)-1 == $sumemry_key)
          {
            $arpu_7_usd[$roi_key] = ($last_7_reg_arr[$roi_key]['value'] != 0) ? $last_7_gros_rev_arr[$roi_key]['value'] / ($last_7_reg_arr[$roi_key]['value'] + $active_subs_arr[$roi_key]['value']) : 0;

            if($roi_key == date('Y-m')){
              $arpu_30_usd[$roi_key] = ($last_30_reg_arr[$roi_key]['value'] != 0) ? $last_30_gros_rev_arr[$roi_key]['value'] / ($last_30_reg_arr[$roi_key]['value'] + $active_subs_arr[$roi_key]['value']) : 0;
            }else{
              $arpu_30_usd[$roi_key] = ($reg_arr[$roi_key]['value'] != 0) ? $gros_rev_usd_arr[$roi_key]['value'] / ($reg_arr[$roi_key]['value'] + $active_subs_arr[$roi_key]['value']): 0;
            }

            if($roi_key == date('Y-m')){
              $price_mo[$roi_key] = ($price_mo_mo_arr[$roi_key]['value'] != 0) ? $price_mo_cost_arr[$roi_key]['value'] / $price_mo_mo_arr[$roi_key]['value'] : 0;
            }else{
              $price_mo[$roi_key] = ($mo_arr[$roi_key]['value'] != 0) ? $cost_campaign_arr[$roi_key]['value'] / $mo_arr[$roi_key]['value'] : 0;
            }

            $roi_sum[$roi_key] = ($arpu_30_usd[$roi_key] != 0) ? $price_mo[$roi_key] / $arpu_30_usd[$roi_key] : 0;
            $roi_arr[$roi_key] = ['value' => $roi_sum[$roi_key], 'class' => $roi_value['class']];

            $arpu_7_usd_arr[$roi_key] = ['value' => $arpu_7_usd[$roi_key], 'class' => $roi_value['class']];
            $arpu_30_usd_arr[$roi_key] = ['value' => $arpu_30_usd[$roi_key], 'class' => $roi_value['class']];
            $price_mo_arr[$roi_key] = ['value' => $price_mo[$roi_key], 'class' => $roi_value['class']];
          }
        }

        foreach ($sumemry_value['fp_success']['dates'] as $fp_success_key => $fp_success_value) {
          if($sumemry_key == 0){
            $fp_success_sum[$fp_success_key] = 0;
          }

          $fp_success_sum[$fp_success_key] = $fp_success_sum[$fp_success_key] + (float)$fp_success_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $fp_success_arr[$fp_success_key] = ['value' => $fp_success_sum[$fp_success_key], 'class' => $fp_success_value['class']];
          }
        }

        foreach ($sumemry_value['fp_failed']['dates'] as $fp_failed_key => $fp_failed_value) {
          if($sumemry_key == 0){
            $fp_failed_sum[$fp_failed_key] = 0;
          }

          $fp_failed_sum[$fp_failed_key] = $fp_failed_sum[$fp_failed_key] + (float)$fp_failed_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $fp_failed_arr[$fp_failed_key] = ['value' => $fp_failed_sum[$fp_failed_key], 'class' => $fp_failed_value['class']];
          }
        }

        foreach ($sumemry_value['dp_success']['dates'] as $dp_success_key => $dp_success_value) {
          if($sumemry_key == 0){
            $dp_success_sum[$dp_success_key] = 0;
          }

          $dp_success_sum[$dp_success_key] = $dp_success_sum[$dp_success_key] + (float)$dp_success_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $dp_success_arr[$dp_success_key] = ['value' => $dp_success_sum[$dp_success_key], 'class' => $dp_success_value['class']];
          }
        }

        foreach ($sumemry_value['dp_failed']['dates'] as $dp_failed_key => $dp_failed_value) {
          if($sumemry_key == 0){
            $dp_failed_sum[$dp_failed_key] = 0;
          }

          $dp_failed_sum[$dp_failed_key] = $dp_failed_sum[$dp_failed_key] + (float)$dp_failed_value['value'];

          if(count($sumemry)-1 == $sumemry_key)
          {
            $dp_failed_arr[$dp_failed_key] = ['value' => $dp_failed_sum[$dp_failed_key], 'class' => $dp_failed_value['class']];
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

          if(count($sumemry)-1 == $sumemry_key)
          {
            $dp_success = $dp_success_sum[$bill_key];
            $dp_failed = $dp_failed_sum[$bill_key];
            $active_subs = $active_subs_sum[$bill_key];

            $sent = $dp_success + $dp_failed;

            if($sent == 0)
            {
              if($active_subs > 0)
              {
                $bill_sum[$bill_key] = ($dp_success/$active_subs)*100;
              }
            }
            else if($dp_failed == 0)
            {
              if($active_subs > 0)
              {
                $bill_sum[$bill_key] = ($dp_success/$active_subs)*100;
              }
            }else
            {
              if($active_subs > 0)
              {
                $bill_sum[$bill_key] = ($dp_success/$active_subs)*100;
              }
              else
              {
                $bill_sum[$bill_key] = ($dp_success/$sent)*100;
              }
            }

            $bill_arr[$bill_key] = ['value' => $bill_sum[$bill_key], 'class' => $bill_value['class']];
          }
        }

        foreach ($sumemry_value['firstpush']['dates'] as $firstpush_key => $firstpush_value) {
          if($sumemry_key == 0){
              $firstpush_sum[$firstpush_key] = 0;
          }

          if(count($sumemry)-1 == $sumemry_key)
          {
            $fp_success = $fp_success_sum[$firstpush_key];
            $fp_failed = $fp_failed_sum[$firstpush_key];
            $active_subs = $active_subs_sum[$firstpush_key];

            $sent = $fp_success + $fp_failed;

            if($sent == 0)
            {
              if($active_subs > 0)
              {
                $firstpush_sum[$firstpush_key] = ($fp_success/$active_subs)*100;
              }
            }
            else if($fp_failed == 0)
            {
              if($active_subs > 0)
              {
                $firstpush_sum[$firstpush_key] = ($fp_success/$active_subs)*100;
              }
            }
            else
            {
              $firstpush_sum[$firstpush_key] = ($fp_success/$sent)*100;
            }

            $firstpush_arr[$firstpush_key] = ['value' => $firstpush_sum[$firstpush_key], 'class' => $firstpush_value['class']];
          }
        }

        foreach ($sumemry_value['dailypush']['dates'] as $dailypush_key => $dailypush_value) {
          if($sumemry_key == 0){
              $dailypush_sum[$dailypush_key] = 0;
          }

          if(count($sumemry)-1 == $sumemry_key)
          {
            $dp_success = $dp_success_sum[$dailypush_key];
            $dp_failed = $dp_failed_sum[$dailypush_key];
            $active_subs = $active_subs_sum[$dailypush_key];

            $sent = $dp_success + $dp_failed;

            if($sent == 0)
            {
              if($active_subs > 0)
              {
                $dailypush_sum[$dailypush_key] = ($dp_success/$active_subs)*100;
              }
            }
            else if($dp_failed == 0)
            {
              if($active_subs > 0)
              {
                $dailypush_sum[$dailypush_key] = ($dp_success/$active_subs)*100;
              }
            }
            else
            {
              $dailypush_sum[$dailypush_key] = ($dp_success/$sent)*100;
            }

            $dailypush_arr[$dailypush_key] = ['value' => $dailypush_sum[$dailypush_key], 'class' => $dailypush_value['class']];
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

      $dataArr['market_cost']['dates'] = $market_cost_arr;
      $dataArr['market_cost']['total'] = $market_cost_total;
      $dataArr['market_cost']['t_mo_end'] = $market_cost_t_mo_end;
      $dataArr['market_cost']['avg'] = $market_cost_avg;

      $dataArr['misc_cost']['dates'] = $misc_cost_arr;
      $dataArr['misc_cost']['total'] = $misc_cost_total;
      $dataArr['misc_cost']['t_mo_end'] = $misc_cost_t_mo_end;
      $dataArr['misc_cost']['avg'] = $misc_cost_avg;

      $dataArr['platform']['dates'] = $platform_arr;
      $dataArr['platform']['total'] = $platform_total;
      $dataArr['platform']['t_mo_end'] = $platform_t_mo_end;
      $dataArr['platform']['avg'] = $platform_avg;

      $dataArr['pnl']['dates'] = $pnl_arr;
      $dataArr['pnl']['total'] = $pnl_total;
      $dataArr['pnl']['t_mo_end'] = $pnl_t_mo_end;
      $dataArr['pnl']['avg'] = $pnl_avg;

      $dataArr['net_after_tax']['dates'] = $net_after_tax_arr;
      $dataArr['net_after_tax']['total'] = $net_after_tax_total;
      $dataArr['net_after_tax']['t_mo_end'] = $net_after_tax_t_mo_end;
      $dataArr['net_after_tax']['avg'] = $net_after_tax_avg;

      $dataArr['net_revenue_after_tax']['dates'] = $net_revenue_after_tax_arr;
      $dataArr['net_revenue_after_tax']['total'] = $net_revenue_after_tax_total;
      $dataArr['net_revenue_after_tax']['t_mo_end'] = $net_revenue_after_tax_t_mo_end;
      $dataArr['net_revenue_after_tax']['avg'] = $net_revenue_after_tax_avg;

      $dataArr['br']['dates'] = $br_arr;
      $dataArr['br']['total'] = $br_total;
      $dataArr['br']['t_mo_end'] = $br_t_mo_end;
      $dataArr['br']['avg'] = $br_avg;

      $dataArr['fp']['dates'] = $fp_arr;
      $dataArr['fp']['total'] = $fp_total;
      $dataArr['fp']['t_mo_end'] = $fp_t_mo_end;
      $dataArr['fp']['avg'] = $fp_avg;

      $dataArr['fp_success']['dates'] = $fp_success_arr;
      $dataArr['fp_success']['total'] = $fp_success_total;
      $dataArr['fp_success']['t_mo_end'] = $fp_success_t_mo_end;
      $dataArr['fp_success']['avg'] = $fp_success_avg;

      $dataArr['fp_failed']['dates'] = $fp_failed_arr;
      $dataArr['fp_failed']['total'] = $fp_failed_total;
      $dataArr['fp_failed']['t_mo_end'] = $fp_failed_t_mo_end;
      $dataArr['fp_failed']['avg'] = $fp_failed_avg;

      $dataArr['dp']['dates'] = $dp_arr;
      $dataArr['dp']['total'] = $dp_total;
      $dataArr['dp']['t_mo_end'] = $dp_t_mo_end;
      $dataArr['dp']['avg'] = $dp_avg;

      $dataArr['dp_success']['dates'] = $dp_success_arr;
      $dataArr['dp_success']['total'] = $dp_success_total;
      $dataArr['dp_success']['t_mo_end'] = $dp_success_t_mo_end;
      $dataArr['dp_success']['avg'] = $dp_success_avg;

      $dataArr['dp_failed']['dates'] = $dp_failed_arr;
      $dataArr['dp_failed']['total'] = $dp_failed_total;
      $dataArr['dp_failed']['t_mo_end'] = $dp_failed_t_mo_end;
      $dataArr['dp_failed']['avg'] = $dp_failed_avg;

      $dataArr['renewal']['dates'] = $renewal_arr;
      $dataArr['renewal']['total'] = $renewal_total;
      $dataArr['renewal']['t_mo_end'] = $renewal_t_mo_end;
      $dataArr['renewal']['avg'] = $renewal_avg;

      $dataArr['vat']['dates'] = $vat_arr;
      $dataArr['vat']['total'] = $vat_total;
      $dataArr['vat']['t_mo_end'] = $vat_t_mo_end;
      $dataArr['vat']['avg'] = $vat_avg;

      $dataArr['spec_tax']['dates'] = $spec_tax_arr;
      $dataArr['spec_tax']['total'] = $spec_tax_total;
      $dataArr['spec_tax']['t_mo_end'] = $spec_tax_t_mo_end;
      $dataArr['spec_tax']['avg'] = $spec_tax_avg;

      $dataArr['government_cost']['dates'] = $government_cost_arr;
      $dataArr['government_cost']['total'] = $government_cost_total;
      $dataArr['government_cost']['t_mo_end'] = $government_cost_t_mo_end;
      $dataArr['government_cost']['avg'] = $government_cost_avg;

      $dataArr['dealer_commision']['dates'] = $dealer_commision_arr;
      $dataArr['dealer_commision']['total'] = $dealer_commision_total;
      $dataArr['dealer_commision']['t_mo_end'] = $dealer_commision_t_mo_end;
      $dataArr['dealer_commision']['avg'] = $dealer_commision_avg;

      $dataArr['wht']['dates'] = $wht_arr;
      $dataArr['wht']['total'] = $wht_total;
      $dataArr['wht']['t_mo_end'] = $wht_t_mo_end;
      $dataArr['wht']['avg'] = $wht_avg;

      $dataArr['misc_tax']['dates'] = $misc_tax_arr;
      $dataArr['misc_tax']['total'] = $misc_tax_total;
      $dataArr['misc_tax']['t_mo_end'] = $misc_tax_t_mo_end;
      $dataArr['misc_tax']['avg'] = $misc_tax_avg;

      $dataArr['other_tax']['dates'] = $other_tax_arr;
      $dataArr['other_tax']['total'] = $other_tax_total;
      $dataArr['other_tax']['t_mo_end'] = $other_tax_t_mo_end;
      $dataArr['other_tax']['avg'] = $other_tax_avg;

      $dataArr['uso']['dates'] = $uso_arr;
      $dataArr['uso']['total'] = $uso_total;
      $dataArr['uso']['t_mo_end'] = $uso_t_mo_end;
      $dataArr['uso']['avg'] = $uso_avg;

      $dataArr['agre_paxxa']['dates'] = $agre_paxxa_arr;
      $dataArr['agre_paxxa']['total'] = $agre_paxxa_total;
      $dataArr['agre_paxxa']['t_mo_end'] = $agre_paxxa_t_mo_end;
      $dataArr['agre_paxxa']['avg'] = $agre_paxxa_avg;

      $dataArr['sbaf']['dates'] = $sbaf_arr;
      $dataArr['sbaf']['total'] = $sbaf_total;
      $dataArr['sbaf']['t_mo_end'] = $sbaf_t_mo_end;
      $dataArr['sbaf']['avg'] = $sbaf_avg;

      $dataArr['clicks']['dates'] = $clicks_arr;
      $dataArr['clicks']['total'] = $clicks_total;
      $dataArr['clicks']['t_mo_end'] = $clicks_t_mo_end;
      $dataArr['clicks']['avg'] = $clicks_avg;

      $dataArr['ratio_for_cpa']['dates'] = $ratio_for_cpa_arr;
      $dataArr['ratio_for_cpa']['total'] = $ratio_for_cpa_total;
      $dataArr['ratio_for_cpa']['t_mo_end'] = $ratio_for_cpa_t_mo_end;
      $dataArr['ratio_for_cpa']['avg'] = $ratio_for_cpa_avg;

      $dataArr['cpa_price']['dates'] = $cpa_price_arr;
      $dataArr['cpa_price']['total'] = $cpa_price_total;
      $dataArr['cpa_price']['t_mo_end'] = $cpa_price_t_mo_end;
      $dataArr['cpa_price']['avg'] = $cpa_price_avg;

      $dataArr['cr_mo_clicks']['dates'] = $cr_mo_clicks_arr;
      $dataArr['cr_mo_clicks']['total'] = $cr_mo_clicks_total;
      $dataArr['cr_mo_clicks']['t_mo_end'] = $cr_mo_clicks_t_mo_end;
      $dataArr['cr_mo_clicks']['avg'] = $cr_mo_clicks_avg;

      $dataArr['cr_mo_landing']['dates'] = $cr_mo_landing_arr;
      $dataArr['cr_mo_landing']['total'] = $cr_mo_landing_total;
      $dataArr['cr_mo_landing']['t_mo_end'] = $cr_mo_landing_t_mo_end;
      $dataArr['cr_mo_landing']['avg'] = $cr_mo_landing_avg;

      $dataArr['landing']['dates']= $landing_arr;
      $dataArr['landing']['total'] = $landing_total;
      $dataArr['landing']['t_mo_end'] = $landing_t_mo_end;
      $dataArr['landing']['avg'] = $landing_avg;

      $dataArr['mo']['dates'] = $mo_arr;
      $dataArr['mo']['total'] = $mo_total;
      $dataArr['mo']['t_mo_end'] = $mo_t_mo_end;
      $dataArr['mo']['avg'] = $mo_avg;

      $dataArr['reg']['dates'] = $reg_arr;
      $dataArr['reg']['total'] = $reg_total;
      $dataArr['reg']['t_mo_end'] = $reg_t_mo_end;
      $dataArr['reg']['avg'] = $reg_avg;

      $dataArr['unreg']['dates'] = $unreg_arr;
      $dataArr['unreg']['total'] = $unreg_total;
      $dataArr['unreg']['t_mo_end'] = $unreg_t_mo_end;
      $dataArr['unreg']['avg'] = $unreg_avg;

      $dataArr['price_mo']['dates'] = $price_mo_arr;
      $dataArr['price_mo']['total'] = $price_mo_total;
      $dataArr['price_mo']['t_mo_end'] = $price_mo_t_mo_end;
      $dataArr['price_mo']['avg'] = $price_mo_avg;

      $dataArr['price_mo_cost']['dates'] = $price_mo_cost_arr;

      $dataArr['price_mo_mo']['dates'] = $price_mo_mo_arr;

      $dataArr['active_subs']['dates'] = $active_subs_arr;
      $dataArr['active_subs']['total'] = $active_subs_total;
      $dataArr['active_subs']['t_mo_end'] = $active_subs_t_mo_end;
      $dataArr['active_subs']['avg'] = $active_subs_avg;

      $dataArr['arpu_7']['dates'] = $arpu_7_arr;
      $dataArr['arpu_7']['total'] = $arpu_7_total;
      $dataArr['arpu_7']['t_mo_end'] = $arpu_7_t_mo_end;
      $dataArr['arpu_7']['avg'] = $arpu_7_avg;

      $dataArr['arpu_7_usd']['dates'] = $arpu_7_usd_arr;
      $dataArr['arpu_7_usd']['total'] = $arpu_7_usd_total;
      $dataArr['arpu_7_usd']['t_mo_end'] = $arpu_7_usd_t_mo_end;
      $dataArr['arpu_7_usd']['avg'] = $arpu_7_usd_avg;

      $dataArr['arpu_30']['dates'] = $arpu_30_arr;
      $dataArr['arpu_30']['total'] = $arpu_30_total;
      $dataArr['arpu_30']['t_mo_end'] = $arpu_30_t_mo_end;
      $dataArr['arpu_30']['avg'] = $arpu_30_avg;

      $dataArr['arpu_30_usd']['dates'] = $arpu_30_usd_arr;
      $dataArr['arpu_30_usd']['total'] = $arpu_30_usd_total;
      $dataArr['arpu_30_usd']['t_mo_end'] = $arpu_30_usd_t_mo_end;
      $dataArr['arpu_30_usd']['avg'] = $arpu_30_usd_avg;

      $dataArr['reg_sub']['dates'] = $reg_sub_arr;
      $dataArr['reg_sub']['total'] = $reg_sub_total;
      $dataArr['reg_sub']['t_mo_end'] = $reg_sub_t_mo_end;
      $dataArr['reg_sub']['avg'] = $reg_sub_avg;

      $dataArr['roi']['dates'] = $roi_arr;
      $dataArr['roi']['total'] = $roi_total;
      $dataArr['roi']['t_mo_end'] = $roi_t_mo_end;
      $dataArr['roi']['avg'] = $roi_avg;

      $dataArr['bill']['dates'] = $bill_arr;
      $dataArr['bill']['total'] = $roi_total;
      $dataArr['bill']['t_mo_end'] = $roi_t_mo_end;
      $dataArr['bill']['avg'] = $roi_avg;

      $dataArr['firstpush']['dates'] = $firstpush_arr;
      $dataArr['firstpush']['total'] = $firstpush_total;
      $dataArr['firstpush']['t_mo_end'] = $firstpush_t_mo_end;
      $dataArr['firstpush']['avg'] = $firstpush_avg;

      $dataArr['dailypush']['dates'] = $dailypush_arr;
      $dataArr['dailypush']['total'] = $dailypush_total;
      $dataArr['dailypush']['t_mo_end'] = $dailypush_t_mo_end;
      $dataArr['dailypush']['avg'] = $dailypush_avg;

      $dataArr['last_7_gros_rev']['dates'] = $last_7_gros_rev_arr;
      $dataArr['last_7_gros_rev']['total'] = $last_7_gros_rev_total;
      $dataArr['last_7_gros_rev']['t_mo_end'] = $last_7_gros_rev_t_mo_end;
      $dataArr['last_7_gros_rev']['avg'] = $last_7_gros_rev_avg;

      $dataArr['last_7_reg']['dates'] = $last_7_reg_arr;
      $dataArr['last_7_reg']['total'] = $last_7_reg_total;
      $dataArr['last_7_reg']['t_mo_end'] = $last_7_reg_t_mo_end;
      $dataArr['last_7_reg']['avg'] = $last_7_reg_avg;

      $dataArr['last_30_gros_rev']['dates'] = $last_30_gros_rev_arr;
      $dataArr['last_30_gros_rev']['total'] = $last_30_gros_rev_total;
      $dataArr['last_30_gros_rev']['t_mo_end'] = $last_30_gros_rev_t_mo_end;
      $dataArr['last_30_gros_rev']['avg'] = $last_30_gros_rev_avg;

      $dataArr['last_30_reg']['dates'] = $last_30_reg_arr;
      $dataArr['last_30_reg']['total'] = $last_30_reg_total;
      $dataArr['last_30_reg']['t_mo_end'] = $last_30_reg_t_mo_end;
      $dataArr['last_30_reg']['avg'] = $last_30_reg_avg;
      $dataArr['last_update'] = $last_update;

      return $dataArr;
    }
  }

  // summery percentage calculation
  public static function allSummeryPerCal($sumemry_value)
  {
    $sum = 0;
    $avg = 0;
    $T_Mo_End = 0;
    $reaming_day = 0;
    $days = 0;
    $total_churn_sum = 0;
    $total_churn = 0;
    $bill_days = 0;
    $total_bill_sum = 0;
    $total_subscriber = 0;
    $firstPush_sum = 0;
    $dailyPush_sum = 0;
    $total_ltv_sum = 0;
    $total_price_mo = 0;
    $total_price_mo_sum = 0;

    $churn_array = array();
    $churn_arr = array();
    $bill_array = array();
    $bill_arr = array();
    $fpush_array = array();
    $dpush_array = array();
    $arpu7_arr = array();
    $arpu30_arr = array();
    $arpu30_array = array();
    $ltv_array =array();
    $ltv_arr = array();
    $price_mo_array =array();
    $price_mo_arr = array();
    $first_push = array();
    $daily_push = array();

    $today = Carbon::now()->format('Y-m-d');
    $firstdayOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');

    /*---Churn---*/

    if(!empty($sumemry_value['churn']['dates']))
    {
      foreach($sumemry_value['churn']['dates'] as $churn_key => $churn_value)
      {
        if(!isset($churn_arr[$churn_key]['value']))
        {
          $churn_arr[$churn_key]['value'] = 0;
        }

        $total_reg = $sumemry_value['reg']['dates'][$churn_key]['value'];
        $total_unreg = $sumemry_value['unreg']['dates'][$churn_key]['value'];
        $total_subscriber = $sumemry_value['t_sub']['dates'][$churn_key]['value'];
        $total_purged = $sumemry_value['purged']['dates'][$churn_key]['value'];

        if($total_subscriber != 0)
        $total_churn = ( $total_unreg / $total_subscriber );

        if($today != $churn_key)
        {
          $total_churn_sum = $total_churn_sum + $total_churn;
          $days++;
        }

        $churn_arr[$churn_key]['value'] = $total_churn * 100;
        $churn_arr[$churn_key]['class'] = "";
      }
    }

    $churn_avg = ($days != 0) ? ($total_churn_sum / $days ) * 100 : 0;
    $churn_array['dates'] = $churn_arr;
    $churn_array['total'] = 0;
    $churn_array['t_mo_end'] = 0;
    $churn_array['avg'] = $churn_avg;

    $sumemry_value['churn'] = $churn_array;

    /*----Bill Rate----*/

    if(!empty($sumemry_value['bill']['dates']))
    {
      foreach ($sumemry_value['bill']['dates'] as $bill_key => $bill_value)
      {
        if(!isset($bill_arr[$bill_key]['value']))
        {
          $bill_arr[$bill_key]['value'] = 0;
          $first_push[$bill_key]['value'] = 0;
        }

        $total_mt_success = $sumemry_value['mt_success']['dates'][$bill_key]['value'];
        $total_mt_failed = $sumemry_value['mt_failed']['dates'][$bill_key]['value'];
        $total_fmt_success = $sumemry_value['fmt_success']['dates'][$bill_key]['value'];
        $total_fmt_failed = $sumemry_value['fmt_failed']['dates'][$bill_key]['value'];
        $total_subscriber = $sumemry_value['t_sub']['dates'][$bill_key]['value'];

        $total_bill_sum_tmp = UtilityPercentage::billRateWithoutPer($total_mt_success,$total_mt_failed,$total_subscriber);

        $total_first_push_sum_tmp = UtilityPercentage::FirstPushWithoutPer($total_fmt_success,$total_fmt_failed,$total_subscriber);

        $total_daily_push_sum_tmp = UtilityPercentage::DailypushWithoutPer($total_mt_success,$total_mt_failed,$total_subscriber);

        if($today != $bill_key)
        {
          $total_bill_sum = $total_bill_sum + $total_bill_sum_tmp;
          $firstPush_sum = $firstPush_sum + $total_first_push_sum_tmp;
          $dailyPush_sum = $dailyPush_sum + $total_daily_push_sum_tmp;
          $bill_days++;
        }

        $bill_arr[$bill_key]['value'] = $total_bill_sum_tmp * 100 ;
        $first_push[$bill_key]['value'] = $total_first_push_sum_tmp * 100;
        $daily_push[$bill_key]['value'] = $total_daily_push_sum_tmp * 100;

        $bill_arr[$bill_key]['class'] = "";
        $first_push[$bill_key]['class'] = "";
        $daily_push[$bill_key]['class'] = "";
      }
    }

    $bill_avg = ($bill_days != 0) ? ($total_bill_sum / $bill_days ) * 100 : 0;
    $bill_array['dates'] = $bill_arr;
    $bill_array['total'] = 0;
    $bill_array['t_mo_end'] = 0;
    $bill_array['avg'] = $bill_avg;

    $sumemry_value['bill'] = $bill_array;

    /* First Push Value */

    $fpush_array_avg = ($bill_days != 0) ? ($firstPush_sum / $bill_days ) * 100 : 0;
    $fpush_array['dates'] = $first_push;
    $fpush_array['total'] = 0;
    $fpush_array['t_mo_end'] = 0;
    $fpush_array['avg'] = $fpush_array_avg;

    $sumemry_value['first_push'] = $fpush_array;

    /* Daily Push Value */

    $dpush_array_avg = ($bill_days != 0) ? ($dailyPush_sum / $bill_days ) * 100 : 0;
    $dpush_array['dates'] = $daily_push;
    $dpush_array['total'] = 0;
    $dpush_array['t_mo_end'] = 0;
    $dpush_array['avg'] = $dpush_array_avg;

    $sumemry_value['daily_push'] = $dpush_array;

    /* arpu 7 calculation */

    $total_R1_sum = 0;
    $total_R3_sum = 0;
    $arpu7_days = 0;
    $arpu7_avg = 0;

    if(!empty($sumemry_value['arpu7raw']['dates']))
    {
      foreach ($sumemry_value['arpu7raw']['dates'] as $arpu7raw_key => $arpu7raw_value)
      {
        if(!isset($arpu7_arr[$arpu7raw_key]['value']))
        {
          $arpu7_arr[$arpu7raw_key]['value']=0;
        }

        $R1 = $arpu7raw_value['value']['total_gross_revusd'];
        $R3 = $arpu7raw_value['value']['total_total_reg'];

        $arpu7 = 0;

        if($R3 > 0)
        {
          $arpu7 = $R1 / $R3 ;
        }

        if($today != $arpu7raw_key)
        {
          $total_R1_sum = $total_R1_sum + $R1 ;
          $total_R3_sum = $total_R3_sum + $R3 ;
          $arpu7_days++;
        }

        $arpu7_arr[$arpu7raw_key]['value'] = $arpu7;
        $arpu7_arr[$arpu7raw_key]['class'] = "";
      }
    }

    $R1_avg = $total_R1_sum;
    $R3_avg = $total_R3_sum;

    if( $R3_avg > 0)
    {
      $arpu7_avg = $R1_avg / $R3_avg ;
    }

    $arpu7_array['dates'] = $arpu7_arr;
    $arpu7_array['total'] = 0;
    $arpu7_array['t_mo_end'] = 0;
    $arpu7_array['avg'] = $arpu7_avg;

    $sumemry_value['usarpu7'] = $arpu7_array;

    /* arpu 30 calculation */

    $total_R1_sum_arpu30 = 0;
    $total_R3_sum_arpu30 = 0;
    $arpu30_days = 0;
    $arpu30_avg = 0;

    if(!empty($sumemry_value['arpu30raw']['dates']))
    {
      foreach ($sumemry_value['arpu30raw']['dates'] as $arpu30raw_key => $arpu30raw_value)
      {
        if(!isset($arpu30_arr[$arpu30raw_key]['value']))
        {
          $arpu30_arr[$arpu30raw_key]['value'] = 0;
        }

        $arpu30_R1 = $arpu30raw_value['value']['total_gross_revusd'];
        $arpu30_R3 = $arpu30raw_value['value']['total_total_reg'];

        $arpu30 = 0;

        if($arpu30_R3 > 0)
        {
          $arpu30 = $arpu30_R1 / $arpu30_R3 ;
        }

        if($today != $arpu30raw_key)
        {
          $total_R1_sum_arpu30 = $total_R1_sum_arpu30 + $arpu30_R1 ;
          $total_R3_sum_arpu30 = $total_R3_sum_arpu30 + $arpu30_R3 ;
          $arpu30_days++;
        }

        $arpu30_arr[$arpu30raw_key]['value'] = $arpu30;
        $arpu30_arr[$arpu30raw_key]['class'] = "";
      }
    }

    $arpu30_R1_avg = $total_R1_sum_arpu30;
    $arpu30_R3_avg = $total_R3_sum_arpu30;

    if($arpu30_R3_avg > 0)
    {
      $arpu30_avg = $arpu30_R1_avg / $arpu30_R3_avg ;
    }

    $arpu30_array['dates'] = $arpu30_arr;
    $arpu30_array['total'] = 0;
    $arpu30_array['t_mo_end'] = 0;
    $arpu30_array['avg'] = $arpu30_avg;

    $sumemry_value['usarpu30'] = $arpu30_array;

    if(!empty($sumemry_value['ltv']['dates']))
    {
        foreach($sumemry_value['ltv']['dates'] as $ltv_key => $ltv_value)
        {
            if(!isset($ltv_arr[$ltv_key]['value']))
            {
                $ltv_arr[$ltv_key]['value'] = 0;
            }

            $total_turt = $sumemry_value['turt']['dates'][$ltv_key]['value'];
            $total_first_day_active = $sumemry_value['first_day_active']['dates'][$ltv_key]['value'];
            $total_unreg = $sumemry_value['unreg']['dates'][$ltv_key]['value'];
            $total_subscriber = $sumemry_value['t_sub']['dates'][$ltv_key]['value'];
            $total_cost_campaign = $sumemry_value['cost_campaign']['dates'][$ltv_key]['value'];


            $average_subs = ($total_subscriber + $total_first_day_active) / 2;

            if($average_subs > 0)
            {
              $churn = ($total_unreg  / $average_subs) * 100;
            }else{
              $churn = 0;
            }

            $gross_margin = $total_turt - $total_cost_campaign;

            if($average_subs > 0)
            {
              $customer_margin = ($gross_margin  / $average_subs) * 100;
            }else{
              $customer_margin = 0;
            }

            if($churn > 0)
            {
              $ltv = $customer_margin / $churn;
            }else{
              $ltv = 0;
            }


            if($today != $churn_key)
            {
                $total_ltv_sum = $total_ltv_sum + $ltv;
                $days++;
            }

            $ltv_arr[$ltv_key]['value'] = $ltv;
            $ltv_arr[$ltv_key]['class'] = "";
        }
    }

    $ltv_avg = ($days != 0) ? ($total_ltv_sum / $days ) : 0;
    $ltv_array['dates']= $ltv_arr;
    $ltv_array['total']= 0;
    $ltv_array['t_mo_end'] = 0;
    $ltv_array['avg']= $ltv_avg;

    $sumemry_value['ltv'] = $ltv_array;

    if(!empty($sumemry_value['price_mo']['dates']))
    {
      foreach($sumemry_value['price_mo']['dates'] as $price_mo_key => $price_mo_value)
      {
        if(!isset($price_mo_arr[$price_mo_key]['value']))
        {
          $price_mo_arr[$price_mo_key]['value'] = 0;
        }

        $cost_campaign = $sumemry_value['cost_campaign']['dates'][$price_mo_key]['value'];
        $mo = $sumemry_value['mo']['dates'][$price_mo_key]['value'];

        if($mo != 0)
        $total_price_mo = ( $cost_campaign / $mo );

        if($today != $price_mo_key)
        {
          $total_price_mo_sum = $total_price_mo_sum + $total_price_mo;
          $days++;
        }

        $price_mo_arr[$price_mo_key]['value'] = $total_price_mo;
        $price_mo_arr[$price_mo_key]['class'] = "";
      }
    }

    $price_mo_avg = ($days != 0) ? ($total_price_mo_sum / $days ) : 0;
    $price_mo_array['dates'] = $price_mo_arr;
    $price_mo_array['total'] = 0;
    $price_mo_array['t_mo_end'] = 0;
    $price_mo_array['avg'] = $price_mo_avg;

    $sumemry_value['price_mo'] = $price_mo_array;

    return $sumemry_value;
  }

  public static function LTV($gros_rev_usd,$total_subscriber,$total_unreg,$first_day_active,$cost_campaign)
  {
    $average_subs = ($total_subscriber + $first_day_active) / 2;

    if($average_subs > 0)
    {
      $churn = ($total_unreg  / $average_subs) * 100;
    }else{
      $churn = 0;
    }

    $gross_margin = $gros_rev_usd - $cost_campaign;

    if($average_subs > 0)
    {
      $customer_margin = ($gross_margin  / $average_subs) * 100;
    }else{
      $customer_margin = 0;
    }

    if($churn > 0)
    {
      $ltv = $customer_margin / $churn;
    }else{
      $ltv = 0;
    }

    return $ltv;
  }

  public static function allreconcialiationData($sumemry)
  {
    $cost_campaign_sum = $cost_campaign_arr = [];
    $input_cost_campaign_sum = $input_cost_campaign_arr = [];
    $cost_campaign_disc_sum = $cost_campaign_disc_arr = [];
    $mo_sum = $mo_arr = [];
    $input_mo_sum = $input_mo_arr = [];
    $mo_disc_sum = $mo_disc_arr = [];
    $price_mo_sum = $price_mo_arr = [];
    $input_price_mo_sum = $input_price_mo_arr = [];
    $price_mo_disc_sum = $price_mo_disc_arr = [];

    $cost_campaign_total = $cost_campaign_t_mo_end = $cost_campaign_avg = 0;
    $input_cost_campaign_total = $input_cost_campaign_t_mo_end = $input_cost_campaign_avg = 0;
    $cost_campaign_disc_total = $cost_campaign_disc_t_mo_end = $cost_campaign_disc_avg = 0;
    $mo_total = $mo_t_mo_end = $mo_avg = 0;
    $input_mo_total = $input_mo_t_mo_end = $input_mo_avg = 0;
    $mo_disc_total = $mo_disc_t_mo_end = $mo_disc_avg = 0;
    $price_mo_total = $price_mo_t_mo_end = $price_mo_avg = 0;
    $input_price_mo_total = $input_price_mo_t_mo_end = $input_price_mo_avg = 0;
    $price_mo_disc_total = $price_mo_disc_t_mo_end = $price_mo_disc_avg = 0;

    if(!empty($sumemry)){
      foreach ($sumemry as $sumemry_key => $sumemry_value)
      {
        $cost_campaign_total = $cost_campaign_total + (float)$sumemry_value['cost_campaign']['total'];
        $cost_campaign_t_mo_end = $cost_campaign_t_mo_end + (float)$sumemry_value['cost_campaign']['t_mo_end'];
        $cost_campaign_avg = $cost_campaign_avg + (float)$sumemry_value['cost_campaign']['avg'];

        $input_cost_campaign_total = $input_cost_campaign_total + (float)$sumemry_value['input_cost_campaign']['total'];
        $input_cost_campaign_t_mo_end = $input_cost_campaign_t_mo_end + (float)$sumemry_value['input_cost_campaign']['t_mo_end'];
        $input_cost_campaign_avg = $input_cost_campaign_avg + (float)$sumemry_value['input_cost_campaign']['avg'];

        $cost_campaign_disc_avg = ($cost_campaign_total != 0 && $input_cost_campaign_total != 0) ? (($input_cost_campaign_total - $cost_campaign_total)/$cost_campaign_total)*100 : (float)0;

        $mo_total = $mo_total + (float)$sumemry_value['mo']['total'];
        $mo_t_mo_end = $mo_t_mo_end + (float)$sumemry_value['mo']['t_mo_end'];
        $mo_avg = $mo_avg + (float)$sumemry_value['mo']['avg'];

        $input_mo_total = $input_mo_total + (float)$sumemry_value['input_mo']['total'];
        $input_mo_t_mo_end = $input_mo_t_mo_end + (float)$sumemry_value['input_mo']['t_mo_end'];
        $input_mo_avg = $input_mo_avg + (float)$sumemry_value['input_mo']['avg'];

        $mo_disc_avg = ($mo_total != 0 && $input_mo_total != 0) ? (($input_mo_total - $mo_total)/$mo_total)*100 : (float)0;

        $price_mo_total = $price_mo_total + (float)$sumemry_value['price_mo']['total'];
        $price_mo_t_mo_end = $price_mo_t_mo_end + (float)$sumemry_value['price_mo']['t_mo_end'];
        $price_mo_avg = $price_mo_avg + (float)$sumemry_value['price_mo']['avg'];

        $input_price_mo_total = $input_price_mo_total + (float)$sumemry_value['input_price_mo']['total'];
        $input_price_mo_t_mo_end = $input_price_mo_t_mo_end + (float)$sumemry_value['input_price_mo']['t_mo_end'];
        $input_price_mo_avg = $input_price_mo_avg + (float)$sumemry_value['input_price_mo']['avg'];

        $price_mo_disc_avg = ($price_mo_total != 0 && $input_price_mo_total != 0) ? (($input_price_mo_total - $price_mo_total)/$price_mo_total)*100 : (float)0;

        foreach ($sumemry_value['cost_campaign']['dates'] as $cost_campaign_key => $cost_campaign_value) {
            if($sumemry_key==0){
                $cost_campaign_sum[$cost_campaign_key]=0;
            }

            $cost_campaign_sum[$cost_campaign_key] = $cost_campaign_sum[$cost_campaign_key] + (float)$cost_campaign_value['value'];

            if(count($sumemry)-1 == $sumemry_key)
            {
                $cost_campaign_arr[$cost_campaign_key] = ['value' => $cost_campaign_sum[$cost_campaign_key], 'class' => $cost_campaign_value['class']];
            }
        }

        foreach ($sumemry_value['input_cost_campaign']['dates'] as $input_cost_campaign_key => $input_cost_campaign_value) {
            if($sumemry_key==0){
                $input_cost_campaign_sum[$input_cost_campaign_key]=0;
            }

            $input_cost_campaign_sum[$input_cost_campaign_key] = $input_cost_campaign_sum[$input_cost_campaign_key] + (float)$input_cost_campaign_value['value'];

            if(count($sumemry)-1 == $sumemry_key)
            {
                $input_cost_campaign_arr[$input_cost_campaign_key] = ['value' => $input_cost_campaign_sum[$input_cost_campaign_key], 'class' => $input_cost_campaign_value['class']];
            }
        }

        foreach ($sumemry_value['cost_campaign_disc']['dates'] as $cost_campaign_disc_key => $cost_campaign_disc_value) {
            if($sumemry_key==0){
                $cost_campaign_disc_sum[$cost_campaign_disc_key]=0;
            }

            $cost_campaign_disc_sum[$cost_campaign_disc_key] = ($cost_campaign_sum[$cost_campaign_disc_key] != 0 && $input_cost_campaign_sum[$cost_campaign_disc_key] != 0) ? (($input_cost_campaign_sum[$cost_campaign_disc_key] - $cost_campaign_sum[$cost_campaign_disc_key])/$cost_campaign_sum[$cost_campaign_disc_key])*100 : (float)0;

            if($cost_campaign_disc_sum[$cost_campaign_disc_key] < 10){
              $cost_campaign_disc_class[$cost_campaign_disc_key] = "text-success";
            }else if($cost_campaign_disc_sum[$cost_campaign_disc_key] > 10 && $cost_campaign_disc_sum[$cost_campaign_disc_key] < 50){
              $cost_campaign_disc_class[$cost_campaign_disc_key] = "text-warning";
            }else if($cost_campaign_disc_sum[$cost_campaign_disc_key] > 50){
              $cost_campaign_disc_class[$cost_campaign_disc_key] = "text-danger";
            }else{
              $cost_campaign_disc_class[$cost_campaign_disc_key] = "text-success";
            }

            if(count($sumemry)-1 == $sumemry_key)
            {
                $cost_campaign_disc_arr[$cost_campaign_disc_key] = ['value' => $cost_campaign_disc_sum[$cost_campaign_disc_key], 'class' => $cost_campaign_disc_class[$cost_campaign_disc_key]];
            }
        }

        foreach ($sumemry_value['mo']['dates'] as $mo_key => $mo_value) {
            if($sumemry_key==0){
                $mo_sum[$mo_key]=0;
            }

            $mo_sum[$mo_key] = $mo_sum[$mo_key] + (float)$mo_value['value'];

            if(count($sumemry)-1 == $sumemry_key)
            {
                $mo_arr[$mo_key] = ['value' => $mo_sum[$mo_key], 'class' => $mo_value['class']];
            }
        }

        foreach ($sumemry_value['input_mo']['dates'] as $input_mo_key => $input_mo_value) {
            if($sumemry_key==0){
                $input_mo_sum[$input_mo_key]=0;
            }

            $input_mo_sum[$input_mo_key] = $input_mo_sum[$input_mo_key] + (float)$input_mo_value['value'];

            if(count($sumemry)-1 == $sumemry_key)
            {
                $input_mo_arr[$input_mo_key] = ['value' => $input_mo_sum[$input_mo_key], 'class' => $input_mo_value['class']];
            }
        }

        foreach ($sumemry_value['mo_disc']['dates'] as $mo_disc_key => $mo_disc_value) {
            if($sumemry_key==0){
                $mo_disc_sum[$mo_disc_key]=0;
            }

            $mo_disc_sum[$mo_disc_key] = ($mo_sum[$mo_disc_key] != 0 && $input_mo_sum[$mo_disc_key] != 0) ? (($input_mo_sum[$mo_disc_key] - $mo_sum[$mo_disc_key])/$mo_sum[$mo_disc_key])*100 : (float)0;

            if($mo_disc_sum[$mo_disc_key] < 10){
              $mo_disc_class[$mo_disc_key] = "text-success";
            }else if($mo_disc_sum[$mo_disc_key] > 10 && $mo_disc_sum[$mo_disc_key] < 50){
              $mo_disc_class[$mo_disc_key] = "text-warning";
            }else if($mo_disc_sum[$mo_disc_key] > 50){
              $mo_disc_class[$mo_disc_key] = "text-danger";
            }else{
              $mo_disc_class[$mo_disc_key] = "text-success";
            }

            if(count($sumemry)-1 == $sumemry_key)
            {
                $mo_disc_arr[$mo_disc_key] = ['value' => $mo_disc_sum[$mo_disc_key], 'class' => $mo_disc_class[$mo_disc_key]];
            }
        }

        foreach ($sumemry_value['price_mo']['dates'] as $price_mo_key => $price_mo_value) {
            if($sumemry_key==0){
                $price_mo_sum[$price_mo_key]=0;
            }

            $price_mo_sum[$price_mo_key] = $price_mo_sum[$price_mo_key] + (float)$price_mo_value['value'];

            if(count($sumemry)-1 == $sumemry_key)
            {
                $price_mo_arr[$price_mo_key] = ['value' => $price_mo_sum[$price_mo_key], 'class' => $price_mo_value['class']];
            }
        }

        foreach ($sumemry_value['input_price_mo']['dates'] as $input_price_mo_key => $input_price_mo_value) {
            if($sumemry_key==0){
                $input_price_mo_sum[$input_price_mo_key]=0;
            }

            $input_price_mo_sum[$input_price_mo_key] = $input_price_mo_sum[$input_price_mo_key] + (float)$input_price_mo_value['value'];

            if(count($sumemry)-1 == $sumemry_key)
            {
                $input_price_mo_arr[$input_price_mo_key] = ['value' => $input_price_mo_sum[$input_price_mo_key], 'class' => $input_price_mo_value['class']];
            }
        }

        foreach ($sumemry_value['price_mo_disc']['dates'] as $price_mo_disc_key => $price_mo_disc_value) {
            if($sumemry_key==0){
                $price_mo_disc_sum[$price_mo_disc_key]=0;
            }

            $price_mo_disc_sum[$price_mo_disc_key] = ($price_mo_sum[$price_mo_disc_key] != 0 && $input_price_mo_sum[$price_mo_disc_key] != 0) ? (($input_price_mo_sum[$price_mo_disc_key] - $price_mo_sum[$price_mo_disc_key])/$price_mo_sum[$price_mo_disc_key])*100 : (float)0;

            if($price_mo_disc_sum[$price_mo_disc_key] < 10){
              $price_mo_disc_class[$price_mo_disc_key] = "text-success";
            }else if($price_mo_disc_sum[$price_mo_disc_key] > 10 && $price_mo_disc_sum[$price_mo_disc_key] < 50){
              $price_mo_disc_class[$price_mo_disc_key] = "text-warning";
            }else if($price_mo_disc_sum[$price_mo_disc_key] > 50){
              $price_mo_disc_class[$price_mo_disc_key] = "text-danger";
            }else{
              $price_mo_disc_class[$price_mo_disc_key] = "text-success";
            }

            if(count($sumemry)-1 == $sumemry_key)
            {
                $price_mo_disc_arr[$price_mo_disc_key] = ['value' => $price_mo_disc_sum[$price_mo_disc_key], 'class' => $price_mo_disc_class[$price_mo_disc_key]];
            }
        }
      }
    }

    $dataArr['cost_campaign']['dates'] = $cost_campaign_arr;
    $dataArr['cost_campaign']['total'] = $cost_campaign_total;
    $dataArr['cost_campaign']['t_mo_end'] = $cost_campaign_t_mo_end;
    $dataArr['cost_campaign']['avg'] = $cost_campaign_avg;

    $dataArr['input_cost_campaign']['dates'] = $input_cost_campaign_arr;
    $dataArr['input_cost_campaign']['total'] = $input_cost_campaign_total;
    $dataArr['input_cost_campaign']['t_mo_end'] = $input_cost_campaign_t_mo_end;
    $dataArr['input_cost_campaign']['avg'] = $input_cost_campaign_avg;

    $dataArr['cost_campaign_disc']['dates'] = $cost_campaign_disc_arr;
    $dataArr['cost_campaign_disc']['total'] = $cost_campaign_disc_total;
    $dataArr['cost_campaign_disc']['t_mo_end'] = $cost_campaign_disc_t_mo_end;
    $dataArr['cost_campaign_disc']['avg'] = $cost_campaign_disc_avg;

    $dataArr['mo']['dates'] = $mo_arr;
    $dataArr['mo']['total'] = $mo_total;
    $dataArr['mo']['t_mo_end'] = $mo_t_mo_end;
    $dataArr['mo']['avg'] = $mo_avg;

    $dataArr['input_mo']['dates'] = $input_mo_arr;
    $dataArr['input_mo']['total'] = $input_mo_total;
    $dataArr['input_mo']['t_mo_end'] = $input_mo_t_mo_end;
    $dataArr['input_mo']['avg'] = $input_mo_avg;

    $dataArr['mo_disc']['dates'] = $mo_disc_arr;
    $dataArr['mo_disc']['total'] = $mo_disc_total;
    $dataArr['mo_disc']['t_mo_end'] = $mo_disc_t_mo_end;
    $dataArr['mo_disc']['avg'] = $mo_disc_avg;

    $dataArr['price_mo']['dates'] = $price_mo_arr;
    $dataArr['price_mo']['total'] = $price_mo_total;
    $dataArr['price_mo']['t_mo_end'] = $price_mo_t_mo_end;
    $dataArr['price_mo']['avg'] = $price_mo_avg;

    $dataArr['input_price_mo']['dates'] = $input_price_mo_arr;
    $dataArr['input_price_mo']['total'] = $input_price_mo_total;
    $dataArr['input_price_mo']['t_mo_end'] = $input_price_mo_t_mo_end;
    $dataArr['input_price_mo']['avg'] = $input_price_mo_avg;

    $dataArr['price_mo_disc']['dates'] = $price_mo_disc_arr;
    $dataArr['price_mo_disc']['total'] = $price_mo_disc_total;
    $dataArr['price_mo_disc']['t_mo_end'] = $price_mo_disc_t_mo_end;
    $dataArr['price_mo_disc']['avg'] = $price_mo_disc_avg;

    $dataArr['month_string'] = $sumemry_value['month_string'];

    return $dataArr;
  }

  public static function CountrySumReconcialiation($sumemry)
  {
    $cost_campaign_sum = $cost_campaign_arr = [];
    $input_cost_campaign_sum = $input_cost_campaign_arr = [];
    $cost_campaign_disc_sum = $cost_campaign_disc_arr = [];
    $mo_sum = $mo_arr = [];
    $input_mo_sum = $input_mo_arr = [];
    $mo_disc_sum = $mo_disc_arr = [];
    $price_mo_sum = $price_mo_arr = [];
    $input_price_mo_sum = $input_price_mo_arr = [];
    $price_mo_disc_sum = $price_mo_disc_arr = [];

    $cost_campaign_total = $cost_campaign_t_mo_end = $cost_campaign_avg = 0;
    $input_cost_campaign_total = $input_cost_campaign_t_mo_end = $input_cost_campaign_avg = 0;
    $cost_campaign_disc_total = $cost_campaign_disc_t_mo_end = $cost_campaign_disc_avg = 0;
    $mo_total = $mo_t_mo_end = $mo_avg = 0;
    $input_mo_total = $input_mo_t_mo_end = $input_mo_avg = 0;
    $mo_disc_total = $mo_disc_t_mo_end = $mo_disc_avg = 0;
    $price_mo_total = $price_mo_t_mo_end = $price_mo_avg = 0;
    $input_price_mo_total = $input_price_mo_t_mo_end = $input_price_mo_avg = 0;
    $price_mo_disc_total = $price_mo_disc_t_mo_end = $price_mo_disc_avg = 0;

    $last_update = "";

    if(!empty($sumemry)){
      foreach ($sumemry as $sumemry_key => $sumemry_value)
      {
        $operatorId = $sumemry_value['operator']['id_operator'];
        $country_id = $sumemry_value['country']['id'];

        if(isset($sumemry_value['last_update']))
        $last_update = $sumemry_value['last_update'];

        $cost_campaign_total = $cost_campaign_total + (float)$sumemry_value['cost_campaign']['total'];
        $cost_campaign_t_mo_end = $cost_campaign_t_mo_end + (float)$sumemry_value['cost_campaign']['t_mo_end'];
        $cost_campaign_avg = $cost_campaign_avg + (float)$sumemry_value['cost_campaign']['avg'];

        $input_cost_campaign_total = $input_cost_campaign_total + (float)$sumemry_value['input_cost_campaign']['total'];
        $input_cost_campaign_t_mo_end = $input_cost_campaign_t_mo_end + (float)$sumemry_value['input_cost_campaign']['t_mo_end'];
        $input_cost_campaign_avg = $input_cost_campaign_avg + (float)$sumemry_value['input_cost_campaign']['avg'];

        $cost_campaign_disc_avg = ($cost_campaign_total != 0 && $input_cost_campaign_total != 0) ? (($input_cost_campaign_total - $cost_campaign_total)/$cost_campaign_total)*100 : (float)0;

        $mo_total = $mo_total + (float)$sumemry_value['mo']['total'];
        $mo_t_mo_end = $mo_t_mo_end + (float)$sumemry_value['mo']['t_mo_end'];
        $mo_avg = $mo_avg + (float)$sumemry_value['mo']['avg'];

        $input_mo_total = $input_mo_total + (float)$sumemry_value['input_mo']['total'];
        $input_mo_t_mo_end = $input_mo_t_mo_end + (float)$sumemry_value['input_mo']['t_mo_end'];
        $input_mo_avg = $input_mo_avg + (float)$sumemry_value['input_mo']['avg'];

        $mo_disc_avg = ($mo_total != 0 && $input_mo_total != 0) ? (($input_mo_total - $mo_total)/$mo_total)*100 : (float)0;

        $price_mo_total = $price_mo_total + (float)$sumemry_value['price_mo']['total'];
        $price_mo_t_mo_end = $price_mo_t_mo_end + (float)$sumemry_value['price_mo']['t_mo_end'];
        $price_mo_avg = $price_mo_avg + (float)$sumemry_value['price_mo']['avg'];

        $input_price_mo_total = $input_price_mo_total + (float)$sumemry_value['input_price_mo']['total'];
        $input_price_mo_t_mo_end = $input_price_mo_t_mo_end + (float)$sumemry_value['input_price_mo']['t_mo_end'];
        $input_price_mo_avg = $input_price_mo_avg + (float)$sumemry_value['input_price_mo']['avg'];

        $price_mo_disc_avg = ($price_mo_total != 0 && $input_price_mo_total != 0) ? (($input_price_mo_total - $price_mo_total)/$price_mo_total)*100 : (float)0;

        if(!empty($sumemry_value['cost_campaign']['dates']))
        {
          foreach ($sumemry_value['cost_campaign']['dates'] as $cost_campaign_key => $cost_campaign_value)
          {
            if(!isset($cost_campaign_arr[$cost_campaign_key]['value']))
            {
              $cost_campaign_arr[$cost_campaign_key]['value'] = 0;
            }

            $cost_campaign_arr[$cost_campaign_key]['value'] = $cost_campaign_arr[$cost_campaign_key]['value'] + $cost_campaign_value['value'];
            $cost_campaign_arr[$cost_campaign_key]['class'] = "";
          }
        }

        if(!empty($sumemry_value['input_cost_campaign']['dates']))
        {
          foreach ($sumemry_value['input_cost_campaign']['dates'] as $input_cost_campaign_key => $input_cost_campaign_value)
          {
            if(!isset($input_cost_campaign_arr[$input_cost_campaign_key]['value']))
            {
              $input_cost_campaign_arr[$input_cost_campaign_key]['value'] = 0;
            }

            $input_cost_campaign_arr[$input_cost_campaign_key]['value'] = $input_cost_campaign_arr[$input_cost_campaign_key]['value'] + $input_cost_campaign_value['value'];
            $input_cost_campaign_arr[$input_cost_campaign_key]['class'] = "";
          }
        }

        if(!empty($sumemry_value['cost_campaign_disc']['dates']))
        {
          foreach ($sumemry_value['cost_campaign_disc']['dates'] as $cost_campaign_disc_key => $cost_campaign_disc_value)
          {
            if(!isset($cost_campaign_disc_arr[$cost_campaign_disc_key]['value']))
            {
              $cost_campaign_disc_arr[$cost_campaign_disc_key]['value'] = 0;
            }

            $cost_campaign_disc_arr[$cost_campaign_disc_key]['value'] = $cost_campaign_disc_arr[$cost_campaign_disc_key]['value'] + $cost_campaign_disc_value['value'];
            $cost_campaign_disc_arr[$cost_campaign_disc_key]['class'] = "";
          }
        }

        if(!empty($sumemry_value['mo']['dates']))
        {
          foreach ($sumemry_value['mo']['dates'] as $mo_key => $mo_value)
          {
            if(!isset($mo_arr[$mo_key]['value']))
            {
              $mo_arr[$mo_key]['value'] = 0;
            }

            $mo_arr[$mo_key]['value'] = $mo_arr[$mo_key]['value'] + $mo_value['value'];
            $mo_arr[$mo_key]['class'] = "";
          }
        }

        if(!empty($sumemry_value['input_mo']['dates']))
        {
          foreach ($sumemry_value['input_mo']['dates'] as $input_mo_key => $input_mo_value)
          {
            if(!isset($input_mo_arr[$input_mo_key]['value']))
            {
              $input_mo_arr[$input_mo_key]['value'] = 0;
            }

            $input_mo_arr[$input_mo_key]['value'] = $input_mo_arr[$input_mo_key]['value'] + $input_mo_value['value'];
            $input_mo_arr[$input_mo_key]['class'] = "";
          }
        }

        if(!empty($sumemry_value['mo_disc']['dates']))
        {
          foreach ($sumemry_value['mo_disc']['dates'] as $mo_disc_key => $mo_disc_value)
          {
            if(!isset($mo_disc_arr[$mo_disc_key]['value']))
            {
              $mo_disc_arr[$mo_disc_key]['value'] = 0;
            }

            $mo_disc_arr[$mo_disc_key]['value'] = $mo_disc_arr[$mo_disc_key]['value'] + $mo_disc_value['value'];
            $mo_disc_arr[$mo_disc_key]['class'] = "";
          }
        }

        if(!empty($sumemry_value['price_mo']['dates']))
        {
          foreach ($sumemry_value['price_mo']['dates'] as $price_mo_key => $price_mo_value)
          {
            if(!isset($price_mo_arr[$price_mo_key]['value']))
            {
              $price_mo_arr[$price_mo_key]['value'] = 0;
            }

            $price_mo_arr[$price_mo_key]['value'] = $price_mo_arr[$price_mo_key]['value'] + $price_mo_value['value'];
            $price_mo_arr[$price_mo_key]['class'] = "";
          }
        }

        if(!empty($sumemry_value['input_price_mo']['dates']))
        {
          foreach ($sumemry_value['input_price_mo']['dates'] as $input_price_mo_key => $input_price_mo_value)
          {
            if(!isset($input_price_mo_arr[$input_price_mo_key]['value']))
            {
              $input_price_mo_arr[$input_price_mo_key]['value'] = 0;
            }

            $input_price_mo_arr[$input_price_mo_key]['value'] = $input_price_mo_arr[$input_price_mo_key]['value'] + $input_price_mo_value['value'];
            $input_price_mo_arr[$input_price_mo_key]['class'] = "";
          }
        }

        if(!empty($sumemry_value['price_mo_disc']['dates']))
        {
          foreach ($sumemry_value['price_mo_disc']['dates'] as $price_mo_disc_key => $price_mo_disc_value)
          {
            if(!isset($price_mo_disc_arr[$price_mo_disc_key]['value']))
            {
              $price_mo_disc_arr[$price_mo_disc_key]['value'] = 0;
            }

            $price_mo_disc_arr[$price_mo_disc_key]['value'] = $price_mo_disc_arr[$price_mo_disc_key]['value'] + $price_mo_disc_value['value'];
            $price_mo_disc_arr[$price_mo_disc_key]['class'] = "";
          }
        }
      }
    }

    $dataArr['cost_campaign']['dates'] = $cost_campaign_arr;
    $dataArr['cost_campaign']['total'] = $cost_campaign_total;
    $dataArr['cost_campaign']['t_mo_end'] = $cost_campaign_t_mo_end;
    $dataArr['cost_campaign']['avg'] = $cost_campaign_avg;

    $dataArr['input_cost_campaign']['dates'] = $input_cost_campaign_arr;
    $dataArr['input_cost_campaign']['total'] = $input_cost_campaign_total;
    $dataArr['input_cost_campaign']['t_mo_end'] = $input_cost_campaign_t_mo_end;
    $dataArr['input_cost_campaign']['avg'] = $input_cost_campaign_avg;

    $dataArr['cost_campaign_disc']['dates'] = $cost_campaign_disc_arr;
    $dataArr['cost_campaign_disc']['total'] = $cost_campaign_disc_total;
    $dataArr['cost_campaign_disc']['t_mo_end'] = $cost_campaign_disc_t_mo_end;
    $dataArr['cost_campaign_disc']['avg'] = $cost_campaign_disc_avg;

    $dataArr['mo']['dates'] = $mo_arr;
    $dataArr['mo']['total'] = $mo_total;
    $dataArr['mo']['t_mo_end'] = $mo_t_mo_end;
    $dataArr['mo']['avg'] = $mo_avg;

    $dataArr['input_mo']['dates'] = $input_mo_arr;
    $dataArr['input_mo']['total'] = $input_mo_total;
    $dataArr['input_mo']['t_mo_end'] = $input_mo_t_mo_end;
    $dataArr['input_mo']['avg'] = $input_mo_avg;

    $dataArr['mo_disc']['dates'] = $mo_disc_arr;
    $dataArr['mo_disc']['total'] = $mo_disc_total;
    $dataArr['mo_disc']['t_mo_end'] = $mo_disc_t_mo_end;
    $dataArr['mo_disc']['avg'] = $mo_disc_avg;

    $dataArr['price_mo']['dates'] = $price_mo_arr;
    $dataArr['price_mo']['total'] = $price_mo_total;
    $dataArr['price_mo']['t_mo_end'] = $price_mo_t_mo_end;
    $dataArr['price_mo']['avg'] = $price_mo_avg;

    $dataArr['input_price_mo']['dates'] = $input_price_mo_arr;
    $dataArr['input_price_mo']['total'] = $input_price_mo_total;
    $dataArr['input_price_mo']['t_mo_end'] = $input_price_mo_t_mo_end;
    $dataArr['input_price_mo']['avg'] = $input_price_mo_avg;

    $dataArr['price_mo_disc']['dates'] = $price_mo_disc_arr;
    $dataArr['price_mo_disc']['total'] = $price_mo_disc_total;
    $dataArr['price_mo_disc']['t_mo_end'] = $price_mo_disc_t_mo_end;
    $dataArr['price_mo_disc']['avg'] = $price_mo_disc_avg;

    $dataArr['last_update'] = $last_update;

    return $dataArr;
  }
}
