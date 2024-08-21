<div class="card bg-none card-box">
    {{ Form::model($user, array('route' => array('users.update', $user->id), 'method' => 'POST')) }}
    <div class="row">
        <div class="col-6 form-group">
            <label class="form-control-label" for="name">{{ __('Name') }}</label>
            <input type="text" class="form-control" id="name" name="name" value="{{$user->name}}" required/>
        </div>
        <div class="col-6 form-group">
            <label class="form-control-label" for="name">{{ __('User Name') }}</label>
            <input type="text" class="form-control" id="user_name" name="user_name" value="{{$user->user_name}}" required/>
        </div>
        <div class="col-6 form-group">
            <label class="form-control-label" for="email">{{ __('E-Mail Address') }}</label>
            <input type="email" class="form-control" id="email" name="email" value="{{$user->email}}" required/>
        </div>
        {{-- <div class="col-6 form-group">
            <label class="form-control-label" for="password">{{ __('Password') }}</label>
            <input type="text" class="form-control" id="password" name="password"/>
        </div>
        <div class="col-6 form-group">
            <label class="form-control-label" for="job_title">{{ __('Job Title') }}</label>
            <input type="text" class="form-control" id="job_title" name="job_title" value="{{$user->job_title}}"/>
        </div> --}}
        <div class="col-6 form-group">
            <label class="form-control-label" for="password">{{ __('Role') }}</label>
            <select name="role" class="form-control select2" required>
                <option value="">{{__('Select Role')}}</option>
                @foreach($roles as $role)
                    <option value="{{$role->id}}" @if($role->id == $userRole) selected @endif>{{$role->name}}</option>
                @endforeach
            </select>
        </div>

        @include('custom_fields.formBuilder')

        <div class="form-group col-12 text-right">
            <input type="submit" value="{{__('Update')}}" class="btn-create badge-blue">
            <input type="button" value="{{__('Cancel')}}" class="btn-create bg-gray" data-dismiss="modal">
        </div>
    </div>
    {{ Form::close() }}
</div>
