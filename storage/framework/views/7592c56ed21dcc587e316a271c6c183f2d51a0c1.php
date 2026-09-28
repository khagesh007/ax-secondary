<?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="row mt-2 currentbtn mb-3">
        <div class="col-3 col-md-3">
            <span class="language_name"><?php echo e($i->meta_key); ?></span>
        </div>
        <div class="col-7 col-md-7">
            <input type="hidden" name="language_name[]" value="<?php echo e($i->meta_key); ?>" class="myLanguages">
            <input type="range" step="10" name="language_level[]" class="language_level ranger" id="language_level_<?php echo e($i->meta_key); ?>" meta-key="<?php echo e($i->meta_key); ?>" value="<?php echo e($i->meta_value); ?>"> <span class="level_value_text"><?php echo e($i->meta_value); ?></span> %
        </div>
        <div class="col-2 col-md-2">
            <i class="bx bx-trash text-danger deleteBtn cursur-pointer" data-id="<?php echo e($i->id); ?>"></i>
        </div>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>




<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/user/includes/component/_languages.blade.php ENDPATH**/ ?>