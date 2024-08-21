@php
    $logo=asset(Storage::url('logo/'));
    $favicon=Utility::getValByName('company_favicon');
    $SITE_RTL = env('SITE_RTL');
    if($SITE_RTL == ''){
        $SITE_RTL = 'off';
    }

@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{$SITE_RTL == 'on'?'rtl':''}}">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title> @yield('title') &dash; {{(Utility::getValByName('header_text')) ? Utility::getValByName('header_text') : config('app.name', 'LeadGo')}}</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="icon" href="{{$logo.'/'.(isset($favicon) && !empty($favicon)?$favicon:'favicon.png')}}" type="image">
    <style>
            .tooltip-inner {
      background-color: #ffcc00 !important; /* Ganti dengan warna yang diinginkan */
      color: #000 !important; 
      font-size: 10px/* Ganti dengan warna teks yang diinginkan */
    }
    .tooltip-arrow::before {
      border-top-color: #ffcc00 !important; /* Ganti dengan warna yang diinginkan */
    }
        .btn-cstools {
            padding: 10px 23px;
            border-radius: 10px;
            color: #fff;
            line-height: 1.5 !important;
            transition: all 0.2s ease;
            font-size: 12px;
            border: none;
            margin-left: 10px;
            font-family: 'Montserrat-SemiBold' !important;
        }
        .truncate {
            max-width: 80px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .styleBackgroundColom {
            background-color: rgb(239, 239, 240);
        }
        .border-notification {
            border-left: 2px black solid;
            height: 100%;
            width: 0px;
            display: inline-block;
            margin-left: 7px;
        }
        .dataTables_wrapper .dt-buttons {
            position: relative;
            ;
            float: right;
            top: 0;
            margin-top: 8px;
            /* margin-right: 0; */
        }
        .tabs-custom {
            border-radius: 8px !important;
            overflow-x: auto;
            overflow-y: hidden;
            flex-wrap: nowrap !important;
        }
        .tabs-custom .nav-link {
            border: 0;
            border-radius: 8px !important;
            background: #d2eeff;
            white-space: nowrap !important;
            margin-right: 14px;
            color: #000;
        }
        .tabs-custom .nav-link.active {
            border: 0;
            border-radius: 8px !important;
        }
        .button-group-pills .btn-default {
            border-radius: 20px;
            line-height: 1.2;
            margin-bottom: 15px;
            margin-left: 10px;
            border: 1px solid #0f5ef7 !important;
            background-color: #FFF !important;
            color: 0f5ef7 !important;
        }
        .button-group-pills .btn.active {
            border-color: #0f5ef7 !important;
            background-color: #0f5ef7 !important;
            color: #FFF;
            box-shadow: none;
        }
        .button-group-pills .btn:hover {
            border-color: #0f5ef7;
            background-color: #0f5ef7;
            color: #FFF;
        }
        .button-group-pills input {
            position: absolute;
            display: none;
        }
        .button-group-pills input {
            position: absolute;
            display: none;
        }
        [data-toggle="buttons"]:not(.btn-group-colors)>.btn:not(.active) {
            color: #0f5ef7 !important;
        }
        .label-data {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 0;
        }
        .value-data {
            font-size: 14px;
        }
        .form-check-input,
        .form-check-label {
            cursor: pointer !important;
        }
        .form-check-input {
            margin-top: 8px !important;
        }
        .data-checklist {
            padding: 14px;
            margin-top: 14px;
            border: 1px solid #e9e9e9;
            border-radius: 8px;
        }
        .data-checklist .label-data {
            font-size: 18px;
        }
        .highcharts-credits {
            display: none !important;
        }
    </style>
    @stack('head')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

    <script>
        window.onload = function() {
            console.log("1")
            var iframe = document.getElementById('myIframe');
            var iframeDoc = iframe.contentDocument || iframe.contentWindow.document;

            // Menghapus elemen sidebar dan topbar
            var inputCSTool = iframeDoc.getElementById('inputCSTool');
            if (inputCSTool) {
                inputCSTool.remove();
                console.log("tes")
            }
            var footer_main = iframeDoc.getElementById('footer_main');
            if (footer_main) {
                footer_main.remove();
                console.log("tes")
            }
            var sidenav = iframeDoc.getElementById('sidenav-main');
            if (sidenav) {
                sidenav.remove();
                console.log("tes")
            }
            var navbar = iframeDoc.getElementById('navbar-main');
            if (navbar) {
                navbar.remove();
                console.log("tes")
            }
            var button = iframeDoc.getElementById('button');
            if (button) {
                button.remove();
                console.log("tes")
            }
        };
        </script>
    {{-- trix --}}
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.0/dist/trix.css">
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.0/dist/trix.umd.min.js"></script>

    {{-- datatable --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.4.1/css/buttons.dataTables.min.css">


    <link rel="stylesheet" href="{{ asset('assets/css/jquery.comiseo.daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    @if($SITE_RTL=='on')
        <link rel="stylesheet" href="{{ asset('css/bootstrap-rtl.css') }}">
    @endif
    <meta name="url" content="{{ url('').'/'.config('chatify.routes.prefix') }}" data-user="{{ Auth::user()->id }}">

    {{-- scripts --}}
    <script src="{{ asset('js/chatify/autosize.js') }}"></script>
    <script src='https://unpkg.com/nprogress@0.2.0/nprogress.js'></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
</head>

<body class="application application-offset">
    <div class="loader-bg">

    <div class="loader-logo">
      <img src="{{ asset('assets/images/logo_linkit_icon.png') }}" alt="logo">
    </div>
    <div class="dot-padding">
      <div class='threedotloader'>
        <div class='dot'></div>
        <div class='dot'></div>
        <div class='dot'></div>
      </div>
    </div>
    <div class="loader-text">Awesome things are getting ready...</div>

  </div>
<div class="container-fluid container-application">
    @include('partials.admin.navbar')
    <div class="main-content position-relative">
        <div class="modal fade" id="commonModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div>
                        <h4 class="h4 font-weight-400 float-left modal-title"></h4>
                        <a href="#" class="more-text widget-text float-right close-icon" data-dismiss="modal" aria-label="Close">{{__('Close')}}</a>
                    </div>
                    <div class="modal-body">
                    </div>
                </div>
            </div>
        </div>
        @include('partials.admin.topbar')
        <div class="page-content">
            <div class="page-title">
                <div class="row justify-content-between align-items-center">
                    <div class="col-xl-4 col-lg-4 col-md-4 d-flex align-items-center justify-content-between justify-content-md-start mb-3 mb-md-0">
                        <div class="d-inline-block">
                            {{-- <h5 class="h4 d-inline-block font-weight-400 mb-0 ">@yield('title')</h5> --}}
                        </div>
                    </div>
                    <div class="col-xl-8 col-lg-8 col-md-8 d-flex align-items-center justify-content-between justify-content-md-end">
                        @yield('action-button')
                    </div>
                </div>
            </div>
            @yield('content')
        </div>
        @include('partials.admin.footer')
    </div>
</div>

<button type="button" id="button" class="btn btn-danger btn-floating btn-sm btn-back-to-top-position up"><i class="fa fa-arrow-up"></i></button>

<div id="omnisearch" class="omnisearch">
    <div class="container">
        <div class="omnisearch-form">
            <div class="form-group">
                <div class="input-group input-group-merge input-group-flush">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                    <input type="text" class="form-control search_keyword" placeholder="{{__('Type and search By Deal, Lead and Tasks.')}}">
                </div>
            </div>
        </div>
        <div class="omnisearch-suggestions">
            <div class="row">
                <div class="col-sm-12">
                    <ul class="list-unstyled mb-0 search-output text-sm">
                        <li>
                            <a class="list-link pl-4" href="#">
                                <i class="fas fa-search"></i>
                                <span>j</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- General JS Scripts -->
{{-- <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.0/css/toastr.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.0/js/toastr.js"></script> --}}
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

<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.4.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.bootstrap4.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.4.1/js/buttons.flash.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.4.1/js/buttons.html5.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.4.1/js/buttons.print.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tableexport.jquery.plugin@1.26.0/tableExport.min.js"></script>

@include('Chatify::layouts.footerLinks')
@if(Utility::getValByName('gdpr_cookie') == 'on')
    <script type="text/javascript">

        var defaults = {
            'messageLocales': {
                /*'en': 'We use cookies to make sure you can have the best experience on our website. If you continue to use this site we assume that you will be happy with it.'*/
                'en': "{{Utility::getValByName('cookie_text')}}"
            },
            'buttonLocales': {
                'en': 'Ok'
            },
            'cookieNoticePosition': 'bottom',
            'learnMoreLinkEnabled': false,
            'learnMoreLinkHref': '/cookie-banner-information.html',
            'learnMoreLinkText': {
                'it': 'Saperne di più',
                'en': 'Learn more',
                'de': 'Mehr erfahren',
                'fr': 'En savoir plus'
            },
            'buttonLocales': {
                'en': 'Ok'
            },
            'expiresIn': 30,
            'buttonBgColor': '#d35400',
            'buttonTextColor': '#fff',
            'noticeBgColor': '#051c4b',
            'noticeTextColor': '#fff',
            'linkColor': '#009fdd'
        };
    </script>
    <script>
        $(document).ready(function() {
            toastr.options.timeOut = 2000;
            @if (Session::has('error'))
                toastr.error('{{ Session::get('error') }}');
            @elseif(Session::has('success'))
                toastr.success('{{ Session::get('success') }}');
            @endif
        });
    </script>
    <script src="{{ asset('assets/js/cookie.notice.js')}}"></script>
@endif

{{-- Pusher JS--}}
@if(\Auth::user()->type != 'Super Admin')
    <script src="https://js.pusher.com/5.0/pusher.min.js"></script>
    <script>
        $(document).ready(function () {
            pushNotification('{{ Auth::id() }}');
        });

        function pushNotification(id) {
            // ajax setup form csrf token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Enable pusher logging - don't include this in production
            Pusher.logToConsole = false;

            var pusher = new Pusher('{{env('PUSHER_APP_KEY')}}', {
                cluster: '{{env('PUSHER_APP_CLUSTER')}}',
                forceTLS: true
            });

            // Pusher Notification
            var channel = pusher.subscribe('send_notification');
            channel.bind('notification', function (data) {
                if (id == data.user_id) {
                    $(".notification-toggle").addClass('beep');
                    $(".notification-dropdown #notification-list").prepend(data.html);
                    $(".notification-dropdown #notification-list-mini").prepend(data.html);
                }
            });

            // Pusher Message
            var msgChannel = pusher.subscribe('my-channel');
            msgChannel.bind('my-chat', function (data) {
                if (id == data.to) {
                    getChat();
                }
            });
        }

        // Mark As Read Notification
        $(document).on("click", ".mark_all_as_read", function () {
            $.ajax({
                url: '{{route('notification.seen',\Auth::user()->id)}}',
                type: "get",
                cache: false,
                success: function (data) {
                    $('.notification-dropdown #notification-list').html('');
                    $(".notification-toggle").removeClass('beep');
                    $('.notification-dropdown #notification-list-mini').html('');
                }
            })
        });

        // Get chat for top ox
        // function getChat() {
        //     $.ajax({
        //         url: '{{route('message.data')}}',
        //         type: "get",
        //         cache: false,
        //         success: function (data) {
        //             if (data.length != 0) {
        //                 $(".message-toggle-msg").addClass('beep');
        //                 $(".dropdown-list-message-msg").html(data);
        //             }
        //         }
        //     })
        // }

        // getChat();

        // $(document).on("click", ".mark_all_as_read_message", function () {
        //     $.ajax({
        //         url: '{{route('message.seen')}}',
        //         type: "get",
        //         cache: false,
        //         success: function (data) {
        //             $('.dropdown-list-message-msg').html('');
        //             $(".message-toggle-msg").removeClass('beep');
        //         }
        //     })
        // });

        var date_picker_locale = {
            format: 'YYYY-MM-DD',
            daysOfWeek: [
                "{{__('Su')}}",
                "{{__('Mon')}}",
                "{{__('Tue')}}",
                "{{__('Wed')}}",
                "{{__('Thu')}}",
                "{{__('Fri')}}",
                "{{__('Sat')}}"
            ],

            monthNames: [
                "{{__('January')}}",
                "{{__('February')}}",
                "{{__('March')}}",
                "{{__('April')}}",
                "{{__('May')}}",
                "{{__('June')}}",
                "{{__('July')}}",
                "{{__('August')}}",
                "{{__('September')}}",
                "{{__('October')}}",
                "{{__('November')}}",
                "{{__('December')}}"
            ],
        };

    </script>
@endif


<script>
    var toster_pos="{{$SITE_RTL =='on' ?'left' : 'right'}}";
</script>
<script src="{{asset('assets/js/custom.js')}}"></script>

@if ($message = Session::get('success'))
    <script>show_toastr('Success', '{!! $message !!}', 'success')</script>
@endif

@if ($message = Session::get('error'))
    <script>show_toastr('Error', '{!! $message !!}', 'error')</script>
@endif

@if ($message = Session::get('info'))
    <script>show_toastr('Info', '{!! $message !!}', 'info')</script>
@endif

<script>
    var calender_header = {
        today: '{{__('today')}}',
        month: '{{__('month')}}',
        week: '{{__('week')}}',
        day: '{{__('day')}}',
        list: '{{__('list')}}'
    };

    var chart_keyword = [
        "{{__('Wed')}}",
        "{{__('Tue')}}",
        "{{__('Mon')}}",
        "{{__('Sun')}}",
        "{{__('Sat')}}",
        "{{__('Fri')}}",
        "{{__('Thu')}}",
    ];
</script>

@stack('script')

<script>
    $(document).ready(function () {
        $("#selectUser").select2();
        // $("#selectEmail").select2();
        $("#selectLevel").select2();

        $("#currency").select2();
        // $("#service_price").select2();

        if ($('.dataTable').length > 0) {
            $(".dataTable").dataTable({
                language: {
                    "lengthMenu": "{{__('Display')}} _MENU_ {{__('records per page')}}",
                    "zeroRecords": "{{__('No data available in table')}}",
                    "info": "{{__('Showing page')}} _PAGE_ {{__('of')}} _PAGES_",
                    "infoEmpty": "{{__('No page available')}}",
                    "infoFiltered": "({{__('filtered from')}} _MAX_ {{__('total records')}})",
                    "paginate": {
                        "previous": "{{__('Previous')}}",
                        "next": "{{__('Next')}}",
                        "last": "{{__('Last')}}"
                    }
                },
                order: [[ 1, "desc" ]],
                // columnDefs: [
                //    { orderable: false, targets: 1 }
                // ],

                aoColumnDefs: [
                    {"orderable": false, "bSortable": false, "aTargets": [ 0, 1] },
                ],

                // { orderable: false, targets: [0,1], bSortable: false}
                drawCallback: function(){
                    $('.paginate_button:not(.disabled)', this.api().table().container())
                    .on('click', function(){
                        var status = 0;

                            $( '.sub_chk' ).each( function(e) {
                                if ($(this).prop('checked') == true){
                                    status = 1;
                                }else{
                                  $('#select-all').prop('checked',false);
                                  return false;
                                }
                            });
                            if(status == 1){
                                $('#select-all').prop('checked',true);
                                  return false;
                            }else{
                                $('#select-all').prop('checked',false);
                                  return false;
                            }

                    });
                }

                // "sPaginationType": "full_numbers",
                            // "bDestroy": true,
                            // "aoColumnDefs": [
                            //   { 'bSortable': false, 'aTargets': [0] }
                            // ]
            })
        }
        if ($('.datatable_activity_cs').length > 0) {
            var tableCsActivity =  $(".datatable_activity_cs").dataTable({
                // "searching": false,
                // dom: 'Blrftip',
                // Bfrtip
                aoColumnDefs: [
                    {"orderable": false, "bSortable": false, "aTargets": [6 ] },
                ],
                buttons: [
                    {
                        extend: 'csv',
                        text: '<i class="fas fa-file-csv"></i> CSV',
                        titleAttr: 'CSV',
                        className: " mt-4 btn-right btn-sm rounded",
                        // exportOptions: {
                        //     columns: ':visible'
                        // }
                    },
                ],
                language: {
                    "lengthMenu": "{{__('Display')}} _MENU_ {{__('records per page')}}",
                    "zeroRecords": "{{__('No data available in table')}}",
                    "info": "{{__('Showing page')}} _PAGE_ {{__('of')}} _PAGES_",
                    "infoEmpty": "{{__('No page available')}}",
                    "infoFiltered": "({{__('filtered from')}} _MAX_ {{__('total records')}})",
                    "paginate": {
                        "previous": "{{__('Previous')}}",
                        "next": "{{__('Next')}}",
                        "last": "{{__('Last')}}"
                    }
                },
                pageLength: 5,
                // lengthMenu: [[5, 10, 20, -1], [5, 10, 20, "Todos"]],
                lengthMenu: [[5, 10, 25, 50], [5, 10, 25, 50]],
                order: [[ 0, "desc" ]],
                // columnDefs: [
                //    { orderable: false, targets: 1 }
                // ],

                // columnDefs:[{targets:9,className:"truncate"}],
                autoWidth: true,
                // { orderable: false, targets: [0,1], bSortable: false}
                drawCallback: function(){
                    $('.paginate_button:not(.disabled)', this.api().table().container())
                    .on('click', function(){
                        var status = 0;

                            $( '.sub_chk' ).each( function(e) {
                                if ($(this).prop('checked') == true){
                                    status = 1;
                                }else{
                                  $('#select-all').prop('checked',false);
                                  return false;
                                }
                            });
                            if(status == 1){
                                $('#select-all').prop('checked',true);
                                  return false;
                            }else{
                                $('#select-all').prop('checked',false);
                                  return false;
                            }

                    });
                }

                // "sPaginationType": "full_numbers",
                            // "bDestroy": true,
                            // "aoColumnDefs": [
                            //   { 'bSortable': false, 'aTargets': [0] }
                            // ]
            })
            $('select#name_cs').change( function () {  tableCsActivity.fnFilter( this.value, 1 );  } );
            // $('select#action').change( function () {  tableCsActivity.fnFilter( this.value, 3 ); });

            $('select#action_cs').change( function () {
                if(this.value == "") {
                    tableCsActivity.fnFilter(this.value, 2);

                }else {
                    tableCsActivity.fnFilter("^"+ this.value+"$", 2, true, false);
                }

            });
            $('select#operator_cs').change( function () {
                if(this.value == "") {
                    tableCsActivity.fnFilter(this.value, 3);
                    console.log("test");
                }else {
                    console.log(this.value)
                    tableCsActivity.fnFilter("^"+ this.value+"$", 3, true, false);
                }

            });
            $('select#service_cs').change( function () {
                if(this.value == "") {
                    tableCsActivity.fnFilter(this.value, 4);
                    console.log("test");
                }else {
                    console.log(this.value)
                    tableCsActivity.fnFilter("^"+ this.value+"$", 4, true, false);
                }

            });
            // $(".truncate").on("click", function() {
            //     var index = $(this).index() + 1;
            //     $('table tr td:nth-child(' + index  + ')').toggleClass("truncate");
            //     $('table tr td:nth-child(' + index  + ')').toggleClass("text-wrap");
            // });
            // $("#buttonCsvLogs").on("click", function() {
            //     $('.datatable_activity_cs').DataTable().buttons(0,0).trigger()
            // });
        }
        if ($('.dataTable_logs').length > 0) {
            var tableLogs =  $(".dataTable_logs").dataTable({
                // "searching": false,
                // dom: 'Blrftip',
                // Bfrtip

                buttons: [
                    {
                        extend: 'csv',
                        text: '<i class="fas fa-file-csv"></i> CSV',
                        titleAttr: 'CSV',
                        className: " mt-4 btn-right btn-sm rounded",
                        // exportOptions: {
                        //     columns: ':visible'
                        // }
                    },
                ],
                language: {
                    "lengthMenu": "{{__('Display')}} _MENU_ {{__('records per page')}}",
                    "zeroRecords": "{{__('No data available in table')}}",
                    "info": "{{__('Showing page')}} _PAGE_ {{__('of')}} _PAGES_",
                    "infoEmpty": "{{__('No page available')}}",
                    "infoFiltered": "({{__('filtered from')}} _MAX_ {{__('total records')}})",
                    "paginate": {
                        "previous": "{{__('Previous')}}",
                        "next": "{{__('Next')}}",
                        "last": "{{__('Last')}}"
                    }
                },
                pageLength: 10,
                order: [[ 0, "desc" ]],
                // columnDefs: [
                //    { orderable: false, targets: 1 }
                // ],

                // columnDefs:[{targets:9,className:"truncate"}],
                autoWidth: true,
                // { orderable: false, targets: [0,1], bSortable: false}
                drawCallback: function(){
                    $('.paginate_button:not(.disabled)', this.api().table().container())
                    .on('click', function(){
                        var status = 0;

                            $( '.sub_chk' ).each( function(e) {
                                if ($(this).prop('checked') == true){
                                    status = 1;
                                }else{
                                  $('#select-all').prop('checked',false);
                                  return false;
                                }
                            });
                            if(status == 1){
                                $('#select-all').prop('checked',true);
                                  return false;
                            }else{
                                $('#select-all').prop('checked',false);
                                  return false;
                            }

                    });
                }

                // "sPaginationType": "full_numbers",
                            // "bDestroy": true,
                            // "aoColumnDefs": [
                            //   { 'bSortable': false, 'aTargets': [0] }
                            // ]
            })
            $('select#service').change( function () {  tableLogs.fnFilter( this.value, 2 );  } );
            // $('select#action').change( function () {  tableLogs.fnFilter( this.value, 3 ); });

            $('select#action').change( function () {
                if(this.value == "") {
                    tableLogs.fnFilter(this.value, 3);

                }else {
                    tableLogs.fnFilter("^"+ this.value+"$", 3, true, false);
                }

            });
            $('select#status').change( function () {
                if(this.value == "") {
                    tableLogs.fnFilter(this.value, 6);
                    console.log("test");
                }else {
                    console.log(this.value)
                    tableLogs.fnFilter("^"+ this.value+"$", 6, true, false);
                }

            });
            $('select#channel').change( function () {
                if(this.value == "") {
                    tableLogs.fnFilter(this.value, 4);
                    console.log("test");
                }else {
                    console.log(this.value)
                    tableLogs.fnFilter("^"+ this.value+"$", 4, true, false);
                }

            });
            $(".truncate").on("click", function() {
                var index = $(this).index() + 1;
                $('table tr td:nth-child(' + index  + ')').toggleClass("truncate");
                $('table tr td:nth-child(' + index  + ')').toggleClass("text-wrap");
            });
            $("#buttonCsvLogs").on("click", function() {
                $('.dataTable_logs').DataTable().buttons(0,0).trigger()
            });
        }
        if($(".datatableMsisdn").length>0) {
            var tableMsisdn =  $(".datatableMsisdn").dataTable({
                // scrollX: true,
                buttons : [
                    {
                        extend: 'csv',
                        text: '<i class="fas fa-file-csv"></i> CSV',
                        titleAttr: 'CSV',
                        className: " mt-4 btn-right btn-sm rounded",
                        filename: function() {
                            // Define your custom file name here
                            return 'list_msisdn';
                        }
                        // exportOptions: {
                        //     columns: ':visible'
                        // }
                    },
                ],
                pageLength: 10,
            })

            $("#buttonExportMsisdn").on("click", function() {
                $('.datatableMsisdn').DataTable().buttons(0,0).trigger()

            });
        }

        if($(".datatableMsisdnCsTool").length>0) {
            var msisdnCstool =  $(".datatableMsisdnCsTool").dataTable({
                // scrollX: true,
                buttons : [
                    {
                        extend: 'csv',
                        text: '<i class="fas fa-file-csv"></i> CSV',
                        titleAttr: 'CSV',
                        className: " mt-4 btn-right btn-sm rounded",
                        filename: function() {
                            // Define your custom file name here
                            return'detail_msisdn';
                        }
                        // exportOptions: {
                        //     columns: ':visible'
                        // }
                    },
                ],
                pageLength: 10,
            })
            $("#buttonCsTool").on("click", function() {
                $('.datatableMsisdnCsTool').DataTable().buttons(0,0).trigger()

            });
            $('select#select_source').change( function () {
                if(this.value == "") {
                    msisdnCstool.fnFilter(this.value, 1);
                }else {
                    msisdnCstool.fnFilter("^"+ this.value+"$", 1, true, false);
                }

            });
            $('select#select_event').change( function () {
                if(this.value == "") {
                    msisdnCstool.fnFilter(this.value, 2);
                }else {
                    msisdnCstool.fnFilter("^"+ this.value+"$", 2, true, false);
                }

            });
            $('select#select_publisher').change( function () {
                if(this.value == "") {
                    msisdnCstool.fnFilter(this.value, 6);
                }else {
                    msisdnCstool.fnFilter("^"+ this.value+"$", 6, true, false);
                }

            });
            $('select#select_handset').change( function () {
                if(this.value == "") {
                    msisdnCstool.fnFilter(this.value, 7);
                }else {
                    msisdnCstool.fnFilter("^"+ this.value+"$", 7, true, false);
                }

            });
            $('select#select_browser').change( function () {
                if(this.value == "") {
                    msisdnCstool.fnFilter(this.value, 8);
                }else {
                    msisdnCstool.fnFilter("^"+ this.value+"$", 8, true, false);
                }

            });
        }
        if ($('.datatable_arpu').length > 0) {
            var tableArpu =  $(".datatable_arpu").dataTable({
                "searching": false,
                "paging": false,
                // scrollX: true,
                info: false,
                pageLength: 50,
                order: [[ 3, "desc" ]],
                // dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'csv',
                    },
                ],
                aoColumnDefs: [
                    {"orderable": false, "bSortable": false, "aTargets": [ 0, 1,2 ] },
                ],
            })

            $("#buttonCSV").on("click", function() {
                $('.datatable_arpu').DataTable().buttons(0,0).trigger()
            });
        }
        if ($('.dataTable_edit_dept').length > 0) {
            $(".dataTable_edit_dept").dataTable({
                language: {
                    "lengthMenu": "{{__('Display')}} _MENU_ {{__('records per page')}}",
                    "zeroRecords": "{{__('No data available in table')}}",
                    "info": "{{__('Showing page')}} _PAGE_ {{__('of')}} _PAGES_",
                    "infoEmpty": "{{__('No page available')}}",
                    "infoFiltered": "({{__('filtered from')}} _MAX_ {{__('total records')}})",
                    "paginate": {
                        "previous": "{{__('Previous')}}",
                        "next": "{{__('Next')}}",
                        "last": "{{__('Last')}}"
                    }
                },
                order :[],

                aoColumnDefs: [
                    {"orderable": false, "bSortable": false, "aTargets": [ 1] },
                ],


                drawCallback: function(){
                    $('.paginate_button:not(.disabled)', this.api().table().container())
                    .on('click', function(){
                        var status = 0;

                            $( '.sub_chk' ).each( function(e) {
                                if ($(this).prop('checked') == true){
                                    status = 1;
                                }else{
                                  $('#select-all').prop('checked',false);
                                  return false;
                                }
                            });
                            if(status == 1){
                                $('#select-all').prop('checked',true);
                                  return false;
                            }else{
                                $('#select-all').prop('checked',false);
                                  return false;
                            }

                    });
                }

                // "sPaginationType": "full_numbers",
                            // "bDestroy": true,
                            // "aoColumnDefs": [
                            //   { 'bSortable': false, 'aTargets': [0] }
                            // ]
            })
        }


        @if(Auth::user()->type != 'Super Admin')
        $(document).on('keyup', '.search_keyword', function () {
            search_data($(this).val());
        });

        if ($(".top-5-scroll").length) {
            $(".top-5-scroll").css({
                height: 315
            }).niceScroll();
        }
        @endif
    });

    @if(Auth::user()->type != 'Super Admin')
    // Common main search
    var currentRequest = null;

    function search_data(keyword = '') {
        currentRequest = $.ajax({
            url: '{{ route('search.json') }}',
            data: {keyword: keyword},
            beforeSend: function () {
                if (currentRequest != null) {
                    currentRequest.abort();
                }
            },
            success: function (data) {
                $('.search-output').html(data);
            }
        });
    }
    @endif
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
      var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
      var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
      });
    });
  </script>
<script >
    $(function() {
      $('#daterange').daterangepicker({
        opens: 'left'
      }, function(start, end, label) {
        console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
      });
    });
    </script>

<script>
    var btn = $("#button");

    $(window).scroll(function () {
        if ($(window).scrollTop() > 200) {
            btn.addClass("show");
        } else {
            btn.removeClass("show");
        }
    });

    btn.on("click", function (e) {
        e.preventDefault();
        $("html, body").animate({ scrollTop: 0 }, "200");
    });


    $(document).ready(function(){
        $(".loader-bg").fadeOut('slow')
    });

</script>
</body>
</html>
