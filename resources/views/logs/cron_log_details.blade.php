@extends('layouts.admin')

@section('title')
    {{ __('Cron Details') }}
@endsection

{{-- @section('action-button')
    <div class="all-button-box row d-flex justify-content-end">
        <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
            <a href="javascript:void(0)" class="btn btn-xs btn-white btn-icon-only width-auto" data-ajax-popup="true" data-title="{{__('Create Country')}}" data-url="{{route('management.add-currency')}}">
                <i class="fas fa-plus"></i> {{__('Add')}}
            </a>
        </div>
    </div>
@endsection --}}

@section('content')
@include('logs.log_filter')

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped dataTable"
                    data-ordering="false"
                    >
                        <thead>
                        <tr>
                            <th>{{__('Status')}}</th>
                            <th>{{__('Date FOR')}}</th>
                            <th>{{__('Start Date')}}</th>
                            <th>{{__('End Date')}}</th>
                            <th>{{__('Signature')}}</th>
                            <th>{{__('Total Data Insert/Updated')}}</th>
                            <th>{{__('Table Name')}}</th>
                            <th width="50px">{{__('Description')}}</th>
                            <th>{{__('Download')}}</th>
                            
                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($crons) && !empty($crons))
                        @foreach ($crons as $cron)
                            <tr>
                                <td {{($cron->status=='Failure')?"class=text-red":''}}>{{isset($cron->status)?$cron->status:''}}</td>
                                <td>{{isset($cron->date)?explode(" ",$cron->date)[0]:''}}</td>
                                <td>{{isset($cron->cron_start_date)?$cron->cron_start_date:''}}</td>
                                <td>{{isset($cron->cron_end_date)?$cron->cron_end_date:''}}</td>
                                <td>{{isset($cron->signature)?$cron->signature:''}}</td>

                                <td>{{isset($cron->total_in_up)?$cron->total_in_up:''}}</td>
                                <td>{{isset($cron->table_name)?$cron->table_name:''}}</td>
                                <td style="white-space: normal;">{{isset($cron->description)?$cron->description:''}}</td>
                                @php
                                    $fileNameArr = explode(" ",$cron->signature);
                                    $fileName = trim($fileNameArr[0]);
                                    $date = isset($cron->cron_start_date)?explode(" ",$cron->cron_start_date)[0]:'';
                                    $folderName = date('Ymd',strtotime($date));
                                @endphp
                                <td><a href="{{route('logfile.downloadCron',[$fileName,$folderName])}}"><i class="fa fa-download" aria-hidden="true" ></i></a></td>
                                

                            </tr>
                        @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
