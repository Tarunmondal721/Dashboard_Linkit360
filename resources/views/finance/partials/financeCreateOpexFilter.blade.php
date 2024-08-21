<?php
$companys = App\Models\Company::orderBy('name', 'ASC')->get();
?>
@php
  $CompanyId = request()->get('company');
@endphp

<div class="card shadow-sm mt-0">
  <div class="card-body">
      <div class="row">
        <div class="col-lg-2">
          <div class="form-group">
            <label>Year</label>
            <select class="simple-multiple-select select2" name="year" id="year" style="width:100%">
              <option value="">Select Year</option>
              @foreach ($data['years'] as $year)
                <option value="{{$year}}" <?php echo ($year == $data['year']) ? 'selected' : '' ?> >{{$year}}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="col-lg-2">
          <div class="form-group">
            <label for="company">Company</label>
            <select name="company" class="form-control select2" required id="company" <?php echo isset($CompanyId) ? 'value="'.$CompanyId.'"': '' ?>>
              <option value=""  selected>Select Company</option>
              @foreach ($companys as $company)
              <option value="{{$company->id}}" <?php  echo isset($CompanyId) && ($company->id == $CompanyId) ? 'selected': '' ?>>{{$company->name}}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="col-lg-2">
          <label class="invisible d-block">Submit</label>
          <button type="submit" class="btn btn-primary" onclick="submit()">Submit</button>
          <button type="submit" class="btn btn-secondary" onclick="reset()">Reset</button>
        </div>
      </div>
  </div>
</div>

<script>
const filterOperator = <?php echo isset($filterOperator)?json_encode($filterOperator): 'null'; ?>;
  function country(){
    var value = '';

    $.ajax({
        type: "POST",
        url: baseUrl+"report/user/filter/country",
        data:{'id':value},
        dataType: "json",
        success: function (responses) {
            document.getElementById('country').innerHTML ='<option value="">Country Name</option>';
            document.getElementById('operator').innerHTML ='<option value="">Operator Name</option>';
            $.each(responses.countrys, function(index,response){
                // $("#country").append('<option value="'+response.country_id+'">'+response.country_name+'</option>');
                if(countryid == response.country_id){
                    $("#country").append('<option value="'+response.country_id+'" selected>'+response.country_name+'</option>');
                }else{
                    $("#country").append('<option value="'+response.country_id+'">'+response.country_name+'</option>');
                }
            });
            $.each(responses.operators, function(index,response){
                // $("#operator").append('<option value="'+response.id_operator+'">'+response.operator_name+'</option>');
                if (filterOperator != null && filterOperator.indexOf(response.id_operator.toString())>-1 ) {
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
      var company = 'allcompany';
      if(id != null){
              value=id;
          }
      console.log(filterOperator);
      $.ajax({
          type: "POST",
          url: baseUrl+"report/user/filter/operator",
          data:{'id':value,'company':company},
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

  function service() {
    var e = document.getElementById("operator");
    var value = $('#operator').val();
    var e = document.getElementById("country");
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
            document.getElementById('service').innerHTML = '<option value="">Service Name</option>';
            $.each(responses, function(index, response) {
                $("#service").append('<option value="' + response.id_service + '">' + response.service_name + '</option>');
            });
        },
    });
  }

  function submit(){
      var e = document.getElementById("year");
      var year = e.value;

      var e = document.getElementById("company");
      var company = e.value;

      var urls= window.location.href;

      var url = urls+'?year='+year+'&company='+company;
      window.location.href = url;
  }

</script>