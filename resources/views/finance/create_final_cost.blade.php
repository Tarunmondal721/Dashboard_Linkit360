@extends('layouts.admin')

@section('title')

    {{ __('Add New Revenue Reconcile') }}

@endsection

@section('content')
    
    @include('finance.partials.financeCreateFilter')

    <div class="text-center" style="text-align: right !important; margin-bottom: 10px;">
        <button type="submit" class="btn btn-primary" onclick="storeData()">Submit</button>
    </div>

    <div class="table-responsive shadow-sm mb-4 add-new-revenue">
        <table class="table table-light table-borderd m-0 font-13 table-text-no-wrap">
            <thead class="thead-dark sticky-col">
                <tr>
                    <th>Country Name</th>
                    <th>Operator Name</th>
                    <th>Revenue</th>
                    <th>Revenue After Share</th>
                    <th>PNL</th>
                </tr>
            </thead>
            <tbody>
                <form id="createTargetRevenueReconcile" enctype="multipart/form-data">

                <input type="hidden" name="year" value="{{ $data['selected_year'] }}" id="selected_year">
                <input type="hidden" name="month" value="{{ $data['selected_month'] }}" id="selected_month">
                
                @if(isset($revdataDetails) && !empty($revdataDetails))
                @foreach ($revdataDetails as $country=>$rev_data)
                @foreach ($rev_data as $operator=>$revdata)
                
                <tr>
                    <td>{{ ucfirst($country) }}</td>
                    <td>{{ ucfirst($operator) }}</td>

                    <td><input type="number" class="form-control" name="revenue[][{{ $revdata['country_id'] }}][{{ $revdata['operator_id'] }}]" placeholder="Input Revenue" min="0" value="{{ $revdata['revenue'] }}"></td>

                    <td><input type="number" class="form-control" name="revenue_after_share[][{{ $revdata['country_id'] }}][{{ $revdata['operator_id'] }}]" placeholder="Input Revenue After Share" min="0" value="{{ $revdata['revenue_after_share'] }}"></td>

                    <td><input type="number" class="form-control" name="pnl[][{{ $revdata['country_id'] }}][{{ $revdata['operator_id'] }}]" placeholder="Input PNL" min="0" value="{{ $revdata['pnl'] }}"></td>
                </tr>
                
                @endforeach
                @endforeach
                @endif

                </form>
                <tr>
                    <td colspan="5" class="text-center">
                        <a href="{{ route('finance.targetRevenue') }}" class="btn btn-link">Back</a>
                        <button type="submit" class="btn btn-primary" onclick="storeData()">Submit</button>
                    </td>
                </tr>
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
                        window.location.reload();
                    }else if(response.error == 1){
                        
                    }
                }

            });
        }
    </script>

@endsection