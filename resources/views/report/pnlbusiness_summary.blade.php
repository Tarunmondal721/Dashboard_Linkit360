@extends('layouts.admin')

@section('title')
@if(!isset($sumOfSummaryData['net_after_tax']))
    {{ __('GP Summary') }}
@endif
@if (isset($sumOfSummaryData['net_after_tax']))
    {{ __('GP Details')}}
@endif
@endsection

@section('content')
<div class="page-content">
  <div class="page-title" style="margin-bottom:25px">
    <div class="row justify-content-between align-items-center">
      <div class="col-xl-4 col-lg-4 col-md-4 d-flex align-items-center justify-content-between justify-content-md-start mb-3 mb-md-0">
        @if(!isset($sumOfSummaryData['net_after_tax']))
        <div class="d-inline-block">
          <h5 class="h4 d-inline-block font-weight-400 mb-0"><b>GP Summary</b></h5><br>
          <p class="d-inline-block font-weight-200 mb-0">Summary of Business Type Data</p>
        </div>
        @endif
        @if(isset($sumOfSummaryData['net_after_tax']))
        <div class="d-inline-block">
          <h5 class="h4 d-inline-block font-weight-400 mb-0"><b>GP Details</b></h5><br>
          <p class="d-inline-block font-weight-200 mb-0">Details of Business Type Data</p>
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
        <span class="badge badge-with-flag badge-secondary px-2 bg-primary text-uppercase"> All Business{{isset($report['month_string'] ) ? $report['month_string'] : ''}} </span>
        <span class="flex-fill ml-2 bg-primary w-100 font-weight-bold" style="height: 1px;"></span>
        <div class="text-right pl-2">
          <button class="btn btn-sm pnl-xls" style="color:white; background-color:green" data-param="all"><i class="fa fa-file-excel-o"></i>Export as XLS</button>
        </div>
      </div>
      <div class="card" style="margin-bottom: 0px">
        <div class="table-responsive shadow-sm" id="all">
          <h1 style="display:none">PNL Country Daily Report Summary For All Operator, Nov 2022</h1>
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
                <td class="gross_revenue_usd_total">{{ numberConverter($end_user_rev_usd['total'],2,'pre') }}</td>
                <td class="gross_revenue_usd_avg">{{ numberConverter($end_user_rev_usd['avg'],2,'pre') }}</td>
                <td class="gross_revenue_usd_month">{{ numberConverter($end_user_rev_usd['t_mo_end'],2,'pre') }}</td>

                @if(isset($end_user_rev_usd['dates'] ) && !empty($end_user_rev_usd['dates'] ))
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

                @if(isset($gros_rev_usd['dates'] ) && !empty($gros_rev_usd['dates'] ))
                @foreach ($gros_rev_usd['dates'] as $gros_rev)
                <td class="share_data">{{numberConverter( $gros_rev['value'],2,'pre') }}</td>
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
                <td class="cost_total">{{ numberConverter($cost_campaign['total'],3,'pre') }}</td>
                <td class="cost_avg">{{ numberConverter($cost_campaign['avg'],3,'pre') }}</td>
                <td class="cost_month">{{ numberConverter($cost_campaign['t_mo_end'],3,'pre') }}</td>

                @if(isset($cost_campaign['dates'] ) && !empty($cost_campaign['dates'] ))
                @foreach ($cost_campaign['dates'] as $costcampaign)
                <td class="cost_data">{{ numberConverter($costcampaign['value'],3,'costcampaign',) }}</td>
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
                    <div class="btn-ico-expand OtherCostSepBtn" data-sign="plus" style="cursor: pointer;">+</div>
                    <div class="text-left"><strong class="text-with-sup">Other Cost<sup><i class="ml-3 text-dark fa fa-info-circle" title="Other cost = Hosting + Content + rnd + md + platform"></i></sup></strong></div>
                  </div>
                </td>
                <td class="other_cost_total">{{numberConverter( $other_cost['total'],2,'pre') }}</td>
                <td class="other_cost_avg">{{numberConverter( $other_cost['avg'],2,'pre') }}</td>
                <td class="other_cost_month">{{numberConverter( $other_cost['t_mo_end'],2,'pre') }}</td>

                @if(isset($other_cost['dates'] ) && !empty($other_cost['dates'] ))
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

                @if(isset($hosting_cost['dates'] ) && !empty($hosting_cost['dates'] ))
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

                @if(isset($content['dates'] ) && !empty($content['dates'] ))
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

                @if(isset($rnd['dates'] )&& !empty($rnd['dates']))
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
                  <div style="font-weight: bolder; position: relative; left: 30px;">BD 2.5%</div>
                </td>
                <td class="bd_total">{{numberConverter( $bd['total'],2,'pre') }}</td>
                <td class="bd_avg">{{numberConverter( $bd['avg'],2,'pre') }}</td>
                <td class="bd_month">{{numberConverter( $bd['t_mo_end'],2,'pre') }}</td>

                @if(isset($bd['dates'] ) && !empty($bd['dates'] ))
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
              <tr class="bg-young-yellow hiddenSoSOtherCostTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="font-weight: bolder; position: relative; left: 30px;">Marketing Cost 1.5%</div>
                </td>
                <td class="market_cost_total">{{numberConverter( $market_cost['total'],2,'pre') }}</td>
                <td class="market_cost_avg">{{numberConverter( $market_cost['avg'],2,'pre') }}</td>
                <td class="market_cost_month">{{numberConverter( $market_cost['t_mo_end'],2,'pre') }}</td>

                @if(isset($market_cost['dates'] ) && !empty($market_cost['dates'] ))
                @foreach ($market_cost['dates'] as $marketCostdata)
                <td class="market_cost_data">{{numberConverter( $marketCostdata['value'],2,'pre') }}</td>
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

                @if(isset($platform['dates'] ) && !empty($platform['dates'] ))
                @foreach ($platform['dates'] as $platformdata)
                <td class="platform_data">{{numberConverter( $platformdata['value'],2,'pre') }}</td>
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
                <td class="pnl_total">{{numberConverter( $pnl['total'],2,'pre') }}</td>
                <td class="pnl_avg">{{numberConverter( $pnl['avg'],2,'pre') }}</td>
                <td class="pnl_month">{{numberConverter( $pnl['t_mo_end'],2,'pre') }}</td>

                @if(isset($pnl['dates'] ) && !empty($pnl['dates'] ))
                @foreach ($pnl['dates'] as $pnldata)
                <td class="pnl_data">{{numberConverter( $pnldata['value'],2,'pre') }}</td>
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
                    <div class="btn-ico-expand OtherCostSepBtn" data-sign="plus" style="cursor: pointer;">+</div>
                    <div class="text-left"><strong class="text-with-sup">Other Cost<sup><i class="ml-3 text-dark fa fa-info-circle" title="Other cost = Hosting + Content + rnd + md + platform"></i></sup></strong></div>
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
              <tr class="bg-old-blue hiddenSoSOtherCostTr" style="display: none;">
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
              <tr class="bg-old-blue hiddenSoSOtherCostTr" style="display: none;">
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
              <tr class="bg-old-blue hiddenSoSOtherCostTr" style="display: none;">
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
              <tr class="bg-old-blue hiddenSoSOtherCostTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="font-weight: bolder; position: relative; left: 30px;">BD 2.5%</div>
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
              @foreach ($sumOfSummaryData as $key =>$market_cost)
              @if($key == 'market_cost')
              <tr class="bg-old-blue hiddenSoSOtherCostTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="font-weight: bolder; position: relative; left: 30px;">Marketing Cost 1.5%</div>
                </td>
                <td class="market_cost_total">{{numberConverter( $market_cost['total'],2,'pre') }}</td>
                <td class="market_cost_avg">{{numberConverter( $market_cost['avg'],2,'pre') }}</td>
                <td class="market_cost_month">{{numberConverter( $market_cost['t_mo_end'],2,'pre') }}</td>

                @if(isset($market_cost['dates'] ) && !empty($market_cost['dates'] ))
                @foreach ($market_cost['dates'] as $marketCostdata)
                <td class="market_cost_data">{{numberConverter( $marketCostdata['value'],2,'pre') }}</td>
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
              @foreach ($sumOfSummaryData as $key =>$arpu_7_usd)
              @if($key == 'arpu_7_usd')
              <tr class="bg-old-yellow arpu_30_usd">
                <td class="font-weight-bold">
                  <div class="text-with-sup">
                    <div class="btn-ico-expand arpu_plus" data-sign="plus" style="cursor: pointer;">+</div>
                    <div class="text-left"><strong class="text-with-sup" style="position: relative; left: -1px;">7 ARPU</strong></div>
                  </div>
                </td>
                <td class="cost_total">N/A</td>
                <td class="cost_avg">{{ numberConverter( $arpu_7_usd['avg'],4,'pre') }}</td>
                <td class="cost_month">N/A</td>

                @if(isset($arpu_7_usd['dates']) && !empty($arpu_7_usd['dates']))
                @foreach ($arpu_7_usd['dates'] as $arpu)
                <td class="cost_data">{{numberConverter( $arpu['value'],4,'pre') }}</td>
                @endforeach
                @endif
              </tr>
              @endif
              @endforeach
              @endif

              @if(isset($sumOfSummaryData) && !empty($sumOfSummaryData))
              @foreach ($sumOfSummaryData as $key =>$arpu_30_usd)
              @if($key == 'arpu_30_usd')
              <tr class="bg-old-yellow hiddenArpuTr" style="display: none;">
                <td class="font-weight-bold">
                  <div class="text-with-sup">
                    <div class="text-left"><strong class="text-with-sup" style="font-weight: bolder; position: relative; left: 30px;">30 ARPU</strong></div>
                  </div>
                </td>
                <td class="cost_total">N/A</td>
                <td class="cost_avg">{{ numberConverter( $arpu_30_usd['avg'],4,'pre') }}</td>
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
    @if(isset($result) && !empty($result))
    @foreach ($result as $key =>  $report)
    <div class="ptable">
      <div class="d-flex align-items-center my-3">
        <span class="badge badge-with-flag badge-secondary px-2 bg-primary text-uppercase">
            {{ ucfirst($key) }} | Operator ({{ $report['operator_count'] ?? 0 }}) | Last Update: {{ $report['summary']['last_update'] ?? 'N/A' }}
        </span>
        <span class="flex-fill ml-2 bg-primary w-100 font-weight-bold" style="height: 1px;"></span>
        <div class="text-right pl-2">
            <button class="btn btn-sm pnl-xls" style="color:white; background-color:green" data-param="{{ucfirst($key)}}"><i class="fa fa-file-excel-o"></i>Export as XLS</button>

        </div>
      </div>
      <div class="card">
        <div class="table-responsive shadow-sm pnlDataTbl" id="{{ucfirst($key)}}">
          <h1 style="display:none"></h1>
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

            @if(!isset($report['summary']['net_after_tax']))
            <tbody>
              <tr class="bg-young-blue end_user_revenue">
                <td class="font-weight-bold">
                  <div class="text-with-sup">
                    <div class="btn-ico-expand grev_plus" data-sign="plus" style="cursor: pointer;">+</div>
                    <div class="text-left"><strong>GMV (USD)<sup><i class="ml-3 text-dark fa fa-info-circle" title="Revenue that we charge user excluding tax"></i></sup></strong></div>
                  </div>
                </td>
                <td class="gross_revenue_usd_total usd">{{isset($report['summary']['end_user_rev_usd']['total']) ? numberConverter($report['summary']['end_user_rev_usd']['total'],2,'pre') : 0}} </td>
                <td class="gross_revenue_usd_avg">{{ isset($report['summary']['end_user_rev_usd']['avg']) ? numberConverter($report['summary']['end_user_rev_usd']['avg'],2,'pre') : 0}}</td>
                <td class="gross_revenue_usd_month">{{isset($report['end_user_rev_usd']['t_mo_end']) ? numberConverter($report['end_user_rev_usd']['t_mo_end'],2,'pre') : 0}}</td>

                @if(isset($report['summary']['end_user_rev_usd']['dates'] ) && !empty($report['summary']['end_user_rev_usd']['dates'] ))
                @foreach ($report['summary']['end_user_rev_usd']['dates'] as $end_user_rev_usd)
                <td class="gross_revenue_usd_data {{$end_user_rev_usd['class']}}">{{numberConverter($end_user_rev_usd['value'],2,'pre')}}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-young-blue hiddenRevTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="font-weight: bolder; position: relative; left: 30px;">GMV <sup><i class="ml-3 text-dark fa fa-info-circle" title="Revenue that we charge user excluding tax"></i></sup></div>
                </td>
                <td class="gross_revenue_total">{{ isset($report['summary']['end_user_rev']['total']) ?numberConverter($report['summary']['end_user_rev']['total'],2,'pre') : 0 }}</td>
                <td class="gross_revenue_avg">{{ isset($report['summary']['end_user_rev']['avg']) ?numberConverter($report['summary']['end_user_rev']['avg'],2,'pre') : 0 }}</td>
                <td class="gross_revenue_month">{{isset($report['summary']['end_user_rev']['t_mo_end']) ? numberConverter($report['summary']['end_user_rev']['t_mo_end'],2,'pre') : 0}}</td>

                @if(isset($report['summary']['end_user_rev']['dates'] ) && !empty($report['summary']['end_user_rev']['dates'] ))
                @foreach ($report['summary']['end_user_rev']['dates'] as $end_user_rev)
                <td class="gross_revenue_data">{{ numberConverter($end_user_rev['value'],2,'pre') }}</td>
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
                <td class="share_total">{{ numberConverter($report['summary']['gros_rev_usd']['total'] ?? 0, 2, 'pre') }}</td>
                <td class="share_avg">{{ numberConverter($report['summary']['gros_rev_usd']['avg'] ?? 0, 2, 'pre') }}</td>
                <td class="share_month">{{ numberConverter($report['summary']['gros_rev_usd']['t_mo_end'] ?? 0, 2, 'pre') }}</td>


                @if(isset($report['summary']['gros_rev_usd']['dates'] ) && !empty($report['summary']['gros_rev_usd']['dates'] ))
                @foreach ($report['summary']['gros_rev_usd']['dates'] as $gros_rev_usd)
                <td class="share_data">{{numberConverter( $gros_rev_usd['value'],2,'pre') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-young-blue hiddenGrossRevUsdTr" style="display:none;">
                <td class="font-weight-bold">
                  <div style="font-weight: bolder; position: relative; left: 30px;">Gross Revenue</div>
                </td>
                <td class="local_share_total">{{ numberConverter($report['summary']['gros_rev']['total'] ??  0, 2, 'pre') }}</td>
                <td class="local_share_avg">{{ numberConverter($report['summary']['gros_rev']['avg'] ?? 0 ,2,'pre') }}</td>
                <td class="local_share_month">{{ numberConverter($report['summary']['gros_rev']['t_mo_end'] ?? 0, 2,'pre') }}</td>

                @if(isset($report['summary']['gros_rev']['dates'] ) && !empty($report['summary']['gros_rev']['dates'] ))
                @foreach ($report['summary']['gros_rev']['dates'] as $gros_rev)
                <td class="local_share_data">{{ numberConverter($gros_rev['value'],2,'pre') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-young-blue">
                <td class="font-weight-bold">
                  <strong class="text-with-sup">Net Revenue (USD)<sup><i class="ml-3 text-dark fa fa-info-circle" title="gross revenue USD - other tax"></i></sup></strong>
                </td>
                <td class="local_share_total">{{numberConverter( $report['summary']['net_rev']['total'] ?? 0, 2,'hosting_cost') }}</td>
                <td class="local_share_month">{{numberConverter( $report['summary']['net_rev']['avg'] ?? 0,2,'hosting_cost') }}</td>
                <td class="local_share_avg">{{numberConverter( $report['summary']['net_rev']['t_mo_end'] ?? 0, 2,'hosting_cost') }}</td>

                @if(isset($report['summary']['net_rev']['dates']) && !empty($report['summary']['net_rev']['dates']))
                @foreach ($report['summary']['net_rev']['dates'] as $net_rev)
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
                <td class="cost_total cost">{{ numberConverter($report['summary']['cost_campaign']['total'] ?? 0, 3,'pre') }}</td>
                <td class="cost_avg">{{ numberConverter($report['summary']['cost_campaign']['avg'] ?? 0,3,'pre') }}</td>
                <td class="cost_month">{{ numberConverter($report['summary']['cost_campaign']['t_mo_end'] ?? 0, 3,'pre') }}</td>

                @if(isset($report['summary']['cost_campaign']['dates'] ) && !empty($report['summary']['cost_campaign']['dates'] ))
                @foreach ($report['summary']['cost_campaign']['dates'] as $cost_campaign)
                <td class="cost_data">{{ numberConverter($cost_campaign['value'],3,'pre') }}</td>
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
                <td class="other_cost_total">{{numberConverter( $report['summary']['other_cost']['total'] ?? 0, 2,'pre') }}</td>
                <td class="other_cost_avg">{{numberConverter( $report['summary']['other_cost']['avg'] ?? 0, 2,'pre') }}</td>
                <td class="other_cost_month">{{numberConverter( $report['summary']['other_cost']['t_mo_end'] ?? 0, 2,'pre') }}</td>

                @if(isset($report['summary']['other_cost']['dates'] ) && !empty($report['summary']['other_cost']['dates'] ))
                @foreach ($report['summary']['other_cost']['dates'] as $other_cost)
                <td class="other_cost_data">{{numberConverter( $other_cost['value'],2,'pre') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-young-yellow hiddenOtherCostTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="font-weight: bolder; position: relative; left: 30px;">Hosting Cost {{$report['summary']['hostingCost'] ?? 0}}%</div>
                </td>
                <td class="hosting_cost_total">{{numberConverter( $report['summary']['hosting_cost']['total'] ?? 0, 2,'pre') }}</td>
                <td class="hosting_cost_avg">{{numberConverter( $report['summary']['hosting_cost']['avg'] ?? 0, 2,'pre') }}</td>
                <td class="hosting_cost_month">{{numberConverter( $report['summary']['hosting_cost']['t_mo_end'] ?? 0, 2,'pre') }}</td>

                @if(isset($report['summary']['hosting_cost']['dates'] ) && !empty($report['summary']['hosting_cost']['dates'] ))
                @foreach ($report['summary']['hosting_cost']['dates'] as $hosting_cost)
                <td class="hosting_cost_data">{{numberConverter( $hosting_cost['value'],2,'pre') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-young-yellow hiddenOtherCostTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="font-weight: bolder; position: relative; left: 30px;">Content {{$report['summary']['contentCost']}}%</div>
                </td>
                <td class="content_total">{{numberConverter( $report['summary']['content']['total'] ?? 0, 2,'pre') }}</td>
                <td class="content_avg">{{numberConverter( $report['summary']['content']['avg'] ?? 0, 2,'pre') }}</td>
                <td class="content_month">{{numberConverter( $report['summary']['content']['t_mo_end'] ?? 0, 2,'pre') }}</td>

                @if(isset($report['summary']['content']['dates'] ) && !empty($report['summary']['content']['dates'] ))
                @foreach ($report['summary']['content']['dates'] as $content)
                <td class="content_data">{{numberConverter( $content['value'],2,'pre') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-young-yellow hiddenOtherCostTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="font-weight: bolder; position: relative; left: 30px;">RND {{$report['rndCost']}}%</div>
                </td>
                <td class="md_total">{{numberConverter( $report['summary']['rnd']['total'] ?? 0, 2,'pre') }}</td>
                <td class="md_avg">{{numberConverter( $report['summary']['rnd']['avg'] ?? 0, 2,'pre') }}</td>
                <td class="md_month">{{numberConverter( $report['summary']['rnd']['t_mo_end'] ?? 0, 2,'pre') }}</td>

                @if(isset($report['summary']['rnd']['dates'] ) && !empty($report['summary']['rnd']['dates'] ))
                @foreach ($report['summary']['rnd']['dates'] as $rnd)
                <td class="md_data">{{numberConverter( $rnd['value'],2,'pre') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-young-yellow hiddenOtherCostTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="font-weight: bolder; position: relative; left: 30px;">BD {{$report['summary']['bdCost']}}%</div>
                </td>
                <td class="bd_total">{{numberConverter( $report['summary']['bd']['total'] ?? 0, 2,'pre') }}</td>
                <td class="bd_avg">{{numberConverter( $report['summary']['bd']['avg'] ?? 0, 2,'pre') }}</td>
                <td class="bd_month">{{numberConverter( $report['summary']['bd']['t_mo_end'] ?? 0, 2,'pre') }}</td>

                @if(isset($report['summary']['bd']['dates'] ) && !empty($report['summary']['bd']['dates'] ))
                @foreach ($report['summary']['bd']['dates'] as $bd)
                <td class="bd_data">{{numberConverter( $bd['value'],2,'pre') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-young-yellow hiddenOtherCostTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="font-weight: bolder; position: relative; left: 30px;">Marketing Cost {{$report['summary']['marketCost']}}%</div>
                </td>
                <td class="market_cost_total">{{numberConverter( $report['summary']['market_cost']['total'] ?? 0, 2,'pre') }}</td>
                <td class="market_cost_avg">{{numberConverter( $report['summary']['market_cost']['avg'] ?? 0, 2,'pre') }}</td>
                <td class="market_cost_month">{{numberConverter( $report['summary']['market_cost']['t_mo_end'] ?? 0, 2,'pre') }}</td>

                @if(isset($report['summary']['market_cost']['dates'] ) && !empty($report['summary']['market_cost']['dates'] ))
                @foreach ($report['summary']['market_cost']['dates'] as $market_cost)
                <td class="market_cost_data">{{numberConverter( $market_cost['value'],2,'pre') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-young-yellow hiddenOtherCostTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="font-weight: bolder; position: relative; left: 30px;">Vostok Platform Cost 10%</div>
                </td>
                <td class="platform_total">{{numberConverter( $report['summary']['platform']['total'] ?? 0, 2,'pre') }}</td>
                <td class="platform_avg">{{numberConverter( $report['summary']['platform']['avg'] ?? 0, 2,'pre') }}</td>
                <td class="platform_month">{{numberConverter( $report['summary']['platform']['t_mo_end'] ?? 0, 2,'pre') }}</td>

                @if(isset($report['summary']['platform']['dates'] ) && !empty($report['summary']['platform']['dates'] ))
                @foreach ($report['summary']['platform']['dates'] as $platform)
                <td class="platform_data">{{numberConverter( $platform['value'],2,'pre') }}</td>
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
                <td class="other_tax_total">{{numberConverter( $report['summary']['other_tax']['total'] ?? 0, 2,'pre') }}</td>
                <td class="other_tax_avg">{{numberConverter( $report['summary']['other_tax']['avg'] ?? 0, 2,'pre') }}</td>
                <td class="other_tax_month">{{numberConverter( $report['summary']['other_tax']['t_mo_end'] ?? 0, 2,'pre') }}</td>

                @if(isset($report['summary']['other_tax']['dates'] ) && !empty($report['summary']['other_tax']['dates'] ))
                @foreach ($report['summary']['other_tax']['dates'] as $other_tax)
                <td class="other_tax_data">{{numberConverter( $other_tax['value'],2,'pre') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-young-yellow hiddenOtherTaxTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="font-weight: bolder; position: relative; left: 30px;">VAT {{$report['vatTax']}}%</div>
                </td>
                <td class="hosting_cost_total">{{numberConverter( $report['summary']['vat']['total'] ?? 0, 2,'pre') }}</td>
                <td class="hosting_cost_avg">{{numberConverter( $report['summary']['vat']['avg'] ?? 0, 2,'pre') }}</td>
                <td class="hosting_cost_month">{{numberConverter( $report['summary']['vat']['t_mo_end'] ?? 0, 2,'pre') }}</td>

                @if(isset($report['summary']['vat']['dates'] ) && !empty($report['summary']['vat']['dates'] ))
                @foreach ($report['summary']['vat']['dates'] as $vat)
                <td class="hosting_cost_data">{{numberConverter( $vat['value'],2,'pre') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-young-yellow hiddenOtherTaxTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="font-weight: bolder; position: relative; left: 30px;">WHT {{$report['summary']['whtTax']}}%</div>
                </td>
                <td class="content_total">{{numberConverter( $report['summary']['wht']['total'] ?? 0, 2,'pre') }}</td>
                <td class="content_avg">{{numberConverter( $report['summary']['wht']['avg'] ?? 0, 2,'pre') }}</td>
                <td class="content_month">{{numberConverter( $report['summary']['wht']['t_mo_end'] ?? 0, 2,'pre') }}</td>

                @if(isset($report['summary']['wht']['dates'] ) && !empty($report['summary']['wht']['dates'] ))
                @foreach ($report['summary']['wht']['dates'] as $wht)
                <td class="content_data">{{numberConverter( $wht['value'],2,'pre') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-young-yellow hiddenOtherTaxTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="font-weight: bolder; position: relative; left: 30px;">MISC TAX {{$report['summary']['miscTax']}}%</div>
                </td>
                <td class="md_total">{{numberConverter( $report['summary']['misc_tax']['total'] ?? 0, 2,'pre') }}</td>
                <td class="md_avg">{{numberConverter( $report['summary']['misc_tax']['avg'] ?? 0, 2,'pre') }}</td>
                <td class="md_month">{{numberConverter( $report['summary']['misc_tax']['t_mo_end'] ?? 0, 2,'pre') }}</td>

                @if(isset($report['summary']['misc_tax']['dates'] ) && !empty($report['summary']['misc_tax']['dates'] ))
                @foreach ($report['summary']['misc_tax']['dates'] as $misc_tax)
                <td class="md_data">{{numberConverter( $misc_tax['value'],2,'pre') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-young-green pnl">
                <td class="font-weight-bold"><strong class="text-with-sup">GP<sup><i class="ml-3 text-dark fa fa-info-circle" title="GP = Net Revenue - (Cost Campaign + other cost)"></i></sup></strong>
                </td>
                <td class="pnl_total p">{{numberConverter( $report['summary']['pnl']['total'] ?? 0, 2,'pre') }}</td>
                <td class="pnl_avg">{{numberConverter( $report['summary']['pnl']['avg'] ?? 0, 2,'pre') }}</td>
                <td class="pnl_month">{{numberConverter( $report['summary']['pnl']['t_mo_end'] ?? 0, 2,'pre') }}</td>

                @if(isset($report['summary']['pnl']['dates'] ) && !empty($report['summary']['pnl']['dates'] ))
                @foreach ($report['pnl']['dates'] as $pnl)
                <td class="pnl_data">{{numberConverter( $pnl['value'],2,'pre') }}</td>
                @endforeach
                @endif
              </tr>
            </tbody>
            @endif
            @if(isset($report['summary']['net_after_tax']))
            <tbody>
              <tr class="bg-young-blue end_user_revenue">
                <td class="font-weight-bold">
                  <div class="text-left"><strong>GMV (USD)<sup><i class="ml-3 text-dark fa fa-info-circle" title="Revenue that we charge user excluding tax"></i></sup></strong></div>
                </td>
                <td class="gross_revenue_usd_total usd">{{numberConverter($report['summary']['end_user_rev_usd']['total']  ?? 0, 2,'hosting_cost') }} </td>
                <td class="gross_revenue_usd_month">{{numberConverter($report['summary']['end_user_rev_usd']['avg']  ?? 0, 2,'hosting_cost') }}</td>
                <td class="gross_revenue_usd_avg">{{numberConverter($report['summary']['end_user_rev_usd']['t_mo_end']  ?? 0, 2,'hosting_cost') }}</td>

                @if(isset($report['summary']['end_user_rev_usd']['dates']) && !empty($report['summary']['end_user_rev_usd']['dates']))
                @foreach ($report['summary']['end_user_rev_usd']['dates'] as $end_user_rev_usd)
                <td class="gross_revenue_usd_data {{$end_user_rev_usd['class']}}">{{numberConverter($end_user_rev_usd['value'] ,2,'hosting_cost') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-young-blue">
                <td class="font-weight-bold">
                  <div>GMV <sup><i class="ml-3 text-dark fa fa-info-circle" title="Revenue that we charge user excluding tax"></i></sup></div>
                </td>
                <td class="gross_revenue_total">{{numberConverter( $report['summary']['end_user_rev']['total'] ,2,'hosting_cost') }}</td>
                <td class="gross_revenue_month">{{numberConverter( $report['summary']['end_user_rev']['avg'] ,2,'hosting_cost') }}</td>
                <td class="gross_revenue_avg">{{numberConverter( $report['summary']['end_user_rev']['t_mo_end'] ,2,'hosting_cost') }}</td>

                @if(isset($report['summary']['end_user_rev']['dates']) && !empty($report['summary']['end_user_rev']['dates']))
                @foreach ($report['summary']['end_user_rev']['dates'] as $end_user_rev)
                <td class="gross_revenue_data">{{numberConverter( $end_user_rev['value'] ,2,'hosting_cost') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-young-blue gross_revenue">
                <td class="font-weight-bold">
                  <strong class="text-with-sup">Gross Revenue (USD)<sup><i class="ml-3 text-dark fa fa-info-circle" title="Revenue after share"></i></sup></strong>
                </td>
                <td class="share_total">{{numberConverter( $report['summary']['gros_rev_usd']['total'] ,2,'hosting_cost') }}</td>
                <td class="share_month">{{numberConverter( $report['summary']['gros_rev_usd']['avg'] ,2,'hosting_cost') }}</td>
                <td class="share_avg">{{numberConverter( $report['summary']['gros_rev_usd']['t_mo_end'] ,2,'hosting_cost') }}</td>

                @if(isset($report['summary']['gros_rev_usd']['dates']) && !empty($report['summary']['gros_rev_usd']['dates']))
                @foreach ($report['summary']['gros_rev_usd']['dates'] as $gros_rev_usd)
                <td class="share_data">{{numberConverter( $gros_rev_usd['value'] ,2,'hosting_cost') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-young-blue">
                <td class="font-weight-bold">
                  <div>Gross Revenue</div>
                </td>
                <td class="local_share_total">{{numberConverter( $report['summary']['gros_rev']['total'] ,2,'hosting_cost') }}</td>
                <td class="local_share_month">{{numberConverter( $report['summary']['gros_rev']['avg'] ,2,'hosting_cost') }}</td>
                <td class="local_share_avg">{{numberConverter( $report['summary']['gros_rev']['t_mo_end'] ,2,'hosting_cost') }}</td>

                @if(isset($report['summary']['gros_rev']['dates']) && !empty($report['summary']['gros_rev']['dates']))
                @foreach ($report['summary']['gros_rev']['dates'] as $gros_rev)
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
                <td class="other_tax_total">{{numberConverter( $report['summary']['other_tax']['total'],2,'pre') }}</td>
                <td class="other_tax_avg">{{numberConverter( $report['summary']['other_tax']['avg'],2,'pre') }}</td>
                <td class="other_tax_month">{{numberConverter( $report['summary']['other_tax']['t_mo_end'],2,'pre') }}</td>

                @if(isset($report['summary']['other_tax']['dates'] ) && !empty($report['summary']['other_tax']['dates'] ))
                @foreach ($report['summary']['other_tax']['dates'] as $other_tax)
                <td class="other_tax_data">{{numberConverter( $other_tax['value'],2,'pre') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-young-yellow hiddenOtherTaxTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="font-weight: bolder; position: relative; left: 30px;">VAT</div>
                </td>
                <td class="hosting_cost_total">{{numberConverter( $report['summary']['vat']['total'],2,'pre') }}</td>
                <td class="hosting_cost_avg">{{numberConverter( $report['summary']['vat']['avg'],2,'pre') }}</td>
                <td class="hosting_cost_month">{{numberConverter( $report['summary']['vat']['t_mo_end'],2,'pre') }}</td>

                @if(isset($report['summary']['vat']['dates'] ) && !empty($report['summary']['vat']['dates'] ))
                @foreach ($report['summary']['vat']['dates'] as $vat)
                <td class="hosting_cost_data">{{numberConverter( $vat['value'],2,'pre') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-young-yellow hiddenOtherTaxTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="font-weight: bolder; position: relative; left: 30px;">WHT</div>
                </td>
                <td class="content_total">{{numberConverter( $report['summary']['wht']['total'],2,'pre') }}</td>
                <td class="content_avg">{{numberConverter( $report['summary']['wht']['avg'],2,'pre') }}</td>
                <td class="content_month">{{numberConverter( $report['summary']['wht']['t_mo_end'],2,'pre') }}</td>

                @if(isset($report['summary']['wht']['dates'] ) && !empty($report['summary']['wht']['dates'] ))
                @foreach ($report['summary']['wht']['dates'] as $wht)
                <td class="content_data">{{numberConverter( $wht['value'],2,'pre') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-young-yellow hiddenOtherTaxTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="font-weight: bolder; position: relative; left: 30px;">MISC TAX </div>
                </td>
                <td class="md_total">{{numberConverter( $report['summary']['misc_tax']['total'],2,'pre') }}</td>
                <td class="md_avg">{{numberConverter( $report['summary']['misc_tax']['avg'],2,'pre') }}</td>
                <td class="md_month">{{numberConverter( $report['summary']['misc_tax']['t_mo_end'],2,'pre') }}</td>

                @if(isset($report['summary']['misc_tax']['dates'] ) && !empty($report['summary']['misc_tax']['dates'] ))
                @foreach ($report['summary']['misc_tax']['dates'] as $misc_tax)
                <td class="md_data">{{numberConverter( $misc_tax['value'],2,'pre') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-young-yellow">
                <td class="font-weight-bold">
                  <strong class="text-with-sup">Net Revenue (USD)<sup><i class="ml-3 text-dark fa fa-info-circle" title="gross revenue USD - other tax"></i></sup></strong>
                </td>
                <td class="local_share_total">{{numberConverter( $report['summary']['net_after_tax']['total'] ,2,'hosting_cost') }}</td>
                <td class="local_share_month">{{numberConverter( $report['summary']['net_after_tax']['avg'] ,2,'hosting_cost') }}</td>
                <td class="local_share_avg">{{numberConverter( $report['summary']['net_after_tax']['t_mo_end'] ,2,'hosting_cost') }}</td>

                @if(isset($report['summary']['net_after_tax']['dates']) && !empty($report['summary']['net_after_tax']['dates']))
                @foreach ($report['summary']['net_after_tax']['dates'] as $net_after_tax)
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
                <td class="other_cost_total">{{numberConverter( $report['summary']['other_cost']['total'] ,2,'hosting_cost') }}</td>
                <td class="other_cost_month">{{numberConverter( $report['summary']['other_cost']['avg'] ,2,'hosting_cost') }}</td>
                <td class="other_cost_avg">{{numberConverter( $report['summary']['other_cost']['t_mo_end'] ,2,'hosting_cost') }}</td>

                @if(isset($report['summary']['other_cost']['dates']) && !empty($report['summary']['other_cost']['dates']))
                @foreach ($report['summary']['other_cost']['dates'] as $other_cost)
                <td class="other_cost_data">{{numberConverter( $other_cost['value'] ,2,'hosting_cost') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-old-blue hiddenOtherCostTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="font-weight: bolder; position: relative; left: 30px;">Hosting Cost</div>
                </td>
                <td class="hosting_cost_total">{{numberConverter( $report['summary']['hosting_cost']['total'] ,2,'hosting_cost') }}</td>
                <td class="hosting_cost_month">{{numberConverter( $report['summary']['hosting_cost']['avg'] ,2,'hosting_cost') }}</td>
                <td class="hosting_cost_avg">{{numberConverter( $report['summary']['hosting_cost']['t_mo_end'] ,2,'hosting_cost') }}</td>

                @if(isset($report['summary']['hosting_cost']['dates']) && !empty($report['summary']['hosting_cost']['dates']))
                @foreach ($report['summary']['hosting_cost']['dates'] as $hosting_cost)
                <td class="hosting_cost_data">{{numberConverter( $hosting_cost['value'] ,2,'hosting_cost') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-old-blue hiddenOtherCostTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="font-weight: bolder; position: relative; left: 30px;">Content 2%</div>
                </td>
                <td class="content_total">{{numberConverter( $report['summary']['content']['total'] ,2,'hosting_cost') }}</td>
                <td class="content_avg">{{numberConverter( $report['summary']['content']['avg'] ,2,'hosting_cost') }}</td>
                <td class="content_month">{{numberConverter( $report['summary']['content']['t_mo_end'] ,2,'hosting_cost') }}</td>

                @if(isset($report['summary']['content']['dates']) && !empty($report['summary']['content']['dates']))
                @foreach ($report['summary']['content']['dates'] as $content)
                <td class="content_data">{{numberConverter( $content['value'] ,2,'hosting_cost') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-old-blue hiddenOtherCostTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="font-weight: bolder; position: relative; left: 30px;">RND 5%</div>
                </td>
                <td class="md_total">{{numberConverter( $report['summary']['rnd']['total'] ,2,'hosting_cost') }}</td>
                <td class="md_avg">{{numberConverter( $report['summary']['rnd']['avg'] ,2,'hosting_cost') }}</td>
                <td class="md_month">{{numberConverter( $report['summary']['rnd']['t_mo_end'] ,2,'hosting_cost') }}</td>

                @if(isset($report['summary']['rnd']['dates']) && !empty($report['summary']['rnd']['dates']))
                @foreach ($report['summary']['rnd']['dates'] as $rnd)
                <td class="md_data">{{numberConverter( $rnd['value'] ,2,'hosting_cost') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-old-blue hiddenOtherCostTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="font-weight: bolder; position: relative; left: 30px;">BD 2.5%</div>
                </td>
                <td class="bd_total">{{numberConverter( $report['summary']['bd']['total'] ,2,'hosting_cost') }}</td>
                <td class="bd_avg">{{numberConverter( $report['summary']['bd']['avg'] ,2,'hosting_cost') }}</td>
                <td class="bd_month">{{numberConverter( $report['summary']['bd']['t_mo_end'] ,2,'hosting_cost') }}</td>

                @if(isset($report['summary']['bd']['dates']) && !empty($report['bd']['dates']))
                @foreach ($report['summary']['bd']['dates'] as $bd)
                <td class="bd_data">{{numberConverter( $bd['value'] ,2,'hosting_cost') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-old-blue hiddenOtherCostTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="font-weight: bolder; position: relative; left: 30px;">Marketing Cost</div>
                </td>
                <td class="market_cost_total">{{numberConverter( $report['summary']['market_cost']['total'],2,'pre') }}</td>
                <td class="market_cost_avg">{{numberConverter( $report['summary']['market_cost']['avg'],2,'pre') }}</td>
                <td class="market_cost_month">{{numberConverter( $report['summary']['market_cost']['t_mo_end'],2,'pre') }}</td>

                @if(isset($report['summary']['market_cost']['dates'] ) && !empty($report['summary']['market_cost']['dates'] ))
                @foreach ($report['summary']['market_cost']['dates'] as $market_cost)
                <td class="market_cost_data">{{numberConverter( $market_cost['value'],2,'pre') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-old-blue hiddenOtherCostTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="font-weight: bolder; position: relative; left: 30px;">Misc Cost</div>
                </td>
                <td class="platform_total">{{numberConverter( $report['summary']['misc_cost']['total'] ,2,'hosting_cost') }}</td>
                <td class="platform_avg">{{numberConverter( $report['summary']['misc_cost']['avg'] ,2,'hosting_cost') }}</td>
                <td class="platform_month">{{numberConverter( $report['summary']['misc_cost']['t_mo_end'] ,2,'hosting_cost') }}</td>

                @if(isset($report['summary']['misc_cost']['dates']) && !empty($report['summary']['misc_cost']['dates']))
                @foreach ($report['summary']['misc_cost']['dates'] as $misc_cost)
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
                <td class="cost_total cost">{{numberConverter( $report['summary']['cost_campaign']['total'] ,3,'hosting_cost') }}</td>
                <td class="cost_month">{{numberConverter( $report['summary']['cost_campaign']['avg'] ,3,'hosting_cost') }}</td>
                <td class="cost_avg">{{numberConverter( $report['summary']['cost_campaign']['t_mo_end'] ,3,'hosting_cost') }}</td>

                @if(isset($report['summary']['cost_campaign']['dates']) && !empty($report['summary']['cost_campaign']['dates']))
                @foreach ($report['summary']['cost_campaign']['dates'] as $cost_campaign)
                <td class="cost_data">{{numberConverter( $cost_campaign['value'] ,3,'hosting_cost') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-young-green pnl">
                <td class="font-weight-bold"><strong class="text-with-sup">GP<sup><i class="ml-3 text-dark fa fa-info-circle" title="GP = Net Revenue - (Cost Campaign + other cost)"></i></sup></strong>
                </td>
                <td class="pnl_total p">{{numberConverter( $report['summary']['pnl']['total'] ,2,'hosting_cost') }}</td>
                <td class="pnl_month">{{numberConverter( $report['summary']['pnl']['avg'] ,2,'hosting_cost') }}</td>
                <td class="pnl_avg">{{numberConverter( $report['summary']['pnl']['t_mo_end'] ,2,'hosting_cost') }}</td>

                @if(isset($report['summary']['pnl']['dates']) && !empty($report['summary']['pnl']['dates']))
                @foreach ($report['summary']['pnl']['dates'] as $pnl)
                <td class="pnl_data">{{numberConverter( $pnl['value'] ,2,'hosting_cost') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-old-yellow">
                <td class="font-weight-bold">
                  <strong>Campaign MO</strong>
                </td>
                <td class="reg_total">{{numberConverter( $report['summary']['mo']['total'] ,2,'hosting_cost') }}</td>
                <td class="reg_month">{{numberConverter( $report['summary']['mo']['avg'] ,2,'hosting_cost') }}</td>
                <td class="reg_avg">{{numberConverter( $report['summary']['mo']['t_mo_end'] ,2,'hosting_cost') }}</td>

                @if(isset($report['summary']['mo']['dates']) && !empty($report['summary']['mo']['dates']))
                @foreach ($report['summary']['mo']['dates'] as $mo)
                <td class="reg_data">{{numberConverter( $mo['value'] ,2,'hosting_cost') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-old-yellow price_mo">
                <td class="font-weight-bold"><strong class="text-with-sup">Price/Mo<sup><i class="ml-3 text-dark fa fa-info-circle" title="Price/Mo = cost campaign / mo"></i></sup></strong></td>
                <td class="price_mo_total">N/A</td>
                <td class="price_mo_avg">{{numberConverter( $report['summary']['price_mo']['avg'] ,2,'hosting_cost') }}</td>
                <td class="price_mo_month">N/A</td>

                @if(isset($report['summary']['price_mo']['dates']) && !empty($report['summary']['price_mo']['dates']))
                @foreach ($report['summary']['price_mo']['dates'] as $price_mo)
                <td class="price_mo_data">{{numberConverter( $price_mo['value'] ,2,'hosting_cost') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-old-yellow active_subscriber">
                <td class="font-weight-bold"><strong>Active Subscriber</strong></td>
                <td class="subs_total">{{numberConverter( $report['summary']['active_subs']['total'] ,2,'hosting_cost') }}</td>
                <td class="subs_month">N/A</td>
                <td class="subs_avg">N/A</td>

                @if(isset($report['summary']['active_subs']['dates']) && !empty($report['summary']['active_subs']['dates']))
                @foreach ($report['summary']['active_subs']['dates'] as $active_subs)
                <td class="subs_data">{{numberConverter( $active_subs['value'] ,2,'hosting_cost') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-old-yellow reg">
                <td class="font-weight-bold"><strong>Reg</strong></td>
                <td class="renewal_total">{{numberConverter( $report['summary']['reg']['total'] ,2,'hosting_cost') }}</td>
                <td class="renewal_month">{{numberConverter( $report['summary']['reg']['avg'] ,2,'hosting_cost') }}</td>
                <td class="renewal_avg">{{numberConverter( $report['summary']['reg']['t_mo_end'] ,2,'hosting_cost') }}</td>

                @if(isset($report['summary']['reg']['dates']) && !empty($report['summary']['reg']['dates']))
                @foreach ($report['summary']['reg']['dates'] as $reg)
                <td class="renewal_data">{{numberConverter( $reg['value'] ,2,'hosting_cost') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-old-yellow unreg">
                <td class="font-weight-bold"><strong>Unreg</strong></td>
                <td class="renewal_total">{{numberConverter( $report['summary']['unreg']['total'] ,2,'hosting_cost') }}</td>
                <td class="renewal_month">{{numberConverter( $report['summary']['unreg']['avg'] ,2,'hosting_cost') }}</td>
                <td class="renewal_avg">{{numberConverter( $report['summary']['unreg']['t_mo_end'] ,2,'hosting_cost') }}</td>

                @if(isset($report['summary']['unreg']['dates']) && !empty($report['summary']['unreg']['dates']))
                @foreach ($report['summary']['unreg']['dates'] as $unreg)
                <td class="renewal_data">{{numberConverter( $unreg['value'] ,2,'hosting_cost') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-old-yellow renewal">
                <td class="font-weight-bold"><strong>Renewal</strong></td>
                <td class="renewal_total">{{numberConverter( $report['summary']['renewal']['total'] ,2,'hosting_cost') }}</td>
                <td class="renewal_month">{{numberConverter( $report['summary']['renewal']['avg'] ,2,'hosting_cost') }}</td>
                <td class="renewal_avg">{{numberConverter( $report['summary']['renewal']['t_mo_end'] ,2,'hosting_cost') }}</td>

                @if(isset($report['summary']['renewal']['dates']) && !empty($report['summary']['renewal']['dates']))
                @foreach ($report['summary']['renewal']['dates'] as $renewal)
                <td class="renewal_data">{{numberConverter( $renewal['value'] ,2,'hosting_cost') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-old-yellow arpu_7">
                <td class="font-weight-bold">
                  <div class="text-with-sup">
                    <div class="btn-ico-expand arpu_plus" data-sign="plus" style="cursor: pointer;">+</div>
                    <div class="text-left"><strong>7 ARPU</strong></div>
                  </div>
                </td>
                <td class="arpu_7_total">N/A</td>
                <td class="arpu_7_month">{{numberConverter( $report['summary']['arpu_7_usd']['avg'] ,4,'hosting_cost') }}</td>
                <td class="arpu_7_avg">N/A</td>

                @if(isset($report['summary']['arpu_7_usd']['dates']) && !empty($report['summary']['arpu_7_usd']['dates']))
                @foreach ($report['summary']['arpu_7_usd']['dates'] as $arpu_7_usd)
                <td class="arpu_7_data">{{numberConverter( $arpu_7_usd['value'] ,4,'hosting_cost') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-old-yellow hiddenArpuTr" style="display: none;">
                <td class="font-weight-bold">
                  <div style="font-weight: bolder; position: relative; left: 30px;">30 ARPU</div>
                </td>
                <td class="arpu_30_total">N/A</td>
                <td class="arpu_30_month">{{numberConverter( $report['summary']['arpu_30_usd']['avg'] ,4,'hosting_cost') }}</td>
                <td class="arpu_30_avg">N/A</td>

                @if(isset($report['summary']['arpu_30_usd']['dates']) && !empty($report['summary']['arpu_30_usd']['dates']))
                @foreach ($report['summary']['arpu_30_usd']['dates'] as $arpu_30_usd)
                <td class="arpu_30_data">{{numberConverter( $arpu_30_usd['value'] ,4,'hosting_cost') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-old-yellow">
                <td class="font-weight-bold">
                  <strong class="text-with-sup" style="position: relative; left: -1px;">ROI<sup><i class="ml-3 text-dark fa fa-info-circle" title="ROI = Price/Mo / 30 ARPU"></i></sup></strong>
                </td>
                <td class="cost_total cost">N/A</td>
                <td class="cost_month">{{ numberConverter( $report['summary']['roi']['dates'][$date]['value'],4,'pre') }}</td>
                <td class="cost_avg">N/A</td>

                @if(isset($report['summary']['roi']['dates']) && !empty($report['summary']['roi']['dates']))
                @foreach ($report['summary']['roi']['dates'] as $roi)
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
                <td class="cost_month">{{ numberConverter( $report['summary']['bill']['avg'],2,'pre') }}</td>
                <td class="cost_avg">N/A</td>

                @if(isset($report['summary']['bill']['dates']) && !empty($report['summary']['bill']['dates']))
                @foreach ($report['summary']['bill']['dates'] as $bill)
                <td class="cost_data">{{numberConverter( $bill['value'] ,2,'hosting_cost') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-old-yellow billExtendedRows" style="display: none;">
                <td>
                  <strong class="text-with-sup"  style="font-weight: bolder; position: relative; left: 30px;">First P.%<sup><i class="ml-3 text-dark fa fa-info-circle" title="(delivered first push / first push sent) * 100%"></i></sup></strong>
                </td>
                <td class="cost_total cost">N/A</td>
                <td class="cost_month">{{ numberConverter( $report['summary']['firstpush']['avg'],2,'pre') }}</td>
                <td class="cost_avg">N/A</td>

                @if(isset($report['summary']['firstpush']['dates']) && !empty($report['summary']['firstpush']['dates']))
                @foreach ($report['summary']['firstpush']['dates'] as $firstpush)
                <td class="cost_data">{{numberConverter( $firstpush['value'] ,2,'hosting_cost') }}</td>
                @endforeach
                @endif
              </tr>

              <tr class="bg-old-yellow billExtendedRows" style="display: none;">
                <td>
                  <strong class="text-with-sup"  style="font-weight: bolder; position: relative; left: 30px;">Daily P.%<sup><i class="ml-3 text-dark fa fa-info-circle" title="(delivered daily push / daily push sent) * 100%"></i></sup></strong>
                </td>
                <td class="cost_total cost">N/A</td>
                <td class="cost_month">{{ numberConverter( $report['summary']['dailypush']['avg'],2,'pre') }}</td>
                <td class="cost_avg">N/A</td>

                @if(isset($report['summary']['dailypush']['dates']) && !empty($report['summary']['dailypush']['dates']))
                @foreach ($report['summary']['dailypush']['dates'] as $dailypush)
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
