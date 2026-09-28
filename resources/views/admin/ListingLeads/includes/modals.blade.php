<div id="generalModal" class="modal zoomIn" tabindex="-1" aria-labelledby="generalModalLabel" aria-hidden="true"
    style="display: none;">
    <div class="modal-dialog modal-md">
        <form id="generalModalForm" class="modal-content needs-validation" action="javascript:void(0)">
            <div class="modal-header bg-soft-danger">
                <h5 class="modal-title" id="generalModalLabel">Assign <span id="bulk_leads_count"></span>
                    Lead(s)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                {{-- <div class="row" id="telesales_agent">
                    <div class="col-4">Select Action*</div>
                    <div class="col-8">
                        <select name='' id="selectAction" class="form-control select2">
                            <option value="" disabled selected>Select Option</option>
                            @can('Lead Assign Agent')
                                <option value="Re-Assign" selected>Assign Agent</option>
                            @endcan


                        </select>
                    </div>
                </div> --}}

                <div class="row mt-2" id="agent_dropdown">
                    @can('Listing Leads Assign Agent')
                        @include('admin.templates._multi_option_dropdown', [
                            'id' => 'agents_dropdown',
                            'label' => 'Select Agent',
                            'agents' => $agents ?? [],
                        ])

                        {{-- <div class="col-4">Select Agents*</div>
                        <div class="col-8">
                            <select name='agents_dropdown' id="agents_dropdown" class="form-control select2">
                                <option value="" selected disabled>Select Agent</option>

                                @foreach ($agents ?? [] as $agent)
                                    <option value="{{ $agent->id }}">{{ $agent->full_name }}</option>
                                @endforeach
                            </select>
                        </div> --}}
                    @endcan
                </div>



                <div class="row d-none mt-2" id="telesales_dropdown">
                    @can('Bulk Telesales Assignments')
                        @include('admin.templates._agents_dynamic_select2', [
                            'id' => 'bulk_leads_manager',
                            'label' => 'Select Manager',
                            'agents' => $managers ?? [],
                        ])


                        {{-- <div class="col-4">Select Manager*</div>
                        <div class="col-8">
                            <select name='bulk_leads_manager' id="bulk_leads_manager" class="form-control select2">
                                @foreach ($managers ?? [] as $manager)
                                    <option value="{{ $manager->id }}">{{ $manager->first_name }} {{ $manager->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div> --}}
                    @endcan
                </div>

                <div class="row mt-2">
                    <div class="col-4">Notifications</div>
                    <div class="col-lg-8">
                        <div class="mt-4 mt-lg-0">
                            <div class="form-check form-switch form-check-inline d-none" dir="ltr">
                                <input type="checkbox" class="form-check-input email_notification"
                                    id="email_notification" name="email_notification">
                                <label class="form-check-label" for="email_notification">Email</label>
                            </div>
                            <div class="form-check form-switch form-check-inline d-none" dir="ltr">
                                <input type="checkbox" class="form-check-input system_notification"
                                    id="system_notification" name="system_notification">
                                <label class="form-check-label" for="system_notification">System</label>
                            </div>
                            <div class="form-check form-switch form-check-inline" dir="ltr">
                                <input type="checkbox" class="form-check-input telegram_notification"
                                    id="telegram_notification" checked name="telegram_notification">
                                <label class="form-check-label" for="telegram_notification">Telegram</label>
                            </div>
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
                            <option value="Duplicate">Duplicate</option>
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










<div id="addContactModal" class="modal zoomIn" tabindex="-1" aria-labelledby="addOwnerModalLabel"
    aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-soft-danger">
                <h5 class="modal-title" id="addContactModalLabel">Create a Listing</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body ">
                <div class="row">


                    <div class="col-xl-12">
                        <div class="">

                            <div class="card-body">



                                <div class="step-arrow-nav mb-4 non-clickable">

                                    <ul class="nav nav-pills custom-nav nav-justified" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="steparrow-gen-info-tab"
                                                data-bs-toggle="pill" data-bs-target="#steparrow-gen-info"
                                                type="button" role="tab" aria-controls="steparrow-gen-info"
                                                aria-selected="true">Owner Details</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link " id="steparrow-description-info-tab"
                                                data-bs-toggle="pill" data-bs-target="#steparrow-description-info"
                                                type="button" role="tab"
                                                aria-controls="steparrow-description-info"
                                                aria-selected="false">Listing Details</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="pills-experience-tab" data-bs-toggle="pill"
                                                data-bs-target="#pills-experience" type="button" role="tab"
                                                aria-controls="pills-experience" aria-selected="false">Finish</button>
                                        </li>
                                    </ul>
                                </div>
                                <form action="{{ route('listings.leads.convertToListing') }}" method="POST"
                                    class=" form-steps needs-validation" novalidate autocomplete="off">
                                    @csrf
                                    <div class="tab-content">
                                        <div class="tab-pane fade" id="steparrow-gen-info" role="tabpanel"
                                            aria-labelledby="steparrow-gen-info-tab">

                                            <div>
                                                <div class="row">
                                                    <input type="hidden" name="listing_lead_id"
                                                        id="listing_lead_id">
                                                    <div class="col-lg-6">
                                                        <div class="mb-3">
                                                            <label class="form-label"
                                                                for="listing_agent_id">Agent*</label>
                                                            <select class="form-control select2 contact_req"
                                                                name="agent_id" id="listing_agent_id" required>
                                                                @foreach ($agents ?? [] as $src)
                                                                    <option value="{{ $src->id }}">
                                                                        {{ $src->first_name }} {{ $src->last_name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            <div class="invalid-feedback">Please enter Agent</div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6">
                                                        <div class="mb-3">
                                                            <label class="form-label" for="listing_first_name">First
                                                                Name*</label>
                                                            <input type="text" class="form-control contact_req"
                                                                id="listing_first_name" placeholder="Enter First name"
                                                                required name="first_name">
                                                            <div class="invalid-feedback">Please enter First name</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="mb-3">
                                                            <label class="form-label" for="listing_last_name">Last
                                                                Name</label>
                                                            <input type="text" class="form-control"
                                                                id="listing_last_name" placeholder="Enter Last name"
                                                                name="last_name">
                                                            <div class="invalid-feedback">Please enter Last name</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="mb-3">
                                                            <label class="form-label"
                                                                for="listing_email">Email*</label>
                                                            <input type="email" class="form-control contact_req"
                                                                id="listing_email" placeholder="Enter email" required
                                                                name="email">
                                                            <div class="invalid-feedback">Please enter an email address
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="mb-3">
                                                            <label class="form-label"
                                                                for="listing_mobile">Mobile*</label>
                                                            <input type="mobile" class="form-control contact_req"
                                                                id="listing_mobile" placeholder="Enter mobile"
                                                                required name="mobile">
                                                            <div class="invalid-feedback">Please enter an mobile
                                                                address</div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="d-flex align-items-start gap-3 mt-4">
                                                <button type="button"
                                                    class="btn btn-label right ms-auto ownerInfo checkDuplicate btn-primary"
                                                    data-nexttab="steparrow-description-info-tab"><i
                                                        class="ri-arrow-right-line label-icon align-middle fs-16 ms-2 btn-"></i>Next</button>
                                            </div>
                                        </div>
                                        <!-- end tab pane -->

                                        <div class="tab-pane fade show active" id="steparrow-description-info"
                                            role="tabpanel" aria-labelledby="steparrow-description-info-tab">
                                            {{-- <input type="hidden" name="owner_id" id="listing_owner_id">
                                            <input type="hidden" name="listing_lead_id" id="listing_lead_id_2"> --}}
                                            {{-- <input type="hidden" name="agent_id" id="listing_agent_id"> --}}

                                            <div>
                                                <div>

                                                    <div class="row">


                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label class="form-label" for="listing_unit_no">Unit
                                                                    No*</label>
                                                                <input type="text"
                                                                    class="form-control property_req checkDuplicate"
                                                                    id="listing_unit_no" placeholder="Enter Unit No"
                                                                    required name="unit_no">
                                                                <div class="invalid-feedback">Please enter Unit No
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label class="form-label"
                                                                    for="listing_for">Purpose*</label>
                                                                <select name='for' id="listing_for"
                                                                    class="form-control select2 property_req checkDuplicate"
                                                                    required>
                                                                    @foreach (\App\Models\Lead::FOR_SELECT ?? [] as $key => $value)
                                                                        @if ($key == 'Unspecified')
                                                                        @else
                                                                            <option value="{{ $key }}">
                                                                                {{ $value }}
                                                                            </option>
                                                                        @endif
                                                                    @endforeach
                                                                </select>
                                                                <div class="invalid-feedback">Please enter Purpose
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label class="form-label"
                                                                    for="property_category_id">Property Type*</label>
                                                                <select class="form-control select2 property_req"
                                                                    id="listing_property_category_id"
                                                                    name="property_category_id" required>
                                                                    @foreach ($categories ?? [] as $cat)
                                                                        <option value="{{ $cat->id }}">
                                                                            {{ $cat->title }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <div class="invalid-feedback">Please enter Property
                                                                    Type</div>
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label class="form-label"
                                                                    for="listing_for">Location</label>
                                                                <input type="hidden" name="listing_location_type"
                                                                    id="listing_location_type"
                                                                    class="location_type checkDuplicate">
                                                                <select
                                                                    class="form-control select2 location_id location_id2 checkDuplicate"
                                                                    id="listing_location_id"
                                                                    name="listing_location_id">
                                                                    <option value=""></option>
                                                                </select>
                                                                <div id="listing_location_address"
                                                                    class="location_address">
                                                                </div>
                                                                <div class="invalid-feedback">Please enter Location
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label class="form-label"
                                                                    for="listing_bed_id">Bed*</label>
                                                                <select class="form-control select2 property_req"
                                                                    name="bed_id" id="listing_bed_id" required>
                                                                    @foreach ($beds ?? [] as $bed)
                                                                        <option value="{{ $bed->id }}">
                                                                            {{ $bed->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <div class="invalid-feedback">Please enter Bed</div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label class="form-label"
                                                                    for="listing_bath_id">Bath*</label>
                                                                <select class="form-control select2 property_req"
                                                                    name="bath_id" id="listing_bath_id" required>
                                                                    @foreach ($baths ?? [] as $bath)
                                                                        <option value="{{ $bath->id }}">
                                                                            {{ $bath->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <div class="invalid-feedback">Please enter Bed</div>
                                                            </div>
                                                        </div>



                                                        {{-- <div class="col-lg-6">
                                                        <div class="mb-3">
                                                            <label class="form-label" for="listing_source_id">Source</label>
                                                            <select class="form-control select2" name="source_id"
                                                                id="listing_source_id">
                                                                @foreach ($sources ?? [] as $src)
                                                                    <option value="{{ $src->id }}">
                                                                        {{ $src->name }}</option>
                                                                @endforeach
                                                            </select>

                                                            <div class="invalid-feedback">Please enter Source</div>
                                                        </div>
                                                    </div> --}}
                                                        <div class="row text-end" id="duplicationMessage">
                                                            <div class="col-md-12">
                                                                <span class="text-danger"
                                                                    id="showDuplicationMessage"></span>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-start gap-3 mt-4">
                                                <button type="button" class="btn btn-light btn-label previestab"
                                                    data-previous="steparrow-gen-info-tab"><i
                                                        class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i>
                                                    Previous</button>

                                                <button type="submit"
                                                    class="btn btn-success btn-label right ms-auto propertyInfo saveListing"
                                                    data-nexttab="pills-experience-tab"><i
                                                        class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>Submit</button>
                                            </div>
                                        </div>
                                        <!-- end tab pane -->

                                        <div class="tab-pane fade" id="pills-experience" role="tabpanel">
                                            <div class="text-center">

                                                <div class="avatar-md mt-5 mb-4 mx-auto">
                                                    <div
                                                        class="avatar-title bg-light text-success display-4 rounded-circle">
                                                        <i class="ri-checkbox-circle-fill"></i>
                                                    </div>
                                                </div>
                                                <h5>Well Done !</h5>
                                                <p class="text-muted">You have Successfully Created a Listing</p>
                                            </div>
                                        </div>
                                        <!-- end tab pane -->
                                    </div>
                                    <!-- end tab content -->
                                </form>
                            </div>
                            <!-- end card body -->
                        </div>
                        <!-- end card -->
                    </div>
                    <!-- end col -->
                </div>
            </div>

        </div>
    </div>
</div>



<div id="mapModal" class="modal zoomIn" tabindex="-1" aria-labelledby="mapModalLabel" aria-hidden="true"
    style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-soft-danger">
                <h5 class="modal-title" id="mapModalLabel">Location</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body">
                <div id="google-map" style="min-height: 500px"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light action-close" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>




<div id="exportModal" class="modal zoomIn" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true"
    style="display: none;">
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
                            @foreach (getListingLeadsExportColumns() as $index => $column)
                                <div class="col-4">
                                    <div class="form-check form-switch form-switch-success">
                                        <input class="form-check-input export-columns" type="checkbox"
                                            name="columns[]" value="{{ $index }}" role="switch"
                                            id="export-column-{{ $index }}">
                                        <label class="form-check-label" for="export-column-{{ $index }}">
                                            {{ $column }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
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





<div class="modal zoomIn" id="unSuccessfulModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog  modal-md modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-soft-danger">
                <h5 class="modal-title" id="unSuccessfulModalLabel">Unsuccessful Reason</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                    id="close-modal"></button>
            </div>
            <div class="modal-body px-5   py-3">
                <div class="row">
                    <div class="col-3">Reason*:</div>
                    <div class="col-9">
                        <input type="hidden" id="unsuccessfull_lead_id">


                        <select name="" id="preFillOption2" class="form-control">
                            <option value=""></option>
                            <option value="Internal Agent">Internal Agent</option>
                            <option value="External Agent">External Agent</option>
                            <option value="Family">Family</option>
                            <option value="Friend">Friend</option>
                            <option value="Promoter">Promoter</option>
                            <option value="Duplicate">Duplicate</option>
                            <option value="Other">Other</option>
                        </select>

                        <textarea name="" rows="3" class="form-control d-none mt-3" placeholder="Add Reason for unsuccessful"
                            id="unsuccessful_note" required></textarea>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success action-archive" id="saveUnsuccessful">
                  Mark as Unsuccessful
                </button>
                <button type="button" class="btn btn-light  action-close" data-bs-dismiss="modal">
                    Close
                </button>

            </div>
        </div>
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
