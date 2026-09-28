<div class="modal zoomIn" id="uploadPhotosModal" data-bs-keyboard="false" tabindex="-1" aria-hidden="true"
     data-bs-backdrop="static">
    <div class="modal-dialog modal-lg" id="image-upload-modal">
        <div class="modal-content">
            <div class="modal-header bg-soft-danger">
                <h5 class="modal-title" id="uploadPhotosModalLabel">


                    <span>Upload Photos</span>

                </h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="close-modal"></button>
            </div>

            <div class="modal-body p-2 text-center">

                <ul class="nav nav-pills nav-justified mb-3" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link waves-effect waves-effect-tab waves-light active " data-bs-toggle="tab"
                           href="#upload-from-computer-tab" role="tab">
                            Upload from Computer
                        </a>
                    </li>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Upload Images From Google Drive')): ?>
                        <li class="nav-item">
                            <a class="nav-link waves-effect waves-effect-tab waves-light" data-bs-toggle="tab"
                               href="#upload-from-google-drive" role="tab">
                                Upload from Google Drive
                            </a>
                        </li>
                    <?php endif; ?>

                </ul>
                <div class="tab-content text-muted">
                    <div class="tab-pane active" id="upload-from-computer-tab" role="tabpanel">
                        <div class="mt-4 text-center">
                            <div>


                                <div class="dropzone" id="photos-dropzone">
                                    <div class="fallback">
                                        <input name="file" type="file" multiple="multiple">
                                    </div>
                                    <div class="dz-message needsclick" id="messagePhotosContainer">
                                        <div class="mb-3" id="messagePhotosIcon">
                                            <i class="display-4 text-muted ri-upload-cloud-2-fill"></i>
                                        </div>
                                        <h4 id="messagePhotos">Drop files here or click to upload.</h4>
                                    </div>
                                </div>

                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Upload Without Watermark')): ?>
                                    <div class="row my-2 text-start">
                                        <div class="ms-2 col-lg-3" style="">
                                            <label for="watermark" class="form-label">Add Watermark*</label>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-check form-switch form-switch-lg form-switch-success"
                                                 dir="ltr">
                                                <input type="checkbox" name="watermark" value="78645675"
                                                       class="form-check-input" id="watermark" checked>

                                            </div>


                                            
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <hr>
                                <div>
                                    <span class="form-text text-muted"><strong class="text-lg-end float-left">Note:
                                        </strong>
                                        <ul
                                            class="text-reset notification-item d-block dropdown-item position-relative active photo-list">
                                            <li>Only JPG, JPEG &amp; PNG allowed (Min 1200x900)</li>
                                            <li>Maximum file size: 3M</li>
                                            <li>Maximum 30 photos allowed </li>
                                        </ul>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Upload Images From Google Drive')): ?>
                        <div class="tab-pane" id="upload-from-google-drive" role="tabpanel">
                            <div class="mt-4">
                                <div>
                                    <div id="">
                                        <div class="row text-start p-3 folder-image-div">
                                            <form id="fetchGoogleDrivePhotos" class="modal-content needs-validation"
                                                  novalidate>

                                                <div class="mb-3">
                                                    <label for="folder_id" class="form-label">Folder Link</label>
                                                    <input type="text" class="form-control" id="folder_id"
                                                           placeholder="Enter Folder Link" name="folder_id">
                                                </div>
                                                <div class="mb-3 text-end">
                                                    <button type="submit" class="btn btn-sm btn-primary"
                                                            id="fetch-image-btn">Fetch
                                                        Photos
                                                    </button>
                                                    <button class="btn btn-outline-primary btn-sm btn-load d-none"
                                                            id="fetch-image-btn-placeholder" disabled>
                                                        <span class="d-flex align-items-center">
                                                            <span class="flex-grow-1 me-2">
                                                                Loading...
                                                            </span>
                                                            <span class="spinner-border flex-shrink-0" role="status">
                                                                <span class="visually-hidden">Loading...</span>
                                                            </span>
                                                        </span>
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                        <div id="fetched-images-div"></div>
                                    </div>

                                    <div class="progress progress-xl mt-1" id="progress-bar-div">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated"
                                             id="image-uploader-progress-bar" role="progressbar" style="width: 0%;"
                                             aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%
                                        </div>
                                    </div>

                                    <div>
                                        <span class="form-text text-muted"><strong class="text-lg-end float-left">Note:
                                            </strong>
                                            <ul
                                                class="text-reset notification-item d-block dropdown-item position-relative active photo-list">
                                                <li>Provide the Google Drive folder link or simply the folder ID.</li>
                                                <li>Ensure that you have access to the folder or that the link is publicly
                                                    shared.</li>
                                                <li>Please select a maximum of 5 photos at once and await the success
                                                    message.</li>
                                            </ul>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
            <div class="m-3 row">

                <div class="col-md-6 text-start">
                    <button type="button" class="btn btn-primary btn-sm action-primary"><span
                            id="expand-modal-images"> <i class="ri-fullscreen-line"></i> Expand </span></button>
                </div>

                <div class="col-md-6 text-end">
                    <button type="button" class="btn btn-light btn-sm action-close"
                            data-bs-dismiss="modal">Close
                    </button>
                </div>


            </div>

        </div>
    </div>
</div>

