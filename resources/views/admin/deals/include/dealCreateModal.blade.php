<div class="modal fade" id="addDealForm" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header p-3">
                <h5 class="modal-title text-uppercase" id="exampleModalLabel">Verify your Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="dealCreateForm" class="checkout-tab">
                <div class="modal-body p-0">
                    <div class="step-arrow-nav">
                        <ul class="nav nav-pills nav-justified custom-nav" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link p-3 active" id="property-selection-tab" data-bs-toggle="pill" data-bs-target="#property-selection-step" type="button" role="tab" aria-controls="pills-bill-info" aria-selected="true">Property Details</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link p-3" id="property-details-tab" data-bs-toggle="pill" data-bs-target="#pills-bill-address" type="button" role="tab" aria-controls="pills-bill-address" aria-selected="false">Agent Details</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link p-3" id="pills-payment-tab" data-bs-toggle="pill" data-bs-target="#pills-payment" type="button" role="tab" aria-controls="pills-payment" aria-selected="false">Document Verification</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link p-3" id="pills-finish-tab" data-bs-toggle="pill" data-bs-target="#pills-finish" type="button" role="tab" aria-controls="pills-finish" aria-selected="false">Verified</button>
                            </li>
                        </ul>
                    </div>
                </div>
                <!--end modal-body-->
                <div class="modal-body">
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="property-selection-step" role="tabpanel" aria-labelledby="property-selection-tab">
                            <div class="row g-3">
                                <div class="col-lg-6">
                                    <div>
                                        <label for="property" class="form-label">Property</label>
                                        <select class="form-control eventInputs" name="property_id" id="deal_property_id" required>
                                        </select>
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-lg-6">
                                    <div>
                                        <label for="lead" class="form-label">Lead</label>
                                        <select class="form-control eventInputs" name="lead_id" id="deal_lead_id">
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6 d-none" id="property_details_container">
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
                                        <li class="list-group-item d-flex justify-content-between lh-sm">
                                            <div>
                                                <h6 class="my-0">Location</h6>
                                            </div>
                                            <span class="text-muted" id="property_location"></span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-lg-6 d-none" id="contact_details_container">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="fs-14 text-primary mb-0">Contact Details</h5>
                                    </div>
                                    <ul class="list-group mb-3">
                                        <li class="list-group-item d-flex justify-content-between lh-sm">
                                            <div>
                                                <h6 class="my-0">Name</h6>
                                            </div>
                                            <span class="text-muted" id="contact_name"></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between lh-sm">
                                            <div>
                                                <h6 class="my-0">Mobile</h6>
                                            </div>
                                            <span class="text-muted" id="contact_mobile"></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between lh-sm">
                                            <div>
                                                <h6 class="my-0">Email</h6>
                                            </div>
                                            <span class="text-muted" id="contact_email"></span>
                                        </li>
                                    </ul>
                                </div>
                                <!--end col-->
                                <div class="col-lg-12">
                                    <div class="d-flex align-items-start gap-3 mt-3">
                                        <button type="button" class="btn btn-primary btn-label right ms-auto nexttab" data-nexttab="property-details-tab"><i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i> Next Step</button>
                                    </div>
                                </div>
                                <!--end col-->
                            </div>
                            <!--end row-->
                        </div>
                        <!-- end tab pane -->
                        <div class="tab-pane fade" id="pills-bill-address" role="tabpanel" aria-labelledby="property-details-tab">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="banknameInput" class="form-label">Bank Name</label>
                                        <input type="text" class="form-control" id="banknameInput" placeholder="Enter your bank name">
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="branchInput" class="form-label">Branch</label>
                                        <input type="text" class="form-control" id="branchInput" placeholder="Branch">
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <label for="accountnameInput" class="form-label">Account Holder Name</label>
                                        <input type="text" class="form-control" id="accountnameInput" placeholder="Enter account holder name">
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="accountnumberInput" class="form-label">Account Number</label>
                                        <input type="number" class="form-control" id="accountnumberInput" placeholder="Enter account number">
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="ifscInput" class="form-label">IFSC</label>
                                        <input type="number" class="form-control" id="ifscInput" placeholder="IFSC">
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-lg-12">
                                    <div class="hstack align-items-start gap-3 mt-4">
                                        <button type="button" class="btn btn-light btn-label previestab" data-previous="property-selection-tab"><i class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i>Back to Personal Info</button>
                                        <button type="button" class="btn btn-primary btn-label right ms-auto nexttab" data-nexttab="pills-payment-tab"><i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>Next Step</button>
                                    </div>
                                </div>
                                <!--end col-->
                            </div>
                        </div>
                        <!-- end tab pane -->
                        <div class="tab-pane fade" id="pills-payment" role="tabpanel" aria-labelledby="pills-payment-tab">
                            <h5 class="mb-3">Choose Document Type</h5>
                            <div class="d-flex gap-2">
                                <div>
                                    <input type="radio" class="btn-check" id="passport" checked name="choose-document">
                                    <label class="btn btn-outline-info" for="passport">Passport</label>
                                </div>
                                <div>
                                    <input type="radio" class="btn-check" id="aadhar-card" name="choose-document">
                                    <label class="btn btn-outline-info" for="aadhar-card">Aadhar Card</label>
                                </div>
                                <div>
                                    <input type="radio" class="btn-check" id="pan-card" name="choose-document">
                                    <label class="btn btn-outline-info" for="pan-card">Pan Card</label>
                                </div>
                                <div>
                                    <input type="radio" class="btn-check" id="other" name="choose-document">
                                    <label class="btn btn-outline-info" for="other">Other</label>
                                </div>
                            </div>
                            <div class="dropzone d-flex align-items-center">
                                <div class="fallback">
                                    <input name="file" type="file" multiple="multiple">
                                </div>
                                <div class="dz-message needsclick text-center">
                                    <div class="mb-3">
                                        <i class="display-4 text-muted ri-upload-cloud-2-fill"></i>
                                    </div>
                                    <h4>Drop files here or click to upload.</h4>
                                </div>
                            </div>
                            <ul class="list-unstyled mb-0" id="dropzone-preview">
                                <li class="mt-2" id="dropzone-preview-list">
                                    <div class="border rounded">
                                        <div class="d-flex p-2">
                                            <div class="flex-shrink-0 me-3">
                                                <div class="avatar-sm bg-light rounded">
                                                    <img src="#" alt="" data-dz-thumbnail class="img-fluid rounded d-block" />
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="pt-1">
                                                    <h5 class="fs-14 mb-1" data-dz-name>&nbsp;</h5>
                                                    <p class="fs-13 text-muted mb-0" data-dz-size></p>
                                                    <strong class="error text-danger" data-dz-errormessage></strong>
                                                </div>
                                            </div>
                                            <div class="flex-shrink-0 ms-3">
                                                <button data-dz-remove class="btn btn-sm btn-danger">Delete</button>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                            <!-- end dropzon-preview -->
                            <div class="d-flex align-items-start gap-3 mt-4">
                                <button type="button" class="btn btn-light btn-label previestab" data-previous="property-details-tab"><i class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i>Back to Bank Details</button>
                                <button type="button" class="btn btn-primary btn-label right ms-auto nexttab" data-nexttab="pills-finish-tab"><i class="ri-save-line label-icon align-middle fs-16 ms-2"></i>Submit</button>
                            </div>
                        </div>
                        <!-- end tab pane -->
                        <div class="tab-pane fade" id="pills-finish" role="tabpanel" aria-labelledby="pills-finish-tab">
                            <div class="row text-center justify-content-center py-4">
                                <div class="col-lg-11">
                                    <div class="mb-4">
                                        <lord-icon src="https://cdn.lordicon.com/lupuorrc.json" trigger="loop" colors="primary:#0ab39c,secondary:#405189" style="width:120px;height:120px"></lord-icon>
                                    </div>
                                    <h5>Verification Completed</h5>
                                    <p class="text-muted mb-4">To stay verified, don't remove the meta tag form your site's home page. To avoid losing verification, you may want to add multiple methods form the <span class="fw-medium">Crypto > KYC Application.</span></p>
                                    <div class="hstack justify-content-center gap-2">
                                        <button type="button" class="btn btn-ghost-success" data-bs-dismiss="modal">Done <i class="ri-thumb-up-fill align-bottom me-1"></i></button>
                                        <button type="button" class="btn btn-primary"><i class="ri-home-4-line align-bottom ms-1"></i> Back to Home</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end tab pane -->
                    </div>
                    <!-- end tab content -->
                </div>
                <!--end modal-body-->
            </form>
        </div>
    </div>
