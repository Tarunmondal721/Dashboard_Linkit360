@extends('layouts.admin')

@section('title')
    {{ __('Company Management') }}
@endsection

@section('action-button')
    <div class="all-button-box row d-flex justify-content-end">
        <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
            <a href="javascript:void(0)" class="btn btn-xs btn-white btn-icon-only width-auto" data-ajax-popup="true" data-title="{{__('Create Company')}}" data-url="{{route('management.add-company')}}">
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
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <table class="table table-striped dataTable">
                        <thead>
                            <tr>
                                <th>{{__('Company Name')}}</th>
                                <th>{{__('Total of Operator')}}</th>
                                <th>{{__('Operators')}}</th>
                                <th width="200px">{{__('Action')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($companies) && !empty($companies))
                            @foreach ($companies as $company)
                            <tr>
                                <td class="company_name">{{$company->name}}</td>
                                <td class="company_name">{{ count ($company->company_operators)}}</td>
                                @if(isset($operators) && !empty($operators))
                                <td class="operator_name">
                                    <a href="javascript:void(0)" class="edit-icon bg-info" data-url="{{ URL::to('management/view-operators/'.$company->id) }}"  data-size="lg" data-ajax-popup="true" data-toggle="tooltip" data-original-title="View All operators" data-title="{{__('View All operators #'.$company->name)}}"><i class="fas fa-eye"></i></a>
                                </td>
                                @endif
                                <td class="Action">
                                    <a href="javascript:void(0)" data-url="{{ URL::to('management/edit-company/'.$company->id) }}" data-size="lg" data-ajax-popup="true" data-title="{{__('Edit Company #'.$company->id)}}" class="edit-icon"><i class="fas fa-pencil-alt"></i></a>
                                    <a href="{{ URL::to('management/company-operator/'.$company->id) }}" class="edit-icon bg-warning" data-toggle="tooltip"><i class="fa fa-podcast"></i></a>
                                </td>
                            </tr>
                            @endforeach
                            @endif
                            <tr>
                                <td class="company_name">Unknown Company</td>
                                <td class="company_name">{{ $unknown_operators }}</td>
                                <td class="operator_name">
                                    <a href="javascript:void(0)" class="edit-icon bg-info" data-url="{{ URL::to('management/view-unknown-company') }}"  data-size="lg" data-ajax-popup="true" data-toggle="tooltip" data-original-title="View All operators" data-title="{{__('View All operators #Unknown Company')}}"><i class="fas fa-eye"></i></a>
                                </td>
                                <td class="Action"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
