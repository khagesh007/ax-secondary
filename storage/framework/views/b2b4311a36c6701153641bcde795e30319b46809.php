<?php $__env->startSection('title'); ?>
    Pipeline
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

    <div id="pipelinePage" ></div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <!-- Load Vue App -->
    <script src="<?php echo e(asset('vue/js/pipeline.js')); ?>"></script>

    <script>
        $(document).ready(function() {

        });
        $("#sidebarPipelines").addClass('active')
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/pipeline/index.blade.php ENDPATH**/ ?>