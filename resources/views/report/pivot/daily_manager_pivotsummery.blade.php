@extends('layouts.admin')

@section('title')
    {{ __('Pivot Summary') }}
@endsection

@section('content')
<div class="page-content">
    <div class="page-title" style="margin-bottom:25px">
      <div class="row justify-content-between align-items-center">
        <div
          class="col-xl-4 col-lg-4 col-md-4 d-flex align-items-center justify-content-between justify-content-md-start mb-3 mb-md-0">
          <div class="d-inline-block">
            <!-- <h5 class="h4 d-inline-block font-weight-400 mb-0 "> PNL Summary -->
            </h5>
            <div>Pivot Summary of Campaign Data</div>
          </div>
        </div>
      </div>
    </div>


    @include('report.partials.filterPivotReport')

    @if(isset($sumemry) && !empty($sumemry))
    <div class="card shadow-sm mt-0">
      <div class="card-body">
        <div class="row">
          <div class="col-lg-3">
            <label>Show Data Base On:</label>
          </div>
          <div class="col-lg-4">
            <div class="form-group">
              <select class="simple-multiple-select select2" name="sorting_pnl_orders" id="sorting_pnl_orders" style="width: 100%" data-select2-id="select2-data-sorting_pnl_orders" tabindex="-1" aria-hidden="true">
                <option value="">Please Select</option>
                <option value="highest_pnl">Highest PNL</option>
                <option value="lowest_pnl">Lowest PNL</option>
                <option value="highest_cost_campaign">Highest Cost Campaign</option>
                <option value="lowest_cost_campaign">Lowest Cost Campaign</option>
                <option value="highest_mo">Highest MO</option>
                <option value="lowest_mo">Lowest MO</option>
                <option value="highest_usd_end_user_revenue" selected="selected" data-select2-id="select2-data-10-ayb7">Highest End User Revenue(USD)</option>
                <option value="lowest_usd_end_user_revenue">Lowest End User Revenue(USD)</option>
                <option value="highest_roi">Highest ROI</option>
                <option value="lowest_roi">Lowest ROI</option>
              </select>
            </div>
          </div>
          <div class="col-lg-4">
              <div class="form-group">
                <button type="button" class="btn btn-primary pivot_submit"><i class="fa fa-sort"></i> Sort</button>
              </div>
          </div>
        </div>
        @if(isset($sumemry) && !empty($sumemry))
        @foreach ($sumemry as $report)
        @endforeach
        @endif
        <div class="d-flex align-items-center my-3">
          <span class="badge badge-with-flag badge-secondary px-2 bg-primary text-uppercase">
            All <?php echo ucwords(str_replace('_',' ',$data['ReportType'])) ?> {{isset($report['month_string']) ? $report['month_string'] : ''}} </span>
          <span class="flex-fill ml-2 bg-primary w-100 font-weight-bold" style="height: 1px;"></span>
          <div class="text-right pl-2">
            <button class="btn btn-sm pnl-xls" style="color:white; background-color:green" data-param="all"><i
                class="fa fa-file-excel-o"></i>Export as XLS</button>
          </div>
        </div>
        <div class="card" style="margin-bottom: 0px">
          <div class="table-responsive shadow-sm" id="all">
            
            <table class="table table-light m-0 font-13 table-text-no-wrap" id="pnlTbl">
              <thead class="thead-dark">
                <tr>
                  <th>Summary</th>
                  <th>Total</th>
                  <th>AVG</th>
                  <th>T.Mo.End</th>
                  
                  @if(isset($no_of_days) && !empty($no_of_days))
                  @foreach ($no_of_days as $days)
                    <th>{{$days['no']}}</th>
                  @endforeach
                  @endif
                </tr>
              </thead>
              <tbody>
                @if(isset($sumOfSummaryData) && !empty($sumOfSummaryData))
                @foreach ($sumOfSummaryData as $key =>$end_user_rev_usd)
                @if($key == 'end_user_rev_usd' && (in_array("revenue", $data['report_column'])))
                <tr class="bg-young-blue end_user_revenue">
                  <td class="font-weight-bold">
                    <div class="text-with-sup">
                      <div class="text-left"><strong>End User Revenue (USD)</strong></div>
                    </div>
                  </td>
                  <td class="gross_revenue_usd_total">{{numberConverter( $end_user_rev_usd['total'],2,'pre') }}</td>
                  <td class="gross_revenue_usd_avg">{{numberConverter( $end_user_rev_usd['avg'],2,'pre') }}</td>
                  <td class="gross_revenue_usd_month">{{numberConverter( $end_user_rev_usd['t_mo_end'],2,'pre') }}</td>
                  @if(isset($end_user_rev_usd['dates']) && !empty($end_user_rev_usd['dates']))
                  @foreach ($end_user_rev_usd['dates'] as $rev_usd)
                      <td class="gross_revenue_usd_data">{{numberConverter( $rev_usd['value'],2,'pre') }}</td>
                  @endforeach
                  @endif
                </tr>
                @endif
                @endforeach
                @endif

                @if(isset($sumOfSummaryData) && !empty($sumOfSummaryData))
                @foreach ($sumOfSummaryData as $key =>$gros_rev_usd)
                @if($key == 'gros_rev_usd' && (in_array("revenue", $data['report_column'])))
                <tr class="bg-young-blue gross_revenue">
                  <td class="font-weight-bold">
                    <div class="text-with-sup">
                      <strong class="text-with-sup">Gross Revenue (USD)<sup><i
                            class="ml-3 text-dark fa fa-info-circle" title="Revenue after share"></i></sup></strong>
                    </div>
                  </td>
                  <td class="share_total">{{numberConverter( $gros_rev_usd['total'],2,'pre') }}</td>
                  <td class="share_avg">{{numberConverter( $gros_rev_usd['avg'],2,'pre') }}</td>
                  <td class="share_month">{{numberConverter( $gros_rev_usd['t_mo_end'],2,'pre') }}</td>

                  @if(isset($gros_rev_usd['dates']) && !empty($gros_rev_usd['dates']))
                  @foreach ($gros_rev_usd['dates'] as $gros_rev)
                      <td class="share_data">{{numberConverter( $gros_rev['value'],2,'pre') }}</td>
                  @endforeach
                  @endif
                </tr>
                @endif
                @endforeach
                @endif


                @if(isset($sumOfSummaryData) && !empty($sumOfSummaryData))
                @foreach ($sumOfSummaryData as $key =>$cost_campaign)
                @if($key == 'cost_campaign' && (in_array("cost_campaign", $data['report_column'])))
                <tr class="bg-young-red cost_campaign">
                  <td class="font-weight-bold">
                    <div class="text-with-sup">
                      <div class="text-left"><strong>Cost Campaign (USD)</strong></div>
                    </div>
                  </td>
                  <td class="cost_total">{{numberConverter( $cost_campaign['total'],2,'pre') }}</td>
                  <td class="cost_avg">{{numberConverter( $cost_campaign['avg'],2,'pre') }}</td>
                  <td class="cost_month">{{numberConverter( $cost_campaign['t_mo_end'],2,'pre') }}</td>
                  
                  @if(isset($cost_campaign['dates']) && !empty($cost_campaign['dates']))
                  @foreach ($cost_campaign['dates'] as $costcampaign)
                    <td class="cost_data">{{numberConverter( $costcampaign['value'],2,'pre') }}</td>
                  @endforeach
                  @endif
                </tr>
                @endif
                @endforeach
                @endif


                @if(isset($sumOfSummaryData) && !empty($sumOfSummaryData))
                @foreach ($sumOfSummaryData as $key =>$mo)
                @if($key == 'mo' && (in_array("mo", $data['report_column'])))
                <tr class="bg-young-blue mo">
                  <td class="font-weight-bold">
                    <div class="text-with-sup">
                      <div class="text-left"><strong>MO</strong></div>
                    </div>
                  </td>
                  <td class="cost_total">{{numberConverter( $mo['total'],2,'pre') }}</td>
                  <td class="cost_avg">{{numberConverter( $mo['avg'],2,'pre') }}</td>
                  <td class="cost_month">{{numberConverter( $mo['t_mo_end'],2,'pre') }}</td>
                  
                  @if(isset($mo['dates']) && !empty($mo['dates']))
                  @foreach ($mo['dates'] as $mo1)
                    <td class="cost_data">{{numberConverter( $mo1['value'],2,'pre') }}</td>
                  @endforeach
                  @endif
                </tr>
                @endif
                @endforeach
                @endif


                @if(isset($sumOfSummaryData) && !empty($sumOfSummaryData))
                @foreach ($sumOfSummaryData as $key =>$roi)
                @if($key == 'roi' && (in_array("roi", $data['report_column'])))
                <tr class="bg-young-red roi">
                  <td class="font-weight-bold">
                    <div class="text-with-sup">
                      <div class="text-left"><strong>ROI</strong></div>
                    </div>
                  </td>
                  <td class="cost_total">{{numberConverter( $roi['total'],2,'pre') }}</td>
                  <td class="cost_avg">{{numberConverter( $roi['avg'],2,'pre') }}</td>
                  <td class="cost_month">{{numberConverter( $roi['t_mo_end'],2,'pre') }}</td>
                  
                  @if(isset($roi['dates']) && !empty($roi['dates']))
                  @foreach ($roi['dates'] as $roi1)
                    <td class="cost_data">{{numberConverter( $roi1['value'],2,'pre') }}</td>
                  @endforeach
                  @endif
                </tr>
                @endif
                @endforeach
                @endif


                @if(isset($sumOfSummaryData) && !empty($sumOfSummaryData))
                @foreach ($sumOfSummaryData as $key =>$pnl)
                @if($key == 'pnl' && (in_array("pnl", $data['report_column'])))
                <tr class="bg-young-green pnl">
                  <td class="font-weight-bold"><strong class="text-with-sup">PNL<sup><i
                          class="ml-3 text-dark fa fa-info-circle"
                          title="PNL = Revenue After Telco - (Cost Campaign + Hosting + Content + rnd + md + platform)"></i></sup></strong>
                  </td>
                  <td class="pnl_total">{{numberConverter( $pnl['total'],2,'pre') }}</td>
                  <td class="pnl_avg">{{numberConverter( $pnl['avg'],2,'pre') }}</td>
                  <td class="pnl_month">{{numberConverter( $pnl['t_mo_end'],2,'pre') }}</td>
                  
                  @if(isset($pnl['dates']) && !empty($pnl['dates']))
                  @foreach ($pnl['dates'] as $pnldata)
                      <td class="pnl_data">{{numberConverter( $pnldata['value'],2,'pre') }}</td>
                  @endforeach
                  @endif
                </tr>
                @endif
                @endforeach
                @endif


                @if(isset($sumOfSummaryData) && !empty($sumOfSummaryData))
                @foreach ($sumOfSummaryData as $key => $bill)
                @if($key == 'bill' && (in_array("bill_rate", $data['report_column'])))

                <tr class="bill">
                  <td>
                    <strong class="text-with-sup">
                      <div class="billSeperateBtn pull-left" data-sign="plus" style="cursor: pointer;">+</div>
                      <div class="text-left">Bill % </div><sup><i class="ml-3 text-dark fa fa-info-circle"
                          title="(Success Charge / Renewal) * 100%"></i></sup>
                    </strong>
                    <div class="billDivs" data-sign="plus"></div>
                  </td>
                  <td class="total_br_msg">N/A</td>
                  <td class="br_avg">{{numberConverter($bill['avg'],2,'post','%')}}</td>
                  <td class="br_monthly_msg">N/A</td>

                  @if(isset($bill['dates']) && !empty($bill['dates']))
                  @foreach ($bill['dates'] as $bill1)

                  <td class="br_data ">{{numberConverter($bill1['value'],2,'post','%')}}</td>
                  <!-- <td class="br_data bg-success text-white">3.55%</td>
                  <td class="br_data ">3.10%</td> -->
                  @endforeach
                  @endif

                </tr>

                @endif
                @endforeach
                @endif


                @if(isset($sumOfSummaryData) && !empty($sumOfSummaryData))
                @foreach ($sumOfSummaryData as $key => $first_push)
                @if($key == 'first_push' && (in_array("bill_rate", $data['report_column'])))

                <tr class="billExtendedRows" style="border:5px solid #0c54a0; border-bottom:none; display: none;">
                  <td>
                    <strong class="text-with-sup">First P.%<sup><i class="ml-3 text-dark fa fa-info-circle"
                          title="((First Push Success Charge / Total Sent) * 100% ) / total service count of each operator"></i></sup></strong>

                  </td>
                  <td class="total_first_push_total">N/A</td>
                  <td class="first_push_avg">{{numberConverter($first_push['avg'],2,'post','%')}}</td>
                  <td class="monthly_first_push_t_mo_end">N/A</td>

                  @if(isset($first_push['dates']) && !empty($first_push['dates']))
                  @foreach ($first_push['dates'] as $firstpush)

                  <td class="first_push_data ">{{numberConverter($firstpush['value'],2,'post','%')}}</td>
                  <!-- <td class="first_push_data bg-success text-white">28.70%</td>
                  <td class="first_push_data ">10.10%</td> -->
                  @endforeach
                  @endif
                </tr>

                @endif
                @endforeach
                @endif


                @if(isset($sumOfSummaryData) && !empty($sumOfSummaryData))
                @foreach ($sumOfSummaryData as $key => $daily_push)
                @if($key == 'daily_push' && (in_array("bill_rate", $data['report_column'])))

                <tr class="billExtendedRows" style="border:5px solid #0c54a0; border-top:none; display: none;">
                  <td>
                    <strong class="text-with-sup">Daily P.%<sup><i class="ml-3 text-dark fa fa-info-circle"
                          title="((Daily Push Success Charge / Total Sent) * 100% ) / total service count of each operator"></i></sup></strong>

                  </td>
                  <td class="total_daily_push_total">N/A</td>
                  <td class="daily_push_avg">{{numberConverter($daily_push['avg'],2,'post','%')}}</td>
                  <td class="monthly_daily_push_t_mo_end">N/A</td>

                  @if(isset($daily_push['dates']) && !empty($daily_push['dates']))
                  @foreach ($daily_push['dates'] as $dailypush)

                  <td class="daily_push_data ">{{numberConverter($dailypush['value'],2,'post','%')}}</td>
                  <!-- <td class="daily_push_data bg-success text-white">28.70%</td>
                  <td class="daily_push_data ">10.10%</td> -->
                  @endforeach
                  @endif
                </tr>

                @endif
                @endforeach
                @endif


                @if(isset($sumOfSummaryData) && !empty($sumOfSummaryData))
                @foreach ($sumOfSummaryData as $key => $usarpu7)
                @if($key == 'usarpu7'  && (in_array("arpu", $data['report_column'])))

                <tr class="bg-young-blue arpu7">
                  <td>
                    <strong class="text-with-sup">
                      <div class="arpuSeperateBtn pull-left" data-sign="plus" style="cursor: pointer;">+</div>
                      <div class="text-left">US7ARPU</div><sup><i class="ml-3 text-dark fa fa-info-circle"
                          title="Total USD Revenue After Telco (Last 7 Days) / (Total Reg Last 7 Days + Total Subs Active Current Day)"></i></sup>
                    </strong>

                  </td>
                  <td class="arpu_usd_30_msg">N/A</td>
                  <td class="arpu_usd_30_avg">{{numberConverter($usarpu7['avg'],2,'pre')}}</td>
                  <td class="arpu_usd_30_msg">N/A</td>

                  @if(isset($usarpu7['dates']) && !empty($usarpu7['dates']))
                  @foreach ($usarpu7['dates'] as $usarpu)

                  <td class="usd_arpu30_data ">{{numberConverter($usarpu['value'],2,'pre')}}</td>
                  <!-- <td class="usd_arpu30_data bg-success text-white">0.018</td>
                  <td class="usd_arpu30_data ">0.018</td> -->
                  @endforeach
                  @endif
                </tr>

                @endif
                @endforeach
                @endif


                @if(isset($sumOfSummaryData) && !empty($sumOfSummaryData))
                @foreach ($sumOfSummaryData as $key => $usarpu30)
                @if($key == 'usarpu30' && (in_array("arpu", $data['report_column'])))

                <tr class="arpuExtendedRows" style="border:5px solid #0c54a0; border-top:none; display: none;">
                  <td><strong>US30ARPU</strong></td>
                  <td class="arpu_usd_30_msg">N/A</td>
                  <td class="arpu_usd_30_avg">{{numberConverter($usarpu30['avg'],2,'pre')}}</td>
                  <td class="arpu_usd_30_msg">N/A</td>

                  @if(isset($usarpu30['dates']) && !empty($usarpu30['dates']))
                  @foreach ($usarpu30['dates'] as $usarpu_30)

                  <td class="usd_arpu30_data ">{{numberConverter($usarpu_30['value'],2,'pre')}}</td>
                  <!-- <td class="usd_arpu30_data bg-success text-white">0.018</td>
                  <td class="usd_arpu30_data ">0.018</td> -->
                  @endforeach
                  @endif
                </tr>

                @endif
                @endforeach
                @endif                
                
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    @endif





    @if(isset($sumemry) && !empty($sumemry))
    @foreach ($sumemry as $report)
    
    <div class="ptable">
      <div class="d-flex align-items-center my-3">
        <span class="badge badge-with-flag badge-secondary px-2 bg-primary text-uppercase">
          <a href="javascript:void(0);" class="text-white"> 
                    {{$report['account_manager']['name']}} </a>
                    {{$report['month_string']}} | Last Update: {{$report['last_update']}} UTC </span>
        <span class="flex-fill ml-2 bg-primary w-100 font-weight-bold" style="height: 1px;"></span>
        <div class="text-right pl-2">
          <button class="btn btn-sm pnl-xls" style="color:white; background-color:green" data-param="Indonesia"><i
              class="fa fa-file-excel-o"></i>Export as XLS</button>
        </div>
      </div>
      <div class="card">
        <div class="table-responsive shadow-sm pnlDataTbl" id="Indonesia">
          <h1 style="display:none">PNL Summary For Indonesia, Nov 2022</h1>
          <table class="table table-light m-0 font-13 table-text-no-wrap" id="pnlTbl">
            <thead class="thead-dark">
              <tr>
                <th>Summary</th>
                <th>Total</th>
                <th>AVG</th>
                <th>T.Mo.End</th>
                <!-- <th>04</th>
                <th>03</th>
                <th>02</th>
                <th>01</th> -->
                @if(isset($no_of_days) && !empty($no_of_days))
                @foreach ($no_of_days as $days)
                  <th>{{$days['no']}}</th>
                @endforeach
                @endif
              </tr>
            </thead>
            <tbody>

              @if(in_array("revenue", $data['report_column']))
              <tr class="bg-young-blue end_user_revenue">
                <td class="font-weight-bold">
                  <div class="text-with-sup">
                    <div class="btn-ico-expand grev_plus" data-sign="plus" style="cursor: pointer;">+</div>
                    <div class="text-left"><strong>End User Revenue (USD)</strong></div>
                  </div>
                </td>
                <td class="gross_revenue_usd_total usd">{{numberConverter($report['end_user_rev_usd']['total'],2,'pre')}} </td>
                <td class="gross_revenue_usd_avg">{{numberConverter($report['end_user_rev_usd']['avg'],2,'pre')}}</td>
                <td class="gross_revenue_usd_month">{{numberConverter($report['end_user_rev_usd']['t_mo_end'],2,'pre')}}</td>
                @if(isset($report['end_user_rev_usd']['dates']) && !empty($report['end_user_rev_usd']['dates']))
                @foreach ($report['end_user_rev_usd']['dates'] as $end_user_rev_usd)
                    <td class="gross_revenue_usd_data {{$end_user_rev_usd['class']}}">{{numberConverter($end_user_rev_usd['value'],2,'pre')}}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-young-blue hiddenRevTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="font-weight: bolder; position: relative; left: 30px;">End User Revenue (IDR)</div>
                </td>
                <td class="gross_revenue_total">{{numberConverter( $report['end_user_rev']['total'],2,'pre') }}</td>
                <td class="gross_revenue_avg">{{numberConverter( $report['end_user_rev']['avg'],2,'pre') }}</td>
                <td class="gross_revenue_month">{{numberConverter( $report['end_user_rev']['t_mo_end'],2,'pre') }}</td>
                
                @if(isset($report['end_user_rev']['dates']) && !empty($report['end_user_rev']['dates']))
                @foreach ($report['end_user_rev']['dates'] as $end_user_rev)
                    <td class="gross_revenue_data">{{numberConverter( $end_user_rev['value'],2,'pre') }}</td>
                @endforeach
                @endif
                
              </tr>

              <tr class="bg-young-blue gross_revenue">
                <td class="font-weight-bold">
                  <div class="text-with-sup">
                    <div class="btn-ico-expand gross_rev_usd_plus" data-sign="plus" style="cursor: pointer;">+</div>
                    <strong class="text-with-sup">Gross Revenue (USD)<sup><i
                          class="ml-3 text-dark fa fa-info-circle" title="Revenue after share"></i></sup></strong>
                  </div>
                </td>
                <td class="share_total">{{numberConverter( $report['gros_rev_usd']['total'],2,'pre') }}</td>
                <td class="share_avg">{{numberConverter( $report['gros_rev_usd']['avg'],2,'pre') }}</td>
                <td class="share_month">{{numberConverter( $report['gros_rev_usd']['t_mo_end'],2,'pre') }}</td>
                
                @if(isset($report['gros_rev_usd']['dates']) && !empty($report['gros_rev_usd']['dates']))
                @foreach ($report['gros_rev_usd']['dates'] as $gros_rev_usd)
                    <td class="share_data">{{numberConverter( $gros_rev_usd['value'],2,'pre') }}</td>
                @endforeach
                @endif    
                
              </tr>

              <tr class="bg-young-blue hiddenGrossRevUsdTr" style="display:none;">
                <td class="font-weight-bold">
                  <div style="font-weight: bolder; position: relative; left: 30px;">Gross Revenue (IDR)</div>
                </td>
                <td class="local_share_total">{{numberConverter( $report['gros_rev']['total'],2,'pre') }}</td>
                <td class="local_share_avg">{{numberConverter( $report['gros_rev']['avg'],2,'pre') }}</td>
                <td class="local_share_month">{{numberConverter( $report['gros_rev']['t_mo_end'],2,'pre') }}</td>

                @if(isset($report['gros_rev']['dates']) && !empty($report['gros_rev']['dates']))
                @foreach ($report['gros_rev']['dates'] as $gros_rev)
                    <td class="local_share_data">{{numberConverter( $gros_rev['value'],2,'pre') }}</td>
                @endforeach
                @endif
              </tr>
              @endif

              @if(in_array("cost_campaign", $data['report_column']))
              <tr class="bg-young-red cost_campaign">
                <td class="font-weight-bold">
                  <div class="text-with-sup">
                    <div class="text-left"><strong>Cost Campaign (USD)</strong></div>
                  </div>
                </td>
                <td class="cost_total cost">{{numberConverter( $report['cost_campaign']['total'],2,'pre') }}</td>
                <td class="cost_avg">{{numberConverter( $report['cost_campaign']['avg'],2,'pre') }}</td>
                <td class="cost_month">{{numberConverter( $report['cost_campaign']['t_mo_end'],2,'pre') }}</td>

                @if(isset($report['cost_campaign']['dates']) && !empty($report['cost_campaign']['dates']))
                @foreach ($report['cost_campaign']['dates'] as $cost_campaign)
                    <td class="cost_data">{{numberConverter( $cost_campaign['value'],2,'pre') }}</td>
                @endforeach
                @endif
              </tr>
              @endif

              @if(in_array("mo", $data['report_column']))
              <tr class="bg-young-blue mo">
                <td class="font-weight-bold">
                  <div class="text-with-sup">
                    <div class="text-left"><strong>MO</strong></div>
                  </div>
                </td>
                <td class="cost_total cost">{{numberConverter( $report['mo']['total'],2,'pre') }}</td>
                <td class="cost_avg">{{numberConverter( $report['mo']['avg'],2,'pre') }}</td>
                <td class="cost_month">{{numberConverter( $report['mo']['t_mo_end'],2,'pre') }}</td>

                @if(isset($report['mo']['dates']) && !empty($report['mo']['dates']))
                @foreach ($report['mo']['dates'] as $mo)
                    <td class="cost_data">{{numberConverter( $mo['value'],2,'pre') }}</td>
                @endforeach
                @endif
              </tr>
              @endif

              @if(in_array("roi", $data['report_column']))
              <tr class="bg-young-red roi">
                <td class="font-weight-bold">
                  <div class="text-with-sup">
                    <div class="text-left"><strong>ROI</strong></div>
                  </div>
                </td>
                <td class="cost_total cost">{{numberConverter( $report['roi']['total'],2,'pre') }}</td>
                <td class="cost_avg">{{numberConverter( $report['roi']['avg'],2,'pre') }}</td>
                <td class="cost_month">{{numberConverter( $report['roi']['t_mo_end'],2,'pre') }}</td>

                @if(isset($report['roi']['dates']) && !empty($report['roi']['dates']))
                @foreach ($report['roi']['dates'] as $roi)
                    <td class="cost_data">{{numberConverter( $roi['value'],2,'pre') }}</td>
                @endforeach
                @endif
              </tr>
              @endif

              @if(in_array("pnl", $data['report_column']))
              <tr class="bg-young-green pnl">
                <td class="font-weight-bold"><strong class="text-with-sup">PNL<sup><i
                        class="ml-3 text-dark fa fa-info-circle"
                        title="PNL = Revenue After Telco - (Cost Campaign + Hosting + Content + rnd + md + platform)"></i></sup></strong>
                </td>
                <td class="pnl_total p">{{numberConverter( $report['pnl']['total'],2,'pre') }}</td>
                <td class="pnl_avg">{{numberConverter( $report['pnl']['avg'],2,'pre') }}</td>
                <td class="pnl_month">{{numberConverter( $report['pnl']['t_mo_end'],2,'pre') }}</td>
                
                @if(isset($report['pnl']['dates']) && !empty($report['pnl']['dates']))
                @foreach ($report['pnl']['dates'] as $pnl)
                    <td class="pnl_data">{{numberConverter( $pnl['value'],2,'pre') }}</td>
                @endforeach
                @endif    
                
              </tr>
              @endif


              @if(in_array("bill_rate", $data['report_column']))
              <tr class="bill">
                <td>
                  <strong class="text-with-sup">
                    <div class="billSeperateBtn pull-left" data-sign="plus" style="cursor: pointer;">+</div>
                    <div class="text-left">Bill % </div><sup><i class="ml-3 text-dark fa fa-info-circle"
                        title="(Success Charge / Renewal) * 100%"></i></sup>
                  </strong>
                  <div class="billDivs" data-sign="plus"></div>
                </td>
                <td class="total_br_msg">N/A</td>
                <td class="br_avg">{{numberConverter($report['bill']['avg'],2,'post','%')}}</td>
                <td class="br_monthly_msg">N/A</td>

                @if(isset($report['bill']['dates']) && !empty($report['bill']['dates']))
                @foreach ($report['bill']['dates'] as $bill1)

                <td class="br_data ">{{numberConverter($bill1['value'],2,'post','%')}}</td>
                <!-- <td class="br_data bg-success text-white">3.55%</td>
                <td class="br_data ">3.10%</td> -->
                @endforeach
                @endif

              </tr>


              <tr class="billExtendedRows" style="border:5px solid #0c54a0; border-bottom:none; display: none;">
                <td>
                  <strong class="text-with-sup">First P.%<sup><i class="ml-3 text-dark fa fa-info-circle"
                        title="((First Push Success Charge / Total Sent) * 100% ) / total service count of each operator"></i></sup></strong>

                </td>
                <td class="total_first_push_total">N/A</td>
                <td class="first_push_avg">{{numberConverter($report['first_push']['avg'],2,'post','%')}}</td>
                <td class="monthly_first_push_t_mo_end">N/A</td>

                @if(isset($report['first_push']['dates']) && !empty($report['first_push']['dates']))
                @foreach ($report['first_push']['dates'] as $firstpush)

                <td class="first_push_data ">{{numberConverter($firstpush['value'],2,'post','%')}}</td>
                <!-- <td class="first_push_data bg-success text-white">28.70%</td>
                <td class="first_push_data ">10.10%</td> -->
                @endforeach
                @endif
              </tr>


              <tr class="billExtendedRows" style="border:5px solid #0c54a0; border-top:none; display: none;">
                <td>
                  <strong class="text-with-sup">Daily P.%<sup><i class="ml-3 text-dark fa fa-info-circle"
                        title="((Daily Push Success Charge / Total Sent) * 100% ) / total service count of each operator"></i></sup></strong>

                </td>
                <td class="total_daily_push_total">N/A</td>
                <td class="daily_push_avg">{{numberConverter($report['daily_push']['avg'],2,'post','%')}}</td>
                <td class="monthly_daily_push_t_mo_end">N/A</td>

                @if(isset($report['daily_push']['dates']) && !empty($report['daily_push']['dates']))
                @foreach ($report['daily_push']['dates'] as $dailypush)

                <td class="daily_push_data ">{{numberConverter($dailypush['value'],2,'post','%')}}</td>
                <!-- <td class="daily_push_data bg-success text-white">28.70%</td>
                <td class="daily_push_data ">10.10%</td> -->
                @endforeach
                @endif
              </tr>
              @endif

              @if(in_array("arpu", $data['report_column']))
              <tr class="bg-young-blue arpu7">
                <td>
                  <strong class="text-with-sup">
                    <div class="arpuSeperateBtn pull-left" data-sign="plus" style="cursor: pointer;">+</div>
                    <div class="text-left">US7ARPU</div><sup><i class="ml-3 text-dark fa fa-info-circle"
                        title="Total USD Revenue After Telco (Last 7 Days) / (Total Reg Last 7 Days + Total Subs Active Current Day)"></i></sup>
                  </strong>

                </td>
                <td class="arpu_usd_30_msg">N/A</td>
                <td class="arpu_usd_30_avg">{{numberConverter($report['usarpu7']['avg'],2,'pre')}}</td>
                <td class="arpu_usd_30_msg">N/A</td>

                @if(isset($report['usarpu7']['dates']) && !empty($report['usarpu7']['dates']))
                @foreach ($report['usarpu7']['dates'] as $usarpu)

                <td class="usd_arpu30_data ">{{numberConverter($usarpu['value'],2,'pre')}}</td>
                
                @endforeach
                @endif
              </tr>


              <tr class="arpuExtendedRows" style="border:5px solid #0c54a0; border-top:none; display: none;">
                <td><strong>US30ARPU</strong></td>
                <td class="arpu_usd_30_msg">N/A</td>
                <td class="arpu_usd_30_avg">{{numberConverter($report['usarpu30']['avg'],2,'pre')}}</td>
                <td class="arpu_usd_30_msg">N/A</td>

                @if(isset($report['usarpu30']['dates']) && !empty($report['usarpu30']['dates']))
                @foreach ($report['usarpu30']['dates'] as $usarpu_30)

                <td class="usd_arpu30_data ">{{numberConverter($usarpu_30['value'],2,'pre')}}</td>
                <!-- <td class="usd_arpu30_data bg-success text-white">0.018</td>
                <td class="usd_arpu30_data ">0.018</td> -->
                @endforeach
                @endif
              </tr>
              @endif
            </tbody>
          </table>
        </div>
      </div>
    </div>

    @endforeach
    @endif





  </div>
@endsection
