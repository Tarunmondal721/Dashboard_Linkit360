@extends('layouts.admin')

@section('title')
     {{ __("Ads Spending") }}
@endsection
@section('pagetytle')
{{ isset($BusinessTypeWise) ?'Spending ADs By Business' : 'Spending ADs By Operator' }}

@endsection
@section('content')

<div class="page-content">

    @include('analytic.partials.filter')

    <div class="d-flex align-items-center my-3">
        <span class="badge badge-secondary px-2 bg-primary text-uppercase">{{isset($AllCuntryCostCamp['month_string'] ) ? $AllCuntryCostCamp['month_string'] : ''}}</span>
        <span class="flex-fill ml-2 bg-primary w-100 font-weight-bold" style="height: 1px;"></span>
        <div class="text-right pl-2">
            <button class="btn btn-sm pnl-xls" style="color:white; background-color:green" data-param="container"><i class="fa fa-file-excel-o"></i>Export XLS</button>
        </div>
    </div>

    <div class="table-responsive shadow-sm mb-3 tableFixHead">
        <table class="table table-light table-bordered m-0 font-13 table-text-no-wrap" id="adsTbl">
            <thead class="thead-dark">
                <tr>
                    <th class="first-col" width="16%">ALL</th>
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
                    <td class="font-weight-bold first-col">All Operator</td>
                    <td>{{isset($AllCuntryCostCamp['cost_campaign'])? numberConverter($AllCuntryCostCamp['cost_campaign']['total'],2,'pre'):''}}</td>
                    <td>{{isset($AllCuntryCostCamp['cost_campaign'])? numberConverter($AllCuntryCostCamp['cost_campaign']['avg'],2,'pre'):''}}</td>
                    <td>{{isset($AllCuntryCostCamp['cost_campaign'])? numberConverter($AllCuntryCostCamp['cost_campaign']['t_mo_end'],2,'pre') :''}}</td>

                    @if (isset($AllCuntryCostCamp['cost_campaign']['dates']) && !empty($AllCuntryCostCamp['cost_campaign']['dates']))
                    @foreach (array_reverse($AllCuntryCostCamp['cost_campaign']['dates']) as $date)
                    <td class="{{isset($date['class'])?$date['class']:''}}">{{isset($date['value'])?numberConverter($date['value'],2,'pre'):0}}&nbsp;<small>({{isset($date['percentage'])?numberConverter($date['percentage'],1,'pre'):0.0}}%)</small></td>
                    @endforeach
                    @endif
                </tr>
            </tbody>
            <tbody><tr><td style="height: 20px;"></td></tr></tbody>
            <thead class="thead-dark">
                <tr>
                    <th class="first-col" width="16%">Country</th>
                    <th>Total</th>
                    <th>Avg</th>
                    <th>T.Mo.End</th>
                    @foreach ($no_of_days as $no_of_day)
                    <th>{{$no_of_day['no']}}</th>
                    @endforeach
                </tr>
            </thead>

            @if (isset($totelCountryCosts) || !empty($totelCountryCosts))
            @foreach ($totelCountryCosts as $totelCountryCost)
            @php
                $countryArray=explode(' ',isset($totelCountryCost['country'])?$totelCountryCost['country']['country']:'');
                $countryClass0= isset($countryArray[0])? $countryArray[0]:'';
                $countryClass1=isset($countryArray[1])?'_'.$countryArray[1]:'';
                $countryClass2=isset($countryArray[2])?'_'.$countryArray[2]:'';
                $countryClass=$countryClass0.$countryClass1.$countryClass2;

                $countryFlag = isset($totelCountryCost['country']) ? $totelCountryCost['country']['flag'] : '';
            @endphp
            <tbody class="ptables" id="{{ isset($countryClass) ? strpos($countryClass, '.') ? str_replace('.', '_', $countryClass) : $countryClass : '' }}_adsTblBdy">
                <tr class=" country-odd-bg">
                    <td class="font-weight-bold first-col"><span class="opbtn" data-param="{{ isset($countryClass) ? strpos($countryClass, '.') ? str_replace('.', '_', $countryClass) : $countryClass : '' }}" style="cursor:pointer; min-width:10px; font-size:20px;">@if(count($totelCountryCost['operator'])>0)<strong>+</strong>@endif</span>

                        {!! isset($BusinessTypeWise)
                            ? $totelCountryCost['country']['country']
                            : '<img src="' . asset('/flags/' . $countryFlag) . '" height="20" width="30">&nbsp;<a href="' . route('report.daily.country.pnldetails', ['company' => 'allcompany', 'country' => $totelCountryCost['country']['id']]) . '">' . (isset($totelCountryCost['country']) ? $totelCountryCost['country']['country'] : '') . '</a>'
                        !!}


                    </td>
                    <td class="font-weight-bold cost">{{isset($totelCountryCost['cost_campaign'])?numberConverter($totelCountryCost['cost_campaign']['total'],2,'pre'):0.00}}</td>
                    <td class="font-weight-bold cost_avg">{{isset($totelCountryCost['cost_campaign']['avg'])? numberConverter($totelCountryCost['cost_campaign']['avg'],2,'pre'):0.00}}</td>
                    <td class="font-weight-bold">{{isset($totelCountryCost['cost_campaign']['t_mo_end'])? numberConverter($totelCountryCost['cost_campaign']['t_mo_end'],2,'pre'):0.00}}</td>

                    @if (isset($totelCountryCost['cost_campaign']['dates']) || !empty($totelCountryCost['cost_campaign']['dates']))
                    @foreach (array_reverse($totelCountryCost['cost_campaign']['dates']) as $date)
                    <td class="{{isset($date['class'])?$date['class']:''}} font-weight-bold">{{isset($date['value'])?numberConverter($date['value'],2,'pre'):o}}&nbsp;<small>({{isset($date['percentage'])?numberConverter($date['percentage'],1,'pre'):0}}%)</small></td>
                    @endforeach
                    @endif
                </tr>

                @if (isset($totelCountryCost['operator']) || !empty($totelCountryCost['operator']))
                @foreach ($totelCountryCost['operator'] as $operator)
                <tr class="{{ isset($countryClass) ? strpos($countryClass, '.') ? str_replace('.', '_', $countryClass) : $countryClass : '' }}  expandable operator-odd-bg" style="display: none;">
                    <td class="first-col"><span class="ml-4"><a href="{{ route('report.daily.operator.pnldetails','operatorId[]='.$operator['operator']['id_operator']) }}">{{isset($operator['operator'])?$operator['operator']['operator_name']:''}} <a href="{{ route('analytic.adsMonitoring.details','operator='.$operator['operator']['operator_name']) }}"><i class="fa fa-info-circle" style="font-size:18px;color:blue"></i></a></a></span></td>
                    <td class="subs">{{isset($operator['cost_campaign'])? numberConverter($operator['cost_campaign']['total'],2,'pre'):''}}</td>
                    <td>{{isset($operator['cost_campaign'])? numberConverter($operator['cost_campaign']['avg'],2,'pre'):''}}</td>
                    <td>{{isset($operator['cost_campaign'])? numberConverter($operator['cost_campaign']['t_mo_end'],2,'pre'):''}}</td>

                    @if (isset($operator['cost_campaign']['dates']) || !empty($operator['cost_campaign']['dates']))
                    @foreach (array_reverse($operator['cost_campaign']['dates']) as $date)
                    <td class="{{isset($date['class'])?$date['class']:''}}">{{isset($date['value'])?numberConverter($date['value'],2,'pre'):0}}&nbsp;<small>({{isset($date['percentage'])?numberConverter($date['percentage'],2,'pre'):0}} %)</small></td>
                    @endforeach
                    @endif
                </tr>
                @endforeach
                @endif
            </tbody>
            @endforeach
            @endif
        </table>
    </div>

    <button type="button" id="button" class="btn btn-danger btn-floating btn-sm btn-back-to-top-position up"><i class="fa fa-arrow-up"></i></button>
</div>

@endsection
