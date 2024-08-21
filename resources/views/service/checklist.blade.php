@extends('layouts.admin')

@section('title')
    {{ __('Service Checklist') }}
@endsection

@section('content')
    <div class="page-content">
        <div class="page-title">
            <div class="row justify-content-between align-items-center">
                <div
                    class="col-xl-4 col-lg-4 col-md-4 d-flex align-items-center justify-content-between justify-content-md-start mb-3 mb-md-0">
                    <div class="d-inline-block">
                    </div>
                </div>
                <div
                    class="col-xl-8 col-lg-8 col-md-8 d-flex align-items-center justify-content-between justify-content-md-end">
                </div>
            </div>
        </div>
        <div class="main-content position-relative">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="d-flex align-items-center my-3">
                            <span class="badge badge-with-flag badge-secondary px-2 bg-primary ">
                                <img src="{{ asset('/flags/' . $service['flag']['flag']) }}" width="30" height="20">
                                Summary Check List | Last Update: {{ $service['updated_at'] }} Asia / Jakarta
                            </span>
                            <span class="flex-fill ml-2 bg-primary w-100 font-weight-bold" style="height: 1px;"></span>
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
                                                <td><b>Company</b></td>
                                                <td>{{ $service['company']['name'] }}</td>
                                            </tr>
                                            <tr>
                                                <td><b>Operator</b></td>
                                                <td>{{ $service['operator_name'] }}</td>
                                            </tr>
                                            <tr>
                                                <td><b>Service</b></td>
                                                <td>{{ $service['service_name'] }}</td>
                                            </tr>
                                            <tr>
                                                <td><b>Account Manager</b></td>
                                                <td>{{ isset($service->accountManager) ? $service->accountManager->name : '' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><b>PMO</b></td>
                                                <td>{{ isset($service->pmouser) ? $service->pmouser->name : '' }}</td>
                                            </tr>
                                            <tr>
                                                <td><b>Developer</b></td>
                                                <td>{{ isset($service->backenduser) ? $service->backenduser->name : '' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><b>Lead Infra</b></td>
                                                <td>{{ isset($service->infra) ? $service->infra->name : '' }}</td>
                                            </tr>
                                            <tr>
                                                <td><b>CS Leader</b></td>
                                                <td>{{ isset($service->csteam) ? $service->csteam->name : '' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <table class="table tableBg">
                                        <tbody>
                                            <tr>
                                                <td><b>Total Check List</b></td>
                                                <td>39</td>
                                            </tr>
                                            <tr>
                                                <td><b>Total Complete</b></td>
                                                <td id="total-checked">0</td>
                                            </tr>
                                            <tr>
                                                <td><b>Total Un check List</b></td>
                                                <td id="total-unchecked">0</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div id="container"></div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-12">
                                    <ul class="nav nav-pills tabs-custom mb-4" id="pills-tab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="pills-pmo-tab" data-bs-toggle="pill"
                                                data-bs-target="#pills-pmo" type="button" role="tab"
                                                aria-controls="pills-pmo" aria-selected="true">PMO</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="pills-infra-tab" data-bs-toggle="pill"
                                                data-bs-target="#pills-infra" type="button" role="tab"
                                                aria-controls="pills-infra" aria-selected="false">Infra
                                                Team</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="pills-business-tab" data-bs-toggle="pill"
                                                data-bs-target="#pills-business" type="button" role="tab"
                                                aria-controls="pills-business" aria-selected="false">Business Team</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="pills-cs-tab" data-bs-toggle="pill"
                                                data-bs-target="#pills-cs" type="button" role="tab"
                                                aria-controls="pills-cs" aria-selected="false">CS Team</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="pills-finance-tab" data-bs-toggle="pill"
                                                data-bs-target="#pills-finance" type="button" role="tab"
                                                aria-controls="pills-finance" aria-selected="false">Finance</button>
                                        </li>
                                    </ul>
                                    <div class="tab-content" id="pills-tabContent">
                                        <div class="tab-pane fade show active" id="pills-pmo" role="tabpanel"
                                            aria-labelledby="pills-pmo-tab" tabindex="0">

                                            <div class="row" style="gap: 20px;">
                                                <div class="col-md">
                                                    <div class="detail-data mb-3">
                                                        <h6 class="label-data">Reporting Data</h6>
                                                        <span class="value-data" id="checklist-count-1">0 of 4
                                                            Checklist</span>
                                                    </div>

                                                    <div class="detail-data">
                                                        <h6 class="label-data">CS Tools</h6>
                                                        <span class="value-data" id="checklist-count-8">0 of 2
                                                            Checklist</span>
                                                    </div>
                                                </div>
                                                <div class="col-md">
                                                    <div class="detail-data mb-3">
                                                        <h6 class="label-data">Push Data Campaign</h6>
                                                        <span class="value-data" id="checklist-count-4">0 of 3
                                                            Checklist</span>
                                                    </div>

                                                    <div class="detail-data">
                                                        <h6 class="label-data">Monitoring</h6>
                                                        <span class="value-data" id="checklist-count-3">0 of 2
                                                            Checklist</span>
                                                    </div>
                                                </div>
                                                <div class="col-md">
                                                    <div class="detail-data mb-3">
                                                        <h6 class="label-data">Postback Ratio Setting</h6>
                                                        <span class="value-data" id="checklist-count-7">0 of 2
                                                            Checklist</span>
                                                    </div>

                                                    <div class="detail-data">
                                                        <h6 class="label-data">Whitelist * VIP Number</h6>
                                                        <span class="value-data" id="checklist-count-6">0 of 2
                                                            Checklist</span>
                                                    </div>
                                                </div>
                                                <div class="col-md">
                                                    <div class="detail-data mb-3">
                                                        <h6 class="label-data">Setting Disable / Enabale
                                                            Campaign</h6>
                                                        <span class="value-data" id="checklist-count-2">0 of 2
                                                            Checklist</span>
                                                    </div>
                                                </div>
                                                <div class="col-md">
                                                    <div class="detail-data mb-3">
                                                        <h6 class="label-data">Arpu Tools</h6>
                                                        <span class="value-data" id="checklist-count-5">0 of 2
                                                            Checklist</span>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="tab-pane fade" id="pills-infra" role="tabpanel"
                                            aria-labelledby="pills-infra-tab" tabindex="0">

                                            <div class="row" style="gap: 20px;">
                                                <div class="col-md-3">
                                                    <div class="detail-data mb-3">
                                                        <h6 class="label-data">Server Monitoring</h6>
                                                        <span class="value-data" id="checklist-count-9">0 of 4
                                                            Checklist</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="detail-data">
                                                        <h6 class="label-data">Security</h6>
                                                        <span class="value-data" id="checklist-count-10">0 of 2
                                                            Checklist</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="detail-data">
                                                        <h6 class="label-data">Testing</h6>
                                                        <span class="value-data" id="checklist-count-11">0 of 1
                                                            Checklist</span>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="tab-pane fade" id="pills-business" role="tabpanel"
                                            aria-labelledby="pills-business-tab" tabindex="0">

                                            <div class="row" style="gap: 20px;">
                                                <div class="col-md-3">
                                                    <div class="detail-data mb-3">
                                                        <h6 class="label-data">File</h6>
                                                        <span class="value-data" id="checklist-count-12">0 of 4
                                                            Checklist</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="detail-data">
                                                        <h6 class="label-data">Launching Service</h6>
                                                        <span class="value-data" id="checklist-count-13">0 of 1
                                                            Checklist</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="detail-data">
                                                        <h6 class="label-data">Reconciliation</h6>
                                                        <span class="value-data" id="checklist-count-14">0 of 1
                                                            Checklist</span>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="tab-pane fade" id="pills-cs" role="tabpanel"
                                            aria-labelledby="pills-cs-tab" tabindex="0">

                                            <div class="row" style="gap: 20px;">
                                                <div class="col-md-3">
                                                    <div class="detail-data mb-3">
                                                        <h6 class="label-data">File</h6>
                                                        <span class="value-data" id="checklist-count-15">0 of 3
                                                            Checklist</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="detail-data">
                                                        <h6 class="label-data">Report Tools</h6>
                                                        <span class="value-data" id="checklist-count-16">0 of 1
                                                            Checklist</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="detail-data">
                                                        <h6 class="label-data">CS Tools</h6>
                                                        <span class="value-data" id="checklist-count-17">0 of 1
                                                            Checklist</span>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="tab-pane fade" id="pills-finance" role="tabpanel"
                                            aria-labelledby="pills-finance-tab" tabindex="0">

                                            <div class="row" style="gap: 20px;">
                                                {{-- <div class="col-md-3">
                                                    <div class="detail-data mb-3">
                                                        <h6 class="label-data">File</h6>
                                                        <span class="value-data" id="checklist-count-18">0 of 1
                                                            Checklist</span>
                                                    </div>
                                                </div> --}}
                                                <div class="col-md-3">
                                                    <div class="detail-data">
                                                        <h6 class="label-data">Reconcilitation</h6>
                                                        <span class="value-data" id="checklist-count-18">0 of 2
                                                            Checklist</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="detail-data">
                                                        <h6 class="label-data">Payment</h6>
                                                        <span class="value-data" id="checklist-count-19">0 of 1
                                                            Checklist</span>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card shadow-sm mt-3">
                    <div class="card-body">
                        <div class="tools">
                            <ul class="nav nav-pills tabs-custom mb-4" id="pills-tab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="pills-check-pmo-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-check-pmo" type="button" role="tab"
                                        aria-controls="pills-check-pmo" aria-selected="true">Check
                                        List PMO</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pills-check-infra-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-check-infra" type="button" role="tab"
                                        aria-controls="pills-check-infra" aria-selected="false">Check List Infra
                                        Team</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pills-check-business-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-check-business" type="button" role="tab"
                                        aria-controls="pills-check-business" aria-selected="false">Check List
                                        Business
                                        Team</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pills-check-cs-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-check-cs" type="button" role="tab"
                                        aria-controls="pills-check-cs" aria-selected="false">Check List CS
                                        Team</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pills-check-finance-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-check-finance" type="button" role="tab"
                                        aria-controls="pills-check-finance" aria-selected="false">Check List
                                        Finance</button>
                                </li>
                            </ul>


                            {{-- @foreach ($checklists as $checklist) --}}

                            <!-- Check List Pmo Start Here -->
                            <form class="service-checklist" action="{{ route('checklist.update') }}"
                                 method="POST" onsubmit="return ChecklistSubmit()">
                                @csrf
                                <input type="hidden" value="{{ $service['id'] }}" name="service_id">
                                <div class="tab-content" id="pills-tabContent">

                                    <div class="tab-pane fade show active" id="pills-check-pmo" role="tabpanel"
                                        aria-labelledby="pills-check-pmo-tab" tabindex="0">
                                        <div class="form-check">
                                            <input class="form-check-input check-all" type="checkbox" data-group-id="5"
                                                id="checkAll-1" name="check_all_pmo">

                                            <label class="form-check-label" for="checkAll">
                                                Check List All
                                            </label>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="data-checklist" id="data-checklist-1">
                                                    <h6 class="label-data">
                                                        Reporting Data
                                                        <span class="text-danger">*</span>
                                                    </h6>
                                                    <div class="form-check">
                                                        <input class="form-check-input pmo-checkbox"
                                                            type="checkbox"data-group-id="5" required
                                                            id="check_pmo_report_fery" name="pmo_1" value="yes"
                                                            {{ isset($checklist->pmo_1) ? ($checklist->pmo_1 == 'yes' ? 'checked' : '') : '' }}>
                                                        <label class="form-check-label" for="check_pmo_report_fery">
                                                            Sync Data to Fery <span class="text-danger">*</span>
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input pmo-checkbox" type="checkbox"
                                                            data-group-id="5"value="yes" required
                                                            id="check_pmo_report_dashboard" name="pmo_2"
                                                            {{ isset($checklist->pmo_2) ? ($checklist->pmo_2 == 'yes' ? 'checked' : '') : '' }}>
                                                        <label class="form-check-label" for="check_pmo_report_dashboard">
                                                            Sync Data to Dashboard LinkIT <span
                                                                class="text-danger">*</span>
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input pmo-checkbox" type="checkbox"
                                                            data-group-id="5" required id="check_pmo_report_revenue"
                                                            name="pmo_3" value="yes"
                                                            {{ isset($checklist->pmo_3) ? ($checklist->pmo_3 == 'yes' ? 'checked' : '') : '' }}>
                                                        <label class="form-check-label" for="check_pmo_report_revenue">
                                                            Setting Revenue Share <span class="text-danger">*</span>
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input pmo-checkbox" type="checkbox"
                                                            data-group-id="5" required id="check_pmo_report_ads"
                                                            name="pmo_4" value="yes"
                                                            {{ isset($checklist->pmo_4) ? ($checklist->pmo_4 == 'yes' ? 'checked' : '') : '' }}>
                                                        <label class="form-check-label" for="check_pmo_report_ads">
                                                            Sync Data Ads Spending <span class="text-danger">*</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="data-checklist" id="data-checklist-2">
                                                    <h6 class="label-data">
                                                        Setting Disable / Enbale Campaign
                                                        <span class="text-danger">*</span>
                                                    </h6>
                                                    <span class="gu-hide" style="color: red;"
                                                        id="error_pmo">{{ __('*Please select campaign') }}</span>
                                                    <div class="form-check">
                                                        <input class="form-check-input pmo-checkbox" type="checkbox"
                                                            data-group-id="5" id="check_pmo_setting_cpa" name="pmo_5"
                                                            value="yes"
                                                            {{ isset($checklist->pmo_5) ? ($checklist->pmo_5 == 'yes' ? 'checked' : '') : '' }}>
                                                        <label class="form-check-label" for="check_pmo_setting_cpa">
                                                            CPA Campaign
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input pmo-checkbox" type="checkbox"
                                                            data-group-id="5" id="check_pmo_setting_api" name="pmo_6"
                                                            value="yes"
                                                            {{ isset($checklist->pmo_6) ? ($checklist->pmo_6 == 'yes' ? 'checked' : '') : '' }}>
                                                        <label class="form-check-label" for="check_pmo_setting_api">
                                                            API Campaign
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="data-checklist" id="data-checklist-3">
                                                    <h6 class="label-data">
                                                        Monitoring
                                                    </h6>
                                                    <div class="form-check">
                                                        <input class="form-check-input pmo-checkbox" type="checkbox"
                                                            data-group-id="5" id="check_pmo_monitoring_double"
                                                            name="pmo_7" value="yes"
                                                            {{ isset($checklist->pmo_7) ? ($checklist->pmo_7 == 'yes' ? 'checked' : '') : '' }}>
                                                        <label class="form-check-label" for="check_pmo_monitoring_double">
                                                            Double Charging
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input pmo-checkbox" type="checkbox"
                                                            data-group-id="5" id="check_pmo_monitoring_safety"
                                                            name="pmo_8" value="yes"
                                                            {{ isset($checklist->pmo_8) ? ($checklist->pmo_8 == 'yes' ? 'checked' : '') : '' }}>
                                                        <label class="form-check-label" for="check_pmo_monitoring_safety">
                                                            Safety Monitoring (ex: alert)
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="data-checklist" id="data-checklist-4">
                                                    <h6 class="label-data">
                                                        Push Data Campaign
                                                        <span class="text-danger">*</span>
                                                    </h6>
                                                    <span class="gu-hide" style="color: red;"
                                                    id="error_push">{{ __('*Please select push data') }}</span>
                                                    <div class="form-check">
                                                        <input class="form-check-input pmo-checkbox" type="checkbox"
                                                            data-group-id="5" id="check_pmo_push_sms" name="pmo_9"
                                                            value="yes"
                                                            {{ isset($checklist->pmo_9) ? ($checklist->pmo_9 == 'yes' ? 'checked' : '') : '' }}>
                                                        <label class="form-check-label" for="check_pmo_push_sms">
                                                            Campaign SMS
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input pmo-checkbox" type="checkbox"
                                                            data-group-id="5" id="check_pmo_push_api" name="pmo_10"
                                                            value="yes"
                                                            {{ isset($checklist->pmo_10) ? ($checklist->pmo_10 == 'yes' ? 'checked' : '') : '' }}>
                                                        <label class="form-check-label" for="check_pmo_push_api">
                                                            Campaign API
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input pmo-checkbox" type="checkbox"
                                                            data-group-id="5" id="check_pmo_push_cpa" name="pmo_11"
                                                            value="yes"
                                                            {{ isset($checklist->pmo_11) ? ($checklist->pmo_11 == 'yes' ? 'checked' : '') : '' }}>
                                                        <label class="form-check-label" for="check_pmo_push_cpa">
                                                            S2S / CPA / WAP
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="data-checklist" id="data-checklist-5">
                                                    <h6 class="label-data">
                                                        Arpu Tools
                                                        <span class="text-danger">*</span>
                                                    </h6>
                                                    <span class="gu-hide" style="color: red;"
                                                    id="error_arpu">{{ __('*Please select arpu tools') }}</span>
                                                    <div class="form-check">
                                                        <input class="form-check-input pmo-checkbox" type="checkbox"
                                                            data-group-id="5" id="check_pmo_arpu_sent" name="pmo_12"
                                                            value="yes"
                                                            {{ isset($checklist->pmo_12) ? ($checklist->pmo_12 == 'yes' ? 'checked' : '') : '' }}>

                                                        <label class="form-check-label" for="check_pmo_arpu_sent">
                                                            Sent Data Arpu tools
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input pmo-checkbox" type="checkbox"
                                                            data-group-id="5" id="check_pmo_arpu_already" name="pmo_13"
                                                            value="yes"
                                                            {{ isset($checklist->pmo_13) ? ($checklist->pmo_13 == 'yes' ? 'checked' : '') : '' }}>
                                                        <label class="form-check-label" for="check_pmo_arpu_already">
                                                            Arpu Tools already set in Dashboard LinkIT
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="data-checklist" id="data-checklist-6">
                                                    <h6 class="label-data">
                                                        Whitelist & VIP Number
                                                    </h6>
                                                    <div class="form-check">
                                                        <input class="form-check-input pmo-checkbox" type="checkbox"
                                                            data-group-id="5" id="check_pmo_white_vip" name="pmo_14"
                                                            value="yes"
                                                            {{ isset($checklist->pmo_14) ? ($checklist->pmo_14 == 'yes' ? 'checked' : '') : '' }}>
                                                        <label class="form-check-label" for="check_pmo_white_vip">
                                                            Has VIP Number
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input pmo-checkbox" type="checkbox"
                                                            data-group-id="5" id="check_pmo_white_whitelist"
                                                            name="pmo_15" value="yes"
                                                            {{ isset($checklist->pmo_15) ? ($checklist->pmo_15 == 'yes' ? 'checked' : '') : '' }}>
                                                        <label class="form-check-label" for="check_pmo_white_whitelist">
                                                            Has Whitelist number employee
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="data-checklist" id="data-checklist-7">
                                                    <h6 class="label-data">
                                                        Postback Ratio Setting
                                                        <span class="text-danger">*</span>
                                                    </h6>
                                                    <span class="gu-hide" style="color: red;"
                                                    id="error_postback">{{ __('*Please select postback') }}</span>
                                                    <div class="form-check">
                                                        <input class="form-check-input pmo-checkbox" type="checkbox"
                                                            data-group-id="5" id="check_pmo_ratio_cpa" name="pmo_16"
                                                            value="yes"
                                                            {{ isset($checklist->pmo_16) ? ($checklist->pmo_16 == 'yes' ? 'checked' : '') : '' }}>
                                                        <label class="form-check-label" for="check_pmo_ratio_cpa">
                                                            Setting Postback CPA
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input pmo-checkbox" type="checkbox"
                                                            data-group-id="5" id="check_pmo_ratio_api" name="pmo_17"
                                                            value="yes"
                                                            {{ isset($checklist->pmo_17) ? ($checklist->pmo_17 == 'yes' ? 'checked' : '') : '' }}>
                                                        <label class="form-check-label" for="check_pmo_ratio_api">
                                                            Setting Postback API
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="data-checklist" id="data-checklist-8">
                                                    <h6 class="label-data">
                                                        CS Tools
                                                        <span class="text-danger">*</span>
                                                    </h6>
                                                    <span class="gu-hide" style="color: red;"
                                                    id="error_cs">{{ __('*Please select cs tools already set') }}</span>
                                                    <div class="form-check">
                                                        <input class="form-check-input pmo-checkbox" type="checkbox"
                                                            data-group-id="5" id="check_pmo_cs_sent" name="pmo_18"
                                                            value="yes"
                                                            {{ isset($checklist->pmo_18) ? ($checklist->pmo_18 == 'yes' ? 'checked' : '') : '' }}>
                                                        <label class="form-check-label" for="check_pmo_cs_sent">
                                                            Sent Data CS Tools
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input pmo-checkbox" data-group-id="5"
                                                            type="checkbox" value="yes" id="check_pmo_cs_already"
                                                            name="pmo_19"value="yes"
                                                            {{ isset($checklist->pmo_19) ? ($checklist->pmo_19 == 'yes' ? 'checked' : '') : '' }}>

                                                        <label class="form-check-label" for="check_pmo_cs_already">
                                                            CS Tools already set in Dashboard LinkIT
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex mt-3" style="justify-content: end;">
                                            <button type="submit" class="btn btn-primary"> Update </button>
                                        </div>

                                    </div>
                                </div>
                            </form>
                            <!-- Check List Pmo End Here -->

                            <!-- Check List Infra Team Start Here -->
                            <form class="service-checklist" action="{{ route('checklist.update') }}" method="POST"
                                onsubmit="return ChecklistSubmit1()">
                                @csrf
                                <input type="hidden" value="{{ $service['id'] }}" name="service_id">
                                <div class="tab-content">
                                    <div class="tab-pane fade" id="pills-check-infra" role="tabpanel"
                                        aria-labelledby="pills-check-infra-tab" tabindex="0">
                                        <div class="form-check">
                                            <input class="form-check-input check-all" type="checkbox" data-group-id="4"
                                                id="checkAll-2" name="check_all_pmo">
                                            <label class="form-check-label" for="checkAll">
                                                Check List All
                                            </label>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="data-checklist" id="data-checklist-9">
                                                    <h6 class="label-data">
                                                        Server Monitoring
                                                        <span class="text-danger">*</span>
                                                    </h6>
                                                    <span class="gu-hide" style="color: red;"
                                                    id="error_server">{{ __('*Please select server monitoring') }}</span>
                                                    <div class="form-check">
                                                        <input class="form-check-input infra-checkbox" data-group-id="4"
                                                            type="checkbox" value="yes"
                                                            id="check_infra_monitor_server" name="infra_1"
                                                            {{ isset($checklist->infra_1) ? ($checklist->infra_1 == 'yes' ? 'checked' : '') : '' }}>
                                                        <label class="form-check-label" for="check_infra_monitor_server">
                                                            Server has been input to 7x24
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input infra-checkbox" type="checkbox"
                                                            data-group-id="4" value="yes"
                                                            id="check_infra_monitor_monitoring" name="infra_2"
                                                            {{ isset($checklist->infra_2) ? ($checklist->infra_2 == 'yes' ? 'checked' : '') : '' }}>
                                                        <label class="form-check-label"
                                                            for="check_infra_monitor_monitoring">
                                                            App Monitoring
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input infra-checkbox" type="checkbox"
                                                            data-group-id="4" value="yes" id="check_infra_monitor_web"
                                                            name="infra_3"
                                                            {{ isset($checklist->infra_3) ? ($checklist->infra_3 == 'yes' ? 'checked' : '') : '' }}>
                                                        <label class="form-check-label" for="check_infra_monitor_web">
                                                            Web Portal Monitoring
                                                        </label>
                                                    </div>
                                                    {{-- <div class="form-check">
                                                            <input class="form-check-input infra-checkbox" type="checkbox"
                                                                data-group-id="4" value="yes" id="check_infra_monitor_ads"
                                                                name="infra_4"
                                                                {{ isset($checklist->infra_4) ? ($checklist->infra_4 == 'yes' ? 'checked' : '') : '' }}>
                                                            <label class="form-check-label" for="check_infra_monitor_ads">
                                                                Sync Data Ads Spending
                                                            </label>
                                                        </div> --}}
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="data-checklist" id="data-checklist-10">
                                                    <h6 class="label-data">
                                                        Security
                                                        <span class="text-danger">*</span>
                                                    </h6>
                                                    <span class="gu-hide" style="color: red;"
                                                    id="error_security">{{ __('*Please select security') }}</span>
                                                    <div class="form-check">
                                                        <input class="form-check-input infra-checkbox" type="checkbox"
                                                            data-group-id="4" value="yes"
                                                            id="check_infra_security_ssl" name="infra_5"
                                                            {{ isset($checklist->infra_5) ? ($checklist->infra_5 == 'yes' ? 'checked' : '') : '' }}>
                                                        <label class="form-check-label" for="check_infra_security_ssl">
                                                            Server SSL
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input infra-checkbox" type="checkbox"
                                                            data-group-id="4" value="yes"
                                                            id="check_infra_security_server" name="infra_6"
                                                            {{ isset($checklist->infra_6) ? ($checklist->infra_6 == 'yes' ? 'checked' : '') : '' }}>
                                                        <label class="form-check-label" for="check_infra_security_server">
                                                            Server has Security
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="data-checklist" id="data-checklist-11">
                                                    <h6 class="label-data">
                                                        Testing
                                                    </h6>
                                                    <div class="form-check">
                                                        <input class="form-check-input infra_checkbox" type="checkbox"
                                                            data-group-id="4" value="yes" id="check_infra_testing"
                                                            name="infra_7"
                                                            {{ isset($checklist->infra_7) ? ($checklist->infra_7 == 'yes' ? 'checked' : '') : '' }}>
                                                        <label class="form-check-label" for="check_infra_testing">
                                                            Stress Test
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex mt-3" style="justify-content: end;">
                                            <button type="submit" class="btn btn-primary"> Update </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <!-- Check List Infra Team End Here -->

                            <!-- Check List Business Start Here -->
                            <form class="service-checklist" action="{{ route('checklist.update') }}" method="POST"
                                onsubmit="return ChecklistSubmit2()">
                                @csrf
                                <input type="hidden" value="{{ $service['id'] }}" name="service_id">
                                <div class="tab-content">
                                    <div class="tab-pane fade" id="pills-check-business" role="tabpanel"
                                        aria-labelledby="pills-check-business-tab" tabindex="0">

                                        <div class="form-check">
                                            <input class="form-check-input check-all" data-group-id="3" type="checkbox"
                                                id="checkAll-3" name="check_all_pmo">
                                            <label class="form-check-label" for="checkAll">
                                                Check List All
                                            </label>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="data-checklist" id="data-checklist-12">
                                                    <h6 class="label-data">
                                                        File
                                                        <span class="text-danger">*</span>
                                                    </h6>
                                                    <span class="gu-hide" style="color: red;"
                                                id="error_file">{{ __('*Please select file') }}</span>
                                                    <div class="form-check">
                                                        <input class="form-check-input business-checkbox"
                                                            type="checkbox"data-group-id="3" value="yes"
                                                            id="check_business_file_response" name="business_1"
                                                            {{ isset($checklist->business_1) ? ($checklist->business_1 == 'yes' ? 'checked' : '') : '' }}>
                                                        <label class="form-check-label"
                                                            for="check_business_file_response">
                                                            Response Matrix / Escalation Matrix
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input business-checkbox" type="checkbox"
                                                            data-group-id="3" value="yes" id="check_business_file_pic"
                                                            name="business_2"
                                                            {{ isset($checklist->business_2) ? ($checklist->business_2 == 'yes' ? 'checked' : '') : '' }}>
                                                        <label class="form-check-label" for="check_business_file_pic">
                                                            PIC Connection
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input business-checkbox" type="checkbox"
                                                            data-group-id="3" value="yes"
                                                            id="check_business_file_local" name="business_3"
                                                            {{ isset($checklist->business_3) ? ($checklist->business_3 == 'yes' ? 'checked' : '') : '' }}>
                                                        <label class="form-check-label" for="check_business_file_local">
                                                            Has Local Support
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input business-checkbox" type="checkbox"
                                                            value="yes" id="check_business_file_contract" data-group-id="3"
                                                            name="business_4"
                                                            {{ isset($checklist->business_4) ? ($checklist->business_4 == 'yes' ? 'checked' : '') : '' }}>
                                                        <label class="form-check-label" for="check_business_file_contract">
                                                            Has file contract
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="data-checklist" id="data-checklist-13">
                                                    <h6 class="label-data">
                                                        Launching Service
                                                        <span class="text-danger">*</span>
                                                    </h6>
                                                    <span class="gu-hide" style="color: red;"
                                                    id="error_launch">{{ __('*Please select launching service') }}</span>
                                                    <div class="form-check">
                                                        <input class="form-check-input business-checkbox" type="checkbox"
                                                            data-group-id="3" value="yes"
                                                            id="check_business_launching" name="business_5"
                                                            {{ isset($checklist->business_5) ? ($checklist->business_5 == 'yes' ? 'checked' : '') : '' }}>
                                                        <label class="form-check-label" for="check_business_launching">
                                                            Has Inform launch new service
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="data-checklist" id="data-checklist-14">
                                                    <h6 class="label-data">
                                                        Reconcilitation
                                                        <span class="text-danger">*</span>
                                                    </h6>
                                                    <span class="gu-hide" style="color: red;"
                                                    id="error_recon">{{ __('*Please select reconcilitation') }}</span>
                                                    <div class="form-check">
                                                        <input class="form-check-input business-checkbox" type="checkbox"
                                                            data-group-id="3" value="yes"
                                                            id="check_business_reconcilitation" name="business_6"
                                                            {{ isset($checklist->business_6) ? ($checklist->business_6 == 'yes' ? 'checked' : '') : '' }}>
                                                        <label class="form-check-label"
                                                            for="check_business_reconcilitation">
                                                            Reconciliataion data 7 days after new service launching
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="d-flex mt-3" style="justify-content: end;">
                                            <button type="submit" class="btn btn-primary"> Update </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <!-- Check List BUsiness End Here -->

                            <!-- Check List Cs Team Start Here -->
                            <form class="service-checklist" action="{{ route('checklist.update') }}" method="POST"
                                >
                                @csrf
                                <input type="hidden" value="{{ $service['id'] }}" name="service_id">
                                <div class="tab-content">
                                    <div class="tab-pane fade" id="pills-check-cs" role="tabpanel"
                                        aria-labelledby="pills-check-cs-tab" tabindex="0">

                                        <div class="form-check">
                                            <input class="form-check-input check-all" type="checkbox" data-group-id="2"
                                                id="checkAll-4" name="check_all_pmo">
                                            <label class="form-check-label" for="checkAll">
                                                Check List All
                                            </label>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="data-checklist" id="data-checklist-15">
                                                    <h6 class="label-data">
                                                        File
                                                    </h6>
                                                    <div class="form-check">
                                                        <input class="form-check-input cs-checkbox" type="checkbox"
                                                            data-group-id="2" value="yes" id="check_cs_file_response"
                                                            name="cs_1" required
                                                            {{ isset($checklist->cs_1) ? ($checklist->cs_1 == 'yes' ? 'checked' : '') : '' }}>
                                                        <label class="form-check-label" for="check_cs_file_response">
                                                            Response Matrix / Escalation Matrix
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input cs-checkbox" type="checkbox"
                                                            data-group-id="2" value="yes" id="check_cs_file_faq"
                                                            name="cs_2"
                                                            {{ isset($checklist->cs_2) ? ($checklist->cs_2 == 'yes' ? 'checked' : '') : '' }}>
                                                        <label class="form-check-label" for="check_cs_file_faq">
                                                            FAQ
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input cs-checkbox" type="checkbox"
                                                            value="yes" data-group-id="2" id="check_cs_file_pb"
                                                            name="cs_3"
                                                            {{ isset($checklist->cs_3) ? ($checklist->cs_3 == 'yes' ? 'checked' : '') : '' }}>
                                                        <label class="form-check-label" for="check_cs_file_pb">
                                                            PB
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="data-checklist" id="data-checklist-16">
                                                    <h6 class="label-data">
                                                        Report Tools
                                                    </h6>
                                                    <div class="form-check">
                                                        <input class="form-check-input cs-checkbox" type="checkbox"
                                                            value="yes" data-group-id="2" id="check_cs_report"
                                                            name="cs_4" required
                                                            {{ isset($checklist->cs_4) ? ($checklist->cs_4 == 'yes' ? 'checked' : '') : '' }}>
                                                        <label class="form-check-label" for="check_cs_report">
                                                            Has connection to monitoring revenue
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="data-checklist" id="data-checklist-17">
                                                    <h6 class="label-data">
                                                        CS Tools
                                                    </h6>
                                                    <div class="form-check">
                                                        <input class="form-check-input cs-checkbox" type="checkbox"
                                                            value="yes" data-group-id="2" id="check_cs_tools"
                                                            name="cs_5" required
                                                            {{ isset($checklist->cs_5) ? ($checklist->cs_5 == 'yes' ? 'checked' : '') : '' }}>
                                                        <label class="form-check-label" for="check_cs_tools">
                                                            Has Access to CS Tools
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="d-flex mt-3" style="justify-content: end;">
                                            <button type="submit" class="btn btn-primary"> Update </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <!-- Check List Cs Team End Here -->

                            <!-- Check List Finance Start Here -->
                            <form class="service-checklist" action="{{ route('checklist.update') }}" method="POST"
                                onsubmit="return ChecklistSubmit3()">
                                @csrf
                                <input type="hidden" value="{{ $service['id'] }}" name="service_id">
                                <div class="tab-content">
                                    <div class="tab-pane fade" id="pills-check-finance" role="tabpanel"
                                        aria-labelledby="pills-check-finance-tab" tabindex="0">
                                        <div class="form-check">
                                            <input class="form-check-input check-all" type="checkbox" id="checkAll-5"
                                                data-group-id="1" name="check_all_pmo">
                                            <label class="form-check-label" for="checkAll-5">
                                                Check List All
                                            </label>
                                        </div>
                                        <div class="row">
                                            {{-- <div class="col-md-4">
                                                <div class="data-checklist" id="data-checklist-18">
                                                    <h6 class="label-data">
                                                        File
                                                        <span class="text-danger">*</span>
                                                    </h6>
                                                    <div class="form-check">
                                                        <input class="form-check-input finance-checkbox" type="checkbox"
                                                            value="yes" id="check_finance_file" data-group-id="1"
                                                            name="finance_1" required
                                                            {{ isset($checklist->finance_1) ? ($checklist->finance_1 == 'yes' ? 'checked' : '') : '' }}>
                                                        <label class="form-check-label" for="check_finance_file">
                                                            Has file contract
                                                        </label>
                                                    </div>
                                                </div>
                                            </div> --}}
                                            <div class="col-md-4">
                                                <div class="data-checklist" id="data-checklist-18">
                                                    <h6 class="label-data">
                                                        Reconciliation
                                                        <span class="text-danger">*</span>
                                                    </h6>
                                                    <span class="gu-hide" style="color: red;"
                                                    id="error_reconcile">{{ __('*Please select reconciliation') }}</span>
                                                    <div class="form-check">
                                                        <input class="form-check-input finance-checkbox" type="checkbox"
                                                            value="yes" data-group-id="1"
                                                            id="check_finance_reconcilitation_newserver" name="finance_2"
                                                            {{ isset($checklist->finance_2) ? ($checklist->finance_2 == 'yes' ? 'checked' : '') : '' }}>
                                                        <label class="form-check-label"
                                                            for="check_finance_reconcilitation_newserver">
                                                            Reconciliation 7 Days after new server launching
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input finance-checkbox" type="checkbox"
                                                            value="yes" data-group-id="1"
                                                            id="check_finance_reconcilitation_already" name="finance_3"
                                                            {{ isset($checklist->finance_3) ? ($checklist->finance_3 == 'yes' ? 'checked' : '') : '' }}>
                                                        <label class="form-check-label"
                                                            for="check_finance_reconcilitation_already">
                                                            Already reconcile data
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="data-checklist" id="data-checklist-19">
                                                    <h6 class="label-data">
                                                        Payment
                                                        <span class="text-danger">*</span>
                                                    </h6>
                                                    <span class="gu-hide" style="color: red;"
                                                    id="error_payment">{{ __('*Please select payment') }}</span>
                                                    <div class="form-check">
                                                        <input class="form-check-input finance-checkbox" type="checkbox"
                                                            value="yes" data-group-id="1" id="check_finance_payment"
                                                            name="finance_4"
                                                            {{ isset($checklist->finance_4) ? ($checklist->finance_4 == 'yes' ? 'checked' : '') : '' }}>
                                                        <label class="form-check-label" for="check_finance_payment">
                                                            Already sent Invoice
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex mt-3" style="justify-content: end;">
                                            <button type="submit" class="btn btn-primary"> Update </button>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <!-- Check List Finance End Here -->

                            {{-- @endforeach --}}
                            {{-- <div class="d-flex mt-3" style="justify-content: end;">
                                    <button type="submit" class="btn btn-primary"> Update </button>
                                </div> --}}





                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        /* Pmo Validation Check Start Here */
        function ChecklistSubmit() {

            let pmo_5 = $("input[name='pmo_5']:checked").val();
            let pmo_6 = $("input[name='pmo_6']:checked").val();
            let pmo_9 = $("input[name='pmo_9']:checked").val();
            let pmo_10 = $("input[name='pmo_10']:checked").val();
            let pmo_11 = $("input[name='pmo_11']:checked").val();
            let pmo_12 = $("input[name='pmo_12']:checked").val();
            let pmo_13 = $("input[name='pmo_13']:checked").val();
            let pmo_16 = $("input[name='pmo_16']:checked").val();
            let pmo_17 = $("input[name='pmo_17']:checked").val();
            let pmo_18 = $("input[name='pmo_18']:checked").val();
            let pmo_19 = $("input[name='pmo_19']:checked").val();

            console.log(pmo_5);
            if (!pmo_5 && !pmo_6) {
                $("#error_pmo").removeClass("gu-hide");
                return false;
            } else {
                $("#error_pmo").addClass("gu-hide");
            }

            if (!pmo_9 && !pmo_10 && !pmo_11) {
                $("#error_push").removeClass("gu-hide");
                return false;
            } else {
                $("#error_push").addClass("gu-hide");
            }

            if (!pmo_12 || !pmo_13) {
                $("#error_arpu").removeClass("gu-hide");
                return false;
            } else {
                $("#error_arpu").addClass("gu-hide");
            }

            if (!pmo_16 && !pmo_17) {
                $("#error_postback").removeClass("gu-hide");
                return false;
            } else {
                $("#error_postback").addClass("gu-hide");
            }

            if (!pmo_19) {
                $("#error_cs").removeClass("gu-hide");
                return false;
            } else {
                $("#error_cs").addClass("gu-hide");
            }


        }
        /* Pmo Validation Check Start Here */

        /* Infra Validation check Start Here */
        function ChecklistSubmit1(){

             let infra_1 = $("input[name='infra_1']:checked").val();
            let infra_2 = $("input[name='infra_2]:checked").val();
            let infra_3 = $("input[name='infra_3']:checked").val();
            let infra_5 = $("input[name='infra_5']:checked").val();
            let infra_6 = $("input[name='infra_6']:checked").val();


            if (!infra_1 && !infra_2 && !infra_3) {
                $("#error_server").removeClass("gu-hide");
                return false;
            } else {
                $("#error_server").addClass("gu-hide");
            }

            if (!infra_5 && !infra_6) {
                $("#error_security").removeClass("gu-hide");
                return false;
            } else {
                $("#error_security").addClass("gu-hide");
            }
        }
        /* Infra Validation check end Here */

        /* Business Validation check Start Here */
        function ChecklistSubmit2() {


            let business_1 = $("input[name='business_1']:checked").val();
            let business_2 = $("input[name='business_2]:checked").val();
            let business_3 = $("input[name='business_3']:checked").val();
            let business_4 = $("input[name='business_4']:checked").val();
            let business_5 = $("input[name='business_5']:checked").val();
            let business_6 = $("input[name='business_6']:checked").val();




            if (!business_1 && !business_2 && !business_3 && !business_4) {
                $("#error_file").removeClass("gu-hide");
                return false;
            } else {
                $("#error_file").addClass("gu-hide");
            }
            if (!business_5) {
                $("#error_launch").removeClass("gu-hide");
                return false;
            } else {
                $("#error_launch").addClass("gu-hide");
            }
            if (!business_6) {
                $("#error_recon").removeClass("gu-hide");
                return false;
            } else {
                $("#error_recon").addClass("gu-hide");
            }
        }
        /* Business Validation check End Here */

        function ChecklistSubmit3()
        {
            let finance_2 = $("input[name=finance_2]:checked").val();
            let finance_3 = $("input[name=finance_3]:checked").val();
            let finance_4 = $("input[name=finance_4]:checked").val();

            console.log(finance_2);
            if(!finance_2 && !finance_3){
                $("#error_reconcile").removeClass("gu-hide");
                return false;
            }else{
                $("#error_reconcile").addClass("gu-hide");

            }
            if(!finance_4){
                $("#error_payment").removeClass("gu-hide");
                return false;
            }else{
                $("#error_payment").addClass("gu-hide");

            }
        }

        function updateChecklistCount(groupId) {
            const checklist = document.getElementById(`data-checklist-${groupId}`);
            const checkboxes = checklist.querySelectorAll('.form-check-input');
            const totalCheckboxes = checkboxes.length;
            let checkedCount = 0;

            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    checkedCount++;
                }
            });

            document.getElementById(`checklist-count-${groupId}`).innerText =
                `${checkedCount} of ${totalCheckboxes} Checklist`;
        }
        document.querySelectorAll('.data-checklist').forEach((checklist, index) => {
            const groupId = index + 1;
            const checkboxes = checklist.querySelectorAll('.form-check-input');
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', () => updateChecklistCount(groupId));
            });
            updateChecklistCount(groupId);
        });

        $(document).ready(function() {
            // Check all checkboxes in the group when the check all checkbox is clicked
            $('.check-all').click(function() {
                const GroupId = $(this).data('group-id');
                $(`.finance-checkbox[data-group-id="${GroupId}"]`).prop('checked', this.checked);
                $(`.cs-checkbox[data-group-id="${GroupId}"]`).prop('checked', this.checked);
                $(`.business-checkbox[data-group-id="${GroupId}"]`).prop('checked', this.checked);
                $(`.infra-checkbox[data-group-id="${GroupId}"]`).prop('checked', this.checked);
                $(`.pmo-checkbox[data-group-id="${GroupId}"]`).prop('checked', this.checked);

                document.querySelectorAll('.data-checklist').forEach((checklist, index) => {
                    const groupId = index + 1;
                    const checkboxes = checklist.querySelectorAll('.form-check-input');
                    checkboxes.forEach(checkbox => {
                        checkbox.addEventListener('change', () => updateChecklistCount(
                            groupId));
                    });

                    // Initial update in case some checkboxes are pre-checked
                    updateChecklistCount(groupId);
                });
            });

            // Update the state of the check all checkbox based on individual checkboxes
            $('.finance-checkbox').click(function() {
                const GroupId = $(this).data('group-id');
                const allCheckboxes = $(`.finance-checkbox[data-group-id="${GroupId}"]`);
                const checkedCheckboxes = allCheckboxes.filter(':checked');
                $(`.check-all[data-group-id="${GroupId}"]`).prop('checked', allCheckboxes.length ===
                    checkedCheckboxes.length);
            });
            $('.cs-checkbox').click(function() {
                const GroupId = $(this).data('group-id');
                const allCheckboxes = $(`.cs-checkbox[data-group-id="${GroupId}"]`);
                const checkedCheckboxes = allCheckboxes.filter(':checked');
                $(`.check-all[data-group-id="${GroupId}"]`).prop('checked', allCheckboxes.length ===
                    checkedCheckboxes.length);
            });
            $('.business-checkbox').click(function() {
                const GroupId = $(this).data('group-id');
                const allCheckboxes = $(`.business-checkbox[data-group-id="${GroupId}"]`);
                const checkedCheckboxes = allCheckboxes.filter(':checked');
                $(`.check-all[data-group-id="${GroupId}"]`).prop('checked', allCheckboxes.length ===
                    checkedCheckboxes.length);
            });
            $('.infra-checkbox').click(function() {
                const GroupId = $(this).data('group-id');
                const allCheckboxes = $(`.infra-checkbox[data-group-id="${GroupId}"]`);
                const checkedCheckboxes = allCheckboxes.filter(':checked');
                $(`.check-all[data-group-id="${GroupId}"]`).prop('checked', allCheckboxes.length ===
                    checkedCheckboxes.length);
            });
            $('.pmo-checkbox').click(function() {
                const GroupId = $(this).data('group-id');
                const allCheckboxes = $(`.pmo-checkbox[data-group-id="${GroupId}"]`);
                const checkedCheckboxes = allCheckboxes.filter(':checked');
                $(`.check-all[data-group-id="${GroupId}"]`).prop('checked', allCheckboxes.length ===
                    checkedCheckboxes.length);
            });


            function updateTotals() {
                const totalCheckboxes = $('.form-check-input:not(.check-all)').length;
                const totalChecked = $('.form-check-input:not(.check-all):checked').length;
                const totalUnchecked = totalCheckboxes - totalChecked;
                $('#total-checked').text(totalChecked);
                $('#total-unchecked').text(totalUnchecked);

                Highcharts.chart("container", {
                    chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false,
                        type: "pie",
                    },
                    title: null,
                    tooltip: {
                        pointFormat: "{series.name}: <b>{point.percentage:.1f}%</b>",
                    },
                    accessibility: {
                        point: {
                            valueSuffix: "%",
                        },
                    },
                    plotOptions: {
                        series: {
                            allowPointSelect: true,
                            cursor: "pointer",
                            dataLabels: [{
                                    enabled: false,
                                    distance: 20,
                                },
                                {
                                    enabled: true,
                                    distance: -50,
                                    format: "{point.percentage:.1f}%",
                                    style: {
                                        fontSize: "1.2em",
                                        textOutline: "none",
                                        opacity: 1,
                                        color: "white",
                                    },
                                    filter: {
                                        operator: ">",
                                        property: "percentage",
                                        value: 8,
                                    },
                                },
                            ],
                            showInLegend: true,
                        },
                    },
                    series: [{
                        name: "Total",
                        colorByPoint: true,
                        data: [{
                                name: "Total Complete",
                                y: totalChecked,
                                color: "#9ED763",
                            },
                            {
                                name: "Total Un Check List",
                                y: totalUnchecked,
                                color: "#FB2576",
                            },
                            // {
                            //     name: "Remaning Check List",
                            //     y: 10,
                            //     color: "#30AADD",
                            // },
                        ],
                    }, ],
                });





            }

            // Function to update the check-all checkbox based on individual checkboxes
            function updateCheckAll(groupId) {
                const allCheckboxes = $(`.form-check-input[data-group-id="${groupId}"]:not(.check-all)`);
                const checkedCheckboxes = allCheckboxes.filter(':checked');
                $(`.check-all[data-group-id="${groupId}"]`).prop('checked', allCheckboxes.length ===
                    checkedCheckboxes.length);
            }

            // Check all checkboxes in the group when the check-all checkbox is clicked
            $('.check-all').click(function() {
                const groupId = $(this).data('group-id');
                $(`.form-check-input[data-group-id="${groupId}"]:not(.check-all)`).prop('checked', this
                    .checked);
                updateTotals();
            });

            // Update the state of the check-all checkbox and totals based on individual checkboxes
            $('.form-check-input:not(.check-all)').click(function() {
                const groupId = $(this).data('group-id');
                updateCheckAll(groupId);
                updateTotals();
            });

            // Update totals when the tab is shown
            $('button[data-bs-target="#pills-check-finance"]').on('shown.bs.tab', function() {
                updateTotals();
            });

            // Initialize totals on page load
            updateTotals();

        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
    </script>
@endsection
