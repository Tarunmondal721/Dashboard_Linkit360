<?php $countrys = App\Models\Operator::select('country_name','country_id')->Status(1)->orderBy('country_name', 'ASC')->distinct()->get(); ?>
<?php $operators = App\Models\Operator::orderBy('operator_name', 'ASC')->get(); ?>
<?php $services = App\Models\Service::orderBy('service_name', 'ASC')->get(); ?>

@extends('layouts.admin')

@section('title')
  {{ __('Adnet Report') }}
@endsection

@section('content')
<div class="page-content">
  <div class="page-title" style="margin-bottom:25px">
    <div class="row justify-content-between align-items-center">
      <div class="col-xl-4 col-lg-4 col-md-4 d-flex align-items-center justify-content-between justify-content-md-start mb-3 mb-md-0">
        <div class="d-inline-block">
          <h5 class="h4 d-inline-block font-weight-400 mb-0"><b>Arpu Details</b></h5><br>
          <p class="d-inline-block font-weight-200 mb-0">Summary of Arpu</p>
        </div>
      </div>
      <div class="col-xl-8 col-lg-8 col-md-8 d-flex align-items-center justify-content-between justify-content-md-end">
      </div>
    </div>
  </div>

  <div class="card shadow-sm mt-0">
    <form id="adnetfrm" style="overflow-y:hidden;">
      <div class="card-body">
        <div class="row">
          <div class="col-lg-3">
            <label>Country Name</label>
            <select name="country" class="form-control select2" onchange="countryChange()" required id="adnet_country_name">
              @if (count($adnetreport['input_request']) >0)
              <option value="{{$adnetreport['input_request']['country']['id']}}">{{$adnetreport['input_request']['country']['label']}}</option>
              @else 
              <option value="">Select Country</option>
              @endif
              @if(isset($countrys) && !empty($countrys))
                    
              @foreach ($countrys as $country)
              @if (in_array($country->country_name,$adnetreport['arrayCountries']))
              <option value="{{$country->country_id}}">{{$country->country_name}}</option>
              @endif
              @endforeach
              
              @else
              <option value="">Select Country</option>
              @endif
            </select>
          </div>

          <div class="col-lg-3">
            <label>Operator Name</label>
            <select name="operator" class="form-control select2" required id="adnet_operator_name" onchange="operatorChange()">
              @if (count($adnetreport['input_request']) >0)
              <option value="{{$adnetreport['input_request']['operator']['id']}}">{{$adnetreport['input_request']['operator']['label']}}</option>
              @else 
              <option value="">Select Operator</option>
              @endif
            </select>
          </div>
          <div class="col-lg-3">
            <label>Service Name</label>
            <select name="service" class="form-control select2" required id="adnet_service_name" onchange="serviceChange()" >
              @if (count($adnetreport['input_request']) >0)
              <option value="{{$adnetreport['input_request']['service']['id']}}">{{$adnetreport['input_request']['service']['label']}}</option>
              @else 
              <option value="">Select Service</option>
              @endif
            </select>
          </div>
          <div class="col-lg-3">
            <label>Keyword</label>
            <select name="keyword" class="form-control select2"  id="keyword_service" >
              @if (count($adnetreport['input_request']) >0)
              <option value="{{$adnetreport['input_request']['keyword']['id']}}">{{$adnetreport['input_request']['keyword']['label']}}</option>
              @else 
              <option value="">Select Keyword</option>
              @endif
            </select>
          </div>
        </div>
          
          <div class="row">
            <div class="col-lg-3">
              <label>From</label>
              <input type="date" class="adnetDate  form-control " id="from_date" name="from" pattern="\d{2}/\d{2}/\d{4}" placeholder="dd/mm/yyyy"  required data-progress-id="form"  value="{{count($adnetreport['input_request']) >0 ? $adnetreport['input_request']['from'] : ''}}">
              <small id="error-from" class="d-none text-danger text-sm">From must be before or same as To date.</small>
            </div>
            <div class="col-lg-3">
              <label >To (Purchase)</label>
              <input type="date" class="adnetDate   form-control " id="to_date" pattern="\d{2}/\d{2}/\d{4}" placeholder="dd/mm/yyyy"  name="to"  required data-progress-id="form" placeholder="dd/mm/yyyy" value="{{count($adnetreport['input_request']) >0 ? $adnetreport['input_request']['to'] : ''}}">
              <small id="error-to" class="d-none text-danger text-sm">To date cannot be beyond today.</small>
            </div>
            <div class="col-lg-3">
              <label>To (Renewal)</label>
              <select name="option_date" class="form-control select2" required id="date_range" onchange="date_ranges()">
                {{-- @if (count($adnetreport['input_request']) >0)
                <option value="{{$adnetreport['input_request']['option_date']['id']}}">{{$adnetreport['input_request']['option_date']['label']}}</option>
                <option value="Today">Today</option>
                <option value="custom_range">Custom Range</option>
                @else  --}}
                <option value="Today">Today</option>
                {{-- <option value="custom_range">Custom Range</option> --}}
                {{-- @endif --}}
              </select>
            </div>
            <div class="col-lg-3">
              <label class="invisible d-block">Search</label>
              <button type="submit" id="submitAdnetBtn" onclick="return submitArpu()" class="btn btn-primary">Submit</button>
              <a href="#" class="btn btn-secondary" onclick="reset()">Reset</a>
            </div>
          </div>
          {{-- <div class="col-lg-3 {{!empty($adnetreport['input_request']['renewal'])  ? '' : 'gu-hide' }}" id="date_range_faield">
            <div class="col-lg-12">
              <label >Custom To (Renewal)</label>
              <input type="date" class="adnetDate  form-control " name="renewal" id="adnet_renewal_datepicker" data-progress-id="to" placeholder="YYYY-MM-DD" value="{{count($adnetreport['input_request']) >0 ? $adnetreport['input_request']['renewal'] : ''}}">
            </div>
          </div> --}}

          <div class="error_block"></div>
           

          @if ($adnetreport['is_publisher'] ==1)
          <div class="row mt-3">
            <div class="col-lg-3">
              <label>Show Data Base On</label>
              <select name="filterAmount" class="form-control select2" required >
                @if (count($adnetreport['input_request']) >0)
                <option value="{{$adnetreport['input_request']['order']['order']}}">{{$adnetreport['input_request']['order']['order']}}</option> 
                @endif
                <option value="Highest Subs">Highest Subs</option>
                <option value="Lowest Subs">Lowest Subs</option>
                <option value="Highest Actual Ltv">Highest Actual Ltv</option>
                <option value="Lowest Actual Ltv">Lowest Actual Ltv</option>
                <option value="Highest Estimating Ltv A">Highest Estimating Ltv A</option>
                <option value="Lowest Estimating Ltv A">Lowest Estimating Ltv A</option>
                <option value="Highest Estimating Ltv B">Highest Estimating Ltv B</option>
                <option value="Lowest Estimating Ltv B">Lowest Estimating Ltv B</option>
                <option value="highest Estimating Ltv C">Lowest Estimating Ltv C</option>
                <option value="Lowest Estimating Ltv C">Lowest Estimating Ltv C</option>
                <option value="Highest Amount Sum">Highest Amount Sum</option>
                <option value="Lowest Amount Sum">Lowest Amount Sum</option>
                <option value="Highest Arpu">Highest Arpu</option>
                <option value="Lowest Arpu">Lowest Arpu</option>
                <option value="Highest Arpu 30 days">Highest Arpu 30 days</option>
                <option value="Lowest Arpu 30 days">Lowest Arpu 30 days</option>
                <option value="Highest Arpu 60 days">Highest Arpu 60 days</option>
                <option value="Lowest Arpu 60 days">Lowest Arpu 60 days</option>
                <option value="Highest Arpu 90 days">Highest Arpu 90 days</option>
                <option value="Lowest Arpu 90 days">Lowest Arpu 90 days</option>
  
              </select>
            </div>
            <div class="col-lg-1">
              <label class="invisible d-block">Search</label>
              <button type="submit" id="submitFilter" onclick="return filter()" class="btn btn-primary">Sort</button>
            </div>

          </div>
              
          @endif
      </div>
    </form>
  </div>

  <div class="adnet-report">
    <div class="">
      <div id="reportXls">
        <div class="d-flex align-items-center my-3">
          <span class="badge badge-secondary px-2 bg-primary text-uppercase">
            <a href="" class="text-white"> ALL {{ isset($adnetreport['input_request']['operator']['label']) ? $adnetreport['input_request']['operator']['label'] : "Operator"}} </a> {{date("F Y")}}
          </span>
          <span class="flex-fill ml-2 bg-primary w-100 font-weight-bold" style="height: 1px;"></span>
          <div class="text-right pl-2 buttonCSV">
            <button class="btn btn-sm btn-csv"   type="button" style="color:white; background-color:green" data-param="all"><i class="fa fa-file-excel-o"></i>Export as CSV</button>
          </div>
        </div>

        <div style="width: 100%;
        max-height: 600px;
        overflow-y: auto;
        border: 1px solid #ddd;">
          <table id="dtbl" class="datatableManual  table table-light  border-dark bg-white" border="1" cellspacing="0" width="100%">
            <thead class="bg-dark">
              <tr class="bg-dark">
                <th draggable="true"  style="font-size: 12px !important;position: sticky; top: 0; z-index: 1" @if($adnetreport['is_publisher'] ==0) onclick="sortTable(0)" @endif  ondragstart="handleDragStart(event)" class=" text-white bg-dark text-wrap p-3 font-normal" >ADNET  
                  @if ($adnetreport['is_publisher'] ==0)
                  <span class="arrow"> <i class="fa fa-sort"></i></span>                    
                   @endif 
                  
                </th>
                <th draggable="true"  style="font-size: 12px !important;position: sticky; top: 0; z-index: 1" @if($adnetreport['is_publisher'] ==0) onclick="sortTable(1)" @endif ondragstart="handleDragStart(event)" class="  text-white bg-dark  text-wrap p-3 font-normal">
                  PUBID Source 
                  @if ($adnetreport['is_publisher'] ==0)
                  <span class="arrow"> <i class="fa fa-sort"></i></span>                    
                   @endif 
                  
                </th>
  
                <th draggable="true"  style="font-size: 12px !important; position: sticky; top: 0; z-index: 1" @if($adnetreport['is_publisher'] ==0) onclick="sortTable(2)" @endif ondragstart="handleDragStart(event)" style="min-width: 100px !important" class="text-white bg-dark  text-wrap p-3 font-normal ">
                  <div class="row">
                    <div class="col-10">
                      Actual LTV({{$adnetreport['day']}} days)
                    </div>
                    @if ($adnetreport['is_publisher'] ==0)
                    <div class="col-2">
                      <span class="arrow"> <i class="fa fa-sort"></i></span>
  
                    </div>
                    
                    @endif

                  </div>
                </th>
                <th draggable="true"  style="font-size: 12px !important; position: sticky; top: 0; z-index: 1" @if($adnetreport['is_publisher'] ==0) onclick="sortTable(3)" @endif ondragstart="handleDragStart(event)" style="min-width: 100px !important" class="text-white bg-dark  text-wrap p-3 font-normal ">
                  <div class="row">
                    <div class="col-10">
                      Estimating LTV A 
                      @if(isset($adnetreport['details']) && !empty($adnetreport['details']) && $adnetreport['details'] != 1 && $adnetreport['details'] != 2 )
                      <span class="tooltip-icon" data-bs-toggle="tooltip" data-html="true" title="LTV (local currency ) = ARPU * ((1 - Churn Rate) / Churn Rate) <br> LTV (USD)= LTV (local currency) * usd">
                          <i class="fas fa-info-circle"></i>
                        </span>
                      @endif
                    </div>
                    @if ($adnetreport['is_publisher'] ==0)
                    <div class="col-2">
                      <span class="arrow"> <i class="fa fa-sort"></i></span>
  
                    </div>
                    
                    @endif

                  </div>

                 </th>
                 <th draggable="true"  style="font-size: 12px !important; position: sticky; top: 0; z-index: 1" @if($adnetreport['is_publisher'] ==0) onclick="sortTable(4)" @endif ondragstart="handleDragStart(event)" style="min-width: 100px !important" class="text-white bg-dark  text-wrap p-3 font-normal ">
                </span>
                <div class="row">
                  <div class="col-10">
                    Estimating LTV B
                    @if(isset($adnetreport['details']) && !empty($adnetreport['details']) && $adnetreport['details'] != 1 && $adnetreport['details'] != 2 )
                    <span class="tooltip-icon" data-bs-toggle="tooltip" data-html="true" title="Average ARPU = ARPU 90 days / 3 <br>LTV (Local Currency) = Average ARPU * AON(AON=6)  <br>LTV usd = LTV local currency * usd rate">
                    <i class="fas fa-info-circle"></i>
                    </span>
                    @endif
                  </div>
                  @if ($adnetreport['is_publisher'] ==0)
                  <div class="col-2">
                    <span class="arrow"> <i class="fa fa-sort"></i></span>

                  </div>
                  
                  @endif

                </div>
              </th>

                <th draggable="true"  style="font-size: 12px !important ; position: sticky; top: 0; z-index: 1" @if($adnetreport['is_publisher'] ==0) onclick="sortTable(5)" @endif ondragstart="handleDragStart(event)" style="min-width: 100px !important" class="text-white bg-dark  text-wrap p-3 font-normal ">
                  </span>
                  <div class="row">
                    <div class="col-10">
                      Estimating LTV C 
                      @if(isset($adnetreport['details']) && !empty($adnetreport['details']) && $adnetreport['details'] != 1 && $adnetreport['details'] != 2 )
                      <span class="tooltip-icon"  data-bs-toggle="tooltip" data-html="true" title="LTV (local currency ) = ARPU30 * ((1 - Churn Rate 30) / Churn Rate 30) <br>LTV (USD)= LTV (local currency) * usd">
                        <i class="fas fa-info-circle"></i>
                        <span>
                      @endif
                    </div>
                    @if ($adnetreport['is_publisher'] ==0)
                    <div class="col-2">
                      <span class="arrow"> <i class="fa fa-sort"></i></span>
  
                    </div>
                    
                    @endif

                  </div>
                </th>

                <th  @if($adnetreport['is_publisher'] ==0) onclick="sortTable(6)" @endif draggable="true"  style="font-size: 12px !important; position: sticky; top: 0; z-index: 1" ondragstart="handleDragStart(event)" style="min-width: 100px !important"  class=" text-white bg-dark  text-wrap p-3 font-normal ">
                  <div class="row">
                    <div class="col-10">
                      CAC
                      @if(isset($adnetreport['details']) && !empty($adnetreport['details']) && $adnetreport['details'] != 1 && $adnetreport['details'] != 2 )
                      <span class="tooltip-icon" data-bs-toggle="tooltip" data-html="true" title="CAC = Cost Campaign / MO (Acquired Subs)">
                          <i class="fas fa-info-circle"></i>
                        </span>
                      @endif
                    </div>
                    @if ($adnetreport['is_publisher'] ==0)
                    <div class="col-2">
                      <span class="arrow"> <i class="fa fa-sort"></i></span>
                      
                    </div>
                    
                    @endif
                  </div>
                </th>
                <th  @if($adnetreport['is_publisher'] ==0) onclick="sortTable(7)" @endif draggable="true"  style="font-size: 12px !important; position: sticky; top: 0; z-index: 1" ondragstart="handleDragStart(event)" style="min-width: 100px !important"  class=" text-white bg-dark text-center text-wrap p-3 font-normal ">
                  <div class="row">
                    <div class="col-10">
                      E Margin
                      @if(isset($adnetreport['details']) && !empty($adnetreport['details']) && $adnetreport['details'] != 1 && $adnetreport['details'] != 2 )
                      <span class="tooltip-icon" data-bs-toggle="tooltip" data-html="true" title="LTV A - CAC <br> LTV B - CAC <br> LTC C - CAC">
                          <i class="fas fa-info-circle"></i>
                        </span>
                      @endif
                    </div>
                    @if ($adnetreport['is_publisher'] ==0)
                    <div class="col-2">
                      <span class="arrow"> <i class="fa fa-sort"></i></span>
                      
                    </div>
                    
                    @endif
                  </div>
                </th>
                <th  @if($adnetreport['is_publisher'] ==0) onclick="sortTable(8)" @endif draggable="true"  style="font-size: 12px !important; position: sticky; top: 0; z-index: 1" ondragstart="handleDragStart(event)" style="min-width: 100px !important"  class=" text-white bg-dark  text-wrap p-3 font-normal ">
                  <div class="row">
                    <div class="col-10">
                      ROI
                      @if(isset($adnetreport['details']) && !empty($adnetreport['details']) && $adnetreport['details'] != 1 && $adnetreport['details'] != 2 )
                      <span class="tooltip-icon" data-bs-toggle="tooltip" data-html="true" title="ROI = CAC / (arpu90(net)/3)">
                          <i class="fas fa-info-circle"></i>
                        </span>
                      @endif
                    </div>
                    @if ($adnetreport['is_publisher'] ==0)
                    <div class="col-2">
                      <span class="arrow"> <i class="fa fa-sort"></i></span>
                      
                    </div>
                    
                    @endif
                  </div>
                </th>
                <th  @if($adnetreport['is_publisher'] ==0) onclick="sortTable(9)" @endif draggable="true"  style="font-size: 12px !important; position: sticky; top: 0; z-index: 1" ondragstart="handleDragStart(event)" style="min-width: 100px !important"  class=" text-white bg-dark  text-wrap p-3 font-normal ">Cost Campaign ($)
                  @if ($adnetreport['is_publisher'] ==0)
                      <span class="arrow"> <i class="fa fa-sort"></i></span>                    
                    @endif
                </th>
                <th  @if($adnetreport['is_publisher'] ==0) onclick="sortTable(10)" @endif draggable="true"  style="font-size: 12px !important; position: sticky; top: 0; z-index: 1" ondragstart="handleDragStart(event)" style="min-width: 100px !important"  class=" text-white bg-dark  text-wrap p-3 font-normal ">ACQUIRED SUBS COUNT
                  @if ($adnetreport['is_publisher'] ==0)
                      <span class="arrow"> <i class="fa fa-sort"></i></span>                    
                    @endif
                </th>
                <th  @if($adnetreport['is_publisher'] ==0) onclick="sortTable(11)" @endif draggable="true"  style="font-size: 12px !important; position: sticky; top: 0; z-index: 1"ondragstart="handleDragStart(event)" style="min-width: 100px !important" class="text-white bg-dark  text-wrap p-3 font-normal  "> AMOUNT SUM GMV {{empty($adnetreport['omr']) ? "" : "(" . $adnetreport['omr'] .")"}} 
                  @if ($adnetreport['is_publisher'] ==0)
                      <span class="arrow"> <i class="fa fa-sort"></i></span>                    
                    @endif
                </th>
                <th  @if($adnetreport['is_publisher'] ==0) onclick="sortTable(12)" @endif draggable="true"  style="font-size: 12px !important; position: sticky; top: 0; z-index: 1"ondragstart="handleDragStart(event)" style="min-width: 100px !important" class=" text-white bg-dark  text-wrap p-3 font-normal">RETAINED SUBS COUNT
                    @if ($adnetreport['is_publisher'] ==0)
                      <span class="arrow"> <i class="fa fa-sort"></i></span>    
                    @endif
                </th>
                <th  @if($adnetreport['is_publisher'] ==0) onclick="sortTable(13)" @endif draggable="true"  style="font-size: 12px !important; position: sticky; top: 0; z-index: 1"ondragstart="handleDragStart(event)" style="min-width: 100px !important" class=" text-white bg-dark  text-wrap p-3 font-normal ">CHURN (%)({{$adnetreport['day'] }} days)
                      @if ($adnetreport['is_publisher'] ==0)
                        <span class="arrow"> <i class="fa fa-sort"></i></span>
                      
                      @endif

                </th>
                <th @if($adnetreport['is_publisher'] == 0) onclick="sortTable(14)" @endif draggable="true"  style="font-size: 12px !important; position: sticky; top: 0; z-index: 1"ondragstart="handleDragStart(event)" style="min-width: 100px !important" class=" text-white bg-dark  text-wrap p-3 font-normal "> CHURN (30days) (%)
                    @if ($adnetreport['is_publisher'] ==0)
                      <span class="arrow"> <i class="fa fa-sort"></i></span>                    
                    @endif
                </th>
                <th @if($adnetreport['is_publisher'] == 0) onclick="sortTable(15)" @endif draggable="true"  style="font-size: 12px !important; position: sticky; top: 0; z-index: 1"ondragstart="handleDragStart(event)" style="min-width: 100px !important" class=" text-white bg-dark  text-wrap p-3 font-normal ">
                  <div class="row">
                    <div class="col-10">
                      ARPU ({{$adnetreport['day']}} days)   
                    </div>
                    @if ($adnetreport['is_publisher'] ==0)
                    <div class="col-2">
                      <span class="arrow"> <i class="fa fa-sort"></i></span>
  
                    </div>
                    
                    @endif

                  </div>

                </th>
                <th @if($adnetreport['is_publisher'] == 0) onclick="sortTable(16)" @endif draggable="true"  style="font-size: 12px !important; position: sticky; top: 0; z-index: 1"ondragstart="handleDragStart(event)" style="min-width: 100px !important" class=" text-white bg-dark  text-wrap p-3 font-normal ">
                  <div class="row">
                    <div class="col-10">
                      ARPU (30 days) 
  
                    </div>
                    @if ($adnetreport['is_publisher'] ==0)
                    <div class="col-2">
                      <span class="arrow"> <i class="fa fa-sort"></i></span>
  
                    </div>
                    
                    @endif

                  </div>
                </th>
                <th @if($adnetreport['is_publisher'] == 0) onclick="sortTable(17)" @endif draggable="true"  style="font-size: 12px !important; position: sticky; top: 0; z-index: 1"ondragstart="handleDragStart(event)" style="min-width: 100px !important" class=" text-white bg-dark  text-wrap p-3 font-normal ">
                  <div class="row">
                    <div class="col-10">
                      ARPU (60 days) 
  
                    </div>
                    @if ($adnetreport['is_publisher'] ==0)
                    <div class="col-2">
                      <span class="arrow"> <i class="fa fa-sort"></i></span>
  
                    </div>
                    
                    @endif

                  </div>
                </th>
                <th @if($adnetreport['is_publisher'] == 0) onclick="sortTable(18)" @endif draggable="true"  style="font-size: 12px !important; position: sticky; top: 0; z-index: 1"ondragstart="handleDragStart(event)" style="min-width: 100px !important" class=" text-white bg-dark  text-wrap p-3 font-normal ">
                  <div class="row">
                    <div class="col-10">
                      ARPU (90 days) 
  
                    </div>
                    @if ($adnetreport['is_publisher'] ==0)
                    <div class="col-2">
                      <span class="arrow"> <i class="fa fa-sort"></i></span>
  
                    </div>
                    
                    @endif

                  </div>
                </th>

              </tr>
            </thead>
            @php
              $currency = empty($adnetreport['omr']) ? "" : "" . $adnetreport['omr'] .""
            @endphp
            @if(isset($adnetreport['details']) && !empty($adnetreport['details']) && $adnetreport['details'] != 1 && $adnetreport['details'] != 2 )
            @foreach ($adnetreport['details'] as $key => $details)
            @php
              $adnetArray=explode(' ',isset($details['adnet'])?$details['adnet']['adnet']:'');
              $adnetClass0= isset($adnetArray[0])? $adnetArray[0]:'';
              $adnetClass1=isset($adnetArray[1])?'_'.$adnetArray[1]:'';
              $adnetClass2=isset($adnetArray[2])?'_'.$adnetArray[2]:'';
              $adnetClass=$adnetClass0.$adnetClass1.$adnetClass2;
            @endphp
            <tbody id="{{ isset($adnetClass) ? strpos($adnetClass, '.') ? str_replace('.', '_', $adnetClass) : $adnetClass : '' }}_adsTblBdy">
              <tr>
                <td class="align-middle">{{$details['adnet']['adnet'] }}</td>
                @if (count($details['publisher']) == 1)
                <td class="align-middle">{{$details['adnet']['publisher_name']}}</td>
                @else 
                <td  class="align-middle"><span class="ossbtn" data-param="{{ isset($adnetClass) ? strpos($adnetClass, '.') ? str_replace('.', '_', $adnetClass) : $adnetClass : '' }}" style="cursor:pointer;">@if(count($details)>0)<strong>+</strong>@endif</span> Total({{count($details['publisher'])}})</td>
                @endif
                
                <td class="align-middle">
                  {{ isset($details['adnet']['actual_ltv']) ? number_format($details['adnet']['actual_ltv'],2) . " " . $currency : 'N/A' }} / {{ isset($details['adnet']['actual_ltv_usd']) ? number_format($details['adnet']['actual_ltv_usd'],2) . " USD": 'N/A' }}
                  <br>
                  {{ isset($details['adnet']['actual_ltv']) ? number_format($details['adnet']['actual_ltv'] * $adnetreport['operator_revenue_share'],2) . " " . $currency : 'N/A' }} / {{ isset($details['adnet']['actual_ltv_usd']) ? number_format($details['adnet']['actual_ltv_usd'] * $adnetreport['operator_revenue_share'],2) . " USD": 'N/A' }}
                </td>
                <td class="align-middle">
                  {{ isset($details['adnet']['ltv_forecast_a']) ? number_format($details['adnet']['ltv_forecast_a'],2) . " " . $currency : 'N/A' }} / {{ isset($details['adnet']['ltv_forecast_a_usd']) ? number_format($details['adnet']['ltv_forecast_a_usd'],2) . " USD": 'N/A' }}
                  <br>
                  {{ isset($details['adnet']['ltv_forecast_a']) ? number_format($details['adnet']['ltv_forecast_a'] * $adnetreport['operator_revenue_share'],2) . " " . $currency : 'N/A' }} / {{ isset($details['adnet']['ltv_forecast_a_usd']) ? number_format($details['adnet']['ltv_forecast_a_usd']  * $adnetreport['operator_revenue_share'],2) . " USD": 'N/A' }}
                </td>
                <td class="align-middle">
                  {{ isset($details['adnet']['ltv_forecast']) ? number_format($details['adnet']['ltv_forecast'],2) . " " . $currency : 'N/A' }} / {{ isset($details['adnet']['ltv_forecast_usd']) ? number_format($details['adnet']['ltv_forecast_usd'] ,2) . " USD": 'N/A' }}
                  <br>
                  {{ isset($details['adnet']['ltv_forecast']) ? number_format($details['adnet']['ltv_forecast'] * $adnetreport['operator_revenue_share'],2) . " " . $currency : 'N/A' }} / {{ isset($details['adnet']['ltv_forecast_usd']) ? number_format($details['adnet']['ltv_forecast_usd'] * $adnetreport['operator_revenue_share'] ,2) . " USD": 'N/A' }}
                </td>
                <td class="align-middle">
                  {{ isset($details['adnet']['ltv_forecast_c']) ? number_format($details['adnet']['ltv_forecast_c'],2) . " " . $currency : 'N/A' }} / {{ isset($details['adnet']['ltv_forecast_c_usd']) ? number_format($details['adnet']['ltv_forecast_c_usd'],2) . " USD": 'N/A' }}
                  <br>
                  {{ isset($details['adnet']['ltv_forecast_c']) ? number_format($details['adnet']['ltv_forecast_c'] * $adnetreport['operator_revenue_share'],2) . " " . $currency : 'N/A' }} / {{ isset($details['adnet']['ltv_forecast_c_usd']) ? number_format($details['adnet']['ltv_forecast_c_usd'] * $adnetreport['operator_revenue_share'],2) . " USD": 'N/A' }}
                </td>
                <td class="align-middle">{{ isset($details['adnet']['cac']) ? number_format($details['adnet']['cac'],2) : 0 }}</td>
                <?php
                $values = [
                    [
                        'value' => isset($details['adnet']['e_margin_a']) ? $details['adnet']['e_margin_a'] : 0,
                        'key' => "LTV A"
                    ],
                    [
                        'value' => isset($details['adnet']['e_margin_b']) ? $details['adnet']['e_margin_b'] : 0,
                        'key' => "LTV B"
                    ],
                    [
                        'value' => isset($details['adnet']['e_margin_c']) ? $details['adnet']['e_margin_c'] : 0,
                        'key' => "LTV C"
                    ],
                ];
                
                usort($values, function($a, $b) {
                    return $a['value'] <=> $b['value'];
                });
                ?>
                
                <td class="align-middle">
                  <?php foreach ($values as $index => $item): ?>
                    <span class="<?= $item['value'] < 0 ? 'text-danger' : 'text-success' ?>">
                        <?= number_format($item['value'], 2) ?>
                    </span> (<?= $item['key'] ?>)
                    <?php if ($index < count($values) - 1): ?> | <?php endif; ?>
                  <?php endforeach; ?>
                </td>
                <td class="align-middle">{{ isset($details['adnet']['roi']) ? number_format($details['adnet']['roi'],2) : 0 }}</td>
                <td class="align-middle">{{ isset($details['adnet']['cost_campaign']) ? number_format($details['adnet']['cost_campaign'],2) : 0 }}</td>

                <td class="align-middle"><a  target="_blank" style="cursor: pointer;" onclick="openInNewWindow('{{ route('report.detail.subs', [
                  'country' => $adnetreport['params_query']['country'],
                  'operator' => $adnetreport['params_query']['operator'],
                  'service' => $adnetreport['params_query']['service'],
                  'cycle' => $adnetreport['params_query']['cycle'],
                  'adnet' => $details['adnet']['adnet'],
                  'from' => $adnetreport['params_query']['from'],
                  'to' => $adnetreport['params_query']['to'],
                  'renewal' => $adnetreport['params_query']['renewal']]) }}')">{{ isset($details['adnet']['acquired_subs_count']) ? number_format($details['adnet']['acquired_subs_count']) : 'N/A' }} <i class="fa fa-info-circle"></i></a>
                </td>
                {{-- <td class="align-middle">{{ isset($details['adnet']['acquired_subs_count']) ? number_format($details['adnet']['acquired_subs_count']) : 'N/A' }}</td> --}}
                <td class="align-middle">{{ isset($details['adnet']['amount_sum']) ? number_format($details['adnet']['amount_sum'],2) : 'N/A' }}</td>
                <td class="align-middle">{{ isset($details['adnet']['retained_subs_count']) ? number_format($details['adnet']['retained_subs_count']) : 'N/A' }}</td>
                <td class="align-middle">{{ isset($details['adnet']['churn']) ? number_format($details['adnet']['churn'],2) : 'N/A' }}</td>
                <td class="align-middle">{{ isset($details['adnet']['churn']) ? number_format($details['adnet']['churn_30'],2) : 'N/A' }}</td>
                <td class="align-middle">
                  {{ isset($details['adnet']['arpu']) ? number_format($details['adnet']['arpu'],2) . " ". $currency : 'N/A' }} / {{ isset($details['adnet']['arpu_usd']) ? number_format($details['adnet']['arpu_usd'],2) . " ". "USD" : 'N/A' }}
                  <br>
                  {{ isset($details['adnet']['arpu']) ? number_format($details['adnet']['arpu'] * $adnetreport['operator_revenue_share'],2) . " ". $currency : 'N/A' }} / {{ isset($details['adnet']['arpu_usd']) ? number_format(($details['adnet']['arpu_usd'] * $adnetreport['operator_revenue_share']),2) . " USD" : 'N/A' }}
                </td>
                <td class="align-middle">
                  {{ isset($details['adnet']['arpu_30']) ? number_format($details['adnet']['arpu_30'],2) . " ". $currency : 'N/A' }} / {{ isset($details['adnet']['arpu_usd_30']) ? number_format($details['adnet']['arpu_usd_30'],2) . " ". "USD" : 'N/A' }}
                  <br>
                  {{ isset($details['adnet']['arpu_30']) ? number_format($details['adnet']['arpu_30'] * $adnetreport['operator_revenue_share'],2) . " ". $currency : 'N/A' }} / {{ isset($details['adnet']['arpu_usd_30']) ? number_format(($details['adnet']['arpu_usd_30'] * $adnetreport['operator_revenue_share']),2) . " USD": 'N/A' }}
                </td>
                <td class="align-middle">
                  {{ isset($details['adnet']['arpu_60']) ? number_format($details['adnet']['arpu_60'],2) . " ". $currency : 'N/A' }} / {{ isset($details['adnet']['arpu_usd_60']) ? number_format($details['adnet']['arpu_usd_60'],2) . " ". "USD" : 'N/A' }}

                  <br>
                  {{ isset($details['adnet']['arpu_60']) ? number_format($details['adnet']['arpu_60'] * $adnetreport['operator_revenue_share'] ,2) . " ". $currency : 'N/A' }} / {{ isset($details['adnet']['arpu_usd_60']) ? number_format(($details['adnet']['arpu_usd_60'] * $adnetreport['operator_revenue_share']),2) . " USD" : 'N/A' }}
                </td>
                <td class="align-middle">
                  {{ isset($details['adnet']['arpu_90']) ? number_format($details['adnet']['arpu_90'],2) . " ". $currency : 'N/A' }} / {{ isset($details['adnet']['arpu_usd_90']) ? number_format($details['adnet']['arpu_usd_90'],2) . " ". "USD" : 'N/A' }}

                  <br>
                  {{ isset($details['adnet']['arpu_90']) ? number_format($details['adnet']['arpu_90'] * $adnetreport['operator_revenue_share'],2) . " ". $currency : 'N/A' }} / {{ isset($details['adnet']['arpu_usd_90']) ? number_format(($details['adnet']['arpu_usd_90'] * $adnetreport['operator_revenue_share']),2) . " USD" : 'N/A' }}
                </td>
                {{-- <td class="align-middle">{{ isset($details['adnet']['arpu_usd']) ? number_format(($details['adnet']['arpu_usd'] * $adnetreport['operator_revenue_share']),2) : 'N/A' }}</td>
                <td class="align-middle">{{ isset($details['adnet']['arpu_usd_30']) ? number_format(($details['adnet']['arpu_usd_30'] * $adnetreport['operator_revenue_share']),2) : 'N/A' }}</td>
                <td class="align-middle">{{ isset($details['adnet']['arpu_usd_60']) ? number_format(($details['adnet']['arpu_usd_60'] * $adnetreport['operator_revenue_share']),2) : 'N/A' }}</td>
                <td class="align-middle">{{ isset($details['adnet']['arpu_usd_90']) ? number_format(($details['adnet']['arpu_usd_90'] * $adnetreport['operator_revenue_share']),2) : 'N/A' }}</td> --}}
              </tr>
              @if (count($details['publisher']) > 1)   
              @foreach ($details['publisher'] as $pubid)
              <tr style="" class="{{ isset($adnetClass) ? strpos($adnetClass, '.') ? str_replace('.', '_', $adnetClass) : $adnetClass : '' }}  expandable operator-odd-bg" style="display: none;">
                <td class="subs align-middle">{{$details['adnet']['adnet'] }}</td>
                <td class="subs align-middle">{{isset($pubid['publisher'])? $pubid['publisher']: "N/A"}}</td>
                {{-- <td class="subs align-middle">{{isset($pubid['actual_ltv']) ? number_format($pubid['actual_ltv'],2) : "N/A"}}</td> --}}
                <td class="subs">
                  {{isset($pubid['actual_ltv'])? number_format($pubid['actual_ltv'], 2) ." " . $currency:"N/A"}} / {{isset($pubid['actual_ltv_usd'])? number_format($pubid['actual_ltv_usd'], 2). " USD":"N/A"}}
                  <br>
                  {{isset($pubid['actual_ltv'])? number_format($pubid['actual_ltv'] * $adnetreport['operator_revenue_share'], 2) ." " . $currency:"N/A"}} / {{isset($pubid['actual_ltv_usd'])? number_format($pubid['actual_ltv_usd'] * $adnetreport['operator_revenue_share'],2). " USD":"N/A"}}
                </td>
                <td class="subs">
                  {{isset($pubid['ltv_forecast_a'])? number_format($pubid['ltv_forecast_a'], 2) ." " . $currency:"N/A"}} / {{isset($pubid['ltv_forecast_a_usd'])? number_format($pubid['ltv_forecast_a_usd'],2). " USD":"N/A"}}
                  <br>
                  {{isset($pubid['ltv_forecast_a'])? number_format($pubid['ltv_forecast_a'] * $adnetreport['operator_revenue_share'], 2) ." " . $currency:"N/A"}} / {{isset($pubid['ltv_forecast_a_usd'])? number_format($pubid['ltv_forecast_a_usd'] * $adnetreport['operator_revenue_share'],2). " USD":"N/A"}}
                </td>
                <td class="subs">
                  {{isset($pubid['ltv_forecast'])? number_format($pubid['ltv_forecast'], 2) ." " . $currency:"N/A"}} / {{isset($pubid['ltv_forecast_usd'])? number_format($pubid['ltv_forecast_usd'],2). " USD":"N/A"}}
                  <br>
                  {{isset($pubid['ltv_forecast'])? number_format($pubid['ltv_forecast'] * $adnetreport['operator_revenue_share'], 2) ." " . $currency:"N/A"}} / {{isset($pubid['ltv_forecast_usd'])? number_format($pubid['ltv_forecast_usd'] * $adnetreport['operator_revenue_share'],2). " USD":"N/A"}}
                </td>
                <td class="subs">
                  {{isset($pubid['ltv_forecast_c'])? number_format($pubid['ltv_forecast_c'], 2) ." " . $currency:"N/A"}} / {{isset($pubid['ltv_forecast_c_usd'])? number_format($pubid['ltv_forecast_c_usd'],2). " USD":"N/A"}}
                  <br>
                  {{isset($pubid['ltv_forecast_c'])? number_format($pubid['ltv_forecast_c'] * $adnetreport['operator_revenue_share'], 2) ." " . $currency:"N/A"}} / {{isset($pubid['ltv_forecast_c_usd'])? number_format($pubid['ltv_forecast_c_usd'] * $adnetreport['operator_revenue_share'],2 ). " USD":"N/A"}}
                </td>
                <td class="subs align-middle">-</td>
                <td class="subs align-middle">-</td>
                <td class="subs align-middle">-</td>
                <td class="subs align-middle">-</td>
                <td class="subs align-middle"><a target="_blank"  style="cursor: pointer;" onclick="openInNewWindow('{{ route('report.detail.subs', [
                  'country' => $adnetreport['params_query']['country'],
                  'operator' => $adnetreport['params_query']['operator'],
                  'service' => $adnetreport['params_query']['service'],
                  'cycle' => $adnetreport['params_query']['cycle'],
                  'adnet' => $details['adnet']['adnet'],
                  'from' => $adnetreport['params_query']['from'],
                  'to' => $adnetreport['params_query']['to'],
                  'renewal' => $adnetreport['params_query']['renewal'],
                  'publisher' => $pubid['publisher']]) }}  ')">{{isset($pubid['acquired_subs_count'])? number_format($pubid['acquired_subs_count']):"N/A"}} <i class="fa fa-info-circle"></i></a>
                </td>
                <td class="subs align-middle">{{isset($pubid['amount_sum'])? number_format($pubid['amount_sum'],2) :"N/A"}}</td>
                <td class="subs align-middle">{{isset($pubid['retained_subs_count'])? number_format($pubid['retained_subs_count']):"N/A"}}</td>
                <td class="subs align-middle">{{isset($pubid['churn'])?  number_format($pubid['churn'],2) : 'N/A' }}</td>
                <td class="subs align-middle">{{isset($pubid['churn_30'])?  number_format($pubid['churn_30'],2) : 'N/A' }}</td>
                <td class="subs align-middle">
                  {{isset($pubid['arpu'])?  number_format($pubid['arpu'],2) ." " . $currency : 'N/A' }} / {{isset($pubid['arpu_usd'])? number_format(($pubid['arpu_usd']),2) . " USD" : 'N/A' }}
                  <br>
                  {{isset($pubid['arpu'])?  number_format($pubid['arpu'] * $adnetreport['operator_revenue_share'],2) ." " . $currency : 'N/A' }} / {{isset($pubid['arpu_usd'])? number_format(($pubid['arpu_usd'] * $adnetreport['operator_revenue_share']),2) . " USD" : 'N/A' }}
                </td>
                <td class="subs">
                  {{isset($pubid['arpu_30'])?  number_format($pubid['arpu_30'],2) ." " . $currency : 'N/A' }} / {{isset($pubid['arpu_usd_30'])? number_format(($pubid['arpu_usd_30']),2) . " USD" : 'N/A' }}
                  <br>
                  {{isset($pubid['arpu_30'])?  number_format($pubid['arpu_30'] * $adnetreport['operator_revenue_share'],2) ." " . $currency : 'N/A' }} / {{isset($pubid['arpu_usd_30'])? number_format(($pubid['arpu_usd_30'] * $adnetreport['operator_revenue_share']),2) . " USD": 'N/A' }}
                </td>
                <td class="subs">
                  {{isset($pubid['arpu_60'])?  number_format($pubid['arpu_60'],2) ." " . $currency : 'N/A' }} / {{isset($pubid['arpu_usd_60'])? number_format(($pubid['arpu_usd_60']),2) . " USD" : 'N/A' }}
                  <br>
                  {{isset($pubid['arpu_60'])?  number_format($pubid['arpu_60'] * $adnetreport['operator_revenue_share'],2) ." " . $currency : 'N/A' }} / {{isset($pubid['arpu_usd_60'])? number_format(($pubid['arpu_usd_60'] * $adnetreport['operator_revenue_share']),2) . " USD": 'N/A' }}
                </td>
                <td class="subs">
                  {{isset($pubid['arpu_90'])?  number_format($pubid['arpu_90'],2) ." " . $currency : 'N/A' }} / {{isset($pubid['arpu_usd_90'])? number_format(($pubid['arpu_usd_90']),2) . " USD" : 'N/A' }}
                  <br>
                  {{isset($pubid['arpu_90'])?  number_format($pubid['arpu_90'] * $adnetreport['operator_revenue_share'], 2) ." " . $currency : 'N/A' }} / {{isset($pubid['arpu_usd_90'])? number_format(($pubid['arpu_usd_90'] * $adnetreport['operator_revenue_share']),2) . " USD" : 'N/A' }}
                </td>
                {{-- <td class="subs align-middle">{{isset($pubid['arpu_usd'])? number_format(($pubid['arpu_usd'] * $adnetreport['operator_revenue_share']),4) : 'N/A' }}</td>
                <td class="subs align-middle">{{isset($pubid['arpu_usd_30'])? number_format(($pubid['arpu_usd_30'] * $adnetreport['operator_revenue_share']),4) : 'N/A' }}</td>
                <td class="subs align-middle">{{isset($pubid['arpu_usd_60'])? number_format(($pubid['arpu_usd_60'] * $adnetreport['operator_revenue_share']),4) : 'N/A' }}</td>
                <td class="subs align-middle">{{isset($pubid['arpu_usd_90'])? number_format(($pubid['arpu_usd_90'] * $adnetreport['operator_revenue_share']),4) : 'N/A' }}</td> --}}
              </tr>
              @endforeach
              @endif
              @endforeach
              @endif
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- modal empty data --}}
<div class="modal fade" id="emptyDataModal" tabindex="-1" role="dialog" aria-labelledby="emptyDataModalExample" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="emptyDataModalExample">Empty Data Arpu</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true" class="text-danger">&times;</span>
        </button>
      </div>
      <div class="modal-body text-dark text-center">Arpu data is empty</div>
      <div class="modal-footer">
        {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary"></button> --}}
      </div>
    </div>
  </div>
</div>

{{-- modal timeout --}}
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">API TIMEOUT</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true" class="text-danger">&times;</span>
        </button>
      </div>
      <div class="modal-body text-dark text-center">The API provider has a problem, Please re-submit.</div>
      <div class="modal-footer">
        {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary"></button> --}}
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="errorRangeDays" tabindex="-1" role="dialog" aria-labelledby="errorRangeDaysLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="errorRangeDaysLabel">Error Input</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true" class="text-danger">&times;</span>
        </button>
      </div>
      <div class="modal-body text-dark text-center">From date must be within the last 30 days from today.</div>
      <div class="modal-footer">
        {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary"></button> --}}
      </div>
    </div>
  </div>
</div>

<?php if(request()->has('option_date') && request()->get('option_date') == "Today"): ?>
<script>
  $(document).ready(function() {
    $('#date_range_faield').addClass('gu-hide');
  });
</script>
<?php endif; ?>

@if ($adnetreport['details'] ==1 || $adnetreport["details"] ==2)
<script>
  $(window).on('load', function() {
    // $('#exampleModal').modal('show');
    var country = $("#adnet_country_name").attr("disabled", true);
    var operator = $("#adnet_operator_name").attr("disabled", true);
    var service = $("#adnet_service_name").attr("disabled", true);
    var keyword = $("#keyword_service").attr("disabled", true);
    var from = $("#from_date").attr("disabled", true);
    var to = $("#to_date").attr("disabled", true);
    Swal.fire({
        icon: 'error',
        text: 'The API provider has a problem, Please re-submit.',
        toast: true,
        position: 'top-end',
        showConfirmButton: true,
        confirmButtonText: 'Retry',
        showCancelButton: true, // Menampilkan tombol Cancel
        cancelButtonText: 'Close',
    }).then((result) => {
        if (result.isConfirmed) { // Jika tombol Cancel ditekan

            submitArpu();
            location.reload();
        }
        if(result.isDismissed){
          var country = $("#adnet_country_name").removeAttr("disabled");
          var operator = $("#adnet_operator_name").removeAttr("disabled");
          var service = $("#adnet_service_name").removeAttr("disabled");
          var keyword = $("#keyword_service").removeAttr("disabled");
          var from = $("#from_date").removeAttr("disabled");
          var to = $("#to_date").removeAttr("disabled");
        }
    });
  });
</script>    
@endif

@if ($adnetreport['emptyData'] == 1)  
<script>
  $(window).on('load', function() {
    Swal.fire({
        icon: 'warning',
        text: 'Arpu data is empty.',
        toast: true,
        position: 'top-end',
        showConfirmButton: true,
        
    });

    // $('#emptyDataModal').modal('show');
  });
</script>
@endif

<script>
  function customRange(){
    var option = $("#option_date_range").val();
    console.log(option);
    if(option == "Custom Range"){
      $("#adnet_renewal_datepicker").removeAttr('disabled');
    }else {
      $("#adnet_renewal_datepicker").attr('disabled', '');
    }
  }
</script>

<script>
  $('.btn-csv').click(function(){
    // var table = $(this).attr('data-table')
    country = $("#adnet_country_name option:selected").text();
    operator = $("#adnet_operator_name option:selected").text();
    service = $("#adnet_service_name option:selected").text();
    $('.datatableManual').tableExport({
        fileName : "adnetreport_"+country + "_" +  operator + "_" + service,
        type : 'csv',
    });
  })
</script>
<script>
  function sortTable(n) {
    var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
    table = document.getElementById("dtbl");
    switching = true;
    dir = "asc";
    while (switching) {
        switching = false;
        rows = table.rows;
        for (i = 1; i < (rows.length - 1); i++) {
            shouldSwitch = false;
            x = rows[i].getElementsByTagName("TD")[n].innerText;
            y = rows[i + 1].getElementsByTagName("TD")[n].innerText;
            var xNumber = parseFloat(x.replace(/[^\d.]/g, ""));
            var yNumber = parseFloat(y.replace(/[^\d.]/g, ""));
            if (!isNaN(xNumber) && !isNaN(yNumber)) {
                x = xNumber;
                y = yNumber;
            } else {
                x = x.toLowerCase();
                y = y.toLowerCase();
            }
            if (dir == "asc") {
                if (x > y) {
                    shouldSwitch = true;
                    break;
                }
            } else if (dir == "desc") {
                if (x < y) {
                    shouldSwitch = true;
                    break;
                }
            }
        }
        if (shouldSwitch) {
            rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
            switching = true;
            switchcount++;
        } else {
            if (switchcount == 0 && dir == "asc") {
                dir = "desc";
                switching = true;
            }
        }
    }
    toggleArrow(n, dir); // Panggil fungsi toggleArrow untuk mengubah panah

}
function toggleArrow(columnIndex, direction) {
    var arrows = document.querySelectorAll(".arrow");
    arrows.forEach(function(arrow) {
        arrow.innerHTML  = ''; // Hapus semua panah yang ada
    });
    var arrow = arrows[columnIndex]; // Ambil panah yang sesuai dengan kolom yang di-klik
    if (direction === "asc") {
        arrow.innerHTML  = ' <i class="fa fa-sort-up"></i> '; // Tambahkan panah ke atas
    } else {
        arrow.innerHTML  = ' <i class="fa fa-sort-down"></i> '; // Tambahkan panah ke bawah
    }
}
</script>
<script>
  $('#from_date, #to_date').on('change', function() {
    var fromDate = new Date($('#from_date').val());
    fromDate.setHours(0);
    fromDate.setMinutes(0);
    var toDate = new Date($('#to_date').val());
    toDate.setHours(0);
    toDate.setMinutes(0);

    var today = new Date();
    today.setHours(0);
    today.setMinutes(0);

    if (fromDate > toDate ) {
      // $('#errorFromTo').modal('show');
      $("#error-from").removeClass("d-none");
      // Reset the "From" date to the previous valid value
      $('#from_date').val("");
      // $('#adnet_to_datepicker').val($('#adnet_to_datepicker').data('prevValue'));
    } else if (fromDate <= toDate){
      $("#error-from").addClass("d-none");
    }

    if(toDate > today) {
      $("#error-to").removeClass("d-none");
      $('#to_date').val("");
    }else if(toDate <= today){
      $("#error-to").addClass("d-none");
    } 
  });
</script>

<script>
    var baseUrl = window.location.origin + "/";
    $(window).on('load', function() {
      var e = document.getElementById("adnet_country_name");
      var value = e.value;
      $.ajax({
        type: "POST",
        url: baseUrl+"report/mappingoperator",
        data:{'id':value, '_token': "{{ csrf_token() }}"},
        dataType: "json",
        success: function (responses) {
          $.each(responses.data, function(index,response){
                $("#adnet_operator_name").append('<option value="'+response.operator_id+'">'+response.operator+'</option>');
      
            })
  
        }
      })
      
    var value = $("#adnet_operator_name").val();
      $.ajax({
          type: "POST",
          url: baseUrl+"report/mappingservice",
          data:{'id':value, '_token': "{{ csrf_token() }}"},
          dataType: "json",
          success: function (responses) {
            $.each(responses.data, function(index,response){
              $("#adnet_service_name").append('<option value="'+response.service_id+'">'+response.service_name+'</option>');
            })
          }
      })
  
  // })
    var value = $("#adnet_operator_name").val();
    var id_service = $("#adnet_service_name").val();
      
    $.ajax({
        type: "POST",
        url: baseUrl+"report/mappingkeyword",
        data:{'id':value , "id_service" : id_service, "_token": "{{ csrf_token() }}"},
        dataType: "json",
        success: function (responses) {
          if(responses.data.length > 0) {
              $.each(responses.data, function(index,response){
                  $("#keyword_service").append('<option value="'+response.service_id+'">'+response.service_name+'</option>');
              });
          }
        },
    error: function(xhr, status, error) {
        console.error("Failed to fetch mapping keywords: ", xhr);
    }
    })
  })


</script>
<script>
      function countryChange(){
        var e = document.getElementById("adnet_country_name");
        var value = e.value;
        $.ajax({
          type: "POST",
          url: baseUrl+"report/mappingoperator",
          data:{'id':value, '_token': "{{ csrf_token() }}"},
          dataType: "json",
          success: function (responses) {
            document.getElementById('adnet_operator_name').innerHTML ='<option value="">Select Operator</option>';
            document.getElementById('adnet_service_name').innerHTML ='<option value="">Select Service</option>';
            document.getElementById('keyword_service').innerHTML ='<option value="">Select Keyword</option>';

            $.each(responses.data, function(index,response){
                 $("#adnet_operator_name").append('<option value="'+response.operator_id+'">'+response.operator+'</option>');
      
              })
    
          }
        })
    }    
    function operatorChange(){
      var value = $("#adnet_operator_name").val();
        $.ajax({
            type: "POST",
            url: baseUrl+"report/mappingservice",
            data:{'id':value, '_token': "{{ csrf_token() }}"},
            dataType: "json",
            success: function (responses) {
              // $("#adnet_service_name").empty();
              // $("#adnet_service_name").append('<option value="">Select Service</option>');    
              document.getElementById('adnet_service_name').innerHTML ='<option value="">Select Service</option>';
              document.getElementById('keyword_service').innerHTML ='<option value="">Select Keyword</option>';

              $.each(responses.data, function(index,response){
                $("#adnet_service_name").append('<option value="'+response.service_id+'">'+response.service_name+'</option>');
              })
            }
        })
      }
  
  // })
  function serviceChange(){
    var value = $("#adnet_operator_name").val();
    var id_service = $("#adnet_service_name").val();
    var service_name = $("#adnet_service_name option:selected").text();

    $.ajax({
        type: "POST",
        url: baseUrl+"report/mappingkeyword",
        data:{'id':value , "id_service" : id_service, '_token': "{{ csrf_token() }}"},
        dataType: "json",
        success: function (responses) {
          $("#keyword_service").empty();
          if(responses.data.length > 0) {
              $.each(responses.data, function(index,response){
                  $("#keyword_service").append('<option value="'+response.service_id+'">'+response.service_name+'</option>');
              });
          } else {
              $("#keyword_service").append('<option value="'+id_service+'">'+service_name+'</option>');
          }
        }
    })
  }
</script>

<script>
  function reset() {
    $('#adnet_country_name').val('');
  }

  function date_ranges() {
    var values = $('#date_range').val();

    if (values == 'custom_range') {
      $('#date_range_faield').removeClass('gu-hide');
    } else  {
      $('#date_range_faield').addClass('gu-hide');
    }
  }
</script>

<script>
  function submitArpu(){
    var country = $("#adnet_country_name").val();
    var operator = $("#adnet_operator_name").val();
    var service = $("#adnet_service_name").val();
    var from = $("#from_date").val();
    var to = $("#to_date").val();
    
    if(country != "" && operator != "" && service != "" && from != "" && to != "" ) {
      Swal.fire({
        title: '',
        allowOutsideClick: false,
        showConfirmButton: false,
        html: '<div style="display: flex; align-items: center; justify-content: center; height: 100px;"><i class="fas fa-spinner fa-spin" style="font-size: 3rem;color:white"></i></div>',
          onBeforeOpen: () => {
            Swal.showLoading();
          },
          didRender: () => {
            document.querySelector('.swal2-popup').style.padding = '0px';
            document.querySelector('.swal2-popup').style.background = 'transparent';
            document.querySelector('.swal2-html-container').style.overflow = 'hidden';
          }  
        });
      setTimeout(() => {
          Swal.close();
      }, 200000);
    }else {
      // return false;
    }
  }

  function filter() {
    var country = $("#adnet_country_name").val();
    var operator = $("#adnet_operator_name").val();
    var service = $("#adnet_service_name").val();
    var from = $("#from_date").val();
    var to = $("#to_date").val();
    
    if(country != "" && operator != "" && service != "" && from != "" && to != "" ) {
      Swal.fire({
        title: 'loading process',
        allowOutsideClick: false,
        showConfirmButton: false,

        onBeforeOpen: () => {
          Swal.showLoading();
        }
      });
      setTimeout(() => {
          Swal.close();
      }, 200000);
    }else {
      // return false;
    }
  }
</script>

<script>
    var dragCol = null;

    function handleDragStart(e) {
      
        dragCol = this;
        e.dataTransfer.effectAllowed = "move";
        e.dataTransfer.setData("text/html", this.outerHTML);
    }

    function handleDragOver(e) {
        if (e.preventDefault) {
            e.preventDefault();
        }
        e.dataTransfer.dropEffect = "move";
        return false;
    }

    function handleDrop(e) {
        if (e.stopPropagation) {
            e.stopPropagation();
        }
        if (dragCol !== this) {
            var sourceIndex = Array.from(dragCol.parentNode.children).indexOf(dragCol);
            var targetIndex = Array.from(this.parentNode.children).indexOf(this);

            var table = document.getElementById("dtbl");
            var rows = table.rows;
            for (var i = 0; i < rows.length; i++) {
                var sourceCell = rows[i].cells[sourceIndex];
                var targetCell = rows[i].cells[targetIndex];

                var tempHTML = sourceCell.innerHTML;
                sourceCell.innerHTML = targetCell.innerHTML;
                targetCell.innerHTML = tempHTML;
            }
        }
        $('.ossbtn').on('click', function () {
            var param = $(this).data('param');
            console.log(param);
            if ($(this).text() == '-') {
                $('.' + param).slideUp();
                $(this).html('<strong>+</strong>');
            } else {
                var test = $("." + param).sort((a, b) => $(b).find(".subs").text().replace(/\$/g, '').replace(/\,/g, '') - $(a).find(".subs").text().replace(/\$/g, '').replace(/\,/g, ''));
                console.log(test);
                $("." + param).sort((a, b) => $(b).find(".subs").text().replace(/\$/g, '').replace(/\,/g, '') - $(a).find(".subs").text().replace(/\$/g, '').replace(/\,/g, '')).appendTo("#" + param + "_adsTblBdy");
                $('.' + param).slideDown();
                $(this).html('<strong>-</strong>');
            }
        });
        return false;
    }

    var cols = document.querySelectorAll('th');
    cols.forEach(function(col) {
        col.addEventListener('dragstart', handleDragStart, false);
        col.addEventListener('dragover', handleDragOver, false);
        col.addEventListener('drop', handleDrop, false);
    });


</script>
<script>
  function openInNewWindow(url) {
    console.log(url);
    window.open(url, '_blank', 'toolbar=0,location=0,menubar=0,width=800,height=600');
  }
</script>
@endsection
