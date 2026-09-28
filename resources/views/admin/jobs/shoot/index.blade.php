@extends('admin.layouts.master')
@section('title')
    Property Photo/Video Shoot | Jobs
@endsection
@section('style')
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css"/>
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css" rel="stylesheet"
          type="text/css"/>

    <!--datatable responsive css-->
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet"
          type="text/css"/>
    <style type="text/css">
        div.dataTables_wrapper div.dataTables_filter input {
            border-color: var(--vz-input-border);
            background-color: var(--vz-input-bg);
            color: var(--vz-body-color);
            line-height: 1.5;
            padding: 0.5rem 0.9rem 0.5rem 1.025rem;
            border-radius: 0.25rem;
            font-size: .8125rem;
        }

        .tasks-board {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            overflow-x: auto;
            -webkit-box-align: stretch;
            -ms-flex-align: stretch;
            align-items: stretch;
            height: 350px;
        }

        .tasks-board .tasks-list {
            min-width: 300px;
            margin-right: 24px;
        }

        .tasks-board::-webkit-scrollbar {
            -webkit-appearance: none
        }

        .tasks-board::-webkit-scrollbar:vertical {
            width: 10px
        }

        .tasks-board::-webkit-scrollbar:horizontal {
            height: 8px
        }

        .tasks-board::-webkit-scrollbar-thumb {
            background-color: rgba(var(--vz-dark-rgb), .075);
            border-radius: 8px
        }

        .tasks-board::-webkit-scrollbar-track {
            border-radius: 8px
        }

        .tasks-board .tasks-list {
            max-width: 200px !important;
            min-width: 175px !important;
        }

        .schedules .card-body {
            padding: 0.2rem 0.2rem !important;

        }

        .card-body .photographer {
            padding: 0.5rem !important;

        }

        .booked .card-body {
            padding: 0.2rem 0.2rem !important;
            background: #fff7f3;
            color: white !important;
        }

        #photographerEventCard.fullscreen {
            z-index: 999;
            width: 100%;
            height: 100%;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #f0f0f0;
            width: 82% !important;
            z-index: 1050;
            margin-left: 255px;
            border: solid 1px;
            right: 103px;
        }

        .active-photographer {
            background: #c5dbc5;
            border: 1px solid #25c425;
            border-radius: 6px;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"
          type="text/css"/>
@endsection
@section('content')

    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <div class="flex-grow-1">
                    <h4 class="card-title mb-0 ">Property Shoot</h4>
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Property Shoot</a></li>

                    </ol>
                </div>
                <div class="flex-shrink-0">
                    <div class="form-check form-switch form-switch-right form-switch-md">
                        @can('Property Shoot Create')
                            <button type="button" id="newShootJob" class="btn btn-danger action-add">
                                <i class="ri-add-line align-bottom me-1"></i> Add New
                            </button>
                        @endcan

                            @if(!empty($booking))
                                @if(strtoupper($booking->status) != 'COMPLETED' && strtoupper($booking->status) != 'WAITING FOR APPROVAL')
                        <button type="button" class="btn action-add edit-assignment">Edit Assignment</button>
                                @endif


                                @if(strtoupper($booking->status) == 'WAITING FOR APPROVAL')
                        <button type="button" class="btn btn-primary  action-edit" id="editBtn">Edit</button>
                                @endif
                            @endif
                        <button type="submit" class="btn btn-success d-none action-save" id="saveBtn">Save</button>
                            @if(!empty($booking) && $booking->status != 'Cancelled')
                                @can('Property Shoot Cancel')
                            <button type="button" data-action="Cancelled" id="cancel-button" class="btn btn-danger action-button action-cancel">
                                <i class="ri-close-line align-bottom me-1"></i>
                                Cancel
                            </button>
                                    @endcan
                            @endif
                        <button type="button" class="btn btn-light d-none action-close" id="closeBtn">Close</button>
                        {{-- @can('Jobs Delete')
                            <button type="button" class="btn btn-soft-danger action-delete" id="multiDeleteBtn"
                                    onClick="deleteMultiple()"><i
                                    class="ri-delete-bin-2-line"></i></button>
                        @endcan --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if(!empty($booking))
        <div class="col-12">
            <div class="collapse slideIn show" id="collapseContactForm">

                <div class="card">
                    <div class="card-body">
                        <div>
                            <div class="row">
                                <div class="col-8">
                                    <div class="row">
                                        <input type="hidden" name="id" id="id" value="{{$booking->id}}">

                                        <div class="col-3">Requested By</div>
                                        <div class="col-4">
                                            <a target="_blank"
                                               href="{{route('admin.user.users.view',['user' => $booking->requestedBy?->id])}}">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0 me-2">
                                                        <img src="{{$booking->requestedBy?->thumb_url}}" alt=""
                                                             class="avatar-xs rounded">
                                                    </div>
                                                    <div class="flex-grow-1"> {{$booking->requestedBy?->full_name}}
                                                        <br>
                                                        <small
                                                            class="text-muted">{{$booking->requestedBy?->roles[0]?->name}}</small>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-5">
                                            <div class="d-flex float-end">
                                                <div class="flex-shrink-0 me-2">
                                                    <i class="ri-time-line fs-24 text-muted"></i>
                                                </div>
                                                <div class="flex-grow-1 "> Preferred Time
                                                    <br>
                                                    <small
                                                        class="">{{date('d M Y h:i A',strtotime($booking->preferred_time))}}</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-3 mt-2">Property</div>
                                        <div class="col-9 mt-2" style="border: 1px solid #ecececd6;
    border-radius: 5px;
    padding: 9px;">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0">
                                                    @if($booking->property?->first_image)
                                                        <img src="{{$booking->property?->first_image?->thumbnail}}"
                                                             alt="" class="avatar-xs rounded">
                                                    @endif
                                                </div>
                                                <div class="flex-grow-1 ms-2">
                                                    <a target="_blank"
                                                       href="{{route('admin.listing.index')}}?ref_number={{$booking->property?->ref_no}}">{{$booking->property?->ref_no}}</a>
                                                    - {{$booking->property?->propertyCategory?->title}}
                                                    - {{$booking->property?->bed?->name}}
                                                    , {{$booking->property?->bath?->name}}

                                                    @if($booking->is_luxury == 1)
                                                        <span class="badge badge-gradient-warning text-light float-end">Luxury</span>
                                                        <span class="badge badge-gradient-info text-light float-end">Standard</span>
                                                    @else
                                                        <span class="badge badge-gradient-info text-light float-end">Standard</span>
                                                    @endif
                                                    <br>
                                                    Unit No. {{$booking->property?->unit_no}}, <span class="text-muted"> <i
                                                            class="ri-map-pin-line"></i> {{$booking->property?->communityProperty?->path_name ?? $booking->property?->subCommunity?->path_name ?? $booking->property?->community?->path_name}}</span>

                                                </div>
                                            </div>

                                            <hr>
                                            <span class="text-muted"><i class="bx bxs-bank" style="color: #d4a188;"></i>  DEWA : {{$booking->dewa == 1 ? 'Yes' : 'No'}} | <i
                                                    class="bx bx-tv" style="color: #d4a188;"></i> Furnished : {{$booking->furnished == 1 ? 'Yes' : 'No'}} | <i
                                                    class="bx bx-loader" style="color: #d4a188;"></i> Cleaned : {{$booking->cleaned == 1 ? 'Yes' : 'No'}} | <i
                                                    class="ri-user-2-fill" style="color: #d4a188;"></i> Agent Present : {{$booking->agent_present == 1 ? 'Yes' : 'No'}}</span>
                                        </div>
                                        <div class="col-3 mt-2 listings d-none">Listing*</div>
                                        <div class="col-9 mt-2 listings d-none">
                                            <select name='property_id' id="property_id"
                                                    class="form-control select2 inputs">
                                                <option value="" disabled selected></option>
                                            </select>
                                            <div class="invalid-feedback">
                                                Please choose listing.
                                            </div>
                                        </div>


                                        <div class="col-3 mt-2">Access Information</div>
                                        <div class="col-9 mt-2 text-muted" style="border: 1px solid #ecececd6;
    border-radius: 5px;
    padding: 9px;
    min-height: 72px;">
                                            {{$booking->access_information}}
                                        </div>
                                        <div class="col-3 mt-2">Additional details</div>
                                        <div class="col-9 mt-2 text-muted" style="border: 1px solid #ecececd6;
    border-radius: 5px;
    padding: 9px;
    min-height: 72px;">
                                            {{$booking->description}}
                                        </div>


                                        <div class="col-3 mt-2 d-none">Requested Tags</div>
                                        <div class="col-9 mt-2 d-none">
                                            @php $tags = json_decode($booking->requested_tags); @endphp
                                            @foreach($tags ?? [] as $tag)
                                                <span class="badge badge-soft-info">{{$tag}}</span>
                                            @endforeach

                                        </div>
                                        <input type="hidden" name="date" id="date" value="{{$booking->preferred_time}}">

                                    </div>
                                    <div class="col-12">
                                        @if($booking->status != 'Waiting for Approval')
                                            <div class="row">
                                                <div class="col-3 mt-2">Assigned to</div>
                                                <div class="col-9 mt-2">
                                                    @if($booking->assigned_to_id != null)
                                                        <a target="_blank"
                                                           href="{{route('admin.user.users.view',['user'=>$booking->assigned_to_id])}}">
                                                            <div class="d-flex align-items-center">
                                                                <div class="flex-shrink-0 me-2">
                                                                    <img src="{{$booking->assignedTo?->thumb_url}}"
                                                                         alt="" class="avatar-xs rounded">
                                                                </div>
                                                                <div
                                                                    class="flex-grow-1"> {{$booking->assignedTo?->full_name}}
                                                                    <br>
                                                                    <small
                                                                        class="text-muted">{{$booking->assignedTo?->roles[0]?->name}}</small>
                                                                </div>
                                                            </div>
                                                        </a>
                                                    @endif

                                                </div>

                                            </div>
                                        @endif
                                            @can('Property Shoot Approve')
                                                <div class="row  @if($booking->status != 'Waiting for Approval') d-none @endif mb-2" id="assignToPhotographer">
                                                    <div class="col-3 mt-3">Assign to</div>
                                                    <div class="col-6 mt-3">
                                                        <select class="form-control inputs" name="assigned_to_id"
                                                                id="shoot_assigned_to_id">
                                                            <option value="" disabled selected> Select Photographer
                                                            </option>
                                                            @foreach($photographers as $pg)
                                                                <option value="{{$pg->id}}"
                                                                        @if($booking->assigned_to_id == $pg->id) selected @endif>{{$pg->full_name}}</option>
                                                            @endforeach
                                                        </select>

                                                    </div>
                                                    <div class="col-3">
                                                        Preferred Time
                                                        <br>

                                                        <input type="text" class="form-control" id="viewPreferredTime"
                                                               name="preferred_time"
                                                               value="{{date('Y-m-d H:i',strtotime($booking->preferred_time))}}">
                                                    </div>

                                                </div>
                                            @endcan



                                            <div class="row  @if($booking->status != 'Waiting for Approval') d-none @endif" id="pgTimelineSection">
                                                <div class="text-center" id="photographerEventsLoader">
                                                    <div class="spinner-border text-success mt-5 mb-5"
                                                         role="status">
                                                        <span class="sr-only">Loading...</span>
                                                    </div>
                                                </div>
                                                <div class="col-12" style="background: #f0f0f0;"
                                                     id="photographerTimeline">


                                                </div>


                                            </div>

                                    </div>
                                </div>

                                <div class="col-4">
                                    <div class="row">
                                        <div class="col-4 mt-2  request_status_area">Current Status</div>
                                        <div class="col-8 mt-2 request_status_area">
                                            <div class="d-flex justify-content-between align-items-center">


                                                <div id="bookingStatusBadge">

                                                </div>


                                            </div>
                                        </div>
                                    </div>
                                    @if(!empty($booking))
                                        @if(strtoupper($booking->status) != 'COMPLETED' && strtoupper($booking->status) != 'REJECTED')
                                            @canany(['Property Shoot Approve','Property Shoot Reject','Property Shoot Status Change'])
                                                <div class="row">
                                                    <div class="col-4 mt-2">Update Status</div>
                                                    <div class="col-8 mt-2">


                                                            <div class=" @if($booking->status != 'Waiting for Approval') d-none @endif" id="approveRejectBtn">
                                                            @can('Property Shoot Approve')
                                                                <button type="button" data-action="Approved"
                                                                        class="btn btn-success action-button action-approve">
                                                                    <i class="ri-check-line align-bottom me-1"></i>
                                                                    Approve
                                                                </button>
                                                            @endcan
                                                            @can('Property Shoot Reject')
                                                                <button type="button" data-action="Rejected"
                                                                        id="reject-button"
                                                                        class="btn btn-danger action-button action-reject">
                                                                    <i class="ri-close-line align-bottom me-1"></i>
                                                                    Reject
                                                                </button>
                                                            @endcan
                                                            </div>

                                                        @if($booking->status != 'Waiting for Approval')
                                                            @can('Property Shoot Status Change')
                                                                @if(strtoupper($booking->status) != 'COMPLETED' && strtoupper($booking->status) != 'REJECTED' && $booking->status != 'Waiting for Approval')
                                                                    <div class="" id="completeBtnDiv">
                                                                    <button type="button" id="complete-button"
                                                                            data-action="Completed"
                                                                            class="btn btn-success action-button action-approve">
                                                                        <i class="ri-check-line align-bottom me-1"></i>
                                                                        Completed
                                                                    </button>
                                                                    </div>
                                                                @endif
                                                            @endcan
                                                        @endif

                                                    </div>
                                                </div>
                                            @endcanany
                                        @endif
                                    @endif


                                    <hr>
                                    @if($booking->pending_notification != null && $booking->pending_notification != '[]' && $booking->pending_notification != '{}')
                                        <div class="row ">
                                            <div class="card mt-3">
                                                <div class="card-header" style="background: #e3e3e3;
    color: #777777;">
                                                    Pending Notification
                                                   <button class="fs-14 las la-question-circle text-info helper-btn" data-bs-toggle="popover" data-bs-trigger="focus" title="Pending Notifications" data-bs-content=" This list displays all of the outstanding notifications generated as a result of various actions taken on this particular job. By selecting any of these buttons, a notification will be promptly dispatched for the corresponding pending event. Any past event will not be affected by this."></button>
                                                </div>
                                                <div class="card-body">
                                                    <!-- Tooltips -->

                                                    @php
                                                        $pendingNotifications = json_decode($booking->pending_notification);

                                                    @endphp
                                                    @if(gettype($pendingNotifications) == 'object' || gettype($pendingNotifications) == 'array')
                                                        @foreach($pendingNotifications ?? [] as $key => $value)

                                                            <button type="button" data-type="{{$key}}"
                                                                    data-id="{{$booking->id}}"
                                                                    class="btn rounded-pill btn-ghost-success waves-effect waves-light force-notification">{{$value}}</button>
                                                        @endforeach

                                                    @endif


                                                </div>
                                            </div>
                                        </div>
                                    @endif


                                    <div class="addHide">
                                        <div class="card">

                                            <div class="card-body mt-0 form-steps">
                                                <ul class="nav nav-tabs nav-tabs-custom nav-success nav-justified mb-1"
                                                    role="tablist">


                                                    <li class="nav-item active">
                                                        <a class="nav-link active" data-bs-toggle="tab" href="#notesTab"
                                                           role="tab" aria-selected="true"
                                                           onclick="scrollAtBottom('notes');">
                                                            <i class="mdi mdi-note-text align-middle"></i>
                                                            Notes</a>
                                                    </li>

                                                    <li class="nav-item addHide">
                                                        <a class="nav-link" data-bs-toggle="tab" href="#activityLogTab"
                                                           id="activityLogButton" role="tab" aria-selected="false">
                                                            <i class="mdi mdi-history align-middle"></i>
                                                            Activity Log</a>
                                                    </li>

                                                </ul>

                                            </div>


                                            <div class="tab-content text-muted">
                                                <div class="tab-pane active" id="notesTab" role="tabpanel">

                                                    <div data-simplebar class="px-3 mb-2 scrollable-notes" id="notes">
                                                    </div>
                                                    <div class="row align-items-center">
                                                        <div class="input-group">
                                                            <textarea class="form-control" id="note" rows="2"
                                                                      placeholder="Enter a note"></textarea>
                                                            <button
                                                                class="btn btn-primary btn-icon waves-effect waves-light"
                                                                type="button" title="Save Note" id="addNoteButton"
                                                                style="height:auto;">
                                                                <i class="mdi mdi-send-outline"></i>

                                                            </button>

                                                        </div>
                                                    </div>

                                                </div>

                                                <div class="tab-pane" id="activityLogTab" role="tabpanel">
                                                    <div class="col-12" id="activityLogDiv">
                                                        <div class="">
                                                            <div class="card-body">
                                                                <div
                                                                    class="profile-timeline acitivity-timeline scrollable-notes"
                                                                    id="scrollableActiviyLog"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>


                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @includeIf('admin.jobs.shoot.filterForm')
    <div class="row">
        <div class="col-xxl-12">
            <div class="card" id="contactList">
                <div class="card-body">

                    <table id="contacts-table" class="table align-middle" style="width:100%">
                        <thead>
                        <tr>
                            <th scope="col" style="width: 50px;">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="checkAll" value="option"
                                           data-com.bitwarden.browser.user-edited="yes">
                                </div>
                            </th>
                            <th>ID</th>
                            <th>Status</th>
                            <th>Request</th>
                            <th>Date</th>
                            <th>Day</th>
                            <th>Time</th>
                            <th>Agent</th>
                            <th>Location</th>
                            <th>Community</th>
                            <th>Ref Number</th>
                            <th>Type</th>
                            <th>Action By</th>
                            <th>Assigned To</th>
                            <th>Updated at</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div id="shootFormDiv"></div>
    </div>
    @include('admin.events.include.modal')
@endsection
@section('script')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.colVis.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        {{-- <script src=" "></script> --}}


    <script>
        let isShootUpdate = false;
        let dataObj = {};
        let isShootJobPage = true;
        let shootFormMode = 'create';
        let getPropertiesSelect2 = "{{route('ajax.properties.select2')}}";
        let agentSelect2Url = "{{route('ajax.getUsersSelect2')}}";
        let userSelect2url = "{{route('ajax.getUsersSelect2')}}";
        let getShootFormModalUrl = "{{route('admin.jobs.shoot.form')}}";
        let shootFormUrl = "{{route('admin.jobs.shoot.store')}}";
        let shootFormUpdateUrl = "{{route('admin.jobs.shoot.update')}}";
        let shootPage = "{{route('admin.jobs.shoot.index')}}";
        let propertyView = "{{route('admin.listing.index')}}";
        let calendarEventView = "{{route('admin.events.index')}}?event=";
        let actionUrl = "{{ route('admin.jobs.shoot.status.update') }}";
        let getAllEventsType = "{{route('admin.events.type.get.all')}}";
        let eventObj = {};
        let isEventPage = false;
        let getEventUrl = "{{ route('admin.events.get') }}";
    </script>
    <script src="{{ URL::asset('/assets/js/plugins/common.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/pages/job-shoot.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/plugins/event.js') }}?v={{time()}}"></script>
    <script>


        // $('#shoot_agent_id').on('select2:open', function() {
        //
        //     $(this).siblings('.select2-container').find('.select2-search__field').focus();
        // });

        $(document).on('click', '#btn_event_card_extend', function (event) {
            $('#photographerEventCard').toggleClass('fullscreen');


        });


        $(document).on("change", "#type", function () {
            let showListings = ["Photos/Video Shoot", "Photo Editing", "Video Editing"].indexOf($(this).val()) !== -
                1;
            if (showListings) {
                $(".listings").removeClass("d-none")
            } else {
                $(".listings").addClass("d-none")
            }
        })

        function viewEvent(id) {
            @can('Events View')
            showEventViewModal(id);
            @endcan
        }

        $(document).on("click", "#newShootJob", function (e) {
            shootFormMode = "create";
            isShootUpdate = false;
            @if(auth()->user()->hasRole('Agent'))
            getShootModal("{{auth()->user()->id}}");
            @else
            getShootModal();
            @endif
        });
        $(".edit-assignment").click(function () {
            $("#assignToPhotographer").removeClass("d-none");
            $("#pgTimelineSection").removeClass("d-none");
            getPhotographerTimeline();
            $("#approveRejectBtn").removeClass("d-none");
            $("#completeBtnDiv").addClass("d-none");
        });
        $(".force-notification").click(function () {
            let notification_type = $(this).data('type');
            let notification_id = $(this).data('id');
            $(this).attr('disabled', true);
            $.ajax({
                url: "{{route('admin.jobs.shoot.notification.force')}}",
                type: 'POST',
                dataType: 'json',
                async: false,
                data: {type: notification_type, id: notification_id},
                cache: false,

                success: function (result) {
                    if (result.status) {
                        showToastify("Notification has been sent successfully", "success");

                    } else {
                        showToastify("Notification not sent", "error");
                    }
                },
                error: function (jqXhr, json, errorThrown) {
                    let data = jqXhr.responseJSON;
                    if (data.errors) {
                        $.each(data.errors, function (index, item) {
                            showToastify(item[0], "error")
                        })
                    }
                    if (jqXhr.status == 500 || jqXhr.status == 400) {
                        showToastify(data.msg, "error")
                    }
                },
            });
        });

        function photoGrapherAction(action) {

            swal({
                title: "Are you sure?",
                text: "You want to mark this property shoot as " + action + " ?",
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            })
                .then((willDelete) => {
                    if (willDelete) { // on confirm delete the category


                        $.ajax({
                            url: "{{ route('admin.jobs.shoot.pg.status.update') }}",
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                id: $("#id").val(),
                                status: action
                            },
                            success: function (result) {

                                if (result.status == '1') { //if successfully deleted
                                    showToastify(result.msg, "success")
                                    dataTable.row()
                                        .draw(); // redraw the datatable show we don't need to reload the page to see the changes
                                    toggleForm(false)
                                } else { // something went wrong on backend
                                    showToastify(result.msg, "error")
                                }
                            },
                            error: function (result) {
                                $(this).prop("disabled", false);
                                console.log(result);
                            }
                        });
                    } else {

                    }
                });
        }

        $(document).ready(function () {


            @if(!empty($booking))
            $('#shoot_assigned_to_id').select2({
                placeholder: "Select Photographer",
                allowClear: true
            });
            $('#shoot_assigned_to_id').change(function () {
                $('.tasks-list').removeClass('active-photographer');
                $('#photographerList' + $(this).val()).addClass('active-photographer');
            });
            $("#closeBtn").removeClass('d-none');
            getActivityLog("{{$booking->id}}");
            $("#bookingStatusBadge").html(`<span class="badge ` + shootingRequestStatus("{{$booking->status}}") + `">{{$booking->status}}</span>`);
            @can('Property Shoot Note View')
            getJobNotes("{{$booking->id}}");
            @endcan
            @if($booking->status == 'Waiting for Approval')
            getPhotographerTimeline();
            @endif
            @endif
            flatpickr('#viewPreferredTime', {
                "minDate": new Date(),
                enableTime: true,
                dateFormat: "Y-m-d H:i"
            });

            @if(isset($_GET['action']) && $_GET['action'] == 'Complete')
            let action = 'Completed';
            photoGrapherAction(action);


            @endif
        });


        let filtersData = [];
        $(document).on("submit", "#searchForm", function (e) {
            e.preventDefault();
            filtersData = $("#searchForm").serialize();
            filtersData = urldecode(filtersData);
            filtersData = QueryStringToJSON(filtersData);
            dataTable.ajax.reload();
        })


        $(document).on("click", "#filter_luxury", function() {
            $(this).toggleClass("active");
            if ($(this).hasClass("active")) {
               console.log('true')
                $("#search_type").val('Luxury');
               $("#searchSearchBtn").trigger("click");
            } else {
                $("#search_type").val('');
                $("#searchSearchBtn").trigger("click");
            }
            dataTable.ajax.reload();
        });
        setTimeout(function() {
            $('<div class="flex-shrink-0 inline-blk" id="all_listing_div"><div class="form-check form-switch form-switch-right form-switch-md"><label for="file-input-showcode" class="form-label text-muted">Luxury</label><input class="form-check-input code-switcher" type="checkbox" id="filter_luxury"></div></div>')
                .insertBefore('.dt-buttons');

            @if (auth()->user()->hasRole(['Telesales', 'Telesales Head']))
            // $('#all_listing').click();
            $('#all_listing').attr('disabled', true);
            $('#all_listing_div').addClass('d-none');
            @endif
        }, 300);
        $(document).on("click", "#clearSearchBtn", function () {
            $(".search-inputs").val("").trigger("change");
            $("input[type='checkbox'].search-inputs").prop("checked", false).trigger("change");
            $("#searchSearchBtn").trigger("click")
        })

        $(document).on("click", "#refreshTableButton", function () {
            $("#searchSearchBtn").trigger("click")
        })
        $(document).on("click", ".schedules", function () {

            $("#viewPreferredTime").val($(this).data('time'));
            // $("#viewPreferredTime").trigger('change');
            // $("#viewPreferredTime").trigger('click');
        })


        function toggleAdvanceSearch() {
            $("#collapseFilterForm").toggleClass("show")
        }

        function activityChangeDataFormat(data) {
            let html = '';
            let val = '';
            if (data == null) {
                return '';
            }
            for (const [key, value] of Object.entries(data)) {

                if (key == 'created_at' || key == 'updated_at') {
                    html += key + ' - ' + moment(value).format("DD/MM/YY HH:mm") + '<br>';

                } else {
                    val = value == null ? '' : value;
                    html += key + ' - ' + val + '<br>';

                }
            }
            return html;
        }

        function getActivityLog(id) {
            $("#activityLogDiv").find(".acitivity-timeline").html("");
            $.ajax({
                url: "{{ route('admin.jobs.shoot.activity.log') }}",
                type: 'GET',
                dataType: 'json',
                data: {
                    subject_model: "PropertyShootBooking",
                    subject_id: id
                },
                success: function (result) {
                    let logs = "";
                    result?.data.map(item => {
                        let date = moment(item.created_at).fromNow()
                        let imageUrl =
                            `${item.causer ? item.causer.thumb_url : "{{ URL::asset('assets/images/users/avatar-8.jpg') }}"}`;
                        // logs += `<div class="acitivity-item py-3 d-flex">
                        //             <div class="flex-shrink-0">
                        //                 <img src="${imageUrl}" alt=""
                        //                      class="avatar-xs rounded-circle acitivity-avatar shadow" />
                        //             </div>
                        //             <div class="flex-grow-1 ms-3">
                        //                 <h6 class="mb-1">${item.causer.name}</h6>
                        //                 <p class="text-muted mb-2">${item.description}</p>
                        //                 <small class="mb-0 text-muted">${date}</small>
                        //             </div>
                        //         </div>`;
                        logs += `<div class="accordion-item border-0">
                                  <div class="accordion-header" id="heading6">
                                  <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapse${item.id}" aria-expanded="false">

                                      <div class="d-flex">
                                        <div class="flex-shrink-0">
                                          <img src="${imageUrl}" alt="" class="avatar-xs rounded-circle">
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                          <h6 class="fs-14 mb-1"> ${item.causer ? item.causer.name : "System"}  </h6>
                                          <small class="text-muted">${item.description} - <small class="mb-0 text-muted">${date}</small> </small>
                                        </div>
                                      </div>
                                    </a>
                                  </div>
                                  <div id="collapse${item.id}" class="accordion-collapse collapse hide" aria-labelledby="heading6" data-bs-parent="#accordion${item.id}">
                                    <div class="accordion-body ms-2 ps-5 pt-2">
                                    <div class="code-view" id="hiddenLog${item.id}">
                                 <span class="language-markup">
                                    <small class="text-muted"><strong>New data:</strong> <br>${activityChangeDataFormat(item.properties.attributes)}<br><strong>Old Data:</strong><br>${activityChangeDataFormat(item.properties.old)} </small></span></div> </div>
                                          </div>
                                        </div>`;
                    })
                    $("#activityLogDiv").find(".acitivity-timeline").html(logs);
                },
                error: function (result) {
                    console.log(result);
                }
            });
        }

        $("#viewPreferredTime").change(function () {
            let preferred_time = $(this).val();
            $("#photographerTimeline").html("");
            getPhotographerTimeline();
        });

        function getPhotographerTimeline() {
            $("#photographerEventsLoader").removeClass("d-none");
            $("#photographerTimeline").html("");
            let date = $("#viewPreferredTime").val();

            $.ajax({
                url: "{{route('admin.jobs.shoot.pg.timeline')}}",
                type: 'GET',
                dataType: 'json',
                async: false,
                data: {date: date},
                cache: false,

                success: function (result) {
                    if (result.status) {
                        $("#photographerEventsLoader").addClass("d-none");
                        $("#photographerTimeline").html(result.html);
                        let selectedPhotographer = $("#shoot_assigned_to_id").val();
                        if(selectedPhotographer != "" && selectedPhotographer != null && selectedPhotographer != undefined) {
                            $("#photographerList" + selectedPhotographer).addClass("active-photographer");
                        }

                    } else {
                        console.log(result.status);
                    }
                },
                error: function (jqXhr, json, errorThrown) {
                    let data = jqXhr.responseJSON;
                    if (data.errors) {
                        $.each(data.errors, function (index, item) {
                            showToastify(item[0], "error")
                        })
                    }
                    if (jqXhr.status == 500 || jqXhr.status == 400) {
                        showToastify(data.msg, "error")
                    }
                },
            });


        }

        function getJobNotes(id = null) {
            @can('Property Shoot Note View')
                id = (id != '' || id != null) ? id : $("#id").val();
            console.log(id);
            $("#notes").html("");
            $.ajax({
                url: "{{ route('admin.jobs.shoot.note.get') }}",
                type: 'GET',
                dataType: 'json',
                data: {
                    id
                },
                success: function (result) {
                    let notes = "";
                    result?.data.map(item => {
                        let date = moment(item.created_at).format("DD/MM/YY HH:mm")
                        let imageUrl =
                            `${item.created_by ? item.created_by.thumb_url : "{{ URL::asset('assets/images/users/avatar-8.jpg') }}"}`;
                        notes += `<div class="d-flex mb-4">
                                       <div class="flex-shrink-0">
                                           <img src="${imageUrl}" alt=""
                                                class="avatar-xs rounded-circle"/>
                                       </div>
                                       <div class="flex-grow-1 ms-3">
                                           <h5 class="fs-13"><a href="javascript:void(0)">${item.created_by ? item.created_by.name : "System"}</a> <small
                                                   class="text-muted">${date}</small></h5>
                                           <p class="text-muted">${item.note}</p>
                                       </div>
                                   </div>`
                    })
                    $("#notes").html(notes)
                    let element = document.getElementById("notes");
                    element.scrollTop = element.scrollHeight
                },
                error: function (result) {
                    console.log(result);
                }
            });
            @endcan
        }

        $(document).on("click", "#addNoteButton", function () {
            @can('Property Shoot Note Create')
            let id = $("#id").val();

            if (id == "") {
                showToastify("Please save record before adding a note.", "error")
                return;
            }

            let note = $("#note").val();
            if (note == "") {
                return;
            }

            var formData = new FormData();
            formData.append("id", id)
            formData.append("note", note)
            $.ajax({
                url: "{{ route('admin.jobs.shoot.note.store') }}",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function (result) {
                    if (result.status == '1') {
                        showToastify(result.msg, "success")
                        $("#note").val("").trigger("change");
                        id = $("#id").val();
                        getJobNotes(id)
                    }
                },
                error: function (jqXhr, json, errorThrown) {
                    let data = jqXhr.responseJSON;
                    if (data.errors) {
                        $.each(data.errors, function (index, item) {
                            showToastify(item[0], "error")
                        })
                    }
                    if (jqXhr.status == 500 || jqXhr.status == 400) {
                        showToastify(data.msg, "error")
                    }
                },
            });
            @endcan
        })

        function toggleForm(show) {
            if (show == undefined) {
                $("#collapseContactForm").toggleClass("show")
                return
            }
            if (show) {
                $("#collapseContactForm").addClass("show");
            } else {
                $("#saveBtn, #closeBtn, #editBtn, #deleteBtn, #cancel-button").addClass("d-none");
                $("#newBtn, #multiDeleteBtn").removeClass("d-none");
                $("#collapseContactForm").removeClass("show");
            }
        }

        $("#checkAll").change(function () {
            $(".selectable-checkbox").prop("checked", this.checked)
        })

        $("#newBtn").click(function () {
            @can('Jobs Create')
                dataObj = {}
            isShootUpdate = false;
            shootFormMode = "create";
            $(".inputs").prop("disabled", false);
            $("select.inputs").val("").trigger("change");
            $("#mainForm").trigger("reset")
            $("#mainForm").removeClass("was-validated")
            $("#id").val("")
            $("#saveBtn, #closeBtn").removeClass("d-none");
            $("#newBtn, #editBtn, #deleteBtn, #multiDeleteBtn").addClass("d-none");
            $(".approved_by_area, .job_status_area, .request_status_area").addClass("d-none")
            toggleForm(true)
            @endcan
        });

        $(document).on("click", "#editBtn", function () {
            @can('Property Shoot Edit')
            $(".inputs").prop("disabled", false)
            editData($('#id').val());
            // $("#saveBtn, #closeBtn").removeClass("d-none");
            // $("#newBtn, #editBtn, #deleteBtn, #multiDeleteBtn").addClass("d-none");
            @endcan
        });

        $(document).on('click', '.edit-link', function (event) {
            @can('Property Shoot Edit')
            $(".inputs").prop("disabled", false)
            let id = $(this).data('id');
            editData(id);
            @endcan
        });

        $(document).on("click", "#closeBtn", function () {
            $(".edit-assignment").addClass("d-none")
            toggleForm(false)
        })

        $(document).on('click', '#deleteBtn', function (event) {
            @can('Property Shoot Delete')
            deleteData($('#id').val());
            @endcan
        });

        function editData(id) {
            @can('Property Shoot Edit')
                isShootUpdate = true;
            toggleForm(true)
            editShootJob(id);
            // $("#saveBtn, #closeBtn").removeClass("d-none");
            // $("#newBtn, #editBtn, #deleteBtn, #multiDeleteBtn").addClass("d-none");
            @endcan
        }

        function editShootJob(id) {
            shootFormMode = "view";
            $.ajax({
                url: "{{ route('admin.jobs.shoot.get') }}",
                type: "get",
                data: {
                    id: id
                },
                success: function (result) {
                    if (result.status == '1') {
                        getShootModal();
                        dataObj = result.data
                        Object.keys(result.data).map((index) => {
                            $(`#${index}`).val(result.data[index]).trigger("change")
                        })
                        $("#shoot_job_id").val(result.data.id)


                        $("#shoot_dewa").prop("checked", result.data.dewa)
                        $("#shoot_furnished").prop("checked", result.data.furnished)
                        $("#shoot_furnished").prop("checked", result.data.furnished)
                        $("#shoot_cleaned").prop("checked", result.data.cleaned)
                        $("#shoot_luxury").prop("checked", result.data.is_luxury)
                        $("#shoot_agent_present").prop("checked", result.data.agent_present)

                        $("#shoot_access_information").val(result.data.access_information);
                        $("#shoot_description").val(result.data.description);
                        $("#shoot_input_title").val(result.data.title);
                        $("#shoot_preferred_time").val(result.data.preferred_time);
                        let requested_by = result.data.requested_by;

                        if (requested_by != null) {
                            let text = `${requested_by.full_name || ""}`;
                            if ($("#shoot_agent_id").find("option[value='" + requested_by.id + "']").length) {
                                $("#shoot_agent_id").val(requested_by.id).trigger('change');
                            } else {
                                $("#shoot_agent_id").append('<option value="' + requested_by.id + '" selected="true">' + text + '</option>');
                            }
                            $("#shoot_agent_id").val(requested_by.id).trigger("change");
                        }
                        let property = result.data.property;
                        if (property != null) {
                            let text = `${property.ref_no || ""} - ${property.title || ""}`;
                            if ($("#shoot_property_id").find("option[value='" + property.id + "']").length) {
                                $("#shoot_property_id").val(property.id).trigger('change');
                            } else {
                                $("#shoot_property_id").append('<option value="' + property.id + '" selected="true">' + text + '</option>');
                            }
                            $("#shoot_property_id").val(property.id).trigger("change");

                        }

                        let tags = JSON.parse(result.data.requested_tags);
                        $('#shoot_requested_tags').val(tags).trigger('change');

                        // drawRequestedTag();


                        $("#shoot_input_title").val(result.data.title);
                        // setTimeout(function () {
                        //
                        // }, 100)
                        shootFormMode = "edit";
                    }
                }
            });
        }


        $(document).on("click", ".action-button", function () {
            let action = $(this).data("action");

            if (action == 'Completed') {
                @can('Property Shoot Status Change')

                @else
                showToastify("You are not authorized to change status", "error");
                return false;
                @endif
            }
            if (action == "Approved") {
                @can('Property Shoot Approve')
                if ($("#shoot_assigned_to_id").val() == "" || $("#shoot_assigned_to_id").val() == null) {
                    showToastify("Please select a photographer", "error")
                    $("#shoot_assigned_to_id").focus().addClass("is-invalid");
                    return false;
                }
                actionUrl = "{{ route('admin.jobs.shoot.assign') }}";
                @endcan
            }
            if (action == "Rejected") {
                @can('Property Shoot Reject')

                @else
                    return false;
                @endcan
            }

            swal({
                title: "Are you sure?",
                text: "You want to mark this property shoot as " + action + " ?",
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            })
                .then((willDelete) => {
                    if (willDelete) { // on confirm delete the category
                        $(this).prop("disabled", true);
                        $.ajax({
                            url: actionUrl,
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                user_id: $("#shoot_assigned_to_id").val(),
                                id: $("#id").val(),
                                time: $("#viewPreferredTime").val(),
                                status: action
                            },
                            success: function (result) {
                                $(this).prop("disabled", false);
                                if (result.status == '1') { //if successfully deleted
                                    showToastify(result.msg, "success")
                                    location.reload();
                                    dataTable.row()
                                        .draw(); // redraw the datatable show we don't need to reload the page to see the changes
                                    toggleForm(false)
                                } else { // something went wrong on backend
                                    showToastify(result.msg, "error")
                                }
                            },
                            error: function (result) {
                                $(this).prop("disabled", false);
                                console.log(result);
                            }
                        });
                    }
                });

        })


        let dataTable = $('#contacts-table').DataTable({ //datatable initialization
            "order": [
                [0, "DESC"]
            ],
            language: {
                searchPlaceholder: "Search shoot jobs"
            },

            dom: 'Bfrtip',
            buttons: [{
                extend: 'colvis',
                titleAttr: 'Show/Hide',
                text: '<i class="colvis" style="font-size:09px">Show / Hide</i>',

            }],
            createdRow: function (row, data, index) {
                $('#contacts-table tbody').css('cursor', 'pointer');

            },
            responsive: true,
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "{{ route('admin.jobs.shoot.dt') }}",
                type: "post",
                data: function (d) {
                    d.requested_by = filtersData?.search_requested_by;
                    d.type = filtersData?.search_type;
                    d.priorities = filtersData?.search_priorities;
                    d.assigned_to = filtersData?.search_assigned_to;
                    d.approved_by = filtersData?.search_approved_by;
                    d.rejected_by = filtersData?.search_rejected_by;
                    d.statuses = filtersData?.search_statuses;
                    d.created_from = filtersData?.search_created_from;
                    d.created_to = filtersData?.search_created_to;
                    d.deleted = filtersData?.search_deleted;
                },
                error: function () {
                    $(".users-error").html("");
                    $("#dataTable").append(
                        '<tbody class="users-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>'
                    );
                    $("#dataTable_processing").css("display", "none");
                }
            },
            fnInitComplete: function (oSettings, json) {
                let first = $("#dataTable_wrapper .row div:first");
                first.addClass("col-md-5").removeClass("col-md-6")
                $(first).next().addClass("col-md-7").removeClass("col-md-6")
                $(".dataTables_filter label").addClass(
                    "d-flex justify-between-between align-items-center gap-2")
                $(`@includeIf('admin.jobs.include.filterButtons')`).insertAfter(".dataTables_filter input")
            },

            "aoColumns": [{
                data: null,
                orderable: false,
            }, {
                data: 'id',
                name: 'id',
                visible: false
            }, {
                data: null,
                orderable: false,
            },  {
                data: null,
                orderable: false,
            }, {
                data: null,
                orderable: false,
            }, {
                data: null,
                orderable: false,
            }, {
                data: null,
                orderable: false,
            }, {
                data: null,
                orderable: false,
            }, {
                data: 'updated_at',
                name: 'updated_at',
            }, {
                data: null,
                orderable: false,
            }, {
                data: null,
                orderable: false,
            },{
                data: null,
                orderable: false,
            },{
                data: null,
                orderable: false,
            },{
                data: null,
                orderable: false,
            },{
                data: null,
                orderable: false,
            },{
                data: null,
                orderable: false,
            }
            ],
            "columnDefs": [{
                "targets": 0,
                render: function (type, row, data, meta) {
                    return `<div class="form-check">
                                            <input class="form-check-input selectable-checkbox" type="checkbox" name="chk_child" value="` +
                        data.id + `">
                                </div>`;
                }
            },
                {
                    targets: 2,
                    visible: true,
                    render: function (type, row, data, meta) {

                        return `<span class="badge ` + shootingRequestStatus(data.status) + `">` + data.status + `</span>`;
                    }
                },


                {
                    "targets": 3,
                    render: function (type, row, data, meta) {
                        return 'Ph+V';
                    }
                }, {
                    "targets": 4,
                    render: function (type, row, data, meta) {
                        if (data.preferred_time == null) {
                            return '-';
                        } else {
                            return moment(data.preferred_time).format('DD/MM/YYYY');
                        }
                    }
                },
                {
                    "targets": 5,
                    render: function (type, row, data, meta) {
                        if (data.preferred_time == null) {
                            return '-';
                        } else {
                            //get day name like monday, tuesday etc
                            return moment(data.preferred_time).format('dddd');
                        }
                    }
                },{
                    "targets": 6,
                    render: function (type, row, data, meta) {
                        if (data.preferred_time == null) {
                            return '-';
                        } else {
                            return moment(data.preferred_time).format('hh:mm:ss A');
                        }
                    }
                },

                {
                    "targets": 7,
                    render: function (type, row, data, meta) {
                        if (!data.requested_by) return "-";
                        return `<div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-2">
                                        <img src="${data.requested_by.thumb_url}" alt="" class="avatar-xs rounded-circle">
                                    </div>
                                    <div class="flex-grow-1">${data.requested_by.name}</div>
                                </div>`;
                    }
                },
                {
                    "targets": 8,
                    render: function (type, row, data, meta) {
                        if (!data?.property) return "-";
                        return `<span class="fs-12">${data?.property?.location}</span>`;
                    }
                },
                {
                    "targets": 9,
                    render: function (type, row, data, meta) {
                        if (data?.property){
                            if(data?.property?.community !=null){
                                return `<span class="fs-12">${data?.property?.community?.name}</span>`;
                            }
                        }
                        return "-";

                    }
                },

                 {
                    "targets": 10,
                    render: function (type, row, data, meta) {
                        if (data.property == null) {
                            return '-';
                        }
                        return `<a target="_blank" href="${propertyView}?ref_number=${data?.property?.ref_no}">` + data?.property?.ref_no + `</a>`;
                    }
                },

                {
                    "targets": 11,
                    render: function (type, row, data, meta) {
                        if (data.is_luxury == 1) {
                            return '<span class="badge badge-gradient-warning text-light">Luxury</span><span class="badge badge-gradient-info text-light">Standard</span>';
                        } else {
                            return '<span class="badge badge-gradient-info text-light">Standard</span>';
                        }

                    }
                },{
                    "targets": 12,
                    render: function (type, row, data, meta) {
                        if (data.approved_by != null) {
                            return `<div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-2">
                                        <img src="${data.approved_by.thumb_url}" alt="" class="avatar-xs rounded-circle">
                                    </div>
                                    <div class="flex-grow-1">${data.approved_by.name}</div>
                                </div>`;
                        } else if (data.rejected_by != null) {
                            return `<div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-2">
                                        <img src="${data.rejected_by.thumb_url}" alt="" class="avatar-xs rounded-circle">
                                    </div>
                                    <div class="flex-grow-1">${data.rejected_by.name}</div>
                                </div>`;
                        } else {
                            return '-';
                        }
                    }
                },  {
                    "targets": 13,
                    render: function (type, row, data, meta) {
                        if (!data.assigned_to) return "-";
                        return `<div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-2">
                                        <img src="${data.assigned_to.thumb_url}" alt="" class="avatar-xs rounded-circle">
                                    </div>
                                    <div class="flex-grow-1">${data.assigned_to.name}</div>
                                </div>`;
                    }
                },
                {
                    targets: 14,
                    visible: true,
                    render: function (type, row, data, meta) {
                        if (data.updated_at == null) {
                            return '-';
                        } else {
                            return moment(data.updated_at).format('DD/MM/YYYY hh:mm:ss');
                        }
                    }
                },
                {
                    targets: 15,
                    orderable: false,
                    visible: true,
                    render: function (type, row, data, meta) {
                        let editLi = '';
                        if (data.status == 'Approved' || data.status == 'Rejected' || data.status == 'Completed') {
                            editLi = '';
                        } else {
                            editLi = `<li><a href="javascript:void(0)" data-id="` + data.id + `" class="dropdown-item edit-item-btn edit-link"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit</a></li>`;
                        }

                        return `<div class="dropdown d-inline-block">
                                    <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ri-more-fill align-middle"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                      ${editLi}
                                        <li>
                                            <a href="javascript:void(0)" onclick="deleteData(` + data.id + `)" data-id="` + data.id + `" class="dropdown-item remove-item-btn delete-link">
                                                <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete
                                            </a>
                                        </li>
                                    </ul>
                                </div>`
                    }
                }
            ]
        });

        function viewData(id) {
            @can('Property Shoot View')
            $(".inputs").prop("disabled", true)
            $("#id").val(id)
            window.location = shootPage + '?id=' + id;
            // $("#saveBtn, #newBtn, #multiDeleteBtn").addClass("d-none");
            // $("#editBtn, #closeBtn, #deleteBtn").removeClass("d-none");
            //shootViewModal(id);
            scrollTop()
            @endcan
        }

        function deleteData(id) {
            @can('Property Shoot Delete')
            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to access this property shoot request details!",
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            })
                .then((willDelete) => {
                    if (willDelete) { // on confirm delete the category
                        $.ajax({
                            url: "{{ route('admin.jobs.shoot.delete') }}",
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                id
                            },
                            success: function (result) {
                                if (result.status == '1') { //if successfully deleted
                                    showToastify(result.msg, "success")
                                    window.location = shootPage
                                    dataTable.row()
                                        .draw(); // redraw the datatable show we don't need to reload the page to see the changes
                                    toggleForm(false)
                                } else { // something went wrong on backend
                                    showToastify(result.msg, "error")
                                }
                            },
                            error: function (result) {
                                console.log(result);
                            }
                        });
                    }
                });
            @endcan
        }

        function deleteMultiple() {
            @can('Property Shoot Multi Delete')
            let ids = [];
            $(".selectable-checkbox").map((index, item) => {
                item.checked ? ids.push(item.value) : []
            })
            if (ids.length > 0) {
                swal({
                    title: "Attention!!",
                    text: "You are trying to delete multiple jobs. Once deleted, you will not be able to access these property shoot request details!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                    .then((willDelete) => {
                        if (willDelete) {
                            $.ajax({
                                url: "{{ route('admin.jobs.shoot.multi.delete') }}",
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    ids
                                },
                                success: function (result) {
                                    if (result.status == '1') {
                                        showToastify(result.msg, "success")
                                        dataTable.row().draw();
                                        toggleForm(false)
                                    } else {
                                        showToastify(result.msg, "error")
                                    }
                                },
                                error: function (result) {
                                    console.log(result);
                                }
                            });
                            document.getElementById("checkAll").checked = !1
                        }
                    });
            }
            @endcan
        }


        function updateMultiOption() {
            var countCheckedCheckboxes = $('input[name="chk_child"]:checked').length;
            console.log(countCheckedCheckboxes);
            $('#bulk_leads_count').html(countCheckedCheckboxes);
            $('#count_selected').html(countCheckedCheckboxes);
            if (countCheckedCheckboxes >= 1) {
                if ($('.multi_options').length === 0) {
                    $(`<div class="multi_options" style="display:inline-block; margin-left:10px;">
                          <div class="btn-group">
                            <button class="btn btn-primary btn-sm dropdown-toggle action-bulk" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Bulk Actions
                            </button>
                            <div class="dropdown-menu">

                    @can('Property Shoot Multi Delete')
                    <a class="dropdown-item" href="javascript:;" onclick="deleteMultiple()">Delete</a>
                    @endcan
                    </div>
                </div>
           </div>`).insertAfter('.dt-buttons');
                }
            } else {
                $('.multi_options').remove();

            }
        }

        $("#checkAll").change(function () {
            $(".selectable-checkbox").prop("checked", this.checked)
            updateMultiOption();
        })
        $(document).on('change', 'tbody tr .selectable-checkbox', function () {

            $(this).parents('tr').toggleClass('selected');
            var checked = $(this).is(':checked');
            if (!checked) {
                $(this).prop('checked', false);
            } else {
                $(this).prop('checked', true);
            }
            updateMultiOption();
        });

        function highlightRow(row) {
            row.removeClass('selected');
            dataTable.$('tr.selected').removeClass('selected');
            row.addClass('selected');
        } // highlightRow


        dataTable.on('click', 'tbody tr', function (e) {
            var $cell = $(e.target).closest('td'),
                msg;
            //console.log('Index ==========='+$cell.index());
            if ($cell.index() != 0) {
                var data = dataTable.row(this).data();
                //
                if ($cell.index() === 9) {
                    // console.log($cell);
                } else {
                    viewData(data.id);
                    highlightRow($(this));

                }
            } // end if
        }); // table row click

        $("#sidebarJobs").addClass('collapse show');
        $('#jobActive').addClass('active');
        $("#shootJobSidebar").addClass('active');
    </script>
@endsection
