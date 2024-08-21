@extends('layouts.admin')

@section('title')
  {{ __('Reconcialiation Media') }}
@endsection

@section('content')
<div class="page-content">
    <div class="page-title" style="margin-bottom:25px">
        <div class="row justify-content-between align-items-center">
            <div class="col-xl-4 col-lg-4 col-md-4 d-flex align-items-center justify-content-between justify-content-md-start mb-3 mb-md-0">
                <div class="d-inline-block">
                    <h5 class="h4 d-inline-block font-weight-400 mb-0"><b>Reconcialiation Media</b></h5><br>
                    <p class="d-inline-block font-weight-200 mb-0">Add Reconcialiation data of Operator</p>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <form action="{{ route('finance.importReconcialiation') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <button type="submit" class="btn btn-success">Import</button>
                <input class="excel_input" type="file" name="file">
            </form>
        </div>
    </div>
</div>
@endsection