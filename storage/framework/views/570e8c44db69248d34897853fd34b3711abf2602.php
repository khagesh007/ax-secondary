
<div class="row row-cols-1 row-cols-md-3 g-4 mb-2">
    <div class="col">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-start align-items-center">
                    <h5 class="card-title text-uppercase fw-medium text-muted text-truncate mb-0">Listings</h5>
                    <?php if(request("exemptListingDateFilter") == false): ?>
                        <span class="badge badge-soft-info ms-2">Published in past <?php echo e($noOfDays); ?> days</span>
                    <?php else: ?>
                        <span class="badge badge-soft-info ms-2">Live</span>
                    <?php endif; ?>
                </div>
                <div class="card-text">
                    <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                        <span class="counter-value" data-target="<?php echo e(propertyTotalCounter(auth()->user()->id) ?? 0); ?>"><?php echo e(propertyTotalCounter(auth()->user()->id) ?? 0); ?></span>
                    </h4>
                </div>
                <div class="card-text">
                    <div class="d-flex justify-content-between">
                        <div>
                            PF
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value" data-target="<?php echo e($listingsCount['pf'] ?? 0); ?>"><?php echo e($listingsCount['pf'] ?? 0); ?></span>
                            </h4>
                        </div>
                        <div>
                            Bayut
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value" data-target="<?php echo e($listingsCount['bayut'] ?? 0); ?>"><?php echo e($listingsCount['bayut'] ?? 0); ?></span>
                            </h4>
                        </div>
                        <div>
                            Dubizzle
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value" data-target="<?php echo e($listingsCount['dubizzle'] ?? 0); ?>"><?php echo e($listingsCount['dubizzle'] ?? 0); ?></span>
                            </h4>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between">
                    <div>
                        <a href="<?php echo e(route('admin.listing.index')); ?>"
                           class="text-decoration-underline ax-text">View all listings</a>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-white ax-grad-background rounded fs-3">
                                            <i class="bx bx-home-circle"></i>
                                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-start align-items-center">
                    <h5 class="card-title text-uppercase fw-medium text-muted text-truncate mb-0">Leads</h5>
                    <span class="badge badge-soft-info ms-2">Recorded in past <?php echo e($noOfDays); ?> days</span>
                </div>
                <div class="card-text">
                    <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                        <span class="counter-value" data-target="<?php echo e($leadsCount->total ?? 0); ?>"><?php echo e($leadsCount->total ?? 0); ?></span>
                    </h4>
                </div>
                <div class="card-text">
                    <div class="d-flex justify-content-between">
                        <div>
                            PF
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value" data-target="<?php echo e($leadsCount->pf ?? 0); ?>"><?php echo e($leadsCount->pf ?? 0); ?></span>
                            </h4>
                        </div>
                        <div>
                            Bayut
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value" data-target="<?php echo e($leadsCount->bayut); ?>"><?php echo e($leadsCount->bayut); ?></span>
                            </h4>
                        </div>
                        <div>
                            Dubizzle
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value" data-target="<?php echo e($leadsCount->dubizzle ?? 0); ?>"><?php echo e($leadsCount->dubizzle ?? 0); ?></span>
                            </h4>
                        </div>
                        <div>
                            Other
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value" data-target="<?php echo e($leadsCount->other ?? 0); ?>"><?php echo e($leadsCount->other ?? 0); ?></span>
                            </h4>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between">
                    <div>
                        <a href="<?php echo e(route('admin.work.leads.index')); ?>"
                           class="text-decoration-underline ax-text">View all leads</a>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-white ax-grad-background rounded fs-3">
                                            <i class="bx bx-phone-call"></i>
                                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-start align-items-center">
                    <h5 class="card-title text-uppercase fw-medium text-muted text-truncate mb-0">Deals</h5>
                    <span class="badge badge-soft-info ms-2">Completed in past <?php echo e($noOfDays); ?> days</span>
                </div>

                <div class="card-text">
                    <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                        <span class="counter-value" data-target="<?php echo e($dealsCount->total ?? 0); ?>"><?php echo e($dealsCount->total ?? 0); ?></span>
                    </h4>
                </div>
                <div class="card-text">
                    <div class="d-flex justify-content-between">
                        <div>
                            PF
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value" data-target="<?php echo e($dealsCount->pf ?? 0); ?>"><?php echo e($dealsCount->pf ?? 0); ?></span>
                            </h4>
                        </div>
                        <div>
                            Bayut
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value" data-target="<?php echo e($dealsCount->bayut ?? 0); ?>"><?php echo e($dealsCount->bayut ?? 0); ?></span>
                            </h4>
                        </div>
                        <div>
                            Dubizzle
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value" data-target="<?php echo e($dealsCount->dubizzle ?? 0); ?>"><?php echo e($dealsCount->dubizzle ?? 0); ?></span>
                            </h4>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between">
                    <div>
                        <a href="<?php echo e(route('admin.work.deals.index')); ?>"
                           class="text-decoration-underline ax-text">View all deals</a>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-white ax-grad-background rounded fs-3">
                                            <i class="bx bx-wallet"></i>
                                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/dashboard/v2/partials/_counters.blade.php ENDPATH**/ ?>