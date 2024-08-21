<div class="card bg-none card-box">
    <form class="pl-3 pr-3" method="post" action="{{ route('users.store') }}">
        @csrf
        <div class="row">
            <div class="col-6 form-group">
                <label class="form-control-label" for="name">{{ __('Name') }}</label>
                <input type="text" class="form-control" id="name" name="name" required/>
            </div>
            <div class="col-6 form-group">
                <label class="form-control-label" for="email">{{ __('E-Mail Address') }}</label>
                <input type="email" class="form-control" id="email" name="email" required/>
            </div>
            <div class="col-6 form-group">
                <label class="form-control-label" for="password">{{ __('Password') }}</label>
                <input type="text" class="form-control" id="password" name="password" required/>
            </div>
            <div class="form-group col-12 text-right">
                <input type="submit" value="{{__('Create')}}" class="btn-create badge-blue">
                <input type="button" value="{{__('Cancel')}}" class="btn-create bg-gray" data-dismiss="modal">
            </div>
        </div>
    </form>
</div>
