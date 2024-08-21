

<div class="card shadow-sm mt-0">
  <div class="card-body">
    <div class="form-group row">
      <div class="col-md-12" style="position: relative;">
        <div class="col-12">
          <div class="row">
            @if(isset($services))
            <div class="table-responsive shadow-sm mb-4 add-new-revenue">
              <table class="table table-light table-borderd m-0 font-13 table-text-no-wrap">
                <thead class="thead-dark sticky-col">
                    <tr>
                        <th>Year</th>
                        <th>service</th>
                        <th>Month</th>
                        <th>Revenue</th>
                        <th>Revenue after share</th>
                        <th>Pnl</th>
                        <th>Opex</th>
                        <th>Ebida</th>
                    </tr>
                </thead>
                <tbody>
                    @if($services)
                    @foreach($services as $data)
                    <tr>
                      <td>{{ $data['year'] }}</td>
                      <td>{{ $data['keyword'] }}</td>
                      <td>{{ $data['month'] }}</td>
                      <td>{{ $data['revenue'] }}</td>
                      <td>{{ $data['revenue_after_share'] }}</td>
                      <td>{{ $data['pnl'] }}</td>
                      <td>{{ $data['opex'] }}</td>
                      <td>{{ $data['ebida'] }}</td>
                    </tr>
                    @endforeach
                    @endif
                </tbody>
              </table>
            </div>
            @else
            <div class="float left">
              <label for="operator" class=" font-weight-500">{{__('No Service data Added')}}</label> 
            </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</div>