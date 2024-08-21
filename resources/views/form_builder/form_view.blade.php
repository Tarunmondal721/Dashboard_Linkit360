@php
    $logo=asset(Storage::url('logo/'));
    $company_logo=Utility::getValByName('company_logo');
    $company_small_logo=Utility::getValByName('company_favicon');
@endphp

    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title> {{__('Form')}} &dash; {{(Utility::getValByName('header_text')) ? Utility::getValByName('header_text') : config('app.name', 'LeadGo')}}</title>

    <link rel="icon" href="{{$logo.'/'.(isset($company_small_logo) && !empty($company_small_logo)?$company_small_logo:'favicon.png')}}" type="image">
    <link rel="stylesheet" href="{{ asset('assets/libs/@fortawesome/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/site.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/ac.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/stylesheet.css') }}">
</head>

<body>
<div class="login-contain">
    <div class="login-inner-contain">
        <a class="navbar-brand" href="#">
            <img src="{{$logo.'/'.(isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo-full.png')}}" alt="{{ config('app.name', 'LeadGo') }}" class="navbar-brand-img">
        </a>
        <div class="login-form w-25 px-5">
            @if($form->is_active == 1)
                <div class="page-title"><h5>{{$form->name}}</h5></div>
                <form method="POST" action="{{ route('form.view.store') }}">
                    @csrf
                    @if($objFields && $objFields->count() > 0)
                        @foreach($objFields as $objField)
                            @if($objField->type == 'text')
                                <div class="form-group">
                                    {{ Form::label('field-'.$objField->id, __($objField->name),['class'=>'form-control-label']) }}
                                    {{ Form::text('field['.$objField->id.']', null, array('class' => 'form-control','required'=>'required','id'=>'field-'.$objField->id)) }}
                                </div>
                            @elseif($objField->type == 'email')
                                <div class="form-group">
                                    {{ Form::label('field-'.$objField->id, __($objField->name),['class'=>'form-control-label']) }}
                                    {{ Form::email('field['.$objField->id.']', null, array('class' => 'form-control','required'=>'required','id'=>'field-'.$objField->id)) }}
                                </div>
                            @elseif($objField->type == 'number')
                                <div class="form-group">
                                    {{ Form::label('field-'.$objField->id, __($objField->name),['class'=>'form-control-label']) }}
                                    {{ Form::number('field['.$objField->id.']', null, array('class' => 'form-control','required'=>'required','id'=>'field-'.$objField->id)) }}
                                </div>
                            @elseif($objField->type == 'date')
                                <div class="form-group">
                                    {{ Form::label('field-'.$objField->id, __($objField->name),['class'=>'form-control-label']) }}
                                    {{ Form::date('field['.$objField->id.']', null, array('class' => 'form-control','required'=>'required','id'=>'field-'.$objField->id)) }}
                                </div>
                            @elseif($objField->type == 'textarea')
                                <div class="form-group">
                                    {{ Form::label('field-'.$objField->id, __($objField->name),['class'=>'form-control-label']) }}
                                    {{ Form::textarea('field['.$objField->id.']', null, array('class' => 'form-control','required'=>'required','id'=>'field-'.$objField->id)) }}
                                </div>
                            @endif
                        @endforeach
                        <input type="hidden" value="{{$code}}" name="code">
                        <button type="submit" class="btn-login" tabindex="4">{{ __('Submit') }}</button>
                    @endif
                </form>
            @else
                <div class="page-title"><h5>{{__('Form is not active.')}}</h5></div>
            @endif
        </div>
    </div>
</div>

<script src="{{asset('assets/js/jquery.min.js')}}"></script>
<script src="{{asset('assets/js/custom.js')}}"></script>

@if ($message = Session::get('success'))
    <script>show_toastr('Success', '{!! $message !!}', 'success')</script>
@endif

@if ($message = Session::get('error'))
    <script>show_toastr('Error', '{!! $message !!}', 'error')</script>
@endif

@if ($message = Session::get('info'))
    <script>show_toastr('Info', '{!! $message !!}', 'info')</script>
@endif
</body>
</html>
