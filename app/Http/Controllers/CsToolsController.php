<?php

namespace App\Http\Controllers;

use App\common\Utility;
use App\Models\Configuration;
use App\Models\Country;
use App\Models\CountryArpu;
use Illuminate\Http\Request;
use App\Models\cs_tools;
use App\Models\MappingOperator;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CsToolsController extends Controller
{
    public function show(Request $request)
    {

        $url = Configuration::where('key', 'middleware_url_api_v1')->first()->value;
        $errorAPI = false;
        $emptyData = false;

        $simpleArrayCountries = CountryArpu::get("country");
        foreach($simpleArrayCountries as $simpleCountry) {
            $arrayCountries[] = $simpleCountry->country;
        } 
        $countrys = Country::whereIn('country', $arrayCountries)->get();
        $listAction = [];
        $listChannel = [];
        $utility = new Utility;

        $serviceActive = 0;
        $serviceInactive = 0;
        $totalSpending = 0;
        $iconCountry = '';
        $dateLatest = '';
        $currencyCode = '';
        $totalAmountCharge = 0;
        $totalChargeSuccess = 0;
        $totalOperator = 0;
        $actions = [];
        $channels = [];
        $operatorMapping = [];

        //cs activities
        $csActivities = [];
        $listCsName = [];
        $listActionCs = [];
        $listOperatorCs = [];
        $listServiceCs = [];
        $csActivityMapping = [];
        if ($request->filled('msisdn') && $request->filled('country')) {

            try {

                $iconCountry = Country::where('id', $request->country)->first()->flag;
                $currencyCode = Country::where('id', $request->country)->first()->currency_code;
                $countryCode = Country::where('id', $request->country)->first()->country_code;
                if($countryCode == "IG") {
                    $urlFinal = $url . "cs-tools?msisdn=" . $request->msisdn . "&" . "country=" . strtolower("IQ");
                }else {
                    $urlFinal = $url . "cs-tools?msisdn=" . $request->msisdn . "&" . "country=" . strtolower($countryCode);

                }
        
                if ($request->filled('operator')) {
                    $operator = MappingOperator::where('operator_id',$request->operator)->first();
                    $operatorParams = $operator->operator;
                    $urlFinal .= "&operator=" . $operator->mapping_operator;

                }
                $result = $utility->GetResponseCsTools($urlFinal);
                if($result["status"] == 404) {
                    return redirect()->route('report.tools.show')->with('error_empty', $result['errors'][0]);
                }
                if($result["status"] == 400) {
                    return redirect()->route('report.tools.show')->with('error_empty', $result['errors'][0]);
                }

                if (is_null($result['data']['subs'])) {
                    $emptyData = true;
                }
                $subsLog = [];
                $logsData = [];
                if (isset($result['error_timeout'])) {
                    $tools =  [];
                    $toolsService = [];
                    $toolsLog = [];
                    $errorAPI = true;
                } else {

                    if($countryCode == "IG") {
                        $latest = Http::withBasicAuth('middleware', 'l1nk1t360')->get($url . "cs-tools/latestupdate/IQ");
                    }else {
                        $latest = Http::withBasicAuth('middleware', 'l1nk1t360')->get($url . "cs-tools/latestupdate/$countryCode");

                    }
                    $latestUpdate = json_decode($latest);
                    $dateLatest = date("Y-m-d H:i:s", strtotime($latestUpdate->data->latest_update));

                    $responseCsActivity = $utility->GetResponseFromUrlMiddleware($url . "activity");
                    $csActivities = $responseCsActivity['data'];
                    // dd($csActivities);
                    foreach ($csActivities as $activity) {
                        if ($activity['msisdn']  == $request->msisdn) {

                            if ($request->filled("operator")) {
                                if ($activity['operator'] == $request->operator) {
                                    if (!in_array($activity['action'], $listActionCs)) {
                                        $listActionCs[] = $activity['action'];
                                    }
                                    if (!in_array($activity['cs_name'], $listCsName)) {
                                        $listCsName[] = $activity['cs_name'];
                                    }
                                    if (!in_array($activity['operator'], $listOperatorCs)) {
                                        $listOperatorCs[] = $activity['operator'];
                                    }
                                    if (!in_array($activity['service'], $listServiceCs)) {
                                        $listServiceCs[] = $activity['service'];
                                    }
                                    $csActivityMapping[] = [
                                        'id' => $activity['id'],
                                        'cs_name' => $activity['cs_name'],
                                        'action' => $activity['action'],
                                        'operator' => $activity['operator'],
                                        'service' => $activity['service'],
                                        'created_at' => $activity['created_at'],
                                        'note' => $activity['note'],
                                        'msisdn' => $activity['msisdn'],
                                    ];
                                }
                            } else {
                                if (!in_array($activity['action'], $listActionCs)) {
                                    $listActionCs[] = $activity['action'];
                                }
                                if (!in_array($activity['cs_name'], $listCsName)) {
                                    $listCsName[] = $activity['cs_name'];
                                }
                                if (!in_array($activity['operator'], $listOperatorCs)) {
                                    $listOperatorCs[] = $activity['operator'];
                                }
                                if (!in_array($activity['service'], $listServiceCs)) {
                                    $listServiceCs[] = $activity['service'];
                                }
                                $csActivityMapping[] = [
                                    'id' => $activity['id'],
                                    'cs_name' => $activity['cs_name'],
                                    'action' => $activity['action'],
                                    'operator' => $activity['operator'],
                                    'service' => $activity['service'],
                                    'created_at' => $activity['created_at'],
                                    'note' => $activity['note'],
                                    'msisdn' => $activity['msisdn'],
                                ];
                            }
                        }
                    }


                    if (!is_null($result['data']['subs'])) {
                        foreach ($result['data']['subs']  as $subs) {
                            if ($subs['status'] == 1) {
                                $serviceActive++;
                            } else if ($subs['status'] == -1) {
                                $serviceInactive++;
                            }
                            if (!in_array($subs['operator'], $operatorMapping)) {
                                $operatorMapping[] = $subs['operator'];
                                $totalOperator++;
                            }
                            $totalSpending += $subs['revenue'];
                            if($subs["country"] == "IQ") {
                                $countryName = Country::where('country_code',"IG")->first()->country;

                            }else {
                                $countryName = Country::where('country_code', $subs['country'])->first()->country;

                            }
                            $dateUnsubs = strtotime($subs['unsubs_date']);
                            $dateSubs = strtotime($subs['subs_date']);
                            $dateRenewal = strtotime($subs['renewal_date']);
                            $dateFreemium = strtotime($subs['freemium_end_date']);
                            $dateCharge = strtotime($subs['charge_date']);
                            $totalAmountCharge += $subs['attempt_charging'];
                            $subsLog[] = [
                                'msisdn' => $subs['msisdn'] ?? '',
                                'country_name' => $countryName ?? '',
                                'country_code' => $subs['country'] ?? '',
                                'operator' => $subs['operator'] ?? '',
                                'subs_date' => !empty($subs['subs_date']) ?  date("Y-m-d H:i:s", $dateSubs) : "",
                                'unsubs_date' => !empty($subs['unsubs_date']) ?  date("Y-m-d H:i:s", $dateUnsubs) : "",
                                'revenue' => $subs['revenue'] ?? '',
                                'adnet' => $subs['adnet'] ?? '',
                                'service' => $subs['service'] ?? '',
                                'cycle' => $subs['cycle'] ?? '',
                                'subs_source' => $subs['subs_source'] ?? '',
                                'renewal_date' => !empty($subs['renewal_date']) ?  date("Y-m-d H:i:s", $dateRenewal) : "",
                                'freemium' => !empty($subs['freemium_end_date']) ?  date("Y-m-d H:i:s", $dateFreemium) : "",
                                'browser' => $subs['browser'] ?? '',
                                'unsubs_from' => $subs['unsubs_from'] ?? '',
                                'handset' => $subs['handset'] ?? '',
                                'last_charge_attempt' => $subs['attempt_charging'] ?? '',
                                'charge_date' => !empty($subs['charge_date']) ?  date("Y-m-d H:i:s", $dateCharge) : "",
                                'status' => $subs['status'] ?? '',
                                'type' => $subs['type'] ?? '',
                                'profile_status' => $subs['profile_status']['String'] ?? '',
                                'unsubscription' => $subs['unsubscription'] ?? '',
                                'cp' => $subs['cp'] ?? '',
                                'subscription_source' => $subs['subs_source'] ?? '',
                                'unsubscription_source' => $subs['unsubscription_source'] ?? ''

                            ];
                        }
                        if(!is_null($result["data"]["logs"]) ) {

                            foreach ($result['data']['logs'] as $logs) {
                                if (!in_array($logs['action'], $actions)) {
                                    $actions[] = $logs['action'];
                                }
                                if (!in_array($logs['channel'], $channels)) {
                                    $channels[] = $logs['channel'];
                                }
                                $logsDateCharge = strtotime($logs['charge_date']);
                                $logsDate = strtotime($logs['date']);
                                if ($logs['is_paid'] == 1 && $logs['price'] != 0) {
                                    $totalChargeSuccess++;
                                }
                                $logsData[] = [
                                    'service' => $logs['service'] ?? '',
                                    'date' => !empty($logs['date']) ?  date("Y-m-d H:i:s", $logsDate) : "",
                                    'charge_date' => !empty($logs['charge_date']) ?  date("Y-m-d H:i:s", $logsDateCharge) : "",
                                    'price' => $logs['price'] ?? '',
                                    'paid' => $logs['is_paid'] == true ? "Paid" : "Unpaid",
                                    'errors' => $logs['errors'] ?? '',
                                    'action' => $logs['action'] ?? '',
                                    'channel' => $logs['channel'] ?? '',
                                    'telco_api_url' => $logs['api_url'] ?? '',
                                    'telco_date' => !empty($logs['date']) ?  date("Y-m-d H:i:s", $logsDate) : "",
                                    'telco_api_response' => $logs['api_response'] ?? '',
                                    'operator' => $logs['operator'] ?? '',
                                    'sms_content' => $logs['sms_content'] ?? '',
                                    'status_sms' => $logs['sms_status'] ?? '',
                                    'sms_date' => !empty($logs['date']) ?  date("Y-m-d H:i:s", $logsDate) : "",
                                ];
                            }
                        }
                    }
                }
                $tools = $subsLog;
                $toolsService = $subsLog;
                $toolsLog = $logsData;
                $listAction = $actions;
                $listChannel = $channels;
            } catch (\Exception $e) {
                $errorAPI = true;
                return redirect()->route('report.tools.show')->with('error_api',$e->getMessage());
                // $tools =  [];
                // $toolsService = [];
                // $toolsLog = [];
                // return view('tools.show', compact('tools', 'csActivityMapping', 'listChannel', 'serviceActive', 'iconCountry', 'serviceInactive', 'totalSpending', 'toolsService', 'toolsLog', 'countrys', 'errorAPI', 'totalAmountCharge', 'emptyData', 'listAction', 'dateLatest', 'currencyCode', 'totalOperator', 'totalChargeSuccess', 'csActivities', 'listCsName', 'listActionCs', 'listOperatorCs', 'listServiceCs'));
            }
        } else {
            $tools =  [];
            $toolsService = [];
            $toolsLog = [];
        }
        return view('tools.show', compact('tools', 'listChannel', 'serviceActive', 'iconCountry', 'serviceInactive', 'totalSpending', 'toolsService', 'toolsLog', 'countrys', 'csActivities', 'errorAPI', 'totalAmountCharge', 'emptyData', 'listAction', 'dateLatest', 'currencyCode', 'totalChargeSuccess', 'listCsName', 'totalOperator', 'csActivityMapping', 'listActionCs', 'listOperatorCs', 'listServiceCs'));
    }
    public function unsubs(Request $request)
    {
        $globalconfig = \config('globalconfig');
        $url = $globalconfig['middleware'] . 'subscription/unsubs';
        $urlActivity = $globalconfig['middleware'] . 'activity';
        $updated_by = auth()->user()->name;
        $countryCode = Country::where('id', $request->country)->first()->country_code;

        try {
            $data = [
                'msisdn' => $request->msisdn,
                'service' => $request->service,
                'country' => $countryCode,
            ];

            $response = Http::withBasicAuth('middleware', 'l1nk1t360')->patch($url, $data);

            $result = json_decode($response, true);

            if ($result['status'] != 200) {
                return back()->with(
                    'error',
                    __($result['message'] . " " . $result['errors'][0])
                );
            } else {
                $utility = new Utility;
                $csActivity = $utility->insertCsActivity($urlActivity, [
                    "cs_name" => $updated_by,
                    "action" => "Unsubscribe",
                    "msisdn" => $request->msisdn,
                    "operator" => $request->operator,
                    "service" => $request->service,
                ]);
                return back()->with(
                    'success',
                    __($result['message'] . ", " . $result['data']['message'])
                );
            }
            // dd($result);
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }
    public function blacklist(Request $request)
    {
        $globalconfig = \config('globalconfig');
        $url = $globalconfig['middleware'] . 'subscription/unsubs';
        try {
            // $data = [
            //     'msisdn' => $request->msisdn,
            //     'service' => $request->service,
            //     'country' => $request->country
            // ];

            // $response = Http::withBasicAuth('middleware', 'l1nk1t360')->patch($url, $data);

            // $result = json_decode($response, true);

            // if ($result['status'] != 200) {
            //     return back()->with(
            //         'error',
            //         __($result['message'] . " " . $result['errors'][0])
            //     );
            // }
            return back()->with(
                'error',
                __("error")
            );
            // return back()->with(
            //     'success',
            //     __($result['message'] . ", " . $result['data']['message'])
            // );
            // dd($result);
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
        }
    }
    public function updateCsActivity(Request $request)
    {

        try {
            $url = Configuration::where('key', 'middleware_url_api_v1')->first()->value;
            $utility = new Utility;
            $csActivity = $utility->updateCsActivity($url . "activity", [
                'id' => (int)$request->id,
                'note' => $request->note
            ]);
            if ($csActivity['status'] == 200) {
                return back()->with('success', $csActivity['message'] . ', ' .  $csActivity['data']['message']);
            } else {
                return back()->with('error', $csActivity['message'] . $csActivity['errors'][0]);
            }
        } catch (\Exception $e) {
            //throw $th;
            throw new HttpException(500, $e->getMessage());
        }
    }
}
