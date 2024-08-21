<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\common\Utility;
use App\Models\Operator;
use App\Models\Service;
use App\Models\ReportsPnlsSummarize;
use App\Models\CronLog;
use App\Models\ReportsPnls;
use App\Models\report_summarize;
use App\common\UtilityPnlCron;
use App\Models\Country;

use Illuminate\Support\Arr;
use DateTime;
class CronPnlSummariseOperator extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'CronPnlSummariseOperator {--date=}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'PNL Summery Summarise';

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
    $date_input = $this->option('date');

    $data = ['description' => $this->description,'signature' => $this->signature,'command' => 'php artisan '.$this->signature,'date' => $date_input,'cron_start_date' => $start_date->format('Y-m-d H:i:s'),'cron_end_date' => $start_date->format('Y-m-d H:i:s'),'total_in_up' => 0,'table_name' => 'reports_pnls_operator_summarizes','status' => 'Processing'];

    CronLog::upsert($data,['signature','date'],['description','command','cron_start_date','cron_end_date','table_name','total_in_up','status']);
        
    Utility::CronLog("Start Cron");
    // $date_input = $this->ask('please enter date "Y-m-d" Format Ex: 2022-10-30');
    $opeartors = Operator::Status(1)->get()->pluck( 'id_operator','operator_name');

    /* these are operator Coming from Waki But we have to insert all records from Operator Table Thats Why Save all ids which are from Waki */
    $opeartorsfetchFromRawData = array();

    $CountriesArr = Country::all()->toArray();
    $Countries = array();

    if(!empty($CountriesArr))
    {
      foreach ($CountriesArr as $key => $Country)
      {
        $Countries[$Country['id']] = $Country;
      }
    }

    $Sumrisereports = report_summarize::filterDate($date_input)
      ->orderBy('operator_id')
      ->get()->toArray();

    $Sumrisereports = UtilityPnlCron::getReportsOperatorID($Sumrisereports);

    /* default column array init */

    $RecordsByDate = ReportsPnls::GetRecordByDate($date_input)->get()->toArray();

    $matchedReports = array();
    $UnmatchedReports = array();

    if(!empty($RecordsByDate))
    {
      $operators = [
        'telcomcel' => 'tcel',
        // 'h3i' => 'three',
        'xldm' => 'xlaxiata',
        'trueh' => 'truemove',
        'xl' => 'id-xl-pass',
        'ais-makro-wap' => 'th-ais-mks',
        'warid' => 'pk-warid-noetic',
        'zong' => 'pk-zong-noetic',
        'telenor' => 'pk-telenor-noetic',
        'mobilink' => 'pk-mobilink-noetic',
        'globe' => 'ph-globe'
      ];

      foreach ($RecordsByDate as $key => $value)
      {
        $TmpmatchedReports = array();
        $Inititvalue = UtilityPnlCron::Columnvalueinit();

        $value = array_merge($Inititvalue , $value);
        
        if(isset($operators[$value['operator']]))
        {
          $value['operator'] = $operators[$value['operator']];
        }

        $opearator_id = Utility::getIdbyOperatorName($value['operator'],$opeartors);

        $temp = array();

        Utility::CronLog("Operator Name :".$value['operator']);

        if($opearator_id)
        {
          $value['id_operator'] = $opearator_id;
          $value['obj_operator'] = $operatorObj = Operator::filterOperatorID($opearator_id)->first();
          $value['country_code'] = $value['country'];
          $value['country_id'] = $operatorObj->country_id;
          $value['type'] = 1; //matched operator from ferry

          if(!in_array($opearator_id,$opeartorsfetchFromRawData))
          {
            $opeartorsfetchFromRawData[] = $opearator_id;
          }

          $matchedReports[] = $value;
        }
        else
        {
          $value['type'] = 0;
          $UnmatchedReports[] = $value;
        }
      }
    }

    /* calculation for matched Data */
    $CalmatchedReports = $this->CalMatchedData($matchedReports);

    $CalmatchedReports = $this->Columnformula($CalmatchedReports);
    // Column Calculate which is relation with reports_summarise table

    $CalmatchedReports = $this->ColumnfetchReportSummarise($CalmatchedReports,$Sumrisereports,$Countries);
    // Operator based calculation 

    $CalmatchedReports = UtilityPnlCron::formulaAccOperator($CalmatchedReports);

    // Column Calculate which is relation with reports_summarise table

    $dbRecords = UtilityPnlCron::dbColumnPrepare($CalmatchedReports);

    $columnNamesRetrive = UtilityPnlCron::columnNames($dbRecords);

    /* insert row on Records  umatched data */
    $insert = 0;
    $totelDataInsert = 0;

    if(!empty($dbRecords))
    {
      $insert =  ReportsPnlsSummarize::upsert($dbRecords,['date','id_operator'],$columnNamesRetrive);

      Utility::CronLog("Operator Id insert/update".$insert);
    }

    $totelDataInsert = count($dbRecords);

    /* Unmatched Data Insert */
    /*ALTER TABLE `reports_live`.`reports_pnls_operator_summarizes` ADD UNIQUE `UnmathUniqueo` (`date`, `operator`);*/

    $UnmatchedReports = $this->UnCalMatchedData($UnmatchedReports);
    $CalmatchedReports = $this->Columnformula($UnmatchedReports);
    $dbRecordsUn = UtilityPnlCron::dbColumnPrepare($CalmatchedReports);
    $insert_un = 0;

    if(!empty($dbRecordsUn))
    { 
      // run command ALTER TABLE `reports`.`reports_pnls_operator_summarizes` ADD UNIQUE (`date`, `id_operator`);

      $insert_un = ReportsPnlsSummarize::upsert($dbRecordsUn,['date','operator'],$columnNamesRetrive);
    }

    $totelDataInsert = $totelDataInsert + count($dbRecordsUn);

    /* those operator has no Waki data , Save those column which are related to report Summery */
        
    $ReamingOperators = Operator::NotInOperators($opeartorsfetchFromRawData)->get(); 
        
    $pnlForReamingOperators = array();

    if(!empty( $ReamingOperators))
    {
      foreach ($ReamingOperators as $key => $ReamingOperator)
      {
        $temp = Array();
        $Inititvalue = UtilityPnlCron::Columnvalueinit();

        $temp['date'] = $date_input;
        $temp['id_operator'] = $ReamingOperator->id_operator;      
        $temp['operator'] = $ReamingOperator->operator_name;
        $temp['obj_operator'] = $ReamingOperator;
        $country = $ReamingOperator->country;
        $temp['country_id'] = $ReamingOperator->country_id;
        $temp['country_code'] = $country->country_code;
        $temp['type'] = 1; 
        $temp = array_merge($Inititvalue , $temp);

        $pnlForReamingOperators[] = $temp;
      }
    }
 
    // Column Calculate which is relation with reports_summarise table

    $CalpnlForReamingOperators = $this->ColumnfetchReportSummarise($pnlForReamingOperators,$Sumrisereports,$Countries);

    echo "\n\r Start for reaming Oparetor :: ";
    $CalpnlForReamingOperators = UtilityPnlCron::formulaAccOperator($CalpnlForReamingOperators);

    $dbRecordsReamingOperator = UtilityPnlCron::dbColumnPrepare($CalpnlForReamingOperators);

    /* insert row on Records  umatched data */
    $insert_reaming = 0;

    if(!empty($dbRecordsReamingOperator))
    {
      $insert_reaming =  ReportsPnlsSummarize::upsert($dbRecordsReamingOperator,['date','id_operator'],$columnNamesRetrive);
    }

    $totelDataInsert = $totelDataInsert + count($dbRecordsReamingOperator);

    Utility::CronLog("Operator Id insert/update End");

    $status = 'success';
    $end_date = new DateTime('now');
    $data = ['description' => $this->description,'signature' => $this->signature,'command' => 'php artisan '.$this->signature,'date' => $date_input,'cron_start_date' => $start_date->format('Y-m-d H:i:s'),'cron_end_date' => $end_date->format('Y-m-d H:i:s'),'total_in_up' => $totelDataInsert,'table_name' => 'reports_pnls_operator_summarizes','status' => $status];

    CronLog::upsert($data,['signature','date'],['description','command','cron_start_date','cron_end_date','table_name','total_in_up','status']);

    return 0;
  }

  public function ColumnfetchReportSummarise($reports,$summarise,$Countries)
  {
    $Funreports = array();

    if(!empty($reports))
    {
      foreach ($reports as $key => $report)
      {
        $usd_value = 0;
        $days = $report['date'];
        $countryArray = array();

        $countryId = $report['obj_operator']->country_id;

        $id_operator = $report['id_operator'];

        if(isset($Countries[$countryId]))
        {
          $usd_value = $Countries[$countryId]['usd'];

          $countryArray['country_code'] = $Countries[$countryId]['country_code'];;
        }

        $merchent_share = 1;
        $operator_share = 1;
        $revenue_share = $report['obj_operator']->revenueshare;

        if(isset($revenue_share))
        {    
          $merchent_share = $revenue_share->merchant_revenue_share;
          $operator_share = $revenue_share->operator_revenue_share;
        }

        $report['merchent_share'] = $merchent_share;
        $report['operator_share'] = $operator_share;
        $report['reg'] = 0;
        $report['unreg'] = 0;
        $report['active_subs'] = 0;
        $report['rev'] = 0;
        $report['rev_usd'] = 0;
        $report['share'] = 0;
        $report['lshare'] = 0;
        $report['br'] = 0;
        $report['br_success'] = 0;
        $report['br_failed'] = 0;
        $report['fp'] = 0;
        $report['fp_success'] = 0;
        $report['fp_failed'] = 0;
        $report['dp'] = 0;
        $report['dp_success'] = 0;
        $report['dp_failed'] = 0;
   
        $operatorArray = array();
        $operatorArray['id_operator'] = $id_operator;
        if(isset($summarise[$id_operator]))
        {
          $mt_success = $summarise[$id_operator]['mt_success'];
          $mt_failed = $summarise[$id_operator]['mt_failed'];
          $fmt_success = $summarise[$id_operator]['fmt_success'];
          $fmt_failed = $summarise[$id_operator]['fmt_failed'];
          $total = $summarise[$id_operator]['total']; // total subscriber in that day
          $reg = $summarise[$id_operator]['total_reg'];
          $report['reg'] = $reg;
          $unreg = $summarise[$id_operator]['total_unreg'];
          $report['unreg'] = $unreg;
          $active_subs = $summarise[$id_operator]['total'];
          $report['active_subs'] = $active_subs;
          $rev = $summarise[$id_operator]['gros_rev'];
          $rev_usd = $usd_value * $rev;

          $report['rev'] = $rev;
                  
          // $gros_rev_Usd = $gros_rev*$usdValue;
          Utility::CronLog("Operator Id:".$id_operator .":". $rev_usd);

          $report['rev_usd'] = $rev_usd;

          /* share formula (gros_rev * usd * revenueshare->merchant)/100*/

          $share = ($rev_usd * $merchent_share) / 100;
          $report['share'] = $share;

          //lshare ~ (gros_rev * revenueshare->merchant)/100

          $lshare = ($rev * $merchent_share) / 100;
          $report['lshare'] = $lshare;

          // billing rate

          $br = UtilityPnlCron::billRate($mt_success,$mt_failed,$total);
          $report['br'] = $br;
          $report['br_success'] = $mt_success;
          $report['br_failed'] = $mt_failed;

          $fp =UtilityPnlCron::FirstPush($fmt_success,$fmt_failed,$total);
          $report['fp_success'] = $fmt_success;
          $report['fp_failed'] = $fmt_failed;

          $dp = UtilityPnlCron::Dailypush($mt_success,$mt_failed,$total);
          $report['dp'] = $dp;
          $report['dp_success'] = $mt_success;
          $report['dp_failed'] = $mt_failed;

          $report['hosting_cost'] = $hosting_cost = 0.08 * $share;
          $md = $rnd = 0.05 * $share;
          $report['rnd'] = $rnd;

          $report['content'] = $content = 0.02 * $share;
          $report['bd'] = $bd = 0.03 * $share;
          $report['platform'] = $Platform = 0.01 * $share;
          $other_cost = $hosting_cost + $content + $md + $bd + $Platform;

          $report['other_cost'] = $other_cost;

          $cost_campaign = $report['cost_campaign'];
          $pnl = $share - ($cost_campaign + $other_cost);

          $report['pnl'] = $pnl;

          $Funreports[] = $report;
        }
      }
    }

    return $Funreports;
  }

  public function Columnformula($repots)
  {
    $Funreports = array();
    if(!empty($repots))
    {
      foreach ($repots as $key => $report)
      {
        $ratio_for_cpa =0 ;
        $cpa_price = 0;
        $cr_mo_clicks = 0;
        $cr_mo_landing = 0;
        $price_mo = 0 ;
        $sbaf = $report['sbaf'];
        $landing = $report['landing'];
        // Click same as mo_postback
        $report['clicks'] = $click = $report['mo_postback'];
        // cost_campaign same as saaf
        $report['cost_campaign']  = $cost_campaign = $report['saaf'];
        // mo same as mo_received
        $report['mo'] = $mo_received = $report['mo_received'];

        //ratio_for_cpa (clicks > 0) ? (mo_received / clicks) : 0;

        if($click > 0)
        {
          $ratio_for_cpa = ($mo_received /$click );
        }

        $report['ratio_for_cpa'] = $ratio_for_cpa;

        /* End ratio_for_cpa */

        //cpa_price (clicks > 0) ? (sbaf / clicks) : 0;

        if($click > 0)
        {
          $cpa_price = ($sbaf /$click );
        }

        $report['cpa_price'] = $cpa_price;

        /* End cpa_price */

        //cr_mo_clicks (clicks == 0) ? 0 : ((mo / clicks) * 100);
        // mo_received ~ mo 
        if($click > 0)
        {
          $cr_mo_clicks = ($mo_received / $click ) * 100;
        }

        $report['cr_mo_clicks'] = $cr_mo_clicks;

        //cr_mo_landing (landing == 0) ? 0 : ((mo / landing) *100);

        if($landing > 0)
        {
          $cr_mo_landing = (($mo_received /$landing )*100);
        }

        $report['cr_mo_landing'] = $cr_mo_landing;

        //price_mo		(mo > 0) ? (cost_campaign / mo) : 0;	
        //stored in db like floor(price_mo *100)/100 

        if($mo_received > 0)
        {
          $price_mo = floor(((($cost_campaign/$mo_received )*100)/100));
        }

        $report['price_mo'] = $price_mo;

        $Funreports[$key] = $report;
      }
    }

    return $Funreports;
  }

  function CalMatchedData($matchedReports)
  {
    $sumRecordsForOperator = array();

    if(!empty($matchedReports))
    {
      foreach ($matchedReports as $key => $value)
      {
        if(isset($sumRecordsForOperator[$value['id_operator']]))
        {
          $value['exist'] = 1;
          $id_operator = $value['id_operator'];

          $sumRecordsForOperator[$id_operator]['mo_received'] += $value['mo_received'];
          $sumRecordsForOperator[$id_operator]['mo_postback'] += $value['mo_postback'];
          $sumRecordsForOperator[$id_operator]['landing'] += $value['landing'];
          $sumRecordsForOperator[$id_operator]['cr_mo_received'] += $value['cr_mo_received'];
          $sumRecordsForOperator[$id_operator]['cr_mo_postback'] += $value['cr_mo_postback'];
          $sumRecordsForOperator[$id_operator]['sbaf'] += $value['sbaf'];
          $sumRecordsForOperator[$id_operator]['saaf'] += $value['saaf'];
          $sumRecordsForOperator[$id_operator]['payout'] += $value['payout'];
        }
        else
        {
          $sumRecordsForOperator[$value['id_operator']] = $value;
        }
      }

      return $sumRecordsForOperator;
    }
  }

  function UnCalMatchedData($matchedReports)
  {
    $sumRecordsForOperator = array();

    if(!empty($matchedReports))
    {
      foreach ($matchedReports as $key => $value)
      {
        if(isset($sumRecordsForOperator[$value['operator']]))
        {
          $value['exist'] = 1;
          $id_operator = $value['operator'];

          $sumRecordsForOperator[$id_operator]['mo_received'] += $value['mo_received'];
          $sumRecordsForOperator[$id_operator]['mo_postback'] += $value['mo_postback'];
          $sumRecordsForOperator[$id_operator]['landing'] += $value['landing'];
          $sumRecordsForOperator[$id_operator]['cr_mo_received'] += $value['cr_mo_received'];
          $sumRecordsForOperator[$id_operator]['cr_mo_postback'] += $value['cr_mo_postback'];
          $sumRecordsForOperator[$id_operator]['sbaf'] += $value['sbaf'];
          $sumRecordsForOperator[$id_operator]['saaf'] += $value['saaf'];
          $sumRecordsForOperator[$id_operator]['payout'] += $value['payout'];
        }
        else
        {
          $sumRecordsForOperator[$value['operator']] = $value;
        }
      }

      return $sumRecordsForOperator;
    }
  }
}
