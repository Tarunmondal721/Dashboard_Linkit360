@extends('layouts.admin')

@section('title')
    {{ __('Pivot Summary') }}
@endsection

@section('content')
<div class="page-content">
    <div class="page-title" style="margin-bottom:25px">
      <div class="row justify-content-between align-items-center">
        <div
          class="col-xl-4 col-lg-4 col-md-4 d-flex align-items-center justify-content-between justify-content-md-start mb-3 mb-md-0">
          <div class="d-inline-block">
            <!-- <h5 class="h4 d-inline-block font-weight-400 mb-0 "> PNL Summary -->
            </h5>
            <div>Pivot Summary of Campaign Data</div>
          </div>
        </div>
      </div>
    </div>


    @include('report.partials.filterPivotReport')
    


  </div>
@endsection
