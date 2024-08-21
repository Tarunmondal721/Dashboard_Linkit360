@extends('layouts.admin')

@section('title')
  {{ __('Unkown Operator') }}
@endsection

@section('content')

<div class="page-content">
  <div class="page-title" style="margin-bottom:25px">
    <div class="row justify-content-between align-items-center">
      <div class="col-xl-4 col-lg-4 col-md-4 d-flex align-items-center justify-content-between justify-content-md-start mb-3 mb-md-0">
        <div class="d-inline-block">
          <h5 class="h4 d-inline-block font-weight-400 mb-0"><b>Unkown Operator</b></h5><br>
          <p class="d-inline-block font-weight-200 mb-0">Summary of Company Data</p>
        </div>
      </div>
      <div class="col-xl-8 col-lg-8 col-md-8 d-flex align-items-center justify-content-between justify-content-md-end"></div>
    </div>
  </div>
  @php
    $OperatorId = request()->get('operator');
    $start_date = request()->get('from');
    $end_date = request()->get('to');
  @endphp

  <div class="card shadow-sm mt-0">
    <div class="card-body">
      <div class="row">
        <div class="col-lg-3">
          <div class="form-group">
            <label for="operator">Operator</label>
            <select class="simple-multiple-select select2" name="operator" id="operator" <?php echo isset($OperatorId) ? 'value="'.$OperatorId.'"': '' ?>>
              <option value="">All Operator</option>
              @foreach ($operatorss as $operators)
              <option value="{{ $operators['id_operator']}}" <?php  echo isset($OperatorId) && ($operators['id_operator'] == $OperatorId) ? 'selected': '' ?>>{{ $operators['operator'] }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="col-lg-3">
          <div class="form-group">
            <label for="from">From</label>
            <input class="form-control form_datetime1" type="date" name="from" id="from" <?php echo isset($start_date) ? 'value="'.$start_date.'"': '' ?>>
          </div>
        </div>

        <div class="col-lg-3">
          <div class="form-group">
            <label for="to">To</label>
            <input class="form-control form_datetime1" type="date" name="to" id="to" <?php echo isset($end_date) ? 'value="'.$end_date.'"': '' ?>>
          </div>
        </div>

        <div class="col-lg-3">
          <label class="invisible d-block">Search</label>
          <button type="submit" class="btn btn-primary pnl_ctr_filter_btn" onclick="submit()"><i class="fa fa-search"></i>Search</button>
        </div>
      </div>
      <div class="row gu-hide" id="date_range_faield">
          <div class="col-lg-3">
              {{ Form::label('date', __('Form'),['class'=>'form-control-label']) }}
              {{ Form::text('date', null, array('class' => 'form-control datepicker','required'=>'required', 'id' =>'dateform')) }}
          </div>
          <div class="col-lg-3">
              {{ Form::label('date', __('To'),['class'=>'form-control-label']) }}
              {{ Form::text('date', null, array('class' => 'form-control datepicker','required'=>'required', 'id' =>'dateto')) }}
          </div>
      </div>
    </div>
  </div>

  <div class="card shadow-sm mt-0">
    <div class="card-body">
      <div class="card">
        <div class="table-responsive shadow-sm" id="all">
          @if(isset($sumemry) && !empty($sumemry))
          @foreach ($sumemry as $report)
          <div class="ptable">
            <div class="d-flex align-items-center my-3">
              <span class="badge badge-with-flag badge-secondary px-2 bg-primary text-uppercase">
                <a href="javascript:void(0);" class="text-white">{{$report['operator']}} </a>{{$report['month_string']}}
              </span>
              <span class="flex-fill ml-2 bg-primary w-100 font-weight-bold" style="height: 1px;"></span>
              <div class="text-right pl-2">
                <button class="btn btn-sm pnl-xls" style="color:white; background-color:green" data-param="{{$report['operator']}}"><i class="fa fa-file-excel-o"></i>Export as XLS</button>
              </div>
            </div>
            <div class="card">
              <div class="table-responsive shadow-sm pnlDataTbl" id="{{$report['operator']}}">
                <h1 style="display:none"></h1>
                <table class="table table-light m-0 font-13 table-text-no-wrap" id="pnlTbl">
                  <thead class="thead-dark">
                    <tr>
                      <th>Summary</th>
                      <th>Total</th>
                      <th>AVG</th>
                      <th>T.Mo.End</th>

                      @if(isset($no_of_days) && !empty($no_of_days))
                      @foreach ($no_of_days as $days)
                      <th>{{$days['no']}}</th>
                      @endforeach
                      @endif
                    </tr>
                  </thead>
                  <tbody>
                    <tr class="bg-young-blue end_user_revenue">
                      <td class="font-weight-bold">
                        <div class="text-with-sup">
                          <div class="text-left"><strong>GMV (USD)<sup><i class="ml-3 text-dark fa fa-info-circle" title="Revenue that we charge user excluding tax"></i></sup></strong></div>
                        </div>
                      </td>
                      <td class="gross_revenue_usd_total usd">{{numberConverter($report['end_user_rev_usd']['total'] ,3,'hosting_cost') }} </td>
                      <td class="gross_revenue_usd_avg">{{numberConverter($report['end_user_rev_usd']['t_mo_end'] ,3,'hosting_cost') }}</td>
                      <td class="gross_revenue_usd_month">{{numberConverter($report['end_user_rev_usd']['avg'] ,3,'hosting_cost') }}</td>

                      @if(isset($report['end_user_rev_usd']['dates']) && !empty($report['end_user_rev_usd']['dates']))
                      @foreach ($report['end_user_rev_usd']['dates'] as $end_user_rev_usd)
                      <td class="gross_revenue_usd_data {{$end_user_rev_usd['class']}}">{{numberConverter($end_user_rev_usd['value'] ,3,'hosting_cost') }}</td>
                      @endforeach
                      @endif
                    </tr>

                    <tr class="bg-young-blue gross_revenue">
                      <td class="font-weight-bold">
                        <div class="text-with-sup">
                          <strong class="text-with-sup">Gross Revenue (USD)<sup><i class="ml-3 text-dark fa fa-info-circle" title="Revenue after share"></i></sup></strong>
                        </div>
                      </td>
                      <td class="share_total">{{numberConverter( $report['gros_rev_usd']['total'] ,3,'hosting_cost') }}</td>
                      <td class="share_avg">{{numberConverter( $report['gros_rev_usd']['t_mo_end'] ,3,'hosting_cost') }}</td>
                      <td class="share_month">{{numberConverter( $report['gros_rev_usd']['avg'] ,3,'hosting_cost') }}</td>

                      @if(isset($report['gros_rev_usd']['dates']) && !empty($report['gros_rev_usd']['dates']))
                      @foreach ($report['gros_rev_usd']['dates'] as $gros_rev_usd)
                      <td class="share_data">{{numberConverter( $gros_rev_usd['value'] ,3,'hosting_cost') }}</td>
                      @endforeach
                      @endif
                    </tr>

                    <tr class="bg-young-blue">
                      <td class="font-weight-bold">
                        <strong>Campaign MO</strong>
                      </td>
                      <td class="reg_total">0</td>
                      <td class="reg_avg">0</td>
                      <td class="reg_month">0</td>

                      @if(isset($report['mo']['dates']) && !empty($report['mo']['dates']))
                      @foreach ($report['mo']['dates'] as $mo)
                      <td class="reg_data">{{numberConverter( $mo['value'] ,2,'hosting_cost') }}</td>
                      @endforeach
                      @else
                      @foreach ($no_of_days as $days)
                      <td>0</td>
                      @endforeach
                      @endif
                    </tr>

                    <tr class="bg-young-red cost_campaign">
                      <td class="font-weight-bold">
                        <div class="text-with-sup">
                          <div class="text-left"><strong>Cost Campaign (USD)</strong></div>
                        </div>
                      </td>
                      <td class="cost_total cost">{{numberConverter( $report['cost_campaign']['total'] ,3,'hosting_cost') }}</td>
                      <td class="cost_avg">{{numberConverter( $report['cost_campaign']['avg'] ,3,'hosting_cost') }}</td>
                      <td class="cost_month">{{numberConverter( $report['cost_campaign']['t_mo_end'] ,3,'hosting_cost') }}</td>

                      @if(isset($report['cost_campaign']['dates']) && !empty($report['cost_campaign']['dates']))
                      @foreach ($report['cost_campaign']['dates'] as $cost_campaign)
                      <td class="cost_data">{{numberConverter( $cost_campaign['value'] ,3,'hosting_cost') }}</td>
                      @endforeach
                      @endif
                    </tr>

                    <tr class="bg-young-yellow o_cost">
                      <td class="font-weight-bold">
                        <div class="text-with-sup">
                          <div class="text-left"><strong class="text-with-sup">Other Cost<sup><i class="ml-3 text-dark fa fa-info-circle" title="Other cost = Hosting + Content + rnd + md + platform"></i></sup></strong></div>
                        </div>
                      </td>
                      <td class="other_cost_total">{{numberConverter( $report['other_cost']['total'] ,2,'hosting_cost') }}</td>
                      <td class="other_cost_avg">{{numberConverter( $report['other_cost']['avg'] ,2,'hosting_cost') }}</td>
                      <td class="other_cost_month">{{numberConverter( $report['other_cost']['t_mo_end'] ,2,'hosting_cost') }}</td>

                      @if(isset($report['other_cost']['dates']) && !empty($report['other_cost']['dates']))
                      @foreach ($report['other_cost']['dates'] as $other_cost)
                      <td class="other_cost_data">{{numberConverter( $other_cost['value'] ,2,'hosting_cost') }}</td>
                      @endforeach
                      @endif
                    </tr>

                    <tr class="bg-young-red price_mo">
                      <td class="font-weight-bold"><strong class="text-with-sup">Price/Mo<sup><i class="ml-3 text-dark fa fa-info-circle" title="Price/Mo = cost campaign / mo"></i></sup></strong></td>
                      <td class="price_mo_total">0</td>
                      <td class="price_mo_avg">0</td>
                      <td class="price_mo_month">0</td>

                      @if(isset($report['price_mo']['dates']) && !empty($report['price_mo']['dates']))
                      @foreach ($report['price_mo']['dates'] as $price_mo)
                      <td class="price_mo_data">{{numberConverter( $price_mo['value'] ,2,'hosting_cost') }}</td>
                      @endforeach
                      @else
                      @foreach ($no_of_days as $days)
                      <td>0</td>
                      @endforeach
                      @endif
                    </tr>

                    <tr class="bg-young-red active_subscriber">
                      <td class="font-weight-bold"><strong>Active Subscriber</strong></td>
                      <td class="subs_total">0</td>
                      <td class="subs_avg">0</td>
                      <td class="subs_month">0</td>

                      @if(isset($report['active_subs']['dates']) && !empty($report['active_subs']['dates']))
                      @foreach ($report['active_subs']['dates'] as $active_subs)
                      <td class="subs_data">{{numberConverter( $active_subs['value'] ,2,'hosting_cost') }}</td>
                      @endforeach
                      @else
                      @foreach ($no_of_days as $days)
                      <td>0</td>
                      @endforeach
                      @endif
                    </tr>

                    <tr class="bg-young-green pnl">
                      <td class="font-weight-bold"><strong class="text-with-sup">GP<sup><i class="ml-3 text-dark fa fa-info-circle" title="GP = Revenue After Telco - (Cost Campaign + Hosting + Content + rnd + md + platform)"></i></sup></strong>
                      </td>
                      <td class="pnl_total p">{{numberConverter( $report['pnl']['total'] ,2,'hosting_cost') }}</td>
                      <td class="pnl_avg">{{numberConverter( $report['pnl']['t_mo_end'] ,2,'hosting_cost') }}</td>
                      <td class="pnl_month">{{numberConverter( $report['pnl']['avg'] ,2,'hosting_cost') }}</td>

                      @if(isset($report['pnl']['dates']) && !empty($report['pnl']['dates']))
                      @foreach ($report['pnl']['dates'] as $pnl)
                      <td class="pnl_data">{{numberConverter( $pnl['value'] ,2,'hosting_cost') }}</td>
                      @endforeach
                      @endif
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          @endforeach
          @endif
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  function submit(){
    var operator = $('#operator').val();
    var urls = window.location.origin+'/report/unknown/operator';
    var from = $('#from').val();
    var to = $('#to').val();

    var url = urls+'/'+'?operator='+operator+'&from='+from+'&to='+to;
    window.location.href = url;     
  }
</script>

@endsection
