<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\common\Utility;
use App\Models\Operator;
use App\Models\Service;
use App\Models\ServiceHistory;
use App\Models\Country;
use App\Models\report_summarize;
use Illuminate\Support\Arr;
use App\Models\CronLog;
use DateTime;

class CronServiceDataSummariseManual extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'CronServiceDataSummariseManual';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'The Value calculate from Summery_history table and sum of mt success Then Save on Table Thats Means Organize Data - Inpute date from keyboard';

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
    $date_input = $this->ask('Please enter date "Y-m-d" Format Ex: 2022-10-30');
    $date = $date_input;

    $Operators = Operator::all();
    $serviceSummarise = array();
    $records = 0;

    $cron_start_date = new DateTime('now');
    $description = 'The Value calculate from Summery_history table by current date this date is '.$date.' and sum of mt success Then Save on Table Thats Means Organize Data';
    $status = 'Failure';
    $data = ['description' => $description,'signature' => $this->signature,'command' => 'php artisan '.$this->signature,'date' => $date,'cron_start_date' => $cron_start_date->format('Y-m-d H:i:s'),'cron_end_date' => $cron_start_date->format('Y-m-d H:i:s'),'total_in_up' => $records,'table_name' => 'report_summarize','status' => $status];

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

        $service_data = ServiceHistory::SumByDateServiceData($operator_id,$services,$date);

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

        $serviceSummarise[] = $summerisetemp;
      }

      if(sizeof($serviceSummarise)>0)
      {
        /* Update structure DB Table : ALTER TABLE `report_summarize` ADD UNIQUE `SummariseData` (`operator_id`, `date`);*/

        report_summarize::upsert($serviceSummarise,['operator_id','date'],['operator_id','operator_name','currency','country_id','date','fmt_success','fmt_failed','mt_success','mt_failed','gros_rev','total_reg','total_unreg','total','purge_total']);

        $status = 'success';
      }
  
      print_r($records. " Records Insert/Updated");
      $cron_end_date = new DateTime('now');
      $data = ['description' => $description,'signature' => $this->signature,'command' => 'php artisan '.$this->signature,'date' => $date,'cron_start_date' => $cron_start_date->format('Y-m-d H:i:s'),'cron_end_date' => $cron_end_date->format('Y-m-d H:i:s'),'total_in_up' => $records ,'table_name' => 'report_summarize','status' => $status];

      CronLog::upsert($data,['signature','date'],['description','command','cron_start_date','cron_end_date','total_in_up','table_name','status']);
    }

    return 0;
  }
}
