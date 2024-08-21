@extends('layouts.admin')

@section('title')

    {{ __('Add New Weekly Caps') }}

@endsection

@section('content')
    
    @include('analytic.partials.weeklyCapsCreateFilter')

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
                <form id="createNewWeeklyCaps" enctype="multipart/form-data">

                <input type="hidden" name="year" value="{{ $data['selected_year'] }}" id="selected_year">
                <input type="hidden" name="month" value="{{ $data['selected_month'] }}" id="selected_month">
                
                @if(isset($WeeklyDataDetails) && !empty($WeeklyDataDetails))
                @foreach ($WeeklyDataDetails as $country=>$weekly_data)
                @foreach ($weekly_data as $operator=>$weekdata)
                
                <tr>
                    <td>{{ ucfirst($country) }}</td>
                    <td>{{ ucfirst($operator) }}</td>

                    <td><input type="number" class="form-control" name="weekly_caps[][{{ $weekdata['country_id'] }}][{{ $weekdata['operator_id'] }}]" placeholder="Input Weekly Caps" min="0" value="{{ $weekdata['weekly_caps'] }}"></td>
                </tr>
                
                @endforeach
                @endforeach
                @endif

                </form>
                <tr>
                    <td colspan="5" class="text-center">
                        <a href="{{ route('analytic.roi') }}" class="btn btn-link">Back</a>
                        <button type="submit" class="btn btn-primary" onclick="storeData()">Submit</button>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="d-flex align-items-center my-2 pull-right">
        {{-- <span class="badge badge-secondary px-2 bg-primary" id="loadTimer">Load Time :{{ round(microtime(true) - LARAVEL_START, 3) }}s</span> --}}
        
        <button type="button" id="button" class="btn btn-danger btn-floating btn-sm btn-back-to-top-position up"><i class="fa fa-arrow-up"></i></button>

        </div>

    <script type="text/javascript">

        var base_url = window.location.origin+'/analytic/roi/createNewWeeklyCaps/';

        function storeData()
        {
            var formData = new FormData($('#createNewWeeklyCaps')[0]);
            $.ajax({
                url: base_url + 'analytic/roi/storeNewWeeklyCaps',
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
                    }
                }

            });
        }
    </script>

@endsection