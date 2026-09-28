<?php $__env->startSection('title'); ?>
<?php echo app('translator')->get('translation.404-basic'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('body'); ?>
<body>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
        <div class="auth-page-wrapper pt-5">
            <!-- auth page bg -->
            <div class="auth-one-bg-position auth-one-bg"  id="auth-particles">
                <div class="bg-overlay"></div>

                <div class="shape">
                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 1440 120">
                        <path d="M 0,36 C 144,53.6 432,123.2 720,124 C 1008,124.8 1296,56.8 1440,40L1440 140L0 140z"></path>
                    </svg>
                </div>
            </div>

            <!-- auth page content -->
            <div class="auth-page-content">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="text-center pt-4">
                                <div class="">
                                    <img src="<?php echo e(URL::asset('assets/images/error.svg')); ?>" alt="" class="error-basic-img move-animation">
                                </div>
                                <div class="mt-n4">
                                    <h1 class="display-1 fw-medium">401</h1>
                                    <h3 class="text-uppercase">Sorry, Page not Found 😭</h3>
                                    <p class="text-muted mb-4">The page you are looking for not available!</p>
                                    <a href="index" class="btn btn-success"><i class="mdi mdi-home me-1"></i>Back to home</a>
                                </div>
                            </div>
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
                                <?php echo $__env->make('errors.partials._signature', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>                            </div>
                        </div>
                    </div>
                </div>
            </footer>
            <!-- end Footer -->

        </div>
        <!-- end auth-page-wrapper -->

<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
<!-- particles js -->
<script src="<?php echo e(URL::asset('assets/libs/particles.js/particles.js.min.js')); ?>"></script>
<!-- particles app js -->
<script src="<?php echo e(URL::asset('assets/js/pages/particles.app.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master-without-nav', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/errors/401.blade.php ENDPATH**/ ?>