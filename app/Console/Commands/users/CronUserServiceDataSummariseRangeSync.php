<?php

namespace App\Console\Commands\users;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\UsersOperatorsServices;
use App\Models\ReportSummeriseUsers;
use App\Models\ServiceHistory;
use App\Models\CronLog;
use DateTime;
use Carbon\CarbonPeriod;

class CronUserServiceDataSummariseRangeSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CronUserServiceDataSummariseRangeSync {--uid=} {--sdate=} {--edate=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $rangeStartDate = $this->option('sdate');
        $rangeEndDate = $this->option('edate');
        $description = "Start Date : ".$rangeStartDate . " End Date : ".$rangeEndDate;
       
        $period = CarbonPeriod::create($rangeStartDate,$rangeEndDate);
        $uid = $this->option('uid');

        $notAllowuserTypes = array("Owner","Super Admin","Business Manager","Admin","BOD");

        $UserQuery = User::Types($notAllowuserTypes)->Active();

        if(isset($uid))
        {
            $UserQuery = $UserQuery->Uid($uid);
        }

        $users = $UserQuery->pluck('id');

        // print_r($users);

        if(count($users) == 0)
        {
            echo "\n\r System not found user for specific user id :".$uid;

            dd("Stop");
        }

        $operators = UsersOperatorsServices::GetOperaterServiceByUserIdIn($users)->get()->groupBy('user_id');

        $cron_start_date = new DateTime('now');
        $status = 'Processing';
        // $description = 'The Value calculate from report_summerise_users table each User  sum of mt success Then Save on Table Thats Means Organize Data - Inpute date from keyboard';
        $data = ['description' => $description,'signature' => $this->signature,'command' => 'php artisan '.$this->signature,'date' => $rangeStartDate,'cron_start_date' => $cron_start_date->format('Y-m-d H:i:s'),'cron_end_date' => $cron_start_date->format('Y-m-d H:i:s'),'total_in_up' => 0,'table_name' => 'report_summerise_users','status' => $status];

