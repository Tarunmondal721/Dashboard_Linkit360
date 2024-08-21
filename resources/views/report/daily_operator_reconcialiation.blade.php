@extends('layouts.admin')

@section('title')
  {{ __('Reconcialiation Media') }}
@endsection

@section('content')
<div class="page-content">
  <div class="page-title" style="margin-bottom:25px">
    <div class="row justify-content-between align-items-center">
      <div class="col-xl-4 col-lg-4 col-md-4 d-flex align-items-center justify-content-between justify-content-md-start mb-3 mb-md-0">
        <div class="d-inline-block">
          <h5 class="h4 d-inline-block font-weight-400 mb-0"><b>Reconcialiation Media</b></h5><br>
          <p class="d-inline-block font-weight-200 mb-0">Reconcialiation of Operator Data</p>
        </div>
      </div>
      <div class="col-xl-8 col-lg-8 col-md-8 d-flex align-items-center justify-content-between justify-content-md-end">
      </div>
    </div>
  </div>

  @include('report.partials.filterReconcialiation')

  <div id="reportXls">
    <div class="d-flex align-items-center my-3">
      <span class="badge badge-secondary px-2 bg-primary text-uppercase">
        <a href="" class="text-white">ALL OPERATOR </a>@if(isset($allsummaryData) && !empty($allsummaryData)){{$allsummaryData['month_string']}}@endif
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
                    <th class="align-middle">T.Mo.End</th>
                    
                    @if(isset($no_of_days) && !empty($no_of_days))
                    @foreach ($no_of_days as $days)
                    <th class="align-middle">{{$days['no']}}</th>
                    @endforeach
                    @endif
                  </tr>
                </thead>
                <tbody>
                  @if(isset($allsummaryData) && !empty($allsummaryData))
                  @foreach ($allsummaryData as $key => $cost_campaign)
                  @if($key == 'cost_campaign')
                  <tr class="cost_campaign" style="background-color: #dae8fc;">
                    <td><strong>Cost Campaign</strong></td>
                    <td class="reg_total">{{numberConverter($cost_campaign['total'],2,'pre')}}</td>
                    <td class="reg_avg">{{numberConverter($cost_campaign['avg'],2,'pre')}}</td>
                    <td class="reg_monthly">{{numberConverter($cost_campaign['t_mo_end'],2,'pre')}}</td>

                    @if(isset($cost_campaign['dates']) && !empty($cost_campaign['dates']))
                    @foreach ($cost_campaign['dates'] as $cost_campaign1)
                    <td class="reg_data">{{numberConverter($cost_campaign1['value'],2,'pre')}}</td>
                    @endforeach
                    @endif
                  </tr>
                  @endif
                  @endforeach
                  @endif

                  @if(isset($allsummaryData) && !empty($allsummaryData))
                  @foreach ($allsummaryData as $key => $input_cost_campaign)
                  @if($key == 'input_cost_campaign')
                  <tr class="input_cost_campaign" style="background-color: #dae8fc;">
                    <td><strong>Cost Campaign Upload</strong></td>
                    <td class="reg_total">{{numberConverter($input_cost_campaign['total'],2,'pre')}}</td>
                    <td class="reg_avg">{{numberConverter($input_cost_campaign['avg'],2,'pre')}}</td>
                    <td class="reg_monthly">{{numberConverter($input_cost_campaign['t_mo_end'],2,'pre')}}</td>

                    @if(isset($input_cost_campaign['dates']) && !empty($input_cost_campaign['dates']))
                    @foreach ($input_cost_campaign['dates'] as $input_cost_campaign1)
                    <td class="reg_data">{{numberConverter($input_cost_campaign1['value'],2,'pre')}}</td>
                    @endforeach
                    @endif
                  </tr>
                  @endif
                  @endforeach
                  @endif

                  @if(isset($allsummaryData) && !empty($allsummaryData))
                  @foreach ($allsummaryData as $key => $cost_campaign_disc)
                  @if($key == 'cost_campaign_disc')
                  <tr class="cost_campaign_disc" style="background-color: #dae8fc;">
                    <td><strong>Discrepancy %</strong></td>
                    <td class="reg_total">N/A</td>
                    <td class="reg_avg">{{numberConverter($cost_campaign_disc['avg'],2,'pre')}}</td>
                    <td class="reg_monthly">N/A</td>

                    @if(isset($cost_campaign_disc['dates']) && !empty($cost_campaign_disc['dates']))
                    @foreach ($cost_campaign_disc['dates'] as $cost_campaign_disc1)
                    <td class="reg_data">{{numberConverter($cost_campaign_disc1['value'],2,'pre')}}</td>
                    @endforeach
                    @endif
                  </tr>
                  @endif
                  @endforeach
                  @endif

                  @if(isset($allsummaryData) && !empty($allsummaryData))
                  @foreach ($allsummaryData as $key => $mo)
                  @if($key == 'mo')
                  <tr class="mo" style="background-color: #ffe6cc;">
                    <td><strong>Campaign MO</strong></td>
                    <td class="unreg_total">{{numberConverter($mo['total'],2,'pre')}}</td>
                    <td class="unreg_avg">{{numberConverter($mo['avg'],2,'pre')}}</td>
                    <td class="unreg_monthly">{{numberConverter($mo['t_mo_end'],2,'pre')}}</td>

                    @if(isset($mo['dates']) && !empty($mo['dates']))
                    @foreach ($mo['dates'] as $mo1)
                    <td class="unreg_data">{{numberConverter($mo1['value'],2,'pre')}}</td>
                    @endforeach
                    @endif
                  </tr>
                  @endif
                  @endforeach
                  @endif

                  @if(isset($allsummaryData) && !empty($allsummaryData))
                  @foreach ($allsummaryData as $key => $input_mo)
                  @if($key == 'input_mo')
                  <tr class="input_mo" style="background-color: #ffe6cc;">
                    <td><strong>Campaign MO Upload</strong></td>
                    <td class="unreg_total">{{numberConverter($input_mo['total'],2,'pre')}}</td>
                    <td class="unreg_avg">{{numberConverter($input_mo['avg'],2,'pre')}}</td>
                    <td class="unreg_monthly">{{numberConverter($input_mo['t_mo_end'],2,'pre')}}</td>

                    @if(isset($input_mo['dates']) && !empty($input_mo['dates']))
                    @foreach ($input_mo['dates'] as $input_mo1)
                    <td class="unreg_data">{{numberConverter($input_mo1['value'],2,'pre')}}</td>
                    @endforeach
                    @endif
                  </tr>
                  @endif
                  @endforeach
                  @endif

                  @if(isset($allsummaryData) && !empty($allsummaryData))
                  @foreach ($allsummaryData as $key => $mo_disc)
                  @if($key == 'mo_disc')
                  <tr class="mo_disc" style="background-color: #ffe6cc;">
                    <td><strong>Discrepancy %</strong></td>
                    <td class="unreg_total">N/A</td>
                    <td class="unreg_avg">{{numberConverter($mo_disc['avg'],2,'pre')}}</td>
                    <td class="unreg_monthly">N/A</td>

                    @if(isset($mo_disc['dates']) && !empty($mo_disc['dates']))
                    @foreach ($mo_disc['dates'] as $mo_disc1)
                    <td class="unreg_data">{{numberConverter($mo_disc1['value'],2,'pre')}}</td>
                    @endforeach
                    @endif
                  </tr>
                  @endif
                  @endforeach
                  @endif

                  @if(isset($allsummaryData) && !empty($allsummaryData))
                  @foreach ($allsummaryData as $key => $price_mo)
                  @if($key == 'price_mo')
                  <tr class="price_mo" style="background-color: #e1d5e7;">
                    <td><strong>Price.MO</strong></td>
                    <td class="purged_total">N/A</td>
                    <td class="purged_avg">{{numberConverter($price_mo['avg'],4,'pre')}}</td>
                    <td class="purged_monthly">N/A</td>

                    @if(isset($price_mo['dates']) && !empty($price_mo['dates']))
                    @foreach ($price_mo['dates'] as $price_mo1)
                    <td class="purged_data">{{numberConverter($price_mo1['value'],4,'pre')}}</td>
                    @endforeach
                    @endif
                  </tr>
                  @endif
                  @endforeach
                  @endif

                  @if(isset($allsummaryData) && !empty($allsummaryData))
                  @foreach ($allsummaryData as $key => $input_price_mo)
                  @if($key == 'input_price_mo')
                  <tr class="input_price_mo" style="background-color: #e1d5e7;">
                    <td><strong>Price.MO Upload</strong></td>
                    <td class="purged_total">N/A</td>
                    <td class="purged_avg">{{numberConverter($input_price_mo['avg'],4,'pre')}}</td>
                    <td class="purged_monthly">N/A</td>

                    @if(isset($input_price_mo['dates']) && !empty($input_price_mo['dates']))
                    @foreach ($input_price_mo['dates'] as $input_price_mo1)
                    <td class="purged_data">{{numberConverter($input_price_mo1['value'],4,'pre')}}</td>
                    @endforeach
                    @endif
                  </tr>
                  @endif
                  @endforeach
                  @endif

                  @if(isset($allsummaryData) && !empty($allsummaryData))
                  @foreach ($allsummaryData as $key => $price_mo_disc)
                  @if($key == 'price_mo_disc')
                  <tr class="price_mo_disc" style="background-color: #e1d5e7;">
                    <td><strong>Discrepancy %</strong></td>
                    <td class="purged_total">N/A</td>
                    <td class="purged_avg">{{numberConverter($price_mo_disc['avg'],4,'pre')}}</td>
                    <td class="purged_monthly">N/A</td>

                    @if(isset($price_mo_disc['dates']) && !empty($price_mo_disc['dates']))
                    @foreach ($price_mo_disc['dates'] as $price_mo_disc1)
                    <td class="purged_data">{{numberConverter($price_mo_disc1['value'],4,'pre')}}</td>
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
  </div>

  <div id="container">
    @if(isset($sumemry) && !empty($sumemry))
    @foreach ($sumemry as $report)
    <div class="ptable">
      <div class="d-flex align-items-center my-3">
        <span class="badge badge-secondary px-2 bg-primary text-uppercase">
          <img src="{{ asset('/flags/'.$report['country']['flag']) }}" width="30" height="20">&nbsp;
          {{$report['country']['country_code']}} {{$report['operator']->getOperatorName($report['operator'])}} {{$report['month_string']}} | Last Update: {{$report['last_update']}}
        </span>
        <span class="flex-fill ml-2 bg-primary w-100 font-weight-bold" style="height: 1px;"></span>
        <div class="text-right pl-2">
          <button class="btn btn-sm report-xls" style="color:white; background-color:green" data-param="{{$report['operator']->operator_name}}"><i class="fa fa-file-excel-o"></i>Export as XLS</button>
        </div>
      </div>

      <div class="card">
        <div class="table-responsive shadow-sm" id="{{$report['operator']->operator_name}}">
          <h1 style="display:hidden"></h1>
          <table class="table table-light table-striped m-0 font-13 xlaxiata" id="dtbl">
            <thead class="thead-dark">
              <tr>
                <th class="align-middle">Summary</th>
                <th class="align-middle">Total</th>
                <th class="align-middle">AVG</th>
                <th class="align-middle">T.Mo.End</th>

                @if(isset($no_of_days) && !empty($no_of_days))
                @foreach ($no_of_days as $days)
                <th class="align-middle">{{$days['no']}}</th>
                @endforeach
                @endif
              </tr>
            </thead>
            <tbody>
              <tr class="cost_campaign" style="background-color: #dae8fc;">
                <td><strong>Cost Campaign</strong></td>
                <td class="reg_total cost">{{numberConverter($report['cost_campaign']['total'],2,'pre')}}</td>
                <td class="reg_avg">{{numberConverter($report['cost_campaign']['avg'],2,'pre')}}</td>
                <td class="reg_monthly">{{numberConverter($report['cost_campaign']['t_mo_end'],2,'pre')}}</td>

                @if(isset($report['cost_campaign']['dates']) && !empty($report['cost_campaign']['dates']))
                @foreach ($report['cost_campaign']['dates'] as $cost_campaign)
                <td class="reg_data">{{numberConverter($cost_campaign['value'],2,'pre')}}</td>
                @endforeach
                @endif
              </tr>
              <tr class="input_cost_campaign" style="background-color: #dae8fc;">
                <td><strong>Cost Campaign Upload</strong></td>
                <td class="reg_total">{{numberConverter($report['input_cost_campaign']['total'],2,'pre')}}</td>
                <td class="reg_avg">{{numberConverter($report['input_cost_campaign']['avg'],2,'pre')}}</td>
                <td class="reg_monthly">{{numberConverter($report['input_cost_campaign']['t_mo_end'],2,'pre')}}</td>

                @if(isset($report['input_cost_campaign']['dates']) && !empty($report['input_cost_campaign']['dates']))
                @foreach ($report['input_cost_campaign']['dates'] as $input_cost_campaign)
                <td class="reg_data">{{numberConverter($input_cost_campaign['value'],2,'pre')}}</td>
                @endforeach
                @endif
              </tr>
              <tr class="cost_campaign" style="background-color: #dae8fc;">
                <td><strong>Discrepancy %</strong></td>
                <td class="reg_total">N/A</td>
                <td class="reg_avg">{{numberConverter($report['cost_campaign_disc']['avg'],2,'pre')}}</td>
                <td class="reg_monthly">N/A</td>

                @if(isset($report['cost_campaign_disc']['dates']) && !empty($report['cost_campaign_disc']['dates']))
                @foreach ($report['cost_campaign_disc']['dates'] as $cost_campaign_disc)
                <td class="reg_data">{{numberConverter($cost_campaign_disc['value'],2,'pre')}}</td>
                @endforeach
                @endif
              </tr>
              <tr class="mo" style="background-color: #ffe6cc;">
                <td><strong>Campaign MO</strong></td>
                <td class="unreg_total">{{numberConverter($report['mo']['total'],2,'pre')}}</td>
                <td class="unreg_avg">{{numberConverter($report['mo']['avg'],2,'pre')}}</td>
                <td class="unreg_monthly">{{numberConverter($report['mo']['t_mo_end'],2,'pre')}}</td>

                @if(isset($report['mo']['dates']) && !empty($report['mo']['dates']))
                @foreach ($report['mo']['dates'] as $mo)
                <td class="unreg_data">{{numberConverter($mo['value'],2,'pre')}}</td>
                @endforeach
                @endif
              </tr>
              <tr class="input_mo" style="background-color: #ffe6cc;">
                <td><strong>Campaign MO Upload</strong></td>
                <td class="reg_total">{{numberConverter($report['input_mo']['total'],2,'pre')}}</td>
                <td class="reg_avg">{{numberConverter($report['input_mo']['avg'],2,'pre')}}</td>
                <td class="reg_monthly">{{numberConverter($report['input_mo']['t_mo_end'],2,'pre')}}</td>

                @if(isset($report['input_mo']['dates']) && !empty($report['input_mo']['dates']))
                @foreach ($report['input_mo']['dates'] as $input_mo)
                <td class="reg_data">{{numberConverter($input_mo['value'],2,'pre')}}</td>
                @endforeach
                @endif
              </tr>
              <tr class="mo_disc" style="background-color: #ffe6cc;">
                <td><strong>Discrepancy %</strong></td>
                <td class="reg_total">N/A</td>
                <td class="reg_avg">{{numberConverter($report['mo_disc']['avg'],2,'pre')}}</td>
                <td class="reg_monthly">N/A</td>

                @if(isset($report['mo_disc']['dates']) && !empty($report['mo_disc']['dates']))
                @foreach ($report['mo_disc']['dates'] as $mo_disc)
                <td class="reg_data">{{numberConverter($mo_disc['value'],2,'pre')}}</td>
                @endforeach
                @endif
              </tr>
              <tr class="price_mo" style="background-color: #e1d5e7;">
                <td><strong>Price.MO</strong></td>
                <td class="purged_total">N/A</td>
                <td class="purged_avg">{{numberConverter($report['price_mo']['avg'],3,'pre')}}</td>
                <td class="purged_monthly">N/A</td>

                @if(isset($report['price_mo']['dates']) && !empty($report['price_mo']['dates']))
                @foreach ($report['price_mo']['dates'] as $price_mo)
                <td class="purged_data">{{numberConverter($price_mo['value'],3,'pre')}}</td>
                @endforeach
                @endif
              </tr>
              <tr class="input_mo" style="background-color: #e1d5e7;">
                <td><strong>Price.MO Upload</strong></td>
                <td class="reg_total">{{numberConverter($report['input_price_mo']['total'],2,'pre')}}</td>
                <td class="reg_avg">{{numberConverter($report['input_price_mo']['avg'],2,'pre')}}</td>
                <td class="reg_monthly">{{numberConverter($report['input_price_mo']['t_mo_end'],2,'pre')}}</td>

                @if(isset($report['input_price_mo']['dates']) && !empty($report['input_price_mo']['dates']))
                @foreach ($report['input_price_mo']['dates'] as $input_price_mo)
                <td class="reg_data">{{numberConverter($input_price_mo['value'],2,'pre')}}</td>
                @endforeach
                @endif
              </tr>
              <tr class="price_mo_disc" style="background-color: #e1d5e7;">
                <td><strong>Discrepancy %</strong></td>
                <td class="reg_total">N/A</td>
                <td class="reg_avg">{{numberConverter($report['price_mo_disc']['avg'],2,'pre')}}</td>
                <td class="reg_monthly">N/A</td>

                @if(isset($report['price_mo_disc']['dates']) && !empty($report['price_mo_disc']['dates']))
                @foreach ($report['price_mo_disc']['dates'] as $price_mo_disc)
                <td class="reg_data">{{numberConverter($price_mo_disc['value'],2,'pre')}}</td>
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
@endsection
