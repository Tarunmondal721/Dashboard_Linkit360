@extends('layouts.admin')

@section('title')

    {{ __('Add New Final Cost Report') }}

@endsection

@section('content')
    
    @include('finance.partials.financeCreateFilter')

    <div class="row">
        <div class="col-6">
            <form action="{{ route('finance.importFinanceCostReport') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <button type="submit" class="btn btn-success">Import</button>
                <input class="excel_input" type="file" name="file">
            </form>
        </div>
        <div class="text-center col-6" style="text-align: right !important; margin-bottom: 10px;">
            <a href="{{ route('finance.financeCostReport') }}" class="btn btn-link">Back</a>
            <button type="button" class="btn btn-primary" onclick="storeData()">Submit</button>
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
                <form id="createFinanceCostReport" enctype="multipart/form-data">

                <input type="hidden" name="year" value="{{ $data['year'] }}" id="year">
                <input type="hidden" name="country_id" value="{{ $data['country'] }}" id="country_id">
                <input type="hidden" name="operator_id" value="{{ $data['operator'] }}" id="operator_id">
                <input type="hidden" name="id_service" value="{{ $data['service'] }}" id="id_service">
                
                @if(isset($revdataDetails) && !empty($revdataDetails))
                @foreach ($revdataDetails as $country=>$rev_data)
                @foreach ($rev_data as $operator=>$revdata)
                
                <tr>
                    <td>Cost Campaign</td>
                    <td><input type="number" class="form-control" name="final_cost_campaign[12]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="final_cost_campaign[11]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="final_cost_campaign[10]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="final_cost_campaign[9]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="final_cost_campaign[8]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="final_cost_campaign[7]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="final_cost_campaign[6]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="final_cost_campaign[5]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="final_cost_campaign[4]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="final_cost_campaign[3]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="final_cost_campaign[2]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="final_cost_campaign[1]" placeholder="Input Cost" min="0"></td>
                </tr>
                <tr>
                    <td>RnD(5%)</td>
                    <td><input type="number" class="form-control" name="rnd[12]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="rnd[11]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="rnd[10]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="rnd[9]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="rnd[8]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="rnd[7]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="rnd[6]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="rnd[5]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="rnd[4]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="rnd[3]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="rnd[2]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="rnd[1]" placeholder="Input Cost" min="0"></td>
                </tr>
                <tr>
                    <td>App Content(2%)</td>
                    <td><input type="number" class="form-control" name="content[12]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="content[11]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="content[10]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="content[9]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="content[8]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="content[7]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="content[6]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="content[5]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="content[4]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="content[3]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="content[2]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="content[1]" placeholder="Input Cost" min="0"></td>
                </tr>
                <tr>
                    <td>Fun Basket(10%)</td>
                    <td><input type="number" class="form-control" name="fun_basket[12]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="fun_basket[11]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="fun_basket[10]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="fun_basket[9]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="fun_basket[8]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="fun_basket[7]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="fun_basket[6]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="fun_basket[5]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="fun_basket[4]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="fun_basket[3]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="fun_basket[2]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="fun_basket[1]" placeholder="Input Cost" min="0"></td>
                </tr>
                <tr>
                    <td>BD(10%)</td>
                    <td><input type="number" class="form-control" name="bd[12]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="bd[11]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="bd[10]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="bd[9]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="bd[8]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="bd[7]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="bd[6]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="bd[5]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="bd[4]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="bd[3]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="bd[2]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="bd[1]" placeholder="Input Cost" min="0"></td>
                </tr>
                <tr>
                    <td>Platform(10%)</td>
                    <td><input type="number" class="form-control" name="platform[12]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="platform[11]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="platform[10]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="platform[9]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="platform[8]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="platform[7]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="platform[6]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="platform[5]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="platform[4]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="platform[3]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="platform[2]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="platform[1]" placeholder="Input Cost" min="0"></td>
                </tr>
                <tr>
                    <td>Hosting(8%)</td>
                    <td><input type="number" class="form-control" name="hosting[12]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="hosting[11]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="hosting[10]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="hosting[9]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="hosting[8]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="hosting[7]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="hosting[6]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="hosting[5]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="hosting[4]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="hosting[3]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="hosting[2]" placeholder="Input Cost" min="0"></td>
                    <td><input type="number" class="form-control" name="hosting[1][{{ $revdata['country_id'] }}][{{ $revdata['operator_id'] }}]" placeholder="Input Cost" min="0"></td>
                </tr>
                
                @endforeach
                @endforeach
                @endif

                </form>
            </tbody>
        </table>
    </div>

    <script type="text/javascript">

        var base_url = window.location.origin+'/finance/createFinanceCostReport/';

        function storeData()
        {
            var formData = new FormData($('#createFinanceCostReport')[0]);
            $.ajax({
                url: base_url + 'finance/storeFinanceCostReport',
                type: 'POST',
                data: formData,
                dataType: 'JSON',
                contentType: false,
                processData: false,
                cache: false,
                global: false,
                success: function(response){
                    if (response.success == 1) {
                        alert('Finance Cost successfully added!');
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