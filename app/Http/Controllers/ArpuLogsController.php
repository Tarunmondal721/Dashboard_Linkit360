<?php

namespace App\Http\Controllers;

use App\common\Utility;
use App\Models\Configuration;
use App\Models\Country;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;

class ArpuLogsController extends Controller
{
    //
    public function summary(Request $request)
    {
        if (\Auth::user()->can('Summary Arpu')) {
            $utility = new Utility;
            $url = Configuration::where('key', 'middleware_url_api')->first()->value;
            $operators = [];

            $filterFrom = $request->from_date ?? null;
            $filterTo = $request->to_date ?? null;
            if($filterTo == null) {
                $filterTo = Carbon::now();
            }
            // $arrayCountries = ['LA', 'PH', 'TH', 'SN', 'OM', 'ID', "JO", "LK", "BH"];
            $arrayCountries = ['Laos', 'Philippines', 'Thailand', "Senegal",'Oman', 'Indonesia', "Jordan", "Sri Lanka", "Pakistan", "Bahrain", "Ethiopia", 'Iraq'];
            $dataMapping = [];
    
            $totalTransactionAllCountry = 0;
            $totalSubsriptionAllCountry = 0;
            foreach ($arrayCountries as $country) {
                if ($request->filled("country")) {
                    // dd("tes");
                    if ($request->country == $country) {
                        if ($country == "Philippines") {
                            $operators = ["smart"];
                        } else if ($country == "Laos") {
                            $operators = ["tplus", "ltc", "etl"];
                        } else if ($country == "Thailand") {
                            $operators = ["ais", "aisgemezz", "dtac"];
                        } else if ($country == "Oman") {
                            $operators = ["omantel", "ooredo"];
                        } else if ($country == "Indonesia") {
                            $operators = ["telkomsel", "id-telkomsel-mks", "telesatpass"];
                        } else if ($country == "Jordan") {
                            $operators = ["umniah"];
                        } else if ($country == "Sri Lanka") {
                            $operators = ["dialog"];
                        } else if ($country == "Bahrain") {
                            $operators = ["batelco", "stc"];
                        } else if ($country == "Senegal") {
                            $operators = ["orange"];
                        }else if ($country == "Pakistan") {
                            $operators = ["jazz"];
                        }else if ($country == "Bahrain") {
                            $operators = ["batelco", "stc"];
                        }else if($country == "Ethiopia") {
                            $operators = ["enlight"];
                        }else if($country == "Iraq") {
                            $operators = ["zain", "korek"];
                        }
                        $codeCountry = Country::where('country', $country)->first()->country_code;
                        if($codeCountry == "IG") {
                            $codeCountry = "iq";
                        }
                        $result = $utility->GetResponseSummary($url . "summary/" . $codeCountry, $filterFrom, $filterTo);
                        $totalTransactions = 0;
                        $totalSubsription = 0;
                        $mappingOperator = [];
                        if(isset($result['status']) && $result['status'] == 500) {
                            continue;
                        }else {
                            foreach ($result['data'] as $data) {
                                if ($request->filled("operatorId")) {
                                    if (in_array($data['operator'], $request->operatorId)) {
                                        $totalSubsription += $data['subscriptions'];
                                        $totalTransactions += $data['transactions'];
        
                                        if (isset($mappingOperator[$data['operator']])) {
                                            $mappingOperator[$data['operator']]['subscriptions'] += $data['subscriptions'];
                                            $mappingOperator[$data['operator']]['transactions'] += $data['transactions'];
                                        } else {
                                            $mappingOperator[$data['operator']] = [
                                                'operator' => $data['operator'],
                                                'service' => $data['service'],
                                                'subscriptions' => $data['subscriptions'],
                                                'transactions' => $data['transactions']
                                            ];
                                        }
                                    }
                                } else {
                                    $totalSubsription += $data['subscriptions'];
                                    $totalTransactions += $data['transactions'];
        
                                    if (isset($mappingOperator[$data['operator']])) {
                                        $mappingOperator[$data['operator']]['subscriptions'] += $data['subscriptions'];
                                        $mappingOperator[$data['operator']]['transactions'] += $data['transactions'];
                                    } else {
                                        $mappingOperator[$data['operator']] = [
                                            'operator' => $data['operator'],
                                            'service' => $data['service'],
                                            'subscriptions' => $data['subscriptions'],
                                            'transactions' => $data['transactions']
                                        ];
                                    }
                                }
                            }
        
                            $totalTransactionAllCountry += $totalTransactions;
                            $totalSubsriptionAllCountry += $totalSubsription;
                            // dd($mappingOperator);
                            $dataMapping[$codeCountry]['country']["country"] = $country;
                            $dataMapping[$codeCountry]['country']["total_transactions"] = $totalTransactions;
                            $dataMapping[$codeCountry]['country']["total_subscriptions"] = $totalSubsription;
                            $dataMapping[$codeCountry]['country']["flags"] = Country::where('country', $country)->first()->flag;
                            $dataMapping[$codeCountry]['operator'] = $mappingOperator;

                        }
                    }
                } else {
                    $codeCountry = Country::where('country', $country)->first()->country_code;
                    if($codeCountry == "IG") {
                        $codeCountry = "iq";
                    }
                    // dd($url . "summary/" . $codeCountry, $filterFrom, $filterTo);

                    $result = $utility->GetResponseSummary($url . "summary/" . $codeCountry,$filterFrom, $filterTo);
                  
                    $totalTransactions = 0;
                    $totalSubsription = 0;
                    $mappingOperator = [];
                    if(isset($result['status']) && $result['status'] == 500) {
                        continue;
                    }else {
                        foreach ($result['data'] as $data) {
                            $totalSubsription += $data['subscriptions'];
                            $totalTransactions += $data['transactions'];
        
                            if (isset($mappingOperator[$data['operator']])) {
                                $mappingOperator[$data['operator']]['subscriptions'] += $data['subscriptions'];
                                $mappingOperator[$data['operator']]['transactions'] += $data['transactions'];
                            } else {
                                $mappingOperator[$data['operator']] = [
                                    'operator' => $data['operator'],
                                    'service' => $data['service'],
                                    'subscriptions' => $data['subscriptions'],
                                    'transactions' => $data['transactions']
                                ];
                            }
                        }
        
                        $totalTransactionAllCountry += $totalTransactions;
                        $totalSubsriptionAllCountry += $totalSubsription;
                        // dd($mappingOperator);
                        $dataMapping[$codeCountry]['country']["country"] = $country;
                        $dataMapping[$codeCountry]['country']["total_transactions"] = $totalTransactions;
                        $dataMapping[$codeCountry]['country']["total_subscriptions"] = $totalSubsription;
                        $dataMapping[$codeCountry]['country']["flags"] = Country::where('country', $country)->first()->flag;
                        $dataMapping[$codeCountry]['operator'] = $mappingOperator;

                    }
                }
            }
    
    
            $summaryData = $dataMapping;
            // dd($summaryData);
    
    
            return view('arpulogs.summary', [
                'summaryData' => $summaryData,
                'totalTransactionAllCountry' => $totalTransactionAllCountry,
                'totalSubsriptionAllCountry' => $totalSubsriptionAllCountry,
                'allCountries' => $arrayCountries,
                'operators' => $operators
            ]);
        }else {
            return redirect()->back()->with('error', __('Permission Denied.'));

        }
    }
    public function detailOperator($country, $operator)
    {
        $utility = new Utility;
        $url = Configuration::where('key', 'middleware_url_api')->first()->value;
        $codeCountry = Country::where('country', $country)->first()->country_code;
        $result = $utility->GetResponseSummary($url . "summary/" . $codeCountry);

        $mappingOperator = [];
        foreach ($result['data'] as $data) {
            if ($data['operator'] == $operator) {
                $mappingOperator[] = [
                    'service' => $data['service'],
                    'subscriptions' => $data['subscriptions'],
                    'transactions' => $data['transactions']
                ];
            }
        }
        return view('arpulogs.detailoperator', [
            'country' => $country,
            'operator' => $operator,
            'mappingOperator' => $mappingOperator
        ]);
    }
}
