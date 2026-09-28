<div id="add-kpi-modal" data-bs-backdrop="static" class="modal fade bs-example-modal-xl" tabindex="-1" role="dialog" aria-labelledby="get-user-kpis"
    aria-hidden="true">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="get-user-kpis">Agent KPI Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="save-parameters">
            <div class="modal-body" id="add-kpi">

                    <div class="row">
                        <div class="col-6">
                            <div class="mb-3">
                                <label for="ForminputState" class="form-label">Manager*</label>
                                <select id="manager_id" name="manager_id" required class="form-select select2 inputs">
                                    <option value="">Select Manager</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div><!--end col-->
                        <div class="col-6">
                            <div class="mb-3">
                                <label for="ForminputState" class="form-label">Agent*</label>
                                <select name="agent_id[]" class="form-control inputs" required id="agent_id" multiple>
                                    <option value="">Select Agents</option>

                                </select>
                            </div>
                        </div><!--end col-->
                        {{-- <div class="col-6">
                            <div class="mb-3">
                                <label for="ForminputState" class="form-label">Module</label>
                                <select name="model" class="form-control" id="model">
                                    <option value="Lead">Leads</option>
                                    <option value="Property">Properties</option>
                                    <option value="ListingLead">Listing Leads</option>
                                    <option value="Event">Events</option>
                                </select>
                            </div>
                        </div><!--end col--> --}}
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="ForminputState" class="form-label">Key Performance Indicator (KPI)*</label>
                                <select name="kpi" class="form-control select2 inputs" id="kpi" required>
                                    @include('admin.kpi.includes._kpi_options',['selected' => null])
                                </select>
                            </div>
                        </div><!--end col-->

                        <div class="col-6">
                            <div class="mb-3">
                                <label for="start_date" class="form-label">Start Date*</label>
                                <input type="date" name="start_date" id="start_date" required class="form-control inputs">
                            </div>
                        </div><!--end col-->
                        <div class="col-6">
                            <div class="mb-3">
                                <label for="end_date" class="form-label">End Date*</label>
                                <input type="date" name="end_date" id="end_date" required class="form-control inputs">
                            </div>
                        </div><!--end col-->
                        {{-- <div class="col-12">
                            <div class="mb-3">
                                <label for="compnayNameinput" class="form-label">Duration</label>
                                <select name="duration" class="form-control select2" id="duration">
                                    <option value="">Select Duration</option>
                                    <option value="7">7 Days</option>
                                    <option value="14">14 Days</option>
                                    <option value="21">21 Days</option>
                                    <option value="28">28 Days</option>
                                </select>
                            </div>
                        </div><!--end col--> --}}
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="ForminputState" class="form-label">Target*</label>
                                <input type="number" name="target" id="target" required class="form-control inputs">
                            </div>
                        </div><!--end col-->

                    </div><!--end row-->

            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Submit</button>

                <a href="javascript:void(0);" class="btn btn-danger  shadow-none fw-medium"
                    data-bs-dismiss="modal"><i class="ri-close-line me-1 align-middle"></i> Close</a>
            </div>
        </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

<div id="edit-kpi-modal" class="modal fade bs-example-modal-xl" tabindex="-1" role="dialog"
    aria-labelledby="get-user-kpis" aria-hidden="true">
    <div class="modal-dialog ">
        <form id="update-parameters">

        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="get-user-kpis">Agent KPI Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="edit-kpi">

            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Submit</button>

                <a href="javascript:void(0);" class="btn btn-danger  shadow-none fw-medium"
                data-bs-dismiss="modal"><i class="ri-close-line me-1 align-middle"></i> Close</a>
            </div>
        </div><!-- /.modal-content -->
        </form>
    </div><!-- /.modal-dialog -->
</div>



<div id="get-user-kpi-modal" class="modal fade bs-example-modal-xl" tabindex="-1" role="dialog"
    aria-labelledby="get-user-kpis" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="get-user-kpis">Agent KPI Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="agent-kpi">
            </div>
            <div class="modal-footer">
                <a href="javascript:void(0);" class="btn btn-link link-success shadow-none fw-medium"
                    data-bs-dismiss="modal"><i class="ri-close-line me-1 align-middle"></i> Close</a>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>



<div id="user-kpi-details" class="modal fade bs-example-modal-xl" tabindex="-1" role="dialog"
    aria-labelledby="get-user-kpis" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="get-user-kpis">Agent KPI Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="kpi-details">
            </div>
            <div class="modal-footer">
                <a href="javascript:void(0);" class="btn btn-link link-success shadow-none fw-medium"
                    data-bs-dismiss="modal"><i class="ri-close-line me-1 align-middle"></i> Close</a>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>


