@extends('layouts.admin')

@section('title')
    {{ __('New Reconcialiation') }}
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
    </div>
</div>

<div class="text-center col-6" style="text-align: right !important; margin-bottom: 10px;">
    <a href="{{ url()->previous() }}" class="btn btn-link"><button type="submit" class="btn btn-primary">Back</button></a>
</div>

<div class="table-responsive shadow-sm mb-4 add-new-revenue">
    <table class="table table-light table-borderd m-0 font-13 table-text-no-wrap">
        <thead class="thead-dark sticky-col">
            <tr>
            @if($file)
            @foreach($file[0][0] as $key=>$value)
                <th>{{ ucfirst($key) }}</th>
            @endforeach
            @endif
            </tr>
        </thead>
        <tbody>
            @if($file)
            @foreach($file[0] as $data)
            <tr>
                @foreach($data as $value)
            	<td>{{ $value }}</td>
                @endforeach
            </tr>
            @endforeach
            @endif
        </tbody>
    </table>
</div>

@endsection
