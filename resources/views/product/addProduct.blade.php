@extends('layouts.admin')

@section('content')

    @php
        if ($errors->any()) {
            foreach ($errors->all() as $error) {
                Session::flash('error', $error);
            }
        }
    @endphp
    <div class="page-content page-content-center">

        <form action="{{ route('report.product.store') }}" method="POST"  onsubmit="return productSubmit()">
            @csrf
            <div class="page-title" style="margin-bottom:25px">
                <div class="row justify-content-between align-items-center">
                    <div
                        class="col-xl-4 col-lg-4 col-md-4 d-flex align-items-center justify-content-between justify-content-md-start mb-3 mb-md-0">
                        <div class="d-inline-block">
                            <h5 class="h4 d-inline-block font-weight-400 mb-0"><b>Add New Product</b></h5>
                        </div>
                    </div>
                    <div
                        class="col-xl-8 col-lg-8 col-md-8 d-flex align-items-center justify-content-between justify-content-md-end">
                    </div>
                </div>
            </div>
            <div class="card shadow-sm mt-0">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="subHeading">General Information</div>
                        </div>
                    </div>


                    <div class="row" style="margin-top: 10px;">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="redirect_summary_dropdown">Product Name*</label>
                                <input type="text" class="form-control fild-Style" id="name" name="name" aria-describedby="emailHelp" placeholder="Input product name">
                                <span class="gu-hide" style="color: red;"
                                    id="errorname">{{ __('*Please enter product name') }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="redirect_summary_dropdown">Doman*</label>
                                <input type="url" class="form-control fild-Style" id="doman" name="doman"
                                    aria-describedby="emailHelp" placeholder="Input doman">
                                <span class="gu-hide" style="color: red;"
                                    id="errordoman">{{ __('*Please enter doman') }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="redirect_summary_dropdown">Analytical Id*</label>
                                <input type="text" class="form-control fild-Style" id="analytical_id" name="analytical_id"
                                    aria-describedby="emailHelp" placeholder="Input analytical Id">
                                <span class="gu-hide" style="color: red;"
                                    id="erroranalytical_id">{{ __('*Please enter analytical id') }}</span>
                            </div>
                        </div>
                        <div class="col-md-12" style="text-align:right;">
                            <label class="invisible d-block">Button</label>
                            <button type="submit" class="btn btn-primary">Create</button>
                        </div>
                    </div>
                </div>
            </div>


        </form>

    </div>
    <script src="{{ asset('assets/js/services.js') }}"></script>
@endsection
