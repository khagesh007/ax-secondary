<div class="col-12">
    <div class="d-flex align-items-lg-center flex-lg-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-16 mb-1"><?php echo e(timeGreetings()); ?>, <span class="ax-text"> <?php echo e(auth()?->user()?->name); ?>!</span>
            </h4>
            <input class="form-control-sm form-control" id="date-filter" name="date" style="height: 37px; margin-top: 8px">
        </div>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Dashboard Reports')): ?>
            <div class="flex-grow-1">
                <form action="">
                    <label for="dashboard-selector" class="form-label">Select Dashboard</label>
                    <select name="dashboard-selector" class="form-control" id="dashboard-selector">
                        <?php $__currentLoopData = $dashboards ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dashboard): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($dashboard["value"]); ?>"
                                    <?php echo e(request("dashboard") == $dashboard["value"] ? "selected" : ""); ?>><?php echo e($dashboard["name"]); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>
<div class="col-12">
    <div class="card mt-1">
        <div class="card-body">
            <div class="d-flex flex-nowrap justify-content-between">
                <div class="d-flex align-items-lg-center flex-lg-row flex-column gap-2">
                    <div class="form-check form-switch form-switch-success">
                        <input
                            class="form-check-input search-inputs filter-checkbox-select"
                            type="checkbox"
                            name="filter-inactive-users"
                            value="inactiveUsers"
                            role="switch"
                            id="filter-inactive-users"
                            <?php echo e(request("inactiveUsers") ? "checked" : ""); ?>

                        >
                        <label class="form-check-label" for="filter-inactive-users">Show Inactive Agent's Data</label>
                    </div>
                    <div class="form-check form-switch form-switch-success">
                        <input
                            class="form-check-input search-inputs filter-checkbox-select"
                            type="checkbox"
                            name="filter-archived-leads"
                            value="archivedLeads"
                            role="switch"
                            id="filter-archived-leads"
                            <?php echo e(request("archivedLeads") ? "checked" : ""); ?>

                        >
                        <label class="form-check-label" for="filter-archived-leads">Show Archived Leads</label>
                    </div>
                    <div class="form-check form-switch form-switch-success">
                        <input
                            class="form-check-input search-inputs filter-checkbox-select"
                            type="checkbox"
                            name="filter-deleted-leads"
                            value="deletedLeads"
                            role="switch"
                            id="filter-deleted-leads"
                            <?php echo e(request("deletedLeads") ? "checked" : ""); ?>

                        >
                        <label class="form-check-label" for="filter-deleted-leads">Show Deleted Leads</label>
                    </div>

                    <div class="form-check form-switch form-switch-success">
                        <input
                            class="form-check-input search-inputs filter-checkbox-select"
                            type="checkbox"
                            name="filter-exempt-date-to-listings"
                            value="exemptListingDateFilter"
                            role="switch"
                            id="filter-exempt-date-to-listings"
                            <?php echo e(request("exemptListingDateFilter") ? "" : "checked"); ?>

                        >
                        <label class="form-check-label apply-filter-button" for="filter-exempt-date-to-listings">Exempt date filter to listings</label>
                    </div>
                </div>
                <div>
                    <div class="text-end">
                        <button class="btn btn-primary" id="apply-filter-button">Apply Filter</button>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/dashboard/v2/partials/_dropdown.blade.php ENDPATH**/ ?>