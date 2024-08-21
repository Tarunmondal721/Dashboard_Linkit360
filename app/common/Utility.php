<?php

namespace App\common;

use App\Models\Configuration;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Promise;
use GuzzleHttp\Psr7;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Exception\ClientException;
use Carbon\Carbon;
use function PHPSTORM_META\type;
use App\Models\Country;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\UserActivity;
use GuzzleHttp\Exception\RequestException;

class Utility
{
    function Log($message)
    {
        Log::build([
            'driver' => 'single',
            'path' => storage_path('logs/' . date("y-m-d") . '-cron.log'),
        ])->notice($message);
    }

    function wakiLog($message)
    {
        Log::build([
            'driver' => 'single',
            'path' => storage_path('logs/' . date("y-m-d") . '-waki-callback.log'),
        ])->notice($message);
    }

    public static function getRangeDates($from, $to)
    {
        // Declare an empty array
        $array = array();

        // Use strtotime function
        $from_str = strtotime($from);
        $to_str = strtotime($to);

        // Use for loop to store dates into array
        // 86400 sec = 24 hrs = 60*60*24 = 1 day
        for ($currentDate = $from_str; $currentDate <= $to_str; $currentDate += (86400)) {
            $Store = date('Y-m-d', $currentDate);
            $array[] = $Store;
        }

        // Display the dates in array format
        return $array;
    }

    public static function getRangeDateNo($dates)
    {
        // Declare an empty array
        $datesarray = array();
        // Loop from the start date to end date and output all dates inbetween
        for ($i = 0; $i < count($dates); $i++) {
            $tempArray = array();
            $date_str = strtotime($dates[$i]);
            $dayno = date('d', $date_str);
            $tempArray['date'] = $dates[$i];
            $tempArray['no'] = $dayno;
            $datesarray[] = $tempArray;
        }

        // Return the array elements
        if (!empty($datesarray))
            $datesarray = collect($datesarray)->reverse()->toArray();

        return $datesarray;
    }

    public static function getRangeMonthsNo($dates)
    {
        // Declare an empty array
        $datesarray = array();
        $monthArray = array();
        // Loop from the start date to end date and output all dates inbetween
        for ($i = 0; $i < count($dates); $i++) {
            $tempArray = array();
            $date_str = strtotime($dates[$i]);
            $monthyears = date('Y-m', $date_str);

            if (!in_array($monthyears, $monthArray)) {
                $dayno = date('M Y', $date_str);
                $month = date('m', $date_str);
                $year = date('Y', $date_str);
                $tempArray['date'] = $monthyears;
                $tempArray['no'] = $dayno;
                $tempArray['month'] = $month;
                $tempArray['year'] = $year;
                $monthArray[] = $monthyears;
                $datesarray[] = $tempArray;
            }
        }

        // Return the array elements
        if (!empty($datesarray))
            $datesarray = collect($datesarray)->sortBy('date')->reverse()->toArray();

        return $datesarray;
    }

    public  static function GetResponse($url)
    {
        $client = new GuzzleClient();
        $responseData = array();

        try {
            $response = $client->request('GET', $url,);
            //response()->json
            $responseData = json_decode($response->getBody(), TRUE);
        } catch (ClientException $e) {
            $response = $e->getResponse();
            //$responseData = $response->getBody()->getContents();
            $message = $url . "has Exception";
            Log($message);
        }

        return $responseData;
    }
    public  static function GetResponseSummary($url, $from, $to)
    {
        $client = new GuzzleClient();
        $responseData = array();
        $timeout = (int)Configuration::where('key', 'timeout_settings')->first()->value;

        try {
            $response = $client->get($url, [
                'auth' => ['middleware', 'l1nk1t360'],
                'timeout' => $timeout, // Response timeout
                'connect_timeout' => $timeout,
                'json' => [
                    'from_date' => $from,
                    "to_date" => $to
                ]
            ]);            //response()->json
            $responseData = json_decode($response->getBody(), TRUE);
        } catch (ClientException $e) {
            $response = $e->getResponse();
            $responseData = $response->getBody()->getContents();
            $responseData = json_decode($responseData, true);
        }catch (RequestException $e) {
            $response = $e->getResponse();
            $responseData = $response->getBody()->getContents();
            $responseData = json_decode($responseData, true);
            
        }

        return $responseData;
    }

    public static function storeIpClient($url, $post_data)
    {
        $client = new GuzzleClient();
        $responseData = array();

        try {
            $response = Http::withBasicAuth('middleware', 'l1nk1t360')->post(
                $url,
                $post_data
            );

            $responseData = json_decode($response, TRUE);
        } catch (\Throwable $th) {
        } catch (ClientException $e) {
        }

        return $responseData;
    }

    public static function updateIpClient($url, $post_data)
    {
        $client = new GuzzleClient();
        $responseData = array();

        try {
            $response = Http::withBasicAuth('middleware', 'l1nk1t360')->patch(
                $url,
                $post_data
            );
            $responseData = json_decode($response, TRUE);
        } catch (\Throwable $th) {
        } catch (ClientException $e) {
        }

        return $responseData;
    }

    public static function insertCsActivity($url, $post_data)
    {
        $client = new GuzzleClient();
        $responseData = array();
        try {
            $response = Http::withBasicAuth('middleware', 'l1nk1t360')->post(
                $url,
                $post_data
            );
            $responseData = json_decode($response, TRUE);
        } catch (\Throwable $th) {
        } catch (ClientException $e) {
        }

        return $responseData;
    }

