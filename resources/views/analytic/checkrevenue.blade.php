@extends('layouts.admin')

@section('title')
     {{ __("Revenue Check") }}
@endsection

@section('pagetytle')
     {{ __("Check Revenue By Operator") }}
@endsection

@section('content')

    @include('analytic.partials.filtercheckrevenue')

    <div class="table-responsive shadow-sm mb-3 tableFixHead">
        <table class="table table-light table-bordered m-0 font-13 table-text-no-wrap" id="adsTbl">
            <thead class="thead-dark">
                <tr>
                    <th class="first-col" width="10%">Operator</th>
                    <th>Date</th>
                    <th>Total Service</th>
                    <th>Revenue</th>
                    <th>USD Rate</th>
                    <th>USD Revenue</th>
                </tr>
            </thead>
            @if(isset($data) && !empty($data))
            <tbody>
                <tr>
                    <td>{{$operator_name->operator_name}}</td>
                    <td>{{$data['date']}}</td>
                    <td>{{$data['total_service']}}</td>
                    <td>{{$data['revenue']}}</td>
                    <td>{{$data['usd_rate']}}</td>
                    <td>{{$data['usd_revenue']}}</td>
                </tr>
            </tbody>
            @endif
        </table>
    </div>
    @if(isset($services) && !empty($services))
    <div class="table-responsive shadow-sm mb-3 tableFixHead text-center">
        <table class="table table-light table-bordered m-0 font-13 table-text-no-wrap" id="adsTbl">
            <thead class="thead-dark">
                <tr>
                    <th class="first-col" width="10%">Service Id</th>
                    <th>Date</th>
                    <th>Fery Revenue</th>
                    <th>DB Revenue</th>
                    <th>Last Update Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($services as $service)
                    <tr>
                        <td>{{ $service['id_service'] }}</td>
                        <td>{{ $service['date'] }}</td>
                        <td>{{ $service['gross_revenue'] }}</td>
                        <td>{{ isset($db_revenue[$service['id_service']]) ? $db_revenue[$service['id_service']]->gros_rev : 0 }}</td>
                        <td>{{ isset($db_revenue[$service['id_service']]) ? $db_revenue[$service['id_service']]->updated_at : 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif


    </div>

    <button type="button" id="button" class="btn btn-danger btn-floating btn-sm btn-back-to-top-position up"><i class="fa fa-arrow-up"></i></button>

</div>

@endsection
