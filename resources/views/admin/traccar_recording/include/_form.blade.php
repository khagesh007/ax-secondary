<form class="row needs-validation" id="mainForm" novalidate>
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-4">
                        <div class="row">
                            <input type="hidden" id="id" name="id">
                            <input type="hidden" id="dailer_id" name="dailer_id"
                                value="{{ auth()->user()->id }}">
                            <h5 class="mt-2">Personal Information</h5>
                            <div class="col-4 mt-2">First Name*</div>
                            <div class="col-8 mt-2">
                                <input type="text" class="form-control inputs"
                                    name="first_name" id="first_name"
                                    placeholder="Enter first name" required>
                                <div class="invalid-feedback">
                                    Please enter first name.
                                </div>
                            </div>
                            <div class="col-4 mt-2">Last Name*</div>
                            <div class="col-8 mt-2">
                                <input type="text" class="form-control inputs"
                                    name="last_name" id="last_name"
                                    placeholder="Enter last name" required>
                                <div class="invalid-feedback">
                                    Please enter last name.
                                </div>
                            </div>
                            <div class="col-4 mt-2">Mobile*</div>
                            <div class="col-6 mt-2" id="mobileDiv1">
                                <input type="tel" class="form-control inputs checkValidity"
                                    name="mobile" id="mobile"
                                    placeholder="Enter mobile number" required>
                                <div class="text-danger mobileValidation">

                                </div>

                                <div class="invalid-feedback">
                                    Please enter a valid mobile number.
                                </div>
                            </div>
                            <div class="col-2 mt-2" id="call_now_button">


                                <a href="javascript:void(0);" title="Call Now"
                                    id="start-call-button"
                                    class="avatar-title bg-info text-white fs-15 rounded ">
                                    <i class="ri-phone-line"></i>
                                </a>
                                <a href="javascript:void(0);" title="Calling"
                                    id="calling-button"
                                    class="avatar-title bg-info text-white fs-15 rounded d-none">
                                    <span class="d-flex align-items-center">
                                        <span class="spinner-grow flex-shrink-0"
                                            role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </span>
                                    </span>
                                </a>

                            </div>


                            <div class="col-4 mt-2">Whatsapp</div>
                            <div class="col-md-6 mt-2 pr-0" id="whatsappDiv1">
                                <input type="text" class="form-control inputs"
                                    name="whatsapp" id="whatsapp"
                                    placeholder="Enter whatsapp number">
                                <div class="invalid-feedback"> Please enter a valid whatsapp
                                    number. </div>
                            </div>
                            <div class="col-md-2 mt-2" id="whatsappDiv2">
                                <a href="javascript:void(0);" title="Whatsapp Now"
                                    id="whatapp-button"
                                    class="avatar-title bg-success text-white fs-15 rounded ">
                                    <i class="ri-whatsapp-line"></i>
                                </a>
                            </div>

                            <div class="col-4 mt-2">Email</div>
                            <div class="col-8 mt-2">
                                <input type="email" class="form-control inputs  mb-1"
                                    name="email" id="email"
                                    placeholder="Enter email address">
                                <span class="text-muted"><small>Kindly be aware that your
                                        listing will require a valid email address from the
                                        owner in order to be published.</small></span>
                                <div class="invalid-feedback">
                                    Please enter a valid email address.
                                </div>
                            </div>
                            <div class="col-4 mt-2">Gender</div>
                            <div class="col-8 mt-2">
                                <select class="form-control select2 inputs" name="gender"
                                    id="gender">
                                    <option value="" selected disabled>Select gender
                                    </option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                                <div class="invalid-feedback">
                                    Please choose gender.
                                </div>
                            </div>
                            <div class="col-4 mt-2">Nationality</div>
                            <div class="col-8 mt-2">
                                <select class="form-control select2 inputs"
                                    name="nationality_id" id="nationality_id">
                                    <option value="" selected disabled>Select nationality
                                    </option>
                                    @foreach ($countries ?? [] as $country)
                                        <option value="{{ $country->id }}">
                                            {{ $country->nationality }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">
                                    Please choose nationality.
                                </div>
                            </div>


                        </div>
                    </div>

                    <div class="col-4">
                        <div class="row">
                            <h5 class="mt-2">Details</h5>

                            <div class="col-4 mt-2">Address</div>
                            <div class="col-8 mt-2">
                                <input type="text" class="form-control inputs"
                                    name="address" id="address"
                                    placeholder="Enter Address">
                                <div class="invalid-feedback">
                                    Please enter address.
                                </div>
                            </div>


                            <div class="col-4 mt-2">Date of birth</div>
                            <div class="col-8 mt-2">
                                <input type="date" class="form-control inputs"
                                    name="dob" id="dob"
                                    placeholder="Enter date of birth">
                                <div class="invalid-feedback">
                                    Please enter date of birth.
                                </div>
                            </div>
                            <div class="col-4 mt-2">Follow up date</div>
                            <div class="col-8 mt-2">
                                <input type="date" class="form-control inputs"
                                    name="follow_up_date" id="follow_up_date"
                                    placeholder="Enter follow up date">
                                <div class="invalid-feedback">
                                    Please enter follow up date.
                                </div>
                            </div>
                            <div class="col-4 mt-2">Passport</div>
                            <div class="col-8 mt-2">
                                <input type="text" class="form-control inputs"
                                    name="passport" id="passport"
                                    placeholder="Enter passport">
                                <div class="invalid-feedback">
                                    Please enter a valid passport.
                                </div>
                            </div>
                            <div class="col-4 mt-2">Passport Expiry</div>
                            <div class="col-8 mt-2">
                                <input type="date" class="form-control inputs"
                                    name="passport_expiry" id="passport_expiry"
                                    placeholder="Enter passport expiry">
                                <div class="invalid-feedback">
                                    Please enter passport expiry.
                                </div>
                            </div>
                            <div class="col-4 mt-2">Phone</div>
                            <div class="col-8 mt-2">
                                <input type="text"
                                    class="form-control inputs checkValidity" name="phone"
                                    id="phone" placeholder="Enter phone number">
                                <div class="text-danger phoneValidation">

                                </div>

                                <div class="invalid-feedback phoneValidation">
                                    Please enter a valid phone number.
                                </div>
                            </div>
                            <div class="col-4 mt-2">Company</div>
                            <div class="col-8 mt-2">
                                <input type="text" class="form-control inputs"
                                    name="company" id="company"
                                    placeholder="Enter company">
                                <div class="invalid-feedback">
                                    Please enter company.
                                </div>
                            </div>
                            <div class="col-4 mt-2">Website</div>
                            <div class="col-8 mt-2">
                                <input type="text" class="form-control inputs"
                                    name="website" id="website"
                                    placeholder="Enter website">
                                <div class="invalid-feedback">
                                    Please enter website.
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col-4">
                        <div class="row">
                            <h5 class="mt-2">Action</h5>
                            <div class="col-4 mt-2">Agent*</div>
                            <div class="col-8 mt-2">
                                <select name='agent_id' id="agent_id"
                                    class="form-control select2 inputs" required>
                                    <option selected disabled value="">Select Agent
                                    </option>
                                    @foreach ($agents ?? [] as $agent)
                                        <option value="{{ $agent->id }}">{{ $agent->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">
                                    Please choose agent.
                                </div>
                            </div>
                            <div class="col-4 mt-2">Contact Type</div>
                            <div class="col-8 mt-2">
                                <select class="form-control select2 inputs"
                                    name="contact_types[]" id="contact_types" multiple>
                                    @foreach ($types ?? [] as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">
                                    Please choose contact type.
                                </div>
                            </div>
                            <div class="col-4 mt-2">Contact Source</div>
                            <div class="col-8 mt-2">
                                <select class="form-control select2 inputs"
                                    name="contact_source_id" id="contact_source_id">
                                    <option value="" selected disabled>Select contact
                                        source</option>
                                    @foreach ($sources ?? [] as $src)
                                        <option value="{{ $src->id }}">{{ $src->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">
                                    Please choose contact source.
                                </div>
                            </div>
                            <div class="col-4 mt-2">Contact Status</div>
                            <div class="col-8 mt-2">
                                <select class="form-control select2 inputs"
                                    name="contact_status_id" id="contact_status_id">
                                    <option value="" selected disabled>Select contact
                                        status</option>
                                    @foreach ($statuses ?? [] as $status)
                                        <option value="{{ $status->id }}">
                                            {{ $status->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">
                                    Please choose contact status.
                                </div>
                            </div>
                        </div>


                        <div id="properties" class="d-none">
                            <hr>
                            <h4>Properties</h4>
                            <div class="table-responsive" style="max-height: 250px">
                                <div data-simplebar style="max-height: 160px;">
                                    <div data-simplebar style="max-height: 215px;">
                                        <ul class="list-group" id="owner_properties_div">
                                        </ul>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button type="submit" class="d-none" id="submitBtn"></button>
    </div>
</form>
