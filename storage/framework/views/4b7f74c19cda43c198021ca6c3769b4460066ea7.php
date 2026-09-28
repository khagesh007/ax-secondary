<div class="accordion-body" style="padding:0px;">
    <div data-simplebar>
        
        <!-- Small Tables -->
        <table class="table table-sm table-nowrap">
            <tbody id="bayutMeta">
                
                
                <?php if(isset($propertyMeta['bayut_status'])): ?>
                <tr>
                    <td>Status</td>
                    <td><span class="badge badge-soft-<?php echo e(strtolower($propertyMeta['bayut_status'] == "Active" ? "success" : "warning")); ?>"><?php echo e($propertyMeta['bayut_status'] ?? ""); ?></span></td>
                </tr>
                <?php endif; ?>
                <?php if(isset($propertyMeta['bayut_rank'])): ?>

                <tr>
                    <td>Quality Score</td>
                    <td>
                        <div class="progress animated-progress custom-progress progress-label">
                            <div class="progress-bar <?php echo e(($propertyMeta['bayut_rank'] >= 80) ? "bg-success" : (($propertyMeta['bayut_rank'] >= 60) ? "bg-warning" : "bg-danger")); ?>" role="progressbar" style="width: <?php echo e($propertyMeta['bayut_rank']); ?>%" aria-valuenow="<?php echo e($propertyMeta['bayut_rank']); ?>" aria-valuemin="0" aria-valuemax="100" title="<?php echo e($propertyMeta['bayut_rank']."%" ?? ""); ?>"><div class="label"><?php echo e($propertyMeta['bayut_rank']."%" ?? ""); ?></div></div>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
                <?php if(isset($propertyMeta['bayut_level'])): ?>

                <tr>
                    <td>Product</td>

                    <?php
                        $level = "";

                        if($propertyMeta['bayut_level'] == "Premium"){
                            $level = "Basic";
                        }else{
                           $level = $propertyMeta['bayut_level'];
                        }
                    ?>
                    <td><span class="badge badge-soft-success badge-bayut-<?php echo e(strtolower($level)); ?>"><?php echo e($level ?? ""); ?></span></td>
                </tr>
                <?php endif; ?>
                <?php if(isset($propertyMeta['bayut_trucheck_status'])): ?>

                <tr>
                    <td>Validation</td>
                    <td><span class="badge badge-soft-dark badge-bayut-<?php echo e($propertyMeta['bayut_trucheck_status'] ?? "not-verified"); ?>"><?php echo e($propertyMeta['bayut_trucheck_status'] ?? null); ?></span></td>
                </tr>
                <?php endif; ?>
                <?php if(isset($propertyMeta['bayut_created_at'])): ?>
                <?php
                    $carbonDateTime = Carbon\Carbon::parse($propertyMeta['bayut_created_at']);
                    $daysAgo = $carbonDateTime->diffForHumans();
                ?>
                <tr>
                    <td>Published Date</td>
                    <td title="<?php echo e($propertyMeta['bayut_created_at']); ?>"><?php echo e($daysAgo ?? null); ?></td>
                </tr>
                <?php endif; ?>
                <?php if(isset($propertyMeta['bayut_updated_at'])): ?>
                <?php
                    $carbonDateTime = Carbon\Carbon::parse($propertyMeta['bayut_updated_at']);
                    $daysAgo = $carbonDateTime->diffForHumans();
                ?>
                <tr>
                    <td>Last Update</td>
                    <td title="<?php echo e($propertyMeta['bayut_updated_at']); ?>"><?php echo e($daysAgo ?? null); ?></td>
                </tr>
                <?php endif; ?>
                <?php if(isset($propertyMeta['bayut_url'])): ?>
                <tr>
                    <td>URL</td>
                    <td>
                        <a href="<?php echo e($propertyMeta['bayut_url']); ?>" class="link-info" target="_blank">View <i class="ri-external-link-line align-middle"></i></a></td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/partials/bayut.blade.php ENDPATH**/ ?>