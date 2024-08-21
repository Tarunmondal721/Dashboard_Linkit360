

@extends('layouts.admin')

@section('title')
    {{ __('Notification Management') }}
@endsection

@section('content')

<div class="row">
  <div class="col-12">
      <ul class="nav nav-tabs mb-4" role="tablist">
          <li>
              <a class="active" id="contact-tab2" data-toggle="tab" href="#notification-incident" role="tab" aria-controls="" aria-selected="false">{{__('Notification Incident')}}</a>
          </li>
          <li>
              <a id="contact-tab4" data-toggle="tab" href="#notification-deployment" role="tab" aria-controls="" aria-selected="false">{{__('Notification Deployment')}}</a>
          </li>

          <li>
              <a id="profile-tab3" data-toggle="tab" href="#notification-bugs" role="tab" aria-controls="" aria-selected="false">{{__('Notification Bugs')}}</a>
          </li>
         
      </ul>
      <div class="tab-content" id="myTabContent2">
          <div class="tab-pane fade fade show active" id="notification-incident" role="tabpanel" aria-labelledby="profile-tab3">
            <div class="row">
              <div class="col-md-12 col-12 ">
                  <div class="card bg-none">
                      <div class="card-body">
                          
                          <div class="table-responsive">
                              <table class="table table-striped dataTable">
                                  <thead>
                                  <tr>
                                      <th>{{__('Id')}}</th>
                                      <th>{{__('Country')}}</th>
                                      <th>{{__('Status')}}</th>
                                      <th >{{__('Category')}}</th>
                                      <th >{{__('Subject')}}</th>
                                      <th >{{__('Number Ticket')}}</th>
                                      <th >{{__('Created By')}}</th>
                                      <th >{{__('Classification')}}</th>
                                      <th >{{__('Severty')}}</th>
                                      <th >{{__('Detail')}}</th>
                                      <th >{{__('Time Incident')}}</th>
                                      <th >{{__('Action')}}</th>
                                  </tr>
                                  </thead>
                                  <tbody>
                                  {{-- @if(isset($companies) && !empty($companies)) --}}
                                    @foreach ($notificationIncident as $incident)
                                    <tr>
                                      <td>{{$incident->id}}</td>
                                      <td>{{$incident->country_name}}</td>
                                      <td>{{$incident->status}}</td>
                                      <td>{{$incident->category}}</td>
                                      <td>{{$incident->subject}}</td>
                                      <td>{{$incident->number_ticket}}</td>
                                      <td>{{$incident->created_by}}</td>
                                      <td>{{$incident->classification}}</td>
                                      <td>{{$incident->severty}}</td>
                                      <td class="text-wrap colom-notification">{{$incident->details}}</td>
                                      <td>{{$incident->time_incident}}</td>
                                      <td >
                                    
                                        <a href="/notification/detail-incident/{{$incident->id}}">
                                          <i class="fa fa-eye"></i>
                                        </a> 
                                        |
                                        <a href="#" onclick="deleteIncidentNotification('{{$incident->id}}')">
                                          <i class="fas fa-trash text-danger"></i>
                                          
                                      </a>
                                      </td>
                                    </tr>
                                        
                                    @endforeach
                                  </tbody>
                              </table>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
          </div>
          <div class="tab-pane fade" id="notification-deployment" role="tabpanel" aria-labelledby="profile-tab3">
            <div class="row">
              <div class="col-md-12 col-12 ">
                  <div class="card bg-none">
                      <div class="card-body">
                          <div class="table-responsive">
                              <table class="table table-striped dataTable">
                                  <thead>
                                  <tr>
                                      <th>{{__('Id')}}</th>
                                      <th>{{__('Country')}}</th>
                                      {{-- <th>{{__('Operator')}}</th> --}}
                                      <th >{{__('Subject')}}</th>
                                      <th >{{__('Message')}}</th>
                                      <th >{{__('Activity Name')}}</th>
                                      <th >{{__('Objective')}}</th>
                                      <th style="width: 50px" >{{__('Maintenance Detail')}}</th>
                                      <th >{{__('Maintenance Schedule')}}</th>
                                      <th >{{__('Downtime')}}</th>
                                      <th >{{__('Service Impact')}}</th>
                                      <th >{{__('Action')}}</th>
                                  </tr>
                                  </thead>
                                  <tbody>
                                  {{-- @if(isset($companies) && !empty($companies)) --}}
                                  @foreach ($notificationDeployment as $deployment)
                                  <tr>
                                    <td>{{$deployment->id}}</td>
                                    <td>{{$deployment->country_name}}</td>
                                    {{-- <td>{{$deployment->operator_name}}</td> --}}
                                    <td>{{$deployment->subject}}</td>
                                    <td class="text-wrap colom-notification">{{$deployment->message}}</td>
                                    <td>{{$deployment->activity_name}}</td>
                                    <td>{{$deployment->objective}}</td>
                                    <td class="text-wrap colom-notification" >{{$deployment->maintenance_detail}}</td>
                                    <td>{{$deployment->maintenance_start ." - " .$deployment->maintenance_end}}</td>
                                    <td>{{$deployment->downtime}}</td>
                                    <td class="text-wrap colom-notification">{{$deployment->service_impact}}</td>
                                    <td >
                                    
                                      <a href="/notification/detail-deployment/{{$deployment->id}}" >
                                        <i class="fa fa-eye"></i>
                                      </a> 
                                      |
                                      <a href="#" onclick="deleteDeploymentNotification('{{$deployment->id}}')">
                                        <i class="fas fa-trash text-danger"></i>
                                        
                                    </a>
                                    </td>
                                  </tr>
                                  @endforeach
                                  </tbody>
                              </table>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
          </div>

          {{-- tab ip config settings client --}}
          <div class="tab-pane fade" id="notification-bugs" role="tabpanel" aria-labelledby="contact-tab4">
              
              <div class="row">
                  <div class="col-md-12 col-12 ">
                      <div class="card bg-none">
                          <div class="card-body">
                              
                              <div class="table-responsive">
                                  <table class="table table-striped dataTable">
                                      <thead>
                                      <tr>
                                          <th>{{__('Id')}}</th>
                                          <th>{{__('IP')}}</th>
                                          <th>{{__('Name')}}</th>
                                          <th >{{__('Status')}}</th>
                                          <th >{{__('Action')}}</th>
                                      </tr>
                                      </thead>
                                      <tbody>
                                      {{-- @if(isset($companies) && !empty($companies)) --}}

                                      <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                      </tr>
                                      </tbody>
                                  </table>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>
