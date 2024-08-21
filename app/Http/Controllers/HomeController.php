<?php

namespace App\Http\Controllers;

use App\Models\User;
// use App\Models\Utility;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        
        if(Auth::check())
        {

          


            return view('admin.dashboard');
        }
        else
        {
            return redirect()->route('login');
                
        }
    }

    // public function getOrderChart($arrParam)
    // {
    //     $arrDuration = [];
    //     if($arrParam['duration'])
    //     {
    //         if($arrParam['duration'] == 'week')
    //         {
    //             $previous_week = strtotime("-1 week +1 day");
    //             for($i = 0; $i < 7; $i++)
    //             {
    //                 $arrDuration[date('Y-m-d', $previous_week)] = date('D', $previous_week);
    //                 $previous_week                              = strtotime(date('Y-m-d', $previous_week) . " +1 day");
    //             }
    //         }
    //     }

    //     $arrTask          = [];
    //     $arrTask['label'] = [];
    //     $arrTask['data']  = [];

    //     $arrDuration = array_reverse($arrDuration);

    //     foreach($arrDuration as $date => $label)
    //     {
    //         $data               = Order::select(\DB::raw('count(*) as total'))->whereDate('created_at', '=', $date)->first();
    //         $arrTask['label'][] = __($label);
    //         $arrTask['data'][]  = $data->total;
    //     }

    //     return $arrTask;
    // }
}
