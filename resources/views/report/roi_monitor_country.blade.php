@extends('layouts.admin')

@section('title')
    {{ __('Monitor ROI') }}
@endsection

@section('content')
    <div class="page-content">
        <div class="page-title" style="margin-bottom:25px">
            <div class="row justify-content-between align-items-center">
                <div
                    class="col-xl-4 col-lg-4 col-md-4 d-flex align-items-center justify-content-between justify-content-md-start mb-3 mb-md-0">
                    <div class="d-inline-block">
                        {{-- <h5 class="h4 d-inline-block font-weight-400 mb-0"><b>Report Summery</b></h5><br> --}}
                        <p class="d-inline-block font-weight-200 mb-0"> Monitor ROI by Operator</p>
                    </div>
                </div>
                <div
                    class="col-xl-8 col-lg-8 col-md-8 d-flex align-items-center justify-content-between justify-content-md-end">
                </div>
            </div>
        </div>

        {{-- @include('report.partials.filter') --}}
        {{-- @include('report.partials.graph') --}}

        @include('report.partials.roi_filter')

        {{-- <div id="reportXls">
        <div id="container"> --}}
        {{--
        <div class="button-group-pills" data-toggle="buttons">
            <label class="btn btn-default active revenue">
                <input type="checkbox" name="options" id="revenue">
                <div>REVENUE</div>
            </label>
            <label class="btn btn-default roi">
                <input type="checkbox" name="options" id="roi">
                <div>ROI</div>
            </label>
            <label class="btn btn-default mo">
                <input type="checkbox" name="options" id="mo">
                <div>MO</div>
            </label>
            <label class="btn btn-default billing">
                <input type="checkbox" name="options" id="billing">
                <div>BILLING RATE</div>
            </label>
            <label class="btn btn-default renewal">
                <input type="checkbox" name="options" id="renewal">
                <div>RENEWAL</div>
            </label>
        </div>
        --}}

        <div class="d-flex align-items-center my-3">
            <span
                class="badge badge-secondary px-2 bg-primary text-uppercase">{{ isset($AllCuntryGrosRev['month_string']) ? $AllCuntryGrosRev['month_string'] : '' }}</span>
            <span class="flex-fill ml-2 bg-primary w-100 font-weight-bold" style="height: 1px;"></span>
            <div class="text-right pl-2">
                <button class="btn btn-sm monitor-xls" style="color:white; background-color:green" data-param="all"><i
                        class="fa fa-file-excel-o"></i>Export XLS</button>
            </div>
        </div>



        {{-- <pre>
            {{print_r($totelCountryCosts)}}

            {{dd('rr')}}

        </pre> --}}



        <div class="table-responsive shadow-sm mb-3 tableFixHead" id="all">
            <h1 style="display:none">Monitor Roi Data Monthly</h1>
            <table class="table table-light table-bordered m-0 font-13 table-text-no-wrap" id="dtbl">
                <thead class="thead-dark">
                    <tr>
                        <th class="first-col" width="10%">Operator</th>
                        {{-- <th>Total</th> --}}
                        <th>Avg</th>
                        {{-- <th>T.Mo.End</th> --}}
                        @foreach ($no_of_days as $no_of_day)
                            <th>{{ $no_of_day['no'] }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @if (isset($totelCountryCosts) || !empty($totelCountryCosts))
                        @foreach ($totelCountryCosts as $totelCountryCost)
                            @php
                                $countryArray = explode(' ', isset($totelCountryCost['country']) ? $totelCountryCost['country']['country'] : '');
                                $countryClass0 = isset($countryArray[0]) ? $countryArray[0] : '';
                                $countryClass1 = isset($countryArray[1]) ? '_' . $countryArray[1] : '';
                                $countryClass2 = isset($countryArray[2]) ? '_' . $countryArray[2] : '';
                                $countryClass = $countryClass0 . $countryClass1 . $countryClass2;
                                $countryFlag = isset($totelCountryCost['operator'][0]['country']) ? $totelCountryCost['operator'][0]['country']['flag'] : $totelCountryCost['country']['flag'];
                                // +isset($countryArray[2])?$countryArray[2]:'';
                                // dd($countryClass);
                            @endphp
                            <tr class=" country-odd-bg">
                                <td class="font-weight-bold first-col"><span class="opbtn"
                                        data-param="{{ isset($countryClass) ? $countryClass : '' }}"
                                        style="cursor:pointer; min-width:10px; font-size:20px;">

                                    </span>
                                    <img src="{{ asset('/flags/' . $countryFlag) }}" height="20"
                                        width="30">&nbsp;{{ isset($totelCountryCost['country']) ? $totelCountryCost['country']['country'] : '' }}
                                </td>
                                {{-- <td>
                                    <p class="font-weight-bold cost">
                                        {{ isset($totelCountryCost['country']) ? ($totelCountryCost['country']['country']) : ''}}
                                    </p>
                                </td> --}}

                                <td>
                                    <p class="font-weight-bold">
                                        {{ isset($totelCountryCost['roi']['avg']) ? numberConverter($totelCountryCost['roi']['avg'], 4, '') : 0 }}
                                    </p>
                                </td>
                                {{-- <td>
                                    <p class="font-weight-bold">
                                        {{ isset($totelCountryCost['roi']['t_mo_end']) ? numberConverter($totelCountryCost['roi']['t_mo_end'], 2, '') : 0 }}
                                    </p>
                                </td> --}}


                                @if (isset($totelCountryCost['roi']['dates']) || !empty($totelCountryCost['roi']['dates']))
                                    @foreach ($no_of_days as $no_of_day)
                                        <td>

                                            <p class="font-weight-bold">
                                                {{ numberConverter($totelCountryCost['roi']['dates'][$no_of_day['date']]['value'], 4, '') }}
                                            </p>

                                        </td>
                                    @endforeach
                                @endif
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>


    </div>

@endsection
@push('script')
    <script>
        $(document).ready(function() {
            // Add a change event listener to the checkbox with id "revenue"
            $('#revenue').change(function() {
                // Check if the checkbox is checked
                if ($(this).prop('checked')) {
                    // Show elements with class "revenue-value"
                    $('.revenue-value').show();
                } else {
                    // Hide elements with class "revenue-value"
                    $('.revenue-value').hide();
                }
            });

            $('#roi').change(function() {
                // Check if the checkbox is checked
                if ($(this).prop('checked')) {
                    // Show elements with class "roi-value"
                    $('.roi-value').show();
                } else {
                    // Hide elements with class "roi-value"
                    $('.roi-value').hide();
                }
            });

            $('#mo').change(function() {
                // Check if the checkbox is checked
                if ($(this).prop('checked')) {
                    // Show elements with class "mo-value"
                    $('.mo-value').show();
                } else {
                    // Hide elements with class "mo-value"
                    $('.mo-value').hide();
                }
            });

            $('#billing').change(function() {
                // Check if the checkbox is checked
                if ($(this).prop('checked')) {
                    // Show elements with class "billing-value"
                    $('.bill-value').show();
                } else {
                    // Hide elements with class "billing-value",
                    $('.bill-value').hide();
                }
            });
            // $('.bill-value').hide();

            $('#renewal').change(function() {
                // Check if the checkbox is checked
                if ($(this).prop('checked')) {
                    // Show elements with class "billing-value"
                    $('.renewal-value').show();
                } else {
                    // Hide elements with class "billing-value",
                    $('.renewal-value').hide();
                }
            });
        });
    </script>
@endpush
