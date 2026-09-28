<div class="modal zoomIn" id="archiveModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog  modal-md modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-soft-danger">
                <h5 class="modal-title" id="archiveModalLabel">Archive Reason</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                    id="close-modal"></button>
            </div>
            <div class="modal-body p-5">
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
                <button type="button" class="btn btn-light  action-close" data-bs-dismiss="modal" id="closeArchiveModal">
                    Close
                </button>

            </div>
        </div>
    </div>
</div>





<div class="modal zoomIn" id="addModal" aria-labelledby="addModalLabel" data-bs-backdrop="static"
    data-bs-keyboard="false" tabindex="-1" aria-modal="true">
    <div class="modal-dialog modal-lg  modal-dialog-scrollable ">
        <div class="modal-content">
            <div class="modal-header bg-soft-danger card-header pb-2">
                <h5 class="modal-title" id="addModalLabel"><span id="modal_title">Add </span>
                    <span id="idSpan">< ID: <span id="leadID"></span> ></span> </h5>
                <button type="button" class="btn-close CloseAddModal" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="mainForm" class="needs-validation" novalidate>
                    <div class="card mb-2">
                        <div class="card-body">
                            <div class="d-flex mb-3">
                                <div class="flex-shrink-0 avatar-sm">
                                    <div class="avatar-title bg-light rounded" id="view-call-type">
                                        <div class="avatar-xs" id="call_type_icon">
                                            <span
                                                class="avatar-title bg-soft-success text-success rounded-circle fs-16">
                                                <i class="mdi mdi-phone-incoming"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="fs-15 mb-1"> <span id="near_number_first_name"></span> <strong><span id="edit-client_number" style="font-size: 20px!important"></span></strong></h5>
                                    <div class="row">
                                        
                                        <div class="col-md-10"> <span id="call_rec"></span></span></div>

                                    </div>


                                </div>
                                <div>
                                    <span id="agent_detail"></span>
                                    <div class="row">

                                        <div class="col-6" id="viewContact_id">
                                            <div id="view_agent" class="mt-2">
                                                <a href="javascript:void(0);" class="badge badge-soft-info viewContact"
                                                    data-id="">View Contact <i class="ri-arrow-right-up-line align-bottom"></i></a>
                                            </div>
                                        </div>
                                        <div class="col-6" id="viewAgent_id">
                                            <div id="view_agent" class="mt-2">
                                                <a href="javascript:void(0);" class="badge badge-soft-primary viewAgent"
                                                    data-id="">View Agent <i class="ri-arrow-right-up-line align-bottom"></i></a>
                                            </div>
                                        </div>
                                        <div class="col-6" id="viewLead_id">
                                            <div id="view_agent" class="mt-2">
                                                <a href="javascript:void(0);" class="badge badge-soft-success viewLead" data-id="">View Lead <i class="ri-arrow-right-up-line align-bottom"></i></a>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                                <br>

                            </div>
                            <h6 class="text-muted mb-0"><span class="badge badge-soft-success"
                                    id="call_log_info"></span></h6>
                        </div>

                    </div>




                    <div class="row g-3 pt-2 no_lead">
                        <input type="hidden" class="lead_id" name="lead_id">
                        <input type="hidden" id="agent_id" name="agent_id">
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-4">
                                    <label for="title" class="form-label">First Name*</label>
                                </div>
                                <div class="col-8">

                                    <input type="text" class="form-control" id="edit-first_name"
                                        name="first_name" placeholder="Enter First Name" required>
                                    <div class="invalid-feedback">
                                        Please enter the first_name.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-4">
                                    <label for="title" class="form-label">Last Name</label>
                                </div>
                                <div class="col-8">
                                    <input type="hidden" name="id" id="id" value="">
                                    <input type="hidden" name="call_log_id" id="call_log_id" value="">
                                    <input type="text" class="form-control" id="edit-last_name" name="last_name"
                                        placeholder="Enter Last Name">
                                    <div class="invalid-feedback">
                                        Please enter the last_name.
                                    </div>
                                </div>
                            </div>
                        </div>



                    </div>
                    <div class="row g-3 pt-2 no_lead">

                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-4">
                                    <label for="title" class="form-label">Lead Type*</label>
                                </div>
                                <div class="col-8">

                                    <select class="form-control select2 inputs" name="type_id" id="type_id"
                                        required>
                                        <option value="" selected disabled>Select Type</option>
                                        <?php $__currentLoopData = $types ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($type->id); ?>"><?php echo e($type->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <div class="invalid-feedback">
                                        Please enter the First name.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-4">
                                    <label for="title" class="form-label">Lead Source*</label>
                                </div>
                                <div class="col-8">
                                    <select class="form-control select2 inputs" name="source_id" id="source_id"
                                        required>
                                        <option value="" selected disabled>Select Source</option>
                                        <?php $__currentLoopData = $sources ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $src): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($src->id); ?>"><?php echo e($src->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <div class="invalid-feedback">
                                        Please enter the last_name.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3 pt-2 no_lead">

                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-4">
                                    <label for="title" class="form-label">Purpose*</label>
                                </div>
                                <div class="col-8">
                                    <select name='for[]' id="for" class="form-control select2 inputs"
                                        multiple required>
                                        <?php $__currentLoopData = \App\Models\Lead::FOR_SELECT ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php if(
                                                $key == 'Unspecified' and
                                                    auth()->user()->hasRole('Agent')): ?>
                                            <?php else: ?>
                                                <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                            <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <div class="invalid-feedback">
                                        Please enter the lead for.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-4">
                                    <label for="phone" class="form-label">Mobile</label>
                                </div>
                                <div class="col-8">
                                    <input type="tel" class="form-control inputs" name="mobile" id="mobile"
                                        required placeholder="Enter mobile">
                                        <div class="text-danger mobileValidation">

                                        </div>

                                    <div class="invalid-feedback">
                                        Please enter a valid mobile number.
                                    </div>
                                    <div class="text-danger" id="mobile-exists"></div>

                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-4">
                                    <label for="title" class="form-label">Lead Status*</label>
                                </div>
                                <div class="col-8">
                                    <select class="form-control select2 inputs" name="lead_status_id"
                                        id="lead_status_id" required>
                                        <option value="" selected disable>Select lead status</option>
                                        <?php $__currentLoopData = $statuses ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($status->id); ?>"><?php echo e($status->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <div class="invalid-feedback">
                                        Please enter the last_name.
                                    </div>
                                </div>
                            </div>
                        </div>
                        

                    </div>
                    <div class="row">
                        <div class="">
                            <div class="row">



                                <h5 class="mt-2">Lead Criteria</h5>
                                <div class="col-2 mt-2">Property</div>
                                <div class="col-10 mt-2">
                                    <select class="form-control select2 inputs" name="property_id"
                                        id="property_id">
                                        
                                    </select>
                                    <div class="invalid-feedback"> Please choose property. </div>
                                </div>



                                <div class="col-2 mt-2">Furnished</div>
                                <div class="col-4 mt-2">
                                    <select class="form-control select2 inputsCriteria inputs" id="furnished"
                                        name="furnished">
                                        <option value="" selected disabled>Select</option>
                                        <option value="FURNISHED">Furnished</option>
                                        <option value="SEMI_FURNISHED">Semi Furnished</option>
                                        <option value="UNFURNISHED">Unfurnished</option>
                                    </select>
                                </div>

                            </div>



                            <div class="" id="">


                                <div class="row  mt-2 align-items-center">
                                    <div class="col-2 mt-2">Community</div>
                                    <div class="col-4 mt-2">
                                        <select class="select2 inputsCriteria inputs" multiple id="communities"
                                            name="communities[]"></select>
                                    </div>
                                    <div class="col-2 mt-2">Location</div>
                                    <div class="col-4 mt-2">
                                        <input type="hidden" name="locations" id="locations">
                                        <select class="select2 inputsCriteria inputs" multiple
                                            id="sub_communities" name="sub_communities[]"></select>
                                    </div>

                                </div>

                                
                                <div class="row">



                                </div>
                                <div class="row">
                                    <div class="col-2 mt-2">Beds</div>
                                    <div class="col-2 mt-2">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" class="inputsCriteria" name="lead_id"
                                            id="criteria_lead_id">
                                        <input type="hidden" class="inputsCriteria" name="criteria_id"
                                            id="criteria_id">
                                        <select class="form-control inputsCriteria select2 inputs"
                                            name="min_bed_id" id="min_bed_id">
                                            <option value="" selected disabled>Select</option>
                                            <?php $__currentLoopData = $beds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bed): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($bed->id); ?>">
                                                    <?php echo e($bed->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <div class="invalid-feedback">
                                            Please choose an option.
                                        </div>
                                    </div>
                                    <div class="col-2 mt-2">
                                        <select class="form-control inputsCriteria select2 inputs"
                                            name="max_bed_id" id="max_bed_id">
                                            <option value="" selected disabled>Select</option>
                                            <?php $__currentLoopData = $beds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bed): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($bed->id); ?>">
                                                    <?php echo e($bed->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <div class="invalid-feedback">
                                            Please choose an option.
                                        </div>
                                    </div>
                                    <div class="col-2 mt-2">Baths</div>
                                    <div class="col-2 mt-2">
                                        <select class="form-control inputsCriteria select2 inputs"
                                            name="min_bath_id" id="min_bath_id">
                                            <option value="" selected disabled>Select</option>
                                            <?php $__currentLoopData = $baths; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bath): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($bath->id); ?>">
                                                    <?php echo e($bath->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <div class="invalid-feedback">
                                            Please choose an option.
                                        </div>
                                    </div>
                                    <div class="col-2 mt-2">
                                        <select class="form-control inputsCriteria select2 inputs"
                                            name="max_bath_id" id="max_bath_id">
                                            <option value="" selected disabled>Select</option>
                                            <?php $__currentLoopData = $baths; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bath): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($bath->id); ?>">
                                                    <?php echo e($bath->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <div class="invalid-feedback">
                                            Please choose an option.
                                        </div>
                                    </div>
                                </div>

                                <div class="row  mt-2 align-items-center">
                                    <div class="col-2 mt-2">Budget</div>

                                    <div class="col-2 mt-2">
                                        <input type="text" class="form-control inputsCriteria inputs"
                                            id="min_price" name="min_price" placeholder="Min Budget">
                                        
                                    </div>

                                    <div class="col-2 mt-2">
                                        <input type="text" class="form-control inputsCriteria inputs"
                                            id="max_price" name="max_price" placeholder="Max Budget">

                                        
                                    </div>

                                    <div class="col-2 mt-2">View</div>
                                    <div class="col-4 mt-2">
                                        <select class="form-control select2 inputs" name="view[]"
                                            id="view" multiple>
                                            <?php $__currentLoopData = \App\Models\Property::VIEWS ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                        </select>
                                        <div class="invalid-feedback">
                                            Please choose an option.
                                        </div>
                                    </div>


                                </div>
                                <div class="row mt-2">

                                    
                                </div>
                                
                            </div>
                        </div>
                    </div>



                    <div class="col-lg-12 mt-2">
                        <div class="hstack gap-2 justify-content-end">
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Leads Archive')): ?>
                                <button type="button" class="btn btn-light action-archive no_lead"
                                    id="archiveLeadBtn">Archive Lead</button>
                            <?php endif; ?>

                            
                            <button type="button" class="btn btn-light action-close CloseAddModal"  data-bs-dismiss="modal">Close</button>
                            <button id="submitBtn" type="submit" class="btn btn-primary action-save no_lead">Save Lead</button>
                        </div>
                    </div>
                </form>

                <div id="TablecontainerDiv" class="mt-3">
                    <hr>
                    <div class="card mb-2" >

                        <div class="card-body">
                            <div class="">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4> Other Leads </h4>
                                    </div>
                                </div>
                                <div id="Tablecontainer"></div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>

        </div>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/calls/include/modal.blade.php ENDPATH**/ ?>