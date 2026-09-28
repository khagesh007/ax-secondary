<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <h4>Properties by Area</h4>
                    </div>
                    <div class="col-md-4">
                        <div>
                            <select name="agent" class="select2" id="report_agent">
                                <option value="">Select Agent</option>
                                <?php $__currentLoopData = $agents ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option data-image="<?php echo e($agent->thumbnail); ?>"
                                            data-image="<?php echo e($agent->thumbnail); ?>"
                                            data-status="<?php echo e($agent->status); ?>"
                                            <?php if($selectedUserId == $agent->id): ?> selected <?php endif; ?>
                                            value="<?php echo e($agent->id); ?>"><?php echo e($agent->first_name); ?>

                                        <?php echo e($agent->last_name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="d-flex flex-nowrap justify-content-between align-items-center">
                            <div class="text-end">
                                <button class="btn btn-primary" id="apply-listings-by-area-filter-button">Apply Filter</button>
                            </div>
                            <div class="flex-shrink-0 ms-2">
                                <ul class="nav justify-content-end nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#table-one" role="tab">
                                            <i class=" bx bx-table"></i>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#graph-one" role="tab">
                                            <i class=" bx bx-bar-chart-square"></i>
                                        </a>
                                    </li>

                                </ul>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body" style="padding: 0">
                <div class="row" id="divLoader">
                    <div class="col-md-12 text-center">
                        <div class="spinner-grow text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>

                <div id="tableAppend"></div>
            </div>
        </div>
    </div>
</div>

<div class="row d-none" id="location-loading-div">
    <div class="col-12">
        <div class="card">
            <div class="card-body" style="padding: 0">
                <div class="row" id="">
                    <div class="col-md-12 text-center">
                        <div class="spinner-grow text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                    <div class="col-md-12 mt-2 text-center">
                        <h4 class="text-muted"> Loading insights of <span
                                id="location_community_name"></span></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row" id="tableAppendTwo">

</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/dashboard/v2/partials/_listings_by_area.blade.php ENDPATH**/ ?>