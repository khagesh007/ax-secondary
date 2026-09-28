<div class="modal flip" id="shootModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-danger p-3">
                <h5 class="modal-title" id="event-form-modal-title">Property Photo Shoot</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-hidden="true"></button>
            </div>
            <div class="modal-body p-4">
                <form class="needs-validation" name="shoot-form" id="shoot-form" novalidate>
                    <div class="row event-form">
                        <input type="hidden" id="shoot_job_id" name="id"/>
                        <div class="col-12">
                            <div class="mb-3">
                                <div class="row">

                                        <?php if(auth()->user()->hasRole(['Admin','Super Admin'])): ?>
                                        <div class="col-2 agent_select_input">
                                            <label class="form-label" for="agent_id">User*</label>
                                        </div>
                                        <div class="col-4 agent_select_input">
                                            <select class="form-select eventInputs select2" name="agent_id"
                                                    id="shoot_agent_id"
                                                    required>
                                                <?php if($agent != ''): ?>

                                                    <option value="<?php echo e($agent->id); ?>" selected><?php echo e($agent->name); ?></option>
                                                <?php endif; ?>
                                            </select>

                                            <div class="invalid-feedback">Please select a valid agent</div>
                                    </div>
                                        <div class="col-1">
                                            <label class="form-label" for="type">Property*</label>
                                        </div>
                                        <div class="col-5">
                                            <select class="form-control" name="property_id"
                                                    id="shoot_property_id">
                                                <?php if($property != ''): ?>
                                                    <option value="<?php echo e($property->id); ?>" selected><?php echo e($property->ref_no); ?> - <?php echo e($property->title); ?></option>
                                                <?php endif; ?>
                                            </select>
                                            <div class="invalid-feedback">Please select a property</div>
                                        </div>
                                            <?php else: ?>
                                        <div class="col-2 d-none agent_select_input">
                                            <label class="form-label" for="agent_id">User*</label>
                                        </div>
                                        <div class="col-4 d-none agent_select_input">
                                            <select class="form-select eventInputs select2" readonly="" name="agent_id"
                                                    id="shoot_agent_id"
                                                    required>
                                                <option value="<?php echo e(auth()->user()->id); ?>" selected><?php echo e(auth()->user()->name); ?></option>
                                            </select>
                                        </div>
                                        <div class="col-2">
                                            <label class="form-label" for="type">Property</label>
                                        </div>
                                        <div class="col-10">
                                            <select class="form-control" name="property_id"
                                                    id="shoot_property_id">
                                                <?php if($property != ''): ?>
                                                    <option value="<?php echo e($property->id); ?>" selected><?php echo e($property->ref_no); ?> - <?php echo e($property->title); ?></option>
                                                <?php endif; ?>
                                            </select>
                                            <div class="invalid-feedback">Please select a property</div>
                                        </div>
                                        <?php endif; ?>


                                </div>
                            </div>
                        </div>



                        <div class="col-12">
                            <div class="mb-3">
                                <div class="row">
                                    <div class="col-2">
                                        <label class="form-label" for="shoot_input_title">Title*</label>
                                    </div>
                                    <div class="col-10">
                                        <input class="form-control eventInputs" placeholder="Enter title"
                                               type="text"
                                               name="title" id="shoot_input_title" value="<?php if($property != ''): ?>Property shoot at - (<?php echo e($property->ref_no); ?>) - <?php echo e($property->title); ?> <?php endif; ?>" required/>
                                        <div class="invalid-feedback">Please provide a valid event title
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-12">
                            <div class="mb-3">
                                <div class="row">
                                    <div class="col-2">
                                        <label class="form-label" for="access_information">
                                            Access Information *</label>
                                    </div>
                                    <div class="col-10">
                                                    <textarea class="form-control" name="access_information"
                                                              id="shoot_access_information"
                                                              placeholder="Enter access information" rows="3"
                                                              spellcheck="false"></textarea>
                                        <span class="text-muted fs-12">Access Instructions: (Sample: NOC, Tenant/Owner will assist, Door is Open, Keys with the agent)

