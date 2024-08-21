@extends('layouts.admin')

@section('title')
     {{ $user->name.__("'s Detail") }}
@endsection

@push('head')
    <link rel="stylesheet" href="{{asset('assets/libs/summernote/summernote-bs4.css')}}">
@endpush

@push('script')
    <script src="{{asset('assets/libs/summernote/summernote-bs4.js')}}"></script>
@endpush

@section('content')
    <div class="row">
        <div class="col-xl-3 col-lg-4 col-md-4 col-sm-12">
            <div class="card profile-card">
                <div class="icon-user avatar rounded-circle">
                    <img @if($user->avatar) src="{{asset('/storage/avatars/'.$user->avatar)}}" @else src="{{asset('assets/img/avatar/avatar-1.png')}}" @endif>
                </div>
                {{-- <h4 class="h4 mb-0 mt-2">{{ $user->fname }} {{ $user->mid_name }} {{ $user->lname }}</h4> --}}
                <h4 class="h4 mb-0 mt-2">{{ $user->name }}</h4>
                <div class="sal-right-card">
                    <span class="badge badge-pill badge-blue">{{ $user->type }}</span>
                </div>
                <h6 class="office-time mb-0 mt-4">{{ $user->email }}</h6>
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
                                                <input class="form-control @error('fname') is-invalid @enderror" name="name" type="text" id="fname" placeholder="{{ __('Enter Your First Name') }}" value="{{ $user->name }}" required autocomplete="fname">

                                                <input type="hidden" name="user_ai_id" value="{{ $user->id }}">
                                                @error('fname')
                                                <span class="invalid-feedback text-danger text-xs" role="alert">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- <div class="col-lg-6 col-sm-6">
                                            <div class="form-group">
                                                <label for="mid_name" class="form-control-label text-dark">{{__('Middle Name')}}</label>
                                                <input class="form-control @error('mid_name') is-invalid @enderror" name="mid_name" type="text" id="mid_name" placeholder="{{ __('Enter Your Middle Name') }}" value="{{ $user->mid_name }}" autocomplete="mid_name">

                                                @error('mid_name')
                                                <span class="invalid-feedback text-danger text-xs" role="alert">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div> --}}
                                        <div class="col-lg-6 col-sm-6">
                                            <div class="form-group">
                                                <label for="lname" class="form-control-label text-dark">{{__('User Name')}}</label>
                                                <input class="form-control @error('lname') is-invalid @enderror" name="lname" type="text" id="lname" placeholder="{{ __('Enter Your Last Name') }}" value="{{ $user->user_name }}" required autocomplete="lname">

                                                @error('lname')
                                                <span class="invalid-feedback text-danger text-xs" role="alert">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-6">
                                            <div class="form-group">
                                                <label for="email" class="form-control-label text-dark">{{__('Email')}}</label>
                                                <input class="form-control @error('email') is-invalid @enderror" name="email" type="text" id="email" placeholder="{{ __('Enter Your Email Address') }}" value="{{ $user->email }}" required autocomplete="email">
                                                @error('email')
                                                <span class="invalid-feedback text-danger text-xs" role="alert">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- <div class="col-lg-6 col-md-6">
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
                                        </div> --}}
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
                                <form method="post" action="{{route('update.userpassword')}}">
                                    @csrf
                                    <input class="form-control" name="id" type="hidden" id="id" required autocomplete="old_password" value="{{ $user->id }}">
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
                                        <input type="hidden" name="user_tbl_id" value="{{ $user->id }}">
                                        <input type="hidden" name="user_type" value="{{ $user->type }}">
                                        <input type="hidden" name="user_email" value="{{ $user->email }}">

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



        <div class="card profile-card" style="display: block; color: #000;">
            <div style="font-weight: bold; font-size: 22px; padding-left: 5px;    padding-left: 17px; margin-bottom: 20px;">User operator details</div>
                @if(isset($OperaterServices) && !empty($OperaterServices))
                @foreach ($OperaterServices as $OperaterService)

                    @foreach ($OperaterService as $key=> $Service)
                        @if ($key==0)
                        <div class="row">
                        <div class="col-md-12 custom-control custom-checkbox" style="margin-bottom: 20px; font-size: 20px; font-weight: bold;">{{$Service->operator->operator_name}}</div>
                        </div>
                        <div class="row">
                        @endif

                        <div class="col-md-4 custom-control custom-checkbox" style="margin-bottom: 20px;">{{$Service->service->service_name}}</div>

                    @endforeach
                    </div>
                @endforeach


            @endif
            <div><a href="{{route('users.show.operator',[$user->id])}}">Edit operator</a></div>
        </div>


{{--         <div class="col-12">
            <h4 class="h4 font-weight-400 float-left">{{__('Deals')}}</h4>
        </div>
        <div class="col">
            <div class="card p-4 mb-4">
                <h5 class="report-text gray-text mb-0">{{__('Total Deals')}}</h5>
                <h5 class="report-text mb-0">{{ $cnt_deal['total'] }} / {{$cnt_deal['cnt_total']}}</h5>
            </div>
        </div>
        <div class="col">
            <div class="card p-4 mb-4">
                <h5 class="report-text gray-text mb-0">{{__('This Month Total Deals')}}</h5>
                <h5 class="report-text mb-0">{{ $cnt_deal['this_month'] }} / {{$cnt_deal['cnt_this_month']}}</h5>
            </div>
        </div>
        <div class="col">
            <div class="card p-4 mb-4">
                <h5 class="report-text gray-text mb-0">{{__('This Week Total Deals')}}</h5>
                <h5 class="report-text mb-0">{{ $cnt_deal['this_week'] }} / {{$cnt_deal['cnt_this_week']}}</h5>
            </div>
        </div>
        <div class="col">
            <div class="card p-4 mb-4">
                <h5 class="report-text gray-text mb-0">{{__('Last 30 Days Total Deals')}}</h5>
                <h5 class="report-text mb-0">{{ $cnt_deal['last_30days'] }} / {{$cnt_deal['cnt_last_30days']}}</h5>
            </div>
        </div> --}}

        {{-- <div class="col-md-12">
            <div class="card bg-none">
                <div class="table-responsive">
                    <table class="table table-striped dataTable">
                        <thead>
                        <tr>
                            <th>{{__('Key Reasult Areas ')}}</th>
                            <th>{{__('job Objective (s)')}}</th>
                            <th>{{__('Key Performance Indicators (KPIs)')}}</th>
                            <th>{{__('Targets')}}</th>
                        </tr>
                        </thead>

                    </table>
                </div>
            </div>
        </div> --}}


@endsection
