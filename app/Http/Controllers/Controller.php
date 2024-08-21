<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    //Get graph data
    public function Getsummarygraphdata(Request $request)
    {
    	$arrData = [];
        $yeardata = [];
        $result = [];
        $number_of_days = date('d');
        if ($request->ajax()) {
            $data = $request->post('graphdata');
            $view = $request->post('yaxes');
            $arrData = $data[$view];
            $response['monthdata'] = $arrData;
            $response['dates'] = (count($arrData) == date('d')) ? array_reverse($this->getDatesTillCurrentDate()) : array_reverse($this->getDatesTillFilterDate(count($arrData)));
            return json_encode($response);
        }
        return false;
    }

    public function Getmixedgraphaxesdata(Request $request)
    {
    	$new_data = [];
        $yeardata = [];
        $result = [];
        $number_of_days = date('d');
        if ($request->ajax()) {
            $data = $request->post('graphdata');
            if(isset($data) && !empty($data)){
            	$new_data['t_subactive'] = (isset($data['t_subactive']) && !empty($data['t_subactive'])) ? $this->getSubactiveReducedData($data['t_subactive']) : [];
            	$new_data['t_renewal'] = (isset($data['t_renewal']) && !empty($data['t_renewal'])) ? $data['t_renewal'] : [];
            }
            $response['monthdata'] = $new_data;
            $response['dates'] = (count($new_data['t_renewal']) == date('d')) ? array_reverse($this->getDatesTillCurrentDate()) : array_reverse($this->getDatesTillFilterDate(count($new_data['t_renewal'])));

            return json_encode($response);
        }
        return false;
    }

    public function Getmixedgraphdata(Request $request)
    {
    	$new_data = [];
        $yeardata = [];
        $result = [];
        $number_of_days = date('d');
        if ($request->ajax()) {
            $data = $request->post('graphdata');
            if(isset($data) && !empty($data)){
            	$new_data['t_reg'] = (isset($data['t_reg']) && !empty($data['t_reg'])) ? $data['t_reg'] : [];
            	$new_data['t_unreg'] = (isset($data['t_unreg']) && !empty($data['t_unreg'])) ? $this->getNegativeData($data['t_unreg']) : [];
            	$new_data['t_purged'] = (isset($data['t_purged']) && !empty($data['t_purged'])) ? $this->getNegativeData($data['t_purged']) : [];
            	$new_data['t_actvsubs'] = $this->getGraphActiveSubs($data);
            }

            $response['monthdata'] = $new_data;
            $response['dates'] = (count($new_data['t_reg']) == date('d')) ? array_reverse($this->getDatesTillCurrentDate()) : array_reverse($this->getDatesTillFilterDate(count($new_data['t_reg'])));


            return json_encode($response);
        }
        return false;
    }

    public function getDatesTillCurrentDate()
    {
        $dates = [];
        for ($i = 1; $i <=  date('d'); $i++) {
            $dates[] = str_pad($i, 2, '0', STR_PAD_LEFT);
        }

        return $dates;
    }

    public function getDatesTillFilterDate($d)
    {
        $dates = [];
        for ($i=1; $i<=$d; $i++) {
            $dates[] = str_pad($i, 2, '0', STR_PAD_LEFT);
        }

        return $dates;
    }

    public function getSubactiveReducedData($data){
    	$result = [];
    	if(isset($data) && !empty($data)){
    		foreach ($data as $dk => $dv) {
    			$dv = str_replace(',','',$dv);
    			$dv = $dv / 1000;
    			$result[] = $dv;
    		}
    	}
    	return $result;
    }

    public function getNegativeData($data){
    	$result = [];

    	if(isset($data) && !empty($data)){
    		foreach ($data as $dk => $dv) {
    			$dv = str_replace(',','',$dv);
    			$val = '-'.$dv;
				$result[] = $val;
    		}
    	}
    	return $result;
    }

    public function getGraphActiveSubs($data){
    	$result = [];
    	if(isset($data) && !empty($data)){
    		if(isset($data['t_reg']) && !empty($data['t_reg'])){
	    		foreach ($data['t_reg'] as $dk => $dv) {
	    			$dv = str_replace(',','',$dv);
	    			$unreg = (isset($data['t_unreg'][$dk])) ? str_replace(',', '', $data['t_unreg'][$dk]) : 0;
	    			$purge = (isset($data['t_purged'][$dk])) ? str_replace(',', '', $data['t_purged'][$dk]) : 0;
	    			$actvSubs = $dv - ($unreg + $purge);
	    			$result[] = $actvSubs;
	    		}
	    	}
    	}
    	return $result;
    }
}
