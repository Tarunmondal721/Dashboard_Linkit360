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

class UtilityReportsDetails
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

    public static function calculateTotalAVG($operator,$data,$start_date,$end_date)
    { 
        
        $result = array();
        $sum =0;
        $avg =0;
        $T_Mo_End =0;
        $reaming_day =0;
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
                    if($today > $end_date)
                    $count = count($data);



                    foreach($data as $key => $value)
                    {
                        
                        if($today == $key)
                        continue;

                        $sum = $sum+$value['value'];
                        

                       // dd($value);
                    }
                    
                    if($count>0 && $sum > 0)
                    {
                        $avg = $sum/$count;

                    }

                    ///Total + average * remaining days

                    if($count>0 )
                    {
                        $T_Mo_End = $sum+ ($avg * $reaming_day);
                        if($today > $end_date)
                        $T_Mo_End = $sum;

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


    public static function calculateTotalSubscribe($operator,$data,$start_date,$end_date)
    { 
        
        $result = array();
        $sum =0;
        $avg =0;
        $T_Mo_End =0;
        $reaming_day =0;
        $today = Carbon::now()->format('Y-m-d');
        $calculateDayforSubscription = Carbon::now()->subDays(1)->format('Y-m-d');

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
        $today = Carbon::now()->format('Y-m-d');
        

        /* tur Row Color Set */
        $turRowData = $operator[$rowType]['dates'];
        $datesRebuild =array();

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
    
}
