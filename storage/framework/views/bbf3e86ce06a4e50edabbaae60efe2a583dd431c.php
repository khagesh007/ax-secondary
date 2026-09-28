<?php $__env->startSection('title'); ?>
    Listing Leads
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

        @media (min-width: 1024.1px) {

            [data-layout="horizontal"] .sticky-header,
            [data-layout="horizontal"] .sticky-table-header {
                top: 116px;
                position: sticky;
                width: 100%;
                z-index: 1002;
            }

            #top-bar .card-header {
                padding: 0.5rem 1rem;
            }

            [data-layout="horizontal"] #top-bar .card-header .form-switch-right {
                padding-right: 0px;
            }

            #page-topbar {
                z-index: 1010;
            }

            .navbar-menu {
                z-index: 1003 !important;
            }

            .table-sticky-header-container.sticky-table-header {
                background-color: #fff;
                border-bottom: solid 1px #f6f6f6;
            }

            [data-layout="vertical"] .sticky-header,
            [data-layout="vertical"] .sticky-table-header {
                top: 70px;
                position: sticky;
                width: 100%;
                z-index: 1002;
            }

            .lead_datatable_header {
                top: 112px;
                position: sticky;
                width: 100%;
                z-index: 998;
                background: #fff;
            }
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" type="text/css" />
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-lg-12" id="top-bar">
            <div class="card">

                <div>
                    <div class="card-header align-items-center d-flex">
                        <div class="flex-grow-1">
                            <h4 class="card-title mb-0 ">Listing Leads</h4>
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Work</a></li>
                                <li class="breadcrumb-item active">Listing Leads</li>
                            </ol>
                        </div>

                        <div class="flex-shrink-0">
                            <div class="form-check form-switch form-switch-right form-switch-md">


                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Listing Leads Create')): ?>
                                    <button type="button" id="newBtn" class="btn btn-danger add-btn action-add">
                                        <i class="ri-add-line align-bottom me-1"></i> Add New
                                    </button>
                                <?php endif; ?>

                                

                                <div class="btn-group d-none" id="generate-listing-btn">
                                    <button type="button" class="btn btn-light dropdown-toggle action-publish"
                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Generate
                                        Listing</button>
                                    <div class="dropdown-menu">


                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Listing Leads Generate Listing')): ?>
                                            <button type="button" class="dropdown-item" id="addContact" data-id="">Generate
                                                Listing</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success d-none action-save"
                                    id="saveBtn">Save</button>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Listing Leads Edit')): ?>
                                    <button type="button" id="editBtn" class="btn btn-info d-none action-edit">Edit</button>
                                <?php endif; ?>

                                
                                <button type="button" class="btn btn-light d-none action-close"
                                    id="closeBtn">Close</button>

                                

                                
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="collapse slideIn" id="collapseContactForm">
                <form class="row needs-validation" id="mainForm" novalidate>
                    <div class="col-4">
                        <div class="card">
                            <div class="card-body">
                                <input type="hidden" id="id" name="id">
                                <input type="hidden" id="dailer_id" name="dailer_id" value="<?php echo e(auth()->user()->id); ?>">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-12">
                                                <h5>Contact (Owner)</h5>
                                            </div>
                                            
                                            
                                            

                                            <div class="col-3 mt-2">First Name<span
                                                    class="clear_req new_status_req unsuccessful_status_req assigned_status_req  qualifiled_status_req">*</span>
                                            </div>
                                            <div class="col-9 mt-2">

                                                <input type="text"
                                                    class="form-control applyValidation unsuccessful_status  new_status  assigned_status  qualifiled_status inputs"
                                                    name="first_name" id="first_name" placeholder="Enter first name"
                                                    required>
                                                <div class="invalid-feedback">
                                                    Please enter first name.
                                                </div>
                                            </div>

                                            <div class="col-3 mt-2">Last Name<span class="clear_req "></span></div>
                                            <div class="col-9 mt-2">
                                                <input type="text" class="form-control applyValidation inputs"
                                                    name="last_name" id="last_name" placeholder="Enter last name">
                                                <div class="invalid-feedback">
                                                    Please enter last name.
                                                </div>
                                            </div>
                                            <div class="col-3 mt-2">Mobile<span
                                                    class="clear_req  new_status_req assigned_status_req not_answered_status_req qualifiled_status_req">*</span>
                                            </div>
                                            <div class="col-md-7 mt-2  text-end" id="mobileDiv1">
                                                <input type="tel"
                                                    class="form-control applyValidation   new_status  assigned_status not_answered_status qualifiled_status  inputs"
                                                    name="mobile" id="mobile" required placeholder="Enter mobile">
                                                <div class="text-danger mobileValidation">
                                                </div>

                                                <div class="invalid-feedback">
                                                    Please enter a valid Mobile number.
                                                </div>
                                                <div class="text-danger" id="mobile-exists"></div>

                                                <a href="" id="whatsappBtn"
                                                    class="f-2 text-success icon-font-15">Whatsapp Now <i
                                                        class="bx bxl-whatsapp "></i></a>
                                            </div>
                                            <div class="col-md-2 col-sm-6" style="padding: 7px 11px 20px 0px;"
                                                id="call_now_button"> <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any([
                                                    'Dial my lead',
                                                    'Dial
                                                    other lead',
                                                    ])): ?>
                                                    <a href="javascript:void(0);" title="Call Now" id="start-call-button"
                                                        class="avatar-title bg-info text-white fs-15 rounded ">
                                                        <i class="ri-phone-line"></i>
                                                    </a>
                                                    <a href="javascript:void(0);" title="Calling" id="calling-button"
                                                        class="avatar-title bg-soft-success text-success fs-15 rounded d-none">
                                                        <span class="d-flex align-items-center">
                                                            <span class="spinner-grow flex-shrink-0" role="status">
                                                                <span class="visually-hidden">Loading...</span>
                                                            </span>
                                                        </span>
                                                    </a>
                                                <?php endif; ?>
                                            </div>








                                            <div class="col-3 mt-2">Email<span class="clear_req "></span></div>
                                            <div class="col-9 mt-2">
                                                <input type="email" class="form-control applyValidation inputs"
                                                    name="email" id="email" placeholder="Enter email address">
                                                <div class="invalid-feedback">
                                                    Please enter a valid email address.
                                                </div>
                                                <div class="text-danger" id="email-exists"></div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>

                        

                        <div class="addHide card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 ">


                                        <div class=" mt-0 form-steps">
                                            <ul class="nav nav-tabs nav-tabs-custom nav-success nav-justified mb-3"
                                                role="tablist">

                                                <li class="nav-item active">


                                                    <a class="nav-link active" data-bs-toggle="tab" href="#notesTab"
                                                        role="tab" aria-selected="true"
                                                        onclick="scrollAtBottom('notes');">
                                                        <i class="mdi mdi-note-text align-middle"></i>Notes</a>
                                                </li>

                                                <li class="nav-item addHide">
                                                    <a class="nav-link" data-bs-toggle="tab" href="#activityLogTab"
                                                        role="tab" aria-selected="false" id="getActivityLog">
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
                                                        <textarea class="form-control " id="leadnote" rows="2" placeholder="Enter a note"></textarea>
                                                        <button class="btn btn-primary btn-icon waves-effect waves-light"
                                                            type="button" title="Save Note" id="addNoteButton"
                                                            style="height:auto;">
                                                            <i class="mdi mdi-send-outline"></i>

                                                        </button>

                                                    </div>



                                                </div>
                                            </div>
                                            <div class="tab-pane" id="activityLogTab" role="tabpanel">
                                                <div class="d-none scrollable-notes pt-0" id="activity-log-div">
                                                    <div class="card-header p-05rem">
                                                        <h6 class="card-title mb-0">Activities </h6>
                                                    </div>

                                                    <div id="activity-log-div_Placeholder">
                                                        <?php echo $__env->make('admin.lead.include.partials.activity._activitylog_placeholder', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                                    </div>

                                                    <div id="activity-log-div2" class="scrolltobottom">

                                                    </div>

                                                    <div id="no_activity" class="d-none">
                                                        <?php echo $__env->make('admin.lead.include.partials.activity._empty_activities', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                                    </div>

                                                    <div class="profile-timeline user-activity-timeline d-none"
                                                        id="activities">

                                                    </div>

                                                    <div class="d-none p-lg-2" id="view-edit-mode">
                                                        <small class="mb-0 text-muted">Created: <span
                                                                id="created_by_span"></span><br>Updated:
                                                            <span id="updated_by_span"> </span></small>
                                                    </div>

                                                </div>
                                            </div>

                                        </div>


                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="col-4">

                        <div class="card">
                            <div class="card-body form-steps">
                                <div class="row">
                                    <div class="col-12">
                                        <h5>Property Details</h5>
                                    </div>

                                    <input type="hidden" id="property_id" value="">
                                    <div class="col-4 mt-2">Type<span
                                            class="clear_req new_status_req assigned_status_req  qualifiled_status_req">*</span>
                                    </div>
                                    <div class="col-8 mt-2">
                                        <select
                                            class="form-control applyValidation new_status  assigned_status  qualifiled_status  select2 inputs non_manadatory"
                                            id="property_category_id" name="property_category_id" required>
                                            <?php $__currentLoopData = $categories ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($cat->id); ?>"><?php echo e($cat->title); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <div class="invalid-feedback">
                                            Please choose Property Category.
                                        </div>
                                    </div>

                                    <div class="col-4 mt-2">Purpose<span
                                            class="clear_req new_status_req assigned_status_req  qualifiled_status_req ">*</span>
                                    </div>
                                    <div class="col-8 mt-2">
                                        <select name='for' id="for"
                                            class="form-control applyValidation new_status  assigned_status  qualifiled_status  select2 inputs non_manadatory"
                                            required>
                                            <?php $__currentLoopData = \App\Models\Lead::FOR_SELECT ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php if($key == 'Unspecified' && auth()->user()->hasRole('Agent')): ?>
                                                <?php else: ?>
                                                    <option value="<?php echo e($key); ?>"><?php echo e($value); ?> </option>
                                                <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <div class="invalid-feedback">
                                            Please choose lead for.
                                        </div>
                                    </div>

                                    <div class="col-4 mt-2">Location<span
                                            class="clear_req new_status_req assigned_status_req  qualifiled_status_req ">*</span>
                                    </div>
                                    <div class="col-8 mt-2">
                                        <input type="hidden" name="location_type" id="location_type"
                                            class="location_type">
                                        <select
                                            class="form-control applyValidation new_status  assigned_status  qualifiled_status  select2 inputs location_id non_manadatory"
                                            id="location_id" name="location_id" required>
                                            <option value=""></option>

                                        </select>
                                        <div id="location_address" class="location_address"></div>
                                    </div>


                                    <div class="col-4 mt-2">Bed<span class="clear_req">*</span></div>
                                    <div class="col-8 mt-2">
                                        <select class="form-control applyValidation select2 inputs non_manadatory"
                                            name="bed_id" id="bed_id" required>
                                            <?php $__currentLoopData = $beds ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bed): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($bed->id); ?>"><?php echo e($bed->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <div class="invalid-feedback">
                                            Please choose Bed.
                                        </div>
                                    </div>

                                    <div class="col-4 mt-2">Bath <span class="clear_req"></span></div>
                                    <div class="col-8 mt-2">
                                        <select class="form-control applyValidation select2 inputs non_manadatory"
                                            name="bath_id" id="bath_id">
                                            <?php $__currentLoopData = $baths ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bath): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($bath->id); ?>"><?php echo e($bath->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <div class="invalid-feedback">
                                            Please choose bath.
                                        </div>
                                    </div>
                                    <div class="col-4 mt-2">Unit No<span class="clear_req  "></span></div>
                                    <div class="col-8 mt-2">
                                        <input type="text" class="form-control applyValidation inputs" name="unit_no"
                                            id="unit_no" placeholder="Unit No eg: 101">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="addHide">
                            <div id="eventWidgetArea">

                            </div>
                        </div>
                    </div>


                    <div class="col-4">
                        <div class="card">
                            <div class="card-body form-steps">
                                <div class="row">
                                    <div class="col-12">
                                        <h5>Action</h5>
                                    </div>
                                    

                                    
                                    <?php echo $__env->make('admin.templates.agents_select2_form', [
                                        'id' => 'agent_id',
                                        'label' => 'Agent',
                                        'agents' => $agents ?? [],
                                        'layout' => 'horizontal',
                                        'required' => 'true',
                                    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                                    


                                    <div class="col-4 mt-2">Source<span
                                            class="clear_req unsuccessful_status_req new_status_req assigned_status_req  qualifiled_status_req ">*</span>
                                    </div>
                                    <div class="col-8 mt-2">
                                        <select
                                            class="form-control applyValidation unsuccessful_status new_status  assigned_status  qualifiled_status  select2 inputs"
                                            name="source_id" id="source_id" required>
                                            <?php $__currentLoopData = $sources ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $src): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="uTxuYi-<?php echo e($src->id); ?>"><?php echo e($src->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <div class="invalid-feedback">
                                            Please choose lead source.
                                        </div>
                                    </div>

                                    <div class="col-4 mt-2">Status<span
                                            class="clear_req unsuccessful_status_req new_status_req assigned_status_req  qualifiled_status_req ">*</span>
                                    </div>
                                    <div class="col-8 mt-2">
                                        <select
                                            class="form-control applyValidation unsuccessful_status new_status  assigned_status  qualifiled_status  select2 inputs"
                                            name="status_id" id="status_id" required>
                                            <?php $__currentLoopData = $statuses ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $src): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php if(auth()->user()->hasRole(['Agent'])): ?>
                                                    <?php if($src->name == 'New' || $src->name == 'Assigned'): ?>
                                                        <?php continue; ?>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                                <option value="<?php echo e($src->id); ?>"><?php echo e($src->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <div class="invalid-feedback">
                                            Please choose Status.
                                        </div>
                                    </div>

                                </div>

                                <div class="row" id="descriptionFullDiv">

                                    <div class="col-4 mt-2">Note<span
                                            class="clear_req unsuccessful_status_req new_status_req assigned_status_req  qualifiled_status_req ">*</span>

                                        <button type="button" class="btn btn-default btn-sm"
                                            id="btn_description_fullscreen" title="Full Screen"
                                            aria-label="Full Screen"><i class="note-icon-arrows-alt"></i></button>
                                    </div>
                                    <div class="col-8 mt-2">
                                        <textarea type="text"
                                            class="form-control applyValidation unsuccessful_status new_status  assigned_status  qualifiled_status  inputs"
                                            name="note" id="note" rows="3"></textarea>
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="col-12 mt-3">
                                        <div id="call_rec"></div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="addHide" id="callLogWidget"></div>



                    </div>


                    <button type="submit" class="d-none" id="submitBtn"></button>
                </form>


            </div>
        </div>
    </div>
    <?php if ($__env->exists('admin.ListingLeads.includes.filterForm')) echo $__env->make('admin.ListingLeads.includes.filterForm', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <div class="row">
        <div class="col-xxl-12">

            <div class="table-sticky-header-container sticky-table-header" id="table-sticky-header">
                <ul class="nav nav-tabs nav-tabs-custom nav-success mb-3 no-border" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link All statusFilterClear " data-bs-toggle="tab" id="All" href="#"
                            role="tab" aria-selected="false" tabindex="-1">
                            All
                        </a>
                    </li>

                    <?php $__currentLoopData = $statuses ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if(auth()->user()->hasRole(['Agent'])): ?>
                            <?php if($status->name == 'New'): ?>
                                <?php continue; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link statusFilter text-uppercase <?php echo e($status->name == 'New' &&!auth()->user()->hasRole(['Agent'])? 'active': ''); ?> <?php echo e($status->name == 'Assigned' &&auth()->user()->hasRole(['Agent'])? 'active': ''); ?>"
                                data-bs-toggle="tab" id="status_id_<?php echo e($status->id); ?>" href="javascript:void(0)"
                                data-status="<?php echo e($status->id); ?>" role="tab" aria-selected="false" tabindex="-1">
                                <i class="<?php echo e($status->icon); ?> me-1"></i><?php echo e($status->name); ?>

                            </a>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                </ul>
            </div>

            <div class="card " id="contactList">
                <div class="card-body">

                    <div class="table-responsive  mb-4" style="overflow: scroll;">
                        <table id="sourceDatatable" class="table align-middle " style="width:100%">
                            <thead class="lead_datatable_header_">
                                <tr class="text-uppercase table-light2">
                                    <th scope="col" style="width-50px;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="checkAll"
                                                value="option" data-com.bitwarden.browser.user-edited="yes">
                                        </div>
                                    </th>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Mobile</th>
                                    <th>Email</th>
                                    <th>Purpose</th>
                                    <th>Type</th>
                                    <th width="15%">Location</th>
                                    <th style="width:50px!important">Note</th>
                                    <th>Beds</th>
                                    <th>Baths</th>
                                    <th>Unit No</th>
                                    <th>Source</th>
                                    <th>Agent</th>
                                    <th>Status</th>
                                    <th>Updated</th>
                                    <th>Created</th>
                                    <th>Created at</th>
                                </tr>
                            </thead>
                            <tbody class="form-check-all">

                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>



    <div class="col-xl-6">

        <!-- end card -->
    </div>
    <?php echo $__env->make('admin.events.include.modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <div id="eventModalsDiv"></div>
    <?php echo $__env->make('admin.ListingLeads.includes.modals', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <?php echo $__env->make('admin.partials.datatable', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <script type="text/javascript"
        src="https://maps.googleapis.com/maps/api/js?key=<?php echo e(env('GOOGLE_MAP_KEY')); ?>&libraries=places&callback=Function.prototype">
    </script>
    <script src="<?php echo e(asset('assets/js/pages/form-wizard.init.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('/assets/js/plugins/widgets.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('/assets/js/plugins/event.js')); ?>?v=<?php echo e(time()); ?>"></script>
    
    


    <script>
        let currentPage = "Listing Lead";
        var all_listing_val = false;
        var isUpdate = false;
        let dataObj = {};
        let defaultStatusId = 1;
        <?php if(auth()->user()->hasRole(['Agent'])): ?>
            let filtersData = {
                search_status: 2,
                search_status_operator: 'in',
            };
        <?php else: ?>
            let filtersData = {
                search_status: 1,
                search_status_operator: 'in',
            };
        <?php endif; ?>
        let defaultSubStatusId = 4;
        let agent_sub_communities = "<?php echo e(route('ajax.user-sub-communities')); ?>";
        let lead_sub_status_url = "<?php echo e(route('ajax.lead-sub-statuses')); ?>";
        let remove_additional_agent = "<?php echo e(route('admin.work.leads.remove.agent')); ?>";
        let fetch_additional_agents = "<?php echo e(route('admin.work.leads.get.agents')); ?>";
        let add_more_agent_url = "<?php echo e(route('admin.work.leads.add.more.agent')); ?>";
        let callIcon = "<?php echo e(asset('assets/json/icons/call-icon.json')); ?>";
        let makeCallLink = "<?php echo e(route('listings.leads.start-call')); ?>";
        let bulkAssignLink = "<?php echo e(route('admin.work.leads.assign-agents')); ?>";
        let mode = 'view';
        let existingContact = null;
        let existingContactDetails = null;
        let canRemoveAdditionalAgents = false;
        let dataTable = null;
        let isEventPage = false;
        let evenStatusDropDown = "";
        <?php echo json_encode(\App\Models\Meeting::STATUS_SELECT, 15, 512) ?>?.map(item => {
            evenStatusDropDown +=
                `<a class="dropdown-item change-meeting-status" href="javascript:void(0)" data-status="${item}">${item}</a>`;
        });
        let meetingStatusChangePermission = "<?php echo e(auth()->user()->can('Listing Lead Meeting Status Change')); ?>";
        let leadsViewMyRecording = false;
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Leads View My Recordings')): ?>
            leadsViewMyRecording = true;
        <?php endif; ?>
        let noFollowupIcon = "<?php echo e(asset('assets/json/icons/wired-outline-981-consultation.json')); ?>";
        let meetingLogIcon = "<?php echo e(asset('assets/json/icons/wired-outline-981-consultation.json')); ?>";
        let call_now_button = 0;




        $('#source_id').select2({
            placeholder: 'Select Source',
            allowClear: true
        });


        $('#property_category_id').select2({
            placeholder: 'Select Type',
            allowClear: true
        });
        $('#listing_source_id').select2({
            placeholder: 'Select Source',
            allowClear: true
        });

        $('#listing_agent_id').select2({
            placeholder: 'Select Agent',
            allowClear: true,
            dropdownParent: $('#addContactModal'),
        });

        $('#listing_location_id').select2({
            placeholder: 'Select Location',
            allowClear: true,
            dropdownParent: $('#addContactModal'),
        });

        $('#listing_property_category_id').select2({
            placeholder: 'Select Property Type',
            allowClear: true,
            dropdownParent: $('#addContactModal'),
        });

        $('#listing_for').select2({
            placeholder: 'Select Property For',
            allowClear: true,
            dropdownParent: $('#addContactModal'),
        });

        $(document).on('click', '#btn_description_fullscreen', function(event) {
            $('#descriptionFullDiv').toggleClass('fullscreen');
            if ($('#descriptionFullDiv').hasClass('fullscreen')) {
                $('#note').attr('rows', 15);
            } else {
                $('#note').attr('rows', 5);
            }
        });

        $('#medium_id').select2({
            placeholder: 'Select Medium',
            allowClear: true
        });

        $('#bath_id').select2({
            placeholder: 'Select Baths',
            allowClear: true
        });
        $('#bed_id').select2({
            placeholder: 'Select Beds',
            allowClear: true

        });

        $('#listing_bath_id').select2({
            placeholder: 'Select Baths',
            allowClear: true,
            dropdownParent: $('#addContactModal'),
        });

        $('#listing_property_category_id').select2({
            placeholder: 'Select Property Type',
            allowClear: true,
            dropdownParent: $('#addContactModal'),
        });


        $('#listing_bed_id').select2({
            placeholder: 'Select Beds',
            allowClear: true,
            dropdownParent: $('#addContactModal'),
        });
        $('#listing_for').select2({
            placeholder: 'Select Purpose',
            allowClear: true,
            dropdownParent: $('#addContactModal'),
        });
        $('#search_lead_statuses').select2({
            placeholder: 'Select Status',
            allowClear: true
        });

        $('#search_communities').select2({
            placeholder: 'Communities',
            allowClear: true
        });
        $('#search_sub_communities').select2({
            placeholder: 'Sub Communities',
            allowClear: true
        });

        $('#search_community_properties').select2({
            placeholder: 'Community Properties',
            allowClear: true
        });


        $('#search_locations').select2({
            placeholder: 'Location',
            allowClear: true

        });

        $('#agent_id').select2({
            placeholder: 'Select Agent',
            allowClear: true
        });
        $('#telesales_id').select2({
            placeholder: 'Select Telesales',
            allowClear: true
        });

        $('#manager_id').select2({
            placeholder: 'Select Manager',
            allowClear: true
        });
        $('#search_agents').select2({
            placeholder: 'Select Agent',
            allowClear: true
        });


        $('#search_unit_no').select2({
            placeholder: 'Unit No.',
            allowClear: true
        });

        $('#search_baths').select2({
            placeholder: 'Select Baths',
            allowClear: true
        });
        $('#search_beds').select2({
            placeholder: 'Select Beds',
            allowClear: true
        });

        $('#status_id').select2({
            placeholder: 'Select Status',
            allowClear: true
        });
        dataTable = $('#sourceDatatable').DataTable({ //datatable initialization
            "order": [
                [0, "DESC"]
            ],
            'lengthMenu': [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],
            language: {
                searchPlaceholder: "Search ..."
            },
            createdRow: function(row, data, index) {
                $('#sourceDatatable tbody').css('cursor', 'pointer');

            },
            dom: `<'row'<'col-sm-10 text-left'B> <'col-sm-2 text-right'f>>
            <'row'<'col-sm-12'tr>>
            <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>`,
            buttons: [{
                extend: 'colvis',
                titleAttr: 'Show/Hide',
                text: '<i class="colvis" style="font-size:09px">Show / Hide</i>',

            }],
            scrollX: true,
            responsive: false,
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "<?php echo e(route('listings.leads.dt')); ?>",
                type: "post",
                data: function(d) {
                    d.beds = filtersData?.search_beds;
                    d.beds_operator = filtersData?.search_beds_operator;

                    d.baths = filtersData?.search_baths;
                    d.baths_operator = filtersData?.search_baths_operator;

                    d.communities = filtersData?.search_communities;
                    d.communities_operator = filtersData?.search_communities_operator;

                    d.sub_communities = filtersData?.search_sub_communities;
                    d.sub_communities_operator = filtersData?.search_sub_communities_operator;


                    d.community_properties_operator = filtersData?.search_community_properties_operator;
                    d.community_properties = filtersData?.search_community_properties;

                    // d.locations = filtersData?.search_locations;
                    // d.locations_operator = filtersData?.search_locations_operator;

                    d.unit_no = filtersData?.search_unit_no;
                    d.unit_no_operator = filtersData?.search_unit_no_operator;

                    // d.sub_communities = filtersData?.sub_communities;

                    d.agent_id = filtersData?.search_agents;
                    d.agent_operator = filtersData?.search_agents_operator;

                    d.lead_statuses = filtersData?.search_status;
                    d.lead_statuses_operator = filtersData?.search_status_operator;



                    d.created_from = filtersData?.search_created_from;
                    d.created_from_operator = filtersData?.search_created_from_operator;

                    d.updated_from = filtersData?.search_updated_from;
                    d.updated_from_operator = filtersData?.search_date_updated_operator;

                    d.property = filtersData?.search_property_ref;
                    d.property_operator = filtersData?.search_property_ref_operator;

                    d.manager_id = filtersData?.search_managers;
                    d.manager_operator = filtersData?.search_managers_operator;

                    d.sources = filtersData?.search_lead_sources;
                    d.sources_operator = filtersData?.search_lead_sources_operator;

                    d.mobile = filtersData?.search_mobile;
                    d.mobile_operator = filtersData?.search_mobile_operator;

                    d.unit_no = filtersData?.search_unit_no;
                    d.unit_no_operator = filtersData?.search_unit_no_operator;


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
                $(`<?php if ($__env->exists('admin.ListingLeads.includes.filterButtons')) echo $__env->make('admin.ListingLeads.includes.filterButtons', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>`).insertAfter(".dataTables_filter input")
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
                },
                {
                    data: 'id',
                    name: 'id',
                    orderable: true,
                },
                {
                    data: null,
                    orderable: true,
                },
                {
                    data: null,
                    orderable: false,
                },
                {
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
                    data: null,
                    orderable: false,
                },
                {
                    data: 'updated_at',
                    name: 'updated_at',
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                },
            ],
            "columnDefs": [{ //manupulate a table column
                    "targets": 0,
                    orderable: true,
                    "data": "id",
                    render: function(type, row, data, meta) {
                        return `<div class="form-check">
                                    <input class="form-check-input selectable-checkbox" type="checkbox" name="chk_child" value="` +
                            data.id + `">
                            </div>`;
                    }
                },
                {
                    "targets": 1,
                    orderable: true,
                    "data": "id",
                    render: function(type, row, data, meta) {
                        return data.id;
                    }
                },
                {
                    "targets": 2,
                    orderable: true,
                    "data": "first_name",
                    render: function(type, row, data, meta) {
                        let last_name = "";
                        if (data.last_name) {
                            last_name = data.last_name;
                        }
                        return `<a href="javascript:void(0)" class="text-primary" onclick="viewSource('` +
                            data.id + `')">` + data.first_name + ` ` + last_name + `</a>`;
                    }
                },
                {
                    "targets": 3,
                    orderable: true,
                    "data": "mobile",
                    render: function(type, row, data, meta) {
                        return data.mobile;

                    }
                },
                {
                    "targets": 4,
                    visible: false,
                    orderable: true,
                    "data": "email",
                    render: function(type, row, data, meta) {
                        return data.email;

                    }

                },
                {
                    targets: 5,
                    orderable: false,
                    visible: true,
                    /*This will hide particular column*/
                    render: function(type, row, data, meta) {
                        var text = 'Residential-for-sale';
                        if (data.for) {
                            return '<span class="badge ' + text + ' badge-border ">' + data.for+'</span>';

                        } else {
                            return '<span class="badge ' + text + ' badge-border ">' + '-' + '</span>';
                        }

                    }
                },
                {
                    targets: 6,
                    orderable: false,
                    visible: false,
                    /*This will hide particular column*/
                    render: function(type, row, data, meta) {
                        if (data.type) {
                            return data.type.name;
                        } else {
                            return '-';
                        }
                        // return data.source.name;

                    }
                },

                {
                    "targets": 7,
                    orderable: true,
                    "data": "location_id",
                    render: function(type, row, data, meta) {
                        if (data.community_property) {

                            let pathName = data.community_property.path_name;
                            return '<p class="datatableLocation mb-0"><strong>' + pathName.split(/,(.+)/)[
                                0] + '</strong>,' + pathName.split(/,(.+)/)[1] + '</p>';


                        }
                        if (data.sub_community) {

                            let pathName = data.sub_community.path_name;
                            return '<p class="datatableLocation mb-0"><strong>' + pathName.split(/,(.+)/)[
                                0] + '</strong>,' + pathName.split(/,(.+)/)[1] + '</p>';
                        }
                        if (data.community) {
                            let pathName = data.community.path_name;
                            return '<p class="datatableLocation mb-0"><strong>' + pathName.split(/,(.+)/)[
                                0] + '</strong>,' + pathName.split(/,(.+)/)[1] + '</p>';
                        }


                        return "-";
                    }

                },
                {
                    targets: 8,
                    orderable: false,
                    visible: false,
                    /*This will hide particular column*/
                    render: function(type, row, data, meta) {
                        if (data.note) {
                            return '<div style="width:200px!important">' + data.note + '</div>';
                        }
                        return '-';


                    }
                },
                {
                    targets: 9,
                    orderable: false,
                    visible: true,
                    /*This will hide particular column*/
                    render: function(type, row, data, meta) {
                        if (data.bed_id) {
                            return data.bed.name;
                        } else {
                            return '-';
                        }
                        // return data.source.name;

                    }
                }, {
                    targets: 10,
                    orderable: false,
                    visible: false,
                    /*This will hide particular column*/
                    render: function(type, row, data, meta) {
                        if (data.bath_id) {
                            return data.bath.name;
                        } else {
                            return '-';
                        }
                        // return data.source.name;

                    }
                },
                {
                    "targets": 11,
                    orderable: true,
                    'visible': false,
                    "data": "unit_no",
                    render: function(type, row, data, meta) {

                        return `<a href="javascript:void(0)" class="text-primary" onclick="viewSource('` +
                            data.id + `')">` + data.unit_no + `</a>`;
                    }
                },

                {
                    targets: 12,
                    orderable: false,
                    visible: true,
                    /*This will hide particular column*/
                    render: function(type, row, data, meta) {
                        if (data.source) {
                            return data.source.name;
                        } else {
                            return '-';
                        }
                        // return data.source.name;

                    }
                },
                {
                    targets: 13,
                    orderable: false,
                    visible: false,
                    /*This will hide particular column*/
                    render: function(type, row, data, meta) {
                        if (!data.agent) return "-";
                        return `<div class="d-flex align-items-center userDetails" data-agent-id="${data.agent.id}" title="${data.agent.name}">
                                    <div class="flex-shrink-0 me-2">
                                        <img src="${data.agent.thumb_url}" alt="" class="avatar-xs rounded-circle">
                                    </div>
                                    <div class="flex-grow-1">${data.agent.first_name}<p title="Created at" class="mb-0"><small>${moment(data.created_at).fromNow()}</small></p></div>
                                </div>`;
                        // return data.source.name;

                    }
                },
                {
                    targets: 14,
                    orderable: false,
                    visible: true,
                    /*This will hide particular column*/
                    render: function(type, row, data, meta) {
                        let badge = "badge-light";
                        if (data?.status?.badge) {
                            badge = data.status.badge;
                        }
                        return '<span class="badge ' + badge + ' badge-border ">' + data?.status?.name +
                            '</span>';

                    }
                },
                {
                    "targets": 15,
                    orderable: true,
                    visible: true,
                    render: function(type, row, data, meta) {
                        if (!data.updated_by) return "-";
                        return `<div class="d-flex align-items-center userDetails" data-agent-id="${data.updated_by.id}"  title="${data.updated_by.name}">
                                    <div class="flex-shrink-0 me-2">
                                        <img src="${data.updated_by.thumb_url}" alt="" class="avatar-xs rounded-circle">
                                    </div>
                                    <div class="flex-grow-1">${data.updated_by.first_name}<p title="Updated at" class="mb-0"><small>${moment(data.updated_at).fromNow()}</small></p></div>
                                </div>`;
                    }
                },
                {
                    "targets": 16,
                    orderable: true,
                    visible: false,
                    render: function(type, row, data, meta) {
                        if (data.created_by) {
                            return `<div class="d-flex align-items-center userDetails" data-agent-id="${data.created_by.id}"  title="${data.created_by.name}">
                                    <div class="flex-shrink-0 me-2">
                                        <img src="${data.created_by.thumb_url}" alt="" class="avatar-xs rounded-circle">
                                    </div>
                                    <div class="flex-grow-1">${data.created_by.first_name}<p title="Created at" class="mb-0"><small>${moment(data.created_at).fromNow()}</small></p></div>
                                </div> `;


                        } else {
                            return `${moment(data.created_at).fromNow()}`;
                        }

                    }
                },
                {
                    "targets": 17,
                    orderable: true,
                    visible: false,
                    render: function(type, row, data, meta) {
                        if (data.created_by) {
                            return `${moment(data.created_at).fromNow()}`;
                        } else {
                            return `${moment(data.created_at).fromNow()}`;
                        }

                    }
                },


            ]
        });


        let mobileInput = initPhoneValidator()
        let listing_mobile_Input = initPhoneValidator("listing_mobile", "full_listing_mobile")


        $(document).on("change", "#listing_mobile", function() {
            checkContactExistence(listing_mobile_Input.getNumber(), "mobile", "Contact");
        });

        $(document).on("click", "#getActivityLog", function() {
            let id = $('#id').val();
            getActivityLog(id);
        });


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
                    subject_model: "ListingLead",
                    subject_id: id
                },
                success: function(result) {
                    $('#activity-log-div_Placeholder').addClass('d-none');
                    $('#activity-log-div2').html('');
                    $('#activity-log-div2').html(result);
                    let element = document.getElementById("activity-log-div2");
                    element.scrollTop = element.scrollHeight
                    fullyLoaded = true;
                },
                error: function(result) {
                    console.log(result);
                }
            });
        }

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



        $("#for").select2({
            placeholder: "Select lead for",
            allowClear: true,
            closeOnSelect: false
        })






        $('#for').select2({
            placeholder: 'Select Purpose',
            allowClear: true
        });


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


        $(document).on("change", "#status_id", function() {
            let status_id = $(this).val();
            if (status_id == 5) {


                if ($('#property_id').val() != 'nullVal') {

                    console.log($('#property_id').val())
                    $('#addContact').click()
                }

            }
            if (status_id == 6) {
                if ($('#property_id').val() != 'nullVal') {
                    $('#unSuccessfulModal').modal('show');
                }
            }

        })


        // $(document).on("change", "#status_id", function() {
        //     let status_id = $(this).val();
        //     if (status_id == 6) {
        //         $('.non_manadatory').attr('required', false);
        //     } else {
        //         $('.non_manadatory').attr('required', true);

        //     }
        // })






        $(document).on("click", "#archiveBtn", function() {
            let id = $(this).attr('data-id');
            $('#archive_lead_id').val(id);
            $('#archiveModal').modal('show');
        })
        $('#preFillOption').select2({
            placeholder: 'Select any reason',
            allowClear: true,
            closeOnSelect: true,
        });
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
                        url: "<?php echo e(route('listings.leads.archive')); ?>",
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
                                dataTable.ajax.reload();
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


            }


        })



        function toggleAdvanceSearch() {
            $("#collapseFilterForm").toggleClass("show")
        }

        $(document).on("click", "#saveBtn", function() {

            if ($('#status_id').val() == 5) {
                if ($('#property_id').val() != 'nullVal') {

                    $('#addContact').click();
                }
            } else if ($('#status_id').val() == 6) {
                if ($('#property_id').val() == 'nullVal') {
                    $('#unSuccessfulModal').modal('show');
                }
            } else {
                $("#submitBtn").trigger("click")
            }
        })

        $('#preFillOption2').select2({
            placeholder: 'Select any reason',
            allowClear: true,
            closeOnSelect: true,
        });
        $(document).ready(function() {
            $('#preFillOption2').change(function() {
                var option = $(this).find('option:selected').val();
                if (option == 'Other') {
                    $('#unsuccessful_note').val('')
                    $('#unsuccessful_note').removeClass('d-none');
                    $('#unsuccessful_note').focus();
                } else {
                    $('#unsuccessful_note').val(option)
                    $('#unsuccessful_note').addClass('d-none');
                }
            });
        })

        $(document).on("click", "#saveUnsuccessful", function() {
            $('#screen_loader').removeClass('d-none');
            let unsuccessful_note = $('#unsuccessful_note').val();
            let id = $('#id').val();
            if (unsuccessful_note == "") {
                showToastify("Please Select Reason For Unsuccessful Listing Lead.", "error")
                return;
            } else {
                $.ajax({
                    url: "<?php echo e(route('listings.leads.saveReason.listingleads')); ?>",
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        id: id,
                        unsuccessful_note: unsuccessful_note,
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


        })






        function initMap(lat, long, title) {
            const myLatLng = {
                lat: lat,
                lng: long
            };
            const map = new google.maps.Map(document.getElementById("google-map"), {
                zoom: 15,
                center: myLatLng,
            });

            new google.maps.Marker({
                position: myLatLng,
                map,
                title: title,
            });
            $("#mapModal").modal("show");
        }

        $(document).on("click", ".view-in-map", function() {
            let lat = $(this).data("lat");
            let long = $(this).data("long");
            let path = $(this).data("path");
            initMap(lat, long, path)
        })

        // removed this function as it was not used anywhere
        $(document).on("change", "#agent_idss", function() {
            let user_id = $(this).val();
            $("#agent_container").html("");
            // $('#screen_loader').removeClass('d-none');
            $(".location_id").val("").trigger("change");
            $(".location_id").html("<option value='' selected disabled>Select location</option>");
            let property_id = dataObj.id;
            if (user_id) {
                let viewDetails =
                    `<a class='userDetails' style="cursor:pointer;" data-agent-id=${user_id}>View Details</a>`;
                $("#agent_container").append(viewDetails);
                $.ajax({
                    url: "<?php echo e(route('ajax.user-sub-community-listing-leads')); ?>?property_id=" + property_id,
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        user_id: user_id
                    },
                    success: function(result) {
                        let locationSelected = dataObj?.community_property_id || dataObj
                            ?.sub_community_id;
                        locationSelect2(dataObj.id);
                        let options = "";
                        result?.data.map(item => {
                            if (item.community_properties.length > 0) {
                                options +=
                                    `<option value="${item.id}" data-type="sub_community" data-lat="${item.latitude}" data-long="${item.longitude}" data-path="${item.path_name}" data-alternate_path_name="${item.alternate_path_name}">${item.path_name}</option>`
                                item.community_properties?.map(item1 => {
                                    options +=
                                        `<option value="${item1.id}" data-type="community_property" data-lat="${item1.latitude}" data-long="${item1.longitude}" data-path="${item1.path_name}" data-alternate_path_name="${item.alternate_path_name}">${item1.path_name}</option>`
                                })
                            } else {
                                options +=
                                    `<option value="${item.id}" data-type="sub_community" data-lat="${item.latitude}" data-long="${item.longitude}" data-path="${item.path_name}" data-alternate_path_name="${item.alternate_path_name}">${item.path_name}</option>`
                            }
                        })

                        $(".location_id").append(options)

                        $(".location_id").val(dataObj?.community_property_id || dataObj
                            ?.sub_community_id).trigger("change")


                    },
                    error: function(result) {
                        console.log(result);
                        // $('#screen_loader').addClass('d-none');

                    }
                });
            }
        })
        // removed this function as it was not used anywhere


        $(document).on("change", ".location_id", function() {
            $(".location_address").html("")
            $(".location_type").val("")


            // let selectedData = $(this).select2('data')[0];
            let lat = $('.location_id option:selected').attr('data-lat')
            let long = $('.location_id option:selected').attr('data-long')
            let path = $('.location_id option:selected').attr('data-path')
            let alternate_path_name = $('.location_id option:selected').attr('data-alternate_path_name')
            let type = $('.location_id option:selected').attr('data-type')



            //lat = lat == 'undefined' ? selectedData.lat : '';
            // selectedData = JSON.parse(selectedData);
            // console.log(selectedData)
            if (lat && long) {
                $(".location_address").html(
                    `<a class="view-in-map" href="javascript:void(0)" data-path="${path}" data-lat="${lat}" data-long="${long}">${path}  </a> ${alternate_path_name!= "null" ? '<i class="ri  ri-information-line" title="'+alternate_path_name+'"></i>': ""}`
                );
                $(".location_type").val(type)
            }


        })
        // Not sure why was this used so commented
        // $("#search_created_from").change(function() {
        //     //console.log($(this).val());
        // });

        $('.location_id').on('select2:select', function(e) {
            $(".location_address").html("")
            $(".location_type").val("")
            var data = e.params.data;
            // console.log(data.long);
            let lat = data.lat;
            let long = data.long;
            let path = data.path;
            let alternate_path_name = data.alternate_path_name;
            let type = data.type;
            console.log(alternate_path_name);
            if (path != null) {
                $(".location_address").html(
                    `<a class="view-in-map" href="javascript:void(0)" data-path="${path}" data-lat="${lat}" data-long="${long}">${path} </a>  ${alternate_path_name != null ? '<i class="ri  ri-information-line" data-bs-toggle="tooltip" data-bs-placement="right" title="'+alternate_path_name+'"></i>': ""}`
                );
            }
            $(".location_type").val(type)
        });

        function locationSelect2(id) {
            $(".location_address").html("");
            $(".location_type").val("");

            $('.location_id').select2({
                placeholder: 'Tower, community, locality',
                ajax: {
                    url: '<?php echo e(route('ajax.sub.community-listing-leads.select2')); ?>?id=' + id,
                    dataType: 'json',
                    type: 'GET',
                    processResults({
                        data
                    }) {
                        let options = '';
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    text: `${item.text}`,
                                    description: `${item.path}`,
                                    id: item.id,
                                    'lat': item.lat,
                                    'long': item.long,
                                    'path': item.path,
                                    'alternate_path_name': item.alternate_path_name,
                                    'type': item.type
                                }
                            })
                        };
                    }
                },
                templateResult: formatResult, // Add templateResult function
                escapeMarkup: function(markup) {
                    return markup;
                }
            });


            $('#addContactModal .location_id').select2({
                placeholder: 'Tower, community, locality',
                dropdownParent: $("#addContactModal"),
                ajax: {
                    url: '<?php echo e(route('ajax.sub.community-listing-leads.select2')); ?>?id=' + id,
                    dataType: 'json',
                    type: 'GET',
                    processResults({
                        data
                    }) {
                        let options = '';
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    text: `${item.text}`,
                                    description: `${item.path}`,
                                    id: item.id,
                                    'lat': item.lat,
                                    'long': item.long,
                                    'path': item.path,
                                    'alternate_path_name': item.alternate_path_name,
                                    'type': item.type
                                }
                            })
                        };
                    }
                },
                templateResult: formatResult, // Add templateResult function
                escapeMarkup: function(markup) {
                    return markup;
                }
            });


        }

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

        function formatResult(result) {
            if (!result.id) {
                return result.text;
            }

            let $result = $('<div>');
            $result.text(result.text);
            $result.append('<div class="description">' + result.description +
                '</div>'); // Use 'description' as HTML content
            return $result;
        }













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

        function toggleCirteriaForm(flag) {
            if (flag) {
                $("#criteriaTabLoader").addClass('d-none');
                $("#criteriaFormDiv").removeClass("d-none");
            } else {
                $("#criteriaTabLoader").removeClass('d-none');
                $("#criteriaFormDiv").addClass("d-none");
            }
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
                                dataTable.ajax.reload();
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

        $(document).on('click', '#hardDeleteBtn', function(event) {
            deleteData($('#id').val(), true);
        });




        $(document).on("click", ".statusFilter", function() {
            let status = $(this).data("status");
            if ($("#append-here").has("#search_status").length > 0) {

                $('#search_status').val(status).trigger('change');
                $("#search_status option:selected").removeAttr("selected");
                $("#search_status option[value='" + status + "']").attr("selected", true);
                $("#search_status").trigger("change");
                $("#searchSearchBtn").trigger("click");
            } else {
                $('#applicator').val('status').trigger('change');
                setTimeout(function() {
                    $('#search_status').val(status).trigger('change');
                    $("#search_status option:selected").removeAttr("selected");
                    $("#search_status option[value='" + status + "']").attr("selected", true);
                    $("#search_status").trigger("change");
                    $("#searchSearchBtn").trigger("click");
                }, 1000);
            }
            // let status = $(this).data("status");
            // $("#search_lead_statuses option:selected").removeAttr("selected");
            // $("#search_lead_statuses option[value='" + status + "']").attr("selected", true);
            // $("#search_lead_statuses").trigger("change");
            // $("#searchSearchBtn").trigger("click");
        });

        $(document).on("click", ".statusFilterClear", function() {
            $("#search_status option:selected").removeAttr("selected").trigger("change");
            $("#search_status").val("").trigger("change");
            $("#searchSearchBtn").trigger("click");
        });
        $(document).ready(function() {
            // this function opens the
            setTimeout(function() {

                var open_id = "<?php echo e(Request('lead')); ?>";
                if (open_id) {
                    $('.dataTables_filter .form-control').val(open_id).trigger('keyup');
                }

            }, 500);
        });
        if (typeof scrollAtBottom !== 'function') {
            function scrollAtBottom($el) {
                let element = document.getElementById($el);
                element.scrollTop = element.scrollHeight;
            }
        }

        function highlightRow(row) {
            row.removeClass('selected');
            dataTable.$('tr.selected').removeClass('selected');
            row.addClass('selected');
        } // highlightRow





        $(document).on("click", "#exportBtn", function() {
            $("#exportForm").removeClass("was-validated");
            $("#exportModal").modal("show")
        })



        $(document).on("change", "#select-all-export", function() {
            $(`.export-columns`).prop("checked", this.checked).trigger("change");
        })

        function decodedFilterData(filtersData) {
            return {

            };
        }

        $(document).on("submit", "#exportForm", function(event) {
            event.preventDefault();
            let isSelected = $(".export-columns:checked").length > 0;
            if (!isSelected) {
                showToastify("Please select at least one column.", "error");
                return;
            }
            let exportRoute = "<?php echo e(route('listings.leads.export-excel')); ?>";
            let columns = $(this).serialize();
            let filtersDataDecoded = decodedFilterData(filtersData);
            filtersDataDecoded = objectToQueryString(filtersDataDecoded)
            window.open(`${exportRoute}?${columns}&${filtersDataDecoded}`);

            $("#exportForm").trigger("reset");
            $("#exportModal").modal("hide");
            return;
        })



        $("#newBtn").click(function() {
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Listing Leads Create')): ?>
                isUpdate = false;
                $('#mainForm').trigger('reset');
                $('#mainForm').removeClass('was-validated');
                $(".inputs").prop("disabled", false)
                $(".inputs").val("").trigger("change")
                $(".inputs").removeClass("is-invalid")
                $("#id").val("")
                $("#saveBtn, #closeBtn").removeClass("d-none");
                $("#newBtn, #editBtn, #deleteBtn, #followUpMenusTab, #bulkAssignBtn, #dropdownMenuLink1").addClass(
                    "d-none");
                $("#addOnAgentDiv, .addHide").addClass("d-none");
                toggleForm(true)
                $("#col-logs").addClass("d-none");
                // resetStatus()
                $("#calling-button").addClass("d-none");
                $('.rmDark').removeClass('bg-dark')
                $('.OpenStatus').removeClass('ribbon-info')
                $('.OpenStatus').addClass('ribbon-soft-info')
                $('.ClassStatus').addClass('ribbon-soft-primay')
                $('.ClassStatus').removeClass('ribbon-soft-info')
                $("#call_now_button").addClass("d-none");
                $("#start-call-button").addClass("d-none");
                $("#whatapp-button").addClass("d-none");

                $('#call_rec').html('')

                $('#mobileDiv1').addClass('col-md-9')
                $('#call_now_button').hide()
                $('#mobileDiv1').removeClass('col-md-7')

                $('.open_sub_status').find('span').addClass('randomClass');
                $('#lead_status_id').val('');
                $('#lead_sub_status_id').val('');
                mode = 'create';
                let agent_logged_in = "<?php echo e(auth()->user()->id); ?>";
                $("#agent_id").val(agent_logged_in).trigger('change');
                $('#lead_status_id').val('');
                $('#lead_sub_status_id').val('');
            <?php endif; ?>
        });

        $(document).on("click", "#closeBtn", function() {
            mode = 'view';
            toggleForm(false)
            $('#generate-listing-btn').addClass('d-none')
            $('#addContact').addClass('d-none')
            $('#generate-listing-btn').addClass('d-none')

            $('#dropdownMenuLink1').addClass('d-none');

        })

        let viewUrl = "<?php echo e(route('admin.work.leads.view', ['lead' => '1'])); ?>";
        viewUrl = viewUrl.slice(0, -1);



        dataTable.on('click', 'tbody tr', function(e) {

            console.log(mode);
            console.log('===');
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
            $('#agents_dropdown').val('').trigger("change")
            $('#agents_dropdown').removeClass('is-invalid');
            $('#generalModal').modal('show');

            // $('#bulkReassignLeadsModal').modal('show');
        });
        // $(document).on("change", "#selectAction", function() {

        //     var selectedOption = this.options[this.selectedIndex].value;

        //     if (selectedOption == 'Re-Assign') {
        //         $('#agent_dropdown').removeClass('d-none');
        //         $('#telesales_dropdown').addClass('d-none');
        //         $('#telesales_head_dropdown').addClass('d-none');

        //     } else if (selectedOption == 'Manager') {
        //         $('#agent_dropdown').addClass('d-none');
        //         $('#telesales_dropdown').removeClass('d-none');
        //         $('#telesales_head_dropdown').addClass('d-none');
        //     }

        // })



        $('#selectAction').select2({
            placeholder: 'Select Action',
            allowClear: false,
            closeOnSelect: true,
            dropdownParent: $('#generalModal')
        });


        $('#bulk_leads_manager').select2({
            placeholder: 'Select Manager',
            allowClear: false,
            closeOnSelect: true,
            dropdownParent: $('#generalModal')
        });


        $(document).on("click", "#bulkAssignment", function() {
            $('#bulkAssignmentLoading').removeClass('d-none');
            $('#bulkAssignment').addClass('d-none');
            var selectedOption = 'Re-Assign';
            var id;
            if (selectedOption == 'Re-Assign') {
                id = $('#agents_dropdown').val();
            } else if (selectedOption == 'Manager') {
                id = $('#bulk_leads_manager').val();
            }
            let email_notification = $('#email_notification').prop('checked');
            let system_notification = $('#system_notification').prop('checked');
            let telegram_notification = $('#telegram_notification').prop('checked');
            if (id == null) {
                $('#agents_dropdown').addClass('is-invalid');
                showToastify("Please select Agent.", "error")
                $('#bulkAssignmentLoading').addClass('d-none');
                $('#bulkAssignment').removeClass('d-none');
                return;
            }
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
                        url: "<?php echo e(route('listings.leads.general.bulk.assign.leads.listing')); ?>",
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
                            $('#bulkAssignmentLoading').addClass('d-none');
                            $('#bulkAssignment').removeClass('d-none');

                            if (result.status == '1') {
                                showToastify(result.msg, "success")
                                $("#selectAction").val('').trigger("change")
                                // $('#agent_dropdown').addClass('d-none');
                                // $('#telesales_dropdown').addClass('d-none');
                                // $('#telesales_head_dropdown').addClass('d-none');

                                $("#generalModalForm").trigger("reset")
                                $("#generalModal").modal("hide")
                                $('#agents_dropdown').val('').trigger("change")
                                $("#checkAll").prop("checked", this.checked)
                                document.getElementById("checkAll").checked = !1
                                dataTable.ajax.reload();
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
                            // console.table(data);
                        }
                    });
                }

                // Ajax Form goes here
            }
        })



        function updateMultiOption() {
            var countCheckedCheckboxes = $('input[name="chk_child"]:checked').length;
            console.log(countCheckedCheckboxes);
            $('#bulk_leads_count').html(countCheckedCheckboxes);
            $('#count_selected').html(countCheckedCheckboxes);
            if (countCheckedCheckboxes >= 1) {
                if ($('.multi_options').length === 0) {

                    $(`<div class="multi_options" style="display:inline-block; margin-left:10px;">
                          <div class="btn-group">
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['Listing Leads Assign Agent', 'Listing Leads Archive', 'Listing Leads Delete'])): ?>

                            <button class="btn btn-primary btn-sm dropdown-toggle action-bulk" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Bulk Actions
                            </button>

                            <div class="dropdown-menu">

                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Listing Leads Assign Agent')): ?>
                                <a class="dropdown-item" href="javascript:;" id="bulk_reassign_btn"><i class="ri-share-forward-2-line"></i> Assign</a>
                                <?php endif; ?>



                                <div class="dropdown-divider"></div>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Listing Leads Archive')): ?>
                                <a type="button" class="dropdown-item" id="archiveBtn" data-id=""><i class=" ri-inbox-archive-line"></i> Archive</a>
                                <?php endif; ?>

                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Listing Leads Delete')): ?>
                                 <a class="dropdown-item" href="javascript:;" onclick="deleteMultiple()"><i class=" ri-delete-bin-line"></i> Delete</a>
                                 <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                   </div>`).insertAfter('.dt-buttons');
                }
            } else {
                $('.multi_options').remove();
            }
        }
        // <a class="dropdown-item" href="javascript:;">Change Status</a>


        $(document).on('submit', '#mainForm', function(event) {
            $("#saveBtn").prop('disabled', true);
            event.preventDefault();


            var formData = new FormData($("#mainForm")[0]);
            $("#mobile").val(mobileInput.getNumber())
            // $("#phone").val(phoneInput.getNumber())
            formData.append("mobile", mobileInput.getNumber())
            // formData.append("phone", phoneInput.getNumber())
            $.ajax({
                url: isUpdate ? "<?php echo e(route('listings.leads.update')); ?>" :
                    "<?php echo e(route('listings.leads.store')); ?>",
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

                        dataTable.ajax.reload();
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
                    dataTable.ajax.reload();

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

        });


        $(document).on("change", "#status_id", function() {

            let statusValue = $('#status_id').val();
            if (statusValue == 1) {
                $('.applyValidation').attr('required', false);
                $(".clear_req").html('');
                $('.new_status').attr('required', true);
                $(".new_status_req").html('*');

            }
            if (statusValue == 2) {
                $('.applyValidation').attr('required', false);
                $(".clear_req").html('');
                $('.assigned_status').attr('required', true);
                $(".assigned_status_req").html('*');
            }

            if (statusValue == 6) {
                $('.applyValidation').attr('required', false);
                $(".clear_req").html('');
                $('.unsuccessful_status').attr('required', true);
                $(".unsuccessful_status_req").html('*');
            }
            if (statusValue == 7) {
                $('.applyValidation').attr('required', false);
                $(".clear_req").html('');
                $('.not_answered_status').attr('required', true);
                $(".not_answered_status_req").html('*');
            }

            if (statusValue == 4 || statusValue == 3) {
                $('.applyValidation').attr('required', false);
                $(".clear_req").html('');
                // console.log('here');
                $('.qualifiled_status').attr('required', true);
                $(".qualifiled_status_req").html('*');
            }
        })

        $('#sourceDatatable tbody').on('click', 'a.edit-link', function() {
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Listing Leads Edit')): ?>
                let id = $(this).data('id');
                editData(id);
            <?php endif; ?>

        });

        function getLead(id) {

            $("#call_now_button").removeClass("d-none");
            $("#start-call-button").removeClass("d-none");
            $("#whatapp-button").removeClass("d-none");
            $.ajax({
                url: "<?php echo e(route('listings.leads.get')); ?>",
                type: "get",
                data: {
                    id: id
                },
                success: function(result) {
                    $('#property_id').val('nullVal');
                    // console.table(result.data);
                    if (result.status == '1') {
                        dataObj = result.data;

                        $("#locations").val('');

                        Object.keys(result.data).map((index) => {
                            $(`#${index}`).val(result.data[index]).trigger("change")

                        })
                        if (result.data.recording_file) {
                            $('#call_rec').html(
                                `
                                <?php if(!auth()->user()->hasRole('Agent')): ?>
                                <div class="col-12 mt-2 mt-4"><h5>Recording File</h5></div>
                                <audio style="width: 100%;height:50px" src="${result.data.recording_file}" controls></audio>
                                <?php endif; ?>
                                `
                            );
                        } else {
                            $('#call_rec').html('')
                        }

                        if (result.data.mobile) {
                            $('#whatsappBtn').removeClass('d-none');
                            let full_name = result.data.first_name + ' ' + result.data.last_name;
                            $('#whatsappBtn').attr('href', 'https://api.whatsapp.com/send?phone=' + result.data
                                .mobile + '&text=Hi ' + full_name +
                                ', This is <?php echo e(auth()->user()->full_name); ?> from AX CAPITAL.');

                        } else {
                            $('#whatsappBtn').addClass('d-none');
                        }


                        $('#listing_lead_id').val(id)
                        $('#listing_lead_id_2').val(id)


                        getLeadNotes();

                        if (result.data.property_id && result.data.contact_id) {
                            $('#addContact').addClass('d-none');
                            $('#generate-listing-btn').addClass('d-none')
                        } else {
                            $('#addContact').removeClass('d-none');
                            $('#generate-listing-btn').removeClass('d-none')
                        }
                        $('#location_address').html(result.data.location);
                        $('.location_address').html(result.data.location);

                        if (result.data.status_id == 5 && result.data.contact_id && result.data.property_id) {
                            $('#editBtn').addClass('d-none');
                        }
                        // alert(result.data.community_property_id);

                        // locationSelect2(dataObj.community_id);
                        $('#source_id').val('uTxuYi-' + result.data.source_id).trigger("change")

                        locationSelect2(result.data.id);

                        var url = "<?php echo e(route('listings.leads.index')); ?>" + "?id=" + result.data.id;
                        // console.log(url);
                        window.history.pushState('data', '', url);
                        let option = "";
                        if (dataObj.community_property) {
                            $('#location_address').html('<a href="javascript:void(0)">' + dataObj
                                .community_property.path_name + '</a>');

                            option +=
                                `<option value="${dataObj.community_property.id}" data-type="community_property" data-lat="${dataObj.community_property.latitude}" data-long="${dataObj.community_property.longitude}" data-path="${dataObj.community_property.path_name}" data-alternate_path_name="${dataObj.community_property.alternate_path_name}" selected>${dataObj.community_property.path_name}</option>`
                            $('#location_type').val('community_property');

                        } else if (dataObj.sub_community) {
                            $('#location_address').html('<a href="javascript:void(0)">' + dataObj.sub_community
                                .path_name + '</a>');

                            option +=
                                `<option value="${dataObj.sub_community.id}" data-type="sub_community" data-lat="${dataObj.sub_community.latitude}" data-long="${dataObj.sub_community.longitude}" data-path="${dataObj.sub_community.path_name}" data-alternate_path_name="${dataObj.sub_community.alternate_path_name}" selected>${dataObj.sub_community.path_name}</option>`
                            $('#location_type').val('sub_community');
                        } else if (dataObj.community) {
                            $('#location_address').html('<a href="javascript:void(0)">' + dataObj.community
                                .path_name + '</a>');
                        }

                        $("#location_id").append(option);
                        $("#listing_location_id").append(option);
                        mobileInput = initPhoneValidator("mobile")
                        if (mode == 'view') {
                            $(".mobileValidation").html("");
                            $("#mobile").removeClass("is-invalid");
                        }
                        if (result.data.status_id == 6) {
                            $('#editBtn').addClass('d-none')
                            $('#generate-listing-btn').addClass('d-none')
                        }
                        const widgetObj = {
                            id: dataObj.id,
                            model_key: 'listing_lead_id',
                            call_log: 0,
                            meeting_log: 1,
                            task_log: 0,
                            followup_log: 1,
                            viewing_log: 0,
                            activeTab: 'meeting_log'

                        };
                        getEventsCard(widgetObj);
                        const widgetObjs = {
                            id: dataObj.id,
                            model_key: 'listing_lead_id',
                            call_log: 1,
                            meeting_log: 0,
                            task_log: 0,
                            followup_log: 0,
                            viewing_log: 0,
                            activeTab: 'call_log'

                        };
                        getEventsCard(widgetObjs, "#callLogWidget");
                        getActivityLog(result.data.id);

                        setTimeout(function() {
                            $("#log-meeting-tab").trigger("click");
                        }, 3000);
                    }
                    toggleForm(true)

                }
            });
        }

        function viewData(id) {
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Listing Leads View')): ?>

                $(".inputsCriteria").val("").trigger("change")
                toggleCirteriaForm(false);
                $("#addOnAgentDiv, #newBtn, #closeBtn, #followUpMenusTab, .addHide,#dropdownMenuLink1").removeClass(
                    "d-none");
                $("#saveBtn").addClass("d-none");
                $("#collapseContactForm").addClass("show");
                $("#editBtn, #bulkAssignBtn").removeClass("d-none");
                $("#newBtn").addClass("d-none");
                $(".inputs").prop("disabled", true);
                $(".inputs").prop("readonly", false);
                $("#mainForm").trigger("reset")
                $("#id, #criteria_lead_id").val(id)
                $(".inputs").val("").trigger("change")
                $(".inputs").removeClass("is-invalid")
                $("#meeting_lead_id, #task_lead_id").val(id)
                isUpdate = true; //set isUpdate to true
                let leadData = getLead(id);
                $("#col-logs").removeClass("d-none")
                $("#start-call-button").removeClass("d-none");
                $("#calling-button").addClass("d-none");
            <?php endif; ?>
        }

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

        function addEvent(type, refreshBtnId = null) {
            let agent = isSelect2Initiated($("#agent_id")) ? $("#agent_id").select2("data")[0] : null;
            let property = null;
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
                lead: null,
                listing_lead: {
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
        $(document).on('click', '#editBtn', function(event) {
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Listing Leads Edit')): ?>
                editData($('#id').val());
                $('#editBtn').addClass('d-none');
                $('#note').attr('readonly', true);
                $('#source_id').prop('disabled', true);
            <?php endif; ?>
        });




        function editData(id) {
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Listing Leads Edit')): ?>
                isUpdate = true;
                $("#col-logs").addClass("d-none")
                $(".inputs").prop("disabled", false)

                $(".inputs").removeClass("is-invalid")
                $('#mainForm').removeClass('was-validated');
                $("#saveBtn, #closeBtn").removeClass("d-none")
                $("#editBtn, #newBtn, #deleteBtn, #bulkAssignBtn, #dropdownMenuLink1").addClass("d-none")
                getLead(id);
                mode = 'edit';
                $('#editBtn').addClass('d-none');
            <?php endif; ?>
        }


        $('#search_sub_communities').select2({
            placeholder: 'Tower, community, locality',
            allowClear: true,
            closeOnSelect: false,
            ajax: {
                url: '<?php echo e(route('ajax.sub-community.property.select2')); ?>',
                dataType: 'json',
                data: function(term, page) {
                    return {
                        q: term, // search term
                        communities: $("#search_sub_communities").val(),
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




        $('#search_community_properties').select2({
            placeholder: 'Tower, community, locality',
            allowClear: true,
            closeOnSelect: false,
            ajax: {
                url: "<?php echo e(route('ajax.sub.community-properties.select2')); ?>",
                dataType: 'json',
                data: function(term, page) {
                    return {
                        term: term.term, // search term
                        q: term, // search term
                        communities: $("#search_community_properties").val(),
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




        let locations = locationSelect2("");

        function deleteMultiple() {
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Bulk Delete Listing Leads')): ?>
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
                                    url: "<?php echo e(route('listings.leads.delete.multi')); ?>",
                                    type: 'POST',
                                    dataType: 'json',
                                    data: {
                                        ids: ids_array
                                    },
                                    success: function(result) {
                                        if (result.status == '1') {
                                            showToastify(result.msg, "success")
                                            $('.multi_options').remove();
                                            dataTable.ajax.reload();
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
            $(".selectable-checkbox").prop("checked", this.checked)
        });














        $(document).on("select2:open", () => {
            document.querySelector(".select2-container--open .select2-search__field").focus()
        })

        // $("#sidebarWork").addClass('collapse show');
        $("#listing-leads").addClass('active');

        dataTable.on('draw.dt', function() {
            $(".chk_child").prop("checked", false);
            $('.multi_options').remove();
            $('#checkAll').prop("checked", false);
        });







        $('#search_locations').select2({
            placeholder: 'Select locations',
            allowClear: true,
            closeOnSelect: false,
            ajax: {
                url: '<?php echo e(route('ajax.sub-community.property.select2')); ?>',
                dataType: 'json',
                type: 'GET',
                data: function(term, page) {
                    return {
                        q: term, // search term
                        communities: $("#search_communities").val(),
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
                                'alternate_path_name': item.alternate_path_name,
                                'type': item.type
                            }
                        })
                    }
                }
            }
        });
        let s_sub_communities = [];
        let s_properties = [];

        $('#search_locations').on('select2:select', function(e) {
            $("#search_sub_communities").val("")
            $("#search_properties").val("")
            let data = e.params.data;
            let lat = data.lat;
            let long = data.long;
            let path = data.path;
            let alternate_path_name = data.alternate_path_name;
            let type = data.type;
            if (data.type == 'sub_community') {
                s_sub_communities.push(data.id);
            } else {
                s_properties.push(data.id);
            }
            $("#search_sub_communities").val(s_sub_communities)
            $("#search_properties").val(s_properties)


        });
        $('#search_locations').on("select2:unselect", function(e) {
            let data = e.params.data;
            if (data.type == 'sub_community') {
                let index = s_sub_communities.indexOf(parseInt(data.id));
                if (index > -1) { // only splice array when item is found
                    s_sub_communities.splice(index, 1); // 2nd parameter means remove one item only
                }
            } else {
                let index = s_properties.indexOf(parseInt(data.id));
                if (index > -1) { // only splice array when item is found
                    s_properties.splice(index, 1); // 2nd parameter means remove one item only
                }
            }
            $("#search_sub_communities").val(s_sub_communities)
            $("#search_properties").val(s_properties)

        });








        function getLeadNotes() {
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Listing Leads Notes List')): ?>
                let listing_lead_id = $("#id").val();
                $("#notes").html("");
                $.ajax({
                    url: "<?php echo e(route('ajax.lead-listing-notes')); ?>",
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        listing_lead_id
                    },
                    success: function(result) {
                        let notes = "";
                        result?.data.map(item => {
                            let date = moment(item.created_at).format("DD/MM/YY HH:mm")
                            let imageUrl =
                                `${item.created_by ? item.created_by.thumb_url : "<?php echo e(URL::asset('assets/images/logo/favicon-32x32.png')); ?>"}`;
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
            <?php endif; ?>
        }

        $(document).on("click", "#addNoteButton", function() {
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Listing Leads Notes Create')): ?>
                let id = $("#id").val();

                if (id == "") {

                    showToastify("Please save record before adding a note.", "error")
                    return;
                }

                let note = $("#leadnote").val();
                if (note == "") {

                    return;
                }

                var formData = new FormData();
                formData.append("listing_lead_id", id)
                formData.append("note", note)
                $.ajax({
                    url: "<?php echo e(route('listings.leads.add-note')); ?>",
                    type: 'POST',
                    dataType: 'json',
                    data: formData,
                    cache: false,
                    processData: false,
                    contentType: false,
                    success: function(result) {
                        // console.table(result)
                        if (result.status == '1') {
                            showToastify(result.msg, "success")
                            $("#leadnote").val("").trigger("change");
                            getLeadNotes()
                        }
                    },
                    error: function(jqXhr, json, errorThrown) {
                        console.log('Error')
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


        $(document).on("click", "#addContact", function() {

            $("#addContactForm select").val("").trigger("change")
            $("#addContactForm").trigger("reset")
            $("#addContactForm").removeClass("was-validated")
            let agent_id = $("#agent_id").val();
            $("#contact_agent_id").val(agent_id).trigger("change");
            $("#contact_type_id").val(3).trigger("change");
            $("#addContactModal").modal({
                backdrop: 'static',
                keyboard: false
            })
            $('#listing_first_name').val($('#first_name').val());
            $('#listing_last_name').val($('#last_name').val());
            $('#listing_location_type').val($('#location_type').val());
            $('#listing_email').val($('#email').val());
            $('#listing_mobile').val($('#mobile').val());
            $('#listing_title').val($('#title').val());
            $('#listing_unit_no').val($('#unit_no').val());
            $('#listing_bed_id').val($('#bed_id').val()).trigger("change");
            $('#listing_bath_id').val($('#bath_id').val()).trigger("change");
            $('#listing_agent_id').val($('#agent_id').val()).trigger("change");
            $('#listing_for').val($('#for').val()).trigger("change");
            $('#listing_property_category_id').val($('#property_category_id').val()).trigger("change");
            $('#listing_source_id').val($('#source_id').val()).trigger("change");
            $('#listing_note').val($('#note').val()).trigger("change");
            resetModal();

            $("#addContactModal").modal("show")

        })






        $(document).on("change", "#listing-mobile", function() {
            $("#whatsapp").val(mobileInput.getNumber()).trigger("change");
            checkContactExistence(mobileInput.getNumber(), "mobile", "Contact")
        })















        function resetModal() {
            $('#listing_owner_id').val('');
            $('#steparrow-gen-info').addClass('show active');
            $('#steparrow-description-info').removeClass('show active');
            $('#pills-experience-tab').removeClass('active');

            $('#steparrow-gen-info-tab').addClass('active');
            $('#steparrow-gen-info-tab').removeClass('done');
            $('#pills-experience').removeClass('show active');

            $('#steparrow-description-info-tab').removeClass('active done');
        }
    </script>
    <script>
        $('.ownerInfo').click(function() {
            var allValid = true;
            $('.contact_req').each(function() {
                var input = $(this);
                var value = input.val();

                if (value === null || value === '') {
                    input.addClass('is-invalid');
                    // Show the error message
                    input.siblings('.invalid-feedback').text('This field is required.');
                    allValid = false;
                } else {
                    input.removeClass('is-invalid');
                    input.siblings('.invalid-feedback').text('');
                }
            });

            if (allValid) {
                $('#steparrow-gen-info').removeClass('show active');
                $('#steparrow-description-info').addClass('show active');
                $('#steparrow-gen-info-tab').removeClass('active');
                $('#steparrow-gen-info-tab').addClass('done');
                $('#steparrow-description-info-tab').addClass('active');
                checkDuplicate();
            }

        });

        $('.statusFilter').click(function() {

            if ($(this).data('status') == '2') {
                dataTable.column(13).visible(true);
            } else {
                dataTable.column(13).visible(false);
            }


        })
        $('.statusFilterClear').click(function() {
            dataTable.column(13).visible(false);
        })

        $('.checkDuplicate').change(function() {
            checkDuplicate();

        })



        function checkDuplicate() {
            var unit_no = $('#listing_unit_no').val();
            var location_id = $('#listing_location_id').val();
            var location_type = $('#listing_location_type').val();
            var location_for = $('#listing_for').val();
            if (unit_no && location_id && location_type && location_for) {
                $.ajax({
                    url: "<?php echo e(route('ajax.check_similar_listing')); ?>",
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        unit_no: unit_no,
                        location_id: location_id,
                        location_type: location_type,
                        for: location_for,
                    },
                    success: function(result) {
                        if (result.status == '1') {
                            if (result.data) {
                                $('#showDuplicationMessage').html(result.message);
                                $('#duplicationMessage').removeClass('d-none');
                                //$('.saveListing').prop('disabled', true);
                            }
                        } else {
                            $('#showDuplicationMessage').html('');
                            $('#duplicationMessage').addClass('d-none');
                            $('.saveListing').prop('disabled', false);
                        }
                    },
                    error: function(result) {
                        console.log(result);
                    }
                });
            }
        }

        $(document).ready(function() {
            let isNewTab = false;
            setTimeout(function() {
                var open_ref_id = "<?php echo e(Request('id')); ?>";
                if (open_ref_id) {
                    isNewTab = true;
                    $('.dataTables_filter .form-control').val(open_ref_id).trigger('keyup');
                }

            }, 500);

            setTimeout(function() {
                if (isNewTab) {
                    $('#sourceDatatable tbody tr:eq(0)').click();
                }
            }, 1000);
        })


        $('.propertyInfo').click(function() {
            var allValid = true;
            $('.property_req').each(function() {
                var input = $(this);
                var value = input.val();

                if (value === null || value === '') {
                    input.addClass('is-invalid');
                    // Show the error message
                    input.siblings('.invalid-feedback').text('This field is required.');
                    allValid = false;
                } else {
                    input.removeClass('is-invalid');
                    input.siblings('.invalid-feedback').text('');
                }
            });

            if (allValid) {
                $('#steparrow-description-info').removeClass('show active');
                $('#pills-experience').addClass('show active');
                $('#steparrow-description-info-tab').removeClass('active');
                $('#steparrow-description-info-tab').addClass('done');
                $('#pills-experience').addClass('active');

            }

        });

        // calling feature start

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

        function startTimer(secs) {
            timeInSecs = parseInt(secs);
            ticker = setInterval("tick()", 1000);
        }

        function listingLeadMakeCall() {
            let lead_id = $("#id").val();
            let dailer_id = $("#dailer_id").val();

            var formData = new FormData();

            formData.append("listing_lead_id", $("#id").val());
            formData.append("dailer_id", $("#dailer_id").val());

            $.ajax({
                url: makeCallLink,
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function(result) {
                    if (result.status == '1') {
                        // getCallLog(lead_id)
                        // getLeadNotes(lead_id)
                        showToastify(result.msg)
                        $("#start-call-button").addClass("d-none");
                        $("#calling-button").removeClass("d-none");
                        $text = "Call Started <br><br> <strong>" + $('#first_name').val() +
                            " " + $('#last_name').val() + "</strong>"
                        $('#call_id').html($text)
                        $('#countdown').html("");
                        setTimeout(() => {
                            $("#start-call-button").removeClass("d-none");
                            $("#calling-button").addClass("d-none");

                        }, 5000)

                    } else {
                        showToastify(result.msg, "error")
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
                        showToastify(data.message, "error")
                    }
                },
            });
        }

        function tick() {
            var secs = timeInSecs;
            if (secs > 0) {
                timeInSecs--;
            } else {
                clearInterval(ticker);
                $('#countdown').html('');
                $('#CancelBTN').addClass('d-none');
                listingLeadMakeCall();
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
        // calling feature end



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
                            url: "<?php echo e(route('contacts.bulk.sms.listingleads.sendMsg')); ?>",

                            data: {
                                beds: filtersData?.search_beds,
                                beds_operator: filtersData?.search_beds_operator,

                                baths: filtersData?.search_baths,
                                baths_operator: filtersData?.search_baths_operator,

                                communities: filtersData?.search_communities,
                                communities_operator: filtersData?.search_communities_operator,

                                sub_communities: filtersData?.search_sub_communities,
                                sub_communities_operator: filtersData
                                    ?.search_sub_communities_operator,


                                community_properties_operator: filtersData
                                    ?.search_community_properties_operator,
                                community_properties: filtersData?.search_community_properties,

                                // locations: filtersData?.search_locations,
                                // locations_operator: filtersData?.search_locations_operator,

                                unit_no: filtersData?.search_unit_no,
                                unit_no_operator: filtersData?.search_unit_no_operator,

                                // sub_communities: filtersData?.sub_communities,

                                agent_id: filtersData?.search_agents,
                                agent_operator: filtersData?.search_agents_operator,

                                lead_statuses: filtersData?.search_status,
                                lead_statuses_operator: filtersData?.search_status_operator,



                                created_from: filtersData?.search_created_from,
                                created_from_operator: filtersData?.search_created_from_operator,

                                updated_from: filtersData?.search_updated_from,
                                updated_from_operator: filtersData?.search_date_updated_operator,



                                manager_id: filtersData?.search_managers,
                                manager_operator: filtersData?.search_managers_operator,

                                sources: filtersData?.search_lead_sources,
                                sources_operator: filtersData?.search_lead_sources_operator,

                                mobile: filtersData?.search_mobile,
                                mobile_operator: filtersData?.search_mobile_operator,

                                unit_no: filtersData?.search_unit_no,
                                unit_no_operator: filtersData?.search_unit_no_operator,
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
    </script>

    <script src="<?php echo e(URL::asset('/assets/js/pages/leads.js')); ?>"></script>
    <?php echo $__env->make('admin.templates.agent_template', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php echo $__env->yieldContent('pagejs'); ?>
    <?php echo $__env->yieldPushContent('script-bottom'); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/ListingLeads/index.blade.php ENDPATH**/ ?>