@extends('layouts.admin')

@section('title')
    {{ __('Project Management') }}
@endsection
@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <iframe src='https://linkit360.atlassian.net/secure/PortfolioEmbeddedReportView.jspa?r=Jk5sn' style="width: 100%" height='700' style='border:1px solid #ccc;'></iframe>
        </div>
    </div>
@endsection