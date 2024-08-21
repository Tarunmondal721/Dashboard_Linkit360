<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\common\Utility;
use App\Models\Operator;
use App\Models\Service;
use App\Models\ServiceHistory;
use Carbon\CarbonPeriod;
use App\Models\CronLog;
use DateTime;

class CronServiceDataRangeDayargs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CronServiceDataRangeDayargs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'The Param  is  date "Y-m-d" to "Y-m-d"  . The comman will be insertorUpdate date  Wise  Service data';

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
        $cron_start_date = new DateTime('now');
        $start_date_input = $this->ask('please enter starte date "Y-m-d" Format Ex: 2022-06-09');
        $end_date_input = $this->ask('please enter end date "Y-m-d" Format Ex: 2022-11-18');
        $period = CarbonPeriod::create($start_date_input,$end_date_input);

        // Iterate over the period
        foreach ($period as $date) {
            // echo $date->format('Y-m-d');

            $no_of_service = 0;

            $Url_service_mt_ = config('thirdpartyapi.api_url.service_mt_');
            $Url_service_fmt_ = config('thirdpartyapi.api_url.service_fmt_');
            $Url_service_unReg = config('thirdpartyapi.api_url.service_unReg');
            $Url_service_active = config('thirdpartyapi.api_url.service_active');
            $Url_service_purged = config('thirdpartyapi.api_url.service_purged');

            $description = ' The Param  is  date  '.$date->format('Y-m-d').' . The comman will be insertorUpdate Daily Service data';
            $data = ['description' => $description,'signature' => $this->signature,'command' => 'php artisan '.$this->signature,'date' => $date,'cron_start_date' => $cron_start_date->format('Y-m-d H:i:s'),'cron_end_date' => $cron_start_date->format('Y-m-d H:i:s'),'total_in_up' => $no_of_service,'table_name' => 'service_histories','status' => 'Failure'];

            CronLog::upsert($data,['signature','date'],['description','command','cron_start_date','cron_end_date','total_in_up','table_name','status']);

            $services = Service::all();

            $start_date = $end_date = $date->format('Y-m-d');

            $service_history_data = array();

            if(!empty($services))
            {
                foreach ($services as $key => $value)
                {
                    $no_of_service++;
                    $data = array();
                    $id = $value['id_service'];
                    $data['id_service'] = $value['id_service'];
                    $data['operator_id'] = $value['operator_id'];
                    $data['operator_name'] = $value['operator_name'];
                    $data['date'] = $start_date;
                    $data['fmt_success'] = 0;
                    $data['fmt_failed'] = 0;

                    $Url_service_mt_temp = $Url_service_mt_.$id."|".$start_date."|".$end_date;

                    // for first push
                    $Url_service_fmt_temp = $Url_service_fmt_.$id."|".$start_date."|".$end_date;
                    $Url_service_unReg_temp = $Url_service_unReg.$id."|".$start_date."|".$end_date;
                    $Url_service_active_temp = $Url_service_active.$id."|".$start_date."|".$end_date;
                    $Url_service_purged_temp = $Url_service_purged.$id."|".$start_date."|".$end_date;

                    /*[{"date":"2020-10-28","id_service":"12","mt_success":"0","mt_failed":"1","gros_rev":"0.00"}]*/

                    $service_fmt_Response = Utility::GetResponse($Url_service_fmt_temp);

                    if(sizeof($service_fmt_Response))
                    {
                        $data['fmt_success'] = $service_fmt_Response[0]['mt_success'];
                        $data['fmt_failed'] = $service_fmt_Response[0]['mt_failed'];
                    }

                    // Sample Response [{"date":"2022-10-28","id_service":"1","mt_success":"1","mt_failed":"4002","gros_rev":"1000.00"}]
                    $service_mt_Response = Utility::GetResponse($Url_service_mt_temp);

                    if(sizeof($service_mt_Response))
                    {
                        $data['date'] = $start_date;
                        $data['mt_success'] = $service_mt_Response[0]['mt_success'];
                        $data['mt_failed'] = $service_mt_Response[0]['mt_failed'];
                        $data['gros_rev'] = $service_mt_Response[0]['gros_rev'];;
                    }else
                    {
                        $data['mt_success'] = 0;
                        $data['mt_failed'] = 0;
                        $data['gros_rev'] = 0;
                    }

                    // Sample Json data [{"date":"2022-10-15","id_service":"1","total_reg":"0","total_unreg":"1"}]
                    $service_unReg_Response = Utility::GetResponse($Url_service_unReg_temp);

                    if(sizeof($service_unReg_Response))
                    {
                        $data['total_reg'] = $service_unReg_Response[0]['total_reg'];
                        $data['total_unreg'] = $service_unReg_Response[0]['total_unreg'];
                    }else
                    {
                        $data['total_reg'] = 0;
                        $data['total_unreg'] = 0;
                    }

                    // Sample response [{"date":"2022-10-28","total":"4003"}]
                    $service_active_Response = Utility::GetResponse($Url_service_active_temp);

                    if(sizeof($service_active_Response))
                    {
                        $data['total'] = $service_active_Response[0]['total'];
                    }else
                    {
                        $data['total'] = 0;
                    }

                    // Sample response [{"date":"2022-10-28","id_service":"973","purge_total":"767"}]
                    $service_purged_Response = Utility::GetResponse($Url_service_purged_temp);

                    if(sizeof($service_purged_Response))
                    {
                        $data['purge_total'] = $service_purged_Response[0]['purge_total'];
                    }else
                    {
                        $data['purge_total'] = 0;
                    }

                    if(sizeof($data))
                    {
                        $service_history_data[] = $data;
                    }

                    /* Update structure DB Table : ALTER TABLE `report`.`service_histories` ADD UNIQUE `upsertUpdate` (`id_service`, `date`);*/

                    if(sizeof($service_history_data)>100)
                    {
                        ServiceHistory::upsert($service_history_data,['id_service','date'],['operator_id','operator_name','id_service','date','fmt_success','fmt_failed','mt_success','mt_failed','gros_rev','total_reg','total_unreg','total','purge_total']);
                    }
                }

                if(sizeof($service_history_data)>0)
                {
                    ServiceHistory::upsert($service_history_data,['id_service','date'],['operator_id','operator_name','id_service','date','fmt_success','fmt_failed','mt_success','mt_failed','gros_rev','total_reg','total_unreg','total','purge_total']);
                }

                $status = 'success';
            }
            else
            {
                echo "no service in Database";
                $status = 'Failure';
            }

            print_r($no_of_service." :"."Success date=".$date."\n");
            $cron_end_date = new DateTime('now');
            $data = ['description' => $description,'signature' => $this->signature,'command' => 'php artisan '.$this->signature,'date' => $date->format('Y-m-d'),'cron_start_date' => $cron_start_date->format('Y-m-d H:i:s'),'cron_end_date' => $cron_end_date->format('Y-m-d H:i:s'),'total_in_up' => $no_of_service ,'table_name' => 'service_histories','status' => $status];

            CronLog::upsert($data,['signature','date'],['description','command','cron_start_date','cron_end_date','total_in_up','table_name','status']);
        }

        return 0;
    }
}
