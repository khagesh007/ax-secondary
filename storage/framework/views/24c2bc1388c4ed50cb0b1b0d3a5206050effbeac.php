<div class="card own_property d-none" id="portalsTab">
    <div class="card-header p-05rem d-flex justify-content-between align-items-center">
        <h6 class="card-title mb-0">Portals</h6>

        <a href="javascript:void(0);" class="" id="viewPortalLogPage">
            <i class="mdi mdi-bell" style="color: #d3a188;"></i>
        </a>
    </div>

    <div class="card-body form-steps">
        <?php $__currentLoopData = $portals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $portal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="form-check form-switch form-switch-success accordion accordion-flush">
                <input class="form-check-input inputs portal-switchs portalBtns " type="checkbox" name="portals[]"
                    value="<?php echo e($portal->id); ?>" role="switch" id="portal-switch-<?php echo e($portal->id); ?>">
                <label class="form-check-label2" for="portal-switch-<?php echo e($portal->id); ?>">
                    <img src="<?php echo e($portal->thumbnail); ?>" alt=""
                        style="height: 20px; width: 20px">
                    <?php echo e($portal->name); ?>

                </label>

                <button class="accordion-button collapsed metaDataBtn "
                    id="metaDataBtn-<?php echo e($portal->id); ?>" type="button" data-bs-toggle="collapse"
                    data-bs-target="#<?php echo e($portal->slug); ?>-container"
                    data-portal-name="<?php echo e($portal->name); ?>" data-property-id=""
                    data-portal-slug="<?php echo e($portal->slug); ?>" aria-expanded="true"
                    aria-controls="flush-collapseOne"
                    style="width: 40px;float: right; padding: 2px 14px 5px 9px;"
                    ></button>

                <div id="<?php echo e($portal->slug); ?>-container"
                    class="accordion-collapse meta-data-accordion collapse">

                </div>

            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/listing/includes/_portals.blade.php ENDPATH**/ ?>