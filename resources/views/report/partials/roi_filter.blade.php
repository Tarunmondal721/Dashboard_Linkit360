<?php $countrys = App\Models\Operator::select('country_name', 'country_id')->Status(1)->orderBy('country_name', 'ASC')->distinct()->get(); ?>
<?php $operators = App\Models\Operator::orderBy('operator_name', 'ASC')->get(); ?>
@php
    $CountryId = request()->get('country');
    // dd($CountryId);
    $filterOperator = request()->get('operatorId');
    $start_date = request()->get('from');
    $end_date = request()->get('to');
    $custom_range = request()->get('custom_range');
@endphp
<div class="page-title" style="margin-bottom:25px">
    <div class="row justify-content-between align-items-center">
        <div
            class="col-xl-4 col-lg-4 col-md-4 d-flex align-items-center justify-content-between justify-content-md-start mb-3 mb-md-0">
            <div class="d-inline-block">

                <div style="white-space:nowrap;">@yield('pagetytle')</div>
            </div>
        </div>
        <div class="col-xl-8 col-lg-8 col-md-8 d-flex align-items-center justify-content-between justify-content-md-end">
        </div>
    </div>
</div>


<div class="card shadow-sm mt-0">
    <div class="card-body">

        <div class="row">
            <div class="col-md-4" style="display:none;">
                <label for="">Data Type</label>
                <div class="form-group">
                    <select class="form-control select2" name="data" style="width: 100%"
                        data-select2-id="select2-data-4-cvqm" tabindex="-1" aria-hidden="true" id="data">
                        <option value="daily"
                            data-select2-id="select2-data-6-on6w"{{ Request::route()->getName() == 'report.monitor.daily.operator' ? 'selected' : '' }}>
                            Daily Reports</option>
                        <option
                            value="monthly"{{ Request::route()->getName() == 'report.monitor.monthly.operator' || Request::route()->getName() == 'report.monitor.monthly.country' ? 'selected' : '' }}>
                            Monthly Reports</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <label for="">Report Type</label>
                <div class="form-group">
                    <select class="form-control select2" name="data_type" id="data_type" style="width: 100%"
                        data-select2-id="select2-data-1-qgws" tabindex="-1" aria-hidden="true">

                        <option value="operator" data-select2-id="select2-data-3-u0zv"
                            {{ Request::route()->getName() == 'roi.monitor.operator' ? 'selected' : '' }}>Operator Summary
                        </option>
                        <option
                            value="country"{{ Request::route()->getName() == 'roi.monitor.country' ? 'selected' : '' }}>
                            Country Summary</option>
                        {{-- <option value="company"{{Request::route()->getName() == 'analytic.revenueMonitoring.company'?'selected': ''}}>Company Summary</option> --}}
                    </select>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="ads_ctry_name">Country</label>
                    <select class="form-control select2" name="country_id" id="country" style="width: 100%"
                        data-select2-id="select2-data-dashboard-country" tabindex="-1" aria-hidden="true"
                        onchange="operatorfind()" <?php echo isset($CountryId) ? 'value="' . $CountryId . '"' : ''; ?>>
                        <option value="" selected>Select Country</option>
                        <option value="allcountry">All Country</option>
                        @foreach ($countrys as $country)
                            <option value="{{ $country->country_id }}"<?php echo isset($CountryId) && $country->country_id == $CountryId ? 'selected' : ''; ?>>{{ $country->country_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <label for="ads_opr_name">Operator</label>
                <select class="form-control select2" name="operator_id" id="operator" multiple>
                    <option value="">Select Operator</option>
                    @foreach ($operators as $operator)
                        <option value="{{ $operator->id_operator }}"<?php echo isset($filterOperator) && in_array($operator->id_operator, $filterOperator) ? 'selected' : ''; ?>>
                            {{ !empty($operator->display_name) ? $operator->display_name : $operator->operator_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-4">
                <label>Date Range</label>
                <select name="date_range" class="form-control select2" required id="date_range"
                    onchange="date_ranges()">
                    <option value="">Select Date</option>
                    <option value="&from={{ date('Y-m-d') }}&to={{ date('Y-m-d') }}">Today</option>
                    <option
                        value="&from={{ date('Y-m-d', strtotime('-1 days')) }}&to={{ date('Y-m-d', strtotime('-1 days')) }}">
                        Yesterday</option>
                    <option value="&from={{ date('Y-m-d') }}&to={{ date('Y-m-d', strtotime('-6 days')) }}">Last 7
                        days</option>
                    <option value="&from={{ date('Y-m-d') }}&to={{ date('Y-m-d', strtotime('-30 days')) }}">Last 30
                        days</option>
                    <option
                        value="&to={{ date('Y-m-01', strtotime(date('Y-m-d'))) }}&from={{ date('Y-m-t', strtotime(date('Y-m-d'))) }}">
                        This Month</option>
                    @php
                        $first_date = strtotime('first day of previous month', time());
                        $previous_month_first_date = date('Y-m-d', $first_date);
                        $previous_month_last_date = date('Y-m-d', strtotime('last day of previous month'));
                    @endphp
                    <option value="&from={{ $previous_month_last_date }}&to={{ $previous_month_first_date }}">Last
                        Month</option>
                    <option value="custom_range" <?php echo isset($custom_range) ? 'selected' : ''; ?>>Custom Range</option>
                </select>
            </div>
            <div class="col-6 row <?php echo !isset($custom_range) ? 'gu-hide' : ''; ?>" id="date_range_faield">
                <div class="col-lg-4">
                    {{ Form::label('date', __('Form'), ['class' => 'form-control-label']) }}
                    <input type="text" class="form-control date" name="date" id="dateform"
                        data-progress-id="form" value="{{ isset($start_date) ? $start_date : date('Y-m-d') }}">
                </div>
                <div class="col-lg-4">
                    {{ Form::label('date', __('To'), ['class' => 'form-control-label']) }}
                    <input type="text" class="form-control date" name="date" id="dateto" data-progress-id="to"
                        value="{{ isset($end_date) ? $end_date : date('Y-m-d') }}">
                </div>
            </div>
            <div class="col-md-3">
                <label class="invisible d-block">Search</label>
                <div class="btn btn-primary" onclick="submit()"><i class="fa fa-search"></i> Search</div>
                <a class="btn btn-secondary" href="{{ url()->previous() }}">Reset</a>
            </div>


        </div>
    </div>
</div>


<div class="card shadow-sm mt-0" style="display: none;">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-3">
                    <label>Show Data Base On:</label>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                        <select name="filter_ads_on" class="form-control select2" required id="filter_ads">
                            <option value="higher_revenue">Highest Revenue</option>
                            <option value="lowest_revenue">Lowest Revenue</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-sort"></i>
                            Sort</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
    function operatorfind(id) {

        var value = $('#country').val();
        // console.log(value);
        // if(id != null){
        //     value=id;
        // }
        // alert(value);
        //     alert("I am an alert box!");
        // console.log(value);
        $.ajax({
            type: "POST",
            url: baseUrl + "report/operator",
            data: {
                'id': value
            },
            dataType: "json",
            success: function(responses) {

                document.getElementById('operator').innerHTML = '<option value="">Operator Name</option>';
                $.each(responses, function(index, response) {
                    // console.log(response);
                    // return false;
                    $("#operator").append('<option value="' + response.id_operator + '">' + response
                        .operator_name + '</option>');
                    // if (filterOperator.indexOf(response.id_operator.toString())>-1 ) {
                    //     $("#operator").append('<option value="'+response.id_operator+'" selected>'+response.operator_name+'</option>');
                    // }
                    // else {
                    //$("#operator").append('<option value="'+response.id_operator+'" >'+response.operator_name+'</option>');
                    // }
                });

            },
        });
    }

    function date_ranges() {
        var values = $('#date_range').val();
        if (values == 'custom_range') {
            date_range_faield
            $('#date_range_faield').removeClass('gu-hide');
            // console.log(values);
        } else {
            $('#date_range_faield').addClass('gu-hide');
        }
    }

    function submit() {
        // var e = document.getElementById("report_data");
        // var report_data = e.value;
        // var e = document.getElementById("report_type");
        // var report_type = e.value;
        // var e = document.getElementById("company");
        // var company = e.value;
        // var e = document.getElementById("country");
        // var country = e.value;
        console.log(window.location.pathname);
        // var report_type =$('#report_type').val();
        var data_type = $('#data_type').val();
        var data = $('#data').val();
        // var datatype =$('#datatype').val();
        var country = $('#country').val();
        var operators = $('#operator').val();
        var orgurl = window.location.pathname;
        let arrurl = orgurl.split('/');
        var urls = window.location.origin + '/' + arrurl[1] + '/' + arrurl[2];
        var date = $('#date_range').val();

        var operatorurl = '';
        if (operators.length > 0) {
            $.each(operators, function(index, operator) {
                operatorurl = operatorurl + '&operatorId[]=' + operator;
            });
        } else {
            operatorurl = '';
        }
        if (date == 'custom_range') {
            var date = '&from=' + $('#dateform').val() + '&to=' + $('#dateto').val() + '&custom_range=custom_range';
        }
        if (data == 'daily') {
            var url = urls + '/' + data_type + '?country=' + country + operatorurl + date;
            window.location.href = url;
            console.log(url);
        } else {
            var url = urls + '/' + data_type + '/' + data + '?country=' + country + operatorurl + date;
            window.location.href = url;
            console.log(url);
        }

    }
</script>
