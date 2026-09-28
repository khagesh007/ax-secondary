<div class="modal fade" id="deal-wizard-modal" tabindex="-1" aria-labelledby="fullscreeexampleModalLabel" aria-modal="true" role="dialog" style="display: block;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fullscreeexampleModalLabel">Add deal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-6">
                        
                        <div class="card">
                            <div class="card-body form-steps">
                                <form action="#" id="form-steps" autocomplete="off">
                                    <div class="text-center pt-3 pb-4 mb-1">
                                        <img src="assets/images/logo-dark.png" alt="" height="17">
                                    </div>
                                    <div class="step-arrow-nav mb-4">

                                        <ul class="nav nav-pills custom-nav nav-justified" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active" id="steparrow-gen-info-tab" data-bs-toggle="pill" data-bs-target="#steparrow-gen-info" type="button" role="tab" aria-controls="steparrow-gen-info" aria-selected="false" data-position="0" tabindex="-1">General</button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="steparrow-description-info-tab" data-bs-toggle="pill" data-bs-target="#steparrow-description-info" type="button" role="tab" aria-controls="steparrow-description-info" aria-selected="true" data-position="1">Description</button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="pills-experience-tab" data-bs-toggle="pill" data-bs-target="#pills-experience" type="button" role="tab" aria-controls="pills-experience" aria-selected="false" data-position="2" tabindex="-1">Finish</button>
                                            </li>
                                        </ul>
                                    </div>

                                    <div class="tab-content">
                                        <div class="tab-pane fade show active" id="steparrow-gen-info" role="tabpanel" aria-labelledby="steparrow-gen-info-tab">
                                            <div>
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="mb-3">
                                                            <label class="form-label" for="steparrow-gen-info-email-input">Property</label>
                                                            <select class="form-control eventInputs" name="
                                                            property_id" id="deal_property_id" required>
                                                            </select>
                                                            <div class="invalid-feedback">Please select a property</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label" for="steparrow-gen-info-password-input">Lead</label>
                                                    <select class="form-control eventInputs" name="
                                                    lead_id" id="deal_lead_id">
                                                    </select>
                                                    <div class="invalid-feedback">Please enter a password</div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-start gap-3 mt-4">
                                                <button type="button" class="btn btn-success btn-label right ms-auto nexttab nexttab" data-nexttab="steparrow-description-info-tab"><i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>Go to more info</button>
                                            </div>
                                        </div>
                                        <!-- end tab pane -->

                                        <div class="tab-pane fade" id="steparrow-description-info" role="tabpanel" aria-labelledby="steparrow-description-info-tab">
                                            <div>
                                                <div class="mb-3">
                                                    <label for="formFile" class="form-label">Upload Image</label>
                                                    <input class="form-control" type="file" id="formFile">
                                                </div>
                                                <div>
                                                    <label class="form-label" for="des-info-description-input">Description</label>
                                                    <textarea class="form-control" placeholder="Enter Description" id="des-info-description-input" rows="3" required=""></textarea>
                                                    <div class="invalid-feedback">Please enter a description</div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-start gap-3 mt-4">
                                                <button type="button" class="btn btn-light btn-label previestab" data-previous="steparrow-gen-info-tab"><i class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i> Back to General</button>
                                                <button type="button" class="btn btn-success btn-label right ms-auto nexttab nexttab" data-nexttab="pills-experience-tab"><i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>Submit</button>
                                            </div>
                                        </div>
                                        <!-- end tab pane -->

                                        <div class="tab-pane fade" id="pills-experience" role="tabpanel" aria-labelledby="#pills-experience-tab">
                                            <div class="text-center">

                                                <div class="avatar-md mt-5 mb-4 mx-auto">
                                                    <div class="avatar-title bg-light text-success display-4 rounded-circle">
                                                        <i class="ri-checkbox-circle-fill"></i>
                                                    </div>
                                                </div>
                                                <h5>Well Done !</h5>
                                                <p class="text-muted">You have Successfully Signed Up</p>
                                            </div>
                                        </div>
                                        <!-- end tab pane -->
                                    </div>
                                    <!-- end tab content -->
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fs-14 text-primary mb-0">Property Details</h5>
                        </div>
                        <ul class="list-group mb-3">
                            <li class="list-group-item d-flex justify-content-between lh-sm">
                                <div>
                                    <h6 class="my-0">Reference #</h6>
                                </div>
                                <span class="text-muted" id="property_reference_no"></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between lh-sm">
                                <div>
                                    <h6 class="my-0">Unit #</h6>
                                </div>
                                <span class="text-muted" id="property_unit_no"></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between lh-sm">
                                <div>
                                    <h6 class="my-0">Category</h6>
                                </div>
                                <span class="text-muted" id="property_category"></span>
                            </li>
                        </ul>
                    </div>
                    <div class="col-lg-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fs-14 text-primary mb-0">Contact Details</h5>
                        </div>
                        <ul class="list-group mb-3">
                            <li class="list-group-item d-flex justify-content-between lh-sm">
                                <div>
                                    <h6 class="my-0">Name</h6>
                                </div>
                                <span class="text-muted" id="property_reference_no"></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between lh-sm">
                                <div>
                                    <h6 class="my-0">Mobile</h6>
                                </div>
                                <span class="text-muted" id="property_unit_no"></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between lh-sm">
                                <div>
                                    <h6 class="my-0">Email</h6>
                                </div>
                                <span class="text-muted" id="property_category"></span>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="modal-footer">
                <a href="javascript:void(0);" class="btn btn-link link-success fw-medium" data-bs-dismiss="modal"><i class="ri-close-line me-1 align-middle"></i> Close</a>
                <button type="button" class="btn btn-primary ">Save changes</button>
            </div>
        </div>
    </div>
</div>
<script src="{{ URL::asset('/assets/js/pages/form-wizard.init.js') }}" async></script>
<script type="text/javascript">
    $("#deal_property_id").select2({
        placeholder: "Select Property",
        allowClear: true,
        dropdownParent: $('#deal-wizard-modal'),
        ajax: {
            url: '{{ route('ajax.agentPropertySelect2') }}',
            dataType: 'json',
            type: 'GET',
            processResults({
                data
            }) {

                return {
                    results: $.map(data, function(item) {
                        return  {
                            text: `${item.ref_no} - ${item?.category} ${item?.unit_no} ${item?.location}`,
                            id: item.id,
                            title: item.title,
                            category: item.category,
                            ref_no: item.ref_no,
                            unit_no: item.unit_no,
                            location: item.location,
                        }
                    })
                }
            }
        }
    });
    $("#deal_property_id").change(function(){
        let property = $(this).select2('data')[0];
        if(property){
          $("#property_reference_no").text(property.ref_no);  
          $("#property_category").text(property.category);  
          $("#property_unit_no").text(property.unit_no);
          $("#property_location").text(property.location);   
        }
        
    })
    $("#deal_lead_id").select2({
        placeholder: "Select Lead",
        allowClear: true,
        dropdownParent: $('#deal-wizard-modal'),
        ajax: {
            url: '{{ route('ajax.agentLeadSelect2') }}',
            dataType: 'json',
            type: 'GET',
            processResults({
                data
            }) {

                return {
                    results: $.map(data, function(item) {
                        return  {
                            text: `${item.first_name || ""} ${item.last_name || ""}`,
                            id: item.id,
                            first_name: item.first_name,
                            last_name: item.last_name,
                            full_name: item.first_name +' '+ item.last_name,
                            mobile: item.mobile,
                            email: item.email
                        }
                    })
                }
            }
        }
    })
</script>
