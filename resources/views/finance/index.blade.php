@extends('layouts.admin')

@section('title')
  {{ __('Revenue Reconcile') }}
@endsection

@section('content')

@php
  $popup = '<a href="javascript:void(0)" data-url="/finance/popup" data-size="lg" data-ajax-popup="true" data-title="Finance Input Data">N/A</a>';
@endphp

<div class="" style="margin-top:25px">
  <div class="page-title" style="margin-bottom:25px">
    <div class="row justify-content-between align-items-center">
      <div class="col-xl-4 col-lg-4 col-md-4 d-flex align-items-center justify-content-between justify-content-md-start mb-3 mb-md-0">
        <div class="d-inline-block">
          <div style="white-space:nowrap;">Reconcilation Monthwise Revenue By Operator</div>
        </div>
      </div>
      <div class="col-xl-8 col-lg-8 col-md-8 d-flex align-items-center justify-content-between justify-content-md-end">
      </div>
    </div>
  </div>

  @include('finance.partials.financeFilter')

  <div id="container">
    <div class="table-responsive shadow-sm mb-4 tableFixHead revenue-reconcile">
      <table class="table table-light table-borderd m-0 font-13 table-text-no-wrap" id="adsTbl">
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
            <td class="align-middle text-center first-col font-weight-bold" rowspan="9" style="width: 15%; background-color: white !important;">All Operator (USD)</td>
            <td>End User Revenue</td>
            <td>{{numberConverter($allsummaryData['dlr']['total'],2,'pre')}}</td>

            @if(isset($allsummaryData['dlr']['dates']) && !empty($allsummaryData['dlr']['dates']))
            @foreach ($allsummaryData['dlr']['dates'] as $dlr)
            <td class="{{$dlr['class']}}">{{numberConverter($dlr['value'],2,'pre')}}</td>
            @endforeach
            @endif
          </tr>
          <tr style="background-color: #B4D4FF;">
            <td>Finance end user revenue</td>
            <td><?=($allsummaryData['fir_usd']['total'] != 0) ? numberConverter($allsummaryData['fir_usd']['total'],2,'pre') : $popup ?></td>

            @if(isset($allsummaryData['fir_usd']['dates']) && !empty($allsummaryData['fir_usd']['dates']))
            @foreach ($allsummaryData['fir_usd']['dates'] as $fir_usd)
            <td><?=($fir_usd['value'] != 0) ? numberConverter($fir_usd['value'],2,'pre') : $popup ?></td>
            @endforeach
            @endif
          </tr>
          <tr style="background-color: #B4D4FF;">
            <td>Discrepency</td>
            <td>{{ numberConverter($allsummaryData['discrepency']['total'],2,'post','%')}}</td>

            @if(isset($allsummaryData['discrepency']['dates']) && !empty($allsummaryData['discrepency']['dates']))
            @foreach ($allsummaryData['discrepency']['dates'] as $discrepency)
            <td class="{{$discrepency['class']}}">{{numberConverter($discrepency['value'],2,'post','%')}}</td>
            @endforeach
            @endif
          </tr>
          <tr style="background-color: #96EFFF;">
            <td>Gross Revenue</td>
            <td>{{ numberConverter($allsummaryData['dlr_after_telco']['total'],2,'pre')}}</td>

            @if(isset($allsummaryData['dlr_after_telco']['dates']) && !empty($allsummaryData['dlr_after_telco']['dates']))
            @foreach ($allsummaryData['dlr_after_telco']['dates'] as $dlr_after_telco)
            <td class="{{$dlr_after_telco['class']}}">{{numberConverter($dlr_after_telco['value'],2,'pre')}}</td>
            @endforeach
            @endif
          </tr>
          <tr style="background-color: #96EFFF;">
            <td>Finance gross revenue</td>
            <td><?=($allsummaryData['fir_after_telco_usd']['total'] != 0) ? numberConverter($allsummaryData['fir_after_telco_usd']['total'],2,'pre') : 
            $popup ?></td>

            @if(isset($allsummaryData['fir_after_telco_usd']['dates']) && !empty($allsummaryData['fir_after_telco_usd']['dates']))
            @foreach ($allsummaryData['fir_after_telco_usd']['dates'] as $fir_after_telco_usd)
            <td><?=($fir_after_telco_usd['value'] != 0) ? numberConverter($fir_after_telco_usd['value'],2,'pre') : $popup ?></td>
            @endforeach
            @endif
          </tr>
          <tr style="background-color: #96EFFF;">
            <td>Discrepency</td>
            <td>{{numberConverter($allsummaryData['discrepency_after_telco']['total'],2,'post','%')}}</td>

            @if(isset($allsummaryData['discrepency_after_telco']['dates']) && !empty($allsummaryData['discrepency_after_telco']['dates']))
            @foreach ($allsummaryData['discrepency_after_telco']['dates'] as $discrepency_after_telco)
            <td class="{{$discrepency_after_telco['class']}}">{{numberConverter($discrepency_after_telco['value'],2,'post','%')}}</td>
            @endforeach
            @endif
          </tr>
          <tr style="background-color: #FF9130;">
            <td>Net revenue</td>
            <td>{{ numberConverter($allsummaryData['net_revenue_usd']['total'],2,'pre')}}</td>

            @if(isset($allsummaryData['net_revenue_usd']['dates']) && !empty($allsummaryData['net_revenue_usd']['dates']))
            @foreach ($allsummaryData['net_revenue_usd']['dates'] as $net_revenue_usd)
            <td class="{{$net_revenue_usd['class']}}">{{numberConverter($net_revenue_usd['value'],2,'pre')}}</td>
            @endforeach
            @endif
          </tr>
          <tr style="background-color: #FF9130;">
            <td>Finance net revenue</td>
            <td><?=($allsummaryData['fir_net_revenue_usd']['total'] != 0) ? numberConverter($allsummaryData['fir_net_revenue_usd']['total'],2,'pre') : $popup ?></td>

            @if(isset($allsummaryData['fir_net_revenue_usd']['dates']) && !empty($allsummaryData['fir_net_revenue_usd']['dates']))
            @foreach ($allsummaryData['fir_net_revenue_usd']['dates'] as $fir_net_revenue_usd)
            <td><?=($fir_net_revenue_usd['value'] != 0) ? numberConverter($fir_net_revenue_usd['value'],2,'pre') : $popup ?></td>
            @endforeach
            @endif
          </tr>
          <tr style="background-color: #FF9130;">
            <td>Discrepency</td>
            <td>{{numberConverter($allsummaryData['discrepency_net_revenue']['total'],2,'post','%')}}</td>

            @if(isset($allsummaryData['discrepency_net_revenue']['dates']) && !empty($allsummaryData['discrepency_net_revenue']['dates']))
            @foreach ($allsummaryData['discrepency_net_revenue']['dates'] as $discrepency_net_revenue)
            <td class="{{$discrepency_net_revenue['class']}}">{{numberConverter($discrepency_net_revenue['value'],2,'post','%')}}</td>
            @endforeach
            @endif
          </tr>
          @endif
        </tbody>
        <tbody><tr><td style="height: 20px;"></td></tr></tbody>
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
        @if(isset($countryWiseData) && !empty($countryWiseData))
        <!-- @php $i = 0; @endphp -->
        @foreach ($countryWiseData as $key => $reconcileData)
        
        @php $country_name = str_replace(" ","_",$reconcileData['country']['country']); @endphp
        <tbody class="reconcileData" id="{{$country_name}}_reconcile">
          <tr style="background-color: #B4D4FF;">
            <td class="align-middle text-center font-weight-bold first-col <?php echo ($i % 2 == 0) ? 'country-odd-bg' : 'bg-soft-neutral' ?>" rowspan="9">
              <span class="opbtn" data-param="{{$country_name}}" style="cursor:pointer; min-width:10px; font-size:20px;"><strong>+</strong></span>&nbsp;&nbsp;<img src="{{ asset('/flags/'.$reconcileData['country']['flag']) }}" height="20" width="30">&nbsp;{{$reconcileData['country']['country']}} ({{($reconcileData['country']['currency_code'] == '$') ? 'USD' : $reconcileData['country']['currency_code']}})</td>
            <td class="font-weight-bold">End User Revenue</td>
            <td class="revenue_total font-weight-bold">{{numberConverter($reconcileData['countrySum']['dlr']['total'],2,'pre')}}</td>

            @if(isset($reconcileData['countrySum']['dlr']['dates']) && !empty($reconcileData['countrySum']['dlr']['dates']))
            @foreach ($reconcileData['countrySum']['dlr']['dates'] as $dlr)
            <td class="font-weight-bold">{{numberConverter($dlr['value'],2,'pre')}}</td>
            @endforeach
            @endif
          </tr>
          <tr style="background-color: #B4D4FF;">
            <td class="font-weight-bold">Finance end user revenue</td>
            <td><?=($reconcileData['countrySum']['fir']['total'] != 0) ? numberConverter($reconcileData['countrySum']['fir']['total'],2,'pre') : 
            $popup ?></td>

            @if(isset($reconcileData['countrySum']['fir']['dates']) && !empty($reconcileData['countrySum']['fir']['dates']))
            @foreach ($reconcileData['countrySum']['fir']['dates'] as $fir)
            <td><?=($fir['value'] != 0) ? numberConverter($fir['value'],2,'pre') : $popup ?></td>
            @endforeach
            @endif
          </tr>
          <tr style="background-color: #B4D4FF;">
            <td class="font-weight-bold">Discrepency</td>
            <td class="font-weight-bold">{{numberConverter($reconcileData['countrySum']['discrepency']['total'],2,'post','%')}}</td>

            @if(isset($reconcileData['countrySum']['discrepency']['dates']) && !empty($reconcileData['countrySum']['discrepency']['dates']))
            @foreach ($reconcileData['countrySum']['discrepency']['dates'] as $discrepency)
            <td class="font-weight-bold {{$discrepency['class']}}">{{numberConverter($discrepency['value'],2,'post','%')}}</td>
            @endforeach
            @endif
          </tr>
          <tr style="background-color: #96EFFF;">
            <td class="font-weight-bold">Gross Revenue</td>
            <td class="share_total font-weight-bold">{{numberConverter($reconcileData['countrySum']['dlr_after_telco']['total'],2,'pre')}}</td>

            @if(isset($reconcileData['countrySum']['dlr_after_telco']['dates']) && !empty($reconcileData['countrySum']['dlr_after_telco']['dates']))
            @foreach ($reconcileData['countrySum']['dlr_after_telco']['dates'] as $dlr_after_telco)
            <td class="font-weight-bold">{{numberConverter($dlr_after_telco['value'],2,'pre')}}</td>
            @endforeach
            @endif
          </tr>
          <tr style="background-color: #96EFFF;">
            <td class="font-weight-bold">Finance gross revenue</td>
            <td><?=($reconcileData['countrySum']['fir_after_telco']['total'] != 0) ? numberConverter($reconcileData['countrySum']['fir_after_telco']['total'],2,'pre') : 
            $popup ?></td>

            @if(isset($reconcileData['countrySum']['fir_after_telco']['dates']) && !empty($reconcileData['countrySum']['fir_after_telco']['dates']))
            @foreach ($reconcileData['countrySum']['fir_after_telco']['dates'] as $fir_after_telco)
            <td><?=($fir_after_telco['value'] != 0) ? numberConverter($fir_after_telco['value'],2,'pre') : $popup ?></td>
            @endforeach
            @endif
          </tr>
          <tr style="background-color: #96EFFF;">
            <td class="font-weight-bold">Discrepency</td>
            <td class="font-weight-bold">{{numberConverter($reconcileData['countrySum']['discrepency_after_telco']['total'],2,'post','%')}}</td>
            
            @if(isset($reconcileData['countrySum']['discrepency_after_telco']['dates']) && !empty($reconcileData['countrySum']['discrepency_after_telco']['dates']))
            @foreach ($reconcileData['countrySum']['discrepency_after_telco']['dates'] as $discrepency_after_telco)
            <td class="font-weight-bold {{$discrepency_after_telco['class']}}">{{numberConverter($discrepency_after_telco['value'],2,'post','%')}}</td>
            @endforeach
            @endif
          </tr>
          <tr style="background-color: #FF9130;">
            <td class="font-weight-bold">Net revenue</td>
            <td class="net_total font-weight-bold">{{numberConverter($reconcileData['countrySum']['net_revenue']['total'],2,'pre')}}</td>

            @if(isset($reconcileData['countrySum']['net_revenue']['dates']) && !empty($reconcileData['countrySum']['net_revenue']['dates']))
            @foreach ($reconcileData['countrySum']['net_revenue']['dates'] as $net_revenue)
            <td class="font-weight-bold">{{numberConverter($net_revenue['value'],2,'pre')}}</td>
            @endforeach
            @endif
          </tr>
          <tr style="background-color: #FF9130;">
            <td class="font-weight-bold">Finance net revenue</td>
            <td><?=($reconcileData['countrySum']['fir_net_revenue']['total'] != 0) ? numberConverter($reconcileData['countrySum']['fir_net_revenue']['total'],2,'pre') : 
            $popup ?></td>

            @if(isset($reconcileData['countrySum']['fir_net_revenue']['dates']) && !empty($reconcileData['countrySum']['fir_net_revenue']['dates']))
            @foreach ($reconcileData['countrySum']['fir_net_revenue']['dates'] as $fir_net_revenue)
            <td><?=($fir_net_revenue['value'] != 0) ? numberConverter($fir_net_revenue['value'],2,'pre') : $popup ?></td>
            @endforeach
            @endif
          </tr>
          <tr style="background-color: #FF9130;">
            <td class="font-weight-bold">Discrepency</td>
            <td class="font-weight-bold">{{numberConverter($reconcileData['countrySum']['discrepency_net_revenue']['total'],2,'post','%')}}</td>
            
            @if(isset($reconcileData['countrySum']['discrepency_net_revenue']['dates']) && !empty($reconcileData['countrySum']['discrepency_net_revenue']['dates']))
            @foreach ($reconcileData['countrySum']['discrepency_net_revenue']['dates'] as $discrepency_net_revenue)
            <td class="font-weight-bold {{$discrepency_net_revenue['class']}}">{{numberConverter($discrepency_net_revenue['value'],2,'post','%')}}</td>
            @endforeach
            @endif
          </tr>
                  
          @if(isset($reconcileData['operator']) && !empty($reconcileData['operator']))
          @foreach ($reconcileData['operator'] as $rec_key=>$reconcile)

          <tr class="{{$country_name}} expandable" style="display: none;">
            <td colspan="{{count($no_of_days)+3}}">
              <table style="width: 100%; margin-bottom: 0px !important;">
                <tbody>
                  <tr style="background-color: #B4D4FF;">
                    <td class="align-middle text-center first-col <?php echo ($rec_key % 2 == 0) ? 'operator-odd-bg' : 'bg-soft-neutral' ?>" rowspan="10" style="width: 15%;">
                      <span class="ml-4"><a href="javascript:void(0)" class="bg-info" data-url="{{ URL::to('finance/serviceReconcileData/'.$reconcile['operator']['id_operator']) }}"  data-size="lg" data-ajax-popup="true" data-toggle="tooltip" data-original-title="View All service data" data-title="{{__('View All service data #'.$reconcile['operator']['operator_name'])}}">{{ $reconcile['operator']['operator_name'] }}</a><div>(Merchant Share = {{ numberConverter($reconcile['operator']['share'],2,'post','%') }}</div><div>Total Tax = {{ $reconcile['operator']['total_tax'] }})</div></span></td>
                    <td>End User Revenue</td>
                    <td>{{ numberConverter($reconcile['dlr']['total'],2,'pre') }}</td>

                    @if(isset($reconcile['dlr']['dates']) && !empty($reconcile['dlr']['dates']))
                    @foreach ($reconcile['dlr']['dates'] as $dlr)
                    <td class="{{$dlr['class']}}">{{numberConverter($dlr['value'],2,'pre')}}</td>
                    @endforeach
                    @endif
                  </tr>
                  <tr style="background-color: #B4D4FF;">
                    <td>Finance end user revenue</td>
                    <td><?=($reconcile['fir']['total'] != 0) ? numberConverter($reconcile['fir']['total'],2,'pre') : 
                    $popup ?></td>

                    @if(isset($reconcile['fir']['dates']) && !empty($reconcile['fir']['dates']))
                    @foreach ($reconcile['fir']['dates'] as $fir)
                    <td><?=($fir['value'] != 0) ? numberConverter($fir['value'],2,'pre') : $popup ?></td>
                    @endforeach
                    @endif
                  </tr>
                  <tr style="background-color: #B4D4FF;">
                    <td>Discrepency</td>
                    <td>{{numberConverter($reconcile['discrepency']['total'],2,'post','%')}}</td>

                    @if(isset($reconcile['discrepency']['dates']) && !empty($reconcile['discrepency']['dates']))
                    @foreach ($reconcile['discrepency']['dates'] as $discrepency)
                    <td class="{{$discrepency['class']}}">{{numberConverter($discrepency['value'],2,'post','%')}}</td>
                    @endforeach
                    @endif
                  </tr>
                  <tr style="background-color: #96EFFF;">
                    <td>Gross Revenue</td>
                    <td class="gros">{{numberConverter($reconcile['dlr_after_telco']['total'],2,'pre')}}</td>

                    @if(isset($reconcile['dlr_after_telco']['dates']) && !empty($reconcile['dlr_after_telco']['dates']))
                    @foreach ($reconcile['dlr_after_telco']['dates'] as $dlr_after_telco)
                    <td class="{{$dlr_after_telco['class']}}">{{numberConverter($dlr_after_telco['value'],2,'pre')}}</td>
                    @endforeach
                    @endif
                  </tr>
                  <tr style="background-color: #96EFFF;">
                    <td>Finance gross revenue</td>
                    <td><?=($reconcile['fir_after_telco']['total'] != 0) ? numberConverter($reconcile['fir_after_telco']['total'],2,'post','%') : 
                    $popup ?></td>

                    @if(isset($reconcile['fir_after_telco']['dates']) && !empty($reconcile['fir_after_telco']['dates']))
                    @foreach ($reconcile['fir_after_telco']['dates'] as $fir_after_telco)
                    <td><?=($fir_after_telco['value'] != 0) ? numberConverter($fir_after_telco['value'],2,'pre') : $popup ?></td>
                    @endforeach
                    @endif
                  </tr>
                  <tr style="background-color: #96EFFF;">
                    <td>Discrepency</td>
                    <td>{{ numberConverter($reconcile['discrepency_after_telco']['total'],2,'post','%') }}</td>

                    @if(isset($reconcile['discrepency_after_telco']['dates']) && !empty($reconcile['discrepency_after_telco']['dates']))
                    @foreach ($reconcile['discrepency_after_telco']['dates'] as $discrepency_after_telco)
                    <td class="{{$discrepency_after_telco['class']}}">{{numberConverter($discrepency_after_telco['value'],2,'post','%')}}</td>
                    @endforeach
                    @endif
                  </tr>
                  <tr style="background-color: #FF9130;">
                    <td>Net revenue</td>
                    <td class="nets">{{numberConverter($reconcile['net_revenue']['total'],2,'pre')}}</td>

                    @if(isset($reconcile['net_revenue']['dates']) && !empty($reconcile['net_revenue']['dates']))
                    @foreach ($reconcile['net_revenue']['dates'] as $net_revenue)
                    <td class="{{$net_revenue['class']}}">{{numberConverter($net_revenue['value'],2,'pre')}}</td>
                    @endforeach
                    @endif
                  </tr>
                  <tr style="background-color: #FF9130;">
                    <td>Finance net revenue</td>
                    <td><?=($reconcile['fir_net_revenue']['total'] != 0) ? numberConverter($reconcile['fir_net_revenue']['total'],2,'post','%') : 
                    $popup ?></td>

                    @if(isset($reconcile['fir_net_revenue']['dates']) && !empty($reconcile['fir_net_revenue']['dates']))
                    @foreach ($reconcile['fir_net_revenue']['dates'] as $fir_net_revenue)
                    <td><?=($fir_net_revenue['value'] != 0) ? numberConverter($fir_net_revenue['value'],2,'pre') : $popup ?></td>
                    @endforeach
                    @endif
                  </tr>
                  <tr style="background-color: #FF9130;">
                    <td>Discrepency</td>
                    <td>{{ numberConverter($reconcile['discrepency_net_revenue']['total'],2,'post','%') }}</td>

                    @if(isset($reconcile['discrepency_net_revenue']['dates']) && !empty($reconcile['discrepency_net_revenue']['dates']))
                    @foreach ($reconcile['discrepency_net_revenue']['dates'] as $discrepency_net_revenue)
                    <td class="{{$discrepency_net_revenue['class']}}">{{numberConverter($discrepency_net_revenue['value'],2,'post','%')}}</td>
                    @endforeach
                    @endif
                  </tr>
                  <tr>
                    <td>Finance reconcile file</td>
                    <td>N/A</td>

                    @if(isset($reconcile['file']['dates']) && !empty($reconcile['file']['dates']))
                    @foreach ($reconcile['file']['dates'] as $file)
                    @php if(isset($file['value'])){ $filename = $file['value'];}else{ $filename = test.txt; } @endphp
                    <td><a href="javascript:void(0)" class="btn btn-sm btn-default download-reconcile" data-file="{{ $filename }}" title="Download Reconcile File"><i class="fa fa-download"></i></a></td>
                    @endforeach
                    @endif
                  </tr>
                </tbody>
              </table>
            </td>
          </tr>
          @endforeach
          @endif
        </tbody>
        <!-- @php $i++; @endphp -->
        @endforeach
        @endif         
      </table>
    </div>
  </div>
</div>
<script type="text/javascript">
  var baseurl = window.location.origin

  $('.download-reconcile').on('click',function(){
    var file = $(this).data('file')
    window.location.href = baseurl+'/finance/downloadFile?file='+file
  })
</script>
@endsection