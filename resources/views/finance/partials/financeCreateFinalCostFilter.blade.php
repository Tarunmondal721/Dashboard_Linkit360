<?php $countrys= App\Models\Operator::select('country_name','country_id')->Status(1)->orderBy('country_name', 'ASC')->distinct()->get();?>
<?php $operators= App\Models\Operator::Status(1)->orderBy('operator_name', 'ASC')->get();?>
@php
  $CountryId = request()->get('country');
  $filterOperator = request()->get('operator');
@endphp

<div class="card shadow-sm mt-0">
  <div class="card-body">
      <div class="row">
        <div class="col-lg-3">
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
        <div class="col-lg-3">
            <div class="form-group">
              <label for="country">Country</label>
              <select class="form-control select2" name="country" id="country" onchange="operator()" <?php echo isset($CountryId) ? 'value="'.$CountryId.'"': '' ?> required >
                <option value="">Country Name</option>
                @foreach ($countrys as $country)
                    <option value="{{$country->country_id}}" <?php echo isset($CountryId) && ($CountryId == $country->country_id) ? 'selected': '' ?>>{{$country->country_name}}</option>
                @endforeach
              </select>

            </div>
          </div>
          <div class="col-lg-3">
            <label for="operator">Operator</label>
            <select class="form-control select2 " name="operator" id="operator" required>
              <option value="">Operator Name</option>
              @foreach ($operators as $operator)
                    <option value="{{$operator->id_operator}}"<?php echo isset($filterOperator) && ($operator->id_operator == $filterOperator) ? 'selected': '' ?> >{{ !empty($operator->display_name)?$operator->display_name:$operator->operator_name }}</option>
                @endforeach
            </select>

          </div>
        <div class="col-lg-3">
          <label class="invisible d-block">Submit</label>
          <button type="submit" class="btn btn-secondary" onclick="submit()">Submit</button>
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
      var company = '';
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

  function submit(){
      var e = document.getElementById("year");
      var year = e.value;

      var e = document.getElementById("country");
      var country = e.value;

      var e = document.getElementById("operator");
      var operator = e.value;

      var urls= window.location.href;

      var url = urls+'?year='+year+'&country='+country+'&operator='+operator;
      window.location.href = url;
  }

</script>