
<?php $countrys= App\Models\Operator::select('country_name','country_id')->Status(1)->orderBy('country_name', 'ASC')->distinct()->get();?>
<?php $companys= App\Models\Company::orderBy('name', 'ASC')->get();?>
<?php $operators= App\Models\Operator::Status(1)->orderBy('operator_name', 'ASC')->get();?>
                @php
                    $CompanyId = request()->get('company');
                    $CountryId = request()->get('country');
                    $filterOperator = request()->get('operatorId');
                    $report_type = request()->segment(count(request()->segments()));
                    $business_type = request()->get('business_type');
                @endphp
    <div class="card shadow-sm mt-0">
      <div class="card-body">
        <div class="row">
          <div class="col-lg-2">
            <div class="form-group">
                <label>Report Type</label>
                <select name="report_type" class="form-control select2" required id="report_type" <?php echo isset($report_type) ? 'value="'.$report_type.'"': '' ?> >
                  <option value="country" <?php echo isset($report_type) && ($report_type =='country') ? 'selected': '' ?>>Country Based</option>
                  <option value="operator" <?php echo isset($report_type) && ($report_type =='operator') ? 'selected': '' ?> >Operator Based</option>
                  <option value="company" <?php echo isset($report_type) && ($report_type =='company') ? 'selected': '' ?> >Company Based</option>
                  <option value="business" <?php echo isset($report_type) && ($report_type =='business') ? 'selected': '' ?> >Business Type Based</option>
                </select>
            </div>
          </div>
          <div class="col-lg-2">
            <div class="form-group">
              <label for="company">Company</label>
              <select class="form-control select2" id="company"
                name="company" onchange="country()" <?php echo isset($CompanyId) ? 'value="'.$CompanyId.'"': '' ?>  >
                <option value="" selected>Select Company</option>
                    <option value="allcompany"  <?php echo isset($CompanyId) && ($CompanyId == 'allcompany') ? 'selected': 'selected' ?> >All Company</option>
                @foreach ($companys as $company)
                    <option value="{{$company->id}}" <?php echo isset($CompanyId) && ($CompanyId == $company->id) ? 'selected': '' ?>>{{$company->name}}</option>
                @endforeach

              </select>

            </div>
          </div>
          <div class="col-lg-2">
            <div class="form-group">
              <label for="country">Country</label>
              <select class="form-control select2" name="country" id="country" onchange="operator()" <?php echo isset($CountryId) ? 'value="'.$CountryId.'"': '' ?> >
                <option value="" selected >Country Name</option>
                @foreach ($countrys as $country)
                    <option value="{{$country->country_id}}" <?php echo isset($CountryId) && ($CountryId == $country->country_id) ? 'selected': '' ?>>{{$country->country_name}}</option>
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
          <div class="col-lg-3">
            <label for="operator">Operator</label>
            <select class="form-control select2 " name="operator" id="operator" multiple>
              <option value="">Operator Name</option>
              @foreach ($operators as $operator)
                    <option value="{{$operator->id_operator}}"<?php echo isset($filterOperator) && (in_array($operator->id_operator, $filterOperator)) ? 'selected': '' ?> >{{ !empty($operator->display_name)?$operator->display_name:$operator->operator_name }}</option>
                @endforeach
            </select>

          </div>
            <div class="col-lg-3">
                <label class="invisible d-block">Button</label>
                <button type="button" class="btn btn-primary" onclick="submit()">Submit</button>
                <button type="submit" class="btn btn-secondary" onclick="reset()">Reset</button>
            </div>
          <div class="col-lg-2">
            <label>Data Base On</label>
            <div class="form-group">
              <select class="simple-multiple-select select2" name="sorting_company_dash"
                id="filter_dashboard" style="width: 100%" data-select2-id="select2-data-filter_dashboard"
                tabindex="-1" aria-hidden="true">
                {{-- <option value="">Select Sorting Data</option> --}}
                <option value="higher_revenue" data-select2-id="select2-data-10-syxz">Highest Revenue</option>
                <option value="lowest_revenue" data-select2-id="select2-data-15-p1be">Lowest Revenue</option>
                <option value="highest_mo" data-select2-id="select2-data-16-kl7s">Highest Mo</option>
                <option value="lowest_mo" data-select2-id="select2-data-17-p484">Lowest Mo</option>
                <option value="highest_cost_campaign" data-select2-id="select2-data-18-2ki6">Highest Cost Campaign
                </option>
                <option value="lowest_cost_campaign" data-select2-id="select2-data-19-ze5h">Lowest Cost Campaign
                </option>
                <option value="highest_pnl" data-select2-id="select2-data-20-pzzr">Highest Pnl</option>
                <option value="lowest_pnl" data-select2-id="select2-data-21-91f3">Lowest Pnl</option>
              </select>

            </div>
          </div>
          <div class="col-lg-2">
            <label class="invisible d-block">Sort</label>
            <button type="button" class="btn btn-primary" id="filterBnt"><i class="fa fa-filter"></i>
              Filter</button>
          </div>
          <div class="col-lg-3">

          </div>

        </div>
        <div class="error_block"></div>
      </div>
    </div>
    <div class="text-right pl-2">
      <button type="button" class="btn btn-sm detail-download-xls2" style="color:white; background-color:green;"><i
          class="fa fa-file-excel-o"></i>Export as XLS</button>
    </div>

    <script>

                    const filterOperator = <?php echo isset($filterOperator)?json_encode($filterOperator): 'null'; ?>;
                    var countryid = <?php echo !empty($CountryId)?$CountryId: 'null' ?>;
                    $( document ).ready(function() {
                        setTimeout(<?php echo isset($CompanyId)?'country': 'pp' ?>, 100);
                        setTimeout(<?php echo ($CountryId != null)?'operatorselect': 'pp' ?>, 1000);
                    });
                    function operatorselect(){
                        operator(<?php echo isset($CountryId)? $CountryId: '' ?>);
                    };
                var baseUrl = window.location.origin + "/";
                function country(){
                    var e = document.getElementById("company");
                    var value = e.value;

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
                    var e = document.getElementById("company");
                    var company = e.value;
                    if(id != null){
                            value=id;
                        }
                    console.log(value);
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
                function business_type(){
                    var e = document.getElementById("country");
                    var country = e.value;
                    var e = document.getElementById("company");
                    var company = e.value;
                    var e = document.getElementById("business_type");
                    var business_type = e.value;

                    console.log(business_type);
                    $.ajax({
                        type: "POST",
                        url: baseUrl+"report/user/filter/business/operator",
                        data:{'country':country,'company':company,'business_type':business_type},
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

                    var report_type = $('#report_type').val();
                    var company = $('#company').val();
                    var country = $('#country').val();
                    var business_type = $('#business_type').val();
                    var operators = $('#operator').val();
                    var urls= window.location.origin+'/dashboard';

                    // console.log(operators); return false;

                    var operatorurl = '';
                    if(operators != undefined && operators != '')
                    {
                        if(operators.length > 0){
                          $.each(operators, function(index,operator){
                            operatorurl = operatorurl+'&operatorId[]='+operator;
                          });
                        }else{
                          operatorurl='';
                        }
                    }

                        urls=urls  +'/'+report_type;

                    // console.log(urls);
                    if(company != ''){

                        urls=urls  +'?company='+company;
                    }else{
                        urls=urls  +'?';
                    }
                    if(country != ''){
                        urls=urls  +'&country='+country;
                    }

                    if(business_type != ''){
                        urls=urls  +'&business_type='+business_type;
                    }
                    // if(date != ''){
                    //     urls=urls +date;
                    // }
                    var url=urls  +operatorurl;
                    window.location.href =url;
                    // var url = urls+report_type+'?company='+company+'&country='+country+operatorurl;
                    // window.location.href = url;
                }
            </script>