</div>
<div id="delete-notification-incident" class="modal fade">
    <div class="modal-dialog">
        <form id="delete-incident-form"  method="POST" action="{{ route('delete.notification.incident') }}">
            @method("delete")
            @csrf
            <input type="hidden" name="id" value="">
          
            <div class="modal-content ">
                <div class="modal-header">
                  <h5 class="modal-title text-center">Delete Notification Incident</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <p class="pl-5"></p>
                </div>
                <div class="modal-footer pl-5">
                  <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                  <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
                </div>
              </div>
        </form>
    </div>
</div>
<div id="delete-notification-deployment" class="modal fade">
    <div class="modal-dialog">
        <form id="delete-deployment-form"  method="POST" action="{{ route('delete.notification.deployment') }}">
            @method("delete")
            @csrf
            <input type="hidden" name="id" value="">
          
            <div class="modal-content ">
                <div class="modal-header">
                  <h5 class="modal-title text-center">Delete Notification Deployment</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <p class="pl-5"></p>
                </div>
                <div class="modal-footer pl-5">
                  <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                  <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
                </div>
              </div>
        </form>
    </div>
</div>
<script>
    function deleteIncidentNotification(id){
        var deleteIncident = $('div#delete-notification-incident'),
            formDeleteIncident  = $('form#delete-incident-form'),
            submitBtn      = formDeleteIncident.find('button[type=submit]');
            // var date    = $(this).data('ip-id'),
            message = "@lang('Are you sure you want to delete this notification ?')";
    
            formDeleteIncident.find('input[name=id]').val(id);
            deleteIncident.find('.modal-body p').html(message);

            deleteIncident.modal('show');
    }
    function deleteDeploymentNotification(id){
        var deleteDeployment = $('div#delete-notification-deployment'),
            formdDeleteDeployment  = $('form#delete-deployment-form'),
            submitBtn      = formdDeleteDeployment.find('button[type=submit]');
            // var date    = $(this).data('ip-id'),
            message = "@lang('Are you sure you want to delete this notification ?')";
    
            formdDeleteDeployment.find('input[name=id]').val(id);
            deleteDeployment.find('.modal-body p').html(message);

            deleteDeployment.modal('show');
    }

</script>
@endsection
