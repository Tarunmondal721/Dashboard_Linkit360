<?php $countrys= App\Models\Country::orderBy('country', 'ASC')->get();?>
<?php $companys= App\Models\Company::orderBy('name', 'ASC')->get();?>
    <div class="card shadow-sm mt-0">
      <div class="card-body">
          <div class="row">
            <div class="col-lg-4">
              <div class="form-group">
                <label for="company">Company</label>
                <select class="form-control select2" name="company" onchange="country()"
                  id="company">
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
                <select class="simple-multiple-select select2" name="country"
                  id="country">
                  <option value="">All Country</option>
                  {{--
                  @foreach ($countrys as $country)
                  <option value="{{ $country->id }}">{{ $country->country }}</option>
                  @endforeach
                  --}}
                </select>
              </div>
            </div>

            

            <div class="col-lg-3">
              <label class="invisible d-block">Search</label>
              <button type="submit" class="btn btn-primary pnl_ctr_filter_btn" onclick="submit()"><i class="fa fa-search"></i>
                Search</button>
            </div>
          </div>
      </div>
    </div>


    <div class="card shadow-sm mt-0">
      <div class="card-body">
        <div class="row">
          <div class="col-lg-3">
            <div class="form-group">
              <label for="report_type">Report Type</label>
              <select class="simple-multiple-select select2" name="report_type" id="report_type" style="width: 100%" data-select2-id="select2-data-report_type" tabindex="-1" aria-hidden="true">

                <option value="">Select Report Type</option>
                @if(isset($data['report_type']) && !empty($data['report_type']))
                @foreach ($data['report_type'] as $report_type)
                  <option value="{{$report_type}}" <?php echo ($report_type == $data['ReportType']) ? 'selected': '' ?>><?php echo ucwords(str_replace('_',' ',$report_type)) ?> Summary</option>
                @endforeach
                @endif
                
              </select>
            </div>
          </div>
          <div class="col-lg-3">
            <label for="report_data">Data Type</label>
            <select class="simple-multiple-select select2" name="report_data" id="report_data" style="width: 100%" data-select2-id="select2-data-report_data" tabindex="-1" aria-hidden="true">
              <option value="">Select Data Type</option>
              <option value="daily" <?php echo isset($data['Daily']) ? 'selected': '' ?>>Daily Report</option>
              {{-- <option value="monthly" <?php echo isset($data['Monthly']) ? 'selected': '' ?>>Monthly Report</option> --}}
            </select>
          </div>
          <div class="col-lg-3">
                <label>Date Range</label>
                <select name="date_range" class="form-control select2" required id="date_range">

                  <option value="">Select Date Range</option>
                  @if(in_array("today", $data['date_range']))
                  <option value="&from={{date('Y-m-d')}}">Today</option>
                  @endif

                  @if(in_array("yesterday", $data['date_range']))
                  <option value="&from={{date('Y-m-d', strtotime( '-1 days' ) )}}">Yesterday</option>
                  @endif
                  
                  @if(in_array("last7day", $data['date_range']))
                  <option value="&from={{date('Y-m-d')}}&to={{date('Y-m-d', strtotime( '-6 days' ) )}}">Last 7 days</option>
                  @endif
                  
                  @if(in_array("last30day", $data['date_range']))
                  <option value="&from={{date('Y-m-d')}}&to={{date('Y-m-d', strtotime( '-30 days' ) )}}">Last 30 days</option>
                  @endif

                  @if(in_array("last60day", $data['date_range']))
                  <option value="&from={{date('Y-m-d')}}&to={{date('Y-m-d', strtotime( '-60 days' ) )}}">Last 60 days</option>
                  @endif

                  @if(in_array("last90day", $data['date_range']))
                  <option value="&from={{date('Y-m-d')}}&to={{date('Y-m-d', strtotime( '-90 days' ) )}}">Last 90 days</option>
                  @endif

                </select>    
            </div>
          <div class="col-lg-3">
            <label class="invisible d-block">Submit</label>
            <button class="btn btn-primary" id="redirectDetailBtn" onclick="submit()">Submit</button>
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
                  document.getElementById('country').innerHTML ='<option value="">Country Name</option>';
                  
                  $.each(responses.countrys, function(index,response){
                      $("#country").append('<option value="'+response.country_id+'">'+response.country_name+'</option>');
                  });
              },
          });
      }

      function submit(){
          var e = document.getElementById("report_data");
          var report_data = e.value;
          var e = document.getElementById("report_type");
          var report_type = e.value;
          var e = document.getElementById("company");
          var company = e.value;
          var e = document.getElementById("country");
          var country = e.value;
          var urls = window.location.origin+'/pivot/summary';
          var date = $('#date_range').val();

          if(report_type != '' && report_type != undefined && report_type != '' && report_type != undefined)
          {
            if(report_data == 'daily'){
                if(report_type == 'account_manager')
                {
                  report_type = 'manager';
                }
                var url = urls+'/'+report_type+'?company='+company+'&country='+country+date;
                window.location.href = url;
            }else{
                var url = urls+'/'+report_type+'/'+report_data+'?company='+company+'&country='+country+'&date='+date;
                window.location.href = url;
            }
          }
      }
    </script>