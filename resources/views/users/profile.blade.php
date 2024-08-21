@extends('layouts.admin')

@section('title')
    {{ __('Edit Profile') }}
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-3 col-lg-4 col-md-4 col-sm-12">
            <div class="card profile-card">
                <div class="icon-user avatar rounded-circle">
                    <img @if($user->avatar) src="{{asset('avatars/'.$user->avatar)}}" @else src="{{asset('assets/img/avatar/avatar-1.png')}}" @endif>
                </div>
                <h4 class="h4 mb-0 mt-2">{{ \Auth::user()->name }}</h4>
                <div class="sal-right-card">
                    <span class="badge badge-pill badge-blue">{{ \Auth::user()->type }}</span>
                </div>
                <h6 class="office-time mb-0 mt-4">{{ \Auth::user()->email }}</h6>
                @if($user->avatar!='')
                    <div class="mt-4">
                        <a href="#" class="delete-icon" data-toggle="tooltip" data-original-title="{{__('Delete Profile Photo')}}" onclick="document.getElementById('delete_avatar').submit();"><i class="fas fa-trash"></i></a>
                    </div>
                @endif
            </div>
        </div>
        <div class="col-xl-9 col-lg-8 col-md-8 col-sm-12">
            <section class="col-lg-12 pricing-plan card">
                <div class="our-system password-card p-3">
                    <div class="row">
                        <ul class="nav nav-tabs my-4">
                            <li>
                                <a data-toggle="tab" href="#personal_info" class="active">{{__('Personal info')}}</a>
                            </li>
                            <li>
                                <a data-toggle="tab" href="#change_password" class="">{{__('Change Password')}}</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div id="personal_info" class="tab-pane in active">
                                <form method="post" action="{{route('update.profile')}}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-6 col-sm-6">
                                            <div class="form-group">
                                                <label for="name" class="form-control-label text-dark">{{__('Name')}}</label>
                                                <input class="form-control @error('fname') is-invalid @enderror" name="name" type="text" id="name" placeholder="{{ __('Enter Your First Name') }}" value="{{ $user->name }}" required autocomplete="fname">

                                                {{-- <input type="hidden" name="user_ai_id" value="{{ $user->id }}"> --}}
                                                @error('name')
                                                <span class="invalid-feedback text-danger text-xs" role="alert">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- <div class="col-lg-6 col-sm-6">
                                            <div class="form-group">
                                                <label for="mid_name" class="form-control-label text-dark">{{__('Middle Name')}}</label>
                                                <input class="form-control @error('mid_name') is-invalid @enderror" name="mid_name" type="text" id="mid_name" placeholder="{{ __('Enter Your Middle Name') }}" value="{{ $user->mid_name }}"  autocomplete="mid_name">

                                                @error('mid_name')
                                                <span class="invalid-feedback text-danger text-xs" role="alert">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-6">
                                            <div class="form-group">
                                                <label for="lname" class="form-control-label text-dark">{{__('Last Name')}}</label>
                                                <input class="form-control @error('lname') is-invalid @enderror" name="lname" type="text" id="lname" placeholder="{{ __('Enter Your Last Name') }}" value="{{ $user->lname }}" required autocomplete="lname">

                                                @error('lname')
                                                <span class="invalid-feedback text-danger text-xs" role="alert">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div> --}}
                                        <div class="col-lg-6 col-sm-6">
                                            <div class="form-group">
                                                <label for="email" class="form-control-label text-dark">{{__('Email')}}</label>
                                                <input class="form-control @error('email') is-invalid @enderror" name="email" type="text" id="email" placeholder="{{ __('Enter Your Email Address') }}" value="{{ $user->email }}" required autocomplete="email">
                                                @error('email')
                                                <span class="invalid-feedback text-danger text-xs" role="alert">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <div class="choose-file">
                                                    <label for="avatar">
                                                        <div>{{__('Choose file here')}}</div>
                                                        <input class="form-control" name="avatar" type="file" id="avatar" accept="image/*" data-filename="profile_update">
                                                    </label>
                                                    <p class="profile_update"></p>
                                                </div>
                                                @error('avatar')
                                                <span class="invalid-feedback text-danger text-xs" role="alert">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <span class="clearfix"></span>
                                            <span class="text-xs text-muted">{{ __('Please upload a valid image file. Size of image should not be more than 2MB.')}}</span>
                                        </div>
                                        <div class="col-lg-12 text-right">
                                            <input type="submit" value="{{__('Save Changes')}}" class="btn-create badge-blue">
                                        </div>
                                    </div>
                                </form>
                                @if($user->avatar!='')
                                    <form action="{{route('delete.avatar')}}" method="post" id="delete_avatar">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                @endif
                            </div>
                            <div id="change_password" class="tab-pane">
                                <form method="post" action="{{route('update.password')}}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-6 col-sm-6 form-group">
                                            <label for="old_password" class="form-control-label text-dark">{{ __('Old Password') }}</label>
                                            <input class="form-control @error('old_password') is-invalid @enderror" name="old_password" type="password" id="old_password" required autocomplete="old_password" placeholder="{{ __('Enter Old Password') }}">
                                            @error('old_password')
                                            <span class="invalid-feedback text-danger text-xs" role="alert">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="col-lg-6 col-sm-6 form-group">
                                            <label for="password" class="form-control-label text-dark">{{ __('Password') }}</label>
                                            <input class="form-control @error('password') is-invalid @enderror" name="password" type="password" required autocomplete="new-password" id="password" placeholder="{{ __('Enter Your Password') }}">
                                            @error('password')
                                            <span class="invalid-feedback text-danger text-xs" role="alert">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-6 col-sm-6 form-group">
                                            <label for="password_confirmation" class="form-control-label text-dark">{{ __('Confirm Password') }}</label>
                                            <input class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" type="password" required autocomplete="new-password" id="password_confirmation" placeholder="{{ __('Enter Your Password') }}">
                                        </div>
                                        <div class="col-lg-12 text-right">
                                            <input type="submit" value="{{__('Change Password')}}" class="btn-create badge-blue">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
