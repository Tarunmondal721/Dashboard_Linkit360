@extends('layouts.admin')

@section('title')
     {{ __("Ads Spending Details") }}
@endsection
@section('pagetytle')
     {{ __("Spending ADs Details By Operator") }}
@endsection
@section('content')
@php 
    $Operator = request()->get('operator');
@endphp

<div class="page-content">
    <div class="adsDetails"><div>{{ $Operator }}</div></div>
    <div class="table-responsive shadow-sm mb-3 tableFixHead" style="display: block; !important">
        <table class="table table-light table-bordered m-0 font-13 table-text-no-wrap" id="adsTbl">
            <thead class="thead-dark">
                <tr>
                    <th>Adnet</th>
                    <th>Data</th>
                    <th>Total</th>
                    <th>Avg</th>
                    <th>T.Mo.End</th>
                    @foreach ($no_of_days as $no_of_day)
                    <th>{{$no_of_day['no']}}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="detailsAds">
                @if (isset($sumemry) || !empty($sumemry))
                @php $i = 0; @endphp
                @foreach ($sumemry as $data)
                <tr class="<?php echo ($i % 2 == 0) ? 'country-odd-bg' : '' ?>">
                    <td class="align-middle text-center font-weight-bold first-col adnetName" rowspan="4">{{$data['adnet']}}</td>
                    <td class="font-weight-bold">Cost Campaign</td>
                    <td class="font-weight-bold">{{numberConverter($data['cost_campaign']['total'],2,'pre')}}</td>
                    <td class="font-weight-bold">{{numberConverter($data['cost_campaign']['avg'],2,'pre')}}</td>
                    <td class="font-weight-bold">{{numberConverter($data['cost_campaign']['t_mo_end'],2,'pre')}}</td>

                    @if(isset($data['cost_campaign']['dates']) && !empty($data['cost_campaign']['dates']))
                    @foreach ($data['cost_campaign']['dates'] as $cost_campaign)
                    <td class="font-weight-bold {{ $cost_campaign['class'] }}">{{numberConverter($cost_campaign['value'],2,'pre')}}</td>
                    @endforeach
                    @endif
                </tr>
                <tr class="<?php echo ($i % 2 == 0) ? 'country-odd-bg' : '' ?>">
                    <td class="font-weight-bold">MO</td>
                    <td class="font-weight-bold">{{numberConverter($data['MO']['total'],2,'pre')}}</td>
                    <td class="font-weight-bold">{{numberConverter($data['MO']['avg'],2,'pre')}}</td>
                    <td class="font-weight-bold">{{numberConverter($data['MO']['t_mo_end'],2,'pre')}}</td>

                    @if(isset($data['MO']['dates']) && !empty($data['MO']['dates']))
                    @foreach ($data['MO']['dates'] as $MO)
                    <td class="font-weight-bold {{ $MO['class'] }}">{{ numberConverter($MO['value'],2,'pre') }}</td>
                    @endforeach
                    @endif
                </tr>
                <tr class="<?php echo ($i % 2 == 0) ? 'country-odd-bg' : '' ?>">
                    <td class="font-weight-bold">Price/MO</td>
                    <td class="font-weight-bold">{{numberConverter($data['price_mo']['total'],2,'post')}}</td>
                    <td class="font-weight-bold">{{numberConverter($data['price_mo']['avg'],2,'post')}}</td>
                    <td class="font-weight-bold">{{numberConverter($data['price_mo']['t_mo_end'],2,'post')}}</td>

                    @if(isset($data['price_mo']['dates']) && !empty($data['price_mo']['dates']))
                    @foreach ($data['price_mo']['dates'] as $price_mo)
                    <td class="font-weight-bold {{ $price_mo['class'] }}">{{numberConverter($price_mo['value'],2,'post')}}</td>
                    @endforeach
                    @endif
                </tr>
                <tr class="<?php echo ($i % 2 == 0) ? 'country-odd-bg' : '' ?>">
                    <td class="font-weight-bold">CR</td>
                    <td class="font-weight-bold">{{numberConverter($data['cr']['total'],2,'pre')}}</td>
                    <td class="font-weight-bold">{{numberConverter($data['cr']['avg'],2,'pre')}}</td>
                    <td class="font-weight-bold">{{numberConverter($data['cr']['t_mo_end'],2,'pre')}}</td>

                    @if(isset($data['cr']['dates']) && !empty($data['cr']['dates']))
                    @foreach ($data['cr']['dates'] as $cr)
                    <td class="font-weight-bold {{ $cr['class'] }}">{{numberConverter($cr['value'],2,'pre')}}</td>
                    @endforeach
                    @endif
                </tr>
                @php $i++; @endphp
                @endforeach
                @endif
            </tbody>
        </table>
    </div>

    <button type="button" id="button" class="btn btn-danger btn-floating btn-sm btn-back-to-top-position up"><i class="fa fa-arrow-up"></i></button>
</div>

@endsection
