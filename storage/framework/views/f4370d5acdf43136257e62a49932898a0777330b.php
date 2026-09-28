<div class="row">
    <div class="col-xxl-12">
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="card row-eq-height">
                    <div class="card-header">
                        <div class="d-flex justify-content-between">
                            <div class="flex-grow-0">
                                <h5 class="card-title">Activities</h5>
                            </div>
                            <div class="flex-shrink-0">
                                <button class="btn btn-sm btn-primary expand-no-of-list" data-type="no_of_activity" data-title="Number of Activity"><i class=" bx bx-expand"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="nav nav-tabs" id="nav-tab" role="tablist" style="width: auto">
                            <button class="nav-link active" id="nav-high-activity-count-tab"
                                    data-bs-toggle="tab"
                                    data-bs-target="#nav-high-activity-count" type="button" role="tab"
                                    aria-controls="nav-high-activity-count" aria-selected="true">High
                            </button>
                            <button class="nav-link" id="nav-least-activity-count-tab"
                                    data-bs-toggle="tab"
                                    data-bs-target="#nav-least-activity-count" type="button" role="tab"
                                    aria-controls="nav-least-activity-count" aria-selected="false">Least
                            </button>
                        </div>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="nav-high-activity-count"
                                 role="tabpanel"
                                 aria-labelledby="nav-high-activity-count-tab">
                                <ul class="list-group" style="height: 450px; overflow-y: auto">
                                    <?php $__currentLoopData = $insights["high_and_least_no_of_activities"]["high_counts"] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-1">
                                                    <img
                                                        src="<?php echo e($item->user_thumbnail ?? $item->thumb_url); ?>"
                                                        alt="" class="avatar-xs rounded">
                                                </div>
                                                <div class="ms-2">
                                                    <div class="fw-bold"><?php echo e($item->user_name); ?></div>
                                                    <?php echo e($item->zone_name ? $item->zone_name . " | " : ""); ?> <?php echo e($item->parent_name); ?>

                                                </div>
                                            </div>
                                            <span
                                                class="badge bg-dark rounded-pill"><?php echo e($item->count); ?></span>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                            <div class="tab-pane fade" id="nav-least-activity-count" role="tabpanel"
                                 aria-labelledby="nav-least-activity-count-tab">
                                <ul class="list-group" style="height: 450px; overflow-y: auto">
                                    <?php $__currentLoopData = $insights["high_and_least_no_of_activities"]["least_counts"] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-1">
                                                    <img
                                                        src="<?php echo e($item->user_thumbnail ?? $item->thumb_url); ?>"
                                                        alt="" class="avatar-xs rounded">
                                                </div>
                                                <div class="ms-2">
                                                    <div class="fw-bold"><?php echo e($item->user_name); ?></div>
                                                    <?php echo e($item->zone_name ? $item->zone_name . " | " : ""); ?> <?php echo e($item->parent_name); ?>

                                                </div>
                                            </div>
                                            <span
                                                class="badge bg-dark rounded-pill"><?php echo e($item->count); ?></span>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card row-eq-height">
                    <div class="card-header">
                        <div class="d-flex justify-content-between">
                            <div class="flex-grow-0">
                                <h5 class="card-title">Highest Number of Leads</h5>
                            </div>
                            <div class="flex-shrink-0">
                                <button class="btn btn-sm btn-primary expand-no-of-list" data-type="no_of_leads" data-title="Highest number of Leads"><i class=" bx bx-expand"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="nav nav-tabs" id="nav-tab" role="tablist" style="width: auto">
                            <button class="nav-link active" id="nav-overall-lead-count-tab"
                                    data-bs-toggle="tab"
                                    data-bs-target="#nav-overall-lead-count" type="button" role="tab"
                                    aria-controls="nav-overall-lead-count" aria-selected="true">Overall
                            </button>
                            <button class="nav-link" id="nav-property-finder-lead-count-tab"
                                    data-bs-toggle="tab"
                                    data-bs-target="#nav-property-finder-lead-count" type="button"
                                    role="tab"
                                    aria-controls="nav-property-finder-lead-count" aria-selected="true">
                                PF
                            </button>
                            <button class="nav-link" id="nav-bayut-lead-count-tab" data-bs-toggle="tab"
                                    data-bs-target="#nav-bayut-lead-count" type="button" role="tab"
                                    aria-controls="nav-bayut-lead-count" aria-selected="false">Bayut
                            </button>
                            <button class="nav-link" id="nav-dubizzle-lead-count-tab"
                                    data-bs-toggle="tab"
                                    data-bs-target="#nav-dubizzle-lead-count" type="button" role="tab"
                                    aria-controls="nav-dubizzle-lead-count" aria-selected="false">
                                Dubizzle
                            </button>
                        </div>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="nav-overall-lead-count"
                                 role="tabpanel"
                                 aria-labelledby="nav-overall-lead-count-tab">
                                <ul class="list-group" style="height: 450px; overflow-y: auto">
                                    <?php $__currentLoopData = $insights["highest_no_of_leads"]["overall_counts"] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-1">
                                                    <img
                                                        src="<?php echo e($item->user_thumbnail ?? $item->thumb_url); ?>"
                                                        alt="" class="avatar-xs rounded">
                                                </div>
                                                <div class="ms-2">
                                                    <div class="fw-bold"><?php echo e($item->user_name); ?></div>
                                                    <?php echo e($item->zone_name ? $item->zone_name . " | " : ""); ?> <?php echo e($item->parent_name); ?>

                                                </div>
                                            </div>
                                            <span
                                                class="badge bg-dark rounded-pill"><?php echo e($item->count); ?></span>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                            <div class="tab-pane fade" id="nav-property-finder-lead-count"
                                 role="tabpanel"
                                 aria-labelledby="nav-property-finder-lead-count-tab">
                                <ul class="list-group" style="height: 450px; overflow-y: auto">
                                    <?php $__currentLoopData = $insights["highest_no_of_leads"]["property_finder_counts"] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-1">
                                                    <img
                                                        src="<?php echo e($item->user_thumbnail ?? $item->thumb_url); ?>"
                                                        alt="" class="avatar-xs rounded">
                                                </div>
                                                <div class="ms-2">
                                                    <div class="fw-bold"><?php echo e($item->user_name); ?></div>
                                                    <?php echo e($item->zone_name ? $item->zone_name . " | " : ""); ?> <?php echo e($item->parent_name); ?>

                                                </div>
                                            </div>
                                            <span
                                                class="badge bg-dark rounded-pill"><?php echo e($item->count); ?></span>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                            <div class="tab-pane fade" id="nav-bayut-lead-count" role="tabpanel"
                                 aria-labelledby="nav-bayut-lead-count-tab">
                                <ul class="list-group" style="height: 450px; overflow-y: auto">
                                    <?php $__currentLoopData = $insights["highest_no_of_leads"]["bayut_counts"] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-1">
                                                    <img
                                                        src="<?php echo e($item->user_thumbnail ?? $item->thumb_url); ?>"
                                                        alt="" class="avatar-xs rounded">
                                                </div>
                                                <div class="ms-2">
                                                    <div class="fw-bold"><?php echo e($item->user_name); ?></div>
                                                    <?php echo e($item->zone_name ? $item->zone_name . " | " : ""); ?> <?php echo e($item->parent_name); ?>

                                                </div>
                                            </div>
                                            <span
                                                class="badge bg-dark rounded-pill"><?php echo e($item->count); ?></span>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                            <div class="tab-pane fade" id="nav-dubizzle-lead-count" role="tabpanel"
                                 aria-labelledby="nav-dubizzle-lead-count-tab">
                                <ul class="list-group" style="height: 450px; overflow-y: auto">
                                    <?php $__currentLoopData = $insights["highest_no_of_leads"]["dubizzle_counts"] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-1">
                                                    <img
                                                        src="<?php echo e($item->user_thumbnail ?? $item->thumb_url); ?>"
                                                        alt="" class="avatar-xs rounded">
                                                </div>
                                                <div class="ms-2">
                                                    <div class="fw-bold"><?php echo e($item->user_name); ?></div>
                                                    <?php echo e($item->zone_name ? $item->zone_name . " | " : ""); ?> <?php echo e($item->parent_name); ?>

                                                </div>
                                            </div>
                                            <span
                                                class="badge bg-dark rounded-pill"><?php echo e($item->count); ?></span>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card row-eq-height">
                    <div class="card-header">
                        <div class="d-flex justify-content-between">
                            <div class="flex-grow-0">
                                <h5 class="card-title">Highest Number of Listings</h5>
                            </div>
                            <div class="flex-shrink-0">
                                <button class="btn btn-sm btn-primary expand-no-of-list" data-type="no_of_listings" data-title="Highest Number of Listings"><i class=" bx bx-expand"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="nav nav-tabs" id="nav-tab" role="tablist" style="width: auto">
                            <button class="nav-link active" id="nav-overall-listing-count-tab"
                                    data-bs-toggle="tab"
                                    data-bs-target="#nav-overall-listing-count" type="button" role="tab"
                                    aria-controls="nav-overall-listing-count" aria-selected="true">
                                Overall
                            </button>
                            <button class="nav-link" id="nav-property-finder-listing-count-tab"
                                    data-bs-toggle="tab"
                                    data-bs-target="#nav-property-finder-listing-count" type="button"
                                    role="tab"
                                    aria-controls="nav-property-finder-listing-count"
                                    aria-selected="true">PF
                            </button>
                            <button class="nav-link" id="nav-bayut-listing-count-tab"
                                    data-bs-toggle="tab"
                                    data-bs-target="#nav-bayut-listing-count" type="button" role="tab"
                                    aria-controls="nav-bayut-listing-count" aria-selected="false">Bayut
                            </button>
                            <button class="nav-link" id="nav-dubizzle-listing-count-tab"
                                    data-bs-toggle="tab"
                                    data-bs-target="#nav-dubizzle-listing-count" type="button"
                                    role="tab"
                                    aria-controls="nav-dubizzle-listing-count" aria-selected="false">
                                Dubizzle
                            </button>
                        </div>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="nav-overall-listing-count"
                                 role="tabpanel"
                                 aria-labelledby="nav-overall-listing-count-tab">
                                <ul class="list-group" style="height: 450px; overflow-y: auto">
                                    <?php $__currentLoopData = $insights["highest_no_of_listings"]["overall_counts"] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-1">
                                                    <img
                                                        src="<?php echo e($item->user_thumbnail ?? $item->thumb_url); ?>"
                                                        alt="" class="avatar-xs rounded">
                                                </div>
                                                <div class="ms-2">
                                                    <div class="fw-bold"><?php echo e($item->user_name); ?></div>
                                                    <?php echo e($item->zone_name ? $item->zone_name . " | " : ""); ?> <?php echo e($item->parent_name); ?>

                                                </div>
                                            </div>
                                            <span
                                                class="badge bg-dark rounded-pill"><?php echo e($item->count); ?></span>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                            <div class="tab-pane fade" id="nav-property-finder-listing-count"
                                 role="tabpanel"
                                 aria-labelledby="nav-property-finder-listing-count-tab">
                                <ul class="list-group" style="height: 450px; overflow-y: auto">
                                    <?php $__currentLoopData = $insights["highest_no_of_listings"]["property_finder_counts"] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-1">
                                                    <img
                                                        src="<?php echo e($item->user_thumbnail ?? $item->thumb_url); ?>"
                                                        alt="" class="avatar-xs rounded">
                                                </div>
                                                <div class="ms-2">
                                                    <div class="fw-bold"><?php echo e($item->user_name); ?></div>
                                                    <?php echo e($item->zone_name ? $item->zone_name . " | " : ""); ?> <?php echo e($item->parent_name); ?>

                                                </div>
                                            </div>
                                            <span
                                                class="badge bg-dark rounded-pill"><?php echo e($item->count); ?></span>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                            <div class="tab-pane fade" id="nav-bayut-listing-count" role="tabpanel"
                                 aria-labelledby="nav-bayut-listing-count-tab">
                                <ul class="list-group" style="height: 450px; overflow-y: auto">
                                    <?php $__currentLoopData = $insights["highest_no_of_listings"]["bayut_counts"] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-1">
                                                    <img
                                                        src="<?php echo e($item->user_thumbnail ?? $item->thumb_url); ?>"
                                                        alt="" class="avatar-xs rounded">
                                                </div>
                                                <div class="ms-2">
                                                    <div class="fw-bold"><?php echo e($item->user_name); ?></div>
                                                    <?php echo e($item->zone_name ? $item->zone_name . " | " : ""); ?> <?php echo e($item->parent_name); ?>

                                                </div>
                                            </div>
                                            <span
                                                class="badge bg-dark rounded-pill"><?php echo e($item->count); ?></span>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                            <div class="tab-pane fade" id="nav-dubizzle-listing-count" role="tabpanel"
                                 aria-labelledby="nav-dubizzle-listing-count-tab">
                                <ul class="list-group" style="height: 450px; overflow-y: auto">
                                    <?php $__currentLoopData = $insights["highest_no_of_listings"]["dubizzle_counts"] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-1">
                                                    <img
                                                        src="<?php echo e($item->user_thumbnail ?? $item->thumb_url); ?>"
                                                        alt="" class="avatar-xs rounded">
                                                </div>
                                                <div class="ms-2">
                                                    <div class="fw-bold"><?php echo e($item->user_name); ?></div>
                                                    <?php echo e($item->zone_name ? $item->zone_name . " | " : ""); ?> <?php echo e($item->parent_name); ?>

                                                </div>
                                            </div>
                                            <span
                                                class="badge bg-dark rounded-pill"><?php echo e($item->count); ?></span>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card row-eq-height">
                    <div class="card-header">
                        <div class="d-flex justify-content-between">
                            <div class="flex-grow-0">
                                <h5 class="card-title">Highest Quality Score</h5>
                            </div>
                            <div class="flex-shrink-0">
                                <button class="btn btn-sm btn-primary expand-no-of-list" data-type="no_of_quality_scores" data-title="Highest Quality Score"><i class=" bx bx-expand"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="nav nav-tabs" id="nav-tab" role="tablist" style="width: auto">
                            <button class="nav-link active" id="nav-property-finder-scores-tab"
                                    data-bs-toggle="tab"
                                    data-bs-target="#nav-property-finder-scores" type="button"
                                    role="tab"
                                    aria-controls="nav-property-finder-scores" aria-selected="true">
                                PF
                            </button>
                            <button class="nav-link" id="nav-bayut-scores-tab" data-bs-toggle="tab"
                                    data-bs-target="#nav-bayut-scores" type="button" role="tab"
                                    aria-controls="nav-bayut-scores" aria-selected="false">Bayut
                            </button>
                            <button class="nav-link" id="nav-dubizzle-scores-tab" data-bs-toggle="tab"
                                    data-bs-target="#nav-dubizzle-scores" type="button" role="tab"
                                    aria-controls="nav-dubizzle-scores" aria-selected="false">Dubizzle
                            </button>
                        </div>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="nav-property-finder-scores"
                                 role="tabpanel"
                                 aria-labelledby="nav-property-finder-scores-tab">
                                <ul class="list-group" style="height: 450px; overflow-y: auto">
                                    <?php $__currentLoopData = $insights["highest_no_of_quality_scores"]["property_finder_counts"] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-1">
                                                    <img
                                                        src="<?php echo e($item->user_thumbnail ?? $item->thumb_url); ?>"
                                                        alt="" class="avatar-xs rounded">
                                                </div>
                                                <div class="ms-2">
                                                    <div class="fw-bold"><?php echo e($item->user_name); ?></div>
                                                    <?php echo e($item->zone_name ? $item->zone_name . " | " : ""); ?> <?php echo e($item->parent_name); ?>

                                                </div>
                                            </div>
                                            <span
                                                class="badge bg-dark rounded-pill"><?php echo e($item->count); ?>%</span>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                            <div class="tab-pane fade" id="nav-bayut-scores" role="tabpanel"
                                 aria-labelledby="nav-bayut-scores-tab">
                                <ul class="list-group" style="height: 450px; overflow-y: auto">
                                    <?php $__currentLoopData = $insights["highest_no_of_quality_scores"]["bayut_counts"] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-1">
                                                    <img
                                                        src="<?php echo e($item->user_thumbnail ?? $item->thumb_url); ?>"
                                                        alt="" class="avatar-xs rounded">
                                                </div>
                                                <div class="ms-2">
                                                    <div class="fw-bold"><?php echo e($item->user_name); ?></div>
                                                    <?php echo e($item->zone_name ? $item->zone_name . " | " : ""); ?> <?php echo e($item->parent_name); ?>

                                                </div>
                                            </div>
                                            <span
                                                class="badge bg-dark rounded-pill"><?php echo e($item->count); ?>%</span>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                            <div class="tab-pane fade" id="nav-dubizzle-scores" role="tabpanel"
                                 aria-labelledby="nav-dubizzle-scores-tab">
                                <ul class="list-group" style="height: 450px; overflow-y: auto">
                                    <?php $__currentLoopData = $insights["highest_no_of_quality_scores"]["dubizzle_counts"] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-1">
                                                    <img
                                                        src="<?php echo e($item->user_thumbnail ?? $item->thumb_url); ?>"
                                                        alt="" class="avatar-xs rounded">
                                                </div>
                                                <div class="ms-2">
                                                    <div class="fw-bold"><?php echo e($item->user_name); ?></div>
                                                    <?php echo e($item->zone_name ? $item->zone_name . " | " : ""); ?> <?php echo e($item->parent_name); ?>

                                                </div>
                                            </div>
                                            <span
                                                class="badge bg-dark rounded-pill"><?php echo e($item->count); ?>%</span>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/dashboard/v2/partials/_agent_insights.blade.php ENDPATH**/ ?>