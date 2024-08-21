@extends('layouts.admin')

@section('title')

    {{ __('Add New Revenue Reconcile') }}

@endsection

@section('content')
    
    @include('finance.partials.financeCreateFilter')

    <div class="row">
        <div class="col-6">
            <form action="{{ route('finance.importTargetRevenue') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <button type="submit" class="btn btn-success">Import</button>
                <input class="excel_input" type="file" name="file">
            </form>
        </div>
            
        <div class="text-center col-6" style="text-align: right !important; margin-bottom: 10px;">
            <a href="{{ route('finance.targetRevenue') }}" class="btn btn-link">Back</a>
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
                </tr>
            </thead>
            <tbody>
                <form id="createTargetRevenueReconcile" enctype="multipart/form-data">

                <input type="hidden" name="year" value="{{ $data['year'] }}" id="selected_year">
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
                    <td><input type="number" class="form-control" name="revenue[9]" placeholder="Input Revenue" min="0"></td>
                    <td><input type="number" class="form-control" name="revenue[8]" placeholder="Input Revenue" min="0"></td>
                    <td><input type="number" class="form-control" name="revenue[7]" placeholder="Input Revenue" min="0"></td>
                    <td><input type="number" class="form-control" name="revenue[6]" placeholder="Input Revenue" min="0"></td>
                    <td><input type="number" class="form-control" name="revenue[5]" placeholder="Input Revenue" min="0"></td>
                    <td><input type="number" class="form-control" name="revenue[4]" placeholder="Input Revenue" min="0"></td>
                    <td><input type="number" class="form-control" name="revenue[3]" placeholder="Input Revenue" min="0"></td>
                    <td><input type="number" class="form-control" name="revenue[2]" placeholder="Input Revenue" min="0"></td>
                    <td><input type="number" class="form-control" name="revenue[1]" placeholder="Input Revenue" min="0"></td>
                </tr>
                <tr>
                    <td>Revenue After Share</td>
                    <td><input type="number" class="form-control" name="revenue_after_share[12]" placeholder="Input revenue_after_share" min="0"></td>
                    <td><input type="number" class="form-control" name="revenue_after_share[11]" placeholder="Input revenue_after_share" min="0"></td>
                    <td><input type="number" class="form-control" name="revenue_after_share[10]" placeholder="Input revenue_after_share" min="0"></td>
                    <td><input type="number" class="form-control" name="revenue_after_share[9]" placeholder="Input revenue_after_share" min="0"></td>
                    <td><input type="number" class="form-control" name="revenue_after_share[8]" placeholder="Input revenue_after_share" min="0"></td>
                    <td><input type="number" class="form-control" name="revenue_after_share[7]" placeholder="Input revenue_after_share" min="0"></td>
                    <td><input type="number" class="form-control" name="revenue_after_share[6]" placeholder="Input revenue_after_share" min="0"></td>
                    <td><input type="number" class="form-control" name="revenue_after_share[5]" placeholder="Input revenue_after_share" min="0"></td>
                    <td><input type="number" class="form-control" name="revenue_after_share[4]" placeholder="Input revenue_after_share" min="0"></td>
                    <td><input type="number" class="form-control" name="revenue_after_share[3]" placeholder="Input revenue_after_share" min="0"></td>
                    <td><input type="number" class="form-control" name="revenue_after_share[2]" placeholder="Input revenue_after_share" min="0"></td>
                    <td><input type="number" class="form-control" name="revenue_after_share[1]" placeholder="Input revenue_after_share" min="0"></td>
                </tr>
                <tr>
                    <td>PNL</td>
                    <td><input type="number" class="form-control" name="pnl[12]" placeholder="Input Revenue" min="0"></td>
                    <td><input type="number" class="form-control" name="pnl[11]" placeholder="Input Revenue" min="0"></td>
                    <td><input type="number" class="form-control" name="pnl[10]" placeholder="Input Revenue" min="0"></td>
                    <td><input type="number" class="form-control" name="pnl[9]" placeholder="Input Revenue" min="0"></td>
                    <td><input type="number" class="form-control" name="pnl[8]" placeholder="Input Revenue" min="0"></td>
                    <td><input type="number" class="form-control" name="pnl[7]" placeholder="Input Revenue" min="0"></td>
                    <td><input type="number" class="form-control" name="pnl[6]" placeholder="Input Revenue" min="0"></td>
                    <td><input type="number" class="form-control" name="pnl[5]" placeholder="Input Revenue" min="0"></td>
                    <td><input type="number" class="form-control" name="pnl[4]" placeholder="Input Revenue" min="0"></td>
                    <td><input type="number" class="form-control" name="pnl[3]" placeholder="Input Revenue" min="0"></td>
                    <td><input type="number" class="form-control" name="pnl[2]" placeholder="Input Revenue" min="0"></td>
                    <td><input type="number" class="form-control" name="pnl[1]" placeholder="Input Revenue" min="0"></td>
                </tr>
                <tr>
                    <td>Opex</td>
                    <td><input type="number" class="form-control" name="opex[12]" placeholder="Input Revenue" min="0"></td>
                    <td><input type="number" class="form-control" name="opex[11]" placeholder="Input Revenue" min="0"></td>
                    <td><input type="number" class="form-control" name="opex[10]" placeholder="Input Revenue" min="0"></td>
                    <td><input type="number" class="form-control" name="opex[9]" placeholder="Input Revenue" min="0"></td>
                    <td><input type="number" class="form-control" name="opex[8]" placeholder="Input Revenue" min="0"></td>
                    <td><input type="number" class="form-control" name="opex[7]" placeholder="Input Revenue" min="0"></td>
                    <td><input type="number" class="form-control" name="opex[6]" placeholder="Input Revenue" min="0"></td>
                    <td><input type="number" class="form-control" name="opex[5]" placeholder="Input Revenue" min="0"></td>
                    <td><input type="number" class="form-control" name="opex[4]" placeholder="Input Revenue" min="0"></td>
                    <td><input type="number" class="form-control" name="opex[3]" placeholder="Input Revenue" min="0"></td>
                    <td><input type="number" class="form-control" name="opex[2]" placeholder="Input Revenue" min="0"></td>
                    <td><input type="number" class="form-control" name="opex[1]" placeholder="Input Revenue" min="0"></td>
                </tr>
                <tr>
                    <td>Ebida</td>
                    <td><input type="number" class="form-control" name="ebida[12]" placeholder="Input Revenue" min="0"></td>
                    <td><input type="number" class="form-control" name="ebida[11]" placeholder="Input Revenue" min="0"></td>
                    <td><input type="number" class="form-control" name="ebida[10]" placeholder="Input Revenue" min="0"></td>
                    <td><input type="number" class="form-control" name="ebida[9]" placeholder="Input Revenue" min="0"></td>
                    <td><input type="number" class="form-control" name="ebida[8]" placeholder="Input Revenue" min="0"></td>
                    <td><input type="number" class="form-control" name="ebida[7]" placeholder="Input Revenue" min="0"></td>
                    <td><input type="number" class="form-control" name="ebida[6]" placeholder="Input Revenue" min="0"></td>
                    <td><input type="number" class="form-control" name="ebida[5]" placeholder="Input Revenue" min="0"></td>
                    <td><input type="number" class="form-control" name="ebida[4]" placeholder="Input Revenue" min="0"></td>
                    <td><input type="number" class="form-control" name="ebida[3]" placeholder="Input Revenue" min="0"></td>
                    <td><input type="number" class="form-control" name="ebida[2]" placeholder="Input Revenue" min="0"></td>
                    <td><input type="number" class="form-control" name="ebida[1]" placeholder="Input Revenue" min="0"></td>
                </tr>
                
                @endforeach
                @endforeach
                @endif

                </form>
            </tbody>
        </table>
    </div>

    <script type="text/javascript">

        var base_url = window.location.origin+'/finance/createTargetRevenueReconcile/';

        function storeData()
        {
            var formData = new FormData($('#createTargetRevenueReconcile')[0]);
            $.ajax({
                url: base_url + 'finance/storeTargetRevenueReconcile',
                type: 'POST',
                data: formData,
                dataType: 'JSON',
                contentType: false,
                processData: false,
                cache: false,
                global: false,
                success: function(response){
                    // console.log(response); return false;
                    if (response.success == 1) {
                        alert('Target revenue successfully added!');
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