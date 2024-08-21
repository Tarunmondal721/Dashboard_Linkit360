<?php
  $companys = App\Models\Company::orderBy('name', 'ASC')->get();
  $countrys = App\Models\Operator::select('country_name','country_id')->Status(1)->orderBy('country_name', 'ASC')->distinct()->get();
  $operators = App\Models\Operator::Status(1)->orderBy('operator_name', 'ASC')->get();
?>

@php
  $CompanyId = request()->get('company');
  $CountryId = request()->get('country');
  $business_type = request()->get('business_type');
  $filterOperator = request()->get('operatorId');
  $start_date = request()->get('from');
  $end_date = request()->get('to');
  $custom_range = request()->get('custom_range');
  $user = Auth::user();
  $user_type = $user->type;
  $allowAllOperator = $user->WhowAccessAlOperator($user_type);
@endphp

<div class="card shadow-sm mt-0">
  <div class="card-body">
    <div class="row">
      <div class="col-lg-2">
        <label>Report Type</label>
        <select name="report_type" class="form-control select2" required id="report_type">
          <option value="operator" selected="" data-select2-id="select2-data-4-abx5">Operator Summary</option>
          <option value="country" <?php echo isset($CountryWise) ? 'selected': '' ?>>Country Summary</option>
        </select>
      </div>

      <div class="col-lg-2">
        <div class="form-group">
          <label for="summarycompany">Company</label>
          <select name="company" class="form-control select2" required id="company" onchange="country()" <?php echo isset($CompanyId) ? 'value="'.$CompanyId.'"': '' ?>>
            <option value=""  selected>Select Company</option>
            <option value="allcompany"<?php  echo isset($CompanyId) && ("allcompany" == $CompanyId) ? 'selected': 'selected' ?> >All Company</option>
            @foreach ($companys as $company)
            <option value="{{$company->id}}" <?php  echo isset($CompanyId) && ($company->id == $CompanyId) ? 'selected': '' ?>>{{$company->name}}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="col-lg-2">
        <div class="form-group">
          <label for="summarycountry">Country</label>
          <select name="country" class="form-control select2" id="country" onchange="operator()" <?php echo isset($CountryId) ? 'value="'.$CountryId.'"': '' ?>>
            <option value="">Country Name</option>
            @foreach ($countrys as $country)
            <option value="{{$country->country_id}}" <?php  echo isset($CountryId) && ($country->country_id == $CountryId) ? 'selected': '' ?>>{{$country->country_name}}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="col-lg-2">
        <div class="form-group">
          <label for="business_type">Business Type</label>
          <select name="business_type" class="form-control select2" required id="business_type" onchange="business_type()" <?php echo isset($business_type) ? 'value="'.$business_type.'"': '' ?> >
            <option value="" selected>Select Business type</option>
            <option value="digital" <?php echo isset($business_type) && ($business_type =='digital') ? 'selected': '' ?>>digital</option>
            <option value="ott" <?php echo isset($business_type) && ($business_type =='ott') ? 'selected': '' ?> >Ott</option>
            <option value="saas" <?php echo isset($business_type) && ($business_type =='saas') ? 'selected': '' ?> >Saas</option>
            <option value="service" <?php echo isset($business_type) && ($business_type =='service') ? 'selected': '' ?> >Service</option>
            <option value="saas_payment" <?php echo isset($business_type) && ($business_type =='saas_payment') ? 'selected': '' ?>>SaaS Payment</option>
            <option value="saas_music" <?php echo isset($business_type) && ($business_type =='saas_music') ? 'selected': '' ?>>SaaS Music</option>
            <option value="saas_gift" <?php echo isset($business_type) && ($business_type =='saas_gift') ? 'selected': '' ?>>SaaS Gift</option>
          </select>
        </div>
      </div>

      <div class="col-lg-2">
        <label for="summery_operator_id">Operator</label>
        <select name="operator" class="form-control select2" required id="operator" multiple >
          <option value="">Operator Name</option>
          @foreach ($operators as $operator)
          <option value="{{$operator->id_operator}}"<?php echo isset($filterOperator) && (in_array($operator->id_operator, $filterOperator)) ? 'selected': '' ?> >{{ !empty($operator->display_name) ? $operator->display_name : $operator->operator_name }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-lg-2">
        <label>Date Range</label>
        <select name="date_range" class="form-control select2" required id="date_range" onchange="date_ranges()"  <?php  echo isset($start_date) && isset($end_date) && ($start_date == date('Y-m-d')) &&($end_date == date('Y-m-d')) ? 'value="&from='.$end_date.'&to='.$start_date.'"': '' ?> >
          <option value="">Select Date</option>
          <option value="&from={{date('Y-m-d')}}&to={{date('Y-m-d')}}" <?php  echo isset($start_date) && isset($end_date) && ($start_date == date('Y-m-d')) &&($end_date == date('Y-m-d')) ? 'selected': '' ?> >Today</option>
          <option value="&from={{date('Y-m-d', strtotime( '-1 days' ) )}}&to={{date('Y-m-d', strtotime( '-1 days' ) )}}" <?php  echo isset($start_date) && isset($end_date) && ($start_date == date('Y-m-d', strtotime( '-1 days' ))) &&($end_date == date('Y-m-d', strtotime( '-1 days' ) )) ? 'selected': '' ?> >Yesterday</option>
          <option value="&from={{date('Y-m-d', strtotime( '-6 days' ) )}}&to={{date('Y-m-d')}}" <?php  echo isset($start_date) && isset($end_date) && ($start_date == date('Y-m-d', strtotime( '-6 days' ) )) &&($end_date == date('Y-m-d')) ? 'selected': '' ?> >Last 7 days</option>
          <option value="&from={{date('Y-m-d', strtotime( '-30 days' ) )}}&to={{date('Y-m-d')}}" <?php  echo isset($start_date) && isset($end_date) && ($start_date == date('Y-m-d', strtotime( '-30 days' ) )) &&($end_date ==  date('Y-m-d') ) ? 'selected': '' ?> >Last 30 days</option>
          <option value="&from={{date('Y-m-01', strtotime(date('Y-m-d')))}}&to={{date('Y-m-d')}}" <?php  echo isset($start_date) && isset($end_date) && ($start_date == date('Y-m-01', strtotime(date('Y-m-d')))) ? 'selected': '' ?> >This Month</option>

          @php
            $first_date = strtotime('first day of previous month', time());
            $previous_month_first_date=date('Y-m-d', $first_date);
            $previous_month_last_date=date('Y-m-d', strtotime('last day of previous month'));
          @endphp
          <option value="&from={{$previous_month_first_date}}&to={{$previous_month_last_date}}" <?php  echo isset($start_date) && isset($end_date) && ($start_date == $previous_month_first_date) &&($end_date == $previous_month_last_date) ? 'selected': '' ?> >Last Month</option>
          <option value="custom_range" <?php echo isset($custom_range)? 'selected': '' ?>>Custom Range</option>
        </select>
      </div>

      <div class="col-6 row <?php echo !isset($custom_range)? 'gu-hide': '' ?>" id="date_range_faield">
        <div class="col-lg-4">
          {{ Form::label('date', __('Form'),['class'=>'form-control-label' ]) }}
          <input type="text" class="form-control date" name="date" id="dateform" data-progress-id="form" value="{{isset($start_date)?$start_date:date('Y-m-d')}}">
        </div>
        <div class="col-lg-4">
          {{ Form::label('date', __('To'),['class'=>'form-control-label']) }}
          <input type="text" class="form-control date" name="date" id="dateto" data-progress-id="to" value="{{isset($end_date)?$end_date:date('Y-m-d')}}">
        </div>
      </div>

      <div class="error_block"></div>

      <div class="col-lg-3">
        <label class="invisible d-block">Search</label>
        <button type="submit" class="btn btn-primary"  onclick="submit()">Submit</button>
        <button type="submit" class="btn btn-secondary" onclick="reset()">Reset</button>
      </div>

      <div class="col-lg-2">
        <label>Data Base On</label>
        <div class="form-group">
          <select class="simple-multiple-select select2" name="data_base_on" id="data_base">
            <option value="highest_cost_campaign">Highest Cost Campaign</option>
            <option value="lowest_cost_campaign">Lowest Cost Campaign</option> 
          </select>
        </div>
      </div>

      <div class="col-lg-2">
        <label class="invisible d-block">Sort</label>
        <button type="button" class="btn btn-primary pnl_submit"><i class="fa fa-sort"></i> Sort</button>
      </div>
    </div>
    <a href="{{ route('finance.createReconcialiation') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Add New Reconcialiation</a>
  </div>
</div>

<script>
  function date_ranges(){
    var values = $('#date_range').val();

    if(values == 'custom_range'){date_range_faield
      $('#date_range_faield').removeClass('gu-hide');    
    }else{
      $('#date_range_faield').addClass('gu-hide');
    }
  }

  $( document ).ready(function() {
    setTimeout(<?php echo isset($CompanyId)?'country': 'pp' ?>, 100);
    setTimeout(<?php echo ($CountryId != null)?'operatorselect': 'pp' ?>, 1000);
  });

  function operatorselect(){
    operator(<?php echo isset($CountryId)? $CountryId: '' ?>);
  };

  const filterOperator = <?php echo isset($filterOperator)?json_encode($filterOperator): '[-1]'; ?>;
  var countryid = <?php echo !empty($CountryId)?$CountryId: '[-1]' ?>;
  var baseUrl = window.location.origin + "/";

  function country(){
    var e = document.getElementById("company");
    var value = e.value;
    
    var newHTML = [];

    $.ajax({
      type: "POST",
      url: baseUrl+"report/user/filter/country",
      data: {'id':value},
      dataType: "json",
      success: function (responses) {
        document.getElementById('country').innerHTML = '<option value="">Country Name</option>';
        document.getElementById('operator').innerHTML = '<option value="">Operator Name</option>';

        $.each(responses.countrys, function(index,response){
          if(countryid == response.country_id){
            $("#country").append('<option value="'+response.country_id+'" selected>'+response.country_name+'</option>');
          }else{
            $("#country").append('<option value="'+response.country_id+'">'+response.country_name+'</option>');
          }
        });

        $.each(responses.operators, function(index,response){
          if (filterOperator.indexOf(response.id_operator.toString())>-1 ) {
            $("#operator").append('<option value="'+response.id_operator+'" selected>'+response.operator_name+'</option>');
          }
          else {
            $("#operator").append('<option value="'+response.id_operator+'"  >'+response.operator_name+'</option>');
          }
        });
      },
    });
  }

  function operator(id){
    var e = document.getElementById("country");
    var value = e.value;

    if(id != null){
      value = id;
    }
    
    var e = document.getElementById("company");
    var company = e.value;

    $.ajax({
      type: "POST",
      url: baseUrl+"report/user/filter/operator",
      data: {'id':value,'company':company},
      dataType: "json",
      success: function (responses) {
        document.getElementById('operator').innerHTML = '<option value="">Operator Name</option>';

        $.each(responses, function(index,response){
          if (filterOperator.indexOf(response.id_operator.toString())>-1 ) {
            $("#operator").append('<option value="'+response.id_operator+'" selected>'+response.operator_name+'</option>');
          }
          else {
            $("#operator").append('<option value="'+response.id_operator+'" >'+response.operator_name+'</option>');
          }
        });
      },
    });
  }

  function business_type(){
    var e = document.getElementById("country");
    var country = e.value;
    var e = document.getElementById("company");
    var company = e.value;
    var e = document.getElementById("business_type");
    var business_type = e.value;

    $.ajax({
      type: "POST",
      url: baseUrl+"report/user/filter/business/operator",
      data:{'country':country,'company':company,'business_type':business_type},
      dataType: "json",
      success: function (responses) {
        document.getElementById('operator').innerHTML = '<option value="">Operator Name</option>';

        $.each(responses, function(index,response){
          if ( filterOperator != null && filterOperator.indexOf(response.id_operator.toString())>-1 ) {
            $("#operator").append('<option value="'+response.id_operator+'" selected>'+response.operator_name+'</option>');
          }
          else {
            $("#operator").append('<option value="'+response.id_operator+'" >'+response.operator_name+'</option>');
          }
        });
      },
    });
  }

  function submit(){
    var e = document.getElementById("report_type");
    var report_type = e.value;

    var e = document.getElementById("company");
    var company = e.value;

    var e = document.getElementById("country");
    var country = e.value;

    var e = document.getElementById("business_type");
    var business_type = e.value;

    var operators = $('#operator').val();

    let arr = window.location.pathname.split('/');
    var urls = window.location.origin+'/'+arr[1]+'/'+arr[2];

    var date = $('#date_range').val();

    var operatorurl = '';

    if(operators.length>0){
      $.each(operators, function(index,operator){
        operatorurl = operatorurl+'&operatorId[]='+operator;
      });
    }else{
      operatorurl = '';
    }

    if(date == 'custom_range'){
      var date = '&from='+$('#dateform').val() +'&to='+$('#dateto').val()+'&custom_range=custom_range';
    }

    urls = urls +'/'+ report_type;

    if(company != ''){
      urls = urls +'?company='+ company;
    }else{
      urls = urls +'?';
    }

    if(country != ''){
      urls = urls +'&country='+ country;
    }

    if(business_type != ''){
      urls = urls +'&business_type='+ business_type;
    }
    
    if(date != ''){
      urls = urls + date;
    }

    var url = urls + operatorurl;
    window.location.href = url;
  }
     
  $('#excelDownload').submit(function(e){
    e.preventDefault();
    var params = window.location.search;
    console.log(params);
    var path = baseUrl+'report/exportExcel'+params;
    console.log(path);
    window.location.replace(path);
  });
</script>
