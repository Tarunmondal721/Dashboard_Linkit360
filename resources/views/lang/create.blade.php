<div class="card bg-none card-box">
    <form method="post" action="{{ route('lang.store') }}">
        @csrf
        <div class="row">
            <div class="form-group col-12">
                <label for="code" class="form-control-label">{{ __('Language Code') }}</label>
                <input class="form-control" type="text" id="code" name="code" required="" placeholder="{{ __('Language Code') }}">
            </div>
            <div class="form-group col-12 text-right">
                <input type="submit" value="{{__('Create')}}" class="btn-create badge-blue">
                <input type="button" value="{{__('Cancel')}}" class="btn-create bg-gray" data-dismiss="modal">
            </div>
        </div>
    </form>
</div>
