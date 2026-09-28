<?php $__env->startSection('title'); ?>
    General Groups
<?php $__env->stopSection(); ?>
<?php $__env->startSection('style'); ?>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" type="text/css" />

    <style>
        .center-tabs {
            display: flex;
            justify-content: center;
        }
    </style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title mb-0 ">General Groups</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Settings</a></li>
                            <li class="breadcrumb-item active">Groups</li>
                        </ol>
                    </div>
                    <div class="flex-shrink-0">
                        <button type="button" class="btn btn-primary" id="reset-groups">Reset Groups</button>
                        <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#myModal">Add
                            new</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row d-none">
        <div class="col-lg-12">

            <div class="card">
                <div class="card-body">
                    <table id="sourceDatatable" class="table align-middle" style="width-100%">
                        <thead class="">
                        <tr class="text-uppercase table-light2">
                            <th scope="col" style="width-50px;"> ID </th>
                            <th>Name</th>
                            <th>Group Admin</th>
                            <th>Parent Group</th>
                            <th>Count</th>

                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody class="form-check-all">
                        <?php $__currentLoopData = $data_filtered; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($i->id); ?></td>
                                <td><?php echo e($i->name); ?></td>
                                <td>
                                    <div class="d-flex align-items-center userDetails"
                                         data-agent-id="<?php echo e($i->user->id); ?>" title="<?php echo e($i->user->first_name); ?>">
                                        <div class="flex-shrink-0 me-2">
                                            <img src="<?php echo e($i->user->thumb_url); ?>" alt=""
                                                 class="avatar-xs rounded-circle">
                                        </div>
                                        <div class="flex-grow-1"><?php echo e($i->user->first_name); ?><p title="Created at"
                                                                                              class="mb-0"></p>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo e($i->parent ? $i->parent->name : '--'); ?></td>
                                <td><?php echo e($i->child_count ? $i->child_count : '--'); ?></td>
                                <td>Edit</td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div><!-- end card -->

            </div>
        </div>
    </div>

    <?php if(count($errors) > 0): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <strong>Oh! Shit, </strong>
                <?php echo e($error); ?>

            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
    <?php endif; ?>

    <?php $__currentLoopData = $data_filtered; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="row">
            <div class="col-xl-5">
                <div class="card" style="border:2px solid <?php echo e($group->color); ?>">
                    <div class="card-header">
                        <h6 class="card-title mb-0"><a
                                href="<?php echo e(route('admin.settings.group.edit', $group->id)); ?>"><?php echo e($group->name); ?> (<?php echo e($group->child_count ? $group->child_count : '--'); ?>)</a></h6>

                    </div>
                    <div class="card-body p-4 text-center">
                        <div class="mx-auto avatar-md mb-3">
                            <img src="<?php echo e($group->user->thumb_url); ?>" alt="" class="img-fluid rounded-circle">
                        </div>
                        <h5 class="card-title mb-1"><?php echo e($group->user->first_name); ?></h5>
                        <p class="text-muted mb-0"><?php echo e(countDirectChildren($group->user->id)); ?></p>
                    </div>
                    
                </div>
            </div>
            <?php if($group->children->count() > 0): ?>


                <div class="col-xl-7">
                    <div class="row">
                        <?php $__currentLoopData = $group->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-xl-4 delete-group-<?php echo e($child->id); ?>">
                                <div class="card" style="border:2px dotted <?php echo e($group->color); ?>">
                                    <div class="card-header">
                                        <button type="button" data-id="<?php echo e($child->id); ?>" id="delete-group-<?php echo e($child->id); ?>"
                                                class="btn-close float-end fs-11 delete-group"></button>
                                        <h6 class="card-title mb-0"><a
                                                href="<?php echo e(route('admin.settings.group.edit', $child->id)); ?>"><?php echo e($child->name); ?></a>
                                        </h6>
                                    </div>
                                    <div class="card-body p-4 text-center">
                                        <div class="mx-auto avatar-md mb-3">
                                            <img src="<?php echo e($child?->user?->thumb_url); ?>" alt=""
                                                 class="img-fluid rounded-circle">
                                        </div>
                                        <h5 class="card-title mb-1"><?php echo e($child?->user?->first_name); ?></h5>
                                        <p class="text-muted mb-0"><?php echo e(countDirectChildren($child?->user?->id)); ?></p>
                                    </div>

                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <hr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>




    <!-- Default Modals -->
    <div id="myModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Create Group</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <form action="<?php echo e(route('admin.settings.group.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-12">
                                <label for="">Group Name</label>
                                <input type="text" name="group_name" id="group_name" class="form-control group_name"
                                       placeholder="Group Name" required>
                            </div>

                            <div class="col-12 col-md-12 mt-2">
                                <label for="">Group Admin</label>

                                <select name="user_id" id="" required class="select2 form-control languageSelect2">
                                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($user->id); ?>"> <?php echo e($user->full_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <div class="col-12 col-md-12 mt-2">
                                <label for="">Parent Group</label>
                                <select name="parent_id" id="parent_id" class="select2 form-control languageSelect2">
                                    <option value="">Select Parent Group</option>
                                    <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($group->id); ?>"> <?php echo e($group->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <div class="col-12 col-md-12 mt-2">
                                <label for="">Group Color</label>
                                <input type="color" name="group_color" id="group_color" class="form-control group_color"
                                       placeholder="Group Name" required>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary ">Save</button>
                    </div>
                </form>

            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <?php echo $__env->make('admin.partials.datatable', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <script>
        let isUpdate = false;

        $(document).ready(function() {
            $('.select2').select2();
        });


        $(document).on('click', '.delete-group', function() {
            let id = $(this).data('id');
            Swal.fire({
                html: '<div class="mt-3"><div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you sure?</h4><p class="text-muted mx-4 mb-0">Are you sure you want to delete this group?</p></div></div>',
                showCancelButton: !0,
                confirmButtonClass: "btn btn-success w-xs me-2 mb-1",
                confirmButtonText: "Yes, I am sure.",
                cancelButtonClass: "btn btn-soft-danger w-xs mb-1",
                buttonsStyling: !1,
                showCloseButton: !0
            })
                .then(function(t) {
                    if (t.isConfirmed) {
                        $.ajax({
                            url: "<?php echo e(route('admin.settings.group.deleteGroup')); ?>",
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                id:id
                            },
                            success: function(result) {
                                if (result.status == '1') {
                                    $('.delete-group-' + id).remove();
                                    showToastify(result.message, "success")
                                } else {
                                    showToastify(result.message, "error")
                                }
                            },
                            error: function(result) {
                                console.table(result);
                            }
                        });
                    }
                })
        })


        $(document).on('click', '#reset-groups', function() {
            let id = $(this).data('id');
            Swal.fire({
                html: '<div class="mt-3"><div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you sure?</h4><p class="text-muted mx-4 mb-0">Are you sure you want to delete all groups?</p></div></div>',
                showCancelButton: !0,
                confirmButtonClass: "btn btn-success w-xs me-2 mb-1",
                confirmButtonText: "Yes, I am sure.",
                cancelButtonClass: "btn btn-soft-danger w-xs mb-1",
                buttonsStyling: !1,
                showCloseButton: !0
            })
                .then(function(t) {
                    if (t.isConfirmed) {
                        $.ajax({
                            url: "<?php echo e(route('admin.settings.group.deleteGroup.all')); ?>",
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                flag:'resetGroups'
                            },
                            success: function(result) {
                                alert(result.message)
                                location.reload();
                            },
                            error: function(result) {
                                console.table(result);
                            }
                        });
                    }
                })
        })



        $(document).on('click', '.btn-edit', function() {
            let id = $(this).data('id');
            let url = "<?php echo e(route('admin.settings.group.edit', ':id')); ?>";
            url = url.replace(':id', id);
            // Redirect to the generated URL
            window.location.href = url;
        });

        function generateRandomRGBColor() {
            const r = Math.floor(Math.random() * 256); // Generate a random value between 0 and 255 for red
            const g = Math.floor(Math.random() * 256); // Generate a random value between 0 and 255 for green
            const b = Math.floor(Math.random() * 256); // Generate a random value between 0 and 255 for blue

            // Create the RGB color string
            const rgbColor = `rgb(${r},${g},${b})`;

            return rgbColor;
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/groups/index.blade.php ENDPATH**/ ?>