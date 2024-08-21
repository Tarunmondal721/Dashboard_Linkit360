<?php

namespace App\Console\Commands\users;

use Illuminate\Console\Command;

use App\Models\report_summarize;
use App\Models\ReportSummeriseUsers;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use App\Models\Country;
use App\Models\CronLog;
use App\Models\MonthlyReportSummery;
use DateTime;
use App\Models\User;
use App\Models\UsersOperatorsServices;

class CronUserMonthlyReportSummery extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CronUserMonthlyReportSummery {--year=}  {--month=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'The Value calculate from Service_history  table each User and sum of this month mt success Then Save in  Table monthly_report_summeries - According to Select Operators';

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
        $start_date = new DateTime('now');
       
        $year = $this->option('year');
        $month = $this->option('month');

        $descriptionCron = " For that Month : ". $year." - " .$month;

        $status = 'Processing';
        $data = ['description' => $descriptionCron,'signature' => $this->signature,'command' => 'php artisan '.$this->signature,'date' => $start_date->format('Y-m-d'),'cron_start_date' => $start_date->format('Y-m-d H:i:s'),'cron_end_date' => $start_date->format('Y-m-d H:i:s'),'total_in_up' => 0,'table_name' => 'monthly_report_summeries','status' => $status];

        CronLog::upsert($data,['signature','date'],['description','command','cron_start_date','cron_end_date','table_name','total_in_up','status']);

        $Country = Country::all()->toArray();

        if(!empty($Country))
        {
            foreach($Country as $CountryI)
            {
                $countries[$CountryI['id']] = $CountryI;
            }
        }

        $MonthReports = array();
        $notAllowuserTypes = array("Owner","Super Admin","Business Manager","Admin","BOD");
        $users = User::Types($notAllowuserTypes)->Active()->pluck('id');
        $UserOperatorServices = UsersOperatorsServices::GetOperaterServiceByUserIdIn($users)->get()->groupBy('user_id');

        $user_id_string = "";
        $total_insert = 0;

        foreach($UserOperatorServices as $Uasrkey => $UserOperatorService){
            $allOperatorMonthData = [];
            
            $user_id_string = $user_id_string ." , " .$Uasrkey;
            
            $UserOperators = $UserOperatorService->groupBy('id_operator');

            if(!empty($UserOperators))
            {
                foreach($UserOperators as $userOperatorId => $oneoperatorservice)
                {
                    $monthdata = array();
                    $temp = array();

                    $country_id  = $oneoperatorservice[0]->operator->country_id;

                    $contain_id = Arr::exists($countries, $country_id);

                    $temp['country_id'] = 0;

                    if($contain_id )
                    {
                        $temp['country_id'] = $countries[$country_id]['id'];
                    }

                    $key = $year."-".$month;

                    $temp['user_id'] = $Uasrkey;
                    $temp['year'] = $year;
                    $temp['month'] = $month;
                    $temp['key'] = $key;
                    
                    $monthdata = ReportSummeriseUsers::User($Uasrkey)->filteroperatorID($userOperatorId)
                        ->filterMonth($month)
                        ->filterYear($year)
                        ->MonthlySumCron()
                        ->first();

                    if(empty($monthdata))
                    {
                        echo "Date is not present in DB"."\n";
                        continue;
                    }

                    $monthdata = $monthdata->toArray();
                    $DateInputObj = new Carbon($year."-".$month);
                    $today = Carbon::now()->format('Y-m');
                    $todaydate = Carbon::now()->format('d');


                    // total Subscriber calculate from Tbale , Its not a Sum of All date . get from last of the current date
                    $total_subscriber = 0;

                    if($today == $key)
                    {
                        $last_day_given_Month = Carbon::now()->format('Y-m-d');//->subDays(6)
                    }
                    else if ($todaydate == 01) // if first day of Month , So We will get Total suscription from previous month last date
                    {
                        $last_day_given_Month = Carbon::now()->subMonthsNoOverflow()->endOfMonth()->toDateString();
                    }
                    else
                    {
                        $last_day_given_Month = $DateInputObj->endOfMonth()->toDateString();
                    }

                    $total_subscription = ReportSummeriseUsers::select("total")->filteroperatorID($userOperatorId)->filterDate($last_day_given_Month)->first();
                    $temp['total'] = 0;
                    if(!empty($total_subscription))
                    {
                        $total_subscriber = $total_subscription->total;

                        $temp['total'] = $total_subscriber;
                    }

                    if(!empty($monthdata))
                    {
                        $monthdata = array_merge($temp,$monthdata);
                    }

                    $allOperatorMonthData[] = $monthdata;
                }
            }

            if(!empty($allOperatorMonthData)){
                $insert = MonthlyReportSummery::upsert($allOperatorMonthData,['user_id','year','month','operator_id'],['country_id','fmt_failed','fmt_success','mt_failed','mt_success','gros_rev','total_reg','total_unreg','purge_total','total']);

                $total_insert = $total_insert + $insert;
                echo $user_id_string = $user_id_string ."_ total update ".$insert;       
            }

            $end_date = new DateTime('now');
            // insert data in cron_logs table

            $status = 'Success';

            echo "Log Update ....";

            $data = ['description' => $descriptionCron.$user_id_string,'signature' => $this->signature,'command' => 'php artisan '.$this->signature,'date' => $start_date->format('Y-m-d'),'cron_start_date' => $start_date->format('Y-m-d H:i:s'),'cron_end_date' => $end_date->format('Y-m-d H:i:s'),'total_in_up' => $total_insert,'table_name' => 'monthly_report_summeries','status' => $status];

            CronLog::upsert($data,['signature','date'],['description','command','cron_end_date','table_name','total_in_up','status']);
        }

        return 0;
    }
}
