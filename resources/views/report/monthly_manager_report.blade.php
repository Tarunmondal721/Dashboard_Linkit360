@extends('layouts.admin')

@section('title')

    {{ isset($BusinessTypeWise) ? 'Report Business Wise Summary' : 'Report Account Manager Summary' }}

@endsection

@section('content')
<div class="page-content">
      <div class="page-title" style="margin-bottom:25px">
        <div class="row justify-content-between align-items-center">
          <div
            class="col-xl-4 col-lg-4 col-md-4 d-flex align-items-center justify-content-between justify-content-md-start mb-3 mb-md-0">
            <div class="d-inline-block">
              <h5 class="h4 d-inline-block font-weight-400 mb-0"><b>Report Summary</b></h5><br>
              <p class="d-inline-block font-weight-200 mb-0">{{ isset($BusinessTypeWise) ? 'Summary of Business Wise' : 'Summary of Account Manager Data'  }}</p>
            </div>
          </div>
          <div
            class="col-xl-8 col-lg-8 col-md-8 d-flex align-items-center justify-content-between justify-content-md-end">
          </div>
        </div>
      </div>

    @include('report.partials.filter')
    @include('report.partials.graphMonthly')

      @if(isset($no_of_days) && !empty($no_of_days))
      @foreach ($no_of_days as $days)
      @endforeach
      @endif

        <div id="reportXls">
        <div id="container">
        <div class="d-flex align-items-center my-3">
            <span class="badge badge-secondary px-2 bg-primary text-uppercase">
                <a href="" class="text-white"> {{ isset($BusinessTypeWise) ? 'ALL BUSINESS' : 'ALL ACCOUNT MANAGER' }} </a>{{ isset($days['year']) ? $days['year'] : ''}}
            </span>
            <span class="flex-fill ml-2 bg-primary w-100 font-weight-bold" style="height: 1px;"></span>
            <div class="text-right pl-2">
                <button class="btn btn-sm report-xls" style="color:white; background-color:green" data-param="all"><i class="fa fa-file-excel-o"></i>Export as XLS</button>
            </div>
        </div>

        <div class="row justify-content-between align-items-center">
          <div class="col-md-12 ">
            <div class="">
              <div class="card">
                <div class="table-responsive  table-striped" id="all">
                  <h1 style="display:hidden"></h1>
                  <table class="table table-light table-striped m-0 font-13 all" id="dtbl">
                    <thead class="thead-dark">
                      <tr>
                        <th class="align-middle">Summary</th>
                        <th class="align-middle">Total</th>
                        <th class="align-middle">AVG</th>
                        <th class="align-middle">T.MO.Year</th>
                        <!-- <th class="align-middle">03</th>
                        <th class="align-middle">02</th>
                        <th class="align-middle">01</th> -->
                        @if(isset($no_of_days) && !empty($no_of_days))
                        @foreach ($no_of_days as $days)
                          <th class="align-middle">{{$days['no']}}</th>
                        @endforeach
                        @endif
                      </tr>
                    </thead>
                    <tbody>

                      @if(isset($allsummaryData) && !empty($allsummaryData))
                      @foreach ($allsummaryData as $key => $tur)
                      @if($key == 'tur')

                      <tr class="gradient text-white rev">
                        <td>
                          <strong class="text-with-sup">
                            <div class="text-left">GMV (USD)
                              <sup><i class="ml-3 text-dark fa fa-info-circle" title="Revenue that we charge user excluding tax"></i></sup>
                            </div>
                          </strong>
                        </td>

                        <td class="revenue_total" data-country="" data-local-currency="USD" data-usd="1"><span class="local_total_revenue">{{numberConverter($tur['total'] ,2,'pre') }}(USD)</span></td>
                        <td class="revenue_avg">{{numberConverter($tur['avg'] ,2,'pre') }}</td>
                        <td class="revenue_monthly">{{numberConverter($tur['t_mo_end'] ,2,'pre') }}</td>

                        @if(isset($tur['dates']) && !empty($tur['dates']))
                        @foreach ($tur['dates'] as $tur1)

                        <td class="gross_revenue ">{{numberConverter($tur1['value'] ,2,'pre') }}</td>
                        <!-- <td class="gross_revenue bg-danger text-white">30,450.33</td> -->
                        <!-- <td class="gross_revenue ">30,888.11</td> -->
                        @endforeach
                        @endif
                      </tr>

                      @endif
                      @endforeach
                      @endif


                      {{-- @if(isset($allsummaryData) && !empty($allsummaryData))
                      @foreach ($allsummaryData as $key => $turt)
                      @if($key == 'turt')

                      <tr class="revExtendedRows"  style="border:5px solid #0c54a0; border-top:none; display: none;">
                        <td>
                          <strong class="text-with-sup">
                            <div class="revenueSeperateBtn pull-left" data-sign="plus"
                              style="cursor: pointer;min-width: 10px;"></div>
                            <div class="text-left">G.Rev (USD)
                              <sup><i class="ml-3 text-dark fa fa-info-circle" title="Total Revenue"></i></sup>
                            </div>
                          </strong>
                        </td>

                        <td class="revenue_total" data-country="" data-local-currency="USD" data-usd="1"><span class="local_total_revenue">{{numberConverter($turt['total'] ,2,'pre') }}(USD)</span></td>
                        <td class="revenue_avg">{{numberConverter($turt['avg'] ,2,'pre') }}</td>
                        <td class="revenue_monthly">{{numberConverter($turt['t_mo_end'] ,2,'pre') }}</td>

                        @if(isset($turt['dates']) && !empty($turt['dates']))
                        @foreach ($turt['dates'] as $turt1)

                        <td class="gross_revenue_share ">{{numberConverter($turt1['value'] ,2,'pre') }}</td>
                        <!-- <td class="gross_revenue bg-danger text-white">30,450.33</td> -->
                        <!-- <td class="gross_revenue ">30,888.11</td> -->
                        @endforeach
                        @endif
                      </tr>

                      @endif
                      @endforeach
                      @endif --}}

                      @if(isset($allsummaryData) && !empty($allsummaryData))
                      @foreach ($allsummaryData as $key => $net_rev)
                      @if($key == 'net_rev')
                      <tr class="subs">
                        <td><strong class="text-with-sup">N.REV(USD)<sup><i class="ml-3 text-dark fa fa-info-circle" title="Net Revenue = G.REV - other tax"></i></sup></strong></td>
                        <td class="revenue_total" data-country="" data-local-currency="USD" data-usd="1"><span class="local_total_revenue">{{numberConverter($net_rev['total'] ,2,'pre') }}</span></td>
                        <td class="subactive_avg">{{numberConverter($net_rev['avg'] ,2,'pre') }}</td>
                        <td class="subactive_monthly">{{numberConverter($net_rev['t_mo_end'] ,2,'pre') }}</td>

                        @if(isset($net_rev['dates']) && !empty($net_rev['dates']))
                        @foreach ($net_rev['dates'] as $netRev)
                        <td class="subactive_data ">{{numberConverter($netRev['value'] ,2,'pre') }}</td>
                        @endforeach
                        @endif
                      </tr>
                      @endif
                      @endforeach
                      @endif

                      @if(isset($allsummaryData) && !empty($allsummaryData))
                      @foreach ($allsummaryData as $key => $t_sub)
                      @if($key == 't_sub')

                      <tr class="subs">
                        <td><strong class="text-with-sup">T Subs <sup><i class="ml-3 text-dark fa fa-info-circle"
                                title="Total Subactive"></i></sup></strong></td>

                        <td class="revenue_total" data-country="" data-local-currency="USD" data-usd="1"><span class="local_total_revenue">{{numberConverter($t_sub['total'] ,2,'pre') }}</span></td>
                        {{-- <td class="revenue_avg">{{numberConverter($t_sub['avg'] ,2,'pre') }}</td>
                        <td class="revenue_monthly">{{numberConverter($t_sub['t_mo_end'] ,2,'pre') }}</td> --}}
                        <td class="subactive_avg">N/A</td>
                        <td class="subactive_monthly">N/A</td>

                        @if(isset($t_sub['dates']) && !empty($t_sub['dates']))
                        @foreach ($t_sub['dates'] as $tsub)

                        <td class="subactive_data ">{{numberConverter($tsub['value'] ,2,'pre') }}</td>
                        <!-- <td class="gross_revenue bg-danger text-white">30,450.33</td> -->
                        <!-- <td class="gross_revenue ">30,888.11</td> -->
                        @endforeach
                        @endif
                      </tr>

                      @endif
                      @endforeach
                      @endif

                      @if(isset($allsummaryData) && !empty($allsummaryData))
                      @foreach ($allsummaryData as $key => $reg)
                      @if($key == 'reg')

                      <tr class="reg">
                        <td><strong>Reg</strong></td>
                        <td class="reg_total">{{numberConverter($reg['total'] ,2,'pre') }}</td>
                        <td class="reg_avg">{{numberConverter($reg['avg'] ,2,'pre') }}</td>
                        <td class="reg_monthly">{{numberConverter($reg['t_mo_end'] ,2,'pre') }}</td>

                        @if(isset($reg['dates']) && !empty($reg['dates']))
                        @foreach ($reg['dates'] as $reg1)
                        <td class="reg_data ">{{numberConverter($reg1['value'] ,2,'pre') }}</td>
                        <!-- <td class="reg_data bg-success text-white">99,256</td>
                        <td class="reg_data ">90,165</td> -->
                        @endforeach
                        @endif
                      </tr>

                      @endif
                      @endforeach
                      @endif


                      @if(isset($allsummaryData) && !empty($allsummaryData))
                      @foreach ($allsummaryData as $key => $unreg)
                      @if($key == 'unreg')

                      <tr class="unreg">
                        <td><strong>Unreg</strong></td>
                        <td class="unreg_total">{{numberConverter($unreg['total'] ,2,'pre') }}</td>
                        <td class="unreg_avg">{{numberConverter($unreg['avg'] ,2,'pre') }}</td>
                        <td class="unreg_monthly">{{numberConverter($unreg['t_mo_end'] ,2,'pre') }}</td>

                        @if(isset($unreg['dates']) && !empty($unreg['dates']))
                        @foreach ($unreg['dates'] as $unreg1)

                        <td class="unreg_data ">{{numberConverter($unreg1['value'] ,2,'pre') }}</td>
                        <!-- <td class="unreg_data bg-success text-white">25,259</td>
                        <td class="unreg_data ">24,879</td> -->
                        @endforeach
                        @endif
                      </tr>

                      @endif
                      @endforeach
                      @endif


                      @if(isset($allsummaryData) && !empty($allsummaryData))
                      @foreach ($allsummaryData as $key => $purged)
                      @if($key == 'purged')

                      <tr class="purged">
                        <td><strong>Purged</strong></td>
                        <td class="purged_total">{{numberConverter($purged['total'] ,2,'pre') }}</td>
                        <td class="purged_avg">{{numberConverter($purged['avg'] ,2,'pre') }}</td>
                        <td class="purged_monthly">{{numberConverter($purged['t_mo_end'] ,2,'pre') }}</td>


                        @if(isset($purged['dates']) && !empty($purged['dates']))
                        @foreach ($purged['dates'] as $purged1)

                        <td class="purged_data ">{{numberConverter($purged1['value'] ,2,'pre') }}</td>
                        <!-- <td class="purged_data bg-success text-white">161</td>
                        <td class="purged_data ">47</td> -->
                        @endforeach
                        @endif
                      </tr>

                      @endif
                      @endforeach
                      @endif


                      @if(isset($allsummaryData) && !empty($allsummaryData))
                      @foreach ($allsummaryData as $key => $churn)
                      @if($key == 'churn')

                      <tr class="churn">
                         <!-- <td><strong>Churn %<sup style="position: relative; top:-29px; margin-left: 38px;"><i class="ml-3 text-dark fa fa-info-circle" title="(unreg/ tsubs) * 100"></i></sup></strong></td> -->
                        <td><strong class="text-with-sup">Churn % <sup><i class="ml-3 text-dark fa fa-info-circle"
                                title="(unreg/ tsubs) * 100"></i></sup></strong>
                        </td>
                        <td class="churn_total_total">N/A</td>
                        <td class="churn_avg">{{numberConverter($churn['avg'] ,2,'post','%') }}</td>
                        <td class="churn_monthly_t_mo_end">N/A</td>

                        @if(isset($churn['dates']) && !empty($churn['dates']))
                        @foreach ($churn['dates'] as $churn1)

                        <td class="churn_data ">{{numberConverter($churn1['value'] ,2,'post','%') }}</td>
                        <!-- <td class="churn_data bg-success text-white">0.46%</td>
                        <td class="churn_data ">0.41%</td> -->
                        @endforeach
                        @endif
                      </tr>

                      @endif
                      @endforeach
                      @endif


                      @if(isset($allsummaryData) && !empty($allsummaryData))
                      @foreach ($allsummaryData as $key => $renewal)
                      @if($key == 'renewal')

                      <tr class="renewal">
                        <td><div class="renewalSeperateBtn pull-left" data-sign="plus" style="cursor: pointer;">+</div><strong>Renewal</strong><div class="renewalDivs" data-sign="plus"></div></td>
                        <td class="all_total_sent">{{numberConverter($renewal['total'] ,2,'pre') }}</td>
                        <td class="all_total_sent_avg">{{numberConverter($renewal['avg'] ,2,'pre') }}</td>
                        <td class="all_total_sent_mon">{{numberConverter($renewal['t_mo_end'] ,2,'pre') }}</td>

                        @if(isset($renewal['dates']) && !empty($renewal['dates']))
                        @foreach ($renewal['dates'] as $renewal1)

                        <td class="total_sent_data ">{{numberConverter($renewal1['value'] ,2,'pre') }}</td>
                        <!-- <td class="total_sent_data bg-danger text-white">11,963,026</td>
                        <td class="total_sent_data ">12,595,008</td> -->
                        @endforeach
                        @endif

                      </tr>

                      @endif
                      @endforeach
                      @endif

                      @if(isset($allsummaryData) && !empty($allsummaryData))
                        @foreach ($allsummaryData as $key => $daily_push_success)
                        @if($key == 'daily_push_success')

                      <tr class="renewalExtendedRows" style="border:5px solid #0c54a0; border-bottom:none; display: none;">
                        <td>
                          <strong>Daily Push Success</strong>

                        </td>
                        <td class="total_daily_push_success_total">{{numberConverter($daily_push_success['total'],2,'post')}}</td>
                        <td class="daily_push_success_avg">{{numberConverter($daily_push_success['avg'],2,'post')}}</td>
                        <td class="monthly_daily_push_success_t_mo_end">{{numberConverter($daily_push_success['t_mo_end'],2,'post')}}</td>

                        @if(isset($daily_push_success['dates']) && !empty($daily_push_success['dates']))
                        @foreach ($daily_push_success['dates'] as $daily_push_success)

                        <td class="daily_push_success_data ">{{numberConverter($daily_push_success['value'],2,'post')}}</td>
                        <!-- <td class="first_push_data bg-success text-white">28.70%</td>
                        <td class="first_push_data ">10.10%</td> -->
                        @endforeach
                        @endif
                      </tr>

                      @endif
                      @endforeach
                      @endif


                      @if(isset($allsummaryData) && !empty($allsummaryData))
                      @foreach ($allsummaryData as $key => $daily_push_failed)
                      @if($key == 'daily_push_failed')

                      <tr class="renewalExtendedRows" style="border:5px solid #0c54a0; border-top:none; display: none;">
                        <td>
                          <strong>Daily Push Failed</strong>

                        </td>
                        <td class="total_daily_push_failed_total">{{numberConverter($daily_push_failed['total'],2,'post')}}</td>
                        <td class="daily_push_failed_avg">{{numberConverter($daily_push_failed['avg'],2,'post')}}</td>
                        <td class="monthly_daily_push_failed_t_mo_end">{{numberConverter($daily_push_failed['t_mo_end'],2,'post')}}</td>

                        @if(isset($daily_push_failed['dates']) && !empty($daily_push_failed['dates']))
                        @foreach ($daily_push_failed['dates'] as $daily_push_failed)

                        <td class="daily_push_failed_data ">{{numberConverter($daily_push_failed['value'],2,'post')}}</td>
                        <!-- <td class="daily_push_data bg-success text-white">28.70%</td>
                        <td class="daily_push_data ">10.10%</td> -->
                        @endforeach
                        @endif
                      </tr>

                      @endif
                      @endforeach
                      @endif

                      @if(isset($allsummaryData) && !empty($allsummaryData))
                      @foreach ($allsummaryData as $key => $bill)
                      @if($key == 'bill')

                      <tr class="bill">
                        <td>
                          <strong class="text-with-sup">
                            <div class="billSeperateBtn pull-left" data-sign="plus" style="cursor: pointer;">+</div>
                            <div class="text-left">Bill % </div><sup><i class="ml-3 text-dark fa fa-info-circle"
                                title="(total daily push delivered / total subscriber) * 100%"></i></sup>
                          </strong>
                          <div class="billDivs" data-sign="plus"></div>
                        </td>
                        <td class="total_br_msg">N/A</td>
                        <td class="br_avg">{{numberConverter($bill['avg'] ,2,'post') }}</td>
                        <td class="br_monthly_msg">N/A</td>

                        @if(isset($bill['dates']) && !empty($bill['dates']))
                        @foreach ($bill['dates'] as $bill1)

                        <td class="br_data ">{{numberConverter($bill1['value'] ,2,'post','%') }}</td>
                        <!-- <td class="br_data bg-success text-white">3.55%</td>
                        <td class="br_data ">3.10%</td> -->
                        @endforeach
                        @endif

                      </tr>

                      @endif
                      @endforeach
                      @endif


                      @if(isset($allsummaryData) && !empty($allsummaryData))
                      @foreach ($allsummaryData as $key => $first_push)
                      @if($key == 'first_push')

                      <tr class="billExtendedRows" style="border:5px solid #0c54a0; border-bottom:none; display: none;">
                        <td>
                          <strong class="text-with-sup">First P.%<sup><i class="ml-3 text-dark fa fa-info-circle"
                                title="(delivered first push / first push sent) * 100%"></i></sup></strong>

                        </td>
                        <td class="total_first_push_total">N/A</td>
                        <td class="first_push_avg">{{numberConverter($first_push['avg'] ,2,'post','%') }}</td>
                        <td class="monthly_first_push_t_mo_end">N/A</td>

                        @if(isset($first_push['dates']) && !empty($first_push['dates']))
                        @foreach ($first_push['dates'] as $firstpush)

                        <td class="first_push_data ">{{numberConverter($firstpush['value'] ,2,'post','%') }}</td>
                        <!-- <td class="first_push_data bg-success text-white">28.70%</td>
                        <td class="first_push_data ">10.10%</td> -->
                        @endforeach
                        @endif
                      </tr>

                      @endif
                      @endforeach
                      @endif


                      @if(isset($allsummaryData) && !empty($allsummaryData))
                      @foreach ($allsummaryData as $key => $daily_push)
                      @if($key == 'daily_push')

                      <tr class="billExtendedRows" style="border:5px solid #0c54a0; border-top:none; display: none;">
                        <td>
                          <strong class="text-with-sup">Daily P.%<sup><i class="ml-3 text-dark fa fa-info-circle"
                                title="(delivered daily push / daily push sent) * 100%"></i></sup></strong>

                        </td>
                        <td class="total_daily_push_total">N/A</td>
                        <td class="daily_push_avg">{{numberConverter($daily_push['avg'] ,2,'post','%') }}</td>
                        <td class="monthly_daily_push_t_mo_end">N/A</td>

                        @if(isset($daily_push['dates']) && !empty($daily_push['dates']))
                        @foreach ($daily_push['dates'] as $dailypush)

                        <td class="daily_push_data ">{{numberConverter($dailypush['value'] ,2,'post','%') }}</td>
                        <!-- <td class="daily_push_data bg-success text-white">28.70%</td>
                        <td class="daily_push_data ">10.10%</td> -->
                        @endforeach
                        @endif
                      </tr>

                      @endif
                      @endforeach
                      @endif

                      {{--
                      @if(isset($allsummaryData) && !empty($allsummaryData))
                      @foreach ($allsummaryData as $key => $arpu30)
                      @if($key == 'arpu30')

                      <tr class="arpu7">
                        <td>
                            <strong class="text-with-sup">
                                <div class="arpuSeperateBtn pull-left" data-sign="plus" style="cursor: pointer;">+</div>
                                <div class="text-left">30ARPU</div><sup><i class="ml-3 text-dark fa fa-info-circle"
                                title="Total USD Revenue After Telco (Last 30 Days) / (Total Reg Last 30 Days + Total Subs Active Current Day) "></i></sup></strong>

                        </td>
                        <td class="arpu_usd_seven_total">N/A</td>
                        <td class="arpu_usd_seven_avg">{{numberConverter($arpu30['avg'] ,4,'arpu30','') }}</td>
                        <td class="arpu_usd_seven_t_mo_end">N/A</td>

                        @if(isset($arpu30['dates']) && !empty($arpu30['dates']))
                        @foreach ($arpu30['dates'] as $arpu)

                        <td class="usd_arpu30_data ">{{numberConverter($arpu['value'] ,4,'arpu30','') }}</td>
                        <!-- <td class="usd_arpu7_data bg-success text-white">0.005</td>
                        <td class="usd_arpu7_data ">0.005</td> -->
                        @endforeach
                        @endif

                      </tr>

                      @endif
                      @endforeach
                      @endif
                      --}}


                       @if (isset($allsummaryData) && !empty($allsummaryData))
                                                @foreach ($allsummaryData as $key => $usarpu30)
                                                    @if ($key == 'usarpu30')
                                                        <tr class="usarpu30">
                                                            <td><strong>US30ARPU</strong></td>
                                                            <td class="arpu_usd_30_total">N/A</td>
                                                            <td class="arpu_usd_30_avg">
                                                                {{ numberConverter($usarpu30['avg'], 4, 'pre') }}</td>
                                                            <td class="arpu_usd_30_t_mo_end">N/A</td>

                                                            @if (isset($usarpu30['dates']) && !empty($usarpu30['dates']))
                                                                @foreach ($usarpu30['dates'] as $usarpu_30)
                                                                    <td class="usd_arpu30_data ">
                                                                        {{ numberConverter($usarpu_30['value'], 4, 'pre') }}
                                                                    </td>
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

        </div>


        @if(isset($sumemry) && !empty($sumemry))
        @foreach ($sumemry as $report)


        <div class="box-panel">
            <div class="d-flex align-items-center my-3">
                <span class="badge badge-secondary px-2 bg-primary text-uppercase">
                    <a href="javascript:void(0);" class="text-white">
                    {{$report['account_manager']['name']}} </a>
                    {{$report['month_string']}} | Last Update: {{$report['last_update']}}
                </span>
                <span class="flex-fill ml-2 bg-primary w-100 font-weight-bold" style="height:1px;"></span>
                <div class="text-right pl-2">
                    <button class="btn btn-sm report-xls" style="color:white; background-color:green" data-param="xlaxiata"><i class="fa fa-file-excel-o"></i>Export as XLS</button>
                </div>
            </div>
          <div class="card">
            <div class="table-responsive shadow-sm" id="{{$report['account_manager']['name']}}">
              <h1 style="display:hidden"></h1>
              <table class="table table-light table-striped m-0 font-13 xlaxiata" id="dtbl">
                <thead class="thead-dark">
                  <tr>
                    <th class="align-middle">Summary</th>
                    <th class="align-middle">Total</th>
                    <th class="align-middle">AVG</th>
                    <th class="align-middle">T.MO.Year</th>

                    @if(isset($no_of_days) && !empty($no_of_days))
                        @foreach ($no_of_days as $days)

                    <th class="align-middle">{{$days['no']}}</th>

                    @endforeach
                        @endif
                  </tr>
                </thead>
                <tbody>
                  <tr class="tur ">
                    <td><strong class="text-with-sup">GMV (USD) <sup><i class="ml-3 text-dark fa fa-info-circle" title="Revenue that we charge user excluding tax"></i></sup></strong></td>
                    <td class="revenue_total_usd" data-country="" data-local-currency=""
                      data-usd="" style="color:#212529"><span
                        class="format_total_revenue">{{numberConverter($report['tur']['total'] ,2,'pre') }}(USD)</span></td>
                    <td class="revenue_avg_usd" style="color:#212529">{{numberConverter($report['tur']['avg'] ,2,'pre') }}</td>
                    <td class="revenue_monthly_usd" style="color:#212529">{{numberConverter($report['tur']['t_mo_end'] ,2,'pre') }}</td>

                    @if(isset($report['tur']['dates']) && !empty($report['tur']['dates']))
                        @foreach ($report['tur']['dates'] as $tur)

                        <td class="gross_revenue_usd {{$tur['class']}}">{{numberConverter($tur['value'] ,2,'pre') }}</td>

                    @endforeach
                        @endif




                  </tr>
                  {{-- <tr class="gradient text-white rev">
                    <td>
                      <strong class="text-with-sup">
                        <div class="revenueSeperateBtn pull-left" data-sign="plus"
                          style="cursor: pointer;min-width: 10px;">+</div>
                        <div class="text-left">E.Rev (COUNTRY-CURRENCY)
                          <sup><i class="ml-3 text-dark fa fa-info-circle" title="Total Revenue"></i></sup>
                        </div>
                      </strong>
                    </td>

                    <td class="revenue_total" data-country="" data-local-currency="" data-usd="">
                      <span class="local_total_revenue">{{numberConverter($report['t_rev']['total'] ,2,'pre') }}</span></td>
                    <td class="revenue_avg">{{numberConverter($report['t_rev']['avg'] ,2,'pre') }}</td>
                    <td class="revenue_monthly">{{numberConverter($report['t_rev']['t_mo_end'] ,2,'pre') }}</td>

                    @if(isset($report['t_rev']['dates']) && !empty($report['t_rev']['dates']))
                        @foreach ($report['t_rev']['dates'] as $t_rev)

                        <td class="gross_revenue_usd {{$t_rev['class']}}">{{numberConverter($t_rev['value'] ,2,'pre') }}</td>

                    @endforeach
                        @endif


                  </tr> --}}
                  {{-- <tr class="revExtendedRows"
                    style="display: none; border:5px solid #0c54a0; border-bottom:none;border-top:none;">
                    <td><strong class="text-with-sup">G.Rev (COUNTRY-CURRENCY)<sup><i class="ml-3 text-dark fa fa-info-circle"
                            title="Total Revenue after telco"></i></sup></strong></td>
                    <td class="rev_share_total">{{numberConverter($report['trat']['total'] ,2,'pre') }}</td>
                    <td class="rev_share_avg">{{numberConverter($report['trat']['avg'] ,2,'pre') }}</td>
                    <td class="rev_share_monthly">{{numberConverter($report['trat']['t_mo_end'] ,2,'pre') }}</td>

                    @if(isset($report['trat']['dates']) && !empty($report['trat']['dates']))
                        @foreach ($report['trat']['dates'] as $trat)

                        <td class="gross_revenue_usd {{$trat['class']}}">{{numberConverter($trat['value'] ,2,'pre') }}</td>

                    @endforeach
                        @endif

                  </tr> --}}

                  {{-- <tr class="revExtendedRows">
                    <td><strong class="text-with-sup">G.Rev (USD)<sup><i class="ml-3 text-dark fa fa-info-circle"
                            title="Total USD Revenue after telco"></i></sup></strong></td>
                    <td class="rev_usd_share_total">{{numberConverter($report['turt']['total'] ,2,'pre') }}</td>
                    <td class="rev_usd_share_avg">{{numberConverter($report['turt']['avg'] ,2,'pre') }}</td>
                    <td class="rev_usd_share_monthly">{{numberConverter($report['turt']['t_mo_end'] ,2,'pre') }}</td>
                    @if(isset($report['turt']['dates']) && !empty($report['turt']['dates']))
                        @foreach ($report['turt']['dates'] as $turt)

                        <td class="gross_revenue_usd {{$turt['class']}}">{{numberConverter($turt['value'] ,2,'pre') }}</td>

                    @endforeach
                        @endif
                  </tr> --}}
                  <tr class="subs">
                    <td><strong class="text-with-sup">N.REV(USD)<sup><i class="ml-3 text-dark fa fa-info-circle" title="Net Revenue = G.REV - other tax"></i></sup></strong></td>
                    <td class="subactive_total">{{numberConverter($report['net_rev']['total'] ,2,'net_rev','') }}</td>
                    <td class="subactive_avg">{{numberConverter($report['net_rev']['avg'] ,2,'net_rev','') }}</td>
                    <td class="subactive_monthly">{{numberConverter($report['net_rev']['t_mo_end'] ,2,'net_rev','') }}</td>

                    @if(isset($report['net_rev']['dates']) && !empty($report['net_rev']['dates']))
                    @foreach ($report['net_rev']['dates'] as $net_rev)
                    <td class="gross_revenue_usd {{$net_rev['class']}}">{{numberConverter($net_rev['value'] ,2,'net_rev','') }}</td>
                    @endforeach
                    @endif
                  </tr>
                  <tr class="subs">
                    <td><strong class="text-with-sup">T Subs <sup><i class="ml-3 text-dark fa fa-info-circle"
                            title="Total Subactive"></i></sup></strong></td>
                    <td class="subactive_total">{{numberConverter($report['t_sub']['total'] ,0,'t_sub','') }}</td>
                    <td class="subactive_avg">N/A</td>
                    <td class="subactive_monthly">N/A</td>

                    @if(isset($report['t_sub']['dates']) && !empty($report['t_sub']['dates']))
                        @foreach ($report['t_sub']['dates'] as $t_sub)

                        <td class="gross_revenue_usd {{$t_sub['class']}}">{{numberConverter($t_sub['value'] ,0,'t_sub','') }}</td>

                    @endforeach
                        @endif

                  </tr>
                  <tr class="reg">
                    <td><strong>Reg</strong></td>
                    <td class="reg_total">{{numberConverter($report['reg']['total'] ,0,'t_sub','') }}</td>
                    <td class="reg_avg">{{numberConverter($report['reg']['avg'] ,2,'pre') }}</td>
                    <td class="reg_monthly">{{numberConverter($report['reg']['t_mo_end'] ,0,'t_sub','') }}</td>

                    @if(isset($report['reg']['dates']) && !empty($report['reg']['dates']))
                        @foreach ($report['reg']['dates'] as $reg)

                        <td class="reg_data {{$reg['class']}}">{{numberConverter($reg['value'] ,0,'t_sub','') }}</td>

                    @endforeach
                        @endif

                  </tr>
                  <tr class="unreg">
                    <td><strong>Unreg</strong></td>
                    <td class="unreg_total">{{numberConverter($report['unreg']['total'] ,0,'t_sub','') }}</td>
                    <td class="unreg_avg">{{numberConverter($report['unreg']['avg'] ,2,'pre') }}</td>
                    <td class="unreg_monthly">{{numberConverter($report['unreg']['t_mo_end'] ,0,'t_sub','') }}</td>

                    @if(isset($report['unreg']['dates']) && !empty($report['unreg']['dates']))
                        @foreach ($report['unreg']['dates'] as $unreg)

                        <td class="unreg_data {{$unreg['class']}}">{{numberConverter($unreg['value'] ,0,'t_sub','') }}</td>

                    @endforeach
                        @endif


                  </tr>
                  <tr class="purged">
                    <td><strong>Purged</strong></td>
                    <td class="purged_total">{{numberConverter($report['purged']['total'] ,0,'t_sub','') }}</td>
                    <td class="purged_avg">{{numberConverter($report['purged']['avg'] ,2,'pre') }}</td>
                    <td class="purged_monthly">{{numberConverter($report['purged']['t_mo_end'] ,0,'t_sub','') }}</td>

                    @if(isset($report['purged']['dates']) && !empty($report['purged']['dates']))
                        @foreach ($report['purged']['dates'] as $purged)

                        <td class="purged_data {{$purged['class']}}">{{numberConverter($purged['value'],0,'t_sub','') }}</td>

                    @endforeach
                        @endif


                  </tr>
                  <tr class="churn">
                    <!-- <td><strong>Churn %<sup style="position: relative; top:-29px; margin-left: 38px;"><i class="ml-3 text-dark fa fa-info-circle" title="(unreg/ tsubs) * 100"></i></sup></strong></td> -->
                    <td><strong class="text-with-sup">Churn % <sup><i class="ml-3 text-dark fa fa-info-circle"
                            title="(unreg/ tsubs) * 100"></i></sup></strong>
                    </td>
                    <td class="churn_total_msg">N/A</td>
                    <td class="churn_avg">{{numberConverter($report['churn']['avg'] ,2,'post','%') }}</td>
                    <td class="churn_monthly_msg">N/A</td>
                    @if(isset($report['churn']['dates']) && !empty($report['churn']['dates']))
                        @foreach ($report['churn']['dates'] as $churn)

                        <td class="churn_data {{$churn['class']}}">{{numberConverter($churn['value'] ,2,'post','%') }}</td>

                    @endforeach
                        @endif


                  </tr>
                  <tr class="renewal">
                    <td><strong><div class="renewalSeperateBtn pull-left" data-sign="plus" style="cursor: pointer;">+</div>Renewal</strong><div class="renewalDivs" data-sign="plus"></div></td>
                    <td class="total_sent_total">{{numberConverter($report['renewal']['total'] ,0,'renewal','') }}</td>
                    <td class="total_sent_avg">{{numberConverter($report['renewal']['avg'] ,0,'renewal','') }}</td>
                    <td class="total_sent_mon">{{numberConverter($report['renewal']['t_mo_end'] ,0,'renewal','') }}</td>
                    @if(isset($report['renewal']['dates']) && !empty($report['renewal']['dates']))
                        @foreach ($report['renewal']['dates'] as $renewal)

                        <td class="total_sent_data {{$renewal['class']}}">{{numberConverter($renewal['value'] ,0,'renewal','') }}</td>

                    @endforeach
                        @endif


                  </tr>
                  <tr class="renewalExtendedRows" style=" border:5px solid #0c54a0; border-bottom:none; display: none;">
                    <td><strong>Daily Push Success</strong></td>
                    <td class="total_first_push_msg">{{numberConverter($report['daily_push_success']['total'],2,'post')}}</td>
                    <td class="first_push_avg">{{numberConverter($report['daily_push_success']['avg'],2,'post')}}</td>
                    <td class="monthly_first_push_msg">{{numberConverter($report['daily_push_success']['t_mo_end'],2,'post')}}</td>
                    @if(isset($report['daily_push_success']['dates']) && !empty($report['daily_push_success']['dates']))
                        @foreach ($report['daily_push_success']['dates'] as $daily_push_success)

                        <td class="br_data {{$daily_push_success['class']}}">{{numberConverter($daily_push_success['value'],2,'post')}}</td>

                    @endforeach
                        @endif


                  </tr>
                  <tr class="renewalExtendedRows" style=" border:5px solid #0c54a0; border-top:none; display: none;">
                    <td><strong>Daily Push Failed</strong></td>
                    <td class="total_daily_push_msg">{{numberConverter($report['daily_push_failed']['total'],2,'post')}}</td>
                    <td class="daily_push_avg">{{numberConverter($report['daily_push_failed']['avg'],2,'post')}}</td>
                    <td class="monthly_daily_push_msg">{{numberConverter($report['daily_push_failed']['t_mo_end'],2,'post')}}</td>

                    @if(isset($report['daily_push_failed']['dates']) && !empty($report['daily_push_failed']['dates']))
                        @foreach ($report['daily_push_failed']['dates'] as $daily_push_failed)

                        <td class="br_data {{$daily_push_failed['class']}}">{{numberConverter($daily_push_failed['value'],2,'post')}}</td>

                    @endforeach
                        @endif

                  </tr>
                  <tr class="bill">
                    <td>
                      <strong class="text-with-sup">
                        <div class="billSeperateBtn pull-left" data-sign="plus" style="cursor: pointer;">+</div>
                        <div class="text-left">Bill % </div><sup><i class="ml-3 text-dark fa fa-info-circle"
                            title="(total daily push delivered / total subscriber) * 100%"></i></sup>
                      </strong>
                      <div class="billDivs" data-sign="plus"></div>
                    </td>
                    <td class="total_br_msg">N/A</td>
                    <td class="br_avg">{{numberConverter($report['bill']['avg'] ,2,'post') }}</td>
                    <td class="br_monthly_msg">N/A</td>

                    @if(isset($report['bill']['dates']) && !empty($report['bill']['dates']))
                        @foreach ($report['bill']['dates'] as $bill)

                        <td class="br_data {{$bill['class']}}">{{numberConverter($bill['value'] ,2,'post','%') }}</td>

                    @endforeach
                        @endif


                  </tr>
                  <tr class="billExtendedRows" style=" border:5px solid #0c54a0; border-bottom:none; display:none;">
                    <td>
                      <strong class="text-with-sup">First P.%<sup><i class="ml-3 text-dark fa fa-info-circle"
                            title="(delivered first push / first push sent) * 100%"></i></sup></strong>

                    </td>
                    <td class="total_first_push_msg">N/A</td>
                    <td class="first_push_avg">{{numberConverter($report['first_push']['avg'],2,'pre')}}</td>
                    <td class="monthly_first_push_msg">N/A</td>
                    @if(isset($report['first_push']['dates']) && !empty($report['first_push']['dates']))
                        @foreach ($report['first_push']['dates'] as $first_push)

                        <td class="br_data {{$first_push['class']}}">{{numberConverter($first_push['value'] ,2,'post','%') }}</td>

                    @endforeach
                        @endif


                  </tr>
                  <tr class="billExtendedRows" style=" border:5px solid #0c54a0; border-top:none; display: none;">
                    <td>
                      <strong class="text-with-sup">Daily P.%<sup><i class="ml-3 text-dark fa fa-info-circle"
                            title="(delivered daily push / daily push sent) * 100%"></i></sup></strong>

                    </td>
                    <td class="total_daily_push_msg">N/A</td>
                    <td class="daily_push_avg">{{numberConverter($report['daily_push']['avg'] ,2,'post','%') }}</td>
                    <td class="monthly_daily_push_msg">N/A</td>

                    @if(isset($report['daily_push']['dates']) && !empty($report['daily_push']['dates']))
                        @foreach ($report['daily_push']['dates'] as $daily_push)

                        <td class="br_data {{$daily_push['class']}}">{{numberConverter($daily_push['value'] ,2,'post','%') }}</td>

                    @endforeach
                        @endif

                  </tr>

                  <tr class="arpu7">
                      <td>
                          <strong class="text-with-sup">
                              <div class="arpuSeperateBtn pull-left" data-sign="plus" style="cursor: pointer;">+</div>
                              <div class="text-left">30ARPU</div><sup><i class="ml-3 text-dark fa fa-info-circle"
                              title="Total USD Revenue After Telco (Last 30 Days) / (Total Reg Last 30 Days + Total Subs Active Current Day) "></i></sup></strong>

                      </td>

                      <td class="arpu_30_msg">N/A</td>
                      <td class="arpu_30_avg">
                          {{number_format("0",4) }}
                      </td>
                      <td class="arpu_30_msg">N/A</td>

                      @if(isset($report['arpu30']['dates']) && !empty($report['arpu30']['dates']))
                      @foreach ($report['arpu30']['dates'] as $arpu30)

                          <td class="arpu30_data">{{number_format("0",4) }}</td>

                      @endforeach
                      @endif
                  </tr>
                  <tr class="arpuExtendedRows" style=" border:5px solid #0c54a0; border-top:none; display: none;">
                    <td><strong>US30ARPU</strong></td>
                    <td class="arpu_usd_30_msg">N/A</td>
                    <td class="arpu_usd_30_avg">{{number_format("0",4) }}</td>
                    <td class="arpu_usd_30_msg">N/A</td>

                    @if(isset($report['usarpu30']['dates']) && !empty($report['usarpu30']['dates']))
                        @foreach ($report['usarpu30']['dates'] as $usarpu30)

                        <td class="usd_arpu30_data">{{number_format("0",4) }}</td>

                    @endforeach
                        @endif


                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        @endforeach
        @endif
        </div>
        </div>

      </div>

@endsection
