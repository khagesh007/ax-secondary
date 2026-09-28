<?php if(isset($duplicates)): ?>
<?php if($duplicates instanceof Illuminate\Support\Collection && $duplicates->count() > 0): ?>
<div class="col-md-12">
    <div class="card">

        <div class="card-body mt-0 form-steps">

            <h5 class="mt-2">Duplicate Leads</h5>
            <table class="table table-nowrap">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Name</th>

                        <th scope="col">Mobile</th>

                        <th scope="col">Source</th>



                        <th scope="col">Purpose</th>
                        <th scope="col">Type</th>
                        <th scope="col">Agent</th>

                        <th scope="col">Status</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $duplicates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <th scope="row"><a href="#" class="fw-semibold"><?php echo e($lead->id); ?></a></th>
                            <td><span class='me-1'><span class='me-1'><i
                                            class='<?php echo e($lead->leadMedium->icon ?? 'mdi mdi-account-cash'); ?>'></i></span><?php echo e($lead->first_name); ?>

                                    <?php echo e($lead->last_name); ?></td>
                            <td><?php echo e($lead->mobile); ?></td>

                            <td><span class="display-inline"><img
                                        src='<?php echo e($lead->leadSource?->thumbnail ?? 'https://ui-avatars.com/api/?name=' . $lead->leadSource?->name); ?>'
                                        class="pe-1" alt=""
                                        style="height: 20px; width: 20px"><?php echo e($lead->leadSource?->name); ?></span></td>


                            <td>
                                <?php if(is_array($lead->for)): ?>
                                    <?php $__currentLoopData = $lead->for; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span class="badge align-middle badge-soft-dark"><?php echo e($item); ?></span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <span class="badge align-middle badge-soft-dark">Unspecified</span>
                                <?php endif; ?>
                            </td>


                            <td><?php echo e($lead->leadType?->name); ?></td>

                            <td>
                                <div class="d-flex align-items-center userDetails" data-agent-id="${data.agent.id}"
                                    title="${data.agent.name}">
                                    <div class="flex-shrink-0 me-2">
                                        <img src="<?php echo e($lead->agent?->thumb_url); ?>" alt=""
                                            class="avatar-xs rounded-circle">
                                    </div>
                                    <div class="flex-grow-1"><?php echo e($lead->agent?->full_name); ?><p title="Created at"
                                            class="mb-0">
                                            <small><?php echo e(\Carbon\Carbon::parse($lead->created_at)->diffForHumans()); ?></small>
                                        </p>
                                    </div>
                                </div>

                            </td>

                            <td><span
                                    class="badge align-middle <?php echo e($lead->leadStatus?->badge ?? 'color-bg-19'); ?>"><?php echo e($lead->leadStatus?->name); ?></span>
                            </td>

                            <td><a href="javascript:void(0);" onclick="viewData(<?php echo e($lead->id); ?>)"
                                    class="link-success">View<i class="ri-arrow-right-line align-middle"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>
<?php endif; ?>

<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/lead/include/component/duplicate_leads.blade.php ENDPATH**/ ?>