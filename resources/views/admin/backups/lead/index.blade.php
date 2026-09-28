@extends('admin.layouts.master')
@section('title')
    Leads
@endsection
@section('style')
    {{-- <link href="{{ URL::asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" /> --}}
    <!--datatable responsive css-->
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

        .scrollable-notes .card-animate:hover {
            box-shadow: 0px 0px 0px 0px rgba(30, 32, 37, .12) !important;
        }

        .non-clickable {
            pointer-events: none;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    <input type="hidden" id="is_admin" value="{{ auth()->user()->hasRole(['Super Admin', 'Admin'])? 'true': 'false' }}">
    <div class="row">
        <div class="col-lg-12" id="top-bar">
            <div class="card">

                <div>
                    <div class="card-header align-items-center d-flex">
                        <div class="flex-grow-1">
                            <h4 class="card-title mb-0 ">Leads</h4>
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Work</a></li>
                                <li class="breadcrumb-item active">Leads</li>
                                <li class="breadcrumb-item d-none" id="lead_id">
                                    <div class="d-flex justify-content-start align-items-center">
                                        <span id="ref_no"></span>
                                        <a href="javascript:void(0)" onclick="copyToClipboard('#ref_no')" class="text-print"
                                            title="Copy to clipboard"> <i class="ri-clipboard-line mx-lg-2"
                                                style="font-size: 14px"></i> </a>
                                    </div>
                                </li>
                            </ol>
                        </div>

                        <div class="flex-shrink-0">
                            <div class="form-check form-switch form-switch-right form-switch-md">


                                @can('Leads Create')
                                    <button type="button" id="newBtn" class="btn btn-danger add-btn action-add">
                                        <i class="ri-add-line align-bottom me-1"></i> Add New
                                    </button>
                                @endcan
                                <button type="submit" class="btn btn-success d-none action-save"
                                    id="saveBtn">Save</button>
                                @can('Leads Edit')
                                    <button type="button" id="editBtn" class="btn btn-info d-none action-edit">Edit</button>
                                @endcan

                                {{-- @can('Leads Additional Agents Bulk Assign')
                                    <button type="button" id="bulkAssignBtn" class="btn btn-dark d-none">Assign to
                                        agents
                                    </button>
                                @endcan --}}
                                <button type="button" class="btn btn-light d-none action-close"
                                    id="closeBtn">Close</button>

                                <button type="button" id="dropdownMenuLink1" data-bs-toggle="dropdown"
                                    aria-expanded="false" class="btn btn-soft-info shadow-none d-none action-more"><i
                                        class="ri-more-2-fill"></i></button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink1">



                                    @can('Leads Archive')
                                        <li>
                                            <a class="dropdown-item" href="javascript:void(0)" id="archiveSingleLead"
                                                title="Hard Delete">
                                                <i class="ri-inbox-archive-line align-bottom me-1"></i> Archive
                                            </a>
                                        </li>
                                    @endcan

                                    <li>
                                        <a class="dropdown-item" href="javascript:void(0)" id="hardDeleteBtn"
                                            title="Hard Delete">
                                            <i class="ri-delete-bin-7-line align-bottom me-1"></i> Delete
                                        </a>
                                    </li>

                                </ul>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <div class="col-12">
            <div class="collapse slideIn" id="collapseContactForm">
                <form class="row needs-validation vertical-navs-step" id="mainForm" novalidate>
                    <input type="hidden" id="id" name="id">
                    <input type="hidden" id="dailer_id" name="dailer_id" value="{{ auth()->user()->id }}">

                    <input type="hidden" id="lead_status_id" name="lead_status_id" value="">
                    <input type="hidden" id="lead_sub_status_id" name="lead_sub_status_id" value="">
                    @includeIf('admin.lead.include.form')



                    <button type="submit" class="d-none" id="submitBtn"></button>
                </form>
                <div class="row">
                    <div class="col-6 addHide">
                        <div class="card">

                            <div class="card-body mt-0 form-steps">
                                <ul class="nav nav-tabs nav-pills nav-custom nav-custom-light arrow-navtabs bg-light"
                                    role="tablist">

                                    <li class="nav-item active">


                                        <a class="nav-link active" data-bs-toggle="tab" href="#notesTab" role="tab"
                                            aria-selected="true" onclick="scrollAtBottom('notes');">
                                            <i class="mdi mdi-note-text align-middle"></i>
                                            Notes</a>
                                    </li>

                                    {{-- <li class="nav-item addHide">
                                        <a class="nav-link" data-bs-toggle="tab"
                                            href="#criteriaTab" role="tab" aria-selected="false">
                                            <i class="mdi mdi-selection-search align-middle"></i>
                                        Lead Criteria</a>
                                    </li> --}}

                                    {{-- <li class="nav-item addHide">
                                        <a class="nav-link" data-bs-toggle="tab"
                                            href="#additionalAgentTab" role="tab" aria-selected="false">
                                        <i class="mdi mdi-note-text align-middle"></i>
                                    Additional Agent</a>
                                    </li> --}}
                                    <li class="nav-item addHide">
                                        <a class="nav-link" data-bs-toggle="tab" href="#activityLogTab" role="tab"
                                            aria-selected="false">
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

                                    <div data-simplebar class="px-3 mb-2 scrollable-notes" id="notes">
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
                                {{-- <div class="tab-pane" id="criteriaTab" role="tabpanel">
                                        <div class="card-body">
                                            <div class="spinner-border text-success" id="criteriaTabLoader"
                                                role="status">
                                                <span class="sr-only">Loading...</span>
                                            </div>
                                            <div class="d-none" id="criteriaFormDiv">
                                                <form id="criteriaForm">
                                                    <div class="row">
                                                        <div class="col-2 mt-2">Beds</div>
                                                        <div class="col-5 mt-2">
                                                            @csrf
                                                            <input type="hidden" class="inputsCriteria" name="lead_id"
                                                                id="criteria_lead_id">
                                                            <input type="hidden" class="inputsCriteria" name="id"
                                                                id="criteria_id">
                                                            <select class="form-control inputsCriteria select2"
                                                                name="min_bed_id" id="min_bed_id">
                                                                <option value="" selected disabled>Select</option>
                                                                @foreach ($beds as $bed)
                                                                    <option value="{{ $bed->id }}">
                                                                        {{ $bed->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            <div class="invalid-feedback">
                                                                Please choose an option.
                                                            </div>
                                                        </div>
                                                        <div class="col-5 mt-2">
                                                            <select class="form-control inputsCriteria select2"
                                                                name="max_bed_id" id="max_bed_id">
                                                                <option value="" selected disabled>Select</option>
                                                                @foreach ($beds as $bed)
                                                                    <option value="{{ $bed->id }}">
                                                                        {{ $bed->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            <div class="invalid-feedback">
                                                                Please choose an option.
                                                            </div>
                                                        </div>
                                                        <div class="col-2 mt-2">Baths</div>
                                                        <div class="col-5 mt-2">
                                                            <select class="form-control inputsCriteria select2"
                                                                name="min_bath_id" id="min_bath_id">
                                                                <option value="" selected disabled>Select</option>
                                                                @foreach ($baths as $bath)
                                                                    <option value="{{ $bath->id }}">
                                                                        {{ $bath->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            <div class="invalid-feedback">
                                                                Please choose an option.
                                                            </div>
                                                        </div>
                                                        <div class="col-5 mt-2">
                                                            <select class="form-control inputsCriteria select2"
                                                                name="max_bath_id" id="max_bath_id">
                                                                <option value="" selected disabled>Select</option>
                                                                @foreach ($baths as $bath)
                                                                    <option value="{{ $bath->id }}">
                                                                        {{ $bath->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            <div class="invalid-feedback">
                                                                Please choose an option.
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row  mt-2 align-items-center">
                                                        <div class="col-3 mt-2">Community</div>
                                                        <div class="col-9 mt-2">
                                                            <select class="select2 inputsCriteria" multiple
                                                                id="communities" name="communities[]"></select>
                                                        </div>
                                                        <div class="col-3 mt-2">Sub Community</div>
                                                        <div class="col-9 mt-2">
                                                            <input type="hidden" name="locations" id="locations">
                                                            <select class="select2 inputsCriteria" multiple
                                                                id="sub_communities" name="sub_communities[]"></select>
                                                        </div>
                                                        <div class="col-3 mt-2">Furnished</div>
                                                        <div class="col-9 mt-2">
                                                            <select class="form-control select2 inputsCriteria"
                                                                id="furnished" name="furnished">
                                                                <option value="" selected disabled>Select</option>
                                                                <option value="FURNISHED">Furnished</option>
                                                                <option value="SEMI_FURNISHED">Semi Furnished</option>
                                                                <option value="UNFURNISHED">Unfurnished</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row  mt-2 align-items-center">
                                                        <div class="col-2 mt-2">Min price</div>
                                                        <div class="col-4 mt-2">

                                                            <select class="form-control select2 inputsCriteria"
                                                                id="min_price" name="min_price">
                                                                <option value="10000">10,000</option>
                                                                <option value="20000">20,000</option>
                                                                <option value="30000">30,000</option>
                                                                <option value="40000">40,000</option>
                                                                <option value="50000">50,000</option>
                                                                <option value="60000">60,000</option>
                                                                <option value="850000">85,000</option>
                                                                <option value="110000">110,000</option>
                                                                <option value="135000">135,000</option>
                                                                <option value="160000">160,000</option>
                                                                <option value="185000">185,000</option>
                                                                <option value="210000">210,000</option>
                                                                <option value="235000">235,000</option>
                                                                <option value="260000">260,000</option>
                                                                <option value="285000">285,000</option>
                                                                <option value="310000">310,000</option>
                                                                <option value="335000">335,000</option>
                                                                <option value="360000">360,000</option>
                                                                <option value="410000">410,000</option>
                                                                <option value="460000">460,000</option>
                                                                <option value="510000">510,000</option>
                                                                <option value="560000">560,000</option>
                                                                <option value="610000">610,000</option>
                                                                <option value="660000">660,000</option>
                                                                <option value="710000">710,000</option>
                                                                <option value="760000">760,000</option>
                                                                <option value="810000">810,000</option>
                                                                <option value="860000">860,000</option>
                                                                <option value="910000">910,000</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-2 mt-2">Max Price</div>
                                                        <div class="col-4 mt-2">

                                                            <select class="form-control select2 inputsCriteria"
                                                                id="max_price" name="max_price">

                                                                <option value="50000">50,000</option>
                                                                <option value="60000">60,000</option>
                                                                <option value="850000">85,000</option>
                                                                <option value="110000">110,000</option>
                                                                <option value="135000">135,000</option>
                                                                <option value="160000">160,000</option>
                                                                <option value="185000">185,000</option>
                                                                <option value="210000">210,000</option>
                                                                <option value="235000">235,000</option>
                                                                <option value="260000">260,000</option>
                                                                <option value="285000">285,000</option>
                                                                <option value="310000">310,000</option>
                                                                <option value="335000">335,000</option>
                                                                <option value="360000">360,000</option>
                                                                <option value="410000">410,000</option>
                                                                <option value="460000">460,000</option>
                                                                <option value="510000">510,000</option>
                                                                <option value="560000">560,000</option>
                                                                <option value="610000">610,000</option>
                                                                <option value="660000">660,000</option>
                                                                <option value="710000">710,000</option>
                                                                <option value="760000">760,000</option>
                                                                <option value="810000">810,000</option>
                                                                <option value="860000">860,000</option>
                                                                <option value="910000">910,000</option>
                                                                <option value="1010000">1,010,000</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-2">

                                                        <div class="col-12 d-flex">
                                                            <span class="flex-grow-1"></span>
                                                            <span class="flex-shrink-0">
                                                                <button type="submit" id="leadCirteriaSaveBtn"
                                                                    class="btn btn-primary">Save</button>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div> --}}
                                <div class="tab-pane" id="additionalAgentTab" role="tabpanel">
                                    <div class="col-12 d-none" id="addOnAgentDiv">
                                        <div class="">
                                            <div class="card-header align-items-center d-flex">
                                                <h4 class="card-title mb-0 flex-grow-1"></h4>
                                                @can('Leads Additional Agents Add')
                                                    <div class="flex-shrink-0">
                                                        <div class="dropdown card-header-dropdown" style="    width: 225px;">
                                                            <select name='agent_id_add' id="add_on_agent"
                                                                placeholder="Select Agent" class="form-control">
                                                                <option selected disabled>Select Agent</option>
                                                                @foreach ($agents ?? [] as $agent)
                                                                    <option value="{{ $agent->id }}">
                                                                        {{ $agent->first_name }}
                                                                        {{ $agent->last_name }}</option>
                                                                @endforeach
                                                            </select>

                                                        </div>
                                                    </div>
                                                @endcan
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
                                <div class="tab-pane" id="activityLogTab" role="tabpanel">
                                    <div id="activityLogDiv">
                                        <div class="">
                                            <div class=" mt-0">

                                                <div
                                                    class="acitivity-timeline profile-timeline scrollable-notes scrollable-notes-reduce-height">
                                                </div>
                                                <div class="d-none p-lg-2" id="view-edit-mode">
                                                    <small class="mb-0 text-muted">
                                                        <span id="created_at" class="d-none">Created:</span> <span
                                                            id="created_by_span"></span>
                                                        <br><span id="updated_at" class="d-none">Updated:</span> <span
                                                            id="updated_by_span"> </span></small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="col-6">
                        <div id="eventWidgetArea">

                        </div>
                    </div>
                    <div class="col-12"></div>
                </div>

            </div>
        </div>
    </div>

    @includeIf('admin.lead.include.filterForm')
    <div class="row">
        <div class="col-xxl-12">
            <div class="table-sticky-header-container" id="table-sticky-header">
                <ul class="nav nav-tabs nav-tabs-custom nav-success mb-1 no-border" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link All  statusFilterClear active" data-bs-toggle="tab" id="All"
                            href="#" role="tab" aria-selected="false" tabindex="-1">
                            All
                        </a>
                    </li>

                    @foreach ($statuses ?? [] as $status)
                        <li class="nav-item" role="presentation">
                            <a class="nav-link All  statusFilter text-uppercase" data-bs-toggle="tab"
                                id="status_id_{{ $status->id }}" href="javascript:void(0)"
                                data-status="{{ $status->id }}" role="tab" aria-selected="false" tabindex="-1">
                                <i class="{{ $status->icon }} me-1"></i>{{ $status->name }}
                            </a>
                        </li>
                    @endforeach

                </ul>
            </div>
            <div class="card" id="contactList">
                <div class="card-body">


                    <table id="contacts-table" class="table align-middle" style="width:100%">
                        <thead class="">
                            <tr class="text-uppercase table-light2">
                                <th scope="col" style="width: 50px;">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="checkAll" value="option">
                                    </div>
                                </th>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Mobile</th>
                                <th>Source</th>
                                <th>Follow Up</th>
                                <th>Type</th>
                                <th>Agent</th>
                                <th>Purpose</th>
                                {{-- <th>Status</th> --}}
                                <th>Status</th>
                                <th>Updated</th>
                                {{-- <th>Action</th> --}}
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('admin.lead.include.modal')
    @include('admin.events.include.modal')
    <div id="eventModalsDiv"></div>
@endsection
@section('script')
    @include('admin.partials.datatable')

    {{-- <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script> --}}
        {{-- <script src=" "></script> --}}

    <script src="{{ URL::asset('/assets/js/plugins/widgets.js') }}"></script>
    <script>
        let all_listing_val = false;
        let isUpdate = false;
        let dataObj = {};
        let defaultStatusId = 1;
        let defaultSubStatusId = 4;
        let agent_sub_communities = "{{ route('ajax.user-sub-communities') }}";
        let lead_sub_status_url = "{{ route('ajax.lead-sub-statuses') }}";
        let remove_additional_agent = "{{ route('admin.work.leads.remove.agent') }}";
        let fetch_additional_agents = "{{ route('admin.work.leads.get.agents') }}";
        let add_more_agent_url = "{{ route('admin.work.leads.add.more.agent') }}";

        let makeCallLink = "{{ route('admin.work.leads.start-call') }}";
        let bulkAssignLink = "{{ route('admin.work.leads.assign-agents') }}";

        let mode = 'view';

        //event/calendar related urls
        let getEventUrl = "{{ route('admin.events.get') }}";
        let currentUserRole = "{{ auth()->user()->roles->first()->name }}";
        let currentUserId = "{{ auth()->user()->id }}";
        let propertySelect2 = "{{ route('ajax.properties.select2') }}";
        let eventCreateUrl = "{{ route('admin.events.store') }}";
        let eventUpdateUrl = "{{ route('admin.events.update') }}";
        let getAllEventsType = "{{ route('admin.events.type.get.all') }}";
        let eventObj = {};
        let isEventPage = false;
        let contactSelect2url = "{{ route('ajax.contacts.select2') }}";
        let leadSelect2url = "{{ route('ajax.leads.select2') }}";
        let eventPropertyDetailsUrl = "{{ route('admin.listing.properties.get') }}";
        let getAttendeesUrl = "{{ route('ajax.getUsersSelect2') }}";
        let getEventModalUrl = "{{ route('admin.events.calendar.form.modal') }}";

        let canRemoveAdditionalAgents = false;
        @can('Leads Additional Agents Remove')
            canRemoveAdditionalAgents = true;
        @endcan


        let call_now_button = 0;

        @can('Dial my lead')
            call_now_button = 1;
        @endcan
        @can('Dial other lead')
            call_now_button = 2;
        @endcan
        @canany(['Dial my contact', 'Dial other contact'])
            call_now_button = 3;
        @endcanany
    </script>
    <script src="{{ URL::asset('/assets/js/plugins/event.js') }}?v={{time()}}"></script>
    <script>
        let mobileInput = initPhoneValidator()
        let phoneInput = initPhoneValidator("phone", "full_phone")
        let evenStatusDropDown = "";
        @json(\App\Models\Meeting::STATUS_SELECT)?.map(item => {
            evenStatusDropDown +=
                `<a class="dropdown-item change-meeting-status" href="javascript:void(0)" data-status="${item}">${item}</a>`;
        });
        let meetingStatusChangePermission = "{{ auth()->user()->can('Contact Meeting Status Change') }}";
        let callIcon = "{{ asset('assets/json/icons/call-icon.json') }}";
        let noTaskIcon = "{{ asset('assets/json/icons/wired-outline-245-edit-document.json') }}";
        let noFollowupIcon = "{{ asset('assets/json/icons/wired-outline-981-consultation.json') }}";
        let meetingLogIcon = "{{ asset('assets/json/icons/wired-outline-981-consultation.json') }}";
        let leadsViewMyRecording = false;
        @can('Leads View My Recordings')
            leadsViewMyRecording = true;
        @endcan
        flatpickr('#meeting_start_time', {
            "minDate": new Date(),
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            onChange: function() {
                //set min date for end date
                flatpickr('#meeting_end_time', {
                    "minDate": new Date($("#meeting_start_time").val()),
                    enableTime: true,
                    dateFormat: "Y-m-d H:i",
                });
            }

        });
        flatpickr('#task_due_date', {
            "minDate": new Date(),
            enableTime: true,
            dateFormat: "Y-m-d H:i",
        });
        $('#agent_id').select2({
            placeholder: 'Select agent',
            allowClear: true,
            closeOnSelect: true,

        });
        $('#source_id').select2({
            placeholder: 'Select source',
            allowClear: true,
            closeOnSelect: true,

        });
        $('#type_id').select2({
            placeholder: 'Select type',
            allowClear: true,
            closeOnSelect: true,

        });
        $('#preFillOption').select2({
            placeholder: 'Select any reason',
            allowClear: true,
            closeOnSelect: true,
        });
        $('#preFillOption2').select2({
            placeholder: 'Select any reason',
            allowClear: true,
            closeOnSelect: true,
        });

        $('#bulk_leads_agent_id').select2({
            placeholder: 'Select agents',
            allowClear: true,
            dropdownParent: $('#bulkReassignLeadsModal')
        });
        $('#gender').select2({
            placeholder: 'Select gender',
            allowClear: true,
            closeOnSelect: true,
        });
        $('#property_id').select2({
            placeholder: 'Select listing',
            allowClear: true,
        });

        $('#telesales_head_id').select2({
            placeholder: 'Select Sales Team Leader',
            allowClear: true,
            closeOnSelect: true,
        });

        $('#telesales_id').select2({
            placeholder: 'Select Telesales',
            allowClear: true,
            closeOnSelect: true,
        });

        $("#for").select2({
            placeholder: "Select lead for",
            allowClear: true,
            closeOnSelect: false
        })

        $("#add_on_agent").select2({
            placeholder: "Select agent",
            allowClear: true,
            closeOnSelect: false
        })

        $("#sub_community_id").select2({
            placeholder: "Select sub community",
            allowClear: true,
            closeOnSelect: false,
        })
        $("#nationality_id").select2({
            placeholder: "Select nationality",
            allowClear: true,
            closeOnSelect: true,
        })

        // $("#lead_status_id").select2({
        //     placeholder: "Select status",
        //     allowClear: true,
        // })

        // $("#lead_sub_status_id").select2({
        //     placeholder: "Select sub status",
        //     allowClear: true,
        // })

        $("#lead_medium_id").select2({
            placeholder: "Select medium",
            allowClear: true,
            closeOnSelect: true,
        })
        $('#meeting_attendees').select2({
            placeholder: 'Select attendees',
            allowClear: true,
            closeOnSelect: false,
            dropdownParent: $('#meetingModal')
        });

        $('#task_priority').select2({
            placeholder: 'Select priority',
            allowClear: true,
            closeOnSelect: false,
            dropdownParent: $('#taskModal')
        });

        // $('#min_price').select2({
        //     placeholder: 'Min',
        //     allowClear: true,
        //     closeOnSelect: false,
        //     tags: true
        // });
        // $('#max_price').select2({
        //     placeholder: 'Max',
        //     allowClear: true,
        //     closeOnSelect: false,
        //     tags: true
        // });

        $('#filter_min_price').select2({
            placeholder: 'Min',
            allowClear: true,
            closeOnSelect: false,
            tags: true
        });
        $('#filter_max_price').select2({
            placeholder: 'Max',
            allowClear: true,
            closeOnSelect: false,
            tags: true
        });

        $('#assign_agents').select2({
            placeholder: 'Select agents',
            allowClear: true,
            closeOnSelect: false,
            dropdownParent: $('#assignAgentsModal')
        });

        $('#selectAction').select2({
            placeholder: 'Select Action',
            allowClear: false,
            closeOnSelect: false,
            dropdownParent: $('#generalModal')
        });

        $('#agents_dropdown').select2({
            placeholder: 'Select Action',
            allowClear: false,
            closeOnSelect: false,
            dropdownParent: $('#generalModal')
        });
        $('#bulk_leads_telesales_leader').select2({
            placeholder: 'Select Telesales Head',
            allowClear: false,
            closeOnSelect: false,
            dropdownParent: $('#generalModal')
        });


        $('#search_agents').select2({
            placeholder: 'Select Agent',
            allowClear: false,
            closeOnSelect: false,
        });

        $('#filter_telesales_head').select2({
            placeholder: 'Select Sales Team Leader',
            allowClear: false,
            closeOnSelect: false,
        });
        $('#search_lead_medium').select2({
            placeholder: 'Select Medium',
            allowClear: false,
            closeOnSelect: false,
        });


        $('#filter_telesales').select2({
            placeholder: 'Select Telesales',
            allowClear: false,
            closeOnSelect: false,
        });
        $('#search_for').select2({
            placeholder: 'Purpose',
            allowClear: false,
            closeOnSelect: false,
        });
        $('#search_lead_statuses').select2({
            placeholder: 'Select Lead Status',
            allowClear: false,
            closeOnSelect: false,
        });
        $('#search_lead_sub_statuses').select2({
            placeholder: 'Select Lead Sub Status',
            allowClear: false,
            closeOnSelect: false,
        });
        $('#search_priorities').select2({
            placeholder: 'Select Priority',
            allowClear: false,
            closeOnSelect: false,
        });
        $('#search_nationalities').select2({
            placeholder: 'Select Nationality',
            allowClear: false,
            closeOnSelect: false,
        });

        $('#search_lead_types').select2({
            placeholder: 'Select Lead Type',
            allowClear: false,
            closeOnSelect: false,
        });

        $('#search_genders').select2({
            placeholder: 'Select Gender',
            allowClear: false,
            closeOnSelect: false,
        });
        $('#search_lead_sources').select2({
            placeholder: 'Select Lead Source',
            allowClear: false,
            closeOnSelect: false,
        });
        $('#filter_min_bed_id').select2({
            placeholder: 'Min Bed',
            allowClear: false,
            closeOnSelect: false,
        });
        $('#filter_max_bed_id').select2({
            placeholder: 'Max Bed',
            allowClear: false,
            closeOnSelect: false,
        });


        $('#filter_min_bath_id').select2({
            placeholder: 'Min Bath',
            allowClear: false,
            closeOnSelect: false,
        });
        $('#filter_max_bath_id').select2({
            placeholder: 'Max Bath',
            allowClear: false,
            closeOnSelect: false,
        });

        $('#filter_furnished').select2({
            placeholder: 'Select furnishing type',
            allowClear: false,
            closeOnSelect: false,
        });

        $('#bulk_leads_telesales').select2({
            placeholder: 'Select Action',
            allowClear: false,
            closeOnSelect: true,
            dropdownParent: $('#generalModal')
        });

        $('#bulk_leads_telesales_leader').select2({
            placeholder: 'Select Action',
            allowClear: false,
            closeOnSelect: true,
            dropdownParent: $('#generalModal')
        });

        $('#min_bed_id').select2({
            placeholder: 'Min Bed',
            allowClear: false,
            closeOnSelect: false,
        });
        $('#max_bed_id').select2({
            placeholder: 'Max Bed',
            allowClear: false,
            closeOnSelect: false,
        });


        $('#min_bath_id').select2({
            placeholder: 'Min Bath',
            allowClear: false,
            closeOnSelect: false,
        });
        $('#max_bath_id').select2({
            placeholder: 'Max Bath',
            allowClear: false,
            closeOnSelect: false,
        });

        $('#furnished').select2({
            placeholder: 'Select Furnish',
            allowClear: false,
            closeOnSelect: false,
        });


        let filtersData = [];
        $(document).on("submit", "#searchForm", function(e) {
            e.preventDefault();
            filtersData = $("#searchForm").serialize();
            filtersData = urldecode(filtersData);
            filtersData = QueryStringToJSON(filtersData);
            dataTable.ajax.reload();
        })

        $(document).on("click", "#clearSearchBtn", function() {
            $(".search-inputs").val("").trigger("change");
            $("input[type='checkbox'].search-inputs").prop("checked", false).trigger("change");
            $("#searchSearchBtn").trigger("click")

        })

        $(document).on("click", "#refreshTableButton", function() {
            $("#searchSearchBtn").trigger("click")
        })

        setTimeout(function() {
            $('@if (auth()->user()->hasRole(['Admin', 'Super Admin', 'Telesales', 'Telesales Head']))<div class="flex-shrink-0 inline-blk" id="all_listing_div"><div class="form-check form-switch form-switch-right form-switch-md"><label for="file-input-showcode" class="form-label text-muted">Lead Pool</label><input class="form-check-input code-switcher" type="checkbox" id="all_listing"></div></div>@endif')
                .insertBefore('.dt-buttons');

            @if (auth()->user()->hasRole(['Telesales', 'Telesales Head']))
                // $('#all_listing').click();
                $('#all_listing').attr('disabled', true);
                $('#all_listing_div').addClass('d-none');
            @endif
        }, 300);

        $(document).on("click", "#all_listing", function() {
            $(this).toggleClass("active");
            if ($(this).hasClass("active")) {
                all_listing_val = true
            } else {
                all_listing_val = false
            }
            dataTable.ajax.reload();
        });



        $(document).on("click", "#unlistedButton", function() {

            let status = 'UNASSIGNED'; //$(this).data("status");

            let selected = [];
            $("#search_agents option:selected").map((ind, el) => {
                selected.push($(el).val())
            });
            const index = selected.indexOf(id.toString());
            if (index == -1) {
                selected.push(id);
            } else {
                selected.splice(index, 1);
            }
            $(this).toggleClass("filters-selected");
            $("#search_portals").val(selected).trigger("change")
            $("#searchSearchBtn").trigger("click")
        })

        function toggleAdvanceSearch() {
            $("#collapseFilterForm").toggleClass("show")
        }

        $(document).on("click", "#saveBtn", function() {
            $("#submitBtn").trigger("click")
        })


        // $(document).ready(function() {
        //     $('.dataTables_filter input').keyup(function() {

        //     })
        // })

        $(document).on("click", "#start-call-buttons", function() {
            // $('#startACall').modal('show');
            var content = document.createElement('div');
            content.innerHTML = "Are you sure you want to start a call with <strong>" +
                $('#first_name').val() +
                " " + $('#last_name').val() + "</strong>";
            swal({
                    title: "Start a call?",

                    // icon: 'info',
                    buttons: true,
                    content: content,
                    animation: false,
                    showClass: {
                        popup: 'animate fadeInDown'
                    },
                    confirmButtonText: 'Yes',
                    cancelButtonText: "No",
                    dangerMode: false,
                })
                .then((assignOwner) => {
                    if (assignOwner) {
                        swal({
                            title: "Call Started.",
                            text: "Please Check Your Phone",
                            // icon: "success",
                        });

                        let id = $("#id").val();
                        $('#start-call-button').addClass('d-none');
                        let mobile = $("#mobile").val();
                        if (id == "" || mobile == "") {
                            return
                        }
                        // alert('Call started')
                        makeCall()
                    } else {
                        swal("Cancelled", "Call not started.");
                    }

                })
        });


        $(document).on("click", "#start-call-buttonss", function() {
            // $('#startACall').modal('show');
            var content = document.createElement('div');
            content.innerHTML = "Are you sure you want to start a call with <strong>" + $('#first_name').val() +
                " " + $('#last_name').val() + "</strong>";
            swal({
                    title: "Start a call?",

                    // icon: 'info',
                    buttons: true,
                    content: content,
                    animation: false,
                    showClass: {
                        popup: 'animate fadeInDown'
                    },
                    confirmButtonText: 'Yes',
                    cancelButtonText: "No",
                    dangerMode: false,
                })
                .then((assignOwner) => {
                    if (assignOwner) {
                        const swalContent = document.createElement('div');
                        const timerElement = document.createElement('span');
                        const timerDuration = 5; // Timer duration in seconds
                        let remainingTime = timerDuration;
                        let timerInterval;

                        swalContent.appendChild(document.createTextNode('Please Check Your Phone'));
                        swalContent.appendChild(document.createElement('br'));
                        swalContent.appendChild(timerElement);

                        const swalModal = swal({
                            title: "Call Started",
                            content: swalContent,
                            buttons: {
                                cancel: {
                                    text: "Cancel",
                                    value: false,
                                    visible: true,
                                    closeModal: true,
                                },

                            },
                        }).then((value) => {
                            if (value) {
                                let id = $("#id").val();
                                $('#start-call-button').addClass('d-none');
                                let mobile = $("#mobile").val();
                                if (id == "" || mobile == "") {
                                    return;
                                }
                                makeCall();
                            } else {
                                clearInterval(timerInterval);
                                console.log("Action cancelled");
                            }
                        });

                        timerInterval = setInterval(() => {
                            remainingTime--;
                            timerElement.textContent = `Time remaining: ${remainingTime}s`;

                            if (remainingTime <= 0) {
                                clearInterval(timerInterval);
                                makeCall();
                            }
                        }, 1000);

                        // swal({
                        //     title: "Call Started.",
                        //     text: "Please Check Your Phone",

                        //     // icon: "success",
                        // });

                        // let id = $("#id").val();
                        // $('#start-call-button').addClass('d-none');
                        // let mobile = $("#mobile").val();
                        // if (id == "" || mobile == "") {
                        //     return
                        // }
                        // makeCall()
                    } else {
                        swal("Cancelled", "Call not started.");
                    }


                })
        });

        $(document).on("click", "#start-call-button", function() {
            var timeInSecs;
            var ticker;
            $text = "Dialing... <br><br> <strong>" + $('#first_name').val() +
                " " + $('#last_name').val() + "</strong>"
            $('#call_id').html($text)
            $('#countdown').html('00:05');
            $('#checkPhone').html('')
            $('#CancelBTN').removeClass('d-none');

            startTimer(5); // 4 minutes in seconds
            $('#startACall').modal('show');
        });

        function tick() {
            var secs = timeInSecs;
            if (secs > 0) {
                timeInSecs--;
            } else {
                clearInterval(ticker);
                $('#countdown').html('');
                $('#CancelBTN').addClass('d-none');
                makeCall();
                $('#checkPhone').html('Please Check Your Phone')
            }
            var mins = Math.floor(secs / 60);
            secs %= 60;
            var pretty = ((mins < 10) ? "0" : "") + mins + ":" + ((secs < 10) ? "0" : "") + secs;
            document.getElementById("countdown").innerHTML = pretty;
        }

        $(document).on("click", ".CancelBTN", function() {
            clearInterval(ticker);
            $('#countdown').html('');
            $('#checkPhone').html('Call Cancelled')
            $('#CancelBTN').addClass('d-none');


        });

        $(document).ready(function() {
            var table = $('#contacts-table').DataTable();
            var searchBox = $('.dataTables_filter input');
            searchBox.attr('id', 'mySearchBox');
        });


        function startTimer(secs) {
            timeInSecs = parseInt(secs);
            ticker = setInterval("tick()", 1000);
        }

        let isNewTab = false;
        setTimeout(function() {

            var open_ref_id = "{{ Request('id') }}";
            if (open_ref_id) {
                isNewTab = true;

                $('#search_id').val(open_ref_id).trigger('click');
                $('#mySearchBox').val(open_ref_id);
                $('#searchSearchBtn').trigger('click');
                // $('.dataTables_filter .form-control').val(open_ref_id).trigger('keyup');
            }
        }, 500);


        setTimeout(function() {
            if (isNewTab) {
                //
                $('#contactList tbody tr:eq(0)').click();
                $('#mySearchBox').val("{{ Request('id') }}");

            }
        }, 1000);

        $(document).ready(function() {
            $('#mySearchBox').on('input', function() {
                let val = $(this).val();
                if (val == "") {
                    $('.dataTables_filter .form-control').val();
                    $('#search_id').val($('.dataTables_filter .form-control').val());
                    $('#searchSearchBtn').trigger('click');
                }
            });
        });

        // $('#searchSearchBtn').trigger('click');

        // $(document).on("change", "#mySearchBox", function() {
        //     $('.dataTables_filter .form-control').val();
        //     $('#search_id').val($('.dataTables_filter .form-control').val());
        //     $('#searchSearchBtn').trigger('click');
        // })

        //   $(document).on("change", "#communities", function() {
        //     alert('test');
        // })






        // $(document).on("click", "#start-call-button", function() {
        //     let id = $("#id").val();
        //     $('#start-call-button').addClass('d-none');
        //     let mobile = $("#mobile").val();
        //     if (id == "" || mobile == "") {
        //         return
        //     }
        //     makeCall()
        // })
        $(document).on("click", "#checkDuplicates", function() {
            $.ajax({
                url: "{{ route('admin.work.leads.check.duplicate') }}",
                type: 'GET',
                dataType: 'json',
                data: {},
                success: function(result) {
                    console.table(result);
                },
                error: function(result) {
                    console.log(result);
                }
            });
        })

        $(document).on("click", "#whatapp-button", function() {
            let id = $("#id").val();
            let mobile = $("#whatsapp").val();
            if (id == "" || mobile == "") {
                return
            }
            window.open(`https://web.whatsapp.com/send?phone=${mobile}&text=&source=&data=`, '_blank');
        })

        $(document).on("click", "#bulkAssignBtn", function() {
            @can('Leads Additional Agents Bulk Assign')
                $("#assignAgentsForm").removeClass("was-validated")
                $("#assignAgentsForm").trigger("reset");
                $("#assign_agents>option").removeAttr('disabled');
                $(`#assign_agents > option[value='${dataObj.agent_id}']`).attr('disabled', 'disabled');
                let agents = [];
                dataObj?.lead_agents?.map(item => {
                    agents.push(item.user_id);
                })
                $("#assign_agents").val(agents).trigger("change")
                $("#assignAgentsModal").modal("show");
            @endcan
        })

        function toggleCirteriaForm(flag) {
            if (flag) {
                //$("#criteriaTabLoader").addClass('d-none');
                //$("#criteriaFormDiv").removeClass("d-none");
            } else {
                //$("#criteriaTabLoader").removeClass('d-none');
                //$("#criteriaFormDiv").addClass("d-none");
            }
        }

        function activityChangeDataFormat(data) {
            let html = '';
            let val = '';
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
        $(document).on('click', ".show-hiddenLog", function() {
            let eleid = $(this).data('id');
            // console.log(eleid);
            if ($("#hiddenLog" + eleid).hasClass('d-none')) {
                $("#hiddenLog" + eleid).removeClass('d-none');
            } else {
                $("#hiddenLog" + eleid).addClass('d-none');
            }

        });

        function getResponseTime(id) {
            $("#leadResponseTime").html("");
            $.ajax({
                url: "{{ route('admin.work.leads.response.time') }}",
                type: 'GET',
                dataType: 'json',
                data: {
                    id: id
                },
                success: function(result) {

                    if (result?.status == 1) {

                        if (result?.data == null) {
                            $('#responseDiv').addClass('d-none');
                            return;
                        } else {
                            $('#responseDiv').removeClass('d-none');
                            $('#leadResponseTime').html(result?.data);
                        }


                    }
                },
                error: function(result) {
                    console.log(result);
                }
            })
        }

        function getActivityLog(id) {
            $("#activityLogDiv").find(".acitivity-timeline").html("");
            $.ajax({
                url: "{{ route('admin.work.leads.activity.log') }}",
                type: 'GET',
                dataType: 'json',
                data: {
                    subject_model: "Lead",
                    subject_id: id
                },
                success: function(result) {
                    let logs = "";
                    result?.data.map(item => {

                        let date = moment(item.created_at).fromNow()
                        let imageUrl =
                            `${item.causer ? item.causer.thumb_url : "{{ URL::asset('assets/images/users/multi-user.jpg') }}"}`;
                        logs += `<div class="accordion-item border-0">
                                  <div class="accordion-header" id="heading6">
                                  <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapse${item.id}" aria-expanded="false">

                                      <div class="d-flex">
                                        <div class="flex-shrink-0">
                                          <img src="${imageUrl}" alt="" class="avatar-xs rounded-circle">
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                          <h6 class="fs-14 mb-1"> ${item.causer ? item.causer.name :"System"}  </h6>
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
                error: function(result) {
                    console.log(result);
                }
            });
        }

        function resetStatus() {
            $("#lead_status_id").val(defaultStatusId).trigger("change")
        }

        function getLeadNotes() {
            @can('Lead Notes List')
                let lead_id = $("#id").val();
                $("#notes").html("");
                $.ajax({
                    url: "{{ route('ajax.lead-notes') }}",
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        lead_id
                    },
                    success: function(result) {
                        let notes = "";
                        result?.data.map(item => {
                            let date = moment(item.created_at).format("DD/MM/YY HH:mm")
                            let imageUrl =
                                `${item.created_by ? item.created_by.thumb_url : "{{ URL::asset('assets/images/logo/favicon-32x32.png') }}"}`;
                            notes += `<div class="d-flex mb-0">
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
                        $("#notes").html(notes);
                        let element = document.getElementById("notes");
                        element.scrollTop = element.scrollHeight
                    },
                    error: function(result) {
                        console.log(result);
                    }
                });
            @endcan
        }


        $(document).on("click", "#bulkLeadsSubmitBtn", function() {

            let selected_agent_bulk = $('#bulk_leads_agent_id').val();
            let email_notification = $('#lead_email_notification').prop('checked');
            let system_notification = $('#lead_system_notification').prop('checked');
            let telegram_notification = $('#lead_telegram_notification').prop('checked');
            if (selected_agent_bulk == "") {
                showToastify("Please select agent.", "error")
                return;
            } else {
                if (ids_array = [], document.getElementsByName("chk_child").forEach((function(e) {
                        if (1 == e.checked) {
                            var t = e.value;
                            ids_array.push(t)

                        }
                    })), "undefined" != typeof ids_array && ids_array.length > 0) {
                    $.ajax({
                        url: "{{ route('admin.work.leads.bulk.assign.leads') }}",
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            ids: ids_array,
                            agent_id: selected_agent_bulk,
                            email_notification: email_notification,
                            system_notification: system_notification,
                            telegram_notification: telegram_notification,
                        },
                        success: function(result) {
                            if (result.status == '1') {
                                showToastify(result.msg, "success")
                                $("#bulkReassignLeadsForm").trigger("reset")
                                $("#bulkReassignLeadsModal").modal("hide")
                                $("#checkAll").prop("checked", this.checked)
                                document.getElementById("checkAll").checked = !1
                                dataTable.row().draw();
                                $('.multi_options').remove();
                            } else {
                                showToastify(result.msg, "error")
                            }
                        },
                        error: function(result) {
                            console.log(result);
                        }
                    });
                }

                // Ajax Form goes here
            }


        })






        $(document).on("click", "#bulkTelesalesSubmitBtn", function() {

            let bulk_leads_telesales = $('#bulk_leads_telesales').val();

            let email_notification = $('#telesales_email_notification').prop('checked');
            let system_notification = $('#telesales_system_notification').prop('checked');
            let telegram_notification = $('#telesales_telegram_notification').prop('checked');

            if (bulk_leads_telesales == "") {
                showToastify("Please select Telesales.", "error")
                return;
            } else {
                if (ids_array = [], document.getElementsByName("chk_child").forEach((function(e) {
                        if (1 == e.checked) {
                            var t = e.value;
                            ids_array.push(t)

                        }
                    })), "undefined" != typeof ids_array && ids_array.length > 0) {
                    $.ajax({
                        url: "{{ route('admin.work.leads.bulk.assign.telesales') }}",
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            ids: ids_array,
                            telesales_id: bulk_leads_telesales,
                            email_notification: email_notification,
                            system_notification: system_notification,
                            telegram_notification: telegram_notification,
                        },
                        success: function(result) {
                            if (result.status == '1') {
                                showToastify(result.msg, "success")
                                $("#bulkReassignTelesalesForm").trigger("reset")
                                $("#telesalesModal").modal("hide")
                                $("#checkAll").prop("checked", this.checked)
                                document.getElementById("checkAll").checked = !1
                                dataTable.row().draw();
                                $('.multi_options').remove();
                            } else {
                                showToastify(result.msg, "error")
                            }
                        },
                        error: function(result) {
                            console.log(result);
                        }
                    });
                }

                // Ajax Form goes here
            }


        })

        $(document).on("click", "#archiveBtn", function() {
            let id = $(this).attr('data-id');
            $('#archive_lead_id').val(id);
            $('#archiveModal').modal('show');
        })


        $(document).ready(function() {
            $('#preFillOption').change(function() {
                // get selected value of option
                var option = $(this).find('option:selected').val();
                if (option == 'Other') {
                    $('#archive_note').val('')
                    $('#archive_note').removeClass('d-none');
                    $('#archive_note').focus();
                } else {
                    $('#archive_note').val(option)
                    $('#archive_note').addClass('d-none');
                }
            });
        })


        $(document).ready(function() {
            $('#preFillOption2').change(function() {
                // get selected value of option
                var option = $(this).find('option:selected').val();
                if (option == 'Other') {
                    $('#lead_reason').val('')
                    $('#lead_reason').removeClass('d-none');
                    $('#lead_reason').focus();
                } else {
                    $('#lead_reason').val(option)
                    $('#lead_reason').addClass('d-none');
                }
            });
        })

        $(document).on('click', '#hardDeleteBtn', function(event) {
            deleteData($('#id').val(), true);



        });

        function renderCreatedUpdated(data) {
            $("#view-edit-mode").removeClass("d-none");
            if (data.created_at) {
                $('#created_at').removeClass('d-none');
                let creator = 'System';
                if (data.created_by) {
                    creator = data.created_by?.first_name;
                }
                $('#created_by_span').text(creator + ' ' + moment(data.created_at).format(
                    'MM/DD/YYYY hh:mm:ss'))
            } else {
                $('#created_at').addClass('d-none');
            }
            if (data.updated_by) {
                $('#updated_at').removeClass('d-none');
                let updater = 'System';
                if (data.updated_at) {
                    updater = data.updated_by?.first_name;
                }
                $('#updated_by_span').text(updater + ' ' + moment(data.updated_at).format(
                    'MM/DD/YYYY hh:mm:ss'))
            } else {
                $('#updated_at').addClass('d-none');
            }
            //$("#view-edit-mode>.card-body").html(div);
        }

        $(document).on("change", "#selectAction", function() {

            var selectedOption = this.options[this.selectedIndex].value;

            if (selectedOption == 'Re-Assign') {
                $('#agent_dropdown').removeClass('d-none');
                $('#telesales_dropdown').addClass('d-none');
                $('#telesales_head_dropdown').addClass('d-none');

            } else if (selectedOption == 'Telesales') {
                $('#agent_dropdown').addClass('d-none');
                $('#telesales_dropdown').removeClass('d-none');
                $('#telesales_head_dropdown').addClass('d-none');
            } else if (selectedOption == 'Telesales Head') {
                $('#agent_dropdown').addClass('d-none');
                $('#telesales_dropdown').addClass('d-none');
                $('#telesales_head_dropdown').removeClass('d-none');
            }


        })


        $(document).on("click", "#saveStatus", function() {
            $('#screen_loader').removeClass('d-none');
            let lead_reason = $('#lead_reason').val();
            let id = $('#unsuccessfull_lead_id').val();
            if (lead_reason == "") {
                showToastify("Please Select Reason For Unsuccessful Lead.", "error")
                return;
            } else {
                $.ajax({
                    url: "{{ route('admin.work.leads.saveReason') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        id: id,
                        lead_reason: lead_reason,
                    },
                    success: function(result) {
                        $('#screen_loader').addClass('d-none');
                        if (result.status == '1') {

                            mode = 'view';
                            showToastify(result.msg, "success")
                            $("#archive_note").val("")
                            $('#unsuccessfullReasonModal').modal('hide');
                            $("#archiveModal").modal("hide")
                            $("#checkAll").prop("checked", this.checked)
                            document.getElementById("checkAll").checked = !1
                            dataTable.row().draw();
                            $('.multi_options').remove();

                            toggleCirteriaForm(false);
                            $("#addOnAgentDiv, #newBtn, #closeBtn, #followUpMenusTab, .addHide,#dropdownMenuLink1,#saveBtn")
                                .addClass("d-none");
                            $("#collapseContactForm").removeClass("show");
                            $("#editBtn, #bulkAssignBtn").addClass("d-none");
                            $("#newBtn").removeClass("d-none");

                            $(".inputs").prop("disabled", false);
                            $(".inputs").prop("readonly", true);
                            $("#mainForm").trigger("reset")
                            $(".inputs").val("").trigger("change")
                            $(".inputs").removeClass("is-invalid")

                        } else {
                            showToastify(result.msg, "error")
                        }

                    },
                    error: function(jqXhr, json, errorThrown) {
                        // $(buttons).prop("disabled", false);
                        $('#screen_loader').addClass('d-none');

                        let data = jqXhr.responseJSON;
                        if (data.errors) {
                            $.each(data.errors, function(index, item) {
                                showToastify(item[0], "error")
                            })
                        }
                        if (jqXhr.status == 500 || jqXhr.status == 400) {
                            showToastify(data.message, "error")
                        }
                        // $('#screen_loader').addClass('d-none');

                    },
                });
            }

            // Ajax Form goes here
        })


        $(document).on("click", "#saveArchive", function() {
            $('#screen_loader').removeClass('d-none');
            let archive_note = $('#archive_note').val();
            if (archive_note == "") {
                showToastify("Please Add Archive note.", "error")
                return;
            } else {
                if (ids_array = [], document.getElementsByName("chk_child").forEach((function(e) {
                        if (1 == e.checked) {
                            var t = e.value;
                            ids_array.push(t)
                        }
                    })), "undefined" != typeof ids_array && ids_array.length > 0) {
                    $.ajax({
                        url: "{{ route('admin.work.leads.archive') }}",
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            ids: ids_array,
                            archive_note: archive_note,
                        },
                        success: function(result) {
                            $('#screen_loader').addClass('d-none');
                            if (result.status == '1') {
                                showToastify(result.msg, "success")
                                $("#archive_note").val("")
                                $("#archiveModal").modal("hide")
                                $("#checkAll").prop("checked", this.checked)
                                document.getElementById("checkAll").checked = !1
                                dataTable.row().draw();
                                $('.multi_options').remove();

                                toggleCirteriaForm(false);
                                $("#addOnAgentDiv, #newBtn, #closeBtn, #followUpMenusTab, .addHide,#dropdownMenuLink1,#saveBtn")
                                    .addClass("d-none");
                                $("#collapseContactForm").removeClass("show");
                                $("#editBtn, #bulkAssignBtn").addClass("d-none");
                                $("#newBtn").removeClass("d-none");

                                $(".inputs").prop("disabled", false);
                                $(".inputs").prop("readonly", true);
                                $("#mainForm").trigger("reset")
                                $(".inputs").val("").trigger("change")
                                $(".inputs").removeClass("is-invalid")

                            } else {
                                showToastify(result.msg, "error")
                            }

                        },
                        error: function(jqXhr, json, errorThrown) {
                            $('#screen_loader').addClass('d-none');

                            let data = jqXhr.responseJSON;
                            if (data.errors) {
                                $.each(data.errors, function(index, item) {
                                    showToastify(item[0], "error")
                                })
                            }
                            if (jqXhr.status == 500 || jqXhr.status == 400) {
                                showToastify(data.message, "error")
                            }
                            // $('#screen_loader').addClass('d-none');

                        },
                    });
                }

                // Ajax Form goes here
            }


        })



        $(document).on("click", "#bulkTelesalesLeaderSubmitBtn", function() {

            let bulk_leads_telesales_leader = $('#bulk_leads_telesales_leader').val();
            let email_notification = $('#telesales_head_email_notification').prop('checked');
            let system_notification = $('#telesales_head_system_notification').prop('checked');
            let telegram_notification = $('#telesales_head_telegram_notification').prop('checked');

            if (bulk_leads_telesales_leader == "") {
                showToastify("Please select Telesales.", "error")
                return;
            } else {
                if (ids_array = [], document.getElementsByName("chk_child").forEach((function(e) {
                        if (1 == e.checked) {
                            var t = e.value;
                            ids_array.push(t)

                        }
                    })), "undefined" != typeof ids_array && ids_array.length > 0) {
                    $.ajax({
                        url: "{{ route('admin.work.leads.bulk.assign.telesales.leader') }}",
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            ids: ids_array,
                            telesales_head_id: bulk_leads_telesales_leader,
                            email_notification: email_notification,
                            system_notification: system_notification,
                            telegram_notification: telegram_notification,
                        },
                        success: function(result) {
                            if (result.status == '1') {
                                showToastify(result.msg, "success")
                                $("#bulkReassignTelesalesLeaderForm").trigger("reset")
                                $("#telesalesLeaderModal").modal("hide")
                                $("#checkAll").prop("checked", this.checked)
                                document.getElementById("checkAll").checked = !1
                                dataTable.row().draw();
                                $('.multi_options').remove();
                            } else {
                                showToastify(result.msg, "error")
                            }
                        },
                        error: function(result) {
                            console.log(result);
                        }
                    });
                }

                // Ajax Form goes here
            }


        })




        $(document).on("click", "#bulkAssignment", function() {
            var selectedOption = $('#selectAction').val();
            var id;
            if (selectedOption == 'Re-Assign') {
                id = $('#agents_dropdown').val();
            } else if (selectedOption == 'Telesales') {
                id = $('#bulk_leads_telesales').val();
            } else if (selectedOption == 'Telesales Head') {
                id = $('#bulk_leads_telesales_leader').val();
            }


            let email_notification = $('#email_notification').prop('checked');
            let system_notification = $('#system_notification').prop('checked');
            let telegram_notification = $('#telegram_notification').prop('checked');

            if (selectedOption == "") {
                showToastify("Please select Option.", "error")
                return;
            } else {
                if (ids_array = [], document.getElementsByName("chk_child").forEach((function(e) {
                        if (1 == e.checked) {
                            var t = e.value;
                            ids_array.push(t)

                        }
                    })), "undefined" != typeof ids_array && ids_array.length > 0) {
                    console.log('ajax ready')

                    $.ajax({
                        url: "{{ route('admin.work.leads.general.bulk.assign.leads') }}",
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            ids: ids_array,
                            id: id,
                            action: selectedOption,
                            email_notification: email_notification,
                            system_notification: system_notification,
                            telegram_notification: telegram_notification,
                        },
                        success: function(result) {
                            console.log('pass')

                            if (result.status == '1') {
                                showToastify(result.msg, "success")
                                $("#selectAction").val('').trigger("change")
                                $('#agent_dropdown').addClass('d-none');
                                $('#telesales_dropdown').addClass('d-none');
                                $('#telesales_head_dropdown').addClass('d-none');

                                $("#generalModalForm").trigger("reset")
                                $("#generalModal").modal("hide")
                                $("#checkAll").prop("checked", this.checked)
                                document.getElementById("checkAll").checked = !1
                                dataTable.row().draw();
                                $('.multi_options').remove();
                            } else {
                                showToastify(result.msg, "error")
                            }
                        },
                        error: function(jqXhr, json, errorThrown) {
                            let data = jqXhr;

                            $("#saveBtn").prop('disabled', false);
                            if (data.responseJSON.errors) {
                                $.each(data.responseJSON.errors, function(index, item) {
                                    showToastify(item[0], "error")
                                })
                            }

                            if (jqXhr.status == 500 || jqXhr.status == 400 || jqXhr.status == 422) {
                                if (data.responseJSON.message) {
                                    showToastify(data.responseJSON.message, "error")
                                }
                            }
                            console.table(data);
                        }
                    });
                }

                // Ajax Form goes here
            }


        })
        // This code is working for sticky navbar
        window.onscroll = function() {
            myFunction()
            scrollFunction();
        };

        // Get the navbar
        var navbar = document.getElementById("top-bar");
        var tableHeader = document.getElementById("table-sticky-header");

        // Get the offset position of the navbar
        var sticky = navbar.offsetTop;

        // Add the sticky class to the navbar when you reach its scroll position. Remove "sticky" when you leave the scroll position
        function myFunction() {
            if (window.pageYOffset >= sticky) {
                navbar.classList.add("sticky-header");
                tableHeader.classList.add("sticky-table-header");
            } else {
                navbar.classList.remove("sticky-header");
                tableHeader.classList.remove("sticky-table-header");
            }
        }

        $(document).on("click", "#addNoteButton", function() {
            @can('Lead Notes Create')
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
                formData.append("lead_id", id)
                formData.append("note", note)
                $.ajax({
                    url: "{{ route('admin.work.leads.add-note') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: formData,
                    cache: false,
                    processData: false,
                    contentType: false,
                    success: function(result) {
                        if (result.status == '1') {
                            showToastify(result.msg, "success")
                            $("#note").val("").trigger("change");
                            getLeadNotes()
                        }
                    },
                    error: function(jqXhr, json, errorThrown) {
                        let data = jqXhr.responseJSON;
                        if (data.errors) {
                            $.each(data.errors, function(index, item) {
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

        $(document).ready(function() {
            // this function opens the
            setTimeout(function() {

                var open_id = "{{ Request('lead') }}";
                if (open_id) {
                    $('.dataTables_filter .form-control').val(open_id).trigger('keyup');
                }

            }, 500);
        });



        function highlightRow(row) {
            row.removeClass('selected');
            dataTable.$('tr.selected').removeClass('selected');
            row.addClass('selected');
        } // highlightRow

        function checkExistence(value, column, model, id = "null") {
            let agent_id = $("#agent_id").val();
            $(`#${column}-exists`).html("")
            $(`#${column}`).removeClass("is-invalid")
            $(`#${column}`).next(".invalid-feedback").text(`enter a valid ${column}.`);
            if (value.length > 0) {
                $.ajax({
                    url: "{{ route('ajax.is-value-unique') }}",
                    type: "get",
                    data: {
                        value,
                        column,
                        model,
                        id,
                        agent_id
                    },
                    success: function(result) {
                        if (!result.data) {
                            //$(`#${column}`).addClass("is-invalid")
                            //$(`#${column}`).next(".invalid-feedback").text(`The ${column} has already been taken.`)
                            //$(`#${column}-exists`).html(`This lead already exists <a href="javascript:void(0)" data-id="${result.id}" class="text-primary" id="request-access-${column}">request access</a>`)
                            // showToastify(`The ${column} has already been taken.`, "error")
                        }
                    }
                });
            }
        }

        function getProperties() {
            $("#property_id").val("").trigger("change");
            // $('#criteriaDiv').addClass('d-none');
            //$('#criteriaTabLoader').removeClass('d-none');

            $("#property_id").html("");
            let id = $("#agent_id").val();
            let lead_for = $("#for").val();
            if (id) {
                $.ajax({
                    url: "{{ route('ajax.properties') }}",
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        agent_id: id,

                    },
                    success: function(result) {
                        let options = "";
                        result?.data.map(item => {
                            options +=
                                // `<option value="${item.id}">[${item.ref_no}] - (Beds: ${item.bed?.name || ""}) - (Unit: ${item.unit_no})</option>`;
                                `<option value="${item.id}">${item.ref_no} - ${item.property_category?.title || ""} - ${item.unit_no || ""} - ${item.location}</option>`;
                        })
                        $("#property_id").html(options);
                        $("#property_id").val(dataObj?.property_id).trigger("change");
                        //$('.inputsCriteria').val("").trigger("change");
                        //$('#criteriaDiv').removeClass('d-none');
                        //$('#criteriaTabLoader').addClass('d-none');

                    },
                    error: function(result) {
                        console.log(result);
                    }
                });
            } else {
                //$('#criteriaDiv').removeClass('d-none');
                //$('#criteriaTabLoader').addClass('d-none');
            }
        }

        // $(document).on("change", "#for", function() {
        //     getProperties();
        // })

        $(document).on("change", "#agent_id", function() {
            let id = $("#id").val();
            checkExistence(mobileInput.getNumber(), "mobile", "Lead", id)
            checkExistence($("#email").val(), "email", "Lead", id)
            if (mode != "view") {
                getProperties();
            }

        })

        $(document).on("click", "#request-access-mobile, #request-access-email", function() {
            let id = $(this).data("id");
            swal({
                    title: "Are you sure?",
                    text: "Are you sure want to request?",
                    icon: 'warning',
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) { // on confirm delete the category
                        $.ajax({
                            url: "{{ route('admin.work.leads.request-access') }}",
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                id
                            },
                            success: function(result) {
                                if (result.status == '1') { //if successfully deleted
                                    showToastify(result.msg, "success")
                                } else { // something went wrong on backend
                                    showToastify(result.msg, "error")
                                }
                            },
                            error: function(result) {
                                console.table(result);
                            }
                        });
                    }
                });
        })

        $(document).on("change", "#email", function() {
            checkExistence($(this).val(), "email", "Lead", $("#id").val())
        })

        $(document).on("change", "#mobile", function() {
            $("#whatsapp").val(mobileInput.getNumber()).trigger("change");
            checkExistence(mobileInput.getNumber(), "mobile", "Lead", $("#id").val())
        })

        function toggleForm(show) {
            if (show == undefined) {
                $("#collapseContactForm").toggleClass("show")
                $('html, body').animate({
                    scrollTop: 0
                }, 'fast');
                return
            }
            if (show) {
                $("#collapseContactForm").addClass("show");
                $('html, body').animate({
                    scrollTop: 0
                }, 'fast');
            } else {
                $("#saveBtn, #closeBtn, #editBtn, #bulkAssignBtn").addClass("d-none");
                $("#newBtn").removeClass("d-none");
                $("#collapseContactForm").removeClass("show");
            }
        }
        $('.modelReasonClose').click(function() {

            $('.click_New').click().addClass('active');
            $('.click_Unsuccessful').removeClass('active');
        })


        $("#newBtn").click(function() {
            @can('Leads Create')

                mode = 'create';
                // click on first .open_sub_status and change value of lead_status_id
                // let lead_status_id = $('.open_sub_status:first').data('id');
                // $('#lead_status_id').val(lead_status_id).trigger('change');

                // $('.open_sub_status').find('span').addClass('randomClass');

                isUpdate = false;
                // $('#criteriaFormDiv').addClass('d-none');
                //$('#criteriaTabLoader').addClass('d-none');
                $('#mainForm').trigger('reset');
                $('#mainForm').removeClass('was-validated');
                $(".inputs").prop("disabled", false)
                $(".inputs").val("").trigger("change")
                $(".inputs").removeClass("is-invalid")
                $("#id").val("")
                $("#saveBtn, #closeBtn").removeClass("d-none");
                $("#newBtn, #editBtn, #deleteBtn, #followUpMenusTab, #bulkAssignBtn, #dropdownMenuLink1, #messageDiv")
                    .addClass(
                        "d-none");
                $("#addOnAgentDiv, .addHide").addClass("d-none");
                toggleForm(true)
                $("#col-logs").addClass("d-none");
                resetStatus()
                $("#calling-button").addClass("d-none");
                $('.rmDark').removeClass('bg-dark')
                $('.OpenStatus').removeClass('ribbon-info')
                $('.OpenStatus').addClass('ribbon-soft-info')
                $('.ClassStatus').addClass('ribbon-soft-primay')
                $('.ClassStatus').removeClass('ribbon-soft-info')
                $("#call_now_button").addClass("d-none");
                $("#start-call-button").addClass("d-none");
                $("#whatapp-button").addClass("d-none");

                $('#whatsappDiv1').addClass('col-md-4')
                $('#whatsappDiv2').hide()
                $('#whatsappDiv1').removeClass('col-md-3 pr-0')

                $('#mobileDiv1').addClass('col-md-4')
                $('#call_now_button').hide()
                $('#mobileDiv1').removeClass('col-md-3 3 pr-0')

                $('.open_sub_status').find('span').addClass('randomClass');
                $('#lead_status_id').val('');
                $('#lead_sub_status_id').val('');

                $('.open_sub_status').removeClass('active');
                if (mode === 'view') {
                    $('#makeItDisable').addClass('non-clickable');
                } else {
                    $('#makeItDisable').removeClass('non-clickable');
                }

                let agent_logged_in = "{{ auth()->user()->id }}";
                $("#agent_id").val(agent_logged_in).trigger('change');
                $('#lead_status_id').val('');
                $('#lead_sub_status_id').val('');
                // clear all inputs
                $('.inputsCriteria').val('').trigger('change');
                $('#responseDiv').addClass('d-none');

                $('#property_id').empty().trigger('change');

                $('#follow_up_label').html('Follow Up');
                $('#follow_up_date').attr('required', false);
            @endcan
        });

        $(document).on("click", "#closeBtn", function() {
            toggleForm(false)
            mode = 'view';
            $('#lead_id').addClass('d-none');
            $('#dropdownMenuLink1').addClass('d-none');

        })
    </script>
    @include('admin.lead.include.script')
    <script>
        let viewUrl = "{{ route('admin.work.leads.view', ['lead' => '1']) }}";
        viewUrl = viewUrl.slice(0, -1);

        let dataTable = $('#contacts-table').DataTable({ //datatable initialization
            "order": [
                [10, "DESC"]
            ],
            'lengthMenu': [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],
            dom: `<'row'<'col-sm-10 text-left'B> <'col-sm-2 text-right'f>>
            <'row'<'col-sm-12'tr>>
            <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>`,
            buttons: [{
                extend: 'colvis',
                titleAttr: 'Show/Hide',
                text: '<i class="colvis" style="font-size:09px">Show / Hide</i>',

            }],
            language: {
                searchPlaceholder: "Search leads"
            },
            createdRow: function(row, data, index) {
                $('#contacts-table tbody').css('cursor', 'pointer');

            },

            responsive: true,
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "{{ route('admin.work.leads.dt') }}",
                type: "post",
                data: function(d) {
                    d.all_listings = all_listing_val;

                    d.id = filtersData?.id;
                    d.id_operator = filtersData?.search_id_operator;

                    d.agents = filtersData?.search_agents;
                    d.agents_operator = filtersData?.search_agents_operator;

                    d.property = filtersData?.property_ref;
                    d.property_operator = filtersData?.property_ref_operator;

                    d.for = filtersData?.search_for;
                    d.for_operator = filtersData?.search_for_operator;

                    // d.telesales_head = filtersData?.filter_telesales_head;
                    // d.telesales_head_operator = filtersData?.filter_telesales_head_operator;

                    // d.telesales = filtersData?.filter_telesales;
                    // d.telesales_operator = filtersData?.filter_telesales_operator;

                    d.lead_statuses = filtersData?.search_lead_statuses;
                    d.lead_statuses_operator = filtersData?.search_lead_statuses_operator;

                    d.lead_sources = filtersData?.search_lead_sources;
                    d.lead_sources_operator = filtersData?.search_lead_sources_operator;

                    d.lead_types = filtersData?.search_lead_types;
                    d.lead_types_operator = filtersData?.search_lead_types_operator;

                    d.lead_medium = filtersData?.search_lead_medium;
                    d.lead_medium_operator = filtersData?.search_lead_medium_operator;




                    d.priorities = filtersData?.search_priorities;
                    d.priorities_operator = filtersData?.search_priorities_operator;


                    d.genders = filtersData?.search_genders;
                    d.genders_operator = filtersData?.search_genders_operator;

                    d.nationality = filtersData?.search_nationalities;
                    d.nationalities_operator = filtersData?.search_nationalities_operator;

                    d.created_from = filtersData?.search_created_from;
                    d.date_created_operator = filtersData?.search_date_created_operator;

                    d.bitrix_data = filtersData?.search_bitrix_data;
                    d.bitrix_data_operator = filtersData?.search_bitrix_data_operator;

                    d.deleted = filtersData?.search_deleted;
                    d.archive = filtersData?.search_archive;
                    d.leadpool = filtersData?.leadpool;


                    d.communities = filtersData?.filter_communities;
                    d.communities_operator = filtersData?.filter_communities_operator;

                    d.sub_communities = filtersData?.filter_sub_communities;
                    d.sub_communities_operator = filtersData?.filter_sub_communities_operator;

                    d.locations = filtersData?.filter_locations;
                    d.community_properties = filtersData?.community_properties;

                    d.furnished = filtersData?.filter_furnished;
                    d.filter_furnished_operator = filtersData?.filter_furnished_operator;

                    d.min_bed_id = filtersData?.filter_min_bed_id;
                    d.max_bed_id = filtersData?.filter_max_bed_id;
                    d.min_bath_id = filtersData?.filter_min_bath_id;
                    d.max_bath_id = filtersData?.filter_max_bath_id;
                    d.min_price = filtersData?.filter_min_price;
                    d.max_price = filtersData?.filter_max_price;
                },
                error: function() {
                    $(".users-error").html("");
                    $("#dataTable").append(
                        '<tbody class="users-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>'
                    );
                    $("#dataTable_processing").css("display", "none");
                }
            },
            fnInitComplete: function(oSettings, json) {
                let first = $("#dataTable_wrapper .row div:first");
                first.addClass("col-md-5").removeClass("col-md-6")
                $(first).next().addClass("col-md-7").removeClass("col-md-6")
                $(".dataTables_filter label").addClass(
                    "d-flex justify-between-between align-items-center gap-2")
                $(`@includeIf('admin.lead.include.filterButtons')`).insertAfter(".dataTables_filter input")
            },

            "aoColumns": [{
                    data: null,
                    orderable: false,
                }, {
                    data: 'id',
                    name: 'id',
                }, {
                    data: 'first_name',
                    name: 'first_name',
                }, {
                    data: 'mobile',
                    name: 'mobile',
                    orderable: false,
                }, {
                    data: 'source_id',
                    name: 'source_id',
                    orderable: false,
                }, {
                    data: 'follow_up_date',
                    name: 'follow_up_date',
                    orderable: false,
                }, {
                    data: 'created_by_id',
                    name: 'created_by_id',
                    orderable: false,
                }, {
                    data: 'agent_id',
                    name: 'agent_id',
                    orderable: false,
                }, {
                    data: 'for',
                    name: 'for',
                    orderable: false,
                },
                /*{
                    data: 'lead_status_id',
                    name: 'lead_status_id',
                    orderable: false,
                },
                */

                {
                    data: 'lead_sub_status_id',
                    name: 'lead_sub_status_id',
                    orderable: false,
                },

                {
                    data: 'updated_at',
                    name: 'updated_at',
                    orderable: true,
                },
                /*{


                    data: null,
                    orderable: false,
                },*/
            ],
            "columnDefs": [{
                    "targets": 0,
                    orderable: true,
                    render: function(type, row, data, meta) {
                        return `<div class="form-check">
                                                <input class="form-check-input selectable-checkbox  " type="checkbox" name="chk_child" value="` +
                            data.id + `">
                                            </div>`;
                    }
                },

                {
                    "targets": 2,
                    orderable: true,
                    render: function(type, row, data, meta) {
                        let image = data.lead_medium ? data.lead_medium.icon ? data.lead_medium.icon :
                            'mdi mdi-account-cash' : 'mdi mdi-account-cash';
                        let name = data.lead_medium ? data.lead_medium.name : 'Unspecified';
                        return `<a href="javascript:void(0)" title="${name}" onclick="viewData('` + data
                            .id +
                            `')" class="fw-medium link-primary"> <span class='me-1'><i class='${image}'></i></span>` +
                            (data.first_name || 'New') + ` ` + (data
                                .last_name || "") +
                            `</a>`;
                    }
                },
                {
                    "targets": 4,
                    orderable: true,
                    render: function(type, row, data, meta) {
                        let avatar = data.lead_source?.thumbnail ? data.lead_source?.thumbnail :
                            'https://ui-avatars.com/api/?name=' + data.lead_source?.name;
                        return `<span class="display-inline"><img src='${avatar}' class="pe-1" alt="" style="height: 20px; width: 20px">${data.lead_source ? data.lead_source.name : "-"}</span>`;
                    }
                },

                {
                    "targets": 5,
                    visible: false,
                    orderable: true,
                    render: function(type, row, data, meta) {
                        if (data.follow_up_date) {
                            if (moment().isSame(moment(data.follow_up_date), 'day')) {
                                return '<span class="badge badge-soft-danger badge-border">Today</span>';
                            } else if (moment() < moment(data.follow_up_date)) {
                                return '<span class="badge badge-soft-warning badge-border">' + moment(data
                                    .follow_up_date).format('ll') + '</span>';
                            } else {
                                return '<span class="badge badge-soft-info badge-border">' + moment(data
                                    .follow_up_date).format('ll') + '</span>';

                            }

                        } else {
                            return '';
                        }

                    }
                },



                {
                    "targets": 6,
                    orderable: true,

                    render: function(type, row, data, meta) {
                        return data.type_id ? data.lead_type.name : "-";
                    }
                },
                {
                    "targets": 7,
                    orderable: true,
                    render: function(type, row, data, meta) {
                        if (!data.agent) return "-";
                        return `<div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-2">
                                        <img src="${data.agent.thumb_url}" alt="" class="avatar-xs rounded-circle">
                                    </div>
                                    <div class="flex-grow-1">${data.agent.name}</div>
                                </div>`;
                    }
                },
                /*
                {
                    "targets": 9,
                    orderable: true,
                    render: function (type, row, data, meta) {
                        return data.lead_status ? data.lead_status.name : "-";
                    }
                },
                */
                {
                    "targets": 8,
                    orderable: true,

                    render: function(type, row, data, meta) {
                        let subStatus = '';
                        if (Array.isArray(data.for)) {
                            subStatus = data.for.map(item =>
                                `<span class="badge align-middle badge-soft-dark">${item}</span>`).join(
                                '');
                        } else {
                            subStatus =
                                '<span class="badge align-middle badge-soft-dark">Unspecified</span>';
                        }
                        return subStatus;
                    }


                },
                {
                    "targets": 9,
                    orderable: true,

                    render: function(type, row, data, meta) {
                        let subStatus =
                            `<span class="badge align-middle ${data?.lead_status?.badge ??'color-bg-19'}">${data.lead_status ? data.lead_status.name : "Unspecified"}</span>`
                        return subStatus;
                    }


                },
                {
                    "targets": 10,
                    orderable: true,
                    render: function(type, row, data, meta) {
                        let url = "{{ route('admin.work.leads.index') }}" + "?id=" + data.id;

                        let new_tab = '<a title="Open Lead in New Tab" target="_blank" href="' + url +
                            '"><span class="ri-external-link-line"></span></a>';

                        let html = `<div class="d-flex justify-content-between gap-2 align-items-center">
                                    <div class="text-center"><br>
                                        <span title="${moment(data.updated_at).format('DD/MM/YYYY hh:mm:ss')}">${moment(data.updated_at).fromNow()}</span>
                                    </div>${new_tab}
                                </div>`

                        if (data.updated_at == null) {
                            return html;
                        } else {
                            return html;
                        }
                    }
                }
            ]
        });

        dataTable.on('click', 'tbody tr', function(e) {
            console.log(mode);
            console.log('===');

            var $cell = $(e.target).closest('td'),
                msg;

            //console.log('Index ==========='+$cell.index());

            if ($cell.index() != 0) {
                var data = dataTable.row(this).data();

                if ($cell.index() === 11 || $cell.index() === 12) {
                    // console.log($cell);
                } else {


                    if (mode == 'create' || mode == 'edit') {

                        var r = confirm("Are you sure you want to discard the changes?");
                        if (r == true) {
                            mode = 'view';
                            viewData(data.id);
                            highlightRow($(this));

                        } else {

                        }


                    } else {

                        viewData(data.id);
                        highlightRow($(this));
                    }
                }


            } // end if
            else {}


        }); // table row click


        dataTable.on('draw.dt', function() {
            $(".chk_child").prop("checked", false);
            $('.multi_options').remove();
            $('#checkAll').prop("checked", false);
        });


        $(document).on('change', '#checkAll', function(e) {

            dataTable.$('tr').toggleClass('selected');
            updateMultiOption();

        });


        $(document).on('change', 'tbody tr .selectable-checkbox', function() {

            $(this).parents('tr').toggleClass('selected');
            var checked = $(this).is(':checked');
            if (!checked) {
                $(this).prop('checked', false);
            } else {
                $(this).prop('checked', true);
            }
            updateMultiOption();
        });



        $(document).on('click', '#bulk_reassign_btn', function() {

            $("#bulkReassignLeadsModal").modal({
                backdrop: 'static',
                keyboard: false
            })
            $('#generalModal').modal('show');

            // $('#bulkReassignLeadsModal').modal('show');
        });

        $(document).on('click', '#bulk_telesale_assign_btn', function() {

            $("#telesalesModal").modal({
                backdrop: 'static',
                keyboard: false
            })
            $('#telesalesModal').modal('show');
        });

        $(document).on('click', '#bulk_telesale_leader_assign_btn', function() {

            $("#telesalesLeaderModal").modal({
                backdrop: 'static',
                keyboard: false
            })
            $('#telesalesLeaderModal').modal('show');
        });

        function updateMultiOption() {
            var countCheckedCheckboxes = $('input[name="chk_child"]:checked').length;
            console.log(countCheckedCheckboxes);
            $('#bulk_leads_count').html(countCheckedCheckboxes);
            $('#count_selected').html(countCheckedCheckboxes);
            if (countCheckedCheckboxes >= 1) {
                if ($('.multi_options').length === 0) {
                    $(`@can('Leads Bulk Action')<div class="multi_options" style="display:inline-block; margin-left:10px;">

                          <div class="btn-group">
                            <button class="btn btn-primary btn-sm dropdown-toggle action-bulk" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Bulk Actions
                            </button>
                            <div class="dropdown-menu bulkActionDropdown">
                                @can('Leads Assign Agent')
                                <a class="dropdown-item" href="javascript:;" id="bulk_reassign_btn">Assign</a>
                                @endcan

                                 @can('Leads Delete')
                                 <div class="dropdown-divider"></div>
                                 @can('Leads Archive')
                                <button type="button" class="dropdown-item" id="archiveBtn" data-id="">Archive</button>
                                @endcan
                                 <a class="dropdown-item" href="javascript:;" onclick="deleteMultiple()">Delete</a>
                                 @endcan

                            </div>
                        </div>
                   </div>@endcan`).insertAfter('.dt-buttons');
                }
            } else {
                $('.multi_options').remove();
            }
        }
        // <a class="dropdown-item" href="javascript:;">Change Status</a>


        $(document).on('submit', '#mainForm', function(event) {
            $("#saveBtn").prop('disabled', true);
            event.preventDefault();

            // alert($('#lead_sub_status_id').val());
            // alert($('#lead_status_id').val());
            if ($('#lead_status_id').val() == '') {
                showToastify("Please Select Status.", "error")
                $('#saveBtn').prop('disabled', false);
                return;
            } else {
                function save() {
                    var formData = new FormData($("#mainForm")[0]);
                    $("#mobile").val(mobileInput.getNumber())
                    $("#phone").val(phoneInput.getNumber())
                    formData.append("mobile", mobileInput.getNumber())
                    formData.append("phone", phoneInput.getNumber())
                    $.ajax({
                        url: isUpdate ? "{{ route('admin.work.leads.update') }}" :
                            "{{ route('admin.work.leads.store') }}",
                        type: 'POST',
                        dataType: 'json',
                        data: formData,
                        cache: false,
                        processData: false,
                        contentType: false,
                        success: function(result) {
                            mode = 'view';
                            if (result.status == '1') {
                                showToastify(result.msg, "success")
                                $("#saveBtn").prop('disabled', false); //enable submit button
                                $('#mainForm').trigger('reset'); //
                                $(".inputs").val("").trigger("change")
                                $(".inputs").removeClass("is-invalid")
                                $("select.inputs").val("").trigger("change")
                                viewData(result.id)
                                dataTable.row().order([10, 'DESC']).draw();
                                isUpdate =
                                    true; // set isUpdate to false so that when we try to add new category it will be create request
                            } else if (result.status == '2') {
                                $("#saveBtn").prop('disabled', false);
                                let error_message = '';

                                $.each(result.errors, function(key, value) {
                                    error_message += '' + key + ': ' + value + ' | ';
                                });
                                showToastify(error_message, "error")
                            } else {
                                $("#saveBtn").prop('disabled', false);
                                swal("Oops!", result.msg, "error");
                                showToastify(result.msg, "error")
                            }
                        },
                        error: function(jqXhr, json, errorThrown) {
                            $("#saveBtn").prop('disabled', false);
                            let data = jqXhr.responseJSON;
                            if (data.errors) {
                                $.each(data.errors, function(index, item) {
                                    showToastify(item[0], "error")
                                })
                            }
                            if (jqXhr.status == 500 || jqXhr.status == 400) {
                                showToastify(data.message, "error")
                            }
                            mode = 'view';
                        },
                    });
                }
            }
            let status = $("#lead_status_id").val()
            let sub_status = $("#lead_sub_status_id").val()
            if (status == 2 && sub_status == 2) {
                swal({
                        title: "Note",
                        text: "Note: This Lead will be converted into contact. You will not be able to Edit this lead anymore!",
                        icon: 'warning',
                        buttons: true,
                        dangerMode: true,
                    })
                    .then((willDelete) => {
                        if (willDelete) { // on confirm delete the category
                            save()
                        } else {
                            $('#saveBtn').prop('disabled', false);
                        }
                    });
            } else {
                save();
            }

        });

        $('.status_Unsuccessful').click(function() {
            if (mode == 'edit') {
                // let = previous_status = $('#lead_status_id').val();
                $('#unsuccessfull_lead_id').val($('#id').val());
                $('#unsuccessfullReasonModal').modal('show');
            }

        });
        $(document).on("submit", "#criteriaForm", function(e) {
            $("#leadCirteriaSaveBtn").prop('disabled', true);
            e.preventDefault();
            var formData = new FormData($("#criteriaForm")[0]);
            $.ajax({
                url: isUpdate ? "{{ route('admin.work.leads.store.criteria') }}" :
                    "{{ route('admin.work.leads.store.criteria') }}",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function(result) {
                    if (result.status == '1') {
                        showToastify(result.msg, "success")
                        $("#leadCirteriaSaveBtn").prop('disabled', false); //enable submit button
                    } else if (result.status == '2') {
                        $("#leadCirteriaSaveBtn").prop('disabled', false);
                        let error_message = '';
                        $.each(result.errors, function(key, value) {
                            error_message += '' + key + ': ' + value + ' | ';
                        });
                        showToastify(error_message, "error")
                    } else {
                        $("#leadCirteriaSaveBtn").prop('disabled', false);
                        showToastify(result.msg, "error")
                    }
                },
                error: function(jqXhr, json, errorThrown) {
                    $("#leadCirteriaSaveBtn").prop('disabled', false);
                    let data = jqXhr.responseJSON;
                    if (data.errors) {
                        $.each(data.errors, function(index, item) {
                            showToastify(item[0], "error")
                        })
                    }
                    if (jqXhr.status == 500 || jqXhr.status == 400) {
                        showToastify(data.message, "error")
                    }
                },
            })
        });

        $('#contacts-table tbody').on('click', 'a.edit-link', function() {
            @can('Leads Edit')
                let id = $(this).data('id');
                editData(id);
            @endcan

        });

        $(document).on("click", "#addMeeting", function() {
            $("#meetingForm").trigger("reset");
            $("#meetingForm").find("input, select").val("").trigger("change");
            $("#meetingForm").removeClass("was-validated");
            $("#meeting_lead_id").val(dataObj.id)
            $("#meetingModal").modal("show");
        })



        $(document).on("click", ".change-meeting-status", function() {
            let meeting_id = $(this).parent(".dropdown-menu").data("id");
            let status = $(this).data("status");

            function changeMeetingStatus() {
                $.ajax({
                    url: "{{ route('admin.work.leads.meeting.status') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        meeting_id,
                        status
                    },
                    success: function(result) {
                        if (result.status == '1') {
                            let data = result.data;
                            showToastify(result.msg, "success");
                            getMeetingLog(data.lead_id)
                            dataTable.row().draw();
                        } else {
                            showToastify(result.msg, "error");
                        }
                    },
                    error: function(result) {
                        console.log(result);
                    }
                });
            }

            @can('Lead Meeting Status Change')
                swal({
                        title: "Attention!!",
                        text: "You are about to change meeting status?",
                        icon: "warning",
                        buttons: true,
                        dangerMode: true,
                    })
                    .then((willDelete) => {
                        if (willDelete) {
                            changeMeetingStatus();
                        }
                    });
            @endcan
        })

        $(document).on("click", "#addTask", function() {
            $("#taskForm").trigger("reset");
            $("#taskForm").find("input, select").val("").trigger("change");
            $("#taskForm").removeClass("was-validated");
            $("#task_lead_id").val(dataObj.id)
            var date = $('#follow_up_date').val();
            $('#task_due_date').val(date);
            $("#taskModal").modal("show");

        })



        $(document).on("click", ".change-task-status", function() {
            let task_id = $(this).parent(".dropdown-menu").data("id");
            let status = $(this).data("status");

            function changeTaskStatus() {
                $.ajax({
                    url: "{{ route('admin.work.leads.task.status') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        task_id,
                        status
                    },
                    success: function(result) {
                        if (result.status == '1') {
                            let data = result.data;
                            showToastify(result.msg, "success");
                            getTask(data.lead_id)
                            dataTable.row().draw();
                        } else {
                            showToastify(result.msg, "error");
                        }
                    },
                    error: function(result) {
                        console.log(result);
                    }
                });
            }

            @can('Lead Task Status Change')
                swal({
                        title: "Attention!!",
                        text: "You are about to change task status?",
                        icon: "warning",
                        buttons: true,
                        dangerMode: true,
                    })
                    .then((willDelete) => {
                        if (willDelete) {
                            changeTaskStatus();
                        }
                    });
            @endcan
        })



        function addEvent(type, refreshBtnId = null) {
            let agent = $("#agent_id").select2("data")[0];
            let property = $("#property_id").select2("data")[0];
            let data = {
                date: moment().format("YYYY-MM-DD HH:mm:ss"),
                type: type,
                contact: {
                    id: null,
                    name: null,
                    email: null,
                    phone: null,
                },
                lead: {
                    id: $("#id").val(),
                    name: $("#first_name").val() + ' ' + $("#last_name").val(),
                },
                user: {
                    id: $("#agent_id").val(),
                    name: agent?.text || ""

                },
                property: {
                    id: property?.id || null,
                    title: property?.text || null
                }
            };
            @can('Events Create')
                getEventModal(data, refreshBtnId);
            @endcan
        }

        function viewEvent(id) {
            @can('Events View')
                showEventViewModal(id);
            @endcan
        }

        function editEvent() {
            @can('Events Edit')
                showEventEditForm();
            @endcan
        }

        function getMeetingLog(id) {
            let statusSelect = "";
            @json(\App\Models\Meeting::STATUS_SELECT)?.
            map(item => {
                statusSelect +=
                    `<a class="dropdown-item change-meeting-status" href="javascript:void(0)" data-status="${item}">${item}</a>`;
            });
            let alertMsg = `<div class="card-animate">
                                <div class="card-body">
                                    <div class="d-flex mb-3 text-center">
                                        <div class="flex-grow-1">
                                            <lord-icon src="{{ asset('assets/json/icons/wired-outline-981-consultation.json') }}" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:55px;height:55px"> </lord-icon>
                                        </div>


                                    </div>
                                    <h3 class="mb-2 text-center"><span class="counter-value text-muted text-center" data-target="97685">No Meetings</span><small class="text-muted fs-13"></small></h3>
                                    <h6 class="text-muted mb-0"></h6>
                                </div>
                            </div>`;
            let meetingStatusChangePermission = "{{ auth()->user()->can('Lead Meeting Status Change') }}";

            $.ajax({
                url: "{{ route('admin.events.all') }}",
                type: 'POST',
                dataType: 'json',
                data: {
                    column: 'lead_id',
                    value: id,
                    type: 'Meeting'

                },
                success: function(result) {
                    $("#meetingLogs").html('');
                    if (result.status == '1') {
                        let html = '';
                        let status = '';
                        Object.keys(result.log).map((index) => {
                            if (result.log[index].status == 'PENDING') {
                                status = `Pending`;
                                statusOption = ``;
                            } else if (result.log[index].status == 'RUNNING') {
                                status = `Running`;
                            } else if (result.log[index].status == 'COMPLETED') {
                                status = `Completed`;
                            } else if (result.log[index].status == 'CANCELED') {
                                status = `Canceled`;
                            } else {
                                status = ``;
                            }
                            let statusDropDown = '';
                            if (meetingStatusChangePermission) {
                                statusDropDown = ` <button type="button" class="btn btn-sm btn-light dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-haspopup="false" aria-expanded="false">
                                                    </button>
                                                    <div class="dropdown-menu" data-id="${result.log[index].id}">
                                                        ${statusSelect}
                                                    </div>`;
                            }
                            let start_time = result.log[index].start_time ? moment(result.log[index]
                                .start_time).format("DD/MM/YYYY HH:mm A") : "";
                            let end_time = result.log[index].end_time ? moment(result.log[index]
                                .end_time).format("DD/MM/YYYY HH:mm A") : "";
                            html += `<div class="text-reset notification-item d-block dropdown-item">
                                        <div class="d-flex">
                                            <div class="flex-1" onclick="viewEvent('${result.log[index].id}')" data-id="${result.log[index].id}">
                                                <h6 class="mt-0 mb-1 fs-13 fw-semibold"> ${result.log[index].title || ""}</h6>
                                                <div class="fs-13 text-muted">
                                                    <p class="mb-1">${result.log[index].description || ""}</p>
                                                </div>
                                                <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                    <span><i class="mdi mdi-clock-outline"></i> ${start_time || ""} - ${end_time || ""}<i class="mdi mdi-google-maps"></i> ${result.log[index].location || ""}</span>
                                                </p>
                                            </div>
                                            <div class="px-2 fs-16">
                                                <div class="btn-group">
                                                    <button class="btn btn-light btn-sm" type="button">
                                                        ${status}
                                                    </button>
                                                   ${statusDropDown}
                                                </div>
                                            </div>
                                        </div>
                                    </div>`;
                        });
                        $("#meetingLogs").html(html);
                        let element = document.getElementById("meetingLogs");
                        element.scrollTop = element.scrollHeight;

                    } else {
                        $("#meetingLogs").html(alertMsg);

                    }
                },
                error: function(jqXhr, json, errorThrown) {
                    $("#meetingLogs").html(alertMsg);
                    let data = jqXhr.responseJSON;
                    if (data.errors) {
                        $.each(data.errors, function(index, item) {
                            showToastify(item[0], "error")
                        })
                    }
                    if (jqXhr.status == 500 || jqXhr.status == 400) {
                        showToastify(data.message, "error")
                    }
                },
            });
        }

        function getViewingLog(id) {
            let statusSelect = "";
            @json(\App\Models\Meeting::STATUS_SELECT)?.
            map(item => {
                statusSelect +=
                    `<a class="dropdown-item change-meeting-status" href="javascript:void(0)" data-status="${item}">${item}</a>`;
            });
            let alertMsg = `<div class="card-animate">
                                <div class="card-body">
                                    <div class="d-flex mb-3 text-center">
                                        <div class="flex-grow-1">
                                            <lord-icon src="{{ asset('assets/json/icons/wired-outline-63-home.json') }}" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:55px;height:55px"> </lord-icon>
                                        </div>


                                    </div>
                                    <h3 class="mb-2 text-center"><span class="counter-value text-muted text-center" data-target="97685">No Viewings Scheduled</span><small class="text-muted fs-13"></small></h3>
                                    <h6 class="text-muted mb-0"></h6>
                                </div>
                            </div>`;
            let meetingStatusChangePermission = "{{ auth()->user()->can('Lead Meeting Status Change') }}";

            $.ajax({
                url: "{{ route('admin.events.all') }}",
                type: 'POST',
                dataType: 'json',
                data: {
                    column: 'lead_id',
                    value: id,
                    type: 'Viewing'

                },
                success: function(result) {
                    $("#viewingLogs").html('');
                    if (result.status == '1') {
                        let html = '';
                        let status = '';
                        Object.keys(result.log).map((index) => {
                            if (result.log[index].status == 'PENDING') {
                                status = `Pending`;
                                statusOption = ``;
                            } else if (result.log[index].status == 'RUNNING') {
                                status = `Running`;
                            } else if (result.log[index].status == 'COMPLETED') {
                                status = `Completed`;
                            } else if (result.log[index].status == 'CANCELED') {
                                status = `Canceled`;
                            } else {
                                status = ``;
                            }
                            let statusDropDown = '';
                            if (meetingStatusChangePermission) {
                                statusDropDown = ` <button type="button" class="btn btn-sm btn-light dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-haspopup="false" aria-expanded="false">
                                                    </button>
                                                    <div class="dropdown-menu" data-id="${result.log[index].id}">
                                                        ${statusSelect}
                                                    </div>`;
                            }
                            let start_time = result.log[index].start_time ? moment(result.log[index]
                                .start_time).format("DD/MM/YYYY HH:mm A") : "";
                            let end_time = result.log[index].end_time ? moment(result.log[index]
                                .end_time).format("DD/MM/YYYY HH:mm A") : "";
                            html += `<div class="text-reset notification-item d-block dropdown-item">
                                        <div class="d-flex">
                                            <div class="flex-1" onclick="viewEvent('${result.log[index].id}')" data-id="${result.log[index].id}">
                                                <h6 class="mt-0 mb-1 fs-13 fw-semibold"> ${result.log[index].title || ""}</h6>
                                                <div class="fs-13 text-muted">
                                                    <p class="mb-1">${result.log[index].description || ""}</p>
                                                </div>
                                                <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                    <span><i class="mdi mdi-clock-outline"></i> ${start_time || ""} - ${end_time || ""}<i class="mdi mdi-google-maps"></i> ${result.log[index].location || ""}</span>
                                                </p>
                                            </div>
                                            <div class="px-2 fs-16">
                                                <div class="btn-group">
                                                    <button class="btn btn-light btn-sm" type="button">
                                                        ${status}
                                                    </button>
                                                   ${statusDropDown}
                                                </div>
                                            </div>
                                        </div>
                                    </div>`;
                        });
                        $("#viewingLogs").html(html);
                        let element = document.getElementById("viewingLogs");
                        element.scrollTop = element.scrollHeight;

                    } else {
                        $("#viewingLogs").html(alertMsg);

                    }
                },
                error: function(jqXhr, json, errorThrown) {
                    $("#meetingLogs").html(alertMsg);
                    let data = jqXhr.responseJSON;
                    if (data.errors) {
                        $.each(data.errors, function(index, item) {
                            showToastify(item[0], "error")
                        })
                    }
                    if (jqXhr.status == 500 || jqXhr.status == 400) {
                        showToastify(data.message, "error")
                    }
                },
            });
        }

        function getTask(id) {
            let statusSelect = "";
            @json(\App\Models\Task::STATUS_SELECT)?.
            map(item => {
                statusSelect +=
                    `<a class="dropdown-item change-task-status" href="javascript:void(0)" data-status="${item}">${item}</a>`;
            });
            let alertMsg = `<div class="card-animate">
                                <div class="card-body">
                                    <div class="d-flex mb-3 text-center">
                                        <div class="flex-grow-1">
                                            <lord-icon src="{{ asset('assets/json/icons/wired-outline-245-edit-document.json') }}" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:55px;height:55px"> </lord-icon>
                                        </div>


                                    </div>
                                    <h3 class="mb-2 text-center"><span class="counter-value text-muted text-center" data-target="97685">No Tasks</span><small class="text-muted fs-13"></small></h3>
                                    <h6 class="text-muted mb-0"></h6>
                                </div>
                            </div>`;

            let changeTaskStatusPermission = "{{ auth()->user()->can('Lead Task Status Change') }}";
            $.ajax({
                url: "{{ route('admin.events.all') }}",
                type: 'POST',
                dataType: 'json',
                data: {
                    column: 'lead_id',
                    value: id,
                    type: 'Task'
                },
                success: function(result) {
                    $("#taskLogs").html('');
                    if (result.status == '1') {
                        let html = '';
                        let priority = '';
                        let status = '';
                        Object.keys(result.log).map((index) => {
                            if (result.log[index].priority == 'high') {
                                priority =
                                    `<span class="badge bg-soft-danger text-danger align-middle">High</span>`;
                            } else if (result.log[index].priority == 'medium') {
                                priority =
                                    `<span class="badge bg-soft-warning text-warning align-middle">Medium</span>`;
                            } else {
                                priority =
                                    `<span class="badge bg-soft-success text-success align-middle">Low</span>`;
                            }
                            if (result.log[index].status == 'PENDING') {
                                status = `<span class="badge badge-soft-primary">Pending</span>`;
                            } else if (result.log[index].status == 'RUNNING') {
                                status = `<span class="badge badge-soft-secondary">Running</span>`;
                            } else if (result.log[index].status == 'COMPLETED') {
                                status = `<span class="badge badge-soft-success">Completed</span>`;
                            } else if (result.log[index].status == 'CANCELED') {
                                status = `<span class="badge badge-soft-danger">Canceled</span>`;
                            } else {
                                status = ``;
                            }
                            let statusDropDown = '';
                            if (changeTaskStatusPermission) {
                                statusDropDown = ` <button type="button" class="btn btn-sm btn-light dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-haspopup="false" aria-expanded="false">
                                                    </button>
                                                    <div class="dropdown-menu" data-id="${result.log[index].id}">
                                                        ${statusSelect}
                                                    </div>`;
                            }
                            let due_date = result.log[index].start_time != null ? moment(result.log[
                                index].start_time).format('DD/MM/YYYY hh:mm A') : '';
                            html += `<div class="text-reset notification-item d-block dropdown-item">
                                        <div class="d-flex">
                                            <div class="flex-1 " onclick="viewEvent('${result.log[index].id}')" data-id="${result.log[index].id}">
                                                <h6 class="mt-0 mb-1 fs-13 fw-semibold">${priority} ${result.log[index].title || ""}</h6>
                                                <div class="fs-13 text-muted">
                                                    <p class="mb-1">${result.log[index].description || ""}</p>
                                                </div>
                                                <p class="mb-0 fs-11 fw-medium text-muted">
                                                    <span><i class="mdi mdi-clock-outline"></i> Due by ${due_date || ""}</span>
                                                </p>
                                            </div>
                                            <div class="px-2 fs-16">
                                               <div class="btn-group">
                                                    <button class="btn btn-light btn-sm" type="button">
                                                        ${status}
                                                    </button>
                                                    ${statusDropDown}
                                                </div>
                                            </div>
                                        </div>
                                    </div>`;
                        });
                        $("#taskLogs").html(html);
                        let element = document.getElementById("taskLogs");
                        element.scrollTop = element.scrollHeight;
                    } else {
                        $("#taskLogs").html(alertMsg);

                    }
                },
                error: function(jqXhr, json, errorThrown) {
                    $("#taskLogs").html(alertMsg);
                    let data = jqXhr.responseJSON;
                    if (data.errors) {
                        $.each(data.errors, function(index, item) {
                            showToastify(item[0], "error")
                        })
                    }
                    if (jqXhr.status == 500 || jqXhr.status == 400) {
                        showToastify(data.message, "error")
                    }
                },
            });
        }

        // $("#for").change(function(){
        //     alert("changed");
        // })

        function getLead(id) {

            $("#call_now_button").removeClass("d-none");
            $("#start-call-button").removeClass("d-none");
            $("#whatapp-button").removeClass("d-none");
            $.ajax({
                url: "{{ route('admin.work.leads.get') }}",
                type: "get",
                data: {
                    id: id
                },
                success: function(result) {
                    // mode = "view";
                    // console.table(result.data.lead_status);
                    // console.log(result.data);
                    if (result.data?.property) {
                        let item = result.data?.property;
                        let option =
                            `<option value="${item.id}">${item.ref_no} - ${item.property_category?.title || ""} - ${item.unit_no || "s"} - ${item.location}</option>`;
                        $("#property_id").html(option);
                    }
                    $('.open_sub_status').removeClass('active');
                    $('.open_sub_status').find('span').addClass('randomClass');
                    $('.closed_sub_status').find('span').addClass('randomClass');

                    // console.log('Here')
                    if (result?.data?.lead_status?.badge) {
                        // console.log('There')

                        let active_badge_class = result.data.lead_status.badge;
                        active_badge_class = active_badge_class.replace('-bg', '-c');


                        $('.sub_' + result.data.lead_status_id).addClass(active_badge_class).parent().addClass(
                            'active');


                        $('.sub_' + result.data.lead_status_id).removeClass('randomClass');
                        // console.log('.sub_' + result.data.lead_status_id)
                    }


                    // console.table(result.data.lead_sub_status_id)
                    // if (result.data.lead_status_id == 1) {

                    //     $('.OpenStatus').removeClass('ribbon-soft-info');
                    //     $('.OpenStatus').addClass('ribbon-info');
                    // } else if (result.data.lead_status_id == 2) {
                    //     $('.ClassStatus').removeClass('ribbon-soft-primary');
                    //     $('.ClassStatus').addClass('ribbon-primary');
                    // }



                    // console.table(result);
                    if (result.status == '1') {
                        //$('#criteriaTabLoader').removeClass('d-none');
                        dataObj = result.data;


                        $('#lead_id').removeClass('d-none');
                        $('#ref_no').text(result.data.id);
                        $("#locations").val('');
                        $('#messageDiv').removeClass("d-none")
                        Object.keys(result.data).map((index) => {
                            $(`#${index}`).val(result.data[index]).trigger("change")
                        })
                        if (result.data?.for == 'Unspecified' && $('#is_admin').val() == 'false') {
                            $('#for').val("").trigger("change");
                        }

                        // console.table(result.data);
                        $('#id').val(result.data.id);
                        $('#lead_sub_status_id').val(result.data.lead_sub_status_id);
                        $('#lead_status_id').val(result.data.lead_status_id);
                        if (result.data.lead_status_id == 12 || result.data.lead_status_id == 9) {
                            // $('#editBtn').addClass('d-none');
                            if (result.data.lead_status_id == 12) {
                                $('#reasonDiv').removeClass('d-none');
                                $('#unsuccessfull_reason').html(result.data.reason);
                            }
                        }else{
                                $('#reasonDiv').addClass('d-none');
                                $('#unsuccessfull_reason').html('');
                            }
                        if (result.data.first_name == "New") {
                            $('#first_name').val('');
                        }
                        if (result.data.type_id == 11) {
                            $("#type_id").val('').trigger("change");
                            //$("#type_id").trigger("change");
                        }
                        if (result.data.source_id == 50) {
                            $("#source_id").val('').trigger("change");
                            //$("#source_id").trigger("change");
                        }
                        // alert()
                        renderCreatedUpdated(result.data)



                        if (result.data?.criteria != null) {
                            toggleCirteriaForm(false);
                            $("#criteriaForm").trigger("reset");
                            $("#bitrix_data_note").html(result.data?.criteria?.bitrix_data);
                            Object.keys(result.data?.criteria).map((index) => {
                                if (index != "id") {
                                    $(`#${index}`).val(result.data?.criteria[index]).trigger("change")
                                }
                                $('#criteria_id').val(result.data?.criteria?.id);

                                var jsonString = result.data.criteria.view;
                                var jsonArray = JSON.parse(jsonString);

                                    // Set the value of the 'view' element with the first value from the JSON array
                                    $('#view').val(jsonArray).trigger('change');


                                if (index == "communities") {

                                    let communities = result.data?.criteria['communities'];

                                    let text
                                    $.each(communities, function(key, value) {
                                        text += value + " ";
                                    });
                                    let locations = result.data?.criteria?.locations;
                                    let subCom = [];
                                    let locationTypes = [];
                                    let cmb = '';

                                    function changeSubCommunites(data) {
                                        $('#sub_communities').val(data).trigger('change');
                                        toggleCirteriaForm(true)
                                    };

                                    if (jQuery.isEmptyObject(locations)) {
                                        toggleCirteriaForm(true)
                                    }

                                    $.each(locations, function(key, value) {
                                        if (value.parentable_type == 'App\\Models\\Community') {

                                            $('#communities').find("option[value='" + value
                                                .parentable_id + "']").remove();
                                            var newOption = new Option(value?.parentable
                                                ?.path_name, value.parentable_id, true, true
                                            );
                                            $('#communities').append(newOption).trigger(
                                                'change');
                                        }
                                        if (value.parentable_type ==
                                            'App\\Models\\SubCommunity') {
                                            let cmbid = value.parentable_id + ':sub_community';
                                            if ($('#sub_communities').find("option[value='" +
                                                    cmbid + "']").length) {
                                                $('#sub_communities').val(value.parentable_id)
                                                    .trigger('change');
                                            } else {
                                                var newOption = new Option(value?.parentable
                                                    ?.path_name, cmbid, true, true);
                                                $('#sub_communities').append(newOption).trigger(
                                                    'change');
                                            }

                                            subCom.push(cmbid);
                                            //  $("#sub_communities").append('<option data-type="sub_community" value="'+value.parentable_id+'" selected="true">'+value?.parentable?.path_name+'</option>');

                                        }
                                        if (value.parentable_type ==
                                            'App\\Models\\CommunityProperty') {
                                            let cmbid = value.parentable_id +
                                                ':community_property';
                                            if ($('#sub_communities').find("option[value='" +
                                                    cmbid + "']").length) {
                                                $('#sub_communities').val(cmbid).trigger(
                                                    'change');
                                            } else {
                                                var newOption = new Option(value?.parentable
                                                    ?.path_name, cmbid, true, true);
                                                $('#sub_communities').append(newOption).trigger(
                                                    'change');
                                            }


                                            //  $("#sub_communities").append('<option data-type="community_property" value="'+value.parentable_id+'" selected="true">'+value?.parentable?.path_name+'</option>');
                                            subCom.push(cmbid);
                                        }


                                        setTimeout(function() {

                                                changeSubCommunites(subCom)
                                            },
                                            3000
                                        ); //selecting sub-community after 3 second due to ajax call

                                    });

                                }
                            });
                            // $("#min_price").val(result.data?.criteria?.price).trigger("change");

                            // if (!$('#min_price').find("option[value='" + result.data?.criteria?.min_price +
                            //         "']").length) {
                            //     var newOption = new Option(result.data?.criteria?.min_price, result.data
                            //         ?.criteria?.min_price, true, true);
                            //     $('#min_price').append(newOption).trigger('change');

                            // }



                            $("#max_price").val(result.data?.criteria?.max_price).trigger("change");

                            // if (!$('#max_price').find("option[value='" + result.data?.criteria?.max_price +
                            //         "']").length) {
                            //     var newOption = new Option(result.data?.criteria?.max_price, result.data
                            //         ?.criteria?.max_price, true, true);

                            //     $('#max_price').append(newOption).trigger('change');
                            // }


                            setTimeout(function() {
                                toggleCirteriaForm(true)
                            }, 3000);
                        } else {
                            $("#criteriaForm").trigger("reset");
                            toggleCirteriaForm(true)
                        }
                        toggleForm(true)
                        if (dataObj.lead_status_id == 2 && dataObj.lead_sub_status_id == 2) {
                            $("#editBtn").addClass("d-none")
                            $('#editBtn').attr('disabled', true);
                        } else {
                            // $("#editBtn").removeClass("d-none")
                            $('#editBtn').attr('disabled', false);
                        }
                        var url = "{{ route('admin.work.leads.index') }}" + "?id=" + result.data.id;
                        window.history.pushState('data', '', url);

                        getLeadNotes()
                        topFunction()
                        viewAgentList(id)
                        //getCallLog(id)
                        //  getMeetingLog(id)
                        // getViewingLog(id)
                        //  getTask(id)
                        getActivityLog(id)
                        getResponseTime(id)
                        const widgetObj = {
                            id: id,
                            model_key: 'lead_id',
                            call_log: 1,
                            meeting_log: 1,
                            task_log: 0,
                            followup_log: 1,
                            viewing_log: 1,

                        };
                        getEventsCard(widgetObj);

                        if ((call_now_button == 1 || call_now_button == 3) && result.data?.agent_id ==
                            globalUser.id) {
                            $("#call_now_button").removeClass("d-none");
                        }
                        $('#lead_sub_status_id').val(result.data.lead_sub_status_id);
                        $('#lead_status_id').val(result.data.lead_status_id);

                        // if (result.data.lead_sub_status_id == 8) {
                        //     if ($('.sub_8').html() == 'Unspecified') {

                        //     } else {
                        //         $('#lead_sub_status_id').val('0');
                        //         $('#lead_status_id').val('');
                        //     }
                        // }

                        $('#id').val(result.data.id);
                        if ($('#whatsapp').val() == '') {
                            $('#whatsappDiv1').addClass('col-md-4')
                            $('#whatsappDiv2').hide()
                            $('#whatsappDiv1').removeClass('col-md-3 pr-0')
                        } else {
                            $('#whatsappDiv1').addClass('col-md-3 pr-0')
                            $('#whatsappDiv2').show()
                            $('#whatsappDiv1').removeClass('col-md-4')
                        }
                    }

                }
            });
        }

        function viewData(id) {
            @can('Leads View')

                $(".inputsCriteria").val("").trigger("change")
                toggleCirteriaForm(false);
                $("#addOnAgentDiv, #newBtn, #closeBtn, #followUpMenusTab, .addHide,#dropdownMenuLink1").removeClass(
                    "d-none");
                $("#saveBtn").addClass("d-none");
                $("#collapseContactForm").addClass("show");
                $("#editBtn, #bulkAssignBtn").removeClass("d-none");
                $("#newBtn").addClass("d-none");

                $('#whatsappDiv1').addClass('col-md-3 pr-0')
                $('#whatsappDiv2').show()
                $('#whatsappDiv1').removeClass('col-md-4')

                $('#mobileDiv1').addClass('col-md-3 pr-0')
                $('#call_now_button').show()
                $('#mobileDiv1').removeClass('col-md-4')

                $(".inputs").prop("disabled", true);
                $(".inputs").prop("readonly", false);
                $("#mainForm").trigger("reset")
                $("#id, #criteria_lead_id").val(id)
                $(".inputs").val("").trigger("change")
                $(".inputs").removeClass("is-invalid")
                $("#meeting_lead_id, #task_lead_id").val(id)
                isUpdate = true; //set isUpdate to true
                getLead(id)
                mode == 'view'
                if (mode === 'view') {
                    $('#makeItDisable').addClass('non-clickable');
                } else {
                    $('#makeItDisable').removeClass('non-clickable');
                }

                $("#col-logs").removeClass("d-none")
                $("#start-call-button").removeClass("d-none");
                $("#calling-button").addClass("d-none");
            @endcan
        }


        $(document).on('click', '#editBtn', function(event) {
            @can('Leads Edit')

                editData($('#id').val());
                mode == 'edit';
                $('#editBtn').addClass('d-none');
                $('#message').attr('readonly', true);
            @endcan
        });

        function editData(id) {
            @can('Leads Edit')
                isUpdate = true;
                $("#col-logs").addClass("d-none")
                $(".inputs").prop("disabled", false)
                @can('Lead status change')
                    $("#lead_status_id").prop("disabled", false);
                @else
                    $("#lead_status_id").prop("disabled", true);
                @endcan
                @can('Lead sub status change')
                    $("#lead_sub_status_id").prop("disabled", false);
                @else
                    $("#lead_sub_status_id").prop("disabled", true);
                @endcan
                $(".inputs").removeClass("is-invalid")
                $('#messageDiv').removeClass("d-none")
                $('#mainForm').removeClass('was-validated');
                $("#saveBtn, #closeBtn").removeClass("d-none")
                $("#editBtn, #newBtn, #deleteBtn, #bulkAssignBtn, #dropdownMenuLink1").addClass("d-none")
                getLead(id);
                mode = 'edit';
                if (mode === 'view') {
                    $('#makeItDisable').addClass('non-clickable');
                } else {
                    $('#makeItDisable').removeClass('non-clickable');
                }
                $('#editBtn').addClass('d-none');
            @endcan
        }

        $(document).on('click', '#archiveSingleLead', function(event) {
            // alert($('#id').val())
            $("input[name='chk_child']").each(function() {
                $(this).prop('checked', false);
            });

            $("input[name='chk_child']").each(function() {
                if ($(this).val() == $('#id').val()) {
                    $(this).prop('checked', true);
                    $('#archiveModal').modal('show');
                }
            });
        });

        function deleteData(id) {
            @can('Leads Delete')
                Swal.fire({

                        html: '<div class="mt-3"><lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon><div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you Sure ?</h4><p class="text-muted mx-4 mb-0">Are you Sure You want to Delete this Lead ?</p></div></div>',
                        showCancelButton: !0,
                        confirmButtonClass: "btn btn-danger w-xs me-2 mb-1",
                        confirmButtonText: "Yes, Delete It!",
                        cancelButtonClass: "btn btn-soft-success w-xs mb-1",
                        buttonsStyling: !1,
                        showCloseButton: !0


                    })
                    .then(function(t) {

                        if (t.isConfirmed) { // on confirm delete the category
                            $.ajax({
                                url: "{{ route('admin.work.leads.delete') }}",
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    id
                                },
                                success: function(result) {
                                    if (result.status == '1') { //if successfully deleted
                                        showToastify("Lead has been deleted successfully",
                                            "success")
                                        dataTable.row()
                                            .draw(); // redraw the datatable show we don't need to reload the page to see the changes
                                        $('#viewModal').modal('hide');
                                        toggleCirteriaForm(false);
                                        $("#addOnAgentDiv, #newBtn, #closeBtn, #followUpMenusTab, .addHide,#dropdownMenuLink1,#saveBtn")
                                            .addClass(
                                                "d-none");
                                        $("#collapseContactForm").removeClass("show");
                                        $("#editBtn, #bulkAssignBtn").addClass("d-none");
                                        $("#newBtn").removeClass("d-none");

                                        $(".inputs").prop("disabled", false);
                                        $(".inputs").prop("readonly", true);
                                        $("#mainForm").trigger("reset")
                                        $(".inputs").val("").trigger("change")
                                        $(".inputs").removeClass("is-invalid")

                                        dataTable.ajax.reload();
                                    } else { // something went wrong on backend
                                        showToastify(result.msg, "error")
                                    }
                                },
                                error: function(result) {
                                    console.log(result);
                                }
                            });
                        }
                    })
            @endcan
        }

        function deleteMultiple() {
            @can('Bulk Delete Leads')
                if (ids_array = [], document.getElementsByName("chk_child").forEach((function(e) {
                        if (1 == e.checked) {
                            var t = e.value;
                            ids_array.push(t)

                        }
                    })), "undefined" != typeof ids_array && ids_array.length > 0) {
                    var content = document.createElement('div');
                    content.innerHTML = "You are about to delete <b>" + ids_array.length +
                        "</b> leads. Once deleted, you will not be able to use them!";
                    Swal.fire({

                            html: '<div class="mt-3"><lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon><div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you Sure ?</h4><p class="text-muted mx-4 mb-0">Are you Sure You want to Delete ' +
                                ids_array.length + ' Lead(s)?</p></div></div>',
                            showCancelButton: !0,
                            confirmButtonClass: "btn btn-danger w-xs me-2 mb-1",
                            confirmButtonText: "Yes, Delete " + ids_array.length + " Lead(s).",
                            cancelButtonClass: "btn btn-soft-success w-xs mb-1",
                            buttonsStyling: !1,
                            showCloseButton: !0


                        })
                        .then(function(t) {

                            if (t.isConfirmed) { // on confirm delete the category
                                $.ajax({
                                    url: "{{ route('admin.work.leads.delete.multi') }}",
                                    type: 'POST',
                                    dataType: 'json',
                                    data: {
                                        ids: ids_array
                                    },
                                    success: function(result) {
                                        if (result.status == '1') {
                                            showToastify(result.msg, "success")
                                            $('.multi_options').remove();
                                            dataTable.row().draw();
                                        } else {
                                            showToastify(result.msg, "error")
                                        }
                                    },
                                    error: function(result) {
                                        console.log(result);
                                    }
                                });
                            }
                        })

                } else {
                    showToastify("Please select at least one checkbox", "error")
                }
            @endcan
        }
        $("#checkAll").change(function() {
            $(".selectable-checkbox").prop("checked", this.checked)
        });

        function locationSelect2() {

            $('#communities').select2({
                placeholder: 'Community',
                allowClear: true,
                closeOnSelect: false,
                ajax: {
                    url: '{{ route('ajax.community.select2') }}',
                    dataType: 'json',
                    type: 'GET',
                    processResults({
                        data
                    }) {

                        return {
                            results: $.map(data, function(item) {
                                return {
                                    text: `${item.path_name}`,
                                    id: item.id

                                }
                            })
                        }
                    }
                }
            });

            $('#sub_communities').select2({
                placeholder: 'Tower, community, locality',
                allowClear: true,
                closeOnSelect: false,
                ajax: {
                    url: '{{ route('ajax.sub-community.property.select2') }}',
                    dataType: 'json',
                    data: function(term, page) {
                        return {
                            q: term, // search term
                            communities: $("#communities").val(),
                            page_limit: 10
                        };
                    },
                    processResults({
                        data
                    }) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    text: `${item.text}`,
                                    id: item.id + ':' + item.type,
                                    'lat': item.lat,
                                    'long': item.long,
                                    'path': item.path,
                                    'type': item.type
                                }
                            })
                        }
                    }
                }
            });
        }

        locationSelect2();
        const locations = [];
        const comProp = [];
        $('#filter_sub_communities').on('select2:select', function(e) {
            $("#location_address").html("")
            $("#location_type").val("")
            var data = e.params.data;

            let lat = data.lat;
            let long = data.long;
            let path = data.path;
            let type = data.type;
            let id = data.id;
            let cmb = data.id + ':' + data.type;
            locations.push(cmb);

            $("#locations").val(locations);



        });

        $('#filter_sub_communities').on("select2:unselect", function(e) {
            var data = e.params.data;
            let type = data.type;
            let id = data.id;
            let cmb = data.id + ':' + data.type;
            const index = locations.indexOf(cmb);
            if (index > -1) { // only splice array when item is found
                locations.splice(index, 1); // 2nd parameter means remove one item only
            }

            $("#filter_locations").val(locations);


        });







        function filter_locationSelect2() {
            $("#filter_location_address").html("")
            $("#filter_location_type").val("")
            $('#filter_communities').select2({
                placeholder: 'Community',
                allowClear: true,
                closeOnSelect: false,
                ajax: {
                    url: '{{ route('ajax.community.select2') }}',
                    dataType: 'json',
                    type: 'GET',
                    processResults({
                        data
                    }) {

                        return {
                            results: $.map(data, function(item) {
                                return {
                                    text: `${item.path_name}`,
                                    id: item.id

                                }
                            })
                        }
                    }
                }
            });

            $('#filter_sub_communities').select2({
                placeholder: 'Tower, community, locality',
                allowClear: true,
                closeOnSelect: false,
                ajax: {
                    url: '{{ route('ajax.sub-community.property.select2') }}',
                    dataType: 'json',
                    data: function(term, page) {
                        return {
                            q: term, // search term
                            communities: $("#filter_communities").val(),
                            page_limit: 10
                        };
                    },
                    processResults({
                        data
                    }) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    text: `${item.text}`,
                                    id: item.id,
                                    'lat': item.lat,
                                    'long': item.long,
                                    'path': item.path,
                                    'type': item.type
                                }
                            })
                        }
                    }
                }
            });
        }

        filter_locationSelect2();
        const filter_locations = [];
        $('#filter_sub_communities').on('select2:select', function(e) {
            $("#filter_location_address").html("")
            $("#filter_location_type").val("")
            var data = e.params.data;
            // console.log(data.long);
            let lat = data.lat;
            let long = data.long;
            let path = data.path;
            let type = data.type;
            let id = data.id;
            let cmb = data.id + ':' + data.type;
            filter_locations.push(cmb);
            $("#filter_locations").val(filter_locations);
        });

        $('#filter_sub_communities').on("select2:unselect", function(e) {
            var data = e.params.data;
            let type = data.type;
            let id = data.id;
            let cmb = data.id + ':' + data.type;
            const index = filter_locations.indexOf(cmb);
            if (index > -1) { // only splice array when item is found
                filter_locations.splice(index, 1); // 2nd parameter means remove one item only
            }
            $("#filter_locations").val(filter_locations);

        });

        $(document).on("select2:open", () => {
            document.querySelector(".select2-container--open .select2-search__field").focus()
        })

        // $("#sidebarWork").addClass('collapse show');
        $("#sidebarLeads").addClass('active');
        dataTable.on('draw.dt', function() {
            $(".chk_child").prop("checked", false);
            $('.multi_options').remove();
            $('#checkAll').prop("checked", false);
        });
        $('#sidebarLeads').addClass('active')


        $('#property_ref').select2({
            placeholder: 'Search',
            minimumInputLength: 0, // Allow empty search terms
            ajax: {
                url: "{{ route('admin.work.leads.autocomplete') }}",
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: '[' + item.ref_no + '] ' + (item.title ?? "No Name"),
                                id: item.id
                            }
                        })
                    };
                },
                cache: true
            }
        });

        $('#property_ref').change(function() {

        })

        $('.badge-nav .nav-link').on('click', function(e) {
            e.preventDefault();
            $('.rmActive').removeClass('active');
            $(this).parent().removeClass('active');
        });


        $(document).ready(function() {
            $('.open_sub_status').click(function() {
                if (mode == 'edit' || mode == 'create') {

                    $('.OpenStatus').removeClass('ribbon-soft-info');
                    $('.OpenStatus').addClass('ribbon-info');
                    $('.ClassStatus').addClass('ribbon-soft-primary');
                    $('.ClassStatus').removeClass('ribbon-primary');

                    $('#lead_status_id').val($(this).data('status_id'));
                    // $('#lead_sub_status_id').val($(this).data('sub_id'));
                    // console.log($('#lead_status_id').val())
                    $('.rmDark').addClass('randomClass');
                    let s_id = $(this).data('status_id');
                    $('.sub_' + $(this).data('status_id')).addClass(`color-c-${s_id}`);
                    //$('.sub_'+$(this).data('status_id')).css("");
                    //.css('border-left-color', "red");


                    $(this).find('span').removeClass('randomClass');

                    if ($(this).data('status_name') == 'New' || $(this).data('status_name') ==
                        'Unsuccessful') {
                        $('#follow_up_date').attr('required', false);
                        $('#follow_up_label').html('Follow Up')
                    } else {
                        $('#follow_up_date').attr('required', true);
                        $('#follow_up_label').html('Follow Up *')
                        $('#follow_up_date').attr('readonly', false);

                    }
                }
            });




        })

        let statusDiv = `<div id="table_status_filter_div">
    <ul class="nav nav-tabs nav-tabs-custom nav-success mb-3 " role="tablist">
        <li class="nav-item  font-12 " role="presentation">
            <a class="nav-link All  statusFilterClear active" data-bs-toggle="tab" id="All" href="#" role="tab" aria-selected="false" tabindex="-1">
                All
            </a>
        </li>

        @foreach ($statuses ?? [] as $status)
        <li class="nav-item font-12" role="presentation">
            <a class="nav-link All  statusFilter" data-bs-toggle="tab" id="status_id_{{ $status->id }}" href="javascript:void(0)" data-status="{{ $status->id }}" role="tab" aria-selected="false" tabindex="-1">
                {{ $status->name }}
            </a>
        </li>
        @endforeach

    </ul>


</div>`;


        // $(statusDiv).insertAfter(".dt-buttons")

        $(document).on("click", ".statusFilter", function() {
            let status = $(this).data("status");

            $("#search_lead_statuses option:selected").removeAttr("selected");
            $("#search_lead_statuses option[value='" + status + "']").attr("selected", true);
            // Trigger the change event to update Select2
            $("#search_lead_statuses").trigger("change");
            // Trigger the click event on the search button
            $("#searchSearchBtn").trigger("click");
        });

        $(document).on("click", ".statusFilterClear", function() {
            $("#search_lead_statuses option:selected").removeAttr("selected").trigger("change");
            $("#searchSearchBtn").trigger("click");
        });

        function normalizePhoneNumber(phoneNumber) {
            // Remove all non-digit characters
            const digitsOnly = phoneNumber.replace(/\D/g, '');

            // Check if the number starts with '0' or '+'
            if (digitsOnly.startsWith('0')) {
                // Remove leading '0' and add the country code prefix
                return '+countryCode' + digitsOnly.substring(1);
            } else if (digitsOnly.startsWith('+')) {
                return '+' + digitsOnly;
            } else {
                // Assuming the number has no prefix, add the country code prefix
                return '+countryCode' + digitsOnly;
            }
        }

        function validatePhoneNumber(phoneNumber) {
            // Regular expression pattern to validate the phone number format
            const pattern = /^(\+|0)[0-9]+$/;

            // Normalize the phone number
            const normalizedNumber = normalizePhoneNumber(phoneNumber);

            // Check if the normalized number matches the pattern
            return pattern.test(normalizedNumber);
        }

        function validatePhoneOnChange() {
            const phoneNumberInput = document.getElementById('phone');
            const phoneNumber = phoneNumberInput.value;

            const isValidPhoneNumber = validatePhoneNumber(phoneNumber);

            if (isValidPhoneNumber) {
                console.log('Valid phone number:', phoneNumber);
            } else {
                console.log('Invalid phone number:', phoneNumber);
            }
        }
        $('#property_id').change(function() {

            let id = $('#property_id').val();
            if (id != null && mode != "view") {
                // alert('here')
                // console.log('Here2')
                // console.log(id)

                // console.log(id)
                $.ajax({
                    url: "{{ route('ajax.PropertyCriteria') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        id: id,
                    },

                    success: function(result) {
                        console.log('SUCCESS')

                        if ($('#furnished').val() === null || mode != 'view') {
                            $('#furnished').val(result.furnished).trigger('change');
                        }

                        if ($('#for').val().length == 0 || mode != 'view') {
                            $('#for').val(result.for).trigger('change');
                        }
                        // check if already not selected then add

                        // $('#min_bed_id').val(result.bed_id).trigger('change');
                        if ($('#min_bed_id').val() === null || mode != 'view') {
                            $('#min_bed_id').val(result.bed_id).trigger('change');
                            $('#max_bed_id').val(result.bed_id).trigger('change');
                        }
                        // ---------------

                        // ---------------
                        // alert($('#min_bath_id').val());
                        if ($('#min_bath_id').val() === null || mode != 'view') {
                            $('#min_bath_id').val(result.bath_id).trigger('change');
                            $('#max_bath_id').val(result.bath_id).trigger('change');
                        }
                        // ---------------
                        if ($('#communities').val().length == 0 || mode != 'view') {
                            // console.log('Inside')

                            if (!$('#communities').find("option[value='" + result.community_id + "']")
                                .length) {
                                $('#communities').val(null).trigger('change');
                                var newOption = new Option(result.community, result.community_id, true,
                                    true);
                                $('#communities').append(newOption).trigger('change');
                            } else {
                                $('#communities').append(result.community_id).trigger('change');
                            }

                        }
                        if ($('#sub_communities').val().length == 0 || mode != 'view') {
                            if (!$('#sub_communities').find("option[value='" + result.subCommunity_id +
                                    "']").length) {
                                $('#sub_communities').val(null).trigger('change');
                                var newOption = new Option(result.subCommunity, result.subCommunity_id,
                                    true, true);
                                $('#sub_communities').append(newOption).trigger('change');
                            } else {
                                $('#sub_communities').append(result.subCommunity_id).trigger('change');
                            }
                        }
                        // ---------------


                        // if ($('#view').val().length == 0 || mode != 'view') {
                        //     $('#view').val(result.view).trigger('change');
                        // }

                        var jsonString = result.view;
                        var jsonArray = JSON.parse(jsonString);

                        if ($('#view').val().length === 0 || mode !== 'view') {
                            // Set the value of the 'view' element with the first value from the JSON array
                            $('#view').val(jsonArray).trigger('change');
                        }

                        // ---------------
                        if ($('#min_price').val() == '' || mode == 'create') {
                            $('#min_price').val(result.minPrice).trigger('change');
                            $('#max_price').val(result.maxPrice).trigger('change');
                        }
                        if ($('#max_price').val() == '' || mode == 'create') {
                            $('#max_price').val(result.maxPrice).trigger('change');
                        }
                        // if ($('#min_price').val() === null) {
                        //     if (!$('#min_price').find("option[value='" + result.price + "']").length) {
                        //         var newOption = new Option(result.price, result.price, true, true);
                        //         $('#min_price').append(newOption).trigger('change');
                        //     } else {
                        //         $('#min_price').append(result.price).trigger('change');
                        //     }
                        // }

                        // ---------------
                    },
                    error: function(jqXhr, json, errorThrown) {
                        console.log(json)
                    },
                });
            }
        })
    </script>



    <script src="{{ URL::asset('/assets/js/pages/leads.js') }}"></script>
    @yield('pagejs')
@endsection
