

<div class="col-4"><?php echo e($label); ?>*</div>
<div class="col-8">
    <select name='<?php echo e($id); ?>' id="<?php echo e($id); ?>" class="form-control select2">
        <option value="" selected disabled><?php echo e($label); ?></option>

        <?php $__currentLoopData = $agents ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option data-image="<?php echo e($agent->thumbnail); ?>" data-status="<?php echo e($agent->status); ?>" value="<?php echo e($agent->id); ?>">
            <?php echo e($agent->first_name); ?>

            <?php echo e($agent->last_name); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <div class="valid-feedback">
        Please Select any <?php echo e($label); ?>.
    </div>
</div>




<?php $__env->startPush('script-bottom'); ?>
<script>

    if ($('#<?php echo e($id); ?>').hasClass("select2")) {
        $('#<?php echo e($id); ?>').select2({
            placeholder: '<?php echo e($label); ?>',
            allowClear: true,
            focusOnOpen: true,
            templateResult: formatAgent,
            templateSelection: formatAgent,
            dropdownParent: $('#generalModal')
        });
    }

</script>
<?php $__env->stopPush(); ?>

<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/templates/_agents_dynamic_select2.blade.php ENDPATH**/ ?>