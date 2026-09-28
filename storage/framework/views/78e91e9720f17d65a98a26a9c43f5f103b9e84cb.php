<?php if(session()->has('success')): ?>
    <uikit-alert type="success">
        <?php echo e(session()->get('success')); ?>

    </uikit-alert>
<?php endif; ?>
<?php if($errors->any()): ?>
    <uikit-alert type="danger">
        Please Correct the errors and try resubmitting.
    </uikit-alert>
<?php endif; ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/vendor/totem/partials/alerts.blade.php ENDPATH**/ ?>