@php 
    $signatures = App\Models\CronLog::select('signature')->distinct()->orderBy('signature', 'ASC')->get(); 
@endphp

@extends('layouts.admin')

@section('title')
    {{ __('Detele Cron') }}
@endsection

@section('content')
<div class="card shadow-sm mt-0">
    <form method="GET" action="{{ route('logfile.deleteCron') }}">
        <div class="card-body">
        <div class="row">
            <div class="col-lg-4">
                <div class="form-group">
                <label for="date">Date</label>
                <input type="date" name="date" id="date" placeholder="dd-mm-yyyy" value="<?php echo isset($_GET['date'])?$_GET['date']: '' ?>" min="1997-01-01" max="2030-12-31">
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                  <label for="company">Signature</label>
                  <select class="form-control select2" id="log_signatre"
                  name="log_signatre" type="text" value="<?php echo isset($_GET['log_signatre'])?$_GET['log_signatre']: '' ?>" >
                    <option value="" selected>Select Signature</option>
                    @foreach ($signatures as $findSignature)
                        <option value="{{$findSignature->signature}}" <?php echo isset($_GET['log_signatre']) && ($findSignature->signature == $_GET['log_signatre']) ? 'selected': '' ?>>{{$findSignature->signature}}</option>
                    @endforeach
                  </select>
                </div>
            </div>
            <div class="col-lg-5">
                <label class="invisible d-block">Search</label>
                <button type="submit" class="btn btn-success">Delete Cron</button>
                <a class="btn btn-secondary" href="{{route('logfile.cron') }}">Back</a>
            </div>
        </div>
    </form> 
</div>
@endsection