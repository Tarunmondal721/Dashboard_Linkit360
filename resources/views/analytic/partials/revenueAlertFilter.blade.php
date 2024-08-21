<?php $companys= App\Models\Company::orderBy('name', 'ASC')->get();?>
<?php $countrys= App\Models\Operator::select('country_name','country_id')->Status(1)->orderBy('country_name', 'ASC')->distinct()->get();?>
<?php $operators= App\Models\Operator::orderBy('operator_name', 'ASC')->get();?>

        <div class="card shadow-sm mt-0">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="company">Company</label>
                            <select class="form-control select2" name="company" onchange="country()" id="company">
                                <option value="">Select Company</option>                  
                                <option value="allcompany">All Company</option>                  
                                @foreach ($companys as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="country">Country</label>
                            <select class="form-control select2" id="country" name="country" >
                                <option value="">Select Country</option>
                                <option value="allcompany">All Country</option>                  
                                @foreach ($countrys as $country)
                                <option value="{{ $country->country_id }}">{{ $country->country_name }}</option>
                                @endforeach

                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <label for="operator">Operator</label>
                        <select class="form-control select2" id="operator" name="operator">
                            <option value="">Select Operator</option>                 
                            @foreach ($operators as $operator)
                            <option value="{{ $operator->id_operator }}">{{ $operator->operator_name }}</option>
                            @endforeach

                        </select>
                    </div>
                    <div class="col-lg-4">
                        <label class="invisible d-block">Search</label>
                        <button type="submit" class="btn btn-primary" name="submit"><i class="fa fa-search"></i>Search</button>
                        <a class="btn btn-secondary" href="#">Reset</a>
                    </div>
                </div>
                <div class="error_block"></div>
            </div>
        </div>

        <div class="card shadow-sm mt-0">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4">
                        <label>Data Base On</label>
                        <div class="form-group">
                            <select class="simple-multiple-select select2" id="sorting_log" style="width: 100%" data-select2-id="select2-data-sorting_log" tabindex="-1" aria-hidden="true">
                                <option value="">Please Select</option>
                                <option value="higher_risk_status">Highest Risk Status</option>
                                <option value="lowest_risk_status">Lowest Risk Status</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <label class="invisible d-block">Sort</label>
                        <button type="button" class="btn btn-primary" id="log-sort-1"><i class="fa fa-filter"></i> Filter</button>
                    </div>
                    <div class="col-lg-2">
                        <label class="invisible d-block">Export Excel</label>
                        <button class="btn btn-sm pnl-xls" style="color:white; background-color:green" data-param="container"><i class="fa fa-file-excel-o"></i>Export XLS</button>
                    </div>
                </div>
            </div>
        </div>

        <script type="text/javascript">

            function country(){
              var e = document.getElementById("company");
              var value = e.value;

              $.ajax({
                  type: "POST",
                  url: baseUrl+"report/country",
                  data:{'id':value},
                  dataType: "json",
                  success: function (responses) {
                      document.getElementById('country').innerHTML ='<option value="">Select Country</option>';
                      
                      $.each(responses.countrys, function(index,response){
                          $("#country").append('<option value="'+response.country_id+'">'+response.country_name+'</option>');
                      });
                  },
              });
            }


            function submit(){
                var e = document.getElementById("company");
                var company = e.value;
                var e = document.getElementById("country");
                var country = e.value;
                // var urls= window.location.origin+'/report/pnlsummary';
                // var url=urls+'/'+report_data+'/'+report_type+'?company='+company+'&country='+country+'&date='+date;

                // window.location.href =url;
                
            }

        </script>    