<div class="card bg-none card-box">
    {{ Form::open(array('url' => 'custom_fields')) }}
    <div class="row">
        <div class="form-group col-12">
            {{ Form::label('name', __('Custom Field Name'),['class'=>'form-control-label']) }}
            {{ Form::text('name', '', array('class' => 'form-control')) }}
        </div>
        <div class="form-group col-6">
            {{ Form::label('type', __('Type'),['class'=>'form-control-label']) }}
            {{ Form::select('type', $types,null, array('class' => 'form-control select2','required'=>'required')) }}
        </div>
        <div class="form-group col-6">
            {{ Form::label('module', __('Modules'),['class'=>'form-control-label']) }}
            {{ Form::select('module', $modules,null, array('class' => 'form-control select2','required'=>'required')) }}
        </div>
        <div class="col-12 text-right">
            <input type="submit" value="{{__('Create')}}" class="btn-create badge-blue">
            <input type="button" value="{{__('Cancel')}}" class="btn-create bg-gray" data-dismiss="modal">
        </div>
    </div>
    {{ Form::close() }}
</div>
