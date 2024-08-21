<!-- <form action="/report/summary" id="summaryForm"> -->
<?php $companys= App\Models\Company::get();?>
        <div class="card shadow-sm mt-0">
          <div class="card-body">
            <div class="row">
              <div class="col-lg-4">
                <label>Data Type</label>
                 <select name="role" class="form-control select2" required id="role">
                    <option value="">{{__('Select Role')}}</option>

                        <option value="1">fffff</option>
                        <option value="1">fffff</option>
                        <option value="1">fffff</option>
                        <option value="1">fffff</option>
                        <option value="1">fffff</option>
                        <option value="1">fffff</option>
                        <option value="1">fffff</option>

                </select>
              </div>

              <div class="col-lg-4">
                <label>Report Type</label>
                <select name="report_type" class="form-control select2" required id="report_type">
                  <option value="operator" selected="" data-select2-id="select2-data-4-abx5">Operator Summary</option>
                  <option value="country">Country Summary</option>
                  <option value="ac manager">Account Manager's Summary</option>
                </select>
              </div>
              <div class="col-lg-4">
                <div class="form-group">
                  <label for="summarycompany">Company</label>
                 <select name="company" class="form-control select2" required id="company" onchange="country()">
                    <option value="" data-select2-id="select2-data-6-fj9l">All Company</option>
                    @foreach ($companys as $company)
                    <option value="{{$company->id}}">{{$company->name}}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="col-lg-4">
                <div class="form-group">
                  <label for="summarycountry">Country</label>
                  <select name="country" class="form-control select2" required id="country" onchange="operator()">
                    <option value="">Country Name</option>
                  </select>

                </div>
              </div>
              <div class="col-lg-4">
                <label for="summery_operator_id">Operator</label>
               <select name="operator" class="form-control select2" required id="operator" multiple >
                  <option value="">Operator Name</option>
                </select>
              </div>
            </div>
            <div class="error_block"></div>
            <!-- </form> -->
            <div class="row mb-4">
              <div class="col-lg-3">
                <label class="invisible d-block">To</label>
                <input class="form-control form_datetime1" type="hidden" name="pnl_to_datepicker" id="pnl_to_datepicker"
                  value="2022-10-31">

                <input type="hidden" id="hiddenFrm" value="2022-10-01">
                <input type="hidden" id="hiddenTo" value="2022-10-31">

                <input type="text" name="date_analytics_picker" id="date_analytics_picker" value="" oct="" 01,=""
                  2022-oct="" 31,="" 2022""="" style="display: none;"><button type="button"
                  class="comiseo-daterangepicker-triggerbutton ui-button ui-corner-all ui-widget comiseo-daterangepicker-bottom comiseo-daterangepicker-vfit"
                  id="drp_autogen0">Oct 1, 2022 - Oct 31, 2022<span class="ui-button-icon-space"> </span><span
                    class="ui-button-icon ui-icon ui-icon-triangle-1-s"></span></button>
              </div>

              <div class="col-lg-3">
                <label class="invisible d-block">Search</label>
                <button type="button" class="btn btn-primary" id="reportsubmit">Submit</button>
                <a class="btn btn-secondary" href="/report/summary">Reset</a>
              </div>

              <div class="col-lg-3">
                <label>Data Base On</label>
                <div class="form-group">
                 <select name="data_base_on" class="form-control select2" required id="data_base_on">
                    <option value="higher_revenue_usd" data-select2-id="select2-data-10-njlv">Highest Revenue USD
                    </option>
                    <option value="lowest_revenue_usd">Lowest Revenue USD</option>
                    <option value="highest_reg">Highest Reg</option>
                    <option value="lowest_reg">Lowest Reg</option>
                    <option value="highest_unreg">Highest Unreg</option>
                    <option value="lowest_unreg">Lowest Unreg</option>
                    <option value="highest_renewal">Highest Renewal</option>
                    <option value="lowest_renewal">Lowest Renewal</option>
                    <option value="highest_bill_rate">Highest Bill Rate</option>
                    <option value="lowest_bill_rate">Lowest Bill Rate</option>
                  </select>
                </div>
              </div>
              <div class="col-lg-2">
                <label class="invisible d-block">Sort</label>
                <button type="button" class="btn btn-primary" id="alphBnt"><i class="fa fa-filter"></i> Filter</button>
              </div>
              <div class="col-lg-2">
                <label class="invisible d-block">Export Excel</label>
                <button class="btn btn-sm pnl-xls" style="color:white; background-color:green" data-param="reportXls"><i
                    class="fa fa-file-excel-o"></i>Export All as XLS</button>
              </div>
            </div>
          </div>
        </div>

            <script>
                var baseUrl = window.location.origin + "/";
                function country(){
                    var e = document.getElementById("company");
                    var value = e.value;

                    $.ajax({
                        type: "POST",
                        url: baseUrl+"report/country",
                        data:{'id':value},
                        dataType: "json",
                        success: function (responses) {
                            document.getElementById('country').innerHTML ='<option value="">Country Name</option>';
                            document.getElementById('operator').innerHTML ='<option value="">Operator Name</option>';
                            $.each(responses.countrys, function(index,response){
                                $("#country").append('<option value="'+response.country_id+'">'+response.country_name+'</option>');
                            });
                            $.each(responses.operators, function(index,response){
                                $("#operator").append('<option value="'+response.id_operator+'">'+response.operator_name+'</option>');
                            });
                        },
                    });
                }
                function operator(){
                    var e = document.getElementById("country");
                    var value = e.value;
                    var e = document.getElementById("company");
                    var company = e.value;
                    $.ajax({
                        type: "POST",
                        url: baseUrl+"report/operator",
                        data:{'id':value,'company':company},
                        dataType: "json",
                        success: function (responses) {
                            document.getElementById('operator').innerHTML ='<option value="">Operator Name</option>';
                            $.each(responses, function(index,response){
                                $("#operator").append('<option value="'+response.id_operator+'">'+response.operator_name+'</option>');
                            });

                        },
                    });
                }
            </script>
