<style>
    .scrollable-div {
        max-height: 229px;
        padding: 10px;
        resize: both;
        overflow: auto;
        /* width: 100%; */
    }
</style>

<div class="row">
    <div class="col-4"><label for="<?php echo e($id); ?>" class="form-label"> <?php echo e($label); ?>*</label>
    </div>


    <div class="col-8 scrollable-div">
        <select name='<?php echo e($id); ?>' id="<?php echo e($id); ?>" class="form-control search-inputs searchInput">
            <option value=""></option>
            <?php $__currentLoopData = $all_agents ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option data-image="<?php echo e($agent->thumbnail); ?>" data-status="<?php echo e($agent->status); ?>"
                    value="<?php echo e($agent->id); ?>">
                    <?php echo e($agent->first_name . ' ' . $agent->last_name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <div class="mt-1">
            <div class="row">
                <div class="col-md-6">
                    
                </div>
                <?php if(auth()->user()->hasRole(['Super Admin', 'Line Manager', 'Team Leader', 'Sales Director', 'Head of Secondary','CEO'])): ?>
                    <div class="col-md-6">
                        <div class="form-check form-switch form-check-inline" dir="ltr">
                            <small for="showMyTeamAgents_v">Team </small> <input type="checkbox" value="myteam"
                                class="form-check-input" id="showMyTeamAgents_v" name="showMyTeamAgents_v">

                        </div>
                    </div>
                <?php endif; ?>

            </div>



        </div>

        <div class="mt-1">

        </div>
    </div>
</div>




<?php $__env->startPush('agent_filter'); ?>
    <script>
        $(document).on('change', '#showInactiveAgents_v', function(e) {
            getAgents();
        });
        $(document).on('change', '#showMyTeamAgents_v', function(e) {
            getAgents();
        });
        getAgents();



        function getAgents() {
            if ($('#showInactiveAgents_v').is(':checked')) {
                all_agents = 'true';
            } else {
                all_agents = 'false';
            }

            if ($('#showMyTeamAgents_v').is(':checked')) {
                my_team = 'true';
            } else {
                my_team = 'false';
            }
            $.ajax({
                url: "<?php echo e(route('ajax.getAgentsBasedOnStatus')); ?>",
                type: 'POST',
                dataType: 'json',
                data: {
                    all_agents: all_agents,
                    my_team: my_team,
                },
                success: function(result) {
                    $("#<?php echo e($id); ?>").html('');

                    // make option and append to select #<?php echo e($id); ?>

                    let options = "";
                    option = ' <option value=""></option>'
                    result?.data.map(item => {
                        // options +=  `<option value="${item.id}">${item.full_name}</option>`;

                        options +=
                            `<option data-image="${item.thumbnail??item.thumb_url}" data-status="${item.status}" value="${item.id}">${item.full_name}</option>`;
                    });
                    $("#<?php echo e($id); ?>").html(options);

                    $('#<?php echo e($id); ?>').select2({
                        width: '100%',
                        placeholder: 'Select Agent',
                        allowClear: true,
                        templateResult: formatAgent,
                        templateSelection: formatAgentView,
                        dropdownParent: $('#generalModal')
                    });
                },
                error: function(jqXhr, json, errorThrown) {
                    console.log()
                },
            });
            $(document).on('change', '#selectall', function() {
                if ($(this).is(':checked')) {

                    var agents = $('#<?php echo e($id); ?> option').map(function() {
                        return $(this).val();
                    }).get();

                    $('#<?php echo e($id); ?>').val(agents).trigger('change');

                } else {
                    $('#<?php echo e($id); ?>').val(null).trigger('change');
                }
            });
        }
    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/templates/_multi_option_dropdown.blade.php ENDPATH**/ ?>