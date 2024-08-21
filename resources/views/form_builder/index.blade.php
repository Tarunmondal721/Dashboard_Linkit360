@extends('layouts.admin')

@section('title')
    {{__('Manage Forms')}}
@endsection

@push('script')
    <script>
        $(document).ready(function () {
            $('.cp_link').on('click', function () {
                var value = $(this).attr('data-link');
                var $temp = $("<input>");
                $("body").append($temp);
                $temp.val(value).select();
                document.execCommand("copy");
                $temp.remove();
                show_toastr('Success', '{{__('Link Copy on Clipboard')}}', 'success')
            });
        });
    </script>
@endpush

@section('action-button')
    <div class="all-button-box row d-flex justify-content-end">
        <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
            <a href="#" data-url="{{ route('form_builder.create') }}" data-ajax-popup="true" data-title="{{__('Create Form')}}" class="btn btn-xs btn-white btn-icon-only width-auto"><i class="fas fa-plus"></i> {{__('Create')}} </a>
        </div>
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
                                <th width="50%">{{__('Name')}}</th>
                                <th width="25%">{{__('Response')}}</th>
                                <th>{{__('Action')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($forms as $form)
                                <tr>
                                    <td>{{ $form->name }}</td>
                                    <td>{{ $form->response->count() }}</td>
                                    <td class="Action">
                                        <span>
                                        <a href="#" class="edit-icon cp_link" data-link="{{url('/form/'.$form->code)}}" data-toggle="tooltip" data-original-title="{{__('Click to copy link')}}"><i class="fas fa-file"></i></a>
                                        <a href="{{route('form_builder.show',$form->id)}}" class="edit-icon bg-warning" data-toggle="tooltip" data-original-title="{{__('Edit/View Form field')}}"><i class="fas fa-table"></i></a>
                                        <a href="{{route('form.response',$form->id)}}" class="edit-icon bg-info" data-toggle="tooltip" data-original-title="{{__('View Response')}}"><i class="fas fa-eye"></i></a>
                                        <a href="#" data-url="{{ route('form.field.bind',$form->id) }}" data-ajax-popup="true" data-title="{{__('Convert into Lead Setting')}}" class="edit-icon bg-success" data-toggle="tooltip" data-original-title="{{__('Convert into Lead Setting')}}"><i class="fas fa-exchange-alt"></i></a>
                                        <a href="#" data-url="{{ URL::to('form_builder/'.$form->id.'/edit') }}" data-ajax-popup="true" data-title="{{__('Edit Form')}}" class="edit-icon"><i class="fas fa-pencil-alt"></i></a>
                                        <a href="#" class="delete-icon"  data-confirm="Are You Sure?|This action can not be undone, All field and response will be delete. Do you want to continue?" data-confirm-yes="document.getElementById('delete-form-{{$form->id}}').submit();"><i class="fas fa-trash"></i></a>
                                        {!! Form::open(['method' => 'DELETE', 'route' => ['form_builder.destroy', $form->id],'id'=>'delete-form-'.$form->id]) !!}
                                            {!! Form::close() !!}
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
