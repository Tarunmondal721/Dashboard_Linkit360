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

class UtilityAnalytic
{
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
            $reaming_day = Carbon::parse($end_date)->daysInMonth;
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

            if($count > 0 && $sum > 0)
            {
                $avg = $sum/$count;
            }

            //Total + average * remaining days

            if($count > 0)
            {
                $T_Mo_End = $avg * $reaming_day;
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


    public static function getDetailRevenueByService($service_id, $current_date){
        $data = [];
        $result = [];
        $zero_based_services = [1021,1024,1095];
        $Url_service_mt_ = config('thirdpartyapi.api_url.gross_revenue');
        $request =UtilityAnalytic::GetResponseFromUrl($Url_service_mt_. $service_id . '|' . $current_date . '|' . $current_date);

        if(isset($request) and !empty($request)){
            foreach ($request as $rkey => $rvalue) {
                if($rvalue->date == $current_date){
                    $data['date'] = $rvalue->date;
                    $data['id_service'] = $rvalue->id_service;
                    $data['gross_revenue'] = (in_array($rvalue->id_service, $zero_based_services)) ? 0 : $rvalue->gross_revenue;
                    $result[] = $data;
                }
            }
            return $result;
        }
        return false;
    }

    
    public  static function GetResponseFromUrl($url)
    {
        $client = new GuzzleClient();
        $responseData = array();

        try {
            $response = $client->request('GET', $url,);
            $responseData = json_decode($response->getBody(), FALSE);
        }catch (ClientException $e) {
            $response = $e->getResponse();
            //$responseData = $response->getBody()->getContents();
            $message = $url ."has Exception";
            Log($message);
        }

        return $responseData;
    }
}
