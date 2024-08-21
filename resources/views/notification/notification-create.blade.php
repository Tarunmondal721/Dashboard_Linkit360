

@extends('layouts.admin')

@section('title')
    {{ __('Notification Management') }}
@endsection

@section('content')

  <div class="page-content page-content-center">
    <form id="form-notification"  method="post"  onsubmit="return notificationSubmit()">
    @csrf
    <div class="page-title" style="margin-bottom:25px">
      <div class="row justify-content-between align-items-center mb-5 ">
          <div
              class="col-xl-4 col-lg-4 col-md-4 d-flex align-items-center justify-content-between justify-content-md-start mb-3 mb-md-0">
              <div class="d-inline-block">
                  <h5 class="h4 d-inline-block font-weight-400 mb-0"><b>Add New Notification</b></h5>
              </div>
          </div>
          <div
              class="col-xl-8 col-lg-8 col-md-8 d-flex align-items-center justify-content-between justify-content-md-end">
          </div>
      </div>
    <div class="card shadow-sm mt-0">
        <div class="card-body">
          
            <div class="row">
                <div class="col-lg-6" style="padding-top: 10px;">
                    <div class="form-group">
                        <label>Country*</label>
                        <select class="form-control select2" id="country_name" name="country" onchange="countryChange()">
                            <option value="">Select Country</option>
                            @foreach ($countrys as $country)
                                <option value="{{$country->country_id}}">{{$country->country_name}}</option>
                            @endforeach
                        </select>
                        <span class="gu-hide" style="color: red;"
                            id="errorCountry">{{ __('*Please select country') }}</span>

                    </div>
                </div>
                <div class="col-lg-6" style="padding-top: 10px;" id="form-email">
                    <div class="form-group">
                    <label>Email</label>
                        <input type="text" class="form-control select2" id="email"
                        name="email" value="{{old('email')}}" placeholder="Input email">
                    <span class="gu-hide" style="color: red;"
                    id="erroremail">{{ __('*Please input email') }}</span>
                        
                    </div>
                </div>


                <div class="col-lg-6" style="padding-top: 10px;" id="form-category">
                    <div class="form-group">
                        <label>Category*</label>
                        <select class="form-control select2" id="category" name="category" onchange="changeCategory()">
                            <option value="">Select Category</option>
                            <option value="deployment">Deployment</option>
                            <option value="bug">Bug</option>
                            <option value="incident">Incident</option>
                        </select>

                    </div>
                </div>
             
          </div>
          <div class="row">

                <div class="col-md-12" style="text-align:right;">
                    {{-- <label class="invisible d-block">Button</label> --}}
                    <button type="submit" id="button-notification" disabled class="btn badge-blue">Create</button>
                </div>

          </div>
        </div>
    </div>
</form>

