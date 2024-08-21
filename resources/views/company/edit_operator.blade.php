@extends('layouts.admin')

@section('title')
    {{ __('Edit Company Operator') }}
@endsection


@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                   <h4>{{$company->name}}</h4>
                   <div class="container">
                    <form class="form-control-label" action="{{url('/management/operator/store')}}" method="POST"  data-parsley-validate novalidate>
                        @csrf 
                        {{-- <span> please select operator below</span> --}}
                        
                        <div class="row">

                                <input type="hidden" class="form-control" id="company_id" name="company_id" value="{{$company->id}}">

                                @foreach ($companyOperators as $companyOperator )
                                 @if(isset($companyOperator->company_id) && ($companyOperator->company_id==$company->id))
                                 <div class="float left">
                                    <div class="col-14 custom-control operator-control custom-checkbox">
                                    <input class="custom-control-input" id="operator_{{$companyOperator->operator_id}}" name="operator[]" type="checkbox" value="{{$companyOperator->operator_id}}" checked>
                                    <label for="operator_{{$companyOperator->operator_id}}" class="custom-control-label font-weight-500">{{$companyOperator->operator_name}}</label>  
                                    </div>
                                 </div>              
                                 @endif
                                @endforeach
                                @if(isset($operators) && !empty($operators))
                                 @foreach ($operators as $operator)
                                 <div class="float left">
                                    <div class="col-14 custom-control operator-control custom-checkbox">
                                    <input class="custom-control-input" id="operator_{{$operator->id_operator}}" name="operator[]" type="checkbox" value="{{$operator->id_operator}}">
                                     <label for="operator_{{$operator->id_operator}}" class="custom-control-label font-weight-500">{{$operator->operator_name}}</label>
                             
                                    </div>
                                 </div>
                    
                                @endforeach
                            @endif

                        </div>
                        <div class="form-group col-12 text-right">
                            <input type="submit" value="{{__('Edit Operator')}}" class="btn-create badge-blue">
                            {{-- <input type="button" value="{{__('Cancel')}}" class="btn-create bg-gray" data-dismiss="modal"> --}}
                        </div>
                        {{-- {{ Form::close() }} --}}
                    </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
