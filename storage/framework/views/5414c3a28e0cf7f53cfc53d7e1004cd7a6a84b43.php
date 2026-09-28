<div class="accordion-body" style="padding:0px;">
    <div data-simplebar>
        <!-- Small Tables -->
        <table class="table table-sm table-nowrap">
            <tbody id="dubizzleMeta">

                <?php if(isset($propertyMeta['dubizzle_status'])): ?>
                <tr>
                    <td>Status</td>
                    <td><span class="badge badge-soft-<?php echo e(strtolower($propertyMeta['dubizzle_status'] == "Active" ? "success" : "warning")); ?>"><?php echo e($propertyMeta['dubizzle_status'] ?? ""); ?></span></td>
                </tr>
                <?php endif; ?>
                <?php if(isset($propertyMeta['dubizzle_rank'])): ?>
                <tr>
                    <td>Quality Score</td>
                    <td>
                        <div class="progress animated-progress custom-progress progress-label">
                            <div class="progress-bar <?php echo e(($propertyMeta['dubizzle_rank'] >= 80) ? "bg-success" : (($propertyMeta['dubizzle_rank'] >= 60) ? "bg-warning" : "bg-danger")); ?>" role="progressbar" style="width: <?php echo e($propertyMeta['dubizzle_rank']); ?>%" aria-valuenow="<?php echo e($propertyMeta['dubizzle_rank']); ?>" aria-valuemin="0" aria-valuemax="100" title="<?php echo e($propertyMeta['dubizzle_rank']."%" ?? ""); ?>"><div class="label"><?php echo e($propertyMeta['dubizzle_rank']."%" ?? ""); ?></div></div>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
                <?php if(isset($propertyMeta['dubizzle_level'])): ?>
                <tr>
                    <td>Product</td>
                    <td><span class="badge badge-soft-success badge-bayut-<?php echo e(strtolower($propertyMeta['dubizzle_level'])); ?>"><?php echo e($propertyMeta['dubizzle_level'] ?? ""); ?></span></td>
                </tr>
                <?php endif; ?>
                <?php if(isset($propertyMeta['dubizzle_trucheck_status'])): ?>
                <tr>
                    <td>Verification</td>
                    <td><span class="badge badge-soft-dark badge-bayut-<?php echo e($propertyMeta['dubizzle_trucheck_status'] && $propertyMeta['dubizzle_trucheck_status']== "truchecked" ? "verified" :"not-verified"); ?>"><?php echo e($propertyMeta['dubizzle_trucheck_status'] ?? null); ?></span></td>
                </tr>
                <?php endif; ?>
                <?php if(isset($propertyMeta['dubizzle_created_at'])): ?>
                <?php
                    $carbonDateTime = Carbon\Carbon::parse($propertyMeta['dubizzle_created_at']);
                    $daysAgo = $carbonDateTime->diffForHumans();
                ?>
                <tr>
                    <td>Published Date</td>
                    <td title="<?php echo e($propertyMeta['dubizzle_created_at']); ?>"><?php echo e($daysAgo ?? null); ?></td>
                </tr>
                <?php endif; ?>
                <?php if(isset($propertyMeta['dubizzle_updated_at'])): ?>

                <?php
                    $carbonDateTime = Carbon\Carbon::parse($propertyMeta['dubizzle_updated_at']);
                    $daysAgo = $carbonDateTime->diffForHumans();
                ?>
                <tr>
                    <td>Last Update</td>
                    <td title="<?php echo e($propertyMeta['dubizzle_updated_at']); ?>"><?php echo e($daysAgo ?? null); ?></td>
                </tr>
                <?php endif; ?>
                <?php if(isset($propertyMeta['dubizzle_refreshed_at'])): ?>
                <?php
                    $carbonDateTime = Carbon\Carbon::parse($propertyMeta['dubizzle_refreshed_at']);
                    $daysAgo = $carbonDateTime->diffForHumans();
                ?>
                <tr>
                    <td>Last Refreshed</td>
                    <td title="<?php echo e($propertyMeta['dubizzle_refreshed_at']); ?>"><?php echo e($daysAgo ?? null); ?></td>
                </tr>
                <?php endif; ?>
                <?php if(isset($propertyMeta['dubizzle_url'])): ?>
                <tr>
                    <td>URL</td>
                    <td>
                        <a href="<?php echo e($propertyMeta['dubizzle_url']); ?>" class="link-info" target="_blank">View <i class="ri-external-link-line align-middle"></i></a></td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/partials/dubizzle.blade.php ENDPATH**/ ?>