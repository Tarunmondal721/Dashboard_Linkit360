@extends('layouts.admin')

@section('title')
     {{ __("Revenue Monitor") }}
@endsection

@section('pagetytle')
     {{ __("Monitor USD Revenue By Operator") }}
@endsection
@section('content')

    @include('analytic.partials.filter')


    <div class="d-flex align-items-center my-3">
        <span class="badge badge-secondary px-2 bg-primary text-uppercase">Nov 2022</span>
        <span class="flex-fill ml-2 bg-primary w-100 font-weight-bold" style="height: 1px;"></span>
        <div class="text-right pl-2">
            <button class="btn btn-sm pnl-xls" style="color:white; background-color:green"
                data-param="container"><i class="fa fa-file-excel-o"></i>Export XLS</button>
        </div>
    </div>


    <div class="table-responsive shadow-sm mb-3 tableFixHead">
        <table class="table table-light table-bordered m-0 font-13 table-text-no-wrap">
            <thead class="thead-dark">
                <tr>
                    <th class="first-col" width="10%">Operator</th>
                    <th>Total</th>
                    <th>Avg</th>
                    <th>T.Mo.End</th>
                    @foreach ($no_of_days as $no_of_day)
                    <th>{{$no_of_day['no']}}</th>
                    @endforeach

                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="font-weight-bold first-col">All Operator </td>
                    <td>${{isset($AllCountryTotalDatas['gros_rev'])?$AllCountryTotalDatas['gros_rev']['total']:''}}</td>
                    <td>${{isset($AllCountryTotalDatas['gros_rev'])?$AllCountryTotalDatas['gros_rev']['avg']:''}}</td>
                    <td>${{isset($AllCountryTotalDatas['gros_rev'])?$AllCountryTotalDatas['gros_rev']['t_mo_end']:''}}</td>


                    @if (isset($AllCountryTotalDatas['gros_rev']['dates']) || !empty($AllCountryTotalDatas['gros_rev']['dates']))
                        @foreach (array_reverse($AllCountryTotalDatas['gros_rev']['dates']) as $date)
                        <td class="{{$date['class']}}">${{$date['value']}}&nbsp;<small>(0.0%)</small></td>
                        @endforeach

                    @endif

                </tr>
            </tbody>
        </table>
    </div>


    <div class="float-right mt-2 mb-2">
        <input type="checkbox" id="monitor-expand"> Expand All &nbsp;&nbsp;
        <!-- <input type="checkbox" id="monitor-remove"> No Data -->
    </div>

    <div class="table-responsive shadow-sm mb-3 tableFixHead">
        <table class="table table-light table-bordered m-0 font-13 table-text-no-wrap" id="adsTbl">
            <thead class="thead-dark">
                <tr>
                    <th class="first-col" width="10%">Operator</th>
                    <th>Total</th>
                    <th>Avg</th>
                    <th>T.Mo.End</th>
                    @foreach ($no_of_days as $no_of_day)
                    <th>{{$no_of_day['no']}}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @if (isset($allCountryDatas) || !empty($allCountryDatas))
                @foreach ($allCountryDatas as $allCountryData)
                    @php
                        $countryArray=explode(' ',isset($allCountryData['country'])?$allCountryData['country']['country']:'');
                        $countryClass0= isset($countryArray[0])?$countryArray[0]:'';
                        $countryClass1=isset($countryArray[1])?'_'.$countryArray[1]:'';
                        $countryClass2=isset($countryArray[2])?'_'.$countryArray[2]:'';
                        $countryClass=$countryClass0.$countryClass1.$countryClass2;
                        +isset($countryArray[2])?$countryArray[2]:'';
                        // dd($countryClass);
                    @endphp
                    <tr class=" country-odd-bg">
                        <td class="font-weight-bold first-col"><span class="opbtn" data-param="{{isset($countryClass)?$countryClass:''}}" style="cursor:pointer; min-width:10px; font-size:20px;"><strong>+</strong></span> <img src="http://staging.report.infralinkit360.com/images/flags/{{isset($allCountryData['country'])?$allCountryData['country']['flag']:''}}" height="20" width="30">&nbsp;{{isset($allCountryData['country'])?$allCountryData['country']['country']:''}}</td>

                        <td class="font-weight-bold cost">${{isset($allCountryData['gros_rev'])?$allCountryData['gros_rev']['total']:0.00}}</td>
                        <td class="font-weight-bold cost_avg">${{isset($allCountryData['gros_rev']['avg'])?$allCountryData['gros_rev']['avg']:0.00}}</td>
                        <td class="font-weight-bold">${{isset($allCountryData['gros_rev']['t_mo_end'])?$allCountryData['gros_rev']['t_mo_end']:0.00}}</td>

                        @if (isset($allCountryData['gros_rev']['dates']) || !empty($allCountryData['gros_rev']['dates']))
                        @foreach (array_reverse($allCountryData['gros_rev']['dates']) as $date)
                        <td class="{{$date['class']}} font-weight-bold">${{$date['value']}}&nbsp;<small>(0.0%)</small></td>
                        @endforeach
                        @endif
                    </tr>
                    @if (is_array($allCountryData['company']) && (isset($allCountryData['company']) || !empty($allCountryData['company'])))

                        @foreach ($allCountryData['company'] as $company)
                        <tr class="{{isset($countryClass)?$countryClass:''}}  expandable operator-odd-bg" style="display: none;">

                            <td class="first-col"><span class="ml-4">{{isset($company['company'])?$company['company']->name:''}}</span></td>

                            <td>${{isset($company['gros_rev'])?$company['gros_rev']['total']:''}}</td>
                            <td>${{isset($company['gros_rev'])?$company['gros_rev']['avg']:''}}</td>
                            <td>${{isset($company['gros_rev'])?$company['gros_rev']['t_mo_end']:''}}</td>
                            @if (isset($company['gros_rev']['dates']) || !empty($company['gros_rev']['dates']))
                                @foreach (array_reverse($company['gros_rev']['dates']) as $date)
                                <td class="{{$date['class']}}">${{$date['value']}}&nbsp;<small>(0.0%)</small></td>
                                @endforeach

                            @endif
                        </tr>
                        @endforeach
                    @endif
                @endforeach
                @endif
            </tbody>
        </table>
    </div>


    {{-- <div class="d-flex align-items-center my-2 pull-right">
        <span class="badge badge-secondary px-2 bg-primary" id="loadTimer">Load Time :{{ round(microtime(true) - LARAVEL_START, 3) }}s</span> --}}
    </div>



    <button type="button" id="button"
        class="btn btn-danger btn-floating btn-sm btn-back-to-top-position up"><i
            class="fa fa-arrow-up"></i></button>




</div>

@endsection
