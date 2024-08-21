<div class="card bg-none card-box">
    {{-- {{ Form::model($company, array('route' => array('CompanyOperators.create'), 'method' => 'POST')) }} --}}
    <form class="needs-validation" action="{{url('/management/operator/store')}}" method="POST"  data-parsley-validate novalidate>
        @csrf
    <div class="row">
        <div class="col-12 form-group">
            <label class="form-control-label" for="name">{{ __('Name') }}</label>
            {{-- <input type="text" class="form-control" id="company" name="company" value="{{$id}}" disabled/> --}}
           
            <input type="text" class="form-control" id="company_name" name="company_name" value="{{$companie->name}}" disabled>
            
        </div>
       
        <div>
            <input type="hidden" class="form-control" id="company_id" name="company_id" value="{{$companie->id}}">
            @if(isset($operators) && !empty($operators))
            @foreach ($operators as $operator)
            <input type="checkbox" id="operator" name="operator[]" value="{{$operator->id_operator}}">
            <label for="operator">{{$operator->operator_name}}</label><br>
            {{-- <input type="checkbox" id="vehicle2" name="vehicle" value="Car">
            <label for="vehicle2"> I have a car</label><br>
            <input type="checkbox" id="vehicle3" name="vehicle" value="Boat">
            <label for="vehicle3"> I have a boat</label><br><br>
            <input type="submit" value="Submit"> --}}
            @endforeach
            @endif
        </div>
        {{-- @include('custom_fields.formBuilder') --}}

        <div class="form-group col-12 text-right">
            <input type="submit" value="{{__('Add Operator')}}" class="btn-create badge-blue">
            <input type="button" value="{{__('Cancel')}}" class="btn-create bg-gray" data-dismiss="modal">
        </div>
    </div>
    {{ Form::close() }}
</div>