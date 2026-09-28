<?php $__env->startSection('title'); ?>
    Contacts
<?php $__env->stopSection(); ?>
<?php $__env->startSection('style'); ?>
    <link href="<?php echo e(URL::asset('assets/libs/sweetalert2/sweetalert2.min.css')); ?>" rel="stylesheet" type="text/css" />
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
    </style>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" type="text/css" />
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="row">

        <div class="col-lg-12" id="top-bar">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title mb-0 ">Contacts</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Work</a></li>
                            <li class="breadcrumb-item active">Contacts</li>
                        </ol>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="form-check form-switch form-switch-right form-switch-md">
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Contacts Create')): ?>
                                <button type="button" id="newBtn" class="btn btn-danger add-btn action-add">
                                    <i class="ri-add-line align-bottom me-1"></i> Add New
                                </button>
                            <?php endif; ?>
                            <button type="submit" class="btn btn-success d-none action-save" id="saveBtn">Save</button>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Contacts Edit')): ?>
                                <button type="button" id="editBtn" class="btn btn-info d-none action-edit">Edit</button>
                            <?php endif; ?>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Contacts Additional Agents Bulk Assign')): ?>
                                <button type="button" id="bulkAssignBtn" class="btn btn-dark d-none action-assign">Assign to
                                    agents</button>
                            <?php endif; ?>
                            <button type="button" class="btn btn-light d-none action-close" id="closeBtn">Close</button>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Contacts Delete')): ?>
                                <button class="btn btn-soft-danger action-delete d-none" id="action-delete"
                                    onClick="deleteMultiple()"><i class="ri-delete-bin-2-line"></i></button>
                            <?php endif; ?>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="collapse slideIn" id="collapseContactForm">
                <div class="">
                    <div class="">
                        <div>
                            <form class="row needs-validation" id="mainForm" novalidate>
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-4">
                                                    <div class="row">
                                                        <input type="hidden" id="id" name="id">
                                                        <input type="hidden" id="dailer_id" name="dailer_id"
                                                            value="<?php echo e(auth()->user()->id); ?>">
                                                        <h5 class="mt-2">Personal Information</h5>
                                                        <div class="col-4 mt-2">First Name*</div>
                                                        <div class="col-8 mt-2">
                                                            <input type="text" class="form-control inputs"
                                                                name="first_name" id="first_name"
                                                                placeholder="Enter first name" required>
                                                            <div class="invalid-feedback">
                                                                Please enter first name.
                                                            </div>
                                                        </div>
                                                        <div class="col-4 mt-2">Last Name*</div>
                                                        <div class="col-8 mt-2">
                                                            <input type="text" class="form-control inputs"
                                                                name="last_name" id="last_name"
                                                                placeholder="Enter last name" required>
                                                            <div class="invalid-feedback">
                                                                Please enter last name.
                                                            </div>
                                                        </div>
                                                        <div class="col-4 mt-2">Mobile*</div>
                                                        <div class="col-6 mt-2" id="mobileDiv1">
                                                            <input type="tel" class="form-control inputs checkValidity"
                                                                name="mobile" id="mobile"
                                                                placeholder="Enter mobile number" required>
                                                            <div class="text-danger mobileValidation">

                                                            </div>

                                                            <div class="invalid-feedback">
                                                                Please enter a valid mobile number.
                                                            </div>
                                                        </div>
                                                        <div class="col-2 mt-2" id="call_now_button">


                                                            <a href="javascript:void(0);" title="Call Now"
                                                                id="start-call-button"
                                                                class="avatar-title bg-info text-white fs-15 rounded ">
                                                                <i class="ri-phone-line"></i>
                                                            </a>
                                                            <a href="javascript:void(0);" title="Calling"
                                                                id="calling-button"
                                                                class="avatar-title bg-info text-white fs-15 rounded d-none">
                                                                <span class="d-flex align-items-center">
                                                                    <span class="spinner-grow flex-shrink-0"
                                                                        role="status">
                                                                        <span class="visually-hidden">Loading...</span>
                                                                    </span>
                                                                </span>
                                                            </a>

                                                        </div>


                                                        <div class="col-4 mt-2">Whatsapp</div>
                                                        <div class="col-md-6 mt-2 pr-0" id="whatsappDiv1">
                                                            <input type="text" class="form-control inputs"
                                                                name="whatsapp" id="whatsapp"
                                                                placeholder="Enter whatsapp number">
                                                            <div class="invalid-feedback"> Please enter a valid whatsapp
                                                                number. </div>
                                                        </div>
                                                        <div class="col-md-2 mt-2" id="whatsappDiv2">
                                                            <a href="javascript:void(0);" title="Whatsapp Now"
                                                                id="whatapp-button"
                                                                class="avatar-title bg-success text-white fs-15 rounded ">
                                                                <i class="ri-whatsapp-line"></i>
                                                            </a>
                                                        </div>

                                                        <div class="col-4 mt-2">Email</div>
                                                        <div class="col-8 mt-2">
                                                            <input type="email" class="form-control inputs  mb-1"
                                                                name="email" id="email"
                                                                placeholder="Enter email address">
                                                            <span class="text-muted"><small>Kindly be aware that your
                                                                    listing will require a valid email address from the
                                                                    owner in order to be published.</small></span>
                                                            <div class="invalid-feedback">
                                                                Please enter a valid email address.
                                                            </div>
                                                        </div>
                                                        <div class="col-4 mt-2">Gender</div>
                                                        <div class="col-8 mt-2">
                                                            <select class="form-control select2 inputs" name="gender"
                                                                id="gender">
                                                                <option value="" selected disabled>Select gender
                                                                </option>
                                                                <option value="Male">Male</option>
                                                                <option value="Female">Female</option>
                                                            </select>
                                                            <div class="invalid-feedback">
                                                                Please choose gender.
                                                            </div>
                                                        </div>
                                                        <div class="col-4 mt-2">Nationality</div>
                                                        <div class="col-8 mt-2">
                                                            <select class="form-control select2 inputs"
                                                                name="nationality_id" id="nationality_id">
                                                                <option value="" selected disabled>Select nationality
                                                                </option>
                                                                <?php $__currentLoopData = $countries ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                    <option value="<?php echo e($country->id); ?>">
                                                                        <?php echo e($country->nationality); ?></option>
                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                            </select>
                                                            <div class="invalid-feedback">
                                                                Please choose nationality.
                                                            </div>
                                                        </div>


                                                    </div>
                                                </div>

                                                <div class="col-4">
                                                    <div class="row">
                                                        <h5 class="mt-2">Details</h5>

                                                        <div class="col-4 mt-2">Address</div>
                                                        <div class="col-8 mt-2">
                                                            <input type="text" class="form-control inputs"
                                                                name="address" id="address"
                                                                placeholder="Enter Address">
                                                            <div class="invalid-feedback">
                                                                Please enter address.
                                                            </div>
                                                        </div>


                                                        <div class="col-4 mt-2">Date of birth</div>
                                                        <div class="col-8 mt-2">
                                                            <input type="date" class="form-control inputs"
                                                                name="dob" id="dob"
                                                                placeholder="Enter date of birth">
                                                            <div class="invalid-feedback">
                                                                Please enter date of birth.
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="col-4 mt-2">Passport</div>
                                                        <div class="col-8 mt-2">
                                                            <input type="text" class="form-control inputs"
                                                                name="passport" id="passport"
                                                                placeholder="Enter passport">
                                                            <div class="invalid-feedback">
                                                                Please enter a valid passport.
                                                            </div>
                                                        </div>
                                                        <div class="col-4 mt-2">Passport Expiry</div>
                                                        <div class="col-8 mt-2">
                                                            <input type="date" class="form-control inputs"
                                                                name="passport_expiry" id="passport_expiry"
                                                                placeholder="Enter passport expiry">
                                                            <div class="invalid-feedback">
                                                                Please enter passport expiry.
                                                            </div>
                                                        </div>
                                                        <div class="col-4 mt-2">Phone</div>
                                                        <div class="col-8 mt-2">
                                                            <input type="text"
                                                                class="form-control inputs checkValidity" name="phone"
                                                                id="phone" placeholder="Enter phone number">
                                                            <div class="text-danger phoneValidation">

                                                            </div>

                                                            <div class="invalid-feedback phoneValidation">
                                                                Please enter a valid phone number.
                                                            </div>
                                                        </div>
                                                        <div class="col-4 mt-2">Company</div>
                                                        <div class="col-8 mt-2">
                                                            <input type="text" class="form-control inputs"
                                                                name="company" id="company"
                                                                placeholder="Enter company">
                                                            <div class="invalid-feedback">
                                                                Please enter company.
                                                            </div>
                                                        </div>
                                                        <div class="col-4 mt-2">Website</div>
                                                        <div class="col-8 mt-2">
                                                            <input type="text" class="form-control inputs"
                                                                name="website" id="website"
                                                                placeholder="Enter website">
                                                            <div class="invalid-feedback">
                                                                Please enter website.
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="row">
                                                        <h5 class="mt-2">Action</h5>

                                                        <?php echo $__env->make('admin.templates.agents_select2_form', [
                                                            'id' => 'agent_id',
                                                            'label' => 'Agents',
                                                            'agents' => $agents ?? [],
                                                            'layout' => 'horizontal',
                                                            'required' => 'false',
                                                        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                                                        
                                                        <div class="col-4 mt-2">Contact Type</div>
                                                        <div class="col-8 mt-2">
                                                            <select class="form-control select2 inputs"
                                                                name="contact_types[]" id="contact_types" multiple>
                                                                <?php $__currentLoopData = $types ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                    <option value="<?php echo e($type->id); ?>"><?php echo e($type->name); ?>

                                                                    </option>
                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                            </select>
                                                            <div class="invalid-feedback">
                                                                Please choose contact type.
                                                            </div>
                                                        </div>
                                                        <div class="col-4 mt-2">Contact Source*</div>
                                                        <div class="col-8 mt-2">
                                                            <select class="form-control select2 inputs"
                                                                name="contact_source_id" id="contact_source_id" required>
                                                                <option value="" selected disabled>Select contact
                                                                    source</option>
                                                                <?php $__currentLoopData = $sources ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $src): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                    <option value="<?php echo e($src->id); ?>"><?php echo e($src->name); ?>

                                                                    </option>
                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                            </select>
                                                            <div class="invalid-feedback">
                                                                Please choose contact source.
                                                            </div>
                                                        </div>
                                                        <div class="col-4 mt-2">Contact Status</div>
                                                        <div class="col-8 mt-2">
                                                            <select class="form-control select2 inputs"
                                                                name="contact_status_id" id="contact_status_id">
                                                                <option value="" selected disabled>Select contact
                                                                    status</option>
                                                                <?php $__currentLoopData = $statuses ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                    <option value="<?php echo e($status->id); ?>">
                                                                        <?php echo e($status->name); ?></option>
                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                            </select>
                                                            <div class="invalid-feedback">
                                                                Please choose contact status.
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div id="properties" class="d-none">
                                                        <hr>
                                                        <h4>Properties</h4>
                                                        <div class="table-responsive" style="max-height: 250px">
                                                            <div data-simplebar style="max-height: 160px;">
                                                                <div data-simplebar style="max-height: 215px;">
                                                                    <ul class="list-group" id="owner_properties_div">
                                                                    </ul>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="d-none" id="submitBtn"></button>
                                </div>
                            </form>
                            <div class="row">
                                <div class="col-6 addHide">
                                    <div class="card">

                                        <div class="card-body mt-0 form-steps">
                                            <ul class="nav nav-tabs nav-tabs-custom nav-success nav-justified mb-0"
                                                role="tablist">


                                                <li class="nav-item active">
                                                    <a class="nav-link active" data-bs-toggle="tab" href="#notesTab"
                                                        role="tab" aria-selected="true"
                                                        onclick="scrollAtBottom('notes');">
                                                        <i class="mdi mdi-note-text align-middle"></i>
                                                        Notes</a>
                                                </li>
                                                <?php if(!auth()->user()->hasRole(['Agent'])): ?>
                                                    <li class="nav-item addHide">
                                                        <a class="nav-link" data-bs-toggle="tab"
                                                            href="#additionalAgentTab" role="tab"
                                                            aria-selected="false">
                                                            <i class="mdi mdi-account-group-outline align-middle"></i>
                                                            Additional Agents</a>
                                                    </li>
                                                <?php endif; ?>
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
                                                <div class="col-12 d-none" id="addOnAgentDiv">
                                                    <div class="">
                                                        <div class="card-header align-items-center d-flex">
                                                            <h4 class="card-title mb-0 flex-grow-1"></h4>
                                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Contacts Additional Agents Add')): ?>
                                                                <div class="flex-shrink-0">
                                                                    <div class="dropdown card-header-dropdown"
                                                                        style="    width: 225px;">
                                                                        <select name='agent_id_add' id="add_on_agent"
                                                                            placeholder="Select Agent" class="form-control">
                                                                            <option selected disabled>Select Agent</option>
                                                                            <?php $__currentLoopData = $agents ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                                <option value="<?php echo e($agent->id); ?>">
                                                                                    <?php echo e($agent->name); ?>

                                                                                </option>
                                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                        </select>

                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>

                                                        <div class="card-body">


                                                            <div class="table-responsive table-card">
                                                                <table
                                                                    class="table table-borderless table-nowrap align-middle mb-0">
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
                                            <div class="tab-pane" id="activityLogTab" role="tabpanel">
                                                <div class="col-12" id="activityLogDiv">
                                                    <div class="">
                                                        <div class="card-body">
                                                            <div class="profile-timeline acitivity-timeline scrollable-notes"
                                                                id="scrollableActiviyLog"></div>
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
            </div>
        </div>
    </div>
    <?php if ($__env->exists('admin.contact.include.filterForm')) echo $__env->make('admin.contact.include.filterForm', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <div class="row">
        <div class="col-xxl-12">
            <div class="table-sticky-header-container" id="table-sticky-header">
                <ul class="nav nav-tabs nav-tabs-custom nav-success mb-1" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link All  statusFilterClear active" data-bs-toggle="tab" id="All"
                            href="#" role="tab" aria-selected="false" tabindex="-1">
                            All
                        </a>
                    </li>

                    <?php $__currentLoopData = $types ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link All  statusFilter text-uppercase" data-bs-toggle="tab"
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

                    <table id="datatable" class="table align-middle" style="width:100%">
                        <thead class="table-light2">
                            <tr class="text-uppercase">
                                <th scope="col" style="width: 50px;">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="checkAll" value="option"
                                            data-com.bitwarden.browser.user-edited="yes">
                                    </div>
                                </th>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Company</th>
                                <th>Email</th>
                                <th>Mobile</th>
                                <th>Gender</th>
                                <th>Types</th>
                                <th>Updated</th>
                                <!-- <th>Action</th> -->
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php echo $__env->make('admin.contact.include.modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('admin.events.include.modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <div id="eventModalsDiv"></div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <?php echo $__env->make('admin.partials.datatable', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    

    <script src="<?php echo e(URL::asset('/assets/js/plugins/event.js')); ?>?v=<?php echo e(time()); ?>"></script>
    <script src="<?php echo e(URL::asset('/assets/js/plugins/widgets.js')); ?>"></script>
    <script>
        let currentPage = "Lead";
        var isUpdate = false;
        let dataObj = {};
        let defaultStatusId = 1;
        let defaultSubStatusId = 4;
        let mode = 'view';

        let existingContact = null;
        let existingContactDetails = null;

        let remove_additional_agent = "<?php echo e(route('admin.work.contacts.remove.agent')); ?>";
        let fetch_additional_agents = "<?php echo e(route('admin.work.contacts.get.agents')); ?>";
        let add_more_agent_url = "<?php echo e(route('admin.work.contacts.add.more.agent')); ?>";

        let makeCallLink = "<?php echo e(route('admin.work.contacts.start-call')); ?>";
        let bulkAssignLink = "<?php echo e(route('admin.work.contacts.assign-agents')); ?>";
        //event/calendar related urls
        let getEventUrl = "<?php echo e(route('admin.events.get')); ?>";
        let currentUserRole = "<?php echo e(auth()->user()->roles->first()->name); ?>";
        let currentUserId = "<?php echo e(auth()->user()->id); ?>";
        let propertySelect2 = "<?php echo e(route('ajax.properties.select2')); ?>";
        let eventCreateUrl = "<?php echo e(route('admin.events.store')); ?>";
        let eventUpdateUrl = "<?php echo e(route('admin.events.update')); ?>";
        let getAllEventsType = "<?php echo e(route('admin.events.type.get.all')); ?>";
        let contactSelect2url = "<?php echo e(route('ajax.contacts.select2')); ?>";
        let leadSelect2url = "<?php echo e(route('ajax.leads.select2')); ?>";
        let eventPropertyDetailsUrl = "<?php echo e(route('admin.listing.properties.get')); ?>";
        let getAttendeesUrl = "<?php echo e(route('ajax.getUsersSelect2')); ?>";
        let getEventModalUrl = "<?php echo e(route('admin.events.calendar.form.modal')); ?>";
        let eventObj = {};
        let isEventPage = false;

        let canRemoveAdditionalAgents = false;
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Contacts Additional Agents Remove')): ?>
            canRemoveAdditionalAgents = true;
        <?php endif; ?>
        let call_now_button = 0;

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Dial my contact')): ?>
            call_now_button = 1;
        <?php endif; ?>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Dial other contact')): ?>
            call_now_button = 2;
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['Dial my contact', 'Dial other contact'])): ?>
            call_now_button = 3;
        <?php endif; ?>
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


        $(document).ready(function() {
            let isNewTab = false;
            setTimeout(function() {

                var open_ref_id = "<?php echo e(Request('contact')); ?>";
                if (open_ref_id) {
                    isNewTab = true;
                    $('.dataTables_filter .form-control').val(open_ref_id).trigger('keyup');
                }

            }, 500);


            setTimeout(function() {
                if (isNewTab) {
                    $('#datatable tbody tr:eq(0)').click();
                }
            }, 1000);
        })

        $("#activityLogButton").click(function() {
            $('#scrollableActiviyLog').scrollTop($('#scrollableActiviyLog')[0].scrollHeight);
        });

        $("#agent_id").select2({
            placeholder: "Select agent",
            allowClear: true,
        })
        $("#search_agents").select2({
            placeholder: "Select agent",
            allowClear: true,
        })


        $("#gender").select2({
            placeholder: "Select gender",
            allowClear: true,
        })
        $("#search_genders").select2({
            placeholder: "Select gender",
            allowClear: true,
        })

        $("#nationality_id").select2({
            placeholder: "Select nationality",
            allowClear: true,
        })

        $("#contact_types").select2({
            placeholder: "Select contact types",
            allowClear: true,
            closeOnSelect: false
        })

        $("#search_contact_types").select2({
            placeholder: "Select contact types",
            allowClear: true,
            closeOnSelect: false
        })

        $("#contact_source_id").select2({
            placeholder: "Select contact source",
            allowClear: true,
        })
        $("#search_contact_sources").select2({
            placeholder: "Select contact source",
            allowClear: true,
        })

        $("#contact_status_id").select2({
            placeholder: "Select contact status",
            allowClear: true,
        })
        $("#search_contact_statuses").select2({
            placeholder: "Select contact status",
            allowClear: true,
        })

        $('#meeting_attendees').select2({
            placeholder: 'Select attendees',
            allowClear: true,
            closeOnSelect: false,
            dropdownParent: $('#meetingModal')
        });
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
        flatpickr('#dob', {
            "maxDate": new Date(),
            enableTime: false,
            dateFormat: datePickerFormat()
        });
        flatpickr('#follow_up_date', {
            enableTime: false,
            dateFormat: datePickerFormat()
        });
        flatpickr('#passport_expiry', {
            enableTime: false,
            dateFormat: datePickerFormat()
        });

        // $('#task_priority').select2({
        //     placeholder: 'Select priority',
        //     allowClear: true,
        //     dropdownParent: $('#taskModal')
        // });

        $('#assign_agents').select2({
            placeholder: 'Select agents',
            allowClear: true,
            closeOnSelect: false,
            dropdownParent: $('#assignAgentsModal')
        });
        $('#bulk_leads_agent_id').select2({
            placeholder: 'Select agents',
            allowClear: true,
            closeOnSelect: true,
            dropdownParent: $('#bulkReassignContactModal')
        });
        let element = document.getElementById("activityLogTab");
        element.scrollTop = element.scrollHeight

        function getProperties(id) {
            $("#owner_properties_div").html("");
            $("#properties").removeClass("d-none");
            $.ajax({
                url: "<?php echo e(route('ajax.properties')); ?>",
                type: 'GET',
                dataType: 'json',
                data: {
                    owner_id: id
                },
                success: function(result) {
                    let tableBody = "";
                    result?.data.map(item => {
                        // tableBody += `<tr>
                    //                 <td>${item.ref_no}</td>
                    //                 <td>${item.unit_no}</td>
                    //                 <td>${item.property_category?.title || ""}</td>
                    //                 <td>${item.community?.name || ""}</td>
                    //                 <td>${item.sub_community?.name || ""}</td>
                    //                 <td>${item.bed?.name || ""}</td>
                    //                 <td>${item.price?.price || ""}</td>
                    //                 <td>${item.agent?.name || ""}</td>
                    //                 <td>${item.publish_status}</td>
                    //             </tr>`;



                        let url = "<?php echo e(route('admin.listing.index')); ?>" + "?ref_number=" + item.ref_no;

                        let publishStatus =
                            `<span class="badge badge-soft-${item.publish_status == "PUBLISHED" ? "success" : (item.publish_status == "DRAFT" ? "primary" : item.publish_status == "PENDING" ? "warning" : item.publish_status == "POCKET" ? "secondary": "danger")}">${item.publish_status}</span>`;


                        tableBody += ` <li class="list-group-item">
                                   <a href="${url}" target="_blank"><div class="d-flex align-items-center">

                                        <div class="flex-grow-1">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0">
                                                    <img src="${item.first_image ? item.first_image.thumbnail : "<?php echo e(URL::asset('assets/images/logo/favicon-32x32.png')); ?>"}" alt="" class="avatar-xs rounded" />
                                                </div>
                                                <div class=" ms-2">
                                                    <h6 class="fs-14 mb-0">${item.ref_no} - ${item.property_category?.title || ""}</h6>
                                                    <small class="text-muted">${item.community?.name || ""} ${item.sub_community?.name || ""}</small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="flex-shrink-0">
                                            ${publishStatus}<br>
                                            <span class="text-warning">AED ${item.price?.price || ""}</span>
                                        </div>

                                    </div></a>
                                </li>`;


                    })
                    $("#owner_properties_div").html(tableBody);
                },
                error: function(result) {
                    console.log(result);
                }
            });
        }

        function activityChangeDataFormat(data) {
            let html = '';
            let val = '';
            for (const [key, value] of Object.entries(data)) {

                if (key == 'created_at' || key == 'updated_at') {
                    html += key + ' - ' + globalDateTime(value) + '<br>';

                } else {
                    val = value == null ? '' : value;
                    html += key + ' - ' + val + '<br>';

                }
            }
            return html;
        }
        $(document).on('click', ".show-hiddenLog", function() {
            let eleid = $(this).data('id');
            console.log(eleid);
            if ($("#hiddenLog" + eleid).hasClass('d-none')) {
                $("#hiddenLog" + eleid).removeClass('d-none');
            } else {
                $("#hiddenLog" + eleid).addClass('d-none');
            }

        });

        function addEvent(type, refreshBtnId = null) {

            let agent = $("#agent_id").select2("data")[0];
            let data = {
                date: moment().format("YYYY-MM-DD HH:mm:ss"),
                type: type,
                contact: {
                    id: $("#id").val(),
                    name: $("#first_name").val() + ' ' + $("#last_name").val(),
                    email: $("#contact_email").val(),
                    phone: $("#contact_phone").val(),
                },
                user: {
                    id: $("#agent_id").val(),
                    name: agent?.text || ""

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

        function getActivityLog(id) {
            $("#activityLogDiv").find(".acitivity-timeline").html("");
            $.ajax({
                url: "<?php echo e(route('admin.work.contacts.activity.log')); ?>",
                type: 'GET',
                dataType: 'json',
                data: {
                    subject_model: "Contact",
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
        $(document).on('click', '#bulk_reassign_btn', function() {

            $("#bulkReassignContactModal").modal({
                backdrop: 'static',
                keyboard: false
            })
            $('#bulk_leads_agent_id').val('').trigger('change');
            $('#bulkReassignContactModal').modal('show');
        });

        function updateMultiOption() {
            var countCheckedCheckboxes = $('input[name="chk_child"]:checked').length;
            $('#bulk_leads_count').html(countCheckedCheckboxes);
            $('#count_selected').html(countCheckedCheckboxes);
            if (countCheckedCheckboxes >= 1) {
                if ($('.multi_options').length === 0) {
                    $(`<div class="multi_options" style="display:inline-block; margin-left:10px;">
                          <div class="btn-group">
                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Bulk Actions
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="javascript:;" id="bulk_reassign_btn">Re-Assign</a>

                                <div class="dropdown-divider"></div>
                                 <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Contacts Delete')): ?>
                    <a class="dropdown-item" href="javascript:;" onclick="deleteMultiple()">Delete</a>
<?php endif; ?>
                    </div>
                </div>
           </div>`).insertAfter('.dt-button');
                    //    $('#action-delete').removeClass('d-none')
                }
            } else {
                $('.multi_options').remove();
                // $('#action-delete').addClass('d-none')

            }
        }
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

        function toggleAdvanceSearch() {
            $("#collapseFilterForm").toggleClass("show")
        }

        $(document).on("click", "#saveBtn", function() {
            $("#submitBtn").trigger("click")
        })

        // $(document).on("click", "#start-call-button", function(){
        //     let id = $("#id").val();
        //     let mobile = $("#mobile").val();
        //     if (id == "" || mobile == ""){
        //         return
        //     }
        //     makeCall()
        // })

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

        // $(document).on("click", ".statusFilter", function() {
        //     let status = $(this).data("status");
        //     $("#search_contact_types option:selected").removeAttr("selected");
        //     $("#search_contact_types option[value='" + status + "']").attr("selected", true);
        //     $("#search_contact_types").trigger("change");
        //     $("#searchSearchBtn").trigger("click");
        // });

        $(document).on("click", ".statusFilter", function() {
            //    if #append-here has  #

            let status = $(this).data("status");
            if ($("#append-here").has("#search_contact_type").length > 0) {

                $('#search_contact_type').val(status).trigger('change');
                $("#search_contact_type option:selected").removeAttr("selected");
                $("#search_contact_type option[value='" + status + "']").attr("selected", true);
                $("#search_contact_type").trigger("change");
                $("#searchSearchBtn").trigger("click");
            } else {
                $('#applicator').val('contact_type').trigger('change');
                setTimeout(function() {
                    $('#search_contact_type').val(status).trigger('change');
                    $("#search_contact_type option:selected").removeAttr("selected");
                    $("#search_contact_type option[value='" + status + "']").attr("selected", true);
                    $("#search_contact_type").trigger("change");
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
            $("#search_contact_type option:selected").removeAttr("selected").trigger("change");
            $("#search_contact_type").val("").trigger("change");
            $("#searchSearchBtn").trigger("click");
        });



        function startTimer(secs) {
            timeInSecs = parseInt(secs);
            ticker = setInterval("tick()", 1000);
        }


        $("#add_on_agent").select2({
            placeholder: "Select Agent",
            allowClear: true,
            closeOnSelect: false
        })

        $(document).on("click", "#bulkAssignBtn", function() {
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Contacts Additional Agents Bulk Assign')): ?>
                $("#assignAgentsForm").removeClass("was-validated")
                $("#assignAgentsForm").trigger("reset");
                $("#assign_agents>option").removeAttr('disabled');
                $(`#assign_agents>option[value='${dataObj.agent_id}']`).attr('disabled', 'disabled');
                let agents = [];
                dataObj?.contact_agents?.map(item => {
                    agents.push(item.user_id);
                })
                $("#assign_agents").val(agents).trigger("change")
                $("#assignAgentsModal").modal("show");
            <?php endif; ?>
        })


        function highlightRow(row) {
            row.removeClass('selected');
            dataTable.$('tr.selected').removeClass('selected');
            row.addClass('selected');
        }

        function getContactNotes() {
            let contact_id = $("#id").val();
            $("#notes").html("");
            $.ajax({
                url: "<?php echo e(route('ajax.contact-notes')); ?>",
                type: 'GET',
                dataType: 'json',
                data: {
                    contact_id
                },
                success: function(result) {
                    let notes = "";
                    result?.data.map(item => {
                        let date = globalDateTime(item.created_at)
                        let imageUrl =
                            `${item.created_by ? item.created_by.thumb_url : "<?php echo e(URL::asset('assets/images/users/avatar-8.jpg')); ?>"}`;
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
                error: function(result) {
                    console.log(result);
                }
            });
        }

        $(document).on("click", "#addNoteButton", function() {
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Contact Notes Create')): ?>
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
                formData.append("contact_id", id)
                formData.append("note", note)
                $.ajax({
                    url: "<?php echo e(route('admin.work.contacts.add-note')); ?>",
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
                            getContactNotes()
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

        let mobileInput = initPhoneValidator('mobile', 'full_mobile')
        var input = document.querySelector("#mobile");
        var iti = window.intlTelInput(input, {
            initialCountry: "ae",
        });

        input.addEventListener("input", function() {
            var selectedCountryData = iti.getSelectedCountryData();
            var countryCode = selectedCountryData.dialCode;
            $('input[name="mobile_countrycode"]').val(countryCode);
        });

        input.addEventListener("countrychange", function() {
            var selectedCountryData = iti.getSelectedCountryData();
            var countryCode = selectedCountryData.dialCode;
            $('input[name="mobile_countrycode"]').val(countryCode);
        });

        $('#mobile').on('input change', function() {
            var isValid = iti.isValidNumber();
            var fullMobile = isValid ? iti.getNumber() : iti.getNumber();

            $('input[name="full_mobile"]').val(fullMobile);
            $('input[name="mobile"]').val(fullMobile);
        });



        let phoneInput = initPhoneValidator("phone", "full_phone")

        var input2 = document.querySelector("#phone");
        var iti2 = window.intlTelInput(input2, {
            initialCountry: "ae",
        });

        input2.addEventListener("input", function() {
            var selectedCountryData = iti2.getSelectedCountryData();
            var countryCode = selectedCountryData.dialCode;
            $('input[name="phone_countrycode"]').val(countryCode);
        });

        input2.addEventListener("countrychange", function() {
            var selectedCountryData = iti2.getSelectedCountryData();
            var countryCode = selectedCountryData.dialCode;
            $('input[name="phone_countrycode"]').val(countryCode);
        });

        $('#phone').on('input change', function() {
            var isValid = iti2.isValidNumber();
            var fullPhone = isValid ? iti2.getNumber() : iti2.getNumber();

            $('input[name="full_phone"]').val(fullPhone);
            $('input[name="phone"]').val(fullPhone);
        });


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
                        if (!result.data && result.existing_data) {
                            // $(`#${column}`).addClass("is-invalid")
                            // $(`#${column}`).next(".invalid-feedback").text(`The ${column} has already been taken.`)
                            // $(`#${column}-exists`).html(`This lead already exists <a href="javascript:void(0)" data-id="${result.id}" class="text-primary" id="request-access-${column}">request access</a>`)
                            // showToastify(`The ${column} has already been taken.`, "error")

                            existingContact = result.id;
                            existingContactDetails = result.existing_data;

                        }
                    }
                });
            }
        }

        $(document).on("change", "#agent_id", function() {
            checkExistence(mobileInput.getNumber(), "mobile", "Contact", $("#id").val())
            // checkExistence($("#email").val(), "email", "Contact", $("#id").val())
        })

        // $(document).on("change", "#email", function() {
        //     checkExistence($(this).val(), "email", "Contact", $("#id").val())
        // })

        $(document).on("change", "#mobile", function() {
            $("#whatsapp").val(mobileInput.getNumber()).trigger("change");
            checkExistence(mobileInput.getNumber(), "mobile", "Contact", $("#id").val())
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

        $("#newBtn").click(function() {
            existingContact = null;
            existingContactDetails = null;
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Contacts Create')): ?>
                isUpdate = false;
                $("#owner_properties_div").html("");
                $("#properties").addClass("d-none");
                $('#mainForm').trigger('reset');
                $('#mainForm').removeClass('was-validated');
                $(".inputs").prop("disabled", false)
                $(".inputs").val("").trigger("change")
                $(".inputs").removeClass("is-invalid")
                $("#id").val("")
                $("#saveBtn, #closeBtn").removeClass("d-none");
                $("#newBtn, #editBtn, #deleteBtn, #followUpMenusTab, #bulkAssignBtn").addClass("d-none");
                $("#addOnAgentDiv, .addHide").addClass("d-none");
                toggleForm(true)

                $('#whatsappDiv1').addClass('col-md-8')
                $('#whatsappDiv2').hide()
                $('#whatsappDiv1').removeClass('col-md-6 pr-0')

                $('#mobileDiv1').addClass('col-8')
                $('#call_now_button').hide()
                $('#mobileDiv1').removeClass('col-6 3 pr-0')


                $("#col-logs").addClass("d-none");
                $("#start-call-button").removeClass("d-none");
                $("#calling-button").addClass("d-none");
                mode = 'create';
            <?php endif; ?>
        });

        $(document).on("click", "#closeBtn", function() {
            toggleForm(false)
            mode = 'view';
        })





        $(document).on('submit', '#mainForm', function(event) {
            $("#saveBtn").prop('disabled', true);
            var form = $(this);
            event.preventDefault();
            $("#mobile").val(mobileInput.getNumber())
            $("#phone").val(phoneInput.getNumber())
            var formData = new FormData($(this)[0]);
            if (existingContact != null && mode == "create") {
                let contact_agent_id = $("#agent_id").val();
                var content = document.createElement('div');
                content.innerHTML = "This owner already exists with name <strong>" +
                    existingContactDetails.first_name +
                    " " + existingContactDetails.last_name + "</strong>";

                Swal.fire({

                        html: '<div class="mt-3"><div class="mt-4 pt-2 fs-15 mx-5"><h4>Contact Already Exists!</h4><p class="text-muted mx-4 mb-0">The contact named <strong>' +
                            existingContactDetails.first_name +
                            " " + existingContactDetails.last_name +
                            '</strong> already exists. Would you like to access it?</p></div></div>',
                        showCancelButton: !0,
                        confirmButtonClass: "btn btn-danger w-xs me-2 mb-1",
                        confirmButtonText: "Yes, I want to access!",
                        cancelButtonClass: "btn btn-soft-success w-xs mb-1",
                        buttonsStyling: !1,
                        showCloseButton: !0


                    })
                    .then(function(t) {

                        if (t.isConfirmed) { // on confirm delete the category
                            $.ajax({
                                url: "<?php echo e(route('admin.work.contacts.assign-agents')); ?>",
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    id: existingContact,
                                    agents: [contact_agent_id],
                                    add_new: true,
                                    formData: form.serializeArray()
                                },
                                success: function(result) {
                                    if (result.status ==
                                        '1') { //if successfully deleted
                                        showToastify(result.msg, "success")
                                        $("#saveBtn").prop('disabled', false);

                                        let agent_id = "";
                                        agent_id = $("#agent_id").val();
                                        viewContact(result.owner.id);
                                        existingContact = null;
                                        existingContactDetails = null;

                                    } else { // something went wrong on backend
                                        showToastify(result.msg, "error")
                                        $("#saveBtn").prop('disabled', false);

                                        existingContact = null;
                                        existingContactDetails = null;
                                    }
                                },
                                error: function(result) {
                                    $("#saveBtn").prop('disabled', false); //enable submit button
                                    existingContact = null;
                                    existingContactDetails = null;
                                }
                            });
                        } else {
                            $("#saveBtn").prop('disabled', false); //enable submit button
                        }



                    })

            } else {
                $.ajax({
                    url: isUpdate ? "<?php echo e(route('admin.work.contacts.update')); ?>" :
                        "<?php echo e(route('admin.work.contacts.store')); ?>",
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
                            viewContact(result.id)
                            dataTable.row()
                                .draw(); // redraw the datatable show we don't need to reload the page to see the changes
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
                        }
                    },
                    error: function(jqXhr, json, errorThrown) {
                        mode = 'view';
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
                    },
                });
            }
        });

        let dataTable = $('#datatable').DataTable({ //datatable initialization
            "order": [
                [0, "DESC"]
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
                searchPlaceholder: "Search contacts"
            },
            createdRow: function(row, data, index) {
                $('#datatable tbody').css('cursor', 'pointer');

            },
            responsive: true,
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "<?php echo e(route('admin.work.contacts.dt')); ?>",
                type: "post",
                data: function(d) {
                    d.id_operator = filtersData?.search_id_find_operator;
                    d.id = filtersData?.search_id_find;

                    d.agents_operator = filtersData?.search_agents_operator;
                    d.agents = filtersData?.search_agents;

                    d.contact_types = filtersData?.search_contact_type;
                    d.contact_types_operator = filtersData?.search_contact_type_operator;

                    d.genders = filtersData?.search_gender_search;
                    d.genders_operator = filtersData?.search_gender_search_operator;

                    d.contact_sources = filtersData?.search_contact_sources;
                    d.contact_sources_operator = filtersData?.search_contact_sources_operator;

                    d.contact_statuses = filtersData?.search_contact_statuses;
                    d.contact_statuses_operator = filtersData?.search_contact_statuses_operator;

                    d.created_date = filtersData?.search_created_from;
                    d.created_date_operator = filtersData?.search_created_from_operator;

                    d.updated_date = filtersData?.search_updated_from;
                    d.updated_date_operator = filtersData?.search_updated_from_operator;

                    d.bitrix_id_operator = filtersData?.search_bitrix_id_operator;
                    d.bitrix_id = filtersData?.search_bitrix_id;

                    d.date_of_birth_operator = filtersData?.search_date_of_birth_operator;
                    d.date_of_birth = filtersData?.search_date_of_birth;

                    d.my_crm_id_operator = filtersData?.search_my_crm_id_operator;
                    d.my_crm_id = filtersData?.search_my_crm_id;

                    d.deleted = filtersData?.search_deleted;

                    d.mobile = filtersData?.search_mobile;
                    d.mobile_operator = filtersData?.search_mobile_operator;

                    d.email = filtersData?.search_email;
                    d.email_operator = filtersData?.search_email_operator;


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
                $(`<?php if ($__env->exists('admin.contact.include.filterButtons')) echo $__env->make('admin.contact.include.filterButtons', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>`).insertAfter(".dataTables_filter input")
                // get datatable total count
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
                    data: 'company',
                    name: 'company',
                    orderable: false,
                    visible: false
                }, {
                    data: 'email',
                    name: 'email',
                    orderable: false,
                }, {
                    data: 'mobile',
                    name: 'mobile',
                    orderable: false,
                }, {
                    data: 'gender',
                    name: 'gender',
                    orderable: false,
                }, {
                    data: null,
                    name: null,
                    orderable: false,
                },

                {
                    data: 'updated_at',
                    name: 'updated_at',
                    orderable: true,
                },
                // {
                //     data: null,
                //     orderable: false,
                // },
                // {
                //     data: null,
                //     orderable: false,
                // }
            ],
            "columnDefs": [{
                    "targets": 0,
                    orderable: true,
                    render: function(type, row, data, meta) {
                        return `<div class="form-check">
                                                <input class="form-check-input selectable-checkbox" type="checkbox" name="chk_child" value="` +
                            data.id + `">
                                            </div>`;
                    }
                },
                {
                    "targets": 2,
                    orderable: false,
                    render: function(type, row, data, meta) {
                        data.last_name = data.last_name == null ? "" : data.last_name;
                        name = data.first_name + " " + data.last_name;

                        return `<a href="javascript:void(0)" class="text-primary" onclick="viewContact('` +
                            data.id + `')">` + name + `</a>`;
                    }
                },
                {
                    "targets": 7,
                    orderable: true,
                    render: function(type, row, data, meta) {
                        let types = ""
                        data.contact_types?.map(item => {
                            types += `${item.name}, `;
                        })
                        return types.substring(0, types.length - 2)
                    }
                },

                {
                    "targets": 8,
                    orderable: true,
                    render: function(type, row, data, meta) {
                        if (data.updated_at == null) {
                            return '-';
                        } else {
                            return `<span title="${moment(data.updated_at).format('DD/MM/YYYY hh:mm:ss')}">${moment(data.updated_at).fromNow()}</span>`;
                        }
                    }
                }
                // {
                //     targets: 8,
                //     orderable: false,
                //     visible: true,
                //     /*This will hide particular column*/
                //     render: function (type, row, data, meta) {
                //         let disabled = true;
                //         <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Contacts Edit')): ?>
                //             disabled = false
                //         <?php endif; ?>

                //         if (data.status == '1') {
                //             return `<div  class="form-check form-switch form-switch-lg form-switch-success" dir="ltr">
            //                         <input ${disabled ? "disabled" : ""} type="checkbox" data-id="${data.id}" data-do="0" name="status" class="form-check-input status-change" checked></div>`;
                //         } else {
                //             return `<div  class="form-check form-switch form-switch-lg form-switch-success " dir="ltr">
            //                         <input ${disabled ? "disabled" : ""} data-id="${data.id}" data-do="1" type="checkbox" name="status" class="form-check-input status-change"></div>`;
                //         }
                //     }
                // },
                // {
                //     targets: 8,
                //     orderable: false,
                //     visible: true,
                //     render: function (type, row, data, meta) {
                //         return `<div class="dropdown d-inline-block">
            //                     <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            //                         <i class="ri-more-fill align-middle"></i>
            //                     </button>
            //                     <ul class="dropdown-menu dropdown-menu-end">
            //                         <li><a href="javascript:void(0)" data-id="` + data.id + `" class="dropdown-item edit-item-btn edit-link"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit</a></li>
            //                         <li>
            //                             <a href="javascript:void(0)" onclick="deleteData(` + data.id + `)" data-id="` + data.id + `" class="dropdown-item remove-item-btn delete-link">
            //                                 <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete
            //                             </a>
            //                         </li>
            //                     </ul>
            //                 </div>`
                //     }
                // }
            ]
        });

        $('#datatable tbody').on('change', 'input.status-change', function() {
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Contacts Edit')): ?>
                let id = $(this).data('id');
                let action = $(this).data('do');
                let formData = {
                    id: id,
                    action: action
                }
                $.ajax({
                    type: "POST",
                    url: "<?php echo e(route('admin.work.contacts.status')); ?>",
                    data: formData,
                    dataType: 'json',
                    success: function(data) {
                        if (data.status == '1') {
                            Toastify({

                                text: "Contact status updated successfully",

                                duration: 3000

                            }).showToast();
                            dataTable.row().draw();
                        }
                    },
                    error: function(data) {
                        console.log('Error:', data);
                    }
                });
            <?php endif; ?>
        });
        $('#datatable tbody').on('click', '.edit-link', function() {
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Contacts Edit')): ?>
                let id = $(this).data('id');
                viewContact(id);
                editData(id);
            <?php endif; ?>

        });


        function getAgents() {
            if ($('#showInactiveAgents').is(':checked')) {
                all_agents = 'true';
            } else {
                all_agents = 'false';
            }
            $.ajax({
                url: "<?php echo e(route('ajax.getAgentsBasedOnStatus')); ?>",
                type: 'POST',
                dataType: 'json',
                data: {
                    all_agents: all_agents,
                },
                success: function(result) {
                    $("#search_agents").html('');

                    // make option and append to select #search_agents
                    let options = "";
                    result?.data.map(item => {
                        options +=
                            `<option data-image="${item.thumbnail??item.thumb_url}" data-status="${item.status}" value="${item.id}">${item.full_name}</option>`;

                    });
                    $("#search_agents").html(options);

                    $('#search_agents').select2({
                        width: '100%',
                        placeholder: 'Select Agent',
                        allowClear: true,
                        templateResult: formatAgent,
                        templateSelection: formatAgentView
                    });
                },
                error: function(jqXhr, json, errorThrown) {
                    console.log()
                },
            });
        }

        function decodedFilterData(filtersData) {
            return {


                id_operator: filtersData?.search_id_find_operator,
                id: filtersData?.search_id_find,

                agents_operator: filtersData?.search_agents_operator,
                agents: filtersData?.search_agents,

                contact_types: filtersData?.search_contact_type,
                contact_types_operator: filtersData?.search_contact_type_operator,

                genders: filtersData?.search_gender_search,
                genders_operator: filtersData?.search_gender_search_operator,

                contact_sources: filtersData?.search_contact_sources,
                contact_sources_operator: filtersData?.search_contact_sources_operator,

                contact_statuses: filtersData?.search_contact_statuses,
                contact_statuses_operator: filtersData?.search_contact_statuses_operator,

                created_date: filtersData?.search_created_from,
                created_date_operator: filtersData?.search_created_from_operator,

                updated_date: filtersData?.search_updated_from,
                updated_date_operator: filtersData?.search_updated_from_operator,

                bitrix_id_operator: filtersData?.search_bitrix_id_operator,
                bitrix_id: filtersData?.search_bitrix_id,

                date_of_birth_operator: filtersData?.search_date_of_birth_operator,
                date_of_birth: filtersData?.search_date_of_birth,

                my_crm_id_operator: filtersData?.search_my_crm_id_operator,
                my_crm_id: filtersData?.search_my_crm_id,

                deleted: filtersData?.search_deleted,

                mobile: filtersData?.search_mobile,
                mobile_operator: filtersData?.search_mobile_operator,

                email: filtersData?.search_email,
                email_operator: filtersData?.search_email_operator,
            };
        }


        $(document).on('change', '#showInactiveAgents', function(e) {
            getAgents();
        });
        getAgents();

        dataTable.on('click', 'tbody tr', function(e) {
            var $cell = $(e.target).closest('td'),
                msg;

            //console.log('Index ==========='+$cell.index());

            if ($cell.index() != 0) {
                var data = dataTable.row(this).data();
                //
                if ($cell.index() === 11 || $cell.index() === 12) {
                    // console.log($cell);
                } else {


                    if (mode == 'create' || mode == 'edit') {

                        var r = confirm("Are you sure you want to discard the changes?");
                        if (r == true) {
                            viewContact(data.id);
                            highlightRow($(this));

                        } else {

                        }


                    } else {
                        viewContact(data.id);
                        highlightRow($(this));
                    }
                }


            } // end if
            else {}


        }); // table row click

        // $(document).ready(function(){
        //     <?php if(request()->id): ?>
        //     viewContact("<?php echo e(request()->id); ?>")
        //     <?php endif; ?>
        // })

        $(document).on('click', '#editBtn', function(event) {
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Contacts Edit')): ?>
                editData($('#id').val());
            <?php endif; ?>
        });

        function viewContact(id) {
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Contacts View')): ?>
                $("#addOnAgentDiv, #closeBtn, #followUpMenusTab, .addHide").removeClass("d-none");
                $("#saveBtn, #newBtn").addClass("d-none");
                $("#collapseContactForm").addClass("show");
                $("#editBtn, #bulkAssignBtn").removeClass("d-none");
                $(".inputs").prop("disabled", true);
                $("#mainForm").trigger("reset")
                $("#id").val(id)
                $(".inputs").val("").trigger("change")
                $(".inputs").removeClass("is-invalid")
                $("#meeting_contact_id, #task_contact_id").val(id)
                isUpdate = true; //set isUpdate to true
                getContact(id)
                $("#col-logs").removeClass("d-none")
                $("#start-call-button").removeClass("d-none");
                $("#calling-button").addClass("d-none");
            <?php endif; ?>
        }

        function getContact(id) {
            $("#call_now_button").addClass("d-none");
            $.ajax({
                url: "<?php echo e(route('admin.work.contacts.view')); ?>",
                type: "get",
                data: {
                    id: id
                },
                success: function(result) {
                    if (result.status == '1') {
                        dataObj = result.data;
                        $('#add_on_agent').val('').trigger('change');
                        Object.keys(result.data).map((index) => {
                            if ('follow_up_date' == index) {
                                $(`#${index}`).val(globalDate(result.data[index])).trigger("change")
                            } else if ('passport_expiry' == index) {
                                $(`#${index}`).val(globalDate(result.data[index])).trigger("change")
                            } else {
                                $(`#${index}`).val(result.data[index]).trigger("change")
                            }

                        })
                        if (result.data?.criteria != null) {
                            Object.keys(result.data?.criteria).map((index) => {
                                if (index != "id") {
                                    $(`#${index}`).val(result.data?.criteria[index]).trigger("change")
                                }
                            });
                        }
                        let contactTypes = [];
                        result?.data?.contact_types.map(item => {
                            contactTypes.push(item.id);
                        })
                        let editMobile = result.data?.mobile.replace(/\+/g,
                            ''); //removing existing + sign because all mobile numbers doesn't have + sign
                        $("#mobile").val("+" + editMobile).trigger("change");

                        // if((call_now_button == 1 || call_now_button == 3) && result.data?.agent_id == globalUser.id){
                        $("#call_now_button").removeClass("d-none");
                        // }
                        $(`#contact_types`).val(contactTypes).trigger("change")
                        toggleForm(true)
                        getContactNotes()
                        topFunction()
                        viewAgentList(id)
                        //widgets related
                        let widgetObj = {
                            id: id,
                            model_key: 'contact_id',
                            call_log: 1,
                            meeting_log: 1,
                            task_log: 0,
                            followup_log: 1,
                            viewing_log: 0,

                        };
                        getEventsCard(widgetObj);
                        mobileInput = initPhoneValidator("mobile", "full_mobile")
                        phoneInput = initPhoneValidator("phone", 'full_phone')


                        if (mode == 'view') {
                            $(".phoneValidation").html("");
                            $("#phone").removeClass("is-invalid");
                            $(".mobileValidation").html("");
                            $("#mobile").removeClass("is-invalid");
                        }
                        // getTask(id)
                        getActivityLog(id)
                        $('#scrollableActiviyLog').scrollTop($('#scrollableActiviyLog')[0].scrollHeight);
                        getProperties(id)
                        if ($('#whatsapp').val() == '') {
                            $('#whatsappDiv1').addClass('col-md-8')
                            $('#whatsappDiv2').hide()
                            $('#whatsappDiv1').removeClass('col-md-6 pr-0')
                        } else {
                            $('#whatsappDiv1').addClass('col-md-6 pr-0')
                            $('#whatsappDiv2').show()
                            $('#whatsappDiv1').removeClass('col-md-8')
                        }

                        if ($('#mobile').val() == '') {
                            $('#mobileDiv1').addClass('col-md-8')
                            $('#mobileDiv2').hide()
                            $('#mobileDiv1').removeClass('col-md-6 pr-0')
                        } else {
                            $('#mobileDiv1').addClass('col-md-6 pr-0')
                            $('#mobileDiv2').show()
                            $('#mobileDiv1').removeClass('col-md-8')
                        }
                        // $('.simplebar-wrapper').hide();
                        var url = "<?php echo e(route('admin.work.contacts.index')); ?>" + "?contact=" + result.data.id;
                        // console.log(url);
                        window.history.pushState('data', '', url);
                    }
                }
            });
        }

        function editData(id) {
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Contacts Edit')): ?>
                isUpdate = true;
                $("#col-logs").addClass("d-none")
                $(".inputs").prop("disabled", false)
                $(".inputs").removeClass("is-invalid")
                $('#mainForm').removeClass('was-validated');
                $("#saveBtn, #closeBtn").removeClass("d-none")
                $("#editBtn, #newBtn, #deleteBtn, #bulkAssignBtn").addClass("d-none")
                getContact(id);
                mode = 'edit';
            <?php endif; ?>
        }

        $(document).on("click", "#whatapp-button", function() {
            let id = $("#id").val();
            let mobile = $("#whatsapp").val();
            if (id == "" || mobile == "") {
                return
            }
            window.open(`https://web.whatsapp.com/send?phone=${mobile}&text=&source=&data=`, '_blank');
        })

        function deleteData(id) {
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Contacts Delete')): ?>

                Swal.fire({

                        html: '<div class="mt-3"><lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon><div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you Sure ?</h4><p class="text-muted mx-4 mb-0">Are you Sure You want to Delete this Record?</p></div></div>',
                        showCancelButton: !0,
                        confirmButtonClass: "btn btn-danger w-xs me-2 mb-1",
                        confirmButtonText: "Yes, Delete Record",
                        cancelButtonClass: "btn btn-soft-success w-xs mb-1",
                        buttonsStyling: !1,
                        showCloseButton: !0
                    })
                    .then(function(t) {
                        if (t.isConfirmed) { // on confirm delete the category
                            $.ajax({
                                url: "<?php echo e(route('admin.work.contacts.delete')); ?>",
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    id,
                                },
                                success: function(result) {
                                    if (result.status == '1') { //if successfully deleted
                                        showToastify("Contact has been deleted successfully", "success")
                                        dataTable.row()
                                            .draw(); // redraw the datatable show we don't need to reload the page to see the changes
                                        $('#viewModal').modal('hide');
                                    } else { // something went wrong on backend
                                        swal("Whoops", result.msg, "error");
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
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Bulk Delete Contacts')): ?>
                if (ids_array = [], document.getElementsByName("chk_child").forEach((function(e) {
                        if (1 == e.checked) {
                            var t = e.value;
                            ids_array.push(t)
                        }
                    })), "undefined" != typeof ids_array && ids_array.length > 0) {



                    Swal.fire({

                            html: '<div class="mt-3"><lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon><div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you Sure ?</h4><p class="text-muted mx-4 mb-0">Are you Sure You want to Delete ' +
                                ids_array.length + ' Contact(s)?</p></div></div>',
                            showCancelButton: !0,
                            confirmButtonClass: "btn btn-danger w-xs me-2 mb-1",
                            confirmButtonText: "Yes, Delete " + ids_array.length + " Contact(s)",
                            cancelButtonClass: "btn btn-soft-success w-xs mb-1",
                            buttonsStyling: !1,
                            showCloseButton: !0
                        })
                        .then(function(t) {
                            if (t.isConfirmed) { // on confirm delete the category
                                $.ajax({
                                    url: "<?php echo e(route('admin.work.contacts.delete.multi')); ?>",
                                    type: 'POST',
                                    dataType: 'json',
                                    data: {
                                        ids: ids_array,
                                    },
                                    success: function(result) {
                                        // console.table(result);
                                        if (result.status == '1') {
                                            showToastify(result.msg, "success");
                                            $('.multi_options').remove();

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
                        })




                } else {
                    showToastify("Please select at least one checkbox", "error")
                }
            <?php endif; ?>
        }

        $("#checkAll").change(function() {
            $(".selectable-checkbox").prop("checked", this.checked)
            updateMultiOption();
        })
        //$("#sidebarWork").addClass('collapse show');
        $("#sidebarContacts").addClass('active');








        $(document).on("click", ".change-meeting-status", function() {
            let meeting_id = $(this).parent(".dropdown-menu").data("id");
            let status = $(this).data("status");

            function changeMeetingStatus() {
                $.ajax({
                    url: "<?php echo e(route('admin.work.contacts.meeting.status')); ?>",
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
                            getMeetingLog(data.contact_id)
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

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Contact Meeting Status Change')): ?>
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

        $(document).on("click", ".change-task-status", function() {
            let task_id = $(this).parent(".dropdown-menu").data("id");
            let status = $(this).data("status");

            function changeTaskStatus() {
                $.ajax({
                    url: "<?php echo e(route('admin.work.contacts.task.status')); ?>",
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
                            getTask(data.contact_id)
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

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Task Status Change')): ?>
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

        $(document).on("click", "#bulkLeadsSubmitBtn", function() {

            let selected_agent_bulk = $('#bulk_leads_agent_id').val();
            let type = document.querySelector('input[name="assign_type"]:checked').value;
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
                        url: "<?php echo e(route('admin.work.contacts.bulk.assign')); ?>",
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            ids: ids_array,
                            agent_id: selected_agent_bulk,
                            type: type
                        },
                        success: function(result) {
                            if (result.status == '1') {
                                showToastify(result.msg, "success")
                                $("#bulkReassignContactForm").trigger("reset")
                                $("#bulkReassignContactModal").modal("hide")
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
        dataTable.on('draw.dt', function() {
            $(".chk_child").prop("checked", false);
            $('.multi_options').remove();
            $('#checkAll').prop("checked", false);
        });
        $('#sidebarContacts').addClass('active')



        $(document).on("click", "#send-sms", function(event) {

            $("#send-bulk-sms").modal("show");
        });

        $(document).on('submit', '#send-bulk-sms-form', function() {
            event.preventDefault();
            let msg_body = $("#bulk-message").val();
            if(msg_body == ""){
                showToastify("Message body is required", "error")
                return;
            }


            let contacts_count = $('#contacts-count').text();
            Swal.fire({
                    html: '<div class="mt-3"><div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you sure?</h4><p class="text-muted mx-4 mb-0">Are you sure you want to send this message to '
                        +contacts_count+
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
                            url: "<?php echo e(route('contacts.bulk.sms.sendMsg')); ?>",

                            data: {
                                id_operator: filtersData?.search_id_find_operator,
                                id: filtersData?.search_id_find,
                                agents_operator: filtersData?.search_agents_operator,
                                agents: filtersData?.search_agents,

                                contact_types: filtersData?.search_contact_type,
                                contact_types_operator: filtersData?.search_contact_type_operator,

                                genders: filtersData?.search_gender_search,
                                genders_operator: filtersData?.search_gender_search_operator,

                                contact_sources: filtersData?.search_contact_sources,
                                contact_sources_operator: filtersData?.search_contact_sources_operator,

                                contact_statuses: filtersData?.search_contact_statuses,
                                contact_statuses_operator: filtersData
                                    ?.search_contact_statuses_operator,

                                created_date: filtersData?.search_created_from,
                                created_date_operator: filtersData?.search_created_from_operator,

                                updated_date: filtersData?.search_updated_from,
                                updated_date_operator: filtersData?.search_updated_from_operator,

                                bitrix_id_operator: filtersData?.search_bitrix_id_operator,
                                bitrix_id: filtersData?.search_bitrix_id,

                                date_of_birth_operator: filtersData?.search_date_of_birth_operator,
                                date_of_birth: filtersData?.search_date_of_birth,

                                my_crm_id_operator: filtersData?.search_my_crm_id_operator,
                                my_crm_id: filtersData?.search_my_crm_id,

                                deleted: filtersData?.search_deleted,

                                mobile: filtersData?.search_mobile,
                                mobile_operator: filtersData?.search_mobile_operator,

                                email: filtersData?.search_email,
                                email_operator: filtersData?.search_email_operator,
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

        $(document).on("submit", "#iisend-bulk-sms-form", function(event) {
            event.preventDefault();
            filtersData = $("#searchForm").serialize();
            filtersData = urldecode(filtersData);
            filtersData = QueryStringToJSON(filtersData);
            let message = $("#bulk-message").val();
            $.ajax({
                type: "POST",
                url: "<?php echo e(route('contacts.bulk.sms.sendMsg')); ?>",

                data: {
                    id_operator: filtersData?.search_id_find_operator,
                    id: filtersData?.search_id_find,
                    agents_operator: filtersData?.search_agents_operator,
                    agents: filtersData?.search_agents,

                    contact_types: filtersData?.search_contact_type,
                    contact_types_operator: filtersData?.search_contact_type_operator,

                    genders: filtersData?.search_gender_search,
                    genders_operator: filtersData?.search_gender_search_operator,

                    contact_sources: filtersData?.search_contact_sources,
                    contact_sources_operator: filtersData?.search_contact_sources_operator,

                    contact_statuses: filtersData?.search_contact_statuses,
                    contact_statuses_operator: filtersData?.search_contact_statuses_operator,

                    created_date: filtersData?.search_created_from,
                    created_date_operator: filtersData?.search_created_from_operator,

                    updated_date: filtersData?.search_updated_from,
                    updated_date_operator: filtersData?.search_updated_from_operator,

                    bitrix_id_operator: filtersData?.search_bitrix_id_operator,
                    bitrix_id: filtersData?.search_bitrix_id,

                    date_of_birth_operator: filtersData?.search_date_of_birth_operator,
                    date_of_birth: filtersData?.search_date_of_birth,

                    my_crm_id_operator: filtersData?.search_my_crm_id_operator,
                    my_crm_id: filtersData?.search_my_crm_id,

                    deleted: filtersData?.search_deleted,

                    mobile: filtersData?.search_mobile,
                    mobile_operator: filtersData?.search_mobile_operator,

                    email: filtersData?.search_email,
                    email_operator: filtersData?.search_email_operator,
                    message: message
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
        })
        // character-count
        $(document).on("keyup", "#bulk-messagess", function() {
            let message = $(this).val();
            let messageLength = message.length;
            let messageCount = 160 - messageLength;
            $("#character-count").text(messageCount);

            if (messageLength > 160) {
                $(this).val(message.substring(0, 160));
                $("character-count").text(0);
            }


        });
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
    </script>
    <script src="<?php echo e(URL::asset('/assets/js/pages/contacts.js')); ?>"></script>
    <?php echo $__env->make('admin.templates.agent_template', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php echo $__env->yieldContent('pagejs'); ?>
    <?php echo $__env->yieldPushContent('script-bottom'); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/contact/index.blade.php ENDPATH**/ ?>