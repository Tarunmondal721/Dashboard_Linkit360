<?php
    $countrys = App\Models\Operator::select('country_name','country_id')->Status(1)->orderBy('country_name', 'ASC')->distinct()->get();
    $operators = App\Models\Operator::orderBy('operator_name', 'ASC')->get();
    $services = App\Models\Service::orderBy('service_name', 'ASC')->get();
    $start_date = request()->get('from');
    $end_date =  request()->get('to');
    $custom_range =  request()->get('custom_range');
?>

<div class="card shadow-sm mt-0">
    <div class="card-body">
        <div class="row">
            <div class="col-md-2">
                <div class="form-group">
                    <label for="summarycountry">Country</label>
                    <select name="country_id" class="form-control select2" id="dashboard-country" onchange="country()">
                        <option value=""> Select country</option>

                        @if(isset($data[0]['sumemry']) && !empty($data[0]['sumemry']))
                        @foreach ($countrys as $country)
                        <option value="{{$country->country_id}}" <?php echo isset($data[0]['sumemry'][0]['country']['id']) && ($data[0]['sumemry'][0]['country']['id'] == $country->country_id) ? 'selected' : '' ?>> {{$country->country_name}}</option>
                        @endforeach
                        @else
                        <option value="">Select Country</option>
                        @endif
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <label for="summery_operator_id">Operator</label>
                <select name="operator_id" class="form-control select2" onchange="operator()" id="dashboard-operator">
                    <option value="">Operator Name</option>

                    @if(isset($data[0]['sumemry']) && !empty($data[0]['sumemry']))
                    @if(isset($operator_details) && !empty($operator_details))
                    @foreach ($operator_details['operators'] as $operator)
                    <option
                        value="{{$operator['id_operator']}}"
                        <?php echo isset($data[0]['sumemry'][0]['operator']) && (count($data) == 1) && $data[0]['sumemry'][0]['operator']['id_operator'] == $operator['id_operator'] ? 'selected' : '' ?>
                    >
                        {{ !empty($operator->display_name)?$operator->display_name:$operator->operator_name }}
                    </option>
                    @endforeach
                    @endif
                    @endif
                </select>
            </div>
            <div class="col-md-2">
                <label for="dashboardservice">Service Name</label>
                <select name="service_id" class="form-control select2" id="dashboard-service" style="width: 100%" data-select2-id="select2-data-dashboard-service" tabindex="-1" aria-hidden="true">
                    <option value="" >Service Name</option>

                    @if(isset($operator_details) && !empty($operator_details))
                    @foreach ($operator_details['services'] as $services)
                    <option value="{{ $services['id_service'] }}" data-select2-id="select2-data-{{ $services['id_service'] }}-7afa" <?php echo isset($data[0]['sumemry'][0]['selected_service']) && ($data[0]['sumemry'][0]['selected_service'] == $services['id_service']) ? 'selected' : '' ?>>{{ $services['service_name'] }}</option>
                    @endforeach
                    @endif
                </select>
            </div>
            <div class="col-lg-2">
                <label>Date Range</label>
                <select name="date_range" class="form-control select2"  id="date_range" onchange="date_ranges()" <?php echo isset($start_date) && isset($end_date) && ($start_date == date('Y-m-d')) && ($end_date == date('Y-m-d')) ? 'value="&from=' . $end_date . '&to=' . $start_date . '"' : '' ?>>
                    <option value="&from={{date('Y-m-d', strtotime( '-30 days' ) )}}&to={{date('Y-m-d')}}" <?php echo isset($start_date) && isset($end_date) && ($start_date == date('Y-m-d', strtotime('-30 days'))) && ($end_date ==  date('Y-m-d')) ? 'selected' : '' ?>>Last 30 days</option>
                    <option value="&from={{date('Y-m-d')}}&to={{date('Y-m-d')}}" <?php echo isset($start_date) && isset($end_date) && ($start_date == date('Y-m-d')) && ($end_date == date('Y-m-d')) ? 'selected' : '' ?>>Today</option>
                    <option value="&from={{date('Y-m-d', strtotime( '-1 days' ) )}}&to={{date('Y-m-d', strtotime( '-1 days' ) )}}" <?php echo isset($start_date) && isset($end_date) && ($start_date == date('Y-m-d', strtotime('-1 days'))) && ($end_date == date('Y-m-d', strtotime('-1 days'))) ? 'selected' : '' ?>>Yesterday</option>
                    <option value="&from={{date('Y-m-d', strtotime( '-6 days' ) )}}&to={{date('Y-m-d')}}" <?php echo isset($start_date) && isset($end_date) && ($start_date == date('Y-m-d', strtotime('-6 days'))) && ($end_date == date('Y-m-d')) ? 'selected' : '' ?>>Last 7 days</option>
                    <option value="&from={{date('Y-m-01', strtotime(date('Y-m-d')))}}&to={{date('Y-m-d')}}" <?php  echo isset($start_date) && isset($end_date) && ($start_date == date('Y-m-01', strtotime(date('Y-m-d')))) ? 'selected': '' ?> >This Month</option>

                    @php
                    $first_date = strtotime('first day of previous month', time());
                    $previous_month_first_date=date('Y-m-d', $first_date);
                    $previous_month_last_date=date('Y-m-d', strtotime('last day of previous month'));
                    @endphp
                    <option value="&from={{$previous_month_first_date}}&to={{$previous_month_last_date}}" <?php echo isset($start_date) && isset($end_date) && ($start_date == $previous_month_first_date) && ($end_date == $previous_month_last_date) ? 'selected' : '' ?>>Last Month</option>
                    <option value="custom_range" <?php echo isset($custom_range)? 'selected': '' ?>>Custom Range</option>
                </select>
            </div>
            <div class="col-8 row <?php echo !isset($custom_range)? 'gu-hide': '' ?>" id="date_range_faield">
                <div class="col-lg-4">
                    {{ Form::label('date', __('Form'),['class'=>'form-control-label']) }}
                    <input type="text" class="form-control date" name="date" id="dateform" data-progress-id="form" value="{{isset($start_date)?$start_date:date('Y-m-d')}}">
                </div>
                <div class="col-lg-4">
                    {{ Form::label('date', __('To'),['class'=>'form-control-label']) }}
                    <input type="text" class="form-control date" name="date" id="dateto" data-progress-id="to" value="{{isset($end_date)?$end_date:date('Y-m-d')}}">
                </div>
            </div>
            <div class="col-md-4">
                <label class="invisible d-block">Search</label>
                <button name="detailsearch" id="detailsearch" class="btn btn-primary" onclick="submit()"><i class="fa fa-search"></i> Search</button>
                <button type="submit" class="btn btn-secondary" onclick="reset()">Reset</button>
            </div>
            <div class="col-lg-2">
                <label>Data Base On</label>
                <div class="form-group">
                    <select name="data_base_on" class="form-control select2" required id="data_base_on">
                        <option value="higher_revenue_usd">Highest Revenue USD
                        </option>
                        <option value="lowest_revenue_usd">Lowest Revenue USD</option>
                        <option value="highest_reg">Highest Reg</option>
                        <option value="lowest_reg">Lowest Reg</option>
                        <option value="highest_unreg">Highest Unreg</option>
                        <option value="lowest_unreg">Lowest Unreg</option>
                    </select>
                </div>
            </div>
            <div class="col-lg-2">
                <label class="invisible d-block">Sort</label>
                <button type="button" class="btn btn-primary" id="alphBnt"><i class="fa fa-filter"></i> Filter</button>
            </div>
        </div>
        <div class="error_block" style="width: 100%;"></div>
    </div>
