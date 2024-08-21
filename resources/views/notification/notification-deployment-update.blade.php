

@extends('layouts.admin')

@section('title')
    {{ __('Notification Management') }}
@endsection

@section('content')

  <div class="page-content page-content-center">
    <form id="form-notification" action="{{route('update.notification.deployment')}}" method="post" onsubmit="return notificationSubmit()">
    @csrf
    <div class="page-title" style="margin-bottom:25px">
      <div class="row justify-content-between align-items-center mb-5 ">
          <div
              class="col-xl-4 col-lg-4 col-md-4 d-flex align-items-center justify-content-between justify-content-md-start mb-3 mb-md-0">
              <div class="d-inline-block">
                  <h5 class="h4 d-inline-block font-weight-400 mb-0"><b>Update Notification Deployment</b></h5>
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
             
              {{-- <div class="col-lg-6" style="padding-top: 10px;" id="form-category">
                  <label>Category</label>
                  <select  class="form-control select2" id="category" name="category" >
                      <option value="{{$notification->category}}">{{$notification->category}}</option>
                      
                  </select>
              </div> --}}
              <div class="col-lg-6" style="padding-top: 10px;" id="form-email">
                <div class="form-group">
                  <label>Email</label>
                    <input type="text" class="form-control select2" id="email"
                    name="email" value="{{old('email', $notification->email)}}" placeholder="Input email">
                  <span class="gu-hide" style="color: red;"
                  id="erroremail">{{ __('*Please input email') }}</span>
                      
                </div>
              </div>
              <div class="col-lg-12" style="padding-top: 10px;" id="form-subject-deployment">
                <div class="form-group">
                  <label>Subject</label>
                    <input type="text" class="form-control select2" id="subjectDeployment"
                    name="subjectDeployment" value="{{old('subjectDeployment', $notification->subject)}}" placeholder="Input subject">
                  <span class="gu-hide" style="color: red;"
                  id="errorSubjectDeployment">{{ __('*Please input subject') }}</span>
                      
                </div>
              </div>
              <div class="col-lg-12" style="padding-top: 10px;" id="form-message">
                <div class="form-group">
                  <label>Message</label>
                  <textarea id="message"  class="form-control"  name="message">{{$notification->message}}</textarea>
                  <span class="gu-hide" style="color: red;"
                  id="errorMessage">{{ __('*Please input message') }}</span>

                </div>
              </div>
              <div class="col-lg-6" style="padding-top: 10px;" id="form-activity-name">
                <div class="form-group">
                  <label>Activity Name</label>
                    <input type="text" class="form-control select2" id="activity_name" value="{{old('activity_name', $notification->activity_name)}}"
                    name="activity_name" placeholder="Input activity">
                  <span class="gu-hide" style="color: red;"
                  id="errorActivityName">{{ __('*Please input activity') }}</span>

                </div>
              </div>
              <div class="col-lg-6" style="padding-top: 10px;" id="form-objective">
                <div class="form-group">
                  <label>Objective</label>
                    <input type="text" class="form-control select2" id="objective"
                    name="objective" placeholder="Input objective" value="{{old('objective', $notification->objective)}}">
                  <span class="gu-hide" style="color: red;"
                  id="errorObjective">{{ __('*Please input objective') }}</span>

                </div>
              </div>
              <div class="col-lg-12" style="padding-top: 10px;" id="form-maintenance-detail">
                <div class="form-group">
                  <label>Maintenance Detail</label>
                  <textarea id="maintenance_detail"  class="form-control"  name="maintenance_detail">{{$notification->maintenance_detail}}</textarea>
                  <span class="gu-hide" style="color: red;"
                  id="errorMaintenance">{{ __('*Please input maintenance detail') }}</span>

                </div>
              </div>
              <div class="col-lg-6" style="padding-top: 10px;" id="form-maintenance-schedule">
                <div class="form-group">
                  <label>Maintenance Schedule</label>
                  <input class="form-control maintenance-schedule"  id="maintenance_schedule" value="{{$notification->maintenance_start . ' | ' . $notification->maintenance_end}}" name="maintenance_schedule" type="text" style="height: 40px;">
                  <span class="gu-hide" style="color: red;"
                  id="errorMaintenanceSchedule">{{ __('*Please input maintenance schedule') }}</span>

                </div>
              </div>
              <div class="col-lg-6" style="padding-top: 10px;" id="form-downtime">
                <div class="form-group">
                  <label>Downtime</label>
                  {{-- <input class="form-control downtime"  id="downtime" name="downtime" value="{{$notification->downtime_start . ' | ' . $notification->downtime_end}}" type="text" style="height: 40px;"> --}}
                  <select class="form-control select2" id="downtime" name="downtime">
                    <option value="{{$notification->downtime}}">{{$notification->downtime}}</option>
                    <option value="15 Minutes">15 Minutes</option>
                    <option value="30 Minutes">30 Minutes</option>
                    <option value="45 Minutes">45 Minutes</option>
                    <option value="60 Minutes">60 Minutes</option>
                  </select>
                  <span class="gu-hide" style="color: red;"
                  id="errorDowntime">{{ __('*Please input downtime') }}</span>

                </div>
              </div>
              <div class="col-lg-12" style="padding-top: 10px;" id="form-service-impact">
                <div class="form-group">
                  <label>Service Impact</label>
                  <textarea id="service_impact"  class="form-control"  name="service_impact">{{$notification->service_impact}}</textarea>
                  <span class="gu-hide" style="color: red;"
                  id="errorServiceImpact">{{ __('*Please input service impact ') }}</span>

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
        var subjectDeployment = $("#subjectDeployment").val();
        if(subjectDeployment == "") {
          $("#errorSubjectDeployment").removeClass("gu-hide");
          return false;
        }else {
          $("#errorSubjectDeployment").addClass("gu-hide");

        }

        var message = $("#message").val();
        if(message == "" ){
          $("#errorMessage").removeClass("gu-hide");
          return false;
        }else {
          $("#errorMessage").addClass("gu-hide");

        }

        var activity_name = $("#activity_name").val();
        if(activity_name == "" ){
          $("#errorActivityName").removeClass("gu-hide");
          return false;
        }else {
          $("#errorActivityName").addClass("gu-hide");

        }
        var objective = $("#objective").val();
        if(objective == "" ){
          $("#errorObjective").removeClass("gu-hide");
          return false;
        }else {
          $("#errorObjective").addClass("gu-hide");

        }
        var maintenanceDetail = $("#maintenance_detail").val();
        if(maintenanceDetail == "" ){
          $("#errorMaintenance").removeClass("gu-hide");
          return false;
        }else {
          $("#errorMaintenance").addClass("gu-hide");

        }
        var maintenanceSchedule = $("#maintenance_schedule").val();
        if(maintenanceSchedule == "" ){
          $("#errorMaintenanceSchedule").removeClass("gu-hide");
          return false;
        }else {
          $("#errorMaintenanceSchedule").addClass("gu-hide");

        }
        var downtime = $("#downtime").val();
        if(downtime == "" ){
          $("#errorDowntime").removeClass("gu-hide");
          return false;
        }else {
          $("#errorDowntime").addClass("gu-hide");

        }
        var serviceImpact = $("#service_impact").val();

        if(serviceImpact == "" ){
          $("#errorServiceImpact").removeClass("gu-hide");
          return false;
        }else {
          $("#errorServiceImpact").addClass("gu-hide");

        }
      }
  </script>
 <script>
      $( document ).ready(function() {
          if ($(".maintenance-schedule").length) 
    {
      $('.maintenance-schedule').daterangepicker({
        locale: date_picker_locale,
        autoUpdateInput: false,
        // singleDatePicker: true,
        timePicker: true,
          timePicker24Hour: true,
  
      });
      $('.maintenance-schedule').on('apply.daterangepicker', function(ev, picker) {
  
        var p_id =$(this).attr("data-progress-id");
        console.log(p_id);
        $('#maintenance_schedule').val(picker.startDate.format('YYYY-MM-DD HH:mm:ss')+ " | " +picker.endDate.format('YYYY-MM-DD HH:mm:ss') );
      });
      }
      if ($(".downtime").length) 
    {
      $('.downtime').daterangepicker({
        locale: date_picker_locale,
        autoUpdateInput: false,
        // singleDatePicker: true,
        timePicker: true,
          timePicker24Hour: true,
  
      });
      $('.downtime').on('apply.daterangepicker', function(ev, picker) {
  
        var p_id =$(this).attr("data-progress-id");
        console.log(p_id);
        $('#downtime').val(picker.startDate.format('YYYY-MM-DD HH:mm:ss')+ " | " +picker.endDate.format('YYYY-MM-DD HH:mm:ss') );
      });
      }
      });
  </script>
@endsection
