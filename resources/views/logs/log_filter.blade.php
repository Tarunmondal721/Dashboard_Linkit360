<?php 
$signatures = App\Models\CronLog::select('signature')->distinct()->orderBy('signature', 'ASC')->get();




?> 
<?php 

//$signatures = DB::table('cron_logs')
               //->select('signature')->orderBy('signature','asc')->get();?>

<div class="card shadow-sm mt-0">

    <form method="GET" action="{{ route('logfile.cron') }}">
    <div class="card-body">
        <div class="row">
            <div class="col-lg-2">
                <label for="date_for">Date for</label>
            <input type="date" name="date_for" id="date_for"
            placeholder="dd-mm-yyyy" value="<?php echo isset($_GET['date_for'])?$_GET['date_for']: '' ?>"
            min="1997-01-01" max="2030-12-31">
            </div>

            <div class="col-lg-2">
                <label for="date">Start Date</label>
                <input type="date" name="date" id="date"
                placeholder="dd-mm-yyyy" value="<?php echo isset($_GET['date'])?$_GET['date']: '' ?>"
                min="1997-01-01" max="2030-12-31">
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                  <label for="company">Signature</label>
                  <select class="form-control select2" id="log_signatre"
                  name="log_signatre" type="text" value="<?php echo isset($_GET['log_signatre'])?$_GET['log_signatre']: '' ?>" >
                    <option value="" selected>Select Signature</option>
                        {{-- <option value="<?php echo isset($_GET['log_signatre'])?$_GET['log_signatre']:'allsignature'  ?>"  <?php echo isset($signatures) && ($signatures == 'allsignature') ? 'selected': '' ?> >All Signatre</option> --}}
                    @foreach ($signatures as $findSignature)
                        <option value="{{$findSignature->signature}}" <?php echo isset($_GET['log_signatre']) && ($findSignature->signature == $_GET['log_signatre']) ? 'selected': '' ?>>{{$findSignature->signature}}</option>
                    @endforeach
    
                  </select>
    
                </div>
            </div>
            {{-- <div class="col-lg-4">
                <label for="log_signatre">Signature</label>
                <input name="log_signatre" type="text" id="log_signatre" value="<?php echo isset($_GET['log_signatre'])?$_GET['log_signatre']: '' ?>"/>
  
            </div> --}}

            <div class="col-lg-5">
                <label class="invisible d-block">Search</label>
                <button type="submit" class="btn btn-primary">Submit</button>
                <a class="btn btn-secondary" href="{{route('logfile.cron') }}">Reset</a>
                <a class="btn btn-success" href="{{route('logfile.deleteCron')}}">Delete Cron</a>
              </div>
            
            
            
            

           
            
            {{-- <div class="col-lg-2">
                <label class="invisible d-block">Sort</label>
                <button type="button" class="btn btn-primary" id="alphBnt"><i class="fa fa-filter"></i> Filter</button>
            </div>
            <div class="col-lg-2">
                <label class="invisible d-block">Export Excel</label>
                <button class="btn btn-sm pnl-xls" style="color:white; background-color:green" data-param="reportXls"><i
                    class="fa fa-file-excel-o"></i>Export All as XLS</button>
            </div> --}}
        </div>



   </form> 




</div>

