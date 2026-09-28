<?php $__env->startSection('title'); ?>
    Agent Dashboard
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('admin.dashboard.partials._calender_notification', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <div id="agentDashboardPage" class="v-application"></div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <!-- Load Vue App -->
    <script src="<?php echo e(asset('vue/js/agentDashboard.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/dashboard/v3/agent.blade.php ENDPATH**/ ?>