<div class="card bg-none card-box">
    {{ Form::model($country, array('route' => array('countries.create'), 'method' => 'POST', 'enctype' => 'multipart/form-data')) }}
    <div class="row">
        <div class="col-6 form-group">
            <label class="form-control-label" for="name">{{ __('Country') }}</label>
            <input type="text" class="form-control" id="country" name="country" value="{{$country->country}}" required/>
        </div>
        <div class="col-6 form-group">
            <label class="form-control-label" for="name">{{ __('Country Code') }}</label>
            <input type="text" class="form-control" id="country_code" name="country_code" value="{{$country->country_code}}" required/>
        </div>
    </div>
    <div class="row">
        <div class="col-6 form-group">
            <label class="form-control-label" for="name">{{ __('Currency Code') }}</label>
            <input type="text" class="form-control" id="currency_code" name="currency_code" value="{{$country->currency_code}}" required/>
        </div>
        <div class="col-6 form-group">
            <label class="form-control-label" for="name">{{ __('USD') }}</label>
            <input type="text" class="form-control" id="usd" name="usd" value="{{$country->usd}}" required/>
        </div>
    </div>
    {{-- <div class="row">
        <div class="col-12 form-group">
            <div class="choose-file">
                <label class="form-control-label">
                    Flag
                    <div>{{__('Choose file here')}}</div>
                    <input class="form-control" name="flag" type="file" id="flag" accept="image/*" data-filename="" required/>
                </label>
                <p class="flag_image"></p>
            </div>
        </div>
    </div> --}}
    <div class="row">
        <div class="form-group col-12 text-right">
            <input type="submit" value="{{__('Create')}}" class="btn-create badge-blue">
            <input type="button" value="{{__('Cancel')}}" class="btn-create bg-gray" data-dismiss="modal">
        </div>
    </div>
    {{ Form::close() }}
</div>
