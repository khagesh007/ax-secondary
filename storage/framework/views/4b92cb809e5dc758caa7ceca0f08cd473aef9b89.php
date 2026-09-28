<?php if(isset($devices)): ?>
<?php if($devices instanceof Illuminate\Support\Collection && $devices->count() > 0): ?>
        <div class="card-body mt-0 form-steps">

            <h5 class="mt-2">User Devices</h5>
            <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col" style="max-width: 140px;">Location</th>
                        <th scope="col">App Version</th>
                        <th scope="col">Device Name</th>
                        <th scope="col">Device OS</th>
                        <th scope="col">Login IP</th>
                        <th scope="col">Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $devices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $device): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <th scope="row"><a href="#" class="fw-semibold"><?php echo e($device->id); ?></a></th>
                            <td><small><?php echo e($device->location_name); ?></small></td>
                            <td><?php echo e($device->app_version); ?></td>
                            <td><?php echo e($device->device_name); ?></td>
                            <td> <?php echo e($device->device_version); ?></td>
                            <td><?php echo e($device?->ip_address); ?></td>
                            <td><?php echo e($device->created_at->format('d M Y h:i A')); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            </div>
        </div>
<?php endif; ?>
<?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/user/includes/component/_devices.blade.php ENDPATH**/ ?>