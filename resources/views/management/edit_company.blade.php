<div class="card bg-none card-box">
    {{ Form::model($company, array('route' => array('companies.update', $company->id), 'method' => 'POST')) }}
    <div class="row">
        <div class="col-12 form-group">
            <label class="form-control-label" for="name">{{ __('Name') }}</label>
            <input type="text" class="form-control" id="company" name="company" value="{{$company->name}}" required/>
        </div>

        {{-- @include('custom_fields.formBuilder') --}}

        <div class="form-group col-12 text-right">
            <input type="submit" value="{{__('Update')}}" class="btn-create badge-blue">
            <input type="button" value="{{__('Cancel')}}" class="btn-create bg-gray" data-dismiss="modal">
        </div>
    </div>
    {{ Form::close() }}
</div>