@extends('layouts.admin')

@section('title')
    {{ __('Settings') }}
@endsection

@push('script')
    <script src="{{ asset('assets/js/jscolor.js') }} "></script>
    <script>
        $(document).on("change", "select[name='invoice_template'], input[name='invoice_color']", function () {
            var template = $("select[name='invoice_template']").val();
            var color = $("input[name='invoice_color']:checked").val();
            $('#invoice_frame').attr('src', '{{url('/invoices/preview')}}/' + template + '/' + color);
        });

        $(document).on("change", "select[name='estimation_template'], input[name='estimation_color']", function () {
            var template = $("select[name='estimation_template']").val();
            var color = $("input[name='estimation_color']:checked").val();
            $('#estimation_frame').attr('src', '{{url('/estimations/preview')}}/' + template + '/' + color);
        });

        $(document).on("change", "select[name='mdf_template'], input[name='mdf_color']", function () {
            var template = $("select[name='mdf_template']").val();
            var color = $("input[name='mdf_color']:checked").val();
            $('#mdf_frame').attr('src', '{{url('/mdf/preview')}}/' + template + '/' + color);
        });

    </script>
    <script>
        $(document).ready(function () {
            if ($('.gdpr_fulltime').is(':checked') ) {

                $('.fulltime').show();
            } else {

                $('.fulltime').hide();
            }

            $('#gdpr_cookie').on('change', function() {
                if ($('.gdpr_fulltime').is(':checked') ) {

                    $('.fulltime').show();
                } else {

                    $('.fulltime').hide();
                }
            });
        });

    </script>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <ul class="nav nav-tabs mb-4" role="tablist">
                <li>
                    <a class="active" id="contact-tab2" data-toggle="tab" href="#business-setting" role="tab" aria-controls="" aria-selected="false">{{__('Site Setting')}}</a>
                </li>
                <li>
                    <a id="contact-tab4" data-toggle="tab" href="#system-setting" role="tab" aria-controls="" aria-selected="false">{{__('System Setting')}}</a>
                </li>
                <li>
                    <a id="profile-tab3" data-toggle="tab" href="#company-setting" role="tab" aria-controls="" aria-selected="false">{{__('Company Setting')}}</a>
                </li>
                <li>
                    <a id="profile-tab3" data-toggle="tab" href="#middleware-setting" role="tab" aria-controls="" aria-selected="false">{{__('Middleware Setting')}}</a>
                </li>
                <li>
                    <a id="profile-tab3" data-toggle="tab" href="#ip-setting" role="tab" aria-controls="" aria-selected="false">{{__('IP Clients Setting')}}</a>
                </li>
                <!-- <li>
                    <a id="profile-tab4" data-toggle="tab" href="#company-payment-setting" role="tab" aria-controls="" aria-selected="false">{{__('Payment Setting')}}</a>
                </li>
                <li>
                    <a id="profile-tab6" data-toggle="tab" href="#invoice-setting" role="tab" aria-controls="" aria-selected="false">{{__('Invoice Print Setting')}}</a>
                </li> -->
                <!-- <li>
                    <a id="profile-tab7" data-toggle="tab" href="#estimation-setting" role="tab" aria-controls="" aria-selected="false">{{__('Estimation Print Setting')}}</a>
                </li> -->
                <!-- <li>
                    <a id="profile-tab8" data-toggle="tab" href="#mdf-setting" role="tab" aria-controls="" aria-selected="false">{{__('MDF Print Setting')}}</a>
                </li> -->
            </ul>
            <div class="tab-content" id="myTabContent2">
                <div class="tab-pane fade fade show active" id="business-setting" role="tabpanel" aria-labelledby="profile-tab3">
                    <form method="post" action="{{route('site.settings.store')}}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                <h4 class="small-title">{{__('Site Settings')}}</h4>
                                <div class="card setting-card">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="logo" class="form-control-label">{{ __('Favicon') }}</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 text-center my-auto">
                                            <img src="{{asset(Storage::url('logo/favicon.png'))}}" class="setting-img"/>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <div class="input-file btn-file">{{__('Select image')}}
                                                    <input type="file" name="favicon" id="favicon" class="form-control {{($errors->has('favicon')) ? 'is-invalid' : ''}}" accept="image/png" data-filename="favicon_update"/>
                                                </div>
                                                <p class="favicon_update text-xs"></p>
                                                @if ($errors->has('favicon'))
                                                    <span class="invalid-feedback text-xs d-block">{{ $errors->first('favicon') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="full_logo" class="form-control-label">{{ __('Logo') }}</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 text-center my-auto">
                                            <img src="{{asset(Storage::url('logo/logo-full.png'))}}" class="setting-img"/>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <div class="input-file btn-file">{{__('Select image')}}
                                                    <input type="file" name="full_logo" id="full_logo" class="form-control {{($errors->has('full_logo')) ? 'is-invalid' : ''}}" accept="image/png" data-filename="logo_update"/>
                                                </div>
                                                <p class="logo_update text-xs"></p>
                                                @if ($errors->has('full_logo'))
                                                    <span class="invalid-feedback text-xs d-block">{{ $errors->first('full_logo') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <!-- <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="landing_logo" class="form-control-label">{{ __('Landing Page Logo') }}</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 text-center my-auto">
                                            <img src="{{asset(Storage::url('logo/landing_logo.png'))}}" class="setting-img"/>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <div class="input-file btn-file">{{__('Select image')}}
                                                    <input type="file" name="landing_logo" id="landing_logo" class="form-control {{($errors->has('landing_logo')) ? 'is-invalid' : ''}}" accept="image/png" data-filename="landing_logo_update"/>
                                                </div>
                                                <p class="landing_logo_update text-xs"></p>
                                                @if ($errors->has('landing_logo'))
                                                    <span class="invalid-feedback text-xs d-block">{{ $errors->first('landing_logo') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-6 my-auto">
                                            <div class="form-group">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" name="enable_landing" value="yes" class="custom-control-input" id="enable_landing" {{ (Utility::getValByName('enable_landing') == 'yes') ? 'checked' : '' }}>
                                                    <label class="custom-control-label font-weight-bold text-dark text-xs" for="enable_landing">{{ __('Enable Landing Page') }}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div> -->
                                    <!-- <div class="row">
                                        <div class="col-12 my-auto">
                                            <div class="form-group">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input" name="SITE_RTL" id="SITE_RTL" {{ env('SITE_RTL') == 'on' ? 'checked="checked"' : '' }}>

                                                    <label class="custom-control-label form-control-label" for="SITE_RTL"></label>
                                                    {{Form::label('SITE_RTL',__('RTL'),array('class'=>'form-control-label')) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            {{Form::label('gdpr_cookie',__('GDPR Cookie')) }}

                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input gdpr_fulltime gdpr_type" name="gdpr_cookie" id="gdpr_cookie" {{ isset($settings['gdpr_cookie']) && $settings['gdpr_cookie'] == 'on' ? 'checked="checked"' : '' }}>
                                                <label class="custom-control-label form-control-label" for="gdpr_cookie"></label>
                                            </div>
                                        </div>


                                        <div class="form-group col-md-12">
                                            {{Form::label('cookie_text',__('GDPR Cookie Text'),array('class'=>'fulltime') )}}
                                            <input type="text" name="cookie_text" class="form-control fulltime" value="{{isset($settings['cookie_text']) && $settings['cookie_text'] ? $settings['cookie_text'] : ''}}" style="display: hidden;">
                                        </div>
                                    </div> -->
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                <h4 class="small-title">{{__('Mailer Settings')}}</h4>
                                <div class="card setting-card">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="mail_driver" class="form-control-label">{{ __('Mail Driver') }}</label>
                                                <input type="text" name="mail_driver" id="mail_driver" class="form-control {{($errors->has('mail_driver')) ? 'is-invalid' : ''}}" value="{{env('MAIL_MAILER')}}" placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_driver_placeholder') }}"/>
                                                @if ($errors->has('mail_driver'))
                                                    <span class="invalid-feedback text-xs text-xs d-block">
                                                        {{ $errors->first('mail_driver') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="mail_host" class="form-control-label">{{ __('Mail Host') }}</label>
                                                <input type="text" name="mail_host" id="mail_host" class="form-control {{($errors->has('mail_host')) ? 'is-invalid' : ''}}" value="{{env('MAIL_HOST')}}" placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_host_placeholder') }}"/>
                                                @if ($errors->has('mail_host'))
                                                    <span class="invalid-feedback text-xs d-block">
                                                        {{ $errors->first('mail_host') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="mail_port" class="form-control-label">{{ __('Mail Port') }}</label>
                                                <input type="number" name="mail_port" id="mail_port" class="form-control {{($errors->has('mail_port')) ? 'is-invalid' : ''}}" value="{{env('MAIL_PORT')}}" placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_port_placeholder') }}"/>
                                                @if ($errors->has('mail_port'))
                                                    <span class="invalid-feedback text-xs d-block">
                                                                        {{ $errors->first('mail_port') }}
                                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="mail_username" class="form-control-label">{{ __('Mail Username') }}</label>
                                                <input type="text" name="mail_username" id="mail_username" class="form-control {{($errors->has('mail_username')) ? 'is-invalid' : ''}}" value="{{env('MAIL_USERNAME')}}" placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_username_placeholder') }}"/>
                                                @if ($errors->has('mail_username'))
                                                    <span class="invalid-feedback text-xs d-block">
                                                                        {{ $errors->first('mail_username') }}
                                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="mail_password" class="form-control-label">{{ __('Mail Password') }}</label>
                                                <input type="text" name="mail_password" id="mail_password" class="form-control {{($errors->has('mail_password')) ? 'is-invalid' : ''}}" value="{{env('MAIL_PASSWORD')}}" placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_password_placeholder') }}"/>
                                                @if ($errors->has('mail_password'))
                                                    <span class="invalid-feedback text-xs d-block">
                                                                        {{ $errors->first('mail_password') }}
                                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="mail_encryption" class="form-control-label">{{ __('Mail Encryption') }}</label>
                                                <input type="text" name="mail_encryption" id="mail_encryption" class="form-control {{($errors->has('mail_encryption')) ? 'is-invalid' : ''}}" value="{{env('MAIL_ENCRYPTION')}}" placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_encryption_placeholder') }}"/>
                                                @if ($errors->has('mail_encryption'))
                                                    <span class="invalid-feedback text-xs d-block">
                                                                        {{ $errors->first('mail_encryption') }}
                                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="mail_from_address" class="form-control-label">{{ __('Mail From Address') }}</label>
                                                <input type="text" name="mail_from_address" id="mail_from_address" class="form-control {{($errors->has('mail_from_address')) ? 'is-invalid' : ''}}" value="{{env('MAIL_FROM_ADDRESS')}}" placeholder="{{ __('Enter Mail From Address') }}"/>
                                                @if ($errors->has('mail_from_address'))
                                                    <span class="invalid-feedback text-xs d-block">
                                                    {{ $errors->first('mail_from_address') }}
                                            </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="mail_from_name" class="form-control-label">{{ __('Mail From Name') }}</label>
                                                <input type="text" name="mail_from_name" id="mail_from_name" class="form-control {{($errors->has('mail_from_name')) ? 'is-invalid' : ''}}" value="{{env('MAIL_FROM_NAME')}}" placeholder="{{ __('Enter Mail From Name') }}"/>
                                                @if ($errors->has('mail_from_name'))
                                                    <span class="invalid-feedback text-xs d-block">
                                                    {{ $errors->first('mail_from_name') }}
                                            </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-12 text-right">
                                            <button type="submit" class="btn btn-md badge-blue text-xs rounded">Submit</button>
                                            <a href="#" class="btn btn-xs bg-warning text-white  width-auto"  data-ajax-popup="true" data-title="{{__('Send Test Mail')}}" data-url="{{route('test.email')}}">
                                                {{__('Send Test Mail')}}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="col-12">
                                <h4 class="small-title">{{__('Pusher Settings')}}</h4>
                                <div class="card setting-card">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" name="enable_chat" value="yes" class="custom-control-input" id="enable_chat" @if(env('CHAT_MODULE') =='yes') checked @endif>
                                                    <label class="custom-control-label font-weight-bold text-dark text-sm" for="enable_chat">{{ __('Enable Chat') }}</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="pusher_app_id" class="form-control-label">{{ __('Pusher App Id') }}</label>
                                                <input type="text" name="pusher_app_id" id="pusher_app_id" class="form-control {{($errors->has('pusher_app_id')) ? 'is-invalid' : ''}}" value="{{env('PUSHER_APP_ID')}}" placeholder="{{ __('Pusher App Id') }}"/>
                                                @if ($errors->has('pusher_app_id'))
                                                    <span class="invalid-feedback text-xs d-block">
                                                    {{ $errors->first('pusher_app_id') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="pusher_app_key" class="form-control-label">{{ __('Pusher App Key') }}</label>
                                                <input type="text" name="pusher_app_key" id="pusher_app_key" class="form-control {{($errors->has('pusher_app_key')) ? 'is-invalid' : ''}}" value="{{env('PUSHER_APP_KEY')}}" placeholder="{{ __('Pusher App Key') }}"/>
                                                @if ($errors->has('pusher_app_key'))
                                                    <span class="invalid-feedback text-xs d-block">
                                                    {{ $errors->first('pusher_app_key') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="pusher_app_secret" class="form-control-label">{{ __('Pusher App Secret') }}</label>
                                                <input type="text" name="pusher_app_secret" id="pusher_app_secret" class="form-control {{($errors->has('pusher_app_secret')) ? 'is-invalid' : ''}}" value="{{env('PUSHER_APP_SECRET')}}" placeholder="{{ __('Pusher App Secret') }}"/>
                                                @if ($errors->has('pusher_app_secret'))
                                                    <span class="invalid-feedback text-xs d-block">
                                                    {{ $errors->first('pusher_app_secret') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="pusher_app_cluster" class="form-control-label">{{ __('Pusher App Cluster') }}</label>
                                                <input type="text" name="pusher_app_cluster" id="pusher_app_cluster" class="form-control {{($errors->has('pusher_app_cluster')) ? 'is-invalid' : ''}}" value="{{env('PUSHER_APP_CLUSTER')}}" placeholder="{{ __('Pusher App Cluster') }}"/>
                                                @if ($errors->has('pusher_app_cluster'))
                                                    <span class="invalid-feedback text-xs d-block">
                                                    {{ $errors->first('pusher_app_cluster') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <a href="https://pusher.com/channels" class="text-xs" target="_blank">{{__('You can Make Pusher channel Account from here and Get your App Id and Secret key')}}</a>
                                        </div>
                                        <div class="form-group col-md-12 text-right">
                                            <input type="submit" value="{{__('Save Changes')}}" class="btn-create badge-blue">
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                        </div>
                    </form>
                </div>
                <div class="tab-pane fade" id="system-setting" role="tabpanel" aria-labelledby="profile-tab3">
                    <form id="setting-form" method="post" action="{{route('settings.store')}}">
                        @csrf
                        <div class="card bg-none">
                            <div class="row company-setting">
                                <div class="col-lg-4 col-md-6 col-sm-6 form-group">
                                    <label class="form-control-label">{{__('Title Text')}}</label>
                                    <input type="text" name="header_text" class="form-control" id="header_text" value="{{ Utility::getValByName('header_text') }}" placeholder="{{ __('Enter Header Title Text') }}">
                                    @error('header_text')
                                    <span class="invalid-header_text text-xs" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-6 form-group">
                                    <label class="form-control-label">{{__('Footer Text')}}</label>
                                    <input type="text" name="footer_text" class="form-control" id="footer_text" value="{{ Utility::getValByName('footer_text') }}" placeholder="{{ __('Enter Footer Text') }}">
                                    @error('footer_text')
                                    <span class="invalid-header_text text-xs" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-6 form-group">
                                    <label class="form-control-label">{{__('Default Language')}}</label>
                                    <select name="default_language" id="default_language" class="form-control select2">
                                        @foreach(Utility::languages() as $language)
                                            <option @if(Utility::getValByName('default_language') == $language) selected @endif value="{{$language}}">{{Str::upper($language)}}</option>
                                        @endforeach
                                    </select>
                                    @error('default_language')
                                    <span class="invalid-header_text text-xs" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-6 form-group">
                                    <label class="form-control-label">{{__('Currency')}} *</label>
                                    <input type="text" name="site_currency" class="form-control" id="site_currency" value="{{$settings['site_currency']}}" required>
                                    <small class="text-xs">
                                        {{ __('Note: Add currency code as per three-letter ISO code') }}.
                                        <a href="https://stripe.com/docs/currencies" target="_blank">{{ __('you can find out here..') }}</a>
                                    </small>
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-6 form-group">
                                    <label class="form-control-label">{{__('Currency Symbol')}} *</label>
                                    <input type="text" name="site_currency_symbol" class="form-control" id="site_currency_symbol" value="{{$settings['site_currency_symbol']}}" required>
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-6 form-group">
                                    <label class="form-control-label">{{__('Currency Symbol Position')}} *</label>
                                    <div class="d-flex radio-check">
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="pre" value="pre" name="site_currency_symbol_position" class="custom-control-input" @if($settings['site_currency_symbol_position'] == 'pre') checked @endif>
                                            <label class="custom-control-label form-control-label" for="pre">{{__('Pre')}}</label>
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="post" value="post" name="site_currency_symbol_position" class="custom-control-input" @if($settings['site_currency_symbol_position'] == 'post') checked @endif>
                                            <label class="custom-control-label form-control-label" for="post">{{__('Post')}}</label>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-lg-4 col-md-6 col-sm-6 form-group">
                                    <label for="site_date_format" class="form-control-label">{{__('Date Format')}}</label>
                                    <select type="text" name="site_date_format" class="form-control select2" id="site_date_format">
                                        <option value="M j, Y" @if($settings['site_date_format'] == 'M j, Y') selected="selected" @endif>{{date('M d Y')}}</option>
                                        <option value="d-m-Y" @if($settings['site_date_format'] == 'd-m-Y') selected="selected" @endif>{{date('d-m-y')}}</option>
                                        <option value="m-d-Y" @if($settings['site_date_format'] == 'm-d-Y') selected="selected" @endif>{{date('m-d-y')}}</option>
                                        <option value="Y-m-d" @if($settings['site_date_format'] == 'Y-m-d') selected="selected" @endif>{{date('y-m-d')}}</option>
                                    </select>
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-6 form-group">
                                    <label for="site_time_format" class="form-control-label">{{__('Time Format')}}</label>
                                    <select type="text" name="site_time_format" class="form-control select2" id="site_time_format">
                                        <option value="g:i A" @if($settings['site_time_format'] == 'g:i A') selected="selected" @endif>{{date('H:s A')}} </option>
                                        <option value="g:i a" @if($settings['site_time_format'] == 'g:i a') selected="selected" @endif>{{date('H:s a')}}</option>
                                        <option value="H:i" @if($settings['site_time_format'] == 'H:i') selected="selected" @endif>{{date('G:s')}}</option>
                                    </select>
                                </div>

                                <div class="col-lg-4 col-md-6 col-sm-6 form-group">
                                    <label for="invoice_prefix" class="form-control-label">{{__('Invoice Prefix')}} *</label>
                                    <input type="text" name="invoice_prefix" class="form-control" id="invoice_prefix" value="{{$settings['invoice_prefix']}}" required>
                                </div>
                                <!-- <div class="col-lg-4 col-md-6 col-sm-6 form-group">
                                    <label for="estimation_prefix" class="form-control-label">{{__('Estimation Prefix')}} *</label>
                                    <input type="text" name="estimation_prefix" class="form-control" id="estimation_prefix" value="{{$settings['estimation_prefix']}}" required>
                                </div> -->
                                <!-- <div class="col-lg-4 col-md-6 col-sm-6 form-group">
                                    <label class="form-control-label">{{__('Invoice/Estimation/MDF Title')}} *</label>
                                    <input type="text" name="footer_title" class="form-control" id="footer_title" value="{{$settings['footer_title']}}">
                                </div> -->
                                <!-- <div class="col-lg-4 col-md-6 col-sm-6 form-group">
                                    <label for="footer_note" class="form-control-label">{{__('Invoice/Estimation/MDF Note')}} *</label>
                                    <textarea name="footer_note" id="footer_note" class="form-control">{{$settings['footer_note']}}</textarea>
                                </div> -->
                                <div class="form-group col-md-12 text-right">
                                    <input type="submit" id="save-btn" value="{{__('Save Changes')}}" class="btn-create badge-blue">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="tab-pane fade" id="company-setting" role="tabpanel" aria-labelledby="contact-tab4">
                    <form id="setting-form" method="post" action="{{route('settings.store')}}">
                        @csrf
                        <div class="card bg-none">
                            <div class="row company-setting">
                                <div class="col-lg-4 col-md-6 col-sm-6 form-group">
                                    <label for="company_name" class="form-control-label">{{__('Company Name')}} *</label>
                                    <input type="text" name="company_name" class="form-control" id="company_name" value="{{$settings['company_name']}}" required>
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-6 form-group">
                                    <label for="company_address" class="form-control-label">{{__('Address')}}</label>
                                    <input type="text" name="company_address" class="form-control" id="company_address" value="{{$settings['company_address']}}">
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-6 form-group">
                                    <label for="company_city" class="form-control-label">{{__('City')}}</label>
                                    <input type="text" name="company_city" class="form-control" id="company_city" value="{{$settings['company_city']}}">
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-6 form-group">
                                    <label for="company_state" class="form-control-label">{{__('State')}}</label>
                                    <input type="text" name="company_state" class="form-control" id="company_state" value="{{$settings['company_state']}}">
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-6 form-group">
                                    <label for="company_zipcode" class="form-control-label">{{__('Zip/Post Code')}}</label>
                                    <input type="text" name="company_zipcode" class="form-control" id="company_zipcode" value="{{$settings['company_zipcode']}}">
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-6 form-group">
                                    <label for="company_country" class="form-control-label">{{__('Country')}}</label>
                                    <input type="text" name="company_country" class="form-control" id="company_country" value="{{$settings['company_country']}}">
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-6 form-group">
                                    <label for="company_telephone" class="form-control-label">{{__('Telephone')}}</label>
                                    <input type="text" name="company_telephone" class="form-control" id="company_telephone" value="{{$settings['company_telephone']}}">
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-6 form-group">
                                    <label for="company_email" class="form-control-label">{{__('System Email')}} *</label>
                                    <input type="email" name="company_email" class="form-control" id="company_email" value="{{$settings['company_email']}}" required>
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-6 form-group">
                                    <label for="company_email_from_name" class="form-control-label">{{__('Email (From Name)')}} *</label>
                                    <input type="text" name="company_email_from_name" class="form-control" id="company_email_from_name" value="{{$settings['company_email_from_name']}}" required>
                                </div>
                                <div class="form-group col-md-12 text-right">
                                    <input type="submit" id="save-btn" value="{{__('Save Changes')}}" class="btn-create badge-blue">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                {{-- tab ip config settings client --}}
                <div class="tab-pane fade" id="middleware-setting" role="tabpanel" aria-labelledby="contact-tab4">
                    <form id="setting-form" method="post" action="{{route('middleware.store')}}">
                        @csrf
                        <div class="card bg-none">
                            <div class="row middleware-api m-3">
                                <div class="col-lg-6 col-md-6 col-sm-6 form-group " >
                                    <label class="form-control-label">{{__('Middleware Url API')}}</label>
                                    <input type="text" name="middleware_url_api" class="form-control" id="middleware_url_api" value="{{ App\Models\Configuration::where('key', 'middleware_url_api')->first()->value}}" placeholder="example url : example:8000/api/v1/">
                                    {{-- @error('middleware_url_api') --}}
                                    @if ($errors->has('middleware_url_api'))
                                    <span class="invalid-feedback text-xs text-xs d-block">
                                        {{ $errors->first('middleware_url_api') }}
                                    </span>
                                     @endif
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 form-group">
                                    <label class="form-control-label">{{__('Timeout Url API')}}</label>
                                    <input type="text" name="timeout_settings" class="form-control" id="timeout_settings" value="{{ App\Models\Configuration::where('key', 'timeout_settings')->first()->value}}">
                                    @if ($errors->has('timeout_settings'))
                                    <span class="invalid-feedback text-xs text-xs d-block">
                                        {{ $errors->first('timeout_settings') }}
                                    </span>
                                    @endif

                                </div>
                                <div class="form-group col-md-12 text-right">
                                    <input type="submit" id="save-btn" value="{{__('Save Changes')}}" class="btn-create badge-blue">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="tab-pane fade" id="ip-setting" role="tabpanel" aria-labelledby="contact-tab4">
                    
                    {{-- <div class="row all-button-box justify-content-end">
                        <div class="float-right col-lg-3">
                            <span><a href="" class="btn btn-xs btn-white btn-icon-only width-auto"><i class="fas fa-plus"></i></a></span>
                        </div>
                    </div> --}}
                    <div class="row justify-content-end mb-3">
                        <div class="col-3">
                            <a href="#"  class="float-right  btn btn-xs btn-white btn-icon-only width-auto"  data-ajax-popup="true" data-title="{{__('Add IP Client')}}" data-url="{{route('add.ip.client')}}"><i class="fas fa-plus"></i>{{__(' Add')}}</a>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-12 ">
                            <div class="card bg-none">
                                <div class="card-body">
                                    
                                    <div class="table-responsive">
                                        <table class="table table-striped dataTable">
                                            <thead>
                                            <tr>
                                                <th>{{__('Id')}}</th>
                                                <th>{{__('IP')}}</th>
                                                <th>{{__('Name')}}</th>
                                                <th >{{__('Status')}}</th>
                                                <th >{{__('Action')}}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            {{-- @if(isset($companies) && !empty($companies)) --}}

                                            @foreach ($listIp as $ip)
                                                <tr>
                                                    <td class="number">{{$ip['id']}}</td>
                                                    <td class="ip">{{$ip['ip_address']}}</td>
                                                    <td class="name">{{$ip['name']}}</td>
                                                    <td class="status">
                                                        @if ($ip['status'] ==true)
                                                            Active
                                                        @else
                                                            Inactive
                                                        @endif
                                                    </td>
                                                    <td class="Action">
                                                        <span>
                                                            <a href="#" class="edit-icon" data-url="{{ URL::to('/ip-settings'.'/'.$ip['id']) }}" data-ajax-popup="true" data-title="{{__('Edit Ip Client #'.$ip['ip_address'])}}"><i class="fas fa-pencil-alt"></i></a>
                                                            <a href="#delete-ip-modal" class="delete-icon" onclick="deleteIp('{{$ip['id']}}', '{{$ip['ip_address']}}')"><i class="fas fa-trash"></i></a>

                                                        </span>
                                                        {{-- <span>
                                                            <a href="javascript:void(0)" data-url="{{ URL::to('management/edit-company/'.$company->id) }}" data-size="lg" data-ajax-popup="true" data-title="{{__('Edit Company #'.$company->id)}}" class="edit-icon"><i class="fas fa-pencil-alt"></i></a> --}}
                                                            {{-- <a href="/ip-settings" class="delete-icon"  data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$ip['id']}}').submit();"><i class="fas fa-trash"></i></a> --}}
                                                        {{-- </span> --}}
                                                    </td>
                                                </tr>
                                            @endforeach
                                            {{-- @endif --}}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="company-payment-setting" role="tabpanel" aria-labelledby="contact-tab4">
                    <small class="text-dark font-weight-bold">{{__("This detail will use for collect payment on invoice from clients. On invoice client will find out pay now button based on your below configuration.")}}</small></br></br>



                    <form id="setting-form" method="post" action="{{route('payment.settings')}}">
                        @csrf
                        <div class="row">
                            <div class="col-8">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 col-sm-6 form-group">
                                                <label class="form-control-label">{{__('Currency')}} *</label>
                                                <input type="text" name="currency" class="form-control" id="currency" value="{{(!isset($payment['currency']) || is_null($payment['currency'])) ? '' : $payment['currency']}}" required>
                                                <small class="text-xs">
                                                    {{ __('Note: Add currency code as per three-letter ISO code') }}.
                                                    <a href="https://stripe.com/docs/currencies" target="_blank">{{ __('you can find out here..') }}</a>
                                                </small>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 form-group">
                                                <label for="currency_symbol" class="form-control-label">{{__('Currency Symbol')}}</label>
                                                <input type="text" name="currency_symbol" class="form-control" id="currency_symbol" value="{{(!isset($payment['currency_symbol']) || is_null($payment['currency_symbol'])) ? '' : $payment['currency_symbol']}}" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="accordion-2" class="accordion accordion-spaced">
                            <!-- Strip -->
                            <div class="card">
                                <div class="card-header py-4" id="heading-2-2" data-toggle="collapse" role="button" data-target="#collapse-2-2" aria-expanded="false" aria-controls="collapse-2-2">
                                    <h6 class="mb-0"><i class="far fa-credit-card mr-3"></i>{{ __('Stripe') }}</h6>

                                </div>
                                <div id="collapse-2-2" class="collapse" aria-labelledby="heading-2-2" data-parent="#accordion-2">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6 py-2">
                                                <h5 class="h5">{{ __('Stripe') }}</h5>
                                            </div>
                                            <div class="col-6 py-2 text-right">
                                                <div class="custom-control custom-switch">
                                                    <input type="hidden" name="is_stripe_enabled" value="off">
                                                    <input type="checkbox" class="custom-control-input" name="is_stripe_enabled" id="is_stripe_enabled" {{ isset($payment['is_stripe_enabled']) && $payment['is_stripe_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                    <label class="custom-control-label form-control-label" for="is_stripe_enabled">{{ __('Enable Stripe') }}</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="stripe_key">{{__('Stripe Key')}}</label>
                                                    <input class="form-control" placeholder="{{__('Stripe Key')}}" name="stripe_key" type="text" value="{{(!isset($payment['stripe_key']) || is_null($payment['stripe_key'])) ? '' : $payment['stripe_key']}}" id="stripe_key">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="stripe_secret">{{__('Stripe Secret')}}</label>
                                                    <input class="form-control " placeholder="{{ __('Stripe Secret') }}" name="stripe_secret" type="text" value="{{(!isset($payment['stripe_secret']) || is_null($payment['stripe_secret'])) ? '' : $payment['stripe_secret']}}" id="stripe_secret">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="stripe_secret">{{__('Stripe_Webhook_Secret')}}</label>
                                                    <input class="form-control " placeholder="{{ __('Enter Stripe Webhook Secret') }}" name="stripe_webhook_secret" type="text" value="{{(!isset($payment['stripe_webhook_secret']) || is_null($payment['stripe_webhook_secret'])) ? '' : $payment['stripe_webhook_secret']}}" id="stripe_webhook_secret">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Paypal -->
                            <div class="card">
                                <div class="card-header py-4" id="heading-2-3" data-toggle="collapse" role="button" data-target="#collapse-2-3" aria-expanded="false" aria-controls="collapse-2-3">
                                    <h6 class="mb-0"><i class="far fa-credit-card mr-3"></i>{{ __('Paypal') }}</h6>
                                </div>
                                <div id="collapse-2-3" class="collapse" aria-labelledby="heading-2-3" data-parent="#accordion-2">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6 py-2">
                                                <h5 class="h5">{{ __('Paypal') }}</h5>
                                            </div>
                                            <div class="col-6 py-2 text-right">
                                                <div class="custom-control custom-switch">
                                                    <input type="hidden" name="is_paypal_enabled" value="off">
                                                    <input type="checkbox" class="custom-control-input" name="is_paypal_enabled" id="is_paypal_enabled" {{ isset($payment['is_paypal_enabled']) && $payment['is_paypal_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                    <label class="custom-control-label form-control-label" for="is_paypal_enabled">{{ __('Enable Paypal') }}</label>
                                                </div>
                                            </div>
                                            <div class="col-md-12 pb-4">
                                                <label class="paypal-label form-control-label" for="paypal_mode">{{__('Paypal Mode')}}</label> <br>
                                                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                    <label class="btn btn-primary btn-sm {{ !isset($payment['paypal_mode']) || $payment['paypal_mode'] == '' || $payment['paypal_mode'] == 'sandbox' ? 'active' : '' }}">
                                                        <input type="radio" name="paypal_mode" value="sandbox" {{ !isset($payment['paypal_mode']) || $payment['paypal_mode'] == '' || $payment['paypal_mode'] == 'sandbox' ? 'checked="checked"' : '' }}>{{__('Sandbox')}}
                                                    </label>
                                                    <label class="btn btn-primary btn-sm {{ isset($payment['paypal_mode']) && $payment['paypal_mode'] == 'live' ? 'active' : '' }}">
                                                        <input type="radio" name="paypal_mode" value="live" {{ isset($payment['paypal_mode']) && $payment['paypal_mode'] == 'live' ? 'checked="checked"' : '' }}>{{__('Live')}}
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="paypal_client_id">{{ __('Client ID') }}</label>
                                                    <input type="text" name="paypal_client_id" id="paypal_client_id" class="form-control" value="{{(!isset($payment['paypal_client_id']) || is_null($payment['paypal_client_id'])) ? '' : $payment['paypal_client_id']}}" placeholder="{{ __('Client ID') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="paypal_secret_key">{{ __('Secret Key') }}</label>
                                                    <input type="text" name="paypal_secret_key" id="paypal_secret_key" class="form-control" value="{{(!isset($payment['paypal_secret_key']) || is_null($payment['paypal_secret_key'])) ? '' : $payment['paypal_secret_key']}}" placeholder="{{ __('Secret Key') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Paystack -->
                            <div class="card">
                                <div class="card-header py-4" id="heading-2-6" data-toggle="collapse" role="button" data-target="#collapse-2-6" aria-expanded="false" aria-controls="collapse-2-6">
                                    <h6 class="mb-0"><i class="far fa-credit-card mr-3"></i>{{ __('Paystack') }}</h6>
                                </div>
                                <div id="collapse-2-6" class="collapse" aria-labelledby="heading-2-6" data-parent="#accordion-2">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6 py-2">
                                                <h5 class="h5">{{ __('Paystack') }}</h5>

                                            </div>
                                            <div class="col-6 py-2 text-right">
                                                <div class="custom-control custom-switch">
                                                    <input type="hidden" name="is_paystack_enabled" value="off">
                                                    <input type="checkbox" class="custom-control-input" name="is_paystack_enabled" id="is_paystack_enabled" {{ isset($payment['is_paystack_enabled']) && $payment['is_paystack_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                    <label class="custom-control-label form-control-label" for="is_paystack_enabled">{{ __('Enable Paystack') }} </label>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="paypal_client_id">{{ __('Public Key')}}</label>
                                                    <input type="text" name="paystack_public_key" id="paystack_public_key" class="form-control" value="{{(!isset($payment['paystack_public_key']) || is_null($payment['paystack_public_key'])) ? '' : $payment['paystack_public_key']}}" placeholder="{{ __('Public Key')}}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="paystack_secret_key">{{ __('Secret Key') }}</label>
                                                    <input type="text" name="paystack_secret_key" id="paystack_secret_key" class="form-control" value="{{(!isset($payment['paystack_secret_key']) || is_null($payment['paystack_secret_key'])) ? '' : $payment['paystack_secret_key']}}" placeholder="{{ __('Secret Key') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- FLUTTERWAVE -->
                            <div class="card">
                                <div class="card-header py-4" id="heading-2-7" data-toggle="collapse" role="button" data-target="#collapse-2-7" aria-expanded="false" aria-controls="collapse-2-7">
                                    <h6 class="mb-0"><i class="far fa-credit-card mr-3"></i>{{ __('Flutterwave') }}</h6>
                                </div>
                                <div id="collapse-2-7" class="collapse" aria-labelledby="heading-2-7" data-parent="#accordion-2">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6 py-2">
                                                <h5 class="h5">{{ __('Flutterwave') }}</h5>

                                            </div>
                                            <div class="col-6 py-2 text-right">
                                                <div class="custom-control custom-switch">
                                                    <input type="hidden" name="is_flutterwave_enabled" value="off">
                                                    <input type="checkbox" class="custom-control-input" name="is_flutterwave_enabled" id="is_flutterwave_enabled" {{ isset($payment['is_flutterwave_enabled']) && $payment['is_flutterwave_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                    <label class="custom-control-label form-control-label" for="is_flutterwave_enabled">{{ __('Enable Flutterwave') }}</label>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="paypal_client_id">{{ __('Public Key')}}</label>
                                                    <input type="text" name="flutterwave_public_key" id="flutterwave_public_key" class="form-control" value="{{(!isset($payment['flutterwave_public_key']) || is_null($payment['flutterwave_public_key'])) ? '' : $payment['flutterwave_public_key']}}" placeholder="Public Key">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="paystack_secret_key">{{ __('Secret Key') }}</label>
                                                    <input type="text" name="flutterwave_secret_key" id="flutterwave_secret_key" class="form-control" value="{{(!isset($payment['flutterwave_secret_key']) || is_null($payment['flutterwave_secret_key'])) ? '' : $payment['flutterwave_secret_key']}}" placeholder="Secret Key">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Razorpay -->
                            <div class="card">
                                <div class="card-header py-4" id="heading-2-8" data-toggle="collapse" role="button" data-target="#collapse-2-8" aria-expanded="false" aria-controls="collapse-2-8">
                                    <h6 class="mb-0"><i class="far fa-credit-card mr-3"></i>{{ __('Razorpay') }}</h6>
                                </div>
                                <div id="collapse-2-8" class="collapse" aria-labelledby="heading-2-7" data-parent="#accordion-2">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6 py-2">
                                                <h5 class="h5">{{ __('Razorpay') }}</h5>

                                            </div>
                                            <div class="col-6 py-2 text-right">
                                                <div class="custom-control custom-switch">
                                                    <input type="hidden" name="is_razorpay_enabled" value="off">
                                                    <input type="checkbox" class="custom-control-input" name="is_razorpay_enabled" id="is_razorpay_enabled" {{ isset($payment['is_razorpay_enabled']) && $payment['is_razorpay_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                    <label class="custom-control-label form-control-label" for="is_razorpay_enabled">Enable Razorpay</label>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="paypal_client_id">Public Key</label>

                                                    <input type="text" name="razorpay_public_key" id="razorpay_public_key" class="form-control" value="{{(!isset($payment['razorpay_public_key']) || is_null($payment['razorpay_public_key'])) ? '' : $payment['razorpay_public_key']}}" placeholder="Public Key">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="paystack_secret_key">Secret Key</label>
                                                    <input type="text" name="razorpay_secret_key" id="razorpay_secret_key" class="form-control" value="{{(!isset($payment['razorpay_secret_key']) || is_null($payment['razorpay_secret_key'])) ? '' : $payment['razorpay_secret_key']}}" placeholder="Secret Key">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Paytm -->
                            <div class="card">
                                <div class="card-header py-4" id="heading-2-14" data-toggle="collapse" role="button" data-target="#collapse-2-14" aria-expanded="false" aria-controls="collapse-2-14">
                                    <h6 class="mb-0"><i class="far fa-credit-card mr-3"></i>{{ __('Paytm') }}</h6>
                                </div>
                                <div id="collapse-2-14" class="collapse" aria-labelledby="heading-2-14" data-parent="#accordion-2">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6 py-2">
                                                <h5 class="h5">{{ __('Paytm') }}</h5>

                                            </div>
                                            <div class="col-6 py-2 text-right">
                                                <div class="custom-control custom-switch">
                                                    <input type="hidden" name="is_paytm_enabled" value="off">
                                                    <input type="checkbox" class="custom-control-input" name="is_paytm_enabled" id="is_paytm_enabled" {{ isset($payment['is_paytm_enabled']) && $payment['is_paytm_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                    <label class="custom-control-label form-control-label" for="is_paytm_enabled">Enable Paytm</label>
                                                </div>
                                            </div>
                                            <div class="col-md-12 pb-4">
                                                <label class="paypal-label form-control-label" for="paypal_mode">Paytm Environment</label> <br>
                                                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                    <label class="btn btn-primary btn-sm {{ !isset($payment['paytm_mode']) || $payment['paytm_mode'] == '' || $payment['paytm_mode'] == 'local' ? 'active' : '' }}">
                                                        <input type="radio" name="paytm_mode" value="local" {{ !isset($payment['paytm_mode']) || $payment['paytm_mode'] == '' || $payment['paytm_mode'] == 'local' ? 'checked="checked"' : '' }}>Local
                                                    </label>
                                                    <label class="btn btn-primary btn-sm {{ isset($payment['paytm_mode']) && $payment['paytm_mode'] == 'production' ? 'active' : '' }}">
                                                        <input type="radio" name="paytm_mode" value="production" {{ isset($payment['paytm_mode']) && $payment['paytm_mode'] == 'production' ? 'checked="checked"' : '' }}>Production
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="paytm_public_key">Merchant ID</label>
                                                    <input type="text" name="paytm_merchant_id" id="paytm_merchant_id" class="form-control" value="{{(!isset($payment['paytm_merchant_id']) || is_null($payment['paytm_merchant_id'])) ? '' : $payment['paytm_merchant_id']}}" placeholder="Merchant ID">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="paytm_secret_key">Merchant Key</label>
                                                    <input type="text" name="paytm_merchant_key" id="paytm_merchant_key" class="form-control" value="{{(!isset($payment['paytm_merchant_key']) || is_null($payment['paytm_merchant_key'])) ? '' : $payment['paytm_merchant_key']}}" placeholder="Merchant Key">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="paytm_industry_type">Industry Type</label>
                                                    <input type="text" name="paytm_industry_type" id="paytm_industry_type" class="form-control" value="{{(!isset($payment['paytm_industry_type']) || is_null($payment['paytm_industry_type'])) ? '' : $payment['paytm_industry_type']}}" placeholder="Industry Type">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Mercado Pago-->
                            <div class="card">
                                <div class="card-header py-4" id="heading-2-12" data-toggle="collapse" role="button" data-target="#collapse-2-12" aria-expanded="false" aria-controls="collapse-2-12">
                                    <h6 class="mb-0"><i class="far fa-credit-card mr-3"></i>{{ __('Mercado Pago') }}</h6>
                                </div>
                                <div id="collapse-2-12" class="collapse" aria-labelledby="heading-2-12" data-parent="#accordion-2">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6 py-2">
                                                <h5 class="h5">{{ __('Mercado Pago') }}</h5>
                                            </div>
                                            <div class="col-6 py-2 text-right">
                                                <div class="custom-control custom-switch">
                                                    <input type="hidden" name="is_mercado_enabled" value="off">
                                                    <input type="checkbox" class="custom-control-input" name="is_mercado_enabled" id="is_mercado_enabled" {{ isset($payment['is_mercado_enabled']) && $payment['is_mercado_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                    <label class="custom-control-label form-control-label" for="is_mercado_enabled">Enable Mercado Pago</label>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="mercado_app_id">App ID</label>
                                                    <input type="text" name="mercado_app_id" id="mercado_app_id" class="form-control" value="{{(!isset($payment['mercado_app_id']) || is_null($payment['mercado_app_id'])) ? '' : $payment['mercado_app_id']}}" placeholder="App ID">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="mercado_secret_key">App Secret KEY</label>
                                                    <input type="text" name="mercado_secret_key" id="mercado_secret_key" class="form-control" value="{{(!isset($payment['mercado_secret_key']) || is_null($payment['mercado_secret_key'])) ? '' : $payment['mercado_secret_key']}}" placeholder="App Secret Key">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Mollie -->
                            <div class="card">
                                <div class="card-header py-4" id="heading-2-8" data-toggle="collapse" role="button" data-target="#collapse-2-10" aria-expanded="false" aria-controls="collapse-2-10">
                                    <h6 class="mb-0"><i class="far fa-credit-card mr-3"></i>{{ __('Mollie') }}</h6>
                                </div>
                                <div id="collapse-2-10" class="collapse" aria-labelledby="heading-2-7" data-parent="#accordion-2">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6 py-2">
                                                <h5 class="h5">{{ __('Mollie') }}</h5>

                                            </div>
                                            <div class="col-6 py-2 text-right">
                                                <div class="custom-control custom-switch">
                                                    <input type="hidden" name="is_mollie_enabled" value="off">
                                                    <input type="checkbox" class="custom-control-input" name="is_mollie_enabled" id="is_mollie_enabled" {{ isset($payment['is_mollie_enabled']) && $payment['is_mollie_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                    <label class="custom-control-label form-control-label" for="is_mollie_enabled">Enable Mollie</label>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="mollie_api_key">Mollie Api Key</label>
                                                    <input type="text" name="mollie_api_key" id="mollie_api_key" class="form-control" value="{{(!isset($payment['mollie_api_key']) || is_null($payment['mollie_api_key'])) ? '' : $payment['mollie_api_key']}}" placeholder="Mollie Api Key">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="mollie_profile_id">Mollie Profile Id</label>
                                                    <input type="text" name="mollie_profile_id" id="mollie_profile_id" class="form-control" value="{{(!isset($payment['mollie_profile_id']) || is_null($payment['mollie_profile_id'])) ? '' : $payment['mollie_profile_id']}}" placeholder="Mollie Profile Id">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="mollie_partner_id">Mollie Partner Id</label>
                                                    <input type="text" name="mollie_partner_id" id="mollie_partner_id" class="form-control" value="{{(!isset($payment['mollie_partner_id']) || is_null($payment['mollie_partner_id'])) ? '' : $payment['mollie_partner_id']}}" placeholder="Mollie Partner Id">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Skrill -->
                            <div class="card">
                                <div class="card-header py-4" id="heading-2-8" data-toggle="collapse" role="button" data-target="#collapse-2-13" aria-expanded="false" aria-controls="collapse-2-10">
                                    <h6 class="mb-0"><i class="far fa-credit-card mr-3"></i>{{ __('Skrill') }}</h6>
                                </div>
                                <div id="collapse-2-13" class="collapse" aria-labelledby="heading-2-7" data-parent="#accordion-2">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6 py-2">
                                                <h5 class="h5">{{ __('Skrill') }}</h5>

                                            </div>
                                            <div class="col-6 py-2 text-right">
                                                <div class="custom-control custom-switch">
                                                    <input type="hidden" name="is_skrill_enabled" value="off">
                                                    <input type="checkbox" class="custom-control-input" name="is_skrill_enabled" id="is_skrill_enabled" {{ isset($payment['is_skrill_enabled']) && $payment['is_skrill_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                    <label class="custom-control-label form-control-label" for="is_skrill_enabled">Enable Skrill</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="mollie_api_key">Skrill Email</label>
                                                    <input type="text" name="skrill_email" id="skrill_email" class="form-control" value="{{(!isset($payment['skrill_email']) || is_null($payment['skrill_email'])) ? '' : $payment['skrill_email']}}" placeholder="Enter Skrill Email">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- CoinGate -->
                            <div class="card">
                                <div class="card-header py-4 collapsed" id="heading-2-8" data-toggle="collapse" role="button" data-target="#collapse-2-15" aria-expanded="false" aria-controls="collapse-2-10">
                                    <h6 class="mb-0"><i class="far fa-credit-card mr-3"></i>{{ __('CoinGate') }}</h6>
                                </div>
                                <div id="collapse-2-15" class="collapse" aria-labelledby="heading-2-7" data-parent="#accordion-2" style="">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6 py-2">
                                                <h5 class="h5">{{ __('CoinGate') }}</h5>
                                            </div>
                                            <div class="col-6 py-2 text-right">
                                                <div class="custom-control custom-switch">
                                                    <input type="hidden" name="is_coingate_enabled" value="off">
                                                    <input type="checkbox" class="custom-control-input" name="is_coingate_enabled" id="is_coingate_enabled" {{ isset($payment['is_coingate_enabled']) && $payment['is_coingate_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                    <label class="custom-control-label form-control-label" for="is_coingate_enabled">Enable CoinGate</label>
                                                </div>
                                            </div>
                                            <div class="col-md-12 pb-4">
                                                <label class="coingate-label form-control-label" for="coingate_mode">CoinGate Mode</label> <br>
                                                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                    <label class="btn btn-primary btn-sm {{ !isset($payment['coingate_mode']) || $payment['coingate_mode'] == '' || $payment['coingate_mode'] == 'sandbox' ? 'active' : '' }}">
                                                        <input type="radio" name="coingate_mode" value="sandbox" {{ !isset($payment['coingate_mode']) || $payment['coingate_mode'] == '' || $payment['coingate_mode'] == 'sandbox' ? 'checked="checked"' : '' }}>Sandbox
                                                    </label>
                                                    <label class="btn btn-primary btn-sm {{ isset($payment['coingate_mode']) && $payment['coingate_mode'] == 'live' ? 'active' : '' }}">
                                                        <input type="radio" name="coingate_mode" value="live" {{ isset($payment['coingate_mode']) && $payment['coingate_mode'] == 'live' ? 'checked="checked"' : '' }}>Live
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="coingate_auth_token">CoinGate Auth Token</label>
                                                    <input type="text" name="coingate_auth_token" id="coingate_auth_token" class="form-control" value="{{(!isset($payment['coingate_auth_token']) || is_null($payment['coingate_auth_token'])) ? '' : $payment['coingate_auth_token']}}" placeholder="CoinGate Auth Token">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer text-right">
                            <div class="form-group">
                                <input class="btn-create badge-blue" type="submit" value="{{__('Save Changes')}}">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="tab-pane fade" id="invoice-setting" role="tabpanel" aria-labelledby="profile-tab6">
                    <div class="card bg-none">
                        <div class="row company-setting">
                            <div class="col-md-2">
                                <form id="setting-form" method="post" action="{{route('template.setting')}}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group">
                                        <label for="invoice_template" class="form-control-label">{{__('Invoice Template')}}</label>
                                        <select class="form-control select2" name="invoice_template" id="invoice_template">
                                            @foreach(Utility::templateData()['templates'] as $key => $template)
                                                <option value="{{$key}}" {{(isset($settings['invoice_template']) && $settings['invoice_template'] == $key) ? 'selected' : ''}}>{{$template}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-control-label">{{__('Color Input')}}</label>
                                        <div class="row gutters-xs">
                                            @foreach(Utility::templateData()['colors'] as $key => $color)
                                                <div class="col-auto">
                                                    <label class="colorinput">
                                                        <input name="invoice_color" type="radio" value="{{$color}}" class="colorinput-input" {{(isset($settings['invoice_color']) && $settings['invoice_color'] == $color) ? 'checked' : ''}}>
                                                        <span class="colorinput-color" style="background: #{{$color}}"></span>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-control-label">{{__('Invoice Logo')}}</label>
                                        <div class="choose-file form-group">
                                            <label for="invoice_logo" class="form-control-label">
                                                <div>{{__('Choose file here')}}</div>
                                                <input type="file" class="form-control" name="invoice_logo" id="invoice_logo" data-filename="invoice_logo_update" accept=".jpeg,.jpg,.png,.doc,.pdf">
                                            </label><br>
                                            <p class="invoice_logo_update"></p>
                                        </div>
                                    </div>
                                    <div class="form-group mt-2">
                                        <input type="submit" value="{{__('Save')}}" class="btn-create badge-blue">
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-10">
                                @if(isset($settings['invoice_template']) && isset($settings['invoice_color']))
                                    <iframe id="invoice_frame" class="w-100 h-1050" frameborder="0" src="{{route('invoice.preview',[$settings['invoice_template'],$settings['invoice_color']])}}"></iframe>
                                @else
                                    <iframe id="invoice_frame" class="w-100 h-1050" frameborder="0" src="{{route('invoice.preview',['template1','ffffff'])}}"></iframe>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="estimation-setting" role="tabpanel" aria-labelledby="profile-tab7">
                    <div class="card bg-none">
                        <div class="row company-setting">
                            <div class="col-md-2">
                                <form id="setting-form" method="post" action="{{route('template.setting')}}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group">
                                        <label for="estimation_template" class="form-control-label">{{__('Estimation Template')}}</label>
                                        <select class="form-control select2" name="estimation_template" id="estimation_template">
                                            @foreach(Utility::templateData()['templates'] as $key => $template)
                                                <option value="{{$key}}" {{(isset($settings['estimation_template']) && $settings['estimation_template'] == $key) ? 'selected' : ''}}>{{$template}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-control-label">{{__('Color Input')}}</label>
                                        <div class="row gutters-xs">
                                            @foreach(Utility::templateData()['colors'] as $key => $color)
                                                <div class="col-auto">
                                                    <label class="colorinput">
                                                        <input name="estimation_color" type="radio" value="{{$color}}" class="colorinput-input" {{(isset($settings['estimation_color']) && $settings['estimation_color'] == $color) ? 'checked' : ''}}>
                                                        <span class="colorinput-color" style="background: #{{$color}}"></span>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="estimation_logo" class="form-control-label">{{__('Estimation Logo')}}</label>
                                        <div class="choose-file form-group">
                                            <label for="estimation_logo" class="form-control-label">
                                                <div>{{__('Choose file here')}}</div>
                                                <input type="file" class="form-control" name="estimation_logo" id="estimation_logo" data-filename="estimation_logo_update" accept=".jpeg,.jpg,.png,.doc,.pdf">
                                            </label><br>
                                            <p class="estimation_logo_update"></p>
                                        </div>
                                    </div>
                                    <div class="form-group mt-2">
                                        <input type="submit" value="{{__('Save')}}" class="btn-create badge-blue">
                                    </div>
                                </form>
                            </div>
                            
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="mdf-setting" role="tabpanel" aria-labelledby="profile-tab8">
                    <div class="card bg-none">
                        <div class="row company-setting">
                            <div class="col-md-2">
                                <form id="setting-form" method="post" action="{{route('template.setting')}}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group">
                                        <label for="mdf_template" class="form-control-label">{{__('MDF Template')}}</label>
                                        <select class="form-control select2" name="mdf_template" id="mdf_template">
                                            @foreach(Utility::templateData()['templates'] as $key => $template)
                                                <option value="{{$key}}" {{(isset($settings['mdf_template']) && $settings['mdf_template'] == $key) ? 'selected' : ''}}>{{$template}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-control-label">{{__('Color Input')}}</label>
                                        <div class="row gutters-xs">
                                            @foreach(Utility::templateData()['colors'] as $key => $color)
                                                <div class="col-auto">
                                                    <label class="colorinput">
                                                        <input name="mdf_color" type="radio" value="{{$color}}" class="colorinput-input" {{(isset($settings['mdf_color']) && $settings['mdf_color'] == $color) ? 'checked' : ''}}>
                                                        <span class="colorinput-color" style="background: #{{$color}}"></span>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="mdf_logo" class="form-control-label">{{__('MDF Logo')}}</label>
                                        <div class="choose-file form-group">
                                            <label for="mdf_logo" class="form-control-label">
                                                <div>{{__('Choose file here')}}</div>
                                                <input type="file" class="form-control" name="mdf_logo" id="mdf_logo" data-filename="mdf_logo_update" accept=".jpeg,.jpg,.png,.doc,.pdf">
                                            </label><br>
                                            <p class="mdf_logo_update"></p>
                                        </div>
                                    </div>
                                    <div class="form-group mt-2">
                                        <input type="submit" value="{{__('Save')}}" class="btn-create badge-blue">
                                    </div>
                                </form>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- DELETE MODAL --}}
    <div id="delete-ip-modal" class="modal fade">
        <div class="modal-dialog">
            <form id="delete-ip-form"  method="POST" action="{{ route('delete.ip.client') }}">
                @method("delete")
                @csrf
                <input type="hidden" name="id" value="">
                {{-- <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">@lang('Delete log file')</h4>
                    </div>
                    <div class="modal-body">
                        <p class="text-center"></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-default " data-dismiss="modal">@lang('Cancel')</button>
                        <button type="submit" class="btn btn-sm btn-danger" data-loading-text="@lang('Loading')&hellip;">@lang('Delete')</button>
                    </div>
                </div> --}}
                <div class="modal-content ">
                    <div class="modal-header">
                      <h5 class="modal-title text-center">Delete Ip Client</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <div class="modal-body">
                      <p class="pl-5"></p>
                    </div>
                    <div class="modal-footer pl-5">
                      <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                      <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
                    </div>
                  </div>
            </form>
        </div>
    </div>
    <script>
        function deleteIp(id, ip){
            var deleteIpModal = $('div#delete-ip-modal'),
                deleteIpForm  = $('form#delete-ip-form'),
                submitBtn      = deleteIpForm.find('button[type=submit]');
                // var date    = $(this).data('ip-id'),
                message = "@lang('Are you sure you want to DELETE this IP Client: :ip ?')";
        
                deleteIpForm.find('input[name=id]').val(id);
                deleteIpModal.find('.modal-body p').html(message.replace(':ip', ip));
        
                deleteIpModal.modal('show');
        }
        // $(function () {
        //     console.log("test");


        //     $("a[href=#delete-ip-modal]").on('click', function(event) {
        //         event.preventDefault();
        //     });
        // })
    </script>
@endsection