    public static function updateCsActivity($url, $post_data)
    {
        $client = new GuzzleClient();
        $responseData = array();
        try {
            $response = Http::withBasicAuth('middleware', 'l1nk1t360')->patch(
                $url,
                $post_data
            );
            $responseData = json_decode($response, TRUE);
        } catch (\Throwable $th) {
        } catch (ClientException $e) {
        }

        return $responseData;
    }

    public static function deleteIpClient($url)
    {
        $responseData = array();
        try {
            $response = Http::withBasicAuth('middleware', 'l1nk1t360')->delete(
                $url
            );
            $responseData = json_decode($response, TRUE);
        } catch (\Throwable $th) {
        } catch (ClientException $e) {
        }

        return $responseData;
    }

    public  static function GetResponseCsTools($url)
    {
        $client = new GuzzleClient();
        $responseData = array();
        $timeout = (int)Configuration::where('key', 'timeout_settings')->first()->value;

        try {
            $response = $client->get($url, [
                'auth' => ['middleware', 'l1nk1t360'],
                'timeout' => $timeout, // Response timeout
                'connect_timeout' => $timeout, // Connection timeout
            ]);
            $responseData = json_decode($response->getBody(), TRUE);
        } 
        catch (ClientException $e) {
            $response = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $jsonStart = strpos($responseBodyAsString, '{');
            $jsonString = substr($responseBodyAsString, $jsonStart);
            $responseData = json_decode($jsonString, true);
            return $responseData;   
        }
        return $responseData;
    }
    public  static function GetResponseFromUrlMiddleware($url)
    {
        $client = new GuzzleClient();
        $responseData = array();
        $timeout = (int)Configuration::where('key', 'timeout_settings')->first()->value;

        try {
            $response = $client->get($url, [
                'auth' => ['middleware', 'l1nk1t360'],
                'timeout' => $timeout, // Response timeout
                'connect_timeout' => $timeout, // Connection timeout
            ]);
            $responseData = json_decode($response->getBody(), TRUE);
        } catch (GuzzleException $e) {
            $responseData['error_timeout'] = 1;
            $responseData['message'] = $e->getMessage();
            return $responseData;

        } catch (ClientException $e) {
            $responseData['error_timeout'] = 2;
            $responseData['message'] = $e->getMessage();
            return $responseData;
        }

        return $responseData;
    }

    public  static function GetResponseFromUrl($url)
    {
        $client = new GuzzleClient();
        $responseData = array();

        try {
            $response = $client->request('GET', $url,);
            $responseData = json_decode($response->getBody(), FALSE);
        } catch (ClientException $e) {
            $response = $e->getResponse();
            //$responseData = $response->getBody()->getContents();
            $message = $url . "has Exception";
            Log($message);
        }

        return $responseData;
    }

    public  static function GetArrayResponseFromUrl($url)
    {
        $client = new GuzzleClient();
        $responseData = array();

        try {
            $response = $client->request('GET', $url,);
            $responseData = json_decode($response->getBody(), TRUE);
        } catch (ClientException $e) {
            $response = $e->getResponse();
            //$responseData = $response->getBody()->getContents();
            $message = $url . "has Exception";
            Log($message);
        }

        return $responseData;
    }

    public  static function GetResponseFromResult($url, $post_data)
    {
        $client = new GuzzleClient();
        $responseData = array();

        try {
            $headers = [
                'Content-Type' => 'application/json',
                'Content-Length: ' . count($post_data),
            ];

            $result = $client->request(
                'POST',
                $url,
                [
                    'headers' => $headers,
                    'json' => $post_data
                ]
            );

            $responseData = $result->getBody()->getContents();
        } catch (ClientException $e) {
            $response = $e->getResponse();
            //$responseData = $response->getBody()->getContents();
            $message = $url . "has Exception";
            Log($message);
        }

        return $responseData;
    }

    public function getIdbyCountryName($Country, $country_name)
    {
        if ($country_name == "" || !isset($country_name)) {
            return 0;
        }

        $country_name = strtolower($country_name);

        $countries = array();
        $j = 0;
        foreach ($Country as $key => $value) {
            $key = strtolower($key);
            $countries[$key] = $value;
        }

        $contains = Arr::hasAny($countries, $country_name);

        if ($contains) {
            $id_country = Arr::get($countries, $country_name);
            return $id_country;
        }

        return 0;
    }

    public static function getIdbyOperatorName($name, $opeartors)
    {
        if ($name == "" || !isset($name)) {
            return 0;
        }

        $name = strtolower($name);

        $operatorslower = array();
        $j = 0;

        foreach ($opeartors as $key => $value) {
            $key = strtolower($key);
            $operatorslower[$key] = $value;
        }

        $operator = Arr::hasAny($operatorslower, $name);

        if ($operator) {
            $operator_id = Arr::get($operatorslower, $name);

            return $operator_id;
        }

        return 0;
    }

    public static function CronLog($message)
    {
        $log = "\n\r" . date("Y-m-d H:i:s") . " : " . $message;

        echo  $log;
    }

    public static function user_activity($message)
    {
        $user_id = Auth::user()->id;

        UserActivity::create(
            [
                'user_id' => $user_id,
                'action' => $message,
            ]
        );
    }
}

