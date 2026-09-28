<div class="col-md-6 col-sm-12 addHide">
    <div class="card">

        <div class="card-body mt-0 form-steps">
            <ul class="nav nav-tabs nav-tabs-custom nav-success nav-justified mb-0" role="tablist">

                <li class="nav-item active">


                    <a class="nav-link active" data-bs-toggle="tab" href="#notesTab" role="tab"
                        aria-selected="true" id="getNotesLog">
                        <i class="mdi mdi-note-text align-middle"></i>
                        Notes</a>
                </li>

                <li class="nav-item addHide">
                    <a class="nav-link" data-bs-toggle="tab" href="#activityLogTab" role="tab"
                        aria-selected="false" id="getActivityLog">
                        <i class="mdi mdi-history align-middle"></i>
                        Activity Log</a>
                </li>
                <li class="nav-item addHide">
                    <a class="nav-link" data-bs-toggle="tab" href="#bitrixDataTab" role="tab"
                        aria-selected="false">
                        <i class="mdi mdi-hours-24 align-middle"></i>
                        Bitrix Data</a>
                </li>
                

            </ul>
        </div>


        <div class="tab-content text-muted">
            <div class="tab-pane active" id="notesTab" role="tabpanel">

                <div id="notes-log-div_Placeholder">
                    <?php echo $__env->make('admin.lead.include.partials.notes._notelog_placeholder', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>



                <div id="no_notes" class="d-none">
                    <?php echo $__env->make('admin.lead.include.partials.notes._empty_notes', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>

                <div  class="" id="notes">
                </div>
                <div class="row align-items-center">

                    <div class="input-group">
                        <textarea class="form-control" id="note" rows="2" placeholder="Enter a note"></textarea>
                        <button class="btn btn-primary btn-icon waves-effect waves-light"
                            type="button" title="Save Note" id="addNoteButton"
                            style="height:auto;">
                            <i class="mdi mdi-send-outline"></i>

                        </button>

                    </div>



                </div>
            </div>
            <div class="tab-pane" id="additionalAgentTab" role="tabpanel">
                <div class="col-md-12 col-sm-12 d-none" id="addOnAgentDiv">
                    <div class="">
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1"></h4>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Leads Additional Agents Add')): ?>
                                <div class="flex-shrink-0">
                                    <div class="dropdown card-header-dropdown" style="    width: 225px;">
                                        <select name='agent_id_add' id="add_on_agent"
                                            placeholder="Select Agent" class="form-control">
                                            <option selected disabled>Select Agent</option>
                                            <?php $__currentLoopData = $agents ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($agent->id); ?>">
                                                    <?php echo e($agent->first_name); ?>

                                                    <?php echo e($agent->last_name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>

                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive table-card">
                                <table class="table table-borderless table-nowrap align-middle mb-0">
                                    <thead class="table-light text-muted">
                                        <tr>
                                            <th scope="col">Agent</th>
                                            <th scope="col">Added By</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="agent_table"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="bitrixDataTab" role="tabpanel">
                <div class="row">

                    <div data-simplebar class="px-3 mb-2 scrollable-notes "
                        style="padding-left: 27px !important;" id="bitrix_data_note">
                    </div>

                </div>
            </div>

            <div class="tab-pane" id="duplicateData" role="tabpanel">
                <div class="row">

                    <div data-simplebar class="px-3 mb-2 scrollable-notes"
                        style="padding-left: 27px !important;" id="duplicate">

                    </div>

                </div>
            </div>


            <div class="tab-pane" id="activityLogTab" role="tabpanel">
                <div class="d-none scrollable-notes pt-0" id="activity-log-div">
                    <div class="card-header p-05rem">
                        <h6 class="card-title mb-0">Activities </h6>
                    </div>

                    <div id="activity-log-div_Placeholder">
                        <?php echo $__env->make('admin.lead.include.partials.activity._activitylog_placeholder', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </div>

                    <div id="activity-log-div2" class="scrolltobottom">

                    </div>

                    <div id="no_activity" class="d-none">
                        <?php echo $__env->make('admin.lead.include.partials.activity._empty_activities', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </div>

                    <div class="profile-timeline user-activity-timeline d-none" id="activities">

                    </div>

                    <div class="d-none p-lg-2" id="view-edit-mode">
                        <small class="mb-0 text-muted">Created: <span id="created_by_span"></span><br>Updated:
                            <span id="updated_by_span"> </span></small>
                    </div>

                </div>

            </div>
        </div>

    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/lead/include/partials/_activities.blade.php ENDPATH**/ ?>