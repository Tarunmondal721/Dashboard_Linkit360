

@extends('layouts.admin')

@section('title')
    {{ __('Notification Management') }}
@endsection

@section('content')

    <div class="page-content page-content-center">
        <form id="form-notification" action="{{route('update.notification.incident')}}" method="post" onsubmit="return notificationSubmit()">
        @csrf
        <div class="page-title" style="margin-bottom:25px">
        <div class="row justify-content-between align-items-center mb-5 ">
            <div
                class="col-xl-4 col-lg-4 col-md-4 d-flex align-items-center justify-content-between justify-content-md-start mb-3 mb-md-0">
                <div class="d-inline-block">
                    <h5 class="h4 d-inline-block font-weight-400 mb-0"><b>Update Notification Incident</b></h5>
                </div>
            </div>
            <div
                class="col-xl-8 col-lg-8 col-md-8 d-flex align-items-center justify-content-between justify-content-md-end">
            </div>
        </div>
        <div class="card shadow-sm mt-0">
        <div class="card-body">
            
            <div class="row">
                <input type="hidden" name="id" value="{{$notification->id}}">
                <div class="col-lg-6" style="padding-top: 10px;">
                    <label>Country*</label>
                    <select class="form-control select2" id="country_name" name="country" onchange="countryChange()">
                        <option value="{{$notification->country_id}}">{{$country->country}}</option>
                        @foreach ($countrys as $country)
                            <option value="{{$country->country_id}}">{{$country->country_name}}</option>
                        @endforeach
                    </select>
                    <span class="gu-hide" style="color: red;"
                        id="errorCountry">{{ __('*Please select country') }}</span>
                </div>
                
                <div class="col-lg-6" style="padding-top: 10px;" id="form-status">
                    <label>Status</label>
                    <select  class="form-control select2" id="status" name="status" >
                        <option value="{{$notification->status == "" || $notification == null  ? "" : $notification->status }}"> {{$notification->status == "" || $notification == null ? "Choose Status" : $notification->status}}</option>
                        <option value="Solve">Solve</option>       
                        <option value="Not Yet Solve">Not Yet Solve</option>
                    </select>
                </div>
                <div class="col-lg-6" style="padding-top: 10px;" id="form-email">
                    <div class="form-group">
                    <label>Email</label>
                        <input type="text" class="form-control select2" id="email"
                        name="email" value="{{old('email', $notification->email)}}" placeholder="Input email">
                    <span class="gu-hide" style="color: red;"
                    id="erroremail">{{ __('*Please input email') }}</span>
                        
                    </div>
                </div>
                <div class="col-lg-6" style="padding-top: 10px;" id="form-subject">
                    <div class="form-group">
                    <label>Subject</label>
                        <input type="text" class="form-control select2" id="subject"
                        name="subject" value="{{$notification->subject}}" placeholder="Input subject">
                    <span class="gu-hide" style="color: red;"
                    id="errorSubject">{{ __('*Please input subject') }}</span>
                        
                    </div>
                </div>
                <div class="col-lg-6" style="padding-top: 10px;" id="form-number-ticket">
                    <div class="form-group">
                    <label>Number Ticket</label>
                        <input type="text" class="form-control select2" id="number_ticket"
                        name="number_ticket" placeholder="Input number ticket"  value="{{$notification->number_ticket}}">
                    <span class="gu-hide" style="color: red;"
                    id="errorNumberTicket">{{ __('*Please input number ticket') }}</span>
                        
                    </div>
                </div>
                <div class="col-lg-6" style="padding-top: 10px;" id="form-created-by">
                    <div class="form-group">
                    <label>Created By</label>
                        <input type="text" class="form-control select2" id="created_by"
                        name="created_by" placeholder="Input created" value="{{$notification->created_by}}">
                    <span class="gu-hide" style="color: red;"
                    id="errorCreatedBy">{{ __('*Please input created') }}</span>
                        
                    </div>
                </div>
                <div class="col-lg-6" style="padding-top: 10px;" id="form-classification">
                    <div class="form-group">
                    <label>Classification</label>
                        <input type="text" class="form-control select2" id="classification"
                        name="classification" placeholder="Input classification" value="{{$notification->classification}}">
                    <span class="gu-hide" style="color: red;"
                    id="errorClassification">{{ __('*Please input classification') }}</span>
                        
                    </div>
                </div>
                <div class="col-lg-6" style="padding-top: 10px;" id="form-severty">
                    <div class="form-group">
                    <label>Severty</label>
                        <input type="text" class="form-control select2" id="severty"
                        name="severty" placeholder="Input severty" value="{{$notification->severty}}">
                    <span class="gu-hide" style="color: red;"
                    id="errorSeverty">{{ __('*Please input severty') }}</span>
                        
                    </div>
                </div>
                <div class="col-lg-12" style="padding-top: 10px;" id="form-time-incident">
                    <div class="form-group">
                        <label>Incident Time</label>
                        <input class="form-control time-incident" value="{{$notification->time_incident}}"  id="time_incident" name="time_incident" type="text" style="height: 40px;">
                        <span class="gu-hide" style="color: red;"
                        id="errorTimeIncident">{{ __('*Please input incident time') }}</span>
                        
                    </div>
                </div>
                <div class="col-lg-12" style="padding-top: 10px;" id="form-details-incident">
                    <div class="form-group">
                    <label>Incident Details</label>
                    <input id="details_incident" value="{!! $notification->details !!}" type="hidden" name="details">
                    <trix-editor input="details_incident"></trix-editor>                   
                    <span class="gu-hide" style="color: red;"
                    id="errorDetailIncident">{{ __('*Please input details ') }}</span>

                    </div>
                </div>
            </div>
            <div class="row">

                <div class="col-md-12" style="text-align:right;">
                    {{-- <label class="invisible d-block">Button</label> --}}
                    <button type="submit" id="button-notification"  class="btn badge-blue">Update</button>
                </div>

            </div>
        </div>
    </div>
    </div>
    </form>

    </div>

    <script>
        var baseUrl = window.location.origin + "/";
        

        function countryChange(){
            var e = document.getElementById("country_name");
            var value = e.value;
            console.log(value);
            $.ajax({
                type: "POST",
                url: baseUrl+"report/operator",
                data:{'id':value},
                dataType: "json",
                success: function (responses) {
                    document.getElementById('operator_name').innerHTML ='<option value="">Select Operator</option>';
                    $.each(responses, function(index,response){
                        $("#operator_name").append('<option value="'+response.id_operator+'">'+response.operator_name+'</option>');
                    });

                },
            });
        }
    </script>
    <script>
        var base_url = window.location.origin;
        function notificationSubmit(){

            var country = $("#country_name").val();
            if(country == "") {
            $("#errorCountry").removeClass("gu-hide");
            return false;
            }else {
            $("#errorCountry").addClass("gu-hide");

            }
            // var operator = $("#operator_name").val();
            // if(operator == "") {
            //   $("#errorOperator").removeClass("gu-hide");
            //   return false;
            // }else {
            //   $("#errorOperator").addClass("gu-hide");

            // }
            var email = $("#email").val();
            if(email == "") {
            $("#erroremail").removeClass("gu-hide");
            return false;
            }else {
            $("#erroremail").addClass("gu-hide");

            }
            var subject = $("#subject").val();
            if(subject == "") {
            $("#errorSubject").removeClass("gu-hide");
            return false;
            }else {
            $("#errorSubject").addClass("gu-hide");

            }
            var number_ticket = $("#number_ticket").val();
            if(number_ticket == "") {
            $("#errorNumberTicket").removeClass("gu-hide");
            return false;
            }else {
            $("#errorNumberTicket").addClass("gu-hide");

            }
            var classification = $("#classification").val();
            if(classification == "") {
            $("#errorClassification").removeClass("gu-hide");
            return false;
            }else {
            $("#errorClassification").addClass("gu-hide");

            }


            var severty = $("#severty").val();
            if(severty == "") {
            $("#errorSeverty").removeClass("gu-hide");
            return false;
            }else {
            $("#errorSeverty").addClass("gu-hide");

            }
            var timeIncident = $("#time_incident").val();
            if(timeIncident == "") {
            $("#errorTimeIncident").removeClass("gu-hide");
            return false;
            }else {
            $("#errorTimeIncident").addClass("gu-hide");

            }
            var detaiIncident = $("#details_incident").val();
            if(detaiIncident == "") {
            $("#errorDetailIncident").removeClass("gu-hide");
            return false;
            }else {
            $("#errorDetailIncident").addClass("gu-hide");

            }

        }
    </script>
    <script>
        $( document ).ready(function() {
            if ($(".time-incident").length) 
    {
        $('.time-incident').daterangepicker({
        locale: date_picker_locale,
        autoUpdateInput: false,
        singleDatePicker: true,
        timePicker: true,
            timePicker24Hour: true,

        });
        $('.time-incident').on('apply.daterangepicker', function(ev, picker) {

        console.log("test");
        $('#time_incident').val(picker.startDate.format('YYYY-MM-DD HH:mm'));
        });
        }

        });
    </script>
@endsection
