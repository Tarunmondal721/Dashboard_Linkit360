@extends('layouts.auth')

@section('title')
    {{ __('Reset Password') }}
@endsection

@section('content')
    <div class="login-form">
        <div class="page-title"><h5>{{__('Reset Password')}}</h5></div>
        @if(session('status'))
            <div class="alert alert-primary">
                {{ session('status') }}
            </div>
        @endif
        <p class="text-xs text-muted">{{__('We will send a link to reset your password')}}</p>
<!--         <form method="POST" action="{{ route('password.email') }}">
        <form method="" action=""> -->
            @csrf
            <div class="form-group">
                <label for="email" class="form-control-label">{{ __('E-Mail Address') }}</label>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                @error('email')
                <span class="invalid-feedback" role="alert">
                    <small>{{ $message }}</small>
                </span>
                @enderror
            </div>
            <span class="error_msg_fpswrd" style="display: none; color: red">
                    <small>Reset Link not Send Due to SMTP Not Setup!!!</small>
                </span>
            <button type="submit" class="btn-login click_fpswrd">{{ __('Send Password Reset Link') }}</button>
            <div class="or-text">{{__('OR')}}</div>
            <div class="text-xs text-muted text-center">
                {{__("Back to")}} <a href="{{route('login',$lang)}}">{{__('Login')}}</a>
            </div>
        <!-- </form> -->
    </div>
@endsection
