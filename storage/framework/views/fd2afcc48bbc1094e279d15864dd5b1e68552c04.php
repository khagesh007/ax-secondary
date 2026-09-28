<style>
    .page-link {
        cursor: pointer;
        color: black !important;
    }
    .page-item .active{
        color: white !important;
    }
    .page-item.active .page-link {
        background-color: #424141;
        border-color: #424141;
        color: white !important;
    }
</style>
<div class="modal zoomIn" id="event-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-danger p-3">
                <h5 class="modal-title" id="event-form-modal-title"><?php echo e($type); ?> Event <?php echo e($listing_lead_id); ?></h5>
                <button type="button" onclick="destroyEventModal()" class="btn-close" data-bs-dismiss="modal"
                        aria-hidden="true"></button>
            </div>
            <div class="modal-body p-4">
                <form class="needs-validation" name="event-form" id="form-event" novalidate>
                    <div class="row event-form">
                        <input type="hidden" id="event_id" name="id"/>
                        <input type="hidden" class="eventInputs" id="calendar_contact_title"/>
                        <input type="hidden" class="eventInputs" id="calendar_lead_title"/>
                        <input type="hidden" class="eventInputs" id="calendar_property_title"/>
                        <input type="hidden" class="eventInputs" id="refreshBtnId"/>
                        <input type="hidden" class="eventInputs" name="listing_lead_id" value="<?php echo e($listing_lead_id); ?>">
                        <div class="col-12">
                            <div class="mb-3">
                                <div class="row">
                                    <div class="col-2 agent_select_input">
                                        <label class="form-label" for="agent_id">User*</label>
                                    </div>
                                    <div class="col-4 agent_select_input">

                                        <select
                                            class="form-select eventInputs <?php if($user == null): ?> userSelect2 <?php endif; ?> select2"
                                            name="agent_id"
                                            id="calendar_agent_id"
                                            required>
                                            <?php if($user != null): ?>
                                                <option data-image="<?php echo e($user?->thumbnail); ?>"
                                                        value="<?php echo e($user->id); ?>"><?php echo e($user->name); ?></option>
                                            <?php else: ?>
                                                <?php $__currentLoopData = $users ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $usr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option data-image="<?php echo e($usr?->thumbnail); ?>"
                                                            value="<?php echo e($usr->id); ?>"><?php echo e($usr->name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                            <?php endif; ?>
                                        </select>
                                        <span onclick="changeUserSelectbox()" style="
    margin-left: -22px;
">
                                            <i class="ri-refresh-fill" style="position: absolute;
    color: #646261e6;
    font-size: 17px;
    cursor: pointer;
    margin-top: 6px;"></i>
                                        </span>
                                        <div class="invalid-feedback">Please select a valid user</div>
                                    </div>
                                    <div class="col-2">
                                        <label class="form-label" for="type">Type*</label>
                                    </div>
                                    <div class="col-4">
                                        <select class="form-select select2 eventInputs" name="event_type_id"
                                                id="calendar_type"
                                                required>
                                            <?php if($event_type != null): ?>
                                                <option value="<?php echo e($event_type->id); ?>"
                                                        selected><?php echo e($event_type->name); ?></option>
                                            <?php else: ?>
                                                <option value="" disabled selected>Select Event Type</option>
                                                <?php $__currentLoopData = $event_types ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $eventType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option <?php if($type == $eventType->name): ?> selected
                                                            <?php endif; ?> value="<?php echo e($eventType->id); ?>"><?php echo e($eventType->name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php endif; ?>

                                        </select>
                                        <div class="invalid-feedback">Please select a valid event type</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <div class="row">
                                    <div class="col-2">
                                        <label class="form-label" for="calendar_start_time">Start Time*</label>
                                    </div>
                                    <div class="col-4">
                                        <div class="input-group">
                                            <input id="calendar_start_time" name="start_time" type="text"
                                                   class="form-control"
                                                   placeholder="Select start time"
                                                   value="<?php echo e(date('d-m-Y H:i',strtotime($date))); ?>">
                                            <span class="input-group-text"><i
                                                    class="ri-time-line"></i></span>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <label class="form-label" for="calendar_end_time">End Time*</label>
                                    </div>
                                    <div class="col-4">
                                        <div class="input-group">
                                            <input id="calendar_end_time" name="end_time" type="text"
                                                   class="form-control"
                                                   placeholder="Select end time"
                                                   <?php
                                                   //add 30 mintues to start time

                                                   $date = strtotime($date);
                                                   $date = strtotime("+30 minutes", $date);
                                                   $date = date('d-m-Y H:i', $date);


                                                   ?>
                                                   value="<?php echo e(date('d-m-Y H:i',strtotime($date))); ?>">
                                            <span class="input-group-text"><i
                                                    class="ri-time-line"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <div  <?php echo e($showProperty ? true : false); ?> class="col-12 <?php if(!$showProperty): ?> d-none <?php endif; ?>" id="modalPropertyInput">
                            <div class="mb-3">
                                <div class="row">

                                    <div class="col-2">
                                        <label class="form-label" for="type">Property</label>
                                    </div>
                                    <div class="col-10">
                                        <select
                                            class="form-control <?php if($properties !='' && count($properties)>0): ?> select2 <?php endif; ?> eventInputs"
                                            name="property_id"
                                            id="calendar_property_ids">
                                            <?php //<option  data-first_image="${item.first_image && item.first_image.path ? item.first_image.path : ''}" data-ref_no="${item.ref_no}" data-unit_no="${item.unit_no}" data-type="${item.property_category?.title || ''}" data-location="${item.location}" value="${item.id}">${item.ref_no} - ${item.property_category?.title || ''} - ${item.unit_no || ''} - ${item.location}</option> ?>
                                            <?php if($property != null): ?>
                                                <option
                                                    data-beds="<?php echo e(!empty($property->bed) ? $property->bed?->name : ''); ?>"
                                                    data-for="<?php echo e($property->for??''); ?>"
                                                    data-first_image="<?php echo e($property?->first_image && $property?->first_image?->path ? $property?->first_image?->path : ''); ?>"
                                                    value="<?php echo e($property->id); ?>"
                                                    selected><?php echo e($property->publish_status ?? ""); ?> <?php echo e($property->ref_no ?? ""); ?> <?php echo e($property->category ?? ""); ?> <?php echo e($property->unit_no ?? ""); ?> <?php echo e($property->location ?? ""); ?> <?php echo e($property->for ?? ""); ?></option>
                                            <?php else: ?>
                                                <option value="" disabled selected>Select Property</option>
                                            <?php endif; ?>
                                            <?php $__currentLoopData = $properties ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $property): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($property->id); ?>"><?php echo e($property->publish_status ?? ""); ?> <?php echo e($property->ref_no ?? ""); ?> <?php echo e($property->category ?? ""); ?> <?php echo e($property->unit_no ?? ""); ?> <?php echo e($property->location ?? ""); ?> <?php echo e($property->for ?? ""); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <div class="invalid-feedback">Please select a property</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-6 mb-3 <?php echo e($showContact); ?>" id="modalContactInputs">
                            <div class="row">
                                <div class="col-4">
                                    <label class="form-label" for="contact">Contact</label>
                                </div>
                                <div class="col-8">
                                    <select class="form-control eventInputs" name="contact_id" id="calendar_contact_id">

                                        <?php if(!empty($contact)): ?>
                                            <option value="<?php echo e($contact->id); ?>"
                                                    selected><?php echo e($contact->first_name .' '.$contact->last_name); ?></option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mb-3 <?php echo e($showLead); ?>" id="modalLeadInputs">
                            <div class="row">
                                <div class="col-4">
                                    <label class="form-label" for="lead">Lead</label>
                                </div>
                                <div class="col-8">
                                    <select class="form-control eventInputs " name="lead_id" id="calendar_lead_id">

                                        <?php if(!empty($lead)): ?>
                                            <option value="<?php echo e($lead->id); ?>"
                                                    selected><?php echo e($lead->first_name .' '.$lead->last_name); ?></option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                        </div>


                        <div class="col-12">
                            <div class="mb-3">
                                <div class="row">
                                    <div class="col-2">
                                        <label class="form-label" for="calendar_input_title">Event title*</label>
                                    </div>
                                    <div class="col-10">
                                        <input class="form-control eventInputs" placeholder="Enter event title"
                                               type="text"
                                               name="title" id="calendar_input_title"

                                               value="<?php echo e($title ?? ''); ?>"

                                               required/>
                                        <div class="invalid-feedback">Please provide a valid event title
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <div class="row">
                                    <div class="col-2">
                                        <label class="form-label" for="event_input_location">Event location</label>
                                    </div>
                                    <div class="col-10">
                                        <input class="form-control" placeholder="Enter event location"
                                               type="text"
                                               name="location" id="event_input_location"
                                               <?php if($property != null && $event_type != null && ($event_type->name == 'Viewing' || $event_type->name == 'Follow-up Reminder')): ?>
                                                   value='<?php echo e($property->location ?? ""); ?>'
                                            <?php endif; ?> />
                                        <div class="invalid-feedback">Please provide a valid event
                                            location
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <div class="row">
                                    <div class="col-2">
                                        <label class="form-label" for="description">Event
                                            Description</label>
                                    </div>
                                    <div class="col-10">
                                                    <textarea class="form-control" name="description" id="description"
                                                              placeholder="Example:
- Tenant Name/Number
- Access to property/Keys location
- Dog's name at the property
- Make sure to show them the Cinema Room on the 1st Floor" rows="3"
                                                              spellcheck="false"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <div class="row">
                                    <div class="col-2">
                                        <label class="form-label" for="priority">Priority</label>
                                    </div>
                                    <div class="col-4">
                                        <select class="form-select eventInputs select2" name="priority"
                                                id="calendar_priority">
                                            <?php $__currentLoopData = \App\Models\Event::PRIORITY_SELECT ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $priority): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option
                                                    value="<?php echo e($priority); ?>"
                                                    <?php if($priority == 'Medium'): ?> selected <?php endif; ?>><?php echo e($priority); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <div class="invalid-feedback">Please select a valid event priority
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <label class="form-label" for="attendees">Attendees</label>
                                        <?php if(auth()->user()->hasRole(['Admin','Super Admin','Line Manager','Sales Director'])): ?>

                                            <div style="font-size: 12px;">
                                                <input type="checkbox" title="Select all users"
                                                       id="event_check_team_attendees" class="form-check-input"
                                                       name="all_team_attendees"> My Team
                                                <input type="checkbox" title="Select all users"
                                                       id="event_check_all_attendees" class="form-check-input"
                                                       name="all_attendees"> All
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-4">


                                        <!-- Input with Icon -->
                                        <div>
                                            <div class="form-icon right">
                                                <select class="form-control form-control-icon" name="attendees[]"
                                                        id="calendar_attendees"
                                                        multiple
                                                        style="height: 35px;"
                                                >

                                                </select>


                                            </div>
                                        </div>
                                        <div class="input-group">


                                        </div>


                                        <div class="invalid-feedback">Please select a valid attendees</div>
                                    </div>


                                </div>
                                <div class="row">
                                    <div class="col-8"></div>
                                    <div class="col-4 mt-2">
                                        <div class="form-check d-none" id="send_notification_div">
                                            <input class="form-check-input" type="checkbox"
                                                   name="send_notification" id="send_notification"
                                                   title="Email to attendees" value="1" checked>
                                            <label class="form-check-label text-muted"
                                                   for="send_notification" title="Send notification attendees">
                                                <i class="bx bx-mail-send"
                                                   style="vertical-align: middle;"></i> Send Notifications</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <div class="row">

                                </div>
                            </div>
                        </div>

                        <hr>
                        <div class="col-12">
                            <div class="mb-3">
                                <div class="row">
                                    <div class="col-2">
                                        <label class="form-label" for="reminder">Reminder</label>
                                    </div>
                                    <div class="col-10">
                                        <div class="row">
                                            <div class="row">
                                                <div class="col-4">
                                                    <div class="form-check mt-2">
                                                        <input class="form-check-input" type="checkbox"
                                                               name="reminder_to_attendees"
                                                               id="reminder_to_attendees" value="1">
                                                        <label class="form-check-label text-muted"
                                                               for="reminder_to_attendees">
                                                            <i class="bx bx-alarm"></i>
                                                            Send Reminder</label>
                                                    </div>

                                                </div>

                                                <div class="col-4 d-none reminder-sec">
                                                    <!-- inline form -->


                                                    <div class="form-group">
                                                        <input class="form-control" placeholder="Time before event"
                                                               type="number"
                                                               name="reminder" id="calendar_reminder"/>
                                                        <div class="invalid-feedback">Please provide a valid
                                                            event reminder
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-4 d-none reminder-sec">
                                                    <select class="form-select select2"
                                                            name="reminder_frequency"
                                                            id="reminder_frequency">
                                                        <?php $__currentLoopData = \App\Models\Event::REMINDER_FREQUENCY_SELECT ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reminder): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option
                                                                value="<?php echo e($reminder); ?>"><?php echo e($reminder); ?></option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </select>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Create Recurring Event')): ?>
                        <hr>
                        <div class="col-12">
                            <div class="mb-3">
                                <div class="row">
                                    <div class="col-3">
                                        <label class="form-label" for="reminder">Recurring Event?</label>
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox"
                                                   name="event_recurring"
                                                   id="event_recurring_checkbox" value="1">
                                            <label class="form-check-label text-muted"
                                                   for="event_recurring_checkbox">
                                                <i class="ri-repeat-2-line"></i>
                                                Yes</label>
                                        </div>
                                    </div>
                                    <div class="col-9  d-none recurring-sec">

                                        <div class="row">


                                            <div class="col-2">
                                                <label>Frequency</label>
                                            </div>
                                            <div class="col-4">

                                                <select class="form-select select2"
                                                        name="recurring_frequency"
                                                        id="recurring_frequency">
                                                    <option selected disabled value="">Select Frequency</option>
                                                    <option value="daily">Daily</option>
                                                    <option value="weekly">Weekly</option>
                                                </select>
                                            </div>
                                            <div class="col-2 mt-2">
                                                <label>- OR - On</label>
                                            </div>
                                            <div class="col-4">

                                                <input type="date" name="recurring_date" id="recurring_date"
                                                       class="form-control">
                                            </div>
                                        </div>
                                        <div class="row mt-2" id="event_days_list">
                                            <div class="col-2 mt-3">
                                                <label>Days</label>
                                            </div>

                                            <div class=" col-10 mt-2" style="    padding: 6px;">
                                                <ul class="pagination pagination-separated">


                                                    <li onclick="addRecurringDays('MON')" class="page-item rec-days"
                                                        style=" margin: 0 0px 0 0px;">
                                                        <a href="#" class="page-link">Mon</a>
                                                    </li>
                                                    <li onclick="addRecurringDays('TUE')" class="page-item rec-days"
                                                        style=" margin: 0 0px 0 0px;">
                                                        <a href="#" class="page-link">Tue</a>
                                                    </li>
                                                    <li onclick="addRecurringDays('WED')" class="page-item rec-days"
                                                        style=" margin: 0 0px 0 0px;">
                                                        <a href="#" class="page-link">Wed</a>
                                                    </li>
                                                    <li onclick="addRecurringDays('THU')" class="page-item rec-days"
                                                        style=" margin: 0 0px 0 0px;">
                                                        <a href="#" class="page-link">Thu</a>
                                                    </li>
                                                    <li onclick="addRecurringDays('FRI')" class="page-item rec-days"
                                                        style=" margin: 0 0px 0 0px;">
                                                        <a href="#" class="page-link">Fri</a>
                                                    </li>
                                                    <li onclick="addRecurringDays('SAT')" class="page-item rec-days"
                                                        style=" margin: 0 0px 0 0px;">
                                                        <a href="#" class="page-link">Sat</a>
                                                    </li>
                                                    <li onclick="addRecurringDays('SUN')" class="page-item rec-days" id="daysSunday"
                                                        style=" margin: 0 0px 0 0px;">
                                                        <a href="#" class="page-link">Sun</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-2">
                                                Ends After
                                            </div>
                                            <div class="col-5">
                                                <div class="input-group">
                                                    <input type="number" class="form-control" name="recurring_count"
                                                           id="event_recurring_count" placeholder="x times"
                                                           aria-label="how many times" aria-describedby="occurance_label">
                                                    <span class="input-group-text" id="occurences_label">Occurrences</span>
                                                </div>
                                            </div>
                                            <div class="col-5">
                                                <div class="input-group">
                                                    <input type="date" class="form-control" name="recurring_end_date"
                                                           id="recurring_end_date" placeholder="recurring date"
                                                           aria-label="recurring date" aria-describedby="basic-addon3">
                                                    <span class="input-group-text" id="basic-addon3">Date</span>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 event-occurring-summary">

                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>
                        <?php endif; ?>
                    </div>


            <div class="hstack gap-2 justify-content-end">
                <button type="button" class="btn btn-light" onclick="destroyEventModal()" data-bs-dismiss="modal">
                    Close
                </button>
                <button type="submit" class="btn btn-success action-save" id="btn-save-event">Save
                </button>
            </div>
            </form>
        </div>
    </div>
</div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/events/include/addModal.blade.php ENDPATH**/ ?>