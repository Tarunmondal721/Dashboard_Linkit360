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

class UtilityPercentage
{
    public static function PercentageDataAVG($operator,$data,$start_date,$end_date)
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
        
        $dayscount = ($created->diff($end_date));
        $noofDays = $dayscount->days;
        
        // if not select Date range 
        if($created_format == $firstdayOfMonth)
        {
            $reaming_day = Carbon::now()->daysInMonth;
            $reaming_day = $reaming_day-(count($data) - 1);
        }else{
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

            if($count > 0 && $sum > 0)
            {
                $avg = $sum/$count;
            }

            if($count > 0)
            {
                $T_Mo_End = $sum+ ($avg * $reaming_day);
            }       
        }
        
        $result['sum'] = $sum;
        $result['avg'] = $avg;
        $result['T_Mo_End'] = $T_Mo_End;
        
        return $result;     
    }

    public static function billRateWithoutPer($mt_success,$mt_failed,$total_subscriber)
    {
        $billing_rate = 0;
        $sent = $mt_success + $mt_failed;

        if($sent == 0)
        {
            if($total_subscriber > 0)
            {
                $billing_rate = ($mt_success/$total_subscriber);    
            }
        }else if($mt_failed == 0){
            if($total_subscriber > 0)
            {
                $billing_rate = ($mt_success/$total_subscriber);
            }
        }else{
            if($total_subscriber > 0)
            {
              $billing_rate = ($mt_success/$total_subscriber);
            }
            else
            {
              $billing_rate = ($mt_success/$sent);
            }
        }

        return $billing_rate;      
    }

    public static function DailypushWithoutPer($mt_success,$mt_failed,$total_subscriber)
    { 
        $Dailypush_rate = 0;
        $sent = $mt_success + $mt_failed;

        if($sent == 0)
        {
            if($total_subscriber > 0)
            {
                $Dailypush_rate = ($mt_success/$total_subscriber);   
            }
        }else if($mt_failed == 0)
        {
            if($total_subscriber > 0)
            {
                $Dailypush_rate = ($mt_success/$total_subscriber);
            }
        }else{
            $Dailypush_rate = ($mt_success/$sent);
        }

        return $Dailypush_rate;    
    }

    public static function FirstPushWithoutPer($fmt_success,$fmt_failed,$total_subscriber)
    {
        $firstPushRate = 0;
        $sent = $fmt_success + $fmt_failed;
       
        if($sent == 0)
        {
            if($total_subscriber > 0)
            {
                $firstPushRate = ($fmt_success/$total_subscriber);        
            }
        }else if($fmt_failed == 0)
        {
            if($total_subscriber > 0)
            {
                $firstPushRate = ($fmt_success/$total_subscriber);
            }
        }else{
            $firstPushRate = ($fmt_success/$sent);
        }

        return $firstPushRate;  
    }

    public static function Arpu7Raw($operator,$reportsByIDs,$days,$total_subscriber,$share,$OperatorCountry)
    {
        $arpu = 0;
        $data = array();

        $usdValue = isset($OperatorCountry['usd']) ? $OperatorCountry['usd'] : 1;
        $day = $days['date'];
        $merchent_share = $share['merchent_share'] / 100;
        $operator_share = $share['operator_share'] / 100;
        
        $day_date = new Carbon($day);
        $last_date_cal =  new Carbon($day);

        $first_day = $day_date->subDays(7)->format('Y-m-d');
        $last_day = $last_date_cal->subDays(1)->format('Y-m-d');
        $datesIndividual =Utility::getRangeDates($first_day,$last_day);
        $no_of_days = Utility::getRangeDateNo($datesIndividual);

        $id_operator = $operator->id_operator;
        $total_gros_rev = 0; // 7 days total revenue 
        $total_total_reg = 0;  // 7 days total reg 
        $total_gros_usd = 0; // 7 days total revenue

        if(!empty($no_of_days))
        {
            foreach($no_of_days as $days)
            {
                $keys = $id_operator.".".$days['date'];
                $summariserow = Arr::get($reportsByIDs, $keys, 0);

                $gros_rev = isset($summariserow['gros_rev']) ? $summariserow['gros_rev'] : 0;
                $gros_rev_Usd = $gros_rev * $usdValue;
                
                $total_gros_usd = $total_gros_usd + $gros_rev_Usd;

                $total_gros_rev =  $total_gros_rev +  $gros_rev;
                $total_reg = isset($summariserow['total_reg']) ? $summariserow['total_reg'] : 0;
                $total_total_reg  = $total_total_reg + $total_reg;
            }

            $R1 = $total_gros_rev * $merchent_share; // change with merchent_share
            $R1_usd = $total_gros_usd * $merchent_share; // change with merchent_share
            $R2 = $total_total_reg;
            $R3 = $R2 + $total_subscriber ;

            $data['total_gross_rev'] = $R1;
            $data['total_gross_revusd'] = $R1_usd;
            $data['total_total_reg'] = $R3;

            return $data;
        }

        return $data;
    }

    public static function Arpu30Raw($operator,$reportsByIDs,$days,$total_subscriber,$share,$OperatorCountry)
    {
        $arpu = 0;
        $data = array();

        $usdValue = isset($OperatorCountry['usd']) ? $OperatorCountry['usd'] : 1;

        $id_operator = $operator->id_operator;
        $day = $days['date'];
        $merchent_share = $share['merchent_share'] / 100;
        $operator_share = $share['operator_share'] / 100;
        
        $day_date = new Carbon($day);
        $last_date_cal =  new Carbon($day);

        $first_day = $day_date->subDays(30)->format('Y-m-d');
        $last_day = $last_date_cal->subDays(1)->format('Y-m-d');
        $datesIndividual = Utility::getRangeDates($first_day,$last_day);
        $no_of_days = Utility::getRangeDateNo($datesIndividual);

        $total_gros_rev = 0;
        $total_total_reg = 0;
        $total_gros_usd = 0;

        if(!empty($no_of_days))
        {
            foreach($no_of_days as $days)
            {
                $keys = $id_operator.".".$days['date'];
                $summariserow = Arr::get($reportsByIDs, $keys, 0);

                $gros_rev = isset($summariserow['gros_rev']) ? $summariserow['gros_rev'] : 0;
                $gros_rev_Usd = $gros_rev * $usdValue;

                $total_gros_usd = $total_gros_usd + $gros_rev_Usd;
                $total_gros_rev =  $total_gros_rev +  $gros_rev;
                $total_reg = isset($summariserow['total_reg']) ? $summariserow['total_reg'] : 0;
                $total_total_reg  = $total_total_reg + $total_reg;
            }

            $R1 = $total_gros_rev * $merchent_share; // change with merchent_share
            $R1_usd = $total_gros_usd * $merchent_share; // change with merchent_share
            $R2 = $total_total_reg;
            $R3 = $R2 + $total_subscriber ;

            $data['total_gross_rev'] = $R1;
            $data['total_gross_revusd'] = $R1_usd;
            $data['total_total_reg'] = $R3;
        }

        return $data;
    }

    public static function Arpu30RawMonth($operator,$reportsByIDs,$days,$total_subscriber,$share,$OperatorCountry,$service_historys)
    {
        $arpu = 0;
        $data = array();

        $usdValue = isset($OperatorCountry['usd']) ? $OperatorCountry['usd'] : 1;

        $day = $days['date'];
        $merchent_share = $share['merchent_share'] / 100;
        $operator_share = $share['operator_share'] / 100;
        
        $day_date = new Carbon($day);
        $last_date_cal =  new Carbon($day);

        $start_date = Carbon::now()->startOfYear()->format('Y-m-d');
        $end_date = Carbon::now()->format('Y-m-d');
        $month = Carbon::now()->format('F Y');
        $datesIndividual = Utility::getRangeDates($start_date,$end_date);
        $no_of_days = Utility::getRangeMonthsNo($datesIndividual);

        $id_operator = $operator->id_operator;
        $total_gros_rev = 0;
        $total_total_reg = 0;
        $total_gros_usd = 0;

        if(!empty($no_of_days))
        {
            foreach($no_of_days as $days)
            {
                $keys = $id_operator.".".$days['date'];
                $summariserow = Arr::get($reportsByIDs, $keys, 0);

                $gros_rev = isset($summariserow['gros_rev']) ? $summariserow['gros_rev'] : 0;

                if($summariserow == 0)
                $gros_rev_Usd = 0;
                else
                $gros_rev_Usd = UtilityReports::UsdCalCriteria($gros_rev,$usdValue,$summariserow,$OperatorCountry,$days);
                
                $total_gros_usd = $total_gros_usd + $gros_rev_Usd;
                $total_gros_rev = $total_gros_rev +  $gros_rev;
                $total_reg = isset($summariserow['total_reg']) ? $summariserow['total_reg'] : 0;
                $total_total_reg = $total_total_reg + $total_reg;
            }

            $R1 = $total_gros_rev * $merchent_share; // change with merchent_share
            $R1_usd = $total_gros_usd * $merchent_share; // change with merchent_share
            $R2 = $total_total_reg;
            $R3 = $R2 + $total_subscriber ;

            $data['total_gross_rev'] = $R1;
            $data['total_gross_revusd'] = $R1_usd;
            $data['total_total_reg'] = $R3;
        }

        return $data;
    }
}