<div id="addContactModal" class="modal zoomIn" tabindex="-1" aria-labelledby="addOwnerModalLabel"
     aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <form id="addContactForm" class="modal-content needs-validation" novalidate>
            <div class="modal-header bg-soft-danger">
                <h5 class="modal-title" id="addContactModalLabel">Add Contact (Owner)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-6">
                        <input type="hidden" name="contact_type_id" value="3" id="contact_type_id">
                        <div class="row">
                            <?php if(auth()->user()->hasRole('Agent')): ?>
                                <input type="hidden" name="agent_id" id="contact_agent_id"
                                       value="<?php echo e(auth()->id()); ?>">
                            <?php else: ?>
                                <div class="col-4">Agent*</div>
                                <div class="col-8">
                                    <select name='agent_id' id="contact_agent_id" class="form-control select2 inputs"
                                            required>
                                        <option></option>
                                        <?php $__currentLoopData = $agents ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($agent->id); ?>"><?php echo e($agent->first_name); ?>

                                                <?php echo e($agent->last_name); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            <?php endif; ?>
                            <div class="col-4 mt-2">First Name*</div>
                            <div class="col-8 mt-2">
                                <input type="text" class="form-control inputs" name="first_name" id="first_name"
                                       placeholder="Enter first name" required>
                            </div>
                            <div class="col-4 mt-2">Last Name*</div>
                            <div class="col-8 mt-2">
                                <input type="text" class="form-control inputs" name="last_name" id="last_name"
                                       placeholder="Enter last name" required>
                            </div>
                            <div class="col-4 mt-2">Mobile*</div>
                            <div class="col-8 mt-2">
                                <input type="text" class="form-control inputs" name="mobile" id="mobile"
                                       placeholder="Enter mobile number" required>
                                <div class="text-danger mobileValidation">
                                </div>
                            </div>
                            <div class="col-4 mt-2">Whatsapp</div>
                            <div class="col-8 mt-2">
                                <input type="text" class="form-control inputs" name="whatsapp" id="whatsapp"
                                       placeholder="Enter whatsapp number">
                            </div>
                            <div class="col-4 mt-2">Address</div>
                            <div class="col-8 mt-2">
                                <input type="text" class="form-control inputs" name="address" id="address"
                                       placeholder="Enter address">
                            </div>
                            <div class="col-4 mt-2">Email</div>
                            <div class="col-8 mt-2">
                                <input type="email" class="form-control inputs" name="email" id="email"
                                       placeholder="Enter email address">
                                <span class="text-muted"><small>Kindly be aware that your listing will require a valid
                                        email address from the owner in order to be published.</small></span>

                            </div>
                            <div class="col-4 mt-2">Gender</div>
                            <div class="col-8 mt-2">
                                <select class="form-control select2 inputs" name="gender" id="gender">
                                    <option selected disabled>Select gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            <div class="col-4 mt-2">Date of birth</div>
                            <div class="col-8 mt-2">
                                <input type="date" class="form-control inputs" data-provider="flatpickr"
                                       data-date-format="Y-m-d" name="dob" id="dob"
                                       placeholder="Enter date of birth">
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="row">
                            <div class="col-4 mt-2">Nationality</div>
                            <div class="col-8 mt-2">
                                <select class="form-control select2 inputs" name="country_id" id="country_id">
                                    <option selected disabled>Select nationality</option>
                                    <?php $__currentLoopData = $countries ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($country->id); ?>"><?php echo e($country->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-4 mt-2">Passport</div>
                            <div class="col-8 mt-2">
                                <input type="text" class="form-control inputs" name="passport" id="passport"
                                       placeholder="Enter passport">
                            </div>
                            <div class="col-4 mt-2">Passport Expiry</div>
                            <div class="col-8 mt-2">
                                <input type="date" class="form-control inputs" data-provider="flatpickr"
                                       data-date-format="Y-m-d" name="passport_expiry" id="passport_expiry"
                                       placeholder="Enter password expiry">
                            </div>
                            <div class="col-4 mt-2">Phone</div>
                            <div class="col-8 mt-2">
                                <input type="text" class="form-control inputs" name="phone" id="phone"
                                       placeholder="Enter phone number">
                                <div class="text-danger phoneValidation">

                                </div>
                            </div>
                            <div class="col-4 mt-2">Company</div>
                            <div class="col-8 mt-2">
                                <input type="text" class="form-control inputs" name="company" id="company"
                                       placeholder="Enter company">
                            </div>
                            <div class="col-4 mt-2">Website</div>
                            <div class="col-8 mt-2">
                                <input type="text" class="form-control inputs" name="website" id="website"
                                       placeholder="Enter website">
                            </div>

                            <div class="col-4 mt-2">Contact Source*</div>
                            <div class="col-8 mt-2">
                                <select class="form-control select2 inputs" name="contact_source_id"
                                        id="contact_source_id" required>
                                    <option selected disabled>Select contact source</option>
                                    <?php $__currentLoopData = $contactSources ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $src): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($src->id); ?>"><?php echo e($src->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light action-close" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary action-save">Save</button>
            </div>
        </form>
    </div>
</div>

