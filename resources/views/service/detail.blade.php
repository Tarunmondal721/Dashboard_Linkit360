@extends('layouts.admin')

@section('title')
	{{ __('Detail Service') }}
@endsection

@section('content')

<!-- tab section -->
<div class="card shadow-sm mt-0">
    <div class="card-body">
        <div class="tools">
            <div class="row">
                <div class="col-md-12">
                    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                        <li class="nav-item pr-lg-4">
                            <a class="nav-link bg-gray-400  active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">Progress</a>
                        </li>
                        <li class="nav-item pr-lg-4">
                            <a class="nav-link bg-gray-400 " id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="false">Update Progress</a>
                        </li>
                        <li class="nav-item pr-lg-4">
                            <a class="nav-link bg-gray-400 " id="pills-contact-tab" data-toggle="pill" href="#pills-contact" role="tab" aria-controls="pills-contact" aria-selected="false">Service Detail</a>
                        </li>
                        <li class="nav-item pr-4">
                            <a class="nav-link bg-gray-400 " id="pills-contact-tab" data-toggle="pill" href="#sms-log" role="tab" aria-controls="pills-contact" aria-selected="false">Update Service</a>
                        </li>
                    </ul>

                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                            <div class="card shadow-sm mt-0">
                                <div class="card-body">
                                    <div class="tools">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                                    <li class="nav-item pr-lg-4">
                                                        <a class="nav-link bg-gray-400  active" id="pills-pmo-tab" data-toggle="pill" href="#pills-pmo" role="tab" aria-controls="pills-pmo" aria-selected="true">PMO</a>
                                                    </li>
                                                    <li class="nav-item pr-lg-4">
                                                        <a class="nav-link bg-gray-400 " id="pills-operational-tab" data-toggle="pill" href="#pills-operational" role="tab" aria-controls="pills-operational" aria-selected="false">Operational</a>
                                                    </li>
                                                    <li class="nav-item pr-lg-4">
                                                        <a class="nav-link bg-gray-400 " id="pills-business-tab" data-toggle="pill" href="#pills-business" role="tab" aria-controls="pills-business" aria-selected="false">Business User</a>
                                                    </li>
                                                    <li class="nav-item pr-4">
                                                        <a class="nav-link bg-gray-400 " id="pills-finance-tab" data-toggle="pill" href="#pills-finance" role="tab" aria-controls="pills-finance" aria-selected="false">Finance</a>
                                                    </li>
                                                </ul>

                                                <div class="tab-content" id="pills-tabContent">
                                                    <div class="tab-pane fade show active" id="pills-pmo" role="tabpanel" aria-labelledby="pills-pmo-tab">
                                                        @include('service.progressReport')
                                                    </div>
                                                    <div class="tab-pane fade" id="pills-operational" role="tabpanel" aria-labelledby="pills-operational-tab">
                                                        No data yet
                                                    </div>
                                                    <div class="tab-pane fade" id="pills-business" role="tabpanel" aria-labelledby="pills-business-tab">
                                                        No data yet
                                                    </div>
                                                    <div class="tab-pane fade" id="pills-finance" role="tabpanel" aria-labelledby="pills-finance-tab">
                                                        No data yet
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                            @include('service.progressCreate')
                        </div>

                        <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">
                            @include('service.detailService')
                        </div>

                        <div class="tab-pane fade" id="sms-log" role="tabpanel" aria-labelledby="pills-contact-tab">
                            @include('service.edit')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- tab section -->
@endsection
