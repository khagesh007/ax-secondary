<div id="addContactModal" class="modal zoomIn" tabindex="-1" aria-labelledby="addOwnerModalLabel"
    aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-soft-danger">
                <h5 class="modal-title" id="addContactModalLabel">Add Contact (Owner)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body ">
                <div class="">

                    <div class="card-body form-steps p-0">


                        <div class="step-arrow-nav mb-4 non-clickables">

                            <ul class="nav nav-pills custom-nav nav-justified" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="steparrow-gen-info-tab" data-bs-toggle="pill"
                                        data-bs-target="#steparrow-gen-info" type="button" role="tab"
                                        aria-controls="steparrow-gen-info" aria-selected="true">Owner Details</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link " id="steparrow-description-info-tab"
                                        data-bs-toggle="pill" data-bs-target="#steparrow-description-info"
                                        type="button" role="tab" aria-controls="steparrow-description-info"
                                        aria-selected="false">Listing Details</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pills-experience-tab" data-bs-toggle="pill"
                                        data-bs-target="#pills-experience" type="button" role="tab"
                                        aria-controls="pills-experience" aria-selected="false">Finish</button>
                                </li>
                            </ul>
                        </div>

                        <div class="tab-content">

                            <div class="tab-pane fade show active" id="steparrow-gen-info" role="tabpanel"
                                aria-labelledby="steparrow-gen-info-tab">
                                <form id="addContactForm" class="needs-validation" novalidate>
                                    <div>
                                        <input type="hidden" name="listing_lead_id" id="listing_lead_id">
                                        {{-- <input type="hidden" name="agent_id" id="contact_agent_id"> --}}
                                        <input type="hidden" name="contact_id" id="contact_contact_id">


                                        <div class="row">

                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="listing_agent_id">Agent</label>
                                                    <select class="form-control select2" name="agent_id"
                                                        id="listing_agent_id">
                                                        @foreach ($agents ?? [] as $src)
                                                            <option value="{{ $src->id }}">
                                                                {{ $src->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <div class="invalid-feedback">Please enter Agent</div>
                                                </div>
                                            </div>

                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="listing_first_name">First
                                                        Name</label>
                                                    <input type="text" class="form-control"
                                                        id="listing_first_name" placeholder="Enter First name"
                                                        required="" name="first_name">
                                                    <div class="invalid-feedback">Please enter First name</div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="listing_last_name">Last
                                                        Name</label>
                                                    <input type="text" class="form-control" id="listing_last_name"
                                                        placeholder="Enter Last name" required=""
                                                        name="last_name">
                                                    <div class="invalid-feedback">Please enter Last name</div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="listing_email">Email</label>
                                                    <input type="email" class="form-control" id="listing_email"
                                                        placeholder="Enter email" required="" name="email">
                                                    <div class="invalid-feedback">Please enter an email address</div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="listing_mobile">Mobile</label>
                                                    <input type="mobile" class="form-control" id="listing_mobile"
                                                        placeholder="Enter mobile" required="" name="mobile">
                                                    <div class="invalid-feedback">Please enter an mobile address</div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="d-flex align-items-start gap-3 mt-4">
                                        <button type="submit"
                                            class="btn btn-success btn-label right ms-auto nexttabs nexttabs checkContact"
                                            data-nexttab="steparrow-description-info-tab"><i
                                                class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>Next</button>
                                    </div>
                                </form>

                            </div>
                            <!-- end tab pane -->

                            <div class="tab-pane fade" id="steparrow-description-info" role="tabpanel"
                                aria-labelledby="steparrow-description-info-tab">
                                <form id="leadListingForm" class="needs-validation" novalidate>
                                    <input type="hidden" name="owner_id" id="listing_owner_id">
                                    <input type="hidden" name="listing_lead_id" id="listing_lead_id_2">
                                    {{-- <input type="hidden" name="agent_id" id="listing_agent_id"> --}}

                                    <div>
                                        <div>

                                            <div class="row">


                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="listing_unit_no">Unit
                                                            No</label>
                                                        <input type="text" class="form-control"
                                                            id="listing_unit_no" placeholder="Enter Unit No"
                                                            required="" name="unit_no">
                                                        <div class="invalid-feedback">Please enter Unit No</div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="listing_for">Purpose</label>
                                                        <select name='for' id="listing_for"
                                                            class="form-control select2" required>
                                                            @foreach (\App\Models\Lead::FOR_SELECT ?? [] as $key => $value)
                                                                @if (
                                                                    $key == 'Unspecified' &&
                                                                        auth()->user()->hasRole('Agent'))
                                                                @else
                                                                    <option value="{{ $key }}">
                                                                        {{ $value }}
                                                                    </option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                        <div class="invalid-feedback">Please enter Purpose</div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="property_category_id">Property Type</label>
                                                        <select class="form-control select2"
                                                            id="listing_property_category_id" name="property_category_id" required>
                                                            @foreach ($categories ?? [] as $cat)
                                                                <option value="{{ $cat->id }}"> {{ $cat->title }}</option>
                                                            @endforeach
                                                        </select>
                                                        <div class="invalid-feedback">Please enter Property Type</div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="listing_for">Location</label>
                                                        <input type="hidden" name="listing_location_type"
                                                            id="listing_location_type" class="location_type">
                                                        <select class="form-control select2 location_id"
                                                            id="listing_location_id" name="listing_location_id">
                                                            <option value=""></option>
                                                        </select>
                                                        <div id="listing_location_address" class="location_address">
                                                        </div>
                                                        <div class="invalid-feedback">Please enter Location</div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="listing_bed_id">Bed</label>
                                                        <select class="form-control select2" name="bed_id"
                                                            id="listing_bed_id" required>
                                                            @foreach ($beds ?? [] as $bed)
                                                                <option value="{{ $bed->id }}">
                                                                    {{ $bed->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <div class="invalid-feedback">Please enter Bed</div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="listing_bath_id">Bath</label>
                                                        <select class="form-control select2" name="bath_id"
                                                            id="listing_bath_id" required>
                                                            @foreach ($baths ?? [] as $bath)
                                                                <option value="{{ $bath->id }}">
                                                                    {{ $bath->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <div class="invalid-feedback">Please enter Bed</div>
                                                    </div>
                                                </div>



                                                {{-- <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="listing_source_id">Source</label>
                                                    <select class="form-control select2" name="source_id"
                                                        id="listing_source_id">
                                                        @foreach ($sources ?? [] as $src)
                                                            <option value="{{ $src->id }}">
                                                                {{ $src->name }}</option>
                                                        @endforeach
                                                    </select>

                                                    <div class="invalid-feedback">Please enter Source</div>
                                                </div>
                                            </div> --}}

                                          
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-start gap-3 mt-4">
                                        <button type="button" class="btn btn-light btn-label previestab"
                                            data-previous="steparrow-gen-info-tab"><i
                                                class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i> Back
                                            to
                                            Owner Details</button>
                                        <button type="submit"
                                            class="btn btn-success btn-label right ms-auto nexttabs nexttabs"
                                            data-nexttab="pills-experience-tab"><i
                                                class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>Submit</button>
                                    </div>
                                </form>
                            </div>
                            <!-- end tab pane -->

                            <div class="tab-pane fade" id="pills-experience" role="tabpanel">
                                <div class="text-center">

                                    <div class="avatar-md mt-5 mb-4 mx-auto">
                                        <div class="avatar-title bg-light text-success display-4 rounded-circle">
                                            <i class="ri-checkbox-circle-fill"></i>
                                        </div>
                                    </div>
                                    <h5>Well Done !</h5>
                                    <p class="text-muted">Listing Successfully created</p>
                                </div>
                            </div>
                            <!-- end tab pane -->
                        </div>
                        <!-- end tab content -->

                    </div>
                    <!-- end card body -->
                </div>
            </div>

        </div>
    </div>
</div>