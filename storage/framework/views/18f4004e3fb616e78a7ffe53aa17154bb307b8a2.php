<?php $__env->startSection('title'); ?>
    Dashboard
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('admin.dashboard.partials._calender_notification', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <div id="dashboardPage" class="v-application"></div>
    <?php echo $__env->make('admin.dashboard._annoucement_modals', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <!-- Load Vue App -->
    <script src="<?php echo e(asset('vue/js/dashboard.js')); ?>"></script>

    <script>
        $(document).ready(function() {
            // uncomment next line for testing. This will reset the acknowledgment status
            // localStorage.setItem('announcementApril01Acknowledged', 'false');
            // Get the current date
            const currentDate = new Date();
            const cutoffDate = new Date('2025-04-30'); // February 12, 2025

            // Check if the current date is before the cutoff date
            if (currentDate < cutoffDate) {
                // Check if the announcement has already been acknowledged
                if (localStorage.getItem('announcementApril01Acknowledged') === 'true') {
                    $('#annoucementModel').modal('hide');
                } else {
                    $('#annoucementModel').modal('show');
                }
            } else {
                // Do not show the modal if the date is February 3, 2025, or later
                $('#annoucementModel').modal('hide');
            }

            // Handle the acknowledgment button click
            $('#acknowledge-btn').on('click', function() {
                $('#annoucementModel').modal('hide');

                if ($('#acknowledged').is(':checked')) {
                    localStorage.setItem('announcementApril01Acknowledged', 'true');
                } else {
                    localStorage.setItem('announcementApril01Acknowledged', 'false');
                }
            });
        });
        $("#DashActive").addClass('active')
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/dashboard/v3/index.blade.php ENDPATH**/ ?>