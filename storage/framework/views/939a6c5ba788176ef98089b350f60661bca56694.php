

<div class="table-responsive">
    <table id="buttons-datatables" class="table align-middle">
        <thead class="table-light">
            <tr>
                <th style="background-color: #f0e4dd!important;z-index:400"><small>Agent</small></th>
                <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $__currentLoopData = $value; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key2 => $value2): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($key2 == 'image'): ?>
                            <?php continue; ?>
                        <?php endif; ?>
                        <th style="background-color: #f0e4dd!important;z-index:400"> <small><?php echo e($key2); ?></small>
                        </th>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php break; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td style="background-color: rgb(242, 240, 240)!important">
                    <div class="d-flex align-items-center" title="<?php echo e($key); ?>">
                        <div class="flex-shrink-0 me-2">
                            <img src="<?php echo e($value['image'] ?? 'https://ui-avatars.com/api/' . $key); ?>" alt=""
                                class="avatar-xs rounded-circle">
                        </div>

                        <div class="flex-grow-1"><?php echo e($key); ?><p title="Created at" class="mb-0">
                            </p>
                        </div>
                    </div>
                </td>
                

                <?php $__currentLoopData = $value; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key2 => $value2): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($key2 == 'image'): ?>
                        <?php continue; ?>
                    <?php endif; ?>
                    <td><?php echo e($value2 == 0 ? '-' : $value2); ?></td>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>

    <tfoot>
        <tr>
            <th style="background-color: #f0e4dd!important;z-index:400">Agent</th>
            <?php $__currentLoopData = $value; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key2 => $value2): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($key2 == 'image'): ?>
                    <?php continue; ?>
                <?php endif; ?>
                <th class="showCount" style="background-color: #f0e4dd!important">0</th>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tr>
    </tfoot>

</table>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/reports/partials/_agents_events_stats.blade.php ENDPATH**/ ?>