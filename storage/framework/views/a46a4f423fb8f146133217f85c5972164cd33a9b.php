<?php $__env->startSection('title'); ?>
   KPI Dashboard
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

    <div id="kpi" class="v-application"></div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <!-- Load Vue App -->
    <script>
        const team_refresh_url = "<?php echo e(route('reports.group.kpi-v2.kpi.team.refresh.report')); ?>?month=";
        const agent_refresh_url = "<?php echo e(route('reports.group.kpi-v2.kpi.agent.refresh.report')); ?>?month=";
        const team_kpi_setup_url =  "<?php echo e(route('reports.team.kpi-v2.team.setup')); ?>";
        const agent_kpi_setup_url =  "<?php echo e(route('reports.group.kpi-v2.setup')); ?>";
        $("#kpiReportSidebar").addClass('active')
    </script>
    <script src="<?php echo e(asset('vue/js/kpi.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/kpi_v3/index.blade.php ENDPATH**/ ?>