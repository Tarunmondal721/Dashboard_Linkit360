@extends('layouts.admin')

@section('title')
    {{ __('Manage Operator') }}
@endsection

@section('action-button')
    <div class="all-button-box row d-flex justify-content-end">
        @can('Create Role')
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                {{-- <a href="#" data-url="{{ route('roles.create') }}" data-ajax-popup="true" data-size="lg" data-title="{{__('Reports')}}" class="btn btn-xs btn-white btn-icon-only width-auto">
                    <i class="fas fa-plus"></i> {{__('Reports')}}
                </a> --}}
            </div>
    @endcan
    <!-- @can('Manage Permissions')
        <a href="{{ route('permissions.index') }}" class="btn btn-primary btn-sm"><i class="fas fa-lock"></i> {{__('Permissions')}} </a>
    @endcan -->
    </div>
@endsection
@section('content')
    @if(Session::has('flash_message_success'))
    <p class="alert {{ Session::get('alert-class', 'alert-success') }}">
        {{ Session::get('flash_message_success') }}
    </p>
    @endif
    @if(Session::has('flash_message_error'))
    <p class="alert {{ Session::get('alert-class', 'alert-danger') }}">
        {{ Session::get('flash_message_error') }}
    </p>
    @endif
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped dataTable">
                            <thead>
                            <tr>
                                
                                <th>{{__('Operator ID')}}</th>
                                <th>{{__('Operator Name')}}</th>
                                <th>{{__('Country Currency')}}</th>
                                <th>{{__('Country Name')}}</th>
                                <th>{{__('USD')}}</th>
                                <th>{{__('Operator Share')}}</th>
                                <th>{{__('Merchant Share')}}</th>
                                <th>{{__('Status')}}</th>
                                <th width="200px">{{__('Action')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($operators as $operator)
                                <tr>
                                    
                                    <td class="Role">{{ $operator->id_operator }}</td>
                                    <td class="Role">{{ $operator->operator_name }}</td>
                                    <td class="Role">{{$operator->country->currency_code}}</td>
                                    <td class="Role">{{ $operator->country_name }}</td>
                                    <td class="Role">{{$operator->country->usd}}</td>
                                    <td class="Role">{{ !empty($operator->revenueshare)?$operator->revenueshare->operator_revenue_share:''}}</td>
                                    <td class="Role">{{ !empty($operator->revenueshare)?$operator->revenueshare->merchant_revenue_share:'' }}</td>
                                    
                                    {{-- <td class="Permission">
                                        @foreach($role->permissions()->pluck('name') as $permission)
                                            <a href="#" class="absent-btn">{{$permission}}</a>
                                        @endforeach
                                    </td> --}}
                                    <td>
                                        <input class="status_btn" type="checkbox" @if ($operator->status == 1) checked  @endif data-operator_id={{$operator->id}}  data-toggle="toggle" data-on="Active" data-off="Inactive" data-onstyle="primary" data-offstyle="warning">
                                    </td>
                                    <td class="Action">

                                        

                                        <a href="javascript:void(0)" data-url="{{ URL::to('management/rev_share/'.$operator->id ) }}"  data-size="lg" data-ajax-popup="true" class="edit-icon bg-warning" data-title="Revenue Share #{{$operator->operator_name}}"  data-toggle="tooltip" data-original-title="Edit Revenue Share"><i class="fas fa-table"></i></a>
                                        <span>
                                        {{-- @can('Edit Operator')
                                                <a href="#" data-url="{{ URL::to('management/'.$operator->id.'/edit') }}" data-size="lg" data-ajax-popup="true" data-title="{{__('Edit Operator')}}" class="edit-icon"><i class="fas fa-pencil-alt"></i></a>
                                            @endcan
                                            @can('Delete Operator')
                                                <a href="#" class="delete-icon"  data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$operator->id}}').submit();"><i class="fas fa-trash"></i></a>
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['operator.destroy', $operator->id],'id'=>'delete-form-'.$operator->id]) !!}
                                                {!! Form::close() !!}
                                            @endcan --}}
                                        </span>
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
@endsection