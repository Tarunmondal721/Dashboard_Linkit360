@extends('layouts.admin')

@section('title')
    {{ __('Manage Users') }}
@endsection

@section('content')
    <div class="page-title" style="margin-bottom:25px">
        <div class="row justify-content-between align-items-center">
            <div class="col-xl-4 col-lg-4 col-md-4 d-flex align-items-center justify-content-between justify-content-md-start mb-3 mb-md-0">
                <div class="d-inline-block">
                    <p>Total Users : {{ count($users) }}</p>
                    <p>Active Users : {{ $activeUsers }}</p>
                    <p>Non Active Users : {{ count($users) - $activeUsers }}</p>
                </div>
            </div>
            <div class="col-xl-8 col-lg-8 col-md-8 d-flex align-items-center justify-content-between justify-content-md-end">
                @can('Create User')
                <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                    <a href="#" class="btn btn-xs btn-white btn-icon-only width-auto" data-ajax-popup="true" data-title="{{__('Create User')}}" data-url="{{route('users.create')}}"><i class="fas fa-plus"></i> {{__('Add')}}</a>
                </div>
                @endcan
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="operatorManagement">
                        <div class="table-responsive">
                            <table class="table table-striped dataTable cBorder">
                                <thead>
                                    <tr>
                                        <th>{{__('Name')}}</th>
                                        <th>{{__('User Name')}}</th>
                                        <th>{{__('Email')}}</th>
                                        <th>{{__('Role')}}</th>
                                        <th>{{__('Total Country')}}</th>
                                        <th>{{__('Last Login Time')}}</th>
                                        <th>{{__('Last IP Address Login')}}</th>
                                        <th>{{__('Last Handset Login')}}</th>
                                        <th>{{__('Password Aging')}}</th>
                                        <th>{{__('Status')}}</th>
                                        <th>{{__('Action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                    <tr>
                                        <td class="Role">@if(\Auth::user()->type != 'Super Admin')
                                            <a href="{{route('users.show',$user->id)}}">{{ $user->name }}</a>
                                            @else{{ $user->name }}
                                            @endif
                                            @if($user->delete_status == 1)<h5 class="office-time mb-0">{{__('Deleted')}}</h5>
                                            @endif</td>
                                        <td class="Role">{{ $user->user_name }}</td>
                                        <td class="Role">{{ $user->email }}</td>
                                        <td class="Role">{{ ucfirst($user->type) }}</td>
                                        <td class="Role">@can('Edit User')
                                            <a href="{{ route('users.show.operator',$user->id) }}" class=" text-sm" data-url="" data-ajax-popup="true" data-title="{{__('Edit User')}}">@endcan{{ count($user->countries) }}@can('Edit User')</a>
                                        @endcan</td>
                                        <td>{{ $user->last_login }}</td>
                                        <td>{{ $user->ip }}</td>
                                        <td>{{ $user->device }}</td>
                                        <td>{{ $user->password_age }}</td>
                                        <td>
                                            @if($user->is_active == 1)
                                            {{ 'Active' }}
                                            @else
                                            {{ 'Non Active' }}
                                            @endif
                                        </td>
                                        <td style="display: flex;">
                                            @if(\Auth::user()->type != 'Super Admin')
                                                <a href="{{route('users.show',$user->id)}}" class=" text-sm userButton btn btn-info  width-auto">{{__('View')}}</a>
                                            @endif
                                            @can('Edit User')
                                                <a href="#" class="text-sm userButton btn btn-primary  width-auto" data-url="{{ route('users.edit',$user->id) }}" data-ajax-popup="true" data-title="{{__('Edit User')}}">{{__('Edit')}}</a>
                                            @endcan
                                            @can('Edit User')
                                            @if($user->is_active == 0)
                                                <a class=" text-sm userButton btn btn-success  width-auto buttonW" data-confirm="{{__('Are you sure want to re active this user?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$user['id']}}').submit();">{{__('Active')}}</a>
                                                {!! Form::open(['method' => 'POST', 'route' => ['users.update.status', $user['id']],'id'=>'delete-form-'.$user['id']]) !!}
                                                {!! Form::close() !!}
                                            @else
                                                <a class=" text-sm userButton btn btn-warning  width-auto buttonW" data-confirm="{{__('Are you sure want to non-active this user?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$user['id']}}').submit();">{{__('Non Active')}}</a>
                                                {!! Form::open(['method' => 'POST', 'route' => ['users.update.status', $user['id']],'id'=>'delete-form-'.$user['id']]) !!}
                                                {!! Form::close() !!}
                                            @endif
                                            @endcan
                                            @can('Delete User')
                                                <a class=" text-sm userButton btn btn-danger  width-auto" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$user['id']}}').submit();">
                                                    @if($user->delete_status == 0){{__('Delete')}} @else {{__('Restore')}}@endif
                                                </a>
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['users.destroy', $user['id']],'id'=>'delete-form-'.$user['id']]) !!}
                                                {!! Form::close() !!}
                                            @endcan
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
