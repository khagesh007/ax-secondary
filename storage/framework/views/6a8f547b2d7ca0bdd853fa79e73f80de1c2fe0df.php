<?php $__env->startSection('styles'); ?>
    <link href="https://fonts.googleapis.com/css?family=Karla:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.materialdesignicons.com/4.8.95/css/materialdesignicons.min.css">
    
    <link rel="stylesheet" href="<?php echo e(asset('assets/deal-wizard/assets/css/bd-wizard.css')); ?>">
    <style>
        main{
            font-family: 'Karla', sans-serif;
        }
        .content{
            font-family: 'Karla', sans-serif !important;
        }
        .page-content {
            padding: calc(21px + 1.5rem) .75rem 60px !important;
        }
        .modal-backdrop.show:nth-of-type(even) {
            z-index: 1049 !important;
        }
        .media{
            display: flex;
        }
        body{
            font-size: 1.0rem;
        }

    </style>
    <title>Leads</title>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <!-- Default Modals -->
    <div class="mt-5"></div>
    <button type="button" class="btn btn-primary " id="listingDealCreateModal">Standard Modal</button>
    <input type="text" id="id" value="12009">
    <input type="text" id="owner_id" value="13783">




    <table class="table">
        <tbody>
        <tr>
            <th>#</th>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
        </tr>
        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($loop->iteration); ?></td>
             
                <td><?php echo e($user->id); ?></td>
                <td><?php echo e($user->name); ?></td>
                <td><?php echo e($user->email); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('global_scripts'); ?>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <script src="<?php echo e(asset('assets/deal-wizard/assets/js/jquery.steps.min.js')); ?>"></script>
    <script src="<?php echo e(asset('vue/js/vueWidgets.js')); ?>"></script>



<?php $__env->stopSection(); ?>


<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/test.blade.php ENDPATH**/ ?>