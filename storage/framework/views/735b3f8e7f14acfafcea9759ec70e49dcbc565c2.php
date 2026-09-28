<div class="modal zoomIn" id="userProfileInformationModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-soft-danger">
                <h5 class="modal-title" id="userProfileInformation"><?php echo e($user->name); ?> </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                    id="close-modal"></button>
            </div>
            <div class="modal-body p-2 text-center">
                <div class="card" id="contact-view-detail">
                    
                                <div class="card-body text-center">
                                    <div class="position-relative d-inline-block">
                                        <a data-lightbox="user-avatar_<?php echo e($user->id); ?>" href="<?php echo e($user->image_url); ?>">

                                            <img src="<?php echo e($user->thumb_url); ?>" alt="" class="avatar-10rem rounded-circle" data-lightbox="<?php echo e($user->image_url); ?>">
                                        </a>
                                        <span class="contact-active position-absolute rounded-circle bg-success"><span class="visually-hidden"></span>
                                    </span></div>
                                    <h5 class="mt-4 mb-1"><?php echo e($user->name); ?></h5>
                                    <p class="text-muted"><?php echo e($user->roles->first()->name); ?></p>

                                    <ul class="list-inline mb-0">
                                        <li class="list-inline-item avatar-xs">
                                            <a href="tel:<?php echo e($user->mobile); ?>" class="avatar-title bg-soft-success text-success fs-15 rounded">
                                                <i class="ri-phone-line"></i>
                                            </a>
                                        </li>
                                        <li class="list-inline-item avatar-xs">
                                            <a href="mailto:<?php echo e($user->email); ?>" class="avatar-title bg-soft-danger text-danger fs-15 rounded">
                                                <i class="ri-mail-line"></i>
                                            </a>
                                        </li>
                                        <li class="list-inline-item avatar-xs">
                                            <a href="https://wa.me/<?php echo e($user->mobile); ?>" class="avatar-title bg-soft-warning text-warning fs-15 rounded" target="_blank">
                                                <i class="ri-whatsapp-line"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <?php if($user->bio): ?>
                                    <h6 class="text-muted text-uppercase fw-semibold mb-3">Personal Information</h6>
                                    <p class="text-muted mb-4" style="text-align: justify;"><?php echo e($user->bio); ?></p>
                                    <?php endif; ?>
                                    <div class="table-responsive table-card">
                                        <table class="table table-borderless mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="fw-medium" scope="row">Designation</td>
                                                    <td><?php echo e($user->position); ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-medium" scope="row">Email ID</td>
                                                    <td><a href="mailto:<?php echo e($user->email); ?>"><?php echo e($user->email); ?></a></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-medium" scope="row">Mobile No</td>
                                                    <td><a href="tel:<?php echo e($user->mobile); ?>" class=""><?php echo e($user->mobile); ?></a></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
            </div>
             <div class="modal-footer">
                <button type="button" class="btn btn-light action-close" data-bs-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/partials/user-profile.blade.php ENDPATH**/ ?>