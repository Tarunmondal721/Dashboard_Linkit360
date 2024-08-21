<div class="card shadow-sm mt-0">
    <div class="card-body">
        {{ Form::model([], ['route' => ['operator.name.update'], 'method' => 'POST']) }}
        <div class="form-group row">
            <div class="col-md-6">
                <input type="hidden" name="operator" value="{{ $operator->id_operator }}">
                <div class="form-group field-orev-share required has-success">
                    <label class="control-label" for="operatorName">Operator name </label>
                    <input type="text" id="opt-share" onkeyup="RevenueCal()" class="form-control" name="operatorName"
                        value="{{ isset($operator->display_name) ? $operator->display_name : $operator->operator_name }}"
                        aria-required="true" aria-invalid="false">
                    <div class="help-block"></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group field-orev-share required has-success">
                    <label class="control-label" for="businessType">Business Type</label>
                    <select name="business_type" class="form-control select2" required id="business_type">
                        <option value="">Select Business Type</option>
                        <option value="digital" <?php echo isset($operator) && $operator->business_type == 'digital' ? 'selected' : ''; ?>>Digital</option>
                        <option value="ott" <?php echo isset($operator) && $operator->business_type == 'ott' ? 'selected' : ''; ?>>Ott</option>
                        <option value="saas" <?php echo isset($operator) && $operator->business_type == 'saas' ? 'selected' : ''; ?>>Saas</option>
                        <option value="service" <?php echo isset($operator) && $operator->business_type == 'service' ? 'selected' : ''; ?>>Service</option>
                        <option value="saas_payment" <?php echo isset($operator) && $operator->business_type == 'saas_payment' ? 'selected' : ''; ?>>SaaS Payment</option>
                        <option value="saas_music" <?php echo isset($operator) && $operator->business_type == 'saas_music' ? 'selected' : ''; ?>>SaaS Music</option>
                        <option value="saas_gift" <?php echo isset($operator) && $operator->business_type == 'saas_gift' ? 'selected' : ''; ?>>SaaS Gift</option>
                    </select>
                    <div class="help-block"></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group field-orev-share required has-success">
                    <label class="control-label" for="businessType">Company</label>
                    <select name="company" class="form-control select2" id="company">
                        <option value="">Select Comapny</option>
                        @foreach ($companys as $company)
                            <option value="{{ $company->id }}" <?php echo isset($operator->company_operators) && $company->id == $operator->company_operators->company_id ? 'selected' : ''; ?>>{{ $company->name }}</option>
                        @endforeach
                    </select>
                    <div class="help-block"></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group field-orev-share required has-success">
                    <label class="control-label" for="businessType">Account Manager</label>
                    <select name="manager" class="form-control select2" id="manager">
                        <option value="">Select Account Manager</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" <?php echo isset($operator->account_manager) && $user->id == $operator->account_manager->user_id ? 'selected' : ''; ?>>{{ $user->name }}</option>
                        @endforeach
                    </select>
                    <div class="help-block"></div>
                </div>
            </div>
            <div class="col-md-6">
                <h5>Default Vat</h5>
                <div class="form-group field-orev-share required has-success">
                    <label class="control-label" for="vat">VAT</label>
                    <input type="number" id="vat" class="form-control" name="vat"
                        value="{{ isset($operator->vat) ? $operator->vat : '' }}" aria-required="true" aria-invalid="false"
                        placeholder="Please input number" min="0" step="0.01">
                    <div class="help-block"><small>formula calculation gross revenue * VAT<br>example = 100.000 IDR *
                            10% then VAT = 10.000 IDR</small></div>
                </div>
            </div>
            <div class="col-md-6">
                <h5>Default Wht</h5>
                <div class="form-group field-orev-share required has-success">
                    <label class="control-label" for="wht">WHT</label>
                    <input type="number" id="wht" class="form-control" name="wht"
                        value="{{ isset($operator->wht) ? $operator->wht : '' }}" aria-required="true" aria-invalid="false"
                        placeholder="Please input number" min="0" step="0.01">
                    <div class="help-block"><small>formula calculation gross revenue * WHT<br>example = 100.000 IDR *
                            10% then WHT = 10.000 IDR</small></div>
                </div>
            </div>

            <div class="col-md-6">
                <h5>Specific Month Vat</h5>
                {{-- <input type="hidden" name="operator" value="{{ $id }}"> --}}
                <div class="form-group field-orev-share required has-success">
                    <label class="control-label" for="orev-share">Vat (%)</label>
                    <input type="text" id="operator_vat_date" class="form-control" name="operator_vat_date"
                        aria-required="true" aria-invalid="false">
                    <div class="help-block"></div>
                    <span class="gu-hide" style="color: red;"
                        id="erroropt-share">{{ __('*Please enter operator revenue share') }}</span>

                    <div class="form-group field-mrev-share required has-success">
                        <label class="control-label" for="mrev-share">Year</label>
                        <input type="text" id="year" class="form-control" name="year">
                        <div class="help-block"></div>
                        <span class="gu-hide" style="color: red;"
                            id="erroryear">{{ __('*Please enter year') }}</span>

                        <label class="control-label" for="mrev-share">Month</label>
                        <select name="month" class="form-control select2" id="month" <?php echo isset($operator->month) ? $operator->month : ''; ?>>
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
                        <span class="gu-hide" style="color: red;"
                            id="errormonth">{{ __('*Please enter month') }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <h5>Specific Month Wht</h5>

                <div class="form-group field-mrev-share required has-success">
                    <label class="control-label" for="mrev-share">Wht (%)</label>
                    <input type="text" id="mrch_share-date" class="form-control" name="operator_wht_date"
                        aria-required="true" aria-invalid="false">
                    <div class="help-block"></div>
                </div>

                <div class="form-group field-mrev-share required has-success">
                    <label class="control-label" for="mrev-share">Year</label>
                    <input type="text" id="wht_year" class="form-control" name="wht_year">
                    <div class="help-block"></div>
                    <span class="gu-hide" style="color: red;" id="erroryear">{{ __('*Please enter year') }}</span>
                    <label class="control-label" for="mrev-share">Month</label>
                    <select name="wht_month" class="form-control select2" id="month1" <?php echo isset($operator->month) ? $operator->month : ''; ?>>
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


            <div class="col-md-6">
                <h5>Default MISC TAX</h5>

                <div class="form-group field-orev-share required has-success">
                    <label class="control-label" for="miscTax">MISC TAX</label>
                    <input type="number" id="miscTax" class="form-control" name="miscTax"
                        value="{{ isset($operator->miscTax) ? $operator->miscTax : '' }}" aria-required="true"
                        aria-invalid="false" placeholder="Please input number" min="0" step="0.01">
                    <div class="help-block"><small>formula calculation gross revenue * MISC TAX<br>example = 100.000
                            IDR * 10% then MISC TAX = 10.000 IDR</small></div>
                </div>
            </div>
            <div class="col-md-6">
                <h5>Specific Month MISC TAX</h5>
                <div class="form-group field-mrev-share required has-success">
                    <label class="control-label" for="mrev-share">MISC TAX (%)</label>
                    <input type="text" id="mrch_share-date" class="form-control" name="operator_misc_tax_date"
                        aria-required="true" aria-invalid="false">
                    <div class="help-block"></div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group field-mrev-share required has-success">
                            <label class="control-label" for="mrev-share">Year</label>
                            <input type="text" id="misc_year" class="form-control" name="misc_year">
                            {{-- <div class="help-block"></div> --}}
                            <span class="gu-hide" style="color: red;" id="erroryear">{{ __('*Please enter year') }}</span>

                        </div>

                    </div>
                    <div class="col-md-6">

                        <div class="form-group field-mrev-share required has-success">

                            <label class="control-label" for="mrev-share">Month</label>
                            <select name="misc_month" class="form-control select2" id="month2" <?php echo isset($operator->month) ? $operator->month : ''; ?>>
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


            <div class="col-md-6">
                <div class="form-group field-orev-share required has-success">
                    <label class="control-label" for="hostingCost">HOSTING COST</label>
                    <input type="number" id="hostingCost" class="form-control" name="hostingCost"
                        value="{{ isset($operator->hostingCost) ? $operator->hostingCost : '' }}" aria-required="true"
                        aria-invalid="false" placeholder="Please input number" min="0" step="0.01">
                    <div class="help-block"></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group field-orev-share required has-success">
                    <label class="control-label" for="content">Content</label>
                    <input type="number" id="content" class="form-control" name="content"
                        value="{{ isset($operator->content) ? $operator->content : '' }}" aria-required="true"
                        aria-invalid="false" placeholder="Please input number" min="0" step="0.01">
                    <div class="help-block"></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group field-orev-share required has-success">
                    <label class="control-label" for="rnd">RND</label>
                    <input type="number" id="rnd" class="form-control" name="rnd"
                        value="{{ isset($operator->rnd) ? $operator->rnd : '' }}" aria-required="true"
                        aria-invalid="false" placeholder="Please input number" min="0" step="0.01">
                    <div class="help-block"></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group field-orev-share required has-success">
                    <label class="control-label" for="bd">BD</label>
                    <input type="number" id="bd" class="form-control" name="bd"
                        value="{{ isset($operator->bd) ? $operator->bd : '' }}" aria-required="true"
                        aria-invalid="false" placeholder="Please input number" min="0" step="0.01">
                    <div class="help-block"></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group field-orev-share required has-success">
                    <label class="control-label" for="miscCost">MISC Cost</label>
                    <input type="number" id="miscCost" class="form-control" name="miscCost"
                        value="{{ isset($operator->miscCost) ? $operator->miscCost : '' }}" aria-required="true"
                        aria-invalid="false" placeholder="Please input number" min="0" step="0.01">
                    <div class="help-block"><small>formula calculation gross revenue * MISC Cost<br>example = 100.000
                            IDR * 10% then MISC Cost = 10.000 IDR</small></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group field-orev-share required has-success">
                    <label class="control-label" for="marketCost">Marketing Cost</label>
                    <input type="number" id="marketCost" class="form-control" name="marketCost"
                        value="{{ isset($operator->marketCost) ? $operator->marketCost : '' }}" aria-required="true"
                        aria-invalid="false" placeholder="Please input number" min="0" step="0.01">
                    <div class="help-block"></div>
                </div>
            </div>
            {{-- <div class="col-md-6">
        <div class="form-group field-mrev-share required has-success">
          <label class="control-label" for="mrev-share">Merchant Revenue Share (%)</label>
          <input type="text" id="mrch_share" value="{{isset($operator->merchant_revenue_share)?$operator->merchant_revenue_share:''}}" class="form-control" name="merchant_revenue_share"  readonly="readonly" aria-required="true" aria-invalid="false">
          <div class="help-block"></div>
        </div>
      </div> --}}
        </div>
        <div class="form-group row">
            <div class="col-sm-3">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
        {{ Form::close() }}
    </div>
</div>


@php
    $month = [
        '01' => 'January',
        '02' => 'February',
        '03' => 'March',
        '04' => 'April',
        '05' => 'May',
        '06' => 'June',
        '07' => 'July',
        '08' => 'August',
        '09' => 'September',
        '10' => 'October',
        '11' => 'November',
        '12' => 'December',
    ];
@endphp

<div class="card shadow-sm mt-0">
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <h5>Vat MonthWise Data</h5>
                <table class="table table-striped dataTable">
                    <thead>
                        <tr>
                            <th>{{ __('Year') }}</th>
                            <th>{{ __('Month') }}</th>
                            <th>{{ __('Vat(%)') }}</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($vat as $share)
                            <tr>
                                <td>{{ $share->year }}</td>
                                <td>{{ $month[$share->month] }}</td>
                                <td>{{ $share->vat }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="col-md-4">
                <h5>Wht MonthWise Data</h5>
                <table class="table table-striped dataTable">
                    <thead>
                        <tr>
                            <th>{{ __('Year') }}</th>
                            <th>{{ __('Month') }}</th>
                            <th>{{ __('Wht(%)') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($wht as $whts)
                            <tr>
                                <td>{{ $whts->year }}</td>
                                <td>{{ $month[$whts->month] }}</td>
                                <td>{{ $whts->wht }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="col-md-4">
                <h5>Misc Tax MonthWise Data</h5>
                <table class="table table-striped dataTable">
                    <thead>
                        <tr>
                            <th>{{ __('Year') }}</th>
                            <th>{{ __('Month') }}</th>
                            <th>{{ __('Misc Tax(%)') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($misc as $miscs)
                            <tr>
                                <td>{{ $miscs->year }}</td>
                                <td>{{ $month[$miscs->month] }}</td>
                                <td>{{ $miscs->misc_tax }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
