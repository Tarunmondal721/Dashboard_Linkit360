@extends('layouts.admin')

@section('title')
    {{ __('Currency Management') }}
@endsection

@section('action-button')
    <div class="all-button-box row d-flex justify-content-end">
        <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
        <a href="javascript:void(0)" class="btn btn-xs btn-white btn-icon-only width-auto" data-ajax-popup="true" data-title="{{__('Create Country')}}" data-url="{{route('management.add-currency')}}">
                <i class="fas fa-plus"></i> {{__('Add')}}
            </a>
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
                            <th>{{__('Id')}}</th>
                            <th>{{__('Flag')}}</th>
                            <th>{{__('Country Name')}}</th>
                            <th>{{__('Country Code')}}</th>
                            <th>{{__('Currency Code')}}</th>
                            <th>{{__('USD')}}</th>
                            <th width="200px">{{__('Action')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($countries) && !empty($countries))
                        @foreach ($countries as $country)
                            <tr>
                                <td>{{$country->id}}</td>
                                <td><img src="/flags/{{$country->flag}}" width="100" height="50" class="border border-dark"></td>
                                <td>{{$country->country}}</td>
                                <td>{{$country->country_code}}</td>
                                <td>{{$country->currency_code}}</td>
                                <td>{{$country->usd}}</td>
                                <td class="Action">
                                <span>
                                     <a href="javascript:void(0)" data-url="{{ URL::to('management/edit-currency/'.$country->id) }}" data-size="lg" data-ajax-popup="true" data-title="{{__('Edit Currency #'.$country->id)}}" class="edit-icon"><i class="fas fa-pencil-alt"></i></a>

                                    {{-- <a href="javascript:void(0)" class="delete-icon"  data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$country->id}}').submit();"><i class="fas fa-trash"></i></a> --}}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
