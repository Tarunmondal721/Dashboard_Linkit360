@extends('layouts.admin')

@section('title')
{{ __('Service List') }}
@endsection

@section('content')

<div class="row justify-content-between align-items-center">
    <div class="col-md-12 ">
        <div class="">
            <div class="card">
                <div class="table-responsive  table-striped" id="all">
                    <h1 style="display:hidden"></h1>
                    <table class="table table-light table-striped m-0 font-13 all" id="dtbl">
                        <thead class="thead-dark">
                            <tr>
                                <th class="align-middle">Name</th>
                                <th class="align-middle">Doman</th>
                                <th class="align-middle">Analytical Id</th>
                                <th class="align-middle">Status</th>
                                <th class="align-middle">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($products))

                            @foreach ($products as $product)
                            <tr>
                                <td>{{isset($product->name)?$product->name:''}}</td>
                                <td>{{isset($product->doman)?$product->doman:''}}</td>
                                <td>{{isset($product->analytical_id)?$product->analytical_id:''}}</td>
                                <td>{{isset($product->status)?$product->status($product->status):''}}</td>
                                <td class="Action">
                                    <span>

                                        <a href="javascript:void(0);" class="edit-icon bg-info" data-url="#" data-size="lg" data-toggle="tooltip" data-original-title="View Service Details" data-title="View Service Details"><i class="fas fa-eye"></i></a>

                                        <a href="{{ route('report.product.edit', ['id'=>$product->id]) }}" class="edit-icon" data-toggle="tooltip" data-original-title="{{__('edit product')}}"><i class="fas fa-pencil-alt"></i></a>
                                    </span>
                                </td>

                            </tr>
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
