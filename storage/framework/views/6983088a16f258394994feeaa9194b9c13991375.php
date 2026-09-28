<?php if(isset($layout) && $layout == 'horizontal'): ?>
<div class="col-4 mt-2"><?php echo e($label); ?><?php echo e($required=='true'?'*':''); ?></div>
<div class="col-8 mt-2">
    <select name='<?php echo e($id); ?>' id="<?php echo e($id); ?>"
        class="form-control select2 inputs" <?php echo e($required=='true'?'required':''); ?>>
        <option selected disabled value=""><?php echo e($label); ?></option>
        </option>
        <?php $__currentLoopData = $agents ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option data-image="<?php echo e($agent->thumbnail); ?>" data-status="<?php echo e($agent->status); ?>" value="<?php echo e($agent->id); ?>">
            <?php echo e($agent->first_name); ?>

            <?php echo e($agent->last_name); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <div class="invalid-feedback">
        Please choose <?php echo e($label); ?>.
    </div>
</div>
<?php else: ?>
<div class="col-md-12">
    <label for="<?php echo e($id); ?>" class="form-label"><?php echo e($label); ?><?php echo e($required=='true'?'*':''); ?></label>
    <select name='<?php echo e($id); ?>' id="<?php echo e($id); ?>" class="form-control select2 inputs" <?php echo e($required=='true'?'required':''); ?>>
        <option selected disabled value=""><?php echo e($label); ?></option>
        <?php $__currentLoopData = $agents ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option data-image="<?php echo e($agent->thumbnail); ?>" data-status="<?php echo e($agent->status); ?>" value="<?php echo e($agent->id); ?>">
            <?php echo e($agent->first_name); ?>

            <?php echo e($agent->last_name); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <div class="invalid-feedback"> Please choose <?php echo e($label); ?>. </div>
</div>
<?php endif; ?>

<?php $__env->startPush('script-bottom'); ?>
<script>
    if ($('#<?php echo e($id); ?>').hasClass("select2")) {
        $('#<?php echo e($id); ?>').select2({
            placeholder: '<?php echo e($label); ?>',
            allowClear: true,
            focusOnOpen: false,
            templateResult: formatAgent,
            templateSelection: formatAgent,
        });
    }
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/templates/agents_select2_form.blade.php ENDPATH**/ ?>