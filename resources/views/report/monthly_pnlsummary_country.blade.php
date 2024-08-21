@extends('layouts.admin')

@section('title')
  {{ __('GP Summary') }}
@endsection

@section('content')
<div class="page-content">
  <div class="page-title" style="margin-bottom:25px">
    <div class="row justify-content-between align-items-center">
      <div class="col-xl-4 col-lg-4 col-md-4 d-flex align-items-center justify-content-between justify-content-md-start mb-3 mb-md-0">
        @if(!isset($sumOfSummaryData['net_after_tax']))
        <div class="d-inline-block">
          <h5 class="h4 d-inline-block font-weight-400 mb-0"><b>GP Summary</b></h5><br>
          <p class="d-inline-block font-weight-200 mb-0">Summary of Country Data</p>
        </div>
        @endif
        @if(isset($sumOfSummaryData['net_after_tax']))
        <div class="d-inline-block">
          <h5 class="h4 d-inline-block font-weight-400 mb-0"><b>GP Details</b></h5><br>
          <p class="d-inline-block font-weight-200 mb-0">Details of Country Data</p>
        </div>
        @endif
      </div>
      <div class="col-xl-8 col-lg-8 col-md-8 d-flex align-items-center justify-content-between justify-content-md-end">
      </div>
    </div>
  </div>

  @include('report.partials.filterPnlReport')

  <div class="card shadow-sm mt-0">
    <div class="card-body">
      <div class="d-flex align-items-center my-3">
        <span class="badge badge-with-flag badge-secondary px-2 bg-primary text-uppercase">All Countries {{isset($report['month_string'] ) ? $report['month_string'] : ''}} </span>
        <span class="flex-fill ml-2 bg-primary w-100 font-weight-bold" style="height: 1px;"></span>
        <div class="text-right pl-2">
          <button class="btn btn-sm pnl-xls" style="color:white; background-color:green" data-param="all"><i class="fa fa-file-excel-o"></i>Export as XLS</button>
        </div>
      </div>
      <div class="card" style="margin-bottom: 0px">
        <div class="table-responsive shadow-sm" id="all">
          <h1 style="display:none">GP Country Daily Report Summary For All Operator</h1>
          <table class="table table-light m-0 font-13 table-text-no-wrap" id="pnlTbl">
            <thead class="thead-dark">
              <tr>
                <th>Summary</th>
                <th>Total</th>
                <th>AVG</th>
                <th>T.MO.Year</th>
                @if(isset($sumOfSummaryData['net_after_tax']))
                <th>E.O.M</th>
                @endif
                
                @if(isset($no_of_days) && !empty($no_of_days))
                @foreach ($no_of_days as $days)
                <th>{{$days['no']}}</th>
                @endforeach
                @endif
              </tr>
            </thead>
            @if(!isset($sumOfSummaryData['net_after_tax']))
            <tbody>
              @if(isset($sumOfSummaryData) && !empty($sumOfSummaryData))
              @foreach ($sumOfSummaryData as $key =>$end_user_rev_usd)
              @if($key == 'end_user_rev_usd')
              <tr class="bg-young-blue end_user_revenue">
                <td class="font-weight-bold">
                  <div class="text-with-sup">
                    <div class="text-left"><strong>GMV (USD)<sup><i class="ml-3 text-dark fa fa-info-circle" title="Revenue that we charge user excluding tax"></i></sup></strong></div>
                  </div>
                </td>
                <td class="gross_revenue_usd_total">{{numberConverter( $end_user_rev_usd['total'] ,2,'pre') }}</td>
                <td class="gross_revenue_usd_avg">{{numberConverter( $end_user_rev_usd['avg'] ,2,'pre') }}</td>
                <td class="gross_revenue_usd_month">{{numberConverter( $end_user_rev_usd['t_mo_end'] ,2,'pre') }}</td>

                @if(isset($end_user_rev_usd['dates']) && !empty($end_user_rev_usd['dates']))
                @foreach ($end_user_rev_usd['dates'] as $rev_usd)
                <td class="gross_revenue_usd_data">{{numberConverter( $rev_usd['value'] ,2,'pre') }}</td>
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
                    <strong class="text-with-sup">Gross Revenue (USD)<sup><i class="ml-3 text-dark fa fa-info-circle" title="Revenue after share"></i></sup></strong>
                  </div>
                </td>
                <td class="share_total">{{numberConverter( $gros_rev_usd['total'] ,2,'pre') }}</td>
                <td class="share_avg">{{numberConverter( $gros_rev_usd['avg'] ,2,'pre') }}</td>
                <td class="share_month">{{numberConverter( $gros_rev_usd['t_mo_end'] ,2,'pre') }}</td>

                @if(isset($gros_rev_usd['dates']) && !empty($gros_rev_usd['dates']))
                @foreach ($gros_rev_usd['dates'] as $gros_rev)
                <td class="share_data">{{numberConverter( $gros_rev['value'] ,2,'pre') }}</td>
                @endforeach
                @endif
              </tr>
              @endif
              @endforeach
              @endif

              @if(isset($sumOfSummaryData) && !empty($sumOfSummaryData))
                @foreach ($sumOfSummaryData as $key =>$net_rev)
                @if($key == 'net_rev')
                <tr class="bg-young-blue gross_revenue">
                  <td class="font-weight-bold">
                    <div class="text-with-sup">
                      <strong class="text-with-sup">Net Revenue (USD)<sup><i class="ml-3 text-dark fa fa-info-circle" title="gross revenue USD - other tax"></i></sup></strong>
                    </div>
                  </td>
                  <td class="share_total">{{numberConverter( $net_rev['total'],2,'pre') }}</td>
                  <td class="share_avg">{{numberConverter( $net_rev['avg'],2,'pre') }}</td>
                  <td class="share_month">{{numberConverter( $net_rev['t_mo_end'],2,'pre') }}</td>

                  @if(isset($net_rev['dates']) && !empty($net_rev['dates']))
                  @foreach ($net_rev['dates'] as $net_revenue)
                  <td class="share_data">{{numberConverter( $net_revenue['value'],2,'pre') }}</td>
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
                <td class="cost_total">{{numberConverter( $cost_campaign['total'] ,3,'pre') }}</td>
                <td class="cost_avg">{{numberConverter( $cost_campaign['avg'] ,3,'pre') }}</td>
                <td class="cost_month">{{numberConverter( $cost_campaign['t_mo_end'] ,3,'pre') }}</td>

                @if(isset($cost_campaign['dates']) && !empty($cost_campaign['dates']))
                @foreach ($cost_campaign['dates'] as $costcampaign)
                <td class="cost_data">{{numberConverter( $costcampaign['value'] ,3,'pre') }}</td>
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
                    <div class="text-left"><strong class="text-with-sup">Other Cost<sup><i class="ml-3 text-dark fa fa-info-circle" title="Other cost = Hosting + Content + rnd + md + platform"></i></sup></strong></div>
                  </div>
                </td>
                <td class="other_cost_total">{{numberConverter( $other_cost['total'] ,2,'pre') }}</td>
                <td class="other_cost_avg">{{numberConverter( $other_cost['avg'] ,2,'pre') }}</td>
                <td class="other_cost_month">{{numberConverter( $other_cost['t_mo_end'] ,2,'pre') }}</td>

                @if(isset($other_cost['dates']) && !empty($other_cost['dates']))
                @foreach ($other_cost['dates'] as $othercost)
                <td class="other_cost_data">{{numberConverter( $othercost['value'] ,2,'pre') }}</td>
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
                <td class="hosting_cost_total">{{numberConverter( $hosting_cost['total'] ,2,'pre') }}</td>
                <td class="hosting_cost_avg">{{numberConverter( $hosting_cost['avg'] ,2,'pre') }}</td>
                <td class="hosting_cost_month">{{numberConverter( $hosting_cost['t_mo_end'] ,2,'pre') }}</td>

                @if(isset($hosting_cost['dates']) && !empty($hosting_cost['dates']))
                @foreach ($hosting_cost['dates'] as $hostingcost)
                <td class="hosting_cost_data">{{numberConverter( $hostingcost['value'] ,2,'pre') }}</td>
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
                <td class="content_total">{{numberConverter( $content['total'] ,2,'post','%') }}</td>
                <td class="content_avg">{{numberConverter( $content['avg'] ,2,'post','%') }}</td>
                <td class="content_month">{{numberConverter( $content['t_mo_end'] ,2,'post','%') }}</td>

                @if(isset($content['dates']) && !empty($content['dates']))
                @foreach ($content['dates'] as $contentdata)
                <td class="content_data">{{numberConverter( $contentdata['value'] ,2,'post','%') }}</td>
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
                <td class="md_total">{{numberConverter( $rnd['total'] ,2,'post','%') }}</td>
                <td class="md_avg">{{numberConverter( $rnd['avg'] ,2,'post','%') }}</td>
                <td class="md_month">{{numberConverter( $rnd['t_mo_end'] ,2,'post','%') }}</td>

                @if(isset($rnd['dates']) && !empty($rnd['dates']))
                @foreach ($rnd['dates'] as $rnddata)
                <td class="md_data">{{numberConverter( $rnddata['value'] ,2,'post','%') }}</td>
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
                  <div style="font-weight: bolder; position: relative; left: 30px;">BD 2.5%</div>
                </td>
                <td class="bd_total">{{numberConverter( $bd['total'] ,2,'post','%') }}</td>
                <td class="bd_avg">{{numberConverter( $bd['avg'] ,2,'post','%') }}</td>
                <td class="bd_month">{{numberConverter( $bd['t_mo_end'] ,2,'post','%') }}</td>

                @if(isset($bd['dates']) && !empty($bd['dates']))
                @foreach ($bd['dates'] as $bddata)
                <td class="bd_data">{{numberConverter( $bddata['value'] ,2,'post','%') }}</td>
                @endforeach
                @endif
              </tr>
              @endif
              @endforeach
              @endif

              @if(isset($sumOfSummaryData) && !empty($sumOfSummaryData))
              @foreach ($sumOfSummaryData as $key =>$market_cost)
              @if($key == 'market_cost')
              <tr class="bg-young-yellow hiddenSoSOtherCostTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="font-weight: bolder; position: relative; left: 30px;">Marketing Cost 1.5%</div>
                </td>
                <td class="market_cost_total">{{numberConverter( $market_cost['total'] ,2,'hosting_cost') }}</td>
                <td class="market_cost_avg">{{numberConverter( $market_cost['avg'] ,2,'hosting_cost') }}</td>
                <td class="market_cost_month">{{numberConverter( $market_cost['t_mo_end'] ,2,'hosting_cost') }}</td>

                @if(isset($market_cost['dates']) && !empty($market_cost['dates']))
                @foreach ($market_cost['dates'] as $marketCostdata)
                <td class="market_cost_data">{{numberConverter( $marketCostdata['value'] ,2,'hosting_cost') }}</td>
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
                <td class="platform_total">{{numberConverter( $platform['total'] ,2,'post','%') }}</td>
                <td class="platform_avg">{{numberConverter( $platform['avg'] ,2,'post','%') }}</td>
                <td class="platform_month">{{numberConverter( $platform['t_mo_end'] ,2,'post','%') }}</td>

                @if(isset($platform['dates']) && !empty($platform['dates']))
                @foreach ($platform['dates'] as $platformdata)
                <td class="platform_data">{{numberConverter( $platformdata['value'] ,2,'post','%') }}</td>
                @endforeach
                @endif
              </tr>
              @endif
              @endforeach
              @endif

              @if(isset($sumOfSummaryData) && !empty($sumOfSummaryData))
              @foreach ($sumOfSummaryData as $key =>$other_tax)
              @if($key == 'other_tax')
              <tr class="bg-young-yellow o_tax">
                <td class="font-weight-bold">
                  <div class="text-with-sup">
                    <div class="btn-ico-expand OtherTaxSepBtn" data-sign="plus" style="cursor: pointer;">+</div>
                    <div class="text-left"><strong class="text-with-sup">Other Tax<sup><i class="ml-3 text-dark fa fa-info-circle" title="Other Tax = Vat + Wht + Misc Tax"></i></sup></strong></div>
                  </div>
                </td>
                <td class="other_tax_total">{{numberConverter( $other_tax['total'],2,'pre') }}</td>
                <td class="other_tax_avg">{{numberConverter( $other_tax['avg'],2,'pre') }}</td>
                <td class="other_tax_month">{{numberConverter( $other_tax['t_mo_end'],2,'pre') }}</td>

                @if(isset($other_tax['dates'] ) && !empty($other_tax['dates'] ))
                @foreach ($other_tax['dates'] as $othertax)
                <td class="other_cost_data">{{numberConverter( $othertax['value'],2,'pre') }}</td>
                @endforeach
                @endif
              </tr>
              @endif
              @endforeach
              @endif

              @if(isset($sumOfSummaryData) && !empty($sumOfSummaryData))
              @foreach ($sumOfSummaryData as $key =>$vat)
              @if($key == 'vat')
              <tr class="bg-young-yellow hiddenSoSOtherTaxTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="position: relative; left: 30px; font-weight: bolder;">VAT</div>
                </td>
                <td class="vat_total">{{numberConverter( $vat['total'],2,'pre') }}</td>
                <td class="vat_avg">{{numberConverter( $vat['avg'],2,'pre') }}</td>
                <td class="vat_month">{{numberConverter( $vat['t_mo_end'],2,'pre') }}</td>

                @if(isset($vat['dates'] ) && !empty($vat['dates'] ))
                @foreach ($vat['dates'] as $vatTax)
                <td class="vat_data">{{numberConverter( $vatTax['value'],2,'pre') }}</td>
                @endforeach
                @endif
              </tr>
              @endif
              @endforeach
              @endif

              @if(isset($sumOfSummaryData) && !empty($sumOfSummaryData))
              @foreach ($sumOfSummaryData as $key =>$wht)
              @if($key == 'wht')
              <tr class="bg-young-yellow hiddenSoSOtherTaxTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="position: relative; left: 30px; font-weight: bolder;">Wht</div>
                </td>
                <td class="wht_total">{{numberConverter( $wht['total'],2,'pre') }}</td>
                <td class="wht_avg">{{numberConverter( $wht['avg'],2,'pre') }}</td>
                <td class="wht_month">{{numberConverter( $wht['t_mo_end'],2,'pre') }}</td>

                @if(isset($wht['dates'] ) && !empty($wht['dates'] ))
                @foreach ($wht['dates'] as $whtTax)
                <td class="wht_data">{{numberConverter( $whtTax['value'],2,'pre') }}</td>
                @endforeach
                @endif
              </tr>
              @endif
              @endforeach
              @endif

              @if(isset($sumOfSummaryData) && !empty($sumOfSummaryData))
              @foreach ($sumOfSummaryData as $key =>$misc_tax)
              @if($key == 'misc_tax')
              <tr class="bg-young-yellow hiddenSoSOtherTaxTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="font-weight: bolder; position: relative; left: 30px;">Misc Tax</div>
                </td>
                <td class="misc_tax_total">{{numberConverter( $misc_tax['total'],2,'pre') }}</td>
                <td class="misc_tax_avg">{{numberConverter( $misc_tax['avg'],2,'pre') }}</td>
                <td class="misc_tax_month">{{numberConverter( $misc_tax['t_mo_end'],2,'pre') }}</td>

                @if(isset($misc_tax['dates'] )&& !empty($misc_tax['dates']))
                @foreach ($misc_tax['dates'] as $miscTax)
                <td class="misc_tax_data">{{numberConverter( $miscTax['value'],2,'pre') }}</td>
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
                <td class="font-weight-bold"><strong class="text-with-sup">GP<sup><i class="ml-3 text-dark fa fa-info-circle" title="GP = Net Revenue - (Cost Campaign + other cost)"></i></sup></strong>
                </td>
                <td class="pnl_total">{{numberConverter( $pnl['total'] ,2,'pre') }}</td>
                <td class="pnl_avg">{{numberConverter( $pnl['avg'] ,2,'pre') }}</td>
                <td class="pnl_month">{{numberConverter( $pnl['t_mo_end'] ,2,'pre') }}</td>

                @if(isset($pnl['dates']) && !empty($pnl['dates']))
                @foreach ($pnl['dates'] as $pnldata)
                <td class="pnl_data">{{numberConverter( $pnldata['value'] ,2,'pre') }}</td>
                @endforeach
                @endif
              </tr>
              @endif
              @endforeach
              @endif
            </tbody>
            @endif

            @if(isset($sumOfSummaryData['net_after_tax']))
            <tbody>
              @if(isset($sumOfSummaryData) && !empty($sumOfSummaryData))
              @foreach ($sumOfSummaryData as $key =>$end_user_rev_usd)
              @if($key == 'end_user_rev_usd')
              <tr class="bg-young-blue end_user_revenue">
                <td class="font-weight-bold">
                  <div class="text-with-sup">
                    <div class="text-left"><strong>GMV (USD)<sup><i class="ml-3 text-dark fa fa-info-circle" title="Revenue that we charge user excluding tax"></i></sup></strong></div>
                  </div>
                </td>
                <td class="gross_revenue_usd_total">{{numberConverter( $end_user_rev_usd['total'],2,'pre') }}</td>
                <td class="gross_revenue_usd_avg">{{numberConverter( $end_user_rev_usd['avg'],2,'pre') }}</td>
                <td class="gross_revenue_usd_month">{{numberConverter( $end_user_rev_usd['t_mo_end'],2,'pre') }}</td>
                <td class="gross_revenue_usd_month">{{ (date('d') != 1) ? isset($end_user_rev_usd['dates'][date('Y-m')]) ? numberConverter(($end_user_rev_usd['dates'][date('Y-m')]['value']/(date('d') - 1) * date('t')),2,'pre') : 'N/A' : 'N/A' }}</td>

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
                    <strong class="text-with-sup">Gross Revenue (USD)<sup><i class="ml-3 text-dark fa fa-info-circle" title="Revenue after share"></i></sup></strong>
                  </div>
                </td>
                <td class="share_total">{{numberConverter( $gros_rev_usd['total'],2,'pre') }}</td>
                <td class="share_avg">{{numberConverter( $gros_rev_usd['avg'],2,'pre') }}</td>
                <td class="share_month">{{numberConverter( $gros_rev_usd['t_mo_end'],2,'pre') }}</td>
                <td class="share_month">{{ (date('d') != 1) ? isset($gros_rev_usd['dates'][date('Y-m')]) ? numberConverter(($gros_rev_usd['dates'][date('Y-m')]['value']/(date('d') - 1) * date('t')),2,'pre') : 'N/A' : 'N/A' }}</td>

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
              @foreach ($sumOfSummaryData as $key =>$other_tax)
              @if($key == 'other_tax')
              <tr class="bg-young-yellow o_tax">
                <td class="font-weight-bold">
                  <div class="text-with-sup">
                    <div class="btn-ico-expand OtherTaxSepBtn" data-sign="plus" style="cursor: pointer;">+</div>
                    <div class="text-left"><strong class="text-with-sup">Other Tax<sup><i class="ml-3 text-dark fa fa-info-circle" title="Other Tax = Vat + Wht + Misc Tax"></i></sup></strong></div>
                  </div>
                </td>
                <td class="other_tax_total">{{numberConverter( $other_tax['total'],2,'pre') }}</td>
                <td class="other_tax_avg">{{numberConverter( $other_tax['avg'],2,'pre') }}</td>
                <td class="other_tax_month">{{numberConverter( $other_tax['t_mo_end'],2,'pre') }}</td>
                <td class="other_tax_month">{{ (date('d') != 1) ? isset($other_tax['dates'][date('Y-m')]) ? numberConverter(($other_tax['dates'][date('Y-m')]['value']/(date('d') - 1) * date('t')),2,'pre') : 'N/A' : 'N/A' }}</td>

                @if(isset($other_tax['dates'] ) && !empty($other_tax['dates'] ))
                @foreach ($other_tax['dates'] as $othertax)
                <td class="other_cost_data">{{numberConverter( $othertax['value'],2,'pre') }}</td>
                @endforeach
                @endif
              </tr>
              @endif
              @endforeach
              @endif

              @if(isset($sumOfSummaryData) && !empty($sumOfSummaryData))
              @foreach ($sumOfSummaryData as $key =>$vat)
              @if($key == 'vat')
              <tr class="bg-young-yellow hiddenSoSOtherTaxTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="position: relative; left: 30px; font-weight: bolder;">VAT</div>
                </td>
                <td class="vat_total">{{numberConverter( $vat['total'],2,'pre') }}</td>
                <td class="vat_avg">{{numberConverter( $vat['avg'],2,'pre') }}</td>
                <td class="vat_month">{{numberConverter( $vat['t_mo_end'],2,'pre') }}</td>
                <td class="vat_month">{{ (date('d') != 1) ? isset($vat['dates'][date('Y-m')]) ? numberConverter(($vat['dates'][date('Y-m')]['value']/(date('d') - 1) * date('t')),2,'pre') : 'N/A' : 'N/A' }}</td>

                @if(isset($vat['dates'] ) && !empty($vat['dates'] ))
                @foreach ($vat['dates'] as $vatTax)
                <td class="vat_data">{{numberConverter( $vatTax['value'],2,'pre') }}</td>
                @endforeach
                @endif
              </tr>
              @endif
              @endforeach
              @endif

              @if(isset($sumOfSummaryData) && !empty($sumOfSummaryData))
              @foreach ($sumOfSummaryData as $key =>$wht)
              @if($key == 'wht')
              <tr class="bg-young-yellow hiddenSoSOtherTaxTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="position: relative; left: 30px; font-weight: bolder;">Wht</div>
                </td>
                <td class="wht_total">{{numberConverter( $wht['total'],2,'pre') }}</td>
                <td class="wht_avg">{{numberConverter( $wht['avg'],2,'pre') }}</td>
                <td class="wht_month">{{numberConverter( $wht['t_mo_end'],2,'pre') }}</td>
                <td class="wht_month">{{ (date('d') != 1) ? isset($wht['dates'][date('Y-m')]) ? numberConverter(($wht['dates'][date('Y-m')]['value']/(date('d') - 1) * date('t')),2,'pre') : 'N/A' : 'N/A' }}</td>

                @if(isset($wht['dates'] ) && !empty($wht['dates'] ))
                @foreach ($wht['dates'] as $whtTax)
                <td class="wht_data">{{numberConverter( $whtTax['value'],2,'pre') }}</td>
                @endforeach
                @endif
              </tr>
              @endif
              @endforeach
              @endif

              @if(isset($sumOfSummaryData) && !empty($sumOfSummaryData))
              @foreach ($sumOfSummaryData as $key =>$misc_tax)
              @if($key == 'misc_tax')
              <tr class="bg-young-yellow hiddenSoSOtherTaxTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="font-weight: bolder; position: relative; left: 30px;">Misc Tax</div>
                </td>
                <td class="misc_tax_total">{{numberConverter( $misc_tax['total'],2,'pre') }}</td>
                <td class="misc_tax_avg">{{numberConverter( $misc_tax['avg'],2,'pre') }}</td>
                <td class="misc_tax_month">{{numberConverter( $misc_tax['t_mo_end'],2,'pre') }}</td>
                <td class="misc_tax_month">{{ (date('d') != 1) ? isset($misc_tax['dates'][date('Y-m')]) ? numberConverter(($misc_tax['dates'][date('Y-m')]['value']/(date('d') - 1) * date('t')),2,'pre') : 'N/A' : 'N/A' }}</td>

                @if(isset($misc_tax['dates'] )&& !empty($misc_tax['dates']))
                @foreach ($misc_tax['dates'] as $miscTax)
                <td class="misc_tax_data">{{numberConverter( $miscTax['value'],2,'pre') }}</td>
                @endforeach
                @endif
              </tr>
              @endif
              @endforeach
              @endif

              @if(isset($sumOfSummaryData) && !empty($sumOfSummaryData))
              @foreach ($sumOfSummaryData as $key =>$net_after_tax)
              @if($key == 'net_after_tax')
              <tr class="bg-young-yellow gross_revenue">
                <td class="font-weight-bold">
                  <div class="text-with-sup">
                    <strong class="text-with-sup">Net Revenue (USD)<sup><i class="ml-3 text-dark fa fa-info-circle" title="gross revenue USD - other tax"></i></sup></strong>
                  </div>
                </td>
                <td class="share_total">{{numberConverter( $net_after_tax['total'],2,'pre') }}</td>
                <td class="share_avg">{{numberConverter( $net_after_tax['avg'],2,'pre') }}</td>
                <td class="share_month">{{numberConverter( $net_after_tax['t_mo_end'],2,'pre') }}</td>
                <td class="share_month">{{ (date('d') != 1) ? isset($net_after_tax['dates'][date('Y-m')]) ? numberConverter(($net_after_tax['dates'][date('Y-m')]['value']/(date('d') - 1) * date('t')),2,'pre') : 'N/A' : 'N/A' }}</td>

                @if(isset($net_after_tax['dates']) && !empty($net_after_tax['dates']))
                @foreach ($net_after_tax['dates'] as $net_rev)
                <td class="share_data">{{numberConverter( $net_rev['value'],2,'pre') }}</td>
                @endforeach
                @endif
              </tr>
              @endif
              @endforeach
              @endif

              @if(isset($sumOfSummaryData) && !empty($sumOfSummaryData))
              @foreach ($sumOfSummaryData as $key =>$other_cost)
              @if($key == 'other_cost')
              <tr class="bg-old-blue o_cost">
                <td class="font-weight-bold">
                  <div class="text-with-sup">
                    <div class="btn-ico-expand other_cost OtherCostSepBtn" data-sign="plus" style="cursor: pointer;">+</div>
                    <div class="text-left"><strong class="text-with-sup">Other Cost<sup><i class="ml-3 text-dark fa fa-info-circle" title="Other cost = Hosting + Content + rnd + md + platform"></i></sup></strong></div>
                  </div>
                </td>
                <td class="other_cost_total">{{numberConverter( $other_cost['total'],2,'pre') }}</td>
                <td class="other_cost_avg">{{numberConverter( $other_cost['avg'],2,'pre') }}</td>
                <td class="other_cost_month">{{numberConverter( $other_cost['t_mo_end'],2,'pre') }}</td>
                <td class="other_cost_month">{{ (date('d') != 1) ? isset($other_cost['dates'][date('Y-m')]) ? numberConverter(($other_cost['dates'][date('Y-m')]['value']/(date('d') - 1) * date('t')),2,'pre') : 'N/A' : 'N/A' }}</td>

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
              <tr class="bg-old-blue hiddenSoSOtherCostTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="position: relative; left: 30px; font-weight: bolder;">Hosting Cost</div>
                </td>
                <td class="hosting_cost_total">{{numberConverter( $hosting_cost['total'],2,'pre') }}</td>
                <td class="hosting_cost_avg">{{numberConverter( $hosting_cost['avg'],2,'pre') }}</td>
                <td class="hosting_cost_month">{{numberConverter( $hosting_cost['t_mo_end'],2,'pre') }}</td>
                <td class="hosting_cost_month">{{ (date('d') != 1) ? isset($hosting_cost['dates'][date('Y-m')]) ? numberConverter(($hosting_cost['dates'][date('Y-m')]['value']/(date('d') - 1) * date('t')),2,'pre') : 'N/A' : 'N/A' }}</td>

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
              <tr class="bg-old-blue hiddenSoSOtherCostTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="position: relative; left: 30px; font-weight: bolder;">Content 2%</div>
                </td>
                <td class="content_total">{{numberConverter( $content['total'],2,'pre') }}</td>
                <td class="content_avg">{{numberConverter( $content['avg'],2,'pre') }}</td>
                <td class="content_month">{{numberConverter( $content['t_mo_end'],2,'pre') }}</td>
                <td class="content_month">{{ (date('d') != 1) ? isset($content['dates'][date('Y-m')]) ? numberConverter(($content['dates'][date('Y-m')]['value']/(date('d') - 1) * date('t')),2,'pre') : 'N/A' : 'N/A' }}</td>

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
              <tr class="bg-old-blue hiddenSoSOtherCostTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="font-weight: bolder; position: relative; left: 30px;">RND 5%</div>
                </td>
                <td class="md_total">{{numberConverter( $rnd['total'],2,'pre') }}</td>
                <td class="md_avg">{{numberConverter( $rnd['avg'],2,'pre') }}</td>
                <td class="md_month">{{numberConverter( $rnd['t_mo_end'],2,'pre') }}</td>
                <td class="md_month">{{ (date('d') != 1) ? isset($rnd['dates'][date('Y-m')]) ? numberConverter(($rnd['dates'][date('Y-m')]['value']/(date('d') - 1) * date('t')),2,'pre') : 'N/A' : 'N/A' }}</td>

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
              <tr class="bg-old-blue hiddenSoSOtherCostTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="font-weight: bolder; position: relative; left: 30px;">BD 2.5%</div>
                </td>
                <td class="bd_total">{{numberConverter( $bd['total'],2,'pre') }}</td>
                <td class="bd_avg">{{numberConverter( $bd['avg'],2,'pre') }}</td>
                <td class="bd_month">{{numberConverter( $bd['t_mo_end'],2,'pre') }}</td>
                <td class="bd_month">{{ (date('d') != 1) ? isset($bd['dates'][date('Y-m')]) ? numberConverter(($bd['dates'][date('Y-m')]['value']/(date('d') - 1) * date('t')),2,'pre') : 'N/A' : 'N/A' }}</td>

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
              @foreach ($sumOfSummaryData as $key =>$market_cost)
              @if($key == 'market_cost')
              <tr class="bg-old-blue hiddenSoSOtherCostTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="font-weight: bolder; position: relative; left: 30px;">Marketing Cost 1.5%</div>
                </td>
                <td class="market_cost_total">{{numberConverter( $market_cost['total'] ,2,'hosting_cost') }}</td>
                <td class="market_cost_avg">{{numberConverter( $market_cost['avg'] ,2,'hosting_cost') }}</td>
                <td class="market_cost_month">{{numberConverter( $market_cost['t_mo_end'] ,2,'hosting_cost') }}</td>
                <td class="market_cost_month">{{ (date('d') != 1) ? isset($market_cost['dates'][date('Y-m')]) ? numberConverter(($market_cost['dates'][date('Y-m')]['value']/(date('d') - 1) * date('t')),2,'pre') : 'N/A' : 'N/A' }}</td>

                @if(isset($market_cost['dates']) && !empty($market_cost['dates']))
                @foreach ($market_cost['dates'] as $marketCostdata)
                <td class="market_cost_data">{{numberConverter( $marketCostdata['value'] ,2,'hosting_cost') }}</td>
                @endforeach
                @endif
              </tr>
              @endif
              @endforeach
              @endif

              @if(isset($sumOfSummaryData) && !empty($sumOfSummaryData))
              @foreach ($sumOfSummaryData as $key =>$misc_cost)
              @if($key == 'misc_cost')
              <tr class="bg-old-blue hiddenSoSOtherCostTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="font-weight: bolder; position: relative; left: 30px;">Misc Cost</div>
                </td>
                <td class="platform_total">{{numberConverter( $misc_cost['total'],2,'pre') }}</td>
                <td class="platform_avg">{{numberConverter( $misc_cost['avg'],2,'pre') }}</td>
                <td class="platform_month">{{numberConverter( $misc_cost['t_mo_end'],2,'pre') }}</td>
                <td class="platform_month">{{ (date('d') != 1) ? isset($misc_cost['dates'][date('Y-m')]) ? numberConverter(($misc_cost['dates'][date('Y-m')]['value']/(date('d') - 1) * date('t')),2,'pre') : 'N/A' : 'N/A' }}</td>

                @if(isset($misc_cost['dates']) && !empty($misc_cost['dates']))
                @foreach ($misc_cost['dates'] as $miscCost)
                <td class="platform_data">{{numberConverter( $miscCost['value'],2,'pre') }}</td>
                @endforeach
                @endif
              </tr>
              @endif
              @endforeach
              @endif

              @if(isset($sumOfSummaryData) && !empty($sumOfSummaryData))
              @foreach ($sumOfSummaryData as $key =>$cost_campaign)
              @if($key == 'cost_campaign')
              <tr class="bg-old-blue cost_campaign">
                <td class="font-weight-bold">
                  <div class="text-with-sup">
                    <div class="text-left"><strong>Cost Campaign (USD)</strong></div>
                  </div>
                </td>
                <td class="cost_total">{{numberConverter( $cost_campaign['total'],3,'pre') }}</td>
                <td class="cost_avg">{{numberConverter( $cost_campaign['avg'],3,'pre') }}</td>
                <td class="cost_month">{{numberConverter( $cost_campaign['t_mo_end'],3,'pre') }}</td>
                <td class="cost_month">{{ (date('d') != 1) ? isset($cost_campaign['dates'][date('Y-m')]) ? numberConverter(($cost_campaign['dates'][date('Y-m')]['value']/(date('d') - 1) * date('t')),2,'pre') : 'N/A' : 'N/A' }}</td>

                @if(isset($cost_campaign['dates']) && !empty($cost_campaign['dates']))
                @foreach ($cost_campaign['dates'] as $costcampaign)
                <td class="cost_data">{{numberConverter( $costcampaign['value'],3,'pre') }}</td>
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
                <td class="font-weight-bold"><strong class="text-with-sup">GP<sup><i class="ml-3 text-dark fa fa-info-circle" title="GP = Net Revenue - (Cost Campaign + other cost)"></i></sup></strong>
                </td>
                <td class="pnl_total">{{numberConverter( $pnl['total'],2,'pre') }}</td>
                <td class="pnl_avg">{{numberConverter( $pnl['avg'],2,'pre') }}</td>
                <td class="pnl_month">{{numberConverter( $pnl['t_mo_end'],2,'pre') }}</td>
                <td class="pnl_month">{{ (date('d') != 1) ? isset($pnl['dates'][date('Y-m')]) ? numberConverter(($pnl['dates'][date('Y-m')]['value']/(date('d') - 1) * date('t')),2,'pre') : 'N/A' : 'N/A' }}</td>

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
              @foreach ($sumOfSummaryData as $key =>$mo)
              @if($key == 'mo')
              <tr class="bg-old-yellow gross_revenue">
                <td class="font-weight-bold">
                  <div class="text-with-sup">
                    <strong class="text-with-sup">Campaign MO</strong>
                  </div>
                </td>
                <td class="share_total">{{numberConverter( $mo['total'],2,'pre') }}</td>
                <td class="share_avg">{{numberConverter( $mo['avg'],2,'pre') }}</td>
                <td class="share_month">{{numberConverter( $mo['t_mo_end'],2,'pre') }}</td>
                <td class="share_month">{{ (date('d') != 1) ? isset($mo['dates'][date('Y-m')]) ? numberConverter(($mo['dates'][date('Y-m')]['value']/(date('d') - 1) * date('t')),2,'pre') : 'N/A' : 'N/A' }}</td>

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
              @foreach ($sumOfSummaryData as $key =>$price_mo)
              @if($key == 'price_mo')
              <tr class="bg-old-yellow price_mo">
                <td class="font-weight-bold">
                  <div class="text-with-sup">
                    <div class="text-left"><strong>Price/Mo</strong></div>
                  </div>
                </td>
                <td class="cost_total">N/A</td>
                <td class="cost_avg">{{numberConverter( $price_mo['avg'],2,'pre') }}</td>
                <td class="cost_month">N/A</td>
                <td class="cost_month">N/A</td>

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
              <tr class="bg-old-yellow active_subs">
                <td class="font-weight-bold">
                  <div class="text-with-sup">
                    <div class="text-left"><strong>Active Subscriber</strong></div>
                  </div>
                </td>
                <td class="cost_total">{{numberConverter( $active_subs['total'],2,'pre') }}</td>
                <td class="cost_avg">N/A</td>
                <td class="cost_month">N/A</td>
                <td class="cost_month">N/A</td>

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
              @foreach ($sumOfSummaryData as $key =>$reg)
              @if($key == 'reg')
              <tr class="bg-old-yellow reg">
                <td class="font-weight-bold">
                  <div class="text-with-sup">
                    <div class="text-left"><strong>Reg</strong></div>
                  </div>
                </td>
                <td class="cost_total">{{numberConverter( $reg['total'],2,'pre') }}</td>
                <td class="cost_avg">{{numberConverter( $reg['avg'],2,'pre') }}</td>
                <td class="cost_month">{{numberConverter( $reg['t_mo_end'],2,'pre') }}</td>
                <td class="cost_month">{{ (date('d') != 1) ? isset($reg['dates'][date('Y-m')]) ? numberConverter(($reg['dates'][date('Y-m')]['value']/(date('d') - 1) * date('t')),2,'pre') : 'N/A' : 'N/A' }}</td>

                @if(isset($reg['dates']) && !empty($reg['dates']))
                @foreach ($reg['dates'] as $activesub)
                <td class="cost_data">{{numberConverter( $activesub['value'],2,'pre') }}</td>
                @endforeach
                @endif
              </tr>
              @endif
              @endforeach
              @endif

              @if(isset($sumOfSummaryData) && !empty($sumOfSummaryData))
              @foreach ($sumOfSummaryData as $key =>$unreg)
              @if($key == 'unreg')
              <tr class="bg-old-yellow unreg">
                <td class="font-weight-bold">
                  <div class="text-with-sup">
                    <div class="text-left"><strong>Unreg</strong></div>
                  </div>
                </td>
                <td class="cost_total">{{numberConverter( $unreg['total'],2,'pre') }}</td>
                <td class="cost_avg">{{numberConverter( $unreg['avg'],2,'pre') }}</td>
                <td class="cost_month">{{numberConverter( $unreg['t_mo_end'],2,'pre') }}</td>
                <td class="cost_month">{{ (date('d') != 1) ? isset($unreg['dates'][date('Y-m')]) ? numberConverter(($unreg['dates'][date('Y-m')]['value']/(date('d') - 1) * date('t')),2,'pre') : 'N/A' : 'N/A' }}</td>

                @if(isset($unreg['dates']) && !empty($unreg['dates']))
                @foreach ($unreg['dates'] as $activesub)
                <td class="cost_data">{{numberConverter( $activesub['value'],2,'pre') }}</td>
                @endforeach
                @endif
              </tr>
              @endif
              @endforeach
              @endif

              @if(isset($sumOfSummaryData) && !empty($sumOfSummaryData))
              @foreach ($sumOfSummaryData as $key =>$renewal)
              @if($key == 'renewal')
              <tr class="bg-old-yellow renewal">
                <td class="font-weight-bold">
                  <div class="text-with-sup">
                    <div class="text-left"><strong>Renewal</strong></div>
                  </div>
                </td>
                <td class="cost_total">{{numberConverter( $renewal['total'],2,'pre') }}</td>
                <td class="cost_avg">{{numberConverter( $renewal['avg'],2,'pre') }}</td>
                <td class="cost_month">{{numberConverter( $renewal['t_mo_end'],2,'pre') }}</td>
                <td class="cost_month">{{ (date('d') != 1) ? isset($renewal['dates'][date('Y-m')]) ? numberConverter(($renewal['dates'][date('Y-m')]['value']/(date('d') - 1) * date('t')),2,'pre') : 'N/A' : 'N/A' }}</td>

                @if(isset($renewal['dates']) && !empty($renewal['dates']))
                @foreach ($renewal['dates'] as $renew)
                <td class="cost_data">{{numberConverter( $renew['value'],2,'pre') }}</td>
                @endforeach
                @endif
              </tr>
              @endif
              @endforeach
              @endif

              @if(isset($sumOfSummaryData) && !empty($sumOfSummaryData))
              @foreach ($sumOfSummaryData as $key =>$arpu_30_usd)
              @if($key == 'arpu_30_usd')
              <tr class="bg-old-yellow">
                <td class="font-weight-bold">
                  <div class="text-with-sup">
                    <div class="text-left"><strong class="text-with-sup">30 ARPU</strong></div>
                  </div>
                </td>
                <td class="cost_total">N/A</td>
                <td class="cost_avg">{{ numberConverter( $arpu_30_usd['avg'],4,'pre') }}</td>
                <td class="cost_month">N/A</td>
                <td class="cost_month">N/A</td>

                @if(isset($arpu_30_usd['dates']) && !empty($arpu_30_usd['dates']))
                @foreach ($arpu_30_usd['dates'] as $arpu)
                <td class="cost_data">{{numberConverter( $arpu['value'],4,'pre') }}</td>
                @endforeach
                @endif
              </tr>
              @endif
              @endforeach
              @endif

              @if(isset($sumOfSummaryData) && !empty($sumOfSummaryData))
              @foreach ($sumOfSummaryData as $key =>$roi)
              @if($key == 'roi')
              <tr class="bg-old-yellow roi">
                <td class="font-weight-bold">
                  <div class="text-with-sup">
                    <div class="text-left"><strong class="text-with-sup" style="position: relative; left: -1px;">ROI<sup><i class="ml-3 text-dark fa fa-info-circle" title="ROI = Price/Mo / 30 ARPU"></i></sup></strong></div>
                  </div>
                </td>
                <td class="cost_total">N/A</td>
                <td class="cost_avg">{{ numberConverter( $roi['dates'][$date]['value'],4,'pre') }}</td>
                <td class="cost_month">N/A</td>
                <td class="cost_month">N/A</td>

                @if(isset($roi['dates']) && !empty($roi['dates']))
                @foreach ($roi['dates'] as $roI)
                <td class="cost_data">{{numberConverter( $roI['value'],4,'pre') }}</td>
                @endforeach
                @endif
              </tr>
              @endif
              @endforeach
              @endif

              @if(isset($sumOfSummaryData) && !empty($sumOfSummaryData))
              @foreach ($sumOfSummaryData as $key =>$bill)
              @if($key == 'bill')
              <tr class="bg-old-yellow bill">
                <td class="font-weight-bold">
                  <div class="text-with-sup">
                    <div class="billSeperateBtn pull-left" data-sign="plus" style="cursor: pointer;">+</div>
                    <div class="text-left"><strong class="text-with-sup" style="position: relative; left: -1px;">Bill %<sup><i class="ml-3 text-dark fa fa-info-circle" title="(total daily push delivered / total subscriber) * 100%"></i></sup></strong></div>
                  </div>
                </td>
                <td class="cost_total">N/A</td>
                <td class="cost_avg">{{ numberConverter( $bill['avg'],2,'pre') }}</td>
                <td class="cost_month">N/A</td>
                <td class="cost_month">N/A</td>

                @if(isset($bill['dates']) && !empty($bill['dates']))
                @foreach ($bill['dates'] as $billrate)
                <td class="cost_data">{{numberConverter( $billrate['value'],2,'pre') }}</td>
                @endforeach
                @endif
              </tr>
              @endif
              @endforeach
              @endif

              @if(isset($sumOfSummaryData) && !empty($sumOfSummaryData))
              @foreach ($sumOfSummaryData as $key =>$firstpush)
              @if($key == 'firstpush')
              <tr class="bg-old-yellow billExtendedRows" style="display: none;">
                <td class="font-weight-bold">
                  <div class="text-with-sup">
                    <strong class="text-with-sup">First P.%<sup><i class="ml-3 text-dark fa fa-info-circle" title="(delivered first push / first push sent) * 100% "></i></sup></strong>
                  </div>
                </td>
                <td class="cost_total">N/A</td>
                <td class="cost_avg">{{ numberConverter( $firstpush['avg'],2,'pre') }}</td>
                <td class="cost_month">N/A</td>
                <td class="cost_month">N/A</td>

                @if(isset($firstpush['dates']) && !empty($firstpush['dates']))
                @foreach ($firstpush['dates'] as $FirstPush)
                <td class="cost_data">{{numberConverter( $FirstPush['value'],2,'pre') }}</td>
                @endforeach
                @endif
              </tr>
              @endif
              @endforeach
              @endif

              @if(isset($sumOfSummaryData) && !empty($sumOfSummaryData))
              @foreach ($sumOfSummaryData as $key =>$dailypush)
              @if($key == 'dailypush')
              <tr class="bg-old-yellow billExtendedRows" style="display: none;">
                <td class="font-weight-bold">
                  <div class="text-with-sup">
                    <strong class="text-with-sup">Daily P.%<sup><i class="ml-3 text-dark fa fa-info-circle" title="(delivered daily push / daily push sent) * 100%"></i></sup></strong>
                  </div>
                </td>
                <td class="cost_total">N/A</td>
                <td class="cost_avg">{{ numberConverter( $dailypush['avg'],2,'pre') }}</td>
                <td class="cost_month">N/A</td>
                <td class="cost_month">N/A</td>

                @if(isset($dailypush['dates']) && !empty($dailypush['dates']))
                @foreach ($dailypush['dates'] as $DailyPush)
                <td class="cost_data">{{numberConverter( $DailyPush['value'],2,'pre') }}</td>
                @endforeach
                @endif
              </tr>
              @endif
              @endforeach
              @endif
            </tbody>
            @endif
          </table>
        </div>
      </div>
    </div>
  </div>

  <div id="container">
    @if(isset($sumemry) && !empty($sumemry))
    @foreach ($sumemry as $report)
    <div class="ptable">
      <div class="d-flex align-items-center my-3">
        <span class="badge badge-with-flag badge-secondary px-2 bg-primary text-uppercase">
          <img src="{{ asset('/flags/'.$report['country']['flag']) }}" alt="{{$report['country']['country']}}" width="30">&nbsp; {{$report['country']['country_code']}} <a href="javascript:void(0);" class="text-white">{{ $report['country']['country']}} </a> | Last Update: {{isset($report['last_update'])?$report['last_update']:''}}
        </span>
        <span class="flex-fill ml-2 bg-primary w-100 font-weight-bold" style="height: 1px;"></span>
        <div class="text-right pl-2">
          <button class="btn btn-sm pnl-xls" style="color:white; background-color:green" data-param="Indonesia"><i class="fa fa-file-excel-o"></i>Export as XLS</button>
        </div>
      </div>
      <div class="card">
        <div class="table-responsive shadow-sm pnlDataTbl" id="Indonesia">
          <h1 style="display:none">GP Summary</h1>
          <table class="table table-light m-0 font-13 table-text-no-wrap" id="pnlTbl">
            <thead class="thead-dark">
              <tr>
                <th>Summary</th>
                <th>Total</th>
                <th>AVG</th>
                <th>T.MO.Year</th>
                @if(isset($sumOfSummaryData['net_after_tax']))
                <th>E.O.M</th>
                @endif
                
                @if(isset($no_of_days) && !empty($no_of_days))
                @foreach ($no_of_days as $days)
                <th>{{$days['no']}}</th>
                @endforeach
                @endif
              </tr>
            </thead>
            @if(!isset($report['net_after_tax']))
            <tbody>
              <tr class="bg-young-blue end_user_revenue">
                <td class="font-weight-bold">
                  <div class="text-with-sup">
                    <div class="btn-ico-expand grev_plus" data-sign="plus" style="cursor: pointer;">+</div>
                    <div class="text-left"><strong>GMV (USD)<sup><i class="ml-3 text-dark fa fa-info-circle" title="Revenue that we charge user excluding tax"></i></sup></strong></div>
                  </div>
                </td>
                <td class="gross_revenue_usd_total usd">{{numberConverter($report['end_user_rev_usd']['total'] ,2,'pre') }} </td>
                <td class="gross_revenue_usd_avg">{{numberConverter($report['end_user_rev_usd']['avg'] ,2,'pre') }}</td>
                <td class="gross_revenue_usd_month">{{numberConverter($report['end_user_rev_usd']['t_mo_end'] ,2,'pre') }}</td>

                @if(isset($report['end_user_rev_usd']['dates']) && !empty($report['end_user_rev_usd']['dates']))
                @foreach ($report['end_user_rev_usd']['dates'] as $end_user_rev_usd)
                <td class="gross_revenue_usd_data {{$end_user_rev_usd['class']}}">{{numberConverter($end_user_rev_usd['value'] ,2,'pre') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-young-blue hiddenRevTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="font-weight: bolder; position: relative; left: 30px;">GMV ({{$report['country']['currency_code']}})<sup><i class="ml-3 text-dark fa fa-info-circle" title="Revenue that we charge user excluding tax"></i></sup></div>
                </td>
                <td class="gross_revenue_total">{{numberConverter( $report['end_user_rev']['total'] ,2,'pre') }}</td>
                <td class="gross_revenue_avg">{{numberConverter( $report['end_user_rev']['avg'] ,2,'pre') }}</td>
                <td class="gross_revenue_month">{{numberConverter( $report['end_user_rev']['t_mo_end'] ,2,'pre') }}</td>

                @if(isset($report['end_user_rev']['dates']) && !empty($report['end_user_rev']['dates']))
                @foreach ($report['end_user_rev']['dates'] as $end_user_rev)
                <td class="gross_revenue_data">{{numberConverter( $end_user_rev['value'] ,2,'pre') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-young-blue gross_revenue">
                <td class="font-weight-bold">
                  <div class="text-with-sup">
                    <div class="btn-ico-expand gross_rev_usd_plus" data-sign="plus" style="cursor: pointer;">+</div>
                    <strong class="text-with-sup">Gross Revenue (USD)<sup><i class="ml-3 text-dark fa fa-info-circle" title="Revenue after share"></i></sup></strong>
                  </div>
                </td>
                <td class="share_total">{{numberConverter( $report['gros_rev_usd']['total'] ,2,'pre') }}</td>
                <td class="share_avg">{{numberConverter( $report['gros_rev_usd']['avg'] ,2,'pre') }}</td>
                <td class="share_month">{{numberConverter( $report['gros_rev_usd']['t_mo_end'] ,2,'pre') }}</td>

                @if(isset($report['gros_rev_usd']['dates']) && !empty($report['gros_rev_usd']['dates']))
                @foreach ($report['gros_rev_usd']['dates'] as $gros_rev_usd)
                <td class="share_data">{{numberConverter( $gros_rev_usd['value'] ,2,'pre') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-young-blue hiddenGrossRevUsdTr" style="display:none;">
                <td class="font-weight-bold">
                  <div style="font-weight: bolder; position: relative; left: 30px;">Gross Revenue ({{$report['country']['currency_code']}})</div>
                </td>
                <td class="local_share_total">{{numberConverter( $report['gros_rev']['total'] ,2,'pre') }}</td>
                <td class="local_share_avg">{{numberConverter( $report['gros_rev']['avg'] ,2,'pre') }}</td>
                <td class="local_share_month">{{numberConverter( $report['gros_rev']['t_mo_end'] ,2,'pre') }}</td>

                @if(isset($report['gros_rev']['dates']) && !empty($report['gros_rev']['dates']))
                @foreach ($report['gros_rev']['dates'] as $gros_rev)
                <td class="local_share_data">{{numberConverter( $gros_rev['value'] ,2,'pre') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-young-blue">
                <td class="font-weight-bold">
                  <strong class="text-with-sup">Net Revenue (USD)<sup><i class="ml-3 text-dark fa fa-info-circle" title="gross revenue USD - other tax"></i></sup></strong>
                </td>
                <td class="local_share_total">{{numberConverter( $report['net_rev']['total'] ,2,'hosting_cost') }}</td>
                <td class="local_share_month">{{numberConverter( $report['net_rev']['avg'] ,2,'hosting_cost') }}</td>
                <td class="local_share_avg">{{numberConverter( $report['net_rev']['t_mo_end'] ,2,'hosting_cost') }}</td>

                @if(isset($report['net_rev']['dates']) && !empty($report['net_rev']['dates']))
                @foreach ($report['net_rev']['dates'] as $net_rev)
                <td class="local_share_data">{{numberConverter( $net_rev['value'] ,2,'hosting_cost') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-young-red cost_campaign">
                <td class="font-weight-bold">
                  <div class="text-with-sup">
                    <div class="text-left"><strong>Cost Campaign (USD)</strong></div>
                  </div>
                </td>
                <td class="cost_total cost">{{numberConverter( $report['cost_campaign']['total'] ,3,'pre') }}</td>
                <td class="cost_avg">{{numberConverter( $report['cost_campaign']['avg'] ,3,'pre') }}</td>
                <td class="cost_month">{{numberConverter( $report['cost_campaign']['t_mo_end'] ,3,'pre') }}</td>

                @if(isset($report['cost_campaign']['dates']) && !empty($report['cost_campaign']['dates']))
                @foreach ($report['cost_campaign']['dates'] as $cost_campaign)
                <td class="cost_data">{{numberConverter( $cost_campaign['value'] ,3,'pre') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-young-yellow o_cost">
                <td class="font-weight-bold">
                  <div class="text-with-sup">
                    <div class="btn-ico-expand other_cost" data-sign="plus" style="cursor: pointer;">+</div>
                    <div class="text-left"><strong class="text-with-sup">Other Cost<sup><i class="ml-3 text-dark fa fa-info-circle" title="Other cost = Hosting + Content + rnd + md + platform"></i></sup></strong></div>
                  </div>
                </td>
                <td class="other_cost_total">{{numberConverter( $report['other_cost']['total'] ,2,'pre') }}</td>
                <td class="other_cost_avg">{{numberConverter( $report['other_cost']['avg'] ,2,'pre') }}</td>
                <td class="other_cost_month">{{numberConverter( $report['other_cost']['t_mo_end'] ,2,'pre') }}</td>

                @if(isset($report['other_cost']['dates']) && !empty($report['other_cost']['dates']))
                @foreach ($report['other_cost']['dates'] as $other_cost)
                <td class="other_cost_data">{{numberConverter( $other_cost['value'] ,2,'pre') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-young-yellow hiddenOtherCostTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="font-weight: bolder; position: relative; left: 30px;">Hosting Cost</div>
                </td>
                <td class="hosting_cost_total">{{numberConverter( $report['hosting_cost']['total'] ,2,'pre') }}</td>
                <td class="hosting_cost_avg">{{numberConverter( $report['hosting_cost']['avg'] ,2,'pre') }}</td>
                <td class="hosting_cost_month">{{numberConverter( $report['hosting_cost']['t_mo_end'] ,2,'pre') }}</td>

                @if(isset($report['hosting_cost']['dates']) && !empty($report['hosting_cost']['dates']))
                @foreach ($report['hosting_cost']['dates'] as $hosting_cost)
                <td class="hosting_cost_data">{{numberConverter( $hosting_cost['value'] ,2,'pre') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-young-yellow hiddenOtherCostTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="font-weight: bolder; position: relative; left: 30px;">Content 2%</div>
                </td>
                <td class="content_total">{{numberConverter( $report['content']['total'] ,2,'post','%') }}</td>
                <td class="content_avg">{{numberConverter( $report['content']['avg'] ,2,'post','%') }}</td>
                <td class="content_month">{{numberConverter( $report['content']['t_mo_end'] ,2,'post','%') }}</td>

                @if(isset($report['content']['dates']) && !empty($report['content']['dates']))
                @foreach ($report['content']['dates'] as $content)
                <td class="content_data">{{numberConverter( $content['value'] ,2,'post','%') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-young-yellow hiddenOtherCostTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="font-weight: bolder; position: relative; left: 30px;">RND 5%</div>
                </td>
                <td class="md_total">{{numberConverter( $report['rnd']['total'] ,2,'post','%') }}</td>
                <td class="md_avg">{{numberConverter( $report['rnd']['avg'] ,2,'post','%') }}</td>
                <td class="md_month">{{numberConverter( $report['rnd']['t_mo_end'] ,2,'post','%') }}</td>

                @if(isset($report['rnd']['dates']) && !empty($report['rnd']['dates']))
                @foreach ($report['rnd']['dates'] as $rnd)
                <td class="md_data">{{numberConverter( $rnd['value'] ,2,'post','%') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-young-yellow hiddenOtherCostTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="font-weight: bolder; position: relative; left: 30px;">BD 2.5%</div>
                </td>
                <td class="bd_total">{{numberConverter( $report['bd']['total'] ,2,'post','%') }}</td>
                <td class="bd_avg">{{numberConverter( $report['bd']['avg'] ,2,'post','%') }}</td>
                <td class="bd_month">{{numberConverter( $report['bd']['t_mo_end'] ,2,'post','%') }}</td>

                @if(isset($report['bd']['dates']) && !empty($report['bd']['dates']))
                @foreach ($report['bd']['dates'] as $bd)
                <td class="bd_data">{{numberConverter( $bd['value'] ,2,'post','%') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-young-yellow hiddenOtherCostTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="font-weight: bolder; position: relative; left: 30px;">Marketing Cost 1.5%</div>
                </td>
                <td class="market_cost_total">{{numberConverter( $report['market_cost']['total'] ,2,'hosting_cost') }}</td>
                <td class="market_cost_avg">{{numberConverter( $report['market_cost']['avg'] ,2,'hosting_cost') }}</td>
                <td class="market_cost_month">{{numberConverter( $report['market_cost']['t_mo_end'] ,2,'hosting_cost') }}</td>

                @if(isset($report['market_cost']['dates']) && !empty($report['market_cost']['dates']))
                @foreach ($report['market_cost']['dates'] as $marketCost)
                <td class="market_cost_data">{{numberConverter( $marketCost['value'] ,2,'hosting_cost') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-young-yellow hiddenOtherCostTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="font-weight: bolder; position: relative; left: 30px;">Vostok Platform Cost 10%</div>
                </td>
                <td class="platform_total">{{numberConverter( $report['platform']['total'] ,2,'pre') }}</td>
                <td class="platform_avg">{{numberConverter( $report['platform']['avg'] ,2,'pre') }}</td>
                <td class="platform_month">{{numberConverter( $report['platform']['t_mo_end'] ,2,'pre') }}</td>

                @if(isset($report['platform']['dates']) && !empty($report['platform']['dates']))
                @foreach ($report['platform']['dates'] as $platform)
                <td class="platform_data">{{numberConverter( $platform['value'] ,2,'pre') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-young-yellow o_tax">
                <td class="font-weight-bold">
                  <div class="text-with-sup">
                    <div class="btn-ico-expand other_tax" data-sign="plus" style="cursor: pointer;">+</div>
                    <div class="text-left"><strong class="text-with-sup">Other Tax<sup><i class="ml-3 text-dark fa fa-info-circle" title="Other Tax = VAT + WHT + MISC TAX"></i></sup></strong></div>
                  </div>
                </td>
                <td class="other_tax_total">{{numberConverter( $report['other_tax']['total'],2,'pre') }}</td>
                <td class="other_tax_avg">{{numberConverter( $report['other_tax']['avg'],2,'pre') }}</td>
                <td class="other_tax_month">{{numberConverter( $report['other_tax']['t_mo_end'],2,'pre') }}</td>

                @if(isset($report['other_tax']['dates'] ) && !empty($report['other_tax']['dates'] ))
                @foreach ($report['other_tax']['dates'] as $other_tax)
                <td class="other_tax_data">{{numberConverter( $other_tax['value'],2,'pre') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-young-yellow hiddenOtherTaxTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="font-weight: bolder; position: relative; left: 30px;">VAT</div>
                </td>
                <td class="hosting_cost_total">{{numberConverter( $report['vat']['total'],2,'pre') }}</td>
                <td class="hosting_cost_avg">{{numberConverter( $report['vat']['avg'],2,'pre') }}</td>
                <td class="hosting_cost_month">{{numberConverter( $report['vat']['t_mo_end'],2,'pre') }}</td>

                @if(isset($report['vat']['dates'] ) && !empty($report['vat']['dates'] ))
                @foreach ($report['vat']['dates'] as $vat)
                <td class="hosting_cost_data">{{numberConverter( $vat['value'],2,'pre') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-young-yellow hiddenOtherTaxTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="font-weight: bolder; position: relative; left: 30px;">WHT</div>
                </td>
                <td class="content_total">{{numberConverter( $report['wht']['total'],2,'pre') }}</td>
                <td class="content_avg">{{numberConverter( $report['wht']['avg'],2,'pre') }}</td>
                <td class="content_month">{{numberConverter( $report['wht']['t_mo_end'],2,'pre') }}</td>

                @if(isset($report['wht']['dates'] ) && !empty($report['wht']['dates'] ))
                @foreach ($report['wht']['dates'] as $wht)
                <td class="content_data">{{numberConverter( $wht['value'],2,'pre') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-young-yellow hiddenOtherTaxTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="font-weight: bolder; position: relative; left: 30px;">MISC TAX</div>
                </td>
                <td class="md_total">{{numberConverter( $report['misc_tax']['total'],2,'pre') }}</td>
                <td class="md_avg">{{numberConverter( $report['misc_tax']['avg'],2,'pre') }}</td>
                <td class="md_month">{{numberConverter( $report['misc_tax']['t_mo_end'],2,'pre') }}</td>

                @if(isset($report['misc_tax']['dates'] ) && !empty($report['misc_tax']['dates'] ))
                @foreach ($report['misc_tax']['dates'] as $misc_tax)
                <td class="md_data">{{numberConverter( $misc_tax['value'],2,'pre') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-young-green pnl">
                <td class="font-weight-bold"><strong class="text-with-sup">GP<sup><i class="ml-3 text-dark fa fa-info-circle" title="GP = Net Revenue - (Cost Campaign + other cost)"></i></sup></strong>
                </td>
                <td class="pnl_total p">{{numberConverter( $report['pnl']['total'] ,2,'pre') }}</td>
                <td class="pnl_avg">{{numberConverter( $report['pnl']['avg'] ,2,'pre') }}</td>
                <td class="pnl_month">{{numberConverter( $report['pnl']['t_mo_end'] ,2,'pre') }}</td>

                @if(isset($report['pnl']['dates']) && !empty($report['pnl']['dates']))
                @foreach ($report['pnl']['dates'] as $pnl)
                <td class="pnl_data">{{numberConverter( $pnl['value'] ,2,'pre') }}</td>
                @endforeach
                @endif
              </tr>
            </tbody>
            @endif

            @if(isset($report['net_after_tax']))
            <tbody>
              <tr class="bg-young-blue end_user_revenue">
                <td class="font-weight-bold">
                  <div class="text-left"><strong>GMV (USD)<sup><i class="ml-3 text-dark fa fa-info-circle" title="Revenue that we charge user excluding tax"></i></sup></strong></div>
                </td>
                <td class="gross_revenue_usd_total usd">{{numberConverter($report['end_user_rev_usd']['total'] ,2,'hosting_cost') }} </td>
                <td class="gross_revenue_usd_month">{{numberConverter($report['end_user_rev_usd']['avg'] ,2,'hosting_cost') }}</td>
                <td class="gross_revenue_usd_avg">{{numberConverter($report['end_user_rev_usd']['t_mo_end'] ,2,'hosting_cost') }}</td>
                <td class="gross_revenue_usd_month">{{ (date('d') != 1) ? isset($report['end_user_rev_usd']['dates'][date('Y-m')]) ? numberConverter(($report['end_user_rev_usd']['dates'][date('Y-m')]['value']/(date('d') - 1) * date('t')),2,'pre') : 'N/A' : 'N/A' }}</td>
                
                @if(isset($report['end_user_rev_usd']['dates']) && !empty($report['end_user_rev_usd']['dates']))
                @foreach ($report['end_user_rev_usd']['dates'] as $end_user_rev_usd)
                <td class="gross_revenue_usd_data {{$end_user_rev_usd['class']}}">{{numberConverter($end_user_rev_usd['value'] ,2,'hosting_cost') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-young-blue">
                <td class="font-weight-bold">
                  <div>GMV ({{$report['country']['currency_code']}})<sup><i class="ml-3 text-dark fa fa-info-circle" title="Revenue that we charge user excluding tax"></i></sup></div>
                </td>
                <td class="gross_revenue_total">{{numberConverter( $report['end_user_rev']['total'] ,2,'hosting_cost') }}</td>
                <td class="gross_revenue_month">{{numberConverter( $report['end_user_rev']['avg'] ,2,'hosting_cost') }}</td>
                <td class="gross_revenue_avg">{{numberConverter( $report['end_user_rev']['t_mo_end'] ,2,'hosting_cost') }}</td>
                <td class="gross_revenue_month">{{ (date('d') != 1) ? isset($report['end_user_rev']['dates'][date('Y-m')]) ? numberConverter(($report['end_user_rev']['dates'][date('Y-m')]['value']/(date('d') - 1) * date('t')),2,'pre') : 'N/A' : 'N/A' }}</td>

                @if(isset($report['end_user_rev']['dates']) && !empty($report['end_user_rev']['dates']))
                @foreach ($report['end_user_rev']['dates'] as $end_user_rev)
                <td class="gross_revenue_data">{{numberConverter( $end_user_rev['value'] ,2,'hosting_cost') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-young-blue gross_revenue">
                <td class="font-weight-bold">
                  <strong class="text-with-sup">Gross Revenue (USD)<sup><i class="ml-3 text-dark fa fa-info-circle" title="Revenue after share"></i></sup></strong>
                </td>
                <td class="share_total">{{numberConverter( $report['gros_rev_usd']['total'] ,2,'hosting_cost') }}</td>
                <td class="share_month">{{numberConverter( $report['gros_rev_usd']['avg'] ,2,'hosting_cost') }}</td>
                <td class="share_avg">{{numberConverter( $report['gros_rev_usd']['t_mo_end'] ,2,'hosting_cost') }}</td>
                <td class="share_month">{{ (date('d') != 1) ? isset($report['gros_rev_usd']['dates'][date('Y-m')]) ? numberConverter(($report['gros_rev_usd']['dates'][date('Y-m')]['value']/(date('d') - 1) * date('t')),2,'pre') : 'N/A' : 'N/A' }}</td>

                @if(isset($report['gros_rev_usd']['dates']) && !empty($report['gros_rev_usd']['dates']))
                @foreach ($report['gros_rev_usd']['dates'] as $gros_rev_usd)
                <td class="share_data">{{numberConverter( $gros_rev_usd['value'] ,2,'hosting_cost') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-young-blue">
                <td class="font-weight-bold">
                  <div>Gross Revenue ({{$report['country']['currency_code']}})</div>
                </td>
                <td class="local_share_total">{{numberConverter( $report['gros_rev']['total'] ,2,'hosting_cost') }}</td>
                <td class="local_share_month">{{numberConverter( $report['gros_rev']['avg'] ,2,'hosting_cost') }}</td>
                <td class="local_share_avg">{{numberConverter( $report['gros_rev']['t_mo_end'] ,2,'hosting_cost') }}</td>
                <td class="gros_rev_month">{{ (date('d') != 1) ? isset($report['gros_rev']['dates'][date('Y-m')]) ? numberConverter(($report['gros_rev']['dates'][date('Y-m')]['value']/(date('d') - 1) * date('t')),2,'pre') : 'N/A' : 'N/A' }}</td>

                @if(isset($report['gros_rev']['dates']) && !empty($report['gros_rev']['dates']))
                @foreach ($report['gros_rev']['dates'] as $gros_rev)
                <td class="local_share_data">{{numberConverter( $gros_rev['value'] ,2,'hosting_cost') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-young-yellow o_tax">
                <td class="font-weight-bold">
                  <div class="text-with-sup">
                    <div class="btn-ico-expand other_tax" data-sign="plus" style="cursor: pointer;">+</div>
                    <div class="text-left"><strong class="text-with-sup">Other Tax<sup><i class="ml-3 text-dark fa fa-info-circle" title="Other Tax = VAT + WHT + MISC TAX"></i></sup></strong></div>
                  </div>
                </td>
                <td class="other_tax_total">{{numberConverter( $report['other_tax']['total'],2,'pre') }}</td>
                <td class="other_tax_avg">{{numberConverter( $report['other_tax']['avg'],2,'pre') }}</td>
                <td class="other_tax_month">{{numberConverter( $report['other_tax']['t_mo_end'],2,'pre') }}</td>
                <td class="other_tax_month">{{ (date('d') != 1) ? isset($report['other_tax']['dates'][date('Y-m')]) ? numberConverter(($report['other_tax']['dates'][date('Y-m')]['value']/(date('d') - 1) * date('t')),2,'pre') : 'N/A' : 'N/A' }}</td>

                @if(isset($report['other_tax']['dates'] ) && !empty($report['other_tax']['dates'] ))
                @foreach ($report['other_tax']['dates'] as $other_tax)
                <td class="other_tax_data">{{numberConverter( $other_tax['value'],2,'pre') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-young-yellow hiddenOtherTaxTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="font-weight: bolder; position: relative; left: 30px;">VAT</div>
                </td>
                <td class="hosting_cost_total">{{numberConverter( $report['vat']['total'],2,'pre') }}</td>
                <td class="hosting_cost_avg">{{numberConverter( $report['vat']['avg'],2,'pre') }}</td>
                <td class="hosting_cost_month">{{numberConverter( $report['vat']['t_mo_end'],2,'pre') }}</td>
                <td class="vat_month">{{ (date('d') != 1) ? isset($report['vat']['dates'][date('Y-m')]) ? numberConverter(($report['vat']['dates'][date('Y-m')]['value']/(date('d') - 1) * date('t')),2,'pre') : 'N/A' : 'N/A' }}</td>

                @if(isset($report['vat']['dates'] ) && !empty($report['vat']['dates'] ))
                @foreach ($report['vat']['dates'] as $vat)
                <td class="hosting_cost_data">{{numberConverter( $vat['value'],2,'pre') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-young-yellow hiddenOtherTaxTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="font-weight: bolder; position: relative; left: 30px;">WHT</div>
                </td>
                <td class="content_total">{{numberConverter( $report['wht']['total'],2,'pre') }}</td>
                <td class="content_avg">{{numberConverter( $report['wht']['avg'],2,'pre') }}</td>
                <td class="content_month">{{numberConverter( $report['wht']['t_mo_end'],2,'pre') }}</td>
                <td class="wht_month">{{ (date('d') != 1) ? isset($report['wht']['dates'][date('Y-m')]) ? numberConverter(($report['wht']['dates'][date('Y-m')]['value']/(date('d') - 1) * date('t')),2,'pre') : 'N/A' : 'N/A' }}</td>

                @if(isset($report['wht']['dates'] ) && !empty($report['wht']['dates'] ))
                @foreach ($report['wht']['dates'] as $wht)
                <td class="content_data">{{numberConverter( $wht['value'],2,'pre') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-young-yellow hiddenOtherTaxTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="font-weight: bolder; position: relative; left: 30px;">MISC TAX</div>
                </td>
                <td class="md_total">{{numberConverter( $report['misc_tax']['total'],2,'pre') }}</td>
                <td class="md_avg">{{numberConverter( $report['misc_tax']['avg'],2,'pre') }}</td>
                <td class="md_month">{{numberConverter( $report['misc_tax']['t_mo_end'],2,'pre') }}</td>
                <td class="misc_tax_month">{{ (date('d') != 1) ? isset($report['misc_tax']['dates'][date('Y-m')]) ? numberConverter(($report['misc_tax']['dates'][date('Y-m')]['value']/(date('d') - 1) * date('t')),2,'pre') : 'N/A' : 'N/A' }}</td>

                @if(isset($report['misc_tax']['dates'] ) && !empty($report['misc_tax']['dates'] ))
                @foreach ($report['misc_tax']['dates'] as $misc_tax)
                <td class="md_data">{{numberConverter( $misc_tax['value'],2,'pre') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-young-yellow">
                <td class="font-weight-bold">
                  <strong class="text-with-sup">Net Revenue (USD)<sup><i class="ml-3 text-dark fa fa-info-circle" title="gross revenue USD - other tax"></i></sup></strong>
                </td>
                <td class="local_share_total">{{numberConverter( $report['net_after_tax']['total'] ,2,'hosting_cost') }}</td>
                <td class="local_share_month">{{numberConverter( $report['net_after_tax']['avg'] ,2,'hosting_cost') }}</td>
                <td class="local_share_avg">{{numberConverter( $report['net_after_tax']['t_mo_end'] ,2,'hosting_cost') }}</td>
                <td class="local_share_month">{{ (date('d') != 1) ? isset($report['net_after_tax']['dates'][date('Y-m')]) ? numberConverter(($report['net_after_tax']['dates'][date('Y-m')]['value']/(date('d') - 1) * date('t')),2,'pre') : 'N/A' : 'N/A' }}</td>

                @if(isset($report['net_after_tax']['dates']) && !empty($report['net_after_tax']['dates']))
                @foreach ($report['net_after_tax']['dates'] as $net_after_tax)
                <td class="local_share_data">{{numberConverter( $net_after_tax['value'] ,2,'hosting_cost') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-old-blue o_cost">
                <td class="font-weight-bold">
                  <div class="text-with-sup">
                    <div class="btn-ico-expand other_cost" data-sign="plus" style="cursor: pointer;">+</div>
                    <div class="text-left"><strong class="text-with-sup">Other Cost<sup><i class="ml-3 text-dark fa fa-info-circle" title="Other cost = Hosting + Content + rnd + md + platform"></i></sup></strong></div>
                  </div>
                </td>
                <td class="other_cost_total">{{numberConverter( $report['other_cost']['total'] ,2,'hosting_cost') }}</td>
                <td class="other_cost_month">{{numberConverter( $report['other_cost']['avg'] ,2,'hosting_cost') }}</td>
                <td class="other_cost_avg">{{numberConverter( $report['other_cost']['t_mo_end'] ,2,'hosting_cost') }}</td>
                <td class="other_cost_month">{{ (date('d') != 1) ? isset($report['other_cost']['dates'][date('Y-m')]) ? numberConverter(($report['other_cost']['dates'][date('Y-m')]['value']/(date('d') - 1) * date('t')),2,'pre') : 'N/A' : 'N/A' }}</td>

                @if(isset($report['other_cost']['dates']) && !empty($report['other_cost']['dates']))
                @foreach ($report['other_cost']['dates'] as $other_cost)
                <td class="other_cost_data">{{numberConverter( $other_cost['value'] ,2,'hosting_cost') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-old-blue hiddenOtherCostTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="font-weight: bolder; position: relative; left: 30px;">Hosting Cost</div>
                </td>
                <td class="hosting_cost_total">{{numberConverter( $report['hosting_cost']['total'] ,2,'hosting_cost') }}</td>
                <td class="hosting_cost_month">{{numberConverter( $report['hosting_cost']['avg'] ,2,'hosting_cost') }}</td>
                <td class="hosting_cost_avg">{{numberConverter( $report['hosting_cost']['t_mo_end'] ,2,'hosting_cost') }}</td>
                <td class="hosting_cost_month">{{ (date('d') != 1) ? isset($report['hosting_cost']['dates'][date('Y-m')]) ? numberConverter(($report['hosting_cost']['dates'][date('Y-m')]['value']/(date('d') - 1) * date('t')),2,'pre') : 'N/A' : 'N/A' }}</td>

                @if(isset($report['hosting_cost']['dates']) && !empty($report['hosting_cost']['dates']))
                @foreach ($report['hosting_cost']['dates'] as $hosting_cost)
                <td class="hosting_cost_data">{{numberConverter( $hosting_cost['value'] ,2,'hosting_cost') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-old-blue hiddenOtherCostTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="font-weight: bolder; position: relative; left: 30px;">Content 2%</div>
                </td>
                <td class="content_total">{{numberConverter( $report['content']['total'] ,2,'hosting_cost') }}</td>
                <td class="content_avg">{{numberConverter( $report['content']['avg'] ,2,'hosting_cost') }}</td>
                <td class="content_month">{{numberConverter( $report['content']['t_mo_end'] ,2,'hosting_cost') }}</td>
                <td class="content_month">{{ (date('d') != 1) ? isset($report['content']['dates'][date('Y-m')]) ? numberConverter(($report['content']['dates'][date('Y-m')]['value']/(date('d') - 1) * date('t')),2,'pre') : 'N/A' : 'N/A' }}</td>

                @if(isset($report['content']['dates']) && !empty($report['content']['dates']))
                @foreach ($report['content']['dates'] as $content)
                <td class="content_data">{{numberConverter( $content['value'] ,2,'hosting_cost') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-old-blue hiddenOtherCostTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="font-weight: bolder; position: relative; left: 30px;">RND 5%</div>
                </td>
                <td class="md_total">{{numberConverter( $report['rnd']['total'] ,2,'hosting_cost') }}</td>
                <td class="md_avg">{{numberConverter( $report['rnd']['avg'] ,2,'hosting_cost') }}</td>
                <td class="md_month">{{numberConverter( $report['rnd']['t_mo_end'] ,2,'hosting_cost') }}</td>
                <td class="rnd_month">{{ (date('d') != 1) ? isset($report['rnd']['dates'][date('Y-m')]) ? numberConverter(($report['rnd']['dates'][date('Y-m')]['value']/(date('d') - 1) * date('t')),2,'pre') : 'N/A' : 'N/A' }}</td>

                @if(isset($report['rnd']['dates']) && !empty($report['rnd']['dates']))
                @foreach ($report['rnd']['dates'] as $rnd)
                <td class="md_data">{{numberConverter( $rnd['value'] ,2,'hosting_cost') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-old-blue hiddenOtherCostTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="font-weight: bolder; position: relative; left: 30px;">BD 2.5%</div>
                </td>
                <td class="bd_total">{{numberConverter( $report['bd']['total'] ,2,'hosting_cost') }}</td>
                <td class="bd_avg">{{numberConverter( $report['bd']['avg'] ,2,'hosting_cost') }}</td>
                <td class="bd_month">{{numberConverter( $report['bd']['t_mo_end'] ,2,'hosting_cost') }}</td>
                <td class="bd_month">{{ (date('d') != 1) ? isset($report['bd']['dates'][date('Y-m')]) ? numberConverter(($report['bd']['dates'][date('Y-m')]['value']/(date('d') - 1) * date('t')),2,'pre') : 'N/A' : 'N/A' }}</td>

                @if(isset($report['bd']['dates']) && !empty($report['bd']['dates']))
                @foreach ($report['bd']['dates'] as $bd)
                <td class="bd_data">{{numberConverter( $bd['value'] ,2,'hosting_cost') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-old-blue hiddenOtherCostTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="font-weight: bolder; position: relative; left: 30px;">Marketing Cost 1.5%</div>
                </td>
                <td class="market_cost_total">{{numberConverter( $report['market_cost']['total'] ,2,'hosting_cost') }}</td>
                <td class="market_cost_avg">{{numberConverter( $report['market_cost']['avg'] ,2,'hosting_cost') }}</td>
                <td class="market_cost_month">{{numberConverter( $report['market_cost']['t_mo_end'] ,2,'hosting_cost') }}</td>
                <td class="market_cost_month">{{ (date('d') != 1) ? isset($report['market_cost']['dates'][date('Y-m')]) ? numberConverter(($report['market_cost']['dates'][date('Y-m')]['value']/(date('d') - 1) * date('t')),2,'pre') : 'N/A' : 'N/A' }}</td>

                @if(isset($report['market_cost']['dates']) && !empty($report['market_cost']['dates']))
                @foreach ($report['market_cost']['dates'] as $marketCost)
                <td class="market_cost_data">{{numberConverter( $marketCost['value'] ,2,'hosting_cost') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-old-blue hiddenOtherCostTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="font-weight: bolder; position: relative; left: 30px;">Misc Cost</div>
                </td>
                <td class="platform_total">{{numberConverter( $report['misc_cost']['total'] ,2,'hosting_cost') }}</td>
                <td class="platform_avg">{{numberConverter( $report['misc_cost']['avg'] ,2,'hosting_cost') }}</td>
                <td class="platform_month">{{numberConverter( $report['misc_cost']['t_mo_end'] ,2,'hosting_cost') }}</td>
                <td class="misc_cost_month">{{ (date('d') != 1) ? isset($report['misc_cost']['dates'][date('Y-m')]) ? numberConverter(($report['misc_cost']['dates'][date('Y-m')]['value']/(date('d') - 1) * date('t')),2,'pre') : 'N/A' : 'N/A' }}</td>

                @if(isset($report['misc_cost']['dates']) && !empty($report['misc_cost']['dates']))
                @foreach ($report['misc_cost']['dates'] as $misc_cost)
                <td class="platform_data">{{numberConverter( $misc_cost['value'] ,2,'hosting_cost') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-old-blue cost_campaign">
                <td class="font-weight-bold">
                  <div class="text-with-sup">
                    <div class="text-left"><strong>Cost Campaign (USD)</strong></div>
                  </div>
                </td>
                <td class="cost_total cost">{{numberConverter( $report['cost_campaign']['total'] ,3,'hosting_cost') }}</td>
                <td class="cost_month">{{numberConverter( $report['cost_campaign']['avg'] ,3,'hosting_cost') }}</td>
                <td class="cost_avg">{{numberConverter( $report['cost_campaign']['t_mo_end'] ,3,'hosting_cost') }}</td>
                <td class="cost_month">{{ (date('d') != 1) ? isset($report['cost_campaign']['dates'][date('Y-m')]) ? numberConverter(($report['cost_campaign']['dates'][date('Y-m')]['value']/(date('d') - 1) * date('t')),2,'pre') : 'N/A' : 'N/A' }}</td>

                @if(isset($report['cost_campaign']['dates']) && !empty($report['cost_campaign']['dates']))
                @foreach ($report['cost_campaign']['dates'] as $cost_campaign)
                <td class="cost_data">{{numberConverter( $cost_campaign['value'] ,3,'hosting_cost') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-young-green pnl">
                <td class="font-weight-bold"><strong class="text-with-sup">GP<sup><i class="ml-3 text-dark fa fa-info-circle" title="GP = Net Revenue - (Cost Campaign + other cost)"></i></sup></strong>
                </td>
                <td class="pnl_total p">{{numberConverter( $report['pnl']['total'] ,2,'hosting_cost') }}</td>
                <td class="pnl_month">{{numberConverter( $report['pnl']['avg'] ,2,'hosting_cost') }}</td>
                <td class="pnl_avg">{{numberConverter( $report['pnl']['t_mo_end'] ,2,'hosting_cost') }}</td>
                <td class="pnl_month">{{ (date('d') != 1) ? isset($report['pnl']['dates'][date('Y-m')]) ? numberConverter(($report['pnl']['dates'][date('Y-m')]['value']/(date('d') - 1) * date('t')),2,'pre') : 'N/A' : 'N/A' }}</td>

                @if(isset($report['pnl']['dates']) && !empty($report['pnl']['dates']))
                @foreach ($report['pnl']['dates'] as $pnl)
                <td class="pnl_data">{{numberConverter( $pnl['value'] ,2,'hosting_cost') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-old-yellow">
                <td class="font-weight-bold">
                  <strong>Campaign MO</strong>
                </td>
                <td class="reg_total">{{numberConverter( $report['mo']['total'] ,2,'hosting_cost') }}</td>
                <td class="reg_month">{{numberConverter( $report['mo']['avg'] ,2,'hosting_cost') }}</td>
                <td class="reg_avg">{{numberConverter( $report['mo']['t_mo_end'] ,2,'hosting_cost') }}</td>
                <td class="reg_month">{{ (date('d') != 1) ? isset($report['mo']['dates'][date('Y-m')]) ? numberConverter(($report['mo']['dates'][date('Y-m')]['value']/(date('d') - 1) * date('t')),2,'pre') : 'N/A' : 'N/A' }}</td>

                @if(isset($report['mo']['dates']) && !empty($report['mo']['dates']))
                @foreach ($report['mo']['dates'] as $mo)
                <td class="reg_data">{{numberConverter( $mo['value'] ,2,'hosting_cost') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-old-yellow price_mo">
                <td class="font-weight-bold"><strong class="text-with-sup">Price/Mo<sup><i class="ml-3 text-dark fa fa-info-circle" title="Price/Mo = cost campaign / mo"></i></sup></strong></td>
                <td class="price_mo_total">N/A</td>
                <td class="price_mo_avg">{{numberConverter( $report['price_mo']['avg'] ,2,'hosting_cost') }}</td>
                <td class="price_mo_month">N/A</td>
                <td class="price_mo_month">N/A</td>

                @if(isset($report['price_mo']['dates']) && !empty($report['price_mo']['dates']))
                @foreach ($report['price_mo']['dates'] as $price_mo)
                <td class="price_mo_data">{{numberConverter( $price_mo['value'] ,2,'hosting_cost') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-old-yellow active_subscriber">
                <td class="font-weight-bold"><strong>Active Subscriber</strong></td>
                <td class="subs_total">{{numberConverter( $report['active_subs']['total'] ,2,'hosting_cost') }}</td>
                <td class="subs_month">N/A</td>
                <td class="subs_avg">N/A</td>
                <td class="price_mo_month">N/A</td>

                @if(isset($report['active_subs']['dates']) && !empty($report['active_subs']['dates']))
                @foreach ($report['active_subs']['dates'] as $active_subs)
                <td class="subs_data">{{numberConverter( $active_subs['value'] ,2,'hosting_cost') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-old-yellow reg">
                <td class="font-weight-bold"><strong>Reg</strong></td>
                <td class="renewal_total">{{numberConverter( $report['reg']['total'] ,2,'hosting_cost') }}</td>
                <td class="renewal_month">{{numberConverter( $report['reg']['avg'] ,2,'hosting_cost') }}</td>
                <td class="renewal_avg">{{numberConverter( $report['reg']['t_mo_end'] ,2,'hosting_cost') }}</td>
                <td class="renewal_month">{{ (date('d') != 1) ? isset($report['reg']['dates'][date('Y-m')]) ? numberConverter(($report['reg']['dates'][date('Y-m')]['value']/(date('d') - 1) * date('t')),2,'pre') : 'N/A' : 'N/A' }}</td>

                @if(isset($report['reg']['dates']) && !empty($report['reg']['dates']))
                @foreach ($report['reg']['dates'] as $reg)
                <td class="renewal_data">{{numberConverter( $reg['value'] ,2,'hosting_cost') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-old-yellow unreg">
                <td class="font-weight-bold"><strong>Unreg</strong></td>
                <td class="renewal_total">{{numberConverter( $report['unreg']['total'] ,2,'hosting_cost') }}</td>
                <td class="renewal_month">{{numberConverter( $report['unreg']['avg'] ,2,'hosting_cost') }}</td>
                <td class="renewal_avg">{{numberConverter( $report['unreg']['t_mo_end'] ,2,'hosting_cost') }}</td>
                <td class="renewal_month">{{ (date('d') != 1) ? isset($report['unreg']['dates'][date('Y-m')]) ? numberConverter(($report['unreg']['dates'][date('Y-m')]['value']/(date('d') - 1) * date('t')),2,'pre') : 'N/A' : 'N/A' }}</td>

                @if(isset($report['unreg']['dates']) && !empty($report['unreg']['dates']))
                @foreach ($report['unreg']['dates'] as $unreg)
                <td class="renewal_data">{{numberConverter( $unreg['value'] ,2,'hosting_cost') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-old-yellow renewal">
                <td class="font-weight-bold"><strong>Renewal</strong></td>
                <td class="renewal_total">{{numberConverter( $report['renewal']['total'] ,2,'hosting_cost') }}</td>
                <td class="renewal_month">{{numberConverter( $report['renewal']['avg'] ,2,'hosting_cost') }}</td>
                <td class="renewal_avg">{{numberConverter( $report['renewal']['t_mo_end'] ,2,'hosting_cost') }}</td>
                <td class="renewal_month">{{ (date('d') != 1) ? isset($report['renewal']['dates'][date('Y-m')]) ? numberConverter(($report['renewal']['dates'][date('Y-m')]['value']/(date('d') - 1) * date('t')),2,'pre') : 'N/A' : 'N/A' }}</td>

                @if(isset($report['renewal']['dates']) && !empty($report['renewal']['dates']))
                @foreach ($report['renewal']['dates'] as $renewal)
                <td class="renewal_data">{{numberConverter( $renewal['value'] ,2,'hosting_cost') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-old-yellow arpu_30">
                <td class="font-weight-bold"><strong>30 ARPU</strong></td>
                <td class="arpu_30_total">N/A</td>
                <td class="arpu_30_month">{{numberConverter( $report['arpu_30_usd']['avg'] ,4,'hosting_cost') }}</td>
                <td class="arpu_30_avg">N/A</td>
                <td class="cost_month">N/A</td>

                @if(isset($report['arpu_30_usd']['dates']) && !empty($report['arpu_30_usd']['dates']))
                @foreach ($report['arpu_30_usd']['dates'] as $arpu_30_usd)
                <td class="arpu_30_data">{{numberConverter( $arpu_30_usd['value'] ,4,'hosting_cost') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-old-yellow">
                <td class="font-weight-bold">
                  <strong class="text-with-sup" style="position: relative; left: -1px;">ROI<sup><i class="ml-3 text-dark fa fa-info-circle" title="ROI = Price/Mo / 30 ARPU"></i></sup></strong>
                </td>
                <td class="cost_total cost">N/A</td>
                <td class="cost_month">{{ numberConverter( $report['roi']['dates'][$date]['value'],4,'pre') }}</td>
                <td class="cost_avg">N/A</td>
                <td class="cost_month">N/A</td>

                @if(isset($report['roi']['dates']) && !empty($report['roi']['dates']))
                @foreach ($report['roi']['dates'] as $roi)
                <td class="cost_data">{{numberConverter( $roi['value'] ,4,'hosting_cost') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-old-yellow">
                <td class="font-weight-bold">
                  <strong class="text-with-sup">
                    <div class="billSeperateBtn pull-left" data-sign="plus" style="cursor: pointer;">+</div>
                    <div class="text-left">Bill % </div><sup><i class="ml-3 text-dark fa fa-info-circle" title="(total daily push delivered / total subscriber) * 100%"></i></sup>
                  </strong>
                </td>
                <td class="cost_total cost">N/A</td>
                <td class="cost_month">{{ numberConverter( $report['bill']['avg'],2,'pre') }}</td>
                <td class="cost_avg">N/A</td>
                <td class="cost_month">N/A</td>

                @if(isset($report['bill']['dates']) && !empty($report['bill']['dates']))
                @foreach ($report['bill']['dates'] as $bill)
                <td class="cost_data">{{numberConverter( $bill['value'] ,2,'hosting_cost') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-old-yellow billExtendedRows" style="display: none;">
                <td>
                  <strong class="text-with-sup"  style="font-weight: bolder; position: relative; left: 30px;">First P.%<sup><i class="ml-3 text-dark fa fa-info-circle" title="(delivered first push / first push sent) * 100%"></i></sup></strong>
                </td>
                <td class="cost_total cost">N/A</td>
                <td class="cost_month">{{ numberConverter( $report['firstpush']['avg'],2,'pre') }}</td>
                <td class="cost_avg">N/A</td>
                <td class="cost_month">N/A</td>

                @if(isset($report['firstpush']['dates']) && !empty($report['firstpush']['dates']))
                @foreach ($report['firstpush']['dates'] as $firstpush)
                <td class="cost_data">{{numberConverter( $firstpush['value'] ,2,'hosting_cost') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-old-yellow billExtendedRows" style="display: none;">
                <td>
                  <strong class="text-with-sup"  style="font-weight: bolder; position: relative; left: 30px;">Daily P.%<sup><i class="ml-3 text-dark fa fa-info-circle" title="(delivered daily push / daily push sent) * 100%"></i></sup></strong>
                </td>
                <td class="cost_total cost">N/A</td>
                <td class="cost_month">{{ numberConverter( $report['dailypush']['avg'],2,'pre') }}</td>
                <td class="cost_avg">N/A</td>
                <td class="cost_month">N/A</td>

                @if(isset($report['dailypush']['dates']) && !empty($report['dailypush']['dates']))
                @foreach ($report['dailypush']['dates'] as $dailypush)
                <td class="cost_data">{{numberConverter( $dailypush['value'] ,2,'hosting_cost') }}</td>
                @endforeach
                @endif
              </tr>
            </tbody>
            @endif
          </table>
        </div>
      </div>
    </div>
    @endforeach
    @endif
  </div>
</div>

@endsection
