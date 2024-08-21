<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Session;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\CronLog;
// use Request;
use Response;
use File;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;


class LogFileController extends Controller
{
    public function pageLoad()
    {
        if(isset($_GET['date'])){
            echo $_GET['date'];
        }
        return view('report.index');
    }

    public function dataUpdate()
    {
        if(isset($_GET['date'])){
            echo $_GET['date'];
        }
        return view('report.index');
    }

    public function query()
    {
        if(isset($_GET['date'])){
            echo $_GET['date'];
        }
        return view('report.index');
    }

    public function currencyExchange()
    {
        if(isset($_GET['date'])){
            echo $_GET['date'];
        }
        return view('report.index');
    }
    public function cron(Request $request){
        if(\Auth::user()->can('Cron Log'))
        {
            $data = $request->all();
            $log_signatre = !empty($data['log_signatre']) ? explode(" ",$data['log_signatre']) : [];
            $signatre = isset($log_signatre[0]) ? trim($log_signatre[0]) : '';
            if(isset($_GET['date']) && isset($_GET['log_signatre']) && isset($_GET['date_for']) ){
                $date_for= isset($_GET['date_for']) ? ($_GET['date_for']) : '';
                $siganture = isset($_GET['log_signatre']) ? ($_GET['log_signatre']) : '';
                $Start_date= isset($_GET['date']) ? ($_GET['date']) : '';

                $query = CronLog::query();
                // if (Request::has('date_for')) {
                //     dd(2342);
                // }
                if (!empty($date_for)) {
                    $query = $query->where('date', '=', $data['date_for']);
                }
                if (!empty($siganture)) {
                    $query = $query->where('signature','LIKE','%'.$signatre.'%');
                }
                if (!empty($Start_date)) {
                    $query = $query->where('cron_start_date','LIKE','%'.$data['date'].'%');
                }

                $crons = $query->orderBy('created_at', 'DESC')->get();

                // var_dump($crons);

            }
            else{
                $date = Carbon::now()->subDays(1);
                $crons=CronLog::where('cron_start_date','>=',$date)
                ->orderBy('created_at', 'DESC')->get();
                // $crons=CronLog::where('date', '=', date('Y-m-d'))
                // ->orderBy('cron_start_date', 'desc')->get();
                $crons=CronLog::orderBy('created_at', 'DESC')->get();

            }
            // $crons=CronLog::orderBy('cron_start_date', 'desc')->get();
            // dd($crons);
            return view('logs.cron_log_details',compact('crons'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function deleteCron(Request $request)
    {
        if(\Auth::user()->can('Cron Log'))
        {
            $data = $request->all();
            
            if(isset($_GET['date']) && isset($_GET['log_signatre']))
            {    
                $log_signatre = !empty($data['log_signatre']) ? explode(" ",$data['log_signatre']) : [];
                $log_signatre = isset($log_signatre[0]) ? trim($log_signatre[0]) : '';
                $fileName = $log_signatre.'.log';
                $date = isset($data['date'])?explode(" ",$data['date'])[0]:'';
                $folderName = date('Ymd',strtotime($date));
                $file = public_path(). "/storage/logs/cron/".$folderName."/".$fileName;
                if(file_exists($file))
                {
                    unlink($file);
                    return redirect()->back()->with('success', __('File delete successfully'));
                }else{
                    return redirect()->back()->with('error', __('File not exists.'));
                }
            }else{
                return view('logs.cron_delete');
            }
        }
        
    }

    public function downloadCron($path,$folderName)
    {
        $fileName = $path.'.log';
        $file= public_path(). "/storage/logs/cron/".$folderName."/".$fileName;
        if(file_exists($file))
        {
            $headers = array('Content-Type: application/log',);
            return Response::download($file, $fileName, $headers);
        }else{
            return redirect()->back()->with('error', __('File not exists.'));
        }
    }
}
    