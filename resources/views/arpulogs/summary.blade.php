@extends('layouts.admin')

@section('title')
    {{ __('Arpu Logs') }}
@endsection
@section('pagetytle')
    {{ __("Monitor Arpu Summary") }}
@endsection
@section('content')

@section('content')
<div class="row">
    <div class="col-12">
            @include('arpulogs.partials.filtersummary')
            <div class="table-responsive shadow-sm mb-3 tableFixHead">
                <table class="table table-light table-bordered m-0 font-13 table-text-no-wrap" id="adsTbl">
                    <thead class="thead-dark">
                        <tr>
                            <th class="first-col" width="10%">Operator Name</th>
                            <th>Total Data Transactions</th>
                            <th>Total Data Subscriptions</th>
            
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold first-col">All Operator </td>
                            <td>{{isset($totalTransactionAllCountry)? number_format($totalTransactionAllCountry,2,",",".") :"N/A"}}</td>
                            <td>{{isset($totalSubsriptionAllCountry)? number_format($totalSubsriptionAllCountry,2,",",".") :"N/A"}}</td>
                        </tr>
                    </tbody>
                    <tbody><tr><td style="height: 20px;"></td></tr></tbody>
                    <thead class="thead-dark">
                        <tr>
                            <th class="first-col" width="10%">Operator Name</th>
                            <th>Total Data Transactions</th>
                            <th>Total Data Subscriptions</th>
                        </tr>
                    </thead>
                    @if(isset($summaryData) && !empty($summaryData) && $summaryData != 1)
                          @foreach ($summaryData as $key => $details)
                          @php
                                $summaryArray=explode(' ',isset($details['country'])?$details['country']['country']:'');
                                $summaryClass0= isset($summaryArray[0])? $summaryArray[0]:'';
                                $summaryClass1=isset($summaryArray[1])?'_'.$summaryArray[1]:'';
                                $summaryClass2=isset($summaryArray[2])?'_'.$summaryArray[2]:'';
                                $summaryClass=$summaryClass0.$summaryClass1.$summaryClass2;
                                
                                @endphp
                            {{-- <tbody > --}}
                            <tbody class="ptables" id="{{ isset($summaryClass) ? strpos($summaryClass, '.') ? str_replace('.', '_', $summaryClass) : $summaryClass : '' }}_adsTblBdy">
                      
            
                            <tr class="country-odd-bg">
                                
                                <td class="font-weight-bold first-col"><span class="ossbtn" data-param="{{ isset($summaryClass) ? strpos($summaryClass, '.') ? str_replace('.', '_', $summaryClass) : $summaryClass : '' }}" style="cursor:pointer;min-width:10px; font-size:20px;">@if(count($details)>0)<strong>+</strong>@endif</span> <img src="{{ asset('/flags/'.$details['country']['flags']) }}" height="20" width="30"> {{$details['country']['country'] }} </td>
            
                                <td class="font-weight-bold  transaction-col">{{ isset($details['country']['total_transactions']) ? numberConverter($details['country']['total_transactions'] ,2,'') : 'N/A' }}</td>
                                <td class="font-weight-bold  subscription-col">{{ isset($details['country']['total_transactions']) ? numberConverter($details['country']['total_subscriptions'] ,2,'') : 'N/A' }}</td>
                
                            </tr>
                                    @if (count($details['operator']) >0)   
                                    @foreach ($details['operator'] as $pubid)
                                        <tr style="" class="{{ isset($summaryClass) ? strpos($summaryClass, '.') ? str_replace('.', '_', $summaryClass) : $summaryClass : '' }}  expandable operator-odd-bg" style="display: none;">
                                            
                                            <td class="subs"><span class="ml-4"><a href="javascript:void(0)" data-url="{{ URL::to('/arpu/detail-operator'.'/'.$details['country']['country'] ."/" .$pubid['operator']) }}" data-ajax-popup="true" data-title="{{__('Detail services  #'.$pubid['operator'])}}" class="text-info ">{{isset($pubid['operator'])?$pubid['operator']:'N/A'}}</a></span></td>
                                            <td class="subs">{{isset($pubid['transactions'])? numberConverter($pubid['transactions'] ,2,'') :"N/A"}}</td>
                                            <td class="subs">{{isset($pubid['subscriptions'])? numberConverter($pubid['subscriptions'] ,2,'') :"N/A"}}</td>
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
            
            <button type="button" id="button" class="btn btn-danger btn-floating btn-sm btn-back-to-top-position up"><i class="fa fa-arrow-up"></i></button>
            </div>
        </div>
    </div>

@endsection