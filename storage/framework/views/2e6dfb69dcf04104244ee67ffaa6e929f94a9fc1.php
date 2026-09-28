<div class="collapse slideIn" id="collapseFilterForm">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css">
    <div class="row">
        <form id="searchForm" class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-3 col-md-6 col-sm-12">
                            <div class="row">
                                <div class="col-4">Role</div>
                                <div class="col-8">
                                    <select name='roles' id="search_roles" class="form-control select2 search-inputs"
                                        multiple>
                                        <?php $__currentLoopData = $roles ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($role->name); ?>"><?php echo e($role->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-4 mt-2">Bitrix Role</div>
                                <div class="col-8 mt-2">
                                    <select name='search_bitrix_role' id="search_bitrix_role" class="form-control select2 search-inputs" multiple>
                                        <?php $__currentLoopData = $bitrix_role ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($value); ?>"><?php echo e($value); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>


                            <div class="row">

                                <div class="col-4 mt-2">
                                    <label for="roles" class="form-label">Parent</label>
                                </div>
                                <div class="col-8 mt-2">
                                    <select name='parent_id' id="search_parent_id"
                                        class="form-control select2 search-inputs">
                                        <option value=""></option>
                                        <?php $__currentLoopData = $userAgents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <optgroup label="<?php echo e($index); ?>">
                                                <?php $__currentLoopData = $group; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($user['id']); ?>"><?php echo e($user['name']); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </optgroup>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>

                                </div>
                            </div>
                            <div class="row">

                                <div class="col-4 mt-2">
                                    <label for="roles" class="form-label">First Name</label>
                                </div>
                                <div class="col-8 mt-2">
                                    <input type="text" class="form-control search-inputs" name="first_name">

                                </div>
                            </div>
                            <div class="row">

                                <div class="col-4 mt-2">
                                    <label for="roles" class="form-label">Last Name</label>
                                </div>
                                <div class="col-8 mt-2">
                                    <input type="text" class="form-control search-inputs" name="last_name">

                                </div>
                            </div>
                            <div class="row">

                                <div class="col-4 mt-2">
                                    <label for="roles" class="form-label">Mobile</label>
                                </div>
                                <div class="col-8 mt-2">
                                    <input type="text" class="form-control search-inputs" name="search_mobile">

                                </div>
                            </div>
                            <div class="row">

                                <div class="col-4 mt-2">
                                    <label for="roles" class="form-label">Email</label>
                                </div>
                                <div class="col-8 mt-2">
                                    <input type="email" class="form-control search-inputs" name="email">

                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 col-sm-12">
                            <div class="row mt-2">
                                <div class="col-lg-4">
                                    <label for="search_nationality_id" class="form-label"> Nationality</label>
                                </div>
                                <div class="col-lg-8">
                                    <select class="form-control select2 search-inputs search-inputs"
                                        name="nationality_id" id="search_nationality_id" multiple>

                                        <?php $__currentLoopData = $countries ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($country->id); ?>"><?php echo e($country->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-4 mt-2">
                                    <label for="roles" class="form-label">Position</label>
                                </div>
                                <div class="col-8 mt-2">
                                    <input type="text" class="form-control search-inputs" name="position">

                                </div>
                            </div>
                            <div class="row">

                                <div class="col-4 mt-2">
                                    <label for="roles" class="form-label">Licence/RERA No
                                    </label>
                                </div>
                                <div class="col-8 mt-2">
                                    <input type="text" class="form-control search-inputs" name="licence_rera">

                                </div>
                            </div>
                            <div class="row">

                                <div class="col-4 mt-2">
                                    <label for="roles" class="form-label">Telegram Username
                                    </label>
                                </div>
                                <div class="col-8 mt-2">
                                    <input type="text" class="form-control search-inputs" name="telegram_username">

                                </div>
                            </div>
                            <div class="row">

                                <div class="col-4 mt-2">
                                    <label for="roles" class="form-label">Telegram Chat Id
                                    </label>
                                </div>
                                <div class="col-8 mt-2">
                                    <input type="text" class="form-control search-inputs" name="telegram_chatid">

                                </div>
                            </div>

                            <div class="row">
                                <div class="col-4">Telelog Entities</div>
                                <div class="col-8">
                                    <select name='telelog_entities' id="search_telelog_entities" class="form-control select2 search-inputs"
                                            multiple>
                                        <?php $__currentLoopData = getTelelogEntities() ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $telelogEntity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($telelogEntity); ?>"><?php echo e($telelogEntity); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>

                        </div>
                        <div class="col-xl-3 col-md-6 col-sm-12">
                            <div class="row">

                                <div class="col-4 mt-2">
                                    <label for="roles" class="form-label">Public Name
                                    </label>
                                </div>
                                <div class="col-8 mt-2">
                                    <input type="text" class="form-control search-inputs" name="public_name">

                                </div>
                            </div>
                            <div class="row">

                                <div class="col-4 mt-2">
                                    <label for="roles" class="form-label">Public License No.
                                    </label>
                                </div>
                                <div class="col-8 mt-2">
                                    <input type="text" class="form-control search-inputs"
                                        name="public_license_no">

                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-lg-4">
                                    <label for="search_created_from" class="form-label">Created From</label>
                                </div>
                                <div class="col-lg-8">
                                    <input type="date" class="form-control search-inputs"
                                        data-provider="flatpickr" data-date-format="Y-m-d" id="search_created_from"
                                        name="search_created_from" placeholder="Enter created from date">
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-lg-4">
                                    <label for="search_created_to" class="form-label">Created To</label>
                                </div>
                                <div class="col-lg-8">
                                    <input type="date" class="form-control search-inputs"
                                        data-provider="flatpickr" data-date-format="Y-m-d" id="search_created_to"
                                        name="search_created_to" placeholder="Enter create to date">
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-lg-4">
                                    <label for="search_created_to" class="form-label">Status</label>
                                </div>
                                <div class="col-lg-8">

                                    <div class="form-check form-switch form-switch-lg form-switch-success"
                                        dir="ltr">
                                        <input type="checkbox" id="search_status" name="search_status"
                                            class="form-check-input status-change" role="switch">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 col-sm-12">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-check form-switch form-switch-success">
                                        <input class="form-check-input search-inputs" type="checkbox"
                                            name="search_deleted" role="switch" id="search_deleted">
                                        <label class="form-check-label" for="search_deleted">Show Deleted Also</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-check form-switch form-switch-success">
                                        <input class="form-check-input search-inputs" type="checkbox"
                                            name="api_access" role="switch" id="search_api_access">
                                        <label class="form-check-label" for="search_api_access">Users with API
                                            access</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-check form-switch form-switch-success">
                                        <input class="form-check-input search-inputs" type="checkbox"
                                            name="search_without_chatid" role="switch" id="search_without_chatid">
                                        <label class="form-check-label" for="search_without_chatid">User without
                                            chatid</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-check form-switch form-switch-success">
                                        <input class="form-check-input search-inputs" type="checkbox"
                                            name="search_without_parent" role="switch" id="search_without_parent"
                                            value="on">
                                        <label class="form-check-label" for="search_without_parent">User without
                                            parent</label>
                                    </div>
                                </div>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Users Logged In Filter')): ?>
                                    <hr>
                                    <div class="col-6">
                                        <div>
                                            <label class="form-check-label" for="search_deleted">App logged in</label>
                                            <div class="toggle-container">
                                                <div class="tw-toggle">
                                                    <input type="radio" name="search_logged_in" value="false">
                                                    <label class="toggle toggle-yes"><i class="fa fa-times"></i></label>
                                                    <input checked type="radio" name="search_logged_in" value="-1">
                                                    <label class="toggle toggle-yes"><i class="fa fa-minus"></i></label>
                                                    <input type="radio" name="search_logged_in" value="true">
                                                    <label class="toggle toggle-yes"><i class="fa fa-check"></i></label>
                                                    <span></span>
                                                </div>
                                            </div>

                                            
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Users Authenticator Filter')): ?>
                                    <div class="col-6">
                                        <div>
                                            <label class="form-check-label" for="search_deleted">Authenticator</label>
                                            <div class="toggle-container">
                                                <div class="tw-toggle">
                                                    <input type="radio" name="search_google_authenticator"
                                                        value="false">
                                                    <label class="toggle toggle-yes"><i class="fa fa-times"></i></label>
                                                    <input checked type="radio" name="search_google_authenticator"
                                                        value="-1">
                                                    <label class="toggle toggle-yes"><i class="fa fa-minus"></i></label>
                                                    <input type="radio" name="search_google_authenticator"
                                                        value="true">
                                                    <label class="toggle toggle-yes"><i class="fa fa-check"></i></label>
                                                    <span></span>
                                                </div>
                                            </div>

                                            
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="mt-4 d-flex justify-content-start align-items-center gap-2">
                                <button type="submit" class="btn btn-sm btn-primary action-search"
                                    id="searchSearchBtn">
                                    <i class="ri-search-line align-bottom me-1"></i> Search
                                </button>
                                <a href="javascript:void(0)" class="btn btn-sm text-warning action-clear"
                                    id="clearSearchBtn">Clear</a>
                                <a href="javascript:void(0)" type="button"
                                    class="btn btn-sm text-danger action-close"
                                    onclick="toggleAdvanceSearch();">Close</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/user/includes/filterForm.blade.php ENDPATH**/ ?>