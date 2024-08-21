<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ReportsPnls;
class WakicallbackController extends Controller
{
    public function actionGetwakidata(Request $request)
    {
        if(isset($request))
        {
            $param               = $request->get('date');
            $date                = date('Y-m-d 00:00:00', strtotime($param));
            $country             = $request->get('country');
            $operator            = $request->get('operator');
            $service             = $request->get('service');
            $publisher           = $request->get('adnet');
            $mo_received         = $request->get('total_mo');
            $mo_postback         = $request->get('total_postback');
            $payout              = $request->get('payout');
            $saaf                = $request->get('spending');
            $price_per_mo        = $request->get('price_per_mo');
            $and                 = $request->get('adn');
            $landing             = $request->get('landing');
            $cr_mo_received      = $request->get('cr_mo_received');
            $cr_mo_postback      = $request->get('cr_mo_postback');
            $url_campaign        = $request->get('url_campaign');
            $url_service         = $request->get('url_service');
            $client              = $request->get('client');
            $aggregator          = $request->get('aggregator');
            $sbaf                = $request->get('sbaf');

            $url_campaign = ($url_campaign != null) ? $url_campaign : '';
            $url_service = ($url_service != null) ? $url_service : '';

            if(empty($operator) || $operator == '-'){
               return [
                    'status' => 'Error',
                    'code' => 404,
                    'message' => 'No operator found'
                ];
            }elseif (empty($client) || $client == '-') {
                return [
                    'status' => 'Error',
                    'code' => 404,
                    'message' => 'No client found'
                ];
            }elseif (empty($service) || $service == '-') {
                return [
                    'status' => 'Error',
                    'code' => 404,
                    'message' => 'No service found'
                ];
            }elseif (empty($publisher) || $publisher == '-') {
                return [
                    'status' => 'Error',
                    'code' => 404,
                    'message' => 'No publisher found'
                ];
            }elseif (empty($country) || $country == '-') {
                return [
                    'status' => 'Error',
                    'code' => 404,
                    'message' => 'No country found'
                ];
            }else {
                if($operator == 'TSEL-TELESAT'){
                    $operator = strtolower($client.'-'.$operator);
                }

                if(($operator == 'ais' || $operator == 'AIS' || $operator == 'Ais') && $client == 'CM'){
                    $operator = 'th-ais-old';
                }

                if(($operator == 'jazz' || $operator == 'Jazz' || $operator == 'JAZZ') && $client == 'Evolve'){
                    $operator = 'jazz-ev';
                }

                if(($operator == 'OMN - omantel - linkit' || $operator == 'omantel') && ($client == 'linkit' || $client == 'Linkit' || $client = 'LINKIT')){
                    $operator = 'omn-omantel-linkit';
                }

                if(($operator == 'telkomsel') && ($client == 'Pgu')){
                    $operator = 'id-telkomsel-pgu';
                }

                $all_data = ReportsPnls::GetRecordByDate($date)
                ->GetRecordByOperator($operator)
                ->GetRecordByService($service)
                ->GetRecordByPublisher($publisher)
                ->GetRecordByCountry($country)
                ->GetRecordByUrlCampaign($url_campaign)
                ->GetRecordByUrlService($url_service)
                ->get()
                ->toArray();

                $query = (isset($all_data) && !empty($all_data)) ? reset($all_data) : [];
                
                if(isset($query) && !empty($query)){
                    $query['mo_received']     = (isset($mo_received) && !empty($mo_received)) ? $mo_received  : 0;
                    $query['mo_postback']     = (isset($mo_postback) && !empty($mo_postback)) ? $mo_postback : 0;
                    $query['payout']          = (isset($payout) && !empty($payout)) ? $payout : 0;
                    $query['saaf']            = (isset($saaf) && !empty($saaf)) ? $saaf : 0;
                    $query['price_per_mo']    = (isset($price_per_mo) && !empty($price_per_mo)) ? $price_per_mo : 0;
                    $query['and']             = (isset($and) && !empty($and)) ? $and : 0;
                    $query['landing']         = (isset($landing) && !empty($landing)) ? $landing : 0;
                    $query['cr_mo_received']  = (isset($cr_mo_received) && !empty($cr_mo_received)) ? $cr_mo_received : 0;
                    $query['cr_mo_postback']  = (isset($cr_mo_postback) && !empty($cr_mo_postback)) ? $cr_mo_postback : 0;
                    $query['url_campaign']    = (isset($url_campaign) && !empty($url_campaign)) ? $url_campaign : '';
                    $query['url_service']     = (isset($url_service) && !empty($url_service)) ? $url_service : '';
                    $query['client']          = (isset($client) && !empty($client)) ? $client : '';
                    $query['aggregator']      = (isset($aggregator) && !empty($aggregator)) ? $aggregator : '';
                    $query['sbaf']            = (isset($sbaf) && !empty($sbaf)) ? $sbaf : 0;
                    $query['updated_at']      = date('Y-m-d H:i:s');
                    $query['created_at']      = date('Y-m-d H:i:s');
                    
                    $sql = ReportsPnls::where('id','=', $query['id'])->update($query);

                    if(isset($sql) && !empty($sql))
                    {
                        $data = [
                            'date'             => $date,
                            'publisher'        => (isset($publisher) && !empty($publisher)) ? $publisher : '',
                            'operator'         => (isset($operator) && !empty($operator)) ? $operator : '',
                            'service'          => (isset($service) && !empty($service)) ? $service : '',
                            'and'              => (isset($and) && !empty($and)) ? $and : 0,
                            'mo_received'      => (isset($mo_received) && !empty($mo_received)) ? $mo_received  : 0,
                            'mo_postback'      => (isset($mo_postback) && !empty($mo_postback)) ? $mo_postback : 0,
                            'landing'          => (isset($landing) && !empty($landing)) ? $landing : 0,
                            'cr_mo_received'   => (isset($cr_mo_received) && !empty($cr_mo_received)) ? $cr_mo_received : 0,
                            'cr_mo_postback'   => (isset($cr_mo_postback) && !empty($cr_mo_postback)) ? $cr_mo_postback : 0,
                            'url_campaign'     => (isset($url_campaign) && !empty($url_campaign)) ? $url_campaign : '',
                            'url_service'      => (isset($url_service) && !empty($url_service)) ? $url_service : '',
                            'client'           => (isset($client) && !empty($client)) ? $client : '',
                            'aggregator'       => (isset($aggregator) && !empty($aggregator)) ? $aggregator : '',
                            'country'          => (isset($country) && !empty($country)) ? $country : '',
                            'sbaf'             => (isset($sbaf) && !empty($sbaf)) ? $sbaf : 0,
                            'saaf'             => (isset($saaf) && !empty($saaf)) ? $saaf : 0,
                            'payout'           => (isset($payout) && !empty($payout)) ? $payout : 0,
                            'price_per_mo'     => (isset($price_per_mo) && !empty($price_per_mo)) ? $price_per_mo : 0,
                            'status' => 'updated'
                        ];

                        return [
                            'status' => 'Success',
                            'message' => 'Data successfully updated',
                            'data' => $data
                        ];
                    }
                    else
                    {
                        return [
                            'status' => 'Failed',
                            'message' => 'Data updation failed'
                        ];
                    }
                } else {
                    $pnl                    = [];
                    $pnl['date']            = $date;
                    $pnl['country']         = (isset($country) && !empty($country)) ? $country : '';
                    $pnl['operator']        = ((isset($operator) && !empty($operator)) ? $operator : '');
                    $pnl['service']         = (isset($service) && !empty($service)) ? $service : '';
                    $pnl['publisher']       = (isset($publisher) && !empty($publisher)) ? $publisher : '';
                    $pnl['mo_received']     = (isset($mo_received) && !empty($mo_received)) ? $mo_received  : 0;
                    $pnl['mo_postback']     = (isset($mo_postback) && !empty($mo_postback)) ? $mo_postback : 0;
                    $pnl['payout']          = (isset($payout) && !empty($payout)) ? $payout : 0;
                    $pnl['saaf']            = (isset($saaf) && !empty($saaf)) ? $saaf : 0;
                    $pnl['price_per_mo']    = (isset($price_per_mo) && !empty($price_per_mo)) ? $price_per_mo : 0;
                    $pnl['and']             = (isset($and) && !empty($and)) ? $and : 0;
                    $pnl['landing']         = (isset($landing) && !empty($landing)) ? $landing : 0;
                    $pnl['cr_mo_received']  = (isset($cr_mo_received) && !empty($cr_mo_received)) ? $cr_mo_received : 0;
                    $pnl['cr_mo_postback']  = (isset($cr_mo_postback) && !empty($cr_mo_postback)) ? $cr_mo_postback : 0;
                    $pnl['url_campaign']    = (isset($url_campaign) && !empty($url_campaign)) ? $url_campaign : '';
                    $pnl['url_service']     = (isset($url_service) && !empty($url_service)) ? $url_service : '';
                    $pnl['client']          = (isset($client) && !empty($client)) ? $client : '';
                    $pnl['aggregator']      = (isset($aggregator) && !empty($aggregator)) ? $aggregator : '';
                    $pnl['sbaf']            = (isset($sbaf) && !empty($sbaf)) ? $sbaf : 0;
                    
                    $sql = ReportsPnls::insert($pnl);

                    if($sql)
                    {
                        $data = [
                            'date'             => $date,
                            'publisher'        => (isset($publisher) && !empty($publisher)) ? $publisher : '',
                            'operator'         => (isset($operator) && !empty($operator)) ? $operator : '',
                            'service'          => (isset($service) && !empty($service)) ? $service : '',
                            'and'              => (isset($and) && !empty($and)) ? $and : 0,
                            'mo_received'      => (isset($mo_received) && !empty($mo_received)) ? $mo_received  : 0,
                            'mo_postback'      => (isset($mo_postback) && !empty($mo_postback)) ? $mo_postback : 0,
                            'landing'          => (isset($landing) && !empty($landing)) ? $landing : 0,
                            'cr_mo_received'   => (isset($cr_mo_received) && !empty($cr_mo_received)) ? $cr_mo_received : 0,
                            'cr_mo_postback'   => (isset($cr_mo_postback) && !empty($cr_mo_postback)) ? $cr_mo_postback : 0,
                            'url_campaign'     => (isset($url_campaign) && !empty($url_campaign)) ? $url_campaign : '',
                            'url_service'      => (isset($url_service) && !empty($url_service)) ? $url_service : '',
                            'client'           => (isset($client) && !empty($client)) ? $client : '',
                            'aggregator'       => (isset($aggregator) && !empty($aggregator)) ? $aggregator : '',
                            'country'          => (isset($country) && !empty($country)) ? $country : '',
                            'sbaf'             => (isset($sbaf) && !empty($sbaf)) ? $sbaf : 0,
                            'saaf'             => (isset($saaf) && !empty($saaf)) ? $saaf : 0,
                            'payout'           => (isset($payout) && !empty($payout)) ? $payout : 0,
                            'price_per_mo'     => (isset($price_per_mo) && !empty($price_per_mo)) ? $price_per_mo : 0,
                            'status' => 'inserted'
                        ];

                        return [
                            'status' => 'Success',
                            'message' => 'Data successfully inserted',
                            'data' => $data
                        ];
                    }
                    else
                    {
                        return [
                            'status' => 'Failed',
                            'message' => 'Data insertion failed'
                        ];
                    }
                }
            }
        } else {
            return [
                'status' => 'Error',
                'message' => 'No data found'
            ];
        }
    }
}