</div>
<script>
    var baseUrl = window.location.origin + "/";

    function country() {
        var e = document.getElementById("dashboard-country");
        var value = e.value;
        console.log(value);
        $.ajax({
            type: "POST",
            url: baseUrl + "report/operator",
            data: {
                'id': value
            },
            dataType: "json",
            success: function(responses) {
                document.getElementById('dashboard-operator').innerHTML = '<option value="">Operator Name</option>';
                $.each(responses, function(index, response) {
                    $("#dashboard-operator").append('<option value="' + response.id_operator + '">' + response.operator_name + '</option>');
                });
                document.getElementById('dashboard-service').innerHTML = '<option value="">Service Name</option>';
            },
        });
    }

    function operator() {
        var e = document.getElementById("dashboard-operator");
        var value = $('#dashboard-operator').val();
        var e = document.getElementById("dashboard-country");
        var country = e.value;
        $.ajax({
            type: "POST",
            url: baseUrl + "report/service",
            data: {
                'id': value,
                'country':country,
            },
            dataType: "json",
            success: function(responses) {
                document.getElementById('dashboard-service').innerHTML = '<option value="">Service Name</option>';
                $.each(responses, function(index, response) {
                    $("#dashboard-service").append('<option value="' + response.id_service + '">' + response.service_name + '</option>');
                });
            },
        });
    }

    function date_ranges(){
        var values = $('#date_range').val();
        if(values == 'custom_range'){date_range_faield
            $('#date_range_faield').removeClass('gu-hide');
        }else{
            $('#date_range_faield').addClass('gu-hide');
        }
    }

    function submit() {
        var country = $('#dashboard-country').val();
        var operator = $('#dashboard-operator').val();
        var service = $('#dashboard-service').val();
        var urls = window.location.origin + window.location.pathname;
        var date = $('#date_range').val();
        if (date == 'custom_range') {
            var date = '&from=' + $('#dateform').val() + '&to=' + $('#dateto').val()+'&custom_range=custom_range';
        }

        if(country != '')
        urls =urls +'?country=' + country;
        else
        urls =urls +'?';
        if(operator != '')
        urls =urls + '&operator=' + operator;
        if(service != '')
        urls =urls +  '&service=' + service;
        var url = urls    + date;
        window.location.href = url;
    }
</script>
