<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\common\Utility;
use App\Models\Country;
use Illuminate\Support\Arr;
use App\Models\Operator;
use App\Models\CronLog;
use DateTime;
class CronOpertorSyn extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CronOpertorSyn';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Operator update from ferry server to linkItDb';

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
        $data = ['description' => $this->description,'signature' => $this->signature,'command' => 'php artisan '.$this->signature,'date' => $start_date->format('Y-m-d'),'cron_start_date' => $start_date->format('Y-m-d H:i:s'),'cron_end_date' => $start_date->format('Y-m-d H:i:s'),'total_in_up' => 0,'table_name' => 'operators','status' => 'Failure'];

        CronLog::upsert($data,['signature','date'],['description','command','cron_start_date','cron_end_date','table_name','total_in_up','status']);

        $url = config('thirdpartyapi.api_url.operator');

        $utility = new Utility;
        $response = $utility->GetResponse($url);
        $operators = array();
        $i = 0;
        $Country = Country::all()->pluck( 'id','country');

        if(!empty($response))
        {
            foreach ($response as $key => $value)
            {
                $id_country = $utility->getIdbyCountryName($Country,$value['country']);

                $operators[$i]['id_operator'] = $value['id_operator'];
                $operators[$i]['operator_name'] = trim($value['operator_name']);
                $operators[$i]['country_id'] = $id_country;
                $operators[$i]['country_name'] = $value['country'];
                $i++;
            }

            if(sizeof($operators)>0)
            {
                $insert = Operator::upsert($operators,['id_operator'],['operator_name','country_id','country_name']);

                $status = $insert ? 'success' : 'Failure';
                echo  $status."\n".$i;
            }
        }

        $end_date = new DateTime('now');
        // insert data in cron_logs table

        $data = ['description' => $this->description,'signature' => $this->signature,'command' => 'php artisan '.$this->signature,'date' => $start_date->format('Y-m-d'),'cron_start_date' => $start_date->format('Y-m-d H:i:s'),'cron_end_date' => $end_date->format('Y-m-d H:i:s'),'total_in_up' => $i,'table_name' => 'operators','status' => $status];

        CronLog::upsert($data,['signature','date'],['description','command','cron_start_date','cron_end_date','table_name','total_in_up','status']);

        return 0;
    }
}
