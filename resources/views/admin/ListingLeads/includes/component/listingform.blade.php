<form action="#" class="form-steps" autocomplete="off">
    <div class="text-center pt-3 pb-4 mb-1">
        <h5>Create Draft Listing</h5>
    </div>
    <div id="custom-progress-bar" class="progress-nav mb-4">
        <div class="progress" style="height: 1px;">
            <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0"
                aria-valuemax="100"></div>
        </div>

        <ul class="nav nav-pills progress-bar-tab custom-nav" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill active" data-progressbar="custom-progress-bar"
                    id="pills-gen-info-tab" data-bs-toggle="pill" data-bs-target="#pills-gen-info" type="button"
                    role="tab" aria-controls="pills-gen-info" aria-selected="true" data-position="0">1</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill" data-progressbar="custom-progress-bar" id="pills-info-desc-tab"
                    data-bs-toggle="pill" data-bs-target="#pills-info-desc" type="button" role="tab"
                    aria-controls="pills-info-desc" aria-selected="false" data-position="1" tabindex="-1">2</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill" data-progressbar="custom-progress-bar" id="pills-success-tab"
                    data-bs-toggle="pill" data-bs-target="#pills-success" type="button" role="tab"
                    aria-controls="pills-success" aria-selected="false" data-position="2" tabindex="-1">3</button>
            </li>
        </ul>
    </div>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="pills-gen-info" role="tabpanel" aria-labelledby="pills-gen-info-tab">
            <div>
                <div class="mb-4">
                    <div>
                        <h5 class="mb-1">Contact Information</h5>
                        <p class="text-muted">Fill all Information as below</p>
                    </div>
                </div>
                <div class="row">

                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label" for="listing_first_name">First Name</label>
                            <input type="text" class="form-control" id="listing_first_name"
                                placeholder="Enter First name" required="">
                            <div class="invalid-feedback">Please enter First name</div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label" for="listing_last_name">Last Name</label>
                            <input type="text" class="form-control" id="listing_last_name"
                                placeholder="Enter Last name" required="">
                            <div class="invalid-feedback">Please enter Last name</div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label" for="listing_email">Email</label>
                            <input type="email" class="form-control" id="listing_email" placeholder="Enter email"
                                required="">
                            <div class="invalid-feedback">Please enter an email address</div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label" for="listing_mobile">Mobile</label>
                            <input type="mobile" class="form-control" id="listing_mobile" placeholder="Enter mobile"
                                required="">
                            <div class="invalid-feedback">Please enter an mobile address</div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="d-flex align-items-start gap-3 mt-4">
                <button type="button" class="btn btn-success btn-label right ms-auto nexttab nexttab"
                    data-nexttab="pills-info-desc-tab"><i
                        class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>Go to more info</button>
            </div>
        </div>
        <!-- end tab pane -->

        <div class="tab-pane fade" id="pills-info-desc" role="tabpanel" aria-labelledby="pills-info-desc-tab">
            <div>
                <div>
                    <div class="mb-4">
                        <div>
                            <h5 class="mb-1">Listing Information</h5>
                            <p class="text-muted">Fill all Information as below</p>
                        </div>
                    </div>
                    <div class="row">

                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label" for="listing_title">Title</label>
                                <input type="text" class="form-control" id="listing_title"
                                    placeholder="Enter Title" required="">
                                <div class="invalid-feedback">Please enter Title</div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label" for="listing_unit_no">Unit No</label>
                                <input type="text" class="form-control" id="listing_unit_no"
                                    placeholder="Enter Unit No" required="">
                                <div class="invalid-feedback">Please enter Unit No</div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label" for="listing_for">Purpose</label>
                                <select name='for[]' id="listing_for" class="form-control select2 inputs" multiple
                                    required>
                                    @foreach (\App\Models\Lead::FOR_SELECT ?? [] as $key => $value)
                                        @if (
                                            $key == 'Unspecified' &&
                                                auth()->user()->hasRole('Agent'))
                                        @else
                                            <option value="{{ $key }}">{{ $value }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">Please enter Purpose</div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label" for="listing_for">Location</label>
                                <input type="text" class="form-control" id="listing_for"
                                    placeholder="Enter Purpose" required="">
                                <div class="invalid-feedback">Please enter Location</div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label" for="listing_bed_id">Bed</label>
                                <select class="form-control select2 inputs" name="bed_id" id="listing_bed_id"
                                    required>
                                    @foreach ($beds ?? [] as $bed)
                                        <option value="{{ $bed->id }}">{{ $bed->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">Please enter Bed</div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label" for="listing_bath_id">Bath</label>
                                <select class="form-control select2 inputs" name="bath_id" id="listing_bath_id"
                                    required>
                                    @foreach ($baths ?? [] as $bath)
                                        <option value="{{ $bath->id }}">{{ $bath->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">Please enter Bed</div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label" for="listing_agent_id">Agent</label>
                                <select class="form-control select2 inputs" name="agent_id" id="listing_agent_id">
                                    @foreach ($agents ?? [] as $src)
                                        <option value="{{ $src->id }}">{{ $src->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">Please enter Agent</div>
                            </div>
                        </div>


                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label" for="listing_source_id">Source</label>
                                <select class="form-control select2 inputs" name="agent_id" id="listing_source_id">
                                    @foreach ($sources ?? [] as $src)
                                        <option value="{{ $src->id }}">{{ $src->name }}</option>
                                    @endforeach
                                </select>

                                <div class="invalid-feedback">Please enter Source</div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label" for="listing_note">Note</label>
                                <input type="text" class="form-control" id="listing_note"
                                    placeholder="Enter Note">
                                <div class="invalid-feedback">Please enter Note</div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
            <div class="d-flex align-items-start gap-3 mt-4">
                <button type="button" class="btn btn-link text-decoration-none btn-label previestab"
                    data-previous="pills-gen-info-tab"><i
                        class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i> Back to General</button>
                <button type="button" class="btn btn-success btn-label right ms-auto nexttab nexttab"
                    data-nexttab="pills-success-tab"><i
                        class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>Submit</button>
            </div>
        </div>
        <!-- end tab pane -->

        <div class="tab-pane fade" id="pills-success" role="tabpanel" aria-labelledby="pills-success-tab">
            <div>
                <div class="text-center">

                    <div class="mb-4">
                        <lord-icon src="https://cdn.lordicon.com/lupuorrc.json" trigger="loop"
                            colors="primary:#0ab39c,secondary:#405189" style="width:120px;height:120px"></lord-icon>
                    </div>
                    <h5>Well Done !</h5>
                    <p class="text-muted">You have Successfully Signed Up</p>
                </div>
            </div>
        </div>
        <!-- end tab pane -->
    </div>
    <!-- end tab content -->
</form>
