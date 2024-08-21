@extends('layouts.admin')

@section('title')
{{ __('Tools') }}
@endsection

@section('content')

<div class="main-content position-relative">
    <div class="page-content">
        <div class="page-title" style="margin-bottom:25px">
            <div class="row justify-content-between align-items-center">
                <div
                    class="col-xl-4 col-lg-4 col-md-4 d-flex align-items-center justify-content-between justify-content-md-start mb-3 mb-md-0">
                    <div class="d-inline-block">
                        <h5 class="h4 d-inline-block font-weight-400 mb-0"><b>Arpu Details</b></h5><br>
                        <p class="d-inline-block font-weight-200 mb-0">Summary of Arpu</p>
                    </div>
                </div>
                <div
                    class="col-xl-8 col-lg-8 col-md-8 d-flex align-items-center justify-content-between justify-content-md-end">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <div class="row">
                    <div class="col-md-12">
                        <div class="d-flex align-items-center my-3">
                            <span class="badge badge-with-flag badge-secondary px-2 bg-primary ">
                                Summary Data Msisdn
                            </span>
                            <span class="flex-fill ml-2 bg-primary w-100 font-weight-bold"
                                style="height: 1px;"></span>
                                <div class="text-right pl-2 buttonDeskripsi" >
                                    <button class="btn btn-sm  buttonDeskripsi"   id="buttonCsTool" style="color:white; background-color:green" type="button" 
                                        data-param="all">Export As Csv</button>
                                </div>
                        </div>
                    </div>
                </div>
                <div class="card shadow-sm mt-0">
                    <div class="card-body">
                            <div class="tools">
                                <div class="row ">
                                    <div class="col-lg-3 col-md-6">
                                        <table class="table">
                                            <tbody>
                                                <tr>
                                                    <td><b>MSISDN</b></td>
                                                    <td>{{$msisdn}}</td>
                                                </tr>
                                                <tr>
                                                    <td><b>Total Revenue</b></td>
                                                    <td>{{$totalRevenue}}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="row ">
                                    <div class="col-md-12 ">
                                        <div class="table-responsive" >
                                            <div class="row justify-content-start">
                                                <div class="col-md-6 col-lg-2 col-12">
                                                    <div class="form-group select">
                                                        <select id="select_source" name="select_source" class="form-control select2" >
                                                            <option value="">Select Source</option>
                                                            @foreach ($listSource as $source)
                                                                <option value="{{$source}}">{{$source}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-lg-2 col-12">
                                                    <div class="form-group select">
                                                        <select id="select_event" name="select_event" class="form-control select2" >
                                                            <option value="">Select Event</option>
                                                            @foreach ($listEvent as $event)
                                                                <option value="{{$event}}">{{$event}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-lg-2 col-12">
                                                    <div class="form-group select">
                                                        <select id="select_publisher" name="select_publisher" class="form-control select2" >
                                                            <option value="">Select Publisher</option>
                                                            @foreach ($listPublisher as $publisher)
                                                                <option value="{{$publisher}}">{{$publisher}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-lg-2  col-12">
                                                    <div class="form-group select">
                                                        <select id="select_handset" name="select_handset" class="form-control select2" >
                                                            <option value="">Select Handset</option>
                                                            @foreach ($listHandset as $handset)
                                                                <option value="{{$handset}}">{{$handset}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-lg-2 col-12">
                                                    <div class="form-group select">
                                                        <select id="select_browser" name="select_browser" class="form-control select2" >
                                                            <option value="">Select Browser</option>
                                                            @foreach ($listBrowser as $browser)
                                                                <option value="{{$browser}}">{{$browser}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <table class="table table-striped datatableMsisdnCsTool " class="display">
                                                <thead class="bg-dark">
                                                    <th class=" text-white bg-dark">No</th>
                                                    <th class="text-white bg-dark">Source </th>
                                                    <th class="text-white bg-dark">Event</th>
                                                    <th class="text-white bg-dark">Event Date</th>
                                                    <th class="text-white bg-dark">Currency</th>
                                                    <th class="text-white bg-dark">Revenue</th>
                                                    <th class="text-white bg-dark">Publisher</th>
                                                    <th class="text-white bg-dark">Handset</th>
                                                    <th class="text-white bg-dark">Browser</th>
                                                    <th class="text-white bg-dark">Pixel</th>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $count =1;
                                                    @endphp
                                                    @foreach ($result['data']['subs'] as $data)
                                                        <tr>
                                                            <td>{{$count++}}</td>
                                                            <td>{{$data['subs_source']}}</td>
                                                            <td>{{$data['type']}}</td>
                                                            <td>{{$data['subs_date']}}</td>
                                                            <td>{{$currency??""}}</td>
                                                            <td>{{$data['revenue'] ?? 0}}</td>
                                                            <td>{{$data['adnet'] ?? 0}}</td>
                                                            <td>{{$data['handset'] ?? 0}}</td>
                                                            <td>{{$data['browser'] ?? 0}}</td>
                                                            <td>{{$data['pixel'] ?? "NA"}}</td>
                                                            
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
            </div>
            <div class="col-6">
                <iframe id="myIframe" src="{{ route('report.tools.show', ['country' => $country, 'msisdn' => $msisdn]) }}" style="width: 100%" height="800" frameborder="0"></iframe>
            </div>

        </div>


        <!-- tab section -->
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.4/xlsx.full.min.js"></script>



    
@endsection
