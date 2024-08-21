@extends('layouts.admin')

@section('title')
  {{ __('Final Cost Report') }}
@endsection

@section('content')
<div class="" style="margin-top:25px">
  <div class="page-title" style="margin-bottom:25px">
    <div class="row justify-content-between align-items-center">
      <div class="col-xl-4 col-lg-4 col-md-4 d-flex align-items-center justify-content-between justify-content-md-start mb-3 mb-md-0">
        <div class="d-inline-block">
          <div style="white-space:nowrap;">Final Cost Report Monthwise Revenue By Operator</div>
        </div>
      </div>
      <div class="col-xl-8 col-lg-8 col-md-8 d-flex align-items-center justify-content-between justify-content-md-end">
      </div>
    </div>
  </div>

  @include('finance.partials.financeFinalCostFilter')

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
          <td>Cost Campaign</td>
          <td>{{numberConverter($allsummaryData['cost_campaign']['total'],2,'pre')}}</td>

          @if(isset($allsummaryData['cost_campaign']['dates']) && !empty($allsummaryData['cost_campaign']['dates']))
          @foreach ($allsummaryData['cost_campaign']['dates'] as $cost_campaign)
          <td class="{{$cost_campaign['class']}}">{{numberConverter($cost_campaign['value'],2,'pre')}}</td>
          @endforeach
          @endif
        </tr>
        <tr style="background-color: #B4D4FF;">
          <td>Finance Input Cost Campaign</td>
          <td class=""><?=($allsummaryData['final_input_cost_camp']['total'] != 0) ? numberConverter($allsummaryData['final_input_cost_camp']['total'],2,'pre') : 'N/A' ?></td>

          @if(isset($allsummaryData['final_input_cost_camp']['dates']) && !empty($allsummaryData['final_input_cost_camp']['dates']))
          @foreach ($allsummaryData['final_input_cost_camp']['dates'] as $final_input_cost_camp)
          <td class=""><?=($final_input_cost_camp['value'] != 0) ? numberConverter($final_input_cost_camp['value'],2,'pre') : 'N/A' ?></td>
          @endforeach
          @endif
        </tr>
        <tr style="background-color: #B4D4FF;">
          <td>Cost Campaign Discrepency</td>
          <td>{{numberConverter($allsummaryData['cost_campaign_disc']['total'],2,'post','%')}}</td>

          @if(isset($allsummaryData['cost_campaign_disc']['dates']) && !empty($allsummaryData['cost_campaign_disc']['dates']))
          @foreach ($allsummaryData['cost_campaign_disc']['dates'] as $cost_campaign_disc)
          <td class="{{$cost_campaign_disc['class']}}">{{numberConverter($cost_campaign_disc['value'],2,'post','%')}}</td>
          @endforeach
          @endif
        </tr>

        <tr style="background-color: #96EFFF;">
          <td>Cost RnD</td>
          <td>{{numberConverter($allsummaryData['cost_rnd']['total'],2,'pre')}}</td>

          @if(isset($allsummaryData['cost_rnd']['dates']) && !empty($allsummaryData['cost_rnd']['dates']))
          @foreach ($allsummaryData['cost_rnd']['dates'] as $cost_rnd)
          <td class="{{$cost_rnd['class']}}">{{numberConverter($cost_rnd['value'],2,'pre')}}</td>
          @endforeach
          @endif
        </tr>
        <tr style="background-color: #96EFFF;">
          <td>Finance Input Cost RnD</td>
          <td class=""><?=($allsummaryData['input_cost_rnd']['total'] != 0) ? numberConverter($allsummaryData['input_cost_rnd']['total'],2,'pre') : 'N/A' ?></td>

          @if(isset($allsummaryData['input_cost_rnd']['dates']) && !empty($allsummaryData['input_cost_rnd']['dates']))
          @foreach ($allsummaryData['input_cost_rnd']['dates'] as $input_cost_rnd)
          <td class=""><?=($input_cost_rnd['value'] != 0) ? numberConverter($input_cost_rnd['value'],2,'pre') : 'N/A' ?></td>
          @endforeach
          @endif
        </tr>
        <tr style="background-color: #96EFFF;">
          <td>Cost RnD Discrepency</td>
          <td>{{numberConverter($allsummaryData['cost_rnd_disc']['total'],2,'post','%')}}</td>

          @if(isset($allsummaryData['cost_rnd_disc']['dates']) && !empty($allsummaryData['cost_rnd_disc']['dates']))
          @foreach ($allsummaryData['cost_rnd_disc']['dates'] as $cost_rnd_disc)
          <td class="{{$cost_rnd_disc['class']}}">{{numberConverter($cost_rnd_disc['value'],2,'post','%')}}</td>
          @endforeach
          @endif
        </tr>

        <tr style="background-color: #FF9130;">
          <td>Cost App Content</td>
          <td>{{numberConverter($allsummaryData['app_content']['total'],2,'pre')}}</td>

          @if(isset($allsummaryData['app_content']['dates']) && !empty($allsummaryData['app_content']['dates']))
          @foreach ($allsummaryData['app_content']['dates'] as $app_content)
          <td class="{{$app_content['class']}}">{{numberConverter($app_content['value'],2,'pre')}}</td>
          @endforeach
          @endif
        </tr>
        <tr style="background-color: #FF9130;">
          <td>Finance Input App Content</td>
          <td class=""><?=($allsummaryData['input_app_content']['total'] != 0) ? numberConverter($allsummaryData['input_app_content']['total'],2,'pre') : 'N/A' ?></td>

          @if(isset($allsummaryData['input_app_content']['dates']) && !empty($allsummaryData['input_app_content']['dates']))
          @foreach ($allsummaryData['input_app_content']['dates'] as $input_app_content)
          <td class=""><?=($input_app_content['value'] != 0) ? numberConverter($input_app_content['value'],2,'pre') : 'N/A' ?></td>
          @endforeach
          @endif
        </tr>
        <tr style="background-color: #FF9130;">
          <td>Cost App Content Discrepency</td>
          <td>{{numberConverter($allsummaryData['app_content_disc']['total'],2,'post','%')}}</td>

          @if(isset($allsummaryData['app_content_disc']['dates']) && !empty($allsummaryData['app_content_disc']['dates']))
          @foreach ($allsummaryData['app_content_disc']['dates'] as $app_content_disc)
          <td class="{{$app_content_disc['class']}}">{{numberConverter($app_content_disc['value'],2,'post','%')}}</td>
          @endforeach
          @endif
        </tr>

        <tr style="background-color: #DFCCFB;">
          <td>Cost Fun Busket</td>
          <td>{{numberConverter($allsummaryData['fun_busket']['total'],2,'pre')}}</td>

          @if(isset($allsummaryData['fun_busket']['dates']) && !empty($allsummaryData['fun_busket']['dates']))
          @foreach ($allsummaryData['fun_busket']['dates'] as $fun_busket)
          <td class="{{$fun_busket['class']}}">{{numberConverter($fun_busket['value'],2,'pre')}}</td>
          @endforeach
          @endif
        </tr>
        <tr style="background-color: #DFCCFB;">
          <td>Finance Input Fun Busket</td>
          <td class=""><?=($allsummaryData['input_fun_busket']['total'] != 0) ? numberConverter($allsummaryData['input_fun_busket']['total'],2,'pre') : 'N/A' ?></td>

          @if(isset($allsummaryData['input_fun_busket']['dates']) && !empty($allsummaryData['input_fun_busket']['dates']))
          @foreach ($allsummaryData['input_fun_busket']['dates'] as $input_fun_busket)
          <td class=""><?=($input_fun_busket['value'] != 0) ? numberConverter($input_fun_busket['value'],2,'pre') : 'N/A' ?></td>
          @endforeach
          @endif
        </tr>
        <tr style="background-color: #DFCCFB;">
          <td>Cost Fun Busket Discrepency</td>
          <td>{{numberConverter($allsummaryData['fun_busket_disc']['total'],2,'post','%')}}</td>

          @if(isset($allsummaryData['fun_busket_disc']['dates']) && !empty($allsummaryData['fun_busket_disc']['dates']))
          @foreach ($allsummaryData['fun_busket_disc']['dates'] as $fun_busket_disc)
          <td class="{{$fun_busket_disc['class']}}">{{numberConverter($fun_busket_disc['value'],2,'post','%')}}</td>
          @endforeach
          @endif
        </tr>

        <tr style="background-color: #FFDB89;">
          <td>Cost BD</td>
          <td>{{numberConverter($allsummaryData['cost_bd']['total'],2,'pre')}}</td>

          @if(isset($allsummaryData['cost_bd']['dates']) && !empty($allsummaryData['cost_bd']['dates']))
          @foreach ($allsummaryData['cost_bd']['dates'] as $cost_bd)
          <td class="{{$cost_bd['class']}}">{{numberConverter($cost_bd['value'],2,'pre')}}</td>
          @endforeach
          @endif
        </tr>
        <tr style="background-color: #FFDB89;">
          <td>Finance Input BD</td>
          <td class=""><?=($allsummaryData['input_cost_bd']['total'] != 0) ? numberConverter($allsummaryData['input_cost_bd']['total'],2,'pre') : 'N/A' ?></td>

          @if(isset($allsummaryData['input_cost_bd']['dates']) && !empty($allsummaryData['input_cost_bd']['dates']))
          @foreach ($allsummaryData['input_cost_bd']['dates'] as $input_cost_bd)
          <td class=""><?=($input_cost_bd['value'] != 0) ? numberConverter($input_cost_bd['value'],2,'pre') : 'N/A' ?></td>
          @endforeach
          @endif
        </tr>
        <tr style="background-color: #FFDB89;">
          <td>Cost BD Discrepency</td>
          <td>{{numberConverter($allsummaryData['cost_bd_disc']['total'],2,'post','%')}}</td>

          @if(isset($allsummaryData['cost_bd_disc']['dates']) && !empty($allsummaryData['cost_bd_disc']['dates']))
          @foreach ($allsummaryData['cost_bd_disc']['dates'] as $cost_bd_disc)
          <td class="{{$cost_bd_disc['class']}}">{{numberConverter($cost_bd_disc['value'],2,'post','%')}}</td>
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
        @foreach ($countryWiseData as $key => $finalCostReport)
        @php $country_name = str_replace(" ","_",$finalCostReport['country']['country']); @endphp
          
        <tr style="background-color: #B4D4FF;">
          <td class="align-middle text-center font-weight-bold first-col <?php echo ($i % 2 == 0) ? 'country-odd-bg' : 'bg-soft-neutral' ?>" rowspan="15"><span class="opbtn" data-param="{{$country_name}}" style="cursor:pointer; min-width:10px; font-size:20px;"><strong>+</strong></span>&nbsp;&nbsp;<img src="{{ asset('/flags/'.$finalCostReport['country']['flag']) }}" height="20" width="30">&nbsp;{{$finalCostReport['country']['country']}} ({{($finalCostReport['country']['currency_code'] == '$') ? 'USD' : $finalCostReport['country']['currency_code']}})</td>
          <td class="font-weight-bold">Cost Campaign</td>
          <td class="font-weight-bold">{{numberConverter($finalCostReport['countrySum']['cost_campaign']['total'],2,'pre')}}</td>

          @if(isset($finalCostReport['countrySum']['cost_campaign']['dates']) && !empty($finalCostReport['countrySum']['cost_campaign']['dates']))
          @foreach ($finalCostReport['countrySum']['cost_campaign']['dates'] as $cost_campaign)
          <td class="font-weight-bold">{{numberConverter($cost_campaign['value'],2,'pre')}}</td>
          @endforeach
          @endif
        </tr>
        <tr style="background-color: #B4D4FF;">
          <td class="font-weight-bold">Finance Input Cost Campaign</td>
          <td class=""><?=($finalCostReport['countrySum']['final_input_cost_camp']['total'] != 0) ? numberConverter($finalCostReport['countrySum']['final_input_cost_camp']['total'],2,'pre') : 'N/A' ?></td>

          @if(isset($finalCostReport['countrySum']['final_input_cost_camp']['dates']) && !empty($finalCostReport['countrySum']['final_input_cost_camp']['dates']))
          @foreach ($finalCostReport['countrySum']['final_input_cost_camp']['dates'] as $final_input_cost_camp)
          <td class=""><?=($final_input_cost_camp['value'] != 0) ? numberConverter($final_input_cost_camp['value'],2,'pre') : 'N/A' ?></td>
          @endforeach
          @endif
        </tr>
        <tr style="background-color: #B4D4FF;">
          <td class="font-weight-bold">Cost Campaign Discrepency</td>
          <td class="font-weight-bold">{{numberConverter($finalCostReport['countrySum']['cost_campaign_disc']['total'],2,'post','%')}}</td>

          @if(isset($finalCostReport['countrySum']['cost_campaign_disc']['dates']) && !empty($finalCostReport['countrySum']['cost_campaign_disc']['dates']))
          @foreach ($finalCostReport['countrySum']['cost_campaign_disc']['dates'] as $cost_campaign_disc)
          <td class="font-weight-bold">{{numberConverter($cost_campaign_disc['value'],2,'post','%')}}</td>
          @endforeach
          @endif
        </tr>

        <tr style="background-color: #96EFFF;">
          <td class="font-weight-bold">Cost RnD</td>
          <td class="font-weight-bold">{{numberConverter($finalCostReport['countrySum']['cost_rnd']['total'],2,'pre')}}</td>

          @if(isset($finalCostReport['countrySum']['cost_rnd']['dates']) && !empty($finalCostReport['countrySum']['cost_rnd']['dates']))
          @foreach ($finalCostReport['countrySum']['cost_rnd']['dates'] as $cost_rnd)
          <td class="font-weight-bold">{{numberConverter($cost_rnd['value'],2,'pre')}}</td>
          @endforeach
          @endif
        </tr>
        <tr style="background-color: #96EFFF;">
          <td class="font-weight-bold">Finance Input Cost RnD</td>
          <td class=""><?=($finalCostReport['countrySum']['input_cost_rnd']['total'] != 0) ? numberConverter($finalCostReport['countrySum']['input_cost_rnd']['total'],2,'pre') : 'N/A' ?></td>

          @if(isset($finalCostReport['countrySum']['input_cost_rnd']['dates']) && !empty($finalCostReport['countrySum']['input_cost_rnd']['dates']))
          @foreach ($finalCostReport['countrySum']['input_cost_rnd']['dates'] as $input_cost_rnd)
          <td class=""><?=($input_cost_rnd['value'] != 0) ? numberConverter($input_cost_rnd['value'],2,'pre') : 'N/A' ?></td>
          @endforeach
          @endif
        </tr>
        <tr style="background-color: #96EFFF;">
          <td class="font-weight-bold">Cost RnD Discrepency</td>
          <td class="font-weight-bold">{{numberConverter($finalCostReport['countrySum']['cost_rnd_disc']['total'],2,'post','%')}}</td>

          @if(isset($finalCostReport['countrySum']['cost_rnd_disc']['dates']) && !empty($finalCostReport['countrySum']['cost_rnd_disc']['dates']))
          @foreach ($finalCostReport['countrySum']['cost_rnd_disc']['dates'] as $cost_rnd_disc)
          <td class="font-weight-bold">{{numberConverter($cost_rnd_disc['value'],2,'post','%')}}</td>
          @endforeach
          @endif
        </tr>

        <tr style="background-color: #FF9130;">
          <td class="font-weight-bold">Cost App Content</td>
          <td class="font-weight-bold">{{numberConverter($finalCostReport['countrySum']['app_content']['total'],2,'pre')}}</td>

          @if(isset($finalCostReport['countrySum']['app_content']['dates']) && !empty($finalCostReport['countrySum']['app_content']['dates']))
          @foreach ($finalCostReport['countrySum']['app_content']['dates'] as $app_content)
          <td class="font-weight-bold">{{numberConverter($app_content['value'],2,'pre')}}</td>
          @endforeach
          @endif
        </tr>
        <tr style="background-color: #FF9130;">
          <td class="font-weight-bold">Finance Input App Content</td>
          <td class=""><?=($finalCostReport['countrySum']['input_app_content']['total'] != 0) ? numberConverter($finalCostReport['countrySum']['input_app_content']['total'],2,'pre') : 'N/A' ?></td>

          @if(isset($finalCostReport['countrySum']['input_app_content']['dates']) && !empty($finalCostReport['countrySum']['input_app_content']['dates']))
          @foreach ($finalCostReport['countrySum']['input_app_content']['dates'] as $input_app_content)
          <td class=""><?=($input_app_content['value'] != 0) ? numberConverter($input_app_content['value'],2,'pre') : 'N/A' ?></td>
          @endforeach
          @endif
        </tr>
        <tr style="background-color: #FF9130;">
          <td class="font-weight-bold">Cost App Content Discrepency</td>
          <td class="font-weight-bold">{{numberConverter($finalCostReport['countrySum']['app_content_disc']['total'],2,'post','%')}}</td>

          @if(isset($finalCostReport['countrySum']['app_content_disc']['dates']) && !empty($finalCostReport['countrySum']['app_content_disc']['dates']))
          @foreach ($finalCostReport['countrySum']['app_content_disc']['dates'] as $app_content_disc)
          <td class="font-weight-bold">{{numberConverter($app_content_disc['value'],2,'post','%')}}</td>
          @endforeach
          @endif
        </tr>

        <tr style="background-color: #DFCCFB;">
          <td class="font-weight-bold">Cost Fun Busket</td>
          <td class="font-weight-bold">{{numberConverter($finalCostReport['countrySum']['fun_busket']['total'],2,'pre')}}</td>

          @if(isset($finalCostReport['countrySum']['fun_busket']['dates']) && !empty($finalCostReport['countrySum']['fun_busket']['dates']))
          @foreach ($finalCostReport['countrySum']['fun_busket']['dates'] as $fun_busket)
          <td class="font-weight-bold">{{numberConverter($fun_busket['value'],2,'pre')}}</td>
          @endforeach
          @endif
        </tr>
        <tr style="background-color: #DFCCFB;">
          <td class="font-weight-bold">Finance Input Fun Busket</td>
          <td class=""><?=($finalCostReport['countrySum']['input_fun_busket']['total'] != 0) ? numberConverter($finalCostReport['countrySum']['input_fun_busket']['total'],2,'pre') : 'N/A' ?></td>

          @if(isset($finalCostReport['countrySum']['input_fun_busket']['dates']) && !empty($finalCostReport['countrySum']['input_fun_busket']['dates']))
          @foreach ($finalCostReport['countrySum']['input_fun_busket']['dates'] as $input_fun_busket)
          <td class=""><?=($input_fun_busket['value'] != 0) ? numberConverter($input_fun_busket['value'],2,'pre') : 'N/A' ?></td>
          @endforeach
          @endif
        </tr>
        <tr style="background-color: #DFCCFB;">
          <td class="font-weight-bold">Cost Fun Busket Discrepency</td>
          <td class="font-weight-bold">{{numberConverter($finalCostReport['countrySum']['fun_busket_disc']['total'],2,'post','%')}}</td>

          @if(isset($finalCostReport['countrySum']['fun_busket_disc']['dates']) && !empty($finalCostReport['countrySum']['fun_busket_disc']['dates']))
          @foreach ($finalCostReport['countrySum']['fun_busket_disc']['dates'] as $fun_busket_disc)
          <td class="font-weight-bold">{{numberConverter($fun_busket_disc['value'],2,'post','%')}}</td>
          @endforeach
          @endif
        </tr>

        <tr style="background-color: #FFDB89;">
          <td class="font-weight-bold">Cost BD</td>
          <td class="font-weight-bold">{{numberConverter($finalCostReport['countrySum']['cost_bd']['total'],2,'pre')}}</td>

          @if(isset($finalCostReport['countrySum']['cost_bd']['dates']) && !empty($finalCostReport['countrySum']['cost_bd']['dates']))
          @foreach ($finalCostReport['countrySum']['cost_bd']['dates'] as $cost_bd)
          <td class="font-weight-bold">{{numberConverter($cost_bd['value'],2,'pre')}}</td>
          @endforeach
          @endif
        </tr>
        <tr style="background-color: #FFDB89;">
          <td class="font-weight-bold">Finance Input BD</td>
          <td class=""><?=($finalCostReport['countrySum']['input_cost_bd']['total'] != 0) ? numberConverter($finalCostReport['countrySum']['input_cost_bd']['total'],2,'pre') : 'N/A' ?></td>

          @if(isset($finalCostReport['countrySum']['input_cost_bd']['dates']) && !empty($finalCostReport['countrySum']['input_cost_bd']['dates']))
          @foreach ($finalCostReport['countrySum']['input_cost_bd']['dates'] as $input_cost_bd)
          <td class=""><?=($input_cost_bd['value'] != 0) ? numberConverter($input_cost_bd['value'],2,'pre') : 'N/A' ?></td>
          @endforeach
          @endif
        </tr>
        <tr style="background-color: #FFDB89;">
          <td class="font-weight-bold">Cost BD Discrepency</td>
          <td class="font-weight-bold">{{numberConverter($finalCostReport['countrySum']['cost_bd_disc']['total'],2,'post','%')}}</td>

          @if(isset($finalCostReport['countrySum']['cost_bd_disc']['dates']) && !empty($finalCostReport['countrySum']['cost_bd_disc']['dates']))
          @foreach ($finalCostReport['countrySum']['cost_bd_disc']['dates'] as $cost_bd_disc)
          <td class="font-weight-bold">{{numberConverter($cost_bd_disc['value'],2,'post','%')}}</td>
          @endforeach
          @endif
        </tr>
        
        @if(isset($finalCostReport['operator']) && !empty($finalCostReport['operator']))
        @foreach ($finalCostReport['operator'] as $rec_key=>$final_cost)

        <tr class="{{$country_name}} expandable" style="display: none; background-color: #B4D4FF;">
          <td class="align-middle text-center first-col <?php echo ($rec_key % 2 == 0) ? 'operator-odd-bg' : 'bg-soft-neutral' ?>" rowspan="15"><span class="ml-4"><a href="javascript:void(0)" class="bg-info" data-url="{{ URL::to('finance/serviceCostData/'.$final_cost['operator']['id_operator']) }}"  data-size="lg" data-ajax-popup="true" data-toggle="tooltip" data-original-title="View All service data" data-title="{{__('View All service data #'.$final_cost['operator']['operator_name'])}}">{{ $final_cost['operator']['operator_name'] }}</a></span></td>
          <td>Cost Campaign</td>
          <td>{{numberConverter($final_cost['cost_campaign']['total'],2,'pre')}}</td>

          @if(isset($final_cost['cost_campaign']['dates']) && !empty($final_cost['cost_campaign']['dates']))
          @foreach ($final_cost['cost_campaign']['dates'] as $cost_campaign)
            <td class="{{$cost_campaign['class']}}">{{numberConverter($cost_campaign['value'],2,'pre')}}</td>
          @endforeach
          @endif
        </tr>
        <tr class="{{$country_name}} expandable" style="display: none; background-color: #B4D4FF;">
          <td>Finance Input Cost Campaign</td>
          <td class=""><?=($final_cost['final_input_cost_campaign']['total'] != 0) ? numberConverter($final_cost['final_input_cost_campaign']['total'],2,'pre') :  'N/A' ?></td>

          @if(isset($final_cost['final_input_cost_campaign']['dates']) && !empty($final_cost['final_input_cost_campaign']['dates']))
          @foreach ($final_cost['final_input_cost_campaign']['dates'] as $final_input_cost_campaign)
          <td class=""><?=($final_input_cost_campaign['value'] != 0) ? numberConverter($final_input_cost_campaign['value'],2,'pre') : 'N/A' ?></td>
          @endforeach
          @endif
        </tr>
        <tr class="{{$country_name}} expandable" style="display: none; background-color: #B4D4FF;">
          <td>Cost Campaign Discrepency</td>
          <td>{{numberConverter($final_cost['cost_campaign_discrepency']['total'],2,'post','%')}}</td>

          @if(isset($final_cost['cost_campaign_discrepency']['dates']) && !empty($final_cost['cost_campaign_discrepency']['dates']))
          @foreach ($final_cost['cost_campaign_discrepency']['dates'] as $cost_campaign_discrepency)
          <td class="{{$cost_campaign_discrepency['class']}}">{{numberConverter($cost_campaign_discrepency['value'],2,'post','%')}}</td>
          @endforeach
          @endif
        </tr>

        <tr class="{{$country_name}} expandable" style="display: none; background-color: #96EFFF;">
          <td>Cost RnD</td>
          <td>{{numberConverter($final_cost['cost_rnd']['total'],2,'pre')}}</td>

          @if(isset($final_cost['cost_rnd']['dates']) && !empty($final_cost['cost_rnd']['dates']))
          @foreach ($final_cost['cost_rnd']['dates'] as $cost_rnd)
          <td class="{{$cost_rnd['class']}}">{{numberConverter($cost_rnd['value'],2,'pre')}}</td>
          @endforeach
          @endif
        </tr>
        <tr class="{{$country_name}} expandable" style="display: none; background-color: #96EFFF;">
          <td>Finance Input Cost RnD</td>
          <td class=""><?=($final_cost['input_cost_rnd']['total'] != 0) ? numberConverter($final_cost['input_cost_rnd']['total'],2,'pre') : 'N/A' ?></td>

          @if(isset($final_cost['input_cost_rnd']['dates']) && !empty($final_cost['input_cost_rnd']['dates']))
          @foreach ($final_cost['input_cost_rnd']['dates'] as $input_cost_rnd)
          <td class=""><?=($input_cost_rnd['value'] != 0) ? numberConverter($input_cost_rnd['value'],2,'pre') : 'N/A' ?></td>
          @endforeach
          @endif
        </tr>
        <tr class="{{$country_name}} expandable" style="display: none; background-color: #96EFFF;">
          <td>Cost RnD Discrepency</td>
          <td>{{numberConverter($final_cost['cost_rnd_discrepency']['total'],2,'post','%')}}</td>

          @if(isset($final_cost['cost_rnd_discrepency']['dates']) && !empty($final_cost['cost_rnd_discrepency']['dates']))
          @foreach ($final_cost['cost_rnd_discrepency']['dates'] as $cost_rnd_discrepency)
          <td class="{{$cost_rnd_discrepency['class']}}">{{numberConverter($cost_rnd_discrepency['value'],2,'post','%')}}</td>
          @endforeach
          @endif
        </tr>

        <tr class="{{$country_name}} expandable" style="display: none; background-color: #FF9130;">
          <td>Cost App Content</td>
          <td>{{numberConverter($final_cost['app_content']['total'],2,'pre')}}</td>

          @if(isset($final_cost['app_content']['dates']) && !empty($final_cost['app_content']['dates']))
          @foreach ($final_cost['app_content']['dates'] as $app_content)
          <td class="{{$app_content['class']}}">{{numberConverter($app_content['value'],2,'pre')}}</td>
          @endforeach
          @endif
        </tr>
        <tr class="{{$country_name}} expandable" style="display: none; background-color: #FF9130;">
          <td>Finance Input Cost App Content</td>
          <td class=""><?=($final_cost['input_app_content']['total'] != 0) ? numberConverter($final_cost['input_app_content']['total'],2,'pre') : 'N/A' ?></td>

          @if(isset($final_cost['input_app_content']['dates']) && !empty($final_cost['input_app_content']['dates']))
          @foreach ($final_cost['input_app_content']['dates'] as $input_app_content)
          <td class=""><?=($input_app_content['value'] != 0) ? numberConverter($input_app_content['value'],2,'pre') : 'N/A' ?></td>
          @endforeach
          @endif
        </tr>
        <tr class="{{$country_name}} expandable" style="display: none; background-color: #FF9130;">
          <td>Cost App Content Discrepency</td>
          <td>{{numberConverter($final_cost['app_content_discrepency']['total'],2,'post','%')}}</td>

          @if(isset($final_cost['app_content_discrepency']['dates']) && !empty($final_cost['app_content_discrepency']['dates']))
          @foreach ($final_cost['app_content_discrepency']['dates'] as $app_content_discrepency)
          <td class="{{$app_content_discrepency['class']}}">{{numberConverter($app_content_discrepency['value'],2,'post','%')}}</td>
          @endforeach
          @endif
        </tr>

        <tr class="{{$country_name}} expandable" style="display: none; background-color: #DFCCFB;">
          <td>Cost Fun Busket</td>
          <td>{{numberConverter($final_cost['fun_busket']['total'],2,'pre')}}</td>

          @if(isset($final_cost['fun_busket']['dates']) && !empty($final_cost['fun_busket']['dates']))
          @foreach ($final_cost['fun_busket']['dates'] as $fun_busket)
          <td class="{{$fun_busket['class']}}">{{numberConverter($fun_busket['value'],2,'pre')}}</td>
          @endforeach
          @endif
        </tr>
        <tr class="{{$country_name}} expandable" style="display: none; background-color: #DFCCFB;">
          <td>Finance Input Fun Busket</td>
          <td class=""><?=($final_cost['input_fun_busket']['total'] != 0) ? numberConverter($final_cost['input_fun_busket']['total'],2,'pre') : 'N/A' ?></td>

          @if(isset($final_cost['input_fun_busket']['dates']) && !empty($final_cost['input_fun_busket']['dates']))
          @foreach ($final_cost['input_fun_busket']['dates'] as $input_fun_busket)
          <td class=""><?=($input_fun_busket['value'] != 0) ? numberConverter($input_fun_busket['value'],2,'pre') : 'N/A' ?></td>
          @endforeach
          @endif
        </tr>
        <tr class="{{$country_name}} expandable" style="display: none; background-color: #DFCCFB;">
          <td>Cost Fun Busket Discrepency</td>
          <td>{{numberConverter($final_cost['fun_busket_discrepency']['total'],2,'post','%')}}</td>

          @if(isset($final_cost['fun_busket_discrepency']['dates']) && !empty($final_cost['fun_busket_discrepency']['dates']))
          @foreach ($final_cost['fun_busket_discrepency']['dates'] as $fun_busket_discrepency)
          <td class="{{$fun_busket_discrepency['class']}}">{{numberConverter($fun_busket_discrepency['value'],2,'post','%')}}</td>
          @endforeach
          @endif
        </tr>

        <tr class="{{$country_name}} expandable" style="display: none; background-color: #FFDB89;">
          <td>Cost BD</td>
          <td>{{numberConverter($final_cost['cost_bd']['total'],2,'pre')}}</td>

          @if(isset($final_cost['cost_bd']['dates']) && !empty($final_cost['cost_bd']['dates']))
          @foreach ($final_cost['cost_bd']['dates'] as $cost_bd)
          <td class="{{$cost_bd['class']}}">{{numberConverter($cost_bd['value'],2,'pre')}}</td>
          @endforeach
          @endif
        </tr>
        <tr class="{{$country_name}} expandable" style="display: none; background-color: #FFDB89;">
          <td>Finance Input BD</td>
          <td class=""><?=($final_cost['input_cost_bd']['total'] != 0) ? numberConverter($final_cost['input_cost_bd']['total'],2,'pre') : 'N/A' ?></td>

          @if(isset($final_cost['input_cost_bd']['dates']) && !empty($final_cost['input_cost_bd']['dates']))
          @foreach ($final_cost['input_cost_bd']['dates'] as $input_cost_bd)
          <td class=""><?=($input_cost_bd['value'] != 0) ? numberConverter($input_cost_bd['value'],2,'pre') : 'N/A' ?></td>
          @endforeach
          @endif
        </tr>
        <tr class="{{$country_name}} expandable" style="display: none; background-color: #FFDB89;">
          <td>Cost BD Discrepency</td>
          <td>{{numberConverter($final_cost['cost_bd_discrepency']['total'],2,'post','%')}}</td>

          @if(isset($final_cost['cost_bd_discrepency']['dates']) && !empty($final_cost['cost_bd_discrepency']['dates']))
          @foreach ($final_cost['cost_bd_discrepency']['dates'] as $cost_bd_discrepency)
          <td class="{{$cost_bd_discrepency['class']}}">{{numberConverter($cost_bd_discrepency['value'],2,'post','%')}}</td>
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