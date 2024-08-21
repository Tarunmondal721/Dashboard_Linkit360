@extends('layouts.admin')

@section('title')
    {{ __('Service Report Details') }}
@endsection

@section('content')
    <div class="page-content">
        <div class="page-title" style="margin-bottom:25px">
            <div class="row justify-content-between align-items-center">
                <div class="col-xl-4 col-lg-4 col-md-4 d-flex align-items-center justify-content-between justify-content-md-start mb-3 mb-md-0">
                    <div class="d-inline-block">
                        <h5 class="h4 d-inline-block font-weight-400 mb-0"><b>Service Report Details</b></h5><br>
                        <p class="d-inline-block font-weight-200 mb-0">Service Report Details</p>
                    </div>
                </div>
                <div class="col-xl-8 col-lg-8 col-md-8 d-flex align-items-center justify-content-between justify-content-md-end">
                </div>
            </div>
        </div>
    @include('report.partials.filterServiceDetails');
        <div id="reportXls">
            <div id="container">

                @if(isset($sumemry) && !empty($sumemry))
                @foreach ($sumemry as $report)
                <div class="box-panel">
                    <h1 style="display:none">Reporting Service Details For Service {{$report['service']['service_name']}}</h1>
                    <div class="d-flex align-items-center my-3">
                        <span class="badge badge-secondary px-2 bg-primary text-uppercase">
                            <a href="javascript:void(0);" class="text-white"> </a> {{$report['service']['service_name']}}
                        </span>
                        <span class="flex-fill ml-2 bg-primary w-100 font-weight-bold" style="height:1px;"></span>
                        <div class="text-right pl-2">
                            <button class="btn btn-sm service-xls"
                                style="color:white; background-color:green" data-param="all"><i
                                    class="fa fa-file-excel-o"></i>Export as XLS</button>
                        </div>
                    </div>

                    <div class="card">
                        <div class="table-responsive shadow-sm" id="all">
                            <h1 style="display:hidden"></h1>
                            <table class="table table-light table-striped m-0 font-13 xlaxiata" id="dtbl">
                                <thead class="thead-dark">
                                    <tr>
                                        <th class="align-middle">Type</th>
                                        <th class="align-middle">Total</th>
                                        <th class="align-middle">Avg</th>
                                        <th class="align-middle">T Mo End</th>


                                        @if(isset($no_of_days) && !empty($no_of_days))
                                        @foreach ($no_of_days as $days)
                                        <th class="align-middle">{{$days['no']}}</th>
                                        @endforeach
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="subs">
                                        <td><strong class="text-with-sup">Active Subs</strong></td>
                                        <td class="">{{ numberConverter($report['t_sub']['total'] ,0,'total','') }}</td>
                                        <td class="">{{ numberConverter($report['t_sub']['avg'] ,0,'avg','') }}</td>
                                        <td class="">{{ numberConverter($report['t_sub']['T_Mo_End'] ,0,'T_Mo_End','') }}</td>


                                        @if(isset($report['t_sub']['dates']) && !empty($report['t_sub']['dates']))
                                        @foreach ($report['t_sub']['dates'] as $t_sub)
                                        <td class="gross_revenue_usd {{$t_sub['class']}}">{{numberConverter($t_sub['value'],2,'')}}</td>
                                        @endforeach
                                        @endif
                                    </tr>

                                    <tr class="reg">
                                        <td><strong>Reg</strong></td>
                                        <td class="">{{ numberConverter($report['reg']['total'] ,0,'total','') }}</td>
                                        <td class="">{{ numberConverter($report['reg']['avg'] ,0,'avg','') }}</td>
                                        <td class="">{{ numberConverter($report['reg']['T_Mo_End'] ,0,'T_Mo_End','') }}</td>

                                        @if(isset($report['reg']['dates']) && !empty($report['reg']['dates']))
                                        @foreach ($report['reg']['dates'] as $reg)
                                        <td class="reg_data {{$reg['class']}}">{{numberConverter($reg['value'],2,'pre')}}</td>
                                        @endforeach
                                        @endif
                                    </tr>

                                    <tr class="unreg">
                                        <td><strong>Unreg</strong></td>
                                        <td class="">{{ numberConverter($report['unreg']['total'] ,0,'total','') }}</td>
                                        <td class="">{{ numberConverter($report['unreg']['avg'] ,0,'avg','') }}</td>
                                        <td class="">{{ numberConverter($report['unreg']['T_Mo_End'] ,0,'T_Mo_End','') }}</td>

                                        @if(isset($report['unreg']['dates']) && !empty($report['unreg']['dates']))
                                        @foreach ($report['unreg']['dates'] as $unreg)
                                        <td class="unreg_data {{$unreg['class']}}">{{numberConverter($unreg['value'],2,'pre')}}</td>
                                        @endforeach
                                        @endif
                                    </tr>

                                    <tr class="purged">
                                        <td><strong>Purged</strong></td>
                                        <td class="">{{ numberConverter($report['purged']['total'] ,0,'total','') }}</td>
                                        <td class="">{{ numberConverter($report['purged']['avg'] ,0,'avg','') }}</td>
                                        <td class="">{{ numberConverter($report['purged']['T_Mo_End'] ,0,'T_Mo_End','') }}</td>

                                        @if(isset($report['purged']['dates']) && !empty($report['purged']['dates']))
                                        @foreach ($report['purged']['dates'] as $purged)
                                        <td class="purged_data {{$purged['class']}}">{{numberConverter($purged['value'],2,'pre')}}</td>
                                        @endforeach
                                        @endif
                                    </tr>

                                    <tr class="churn">
                                        <td><strong class="text-with-sup">Churn Rate%</strong></td>
                                        <td class="">N/A</td>
                                        <td class="">{{ numberConverter($report['churn']['avg'] ,0,'avg','') }}</td>
                                        <td class="">N\A</td>

                                        @if(isset($report['churn']['dates']) && !empty($report['churn']['dates']))
                                        @foreach ($report['churn']['dates'] as $churn)
                                        <td class="churn_data {{$churn['class']}}">{{numberConverter($churn['value'],2,'post','%')}}</td>
                                        @endforeach
                                        @endif
                                    </tr>

                                    <tr class="text-white rev">
                                        <td><strong class="text-with-sup">Revenue</strong></td>
                                        <td class="">{{ numberConverter($report['t_rev']['total'] ,0,'total','') }}</td>
                                        <td class="">{{ numberConverter($report['t_rev']['avg'] ,0,'avg','') }}</td>
                                        <td class="">{{ numberConverter($report['t_rev']['T_Mo_End'] ,0,'T_Mo_End','') }}</td>

                                        @if(isset($report['t_rev']['dates']) && !empty($report['t_rev']['dates']))
                                        @foreach ($report['t_rev']['dates'] as $t_rev)
                                        <td class="gross_revenue_usd {{$t_rev['class']}}">{{numberConverter($t_rev['value'],3,'post')}}</td>
                                        @endforeach
                                        @endif
                                    </tr>
                                </tbody>
                                <tbody><tr><td style="height: 20px;"></td></tr></tbody>
                                <thead class="thead-dark">
                                    <tr class="text-left">
                                        <th colspan="9" class="bg-white text-dark border-0 font-weight-bold">First Push</th>
                                    </tr>
                                    <tr>
                                        <th class="align-middle">Type</th>
                                        <th class="align-middle">Total</th>
                                        <th class="align-middle">Avg</th>
                                        <th class="align-middle">T Mo End</th>


                                        @if(isset($no_of_days) && !empty($no_of_days))
                                        @foreach ($no_of_days as $days)
                                        <th class="align-middle">{{$days['no']}}</th>
                                        @endforeach
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="subs">
                                        <td><strong class="text-with-sup">Sent</strong></td>
                                        <td class="">{{ numberConverter($report['sent']['total'] ,0,'total','') }}</td>
                                        <td class="">{{ numberConverter($report['sent']['avg'] ,0,'total','') }}</td>
                                        <td class="">{{ numberConverter($report['sent']['T_Mo_End'] ,0,'total','') }}</td>


                                        @if(isset($report['sent']['dates']) && !empty($report['sent']['dates']))
                                        @foreach ($report['sent']['dates'] as $sent)
                                        <td class="gross_revenue_usd {{$sent['class']}}">{{numberConverter($sent['value'],2,'pre')}}</td>
                                        @endforeach
                                        @endif
                                    </tr>

                                    <tr class="reg">
                                        <td data-title="Company" class="first_failed text-danger font-weight-bold" style="cursor: default;">Failed</td>
                                        <td class="">{{ numberConverter($report['fmt_failed']['total'] ,0,'total','') }}</td>
                                        <td class="">{{ numberConverter($report['fmt_failed']['avg'] ,0,'total','') }}</td>
                                        <td class="">{{ numberConverter($report['fmt_failed']['T_Mo_End'] ,0,'total','') }}</td>


                                        @if(isset($report['fmt_failed']['dates']) && !empty($report['fmt_failed']['dates']))
                                        @foreach ($report['fmt_failed']['dates'] as $fmt_failed)
                                        <td class="fmt_failed_data {{$fmt_failed['class']}}">{{numberConverter($fmt_failed['value'],2,'pre')}}</td>
                                        @endforeach
                                        @endif
                                    </tr>

                                    @php if (isset($report['data']['first_push_data']['reasons']) && !empty($report['data']['first_push_data']['reasons'])) {
                                        foreach ($report['data']['first_push_data']['reasons'] as $sk => $sv) {
                                    @endphp
                                    <tr style="display: none;" class="first_failed_status_desc hidden row-bg-secondary text-white">
                                        <td data-title="Company" class=""><?= $sv ?></td>
                                        <td data-title="Company" class="mt_total_failed">N/A</td>
                                        <td data-title="Company" class="mt_total_failed">N/A</td>
                                        <td data-title="Company" class="mt_total_failed">N/A</td>

                                        <?php
                                            foreach(array_reverse($report['data']['first_push_data']['revenue']) as $revKey => $fpi){
                                                $fp_mt_failed1 = 0;
                                                if(isset($report['data']['first_push_data'][$sv]) && !empty($report['data']['first_push_data'][$sv])){
                                                    foreach ($report['data']['first_push_data'][$sv] as $sk1 => $sv1) {
                                                        $day = date('d', strtotime($sk1));
                                                        if (date('d',strtotime($revKey)) == $day) {
                                                            $fp_mt_failed1 = isset($sv1) ? $sv1 : 0;
                                                        }
                                                    }
                                                }

                                                echo '<td data-title="Company" class="mt_failed" >' . numberConverter($fp_mt_failed1,2,'pre') . '</td>';
                                            }
                                        ?>
                                    </tr>
                                    @php } } @endphp

                                    <tr class="unreg">
                                        <td><strong>Unknown</strong></td>
                                        <td class="">0</td>
                                        <td class="">{{ numberConverter($report['unreg']['avg'] ,0,'avg','') }}</td>
                                        <td class="">{{ numberConverter($report['unreg']['T_Mo_End'] ,0,'T_Mo_End','') }}</td>


                                        @if(isset($report['unreg']['dates']) && !empty($report['unreg']['dates']))
                                        @foreach ($report['unreg']['dates'] as $unreg)
                                        <td class="unreg_data">0</td>
                                        @endforeach
                                        @endif
                                    </tr>

                                    <tr class="purged">
                                        <td><strong>Delivered</strong></td>
                                        <td class="">{{ numberConverter($report['fmt_success']['total'] ,0,'total','') }}</td>
                                        <td class="">{{ numberConverter($report['fmt_success']['avg'] ,0,'avg','') }}</td>
                                        <td class="">{{ numberConverter($report['fmt_success']['T_Mo_End'] ,0,'T_Mo_End','') }}</td>


                                        @if(isset($report['fmt_success']['dates']) && !empty($report['fmt_success']['dates']))
                                        @foreach ($report['fmt_success']['dates'] as $fmt_success)
                                        <td class="fmt_success_data {{$fmt_success['class']}}">{{numberConverter($fmt_success['value'],2,'pre')}}</td>
                                        @endforeach
                                        @endif
                                    </tr>

                                    <tr class="fp_success_rate">
                                        <td><strong class="text-with-sup">Success Rate %</strong></td>
                                        <td class="">{{ numberConverter($report['fp_success_rate']['total'] ,0,'total','') }}</td>
                                        <td class="">{{ numberConverter($report['fp_success_rate']['avg'] ,0,'avg','') }}</td>
                                        <td class="">{{ numberConverter($report['fp_success_rate']['T_Mo_End'] ,0,'T_Mo_End','') }}</td>


                                        @if(isset($report['fp_success_rate']['dates']) && !empty($report['fp_success_rate']['dates']))
                                        @foreach ($report['fp_success_rate']['dates'] as $fp_success_rate)
                                        <td class="churn_data {{$fp_success_rate['class']}}">{{numberConverter($fp_success_rate['value'],2,'pre')}}%</td>
                                        @endforeach
                                        @endif
                                    </tr>

                                    <tr class="text-white rev">
                                        <td><strong class="text-with-sup">Revenue</strong></td>
                                        <td class="">{{isset($report['data']['first_push_data']['total']) ?  numberConverter($report['data']['first_push_data']['total'] ,0,'total','') : 0 }}</td>
                                        <td class="">{{isset($report['data']['first_push_data']['avg']) ?  numberConverter($report['data']['first_push_data']['avg'] ,0,'avg','') : 0 }}</td>
                                        <td class="">{{isset($report['data']['first_push_data']['T_Mo_End']) ?  numberConverter($report['data']['first_push_data']['T_Mo_End'] ,0,'T_Mo_End','') : 0 }}</td>

                                        @if(isset($no_of_days) && !empty($no_of_days))
                                        @foreach ($no_of_days as $days)
                                        <td class="gross_revenue_usd">{{isset($report['data']['first_push_data']['revenue'][$days['date']]) ? numberConverter($report['data']['first_push_data']['revenue'][$days['date']],2,'pre') : 0}}</td>
                                        @endforeach
                                        @endif
                                    </tr>
                                </tbody>
                                <tbody><tr><td style="height: 20px;"></td></tr></tbody>
                                <thead class="thead-dark">
                                    <tr class="text-left">
                                        <th colspan="9" class="bg-white text-dark border-0 font-weight-bold">Daily Push</th>
                                    </tr>

                                    <tr>
                                        <th class="align-middle">Type</th>
                                        <th class="align-middle">Total</th>
                                        <th class="align-middle">Avg</th>
                                        <th class="align-middle">T Mo End</th>


                                        @if(isset($no_of_days) && !empty($no_of_days))
                                        @foreach ($no_of_days as $days)
                                        <th class="align-middle">{{$days['no']}}</th>
                                        @endforeach
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="subs">
                                        <td><strong class="text-with-sup">Sent</strong></td>
                                        <td class="">{{ numberConverter($report['renewal']['total'] ,0,'total','') }}</td>
                                        <td class="">{{ numberConverter($report['renewal']['avg'] ,0,'avg','') }}</td>
                                        <td class="">{{ numberConverter($report['renewal']['T_Mo_End'] ,0,'T_Mo_End','') }}</td>


                                        @if(isset($report['renewal']['dates']) && !empty($report['renewal']['dates']))
                                        @foreach ($report['renewal']['dates'] as $renewal)
                                        <td class="gross_revenue_usd {{$renewal['class']}}">{{numberConverter($renewal['value'],2,'pre')}}</td>
                                        @endforeach
                                        @endif
                                    </tr>

                                    <tr class="reg">
                                        <td data-title="Company" class="daily_failed text-danger font-weight-bold" style="cursor: default;">Failed</td>
                                        <td class="">{{ numberConverter($report['mt_failed']['total'] ,0,'total','') }}</td>
                                        <td class="">{{ numberConverter($report['mt_failed']['avg'] ,0,'avg','') }}</td>
                                        <td class="">{{ numberConverter($report['mt_failed']['T_Mo_End'] ,0,'T_Mo_End','') }}</td>


                                        @if(isset($report['mt_failed']['dates']) && !empty($report['mt_failed']['dates']))
                                        @foreach ($report['mt_failed']['dates'] as $mt_failed)
                                        <td class="mt_failed_data {{$mt_failed['class']}}">{{numberConverter($mt_failed['value'],2,'pre')}}</td>
                                        @endforeach
                                        @endif
                                    </tr>

                                    @php if (isset($report['data']['daily_push_data']['reasons']) && !empty($report['data']['daily_push_data']['reasons'])) {
                                        foreach ($report['data']['daily_push_data']['reasons'] as $sk => $dailyPush) {
                                    @endphp
                                    <tr style="display: none;" class="daily_failed_status_desc hidden row-bg-secondary text-white">
                                        <td data-title="Company" class=""><?= $dailyPush ?></td>
                                        <td data-title="Company" class="mt_total_failed">N/A</td>
                                        <td data-title="Company" class="mt_total_failed">N/A</td>
                                        <td data-title="Company" class="mt_total_failed">N/A</td>

                                        <?php
                                            foreach(array_reverse($report['data']['daily_push_data']['revenue']) as $revKey => $fpi){
                                                $mt_failed1 = 0;
                                                if(isset($report['data']['daily_push_data'][$dailyPush]) && !empty($report['data']['daily_push_data'][$dailyPush])){
                                                    foreach ($report['data']['daily_push_data'][$dailyPush] as $dp1 => $dPush) {
                                                        $day = date('d', strtotime($dp1));
                                                        if (date('d',strtotime($revKey)) == $day) {
                                                            $mt_failed1 = isset($dPush) ? $dPush : 0;
                                                        }
                                                    }
                                                }

                                                echo '<td data-title="Company" class="mt_failed" >' . numberConverter($mt_failed1,2,'pre') . '</td>';
                                            }
                                        ?>
                                    </tr>
                                    @php } } @endphp

                                    <tr class="unreg">
                                        <td><strong>Unknown</strong></td>
                                        <td class="">0</td>
                                        <td class="">{{ numberConverter($report['unreg']['avg'] ,0,'avg','') }}</td>
                                        <td class="">{{ numberConverter($report['unreg']['T_Mo_End'] ,0,'T_Mo_End','') }}</td>


                                        @if(isset($report['unreg']['dates']) && !empty($report['unreg']['dates']))
                                        @foreach ($report['unreg']['dates'] as $unreg)
                                        <td class="unreg_data">0</td>
                                        @endforeach
                                        @endif
                                    </tr>

                                    <tr class="purged">
                                        <td><strong>Delivered</strong></td>
                                        <td class="">{{ numberConverter($report['mt_success']['total'] ,0,'total','') }}</td>
                                        <td class="">{{ numberConverter($report['mt_success']['avg'] ,0,'avg','') }}</td>
                                        <td class="">{{ numberConverter($report['mt_success']['T_Mo_End'] ,0,'T_Mo_End','') }}</td>

                                        @if(isset($report['mt_success']['dates']) && !empty($report['mt_success']['dates']))
                                        @foreach ($report['mt_success']['dates'] as $mt_success)
                                        <td class="purged_data {{$mt_success['class']}}">{{numberConverter($mt_success['value'],2,'pre')}}</td>
                                        @endforeach
                                        @endif
                                    </tr>

                                    <tr class="churn">
                                        <td><strong class="text-with-sup">Success Rate %</strong></td>
                                        <td class="">{{ numberConverter($report['success_rate']['total'] ,0,'total','') }}</td>
                                        <td class="">{{ numberConverter($report['success_rate']['avg'] ,0,'avg','') }}</td>
                                        <td class="">{{ numberConverter($report['success_rate']['T_Mo_End'] ,0,'T_Mo_End','') }}</td>

                                        @if(isset($report['success_rate']['dates']) && !empty($report['success_rate']['dates']))
                                        @foreach ($report['success_rate']['dates'] as $success_rate)
                                        <td class="churn_data {{$success_rate['class']}}">{{numberConverter($success_rate['value'],2,'pre')}}%</td>
                                        @endforeach
                                        @endif
                                    </tr>

                                    <tr class="text-white rev">
                                        <td><strong class="text-with-sup">Revenue</strong></td>
                                        <td class="">{{ numberConverter($report['t_rev']['total'] ,0,'total','') }}</td>
                                        <td class="">{{ numberConverter($report['t_rev']['avg'] ,0,'avg','') }}</td>
                                        <td class="">{{ numberConverter($report['t_rev']['T_Mo_End'] ,0,'T_Mo_End','') }}</td>


                                        @if(isset($no_of_days) && !empty($no_of_days))
                                        @foreach ($no_of_days as $days)
                                        <td class="gross_revenue_usd">{{isset($report['data']['daily_push_data']['revenue'][$days['date']]) ? numberConverter($report['data']['daily_push_data']['revenue'][$days['date']],2,'pre') : 0}}</td>
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
@endsection
