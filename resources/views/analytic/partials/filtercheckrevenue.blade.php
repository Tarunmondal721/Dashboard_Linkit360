<?php $operators= App\Models\Operator::orderBy('operator_name', 'ASC')->get();?>  
    <div class="page-title" style="margin-bottom:25px">
        <div class="row justify-content-between align-items-center">
            <div class="col-xl-4 col-lg-4 col-md-4 d-flex align-items-center justify-content-between justify-content-md-start mb-3 mb-md-0">
                <div class="d-inline-block">

                    <div style="white-space:nowrap;">@yield('pagetytle')</div>
                </div>
            </div>
            <div class="col-xl-8 col-lg-8 col-md-8 d-flex align-items-center justify-content-between justify-content-md-end">
            </div>
        </div>
    </div>


    <div class="card shadow-sm mt-0">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <label for="ads_opr_name">Operator</label>
                    <select class="form-control select2" name="operator_id" id="operator">
                        <option value="">Select Operator</option>
                        @foreach ($operators as $operator)
                        <option value="{{$operator->id_operator}}">{{$operator->operator_name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-4">
                    <label>Date</label>
                    <input type="date" name="date" id="date">
                </div>
                <div class="col-md-3">
                    <label class="invisible d-block">Search</label>
                    <div class="btn btn-primary" onclick="submit()"><i class="fa fa-search"></i> Search</div>
                    <a class="btn btn-secondary" href="{{url()->previous()}}">Reset</a>
                </div>
            </div>
        </div>
    </div>   

    <script>   

        function submit(){
                    console.log(window.location.pathname);
                    var operators = $('#operator').val();
                    var orgurl=window.location.pathname;
                    let arrurl = orgurl.split('/');
                    var urls= window.location.origin+'/'+arrurl[1];
                    var date = $('#date').val();
                    console.log(urls);
                    
                    var url=urls+'?date='+date+'&id='+operators;
                    window.location.href =url;
                    console.log(url);
                }
    </script>
