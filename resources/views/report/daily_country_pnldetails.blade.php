@extends('layouts.admin')

@section('title')
    {{ __('GP Details') }}
@endsection

@section('content')

<div class="page-content">
      <div class="page-title" style="margin-bottom:25px">
        <div class="row justify-content-between align-items-center">
          <div
            class="col-xl-4 col-lg-4 col-md-4 d-flex align-items-center justify-content-between justify-content-md-start mb-3 mb-md-0">
            <div class="d-inline-block">
              <h5 class="h4 d-inline-block font-weight-400 mb-0"><b>GP Details</b></h5><br>
              <p class="d-inline-block font-weight-200 mb-0">Summary of Campaign Data</p>
            </div>
          </div>
          <div
            class="col-xl-8 col-lg-8 col-md-8 d-flex align-items-center justify-content-between justify-content-md-end">
          </div>
        </div>
      </div>

    @include('report.partials.filterPnlReportDetails')

    <div class="card shadow-sm mt-0">
      <div class="card-body">

        <div class="d-flex align-items-center my-3">
          <span class="badge badge-with-flag badge-secondary px-2 bg-primary text-uppercase">
            Country Wise GP Details {{isset($report['month_string'] ) ? $report['month_string'] : ''}} </span>
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
                @if(isset($sumOfSummaryData) && !empty($sumOfSummaryData))
                @foreach ($sumOfSummaryData as $key =>$end_user_rev_usd)
                @if($key == 'end_user_rev_usd')
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
                @if($key == 'gros_rev_usd')
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
                @foreach ($sumOfSummaryData as $key =>$mo)
                @if($key == 'mo')
                <tr class="bg-young-blue gross_revenue">
                  <td class="font-weight-bold">
                    <div class="text-with-sup">
                      <strong class="text-with-sup">Campaign MO</strong>
                    </div>
                  </td>
                  <td class="share_total">{{numberConverter( $mo['total'],2,'pre') }}</td>
                  <td class="share_avg">{{numberConverter( $mo['avg'],2,'pre') }}</td>
                  <td class="share_month">{{numberConverter( $mo['t_mo_end'],2,'pre') }}</td>

                  @if(isset($mo['dates']) && !empty($mo['dates']))
                  @foreach ($mo['dates'] as $mO)
                      <td class="share_data">{{numberConverter( $mO['value'],2,'pre') }}</td>
                  @endforeach
                  @endif
                </tr>
                @endif
                @endforeach
                @endif


                @if(isset($sumOfSummaryData) && !empty($sumOfSummaryData))
                @foreach ($sumOfSummaryData as $key =>$cost_campaign)
                @if($key == 'cost_campaign')
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
                @foreach ($sumOfSummaryData as $key =>$other_cost)
                @if($key == 'other_cost')
                <tr class="bg-young-yellow o_cost">
                  <td class="font-weight-bold">
                    <div class="text-with-sup">
                      <div class="btn-ico-expand other_cost OtherCostSepBtn" data-sign="plus" style="cursor: pointer;">+</div>
                      <div class="text-left"><strong class="text-with-sup">Other Cost<sup><i
                              class="ml-3 text-dark fa fa-info-circle"
                              title="Other cost = Hosting + Content + rnd + md + platform"></i></sup></strong></div>
                    </div>
                  </td>
                  <td class="other_cost_total">{{numberConverter( $other_cost['total'],2,'pre') }}</td>
                  <td class="other_cost_avg">{{numberConverter( $other_cost['avg'],2,'pre') }}</td>
                  <td class="other_cost_month">{{numberConverter( $other_cost['t_mo_end'],2,'pre') }}</td>

                  @if(isset($other_cost['dates']) && !empty($other_cost['dates']))
                  @foreach ($other_cost['dates'] as $othercost)
                      <td class="other_cost_data">{{numberConverter( $othercost['value'],2,'pre') }}</td>
                  @endforeach
                  @endif
                </tr>
                @endif
                @endforeach
                @endif

                @if(isset($sumOfSummaryData) && !empty($sumOfSummaryData))
                @foreach ($sumOfSummaryData as $key =>$hosting_cost)
                @if($key == 'hosting_cost')
                <tr class="bg-young-yellow hiddenSoSOtherCostTr" style="display: none;">
                  <td class="font-weight-bold">
                    <div style="position: relative; left: 30px; font-weight: bolder;">Hosting Cost</div>
                  </td>
                  <td class="hosting_cost_total">{{numberConverter( $hosting_cost['total'],2,'pre') }}</td>
                  <td class="hosting_cost_avg">{{numberConverter( $hosting_cost['avg'],2,'pre') }}</td>
                  <td class="hosting_cost_month">{{numberConverter( $hosting_cost['t_mo_end'],2,'pre') }}</td>

                  @if(isset($hosting_cost['dates']) && !empty($hosting_cost['dates']))
                  @foreach ($hosting_cost['dates'] as $hostingcost)
                    <td class="hosting_cost_data">{{numberConverter( $hostingcost['value'],2,'pre') }}</td>
                  @endforeach
                  @endif
                </tr>
                @endif
                @endforeach
                @endif


                @if(isset($sumOfSummaryData) && !empty($sumOfSummaryData))
                @foreach ($sumOfSummaryData as $key =>$content)
                @if($key == 'content')
                <tr class="bg-young-yellow hiddenSoSOtherCostTr" style="display: none;">
                  <td class="font-weight-bold">
                    <div style="position: relative; left: 30px; font-weight: bolder;">Content 2%</div>
                  </td>
                  <td class="content_total">{{numberConverter( $content['total'],2,'pre') }}</td>
                  <td class="content_avg">{{numberConverter( $content['avg'],2,'pre') }}</td>
                  <td class="content_month">{{numberConverter( $content['t_mo_end'],2,'pre') }}</td>

                  @if(isset($content['dates']) && !empty($content['dates']))
                  @foreach ($content['dates'] as $contentdata)
                      <td class="content_data">{{numberConverter( $contentdata['value'],2,'pre') }}</td>
                  @endforeach
                  @endif
                </tr>
                @endif
                @endforeach
                @endif


                @if(isset($sumOfSummaryData) && !empty($sumOfSummaryData))
                @foreach ($sumOfSummaryData as $key =>$rnd)
                @if($key == 'rnd')
                <tr class="bg-young-yellow hiddenSoSOtherCostTr" style="display: none;">
                  <td class="font-weight-bold">
                    <div style="font-weight: bolder; position: relative; left: 30px;">RND 5%</div>
                  </td>
                  <td class="md_total">{{numberConverter( $rnd['total'],2,'pre') }}</td>
                  <td class="md_avg">{{numberConverter( $rnd['avg'],2,'pre') }}</td>
                  <td class="md_month">{{numberConverter( $rnd['t_mo_end'],2,'pre') }}</td>
                  @if(isset($rnd['dates']) && !empty($rnd['dates']))
                  @foreach ($rnd['dates'] as $rnddata)
                      <td class="md_data">{{numberConverter( $rnddata['value'],2,'pre') }}</td>
                  @endforeach
                  @endif
                </tr>
                @endif
                @endforeach
                @endif

                @if(isset($sumOfSummaryData) && !empty($sumOfSummaryData))
                @foreach ($sumOfSummaryData as $key =>$bd)
                @if($key == 'bd')
                <tr class="bg-young-yellow hiddenSoSOtherCostTr" style="display: none;">
                  <td class="font-weight-bold">
                    <div style="font-weight: bolder; position: relative; left: 30px;">BD 3%</div>
                  </td>
                  <td class="bd_total">{{numberConverter( $bd['total'],2,'pre') }}</td>
                  <td class="bd_avg">{{numberConverter( $bd['avg'],2,'pre') }}</td>
                  <td class="bd_month">{{numberConverter( $bd['t_mo_end'],2,'pre') }}</td>
                  @if(isset($bd['dates']) && !empty($bd['dates']))
                  @foreach ($bd['dates'] as $bddata)
                      <td class="bd_data">{{numberConverter( $bddata['value'],2,'pre') }}</td>
                  @endforeach
                  @endif
                </tr>
                @endif
                @endforeach
                @endif

                @if(isset($sumOfSummaryData) && !empty($sumOfSummaryData))
                @foreach ($sumOfSummaryData as $key =>$platform)
                @if($key == 'platform')
                <tr class="bg-young-yellow hiddenSoSOtherCostTr" style="display: none;">
                  <td class="font-weight-bold">
                    <div style="font-weight: bolder; position: relative; left: 30px;">Vostok Platform Cost 10%</div>
                  </td>
                  <td class="platform_total">{{numberConverter( $platform['total'],2,'pre') }}</td>
                  <td class="platform_avg">{{numberConverter( $platform['avg'],2,'pre') }}</td>
                  <td class="platform_month">{{numberConverter( $platform['t_mo_end'],2,'pre') }}</td>
                  @if(isset($platform['dates']) && !empty($platform['dates']))
                  @foreach ($platform['dates'] as $platformdata)
                      <td class="platform_data">{{numberConverter( $platformdata['value'],2,'pre') }}</td>
                  @endforeach
                  @endif
                </tr>
                @endif
                @endforeach
                @endif

                @if(isset($sumOfSummaryData) && !empty($sumOfSummaryData))
                @foreach ($sumOfSummaryData as $key =>$price_mo)
                @if($key == 'price_mo')
                <tr class="bg-young-red price_mo">
                  <td class="font-weight-bold">
                    <div class="text-with-sup">
                      <div class="text-left"><strong>Price/Mo</strong></div>
                    </div>
                  </td>
                  <td class="cost_total">{{numberConverter( $price_mo['total'],2,'pre') }}</td>
                  <td class="cost_avg">{{numberConverter( $price_mo['avg'],2,'pre') }}</td>
                  <td class="cost_month">{{numberConverter( $price_mo['t_mo_end'],2,'pre') }}</td>

                  @if(isset($price_mo['dates']) && !empty($price_mo['dates']))
                  @foreach ($price_mo['dates'] as $price)
                    <td class="cost_data">{{numberConverter( $price['value'],2,'pre') }}</td>
                  @endforeach
                  @endif
                </tr>
                @endif
                @endforeach
                @endif


                @if(isset($sumOfSummaryData) && !empty($sumOfSummaryData))
                @foreach ($sumOfSummaryData as $key =>$active_subs)
                @if($key == 'active_subs')
                <tr class="bg-young-red active_subs">
                  <td class="font-weight-bold">
                    <div class="text-with-sup">
                      <div class="text-left"><strong>Active Subscriber</strong></div>
                    </div>
                  </td>
                  <td class="cost_total">{{numberConverter( $active_subs['total'],2,'pre') }}</td>
                  <td class="cost_avg">{{numberConverter( $active_subs['avg'],2,'pre') }}</td>
                  <td class="cost_month">{{numberConverter( $active_subs['t_mo_end'],2,'pre') }}</td>

                  @if(isset($active_subs['dates']) && !empty($active_subs['dates']))
                  @foreach ($active_subs['dates'] as $activesub)
                    <td class="cost_data">{{numberConverter( $activesub['value'],2,'pre') }}</td>
                  @endforeach
                  @endif
                </tr>
                @endif
                @endforeach
                @endif


                @if(isset($sumOfSummaryData) && !empty($sumOfSummaryData))
                @foreach ($sumOfSummaryData as $key =>$roi)
                @if($key == 'roi')
                <tr class="bg-young-red roi">
                  <td class="font-weight-bold">
                    <div class="text-with-sup">
                      <div class="text-left"><strong class="text-with-sup" style="position: relative; left: -1px;">ROI<sup><i class="ml-3 text-dark fa fa-info-circle" title="ROI = Price/Mo / 30 ARPU"></i></sup></strong></div>
                    </div>
                  </td>
                  <td class="cost_total">{{numberConverter( $roi['total'],2,'pre') }}</td>
                  <td class="cost_avg">{{numberConverter( $roi['avg'],2,'pre') }}</td>
                  <td class="cost_month">{{numberConverter( $roi['t_mo_end'],2,'pre') }}</td>

                  @if(isset($roi['dates']) && !empty($roi['dates']))
                  @foreach ($roi['dates'] as $roI)
                    <td class="cost_data">{{numberConverter( $roI['value'],2,'pre') }}</td>
                  @endforeach
                  @endif
                </tr>
                @endif
                @endforeach
                @endif


                @if(isset($sumOfSummaryData) && !empty($sumOfSummaryData))
                @foreach ($sumOfSummaryData as $key =>$pnl)
                @if($key == 'pnl')
                <tr class="bg-young-green pnl">
                  <td class="font-weight-bold"><strong class="text-with-sup">GP<sup><i
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

              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>


    <div class="card shadow-sm mt-0">
      <div class="card-body">
        <div class="card">
          <div class="table-responsive shadow-sm" id="all">
            @if(isset($sumemry) && !empty($sumemry))
            @foreach ($sumemry as $report)
                <div class="ptable">
                  <div class="d-flex align-items-center my-3">
                    <span class="badge badge-with-flag badge-secondary px-2 bg-primary text-uppercase">
                      <img src="{{ asset('/flags/'.$report['country']['flag']) }}" alt="{{$report['country']['country']}}" width="30">&nbsp;
                      ID <a href="javascript:void(0);"
                            class="text-white">
                          {{$report['country']['country']}} </a>
                            {{$report['month_string']}} | Last Update: {{$report['last_update']}} UTC </span>
                    <span class="flex-fill ml-2 bg-primary w-100 font-weight-bold" style="height: 1px;"></span>
                    <div class="text-right pl-2">
                      <button class="btn btn-sm pnl-xls" style="color:white; background-color:green" data-param="{{$report['country']['country']}}"><i
                          class="fa fa-file-excel-o"></i>Export as XLS</button>
                    </div>
                  </div>
                  <div class="card">
                    <div class="table-responsive shadow-sm pnlDataTbl" id="{{$report['country']['country']}} ">
                      <h1 style="display:none"></h1>
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

                          <tr class="bg-young-blue end_user_revenue">
                            <td class="font-weight-bold">
                              <div class="text-with-sup">
                                <div class="btn-ico-expand grev_plus" data-sign="plus" style="cursor: pointer;">+</div>
                                <div class="text-left"><strong>End User Revenue (USD)</strong></div>
                              </div>
                            </td>
                            <td class="gross_revenue_usd_total usd">{{numberConverter($report['end_user_rev_usd']['total'] ,2,'hosting_cost') }} </td>
                            <td class="gross_revenue_usd_avg">{{numberConverter($report['end_user_rev_usd']['t_mo_end'] ,2,'hosting_cost') }}</td>
                            <td class="gross_revenue_usd_month">{{numberConverter($report['end_user_rev_usd']['avg'] ,2,'hosting_cost') }}</td>
                            @if(isset($report['end_user_rev_usd']['dates']) && !empty($report['end_user_rev_usd']['dates']))
                            @foreach ($report['end_user_rev_usd']['dates'] as $end_user_rev_usd)
                                <td class="gross_revenue_usd_data {{$end_user_rev_usd['class']}}">{{numberConverter($end_user_rev_usd['value'] ,2,'hosting_cost') }}</td>
                            @endforeach
                            @endif
                          </tr>

                          <tr class="bg-young-blue hiddenRevTr" style="display: none;">
                            <td class="font-weight-bold">
                              <div style="font-weight: bolder; position: relative; left: 30px;">End User Revenue (IDR)</div>
                            </td>
                            <td class="gross_revenue_total">{{numberConverter( $report['end_user_rev']['total'] ,2,'hosting_cost') }}</td>
                            <td class="gross_revenue_avg">{{numberConverter( $report['end_user_rev']['t_mo_end'] ,2,'hosting_cost') }}</td>
                            <td class="gross_revenue_month">{{numberConverter( $report['end_user_rev']['avg'] ,2,'hosting_cost') }}</td>

                            @if(isset($report['end_user_rev']['dates']) && !empty($report['end_user_rev']['dates']))
                            @foreach ($report['end_user_rev']['dates'] as $end_user_rev)
                                <td class="gross_revenue_data">{{numberConverter( $end_user_rev['value'] ,2,'hosting_cost') }}</td>
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
                            <td class="share_total">{{numberConverter( $report['gros_rev_usd']['total'] ,2,'hosting_cost') }}</td>
                            <td class="share_avg">{{numberConverter( $report['gros_rev_usd']['t_mo_end'] ,2,'hosting_cost') }}</td>
                            <td class="share_month">{{numberConverter( $report['gros_rev_usd']['avg'] ,2,'hosting_cost') }}</td>

                            @if(isset($report['gros_rev_usd']['dates']) && !empty($report['gros_rev_usd']['dates']))
                            @foreach ($report['gros_rev_usd']['dates'] as $gros_rev_usd)
                                <td class="share_data">{{numberConverter( $gros_rev_usd['value'] ,2,'hosting_cost') }}</td>
                            @endforeach
                            @endif

                          </tr>

                          <tr class="bg-young-blue hiddenGrossRevUsdTr" style="display:none;">
                            <td class="font-weight-bold">
                              <div style="font-weight: bolder; position: relative; left: 30px;">Gross Revenue (IDR)</div>
                            </td>
                            <td class="local_share_total">{{numberConverter( $report['gros_rev']['total'] ,2,'hosting_cost') }}</td>
                            <td class="local_share_avg">{{numberConverter( $report['gros_rev']['t_mo_end'] ,2,'hosting_cost') }}</td>
                            <td class="local_share_month">{{numberConverter( $report['gros_rev']['avg'] ,2,'hosting_cost') }}</td>

                            @if(isset($report['gros_rev']['dates']) && !empty($report['gros_rev']['dates']))
                            @foreach ($report['gros_rev']['dates'] as $gros_rev)
                                <td class="local_share_data">{{numberConverter( $gros_rev['value'] ,2,'hosting_cost') }}</td>
                            @endforeach
                            @endif
                          </tr>

                          <tr class="bg-young-blue hiddenGrossRevUsdTr" style="display:none;">
                            <td class="font-weight-bold">
                              <div style="font-weight: bolder; position: relative; left: 30px;">Net After Tax</div>
                            </td>
                            <td class="local_share_total">{{numberConverter( $report['net_after_tax']['total'] ,2,'hosting_cost') }}</td>
                            <td class="local_share_avg">{{numberConverter( $report['net_after_tax']['t_mo_end'] ,2,'hosting_cost') }}</td>
                            <td class="local_share_month">{{numberConverter( $report['net_after_tax']['avg'] ,2,'hosting_cost') }}</td>

                            @if(isset($report['net_after_tax']['dates']) && !empty($report['net_after_tax']['dates']))
                            @foreach ($report['net_after_tax']['dates'] as $net_after_tax)
                                <td class="local_share_data">{{numberConverter( $net_after_tax['value'] ,2,'hosting_cost') }}</td>
                            @endforeach
                            @endif
                          </tr>

                          <tr class="bg-young-blue hiddenGrossRevUsdTr" style="display:none;">
                            <td class="font-weight-bold">
                              <div style="font-weight: bolder; position: relative; left: 30px;">Net Revenue After Tax</div>
                            </td>
                            <td class="local_share_total">{{numberConverter( $report['net_after_tax']['total'] ,2,'hosting_cost') }}</td>
                            <td class="local_share_avg">{{numberConverter( $report['net_after_tax']['t_mo_end'] ,2,'hosting_cost') }}</td>
                            <td class="local_share_month">{{numberConverter( $report['net_after_tax']['avg'] ,2,'hosting_cost') }}</td>

                            @if(isset($report['net_after_tax']['dates']) && !empty($report['net_after_tax']['dates']))
                            @foreach ($report['net_after_tax']['dates'] as $net_after_tax)
                                <td class="local_share_data">{{numberConverter( $net_after_tax['value'] ,2,'hosting_cost') }}</td>
                            @endforeach
                            @endif
                          </tr>

                          <tr class="bg-young-blue">
                            <td class="font-weight-bold">
                              <strong>Campaign MO</strong>
                            </td>
                            <td class="reg_total">{{numberConverter( $report['mo']['total'] ,2,'hosting_cost') }}</td>
                            <td class="reg_avg">{{numberConverter( $report['mo']['t_mo_end'] ,2,'hosting_cost') }}</td>
                            <td class="reg_month">{{numberConverter( $report['mo']['avg'] ,2,'hosting_cost') }}</td>

                            @if(isset($report['mo']['dates']) && !empty($report['mo']['dates']))
                            @foreach ($report['mo']['dates'] as $mo)
                                <td class="reg_data">{{numberConverter( $mo['value'] ,2,'hosting_cost') }}</td>
                            @endforeach
                            @endif
                          </tr>

                          <tr class="bg-young-red cost_campaign">
                            <td class="font-weight-bold">
                              <div class="text-with-sup">
                                <div class="text-left"><strong>Cost Campaign (USD)</strong></div>
                              </div>
                            </td>
                            <td class="cost_total cost">{{numberConverter( $report['cost_campaign']['total'] ,2,'hosting_cost') }}</td>
                            <td class="cost_avg">{{numberConverter( $report['cost_campaign']['t_mo_end'] ,2,'hosting_cost') }}</td>
                            <td class="cost_month">{{numberConverter( $report['cost_campaign']['avg'] ,2,'hosting_cost') }}</td>

                            @if(isset($report['cost_campaign']['dates']) && !empty($report['cost_campaign']['dates']))
                            @foreach ($report['cost_campaign']['dates'] as $cost_campaign)
                                <td class="cost_data">{{numberConverter( $cost_campaign['value'] ,2,'hosting_cost') }}</td>
                            @endforeach
                            @endif
                          </tr>

                          <tr class="bg-young-yellow o_cost">
                            <td class="font-weight-bold">
                              <div class="text-with-sup">
                                <div class="btn-ico-expand other_cost" data-sign="plus" style="cursor: pointer;">+</div>
                                <div class="text-left"><strong class="text-with-sup">Other Cost<sup><i
                                        class="ml-3 text-dark fa fa-info-circle"
                                        title="Other cost = Hosting + Content + rnd + md + platform"></i></sup></strong></div>
                              </div>
                            </td>
                            <td class="other_cost_total">{{numberConverter( $report['other_cost']['total'] ,2,'hosting_cost') }}</td>
                            <td class="other_cost_avg">{{numberConverter( $report['other_cost']['t_mo_end'] ,2,'hosting_cost') }}</td>
                            <td class="other_cost_month">{{numberConverter( $report['other_cost']['avg'] ,2,'hosting_cost') }}</td>

                            @if(isset($report['other_cost']['dates']) && !empty($report['other_cost']['dates']))
                            @foreach ($report['other_cost']['dates'] as $other_cost)
                                <td class="other_cost_data">{{numberConverter( $other_cost['value'] ,2,'hosting_cost') }}</td>
                            @endforeach
                            @endif
                          </tr>
                          <tr class="bg-young-yellow hiddenOtherCostTr" style="display: none;">
                            <td class="font-weight-bold">
                              <div style="font-weight: bolder; position: relative; left: 30px;">Hosting Cost</div>
                            </td>
                            <td class="hosting_cost_total">{{numberConverter( $report['hosting_cost']['total'] ,2,'hosting_cost') }}</td>
                            <td class="hosting_cost_avg">{{numberConverter( $report['hosting_cost']['t_mo_end'] ,2,'hosting_cost') }}</td>
                            <td class="hosting_cost_month">{{numberConverter( $report['hosting_cost']['avg'] ,2,'hosting_cost') }}</td>

                            @if(isset($report['hosting_cost']['dates']) && !empty($report['hosting_cost']['dates']))
                            @foreach ($report['hosting_cost']['dates'] as $hosting_cost)
                                <td class="hosting_cost_data">{{numberConverter( $hosting_cost['value'] ,2,'hosting_cost') }}</td>
                            @endforeach
                            @endif
                          </tr>
                          <tr class="bg-young-yellow hiddenOtherCostTr" style="display: none;">
                            <td class="font-weight-bold">
                              <div style="font-weight: bolder; position: relative; left: 30px;">Content 2%</div>
                            </td>
                            <td class="content_total">{{numberConverter( $report['content']['total'] ,2,'hosting_cost') }}</td>
                            <td class="content_avg">{{numberConverter( $report['content']['total'] ,2,'hosting_cost') }}</td>
                            <td class="content_month">{{numberConverter( $report['content']['total'] ,2,'hosting_cost') }}</td>

                            @if(isset($report['content']['dates']) && !empty($report['content']['dates']))
                            @foreach ($report['content']['dates'] as $content)
                                <td class="content_data">{{numberConverter( $content['value'] ,2,'hosting_cost') }}</td>
                            @endforeach
                            @endif

                          </tr>
                          <tr class="bg-young-yellow hiddenOtherCostTr" style="display: none;">
                            <td class="font-weight-bold">
                              <div style="font-weight: bolder; position: relative; left: 30px;">RND 5%</div>
                            </td>
                            <td class="md_total">{{numberConverter( $report['rnd']['total'] ,2,'hosting_cost') }}</td>
                            <td class="md_avg">{{numberConverter( $report['rnd']['total'] ,2,'hosting_cost') }}</td>
                            <td class="md_month">{{numberConverter( $report['rnd']['total'] ,2,'hosting_cost') }}</td>

                            @if(isset($report['rnd']['dates']) && !empty($report['rnd']['dates']))
                            @foreach ($report['rnd']['dates'] as $rnd)
                                <td class="md_data">{{numberConverter( $rnd['value'] ,2,'hosting_cost') }}</td>
                            @endforeach
                            @endif
                          </tr>
                          <tr class="bg-young-yellow hiddenOtherCostTr" style="display: none;">
                            <td class="font-weight-bold">
                              <div style="font-weight: bolder; position: relative; left: 30px;">BD 3%</div>
                            </td>
                            <td class="bd_total">{{numberConverter( $report['bd']['total'] ,2,'hosting_cost') }}</td>
                            <td class="bd_avg">{{numberConverter( $report['bd']['total'] ,2,'hosting_cost') }}</td>
                            <td class="bd_month">{{numberConverter( $report['bd']['total'] ,2,'hosting_cost') }}</td>

                            @if(isset($report['bd']['dates']) && !empty($report['bd']['dates']))
                            @foreach ($report['bd']['dates'] as $bd)
                                <td class="bd_data">{{numberConverter( $bd['value'] ,2,'hosting_cost') }}</td>
                            @endforeach
                            @endif
                          </tr>
                          <tr class="bg-young-yellow hiddenOtherCostTr" style="display: none;">
                            <td class="font-weight-bold">
                              <div style="font-weight: bolder; position: relative; left: 30px;">Vostok Platform Cost 10%</div>
                            </td>
                            <td class="platform_total">{{numberConverter( $report['platform']['total'] ,2,'hosting_cost') }}</td>
                            <td class="platform_avg">{{numberConverter( $report['platform']['total'] ,2,'hosting_cost') }}</td>
                            <td class="platform_month">{{numberConverter( $report['platform']['total'] ,2,'hosting_cost') }}</td>

                            @if(isset($report['platform']['dates']) && !empty($report['platform']['dates']))
                            @foreach ($report['platform']['dates'] as $platform)
                                <td class="platform_data">{{numberConverter( $platform['value'] ,2,'hosting_cost') }}</td>
                            @endforeach
                            @endif
                          </tr>

                          <tr class="bg-young-red price_mo">
                            <td class="font-weight-bold"><strong class="text-with-sup">Price/Mo<sup><i
                                    class="ml-3 text-dark fa fa-info-circle"
                                    title="Price/Mo = cost campaign / mo"></i></sup></strong></td>
                            <td class="price_mo_total">{{numberConverter( $report['price_mo']['total'] ,2,'hosting_cost') }}</td>
                            <td class="price_mo_avg">{{numberConverter( $report['price_mo']['total'] ,2,'hosting_cost') }}</td>
                            <td class="price_mo_month">{{numberConverter( $report['price_mo']['total'] ,2,'hosting_cost') }}</td>

                            @if(isset($report['price_mo']['dates']) && !empty($report['price_mo']['dates']))
                            @foreach ($report['price_mo']['dates'] as $price_mo)
                                <td class="price_mo_data">{{numberConverter( $price_mo['value'] ,2,'hosting_cost') }}</td>
                            @endforeach
                            @endif

                          </tr>

                          <tr class="bg-young-red active_subscriber">
                            <td class="font-weight-bold"><strong>Active Subscriber</strong></td>
                            <td class="subs_total">{{numberConverter( $report['active_subs']['total'] ,2,'hosting_cost') }}</td>
                            <td class="subs_avg">{{numberConverter( $report['active_subs']['t_mo_end'] ,2,'hosting_cost') }}</td>
                            <td class="subs_month">{{numberConverter( $report['active_subs']['avg'] ,2,'hosting_cost') }}</td>

                            @if(isset($report['active_subs']['dates']) && !empty($report['active_subs']['dates']))
                            @foreach ($report['active_subs']['dates'] as $active_subs)
                                <td class="subs_data">{{numberConverter( $active_subs['value'] ,2,'hosting_cost') }}</td>
                            @endforeach
                            @endif
                          </tr>

                          <tr class="bg-young-red arpu_7">
                            <td class="font-weight-bold">
                              <div class="text-with-sup">
                                <div class="btn-ico-expand arpu_plus" data-sign="plus" style="cursor: pointer;">+</div>
                                <div class="text-left"><strong>7 ARPU</strong></div>
                              </div>
                            </td>
                            <td class="arpu_7_total">{{numberConverter( $report['arpu_7']['total'] ,2,'hosting_cost') }}</td>
                            <td class="arpu_7_avg">{{numberConverter( $report['arpu_7']['t_mo_end'] ,2,'hosting_cost') }}</td>
                            <td class="arpu_7_month">{{numberConverter( $report['arpu_7']['avg'] ,2,'hosting_cost') }}</td>

                            @if(isset($report['arpu_7']['dates']) && !empty($report['arpu_7']['dates']))
                            @foreach ($report['arpu_7']['dates'] as $arpu_7)
                                <td class="arpu_7_data">{{numberConverter( $arpu_7['value'] ,2,'hosting_cost') }}</td>
                            @endforeach
                            @endif
                          </tr>
                          <tr class="bg-young-red hiddenArpuTr" style="display: none;">
                            <td class="font-weight-bold">
                              <div style="font-weight: bolder; position: relative; left: 30px;">30 ARPU</div>
                            </td>
                            <td class="arpu_30_total">{{numberConverter( $report['arpu_30']['total'] ,2,'hosting_cost') }}</td>
                            <td class="arpu_30_avg">{{numberConverter( $report['arpu_30']['t_mo_end'] ,2,'hosting_cost') }}</td>
                            <td class="arpu_30_month">{{numberConverter( $report['arpu_30']['avg'] ,2,'hosting_cost') }}</td>

                            @if(isset($report['arpu_30']['dates']) && !empty($report['arpu_30']['dates']))
                            @foreach ($report['arpu_30']['dates'] as $arpu_30)
                                <td class="arpu_30_data">{{numberConverter( $arpu_30['value'] ,2,'hosting_cost') }}</td>
                            @endforeach
                            @endif

                          </tr>

                          <tr class="bg-young-red">
                            <td class="font-weight-bold">
                              <strong class="text-with-sup" style="position: relative; left: -1px;">ROI<sup><i
                                    class="ml-3 text-dark fa fa-info-circle"
                                    title="ROI = Price/Mo / 30 ARPU"></i></sup></strong>
                            </td>
                            <td class="cost_total cost">{{numberConverter( $report['roi']['total'] ,2,'hosting_cost') }}</td>
                            <td class="cost_avg">{{numberConverter( $report['roi']['t_mo_end'] ,2,'hosting_cost') }}</td>
                            <td class="cost_month">{{numberConverter( $report['roi']['avg'] ,2,'hosting_cost') }}</td>

                            @if(isset($report['roi']['dates']) && !empty($report['roi']['dates']))
                            @foreach ($report['roi']['dates'] as $roi)
                                <td class="cost_data">{{numberConverter( $roi['value'] ,2,'hosting_cost') }}</td>
                            @endforeach
                            @endif
                          </tr>

                          <tr class="bg-young-green pnl">
                            <td class="font-weight-bold"><strong class="text-with-sup">GP<sup><i
                                    class="ml-3 text-dark fa fa-info-circle"
                                    title="GP = Revenue After Telco - (Cost Campaign + Hosting + Content + rnd + md + platform)"></i></sup></strong>
                            </td>
                            <td class="pnl_total p">{{numberConverter( $report['pnl']['total'] ,2,'hosting_cost') }}</td>
                            <td class="pnl_avg">{{numberConverter( $report['pnl']['t_mo_end'] ,2,'hosting_cost') }}</td>
                            <td class="pnl_month">{{numberConverter( $report['pnl']['avg'] ,2,'hosting_cost') }}</td>

                            @if(isset($report['pnl']['dates']) && !empty($report['pnl']['dates']))
                            @foreach ($report['pnl']['dates'] as $pnl)
                                <td class="pnl_data">{{numberConverter( $pnl['value'] ,2,'hosting_cost') }}</td>
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
    </div>





  </div>

@endsection
