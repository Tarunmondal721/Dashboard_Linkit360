<div class="card shadow-sm mt-0">
    <div class="card-body">
      <form>
        <div class="form-group row">
          <div class="col-md-10">
            <div class="form-group field-revenueshare-operator required">
              <label class="control-label" for="revenueshare-operator">Operator Name</label>
              <select id="revenueshare-operator" class="form-control" name="Revenueshare[operator]"
                disabled="disabled" aria-required="true">
                <option value="">Select Operator</option>
                <option value="1" selected="">telkomsel</option>
                
              </select>

              <div class="help-block"></div>
            </div> <input type="hidden" name="Revenueshare[operator]" value="1" readonly="">
            <input type="hidden" name="Revenueshare[merchant]" value="2" readonly="">
          </div>
        </div>
        <div class="row">
          <div class="col-md-10">
            <div class="form-group row">
              <div class="col-md-6">
                <div class="form-group field-orev-share required has-success">
                  <label class="control-label" for="orev-share">Operator Revenue Share (%)</label>
                  <input type="text" id="orev-share" class="form-control"
                    name="Revenueshare[operator_revenue_share]" value="50.0000" aria-required="true"
                    aria-invalid="false">

                  <div class="help-block"></div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group field-mrev-share required has-success">
                  <label class="control-label" for="mrev-share">Merchant Revenue Share (%)</label>
                  <input type="text" id="mrev-share" class="form-control"
                    name="Revenueshare[merchant_revenue_share]" value="50.0000" readonly="readonly"
                    aria-required="true" aria-invalid="false" disabled>

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
      </form>
    </div>
  </div>
  {{-- @endsection --}}