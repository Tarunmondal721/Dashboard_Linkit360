<?php $countrys= App\Models\Operator::select('country_name','country_id')->Status(1)->orderBy('country_name', 'ASC')->distinct()->get();?>
<?php $operators= App\Models\Operator::Status(1)->orderBy('operator_name', 'ASC')->get();?>    

    <div class="card shadow-sm mt-0">
      <div class="card-body">
          <div class="row">
            <div class="col-lg-2">
              <div class="form-group">
                <label for="country">Country</label>
                <select class="form-control select2" id="country" name="country" onchange="operator()">
                    <option value="">Select Country</option>
                    <option value="allcompany">All Country</option>                  
                    @foreach ($countrys as $country)
                    <option value="{{ $country->country_id }}">{{ $country->country_name }}</option>
                    @endforeach
                </select>
              </div>
            </div>
            <div class="col-lg-2">
              <label for="operator">Operator</label>
                <select class="form-control select2" id="operator" name="operator">
                    <option value="">Select Operator</option>                 
                    @foreach ($operators as $operator)
                    <option value="{{ $operator->id_operator }}">{{ $operator->operator_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-2">
              <label for="months">Month</label>
                <select class="simple-multiple-select select2" name="month" id="month" style="width:100%">
                  <option value="">Select Month</option>
                  @foreach ($data['months'] as $month)
                    <option value="{{$month}}" <?php echo ($month == $data['selected_month']) ? 'selected' : '' ?> >{{$data['monthArray'][$month]}}</option>
                  @endforeach
                </select>
            </div>
            <div class="col-lg-2">
              <label class="invisible d-block">Button</label>
              <button type="submit" class="btn btn-primary" id="roi_submit">Submit</button>
            </div>
            <div class="col-lg-4">
              <label class="invisible d-block">Button</label>
              <a href="{{route('analytic.roi.createNewWeeklyCaps')}}" class="btn btn-primary">Add New Weekly Caps</a>
            </div>
          </div>
          <div class="error_block"></div>

        <div class="row">
          <div class="col-md-3">
            <label for="sorting_roi">Show Data Base On</label>
            <div class="form-group">
              <select class="simple-multiple-select select2" name="sorting_roi" id="sorting_roi" style="width: 100%">
                <option value="highest_cp_revenue">Highest CP Revenue</option>
                <option value="lowest_cp_revenue">Lowest CP Revenue</option>
                <option value="highest_mo">Highest MO</option>
                <option value="lowest_mo">Lowest MO</option>
                <option value="highest_ad_cost">Highest Ad Cost</option>
                <option value="lowest_ad_cost">Lowest Ad Cost</option>
                <option value="highest_roi">Highest ROI</option>
                <option value="lowest_roi">Lowest ROI</option>
              </select>
            </div>
          </div>
          <div class="col-md-2">
            <label class="invisible d-block">Sort</label>
            <button type="button" class="btn btn-primary" id="sortRoiBtn"><i class="fa fa-filter"></i>
              Filter</button>
          </div>
        </div>
      </div>
    </div>
    <script>
      var baseUrl = window.location.origin + "/";
      function operator(){
            var e = document.getElementById("country");
            var value = e.value;
            console.log(value);
            $.ajax({
                type: "POST",
                url: baseUrl+"report/operator",
                data:{'id':value},
                dataType: "json",
                success: function (responses) {
                    document.getElementById('operator').innerHTML ='<option value="">Select Operator</option>';
                    $.each(responses, function(index,response){
                        $("#operator").append('<option value="'+response.id_operator+'">'+response.operator_name+'</option>');
                    });

                },
            });
        }
    </script>