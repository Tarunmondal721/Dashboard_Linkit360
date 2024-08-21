@extends('layouts.admin')

{{-- @section('title')



    {{ __('Log Performance') }}

@endsection --}}

@section('content')

<div class="page-content">
    <div class="page-title" style="margin-bottom:25px">
        <div class="row justify-content-between align-items-center">
            <div
                class="col-xl-4 col-lg-4 col-md-4 d-flex align-items-center justify-content-between justify-content-md-start mb-3 mb-md-0">
                <div class="d-inline-block">
                    <h5 class="h4 d-inline-block font-weight-400 mb-0"><b>Log Performance</b></h5>
                    <div style="white-space:nowrap;">Day Basis Data</div>
                </div>
            </div>
            <div
                class="col-xl-8 col-lg-8 col-md-8 d-flex align-items-center justify-content-between justify-content-md-end">
            </div>
        </div>
    </div>


    <div class="card shadow-sm mt-0">
        <div class="card-body">
            <form action="/daily-logperformance" id="summaryForm">
                <input type="hidden" name="_csrf"
                    value="ihIjQmO48ldgZeftvhoXVGlkeWnNEpg5qU-ivJqpfSjQS2YpKMvfDwwIqr_yQFE4AixLPoRn2nzGPcHs95lQYw=="
                    id="csrf_token">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="summarycompany">Company</label>
                            <select class="simple-multiple-select select2-hidden-accessible"
                                id="dashboard-company" name="company_id" style="width: 100%"
                                data-select2-id="select2-data-dashboard-company" tabindex="-1"
                                aria-hidden="true">
                                <option value="" data-select2-id="select2-data-2-a5a3">All Company</option>
                                <option value="7">ClickMultimediaTH</option>
                                <option value="15">KreativeBersamaID</option>
                                <option value="2">KreativeBersamaPH</option>
                                <option value="1">KreativeMultimediaVN</option>
                                <option value="11">LinkIT.Africa</option>
                                <option value="16">LinkIT.Airpay</option>
                                <option value="14">LinkIT.America</option>
                                <option value="12">LinkIT.Asia</option>
                                <option value="9">LinkIT.EU</option>
                                <option value="10">LinkIT.Global</option>
                                <option value="6">LinkIT.ID</option>
                                <option value="8">LinkIT.MENA</option>
                                <option value="13">LinkIT.OTT</option>
                                <option value="5">PASS</option>
                                <option value="17">R</option>
                                <option value="4">Waki</option>
                                <option value="3">Yatta</option>
                            </select><span class="select2 select2-container select2-container--bootstrap4"
                                dir="ltr" data-select2-id="select2-data-1-6mev" style="width: 100%;"><span
                                    class="selection"><span
                                        class="select2-selection select2-selection--single" role="combobox"
                                        aria-haspopup="true" aria-expanded="false" tabindex="0"
                                        aria-disabled="false"
                                        aria-labelledby="select2-dashboard-company-container"><span
                                            class="select2-selection__rendered"
                                            id="select2-dashboard-company-container" role="textbox"
                                            aria-readonly="true" title="All Company">All Company</span><span
                                            class="select2-selection__arrow" role="presentation"><b
                                                role="presentation"></b></span></span></span><span
                                    class="dropdown-wrapper" aria-hidden="true"></span></span>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="summarycountry">Country</label>
                            <select class="simple-multiple-select select2-hidden-accessible"
                                id="dashboard-country" name="country_id[]" style="width: 100%" multiple=""
                                data-select2-id="select2-data-dashboard-country" tabindex="-1"
                                aria-hidden="true">
                                <option value="">Country Name</option>
                                <option value="11">Bangladesh</option>
                                <option value="9">Cambodia</option>
                                <option value="65">Czech Republic</option>
                                <option value="21">Egypt</option>
                                <option value="3">Ghana</option>
                                <option value="90">Haiti</option>
                                <option value="1">Indonesia</option>
                                <option value="97">Iraq</option>
                                <option value="20">Ivory Coast</option>
                                <option value="14">Kenya</option>
                                <option value="160">Kingdom Saudi Arabia</option>
                                <option value="7">Kuwait</option>
                                <option value="8">Laos</option>
                                <option value="10">Malaysia</option>
                                <option value="17">Myanmar</option>
                                <option value="138">Nigeria</option>
                                <option value="141">Norway</option>
                                <option value="142">Oman</option>
                                <option value="5">Pakistan</option>
                                <option value="144">Palestine</option>
                                <option value="16">Philippines</option>
                                <option value="149">Poland</option>
                                <option value="6">Qatar</option>
                                <option value="162">Serbia</option>
                                <option value="19">South Africa</option>
                                <option value="173">Sri Lanka</option>
                                <option value="174">Sudan</option>
                                <option value="22">Sweden</option>
                                <option value="2">Thailand</option>
                                <option value="13">Timor Leste</option>
                                <option value="12">United Arab Emirates</option>
                                <option value="18">United Kingdom</option>
                                <option value="15">Vietnam</option>
                            </select><span class="select2 select2-container select2-container--bootstrap4"
                                dir="ltr" data-select2-id="select2-data-3-6dll" style="width: 100%;"><span
                                    class="selection"><span
                                        class="select2-selection select2-selection--multiple"
                                        role="combobox" aria-haspopup="true" aria-expanded="false"
                                        tabindex="-1" aria-disabled="false">
                                        <ul class="select2-selection__rendered"
                                            id="select2-dashboard-country-container"></ul><span
                                            class="select2-search select2-search--inline"><input
                                                class="select2-search__field" type="search" tabindex="0"
                                                autocorrect="off" autocapitalize="none" spellcheck="false"
                                                role="searchbox" aria-autocomplete="list" autocomplete="off"
                                                aria-describedby="select2-dashboard-country-container"
                                                placeholder="" style="width: 0.75em;"></span>
                                    </span></span><span class="dropdown-wrapper"
                                    aria-hidden="true"></span></span>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <label for="summery_operator_id">Operator</label>
                        <select class="simple-multiple-select select2-hidden-accessible"
                            id="dashboard-operator" name="operator_id[]" style="width: 100%" multiple=""
                            data-select2-id="select2-data-dashboard-operator" tabindex="-1"
                            aria-hidden="true">
                            <option value="">Operator Name</option>
                            <option value="22">Airtelbd</option>
                            <option value="8">Ais</option>
                            <option value="5">Axisss</option>
                            <option value="23">Blink</option>
                            <option value="57">Ccstrrp</option>
                            <option value="16">Cellcard</option>
                            <option value="56">Ci-orange-linkit</option>
                            <option value="89">Cze-nth-linkit</option>
                            <option value="9">Dtac</option>
                            <option value="64">Eg-etisalat</option>
                            <option value="63">Eg-orange</option>
                            <option value="62">Eg-vodafone</option>
                            <option value="111">Eg-we-linkit</option>
                            <option value="18">Etisalat-knc</option>
                            <option value="88">Gha-vodafone-linkit</option>
                            <option value="83">Hti-natcom-linkit</option>
                            <option value="113">Id-extravaganza-linkit </option>
                            <option value="41">Id-isat-pass</option>
                            <option value="38">Id-isat-waki</option>
                            <option value="37">Id-isat-yatta</option>
                            <option value="76">Id-oxford-airpay</option>
                            <option value="75">Id-smartfren-kb</option>
                            <option value="73">Id-smartfren-pass</option>
                            <option value="90">Id-smartfren-waki</option>
                            <option value="74">Id-smartfren-yatta</option>
                            <option value="103">Id-surat-sakit</option>
                            <option value="54">Id-telkomsel-mks</option>
                            <option value="96">Id-telkomsel-pgu</option>
                            <option value="85">Id-tri-yatta</option>
                            <option value="35">Id-xl-kb</option>
                            <option value="40">Id-xl-pass</option>
                            <option value="39">Id-xl-waki</option>
                            <option value="36">Id-xl-yatta</option>
                            <option value="2">Indosat</option>
                            <option value="109">Irq-zain-linkit</option>
                            <option value="11">Jazz</option>
                            <option value="33">Jazz-ev</option>
                            <option value="61">Kb-tri-telesat</option>
                            <option value="67">Kb-tsel-telesat</option>
                            <option value="78">Ksa-mobily</option>
                            <option value="79">Ksa-stc</option>
                            <option value="95">Ksa-virgin-linkit</option>
                            <option value="80">Ksa-zein</option>
                            <option value="84">Lao-tplus-linkit</option>
                            <option value="53">Linkit-rbt-isat</option>
                            <option value="98">Linkit-tsel-telesat</option>
                            <option value="77">Lka-dialog-dotjo</option>
                            <option value="14">Ltc</option>
                            <option value="15">Metfone</option>
                            <option value="58">Metfone-pax</option>
                            <option value="70">Mm-mytel-linkit</option>
                            <option value="29">Mobifone</option>
                            <option value="10">Mtnghana</option>
                            <option value="17">My Maxis</option>
                            <option value="43">My-gtmh-linkit</option>
                            <option value="72">Nga-mtn-finklasic</option>
                            <option value="110">Nor-all-linkit</option>
                            <option value="81">Omn-omantel-linkit</option>
                            <option value="97">Omn-ooredoo-linkit</option>
                            <option value="12">Ooredoo</option>
                            <option value="91">Pass-rbt-isat</option>
                            <option value="60">Pass-tri-telesat</option>
                            <option value="65">Pass-tsel-telesat</option>
                            <option value="50">Ph-globe</option>
                            <option value="87">Ph-smart-zed</option>
                            <option value="44">Pk-mobilink-noetic</option>
                            <option value="104">Pk-telenor-linkit</option>
                            <option value="45">Pk-telenor-noetic</option>
                            <option value="46">Pk-ufone-noetic</option>
                            <option value="47">Pk-warid-noetic</option>
                            <option value="48">Pk-zong-noetic</option>
                            <option value="106">Pol-orange-linkit</option>
                            <option value="108">Pol-plus-linkit</option>
                            <option value="107">Pol-t-mobile</option>
                            <option value="112">Pse-jawwal-kidzo</option>
                            <option value="93">Pse-jawwal-linkit</option>
                            <option value="94">Pse-ooredoo-linkit</option>
                            <option value="25">Safaricom</option>
                            <option value="100">Sdn-mtn-dotjo</option>
                            <option value="69">Se-all-linkit</option>
                            <option value="24">Smart</option>
                            <option value="6">Smartfren</option>
                            <option value="28">Smartp</option>
                            <option value="82">Srb-nth-linkit</option>
                            <option value="7">Tcel</option>
                            <option value="1">Telkomsel</option>
                            <option value="42">Th-ais-cm</option>
                            <option value="101">Th-ais-gemezz</option>
                            <option value="30">Th-ais-gmob</option>
                            <option value="92">Th-ais-gmob-r01-r03</option>
                            <option value="32">Th-ais-mks</option>
                            <option value="31">Th-ais-qr</option>
                            <option value="86">Th-true-gm</option>
                            <option value="34">Th-true-qr</option>
                            <option value="4">Three</option>
                            <option value="20">True-cyb</option>
                            <option value="102">Uae-etisalat-airpay</option>
                            <option value="71">Uae-etisalat-linkit</option>
                            <option value="49">Unitel</option>
                            <option value="21">Vietnamobile</option>
                            <option value="27">Viettel</option>
                            <option value="26">Vinaphone</option>
                            <option value="13">Viva</option>
                            <option value="52">Waki-rbt-isat</option>
                            <option value="59">Waki-tri-telesat</option>
                            <option value="66">Waki-tsel-telesat</option>
                            <option value="3">Xlaxiata</option>
                            <option value="51">Yatta-rbt-isat</option>
                            <option value="99">Yatta-tsel-telesat</option>
                            <option value="55">Za-mtn-mobixone</option>
                            <option value="105">Za-mtn-mondia</option>
                            <option value="68">Za-vodacom-mobixone</option>
                        </select><span class="select2 select2-container select2-container--bootstrap4"
                            dir="ltr" data-select2-id="select2-data-4-pwul" style="width: 100%;"><span
                                class="selection"><span
                                    class="select2-selection select2-selection--multiple" role="combobox"
                                    aria-haspopup="true" aria-expanded="false" tabindex="-1"
                                    aria-disabled="false">
                                    <ul class="select2-selection__rendered"
                                        id="select2-dashboard-operator-container"></ul><span
                                        class="select2-search select2-search--inline"><input
                                            class="select2-search__field" type="search" tabindex="0"
                                            autocorrect="off" autocapitalize="none" spellcheck="false"
                                            role="searchbox" aria-autocomplete="list" autocomplete="off"
                                            aria-describedby="select2-dashboard-operator-container"
                                            placeholder="" style="width: 0.75em;"></span>
                                </span></span><span class="dropdown-wrapper"
                                aria-hidden="true"></span></span>
                    </div>
                    <div class="col-lg-4">
                        <label class="invisible d-block">Search</label>
                        <button type="submit" class="btn btn-primary search_btn_summery_data1"
                            name="summarysubmit"><i class="fa fa-search"></i> Search</button>
                        <a class="btn btn-secondary" href="/daily-logperformance">Reset</a>
                    </div>
                </div>
                <div class="error_block"></div>
            </form>
        </div>
    </div>


    <div class="card shadow-sm mt-0">
        <div class="card-body">
            <div class="row">
              <div class="col-lg-4">
                  <label for="redirect_report_dropdown">Please Select</label>
                  <select class="simple-multiple-select select2-hidden-accessible" name="redirect_report_dropdown" id="logcycle" style="width: 100%" data-select2-id="select2-data-logcycle" tabindex="-1" aria-hidden="true">
                    <option value="daily" selected="" data-select2-id="select2-data-6-2ik3">Daily Log Performance</option>
                    <option value="monthly">Monthwise Log Performance</option>
                  </select><span class="select2 select2-container select2-container--bootstrap4" dir="ltr" data-select2-id="select2-data-5-9z89" style="width: 100%;"><span class="selection"><span class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-disabled="false" aria-labelledby="select2-logcycle-container"><span class="select2-selection__rendered" id="select2-logcycle-container" role="textbox" aria-readonly="true" title="Daily Log Performance">Daily Log Performance</span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>
              </div>
              <div class="col-lg-2">
                  <label class="invisible d-block">Search</label>
                  <button type="button" class="btn btn-primary" id="logbtn">Submit</button>
              </div>
              <div class="col-lg-4">
                  <label>Data Base On</label>
                  <div class="form-group">
                    <select class="simple-multiple-select select2-hidden-accessible" id="sorting_log" style="width: 100%" data-select2-id="select2-data-sorting_log" tabindex="-1" aria-hidden="true">
                      <option value="" data-select2-id="select2-data-8-qaun">Please Select</option>
                      <option value="higher_revenue_usd">Highest Revenue USD</option>
                      <option value="lowest_revenue_usd">Lowest Revenue USD</option>
                      <option value="higher_revenue_share_usd">Highest Revenue Share USD</option>
                      <option value="lowest_revenue_share_usd">Lowest Revenue Share USD</option>
                      <option value="higher_mo">Highest MO</option>
                      <option value="lowest_mo">Lowest MO</option>
                      <option value="higher_cost_campaign">Highest Cost Campaign</option>
                      <option value="lowest_cost_campaign">Lowest Cost Campaign</option>
                      <option value="higher_pnl">Highest PNL</option>
                      <option value="lowest_pnl">Lowest PNL</option>
                    </select><span class="select2 select2-container select2-container--bootstrap4" dir="ltr" data-select2-id="select2-data-7-337j" style="width: 100%;"><span class="selection"><span class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-disabled="false" aria-labelledby="select2-sorting_log-container"><span class="select2-selection__rendered" id="select2-sorting_log-container" role="textbox" aria-readonly="true" title="Please Select">Please Select</span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>
                  </div>
              </div>
              <div class="col-lg-2">
                <label class="invisible d-block">Sort</label>
                <button type="button" class="btn btn-primary" id="log-sort-btn" disabled=""><i class="fa fa-filter"></i> Filter</button>
              </div>
              <div class="col-lg-2">
                  <label class="invisible d-block">Export Excel</label>
                  <button class="btn btn-sm pnl-xls" style="color:white; background-color:green" data-param="container"><i class="fa fa-file-excel-o"></i>Export XLS</button>
              </div>
            </div>
        </div>
      </div>


    <div id="container">
        <h1 style="display:none">Daily Log Performance</h1>
        <div class="">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">All Operator</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-success revenue_usd"><span>27,544.30</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.21%</small></td>
                            <td class="text-success">39,113.55<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>31.08%</small></td>
                            <td class="text-success">31,673.19<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>22.25%</small></td>
                            <td class="text-success">27,317.64<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>17.90%</small></td>
                            <td class="text-success">24,364.57<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>31.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-success revenue_share_usd"><span>12,653.56</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>1.63%</small></td>
                            <td class="text-success">18,401.55<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>31.53%</small></td>
                            <td class="text-success">14,873.92<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>16.58%</small></td>
                            <td class="text-success">13,344.34<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>15.80%</small></td>
                            <td class="text-success">11,820.49<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>41.10%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-danger">30,568<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-37.75%</small></td>
                            <td class="text-success">50,397<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>14.66%</small></td>
                            <td class="text-success">44,359<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>5.59%</small></td>
                            <td class="text-success">63,502<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>78.07%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-danger">1,943.12<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-32.46%</small></td>
                            <td class="text-danger">3,516.62<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-7.43%</small></td>
                            <td class="text-success">3,521.39<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>51.29%</small></td>
                            <td class="text-success">2,757.30<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>339.18%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-success">7,911.27<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>1.75%</small></td>
                            <td class="text-success">6,863.62<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>16.32%</small></td>
                            <td class="text-danger">6,291.61<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-1.45%</small></td>
                            <td class="text-success">6,206.76<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>135.15%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
















































































































        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">airtelbd</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class=" revenue_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class=" revenue_share_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0.00<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0.00<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0.00<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">ais</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class=" revenue_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class=" revenue_share_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-97.71%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.11<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-96.69%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-success">-0.11<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>-96.69%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">axisss</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class=" revenue_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class=" revenue_share_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0.00<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">blink</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class=" revenue_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-60.32%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class=" revenue_share_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-60.32%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0.00<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">ccstrrp</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class=" revenue_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-success">31.11<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class=" revenue_share_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-success">28.00<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="text-success">1<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-success">16.82<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-success">2.66<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">cellcard</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class=" revenue_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">7,910.00<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                            <td class="text-success">1,845.67<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                            <td class="text-success">615.22<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                            <td class="text-success">151.70<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class=" revenue_share_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">3,955.00<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                            <td class="text-success">922.83<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                            <td class="text-success">307.61<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                            <td class="text-success">75.85<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">1,929.15<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                            <td class="text-success">450.14<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                            <td class="text-success">150.05<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                            <td class="text-success">37.00<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">ci-orange-linkit</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class=" revenue_usd"><span>0.55</span>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0.65<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>23.53%</small></td>
                            <td class="text-danger">0.50<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-4.86%</small></td>
                            <td class="text-danger">0.52<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-31.79%</small></td>
                            <td class="text-success">0.88<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>464.93%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class=" revenue_share_usd"><span>0.22</span>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0.26<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>23.53%</small></td>
                            <td class="text-danger">0.20<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-4.86%</small></td>
                            <td class="text-danger">0.21<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-31.79%</small></td>
                            <td class="text-success">0.35<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>464.93%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.14<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-14.16%</small></td>
                            <td class="text-danger">0.13<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-10.64%</small></td>
                            <td class="text-danger">0.15<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-33.33%</small></td>
                            <td class="text-success">0.25<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>420.99%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">cze-nth-linkit</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class=" revenue_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-success">0.05<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                            <td class="text-success">0.09<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class=" revenue_share_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-success">0.02<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                            <td class="text-success">0.04<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-success">0.01<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                            <td class="text-success">0.03<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">dtac</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-success revenue_usd"><span>46.37</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>7.66%</small></td>
                            <td class="text-success">51.35<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.96%</small></td>
                            <td class="text-danger">55.47<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-2.64%</small></td>
                            <td class="text-danger">58.90<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-24.11%</small></td>
                            <td class="text-danger">159.82<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-68.29%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-success revenue_share_usd"><span>16.23</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>7.66%</small></td>
                            <td class="text-success">17.97<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.96%</small></td>
                            <td class="text-danger">19.42<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-2.64%</small></td>
                            <td class="text-danger">20.62<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-24.11%</small></td>
                            <td class="text-danger">55.94<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-68.29%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-danger">76<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-64.50%</small></td>
                            <td class="text-success">200<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>212.80%</small></td>
                            <td class="text-success">130<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>112.48%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">7.21<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-83.10%</small></td>
                            <td class="text-success">41.03<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>162.34%</small></td>
                            <td class="text-success">34.99<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>134.09%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">9.99<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-31.63%</small></td>
                            <td class="text-success">7.42<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>-127.64%</small></td>
                            <td class="text-danger">-24.93<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-521.00%</small></td>
                            <td class="text-danger">8.75<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-65.03%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">eg-etisalat</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-success revenue_usd"><span>1.67</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>31.57%</small></td>
                            <td class="text-success">2.05<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>31.62%</small></td>
                            <td class="text-danger">1.77<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-15.57%</small></td>
                            <td class="text-danger">1.99<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-33.87%</small></td>
                            <td class="text-success">1.64<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>207.31%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-success revenue_share_usd"><span>0.70</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>31.57%</small></td>
                            <td class="text-success">0.86<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>31.62%</small></td>
                            <td class="text-danger">0.74<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-15.57%</small></td>
                            <td class="text-danger">0.84<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-33.87%</small></td>
                            <td class="text-success">0.69<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>207.31%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-93.68%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.01<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-98.17%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.48<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-2.02%</small></td>
                            <td class="text-danger">0.52<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-21.85%</small></td>
                            <td class="text-danger">0.62<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-35.32%</small></td>
                            <td class="text-success">0.52<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>-254.30%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">eg-orange</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-danger revenue_usd"><span>51.78</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-2.97%</small></td>
                            <td class="text-success">60.34<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>12.89%</small></td>
                            <td class="text-danger">58.86<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-10.32%</small></td>
                            <td class="text-danger">62.19<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-36.50%</small></td>
                            <td class="text-success">131.94<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>36.20%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-danger revenue_share_usd"><span>21.75</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-2.97%</small></td>
                            <td class="text-success">25.34<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>12.89%</small></td>
                            <td class="text-danger">24.72<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-10.32%</small></td>
                            <td class="text-danger">26.12<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-36.50%</small></td>
                            <td class="text-success">55.42<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>36.20%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">76<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-44.63%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">5.67<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-62.75%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">12.65<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-25.39%</small></td>
                            <td class="text-danger">17.20<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-17.95%</small></td>
                            <td class="text-danger">19.33<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-38.19%</small></td>
                            <td class="text-success">39.58<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>29.28%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">eg-vodafone</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class=" revenue_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">21.59<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-86.71%</small></td>
                            <td class="text-success">266.74<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>35.22%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class=" revenue_share_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">9.07<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-86.71%</small></td>
                            <td class="text-success">112.03<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>35.22%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">260<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-50.74%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">12.79<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-56.60%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">6.89<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-86.71%</small></td>
                            <td class="text-success">79.94<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>24.75%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">eg-we-linkit</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-danger revenue_usd"><span>0.32</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-55.56%</small></td>
                            <td class="text-success">0.45<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>16.67%</small></td>
                            <td class="text-success">0.40<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>13.26%</small></td>
                            <td class="text-success">0.25<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                            <td class="text-success">0.06<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class=" revenue_share_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0.00<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0.00<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">etisalat-knc</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class=" revenue_usd"><span>2.72</span>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">3.50<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-10.00%</small></td>
                            <td class="text-success">3.54<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>8.33%</small></td>
                            <td class="text-danger">3.39<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-37.08%</small></td>
                            <td class="text-danger">8.67<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-82.05%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class=" revenue_share_usd"><span>1.39</span>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">1.79<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-10.00%</small></td>
                            <td class="text-success">1.81<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>8.33%</small></td>
                            <td class="text-danger">1.73<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-37.08%</small></td>
                            <td class="text-danger">4.42<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-82.05%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">1<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>33.32%</small></td>
                            <td class="text-danger">1<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-9.08%</small></td>
                            <td class="text-danger">2<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-95.53%</small></td>
                            <td class="text-success">11<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>148.25%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0.06<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                            <td class="text-success">0.07<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>150.00%</small></td>
                            <td class="text-danger">0.28<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-96.40%</small></td>
                            <td class="text-danger">2.56<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-70.71%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.84<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-34.44%</small></td>
                            <td class="text-danger">1.04<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-0.95%</small></td>
                            <td class="text-success">0.82<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>-113.82%</small></td>
                            <td class="text-success">0.30<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>-104.80%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">gha-vodafone-linkit</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class=" revenue_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.61<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class=" revenue_share_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.31<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0.23<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">hti-natcom-linkit</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class=" revenue_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-success">0.00<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class=" revenue_share_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-success">0.00<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-success">0.00<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                            <td class="text-success">0.00<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">id-extravaganza-linkit </td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-danger revenue_usd"><span>4,237.44</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-2.85%</small></td>
                            <td class="text-success">4,277.36<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>7.05%</small></td>
                            <td class="text-success">4,193.13<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>194.09%</small></td>
                            <td class="text-success">1,872.97<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                            <td class="text-danger">461.83<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-danger revenue_share_usd"><span>1,186.48</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-2.85%</small></td>
                            <td class="text-success">1,197.66<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>7.05%</small></td>
                            <td class="text-success">1,174.08<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>194.09%</small></td>
                            <td class="text-success">524.43<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                            <td class="text-danger">129.31<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">716.77<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-11.02%</small></td>
                            <td class="text-success">811.37<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>182.28%</small></td>
                            <td class="text-success">366.27<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                            <td class="text-success">90.31<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">id-isat-pass</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-danger revenue_usd"><span>141.25</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-25.81%</small></td>
                            <td class="text-success">190.79<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>9.95%</small></td>
                            <td class="text-success">179.86<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>1.86%</small></td>
                            <td class="text-danger">197.81<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-20.87%</small></td>
                            <td class="text-success">240.69<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>24.63%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-danger revenue_share_usd"><span>56.50</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-25.81%</small></td>
                            <td class="text-success">76.32<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>9.95%</small></td>
                            <td class="text-success">71.94<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>1.86%</small></td>
                            <td class="text-danger">79.12<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-20.87%</small></td>
                            <td class="text-success">96.27<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>24.63%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">38.28<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-25.98%</small></td>
                            <td class="text-danger">49.26<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-6.38%</small></td>
                            <td class="text-danger">57.50<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-22.81%</small></td>
                            <td class="text-success">71.90<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>228.63%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">id-isat-waki</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-success revenue_usd"><span>229.22</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>8.89%</small></td>
                            <td class="text-success">261.58<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>11.50%</small></td>
                            <td class="text-success">239.94<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>1.38%</small></td>
                            <td class="text-danger">265.54<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-23.14%</small></td>
                            <td class="text-danger">342.73<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-1.73%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-success revenue_share_usd"><span>91.69</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>8.89%</small></td>
                            <td class="text-success">104.63<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>11.50%</small></td>
                            <td class="text-success">95.98<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>1.38%</small></td>
                            <td class="text-danger">106.21<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-23.14%</small></td>
                            <td class="text-danger">137.09<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-1.73%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">58.43<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-16.41%</small></td>
                            <td class="text-danger">66.95<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-5.08%</small></td>
                            <td class="text-danger">77.61<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-24.62%</small></td>
                            <td class="text-success">102.50<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>156.75%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">id-isat-yatta</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-danger revenue_usd"><span>293.28</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-1.77%</small></td>
                            <td class="text-success">338.53<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>7.45%</small></td>
                            <td class="text-success">329.71<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>3.83%</small></td>
                            <td class="text-danger">342.30<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-8.80%</small></td>
                            <td class="text-success">378.33<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>6.28%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-danger revenue_share_usd"><span>117.31</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-1.77%</small></td>
                            <td class="text-success">135.41<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>7.45%</small></td>
                            <td class="text-success">131.88<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>3.83%</small></td>
                            <td class="text-danger">136.92<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-8.80%</small></td>
                            <td class="text-success">151.33<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>6.28%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">472<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-76.88%</small></td>
                            <td class="text-success">1,262<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>665.82%</small></td>
                            <td class="text-success">388<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">24.57<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-86.96%</small></td>
                            <td class="text-success">118.78<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>498.28%</small></td>
                            <td class="text-success">36.66<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">75.69<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-19.38%</small></td>
                            <td class="text-success">67.81<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>-172.33%</small></td>
                            <td class="text-danger">-18.74<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-120.37%</small></td>
                            <td class="text-success">76.60<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>96.37%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">id-oxford-airpay</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class=" revenue_usd"><span>322.94</span>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">369.09<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>14.28%</small></td>
                            <td class="text-danger">333.77<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-3.55%</small></td>
                            <td class="text-danger">385.19<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-31.79%</small></td>
                            <td class="text-success">594.79<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>7,158.35%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class=" revenue_share_usd"><span>126.92</span>&nbsp;<small>0.00%</small>
                            </td>
                            <td class="text-success">145.05<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>14.28%</small></td>
                            <td class="text-danger">131.17<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-3.55%</small></td>
                            <td class="text-danger">151.38<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-31.79%</small></td>
                            <td class="text-success">233.75<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>7,158.35%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">79.62<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-23.50%</small></td>
                            <td class="text-danger">98.38<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-11.78%</small></td>
                            <td class="text-danger">121.07<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-33.47%</small></td>
                            <td class="text-success">198.40<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>4,291.99%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">id-smartfren-kb</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-danger revenue_usd"><span>12.93</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-1.46%</small></td>
                            <td class="text-danger">13.45<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-5.77%</small></td>
                            <td class="text-danger">16.50<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-17.54%</small></td>
                            <td class="text-danger">21.45<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-76.80%</small></td>
                            <td class="text-success">172.39<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>1,311.75%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-danger revenue_share_usd"><span>5.50</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-1.46%</small></td>
                            <td class="text-danger">5.72<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-5.77%</small></td>
                            <td class="text-danger">7.02<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-17.54%</small></td>
                            <td class="text-danger">9.12<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-76.80%</small></td>
                            <td class="text-success">73.30<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>1,311.75%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="text-success">1<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>2,903.00%</small></td>
                            <td class="text-danger">0<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-99.91%</small></td>
                            <td class="text-success">2,932<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>313.34%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-success">0.02<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                            <td class="text-danger">0.01<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-99.91%</small></td>
                            <td class="text-success">55.91<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>325.72%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">3.23<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-34.71%</small></td>
                            <td class="text-danger">5.39<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-22.75%</small></td>
                            <td class="text-danger">7.36<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-68.80%</small></td>
                            <td class="text-success">2.98<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>-132.09%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">id-smartfren-pass</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-success revenue_usd"><span>10.50</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>256.52%</small></td>
                            <td class="text-danger">9.68<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-18.73%</small></td>
                            <td class="text-danger">13.48<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-13.00%</small></td>
                            <td class="text-danger">15.93<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-71.69%</small></td>
                            <td class="text-success">201.30<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>833.88%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-success revenue_share_usd"><span>4.46</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>256.52%</small></td>
                            <td class="text-danger">4.12<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-18.73%</small></td>
                            <td class="text-danger">5.73<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-13.00%</small></td>
                            <td class="text-danger">6.77<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-71.69%</small></td>
                            <td class="text-success">85.59<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>833.88%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">1<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-55.72%</small></td>
                            <td class="text-danger">1<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-99.62%</small></td>
                            <td class="text-success">3,788<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>111.72%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.02<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-42.86%</small></td>
                            <td class="text-danger">0.03<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-99.65%</small></td>
                            <td class="text-success">70.92<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>57.57%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">2.18<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-47.40%</small></td>
                            <td class="text-danger">4.40<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-17.96%</small></td>
                            <td class="text-danger">5.43<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-52.74%</small></td>
                            <td class="text-success">-2.21<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>-94.21%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">id-smartfren-waki</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-danger revenue_usd"><span>45.70</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-3.25%</small></td>
                            <td class="text-danger">46.79<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-20.06%</small></td>
                            <td class="text-danger">65.96<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-34.16%</small></td>
                            <td class="text-danger">89.82<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-43.70%</small></td>
                            <td class="text-success">87.74<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-danger revenue_share_usd"><span>19.43</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-3.25%</small></td>
                            <td class="text-danger">19.90<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-20.06%</small></td>
                            <td class="text-danger">28.04<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-34.16%</small></td>
                            <td class="text-danger">38.19<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-43.70%</small></td>
                            <td class="text-success">37.31<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">3<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-14.29%</small></td>
                            <td class="text-danger">93<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-93.82%</small></td>
                            <td class="text-danger">1,370<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-38.06%</small></td>
                            <td class="text-success">1,840<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0.07<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>76.92%</small></td>
                            <td class="text-danger">1.22<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-94.96%</small></td>
                            <td class="text-danger">23.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-58.21%</small></td>
                            <td class="text-success">41.13<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">11.66<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-34.78%</small></td>
                            <td class="text-success">18.37<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>180.58%</small></td>
                            <td class="text-success">4.30<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>-169.34%</small></td>
                            <td class="text-success">-14.32<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">id-smartfren-yatta</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-success revenue_usd"><span>103.49</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>8.52%</small></td>
                            <td class="text-danger">111.88<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-0.96%</small></td>
                            <td class="text-danger">122.45<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-21.05%</small></td>
                            <td class="text-success">139.87<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>13.88%</small></td>
                            <td class="text-success">219.51<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>1,591.11%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-success revenue_share_usd"><span>44.00</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>8.52%</small></td>
                            <td class="text-danger">47.57<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-0.96%</small></td>
                            <td class="text-danger">52.07<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-21.05%</small></td>
                            <td class="text-success">59.47<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>13.88%</small></td>
                            <td class="text-success">93.34<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>1,591.11%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">509<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>21.25%</small></td>
                            <td class="text-danger">533<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-83.69%</small></td>
                            <td class="text-success">2,755<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>76.75%</small></td>
                            <td class="text-success">4,052<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>491.99%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">4.97<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>22.11%</small></td>
                            <td class="text-danger">5.22<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-87.22%</small></td>
                            <td class="text-success">40.99<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>26.37%</small></td>
                            <td class="text-success">78.33<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>495.71%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">22.23<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-37.06%</small></td>
                            <td class="text-success">34.72<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>162.57%</small></td>
                            <td class="text-danger">6.86<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-33.96%</small></td>
                            <td class="text-success">-3.08<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>-65.98%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">id-surat-sakit</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-success revenue_usd"><span>158.40</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>52.31%</small></td>
                            <td class="text-danger">320.23<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-12.44%</small></td>
                            <td class="text-danger">353.55<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-0.97%</small></td>
                            <td class="text-success">343.38<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>132.04%</small></td>
                            <td class="text-success">121.16<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-success revenue_share_usd"><span>150.48</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>52.31%</small></td>
                            <td class="text-danger">304.22<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-12.44%</small></td>
                            <td class="text-danger">335.87<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-0.97%</small></td>
                            <td class="text-success">326.21<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>132.04%</small></td>
                            <td class="text-success">115.10<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0.00<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">195.27<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-21.94%</small></td>
                            <td class="text-danger">236.28<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-3.24%</small></td>
                            <td class="text-success">233.02<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>130.21%</small></td>
                            <td class="text-success">82.42<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">id-telkomsel-mks</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-danger revenue_usd"><span>3,771.71</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-1.29%</small></td>
                            <td class="text-success">4,428.94<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>8.52%</small></td>
                            <td class="text-success">3,571.04<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>30.25%</small></td>
                            <td class="text-success">2,689.52<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>202.71%</small></td>
                            <td class="text-success">948.03<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>2,402.86%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-danger revenue_share_usd"><span>1,177.53</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-1.29%</small></td>
                            <td class="text-success">1,382.71<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>8.52%</small></td>
                            <td class="text-success">1,114.88<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>30.25%</small></td>
                            <td class="text-success">839.67<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>202.71%</small></td>
                            <td class="text-success">295.98<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>2,402.86%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">777.91<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-25.55%</small></td>
                            <td class="text-success">831.15<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>18.41%</small></td>
                            <td class="text-success">660.85<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>190.54%</small></td>
                            <td class="text-success">237.82<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>1,755.94%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">id-telkomsel-pgu</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class=" revenue_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0.00<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class=" revenue_share_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0.00<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">815<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-19.96%</small></td>
                            <td class="text-success">652<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>13,389.06%</small></td>
                            <td class="text-danger">228<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-43.04%</small></td>
                            <td class="">267&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">105.73<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-20.15%</small></td>
                            <td class="text-success">84.67<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>13,660.57%</small></td>
                            <td class="text-danger">29.38<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-22.68%</small></td>
                            <td class="">31.34&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">-105.73<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>-20.15%</small></td>
                            <td class="text-danger">-84.67<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>13,660.57%</small></td>
                            <td class="text-success">-29.38<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>-22.68%</small></td>
                            <td class="text-success">-31.34<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">id-tri-yatta</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-danger revenue_usd"><span>17.41</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-3.55%</small></td>
                            <td class="text-success">19.40<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>13.35%</small></td>
                            <td class="text-danger">13.21<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-31.86%</small></td>
                            <td class="text-danger">15.26<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-5.70%</small></td>
                            <td class="text-success">12.58<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-danger revenue_share_usd"><span>6.84</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-3.55%</small></td>
                            <td class="text-success">7.62<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>13.35%</small></td>
                            <td class="text-danger">5.19<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-31.86%</small></td>
                            <td class="text-danger">6.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-5.70%</small></td>
                            <td class="text-success">4.94<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-99.94%</small></td>
                            <td class="">113&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.01<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-99.90%</small></td>
                            <td class="">8.33&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">3.85<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-20.48%</small></td>
                            <td class="text-danger">3.35<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-38.84%</small></td>
                            <td class="text-success">4.18<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>-160.92%</small></td>
                            <td class="text-success">-4.80<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">id-xl-kb</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class=" revenue_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">17.82<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-50.86%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class=" revenue_share_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">7.13<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-50.86%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-success">5.38<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>35.18%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">id-xl-pass</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-danger revenue_usd"><span>120.70</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-41.68%</small></td>
                            <td class="text-success">184.06<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>13.19%</small></td>
                            <td class="text-danger">166.08<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-1.15%</small></td>
                            <td class="text-success">168.94<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.80%</small></td>
                            <td class="text-success">164.92<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>9.42%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-danger revenue_share_usd"><span>48.28</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-41.68%</small></td>
                            <td class="text-success">73.63<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>13.19%</small></td>
                            <td class="text-danger">66.43<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-1.15%</small></td>
                            <td class="text-success">67.58<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.80%</small></td>
                            <td class="text-success">65.97<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>9.42%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-74.89%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">38.12<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-21.33%</small></td>
                            <td class="text-danger">45.59<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-8.95%</small></td>
                            <td class="text-danger">49.04<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-1.80%</small></td>
                            <td class="text-success">50.02<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>166.70%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">id-xl-waki</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-danger revenue_usd"><span>172.42</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-15.65%</small></td>
                            <td class="text-success">216.19<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>19.75%</small></td>
                            <td class="text-danger">192.28<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-1.11%</small></td>
                            <td class="text-success">193.90<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>1.88%</small></td>
                            <td class="text-success">183.06<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>8.09%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-danger revenue_share_usd"><span>68.97</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-15.65%</small></td>
                            <td class="text-success">86.48<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>19.75%</small></td>
                            <td class="text-danger">76.91<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-1.11%</small></td>
                            <td class="text-success">77.56<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>1.88%</small></td>
                            <td class="text-success">73.23<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>8.09%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">47.73<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-11.28%</small></td>
                            <td class="text-danger">53.41<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-7.83%</small></td>
                            <td class="text-danger">56.48<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-0.41%</small></td>
                            <td class="text-success">55.50<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>154.17%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">id-xl-yatta</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-danger revenue_usd"><span>527.62</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-8.50%</small></td>
                            <td class="text-success">631.26<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>12.41%</small></td>
                            <td class="text-success">586.48<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.12%</small></td>
                            <td class="text-danger">589.38<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-1.35%</small></td>
                            <td class="text-success">651.28<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>2.25%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-danger revenue_share_usd"><span>211.05</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-8.50%</small></td>
                            <td class="text-success">252.50<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>12.41%</small></td>
                            <td class="text-success">234.59<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.12%</small></td>
                            <td class="text-danger">235.75<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-1.35%</small></td>
                            <td class="text-success">260.51<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>2.25%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">138.42<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-17.29%</small></td>
                            <td class="text-danger">163.18<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-6.52%</small></td>
                            <td class="text-danger">171.77<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-3.52%</small></td>
                            <td class="text-success">200.67<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>153.31%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">indosat</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-danger revenue_usd"><span>311.04</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-2.29%</small></td>
                            <td class="text-success">349.33<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>12.76%</small></td>
                            <td class="text-success">319.33<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>7.36%</small></td>
                            <td class="text-danger">319.62<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-3.64%</small></td>
                            <td class="text-danger">361.35<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-22.83%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-danger revenue_share_usd"><span>124.42</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-2.29%</small></td>
                            <td class="text-success">139.73<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>12.76%</small></td>
                            <td class="text-success">127.73<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>7.36%</small></td>
                            <td class="text-danger">127.85<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-3.64%</small></td>
                            <td class="text-danger">144.54<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-22.83%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                            <td class="text-danger">169<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-32.99%</small></td>
                            <td class="text-success">388<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>83.29%</small></td>
                            <td class="text-success">163<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>882.60%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">13.87<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-4.99%</small></td>
                            <td class="text-success">32.23<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>40.13%</small></td>
                            <td class="text-success">14.63<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>1,327.40%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">79.49<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-21.77%</small></td>
                            <td class="text-danger">82.69<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-0.34%</small></td>
                            <td class="text-danger">69.87<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-18.55%</small></td>
                            <td class="text-success">104.71<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>162.75%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">irq-zain-linkit</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class=" revenue_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-success">34.96<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>13.05%</small></td>
                            <td class="text-success">25.05<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>104,607.69%</small></td>
                            <td class="text-danger">6.18<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class=" revenue_share_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-success">9.96<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>13.05%</small></td>
                            <td class="text-success">7.14<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>104,607.69%</small></td>
                            <td class="text-danger">1.76<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">329<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>103.53%</small></td>
                            <td class="text-success">149<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>40.06%</small></td>
                            <td class="text-success">91<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                            <td class="text-success">22<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">102.31<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>89.66%</small></td>
                            <td class="text-danger">48.54<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-4.86%</small></td>
                            <td class="text-success">35.84<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                            <td class="text-success">8.84<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">-102.31<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>101.44%</small></td>
                            <td class="text-success">-41.37<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>-7.40%</small></td>
                            <td class="text-danger">-30.70<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-627,965.27%</small></td>
                            <td class="text-success">-7.57<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">jazz</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-success revenue_usd"><span>2,109.09</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>9.63%</small></td>
                            <td class="text-danger">1,961.07<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-3.83%</small></td>
                            <td class="text-danger">2,052.32<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-19.17%</small></td>
                            <td class="text-success">2,503.53<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>2.34%</small></td>
                            <td class="text-success">2,186.66<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>55.99%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-success revenue_share_usd"><span>1,476.36</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>9.63%</small></td>
                            <td class="text-danger">1,372.75<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-3.83%</small></td>
                            <td class="text-danger">1,436.63<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-19.17%</small></td>
                            <td class="text-success">1,752.47<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>2.34%</small></td>
                            <td class="text-success">1,530.66<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>55.99%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-danger">565<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-95.52%</small></td>
                            <td class="text-success">7,117<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>108.14%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">5.51<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-95.52%</small></td>
                            <td class="text-success">72.15<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>63.84%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">555.57<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-36.47%</small></td>
                            <td class="text-danger">814.58<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-25.45%</small></td>
                            <td class="text-success">1,048.92<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>12.83%</small></td>
                            <td class="text-success">869.34<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>328.68%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">jazz-ev</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class=" revenue_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">742.79<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-45.98%</small></td>
                            <td class="text-danger">1,263.47<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-18.12%</small></td>
                            <td class="text-success">1,519.07<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>5.58%</small></td>
                            <td class="text-success">1,248.75<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>79.82%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class=" revenue_share_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">445.67<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-45.98%</small></td>
                            <td class="text-danger">758.08<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-18.12%</small></td>
                            <td class="text-success">911.44<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>5.58%</small></td>
                            <td class="text-success">749.25<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>79.82%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-danger">0<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-98.70%</small></td>
                            <td class="text-danger">1,511<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-81.99%</small></td>
                            <td class="text-success">4,018<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>1,139.20%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-danger">14.73<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-82.03%</small></td>
                            <td class="text-success">42.19<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>442.14%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">319.96<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-46.20%</small></td>
                            <td class="text-danger">546.83<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-18.32%</small></td>
                            <td class="text-success">643.96<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>18.73%</small></td>
                            <td class="text-success">490.26<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>341.17%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">kb-tri-telesat</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-danger revenue_usd"><span>1.73</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-84.92%</small></td>
                            <td class="text-danger">6.92<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-26.79%</small></td>
                            <td class="text-danger">9.64<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-17.86%</small></td>
                            <td class="text-danger">10.55<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-35.51%</small></td>
                            <td class="text-success">17.91<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>92.26%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-danger revenue_share_usd"><span>0.86</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-84.92%</small></td>
                            <td class="text-danger">3.46<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-26.79%</small></td>
                            <td class="text-danger">4.82<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-17.86%</small></td>
                            <td class="text-danger">5.28<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-35.51%</small></td>
                            <td class="text-success">8.96<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>92.26%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="text-success">14<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0.73<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">2.13<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-37.28%</small></td>
                            <td class="text-danger">3.39<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-19.84%</small></td>
                            <td class="text-danger">3.77<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-35.97%</small></td>
                            <td class="text-success">5.61<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>91.02%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">kb-tsel-telesat</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-success revenue_usd"><span>618.88</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>2.22%</small></td>
                            <td class="text-success">734.72<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>6.63%</small></td>
                            <td class="text-success">682.16<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>4.98%</small></td>
                            <td class="text-success">645.45<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>30.59%</small></td>
                            <td class="text-success">398.29<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>237.19%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-success revenue_share_usd"><span>433.22</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>2.22%</small></td>
                            <td class="text-success">514.30<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>6.63%</small></td>
                            <td class="text-success">477.51<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>4.98%</small></td>
                            <td class="text-success">451.81<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>30.59%</small></td>
                            <td class="text-success">278.80<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>237.19%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">1,617<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-45.07%</small></td>
                            <td class="text-success">2,445<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>7.24%</small></td>
                            <td class="text-success">2,241<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>67.70%</small></td>
                            <td class="text-success">1,271<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">82.56<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-51.67%</small></td>
                            <td class="text-danger">177.87<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-34.14%</small></td>
                            <td class="text-success">200.29<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>22.65%</small></td>
                            <td class="text-success">129.91<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">192.86<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-14.17%</small></td>
                            <td class="text-success">179.56<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>74.50%</small></td>
                            <td class="text-success">158.82<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>31.90%</small></td>
                            <td class="text-success">89.12<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>122.81%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">ksa-mobily</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-danger revenue_usd"><span>29.53</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-15.27%</small></td>
                            <td class="text-danger">58.30<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-72.33%</small></td>
                            <td class="text-danger">138.03<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-1.31%</small></td>
                            <td class="text-danger">145.13<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-38.29%</small></td>
                            <td class="text-success">208.51<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>238,304.17%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-danger revenue_share_usd"><span>14.76</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-15.27%</small></td>
                            <td class="text-danger">29.15<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-72.33%</small></td>
                            <td class="text-danger">69.02<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-1.31%</small></td>
                            <td class="text-danger">72.57<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-38.29%</small></td>
                            <td class="text-success">104.25<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>238,304.17%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">78<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-21.97%</small></td>
                            <td class="text-success">134<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>446.34%</small></td>
                            <td class="text-success">53<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>245.40%</small></td>
                            <td class="text-success">59<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">45.11<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-23.04%</small></td>
                            <td class="text-success">79.65<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>414.41%</small></td>
                            <td class="text-success">31.71<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>5.33%</small></td>
                            <td class="text-success">70.13<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">-27.67<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-223.02%</small></td>
                            <td class="text-danger">-27.67<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-172.09%</small></td>
                            <td class="text-danger">23.79<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-60.65%</small></td>
                            <td class="text-success">10.01<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>31,238.97%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">ksa-stc</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class=" revenue_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-danger">22.20<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-71.03%</small></td>
                            <td class="text-success">66.96<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class=" revenue_share_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-danger">11.10<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-71.03%</small></td>
                            <td class="text-success">33.48<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">10<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>86.11%</small></td>
                            <td class="text-success">20<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                            <td class="text-success">7<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>80.23%</small></td>
                            <td class="text-success">17<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">7.06<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>136.02%</small></td>
                            <td class="text-success">17.53<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                            <td class="text-danger">5.84<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-21.73%</small></td>
                            <td class="text-success">29.07<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">-7.06<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>136.02%</small></td>
                            <td class="text-danger">-17.53<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-271.54%</small></td>
                            <td class="text-danger">2.71<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-87.72%</small></td>
                            <td class="text-danger">-3.29<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">ksa-virgin-linkit</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-danger revenue_usd"><span>5.05</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-55.81%</small></td>
                            <td class="text-success">6.12<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>28.80%</small></td>
                            <td class="text-danger">5.80<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-13.72%</small></td>
                            <td class="text-danger">7.16<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-65.23%</small></td>
                            <td class="text-success">7.08<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-danger revenue_share_usd"><span>3.79</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-55.81%</small></td>
                            <td class="text-success">4.59<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>28.80%</small></td>
                            <td class="text-danger">4.35<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-13.72%</small></td>
                            <td class="text-danger">5.37<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-65.23%</small></td>
                            <td class="text-success">5.31<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0.09<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">2.40<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-6.51%</small></td>
                            <td class="text-danger">2.92<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-19.53%</small></td>
                            <td class="text-danger">3.80<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-65.86%</small></td>
                            <td class="text-danger">3.72<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">ksa-zein</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-success revenue_usd"><span>6.65</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>47.06%</small></td>
                            <td class="text-danger">8.97<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-67.31%</small></td>
                            <td class="text-success">17.78<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>3.46%</small></td>
                            <td class="text-danger">18.41<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-39.69%</small></td>
                            <td class="text-success">20.00<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>685,775.26%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-success revenue_share_usd"><span>2.66</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>47.06%</small></td>
                            <td class="text-danger">3.59<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-67.31%</small></td>
                            <td class="text-success">7.11<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>3.46%</small></td>
                            <td class="text-danger">7.37<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-39.69%</small></td>
                            <td class="text-success">8.00<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>685,775.26%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">34<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-14.70%</small></td>
                            <td class="text-success">31<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>4,800.25%</small></td>
                            <td class="text-success">11<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>21.02%</small></td>
                            <td class="text-success">9<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">28.62<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>15.00%</small></td>
                            <td class="text-success">20.25<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>2,496.11%</small></td>
                            <td class="text-danger">7.01<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-58.56%</small></td>
                            <td class="text-success">12.80<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">-26.68<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>59.67%</small></td>
                            <td class="text-danger">-15.12<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-448.32%</small></td>
                            <td class="text-success">-1.58<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>-79.80%</small></td>
                            <td class="text-danger">-6.85<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-781,702.96%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">lao-tplus-linkit</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-danger revenue_usd"><span>24.79</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-8.75%</small></td>
                            <td class="text-success">15.69<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>1,417.91%</small></td>
                            <td class="text-danger">10.39<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-34.82%</small></td>
                            <td class="text-success">14.56<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>66.99%</small></td>
                            <td class="text-success">6.86<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-danger revenue_share_usd"><span>17.35</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-8.75%</small></td>
                            <td class="text-success">10.98<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>1,417.91%</small></td>
                            <td class="text-danger">7.27<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-34.82%</small></td>
                            <td class="text-success">10.19<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>66.99%</small></td>
                            <td class="text-success">4.80<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">2<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-83.33%</small></td>
                            <td class="text-danger">3<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-68.63%</small></td>
                            <td class="text-danger">7<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-55.56%</small></td>
                            <td class="text-success">5<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.52<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-83.72%</small></td>
                            <td class="text-danger">0.88<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-68.12%</small></td>
                            <td class="text-danger">2.02<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-49.13%</small></td>
                            <td class="text-success">1.47<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">3.98<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>-250.92%</small></td>
                            <td class="text-danger">3.86<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-35.77%</small></td>
                            <td class="text-success">5.67<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>578.04%</small></td>
                            <td class="text-success">2.22<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">linkit-rbt-isat</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class=" revenue_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-success">123.41<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>3,150.64%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class=" revenue_share_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-success">49.36<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>3,150.64%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-success">35.54<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">linkit-tsel-telesat</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-danger revenue_usd"><span>418.82</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-11.16%</small></td>
                            <td class="text-danger">433.46<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-9.31%</small></td>
                            <td class="text-success">426.10<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>28.44%</small></td>
                            <td class="text-success">322.72<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>1,156.42%</small></td>
                            <td class="text-success">85.91<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-danger revenue_share_usd"><span>209.41</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-11.16%</small></td>
                            <td class="text-danger">216.73<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-9.31%</small></td>
                            <td class="text-success">213.05<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>28.44%</small></td>
                            <td class="text-success">161.36<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>1,156.42%</small></td>
                            <td class="text-success">42.95<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">1,454<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-59.82%</small></td>
                            <td class="text-success">3,209<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>32.62%</small></td>
                            <td class="text-success">2,663<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>448.32%</small></td>
                            <td class="">777&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">71.22<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-62.34%</small></td>
                            <td class="text-danger">232.55<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-22.61%</small></td>
                            <td class="text-success">269.22<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>381.63%</small></td>
                            <td class="">80.17&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">46.37<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>-371.63%</small></td>
                            <td class="text-success">-88.12<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>-51.33%</small></td>
                            <td class="text-danger">-156.03<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>234.47%</small></td>
                            <td class="text-success">-49.98<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">lka-dialog-dotjo</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class=" revenue_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-99.99%</small></td>
                            <td class="text-success">2.84<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class=" revenue_share_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-99.99%</small></td>
                            <td class="text-success">1.06<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-success">22<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-success">4.82<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0.00<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-danger">-4.05<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">ltc</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-danger revenue_usd"><span>30.94</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-6.07%</small></td>
                            <td class="text-success">19.76<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>801.76%</small></td>
                            <td class="text-danger">13.12<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-32.48%</small></td>
                            <td class="text-success">18.30<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>45.21%</small></td>
                            <td class="text-danger">9.49<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-95.41%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-danger revenue_share_usd"><span>12.38</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-6.07%</small></td>
                            <td class="text-success">7.90<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>801.76%</small></td>
                            <td class="text-danger">5.25<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-32.48%</small></td>
                            <td class="text-success">7.32<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>45.21%</small></td>
                            <td class="text-danger">3.79<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-95.41%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">2<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-79.25%</small></td>
                            <td class="text-danger">2<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-68.26%</small></td>
                            <td class="text-danger">6<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-65.17%</small></td>
                            <td class="text-success">6<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>1,721.02%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.52<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-76.67%</small></td>
                            <td class="text-danger">0.70<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-70.34%</small></td>
                            <td class="text-danger">1.92<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-63.16%</small></td>
                            <td class="text-success">1.82<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>3,663.79%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">2.49<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>-255.35%</small></td>
                            <td class="text-danger">2.47<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-25.08%</small></td>
                            <td class="text-success">3.18<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>-305.35%</small></td>
                            <td class="text-danger">0.89<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-95.65%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">metfone</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class=" revenue_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class=" revenue_share_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">5<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-63.70%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">1.30<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-39.85%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-success">-1.30<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>-39.85%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">metfone-pax</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-danger revenue_usd"><span>54.55</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-0.27%</small></td>
                            <td class="text-success">65.31<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>6.76%</small></td>
                            <td class="text-danger">62.71<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-10.63%</small></td>
                            <td class="text-danger">73.70<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-33.97%</small></td>
                            <td class="text-success">77.02<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>5,135.13%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-danger revenue_share_usd"><span>27.28</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-0.27%</small></td>
                            <td class="text-success">32.65<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>6.76%</small></td>
                            <td class="text-danger">31.36<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-10.63%</small></td>
                            <td class="text-danger">36.85<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-33.97%</small></td>
                            <td class="text-success">38.51<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>5,135.13%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-success">93<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-success">17.16<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">11.14<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-20.07%</small></td>
                            <td class="text-danger">13.41<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-16.08%</small></td>
                            <td class="text-success">16.50<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>242.60%</small></td>
                            <td class="text-success">0.89<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>67.83%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">mm-mytel-linkit</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-success revenue_usd"><span>201.40</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>1.09%</small></td>
                            <td class="text-success">232.78<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>10.82%</small></td>
                            <td class="text-success">213.41<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>3.97%</small></td>
                            <td class="text-success">205.79<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>15.95%</small></td>
                            <td class="text-success">114.86<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>67,296.70%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-success revenue_share_usd"><span>100.70</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>1.09%</small></td>
                            <td class="text-success">116.39<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>10.82%</small></td>
                            <td class="text-success">106.70<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>3.97%</small></td>
                            <td class="text-success">102.90<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>15.95%</small></td>
                            <td class="text-success">57.43<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>67,296.70%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">44<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-4.39%</small></td>
                            <td class="text-danger">50<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-29.74%</small></td>
                            <td class="text-danger">60<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-38.08%</small></td>
                            <td class="text-success">49<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">7.58<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-3.77%</small></td>
                            <td class="text-danger">8.67<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-41.38%</small></td>
                            <td class="text-danger">11.89<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-37.92%</small></td>
                            <td class="text-success">9.55<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">63.05<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-19.24%</small></td>
                            <td class="text-success">72.90<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>5.36%</small></td>
                            <td class="text-success">70.39<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>31.68%</small></td>
                            <td class="text-success">36.98<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>51,305.78%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">mobifone</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class=" revenue_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-success">21.29<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class=" revenue_share_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-success">6.39<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-success">18<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>192.38%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-success">3.39<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>120.77%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-success">1.90<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>-326.04%</small></td>
                            <td class="text-danger">-0.89<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>300.57%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">mtnghana</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class=" revenue_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class=" revenue_share_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">my maxis</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class=" revenue_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class=" revenue_share_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">my-gtmh-linkit</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class=" revenue_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-danger">9.14<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-37.89%</small></td>
                            <td class="text-danger">33.25<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-73.49%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class=" revenue_share_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-danger">0.64<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-37.89%</small></td>
                            <td class="text-danger">2.33<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-73.49%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="text-success">3<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0.77<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-danger">0.52<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-37.85%</small></td>
                            <td class="text-danger">3.40<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-71.66%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">nga-mtn-finklasic</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-danger revenue_usd"><span>79.16</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-9.92%</small></td>
                            <td class="text-danger">82.56<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-13.39%</small></td>
                            <td class="text-success">89.82<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>65.93%</small></td>
                            <td class="text-success">63.49<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>22.36%</small></td>
                            <td class="text-success">32.37<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>1,188,754.55%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-danger revenue_share_usd"><span>21.37</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-9.92%</small></td>
                            <td class="text-danger">22.29<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-13.39%</small></td>
                            <td class="text-success">24.25<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>65.93%</small></td>
                            <td class="text-success">17.14<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>22.36%</small></td>
                            <td class="text-success">8.74<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>1,188,754.55%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-danger">92<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-72.35%</small></td>
                            <td class="text-success">191<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>13.87%</small></td>
                            <td class="text-success">109<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-danger">9.62<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-77.31%</small></td>
                            <td class="text-danger">25.78<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-21.96%</small></td>
                            <td class="text-success">19.80<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">9.12<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-50.71%</small></td>
                            <td class="text-success">6.27<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>-119.71%</small></td>
                            <td class="text-success">-13.92<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>-39.17%</small></td>
                            <td class="text-danger">-13.60<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-1,710,698.74%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">nor-all-linkit</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class=" revenue_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">7.71<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>1,000.00%</small></td>
                            <td class="text-danger">1.96<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-76.47%</small></td>
                            <td class="text-success">4.52<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>8,200.00%</small></td>
                            <td class="text-success">1.13<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class=" revenue_share_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">4.62<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>1,000.00%</small></td>
                            <td class="text-danger">1.18<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-76.47%</small></td>
                            <td class="text-success">2.71<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>8,200.00%</small></td>
                            <td class="text-success">0.68<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-danger">0<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-97.83%</small></td>
                            <td class="text-success">1<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>5,105.41%</small></td>
                            <td class="text-success">0<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-success">1.52<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                            <td class="text-success">0.37<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">3.36<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>988.89%</small></td>
                            <td class="text-success">0.86<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>-241.72%</small></td>
                            <td class="text-success">0.43<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>1,737.95%</small></td>
                            <td class="text-danger">0.11<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">omn-omantel-linkit</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-danger revenue_usd"><span>809.12</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-10.22%</small></td>
                            <td class="text-success">1,037.51<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>84.13%</small></td>
                            <td class="text-success">826.21<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>4.76%</small></td>
                            <td class="text-success">801.38<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>25.17%</small></td>
                            <td class="text-success">629.09<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-danger revenue_share_usd"><span>323.65</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-10.22%</small></td>
                            <td class="text-success">415.00<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>84.13%</small></td>
                            <td class="text-success">330.48<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>4.76%</small></td>
                            <td class="text-success">320.55<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>25.17%</small></td>
                            <td class="text-success">251.64<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">97<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>4.47%</small></td>
                            <td class="text-success">114<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>2.59%</small></td>
                            <td class="text-success">120<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>64.11%</small></td>
                            <td class="text-success">89<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">114.88<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-32.44%</small></td>
                            <td class="text-danger">195.51<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-9.80%</small></td>
                            <td class="text-success">239.02<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>33.90%</small></td>
                            <td class="text-success">200.81<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">140.72<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>870.22%</small></td>
                            <td class="text-danger">106.89<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-37.39%</small></td>
                            <td class="text-danger">120.21<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-11.68%</small></td>
                            <td class="text-success">99.85<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">omn-ooredoo-linkit</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-danger revenue_usd"><span>71.78</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-30.04%</small></td>
                            <td class="text-danger">102.77<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-4.69%</small></td>
                            <td class="text-danger">117.15<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-22.67%</small></td>
                            <td class="text-success">138.06<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>124.77%</small></td>
                            <td class="text-success">49.19<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-danger revenue_share_usd"><span>27.28</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-30.04%</small></td>
                            <td class="text-danger">39.05<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-4.69%</small></td>
                            <td class="text-danger">44.52<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-22.67%</small></td>
                            <td class="text-success">52.46<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>124.77%</small></td>
                            <td class="text-success">18.69<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-danger">23<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-55.80%</small></td>
                            <td class="text-danger">43<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-6.32%</small></td>
                            <td class="text-success">22<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-danger">37.42<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-57.91%</small></td>
                            <td class="text-danger">77.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-18.63%</small></td>
                            <td class="text-success">42.32<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">23.38<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>-269.73%</small></td>
                            <td class="text-success">-5.90<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>-86.99%</small></td>
                            <td class="text-success">-38.02<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>-50.61%</small></td>
                            <td class="text-success">-28.35<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">ooredoo</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class=" revenue_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class=" revenue_share_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-danger">33<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-39.43%</small></td>
                            <td class="text-success">21<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0.00<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0.00<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-success">-59.73<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>-36.62%</small></td>
                            <td class="text-danger">-37.97<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">pass-rbt-isat</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class=" revenue_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="">51.45&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class=" revenue_share_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="">18.01&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0.00<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-danger">12.97<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">pass-tri-telesat</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-success revenue_usd"><span>19.46</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                            <td class="text-danger">11.79<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-31.87%</small></td>
                            <td class="text-danger">18.01<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-16.84%</small></td>
                            <td class="text-success">19.50<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>20.02%</small></td>
                            <td class="text-success">18.17<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>105.26%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-success revenue_share_usd"><span>9.73</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                            <td class="text-danger">5.89<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-31.87%</small></td>
                            <td class="text-danger">9.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-16.84%</small></td>
                            <td class="text-success">9.75<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>20.02%</small></td>
                            <td class="text-success">9.09<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>105.26%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-success">4<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-success">0.34<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">2.24<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-63.99%</small></td>
                            <td class="text-danger">6.02<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-22.82%</small></td>
                            <td class="text-success">6.87<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>43.29%</small></td>
                            <td class="text-success">6.05<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>116.92%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">pass-tsel-telesat</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-success revenue_usd"><span>1,056.13</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>6.57%</small></td>
                            <td class="text-danger">1,240.12<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-2.57%</small></td>
                            <td class="text-success">1,207.02<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>18.94%</small></td>
                            <td class="text-danger">1,095.41<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-0.55%</small></td>
                            <td class="text-success">922.83<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>495.21%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-success revenue_share_usd"><span>739.29</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>6.57%</small></td>
                            <td class="text-danger">868.08<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-2.57%</small></td>
                            <td class="text-success">844.91<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>18.94%</small></td>
                            <td class="text-danger">766.79<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-0.55%</small></td>
                            <td class="text-success">645.98<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>495.21%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">3,135<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-57.55%</small></td>
                            <td class="text-success">7,120<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>47.97%</small></td>
                            <td class="text-success">4,763<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>173.25%</small></td>
                            <td class="text-success">2,397<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>278.23%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">134.61<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-61.03%</small></td>
                            <td class="text-success">498.55<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>17.40%</small></td>
                            <td class="text-success">393.15<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>80.94%</small></td>
                            <td class="text-success">237.91<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>401.80%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">345.17<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-10.39%</small></td>
                            <td class="text-danger">140.13<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-11.23%</small></td>
                            <td class="text-danger">217.57<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-47.57%</small></td>
                            <td class="text-success">274.72<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>2,118.66%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">ph-globe</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class=" revenue_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.07<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-45.45%</small></td>
                            <td class="">0.12&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.12<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-66.48%</small></td>
                            <td class="text-danger">4.65<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-81.94%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class=" revenue_share_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.03<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-45.45%</small></td>
                            <td class="">0.05&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.05<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-66.48%</small></td>
                            <td class="text-danger">1.86<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-81.94%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">14<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-6.21%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">3.30<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-16.85%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.03<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-45.46%</small></td>
                            <td class="">0.04&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.04<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-64.60%</small></td>
                            <td class="text-danger">-1.76<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-183.48%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">ph-smart-zed</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-success revenue_usd"><span>22.55</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>126.32%</small></td>
                            <td class="text-success">23.10<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>3.24%</small></td>
                            <td class="text-danger">22.52<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-17.18%</small></td>
                            <td class="text-danger">28.69<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-44.95%</small></td>
                            <td class="text-danger">38.96<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-success revenue_share_usd"><span>11.27</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>126.32%</small></td>
                            <td class="text-success">11.55<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>3.24%</small></td>
                            <td class="text-danger">11.26<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-17.18%</small></td>
                            <td class="text-danger">14.35<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-44.95%</small></td>
                            <td class="text-danger">19.48<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                            <td class="text-danger">0<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-83.36%</small></td>
                            <td class="text-danger">79<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0.01<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-83.19%</small></td>
                            <td class="text-danger">7.53<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">5.54<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-31.04%</small></td>
                            <td class="text-danger">7.39<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-22.99%</small></td>
                            <td class="text-danger">9.93<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-46.00%</small></td>
                            <td class="text-danger">6.20<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">pk-mobilink-noetic</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class=" revenue_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class=" revenue_share_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0.00<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">pk-telenor-linkit</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-danger revenue_usd"><span>68.34</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-6.19%</small></td>
                            <td class="text-danger">68.05<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-3.70%</small></td>
                            <td class="text-success">70.44<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>63.59%</small></td>
                            <td class="text-success">42.45<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>14,852.37%</small></td>
                            <td class="text-danger">10.54<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-danger revenue_share_usd"><span>34.17</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-6.19%</small></td>
                            <td class="text-danger">34.02<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-3.70%</small></td>
                            <td class="text-success">35.22<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>63.59%</small></td>
                            <td class="text-success">21.22<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>14,852.37%</small></td>
                            <td class="text-danger">5.27<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">3,004<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-14.32%</small></td>
                            <td class="text-success">2,222<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                            <td class="text-success">741<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                            <td class="">183&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">78.07<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-14.38%</small></td>
                            <td class="text-success">57.76<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                            <td class="text-success">19.25<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                            <td class="">4.75&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">-58.90<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>-10.37%</small></td>
                            <td class="text-danger">-33.60<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-315.80%</small></td>
                            <td class="text-danger">-4.34<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-4,319.00%</small></td>
                            <td class="">-1.04&nbsp;<small>0.00%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">pk-telenor-noetic</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class=" revenue_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-97.46%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class=" revenue_share_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-97.46%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0.00<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0.00<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0.00<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0.00<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">pk-ufone-noetic</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class=" revenue_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-78.45%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class=" revenue_share_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-78.45%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0.00<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0.00<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0.00<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-49.77%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">pk-warid-noetic</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class=" revenue_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-89.28%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class=" revenue_share_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-89.28%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0.00<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0.00<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0.00<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-39.96%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">pk-zong-noetic</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class=" revenue_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-91.18%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class=" revenue_share_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-91.18%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0.00<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0.00<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0.00<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">pol-orange-linkit</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-success revenue_usd"><span>21.08</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                            <td class="text-danger">17.69<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-6.00%</small></td>
                            <td class="text-danger">14.93<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-23.42%</small></td>
                            <td class="text-success">24.36<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>757.73%</small></td>
                            <td class="text-danger">6.71<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-success revenue_share_usd"><span>10.54</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                            <td class="text-danger">8.84<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-6.00%</small></td>
                            <td class="text-danger">7.46<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-23.42%</small></td>
                            <td class="text-success">12.18<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>757.73%</small></td>
                            <td class="text-danger">3.35<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-success">5<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>402.24%</small></td>
                            <td class="">1&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-success">15.47<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>428.89%</small></td>
                            <td class="">4.54&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">4.92<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-28.08%</small></td>
                            <td class="text-danger">5.01<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-22.32%</small></td>
                            <td class="text-danger">-7.01<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>263.17%</small></td>
                            <td class="">-2.20&nbsp;<small>0.00%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">pol-plus-linkit</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-danger revenue_usd"><span>239.75</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-3.19%</small></td>
                            <td class="text-success">259.32<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>12.21%</small></td>
                            <td class="text-success">220.86<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>115.88%</small></td>
                            <td class="text-success">138.43<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>472,800.01%</small></td>
                            <td class="text-danger">34.14<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-danger revenue_share_usd"><span>119.87</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-3.19%</small></td>
                            <td class="text-success">129.66<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>12.21%</small></td>
                            <td class="text-success">110.43<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>115.88%</small></td>
                            <td class="text-success">69.22<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>472,800.01%</small></td>
                            <td class="text-danger">17.07<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">19<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-1.45%</small></td>
                            <td class="text-success">21<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>234.05%</small></td>
                            <td class="text-success">20<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                            <td class="">5&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">37.61<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-25.00%</small></td>
                            <td class="text-success">47.58<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>212.82%</small></td>
                            <td class="text-success">54.54<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                            <td class="">13.45&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">32.74<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-3.36%</small></td>
                            <td class="text-success">26.34<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>27.54%</small></td>
                            <td class="text-danger">-7.18<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-70,296.09%</small></td>
                            <td class="">-1.77&nbsp;<small>0.00%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">pol-t-mobile</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-success revenue_usd"><span>50.06</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>18.75%</small></td>
                            <td class="text-success">45.16<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>10.09%</small></td>
                            <td class="text-success">42.94<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>393.94%</small></td>
                            <td class="text-success">20.11<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>34,250.00%</small></td>
                            <td class="text-danger">4.97<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-success revenue_share_usd"><span>25.03</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>18.75%</small></td>
                            <td class="text-success">22.58<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>10.09%</small></td>
                            <td class="text-success">21.47<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>393.94%</small></td>
                            <td class="text-success">10.06<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>34,250.00%</small></td>
                            <td class="text-danger">2.49<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">5<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>18.52%</small></td>
                            <td class="text-success">7<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>1,393.34%</small></td>
                            <td class="text-success">4<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                            <td class="">1&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">12.54<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>7.14%</small></td>
                            <td class="text-success">18.33<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>9,300.00%</small></td>
                            <td class="text-success">9.36<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                            <td class="">2.31&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">-0.78<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-124.36%</small></td>
                            <td class="text-danger">-3.98<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-239.53%</small></td>
                            <td class="text-danger">-2.55<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-12,550.27%</small></td>
                            <td class="">-0.62&nbsp;<small>0.00%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">pse-jawwal-kidzo</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-success revenue_usd"><span>80.47</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>7.51%</small></td>
                            <td class="text-danger">78.37<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-1.55%</small></td>
                            <td class="text-success">56.17<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>1,337.27%</small></td>
                            <td class="text-success">20.03<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                            <td class="text-danger">4.94<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-success revenue_share_usd"><span>36.21</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>7.51%</small></td>
                            <td class="text-danger">35.27<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-1.55%</small></td>
                            <td class="text-success">25.28<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>1,337.27%</small></td>
                            <td class="text-success">9.01<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                            <td class="text-danger">2.22<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">18.62<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-28.53%</small></td>
                            <td class="text-success">16.71<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>1,249.41%</small></td>
                            <td class="text-success">5.98<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                            <td class="">1.48&nbsp;<small>0.00%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">pse-jawwal-linkit</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class=" revenue_usd"><span>43.26</span>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">45.32<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-7.09%</small></td>
                            <td class="text-danger">52.59<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-13.37%</small></td>
                            <td class="text-success">67.38<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>77.98%</small></td>
                            <td class="text-danger">25.95<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class=" revenue_share_usd"><span>12.98</span>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">13.60<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-7.09%</small></td>
                            <td class="text-danger">15.78<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-13.37%</small></td>
                            <td class="text-success">20.21<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>77.98%</small></td>
                            <td class="text-danger">7.79<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">170<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>44.12%</small></td>
                            <td class="text-success">152<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>90.66%</small></td>
                            <td class="text-success">90<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>118.97%</small></td>
                            <td class="">32&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">75.31<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>99.72%</small></td>
                            <td class="text-success">52.75<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>58.31%</small></td>
                            <td class="text-success">37.09<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>28.07%</small></td>
                            <td class="">16.29&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">-68.04<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>151.44%</small></td>
                            <td class="text-danger">-42.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>104.93%</small></td>
                            <td class="text-danger">-22.97<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>9.60%</small></td>
                            <td class="">-10.83&nbsp;<small>0.00%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">pse-ooredoo-linkit</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class=" revenue_usd"><span>12.69</span>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">10.38<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>46.51%</small></td>
                            <td class="text-danger">11.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-8.48%</small></td>
                            <td class="text-success">13.87<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>57.61%</small></td>
                            <td class="text-danger">5.59<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class=" revenue_share_usd"><span>4.31</span>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">3.53<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>46.51%</small></td>
                            <td class="text-danger">3.74<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-8.48%</small></td>
                            <td class="text-success">4.72<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>57.61%</small></td>
                            <td class="text-danger">1.90<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">4<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-61.96%</small></td>
                            <td class="">3&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">2.59<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-64.85%</small></td>
                            <td class="">2.46&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">1.69<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-3.67%</small></td>
                            <td class="text-danger">2.49<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-13.64%</small></td>
                            <td class="text-success">0.68<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>-112.98%</small></td>
                            <td class="">-1.13&nbsp;<small>0.00%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">safaricom</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-danger revenue_usd"><span>10.81</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-4.35%</small></td>
                            <td class="text-success">12.03<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>30.92%</small></td>
                            <td class="text-danger">10.70<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-6.44%</small></td>
                            <td class="text-danger">12.02<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-53.25%</small></td>
                            <td class="text-danger">20.91<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-29.01%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-danger revenue_share_usd"><span>4.06</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-4.35%</small></td>
                            <td class="text-success">4.53<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>30.92%</small></td>
                            <td class="text-danger">4.02<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-6.44%</small></td>
                            <td class="text-danger">4.52<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-53.25%</small></td>
                            <td class="text-danger">7.86<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-29.01%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-danger">1<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-83.43%</small></td>
                            <td class="text-danger">2<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-78.78%</small></td>
                            <td class="text-success">11<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>853.08%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.22<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-64.29%</small></td>
                            <td class="text-danger">0.30<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-89.01%</small></td>
                            <td class="text-success">2.77<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>428.84%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">1.78<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-33.10%</small></td>
                            <td class="text-danger">2.47<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-7.20%</small></td>
                            <td class="text-danger">3.02<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-34.17%</small></td>
                            <td class="text-success">3.19<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>59.38%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">sdn-mtn-dotjo</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class=" revenue_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class=" revenue_share_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0.00<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0.00<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">se-all-linkit</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-success revenue_usd"><span>498.16</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>49.30%</small></td>
                            <td class="text-success">524.34<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>9.85%</small></td>
                            <td class="text-success">369.23<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>2.03%</small></td>
                            <td class="text-success">315.66<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>94.19%</small></td>
                            <td class="text-success">167.19<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>4,101.72%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-success revenue_share_usd"><span>298.89</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>49.30%</small></td>
                            <td class="text-success">314.60<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>9.85%</small></td>
                            <td class="text-success">221.54<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>2.03%</small></td>
                            <td class="text-success">189.39<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>94.19%</small></td>
                            <td class="text-success">100.31<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>4,101.72%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">43<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>67.60%</small></td>
                            <td class="text-danger">16<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-53.94%</small></td>
                            <td class="text-success">22<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>82.03%</small></td>
                            <td class="text-success">10<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">204.88<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>56.64%</small></td>
                            <td class="text-danger">78.33<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-56.22%</small></td>
                            <td class="text-success">111.14<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>76.16%</small></td>
                            <td class="text-success">52.89<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">-35.98<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-133.40%</small></td>
                            <td class="text-success">82.60<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>-1,932.41%</small></td>
                            <td class="text-success">35.32<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>129.62%</small></td>
                            <td class="text-success">27.09<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>1,134.12%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">smart</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-success revenue_usd"><span>557.78</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.60%</small></td>
                            <td class="text-danger">551.02<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-0.68%</small></td>
                            <td class="text-danger">554.36<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-2.49%</small></td>
                            <td class="text-danger">582.47<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-19.22%</small></td>
                            <td class="text-success">685.02<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>179.49%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-success revenue_share_usd"><span>195.22</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.60%</small></td>
                            <td class="text-danger">192.86<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-0.68%</small></td>
                            <td class="text-danger">194.03<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-2.49%</small></td>
                            <td class="text-danger">203.87<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-19.22%</small></td>
                            <td class="text-success">239.76<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>179.49%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">4<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>127.28%</small></td>
                            <td class="text-success">1<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.01%</small></td>
                            <td class="text-danger">1<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-98.83%</small></td>
                            <td class="text-success">95<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>200.34%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.09<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-58.33%</small></td>
                            <td class="text-danger">0.13<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-17.32%</small></td>
                            <td class="text-danger">0.11<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-99.50%</small></td>
                            <td class="text-success">27.73<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>366.62%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">68.58<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-43.58%</small></td>
                            <td class="text-danger">109.36<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-12.26%</small></td>
                            <td class="text-danger">123.68<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-9.93%</small></td>
                            <td class="text-success">123.83<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>802.81%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">smartfren</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-success revenue_usd"><span>1,186.74</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.27%</small></td>
                            <td class="text-danger">1,414.55<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-1.37%</small></td>
                            <td class="text-danger">1,416.91<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-2.38%</small></td>
                            <td class="text-success">1,470.74<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>10.69%</small></td>
                            <td class="text-success">1,370.10<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>134.31%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-success revenue_share_usd"><span>504.36</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.27%</small></td>
                            <td class="text-danger">601.18<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-1.37%</small></td>
                            <td class="text-danger">602.19<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-2.38%</small></td>
                            <td class="text-success">625.06<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>10.69%</small></td>
                            <td class="text-success">582.29<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>134.31%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">14,117<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-16.85%</small></td>
                            <td class="text-success">20,029<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>28.77%</small></td>
                            <td class="text-success">13,781<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>106.14%</small></td>
                            <td class="text-success">7,763<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>139.70%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">486.49<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-13.15%</small></td>
                            <td class="text-success">689.18<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>52.87%</small></td>
                            <td class="text-success">425.93<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>195.21%</small></td>
                            <td class="text-success">192.72<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>246.15%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">-118.94<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>97.05%</small></td>
                            <td class="text-danger">-224.65<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-508.56%</small></td>
                            <td class="text-danger">76.86<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-75.89%</small></td>
                            <td class="text-success">287.13<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>485.55%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">smartp</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-success revenue_usd"><span>517.06</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.07%</small></td>
                            <td class="text-success">594.79<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>11.78%</small></td>
                            <td class="text-danger">541.89<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-0.56%</small></td>
                            <td class="text-danger">500.79<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-35.95%</small></td>
                            <td class="text-danger">904.83<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-16.88%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-success revenue_share_usd"><span>206.82</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.07%</small></td>
                            <td class="text-success">237.92<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>11.78%</small></td>
                            <td class="text-danger">216.75<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-0.56%</small></td>
                            <td class="text-danger">200.32<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-35.95%</small></td>
                            <td class="text-danger">361.93<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-16.88%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">165<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>1.05%</small></td>
                            <td class="text-danger">232<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-2.42%</small></td>
                            <td class="text-danger">162<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-41.26%</small></td>
                            <td class="text-success">704<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>620.68%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">21.38<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-31.88%</small></td>
                            <td class="text-danger">49.23<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-4.42%</small></td>
                            <td class="text-danger">34.30<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-11.25%</small></td>
                            <td class="text-success">77.98<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>720.59%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">123.03<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-13.70%</small></td>
                            <td class="text-danger">115.26<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-6.73%</small></td>
                            <td class="text-danger">123.40<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-42.03%</small></td>
                            <td class="text-success">216.30<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>74.50%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">srb-nth-linkit</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-danger revenue_usd"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-danger">14.97<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-2.94%</small></td>
                            <td class="text-danger">14.50<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-20.35%</small></td>
                            <td class="text-danger">17.49<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-50.67%</small></td>
                            <td class="text-danger">38.68<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-danger revenue_share_usd"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-danger">6.73<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-2.94%</small></td>
                            <td class="text-danger">6.52<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-20.35%</small></td>
                            <td class="text-danger">7.87<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-50.67%</small></td>
                            <td class="text-danger">17.40<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-99.71%</small></td>
                            <td class="text-success">9<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-success">21.03<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">5.55<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-2.95%</small></td>
                            <td class="text-danger">5.30<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-18.34%</small></td>
                            <td class="text-success">6.29<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>32.44%</small></td>
                            <td class="text-danger">-7.17<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">tcel</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-danger revenue_usd"><span>88.70</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-8.56%</small></td>
                            <td class="text-success">104.73<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>11.13%</small></td>
                            <td class="text-success">93.22<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>18.77%</small></td>
                            <td class="text-success">81.77<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>8.25%</small></td>
                            <td class="text-danger">80.79<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-19.04%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-danger revenue_share_usd"><span>39.92</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-8.56%</small></td>
                            <td class="text-success">47.13<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>11.13%</small></td>
                            <td class="text-success">41.95<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>18.77%</small></td>
                            <td class="text-success">36.80<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>8.25%</small></td>
                            <td class="text-danger">36.36<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-19.04%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-danger">382<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-28.38%</small></td>
                            <td class="text-success">472<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>127.23%</small></td>
                            <td class="text-success">268<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>444.83%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-danger">16.54<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-40.95%</small></td>
                            <td class="text-success">24.17<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>108.68%</small></td>
                            <td class="text-success">16.11<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>307.68%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">25.22<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>30.43%</small></td>
                            <td class="text-success">11.64<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>-552.81%</small></td>
                            <td class="text-danger">1.65<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-87.24%</small></td>
                            <td class="text-danger">9.90<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-52.99%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">telkomsel</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-success revenue_usd"><span>0.32</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>66.67%</small></td>
                            <td class="text-success">0.27<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>3.45%</small></td>
                            <td class="text-success">0.20<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>4.40%</small></td>
                            <td class="text-danger">0.19<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-14.14%</small></td>
                            <td class="text-danger">21.22<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-90.67%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-success revenue_share_usd"><span>0.16</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>66.67%</small></td>
                            <td class="text-success">0.14<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>3.45%</small></td>
                            <td class="text-success">0.10<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>4.40%</small></td>
                            <td class="text-danger">0.09<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-14.14%</small></td>
                            <td class="text-danger">10.61<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-90.67%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">343<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-42.61%</small></td>
                            <td class="text-danger">463<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-4.46%</small></td>
                            <td class="text-success">665<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>253.45%</small></td>
                            <td class="text-success">421<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">13.90<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-76.66%</small></td>
                            <td class="text-danger">46.60<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-25.83%</small></td>
                            <td class="text-success">81.55<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>483.61%</small></td>
                            <td class="text-success">46.39<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">-13.83<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>-76.73%</small></td>
                            <td class="text-success">-46.52<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>-25.86%</small></td>
                            <td class="text-danger">-81.47<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>486.79%</small></td>
                            <td class="text-danger">-37.06<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-209.91%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">th-ais-cm</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-danger revenue_usd"><span>167.87</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-3.19%</small></td>
                            <td class="text-success">194.46<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>10.63%</small></td>
                            <td class="text-success">180.55<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>1.03%</small></td>
                            <td class="text-danger">181.13<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-14.39%</small></td>
                            <td class="text-danger">214.55<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-0.11%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-danger revenue_share_usd"><span>83.93</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-3.19%</small></td>
                            <td class="text-success">97.23<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>10.63%</small></td>
                            <td class="text-success">90.28<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>1.03%</small></td>
                            <td class="text-danger">90.57<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-14.39%</small></td>
                            <td class="text-danger">107.28<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-0.11%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">16<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-95.36%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">5.55<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-91.07%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">59.92<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-16.92%</small></td>
                            <td class="text-danger">68.67<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-3.19%</small></td>
                            <td class="text-danger">70.89<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-15.57%</small></td>
                            <td class="text-success">76.71<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>-303.39%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">th-ais-gemezz</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-success revenue_usd"><span>15.76</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>95.83%</small></td>
                            <td class="text-success">15.03<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>28.73%</small></td>
                            <td class="text-danger">15.35<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-33.38%</small></td>
                            <td class="text-danger">19.77<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-13.92%</small></td>
                            <td class="text-danger">10.54<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-success revenue_share_usd"><span>7.88</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>95.83%</small></td>
                            <td class="text-success">7.51<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>28.73%</small></td>
                            <td class="text-danger">7.67<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-33.38%</small></td>
                            <td class="text-danger">9.88<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-13.92%</small></td>
                            <td class="text-danger">5.27<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">1<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                            <td class="text-danger">0<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-99.91%</small></td>
                            <td class="text-danger">248<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-27.84%</small></td>
                            <td class="text-danger">187<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-danger">22.03<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-47.23%</small></td>
                            <td class="text-danger">20.11<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">4.58<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>9.00%</small></td>
                            <td class="text-success">5.27<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>-201.66%</small></td>
                            <td class="text-success">-15.17<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>-55.05%</small></td>
                            <td class="text-success">-16.45<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">th-ais-gmob</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-success revenue_usd"><span>240.98</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>41.30%</small></td>
                            <td class="text-success">273.11<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>5.73%</small></td>
                            <td class="text-success">246.28<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>11.56%</small></td>
                            <td class="text-success">223.97<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>7.78%</small></td>
                            <td class="text-success">384.38<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>12.46%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-success revenue_share_usd"><span>96.39</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>41.30%</small></td>
                            <td class="text-success">109.24<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>5.73%</small></td>
                            <td class="text-success">98.51<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>11.56%</small></td>
                            <td class="text-success">89.59<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>7.78%</small></td>
                            <td class="text-success">153.75<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>12.46%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">282<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-56.98%</small></td>
                            <td class="text-danger">732<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-35.19%</small></td>
                            <td class="text-success">928<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>189.16%</small></td>
                            <td class="text-success">509<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">21.95<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-44.97%</small></td>
                            <td class="text-danger">51.01<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-40.69%</small></td>
                            <td class="text-success">74.14<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>133.80%</small></td>
                            <td class="text-success">70.08<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">39.81<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-11.33%</small></td>
                            <td class="text-success">22.54<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>-241.62%</small></td>
                            <td class="text-danger">-4.58<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-113.36%</small></td>
                            <td class="text-danger">50.45<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-14.08%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">th-ais-gmob-r01-r03</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-success revenue_usd"><span>129.80</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>4.88%</small></td>
                            <td class="text-danger">139.67<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-4.21%</small></td>
                            <td class="text-success">149.40<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>2.78%</small></td>
                            <td class="text-danger">144.90<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-14.68%</small></td>
                            <td class="text-success">106.17<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-success revenue_share_usd"><span>38.94</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>4.88%</small></td>
                            <td class="text-danger">41.90<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-4.21%</small></td>
                            <td class="text-success">44.82<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>2.78%</small></td>
                            <td class="text-danger">43.47<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-14.68%</small></td>
                            <td class="text-success">31.85<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">522<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>121,758.54%</small></td>
                            <td class="text-danger">254<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-68.52%</small></td>
                            <td class="text-success">554<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>15.66%</small></td>
                            <td class="text-success">361<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">30.98<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                            <td class="text-danger">15.26<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-68.96%</small></td>
                            <td class="text-danger">38.09<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-10.49%</small></td>
                            <td class="text-success">41.38<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">-7.56<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-124.00%</small></td>
                            <td class="text-success">15.10<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>-180.42%</small></td>
                            <td class="text-danger">-8.09<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>14.79%</small></td>
                            <td class="text-danger">-19.25<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">th-ais-mks</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-danger revenue_usd"><span>123.18</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-6.97%</small></td>
                            <td class="text-success">154.61<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>17.51%</small></td>
                            <td class="text-success">134.48<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>164.39%</small></td>
                            <td class="text-success">63.42<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>942.74%</small></td>
                            <td class="text-success">21.35<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>42.34%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-danger revenue_share_usd"><span>43.11</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-6.97%</small></td>
                            <td class="text-success">54.11<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>17.51%</small></td>
                            <td class="text-success">47.07<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>164.39%</small></td>
                            <td class="text-success">22.20<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>942.74%</small></td>
                            <td class="text-success">7.47<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>42.34%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">629<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-32.33%</small></td>
                            <td class="text-success">940<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>2.89%</small></td>
                            <td class="text-success">618<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>1,855,121.92%</small></td>
                            <td class="text-success">152<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>150,136.39%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">50.98<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-28.52%</small></td>
                            <td class="text-danger">71.70<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-15.14%</small></td>
                            <td class="text-success">52.07<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>400,466.67%</small></td>
                            <td class="text-success">12.84<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>400,626.61%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">-23.92<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>-36.64%</small></td>
                            <td class="text-success">-40.63<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>-43.53%</small></td>
                            <td class="text-danger">-37.13<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-2,595.55%</small></td>
                            <td class="text-danger">-7.73<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-921.54%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">th-ais-qr</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-success revenue_usd"><span>246.52</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>1.34%</small></td>
                            <td class="text-danger">244.11<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-25.50%</small></td>
                            <td class="text-danger">320.08<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-8.26%</small></td>
                            <td class="text-success">346.00<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>1.41%</small></td>
                            <td class="text-success">321.36<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>107.16%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-success revenue_share_usd"><span>98.61</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>1.34%</small></td>
                            <td class="text-danger">97.64<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-25.50%</small></td>
                            <td class="text-danger">128.03<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-8.26%</small></td>
                            <td class="text-success">138.40<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>1.41%</small></td>
                            <td class="text-success">128.54<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>107.16%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">170<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-28.32%</small></td>
                            <td class="text-danger">232<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-14.00%</small></td>
                            <td class="text-success">268<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>14.91%</small></td>
                            <td class="text-success">210<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">19.85<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-26.68%</small></td>
                            <td class="text-danger">26.86<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-37.31%</small></td>
                            <td class="text-danger">43.53<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-3.52%</small></td>
                            <td class="text-success">45.52<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">30.13<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-62.56%</small></td>
                            <td class="text-success">69.92<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>2.92%</small></td>
                            <td class="text-success">64.71<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>2.37%</small></td>
                            <td class="text-success">55.69<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>186.74%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">th-true-gm</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-danger revenue_usd"><span>14.09</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-11.11%</small></td>
                            <td class="text-success">17.57<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>13.99%</small></td>
                            <td class="text-success">16.21<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>6.56%</small></td>
                            <td class="text-danger">16.65<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-40.82%</small></td>
                            <td class="text-success">64.99<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-danger revenue_share_usd"><span>7.04</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-11.11%</small></td>
                            <td class="text-success">8.79<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>13.99%</small></td>
                            <td class="text-success">8.10<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>6.56%</small></td>
                            <td class="text-danger">8.32<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-40.82%</small></td>
                            <td class="text-success">32.50<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">8<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                            <td class="text-success">2<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                            <td class="text-success">1<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                            <td class="text-success">146<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0.56<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                            <td class="text-success">0.13<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                            <td class="text-success">0.04<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                            <td class="text-success">35.57<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">4.29<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-22.76%</small></td>
                            <td class="text-success">5.30<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.08%</small></td>
                            <td class="text-danger">5.69<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-41.99%</small></td>
                            <td class="text-success">-13.09<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">th-true-qr</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-success revenue_usd"><span>13.84</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>12.24%</small></td>
                            <td class="text-danger">17.25<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-19.33%</small></td>
                            <td class="text-success">14.25<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>44.07%</small></td>
                            <td class="text-success">9.99<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>37.34%</small></td>
                            <td class="text-danger">11.08<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-67.48%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-success revenue_share_usd"><span>4.84</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>12.24%</small></td>
                            <td class="text-danger">6.04<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-19.33%</small></td>
                            <td class="text-success">4.99<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>44.07%</small></td>
                            <td class="text-success">3.50<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>37.34%</small></td>
                            <td class="text-danger">3.88<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-67.48%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="text-success">13<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-success">3.75<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">3.31<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-46.15%</small></td>
                            <td class="text-success">3.69<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>34.31%</small></td>
                            <td class="text-success">2.69<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>32.78%</small></td>
                            <td class="text-danger">-0.64<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-108.30%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">three</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-success revenue_usd"><span>1,200.58</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.46%</small></td>
                            <td class="text-success">1,276.97<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>8.59%</small></td>
                            <td class="text-danger">1,242.02<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-3.31%</small></td>
                            <td class="text-danger">1,363.67<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-27.77%</small></td>
                            <td class="text-danger">2,500.37<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-32.71%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-success revenue_share_usd"><span>471.83</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.46%</small></td>
                            <td class="text-success">501.85<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>8.59%</small></td>
                            <td class="text-danger">488.11<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-3.31%</small></td>
                            <td class="text-danger">535.92<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-27.77%</small></td>
                            <td class="text-danger">982.65<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-32.71%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">11<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-55.85%</small></td>
                            <td class="text-success">19,187<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>140.97%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.22<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-49.40%</small></td>
                            <td class="text-success">38.19<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>7.38%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">275.70<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-28.98%</small></td>
                            <td class="text-danger">375.98<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-11.33%</small></td>
                            <td class="text-danger">438.61<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-29.58%</small></td>
                            <td class="text-success">799.01<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>132.70%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">true-cyb</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-success revenue_usd"><span>100.12</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>2.28%</small></td>
                            <td class="text-success">114.37<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>15.82%</small></td>
                            <td class="text-danger">104.47<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-8.96%</small></td>
                            <td class="text-danger">117.32<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-28.73%</small></td>
                            <td class="text-danger">233.18<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-87.66%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-success revenue_share_usd"><span>25.03</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>2.28%</small></td>
                            <td class="text-success">28.59<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>15.82%</small></td>
                            <td class="text-danger">26.12<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-8.96%</small></td>
                            <td class="text-danger">29.33<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-28.73%</small></td>
                            <td class="text-danger">58.29<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-87.66%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-99.54%</small></td>
                            <td class="text-success">28<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>53.69%</small></td>
                            <td class="text-success">18<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>5,400.37%</small></td>
                            <td class="text-danger">50<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-65.93%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-success">7.00<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>79.96%</small></td>
                            <td class="text-success">4.21<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>3,194.08%</small></td>
                            <td class="text-danger">13.18<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-59.25%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">1.25<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>-126.09%</small></td>
                            <td class="text-danger">-5.37<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>175.56%</small></td>
                            <td class="text-danger">-2.27<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-185.18%</small></td>
                            <td class="text-danger">-1.51<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-116.36%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">uae-etisalat-airpay</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-danger revenue_usd"><span>168.13</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-10.12%</small></td>
                            <td class="text-danger">197.92<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-13.81%</small></td>
                            <td class="text-success">242.29<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>10.83%</small></td>
                            <td class="text-danger">242.11<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-1.55%</small></td>
                            <td class="text-danger">132.50<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-danger revenue_share_usd"><span>8.41</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-10.12%</small></td>
                            <td class="text-danger">9.90<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-13.81%</small></td>
                            <td class="text-success">12.11<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>10.83%</small></td>
                            <td class="text-danger">12.11<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-1.55%</small></td>
                            <td class="text-danger">6.63<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">15<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-15.70%</small></td>
                            <td class="text-success">50<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>26.21%</small></td>
                            <td class="text-danger">40<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-35.40%</small></td>
                            <td class="text-danger">30<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">5.87<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-29.03%</small></td>
                            <td class="text-success">8.43<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>7.11%</small></td>
                            <td class="text-danger">8.62<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-2.66%</small></td>
                            <td class="text-danger">4.75<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">uae-etisalat-linkit</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-success revenue_usd"><span>311.21</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>33.93%</small></td>
                            <td class="text-success">307.07<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>9.02%</small></td>
                            <td class="text-success">314.72<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>1.49%</small></td>
                            <td class="text-danger">320.19<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-35.19%</small></td>
                            <td class="text-success">442.11<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>3,696.12%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-success revenue_share_usd"><span>161.83</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>33.93%</small></td>
                            <td class="text-success">159.68<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>9.02%</small></td>
                            <td class="text-success">163.65<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>1.49%</small></td>
                            <td class="text-danger">166.50<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-35.19%</small></td>
                            <td class="text-success">229.90<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>3,696.12%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">1<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>25.01%</small></td>
                            <td class="text-danger">3<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-95.47%</small></td>
                            <td class="text-danger">35<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-41.51%</small></td>
                            <td class="text-success">55<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">2.23&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">8.80<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-95.37%</small></td>
                            <td class="text-danger">111.10<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-38.28%</small></td>
                            <td class="text-success">165.85<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">72.20<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-35.02%</small></td>
                            <td class="text-success">106.38<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>-262.69%</small></td>
                            <td class="text-danger">13.92<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-25.93%</small></td>
                            <td class="text-success">9.69<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>124.06%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">unitel</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-danger revenue_usd"><span>104.33</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-0.51%</small></td>
                            <td class="text-success">121.40<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>11.05%</small></td>
                            <td class="text-success">113.17<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>9.51%</small></td>
                            <td class="text-success">104.37<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>27.92%</small></td>
                            <td class="text-success">70.19<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>209.63%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-danger revenue_share_usd"><span>36.51</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-0.51%</small></td>
                            <td class="text-success">42.49<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>11.05%</small></td>
                            <td class="text-success">39.61<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>9.51%</small></td>
                            <td class="text-success">36.53<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>27.92%</small></td>
                            <td class="text-success">24.57<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>209.63%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">27<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-61.68%</small></td>
                            <td class="text-danger">98<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-62.33%</small></td>
                            <td class="text-danger">182<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-9.72%</small></td>
                            <td class="text-success">202<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>4,414.83%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">2.97<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-80.20%</small></td>
                            <td class="text-danger">4.32<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-75.44%</small></td>
                            <td class="text-danger">11.77<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-67.76%</small></td>
                            <td class="text-success">12.18<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>1,296.89%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">26.44<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>27.42%</small></td>
                            <td class="text-success">30.51<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>81.47%</small></td>
                            <td class="text-success">22.01<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>-334.58%</small></td>
                            <td class="text-success">11.93<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>61.84%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">vietnamobile</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class=" revenue_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-danger">5.76<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-77.47%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class=" revenue_share_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-danger">1.73<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-77.47%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">12<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-69.80%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.07<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-18.68%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-success">1.36<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>53.71%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">viettel</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-danger revenue_usd"><span>25.26</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-4.97%</small></td>
                            <td class="text-success">30.23<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>4.69%</small></td>
                            <td class="text-danger">36.29<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-23.54%</small></td>
                            <td class="text-danger">47.39<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-25.06%</small></td>
                            <td class="text-danger">62.68<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-81.11%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-danger revenue_share_usd"><span>7.58</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-4.97%</small></td>
                            <td class="text-success">9.07<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>4.69%</small></td>
                            <td class="text-danger">10.89<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-23.54%</small></td>
                            <td class="text-danger">14.22<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-25.06%</small></td>
                            <td class="text-danger">18.80<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-81.11%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-danger">3<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-55.52%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-danger">1.23<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-40.50%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">5.34<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-24.79%</small></td>
                            <td class="text-danger">8.44<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-27.72%</small></td>
                            <td class="text-danger">11.50<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-25.39%</small></td>
                            <td class="text-success">14.01<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>55.17%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">vinaphone</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-success revenue_usd"><span>221.64</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>3.63%</small></td>
                            <td class="text-success">250.02<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>10.59%</small></td>
                            <td class="text-danger">232.92<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-0.04%</small></td>
                            <td class="text-danger">244.40<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-26.01%</small></td>
                            <td class="text-success">248.99<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>44.01%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-success revenue_share_usd"><span>66.49</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>3.63%</small></td>
                            <td class="text-success">75.01<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>10.59%</small></td>
                            <td class="text-danger">69.88<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-0.04%</small></td>
                            <td class="text-danger">73.32<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-26.01%</small></td>
                            <td class="text-success">74.70<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>44.01%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-success">31<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>14.56%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-success">7.77<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>32.20%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">41.06<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-26.16%</small></td>
                            <td class="text-danger">52.53<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-8.36%</small></td>
                            <td class="text-danger">58.53<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-13.17%</small></td>
                            <td class="text-success">53.34<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>714.13%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">viva</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class=" revenue_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">3.09<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-83.42%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class=" revenue_share_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">1.24<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-83.42%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">-4.67<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>-14.29%</small></td>
                            <td class="text-success">-5.27<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>-3.33%</small></td>
                            <td class="text-success">-5.39<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>-1.11%</small></td>
                            <td class="text-danger">-4.52<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>628.37%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">waki-rbt-isat</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class=" revenue_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-success">39.64<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>9,162.39%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class=" revenue_share_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-success">15.86<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>9,162.39%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-success">11.42<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">waki-tri-telesat</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-danger revenue_usd"><span>0.32</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-44.44%</small></td>
                            <td class="text-danger">0.37<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-22.64%</small></td>
                            <td class="text-danger">0.68<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-12.19%</small></td>
                            <td class="text-success">0.72<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>44.32%</small></td>
                            <td class="text-success">0.65<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>82.06%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-danger revenue_share_usd"><span>0.13</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-44.44%</small></td>
                            <td class="text-danger">0.15<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-22.64%</small></td>
                            <td class="text-danger">0.27<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-12.19%</small></td>
                            <td class="text-success">0.29<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>44.32%</small></td>
                            <td class="text-success">0.26<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>82.06%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="text-success">1<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0.06<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.08<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-43.88%</small></td>
                            <td class="text-danger">0.19<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-15.21%</small></td>
                            <td class="text-success">0.21<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>42.57%</small></td>
                            <td class="text-success">0.13<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>16.82%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">waki-tsel-telesat</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-success revenue_usd"><span>971.52</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>12.08%</small></td>
                            <td class="text-success">1,099.90<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>3.23%</small></td>
                            <td class="text-success">1,034.67<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>22.55%</small></td>
                            <td class="text-success">871.85<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.11%</small></td>
                            <td class="text-success">461.38<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>436.06%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-success revenue_share_usd"><span>680.06</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>12.08%</small></td>
                            <td class="text-success">769.93<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>3.23%</small></td>
                            <td class="text-success">724.27<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>22.55%</small></td>
                            <td class="text-success">610.30<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.11%</small></td>
                            <td class="text-success">322.97<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>436.06%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">1,327<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-69.41%</small></td>
                            <td class="text-success">4,294<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>41.26%</small></td>
                            <td class="text-success">3,646<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>167.82%</small></td>
                            <td class="text-success">1,512<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>228.91%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">71.75<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-70.88%</small></td>
                            <td class="text-danger">381.96<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-0.18%</small></td>
                            <td class="text-success">385.36<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>126.10%</small></td>
                            <td class="text-success">169.38<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>355.63%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">338.28<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-7.36%</small></td>
                            <td class="text-success">160.30<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>57.22%</small></td>
                            <td class="text-success">97.87<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>22.88%</small></td>
                            <td class="text-success">85.06<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>-2,236.07%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">xlaxiata</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-danger revenue_usd"><span>3,131.90</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-4.49%</small></td>
                            <td class="text-success">3,544.36<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>20.68%</small></td>
                            <td class="text-success">3,150.57<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>2.46%</small></td>
                            <td class="text-success">3,053.37<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>4.04%</small></td>
                            <td class="text-danger">2,960.73<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-6.88%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-danger revenue_share_usd"><span>1,879.14</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-4.49%</small></td>
                            <td class="text-success">2,126.61<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>20.68%</small></td>
                            <td class="text-success">1,890.34<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>2.46%</small></td>
                            <td class="text-success">1,832.02<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>4.04%</small></td>
                            <td class="text-danger">1,776.44<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-6.88%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-success">0<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                            <td class="text-success">0<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>234.15%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">1,207.13<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-12.92%</small></td>
                            <td class="text-danger">1,378.38<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-5.03%</small></td>
                            <td class="text-success">1,404.96<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>1.43%</small></td>
                            <td class="text-success">1,380.66<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>233.51%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">yatta-rbt-isat</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class=" revenue_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-success">70.07<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>659.53%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class=" revenue_share_usd"><span>0.00</span>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-success">24.52<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>659.53%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-success">17.66<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">yatta-tsel-telesat</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-success revenue_usd"><span>520.70</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>2.65%</small></td>
                            <td class="text-danger">493.90<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-5.26%</small></td>
                            <td class="text-success">477.44<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>84.72%</small></td>
                            <td class="text-success">310.65<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>504.33%</small></td>
                            <td class="text-success">89.27<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-success revenue_share_usd"><span>364.49</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>2.65%</small></td>
                            <td class="text-danger">345.73<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-5.26%</small></td>
                            <td class="text-success">334.21<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>84.72%</small></td>
                            <td class="text-success">217.46<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>504.33%</small></td>
                            <td class="text-success">62.49<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">1,531<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-70.92%</small></td>
                            <td class="text-success">4,772<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>73.33%</small></td>
                            <td class="text-success">3,195<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>746.50%</small></td>
                            <td class="">881&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">77.08<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-71.48%</small></td>
                            <td class="text-success">391.70<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>8.62%</small></td>
                            <td class="text-success">318.81<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>513.18%</small></td>
                            <td class="">91.43&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">106.13<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>-1,521.84%</small></td>
                            <td class="text-success">-166.41<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>-27.76%</small></td>
                            <td class="text-danger">-167.35<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>541.57%</small></td>
                            <td class="text-success">-47.70<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">za-mtn-mobixone</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-success revenue_usd"><span>17.19</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>42.06%</small></td>
                            <td class="text-danger">18.49<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-0.95%</small></td>
                            <td class="text-danger">18.49<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-10.41%</small></td>
                            <td class="text-danger">20.98<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-40.61%</small></td>
                            <td class="text-success">51.25<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>70.77%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-success revenue_share_usd"><span>8.59</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>42.06%</small></td>
                            <td class="text-danger">9.25<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-0.95%</small></td>
                            <td class="text-danger">9.25<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-10.41%</small></td>
                            <td class="text-danger">10.49<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-40.61%</small></td>
                            <td class="text-success">25.63<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>70.77%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">60<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>575.81%</small></td>
                            <td class="text-success">67<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>15,447.36%</small></td>
                            <td class="text-success">71<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>42.20%</small></td>
                            <td class="text-success">86<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">28.69<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>692.31%</small></td>
                            <td class="text-success">31.59<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>16,100.00%</small></td>
                            <td class="text-success">34.32<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>34.87%</small></td>
                            <td class="text-success">42.40<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">-23.52<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-683.93%</small></td>
                            <td class="text-danger">-24.63<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-406.86%</small></td>
                            <td class="text-danger">-26.10<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>129.74%</small></td>
                            <td class="text-danger">-23.58<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-1,978.36%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">za-mtn-mondia</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-success revenue_usd"><span>305.10</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>31.17%</small></td>
                            <td class="text-success">196.38<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>74.26%</small></td>
                            <td class="text-success">182.55<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>441.09%</small></td>
                            <td class="text-success">82.58<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>883.35%</small></td>
                            <td class="text-success">22.43<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-success revenue_share_usd"><span>158.65</span><i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>31.17%</small></td>
                            <td class="text-success">102.12<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>74.26%</small></td>
                            <td class="text-success">94.93<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>441.09%</small></td>
                            <td class="text-success">42.94<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>883.35%</small></td>
                            <td class="text-success">11.66<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="">0&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="">0.00&nbsp;<small>0.00%</small></td>
                            <td class="text-success">0.00<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-success">46.94<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>11.33%</small></td>
                            <td class="text-success">61.43<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>400.56%</small></td>
                            <td class="text-success">28.38<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>829.03%</small></td>
                            <td class="text-danger">7.75<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-panel">
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-light table-striped m-0 font-13 table-text-no-wrap">
                    <thead class="thead-dark text-uppercase">
                        <tr>
                            <th class="text-center" width="15%">Operator</th>
                            <th>Type</th>
                            <th>Yesterday</th>
                            <th>Last 7 days average</th>
                            <th>Last 30 days average</th>
                            <th>Last 90 days average</th>
                            <th>Last 1 year average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-weight-bold bg-white text-center align-middle text-uppercase"
                                rowspan="6">za-vodacom-mobixone</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Revenue (USD)</td>
                            <td class="text-danger revenue_usd"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-danger">76.94<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-22.59%</small></td>
                            <td class="text-danger">100.68<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-13.01%</small></td>
                            <td class="text-danger">117.54<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-42.46%</small></td>
                            <td class="text-success">158.35<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>873,988.22%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Rev Share (USD)</td>
                            <td class="text-danger revenue_share_usd"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-danger">36.93<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-22.59%</small></td>
                            <td class="text-danger">48.33<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-13.01%</small></td>
                            <td class="text-danger">56.42<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-42.46%</small></td>
                            <td class="text-success">76.01<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>873,988.22%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily MO</td>
                            <td class="text-danger mo"><span>0</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-success">7<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>9,844.98%</small></td>
                            <td class="text-danger">2<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-94.16%</small></td>
                            <td class="text-success">87<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily Cost Campaign</td>
                            <td class="text-danger cost_campaign"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">0.00<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-100.00%</small></td>
                            <td class="text-success">5.03<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>5,699.98%</small></td>
                            <td class="text-danger">1.70<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-96.95%</small></td>
                            <td class="text-success">90.39<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>100.00%</small></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Daily PNL</td>
                            <td class="text-danger pnl"><span>0.00</span><i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>0.00%</small></td>
                            <td class="text-danger">14.61<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-60.77%</small></td>
                            <td class="text-danger">30.60<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-30.74%</small></td>
                            <td class="text-success">42.29<i
                                    class="fa fa-arrow-up"></i>&nbsp;<small>89.57%</small></td>
                            <td class="text-danger">-30.32<i
                                    class="fa fa-arrow-down"></i>&nbsp;<small>-1,419,058.87%</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- <div class="d-flex align-items-center my-2 pull-right">
        <span class="badge badge-secondary px-2 bg-primary" id="loadTimer">Load Time :{{ round(microtime(true) - LARAVEL_START, 3) }}s</span>
    </div> --}}



    <button type="button" id="button"
        class="btn btn-danger btn-floating btn-sm btn-back-to-top-position up"><i
            class="fa fa-arrow-up"></i></button>




</div>

@endsection