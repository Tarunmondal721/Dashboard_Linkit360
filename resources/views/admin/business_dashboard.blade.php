@extends('layouts.admin')

@section('title')
    {{ __('Dashboard') }}
@endsection

@push('head')
    {{--  @if ($calenderTasks)
        <link rel="stylesheet" href="{{asset('assets/libs/fullcalendar/dist/fullcalendar.min.css')}}">
    @endif --}}
@endpush
@section('content')
    <div class="page-content">
        <div class="page-title" style="margin-bottom:25px">
            <div class="row justify-content-between align-items-center">
                <div
                    class="col-xl-4 col-lg-4 col-md-4 d-flex align-items-center justify-content-between justify-content-md-start mb-3 mb-md-0">
                    <div class="d-inline-block">
                        <h5 class="h4 d-inline-block font-weight-400 mb-0"><b>Dashboard</b></h5><br>
                        <p class="d-inline-block font-weight-200 mb-0">Reports and Stats for Today</p>
                    </div>
                </div>
                <div
                    class="col-xl-8 col-lg-8 col-md-8 d-flex align-items-center justify-content-between justify-content-md-end">
                </div>
            </div>
        </div>

        @include('admin.partials.filter')
        @include('admin.pie')

        <div id="excelData2">
            <div class="dashboard-data">
                <div id="dashboardCountrydata" class="dash">


                    <div class="card shadow-sm ">

                        <div class="d-flex align-items-center my-2">
                            <span class="badge badge-secondary px-2 bg-primary">
                                All Business({{ count($sumemry) }}) |
                                Operators({{ isset($allDataSum) ? $allDataSum['operator_count'] : '' }}) </span>

                            <span class="flex-fill ml-2 bg-primary w-100 font-weight-bold" style="height: 1px;"></span>
                        </div>
                        <div class="card-body" style="overflow: scroll;">
                            <div class="card-text">
                                <table width="100%" id="dtbl">
                                    <thead>
                                        <tr>
                                            <th width="10%"></th>
                                            <th class="p-1 text-center gradient" width="22.5%">Current Month
                                                (<?= date('M Y') ?>)</th>
                                            <th class="p-1 text-center gradient-green" width="22.5%">Estimated EOM
                                                (<?= date('M Y') ?>)</th>
                                            <th class="p-1 text-center gradient-red" width="22.5%">Last Month
                                                (<?= date('M Y', strtotime('-1 month')) ?>)</th>
                                            <th class="p-1 text-center gradient-purple" width="22.5%">Previous Month
                                                (<?= date('M Y', strtotime('-2 month')) ?>)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (isset($allDataSum) && !empty($allDataSum))
                                            <tr>
                                                <th>GMV/AVG GMV<sup><i class="ml-2 mr-2 mt-1 text-dark fa fa-info-circle"
                                                            title="End User Revenue Before Share / Average End User Revenue Before Share"></i></sup>
                                                </th>
                                                <td class="p-1 gray-bg ">
                                                    <div class="inter"><span
                                                            class="{{ $allDataSum['current_revenue_usd_class'] }} intermate current_revenue">{{ numberConverter($allDataSum['current_revenue_usd'], 2) }}(USD)
                                                            <i
                                                                class="fa {{ $allDataSum['current_revenue_usd_arrow'] }}"></i>&nbsp;</span><span
                                                            class="{{ $allDataSum['estimated_avg_revenue_usd_class'] }}">{{ numberConverter($allDataSum['current_avg_revenue_usd'], 2) }}(USD)<i
                                                                class="fa {{ $allDataSum['estimated_avg_revenue_usd_arrow'] }}"></i>&nbsp;</span>
                                                    </div>
                                                </td>
                                                <td class="p-1">
                                                    <div class="inter"><span
                                                            class="{{ $allDataSum['estimated_revenue_usd_class'] }} intermate">{{ numberConverter($allDataSum['estimated_revenue_usd'], 2) }}(USD)
                                                            <i
                                                                class="fa {{ $allDataSum['estimated_revenue_usd_arrow'] }}"></i>&nbsp;<small>{{ $allDataSum['estimated_revenue_usd_percentage'] }}%</small></span><span
                                                            class="{{ $allDataSum['estimated_avg_revenue_usd_class'] }}">{{ numberConverter($allDataSum['estimated_avg_revenue_usd'], 2) }}(USD)<i
                                                                class="fa {{ $allDataSum['estimated_avg_revenue_usd_arrow'] }}"></i>&nbsp;<small>{{ $allDataSum['estimated_avg_revenue_usd_percentage'] }}%</small></span>
                                                    </div>
                                                </td>
                                                <td class="p-1 gray-bg">
                                                    <div class="inter"><span
                                                            class="{{ $allDataSum['last_revenue_usd_class'] }} intermate">{{ numberConverter($allDataSum['last_revenue_usd'], 2) }}(USD)
                                                            <i
                                                                class="fa {{ $allDataSum['last_revenue_usd_arrow'] }}"></i>&nbsp;<small>{{ $allDataSum['last_revenue_usd_percentage'] }}%</small></span><span
                                                            class="{{ $allDataSum['last_avg_revenue_usd_class'] }}">{{ numberConverter($allDataSum['last_avg_revenue_usd'], 2) }}(USD)<i
                                                                class="fa {{ $allDataSum['last_avg_revenue_usd_arrow'] }}"></i>&nbsp;<small>{{ $allDataSum['last_avg_revenue_usd_percentage'] }}%</small></span>
                                                    </div>
                                                </td>
                                                <td class="p-1">
                                                    <div class="inter"><span
                                                            class="intermate">{{ numberConverter($allDataSum['prev_revenue_usd'], 2) }}(USD)</span><span>{{ numberConverter($allDataSum['prev_avg_revenue_usd'], 2) }}(USD)</span>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>G.Rev/AVG G.Rev<sup><i
                                                            class="ml-2 mr-2 mt-1 text-dark fa fa-info-circle"
                                                            title="Gross Revenue / Average Gross Revenue"></i></sup></th>
                                                <td class="p-1 gray-bg ">
                                                    <div class="inter"><span
                                                            class="{{ $allDataSum['current_gross_revenue_usd_class'] }} intermate current_gross_revenue">{{ numberConverter($allDataSum['current_gross_revenue_usd'], 2) }}(USD)
                                                            <i
                                                                class="fa {{ $allDataSum['current_gross_revenue_usd_arrow'] }}"></i>&nbsp;</span><span
                                                            class="{{ $allDataSum['estimated_avg_gross_revenue_usd_class'] }}">{{ numberConverter($allDataSum['current_avg_gross_revenue_usd'], 2) }}(USD)<i
                                                                class="fa {{ $allDataSum['estimated_avg_gross_revenue_usd_arrow'] }}"></i>&nbsp;</span>
                                                    </div>
                                                </td>
                                                <td class="p-1">
                                                    <div class="inter"><span
                                                            class="{{ $allDataSum['estimated_gross_revenue_usd_class'] }} intermate">{{ numberConverter($allDataSum['estimated_gross_revenue_usd'], 2) }}(USD)
                                                            <i
                                                                class="fa {{ $allDataSum['estimated_gross_revenue_usd_arrow'] }}"></i>&nbsp;<small>{{ $allDataSum['estimated_gross_revenue_usd_percentage'] }}%</small></span><span
                                                            class="{{ $allDataSum['estimated_avg_gross_revenue_usd_class'] }}">{{ numberConverter($allDataSum['estimated_avg_gross_revenue_usd'], 2) }}(USD)<i
                                                                class="fa {{ $allDataSum['estimated_avg_gross_revenue_usd_arrow'] }}"></i>&nbsp;<small>{{ $allDataSum['estimated_avg_gross_revenue_usd_percentage'] }}%</small></span>
                                                    </div>
                                                </td>
                                                <td class="p-1 gray-bg">
                                                    <div class="inter"><span
                                                            class="{{ $allDataSum['last_gross_revenue_usd_class'] }} intermate">{{ numberConverter($allDataSum['last_gross_revenue_usd'], 2) }}(USD)
                                                            <i
                                                                class="fa {{ $allDataSum['last_gross_revenue_usd_arrow'] }}"></i>&nbsp;<small>{{ $allDataSum['last_gross_revenue_usd_percentage'] }}%</small></span><span
                                                            class="{{ $allDataSum['last_avg_gross_revenue_usd_class'] }}">{{ numberConverter($allDataSum['last_avg_gross_revenue_usd'], 2) }}(USD)<i
                                                                class="fa {{ $allDataSum['last_avg_gross_revenue_usd_arrow'] }}"></i>&nbsp;<small>{{ $allDataSum['last_avg_gross_revenue_usd_percentage'] }}%</small></span>
                                                    </div>
                                                </td>
                                                <td class="p-1">
                                                    <div class="inter"><span
                                                            class="intermate">{{ numberConverter($allDataSum['prev_gross_revenue_usd'], 2) }}(USD)</span><span>{{ numberConverter($allDataSum['prev_avg_gross_revenue_usd'], 2) }}(USD)</span>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>N.Rev/AVG N.Rev<sup><i
                                                            class="ml-2 mr-2 mt-1 text-dark fa fa-info-circle"
                                                            title="Net Revenue / Average Net Revenue"></i></sup></th>
                                                <td class="p-1 gray-bg ">
                                                    <div class="inter"><span
                                                            class="{{ $allDataSum['current_net_revenue_usd_class'] }} intermate current_net_revenue">{{ numberConverter($allDataSum['current_net_revenue_usd'], 2) }}(USD)
                                                            <i
                                                                class="fa {{ $allDataSum['current_net_revenue_usd_arrow'] }}"></i>&nbsp;</span><span
                                                            class="{{ $allDataSum['estimated_avg_net_revenue_usd_class'] }}">{{ numberConverter($allDataSum['current_avg_net_revenue_usd'], 2) }}(USD)<i
                                                                class="fa {{ $allDataSum['estimated_avg_net_revenue_usd_arrow'] }}"></i>&nbsp;</span>
                                                    </div>
                                                </td>
                                                <td class="p-1">
                                                    <div class="inter"><span
                                                            class="{{ $allDataSum['estimated_net_revenue_usd_class'] }} intermate">{{ numberConverter($allDataSum['estimated_net_revenue_usd'], 2) }}(USD)
                                                            <i
                                                                class="fa {{ $allDataSum['estimated_net_revenue_usd_arrow'] }}"></i>&nbsp;<small>{{ $allDataSum['estimated_net_revenue_usd_percentage'] }}%</small></span><span
                                                            class="{{ $allDataSum['estimated_avg_net_revenue_usd_class'] }}">{{ numberConverter($allDataSum['estimated_avg_net_revenue_usd'], 2) }}(USD)<i
                                                                class="fa {{ $allDataSum['estimated_avg_net_revenue_usd_arrow'] }}"></i>&nbsp;<small>{{ $allDataSum['estimated_avg_net_revenue_usd_percentage'] }}%</small></span>
                                                    </div>
                                                </td>
                                                <td class="p-1 gray-bg">
                                                    <div class="inter"><span
                                                            class="{{ $allDataSum['last_net_revenue_usd_class'] }} intermate">{{ numberConverter($allDataSum['last_net_revenue_usd'], 2) }}(USD)
                                                            <i
                                                                class="fa {{ $allDataSum['last_net_revenue_usd_arrow'] }}"></i>&nbsp;<small>{{ $allDataSum['last_net_revenue_usd_percentage'] }}%</small></span><span
                                                            class="{{ $allDataSum['last_avg_net_revenue_usd_class'] }}">{{ numberConverter($allDataSum['last_avg_net_revenue_usd'], 2) }}(USD)<i
                                                                class="fa {{ $allDataSum['last_avg_net_revenue_usd_arrow'] }}"></i>&nbsp;<small>{{ $allDataSum['last_avg_net_revenue_usd_percentage'] }}%</small></span>
                                                    </div>
                                                </td>
                                                <td class="p-1">
                                                    <div class="inter"><span
                                                            class="intermate">{{ numberConverter($allDataSum['prev_net_revenue_usd'], 2) }}(USD)</span><span>{{ numberConverter($allDataSum['prev_avg_net_revenue_usd'], 2) }}(USD)</span>
                                                    </div>
                                                </td>
                                            </tr>

                                            <tr>
                                                <th>REG/AVG REG/C.MO<sup><i
                                                            class="ml-2 mr-2 mt-1 text-dark fa fa-info-circle"
                                                            title="reg / average reg / campaign MO"></i></sup></th>
                                                <td class="p-1 gray-bg ">
                                                    <div class="inter"><span
                                                            class="{{ $allDataSum['current_total_mo_class'] }} intermate current_total_mo">{{ number_format(round($allDataSum['current_total_mo'])) }}<i
                                                                class="fa {{ $allDataSum['current_total_mo_arrow'] }}"></i>&nbsp;</span><span
                                                            class="{{ $allDataSum['estimated_avg_mo_class'] }} intermate">{{ number_format(round($allDataSum['current_avg_mo'])) }}<i
                                                                class="fa {{ $allDataSum['estimated_avg_mo_arrow'] }}"></i>&nbsp;</span><span
                                                            class="{{ $allDataSum['current_mo_class'] }}">{{ number_format(round($allDataSum['current_mo'])) }}<i
                                                                class="fa {{ $allDataSum['current_mo_arrow'] }}"></i>&nbsp;</span>
                                                    </div>
                                                </td>
                                                <td class="p-1">
                                                    <div class="inter"><span
                                                            class="{{ $allDataSum['estimated_total_mo_class'] }} intermate">{{ number_format(round($allDataSum['estimated_total_mo'])) }}<i
                                                                class="fa {{ $allDataSum['estimated_total_mo_arrow'] }}"></i>&nbsp;<small>{{ $allDataSum['estimated_total_mo_percentage'] }}%</small></span><span
                                                            class="{{ $allDataSum['estimated_avg_mo_class'] }} intermate">{{ number_format(round($allDataSum['estimated_avg_mo'])) }}<i
                                                                class="fa {{ $allDataSum['estimated_avg_mo_arrow'] }}"></i>&nbsp;<small>{{ $allDataSum['estimated_avg_mo_percentage'] }}%</small></span><span
                                                            class="{{ $allDataSum['estimated_mo_class'] }}">{{ number_format(round($allDataSum['estimated_mo'])) }}<i
                                                                class="fa {{ $allDataSum['estimated_mo_arrow'] }}"></i>&nbsp;<small>{{ $allDataSum['estimated_mo_percentage'] }}%</small></span>
                                                    </div>
                                                </td>
                                                <td class="p-1 gray-bg">
                                                    <div class="inter"><span
                                                            class="{{ $allDataSum['last_total_mo_class'] }} intermate">{{ number_format(round($allDataSum['last_total_mo'])) }}<i
                                                                class="fa {{ $allDataSum['last_total_mo_arrow'] }}"></i>&nbsp;<small>{{ $allDataSum['last_total_mo_percentage'] }}%</small></span><span
                                                            class="{{ $allDataSum['last_avg_mo_class'] }} intermate">{{ number_format(round($allDataSum['last_avg_mo'])) }}<i
                                                                class="fa {{ $allDataSum['last_avg_mo_arrow'] }}"></i>&nbsp;<small>{{ $allDataSum['last_avg_mo_percentage'] }}%</small></span><span
                                                            class="{{ $allDataSum['last_mo_class'] }}">{{ number_format(round($allDataSum['last_mo'])) }}<i
                                                                class="fa {{ $allDataSum['last_mo_arrow'] }}"></i>&nbsp;<small>{{ $allDataSum['last_mo_percentage'] }}%</small></span>
                                                    </div>
                                                </td>
                                                <td class="p-1">
                                                    <div class="inter"><span
                                                            class="intermate">{{ number_format(round($allDataSum['prev_total_mo'])) }}</span><span
                                                            class="intermate">{{ number_format(round($allDataSum['prev_avg_mo'])) }}</span><span>{{ number_format(round($allDataSum['prev_mo'])) }}</span>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>
                                                    <div class="inter">Cost Campaign / Price.MO</div>
                                                </th>
                                                <td class="p-1 gray-bg current_month_costCampaign">
                                                    <div class="inter"><span
                                                            class="{{ $allDataSum['current_cost_class'] }} intermate current_cost">{{ numberConverter($allDataSum['current_cost'], 3) }}<i
                                                                class="fa {{ $allDataSum['current_cost_arrow'] }}"></i>&nbsp;</span><span
                                                            class="{{ $allDataSum['current_price_mo_class'] }}">{{ numberConverter($allDataSum['current_price_mo'], 3) }}</span>
                                                    </div>
                                                </td>
                                                <td class="p-1">
                                                    <div class="inter"><span
                                                            class="{{ $allDataSum['estimated_cost_class'] }} intermate">{{ numberConverter($allDataSum['estimated_cost'], 3) }}<i
                                                                class="fa {{ $allDataSum['estimated_cost_arrow'] }}"></i>&nbsp;<small>{{ $allDataSum['estimated_cost_percentage'] }}%</small></span><span
                                                            class="{{ $allDataSum['estimated_price_mo_class'] }}">{{ numberConverter($allDataSum['estimated_price_mo'], 3) }}<i
                                                                class="fa {{ $allDataSum['estimated_price_mo_arrow'] }}"></i>&nbsp;<small>{{ $allDataSum['estimated_price_mo_percentage'] }}%</small></span>
                                                    </div>
                                                </td>
                                                <td class="p-1 gray-bg">
                                                    <div class="inter"><span
                                                            class="{{ $allDataSum['last_cost_class'] }} intermate">{{ numberConverter($allDataSum['last_cost'], 3) }}<i
                                                                class="fa {{ $allDataSum['last_cost_arrow'] }}"></i>&nbsp;<small>{{ $allDataSum['last_cost_percentage'] }}%</small></span><span
                                                            class="{{ $allDataSum['last_price_mo_class'] }}">{{ numberConverter($allDataSum['last_price_mo'], 3) }}<i
                                                                class="fa {{ $allDataSum['last_price_mo_arrow'] }}"></i>&nbsp;<small>{{ $allDataSum['last_price_mo_percentage'] }}%</small></span>
                                                    </div>
                                                </td>
                                                <td class="p-1">
                                                    <div class="inter"><span
                                                            class="intermate">{{ numberConverter($allDataSum['prev_cost'], 3) }}</span><span>{{ numberConverter($allDataSum['prev_price_mo'], 3) }}</span>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>ROI<sup><i class="ml-2 mr-2 mt-1 text-dark fa fa-info-circle"
                                                            title="(cost campaign/mo)/ (share/reg)"></i></sup>/ US30ARPU
                                                </th>
                                                <td class="p-1 gray-bg current_month_roi">
                                                    <div class="inter"><span
                                                            class="{{ $allDataSum['currentMonthROI_class'] }} intermate currentMonthROI">{{ numberConverter($allDataSum['currentMonthROI'], 4, 'pre') }}<i
                                                                class="fa {{ $allDataSum['currentMonthROI_arrow'] }}"></i>&nbsp;</span><span
                                                            class="{{ $allDataSum['current_30_arpu_class'] }}">{{ numberConverter($allDataSum['current_30_arpu'], 4, 'pre') }}<i
                                                                class="fa {{ $allDataSum['current_30_arpu_arrow'] }}"></i>&nbsp;</span>
                                                    </div>
                                                </td>
                                                <td class="p-1">
                                                    <div class="inter"><span
                                                            class="{{ $allDataSum['estimatedMonthROI_class'] }} intermate">{{ numberConverter($allDataSum['estimatedMonthROI'], 4, 'pre') }}<i
                                                                class="fa {{ $allDataSum['estimatedMonthROI_arrow'] }}"></i>&nbsp;<small>{{ $allDataSum['estimatedMonthROI_percentage'] }}%</small></span><span
                                                            class="{{ $allDataSum['estimated_30_arpu_class'] }}">{{ numberConverter($allDataSum['estimated_30_arpu'], 4, 'pre') }}<i
                                                                class="fa {{ $allDataSum['estimated_30_arpu_arrow'] }}"></i>&nbsp;<small>{{ $allDataSum['estimated_30_arpu_percentage'] }}%</small></span>
                                                    </div>
                                                </td>
                                                <td class="p-1 gray-bg">
                                                    <div class="inter"><span
                                                            class="{{ $allDataSum['lastMonthROI_class'] }} intermate">{{ numberConverter($allDataSum['lastMonthROI'], 4, 'pre') }}<i
                                                                class="fa {{ $allDataSum['lastMonthROI_arrow'] }}"></i>&nbsp;<small>{{ $allDataSum['lastMonthROI_percentage'] }}%</small></span><span
                                                            class="{{ $allDataSum['last_30_arpu_class'] }}">{{ numberConverter($allDataSum['last_30_arpu'], 4, 'pre') }}<i
                                                                class="fa {{ $allDataSum['last_30_arpu_arrow'] }}"></i>&nbsp;<small>{{ $allDataSum['last_30_arpu_percentage'] }}%</small></span>
                                                    </div>
                                                </td>
                                                <td class="p-1">
                                                    <div class="inter"><span
                                                            class="intermate">{{ numberConverter($allDataSum['previousMonthROI'], 4, 'pre') }}</span><span>{{ numberConverter($allDataSum['prev_30_arpu'], 4, 'pre') }}</span>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>GP/AVG GP<sup><i class="ml-2 mr-2 mt-1 text-dark fa fa-info-circle"
                                                            title="Gross Profit = Gross Revenue - (cost campaign + other cost) / Average Gross Profit"></i></sup>
                                                </th>
                                                <td class="p-1 gray-bg ">
                                                    <div class="inter"><span
                                                            class="{{ $allDataSum['current_pnl_class'] }} intermate current_pnl">{{ number_format(round($allDataSum['current_pnl'])) }}<i
                                                                class="fa {{ $allDataSum['current_pnl_arrow'] }}"></i>&nbsp;</span><span
                                                            class="{{ $allDataSum['estimated_avg_pnl_class'] }}">{{ number_format(round($allDataSum['current_avg_pnl'])) }}<i
                                                                class="fa {{ $allDataSum['estimated_avg_pnl_arrow'] }}"></i>&nbsp;</span>
                                                    </div>
                                                </td>
                                                <td class="p-1">
                                                    <div class="inter"><span
                                                            class="{{ $allDataSum['estimated_pnl_class'] }} intermate">{{ number_format(round($allDataSum['estimated_pnl'])) }}<i
                                                                class="fa {{ $allDataSum['estimated_pnl_arrow'] }}"></i>&nbsp;<small>{{ $allDataSum['estimated_pnl_percentage'] }}%</small></span><span
                                                            class="{{ $allDataSum['estimated_avg_pnl_class'] }}">{{ number_format(round($allDataSum['estimated_avg_pnl'])) }}<i
                                                                class="fa {{ $allDataSum['estimated_avg_pnl_arrow'] }}"></i>&nbsp;<small>{{ $allDataSum['estimated_avg_pnl_percentage'] }}%</small></span>
                                                    </div>
                                                </td>
                                                <td class="p-1 gray-bg">
                                                    <div class="inter"><span
                                                            class="{{ $allDataSum['last_pnl_class'] }} intermate">{{ number_format(round($allDataSum['last_pnl'])) }}<i
                                                                class="fa {{ $allDataSum['last_pnl_arrow'] }}"></i>&nbsp;<small>{{ $allDataSum['last_pnl_percentage'] }}%</small></span><span
                                                            class="{{ $allDataSum['last_avg_pnl_class'] }}">{{ number_format(round($allDataSum['last_avg_pnl'])) }}<i
                                                                class="fa {{ $allDataSum['last_avg_pnl_arrow'] }}"></i>&nbsp;<small>{{ $allDataSum['last_avg_pnl_percentage'] }}%</small></span>
                                                    </div>
                                                </td>
                                                <td class="p-1">
                                                    <div class="inter"><span
                                                            class="intermate">{{ number_format(round($allDataSum['prev_pnl'])) }}</span><span>{{ number_format(round($allDataSum['prev_avg_pnl'])) }}</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    @if (isset($sumemry) && !empty($sumemry))
                        @foreach ($sumemry as $report)
                            <div class="card shadow-sm revfilter">

                                <div class="d-flex align-items-center my-2">
                                    <span class="badge badge-secondary px-2 bg-primary">
                                        {{-- &nbsp;<a
                                            href="{{ route('report.daily.company.pnldetails', 'company=' . $report['company']->id) }}"
                                            class="text-white"> --}}
                                            {{ $report['country']['country'] }} |
                                            Operators({{ $report['operator_count'] }}) | Last Update:
                                            {{ $report['updated_at'] }} </a></span>
                                    <span class="flex-fill ml-2 bg-primary w-100 font-weight-bold"
                                        style="height: 1px;"></span>
                                </div>
                                <div class="card-body" style="overflow: scroll;">
                                    <div class="card-text">
                                        <table width="100%">
                                            <thead>
                                                <tr>
                                                    <th width="10%"></th>
                                                    <th class="p-1 text-center gradient" width="22.5%">Current Month
                                                        (<?= date('M Y') ?>)</th>
                                                    <th class="p-1 text-center gradient-green" width="22.5%">Estimated
                                                        EOM (<?= date('M Y') ?>)</th>
                                                    <th class="p-1 text-center gradient-red" width="22.5%">Last Month
                                                        (<?= date('M Y', strtotime('-1 month')) ?>)</th>
                                                    <th class="p-1 text-center gradient-purple" width="22.5%">Previous
                                                        Month (<?= date('M Y', strtotime('-2 month')) ?>)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <th>GMV/AVG GMV<sup><i
                                                                class="ml-2 mr-2 mt-1 text-dark fa fa-info-circle"
                                                                title="End User Revenue Before Share / Average End User Revenue Before Share"></i></sup>
                                                    </th>
                                                    <td class="p-1 gray-bg current_month_revenue">
                                                        <div class="inter"><span
                                                                class="{{ $report['current_revenue_usd_class'] }} intermate">{{ numberConverter($report['current_revenue_usd'], 2) }}(USD)<i
                                                                    class="fa {{ $report['current_revenue_usd_arrow'] }}"></i>&nbsp;</span><span
                                                                class="{{ $report['estimated_avg_revenue_usd_class'] }}">{{ numberConverter($report['current_avg_revenue_usd'], 2) }}(USD)<i
                                                                    class="fa {{ $report['estimated_avg_revenue_usd_arrow'] }}"></i>&nbsp;</span>
                                                        </div>
                                                    </td>

                                                    <td class="p-1">
                                                        <div class="inter"><span
                                                                class="{{ $report['estimated_revenue_usd_class'] }} intermate">{{ numberConverter($report['estimated_revenue_usd'], 2) }}(USD)<i
                                                                    class="fa {{ $report['estimated_revenue_usd_arrow'] }}"></i>&nbsp;<small>{{ $report['estimated_revenue_usd_percentage'] }}%</small></span><span
                                                                class="{{ $report['estimated_avg_revenue_usd_class'] }}">{{ numberConverter($report['estimated_avg_revenue_usd'], 2) }}(USD)<i
                                                                    class="fa {{ $report['estimated_avg_revenue_usd_arrow'] }}"></i>&nbsp;<small>{{ $report['estimated_avg_revenue_usd_percentage'] }}%</small></span>
                                                        </div>
                                                    </td>

                                                    <td class="p-1 gray-bg">
                                                        <div class="inter"><span
                                                                class="{{ $report['last_revenue_usd_class'] }} intermate">{{ numberConverter($report['last_revenue_usd'], 2) }}(USD)<i
                                                                    class="fa {{ $report['last_revenue_usd_arrow'] }}"></i>&nbsp;<small>{{ $report['last_revenue_usd_percentage'] }}%</small></span><span
                                                                class="{{ $report['last_avg_revenue_usd_class'] }}">{{ numberConverter($report['last_avg_revenue_usd'], 2) }}(USD)<i
                                                                    class="fa {{ $report['last_avg_revenue_usd_arrow'] }}"></i>&nbsp;<small>{{ $report['last_avg_revenue_usd_percentage'] }}%</small></span>
                                                        </div>
                                                    </td>
                                                    <td class="p-1">
                                                        <div class="inter"><span
                                                                class="intermate">{{ numberConverter($report['prev_revenue_usd'], 2) }}(USD)</span><span>{{ numberConverter($report['prev_avg_revenue_usd'], 2) }}(USD)</span>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>G.Rev/AVG G.Rev<sup><i
                                                                class="ml-2 mr-2 mt-1 text-dark fa fa-info-circle"
                                                                title="Gross Revenue / Average Gross Revenue"></i></sup>
                                                    </th>
                                                    <td class="p-1 gray-bg ">
                                                        <div class="inter"><span
                                                                class="{{ $report['current_gross_revenue_usd_class'] }} intermate">{{ numberConverter($report['current_gross_revenue_usd'], 2) }}(USD)
                                                                <i
                                                                    class="fa {{ $report['current_gross_revenue_usd_arrow'] }}"></i>&nbsp;</span><span
                                                                class="{{ $report['estimated_avg_gross_revenue_usd_class'] }}">{{ numberConverter($report['current_avg_gross_revenue_usd'], 2) }}(USD)<i
                                                                    class="fa {{ $report['estimated_avg_gross_revenue_usd_arrow'] }}"></i>&nbsp;</span>
                                                        </div>
                                                    </td>
                                                    <td class="p-1">
                                                        <div class="inter"><span
                                                                class="{{ $report['estimated_gross_revenue_usd_class'] }} intermate">{{ numberConverter($report['estimated_gross_revenue_usd'], 2) }}(USD)
                                                                <i
                                                                    class="fa {{ $report['estimated_gross_revenue_usd_arrow'] }}"></i>&nbsp;<small>{{ $report['estimated_gross_revenue_usd_percentage'] }}%</small></span><span
                                                                class="{{ $report['estimated_avg_gross_revenue_usd_class'] }}">{{ numberConverter($report['estimated_avg_gross_revenue_usd'], 2) }}(USD)<i
                                                                    class="fa {{ $report['estimated_avg_gross_revenue_usd_arrow'] }}"></i>&nbsp;<small>{{ $report['estimated_avg_gross_revenue_usd_percentage'] }}%</small></span>
                                                        </div>
                                                    </td>
                                                    <td class="p-1 gray-bg">
                                                        <div class="inter"><span
                                                                class="{{ $report['last_gross_revenue_usd_class'] }} intermate">{{ numberConverter($report['last_gross_revenue_usd'], 2) }}(USD)
                                                                <i
                                                                    class="fa {{ $report['last_gross_revenue_usd_arrow'] }}"></i>&nbsp;<small>{{ $report['last_gross_revenue_usd_percentage'] }}%</small></span><span
                                                                class="{{ $report['last_avg_gross_revenue_usd_class'] }}">{{ numberConverter($report['last_avg_gross_revenue_usd'], 2) }}(USD)<i
                                                                    class="fa {{ $report['last_avg_gross_revenue_usd_arrow'] }}"></i>&nbsp;<small>{{ $report['last_avg_gross_revenue_usd_percentage'] }}%</small></span>
                                                        </div>
                                                    </td>
                                                    <td class="p-1">
                                                        <div class="inter"><span
                                                                class="intermate">{{ numberConverter($report['prev_gross_revenue_usd'], 2) }}(USD)</span><span>{{ numberConverter($report['prev_avg_gross_revenue_usd'], 2) }}(USD)</span>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>N.Rev/AVG N.Rev<sup><i
                                                                class="ml-2 mr-2 mt-1 text-dark fa fa-info-circle"
                                                                title="Net Revenue / Average Net Revenue"></i></sup></th>
                                                    <td class="p-1 gray-bg ">
                                                        <div class="inter"><span
                                                                class="{{ $report['current_net_revenue_usd_class'] }} intermate">{{ numberConverter($report['current_net_revenue_usd'], 2) }}(USD)
                                                                <i
                                                                    class="fa {{ $report['current_net_revenue_usd_arrow'] }}"></i>&nbsp;</span><span
                                                                class="{{ $report['estimated_avg_net_revenue_usd_class'] }}">{{ numberConverter($report['current_avg_net_revenue_usd'], 2) }}(USD)<i
                                                                    class="fa {{ $report['estimated_avg_net_revenue_usd_arrow'] }}"></i>&nbsp;</span>
                                                        </div>
                                                    </td>
                                                    <td class="p-1">
                                                        <div class="inter"><span
                                                                class="{{ $report['estimated_net_revenue_usd_class'] }} intermate">{{ numberConverter($report['estimated_net_revenue_usd'], 2) }}(USD)
                                                                <i
                                                                    class="fa {{ $report['estimated_net_revenue_usd_arrow'] }}"></i>&nbsp;<small>{{ $report['estimated_net_revenue_usd_percentage'] }}%</small></span><span
                                                                class="{{ $report['estimated_avg_net_revenue_usd_class'] }}">{{ numberConverter($report['estimated_avg_net_revenue_usd'], 2) }}(USD)<i
                                                                    class="fa {{ $report['estimated_avg_net_revenue_usd_arrow'] }}"></i>&nbsp;<small>{{ $report['estimated_avg_net_revenue_usd_percentage'] }}%</small></span>
                                                        </div>
                                                    </td>
                                                    <td class="p-1 gray-bg">
                                                        <div class="inter"><span
                                                                class="{{ $report['last_net_revenue_usd_class'] }} intermate">{{ numberConverter($report['last_net_revenue_usd'], 2) }}(USD)
                                                                <i
                                                                    class="fa {{ $report['last_net_revenue_usd_arrow'] }}"></i>&nbsp;<small>{{ $report['last_net_revenue_usd_percentage'] }}%</small></span><span
                                                                class="{{ $report['last_avg_net_revenue_usd_class'] }}">{{ numberConverter($report['last_avg_net_revenue_usd'], 2) }}(USD)<i
                                                                    class="fa {{ $report['last_avg_net_revenue_usd_arrow'] }}"></i>&nbsp;<small>{{ $report['last_avg_net_revenue_usd_percentage'] }}%</small></span>
                                                        </div>
                                                    </td>
                                                    <td class="p-1">
                                                        <div class="inter"><span
                                                                class="intermate">{{ numberConverter($report['prev_net_revenue_usd'], 2) }}(USD)</span><span>{{ numberConverter($report['prev_avg_net_revenue_usd'], 2) }}(USD)</span>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>REG/AVG REG/C.MO<sup><i
                                                                class="ml-2 mr-2 mt-1 text-dark fa fa-info-circle"
                                                                title="reg / average reg / campaign MO"></i></sup></th>
                                                    <td class="p-1 gray-bg current_month_mo">
                                                        <div class="inter"><span
                                                                class="{{ $report['current_total_mo_class'] }} intermate">{{ number_format(round($report['current_total_mo'])) }}<i
                                                                    class="fa {{ $report['current_total_mo_arrow'] }}"></i>&nbsp;</span><span
                                                                class="{{ $report['estimated_avg_mo_class'] }} intermate">{{ number_format(round($report['current_avg_mo'])) }}<i
                                                                    class="fa {{ $report['estimated_avg_mo_arrow'] }}"></i>&nbsp;</span><span
                                                                class="{{ $report['current_mo_class'] }}">{{ numberConverter($report['current_mo'], 2, 'pre') }}<i
                                                                    class="fa {{ $report['current_mo_arrow'] }}"></i>&nbsp;</span>
                                                        </div>
                                                    </td>
                                                    <td class="p-1">
                                                        <div class="inter"><span
                                                                class="{{ $report['estimated_total_mo_class'] }} intermate">{{ number_format(round($report['estimated_total_mo'])) }}<i
                                                                    class="fa {{ $report['estimated_total_mo_arrow'] }}"></i>&nbsp;<small>{{ $report['estimated_total_mo_percentage'] }}%</small></span><span
                                                                class="{{ $report['estimated_avg_mo_class'] }} intermate">{{ number_format(round($report['estimated_avg_mo'])) }}<i
                                                                    class="fa {{ $report['estimated_avg_mo_arrow'] }}"></i>&nbsp;<small>{{ $report['estimated_avg_mo_percentage'] }}%</small></span><span
                                                                class="{{ $report['estimated_mo_class'] }}">{{ numberConverter($report['estimated_mo'], 2, 'pre') }}<i
                                                                    class="fa {{ $report['estimated_mo_arrow'] }}"></i>&nbsp;<small>{{ $report['estimated_mo_percentage'] }}%</small></span>
                                                        </div>
                                                    </td>
                                                    <td class="p-1 gray-bg">
                                                        <div class="inter"><span
                                                                class="{{ $report['last_total_mo_class'] }} intermate">{{ number_format(round($report['last_total_mo'])) }}<i
                                                                    class="fa {{ $report['last_total_mo_arrow'] }}"></i>&nbsp;<small>{{ $report['last_total_mo_percentage'] }}%</small></span><span
                                                                class="{{ $report['last_avg_mo_class'] }} intermate">{{ number_format(round($report['last_avg_mo'])) }}<i
                                                                    class="fa {{ $report['last_avg_mo_arrow'] }}"></i>&nbsp;<small>{{ $report['last_avg_mo_percentage'] }}%</small></span><span
                                                                class="{{ $report['last_mo_class'] }}">{{ numberConverter($report['last_mo'], 2, 'pre') }}<i
                                                                    class="fa {{ $report['last_mo_arrow'] }}"></i>&nbsp;<small>{{ $report['last_mo_percentage'] }}%</small></span>
                                                        </div>
                                                    </td>
                                                    <td class="p-1">
                                                        <div class="inter"><span
                                                                class="intermate">{{ number_format(round($report['prev_total_mo'])) }}</span><span
                                                                class="intermate">{{ number_format(round($report['prev_avg_mo'])) }}</span><span>{{ number_format(round($report['prev_mo'])) }}</span>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>
                                                        <div class="inter">Cost Campaign / Price.MO</div>
                                                    </th>
                                                    <td class="p-1 gray-bg current_month_costCampaign">
                                                        <div class="inter"><span
                                                                class="{{ $report['current_cost_class'] }} intermate">{{ numberConverter($report['current_cost'], 3) }}<i
                                                                    class="fa {{ $report['current_cost_arrow'] }}"></i>&nbsp;</span><span
                                                                class="{{ $report['current_price_mo_class'] }}">{{ numberConverter($report['current_price_mo'], 3) }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="p-1">
                                                        <div class="inter"><span
                                                                class="{{ $report['estimated_cost_class'] }} intermate">{{ numberConverter($report['estimated_cost'], 3) }}<i
                                                                    class="fa {{ $report['estimated_cost_arrow'] }}"></i>&nbsp;<small>{{ $report['estimated_cost_percentage'] }}%</small></span><span
                                                                class="{{ $report['estimated_price_mo_class'] }}">{{ numberConverter($report['estimated_price_mo'], 3) }}<i
                                                                    class="fa {{ $report['estimated_price_mo_arrow'] }}"></i>&nbsp;<small>{{ $report['estimated_price_mo_percentage'] }}%</small></span>
                                                        </div>
                                                    </td>
                                                    <td class="p-1 gray-bg">
                                                        <div class="inter"><span
                                                                class="{{ $report['last_cost_class'] }} intermate">{{ numberConverter($report['last_cost'], 3) }}<i
                                                                    class="fa {{ $report['last_cost_arrow'] }}"></i>&nbsp;<small>{{ $report['last_cost_percentage'] }}%</small></span><span
                                                                class="{{ $report['last_price_mo_class'] }}">{{ numberConverter($report['last_price_mo'], 3) }}<i
                                                                    class="fa {{ $report['last_price_mo_arrow'] }}"></i>&nbsp;<small>{{ $report['last_price_mo_percentage'] }}%</small></span>
                                                        </div>
                                                    </td>
                                                    <td class="p-1">
                                                        <div class="inter"><span
                                                                class="intermate">{{ numberConverter($report['prev_cost'], 3) }}</span><span>{{ numberConverter($report['prev_price_mo'], 3) }}</span>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>ROI<sup><i class="ml-2 mr-2 mt-1 text-dark fa fa-info-circle"
                                                                title="(cost campaign/mo)/ (share/reg)"></i></sup>/
                                                        US30ARPU</th>
                                                    <td class="p-1 gray-bg current_month_roi">
                                                        <div class="inter"><span
                                                                class="{{ $report['currentMonthROI_class'] }} intermate">{{ numberConverter($report['currentMonthROI'], 4, 'pre') }}<i
                                                                    class="fa {{ $report['currentMonthROI_arrow'] }}"></i>&nbsp;</span><span
                                                                class="{{ $report['current_30_arpu_class'] }}">{{ numberConverter($report['current_30_arpu'], 4, 'pre') }}<i
                                                                    class="fa {{ $report['current_30_arpu_arrow'] }}"></i>&nbsp;</span>
                                                        </div>
                                                    </td>
                                                    <td class="p-1">
                                                        <div class="inter"><span
                                                                class="{{ $report['estimatedMonthROI_class'] }} intermate">{{ numberConverter($report['estimatedMonthROI'], 4, 'pre') }}<i
                                                                    class="fa {{ $report['estimatedMonthROI_arrow'] }}"></i>&nbsp;<small>{{ $report['estimatedMonthROI_percentage'] }}%</small></span><span
                                                                class="{{ $report['estimated_30_arpu_class'] }}">{{ numberConverter($report['estimated_30_arpu'], 4, 'pre') }}<i
                                                                    class="fa {{ $report['estimated_30_arpu_arrow'] }}"></i>&nbsp;<small>{{ $report['estimated_30_arpu_percentage'] }}%</small></span>
                                                        </div>
                                                    </td>
                                                    <td class="p-1 gray-bg">
                                                        <div class="inter"><span
                                                                class="{{ $report['lastMonthROI_class'] }} intermate">{{ numberConverter($report['lastMonthROI'], 4, 'pre') }}<i
                                                                    class="fa {{ $report['lastMonthROI_arrow'] }}"></i>&nbsp;<small>{{ $report['lastMonthROI_percentage'] }}%</small></span><span
                                                                class="{{ $report['last_30_arpu_class'] }}">{{ numberConverter($report['last_30_arpu'], 4, 'pre') }}<i
                                                                    class="fa {{ $report['last_30_arpu_arrow'] }}"></i>&nbsp;<small>{{ $report['last_30_arpu_percentage'] }}%</small></span>
                                                        </div>
                                                    </td>
                                                    <td class="p-1">
                                                        <div class="inter"><span
                                                                class="intermate">{{ numberConverter($report['previousMonthROI'], 4, 'pre') }}</span><span>{{ numberConverter($report['prev_30_arpu'], 4, 'pre') }}</span>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>GP/AVG GP<sup><i class="ml-2 mr-2 mt-1 text-dark fa fa-info-circle"
                                                                title="Gross Profit = Gross Revenue - (cost campaign + other cost) / Average Gross Profit"></i></sup>
                                                    </th>
                                                    <td class="p-1 gray-bg current_month_pnl">
                                                        <div class="inter"><span
                                                                class="{{ $report['current_pnl_class'] }} intermate">{{ number_format(round($report['current_pnl'])) }}<i
                                                                    class="fa {{ $report['current_pnl_arrow'] }}"></i>&nbsp;</span><span
                                                                class="{{ $report['estimated_pnl_class'] }}">{{ number_format(round($report['current_avg_pnl'])) }}<i
                                                                    class="fa {{ $report['estimated_pnl_arrow'] }}"></i>&nbsp;</span>
                                                        </div>
                                                    </td>
                                                    <td class="p-1">
                                                        <div class="inter"><span
                                                                class="{{ $report['estimated_pnl_class'] }} intermate">{{ number_format(round($report['estimated_pnl'])) }}<i
                                                                    class="fa {{ $report['estimated_pnl_arrow'] }}"></i>&nbsp;<small>{{ $report['estimated_pnl_percentage'] }}%</small></span><span
                                                                class="{{ $report['estimated_avg_pnl_class'] }}">{{ number_format(round($report['estimated_avg_pnl'])) }}<i
                                                                    class="fa {{ $report['estimated_avg_pnl_arrow'] }}"></i>&nbsp;<small>{{ $report['estimated_avg_pnl_percentage'] }}%</small></span>
                                                        </div>
                                                    </td>
                                                    <td class="p-1 gray-bg">
                                                        <div class="inter"><span
                                                                class="{{ $report['last_pnl_class'] }} intermate">{{ number_format(round($report['last_pnl'])) }}<i
                                                                    class="fa {{ $report['last_pnl_arrow'] }}"></i>&nbsp;<small>{{ $report['last_pnl_percentage'] }}%</small></span><span
                                                                class="{{ $report['last_avg_pnl_class'] }}">{{ number_format(round($report['last_avg_pnl'])) }}<i
                                                                    class="fa {{ $report['last_avg_pnl_arrow'] }}"></i>&nbsp;<small>{{ $report['last_avg_pnl_percentage'] }}%</small></span>
                                                        </div>
                                                    </td>
                                                    <td class="p-1">
                                                        <div class="inter"><span
                                                                class="intermate">{{ number_format(round($report['prev_pnl'])) }}</span><span>{{ number_format(round($report['prev_avg_pnl'])) }}</span>
                                                        </div>
                                                    </td>
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
@endsection
@push('script')
    <script src="{{ asset('assets/js/chart.min.js') }}"></script>
    {{-- @if ($calenderTasks)
        <script src="{{ asset('assets/libs/fullcalendar/dist/fullcalendar.min.js') }}"></script>
    @endif --}}
@endpush
