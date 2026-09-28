<?php $__env->startSection('title'); ?>
    Leads
<?php $__env->stopSection(); ?>
<?php $__env->startSection('style'); ?>
    
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
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <input type="hidden" id="is_admin" value="<?php echo e(auth()->user()->hasRole(['Super Admin', 'Admin'])? 'true': 'false'); ?>">
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


                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Leads Self Assign')): ?>
                                    <button type="button" id="takeLead" class="btn btn-danger add-btn action-add d-none">
                                        <i class="ri-add-line align-bottom me-1"></i> Take this lead
                                    </button>
                                    <button type="button" id="takeArchiveLead"
                                        class="btn btn-danger add-btn action-add d-none">
                                        <i class="ri-add-line align-bottom me-1"></i> Take this Archived lead
                                    </button>
                                <?php endif; ?>



                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Leads Create')): ?>
                                    <button type="button" id="newBtn" class="btn btn-danger add-btn action-add">
                                        <i class="ri-add-line align-bottom me-1"></i> Add New
                                    </button>
                                <?php endif; ?>
                                <button type="submit" class="btn btn-success d-none action-save"
                                    id="saveBtn">Save</button>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Leads Edit')): ?>
                                    <button type="button" id="editBtn" class="btn btn-info d-none action-edit">Edit</button>
                                <?php endif; ?>

                                
                                <button type="button" class="btn btn-light d-none action-close"
                                    id="closeBtn">Close</button>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Properties Export')): ?>
                                    <button type="button" class="btn btn-soft-success action-export" id="exportBtn"
                                        title="Export to Excel"><i class="ri-file-excel-2-line"></i></button>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['Leads Archive', 'Leads Delete'])): ?>
                                    <button type="button" id="dropdownMenuLink1" data-bs-toggle="dropdown"
                                        aria-expanded="false" class="btn btn-soft-info shadow-none d-none action-more"><i
                                            class="ri-more-2-fill"></i></button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink1">
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Leads Create Offer')): ?>
                                            <li>
                                                <a class="dropdown-item" href="javascript:void(0)" onclick="openOfferModal()"
                                                    title="Make offer">
                                                    <i class=" ri-file-list-3-line align-bottom me-1"></i> Make New Offer
                                                </a>
                                            </li>
                                        <?php endif; ?>

                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Leads KYC Document Write')): ?>
                                            <li>
                                                <a class="dropdown-item" href="javascript:void(0)" onclick="generateKycLink()"
                                                    title="Write KYC Document">
                                                    <i class="ri-pencil-line align-bottom me-1"></i> Write KYC Document
                                                </a>
                                            </li>
                                        <?php endif; ?>


                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Leads Archive')): ?>
                                            <li>
                                                <a class="dropdown-item" href="javascript:void(0)" id="archiveSingleLead"
                                                    title="Hard Delete">
                                                    <i class="ri-inbox-archive-line align-bottom me-1"></i> Archive
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Leads Transfer to Leadpool')): ?>
                                            <li>
                                                <a class="dropdown-item" href="javascript:void(0)" id="transferToPortalLeadPool"
                                                    title="Transfer to portal Leadpool">
                                                    <i class="ri-arrow-up-circle-line align-bottom me-1"></i> Transfer to Portal Lead Pool
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Leads Delete')): ?>
                                            <li>
                                                <a class="dropdown-item" href="javascript:void(0)" id="hardDeleteBtn"
                                                    title="Hard Delete">
                                                    <i class="ri-delete-bin-7-line align-bottom me-1"></i> Delete
                                                </a>
                                            </li>
                                        <?php endif; ?>

                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Leads Create Deal')): ?>
                                            <li>
                                                <a class="dropdown-item disabled" href="javascript:void(0)" onclick="openDealModal()"
                                                   title="Create deal using this lead">
                                                    <i class="mdi mdi-handshake-outline align-bottom me-1"></i> Create Deal
                                                </a>
                                            </li>
                                        <?php endif; ?>

                                    </ul>
                                <?php endif; ?>


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
                    <input type="hidden" id="dailer_id" name="dailer_id" value="<?php echo e(auth()->user()->id); ?>">

                    <input type="hidden" id="lead_status_id" name="lead_status_id" value="">
                    <input type="hidden" id="lead_sub_status_id" name="lead_sub_status_id" value="">
                    <?php if ($__env->exists('admin.lead.include.form')) echo $__env->make('admin.lead.include.form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>



                    <button type="submit" class="d-none" id="submitBtn"></button>
                </form>

                <div class="row" id="duplicateLeads">
                </div>

                <div class="row">
                    <?php echo $__env->make('admin.lead.include.partials._activities', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                    <div class="col-md-6  col-sm-12 addHide">
                        <div id="eventWidgetArea">

                        </div>
                    </div>
                    <div class="col-12"></div>
                </div>

            </div>
        </div>
    </div>

    <?php if ($__env->exists('admin.lead.include.filterForm')) echo $__env->make('admin.lead.include.filterForm', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <div class="row">
        <div class="col-xxl-12">
            <div class="table-sticky-header-container" id="table-sticky-header">
                <ul class="nav nav-tabs nav-tabs-custom nav-success mb-1 no-border" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link ActiveLeads  activeLeads active" data-bs-toggle="tab" id="activeLeads"
                            href="#" role="tab" aria-selected="false" tabindex="-1">
                            Active Leads

                        </a>
                    </li>

                    <li class="nav-item" role="presentation">
                        <a class="nav-link LeadPool  portalLeadPoolFilter" data-bs-toggle="tab" id="portalLeadPoolFilter"
                            href="#" role="tab" aria-selected="false" tabindex="-1">
                            Portal Lead Pool

                        </a>
                    </li>

                    <li class="nav-item" role="presentation">
                        <a class="nav-link LeadPool  leadPoolFilter" data-bs-toggle="tab" id="leadPoolFilter"
                            href="#" role="tab" aria-selected="false" tabindex="-1">
                            Bitrix Lead Pool

                        </a>
                    </li>

                    <?php if(auth()->user()->hasRole(['Super Admin', 'Admin'])): ?>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link ArchiveLeads  archiveleads" data-bs-toggle="tab" id="archiveLeads"
                                href="#" role="tab" aria-selected="false" tabindex="-1">
                                Archived Leads
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <div class="col-xxl-12">
            <div class="table-sticky-header-container" id="table-sticky-header">
                <ul class="nav nav-tabs nav-tabs-custom nav-success mb-1 no-border" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link All  statusFilterClear active" data-bs-toggle="tab" id="All"
                            href="#" role="tab" aria-selected="false" tabindex="-1">
                            All
                            <span
                                class="position-absolute topbar-badge cartitem-badge fs-10 translate-middle badge rounded-pill bg-info d-none"
                                id="lead_status_counter_all">0</span>
                        </a>
                    </li>


                    <?php $__currentLoopData = $statuses ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="nav-item" role="presentation">
                            <a id="lead_status_counter_<?php echo e($status->id); ?>" data-bs-toggle="tab" data-bs-html="true"
                                class="nav-link All statusFilter text-uppercase" data-bs-toggle="tab"
                                id="status_id_<?php echo e($status->id); ?>" href="javascript:void(0)"
                                data-status="<?php echo e($status->id); ?>" role="tab" aria-selected="false" tabindex="-1">
                                <i class="<?php echo e($status->icon); ?> me-1"></i><?php echo e($status->name); ?>


                            </a>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                </ul>
            </div>
            <div class="card" id="contactList">
                <div class="card-body">
                    <table id="contacts-table" class="table align-middle" style="width:100%">
                        <thead class="lead_datatable_header">
                            <tr class="text-uppercase table-light2">
                                <th scope="col" style="width: 50px;">
                                    <div class="form-check">
                                        <input class="form-check-input " type="checkbox" id="checkAll" value="option">
                                    </div>
                                </th>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Mobile</th>
                                <th>Source</th>
                                <th>Follow Up</th>
                                <th>Type</th>
                                <th>Agent</th>
                                <th>Previous Agent</th>
                                <th>Location</th>
                                <th>Telesales</th>
                                <th>Purpose</th>
                                <th>Status</th>
                                <th>Updated</th>

                                
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('admin.lead.include.modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('admin.events.include.modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <div id="eventModalsDiv"></div>

    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Convert to Listing Lead')): ?>
        <?php echo $__env->make('admin.lead.include.component.leads_to_listing', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <?php echo $__env->make('admin.partials.datatable', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <script src="<?php echo e(asset('assets/js/pages/form-wizard.init.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('assets/libs/jquery.priceformat/jquery.priceformat.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('assets/libs/cleave.js/cleave.js.min.js')); ?>"></script>
    
    

    <script src="<?php echo e(URL::asset('/assets/js/plugins/widgets.js')); ?>"></script>
    <script>
        // moment.updateLocale('en', {
        //     relativeTime: {
        //         future: "in %s",
        //         past: "%s",
        //         s: 'a few seconds',
        //         ss: '%d seconds',
        //         m: "a min",
        //         mm: "%d mins",
        //         h: "an hour",
        //         hh: "%d hours",
        //         d: "a day",
        //         dd: "%d days",
        //         w: "a week",
        //         ww: "%d weeks",
        //         M: "a month",
        //         MM: "%d months",
        //         y: "a year",
        //         yy: "%d years"
        //     }
        // });
        let isTeleSales = false;
        <?php if(auth()->user()->hasRole('Telesales')): ?>
            isTeleSales = true;
        <?php endif; ?>
        let currentPage = "Lead";
        let all_listing_val = false;
        let only_archive = false;
        let portal_lead = false;
        let isUpdate = false;
        let dataObj = {};
        let mobileNumberValidatorFalied = 0;
        let defaultStatusId = 1;
        let defaultSubStatusId = 4;
        let agent_sub_communities = "<?php echo e(route('ajax.user-sub-communities')); ?>";
        let lead_sub_status_url = "<?php echo e(route('ajax.lead-sub-statuses')); ?>";
        let remove_additional_agent = "<?php echo e(route('admin.work.leads.remove.agent')); ?>";
        let fetch_additional_agents = "<?php echo e(route('admin.work.leads.get.agents')); ?>";
        let add_more_agent_url = "<?php echo e(route('admin.work.leads.add.more.agent')); ?>";

        let makeCallLink = "<?php echo e(route('admin.work.leads.start-call')); ?>";
        let bulkAssignLink = "<?php echo e(route('admin.work.leads.assign-agents')); ?>";

        let mode = 'view';
        var allValid2 = false;
        let allValid = false;
        let isDuplicate = false;
        //event/calendar related urls
        let getEventUrl = "<?php echo e(route('admin.events.get')); ?>";
        let currentUserRole = "<?php echo e(auth()->user()->roles->first()->name); ?>";
        let currentUserId = "<?php echo e(auth()->user()->id); ?>";
        let propertySelect2 = "<?php echo e(route('ajax.properties.select2')); ?>";
        let eventCreateUrl = "<?php echo e(route('admin.events.store')); ?>";
        let eventUpdateUrl = "<?php echo e(route('admin.events.update')); ?>";
        let getAllEventsType = "<?php echo e(route('admin.events.type.get.all')); ?>";
        let eventObj = {};
        let isEventPage = false;
        let contactSelect2url = "<?php echo e(route('ajax.contacts.select2')); ?>";
        let leadSelect2url = "<?php echo e(route('ajax.leads.select2')); ?>";
        let eventPropertyDetailsUrl = "<?php echo e(route('admin.listing.properties.get')); ?>";
        
        

        let canRemoveAdditionalAgents = false;
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Leads Additional Agents Remove')): ?>
            canRemoveAdditionalAgents = true;
        <?php endif; ?>


        let call_now_button = 0;

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Dial my lead')): ?>
            call_now_button = 1;
        <?php endif; ?>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Dial other lead')): ?>
            call_now_button = 2;
        <?php endif; ?>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['Dial my contact', 'Dial other contact'])): ?>
            call_now_button = 3;
        <?php endif; ?>
    </script>
    <script src="<?php echo e(URL::asset('/assets/js/plugins/event.js')); ?>?v=<?php echo e(time()); ?>"></script>
    <script>
        let mobileInput = initPhoneValidator('mobile', 'full_mobile');
        var input = document.querySelector("#mobile");
        var iti = window.intlTelInput(input, {
            initialCountry: "ae",
        });

        input.addEventListener("input", function() {
            var selectedCountryData = iti.getSelectedCountryData();
            var countryCode = selectedCountryData.dialCode;
            $('input[name="mobile_countrycode"]').val(countryCode);

            var fullMobile = iti.getNumber();
            $('input[name="full_mobile"]').val(fullMobile);
            $('input[name="mobile"]').val(fullMobile);
        });

        input.addEventListener("countrychange", function() {
            var selectedCountryData = iti.getSelectedCountryData();
            var countryCode = selectedCountryData.dialCode;
            $('input[name="mobile_countrycode"]').val(countryCode);

            var fullMobile = iti.getNumber();
            $('input[name="full_mobile"]').val(fullMobile);
            $('input[name="mobile"]').val(fullMobile);

            var isValid = iti.isValidNumber();
            var mobileValidation = isValid ? "" : "Please enter a valid mobile number with Country Code.";
            $('.mobileValidation').html(mobileValidation);

        });


        let phoneInput = initPhoneValidator('phone', 'full_phone');
        var input2 = document.querySelector("#phone");
        var iti2 = window.intlTelInput(input2, {
            initialCountry: "ae",
        });


        input2.addEventListener("input", function() {
            var selectedCountryData = iti2.getSelectedCountryData();
            var countryCode = selectedCountryData.dialCode;
            $('input[name="phone_countrycode"]').val(countryCode);

            var fullPhone = iti2.getNumber();
            $('input[name="full_phone"]').val(fullPhone);
            $('input[name="phone"]').val(fullPhone);
        });

        input2.addEventListener("countrychange", function() {
            var selectedCountryData = iti2.getSelectedCountryData();
            var countryCode = selectedCountryData.dialCode;
            $('input[name="phone_countrycode"]').val(countryCode);

            var fullPhone = iti2.getNumber();
            $('input[name="full_phone"]').val(fullPhone);
            $('input[name="phone"]').val(fullPhone);

            var isValid = iti2.isValidNumber();
            var phoneValidation = isValid ? "" : "Please enter a valid phone number with Country Code.";
            $('.phoneValidation').html(phoneValidation);
        });



        let evenStatusDropDown = "";
        <?php echo json_encode(\App\Models\Meeting::STATUS_SELECT, 15, 512) ?>?.map(item => {
            evenStatusDropDown +=
                `<a class="dropdown-item change-meeting-status" href="javascript:void(0)" data-status="${item}">${item}</a>`;
        });
        let meetingStatusChangePermission = "<?php echo e(auth()->user()->can('Contact Meeting Status Change')); ?>";
        let callIcon = "<?php echo e(asset('assets/json/icons/call-icon.json')); ?>";
        let noTaskIcon = "<?php echo e(asset('assets/json/icons/wired-outline-245-edit-document.json')); ?>";
        let noFollowupIcon = "<?php echo e(asset('assets/json/icons/wired-outline-981-consultation.json')); ?>";
        let meetingLogIcon = "<?php echo e(asset('assets/json/icons/wired-outline-981-consultation.json')); ?>";
        let leadsViewMyRecording = false;
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Leads View My Recordings')): ?>
            leadsViewMyRecording = true;
        <?php endif; ?>
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
            closeOnSelect: true,


        });
        $('#source_id').select2({
            placeholder: 'Select source',
            allowClear: true,
            closeOnSelect: true,
            closeOnSelect: true,


        });
        $('#type_id').select2({
            placeholder: 'Select type',
            allowClear: true,
            closeOnSelect: true,
            closeOnSelect: true,


        });
        $('#preFillOption').select2({
            placeholder: 'Select any reason',
            allowClear: true,
            closeOnSelect: true,
        });

        $('#preFillOption_two').select2({
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
            templateResult: formatProperty,
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
            closeOnSelect: true,

            dropdownParent: $('#generalModal')
        });

        /*$('#agents_dropdown').select2({
            placeholder: 'Select Action',
            allowClear: false,
            closeOnSelect: true,
            dropdownParent: $('#generalModal')
        });
        $('#bulk_leads_telesales_leader').select2({
            placeholder: 'Select Telesales Head',
            allowClear: false,
            closeOnSelect: true,
            dropdownParent: $('#generalModal')
        });*/


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
        $('#search_completion_status').select2({
            placeholder: 'Select Completion Status',
            allowClear: false,
            closeOnSelect: false,
        });
        $('#search_regions').select2({
            placeholder: 'Select Region',
            allowClear: false,
            closeOnSelect: false,
        });


        /*$('#bulk_leads_telesales').select2({
            placeholder: 'Select Action',
            allowClear: false,
            closeOnSelect: true,
            dropdownParent: $('#generalModal')
        });*/



        $('#min_bed_id').select2({
            placeholder: 'Min Bed',
            allowClear: false,
            closeOnSelect: true,
        });
        $('#max_bed_id').select2({
            placeholder: 'Max Bed',
            allowClear: false,
            closeOnSelect: true,
        });


        $('#min_bath_id').select2({
            placeholder: 'Min Bath',
            allowClear: false,
            closeOnSelect: true,
        });
        $('#max_bath_id').select2({
            placeholder: 'Max Bath',
            allowClear: false,
            closeOnSelect: true,
        });

        $('#furnished').select2({
            placeholder: 'Select Furnish',
            allowClear: false,
            closeOnSelect: true,
        });
        flatpickr('#dob', {
            "maxDate": new Date(),
            enableTime: false,
            dateFormat: datePickerFormat()
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
            $('.statusFilter').removeClass('active');
            $('.statusFilterClear').addClass('active');
            dataTable.column(8).visible(false);
            dataTable.column(9).visible(false);

        })

        $(document).on("click", "#refreshTableButton", function() {
            $("#searchSearchBtn").trigger("click")
        })

        setTimeout(function() {
            // $('<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['Lead Pool List'])): ?><div class="flex-shrink-0 inline-blk" id="all_listing_div"><div class="form-check form-switch form-switch-right form-switch-md"><label for="file-input-showcode" class="form-label text-muted">Lead Pool</label><input class="form-check-input code-switcher" type="checkbox" id="all_listing"></div></div><?php endif; ?>')
            //     .insertBefore('.dt-buttons');
            // <?php if(auth()->user()->hasRole(['Telesales', 'Telesales Head'])): ?>
            //     $('#all_listing').attr('disabled', true);
            //     $('#all_listing_div').addClass('d-none');
            // <?php endif; ?>

        }, 300);

        $(document).on("click", "#all_listing", function() {
            $(this).toggleClass("active");
            var locationColumn = dataTable.column(8);
            var agentColumn = dataTable.column(7);
            if ($(this).hasClass("active")) {
                all_listing_val = true
                only_archive = false
                portal_lead = false

                dataTable.ajax.reload();
                locationColumn.visible(true);
                agentColumn.visible(false);
            } else {
                all_listing_val = false
                only_archive = false
                portal_lead = false
                dataTable.ajax.reload();
                locationColumn.visible(false);
                agentColumn.visible(true);
                <?php if(auth()->user()->hasRole('Agent')): ?>
                    $('.multi_options').addClass('d-none');
                <?php endif; ?>

            }


        });
        $(document).on("click", "#leadPoolFilter", function() {
            $('#leadPoolFilter').addClass("active");
            $('#portalLeadPoolFilter').removeClass("active");
            $('#activeLeads').removeClass("active");
            $('#archiveLeads').removeClass("active");

            var locationColumn = dataTable.column(8);
            var agentColumn = dataTable.column(7);
            if ($(this).hasClass("active")) {
                all_listing_val = true
                only_archive = false
                portal_lead = false
                dataTable.ajax.reload();
                locationColumn.visible(true);
                agentColumn.visible(false);
            } else {
                all_listing_val = false
                only_archive = false
                portal_lead = false

                dataTable.ajax.reload();
                locationColumn.visible(false);
                agentColumn.visible(true);
                <?php if(auth()->user()->hasRole('Agent')): ?>
                    $('.multi_options').addClass('d-none');
                <?php endif; ?>

            }
        })
        $(document).on("click", "#portalLeadPoolFilter", function() {
            $('#portalLeadPoolFilter').addClass("active");
            $('#leadPoolFilter').removeClass("active");
            $('#activeLeads').removeClass("active");
            $('#archiveLeads').removeClass("active");

            var locationColumn = dataTable.column(8);
            var agentColumn = dataTable.column(7);
            if ($(this).hasClass("active")) {
                all_listing_val = false
                only_archive = false
                portal_lead = true;
                dataTable.ajax.reload();
                locationColumn.visible(true);
                agentColumn.visible(false);
            } else {
                all_listing_val = false
                only_archive = false
                portal_lead = false;

                dataTable.ajax.reload();
                locationColumn.visible(false);
                agentColumn.visible(true);
                <?php if(auth()->user()->hasRole('Agent')): ?>
                    $('.multi_options').addClass('d-none');
                <?php endif; ?>

            }
        })
        $(document).on("click", "#archiveLeads", function() {
            $('#leadPoolFilter').removeClass("active");
            $('#portalLeadPoolFilter').removeClass("active");
            $('#activeLeads').removeClass("active");
            $('#archiveLeads').addClass("active");

            var locationColumn = dataTable.column(9);
            var previousAgentColumn = dataTable.column(8);
            var agentColumn = dataTable.column(7);
            if ($(this).hasClass("active")) {
                only_archive = true
                all_listing_val = false
                portal_lead = false
                dataTable.ajax.reload();
                agentColumn.visible(true);
                previousAgentColumn.visible(false);
            } else {
                only_archive = false
                all_listing_val = false
                portal_lead = false
                dataTable.ajax.reload();
                // locationColumn.visible(true);
                agentColumn.visible(true);
                previousAgentColumn.visible(false);
                <?php if(auth()->user()->hasRole('Agent')): ?>
                    $('.multi_options').addClass('d-none');
                <?php endif; ?>

            }
        })


        $(document).on("click", "#activeLeads", function() {
            $('#leadPoolFilter').removeClass("active");
            $('#portalLeadPoolFilter').removeClass("active");
            $('#activeLeads').addClass("active");
            $('#archiveLeads').removeClass("active");

            var locationColumn = dataTable.column(9);
            var previousAgentColumn = dataTable.column(8);
            var agentColumn = dataTable.column(7);
            if ($(this).hasClass("active")) {
                all_listing_val = false
                only_archive = false
                portal_lead = false

                dataTable.ajax.reload();
                // locationColumn.visible(true);
                agentColumn.visible(true);
                previousAgentColumn.visible(false);
            } else {
                all_listing_val = true
                only_archive = false
                portal_lead = false

                dataTable.ajax.reload();
                // locationColumn.visible(false);
                agentColumn.visible(true);
                previousAgentColumn.visible(false);
                <?php if(auth()->user()->hasRole('Agent')): ?>
                    $('.multi_options').addClass('d-none');
                <?php endif; ?>

            }
            previousAgentColumn.visible(false);
        })


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

        $(document).on("click", "#exportBtn", function() {
            $("#exportForm").removeClass("was-validated");
            $("#exportModal").modal("show")
        })


        function decodedFilterData(filtersData) {
            return {
                id: filtersData?.search_id,
                id_operator: filtersData?.search_id_operator,
                first_name: filtersData?.search_first_name,
                search_first_name_operator: filtersData?.search_first_name_operator,
                email: filtersData?.search_email,
                search_email_operator: filtersData?.search_email_operator,

                last_name: filtersData?.search_last_name,
                search_last_name_operator: filtersData?.search_last_name_operator,


                lead_statuses: filtersData?.search_lead_statuses,
                lead_statuses_operator: filtersData?.search_lead_statuses_operator,



                agents: filtersData?.search_agents,
                agents_operator: filtersData?.search_agents_operator,

                managers: filtersData?.search_managers,
                managers_operator: filtersData?.search_managers_operator,

                regions: filtersData?.search_regions,
                regions_operator: filtersData?.search_regions_operator,

                completion_status: filtersData?.search_completion_status,
                completion_status_operator: filtersData?.search_completion_status_operator,


                previous_agent: filtersData?.search_previous_agents,
                previous_agent_operator: filtersData?.search_previous_agents_operator,

                updated_by: filtersData?.search_updated_by,
                updated_by_operator: filtersData?.search_updated_by_operator,


                property: filtersData?.property_ref,
                property_operator: filtersData?.property_ref_operator,

                for: filtersData?.search_for,
                for_operator: filtersData?.search_for_operator,


                telesales: filtersData?.filter_telesales,
                telesales_operator: filtersData?.filter_telesales_operator,


                lead_sources: filtersData?.search_lead_sources,
                lead_sources_operator: filtersData?.search_lead_sources_operator,

                lead_types: filtersData?.search_lead_types,
                lead_types_operator: filtersData?.search_lead_types_operator,

                lead_medium: filtersData?.search_lead_medium,
                lead_medium_operator: filtersData?.search_lead_medium_operator,




                priorities: filtersData?.search_priorities,
                priorities_operator: filtersData?.search_priorities_operator,


                genders: filtersData?.search_genders,
                genders_operator: filtersData?.search_genders_operator,

                nationality: filtersData?.search_nationalities,
                nationalities_operator: filtersData?.search_nationalities_operator,

                created_from: filtersData?.search_created_from,
                date_created_operator: filtersData?.search_created_from_operator,

                updated_from: filtersData?.search_updated_from,
                date_updated_operator: filtersData?.search_updated_from_operator,



                follow_up_date: filtersData?.search_follow_up_date,
                follow_up_date_operator: filtersData?.search_date_followupdate_operator,

                bitrix_data: filtersData?.search_bitrix_data,
                bitrix_data_operator: filtersData?.search_bitrix_data_operator,

                deleted: filtersData?.search_deleted,
                archive: filtersData?.search_archive,
                incomplete_lead: filtersData?.search_incomplete_leads,

                inactive_agents: filtersData?.search_inactive_agents,

                leadpool: filtersData?.leadpool,
                portalLeadPool: filtersData?.portalLeadPool,


                communities: filtersData?.filter_communities,
                communities_operator: filtersData?.search_communities_operator,

                sub_communities: filtersData?.search_sub_communities,
                sub_communities_operator: filtersData?.search_sub_communities_operator,

                locations: filtersData?.filter_locations,
                community_properties: filtersData?.community_properties,

                furnished: filtersData?.filter_furnished,
                filter_furnished_operator: filtersData?.filter_furnished_operator,

                min_bed_id: filtersData?.filter_min_bed_id,
                max_bed_id: filtersData?.filter_max_bed_id,
                min_bath_id: filtersData?.filter_min_bath_id,
                max_bath_id: filtersData?.filter_max_bath_id,
                min_price: filtersData?.filter_min_price,
                max_price: filtersData?.filter_max_price,


            };
        }

        $(document).on("submit", "#exportForm", function(event) {
            event.preventDefault();
            let isSelected = $(".export-columns:checked").length > 0;
            if (!isSelected) {
                showToastify("Please select at least one column.", "error");
                return;
            }
            let exportRoute = "<?php echo e(route('admin.work.leads.export-excel')); ?>";
            let columns = $(this).serialize();
            let filtersDataDecoded = decodedFilterData(filtersData);
            filtersDataDecoded = objectToQueryString(filtersDataDecoded)
            window.open(`${exportRoute}?${columns}&${filtersDataDecoded}`);

            $("#exportForm").trigger("reset");
            $("#exportModal").modal("hide");
            return;
        })

        let isNewTab = false;

        setTimeout(function() {
            var open_ref_id = "<?php echo e(Request('id')); ?>";
            if (open_ref_id) {
                isNewTab = true;

                if ($("#append-here").has("#search_id_find").length > 0) {
                    $('#id_find').val(open_ref_id).trigger('click');
                    $('#mySearchBox').val(open_ref_id);
                    $("#searchSearchBtn").trigger("click");
                    // $('.dataTables_filter .form-control').val(open_ref_id).trigger('keyup');
                } else {
                    $('#applicator').val('id_find').trigger('change');
                    setTimeout(function() {
                        $('#id_find').val(open_ref_id).trigger('click');
                        $('#mySearchBox').val(open_ref_id);
                        $("#searchSearchBtn").trigger("click");
                        // $('.dataTables_filter .form-control').val(open_ref_id).trigger('keyup');


                    }, 500);
                }
            }
        }, 500);


        // setTimeout(function() {
        //     if (isNewTab) {
        //         //
        //         $('#contactList tbody tr:eq(0)').click();
        //         $('#mySearchBox').val("<?php echo e(Request('id')); ?>");

        //     }
        // }, 1500);

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
                url: "<?php echo e(route('admin.work.leads.check.duplicate')); ?>",
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
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Leads Additional Agents Bulk Assign')): ?>
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
            <?php endif; ?>
        })


        $(document).on("click", ".open_sub_status", function() {

            let statusValue = $('#lead_status_id').val();
            // alert(statusValue)
            if (statusValue == 1) {
                $('.applyValidation').attr('required', false);
                $(".clear_req").html('');
                $('.newlead').attr('required', true);
                $(".newlead_req").html('*');

            }
            if (statusValue == 10) {
                $('.applyValidation').attr('required', false);
                $(".clear_req").html('');
                $('.notAnswered').attr('required', true);
                $(".notAnswered_req").html('*');
            }
            if (statusValue == 5) {
                $('.applyValidation').attr('required', false);
                $(".clear_req").html('');
                $('.contacted').attr('required', true);
                $(".contacted_req").html('*');
            }
            if (statusValue == 8 || statusValue == 7 || statusValue == 9) {
                $('.applyValidation').attr('required', false);
                $(".clear_req").html('');
                $('.qualified').attr('required', true);
                $(".qualified_req").html('*');
            }

            <?php if(auth()->user()->region_id == 2): ?>
                $('#property_id').attr('required', false);
                $('.property_req').html('');
            <?php endif; ?>

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
                    html += key + ' - ' + value + '<br>';

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
                url: "<?php echo e(route('admin.work.leads.response.time')); ?>",
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

        function _getActivityLog(id) {
            $("#activityLogDiv").find(".acitivity-timeline").html("");
            $.ajax({
                url: "<?php echo e(route('admin.work.leads.activity.log')); ?>",
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
                            `${item.causer ? item.causer.thumb_url : "<?php echo e(URL::asset('assets/images/users/multi-user.jpg')); ?>"}`;
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

        function getActivityLog(id) {
            $('#no_activity').addClass('d-none');
            $('#activity-log-div2').html('');
            $('#activity-log-div_Placeholder').removeClass('d-none');
            $("#activity-log-div").removeClass("d-none")
            $("#activity-log-div").find(".user-activity-timeline").html("");
            fullyLoaded = true;
            $.ajax({
                url: "<?php echo e(route('admin.work.leads.activity.log')); ?>",
                type: 'GET',
                dataType: 'html',
                data: {
                    subject_model: "Lead",
                    subject_id: id
                },
                success: function(result) {
                    $('#activity-log-div_Placeholder').addClass('d-none');
                    $('#activity-log-div2').html('');
                    $('#activity-log-div2').html(result);
                    // let element = document.getElementById("activity-log-div2");
                    // element.scrollTop = element.scrollHeight
                    fullyLoaded = true;
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
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Lead Notes List')): ?>
                let lead_id = $("#id").val();
                $("#notes").html("");
                $('#notes-log-div_Placeholder').removeClass('d-none');
                $('#no_notes').addClass('d-none');
                $("#notes-log-div").removeClass("d-none")
                $.ajax({
                    url: "<?php echo e(route('ajax.lead-notes-render')); ?>",
                    type: 'GET',
                    dataType: 'html',
                    data: {
                        lead_id
                    },
                    success: function(result) {
                        $('#notes-log-div_Placeholder').addClass('d-none');
                        $('#notes').html('');
                        $("#notes").html(result);
                    },
                    error: function(result) {
                        console.log(result);
                    }
                });
            <?php endif; ?>
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
                        url: "<?php echo e(route('admin.work.leads.bulk.assign.leads')); ?>",
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
                        url: "<?php echo e(route('admin.work.leads.bulk.assign.telesales')); ?>",
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
            $('#preFillOption').val('').trigger('change');
            $('#archiveModal').modal('show');
        })

        $(document).on("click", "#transferToLeadpool", function() {
            $('#preFillOption_two').val('').trigger('change');
            $('#transferToLeadpoolModal').modal('show');
        })

        $(document).on("click", "#transferToPortalLeadPool", function() {
            $('#transfer_portal_lead_pool_lead_id').val($('#id').val());
            $('#transfer_portal_lead_pool_note').val('');
            $('#transferToPortalLeadPoolModal').modal('show');
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


            $('#preFillOption_two').change(function() {
                // get selected value of option
                var option = $(this).find('option:selected').val();
                if (option == 'Other') {
                    $('#transfer_note').val('')
                    $('#transfer_note').removeClass('d-none');
                    $('#transfer_note').focus();
                } else {
                    $('#transfer_note').val(option)
                    $('#transfer_note').addClass('d-none');
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

        $(document).on("change", ".selectAction", function() {

            var selectedOption = $(this).val();

            if (selectedOption == 'Re-Assign') {
                $('#agent_dropdown').removeClass('d-none');
                $('#agents_dropdown').val('').trigger('change');
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
                    url: "<?php echo e(route('admin.work.leads.saveReason')); ?>",
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
            }else if($('#preFillOption').val() == "Other" && archive_note.length < 35){
                showToastify("The archive note must be at least 35 characters.", "error")
                return;
            } else {
                if (ids_array = [], document.getElementsByName("chk_child").forEach((function(e) {
                        if (1 == e.checked) {
                            var t = e.value;
                            ids_array.push(t)
                        }
                    })), "undefined" != typeof ids_array && ids_array.length > 0) {
                    $.ajax({
                        url: "<?php echo e(route('admin.work.leads.archive')); ?>",
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


        $(document).on("click", "#savetransferToLeadpool", function() {
            $('#screen_loader').removeClass('d-none');
            let transfer_note = $('#transfer_note').val();
            if (transfer_note == "") {
                showToastify("Please Add Transfer note.", "error")
                return;
            } else {
                if (ids_array = [], document.getElementsByName("chk_child").forEach((function(e) {
                        if (1 == e.checked) {
                            var t = e.value;
                            ids_array.push(t)
                        }
                    })), "undefined" != typeof ids_array && ids_array.length > 0) {
                    $.ajax({
                        url: "<?php echo e(route('admin.work.leads.transferToLeadpool')); ?>",
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            ids: ids_array,
                            transfer_note: transfer_note,
                        },
                        success: function(result) {
                            $('#screen_loader').addClass('d-none');
                            if (result.status == '1') {
                                showToastify(result.msg, "success")
                                $("#transfer_note").val("")
                                $("#transferToLeadpoolModal").modal("hide")
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
                        url: "<?php echo e(route('admin.work.leads.bulk.assign.telesales.leader')); ?>",
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





        $(document).on('click', '#takeLead', function() {
            Swal.fire({
                    html: '<div class="mt-3"><div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you sure?</h4><p class="text-muted mx-4 mb-0">Are you sure you want to take this Lead?</p></div></div>',
                    showCancelButton: !0,
                    confirmButtonClass: "btn btn-success w-xs me-2 mb-1",
                    confirmButtonText: "Yes, I am sure.",
                    cancelButtonClass: "btn btn-soft-danger w-xs mb-1",
                    buttonsStyling: !1,
                    showCloseButton: !0
                })
                .then(function(t) {
                    if (t.isConfirmed) { // on confirm delete the category
                        let id = $('#id').val();
                        takeLead(id);
                    }
                })
        })

        $(document).on('click', '#takeArchiveLead', function() {
            Swal.fire({
                    html: '<div class="mt-3"><div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you sure?</h4><p class="text-muted mx-4 mb-0">Are you sure you want to take this Archived Lead?</p></div></div>',
                    showCancelButton: !0,
                    confirmButtonClass: "btn btn-success w-xs me-2 mb-1",
                    confirmButtonText: "Yes, I am sure.",
                    cancelButtonClass: "btn btn-soft-danger w-xs mb-1",
                    buttonsStyling: !1,
                    showCloseButton: !0
                })
                .then(function(t) {
                    if (t.isConfirmed) { // on confirm delete the category
                        let id = $('#id').val();
                        takeArchivedLead(id);
                    }
                })
        })



        $(document).on("click", "#bulkAssignment", function() {
            // var selectedOption = $('#selectAction').val();
            // get radio name selectAction value and save in selectedOption
            var selectedOption = $('input[name="selectAction"]:checked').val();
            $('#bulkAssignmentLoading').removeClass('d-none');
            $('#bulkAssignment').addClass('d-none');


            var id;

            if (selectedOption == 'Re-Assign') {
                id = $('#agents_dropdown').val();
            } else if (selectedOption == 'Telesales') {
                id = $('#bulk_leads_telesales').val();
            } else if (selectedOption == 'Telesales Head') {
                id = $('#bulk_leads_telesales_leader').val();
            }
            // if id is empty then show error
            // alert(selectedOption)
            if (id == "" || id == null) {
                showToastify("Please Select an agent.", "error")
                $('#bulkAssignmentLoading').addClass('d-none');
                $('#bulkAssignment').removeClass('d-none');
                return;
            }
            let change_status = $('#change_status_new').prop('checked');

            let email_notification = $('#email_notification').prop('checked');
            let system_notification = $('#system_notification').prop('checked');
            let telegram_notification = $('#telegram_notification').prop('checked');

            if (selectedOption == "") {
                showToastify("Please select Option.", "error")
                $('#bulkAssignmentLoading').addClass('d-none');
                $('#bulkAssignment').removeClass('d-none');
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
                        url: "<?php echo e(route('admin.work.leads.general.bulk.assign.leads')); ?>",
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            ids: ids_array,
                            id: id,
                            action: selectedOption,
                            change_status: change_status,
                            email_notification: email_notification,
                            system_notification: system_notification,
                            telegram_notification: telegram_notification,
                        },
                        success: function(result) {
                            console.log('pass')
                            $('#bulkAssignmentLoading').addClass('d-none');
                            $('#bulkAssignment').removeClass('d-none');
                            $('#agents_dropdown').val('').trigger('change');
                            if (result.status == '1') {
                                showToastify(result.msg, "success")
                                $("#selectAction").val('').trigger("change")
                                $('#agent_dropdown').removeClass('d-none');
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
                            $('#bulkAssignmentLoading').addClass('d-none');
                            $('#bulkAssignment').removeClass('d-none');
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
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Lead Notes Create')): ?>
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
                    url: "<?php echo e(route('admin.work.leads.add-note')); ?>",
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
            <?php endif; ?>
        })

        $(document).ready(function() {
            // this function opens the
            setTimeout(function() {

                var open_id = "<?php echo e(Request('lead')); ?>";
                if (open_id) {
                    $('.dataTables_filter .form-control').val(open_id).trigger('keyup');
                }

            }, 500);
        });

        function initMinPriceInput(price = "") {
            $("#min_price").val(price).trigger("change");
            new Cleave('#min_price', {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand'
            });
        }

        initMinPriceInput()
        new Cleave('.cleave', {
            numeral: true,
            numeralThousandsGroupStyle: 'thousand'
        });

        function initMaxPriceInput(price = "") {
            $("#max_price").val(price).trigger("change");
            new Cleave('#max_price', {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand'
            });
        }
        initMaxPriceInput()



        function initFilterMaxPriceInput(price = "") {
            $("#filter_max_price").val(price).trigger("change");
            new Cleave('#filter_max_price', {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand'
            });
        }
        // initFilterMaxPriceInput()

        function initFilterMinPriceInput(price = "") {
            $("#filter_min_price").val(price).trigger("change");
            new Cleave('#filter_min_price', {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand'
            });
        }
        // initFilterMinPriceInput()


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
                    url: "<?php echo e(route('ajax.is-value-unique')); ?>",
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

        function badgeStatus(status) {
            let badge = "";
            switch (status) {
                case 'DRAFT':
                    badge = `<span class="badge bg-soft-primary text-primary">${status}</span>`;
                    break;
                case 'PUBLISHED':
                    badge = `<span class="badge bg-soft-success text-success">${status}</span>`;
                    break;
                case 'UNPUBLISHED':
                    badge = `<span class="badge bg-soft-danger text-danger">${status}</span>`;
                    break;
                case 'REFUSED':
                    badge = `<span class="badge bg-soft-danger text-danger">${status}</span>`;
                    break;
                default:
                    badge = `<span class="badge bg-soft-secondary text-secondary">${status}</span>`;
            }
            return badge;
        }

        function formatProperty(e) {
            let f_unit_no = $(e.element).data('unit_no');
            let f_ref_no = $(e.element).data('ref_no');
            let f_location = $(e.element).data('location');
            let f_type = $(e.element).data('type');
            let f_beds = $(e.element).data('beds');
            let f_for = $(e.element).data('for');
            let f_status = $(e.element).data('status');
            f_status = f_status ? badgeStatus(f_status) : '';

            let f_first_image = $(e.element).data('first_image') ? $(e.element).data('first_image') :
                'https://ui-avatars.com/api/?name=' + f_unit_no + '&color=7F9CF5&background=random';


            return e.id ? $('<span><img src="' + f_first_image + '" class="img-flag rounded-5" width="18" /> ' + f_ref_no +
                ' - ' + f_type + ' - ' + f_unit_no + ' - '+ f_status + ' - ' + f_for +
                "</span> <br> <span class='small ml-5 d-inline-block'> <i class='mdi mdi-bed-double'></i>" + f_beds +
                "</span> <span class='small ml-5 d-inline-block'> <i class='mdi mdi-google-maps'></i>" + f_location +
                "</span> ") : e.text;


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
                    url: "<?php echo e(route('ajax.properties')); ?>",
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        agent_id: id,

                    },
                    success: function(result) {
                        let options = "";
                        result?.data.map(item => {
                            options +=
                                `<option data-beds="${item.bed?item.bed.name: ''}" data-first_image="${item.first_image && item.first_image.path ? item.first_image.path : ''}" data-ref_no="${item.ref_no}" data-unit_no="${item.unit_no}" data-type="${item.property_category?.title || ''}" data-location="${item.location}" data-status="${item.publish_status}" data-for="${item.for}" value="${item.id}">${item.ref_no} - ${item.property_category?.title || ''} - ${item.unit_no || ''} - ${item.location}</option>`;

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
                            url: "<?php echo e(route('admin.work.leads.request-access')); ?>",
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
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Leads Create')): ?>

                mode = 'create';
                // click on first .open_sub_status and change value of lead_status_id
                // let lead_status_id = $('.open_sub_status:first').data('id');
                // $('#lead_status_id').val(lead_status_id).trigger('change');

                // $('.open_sub_status').find('span').addClass('randomClass');

                isUpdate = false;
                $('#listingLeadConversion').addClass('d-none');
                $('#listingConvertor').addClass('d-none');
                $('#viewListingLead').addClass('d-none');
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

                let agent_logged_in = "<?php echo e(auth()->user()->id); ?>";
                $("#agent_id").val(agent_logged_in).trigger('change');
                $('#lead_status_id').val('');
                $('#lead_sub_status_id').val('');
                // clear all inputs
                $('.inputsCriteria').val('').trigger('change');
                $('#responseDiv').addClass('d-none');

                $('#property_id').empty().trigger('change');

                $('#follow_up_label').html('Follow Up');
                $('#follow_up_date').attr('required', false);
            <?php endif; ?>
        });

        $(document).on("click", "#closeBtn", function() {
            toggleForm(false)
            mode = 'view';
            $('#lead_id').addClass('d-none');
            $('#takeLead').addClass('d-none')
            $('#takeArchiveLead').addClass('d-none')
            $('#dropdownMenuLink1').addClass('d-none');

        })
    </script>
    <?php echo $__env->make('admin.lead.include.script', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <script>
        let viewUrl = "<?php echo e(route('admin.work.leads.view', ['lead' => '1'])); ?>";
        viewUrl = viewUrl.slice(0, -1);

        <?php if(auth()->user()->hasRole(['Telesales', 'Telesales Head'])): ?>
            all_listing_val = true
            $('#all_listing').attr('disabled', true);
            $('#all_listing_div').addClass('d-none');
        <?php endif; ?>

        let selectedLead = null;

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
            scrollX: true,
            // responsive: true,
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "<?php echo e(route('admin.work.leads.dt')); ?>",
                type: "post",
                data: function(d) {
                    d.all_listings = all_listing_val;
                    d.only_archive = only_archive;
                    d.portal_lead = portal_lead;


                    d.id = filtersData?.search_id_find;
                    d.id_operator = filtersData?.search_id_find_operator;

                    d.first_name = filtersData?.search_first_name;
                    d.search_first_name_operator = filtersData?.search_first_name_operator;

                    d.email = filtersData?.search_email;
                    d.search_email_operator = filtersData?.search_email_operator;

                    d.last_name = filtersData?.search_last_name;
                    d.search_last_name_operator = filtersData?.search_last_name_operator;


                    d.lead_statuses = filtersData?.search_lead_statuses;
                    d.lead_statuses_operator = filtersData?.search_lead_statuses_operator;

                    d.mobile = filtersData?.search_mobile;
                    d.mobile_operator = filtersData?.search_mobile_operator;


                    d.agents = filtersData?.search_agents;
                    d.agents_operator = filtersData?.search_agents_operator;

                    d.managers = filtersData?.search_managers;
                    d.managers_operator = filtersData?.search_managers_operator;

                    d.regions = filtersData?.search_region;
                    d.regions_operator = filtersData?.search_region_operator;

                    d.completion_status = filtersData?.search_completion_status;
                    d.completion_status_operator = filtersData?.search_completion_status_operator;


                    d.previous_agent = filtersData?.search_previous_agents;
                    d.previous_agent_operator = filtersData?.search_previous_agents_operator;

                    d.updated_by = filtersData?.search_updated_by;
                    d.updated_by_operator = filtersData?.search_updated_by_operator;


                    d.property = filtersData?.search_property_ref;
                    d.property_operator = filtersData?.search_property_ref_operator;

                    d.for = filtersData?.search_for;
                    d.for_operator = filtersData?.search_for_operator;

                    // d.telesales_head = filtersData?.filter_telesales_head;
                    // d.telesales_head_operator = filtersData?.filter_telesales_head_operator;

                    d.telesales = filtersData?.filter_telesales;
                    d.telesales_operator = filtersData?.filter_telesales_operator;


                    d.lead_sources = filtersData?.search_lead_sources;
                    d.lead_sources_operator = filtersData?.search_lead_sources_operator;

                    d.lead_types = filtersData?.search_lead_type;
                    d.lead_types_operator = filtersData?.search_lead_type_operator;

                    d.lead_medium = filtersData?.search_lead_medium;
                    d.lead_medium_operator = filtersData?.search_lead_medium_operator;




                    d.priorities = filtersData?.search_priorities;
                    d.priorities_operator = filtersData?.search_priorities_operator;


                    d.genders = filtersData?.search_genders;
                    d.genders_operator = filtersData?.search_genders_operator;

                    d.nationality = filtersData?.search_nationalities;
                    d.nationalities_operator = filtersData?.search_nationalities_operator;

                    d.created_from = filtersData?.search_created_from;
                    d.date_created_operator = filtersData?.search_created_from_operator;

                    d.updated_from = filtersData?.search_updated_from;
                    d.date_updated_operator = filtersData?.search_updated_from_operator;


                    d.follow_up_date = filtersData?.search_follow_up_date;
                    d.follow_up_date_operator = filtersData?.search_follow_up_date_operator;

                    d.bitrix_data = filtersData?.search_bitrix_data;
                    d.bitrix_data_operator = filtersData?.search_bitrix_data_operator;

                    d.deleted = filtersData?.search_deleted;
                    d.archive = filtersData?.search_archive;
                    d.incomplete_lead = filtersData?.search_incomplete_leads,

                        d.inactive_agents = filtersData?.search_inactive_agents;

                    d.leadpool = filtersData?.leadpool;
                    d.portalLeadPool = filtersData?.portalLeadPool;


                    d.communities = filtersData?.filter_communities;
                    d.communities_operator = filtersData?.search_communities_operator;

                    d.sub_communities = filtersData?.filter_sub_communities;
                    d.sub_communities_operator = filtersData?.filter_sub_communities_operator;

                    d.locations = filtersData?.filter_locations;
                    d.community_properties = filtersData?.community_properties;

                    d.furnished = filtersData?.search_furnished;
                    d.filter_furnished_operator = filtersData?.search_furnished_operator;

                    d.lead_category = filtersData?.search_lead_category;
                    d.lead_category_operator = filtersData?.search_lead_category_operator;


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
                $(`<?php if ($__env->exists('admin.lead.include.filterButtons')) echo $__env->make('admin.lead.include.filterButtons', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>`).insertAfter(".dataTables_filter input")
                let total = json.recordsTotal;
                $('.contacts-count').html(total);
            },
            "drawCallback": function(settings) {
                let total = settings.json.recordsTotal;
                $('.contacts-count').html(total);
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
                },
                {
                    data: 'previous_agent_id',
                    name: 'previous_agent_id',
                    orderable: true,
                },
                {
                    data: 'telesales_id',
                    name: 'telesales_id',
                    orderable: false,
                }, {
                    data: 'for',
                    name: 'for',
                    orderable: false,
                },
                {
                    data: 'lead_status_id',
                    name: 'lead_status_id',
                    orderable: false,
                },

                {
                    data: 'lead_status_id',
                    name: 'lead_status_id',
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
                        return `<a href="javascript:void(0)" title="${name}" class="fw-medium link-primary"> <span class='me-1'><i class='${image}'></i></span>` +
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
                    visible: false,

                    render: function(type, row, data, meta) {
                        return data.type_id ? data.lead_type.name : "-";
                    }
                },
                {
                    "targets": 7,
                    orderable: true,

                    render: function(type, row, data, meta) {
                        if (!data.agent) return "-";


                        return `<div class="d-flex align-items-center" data-agent-id="${data.agent.id}" title="${data.agent.name}">
                                    <div class="flex-shrink-0 me-2">
                                        <img src="${data.agent.thumb_url}" alt="" class="avatar-xs rounded-circle">
                                    </div>

                                    <div class="flex-grow-1">${data.agent.first_name}<p title="Created at" class="mb-0"><small>${moment(data.created_at).fromNow()}</small></p></div>
                                </div>`;
                    }
                },
                {
                    "targets": 8,
                    'visible': false,
                    render: function(type, row, data, meta) {
                        if (!data.previous_agent) return "-";


                        return `<div class="d-flex align-items-center" data-agent-id="${data.previous_agent.id}" title="${data.previous_agent.name}">
                                    <div class="flex-shrink-0 me-2">
                                        <img src="${data.previous_agent.thumb_url}" alt="" class="avatar-xs rounded-circle">
                                    </div>

                                    <div class="flex-grow-1">${data.previous_agent.first_name}<p title="Created at" class="mb-0"></p></div>
                                </div>`;
                    }
                },
                {
                    "targets": 9,
                    orderable: true,
                    visible: false,
                    render: function(type, row, data, meta) {
                        //return "-";
                        let locations = data?.criteria?.locations ?? [];
                        let path_name = "";
                        $.each(locations, function(key, val) {
                            path_name += val?.parentable?.path_name + "<br>";
                        });
                        return path_name;
                        //return data?.criteria?.locations[0]?.parentable.path_name ?? "-";
                    }
                },
                {
                    "targets": 10,
                    orderable: false,
                    visible: false,

                    render: function(type, row, data, meta) {
                        if (!data.telesales) return "-";
                        return `<div class="d-flex align-items-center userDetails" data-agent-id="${data.telesales.id}" title="${data.telesales.name}">
                                    <div class="flex-shrink-0 me-2">
                                        <img src="${data.telesales.thumb_url}" alt="" class="avatar-xs rounded-circle">
                                    </div>
                                    <div class="flex-grow-1">${data.telesales.first_name}<p title="Created at" class="mb-0"></p></div>
                                </div>`;
                    }
                },
                {
                    "targets": 11,
                    orderable: true,

                    render: function(type, row, data, meta) {
                        let subStatus = '';
                        if (Array.isArray(data.for)) {

                            let ref_no = data.property ? data.property.ref_no : null;
                            let down_payment_price = data.property ? Number(data?.property?.price?.price)
                                .toLocaleString() : 0;
                            let location = data.property ? data.property.location : '';
                            let category = data.property ? data.property.category : '';
                            let html_data = ''
                            if (ref_no) {
                                html_data = `</span><ul class="list-group" style="padding: 0; margin: 0; font-size: 10px">
    <li class="list-group-item " style="padding: 0;">
        <div class="flex-grow-1">
            <div class="d-flex" style="margin: 0;">
                <div class="ms-1" style="margin: 0;">
                    <h6 class="fs-12 mb-0" style="margin: 0;">${ref_no} - ${category}</h6>
                    <small class="text-muted" style="margin: 0;">${location}</small><br>
                    <span class="text-info" style="margin: 0;font-weight: 500;">AED ${down_payment_price}</span>
                </div>
            </div>
        </div>
        <div class="flex-shrink-0" style="margin: 0; display: none;">
            <span class="badge badge-soft-primary" style="margin: 0;"></span><br>
            <span class="text-warnin" style="margin: 0;">AED 95000</span>
        </div>
    </li>
</ul>`;
                            }

                            subStatus = data.for.map((item, key) =>
                                    `<span class="badge align-middle badge-soft-dark">${item} ${html_data}`)
                                .join(
                                    '');
                        } else {
                            if (data.for) {
                                subStatus =
                                    `<span class="badge align-middle badge-soft-dark">${data.for}</span>`;
                            } else {
                                subStatus =
                                    '<span class="badge align-middle badge-soft-dark">Unspecified</span>';
                            }



                        }
                        return subStatus;
                    }


                },
                {
                    "targets": 12,
                    orderable: true,

                    render: function(type, row, data, meta) {
                        let archiveStatus = '';
                        if (data.is_archive == 1) {
                            archiveStatus =
                                `<span class="badge align-middle badge-soft-danger">Archived</span>`;
                        }
                        let subStatus =
                            `<span class="badge align-middle ${data?.lead_status?.badge ??'color-bg-19'}">${data.lead_status ? data.lead_status.name : "Unspecified"}</span>${archiveStatus}`
                        return subStatus;
                    }


                },
                {
                    "targets": 13,
                    orderable: true,
                    render: function(type, row, data, meta) {
                        let url = "<?php echo e(route('admin.work.leads.index')); ?>" + "?id=" + data.id;

                        let new_tab = '<a title="Open Lead in New Tab" target="_blank" href="' + url +
                            '"><span class="ri-external-link-line"></span></a>';

                        let html = `<div class="d-flex justify-content-between gap-2 align-items-center">
                                    <div class="text-center"><br>

                                        <span title="${moment(data.updated_at).format('DD/MM/YYYY hh:mm:ss')}">${moment(data.updated_at).fromNow()}</span>
                                    </div>${new_tab}
                                </div>`


                        if (data.updated_by != null) {
                            let htmll = `<div class="d-flex align-items-center userDetails" data-agent-id="${data.updated_by.id}"  title="${data.updated_by.name}">
                                    <div class="flex-shrink-0 me-2">
                                        <img src="${data.updated_by.thumb_url}" alt="" class="avatar-xs rounded-circle">
                                    </div>
                                    <div class="flex-grow-1">${data.updated_by.first_name}<p class="mb-0"><small>${moment(data.updated_at).fromNow()}</small></p></div>
                                </div>`
                            return htmll;
                        } else {
                            if (data.updated_at != null) {
                                return html;
                            } else {
                                return '-'
                            }

                        }
                    }
                },

            ]
        });

        dataTable.on('click', 'tbody tr', function(e) {
            var $cell = $(e.target).closest('td'),
                msg;
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

                        }
                    } else {
                        viewData(data.id);
                        highlightRow($(this));
                    }
                }
            } // end if
        }); // table row click


        dataTable.on('draw.dt', function() {
            $(".chk_child").prop("checked", false);
            $('.multi_options').remove();
            $('#checkAll').prop("checked", false);
        });
        // dataTable.on('xhr.dt', function(e, settings, json, xhr) {
        //     let total_count_leads = 0;
        //     $.each(json.statusCount, function(key, val) {
        //         $("#lead_status_counter_" + val.id)
        //             .attr("title", "<em>Total Count: </em> <u>with</u> <b>Some</b>")
        //             .tooltip("_fixTitle")
        //             .tooltip("hide")
        //             .attr("title", "<em>" + val.name + " Leads </em> <b>" + val.lead_count + "</b>")
        //             .tooltip("_fixTitle");

        //         total_count_leads += val.lead_count;
        //     });

        //     $("#lead_status_counter_all").html(total_count_leads);

        // });





        $(document).on('change', '#showInactiveAgents', function(e) {
            // getAgents();
            // if ($(this).is(':checked')) {
            //     all_agents = 'true';
            // } else {
            //     all_agents = 'false';
            // }
            //     $.ajax({
            //             url: "<?php echo e(route('ajax.getAgentsBasedOnStatus')); ?>",
            //             type: 'POST',
            //             dataType: 'json',
            //             data: {
            //                 all_agents: all_agents,
            //             },
            //             success: function(result) {
            //                 $("#search_agents").html('');

            //                 // make option and append to select #search_agents
            //                 let options = "";
            //                 result?.data.map(item => {
            //                     options +=  `<option value="${item.id}">${item.full_name}</option>`;

            //                 });
            //                 $("#search_agents").html(options);

            //                 $('#search_agents').select2({
            //                     width: '100%',
            //                     placeholder: 'Select Agent',
            //                     allowClear: true,
            //                 });
            //             },
            //             error: function(jqXhr, json, errorThrown) {
            //                 console.log()
            //             },
            //         });

        });



        $(document).on('change', 'tbody tr .selectable-checkbox', function() {
            <?php if(auth()->user()->hasRole('Agent')): ?>
                if ($('tbody tr .selectable-checkbox:checked').length > 5) {
                    $(this).prop('checked', false);
                    showToastify("You can choose a maximum of 5 leads at once.", "error")
                }
            <?php endif; ?>

            $(this).parents('tr').toggleClass('selected');
            var checked = $(this).is(':checked');
            if (!checked) {
                $(this).prop('checked', false);
            } else {
                $(this).prop('checked', true);
            }
            updateMultiOption();
        });
        $(document).on('change', '#checkAll', function() {
            updateMultiOption();
        })

        function takeLead(id) {

            $.ajax({
                url: "<?php echo e(route('admin.work.leads.assign.leads.myself')); ?>",
                type: 'POST',
                dataType: 'json',
                data: {
                    id: id,
                },
                success: function(result) {
                    if (result.status == '1') {
                        showToastify(result.msg, "success")
                        dataTable.row().draw();
                        $('.multi_options').remove();
                        getLead(id)
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

        function takeArchivedLead(id) {
            $.ajax({
                url: "<?php echo e(route('admin.work.leads.assign.archived.leads.myself')); ?>",
                type: 'POST',
                dataType: 'json',
                data: {
                    id: id,
                },
                success: function(result) {
                    if (result.status == '1') {
                        showToastify(result.msg, "success")
                        dataTable.row().draw();
                        $('.multi_options').remove();
                        getLead(id)
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
        $(document).on('click', '#bulk_assign_to_myself', function() {
            Swal.fire({
                    html: '<div class="mt-3"><div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you sure?</h4><p class="text-muted mx-4 mb-0">Are you sure you want to take these Lead(s)?</p></div></div>',
                    showCancelButton: !0,
                    confirmButtonClass: "btn btn-success w-xs me-2 mb-1",
                    confirmButtonText: "Yes, I am sure.",
                    cancelButtonClass: "btn btn-soft-danger w-xs mb-1",
                    buttonsStyling: !1,
                    showCloseButton: !0
                })
                .then(function(t) {
                    if (t.isConfirmed) { // on confirm delete the category
                        selfAssign();
                    }
                })
        })



        function selfAssign() {
            if (ids_array = [], document.getElementsByName("chk_child").forEach((function(e) {
                    if (1 == e.checked) {
                        var t = e.value;
                        ids_array.push(t)
                    }
                })), "undefined" != typeof ids_array && ids_array.length > 0) {
                console.log(ids_array)
                $.ajax({
                    url: "<?php echo e(route('admin.work.leads.bulk.assign.leads.myself')); ?>",
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        ids: ids_array,
                    },
                    success: function(result) {
                        if (result.status == '1') {
                            showToastify(result.msg, "success")
                            $("#selectAction").val('').trigger("change")
                            $('#agent_dropdown').removeClass('d-none');
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
        }

        $(document).on('click', '#bulk_archived_assign_to_myself', function() {
            Swal.fire({
                    html: '<div class="mt-3"><div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you sure?</h4><p class="text-muted mx-4 mb-0">Are you sure you want to take these Archived Lead(s)?</p></div></div>',
                    showCancelButton: !0,
                    confirmButtonClass: "btn btn-success w-xs me-2 mb-1",
                    confirmButtonText: "Yes, I am sure.",
                    cancelButtonClass: "btn btn-soft-danger w-xs mb-1",
                    buttonsStyling: !1,
                    showCloseButton: !0
                })
                .then(function(t) {
                    if (t.isConfirmed) { // on confirm delete the category
                        selfAssignArchived();
                    }
                })
        })


        function selfAssignArchived() {
            if (ids_array = [], document.getElementsByName("chk_child").forEach((function(e) {
                    if (1 == e.checked) {
                        var t = e.value;
                        ids_array.push(t)
                    }
                })), "undefined" != typeof ids_array && ids_array.length > 0) {
                console.log(ids_array)
                $.ajax({
                    url: "<?php echo e(route('admin.work.leads.bulk.assign.archived.leads.myself')); ?>",
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        ids: ids_array,
                    },
                    success: function(result) {
                        if (result.status == '1') {
                            showToastify(result.msg, "success")
                            $("#selectAction").val('').trigger("change")
                            $('#agent_dropdown').removeClass('d-none');
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
        }




        $(document).on('click', '#bulk_reassign_btn', function() {

            $("#bulkReassignLeadsModal").modal({
                backdrop: 'static',
                keyboard: false
            })
            // $("#selectAction").val('').trigger("change")
            $('#inlineRadio1').attr('checked', 'checked');
            $('#agent_dropdown').removeClass('d-none');
            $('#telesales_dropdown').addClass('d-none');
            $('#telesales_head_dropdown').addClass('d-none');

            $("#generalModalForm").trigger("reset")
            $("#generalModal").modal("hide")
            $("#checkAll").prop("checked", this.checked)
            $('#agents_dropdown').val('').trigger('change');
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

            let all_listing_on = '';
            let all_archived_on = '';

            let all_listing_off = 'd-none';
            let all_archived_off = 'd-none';
            let showAssignOption = true;

            if (all_listing_val) {
                all_listing_on = 'd-none';
                all_listing_off = '';
            }
            if (only_archive) {
                all_archived_on = 'd-none';
                all_archived_off = '';
            }

            <?php if(auth()->user()->hasRole('Agent')): ?>
                showAssignOption = !all_listing_val && !only_archive && !portal_lead;
            <?php endif; ?>

            if (countCheckedCheckboxes >= 1) {

                if ($('.multi_options').length === 0) {

                    $(`<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['Leads Bulk Action', 'Leads Self Assign', 'Leads Transfer to Leadpool'])): ?><div class="multi_options" style="display:inline-block; margin-left:10px;">

                          <div class="btn-group">
                            <button class="btn btn-primary btn-sm dropdown-toggle action-bulk" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Bulk Actions
                            </button>
                            <div class="dropdown-menu">
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['Leads Assign Agent'])): ?>
                                <a class="dropdown-item ${showAssignOption ? '' : 'd-none'}" href="javascript:;" id="bulk_reassign_btn">Assign</a>
                                <?php endif; ?>

                                <?php if(auth()->user()->hasRole('Agent')): ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Leads Self Assign')): ?>
                                <a class="dropdown-item ${all_listing_off}" href="javascript:;" id="bulk_assign_to_myself">Take these lead(s)</a>
                                <?php endif; ?>

                                   <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Leads Self Assign')): ?>
                                <a class="dropdown-item ${all_archived_off}" href="javascript:;" id="bulk_archived_assign_to_myself">Take these Archived lead(s)</a>
                                <?php endif; ?>

                                <?php endif; ?>



                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['Leads Archive', 'Leads Delete'])): ?>
                                 <div class="dropdown-divider"></div>


                                 <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Leads Archive')): ?>
                                <button type="button" class="dropdown-item" id="archiveBtn" data-id=""><i class"mdi-delete-empty mdi"></i>  Archive</button>
                                <?php endif; ?>

                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Leads Delete')): ?>
                                 <a class="dropdown-item" href="javascript:;" onclick="deleteMultiple()">Delete</a>
                                 <?php endif; ?>
                                 <?php endif; ?>

                            </div>
                        </div>
                   </div><?php endif; ?>`).insertAfter('.dt-buttons');
                }
                <?php if(auth()->user()->hasRole('Agent')): ?>
                    if ($('#all_listing').hasClass("active")) {

                    } else {
                        // $('.multi_options').addClass('d-none');
                    }
                <?php endif; ?>
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
                        url: isUpdate ? "<?php echo e(route('admin.work.leads.update')); ?>" :
                            "<?php echo e(route('admin.work.leads.store')); ?>",
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
                                showToastify(result.msg, "error")
                                // showToastify(result.msg, "error")
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

                            if (jqXhr.status == 0) {
                                showToastify(result.msg, "success")
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
                if (checkQualifiedLead()){
                    save();
                }else{
                    $('#saveBtn').prop('disabled', false);
                }
            }

        });

        $('.status_Unsuccessful').click(function() {
            if (mode == 'edit') {
                // let = previous_status = $('#lead_status_id').val();
                $('#unsuccessfull_lead_id').val($('#id').val());
                $('#preFillOption2').val('').trigger('change');
                $('#unsuccessfullReasonModal').modal('show');
            }

        });

        $('.status_Qualified').click(function() {
            if (mode == 'edit') {
                selectedLead.validateQualifiedNoteCheck = true;
                selectedLead.validateQualifiedInputCheck = true;
                checkQualifiedLead();
            }
        });

        $(document).on("submit", "#criteriaForm", function(e) {
            $("#leadCirteriaSaveBtn").prop('disabled', true);
            e.preventDefault();
            var formData = new FormData($("#criteriaForm")[0]);
            $.ajax({
                url: isUpdate ? "<?php echo e(route('admin.work.leads.store.criteria')); ?>" :
                    "<?php echo e(route('admin.work.leads.store.criteria')); ?>",
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
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Leads Edit')): ?>
                let id = $(this).data('id');
                editData(id);
            <?php endif; ?>

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
                    url: "<?php echo e(route('admin.work.leads.meeting.status')); ?>",
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

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Lead Meeting Status Change')): ?>
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
            <?php endif; ?>
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

        $('#showInactiveAgents').change(function() {
            if ($(this).is(':checked')) {
                $('#search_inactive_agents').prop('checked', true);
            } else {
                $('#search_inactive_agents').prop('checked', false);
            }
        })

        $('#getActivityLog').click(function() {
            let id = $('#id').val();
            getActivityLog(id);
        })

        $('#getNotesLog').click(function() {
            let id = $('#id').val();
            getLeadNotes(id);
        })



        $(document).on("change", "#search_my_data", function() {
            if ($(this).is(':checked')) {
                $("#search_agents").val("<?php echo e(auth()->user()->id); ?>").trigger("change");
                $("#search_lead_statuses").val("1").trigger("change");
                $("#searchSearchBtn").trigger("click")
                dataTable.column(8).visible(true);
                dataTable.column(9).visible(true);
            } else {
                $("#search_agents").val(null).trigger("change");
                $("#search_lead_statuses").val(null).trigger("change");
                $("#searchSearchBtn").trigger("click")
                dataTable.column(8).visible(false);
                dataTable.column(9).visible(false);
            }

        })

        $(document).on("click", ".change-task-status", function() {
            let task_id = $(this).parent(".dropdown-menu").data("id");
            let status = $(this).data("status");

            function changeTaskStatus() {
                $.ajax({
                    url: "<?php echo e(route('admin.work.leads.task.status')); ?>",
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

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Lead Task Status Change')): ?>
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
            <?php endif; ?>
        })



        function addEvent(type, refreshBtnId = null) {
            let agent = isSelect2Initiated($("#agent_id")) ? $("#agent_id").select2("data")[0] : null;
            let property = $("#property_id").select2("data")[0];
            let agent_id = $("#agent_id").val();
            <?php if(auth()->user()->hasRole('Telesales')): ?>
                agent_id = "<?php echo e(auth()->user()->id); ?>";
            <?php endif; ?>
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
                    id: agent_id,
                    name: agent?.text || ""

                },
                property: {
                    id: property?.id || null,
                    title: property?.text || null
                }
            };
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Events Create')): ?>
                getEventModal(data, refreshBtnId);
            <?php endif; ?>
        }

        function viewEvent(id) {
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Events View')): ?>
                showEventViewModal(id);
            <?php endif; ?>
        }

        function editEvent() {
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Events Edit')): ?>
                showEventEditForm();
            <?php endif; ?>
        }

        function getMeetingLog(id) {
            let statusSelect = "";
            <?php echo json_encode(\App\Models\Meeting::STATUS_SELECT, 15, 512) ?>?.
            map(item => {
                statusSelect +=
                    `<a class="dropdown-item change-meeting-status" href="javascript:void(0)" data-status="${item}">${item}</a>`;
            });
            let alertMsg = `<div class="card-animate">
                                <div class="card-body">
                                    <div class="d-flex mb-3 text-center">
                                        <div class="flex-grow-1">
                                            <lord-icon src="<?php echo e(asset('assets/json/icons/wired-outline-981-consultation.json')); ?>" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:55px;height:55px"> </lord-icon>
                                        </div>


                                    </div>
                                    <h3 class="mb-2 text-center"><span class="counter-value text-muted text-center" data-target="97685">No Meetings</span><small class="text-muted fs-13"></small></h3>
                                    <h6 class="text-muted mb-0"></h6>
                                </div>
                            </div>`;
            let meetingStatusChangePermission = "<?php echo e(auth()->user()->can('Lead Meeting Status Change')); ?>";

            $.ajax({
                url: "<?php echo e(route('admin.events.all')); ?>",
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
                        // let element = document.getElementById("meetingLogs");
                        // element.scrollTop = element.scrollHeight;

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
            <?php echo json_encode(\App\Models\Meeting::STATUS_SELECT, 15, 512) ?>?.
            map(item => {
                statusSelect +=
                    `<a class="dropdown-item change-meeting-status" href="javascript:void(0)" data-status="${item}">${item}</a>`;
            });
            let alertMsg = `<div class="card-animate">
                                <div class="card-body">
                                    <div class="d-flex mb-3 text-center">
                                        <div class="flex-grow-1">
                                            <lord-icon src="<?php echo e(asset('assets/json/icons/wired-outline-63-home.json')); ?>" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:55px;height:55px"> </lord-icon>
                                        </div>


                                    </div>
                                    <h3 class="mb-2 text-center"><span class="counter-value text-muted text-center" data-target="97685">No Viewings Scheduled</span><small class="text-muted fs-13"></small></h3>
                                    <h6 class="text-muted mb-0"></h6>
                                </div>
                            </div>`;
            let meetingStatusChangePermission = "<?php echo e(auth()->user()->can('Lead Meeting Status Change')); ?>";

            $.ajax({
                url: "<?php echo e(route('admin.events.all')); ?>",
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
                        // let element = document.getElementById("viewingLogs");
                        // element.scrollTop = element.scrollHeight;

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
            <?php echo json_encode(\App\Models\Task::STATUS_SELECT, 15, 512) ?>?.
            map(item => {
                statusSelect +=
                    `<a class="dropdown-item change-task-status" href="javascript:void(0)" data-status="${item}">${item}</a>`;
            });
            let alertMsg = `<div class="card-animate">
                                <div class="card-body">
                                    <div class="d-flex mb-3 text-center">
                                        <div class="flex-grow-1">
                                            <lord-icon src="<?php echo e(asset('assets/json/icons/wired-outline-245-edit-document.json')); ?>" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:55px;height:55px"> </lord-icon>
                                        </div>


                                    </div>
                                    <h3 class="mb-2 text-center"><span class="counter-value text-muted text-center" data-target="97685">No Tasks</span><small class="text-muted fs-13"></small></h3>
                                    <h6 class="text-muted mb-0"></h6>
                                </div>
                            </div>`;

            let changeTaskStatusPermission = "<?php echo e(auth()->user()->can('Lead Task Status Change')); ?>";
            $.ajax({
                url: "<?php echo e(route('admin.events.all')); ?>",
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
                        // let element = document.getElementById("taskLogs");
                        // element.scrollTop = element.scrollHeight;
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
                url: "<?php echo e(route('admin.work.leads.get')); ?>",
                type: "get",
                data: {
                    id: id
                },
                success: function(result) {
                    // mode = "view";
                    selectedLead = result.data;
                    if (result.data?.property) {
                        let item = result.data?.property;
                        let option =
                            `<option data-status="${item.publish_status}" data-for="${item.for}" value="${item.id}">${item.ref_no} - ${item.property_category?.title || ""} - ${item.unit_no || "s"} - ${item.location}</option>`;
                        $("#property_id").html(option);
                    }
                    $('.open_sub_status').removeClass('active');
                    $('.open_sub_status').find('span').addClass('randomClass');
                    $('.closed_sub_status').find('span').addClass('randomClass');
                    $('#notes-log-div_Placeholder').addClass('d-none');
                    $('#activity-log-div2').html('');
                    $('#notes').html('');
                    $('#no_activity').removeClass('d-none');
                    $('#no_notes').removeClass('d-none');
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


                    <?php if(auth()->user()->region_id == 2): ?>
                        $('#property_id').attr('required', false);
                        $('.property_req').html('');
                    <?php endif; ?>
                    // console.table(result);
                    if (result.status == '1') {

                        //$('#criteriaTabLoader').removeClass('d-none');
                        dataObj = result.data;
                        $('#listingLeadConversion').addClass('d-none');
                        $('#listingConvertor').addClass('d-none');
                        $('#viewListingLead').addClass('d-none');

                        $('#lead_id').removeClass('d-none');
                        $('#ref_no').text(result.data.id);
                        $('#lead_ref').val(result.data.id);
                        // alert(result.data.id);
                        $("#locations").val('');
                        $('#messageDiv').removeClass("d-none")
                        Object.keys(result.data).map((index) => {
                            if ('dob' == index) {
                                $(`#${index}`).val(globalDate(result.data[index])).trigger("change")
                            } else {
                                $(`#${index}`).val(result.data[index]).trigger("change")
                            }
                        })
                        if (result.data?.for == 'Unspecified' && $('#is_admin').val() == 'false') {
                            $('#for').val("").trigger("change");
                        }

                        if (result.data?.agent_id == null) {
                            <?php if(auth()->user()->hasRole('Agent')): ?>
                                $('#editBtn').addClass('d-none');
                            <?php endif; ?>
                        }

                        if (result.data?.source_id != null) {
                            $('#source_id').attr('disabled', true);
                            <?php if(auth()->user()->hasRole('Super Admin')): ?>
                                if (mode == 'edit') {
                                    $('#source_id').attr('disabled', false);
                                }
                            <?php endif; ?>
                        }
                        if (result.data?.lead_medium_id != null) {
                            $('#lead_medium_id').attr('disabled', true);
                            <?php if(auth()->user()->hasRole('Super Admin')): ?>
                                if (mode == 'edit') {
                                    $('#lead_medium_id').attr('disabled', false);
                                }
                            <?php endif; ?>
                        }
                        mobileInput = initPhoneValidator()
                        phoneInput = initPhoneValidator("phone")

                        if (mode == 'view') {
                            $(".phoneValidation").html("");
                            $("#phone").removeClass("is-invalid");
                            $(".mobileValidation").html("");
                            $("#mobile").removeClass("is-invalid");
                        }

                        if (result.data?.mobile != null) {

                            if (mode == 'edit') {
                                if (mobileNumberValidatorFalied == 1) {
                                    $('#mobile').attr('disabled', false);
                                } else {
                                    $('#mobile').attr('disabled', true);
                                }
                            }



                            <?php if(auth()->user()->hasRole('Super Admin')): ?>
                                if (mode == 'edit') {
                                    $('#mobile').attr('disabled', false);
                                }
                            <?php endif; ?>
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
                        } else {
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
                        getDuplicateLeads(result.data.id)
                        getLeadNotes(result.data.id);
                        getActivityLog(result.data.id);
                        if (result.data?.mobile == '***********') {
                            $('#start-call-button').addClass('d-none');
                        } else {
                            $('#start-call-button').removeClass('d-none');
                        }


                        if (result.data?.agent_id == null) {
                            $('#takeLead').removeClass('d-none');
                        } else {
                            $('#takeLead').addClass('d-none')
                        }

                        if (result.data?.is_archive == true) {
                            $('#takeArchiveLead').removeClass('d-none');
                        } else {
                            $('#takeArchiveLead').addClass('d-none')
                        }

                        if (result.data?.whatsapp == '***********') {
                            $('#whatapp-button').addClass('d-none');
                        } else {
                            $('#whatapp-button').removeClass('d-none');
                        }


                        if (result.data?.criteria != null) {
                            toggleCirteriaForm(false);
                            $("#criteriaForm").trigger("reset");
                            if (result.data.agent_id == null) {
                                $("#bitrix_data_note").html(replaceNumbersWithAsterisks(result.data?.criteria
                                    ?.bitrix_data));

                            } else {
                                $("#bitrix_data_note").html(result.data?.criteria?.bitrix_data);
                            }

                            Object.keys(result.data?.criteria).map((index) => {
                                if (index != "id") {
                                    $(`#${index}`).val(result.data?.criteria[index]).trigger("change")
                                }
                                $('#criteria_id').val(result.data?.criteria?.id);

                                var jsonString = result.data.criteria.view;
                                var jsonArray = JSON.parse(jsonString);

                                // Set the value of the 'view' element with the first value from the JSON array
                                $('#view').val(jsonArray).trigger('change');
                                // alert(result.data?.criteria?.max_price)


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

                            initMaxPriceInput(result.data?.criteria?.max_price)
                            initMinPriceInput(result.data?.criteria?.min_price)
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
                        var url = "<?php echo e(route('admin.work.leads.index')); ?>" + "?id=" + result.data.id;
                        window.history.pushState('data', '', url);

                        // getLeadNotes()
                        topFunction()
                        viewAgentList(id)
                        //getCallLog(id)
                        //  getMeetingLog(id)
                        // getViewingLog(id)
                        //  getTask(id)
                        // getActivityLog(id)
                        getResponseTime(id)
                        const widgetObj = {
                            id: id,
                            model_key: 'lead_id',
                            call_log: 1,
                            meeting_log: 1,
                            task_log: 0,
                            followup_log: 1,
                            viewing_log: isTeleSales ? '0' : 1,

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


                        if (result.data.lead_type?.name) {
                            if (result.data.lead_type.name) {

                                $('#type_id').val(result.data.type_id).trigger("change");

                                $('#listingLeadConversion').removeClass('d-none');
                                $('#listingConvertor').removeClass('d-none');
                                $('#listingConvertor').attr('disabled', false);

                                if (result.data.converted_to_listing_lead) {
                                    $('#editBtn').addClass('d-none');
                                    $('#listingConvertor').addClass('d-none');
                                    $('#listingConvertor').attr('disabled', true);
                                    $('#listingConvertor').addClass('d-none');

                                    var url = "<?php echo e(route('listings.leads.index')); ?>" + "?id=" + result.data
                                        .converted_to_listing_lead;
                                    $('#viewListingLead').removeClass('d-none');
                                    $('#viewListingLead').attr('href', url);

                                } else {
                                    // $('#listingConvertor').click();
                                    $('#viewListingLead').addClass('d-none');

                                }

                            }
                        } else {
                            $('#listingLeadConversion').addClass('d-none');
                        }

                    }

                }
            });
        }

        function checkQualifiedLead(){

            let shouldContinue = true;

            if (selectedLead?.validateQualifiedNoteCheck){
                $('#qualified_lead_id').val($('#id').val());
                $('#qualified_lead_note').val('').trigger('change');
                $('#qualifiedNoteModal').modal('show');
                shouldContinue = false;
            }

            if (selectedLead?.validateQualifiedInputCheck){

                let messages = [];

                if ($("#first_name").val() == ""){
                    messages.push("The first name is required to qualify this lead.");
                }

                if ($("#for").val() == ""){
                    messages.push("The purpose is required to qualify this lead.");
                }

                if ($("#sub_communities").val() == ""){
                    messages.push("The location is required to qualify this lead.");
                    $("#sub_communities").prop("required", true)
                }

                if (messages.length > 0){
                    shouldContinue = false;
                }

                messages.map(message => {
                    showToastify(message, "error")
                })
            }

            if(!selectedLead?.validateQualifiedInputCheck){
                $("#sub_communities").prop("required", false)
            }

            return shouldContinue;
        }

        const generateKycLink = () => {
            let id = $('#id').val();
            let doc_type = 'lead-kyc-form-individual';
            if (dataObj.lead_meta != null) {
                for (const [key, value] of Object.entries(dataObj.lead_meta)) {
                    if (value.meta_key == 'type' && value.meta_value == 'COMPANY') {
                        doc_type = 'lead-kyc-form-entity';
                    }
                }
            }
            let url = "<?php echo e(route('admin.documents.index')); ?>" + "?file_slug=" + doc_type + "&lead_id=" + id;
            // redirect to url
            window.open(url, '_blank');
        }

        function viewData(id) {
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Leads View')): ?>

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
            <?php endif; ?>
        }


        $(document).on('click', '#editBtn', function(event) {
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Leads Edit')): ?>

                editData($('#id').val());
                mode == 'edit';
                $('#editBtn').addClass('d-none');
                $('#message').attr('readonly', true);
            <?php endif; ?>
        });

        function editData(id) {
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Leads Edit')): ?>
                isUpdate = true;
                $("#col-logs").addClass("d-none")
                $(".inputs").prop("disabled", false)
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Lead status change')): ?>
                    $("#lead_status_id").prop("disabled", false);
                <?php else: ?>
                    $("#lead_status_id").prop("disabled", true);
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Lead sub status change')): ?>
                    $("#lead_sub_status_id").prop("disabled", false);
                <?php else: ?>
                    $("#lead_sub_status_id").prop("disabled", true);
                <?php endif; ?>
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
            <?php endif; ?>
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
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Leads Delete')): ?>
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
                                url: "<?php echo e(route('admin.work.leads.delete')); ?>",
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
            <?php endif; ?>
        }

        function deleteMultiple() {
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Bulk Delete Leads')): ?>
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
                                    url: "<?php echo e(route('admin.work.leads.delete.multi')); ?>",
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
            <?php endif; ?>
        }


        $("#checkAll").change(function() {
            <?php if(auth()->user()->hasRole('Agent')): ?>
                $(".selectable-checkbox").prop("checked", false)
                $(".selectable-checkbox").slice(0, 5).prop("checked", true)
                showToastify("You can only select 5 leads at a time", "error")
            <?php else: ?>
                $(".selectable-checkbox").prop("checked", this.checked)
            <?php endif; ?>


        });

        function locationSelect2() {

            $('#communities').select2({
                placeholder: 'Community',
                allowClear: true,
                closeOnSelect: false,
                ajax: {
                    url: '<?php echo e(route('ajax.community.select2')); ?>',
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
                    url: '<?php echo e(route('ajax.sub-community.property.select2')); ?>',
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
                    url: '<?php echo e(route('ajax.community.select2')); ?>',
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
                    url: '<?php echo e(route('ajax.sub-community.property.select2')); ?>',
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
            minimumInputLength: 0,
            ajax: {
                url: "<?php echo e(route('admin.work.leads.autocomplete')); ?>",
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: '[' + item.ref_no + '] ' + (item.title ?? "No Name"),
                                id: item?.id ?? '-',
                                unit_no: item?.unit_no ?? '-',
                                ref_no: item?.ref_no ?? '-',
                                location: item?.community_property?.name ?? '-',
                                type: item?.property_category?.title ?? '-',
                                beds: item?.bed?.name ?? '-',
                                first_image: item?.first_image?.thumbnail ?? '-',
                            }
                        })
                    };
                },
                cache: true
            },
            templateResult: formatProperty2,

        });





        function formatProperty2(e) {
            let f_unit_no = e.unit_no;
            let f_ref_no = e.ref_no;
            let f_location = e.location;
            let f_type = e.type;
            let f_beds = e.beds;
            let f_first_image = e.first_image ? e.first_image :
                'https://ui-avatars.com/api/?name=' + f_unit_no + '&color=7F9CF5&background=random';

            return e.id ? $('<span><img src="' + f_first_image + '" class="img-flag rounded-5" width="18" /> ' + f_ref_no +
                ' - ' + f_type + ' - ' + f_unit_no +
                "</span><span class=' small ml-5 d-inline-block'> </span> <span class=' small ml-5 d-inline-block'> <i class='mdi mdi-google-maps '></i>" +
                f_location +
                "</span> ") : e.text;
        }
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

        $(document).on("click", ".statusFilter", function() {
            //    if #append-here has  #

            let status = $(this).data("status");
            if ($("#append-here").has("#search_lead_statuses").length > 0) {

                $('#search_lead_statuses').val(status).trigger('change');
                $("#search_lead_statuses option:selected").removeAttr("selected");
                $("#search_lead_statuses option[value='" + status + "']").attr("selected", true);
                $("#search_lead_statuses").trigger("change");
                $("#searchSearchBtn").trigger("click");
            } else {
                $('#applicator').val('lead_statuses').trigger('change');
                setTimeout(function() {
                    $('#search_lead_statuses').val(status).trigger('change');
                    $("#search_lead_statuses option:selected").removeAttr("selected");
                    $("#search_lead_statuses option[value='" + status + "']").attr("selected", true);
                    $("#search_lead_statuses").trigger("change");
                    $("#searchSearchBtn").trigger("click");
                }, 1000);
            }

            // let status = $(this).data("status");
            // $('#search_lead_statuses').val(status).trigger('change');
            // $("#search_lead_statuses option:selected").removeAttr("selected");
            // $("#search_lead_statuses option[value='" + status + "']").attr("selected", true);
            // // Trigger the change event to update Select2
            // $("#search_lead_statuses").trigger("change");
            // // Trigger the click event on the search button
            // $("#searchSearchBtn").trigger("click");

        });

        $(document).on("click", ".statusFilterClear", function() {
            // $("#search_lead_statuses option:selected").removeAttr("selected").trigger("change");
            $("#search_lead_statuses").val(null).trigger("change");
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
                    url: "<?php echo e(route('ajax.PropertyCriteria')); ?>",
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


        function getDuplicateLeads(id) {
            $.ajax({
                url: "<?php echo e(route('admin.work.leads.duplicateLeads')); ?>",
                type: 'POST',
                dataType: 'html',
                data: {
                    id: id,
                },

                success: function(result) {
                    $('#duplicateLeads').html(result);
                },
                error: function(jqXhr, json, errorThrown) {
                    console.log(json)
                },
            });
        }


        $('#type_ids').change(function() {
            $('#listing_type_id').val($(this).val());
            if ($(this).val() != null) {
                $('#listingLeadConversion').removeClass('d-none');
                $('#listingConvertor').removeClass('d-none');
                $('#listingConvertor').attr('disabled', false);
                $('#listingLeadConversion').data('value', $(this).val());
            } else {
                $('#listingLeadConversion').addClass('d-none');
                $('#listingConvertor').attr('disabled', true);

            }
        })






        $('#listingConvertor').click(function() {



            $("#addListingModal").modal({
                backdrop: 'static',
                keyboard: false
            })
            $('#steparrow-gen-info').addClass('show active');
            $('#steparrow-description-info').removeClass('show active');
            $('#steparrow-gen-info-tab').addClass('active');
            $('#steparrow-gen-info-tab').removeClass('done');
            $('#steparrow-description-info-tab').removeClass('active');

            $('.resetFields').val('').trigger('change');
            $('#listing_location_address').html();
            $('#showDuplicationMessage').html();
            $('#listing_agent_id').val($('#agent_id').val());
            $('#listing_agent_id').trigger('change');
            $('#listing_telesales_id').val($('#telesales_id').val());
            $('#listing_telesales_id').trigger('change');

            $('#listing_medium_id').val($('#lead_medium_id').val());
            $('#listing_medium_id').trigger('change');

            $('#listing_source_id').val($('#source_id').val());
            $('#listing_source_id').trigger('change');


            $('#listing_telesales_id').val($('#telesales_id').val());
            $('#listing_telesales_id').trigger('change');




            $('#listing_first_name').val($('#first_name').val());
            $('#listing_last_name').val($('#last_name').val());
            $('#listing_mobile').val($('#mobile').val());
            $('#listing_email').val($('#email').val());
            $('#listing_email').val($('#email').val());


            var firstOption = $('#for option:selected:first').val();
            $('#listing_for').val(firstOption).trigger('change');

            // resetModal();
            $('#pills-experience').removeClass('active');
            $('#pills-experience').removeClass('show active');

            $('#addListingModal').modal('show');
        })


        // let listing_mobile_Input = initPhoneValidator("listing_mobile", "full_listing_mobile")
        // $(document).on("change", "#listing_mobile", function() {
        //     checkContactExistence(listing_mobile_Input.getNumber(), "mobile", "Contact");
        // });


        function checkContactExistence(value, column, model, id = "null") {
            let agent_id = $("#listing_agent_id").val();
            $(`#${column}-exists`).html("")
            $(`#${column}`).removeClass("is-invalid")
            $(`#${column}`).next(".invalid-feedback").text(`enter a valid ${column}.`);
            // Removed agent id from below ajax to resolve a problem. If this makes issue pass the agent_id to below line data: {value, column, model, id},
            if (value.length > 0) {
                $.ajax({
                    url: "<?php echo e(route('ajax.is-value-unique')); ?>",
                    type: "get",
                    data: {
                        value,
                        column,
                        model,
                        id
                    },
                    success: function(result) {
                        if (!result.data) {
                            // $(`#${column}`).addClass("is-invalid")
                            // $(`#${column}`).next(".invalid-feedback").text(`The ${column} has already been taken.`)
                            // $(`#${column}-exists`).html(`This lead already exists <a href="javascript:void(0)" data-id="${result.id}" class="text-primary" id="request-access-${column}">request access</a>`)
                            // console.log(result);
                            showToastify(`The ${column} has already been taken.`, "error")
                            existingContact = result.id;
                            existingContactDetails = result.existing_data;
                            alert("This contact already exists. Please select the contact from the list.")
                        }
                    }
                });
            }
        }

        $(document).ready(function() {
            $('.checkDuplicate').change(function() {
                checkDuplicate();

            })

            $('#saveListingForm').click(function() {
                event.preventDefault();
                checkDuplicate();
                console.log(allValid2);
                console.log(allValid);
                console.log(isDuplicate);
                // alert(isDuplicate)
                if (allValid2 && allValid && isDuplicate == false) {

                    var formData = new FormData($("#leadToListingForm")[0]);
                    console.log(formData)
                    //
                    $.ajax({
                        url: "<?php echo e(route('listings.leads.leadToListing')); ?>",
                        type: 'POST',
                        dataType: 'json',
                        data: formData,
                        cache: false,
                        processData: false,
                        contentType: false,
                        success: function(result) {
                            if (result.status == 1) {
                                $('#addListingModal').modal('hide');
                                showToastify("Lead Converted Successfully", "success");
                                $('#steparrow-description-info').removeClass('show active');
                                $('#steparrow-description-info-tab').removeClass('active');
                                $('#steparrow-description-info-tab').addClass('done');
                                $('#pills-experience').addClass('active');
                                $('#pills-experience').addClass('show active');
                                $('#editBtn').addClass('d-none');
                                $('#saveBtn').addClass('d-none');
                                $('#listingConvertor').addClass('d-none');
                                $('#listingConvertor').attr('disabled', true);
                                $('#listingConvertor').addClass('d-none');
                                $('.inputs').prop('disabled', true);
                                var url = "<?php echo e(route('listings.leads.index')); ?>" + "?id=" +
                                    result.id;
                                $('#viewListingLead').removeClass('d-none');
                                $('#viewListingLead').attr('href', url);
                                dataTable.ajax.reload();
                            } else if (result.status == 3) {
                                showToastify(result.msg, "error");
                            } else {

                                showToastify("Something Wend Wrong.", "error");
                            }
                        },
                        error: function(jqXhr, json, errorThrown) {
                            showToastify("Something Wend Wrong.", "error");

                        },
                    });
                }
                // if(isDuplicate==true){
                //     alert('Truue')
                // }

            })


        })

        function replaceNumbersWithAsterisks(inputText) {
            // Define a regular expression pattern to match numbers with more than 6 digits
            var pattern = /\d{7,}/g;

            // Replace matched numbers with asterisks
            var modifiedText = "";

            if (inputText) {
                modifiedText = inputText.replace(pattern, "*******");
            }


            // Return the modified text
            return modifiedText;
        }

        $(document).on("click", "#send-sms", function(event) {

            $("#send-bulk-sms").modal("show");
        });

        $(document).on('submit', '#send-bulk-sms-form', function() {
            event.preventDefault();
            let msg_body = $("#bulk-message").val();
            if (msg_body == "") {
                showToastify("Message body is required", "error")
                return;
            }


            let contacts_count = $('#contacts-count').text();
            Swal.fire({
                    html: '<div class="mt-3"><div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you sure?</h4><p class="text-muted mx-4 mb-0">Are you sure you want to send this message to ' +
                        contacts_count +
                        '?</p></div></div>',
                    showCancelButton: !0,
                    confirmButtonClass: "btn btn-success w-xs me-2 mb-1",
                    confirmButtonText: "Yes, I am sure.",
                    cancelButtonClass: "btn btn-soft-danger w-xs mb-1",
                    buttonsStyling: !1,
                    showCloseButton: !0
                })
                .then(function(t) {
                    if (t.isConfirmed) { // on confirm delete the category
                        filtersData = $("#searchForm").serialize();
                        filtersData = urldecode(filtersData);
                        filtersData = QueryStringToJSON(filtersData);
                        let message = $("#bulk-message").val();
                        let campaign_name = $("#campaign_name").val();
                        let custom_link = $("#custom_link").val();

                        $.ajax({
                            type: "POST",
                            url: "<?php echo e(route('contacts.bulk.sms.leads.sendMsg')); ?>",

                            data: {
                                all_listings: all_listing_val,
                                only_archive: only_archive,

                                id: filtersData?.search_id_find,
                                id_operator: filtersData?.search_id_find_operator,

                                first_name: filtersData?.search_first_name,
                                search_first_name_operator: filtersData?.search_first_name_operator,

                                email: filtersData?.search_email,
                                search_email_operator: filtersData?.search_email_operator,

                                last_name: filtersData?.search_last_name,
                                search_last_name_operator: filtersData?.search_last_name_operator,


                                lead_statuses: filtersData?.search_lead_statuses,
                                lead_statuses_operator: filtersData?.search_lead_statuses_operator,

                                mobile: filtersData?.search_mobile,
                                mobile_operator: filtersData?.search_mobile_operator,


                                agents: filtersData?.search_agents,
                                agents_operator: filtersData?.search_agents_operator,

                                managers: filtersData?.search_managers,
                                managers_operator: filtersData?.search_managers_operator,

                                regions: filtersData?.search_region,
                                regions_operator: filtersData?.search_region_operator,

                                completion_status: filtersData?.search_completion_status,
                                completion_status_operator: filtersData
                                    ?.search_completion_status_operator,


                                previous_agent: filtersData?.search_previous_agents,
                                previous_agent_operator: filtersData?.search_previous_agents_operator,

                                updated_by: filtersData?.search_updated_by,
                                updated_by_operator: filtersData?.search_updated_by_operator,


                                property: filtersData?.search_property_ref,
                                property_operator: filtersData?.search_property_ref_operator,

                                for: filtersData?.search_for,
                                for_operator: filtersData?.search_for_operator,

                                // telesales_head: filtersData?.filter_telesales_head,
                                // telesales_head_operator: filtersData?.filter_telesales_head_operator,

                                telesales: filtersData?.filter_telesales,
                                telesales_operator: filtersData?.filter_telesales_operator,


                                lead_sources: filtersData?.search_lead_sources,
                                lead_sources_operator: filtersData?.search_lead_sources_operator,

                                lead_types: filtersData?.search_lead_type,
                                lead_types_operator: filtersData?.search_lead_type_operator,

                                lead_medium: filtersData?.search_lead_medium,
                                lead_medium_operator: filtersData?.search_lead_medium_operator,




                                priorities: filtersData?.search_priorities,
                                priorities_operator: filtersData?.search_priorities_operator,


                                genders: filtersData?.search_genders,
                                genders_operator: filtersData?.search_genders_operator,

                                nationality: filtersData?.search_nationalities,
                                nationalities_operator: filtersData?.search_nationalities_operator,

                                created_from: filtersData?.search_created_from,
                                date_created_operator: filtersData?.search_created_from_operator,

                                updated_from: filtersData?.search_updated_from,
                                date_updated_operator: filtersData?.search_updated_from_operator,


                                follow_up_date: filtersData?.search_follow_up_date,
                                follow_up_date_operator: filtersData?.search_follow_up_date_operator,

                                bitrix_data: filtersData?.search_bitrix_data,
                                bitrix_data_operator: filtersData?.search_bitrix_data_operator,

                                deleted: filtersData?.search_deleted,
                                archive: filtersData?.search_archive,
                                incomplete_lead: filtersData?.search_incomplete_leads,

                                inactive_agents: filtersData?.search_inactive_agents,

                                leadpool: filtersData?.leadpool,


                                communities: filtersData?.filter_communities,
                                communities_operator: filtersData?.search_communities_operator,

                                sub_communities: filtersData?.filter_sub_communities,
                                sub_communities_operator: filtersData?.filter_sub_communities_operator,

                                locations: filtersData?.filter_locations,
                                community_properties: filtersData?.community_properties,

                                furnished: filtersData?.search_furnished,
                                filter_furnished_operator: filtersData?.search_furnished_operator,

                                lead_category: filtersData?.search_lead_category,
                                lead_category_operator: filtersData?.search_lead_category_operator,
                                min_bed_id: filtersData?.filter_min_bed_id,
                                max_bed_id: filtersData?.filter_max_bed_id,
                                min_bath_id: filtersData?.filter_min_bath_id,
                                max_bath_id: filtersData?.filter_max_bath_id,
                                min_price: filtersData?.filter_min_price,
                                max_price: filtersData?.filter_max_price,


                                message: message,
                                campaign_name: campaign_name,
                                custom_link: custom_link
                            },
                            success: function(response) {
                                $("#bulk-message").val("");
                                $("#send-bulk-sms").modal("hide");
                                showToastify(result.msg, "success")

                            },
                            error: function(xhr, status, error) {
                                console.error(error);
                            }
                        });
                        return;
                    }
                })
        })
        $(document).on('keyup', '.sms-counter', function() {

            var message = $('#bulk-message').val();
            $('#msg-preview').html(message);
            var custom_link = $('#custom_link').val();
            var link = `<a href="${custom_link}">${custom_link}</a>`;


            if ($('#send_checking_link').is(':checked')) {
                $('#link-preview').html('http://axc.ae/abcd');
            } else {
                $('#link-preview').html(link);
            }


            var messageLength = message.length;
            var remaining = 160 - messageLength;

            var msgpreview = $('#msg-preview').text();

            var linkpreview = $('#link-preview').text();


            var msgpreviewCount = msgpreview.length;
            var linkpreviewCount = linkpreview.length;
            var totalCharacter = msgpreviewCount + linkpreviewCount;

            $('#total-character').html(totalCharacter);
            $('#total-parts').html(Math.ceil(totalCharacter / 160));

        });
        const openDealModal = () => {
            $('#leadDealModal').modal('show');
            $("#deal_lead_id").val($("#id").val());
        }


        $(document).on("submit", "#leadDealForm", function(e) {
            e.preventDefault();
            $("#leadDealSubmit").prop('disabled', true);
            $("#leadDealSubmit").html(`<span class="d-flex align-items-center">
            <span class="spinner-grow flex-shrink-0" role="status">
                <span class="visually-hidden">Loading...</span>
            </span>
            <span class="flex-grow-1 ms-2">
                Loading...
            </span>
        </span>`);
            var formData = new FormData($("#leadDealForm")[0]);
            $.ajax({
                url: "<?php echo e(route('admin.work.deals.create.from.lead')); ?>",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function(result) {
                    if (result.status == '1') {
                        showToastify(result.msg, "success")
                        $("#leadDealModal").modal('hide');
                        $("#leadDealSubmit").html('Create Deal'); //enable submit button
                        $("#leadDealSubmit").prop('disabled', false); //enable submit button
                        let deal_id = result.data.id;
                        let viewUrl = "<?php echo e(route('admin.deals_revamped.index')); ?>";
                        window.location.href = viewUrl + '?deal_id=' + deal_id;
                    } else if (result.status == '2') {
                        $("#leadDealSubmit").html('Create Deal');
                        $("#leadDealSubmit").prop('disabled', false);
                        let error_message = '';
                        $.each(result.errors, function(key, value) {
                            error_message += '' + key + ': ' + value + ' | ';
                        });
                        showToastify(error_message, "error")
                    } else {
                        $("#leadDealSubmit").html('Create Deal');
                        $("#leadDealSubmit").prop('disabled', false);
                        showToastify(result.msg, "error")
                    }
                },
                error: function(jqXhr, json, errorThrown) {
                    $("#leadDealSubmit").html('Create Deal');
                    $("#leadDealSubmit").prop('disabled', false);
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
        const openOfferModal = () => {
            $('#leadOfferModal').modal('show');
            $("#offer_lead_id").val($("#id").val());
        }
        $(document).on("submit", "#leadOfferForm", function(e) {
            e.preventDefault();
            $("#leadOfferSubmit").prop('disabled', true);
            $("#leadOfferSubmit").html(`<span class="d-flex align-items-center">
            <span class="spinner-grow flex-shrink-0" role="status">
                <span class="visually-hidden">Loading...</span>
            </span>
            <span class="flex-grow-1 ms-2">
                Loading...
            </span>
        </span>`);
            var formData = new FormData($("#leadOfferForm")[0]);
            $.ajax({
                url: "<?php echo e(route('admin.work.leads.offer.store')); ?>",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function(result) {
                    if (result.status == '1') {
                        showToastify(result.message, "success")
                        $("#leadOfferModal").modal('hide');
                        $("#leadOfferSubmit").html('Create Offer'); //enable submit button
                        $("#leadOfferSubmit").prop('disabled', false); //enable submit button
                        $("#leadOfferForm")[0].reset();
                    } else if (result.status == '2') {
                        $("#leadOfferSubmit").html('Create Offer');
                        $("#leadOfferSubmit").prop('disabled', false);
                        let error_message = '';
                        $.each(result.errors, function(key, value) {
                            error_message += '' + key + ': ' + value + ' | ';
                        });
                        showToastify(error_message, "error")
                    } else {
                        $("#leadDealSubmit").html('Create Deal');
                        $("#leadDealSubmit").prop('disabled', false);
                        showToastify(result.msg, "error")
                    }
                },
                error: function(jqXhr, json, errorThrown) {
                    $("#leadDealSubmit").html('Create Deal');
                    $("#leadDealSubmit").prop('disabled', false);
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

        $(document).on("click", "#saveQualifiedStatus", function() {
            $('#screen_loader').removeClass('d-none');
            let note = $('#qualified_lead_note').val();
            let id = $('#qualified_lead_id').val();
            if (note == "") {
                showToastify("Please Enter Note For Qualified Lead.", "error")
                return;
            } else {
                $.ajax({
                    url: "<?php echo e(route('admin.work.leads.add-qualified-note')); ?>",
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        id: id,
                        note: note,
                    },
                    success: function(result) {
                        $('#screen_loader').addClass('d-none');
                        if (result.status == '1') {
                            selectedLead.validateQualifiedNoteCheck = false;
                            showToastify(result.msg, "success")
                            $("#qualified_lead_note").val("")
                            $('#qualifiedNoteModal').modal('hide');
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
                    },
                });
            }
        })

        $(document).on("click", "#saveTransferToPortalLeadPool", function() {
            $('#screen_loader').removeClass('d-none');
            let note = $('#transfer_portal_lead_pool_note').val();
            let id = $('#transfer_portal_lead_pool_lead_id').val();
            if (note == "") {
                showToastify("Please Enter Note For Transfer Portal Lead Pool.", "error")
                return;
            } else {
                $.ajax({
                    url: "<?php echo e(route('admin.work.leads.transfer-to-portal-lead-pool')); ?>",
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        id: id,
                        note: note,
                    },
                    success: function(result) {
                        $('#screen_loader').addClass('d-none');
                        if (result.status == '1') {

                            mode = 'view';
                            showToastify(result.msg, "success")
                            $("#archive_note").val("")
                            $('#transferToPortalLeadPoolModal').modal('hide');
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
                    },
                });
            }
        })

        $(document).on("input", "#qualified_lead_note", function (){
            if($(this).val().length >= 35){
                $("#qualified_lead_note-help").addClass("text-success").removeClass("text-danger")
            }else{
                $("#qualified_lead_note-help").addClass("text-danger").removeClass("text-success")
            }
        })
    </script>





    <script src="<?php echo e(URL::asset('/assets/js/pages/leads.js')); ?>"></script>
    
    <?php echo $__env->yieldContent('pagejs'); ?>
    <?php echo $__env->yieldContent('page_scripts'); ?>
    <?php echo $__env->yieldPushContent('script-bottom'); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/lead/index.blade.php ENDPATH**/ ?>