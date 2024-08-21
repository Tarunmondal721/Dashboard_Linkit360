@extends('layouts.admin')

@section('title')

    {{ __('Target Revenue') }}

@endsection

@section('content')
<div class="" style="margin-top:25px">
  <div class="page-title" style="margin-bottom:25px">
    <div class="row justify-content-between align-items-center">
      <div class="col-xl-4 col-lg-4 col-md-4 d-flex align-items-center justify-content-between justify-content-md-start mb-3 mb-md-0">
        <div class="d-inline-block">
          <div style="white-space:nowrap;">Targeting Monthwise Revenue, Revenue after telco & GP</div>
        </div>
      </div>
      <div class="col-xl-8 col-lg-8 col-md-8 d-flex align-items-center justify-content-between justify-content-md-end">
      </div>
    </div>
  </div>

  @include('finance.partials.financeTargetFilter')


      <div class="table-responsive shadow-sm mb-4 tableFixHead revenue-reconcile">
          <table class="table table-light table-borderd m-0 font-13 table-text-no-wrap">
              <thead class="thead-dark sticky-col">
                  <tr>
                      <th class="sticky-col first-col" width="10%">Operator Name</th>
                      <th>Source</th>
                      <th>Total</th>
                      @if(isset($no_of_days) && !empty($no_of_days))
                      @foreach ($no_of_days as $days)
                        <th class="align-middle">{{$days['no']}}</th>
                      @endforeach
                      @endif

                  </tr>
              </thead>
              <tbody>
                  @if(isset($allsummaryData) && !empty($allsummaryData))
                  <tr style="background-color: #B4D4FF;">
                      <td class="align-middle text-center first-col font-weight-bold" style="background-color: white !important;" rowspan="15">All Operator (USD)</td>
                      <td>Gross Revenue</td>
                      <td>{{numberConverter($allsummaryData['gross_rev']['total'],2,'pre')}}</td>

                      @if(isset($allsummaryData['gross_rev']['dates']) && !empty($allsummaryData['gross_rev']['dates']))
                      @foreach ($allsummaryData['gross_rev']['dates'] as $gross_rev)
                        <td class="{{$gross_rev['class']}}">{{numberConverter($gross_rev['value'],2,'pre')}}</td>
                      @endforeach
                      @endif
                  </tr>

                  <tr style="background-color: #B4D4FF;">
                      <td>Target Revenue</td>
                      <td class=""><?=($allsummaryData['target_rev']['total'] != 0) ? numberConverter($allsummaryData['target_rev']['total'],2,'pre') :
                      'N/A' ?></td>

                      @if(isset($allsummaryData['target_rev']['dates']) && !empty($allsummaryData['target_rev']['dates']))
                      @foreach ($allsummaryData['target_rev']['dates'] as $target_rev)
                        <td class=""><?=($target_rev['value'] != 0) ? numberConverter($target_rev['value'],2,'pre') : 'N/A' ?></td>
                      @endforeach
                      @endif
                  </tr>

                  <tr style="background-color: #B4D4FF;">
                      <td>Revenue Discrepency</td>
                      <td>{{numberConverter($allsummaryData['rev_disc']['total'],2,'post','%')}}</td>

                      @if(isset($allsummaryData['rev_disc']['dates']) && !empty($allsummaryData['rev_disc']['dates']))
                      @foreach ($allsummaryData['rev_disc']['dates'] as $rev_disc)
                        <td class="{{$rev_disc['class']}}">{{numberConverter($rev_disc['value'],2,'post','%')}}</td>
                      @endforeach
                      @endif
                  </tr>

                  <tr style="background-color: #96EFFF;">
                      <td>Revenue After Share</td>
                      <td>{{numberConverter($allsummaryData['rev_after_share']['total'],2,'pre')}}</td>

                      @if(isset($allsummaryData['rev_after_share']['dates']) && !empty($allsummaryData['rev_after_share']['dates']))
                      @foreach ($allsummaryData['rev_after_share']['dates'] as $rev_after_share)
                        <td class="{{$rev_after_share['class']}}">{{numberConverter($rev_after_share['value'],2,'pre')}}</td>
                      @endforeach
                      @endif
                  </tr>

                  <tr style="background-color: #96EFFF;">
                      <td>Target Revenue After Share</td>
                      <td class=""><?=($allsummaryData['target_after_share']['total'] != 0) ? numberConverter($allsummaryData['target_after_share']['total'],2,'pre') :
                      'N/A' ?></td>

                      @if(isset($allsummaryData['target_after_share']['dates']) && !empty($allsummaryData['target_after_share']['dates']))
                      @foreach ($allsummaryData['target_after_share']['dates'] as $target_after_share)
                        <td class=""><?=($target_after_share['value'] != 0) ? numberConverter($target_after_share['value'],2,'pre') : 'N/A' ?></td>
                      @endforeach
                      @endif
                  </tr>

                  <tr style="background-color: #96EFFF;">
                      <td>Revenue After Share Discrepency</td>
                      <td>{{numberConverter($allsummaryData['target_rev_disc']['total'],2,'post','%')}}</td>

                      @if(isset($allsummaryData['target_rev_disc']['dates']) && !empty($allsummaryData['target_rev_disc']['dates']))
                      @foreach ($allsummaryData['target_rev_disc']['dates'] as $target_rev_disc)
                        <td class="{{$target_rev_disc['class']}}">{{numberConverter($target_rev_disc['value'],2,'post','%')}}</td>
                      @endforeach
                      @endif
                  </tr>

                  <tr style="background-color: #FF9130;">
                      <td>GP</td>
                      <td>{{numberConverter($allsummaryData['pnl']['total'],2,'pre')}}</td>

                      @if(isset($allsummaryData['pnl']['dates']) && !empty($allsummaryData['pnl']['dates']))
                      @foreach ($allsummaryData['pnl']['dates'] as $pnl)
                        <td class="{{$pnl['class']}}">{{numberConverter($pnl['value'],2,'pre')}}</td>
                      @endforeach
                      @endif
                  </tr>

                  <tr style="background-color: #FF9130;">
                      <td>Target GP</td>
                      <td class=""><?=($allsummaryData['target_pnl']['total'] != 0) ? numberConverter($allsummaryData['target_pnl']['total'],2,'pre') :
                      'N/A' ?></td>

                      @if(isset($allsummaryData['target_pnl']['dates']) && !empty($allsummaryData['target_pnl']['dates']))
                      @foreach ($allsummaryData['target_pnl']['dates'] as $target_pnl)
                        <td class=""><?=($target_pnl['value'] != 0) ? numberConverter($target_pnl['value'],2,'pre') : 'N/A' ?></td>
                      @endforeach
                      @endif
                  </tr>

                  <tr style="background-color: #FF9130;">
                      <td>GP Discrepency</td>
                      <td>{{numberConverter($allsummaryData['pnl_disc']['total'],2,'post','%')}}</td>

                      @if(isset($allsummaryData['pnl_disc']['dates']) && !empty($allsummaryData['pnl_disc']['dates']))
                      @foreach ($allsummaryData['pnl_disc']['dates'] as $pnl_disc)
                        <td class="{{$pnl_disc['class']}}">{{numberConverter($pnl_disc['value'],2,'post','%')}}</td>
                      @endforeach
                      @endif
                  </tr>

                  <tr style="background-color: #DFCCFB;">
                      <td>Opex</td>
                      <td>{{numberConverter($allsummaryData['opex']['total'],2,'pre')}}</td>

                      @if(isset($allsummaryData['opex']['dates']) && !empty($allsummaryData['opex']['dates']))
                      @foreach ($allsummaryData['opex']['dates'] as $opex)
                        <td class="{{$opex['class']}}">{{numberConverter($opex['value'],2,'pre')}}</td>
                      @endforeach
                      @endif
                  </tr>

                  <tr style="background-color: #DFCCFB;">
                      <td>Target Opex</td>
                      <td class=""><?=($allsummaryData['target_opex']['total'] != 0) ? numberConverter($allsummaryData['target_opex']['total'],2,'pre') :
                      'N/A' ?></td>

                      @if(isset($allsummaryData['target_opex']['dates']) && !empty($allsummaryData['target_opex']['dates']))
                      @foreach ($allsummaryData['target_opex']['dates'] as $target_opex)
                        <td class=""><?=($target_opex['value'] != 0) ? numberConverter($target_opex['value'],2,'pre') : 'N/A' ?></td>
                      @endforeach
                      @endif
                  </tr>

                  <tr style="background-color: #DFCCFB;">
                      <td>Opex Discrepency</td>
                      <td>{{numberConverter($allsummaryData['opex_disc']['total'],2,'post','%')}}</td>

                      @if(isset($allsummaryData['opex_disc']['dates']) && !empty($allsummaryData['opex_disc']['dates']))
                      @foreach ($allsummaryData['opex_disc']['dates'] as $opex_disc)
                        <td class="{{$opex_disc['class']}}">{{numberConverter($opex_disc['value'],2,'post','%')}}</td>
                      @endforeach
                      @endif
                  </tr>

                  <tr style="background-color: #FFDB89;">
                      <td>Ebida</td>
                      <td>{{numberConverter($allsummaryData['ebida']['total'],2,'pre')}}</td>

                      @if(isset($allsummaryData['ebida']['dates']) && !empty($allsummaryData['ebida']['dates']))
                      @foreach ($allsummaryData['ebida']['dates'] as $ebida)
                        <td class="{{$ebida['class']}}">{{numberConverter($ebida['value'],2,'pre')}}</td>
                      @endforeach
                      @endif
                  </tr>

                  <tr style="background-color: #FFDB89;">
                      <td>Target Ebida</td>
                      <td class=""><?=($allsummaryData['target_ebida']['total'] != 0) ? numberConverter($allsummaryData['target_ebida']['total'],2,'pre') :
                      'N/A' ?></td>

                      @if(isset($allsummaryData['target_ebida']['dates']) && !empty($allsummaryData['target_ebida']['dates']))
                      @foreach ($allsummaryData['target_ebida']['dates'] as $target_ebida)
                        <td class=""><?=($target_ebida['value'] != 0) ? numberConverter($target_ebida['value'],2,'pre') : 'N/A' ?></td>
                      @endforeach
                      @endif
                  </tr>

                  <tr style="background-color: #FFDB89;">
                      <td>Ebida Discrepency</td>
                      <td>{{numberConverter($allsummaryData['ebida_disc']['total'],2,'post','%')}}</td>

                      @if(isset($allsummaryData['ebida_disc']['dates']) && !empty($allsummaryData['ebida_disc']['dates']))
                      @foreach ($allsummaryData['ebida_disc']['dates'] as $ebida_disc)
                        <td class="{{$ebida_disc['class']}}">{{numberConverter($ebida_disc['value'],2,'post','%')}}</td>
                      @endforeach
                      @endif
                  </tr>

                  @endif
              </tbody>
          </table>
      </div>

      <div class="float-right mt-2 mb-2">
          <input type="checkbox" id="monitor-expand"> Expand All
      </div>

      <div class="table-responsive shadow-sm tableFixHead">
          <table class="table table-light table-borderd m-0 font-13 table-text-no-wrap">
              <thead class="thead-dark sticky-col">
                  <tr>
                      <th class="sticky-col first-col" width="10%">Operator Name</th>
                      <th>Source</th>
                      <th>Total</th>
                      @if(isset($no_of_days) && !empty($no_of_days))
                      @foreach ($no_of_days as $days)
                        <th class="align-middle">{{$days['no']}}</th>
                      @endforeach
                      @endif
                  </tr>
              </thead>
              <tbody>
                  @if(isset($countryWiseData) && !empty($countryWiseData))
                  @php $i = 0; @endphp
                  @foreach ($countryWiseData as $key => $reconcileData)

                  @php $country_name = str_replace(array('.', ' ',),"_",$reconcileData['country']['country']); @endphp

                  <tr style="background-color: #B4D4FF;">
                      <td class="align-middle text-center font-weight-bold first-col <?php echo ($i % 2 == 0) ? 'country-odd-bg' : 'bg-soft-neutral' ?>" rowspan="15"><span
                              class="opbtn" data-param="{{$country_name}}"
                              style="cursor:pointer; min-width:10px; font-size:20px;"><strong>+</strong></span>&nbsp;&nbsp;

                        @if (isset($reconcileData['country']['flag']))
                        <img src="{{ asset('/flags/'.$reconcileData['country']['flag']) }}" height="20" width="30">
                        @endif
                              &nbsp;{{$reconcileData['country']['country']}}

                              @if (isset($reconcileData['country']['currency_code']))
                              ({{($reconcileData['country']['currency_code'] == '$') ? 'USD' : $reconcileData['country']['currency_code']}})
                              @endif
                        </td>
                      <td class="font-weight-bold">Gross Revenue</td>
                      <td class="font-weight-bold">
                        {{numberConverter($reconcileData['countrySum']['gross_rev']['total'],2,'pre')}}
                      </td>

                      @if(isset($reconcileData['countrySum']['gross_rev']['dates']) && !empty($reconcileData['countrySum']['gross_rev']['dates']))
                      @foreach ($reconcileData['countrySum']['gross_rev']['dates'] as $gross_rev)
                        <td class="font-weight-bold">{{numberConverter($gross_rev['value'],2,'pre')}}</td>
                      @endforeach
                      @endif
                  </tr>

                  <tr style="background-color: #B4D4FF;">
                      <td class="font-weight-bold">Target Revenue</td>

                      <td class=""><?=($reconcileData['countrySum']['target_rev']['total'] != 0) ? numberConverter($reconcileData['countrySum']['target_rev']['total'],2,'pre') :
                      'N/A' ?></td>

                      @if(isset($reconcileData['countrySum']['target_rev']['dates']) && !empty($reconcileData['countrySum']['target_rev']['dates']))
                      @foreach ($reconcileData['countrySum']['target_rev']['dates'] as $target_rev)
                        <td class=""><?=($target_rev['value'] != 0) ? numberConverter($target_rev['value'],2,'pre') : 'N/A' ?></td>
                      @endforeach
                      @endif
                  </tr>

                  <tr style="background-color: #B4D4FF;">
                      <td class="font-weight-bold">Revenue Discrepency</td>
                      <td class="font-weight-bold">
                        {{numberConverter($reconcileData['countrySum']['rev_disc']['total'],2,'post','%')}}
                      </td>
                      @if(isset($reconcileData['countrySum']['rev_disc']['dates']) && !empty($reconcileData['countrySum']['rev_disc']['dates']))
                      @foreach ($reconcileData['countrySum']['rev_disc']['dates'] as $rev_disc)
                        <td class="font-weight-bold">{{numberConverter($rev_disc['value'],2,'post','%')}}</td>
                      @endforeach
                      @endif
                  </tr>

                  <tr style="background-color: #96EFFF;">
                      <td class="font-weight-bold">Revenue After Share</td>
                      <td class="font-weight-bold">
                        {{numberConverter($reconcileData['countrySum']['rev_after_share']['total'],2,'pre')}}
                      </td>
                      @if(isset($reconcileData['countrySum']['rev_after_share']['dates']) && !empty($reconcileData['countrySum']['rev_after_share']['dates']))
                      @foreach ($reconcileData['countrySum']['rev_after_share']['dates'] as $rev_after_share)
                        <td class="font-weight-bold">{{numberConverter($rev_after_share['value'],2,'pre')}}</td>
                      @endforeach
                      @endif
                  </tr>

                  <tr style="background-color: #96EFFF;">
                      <td class="font-weight-bold">Target Revenue After Share</td>

                      <td class=""><?=($reconcileData['countrySum']['target_after_share']['total'] != 0) ? numberConverter($reconcileData['countrySum']['target_after_share']['total'],2,'pre') :
                      'N/A' ?></td>

                      @if(isset($reconcileData['countrySum']['target_after_share']['dates']) && !empty($reconcileData['countrySum']['target_after_share']['dates']))
                      @foreach ($reconcileData['countrySum']['target_after_share']['dates'] as $target_after_share)
                        <td class=""><?=($target_after_share['value'] != 0) ? numberConverter($target_after_share['value'],2,'pre') : 'N/A' ?></td>
                      @endforeach
                      @endif
                  </tr>

                  <tr style="background-color: #96EFFF;">
                      <td class="font-weight-bold">Revenue After Share Discrepency</td>
                      <td class="font-weight-bold">
                        {{numberConverter($reconcileData['countrySum']['target_rev_disc']['total'],2,'post','%')}}
                      </td>

                      @if(isset($reconcileData['countrySum']['target_rev_disc']['dates']) && !empty($reconcileData['countrySum']['target_rev_disc']['dates']))
                      @foreach ($reconcileData['countrySum']['target_rev_disc']['dates'] as $target_rev_disc)
                        <td class="font-weight-bold">{{numberConverter($target_rev_disc['value'],2,'post','%')}}</td>
                      @endforeach
                      @endif
                  </tr>

                  <tr style="background-color: #FF9130;">
                      <td class="font-weight-bold">GP</td>
                      <td class="font-weight-bold">
                        {{numberConverter($reconcileData['countrySum']['pnl']['total'],2,'pre')}}
                      </td>
                      @if(isset($reconcileData['countrySum']['pnl']['dates']) && !empty($reconcileData['countrySum']['pnl']['dates']))
                      @foreach ($reconcileData['countrySum']['pnl']['dates'] as $pnl)
                        <td class="font-weight-bold">{{numberConverter($pnl['value'],2,'pre')}}</td>
                      @endforeach
                      @endif
                  </tr>

                  <tr style="background-color: #FF9130;">
                      <td class="font-weight-bold">Target GP</td>

                      <td class=""><?=($reconcileData['countrySum']['target_pnl']['total'] != 0) ? numberConverter($reconcileData['countrySum']['target_pnl']['total'],2,'pre') :
                      'N/A' ?></td>

                      @if(isset($reconcileData['countrySum']['target_pnl']['dates']) && !empty($reconcileData['countrySum']['target_pnl']['dates']))
                      @foreach ($reconcileData['countrySum']['target_pnl']['dates'] as $target_pnl)
                        <td class=""><?=($target_pnl['value'] != 0) ? numberConverter($target_pnl['value'],2,'pre') : 'N/A' ?></td>
                      @endforeach
                      @endif
                  </tr>

                  <tr style="background-color: #FF9130;">
                      <td class="font-weight-bold">GP Discrepency</td>
                      <td class="font-weight-bold">
                        {{numberConverter($reconcileData['countrySum']['pnl_disc']['total'],2,'post','%')}}
                      </td>
                      @if(isset($reconcileData['countrySum']['pnl_disc']['dates']) && !empty($reconcileData['countrySum']['pnl_disc']['dates']))
                      @foreach ($reconcileData['countrySum']['pnl_disc']['dates'] as $pnl_disc)
                        <td class="font-weight-bold">{{numberConverter($pnl_disc['value'],2,'post','%')}}</td>
                      @endforeach
                      @endif
                  </tr>

                  <tr style="background-color: #DFCCFB;">
                      <td class="font-weight-bold">Opex</td>
                      <td class="font-weight-bold">
                        {{numberConverter($reconcileData['countrySum']['opex']['total'],2,'pre')}}
                      </td>
                      @if(isset($reconcileData['countrySum']['opex']['dates']) && !empty($reconcileData['countrySum']['opex']['dates']))
                      @foreach ($reconcileData['countrySum']['opex']['dates'] as $opex)
                        <td class="font-weight-bold">{{numberConverter($opex['value'],2,'pre')}}</td>
                      @endforeach
                      @endif
                  </tr>

                  <tr style="background-color: #DFCCFB;">
                      <td class="font-weight-bold">Target Opex</td>

                      <td class=""><?=($reconcileData['countrySum']['target_opex']['total'] != 0) ? numberConverter($reconcileData['countrySum']['target_opex']['total'],2,'pre') :
                      'N/A' ?></td>

                      @if(isset($reconcileData['countrySum']['target_opex']['dates']) && !empty($reconcileData['countrySum']['target_opex']['dates']))
                      @foreach ($reconcileData['countrySum']['target_opex']['dates'] as $target_opex)
                        <td class=""><?=($target_opex['value'] != 0) ? numberConverter($target_opex['value'],2,'pre') : 'N/A' ?></td>
                      @endforeach
                      @endif
                  </tr>

                  <tr style="background-color: #DFCCFB;">
                      <td class="font-weight-bold">Opex Discrepency</td>
                      <td class="font-weight-bold">
                        {{numberConverter($reconcileData['countrySum']['opex_disc']['total'],2,'post','%')}}
                      </td>
                      @if(isset($reconcileData['countrySum']['opex_disc']['dates']) && !empty($reconcileData['countrySum']['opex_disc']['dates']))
                      @foreach ($reconcileData['countrySum']['opex_disc']['dates'] as $opex_disc)
                        <td class="font-weight-bold">{{numberConverter($opex_disc['value'],2,'post','%')}}</td>
                      @endforeach
                      @endif
                  </tr>

                  <tr style="background-color: #FFDB89;">
                      <td class="font-weight-bold">Ebida</td>
                      <td class="font-weight-bold">
                        {{numberConverter($reconcileData['countrySum']['ebida']['total'],2,'pre')}}
                      </td>
                      @if(isset($reconcileData['countrySum']['ebida']['dates']) && !empty($reconcileData['countrySum']['ebida']['dates']))
                      @foreach ($reconcileData['countrySum']['ebida']['dates'] as $ebida)
                        <td class="font-weight-bold">{{numberConverter($ebida['value'],2,'pre')}}</td>
                      @endforeach
                      @endif
                  </tr>

                  <tr style="background-color: #FFDB89;">
                      <td class="font-weight-bold">Target Ebida</td>

                      <td class=""><?=($reconcileData['countrySum']['target_ebida']['total'] != 0) ? numberConverter($reconcileData['countrySum']['target_ebida']['total'],2,'pre') :
                      'N/A' ?></td>

                      @if(isset($reconcileData['countrySum']['target_ebida']['dates']) && !empty($reconcileData['countrySum']['target_ebida']['dates']))
                      @foreach ($reconcileData['countrySum']['target_ebida']['dates'] as $target_ebida)
                        <td class=""><?=($target_ebida['value'] != 0) ? numberConverter($target_ebida['value'],2,'pre') : 'N/A' ?></td>
                      @endforeach
                      @endif
                  </tr>

                  <tr style="background-color: #FFDB89;">
                      <td class="font-weight-bold">Ebida Discrepency</td>
                      <td class="font-weight-bold">
                        {{numberConverter($reconcileData['countrySum']['ebida_disc']['total'],2,'post','%')}}
                      </td>
                      @if(isset($reconcileData['countrySum']['ebida_disc']['dates']) && !empty($reconcileData['countrySum']['ebida_disc']['dates']))
                      @foreach ($reconcileData['countrySum']['ebida_disc']['dates'] as $ebida_disc)
                        <td class="font-weight-bold">{{numberConverter($ebida_disc['value'],2,'post','%')}}</td>
                      @endforeach
                      @endif
                  </tr>

                  @if(isset($reconcileData['operator']) && !empty($reconcileData['operator']))
                  @foreach ($reconcileData['operator'] as $rec_key=>$reconcile)

                  <tr class="{{$country_name}} expandable" style="display: none; background-color: #B4D4FF;">
                      <td class="align-middle text-center first-col <?php echo ($rec_key % 2 == 0) ? 'operator-odd-bg' : 'bg-soft-neutral' ?>" rowspan="15"><span
                              class="ml-4"><a href="javascript:void(0)" class="bg-info" data-url="{{ URL::to('finance/serviceTargetData/'.$reconcile['operator']['id_operator']) }}"  data-size="lg" data-ajax-popup="true" data-toggle="tooltip" data-original-title="View All service data" data-title="{{__('View All service data #'.$reconcile['operator']['operator_name'])}}">{{ $reconcile['operator']['operator_name'] }}</a></span></td>
                      <td>Gross Revenue</td>
                      <td>{{numberConverter($reconcile['gross_rev']['total'],2,'pre')}}</td>

                      @if(isset($reconcile['gross_rev']['dates']) && !empty($reconcile['gross_rev']['dates']))
                      @foreach ($reconcile['gross_rev']['dates'] as $gross_rev)
                        <td class="{{$gross_rev['class']}}">{{numberConverter($gross_rev['value'],2,'pre')}}</td>
                      @endforeach
                      @endif
                  </tr>

                  <tr class="{{$country_name}} expandable" style="display: none; background-color: #B4D4FF;">
                      <td>Target Revenue</td>
                      <td class=""><?=($reconcile['target_rev']['total'] != 0) ? numberConverter($reconcile['target_rev']['total'],2,'pre') :
                      'N/A' ?></td>

                      @if(isset($reconcile['target_rev']['dates']) && !empty($reconcile['target_rev']['dates']))
                      @foreach ($reconcile['target_rev']['dates'] as $target_rev)
                        <td class=""><?=($target_rev['value'] != 0) ? numberConverter($target_rev['value'],2,'pre') : 'N/A' ?></td>
                      @endforeach
                      @endif
                  </tr>

                  <tr class="{{$country_name}} expandable" style="display: none; background-color: #B4D4FF;">
                      <td>Revenue Discrepency</td>
                      <td>{{numberConverter($reconcile['rev_disc']['total'],2,'post','%')}}</td>

                      @if(isset($reconcile['rev_disc']['dates']) && !empty($reconcile['rev_disc']['dates']))
                      @foreach ($reconcile['rev_disc']['dates'] as $rev_disc)
                        <td class="{{$rev_disc['class']}}">{{numberConverter($rev_disc['value'],2,'post','%')}}</td>
                      @endforeach
                      @endif
                  </tr>

                  <tr class="{{$country_name}} expandable" style="display: none; background-color: #96EFFF;">
                      <td>Revenue After Share</td>
                      <td>{{numberConverter($reconcile['rev_after_share']['total'],2,'pre')}}</td>

                      @if(isset($reconcile['rev_after_share']['dates']) && !empty($reconcile['rev_after_share']['dates']))
                      @foreach ($reconcile['rev_after_share']['dates'] as $rev_after_share)
                        <td class="{{$rev_after_share['class']}}">{{numberConverter($rev_after_share['value'],2,'pre')}}</td>
                      @endforeach
                      @endif
                  </tr>

                  <tr class="{{$country_name}} expandable" style="display: none; background-color: #96EFFF;">
                      <td>Target Revenue After Share</td>
                      <td class=""><?=($reconcile['target_after_share']['total'] != 0) ? numberConverter($reconcile['target_after_share']['total'],2,'pre') :
                      'N/A' ?></td>

                      @if(isset($reconcile['target_after_share']['dates']) && !empty($reconcile['target_after_share']['dates']))
                      @foreach ($reconcile['target_after_share']['dates'] as $target_after_share)
                        <td class=""><?=($target_after_share['value'] != 0) ? numberConverter($target_after_share['value'],2,'pre') : 'N/A' ?></td>
                      @endforeach
                      @endif
                  </tr>

                  <tr class="{{$country_name}} expandable" style="display: none; background-color: #96EFFF;">
                      <td>Revenue After Share Discrepency</td>
                      <td>{{numberConverter($reconcile['target_rev_disc']['total'],2,'post','%')}}</td>

                      @if(isset($reconcile['target_rev_disc']['dates']) && !empty($reconcile['target_rev_disc']['dates']))
                      @foreach ($reconcile['target_rev_disc']['dates'] as $target_rev_disc)
                        <td class="{{$target_rev_disc['class']}}">{{numberConverter($target_rev_disc['value'],2,'post','%')}}</td>
                      @endforeach
                      @endif
                  </tr>

                  <tr class="{{$country_name}} expandable" style="display: none; background-color: #FF9130;">
                      <td>GP</td>
                      <td>{{numberConverter($reconcile['pnl']['total'],2,'pre')}}</td>

                      @if(isset($reconcile['pnl']['dates']) && !empty($reconcile['pnl']['dates']))
                      @foreach ($reconcile['pnl']['dates'] as $pnl)
                        <td class="{{$pnl['class']}}">{{numberConverter($pnl['value'],2,'pre')}}</td>
                      @endforeach
                      @endif
                  </tr>

                  <tr class="{{$country_name}} expandable" style="display: none; background-color: #FF9130;">
                      <td>Target GP</td>
                      <td class=""><?=($reconcile['target_pnl']['total'] != 0) ? numberConverter($reconcile['target_pnl']['total'],2,'pre') :
                      'N/A' ?></td>

                      @if(isset($reconcile['target_pnl']['dates']) && !empty($reconcile['target_pnl']['dates']))
                      @foreach ($reconcile['target_pnl']['dates'] as $target_pnl)
                        <td class=""><?=($target_pnl['value'] != 0) ? numberConverter($target_pnl['value'],2,'pre') : 'N/A' ?></td>
                      @endforeach
                      @endif
                  </tr>

                  <tr class="{{$country_name}} expandable" style="display: none; background-color: #FF9130;">
                      <td>GP Discrepency</td>
                      <td>{{numberConverter($reconcile['pnl_disc']['total'],2,'post','%')}}</td>

                      @if(isset($reconcile['pnl_disc']['dates']) && !empty($reconcile['pnl_disc']['dates']))
                      @foreach ($reconcile['pnl_disc']['dates'] as $pnl_disc)
                        <td class="{{$pnl_disc['class']}}">{{numberConverter($pnl_disc['value'],2,'post','%')}}</td>
                      @endforeach
                      @endif
                  </tr>

                  <tr class="{{$country_name}} expandable" style="display: none; background-color: #DFCCFB;">
                      <td>Opex</td>
                      <td>{{numberConverter($reconcile['opex']['total'],2,'pre')}}</td>

                      @if(isset($reconcile['opex']['dates']) && !empty($reconcile['opex']['dates']))
                      @foreach ($reconcile['opex']['dates'] as $opex)
                        <td class="{{$opex['class']}}">{{numberConverter($opex['value'],2,'pre')}}</td>
                      @endforeach
                      @endif
                  </tr>

                  <tr class="{{$country_name}} expandable" style="display: none; background-color: #DFCCFB;">
                      <td>Target Opex</td>
                      <td class=""><?=($reconcile['target_opex']['total'] != 0) ? numberConverter($reconcile['target_opex']['total'],2,'pre') :
                      'N/A' ?></td>

                      @if(isset($reconcile['target_opex']['dates']) && !empty($reconcile['target_opex']['dates']))
                      @foreach ($reconcile['target_opex']['dates'] as $target_opex)
                        <td class=""><?=($target_opex['value'] != 0) ? numberConverter($target_opex['value'],2,'pre') : 'N/A' ?></td>
                      @endforeach
                      @endif
                  </tr>

                  <tr class="{{$country_name}} expandable" style="display: none; background-color: #DFCCFB;">
                      <td>Opex Discrepency</td>
                      <td>{{numberConverter($reconcile['opex_disc']['total'],2,'post','%')}}</td>

                      @if(isset($reconcile['opex_disc']['dates']) && !empty($reconcile['opex_disc']['dates']))
                      @foreach ($reconcile['opex_disc']['dates'] as $opex_disc)
                        <td class="{{$opex_disc['class']}}">{{numberConverter($opex_disc['value'],2,'post','%')}}</td>
                      @endforeach
                      @endif
                  </tr>
                  <tr class="{{$country_name}} expandable" style="display: none; background-color: #FFDB89;">
                      <td>Ebida</td>
                      <td>{{numberConverter($reconcile['ebida']['total'],2,'pre')}}</td>

                      @if(isset($reconcile['ebida']['dates']) && !empty($reconcile['ebida']['dates']))
                      @foreach ($reconcile['ebida']['dates'] as $ebida)
                        <td class="{{$ebida['class']}}">{{numberConverter($ebida['value'],2,'pre')}}</td>
                      @endforeach
                      @endif
                  </tr>

                  <tr class="{{$country_name}} expandable" style="display: none; background-color: #FFDB89;">
                      <td>Target Ebida</td>
                      <td class=""><?=($reconcile['target_ebida']['total'] != 0) ? numberConverter($reconcile['target_ebida']['total'],2,'pre') :
                      'N/A' ?></td>

                      @if(isset($reconcile['target_ebida']['dates']) && !empty($reconcile['target_ebida']['dates']))
                      @foreach ($reconcile['target_ebida']['dates'] as $target_ebida)
                        <td class=""><?=($target_ebida['value'] != 0) ? numberConverter($target_ebida['value'],2,'pre') : 'N/A' ?></td>
                      @endforeach
                      @endif
                  </tr>

                  <tr class="{{$country_name}} expandable" style="display: none; background-color: #FFDB89;">
                      <td>Ebida Discrepency</td>
                      <td>{{numberConverter($reconcile['ebida_disc']['total'],2,'post','%')}}</td>

                      @if(isset($reconcile['ebida_disc']['dates']) && !empty($reconcile['ebida_disc']['dates']))
                      @foreach ($reconcile['ebida_disc']['dates'] as $ebida_disc)
                        <td class="{{$ebida_disc['class']}}">{{numberConverter($ebida_disc['value'],2,'post','%')}}</td>
                      @endforeach
                      @endif
                  </tr>

                  @endforeach
                  @endif

                  @php $i++; @endphp
                  @endforeach

                  @endif


              </tbody>
          </table>
      </div>


</div>
@endsection
