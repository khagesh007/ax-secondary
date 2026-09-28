<?php $__env->startSection('title'); ?>
    Update PF Token
<?php $__env->stopSection(); ?>
<?php $__env->startSection('meta_description', 'Lead Generation Form'); ?>
<?php $__env->startSection('css'); ?>
    <link href="<?php echo e(URL::asset('assets/libs/sweetalert2/sweetalert2.min.css')); ?>" rel="stylesheet" type="text/css" />
    <!--datatable responsive css-->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet"
        type="text/css" />
    <link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
    <style type="text/css">
        div.dataTables_wrapper div.dataTables_filter input {
            border-color: var(--vz-input-border);
            background-color: var(--vz-input-bg);
            color: var(--vz-body-color);
            line-height: 1.5;
            padding: 0.5rem 0.9rem 0.5rem 1.025rem;
            border-radius: 0.25rem;
            font-size: .8125rem;
        }
    </style>

    
    <link rel="stylesheet" href="<?php echo e(asset('assets/libs/intl-tel-input/css/intlTelInput.min.css')); ?>">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"
        type="text/css" />
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('admin.partials.loader', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <div class="auth-page-wrapper pt-2">
        <!-- auth page bg -->
        <div class="auth-one-bg-position auth-one-bg" id="auth-particles">
            <div class="bg-overlay"></div>

            <div class="shape">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink"
                    viewBox="0 0 1440 120">
                    <path d="M 0,36 C 144,53.6 432,123.2 720,124 C 1008,124.8 1296,56.8 1440,40L1440 140L0 140z"></path>
                </svg>
            </div>
        </div>

        <!-- auth page content -->
        <div class="auth-page-content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center mt-2">
                            <img src="<?php echo e(asset('assets/images/logo/ax-short-logo.png')); ?>" alt="">
                        </div>

                    </div>
                </div>
                <!-- end row -->

                <div class="row  justify-content-center formDiv">
                    <div class="col-md-8">
                        <div class="card mt-4">
                            <div class="card-body p-4">
                                <div class="text-center mt-2">
                                </div>
                                <div class="p-2 mt-4 ">
                                    <div class="row">
                                        <div class="col-md-12 text-center text-primary">
                                            <h1>PF Token Update</h1>
                                        </div>
                                    </div>
                                    <div class="row mt-3">

                                        <div class="col-md-12 mt-4">
                                            <label for="">PF</label>
                                            <textarea name="token" id="token" cols="30" rows="10" class="form-control"><?php echo e($pf_token->value); ?></textarea>
                                        </div>
                                    </div>


                                    <div class="row mt-3">
                                        <button class="btn btn-primary" id="update-token" type="button">
                                            Update
                                        </button>
                                    </div>
                                </div>
                                <!-- end card body -->
                            </div>
                            <!-- end card -->
                        </div>
                    </div>
                    <!-- end row -->
                </div>
                <!-- end container -->
            </div>
            <!-- end auth page content -->

            <!-- footer -->
            <footer class="footer">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="text-center">
                                <p class="mb-0 text-muted">&copy;
                                    <script>
                                        document.write(new Date().getFullYear())
                                    </script> <?php echo e(getApp('name')); ?>

                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
            <!-- end Footer -->
        </div>
    <?php $__env->stopSection(); ?>
    <?php $__env->startSection('script'); ?>
        <script src="<?php echo e(URL::asset('assets/libs/particles.js/particles.js.min.js')); ?>"></script>
        <script src="<?php echo e(URL::asset('assets/js/pages/particles.app.js')); ?>"></script>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <script src="<?php echo e(URL::asset('/assets/js/app.min.js')); ?>"></script>
        <script src="<?php echo e(asset('assets/js/helpers.js')); ?>"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <script>

$(document).on("click", "#update-token", function() {
    let token = $('#token').val();


    // Include the CSRF token
    let csrfToken = $('meta[name="csrf-token"]').attr('content');

    $.ajax({
        url: "<?php echo e(route('pf.token.update')); ?>",
        type: 'POST',
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': csrfToken // Add CSRF token to the headers
        },
        data: {
            token,
        },
        success: function(result) {
            if (result.status == '1') {
                showToastify(result.msg, "success");
            } else {
                showToastify(result.msg, "error");
            }
        },
        error: function(result) {
            console.log(result);
        }
    });
});




        </script>
    <?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master-without-nav', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/token_update/pf.blade.php ENDPATH**/ ?>