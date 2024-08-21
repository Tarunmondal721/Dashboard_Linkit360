

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
                        <th>Cost Campaign</th>
                        <th>RnD</th>
                        <th>Content</th>
                        <th>Fun Basket</th>
                        <th>BD</th>
                        <th>Platform</th>
                        <th>Hosting</th>
                    </tr>
                </thead>
                <tbody>
                    @if($services)
                    @foreach($services as $data)
                    <tr>
                      <td>{{ $data['year'] }}</td>
                      <td>{{ $data['keyword'] }}</td>
                      <td>{{ $data['month'] }}</td>
                      <td>{{ $data['final_cost_campaign'] }}</td>
                      <td>{{ $data['rnd'] }}</td>
                      <td>{{ $data['content'] }}</td>
                      <td>{{ $data['fun_basket'] }}</td>
                      <td>{{ $data['bd'] }}</td>
                      <td>{{ $data['platform'] }}</td>
                      <td>{{ $data['hosting'] }}</td>
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