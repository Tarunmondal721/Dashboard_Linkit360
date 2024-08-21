<div class="card shadow-sm mt-0">
  <div class="card-body">
    {{ Form::model($operator, array('route' => array('management.revShare.update.date'), 'method' => 'POST' ,)) }}
    <div class="row">
      <div class="col-md-10">
        <h5>Default Revenue Share</h5>
        <div class="form-group row">
          <div class="col-md-6">
            <input type="hidden" name="operator" value="{{$id}}">
            <div class="form-group field-orev-share required has-success">
              <label class="control-label" for="orev-share">Operator Revenue Share (%)</label>
              <input type="text" id="opt-share" class="form-control" onkeyup="RevenueCal()" name="operator_revenue_share" value="{{isset($operator->operator_revenue_share)?$operator->operator_revenue_share:''}}" aria-required="true" aria-invalid="false">
              <div class="help-block"></div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group field-mrev-share required has-success">
              <label class="control-label" for="mrev-share">Merchant Revenue Share (%)</label>
              <input type="text" id="mrch_share" value="{{isset($operator->merchant_revenue_share)?$operator->merchant_revenue_share:''}}" class="form-control" name="merchant_revenue_share"  readonly="readonly" aria-required="true" aria-invalid="false">
              <div class="help-block"></div>
            </div>
          </div>
        </div>
        <h5>Specific Month Revenue Share</h5>
        <div class="form-group row">
          <div class="col-md-6">
            <input type="hidden" name="operator" value="{{$id}}">
            <div class="form-group field-orev-share required has-success">
              <label class="control-label" for="orev-share">Operator Revenue Share (%)</label>
              <input type="text" id="opt-share-date" class="form-control" onkeyup="RevenueDateCal()" name="operator_revenue_share_date" aria-required="true" aria-invalid="false">
              <div class="help-block"></div>
              <span class="gu-hide" style="color: red;" id="erroropt-share">{{ __('*Please enter operator revenue share') }}</span>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group field-mrev-share required has-success">
              <label class="control-label" for="mrev-share">Merchant Revenue Share (%)</label>
              <input type="text" id="mrch_share-date" class="form-control" name="merchant_revenue_share_date"  readonly="readonly" aria-required="true" aria-invalid="false">
              <div class="help-block"></div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group field-mrev-share required has-success">
              <label class="control-label" for="mrev-share">Year</label>
              <input type="text" id="year" class="form-control" name="year">
              <div class="help-block"></div>
              <span class="gu-hide" style="color: red;" id="erroryear">{{ __('*Please enter year') }}</span>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group field-mrev-share required has-success">
              <label class="control-label" for="mrev-share">Month</label>
              <select name="month" class="form-control select2" id="month" <?php  echo isset($operator->month) ? ($operator->month) : '' ?> >
                <option value="">Select Month</option>
                <option value="01">January</option>
                <option value="02">February</option>
                <option value="03">March</option>
                <option value="04">April</option>
                <option value="05">May</option>
                <option value="06">June</option>
                <option value="07">July</option>
                <option value="08">August</option>
                <option value="09">September</option>
                <option value="10">October</option>
                <option value="11">November</option>
                <option value="12">December</option>
              </select>
              <div class="help-block"></div>
              <span class="gu-hide" style="color: red;" id="errormonth">{{ __('*Please enter month') }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="form-group row">
      <div class="col-sm-3">
        <button type="submit" id="revenueUpdBtn" class="btn btn-primary">Submit</button>
      </div>
    </div>
    {{ Form::close() }}
  </div>
</div>

@php
$month = ['01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April', '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August', '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'];
@endphp

<div class="card shadow-sm mt-0">
  <div class="card-body">
    <div class="row">
      <div class="col-md-6">
        <table class="table table-striped dataTable">
          <thead>
            <tr>
              <th>{{__('Year')}}</th>
              <th>{{__('Month')}}</th>
              <th>{{__('Operator Revenue Share(%)')}}</th>
              <th>{{__('Merchant Revenue Share(%)')}}</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($revShare as $share)
            <tr>
              <td>{{ $share->year }}</td>
              <td>{{ $month[$share->month] }}</td>
              <td>{{ $share->operator_revenue_share }}</td>
              <td>{{ $share->merchant_revenue_share }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

