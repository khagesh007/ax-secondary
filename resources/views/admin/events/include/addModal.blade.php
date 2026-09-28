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
                <h5 class="modal-title" id="event-form-modal-title">{{$type}} Event {{$listing_lead_id}}</h5>
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
                        <input type="hidden" class="eventInputs" name="listing_lead_id" value="{{$listing_lead_id}}">
                        <div class="col-12">
                            <div class="mb-3">
                                <div class="row">
                                    <div class="col-2 agent_select_input">
                                        <label class="form-label" for="agent_id">User*</label>
                                    </div>
                                    <div class="col-4 agent_select_input">

                                        <select
                                            class="form-select eventInputs @if($user == null) userSelect2 @endif select2"
                                            name="agent_id"
                                            id="calendar_agent_id"
                                            required>
                                            @if($user != null)
                                                <option data-image="{{$user?->thumbnail}}"
                                                        value="{{ $user->id }}">{{ $user->name }}</option>
                                            @else
                                                @foreach($users ?? [] as $usr)
                                                    <option data-image="{{$usr?->thumbnail}}"
                                                            value="{{ $usr->id }}">{{ $usr->name }}</option>
                                                @endforeach

                                            @endif
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
                                            @if($event_type != null)
                                                <option value="{{ $event_type->id }}"
                                                        selected>{{ $event_type->name }}</option>
                                            @else
                                                <option value="" disabled selected>Select Event Type</option>
                                                @foreach($event_types ?? [] as $eventType)
                                                    <option @if($type == $eventType->name) selected
                                                            @endif value="{{ $eventType->id }}">{{ $eventType->name }}</option>
                                                @endforeach
                                            @endif

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
                                                   value="{{date('d-m-Y H:i',strtotime($date))}}">
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
                                                   value="{{date('d-m-Y H:i',strtotime($date))}}">
                                            <span class="input-group-text"><i
                                                    class="ri-time-line"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <div  {{$showProperty ? true : false}} class="col-12 @if(!$showProperty) d-none @endif" id="modalPropertyInput">
                            <div class="mb-3">
                                <div class="row">

                                    <div class="col-2">
                                        <label class="form-label" for="type">Property</label>
                                    </div>
                                    <div class="col-10">
                                        <select
                                            class="form-control @if($properties !='' && count($properties)>0) select2 @endif eventInputs"
                                            name="property_id"
                                            id="calendar_property_ids">
                                            <?php //<option  data-first_image="${item.first_image && item.first_image.path ? item.first_image.path : ''}" data-ref_no="${item.ref_no}" data-unit_no="${item.unit_no}" data-type="${item.property_category?.title || ''}" data-location="${item.location}" value="${item.id}">${item.ref_no} - ${item.property_category?.title || ''} - ${item.unit_no || ''} - ${item.location}</option> ?>
                                            @if($property != null)
                                                <option
                                                    data-beds="{{!empty($property->bed) ? $property->bed?->name : ''}}"
                                                    data-for="{{ $property->for??'' }}"
                                                    data-first_image="{{$property?->first_image && $property?->first_image?->path ? $property?->first_image?->path : ''}}"
                                                    value="{{ $property->id }}"
                                                    selected>{{ $property->publish_status ?? ""}} {{ $property->ref_no ?? ""}} {{ $property->category ?? "" }} {{ $property->unit_no ?? "" }} {{ $property->location ?? "" }} {{ $property->for ?? ""}}</option>
                                            @else
                                                <option value="" disabled selected>Select Property</option>
                                            @endif
                                            @foreach($properties ?? [] as $property)
                                                <option value="{{ $property->id }}">{{ $property->publish_status ?? ""}} {{ $property->ref_no ?? ""}} {{ $property->category ?? "" }} {{ $property->unit_no ?? "" }} {{ $property->location ?? "" }} {{ $property->for ?? ""}}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">Please select a property</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-6 mb-3 {{$showContact}}" id="modalContactInputs">
                            <div class="row">
                                <div class="col-4">
                                    <label class="form-label" for="contact">Contact</label>
                                </div>
                                <div class="col-8">
                                    <select class="form-control eventInputs" name="contact_id" id="calendar_contact_id">

                                        @if(!empty($contact))
                                            <option value="{{ $contact->id }}"
                                                    selected>{{ $contact->first_name .' '.$contact->last_name }}</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mb-3 {{$showLead}}" id="modalLeadInputs">
                            <div class="row">
                                <div class="col-4">
                                    <label class="form-label" for="lead">Lead</label>
                                </div>
                                <div class="col-8">
                                    <select class="form-control eventInputs " name="lead_id" id="calendar_lead_id">

                                        @if(!empty($lead))
                                            <option value="{{ $lead->id }}"
                                                    selected>{{ $lead->first_name .' '.$lead->last_name }}</option>
                                        @endif
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

                                               value="{{$title ?? ''}}"

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
                                               @if($property != null && $event_type != null && ($event_type->name == 'Viewing' || $event_type->name == 'Follow-up Reminder'))
                                                   value='{{ $property->location ?? "" }}'
                                            @endif />
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
                                            @foreach (\App\Models\Event::PRIORITY_SELECT ?? [] as $priority)
                                                <option
                                                    value="{{ $priority }}"
                                                    @if($priority == 'Medium') selected @endif>{{ $priority }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">Please select a valid event priority
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <label class="form-label" for="attendees">Attendees</label>
                                        @if(auth()->user()->hasRole(['Admin','Super Admin','Line Manager','Sales Director']))

                                            <div style="font-size: 12px;">
                                                <input type="checkbox" title="Select all users"
                                                       id="event_check_team_attendees" class="form-check-input"
                                                       name="all_team_attendees"> My Team
                                                <input type="checkbox" title="Select all users"
                                                       id="event_check_all_attendees" class="form-check-input"
                                                       name="all_attendees"> All
                                            </div>
                                        @endif
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
                                                        @foreach (\App\Models\Event::REMINDER_FREQUENCY_SELECT ?? [] as $reminder)
                                                            <option
                                                                value="{{ $reminder }}">{{ $reminder }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @can('Create Recurring Event')
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
                        @endif
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