</div>
@push('partial-scripts')
    <script src="{{ URL::asset('/assets/js/pages/crypto-kyc.init.js') }}"></script>
    <script type="text/javascript">
        // Property Select 2
        $("#deal_property_id").select2({
            placeholder: "Select Property",
            allowClear: true,
            dropdownParent: $('#addDealForm'),
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
                                owner_id: item?.owner?.id,
                                owner_name: item?.owner?.first_name+' '+item?.owner?.last_name,
                                owner_mobile: item?.owner?.mobile,
                                owner_email: item?.owner.email
                            }
                        })
                    }
                }
            }
        });
        // Property Select2 ends here

        // Lead Select2 here
        $("#deal_lead_id").select2({
            placeholder: "Select Lead",
            allowClear: true,
            dropdownParent: $('#addDealForm'),
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
                                email: item.email,
                            }
                        })
                    }
                }
            }
        });

        // Lead select2 ends here

        // On change of property showing contact details
        $("#deal_property_id").change(function(){
            let property = $(this).select2('data')[0];
            if(property){
              $("#property_reference_no").text(property.ref_no);  
              $("#property_category").text(property.category);  
              $("#property_unit_no").text(property.unit_no);
              $("#property_location").text(property.location); 
              $("#contact_name").text(property.owner_name);
              $("#contact_email").text(property.owner_email);
              $("#contact_mobile").text(property.owner_mobile);

              $("#property_details_container").removeClass("d-none"); 
              $("#contact_details_container").removeClass("d-none");
            }
            else{
               $("#property_details_container").addClass("d-none");  
            }
            
        })
    </script>
@endpush