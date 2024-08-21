<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ReportsPnlsOperatorSummarizes;
use App\Models\Operator;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use App\Models\Country;
use App\Models\CronLog;
use App\Models\PnlSummeryMonth;
use DateTime;


class CronPnlMonthlyReportSummery extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CronPnlMonthlyReportSummery {--year=}  {--month=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'The Value calculate from reports_pnls_operator_summarizes  table and sum of this month mt success Then Save in  Table monthly_report_summeries - According to Select Operators';

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
        $status = 'Failure';
        $datesNotInclude[] = date("Y-m-d");
        $start_date = new DateTime('now');
        $data = ['description' => $this->description,'signature' => $this->signature,'command' => 'php artisan '.$this->signature,'date' => $start_date->format('Y-m-d'),'cron_start_date' => $start_date->format('Y-m-d H:i:s'),'cron_end_date' => $start_date->format('Y-m-d H:i:s'),'total_in_up' => 0,'table_name' => 'pnl_summery_months','status' => 'Failure'];

        CronLog::upsert($data,['signature','date'],['description','command','cron_start_date','cron_end_date','table_name','total_in_up','status']);

        $year = $this->option('year');
        $month = $this->option('month');

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
                $temp['country_id'] = "";

                if($contain_id )
                {
                    $temp['country_id'] = $countries[$country_id]['id'];
                    $temp['country_code'] = $countries[$country_id]['country_code'];
                }

                $key = $year."-".$month;

                $temp['user_id'] = 0;
                $temp['year'] = $year;
                $temp['month'] = $month;
                $temp['key'] = $key;
                $temp['type'] = 1;

                $id_operator = $operator->id_operator;

                $monthdata = ReportsPnlsOperatorSummarizes::filteroperatorID($id_operator)
                    ->filterMonth($month)
                    ->filterYear($year)
                    ->NotDateInclude($datesNotInclude)
                    ->SelectAllAttributeSum()
                    ->first();

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
                    $last_day_given_Month = Carbon::now()->subDays(1)->format('Y-m-d');
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

                $total_subscription = ReportsPnlsOperatorSummarizes::select("active_subs")->filteroperatorID($id_operator)->Whendate($last_day_given_Month)->first();

                $temp['active_subs'] = 0;

                if(!empty($total_subscription))
                {
                    $total_subscriber = $total_subscription->active_subs;

                    $temp['active_subs'] = $total_subscriber;
                }

                if(!empty($monthdata))
                {
                    $monthdata = array_merge($temp,$monthdata);
                }

                $allOperatorMonthData[] = $monthdata;
            }

            if(sizeof($allOperatorMonthData)>0){
                $insert = PnlSummeryMonth::upsert($allOperatorMonthData,['user_id','year','month','id_operator'],['country_id','mo_received','mo_postback','cr_mo_received','cr_mo_postback','saaf','sbaf','cost_campaign','clicks','ratio_for_cpa','cpa_price','cr_mo_clicks','cr_mo_landing','mo','landing','reg','unreg','price_mo','active_subs','rev_usd','rev','share','lshare','other_cost','hosting_cost','content','rnd','bd','platform','pnl','br_success','br_failed','fp','fp_success','fp_failed','dp','dp_success','dp_failed','other_tax','misc_tax','excise_tax','vat','end_user_revenue_after_tax','wht','rev_after_makro_share','discremancy_project','arpu_7','arpu_30','net_revenue','tax_operator','bearer_cost','shortcode_fee','waki_messaging','net_revenue_after_tax','end_user_rev_local_include_tax','end_user_rev_usd_include_tax','gross_usd_rev_after_tax','spec_tax','net_after_tax','government_cost','dealer_commision','uso','verto','agre_paxxa','net_income_after_vat','gross_revenue_share_linkit','gross_revenue_share_paxxa']);

                $status = $insert ? 'success' : 'Failure';
                echo  'insert '.sizeof($allOperatorMonthData).' data in '.$year.":".$month."\n";
            }
        }

        $end_date = new DateTime('now');
        // insert data in cron_logs table
        // $status = $insert ? 'success' : 'Failure';
        $data = ['description' => $this->description,'signature' => $this->signature,'command' => 'php artisan '.$this->signature,'date' => $start_date->format('Y-m-d'),'cron_start_date' => $start_date->format('Y-m-d H:i:s'),'cron_end_date' => $end_date->format('Y-m-d H:i:s'),'total_in_up' => sizeof($allOperatorMonthData),'table_name' => 'pnl_summery_months','status' => $status];

        CronLog::upsert($data,['signature','date'],['description','command','cron_start_date','cron_end_date','table_name','total_in_up','status']);
        
        return 0;
    }
}
