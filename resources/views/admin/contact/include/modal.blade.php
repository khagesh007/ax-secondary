<div class="modal fade" id="callModal" tabindex="-1" aria-labelledby="callModalLabel" aria-modal="true">
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
                            <input type="hidden" name="id" id="call_contact_id" value="">
                            <input type="datetime-local" class="form-control" id="call_followup_time" name="time"
                                placeholder="Enter follow-up time" required>
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
                                <button type="button" class="btn btn-light action-close"
                                    data-bs-dismiss="modal">Close</button>
                                <button id="submitBtn" type="submit"
                                    class="btn btn-primary action-save">Submit</button>
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



<div id="assignAgentsModal" class="modal fade" tabindex="-1" aria-labelledby="assignAgentsModalLabel"
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
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary ">Save</button>
            </div>
        </form>
    </div>
</div>


<div id="bulkReassignContactModal" class="modal fade" tabindex="-1" aria-labelledby="bulkReassignContactModalLabel"
    aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-md">
        <form id="bulkReassignContactForm" class="modal-content needs-validation" action="javascript:void(0)">
            <div class="modal-header bg-soft-danger">
                <h5 class="modal-title" id="bulkReassignContactModalLabel">Assign <span id="bulk_leads_count"></span>
                    contacts</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-4">Select Agent*</div>
                    <div class="col-8">
                        <select name='bulk_leads_agent_id' id="bulk_leads_agent_id" class="form-control select2">
                            <option></option>
                            @foreach ($agents ?? [] as $agent)
                                <option value="{{ $agent->id }}">{{ $agent->first_name }} {{ $agent->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-4 mt-2">
                        Assign As
                    </div>
                    <div class="col-8 mt-2">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="assign_type" id="inlineRadio1"
                                value="primary" checked required>
                            <label class="form-check-label" for="inlineRadio1">Primary agent</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="assign_type" id="inlineRadio2"
                                value="additional" required>
                            <label class="form-check-label" for="inlineRadio2">As additional agent</label>
                        </div>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" id="bulkLeadsSubmitBtn">Assign Now!</button>
            </div>
        </form>
    </div>
</div>

<!-- Default Modals -->



<div class="modal zoomIn" id="startACall" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog  modal-md modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-soft-danger">

                <h5 class="modal-title" id="generalModalLabel">Dial Number</h5>

                <button type="button" class="btn-close CancelBTN" data-bs-dismiss="modal" aria-label="Close"
                    id="close-modal"></button>
            </div>
            <div class="modal-body p-3 text-center text-dark">
                <div class="row">
                    <h5 id="call_id"></h5>
                    <h1> <span id="countdown" style="font-weight: bold;">00:05</span></h3>
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



<div id="send-bulk-sms" class="modal zoomIn" tabindex="-1" aria-labelledby="send-bulk-smsLabel" aria-hidden="true"
    style="display: none;">
    <div class="modal-dialog modal-lg">
        <form id="send-bulk-sms-form" class="modal-content needs-validation" novalidate>
            <div class="modal-header bg-soft-danger">
                <h5 class="modal-title" id="send-bulk-smsLabel">Bulk Sms</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body">
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-start align-items-center gap-2">
                            <h5><strong>Write your Message</strong> </h5>

                        </div>
                    </div>
                    <div class="col-xl-12">
                        <div class="card crm-widget">
                            <div class="card-body p-0">
                                <div class="row row-cols-md-3 row-cols-1">
                                    <div class="col col-lg border-end">
                                        <div class="py-4 px-3">
                                            <h5 class="text-muted text-uppercase fs-13">Total
                                            </h5>
                                            <div class="d-flex align-items-center">

                                                <div class="flex-grow-1 ms-3">
                                                    <h2 class="mb-0"><span class="counter-value"
                                                            id="total-character">0</span></h2>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- end col -->

                                    <div class="col col-lg border-end">
                                        <div class="py-4 px-3">
                                            <h5 class="text-muted text-uppercase fs-13">Parts
                                            </h5>
                                            <div class="d-flex align-items-center">

                                                <div class="flex-grow-1 ms-3">
                                                    <h2 class="mb-0"><span class=""
                                                            id="total-parts">0</span></h2>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- end col -->
                                    <div class="col col-lg border-end">
                                        <div class="py-4 px-3">
                                            <h5 class="text-muted text-uppercase fs-13">Remaining
                                            </h5>
                                            <div class="d-flex align-items-center">

                                                <div class="flex-grow-1 ms-3">
                                                    <h2 class="mb-0"><span class=""
                                                            id="remaining-character">160</span></h2>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- end col -->
                                    <div class="col col-lg border-end">
                                        <div class="py-4 px-3">
                                            <h5 class="text-muted text-uppercase fs-13">Contacts<span title="The number of sent messages may vary, as CRM only send SMS to valid numbers. If the number is not valid, the SMS will not be sent."><i
                                                class=" ri-information-line"></i></span>
                                            </h5>
                                            <div class="d-flex align-items-center">

                                                <div class="flex-grow-1 ms-3">
                                                    <h3 class="mb-0"><span class="contacts-count" id="contacts-count">-</span> </h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- end col -->
                                    <div class="col col-lg border-end">
                                        <div class="py-4 px-3">
                                            <h5 class="text-muted text-uppercase fs-13">Encoding
                                            </h5>
                                            <div class="d-flex align-items-center">

                                                <div class="flex-grow-1 ms-3">
                                                    <h2 class="mb-0">GSM</h2>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- end col -->

                                </div><!-- end row -->
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label for="campaign_name" class="form-label">Campaign Name</label>
                            <input name="campaign_name" type="campaign_name"
                                placeholder="Name of your Campaign" id="campaign_name" class="form-control inputs">
                        </div>
                    </div><!--end col-->
                    <div class="col-12 mt-2">
                        <div class="row">
                            <p class="text-muted">Use these for more customizations: <code>  @{{ name }} </code>: For Name</p>

                            <div class="col-12">
                                <div class="mb-3">
                                    <textarea name="message" id="bulk-message" class="form-control sms-counter" rows="5" placeholder="Dear @{{ name }}, Your Message."></textarea>
                                </div>
                            </div><!--end col-->
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="send_checking_link" name="send_checking_link" checked="">
                            <label class="form-check-label" for="send_checking_link">
                                Send Tracking Link
                            </label>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label for="ForminputState" class="form-label">Custom Link</label>
                            <input name="custom_link" type="url"
                                placeholder="if you want to send any custom link paste here." id="custom_link"
                                class="form-control inputs sms-counter">
                        </div>
                    </div><!--end col-->


                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="ForminputState" class="form-label">Preview</label>
                            <div>
                                <span id="msg-preview">Write your message</span>
                                <span id="link-preview"></span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light action-close" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary action-save">Send message to <span class="contacts-count">-</span</button>
            </div>
        </form>
    </div>
</div>
