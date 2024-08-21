<div class="card shadow-sm mt-0">
    <div class="card-body">
        {{ Form::model($operator, ['route' => ['management.vatwht.update.date'], 'method' => 'POST']) }}
        <div class="row">
            <div class="col-lg-12">
                <h5>Default Vat And Wht</h5>
                <div class="form-group row">
                    <div class="col-md-6">
                        <input type="hidden" name="operator" value="{{ $id }}">
                        <div class="form-group field-orev-share required has-success">
                            <label class="control-label" for="orev-share">Vat (%)</label>
                            <input type="text" id="opt-vat" class="form-control"
                                name="operator_vat"
                                value="{{ isset($operator->vat) ? $operator->vat : '' }}"
                                aria-required="true" aria-invalid="false">
                                <div class="help-block"><small>formula calculation gross revenue * VAT<br>example = 100.000 IDR * 10% then VAT = 10.000 IDR</small></div>
                            </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group field-mrev-share required has-success">
                            <label class="control-label" for="opt_wht">Wht (%)</label>
                            <input type="text" id="opt_wht"
                                value="{{ isset($operator->wht) ? $operator->wht : '' }}"
                                class="form-control" name="operator_wht"
                                aria-required="true" aria-invalid="false">
                                <div class="help-block"><small>formula calculation gross revenue * WHT<br>example = 100.000 IDR * 10% then WHT = 10.000 IDR</small></div>
                            </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-md-6">
                <h5>Specific Month Vat</h5>
                {{-- <input type="hidden" name="operator" value="{{ $id }}"> --}}
                <div class="form-group field-orev-share required has-success">
                    <label class="control-label" for="orev-share">Vat (%)</label>
                    <input type="text" id="operator_vat_date" class="form-control"
                        name="operator_vat_date" aria-required="true" aria-invalid="false">
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
        </div>


        {{-- </div>
        </div> --}}
        <div class="form-group row">
            <div class="col-sm-3">
                <button type="submit" id="revenueUpdBtn" class="btn btn-primary">Submit</button>
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
            <div class="col-md-6">
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
            <div class="col-md-6">
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
        </div>
    </div>
</div>
