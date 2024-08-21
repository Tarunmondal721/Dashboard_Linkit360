<div class="card bg-none card-box">
    <form class="pl-3 pr-3" method="post" action="{{ route('users.store') }}"onsubmit="return confirmPassword()" >
        @csrf
        <div class="row">
            <div class="col-6 form-group">
                <label class="form-control-label" for="name">{{ __('Name') }}</label>
                <input type="text" class="form-control" id="name" name="name" />
            </div>
            <div class="col-6 form-group">
                <label class="form-control-label" for="name">{{ __('User Name') }}</label>
                <input type="text" class="form-control" id="user_name" name="user_name" />
            </div>
            <div class="col-6 form-group">
                <label class="form-control-label" for="email">{{ __('E-Mail Address') }}</label>
                <input type="email" class="form-control" id="email" name="email" />
            </div>
            <div class="col-6 form-group">
                <label class="form-control-label" for="password">{{ __('Password') }}</label>
                <input type="password" class="form-control" id="password" name="password" />
            </div>
            <div class="col-6 form-group">
                <label class="form-control-label" for="confirm_password">{{ __('Confirm Password') }}</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" />
                <span class="gu-hide" style="color: red;" id="erroremail">{{__('password and confirm password must be match')}}</span>
            </div>
            <div class="col-6 form-group">
                <label class="form-control-label" for="role">{{ __('Role') }}</label>
                <select name="role" class="form-control select2"  id="role">
                    <option value="">{{__('Select Role')}}</option>
                    @foreach($roles as $role)
                        <option value="{{$role->id}}">{{$role->name}}</option>
                    @endforeach
                </select>
            </div>

            @include('custom_fields.formBuilder')

            <div class="form-group col-12 text-right">
                <input type="submit" value="{{__('Create')}}" class="btn-create badge-blue">
                <input type="button" value="{{__('Cancel')}}" class="btn-create bg-gray" data-dismiss="modal">
            </div>
        </div>
    </form>
</div>
