

<div class="card shadow-sm mt-0">
  <div class="card-body">
    <div class="form-group row">
      <div class="col-md-12" style="position: relative;">
        <label class="control-label" for="revenueshare-operator">
          @if (isset($companyOperators))
          <p class="col-md-8">Total Operators : {{count($companyOperators)}}</p>
          @elseif(isset($operators))
          <p>total operators : {{count($operators)}}</p>
          @else
          <p>total operators : 0</p>
          @endif
        </label>
        @if (isset($companyOperators))
        <div class="company_button">
          <button class="col-md-2 bg-info"><a href="{{ URL::to('management/company-operator/'.$company_id) }}"  data-toggle="tooltip"><i class="fa fa-plus"></i>Add</a></button>
        </div>
        @endif
        <div class="col-12">
          <div class="row">
            @if (isset($companyOperators)&& (!empty($companyOperators)))
            @foreach ($companyOperators as $companyOperator )
            <div class="col-4 custom-control custom-checkbox ">
              <label class="form-check-label padtop14">{{$companyOperator->Operator[0]->operator_name}}</label>
            </div>
            @endforeach
            @elseif(isset($operators)&& (!empty($operators)))
            @foreach ($operators as $operator )
            <div class="col-4 custom-control custom-checkbox ">
              <label class="form-check-label padtop14">{{$operator->operator_name}}</label>
            </div>
            @endforeach
            @else
            <div class="float left">
              <label for="operator" class=" font-weight-500">{{__('No Operator Added')}}</label> 
            </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</div>