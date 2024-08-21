@extends('layouts.admin')

@section('title')
    {{ __('Manage Operator') }}
@endsection

@section('action-button')
    <div class="all-button-box row d-flex justify-content-end">
        @can('Create Role')
        <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6"></div>
        @endcan
    </div>
@endsection
@section('content')
    @php
    if ($errors->any()) {
        foreach ($errors->all() as $error) {
            Session::flash('error', $error);
        }
    }
    @endphp
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
                    <div class="operatorManagement">
                        <div class="table-responsive">
                            <table class="table table-striped dataTable">
                                <thead>
                                    <tr>
                                        <th>{{__('Operator Name')}}</th>
                                        <th>{{__('Operator Display Name')}}</th>
                                        <th>{{__('Country Name')}}</th>
                                        <th>{{__('Company Name')}}</th>
                                        <th>{{__('Business Type')}}</th>
                                        <th>{{__('Account Manager Name')}}</th>
                                        <th>{{__('Operator Share')}}</th>
                                        <th>{{__('Merchant Share')}}</th>
                                        <th>{{__('Total Cost')}}</th>
                                        <th>{{__('Total Tax')}}</th>
                                        <th>{{__('Status')}}</th>
                                        <th>{{__('Action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($operators as $operator)
                                    <tr>
                                        <td class="Role">{{isset($operator->operator_name) && $operator->operator_name ? $operator->operator_name : $operator->operator_name}}</td>
                                        <td class="Role">{{ !empty($operator->display_name)?$operator->display_name:$operator->operator_name }}</td>
                                        <td class="Role">{{ $operator->country_name }}</td>
                                        <td class="Role">{{ !empty($operator->company_operators) ? $operator->company_operators->Company->name :'' }}</td>
                                        <td class="Role">{{ !empty($operator->business_type)?$operator->business_type:'' }}</td>
                                        <td class="Role">{{ !empty($operator->account_manager->user)?$operator->account_manager->user->name:''}}</td>
                                        <td class="Role">{{ !empty($operator->revenueshare)?$operator->revenueshare->operator_revenue_share:''}}</td>
                                        <td class="Role">{{ !empty($operator->revenueshare)?$operator->revenueshare->merchant_revenue_share:'' }}</td>
                                        <td class="Role">{{ ((int)$operator->hostingCost+(int)$operator->content+(int)$operator->bd+(int)$operator->rnd+(int)$operator->miscCost+(int)$operator->marketCost) }}</td>
                                        <td class="Role">{{ ((int)$operator->vat+(int)$operator->wht+(int)$operator->miscTax) }}</td>
                                        <td>
                                            <input class="status_btn" type="checkbox" @if ($operator->status == 1) checked  @endif data-operator_id={{$operator->id}}  data-toggle="toggle" data-on="Active" data-off="Inactive" data-onstyle="primary" data-offstyle="warning">
                                        </td>
                                        <td class="Action operatorManagementAction">
                                            {{-- <a href="javascript:void(0)" data-url="{{ route('management.rev-share',$operator->id_operator) }}"  data-size="lg" data-ajax-popup="true" class="edit-icon bg-warning" data-title="Create Revenue Share #1"  data-toggle="tooltip" data-original-title="Create Revenue Share"><i class="fas fa-table"></i></a> --}}
                                            <span>
                                                <a href="#" data-url="{{route('management.operator.edit',$operator->id_operator)}}" data-size="lg" data-ajax-popup="true" data-title="{{__('Edit Details')}}" class="edit-icon" data-toggle="tooltip" data-original-title="Edit Operator Details"><i class="fas fa-pencil-alt"></i></a>
                                            </span>
                                            <span>
                                                <a href="#" data-url="{{route('management.revShare.date',$operator->id_operator)}}" data-size="lg" data-ajax-popup="true" data-title="{{__('Edit Revenue Share')}}" class="edit-icon" data-toggle="tooltip" data-original-title="Edit Revenue Share"><i class="fas fa-pencil-alt"></i></a>
                                            </span>
                                            {{-- <span>
                                                <a href="#" data-url="{{route('management.VatWht.date',$operator->id_operator)}}" data-size="lg" data-ajax-popup="true" data-title="{{__('Edit Vat Wht')}}" class="edit-icon" data-toggle="tooltip" data-original-title="Edit Vat Wht"><i class="fas fa-pencil-alt"></i></a>
                                            </span> --}}
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
