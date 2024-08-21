@php
    $countryId = request()->get('country');
    $operatorId = request()->get('operator');
    $account_managerId = request()->get('account_manager');
    $pmoId = request()->get('pmo');
    $backendId = request()->get('backend');
    $statues = request()->get('status');
    $inter = request()->get('intergration');
    $start_date = request()->get('from');
    $end_date = request()->get('to');
    $custom_range = request()->get('custom_range');
@endphp

<div class="card shadow-sm mt-0">
    <div class="card-body">
        <div class="row">
            <div class="col-sm-3">
                <div class="form-group">
                    <label for="country">Choose Country</label>
                    <select class="form-control select2" name="country" id="country" style="width: 100%"
                        data-select2-id="select2-data-filtertype" tabindex="-1" aria-hidden="true">
                        <option value="" selected>Select Country</option>
                        @foreach ($countrys as $country)
                            <option value="{{ $country->id }}"<?php echo isset($countryId) && $country->id == $countryId ? 'selected' : ''; ?>>{{ $country->country }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <label for="operator">Choose Operator</label>
                    <select class="form-control select2" id="operator" name="operator" style="width: 100%"
                        data-select2-id="select2-data-dashboard-company" tabindex="-1" aria-hidden="true">
                        <option value="" selected>Select Operator</option>
                        @foreach ($operators as $operator)
                            <option value="{{ $operator['operator_name'] }}" <?php echo isset($operatorId) && $operator['operator_name'] == $operatorId ? 'selected' : ''; ?>>
                                {{ $operator['operator_name'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <label for="account_manager">Choose Account Manager</label>
                    <select class="form-control select2" name="account_manager" id="account_manager" style="width: 100%"
                        data-select2-id="select2-data-account_manager" tabindex="-1" aria-hidden="true">
                        <option value="" selected>Select Account Manager</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" <?php echo isset($account_managerId) && $user->id == $account_managerId ? 'selected' : ''; ?>>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-sm-3">
                <label for="pmo">Choose PMO</label>
                <select class="form-control select2" name="pmo" id="pmo" style="width: 100%"
                    data-select2-id="select2-data-pmo" tabindex="-1" aria-hidden="true">
                    <option value="" selected>Select PMO</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" <?php echo isset($pmoId) && $user->id == $pmoId ? 'selected' : ''; ?>>{{ $user->name }}</option>
                    @endforeach
                </select>


                </select>
            </div>
            <div class="col-sm-3">
                <label>Choose Developer</label>
                <div class="form-group">
                    <select class="form-control select2" name="backend" id="backend" style="width: 100%"
                        data-select2-id="select2-data-backend" tabindex="-1" aria-hidden="true">
                        <option value="" selected>Select Developer</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" <?php echo isset($backendId) && $user->id == $backendId ? 'selected' : ''; ?>>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-sm-3">
                <label>Choose Status</label>
                <div class="form-group">
                    <select class="form-control select2" name="status" id="status" style="width: 100%"
                        data-select2-id="select2-data-status" tabindex="-1" aria-hidden="true">
                        <option value="" selected>Select Status</option>
                        <option value="1" <?php echo isset($statues) && $statues == '1' ? 'selected' : ''; ?>>Active</option>
                        <option value="0" <?php echo isset($statues) && $statues == '0' ? 'selected' : ''; ?>>Not Active</option>
                        {{-- <option value="11-20" <?php echo isset($statues) && $statues == '11-20' ? 'selected' : ''; ?>>Under Review Operational</option>
                        <option value="21-25" <?php echo isset($statues) && $statues == '21-25' ? 'selected' : ''; ?>>Under Review Finance</option>
                        <option value="26" <?php echo isset($statues) && $statues == '26' ? 'selected' : ''; ?>>Go Live</option> --}}

                    </select>
                </div>
            </div>

            <div class="col-sm-3">
                <label>Choose Status Intergration</label>
                <select class="form-control select2" name="intergration" id="intergration">
                    <option value="" selected>Select Status</option>
                    <option value="On Progress Development" <?php echo isset($inter) && $inter == 'On Progress Development' ? 'selected' : ''; ?>>On Progress
                        Development</option>
                    <option value="UAT" <?php echo isset($inter) && $inter == 'UAT' ? 'selected' : ''; ?>>UAT</option>
                    <option value="Go Live" <?php echo isset($inter) && $inter == 'Go Live' ? 'selected' : ''; ?>>Go Live</option>

                    <option value="On Hold" <?php echo isset($inter) && $inter == 'On Hold' ? 'selected' : ''; ?>>On Hold</option>
                    <option value="Draft" <?php echo isset($inter) && $inter == 'Draft' ? 'selected' : ''; ?>>Draft</option>
                </select>
            </div>


            <div class="col-sm-3">
                <label>Date Range</label>
                <select name="date_range" class="form-control select2" required id="date_range" onchange="date_ranges()"
                    <?php echo isset($start_date) && isset($end_date) && $start_date == date('Y-m-d') && $end_date == date('Y-m-d') ? 'value="&from=' . $end_date . '&to=' . $start_date . '"' : ''; ?>>
                    <option value="">Select Date</option>
                    <option value="&from={{ date('Y-m-d') }}&to={{ date('Y-m-d') }}" <?php echo isset($start_date) && isset($end_date) && $start_date == date('Y-m-d') && $end_date == date('Y-m-d') ? 'selected' : ''; ?>>Today
                    </option>
                    <option
                        value="&from={{ date('Y-m-d', strtotime('-1 days')) }}&to={{ date('Y-m-d', strtotime('-1 days')) }}"
                        <?php echo isset($start_date) && isset($end_date) && $start_date == date('Y-m-d', strtotime('-1 days')) && $end_date == date('Y-m-d', strtotime('-1 days')) ? 'selected' : ''; ?>>Yesterday</option>
                    <option value="&from={{ date('Y-m-d', strtotime('-6 days')) }}&to={{ date('Y-m-d') }}"
                        <?php echo isset($start_date) && isset($end_date) && $start_date == date('Y-m-d', strtotime('-6 days')) && $end_date == date('Y-m-d') ? 'selected' : ''; ?>>Last 7 days</option>
                    <option value="&from={{ date('Y-m-d', strtotime('-30 days')) }}&to={{ date('Y-m-d') }}"
                        <?php echo isset($start_date) && isset($end_date) && $start_date == date('Y-m-d', strtotime('-30 days')) && $end_date == date('Y-m-d') ? 'selected' : ''; ?>>Last 30 days</option>
                    <option value="&from={{ date('Y-m-01', strtotime(date('Y-m-d'))) }}&to={{ date('Y-m-d') }}"
                        <?php echo isset($start_date) && isset($end_date) && $start_date == date('Y-m-01', strtotime(date('Y-m-d'))) ? 'selected' : ''; ?>>This Month</option>

                    @php
                        $first_date = strtotime('first day of previous month', time());
                        $previous_month_first_date = date('Y-m-d', $first_date);
                        $previous_month_last_date = date('Y-m-d', strtotime('last day of previous month'));
                    @endphp
                    <option value="&from={{ $previous_month_first_date }}&to={{ $previous_month_last_date }}"
                        <?php echo isset($start_date) && isset($end_date) && $start_date == $previous_month_first_date && $end_date == $previous_month_last_date ? 'selected' : ''; ?>>Last Month</option>
                    <option value="custom_range" <?php echo isset($custom_range) ? 'selected' : ''; ?>>Custom Range</option>
                </select>
            </div>

            <div class="col-6 row <?php echo !isset($custom_range) ? 'gu-hide' : ''; ?>" id="date_range_faield">
                <div class="col-lg-6">
                    {{ Form::label('date', __('Form'), ['class' => 'form-control-label']) }}
                    <input type="text" class="form-control date" name="date" id="dateform"
                        data-progress-id="form" value="{{ isset($start_date) ? $start_date : date('Y-m-d') }}">
                </div>
                <div class="col-lg-6">
                    {{ Form::label('date', __('To'), ['class' => 'form-control-label']) }}
                    <input type="text" class="form-control date" name="date" id="dateto"
                        data-progress-id="to" value="{{ isset($end_date) ? $end_date : date('Y-m-d') }}">
                </div>
            </div>

            <div class="col-lg-2" style="text-align:left;">
                <label class="invisible d-block">Button</label>
                <div class="catalouge-btn">
                    <div><button class="btn btn-primary" onclick="submit()">Submit</button></div>
                    <div><a class="btn btn-secondary" href="{{ route('report.list') }}">Reset</a></div>
                </div>
            </div>
        </div>

        <div class="error_block"></div>
    </div>
</div>

<script>
    function date_ranges() {
        var values = $('#date_range').val();

        if (values == 'custom_range') {
            date_range_faield
            $('#date_range_faield').removeClass('gu-hide');
        } else {
            $('#date_range_faield').addClass('gu-hide');
        }
    }

    function submit() {

        var country = $('#country').val();
        var operator = $('#operator').val();
        var account_manager = $('#account_manager').val();
        var pmo = $('#pmo').val();
        var backend = $('#backend').val();
        var status = $('#status').val();
        var inter = $('#intergration').val();
        var date = $('#date_range').val();
        var orgurl = window.location.pathname;
        let arrurl = orgurl.split('/');
        var urls = window.location.origin + '/' + arrurl[1] + '/' + arrurl[2];
        var date = $('#date_range').val();


        if (country != '') {
            urls = urls + '?country=' + country;
        } else {
            urls = urls + '?';
        }
        if (operator != '') {
            urls = urls + '&operator=' + operator;
        }
        if (account_manager != '') {
            urls = urls + '&account_manager=' + account_manager;
        }
        if (pmo != '') {
            urls = urls + '&pmo=' + pmo;
        }
        if (backend != '') {
            urls = urls + '&backend=' + backend;
        }
        if (status != '') {
            urls = urls + '&status=' + status;
        }
        if (inter != '') {
            urls = urls + '&intergration=' + inter;
        }
        if (date == 'custom_range') {
            var date = '&from=' + $('#dateform').val() + '&to=' + $('#dateto').val() + '&custom_range=custom_range';
        }
        if (date != '') {
            urls = urls + date;
        }
        var url = urls;
        window.location.href = url;

    }
</script>
