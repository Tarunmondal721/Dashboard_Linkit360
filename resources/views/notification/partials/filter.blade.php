
@php
$countryId= request()->get('country');
$operatorId= request()->get('operator');
$account_managerId = request()->get('account_manager');
$pmoId = request()->get('pmo');
@endphp

<div class="card shadow-sm mt-0">
<div class="card-body">
<div class="row">
    <div class="col-lg-3">
        <div class="form-group">
            <label for="country">Choose Country</label>
            <select class="form-control select2" name="country" id="country" style="width: 100%" data-select2-id="select2-data-filtertype" tabindex="-1" aria-hidden="true">
            <option value=""  selected>Select Country</option>
            <option value="indonesia">Indonesia</option>
            <option value="oman">Oman</option>
            <option value="laos">Laos</option>
            </select>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="form-group">
            <label for="operator">Choose Operator</label>
            <select class="form-control select2" id="operator" name="operator" style="width: 100%" data-select2-id="select2-data-dashboard-company" tabindex="-1" aria-hidden="true">
            <option value=""  selected>Select Operator</option>
            <option value="omantel">omantel</option>
            <option value="etl">etl</option>
            </select>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="form-group">
            <label for="category_notification">Choose Category Notification</label>
            <select class="form-control select2" name="category_notification" id="category_notification" style="width: 100%" data-select2-id="select2-data-category-notification" tabindex="-1" aria-hidden="true">
            <option value=""  selected>Select Category Notification</option>
            <option value="deployment">Deployment</option>
            <option value="bugs">Bugs</option>
            <option value="incident">Incident</option>
            </select>
        </div>
    </div>
    <div class="col-lg-3">
        <label for="notification">Choose Notification</label>
        <select class="form-control select2" name="notification" id="notification" style="width: 100%" data-select2-id="select2-data-notification" tabindex="-1" aria-hidden="true">
            <option value=""  selected>Select Notification</option>
            <option value="meeting">Meeting</option>
            <option value="call">Call</option>

        </select>
    </div>

    <div class="col-lg-3" style="text-align:left;">
        <label class="invisible d-block">Button</label>
        <button  class="btn btn-primary" onclick="submit()" >Submit</button>
        {{-- <a class="btn btn-secondary" href="{{route('report.list')}}">Reset</a> --}}
        <a class="btn btn-secondary">Reset</a>
    </div>
</div>

<div class="error_block"></div>
</div>
</div>

<script>
function submit(){

var country =$('#country').val();
var category_notification =$('#category_notification').val();
var notification =$('#notification').val();
var operator = $('#operator').val();
var account_manager = $('#account_manager').val();
var pmo =$('#pmo').val();
var backend = $('#backend').val();
var orgurl=window.location.pathname;
let arrurl = orgurl.split('/');
var urls= window.location.origin+'/'+arrurl[1]+'/'+arrurl[2];
var date = $('#date_range').val();


if(country != ''){
urls=urls  +'?country='+country;
}else{
urls=urls  +'?';
}
if(operator != ''){
urls=urls  +'&operator='+operator;
}
if(category_notification != ''){
urls=urls  +'&category_notification='+category_notification;
}
if(notification != ''){
    urls = urls +'&notification='+notification
}

var url=urls;
window.location.href =url;

}
</script>
