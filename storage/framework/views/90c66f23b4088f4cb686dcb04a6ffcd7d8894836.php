<!doctype html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" data-topbar="light">

<head>
    <meta charset="utf-8" />
    <title><?php echo $__env->yieldContent('title'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="<?php echo $__env->yieldContent('meta_description','AX Secondary CRM - Login'); ?>" name="description" />
    <meta content="Themesbrand" name="author" />
   
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="<?php echo e(URL::asset('assets/images/logo/favicon-32x32.png')); ?>">
    <?php echo $__env->make('layouts.head-css', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <style type="text/css">
        .auth-one-bg .bg-overlay {
            background: linear-gradient(90deg, #000000de, #92572261);
            opacity: .2;
        }

        .auth-one-bg {
            background-image: url(<?php echo e(URL::asset('assets/images/bg_numbers.jpeg')); ?>);
            background-position: top center;
            background-size: cover;
            background-size: cover;
        }

        .auth-one-bg-position {
            height: 480px;

        }

        .pt-8 {
            padding-top: 8rem !important;
        }

        .btn-success {
            background-color: #d3a188;
            border-color: #d3a188;
            color: #333;
        }

        .btn-success:hover {
            background-color: #333;
            border-color: #333;
            color: #d3a188;
        }
    </style>

    
</head>

<?php echo $__env->yieldContent('body'); ?>

<?php echo $__env->yieldContent('content'); ?>

<?php echo $__env->make('layouts.vendor-scripts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</body>

</html>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/layouts/master-without-nav.blade.php ENDPATH**/ ?>