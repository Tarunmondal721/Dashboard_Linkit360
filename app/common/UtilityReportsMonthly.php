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
use Illuminate\Support\Arr;
use App\common\UtilityPercentage;
use App\common\UtilityReports;

class UtilityReportsMonthly
{
    

    

    

    



    

    

    
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

    public static function  Dailypush($mt_success,$mt_failed,$total_subscriber)
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

    public static function Arpu30($operator,$reportsByIDs,$days,$total_subscriber,$share)
    {
        $arpu =0;
        $day = $days['date'];
        $day = $day."-01";
        $merchent_share = $share['merchent_share'] / 100;
        
        $day_date = new Carbon($day);
        $last_date_cal =  new Carbon($day);

       

        $first_day = $days['date'];
       

        $last_day = $last_date_cal->startOfMonth()->subMonth()->format('Y-m');

        

        //$datesIndividual =Utility::getRangeDates($first_day,$last_day);
       // Utility::getRangeDateNo($datesIndividual)

        $no_of_days = [$first_day, $last_day];
        $id_operator =$operator->id_operator;
        $total_gros_rev = 0; // 7 days total revenue 
        $total_total_reg = 0;  // 7 days total reg 

            if(!empty($no_of_days))
            {
                foreach($no_of_days as $days)
                {
                  
                    $keys = $id_operator.".".$days;
                    $summariserow = Arr::get($reportsByIDs, $keys, 0);

                    //dd($summariserow);
                    $gros_rev = isset($summariserow['gros_rev']) ? $summariserow['gros_rev'] : 0;
                    $total_gros_rev =  $total_gros_rev +  $gros_rev;
                    $total_reg = isset($summariserow['total_reg']) ? $summariserow['total_reg'] : 0;
                    $total_total_reg  = $total_total_reg + $total_reg;
                }

                //ARPU = R1 / (R2 + S)  S for Total subscribe in that day

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

    public static function Arpu30USD($operator,$reportsByIDs,$days,$total_subscriber,$share)
    {
        $arpu =0;
        $day = $days['date'];
        $day = $day."-01";
        $merchent_share = $share['merchent_share'] / 100;
        
        $day_date = new Carbon($day);
        $last_date_cal =  new Carbon($day);

        $first_day = $days['date'];
        $last_day = $last_date_cal->startOfMonth()->subMonth()->format('Y-m');

        //$datesIndividual =Utility::getRangeDates($first_day,$last_day);
        // Utility::getRangeDateNo($datesIndividual)

        $no_of_days = [$first_day, $last_day];
        $id_operator =$operator->id_operator;
        $total_gros_rev = 0; 
        $total_total_reg = 0; 

        if(!empty($no_of_days))
        {
            foreach($no_of_days as $days)
            { 
                $keys = $id_operator.".".$days;
                $summariserow = Arr::get($reportsByIDs, $keys, 0);

                //dd($summariserow);
                $gros_rev = isset($summariserow['rev']) ? $summariserow['rev'] : 0;
                $total_gros_rev =  $total_gros_rev +  $gros_rev;
                $total_reg = isset($summariserow['reg']) ? $summariserow['reg'] : 0;
                $total_total_reg  = $total_total_reg + $total_reg;
            }

            //ARPU = R1 / (R2 + S)  S for Total subscribe in that day

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


    public static function Arpu30Old($operator,$reportsByIDs,$months,$total_subscriber,$share)
    {
        dd($months);
        $arpu =0;
        $day = $days['date'];
        $merchent_share = $share['merchent_share'] / 100;
        
        $day_date = new Carbon($day);
        $last_date_cal =  new Carbon($day);

        $first_day = $day_date->subDays(30)->format('Y-m-d');
        $last_day = $last_date_cal->subDays(1)->format('Y-m-d');

        $datesIndividual =Utility::getRangeDates($first_day,$last_day);

        $no_of_days = Utility::getRangeDateNo($datesIndividual);
        $id_operator =$operator->id_operator;
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

            //ARPU = R1 / (R2 + S)  S for Total subscribe in that day

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

    public static function ROI($id_operator,$reportsByIDs,$days,$total_subscriber,$cost_campaign,$mo,$roiData=array(),$roiActiveSubsData=array())
    {
        $ROI = [];
        $roi = 0;
        $arpu_30 = 0;
        $price_mo = 0;

        $day = $days['date'];

        $day_date = new Carbon($day);
        $last_date_cal =  new Carbon($day);

        $first_day = $days['date'];
        $last_day = $last_date_cal->startOfMonth()->subMonth()->format('Y-m');

        $no_of_days = [$first_day];

        $total_gros_rev_usd = 0;
        $total_reg = 0;
        $total_cost_campaign = 0;
        $total_mo = 0;

        if(!empty($no_of_days))
        {
          foreach($no_of_days as $days)
          {
            if($days == Carbon::now()->format('Y-m')){
                $total_gros_rev_usd = $roiData['share'];
                $total_reg = $roiData['reg'];
                $total_subscriber = $roiActiveSubsData['active_subs'];
                $cost_campaign = $roiActiveSubsData['cost_campaign'];
                $mo = $roiActiveSubsData['mo'];
            }else{
                $keys = $id_operator.".".$days;

                $summariserow = Arr::get($reportsByIDs, $keys, 0);

                $gros_rev_usd = isset($summariserow['share']) ? $summariserow['share'] : 0;

                $total_gros_rev_usd = $total_gros_rev_usd + $gros_rev_usd;

                $reg = isset($summariserow['reg']) ? $summariserow['reg'] : 0;

                $total_reg = $total_reg + $reg;
            }
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
          $ROI['last_7_gros_rev'] = $R1;
          $ROI['last_7_reg'] = $R2;
          $ROI['roi'] = $roi;
        }

        return $ROI;
    }

    public static function calculateTotalAVG($operator,$data,$start_date,$end_date,$no_of_months=array())
    {
        $CurrentMonth = Carbon::now()->format('m'); 
        $monthCount = count($no_of_months);
        $reaming_day = ($monthCount != $CurrentMonth) ? ($monthCount - $CurrentMonth) : 1;
        // dd($reaming_day);

        $result = array();
        $sum =0;
        $avg =0;
        $T_Mo_End =0;
        $today = Carbon::now()->format('Y-m-d');
        $firstdayOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');
        $created = new Carbon($start_date);
        $created_format = $created->format('Y-m-d');
        $dayscount = ($created->diff($end_date));

        $noofDays =$dayscount->days;
        // if not select Date range
        if($created_format == $firstdayOfMonth)
        {
            $reaming_day = Carbon::now()->daysInMonth;
            $reaming_day = $reaming_day-(count($data) - 1);
           // dd($reaming_day);
           // $reaming_day = count($data)-1;
        }
        else
        {
            $reaming_day = $noofDays;
        }

        if(!empty($data))
        {
            $count = count($data)-1;
            foreach($data as $key => $value)
            {
                if($today == $key)
                continue;
                $sum = $sum+$value['value'];
            }

            if($count>0 && $sum > 0)
            {
                $avg = $sum/$count;
            }

            // Total + average * remaining days
            if($count>0 )
            {
                $T_Mo_End = $sum+ ($avg * $reaming_day);
            }
        }

        $result['sum'] = $sum;
        $result['avg'] = $avg;
        $result['T_Mo_End'] = $T_Mo_End;
        return $result;           
    }

    public static function calculateRevTotalAVG($operator,$data,$start_date,$end_date,$no_of_months=array())
    {
        $CurrentMonth = Carbon::now()->format('m'); 
        $monthCount = count($no_of_months);
        $reaming_day = ($monthCount != $CurrentMonth) ? ($monthCount - $CurrentMonth) : 1;
        // dd($reaming_day);

        $result = array();
        $sum =0;
        $avg =0;
        $T_Mo_End =0;
        $today = Carbon::now()->format('Y-m');
        $firstdayOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');
        $created = new Carbon($start_date);
        $created_format = $created->format('Y-m-d');
        $dayscount = ($created->diff($end_date));

        $noofDays =$dayscount->days;
        // if not select Date range
        if($created_format == $firstdayOfMonth)
        {
            $reaming_day = Carbon::now()->daysInMonth;
            $reaming_day = $reaming_day-(count($data) - 1);
           // dd($reaming_day);
           // $reaming_day = count($data)-1;
        }
        else
        {
            $reaming_day = $noofDays;
        }

        if(!empty($data))
        {
            $count = count($data)-1;
            foreach($data as $key => $value)
            {
                if($today == $key)
                continue;
                $sum = $sum+$value['value'];
            }

            if($count>0 && $sum > 0)
            {
                $avg = $sum/$count;
            }

            // Total + average * remaining days
            if($count>0 )
            {
                $T_Mo_End = ($avg * 12);
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
        $sum =0;
        $avg =0;
        $T_Mo_End =0;
        $reaming_day =0;
        $today = Carbon::now()->format('Y-m-d');
        $thisMonth = Carbon::now()->format('Y-m');
        $calculateDayforSubscription = Carbon::now()->subDays(1)->format('Y-m');

       // dd($calculateDayforSubscription);
        $firstdayOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');
        $created = new Carbon($start_date);
        $created_format = $created->format('Y-m-d');
        $dayscount = ($created->diff($end_date));

        // if not select Date range 
        if($created_format == $firstdayOfMonth)
        {
            $reaming_day = Carbon::now()->daysInMonth;
            $reaming_day = $reaming_day-(count($data) - 1);
           // dd($reaming_day);
           // $reaming_day = count($data)-1;
        }
        else
        {
            $reaming_day = $dayscount;
            $reaming_day = 1;

        }
           

        if(!empty($data))
                {

                    $count = count($data)-1;

                    

                    

                    foreach($data as $key => $value)
                    {

                        if($thisMonth > $end_date)
                        $calculateDayforSubscription = $end_date;
                        
                        
                        if($key == $calculateDayforSubscription)
                       
                        {
                            //dd($value['value']);
                            $sum = $value['value'];
                        }

                        
                        

                       // dd($value);
                    }
                    
                    if($count>0 && $sum > 0)
                    {
                        $avg = $sum/$count;

                    }

                    ///Total + average * remaining days

                    if($count>0 )
                    {
                        $T_Mo_End = $sum+ ($avg *$reaming_day); 

                    }


                    
                }    

                $sum =sprintf('%0.2f', $sum);
                $avg =sprintf('%0.2f', $avg);
                $T_Mo_End =sprintf('%0.2f', $T_Mo_End);
        
                $result['sum'] =$sum;
                $result['avg'] =$avg;
                $result['T_Mo_End'] =$T_Mo_End;
                
               // dd($result);


        return $result;

        

              
    }

    /*
    ColorFirstDay method 
    Author : Matainja
    $opetaor ~ All operator array with all Details 
    $rowType ~ String Example tur ,Sub , Reg */


    public static function ColorFirstDay($operator,$rowType)
    {
        $today = Carbon::now()->format('Y-m');

        $previousMonth = Carbon::now()->subMonth()->format('Y-m');

       // dd($today);
        

        /* tur Row Color Set */
        $turRowData = $operator[$rowType]['dates'];
        $datesRebuild =array();

        if(!empty($turRowData))
        {
            $no_ofdays = count($turRowData);
            if($no_ofdays > 1)
            {
                $FirstDateOfcalculation = Carbon::now()->subMonth()->format('Y-m');
                
            }

         foreach($turRowData as $key => $value)
            {
               


                $currentdateValue = $value['value'];
               // dd($currentdateValue);
                $class ="first-ffff-class";

                $datesRebuild[$key]['value']=$currentdateValue;
                        $datesRebuild[$key]['class']=$class;

                
                if($today == $key)
                {

                    
                }
                
                else
                {

                    $class ="";
                    $datePrevious = new Carbon($key);
                $PreviousDate = $datePrevious->subDays(1)->format('Y-m-d');

                $previousDateData = 0;
                if(isset($turRowData[$PreviousDate]['value']))
                  {

                    $previousDateData = $turRowData[$PreviousDate]['value'];

                  }
                  
                  
                 
                  if($currentdateValue > $previousDateData)
                  {
                    $class ="text-success";

                    if(isset($FirstDateOfcalculation) && $key == $FirstDateOfcalculation )
                        {
                            $class ="bg-success text-white";
                        }

                        $datesRebuild[$key]['value']=$currentdateValue;
                        $datesRebuild[$key]['class']=$class;


                  }
                  else
                  {
                    $class ="text-danger";

                    if(isset($FirstDateOfcalculation) && $key == $FirstDateOfcalculation )
                        {
                            $class ="bg-danger text-white";
                        }

                        $datesRebuild[$key]['value']=$currentdateValue;
                        $datesRebuild[$key]['class']=$class;

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
                    if($sumemry_key==0){
                        $tur_sum[$tur_key]=0;
                    }

                    $tur_sum[$tur_key] = $tur_sum[$tur_key] + (float)$tur_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $tur_arr[$tur_key] = ['value' => $tur_sum[$tur_key], 'class' => $tur_value['class']];
                    }
                }


                foreach ($sumemry_value['t_rev']['dates'] as $t_rev_key => $t_rev_value) {
                    if($sumemry_key==0){
                        $t_rev_sum[$t_rev_key]=0;
                    }

                    $t_rev_sum[$t_rev_key] = $t_rev_sum[$t_rev_key] + (float)$t_rev_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $t_rev_arr[$t_rev_key] = ['value' => $t_rev_sum[$t_rev_key], 'class' => $t_rev_value['class']];
                    }
                }

                foreach ($sumemry_value['trat']['dates'] as $trat_key => $trat_value) {
                    if($sumemry_key==0){
                        $trat_sum[$trat_key]=0;
                    }

                    $trat_sum[$trat_key] = $trat_sum[$trat_key] + (float)$trat_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $trat_arr[$trat_key] = ['value' => $trat_sum[$trat_key], 'class' => $trat_value['class']];
                    }
                }


                foreach ($sumemry_value['turt']['dates'] as $turt_key => $turt_value) {
                    if($sumemry_key==0){
                        $turt_sum[$turt_key]=0;
                    }

                    $turt_sum[$turt_key] = $turt_sum[$turt_key] + (float)$turt_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $turt_arr[$turt_key] = ['value' => $turt_sum[$turt_key], 'class' => $turt_value['class']];
                    }
                }


                foreach ($sumemry_value['t_sub']['dates'] as $t_sub_key => $t_sub_value) {
                    if($sumemry_key==0){
                        $t_sub_sum[$t_sub_key]=0;
                    }

                    $t_sub_sum[$t_sub_key] = $t_sub_sum[$t_sub_key] + (float)$t_sub_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $t_sub_arr[$t_sub_key] = ['value' => $t_sub_sum[$t_sub_key], 'class' => $t_sub_value['class']];
                    }
                }


                foreach ($sumemry_value['reg']['dates'] as $reg_key => $reg_value) {
                    if($sumemry_key==0){
                        $reg_sum[$reg_key]=0;
                    }

                    $reg_sum[$reg_key] = $reg_sum[$reg_key] + (float)$reg_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $reg_arr[$reg_key] = ['value' => $reg_sum[$reg_key], 'class' => $reg_value['class']];
                    }
                }


                foreach ($sumemry_value['unreg']['dates'] as $unreg_key => $unreg_value) {
                    if($sumemry_key==0){
                        $unreg_sum[$unreg_key]=0;
                    }

                    $unreg_sum[$unreg_key] = $unreg_sum[$unreg_key] + (float)$unreg_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $unreg_arr[$unreg_key] = ['value' => $unreg_sum[$unreg_key], 'class' => $unreg_value['class']];
                    }
                }                

                foreach ($sumemry_value['purged']['dates'] as $purged_key => $purged_value) {
                    if($sumemry_key==0){
                        $purged_sum[$purged_key]=0;
                    }

                    $purged_sum[$purged_key] = $purged_sum[$purged_key] + (float)$purged_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $purged_arr[$purged_key] = ['value' => $purged_sum[$purged_key], 'class' => $purged_value['class']];
                    }
                } 


                foreach ($sumemry_value['churn']['dates'] as $churn_key => $churn_value) {
                    if($sumemry_key==0){
                        $churn_sum[$churn_key]=0;
                    }

                    $churn_sum[$churn_key] = $churn_sum[$churn_key] + (float)$churn_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $churn_arr[$churn_key] = ['value' => $churn_sum[$churn_key], 'class' => $churn_value['class']];
                    }
                }


                foreach ($sumemry_value['renewal']['dates'] as $renewal_key => $renewal_value) {
                    if($sumemry_key==0){
                        $renewal_sum[$renewal_key]=0;
                    }

                    $renewal_sum[$renewal_key] = $renewal_sum[$renewal_key] + (float)$renewal_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $renewal_arr[$renewal_key] = ['value' => $renewal_sum[$renewal_key], 'class' => $renewal_value['class']];
                    }
                }


                foreach ($sumemry_value['bill']['dates'] as $bill_key => $bill_value) {
                    if($sumemry_key==0){
                        $bill_sum[$bill_key]=0;
                    }

                    $bill_sum[$bill_key] = $bill_sum[$bill_key] + (float)$bill_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $bill_arr[$bill_key] = ['value' => $bill_sum[$bill_key], 'class' => $bill_value['class']];
                    }
                }


                foreach ($sumemry_value['first_push']['dates'] as $first_push_key => $first_push_value) {
                    if($sumemry_key==0){
                        $first_push_sum[$first_push_key]=0;
                    }

                    $first_push_sum[$first_push_key] = $first_push_sum[$first_push_key] + (float)$first_push_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $first_push_arr[$first_push_key] = ['value' => $first_push_sum[$first_push_key], 'class' => $first_push_value['class']];
                    }
                }


