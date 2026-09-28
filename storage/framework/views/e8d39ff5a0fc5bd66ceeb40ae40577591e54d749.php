<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" id="html" data-layout="" data-topbar=""
    data-preloader="enable" data-sidebar="" data-sidebar-size="" data-sidebar-image="" data-layout-style = ""
    data-layout-mode = "" data-layout-width = "">

<head>
    <meta charset="utf-8" />
    <title><?php echo $__env->yieldContent('title'); ?>| <?php echo e(env('APP_NAME')); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>" />
    <meta content="" name="description" />
    <meta content="" name="author" />
    <link rel="shortcut icon" href="<?php echo e(URL::asset('assets/images/logo/favicon-32x32.png')); ?>">
    <?php echo $__env->make('admin.layouts.head-css', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <script>
        var dataLayout = localStorage.getItem('data-layout') || 'vertical';
        var dataTopbar = localStorage.getItem('data-topbar') || 'light';
        var dataSidebar = localStorage.getItem('data-sidebar') || 'dark';
        var dataSidebarSize = localStorage.getItem('data-sidebar-size') || 'lg';
        var dataSidebarImage = localStorage.getItem('data-sidebar-image') || 'none';
        var dataLayoutStyle = localStorage.getItem('data-layout-style') || 'default';
        var dataLayoutMode = localStorage.getItem('data-layout-mode') || 'light';
        var dataLayoutWidth = localStorage.getItem('data-layout-width') || 'fluid';
        var dataLayoutPosition = localStorage.getItem('data-layout-position') || 'fixed';
        document.getElementById('html').setAttribute('data-layout', dataLayout);
        document.getElementById('html').setAttribute('data-topbar', dataTopbar);
        document.getElementById('html').setAttribute('data-sidebar', dataSidebar);
        document.getElementById('html').setAttribute('data-sidebar-size', dataSidebarSize);
        document.getElementById('html').setAttribute('data-sidebar-image', dataSidebarImage);
        document.getElementById('html').setAttribute('data-layout-style', dataLayoutStyle);
        document.getElementById('html').setAttribute('data-layout-mode', dataLayoutMode);
        document.getElementById('html').setAttribute('data-layout-width', dataLayoutWidth);
        document.getElementById('html').setAttribute('data-layout-position', dataLayoutPosition);
    </script>

    <style>
        .localEnv {
            background: #424141 !important;
        }
    </style>
    <?php echo $__env->yieldContent('styles'); ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        var userDetailsRoute = "<?php echo e(route('ajax.getUser')); ?>";
    </script>
</head>



<?php $__env->startSection('body'); ?>

    <?php echo $__env->make('admin.layouts.body', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->yieldSection(); ?>
<!-- Begin page -->


<div id="layout-wrapper">

    <?php echo $__env->make('admin.layouts.topbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('admin.layouts.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <!-- ============================================================== -->
    <!-- Start right Content here -->
    <!-- ============================================================== -->
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid " id="normal-account">
                <?php echo $__env->yieldContent('content'); ?>



            </div>

            <div class="d-none container-fluid m-auto pt-5" id="banned-account">

                <div class="row">
                    <div class="col-md-6 offset-md-3">
                        <div class="card">
                            <div class="card-body p-5 text-center" style="border: 2px solid #f06547">
                                <h5 class="">
                                    Please action your New leads in order to access the other modules.
                                    
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- container-fluid -->
        </div>
        <!-- End Page-content -->

        <?php echo $__env->make('admin.layouts.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    </div>
    <!-- end main content-->
</div>
<!-- END layout-wrapper -->



<?php echo $__env->make('admin.layouts.global-search', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


<?php echo $__env->make('admin.layouts.customizer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<!-- JAVASCRIPT -->
<?php echo $__env->make('admin.layouts.vendor-scripts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php echo $__env->yieldContent('global_scripts'); ?>
<?php echo $__env->yieldPushContent('agent_filter'); ?>

<script>
    $(document).on("change", "#dashboard-selector", function(e) {
        let url = $(this).find(':selected').data('url');
        if (url) {
            window.location.href = url;
        }

    })
</script>
<script>
    getLeadStatus();
    // setInterval(function() {
    //     getLeadStatus();
    // }, 60000);


    function showCountdown() {
        const now = new Date();
        const targetTime = new Date();
        targetTime.setHours(12, 30, 0, 0); // 9:30 PM
        const timeDiff = targetTime - now;
        if (timeDiff <= 0) {
            $('#liveTime').text('The time has passed.');
        } else {
            const hours = Math.floor(timeDiff / 3600000);
            const minutes = Math.floor((timeDiff % 3600000) / 60000);
            const seconds = Math.floor((timeDiff % 60000) / 1000);
            // make them 2 digit
            const hoursStr = hours.toString().padStart(2, '0');
            const minutesStr = minutes.toString().padStart(2, '0');
            const secondsStr = seconds.toString().padStart(2, '0');

            $('#liveTime').text(`${hoursStr}:${minutesStr}:${secondsStr}`);
        }
    }


    function runCountdown() {
        showCountdown();
        setInterval(showCountdown, 1000);
    }

    function getLeadStatus() {
        $.ajax({
            url: "<?php echo e(route('agentDailyReport')); ?>",
            type: "POST",
            data: {
                _token: "<?php echo e(csrf_token()); ?>",
                user_id: "<?php echo e(auth()->user()->id); ?>"
            },
            success: function(response) {
                if (response.status == 1) {
                    runCountdown();
                    $('#agentReportStatusBar').html(response.msg);
                    $('#agentReportStatus').removeClass('d-none');
                    timeBasedDisabler();
                }
                if (response.status == 0) {
                    $('#agentReportStatus').addClass('d-none');
                    timeBasedEnabler();
                }

            }
        });
    }


    function timeBasedDisabler() {
        const now = moment(); // Current date and time

        const targetTime = moment({
            year: now.year(),
            month: now.month(),
            date: now.date(),
            hour: 9,
            minute: 30,
            second: 0,
        });

        const timeDiff = targetTime - now;

        if (timeDiff <= 0) {
            $('#scrollbar .nav-link, .nav-item').addClass('disabled text-danger');
            $('.rm-disabled').removeClass('disabled text-danger');

            const currentURL = window.location.href;
            const baseURL = window.location.origin + '/';

            if (currentURL.includes("work/leads")) {} else if (currentURL == baseURL) {} else {
                $('#normal-account').addClass('d-none');
                $('#banned-account').removeClass('d-none');
            }

        }

    }

    function timeBasedEnabler() {
        const now = moment(); // Current date and time

        const targetTime = moment({
            year: now.year(),
            month: now.month(),
            date: now.date(),
            hour: 9,
            minute: 30,
            second: 0,
        });

        const timeDiff = targetTime - now;


        $('#scrollbar .nav-link, .nav-item').removeClass('disabled text-danger');
        $('.rm-disabled').removeClass('disabled text-danger');

        const currentURL = window.location.href;
        const baseURL = window.location.origin + '/';

        $('#normal-account').removeClass('d-none');
        $('#banned-account').addClass('d-none');



    }

    // $(document).ready(function() {
        var url = window.location.href;
        if (url.includes("portal")) {
            let portal = url.split('portal=')[1];
            if (portal == 'AX-CONTACTS-LOGIN') {
                $('#ax-contacts-login')[0].click();
                // $('#ax-contacts-login').trigger('click');
            } else if (portal == 'AX-CHATS-LOGIN') {
                $('#ax-chats-login')[0].click();

            } else if (portal == 'AX-TELELOGS-LOGIN') {
                $('#ax-telelogs-login')[0].click();

            }
        } else {

        }
    // });
</script>

</body>

</html>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/layouts/master.blade.php ENDPATH**/ ?>