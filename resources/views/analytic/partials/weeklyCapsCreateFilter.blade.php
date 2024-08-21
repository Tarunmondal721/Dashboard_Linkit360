        <div class="card shadow-sm mt-0">
          <div class="card-body">
              <div class="row">
                <div class="col-lg-3">
                  <div class="form-group">
                    <label>Year</label>
                    <select class="simple-multiple-select select2" name="year" id="year" style="width:100%">
                      <option value="">Select Year</option>
                      @foreach ($data['years'] as $year)
                        <option value="{{$year}}" <?php echo ($year == $data['selected_year']) ? 'selected' : '' ?> >{{$year}}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="col-lg-3">
                  <div class="form-group">
                    <label>Month</label>
                    <select class="simple-multiple-select select2" name="month" id="month" style="width:100%">
                      <option value="">Select Month</option>
                      @foreach ($data['months'] as $month)
                        <option value="{{$month}}" <?php echo ($month == $data['selected_month']) ? 'selected' : '' ?> >{{$data['monthArray'][$month]}}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="col-lg-3">
                  <label class="invisible d-block">Submit</label>
                  <button type="submit" class="btn btn-secondary" onclick="submit()">Submit</button>
                </div>
              </div>
          </div>
        </div>

        <script>

        function submit(){
            var e = document.getElementById("year");
            var year = e.value;

            var e = document.getElementById("month");
            var month = e.value;

            var urls= window.location.origin+'/analytic/roi/createNewWeeklyCaps';

            var url = urls+'?year='+year+'&month='+month;
            window.location.href = url;
        }

        </script>