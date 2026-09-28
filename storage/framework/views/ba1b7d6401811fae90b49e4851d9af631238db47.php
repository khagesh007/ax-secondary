    <div class="">
        <div class="card-header align-items-center d-flex">
            <div class="flex-grow-1 oveflow-hidden">
            </div>
            <div class="flex-shrink-0 ms-2">
                <ul class="nav justify-content-end nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#table-one" role="tab">
                            Tabular View
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#graph-one" role="tab">
                            Graphical View
                        </a>
                    </li>

                </ul>
            </div>
        </div>

        <div class="card-body">
            <!-- Tab panes -->
            <div class="tab-content text-muted">
                <div class="tab-pane active" id="table-one" role="tabpanel">
                    <?php if($locations->count() > 0): ?>
                    <div class=" table-card m-3">
                        <ul class="nav nav-tabs nav-tabs-custom nav-success mb-1 no-border d-inline-flex" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link All  <?php echo e($status==''?'active':''); ?>  statusFilterClear margin-minus-4 listingStatus" data-bs-toggle="tab"
                                    id="All" href="#" role="tab" aria-selected="false" tabindex="-1">
                                    All
                                </a>
                            </li>

                            <?php $__currentLoopData = \App\Models\Property::PUBLISH_STATUS_SELECT ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($key == 'ARCHIVED'): ?>
                                    <?php if(auth()->user()->hasRole(['Admin', 'Super Admin'])): ?>
                                        <li class="nav-item" role="presentation" id="status_id_<?php echo e($key); ?>_placement">
                                            <a class="nav-link All  statusFilter margin-minus-4 text-uppercase listingStatus <?php echo e($status==$key?'active':''); ?>"
                                                data-bs-toggle="tab" id="status_id_<?php echo e($key); ?>" href="javascript:void(0)"
                                                data-status="<?php echo e($key); ?>" role="tab" aria-selected="false"
                                                tabindex="-1">
                                                <?php echo e($value); ?>

                                            </a>
                                        </li>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <li class="nav-item" role="presentation" id="status_id_<?php echo e($key); ?>_placement">
                                        <a class="nav-link All  statusFilter margin-minus-4 text-uppercase listingStatus  <?php echo e($status==$key?'active':''); ?>" data-bs-toggle="tab"
                                            id="status_id_<?php echo e($key); ?>" href="javascript:void(0)"
                                            data-status="<?php echo e($key); ?>" role="tab" aria-selected="false"
                                            tabindex="-1">
                                            <?php echo e($value); ?>

                                        </a>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            <li class="nav-item" role="presentation" id="status_id_Live_placement">
                                <a class="nav-link All  statusFilterLive listingStatus" data-bs-toggle="tab" id="All" href="#"
                                    role="tab" aria-selected="false" tabindex="-1" data-status="PUBLISHED" data-is_live="1">
                                    <i title="Live Listing" class="bx bx-check-circle text-success" style="font-size: 20px"></i>
                                </a>
                            </li>
                            <li>
                                <select name="portal_id" id="portal_id" class="form-control select2 inputs">
                                    <option value="">Select Portal</option>
                                    <?php $__currentLoopData = $portals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $portal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($portal->id); ?>" <?php echo e($selected_portal==$portal->id?'selected':''); ?>><?php echo e($portal->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </li>
                        </ul>


                        <div class="table-responsive">
                        <table id="buttons-datatables" class="display table align-middle">
                            <div id="datatableSearch"></div>
                            <thead>
                                <tr>
                                    <th style="background-color: #f0e4dd!important;z-index:400"><small>Agent</small></th>
                                    <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                    $location_name = $location->id.'_'. $location->name;
                                    ?>
                                        <th class="getlocationdetails" data-location-name="<?php echo e($location->name); ?>" style="background-color: #f0e4dd!important;"><small title="<?php echo e($location_name); ?>" data-title="<?php echo e($location->name); ?>" class="location-names"><?php echo e(shortNameGenerator($location->name)); ?></small></th>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <th style="background-color: #f0e4dd!important"><small  title="Others" data-title="Others" class="location-names">Others</small></th>
                                    <th style="background-color: #f0e4dd!important"> <small>Total</small> </th>
                                </tr>
                            </thead>
                            <tbody class="">
                                <?php $__currentLoopData = $agents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td  style="background-color: rgb(242, 240, 240)!important">
                                            <div class="d-flex align-items-center" data-agent-id="<?php echo e($agent->id); ?>"
                                                title="<?php echo e($agent->full_name); ?>">
                                                <div class="flex-shrink-0 me-2">
                                                    <img src="<?php echo e($agent->thumbnail ?? "https://ui-avatars.com/api/".$agent->full_name); ?>" alt="" class="avatar-xs rounded-circle">
                                                </div>

                                                <div class="flex-grow-1"><?php echo e($agent->full_name); ?><p title="Created at" class="mb-0">
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <?php
                                            $count = 0;
                                        ?>
                                        <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                        $location_name = $location->id.'_'. $location->name;
                                    ?>
                                            <td ><?php echo e($agentLocationCounts[$agent->name][$location_name] == 0 ? '-' : $agentLocationCounts[$agent->name][$location_name] ?? 0); ?>

                                            </td>
                                            <?php
                                                $count += $agentLocationCounts[$agent->name][$location_name];
                                            ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <td><?php echo e($agentLocationCounts[$agent->name]['Others'] == 0 ? '-' : $agentLocationCounts[$agent->name]['Others'] ?? 0); ?>

                                        </td>
                                        <?php
                                            $count += $agentLocationCounts[$agent->name]['Others'];
                                        ?>

                                        <td style="background-color: rgb(225, 221, 221)!important"><b><?php echo e($count); ?></b></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                

                                <tr>
                                    <th style="background-color: #f0e4dd!important;z-index:400">Summary</th>
                                    <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <th class="showCount location-counts" style="background-color: #f0e4dd!important">0</th>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                    <th class="showCount location-counts" style="background-color: #f0e4dd!important">0</th>
                                    <th class="showCount" style="background-color: #f0e4dd!important;z-index:400">0</th>
                                </tr>

                                <tr>
                                    <th style="background-color: #f0e4dd!important;z-index:400">%</th>
                                    <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <th class="showPercentage location-percentage" style="background-color: #f0e4dd!important">0</th>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                    <th class="showPercentage location-percentage" style="background-color: #f0e4dd!important">0</th>
                                    <th class="showPercentage" style="background-color: #f0e4dd!important;z-index:400">0</th>
                                </tr>
                            </tbody>




                        </table>
                    </div>
                    </div>

                <?php else: ?>
                    <div class="alert alert-danger">No Locations Found</div>
                <?php endif; ?>

                </div>
                <div class="tab-pane" id="graph-one" role="tabpanel">
                    <div id="tableAppendTwoGraph" style="height:800px; width:1100px"></div>
                </div>

            </div>
        </div><!-- end card-body -->
    </div>

<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/reports/partials/_area_wise_listing_report.blade.php ENDPATH**/ ?>