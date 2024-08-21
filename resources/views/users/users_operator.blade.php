@extends('layouts.admin')

@section('title')
{{__('Manage User Operators')}}
@endsection

@section('content')


        @php
        if($errors->has('operators'))
        Session::flash('error', $errors->first('operators'));
        @endphp
<div class="user-operator">
{{ Form::open(array('route' => 'management.user.operator.store'))}}
<div class="card bg-none card-box">

  <div class="row">
    <div class="col-12">
      <div class="form-group col-12 text-right">
        <input type="submit" value="{{__('Save')}}" class="btn-create badge-blue">
      </div>
      <div class="form-group">
        <table class="table table-striped">
          <tr>
            <th class="text-dark">
              <div class="row">
                <div class="form-check" style="padding-left: 0;">
                  <div class="col-md-12 custom-control ">
                    <input class="form-check-input" type="checkbox" value="" id="checkAll">
                    <label class="form-check-label padtop14" for="select_all">
                      Select All
                      <input type="hidden" class="form-control" id="user_id" name="user_id" value="{{$user->id}}">
                    </label>
                  </div>
                </div>
              </div>
            </th>
            <th class="text-dark"></th>
          </tr>
          <tr>
          <td>Assign Operator</td>
          </tr>
          <tr>
            <td colspan="2">
                @if(isset($operators) && !empty($operators))
                @foreach ($operators as $operator)
                  @if(in_array($operator->id_operator, $activeUserOperatorsArray))
                    <div class="row">
                        <div class="col-md-10 custom-control custom-checkbox  " onclick="assingservicescheck({{$operator->id_operator}})">
                            <input class="form-check-input mg-top" type="checkbox" value="{{$operator->id_operator}}" id="operators_{{$operator->id_operator}}" name="operators[]" checked>
                            <label class="form-check-label padtop14 opstyle" for="operators_{{$operator->id_operator}}">
                                {{$operator->operator_name}}
                            </label>

                        </div>

                        <div class="row" style="margin-bottom: 20px;">
                            @if(isset($operator->services) && !empty($operator->services))
                            @foreach ($operator->services as $service)
                            <div class=" col-md-4" style="overflow: hidden;">
                                <div class="col-md-12 custom-control custom-checkbox ">
                                    @if(in_array($service->id_service, $activeUserServicesByOperaterArray))
                                        <input class="form-check-input" type="checkbox" value="{{$service->id_service}}" id="services_{{$operator->id_operator}}_{{$service->id_service}}" name="services_{{$operator->id_operator}}[]" checked>
                                        <label class="form-check-label padtop14" for="services_{{$operator->id_operator}}_{{$service->id_service}}">
                                            {{$service->service_name}}
                                        </label>
                                    @else
                                        <input class="form-check-input" type="checkbox" value="{{$service->id_service}}" id="services_{{$operator->id_operator}}_{{$service->id_service}}" name="services_{{$operator->id_operator}}[]">
                                        <label class="form-check-label padtop14" for="services_{{$operator->id_operator}}_{{$service->id_service}}">
                                            {{$service->service_name}}
                                        </label>
                                    @endif
                                </div>
                            </div>

                            @endforeach
                            @endif
                        </div>
                    </div>
                  @endif
                @endforeach
                @endif

            </td>
          </tr>
          <tr>
            <td>New Operator</td>
          </tr>
          <tr>
            <td colspan="2">
                @if(isset($operators) && !empty($operators))
                @foreach ($operators as $operator)
                  @if(!in_array($operator->id_operator, $activeUserOperatorsArray))
                    <div class="row">
                        <div class="col-md-10  col-xs-12 custom-control custom-checkbox ">
                            <input class="form-check-input mg-top" type="checkbox" value="{{$operator->id_operator}}" id="operators_{{$operator->id_operator}}" name="operators[]">
                            <label class="form-check-label padtop14 opstyle" for="operators_{{$operator->id_operator}}">
                                {{$operator->operator_name}}
                            </label>

                        </div>
                        <div class="col-md-2 col-xs-12 user-serv custom-control" onclick="servicescheck({{$operator->id_operator}})">
                            <input class="form-check-input" type="checkbox" value=""  id="services_{{$operator->id_operator}}_checkAll">
                            <label class="form-check-label padtop14" for="select_all">
                              Select All Services

                            </label>
                        </div>
                        </div>
                        <div class="row" style="margin-bottom: 20px;">

                            @if(isset($operator->services) && !empty($operator->services))
                            @foreach ($operator->services as $service)
                            <div class=" col-md-4" style="overflow: hidden;">
                                <div class="custom-control custom-checkbox ">
                                    <input class="form-check-input" type="checkbox" value="{{$service->id_service}}" id="services_{{$operator->id_operator}}_{{$service->id_service}}" name="services_{{$operator->id_operator}}[]">
                                    <label class="form-check-label padtop14" for="services_{{$operator->id_operator}}_{{$service->id_service}}">
                                        {{$service->service_name}}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                            @endif

                        </div>
                    </div>
                  @endif
                @endforeach
                @endif


            </td>
          </tr>

        </table>

      </div>
    </div>
  </div>
</div>
{{ Form::close() }}
</div>

<script type="text/javascript">
  $("#checkAll").change(function() {
    $('input:checkbox').not(this).prop('checked', this.checked);
    // $('input[name="operators[]"').not(this).prop('checked', this.checked);
  });

  function servicescheck(id){
    $("#services_"+id+"_checkAll").change(function() {
    $('input[name="services_'+id+'[]"').not(this).prop('checked', this.checked);
    });
  }
  function assingservicescheck(id){
    $("#operators_"+id).change(function() {
    $('input[name="services_'+id+'[]"').not(this).prop('checked', this.checked);
    });
  }
</script>
@endsection
