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
            <div class="col-md-12">
                <div class="d-flex align-items-center my-3">
                    <span class="badge badge-with-flag badge-secondary px-2 bg-primary ">
                        Summary Data Msisdn
                    </span>
                    <span class="flex-fill ml-2 bg-primary w-100 font-weight-bold"
                        style="height: 1px;"></span>
                        <div class="text-right pl-2 buttonDeskripsi" >
                            <button class="btn btn-sm  buttonDeskripsi"   id="buttonExportMsisdn" style="color:white; background-color:green"  type="button" 
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
                                            <td><b>Adnet Name</b></td>
                                            <td>{{$adnet}}</td>
                                        </tr>
                                        <tr>
                                            <td><b>Total MSISDN</b></td>
                                            <td>{{count($result['data'])}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row ">
                            <div class="col-md-12 ">
                                <div class="table-responsive" >
                                    <table class="table table-striped datatableMsisdn " class="display">
                                        <thead class="bg-dark">
                                            <th class=" text-white bg-dark">No</th>
                                            <th class=" text-white bg-dark">Country</th>
                                            <th class="text-white bg-dark">Msisdn</th>
                                        </thead>
                                        <tbody>
                                            @php
                                                $count=1;
                                            @endphp
                                            @foreach ($result['data'] as $data)
                                            <tr>
                                                <td>{{$count++}}</td>
                                                <td>{{$data['country']}}</td>
                                                <td><a target="_blank" onclick="openInNewWindow('{{ route('report.detail.cstool', [
                                                    'msisdn' => $data['msisdn'],
                                                    'country' => $data['country'],
                                                    'from' => $from,
                                                    'renewal' => $renewal,
                                                ]) }} ')">{{$data['msisdn']}} <i class="fa fa-info-circle"></i></a></td>
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

        <!-- tab section -->
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.4/xlsx.full.min.js"></script>
<script>
        function openInNewWindow(url) {
            console.log(url);
            window.open(url, '_blank', 'toolbar=0,location=0,menubar=0,width=800,height=600');
        }
</script>
@endsection
