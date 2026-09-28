<div data-simplebar id="scrolltobottom" class="scrolltobottom scrollable-notes px-3 mb-2 ">
    <?php if($notes->count() > 0): ?>
        <?php $__currentLoopData = $notes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            
            <div class="d-flex mb-0">
                <div class="flex-shrink-0">
                    <img src="<?php echo e($item?->createdBy?->thumb_url ?? URL::asset('assets/images/users/multi-user.jpg')); ?>"
                        alt="" class="avatar-xs rounded-circle" />
                </div>
                <div class="flex-grow-1 ms-3">
                    <h5 class="fs-13"><a
                            href="javascript:void(0)"><?php echo e($item->createdBy ? $item->createdBy?->full_name : 'System'); ?></a>
                        <small
                            class="text-muted"><?php echo e(\Carbon\Carbon::parse($item->created_at)->diffForHumans()); ?></small>
                    </h5>
                    <?php if(auth()->user()->hasRole('Agent')): ?>
                        <?php if($item->lead && $item->lead?->agent_id == null): ?>
                            <p class="text-muted"><?php echo replaceNumbersWithAsterisks($item->note); ?></p>
                        <?php else: ?>
                            <p class="text-muted"><?php echo $item->note; ?> </p>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="text-muted"><?php echo $item->note; ?> </p>
                    <?php endif; ?>

                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php else: ?>
        <?php echo $__env->make('admin.lead.include.partials.notes._empty_notes', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>
</div>


<script>
    // var objDiv3 = document.getElementById("scrollable-notes");
    // objDiv3.scrollTop = objDiv3.scrollHeight;
</script>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/lead/include/partials/notes/_notes_log.blade.php ENDPATH**/ ?>