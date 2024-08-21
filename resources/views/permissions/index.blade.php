@extends('layouts.admin')

@section('title')
    {{__('Manage Permissions')}}
@endsection

@push('head')
    <link rel="stylesheet" href="{{asset('assets/modules/datatables/datatables.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/modules/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css')}}">
@endpush

@push('script')
    <script src="{{asset('assets/modules/datatables/datatables.min.js')}}"></script>
    <script src="{{asset('assets/modules/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{asset('assets/modules/jquery-ui/jquery-ui.min.js')}}"></script>
@endpush

@section('action-button')
    @can('Create Permission')
        <a href="#" data-url="{{ route('permissions.create') }}" data-ajax-popup="true" data-title="{{__('Create Permission')}}" class="btn btn-primary btn-sm"><i class="fas fa-plus-circle"></i> {{__('Create')}} </a>
    @endcan
@endsection

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0" id="dataTable">
                            <thead>
                            <tr>
                                <th>{{__('Permissions')}}</th>
                                <th class="text-right" width="200px">{{__('Action')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach ($permissions as $permission)
                                    <tr>
                                        <td>{{ $permission->name }}</td>
                                        <td class="text-right">
                                            @can('Edit Permission')
                                                <a href="#" data-url="{{ URL::to('permissions/'.$permission->id.'/edit') }}" data-ajax-popup="true" data-title="{{__('Edit Permission')}}" class="btn btn-outline-primary btn-sm mr-1"><i class="fas fa-pencil-alt"></i> <span>{{__('Edit')}}</span></a>
                                            @endcan
                                            @can('Delete Permission')
                                                <a href="#" class="btn btn-outline-danger btn-sm"  data-confirm="Are You Sure?|This action can not be undone. Do you want to continue?" data-confirm-yes="document.getElementById('delete-form-{{$permission->id}}').submit();"><i class="fas fa-trash"></i> <span>{{__('Delete')}}</span></a>
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['permissions.destroy', $permission->id],'id'=>'delete-form-'.$permission->id]) !!}
                                                {!! Form::close() !!}
                                            @endif
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
