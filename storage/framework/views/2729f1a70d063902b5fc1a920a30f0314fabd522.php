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

                        <div class="col-4">
                            <label class="form-check-label" for="statusCheckbox">Note</label>
                        </div>
                        <div class="col-8">

                            <textarea class="form-control" name="note" placeholder="Enter note"></textarea>

                        </div>

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


<div id="exportModal" class="modal zoomIn" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <form id="exportForm" class="modal-content needs-validation" novalidate>
            <div class="modal-header bg-soft-danger">
                <h5 class="modal-title" id="exportModalLabel">Export</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body">
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-start align-items-center gap-2">
                            <h5><strong>Select columns</strong> </h5>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="select-all-export">
                                <label class="form-check-label" for="select-all-export">
                                    Select All
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mt-2">
                        <div class="row">
                            <?php $__currentLoopData = getLeadsExportColumns(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-4">
                                <div class="form-check form-switch form-switch-success">
                                    <input class="form-check-input export-columns" type="checkbox" name="columns[]" value="<?php echo e($index); ?>" role="switch" id="export-column-<?php echo e($index); ?>">
                                    <label class="form-check-label" for="export-column-<?php echo e($index); ?>">
                                        <?php echo e($column); ?>

                                    </label>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light action-close" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary action-save">Export</button>
            </div>
        </form>
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
                            <?php $__currentLoopData = $agents ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($agent->id); ?>"><?php echo e($agent->first_name); ?> <?php echo e($agent->last_name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                    Lead(s)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-4">Select Agent*</div>
                    <div class="col-8">
                        <select name='bulk_leads_agent_ids' id="bulk_leads_agent_ids" class="form-control select2">

                            <?php $__currentLoopData = $agents ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($agent->id); ?>"><?php echo e($agent->first_name); ?> <?php echo e($agent->last_name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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

                            <?php $__currentLoopData = $telesales ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($agent->id); ?>"><?php echo e($agent->first_name); ?> <?php echo e($agent->last_name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                            <?php $__currentLoopData = $telesalesheads ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($agent->id); ?>"><?php echo e($agent->first_name); ?> <?php echo e($agent->last_name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                    Archive Lead(s)
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
                <h5 class="modal-title" id="generalModalLabel">Assign <span id="bulk_leads_count"></span>
                    Leads</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <div class="mb-3">
                    <label for="" class="me-3">Assign to</label>

                    <div class="form-check form-check-inline">
                        <input class="form-check-input selectAction" type="radio" name="selectAction" id="inlineRadio1" value="Re-Assign" checked>
                        <p class="form-check-label" for="inlineRadio1">Agent</p>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input selectAction" type="radio" name="selectAction" id="inlineRadio2" value="Telesales">
                        <p class="form-check-label" for="inlineRadio2">Telesales</p>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input selectAction" type="radio" name="selectAction" id="inlineRadio3" value="Telesales Head">
                        <p class="form-check-label" for="inlineRadio3">Telesales Head</p>
                    </div>
                </div>
                

                <div class="row mt-2" id="agent_dropdown">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Leads Assign Agent')): ?>
                        

                        <?php echo $__env->make('admin.templates._agents_dynamic_select2', ['id' => 'agents_dropdown', 'label' => 'Agents','agents' => $all_active_agents ??[]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


                    <?php endif; ?>
                </div>

                <div class="row d-none mt-2" id="telesales_dropdown">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Bulk Telesales Assignments')): ?>

                    <?php echo $__env->make('admin.templates._agents_dynamic_select2', ['id' => 'bulk_leads_telesales', 'label' => 'Select Telesales','agents' => $telesales ??[]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


                        
                    <?php endif; ?>
                </div>

                <div class="row d-none mt-2" id="telesales_head_dropdown">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Bulk Telesales Head Assignments')): ?>

                    <?php echo $__env->make('admin.templates._agents_dynamic_select2', ['id' => 'bulk_leads_telesales_leader', 'label' => 'Select Telesales head','agents' => $telesalesheads ??[]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                        
                    <?php endif; ?>
                </div>

                <div class="row mt-3">
                    <div class="col-4">Status</div>
                    <div class="col-lg-8">
                        <div class="mt-4 mt-lg-0">

                            <div class="form-check form-switch form-check-inline" dir="ltr">
                                <input type="checkbox" class="form-check-input change_status_new"
                                    id="change_status_new" value="1" checked name="change_status_new">
                                <label class="form-check-label" for="change_status_new">Change Status to <span
                                        class="text-danger">NEW</span> </label>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-4">Notifications</div>
                    <div class="col-lg-8">
                        <div class="mt-4 mt-lg-0">
                            <div class="form-check form-switch form-check-inline" dir="ltr">
                                <input type="checkbox" class="form-check-input email_notification"
                                    id="email_notification" name="email_notification" disabled>
                                <label class="form-check-label" for="email_notification">Email</label>
                            </div>
                            <div class="form-check form-switch form-check-inline" dir="ltr">
                                <input type="checkbox" class="form-check-input system_notification"
                                    id="system_notification" name="system_notification" disabled>
                                <label class="form-check-label" for="system_notification">System</label>
                            </div>
                            <div class="form-check form-switch form-check-inline" dir="ltr">
                                <input type="checkbox" class="form-check-input telegram_notification"
                                    id="telegram_notification" checked name="telegram_notification">
                                <label class="form-check-label" for="telegram_notification">Telegram</label>
                            </div>
                            <br>


                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light  action-close" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary  action-assign" id="bulkAssignment">Assign
                    Now</button>
                    <button type="button" class="btn btn-primary  action-assign d-none" disabled id="bulkAssignmentLoading"><span class="d-flex align-items-center">
                        <span class="flex-grow-1 me-2">
                            Loading...
                        </span>
                        <span class="spinner-border spinner-border-small flex-shrink-0" role="status" style="">
                            <span class="visually-hidden">Loading...</span>
                        </span>
                    </span></button>
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
                    <h1> <span id="countdown" style="font-weight: bold;">00:05</span></h1>
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



<div class="modal zoomIn" id="unsuccessfullReasonModal" tabindex="-1" aria-hidden="true"
    data-bs-backdrop="static">
    <div class="modal-dialog  modal-md modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-soft-danger">
                <h5 class="modal-title" id="unsuccessfullReasonLabel">Lead Unsuccessful Reason</h5>
                <button type="button" class="btn-close modelReasonClose" data-bs-dismiss="modal" aria-label="Close"
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
                            <option value="Duplicated">Duplicated</option>
                            <option value="Not Looking Anymore">Not Looking Anymore</option>

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




<div class="modal zoomIn" id="transferToLeadpoolModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog  modal-md modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-soft-danger">
                <h5 class="modal-title" id="archiveModalLabel">Transfer Reason</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                    id="close-modal"></button>
            </div>
            <div class="modal-body px-5   py-3">
                <div class="row">
                    <div class="col-3">Reason*:</div>
                    <div class="col-9">
                        <input type="hidden" id="transfer_lead_id">


                        <select name="" id="preFillOption_two" class="form-control">
                            <option value=""></option>
                            <option value="Inactive Agent">Inactive Agent</option>
                            <option value="Not actioned">Not actioned</option>
                            <option value="Other">Other</option>
                        </select>

                        <textarea name="" rows="3" class="form-control d-none mt-3" placeholder="Add Reason for Transfer"
                            id="transfer_note" required></textarea>


                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success action-archive" id="savetransferToLeadpool">
                    Transfer to Lead Pool
                </button>
                <button type="button" class="btn btn-light  action-close" data-bs-dismiss="modal">
                    Close
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
                            <p class="text-muted">Use these for more customizations: <code>  {{ name }} </code>: For Name</p>

                            <div class="col-12">
                                <div class="mb-3">
                                    <textarea name="message" id="bulk-message" class="form-control sms-counter" rows="5" placeholder="Dear {{ name }}, Your Message."></textarea>
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

<div class="modal zoomIn" id="leadDealModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog  modal-md modal-dialog-scrollable">
        <div class="modal-content">
            <form id="leadDealForm" class="modal-content needs-validation" novalidate>
            <div class="modal-header bg-soft-danger">
                <h5 class="modal-title" id="archiveModalLabel">Create Deal Using This Lead</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                    id="close-modal"></button>
            </div>
            <div class="modal-body px-5   py-3">
                <div class="row">
                    <div class="col-4">Listing Ref. No.*:</div>
                    <div class="col-8">
                        <input type="hidden" name="lead_id" id="deal_lead_id">
                        <input type="text" name="ref_no" id="deal_lead_listing_ref_no" class="form-control" required
                        placeholder="Enter the listing ref. no.">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success action-archive btn-load"
                id="leadDealSubmit"
                >
                  Create Deal
                </button>
                <button type="button" class="btn btn-light  action-close" data-bs-dismiss="modal">
                    Close
                </button>

            </div>
            </form>
        </div>
    </div>
</div>

<div class="modal zoomIn" id="leadOfferModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog  modal-md modal-dialog-scrollable">
        <div class="modal-content">
            <form id="leadOfferForm" class="modal-content needs-validation" novalidate>
                <div class="modal-header bg-soft-danger">
                    <h5 class="modal-title" id="archiveModalLabel">Create new offer for this lead</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            id="close-modal"></button>
                </div>
                <div class="modal-body px-5   py-3">
                    <div class="row">
                        <div class="col-4">Amount *:</div>
                        <div class="col-8">
                            <input type="hidden" name="lead_id" id="offer_lead_id">
                            <input type="text" name="amount" id="" class="form-control cleave" required
                                   placeholder="Enter the amount">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-4">Note :</div>
                        <div class="col-8">
                           <textarea class="form-control" name="notes" rows="5"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success action-archive btn-load"
                            id="leadOfferSubmit"
                    >
                        Create Offer
                    </button>
                    <button type="button" class="btn btn-light  action-close" data-bs-dismiss="modal">
                        Close
                    </button>

                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal zoomIn" id="qualifiedNoteModal" tabindex="-1" aria-hidden="true"
     data-bs-backdrop="static">
    <div class="modal-dialog  modal-md modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-soft-danger">
                <h5 class="modal-title" id="qualifiedNoteLabel">Qualified Lead Note</h5>
                <button type="button" class="btn-close modelQualifiedNoteClose" data-bs-dismiss="modal" aria-label="Close"
                        id="close-modal"></button>
            </div>
            <div class="modal-body px-5   py-3">
                <div class="row">
                    <div class="col-3">Note*:</div>
                    <div class="col-9">
                        <input type="hidden" id="qualified_lead_id">

                        <textarea name="" rows="3" class="form-control mt-3" placeholder="Add Note for Qualified"
                                  id="qualified_lead_note" required></textarea>
                        <small id="qualified_lead_note-help" class="text-danger">Minimum 35 characters required.</small>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success action-archive" id="saveQualifiedStatus">
                    Save Note
                </button>
                <button type="button" class="btn btn-light  action-close modelQualifiedNoteClose" data-bs-dismiss="modal">
                    Close
                </button>

            </div>
        </div>
    </div>
</div>


<div class="modal zoomIn" id="transferToPortalLeadPoolModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog  modal-md modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-soft-danger">
                <h5 class="modal-title" id="transferToPortalLeadPoolModalLabel">Transfer Reason</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="close-modal"></button>
            </div>
            <div class="modal-body px-5   py-3">
                <div class="row">
                    <div class="col-3">Reason*:</div>
                    <div class="col-9">
                        <input type="hidden" id="transfer_portal_lead_pool_lead_id">
                        <textarea name="" rows="3" class="form-control mt-3" placeholder="Add Reason for Transfer"
                                  id="transfer_portal_lead_pool_note" required></textarea>
                        <small id="transfer_portal_lead_pool_note-help" class="text-danger">Minimum 35 characters required.</small>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success action-archive" id="saveTransferToPortalLeadPool">
                    Transfer to Lead Pool
                </button>
                <button type="button" class="btn btn-light  action-close" data-bs-dismiss="modal">
                    Close
                </button>

            </div>
        </div>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/lead/include/modal.blade.php ENDPATH**/ ?>