@extends('layouts.admin')

@section('title')

    {{ __('New Revenue Reconcile') }}

@endsection

@section('content')
    
<div class="page-title" style="margin-bottom:25px">
    <div class="row justify-content-between align-items-center">
      <div class="col-xl-4 col-lg-4 col-md-4 d-flex align-items-center justify-content-between justify-content-md-start mb-3 mb-md-0">
        <div class="d-inline-block">
          <h5 class="h4 d-inline-block font-weight-400 mb-0"><b>Uploaded File Data</b></h5><br>
          <p class="d-inline-block font-weight-200 mb-0">File data uploaded successfully</p>
        </div>
      </div>
      <div class="col-xl-8 col-lg-8 col-md-8 d-flex align-items-center justify-content-between justify-content-md-end">
      </div>
    </div>
  </div>
    <div class="text-center col-12" style="text-align: right !important; margin-bottom: 10px;">
        <a href="{{ url()->previous() }}" class="btn btn-link"><button type="submit" class="btn btn-primary">Back</button></a>
    </div>
       <div class="table-responsive shadow-sm mb-4 add-new-revenue">
       <table class="table table-light table-borderd m-0 font-13 table-text-no-wrap">
            <thead class="thead-dark sticky-col">
                <tr>
                    <th>Year</th>
                    @if(isset($serviceWise))
                    <th>Country</th>
                    <th>Operator</th>
                    <th>service</th>
                    @else
                    <th>Company</th>
                    @endif
                    <th>Name</th>
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
                @if($file)
                @foreach($file[0] as $data)
                <tr>
                	<td>{{ $data['year'] }}</td>
                    @if(isset($serviceWise))
                	<td>{{ $data['country'] }}</td>
                	<td>{{ $data['operator'] }}</td>
                	<td>{{ $data['service'] }}</td>
                    @else
                    <td>{{ $data['company'] }}</td>
                    @endif
                	<td>{{ $data['name'] }}</td>
                	<td>{{ $data['december'] }}</td>
                	<td>{{ $data['november'] }}</td>
                	<td>{{ $data['october'] }}</td>
                	<td>{{ $data['september'] }}</td>
                	<td>{{ $data['august'] }}</td>
                	<td>{{ $data['july'] }}</td>
                	<td>{{ $data['june'] }}</td>
                	<td>{{ $data['may'] }}</td>
                	<td>{{ $data['april'] }}</td>
                	<td>{{ $data['march'] }}</td>
                	<td>{{ $data['february'] }}</td>
                	<td>{{ $data['january'] }}</td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
      </div>

      @endsection