        CronLog::upsert($data,['signature','date'],['description','command','cron_start_date','cron_end_date','total_in_up','table_name','status']);
        $totalrecords = 0;
        $total_dates = "";
        $mofiphone = array();
        foreach ($period as $date) {
            $totalUserRecords = 0;
            $date = $date->format('Y-m-d');

            foreach($operators as $Uasrkey=>$operator){
                $Operators = $operator->groupBy('id_operator');

                $serviceSummarise = array();
                $records = 0;

                if(!empty($Operators))
                {
                    foreach ($Operators as $key => $Operator)
                    {
                        $services = $Operator->pluck('id_service');
                        // $operatorDetails=$Operator->toArray();
                        $summerisetemp = array();
                        foreach ($Operator as $OperatorDetails)
                        {
                            $operator_id = $key;
                            $operator_name = $OperatorDetails->operator->operator_name;
                            $country_id = $OperatorDetails->operator->country_id;
                            break;
                        }

                        $summerisetemp['operator_id'] = $operator_id;
                        $summerisetemp['user_id'] = $Uasrkey;
                        $summerisetemp['operator_name'] = $operator_name;
                        $summerisetemp['country_id'] = $country_id;
                        $summerisetemp['date'] = $date;
                        $summerisetemp['fmt_success'] = 0;
                        $summerisetemp['fmt_failed'] = 0;
                        $summerisetemp['mt_success'] = 0;
                        $summerisetemp['mt_failed'] = 0;
                        $summerisetemp['gros_rev'] = 0;
                        $summerisetemp['total_reg'] = 0;
                        $summerisetemp['total_unreg'] = 0;
                        $summerisetemp['total'] = 0;
                        $summerisetemp['purge_total'] = 0;
                        $summerisetemp['currency'] = "";

                        if($operator_id == 29)
                        {
                            echo " Operator Id 29 : user ".$Uasrkey ." : ";

                            $service_historys = ServiceHistory::FilterOperator($operator_id)->filterService($services)->filterDate($date)->get();
    
                            if(!empty($service_historys))
                            {
                                $fmt_success = 0;
                                $fmt_failed = 0;
                                $mt_success = 0;
                                $mt_failed = 0;
                                $gros_rev = 0;
                                $total_reg = 0;
                                $total_unreg = 0;
                                $total = 0;
                                $purge_total = 0;

                                foreach ($service_historys as $key => $services)
                                {
                                    $id_service = $services->id_service;
                                    $temp_mt_success = $services->mt_success;
                                    $temp_gros_rev = $services->gros_rev;
               
                                    if($id_service == 466){
                                        $temp_gros_rev = $temp_mt_success * 3000;
                                    }else if($id_service == 698){
                                        $temp_gros_rev = $temp_mt_success * 5000;
                                    }
               
                                    $fmt_success = $fmt_success + $services->fmt_success;
                                    $fmt_failed = $fmt_failed + $services->fmt_failed;
                                    $mt_success = $mt_success + $services->mt_success;
                                    $mt_failed = $mt_failed + $services->mt_failed;
                                    $gros_rev = $gros_rev + $temp_gros_rev;
                                    $total_reg = $total_reg + $services->total_reg;
                                    $total_unreg = $total_unreg + $services->total_unreg;
                                    $total = $total + $services->total;
                                    $purge_total = $purge_total + $services->purge_total;
                                }

                                $summerisetemp['fmt_success'] = $fmt_success;
                                $summerisetemp['fmt_failed'] = $fmt_failed;
                                $summerisetemp['mt_success'] = $mt_success;
                                $summerisetemp['mt_failed'] = $mt_failed;
                                $summerisetemp['gros_rev'] = $gros_rev;
                                $summerisetemp['total_reg'] = $total_reg;
                                $summerisetemp['total_unreg'] = $total_unreg;
                                $summerisetemp['total'] = $total;
                                $summerisetemp['purge_total'] = $purge_total;

                                $mofiphone[] = $summerisetemp;
                            }
                        }
                        else
                        {
                            $service_data = ServiceHistory::SumByDateServiceData($operator_id,$services,$date);

                            $serviceresult = $service_data->get();

                            if(!empty($serviceresult))
                            {
                                foreach ($serviceresult as $key => $serviceData)
                                {
                                    $summerisetemp['fmt_success'] = $serviceData->total_fmt_success;
                                    $summerisetemp['fmt_failed'] = $serviceData->total_fmt_failed;
                                    $summerisetemp['mt_success'] = $serviceData->total_mt_success;
                                    $summerisetemp['mt_failed'] = $serviceData->total_mt_failed;
                                    $summerisetemp['total_reg'] = $serviceData->total_total_reg;
                                    $summerisetemp['total_unreg'] = $serviceData->total_total_unreg;
                                    $summerisetemp['total'] = $serviceData->total_total;
                                    $summerisetemp['purge_total'] = $serviceData->total_purge_total;

                                    if ($country_id == 142) {
                                        $summerisetemp['gros_rev'] = $serviceData->total_gros_rev / 1000;
                                    }else{
                                        $summerisetemp['gros_rev'] = $serviceData->total_gros_rev;
                                    }

                                    $records++;
                                }
                            }
                        }

                        $serviceSummarise[] = $summerisetemp;
                    }

                    if(sizeof($serviceSummarise)>0)
                    {
                        /* Update structure DB Table : ALTER TABLE `report_summarize` ADD UNIQUE `SummariseData` (`operator_id`, `date`);*/

                        ReportSummeriseUsers::upsert($serviceSummarise,['operator_id','user_id','date'],['operator_name','country_id','fmt_success','fmt_failed','mt_success','mt_failed','gros_rev','total_reg','total_unreg','total','purge_total','currency']);

                        $status = 'Success';
                    }
                    // dd($user->toArray());
                    print_r(sizeof($serviceSummarise)." Records Insert/Updated for user ".$Uasrkey."\n");
                    $totalUserRecords += sizeof($serviceSummarise); 
                }
            }

            $totalrecords += $totalUserRecords;
            print_r($date." : ".$totalUserRecords."  Records Insert/Updated  \n");
        }

        $cron_end_date = new DateTime('now');
        $data = ['description' => $description,'signature' => $this->signature,'command' => 'php artisan '.$this->signature,'date' => $rangeStartDate,'cron_start_date' => $cron_start_date->format('Y-m-d H:i:s'),'cron_end_date' => $cron_end_date->format('Y-m-d H:i:s'),'total_in_up' => $totalrecords ,'table_name' => 'report_summerise_users','status'=> $status];

        CronLog::upsert($data,['signature','date'],['description','command','cron_start_date','cron_end_date','total_in_up','table_name','status']);
        
        return 0;
    }
}
