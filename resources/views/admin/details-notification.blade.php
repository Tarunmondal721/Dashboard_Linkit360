<div class="card bg-secondary    card-box">
    {{-- <a href="" class="btn badge-blue"><i class="fas fa-plus"></i> Add New Notification</a> --}}

    <div class="row mt-3 ">
        <div class="col-md-12 mb-3">
            <ul class="nav nav-tabs mb-4" role="tablist">
                <li>
                    <a class="active" id="notification-tab1" data-toggle="tab" href="#incident-tab" role="tab" aria-controls="" aria-selected="false">{{__('Incidents' . " ( " . count($notificationsIncident) ." ) " )}}</a>
                </li>
                <li>
                    <a id="notification-tab2" data-toggle="tab" href="#bugs-tab" role="tab" aria-controls="" aria-selected="false">{{__('Bugs')}}</a>
                </li>
                <li>
                    <a id="notification-tab3" data-toggle="tab" href="#deployment-tab" role="tab" aria-controls="" aria-selected="false">{{__('Deployment' . " ( " . count($notificationsDeployment) ." ) " )}}</a>
                </li>

            </ul>
            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="incident-tab" role="tabpanel" aria-labelledby="pills-home-tab">
                    <div class="row ">
                        <div class="col-1">
                            <div>
                                <i class="fa fa-crosshairs" style="color: #ec0909;"></i>            
                            </div>
                            <div style="border-left: 2px black solid; height: 25px;width: 0px;display: inline-block;margin-left: 7px">
                            </div>
                        </div>
                        
                        <div class="col-11">
                            <div class="alert alert-danger font-weight-normal" style="height: 50px;">
                                {{count($notificationsIncident) == 0 ? 'No Incident Alert Detected' : 'Incident Detected' }}  
                            </div>
                        </div>
                    </div>
                    <div class="accordion" id="accordion">
                    @foreach ($notificationsIncident as $incident)
                        <div class="row">
                            <div class="col-1 mb-5">
                                <div>
                                    <i class="fa fa-crosshairs" style="color: #ec0909;"></i>            
                                </div>
                                <div class="mb-2 border-notification">
                                </div>
                            </div>
                        
                            <div class="col-11 mt-2 bordered">
                                <div class="card  bg-white "  >
                                    <div class="card-header" id="headingOne">
                                      <h3 class="mb-0">
                                        <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#{{"collapseOne".$incident->id}}" aria-expanded="true" aria-controls="{{"collapseOne".$incident->id}}">
                                          {{$incident->subject}}
                                        </button>
                                      </h3>
                                    </div>
                                
                                    <div id="{{"collapseOne".$incident->id}}" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                                      <div class="card-body " style="margin-top: -25px">
                                            <table class="table ">
                                                <div class="row">
                                                    <tr>
                                                        <div class="col-1">
                                                            <td><i class="fa fa-hashtag"></i> Number Ticket</td>
                                                        </div>
                                                        <div class="col-11">
                                                            <td>: {{$incident->number_ticket}}</td>

                                                        </div>

                                                    </tr>
                                                </div>
                                                <div class="row">
                                                    <tr>
                                                        <div class="col-1">
                                                            <td><i class="fa fa-plus"></i> Created By</td>
                                                        </div>
                                                        <div class="col-11">
                                                            <td class="text-wrap">: {{$incident->created_by}}</td>

                                                        </div>
                                                    </tr>
                                                </div>
                                                <div class="row">
                                                    <tr>
                                                        <div class="col-1">
                                                            <td><i class="fa fa-landmark"></i> Classification </td>
                                                        </div>
                                                        <div class="col-11">
                                                            <td class="text-wrap">: {{$incident->classification}}</td>

                                                        </div>
                                                    </tr>
                                                </div>
                                                <div class="row">
                                                    <tr>
                                                        <div class="col-1">
                                                            <td><i class="fa fa-bolt"></i> Severty </td>
                                                        </div>
                                                        <div class="col-11">
                                                            <td class="text-wrap">: {{$incident->severty}}</td>

                                                        </div>
                                                    </tr>
                                                </div>
                                                <div class="row">
                                                    <tr>
                                                        <div class="col-1">
                                                            <td><i class="fa fa-clock"></i>  Time Incident </td>
                                                        </div>
                                                        <div class="col-11">
                                                            <td class="text-wrap">: {{$incident->time_incident}}</td>

                                                        </div>
                                                    </tr>
                                                </div>
                                                <div class="row">
                                                    <tr>
                                                        <div class="col-1">
                                                            <td><i class="fa fa-exclamation"></i>  Status </td>
                                                        </div>
                                                        <div class="col-11">
                                                            <td class="text-wrap">: {{$incident->status}}</td>

                                                        </div>
                                                    </tr>
                                                </div>
                                               
                                                <div class="row">
                                                    <tr>
                                                        <div class="col-1">
                                                            {{-- <td><i class="fa fa-clock"></i>  Time Incident </td> --}}
                                                        </div>
                                                        <div class="col-11">
                                                            {{-- <td class="text-wrap">: {{$incident->time_incident}}</td> --}}

                                                        </div>
                                                    </tr>
                                                </div>
                                            </table>
                                            <div class="row">
                                                {{-- <tr>
                                                    <div class="col-1">
                                                        <td><i class="fa fa-envelope"></i>  Incident Detail </td>
                                                    </div>
                                                    <div class="col-11">
                                                        <td class="text-wrap">: {!!$incident->details!!}</td>

                                                    </div>
                                                </tr> --}}
                                                Details : 
                                                <p>{!!$incident->details!!}</p>
                                            </div>
                                      </div>
                                    </div>
                                  </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="tab-pane fade" id="bugs-tab" role="tabpanel" aria-labelledby="pills-profile-tab">
                    <div class="row ">
                        <div class="col-1">
                            <div>
                                <i class="fa fa-crosshairs" style="color: #ec0909;"></i>            
                            </div>
                            <div style="border-left: 2px black solid; height: 25px;width: 0px;display: inline-block;margin-left: 7px">
                            </div>
                        </div>
                        
                        <div class="col-11">
                            <div class="alert alert-danger font-weight-normal" style="height: 50px;">
                                No Bugs Detected
                            </div>
                        </div>
                    </div>
                    
                </div>
                <div class="tab-pane fade" id="deployment-tab" role="tabpanel" aria-labelledby="pills-profile-tab">
                    <div class="row ">
                        <div class="col-1">
                            <div>
                                <i class="fa fa-crosshairs" style="color: #ec0909;"></i>            
                            </div>
                            <div style="border-left: 2px black solid; height: 25px;width: 0px;display: inline-block;margin-left: 7px">
                            </div>
                        </div>
                        
                        <div class="col-11">
                            <div class="alert alert-danger font-weight-normal" style="height: 50px;">
                                {{count($notificationsDeployment) == 0 ? 'No Deployment Alert Detected' : 'Deployment Detected' }}  
                            </div>
                        </div>
                    </div>
                    <div class="accordion" id="accordion">
                    @foreach ($notificationsDeployment as $deployment)
                        <div class="row">
                            <div class="col-1 mb-5">
                                <div>
                                    <i class="fa fa-crosshairs" style="color: #ec0909;"></i>            
                                </div>
                                <div class="mb-2 border-notification">
                                </div>
                            </div>
                        
                            <div class="col-11 mt-2 bordered">
                                <div class="card  bg-white "  >
                                    <div class="card-header" id="headingOne">
                                      <h3 class="mb-0">
                                        <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#{{"collapseOne".$deployment->id}}" aria-expanded="true" aria-controls="{{"collapseOne".$deployment->id}}">
                                          {{$deployment->subject}}
                                        </button>
                                      </h3>
                                    </div>
                                
                                    <div id="{{"collapseOne".$deployment->id}}" class="collapse"  data-parent="#accordion">
                                      <div class="card-body " style="margin-top: -25px">
                                        <p><small class="">{{$deployment->message}}</small></p>
                                        <table class="table ">
                                            <div class="row">
                                                <tr>
                                                    <div class="col-1">
                                                        <td><i class="fa fa-key"></i> Acitvity Name</td>
                                                    </div>
                                                    <div class="col-11">
                                                        <td>: {{$deployment->activity_name}}</td>

                                                    </div>

                                                </tr>
                                            </div>
                                            <div class="row">
                                                <tr>
                                                    <div class="col-1">
                                                        <td><i class="fa fa-object-group"></i> Objective</td>
                                                    </div>
                                                    <div class="col-11">
                                                        <td class="text-wrap">: {{$deployment->objective}}</td>

                                                    </div>
                                                </tr>
                                            </div>
                                            <div class="row">
                                                <tr>
                                                    <div class="col-1">
                                                        <td><i class="fa fa-wrench"></i> Maintenance Detail</td>
                                                    </div>
                                                    <div class="col-11">
                                                        <td class="text-wrap">: {{$deployment->maintenance_detail}}</td>
                                                    </div>
                                                </tr>
                                            </div>
                                            <div class="row">
                                                <tr>
                                                    <div class="col-1">
                                                        <td><i class="fa fa-clock"></i> Maintenance Schedule</td>
                                                    </div>
                                                    <div class="col-11">
                                                        <td class="text-wrap">: {{$deployment->maintenance_start . " - " . $deployment->maintenance_end}}</td>
                                                    </div>
                                                </tr>
                                            </div>
                                            <div class="row">
                                                <tr>
                                                    <div class="col-1">
                                                        <td><i class="fa fa-clock"></i> Downtime</td>
                                                    </div>
                                                    <div class="col-11">
                                                        <td class="text-wrap">: {{$deployment->downtime}}</td>
                                                    </div>
                                                </tr>
                                            </div>
                                            <div class="row">
                                                <tr>
                                                    <div class="col-1">
                                                        <td><i class="fa fa-toolbox"></i> Service Impact</td>
                                                    </div>
                                                    <div class="col-11">
                                                        <td class="text-wrap">: {{$deployment->service_impact}} </td>
                                                    </div>
                                                </tr>
                                            </div>
                                        </table>
                                          {{-- <hr>
                                          <p><small class=""><i class="fa fa-key"></i> Acitvity Name &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&ensp;: {{$deployment->activity_name}}</small></p>
                                          <p><small class=""><i class="fa fa-object-group"></i> Objective &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&ensp;&nbsp;: {{$deployment->objective}}</small></p>
                                          <p><small class=""><i class="fa fa-wrench"></i> Maintenance Detail &emsp;&emsp;&emsp;&ensp;: {{$deployment->maintenance_detail}} </small></p>
                                          <p><small class=""><i class="fa fa-clock"></i> Maintenance Schedule &emsp;&ensp;: {{$deployment->maintenance_start . " - " . $deployment->maintenance_end}} </small></p>
                                          <p><small class=""><i class="fa fa-clock"></i> Downtime &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&ensp;&nbsp;: {{$deployment->downtime_start . " - " . $deployment->downtime_end}} </small></p>
                                          <p><small class=" text-wrap"><i class="fa fa-toolbox"></i> Service Impact &emsp;&emsp;&emsp;&emsp;&emsp;&ensp;&nbsp;: {{$deployment->service_impact}}</small></p> --}}
                                      </div>
                                    </div>
                                  </div>
                            </div>
                        </div>
                        @endforeach
                        </div>
                    </div>
                    
                </div>

            </div>
        </div>
    </div>




    {{-- <div class="row mt-5">
        <div class="col-1">
            <div>
                <i class="fa fa-crosshairs" style="color: #ec0909;"></i>            
            </div>
            <div style="border-left: 2px black solid; height: 25px;width: 0px;display: inline-block;margin-left: 7px">
            </div>
        </div>
        
        <div class="col-11">
            <div class="alert alert-danger font-weight-normal" style="height: 50px;">
                Incident Detected
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-1">
            <div>
                <i class="fa fa-crosshairs" style="color: #ec0909;"></i>            
            </div>
            <div style="border-left: 2px black solid; height: 70px;width: 0px;display: inline-block;margin-left: 7px">
            </div>
        </div>
        
        <div class="col-11 mt-2">
            <div style="height: 100px;background-color: gainsboro;color: black">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-1">
            <div>
                <i class="fa fa-crosshairs" style="color: #ec0909;"></i>     
            </div>
            <div style="border-left: 2px black solid; height: 70px;width: 0px;display: inline-block;margin-left: 7px">
            </div>
        </div>
        
        <div class="col-11 mt-2 mb-3">
            <div style="height: 100px;background-color: gainsboro">

            </div>
        </div>
    </div> --}}
    <!-- Button trigger modal -->

  
  <!-- Modal -->
  <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          ...
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary">Save changes</button>
        </div>
      </div>
    </div>
  </div>
</div>