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

class UtilityMobifone
{
    public static function ServiceRearrangeDate($services)
    {
        if(!empty($services))
        {
            $servicesResult = array();
            $tempservices = array();
            foreach($services as $service)
            {
                $tempservices[$service['date']][] = $service->toArray();
            }

            $servicesResult = $tempservices;

            return $servicesResult;
        }
    }
}
