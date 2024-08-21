@extends('layouts.admin')

@section('title')
{{ __("Revenue Alert") }}
@endsection
<!-- @section('pagetytle')
{{ __("Monitor ADs By Operator") }}
@endsection -->
@section('content')

<!-- <div class="main-content position-relative"> -->
    <div class="page-content">
        @include('analytic.partials.revenueAlertFilter')

        <div id="container">
            <h1 style="display:none">Revenue Alert</h1>


            <div class="box-panel">
                {{--<div class="table-responsive shadow-sm mb-3">
                    <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                        <thead class="thead-dark text-uppercase">
                            <tr>
                                <th>Operator</th>
                                <th>Risk Status</th>
                                <th>Yesterday</th>
                                <th>Previous day</th>
                                <th>Last 7 days</th>
                                <th>Last 30 days</th>
                                <th>Last 90 days</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($DataArrays))
                                @foreach ($DataArrays as $key=> $DataArray)
                                    <tr>
                                        <td class="font-weight-bold text-uppercase">{{isset($key)?$key:''}}</td>

                                        <td class="font-weight-bold risk_status">2,180.13</td>

                                        <td class="text-danger"><span>{{isset($DataArray['yesterday_revenue'])?number_format($DataArray['yesterday_revenue'],2):''}}</span><i class="fa fa-arrow-down"></i>&nbsp;<small>{{isset($DataArray['yesterday_revenue_share'])?number_format($DataArray['yesterday_revenue_share'],2):''}}%</small></td>

                                        <td class="text-success"><span>144,838.30</span><i class="fa fa-arrow-up"></i>&nbsp;<small>6.25%</small></td>

                                        <td class="text-success"><span>{{isset($DataArray['past7day_revenue'])?number_format($DataArray['past7day_revenue'],2):''}}</span><i class="fa fa-arrow-up"></i>&nbsp;<small>{{isset($DataArray['past7day_revenue_share'])?number_format($DataArray['past7day_revenue_share'],2):''}}%</small></td>

                                        <td class="text-success"><span>{{isset($DataArray['past30day_revenue'])?number_format($DataArray['past30day_revenue'],2):''}}</span><i class="fa fa-arrow-up"></i>&nbsp;<small>{{isset($DataArray['past30day_revenue_share'])?number_format($DataArray['past30day_revenue_share'],2):''}}%</small></td>
                                        
                                        <td class=""><span>{{isset($DataArray['past90day_revenue'])?number_format($DataArray['past90day_revenue'],2):''}}</span>&nbsp;<small>{{isset($DataArray['past90day_revenue_share'])?number_format($DataArray['past90day_revenue_share'],2):''}}%</small></td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>--}}
                
                <div class="table-responsive shadow-sm tableFixHead">
                    <table class="table table-light table-borderd m-0 font-13 table-text-no-wrap">
                        <thead class="thead-dark sticky-col">
                          <tr>
                            <th class="sticky-col first-col" width="10%">Operator</th>
                            <th>Data</th>
                            <th>Risk Status</th>
                            <th>Yesterday</th>
                            <th>Previous Day</th>
                            <th>Last 7 Days</th>
                            <th>Last 30 Days</th>
                            <th>Last 90 Days</th>
                          </tr>
                        </thead>
                        
                        <tbody>

                            @if(isset($sumemry) && !empty($sumemry))
                            @php $i = 0; @endphp
                            @foreach ($sumemry as $key => $sumemry_data)
                            {{--<?php dd($sumemry_data); ?>--}}

                            @php $operator_name = strtoupper($sumemry_data['operator']['operator_name']); @endphp

                            <tr class="<?php echo ($i % 2 == 0) ? 'country-odd-bg' : '' ?>">
                                <td class="align-middle text-center font-weight-bold first-col"><span class="opbtn" data-param="{{$operator_name}}" style="cursor:pointer; min-width:10px; font-size:20px;"><strong>+</strong></span>{{$operator_name}}</td>
                                <td class="align-middle">Revenue</td>
                                <td class="align-middle">ERR</td>
                                <td class="align-middle">{{$sumemry_data['yesterday']['gros_rev']}}&nbsp;<small>({{$sumemry_data['yesterday']['percentage']}}%)</small></td>
                                <td class="align-middle">{{$sumemry_data['previousday']['gros_rev']}}&nbsp;<small>({{$sumemry_data['previousday']['percentage']}}%)</small></td>
                                <td class="align-middle">{{$sumemry_data['last_7day']['gros_rev']}}&nbsp;<small>({{$sumemry_data['last_7day']['percentage']}}%)</small></td>
                                <td class="align-middle">{{$sumemry_data['last_30day']['gros_rev']}}&nbsp;<small>({{$sumemry_data['last_30day']['percentage']}}%)</small></td>
                                <td class="align-middle">{{$sumemry_data['last_90day']['gros_rev']}}&nbsp;<small>({{$sumemry_data['last_90day']['percentage']}}%)</small></td>
                            </tr>
                            <tr class="{{$operator_name}} expandable <?php echo ($i % 2 == 0) ? 'operator-odd-bg' : '' ?>">
                                <td class="align-middle text-center first-col"><span class="ml-4"></span></td>
                                <td>Mo</td>
                                <td class="align-middle">ERR</td>
                                <td class="align-middle">{{$sumemry_data['yesterday']['mo']}}</td>
                                <td class="align-middle">{{$sumemry_data['previousday']['mo']}}</td>
                                <td class="align-middle">{{$sumemry_data['last_7day']['mo']}}</td>
                                <td class="align-middle">{{$sumemry_data['last_30day']['mo']}}</td>
                                <td class="align-middle">{{$sumemry_data['last_90day']['mo']}}</td>
                            </tr>
                            <tr class="{{$operator_name}} expandable <?php echo ($i % 2 == 0) ? 'operator-odd-bg' : '' ?>">
                                <td class="align-middle text-center first-col"><span class="ml-4"></span></td>
                                <td>Renewal</td>
                                <td class="align-middle">ERR</td>
                                <td class="align-middle">{{$sumemry_data['yesterday']['renewal']}}</td>
                                <td class="align-middle">{{$sumemry_data['previousday']['renewal']}}</td>
                                <td class="align-middle">{{$sumemry_data['last_7day']['renewal']}}</td>
                                <td class="align-middle">{{$sumemry_data['last_30day']['renewal']}}</td>
                                <td class="align-middle">{{$sumemry_data['last_90day']['renewal']}}</td>
                            </tr>

                            <tr class="{{$operator_name}} expandable <?php echo ($i % 2 == 0) ? 'operator-odd-bg' : '' ?>">
                                <td class="align-middle text-center first-col"><span class="ml-4"></span></td>
                                <td>Bill Rate</td>
                                <td class="align-middle">ERR</td>
                                <td class="align-middle">{{$sumemry_data['yesterday']['bill_rate']}}</td>
                                <td class="align-middle">{{$sumemry_data['previousday']['bill_rate']}}</td>
                                <td class="align-middle">{{$sumemry_data['last_7day']['bill_rate']}}</td>
                                <td class="align-middle">{{$sumemry_data['last_30day']['bill_rate']}}</td>
                                <td class="align-middle">{{$sumemry_data['last_90day']['bill_rate']}}</td>
                            </tr>

                            @php $i++; @endphp
                            @endforeach
                            @endif

                        </tbody>
                        {{----}}
                    </table>
                </div>
            </div>
        </div>

        <button type="button" id="button" class="btn btn-danger btn-floating btn-sm btn-back-to-top-position up"><i class="fa fa-arrow-up"></i></button>

    </div>

@endsection