<div id="kpi-info" class="modal fade bs-example-modal-xl" tabindex="-1" role="dialog"
    aria-labelledby="kpi-info" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="kpi-info">KPI Info</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="kpi-details">
                <h6 class="fs-15">New Lead</h6>
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <i class="ri-checkbox-circle-fill text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <p class="text-muted mb-0">This Kpi Gives detail about newly generated leads</p>
                    </div>
                </div>

                <h6 class="fs-15">Successful Leads</h6>
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <i class="ri-checkbox-circle-fill text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <p class="text-muted mb-0">This Kpi Gives detail about newly generated leads</p>
                    </div>
                </div>

                <h6 class="fs-15">New Listing</h6>
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <i class="ri-checkbox-circle-fill text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <p class="text-muted mb-0">This Kpi Gives detail about newly generated leads</p>
                    </div>
                </div>

                <h6 class="fs-15">WhatsApp Messages</h6>
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <i class="ri-checkbox-circle-fill text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <p class="text-muted mb-0">This Kpi Gives detail about newly generated leads</p>
                    </div>
                </div>
                <h6 class="fs-15">WhatsApp Response</h6>
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <i class="ri-checkbox-circle-fill text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <p class="text-muted mb-0">This Kpi Gives detail about newly generated leads</p>
                    </div>
                </div>
                <h6 class="fs-15">Successful WhatsApp Messages</h6>
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <i class="ri-checkbox-circle-fill text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <p class="text-muted mb-0">This Kpi Gives detail about newly generated leads</p>
                    </div>
                </div>
                <h6 class="fs-15">Tru Check</h6>
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <i class="ri-checkbox-circle-fill text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <p class="text-muted mb-0">This Kpi Gives detail about newly generated leads</p>
                    </div>
                </div>
                <h6 class="fs-15">Tru Check Ready</h6>
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <i class="ri-checkbox-circle-fill text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <p class="text-muted mb-0">This Kpi Gives detail about newly generated leads</p>
                    </div>
                </div>
                <h6 class="fs-15">Tru Check Offplan</h6>
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <i class="ri-checkbox-circle-fill text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <p class="text-muted mb-0">This Kpi Gives detail about newly generated leads</p>
                    </div>
                </div>
                <h6 class="fs-15">PF Verified</h6>
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <i class="ri-checkbox-circle-fill text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <p class="text-muted mb-0">This Kpi Gives detail about newly generated leads</p>
                    </div>
                </div>
                <h6 class="fs-15">PF Verified Ready</h6>
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <i class="ri-checkbox-circle-fill text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <p class="text-muted mb-0">This Kpi Gives detail about newly generated leads</p>
                    </div>
                </div>
                <h6 class="fs-15">PF Verified Offplan</h6>
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <i class="ri-checkbox-circle-fill text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <p class="text-muted mb-0">This Kpi Gives detail about newly generated leads</p>
                    </div>
                </div>
                <h6 class="fs-15">Total Listings</h6>
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <i class="ri-checkbox-circle-fill text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <p class="text-muted mb-0">This Kpi Gives detail about newly generated leads</p>
                    </div>
                </div>
                <h6 class="fs-15">New Listing Leads</h6>
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <i class="ri-checkbox-circle-fill text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <p class="text-muted mb-0">This Kpi Gives detail about newly generated leads</p>
                    </div>
                </div>
                <h6 class="fs-15">Successful Listing Leads</h6>
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <i class="ri-checkbox-circle-fill text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <p class="text-muted mb-0">This Kpi Gives detail about newly generated leads</p>
                    </div>
                </div>
                <h6 class="fs-15">Total Meetings</h6>
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <i class="ri-checkbox-circle-fill text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <p class="text-muted mb-0">This Kpi Gives detail about newly generated leads</p>
                    </div>
                </div>
                <h6 class="fs-15">Total Viewings</h6>
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <i class="ri-checkbox-circle-fill text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <p class="text-muted mb-0">This Kpi Gives detail about newly generated leads</p>
                    </div>
                </div>
                <h6 class="fs-15">Total Events</h6>
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <i class="ri-checkbox-circle-fill text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <p class="text-muted mb-0">This Kpi Gives detail about newly generated leads</p>
                    </div>
                </div>
                <h6 class="fs-15">Call Logs</h6>
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <i class="ri-checkbox-circle-fill text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <p class="text-muted mb-0">This Kpi Gives detail about newly generated leads</p>
                    </div>
                </div>
                <h6 class="fs-15">Connected Call Logs</h6>
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <i class="ri-checkbox-circle-fill text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <p class="text-muted mb-0">This Kpi Gives detail about newly generated leads</p>
                    </div>
                </div>
                <h6 class="fs-15">Outgoing Call Logs</h6>
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <i class="ri-checkbox-circle-fill text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <p class="text-muted mb-0">This Kpi Gives detail about newly generated leads</p>
                    </div>
                </div>
                <h6 class="fs-15">Incoming Call Logs</h6>
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <i class="ri-checkbox-circle-fill text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <p class="text-muted mb-0">This Kpi Gives detail about newly generated leads</p>
                    </div>
                </div>
                <h6 class="fs-15">Missed Call Logs</h6>
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <i class="ri-checkbox-circle-fill text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <p class="text-muted mb-0">This Kpi Gives detail about newly generated leads</p>
                    </div>
                </div>
                <h6 class="fs-15">Incoming Missed Call Logs</h6>
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <i class="ri-checkbox-circle-fill text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <p class="text-muted mb-0">This Kpi Gives detail about newly generated leads</p>
                    </div>
                </div>
                <h6 class="fs-15">Outgoing Missed Call Logs</h6>
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <i class="ri-checkbox-circle-fill text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <p class="text-muted mb-0">This Kpi Gives detail about newly generated leads</p>
                    </div>
                </div>
                <h6 class="fs-15">Bulk SMS</h6>
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <i class="ri-checkbox-circle-fill text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <p class="text-muted mb-0">This Kpi Gives detail about newly generated leads</p>
                    </div>
                </div>
                <h6 class="fs-15">Successful Bulk SMS</h6>
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <i class="ri-checkbox-circle-fill text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <p class="text-muted mb-0">This Kpi Gives detail about newly generated leads</p>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <a href="javascript:void(0);" class="btn btn-link link-success shadow-none fw-medium"
                    data-bs-dismiss="modal"><i class="ri-close-line me-1 align-middle"></i> Close</a>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
