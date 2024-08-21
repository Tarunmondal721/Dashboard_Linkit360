<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\common\Utility;
use App\Models\Operator;
use App\Models\Service;
use App\Models\CronLog;
use DateTime;

class CronServiceSync extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'CronServiceSync';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'The cron Will update all Service in Own Db from ferry Server';

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
    $url = config('thirdpartyapi.api_url.service');
    $url_service_name = config('thirdpartyapi.api_url.service_name');

    $cron_start_date = new DateTime('now');
    $date = date("Y-m-d");
    $status = 'Failure';
    $data = ['description' => $this->description,'signature' => $this->signature,'command' => 'php artisan '.$this->signature,'date' => $date,'cron_start_date' => $cron_start_date->format('Y-m-d H:i:s'),'cron_end_date' => $cron_start_date->format('Y-m-d H:i:s'),'total_in_up' => 0,'table_name' => 'services','status' => $status];

    CronLog::upsert($data,['signature','date'],['description','command','cron_start_date','cron_end_date','total_in_up','table_name','status']);

    $response = Utility::GetResponse($url);
    $opeartors = Operator::all()->pluck( 'id_operator','operator_name');

    $services = array();

    if(!empty($response))
    {
      foreach ($response as $key => $value)
      {    
        $service_name = "";

        $url_service_name_temp = $url_service_name."".$value['id_service'];

        $response_service_name = Utility::GetResponse($url_service_name_temp);

        if(!empty($response_service_name))
        {
          $service_name = $response_service_name[0]['servicename'];
        }

        $data = array();
        $opearator_id = Utility::getIdbyOperatorName($value['operator'],$opeartors);
        $data['id_service'] = $value['id_service'];
        $data['service_name'] = $service_name;
        $data['operator_id'] = $opearator_id;
        $data['operator_name'] = $value['operator'];
        $data['dascription'] = $value['description'];
        $data['service_type'] = $value['service_type'];
        $data['sdc'] = $value['sdc'];
        $data['price'] = $value['price'];
        $data['keyword'] = $value['keyword'];
        $data['owner'] = $value['owner'];
        $data['keyword_complete'] = $value['keyword_complete'];

        $services[] = $data;
      }

      if(sizeof($services)>0)
      {      
        Service::upsert($services,['id_service'],['service_name','operator_id','operator_name','dascription','service_type','sdc','price','keyword','owner','keyword_complete']);
                
        $status = 'success';
      }

      $cron_end_date = new DateTime('now');
      $data = ['description' => $this->description,'signature' => $this->signature,'command' => 'php artisan '.$this->signature,'date' => $date,'cron_start_date' => $cron_start_date->format('Y-m-d H:i:s'),'cron_end_date' => $cron_end_date->format('Y-m-d H:i:s'),'total_in_up' => sizeof($services) ,'table_name' => 'services','status' => $status];

      CronLog::upsert($data,['signature','date'],['description','command','cron_start_date','cron_end_date','total_in_up','table_name','status']);
    }

    return 0;
  }
}
