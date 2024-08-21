@extends('layouts.admin')

@section('title')
    {{__('Manage Role Operators')}}
@endsection

@section('content')
{{ Form::open(array('route' => 'roles.operator.store'))}}
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

     <div class="form-check">

    
<div class="col-4 custom-control ">
                           
  <input class="form-check-input" type="checkbox" value="" id="checkAll">
  <label class="form-check-label padtop14" for="select_all">
   Select All
   <input type="hidden" class="form-control" id="role_id" name="role_id" value="{{$role->id}}">
  </label>
</div>


</div>
</div>

                                    </th>
                        <th class="text-dark"></th>
                    </tr>

                    <tr>
                            <td>Assign Operator</td>
                            <td>
                                <div class="row">
                                      @if(isset($operators) && !empty($operators))
                                          @foreach ($operators as $operator )
                                
                                                @if(in_array($operator->id_operator, $activeRoleOperators))
   


                                                       <div class="col-2 custom-control custom-checkbox ">

                                                        <input class="form-check-input" type="checkbox" value="{{$operator->id_operator}}" id="operators_{{$operator->id_operator}}" name="operators[]" checked>
                                                      <label class="form-check-label padtop14" for="operators_{{$operator->id_operator}}">
                                                      {{$operator->operator_name}}
                                                      </label>
                                                    
                                                   
                                                      </div>

                                                 @endif

                             
                                            @endforeach
                                        @endif
                           
                                   
                              </div>
                            </td>
                        </tr>

                          <tr>
                            <td>New Operator</td>
                            
                            <td>
                                <div class="row">
                                    
                                   @if(isset($operators) && !empty($operators))
                                       @foreach ($operators as $operator)

                                             @if(!in_array($operator->id_operator, $activeRoleOperators))
                           
                                                    <div class="col-2 custom-control custom-checkbox ">

                                                        <input class="form-check-input" type="checkbox" value="{{$operator->id_operator}}" id="operators_{{$operator->id_operator}}" name="operators[]">
                                                        <label class="form-check-label padtop14" for="operators_{{$operator->id_operator}}">
                                                        {{$operator->operator_name}}
                                                        </label>
                                  
                                   
                                                    </div>
                                            @endif
                        
                                        @endforeach
                            @endif
                            
                    </div>
                </td>
            </tr>

                </table>

        </div>
    </div>
    </div>
    </div>
{{ Form::close() }}
<script type="text/javascript">
    $("#checkAll").change(function(){
        
$('input:checkbox').not(this).prop('checked', this.checked);      
});
  
</script>
@endsection