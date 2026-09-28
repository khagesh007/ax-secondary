<div class="card own_property d-none" id="eventsLog">

    <div class="card-body mt-0 form-steps pb-0">
        <ul class="nav nav-tabs nav-tabs-custom nav-success nav-justified mb-3" role="tablist">

            <li class="nav-item active">
                <a class="nav-link no-wrap active" data-bs-toggle="tab" href="#notesTab" role="tab" aria-selected="true"
                    {{-- onclick="scrollAtBottom('notes');" --}}>
                    <i class="mdi mdi-note-text align-middle"></i>
                    Events</a>
            </li>
            <li class="nav-item addHide">
                <a class="nav-link no-wrap" data-bs-toggle="tab" href="#activityLogTab" role="tab"
                    aria-selected="false" id="getActivityLog">
                    <i class="mdi mdi-history align-middle"></i>
                    Activity Log</a>
            </li>
            <li class="nav-item addHide">
                <a class="nav-link no-wrap" data-bs-toggle="tab" href="#propertyCycleTab" role="tab"
                    aria-selected="false" id="getStatusTimeline">
                    <i class="mdi mdi-history align-middle"></i>
                    Listing Cycle</a>
            </li>
        </ul>
    </div>


    <div class="tab-content text-muted">
        <div class="tab-pane active" id="notesTab" role="tabpanel">
            @include('admin.listing.includes.partials._notes')

        </div>
        <div class="tab-pane" id="activityLogTab" role="tabpanel">

            <div class="d-none scrollable-notes pt-0" id="activity-log-div">
                <div class="card-header p-05rem">
                    <h6 class="card-title mb-0">Activities </h6>
                </div>

                <div id="activity-log-div_Placeholder">
                    @include('admin.listing.includes.partials._activitylog_placeholder')
                </div>

                <div id="activity-log-div2" class="scrolltobottom">

                </div>

                <div id="no_activity" class="d-none">
                    @include('admin.listing.includes.partials.activity._empty_activities')
                </div>

                <div class="profile-timeline user-activity-timeline d-none" id="activities">

                </div>

                <div class="d-none p-lg-2" id="view-edit-mode">
                    <small class="mb-0 text-muted">Created: <span id="created_by_span"></span><br>Updated:
                        <span id="updated_by_span"> </span></small>
                </div>

            </div>


        </div>
        <div class="tab-pane" id="propertyCycleTab" role="tabpanel">
            <div id="accordionPlaceholder">

                @include('admin.listing.includes.partials._timeline_placeholder')

            </div>
            <div id="StatusTimeline">
            </div>

            <div id="no_timeline" class="d-none">
                @include('admin.listing.includes.timeline._empty_timeline')
            </div>
        </div>
    </div>

</div>
