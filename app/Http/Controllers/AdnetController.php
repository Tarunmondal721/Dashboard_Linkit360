<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Session;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\report_summarize;
use App\Models\Operator;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use App\Models\Company;
use App\Models\Country;
use App\Models\CompanyOperators;
use App\Models\User;
use App\Models\Revenushare;
use App\common\Utility;
use App\common\UtilityReports;
use App\Models\ApiArpuHistory;
use App\Models\Configuration;
use App\Models\CountryArpu;
use App\Models\MappingOperator;
use App\Models\MappingService;
use App\Models\ReportsPnls;
use App\Models\ReportsPnlsOperatorSummarizes;
use Illuminate\Support\Str;

use function PHPUnit\Framework\isNull;

class AdnetController extends Controller
{
    public $layout = 'v2dashboard';
    public $ApiUrl = 'http://149.129.252.221:8028/';

    public function index(Request $request)
    {
        ini_set('max_execution_time', 0);

        if (\Auth::user()->can('Adnet Report')) {

            try {
                $operators = [];
                $countries = [];
                $operator_wise_usd = [];
                $services = [];
                $adnet_operator = '';
                $adnet_service = '';
                $adnet_country = '';
                $all_operator_service_publishers = [];
                $details = [];
                $selected_country_based_operators = [];
                $selected_operator_based_services = [];
                $filter_renewal = '';
                $filter_to = '';
                $filter_from = '';
                $operator_revenue_share = 0;
                $inputRequest = [];
                $omr = "";
                $day = 0;
                $errorRangeDays = 0;

                //get array arpu country
                $simpleArrayCountries = CountryArpu::get("country");
                foreach($simpleArrayCountries as $simpleCountry) {
                    $arrayCountries[] = $simpleCountry->country;
                } 
            
                $emptyData = false;
                $Operators = Operator::all();
                if (isset($Operators) && !empty($Operators)) {
                    foreach ($Operators as $okey => $ovalue) {
                        if (isset($ovalue) && !empty($ovalue)) {
                            $operators[$ovalue->id_operator] = $ovalue->operator_name;
                            // if (in_array($ovalue->country_name, $arrayCountries)) {
                            $countries[$ovalue->country_id] = $ovalue->country_name;
                            // }
                            $operator_wise_usd[$ovalue->country_id] =  $ovalue->country->usd;
                            $country_based_operators[$ovalue->country_id][$ovalue->id_operator] = $ovalue->operator_name;
                            $currency_code[$ovalue->country_id] = $ovalue->country->country_code;
                        }
                    }
                }
                // dd($countries);

                // dd($operator_wise_usd);

                $params = $request->all();
                if (isset($params) && !empty($params)) {
                    $d = key(array_slice($params, 0, 1, true));
                    foreach ($params as $key => $param) {
                        if (empty($param)) {
                            unset($params[$key]);
                        }
                    }

                    if (!empty($params[$d])) {
                        $adnet_country = $params[$d];
                        $omr = Country::find($adnet_country)->currency_code;
                    }

                    if (!empty($params['operator'])) {
                        $adnet_operator = $params['operator'];
                        $adnet_operator_name = (isset($operators[$adnet_operator]) && !empty($operators[$adnet_operator])) ? $operators[$adnet_operator] : '';
                    }

                    if (!empty($params['service'])) {
                        $adnet_service = $params['service'];
                    }
                    if (!empty($params['keyword'])) {
                        $adnet_service = $params['keyword'];
                    }

                    if (!empty($params['from'])) {
                        $filter_from = date('Y-m-d', strtotime($params['from']));
                    }

                    if (!empty($params['to'])) {
                        $filter_to  = date('Y-m-d', strtotime($params['to']));
                    }

                    if (!empty($params['renewal'])) {
                        $filter_renewal = date('Y-m-d', strtotime($params['renewal']));
                    }

                    if (!empty($params['service'])) {
                        $serviceParams = MappingService::where('service_id', $params['service'])->first()->service_name;
                    } else {
                        $serviceParams = "Select Service";
                    }
                    if (!empty($params['keyword'])) {
                        $keywordParams = MappingService::where('service_id', $params['keyword'])->first()->service_name;
                    } else {
                        $keywordParams = "No keywords available";
                    }
                    if ($params['option_date'])
                        $inputRequest = [

                            'country' => [
                                'id' => $params['country'] ?? "",
                                'label' => !empty($params['country']) ?  Country::where('id', $params['country'])->first()->country : "Select Country"
                            ],
                            'operator' => [
                                'id' => $params['operator'] ?? '',
                                'label' => !empty($params['operator']) ?  Operator::where('id_operator', $params['operator'])->first()->operator_name : "Select Operator"
                            ],
                            'service' => [
                                'id' => $params['service'] ?? '',
                                'label' => $serviceParams
                            ],
                            'keyword' => [
                                'id' => $params['keyword'] ?? '',
                                'label' => $keywordParams
                            ],
                            'order' => [
                                'order' => $params['filterAmount'] ?? 'Highest Subs',
                            ],
                            'option_date' => [
                                'id' => $params['option_date'] ?? '',
                                'label' => $params['option_date'] ?? ''
                            ],
                            'from' => $params['from'] ?? '',
                            'to' => $params['to'] ?? '',
                            'renewal' => $params['renewal'] ?? ''
                        ];
                }
                if (isset($adnet_country) && $adnet_country > 0) {
                    $usd = (isset($countries[$adnet_country]) && $countries[$adnet_country] == 'Cambodia') ? 1 : ((isset($operator_wise_usd[$adnet_country]) && !empty($operator_wise_usd[$adnet_country])) ? $operator_wise_usd[$adnet_country] : 0);

                    $selected_country_based_operators = (isset($country_based_operators[$adnet_country]) && !empty($country_based_operators[$adnet_country])) ? $country_based_operators[$adnet_country] : [];
                }

                if (isset($adnet_operator) && $adnet_operator > 0) {
                    $operator = Operator::find($adnet_operator);
                    if (isset($operator->services) && !empty($operator->services)) {
                        foreach ($operator->services as $dkey => $dvalue) {
                            $operator_based_services[$dvalue->id_service] = $dvalue->service_name;
                        }
                    }


                    $selected_operator_based_services = (isset($operator_based_services) && !empty($operator_based_services)) ? $operator_based_services : [];

                    $query = Service::GetserviceByOperatorId($adnet_operator)->GetserviceById($adnet_service)->first();

                    $service = (isset($query['keyword']) && !empty($query['keyword'])) ? $query['keyword'] : '';

                    if ($params['option_date'] == "Today") {
                        $renewal = date('Y-m-d');
                        $fromDate = strtotime($filter_from);
                        $renewalDate = strtotime($renewal);
                        $datediff = $renewalDate - $fromDate;
                    }else {
                        $fromDate = strtotime($filter_from);
                        $toDate = strtotime($filter_renewal);
                        $datediff = $toDate - $fromDate;

                    }
                    $day = round($datediff / (60 * 60 * 24)) + 1;
                    $day -= 1;
                    $details = $this->get_details($adnet_operator, $adnet_service,$request->keyword, $filter_from, $filter_to, $filter_renewal, $adnet_country, $usd, $params['option_date'], $day, $request->filterAmount ?? "Highest Subs");
                    // dd($details);
                    if (empty($details['sorted_data'])) {
                        $emptyData = true;
                    }
                    $operator = Operator::with('revenueshare')->find($adnet_operator);
                    $revenueshare = $operator->revenueshare;

                    $operator_revenue_share = (isset($revenueshare) && !empty($revenueshare)) ? (($revenueshare['merchant_revenue_share']) / 100) : 0;
                    // dd($operator_revenue_share);
                }
                $adnetreport = ['countries' => $countries, 'operator_wise_usd' => $operator_wise_usd, 'filter_country' => $adnet_country, 'filter_operator' => $adnet_operator, 'filter_service' => $adnet_service, 'details' => $details['sorted_data'] ?? [],'is_publisher' => $details['is_publisher'] ?? 0, 'selected_services' => $selected_operator_based_services, 'selected_operators' => $selected_country_based_operators, 'filter_from' => $filter_from, 'filter_to' => $filter_to, 'filter_renewal' => $filter_renewal, 'currency_code' => $currency_code, 'operator_revenue_share' => $operator_revenue_share, 'input_request' => $inputRequest, 'omr' => $omr, 'emptyData' => $emptyData, 'arrayCountries' => $arrayCountries, 'day' => $day, 'errorRangeDays' => $errorRangeDays, "params_query" => $details['params'] ?? null];

                return view('report.adnetreport', compact('adnetreport'));
            } catch (\Throwable $th) {
                //throw $th;
                return back()->with('error', $th->getMessage());
            }
            
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function get_details($id_operator, $id_service,$keyword, $from, $to, $renewal, $id_country, $usd, $option_date, $day, $order)
    {
        // dd($order);
        $is_publisher =0;
        if ($option_date == "Today") {
            $renewal = date('Y-m-d');
        }
        $url = Configuration::where('key', 'middleware_url_api')->first()->value;
        $utility = new Utility;
        $operator = strtolower(Operator::find($id_operator)->operator_name);
        $mappingOperator = MappingOperator::where("operator", $operator)->first();

        $country = strtoupper(Country::find($id_country)->country_code);
        if($country == "IG") {
            $country = "IQ";
        }

        if($mappingOperator) {
            $urlComplete = $url . "arpu?from=" . $from . "&to=" . $to . "&country=" . $country . "&operator=" . $mappingOperator->mapping_operator  . "&to_renewal=" . $renewal;

        }else {
            $urlComplete = $url . "arpu?from=" . $from . "&to=" . $to . "&country=" . $country . "&operator=" . $operator  . "&to_renewal=" . $renewal;

        }
        
        $serviceParam = "";
        $cycle = "";
        $mappingService = (object)[];
        if($keyword != "") {
            $mappingService = MappingService::where("service_id", $keyword)->first();
        }else {
            $mappingService = MappingService::where("service_id", $id_service)->first();
        }
        if($mappingService ) {
            if($mappingService->cycle != null) {
                $urlComplete .= "&cycle=" . $mappingService->cycle;
                $cycle = $mappingService->cycle;

            }
            if($mappingService->mapping_service != null ) {
                $urlComplete .= "&service=" . $mappingService->mapping_service;
                
            }else {
                $urlComplete .= "&service=" . $mappingService->keyword;
            }
            $serviceParam = $mappingService->service_name;
            $service = $mappingService->mapping_service ?? $mappingService->keyword;
        }else {
            $service = Service::find($id_service)->keyword;
            $serviceParam = $service->service_name;
            $urlComplete .= "&service=" . $service->keyword;
            
        }
        // dd($urlComplete);
        $result = $utility->GetResponseFromUrlMiddleware($urlComplete);

        $adnet_data = [];
        if (isset($result['error_timeout']) && $result["error_timeout"] == 1) {
            $errorTimeout = [
                'sorted_data' => $result['error_timeout']
            ];
            $apiHistory = ApiArpuHistory::where('url', $urlComplete)->first();
            if(!$apiHistory) {
                ApiArpuHistory::create([
                    "country" => $country,
                    "operator" => $mappingOperator->mapping_operator ?? $operator,
                    "service" => $service,
                    "cycle" => $cycle ?? null,
                    "from" => $from,
                    "to" => $to,
                    "renewal" => $renewal,
                    "url" => $urlComplete,
                    "status" => "failed"
                ]);
            }
            return $errorTimeout;
        }else if (isset($result['error_timeout']) && $result["error_timeout"] == 2) {
            $errorTimeout = [
                'sorted_data' => $result['error_timeout']
            ];
            $apiHistory = ApiArpuHistory::where('url', $urlComplete)->first();
            if(!$apiHistory) {
                ApiArpuHistory::create([
                    "country" => $country,
                    "operator" => $mappingOperator->mapping_operator ?? $operator,
                    "service" => $service,
                    "cycle" => $cycle ?? null,
                    "from" => $from,
                    "to" => $to,
                    "renewal" => $renewal,
                    "url" => $urlComplete,
                    "status" => $result["message"]
                ]);
            }
            
            return $errorTimeout;
        }else {
            $apiHistory = ApiArpuHistory::where('url', $urlComplete)->first();
            if(!$apiHistory) {
                ApiArpuHistory::create([
                    "country" => $country,
                    "operator" => $mappingOperator->mapping_operator ?? $operator,
                    "service" => $service,
                    "cycle" => $cycle ?? null,
                    "from" => $from,
                    "to" => $to,
                    "renewal" => $renewal,
                    "url" => $urlComplete,
                    "status" => "success"
                ]);
            }
            
        }
        if (!isset($result['data']['data'])) {
            $apiHistory = ApiArpuHistory::where('url', $urlComplete)->first();
            if(!$apiHistory) {
                ApiArpuHistory::create([
                    "country" => $country,
                    "operator" => $mappingOperator->mapping_operator ?? $operator,
                    "service" => $service,
                    "cycle" => $cycle ?? null,
                    "from" => $from,
                    "to" => $to,
                    "renewal" => $renewal,
                    "url" => $urlComplete,
                    "status" => "success"
                ]);
            }
            return $errorEmpty = [
                "sorted_data" => $adnet_data
            ];
        }
        foreach ($result['data']['data'] as $key => $value) {
            if (!array_key_exists($value['adnet'], $adnet_data)) {

                $adnet_data[$value['adnet']]['publisher'][$value['publisher']]['subs'] = $value['subs'];
                $adnet_data[$value['adnet']]['publisher'][$value['publisher']]['publisher'] = $value['publisher'];
                $adnet_data[$value['adnet']]['publisher'][$value['publisher']]['revenue'] = $value['revenue'];
                $adnet_data[$value['adnet']]['publisher'][$value['publisher']]['subs_active'] = $value['subs_active'];
                // dd($adnet_data);
            } else {
                if (!array_key_exists($value['publisher'], $adnet_data[$value['adnet']]['publisher'])) {
                    $is_publisher = 1;
                    $adnet_data[$value['adnet']]['publisher'][$value['publisher']]['subs'] = $value['subs'];
                    $adnet_data[$value['adnet']]['publisher'][$value['publisher']]['publisher'] = $value['publisher'];
                    $adnet_data[$value['adnet']]['publisher'][$value['publisher']]['revenue'] = $value['revenue'];
                    $adnet_data[$value['adnet']]['publisher'][$value['publisher']]['subs_active'] = $value['subs_active'];
                } else {
                    $adnet_data[$value['adnet']]['publisher'][$value['publisher']]['subs'] += $value['subs'];
                    $adnet_data[$value['adnet']]['publisher'][$value['publisher']]['revenue'] += $value['revenue'];
                    $adnet_data[$value['adnet']]['publisher'][$value['publisher']]['subs_active'] += $value['subs_active'];
                }
            }
        }
        $adnetNew = [];

        $pcontoh = [];
        foreach ($adnet_data as $key => $ovalue) {
            $totalSubs = 0;
            $totalAmount = 0;
            $totalSubsActive = 0;
            $totalArpuUsd = 0;
            $totalArpuUsd30 = 0;
            $totalArpuUsd60 = 0;
            $totalArpuUsd90 = 0;
            $totalChurn = 0;
            $totalChurn30 = 0;
            $totalArpu30 = 0;
            $totalArpu60 = 0;
            $totalArpu = 0;
            $totalArpu90 = 0;
            $totalLTV=0;
            $totalLTVUSD=0;
            $totalLtvForecastA=0;
            $totalLtvForecastAUSD=0;
            $totalLtvForecastC=0;
            $totalLtvForecastCUSD=0;
            $totalLTVActual = 0;
            $totalLTVActualUsd = 0;
            $totalCac = 0;
            $totalRoi = 0;
            $totalEmarginLtvA = 0;
            $totalEmarginLtvB = 0;
            $totalEmarginLtvC = 0;
            // dd($to);
            
            $costCampaignAdnet = ReportsPnls::where('publisher', strtolower($key))
                ->where('service', 'like', "%" . $mappingService->keyword . "%")
                ->where('operator', $mappingOperator->operator)
                ->whereBetween('date', [$from, $to])
                ->sum('saaf');
            foreach ($ovalue['publisher'] as $value) {

                $churn = ($value['subs'] == 0) ? 0 : ((($value['subs'] - $value['subs_active']) / $value['subs']) * 100);
                $adnetNew[$key]['publisher'][$value['publisher']]['acquired_subs_count'] = $value['subs'];
                $adnetNew[$key]['publisher'][$value['publisher']]['amount_sum'] = $value['revenue'];
                $adnetNew[$key]['publisher'][$value['publisher']]['retained_subs_count'] = $value['subs_active'];
                $churn = ($value['subs'] == 0) ? 0 : ((($value['subs'] - $value['subs_active']) / $value['subs']) * 100);
                
                $adnetNew[$key]['publisher'][$value['publisher']]['churn'] = $churn;
                
                if ($country == "OM") {
                    
                    $arpu = ($value['subs'] == 0) ? 0 : (($value['revenue'] / 1000) / $value['subs']);
                    $adnetNew[$key]['publisher'][$value['publisher']]['arpu'] = $arpu;
                    $arpu_usd = ($value['revenue'] / 1000) / $value['subs'] * $usd;
                    $ltvActual = ($value['revenue'] / 1000) / $value['subs'];
                    $ltvActualUsd = ($value['revenue'] / 1000) / $value['subs'] * $usd;

                    $adnetNew[$key]['publisher'][$value['publisher']]['amount_sum'] = $value['revenue'] / 1000;
                    $percent30 = 30 / $day;
                    // dd($day);
                    $churn30 =  100-($churn * $percent30);
                    if($churn == 0) {
                        $churn30 = 100;
                    }
                    $arpu30 = $arpu * $percent30;

                    $finalArpu30 = $arpu30 * ($churn30)/100;
                    if($finalArpu30 < 0) {
                        $churn30 += 100;
                        $finalArpu30 = $arpu30 * ($churn30)/100;
                        if($finalArpu30 < 0 ){
                            $finalArpu30 = 0 + $arpu;
                        }
                        $arpu60 = $finalArpu30;
                        $arpu90 = $finalArpu30;
                    }else {
                        $remainingchurn60 = 100 - (($churn * $percent30) * 2);

                        if($remainingchurn60 <0) {
                            $remainingchurn60 +=100;

                        }else if($remainingchurn60  >= 0  && $remainingchurn60 < 1) {
                            $remainingchurn60 = 50;
                        }
                        $arpu60 = ($finalArpu30 * ($remainingchurn60)/100) + $finalArpu30;

    
                        if(($churn * $percent30) * 2 >=100) {
                            $arpu90 = 0 + $arpu60;
                        }else {
                            $remainingchurn90 = 100 - (($churn * $percent30) * 2);
                            if($remainingchurn90 < 0) {
                                // $remainingchurn90 = abs($remainingchurn90);
                                $remainingchurn90 +=100;

                            }
                            $arpu90 =($finalArpu30 * ($remainingchurn90/100)) + $arpu60;
                        }

                    }
                    
                    $finalarpuUsd30 = $finalArpu30 * $usd;
                    
                    $usd60 = $arpu60 * $usd ;
                    
                    $usd90 = $arpu90 *$usd ;

                    $avgArpu = $arpu90/3;
                    $aon = 6;
                    $ltvForecast = $avgArpu * $aon;
                    $ltvForecastUsd = $ltvForecast * $usd;
                    if($churn ==0) {
                        $ltvforecast_a = 0 ;
                    }else {
                        $ltvforecast_a = $arpu * (1 - ($churn/100)) / ($churn /100);
                    }

                    if($churn == 0) {
                        if(1 - ($churn * $percent30/100)  < 0 ) {
                            $ltvforecast_c = $finalArpu30 * (1 - ($churn * $percent30/100));

                        }else {
                            $ltvforecast_c = $finalArpu30 * (1 - ($churn * $percent30/100));

                        }
                    }else {
                        if(1 - ($churn * $percent30/100)  < 0 ) {
                            $absChurn =(($churn * $percent30/100) -1) ;
                            $ltvforecast_c = $finalArpu30 * ($absChurn / ($churn * $percent30 /100));
                        }else {
                            $ltvforecast_c = $finalArpu30 * (1 - ($churn * $percent30/100)) / ($churn * $percent30 /100);

                        }
                    }
                    $ltvforecast_a_usd = $ltvforecast_a * $usd;

                    $ltvforecast_c_usd = $ltvforecast_c * $usd;

                } else {
                    $arpu = ($value['subs'] == 0) ? 0 : ($value['revenue'] / $value['subs']);
                    $adnetNew[$key]['publisher'][$value['publisher']]['arpu'] = $arpu;

                    $arpu_usd = $value['revenue'] / $value['subs'] * $usd;
                    $ltvActual = $value['revenue'] / $value['subs'];
                    $ltvActualUsd = $value['revenue'] / $value['subs'] * $usd;

                    $percent30 = 30 / $day;
                    // dd($day);
                    $churn30 =  100-($churn * $percent30);
                    if($churn == 0) {
                        $churn30 = 100;
                    }
                    $arpu30 = $arpu * $percent30;

                    $finalArpu30 = $arpu30 * ($churn30)/100;
                    if($finalArpu30 < 0) {
                        $churn30 += 100;
                        $finalArpu30 = $arpu30 * ($churn30)/100;
                        if($finalArpu30 < 0 ){
                            $finalArpu30 = 0 + $arpu;
                        }
                        $arpu60 = $finalArpu30;
                        $arpu90 = $finalArpu30;
                    }else {
                        $remainingchurn60 = 100 - (($churn * $percent30) * 2);

                        if($remainingchurn60 <0) {
                            $remainingchurn60 +=100;

                        }else if($remainingchurn60  >= 0  && $remainingchurn60 < 1) {
                            $remainingchurn60 = 50;
                        }
                        $arpu60 = ($finalArpu30 * ($remainingchurn60)/100) + $finalArpu30;

    
                        if(($churn * $percent30) * 2 >=100) {
                            $arpu90 = 0 + $arpu60;
                        }else {
                            $remainingchurn90 = 100 - (($churn * $percent30) * 2);
                            if($remainingchurn90 < 0) {
                                // $remainingchurn90 = abs($remainingchurn90);
                                $remainingchurn90 +=100;

                            }
                            $arpu90 =($finalArpu30 * ($remainingchurn90/100)) + $arpu60;
                        }

                    }                   
                    $finalarpuUsd30 = $finalArpu30 * $usd;
                    
                    $usd60 = $arpu60 * $usd ;
                    
                    $usd90 = $arpu90 *$usd ;


                    $avgArpu = $arpu90/3;
                    $aon = 6;
                    $ltvForecast = $avgArpu * $aon;
                    $ltvForecastUsd = $ltvForecast * $usd;

                    if($churn ==0) {
                        $ltvforecast_a = 0;
                        
                    }else {
                        $ltvforecast_a = $arpu * (1 - ($churn/100)) / ($churn /100);
                    }

                    if($churn == 0) {
                        if(1 - ($churn * $percent30/100)  < 0 ) {
                            $ltvforecast_c = $finalArpu30 * (1 - ($churn * $percent30/100));

                        }else {
                            $ltvforecast_c = $finalArpu30 * (1 - ($churn * $percent30/100));

                        }
                    }else {
                        if(1 - ($churn * $percent30/100)  < 0 ) {
                            $absChurn =(($churn * $percent30/100) -1) ;
                            $ltvforecast_c = $finalArpu30 * ($absChurn / ($churn * $percent30 /100));
                        }else {
                            $ltvforecast_c = $finalArpu30 * (1 - ($churn * $percent30/100)) / ($churn * $percent30 /100);

                        }
                    }
                    $ltvforecast_a_usd = $ltvforecast_a * $usd;
                    
                    $ltvforecast_c_usd = $ltvforecast_c * $usd;



                }

                $adnetNew[$key]['publisher'][$value['publisher']]['ltv_forecast'] = $ltvForecast;
                $adnetNew[$key]['publisher'][$value['publisher']]['ltv_forecast_usd'] = $ltvForecastUsd;
                $adnetNew[$key]['publisher'][$value['publisher']]['ltv_forecast_a'] = $ltvforecast_a;
                $adnetNew[$key]['publisher'][$value['publisher']]['ltv_forecast_a_usd'] = $ltvforecast_a_usd;
                $adnetNew[$key]['publisher'][$value['publisher']]['ltv_forecast_c'] = $ltvforecast_c;
                $adnetNew[$key]['publisher'][$value['publisher']]['ltv_forecast_c_usd'] = $ltvforecast_c_usd;
                $adnetNew[$key]['publisher'][$value['publisher']]['actual_ltv'] = $ltvActual;
                $adnetNew[$key]['publisher'][$value['publisher']]['actual_ltv_usd'] = $ltvActualUsd;

                $adnetNew[$key]['publisher'][$value['publisher']]['arpu_usd'] = $arpu_usd;
                $adnetNew[$key]['publisher'][$value['publisher']]['arpu_usd_30'] = $finalarpuUsd30;
                $adnetNew[$key]['publisher'][$value['publisher']]['arpu_usd_60'] = $usd60;
                $adnetNew[$key]['publisher'][$value['publisher']]['arpu_usd_90'] = $usd90;

                $adnetNew[$key]['publisher'][$value['publisher']]['churn_30'] = ($churn * $percent30);
                $adnetNew[$key]['publisher'][$value['publisher']]['arpu_30'] = $finalArpu30;
                $adnetNew[$key]['publisher'][$value['publisher']]['arpu_60'] = $arpu60;
                $adnetNew[$key]['publisher'][$value['publisher']]['arpu_90'] = $arpu90;
                $adnetNew[$key]['publisher'][$value['publisher']]['publisher'] = $value['publisher'];

                $totalLTV += $adnetNew[$key]['publisher'][$value['publisher']]['ltv_forecast'];
                $totalLTVUSD += $adnetNew[$key]['publisher'][$value['publisher']]['ltv_forecast_usd'];
                $totalLtvForecastA += $adnetNew[$key]['publisher'][$value['publisher']]['ltv_forecast_a'];
                $totalLtvForecastAUSD += $adnetNew[$key]['publisher'][$value['publisher']]['ltv_forecast_a_usd'];
                $totalLtvForecastC += $adnetNew[$key]['publisher'][$value['publisher']]['ltv_forecast_c'];
                $totalLtvForecastCUSD += $adnetNew[$key]['publisher'][$value['publisher']]['ltv_forecast_c_usd'];
                $totalLTVActual += $adnetNew[$key]['publisher'][$value['publisher']]['actual_ltv'];
                $totalLTVActualUsd += $adnetNew[$key]['publisher'][$value['publisher']]['actual_ltv_usd'];
                $totalSubs += $adnetNew[$key]['publisher'][$value['publisher']]['acquired_subs_count'];
                $totalAmount += $adnetNew[$key]['publisher'][$value['publisher']]['amount_sum'];
                $totalSubsActive += $adnetNew[$key]['publisher'][$value['publisher']]['retained_subs_count'];
                $totalChurn += $adnetNew[$key]['publisher'][$value['publisher']]['churn'];
                $totalArpu += $adnetNew[$key]['publisher'][$value['publisher']]['arpu'];
                $totalChurn30 += $adnetNew[$key]['publisher'][$value['publisher']]['churn_30'];
                $totalArpu30 += $adnetNew[$key]['publisher'][$value['publisher']]['arpu_30'];
                $totalArpu60 += $adnetNew[$key]['publisher'][$value['publisher']]['arpu_60'];
                $totalArpu90 += $adnetNew[$key]['publisher'][$value['publisher']]['arpu_90'];
                $totalArpuUsd += $adnetNew[$key]['publisher'][$value['publisher']]['arpu_usd'];
                $totalArpuUsd30 += $adnetNew[$key]['publisher'][$value['publisher']]['arpu_usd_30'];
                $totalArpuUsd60 += $adnetNew[$key]['publisher'][$value['publisher']]['arpu_usd_60'];
                $totalArpuUsd90 += $adnetNew[$key]['publisher'][$value['publisher']]['arpu_usd_90'];
                $publisher = $value['publisher'];
            }
            // $adnetNew[$key]['adnet']['cost_campaign'] = $costCampaignAdnet;
            $adnetNew[$key]['adnet']['acquired_subs_count'] = $totalSubs;
            $adnetNew[$key]['adnet']['cost_campaign'] = $costCampaignAdnet;

            // search cac
            $adnetNew[$key]['adnet']['cac'] = $costCampaignAdnet / $totalSubs ;

            // search roi
            $operator = Operator::with('revenueshare')->find($mappingOperator->operator_id);
            $revenueshare = $operator->revenueshare;

            $operator_revenue_share = (isset($revenueshare) && !empty($revenueshare)) ? (($revenueshare['merchant_revenue_share']) / 100) : 0;
            
            $calculasiRoi = (($totalArpuUsd90 * $operator_revenue_share) /3 );
            if($calculasiRoi == 0 ) {
                $roi = 0 ;
                
            }else {
                $roi = $adnetNew[$key]['adnet']['cac'] / $calculasiRoi ;
            }



            $adnetNew[$key]['adnet']['roi'] = $roi;
            $eMarginLtvA = ($totalLtvForecastAUSD * $operator_revenue_share) - $adnetNew[$key]['adnet']['cac'];
            $eMarginLtvC =  ($totalLtvForecastCUSD * $operator_revenue_share)  - $adnetNew[$key]['adnet']['cac'];
            $eMarginLtvB = ($totalLTVUSD * $operator_revenue_share)- $adnetNew[$key]['adnet']['cac'];
            $adnetNew[$key]['adnet']['e_margin_a'] = $eMarginLtvA;
            $adnetNew[$key]['adnet']['e_margin_b'] = $eMarginLtvB;
            $adnetNew[$key]['adnet']['e_margin_c'] = $eMarginLtvC;
            $adnetNew[$key]['adnet']['amount_sum'] = $totalAmount;
            // if ($key == "cad") {
            //     dd($adnetNew[$key]['adnet']['amount_sum']);
            // }
            
            $adnetNew[$key]['adnet']['retained_subs_count'] = $totalSubsActive;
            $adnetNew[$key]['adnet']['arpu'] = $totalArpu;
            $adnetNew[$key]['adnet']['churn'] = $totalChurn;
            $adnetNew[$key]['adnet']['churn_30'] = $totalChurn30;
            $adnetNew[$key]['adnet']['arpu_30'] = $totalArpu30;
            $adnetNew[$key]['adnet']['arpu_60'] = $totalArpu60;
            $adnetNew[$key]['adnet']['arpu_90'] = $totalArpu90;
            $adnetNew[$key]['adnet']['arpu_usd'] = $totalArpuUsd;
            $adnetNew[$key]['adnet']['arpu_usd_30'] = $totalArpuUsd30;
            $adnetNew[$key]['adnet']['arpu_usd_60'] = $totalArpuUsd60;
            $adnetNew[$key]['adnet']['arpu_usd_90'] = $totalArpuUsd90;
            $adnetNew[$key]['adnet']['publisher_name'] = $publisher;
            $adnetNew[$key]['adnet']['ltv_forecast'] = $totalLTV;
            $adnetNew[$key]['adnet']['ltv_forecast_usd'] = $totalLTVUSD;
            $adnetNew[$key]['adnet']['ltv_forecast_a'] = $totalLtvForecastA;
            $adnetNew[$key]['adnet']['ltv_forecast_a_usd'] = $totalLtvForecastAUSD;
            $adnetNew[$key]['adnet']['ltv_forecast_c'] = $totalLtvForecastC;
            $adnetNew[$key]['adnet']['ltv_forecast_c_usd'] = $totalLtvForecastCUSD;
            $adnetNew[$key]['adnet']['adnet'] = $key;
            $adnetNew[$key]['adnet']['actual_ltv'] = $totalLTVActual;
            $adnetNew[$key]['adnet']['actual_ltv_usd'] = $totalLTVActualUsd;
        }

        // dd($adnetNew);
        $sortedData = $this->sortDataByAmountSum($adnetNew, $order);

        $paramsQuery = [
            "country" => $country,
            "operator" => $operator,
            "service" => $service,
            "cycle" => $cycle,
            "from" => $from,
            "to" => $to,
            "renewal" => $renewal
        ];
        return [
            "params" => $paramsQuery,
            "sorted_data" => $sortedData,
            "is_publisher" => $is_publisher
        ];
    }
    function sortDataByAmountSum($array, $order)
    {
        if ($order == "Highest Amount Sum") {
            $compareAmountSum = function ($a, $b) {
                return $b['adnet']['amount_sum'] - $a['adnet']['amount_sum'];
            };
        } else if ($order == "Lowest Amount Sum") {
            $compareAmountSum = function ($a, $b) {
                return $a['adnet']['amount_sum'] - $b['adnet']['amount_sum'];
            };
        } else if ($order == "Lowest Subs") {
            $compareAmountSum = function ($a, $b) {
                return $a['adnet']['acquired_subs_count'] - $b['adnet']['acquired_subs_count'];
            };
        } else if ($order == "Highest Subs") {
            $compareAmountSum = function ($a, $b) {
                return $b['adnet']['acquired_subs_count'] - $a['adnet']['acquired_subs_count'];
            };
        } else if ($order == "Lowest Arpu") {
            $compareAmountSum = function ($a, $b) {
                return $a['adnet']['arpu'] - $b['adnet']['arpu'];
            };
        } else if ($order == "Highest Arpu") {
            $compareAmountSum = function ($a, $b) {
                return $b['adnet']['arpu'] - $a['adnet']['arpu'];
            };
        } else if ($order == "Lowest Arpu 30 days") {
            $compareAmountSum = function ($a, $b) {
                return $a['adnet']['arpu_30'] - $b['adnet']['arpu_30'];
            };
        } else if ($order == "Highest Arpu 30 days") {
            $compareAmountSum = function ($a, $b) {
                return $b['adnet']['arpu_30'] - $a['adnet']['arpu_30'];
            };
        } else if ($order == "Lowest Arpu 60 days") {
            $compareAmountSum = function ($a, $b) {
                return $a['adnet']['arpu_60'] - $b['adnet']['arpu_60'];
            };
        } else if ($order == "Highest Arpu 60 days") {
            $compareAmountSum = function ($a, $b) {
                return $b['adnet']['arpu_60'] - $a['adnet']['arpu_60'];
            };
        } else if ($order == "Lowest Arpu 90 days") {
            $compareAmountSum = function ($a, $b) {
                return $a['adnet']['arpu_90'] - $b['adnet']['arpu_90'];
            };
        } else if ($order == "Highest Arpu 90 days") {
            $compareAmountSum = function ($a, $b) {
                return $b['adnet']['arpu_90'] - $a['adnet']['arpu_90'];
            };
        }else if ($order == "Highest Actual Ltv") {
            $compareAmountSum = function ($a, $b) {
                return $b['adnet']['actual_ltv'] - $a['adnet']['actual_ltv'];
            };
        }else if ($order == "Lowest Actual Ltv") {
            $compareAmountSum = function ($a, $b) {
                return $a['adnet']['actual_ltv'] - $b['adnet']['actual_ltv'];
            };
        }else if ($order == "Highest Estimating Ltv A") {
            $compareAmountSum = function ($a, $b) {
                return $b['adnet']['ltv_forecast_a'] - $a['adnet']['ltv_forecast_a'];
            };
        }else if ($order == "Lowest Estimating Ltv A") {
            $compareAmountSum = function ($a, $b) {
                return $a['adnet']['ltv_forecast_a'] - $b['adnet']['ltv_forecast_a'];
            };
        }else if ($order == "Highest Estimating Ltv B") {
            $compareAmountSum = function ($a, $b) {
                return $b['adnet']['ltv_forecast'] - $a['adnet']['ltv_forecast'];
            };
        }else if ($order == "Lowest Estimating Ltv B") {
            $compareAmountSum = function ($a, $b) {
                return $a['adnet']['ltv_forecast'] - $b['adnet']['ltv_forecast'];
            };
        }else if ($order == "Highest Estimating Ltv C") {
            $compareAmountSum = function ($a, $b) {
                return $b['adnet']['ltv_forecast_c'] - $a['adnet']['ltv_forecast_c'];
            };
        }else if ($order == "Lowest Estimating Ltv C") {
            $compareAmountSum = function ($a, $b) {
                return $a['adnet']['ltv_forecast_c'] - $b['adnet']['ltv_forecast_c'];
            };
        }
       
        usort($array, $compareAmountSum);

        return $array;
    }

    public function  sortPayloadByAmountSum($array, $order)
    {

        if ($order == "Lowest Subs") {
            $compareAmountSum = function ($a, $b) {
                return $a['acquired_subs_count'] - $b['acquired_subs_count'];
            };
        } else if ($order == "Highest Subs") {
            $compareAmountSum = function ($a, $b) {
                return $b['acquired_subs_count'] - $a['acquired_subs_count'];
            };
        }

        // Loop through each entry and sort the "pubid" array
        foreach ($array as &$entry) {
            usort($entry, $compareAmountSum);
        }

        uasort($array, $compareAmountSum);

        return $array;
    }


    public function detailSubs(Request $request){
        try {
            $url = Configuration::where('key', 'middleware_url_api_v1')->first()->value;
            $utility = new Utility;

            if($request->country == "ID") {
                $service = strtoupper($request->service);
            }else {
                
                $service = $request->service;
            } 
            $operator = MappingOperator::where("operator", $request->operator)->first()->mapping_operator;
            $urlFinal = $url . "subscription/list-subs?country=" . $request->country . "&" . "operator=" . $operator . "&service=" . $service . "&cycle=". $request->cycle ."&publisher=" . $request->publisher . "&from=" .$request->from ."&to=" . $request->to . "&adnet=" .$request->adnet ;
            $result = $utility->GetResponseFromUrlMiddleware($urlFinal);
            $adnet = $request->adnet;
            $from = $request->from;
            $renewal = $request->renewal;
            return view('report.adnetreport-detail', compact('adnet', 'from', 'renewal','result'));
            
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }
    public function cstool(Request $request){
        try {

            $url = Configuration::where('key', 'middleware_url_api_v1')->first()->value;
            $utility = new Utility;
            $urlFinal = $url . "cs-tools?msisdn=" . $request->msisdn . "&" . "country=" . strtolower($request->country)."&start_date=".$request->from . "&to_date=". $request->renewal;
            $result = $utility->GetResponseFromUrlMiddleware($urlFinal);
            if(isset($result['error_timeout']) && $result['error_timeout'] ==1) {
                return back()->with('error', "The API provider has a problem.");
            }
            $listSource = [];
            $listEvent = [];
            $listPublisher = [];
            $listHandset = [];
            $listBrowser = [];
            $totalRevenue = 0;
            foreach($result['data']['subs'] as $sub) {
                $revenue = $sub['revenue'] ?? 0;
                $totalRevenue += $revenue;
                if (!in_array($sub['subs_source'], $listSource)) {
                    $listSource[] = $sub['subs_source'];
                }
                if (!in_array($sub['type'], $listEvent)) {
                    $listEvent[] = $sub['type'];
                }
                if (!in_array($sub['adnet'], $listPublisher)) {
                    $listPublisher[] = $sub['adnet'];
                }
                if (!in_array($sub['handset'], $listHandset)) {
                    $listHandset[] = $sub['handset'];
                }
                if (!in_array($sub['browser'], $listBrowser)) {
                    $listBrowser[] = $sub['browser'];
                }

            }
            $msisdn = $request->msisdn;
            $country = Country::where('country_code', $request->country)->first()->id;
            $currency = Country::where('id', $country)->first()->currency_code;
            return view('report.adnetreport-cstool', compact('country','currency','msisdn','result', 'listSource', 'listEvent', 'listPublisher', 'listHandset', 'listBrowser', 'totalRevenue'));
            //code...
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function mappingoperator(Request $request) {
        $mappingOperators = MappingOperator::where("country_id", $request->id)->get();
        $country = Country::where("id", $request->id)->first();
        $exampleNumber = CountryArpu::where('country', $country->country)->first()->example_number;
        return response()->json(['data' => $mappingOperators , 'exampleNumber' => $exampleNumber]);
    }
    public function mappingservice(Request $request) {
        $mappingServices = MappingService::where("operator_id", $request->id)->where('is_parent', 1)->get();
        return response()->json(['data' => $mappingServices ]);
    }
    public function mappingkeyword(Request $request) {
        $mappingServices = MappingService::where("operator_id", $request->id)->where('id_parent', $request->id_service)->orderBy('order', 'desc')->get();
        return response()->json(['data' => $mappingServices ]);
    }
}
