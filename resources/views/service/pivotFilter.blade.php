@extends('layouts.admin')

@section('title')

    {{ __('User pivot') }}

@endsection

@section('content')

@php
if ($errors->any()) {
    foreach ($errors->all() as $error) {
        Session::flash('error', $error);
    }
}
@endphp
{{-- {{ Form::open(array('url' => '/management/pivot/update')) }} --}}
<form action="{{ route('management.pivot.update')}}" method="POST" onsubmit="return pivotUserSubmit()" >
    @csrf
<div class="page-content">

    <div class="page-title" style="margin-bottom:25px">
      <div class="row justify-content-between align-items-center">
        <div
          class="col-xl-8 col-lg-8 col-md-8 d-flex align-items-center justify-content-between justify-content-md-end">
        </div>
      </div>
    </div>

    <div class="card shadow-sm mt-0">
      <div class="card-body">
        <div class="row">
          <div class="col-md-12">
            <div class="subHeading">Type Based</div>
          </div>
        </div>
        @php
            $types=[];
            $datas=[];
            $date=[];
            if(isset($pivotUser)){
                $types=json_decode($pivotUser->report_type);
                $datas=json_decode($pivotUser->report_column);
                $date=json_decode($pivotUser->description);
            }
            // $date=[];
            // $month = date('m')-2;
            // $year = date('Y');
            // dd($date);
        @endphp

        <div class="row">

          <div class="col-md-3 custom-control custom-checkbox ">
            <input class="form-check-input" type="checkbox" value="country" id="country" name="type[]" <?php echo in_array("country", $types)?'checked':''; ?> >
            <label class="form-check-label  line-height03" for="country">
              Country
            </label>
          </div>

          <div class="col-md-3 custom-control custom-checkbox">
            <input class="form-check-input" type="checkbox" value="operator" id="operator" name="type[]" <?php echo in_array("operator", $types)?'checked':''; ?>>
            <label class="form-check-label  line-height03" for="operator">
              Operator
            </label>
          </div>

          <div class="col-md-3 custom-control custom-checkbox ">
            <input class="form-check-input" type="checkbox" value="company" id="company" name="type[]" <?php echo in_array("company", $types)?'checked':''; ?>>
            <label class="form-check-label  line-height03" for="company">
              Company
            </label>
          </div>

          <div class="col-md-3 custom-control custom-checkbox ">
            <input class="form-check-input" type="checkbox" value="account_manager" id="account_manager" name="type[]" <?php echo in_array("account_manager", $types)?'checked':''; ?>>
            <label class="form-check-label  line-height03" for="account_manager">
              Account Manager
            </label>
          </div>
          <span class="gu-hide" style="color: red;" id="errortype">{{ __('*Please select Type') }}</span>
        </div>
      </div>
    </div>

    <div class="card shadow-sm mt-0">
      <div class="card-body">
        <div class="row">
          <div class="col-md-12">
            <div class="subHeading">Data</div>
          </div>
        </div>

        <div class="row">

          <div class="col-md-3 custom-control custom-checkbox ">
            <input class="form-check-input" type="checkbox" value="revenue" id="revenue" name="data[]" <?php echo in_array("revenue", $datas)?'checked':''; ?> >
            <label class="form-check-label  line-height03" for="revenue">
              Revenue
            </label>
          </div>

          <div class="col-md-3 custom-control custom-checkbox">
            <input class="form-check-input" type="checkbox" value="cost_campaign" id="cost_campaign" name="data[]"  <?php echo in_array("cost_campaign", $datas)?'checked':''; ?> >
            <label class="form-check-label  line-height03" for="cost_campaign">
              Cost Campaign
            </label>
          </div>

          <div class="col-md-3 custom-control custom-checkbox ">
            <input class="form-check-input" type="checkbox" value="mo" id="mo" name="data[]" <?php echo in_array("mo", $datas)?'checked':''; ?> >
            <label class="form-check-label  line-height03" for="mo">
              MO
            </label>
          </div>

          <div class="col-md-3 custom-control custom-checkbox ">
            <input class="form-check-input" type="checkbox" value="roi" id="roi" name="data[]" <?php echo in_array("roi", $datas)?'checked':''; ?> >
            <label class="form-check-label  line-height03" for="roi">
              ROI
            </label>
          </div>

          <div class="col-md-3 custom-control custom-checkbox ">
            <input class="form-check-input" type="checkbox" value="arpu" id="arpu" name="data[]" <?php echo in_array("arpu", $datas)?'checked':''; ?> >
            <label class="form-check-label  line-height03" for="arpu">
              ARPU
            </label>
          </div>

          <div class="col-md-3 custom-control custom-checkbox ">
            <input class="form-check-input" type="checkbox" value="bill_rate" id="bill_rate" name="data[]" <?php echo in_array("bill_rate", $datas)?'checked':''; ?> >
            <label class="form-check-label  line-height03" for="bill_rate">
              Bill Rate
            </label>
          </div>

          <div class="col-md-3 custom-control custom-checkbox ">
            <input class="form-check-input" type="checkbox" value="pnl" id="pnl" name="data[]" <?php echo in_array("pnl", $datas)?'checked':''; ?> >
            <label class="form-check-label  line-height03" for="pnl">
              PNL
            </label>
          </div>
        </div>

        <span class="gu-hide" style="color: red;" id="errordata">{{ __('*Please select Data') }}</span>


      </div>
    </div>

    <div class="card shadow-sm mt-0">
      <div class="card-body">
        <div class="row">
          <div class="col-md-12">
            <div class="subHeading">Date Filter</div>
          </div>
        </div>

        <div class="row">
            <div class="col-md-3 custom-control custom-checkbox ">
                <input class="form-check-input" type="checkbox" value="today" id="today" name="date[]"  <?php echo in_array("today",$date)?'checked':''; ?> >
                <label class="form-check-label  line-height03" for="today">
                    Today
                </label>
            </div>
            <div class="col-md-3 custom-control custom-checkbox ">
                <input class="form-check-input" type="checkbox" value="yesterday" id="yesterday" name="date[]"  <?php echo in_array("yesterday",$date)?'checked':''; ?> >
                <label class="form-check-label  line-height03" for="yesterday">
                    Yesterday
                </label>
            </div>

            <div class="col-md-3 custom-control custom-checkbox ">
                <input class="form-check-input" type="checkbox" value="last7days" id="last7days" name="date[]"  <?php echo in_array("last7days",$date)?'checked':''; ?> >
                <label class="form-check-label  line-height03" for="last7days">
                    Last 7 days
                </label>
            </div>

            <div class="col-md-3 custom-control custom-checkbox ">
                <input class="form-check-input" type="checkbox" value="last30day" id="last30days" name="date[]"  <?php echo in_array("last30day",$date)?'checked':''; ?> >
                <label class="form-check-label  line-height03" for="last30days">
                    Last 30 days
                </label>
            </div>

            <div class="col-md-3 custom-control custom-checkbox ">
                <input class="form-check-input" type="checkbox" value="last60day" id="last2month" name="date[]"  <?php echo in_array("last60day",$date)?'checked':''; ?> >
                <label class="form-check-label  line-height03" for="last2month">
                    Last 2 month
                </label>
            </div>
            <div class="col-md-3 custom-control custom-checkbox ">
                <input class="form-check-input" type="checkbox" value="last90day" id="last3month" name="date[]"  <?php echo in_array("last90day",$date)?'checked':''; ?> >
                <label class="form-check-label  line-height03" for="last3month">
                    Last 3 month
                </label>
            </div>
          <div class="col-lg-4">

            <span class="gu-hide" style="color: red;" id="errordate">{{ __('*Please select Date') }}</span>
          </div>
          <div class="col-lg-4">
            <button type="submit" class="btn btn-primary" id="pivotsubmit">Save & Edit</button>
            {{-- <input type="submit" value="{{__('Save & Edit')}}" class="btn btn-primary"> --}}
          </div>



        </div>




      </div>
    </div>
    {{-- {{ Form::close() }} --}}
    </form>
  </div>
  <script src="{{ asset('assets/js/services.js') }}"></script>

@endsection