                foreach ($sumemry_value['daily_push']['dates'] as $daily_push_key => $daily_push_value) {
                    if($sumemry_key==0){
                        $daily_push_sum[$daily_push_key]=0;
                    }

                    $daily_push_sum[$daily_push_key] = $daily_push_sum[$daily_push_key] + (float)$daily_push_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $daily_push_arr[$daily_push_key] = ['value' => $daily_push_sum[$daily_push_key], 'class' => $daily_push_value['class']];
                    }
                }



                foreach ($sumemry_value['arpu7']['dates'] as $arpu7_key => $arpu7_value) {
                    if($sumemry_key==0){
                        $arpu7_sum[$arpu7_key]=0;
                    }

                    $arpu7_sum[$arpu7_key] = $arpu7_sum[$arpu7_key] + (float)$arpu7_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $arpu7_arr[$arpu7_key] = ['value' => $arpu7_sum[$arpu7_key], 'class' => $arpu7_value['class']];
                    }
                }


                foreach ($sumemry_value['usarpu7']['dates'] as $usarpu7_key => $usarpu7_value) {
                    if($sumemry_key==0){
                        $usarpu7_sum[$usarpu7_key]=0;
                    }

                    $usarpu7_sum[$usarpu7_key] = $usarpu7_sum[$usarpu7_key] + (float)$usarpu7_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $usarpu7_arr[$usarpu7_key] = ['value' => $usarpu7_sum[$usarpu7_key], 'class' => $usarpu7_value['class']];
                    }
                }


                foreach ($sumemry_value['arpu30']['dates'] as $arpu30_key => $arpu30_value) {
                    if($sumemry_key==0){
                        $arpu30_sum[$arpu30_key]=0;
                    }

                    $arpu30_sum[$arpu30_key] = $arpu30_sum[$arpu30_key] + (float)$arpu30_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $arpu30_arr[$arpu30_key] = ['value' => $arpu30_sum[$arpu30_key], 'class' => $arpu30_value['class']];
                    }
                }


                foreach ($sumemry_value['usarpu30']['dates'] as $usarpu30_key => $usarpu30_value) {
                    if($sumemry_key==0){
                        $usarpu30_sum[$usarpu30_key]=0;
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


    public static function monthly_all_summary_data($sumemry)
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

            foreach ($sumemry as $sumemry_key => $sumemry_value)
            {
                // dd($sumemry_value);
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

                foreach ($sumemry_value['tur']['dates'] as $tur_key => $tur_value) {
                    if($sumemry_key==0){
                        $tur_sum[$tur_key]=0;
                    }

                    $tur_sum[$tur_key] = $tur_sum[$tur_key] + (float)$tur_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $tur_arr[$tur_key] = ['value' => $tur_sum[$tur_key], 'class' => $tur_value['class']];
                    }
                }


                foreach ($sumemry_value['t_rev']['dates'] as $t_rev_key => $t_rev_value) {
                    if($sumemry_key==0){
                        $t_rev_sum[$t_rev_key]=0;
                    }

                    $t_rev_sum[$t_rev_key] = $t_rev_sum[$t_rev_key] + (float)$t_rev_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $t_rev_arr[$t_rev_key] = ['value' => $t_rev_sum[$t_rev_key], 'class' => $t_rev_value['class']];
                    }
                }

                foreach ($sumemry_value['trat']['dates'] as $trat_key => $trat_value) {
                    if($sumemry_key==0){
                        $trat_sum[$trat_key]=0;
                    }

                    $trat_sum[$trat_key] = $trat_sum[$trat_key] + (float)$trat_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $trat_arr[$trat_key] = ['value' => $trat_sum[$trat_key], 'class' => $trat_value['class']];
                    }
                }


                foreach ($sumemry_value['turt']['dates'] as $turt_key => $turt_value) {
                    if($sumemry_key==0){
                        $turt_sum[$turt_key]=0;
                    }

                    $turt_sum[$turt_key] = $turt_sum[$turt_key] + (float)$turt_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $turt_arr[$turt_key] = ['value' => $turt_sum[$turt_key], 'class' => $turt_value['class']];
                    }
                }

                foreach ($sumemry_value['net_rev']['dates'] as $net_rev_key => $net_rev_value) {
                    if($sumemry_key==0){
                        $net_rev_sum[$net_rev_key]=0;
                    }

                    $net_rev_sum[$net_rev_key] = $net_rev_sum[$net_rev_key] + (float)$net_rev_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $net_rev_arr[$net_rev_key] = ['value' => $net_rev_sum[$net_rev_key], 'class' => $net_rev_value['class']];
                    }
                }


                foreach ($sumemry_value['t_sub']['dates'] as $t_sub_key => $t_sub_value) {
                    if($sumemry_key==0){
                        $t_sub_sum[$t_sub_key]=0;
                    }

                    $t_sub_sum[$t_sub_key] = $t_sub_sum[$t_sub_key] + (float)$t_sub_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $t_sub_arr[$t_sub_key] = ['value' => $t_sub_sum[$t_sub_key], 'class' => $t_sub_value['class']];
                    }
                }


                foreach ($sumemry_value['reg']['dates'] as $reg_key => $reg_value) {
                    if($sumemry_key==0){
                        $reg_sum[$reg_key]=0;
                    }

                    $reg_sum[$reg_key] = $reg_sum[$reg_key] + (float)$reg_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $reg_arr[$reg_key] = ['value' => $reg_sum[$reg_key], 'class' => $reg_value['class']];
                    }
                }


                foreach ($sumemry_value['unreg']['dates'] as $unreg_key => $unreg_value) {
                    if($sumemry_key==0){
                        $unreg_sum[$unreg_key]=0;
                    }

                    $unreg_sum[$unreg_key] = $unreg_sum[$unreg_key] + (float)$unreg_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $unreg_arr[$unreg_key] = ['value' => $unreg_sum[$unreg_key], 'class' => $unreg_value['class']];
                    }
                }                

                foreach ($sumemry_value['purged']['dates'] as $purged_key => $purged_value) {
                    if($sumemry_key==0){
                        $purged_sum[$purged_key]=0;
                    }

                    $purged_sum[$purged_key] = $purged_sum[$purged_key] + (float)$purged_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $purged_arr[$purged_key] = ['value' => $purged_sum[$purged_key], 'class' => $purged_value['class']];
                    }
                } 


                foreach ($sumemry_value['churn']['dates'] as $churn_key => $churn_value) {
                    if($sumemry_key==0){
                        $churn_sum[$churn_key]=0;
                    }

                    $churn_sum[$churn_key] = $churn_sum[$churn_key] + (float)$churn_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $churn_arr[$churn_key] = ['value' => $churn_sum[$churn_key], 'class' => $churn_value['class']];
                    }
                }


                foreach ($sumemry_value['renewal']['dates'] as $renewal_key => $renewal_value) {
                    if($sumemry_key==0){
                        $renewal_sum[$renewal_key]=0;
                    }

                    $renewal_sum[$renewal_key] = $renewal_sum[$renewal_key] + (float)$renewal_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $renewal_arr[$renewal_key] = ['value' => $renewal_sum[$renewal_key], 'class' => $renewal_value['class']];
                    }
                }

                foreach ($sumemry_value['daily_push_success']['dates'] as $daily_push_success_key => $daily_push_success_value) {
                    if($sumemry_key==0){
                        $daily_push_success_sum[$daily_push_success_key]=0;
                    }

                    $daily_push_success_sum[$daily_push_success_key] = $daily_push_success_sum[$daily_push_success_key] + (float)$daily_push_success_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $daily_push_success_arr[$daily_push_success_key] = ['value' => $daily_push_success_sum[$daily_push_success_key], 'class' => $daily_push_success_value['class']];
                    }
                }

                foreach ($sumemry_value['daily_push_failed']['dates'] as $daily_push_failed_key => $daily_push_failed_value) {
                    if($sumemry_key==0){
                        $daily_push_failed_sum[$daily_push_failed_key]=0;
                    }

                    $daily_push_failed_sum[$daily_push_failed_key] = $daily_push_failed_sum[$daily_push_failed_key] + (float)$daily_push_failed_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $daily_push_failed_arr[$daily_push_failed_key] = ['value' => $daily_push_failed_sum[$daily_push_failed_key], 'class' => $daily_push_failed_value['class']];
                    }
                }


                foreach ($sumemry_value['bill']['dates'] as $bill_key => $bill_value) {
                    if($sumemry_key==0){
                        $bill_sum[$bill_key]=0;
                    }

                    $bill_sum[$bill_key] = $bill_sum[$bill_key] + (float)$bill_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $bill_arr[$bill_key] = ['value' => $bill_sum[$bill_key], 'class' => $bill_value['class']];
                    }
                }


                foreach ($sumemry_value['first_push']['dates'] as $first_push_key => $first_push_value) {
                    if($sumemry_key==0){
                        $first_push_sum[$first_push_key]=0;
                    }

                    $first_push_sum[$first_push_key] = $first_push_sum[$first_push_key] + (float)$first_push_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $first_push_arr[$first_push_key] = ['value' => $first_push_sum[$first_push_key], 'class' => $first_push_value['class']];
                    }
                }


                foreach ($sumemry_value['daily_push']['dates'] as $daily_push_key => $daily_push_value) {
                    if($sumemry_key==0){
                        $daily_push_sum[$daily_push_key]=0;
                    }

                    $daily_push_sum[$daily_push_key] = $daily_push_sum[$daily_push_key] + (float)$daily_push_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $daily_push_arr[$daily_push_key] = ['value' => $daily_push_sum[$daily_push_key], 'class' => $daily_push_value['class']];
                    }
                }



                foreach ($sumemry_value['arpu7']['dates'] as $arpu7_key => $arpu7_value) {
                    if($sumemry_key==0){
                        $arpu7_sum[$arpu7_key]=0;
                    }

                    $arpu7_sum[$arpu7_key] = $arpu7_sum[$arpu7_key] + (float)$arpu7_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $arpu7_arr[$arpu7_key] = ['value' => $arpu7_sum[$arpu7_key], 'class' => $arpu7_value['class']];
                    }
                }


                foreach ($sumemry_value['usarpu7']['dates'] as $usarpu7_key => $usarpu7_value) {
                    if($sumemry_key==0){
                        $usarpu7_sum[$usarpu7_key]=0;
                    }

                    $usarpu7_sum[$usarpu7_key] = $usarpu7_sum[$usarpu7_key] + (float)$usarpu7_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $usarpu7_arr[$usarpu7_key] = ['value' => $usarpu7_sum[$usarpu7_key], 'class' => $usarpu7_value['class']];
                    }
                }


                foreach ($sumemry_value['arpu30']['dates'] as $arpu30_key => $arpu30_value) {
                    if($sumemry_key==0){
                        $arpu30_sum[$arpu30_key]=0;
                    }

                    $arpu30_sum[$arpu30_key] = $arpu30_sum[$arpu30_key] + (float)$arpu30_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $arpu30_arr[$arpu30_key] = ['value' => $arpu30_sum[$arpu30_key], 'class' => $arpu30_value['class']];
                    }
                }


                foreach ($sumemry_value['usarpu30']['dates'] as $usarpu30_key => $usarpu30_value) {
                    if($sumemry_key==0){
                        $usarpu30_sum[$usarpu30_key]=0;
                    }

                    $usarpu30_sum[$usarpu30_key] = $usarpu30_sum[$usarpu30_key] + (float)$usarpu30_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $usarpu30_arr[$usarpu30_key] = ['value' => $usarpu30_sum[$usarpu30_key], 'class' => $usarpu30_value['class']];
                    }
                }


                foreach ($sumemry_value['mt_success']['dates'] as $mt_success_key => $mt_success_value) {
                    if($sumemry_key==0){
                        $mt_success_sum[$mt_success_key]=0;
                    }

                    $mt_success_sum[$mt_success_key] = $mt_success_sum[$mt_success_key] + (float)$mt_success_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $mt_success_arr[$mt_success_key] = ['value' => $mt_success_sum[$mt_success_key], 'class' => $mt_success_value['class']];
                    }
                }


                foreach ($sumemry_value['mt_failed']['dates'] as $mt_failed_key => $mt_failed_value) {
                    if($sumemry_key==0){
                        $mt_failed_sum[$mt_failed_key]=0;
                    }

                    $mt_failed_sum[$mt_failed_key] = $mt_failed_sum[$mt_failed_key] + (float)$mt_failed_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $mt_failed_arr[$mt_failed_key] = ['value' => $mt_failed_sum[$mt_failed_key], 'class' => $mt_failed_value['class']];
                    }
                }

                foreach ($sumemry_value['fmt_success']['dates'] as $fmt_success_key => $fmt_success_value) {
                    if($sumemry_key==0){
                        $fmt_success_sum[$fmt_success_key]=0;
                    }

                    $fmt_success_sum[$fmt_success_key] = $fmt_success_sum[$fmt_success_key] + (float)$fmt_success_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $fmt_success_arr[$fmt_success_key] = ['value' => $fmt_success_sum[$fmt_success_key], 'class' => $fmt_success_value['class']];
                    }
                }

                foreach ($sumemry_value['fmt_failed']['dates'] as $fmt_failed_key => $fmt_failed_value) {
                    if($sumemry_key==0){
                        $fmt_failed_sum[$fmt_failed_key]=0;
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

                $arpu30raw_arr =array();

                if(!empty($sumemry_value['arpu30raw']['dates']))
                {

                   

                foreach($sumemry_value['arpu30raw']['dates'] as $arpu30raw_key => $arpu30raw_value) {

                    if(!isset($arpu30raw_arr[$arpu30raw_key]['value']))
                    {
                        $arpu30raw_arr[$arpu30raw_key]['value']['total_gross_revusd']= 0;
                        $arpu30raw_arr[$arpu30raw_key]['value']['total_total_reg']= 0;
                    }

                    $arpu30raw_arr[$arpu30raw_key]['value']['total_gross_revusd'] =  $arpu30raw_arr[$arpu30raw_key]['value']['total_gross_revusd'] + $arpu30raw_value['value']['total_gross_revusd'];

                    $arpu30raw_arr[$arpu30raw_key]['value']['total_total_reg'] =  $arpu30raw_arr[$arpu30raw_key]['value']['total_total_reg'] + $arpu30raw_value['value']['total_total_reg'];

                    $arpu30raw_arr[$arpu30raw_key]['class'] ="";
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

            $dataArr['usarpu7']['dates'] = $usarpu7_arr;
            $dataArr['usarpu7']['total'] = $usarpu7_total;                
            $dataArr['usarpu7']['t_mo_end'] = $usarpu7_t_mo_end;                
            $dataArr['usarpu7']['avg'] = $usarpu7_avg;

            $dataArr['arpu30']['dates'] = $arpu30_arr;
            $dataArr['arpu30']['total'] = $arpu30_total;                
            $dataArr['arpu30']['t_mo_end'] = $arpu30_t_mo_end;                
            $dataArr['arpu30']['avg'] = $arpu30_avg;

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

            $dataArr['mt_success']['dates'] = $mt_success_arr;
            $dataArr['mt_failed']['dates'] = $mt_failed_arr;

            $dataArr['fmt_success']['dates'] = $fmt_success_arr;
            $dataArr['fmt_failed']['dates'] = $fmt_failed_arr;

            return $dataArr;
    }

    public static function CountrySumOperator($sumemry)
    {
            //dd($sumemry);
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

            $turSumTest = 0;

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

            foreach ($sumemry as $sumemry_key => $sumemry_value)
            {

                $country_id =$sumemry_value['country']['id'];

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

               $t_rev_date_wise_sum =0;
               if (!empty($sumemry_value['t_rev']['dates'])) 
               {
                # code...
               
                       foreach ($sumemry_value['t_rev']['dates'] as $t_rev_key => $t_rev_value) 
                       {

                                if(!isset($t_rev_arr[$t_rev_key]['value']))
                                {
                                    $t_rev_arr[$t_rev_key]['value']=0;

                                }



                          if($t_rev_key == "2022-12-13"  && $country_id == 1 )
                           {

                            $turSumTest = $turSumTest+$t_rev_value['value'];

                           }
                                  

                            $t_rev_arr[$t_rev_key]['value'] =  $t_rev_arr[$t_rev_key]['value'] + $t_rev_value['value'] ;

                             $t_rev_arr[$t_rev_key]['class'] ="";
                        }

                       

                }
           if (!empty($sumemry_value['tur']['dates'])) 
               {

                 foreach ($sumemry_value['tur']['dates'] as $tur_key => $tur_value) 
                 {
                            if(!isset($tur_arr[$tur_key]['value']))
                                        {
                                $tur_arr[$tur_key]['value']=0;
                            }

                          /*  test sum Data

                          if($tur_key == "2022-12-13")
                           {

                            $turSumTest = $turSumTest+$tur_value['value'];

                           }*/
                   


                        $tur_arr[$tur_key]['value'] = $tur_arr[$tur_key]['value']+$tur_value['value'];

                    $tur_arr[$tur_key]['class'] ="";


                }
            }


             if (!empty($sumemry_value['trat']['dates'])) 
               {

                 foreach ($sumemry_value['trat']['dates'] as $trat_key => $trat_value) 
                 {
                            if(!isset($trat_arr[$trat_key]['value']))
                                        {
                                $trat_arr[$trat_key]['value']=0;
                            }

                          
                   


                        $trat_arr[$trat_key]['value'] = $trat_arr[$trat_key]['value']+$trat_value['value'];

                    $trat_arr[$trat_key]['class'] ="";


                }
            }



            if (!empty($sumemry_value['turt']['dates'])) 
               {

                 foreach ($sumemry_value['turt']['dates'] as $turt_key => $turt_value) 
                 {
                            if(!isset($turt_arr[$turt_key]['value']))
                                        {
                                $turt_arr[$turt_key]['value']=0;
                            }

                           
                   


                        $turt_arr[$turt_key]['value'] = $turt_arr[$turt_key]['value']+$turt_value['value'];

                    $turt_arr[$turt_key]['class'] ="";


                }
            }

            if (!empty($sumemry_value['net_rev']['dates'])) 
               {

                 foreach ($sumemry_value['net_rev']['dates'] as $net_rev_key => $net_rev_value) 
                 {
                            if(!isset($net_rev_arr[$net_rev_key]['value']))
                                        {
                                $net_rev_arr[$net_rev_key]['value']=0;
                            }

                           
                   


                        $net_rev_arr[$net_rev_key]['value'] = $net_rev_arr[$net_rev_key]['value']+$net_rev_value['value'];

                    $net_rev_arr[$net_rev_key]['class'] ="";


                }
            }


            if (!empty($sumemry_value['t_sub']['dates'])) 
               {

                 foreach ($sumemry_value['t_sub']['dates'] as $t_sub_key => $t_sub_value) 
                 {
                            if(!isset($t_sub_arr[$t_sub_key]['value']))
                                        {
                                $t_sub_arr[$t_sub_key]['value']=0;
                            }

                           
                   


                        $t_sub_arr[$t_sub_key]['value'] = $t_sub_arr[$t_sub_key]['value']+$t_sub_value['value'];

                    $t_sub_arr[$t_sub_key]['class'] ="";


                }
            }


            if (!empty($sumemry_value['reg']['dates'])) 
               {

                 foreach ($sumemry_value['reg']['dates'] as $reg_key => $reg_value) 
                 {
                            if(!isset($reg_arr[$reg_key]['value']))
                                        {
                                $reg_arr[$reg_key]['value']=0;
                            }

                           
                   


                        $reg_arr[$reg_key]['value'] = $reg_arr[$reg_key]['value']+$reg_value['value'];

                    $reg_arr[$reg_key]['class'] ="";


                }
            }
        
              if (!empty($sumemry_value['unreg']['dates'])) 
               {

                 foreach ($sumemry_value['unreg']['dates'] as $unreg_key => $unreg_value) 
                 {
                            if(!isset($unreg_arr[$unreg_key]['value']))
                                        {
                                $unreg_arr[$unreg_key]['value']=0;
                            }

                          
                   


                        $unreg_arr[$unreg_key]['value'] = $unreg_arr[$unreg_key]['value']+$unreg_value['value'];

                    $unreg_arr[$unreg_key]['class'] ="";


                }
            }



            if (!empty($sumemry_value['purged']['dates'])) 
               {

                     foreach ($sumemry_value['purged']['dates'] as $purged_key => $purged_value) 
                     {
                                if(!isset($purged_arr[$purged_key]['value']))
                                            {
                                    $purged_arr[$purged_key]['value']=0;
                                }

                               /*if($purged_key == "2022-12-14")
                               {



                                $turSumTest = $turSumTest+$purged_value['value'];

                               }*/
                       


                            $purged_arr[$purged_key]['value'] 
                                                  = $purged_arr[$purged_key]['value']+$purged_value['value'];

                        $purged_arr[$purged_key]['class'] ="";


                    }
                }



                if (!empty($sumemry_value['churn']['dates'])) 
               {

                     foreach ($sumemry_value['churn']['dates'] as $churn_key => $churn_value) 
                     {
                                if(!isset($churn_arr[$churn_key]['value']))
                                            {
                                    $churn_arr[$churn_key]['value']=0;
                                }

                               if($churn_key == "2022-12-14")
                               {

                                

                               //   $turSumTest = $turSumTest+$churn_value['value'];

                               }
                       


                            $churn_arr[$churn_key]['value'] 
                                                  = $churn_arr[$churn_key]['value']+$churn_value['value'];

                        $churn_arr[$churn_key]['class'] ="";


                    }
                }


                 if (!empty($sumemry_value['renewal']['dates'])) 
               {

                     foreach ($sumemry_value['renewal']['dates'] as $renewal_key => $renewal_value) 
                     {
                                if(!isset($renewal_arr[$renewal_key]['value']))
                                            {
                                    $renewal_arr[$renewal_key]['value']=0;
                                }

                               if($renewal_key == "2022-12-14")
                               {

                                

                                //$turSumTest = $turSumTest+$renewal_value['value'];

                               }
                       


                            $renewal_arr[$renewal_key]['value'] 
                                                  = $renewal_arr[$renewal_key]['value']+$renewal_value['value'];

                        $renewal_arr[$renewal_key]['class'] ="";


                    }
                }

                if (!empty($sumemry_value['daily_push_success']['dates']))
               {
                foreach ($sumemry_value['daily_push_success']['dates'] as $daily_push_success_key => $daily_push_success_value)
                   {
                    if(!isset($daily_push_success_arr[$daily_push_success_key]['value']))
                    {
                      $daily_push_success_arr[$daily_push_success_key]['value']=0;
                    }
                    $daily_push_success_arr[$daily_push_success_key]['value'] = $daily_push_success_arr[$daily_push_success_key]['value']+$daily_push_success_value['value'];
                    $daily_push_success_arr[$daily_push_success_key]['class'] ="";
                  }
                }

                if (!empty($sumemry_value['daily_push_failed']['dates']))
               {
                foreach ($sumemry_value['daily_push_failed']['dates'] as $daily_push_failed_key => $daily_push_failed_value)
                   {
                    if(!isset($daily_push_failed_arr[$daily_push_failed_key]['value']))
                    {
                      $daily_push_failed_arr[$daily_push_failed_key]['value']=0;
                    }
                    $daily_push_failed_arr[$daily_push_failed_key]['value'] = $daily_push_failed_arr[$daily_push_failed_key]['value']+$daily_push_failed_value['value'];
                    $daily_push_failed_arr[$daily_push_failed_key]['class'] ="";
                  }
                }


                 if (!empty($sumemry_value['bill']['dates'])) 
               {

                     foreach ($sumemry_value['bill']['dates'] as $bill_key => $bill_value) 
                     {
                                if(!isset($bill_arr[$bill_key]['value']))
                                            {
                                    $bill_arr[$bill_key]['value']=0;
                                }

                               if($bill_key == "2022-12-14")
                               {

                                

                              //    $turSumTest = $turSumTest+$bill_value['value'];

                               }
                       


                            $bill_arr[$bill_key]['value'] 
                                                  = $bill_arr[$bill_key]['value']+$bill_value['value'];

                        $bill_arr[$bill_key]['class'] ="";


                    }
                }


                 if (!empty($sumemry_value['first_push']['dates'])) 
               {

                     foreach ($sumemry_value['first_push']['dates'] as $first_push_key => $first_push_value) 
                     {
                                if(!isset($first_push_arr[$first_push_key]['value']))
                                            {
                                    $first_push_arr[$first_push_key]['value']=0;
                                }

                               if($first_push_key == "2022-12-14")
                               {

                                

                               //   $turSumTest = $turSumTest+$first_push_value['value'];

                               }
                       


                            $first_push_arr[$first_push_key]['value'] 
                                                  = $first_push_arr[$first_push_key]['value']+$first_push_value['value'];

                        $first_push_arr[$first_push_key]['class'] ="";


                    }
                }


                if (!empty($sumemry_value['daily_push']['dates'])) 
               {

                     foreach ($sumemry_value['daily_push']['dates'] as $daily_push_key => $daily_push_value) 
                     {
                                if(!isset($daily_push_arr[$daily_push_key]['value']))
                                            {
                                    $daily_push_arr[$daily_push_key]['value']=0;
                                }

                               if($daily_push_key == "2022-12-14")
                               {

                                

                               //   $turSumTest = $turSumTest+$daily_push_value['value'];

                               }
                       


                            $daily_push_arr[$daily_push_key]['value'] 
                                                  = $daily_push_arr[$daily_push_key]['value']+$daily_push_value['value'];

                        $daily_push_arr[$daily_push_key]['class'] ="";


                    }
                }


                if (!empty($sumemry_value['arpu7']['dates'])) 
               {

                     foreach ($sumemry_value['arpu7']['dates'] as $arpu7_key => $arpu7_value) 
                     {
                                if(!isset($arpu7_arr[$arpu7_key]['value']))
                                            {
                                    $arpu7_arr[$arpu7_key]['value']=0;
                                }

                               if($arpu7_key == "2022-12-14")
                               {

                                

                               //   $turSumTest = $turSumTest+$arpu7_value['value'];

                               }
                       


                            $arpu7_arr[$arpu7_key]['value'] 
                                                  = $arpu7_arr[$arpu7_key]['value']+$arpu7_value['value'];

                        $arpu7_arr[$arpu7_key]['class'] ="";


                    }
                }



                if (!empty($sumemry_value['usarpu7']['dates'])) 
               {

                     foreach ($sumemry_value['usarpu7']['dates'] as $usarpu7_key => $usarpu7_value) 
                     {
                                if(!isset($usarpu7_arr[$usarpu7_key]['value']))
                                            {
                                    $usarpu7_arr[$usarpu7_key]['value']=0;
                                }

                               if($arpu7_key == "2022-12-14")
                               {

                                

                               //   $turSumTest = $turSumTest+$usarpu7_value['value'];

                               }
                       


                            $usarpu7_arr[$usarpu7_key]['value'] 
                                                  = $usarpu7_arr[$usarpu7_key]['value']+$usarpu7_value['value'];

                        $usarpu7_arr[$usarpu7_key]['class'] ="";


                    }
                }


                
                if (!empty($sumemry_value['arpu30']['dates'])) 
               {

                     foreach ($sumemry_value['arpu30']['dates'] as $arpu30 => $arpu30_value) 
                     {
                                if(!isset($arpu30_arr[$arpu30]['value']))
                                            {
                                    $arpu30_arr[$arpu30]['value']=0;
                                }

                               if($arpu30 == "2022-12-14")
                               {

                                

                               //   $turSumTest = $turSumTest+$arpu30_value['value'];

                               }
                       


                            $arpu30_arr[$arpu30]['value'] 
                                                  = $arpu30_arr[$arpu30]['value']+$arpu30_value['value'];

                        $arpu30_arr[$arpu30]['class'] ="";


                    }
                }


                 if (!empty($sumemry_value['usarpu30']['dates'])) 
               {

                     foreach ($sumemry_value['usarpu30']['dates'] as $usarpu30_key => $usarpu30_value) 
                     {
                                if(!isset($usarpu30_arr[$usarpu30_key]['value']))
                                            {
                                    $usarpu30_arr[$usarpu30_key]['value']=0;
                                }

                               if($arpu30 == "2022-12-14")
                               {

                                

                               //   $turSumTest = $turSumTest+$usarpu30_value['value'];

                               }
                       


                            $usarpu30_arr[$usarpu30_key]['value'] 
                                                  = $usarpu30_arr[$usarpu30_key]['value']+$usarpu30_value['value'];

                        $usarpu30_arr[$usarpu30_key]['class'] ="";


                    }
                }

                 if (!empty($sumemry_value['mt_success']['dates'])) 
               {

                     foreach ($sumemry_value['mt_success']['dates'] as $mt_success_key => $mt_success_value) 
                     {


                                if(!isset($mt_success_arr[$mt_success_key]['value']))
                                            {
                                    $mt_success_arr[$mt_success_key]['value']=0;
                                }

                              
                       


                            $mt_success_arr[$mt_success_key]['value'] 
                                                  = $mt_success_arr[$mt_success_key]['value']+$mt_success_value['value'];

                        $mt_success_arr[$mt_success_key]['class'] ="";


                    }


                }

                  if (!empty($sumemry_value['mt_failed']['dates'])) 
               {

                     foreach ($sumemry_value['mt_failed']['dates'] as $mt_failed_key => $mt_failed_value) 
                     {
                                if(!isset($mt_failed_arr[$mt_failed_key]['value']))
                                            {
                                    $mt_failed_arr[$mt_failed_key]['value']=0;
                                }

                              
                       


                            $mt_failed_arr[$mt_failed_key]['value'] 
                                                  = $mt_failed_arr[$mt_failed_key]['value']+$mt_failed_value['value'];

                        $mt_failed_arr[$mt_failed_key]['class'] ="";


                    }
                }

                if (!empty($sumemry_value['fmt_success']['dates'])) 
                {
                    foreach ($sumemry_value['fmt_success']['dates'] as $fmt_success_key => $fmt_success_value) 
                    {
                        if(!isset($fmt_success_arr[$fmt_success_key]['value']))
                        {
                            $fmt_success_arr[$fmt_success_key]['value']=0;
                        }

                        $fmt_success_arr[$fmt_success_key]['value'] = $fmt_success_arr[$fmt_success_key]['value']+$fmt_success_value['value'];

                        $fmt_success_arr[$fmt_success_key]['class'] = "";
                    }
                }

                if(!empty($sumemry_value['fmt_failed']['dates'])) 
                {
                    foreach ($sumemry_value['fmt_failed']['dates'] as $fmt_failed_key => $fmt_failed_value) 
                    {
                        if(!isset($fmt_failed_arr[$fmt_failed_key]['value']))
                        {
                            $fmt_failed_arr[$fmt_failed_key]['value']=0;
                        }

                        $fmt_failed_arr[$fmt_failed_key]['value'] = $fmt_failed_arr[$fmt_failed_key]['value']+$fmt_failed_value['value'];
                        $fmt_failed_arr[$fmt_failed_key]['class'] = "";
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

            $dataArr['bill']['dates'] = $bill_arr;
            $dataArr['bill']['total'] = $bill_total;                
            $dataArr['bill']['t_mo_end'] = $bill_t_mo_end;                
            $dataArr['bill']['avg'] = $bill_avg;

            $dataArr['daily_push']['dates'] = $daily_push_arr;
            $dataArr['daily_push']['total'] = $daily_push_total;                
            $dataArr['daily_push']['t_mo_end'] = $daily_push_t_mo_end;                
            $dataArr['daily_push']['avg'] = $daily_push_avg;


            $dataArr['first_push']['dates'] = $first_push_arr;
            $dataArr['first_push']['total'] = 0;                
            $dataArr['first_push']['t_mo_end'] = 0;                
            $dataArr['first_push']['avg'] = 0;

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

            $dataArr['mt_success']['dates'] = $mt_success_arr;
            $dataArr['mt_success']['total'] = 0;                
            $dataArr['mt_success']['t_mo_end'] = 0;                
            $dataArr['mt_success']['avg'] = 0;

            $dataArr['mt_failed']['dates'] = $mt_failed_arr;
            $dataArr['mt_failed']['total'] = 0;                
            $dataArr['mt_failed']['t_mo_end'] = 0;                
            $dataArr['mt_failed']['avg'] = 0;

            $dataArr['fmt_success']['dates'] = $fmt_success_arr;
            $dataArr['fmt_success']['total'] = 0;                
            $dataArr['fmt_success']['t_mo_end'] = 0;                
            $dataArr['fmt_success']['avg'] = 0;

            $dataArr['fmt_failed']['dates'] = $fmt_failed_arr;
            $dataArr['fmt_failed']['total'] = 0;                
            $dataArr['fmt_failed']['t_mo_end'] = 0;                
            $dataArr['fmt_failed']['avg'] = 0;

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

            $dataArr['last_update']= $last_update;
           

            

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
            $last_update = '';

            foreach ($sumemry as $sumemry_key => $sumemry_value)
            {
                // dd($sumemry_value);
                if(isset($sumemry_value['last_update']))
                $last_update = $sumemry_value['last_update'];

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


                foreach ($sumemry_value['end_user_rev_usd']['dates'] as $end_user_rev_usd_key => $end_user_rev_usd_value) {
                    if($sumemry_key==0){
                        $end_user_rev_usd_sum[$end_user_rev_usd_key]=0;
                    }
                    // $end_user_rev_usd_arr[$end_user_rev_usd_key] = ['value' => (float)($end_user_rev_usd_sum[$end_user_rev_usd_key]+(float)$end_user_rev_usd_value['value']) , 'class' => $end_user_rev_usd_value['class']];

                    $end_user_rev_usd_sum[$end_user_rev_usd_key] = $end_user_rev_usd_sum[$end_user_rev_usd_key] + (float)$end_user_rev_usd_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $end_user_rev_usd_arr[$end_user_rev_usd_key] = ['value' => $end_user_rev_usd_sum[$end_user_rev_usd_key], 'class' => $end_user_rev_usd_value['class']];
                    }

                }

                foreach ($sumemry_value['end_user_rev']['dates'] as $end_user_rev_key => $end_user_rev_value) {
                    if($sumemry_key==0){
                        $end_user_rev_sum[$end_user_rev_key]=0;
                    }
                    // $end_user_rev_arr[$end_user_rev_key] = ['value' => (float)($end_user_rev_sum[$end_user_rev_key]+(float)$end_user_rev_value['value']) , 'class' => $end_user_rev_value['class']];

                    $end_user_rev_sum[$end_user_rev_key] = $end_user_rev_sum[$end_user_rev_key] + (float)$end_user_rev_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $end_user_rev_arr[$end_user_rev_key] = ['value' => $end_user_rev_sum[$end_user_rev_key], 'class' => $end_user_rev_value['class']];
                    }
                }

                foreach ($sumemry_value['gros_rev_usd']['dates'] as $gros_rev_usd_key => $gros_rev_usd_value) {
                    if($sumemry_key==0){
                        $gros_rev_usd_sum[$gros_rev_usd_key]=0;
                    }
                    // $gros_rev_usd_arr[$gros_rev_usd_key] = ['value' => (float)($gros_rev_usd_sum[$gros_rev_usd_key]+(float)$gros_rev_usd_value['value']) , 'class' => $gros_rev_usd_value['class']];

                    $gros_rev_usd_sum[$gros_rev_usd_key] = $gros_rev_usd_sum[$gros_rev_usd_key] + (float)$gros_rev_usd_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $gros_rev_usd_arr[$gros_rev_usd_key] = ['value' => $gros_rev_usd_sum[$gros_rev_usd_key], 'class' => $gros_rev_usd_value['class']];
                    }
                }

                foreach ($sumemry_value['gros_rev']['dates'] as $gros_rev_key => $gros_rev_value) {
                    if($sumemry_key==0){
                        $gros_rev_sum[$gros_rev_key]=0;
                    }
                    //$gros_rev_arr[$gros_rev_key] = ['value' => (float)($gros_rev_sum[$gros_rev_key]+(float)$gros_rev_value['value']) , 'class' => $gros_rev_value['class']];

                    $gros_rev_sum[$gros_rev_key] = $gros_rev_sum[$gros_rev_key] + (float)$gros_rev_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $gros_rev_arr[$gros_rev_key] = ['value' => $gros_rev_sum[$gros_rev_key], 'class' => $gros_rev_value['class']];
                    }
                }

                foreach ($sumemry_value['net_rev']['dates'] as $net_rev_key => $net_rev_value) {
                    if($sumemry_key==0){
                        $net_rev_sum[$net_rev_key]=0;
                    }

                    $net_rev_sum[$net_rev_key] = $net_rev_sum[$net_rev_key] + (float)$net_rev_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $net_rev_arr[$net_rev_key] = ['value' => $net_rev_sum[$net_rev_key], 'class' => $net_rev_value['class']];
                    }
                }

                foreach ($sumemry_value['cost_campaign']['dates'] as $cost_campaign_key => $cost_campaign_value) {
                    if($sumemry_key==0){
                        $cost_campaign_sum[$cost_campaign_key]=0;
                    }
                    //$cost_campaign_arr[$cost_campaign_key] = ['value' => (float)($cost_campaign_sum[$cost_campaign_key]+(float)$cost_campaign_value['value']) , 'class' => $cost_campaign_value['class']];

                    $cost_campaign_sum[$cost_campaign_key] = $cost_campaign_sum[$cost_campaign_key] + (float)$cost_campaign_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $cost_campaign_arr[$cost_campaign_key] = ['value' => $cost_campaign_sum[$cost_campaign_key], 'class' => $cost_campaign_value['class']];
                    }
                }

                foreach ($sumemry_value['other_cost']['dates'] as $other_cost_key => $other_cost_value) {
                    if($sumemry_key==0){
                        $other_cost_sum[$other_cost_key]=0;
                    }
                    // $other_cost_arr[$other_cost_key] = ['value' => (float)($other_cost_sum[$other_cost_key]+(float)$other_cost_value['value']) , 'class' => $other_cost_value['class']];

                    $other_cost_sum[$other_cost_key] = $other_cost_sum[$other_cost_key] + (float)$other_cost_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $other_cost_arr[$other_cost_key] = ['value' => $other_cost_sum[$other_cost_key], 'class' => $other_cost_value['class']];
                    }
                }

                foreach ($sumemry_value['hosting_cost']['dates'] as $hosting_cost_key => $hosting_cost_value) {
                    if($sumemry_key==0){
                        $hosting_cost_sum[$hosting_cost_key]=0;
                    }
                    // $hosting_cost_arr[$hosting_cost_key] = ['value' => (float)($hosting_cost_sum[$hosting_cost_key]+(float)$hosting_cost_value['value']) , 'class' => $hosting_cost_value['class']];

                    $hosting_cost_sum[$hosting_cost_key] = $hosting_cost_sum[$hosting_cost_key] + (float)$hosting_cost_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $hosting_cost_arr[$hosting_cost_key] = ['value' => $hosting_cost_sum[$hosting_cost_key], 'class' => $hosting_cost_value['class']];
                    }
                }

                foreach ($sumemry_value['content']['dates'] as $content_key => $content_value) {
                    if($sumemry_key==0){
                        $content_sum[$content_key]=0;
                    }
                    // $content_arr[$content_key] = ['value' => (float)($content_sum[$content_key]+(float)$content_value['value']) , 'class' => $content_value['class']];

                    $content_sum[$content_key] = $content_sum[$content_key] + (float)$content_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $content_arr[$content_key] = ['value' => $content_sum[$content_key], 'class' => $content_value['class']];
                    }
                }

                foreach ($sumemry_value['rnd']['dates'] as $rnd_key => $rnd_value) {
                    if($sumemry_key==0){
                        $rnd_sum[$rnd_key]=0;
                    }
                    //$rnd_arr[$rnd_key] = ['value' => (float)($rnd_sum[$rnd_key]+(float)$rnd_value['value']) , 'class' => $rnd_value['class']];

                    $rnd_sum[$rnd_key] = $rnd_sum[$rnd_key] + (float)$rnd_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $rnd_arr[$rnd_key] = ['value' => $rnd_sum[$rnd_key], 'class' => $rnd_value['class']];
                    }
                }

                foreach ($sumemry_value['bd']['dates'] as $bd_key => $bd_value) {
                    if($sumemry_key==0){
                        $bd_sum[$bd_key]=0;
                    }
                    // $bd_arr[$bd_key] = ['value' => (float)($bd_sum[$bd_key]+(float)$bd_value['value']) , 'class' => $bd_value['class']];

                    $bd_sum[$bd_key] = $bd_sum[$bd_key] + (float)$bd_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $bd_arr[$bd_key] = ['value' => $bd_sum[$bd_key], 'class' => $bd_value['class']];
                    }
                }

                foreach ($sumemry_value['market_cost']['dates'] as $market_cost_key => $market_cost_value) {
                    if($sumemry_key==0){
                        $market_cost_sum[$market_cost_key]=0;
                    }
                    // $bd_arr[$bd_key] = ['value' => (float)($bd_sum[$bd_key]+(float)$bd_value['value']) , 'class' => $bd_value['class']];

                    $market_cost_sum[$market_cost_key] = $market_cost_sum[$market_cost_key] + (float)$market_cost_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $market_cost_arr[$market_cost_key] = ['value' => $market_cost_sum[$market_cost_key], 'class' => $market_cost_value['class']];
                    }
                }

                foreach ($sumemry_value['platform']['dates'] as $platform_key => $platform_value) {
                    if($sumemry_key==0){
                        $platform_sum[$platform_key]=0;
                    }
                    //$platform_arr[$platform_key] = ['value' => (float)($platform_sum[$platform_key]+(float)$platform_value['value']) , 'class' => $platform_value['class']];

                    $platform_sum[$platform_key] = $platform_sum[$platform_key] + (float)$platform_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $platform_arr[$platform_key] = ['value' => $platform_sum[$platform_key], 'class' => $platform_value['class']];
                    }
                }

                foreach ($sumemry_value['other_tax']['dates'] as $other_tax_key => $other_tax_value) {
                    if($sumemry_key==0){
                        $other_tax_sum[$other_tax_key]=0;
                    }
                    // $other_cost_arr[$other_cost_key] = ['value' => (float)($other_cost_sum[$other_cost_key]+(float)$other_cost_value['value']) , 'class' => $other_cost_value['class']];

                    $other_tax_sum[$other_tax_key] = $other_tax_sum[$other_tax_key] + (float)$other_tax_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $other_tax_arr[$other_tax_key] = ['value' => $other_tax_sum[$other_tax_key], 'class' => $other_tax_value['class']];
                    }
                }

                foreach ($sumemry_value['vat']['dates'] as $vat_key => $vat_value) {
                    if($sumemry_key==0){
                        $vat_sum[$vat_key]=0;
                    }
                    // $hosting_cost_arr[$hosting_cost_key] = ['value' => (float)($hosting_cost_sum[$hosting_cost_key]+(float)$hosting_cost_value['value']) , 'class' => $hosting_cost_value['class']];

                    $vat_sum[$vat_key] = $vat_sum[$vat_key] + (float)$vat_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $vat_arr[$vat_key] = ['value' => $vat_sum[$vat_key], 'class' => $vat_value['class']];
                    }
                }

                foreach ($sumemry_value['wht']['dates'] as $wht_key => $wht_value) {
                    if($sumemry_key==0){
                        $wht_sum[$wht_key]=0;
                    }
                    // $content_arr[$content_key] = ['value' => (float)($content_sum[$content_key]+(float)$content_value['value']) , 'class' => $content_value['class']];

                    $wht_sum[$wht_key] = $wht_sum[$wht_key] + (float)$wht_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $wht_arr[$wht_key] = ['value' => $wht_sum[$wht_key], 'class' => $wht_value['class']];
                    }
                }

                foreach ($sumemry_value['misc_tax']['dates'] as $misc_tax_key => $misc_tax_value) {
                    if($sumemry_key==0){
                        $misc_tax_sum[$misc_tax_key]=0;
                    }
                    //$rnd_arr[$rnd_key] = ['value' => (float)($rnd_sum[$rnd_key]+(float)$rnd_value['value']) , 'class' => $rnd_value['class']];

                    $misc_tax_sum[$misc_tax_key] = $misc_tax_sum[$misc_tax_key] + (float)$misc_tax_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $misc_tax_arr[$misc_tax_key] = ['value' => $misc_tax_sum[$misc_tax_key], 'class' => $misc_tax_value['class']];
                    }
                }

                foreach ($sumemry_value['pnl']['dates'] as $pnl_key => $pnl_value) {
                    if($sumemry_key==0){
                        $pnl_sum[$pnl_key]=0;
                    }
                    //$pnl_arr[$pnl_key] = ['value' => (float)($pnl_sum[$pnl_key]+(float)$pnl_value['value']) , 'class' => $pnl_value['class']];

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


    public static function sumOfCountryReconcileData($sumemryData)
    {
        // dd($sumemryData);
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
                    if($sumemry_key==0){
                        $dlr_sum[$dlr_key]=0;
                    }

                    $dlr_sum[$dlr_key] = $dlr_sum[$dlr_key] + (float)$dlr_value['value'];
                    if(count($sumemryData)-1 == $sumemry_key)
                    {
                        $dlr_arr[$dlr_key] = ['value' => $dlr_sum[$dlr_key], 'class' => $dlr_value['class']];
                    }
                }

                foreach ($sumemry['fir']['dates'] as $fir_key => $fir_value)
                {
                    if($sumemry_key==0){
                        $fir_sum[$fir_key]=0;
                    }

                    $fir_sum[$fir_key] = $fir_sum[$fir_key] + (float)$fir_value['value'];

                    if(count($sumemryData)-1 == $sumemry_key)
                    {
                        $fir_arr[$fir_key] = ['value' => $fir_sum[$fir_key], 'class' => $fir_value['class']];
                    }
                }

                foreach ($sumemry['discrepency']['dates'] as $discrepency_key => $discrepency_value) {
                    if($sumemry_key==0){
                        $discrepency_sum[$discrepency_key]=0;
                    }

                    $discrepency_sum[$discrepency_key] = $discrepency_sum[$discrepency_key] + (float)$discrepency_value['value'];

                    if(count($sumemryData)-1 == $sumemry_key)
                    {
                        $discrepency_arr[$discrepency_key] = ['value' => $discrepency_sum[$discrepency_key], 'class' => $discrepency_value['class']];
                    }
                }

                foreach ($sumemry['dlr_after_telco']['dates'] as $dlr_after_telco_key => $dlr_after_telco_value) {
                    if($sumemry_key==0){
                        $dlr_after_telco_sum[$dlr_after_telco_key]=0;
                    }

                    $dlr_after_telco_sum[$dlr_after_telco_key] = $dlr_after_telco_sum[$dlr_after_telco_key] + (float)$dlr_after_telco_value['value'];

                    if(count($sumemryData)-1 == $sumemry_key)
                    {
                        $dlr_after_telco_arr[$dlr_after_telco_key] = ['value' => $dlr_after_telco_sum[$dlr_after_telco_key], 'class' => $dlr_after_telco_value['class']];
                    }
                }

                foreach ($sumemry['fir_after_telco']['dates'] as $fir_after_telco_key => $fir_after_telco_value) {
                    if($sumemry_key==0){
                        $fir_after_telco_sum[$fir_after_telco_key]=0;
                    }

                    $fir_after_telco_sum[$fir_after_telco_key] = $fir_after_telco_sum[$fir_after_telco_key] + (float)$fir_after_telco_value['value'];

                    if(count($sumemryData)-1 == $sumemry_key)
                    {
                        $fir_after_telco_arr[$fir_after_telco_key] = ['value' => $fir_after_telco_sum[$fir_after_telco_key], 'class' => $fir_after_telco_value['class']];
                    }
                }

                foreach ($sumemry['discrepency_after_telco']['dates'] as $discrepency_after_telco_key => $discrepency_after_telco_value) {
                    if($sumemry_key==0){
                        $discrepency_after_telco_sum[$discrepency_after_telco_key]=0;
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
        // dd($sumemry);
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
                // dd($sumemry_value);
                $dlr_total = $dlr_total + (float)$sumemry_value['dlr']['total'];
                $dlr_after_telco_total = $dlr_after_telco_total + (float)$sumemry_value['dlr_after_telco']['total'];

                $fir_total = $fir_total + (float)$sumemry_value['fir']['total'];
                $fir_after_telco_total = $fir_after_telco_total + (float)$sumemry_value['fir_after_telco']['total'];
                
                $discrepency_total = $discrepency_total + (float)$sumemry_value['discrepency']['total'];
                $discrepency_after_telco_total = $discrepency_after_telco_total + (float)$sumemry_value['discrepency_after_telco']['total'];

                foreach ($sumemry_value['dlr']['dates'] as $dlr_key => $dlr_value) {
                    if($sumemry_key==0){
                        $dlr_sum[$dlr_key]=0;
                    }

                    $dlr_sum[$dlr_key] = $dlr_sum[$dlr_key] + (float)$dlr_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $dlr_arr[$dlr_key] = ['value' => $dlr_sum[$dlr_key], 'class' => $dlr_value['class']];
                    }
                }

                foreach ($sumemry_value['fir']['dates'] as $fir_key => $fir_value) {
                    if($sumemry_key==0){
                        $fir_sum[$fir_key]=0;
                    }

                    $fir_sum[$fir_key] = $fir_sum[$fir_key] + (float)$fir_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $fir_arr[$fir_key] = ['value' => $fir_sum[$fir_key], 'class' => $fir_value['class']];
                    }
                }

                foreach ($sumemry_value['discrepency']['dates'] as $discrepency_key => $discrepency_value) {
                    if($sumemry_key==0){
                        $discrepency_sum[$discrepency_key]=0;
                    }

                    $discrepency_sum[$discrepency_key] = $discrepency_sum[$discrepency_key] + (float)$discrepency_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $discrepency_arr[$discrepency_key] = ['value' => $discrepency_sum[$discrepency_key], 'class' => $discrepency_value['class']];
                    }
                }

                foreach ($sumemry_value['dlr_after_telco']['dates'] as $dlr_after_telco_key => $dlr_after_telco_value) {
                    if($sumemry_key==0){
                        $dlr_after_telco_sum[$dlr_after_telco_key]=0;
                    }

                    $dlr_after_telco_sum[$dlr_after_telco_key] = $dlr_after_telco_sum[$dlr_after_telco_key] + (float)$dlr_after_telco_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $dlr_after_telco_arr[$dlr_after_telco_key] = ['value' => $dlr_after_telco_sum[$dlr_after_telco_key], 'class' => $dlr_after_telco_value['class']];
                    }
                }

                foreach ($sumemry_value['fir_after_telco']['dates'] as $fir_after_telco_key => $fir_after_telco_value) {
                    if($sumemry_key==0){
                        $fir_after_telco_sum[$fir_after_telco_key]=0;
                    }

                    $fir_after_telco_sum[$fir_after_telco_key] = $fir_after_telco_sum[$fir_after_telco_key] + (float)$fir_after_telco_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $fir_after_telco_arr[$fir_after_telco_key] = ['value' => $fir_after_telco_sum[$fir_after_telco_key], 'class' => $fir_after_telco_value['class']];
                    }
                }

                foreach ($sumemry_value['discrepency_after_telco']['dates'] as $discrepency_after_telco_key => $discrepency_after_telco_value) {
                    if($sumemry_key==0){
                        $discrepency_after_telco_sum[$discrepency_after_telco_key]=0;
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
        // dd($sumemryData);
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
                    if($sumemry_key==0){
                        $gross_rev_sum[$gross_rev_key]=0;
                    }

                    $gross_rev_sum[$gross_rev_key] = $gross_rev_sum[$gross_rev_key] + (float)$gross_rev_value['value'];
                    if(count($sumemryData)-1 == $sumemry_key)
                    {
                        $gross_rev_arr[$gross_rev_key] = ['value' => $gross_rev_sum[$gross_rev_key], 'class' => $gross_rev_value['class']];
                    }
                }

                foreach ($sumemry['target_rev']['dates'] as $target_rev_key => $target_rev_value)
                {
                    if($sumemry_key==0){
                        $target_rev_sum[$target_rev_key]=0;
                    }

                    $target_rev_sum[$target_rev_key] = $target_rev_sum[$target_rev_key] + (float)$target_rev_value['value'];

                    if(count($sumemryData)-1 == $sumemry_key)
                    {
                        $target_rev_arr[$target_rev_key] = ['value' => $target_rev_sum[$target_rev_key], 'class' => $target_rev_value['class']];
                    }
                }

                foreach ($sumemry['rev_disc']['dates'] as $rev_disc_key => $rev_disc_value) {
                    if($sumemry_key==0){
                        $rev_disc_sum[$rev_disc_key]=0;
                    }

                    $rev_disc_sum[$rev_disc_key] = $rev_disc_sum[$rev_disc_key] + (float)$rev_disc_value['value'];

                    if(count($sumemryData)-1 == $sumemry_key)
                    {
                        $rev_disc_arr[$rev_disc_key] = ['value' => $rev_disc_sum[$rev_disc_key], 'class' => $rev_disc_value['class']];
                    }
                }

                foreach ($sumemry['rev_after_share']['dates'] as $rev_after_share_key => $rev_after_share_value) {
                    if($sumemry_key==0){
                        $rev_after_share_sum[$rev_after_share_key]=0;
                    }

                    $rev_after_share_sum[$rev_after_share_key] = $rev_after_share_sum[$rev_after_share_key] + (float)$rev_after_share_value['value'];

                    if(count($sumemryData)-1 == $sumemry_key)
                    {
                        $rev_after_share_arr[$rev_after_share_key] = ['value' => $rev_after_share_sum[$rev_after_share_key], 'class' => $rev_after_share_value['class']];
                    }
                }

                foreach ($sumemry['target_after_share']['dates'] as $target_after_share_key => $target_after_share_value) {
                    if($sumemry_key==0){
                        $target_after_share_sum[$target_after_share_key]=0;
                    }

                    $target_after_share_sum[$target_after_share_key] = $target_after_share_sum[$target_after_share_key] + (float)$target_after_share_value['value'];

                    if(count($sumemryData)-1 == $sumemry_key)
                    {
                        $target_after_share_arr[$target_after_share_key] = ['value' => $target_after_share_sum[$target_after_share_key], 'class' => $target_after_share_value['class']];
                    }
                }

                foreach ($sumemry['target_rev_disc']['dates'] as $target_rev_disc_key => $target_rev_disc_value) {
                    if($sumemry_key==0){
                        $target_rev_disc_sum[$target_rev_disc_key]=0;
                    }

                    $target_rev_disc_sum[$target_rev_disc_key] = $target_rev_disc_sum[$target_rev_disc_key] + (float)$target_rev_disc_value['value'];

                    if(count($sumemryData)-1 == $sumemry_key)
                    {
                        $target_rev_disc_arr[$target_rev_disc_key] = ['value' => $target_rev_disc_sum[$target_rev_disc_key], 'class' => $target_rev_disc_value['class']];
                    }
                }


                foreach ($sumemry['pnl']['dates'] as $pnl_key => $pnl_value) {
                    if($sumemry_key==0){
                        $pnl_sum[$pnl_key]=0;
                    }

                    $pnl_sum[$pnl_key] = $pnl_sum[$pnl_key] + (float)$pnl_value['value'];

                    if(count($sumemryData)-1 == $sumemry_key)
                    {
                        $pnl_arr[$pnl_key] = ['value' => $pnl_sum[$pnl_key], 'class' => $pnl_value['class']];
                    }
                }

                
                foreach ($sumemry['target_pnl']['dates'] as $target_pnl_key => $target_pnl_value) {
                    if($sumemry_key==0){
                        $target_pnl_sum[$target_pnl_key]=0;
                    }

                    $target_pnl_sum[$target_pnl_key] = $target_pnl_sum[$target_pnl_key] + (float)$target_pnl_value['value'];

                    if(count($sumemryData)-1 == $sumemry_key)
                    {
                        $target_pnl_arr[$target_pnl_key] = ['value' => $target_pnl_sum[$target_pnl_key], 'class' => $target_pnl_value['class']];
                    }
                }


                foreach ($sumemry['pnl_disc']['dates'] as $pnl_disc_key => $pnl_disc_value) {
                    if($sumemry_key==0){
                        $pnl_disc_sum[$pnl_disc_key]=0;
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
        //dd($sumemry);
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
                // dd($sumemry_value);
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
                    if($sumemry_key==0){
                        $gross_rev_sum[$gross_rev_key]=0;
                    }

                    $gross_rev_sum[$gross_rev_key] = $gross_rev_sum[$gross_rev_key] + (float)$gross_rev_value['value'];
                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $gross_rev_arr[$gross_rev_key] = ['value' => $gross_rev_sum[$gross_rev_key], 'class' => $gross_rev_value['class']];
                    }
                }

                foreach ($sumemry_value['target_rev']['dates'] as $target_rev_key => $target_rev_value)
                {
                    if($sumemry_key==0){
                        $target_rev_sum[$target_rev_key]=0;
                    }

                    $target_rev_sum[$target_rev_key] = $target_rev_sum[$target_rev_key] + (float)$target_rev_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $target_rev_arr[$target_rev_key] = ['value' => $target_rev_sum[$target_rev_key], 'class' => $target_rev_value['class']];
                    }
                }

                foreach ($sumemry_value['rev_disc']['dates'] as $rev_disc_key => $rev_disc_value) {
                    if($sumemry_key==0){
                        $rev_disc_sum[$rev_disc_key]=0;
                    }

                    $rev_disc_sum[$rev_disc_key] = $rev_disc_sum[$rev_disc_key] + (float)$rev_disc_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $rev_disc_arr[$rev_disc_key] = ['value' => $rev_disc_sum[$rev_disc_key], 'class' => $rev_disc_value['class']];
                    }
                }

                foreach ($sumemry_value['rev_after_share']['dates'] as $rev_after_share_key => $rev_after_share_value) {
                    if($sumemry_key==0){
                        $rev_after_share_sum[$rev_after_share_key]=0;
                    }

                    $rev_after_share_sum[$rev_after_share_key] = $rev_after_share_sum[$rev_after_share_key] + (float)$rev_after_share_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $rev_after_share_arr[$rev_after_share_key] = ['value' => $rev_after_share_sum[$rev_after_share_key], 'class' => $rev_after_share_value['class']];
                    }
                }

                foreach ($sumemry_value['target_after_share']['dates'] as $target_after_share_key => $target_after_share_value) {
                    if($sumemry_key==0){
                        $target_after_share_sum[$target_after_share_key]=0;
                    }

                    $target_after_share_sum[$target_after_share_key] = $target_after_share_sum[$target_after_share_key] + (float)$target_after_share_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $target_after_share_arr[$target_after_share_key] = ['value' => $target_after_share_sum[$target_after_share_key], 'class' => $target_after_share_value['class']];
                    }
                }

                foreach ($sumemry_value['target_rev_disc']['dates'] as $target_rev_disc_key => $target_rev_disc_value) {
                    if($sumemry_key==0){
                        $target_rev_disc_sum[$target_rev_disc_key]=0;
                    }

                    $target_rev_disc_sum[$target_rev_disc_key] = $target_rev_disc_sum[$target_rev_disc_key] + (float)$target_rev_disc_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $target_rev_disc_arr[$target_rev_disc_key] = ['value' => $target_rev_disc_sum[$target_rev_disc_key], 'class' => $target_rev_disc_value['class']];
                    }
                }


                foreach ($sumemry_value['pnl']['dates'] as $pnl_key => $pnl_value) {
                    if($sumemry_key==0){
                        $pnl_sum[$pnl_key]=0;
                    }

                    $pnl_sum[$pnl_key] = $pnl_sum[$pnl_key] + (float)$pnl_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $pnl_arr[$pnl_key] = ['value' => $pnl_sum[$pnl_key], 'class' => $pnl_value['class']];
                    }
                }

                
                foreach ($sumemry_value['target_pnl']['dates'] as $target_pnl_key => $target_pnl_value) {
                    if($sumemry_key==0){
                        $target_pnl_sum[$target_pnl_key]=0;
                    }

                    $target_pnl_sum[$target_pnl_key] = $target_pnl_sum[$target_pnl_key] + (float)$target_pnl_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $target_pnl_arr[$target_pnl_key] = ['value' => $target_pnl_sum[$target_pnl_key], 'class' => $target_pnl_value['class']];
                    }
                }


                foreach ($sumemry_value['pnl_disc']['dates'] as $pnl_disc_key => $pnl_disc_value) {
                    if($sumemry_key==0){
                        $pnl_disc_sum[$pnl_disc_key]=0;
                    }

                    $pnl_disc_sum[$pnl_disc_key] = $pnl_disc_sum[$pnl_disc_key] + (float)$pnl_disc_value['value'];

                    if(count($sumemry)-1 == $sumemry_key)
                    {
                        $pnl_disc_arr[$pnl_disc_key] = ['value' => $pnl_disc_sum[$pnl_disc_key], 'class' => $pnl_disc_value['class']];
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
        }else{
            return [];
        }
    }

    public static function summeryPercentageCalculation($sumemry_value)
    {
        // dd($sumemry_value);
        $sum = 0;
        $avg = 0;
        $T_Mo_End = 0;
        $reaming_day = 0;
        $days = 0;
        $total_churn_sum = 0;
        $total_ltv_sum = 0;
        $total_churn = 0;
        $bill_days = 0;
        $total_bill_sum = 0;
        $total_subscriber = 0;
        $firstPush_sum = 0;
        $dailyPush_sum = 0;
        $churn_array =array();
        $churn_arr =array();
        $bill_array = array();
        $bill_arr = array();
        $fpush_array =array();
        $dpush_array =array();
        $arpu7_arr =array();
        $ltv_array =array();
        $ltv_arr =array();

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

                
                // if($total_subscriber != 0)
                // $total_churn = (($total_reg - $total_unreg + $total_purged) / $total_subscriber);

                if($total_reg != 0)
                $total_churn = ( $total_unreg / $total_reg);


                if($today != $churn_key)
                {
                    $total_churn_sum = $total_churn_sum + $total_churn;
                    $days++;
                }

                $churn_arr[$churn_key]['value'] = $total_churn * 100;
                $churn_arr[$churn_key]['class'] = "";
            }
        }
        
        $churn_avg = ($total_churn_sum / $days ) * 100;
        $churn_array['dates']= $churn_arr;
        $churn_array['total']= 0;
        $churn_array['t_mo_end'] = 0;
        $churn_array['avg']= $churn_avg;

        $sumemry_value['churn'] = $churn_array;


        /*----Bill Rate----*/

        if(!empty($sumemry_value['bill']['dates'])) 
        {
            foreach ($sumemry_value['bill']['dates'] as $bill_key => $bill_value) 
            {

                if(!isset($bill_arr[$bill_key]['value']))
                {
                    $bill_arr[$bill_key]['value']= 0;
                    $first_push[$bill_key]['value']= 0;
                }
                        
                $total_mt_success = $sumemry_value['mt_success']['dates'][$bill_key]['value'];
                $total_mt_failed = $sumemry_value['mt_failed']['dates'][$bill_key]['value'];
                $total_fmt_success = $sumemry_value['fmt_success']['dates'][$bill_key]['value'];
                $total_fmt_failed = $sumemry_value['fmt_failed']['dates'][$bill_key]['value'];

                $total_subscriber = $sumemry_value['t_sub']['dates'][$bill_key]['value'];

                $total_bill_sum_tmp =  UtilityPercentage::billRateWithoutPer($total_mt_success,$total_mt_failed,$total_subscriber);

                $total_first_push_sum_tmp =  UtilityPercentage::FirstPushWithoutPer($total_fmt_success,$total_fmt_failed,$total_subscriber);

                $total_daily_push_sum_tmp =  UtilityPercentage::DailypushWithoutPer($total_mt_success,$total_mt_failed,$total_subscriber);


                if($today != $bill_key)
                {
                    $total_bill_sum = $total_bill_sum +$total_bill_sum_tmp;
                    $firstPush_sum = $firstPush_sum +$total_first_push_sum_tmp;
                    $dailyPush_sum = $dailyPush_sum + $total_daily_push_sum_tmp;
                    $bill_days++;
                }

                $bill_arr[$bill_key]['value'] =$total_bill_sum_tmp * 100 ;
                $first_push[$bill_key]['value']=$total_first_push_sum_tmp * 100;
                $daily_push[$bill_key]['value']=$total_daily_push_sum_tmp * 100;

                $bill_arr[$bill_key]['class'] ="";
                $first_push[$bill_key]['class']="";
                $daily_push[$bill_key]['class']="";
            }
        }


        $bill_avg = ($total_bill_sum / $bill_days ) * 100;
        $bill_array['dates']= $bill_arr;
        $bill_array['total']= 0;
        $bill_array['t_mo_end'] = 0;
        $bill_array['avg']= $bill_avg;

        $sumemry_value['bill'] = $bill_array;

        /* First Push Value */

        $fpush_array_avg = ($firstPush_sum / $bill_days ) * 100;
        $fpush_array['dates'] = $first_push;
        $fpush_array['total'] = 0;
        $fpush_array['t_mo_end'] = 0;
        $fpush_array['avg']= $fpush_array_avg;

        $sumemry_value['first_push'] = $fpush_array;

        /* Daily Push Value */

        $dpush_array_avg = ($dailyPush_sum / $bill_days ) * 100;
        $dpush_array['dates'] = $daily_push;
        $dpush_array['total'] = 0;
        $dpush_array['t_mo_end'] = 0;
        $dpush_array['avg']= $dpush_array_avg;

        $sumemry_value['daily_push'] = $dpush_array;

        /* arpu 7 calculation */

        $total_R1_sum = 0;
        $total_R3_sum = 0;
        $arpu7_days = 0;
        $arpu7_avg = 0;

        if(!empty($sumemry_value['arpu7raw']['dates'])) 
        {
            //dd($sumemry_value['arpu7raw']['dates']);
            foreach ($sumemry_value['arpu7raw']['dates'] as $arpu7raw_key => $arpu7raw_value) 
            {
                if(!isset($arpu7_arr[$arpu7raw_key]['value']))
                {
                    $arpu7_arr[$arpu7raw_key]['value']=0;
                }

                // dd($arpu7raw_value['value']);

                $R1 = $arpu7raw_value['value']['total_gross_rev'];
                $R3 = $arpu7raw_value['value']['total_total_reg'];
                       

                // $R2 = $total_total_reg;
                // $R3 = $R2 + $total_subscriber ;
                $arpu7 =0;
                           
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
        // $R2 = $total_total_reg;
        // $R3 = $R2 + $total_subscriber ;
                       
        if( $R3_avg > 0)
        {
            $arpu7_avg = $R1_avg / $R3_avg ;
        }
        // dd($arpu7_arr);
        $arpu7_array['dates']=$arpu7_arr;
        $arpu7_array['total']=0;
        $arpu7_array['t_mo_end']=0;
        $arpu7_array['avg']=$arpu7_avg;

        $sumemry_value['arpu7'] = $arpu7_array;

        /* ltv */

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

    public static function allSummeryPercentageCalculation($sumemry_value)
    {
        // dd($sumemry_value);
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
        $churn_array =array();
        $churn_arr =array();
        $bill_array = array();
        $bill_arr = array();
        $fpush_array =array();
        $dpush_array =array();
        $arpu7_arr =array();
        $arpu30_arr =array();
        $arpu30_array =array();
        $ltv_array =array();
        $ltv_arr = array();

        $today = Carbon::now()->format('Y-m');

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

                
                // if($total_subscriber != 0)
                // $total_churn = (($total_reg - $total_unreg + $total_purged) / $total_subscriber);

                if($total_subscriber != 0)
                $total_churn = ( $total_unreg / $total_subscriber);


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
        $churn_array['dates']= $churn_arr;
        $churn_array['total']= 0;
        $churn_array['t_mo_end'] = 0;
        $churn_array['avg']= $churn_avg;

        $sumemry_value['churn'] = $churn_array;


        /*----Bill Rate----*/

        if(!empty($sumemry_value['bill']['dates'])) 
        {
            foreach ($sumemry_value['bill']['dates'] as $bill_key => $bill_value) 
            {

                if(!isset($bill_arr[$bill_key]['value']))
                {
                    $bill_arr[$bill_key]['value']= 0;
                    $first_push[$bill_key]['value']= 0;
                }
                        
                $total_mt_success = $sumemry_value['mt_success']['dates'][$bill_key]['value'];
                $total_mt_failed = $sumemry_value['mt_failed']['dates'][$bill_key]['value'];
                $total_fmt_success = $sumemry_value['fmt_success']['dates'][$bill_key]['value'];
                $total_fmt_failed = $sumemry_value['fmt_failed']['dates'][$bill_key]['value'];

                $total_subscriber = $sumemry_value['t_sub']['dates'][$bill_key]['value'];

                $total_bill_sum_tmp =  UtilityPercentage::billRateWithoutPer($total_mt_success,$total_mt_failed,$total_subscriber);

                $total_first_push_sum_tmp =  UtilityPercentage::FirstPushWithoutPer($total_fmt_success,$total_fmt_failed,$total_subscriber);

                $total_daily_push_sum_tmp =  UtilityPercentage::DailypushWithoutPer($total_mt_success,$total_mt_failed,$total_subscriber);


                if($today != $bill_key)
                {
                    $total_bill_sum = $total_bill_sum +$total_bill_sum_tmp;
                    $firstPush_sum = $firstPush_sum +$total_first_push_sum_tmp;
                    $dailyPush_sum = $dailyPush_sum + $total_daily_push_sum_tmp;
                    $bill_days++;
                }

                $bill_arr[$bill_key]['value'] =$total_bill_sum_tmp * 100 ;
                $first_push[$bill_key]['value']=$total_first_push_sum_tmp * 100;
                $daily_push[$bill_key]['value']=$total_daily_push_sum_tmp * 100;

                $bill_arr[$bill_key]['class'] ="";
                $first_push[$bill_key]['class']="";
                $daily_push[$bill_key]['class']="";
            }
        }


        $bill_avg = ($bill_days != 0) ? ($total_bill_sum / $bill_days ) * 100 : 0;
        $bill_array['dates']= $bill_arr;
        $bill_array['total']= 0;
        $bill_array['t_mo_end'] = 0;
        $bill_array['avg']= $bill_avg;

        $sumemry_value['bill'] = $bill_array;

        /* First Push Value */

        $fpush_array_avg = ($bill_days != 0) ? ($firstPush_sum / $bill_days ) * 100 : 0;
        $fpush_array['dates'] = $first_push;
        $fpush_array['total'] = 0;
        $fpush_array['t_mo_end'] = 0;
        $fpush_array['avg']= $fpush_array_avg;

        $sumemry_value['first_push'] = $fpush_array;

        /* Daily Push Value */

        $dpush_array_avg = ($bill_days != 0) ? ($dailyPush_sum / $bill_days ) * 100 : 0;
        $dpush_array['dates'] = $daily_push;
        $dpush_array['total'] = 0;
        $dpush_array['t_mo_end'] = 0;
        $dpush_array['avg']= $dpush_array_avg;

        $sumemry_value['daily_push'] = $dpush_array;

        /* arpu 7 calculation */

        $total_R1_sum = 0;
        $total_R3_sum = 0;
        $arpu7_days = 0;
        $arpu7_avg = 0;

        if(!empty($sumemry_value['arpu7raw']['dates'])) 
        {
            //dd($sumemry_value['arpu7raw']['dates']);
            foreach ($sumemry_value['arpu7raw']['dates'] as $arpu7raw_key => $arpu7raw_value) 
            {
                if(!isset($arpu7_arr[$arpu7raw_key]['value']))
                {
                    $arpu7_arr[$arpu7raw_key]['value']=0;
                }

                // dd($arpu7raw_value['value']);

                $R1 = $arpu7raw_value['value']['total_gross_revusd'];
                $R3 = $arpu7raw_value['value']['total_total_reg'];
                       

                // $R2 = $total_total_reg;
                // $R3 = $R2 + $total_subscriber ;
                $arpu7 =0;
                           
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
        // $R2 = $total_total_reg;
        // $R3 = $R2 + $total_subscriber ;
                       
        if( $R3_avg > 0)
        {
            $arpu7_avg = $R1_avg / $R3_avg ;
        }
        // dd($arpu7_arr);
        $arpu7_array['dates']=$arpu7_arr;
        $arpu7_array['total']=0;
        $arpu7_array['t_mo_end']=0;
        $arpu7_array['avg']=$arpu7_avg;

        $sumemry_value['usarpu7'] = $arpu7_array;


        /* arpu 30 calculation */

        $total_R1_sum_arpu30 = 0;
        $total_R3_sum_arpu30 = 0;
        $arpu30_days = 0;
        $arpu30_avg = 0;

        if(!empty($sumemry_value['arpu30raw']['dates'])) 
        {
            //dd($sumemry_value['arpu30raw']['dates']);
            foreach ($sumemry_value['arpu30raw']['dates'] as $arpu30raw_key => $arpu30raw_value) 
            {
                if(!isset($arpu30_arr[$arpu30raw_key]['value']))
                {
                    $arpu30_arr[$arpu30raw_key]['value']=0;
                }

                // dd($arpu30raw_value['value']);

                $arpu30_R1 = $arpu30raw_value['value']['total_gross_revusd'];
                $arpu30_R3 = $arpu30raw_value['value']['total_total_reg'];
                       

                // $R2 = $total_total_reg;
                // $arpu30_R3 = $R2 + $total_subscriber ;
                $arpu30 =0;
                           
                if($arpu30_R3 > 0)
                {
                    $arpu30 = $arpu30_R1 / $arpu30_R3 ;
                }
                
                if($today != $arpu30raw_key)
                {
                    $total_R1_sum_arpu30 =$total_R1_sum_arpu30 + $arpu30_R1 ;
                    $total_R3_sum_arpu30 =$total_R3_sum_arpu30 + $arpu30_R3 ;
                    $arpu30_days++;
                }

                $arpu30_arr[$arpu30raw_key]['value'] = $arpu30;
                $arpu30_arr[$arpu30raw_key]['class'] = "";
            }
        }

        $arpu30_R1_avg = $total_R1_sum_arpu30;
        $arpu30_R3_avg = $total_R3_sum_arpu30;
        // $R2 = $total_total_reg;
        // $arpu30_R3 = $R2 + $total_subscriber ;
                       
        if( $arpu30_R3_avg > 0)
        {
            $arpu30_avg = $arpu30_R1_avg / $arpu30_R3_avg ;
        }
        // dd($arpu30_arr);
        $arpu30_array['dates']=$arpu30_arr;
        $arpu30_array['total']=0;
        $arpu30_array['t_mo_end']=0;
        $arpu30_array['avg']=$arpu30_avg;

        $sumemry_value['usarpu30'] = $arpu30_array;

        /* ltv */

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
    
}
