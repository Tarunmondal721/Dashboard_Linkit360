<div class="card shadow-sm mt-0">
    <div class="card-body">
        <div class="row">
            <div class="table-responsive  table-striped" id="all">
                <h1 style="display:hidden"></h1>
                <form action="{{ route('report.progress.update') }}" method="POST" enctype="multipart/form-data" onsubmit="">
                    @csrf
                    <input type="hidden" name="service_id" value="{{ $id }}">
                    <table class="table table-light table-striped m-0 font-13 all" id="dtbl">
                        <thead class="thead-dark">
                            <tr>
                                <th class="align-middle">No</th>
                                <th class="align-middle">Task Name</th>
                                <th class="align-middle">Due Date</th>
                                <th class="align-middle">Note</th>
                                <th class="align-middle">Status</th>
                                <th class="align-middle">Attachment</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $count = 0;
                            @endphp
                            @if (isset($progress))
                            @foreach ($progress as $progres)
                            <tr>
                                <input type="hidden" name="progres_{{ isset($progres) ? $progres->id : '' }}" value="{{ isset($progres) ? $progres->id : '' }}">
                                <td class="align-middle">{{ ++$count }}</td>
                                <td class="align-middle">{{ isset($progres) ? $progres->name : '' }} <a href="{{ route('report.edit', ['id' => $id]) }}"><i class="fa fa-info-circle" style="font-size:18px;color:blue"></i></a>
                                </td>
                                <td class="align-middle col-lg-2">
                                    <div class="md-form md-outline input-with-post-icon">
                                        <input class="form-control dateProgress" data-progress-id="{{ isset($progres) ? $progres->id : '' }}" id="date_{{ isset($progres) ? $progres->id : '' }}" name="date_{{ isset($progres) ? $progres->id : '' }}" type="text" style="height: 40px;" value="<?php echo isset($progressOldData[$progres->id]) ? $progressOldData[$progres->id]->dute_date : ''; ?>">
                                    </div>
                                </td>
                                <td class="align-middle col-lg-2">
                                    <input type="text" class="form-control" name="note_{{ isset($progres) ? $progres->id : '' }}" value="<?php echo isset($progressOldData[$progres->id]) ? $progressOldData[$progres->id]->note : ''; ?>">
                                </td>

                                <td class="align-middle col-lg-2">
                                    @if ($progressOldData[$progres->id]->status == 'done')
                                    <select disabled="disabled" name="" class="form-control select2">
                                        <option value="done" <?php echo isset($progressOldData[$progres->id]) && $progressOldData[$progres->id]->status == 'done' ? 'selected' : ''; ?>>Done</option>
                                    </select>
                                    <input type="hidden" name="status_{{ isset($progres) ? $progres->id : '' }}" value="done">
                                    @else
                                    <select name="status_{{ isset($progres) ? $progres->id : '' }}" class="form-control select2">
                                        <option value="pending" <?php echo isset($progressOldData[$progres->id]) && $progressOldData[$progres->id]->status == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="in-progress" <?php echo isset($progressOldData[$progres->id]) && $progressOldData[$progres->id]->status == 'in-progress' ? 'selected' : ''; ?>>In Progress</option>
                                        <option value="done" <?php echo isset($progressOldData[$progres->id]) && $progressOldData[$progres->id]->status == 'done' ? 'selected' : ''; ?>>Done</option>
                                    </select>
                                    @endif
                                </td>
                                <td class="align-middle col-lg-2">
                                    <input type="file" class="form-control catalogue_file" name="file_{{ isset($progres) ? $progres->id : '' }}"  style="display: none;" onchange="displayFileName(this)" value="<?php echo isset($progressOldData[$progres->id]) ? $progressOldData[$progres->id]->file : ''; ?>">
                                    <button type="button" class="btn file"
                                    onclick="document.querySelector('input[name=\'file_{{ isset($progres) ? $progres->id : '' }}\']').click();">Choose
                                    File</button><span id="file_{{ isset($progres) ? $progres->id : '' }}"></span>
                                </td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                    <div class="col-md-12" style="text-align:right;">
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        if ($(".dateProgress").length) {
            $('.dateProgress').daterangepicker({
                locale: date_picker_locale,
                autoUpdateInput: false,
                singleDatePicker: true,

            });
            $('.dateProgress').on('apply.daterangepicker', function(ev, picker) {
                var p_id = $(this).attr("data-progress-id");
                $('#date_' + p_id).val(picker.startDate.format('YYYY-MM-DD'));
            });
        }
    });

    function displayFileName(input) {
        var fileName = input.files[0].name;
        document.getElementById(input.name).textContent = fileName;
        input.setAttribute('value', fileName);
    }
</script>
