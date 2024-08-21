@extends('layouts.admin')

@section('title')

    {{ __('Add New Opex') }}

@endsection

@section('content')
    
    @include('finance.partials.financeCreateOpexFilter')

    <div class="row">
        <div class="col-6">
            <form action="{{ route('finance.importTargetOpex') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <button type="submit" class="btn btn-success">Import</button>
                <input class="excel_input" type="file" name="file">
            </form>
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
                <form id="createTargetOpex" enctype="multipart/form-data">

                <input type="hidden" name="year" value="{{ $data['year'] }}" id="selected_year">
                <input type="hidden" name="company_id" value="{{ $data['company'] }}" id="company_id">
                
                @if(isset($revdataDetails) && !empty($revdataDetails))
                @foreach ($revdataDetails as $rev_data)
                
                <tr>
                    <td>Opex</td>
                    <td><input type="number" class="form-control" name="opex[12]"  min="0"></td>
                    <td><input type="number" class="form-control" name="opex[11]"  min="0"></td>
                    <td><input type="number" class="form-control" name="opex[10]"  min="0"></td>
                    <td><input type="number" class="form-control" name="opex[9]"  min="0"></td>
                    <td><input type="number" class="form-control" name="opex[8]"  min="0"></td>
                    <td><input type="number" class="form-control" name="opex[7]"  min="0"></td>
                    <td><input type="number" class="form-control" name="opex[6]"  min="0"></td>
                    <td><input type="number" class="form-control" name="opex[5]"  min="0"></td>
                    <td><input type="number" class="form-control" name="opex[4]"  min="0"></td>
                    <td><input type="number" class="form-control" name="opex[3]"  min="0"></td>
                    <td><input type="number" class="form-control" name="opex[2]"  min="0"></td>
                    <td><input type="number" class="form-control" name="opex[1]"  min="0"></td>
                </tr>
                <tr>
                    <td>Target Opex</td>
                    <td><input type="number" class="form-control" name="target_opex[12]"  min="0"></td>
                    <td><input type="number" class="form-control" name="target_opex[11]"  min="0"></td>
                    <td><input type="number" class="form-control" name="target_opex[10]"  min="0"></td>
                    <td><input type="number" class="form-control" name="target_opex[9]"  min="0"></td>
                    <td><input type="number" class="form-control" name="target_opex[8]"  min="0"></td>
                    <td><input type="number" class="form-control" name="target_opex[7]"  min="0"></td>
                    <td><input type="number" class="form-control" name="target_opex[6]"  min="0"></td>
                    <td><input type="number" class="form-control" name="target_opex[5]"  min="0"></td>
                    <td><input type="number" class="form-control" name="target_opex[4]"  min="0"></td>
                    <td><input type="number" class="form-control" name="target_opex[3]"  min="0"></td>
                    <td><input type="number" class="form-control" name="target_opex[2]"  min="0"></td>
                    <td><input type="number" class="form-control" name="target_opex[1]"  min="0"></td>
                </tr>
                
                @endforeach
                @endif

                </form>
            </tbody>
        </table>
    </div>

    <div class="text-center col-12" style="text-align: right !important; margin-bottom: 10px;">
        <a href="{{ route('finance.targetRevenue') }}" class="btn btn-link">Back</a>
        <button type="submit" class="btn btn-primary" onclick="storeData()">Submit</button>
    </div>

    <script type="text/javascript">

        var base_url = window.location.origin+'/finance/createTargetOpex/';

        function storeData()
        {
            var formData = new FormData($('#createTargetOpex')[0]);
            $.ajax({
                url: base_url + 'finance/storeTargetOpex',
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
                        alert('Target Opex successfully added!');
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