@extends('layouts.admin')

@section('title')
    {{ __('Users Activity') }}
@endsection

@section('content')
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
                                        <th>{{__('Last IP Address Login')}}</th>
                                        <th>{{__('Action')}}</th>
                                        <th>{{__('Action Date')}}</th>
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
                                        <td>{{ $user->ip }}</td>
                                        <td>{{ $user->action }}</td>
                                        <td>{{ $user->action_time }}</td>
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
