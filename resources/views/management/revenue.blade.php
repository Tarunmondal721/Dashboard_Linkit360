@extends('layouts.admin')

@section('title')
    {{ __('Revenue Management') }}
@endsection

@section('content')
<div class="page-content">
    {{-- <div class="page-title" style="margin-bottom:25px">
      <div class="row justify-content-between align-items-center">
        <div
          class="col-xl-4 col-lg-4 col-md-4 d-flex align-items-center justify-content-between justify-content-md-start mb-3 mb-md-0">
          <div class="d-inline-block">
            <h5 class="h4 d-inline-block font-weight-400 mb-0 "> Report Summary
            </h5>
          </div>
        </div>
        <div
          class="col-xl-8 col-lg-8 col-md-8 d-flex align-items-center justify-content-between justify-content-md-end">
        </div>
      </div>
    </div> --}}

    <div class="card shadow-sm mt-0">
      <div class="card-body">
        <!-- <form action="/report/summary" id="summaryForm"> -->
        <input type="hidden" name="_csrf"
          value="PJnNKv58eb8B5oELcNeN9r5ncqjd4NSYK-uSSzfq07RGzJhyySMWjTLe4jkylsKZ7R4Lkbiztcd9gsUmAr2Jww=="
          id="csrf_token">
        <div class="row">
          <div class="col-lg-4">
            <label>Data Type</label>
            <select class="simple-multiple-select dropdown-style" id="data-type" style="width: 100%"
              data-select2-id="select2-data-data-type" tabindex="-1" aria-hidden="true">
              <option value="daily" selected="" data-select2-id="select2-data-2-7buy">Daily Report</option>
              <option value="monthly">Monthly Report</option>
            </select>
          </div>

          <div class="col-lg-4">
            <label>Report Type</label>
            <select class="simple-multiple-select dropdown-style" id="report-type" style="width: 100%"
              data-select2-id="select2-data-report-type" tabindex="-1" aria-hidden="true">
              <option value="operator" selected="" data-select2-id="select2-data-4-abx5">Operator Summary</option>
              <option value="country">Country Summary</option>
              <option value="ac manager">Account Manager's Summary</option>
            </select>
          </div>
          <div class="col-lg-4">
            <div class="form-group">
              <label for="summarycompany">Company</label>
              <select class="simple-multiple-select dropdown-style" id="dashboard-company"
                name="company_id" style="width: 100%" data-select2-id="select2-data-dashboard-company" tabindex="-1"
                aria-hidden="true">
                <option value="" data-select2-id="select2-data-6-fj9l">All Company</option>
                <option value="7">ClickMultimediaTH</option>
                <option value="10">KreativeBersamaID</option>
                <option value="2">KreativeBersamaPH</option>
                <option value="1">KreativeMultimediaVN</option>
                <option value="14">LinkIT.Africa</option>
                <option value="16">LinkIT.Airpay</option>
                <option value="15">LinkIT.America</option>
                <option value="11">LinkIT.Asia</option>
                <option value="9">Linkit.EU</option>
                <option value="12">LinkIT.Global</option>
                <option value="6">Linkit.ID</option>
                <option value="8">Linkit.MENA</option>
                <option value="13">LinkIT.OTT</option>
                <option value="5">PASS</option>
                <option value="4">Waki</option>
                <option value="3">Yatta</option>
              </select>
            </div>

          </div>
          <div class="col-lg-4">
            <div class="form-group">
              <label for="summarycountry">Country</label>
              <select class="dropdown-style" style="width:100%">
                <option value="">Country Name</option>
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
              </select>
              
            </div>
          </div>
          <div class="col-lg-4">
            <label for="summery_operator_id">Operator</label>
            <select class="dropdown-style" style="width:100%">
              <option value="">Operator Name</option>
              <option value="8">Ais</option>
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
              <option value="104">Pk-telenor-linkit</option>
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
            </select>
          </div>
        </div>
        <div class="error_block"></div>
        <!-- </form> -->
        <div class="row mb-4">

          <!-- <div class="col-lg-2">
                <label class="invisible d-block" for="">From</label>
                <input name="from_datepicker" class="form-control form_datetime1" type="text" id="from_datepicker" value="2022-10-01">
            </div>
            <div class="col-lg-2">
                <label class="invisible d-block" for="">To</label>
                <input name="to_datepicker" class="form-control form_datetime1" type="text" id="to_datepicker" value="2022-10-31">
            </div> -->


          <div class="col-lg-3">
            <label class="invisible d-block">To</label>
            <input class="form-control form_datetime1" type="hidden" name="pnl_to_datepicker" id="pnl_to_datepicker"
              value="2022-10-31">

            <input type="hidden" id="hiddenFrm" value="2022-10-01">
            <input type="hidden" id="hiddenTo" value="2022-10-31">

            {{-- <input type="text" name="date_analytics_picker" id="date_analytics_picker" value="" oct="" 01,=""
              2022-oct="" 31,="" 2022""="" style="display: none;"><button type="button"
              class="comiseo-daterangepicker-triggerbutton ui-button ui-corner-all ui-widget comiseo-daterangepicker-bottom comiseo-daterangepicker-vfit"
              id="drp_autogen0">Oct 1, 2022 - Oct 31, 2022<span class="ui-button-icon-space"> </span><span
                class="ui-button-icon ui-icon ui-icon-triangle-1-s"></span></button> --}}
                <span class="ui-button-icon-space"><input type="text" name="daterange" id="daterange" value="01/01/2018 - 01/15/2018"></span> <span
                  class="ui-button-icon ui-icon ui-icon-triangle-1-s"></span></button>
          </div>

          <div class="col-lg-3">
            <label class="invisible d-block">Search</label>
            <button type="button" class="btn btn-primary" id="reportsubmit">Submit</button>
            <a class="btn btn-secondary" href="#">Reset</a>
          </div>

          <div class="col-lg-3">
            <label>Data Base On</label>
            <div class="form-group">
              <select class="simple-multiple-select dropdown-style" name="sorting_pnl_orders"
                id="sorting_pnl_orders" style="width: 100%" data-select2-id="select2-data-sorting_pnl_orders"
                tabindex="-1" aria-hidden="true">
                <option value="higher_revenue_usd" data-select2-id="select2-data-10-njlv">Highest Revenue USD
                </option>
                <option value="lowest_revenue_usd">Lowest Revenue USD</option>
                <option value="highest_reg">Highest Reg</option>
                <option value="lowest_reg">Lowest Reg</option>
                <option value="highest_unreg">Highest Unreg</option>
                <option value="lowest_unreg">Lowest Unreg</option>
                <option value="highest_renewal">Highest Renewal</option>
                <option value="lowest_renewal">Lowest Renewal</option>
                <option value="highest_bill_rate">Highest Bill Rate</option>
                <option value="lowest_bill_rate">Lowest Bill Rate</option>
              </select>
            </div>
          </div>
          <div class="col-lg-2">
            <label class="invisible d-block">Sort</label>
            <button type="button" class="btn btn-primary" id="alphBnt"><i class="fa fa-filter"></i> Filter</button>
          </div>
          <div class="col-lg-2">
            <label class="invisible d-block">Export Excel</label>
            <button class="btn btn-sm pnl-xls" style="color:white; background-color:green" data-param="reportXls"><i
                class="fa fa-file-excel-o"></i>Export All as XLS</button>
          </div>
        </div>
      </div>
    </div>

   

    


    



  </div>
  @endsection

{{-- <input type="text" name="daterange" value="01/01/2018 - 01/15/2018" /> --}}

