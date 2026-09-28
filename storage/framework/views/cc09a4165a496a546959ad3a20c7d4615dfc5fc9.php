<?php $__env->startSection('title'); ?>
    Listing Verifications
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

    <div id="listing-verifications" class="v-application"></div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <!-- Load Vue App -->
    <script>
        $("#adminDashboardSidebar").addClass('active')
    </script>
    <script src="<?php echo e(asset('vue/js/listing-verifications.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/listing_verifications/index.blade.php ENDPATH**/ ?>