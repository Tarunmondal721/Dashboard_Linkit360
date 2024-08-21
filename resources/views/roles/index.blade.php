@extends('layouts.admin')

@section('title')
    {{__('Manage Roles')}}
@endsection

@section('action-button')
    <div class="all-button-box row d-flex justify-content-end">
        @can('Create Role')
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a href="#" data-url="{{ route('roles.create') }}" data-ajax-popup="true" data-size="lg" data-title="{{__('Create Role')}}" class="btn btn-xs btn-white btn-icon-only width-auto">
                    <i class="fas fa-plus"></i> {{__('Create')}}
                </a>
            </div>
    @endcan
    <!-- @can('Manage Permissions')
        <a href="{{ route('permissions.index') }}" class="btn btn-primary btn-sm"><i class="fas fa-lock"></i> {{__('Permissions')}} </a>
    @endcan -->
    </div>

@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped dataTable">
                            <thead>
                            <tr>
                                <th>{{__('Role')}}</th>
                                <th>{{__('Permissions')}}</th>
                                <th width="200px">{{__('Action')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($roles as $role)
                                <tr>
                                    <td class="Role">{{ $role->name }}</td>
                                    <td class="Permission">
                                        @foreach($role->permissions()->pluck('name') as $permission)
                                            <a href="#" class="absent-btn">{{$permission}}</a>
                                        @endforeach
                                    </td>
                                    <td class="Action">
                                        <span>
                                        @can('Edit Role')
                                                <a href="#" data-url="{{ URL::to('roles/'.$role->id.'/edit') }}" data-size="lg" data-ajax-popup="true" data-title="{{__('Edit Role')}}" class="edit-icon"><i class="fas fa-pencil-alt"></i></a>
                                            @endcan
                                            @can('Delete Role')
                                                <a href="#" class="delete-icon"  data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$role->id}}').submit();"><i class="fas fa-trash"></i></a>
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['roles.destroy', $role->id],'id'=>'delete-form-'.$role->id]) !!}
                                                {!! Form::close() !!}
                                            @endcan    
                                             @can('Edit Role')
                                             {{-- {{ URL::to('management/company-operator/') }} --}}
                                                <a href="{{ route('roles.operator',$role->id) }}" class="edit-icon bg-warning" data-toggle="tooltip" data-original-title="{{__('edit/update Role operators')}}"><i class="fa fa-podcast"></i></a>
                                             
                                            @endif  
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
