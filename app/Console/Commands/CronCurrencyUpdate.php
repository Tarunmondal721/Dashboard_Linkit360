<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\common\Utility;
use App\Models\Country;
use App\Models\CronLog;
use DateTime;

class CronCurrencyUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CurrencyUpdate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'The Cron Will be Run for Currency USD value update';

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
        $data = ['description' => $this->description,'signature' => $this->signature,'command' => 'php artisan '.$this->signature,'date' => $start_date->format('Y-m-d'),'cron_start_date' => $start_date->format('Y-m-d H:i:s'),'cron_end_date' => $start_date->format('Y-m-d H:i:s'),'total_in_up' => 0,'table_name' => 'reports_summarize_dashbroads','status' => 'Processing'];

        CronLog::upsert($data,['signature','date'],['description','command','cron_start_date','cron_end_date','table_name','total_in_up','status']);

        $utility = new Utility;
        $updateData = array();
        $Country = [];
        $Countrys = Country::get();

        if(!empty($Countrys))
        {
            foreach($Countrys as $Country){

                if(strcmp($Country->currency_code,'$') == 0 || strcmp($Country->currency_code,'SSP') == 0 || strcmp($Country->currency_code,'NER') == 0)
                    continue;

                $CurrencyUrl = config('globalconfig.currencyApiUrl').'&base_currency='.$Country->currency_code;

                $newUpdate = $utility->GetResponseFromUrl($CurrencyUrl)->data;
                $updateUSD = $newUpdate->USD;
                $usdValue = (double)$updateUSD->value;
                $value = number_format($usdValue, 9);
                $updateData[] = [
                    'id' => $Country->id,
                    'country' => $Country->country,
                    'country_code' => $Country->country_code,
                    'currency_code' => $Country->currency_code,
                    'currency_value' => $Country->currency_value,
                    'usd' => $value,
                    'flag' => $Country->flag,
                ];
            }
        }

        if(!empty($updateData))
        {
            Country::upsert($updateData, ['id'],['usd']);
        }

        $end_date = new DateTime('now');
        // insert data in cron_logs table
        $status = 'Success';
        $data = ['description' => $this->description,'signature' => $this->signature,'command' => 'php artisan '.$this->signature,'date' => $start_date->format('Y-m-d'),'cron_start_date' => $start_date->format('Y-m-d H:i:s'),'cron_end_date' => $end_date->format('Y-m-d H:i:s'),'total_in_up' => sizeof($updateData),'table_name' => 'reports_summarize_dashbroads','status' => $status];

        CronLog::upsert($data,['signature','date'],['description','command','cron_start_date','cron_end_date','table_name','total_in_up','status']);
        
        echo sizeof($updateData).' Records Updated';

        return 0;
    }
}
