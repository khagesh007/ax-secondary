<div class="collapse slideIn" id="collapseFilterForm">
    <div class="row">
        <form id="searchForm" class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-3 col-md-6 col-sm-12">
                            <div class="row">
                                <div class="col-4">Requested By</div>
                                <div class="col-8">
                                    <select name='search_requested_by' id="search_requested_by" class="form-control select2 search-inputs" multiple>
                                        <?php $__currentLoopData = $agents ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option
                                                value="<?php echo e($agent->id); ?>"><?php echo e($agent->first_name); ?> <?php echo e($agent->last_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-4">Types</div>
                                <div class="col-8">
                                    <select name='search_type' id="search_type"
                                            class="form-control select2 search-inputs">
                                        <option value="" selected disabled>Select Type</option>
                                        <option value="Standard">Standard Only</option>
                                        <option value="Luxury">Luxury</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-lg-4">
                                    <label for="search_assigned_to" class="form-label"> Assigned To</label>
                                </div>
                                <div class="col-lg-8">
                                    <select name='search_assigned_to' id="search_assigned_to"
                                            class="form-control select2 search-inputs" multiple>
                                        <?php $__currentLoopData = $photographers ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($pg->id); ?>" ><?php echo e($pg->full_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 col-sm-12">

                            <div class="row">
                                <div class="col-lg-4">
                                    <label for="search_approved_by" class="form-label"> Approved By</label>
                                </div>
                                <div class="col-lg-8">
                                    <select name='search_approved_by' id="search_approved_by"
                                            class="form-control select2 search-inputs" multiple>
                                        <?php $__currentLoopData = $admins ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $admin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option
                                                value="<?php echo e($admin->id); ?>"><?php echo e($admin->first_name); ?> <?php echo e($admin->last_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-lg-4">
                                    <label for="search_rejected_by" class="form-label"> Rejected By</label>
                                </div>
                                <div class="col-lg-8">
                                    <select name='search_rejected_by' id="search_rejected_by"
                                            class="form-control select2 search-inputs" multiple>
                                        <?php $__currentLoopData = $admins ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $admin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option
                                                value="<?php echo e($admin->id); ?>"><?php echo e($admin->first_name); ?> <?php echo e($admin->last_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-lg-4">
                                    <label for="search_statuses" class="form-label"> Statuses</label>
                                </div>
                                <div class="col-lg-8">
                                    <select name='search_statuses' id="search_statuses"
                                            class="form-control select2 search-inputs" multiple>
                                        <?php $__currentLoopData = \App\Models\PropertyShootBooking::STATUS_SELECT ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option
                                                value="<?php echo e($status); ?>"><?php echo e($status); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 col-sm-12">

                            <div class="row mt-2">
                                <div class="col-lg-4">
                                    <label for="search_created_from" class="form-label">Created From</label>
                                </div>
                                <div class="col-lg-8">
                                    <input type="date" class="form-control search-inputs" data-provider="flatpickr"
                                           data-date-format="Y-m-d" id="search_created_from" name="search_created_from" placeholder="Select created from date">
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-lg-4">
                                    <label for="search_created_to" class="form-label">Created To</label>
                                </div>
                                <div class="col-lg-8">
                                    <input type="date" class="form-control search-inputs" data-provider="flatpickr"
                                           data-date-format="Y-m-d" id="search_created_to" name="search_created_to" placeholder="Select created to date">
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 col-sm-12">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-check form-switch form-switch-success">
                                        <input class="form-check-input search-inputs" type="checkbox" name="search_deleted" role="switch" id="search_deleted">
                                        <label class="form-check-label"
                                               for="search_deleted">Show Deleted</label>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 d-flex justify-content-start align-items-center gap-2">
                                <button type="submit" class="btn btn-sm btn-primary action-serach" id="searchSearchBtn">
                                    <i class="ri-search-line align-bottom me-1"></i> Search
                                </button>
                                <a href="javascript:void(0)" class="btn btn-sm text-warning action-close" id="clearSearchBtn">Clear</a>
                                <a href="javascript:void(0)" type="button" class="btn btn-sm text-danger action-delete" onclick="toggleAdvanceSearch();">Close</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/jobs/shoot/filterForm.blade.php ENDPATH**/ ?>