<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\report_summarize;
use App\Models\Operator;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use App\Models\Country;
use App\Models\CronLog;
use App\Models\MonthlyReportSummery;
use DateTime;

class CronMonthlyReportSummery extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CronMonthlyReportSummery {--year=}  {--month=}';

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
        $start_date = new DateTime('now');
        $datesNotInclude[] = date("Y-m-d");
        $status = 'Failure';
        
        $year = $this->option('year');
        $month = $this->option('month');
        $descriptionCron = " For that Month : ". $year." - " .$month;
        $data = ['description' => $this->description,'signature' => $this->signature,'command' => 'php artisan '.$this->signature,'date' => $start_date->format('Y-m-d'),'cron_start_date' => $start_date->format('Y-m-d H:i:s'),'cron_end_date' => $start_date->format('Y-m-d H:i:s'),'total_in_up' => 0,'table_name' => 'monthly_report_summeries','status' => $status];

        CronLog::upsert($data,['signature','date'],['description','command','cron_start_date','cron_end_date','table_name','total_in_up','status']);

        $Operators = Operator::Status(1)->get();
        $Country = Country::all()->toArray();
        $allOperatorMonthData = [];

        if(!empty($Country))
        {
            foreach($Country as $CountryI)
            {
                $countries[$CountryI['id']] = $CountryI;
            }
        }

        $MonthReports = array();

        if(!empty($Operators))
        {
            foreach($Operators as $operator)
            {
                $monthdata = array();
                $temp = array();

                $country_id  = $operator->country_id;
                $contain_id = Arr::exists($countries, $country_id);

                $temp['country_id'] = 0;

                if($contain_id )
                {
                    $temp['country_id'] = $countries[$country_id]['id'];
                }

                $key = $year."-".$month;

                $temp['user_id'] = 0;
                $temp['year'] = $year;
                $temp['month'] = $month;
                $temp['key'] = $key;

                $id_operator = $operator->id_operator;

                $monthdata = report_summarize::filteroperatorID($id_operator)->filterMonth($month)->filterYear($year)->MonthlySumCron()->NotDateInclude($datesNotInclude)->first();

                if(empty($monthdata))
                {
                   echo "\n\rData is not present in DB for operator id : ".$id_operator;
                   continue;
                }

                $monthdata = $monthdata->toArray();

                $DateInputObj = new Carbon($year."-".$month);
                $today = Carbon::now()->format('Y-m');
                $todaydate = Carbon::now()->format('d');

                $total_subscriber = 0;

                if($today == $key)
                {
                    $last_day_given_Month = Carbon::now()->format('Y-m-d');  //subDays(6)
                }
                else if ($todaydate == 01)
                {
                    // if first day of Month , So We will get Total suscription from previous month last date
                    $last_day_given_Month = Carbon::now()->subMonthsNoOverflow()->endOfMonth()->toDateString();
                }
                else
                {
                    $last_day_given_Month = $DateInputObj->endOfMonth()->toDateString();
                }

                $total_subscription = report_summarize::select("total")->filteroperatorID($id_operator)->filterDate($last_day_given_Month)->first();
                    
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

            // dd($allOperatorMonthData);
            if(!empty($allOperatorMonthData)){
                $insert = MonthlyReportSummery::upsert($allOperatorMonthData,['user_id','year','month','operator_id'],['country_id','fmt_failed','fmt_success','mt_failed','mt_success','gros_rev','total_reg','total_unreg','purge_total','total']);

                echo  $status = $insert ? 'success' : 'Failure';
                echo  'insert '.sizeof($allOperatorMonthData).' data in '.$year.":".$month."\n";
            }
        }

        $end_date = new DateTime('now');
        // insert data in cron_logs table

        $data = ['description' => $descriptionCron,'signature' => $this->signature,'command' => 'php artisan '.$this->signature,'date' => $start_date->format('Y-m-d'),'cron_start_date' => $start_date->format('Y-m-d H:i:s'),'cron_end_date' => $end_date->format('Y-m-d H:i:s'),'total_in_up' => sizeof($allOperatorMonthData),'table_name' => 'monthly_report_summeries','status' => $status];

        CronLog::upsert($data,['signature','date'],['description','command','cron_start_date','cron_end_date','table_name','total_in_up','status']);

        return 0;
    }
}
