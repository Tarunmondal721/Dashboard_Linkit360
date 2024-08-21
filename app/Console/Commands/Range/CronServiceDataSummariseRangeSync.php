<?php

namespace App\Console\Commands\Range;

use Illuminate\Console\Command;
use App\common\Utility;
use App\Models\Operator;
use App\Models\Service;
use App\Models\ServiceHistory;
use App\Models\Country;
use App\Models\report_summarize;
use Carbon\CarbonPeriod;
use App\Models\CronLog;
use DateTime;

class CronServiceDataSummariseRangeSync extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'CronServiceDataSummariseRangeSync {--sdate=} {--edate=}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'The Value calculate from Summery_history table by date range and sum of mt success Then Save on Table Thats Means Organize Data - Inpute date range from keyboard on ';

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
    Utility::CronLog("Start Cron\n");
    $rangeStartDate = $this->option('sdate');
    $rangeEndDate = $this->option('edate');
    $period = CarbonPeriod::create($rangeStartDate,$rangeEndDate);
    $cron_start_date = new DateTime('now');
    $description = 'The Value calculate from Summery_history table by current date  and sum of mt success Then Save on Table Thats Means Organize Data';
    $status = 'processing';
    $description = "Start Date : ".$rangeStartDate . " End Date : ".$rangeEndDate;
   
    $data = ['description' => $description,'signature' => $this->signature,'command' => 'php artisan '.$this->signature,'date' => $rangeStartDate,'cron_start_date' => $cron_start_date->format('Y-m-d H:i:s'),'cron_end_date' => $cron_start_date->format('Y-m-d H:i:s'),'total_in_up' => 0,'table_name' => 'report_summarize','status' => $status];
    $records = 0;
    $total_dates = "";
    
    foreach ($period as $date) {
      $date_input = $date->format('Y-m-d');
      $date = $date_input;

      $Operators = Operator::Status(1)->get();
      $serviceSummarise = array();

      CronLog::upsert($data,['signature','date'],['description','command','cron_start_date','cron_end_date','total_in_up','table_name','status']);

      if(!empty($Operators))
      {
        foreach ($Operators as $key => $Operator)
        {
          $summerisetemp = array();
          $operator_id = $Operator->id_operator;
          $operator_name = $Operator->operator_name;
          $country_id = $Operator->country_id;
          $services = $Operator->services->pluck('id_service');

          $summerisetemp['operator_id'] = $operator_id;
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

          if($operator_id == 29)
          {
            $service_historys = ServiceHistory::FilterOperator($operator_id)->filterDate($date_input)->get();

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
                }
                else if($id_service == 698)
                {
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
            }
          }
          else
          {
            $service_data = ServiceHistory::SumByDateServiceData($operator_id,$services,$date);
            //$serviceSql = $service_data->toSql();
            // dd($service_data);

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
              }
            }
          }

          $serviceSummarise[] = $summerisetemp;
        }

        if(sizeof($serviceSummarise)>0)
        {
          /* Update structure DB Table : ALTER TABLE `report_summarize` ADD UNIQUE `SummariseData` (`operator_id`, `date`);*/

          report_summarize::upsert($serviceSummarise,['operator_id','date'],['operator_id','operator_name','country_id','date','fmt_success','fmt_failed','mt_success','mt_failed','gros_rev','total_reg','total_unreg','total','purge_total']);

          $records = $records+sizeof($serviceSummarise);

          // $total_dates =$total_dates.": ". $records."@ ";
          $status = 'success';
        }

        print_r("date ".$date." ".sizeof($serviceSummarise). " Records Insert/Updated\n");
      }
    }

    $cron_end_date = new DateTime('now');
    $data = ['description' => $description,'signature' => $this->signature,'command' => 'php artisan '.$this->signature,'date' => $rangeStartDate,'cron_start_date' => $cron_start_date->format('Y-m-d H:i:s'),'cron_end_date' => $cron_end_date->format('Y-m-d H:i:s'),'total_in_up' => $records ,'table_name' => 'report_summarize','status' => $status];

    CronLog::upsert($data,['signature','date'],['description','command','cron_start_date','cron_end_date','total_in_up','table_name','status']);
    Utility::CronLog("end Cron");

    return 0;
  }
}