<div id="duplicateModal" class="modal zoomIn" tabindex="-1" aria-labelledby="duplicateModalLabel"
     aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-md">
        <form id="duplicateForm" class="modal-content needs-validation" novalidate>
            <div class="modal-header bg-soft-danger">
                <h5 class="modal-title" id="duplicateModalLabel">Duplicate Listing</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-4">Reason for duplication*</div>
                    <div class="col-8">
                        <select name='duplication_reason' id="duplication_reason" class="form-control select2"
                                required>
                            <option value=""></option>
                            <option value="Copy Content Only">Copy Content Only</option>
                            <option value="Relist (Older than 3 months)" id="hideOpt">Relist (Older than 3 months)
                            </option>
                            <?php if(!auth()->user()->hasRole('Agent')): ?>
                                <option value="Relist (Agent Request)">Relist (Agent Request)</option>
                                <option value="Offline in PF">Offline in PF</option>
                                <option value="Offline in Bayut / Dubizzle">Offline in Bayut / Dubizzle</option>
                            <?php endif; ?>

                        </select>
                    </div>
                </div>
                

                <div class="row mt-2">
                    <div class="col-4">Copy to*</div>
                    <div class="col-8">
                        <select name='for' id="duplicate_for" class="form-control select2" required>
                            <?php $__currentLoopData = \App\Models\Property::FOR_SELECT ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-4">Under Agent*</div>
                    <div class="col-8">
                        <select name='duplicate_agent_id' id="duplicate_agent_id" class="form-control select2"
                                required>
                            <option value=""></option>
                            <?php $__currentLoopData = $agents ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option data-image="<?php echo e($agent->thumbnail); ?>" data-status="<?php echo e($agent->status); ?>"
                                        value="<?php echo e($agent->id); ?>"
                                    <?php echo e($agent->id == auth()->user()->id ? 'selected' : ''); ?>>
                                    <?php echo e($agent->first_name); ?>

                                    <?php echo e($agent->last_name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-4"></div>
                    <div class="col-8">
                        <div class="form-check form-switch form-switch-success">
                            <input class="form-check-input" type="checkbox" name="copy_images" value="on"
                                   role="switch" id="duplicate-copy-image">
                            <label class="form-check-label" for="duplicate-copy-image">
                                Copy Images
                            </label>
                        </div>
                    </div>
                </div>

                
                <div class="row mt-4">
                    <small><b>Note:</b> Please note that when duplicating a listing, the Unit Number, Owner, Title Deed,
                        and Documents will not be copied or duplicated.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light action-close" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary action-save" id="copy_listing_btn">Save</button>
            </div>
        </form>
    </div>
</div>
<div id="refusePropertyModal" class="modal zoomIn" tabindex="-1" aria-labelledby="refusePropertyModal"
     aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-md">
        <form id="refusePropertyForm" class="modal-content needs-validation" novalidate>
            <input type="hidden" name="refuseRevisionChanges" id="refuseRevisionChanges">
            <div class="modal-header bg-soft-danger">
                <h5 class="modal-title" id="refusePropertyModalLabel">Refuse Property</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-4">Reason*</div>
                    <div class="col-8">
                        <textarea type="text" class="form-control" name="refuseReason" id="refuseReason"
                                  placeholder="Please write the reason to refuse" required maxlength="500"></textarea>
                        <span class="text-danger">*Max 500 Characters</span>

                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-4">Attachment</div>
                    <div class="col-8">
                        <input type="file" class="form-control" name="refuseAttachment" id="refuseAttachment">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light action-close" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary action-save" id="refusePropertyButton">

                    <span class="d-flex align-items-center">
                        <span class="spinner-border flex-shrink-0 custom-spinner-border d-none" role="status"
                              id="refuseLoading">
                            <span class="visually-hidden">Loading...</span>
                        </span>
                    </span>

                    <span id="refuseBtn"> Refuse</span>

                </button>
            </div>
        </form>
    </div>
</div>

<div id="photosModal" class="modal zoomIn" tabindex="-1" aria-labelledby="photosModalLabel" aria-hidden="true"
     style="display: none;">
    <div class="modal-dialog modal-lg">
        <div>
            <div class="modal-body">

                <div class="swiper navigation-swiper rounded">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <img src="<?php echo e(URL::asset('assets/images/small/img-4.jpg')); ?>" alt=""
                                 class="img-fluid"/>
                        </div>
                        <div class="swiper-slide">
                            <img src="<?php echo e(URL::asset('assets/images/small/img-5.jpg')); ?>" alt=""
                                 class="img-fluid"/>
                        </div>
                        <div class="swiper-slide">
                            <img src="<?php echo e(URL::asset('assets/images/small/img-6.jpg')); ?>" alt=""
                                 class="img-fluid"/>
                        </div>
                    </div>
                    <div class="swiper-button-next bg-white shadow"></div>
                    <div class="swiper-button-prev bg-white shadow"></div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="publishRequestModal" class="modal zoomIn" tabindex="-1" aria-labelledby="publishRequestModalLabel"
     aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <form id="publishRequestForm" class="modal-content needs-validation" novalidate>
            <input type="hidden" id="request_type" name="request_type">
            <div class="modal-header bg-soft-danger">
                <h5 class="modal-title" id="publishRequestModalLabel">Request </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mt-4">
                    <div class="col-2">Note / Reason *</div>
                    <div class="col-10">
                        <textarea name="request_note" id="request_note" class="form-control"
                                  placeholder="Enter request note" required></textarea>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="form-check" id="form-consent">
                            <input class="form-check-input" name="request_consent" type="checkbox"
                                   id="request_consent" required>
                            <label class="form-check-label" for="request_consent" id="request_consent_txt">
                                I hereby mention that all the details filled and images, Title Deed, Owner's Passport
                                copy and Signed Listing Form provided are 100% genuine and I hold the responsibility for
                                the correctness of above-mentioned particulars.
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light action-close" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary action-save">Send Request</button>
            </div>
        </form>
    </div>
</div>
<div id="assignAgentsModal" class="modal zoomIn" tabindex="-1" aria-labelledby="assignAgentsModalLabel"
     aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <form id="assignAgentsForm" class="modal-content needs-validation" novalidate>
            <div class="modal-header bg-soft-danger">
                <h5 class="modal-title" id="assignAgentsModalLabel">Assign Agents</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-4">Select Agent</div>
                    <div class="col-8">
                        <select name='agents[]' id="assign_agents" class="form-control select2" multiple>
                            <?php $__currentLoopData = $agents ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($agent->id); ?>"><?php echo e($agent->first_name); ?> <?php echo e($agent->last_name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light action-close" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary action-save">Save</button>
            </div>
        </form>
    </div>
</div>

<div id="mapModal" class="modal zoomIn" tabindex="-1" aria-labelledby="mapModalLabel" aria-hidden="true"
     style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-soft-danger">
                <h5 class="modal-title" id="mapModalLabel">Location</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-start align-items-center gap-2">
                            <h5><strong>Select columns</strong></h5>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="select-all-export">
                                <label class="form-check-label" for="select-all-export">
                                    Select All
                                </label>
                            </div>
                            <?php if(auth()->user()->hasRole(['Super Admin', 'Admin'])): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="archived">
                                    <label class="form-check-label">
                                        Include Archived ?
                                    </label>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>

                    <div class="col-12 mt-2">
                        <div class="row">
                            <?php $__currentLoopData = getPropertyExportColumns(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-4">
                                    <div class="form-check form-switch form-switch-success">
                                        <input class="form-check-input export-columns" type="checkbox"
                                               name="columns[]" value="<?php echo e($index); ?>" role="switch"
                                               id="export-column-<?php echo e($index); ?>">
                                        <label class="form-check-label" for="export-column-<?php echo e($index); ?>">
                                            <?php echo e($column); ?>

                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Properties Assign Agents')): ?>
    <div id="bulkReassignPropertiesModal" class="modal zoomIn" tabindex="-1"
         aria-labelledby="bulkReassignPropertiesModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-md">
            <form id="bulkReassignPropertiesForm" class="modal-content needs-validation" action="javascript:void(0)">
                <div class="modal-header bg-soft-danger">
                    <h5 class="modal-title" id="bulkReassignPropertiesModalLabel">Assign <span
                            id="bulk_properties_count"></span> Properties</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">

                        <div class="col-4">Select Agent*</div>
                        <div class="col-8">
                            <select name='bulk_properties_agent_id' id="bulk_properties_agent_id"
                                    class="form-control select2">
                                <option></option>
                                <?php $__currentLoopData = $agents ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option data-image="<?php echo e($agent->thumbnail); ?>" data-status="<?php echo e($agent->status); ?>"
                                            value="<?php echo e($agent->id); ?>">
                                        <?php echo e($agent->first_name); ?>

                                        <?php echo e($agent->last_name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>

                        </div>
                    </div>
                    <div class="bulk-properties-agent-validation d-none mt-5">

                        <div role="alert"
                             class="alert alert-danger alert-dismissible border-2 bg-body-secondary fade show mb-xl-0">
                            <ul>
                                <li>This agent is not eligible for bulk listing transfer. Please contact admin.</li>
                                <li> An agent must meet at least one of the following requirements:

                                    <ul>
                                        <li>Have a valid license number.</li>
                                        <li>Have their Public Profile enabled.</li>
                                        <li>Have at least one live listing.</li>
                                        <li>Have a completed profile on Portals.</li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light action-close" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary action-assign" disabled id="bulkPropertiesSubmitBtn">
                        Assign
                        Now
                    </button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>
<div class="modal zoomIn" id="pendingChangesModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog  modal-lg modal-dialog-scrollable" style="max-width: 1150px !important;">
        <div class="modal-content">
            <div class="modal-header bg-soft-danger">
                <h5 class="modal-title" id="uploadPhotosModalLabel">Take action on changes</h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="close-modal"></button>
            </div>
            <div class="modal-body text-left">
                <input type="hidden" id="pendingDataId">
                <!-- Center Alignment -->

                <div class="row" id="pendingDataRow">
                    <div class="col-6">
                        <div class="shadow p-3 bg-light rounded">
                            <h5>Old Data</h5>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="shadow p-3 bg-light rounded">
                            <h5>New Data</h5>
                        </div>

                    </div>
                    <div class="col-6">
                        <div class="row" id="pendingListOld">

                        </div>
                    </div>
                    <div class="col-6">
                        <div class="row" id="pendingListNew">

                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-4">
                        <div class="card d-none" id="pfApprovalDataContainer">
                            <div class="card-header p-05rem">
                                <h6 class="card-title mb-0">Property Finder</h6>
                            </div>
                            <div class="card-body">
                                <div id="pfApprovalData">

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="card d-none" id="bayutApprovalDataContainer">
                            <div class="card-header p-05rem">
                                <h6 class="card-title mb-0">Bayut</h6>
                            </div>
                            <div class="card-body">
                                <div id="bayutApprovalData">

                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="col-4">
                        <div class="card d-none" id="dubizzleApprovalDataContainer">
                            <div class="card-header p-05rem">
                                <h6 class="card-title mb-0">Dubizzle</h6>
                            </div>
                            <div class="card-body">
                                <div id="dubizzleApprovalData">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <?php if(auth()->user()->hasRole(['Super Admin', 'Admin'])): ?>
                    <button type="button" class="btn btn-success pcma action-approve">
                        Approve
                    </button>
                    <button type="button" class="btn btn-danger pcmr action-reject">
                        Reject
                    </button>
                <?php endif; ?>
                <button type="button" class="btn btn-light pcmc action-close">
                    Close
                </button>

            </div>
        </div>
    </div>
</div>


<div class="modal fade flip" id="archiveModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog  modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-soft-danger">
                <h5 class="modal-title" id="archiveModalLabel">Archive Note</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="close-modal"></button>
            </div>
            <div class="modal-body p-5 text-center">
                <div class="row">
                    <div class="col-3">Add your Archive Note*:</div>
                    <div class="col-9">
                        <input type="hidden" id="archive_lead_id">
                        <textarea name="" rows="3" class="form-control" placeholder="Add your Archive Note"
                                  id="archive_note"
                                  required></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success action-save" id="saveArchive">
                    Update
                </button>
                <button type="button" class="btn btn-light action-close" data-bs-dismiss="modal">
                    Close
                </button>

            </div>
        </div>
    </div>
</div>

<div id="check" class="modal fade" tabindex="-1" aria-labelledby="publishRequestModalLabel" aria-hidden="true"
     style="display: none;">
    <div class="modal-dialog modal-lg">
        <form id="publishRequestForm" class="modal-content needs-validation" novalidate>
            <input type="hidden" id="request_type" name="request_type">
            <div class="modal-header bg-soft-danger">
                <h5 class="modal-title" id="publishRequestModalLabel">Request </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mt-4">
                    <div class="col-2">Note / Reason *</div>
                    <div class="col-10">
                        <textarea name="request_note" id="request_note" class="form-control"
                                  placeholder="Enter request note" required></textarea>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" name="request_consent" type="checkbox"
                                   id="request_consent" required>
                            <label class="form-check-label" for="request_consent" id="request_consent_txt">
                                I hereby mention that all the details filled and images, Title Deed, Owner's Passport
                                copy and Signed Listing Form provided are 100% genuine and I hold the responsibility for
                                the correctness of above-mentioned particulars.
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light action-close" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary action-save">Send Request</button>
            </div>
        </form>
    </div>
</div>
<?php if(auth()->user()->can('Listing Duplicate Check')): ?>
    <div id="checkDuplicateListingModal" class="modal fade" tabindex="-1"
         aria-labelledby="checkDuplicateListingModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">

                <div class="modal-header bg-soft-danger">
                    <h5 class="modal-title" id="publishRequestModalLabel">This listing may be duplicate of these
                        listings </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="table-responsive table-card">

                        <table class="table table-nowrap table-striped-columns mb-0">
                            <thead class="table-light">
                            <tr style="position:sticky">
                                <th>Id</th>
                                <th>Title</th>
                                <th>Unit No.</th>
                                <th>Dates</th>
                                <th>Agent</th>
                                <th>Status</th>
                                <th>Match Score</th>
                            </tr>

                            </thead>

                            <tbody id="duplicatePropertyList">

                            </tbody>
                        </table><!-- end table -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light action-close" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>




<div class="modal zoomIn" id="deleteModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog  modal-md modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-soft-danger">
                <h5 class="modal-title" id="archiveModalLabel">Deletion Confirm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="close-modal"></button>
            </div>
            <div class="modal-body px-5 py-3">
                <div class="row">
                    <h3>Are you sure? </h3>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success action-archive" id="saveArchive">
                    Delete Listing
                </button>
                <button type="button" class="btn btn-light  action-close" data-bs-dismiss="modal">
                    Close
                </button>

            </div>
        </div>
    </div>
</div>


<!-- staticBackdrop Modal -->
<div class="modal fade" id="loaderModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <lord-icon src="https://cdn.lordicon.com/ymrqtsej.json" trigger="hover" colors="primary:#121331"
                           style="width:250px;height:250px">
                </lord-icon>

                <div class="mt-4">
                    <h4 class="mb-3">You've made it!</h4>
                    <p class="text-muted mb-4"> The transfer was not successfully received by us. the email of the
                        recipient wasn't correct.</p>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal zoomIn" id="generatePosts" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog  modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-soft-danger">
                <h5 class="modal-title" id="archiveModalLabel">Post Specification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="close-modal"></button>
            </div>
            <div class="modal-body p-5 ">
                <div class="row">
                    <!-- Basic Input -->

                    <div class="col-md-3">
                        <div>
                            <label for="basiInput" class="form-label">First Text</label>
                            <input type="text" class="form-control" placeholder="For" id="__first_text">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div>
                            <label for="basiInput" class="form-label">Second Text</label>
                            <input type="text" class="form-control" placeholder="Sale" id="__second_text">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div>
                            <label for="basiInput" class="form-label">Show Profile</label>
                            <select name="profile" id="__show_profile" class="form-control">
                                <option value="" disabled>Select Option</option>
                                <option value="yes" selected>Yes</option>
                                <option value="no">No</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div>
                            <label for="basiInput" class="form-label">Show Price</label>
                            <select name="price" id="__show_price" class="form-control">
                                <option value="" disabled>Select Option</option>
                                <option value="yes" selected>Yes</option>
                                <option value="no">No</option>
                            </select>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success action-save" id="gens">
                    Generate
                </button>
                <button type="button" class="btn btn-light action-close" data-bs-dismiss="modal">
                    Close
                </button>

            </div>
        </div>
    </div>
</div>


<div class="modal zoomIn" id="generateBrochureModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog  modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header bg-soft-danger">
                <h5 class="modal-title" id="archiveModalLabel">Brochure Specification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="close-modal"></button>
            </div>
            <div class="modal-body p-5 ">
                <div class="row">


                    <div class="col-md-5">
                        <div class="">
                            <div class="row mb-2" id="featureFullDiv">
                                <div>
                                    <label for="title" class="form-label"> Amenities: <span
                                            class="text-danger">(<span id="countAmenities">0/9</span>)</span></label>
                                    <div class="scrollable-features">
                                        <div class="single-feature">
                                            <div class="form-check amenities-form-check appendFeatures">
                                                <input class="btn-check form-check-input inputs" name="features[]"
                                                       type="checkbox" value="" id="pdf_feature-ID"
                                                       autocomplete="off">
                                                <label class="btn btn-amenities-primary btn-custom btn-primary"
                                                       for="feature-ID">
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                            </div>

                            <div class="row mt-2" id="">
                                <div>
                                    <label for="title" class="form-label"> Description <span
                                            class="text-danger">(Max Characters: <span class="text-danger">700</span>)
                                        </span></label>
                                    <textarea class="form-control regenerateIframe" id="PDF_description"
                                              placeholder="Enter description"
                                              name="description" rows="10" maxlength="700"></textarea>
                                    <div class="mt-2">

                                        <div class="d-flex ">
                                            <div class="flex-grow-0">
                                                Characters Count: <span id="descriptionCount"></span>
                                            </div>
                                            <div class="flex-grow-1 text-end">
                                                <button class="btn btn-sm btn-primary" type="button"
                                                        id="trimDescription">Trim to 700 Characters
                                                </button>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="invalid-feedback">

                                    </div>

                                </div>
                            </div>

                            <div class="row mt-2" id="">
                                <div class="col-md-4">
                                    <div class="form-check form-switch form-check-inline" dir="ltr">
                                        <label for="title" class="form-label">Show Price*</label>
                                        <input type="checkbox" class="form-check-input regenerateIframe"
                                               id="show_price_iframe" value="1" checked>
                                    </div>

                                    
                                </div>
                                <div class="col-md-8">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input regenerateIframe" type="radio" checked
                                               value="agent" name="profile_user_iframe" id="WithoutinlineRadio1">
                                        <label for="">Property Agent</label>
                                    </div>

                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input regenerateIframe" type="radio"
                                               value="me" name="profile_user_iframe" id="WithoutinlineRadio2">
                                        <label for=""><?php echo e(auth()->user()->full_name); ?> (Myself)</label>
                                    </div>
                                </div>

                            </div>

                            
                            


                            

                            
                            
                            

                            <div class="row mt-2" id="">
                                <div class="col-md-12">
                                    <label for="title" class="form-label">Images</label>
                                </div>
                                <div class="col-lg-4 col-md-4 mt-2">
                                    <select name="image_1" id="image_1_iframe" class="form-control regenerateIframe">
                                    </select>
                                </div>
                                <div class="col-lg-4 col-md-4 mt-2">
                                    <select name="image_1" id="image_2_iframe" class="form-control regenerateIframe">
                                    </select>
                                </div>
                                <div class="col-lg-4 col-md-4 mt-2">
                                    <select name="image_1" id="image_3_iframe" class="form-control regenerateIframe">
                                    </select>
                                </div>
                            </div>


                        </div>
                    </div>

                    <div class="col-md-7">
                        <div class="container">
                            <div class="row">
                                <div class="col-12">
                                    <div class="">
                                        <div class="">
                                            <div class="iframecontainer">
                                                <iframe id="pdfIframe" src="" frameborder="0" class="iframe"
                                                        style="overflow:hidden;overflow-x:hidden;overflow-y:hidden;height:100%;width:100%;position:absolute;top:0%;left:0px;right:0px;bottom:0px"
                                                        height="100%" width="100%"></iframe>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success action-save" id="genPDF">
                    Generate
                </button>
                <button type="button" class="btn btn-light action-close" data-bs-dismiss="modal">
                    Close
                </button>

            </div>
        </div>
    </div>
</div>


<div class="modal zoomIn" id="generateNewBrochureModal" tabindex="-1" aria-hidden="true"
     data-bs-backdrop="static">
    <div class="modal-dialog  modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header bg-soft-danger">
                <h5 class="modal-title" id="archiveModalLabel">Brochure Specification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="close-modal"></button>
            </div>
            <div class="modal-body p-5 ">
                <div class="row">


                    <div class="col-md-5">
                        <div class="">
                            <div class="row mb-2" id="featureFullDiv">
                                <div>
                                    <label for="title" class="form-label"> Amenities: <span
                                            class="text-danger">(<span
                                                id="countNewAmenities">0/9</span>)</span></label>
                                    <div class="scrollable-features-two">
                                        <div class="single-feature">
                                            <div class="form-check amenities-form-check appendNewFeatures">
                                                <input class="btn-check form-check-input inputs" name="features[]"
                                                       type="checkbox" value="" id="pdf_feature-ID"
                                                       autocomplete="off">
                                                <label class="btn btn-amenities-primary btn-custom btn-primary"
                                                       for="feature-ID">
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                            </div>


                            <div class="row mt-2" id="">
                                <div class="col-md-4">
                                    <div class="form-check form-switch form-check-inline" dir="ltr">
                                        <label for="title" class="form-label">Show Price*</label>
                                        <input type="checkbox" class="form-check-input regenerateNewIframe"
                                               id="show_new_price_iframe" value="1" checked>
                                    </div>

                                    
                                </div>
                                <div class="col-md-8">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input regenerateNewIframe" type="radio" checked
                                               value="agent" name="profile_new_user_iframe"
                                               id="WithoutinlineRadio1">
                                        <label for="">Property Agent</label>
                                    </div>

                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input regenerateNewIframe" type="radio"
                                               value="me" name="profile_new_user_iframe"
                                               id="WithoutinlineRadio2">
                                        <label for=""><?php echo e(auth()->user()->full_name); ?> (Myself)</label>
                                    </div>
                                </div>

                            </div>

                            
                            


                            

                            
                            
                            

                            <div class="row mt-2" id="">
                                <div class="col-md-12">
                                    <label for="title" class="form-label">Images</label>
                                </div>
                                <div class="col-lg-4 col-md-4 mt-2">
                                    <select name="image_1" id="image_5_iframe"
                                            class="form-control regenerateNewIframe">
                                    </select>
                                </div>
                                <div class="col-lg-4 col-md-4 mt-2">
                                    <select name="image_2" id="image_6_iframe"
                                            class="form-control regenerateNewIframe">
                                    </select>
                                </div>
                                <div class="col-lg-4 col-md-4 mt-2">
                                    <select name="image_3" id="image_7_iframe"
                                            class="form-control regenerateNewIframe">
                                    </select>
                                </div>
                                <div class="col-lg-4 col-md-4 mt-2">
                                    <select name="image_4" id="image_8_iframe"
                                            class="form-control regenerateNewIframe">
                                    </select>
                                </div>
                            </div>


                        </div>
                    </div>

                    <div class="col-md-7">
                        <div class="container">
                            <div class="row">
                                <div class="col-12">
                                    <div class="">
                                        <div class="">
                                            <div class="iframecontainer">
                                                <iframe id="pdfNewIframe" src="" frameborder="0"
                                                        class="iframe"
                                                        style="overflow:hidden;overflow-x:hidden;overflow-y:hidden;height:100%;width:100%;position:absolute;top:0%;left:0px;right:0px;bottom:0px"
                                                        height="100%" width="100%"></iframe>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success action-save" id="genNewPDF">
                    Generate
                </button>
                <button type="button" class="btn btn-light action-close" data-bs-dismiss="modal">
                    Close
                </button>

            </div>
        </div>
    </div>
</div>

<div class="modal zoomIn" id="generatePost" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog  modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header bg-soft-danger">
                <h5 class="modal-title" id="archiveModalLabel">Post Specification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="close-modal"></button>
            </div>
            <div class="modal-body p-5 ">
                <div class="row">


                    <div class="col-md-5">
                        <div class="">

                            <div class="row mt-2" id="">
                                <div>
                                    <label for="basiInput" class="form-label">Title</label>
                                    <select name="post_title" id="post_title" class="form-control regeneratePost">
                                        <option value="For Sale" data-pretext="For" data-posttext="Sale" selected>
                                            For
                                            Sale
                                        </option>
                                        <option value="For Rent" data-pretext="For" data-posttext="Rent">For Rent
                                        </option>
                                        <option value="Just Sold" data-pretext="Just" data-posttext="Sold">Just
                                            Sold
                                        </option>
                                        <option value="Just Rented" data-pretext="Just" data-posttext="Rented">
                                            Just
                                            Rented
                                        </option>
                                    </select>
                                </div>
                            </div>


                            <div class="row mt-2" id="">
                                <div class="col-md-6">
                                    <label for="basiInput" class="form-label">Show Profile</label>
                                    <select name="show_profile_post" id="show_profile_post"
                                            class="form-control regeneratePost">
                                        <option value="" disabled>Select Option</option>
                                        <option value="yes">Yes</option>
                                        <option value="no" selected>No</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <div>
                                        <label for="title" class="form-label">Show Price*</label>
                                        <select name="show_price" id="show_price_post"
                                                class="form-control regeneratePost">
                                            <option value="true">Yes</option>
                                            <option value="false" selected>No</option>
                                        </select>
                                    </div>
                                </div>

                            </div>
                            <div class="row mt-2" id="">
                                <div>
                                    <label for="title" class="form-label">Location</label>
                                    <input type="text" class="form-control regeneratePost" id="location_info"
                                           name="location_info">
                                </div>
                            </div>

                            <div class="row mt-2" id="">
                                <div>
                                    <label for="title" class="form-label">Featured Image</label>
                                    <div class="col-lg-12  col-md-10 mt-2">
                                        <select name="image_4" id="image_4_iframe"
                                                class="form-control regeneratePost">
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-7">

                        <div class="container">
                            <div class="row">
                                <div class="col-12">
                                    <div class="">
                                        <div class="containerbox">
                                            <div class="">
                                                <iframe id="postIframe" src="" frameborder="0"
                                                        class="responsive-iframe" 
                                                    ></iframe>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success action-save" id="gen">
                    Generate
                </button>
                <button type="button" class="btn btn-light action-close" data-bs-dismiss="modal">
                    Close
                </button>

            </div>
        </div>
    </div>
</div>

<div class="modal zoomIn" id="downloadRequestBulkDocumentsModal" tabindex="-1" aria-hidden="true"
     data-bs-backdrop="static">
    <div class="modal-dialog">
        <form class="modal-content" id="downloadRequestBulkDocumentsForm">
            <div class="modal-header bg-soft-danger">
                <h5 class="modal-title" id="downloadRequestBulkDocumentsModalLabel">Request download</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="close-modal"></button>
            </div>
            <div class="modal-body p-5 ">
                <div class="row">
                    <div class="col-md-12">

                        <div class="">
                            <div class="row mt-2" id="download-request-zip-inputs"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success action-save">
                    Request
                </button>
                <button type="button" class="btn btn-light action-close" data-bs-dismiss="modal">
                    Close
                </button>
            </div>
        </form>
    </div>
</div>


<div class="modal zoomIn" id="saveAndSearchModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-soft-danger">
                <h5 class="modal-title" id="saveAndSearchModalLabel">Name Your Search</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="close-modal"></button>
            </div>
            <div class="modal-body p-5 ">
                <div class="row">
                    <div class="col-md-12">
                        <label for="" class="form-label">Name</label>
                        <input type="text" class="form-control" id="search_name_input"
                               name="search_name_input" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success action-save" class="button" id="saveAndSearchBtn">
                    Save and Search
                </button>
                <button type="button" class="btn btn-light action-close" id="saveAndSearchCloseBtn"
                        data-bs-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>


<div class="modal zoomIn" id="unpublishedRequest" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog  modal-md modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-soft-danger">
                <h5 class="modal-title" id="unpublishedRequestLabel">Reason for Unpublishing Listing</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="close-modal"></button>
            </div>
            <div class="modal-body px-5   py-3">
                <div class="row">
                    <div class="col-3">Reason*:</div>
                    <div class="col-9">
                        <select name="" id="preFillOption" class="form-control">
                            <option value=""></option>
                            <option value="Expired Contracts/Rental Forms">Expired Contracts/Rental Forms</option>
                            <option value="Flagged Listings (Under Review)">Flagged Listings (Under Review)</option>
                            <option value="Agent Request">Agent Request</option>
                            <option value="Other">Other</option>
                        </select>

                        <textarea name="" rows="3" class="form-control d-none mt-3"
                                  placeholder="Add Reason for Unpublish"
                                  id="unpublish_note" required></textarea>

                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-4">Action</div>
                    <div class="col-lg-8">
                        <div class="mt-4 mt-lg-0">
                            <div class="form-check form-switch form-check-inline" dir="ltr">
                                <input type="checkbox" class="form-check-input clear_permit_number"
                                       id="clear_permit_number" name="clear_permit_number">
                                <label class="form-check-label" for="clear_permit_number">Clear Permit
                                    Number</label>
                            </div>

                            <br>


                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success action-archive" id="saveUnpublished">
                    Unpublish Listing
                </button>
                <button type="button" class="btn btn-light  action-close" data-bs-dismiss="modal">
                    Close
                </button>

            </div>
        </div>
    </div>
</div>


<div class="modal zoomIn" id="chooseDocument" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog  modal-md modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-soft-danger">
                <h5 class="modal-title" id="archiveModalLabel">Choose Document<span id="whichPassword"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="close-modal"></button>
            </div>
            <div class="modal-body px-5 py-3">

                <input type="hidden" id="document_to_be_restored" value="">
                <span>How you want to upload this <strong><span id="new_title"></span></strong>?</span>
                <div class="row" id="">
                    <div class="col-lg-6 mt-2">
                        <label for="document_type_name" class="form-label">Upload as</label>
                    </div>
                    <div class="col-lg-6 mt-2">
                        <select name="document_type_name" id="document_type_name" class="form-control select2">
                            <option value="Title Deed">Title Deed</option>
                            <option value="Owner Passport Copy">Owner's Passport/EID</option>
                            <option value="Signed Listing Form">Contract A/Leasing Form/NOC*</option>
                        </select>

                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success action-save" id="saveNewDocument">
                    Save This Document
                </button>
                <button type="button" class="btn btn-light  action-close" data-bs-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>


<div class="modal zoomIn" id="republishRequestModal" tabindex="-1" aria-hidden="true"
     data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form id="republishRequestForm" class="modal-content needs-validation" novalidate>

            <div class="modal-content">
                <div class="modal-header bg-soft-danger">
                    <h5 class="modal-title" id="archiveModalLabel">Upload New Contract<span
                            id="whichPassword"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            id="close-modal"></button>
                </div>
                <div class="modal-body px-5 py-3">

                    <div class="row">
                        <h4>
                            <span id="republishRequestModalLabel">Portal Details</span>
                        </h4>
                    </div>

                    <div class="text-center d-none" id="pre-data-spinner">
                        <div class="spinner-border"></div>
                    </div>

                    <div class="row" id="portal-data-div-agent">
                    </div>

                    <div class="row" id="">
                        <div class="col-lg-12 mt-2">
                            <label for="contract_file" class="form-label required">Contract<span
                                    class="text-danger">*</span></label>
                            <input type="file" name="contract_file" id="contract_file" class="form-control"
                                   required>
                        </div>
                        <div class="col-lg-12 mt-2">
                            <label for="contract_file_password" class="form-label">Contract File Password</label>
                            <input type="text" name="contract_file_password" id="contract_file_password"
                                   class="form-control" placeholder="Enter contract file password">
                        </div>

                        <div class="col-lg-6 mt-2">
                            <label for="issue_date" class="form-label required">Contract Start<span
                                    class="text-danger">*</span></label>
                            <input type="date" name="issue_date" id="contract_issue_date"
                                   class="form-control" required>
                        </div>
                        <div class="col-lg-6 mt-2">
                            <label for="expiry_date" class="form-label required">Contract End<span
                                    class="text-danger">*</span></label>
                            <input type="date" name="expiry_date" id="contract_expiry_date"
                                   class="form-control" required>
                        </div>
                        <div class="col-lg-12">
                            <span class="text-danger" id="date-validations"></span>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success action-save" id="saveRepublishRequest">
                        Request Republish
                    </button>
                    <button class="btn btn-outline-primary btn-load d-none" id="saveRepublishRequest-placeholder">
                        <span class="d-flex align-items-center">
                            <span class="flex-grow-1 me-2">
                                Loading...
                            </span>
                            <span class="spinner-border flex-shrink-0" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </span>
                        </span>
                    </button>
                    <button type="button" class="btn btn-light  action-close" data-bs-dismiss="modal">
                        Close
                    </button>
                </div>
            </div>
        </form>
    </div>

</div>


<div class="modal zoomIn" id="republishRequestModalApproval" tabindex="-1" aria-hidden="true"
     data-bs-backdrop="static">
    <div class="modal-dialog  modal-lg modal-dialog-scrollable">


        <div class="modal-content" id="appendRequest">

        </div>

    </div>

</div>

<div class="modal zoomIn" id="duplicated-property-check" tabindex="-1" aria-hidden="true"
     data-bs-backdrop="static">
    <div class="modal-dialog">


        <div class="modal-content p-4" id="dasdds">
            <form action="javascript:void(0);">
                <div class="row g-3">
                    <div class="col-12">
                        <h3>
                            Property Insights
                        </h3>
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_duplicated" name="is_duplicated">
                            <label class="form-check-label" for="gridCheck">
                                Listing is Duplicated, The reference number will be generated as axc-12333-r1
                            </label>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <table class="table">

                            <tbody>
                            <tr>
                                <td><b>Duplicate Status</b></td>
                                <td><span class="is_duplicated"></span></td>
                            </tr>
                            <tr>
                                <td><b>Duplicate of</b></td>
                                <td><span class="duplicated_from"></span></td>
                            </tr>
                            <tr>
                                <td><b>Reason</b></td>
                                <td><span class="duplicate_reason"></span></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <!--end col-->


                    <div class="col-lg-12">
                        <div class="hstack gap-2 justify-content-end">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                            <button type="button" id="publish-request-btn" class="btn btn-primary">Submit</button>
                        </div>
                    </div><!--end col-->
                </div><!--end row-->
            </form>
        </div>

    </div>

</div>

<div class="modal zoomIn" id="pf_verification_modal" tabindex="-1" aria-hidden="true"
     data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">


        <div class="modal-content p-4" id="dasdds">
            <form action="javascript:void(0);" id="pf_verification_form" enctype="multipart/form-data">
                <div class="row g-3">
                    <div class="col-12">
                        <h3>
                            Create Verification Request For Property Finder
                        </h3>
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input type="hidden" name="id" id="pf_verification_property_id" value="">
                            <label class="form-check-label" for="gridCheck">
                                You are about to create a Listing Verification request for Property Finder. We will send
                                the documents attached to this listing to Property Finder for verification. Please make
                                sure that the documents are correct and complete. If you want to send additional
                                documents you can attach documents under documents section in media section.
                            </label>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <table class="table">

                            <tbody id="pfVerificationDocTable">

                            </tbody>
                        </table>
                    </div>
                    <div class="col-lg-12">
                        <button type="button" class="btn btn-primary" id="pfVerificationAddMore">Add More</button>
                    </div>
                    <!--end col-->


                    <div class="col-lg-12">
                        <div class="hstack gap-2 justify-content-end">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div><!--end col-->
                </div><!--end row-->
            </form>
        </div>

    </div>

</div>
<div class="modal zoomIn" id="pfUpgradesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content p-4">
            <div class="modal-header">
                <h5 class="modal-title" id="pfUpgradesModalLabel">Property Finder Upgrades</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="close-modal"></button>
            </div>
            <div class="modal-body">
                <div id="pfUpgradeModalLoader" class="text-center">
                    <!-- Growing spinner -->
                    <div class="spinner-grow text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="spinner-grow text-secondary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="spinner-grow text-success" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="spinner-grow text-info" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="spinner-grow text-warning" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="spinner-grow text-danger" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="spinner-grow text-dark" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="spinner-grow text-light" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <div id="pfAvailableUpgrades">

                </div>
            </div>

        </div>

    </div>

</div>

<div class="modal zoomIn" id="pfUpgradeConfirmModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white">Confirm Upgrade Purchase</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <i class="ri-question-line display-5 text-warning"></i>
                </div>
                <div class="text-center">
                    <h4 class="mb-3">Are you sure you want to purchase this upgrade?</h4>
                    <div class="upgrade-details">
                        <div class="card border-light bg-light">
                            <div class="card-body">
                                <h6 class="card-title text-primary" id="confirmFeatureName"></h6>
                                <p class="card-text mb-2"><strong>Product:</strong> <span id="confirmProductName"></span></p>
                                <p class="card-text mb-2"><strong>Price:</strong> <span id="confirmPrice"></span> credits</p>
                                <p class="card-text mb-0"><strong>Auto Renewal:</strong> <span id="confirmAutoRenewal" class="badge bg-secondary"></span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmUpgradeBtn">
                    <i class="ri-shopping-cart-line me-1"></i>Confirm Purchase
                </button>
            </div>

        </div>
    </div>
</div>


<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/listing/includes/modals.blade.php ENDPATH**/ ?>