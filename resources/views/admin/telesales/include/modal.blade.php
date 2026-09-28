<div class="modal zoomIn" id="callModal" tabindex="-1" aria-labelledby="callModalLabel" aria-modal="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-soft-danger">
                <h5 class="modal-title" id="callModalLabel"><span id="modal_title">Add </span> Call follow up</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="mainForm" class="needs-validation" novalidate>
                    <div class="row g-3">

                        <div class="col-4">

                            <label for="title" class="form-label">Time*</label>
                        </div>
                        <div class="col-8">
                            <input type="hidden" name="id" id="call_lead_id" value="">
                            <input type="datetime-local" class="form-control" id="call_followup_time" name="time"
                                placeholder="Enter time" required>
                            <div class="invalid-feedback">
                                Please select a time.
                            </div>
                        </div>
                        <!--end col-->
                        <!--end col-->
                        <div class="col-4">
                            <label class="form-check-label" for="statusCheckbox">Note</label>
                        </div>
                        <div class="col-8">

                            <textarea class="form-control" name="note" placeholder="Enter note"></textarea>

                        </div>
                        <!--end col-->

                        <div class="col-lg-12">
                            <div class="hstack gap-2 justify-content-end">
                                <button type="button" class="btn btn-light  action-close"
                                    data-bs-dismiss="modal">Close</button>
                                <button id="submitBtn" type="submit"
                                    class="btn btn-primary  action-save">Submit</button>
                            </div>
                        </div>
                        <!--end col-->
                    </div>
                    <!--end row-->
                </form>
            </div>
        </div>
    </div>
</div>



