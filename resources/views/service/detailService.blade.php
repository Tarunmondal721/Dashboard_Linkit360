{{-- @extends('layouts.admin')

@section('title')
{{ __('Edit Service') }}
@endsection

@section('content') --}}
@php
    if ($errors->any()) {
        foreach ($errors->all() as $error) {
            Session::flash('error', $error);
        }
    }
@endphp

<form action="{{ route('report.update') }}" method="POST" onsubmit="return serviceEdit()">
    @csrf
    <input type="hidden" id="id" name="id" value="{{ $service->id }}">
    <div class="card shadow-sm mt-0">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="subHeading">General Information</div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="redirect_summary_dropdown">Country*</label>
                        <select class="form-control select2" name="country" id="country" style="width: 100%" disabled>
                            @foreach ($countrys as $country)
                                <option value="{{ $country->id }}" <?php echo $country->id == $service->country_id ? 'selected' : ''; ?>>
                                    {{ $country->country }}
                                </option>
                            @endforeach
                        </select>
                        <span class="gu-hide" style="color: red;"
                            id="errorcountry">{{ __('*Please select country') }}</span>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="summarycompany">Company*</label>
                        <select class="form-control select2" id="company" name="company" style="width: 100%"
                            data-select2-id="select2-data-dashboard-company" tabindex="-1" aria-hidden="true" disabled>
                            @foreach ($companys as $company)
                                <option value="{{ $company->id }}" <?php echo $company->id == $service->company_id ? 'selected' : ''; ?>>
                                    {{ $company->name }}
                                </option>
                            @endforeach
                        </select>
                        <span class="gu-hide" style="color: red;"
                            id="errorcompany">{{ __('*Please select company') }}</span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6">

                    <div class="subHeading2">Operator*</div>

                    <div class="form-check-inline form-check-inline2">
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input" name="operatorType" checked disabled>Existing
                        </label>
                    </div>
                    <div class="form-check-inline form-check-inline2">
                        <label class="form-check-label">
                            <input type="radio" disabled class="form-check-input" name="operatorType"
                                id="operatorType">New
                        </label>
                    </div>

                    <div class="form-group">
                        <select class="form-control select2" name="operator" id="operator" style="width: 100%"
                            disabled>
                            @if (isset($ScOperators))
                                @foreach ($ScOperators as $operator)
                                    <option value="{{ $operator->id_operator }}" <?php echo $operator->id_operator == $service->operator_id ? 'selected' : ''; ?>>
                                        {{ $operator->operator_name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <span class="gu-hide" style="color: red;"
                        id="erroroperator">{{ __('*Please select operator') }}</span>
                </div>
                <!-- The start Modal -->
                <div class="serviceModal">
                    <div class="modal fade" id="commonModal" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div>
                                    <h4 class="h4 font-weight-400 float-left modal-title"></h4>
                                    <a href="#" class="more-text widget-text float-right close-icon"
                                        data-dismiss="modal" aria-label="Close">Close</a>
                                </div>
                                <div class="modal-body">
                                    <label for="redirect_summary_dropdown">Operator Name</label>
                                    <!-- <input type="text" name="operatorName" id="operatorName"> -->
                                    <div>
                                        <div class="form-group">
                                            <input type="text" class="form-control fild-Style" name="operatorName"
                                                id="operatorName" aria-describedby="emailHelp"
                                                placeholder="Input operator name" style="/ width: 78%; /">
                                        </div>
                                    </div>
                                    <label for="redirect_summary_dropdown">Existing Operator In Fery</label>
                                    <!-- <input type="text" name="operatorName" id="operatorName"> -->
                                    <div>
                                        <select class="form-control select2" id="ScOperator" name="ScOperator"
                                            style="width: 100%" data-select2-id="select2-data-dashboard-company"
                                            tabindex="-1" aria-hidden="true" onchange="operaterSelect()">
                                            <option value="" selected>Select Operator</option>
                                            @if (isset($operators))

                                                @foreach ($operators as $ScOperator)
                                                    <option value="{{ $ScOperator['id_operator'] }}">
                                                        {{ $ScOperator['operator_name'] }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>

                                    <div style="text-align: center;">
                                        <div class="btn btn-primary" id="newOperatorSave">save</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- The end Modal -->
                <div class="col-lg-6">

                    <div class="subHeading2">Aggregrator</div>

                    <div class="form-check-inline form-check-inline2">
                        <label class="form-check-label" onclick="aggregratorYes()">
                            <input type="radio" disabled class="form-check-input" name="aggregratorPermission"
                                value="yes" <?php echo $service->aggregator_status == 'yes' ? 'checked' : ''; ?>>Yes
                        </label>
                    </div>
                    <div class="form-check-inline form-check-inline2" onclick="aggregratorNo()">
                        <label class="form-check-label">
                            <input type="radio" disabled class="form-check-input" name="aggregratorPermission"
                                value="no" <?php echo $service->aggregator_status == 'no' ? 'checked' : ''; ?>>No
                        </label>
                    </div>

                    <div class="form-group">
                        <input type="text" disabled class="form-control fild-Style" id="aggregrator"
                            name="aggregrator" value="{{ isset($service->aggregator) ? $service->aggregator : '' }}"
                            aria-describedby="emailHelp" placeholder="Input aggregrator name" <?php echo $service->aggregator_status == 'no' ? 'readonly' : ''; ?>>
                    </div>

                </div>
            </div>

            <div class="row" style="margin-top: 10px;">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="redirect_summary_dropdown">Service Name*</label>
                        <input type="text" class="form-control fild-Style" id="servicename" name="servicename"
                            value="{{ isset($service->service_name) ? $service->service_name : '' }}"
                            aria-describedby="emailHelp" placeholder="Input service name" disabled>
                        <span class="gu-hide" style="color: red;"
                            id="errorservicename">{{ __('*Please enter service name') }}</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="redirect_summary_dropdown">Subkeyword*</label>
                        <input type="text" class="form-control fild-Style" id="subkeyword" name="subkeyword"
                            value="{{ isset($service->subkeyword) ? $service->subkeyword : '' }}"
                            aria-describedby="emailHelp" placeholder="Input subkeyword" disabled>
                        <span class="gu-hide" style="color: red;"
                            id="errorsubkeyword">{{ __('*Please enter subkeyword') }}</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="redirect_summary_dropdown">Short Code*</label>
                        <input type="text" class="form-control fild-Style" id="short_code" name="short_code"
                            value="{{ isset($service->short_code) ? $service->short_code : '' }}"
                            aria-describedby="emailHelp" placeholder="Input short code" disabled>
                        <span class="gu-hide" style="color: red;"
                            id="errorshort_code">{{ __('*Please enter short code') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="card shadow-sm mt-0">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="subHeading">Detail Information</div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <div class="subHeading2">Type</div>
                    <div class="form-check-inline form-check-inline2">
                        <label class="form-check-label">
                            <input type="radio" disabled class="form-check-input" name="type"
                                value="subscription" <?php echo $service->type == 'subscription' ? 'checked' : ''; ?>>Subscription
                        </label>
                    </div>
                    <div class="form-check-inline form-check-inline2">
                        <label class="form-check-label">
                            <input type="radio" disabled class="form-check-input" name="type"
                                value="single charge" <?php echo $service->type == 'single charge' ? 'checked' : ''; ?>>Single Charge
                        </label>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="subHeading2">Channel</div>
                    @php
                        $channels = isset($service->channel) ? unserialize($service->channel) : '';
                        $cycles = isset($service->cycle) ? unserialize($service->cycle) : '';
                        // dd($channels);
                    @endphp
                    <div class="form-check form-check-inline form-check-inline2">
                        <input class="form-check-input" disabled type="checkbox" id="channelCheckbox1"
                            name="channelowap" value="WAP" <?php if (isset($channels)) {
                                foreach ($channels as $key => $channel) {
                                    echo $channel == 'WAP' ? 'checked' : '';
                                }
                            } ?>>
                        <label class="form-check-label" for="channelCheckbox1">WAP</label>
                    </div>
                    <div class="form-check form-check-inline form-check-inline2">
                        <input class="form-check-input" disabled type="checkbox" id="channeloussd"
                            name="channeloussd" value="USSD" <?php if (isset($channels)) {
                                foreach ($channels as $key => $channel) {
                                    echo $channel == 'USSD' ? 'checked' : '';
                                }
                            } ?>>
                        <label class="form-check-label" for="channeloussd">USSD</label>
                    </div>
                    <div class="form-check form-check-inline form-check-inline2">
                        <input class="form-check-input" type="checkbox" id="channelosms" name="channelosms"
                            value="SMS" <?php if (isset($channels)) {
                                foreach ($channels as $key => $channel) {
                                    echo $channel == 'SMS' ? 'checked' : '';
                                }
                            } ?>>
                        <label class="form-check-label" for="channelosms">SMS</label>
                    </div>
                    <div class="form-check form-check-inline form-check-inline2">
                        <input class="form-check-input" disabled type="checkbox" id="channeloivr" name="channeloivr"
                            value="IVR" <?php if (isset($channels)) {
                                foreach ($channels as $key => $channel) {
                                    echo $channel == 'IVR' ? 'checked' : '';
                                }
                            } ?>>
                        <label class="form-check-label" for="channeloivr">IVR</label>
                    </div>
                </div>
                <div class="col-lg-6" onclick="cyclePermission()">
                    <div class="subHeading2">Cycle</div>
                    <div class="form-check form-check-inline form-check-inline2">
                        <input class="form-check-input" disabled type="checkbox" id="cycleDaily" name="cycleDaily"
                            value="daily" <?php echo isset($cycles['changeCycleDaily']) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="cycleDaily">Daily</label>
                    </div>
                    <div class="form-check form-check-inline form-check-inline2">
                        <input class="form-check-input" disabled type="checkbox" id="cycleWeekly" name="cycleWeekly"
                            value="weekly" <?php echo isset($cycles['changeCycleWeekly']) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="cycleWeekly">Weekly</label>
                    </div>
                    <div class="form-check form-check-inline form-check-inline2">
                        <input class="form-check-input" disabled type="checkbox" id="cycleMonthly"
                            name="cycleMonthly" value="monthly" <?php echo isset($cycles['changeCycleMonthly']) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="cycleMonthly">Monthly</label>
                    </div>
                </div>
            </div>

            <div class="row" style="padding-top: 15px;">
                <div class="col-md-12">
                    <div class="subHeading2">Change Cycle</div>
                </div>
                <div class="col-lg-4" style="padding-top: 10px;" id="changeCycleDailyPermission">
                    <label>Daily</label>
                    <select class="form-control select2" disabled id="changeCycleDaily" name="changeCycleDaily"
                        <?php echo !isset($cycles['changeCycleDaily']) ? 'disabled' : ''; ?>>
                        <option value="" selected>Select Daily</option>
                        @for ($x = 1; $x <= 5; $x++)
                            <option value="{{ $x }}" <?php echo isset($cycles['changeCycleDaily']) && $cycles['changeCycleDaily'] == $x ? 'selected' : ''; ?>>
                                {{ $x }}x charge
                            </option>
                        @endfor

                    </select>
                </div>
                <div class="col-lg-4" style="padding-top: 10px;" id="changeCycleWeeklyPermission">

                    <label>Weekly</label>
                    <select class="form-control select2" id="changeCycleWeekly" name="changeCycleWeekly" disabled
                        style="width: 100%" <?php echo !isset($cycles['changeCycleWeekly']) ? 'disabled' : ''; ?>>
                        <option value="" selected>Select Weekly</option>
                        @for ($x = 1; $x <= 5; $x++)
                            <option value="{{ $x }}" <?php echo isset($cycles['changeCycleWeekly']) && $cycles['changeCycleWeekly'] == $x ? 'selected' : ''; ?>>
                                {{ $x }}x charge
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="col-lg-4" style="padding-top: 10px;" id="changeCycleMonthlyPermission">
                    <label>Monthly</label>
                    <select class="form-control select2" disabled id="changeCycleMonthly" style="width: 100%"
                        data-select2-id="select2-data-data-type" tabindex="-1" aria-hidden="true"
                        name="changeCycleMonthly" <?php echo !isset($cycles['changeCycleMonthly']) ? 'disabled' : ''; ?>>
                        <option value="" selected>Select Monthly</option>
                        @for ($x = 1; $x <= 5; $x++)
                            <option value="{{ $x }}" <?php echo isset($cycles['changeCycleMonthly']) && $cycles['changeCycleMonthly'] == $x ? 'selected' : ''; ?>>
                                {{ $x }}x charge
                            </option>
                        @endfor
                    </select>
                </div>
            </div>

            <div class="row" style="padding-top: 20px;">
                <div class="col-lg-6">
                    <div class="subHeading2">Freemium</div>
                    <div class="form-check-inline form-check-inline2">
                        <label class="form-check-label">
                            <input type="radio" disabled class="form-check-input" name="freemiumPermission"
                                onclick="freemiumYes()" <?php echo !empty($service->freemium) ? 'checked' : ''; ?>>Yes
                        </label>
                    </div>
                    <div class="form-check-inline form-check-inline2">
                        <label class="form-check-label">
                            <input type="radio" disabled class="form-check-input" name="freemiumPermission"
                                onclick="freemiumNo()" <?php echo empty($service->freemium) ? 'checked' : ''; ?>>No
                        </label>
                    </div>
                    <select class="form-control select2" disabled style="width: 100%" name="freemiumDays"
                        id="freemiumDays" <?php echo empty($service->freemium) ? 'disabled' : ''; ?>>
                        <option value="" selected>Select Days</option>
                        @for ($x = 1; $x <= 10; $x++)
                            <option value="{{ $x }}" <?php echo !empty($service->freemium) && $service->freemium == $x ? 'selected' : ''; ?>>
                                {{ $x }} Days
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-lg-6">

                    <div class="form-group">
                        <label class="subHeading2" for="exampleFormControlTextarea1">Service Price</label>
                        <textarea class="form-control textarea-height" id="service_price" rows="3" name="service_price" disabled
                            value="{{ isset($service->service_price) ? $service->service_price : '' }}">{{ isset($service->service_price) ? $service->service_price : '' }}</textarea>
                    </div>
                </div>

            </div>

            <div class="row" style="margin-top: 10px;">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="redirect_summary_dropdown">Our Revenue Share</label>
                        <input type="text" disabled class="form-control fild-Style" id="revenueShare"
                            name="revenueShare" aria-describedby="emailHelp" placeholder="Input revenue share"
                            onkeypress='return event.charCode >= 48 && event.charCode <= 57' minlength="0"
                            maxlength="3"
                            value="{{ isset($service->revenue_share) ? $service->revenue_share : '' }}">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label class="" for="subscription_keyword">Subscription Keyword</label>
                        <textarea disabled class="form-control textarea-height" id="subscription_keyword" name="subscription_keyword"
                            rows="3" value="{{ isset($service->subscription_keyword) ? $service->subscription_keyword : '' }}">{{ isset($service->subscription_keyword) ? $service->subscription_keyword : '' }}</textarea>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="redirect_summary_dropdown">Unsubscription Keyword</label>
                        <input type="text" disabled class="form-control fild-Style" id="unsubscription_keyword"
                            name="unsubscription_keyword" aria-describedby="emailHelp" placeholder="Unreg cplay"
                            value="{{ isset($service->unsubscription_keyword) ? $service->unsubscription_keyword : '' }}">
                    </div>
                </div>
                <div class="col-lg-6">

                </div>
            </div>
        </div>
    </div>

    @php
        $portalInformation = isset($service->portal_information) ? unserialize($service->portal_information) : '';
        // dd($service);
    @endphp
    <div class="card shadow-sm mt-0">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="subHeading2">Portal Information</div>
                </div>
            </div>
            <div class="row" style="margin-top: 10px;">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="portal_url">Portal URL</label>
                        <input type="url" disabled class="form-control fild-Style" id="portal_url"
                            name="portal_url" id="portal_url" aria-describedby="portal_url"
                            placeholder="demo.goaly.mobi"
                            value="{{ isset($portalInformation['portal_url']) ? $portalInformation['portal_url'] : '' }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="cms_url">CMS URL</label>
                        <input type="url" disabled class="form-control fild-Style" id="cms_url" name="cms_url"
                            aria-describedby="cms_url" placeholder="cms.demo.goaly.mobi"
                            value="{{ isset($portalInformation['cms_url']) ? $portalInformation['cms_url'] : '' }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="callback_url">Callback URL</label>
                        <input type="url" disabled class="form-control fild-Style" id="callback_url"
                            name="callback_url" aria-describedby="callback_url"
                            placeholder="demo.goaly.mobi/callback"
                            value="{{ isset($portalInformation['callback_url']) ? $portalInformation['callback_url'] : '' }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="notif_subs_url">Notif Subs URL</label>
                        <input type="url" disabled class="form-control fild-Style" id="notif_subs_url"
                            name="notif_subs_url" aria-describedby="emailHelp"
                            placeholder="demo.goaly.mobi/subscription/notif"
                            value="{{ isset($portalInformation['notif_subs_url']) ? $portalInformation['notif_subs_url'] : '' }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="notif_unsubs_url">Notif Unsubs URL</label>
                        <input type="url" class="form-control fild-Style" id="notif_unsubs_url" disabled
                            name="notif_unsubs_url" aria-describedby="notif_unsubs_url"
                            placeholder="demo.goaly.mobi/unsubscription/notif"
                            value="{{ isset($portalInformation['notif_unsubs_url']) ? $portalInformation['notif_unsubs_url'] : '' }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="notif_renewal_url">Notif Renewal URL</label>
                        <input type="url" disabled class="form-control fild-Style" id="notif_renewal_url"
                            name="notif_renewal_url" aria-describedby="notif_renewal_url"
                            placeholder="demo.goaly.mobi/renewal/notif"
                            value="{{ isset($portalInformation['notif_renewal_url']) ? $portalInformation['notif_renewal_url'] : '' }}">
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="card shadow-sm mt-0">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="subHeading2">MT SMS Wording</div>
                </div>
            </div>
            <div class="row" style="margin-top: 10px;">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="subs_sms">Subs SMS</label>
                        <input type="text" disabled class="form-control fild-Style" id="subs_sms"
                            name="subs_sms" aria-describedby="emailHelp" placeholder="hello"
                            value="{{ isset($service->subs_sms) ? $service->subs_sms : '' }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="unsubs_sms">Unsubs SMS</label>
                        <input type="text" disabled class="form-control fild-Style" id="unsubs_sms"
                            name="unsubs_sms" aria-describedby="unsubs_sms" placeholder="good bye"
                            value="{{ isset($service->unsubs_sms) ? $service->unsubs_sms : '' }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="renewal_sms">Renewal SMS</label>
                        <input type="text" disabled class="form-control fild-Style" id="renewal_sms"
                            name="renewal_sms" aria-describedby="renewal_sms" placeholder="welcome again"
                            value="{{ isset($service->renewal_sms) ? $service->renewal_sms : '' }}">
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
        $campaignType = isset($service->campaign_type) ? unserialize($service->campaign_type) : '';
        // dd($campaignType);
    @endphp

    <div class="card shadow-sm mt-0">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="subHeading2">Media Campaign</div>
                </div>
            </div>
            <div class="row" style="margin-top: 10px;">
                <div class="col-lg-6">
                    <div style="margin-bottom: 10px;">Campaign Type</div>
                    <div class="form-check form-check-inline form-check-inline2">
                        <input class="form-check-input" disabled type="checkbox" id="click_to_sms"
                            name="click_to_sms" value="click_to_sms" selected <?php echo isset($campaignType['click_to_sms']) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="click_to_sms">Click2SMS</label>
                    </div>
                    <div class="form-check form-check-inline form-check-inline2">
                        <input class="form-check-input" disabled type="checkbox" name="wap" id="wap"
                            value="wap" <?php echo isset($campaignType['wap']) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="wap">WAP</label>
                    </div>
                    <div class="form-check form-check-inline form-check-inline2">
                        <input class="form-check-input" disabled type="checkbox" name="api" id="api"
                            value="api" <?php echo isset($campaignType['api']) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="api">API</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="campaign_url">Campaign URL</label>
                        <input type="url" disabled class="form-control fild-Style" id="campaign_url"
                            name="campaign_url" aria-describedby="emailHelp" placeholder="demo.goaly.mobi/daily"
                            value="{{ isset($service->campaign_url) ? $service->campaign_url : '' }}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12" style="text-align:right;">
                    <label class="invisible d-block">Button</label>
                    {{-- <button type="submit" class="btn btn-primary">Submit</button> --}}
                </div>
            </div>
        </div>
    </div>
</form>
<script src="{{ asset('assets/js/services.js') }}"></script>
{{-- @endsection --}}
