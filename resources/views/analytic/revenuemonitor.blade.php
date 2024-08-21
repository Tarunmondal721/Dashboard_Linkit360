@extends('layouts.admin')

@section('title')
     {{ __("Revenue Monitor") }}
@endsection

@section('pagetytle')
    {{isset($BusinessWise) ? 'Monitor USD Revenue By Business' : 'Monitor USD Revenue By Operator'  }}
    {{ __("") }}
@endsection

@section('content')

    @include('analytic.partials.filterRevenueMonitoring')

    <div class="d-flex align-items-center my-3">
        <span class="badge badge-secondary px-2 bg-primary text-uppercase">{{isset($AllCuntryGrosRev['month_string'] ) ? $AllCuntryGrosRev['month_string'] : ''}}</span>
        <span class="flex-fill ml-2 bg-primary w-100 font-weight-bold" style="height: 1px;"></span>
        <div class="text-right pl-2">
            <button class="btn btn-sm pnl-xls" style="color:white; background-color:green" data-param="container"><i class="fa fa-file-excel-o"></i>Export XLS</button>
        </div>
    </div>

    <div class="table-responsive shadow-sm mb-3 tableFixHead" id="container">
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
                <tr>
                    <td class="font-weight-bold first-col">All Operator </td>
                    <td>{{isset($AllCuntryGrosRev['gros_rev'])?numberConverter($AllCuntryGrosRev['gros_rev']['total'] ,2,''):''}}</td>
                    <td>{{isset($AllCuntryGrosRev['gros_rev'])?numberConverter($AllCuntryGrosRev['gros_rev']['avg'] ,2,''):''}}</td>
                    <td>{{isset($AllCuntryGrosRev['gros_rev'])?numberConverter($AllCuntryGrosRev['gros_rev']['t_mo_end'] ,2,''):''}}</td>

                    @if (isset($AllCuntryGrosRev['gros_rev']['dates']) || !empty($AllCuntryGrosRev['gros_rev']['dates']))
                    @foreach (array_reverse($AllCuntryGrosRev['gros_rev']['dates']) as $date)
                    <td class="{{$date['class']}}">{{numberConverter($date['value'],2,'')}}&nbsp;<small>({{isset($date['percentage'])?numberConverter($date['percentage'],1,'pre'):0.0}}%)</small></td>
                    @endforeach
                    @endif
                </tr>
            </tbody>
            <tbody><tr><td style="height: 20px;"></td></tr></tbody>
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
            @if (isset($totelCountryCosts) || !empty($totelCountryCosts))
            @foreach ($totelCountryCosts as $totelCountryCost)
            @php
                $countryArray=explode(' ',isset($totelCountryCost['country'])?$totelCountryCost['country']['country']:'');
                
                $countryClass0= isset($countryArray[0])?$countryArray[0]:'';
                $countryClass1=isset($countryArray[1])?'_'.$countryArray[1]:'';
                $countryClass2=isset($countryArray[2])?'_'.$countryArray[2]:'';
                $countryClass=$countryClass0.$countryClass1.$countryClass2;
                $countryFlag = isset($totelCountryCost['operator'][0]['country']) ? $totelCountryCost['operator'][0]['country']['flag'] : $totelCountryCost['country']['flag'];
            @endphp
            <tbody class="ptables" id="{{ isset($countryClass) ? strpos($countryClass, '.') ? str_replace('.', '_', $countryClass) : $countryClass : '' }}_adsTblBdy">
                <tr class=" country-odd-bg">
                    <td class="font-weight-bold first-col"><span class="opbtn" data-param="{{ isset($countryClass) ? strpos($countryClass, '.') ? str_replace('.', '_', $countryClass) : $countryClass : '' }}" style="cursor:pointer; min-width:10px; font-size:20px;">@if(count($totelCountryCost['operator'])>0)<strong>+</strong>@endif</span>

                        {!! isset($BusinessWise)
                            ? $totelCountryCost['country']['country']
                            : '<img src="' . asset('/flags/' . $countryFlag) . '" height="20" width="30">&nbsp;<a href="' . route('report.details', ['country' => $totelCountryCost['country']['id'], 'menu' => 'monitoring']) . '">' . (isset($totelCountryCost['country']) ? $totelCountryCost['country']['country'] : '') . '</a>'
                        !!}

                        {{-- <img src="{{ asset('/flags/'.$countryFlag) }}" height="20" width="30">&nbsp;<a href="{{ route('report.details',['country' => $totelCountryCost['country']['id'], 'menu' => 'monitoring']) }}" target="_blank" class="text-info">{{isset($totelCountryCost['country'])?$totelCountryCost['country']['country']:''}}</a> --}}
                    </td>
                    <td class="font-weight-bold cost">{{isset($totelCountryCost['gros_rev']) ? numberConverter($totelCountryCost['gros_rev']['total'] ,2,'') : 0.00 }}</td>
                    <td class="font-weight-bold cost_avg">{{isset($totelCountryCost['gros_rev']['avg']) ? numberConverter($totelCountryCost['gros_rev']['avg'] ,2,'') : 0.00 }}</td>
                    <td class="font-weight-bold">{{isset($totelCountryCost['gros_rev']['t_mo_end']) ? numberConverter($totelCountryCost['gros_rev']['t_mo_end'] ,2,'') : 0.00 }}</td>

                    @if (isset($totelCountryCost['gros_rev']['dates']) || !empty($totelCountryCost['gros_rev']['dates']))
                    @foreach (array_reverse($totelCountryCost['gros_rev']['dates']) as $date)
                    <td class="{{$date['class']}} font-weight-bold">{{ numberConverter($date['value'],2,'') }}&nbsp;<small>({{isset($date['percentage'])?numberConverter($date['percentage'],1,'pre'):0.0}}%)</small></td>
                    @endforeach
                    @endif
                </tr>
                @if (is_array($totelCountryCost['operator']) && (isset($totelCountryCost['operator']) || !empty($totelCountryCost['operator'])))
                @foreach ($totelCountryCost['operator'] as $operator)
                <tr class="{{ isset($countryClass) ? strpos($countryClass, '.') ? str_replace('.', '_', $countryClass) : $countryClass : '' }} expandable operator-odd-bg" style="display: none;">
                    <td class="first-col"><span class="ml-4"><a href="{{ route('report.details','?operator='.$operator['operator']['id_operator']) }}" target="_blank" class="text-info">{{isset($operator['operator'])?$operator['operator']['operator_name']:''}}</a></span></td>
                    <td class="subs">{{isset($operator['gros_rev']) ? numberConverter($operator['gros_rev']['total'],2,''):''}}</td>
                    <td>{{isset($operator['gros_rev']) ? numberConverter($operator['gros_rev']['avg'],2,''):''}}</td>
                    <td>{{isset($operator['gros_rev']) ? numberConverter($operator['gros_rev']['t_mo_end'],2,''):''}}</td>

                    @if (isset($operator['gros_rev']['dates']) || !empty($operator['gros_rev']['dates']))
                    @foreach (array_reverse($operator['gros_rev']['dates']) as $date)
                    <td class="{{$date['class']}}">{{numberConverter($date['value'],2,'')}}&nbsp;<small>({{isset($date['percentage'])?numberConverter($date['percentage'],1,'pre'):0.0}}%)</small></td>
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


    {{-- <div class="d-flex align-items-center my-2 pull-right">
        <span class="badge badge-secondary px-2 bg-primary" id="loadTimer">Load Time :{{ round(microtime(true) - LARAVEL_START, 3) }}s</span> --}}
    </div>

    <button type="button" id="button" class="btn btn-danger btn-floating btn-sm btn-back-to-top-position up"><i class="fa fa-arrow-up"></i></button>
</div>

@endsection
