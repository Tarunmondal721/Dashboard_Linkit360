<div class="card bg-none card-box">
    <form class="pl-3 pr-3" method="post"  action="{{ route('test.email.send') }}">
        @csrf
        <div class="row">
            <div class="col-12 form-group"  id="form-test-email">
                <label for="email" class="form-control-label"  >{{ __('E-Mail Address') }}</label>
                <input type="email" class="form-control" id="email" name="email" required/>
            </div>
            <div class="col-12 form-group text-right">
                <input type="submit" value="{{__('Send Test Mail')}} " class="btn-create badge-blue">
                <input type="button" value="{{__('Cancel')}}" class="btn-create bg-gray" data-dismiss="modal">
            </div>
        </div>
    </form>
</div>
{{-- 
<script>
    function sendTestEmail() {
        var mail_driver = $("#mail_driver").val();
        var mail_host = $("#mail_host").val();
        var mail_port = $("#mail_port").val();
        var mail_username = $("#mail_username").val();
        var mail_password = $("#mail_password").val();
        var mail_encryption = $("#mail_encryption").val();
        var mail_from_address = $("#mail_from_address").val();
        var mail_from_name = $("#mail_from_name").val();
        var formTestEmail = $("#form-test-email");
        formTestEmail.append(`<input type="hidden" name="mail_driver" value="${mail_driver}" >`)
        formTestEmail.append(`<input type="hidden" name="mail_host" value="${mail_host}" >`)
        formTestEmail.append(`<input type="hidden" name="mail_port" value="${mail_port}" >`)
        formTestEmail.append(`<input type="hidden" name="mail_username" value="${mail_username}" >`)
        formTestEmail.append(`<input type="hidden" name="mail_password" value="${mail_password}" >`)
        formTestEmail.append(`<input type="hidden" name="mail_encryption" value="${mail_encryption}" >`)
        formTestEmail.append(`<input type="hidden" name="mail_from_address" value="${mail_from_address}" >`)
        formTestEmail.append(`<input type="hidden" name="mail_from_name" value="${mail_from_name}" >`)
    }
</script> --}}