<div id="assignAgentsModal" class="modal zoomIn" tabindex="-1" aria-labelledby="assignAgentsModalLabel"
    aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-md">
        <form id="assignAgentsForm" class="modal-content needs-validation" novalidate>
            <div class="modal-header bg-soft-danger">
                <h5 class="modal-title" id="assignAgentsModalLabel">Assign Agents</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-4">Select Agents*</div>
                    <div class="col-8">
                        <select name='agents[]' id="assign_agents" class="form-control select2" multiple>
                            @foreach ($agents ?? [] as $agent)
                                <option value="{{ $agent->id }}">{{ $agent->first_name }} {{ $agent->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-4">Notifications</div>
                    <div class="col-lg-8">
                        <div class="mt-4 mt-lg-0">
                            <div class="form-check form-switch form-check-inline" dir="ltr">
                                <input type="checkbox" class="form-check-input email_notification"
                                    id="assign_agent_email_notification" name="email_notification">
                                <label class="form-check-label" for="email_notification">Email</label>
                            </div>
                            <div class="form-check form-switch form-check-inline" dir="ltr">
                                <input type="checkbox" class="form-check-input system_notification"
                                    id="assign_agent_system_notification" name="system_notification">
                                <label class="form-check-label" for="system_notification">System</label>
                            </div>
                            <div class="form-check form-switch form-check-inline" dir="ltr">
                                <input type="checkbox" class="form-check-input telegram_notification"
                                    id="assign_agent_telegram_notification" name="telegram_notification">
                                <label class="form-check-label" for="telegram_notification">Telegram</label>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light  action-close" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary  action-save">Save</button>
            </div>
        </form>
    </div>
</div>



<div id="bulkReassignLeadsModal" class="modal zoomIn" tabindex="-1" aria-labelledby="bulkReassignLeadsModalLabel"
    aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-md">
        <form id="bulkReassignLeadsForm" class="modal-content needs-validation" action="javascript:void(0)">
            <div class="modal-header bg-soft-danger">
                <h5 class="modal-title" id="bulkReassignLeadsModalLabel">Assign <span id="bulk_leads_counts"></span>
                    Leads</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-4">Select Agent*</div>
                    <div class="col-8">
                        <select name='bulk_leads_agent_ids' id="bulk_leads_agent_ids" class="form-control select2">

                            @foreach ($agents ?? [] as $agent)
                                <option value="{{ $agent->id }}">{{ $agent->first_name }} {{ $agent->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-4">Notifications</div>
                    <div class="col-lg-8">
                        <div class="mt-4 mt-lg-0">
                            <div class="form-check form-switch form-check-inline" dir="ltr">
                                <input type="checkbox" class="form-check-input email_notification"
                                    id="lead_email_notification" name="email_notification">
                                <label class="form-check-label" for="email_notification">Email</label>
                            </div>
                            <div class="form-check form-switch form-check-inline" dir="ltr">
                                <input type="checkbox" class="form-check-input system_notification"
                                    id="lead_system_notification" name="system_notification">
                                <label class="form-check-label" for="system_notification">System</label>
                            </div>
                            <div class="form-check form-switch form-check-inline" dir="ltr">
                                <input type="checkbox" class="form-check-input telegram_notification"
                                    id="lead_telegram_notification" name="telegram_notification">
                                <label class="form-check-label" for="telegram_notification">Telegram</label>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light  action-close" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary  action-assign" id="bulkLeadsSubmitBtn">Assign
                    Now!</button>
            </div>
        </form>
    </div>
</div>




<div id="telesalesModal" class="modal zoomIn" tabindex="-1" aria-labelledby="telesalesModalLabel"
    aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-md">
        <form id="bulkReassignTelesalesForm" class="modal-content needs-validation" action="javascript:void(0)">
            <div class="modal-header bg-soft-danger">
                <h5 class="modal-title" id="telesalesModalLabel">Assign <span id="bulk_leads_countss"></span>
                    Telesales
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-4">Select Telesales*</div>
                    <div class="col-8">
                        <select name='bulk_leads_telesales__' id="bulk_leads_telesales__"
                            class="form-control select2">

                            @foreach ($telesales ?? [] as $agent)
                                <option value="{{ $agent->id }}">{{ $agent->first_name }} {{ $agent->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-4">Notifications</div>
                    <div class="col-lg-8">
                        <div class="mt-4 mt-lg-0">
                            <div class="form-check form-switch form-check-inline" dir="ltr">
                                <input type="checkbox" class="form-check-input email_notification"
                                    id="telesales_email_notification" name="email_notification">
                                <label class="form-check-label" for="email_notification">Email</label>
                            </div>
                            <div class="form-check form-switch form-check-inline" dir="ltr">
                                <input type="checkbox" class="form-check-input system_notification"
                                    id="telesales_system_notification" name="system_notification">
                                <label class="form-check-label" for="system_notification">System</label>
                            </div>
                            <div class="form-check form-switch form-check-inline" dir="ltr">
                                <input type="checkbox" class="form-check-input telegram_notification"
                                    id="telesales_telegram_notification" name="telegram_notification">
                                <label class="form-check-label" for="telegram_notification">Telegram</label>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light  action-close" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary  action-assign" id="bulkTelesalesSubmitBtn">Assign
                    Now!</button>
            </div>
        </form>
    </div>
</div>


<div id="telesalesLeaderModal" class="modal zoomIn" tabindex="-1" aria-labelledby="telesalesLeaderModalLabel"
    aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-md">
        <form id="bulkReassignTelesalesLeaderForm" class="modal-content needs-validation"
            action="javascript:void(0)">
            <div class="modal-header bg-soft-danger">
                <h5 class="modal-title" id="telesalesLeaderModalLabel">Assign <span id="bulk_leads_countsss"></span>
                    Telesales Head</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-4">Select Telesales head*</div>
                    <div class="col-8">
                        <select name='bulk_leads_telesales_leader' id="bulk_leads_telesales_leaderss"
                            class="form-control select2">
                            @foreach ($telesalesheads ?? [] as $agent)
                                <option value="{{ $agent->id }}">{{ $agent->first_name }} {{ $agent->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-4">Notifications</div>
                    <div class="col-lg-8">
                        <div class="mt-4 mt-lg-0">
                            <div class="form-check form-switch form-check-inline" dir="ltr">
                                <input type="checkbox" class="form-check-input email_notification"
                                    id="telesales_head_email_notification" name="email_notification">
                                <label class="form-check-label" for="email_notification">Email</label>
                            </div>
                            <div class="form-check form-switch form-check-inline" dir="ltr">
                                <input type="checkbox" class="form-check-input system_notification"
                                    id="telesales_head_system_notification" name="system_notification">
                                <label class="form-check-label" for="system_notification">System</label>
                            </div>
                            <div class="form-check form-switch form-check-inline" dir="ltr">
                                <input type="checkbox" class="form-check-input telegram_notification"
                                    id="telesales_head_telegram_notification" name="telegram_notification">
                                <label class="form-check-label" for="telegram_notification">Telegram</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light action-close" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary action-assign" id="bulkTelesalesLeaderSubmitBtn">Assign
                    Now!</button>
            </div>
        </form>
    </div>
</div>



<div class="modal zoomIn" id="archiveModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog  modal-md modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-soft-danger">
                <h5 class="modal-title" id="archiveModalLabel">Archive Reason</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                    id="close-modal"></button>
            </div>
            <div class="modal-body px-5   py-3">
                <div class="row">
                    <div class="col-3">Reason*:</div>
                    <div class="col-9">
                        <input type="hidden" id="archive_lead_id">


                        <select name="" id="preFillOption" class="form-control">
                            <option value=""></option>
                            <option value="Internal Agent">Internal Agent</option>
                            <option value="External Agent">External Agent</option>
                            <option value="Family">Family</option>
                            <option value="Friend">Friend</option>
                            <option value="Promoter">Promoter</option>
                            <option value="Other">Other</option>
                        </select>

                        <textarea name="" rows="3" class="form-control d-none mt-3" placeholder="Add Reason for Archive"
                            id="archive_note" required></textarea>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success action-archive" id="saveArchive">
                    Archive Leads
                </button>
                <button type="button" class="btn btn-light  action-close" data-bs-dismiss="modal">
                    Close
                </button>

            </div>
        </div>
    </div>
</div>







<div id="generalModal" class="modal zoomIn" tabindex="-1" aria-labelledby="generalModalLabel" aria-hidden="true"
    style="display: none;">
    <div class="modal-dialog modal-md">
        <form id="generalModalForm" class="modal-content needs-validation" action="javascript:void(0)">
            <div class="modal-header bg-soft-danger">
                <h5 class="modal-title" id="generalModalLabel">Re-Assign <span id="bulk_leads_count"></span>
                    Leads</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <div class="row" id="telesales_agent">
                    <div class="col-4">Select Action*</div>
                    <div class="col-8">
                        <select name='' id="selectAction" class="form-control select2">
                            <option value="" disabled selected>Select Option</option>
                            @can('Leads Assign Agent')
                                <option value="Re-Assign">Re-Assign Agent</option>
                            @endcan
                            @can('Bulk Telesales Assignments')
                                <option value="Telesales">Assign To Telesales</option>
                            @endcan
                            @can('Bulk Telesales Head Assignments')
                                <option value="Telesales Head">Assign to Telesales Head</option>
                            @endcan
                        </select>
                    </div>
                </div>

                <div class="row d-none mt-2" id="agent_dropdown">
                    @can('Leads Assign Agent')
                        <div class="col-4">Select Agents*</div>
                        <div class="col-8">
                            <select name='agents_dropdown' id="agents_dropdown" class="form-control select2">
                                <option value="" selected disabled>Select Agent</option>

                                @foreach ($agents ?? [] as $agent)
                                    <option value="{{ $agent->id }}">{{ $agent->first_name }} {{ $agent->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endcan
                </div>

                <div class="row d-none mt-2" id="telesales_dropdown">
                    @can('Bulk Telesales Assignments')
                        <div class="col-4">Select Telesales*</div>
                        <div class="col-8">
                            <select name='bulk_leads_telesales' id="bulk_leads_telesales" class="form-control select2">
                                <option value="" selected disabled>Select Telesales</option>

                                @foreach ($telesales ?? [] as $agent)
                                    <option value="{{ $agent->id }}">{{ $agent->first_name }} {{ $agent->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endcan
                </div>

                <div class="row d-none mt-2" id="telesales_head_dropdown">
                    @can('Bulk Telesales Head Assignments')
                        <div class="col-4">Select Telesales head*</div>
                        <div class="col-8">
                            <select name='bulk_leads_telesales_leader' id="bulk_leads_telesales_leader"
                                class="form-control select2">
                                <option value="" selected disabled>Select Telesales Head</option>

                                @foreach ($telesalesheads ?? [] as $agent)
                                    <option value="{{ $agent->id }}">{{ $agent->first_name }} {{ $agent->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endcan
                </div>

                <div class="row mt-2">
                    <div class="col-4">Notifications</div>
                    <div class="col-lg-8">
                        <div class="mt-4 mt-lg-0">
                            <div class="form-check form-switch form-check-inline" dir="ltr">
                                <input type="checkbox" class="form-check-input email_notification"
                                    id="email_notification" name="email_notification">
                                <label class="form-check-label" for="email_notification">Email</label>
                            </div>
                            <div class="form-check form-switch form-check-inline" dir="ltr">
                                <input type="checkbox" class="form-check-input system_notification"
                                    id="system_notification" name="system_notification">
                                <label class="form-check-label" for="system_notification">System</label>
                            </div>
                            <div class="form-check form-switch form-check-inline" dir="ltr">
                                <input type="checkbox" class="form-check-input telegram_notification"
                                    id="telegram_notification" checked name="telegram_notification">
                                <label class="form-check-label" for="telegram_notification">Telegram</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light  action-close" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary  action-assign" id="bulkAssignment">Assign
                    Now</button>
            </div>
        </form>
    </div>
</div>





<div class="modal zoomIn" id="startACall" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog  modal-md modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-soft-danger">

                 <h5 class="modal-title" id="generalModalLabel"> Dial Number</h5>

                <button type="button" class="btn-close CancelBTN" data-bs-dismiss="modal" aria-label="Close"
                    id="close-modal"></button>
            </div>
            <div class="modal-body p-3 text-center text-dark">
                <div class="row">
                    <h5 id="call_id"></h5>
                  <h1>  <span id="countdown" style="font-weight: bold;">00:05</span></h3>
                    <br>
                    <h4 id="checkPhone">

                    </h4>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success action-cancel CancelBTN" id="CancelBTN">
                    Cancel Call
                </button>


            </div>
        </div>
    </div>
</div>



<div class="modal zoomIn" id="unsuccessfullReasonModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog  modal-md modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-soft-danger">
                <h5 class="modal-title" id="unsuccessfullReasonLabel">Lead Unsuccessful Reason</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                    id="close-modal"></button>
            </div>
            <div class="modal-body px-5   py-3">
                <div class="row">
                    <div class="col-3">Reason*:</div>
                    <div class="col-9">
                        <input type="hidden" id="unsuccessfull_lead_id">


                        <select name="" id="preFillOption2" class="form-control">
                            <option></option>
                            <option value="Incorrect Contact Details">Incorrect Contact Details</option>
                            <option value="Budget Differs">Budget Differs</option>
                            <option value="Not Interested">Not Interested</option>
                            <option value="Internal Agent">Internal Agent</option>
                            <option value="External Agent">External Agent</option>
                            <option value="Family">Family</option>
                            <option value="Friend">Friend</option>
                            <option value="Promoter">Promoter</option>
                     
                            <option value="Other">Other</option>
                        </select>

                        <textarea name="" rows="3" class="form-control d-none mt-3" placeholder="Add Reason for Unsuccessfull"
                            id="lead_reason" required></textarea>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success action-archive" id="saveStatus">
                    Save Reason
                </button>
                <button type="button" class="btn btn-light  action-close modelReasonClose" data-bs-dismiss="modal">
                    Close
                </button>

            </div>
        </div>
    </div>
</div>