</div>

  <script>
    var baseUrl = window.location.origin + "/";
    function changeCategory(){
        var test = $('#category').val();
        if(test=='incident'){
            $('#button-notification').removeAttr('disabled');
            var form = $('#form-notification');
            form.attr('action', "{{route('add.notification.incident')}}");
            $("#form-subject-deployment").remove();
            $("#form-message").remove();
            $("#form-activity-name").remove();
            $("#form-objective").remove();
            $("#form-maintenance-detail").remove();
            $("#form-maintenance-schedule").remove();
            $("#form-downtime").remove();
            $("#form-service-impact").remove();
            $('#form-activity').remove();
            $("#form-deployment-details").remove();
            var formSubject = `<div class="col-lg-6" style="padding-top: 10px;" id="form-subject">
                <div class="form-group">
                  <label>Subject</label>
                    <input type="text" class="form-control select2" id="subject"
                    name="subject" placeholder="Input subject">
                  <span class="gu-hide" style="color: red;"
                  id="errorSubject">{{ __('*Please input subject') }}</span>
                      
                </div>
              </div>`;
              $(formSubject).insertAfter('#form-category');
            
            var formNumberTicket = `<div class="col-lg-6" style="padding-top: 10px;" id="form-number-ticket">
                <div class="form-group">
                  <label>Number Ticket</label>
                    <input type="text" class="form-control select2" id="number_ticket"
                    name="number_ticket" placeholder="Input number ticket">
                  <span class="gu-hide" style="color: red;"
                  id="errorNumberTicket">{{ __('*Please input number ticket') }}</span>
                      
                </div>
              </div>`;
              $(formNumberTicket).insertAfter('#form-subject');
            var formClassification = `<div class="col-lg-6" style="padding-top: 10px;" id="form-classification">
                <div class="form-group">
                  <label>Classification</label>
                    <input type="text" class="form-control select2" id="classification"
                    name="classification" placeholder="Input number ticket">
                  <span class="gu-hide" style="color: red;"
                  id="errorClassification">{{ __('*Please input classification') }}</span>
                      
                </div>
              </div>`;
              $(formClassification).insertAfter('#form-number-ticket');
            var formSeverty = `<div class="col-lg-6" style="padding-top: 10px;" id="form-severty">
                <div class="form-group">
                  <label>Severty</label>
                    <input type="text" class="form-control select2" id="severty"
                    name="severty" placeholder="Input number ticket">
                  <span class="gu-hide" style="color: red;"
                  id="errorSeverty">{{ __('*Please input severty') }}</span>
                      
                </div>
              </div>`;
              $(formSeverty).insertAfter('#form-classification');

              var incidentTime = `<div class="col-lg-6" style="padding-top: 10px;" id="form-time-incident">
                <div class="form-group">
                  <label>Incident Time</label>
                  <input class="form-control time-incident"  id="time_incident" name="time_incident" type="text" style="height: 40px;">
                  <span class="gu-hide" style="color: red;"
                  id="errorTimeIncident">{{ __('*Please input incident time') }}</span>

                </div>
              </div>`;
            $(incidentTime).insertAfter('#form-severty');
            var formDetailIncident = ` <div class="col-lg-12" style="padding-top: 10px;" id="form-details-incident">
                <div class="form-group">
                  <label>Incident Details</label>
                  <input id="details_incident" value="" type="hidden" name="details">
                  <trix-editor input="details_incident"></trix-editor>
                  <span class="gu-hide" style="color: red;"
                  id="errorDetailIncident">{{ __('*Please input details ') }}</span>

                </div>
              </div>`;
            $(formDetailIncident).insertAfter('#form-time-incident');
           
            $('.time-incident').daterangepicker({
              locale: date_picker_locale,
              autoUpdateInput: false,
              singleDatePicker: true,
              timePicker: true,
                timePicker24Hour: true,

            });
            $('.time-incident').on('apply.daterangepicker', function(ev, picker) {


              $('#time_incident').val(picker.startDate.format('YYYY-MM-DD HH:mm:ss'));
            });
          }else if(test =='deployment'){
            $("#form-subject").remove();
            $("#form-number-ticket").remove();
            $("#form-classification").remove();
            $("#form-severty").remove();

            $("#form-details-incident").remove();
            $("#form-time-incident").remove();
            $('#button-notification').removeAttr('disabled');
            var form = $('#form-notification');
            form.attr('action', "{{route('add.notification.deployment')}}");
            var formSubjectDeployment = `<div class="col-lg-6" style="padding-top: 10px;" id="form-subject-deployment">
                <div class="form-group">
                  <label>Subject</label>
                    <input type="text" class="form-control select2" id="subjectDeployment"
                    name="subjectDeployment" placeholder="Input subject">
                  <span class="gu-hide" style="color: red;"
                  id="errorSubjectDeployment">{{ __('*Please input subject') }}</span>
                      
                </div>
              </div>`;
              $(formSubjectDeployment).insertAfter('#form-category');
            
            var formMessage = ` <div class="col-lg-12" style="padding-top: 10px;" id="form-message">
                <div class="form-group">
                  <label>Message</label>
                  <textarea id="message"  class="form-control"  name="message"></textarea>
                  <span class="gu-hide" style="color: red;"
                  id="errorMessage">{{ __('*Please input message') }}</span>

                </div>
              </div>`;
              $(formMessage).insertAfter('#form-subject-deployment');

            var activityName = ` <div class="col-lg-6" style="padding-top: 10px;" id="form-activity-name">
                <div class="form-group">
                  <label>Activity Name</label>
                    <input type="text" class="form-control select2" id="activity_name"
                    name="activity_name" placeholder="Input activity">
                  <span class="gu-hide" style="color: red;"
                  id="errorActivityName">{{ __('*Please input activity') }}</span>

                </div>
              </div>`;

              $(activityName).insertAfter('#form-message')
            
            var objective = `   
              <div class="col-lg-6" style="padding-top: 10px;" id="form-objective">
                <div class="form-group">
                  <label>Objective</label>
                    <input type="text" class="form-control select2" id="objective"
                    name="objective" placeholder="Input objective">
                  <span class="gu-hide" style="color: red;"
                  id="errorObjective">{{ __('*Please input objective') }}</span>

                </div>
              </div>`;
              $(objective).insertAfter('#form-activity-name')
            
            var maintenanceDetail = `   
              <div class="col-lg-12" style="padding-top: 10px;" id="form-maintenance-detail">
                <div class="form-group">
                  <label>Maintenance Detail</label>
                  <textarea id="maintenance_detail"  class="form-control"  name="maintenance_detail"></textarea>
                  <span class="gu-hide" style="color: red;"
                  id="errorMaintenance">{{ __('*Please input maintenance detail') }}</span>

                </div>
              </div>`;
              $(maintenanceDetail).insertAfter('#form-objective')
            
              
            var maintenanceSchedule = `<div class="col-lg-6" style="padding-top: 10px;" id="form-maintenance-schedule">
                <div class="form-group">
                  <label>Maintenance Schedule</label>
                  <input class="form-control maintenance-schedule"  id="maintenance_schedule" name="maintenance_schedule" type="text" style="height: 40px;">
                  <span class="gu-hide" style="color: red;"
                  id="errorMaintenanceSchedule">{{ __('*Please input maintenance schedule') }}</span>

                </div>
              </div>`;
            $(maintenanceSchedule).insertAfter('#form-maintenance-detail');
            var downtime = `<div class="col-lg-6" style="padding-top: 10px;" id="form-downtime">
                <div class="form-group">
                  <label>Downtime</label>
                  <span class="gu-hide" style="color: red;"
                  id="errorDowntime">{{ __('*Please input downtime') }}</span>
                  <select class="form-control select2" id="downtime" name="downtime">
                      <option value="">Select Downtime</option>
                      <option value="15 Minutes">15 Minutes</option>
                      <option value="30 Minutes">30 Minutes</option>
                      <option value="45 Minutes">45 Minutes</option>
                      <option value="60 Minutes">60 Minutes</option>
                  </select>
                  </div>
                  </div>`;
            $(downtime).insertAfter('#form-maintenance-schedule');

              var serviceImpact = `   
              <div class="col-lg-12" style="padding-top: 10px;" id="form-service-impact">
                <div class="form-group">
                  <label>Service Impact</label>
                  <textarea id="service_impact"  class="form-control"  name="service_impact"></textarea>
                  <span class="gu-hide" style="color: red;"
                  id="errorServiceImpact">{{ __('*Please input service impact ') }}</span>

                </div>
              </div>`;
              $(serviceImpact).insertAfter('#form-downtime')
            
            // var formWebPortal = 
            // var formDeploymentDetails
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
          }else {
            $('#button-notification').attr('disabled', '');
            $("#form-number-ticket").remove();
            $("#form-classification").remove();
            $("#form-severty").remove();
            $("#form-subject").remove();
            $("#form-details-incident").remove();
            $("#form-time-incident").remove();
            $("#form-subject-deployment").remove();
            $("#form-message").remove();
            $("#form-activity-name").remove();
            $("#form-objective").remove();
            $("#form-maintenance-detail").remove();
            $("#form-maintenance-schedule").remove();
            $("#form-downtime").remove();
            $("#form-service-impact").remove();

            $('#form-activity').remove();
            $("#form-deployment-details").remove();
        }
    }

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
        var operator = $("#operator_name").val();
        if(operator == "") {
          $("#errorOperator").removeClass("gu-hide");
          return false;
        }else {
          $("#errorOperator").addClass("gu-hide");

        }
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

 
@endsection
