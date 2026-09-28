<div class="row">
    <div class="col-md-12 pb-3">
        <div class="card">
            <div class="card-body text-center p-3">
                <h5>Performance Report</h5>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card card-height-100">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">⚠️ Leads In NEW Status</h4>
                <div class="flex-shrink-0">
                    <button class="btn btn-sm btn-primary expand-report" data-title="Leads In NEW Status"><i class=" bx bx-expand"></i></button>
                </div>
            </div>

            <div class="card-body">
                <div data-simplebar style="max-height: 250px;" class="mb-4">
                    <ul class="list-group">
                        <?php $__currentLoopData = $new_status_lead ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($user['leads_count']>2): ?>
                                <li class="list-group-item">
                                    <div class="d-flex align-items-center list-content">
                                        <div class="flex-grow-1">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0">
                                                    <img src="<?php echo e($user['thumb_url']); ?>" alt=""
                                                         class="avatar-xs rounded" />
                                                </div>
                                                <div class="flex-shrink-0 ms-2">
                                                    <h6 class="fs-14 mb-0"><?php echo e($user['name']); ?></h6>
                                                    <p class="text-muted"><?php echo e(teamFinder($user['bitrix_role'])); ?> - <?php echo e($user->parent->full_name); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <span class="text"><?php echo e($user['leads_count']); ?> Leads</span>
                                        </div>
                                    </div>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card card-height-100">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">⚠️ Incomplete Leads</h4>
                <div class="flex-shrink-0">
                    <button class="btn btn-sm btn-primary expand-report" data-title="Incomplete Leads"><i class=" bx bx-expand"></i></button>
                </div>
            </div>
            <div class="card-body">
                <div data-simplebar style="max-height: 250px;" class="mb-4">
                    <ul class="list-group">
                        <?php $__currentLoopData = $incomplete_lead_agents ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($user['leads_count']>2): ?>
                                <li class="list-group-item">
                                    <div class="d-flex align-items-center list-content">
                                        <div class="flex-grow-1">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0">
                                                    <img src="<?php echo e($user['thumb_url']); ?>" alt=""
                                                         class="avatar-xs rounded" />
                                                </div>
                                                <div class="flex-shrink-0 ms-2">
                                                    <h6 class="fs-14 mb-0"><?php echo e($user['name']); ?></h6>
                                                    <p class="text-muted"><?php echo e(teamFinder($user['bitrix_role'])); ?> - <?php echo e($user->parent->full_name); ?> </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <span class="text"><?php echo e($user['leads_count']); ?> Leads</span>
                                        </div>
                                    </div>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card card-height-100">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">🗓 Days since their last event</h4>
                <div class="flex-shrink-0">
                    <button class="btn btn-sm btn-primary expand-report"  data-title="Days since their last event"><i class=" bx bx-expand"></i></button>
                </div>
            </div>

            <div class="card-body">
                <div data-simplebar style="max-height: 250px;" class="mb-4">
                    <ul class="list-group">
                        <?php $__currentLoopData = $agents ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="list-group-item">
                                <div class="d-flex align-items-center list-content">
                                    <div class="flex-grow-1">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                <img src="<?php echo e($user['image']); ?>" alt=""
                                                     class="avatar-xs rounded" />
                                            </div>
                                            <div class="flex-shrink-0 ms-2">
                                                <h6 class="fs-14 mb-0"><?php echo e($user['name']); ?></h6>
                                                <p class="text-muted"><?php echo e(teamFinder($user['bitrix_role'])); ?> - <?php echo e($user['manager']); ?></p>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="text"><?php echo e($user['days']); ?> Days</span>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card card-height-100">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">⚠️ Leads by Source in <?php echo e($hours ?? 0); ?> hours</h4>
                <div class="flex-shrink-0">
                    <button class="btn btn-sm btn-primary expand-report"  data-title="Leads by Source in <?php echo e($hours ?? 0); ?> hours"><i class=" bx bx-expand"></i></button>
                </div>
            </div>

            <div class="card-body">
                <div data-simplebar style="max-height: 250px;" class="mb-4">
                    <ul class="list-group">
                        <?php $__currentLoopData = $sourceWithLeadsCount ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $source): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($source['leads_count']>2): ?>
                                <li class="list-group-item">
                                    <div class="d-flex align-items-center list-content">
                                        <div class="flex-grow-1">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0">
                                                    <img src="<?php echo e($source['thumbnail']??'https://secondary.axcrm.ae/assets/images/logo/favicon-32x32.png'); ?>" alt=""
                                                         class="avatar-xs rounded" />
                                                </div>
                                                <div class="flex-shrink-0 ms-2">
                                                    <h6 class="fs-14 mb-0"><?php echo e($source['name']); ?></h6>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <span class="text"><?php echo e($source['leads_count']); ?> Leads</span>
                                        </div>
                                    </div>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card card-height-100">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">🚨 Suspension Details</h4>
                <div class="flex-shrink-0">
                    <button class="btn btn-sm btn-primary expand-report" data-title="Suspension Details"><i class=" bx bx-expand"></i></button>
                </div>
            </div>
            <div class="card-body">
                <div data-simplebar style="max-height: 250px;" class="mb-4">
                    <ul class="list-group">
                        <?php $__currentLoopData = $users ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $suspensions = $user->suspensions->count();
                                $warnings = $user->warnings->count();
                            ?>
                            <?php if($suspensions > 2): ?>

                                <?php
                                    $ratio = $suspensions/$warnings;
                                    $ratio = number_format((float)$ratio, 2, '.', '');
                                ?>

                                <li class="list-group-item">
                                    <div class="d-flex align-items-center list-content">
                                        <div class="flex-grow-1">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0">
                                                    <img src="<?php echo e($user->thumb_url); ?>" alt=""
                                                         class="avatar-xs rounded" />
                                                </div>
                                                <div class="flex-shrink-0 ms-2">
                                                    <h6 class="fs-14 mb-0"><?php echo e($user->full_name); ?></h6>
                                                    <p class="fs-12 mb-0 text-muted">W: <?php echo e($warnings); ?> - Ratio <?php echo e($ratio); ?>   </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <span class="text"><?php echo e($suspensions); ?></span>
                                        </div>
                                    </div>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card card-height-100">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">🗓 0 DEALS SINCE JOINING (MORE THAN 2 MONTH)</h4>
                <div class="flex-shrink-0">
                    <button class="btn btn-sm btn-primary expand-report" data-title="0 DEALS SINCE JOINING (MORE THAN 2 MONTH)"><i class=" bx bx-expand"></i></button>
                </div>
            </div>

            <div class="card-body">
                <div data-simplebar style="max-height: 250px;" class="mb-4">
                    <ul class="list-group">
                        <?php $__currentLoopData = $no_deal_agents ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="list-group-item">
                                <div class="d-flex align-items-center list-content">
                                    <div class="flex-grow-1">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                <img src="<?php echo e($user['thumb_url']); ?>" alt=""
                                                     class="avatar-xs rounded" />
                                            </div>
                                            <div class="flex-shrink-0 ms-2">
                                                <h6 class="fs-14 mb-0"><?php echo e($user['name']); ?></h6>
                                                <p class="text-muted"><?php echo e(teamFinder($user['bitrix_role'])); ?> - <?php echo e($user->parent->full_name); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="text"><?php echo e(date('M d, Y',strtotime($user['joining_date']))); ?></span>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card card-height-100">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">🗓 Days Since Last Deal</h4>
                <div class="flex-shrink-0">
                    <button class="btn btn-sm btn-primary expand-report" data-title="Days Since Last Deal"><i class=" bx bx-expand"></i></button>
                </div>
            </div>

            <div class="card-body">
                <div data-simplebar style="max-height: 250px;" class="mb-4">
                    <ul class="list-group">
                        <?php $__currentLoopData = $sorted_deals_days_count ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="list-group-item">
                                <div class="d-flex align-items-center list-content">
                                    <div class="flex-grow-1">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                <img src="<?php echo e($user['thumb_url']); ?>" alt=""
                                                     class="avatar-xs rounded" />
                                            </div>
                                            <div class="flex-shrink-0 ms-2">
                                                <h6 class="fs-14 mb-0"><?php echo e($user['name']); ?></h6>
                                                <p class="text-muted"><?php echo e(teamFinder($user['bitrix_role'])); ?> - <?php echo e($user->parent->full_name); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="text"><?php echo e($user['days_since_last_deal']); ?></span>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/dashboard/v2/partials/_performance_reports.blade.php ENDPATH**/ ?>