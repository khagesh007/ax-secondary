<div class="collapse slideIn" id="collapseUserForm">
    <div class="row" id="rowLoader">
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body form-steps">
                    <input type="hidden" id="is_live_listing" value="0">


                    <input type="hidden" name="id" id="id">
                    <input type="hidden" name="temp_id" id="temp_id">
                    <input type="hidden" name="isPublishRequest" id="isPublishRequest" value="0">
                    <input type="hidden" name="isPocketRequest" id="isPocketRequest">
                    <?php if(auth()->user()->hasRole('Agent')): ?>
                        <input type="hidden" name="agent_id" id="agent_id" value="<?php echo e(auth()->id()); ?>">
                        <div class="agent_container">

                        </div>
                    <?php else: ?>
                        <div class="row">
                            <div class="col-lg-4 col-md-4 mt-2">
                                <label for="agent_id" class="form-label"> Agent*</label>
                            </div>
                            <div class="col-lg-8 col-md-8 mt-2">
                                <select class="form-select select2 inputs" name="agent_id" id="agent_id" required>
                                    <option value=""></option>
                                    <?php $__currentLoopData = $agents ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option data-image="<?php echo e($agent->thumbnail); ?>" data-status="<?php echo e($agent->status); ?>"
                                            value="<?php echo e($agent->id); ?>">
                                            <?php echo e($agent->first_name); ?>

                                            <?php echo e($agent->last_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Show Deactivated Agents')): ?>
                                <div class="mt-1">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-check form-switch form-check-inline" dir="ltr">
                                                <small for="showInactiveAgentsContacts">Inactive </small> <input type="checkbox" value="inactive"
                                                    class="form-check-input" id="showInactiveAgentsContacts" name="showInactiveAgentsContacts">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <div class="invalid-feedback">
                                    Please choose an agent.
                                </div>

                                <div id="agent_container"></div>
                            </div>

                        </div>
                    <?php endif; ?>
                    <div class="row">
                        <div class="col-lg-4 col-md-4 mt-2">
                            <label for="refered_by" class="form-label"> Referred By</label>
                        </div>
                        <div class="col-lg-8 col-md-8 mt-2">
                            <select class="form-select select2 inputs" name="refered_by" id="refered_by">
                                <option value=""></option>
                                <?php $__currentLoopData = $refered_by ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option data-image="<?php echo e($agent->thumbnail); ?>" data-status="<?php echo e($agent->status); ?>"
                                        value="<?php echo e($agent->id); ?>">
                                        <?php echo e($agent->first_name); ?>

                                        <?php echo e($agent->last_name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>


                            <div class="invalid-feedback">
                                Please choose an Refered By.
                            </div>


                        </div>

                    </div>
                    <div class="row owner_container">
                        <div class="col-lg-4 col-md-4 mt-2">
                            <label for="owner_id" class="form-label"> Owner*</label>
                        </div>
                        <div class="col-lg-8 col-md-8 mt-2">
                            <select class="form-select select2 inputs" name="owner_id" id="owner_id" required>
                                <option value=""></option>
                                <?php $__currentLoopData = $owners ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $owner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option data-mobile="<?php echo e($owner->mobile); ?>" value="<?php echo e($owner->id); ?>">
                                        <?php echo e($owner->first_name); ?> <?php echo e($owner->last_name); ?>


                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <div class="invalid-feedback">
                                Please choose an owner.
                            </div>
                            <div id="owners_detail_link">

                            </div>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Contacts Create')): ?>
                                <a href="javascript:void(0)" title="Add New Owner" id="addContact"
                                    class=" waves-effect waves-light"><i class="ri-add-circle-fill"></i>Add new owner</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body form-steps">
                    <div class="row">
                        <div class="col-lg-4 col-md-4 ">
                            <label for="for" class="form-label"> Category*</label>
                        </div>
                        <div class="col-lg-8 col-md-8 mt-2 effect-publish-div">
                            <select class="form-control select2 inputs effect-publish" id="for" name="for"
                                required>
                                <option value=""></option>
                                <?php $__currentLoopData = \App\Models\Property::FOR_SELECT ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $for): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($for); ?>"><?php echo e($for); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <div class="invalid-feedback">
                                Please choose a category.
                            </div>
                            <div class="will-effect-status text-danger">
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 ">
                            <label for="property_category_id" class="form-label"> Type*</label>
                        </div>
                        <div class="col-lg-8 col-md-8 mt-2 effect-publish-div">
                            <select class="form-control select2 inputs effect-publish" id="property_category_id"
                                name="property_category_id" required>
                                <option value=""></option>
                                <?php $__currentLoopData = $categories ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($cat->id); ?>"><?php echo e($cat->title); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <div class="invalid-feedback">
                                Please choose a type.
                            </div>
                            <div class="will-effect-status text-danger">
                            </div>
                        </div>
                        <div class="d-none" id="villa_type_fields">
                            <div class="row">
                                <div class="col-lg-4 col-md-4">
                                    <label for="villa_type" class="form-label"> Villa type</label>
                                </div>
                                <div class="col-lg-8 col-md-8 mt-2">
                                    <select class="form-control select2 inputs" id="villa_type" name="villa_type[]"
                                        multiple>
                                        <option></option>
                                        <?php $__currentLoopData = \App\Models\Property::VILLA_TYPE ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 mt-2">
                            <label for="bed_id" class="form-label"> Beds</label>
                        </div>
                        <div class="col-lg-8 col-md-8 mt-2 effect-publish-div">
                            <select class="form-control select2 inputs effect-publish" id="bed_id" name="bed_id">
                                <option value=""></option>
                                <?php $__currentLoopData = $beds ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bed): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($bed->id); ?>"><?php echo e($bed->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <div class="invalid-feedback">
                                Please choose a bed.
                            </div>
                            <div class="will-effect-status text-danger">
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 mt-2">
                            <label for="bath_id" class="form-label"> Baths</label>
                        </div>
                        <div class="col-lg-8 col-md-8 mt-2 effect-publish-div">
                            <select class="form-control select2 inputs effect-publish" id="bath_id" name="bath_id">
                                <option value=""></option>
                                <?php $__currentLoopData = $baths ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bath): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($bath->id); ?>"><?php echo e($bath->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <div class="invalid-feedback">
                                Please choose a bath.
                            </div>
                            <div class="will-effect-status text-danger">
                            </div>
                        </div>


                        <div class="col-lg-4 col-md-4 mt-2">
                            <label for="location_id" class="form-label"> Location*</label>
                        </div>
                        <div class="col-lg-8 col-md-8 mt-2 effect-publish-div">
                            <input type="hidden" name="location_type" id="location_type">
                            <select class="form-control select2 inputs effect-publish" id="location_id"
                                name="location_id" required>
                                <option value=""></option>
                                
                            </select>
                            <div class="invalid-feedback">
                                Please choose a location.
                            </div>
                            <div id="location_address"></div>

                            <div class="will-effect-status text-danger">
                            </div>
                        </div>


                        <div class="col-lg-4 col-md-4 mt-2">
                            <label for="unit_no" class="form-label">Unit #*</label>
                        </div>
                        <div class="col-lg-8 col-md-8 mt-2  effect-publish-div">
                            <input class="form-control inputs effect-publish " id="unit_no" name="unit_no"
                                placeholder="Enter unit #" required />
                            <div class="invalid-feedback">
                                Please enter unit no.
                            </div>
                            <div id="duplicate_listing_alert">


                            </div>
                            <div class="will-effect-status text-danger">
                            </div>
                        </div>

                        <div class="col-lg-4 mt-2">
                            <label for="title_deed_text" class="form-label">Title Deed</label>
                        </div>
                        <div class="col-lg-8 mt-2">
                            <input type="text" id="title_deed_text" name="title_deed_text"
                                class="form-control inputs" placeholder="Enter title deed">

                        </div>

                        <div class="col-lg-4 col-md-4 mt-2">
                            <label for="built_up_area" class="form-label">Size(<span id="">Sqft</span>)*</label>
                            <div class="flex-shrink-0">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" id="size-converter" name="size-converter">
                                    <label class="form-check-label" for="size-converter">Sqm</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8 col-md-8 mt-2 effect-publish-div">
                            <div class="row">
                                <div class="col-lg-6 col-md-6 mt-2 ">
                                    <input type="number" step="0.01" class="size-input form-control inputs effect-publish"
                                        id="built_up_area" name="built_up_area" placeholder="Enter BUA" />
                                    <div class="invalid-feedback">
                                        Please enter build-up area.
                                    </div>

                                </div>
                                <div class="col-lg-6 col-md-6 mt-2">
                                    <input type="number" step="0.01" class="size-input form-control inputs" id="plot_size"
                                        name="plot_size" placeholder="Enter plot size" />
                                    <div class="invalid-feedback">
                                        Please enter plot size.
                                    </div>
                                </div>
                            </div>
                            <div class="will-effect-status text-danger">
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-4 mt-2">
                            <label for="parking" class="form-label">Parkings*</label>
                        </div>
                        <div class="col-lg-8 col-md-8 mt-2">
                            <input type="number" class="form-control inputs" id="parking" name="parking"
                                placeholder="Enter number of parking slots" min="0" />
                            <div class="invalid-feedback">
                                Please enter parking.
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 mt-2">
                            <label for="view" class="form-label">View</label>
                        </div>
                        <div class="col-lg-8 col-md-8 mt-2">
                            <select class="form-control select2 inputs" name="view[]" id="view">
                                <option></option>
                                <?php $__currentLoopData = \App\Models\Property::VIEWS ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            </select>
                        </div>


                    </div>
                </div>
            </div>

            <?php if(! \Spatie\Permission\PermissionServiceProvider::bladeMethodWrapper('hasRole', 'Agent')): ?>
                <div class="card">
                    <div class="card-header p-05rem">
                        <h6 class="card-title mb-0">RERA Validity </h6>
                    </div>
                    <div class="card-body form-steps">
                        <div class="row">
                            <div class="col-lg-4 col-md-4 mt-2">
                                <label for="rera_permit_number" class="form-label">Company</label>
                            </div>
                            <div class="col-lg-8 col-md-8 mt-2">
                             <select class="form-control select2 inputs" id="entity_company_id" name="entity_company_id">
                                    <option value=""></option>
                                    <?php $__currentLoopData = $entity_companies ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($company->id); ?>"><?php echo e($company->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <div class="invalid-feedback">
                                    Please choose a company.
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 mt-2">
                                <label for="rera_permit_number" class="form-label">RERA Permit No.(Trakheesi)</label>
                            </div>
                            <div class="col-lg-8 col-md-8 mt-2">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="rera_permit_number"
                                        name="rera_permit_number" placeholder="Enter RERA permit #"
                                        aria-label="Enter RERA permit #" aria-describedby="button-addon2">
                                    <?php if(auth()->user()->can('Listing Rera Check')): ?>
                                        <button class="btn btn-primary btn-icon waves-effect waves-light d-none btn-load"
                                            type="button" title="Validate RERA Permit Number" id="checkRera">
                                            <i class="mdi mdi-eye-arrow-right"></i>

                                        </button>
                                    <?php endif; ?>
                                </div>
                                <div class=" fs-12 mt-1" id="reraDateRange">

                                </div>
                                <div class=" fs-12 mt-1" id="reraPfMsg">

                                </div>
                                <div class="invalid-feedback">
                                    Please enter RERA permit no.
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <?php echo $__env->make('admin.listing.includes.partials._activities', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

            <div id="eventWidgetArea">

            </div>

        </div>
        <div class="col-xl-5" id="change-layout">
            <div class="card">
                <div class="card-body form-steps">
                    <div class="row">
                        <div class="col-lg-2 col-md-2 mt-2">
                            <label for="title" class="form-label"> Title* <i class="bx bx-info-circle"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title=""
                                    data-bs-original-title="The ideal size for a property title is between 38 to 42 characters."></i></label>
                        </div>



                        <div class="col-lg-10  col-md-10 mt-2">
                            <input type="text" class="form-control inputs" minlength="35" maxlength="50"
                                name="title" id="title" placeholder="Enter title" required>
                            <div class="invalid-feedback">
                                Title must be between 35 and 50 characters.
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2 col-md-2 mt-2">
                            <label for="community_id" class="form-label"> Price*</label>
                        </div>
                        <div class="col-lg-6  col-md-6 mt-2 effect-publish-div">
                            <input type="text" class="form-control inputs effect-publish" name="price"
                                id="price" placeholder="Enter price" required>
                            <div class="invalid-feedback">
                                Please enter price.
                            </div>
                            <div class="will-effect-status text-danger">
                            </div>
                        </div>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Properties Hide Price')): ?>
                            <div class="col-lg-4 col-md-4 mt-2 d-none" id="hideprice-section">
                                <div class="flex-shrink-0 mt-2">
                                    <div class="form-check form-switch form-switch-right form-switch-md">
                                        <label for="input-group-hide-price" class="form-label text-muted">Hide
                                            Price</label>
                                        <input class="form-check-input code-switcher" name="price_on_application"
                                            type="checkbox" id="price_on_application">
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="col-lg-4 col-md-4 mt-2 d-none" id="cheques-section">
                            <select class="form-control select2 inputs" id="cheques" name="cheques">
                                <option value=""></option>
                                <option value="1">1 Cheque</option>
                                <option value="2">2 Cheques</option>
                                <option value="3">3 Cheques</option>
                                <option value="4">4 Cheques</option>
                                <option value="5">5 Cheques</option>
                                <option value="6">6 Cheques</option>
                                <option value="7">7 Cheques</option>
                                <option value="8">8 Cheques</option>
                                <option value="9">9 Cheques</option>
                                <option value="10">10 Cheques</option>
                                <option value="11">11 Cheques</option>
                                <option value="12">12 Cheques</option>
                            </select>
                            <div class="invalid-feedback">
                                Please choose a no. of cheques.
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2 col-md-2 mt-2">
                            <label for="payment_method" class="form-label"> Payment Method*</label>
                        </div>
                        <div class="col-lg-6  col-md-6 mt-2 effect-publish-div">
                            <select class="form-control select2 inputs" id="payment_method" name="payment_method" required>
                                <option value="cash">Cash</option>
                                <option value="installments">Installments</option>
                                <option value="cash,installments">Cash, Installments</option>
                            </select>
                            <div class="invalid-feedback">
                                Please choose payment method.
                            </div>
                            <div class="will-effect-status text-danger">
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 mt-2 d-none" id="down_payment_price_container">
                            <input type="text" class="form-control inputs down_payment_price_field" name="down_payment_price"
                                id="down_payment_price" placeholder="Down payment price">
                            <div class="invalid-feedback">
                                Please add down payment price.
                            </div>
                        </div>
                    </div>

                    <div class="row" id="descriptionFullDiv">
                        <div class="col-lg-2 col-md-2 mt-2">
                            <label for="title" class="form-label"> Description*</label>
                            <button type="button" class="btn btn-default btn-sm" id="btn_description_fullscreen"
                                title="Full Screen" aria-label="Full Screen"><i
                                    class="note-icon-arrows-alt"></i></button>
                        </div>
                        <div class="col-lg-10  col-md-10 mt-2">
                            <textarea class="form-control inputs" id="description" placeholder="Enter description" name="description"
                                rows="10" maxlength="2000"></textarea>
                            <div class="invalid-feedback">
                                Please enter description 750-2000 characters.
                            </div>
                        </div>


                    </div>
                    <div class="row mb-3" id="featureFullDiv">
                        <div class="col-lg-2 col-md-2 mt-2">
                            <label for="title" class="form-label"> Amenities</label>
                            <button type="button" class="btn btn-default btn-sm" id="btn_features_fullscreen"
                                title="Full Screen" aria-label="Full Screen"><i
                                    class="note-icon-arrows-alt"></i></button>
                        </div>
                        <div class="col-lg-10 col-md-10 mt-2">
                            <div class="scrollable-features">
                                <?php $__currentLoopData = $features ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="single-feature">
                                        <div class="form-check amenities-form-check">
                                            <input class="btn-check form-check-input inputs" name="features[]"
                                                type="checkbox" value="<?php echo e($feature->id); ?>"
                                                data-title="<?php echo e($feature->title); ?>" id="feature-<?php echo e($feature->id); ?>"
                                                autocomplete="off">
                                            <label class="btn btn-amenities-primary btn-custom btn-primary"
                                                for="feature-<?php echo e($feature->id); ?>">
                                                <?php echo e($feature->title); ?>

                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2 col-md-2 mt-2">
                            <label for="built_up_area" class="form-label">Furnishing*</label>
                        </div>
                        <div class="col-lg-10 col-md-10 mt-2">
                            <select class="form-control select2 inputs" id="furnished" name="furnished">
                                <option></option>
                                <option value="FURNISHED">Furnished</option>
                                <option value="SEMI_FURNISHED">Semi Furnished</option>
                                <option value="UNFURNISHED">Unfurnished</option>
                            </select>
                            <div class="invalid-feedback">
                                Please choose a furnished type.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header p-05rem">
                    <h6 class="card-title mb-0">Media </h6>
                </div>

                <div class="card-body mt-0 form-steps" id="imgFullDiv" style="overflow: scroll">
                    <ul class="nav nav-tabs nav-tabs-custom nav-success mb-3" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#photos-tab" role="tab"
                                aria-selected="false">
                                <i class="mdi mdi-folder-multiple-image align-middle"></i>
                                Photos
                                <span
                                    class="position-absolute topbar-badge cartitem-badge fs-10 translate-middle badge rounded-pill bg-info"
                                    id="photo_counter">0</span>
                            </a>
                        </li>
                        <li class="nav-item docsTab">
                            <a class="nav-link docsTab" data-bs-toggle="tab" href="#documents-tab" role="tab"
                                aria-selected="false">
                                <i class="mdi mdi-file-document-multiple"></i>
                                Documents
                                <span
                                    class="position-absolute topbar-badge cartitem-badge fs-10 translate-middle badge rounded-pill bg-info"
                                    id="document_counter">0</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#floor-plan-tab" role="tab"
                                aria-selected="false">
                                <i class="mdi mdi-floor-plan"></i>
                                FloorPlan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#other-media-tab" role="tab"
                                aria-selected="false">
                                <i class="mdi mdi-youtube-subscription"></i>
                                Other
                            </a>
                        </li>
                        <?php if(auth()->user()->hasRole(['Super Admin'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" id="deletedMedia" href="#other-media-deleted" role="tab"
                                aria-selected="false">
                                <i class="mdi mdi-youtube-subscription"></i>
                                Deleted Document
                            </a>
                        </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <button type="button" class="btn btn-default btn-sm" id="btn_pics_fullscreen"
                                title="Full Screen" aria-label="Full Screen"><i
                                    class="note-icon-arrows-alt"></i></button>
                        </li>


                    </ul>
                    <div class="tab-content ">
                        <div class="tab-pane active " id="photos-tab" role="tabpanel">

                            <div class="own_property d-none display-flex">
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Add Media')): ?>
                                <button type="button" id="uploadPhotos" class="btn btn-sm inputs btn-primary waves-effect">
                                    <i class="ri-upload-2-fill label-icon align-middle "></i>
                                    Upload Photos
                                </button>
                                <?php endif; ?>

                                <a type="button" id="dropdownMenuLink2" data-bs-toggle="dropdown"
                                    aria-expanded="false"
                                    class="btn btn-outline-primary btn-sm  dropdown-toggle">Download</a>


                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink2">

                                    

                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Download Bulk Without Watermark')): ?>
                                        <li class="downloadBulkwithoutWatermark d-none">
                                            <a class="dropdown-item" data-ref_no="<?php echo e(request()->ref_number); ?>"
                                                href="#" onclick="event.preventDefault();"
                                                id="downloadBulkwithoutWatermark">
                                                <i class="ri-download-2-line label-icon align-middle "></i>
                                                Originals
                                            </a>
                                        </li>
                                    <?php endif; ?>


                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Download Bulk With Watermark')): ?>
                                        <li class="downloadWithWaterMark d-none">
                                            <a class="dropdown-item" data-ref_no="<?php echo e(request()->ref_number); ?>"
                                                href="#" onclick="event.preventDefault();"
                                                id="downloadBulkWatermark">
                                                <i class="ri-download-2-line label-icon align-middle "></i>
                                                Watermarked
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Download Not For Marketing')): ?>
                                        <li class="downloadWithWaterMark d-none">
                                            <a class="dropdown-item" data-ref_no="<?php echo e(request()->ref_number); ?>"
                                                href="#" onclick="event.preventDefault();"
                                                id="downloadNotForMarketing">
                                                <i class="ri-download-2-line label-icon align-middle "></i>
                                                Not For Marketing
                                            </a>
                                        </li>
                                    <?php endif; ?>


                                </ul>




                                

                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Bulk Delete Images')): ?>
                                    <button type="button" id="selectAllOptions"
                                        class="btn btn-sm btn-soft-secondary waves-effect action-more d-none">
                                        <i class="mdi-progress-check mdi label-icon align-middle "></i>
                                        Select all
                                    </button>
                                    <button type="button" id="deleteImages"
                                        class="btn btn-sm btn-soft-danger waves-effect action-danger mt-2 d-none"
                                        onclick="deleteMultipleImages()">
                                        <i class="mdi-delete-outline mdi label-icon align-middle "></i>
                                        Delete Multiple
                                    </button>
                                <?php endif; ?>
                            </div>


                            

                            <div class="mt-2">



                                <div class="scrollable-gallery" id="photos-gallery"></div>
                            </div>
                        </div>
                        <div class="tab-pane " id="documents-tab" role="tabpanel">
                            
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Download Bulk Documents')): ?>
                                <button type="button" id="downloadBulkDocuments"
                                    class="btn btn-sm btn-soft-secondary waves-effect action-export bulkDownload"
                                    data-ref_no="<?php echo e(request()->ref_number); ?>">
                                    <i class="ri-download-2-line label-icon align-middle "></i>
                                    Download All
                                </button>
                            <?php endif; ?>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Request Documents')): ?>
                            <button type="button" id="downloadRequestBulkDocuments"
                                class="btn btn-sm btn-soft-secondary waves-effect action-export bulkDownload"
                                data-ref_no="<?php echo e(request()->ref_number); ?>">
                                <i class="ri-download-2-line label-icon align-middle "></i>
                                Request Download
                            </button>
                            <?php endif; ?>

                            <div class="row">
                                <div class="col-lg-4 mt-2">
                                    <label for="title_deed" class="form-label">Title Deed*</label>
                                </div>
                                <div class="col-lg-8 mt-2">
                                    <input type="file" id="title_deed" title="Choose" name="title_deed"
                                        class="form-control inputs" accept="application/pdf,image/png, image/jpeg">
                                    <div id="title_deed_file"></div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-4 mt-2">
                                    <label for="owner_passport_copy" class="form-label">Owner's Passport/EID*</label>
                                </div>
                                <div class="col-lg-8 mt-2">
                                    <input type="file" id="owner_passport_copy" name="owner_passport_copy"
                                        class="form-control inputs" accept="application/pdf,image/png, image/jpeg">
                                    <div id="owner_passport_copy_file"></div>

                                </div>
                            </div>


                            <div class="row">
                                <div class="col-lg-4 mt-2">
                                    <label for="signed_listing_form" class="form-label">Contract A/Leasing
                                        Form/NOC*</label>
                                </div>
                                <div class="col-lg-8 mt-2">
                                    <input type="file" id="signed_listing_form" name="signed_listing_form"
                                        class="form-control inputs" accept="application/pdf,image/png, image/jpeg">

                                    <div id="signed_listing_form_file"></div>


                                </div>
                            </div>

                            

                            <div class="row">


                                

                                <div class="col-lg-4 mt-2">
                                    <label for="contract_date" class="form-label"> Contract Duration</label>
                                </div>
                                <div class="col-lg-4 mt-2">
                                    <input type="text" class="form-control inputs" id="contract_start"
                                        autocomplete="off" name="contract_start" placeholder="Select date range">
                                    
                                    <div class="invalid-feedback">
                                        Please select Contract date range.
                                    </div>
                                </div>
                                <div class="col-lg-4 mt-2">
                                    <input type="text" class="form-control inputs" id="contract_end"
                                        autocomplete="off" name="contract_end" placeholder="Select date range">

                                    <div class="invalid-feedback">
                                        Please select Contract date range.
                                    </div>
                                </div>

                                <div class="col-12 text-center">
                                    <div class="ContractDaysPlacing mt-2"></div>
                                </div>
                                
                                <div class="col-lg-12">
                                    <button type="button" class="btn btn-sm btn-outline-success"
                                        id="add-more-documents-btn" style="display: none;">Add more
                                    </button>
                                    <div class="row" id="more-documents"
                                        style="max-height: 250px;overflow-y: scroll;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="floor-plan-tab" role="tabpanel">
                            <input type="file" name="floor_plan" id="floor_plan"
                                accept="image/png, image/jpeg ,image/jpg" class="form-control inputs">
                            <div id="floor_plan_file" class="mt-2"></div>
                        </div>
                        <div class="tab-pane" id="other-media-tab" role="tabpanel">
                            <div class="row">
                                <div class="col-lg-4 mt-2">
                                    <label for="video_link" class="form-label">YouTube video link</label>
                                </div>
                                <div class="col-lg-8 mt-2">
                                    <input type="text" id="video_link" name="video_link"
                                        class="form-control inputs" placeholder="Enter youTube video link">
                                </div>
                                <div class="col-lg-4 mt-2">
                                    <label for="360_link" class="form-label">360 virtual tour link</label>
                                </div>
                                <div class="col-lg-8 mt-2">
                                    <input type="text" id="360_link" name="360_link" class="form-control inputs"
                                        placeholder="Enter 360 virtual tour link">
                                </div>
                                <div class="col-lg-4 mt-2">
                                    <label for="rera_qr" class="form-label">RERA QR </label>
                                </div>
                                <div class="col-lg-6 mt-2">
                                    <input type="text" id="qr_link" name="qr_link" class="form-control inputs"
                                        placeholder="RERA qr link">
                                </div>
                                <div class="col-lg-2 mt-2">
                                    <div id="qr_link_file">

                                    </div>

                                </div>

                            </div>
                        </div>

                        <div class="tab-pane" id="other-media-deleted" role="tabpanel">
                            <div class="row" id="deleted_documents">


                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- end col -->
        <div class="col-xl-3">
            <div class="card d-none" id="duplicate-check-tabs">
                <div class="card-header p-05rem">
                    <h6 class="card-title mb-0">Insights</h6>
                </div>

                <div class="card-body form-steps">
                        <div class="row">
                            <div class="col-lg-5 col-md-5 mt-2">
                                <label for="is_duplicated" class="form-label">Duplicated</label>
                            </div>
                            <div class="col-lg-7 col-lg-7 mt-2">
                                <label for="is_duplicated" class="is_duplicated" id="" class="form-label"></label>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-lg-5 col-md-5 mt-2">
                                <label for="duplicated_from"  class="form-label">Duplicated From</label>
                            </div>
                            <div class="col-lg-7 col-lg-7 mt-2">
                                <label for="duplicated_from" class="duplicated_from" id="" class="form-label"></label>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-lg-5 col-md-5 mt-2">
                                <label for="duplicate_reason" class="form-label">Duplicated Reason</label>
                            </div>
                            <div class="col-lg-7 col-lg-7 mt-2">
                                <label for="duplicate_reason" class="duplicate_reason" id="" class="form-label"></label>
                            </div>

                        </div>
                </div>
            </div>

            <div class="card d-none" id="actionTab">
                <div class="card-header p-05rem">
                    <h6 class="card-title mb-0">Actions </h6>
                </div>

                <div class="card-body form-steps">
                    <div class="row" id="state_div">

                        

                        <div class="col-lg-2 col-lg-2 mt-2 ">
                            <?php if(auth()->user()->can('Listing Duplicate Check')): ?>
                                <button type="button" id="checkDuplicateProperty"
                                    class="btn btn-sm btn-soft-danger text-danger action-delete"
                                    title="Check Duplicate Listing">
                                    <i class="mdi mdi-content-duplicate"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                        <div class="col-lg-3 col-lg-3 mt-3 ">
                        </div>
                        <div class="col-lg-7 col-lg-7 mt-2" id="is_live_status">
                        </div>
                    </div>
                    <div class="row">
                        
                        <div class="col-lg-12 col-lg-7 mt-2" id="media_changed_container">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-5 col-md-5 mt-2">
                            <label for="publish_status" class="form-label"> Publish Status*</label>
                        </div>
                        <div class="col-lg-7 col-lg-7 mt-2">
                            <select class="form-select select2 inputs" id="publish_status" name="publish_status"
                                required>
                                <option value="DRAFT" <?php if(!auth()->user()->can('Properties Draft')): ?> disabled <?php endif; ?>>
                                    Draft
                                </option>
                                <option value="PENDING" <?php if(!auth()->user()->can('Properties Pending')): ?> disabled <?php endif; ?>>Pending
                                </option>
                                <option value="PUBLISHED" <?php if(!auth()->user()->can('Properties Publish')): ?> disabled <?php endif; ?>>Published
                                </option>
                                <option value="POCKET" <?php if(!auth()->user()->can('Properties Pocket')): ?> disabled <?php endif; ?>>
                                    Pocket
                                </option>
                                <option value="UNPUBLISHED" <?php if(!auth()->user()->can('Properties Un-publish')): ?> disabled <?php endif; ?>>
                                    Unpublished
                                </option>
                                <option value="ARCHIVED" <?php if(!auth()->user()->can('Properties Archive')): ?> disabled <?php endif; ?>>Archived
                                </option>
                                <option value="REFUSED" <?php if(!auth()->user()->can('Properties Refuse')): ?> disabled <?php endif; ?>>
                                    Refused
                                </option>
                            </select>
                            <div class="invalid-feedback">
                                Please choose publish status.
                            </div>
                        </div>
                        <div class="col-12">
                            <span><small class="text-danger" id="admin_unpublish_reason"></small></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-5 col-md-5 mt-2">
                            <label for="completion_status" class="form-label">Completion Status*</label>
                        </div>
                        <div class="col-lg-7 col-md-7 mt-2">
                            <select class="form-select select2 inputs" name="completion_status"
                                id="completion_status" required>
                                <option></option>
                                <?php $__currentLoopData = \App\Models\Property::COMPLETION_STATUS_SELECT; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <div class="invalid-feedback">
                                Please choose property status.
                            </div>
                        </div>
                    </div>
                    <div class="row d-none" id="developerDiv">
                        <div class="col-lg-5 col-md-5 mt-2">
                            <label for="developer_id" class="form-label">Developer</label>
                        </div>
                        <div class="col-lg-7 col-md-7 mt-2">
                            <select class="form-select select2 inputs" name="developer_id" id="developer_id">
                                <option></option>
                                <?php $__currentLoopData = $developers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dev): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($dev->id); ?>"><?php echo e($dev->short_name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-5 col-md-5 mt-2">
                            <label for="build_year" class="form-label">Build Year</label>
                        </div>
                        <div class="col-lg-7 col-md-7 mt-2">
                            <input type="number" min="1900" max="2031" step="1" class="form-control inputs pocket-hide" id="build_year" name="build_year"
                                placeholder="Enter build year">
                            <div class="invalid-feedback">
                                Please choose build year.
                            </div>
                        </div>
                    </div>
                    <div class="row d-none" id="off_plan_dld">
                        <div class="col-lg-5 col-md-5 mt-2">
                            <label for="offplan_dld_wavier" class="form-label">DLD Wavier*</label>
                        </div>
                        <div class="col-lg-7 col-md-7 mt-2">
                            <input type="number" min="1" max="100" step="1"
                                   class="form-control inputs pocket-hide" id="offplan_dld_wavier" name="offplan_dld_wavier"
                                   placeholder="DLD waivers">
                            <div class="invalid-feedback">
                                Please choose 1-100.
                            </div>
                        </div>
                    </div>
                    <div class="row d-none" id="original_price_section">
                        <div class="col-lg-5 col-md-5 mt-2">
                            <label for="build_year" class="form-label">Original Price*</label>
                        </div>
                        <div class="col-lg-7 col-md-7 mt-2">

                            <input type="text" class="form-control inputs" name="offplan_original_price"
                                   id="offplan_original_price" placeholder="Original price" required>
                            <div class="invalid-feedback">
                                Please enter original price.
                            </div>

                        </div>
                    </div>
                    <div class="row d-none" id="amount_paid_section">
                        <div class="col-lg-5 col-md-5 mt-2">
                            <label for="offplan_amount_paid" class="form-label">Amount Paid*</label>
                        </div>
                        <div class="col-lg-7 col-md-7 mt-2">

                            <input type="text" class="form-control inputs" name="offplan_amount_paid"
                                   id="offplan_amount_paid" placeholder="Amount Paid" required>
                            <div class="invalid-feedback">
                                Please enter amount paid.
                            </div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-5 col-md-5 mt-2">
                            <label for="property_status_id" class="form-label"> Occupancy</label>
                        </div>
                        <div class="col-lg-7 col-md-7 mt-2">
                            <select class="form-select select2 inputs" name="property_status_id"
                                id="property_status_id">
                                <option value=""></option>
                                <?php $__currentLoopData = $status; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($st->id); ?>"><?php echo e($st->title); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <div class="invalid-feedback">
                                Please choose property status.
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-5 col-md-5 mt-2">
                            <label for="availability_date" class="form-label"> Availability Date</label>
                        </div>
                        <div class="col-lg-7 col-md-7 mt-2">
                            <input type="date" class="form-control inputs pocket-hide" id="availability_date"
                                data-provider="flatpickr" data-date-format="Y-m-d" name="availability_date"
                                placeholder="Enter availability date">
                            <div class="invalid-feedback">
                                Please enter availability date.
                            </div>
                        </div>
                    </div>
                    <div class="d-none" id="rented-area">
                        <div class="row">
                            <div class="col-lg-5 col-md-5 mt-2">
                                <label for="rented_at" class="form-label"> Rented at</label>
                            </div>
                            <div class="col-lg-7 col-md-7 mt-2">
                                <input type="number" class="form-control inputs" id="rented_at" name="rented_at"
                                    placeholder="Enter rented at">
                                <div class="invalid-feedback">
                                    Please enter rented at.
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-5 col-md-5 mt-2">
                                <label for="rented_until" class="form-label"> Rented until</label>
                            </div>
                            <div class="col-lg-7 col-md-7 mt-2">
                                <input type="date" class="form-control inputs" id="rented_until"
                                    name="rented_until" data-provider="flatpickr" data-date-format="Y-m-d"
                                    placeholder="Enter rented until">
                                <div class="invalid-feedback">
                                    Please enter rented until.
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-5 col-md-5 mt-2">
                                <label for="next_available" class="form-label"> Next available</label>
                            </div>
                            <div class="col-lg-7 col-md-7 mt-2">
                                <input type="date" class="form-control inputs" id="next_available"
                                    data-provider="flatpickr" data-date-format="Y-m-d" name="next_available"
                                    placeholder="Enter next available">
                                <div class="invalid-feedback">
                                    Please enter next available date.
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-5 col-md-5 mt-2">
                                <label for="remind" class="form-label"> Remind</label>
                            </div>
                            <div class="col-lg-7 col-md-7 mt-2">
                                <select class="form-select select2 inputs" name="remind" id="remind">
                                    <?php $__currentLoopData = \App\Models\Property::REMIND_SELECT; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <div class="invalid-feedback">
                                    Please choose remind.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-5 col-md-5 mt-2">
                            <label for="exclusive" class="form-label"> Exclusive*</label>
                        </div>
                        <div class="col-lg-7 col-md-7 mt-2">
                            <select class="form-select select2 inputs" name="exclusive" id="exclusive">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                            <div class="invalid-feedback">
                                Please choose exclusive or not.
                            </div>
                        </div>
                    </div>
                    <div class="row d-none" id="exlusiveDateRow">
                        <div class="col-lg-5 col-md-5 mt-2">
                            <label for="exclusive_dates" class="form-label"> Exclusive Dates</label>
                        </div>
                        <div class="col-lg-7 col-md-7 mt-2">
                            <input type="text" class="form-control inputs" id="exclusive_dates"
                                autocomplete="off" name="exclusive_dates" placeholder="Select date range">
                            <div class="invalid-feedback">
                                Please select exclusive date range.
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            
            <?php echo $__env->make('admin.listing.includes._portals', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            


            
            <div class="card d-none" id="messageTab">
                <div class="chat-wrapper d-lg-flex gap-1 p-1">
                    <!-- Start User chat -->
                    <div class="user-chat w-100 overflow-hidden">
                        <div class="chat-content d-lg-flex">
                            <!-- start chat conversation section -->
                            <div class="w-100 overflow-hidden position-relative">
                                <!-- conversation user -->
                                <div class="position-relative">
                                    <div class="position-relative" id="users-chat" style="display: block;">
                                        <div class="p-05rem user-chat-topbar">
                                            <div class="row align-items-center">
                                                <div class="col-sm-4 col-8">
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0 d-block d-lg-none me-3">
                                                            <a href="javascript: void(0);"
                                                                class="user-chat-remove fs-18 p-1"><i
                                                                    class="ri-arrow-left-s-line align-bottom"></i></a>
                                                        </div>
                                                        <div class="flex-grow-1 overflow-hidden">
                                                            <div class="d-flex align-items-center">

                                                                <div class="flex-grow-1 overflow-hidden">
                                                                    <h5 class="text-truncate mb-0 fs-16">Notes</h5>
                                                                    <p
                                                                        class="text-truncate text-muted fs-14 mb-0 userStatus">
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-sm-8 col-4">
                                                    <ul class="list-inline user-chat-nav text-end mb-0">
                                                        <li class="list-inline-item m-0">

                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end chat user head -->
                                        <div class="chat-conversation p-3 p-lg-4 " id="chat-conversation"
                                            data-simplebar="init" style="max-height:325px;">
                                            <div class="simplebar-wrapper" style="margin: -24px;">
                                                <div class="simplebar-height-auto-observer-wrapper">
                                                    <div class="simplebar-height-auto-observer"></div>
                                                </div>
                                                <div class="simplebar-mask">
                                                    <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
                                                        <div class="simplebar-content-wrapper" id="chat_container"
                                                            tabindex="0" role="region"
                                                            aria-label="scrollable content"
                                                            style="height: 100%; overflow: hidden scroll;">
                                                            <div class="simplebar-content" style="padding: 8px;">
                                                                <div id="elmLoader"></div>
                                                                <ul class="list-unstyled chat-conversation-list"
                                                                    id="users-conversation">
                                                                </ul>
                                                                <!-- end chat-conversation-list -->
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="simplebar-placeholder" style="width: auto; "></div>
                                            </div>
                                            <div class="simplebar-track simplebar-horizontal"
                                                style="visibility: hidden;">
                                                <div class="simplebar-scrollbar" style="width: 0px; display: none;">
                                                </div>
                                            </div>
                                            <div class="simplebar-track simplebar-vertical"
                                                style="visibility: visible;">
                                                <div class="simplebar-scrollbar"
                                                    style="height: 25px; display: block; transform: translate3d(0px, 87px, 0px);">
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <!-- end chat-conversation -->
                                    <div class="chat-input-section">
                                        <div id="showWhenImage" class="d-none " style="border: 1px dotted #898888">
                                            <div class="img-thumbnail bg-white">
                                                <img id="blah" src="" height="auto" width="80"
                                                    style="max-height: 250px" class="rounded" />
                                                <span class="cross-icon" id="deleteImage">&times;</span>
                                            </div>


                                        </div>


                                        <div class="col-auto d-none">
                                            <div class="chat-input-links me-2">
                                                <div class="links-list-item">
                                                    <button type="button"
                                                        class="btn btn-link text-decoration-none emoji-btn"
                                                        id="emoji-btn">
                                                        <i class="bx bx-smile align-middle"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row g-0 align-items-center">

                                            <div class="col-auto">
                                                <div class="chat-input-links me-2">
                                                    <div class="links-list-item">
                                                        <div class="upload mt-3" id="uploadDiv">
                                                            <label class="upload-area">
                                                                <input type="file" name="mediainput"
                                                                    id="mediaInput"
                                                                    accept="image/png, image/gif, image/jpeg">
                                                                <span class=" upload-button align-middle">
                                                                    <i class=" bx bx-paperclip align-middle"></i>
                                                                </span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="chat-input-feedback">
                                                    Please Enter a Message
                                                </div>
                                                <textarea class="form-control chat-input" id="chat-input" rows="1" placeholder="Enter a note"></textarea>
                                            </div>


                                            <div class="col-auto">
                                                <div class="chat-input-links ms-2">
                                                    <div class="links-list-item">
                                                        <button type="button"
                                                            class="btn btn-success chat-send waves-effect waves-light"
                                                            id="addNoteChatButton">
                                                            <span></span>

                                                            <span class="d-flex align-items-center">
                                                                <span
                                                                    class="spinner-border flex-shrink-0 custom-spinner-border d-none"
                                                                    role="status" id="messegeLoading">
                                                                    <span class="visually-hidden">Loading...</span>
                                                                </span>
                                                            </span>

                                                            <i class="ri-send-plane-2-fill align-bottom"
                                                                id="messageSend"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>


                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/listing/includes/form.blade.php ENDPATH**/ ?>