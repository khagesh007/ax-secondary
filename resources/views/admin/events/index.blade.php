@extends('admin.layouts.master')
@section('title')
    Events
@endsection
@section('style')
    <link href="{{ URL::asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css"/>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="{{ URL::asset('/assets/libs/fullcalendar/fullcalendar.min.css') }}" rel="stylesheet">
    <style>
        .fc-daygrid-dot-event.fc-event-mirror, .fc-daygrid-dot-event:hover, .fc-event, .fc-event-dot {
            background-color: #d8d0ff !important;
        }
       .fc-timegrid-event-harness .fc-event {
    margin: 0 !important;
    padding: 0 !important;
}
    </style>
@endsection
@section('content')

    @if(!checkOAuthTokenExists(auth()->id()))
        <div class="row" >
            <div class="col-12">
                <div class="alert alert-danger alert-dismissible alert-label-icon rounded-label shadow fade show"
                     role="alert">
                    <i class="ri-airplay-line label-icon"></i><strong>Hi {{ auth()?->user()?->name }}</strong> - Your CRM Calendar is not synchronized with your Google Calendar. To initiate the synchronization, please click here.
                    <a href="{{ route('oauth.authenticate') }}" class="btn rounded-pill btn-sm btn-info waves-effect waves-light">Authorize Now</a>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

                </div>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-xl-12">
                    <div class="card card-h-100">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-1 mt-1">
                                    <label class="form-label" for="filter_agents">User</label>
                                </div>
                                <div class="col-5">

                                    <select class="form-select select2" name="filter_agents[]" id="filter_agents" multiple>
                                        <?php
                                            /*
                                        @foreach ($agents ?? [] as $agent)
                                            @if($agent->id == auth()->user()->id)
                                                <option value="{{ $agent->id }}" data-status="{{$agent->status}}" data-image="{{ $agent->thumbnail }}"  selected>{{ $agent->first_name }} {{ $agent->last_name }}</option>
                                            @else
                                                <option
                                                    value="{{ $agent->id }}" data-status="{{$agent->status}}" data-image="{{ $agent->thumbnail }}">{{ $agent->first_name }} {{ $agent->last_name }}</option>
                                            @endif

                                        @endforeach
                                            */
                                        ?>
                                    </select>
                                    <div class="row">

                                        <div class="col-md-3 ">
                                            <div class="form-check form-switch form-check-inline" dir="ltr">
                                                <small for="showMyTeamAgents">Team </small> <input type="checkbox" checked` value="myteam" class="form-check-input" id="showMyTeamAgents" name="showMyTeamAgents">

                                            </div>
                                        </div>
                                        <div class="col-md-9">

                                        </div>
                                    </div>
                                </div>

                                <div class="col-2 mt-1">
                                    <label class="form-label" for="filter_type">Type</label>
                                </div>
                                <div class="col-3">
                                    <select class="form-select" name="filter_type[]" id="filter_type" multiple>

                                        @foreach ($event_types ?? [] as $type)
                                            <option
                                                value="{{ $type->id }}">{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>
                </div><!-- end col -->
            </div>
            <div class="row">
                <div class="col-xl-3">
                    <div class="card card-h-100">
                        <div class="card-body">
                            <!-- open modal by clicking below button -->

                            <button class="btn btn-primary w-100" id="btn-new-event" data-bs-toggle="modal"
                                    data-bs-target="#event-modal"><i class="mdi mdi-plus"></i> Create New
                                Event
                            </button>

                            <div id="external-events">
                                <br>
                                <p class="text-muted">Drag and drop your event or click in the calendar</p>
                                @foreach ($event_types ?? [] as $type)
                                    <div class="external-event fc-event {{$type->badge}}"
                                         data-class="{{$type->badge}}" id="{{ $type->id }}" data-id="{{ $type->id }}">
                                        <i class="mdi mdi-checkbox-blank-circle font-size-11 me-2"></i>{{ $type->name }}
                                    </div>
                                @endforeach

                            </div>

                        </div>
                    </div>

                    <!--end card-->
                    <div>
                        <h5 class="mb-1">Upcoming Events</h5>
                        <p class="text-muted">Don't miss scheduled events</p>

                        <div class="card">
                            <div class="card-body">

                                <div>

                                    <div class="pe-2 me-n1 mb-3" data-simplebar="init" style="height: 30vh; max-height: 450px;">
                                        <div class="simplebar-wrapper" style="margin: 0px -8px 0px 0px;">
                                            <div class="simplebar-height-auto-observer-wrapper">
                                                <div class="simplebar-height-auto-observer"></div>
                                            </div>
                                            <div class="simplebar-mask">
                                                <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
                                                    <div class="simplebar-content-wrapper" tabindex="0" role="region" aria-label="scrollable content" style="height: 100%; overflow: hidden scroll;">
                                                        <div class="simplebar-content" style="padding: 0px 8px 0px 0px;">
                                                            <div id="upcoming-event-list1"><ul class="list-group list-group-flush border-dashed">
                                                                    @forelse($upcoming_events as $event)
                                                                        <li class="list-group-item ps-0 cursor-pointer" onclick="viewEvent('{{$event->id}}')" data-id="7">
                                                                            <div class="row align-items-center g-3">
                                                                                <div class="col-auto">
                                                                                    <div class="avatar-sm p-1 py-2 h-auto bg-light bg-light rounded-3" title="25 Apr 2023">
                                                                                        <div class="text-center">
                                                                                            <h5 class="mb-0">{{date('d',strtotime($event->start_time))}}</h5>
                                                                                            <div class="text-muted">{{date('D',strtotime($event->start_time))}}</div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col">
                                                                                    <h5 class="text-muted mt-0 mb-1 fs-10">{{date('h:i A',strtotime($event->start_time))}} - {{date('h:i A',strtotime($event->end_time))}}</h5>
                                                                                    <a href="javascript:;" class="text-reset fs-12 mb-0">{{$event->title}}</a>

                                                                                </div>

                                                                            </div>
                                                                        </li>
                                                                    @empty
                                                                        <div class="d-flex mb-3 text-center">
                                                                            <div class="flex-grow-1">
                                                                                <lord-icon src="{{asset('assets/json/icons/common-event.json')}}" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:125px;height:125px"> </lord-icon>
                                                                            </div>


                                                                        </div>
                                                                        <h5 class="mb-2 text-center"><span class="text-muted text-center">No Upcoming Events</span><small class="text-muted fs-13"></small></h5>
                                                                    @endforelse


                                                                </ul></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="simplebar-placeholder" style="width: auto; height: 2511px;"></div>
                                        </div>
                                        <div class="simplebar-track simplebar-horizontal" style="visibility: hidden;">
                                            <div class="simplebar-scrollbar" style="width: 0px; display: none;"></div>
                                        </div>
                                        <div class="simplebar-track simplebar-vertical" style="visibility: visible;">
                                            <div class="simplebar-scrollbar" style="height: 63px; display: block; transform: translate3d(0px, 24px, 0px);"></div>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- end card-body -->
                        </div>
                    </div>
                    <div>

                    </div>
                </div> <!-- end col-->

                <div class="col-xl-9">
                    <div class="card card-h-100">
                        <div class="card-body">
                            <div id="calendar"></div>
                        </div>
                    </div>
                </div><!-- end col -->
            </div>
            <!--end row-->

            <div style='clear:both'></div>

            @include('admin.events.include.modal')
        </div>
    </div>
    <div id="eventModalsDiv"></div>
@endsection
@section('script')

    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <script src="{{ URL::asset('assets/libs/fullcalendar/fullcalendar.min.js') }}"></script>
        {{-- <script src=" "></script> --}}



    <script>
        let currentPage = "Calendar";

        let eventObj = {};
        let propertyObj = {};
        let isEventPage = true;
        let Draggable = FullCalendar.Draggable;
        let externalEventContainerEl = document.getElementById('external-events');
        let propertySelect2 = "{{ route('ajax.properties.select2') }}";
        let contactSelect2 = "{{ route('ajax.contacts') }}";
        let currentUserRole = "{{ auth()->user()->roles->first()->name }}";
        let currentUserId = "{{ auth()->user()->id }}";
        let eventCreateUrl = "{{route('admin.events.store')}}";
        let eventUpdateUrl = "{{route('admin.events.update')}}";
        let agentSelect2Url = "{{route('ajax.getUsersSelect2')}}";
        let getPropertiesSelect2 = "{{route('ajax.properties.select2')}}";
        let getAllEventsType = "{{route('admin.events.type.get.all')}}";
        let getEventUrl = "{{route('admin.events.get')}}";
        let contactSelect2url = "{{route('ajax.contacts.select2')}}";
        let leadSelect2url = "{{route('ajax.leads.select2')}}";
        let eventPropertyDetailsUrl = "{{route('admin.listing.properties.get')}}";
        let getAttendeesUrl = "{{route('ajax.getUsersSelect2')}}";
        let getEventModalUrl = "{{route('admin.events.calendar.form.modal')}}";
    </script>
    <script src="{{ URL::asset('/assets/js/pages/event-calendar.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/plugins/common.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/plugins/event.js') }}?v={{time()}}"></script>
    <script>

        @if(request()->get('event'))
            let viewEventID = '{{request()->get('event')}}';
            $(function () {
                if(viewEventID != '')
                viewEvent(viewEventID);
            });
        @endif
        // write on load logic here
        $(document).ready(function () {


        });


        // $("#calendar_lead_id, #calendar_contact_id, #calendar_property_id").select2({
        //     allowClear: true,
        //     dropdownParent: $('#event-modal')
        // })
        $("#calendar_property_id2").getAgentSelect2();

        // init draggable
        new Draggable(externalEventContainerEl, {
            itemSelector: '.external-event',
            eventData: function (eventEl) {
                return {
                    title: eventEl.innerText,
                    start: new Date(),
                    className: eventEl.getAttribute('data-class')
                };
            }
        });
        $(document).on("change", "#filter_agents, #filter_type", function () {
            let agents = $('#filter_agents').val();
            let type = $('#filter_type').val();

            calendar.refetchEvents();
        })

        function getInitialView() {
            if (window.innerWidth >= 768 && window.innerWidth < 1200) {
                return 'timeGridWeek';
            } else if (window.innerWidth <= 768) {
                return 'listMonth';
            } else {
                return 'dayGridMonth';
            }
        }

        let calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
            //initialView: 'timeGridWeek',

            editable: true,
            droppable: true,
            selectable: true,
            navLinks: true,
            eventResizableFromStart: true,
            longPressDelay: 0,
            initialView: getInitialView(),
            themeSystem: 'bootstrap',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
            },
            events: {
                url: '{{ route('admin.events.calendar') }}',
                extraParams: function () {
                    return {
                        agents: $('#filter_agents').val(),
                        type: $('#filter_type').val(),
                        // myTeam : $('#filter_my_team').is(":checked")
                    };
                }
            },
            eventClick: function (info) {
                if (info.event.id == null || info.event.id == undefined || info.event.id == "") {
                    $(".agent_select_input").removeClass("d-none");

                    addNewFromTemplateEvent(info);
                    // clear temporary event dragged on calendar from calendar
                    calendar.getEvents().forEach(function (event) {
                        if (event.id == null || event.id == undefined || event.id == "") {
                            event.remove();
                        }
                    });
                } else {
                    eventFormMode = 'view';
                    viewEvent(info.event.id)
                }
            },
            dateClick: function (info) {
                //if date is in past then disable that time slot or day slot in calendar

                let date = new Date(); // Current date
                let yesterday = date.setDate(date.getDate() - 1);
                if (info.date < yesterday) {
                    return false;
                }
                $(".inputs").val("");
                $(".agent_select_input").removeClass("d-none");
                addNewEvent(info);
            },
           eventDrop: function (info){
                //when event is moved to another date

               updateDate(info.event.id,info.event.start);
           },
            // when new event dragged to calendar
            drop: function (info) {
                // console.log(info);
                //     $(".agent_select_input").removeClass("d-none");
                //     addNewFromTemplateEvent(info);

            },
            windowResize: function (view) {
                var newView = getInitialView();
                calendar.changeView(newView);
            },
        });
        calendar.render();
$("#btn-new-event").click(function (){
    addNewEvent({})
});
        function addNewEvent(info) {
            @can("Events Create")
            eventFormMode = 'create';
            isEventUpdate = false;
            eventObj = {}


            getEventTypes();
            let agent = [];
            let data = {
                date : info.date!=null ? moment(info.date).format("YYYY-MM-DD")+' 12:00:00' : moment().format("YYYY-MM-DD")+ ' 12:00:00',
                type : info?.event?.title,
                contact : [],
                @if(!auth()->user()->hasRole(['Admin','Super Admin']))
                user: {
                    id: "{{auth()->user()->id}}",
                    name: "{{auth()?->user()?->name}}",
                },
                @else
                    user : null,
                @endif
                property : {
                    id : 4069,
                }
            };
           // showAddEventModal(data);
            getEventModal(data);

            @endcan
        }

        function addNewFromTemplateEvent(info) {

            @can("Events Create")
                isEventUpdate = false;
            eventObj = {}
            let agent = [];
            let data = {
                date :  info.event.start!=null ? moment(info.event.start).format("YYYY-MM-DD")+' 12:00:00' : moment().format("YYYY-MM-DD")+' 12:00:00',
                type : info?.event?.title,
                contact : [],
                user: {
                    id: "{{auth()->user()->id}}",
                    name: "{{auth()->user()->name}}" || ""
                }
            };

          //  showAddEventModal(data);
            getEventModal(data);


            @endcan
        }

           function viewEvent(id){
               @can("Events View")
            showEventViewModal(id);
               @endcan
        }


        function editEvent() {
            @can("Events Edit")
            getEventModal({});
            showEventEditForm();
            @endcan
        }










        function updateDate(id, date){
            @can("Events Edit")
            let newdate = moment(date).format("YYYY-MM-DD");
            $.ajax({
                url: "{{route('admin.events.update.date')}}",
                type: 'POST',
                dataType: 'json',
                data: {
                    id:id,
                    date:newdate
                },
                success: function (result) {
                    if (result.status == '1') { //if successfully deleted
                        showToastify(result.msg)
                        calendar.refetchEvents()
                    } else { // something went wrong on backend
                        showToastify(result.msg, "error")
                    }
                },
                error: function (result) {
                    console.log(result);
                }
            });


            @endcan
        }

        $("#btn-new-event").click(function (){
           $("#btn-delete-event").addClass('d-none');
           $("#btn-save-event").removeClass('d-none');
        });


        $("#eventSidebar").addClass('menu-link active');

        // $("#sidebarEvents").addClass('collapse show');
        // $("#eventSidebar").addClass('active');

        $(document).on('change', '#showMyTeamAgents', function(e) {
            getAgents();
        });
        function getAgents() {
            if ($('#showInactiveAgents').is(':checked')) {
                all_agents = 'true';
            } else {
                all_agents = 'false';
            }

            if ($('#showMyTeamAgents').is(':checked')) {
                my_team = 'true';
            } else {
                my_team = 'false';
            }
            $.ajax({
                url: route('ajax.getAgentsBasedOnStatus'),
                type: 'POST',
                dataType: 'json',
                data: {
                    all_agents: all_agents,
                    my_team: my_team,
                },
                success: function (result) {
                    $("#filter_agents").html('');

                    // make option and append to select #search_agents
                    let options = "";
                    result?.data.map(item => {
                        // options +=  `<option value="${item.id}">${item.full_name}</option>`;

                        options +=
                            `<option data-image="${item.thumbnail ?? item.thumb_url}" data-status="${item.status}" value="${item.id}">${item.full_name}</option>`;
                    });
                    $("#filter_agents").html(options);

                    $('#filter_agents').select2({
                        width: '100%',
                        placeholder: 'Select Agent',
                        allowClear: true,
                        closeOnSelect: false,
                        templateResult: formatAgent,
                        templateSelection: formatAgentView
                    });
                },
                error: function (jqXhr, json, errorThrown) {
                    console.log()
                },
            });
        }
            getAgents();
    // if ($("#filter_agents").hasClass("select2")) {
    //     $('#filter_agents').select2({
    //         placeholder: 'Select agent',
    //         allowClear: true,
    //         focusOnOpen: false,
    //         closeOnSelect: false,
    //         minimumResultsForSearch: Infinity, // Disable search functionality
    //         templateResult: formatAgent,
    //     });
    // }
    function formatAgent(e) {
        let img = $(e.element).data('image');
        let status = $(e.element).data('status');
        let onlineClass = "online"
        if (!status) {
            onlineClass = "away";
        }
        return e.id ? $('<div class="d-flex align-items-center"><div class="flex-shrink-0 chat-user-img ' +
            onlineClass + ' align-self-center me-2 ms-0"><div class="avatar-xxs mb-2"> <img src="' + img +
            '" class="rounded-circle img-fluid userprofile" alt=""><span class="user-status"></span> </div></div><div class="flex-grow-1 overflow-hidden"><p class="text-truncate mb-0">' +
            e.text + '</p></div></div>') : e.text;
    }

    function formatAgentView(e) {
        let img = $(e.element).data('image');
        let status = $(e.element).data('status');
        let onlineClass = "online"
        if (!status) {
            onlineClass = "away";
        }
        return (e.text ?? "No Name")
    }
</script>
@endsection
