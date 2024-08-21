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

class UtilityAccountManager
{
    public static function getReportsOperatorID($reports)
    {
        if(!empty($reports))
        {
            $reportsResult = array();
            $tempreport = array();

            foreach($reports as $report)
            {
                $date = isset($report['date']) ? $report['date'] : $report['key'];
                $tempreport[$report['user_id']][$report['operator_id']][$date] = $report;
            }

            $reportsResult = $tempreport;

            return $reportsResult;
        }
    }
}
