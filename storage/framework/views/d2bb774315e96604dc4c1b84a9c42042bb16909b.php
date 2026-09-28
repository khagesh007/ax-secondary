<?php $__env->startSection('title'); ?>
    File Manager | Documents Library
<?php $__env->stopSection(); ?>
<?php $__env->startSection('style'); ?>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" type="text/css" />
    
    <link href="<?php echo e(URL::asset('assets/libs/dropzone/dropzone.min.css')); ?>" rel="stylesheet">
  <link href="<?php echo e(asset('vue/css/app.css')); ?>">
   <style>
       @media (min-width: 992px)
       .file-manager-sidebar {
           min-width: 300px;
           max-width: 300px;
           height: calc(100vh - 70px - 60px - 8px);
       }

       .file-manager-sidebar {
           position: relative;
           background-color: #fff;
       }
       .file-manager-content {
           background-color: #fff;
       }
       .search-box {
           position: relative;
       }
       .file-detail-show .file-manager-detail-content {
           display: block;
       }

       .file-manager-detail-content {
           width: 32%;
           max-width: 100%;
           background-color: #fff;
           display: none;
       }
       .file-manager-menu li .sub-menu li::before {
           content: "\f0374";
           position: absolute;
           left: 0;
           font-family: Material Design Icons;
           color: rgba(var(--vz-dark-rgb),.4);
       }
       .file-manager-menu li a.active, .file-manager-menu li a:hover, .file-manager-menu li a[aria-expanded=true] {
           color: #0ab39c;
       }

       .file-manager-menu li a {
           color: var(--vz-body-color);
           font-weight: 500;
           -webkit-transition: all .5s ease;
           transition: all .5s ease;
       }
       .file-manager-menu li .sub-menu li {
           padding-left: 25px;
           position: relative;
       }

       .file-manager-menu li {
           padding: 5px 0;
       }
       a, button {
           outline: 0!important;
           position: relative;
       }
       .dropdown, .dropdown-center, .dropend, .dropstart, .dropup, .dropup-center {
           position: relative;
       }
       .dropdown-menu {
           -webkit-box-shadow: 0 5px 10px rgba(30,32,37,.12);
           box-shadow: 0 5px 10px rgba(30,32,37,.12);
           -webkit-animation-name: DropDownSlide;
           animation-name: DropDownSlide;
           -webkit-animation-duration: .3s;
           animation-duration: .3s;
           -webkit-animation-fill-mode: both;
           animation-fill-mode: both;
           position: absolute;
           z-index: 1000;
       }
       .dropdown .btn {
           box-shadow: none;
       }

   </style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div id="fileManager">
        <Documents />
    </div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
<?php echo $__env->make('admin.partials.datatable', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
       <script>
        let documentsPath = "<?php echo e(asset('document-templates')); ?>";
       </script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script> -->
    <script src="<?php echo e(asset('vue/js/vue.js')); ?>"></script>
    <script>
        $("#documentLibrary").addClass('active');
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/documents/index.blade.php ENDPATH**/ ?>