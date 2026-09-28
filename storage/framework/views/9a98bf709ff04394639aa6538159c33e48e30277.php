<div class="row sticky-top-duration position-sticky">
    <div class="col-md-12">
        <div class="card p-2" style="
        /* background: #e9e9ee; */
        border: none;
        box-shadow: none;
        padding: 5px 5px !important;
    ">
            <div class="card-body p-1" style="overflow: scroll">
                <form id="filter-form">
                    <div class="d-flex flex-nowrap justify-content-between">
                        <div class="me-1 d-flex justify-content-between gap-2" style="min-width: 150px!important">
                            <input class="form-control-sm form-control" id="date-filter" name="date">
                            <select name='groups[]' id="group-select" class="form-control select2 inputs" multiple>
                                <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if(!is_null($loggedInUser->zone_id)): ?>
                                        <?php if($loggedInUser->zone_id == $group->id): ?>
                                            <option value="<?php echo e($group->id); ?>" selected><?php echo e($group->name); ?></option>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <option value="<?php echo e($group->id); ?>" <?php echo e(in_array($group->id, request("groups") ?? []) ? "selected" : ""); ?>><?php echo e($group->name); ?></option>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="text-end">
                            <button class="btn btn-primary">Apply Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/reports/agent_insights/partials/_filter.blade.php ENDPATH**/ ?>