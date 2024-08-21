@php
    $companys= App\Models\Company::orderBy('name', 'ASC')->get();
    $CompanyId= request()->get('company');
    $CountryId= request()->get('country');
    $filterOperator = request()->get('operatorId');
    $start_date = request()->get('from');
    $end_date =  request()->get('to');
    $custom_range =  request()->get('custom_range');
    $countrys = App\Models\Operator::select('country_name','country_id')->Status(1)->orderBy('country_name', 'ASC')->distinct()->get();
    $operators= App\Models\Operator::Status(1)->orderBy('operator_name', 'ASC')->get();
@endphp

        <div class="card shadow-sm mt-0">
        <div class="card-body">
        <div class="row">
        {{-- <div class="col-lg-4">
            <label>Data Type</label>
            <select name="report_data" class="form-control select2" required id="report_data">
                <option value="daily" <?php echo isset($data['Daily']) ? 'selected': '' ?>>Daily Report</option>
                <option value="monthly" <?php echo isset($data['Monthly']) ? 'selected': '' ?>>Monthly Report</option>
            </select>
        </div> --}}

        {{-- <div class="col-lg-4">
            <label>Report Type</label>
            <select name="report_type" class="form-control select2" required id="report_type">
                <option value="operator" <?php echo isset($data['OperatorWise']) ? 'selected': '' ?>>Operator Summary</option>
                <option value="country" <?php echo isset($data['CountryWise']) ? 'selected': '' ?>>Country Summary</option>
            </select>
        </div> --}}

          {{--<div class="col-lg-4">
            <div class="form-group">
              <label for="summarycompany">Company</label>
             <select name="company" class="form-control select2" required id="company" onchange="country()" <?php echo isset($CompanyId) ? 'value="'.$CompanyId.'"': '' ?>>
             <option value=""  selected>Select Company</option>
                <option value="allcompany"<?php  echo isset($CompanyId) && ("allcompany" == $CompanyId) ? 'selected': '' ?> >All Company</option>

                @foreach ($companys as $company)
                <option value="{{$company->id}}" <?php  echo isset($CompanyId) && ($company->id == $CompanyId) ? 'selected': '' ?>>{{$company->name}}</option>
                @endforeach
              </select>
            </div>
          </div>--}}
          <div class="col-lg-4">
            <div class="form-group">
              <label for="summarycountry">Country</label>
              {{-- <select name="country" class="form-control select2" id="country" onchange="operator()" <?php echo isset($CountryId) ? 'value="'.$CountryId.'"': '' ?>>
                <option value="">Country Name</option>
              </select> --}}
              <select name="country" class="form-control select2" id="country" onchange="operator()">
                    <option value=""> Select country</option>
                    @if(isset($sumemry) && !empty($sumemry))
                    @foreach ($countrys as $country)

                    <option value="{{$country->country_id}}" <?php echo isset($sumemry[0]['country']['id']) && ($sumemry[0]['country']['id'] == $country->country_id) ? 'selected' : '' ?>> {{$country->country_name}}</option>

                    @endforeach
                    @else
                    <option value="">Select Country</option>
                    @endif

                    <!--  -->
                </select>

            </div>
          </div>
          <div class="col-lg-4">
            <label for="summery_operator_id">Operator</label>
           {{-- <select name="operator" class="form-control select2" required id="operator">
              <option value="">Operator Name</option>
            </select> --}}
            <select name="operator" class="form-control select2" required id="operator">
                <option value="">Operator Name</option>
                    @if(isset($sumemry) && !empty($sumemry))
                    @if(isset($operator_details) && !empty($operator_details))
                    @foreach ($operator_details['operators'] as $operator)

                    <option value="{{$operator['id_operator']}}" <?php echo isset($sumemry[0]['operator']) && ($sumemry[0]['operator']['id_operator'] == $operator['id_operator']) ? 'selected' : '' ?>> {{$operator['operator_name']}}</option>

                    @endforeach
                    @endif
                    @endif
            </select>
          </div>
          <div class="col-lg-4">
            <label>Date Range</label>
             <select name="date_range" class="form-control select2" required id="date_range" onchange="date_ranges()"  <?php  echo isset($start_date) && isset($end_date) && ($start_date == date('Y-m-d')) &&($end_date == date('Y-m-d')) ? 'value="&from='.$end_date.'&to='.$start_date.'"': '' ?> >

                    <option value="&from={{date('Y-m-d', strtotime( '-30 days' ) )}}&to={{date('Y-m-d')}}" <?php  echo isset($start_date) && isset($end_date) && ($start_date == date('Y-m-d', strtotime( '-30 days' ) )) &&($end_date ==  date('Y-m-d') ) ? 'selected': '' ?> >Last 30 days</option>

                    <option value="&from={{date('Y-m-d')}}&to={{date('Y-m-d')}}" <?php  echo isset($start_date) && isset($end_date) && ($start_date == date('Y-m-d')) &&($end_date == date('Y-m-d')) ? 'selected': '' ?> >Today</option>
                    <option value="&from={{date('Y-m-d', strtotime( '-1 days' ) )}}&to={{date('Y-m-d', strtotime( '-1 days' ) )}}" <?php  echo isset($start_date) && isset($end_date) && ($start_date == date('Y-m-d', strtotime( '-1 days' ))) &&($end_date == date('Y-m-d', strtotime( '-1 days' ) )) ? 'selected': '' ?> >Yesterday</option>

                    <option value="&from={{date('Y-m-d', strtotime( '-6 days' ) )}}&to={{date('Y-m-d')}}" <?php  echo isset($start_date) && isset($end_date) && ($start_date == date('Y-m-d', strtotime( '-6 days' ) )) &&($end_date == date('Y-m-d')) ? 'selected': '' ?> >Last 7 days</option>

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
          {{-- <div class="col-lg-3">
            <label>Data Base On</label>
            <div class="form-group">
             <select class="simple-multiple-select select2" name="data_base_on" id="data_base">
                <option value="">Please Select</option>
                <option value="highest_gp">Highest GP</option>
                <option value="lowest_gp">Lowest GP</option>
                <option value="highest_cost_campaign">Highest Cost Campaign</option>
                <option value="lowest_cost_campaign">Lowest Cost Campaign</option>
                <option value="highest_usd_end_user_revenue">Highest End User Revenue(USD)</option>
                <option value="lowest_usd_end_user_revenue">Lowest End User Revenue(USD)</option>
              </select>
            </div>
          </div>
          <div class="col-lg-2">
            <label class="invisible d-block">Sort</label>
            <button type="button" class="btn btn-primary pnl_submit"><i class="fa fa-sort"></i> Sort</button>
          </div> --}}
          <div class="col-lg-2">
            <label class="invisible d-block">Export Excel</label>
            <button class="btn btn-sm pnl-xls" style="color:white; background-color:green" data-param="reportXls"><i
                class="fa fa-file-excel-o"></i>Export All as XLS</button>
          </div>
        </div>

        <!-- </form> -->




        </div>
        </div>
        <!-- </form> -->

    <script>
        function date_ranges(){
            var values = $('#date_range').val();
            if(values == 'custom_range'){date_range_faield
                $('#date_range_faield').removeClass('gu-hide');
                // console.log(values);
            }else{
                $('#date_range_faield').addClass('gu-hide');
            }
        }

        var baseUrl = window.location.origin + "/";
        function operator() {
            var e = document.getElementById("country");
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
                    document.getElementById('operator').innerHTML = '<option value="">Operator Name</option>';
                    $.each(responses, function(index, response) {
                        $("#operator").append('<option value="' + response.id_operator + '">' + response.operator_name + '</option>');
                    });
                },
            });
        }

        function submit()
        {
            var country = $('#country').val();
            var operator = $('#operator').val();
            var urls = window.location.origin + window.location.pathname;
            var date = $('#date_range').val();
            if (date == 'custom_range') {
                var date = '&from=' + $('#dateform').val() + '&to=' + $('#dateto').val()+'&custom_range=custom_range';
            }

            if(country != '')
            urls = urls +'?country=' + country;
            else
            urls = urls +'?';
            if(operator != '')
            urls = urls + '&operator=' + operator;
            var url = urls + date;
            window.location.href = url;
        }
    </script>