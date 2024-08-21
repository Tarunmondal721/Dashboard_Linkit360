<?php
    $start_date = request()->get('from');
    $end_date = request()->get('to');
?>

@extends('layouts.admin')

@section('title')
    {{ __('Report Details') }}
@endsection

@section('content')
    <div class="page-content">
        <div class="page-title" style="margin-bottom:25px">
            <div class="row justify-content-between align-items-center">
                <div class="col-md-12 d-flex align-items-center justify-content-between justify-content-md-start mb-3 mb-md-0">
                    <div class="d-inline-block">
                        <h5 class="h4 d-inline-block font-weight-400 mb-0"><b>{{ __('Report Details') }}</b></h5>
                        <div>{{ __('Detail Data on Operators and Their Services') }}</div>
                    </div>
                </div>
                <div class="col-xl-8 col-lg-8 col-md-8 d-flex align-items-center justify-content-between justify-content-md-end">
                </div>
            </div>
        </div>

        @include('report.partials.filterReportDetails')

        <div id="container">
            <div class="card shadow-sm mt-0">
                <div class="">
                    <div id="excelData">
                        <h1 style="display:none">Reporting Details For Operator</h1>

                        @for ($i = 0; $i < count($data); $i++)
                            @php
                                $item = $data[$i];
                                $sumemry = $item['sumemry'];
                                $no_of_days = $item['no_of_days'];
                                $allsummaryData = $item['allsummaryData'];
                            @endphp
                            @if (isset($sumemry) && !empty($sumemry))
                                @foreach ($sumemry as $key => $report)
                                    <!-- start Operator repeat -->
                                    <div class="card">
                                        <div class="d-flex align-items-center my-3">
                                            <span class="badge badge-secondary px-2 bg-primary text-uppercase">
                                                <img src="{{ asset('/flags/' . $report['country']['flag']) }}" width="30" height="20">&nbsp;{{ $report['country']['country_code'] }}<a href="{{ route('report.details', '?operator=' . $report['operator']->id_operator) }}" class="text-white"> {{ $report['operator']->getOperatorName($report['operator']) }} </a>
                                                {{ $report['month_string'] }} | Last Update: {{ $report['last_update'] }}
                                            </span>
                                            <span class="flex-fill ml-2 bg-primary w-100 font-weight-bold" style="height: 1px;"></span>
                                            <div class="text-right pl-2">
                                                <button class="btn btn-sm report-xls" style="color:white; background-color:green" data-param="xlaxiata"><i class="fa fa-file-excel-o"></i>Export as XLS</button>
                                            </div>
                                        </div>
                                        <div class="card">
                                            <div class="table-responsive shadow-sm" id="xlaxiata">
                                                <h1 style="display:hidden"></h1>
                                                <table class="table table-light table-striped m-0 font-13 xlaxiata" id="dtbl">
                                                    <thead class="thead-dark">
                                                        <tr>
                                                            <th class="align-middle">Summary</th>
                                                            <th class="align-middle">Total</th>
                                                            <th class="align-middle">AVG</th>
                                                            <th class="align-middle">T.Mo.End</th>

                                                            @if (isset($no_of_days) && !empty($no_of_days))
                                                            @foreach ($no_of_days as $days)
                                                            <th class="align-middle">{{ $days['no'] }}</th>
                                                            @endforeach
                                                            @endif
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr class="tur" style="background-color: #9ce0ff;">
                                                            <td><strong class="text-with-sup">GMV(USD) <sup><i class="ml-3 text-dark fa fa-info-circle" title="Revenue that we charge user excluding tax"></i></sup></strong>
                                                            </td>
                                                            <td class="revenue_total_usd" data-country="{{ $report['country']['country'] }}" data-local-currency="{{ $report['country']['country_code'] }}" data-usd="{{ $report['country']['usd'] }}" style="color:#212529"><span class="format_total_revenue">{{ numberConverter($allsummaryData['tur']['total'], 2, 'pre') }}</span>
                                                            </td>
                                                            <td class="revenue_avg_usd" style="color:#212529">{{ numberConverter($allsummaryData['tur']['avg'], 2, 'pre') }}</td>
                                                            <td class="revenue_monthly_usd" style="color:#212529">{{ numberConverter($allsummaryData['tur']['t_mo_end'], 2, 'pre') }}</td>

                                                            @if (isset($allsummaryData['tur']['dates']) && !empty($allsummaryData['tur']['dates']))
                                                            @foreach ($allsummaryData['tur']['dates'] as $tur)
                                                            <td class="gross_revenue_usd {{ $tur['class'] }}">{{ numberConverter($tur['value'], 2, 'pre') }}</td>
                                                            @endforeach
                                                            @endif
                                                        </tr>

                                                        <tr class="text-white rev" style="background-color: #9ce0ff">
                                                            <td>
                                                                <strong class="text-with-sup">
                                                                    <div class="revenueSeperateBtn pull-left" data-sign="plus" style="cursor: pointer;min-width: 10px;">+</div>
                                                                    <div class="text-left"> GMV ({{ $report['country']['currency_code'] }})<sup><i class="ml-3 text-dark fa fa-info-circle" title="Revenue that we charge user excluding tax"></i></sup>
                                                                    </div>
                                                                </strong>
                                                            </td>
                                                            <td class="revenue_total" data-country="{{ $report['country']['country'] }}" data-local-currency="{{ $report['country']['country_code'] }}" data-usd="{{ $report['country']['usd'] }}"><span class="local_total_revenue">{{ numberConverter($allsummaryData['t_rev']['total'], 2, 'pre') }}</span>
                                                            </td>
                                                            <td class="revenue_avg">{{ numberConverter($allsummaryData['t_rev']['avg'], 2, 'pre') }}</td>
                                                            <td class="revenue_monthly">{{ numberConverter($allsummaryData['t_rev']['t_mo_end'], 2, 'pre') }}</td>

                                                            @if (isset($allsummaryData['t_rev']['dates']) && !empty($allsummaryData['t_rev']['dates']))
                                                            @foreach ($allsummaryData['t_rev']['dates'] as $t_rev)
                                                            <td class="gross_revenue_usd {{ $t_rev['class'] }}">{{ numberConverter($t_rev['value'], 2, 'pre') }}</td>
                                                            @endforeach
                                                            @endif
                                                        </tr>

                                                        <tr class="revExtendedRows" style="border-top: 5px solid rgb(12, 84, 160); border:5px solid #0c54a0; border-bottom:none; display:none; background-color: #9ce0ff;">
                                                            <td><strong class="text-with-sup">G.REV({{ $report['country']['currency_code'] }})<sup><i class="ml-3 text-dark fa fa-info-circle" title="Total Revenue after telco"></i></sup></strong>
                                                            </td>
                                                            <td class="rev_share_total">{{ numberConverter($allsummaryData['trat']['total'], 2, 'pre') }}</td>
                                                            <td class="rev_share_avg">{{ numberConverter($allsummaryData['trat']['avg'], 2, 'pre') }}</td>
                                                            <td class="rev_share_monthly">{{ numberConverter($allsummaryData['trat']['t_mo_end'], 2, 'pre') }}</td>

                                                            @if (isset($allsummaryData['trat']['dates']) && !empty($allsummaryData['trat']['dates']))
                                                            @foreach ($allsummaryData['trat']['dates'] as $trat)
                                                            <td class="gross_revenue_usd {{ $trat['class'] }}">{{ numberConverter($trat['value'], 2, 'pre') }}</td>
                                                            @endforeach
                                                            @endif
                                                        </tr>

                                                        <tr class="revExtendedRows" style=" border:5px solid #0c54a0; border-top:none; border-bottom:none; display:none; background-color: #9ce0ff;">
                                                            <td><strong class="text-with-sup">G.REV(USD)<sup><i class="ml-3 text-dark fa fa-info-circle" title="Total USD Revenue after telco"></i></sup></strong>
                                                            </td>
                                                            <td class="rev_usd_share_total">{{ numberConverter($allsummaryData['turt']['total'], 2, 'pre') }}</td>
                                                            <td class="rev_usd_share_avg">{{ numberConverter($allsummaryData['turt']['avg'], 2, 'pre') }}</td>
                                                            <td class="rev_usd_share_monthly">{{ numberConverter($allsummaryData['turt']['t_mo_end'], 2, 'pre') }}</td>

                                                            @if (isset($allsummaryData['turt']['dates']) && !empty($allsummaryData['turt']['dates']))
                                                            @foreach ($allsummaryData['turt']['dates'] as $turt)
                                                            <td class="gross_revenue_usd {{ $turt['class'] }}">{{ numberConverter($turt['value'], 2, 'pre') }}</td>
                                                            @endforeach
                                                            @endif
                                                        </tr>

                                                        <tr class="revExtendedRows" style=" border:5px solid #0c54a0; border-top:none; display:none; background-color: #9ce0ff;">
                                                            <td><strong class="text-with-sup">N.REV (USD)<sup><i class="ml-3 text-dark fa fa-info-circle" title="Net Revenue = G.REV - other tax"></i></sup></strong>
                                                            </td>
                                                            <td class="subactive_total">{{ numberConverter($allsummaryData['net_rev']['total'], 2, 'pre') }}</td>
                                                            <td class="subactive_avg">{{ numberConverter($allsummaryData['net_rev']['avg'], 2, 'pre') }}</td>
                                                            <td class="subactive_monthly">{{ numberConverter($allsummaryData['net_rev']['t_mo_end'], 2, 'pre') }}</td>

                                                            @if (isset($allsummaryData['net_rev']['dates']) && !empty($allsummaryData['net_rev']['dates']))
                                                            @foreach ($allsummaryData['net_rev']['dates'] as $net_rev)
                                                            <td class="gross_revenue_usd {{ $net_rev['class'] }}">{{ numberConverter($net_rev['value'], 0, 'pre') }}</td>
                                                            @endforeach
                                                            @endif
                                                        </tr>

                                                        <tr class="subs" style="background-color: #e2b3fc;">
                                                            <td><strong class="text-with-sup">T Subs <sup><i class="ml-3 text-dark fa fa-info-circle" title="Total Subactive"></i></sup></strong></td>
                                                            <td class="subactive_total">{{ numberConverter($allsummaryData['t_sub']['total'], 0, 'pre') }}</td>
                                                            <td class="subactive_avg">N/A</td>
                                                            <td class="subactive_monthly">N/A</td>

                                                            @if (isset($allsummaryData['t_sub']['dates']) && !empty($allsummaryData['t_sub']['dates']))
                                                            @foreach ($allsummaryData['t_sub']['dates'] as $t_sub)
                                                            <td class="gross_revenue_usd {{ $t_sub['class'] }}">{{ numberConverter($t_sub['value'], 0, 'pre') }}</td>
                                                            @endforeach
                                                            @endif
                                                        </tr>

                                                        <tr class="reg" style="background-color: #e2b3fc;">
                                                            <td><strong>Reg</strong></td>
                                                            <td class="reg_total">{{ numberConverter($allsummaryData['reg']['total'], 0, 'pre') }}</td>
                                                            <td class="reg_avg">{{ numberConverter($allsummaryData['reg']['avg'], 0, 'pre') }}</td>
                                                            <td class="reg_monthly">{{ numberConverter($allsummaryData['reg']['t_mo_end'], 0, 'pre') }}</td>

                                                            @if (isset($allsummaryData['reg']['dates']) && !empty($allsummaryData['reg']['dates']))
                                                            @foreach ($allsummaryData['reg']['dates'] as $reg)
                                                            <td class="reg_data {{ $reg['class'] }}">{{ numberConverter($reg['value'], 0, 'pre') }}</td>
                                                            @endforeach
                                                            @endif
                                                        </tr>

                                                        <tr class="unreg" style="background-color: #e2b3fc;">
                                                            <td><strong>Unreg</strong></td>
                                                            <td class="unreg_total">{{ numberConverter($allsummaryData['unreg']['total'], 0, 'pre') }}</td>
                                                            <td class="unreg_avg">{{ numberConverter($allsummaryData['unreg']['avg'], 0, 'pre') }}</td>
                                                            <td class="unreg_monthly">{{ numberConverter($allsummaryData['unreg']['t_mo_end'], 0, 'pre') }}</td>

                                                            @if (isset($allsummaryData['unreg']['dates']) && !empty($allsummaryData['unreg']['dates']))
                                                            @foreach ($allsummaryData['unreg']['dates'] as $unreg)
                                                            <td class="unreg_data {{ $unreg['class'] }}">{{ numberConverter($unreg['value'], 0, 'pre') }}</td>
                                                            @endforeach
                                                            @endif
                                                        </tr>

                                                        <tr class="purged" style="background-color: #e2b3fc;">
                                                            <td><strong>Purged</strong></td>
                                                            <td class="purged_total">{{ numberConverter($allsummaryData['purged']['total'], 0, 'pre') }}</td>
                                                            <td class="purged_avg">{{ numberConverter($allsummaryData['purged']['avg'], 0, 'pre') }}</td>
                                                            <td class="purged_monthly">{{ numberConverter($allsummaryData['purged']['t_mo_end'], 0, 'pre') }}</td>

                                                            @if (isset($allsummaryData['purged']['dates']) && !empty($allsummaryData['purged']['dates']))
                                                            @foreach ($allsummaryData['purged']['dates'] as $purged)
                                                            <td class="purged_data {{ $purged['class'] }}">{{ numberConverter($purged['value'], 0, 'pre') }}</td>
                                                            @endforeach
                                                            @endif
                                                        </tr>

                                                        <tr class="churn" style="background-color: #e2b3fc;">
                                                            <td><strong class="text-with-sup">Churn % <sup><i class="ml-3 text-dark fa fa-info-circle" title="(unreg/ tsubs) * 100"></i></sup></strong></td>
                                                            <td class="churn_total_msg">N/A</td>
                                                            <td class="churn_avg">{{ numberConverter($allsummaryData['churn']['avg'], 2, 'post', '%') }}</td>
                                                            <td class="churn_monthly_msg">N/A</td>

                                                            @if (isset($allsummaryData['churn']['dates']) && !empty($allsummaryData['churn']['dates']))
                                                            @foreach ($allsummaryData['churn']['dates'] as $churn)
                                                            <td class="churn_data {{ $churn['class'] }}">{{ numberConverter($churn['value'], 2, 'post', '%') }}</td>
                                                            @endforeach
                                                            @endif
                                                        </tr>

                                                        <tr class="renewal" style="background-color: #e2b3fc;">
                                                            <td><strong>Renewal</strong></td>
                                                            <td class="total_sent">{{ numberConverter($allsummaryData['renewal']['total'], 0, 'pre') }}</td>
                                                            <td class="total_sent_avg">{{ numberConverter($allsummaryData['renewal']['avg'], 0, 'pre') }}</td>
                                                            <td class="total_sent_mon">{{ numberConverter($allsummaryData['renewal']['t_mo_end'], 0, 'pre') }}</td>

                                                            @if (isset($allsummaryData['renewal']['dates']) && !empty($allsummaryData['renewal']['dates']))
                                                            @foreach ($allsummaryData['renewal']['dates'] as $renewal)
                                                            <td class="total_sent_data {{ $renewal['class'] }}">{{ numberConverter($renewal['value'], 0, 'pre') }}</td>
                                                            @endforeach
                                                            @endif
                                                        </tr>

                                                        <tr class="bill" style="background-color: #a9fc9a;">
                                                            <td>
                                                                <strong class="text-with-sup">
                                                                    <div class="billSeperateBtn pull-left" data-sign="plus" style="cursor: pointer;">+</div>
                                                                    <div class="text-left">Bill % </div><sup><i class="ml-3 text-dark fa fa-info-circle" title="(Success Charge / Renewal) * 100%"></i></sup>
                                                                </strong>
                                                                <div class="billDivs" data-sign="plus"></div>
                                                            </td>
                                                            <td class="total_br_msg">N/A</td>
                                                            <td class="br_avg">{{ numberConverter($allsummaryData['bill']['avg'], 2, 'post', '%') }}</td>
                                                            <td class="br_monthly_msg">N/A</td>

                                                            @if (isset($allsummaryData['bill']['dates']) && !empty($allsummaryData['bill']['dates']))
                                                            @foreach ($allsummaryData['bill']['dates'] as $bill)
                                                            <td class="br_data {{ $bill['class'] }}">{{ numberConverter($bill['value'], 2, 'post', '%') }}</td>
                                                            @endforeach
                                                            @endif
                                                        </tr>

                                                        <tr class="billExtendedRows" style=" border:5px solid #0c54a0; border-bottom:none; display: none; background-color: #a9fc9a;">
                                                            <td>
                                                                <strong class="text-with-sup">First P.%<sup><i class="ml-3 text-dark fa fa-info-circle" title="((First Push Success Charge / Total Sent) * 100% ) / total service count of each operator"></i></sup></strong>
                                                            </td>
                                                            <td class="total_first_push_msg">N/A</td>
                                                            <td class="first_push_avg">{{ numberConverter($allsummaryData['first_push']['avg'], 2, 'post', '%') }}</td>
                                                            <td class="monthly_first_push_msg">N/A</td>

                                                            @if (isset($allsummaryData['first_push']['dates']) && !empty($allsummaryData['first_push']['dates']))
                                                            @foreach ($allsummaryData['first_push']['dates'] as $first_push)
                                                            <td class="br_data {{ $first_push['class'] }}">{{ numberConverter($first_push['value'], 2, 'post', '%') }}</td>
                                                            @endforeach
                                                            @endif
                                                        </tr>

                                                        <tr class="billExtendedRows" style=" border:5px solid #0c54a0; border-top:none; display: none; background-color: #a9fc9a;">
                                                            <td>
                                                                <strong class="text-with-sup">Daily P.%<sup><i class="ml-3 text-dark fa fa-info-circle" title="((Daily Push Success Charge / Total Sent) * 100% ) / total service count of each operator"></i></sup></strong>
                                                            </td>
                                                            <td class="total_daily_push_msg">N/A</td>
                                                            <td class="daily_push_avg">{{ numberConverter($allsummaryData['daily_push']['avg'], 2, 'post', '%') }}</td>
                                                            <td class="monthly_daily_push_msg">N/A</td>

                                                            @if (isset($allsummaryData['daily_push']['dates']) && !empty($allsummaryData['daily_push']['dates']))
                                                            @foreach ($allsummaryData['daily_push']['dates'] as $daily_push)
                                                            <td class="br_data {{ $daily_push['class'] }}">{{ numberConverter($daily_push['value'], 2, 'post', '%') }}</td>
                                                            @endforeach
                                                            @endif
                                                        </tr>

                                                        <tr class="arpu7" style="background-color: #a9fc9a;">
                                                            <td>
                                                                <strong class="text-with-sup">
                                                                    <div class="arpuSeperateBtn pull-left" data-sign="plus" style="cursor: pointer;">+</div>
                                                                    <div class="text-left">7ARPU</div><sup><i class="ml-3 text-dark fa fa-info-circle" title="Total USD Revenue After Telco (Last 7 Days) / (Total Reg Last 7 Days + Total Subs Active Current Day)"></i></sup>
                                                                </strong>
                                                            </td>
                                                            <td class="arpu_seven_msg">N/A</td>
                                                            <td class="arpu_seven_avg">{{ numberConverter($allsummaryData['arpu7']['avg'], 4, 'report') }}</td>
                                                            <td class="arpu_seven_msg">N/A</td>

                                                            @if (isset($allsummaryData['arpu7']['dates']) && !empty($allsummaryData['arpu7']['dates']))
                                                            @foreach ($allsummaryData['arpu7']['dates'] as $arpu7)
                                                            <td class="arpu7_data {{ $arpu7['class'] }}">{{ numberConverter($arpu7['value'], 4, 'pre') }}</td>
                                                            @endforeach
                                                            @endif
                                                        </tr>

                                                        <tr class="arpuExtendedRows" style=" border:5px solid #0c54a0; border-bottom:none; display: none; background-color: #a9fc9a;">
                                                            <td><strong>US7ARPU</strong></td>
                                                            <td class="arpu_usd_seven_msg">N/A</td>
                                                            <td class="arpu_usd_seven_avg">{{ numberConverter($allsummaryData['usarpu7']['avg'], 4) }}</td>
                                                            <td class="arpu_usd_seven_msg">N/A</td>

                                                            @if (isset($allsummaryData['usarpu7']['dates']) && !empty($allsummaryData['usarpu7']['dates']))
                                                            @foreach ($allsummaryData['usarpu7']['dates'] as $usarpu7)
                                                            <td class="usd_arpu7_data {{ $usarpu7['class'] }}">{{ numberConverter($usarpu7['value'], 4, 'pre') }}</td>
                                                            @endforeach
                                                            @endif
                                                        </tr>

                                                        <tr class="arpuExtendedRows" style=" border-left:5px solid #0c54a0; border-right:5px solid #0c54a0; display: none; background-color: #a9fc9a;">
                                                            <td><strong class="text-with-sup">30ARPU<sup><i class="ml-3 text-dark fa fa-info-circle" title="Total USD Revenue After Telco (Last 30 Days) / (Total Reg Last 30 Days + Total Subs Active Current Day) "></i></sup></strong>
                                                            </td>
                                                            <td class="arpu_30_msg">N/A</td>
                                                            <td class="arpu_30_avg">{{ numberConverter($allsummaryData['arpu30']['avg'], 4) }}</td>
                                                            <td class="arpu_30_msg">N/A</td>

                                                            @if (isset($allsummaryData['arpu30']['dates']) && !empty($allsummaryData['arpu30']['dates']))
                                                            @foreach ($allsummaryData['arpu30']['dates'] as $arpu30)
                                                            <td class="arpu30_data {{ $arpu30['class'] }}">{{ numberConverter($arpu30['value'], 4, 'pre') }}</td>
                                                            @endforeach
                                                            @endif
                                                        </tr>

                                                        <tr class="arpuExtendedRows" style=" border:5px solid #0c54a0; border-top:none; display: none; background-color: #a9fc9a;">
                                                            <td><strong>US30ARPU</strong></td>
                                                            <td class="arpu_usd_30_msg">N/A</td>
                                                            <td class="arpu_usd_30_avg">{{ numberConverter($allsummaryData['usarpu30']['avg'], 4) }}</td>
                                                            <td class="arpu_usd_30_msg">N/A</td>

                                                            @if (isset($allsummaryData['usarpu30']['dates']) && !empty($allsummaryData['usarpu30']['dates']))
                                                            @foreach ($allsummaryData['usarpu30']['dates'] as $usarpu30)
                                                            <td class="usd_arpu30_data {{ $usarpu30['class'] }}">{{ numberConverter($usarpu30['value'], 4, 'pre') }}</td>
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
                        @endfor

                        <!-- Service data start -->
                        @for ($i = 0; $i < count($data); $i++)
                            @php
                                $item = $data[$i];
                                $sumemry = $item['sumemry'];
                                $no_of_days = $item['no_of_days'];
                                $allsummaryData = $item['allsummaryData'];

                            @endphp

                            @if (isset($sumemry) && !empty($sumemry))
                                @foreach ($sumemry as $value)
                                    @foreach ($value['services'] as $key => $service)
                                        <div class="box-panel">
                                            <div class="table-responsive shadow-sm">
                                                <table class="table table-light table-striped m-0 font-13 sub_detail dtbl">
                                                    <thead class="thead-dark text-no-wrapping">
                                                        <tr>
                                                            <th class="col-service sticky-col first-col"
                                                                style="background-color:#5a646f" width="10%">Service Info
                                                            </th>
                                                            <th style="background-color:#5a646f" class="sticky-col second-col"
                                                                width="3%">Type</th>
                                                            <th class="col-value" width="5%">Total</th>
                                                            <th class="col-value" width="5%">AVG</th>
                                                            <th class="col-value" width="5%">T.Mo.End</th>

                                                            @if (isset($no_of_days) && !empty($no_of_days))
                                                                @foreach ($no_of_days as $days)
                                                                    <th class="col-value" width="auto">{{ $days['no'] }}
                                                                    </th>
                                                                @endforeach
                                                            @endif
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr class="service_info sticky-col first-col">
                                                            <td rowspan="13"
                                                                class="text-center align-middle font-weight-bold bg-white col-service sticky-col first-col"
                                                                style="width:25px;">
                                                                <div class="mb-4"> Name: <br><a
                                                                        href="{{ route('report.service.details', '?service=' . $service['service']->id_service) . '&from=' . $end_date . '&to=' . $start_date }}">{{ $service['service']->service_name }}</a>
                                                                </div>
                                                                <div class="mb-4"> Keyword: <br><span
                                                                        class="font-italic">{{ $service['service']->keyword }}</span>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr class="bg-secondary text-white t_rev sticky-col second-col">
                                                            <td class="sticky-col second-col bg-secondary"
                                                                style="background-color: #6c757d"><strong>GMV
                                                                    ({{ $report['country']['currency_code'] }})
                                                                    <sup><i class="ml-3 text-dark fa fa-info-circle"
                                                                            title="Revenue that we charge user excluding tax"></i></sup></strong>
                                                            </td>
                                                            <td class="col-value"><span
                                                                    class="local_total_revenue">{{ numberConverter($service['t_rev']['total'], 2, 'pre') }}</span>
                                                            </td>
                                                            <td class="col-value">
                                                                {{ numberConverter($service['t_rev']['avg'], 2, 'pre') }}</td>
                                                            <td class="">
                                                                {{ numberConverter($service['t_rev']['t_mo_end'], 2, 'pre') }}
                                                            </td>

                                                            @foreach ($service['t_rev']['dates'] as $t_rev)
                                                                <td data-title="Company" class="col-value gross_revenue ">
                                                                    {{ numberConverter($t_rev['value'], 2, 'pre') }}</td>
                                                            @endforeach
                                                        </tr>
                                                        <tr class="t_rev sticky-col second-col">
                                                            <td class="sticky-col second-col"
                                                                style="background-color: #e3e3e4"><strong>GMV (USD)<sup><i
                                                                            class="ml-3 text-dark fa fa-info-circle"
                                                                            title="Revenue that we charge user excluding tax"></i></sup></strong>
                                                            </td>
                                                            <td class="col-value revenue_total_usd">{{ numberConverter($service['tur']['total'], 2, 'pre') }}
                                                            </td>
                                                            <td class="col-value">
                                                                {{ numberConverter($service['tur']['avg'], 2, 'pre') }}</td>
                                                            <td class="">
                                                                {{ numberConverter($service['tur']['t_mo_end'], 2, 'pre') }}
                                                            </td>

                                                            @foreach ($service['tur']['dates'] as $tur)
                                                                <td data-title="Company" class="col-value gross_revenue ">
                                                                    {{ numberConverter($tur['value'], 2, 'pre') }}</td>
                                                            @endforeach
                                                        </tr>
                                                        <tr class="t_rev sticky-col second-col">
                                                            <td class="sticky-col second-col"
                                                                style="background-color: #fdfdfe"><strong>G.Rev
                                                                    ({{ $report['country']['currency_code'] }})</strong></td>
                                                            <td class="col-value"><span
                                                                    class="local_total_revenue">{{ numberConverter($service['rev_after_share_usd']['total'], 2, 'pre') }}</span>
                                                            </td>
                                                            <td class="col-value">
                                                                {{ numberConverter($service['rev_after_share_usd']['avg'], 2, 'pre') }}
                                                            </td>
                                                            <td class="">
                                                                {{ numberConverter($service['rev_after_share_usd']['t_mo_end'], 2, 'pre') }}
                                                            </td>

                                                            @foreach ($service['rev_after_share_usd']['dates'] as $rev_after_share_usd)
                                                                <td data-title="Company" class="col-value gross_revenue ">
                                                                    {{ numberConverter($rev_after_share_usd['value'], 2, 'pre') }}
                                                                </td>
                                                            @endforeach
                                                        </tr>
                                                        <tr class="t_rev sticky-col second-col">
                                                            <td class="sticky-col second-col"
                                                                style="background-color: #e3e3e4"><strong>G.Rev (USD)</strong>
                                                            </td>
                                                            <td class="col-value"><span
                                                                    class="local_total_revenue">{{ numberConverter($service['usd_rev_after_share']['total'], 2, 'pre') }}</span>
                                                            </td>
                                                            <td class="col-value">
                                                                {{ numberConverter($service['usd_rev_after_share']['avg'], 2, 'pre') }}
                                                            </td>
                                                            <td class="">
                                                                {{ numberConverter($service['usd_rev_after_share']['t_mo_end'], 2, 'pre') }}
                                                            </td>

                                                            @foreach ($service['usd_rev_after_share']['dates'] as $usd_rev_after_share)
                                                                <td data-title="Company" class="col-value gross_revenue ">
                                                                    {{ numberConverter($usd_rev_after_share['value'], 2, 'pre') }}
                                                                </td>
                                                            @endforeach
                                                        </tr>

                                                        <tr class="">
                                                            <td class="sticky-col second-col"
                                                                style="background-color: #e3e3e4"><strong>N.REV (USD) <sup><i class="ml-3 text-dark fa fa-info-circle"
                                                                    title="Net Revenue = G.Rev(USD) - Other Tax Operator"></i></sup></strong>
                                                            </td>
                                                            <td class="col-value"><span
                                                                    class="local_total_revenue">{{ numberConverter($service['net_rev']['total'], 2, 'pre') }}</span>
                                                            </td>
                                                            <td class="col-value">
                                                                {{ numberConverter($service['net_rev']['avg'], 2, 'pre') }}
                                                            </td>
                                                            <td class="">
                                                                {{ numberConverter($service['net_rev']['t_mo_end'], 2, 'pre') }}
                                                            </td>

                                                            @foreach ($service['net_rev']['dates'] as $net)
                                                                <td data-title="Company" class="col-value gross_revenue ">
                                                                    {{ numberConverter($net['value'], 2, 'pre') }}</td>
                                                            @endforeach
                                                        </tr>

                                                        <tr class="t_subs sticky-col second-col">
                                                            <td class="sticky-col second-col"
                                                                style="background-color: #fdfdfe"><strong>Total
                                                                    SubActive</strong></td>
                                                            <td class="subactive_total col-value">
                                                                {{ numberConverter($service['t_sub']['total'], 0, 'pre') }}
                                                            </td>
                                                            <td class="">N/A</td>
                                                            <td class="">N/A</td>

                                                            @foreach ($service['t_sub']['dates'] as $t_sub)
                                                                <td data-title="Company" class="col-value subactive_data ">
                                                                    {{ numberConverter($t_sub['value'], 0, 'pre') }}</td>
                                                            @endforeach
                                                        </tr>
                                                        <tr class="t_reg sticky-col second-col">
                                                            <td class="sticky-col second-col"
                                                                style="background-color: #e3e3e4"><strong>Total Reg</strong>
                                                            </td>
                                                            <td class="col-value reg_total">
                                                                {{ numberConverter($service['reg']['total'], 0, 'pre') }}</td>
                                                            <td class="col-value">
                                                                {{ numberConverter($service['reg']['avg'], 0, 'pre') }}</td>
                                                            <td class="">
                                                                {{ numberConverter($service['reg']['t_mo_end'], 0, 'pre') }}
                                                            </td>

                                                            @foreach ($service['reg']['dates'] as $reg)
                                                                <td data-title="Company" class="col-value reg_data ">
                                                                    {{ numberConverter($reg['value'], 0, 'pre') }}</td>
                                                            @endforeach
                                                        </tr>
                                                        <tr class="t_unreg sticky-col second-col">
                                                            <td class="sticky-col second-col"
                                                                style="background-color: #fdfdfe"><strong>Total Unreg</strong>
                                                            </td>
                                                            <td class="col-value unreg_total">
                                                                {{ numberConverter($service['unreg']['total'], 0, 'pre') }}
                                                            </td>
                                                            <td class="col-value">
                                                                {{ numberConverter($service['unreg']['avg'], 0, 'pre') }}</td>
                                                            <td class="">
                                                                {{ numberConverter($service['unreg']['t_mo_end'], 0, 'pre') }}
                                                            </td>

                                                            @foreach ($service['unreg']['dates'] as $unreg)
                                                                <td data-title="Company" class="col-value unreg_data ">
                                                                    {{ numberConverter($unreg['value'], 0, 'pre') }}</td>
                                                            @endforeach
                                                        </tr>
                                                        <tr class="billing_rate">
                                                            <td><strong class="text-with-sup">
                                                                    <div class="billSeperateBtn pull-left" data-sign="plus"
                                                                        style="cursor: pointer;">+</div>
                                                                    <div class="text-left">Bill % </div>
                                                                </strong>
                                                                <div class="billDivs" data-sign="plus"></div>
                                                            </td>
                                                            <td class="">N/A</td>
                                                            <td class="br_avg col-value">
                                                                {{ numberConverter($service['bill']['avg'], 2, 'pre') }}</td>
                                                            <td class="br_monthly_msg col-value">N/A</td>

                                                            @foreach ($service['bill']['dates'] as $bill)
                                                                <td data-title="Company" class="col-value br_data ">
                                                                    {{ numberConverter($bill['value'], 2, 'pre') }}%</td>
                                                            @endforeach
                                                        </tr>
                                                        <tr class="billExtendedRows" style="display: none;">
                                                            <td class="sticky-col second-col"
                                                                style="background-color: #e3e3e4"><strong>First P.%</strong>
                                                            </td>
                                                            <td class="">N/A</td>
                                                            <td class="br_avg col-value">
                                                                {{ numberConverter($service['first_push']['avg'], 2, 'pre') }}
                                                            </td>
                                                            <td class="br_monthly_msg col-value">N/A</td>

                                                            @foreach ($service['first_push']['dates'] as $first_push)
                                                                <td data-title="Company" class="col-value br_data ">
                                                                    {{ numberConverter($first_push['value'], 2, 'pre') }}%
                                                                </td>
                                                            @endforeach
                                                        </tr>
                                                        <tr class="billExtendedRows" style="display: none;">
                                                            <td class="sticky-col second-col" style="background-color: #e3e3e4"><strong>Daily P.%</strong></td>
                                                            <td class="">N/A</td>
                                                            <td class="br_avg col-value">{{ numberConverter($service['daily_push']['avg'], 2, 'pre') }}</td>
                                                            <td class="br_monthly_msg col-value">N/A</td>

                                                            @foreach ($service['daily_push']['dates'] as $daily_push)
                                                            <td data-title="Company" class="col-value br_data ">{{ numberConverter($daily_push['value'], 2, 'pre') }}%</td>
                                                            @endforeach
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @endforeach
                                @endforeach
                            @endif
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
