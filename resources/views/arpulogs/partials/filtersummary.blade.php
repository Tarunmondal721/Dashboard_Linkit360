
@php
    $CountryId = request()->get('country');
    $filterOperator = request()->get('operatorId');
    $from = request()->get('from_date');
    $to = request()->get('to_date');

@endphp
<div class="page-title" style="margin-bottom:25px">
    <div class="row justify-content-between align-items-center">
        <div class="col-xl-4 col-lg-4 col-md-4 d-flex align-items-center justify-content-between justify-content-md-start mb-3 mb-md-0">
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
            <div class="col-md-4">
                <label for="">Country</label>
                <div class="form-group">
                    <select class="form-control select2" name="data_type" id="country" style="width: 100%" data-select2-id="select2-data-1-qgws" tabindex="-1" aria-hidden="true" onchange="operatorfind()"  <?php echo isset($CountryId) ? 'value="'.$CountryId.'"': '' ?>>
                        <option value=""  selected>Select Country</option>
                        @foreach ($allCountries as $country)
                            <option value="{{$country}}" {{Request::get("country") == $country ? "selected" : ""}}>{{$country}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="ads_ctry_name">Operator</label>
                    <select class="form-control select2" name="country_id" id="operator" style="width: 100%" data-select2-id="select2-data-dashboard-country" tabindex="-1" aria-hidden="true"   multiple>
                        <option value="" >Select Operator</option>
                        @foreach ($operators as $operator)
                            <option value="{{$operator}}" <?php echo isset($filterOperator) && (in_array($operator, $filterOperator)) ? 'selected': '' ?>>{{$operator}}</option>
                        
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="from_date">From</label>
                    <input type="date" class="  form-control " name="from_date" id="from_date"
                    data-progress-id="form" placeholder="YYYY-MM-DD" value="{{isset($from) ? $from:''}}" >
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="to_date">To</label>
                    <input type="date" class="  form-control " name="to_date" id="to_date"
                    data-progress-id="form" placeholder="YYYY-MM-DD" value="{{isset($to) ? $to:''}}" >
                </div>
            </div>
            <div class="col-md-3">
                <label class="invisible d-block">Search</label>
                <div class="btn btn-primary" onclick="submit()"><i class="fa fa-search"></i> Search</div>
                <a class="btn btn-secondary" onclick="reset()">Reset</a>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mt-0">
    <div class="card-body">
        <div class="row">
            <div class="col-lg-3">
                <label>Show Data Base On:</label>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <select name="filter_ads_summary" class="form-control select2" required id="filter_ads_summary">
                        <option value="highest_transaction">Highest Transaction</option>
                        <option value="lowest_transaction">Lowest Transaction</option>
                        <option value="highest_subscription">Highest Subscription</option>
                        <option value="lowesr_subscription">Lowest Subscription</option>
                    </select>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <button type="button" class="btn btn-primary adsSummary"><i class="fa fa-sort"></i>Sort</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function operatorfind(id){
        var value = $('#country').val();
        
        console.log(value);
        $("#operator").empty();
        $("#operator").append('<option value="" >'+"Select Operator"+'</option>');
        // $arrayCountries = ['Philippines', 'Laos', 'Thailand', 'Oman', 'Indonesia', "Jordan", "Sri Lanka", "Bahrain"];
        var responses = [];
        if(value=="Philippines") {
            responses = ["smart"];
        }else if(value == "Laos") {
            responses = ["tplus", "ltc", "etl"];
        }else if(value == "Thailand") {
            responses = ["ais", "aisgemezz", "dtac"];
        }else if(value == "Oman") {
            responses = ["omantel", "ooredo"];
        }else if(value == "Indonesia") {
            responses = ["telkomsel", "id-telkomsel-mks", "telesatpass"];
        }else if(value == "Jordan") {
            responses = ["umniah"];
        }else if(value == "Sri Lanka") {
            responses = ["dialog"];
        }else if(value == "Bahrain") {
            responses = ["batelco", "stc"];
        }
        $.each(responses, function(index,response){
            $("#operator").append('<option value="'+response+'">'+response+'</option>');
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

    function submit(){
        var country = $('#country').val();
        var fromDate = $('#from_date').val();
        var toDate = $('#to_date').val();
        var operators = $('#operator').val();
        console.log(operators)
        var orgurl = window.location.pathname;
        let arrurl = orgurl.split('/');
        var urls = window.location.origin+'/'+arrurl[1]+'/'+arrurl[2];
        var operatorurl = '';
        if(operators.length > 0){
            $.each(operators, function(index,operator){
                operatorurl = operatorurl+'&operatorId[]='+operator;
            });
        }else{
            operatorurl = '';
        }

        var url = urls+'?country='+country+operatorurl+'&from_date='+fromDate+'&to_date='+toDate;
        window.location.href = url;

    }
</script>
