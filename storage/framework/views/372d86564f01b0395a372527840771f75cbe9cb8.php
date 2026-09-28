<div class="accordion-body" style="padding:0px;">
    <div data-simplebar style="max-height: 215px; padding: 0px !important;">
        <!-- Small Tables -->
        <table class="table table-sm table-nowrap">
            <tbody id="propertyFinderMeta">
                <?php if(isset($propertyMeta['pf_rank_details'])): ?>
                    <?php
                        $rankDetails = [];
                        if ($propertyMeta['pf_rank_details'] ?? null) {
                            $rankDetails = json_decode($propertyMeta['pf_rank_details'], true);
                        }
                        $rankText = '';
                        //dd($rankDetails );
                        foreach ($rankDetails as $key => $rank) {
                            $rankText .= ucwords(str_replace('_', ' ', $key)) . ': ' . $rank['tag'] . ',';
                        }
                        if (substr($rankText, -1) === ',') {
                            $rankText = substr($rankText, 0, -1);
                        }
                    ?>
                <?php endif; ?>

                <?php if(isset($propertyMeta['pf_status'])): ?>
                    <tr>
                        <td>Status</td>
                        <td><span
                                class="badge badge-soft-success badge-pf-<?php echo e(strtolower($propertyMeta['pf_status'] ?? null)); ?>"><?php echo e($propertyMeta['pf_status'] ?? ''); ?></span>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php if(isset($propertyMeta['pf_rank'])): ?>
                    <tr>
                        <td>Quality Score</td>
                        <td>
                            <div class="progress animated-progress custom-progress progress-label">
                                <div class="progress-bar <?php echo e($propertyMeta['pf_rank'] >= 80 ? 'bg-success' : ($propertyMeta['pf_rank'] >= 60 ? 'bg-warning' : 'bg-danger')); ?>"
                                    role="progressbar" style="width: <?php echo e($propertyMeta['pf_rank']); ?>%"
                                    aria-valuenow="<?php echo e($propertyMeta['pf_rank']); ?>" aria-valuemin="0" aria-valuemax="100"
                                    title="<?php echo e($rankText); ?>">
                                    <div class="label"><?php echo e($propertyMeta['pf_rank']); ?>%</div>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php if(isset($propertyMeta['pf_level'])): ?>
                    <tr>
                        <td>Product</td>
                        <td><span
                                class="badge badge-soft-success badge-pf-<?php echo e(strtolower($propertyMeta['pf_level'])); ?>"><?php echo e($propertyMeta['pf_level'] ?? ''); ?></span>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php if(isset($propertyMeta['pf_verified'])): ?>
                    <tr>
                        <td>Verification</td>
                        <td><span
                                class="badge badge-soft-dark badge-pf-<?php echo e($propertyMeta['pf_verified'] && $propertyMeta['pf_verified'] == 'Verified' ? 'verified' : 'not-verified'); ?>"><?php echo e($propertyMeta['pf_verified'] && $propertyMeta['pf_verified'] == 'Verified' ? 'Verified' : 'Not Verified'); ?></span>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php if(isset($propertyMeta['pf_created_at'])): ?>
                    <?php
                        $carbonDateTime = Carbon\Carbon::parse($propertyMeta['pf_created_at']);
                        $daysAgo = $carbonDateTime->diffForHumans();
                    ?>
                    <tr>
                        <td>Published Date</td>
                        <td title="<?php echo e($propertyMeta['pf_created_at']); ?>"><?php echo e($daysAgo ?? null); ?></td>
                    </tr>
                <?php endif; ?>
                <?php if(isset($propertyMeta['pf_updated_at'])): ?>
                    <?php
                        $carbonDateTime = Carbon\Carbon::parse($propertyMeta['pf_updated_at']);
                        $daysAgo = $carbonDateTime->diffForHumans();
                    ?>
                    <tr>
                        <td>Last Update</td>
                        <td title="<?php echo e($propertyMeta['pf_updated_at']); ?>"><?php echo e($daysAgo ?? null); ?></td>
                    </tr>
                <?php endif; ?>
                <?php if(isset($propertyMeta['pf_url'])): ?>
                    <tr>
                        <td>URL</td>
                        <td>
                            <a href="<?php echo e($propertyMeta['pf_url']); ?>" class="link-info" target="_blank">View <i
                                    class="ri-external-link-line align-middle"></i></a>
                        </td>
                    </tr>


                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Property Finder Upgrades')): ?>
                <tr>

                    <td colspan="2">
                        <a href="javascript:void(0)" id="showPfUpgradesModal" class="btn btn-sm btn-success" >Available Upgrades </a>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/partials/property-finder.blade.php ENDPATH**/ ?>