@extends('layouts.admin')

@section('title')
    {{__('Manage Custom Fields')}}
@endsection

@section('action-button')
    <div class="all-button-box row d-flex justify-content-end">
        @can('Create Custom Field')
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a href="#" data-url="{{ route('custom_fields.create') }}" data-ajax-popup="true" data-title="{{__('Create Custom Field')}}" class="btn btn-xs btn-white btn-icon-only width-auto"><i class="fas fa-plus"></i> {{__('Create')}} </a>
            </div>
        @endcan
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
                                <th>{{__('Custom Field')}}</th>
                                <th>{{__('Type')}}</th>
                                <th>{{__('Module')}}</th>
                                <th width="250px">{{__('Action')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($custom_fields as $custom_field)
                                <tr>
                                    <td>{{ $custom_field->name }}</td>
                                    <td>{{ ucfirst($custom_field->type) }}</td>
                                    <td>{{ ucfirst($custom_field->module) }}</td>
                                    <td class="Action">
                                        <span>
                                        @can('Edit Custom Field')
                                                <a href="#" data-url="{{ URL::to('custom_fields/'.$custom_field->id.'/edit') }}" data-ajax-popup="true" data-title="{{__('Edit Custom Field')}}" class="edit-icon"><i class="fas fa-pencil-alt"></i></a>
                                            @endcan
                                            @can('Delete Custom Field')
                                                <a href="#" class="delete-icon"  data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$custom_field->id}}').submit();"><i class="fas fa-trash"></i></a>
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['custom_fields.destroy', $custom_field->id],'id'=>'delete-form-'.$custom_field->id]) !!}
                                                {!! Form::close() !!}
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
