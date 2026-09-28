<?php if(isset($suspensions)): ?>
    <?php if($suspensions instanceof Illuminate\Support\Collection && $suspensions->count() > 0): ?>


        <div class="card-body mt-0 form-steps">

            <h5 class="mt-2">User Warnings & Suspensions</h5>
            <div style=" max-height: 500px;overflow-y: auto;">
            <table class="table table-nowrap">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">No. of Leads</th>
                        <th scope="col">Manager</th>
                        <th scope="col">Tag</th>
                        <th scope="col">Date</th>

                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $suspensions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $suspension): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $jsonData = json_decode($suspension->meta_value, true);
                        ?>
                        <tr>
                            <th scope="row"><?php echo e($suspension->id); ?></th>
                            <td><?php echo e($jsonData['leads']); ?></td>
                            <td><?php echo e($jsonData['manager']); ?></td>
                            <td><span class="badge badge-warning bg-<?php echo e($suspension->tag); ?>"><?php echo e(formatText($suspension->tag)); ?></span></td>
                            <td><?php echo e(\Carbon\Carbon::parse($suspension->created_at)); ?> (<?php echo e(\Carbon\Carbon::parse($suspension->created_at)->diffForHumans()); ?>)</td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>

            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/user/includes/component/_suspension_data.blade.php ENDPATH**/ ?>