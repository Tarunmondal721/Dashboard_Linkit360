<?php
  $companys = App\Models\Company::orderBy('name', 'ASC')->get();
  $countrys = App\Models\Operator::select('country_name','country_id')->Status(1)->orderBy('country_name', 'ASC')->distinct()->get();
  $users = App\Models\User::select('name','id')->Types(['Owner','AccountsRoles'])->orderBy('name', 'ASC')->get();
  $operators = App\Models\Operator::Status(1)->orderBy('operator_name', 'ASC')->get();
?>

@php
  $CompanyId = request()->get('company');
  $CountryId = request()->get('country');
  $UserId = request()->get('business_manager');
  $filterOperator = request()->get('operatorId');
  $req_year = request()->get('year');
@endphp

<div class="card shadow-sm mt-0">
  <div class="card-body">
    <div class="row">
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
        <label for="summery_business_manager">Business Manager</label>
        <select name="business_manager" class="form-control select2" required id="business_manager" onchange="business_manager()" <?php echo isset($UserId) ? 'value="'.$UserId.'"': '' ?>>
          <option value="">Select Name</option>
          @foreach ($users as $user)
          <option value="{{$user->id}}" <?php  echo isset($UserId) && ($user->id == $UserId) ? 'selected': '' ?>>{{$user->name}}</option>
          @endforeach
        </select>
      </div>
      <div class="col-lg-2">
        <label for="summery_operator_id">Operator</label>
        <select name="operator" class="form-control select2" required id="operator" multiple>
          <option value="">Operator Name</option>
          @foreach ($operators as $operator)
          <option value="{{$operator->id_operator}}"<?php echo isset($filterOperator) && (in_array($operator->id_operator, $filterOperator)) ? 'selected': '' ?> >{{ !empty($operator->display_name) ? $operator->display_name : $operator->operator_name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-lg-2">
        <div class="form-group">
          <label>Year</label>
          <select class="simple-multiple-select select2" name="year" id="year" style="width: 100%">
              @foreach ($years as $year)
              <option value="{{$year}}" <?php echo ($year == $req_year) ? 'selected' : '' ?>>{{$year}}</option>
              @endforeach
          </select>
        </div>
      </div>
      <div class="col-lg-2">
        <label class="invisible d-block">Search</label>
        <button type="submit" class="btn btn-primary" onclick="submit()">Submit</button>
        <button type="submit" class="btn btn-secondary" onclick="reset()">Reset</button>
      </div>

      <div class="col-lg-2">
        <div class="form-group">
          <label>Show Data Base On:</label>
          <select name="filter_ads_on" class="form-control select2" required id="filter_ads">
              <option value="higher_revenue_usd">Highest end user revenue</option>
              <option value="lowest_revenue_usd">Lowest end user revenue</option>
              <option value="highest_reg">Highest gross revenue</option>
              <option value="lowest_reg">Lowest gross revenue</option>
              <option value="highest_unreg">Highest net revenue</option>
              <option value="lowest_unreg">Lowest net revenue</option>
          </select>
        </div>
      </div>
      <div class="col-lg-2">
        <div class="form-group">
          <label class="invisible d-block">Sort</label>
          <button type="button" class="btn btn-primary ads_submit"><i class="fa fa-filter"></i> Filter</button>
        </div>
      </div>
    </div>
    <a href="{{ route('finance.createRevenueReconcile') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Add New Revenue Reconcile </a>
  </div>
</div>
<strong><span class="target">&nbsp;&nbsp;&nbsp;&nbsp;</span> Finance Input Revenue not available</strong>
<div class="d-flex align-items-center my-3">
  <span class="badge badge-secondary px-2 bg-primary text-uppercase">Revenue Reconcile <?= isset($req_year) ? $req_year : date('Y'); ?></span>
  <span class="flex-fill ml-2 bg-primary w-100 font-weight-bold" style="height: 1px;"></span>
  <div class="text-right pl-2">
    <button class="btn btn-sm pnl-xls" style="color:white; background-color:green" data-param="container"><i class="fa fa-file-excel-o"></i>Export XLS</button>
  </div>
</div>

<script>
  $( document ).ready(function() {
    setTimeout(<?php echo isset($CompanyId)?'country': 'pp' ?>, 100);
    setTimeout(<?php echo ($CountryId != null)?'operatorselect': 'pp' ?>, 1000);
  });

  function operatorselect(){
    operator(<?php echo isset($CountryId)? $CountryId: '' ?>);
  };

  const filterOperator = <?php echo isset($filterOperator)?json_encode($filterOperator): '[-1]'; ?>;
  var countryid = <?php echo !empty($CountryId)?$CountryId: '[-1]' ?>;
  var userid = <?php echo !empty($UserId)?$UserId: '[-1]' ?>;
  var baseUrl = window.location.origin + "/";

  function country(){
    var e = document.getElementById("company");
    var value = e.value;
    
    var newHTML = [];

    $.ajax({
      type: "POST",
      url: baseUrl+"finance/user/filter/country",
      data: {'id':value},
      dataType: "json",
      success: function (responses) {
        document.getElementById('country').innerHTML = '<option value="">Country Name</option>';
        document.getElementById('operator').innerHTML = '<option value="">Operator Name</option>';
        document.getElementById('business_manager').innerHTML = '<option value="">Select Name</option>';

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

        $.each(responses.users, function(index,response){
          if(userid == response.user.id){
            $("#business_manager").append('<option value="'+response.user.id+'" selected>'+response.user.name+'</option>');
          }else{
            $("#business_manager").append('<option value="'+response.user.id+'">'+response.user.name+'</option>');
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
      url: baseUrl+"finance/user/filter/operator",
      data: {'id':value,'company':company},
      dataType: "json",
      success: function (responses) {
        document.getElementById('operator').innerHTML = '<option value="">Operator Name</option>';
        document.getElementById('business_manager').innerHTML = '<option value="">Select Name</option>';

        $.each(responses.operators, function(index,response){
          if (filterOperator.indexOf(response.id_operator.toString())>-1 ) {
            $("#operator").append('<option value="'+response.id_operator+'" selected>'+response.operator_name+'</option>');
          }
          else {
            $("#operator").append('<option value="'+response.id_operator+'" >'+response.operator_name+'</option>');
          }
        });

        $.each(responses.users, function(index,response){
          if(userid == response.user.id){
            $("#business_manager").append('<option value="'+response.user.id+'" selected>'+response.user.name+'</option>');
          }else{
            $("#business_manager").append('<option value="'+response.user.id+'">'+response.user.name+'</option>');
          }
        });
      },
    });
  }

  function business_manager(){
    var e = document.getElementById("country");
    var country = e.value;
    var e = document.getElementById("company");
    var company = e.value;
    var e = document.getElementById("business_manager");
    var business_manager = e.value;

    console.log(business_manager);
    $.ajax({
      type: "POST",
      url: baseUrl+"finance/user/filter/business/operator",
      data:{'country':country,'company':company,'business_manager':business_manager},
      dataType: "json",
      success: function (responses) {
        document.getElementById('operator').innerHTML ='<option value="">Operator Name</option>';
        $.each(responses, function(index,response){
            // $("#operator").append('<option value="'+response.id_operator+'">'+response.operator_name+'</option>');
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
    var e = document.getElementById("company");
    var company = e.value;

    var e = document.getElementById("country");
    var country = e.value;

    var e = document.getElementById("business_manager");
    var business_manager = e.value;

    var operators = $('#operator').val();

    var e = document.getElementById("year");
    var year = e.value;

    var urls = window.location.origin+'/finance/revenueReconcile';

    var operatorurl = '';

    if(operators.length>0){
      $.each(operators, function(index,operator){
        operatorurl = operatorurl+'&operatorId[]='+operator;
      });
    }else{
      operatorurl = '';
    }

    if(company != ''){
      urls = urls +'?company='+ company;
    }else{
      urls = urls +'?';
    }

    if(country != ''){
      urls = urls +'&country='+ country;
    }

    if(business_manager != ''){
      urls = urls +'&business_manager='+ business_manager;
    }

    var url = urls + operatorurl + '&year='+year;
    window.location.href = url;
  }

</script>
