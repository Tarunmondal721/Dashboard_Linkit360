<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\common\Utility;
use App\common\UtilityReports;
use App\Models\Operator;
use App\Models\Service;
use App\Models\ServiceHistory;
use App\Models\Country;
use App\Models\report_summarize;
use App\Models\ReportsPnlsOperatorSummarizes;
use App\Models\ReportsSummarizeDashbroads;
use Illuminate\Support\Arr;
use App\Models\CronLog;
use DateTime;
use Carbon\Carbon;
use Schema;

class CronDashboardSummarizeDay extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'CronDashboardSummarizeDay';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'CronDashboardSummarizeDay Summarise date for Dasboard';

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
    $today = date('Y-m-d');  
    
    $datesNotInclude[] = $today;// today ignore for calculation 
    $DbdatesNotInclude[] = $today;
    $start_date = new DateTime('now');
    $data = ['description' => $this->description,'signature' => $this->signature,'command' => 'php artisan '.$this->signature,'date' => $start_date->format('Y-m-d'),'cron_start_date' => $start_date->format('Y-m-d H:i:s'),'cron_end_date' => $start_date->format('Y-m-d H:i:s'),'total_in_up' => 0,'table_name' => 'reports_summarize_dashbroads','status' => 'Processing'];

    CronLog::upsert($data,['signature','date'],['description','command','cron_start_date','cron_end_date','table_name','total_in_up','status']);

    $reamingdays = UtilityReports::ReamingDaysMonth();

    $date = Carbon::now();
    $today = Carbon::now();
    $current_month = $today->month;
    $current_year = $today->year;
    $current_date = $today->toDateString();

    $previous_day = $today->subDays(1);
    $previous_day_month = $previous_day->month;

    if($current_month == $previous_day_month)
    {
      $current_date = $previous_day->toDateString();
    }

    $lastMonthfirstday = Carbon::now()->startOfMonth()->subMonthsNoOverflow();

    $last_month = $lastMonthfirstday->month;
    $last_year = $lastMonthfirstday->year;
    $last_day_of_Lastmonth = $lastMonthfirstday->endOfMonth()->toDateString();

    $firstDayofPreviousMonth = $lastMonthfirstday->startOfMonth()->subMonthsNoOverflow();

    $previous_month = $firstDayofPreviousMonth->month;
    $previous_year = $firstDayofPreviousMonth->year;
    $last_day_of_previousMonth = $firstDayofPreviousMonth->endOfMonth()->toDateString();

    $operatorRecords = [];
    $Operators = Operator::all();

    if(!empty($Operators))
    {
      foreach ($Operators as $key => $Operator)
      {
        $current_month_Result = 0;
        $records = $this->DbColumns();
        $countryArr = array();

        $id_operator = $Operator->id_operator;
        $operator_name = $Operator->operator_name;
        $country = $Operator->country;
        $company = $Operator->company_operators;
        $company_id = null;

        if(isset($company))
        {
          $company_id = $company->company_id;
        }

        $Exchange_rate = $country->usd;
        $country_id = $Operator->country_id;
        $country_code = $country->country_code;

        $records['country_id'] = (int)$country_id;
        $countryArr['country_code'] = $country_code;
        $records['operator_id'] = (int)$id_operator;
        $records['date'] = $current_date;
        $records['company_id'] = isset($company_id) ? $company_id : null;

        $share = $Operator->revenueshare;
        $operator_revenue_share = isset($share->operator_revenue_share) ? $share->operator_revenue_share : '';
        $merchant_revenue_share = isset($share->merchant_revenue_share) ? $share->merchant_revenue_share : '';

        /* Current Month Calculation */

        $current_month_Query = ReportsPnlsOperatorSummarizes::filterOperatorId($id_operator)
        ->filterMonth($current_month)
        ->filterYear($current_year)
        ->NotDateInclude($DbdatesNotInclude)
        ->SelectMonthSum();

        $current_month_Result = $current_month_Query->get()->toArray();

        $current_month_forActiveSubs_query = ReportsPnlsOperatorSummarizes::filterOperatorId($id_operator)->Whendate($current_date);

        $current_month_forActiveSubs_result = $current_month_forActiveSubs_query->first();

        $current_month_subscribe = 0;

        if(!empty($current_month_forActiveSubs_result))
        {
          $current_month_subscribe = $current_month_forActiveSubs_result->active_subs;
        }

        $current_month_dayoftheday = date('d',strtotime("-1 days"));
        $current_average_pnl = 0;
        $current_average_cost = 0;
        $current_average_mo = 0;
        $current_avg_30_arpu = 0;

        if(!empty($current_month_Result))
        {
          $current_revenue = $current_month_Result[0]['rev'];
          $current_gross_revenue = $current_month_Result[0]['lshare'];

          $current_revenue_usd = UtilityReports::UsdCalCriteria($current_revenue,$Exchange_rate,$records,$countryArr,"days","daily");

          $current_gross_revenue_usd = UtilityReports::UsdCalCriteria($current_gross_revenue,$Exchange_rate,$records,$countryArr,"days","daily");

          $hosting_cost = $current_month_Result[0]['hosting_cost'];
          $content = $current_month_Result[0]['content'];
          $rnd = $current_month_Result[0]['rnd'];
          $bd = $current_gross_revenue_usd * (2.5/100);
          $market_cost = $current_gross_revenue_usd * (1.5/100);

          $other_cost = $bd + $hosting_cost + $content + $rnd + $market_cost;

          $current_mo = $current_month_Result[0]['mo_received'];
          $current_cost = $current_month_Result[0]['saaf'];
          $current_pnl = $current_gross_revenue_usd - ($other_cost + $current_cost);
          $current_price_mo = ($current_mo == 0) ? 0 : ($current_cost / $current_mo);
          $current_usd_rev_share = (((float)$current_revenue_usd * (float)$merchant_revenue_share) / 100 );
          $current_reg = $current_month_Result[0]['reg'];
          $current_reg_sub = $current_reg + $current_month_subscribe;
          $current_30_arpu = ($current_reg_sub == 0) ? 0 : ($current_usd_rev_share / $current_reg_sub);

          $current_roi = ($current_30_arpu == 0) ? 0 : ($current_price_mo / $current_30_arpu);

          $current_average_pnl = $current_pnl / $current_month_dayoftheday;
          $current_average_cost = $current_cost / $current_month_dayoftheday;
          $current_average_mo = $current_mo / $current_month_dayoftheday;
          $current_avg_30_arpu = (($reamingdays > 0) ? ($current_30_arpu /$reamingdays) : ($current_30_arpu / date('t', strtotime($date))));
          $current_total_mo = $current_reg;
          $current_average_total_mo = $current_total_mo / $current_month_dayoftheday;

          $records['current_revenue'] = (float)$current_revenue;
          $records['current_revenue_usd'] = (float)$current_revenue_usd;
          $records['current_gross_revenue'] = (float)$current_gross_revenue;
          $records['current_gross_revenue_usd'] = (float)$current_gross_revenue_usd;
          $records['current_mo'] = $current_mo;
          $records['current_total_mo'] = $current_total_mo;
          $records['current_cost'] = $current_cost;
          $records['current_pnl'] = $current_pnl;
          $records['current_price_mo'] = (float)$current_price_mo;
          $records['current_usd_rev_share'] = (float)$current_usd_rev_share;
          $records['current_reg_sub'] = (float)$current_reg_sub;
          $records['current_30_arpu'] = (float)$current_30_arpu;
          $records['current_roi'] = (float)$current_roi;

          $estimated_revenue = ($current_pnl + ($current_average_pnl * $reamingdays));
          $estimated_gross_revenue = ($current_pnl + ($current_average_pnl * $reamingdays));
                                    
          $estimated_revenue_usd = UtilityReports::UsdCalCriteria($estimated_revenue,$Exchange_rate,$records,$countryArr,"days","daily");

          $estimated_gross_revenue_usd = UtilityReports::UsdCalCriteria($estimated_gross_revenue,$Exchange_rate,$records,$countryArr,"days","daily");
                                    
          $estimated_mo = $current_mo + ($current_average_mo * $reamingdays);
          $estimated_total_mo = $current_total_mo + ($current_average_total_mo * $reamingdays);
          $estimated_cost = $current_cost + ($current_average_cost * $reamingdays);
          $estimated_pnl = $current_pnl + ($current_average_pnl * $reamingdays);

          $estimated_price_mo = ($estimated_mo == 0) ? 0 : ($estimated_cost / $estimated_mo);
          $estimated_30_arpu = ($current_30_arpu + $current_avg_30_arpu * $reamingdays);
          $estimated_roi = ($estimated_30_arpu == 0) ? 0 : ($estimated_price_mo / $estimated_30_arpu);

          $records['estimated_revenue'] = $estimated_revenue;
          $records['estimated_revenue_usd'] = $estimated_revenue_usd;
          $records['estimated_gross_revenue'] = (float)$estimated_gross_revenue;
          $records['estimated_gross_revenue_usd'] = (float)$estimated_gross_revenue_usd;
          $records['estimated_mo'] = $estimated_mo;
          $records['estimated_total_mo'] = $estimated_total_mo;
          $records['estimated_cost'] = $estimated_cost;
          $records['estimated_pnl'] = $estimated_pnl;
          $records['estimated_price_mo'] = $estimated_price_mo;
          $records['estimated_30_arpu'] = $estimated_30_arpu;
          $records['estimated_roi'] = $estimated_roi;
        }

        /* End Current Month Calculation */

        $last_month_Query = ReportsPnlsOperatorSummarizes::filterOperatorId($id_operator)
        ->filterMonth($last_month)
        ->filterYear($last_year)
        ->SelectMonthSum();

        $last_month_Result = $last_month_Query->get()->toArray();

        $last_month_forActiveSubs_query = ReportsPnlsOperatorSummarizes::filterOperatorId($id_operator)
        ->Whendate($last_day_of_Lastmonth);

        $last_month_forActiveSubs_result = $last_month_forActiveSubs_query->first();

        $last_month_subscribe = 0;

        if(!empty($last_month_forActiveSubs_result))
        {
          $last_month_subscribe = $last_month_forActiveSubs_result->active_subs;
        }

        if(!empty($last_month_Result))
        {
          $last_revenue = $last_month_Result[0]['rev'];
          $last_gross_revenue = $last_month_Result[0]['lshare'];

          $last_revenue_usd = UtilityReports::UsdCalCriteria($last_revenue,$Exchange_rate,$records,$countryArr,"days","daily");

          $last_gross_revenue_usd = UtilityReports::UsdCalCriteria($last_gross_revenue,$Exchange_rate,$records,$countryArr,"days","daily"); 

          $hosting_cost = $last_month_Result[0]['hosting_cost'];
          $content = $last_month_Result[0]['content'];
          $rnd = $last_month_Result[0]['rnd'];
          $bd = $last_gross_revenue_usd * (2.5/100);
          $market_cost = $last_gross_revenue_usd * (1.5/100);

          $other_cost = $bd + $hosting_cost + $content + $rnd + $market_cost;    

          $last_mo = $last_month_Result[0]['mo_received'];
          $last_cost = $last_month_Result[0]['saaf'];
          $last_pnl = $last_gross_revenue_usd - ($other_cost + $last_cost);
          $last_price_mo = ($last_mo == 0) ? 0 : ($last_cost / $last_mo);
          $last_usd_rev_share = (((float)$last_revenue_usd * (float)$merchant_revenue_share) / 100 );
          $reg = $last_month_Result[0]['reg'];
          $last_reg_sub = $reg + $last_month_subscribe;
          $last_30_arpu = ($last_reg_sub == 0) ? 0 : ($last_usd_rev_share / $last_reg_sub);
          $last_price_mo = ($last_mo == 0) ? 0 : ($last_cost / $last_mo);
          $last_total_mo = $reg;

          $last_roi = ($last_30_arpu == 0) ? 0 : ($last_price_mo / $last_30_arpu);

          $records['last_revenue'] = $last_revenue;
          $records['last_revenue_usd'] = $last_revenue_usd;
          $records['last_gross_revenue'] = (float)$last_gross_revenue;
          $records['last_gross_revenue_usd'] = (float)$last_gross_revenue_usd;
          $records['last_mo'] = $last_mo;
          $records['last_total_mo'] = $last_total_mo;
          $records['last_cost'] = $last_cost;
          $records['last_pnl'] = $last_pnl;
          $records['last_price_mo'] = $last_price_mo;
          $records['last_usd_rev_share'] = $last_usd_rev_share;
          $records['last_reg_sub'] = $last_reg_sub;
          $records['last_30_arpu'] = $last_30_arpu;
          $records['last_roi'] = $last_roi;
        }

        /* end Last month Calculation */

        $last_previous_month_Query = ReportsPnlsOperatorSummarizes::filterOperatorId($id_operator)
        ->filterMonth($previous_month)
        ->filterYear($previous_year)
        ->SelectMonthSum();

        $last_previous_month_Result = $last_previous_month_Query->get()->toArray();

        $last_previous_month_forActiveSubs_query = ReportsPnlsOperatorSummarizes::filterOperatorId($id_operator)->Whendate($last_day_of_previousMonth);

        $last_month_forActiveSubs_result = $last_previous_month_forActiveSubs_query->first();

        $last_previous_month_subscribe = 0;

        if(!empty( $last_month_forActiveSubs_result))
        {
          $last_previous_month_subscribe =  $last_month_forActiveSubs_result->active_subs;
        }

        if(!empty($last_previous_month_Result))
        {
          $prev_revenue = $last_previous_month_Result[0]['rev'];
          $prev_gross_revenue = $last_previous_month_Result[0]['lshare'];

          $prev_revenue_usd = UtilityReports::UsdCalCriteria($prev_revenue,$Exchange_rate,$records,$countryArr,"days","daily");

          $prev_gross_revenue_usd = UtilityReports::UsdCalCriteria($prev_gross_revenue,$Exchange_rate,$records,$countryArr,"days","daily");

          $hosting_cost = $last_previous_month_Result[0]['hosting_cost'];
          $content = $last_previous_month_Result[0]['content'];
          $rnd = $last_previous_month_Result[0]['rnd'];
          $bd = $prev_gross_revenue_usd * (2.5/100);
          $market_cost = $prev_gross_revenue_usd * (1.5/100);

          $other_cost = $bd + $hosting_cost + $content + $rnd + $market_cost;

          $prev_mo = $last_previous_month_Result[0]['mo_received'];
          $prev_cost = $last_previous_month_Result[0]['saaf'];
          $prev_pnl = $prev_gross_revenue_usd - ($other_cost + $prev_cost);
          $prev_price_mo = ($prev_mo == 0) ? 0 : ($prev_cost / $prev_mo);
          $previous_usd_rev_share = (((float)$prev_revenue_usd * (float)$merchant_revenue_share) / 100 );
          $prev_reg =  $last_previous_month_Result[0]['reg'];
          $previous_reg_sub = $prev_reg + $last_previous_month_subscribe;
          $prev_30_arpu = ($previous_reg_sub == 0) ? 0 : ($prev_revenue_usd / $previous_reg_sub);
          $prev_roi = ($prev_mo == 0) ? 0 : ($prev_cost / $prev_mo);
          $prev_total_mo = $prev_reg;

          $records['prev_revenue'] = $prev_revenue;
          $records['prev_revenue_usd'] = $prev_revenue_usd;
          $records['prev_gross_revenue'] = (float)$prev_gross_revenue;
          $records['prev_gross_revenue_usd'] = (float)$prev_gross_revenue_usd;
          $records['prev_mo'] = $prev_mo;
          $records['prev_total_mo'] = $prev_total_mo;
          $records['prev_cost'] = $prev_cost;
          $records['prev_pnl'] = $prev_pnl;
          $records['prev_price_mo'] = $prev_price_mo;
          $records['previous_usd_rev_share'] = $previous_usd_rev_share;
          $records['previous_reg_sub'] = $previous_reg_sub;
          $records['prev_30_arpu'] = $prev_30_arpu;
          $records['prev_roi'] = $prev_roi;
        }

        echo "\n\rOperator Id ".$id_operator." || ".'OPERATOR- '.$operator_name.' || '.'COMPANY- '.$company_id.' || '.'COUNTRY- '.$country_id;

        $operatorRecords[$key] = $records;
        /* End last month  previous month calculation */
      }
    }

    if(sizeof($operatorRecords)>0)
    {
      $insert = ReportsSummarizeDashbroads::upsert($operatorRecords,['operator_id'],['date','current_revenue','current_revenue_usd','current_mo','current_total_mo','current_cost','current_pnl','current_price_mo','current_usd_rev_share','current_reg_sub','current_30_arpu','current_roi','estimated_revenue','estimated_revenue_usd','estimated_mo','estimated_total_mo','estimated_cost','estimated_pnl','estimated_price_mo','estimated_30_arpu','estimated_roi','last_revenue','last_revenue_usd','last_mo','last_total_mo','last_cost','last_pnl','last_price_mo','last_usd_rev_share','last_reg_sub','last_30_arpu','last_roi','prev_revenue','prev_revenue_usd','prev_mo','prev_total_mo','prev_cost','prev_pnl','prev_price_mo','previous_usd_rev_share','previous_reg_sub','prev_30_arpu','prev_roi','company_id','current_gross_revenue','current_gross_revenue_usd','estimated_gross_revenue','estimated_gross_revenue_usd','last_gross_revenue','last_gross_revenue_usd','prev_gross_revenue','prev_gross_revenue_usd']);
    }

    $totelDataInsert = count($operatorRecords);
    $end_date = new DateTime('now');
    $status = $insert ? 'Success' : 'Failure';
    $data = ['description' => $this->description,'signature' => $this->signature,'command' => 'php artisan '.$this->signature,'date' => $start_date->format('Y-m-d'),'cron_start_date' => $start_date->format('Y-m-d H:i:s'),'cron_end_date' => $end_date->format('Y-m-d H:i:s'),'total_in_up' => $totelDataInsert,'table_name' => 'reports_summarize_dashbroads','status' => $status];

    CronLog::upsert($data,['signature','date'],['description','command','cron_start_date','cron_end_date','table_name','total_in_up','status']);

    echo "\n\r".$totelDataInsert.' Records Insert/Updated';
    return 0;
  }

  public function DbColumns()
  {
    $columns = array();

    $columns_records = Schema::getColumnListing('reports_summarize_dashbroads');

    if(!empty($columns_records))
    {
      foreach ($columns_records as $key => $column) {
        $columns[$column] = 0;
      }

      $columns = Arr::except($columns,['id','created_at','updated_at',]);
    }

    return $columns;
  }
}
