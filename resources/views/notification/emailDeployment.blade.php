<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/responsive.css') }}">
    <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"
  />

    <link rel="stylesheet" href="{{ asset('assets/libs/bootstrap-daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/@fortawesome/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/animate.css/animate.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/site.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/ac.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/stylesheet.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/stylesheet.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/css/jquery.comiseo.daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

</head>
<body>
    <p>{{$mailData->message}}</p>

    <table class="table ">
        <div class="row">
            <tr>
                <div class="col-1">
                    <td><i class="fa fa-key"></i> Acitvity Name</td>
                </div>
                <div class="col-11">
                    <td>: {{$mailData->activity_name}}</td>

                </div>

            </tr>
        </div>
        <div class="row">
            <tr>
                <div class="col-1">
                    <td><i class="fa fa-object-group"></i> Objective</td>
                </div>
                <div class="col-11">
                    <td class="text-wrap">: {{$mailData->objective}}</td>

                </div>
            </tr>
        </div>
        <div class="row">
            <tr>
                <div class="col-1">
                    <td><i class="fa fa-wrench"></i> Maintenance Detail</td>
                </div>
                <div class="col-11">
                    <td class="text-wrap">: {{$mailData->maintenance_detail}}</td>
                </div>
            </tr>
        </div>
        <div class="row">
            <tr>
                <div class="col-1">
                    <td><i class="fa fa-clock"></i> Maintenance Schedule</td>
                </div>
                <div class="col-11">
                    <td class="text-wrap">: {{$mailData->maintenance_start . " - " . $mailData->maintenance_end}}</td>
                </div>
            </tr>
        </div>
        <div class="row">
            <tr>
                <div class="col-1">
                    <td><i class="fa fa-clock"></i> Downtime</td>
                </div>
                <div class="col-11">
                    <td class="text-wrap">: {{$mailData->downtime}}</td>
                </div>
            </tr>
        </div>
        <div class="row">
            <tr>
                <div class="col-1">
                    <td><i class="fa fa-toolbox"></i> Service Impact</td>
                </div>
                <div class="col-11">
                    <td class="text-wrap">: {{$mailData->service_impact}} </td>
                </div>
            </tr>
        </div>
    </table>

    <script src="{{asset('assets/js/jquery.min.js')}}"></script>
<script src="{{ asset('assets/js/site.core.js') }}"></script>
<script src="{{ asset('assets/libs/progressbar.js/dist/progressbar.min.js') }}"></script>
<script src="{{ asset('assets/libs/apexcharts/dist/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/libs/moment/min/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/site.js') }}"></script>
<script src="{{ asset('assets/js/datatables.min.js') }}"></script>
<script src="{{ asset('assets/libs/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('assets/libs/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
<script src="{{ asset('assets/libs/select2/dist/js/select2.min.js') }}"></script>
<script src="{{asset('assets/libs/nicescroll/jquery.nicescroll.min.js')}}"></script>
<script src="{{ asset('assets/js/jquery.form.js')}}"></script>
<script src="{{ asset('assets/dashboard/js/Chart.js')}}"></script>
<script src="{{ asset('assets/dashboard/js/utils.js')}}"></script>
<script src="{{ asset('assets/dashboard/js/dashboard.js')}}"></script>
<script src="{{ asset('assets/js/excelexportjs.js')}}"></script>
<script src="{{ asset('assets/js/report.js')}}"></script>
</body>
</html>