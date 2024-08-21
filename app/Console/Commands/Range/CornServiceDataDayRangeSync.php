<?php

namespace App\Console\Commands\Range;

use Illuminate\Console\Command;
use App\common\Utility;
use App\Models\Service;
use App\Models\ServiceHistory;
use App\Models\CronLog;
use Carbon\CarbonPeriod;
use DateTime;

class CornServiceDataDayRangeSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CornServiceDataDayRangeSync {--sdate=} {--edate=}';

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
        Utility::CronLog("Start Cron");
        $cron_start_date = new DateTime('now');

        $Url_service_mt_ = config('thirdpartyapi.api_url.service_mt_');
        $Url_service_fmt_ = config('thirdpartyapi.api_url.service_fmt_');
        $Url_service_unReg = config('thirdpartyapi.api_url.service_unReg');
        $Url_service_active = config('thirdpartyapi.api_url.service_active');
        $Url_service_purged = config('thirdpartyapi.api_url.service_purged');

        $loop = 0;
        $rangeStartDate = $this->option('sdate');
        $rangeEndDate = $this->option('edate');
        $period = CarbonPeriod::create($rangeStartDate,$rangeEndDate);

        $description = "Start Date : ".$rangeStartDate . " End Date : ".$rangeEndDate;

        Utility::CronLog("Data inserting date : ".$cron_start_date->format('Y-m-d H:i:s'));

        $data = ['description' => $description,'signature' => $this->signature,'command' => 'php artisan '.$this->signature,'date' => $rangeStartDate,'cron_start_date' => $cron_start_date->format('Y-m-d H:i:s'),'cron_end_date' => $cron_start_date->format('Y-m-d H:i:s'),'total_in_up' => $loop,'table_name' => 'service_histories','status' => 'processing'];

        CronLog::upsert($data,['id'],['description','signature','command','date','cron_start_date','cron_end_date','table_name','total_in_up','status']);

        // $serviceStaticIdsTest =  array(2001,2077,2119,2120);
        $ServicesDivideByHundread = array(2121,2122,2123,2124,2167,2169,2170,2171);
        $ServicesDivideByThousand = array(1820,1821);
        foreach ($period as $date) {
            $services = Service::all();
            $start_date = $end_date = $date->format('Y-m-d');
            // $services = Service::GetserviceByIds($serviceStaticIdsTest)->get();

            $service_history_data = array();
            $testData = array();
            $LastServiceID = 0;
            $totalRowInsert = 0;

            if(!empty($services))
            {
                foreach ($services as $key => $value)
                {
                    $data = array();
                    $id = $value['id_service'];
                    $LastServiceID = "Service id :".$id .$value['operator_id']."-".$value['operator_name'];

                    echo "\n\r : Service ID : ".$id;

                    $data['id_service'] = $value['id_service'];
                    $data['operator_id'] = $value['operator_id'];
                    $data['operator_name'] = $value['operator_name'];
                    $data['date'] = $start_date;
                    $data['fmt_success'] = 0;
                    $data['fmt_failed'] = 0;
                    $data['mt_success'] = 0;
                    $data['mt_failed'] = 0;
                    $data['total_reg'] = 0;
                    $data['total_unreg'] = 0;
                    $data['total'] = 0;
                    $data['gros_rev'] =0;
                    $data['purge_total'] = 0;
                    $data['date'] = $start_date;

                    $Url_service_mt_temp = $Url_service_mt_.$id."|".$start_date."|".$end_date;
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
                        $data['mt_success'] = $service_mt_Response[0]['mt_success'];
                        $data['mt_failed'] = $service_mt_Response[0]['mt_failed'];

                        if(in_array($id, $ServicesDivideByHundread)){
                            $data['gros_rev'] = $service_mt_Response[0]['gros_rev']/100;
                        }elseif(in_array($id, $ServicesDivideByThousand)){
                            $data['gros_rev'] = $service_mt_Response[0]['gros_rev']/1000;
                        }else{
                            $data['gros_rev'] = $service_mt_Response[0]['gros_rev'];
                        }
                    }

                    // Sample Json data [{"date":"2022-10-15","id_service":"1","total_reg":"0","total_unreg":"1"}]
                    $service_unReg_Response = Utility::GetResponse($Url_service_unReg_temp);

                    if(sizeof($service_unReg_Response))
                    {
                        $data['total_reg'] = $service_unReg_Response[0]['total_reg'];
                        $data['total_unreg'] = $service_unReg_Response[0]['total_unreg'];
                    }

                    // Sample response [{"date":"2022-10-28","total":"4003"}]
                    $service_active_Response = Utility::GetResponse($Url_service_active_temp);

                    if(sizeof($service_active_Response))
                    {
                        $data['total'] = $service_active_Response[0]['total'];
                    }

                    // Sample response [{"date":"2022-10-28","id_service":"973","purge_total":"767"}]
                    $service_purged_Response=Utility::GetResponse($Url_service_purged_temp);

                    if(sizeof($service_purged_Response))
                    {
                        $data['purge_total'] = $service_purged_Response[0]['purge_total'];
                    }

                    if(sizeof($data))
                    {
                        $service_history_data[] = $data;
                    }

                    /* Update structure DB Table : ALTER TABLE `report`.`service_histories` ADD UNIQUE `upsertUpdate` (`id_service`, `date`);*/

                    if(sizeof($service_history_data)>300)
                    {
                        ServiceHistory::upsert($service_history_data,['id_service','date'],['operator_id','operator_name','id_service','date','fmt_success','fmt_failed','mt_success','mt_failed','gros_rev','total_reg','total_unreg','total','purge_total']);

                        $totalRowInsert = $totalRowInsert+count($service_history_data);
                        Utility::CronLog("Inserting :" . count($service_history_data));
                        $service_history_data = array();
                    }

                    $loop = $loop+sizeof($service_history_data);
                }

                if(sizeof($service_history_data)>0)
                {
                    ServiceHistory::upsert($service_history_data,['id_service','date'],['operator_id','operator_name','id_service','date','fmt_success','fmt_failed','mt_success','mt_failed','gros_rev','total_reg','total_unreg','total','purge_total']);

                    $totalRowInsert = $totalRowInsert+count($service_history_data);

                    Utility::CronLog("after loop Inserting :" . count($service_history_data));
                    $loop = $loop+sizeof($service_history_data);
                }
            }
            else
            {
                echo "no service in Database";
                $status = 'Failure';
            }
        }
        
        $cron_end_date = new DateTime('now');

        $data = ['description' => $description,'signature' => $this->signature,'command' => 'php artisan '.$this->signature,'date' => $rangeStartDate,'cron_start_date' => $cron_start_date->format('Y-m-d H:i:s'),'cron_end_date' => $cron_end_date->format('Y-m-d H:i:s'),'total_in_up' => $loop,'table_name' => 'service_histories','status' => 'Success'];

        CronLog::upsert($data,['id'],['description','signature','command','date','cron_start_date','cron_end_date','total_in_up','table_name','status']);
        Utility::CronLog("End Cron");

        return 0;
    }
}

