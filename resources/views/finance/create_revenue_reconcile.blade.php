@extends('layouts.admin')

@section('title')

    {{ __('Add New Revenue Reconcile') }}

@endsection

@section('content')
    
    @include('finance.partials.financeCreateFilter')

    <div class="row">
        <div class="col-6">
            <form action="{{ route('finance.importRevenueReconcile') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <button type="submit" class="btn btn-success">Import</button>
                <input class="excel_input" type="file" name="file">
            </form>
        </div>
        <div class="text-center col-6" style="text-align: right !important; margin-bottom: 10px;">
            <a href="{{ route('finance.revenueReconcile') }}" class="btn btn-link">Back</a>
            <button type="submit" class="btn btn-primary" onclick="storeData()">Submit</button>
        </div>
    </div>
    
    <div class="table-responsive shadow-sm mb-4 add-new-revenue">
        <table class="table table-light table-borderd m-0 font-13 table-text-no-wrap">
            <thead class="thead-dark sticky-col">
                <tr>
                    <th></th>
                    <th>December</th>
                    <th>November</th>
                    <th>October</th>
                    <th>September</th>
                    <th>August</th>
                    <th>July</th>
                    <th>June</th>
                    <th>May</th>
                    <th>April</th>
                    <th>March</th>
                    <th>February</th>
                    <th>January</th>
                    <th>Reconcile File (Optional)</th>
                </tr>
            </thead>
            <tbody>
                <form id="createRevenueReconcile" enctype="multipart/form-data">

                <input type="hidden" name="year" value="{{ $data['year'] }}" id="year">
                <input type="hidden" name="country_id" value="{{ $data['country'] }}" id="country_id">
                <input type="hidden" name="operator_id" value="{{ $data['operator'] }}" id="operator_id">
                <input type="hidden" name="id_service" value="{{ $data['service'] }}" id="id_service">
                
                @if(isset($revdataDetails) && !empty($revdataDetails))
                @foreach ($revdataDetails as $country=>$rev_data)
                @foreach ($rev_data as $operator=>$revdata)
                
                <tr>
                    <td>Revenue</td>
                    <td><input type="number" class="form-control" name="revenue[12]" placeholder="Input Revenue" min="0"></td>

                    <td><input type="number" class="form-control" name="revenue[11]" placeholder="Input Revenue" min="0"></td>

                    <td><input type="number" class="form-control" name="revenue[10]" placeholder="Input Revenue" min="0"></td>

                    <td><input type="number" class="form-control" name="revenue[09]" placeholder="Input Revenue" min="0"></td>

                    <td><input type="number" class="form-control" name="revenue[08]" placeholder="Input Revenue" min="0"></td>

                    <td><input type="number" class="form-control" name="revenue[07]" placeholder="Input Revenue" min="0"></td>

                    <td><input type="number" class="form-control" name="revenue[06]" placeholder="Input Revenue" min="0"></td>

                    <td><input type="number" class="form-control" name="revenue[05]" placeholder="Input Revenue" min="0"></td>

                    <td><input type="number" class="form-control" name="revenue[04]" placeholder="Input Revenue" min="0"></td>

                    <td><input type="number" class="form-control" name="revenue[03]" placeholder="Input Revenue" min="0"></td>

                    <td><input type="number" class="form-control" name="revenue[02]" placeholder="Input Revenue" min="0"></td>

                    <td><input type="number" class="form-control" name="revenue[01]" placeholder="Input Revenue" min="0"></td>

                    <td><input type="file" name="file"></td>
                </tr>
                <tr>
                    <td>Revenue After Telco</td>
                    <td><input type="number" class="form-control" name="revenue_telco[12]" placeholder="Input Revenue" min="0"></td>

                    <td><input type="number" class="form-control" name="revenue_telco[11]" placeholder="Input Revenue" min="0"></td>

                    <td><input type="number" class="form-control" name="revenue_telco[10]" placeholder="Input Revenue" min="0"></td>

                    <td><input type="number" class="form-control" name="revenue_telco[09]" placeholder="Input Revenue" min="0"></td>

                    <td><input type="number" class="form-control" name="revenue_telco[08]" placeholder="Input Revenue" min="0"></td>

                    <td><input type="number" class="form-control" name="revenue_telco[07]" placeholder="Input Revenue" min="0"></td>

                    <td><input type="number" class="form-control" name="revenue_telco[06]" placeholder="Input Revenue" min="0"></td>

                    <td><input type="number" class="form-control" name="revenue_telco[05]" placeholder="Input Revenue" min="0"></td>

                    <td><input type="number" class="form-control" name="revenue_telco[04]" placeholder="Input Revenue" min="0"></td>

                    <td><input type="number" class="form-control" name="revenue_telco[03]" placeholder="Input Revenue" min="0"></td>

                    <td><input type="number" class="form-control" name="revenue_telco[02]" placeholder="Input Revenue" min="0"></td>

                    <td><input type="number" class="form-control" name="revenue_telco[01]" placeholder="Input Revenue" min="0"></td>
                </tr>
                <tr>
                    <td>Net Revenue</td>
                    <td><input type="number" class="form-control" name="net_revenue[12]" placeholder="Input Revenue" min="0"></td>

                    <td><input type="number" class="form-control" name="net_revenue[11]" placeholder="Input Revenue" min="0"></td>

                    <td><input type="number" class="form-control" name="net_revenue[10]" placeholder="Input Revenue" min="0"></td>

                    <td><input type="number" class="form-control" name="net_revenue[09]" placeholder="Input Revenue" min="0"></td>

                    <td><input type="number" class="form-control" name="net_revenue[08]" placeholder="Input Revenue" min="0"></td>

                    <td><input type="number" class="form-control" name="net_revenue[07]" placeholder="Input Revenue" min="0"></td>

                    <td><input type="number" class="form-control" name="net_revenue[06]" placeholder="Input Revenue" min="0"></td>

                    <td><input type="number" class="form-control" name="net_revenue[05]" placeholder="Input Revenue" min="0"></td>

                    <td><input type="number" class="form-control" name="net_revenue[04]" placeholder="Input Revenue" min="0"></td>

                    <td><input type="number" class="form-control" name="net_revenue[03]" placeholder="Input Revenue" min="0"></td>

                    <td><input type="number" class="form-control" name="net_revenue[02]" placeholder="Input Revenue" min="0"></td>

                    <td><input type="number" class="form-control" name="net_revenue[01]" placeholder="Input Revenue" min="0"></td>
                </tr>
                
                @endforeach
                @endforeach
                @endif

                </form>
            </tbody>
        </table>
    </div>

    <script type="text/javascript">

        var base_url = window.location.origin+'/finance/createRevenueReconcile/';

        function storeData()
        {
            var formData = new FormData($('#createRevenueReconcile')[0]);
            $.ajax({
                url: base_url + 'finance/storeRevenueReconcile',
                type: 'POST',
                data: formData,
                dataType: 'JSON',
                contentType: false,
                processData: false,
                cache: false,
                global: false,
                success: function(response){
                    // console.log(response);
                    if (response.success == 1) {
                        alert('revenue reconcile successfully added!');
                        window.location.reload();
                    }else{
                        alert('something went wrong!');
                        window.location.reload();
                    }
                }

            });
        }
    </script>

@endsection