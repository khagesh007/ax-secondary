<div id="widgetEvents">
    <div class="card">

        <div class="card-body mt-0 form-steps">
            <ul class="nav nav-tabs nav-tabs-custom nav-success nav-justified mb-1"
                role="tablist">


                <?php if($call_log): ?>
                <li class="nav-item <?php if($active_tab == 'call_log'): ?> active <?php endif; ?>">

                    <a class="nav-link active" data-bs-toggle="tab"
                       href="#callTab<?php echo e($unique_id); ?>" role="tab" aria-selected="true"
                       onclick="scrollAtBottom('callLogTbody');">
                        <i class="mdi mdi-phone-sync-outline align-middle"></i>
                        Call Log
                    </a>
                </li>
                <?php endif; ?>
            <?php if($meeting_log): ?>
                <li class="nav-item <?php if($active_tab == 'meeting_log'): ?> active <?php endif; ?>">
                    <a class="nav-link align-middle" id="log-meeting-tab" data-bs-toggle="tab" href="#meetingTab<?php echo e($unique_id); ?>"
                       role="tab" aria-selected="false"
                       onclick="scrollAtBottom('meetingLogs');">
                        <i class="mdi mdi-calendar-clock-outline align-middle"></i>
                        Meetings
                    </a>
                </li>
                <?php endif; ?>
                <?php if($task_log): ?>
                <li class="nav-item <?php if($active_tab == 'task_log'): ?> active <?php endif; ?>">
                    <a class="nav-link align-middle"  id="log-task-tab" data-bs-toggle="tab" href="#taskTab<?php echo e($unique_id); ?>"
                       role="tab" aria-selected="false" onclick="scrollAtBottom('taskLogs');">
                        <i class="mdi mdi-calendar-check-outline align-middle"></i>
                        Tasks
                    </a>
                </li>
                <?php endif; ?>
                    <?php if($followup_log): ?>
                <li class="nav-item <?php if($active_tab == 'followup_log'): ?> active <?php endif; ?>">
                    <a class="nav-link align-middle"  id="log-followup-tab" data-bs-toggle="tab" href="#followupTab<?php echo e($unique_id); ?>"
                       role="tab" aria-selected="false"  onclick="scrollAtBottom('followupLogs<?php echo e($unique_id); ?>');">
                        <i class="mdi mdi-calendar-check-outline align-middle"></i>
                        Follow-ups
                    </a>
                </li>
                <?php endif; ?>
                    <?php if($viewing_log): ?>
                        <li class="nav-item <?php if($active_tab == 'viewing_log'): ?> active <?php endif; ?>">
                            <a class="nav-link align-middle" id="log-viewing-tab" data-bs-toggle="tab" href="#viewingLogTab<?php echo e($unique_id); ?>" role="tab" aria-selected="true" onclick="scrollAtBottom('nav-viewing-event');">
                                <i class="ri ri-eye-2-line align-middle"></i>
                                Viewings
                            </a>
                        </li>
                <?php endif; ?>



                <div class="btn-group d-none" >
                    <button type="button" class="btn btn-sm btn-primary dropdown-toggle action-save"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Add
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item d-none" href="javascript:void(0)"
                           data-bs-toggle="modal" data-bs-target="#callModal">Call</a>
                        <a class="dropdown-item" href="javascript:void(0)"
                           id="addMeeting" >Meeting</a>
                        <a class="dropdown-item" href="javascript:void(0)"
                           id="addTask" onclick="addEvent('Task')">Task</a>
                    </div>
                </div><!-- /btn-group -->

            </ul>







            <div class="tab-content text-muted">
                <div class="tab-pane <?php if($active_tab == 'call_log'): ?> active <?php endif; ?>" id="callTab<?php echo e($unique_id); ?>" role="tabpanel">


                    <div id="callLogTbody<?php echo e($unique_id); ?>" data-simpleba class="px-1 mb-1 scrollable-notes ">

                            


                    </div>

                </div>
                <div class="tab-pane <?php if($active_tab == 'meeting_log'): ?> active <?php endif; ?>" id="meetingTab<?php echo e($unique_id); ?>" role="tabpanel">
                    <button type="button" class="btn d-none btn-warning" id="refreshMeetingLog" onclick="getEventLog('<?php echo e($id); ?>', '<?php echo e($model_id); ?>', '<?php echo e($unique_id); ?>', 'Meeting', 'MeetingLogTab'); ">Refresh</button>
                    <div class="float-end"><button type="button" onclick="addEvent('Meeting','refreshMeetingLog')" class="float-right btn btn-primary btn-sm">Add</button> </div>
                    <div id="MeetingLogTab<?php echo e($unique_id); ?>" data-simplebar
                         class="px-1 mb-1 scrollable-notes">
                        <div class="spinner-border text-success mt-5" style="margin-left: 46%;"  role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="tab-pane <?php if($active_tab == 'followup_log'): ?> active <?php endif; ?>" id="followupTab<?php echo e($unique_id); ?>" role="tabpanel">
                    <div class="float-end">
                        <button type="button" class="btn d-none btn-warning" id="refreshFollowupLog" onclick="getEventLog('<?php echo e($id); ?>', '<?php echo e($model_id); ?>', '<?php echo e($unique_id); ?>', 'Follow-up Reminder', 'followupLogs'); ">Refresh</button>
                        <button type="button" onclick="addEvent('Follow-up Reminder','refreshFollowupLog');" class="float-right btn btn-primary btn-sm">Add</button> </div>
                    <div id="followupLogs<?php echo e($unique_id); ?>" data-simplebar
                         class="px-1 mb-1 scrollable-notes">
                        <div class="spinner-border text-success mt-5" style="margin-left: 46%;"  role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="tab-pane <?php if($active_tab == 'task_log'): ?> active <?php endif; ?>" id="taskTab<?php echo e($unique_id); ?>" role="tabpanel">
                    <div class="float-end">
                        <button type="button" class="btn d-none btn-warning" id="refreshTaskLog" onclick="getEventLog('<?php echo e($id); ?>', '<?php echo e($model_id); ?>', '<?php echo e($unique_id); ?>', 'Task', 'taskLogs'); ">Refresh</button>
                        <button type="button" onclick="addEvent('Task','refreshTaskLog')" class="float-right btn btn-primary btn-sm">Add</button> </div>
                    <div id="taskLogs<?php echo e($unique_id); ?>" data-simplebar class="px-1 mb-1 scrollable-notes">
                        <div class="spinner-border text-success mt-5" style="margin-left: 46%;" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>

                <div class="tab-pane <?php if($active_tab == 'viewing_log'): ?> active <?php endif; ?>" id="viewingLogTab<?php echo e($unique_id); ?>" role="tabpanel">
                    <button type="button" class="btn d-none btn-warning" id="refreshViewingLog" onclick="getEventLog('<?php echo e($id); ?>', '<?php echo e($model_id); ?>', '<?php echo e($unique_id); ?>', 'Viewing', 'viewingLogs'); ">Refresh</button>
                    <div class="float-end"><button type="button"  onclick="addEvent('Viewing','refreshViewingLog')" class="float-right btn btn-primary btn-sm">Add</button> </div>
                    <div id="viewingLogs<?php echo e($unique_id); ?>" data-simplebar class="px-1 mb-1 scrollable-notes">
                        <div class="spinner-border text-success mt-5" style="margin-left: 46%;" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/partials/widgets/events.blade.php ENDPATH**/ ?>