For any other reason please specify</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <div class="row">
                                    <div class="col-2">
                                        <label class="form-label" for="description">
                                            Additional details</label>
                                    </div>
                                    <div class="col-10">
                                                    <textarea class="form-control" name="description"
                                                              id="shoot_description"
                                                              placeholder="Enter any additional details" rows="3"
                                                              spellcheck="false"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 d-none">
                            <div class="mb-3">
                                <div class="row">
                                    <div class="col-2">
                                        <label class="form-label" for="requested_tags">
                                           Requested Tags*</label>
                                    </div>
                                    <div class="col-10">
                                                    <select class="form-select" id="shoot_requested_tags"  name="requested_tags[]"
                                                            id="shoot_tags" multiple="multiple">
                                                        <option value="Standard Photos" selected>Standard Photos</option>
                                                        <option value="Video">Standard Video</option>
                                                        <option value="Instagram Video">Instagram Video</option>
                                                        <option value="Reel Video">Reel Video</option>
                                                        <option value="Video 360">Video 360</option>
                                                    </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <div class="row">
                                    <div class="col-2">
                                        <label class="form-label" for="start_time">Preferred Time*</label>
                                    </div>
                                    <div class="col-4">
                                        <div class="input-group">
                                            <input id="shoot_preferred_time" title="Preferred time for photo shoot" name="preferred_time" type="text"
                                                   class="form-control"
                                                   placeholder="Select date time" required readonly>
                                            <span class="input-group-text"><i
                                                    class="ri-time-line"></i></span>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <div class="row">
                                    <div class="col-2">
                                        <label class="form-label" title="Is property furnished ?" for="start_time">Furnished</label>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-check form-switch form-switch-lg form-switch-success" dir="ltr">
                                            <input type="checkbox" id="shoot_furnished"  name="furnished" class="form-check-input"></div>

                                    </div>
                                    <div class="col-2">
                                        <label class="form-label" title="Is property cleaned" for="cleaned">Cleaned</label>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-check form-switch form-switch-lg form-switch-success" dir="ltr">
                                            <input type="checkbox"  id="shoot_cleaned" name="cleaned" class="form-check-input"></div>


                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="col-12  ">
                            <div class="mb-3">
                                <div class="row">
                                    <div class="col-2 d-none">
                                        <label class="form-label" title="Is it a luxury property shoot?" for="start_time">Luxury</label>
                                    </div>
                                    <div class="col-4 d-none">
                                        <div class="form-check form-switch form-switch-lg form-switch-success" dir="ltr">
                                            <input type="checkbox"  id="shoot_luxury" name="luxury" class="form-check-input"></div>
                                        <!-- Inline Radios -->

                                    </div>
                                    <div class="col-2">
                                        <label class="form-label"  title="Agent will be present at location" for="shoot_agent_present">Agent Present</label>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-check form-switch form-switch-lg form-switch-success" dir="ltr">
                                            <input type="checkbox"  id="shoot_agent_present" name="agent_present" class="form-check-input"></div>
                                        <!-- Inline Radios -->

                                    </div>
                                    <div class="col-2">
                                        <label class="form-label" title="DEWA" for="DEWA">DEWA</label>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-check form-switch form-switch-lg form-switch-success" dir="ltr">
                                            <input type="checkbox"  name="dewa" id="shoot_dewa" class="form-check-input"></div>

                                    </div>


                                </div>
                            </div>
                        </div>






                    </div>
                    <div class="hstack gap-2 d-flex">
                        <div class="flex-grow-1 mt-2">


                        </div>
                        <div class="flex-shrink-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="d-none" id="btn-save-shoot"></button>
                        <button type="button" class="btn btn-success action-save" onclick="showShootTNCModal()" data-bs-target="#shootModalTNC" data-bs-toggle="modal" data-bs-dismiss="modal">Send Request
                        </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal flip" id="shootModalTNC" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog  modal-dialog-scrollable modal-lg modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-danger p-3">
                <h5 class="modal-title" id="event-form-modal-title">Property Photo Shoot Term & Conditions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-hidden="true"></button>
            </div>
            <div class="modal-body p-4">

                    <h6 class="fs-15">Give your text a good structure</h6>
                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <i class="ri-checkbox-circle-fill text-success"></i>
                        </div>
                        <div class="flex-grow-1 ms-2">
                            <p class="text-muted mb-0">Raw denim you probably haven't heard of them jean shorts Austin.
                                Nesciunt tofu stumptown aliqua, retro synth master cleanse.</p>
                        </div>
                    </div>
                    <div class="d-flex mt-2">
                        <div class="flex-shrink-0">
                            <i class="ri-checkbox-circle-fill text-success"></i>
                        </div>
                        <div class="flex-grow-1 ms-2 ">
                            <p class="text-muted mb-0">Too much or too little spacing, as in the example below, can make things unpleasant for the reader. The goal is to make your text as comfortable to read as possible. </p>
                        </div>
                    </div>
                    <div class="d-flex mt-2">
                        <div class="flex-shrink-0">
                            <i class="ri-checkbox-circle-fill text-success"></i>
                        </div>
                        <div class="flex-grow-1 ms-2 ">
                            <p class="text-muted mb-0">In some designs, you might adjust your tracking to create a certain artistic effect. It can also help you fix fonts that are poorly spaced to begin with.</p>
                        </div>
                    </div>
                    <div class="d-flex mt-2">
                        <div class="flex-shrink-0">
                            <i class="ri-checkbox-circle-fill text-success"></i>
                        </div>
                        <div class="flex-grow-1 ms-2 ">
                            <p class="text-muted mb-0">For that very reason, I went on a quest and spoke to many different professional graphic designers and asked them what graphic design tips they live.</p>
                        </div>
                    </div>
                    <div class="d-flex mt-2">
                        <div class="flex-shrink-0">
                            <i class="ri-checkbox-circle-fill text-success"></i>
                        </div>
                        <div class="flex-grow-1 ms-2 ">
                            <p class="text-muted mb-0">You've probably heard that opposites attract. The same is true for fonts. Don't be afraid to combine font styles that are different but complementary, like sans serif with serif, short with tall, or decorative with simple. Qui photo booth letterpress, commodo enim craft beer mlkshk aliquip jean shorts ullamco ad vinyl cillum PBR.</p>
                        </div>
                    </div>
                    <div class="d-flex mt-2">
                        <div class="flex-shrink-0">
                            <i class="ri-checkbox-circle-fill text-success"></i>
                        </div>
                        <div class="flex-grow-1 ms-2 ">
                            <p class="text-muted mb-0">For that very reason, I went on a quest and spoke to many different professional graphic designers and asked them what graphic design tips they live.</p>
                        </div>
                    </div>
                    <h6 class="fs-16 my-3">Graphic Design</h6>
                    <div class="d-flex mt-2">
                        <div class="flex-shrink-0">
                            <i class="ri-checkbox-circle-fill text-success"></i>
                        </div>
                        <div class="flex-grow-1 ms-2 ">
                            <p class="text-muted mb-0">Opposites attract, and that’s a fact. It’s in our nature to be interested in the unusual, and that’s why using contrasting colors in Graphic Design is a must. It’s eye-catching, it makes a statement, it’s impressive graphic design. Increase or decrease the letter spacing depending.</p>
                        </div>
                    </div>
                    <div class="d-flex mt-2">
                        <div class="flex-shrink-0">
                            <i class="ri-checkbox-circle-fill text-success"></i>
                        </div>
                        <div class="flex-grow-1 ms-2 ">
                            <p class="text-muted mb-0">Trust fund seitan letterpress, keytar raw denim keffiyeh etsy art party before they sold out master cleanse gluten-free squid scenester freegan cosby sweater.</p>
                        </div>
                    </div>
                    <div class="d-flex mt-2">
                        <div class="flex-shrink-0">
                            <i class="ri-checkbox-circle-fill text-success"></i>
                        </div>
                        <div class="flex-grow-1 ms-2 ">
                            <p class="text-muted mb-0">Just like in the image where we talked about using multiple fonts, you can see that the background in this graphic design is blurred. Whenever you put text on top of an image, it’s important that your viewers can understand.</p>
                        </div>
                    </div>
                    <div class="d-flex mt-2">
                        <div class="flex-shrink-0">
                            <i class="ri-checkbox-circle-fill text-success"></i>
                        </div>
                        <div class="flex-grow-1 ms-2 ">
                            <p class="text-muted mb-0">Keytar raw denim keffiyeh etsy art party before they sold out master cleanse gluten-free squid scenester freegan cosby sweater.</p>
                        </div>
                    </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-soft-danger" onclick="showShootModal()" id="goBackToShootForm">Go Back</button>
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary action-save" id="shootModalSubmitTNC">Accept & Send Request</button>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/jobs/shoot/modal.blade.php ENDPATH**/ ?>