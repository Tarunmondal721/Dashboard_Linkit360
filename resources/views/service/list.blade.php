@extends('layouts.admin')

@section('title')
    {{ __('Service List') }}
@endsection

@section('content')

    @include('service.partials.filter')

    <div class="row justify-content-between align-items-center">
        <div class="col-md-12 ">
            <div class="">

                <div class="card-body">

                    <strong>
                        <h4>Summary Service Catalouge</h4>
                    </strong>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="subheading3">Total Service Catalouge Created <span
                                    class="spacing-1">{{ count($services) }}</span></label>

                            @php
                                $statuses = $services->pluck('status_intregration')->countBy();
                            @endphp

                            <label class="subheading3">Total Service Active <span
                                    class="spacing-2">{{ $services->where('is_active', 1)->count() }}</span></label>
                            <label class="subheading3">Total Service Not-Active <span
                                    class="spacing-3">{{ $services->where('is_active', 0)->count() }}</span></label>
                            <label class="subheading3">Total Status Integration Go Live <span
                                    class="spacing-4">{{ $statuses->get('Go Live', 0) }}</span></label>
                        </div>
                        <div class="col-md-6">
                            <label class="subheading3">Total Status Integration UAT <span
                                    class="spacing-1">{{ $statuses->get('UAT', 0) }}</span></label>
                            <label class="subheading3">Total Status Integration <br>On Progress Development <span
                                    class="spacing-5">{{ $statuses->get('On Progress Development', 0) }}</span></label>
                            <label class="subheading3">Total Status Integration On Hold <span
                                    class="spacing-6">{{ $statuses->get('On Hold', 0) }}</span></label>
                        </div>
                    </div>

                </div>

                <div class="">
                    <div class="table-responsive  table-striped" id="all">
                        <h1 style="display:hidden"></h1>
                        <table class="table table-striped dataTable servicecatalogue">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="display: none;"></th>
                                    <th style="display: none;"></th>
                                    <th class="align-middle">Country</th>
                                    <th class="align-middle">Company</th>
                                    <th class="align-middle">Type</th>
                                    <th class="align-middle">Operator</th>
                                    <th class="align-middle">Service</th>
                                    <th class="align-middle">PMO</th>
                                    <th class="align-middle">Developer</th>
                                    <th class="align-middle">Account Manager</th>
                                    <th class="align-middle">Status</th>
                                    <th class="align-middle">Project Start Date</th>
                                    <th class="align-middle">Project End Date</th>
                                    <th class="align-middle">Step</th>
                                    <th class="align-middle">Percentage Complete</th>
                                    <th class="align-middle">Status Intergration</th>
                                    <th class="align-middle">Go Live Date</th>
                                    <th class="align-middle">Go Live Note</th>
                                    <th class="align-middle">KPI</th>
                                    <th class="align-middle">Remaining Days Vs KPI</th>
                                    <th class="align-middle">Workflow</th>
                                    <th class="align-middle">Checklist</th>
                                    <th class="align-middle">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($services))
                                    @foreach ($services as $service)
                                        <tr>
                                            <td style="display: none;"></td>
                                            <td style="display: none;"></td>
                                            <td>{{ isset($service->country->country) ? $service->country->country : '' }}
                                            </td>
                                            <td>{{ isset($service->company->name) ? $service->company->name : '' }}</td>
                                            <td>{{ isset($service->service_type) ? $service->service_type : '' }}</td>
                                            <td>{{ isset($service->operator_name) ? $service->operator_name : '' }}
                                            </td>
                                            <td>{{ isset($service->service_name) ? $service->service_name : '' }}</td>
                                            <td>{{ isset($service->pmouser) ? $service->pmouser->name : '' }}</td>
                                            <td>{{ !is_null($service->backenduser) ? $service->backenduser->name : '' }}
                                            </td>
                                            <td>{{ isset($service->accountManager) ? $service->accountManager->name : '' }}
                                            </td>
                                            <td>{{ $service->is_active == 1 ? 'Active' : 'Not active' }}</td>
                                            @if ($service->is_draf == 1)
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            @else
                                                <td>{{ isset($service->project_start_date) ? $service->project_start_date : '' }}
                                                </td>
                                                <td>{{ isset($service->project_end_date) ? $service->project_end_date : '' }}
                                                </td>
                                                <td>{{ isset($service->status_count) ? $service->status_count . ' - 27' : '' }}
                                                </td>
                                                <td>{{ isset($service->percentage) ? numberConverter($service->percentage, 2, 'pre') . '%' : '0.00%' }}
                                                </td>
                                            @endif
                                            <td>{{ isset($service->status_intregration) ? $service->status_intregration : '' }}
                                            </td>
                                            @if ($service->is_draf == 1)
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            @else
                                                @php
                                                if(isset($days)){
                                                    foreach ($days as $key => $value) {
                                                        if($service->id == $key)
                                                        $remaining_days = 14 - $value;
                                                        $abs_remaining_days = abs($remaining_days);
                                                        $class = $remaining_days >= 0 ? 'positive' : 'negative';
                                                    }
                                                }
                                                @endphp
                                                <td>{{ isset($service->go_live_date) ? $service->go_live_date : '' }}</td>
                                                <td>
                                                    @if ($service->note != null && $service->status_intregration == 'Go Live')
                                                        <button type="button" class="note-btn" data-toggle="modal"
                                                            data-target="#noteModal" data-note="{{ $service->note }}">
                                                            Check Note
                                                        </button>
                                                    @endif
                                                </td>
                                                <td> 14 Days </td>

                                                @if ($service->status_intregration == 'Go Live')
                                                    <td></td>
                                                @else
                                                    <td style="text-align: center;" id="dayDifferenceCell" class="{{ $class }}">
                                                        {{ $abs_remaining_days }} Days
                                                    </td>
                                                @endif
                                            @endif

                                            @if ($service->is_draf == 1)
                                                <td></td>
                                                <td></td>
                                            @else
                                                <td style="text-align: center;"><a href="#"
                                                        data-url="{{ route('report.progress.reoprt', $service->id) }}"
                                                        data-size="lg" data-ajax-popup="true"
                                                        data-title="{{ __('Update Display Workflow') }}"
                                                        data-toggle="tooltip" data-original-title="Edit Workflow"><img
                                                            src="{{ asset('assets/images/workflow.png') }}"
                                                            alt="Workflow Img" srcset="" height="20"></a>
                                                </td>
                                                <td style="text-align: center;"><a href="{{ route('service.checklist', $service->id) }}"><img
                                                            src="{{ asset('assets/images/checklist.png') }}"
                                                            alt="Checklist Img" srcset="" height="20"></a>
                                                </td>
                                            @endif
                                            <td class="Action">
                                                <span>
                                                    @if ($service->is_draf == 1)
                                                        <button type="submit" name="" value=""
                                                            class="edit-draft"><a
                                                                href="{{ route('report.edit', ['id' => $service->id]) }}">Edit
                                                                Draft</a> </button>
                                                    @else
                                                        <a href="{{ route('report.edit', ['id' => $service->id]) }}"
                                                            class="edit-icon" data-toggle="tooltip"
                                                            data-original-title="{{ __('edit service') }}"><i
                                                                class="fas fa-pencil-alt"></i></a>
                                                    @endif
                                                    @foreach ($count as $key => $check)
                                                        @if ($service->id == $key && $check >= 12)
                                                            <button type="submit" name="" value=""
                                                                class="golive-btn"><a id="golive" class="golive"
                                                                    data-service-id="{{ $service->id }}"
                                                                    style="color: white;">Go
                                                                    Live</a> </button>
                                                        @endif
                                                    @endforeach
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>

                        <!-- Golive Modal Start Here -->
                        <div class="goliveModal">
                            <div class="modal fade" id="golivecontent" tabindex="-1" role="dialog" aria-hidden="true"
                                style="width: 155%;">
                                <div class="row" style="margin-top: 10%;">
                                    <div class="col-md-3" style="margin-left: 20%;">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content contentlive">
                                                <div class="modal-body modalboday">
                                                    <div class="form-group">
                                                        <label class="form-check-label subhead"> Go Live
                                                            Confirmation</label>
                                                        <div>
                                                            <label class="form-check-label subhead2"> Are you sure want to
                                                                Go Live this Connection?</label>
                                                        </div>
                                                        <label for="" class="subhead2">Is there any note that need
                                                            to add?<sup><i class="fa fa-asterisk"
                                                                    style="color: red; font-size:7px;"></i></sup></label>
                                                        <select class="form-control select2" id="golivecheck"
                                                            name="is_golive" aria-hidden="true" onchange="toggleNote()">
                                                            <option value="no" selected>No</option>
                                                            <option value="yes">Yes</option>
                                                        </select>
                                                        <label for=""
                                                            class="form-check-label subhead2">Note?<sup><i
                                                                    class="fa fa-asterisk"
                                                                    style="color: red; font-size:7px;"></i></sup></label>
                                                        <textarea name="note" id="note" style="height: 45px;" cols="36" rows="10"></textarea>
                                                    </div>
                                                    <div style="text-align: center;">
                                                        <div class="btn btn-primary golive" id="golivedata">Go Live
                                                        </div>
                                                        <div class="btn btn-danger" data-dismiss="modal"
                                                            aria-label="Cancel">Cancel</div>
                                                        {{-- <a href="#"
                                                                class="more-text btn btn-danger widget-text float-right close-icon"
                                                                data-dismiss="modal" aria-label="Close">Cancle</a> --}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Golive Modal End Here -->

                        <!-- Note Modal Start Here -->
                        <div class="livenote">
                            <div class="modal fade noteModal" id="noteModal" class="noteModal" tabindex="-1"
                                role="dialog" aria-labelledby="noteModalLabel" aria-hidden="true">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content contentlive">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="noteModalLabel">Note</h5>
                                                    <div>
                                                        <h4 class="h4 font-weight-400 float-left modal-title">
                                                        </h4>
                                                        <a href="#"
                                                            class="more-text widget-text float-right close-icon"
                                                            data-dismiss="modal" aria-label="Close">Close</a>
                                                    </div>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <textarea name="note" class="form-control" disabled></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Note Modal End Here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/services.js') }}"></script>
    <script>
        window.onload = function() {
            toggleNote();
        };

        function closeModalAndGoBack() {
            $('#golivecontent').modal('hide');
            window.location.href = '/service/list'
        }

        $(document).ready(function() {
            $('.golive').click(function() {
                var serviceId = $(this).data('service-id');
                console.log('Service ID:', serviceId);
                $('#golivedata').data('service-id', serviceId);
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            const noteModal = document.getElementById('noteModal');
            const noteTextarea = noteModal.querySelector('textarea[name="note"]');

            document.querySelectorAll('.note-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const note = this.getAttribute('data-note');
                    noteTextarea.value = note;
                });
            });
        });
    </script>



@endsection
