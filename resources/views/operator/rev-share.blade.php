<div class="card shadow-sm mt-0">
  <div class="card-body">
    {{ Form::model($operator, array('route' => array('operator.updateRev', $id), 'method' => 'POST')) }}
    <div class="row">
      <div class="col-md-10">
        <div class="form-group row">
          <div class="col-md-6">
            <input type="hidden" name="operator" value="{{$id}}">
            <div class="form-group field-orev-share required has-success">
              <label class="control-label" for="orev-share">Operator Revenue Share (%)</label>
              <input type="text" id="opt-share" onkeyup="RevenueCal()" class="form-control" name="operator_revenue_share" value="{{isset($operator->operator_revenue_share)?$operator->operator_revenue_share:''}}" aria-required="true" aria-invalid="false">
              <div class="help-block"></div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group field-mrev-share required has-success">
              <label class="control-label" for="mrev-share">Merchant Revenue Share (%)</label>
              <input type="text" id="mrch_share" value="{{isset($operator->merchant_revenue_share)?$operator->merchant_revenue_share:''}}" class="form-control"
                name="merchant_revenue_share"  readonly="readonly" aria-required="true" aria-invalid="false">
              <div class="help-block"></div>
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