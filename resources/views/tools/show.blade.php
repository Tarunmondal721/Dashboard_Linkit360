@extends('layouts.admin')

@section('title')
{{ __('Tools') }}
@endsection

@section('content')
<div class="main-content position-relative">
    <div class="page-content">
        <div class="card shadow-sm mt-0" id="inputCSTool">
            <div class="card-body">
                {{-- <div class="row">
                    <div class="col-md-12">
                        <div class="subHeading">Input MSISDN</div>
                    </div>
                </div> --}}
                <form action="{{ route('report.tools.show') }}" method="GET" onsubmit="return submitCsTools()">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="subHeading">*Country</label>
                            <div class="form-group">
                                <select id="country_msisdn" required onchange="countryChangeMsisdn()" name="country" class="form-control select2" >
                                    <option value="" selected>Select Country</option>
                                    @foreach ($countrys as $country)
                                        <option value="{{$country->id}}">{{$country->country}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="subHeading">Operator</label>
                            <div class="form-group">
                                <select id="operator"  name="operator" class="form-control select2" >
                                    <option value="">Select Operator</option>

                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="subHeading">*Msisdn</label>
                            <div class="form-group">
                                <input type="text" class="form-control fild-Style" required id="msisdn" name="msisdn" aria-describedby="emailHelp" placeholder="Input MSISDN">
                            </div>
                        </div>
                        <div class="col-md-3 ">
                            <div class="form-group mt-14 "  style="margin-top: 55px !important">
                                <button type="submit" class="btn btn-primary btn-cstools" >Submit</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @if (isset($tools) && count($tools) > 0)

        <div class="row">
            <div class="col-md-12">
                <div class="d-flex align-items-center my-3">
                    <span class="badge badge-with-flag badge-secondary px-2 bg-primary ">
                        <img src="{{ asset('/flags/'.$iconCountry) }}" width="30"
                            height="20">
                        Summary User | Last Update : {{$dateLatest}} Asia/Jakarta
                    </span>
                    <span class="flex-fill ml-2 bg-primary w-100 font-weight-bold"
                        style="height: 1px;"></span>
                        <div class="text-right pl-2 buttonDeskripsi" >
                            <button class="btn btn-sm  buttonDeskripsi" onclick="descriptionButton()"  id="buttonDeskripsi" style="color:white; background-color:green" type="button" 
                                data-param="all"><i class="fa fa-info-circle"></i> Glosary</button>
                        </div>
                </div>
            </div>
        </div>
        <div class="card shadow-sm mt-0">
            <div class="card-body">
                <div class="tools">
                    <div class="row">
                        <div class="col-lg-3 col-md-6">
                            <table class="table tableBg">
                                <tbody>
                                    <tr>

                                        <td><b>MSISDN</b></td>
                                        <td>{{$tools[0]['msisdn']}}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Country</b></td>
                                        <td>{{$tools[0]['country_name']}}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Total Operator</b></td>
                                        <td>{{$totalOperator}}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Total Active Service</b></td>
                                        <td>{{$serviceActive}}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Total Inactive Service</b></td>
                                        <td>{{$serviceInactive}}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Total Attempt Charge</b></td>
                                        <td>{{$totalAmountCharge}}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Total Success Charging</b></td>
                                        <td>{{$totalChargeSuccess}}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Total Spending Revenue</b></td>
                                        <td>{{number_format($totalSpending,2) . " " . $currencyCode}}</td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="d-flex align-items-center my-3">
                    <span class="badge badge-with-flag badge-secondary px-2 bg-primary ">
                        @if (!empty($iconCountry))
                            <img src="{{ asset('/flags/'.$iconCountry) }}" width="30"
                        height="20">
                        @endif
                        Detail Services | Last Update : {{$dateLatest}} Asia/Jakarta </span>
                    <span class="flex-fill ml-2 bg-primary w-100 font-weight-bold" style="height: 1px;"></span>
                    
                </div>
            </div>
        </div>

        @php
            $array=[];
        @endphp
        @foreach ($tools as $tool )
        <div class="card shadow-sm mt-0">
            <div class="card-body">
                <div class="tools">
                    <div class="row">
                        <div class="col-lg-3 col-md-6">
                            <table class="table tableBg  ">
                                <tbody>
                                    <tr>
                                        {{--dd($tool);--}}
                                        <td><b>MSISDN</b></td>
                                        <td>{{isset($tool) && !empty($tool['msisdn'])?$tool['msisdn']:'NA'}}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Country</b></td>
                                        <td>{{isset($tool) && !empty($tool['country_name'])?$tool['country_name']:'NA'}}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Operator</b></td>
                                        <td>{{isset($tool) && !empty($tool['operator'])?$tool['operator']:'NA'}}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Type</b></td>
                                        <td>{{isset($tool) && !empty($tool['type'])?$tool['type']:'NA'}}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Status<sup><i class="ml-1 text-dark fa fa-info-circle"
                                            title="Active &#xA;Inactive"></i></sup></b></td>
                                        <td>
                                            <span class="badge badge-pill {{isset($tool) && ($tool['status'] == 1 )? 'badge-success':'badge-danger'}}">{{isset($tool) && ($tool['status'] == 1 )? 'Active':'Inactive'}}</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                        <div class="col-lg-3 col-md-6">
                            <table class="table tableBg  ">
                                <tbody>
                                    <tr>
                                        <td><b>Service</b></td>
                                        <td>{{isset($tool) && !empty($tool['service'])?$tool['service']:'NA'}}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Cycle</b></td>
                                        <td>{{isset($tool) && !empty($tool['cycle'])?$tool['cycle']:'NA'}}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Adnet</b></td>
                                        <td>{{isset($tool) && !empty($tool['adnet'])?$tool['adnet']:'NA'}}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Freemium</b></td>
                                        <td>{{isset($tool) && !empty($tool['freemium'])?$tool['freemium']:'NA'}}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Revenue</b></td>
                                        <td>{{isset($tool) && !empty($tool['revenue'])?number_format($tool['revenue'],2):number_format(0, 2)}} {{$currencyCode}}</td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                        <div class="col-lg-3 col-md-6">
                            <table class="table tableBg  ">
                                <tbody>
                                    <tr>
                                        <td><b>Subscription Date</b></td>
                                        <td>{{isset($tool) && !empty($tool['subs_date'])?$tool['subs_date'] . " UTC+7":'NA'}}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Renewal Date</b></td>
                                        <td>{{isset($tool) && !empty($tool['renewal_date'])?$tool['renewal_date']. " UTC+7":'NA'}}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Schedule Charge</b></td>
                                        <td>{{isset($tool) && !empty($tool['charge_date'])?$tool['charge_date']. " UTC+7":'NA'}}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Total Charge Attempt</b></td>
                                        <td>{{isset($tool) && !empty($tool['last_charge_attempt'])?$tool['last_charge_attempt']:'NA'}}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Unsubscription Date</b></td>
                                        <td>{{isset($tool)&& !empty($tool['unsubs_date']) ? $tool['unsubs_date'] . " UTC+7": 'NA'}}</td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                        <div class="col-lg-3 col-md-6">
                            <table class="table tableBg  ">
                                <tbody>
                                    <tr>
                                        <td><b>Subs From</b></td>
                                        <td>{{isset($tool) && !empty($tool['subs_source'])?$tool['subs_source']:'NA'}}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Unsubs From</b></td>
                                        <td>{{isset($tool) && !empty($tool['unsubs_from'])?$tool['unsubs_from']:'NA'}}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Handset</b></td>
                                        <td>{{isset($tool) && !empty($tool['handset'])?$tool['handset']:'NA'}}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Browser</b></td>
                                        <td>{{isset($tool) && !empty($tool['browser'])?$tool['browser']:'NA'}}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Profile Status<sup><i class="ml-1 text-dark fa fa-info-circle"
                                            title="Active &#xA;Grace &#xA;Parking &#xA;Purging &#xA;Blacklist &#xA;Inactive"></i></sup></b></td>
                                        <td>
                                            @if (isset($tool) && !empty($tool['profile_status'])) 
                                                @if ($tool['profile_status'] == 'Active' || $tool['profile_status'] == 'active' )
                                                    <span class="badge badge-pill badge-success">{{$tool['profile_status']}}</span>
                                                @elseif($tool['profile_status'] == 'Purging' || $tool['profile_status'] == 'purging' )
                                                    
                                                    <span class="badge badge-pill text-white" style="background-color: red">{{$tool['profile_status']}}</span>
                                                @elseif($tool['profile_status'] == 'Blacklist' || $tool['profile_status'] == 'Blacklist' )
                                                    
                                                    <span class="badge badge-pill badge-dark">{{$tool['profile_status']}}</span>
                                                @elseif($tool['profile_status'] == 'Grace' || $tool['profile_status'] == 'grace' )
                                                    
                                                    <span class="badge badge-pill text-white" style="background-color: #19a7ce">{{$tool['profile_status']}}</span>
                                                @elseif($tool['profile_status'] == 'Parking' || $tool['profile_status'] == 'parking' )
                                                
                                                    <span class="badge badge-pill text-white" style="background-color: #DCBFFF">{{$tool['profile_status']}}</span>
                                                @elseif($tool['profile_status'] == 'inactive' || $tool['profile_status'] == 'Inactive' )
                                                    <span class="badge badge-pill badge-danger">{{$tool['profile_status']}}</span>

                                                @endif

                                            @else
                                                NA
                                            @endif
                                          
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12" style="text-align: right;">
                                @if ($tool['status'] != -1)
                                    <a href="#unsubs-msisdn" onclick="unsubs('{{$tool['country_code']}}', '{{$tool['service']}}', 
                                '{{$tool['msisdn']}}', '{{$tool['operator']}}' )" class="btn btn-danger"  >Unsubscription User Now</a>
                                    
                                @endif
                                @if ($tool['profile_status'] != 'blacklist' || $tool['profile_status'] != 'Blacklist' )
                                    <a href="#blacklist-msisdn" onclick="blacklist('{{$tool['country_code']}}', '{{$tool['service']}}', 
                                    '{{$tool['msisdn']}}', '{{$tool['operator']}}')" class="btn badge-blue"  >Blacklist</a>
                                    
                                @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @php
            $subs1="<tr class=' text-white rev'><td>";
            $subs2=isset($tool)?$tool['charge_date']:'';
            $subs3="</td><td>";
            $subs4=isset($tool)?$tool['service']:'';
            $subs5="</td><td>??</td><td>??</td><td>??</td></tr>";
            $subs=$subs1.$subs2.$subs3.$subs4.$subs5;

            $array[]=$subs;
        @endphp
        @endforeach
        @endif

        <!-- tab cs activity -->
        @if (!empty($toolsLog))
            <div class="d-flex align-items-center my-3">
                <span class="badge badge-with-flag badge-secondary px-2 bg-primary ">
                    Cs Activity
                </span>
                <span class="flex-fill ml-2 bg-primary w-100 font-weight-bold"
                    style="height: 1px;"></span>
                {{-- <div class="text-right pl-2 buttonCsvLogs" >
                    <button class="btn btn-sm buttonCsvLogs" id="buttonCsvLogs"  type="button" style="color:white; background-color:green"
                        data-param="all"><i class="fa fa-file-excel-o"></i>Export as CSV</button>
                </div> --}}
            </div>
            <div class="card shadow-sm mt-0">
                <div class="card-body">
                    <div class="tools">
                        <div class="row ">
                            <div class="col-md-12 ">
                                <div class="table-responsive" >
                                    <div class="row justify-content-start " >
                                        <div class="col-md-2">
                                            <div class="form-group select">
                                                <select id="name_cs" name="name_cs" class="form-control select2" >
                                                    <option value="">CS Name</option>
                                                    @foreach ($listCsName as $csName)
                                                        <option value="{{$csName}}">{{$csName}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group select">
                                                <select id="action_cs" name="action_cs" class="form-control select2" >
                                                    
                                                    <option value="">Select Action</option>
                                                    @foreach ($listActionCs as $actionCs)
                                                        <option value="{{$actionCs}}">{{$actionCs}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group select">
                                                <select id="operator_cs" name="operator_cs" class="form-control select2" >
                                                    <option value="">Select Operator</option>
                                                    @foreach ($listOperatorCs as $operatorCs)
                                                        <option value="{{$operatorCs}}">{{$operatorCs}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group select">
                                                <select id="service_cs" name="service_cs" class="form-control select2" >
                                                    
                                                    <option value="">Select Service</option>
                                                    @foreach ($listServiceCs as $serviceCs)
                                                        <option value="{{$serviceCs}}">{{$serviceCs}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                    </div>

                                    <table class="table table-striped datatable_activity_cs " class="display">
                                        <thead>
                                            <th class="col-1">Date</th>
                                            <th class="col-1">Cs Name</th>
                                            <th class="col-1">Action</th>
                                            <th class="col-1">Operator</th>
                                            <th class="col-1">Service</th>
                                            <th class="col-6">Note</th>
                                            <th class="col-1"></th>
                                            
                                        </thead>
                                        <tbody>
                                            @foreach ($csActivityMapping as $activity)
                                                 <tr >
                                                    <form action="{{route('update.cs.activity')}}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{$activity['id']}}">
                                                    @php
                                                        $dateActivity = strtotime($activity['created_at']);

                                                    @endphp
                                                    <td class="align-middle">{{date("Y-m-d H:i:s", $dateActivity)}}</td>
                                                    <td class="align-middle">{{$activity['cs_name']}}</td>
                                                    <td class="align-middle">    
                                                        @if (($activity['action'] == 'unscubscribe' || $activity['action'] == 'Unsubscribe') )
                                                            <span class="badge badge-pill badge-danger">{{$activity['action']}}</span>
                                                        @else
                                                            <span class="badge badge-pill text-white badge-dark">{{$activity['action']}}</span>
                                                        @endif
                                                    </td>
                                                    <td class="align-middle">{{$activity['operator']}}</td>
                                                    <td class="align-middle">{{$activity['service']}}</td>

                                                    <td class="align-middle" class="text-wrap" >

                                                        <textarea  name="note" id="row-1-position" name="row-1-position" style="border: 1px solid darkgray " class="w-100  rounded " cols="30" rows="2">{{$activity['note']}}</textarea>
                                                    </td>
                                                    <td class="align-middle">
                                                        <button type="submit" class="btn btn-sm badge-blue">Save</button>
                                                    </td>
                                                </form>
                                                </tr>

                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @if (!empty($toolsLog))
            <div class="d-flex align-items-center my-3">
                <span class="badge badge-with-flag badge-secondary px-2 bg-primary ">
                    {{-- <img src="{{ asset('/flags/'.$iconCountry) }}" width="30"
                        height="20"> --}}
                    All Logs | Last Update : {{$dateLatest}} Asia/Jakarta
                </span>
                <span class="flex-fill ml-2 bg-primary w-100 font-weight-bold"
                    style="height: 1px;"></span>
                <div class="text-right pl-2 buttonCsvLogs" >
                    <button class="btn btn-sm buttonCsvLogs" id="buttonCsvLogs"  type="button" style="color:white; background-color:green"
                        data-param="all"><i class="fa fa-file-excel-o"></i>Export as CSV</button>
                </div>
            </div>
            <div class="card shadow-sm mt-0">
                <div class="card-body">
                    <div class="tools">
                        <div class="row ">
                            <div class="col-md-12 ">
                                <div class="table-responsive" >
                                    <div class="row justify-content-start">
                                        <div class="col-md-2">
                                            <div class="form-group select">
                                                <select id="service" name="service" class="form-control select2" >
                                                    <option value="">Select Service</option>
                                                    @foreach ($tools as $tool)
                                                        <option value="{{$tool['service']}}">{{$tool['service']}}</option>                                                    
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group select">
                                                <select id="action" name="action" class="form-control select2" >
                                                    
                                                    <option value="">Select Action</option>
                                                    @foreach ($listAction as $item)
                                                        <option value="{{$item}}">{{$item}}</option>
                                                        
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group select">
                                                <select id="channel" name="channel" class="form-control select2" >
                                                    
                                                    <option value="">Select Channel</option>
                                                    @foreach ($listChannel as $item)
                                                        <option value="{{$item}}">{{$item}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group select">
                                                <select id="status" name="status" class="form-control select2" >
                                                    <option value="">Select Status</option>
                                                    <option value="Paid">Paid</option>
                                                    <option value="Unpaid">Unpaid</option>
                                                </select>
                                            </div>
                                        </div>

                                    </div>

                                    <table class="table table-striped dataTable_logs " class="display">
                                        <thead>
                                            <th>Date</th>
                                            <th>Operator</th>
                                            <th>Service</th>
                                            <th>Action</th>
                                            <th>Channel</th>
                                            <th>Price</th>
                                            <th>Status</th>
                                            <th>Error</th>
                                            <th>Telco Api URl</th>
                                            <th>Telco Api Response</th>
                                            <th>SMS Content</th>
                                            <th>SMS Status</th>
                                            
                                        </thead>
                                        <tbody>
                                            @foreach ($toolsLog as $toolLog)

                                                <tr>
                                                    <td>{{isset($toolLog)?$toolLog['date']:''}}</td>
                                                    <td>{{isset($toolLog)?$toolLog['operator']:''}}</td>
                                                    <td>{{isset($toolLog)?$toolLog['service']:''}}</td>

                                                    <td>    
                                                        @if (isset($toolLog) && ($toolLog['action'] == 'Subscription' || $toolLog['action'] == 'subscribe') )
                                                            <span class="badge badge-pill badge-primary">{{$toolLog['action']}}</span>
                                                        @elseif(isset($toolLog) && ($toolLog['action'] == 'renewal' || $toolLog['action'] == 'Renewal'))
                                                            <span class="badge badge-pill badge-info">{{$toolLog['action']}}</span>
                                                        @elseif(isset($toolLog) && ($toolLog['action'] == 'Unsubscription' || $toolLog['action'] == 'unsubscription') )
                                                            <span class="badge badge-pill badge-danger">{{$toolLog['action']}}</span>
                                                        @elseif(isset($toolLog) && ($toolLog['action'] == 'Single Charge' || $toolLog['action'] == 'first_charge') )
                                                            <span class="badge badge-pill badge-primary">{{$toolLog['action']}}</span>
                                                        @elseif(isset($toolLog) && ($toolLog['action'] == 'Retry firstpush' || $toolLog['action'] == 'Retry renewal') )
                                                            <span class="badge badge-pill badge-warning">{{$toolLog['action']}}</span>
                                                        @else
                                                            <span class="badge badge-pill text-dark badge-secondary">{{$toolLog['action']}}</span>
                                                        @endif
                                                        {{-- {{isset($toolLog)?$toolLog['action']:''}} --}}
                                                    </td>
                                                    <td>{{isset($toolLog)?$toolLog['channel']:''}}</td>
                                                    <td>{{isset($toolLog) ? number_format($toolLog['price'],2):number_format(0, 2)}} {{$currencyCode}}</td>
                                                    <td>
                                                        @if (isset($toolLog) && $toolLog['paid'] == "Paid")
                                                            <span class="badge badge-pill badge-success">{{$toolLog['paid']}}</span>
                                                        @else
                                                            <span class="badge badge-pill badge-danger">{{$toolLog['paid']}}</span>
                                                        @endif
                                                    </td>
                                                    <td>{{isset($toolLog) && !empty($toolLog['errors'])? $toolLog['errors'] : 'NA'}}</td>
                                                    <td class="truncate">{{isset($toolLog) && !empty($toolLog['telco_api_url'])?$toolLog['telco_api_url']:'NA'}}</td>
                                                    <td class="truncate">{{isset($toolLog) && !empty($toolLog['telco_api_response'])?$toolLog['telco_api_response']:'NA'}}</td>
                                                    <td>{{isset($toolLog) && !empty($toolLog['sms_content'])?$toolLog['sms_content']:'NA'}}</td>
                                                    <td>{{isset($toolLog) && !empty($toolLog['status_sms'])?$toolLog['status_sms']:'NA'}}</td>
                                                </tr>
                                                
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- tab section -->
    </div>
</div>
{{-- modal description --}}
<div id="modal-description" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">

        <div class="modal-content ">
            <div class="modal-header bg-white">
                <h5 class="modal-title text-center">Glosary CS Tools</h5>
                    <button type="buttonr" class="btn btn-danger " data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="text-white">&times;</span>
                    </button>
            </div>
            <div class="modal-body bg-white">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-10">
                            <table class="table table-bordered">
                                <thead class="thead-dark">
                                    <th>Field Name</th>
                                    <th>Description</th>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="bg-secondary" colspan="2">Service Summary & Detail Section</td>
                                    </tr>
                                    <tr>
                                        <td>MSISDN</td>
                                        <td>MSISDN	Data msisdn from user CS team want to see</td>
                                    </tr>
                                    <tr>
                                        <td>Country</td>
                                        <td>Country	Country of the MSISDN user</td>
                                    </tr>
                                    <tr>
                                        <td>Total Active Service</td>
                                        <td>How many service user still active</td>
                                    </tr>
                                    <tr>
                                        <td>Total In Active Service</td>
                                        <td>How many service user not active</td>
                                    </tr>
                                    <tr>
                                        <td>Total Attempt Charge</td>
                                        <td>How many attempt user are charged in all active service </td>
                                    </tr>
                                    <tr>
                                        <td>Total Success Charging</td>
                                        <td>How many success charging user get in all active service</td>
                                    </tr>
                                    <tr>
                                        <td>Total Spending Revenue</td>
                                        <td>Show how many spending user get for all active service</td>
                                    </tr>
                                    <tr>
                                        <td>Operator</td>
                                        <td>Show what operator user is using</td>
                                    </tr>
                                    <tr>
                                        <td>Type</td>
                                        <td class="text-wrap">Status subscription user, is it still subscription or get renewal or unsubscription</td>
                                    </tr>
                                    <tr>
                                        <td>Status</td>
                                        <td>To show status active user, is it still active or inactive</td>
                                    </tr>
                                    <tr>
                                        <td>Service</td>
                                        <td>Show what service user subscribe</td>
                                    </tr>
                                    <tr>
                                        <td>Cycle</td>
                                        <td>Show what cycle subscription in the service</td>
                                    </tr>
                                    <tr>
                                        <td>Adnet</td>
                                        <td>Show what partner or adnet user subscribe</td>
                                    </tr>
                                    <tr>
                                        <td>Freemium</td>
                                        <td>To show last date of freemium user</td>
                                    </tr>
                                    <tr>
                                        <td>Revenue</td>
                                        <td class="text-wrap">To Show how many revenue we can get from the service user subscribe</td>
                                    </tr>
                                    <tr>
                                        <td>Subscription Date</td>
                                        <td>When user do subscribe</td>
                                    </tr>
                                    <tr>
                                        <td>Renewal Date</td>
                                        <td>When user will be renewal</td>
                                    </tr>
                                    <tr>
                                        <td>Schedule Charge</td>
                                        <td>To show when user will be get charged</td>
                                    </tr>
                                    <tr>
                                        <td>Total Charge Attempt</td>
                                        <td>How many attempt user are charged In active service</td>
                                    </tr>
                                    <tr>
                                        <td>Unsubscription Date</td>
                                        <td>When last user do unsubscription</td>
                                    </tr>
                                    <tr>
                                        <td>Subs From</td>
                                        <td>Show what channel user subscribe</td>
                                    </tr>
                                    <tr>
                                        <td>Unsubs From</td>
                                        <td>Show what channel user unsubscribe</td>
                                    </tr>
                                    <tr>
                                        <td>Handset</td>
                                        <td>Show what type of phone / device user user</td>
                                    </tr>
                                    <tr>
                                        <td>Browser</td>
                                        <td>Show what type and version browser user use</td>
                                    </tr>
                                    <tr>
                                        <td rowspan="7" class="align-middle">Profile Status</td>
                                        <td>Show what type and version browser user use</td>
                                    </tr>
                                    <tr>
                                        <td>Active = user active in subscribe</td>
                                    </tr>
                                    <tr>
                                        <td>Grace = user got 30 times failed charge</td>
                                    </tr>
                                    <tr>
                                        <td>Parking = user got 60 times failed charge</td>
                                    </tr>
                                    <tr>
                                        <td class="text-wrap">Purging = user got 90 times failed charge, and user status user will change to be inactive</td>
                                    </tr>
                                    <tr>
                                        <td>Blacklist = user cannot do subscription on specific service</td>
                                    </tr>
                                    <tr>
                                        <td>Inactive = user not active in subscribe</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="bg-secondary">CS Activity Section</td>
                                    </tr>
                                    <tr>
                                        <td>Date</td>
                                        <td>What time CS Team to do</td>
                                    </tr>
                                    <tr>
                                        <td>CS Name</td>
                                        <td>To show name CS Team</td>
                                    </tr>
                                    <tr>
                                        <td>Action</td>
                                        <td>To show what action CS Team to do in cs tools</td>
                                    </tr>
                                    <tr>
                                        <td>Operator</td>
                                        <td>Show what operator CS Team to do on user</td>
                                    </tr>
                                    <tr>
                                        <td>Service</td>
                                        <td>Show what service CS Team to do on user</td>
                                    </tr>
                                    <tr>
                                        <td>Note</td>
                                        <td>To input note what CS Team to do</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="bg-secondary">Logs Section</td>
                                    </tr>
                                    <tr>
                                        <td>Date</td>
                                        <td>what time user to do</td>
                                    </tr>
                                    <tr>
                                        <td>Operator</td>
                                        <td>Show what user do in specific operator</td>
                                    </tr>
                                    <tr>
                                        <td>Service</td>
                                        <td>Show what user do in specific service</td>
                                    </tr>
                                    <tr>
                                        <td>Channel</td>
                                        <td>What channel user do</td>
                                    </tr>
                                    <tr>
                                        <td>Price</td>
                                        <td>Price of the service</td>
                                    </tr>
                                    <tr>
                                        <td class="align-middle" rowspan="2">Status</td>
                                        <td>Paid = user get charged</td>
                                    </tr>
                                    <tr>
                                        <td>Unpaid = user failed get charged</td>
                                    </tr>
                                    <tr>
                                        <td>Error</td>
                                        <td>To show what error user get failed charged</td>
                                    </tr>
                                    <tr>
                                        <td>Telco API URL</td>
                                        <td>To show the API subscription developer hit to telco</td>
                                    </tr>
                                    <tr>
                                        <td>Telco API Response</td>
                                        <td>To show what response we got from telco side</td>
                                    </tr>
                                    <tr>
                                        <td>SMS Content</td>
                                        <td>To show what sms content we sent to user</td>
                                    </tr>
                                    <tr>
                                        <td class="align-middle" rowspan="2">SMS Status</td>
                                        <td>Sent = Success sent sms to user</td>
                                    </tr>
                                    <tr>
                                        <td>Failed = failed sent sms to user</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    

                </div>
            </div>
            
        </div>
    </div>
</div>


{{-- modal unsubs --}}
<div id="unsubs-msisdn" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog  modal-sm"> <!-- Change modal-dialog class to modal-dialog-centered modal-sm -->
        <form id="unsubs-msisdn-form"  method="POST" action="{{ route('unsubs') }}">
            @method("delete")
            @csrf
            <input type="hidden" name="country" value="">
            <input type="hidden" name="service" value="">
            <input type="hidden" name="msisdn" value="">
            <input type="hidden" name="operator" value="">
          
            <div class="modal-content bg-white">

                <div class="modal-header bg-white">
                    <h5 class="modal-title text-center">Unsubs Msisdn</h5>
                        <button type="buttonr" class="btn btn-danger " data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="text-white">&times;</span>
                        </button>
                </div>
                <div class="modal-body">
                  <p class="pl-5"></p>
                </div>
                <div class="modal-footer pl-5">
                  <button type="submit" class="btn btn-sm btn-danger">Yes</button>
                  <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                </div>
              </div>
        </form>
    </div>
</div>

{{-- modal blacklist --}}
<div id="blacklist-msisdn" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="blacklist-msisdn-form"  method="POST" action="{{ route('blacklist') }}">
            @method("delete")
            @csrf
            <input type="hidden" name="country" value="">
            <input type="hidden" name="service" value="">
            <input type="hidden" name="msisdn" value="">
            <input type="hidden" name="operator" value="">

            <div class="modal-content bg-white ">
                <div class="modal-header bg-white">
                    <h5 class="modal-title text-center">Blacklist Msisdn</h5>
                        <button type="buttonr" class="btn btn-danger " data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="text-white">&times;</span>
                        </button>
                </div>
                <div class="modal-body">
                  <p class="pl-5"></p>
                </div>
                <div class="modal-footer pl-5">
                  <button type="submit" class="btn btn-sm btn-danger">Yes</button>
                  <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                </div>
              </div>
        </form>
    </div>
</div>


<div class="modal fade" id="emptyDataCsTools" tabindex="-1" role="dialog" aria-labelledby="emptyDataCsToolsExample" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="emptyDataCsToolsExample">Empty Data Arpu</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true" class="text-danger">&times;</span>
          </button>
        </div>
        <div class="modal-body text-dark text-center">
            Cs Tools data is empty
        </div>
        <div class="modal-footer">
          {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary"></button> --}}
        </div>
      </div>
    </div>
  </div>
{{-- modal timeout --}}
<div class="modal fade" id="errorApiCsTool" tabindex="-1" role="dialog" aria-labelledby="errorApiCsToolLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="errorApiCsToolLabel">API TIMEOUT</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true" class="text-danger">&times;</span>
          </button>
        </div>
        <div class="modal-body text-dark text-center">
          The API provider has a problem, Please re-submit.
        </div>
        <div class="modal-footer">
          {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary"></button> --}}
        </div>
      </div>
    </div>
  </div>
    @if (session('error_api'))
    <script>
        $(window).on('load', function() {
          Swal.fire({
              icon: 'error',
              title: "API TIMEOUT",
              text: "The API provider has a problem, Please re-submit.",
              toast: true,
              position: 'top-end',
              showConfirmButton: true,
          });      
        });
      </script>
    @endif
    @if (session('error_empty'))
    
    <script>
        $(window).on('load', function() {
          Swal.fire({
              icon: 'error',
              text: "{{session('error_empty')}}",
              toast: true,
              position: 'top-end',
              showConfirmButton: true,
              
          });
      
          // $('#emptyDataModal').modal('show');
        });
      </script>
    @endif

<script>
    function countryChangeMsisdn() {
        var value = $("#country_msisdn").val();
        console.log(value);
        $.ajax({
          type: "POST",
          url: baseUrl+"report/mappingoperator",
          data:{'id':value},
          dataType: "json",
          success: function (responses) {
            document.getElementById('operator').innerHTML ='<option value="">Select Operator</option>';
            $("#msisdn").attr('placeholder', responses.exampleNumber).blur();
            $.each(responses.data, function(index,response){
                    $("#operator").append('<option value="'+response.operator_id+'">'+response.operator+'</option>');
              })
          }
        })
    }
    function descriptionButton() {
        console.log("tes");
        var descriptionModal = $('div#modal-description');
        descriptionModal.modal('show');

    }
    function blacklist(country, service, msisdn, operator){
        var blacklistModal = $('div#blacklist-msisdn'),
                blacklistForm  = $('form#blacklist-msisdn-form'),
                submitBtn      = blacklistForm.find('button[type=submit]');
                // var date    = $(this).data('ip-id'),
                message = "@lang('Are you sure you want to Blacklist Msisdn :msisdn?')";
        
                blacklistForm.find('input[name=country]').val(country);
                blacklistForm.find('input[name=service]').val(service);
                blacklistForm.find('input[name=msisdn]').val(msisdn);
                blacklistForm.find('input[name=msisdn]').val(operator);
                // blacklistModal.find('.modal-body p').html(message.replace(':ip', ip));  
                // blacklistModal.find('.modal-body p').html(message);  
                blacklistModal.find('.modal-body p').html(message.replace(':msisdn',msisdn ));  

                blacklistModal.modal('show');
    }
    function unsubs(country, service, msisdn, operator){
            var unsubsModal = $('div#unsubs-msisdn'),
                unsubsForm  = $('form#unsubs-msisdn-form'),
                submitBtn      = unsubsForm.find('button[type=submit]');
                // var date    = $(this).data('ip-id'),
                message = "@lang('Are you sure you want to Unsubscription Msisdn :msisdn?')";
        
                unsubsForm.find('input[name=country]').val(country);
                unsubsForm.find('input[name=service]').val(service);
                unsubsForm.find('input[name=msisdn]').val(msisdn);
                unsubsForm.find('input[name=operator]').val(operator);
                unsubsModal.find('.modal-body p').html(message.replace(':msisdn',msisdn ));  
                // unsubsModal.find('.modal-body p').html(message);  
        
                unsubsModal.modal('show');
        }
</script>
<script>
    function submitCsTools(){
        $(".btn-cstools").html('<i class="fa fa-spinner fa-spin"></i>');
        // $(".btn-cstools").attr("disabled", 'disabled');
        Swal.fire({
        title: '',
        allowOutsideClick: false,
        showConfirmButton: false,
        html: '<div style="display: flex; align-items: center; justify-content: center; height: 100px;"><i class="fas fa-spinner fa-spin" style="font-size: 3rem;color:white"></i></div>',
          onBeforeOpen: () => {
            Swal.showLoading();
          },
          didRender: () => {
            document.querySelector('.swal2-popup').style.padding = '0px';
            document.querySelector('.swal2-popup').style.background = 'transparent';
            document.querySelector('.swal2-html-container').style.overflow = 'hidden';
          }  
        });
      setTimeout(() => {
          Swal.close();
      }, 200000);
    }
</script>
@endsection
