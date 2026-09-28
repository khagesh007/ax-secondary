<?php $__env->startSection('title'); ?>
    Consolidated Reports
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

    <div id="reportPage" ></div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script src="<?php echo e(asset('vue/js/consolidatedReport.js')); ?>"></script>

    <script>
        $(document).ready(function() {

        });
        $("#reportsActive").addClass('active')
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/reports/consolidateReport/index.blade.php ENDPATH**/ ?>