


<div class="profile-timeline">
    <div class="accordion accordion-flush scrollable-notes" id="accordionFlushExample">

        <?php if($cyclesGrouped->count() > 0): ?>
        <?php $__currentLoopData = $cyclesGrouped; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $refNo => $cycles): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <span class="p-1 border badge badge-warning text-warning rounded-pill fs-15"><?php echo e($refNo); ?></span>
            <?php $__currentLoopData = $cycles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $cycle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                <div class="accordion-item border-0">
                    <div class="accordion-header" id="headingOne">
                        <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse"
                            href="#collapseOne_<?php echo e($index); ?>" aria-expanded="true"
                            aria-controls="collapseOne_<?php echo e($index); ?>">
                            <div class="d-flex align-items-center">
                                <?php echo statusUi($cycle->status); ?>

                                <div class="flex-grow-1 ms-3">
                                    <h6 class=" mb-0 fw-semibold"><?php echo e($cycle->status); ?> <span class="fw-normal"
                                            title="<?php echo e($cycle->created_at); ?>">
                                            <?php echo e(\Carbon\Carbon::parse($cycle->created_at)->diffForHumans()); ?>

                                        </span></h6>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div id="collapseOne_<?php echo e($index); ?>"
                        class="accordion-collapse collapse <?php echo e($loop->last ? 'show' : ''); ?>" aria-labelledby="headingOne"
                        data-bs-parent="#accordionExample">
                        <div class="accordion-body ms-1 ps-5 pt-0">

                            <?php
                                $previousCycle = $index > 0 ? $cycles[$index - 1] : null;
                            ?>


                            <?php if($index == 0): ?>
                                <p class="mb-1"> <?php echo e($cycle?->createdBy?->full_name??'SYSTEM'); ?> changed status to
                                    <b><?php echo e($cycle->status); ?></b>.
                                <h6>
                                <?php else: ?>
                                    <p class="mb-1"> <?php echo e($cycle?->createdBy?->full_name ??'SYSTEM'); ?> changed status to
                                        <b><?php echo e($cycle->status); ?></b> from
                                        <b><?php echo e(optional($previousCycle)->status ?? ''); ?></b>.  <small><?php echo e(date('d-M-y',strtotime($cycle->created_at))); ?></small>
                                    </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
            <?php echo $__env->make('admin.listing.includes.timeline._empty_timeline', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php endif; ?>



    </div>

</div>





<script>
    var objDiv = document.getElementById("accordionFlushExample");
    objDiv.scrollTop = objDiv.scrollHeight;
</script>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/listing/includes/partials/_timeline.blade.php ENDPATH**/ ?>