@extends('layouts.admin')

@section('title')
    {{ __('Edit Service') }}
@endsection

@section('content')
    @php
        if ($errors->any()) {
            foreach ($errors->all() as $error) {
                Session::flash('error', $error);
            }
        }

        // $CountryId = request()->get('country');
        // if (isset($CountryId) && !empty($CountryId)) {
        //     $operators = App\Models\Operator::GetOperatorByCountryId($CountryId)
        //         ->Status(1)->select('operator_name')
        //         ->orderBy('operator_name', 'ASC')
        //         ->get();
        // }

    @endphp


    <div class="page-content page-content-center">
        {{-- {{ Form::model(array('route' =>'report.store', 'method' => 'POST')) }} --}}
        <form action="{{ route('report.update') }}" enctype="multipart/form-data" method="POST"
            onsubmit="return serviceSubmit()" id="reportForm">
            <input type="hidden" id="id" name="id" value="{{ $service->id }}">
            <input type="hidden" name="country" value="{{ $service->country_id }}">

            @csrf
            <div id="accordion">
                <!-- General Information Start Here -->
                <div class="card shadow-sm mt-0">
                    <div class="card-header service-catalogue">
                        General Information
                        <i class="fa fa-caret-down float-right cursor_change icon-size" href="#collapseOne" data-toggle="collapse"></i>

                    </div>
                    <div class="card-body collapse" id="collapseOne" data-parent="#accordion">
                        {{-- <div class="row">
                        <div class="col-md-12">
                            <h4 style="font-weight: 600; color:black;">General Information</h4>
                        </div>
                    </div> --}}

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="redirect_summary_dropdown" class="subHeading2">Country
                                        <sup><i class="fa fa-asterisk" style="color: red; font-size:7px;"></i></sup>
                                    </label>
                                    @if ($service->is_draf == 1)
                                        <select class="form-control select2" name="country" id="country"
                                            style="width: 100%" onchange="Operators()">
                                            <option value="">Select Country</option>
                                            @foreach ($countrys as $country)
                                                <option value="{{ $country->id }}" <?php echo $country->id == $service->country_id ? 'selected' : ''; ?>>
                                                    {{ $country->country }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @else
                                        <select class="form-control select2" disabled name="" id="country"
                                            style="width: 100%">
                                            @foreach ($countrys as $country)
                                                <option value="{{ $country->id }}" <?php echo $country->id == $service->country_id ? 'selected' : ''; ?>>
                                                    {{ $country->country }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @endif

                                    <span class="gu-hide" style="color: red;"
                                        id="errorcountry">{{ __('*Please select country') }}</span>

                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="summarycompany" class="subHeading2">Company
                                        <sup><i class="fa fa-asterisk" style="color: red; font-size:7px;"></i></sup>
                                    </label>
                                    <select class="form-control select2" id="company" name="company" style="width: 100%">
                                        <option value="">Select Company</option>
                                        @foreach ($companys as $company)
                                            <option value="{{ $company->id }}" <?php echo $company->id == $service->company_id ? 'selected' : ''; ?>>{{ $company->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="gu-hide" style="color: red;"
                                        id="errorcompany">{{ __('*Please select company') }}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="subHeading2">Type
                                    <sup><i class="fa fa-asterisk" style="color: red; font-size:7px;"></i></sup>
                                </div>

                                <div class="form-check-inline form-check-inline2">
                                    <label class="form-check-label">
                                        <input type="radio" class="form-check-input" name="service_type" value="service"
                                            <?php echo $service->service_type == 'service' ? 'checked' : ''; ?>>Service
                                    </label>
                                </div>
                                <div class="form-check-inline form-check-inline2">
                                    <label class="form-check-label">
                                        <input type="radio" class="form-check-input" name="service_type" value="product"
                                            <?php echo $service->service_type == 'product' ? 'checked' : ''; ?>>Product
                                    </label>
                                </div>
                                <div>
                                    <span class="gu-hide" style="color: red;"
                                        id="error_service_type">{{ __('*Please select service type') }}</span>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-4">


                                <!-- The start Modal -->
                                <div class="serviceModal">
                                    <div class="modal fade" id="commonModaldj" tabindex="-1" role="dialog"
                                        aria-hidden="true" style="width: 220%;">
                                        <div class="row" style="margin-top: 10%;">
                                            <div class="col-lg-12">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div>
                                                            <h4 class="h4 font-weight-400 float-left modal-title"></h4>
                                                            <a href="#"
                                                                class="more-text widget-text float-right close-icon"
                                                                data-dismiss="modal" aria-label="Close">Close</a>
                                                        </div>
                                                        <div class="modal-body">
                                                            <label for="redirect_summary_dropdown">Operator Name <sup><i
                                                                        class="fa fa-asterisk"
                                                                        style="color: red; font-size:7px;"></i></sup></label>
                                                            <!-- <input type="text" name="operatorName" id="operatorName"> -->
                                                            <div>
                                                                <div class="form-group">
                                                                    <input type="text" class="form-control fild-Style"
                                                                        name="operatorName" id="operatorName"
                                                                        aria-describedby="emailHelp"
                                                                        placeholder="Input operator name"
                                                                        style="/ width: 78%; /">
                                                                </div>
                                                            </div>
                                                            {{-- <label for="redirect_summary_dropdown">Existing Operator In Fery</label> --}}
                                                            <!-- <input type="text" name="operatorName" id="operatorName"> -->
                                                            {{-- <div>
                                        <select class="form-control select2" id="ScOperator" name="ScOperator"
                                            style="width: 100%" data-select2-id="select2-data-dashboard-company"
                                            tabindex="-1" aria-hidden="true" onchange="operaterSelect()">
                                            <option value="" selected>Select Operator</option>
                                            @if (isset($operators))
                                                @foreach ($operators as $ScOperator)
                                                    <option value="{{ $ScOperator['id_operator'] }}">
                                                        {{ $ScOperator['operator_name'] }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div> --}}

                                                            <div style="text-align: center;">
                                                                <div class="btn btn-primary" id="newOperatorSave">save
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <!-- The end Modal -->
                                <label for="summarycompany" class="subHeading2">Operator
                                    <sup><i class="fa fa-asterisk" style="color: red; font-size:7px;"></i></sup>
                                </label>
                                @if ($service->is_draf == 1)
                                    <div class="form-check-inline form-check-inline2">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input" name="exit_operator"
                                                value="1" id="existingRadio" <?php echo $service->exit_operator == 1 ? 'checked' : ''; ?>> Existing
                                        </label>
                                    </div>
                                    <div class="form-check-inline form-check-inline2">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input" name="exit_operator"
                                                value="0" id="operatordj" <?php echo $service->exit_operator == 0 ? 'checked' : ''; ?>> New
                                        </label>
                                    </div>

                                    <!-- Existing Operator Select Dropdown -->
                                    <div class="form-group" id="existingOperatorSelect">
                                        <select class="form-control select2" name="operator" id="operator"
                                            style="width: 100%">
                                            <option value="">Operator Name</option>
                                            @if (isset($operators) && !empty($operators))
                                                @foreach ($operators as $operator)
                                                    <option value="{{ $operator->operator_name }}" <?php echo $service->operator_name == $operator->operator_name ? 'selected' : ''; ?>>
                                                        {{ $operator->operator_name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>

                                    <!-- New Operator Input -->
                                    <div class="form-group" id="newOperatorInput" style="display: none;">
                                        <input type="text" class="form-control fild-Style" id="newOperator"
                                            name="newOperatorName" readonly placeholder="Operator Name"
                                            value="{{ isset($service->operator_name) ? $service->operator_name : '' }}"
                                            aria-describedby="emailHelp">
                                    </div>
                                @else
                                    <div class="form-group" id="newOperatorInput">
                                        <input type="text" class="form-control fild-Style" id="newOperator"
                                            name="operator" readonly
                                            value="{{ isset($service->operator_name) ? $service->operator_name : '' }}"
                                            aria-describedby="emailHelp">
                                    </div>
                                @endif

                                <span class="gu-hide" style="color: red;"
                                    id="erroroperator">{{ __('*Please select operator') }}</span>
                            </div>
                            <div class="col-md-4">

                                <div class="subHeading2">Will be use for Merchant Airpay?
                                    <sup><i class="fa fa-asterisk" style="color: red; font-size:7px;"></i></sup>
                                </div>

                                <div class="form-check-inline form-check-inline2">
                                    <label class="form-check-label">
                                        <input type="radio" class="form-check-input" name="is_airpay" value="yes"
                                            <?php echo $service->is_airpay == 'yes' ? 'checked' : ''; ?>>Yes
                                    </label>
                                </div>
                                <div class="form-check-inline form-check-inline2">
                                    <label class="form-check-label">
                                        <input type="radio" class="form-check-input" name="is_airpay" value="no"
                                            <?php echo $service->is_airpay == 'no' ? 'checked' : ''; ?>>No
                                    </label>
                                </div>
                                <div>
                                    <span class="gu-hide" style="color: red;"
                                        id="error_airpay">{{ __('*Please select merchant airpay') }}</span>
                                </div>

                            </div>

                            <div class="col-md-4">

                                <div class="subHeading2">Aggregrator <sup><i class="fa fa-asterisk"
                                            style="color: red; font-size:7px;"></i></sup></div>

                                <div class="form-check-inline form-check-inline2" onclick="aggregratorYes()">
                                    <label class="form-check-label">
                                        <input type="radio" class="form-check-input" name="aggregratorPermission"
                                            value="yes" <?php echo $service->aggregator_status == 'yes' ? 'checked' : ''; ?>>Yes
                                    </label>
                                </div>
                                <div class="form-check-inline form-check-inline2" onclick="aggregratorNo()">
                                    <label class="form-check-label">
                                        <input type="radio" class="form-check-input" name="aggregratorPermission"
                                            value="no" <?php echo $service->aggregator_status == 'no' ? 'checked' : ''; ?>>No
                                    </label>
                                </div>

                                <div class="form-group">
                                    <input type="text" class="form-control fild-Style" id="aggregrator"
                                        name="aggregrator"
                                        value="{{ isset($service->aggregator) ? $service->aggregator : '' }}"
                                        aria-describedby="emailHelp" placeholder="Input aggregrator name"
                                        <?php echo $service->aggregator_status == 'no' ? 'readonly' : ''; ?>>
                                </div>

                                <span class="gu-hide" style="color: red;"
                                    id="erroraggregrator">{{ __('*Please enter aggregrator name') }}</span>

                            </div>
                        </div>

                        <div class="row" style="margin-top: 10px;">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="redirect_summary_dropdown" class="subHeading2">Service Name
                                        <sup><i class="fa fa-asterisk" style="color: red; font-size:7px;"></i></sup>
                                    </label>
                                    <input type="text" class="form-control fild-Style" id="servicename"
                                        name="servicename" aria-describedby="emailHelp" placeholder="Input service name"
                                        value="{{ isset($service->service_name) ? $service->service_name : '' }}">
                                    <span class="gu-hide" style="color: red;"
                                        id="errorservicename">{{ __('*Please enter service name') }}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="redirect_summary_dropdown" class="subHeading2">Subkeyword
                                        <sup><i class="fa fa-asterisk" style="color: red; font-size:7px;"></i></sup>
                                    </label>
                                    <input type="text" class="form-control fild-Style" id="subkeyword"
                                        name="subkeyword"
                                        value="{{ isset($service->subkeyword) ? $service->subkeyword : '' }}"
                                        aria-describedby="emailHelp" placeholder="Input subkeyword">
                                    <span class="gu-hide" style="color: red;"
                                        id="errorsubkeyword">{{ __('*Please enter subkeyword') }}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="redirect_summary_dropdown" class="subHeading2">Short Code
                                        <sup><i class="fa fa-asterisk" style="color: red; font-size:7px;"></i></sup>
                                    </label>
                                    <input type="text" class="form-control fild-Style" id="short_code"
                                        name="short_code"
                                        value="{{ isset($service->short_code) ? $service->short_code : '' }}"
                                        aria-describedby="emailHelp" placeholder="Input short code" v>
                                    <span class="gu-hide" style="color: red;"
                                        id="errorshort_code">{{ __('*Please enter short code') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 10px;">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="subHeading2">Status Intregration <sup><i class="fa fa-asterisk"
                                                style="color: red; font-size:7px;"></i></sup>
                                    </label>
                                    @if ($service->is_draf == 1)
                                        <select class="form-control fild-Style select2" name="status_intregration"
                                            id="status_intregration">
                                            <option value="Draft" <?php echo $service->status_intregration == 'Draft' ? 'selected' : ''; ?>>Draft</option>
                                        </select>
                                    @else
                                        <select class="form-control fild-Style select2" name="status_intregration"
                                            id="status_intregration">
                                            <option value="UAT" <?php echo $service->status_intregration == 'UAT' ? 'selected' : ''; ?>>UAT</option>
                                            <option value="On Progress Development" <?php echo $service->status_intregration == 'On Progress Development' ? 'selected' : ''; ?>>On Progress
                                                Development</option>
                                            <option value="On Hold" <?php echo $service->status_intregration == 'On Hold' ? 'selected' : ''; ?>>On Hold</option>
                                            @if ($service->status_intregration == 'Go Live')
                                                <option value="Go Live" <?php echo $service->status_intregration == 'Go Live' ? 'selected' : ''; ?>>Go Live</option>
                                            @endif
                                        </select>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="redirect_summary_dropdown" class="subHeading2">Project Start Date
                                        <sup><i class="fa fa-asterisk" style="color: red; font-size:7px;"></i></sup>
                                    </label>
                                    <input type="date" class="form-control fild-Style" id="start_date"
                                        name="start_date" aria-describedby="emailHelp"
                                        value="{{ isset($service->project_start_date) ? $service->project_start_date : '' }}"
                                        onchange="updateEndDateOptions()">
                                    <span class="gu-hide" style="color: red;"
                                        id="errorstartdate">{{ __('*Please enter Project start date') }}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="redirect_summary_dropdown" class="subHeading2">Project End Date
                                        <sup><i class="fa fa-asterisk" style="color: red; font-size:7px;"></i></sup>
                                    </label>
                                    <input type="date" class="form-control fild-Style" id="end_date" name="end_date"
                                        aria-describedby="emailHelp"
                                        value="{{ isset($service->project_end_date) ? $service->project_end_date : '' }}">
                                    <span class="gu-hide" style="color: red;"
                                        id="errorenddate">{{ __('*Please enter Project end date') }}</span>
                                </div>
                            </div>

                        </div>
                        @if ($service->status_intregration == 'Go Live')
                            <div class="row" style="margin-top: 10px;">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="redirect_summary_dropdown" class="subHeading2">Go Live Date
                                            <sup><i class="fa fa-asterisk" style="color: red; font-size:7px;"></i></sup>
                                        </label>
                                        <input type="date" class="form-control fild-Style" id="go_live_date"
                                            name="go_live_date" aria-describedby="emailHelp"
                                            value="{{ isset($service->go_live_date) ? $service->go_live_date : '' }}">
                                        <span class="gu-hide" style="color: red;"
                                            id="livedate">{{ __('*Please enter go live date') }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <!-- General Information End Here -->

                <!-- Detail Information Start Here -->
                <div class="card shadow-sm mt-0">
                    <div class="card-header service-catalogue">
                        Detail Information
                        <i class="fa fa-caret-down float-right cursor_change icon-size" href="#collapseTwo" data-toggle="collapse"></i>

                    </div>
                    <div class="card-body collapse" id="collapseTwo" data-parent="#accordion">

                        <div class="row">
                            <div class="col-md-4">
                                <div class="subHeading2">Type
                                    <sup><i class="fa fa-asterisk" style="color: red; font-size:7px;"></i></sup>
                                </div>
                                <div class="form-check-inline form-check-inline2">
                                    <label class="form-check-label">
                                        <input type="radio" class="form-check-input" name="type"
                                            value="subscription" <?php echo $service->type == 'subscription' ? 'checked' : ''; ?>>Subscription
                                    </label>
                                </div>
                                <div class="form-check-inline form-check-inline2">
                                    <label class="form-check-label">
                                        <input type="radio" class="form-check-input" name="type"
                                            value="single charge" <?php echo $service->type == 'single charge' ? 'checked' : ''; ?>>Single Charge
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4" id="channel_type">
                                @php
                                    $channels = isset($service->channel) ? unserialize($service->channel) : '';
                                    $cycles = isset($service->cycle) ? unserialize($service->cycle) : '';
                                    // dd($cycles);
                                @endphp
                                <div class="subHeading2">Channel
                                    <sup><i class="fa fa-asterisk" style="color: red; font-size:7px;"></i></sup>
                                </div>
                                <div class="form-check form-check-inline form-check-inline2">
                                    <input class="form-check-input" type="checkbox" id="channelCheckbox1"
                                        name="channelowap" {{-- channelowap --}} value="WAP" <?php if (isset($channels)) {
                                            foreach ($channels as $key => $channel) {
                                                echo $channel == 'WAP' ? 'checked' : '';
                                            }
                                        } ?>>
                                    <label class="form-check-label" for="channelCheckbox1">WAP</label>
                                </div>
                                <div class="form-check form-check-inline form-check-inline2">
                                    <input class="form-check-input" type="checkbox" id="channeloussd"
                                        name="channeloussd" {{-- channeloussd --}} value="USSD" <?php if (isset($channels)) {
                                            foreach ($channels as $key => $channel) {
                                                echo $channel == 'USSD' ? 'checked' : '';
                                            }
                                        } ?>>
                                    <label class="form-check-label" for="channeloussd">USSD</label>
                                </div>
                                <div class="form-check form-check-inline form-check-inline2">
                                    <input class="form-check-input" type="checkbox" id="channelosms" name="channelosms"
                                        {{-- channelosms --}} value="SMS" <?php if (isset($channels)) {
                                            foreach ($channels as $key => $channel) {
                                                echo $channel == 'SMS' ? 'checked' : '';
                                            }
                                        } ?>>
                                    <label class="form-check-label" for="channelosms">SMS</label>
                                </div>
                                <div class="form-check form-check-inline form-check-inline2">
                                    <input class="form-check-input" type="checkbox" id="channeloivr" name="channeloivr"
                                        {{-- channeloivr --}} value="IVR" <?php if (isset($channels)) {
                                            foreach ($channels as $key => $channel) {
                                                echo $channel == 'IVR' ? 'checked' : '';
                                            }
                                        } ?>>
                                    <label class="form-check-label" for="channeloivr">IVR</label>
                                </div>
                                <div>
                                    <span class="gu-hide" style="color: red;"
                                        id="error_channel">{{ __('*Please select Channel') }}</span>
                                </div>
                            </div>
                            <div class="col-md-4" onclick="cyclePermission()" id="cycle_type">
                                <div class="subHeading2">Cycle
                                    <sup><i class="fa fa-asterisk" style="color: red; font-size:7px;"></i></sup>
                                </div>
                                <div class="form-check form-check-inline form-check-inline2">
                                    <input class="form-check-input" type="checkbox" id="cycleDaily" name="cycleDaily"
                                        {{-- cycleDaily --}} value="daily" <?php echo isset($cycles['changeCycleDaily']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="cycleDaily">Daily</label>
                                </div>
                                <div class="form-check form-check-inline form-check-inline2">
                                    <input class="form-check-input" type="checkbox" id="cycleWeekly" name="cycleWeekly"
                                        {{-- cycleWeekly --}} value="weekly" <?php echo isset($cycles['changeCycleWeekly']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="cycleWeekly">Weekly</label>
                                </div>
                                <div class="form-check form-check-inline form-check-inline2">
                                    <input class="form-check-input" type="checkbox" id="cycleMonthly"
                                        name="cycleMonthly" {{-- cycleMonthly --}} value="monthly" <?php echo isset($cycles['changeCycleMonthly']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="cycleMonthly">Monthly</label>
                                </div>

                                <div>
                                    <span class="gu-hide" style="color: red;"
                                        id="error_cycle">{{ __('*Please select cycle') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="row" style="padding-top: 15px;">
                            <div class="col-md-12">
                                <div class="subHeading2">Charge Cycle</div>
                            </div>
                            <div class="col-md-4" style="padding-top: 10px;" id="changeCycleDailyPermission">
                                <label>Daily</label>
                                <select class="form-control select2" id="changeCycleDaily" name="changeCycleDaily"
                                    disabled>
                                    <option value="" selected>Select Daily</option>
                                    @for ($x = 1; $x <= 5; $x++)
                                        <option value="{{ $x }}" <?php echo isset($cycles['changeCycleDaily']) && $cycles['changeCycleDaily'] == $x ? 'selected' : ''; ?>>{{ $x }}x
                                            charge
                                        </option>
                                    @endfor

                                </select>
                                {{-- <span class="gu-hide" style="color: red;"
                            id="errorr_daily">{{ __('*Please select Daily') }}</span> --}}
                            </div>
                            <div class="col-md-4" style="padding-top: 10px;" id="changeCycleWeeklyPermission">
                                <label>Weekly</label>
                                <select class="form-control select2" id="changeCycleWeekly" style="width: 100%"
                                    name="changeCycleWeekly" disabled>
                                    <option value="" selected>Select Weekly</option>
                                    @for ($x = 1; $x <= 5; $x++)
                                        <option value="{{ $x }}" <?php echo isset($cycles['changeCycleWeekly']) && $cycles['changeCycleWeekly'] == $x ? 'selected' : ''; ?>>{{ $x }}x
                                            charge
                                        </option>
                                    @endfor
                                </select>
                                {{-- <span class="gu-hide" style="color: red;"
                            id="errorr_weekly">{{ __('*Please select Weekly') }}</span> --}}
                            </div>

                            <div class="col-md-4" style="padding-top: 10px;" id="changeCycleMonthlyPermission">
                                <label>Monthly</label>
                                <select class="form-control select2" id="changeCycleMonthly" style="width: 100%"
                                    data-select2-id="select2-data-data-type" tabindex="-1" aria-hidden="true"
                                    name="changeCycleMonthly" disabled>
                                    <option value="" selected>Select Monthly</option>
                                    @for ($x = 1; $x <= 5; $x++)
                                        <option value="{{ $x }}">{{ $x }}x charge</option>
                                        <option value="{{ $x }}" <?php echo isset($cycles['changeCycleMonthly']) && $cycles['changeCycleMonthly'] == $x ? 'selected' : ''; ?>>{{ $x }}x
                                            charge
                                        </option>
                                    @endfor
                                </select>
                                {{-- <span class="gu-hide" style="color: red;"
                            id="errorr_monthly">{{ __('*Please select Monthly') }}</span> --}}
                            </div>
                        </div>

                        <div class="row" style="padding-top: 20px;">

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="redirect_summary_dropdown" class="subHeading2">Our Revenue Share
                                        <sup><i class="fa fa-asterisk" style="color: red; font-size:7px;"></i></sup>
                                    </label>
                                    <span style="display: flex;">
                                        <input type="number" name="revenueshare" id="revenueoperator"
                                            style="width: 50%;" placeholder="Operator Revenue" min="0"
                                            max="100" step="0.00001" value="{{ $service->revenue_share }}">&nbsp;
                                        &nbsp;
                                        <input type="number" name="revenuemerchant" id="revenuemerchant" min="0"
                                            max="100" step="0.00001" style="width: 50%;"
                                            placeholder="Merchant Revenue"
                                            value="{{ isset($service->merchant_share) ? $service->merchant_share : '0.0' }}">
                                    </span>
                                    <span class="gu-hide" style="color: red;"
                                        id="errorrevenue">{{ __('*Please enter Operator Revenue') }}</span>
                                    <span class="gu-hide" style="color: red;"
                                        id="error_merchant">{{ __('*Please enter Merchant Revenue') }}</span>
                                </div>
                            </div>

                            <div class="col-md-4">

                                <label class="subHeading2">Freemium <sup><i class="fa fa-asterisk"
                                            style="color: red; font-size:7px;"></i></sup></label>

                                <div class="form-check-inline form-check-inline2" onclick="freemiumYes()">
                                    <label class="form-check-label">
                                        <input type="radio" class="form-check-input" name="freemiumPermission"
                                            value="yes" <?php echo $service->is_freemium == 'yes' ? 'checked' : ''; ?>>Yes
                                    </label>
                                </div>

                                <div class="form-check-inline form-check-inline2" onclick="freemiumNo()">
                                    <label class="form-check-label">
                                        <input type="radio" class="form-check-input" name="freemiumPermission"
                                            value="no" <?php echo $service->is_freemium == 'no' ? 'checked' : ''; ?>>No
                                    </label>
                                </div>



                                <select class="form-control select2" style="width: 100%" name="freemiumDays"
                                    id="freemiumDays" <?php echo $service->is_freemium == 'no' ? 'disabled' : ''; ?>>
                                    <option value="0" selected>Select Days</option>
                                    @for ($x = 1; $x <= 10; $x++)
                                        <option value="{{ $x }}" <?php echo isset($service->freemium) && $service->freemium == $x ? 'selected' : ''; ?>>{{ $x }} Days
                                        </option>
                                    @endfor
                                </select>

                                <span class="gu-hide" style="color: red;"
                                    id="error_freemium">{{ __('*Please Select Freemium Days') }}</span>

                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="subHeading2" for="service_price">Service Price(Exclude Tax)
                                        <sup><i class="fa fa-asterisk" style="color: red; font-size:7px;"></i></sup>
                                    </label>
                                    <table class="table">
                                        <tbody id="priceAdd">

                                            @php
                                                $price_data = json_decode($service->currency_service_price, true);
                                                $num_data = count($price_data['currency']);

                                            @endphp
                                            @for ($i = 0; $i < $num_data; $i++)
                                                <tr>
                                                    {{-- <td class="rowCounts">1</td> --}}
                                                    <td style="width: 32%;">
                                                        <select class="form-control select2"
                                                            id="currency{{ $i + 1 }}" name="currency[]">
                                                            <option value="" selected>Currency</option>
                                                            @foreach ($countrys as $country)
                                                                <option value="{{ $country->currency_code }}"
                                                                    <?php echo $price_data['currency'][$i] == $country->currency_code ? 'selected' : ''; ?>>
                                                                    {{ $country->currency_code }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control fild-Style"
                                                            id="service_price" name="service_price[]"
                                                            value="{{ $price_data['service_price'][$i] }}">
                                                    </td>


                                                </tr>
                                            @endfor
                                        </tbody>

                                    </table>
                                    <u><a class="" onclick="addMore({{ $countrys }})"
                                            style="cursor: pointer; color:blue; font-size:x-small">Add new price</a></u>
                                </div>
                                <span class="gu-hide" style="color: red;"
                                    id="error_currency">{{ __('*Please select service price') }}</span>
                                <span class="gu-hide" style="color: red;"
                                    id="error_service">{{ __('*Please enter service price') }}</span>
                            </div>

                        </div>

                        <div class="row" style="padding-top: 20px;">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="redirect_summary_dropdown" class="subHeading2">Report Source
                                        <sup><i class="fa fa-asterisk" style="color: red; font-size:7px;"></i></sup>
                                    </label>
                                    <input type="text" class="form-control fild-Style" id="report_source"
                                        name="report_source" aria-describedby="emailHelp" placeholder="Report Source"
                                        value="{{ isset($service->report_source) ? $service->report_source : '' }}">
                                    <div>
                                        <span class="gu-hide" style="color: red;"
                                            id="errorsource">{{ __('*Please enter Report Source') }}
                                        </span>
                                    </div>

                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="redirect_summary_dropdown" class="subHeading2">Report Partner
                                        <sup><i class="fa fa-asterisk" style="color: red; font-size:7px;"></i></sup>
                                    </label>
                                    <input type="text" class="form-control fild-Style" id="report_partner"
                                        name="report_partner" aria-describedby="emailHelp" placeholder="Report Partner"
                                        value="{{ $service->report_partner }}">
                                    <span class="gu-hide" style="color: red;"
                                        id="errorpartner">{{ __('*Please enter report partner') }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="redirect_summary_dropdown" class="subHeading2">URL CS Tools Source
                                    </label>
                                    <input type="text" class="form-control fild-Style" id="cs_tool" name="cs_tool"
                                        aria-describedby="emailHelp" placeholder="URL CS Tools Source"
                                        value="{{ $service->url_cs_tools }}">
                                </div>
                            </div>

                        </div>
                        <div class="row" style="margin-top: 10px;">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="redirect_summary_dropdown" class="subHeading2">URL CS Tools Main
                                    </label>
                                    <input type="text" class="form-control fild-Style" id="cs_tool" name="cs_tool_main"
                                        aria-describedby="emailHelp" placeholder="URL CS Tools Main"
                                        value="{{ $service->url_cs_tools_main }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="redirect_summary_dropdown" class="subHeading2">Schedule Payment

                                    </label>
                                    <input type="date" class="form-control fild-Style" id="schedule_payment"
                                        name="schedule_payment" aria-describedby="emailHelp"
                                        value="{{ isset($service->schedule_payment) ? $service->schedule_payment : '' }}">

                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="redirect_summary_dropdown" class="subHeading2">When Payment Come In
                                    </label>
                                    <input type="date" class="form-control fild-Style" id="payment_come"
                                        name="payment_come" aria-describedby="emailHelp"
                                        value="{{ isset($service->payment_come_date) ? $service->payment_come_date : '' }}">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Detail Information End Here -->

                <!-- Detail Portal Start Here -->
                <div class="card shadow-sm mt-0">
                    <div class="card-header service-catalogue">
                        Detail Portal
                        <i class="fa fa-caret-down float-right cursor_change icon-size" href="#collapseThree" data-toggle="collapse"></i>
                    </div>

                    <div class="card-body collapse" id="collapseThree" data-parent="#accordion">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="redirect_summary_dropdown" class="subHeading2">Sub Domain Portal
                                        <sup><i class="fa fa-asterisk" style="color: red; font-size:7px;"></i></sup>
                                    </label>
                                    <input type="text" class="form-control fild-Style" id="domain_portal"
                                        name="domain_portal" aria-describedby="emailHelp" placeholder="Sub Domain Portal"
                                        value="{{ $service->sub_domain_portal }}">
                                    <span class="gu-hide" style="color: red;"
                                        id="errordomain">{{ __('*Please enter domain name') }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="redirect_summary_dropdown" class="subHeading2">Portal URL
                                    </label>
                                    <input type="text" class="form-control fild-Style" id="portal_url"
                                        name="portal_url" aria-describedby="emailHelp" placeholder="Portal URL"
                                        value="{{ $service->portal_url }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="redirect_summary_dropdown" class="subHeading2">CMS Portal
                                    </label>
                                    <input type="text" class="form-control fild-Style" id="portal_url"
                                        name="cms_portal" aria-describedby="emailHelp" placeholder="CMS Portal"
                                        value="{{ $service->cms_portal }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="redirect_summary_dropdown" class="subHeading2">Username CMS Portal
                                    </label>
                                    <input type="text" class="form-control fild-Style" id="username_portal"
                                        name="username_portal" aria-describedby="emailHelp"
                                        placeholder="Username CMS Portal" value="{{ $service->username_cms_portal }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="redirect_summary_dropdown" class="subHeading2">Password CMS Portal
                                    </label>
                                    <input type="text" class="form-control fild-Style" id="password_portal"
                                        name="password_portal" aria-describedby="emailHelp"
                                        placeholder="Password CMS Portal" value="{{ $service->password_cms_portal }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Detail Portal End Here -->

                <!-- Detail Campaign Start Here -->
                <div class="card shadow-sm mt-0">
                    <div class="card-header service-catalogue">
                        Detail Campaign
                        <i class="fa fa-caret-down float-right cursor_change icon-size" href="#collapseFour" data-toggle="collapse"></i>

                    </div>

                    <div class="card-body collapse" id="collapseFour" data-parent="#accordion">
                        <div class="row">

                            <div class="col-md-4">

                                @php
                                    $campaign = isset($service->campaign_type) ? unserialize($service->campaign_type) : '';
                                    // dd($cycles);
                                @endphp
                                <div class="form-group" id="campaign">
                                    <label for="redirect_summary_dropdown" class="subHeading2">Campaign Type
                                    </label>
                                    <div class="form-check-inline form-check-inline2">

                                        <label class="form-check-label">
                                            <input type="checkbox" class="form-check-input" name="sms"
                                                value="Click2SMS" <?php if (isset($campaign)) {
                                                    foreach ($campaign as $key => $cam) {
                                                        echo $cam == 'Click2SMS' ? 'checked' : '';
                                                    }
                                                } ?>>Click2SMS
                                        </label>
                                    </div>
                                    <div class="form-check-inline form-check-inline2">
                                        <label class="form-check-label">
                                            <input type="checkbox" class="form-check-input" name="wap"
                                                value="S2S/Wap" <?php if (isset($campaign)) {
                                                    foreach ($campaign as $key => $cam) {
                                                        echo $cam == 'S2S/Wap' ? 'checked' : '';
                                                    }
                                                } ?>>S2S/Wap
                                        </label>
                                    </div>
                                    <div class="form-check-inline form-check-inline2">
                                        <label class="form-check-label">
                                            <input type="checkbox" class="form-check-input" name="api"
                                                value="API" <?php if (isset($campaign)) {
                                                    foreach ($campaign as $key => $cam) {
                                                        echo $cam == 'API' ? 'checked' : '';
                                                    }
                                                } ?>>API
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="redirect_summary_dropdown" class="subHeading2">URL Postback
                                    </label>
                                    <input type="text" class="form-control fild-Style" id="postback" name="postback"
                                        aria-describedby="emailHelp" placeholder="URL Postback"
                                        value="{{ $service->url_postback }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="redirect_summary_dropdown" class="subHeading2">URL Campaign
                                    </label>
                                    <input type="text" class="form-control fild-Style" id="campaign_url"
                                        name="campaign_url" aria-describedby="emailHelp" placeholder="URL Campaign"
                                        value="{{ isset($service->url_campaign) ? $service->url_campaign : '' }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Detail Campaign End Here -->

                <!-- Upload File Document Start Here -->
                <div class="card shadow-sm mt-0">
                    <div class="card-header service-catalogue">
                        Upload File Document
                        <i class="fa fa-caret-down float-right cursor_change icon-size" href="#collapseFive" data-toggle="collapse"></i>

                        </div>

                    <div class="card-body collapse" id="collapseFive" data-parent="#accordion">
                        <div class="row">

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="subHeading2">Upload File PB (Product Brief) @if ($service->product_brief_file)
                                            <a href="{{ asset('assets/service_catalogue/product_brief/' . $service->product_brief_file) }}"
                                                class="files" id="display7" download> <i
                                                    class="fa fa-download"></i></a>
                                        @endif
                                    </label>
                                    <div class="form-check-inline">
                                        <label class="form-check-label">
                                            <input type="file" class="form-check-input" name="product_brief_file"
                                                style="display: none;" onchange="updateFileName(this)">
                                            <button type="button" class="btn file"
                                                onclick="document.querySelector('input[name=\'product_brief_file\']').click();">Choose
                                                File</button>
                                            <span id="file-selected">{{ $service->product_brief_file }}</span>
                                        </label>

                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="subHeading2">Upload FAQ @if ($service->faq_file)
                                            <a href="{{ asset('assets/service_catalogue/faq_file/' . $service->faq_file) }}"
                                                class="files" id="display6"download> <i class="fa fa-download"></i></a>
                                        @endif
                                    </label>
                                    <div class="form-check-inline">
                                        <label class="form-check-label">
                                            <input type="file" class="form-check-input" name="faq_file"
                                                style="display: none;" onchange="updateFileName1(this)">
                                            <button type="button" class="btn file"
                                                onclick="document.querySelector('input[name=\'faq_file\']').click();">Choose
                                                File</button>
                                            <span id="file-select">{{ $service->faq_file }}</span>
                                        </label>

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="subHeading2">Contract @if ($service->contract_file)
                                            <a href="{{ asset('assets/service_catalogue/contract_file/' . $service->contract_file) }}"
                                                class="files" id="display5" download> <i
                                                    class="fa fa-download"></i></a>
                                        @endif
                                    </label>
                                    <div class="form-check-inline">
                                        <label class="form-check-label">
                                            <input type="file" class="form-check-input" name="contract_file"
                                                style="display: none;" onchange="updateFileName2(this)">
                                            <button type="button" class="btn file"
                                                onclick="document.querySelector('input[name=\'contract_file\']').click();">Choose
                                                File</button>
                                            <span id="file-select1">{{ $service->contract_file }}</span>
                                        </label>

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="subHeading2">Merchant COI Document @if ($service->merchant_coi_file)
                                            <a href="{{ asset('public/assets/service_catalogue/coi_file/' . $service->merchant_coi_file) }}"
                                                class="files" id="display4" download> <i
                                                    class="fa fa-download"></i></a>
                                        @endif
                                    </label>
                                    <div class="form-check-inline">
                                        <label class="form-check-label">
                                            <input type="file" class="form-check-input" name="coi_file"
                                                style="display: none;" onchange="updateFileName3(this)">
                                            <button type="button" class="btn file"
                                                onclick="document.querySelector('input[name=\'coi_file\']').click();">Choose
                                                File</button>
                                            <span id="file-select2">{{ $service->merchant_coi_file }}</span>
                                        </label>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row" style="padding-top: 15px">
                            <div class="col-md-3">

                                <div class="form-group">
                                    <label class="subHeading2">Addendums @if ($service->addendums_file)
                                            <a href="{{ asset('assets/service_catalogue/addendums_file/' . $service->addendums_file) }}"
                                                class="files" id="display3" download> <i
                                                    class="fa fa-download"></i></a>
                                        @endif
                                    </label>
                                    <div class="form-check-inline">
                                        <label class="form-check-label">
                                            <input type="file" class="form-check-input" name="addendums_file"
                                                style="display: none;" onchange="updateFileName4(this)">
                                            <button type="button" class="btn file"
                                                onclick="document.querySelector('input[name=\'addendums_file\']').click();">Choose
                                                File</button>
                                            <span id="file-select3" value="">{{ $service->addendums_file }}</span>
                                        </label>

                                    </div>

                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="subHeading2">Content Authority Letter @if ($service->content_authority_letter)
                                            <a href="{{ asset('assets/service_catalogue/authority_file/' . $service->content_authority_letter) }}"
                                                class="files" id="display2" download> <i
                                                    class="fa fa-download"></i></a>
                                        @endif
                                    </label>
                                    <div class="form-check-inline">
                                        <label class="form-check-label">
                                            <input type="file" class="form-check-input" name="authority_file"
                                                style="display: none;" onchange="updateFileName5(this)">
                                            <button type="button" class="btn file"
                                                onclick="document.querySelector('input[name=\'authority_file\']').click();">Choose
                                                File</button>
                                            <span id="file-select4">{{ $service->content_authority_letter }}</span>
                                        </label>

                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="subHeading2">COR & DGT 1 @if ($service->cor_dgt_file)
                                            <a href="{{ asset('assets/service_catalogue/cor_dgt_file/' . $service->cor_dgt_file) }}"
                                                class="files" id="display1" download> <i
                                                    class="fa fa-download"></i></a>
                                        @endif
                                    </label>

                                    <div class="form-check-inline">
                                        <label class="form-check-label">


                                            <input type="file" class="form-check-input" name="cor_dgt_file"
                                                style="display: none;" onchange="updateFileName6(this)">
                                            <button type="button" class="btn file"
                                                onclick="document.querySelector('input[name=\'cor_dgt_file\']').click();">Choose
                                                File</button>
                                            <span id="file-select5"
                                                name="cor_dgt_file">{{ $service->cor_dgt_file }}</span>

                                        </label>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <span class="text-danger">Note:Each File Upload Max 10MB</span>

                    </div>
                </div>
                <!-- Upload File Document End Here -->

                <!-- Escalation Matrix Internal Team Start Here -->
                <div class="card shadow-sm mt-0">
                    <div class="card-header service-catalogue">
                        Escalation Matrix Internal Team
                        <i class="fa fa-caret-down float-right cursor_change icon-size" href="#collapseSix" data-toggle="collapse"></i>

                    </div>

                    <div class="card-body collapse" id="collapseSix" data-parent="#accordion">
                        <div class="row">
                            <div class="col-md-12">
                                <button type="button" class="btn file btn-sm"
                                    onclick="addPeople({{ $Users }})">Add
                                    People</button>
                            </div>
                            <div class="col-lg-12">
                                <table class="table">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th scope="col">No</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">Email</th>
                                            <th scope="col">Whatsapp Number</th>
                                            <th scope="col">Level</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="peopleTable">

                                        @php
                                            $matrix_data = json_decode($service->matrix_enternal_team, true);
                                            $num_matrix = count($matrix_data['team_name']);

                                        @endphp
                                        @for ($i = 0; $i < $num_matrix; $i++)
                                            <tr>
                                                <td class="rowCount">{{ $i + 1 }}</td>
                                                <td style="width: 200px;">
                                                    <select class="form-control selectUser select2"
                                                        id="selectUser{{ $i + 1 }}" onchange="Email(this)"
                                                        name="team_name[]">
                                                        <option value="" selected>Select Name</option>
                                                        @foreach ($Users as $User)
                                                            <option value="{{ $User->id }}" <?php echo $User->id == $matrix_data['team_name'][$i] ? 'selected' : ''; ?>>
                                                                {{ $User->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control fild-Style"
                                                        id="selectEmail{{ $i + 1 }}" readonly
                                                        style="height: 39px;" name="team_email[]"
                                                        value="{{ isset($matrix_data['team_email'][$i]) ? $matrix_data['team_email'][$i] : '' }}"
                                                        aria-describedby="emailHelp" placeholder="Email">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control fild-Style "
                                                        id="whatsapp_number_1" name="team_whatsapp[]"
                                                        value="{{ isset($matrix_data['team_whatsapp'][$i]) ? $matrix_data['team_whatsapp'][$i] : '' }}"
                                                        style="height: 39px;" name="whatsapp_number"
                                                        aria-describedby="emailHelp" placeholder="Whatsapp Number">
                                                </td>
                                                <td>
                                                    <select class="form-control selectLevel select2"
                                                        id="selectLevel{{ $i + 1 }}" name="level[]">
                                                        <option value="" selected>Select Level</option>
                                                        <option value="level1" <?php echo $matrix_data['level'][$i] == 'level1' ? 'selected' : ''; ?>>Level 1</option>
                                                        <option value="level2" <?php echo $matrix_data['level'][$i] == 'level2' ? 'selected' : ''; ?>>Level 2</option>
                                                        <option value="level3" <?php echo $matrix_data['level'][$i] == 'level3' ? 'selected' : ''; ?>>Level 3</option>
                                                        <option value="level4" <?php echo $matrix_data['level'][$i] == 'level4' ? 'selected' : ''; ?>>Level 4</option>
                                                        <option value="level5" <?php echo $matrix_data['level'][$i] == 'level5' ? 'selected' : ''; ?>>Level 5</option>
                                                        <option value="level6" <?php echo $matrix_data['level'][$i] == 'level6' ? 'selected' : ''; ?>>Level 6</option>
                                                    </select>
                                                </td>
                                                {{-- <td>
                                        <button type="button" class="delete-btn" data title="Delete" hidden
                                            onclick="removeClient(this)">
                                            <i class="fa fa-trash" style="color: red"></i>
                                        </button>
                                    </td> --}}
                                            </tr>
                                        @endfor
                                    </tbody>
                                </table>
                            </div>


                        </div>
                    </div>
                </div>
                <!-- Escalation Matrix Internal Team End Here -->

                <!-- Escalation Matrix Client Start Here -->
                <div class="card shadow-m mt-0">
                    <div class="card-header service-catalogue">
                        Escalation Matrix Client
                            <i class="fa fa-caret-down float-right cursor_change icon-size" href="#collapseSeven" data-toggle="collapse"></i>

                        </div>

                    <div class="card-body collapse" id="collapseSeven" data-parent="#accordion">
                        <div class="row">
                            <div class="col-md-12">
                                <h4 style="font-weight: 700; color:black;">Escalation Matrix Client</h4>
                            </div>
                            {{-- <input type="text" id="Count" name="userCount" value="1"> --}}
                            <div class="col-md-12">
                                <button type="button" class="btn file btn-sm" onclick="addRow()">Add People</button>
                            </div>
                            <div class="col-lg-12">
                                <table class="table" id="clientTable">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>No</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Whatsapp Number</th>
                                            <th></th>
                                        </tr>
                                    </thead>


                                    <tbody id="clientadd">
                                        @php
                                            $client_data = json_decode($service->matrix_client, true);
                                            $num_clients = count($client_data['client_name']);
                                        @endphp

                                        @for ($i = 0; $i < $num_clients; $i++)
                                            <tr>
                                                <td class="">{{ $i + 1 }}</td>
                                                <td><input type="text" class="form-control fild-Style"
                                                        style="height: 39px;" placeholder="Name" name="client_name[]"
                                                        value="{{ $client_data['client_name'][$i] }}">
                                                </td>
                                                <td><input type="email" class="form-control fild-Style"
                                                        style="height: 39px;" placeholder="Email" name="client_email[]"
                                                        value="{{ $client_data['client_email'][$i] }}">
                                                </td>
                                                <td><input type="number" class="form-control fild-Style"
                                                        style="height: 39px;" placeholder="Whatsapp Number"
                                                        name="client_whatsapp[]"
                                                        value="{{ isset($client_data['client_whatsapp'][$i]) ? $client_data['client_whatsapp'][$i] : '' }}">
                                                </td>

                                            </tr>
                                        @endfor
                                    </tbody>


                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Escalation Matrix Client End Here -->

                <!-- Escalation Matrix Telco Start Here -->
                <div class="card shadow-m mt-0">
                    <div class="card-header service-catalogue"
                        >Escalation Matrix Telco
                        <i class="fa fa-caret-down float-right cursor_change icon-size" href="#collapseEight" data-toggle="collapse"></i>

                        </div>

                    <div class="card-body collapse" id="collapseEight" data-parent="#accordion">
                        <div class="row">
                            <div class="col-md-12">
                                <h4 style="font-weight: 700; color:black;">Escalation Matrix Telco</h4>
                            </div>
                            {{-- <input type="text" id="Count" name="userCount" value="1"> --}}
                            <div class="col-md-12">
                                <button type="button" class="btn file btn-sm" onclick="addTelco()">Add People</button>
                            </div>
                            <div class="col-lg-12">
                                <table class="table" id="telcoTable">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>No</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Whatsapp Number</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="telcoadd">
                                        @php
                                            $telco_data = json_decode($service->matrix_telco, true);
                                            $num_telco = count($telco_data['telco_name']);
                                        @endphp

                                        @for ($i = 0; $i < $num_telco; $i++)
                                            <tr>
                                                <td class="">{{ $i + 1 }}</td>
                                                <td><input type="text" class="form-control fild-Style"
                                                        style="height: 39px;" placeholder="Name" name="telco_name[]"
                                                        value="{{ isset($telco_data['telco_name'][$i]) ? $telco_data['telco_name'][$i] : '' }}">
                                                </td>
                                                <td><input type="email" class="form-control fild-Style"
                                                        style="height: 39px;" placeholder="Email" name="telco_email[]"
                                                        value="{{ isset($telco_data['telco_email'][$i]) ? $telco_data['telco_email'][$i] : '' }}">
                                                </td>
                                                <td><input type="number" class="form-control fild-Style"
                                                        style="height: 39px;" placeholder="Whatsapp Number"
                                                        name="telco_whatsapp[]"
                                                        value="{{ isset($telco_data['telco_whatsapp'][$i]) ? $telco_data['telco_whatsapp'][$i] : '' }}">
                                                </td>
                                            </tr>
                                        @endfor

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Escalation Matrix Telco End Here -->

                <!-- Team In Charge  Start Here -->
                <div class="card shadow-sm mt-0">
                    <div class="card-header service-catalogue">
                        Team In Charge
                        <i class="fa fa-caret-down float-right cursor_change icon-size" href="#collapseNine" data-toggle="collapse"></i>
                    </div>
                    <div class="card-body collapse" id="collapseNine" data-parent="#accordion">
                        <div class="row">
                            <div class="col-md-3" style="padding-top: 10px;">
                                <label class="subHeading2">Account Manager
                                    <sup><i class="fa fa-asterisk" style="color: red; font-size:7px;"></i></sup>
                                </label>
                                <select class="form-control select2" id="account_manager" name="account_manager">
                                    <option value="">Select Account Manager</option>
                                    @foreach ($Users as $User)
                                        <option value="{{ $User->id }}" <?php echo $User->id == $service->account_manager ? 'selected' : ''; ?>>{{ $User->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="gu-hide" style="color: red;"
                                    id="erroraccount_manager">{{ __('*Please select account manager') }}</span>
                            </div>
                            <div class="col-md-3" style="padding-top: 10px;">
                                <label class="subHeading2">PMO
                                    <sup><i class="fa fa-asterisk" style="color: red; font-size:7px;"></i></sup>
                                </label>
                                <select class="form-control select2" id="pmo" name="pmo">
                                    <option value="">Select PMO</option>
                                    @foreach ($Users as $User)
                                        <option value="{{ $User->id }}" <?php echo $User->id == $service->pmo ? 'selected' : ''; ?>>{{ $User->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="gu-hide" style="color: red;"
                                    id="errorpmo">{{ __('*Please select PMO') }}</span>
                            </div>
                            <div class="col-md-3" style="padding-top: 10px;">
                                <label class="subHeading2">Developer/Backend
                                    <sup><i class="fa fa-asterisk" style="color: red; font-size:7px;"></i></sup>
                                </label>
                                <select class="form-control select2" id="backend" name="backend">
                                    <option value="">Select Developer</option>
                                    @foreach ($Users as $User)
                                        <option value="{{ $User->id }}" <?php echo $User->id == $service->backend ? 'selected' : ''; ?>>{{ $User->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="gu-hide" style="color: red;"
                                    id="errorbackend">{{ __('*Please select Developer') }}</span>
                            </div>
                            <div class="col-md-3" style="padding-top: 10px;">
                                <label class="subHeading2">CS Leader
                                    <sup><i class="fa fa-asterisk" style="color: red; font-size:7px;"></i></sup>
                                </label>
                                <select class="form-control select2" id="csteam" name="csteam">
                                    <option value="">Select CS Team</option>
                                    @foreach ($Users as $User)
                                        <option value="{{ $User->id }}" <?php echo $User->id == $service->cs_team ? 'selected' : ''; ?>>{{ $User->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="gu-hide" style="color: red;"
                                    id="errorcsteam">{{ __('*Please select CS Team') }}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <label class="subHeading2">Infra Leader
                                    <sup><i class="fa fa-asterisk" style="color: red; font-size:7px;"></i></sup>
                                </label>
                                <select class="form-control select2" id="infrateam" name="infrateam">
                                    <option value="">Select Infra Leader</option>
                                    @foreach ($Users as $User)
                                        <option value="{{ $User->id }}" <?php echo $User->id == $service->infra_team ? 'selected' : ''; ?>>{{ $User->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="gu-hide" style="color: red;"
                                    id="errorinfrateam">{{ __('*Please select Infra Team') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Team In Charge  End Here -->

                <!--Button Part Start Here -->
                <div class="row">

                    <div class="col-md-12" style="text-align:right;">
                        <label class="invisible d-block">Button</label>
                        @if ($service->is_draf == 1)
                            <button type="submit" name="is_draf" value="1" class="btn btn-primary"
                                onclick="saveAsDraft()">Update As
                                Draft</button>
                            @if ($service->is_active == 1)
                                <a name="is_active" value="0" class="btn btn-danger" id="statuschange">Change
                                    Status To Non Active</a>
                            @else
                                <a name="is_active" value="1" class="btn btn-success" id="statuschange">Change
                                    Status To Active</a>
                            @endif

                            <button type="submit" name="is_draft" value="0"
                                class="btn btn-primary">Create</button>
                        @else
                            <button type="submit" name="" value="is_draft"
                                class="btn btn-primary">Update</button>

                            @if ($service->is_active == 1)
                                <a name="is_active" value="0" class="btn btn-danger" id="statuschange">Change
                                    Status To Non Active</a>
                            @else
                                <a name="is_active" value="1" class="btn btn-success" id="statuschange">Change
                                    Status To Active</a>
                            @endif
                            <button type="submit" name="" value="" class="btn btn-primary"> <a
                                    href="{{ route('report.list') }}" style="color:white">Cancel</a> </button>
                        @endif

                    </div>

                </div>
                <!--Button Part End Here -->


                <!-- Status Change Model Start Here -->
                <div class="statusModal">
                    <div class="modal fade" id="commonstatus" tabindex="-1" role="dialog" aria-hidden="true"
                        style="width: 155%;">
                        <div class="row" style="margin-top: 10%;">
                            <div class="col-lg-6">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div>
                                            <h4 class="h4 font-weight-400 float-left modal-title"></h4>
                                            <a href="#" class="more-text widget-text float-right close-icon"
                                                data-dismiss="modal" aria-label="Close">Close</a>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group" style="margin-left: 30px;">
                                                <div class="form-check-inline form-check-inline2">
                                                    @if ($service->is_active == 1)
                                                        <label class="form-check-label"
                                                            style="font-weight: bold; color: #333; font-sixe: smaller;">
                                                            <input type="checkbox"
                                                                class="form-check-input service-catalouge-checkbox"
                                                                name="commonstatus"
                                                                style="height: 20px; appearance: none;">
                                                            <span class="checkmark"></span>
                                                            Are you sure want to <br> Non-Active this service?
                                                        </label>
                                                    @else
                                                        <label class="form-check-label"
                                                            style="font-weight: bold; color: #333; font-sixe: smaller;">
                                                            <input type="checkbox"
                                                                class="form-check-input service-catalouge-checkbox"
                                                                name="commonstatus"
                                                                style="height: 20px; appearance: none;">
                                                            <span class="checkmark"></span>
                                                            Are you sure want to <br> Re-Active this service?
                                                        </label>
                                                    @endif

                                                </div>
                                            </div>
                                            <div style="text-align: center;">
                                                <div class="btn btn-primary" data-service-id="{{ $service->id }}"
                                                    id="newstatus">Yes</div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- Status Change Model End Here -->
            </div>
        </form>


    </div>
    <script src="{{ asset('assets/js/services.js') }}"></script>
    <script>
        window.onload = updateEndDateOptions();
        window.onload = cyclePermission();

        function saveAsDraft() {
            document.getElementById('reportForm').setAttribute('action', '{{ route('draft.update') }}');
            document.getElementById('reportForm').removeAttribute('onsubmit');
            document.getElementById('reportForm').submit();
        }

        $(document).ready(function () {
        $('#accordion .collapse').on('shown.bs.collapse', function () {
            $(this).prev().find('.fa').removeClass('fa-caret-down').addClass('fa-caret-up');
        }).on('hidden.bs.collapse', function () {
            $(this).prev().find('.fa').removeClass('fa-caret-up').addClass('fa-caret-down');
        });
    });
    </script>
@endsection
