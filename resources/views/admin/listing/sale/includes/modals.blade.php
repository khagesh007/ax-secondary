<div class="modal fade flip" id="uploadPhotosModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog  modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadPhotosModalLabel">Upload Photos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="close-modal"></button>
            </div>
            <div class="modal-body p-5 text-center">
                <div class="mt-4 text-center">
                    <div>
                        <div class="dropzone" id="photos-dropzone">
                            <div class="fallback">
                                <input name="file" type="file" multiple="multiple">
                            </div>
                            <div class="dz-message needsclick">
                                <div class="mb-3">
                                    <i class="display-4 text-muted ri-upload-cloud-2-fill"></i>
                                </div>
                                <h4>Drop files here or click to upload.</h4>
                            </div>
                        </div>
                        <hr>
                        <div>
                             <span class="form-text text-muted"><strong class="text-lg-end float-left">Note: </strong>
                                <ul class="text-reset notification-item d-block dropdown-item position-relative active photo-list">
                                    <li>Only JPG, JPEG &amp; PNG allowed (Min 800x600) without watermark</li>
                                    <li>Max file size: 3M</li>
                                    <li>Max 30 Photos allowed </li>
                            </ul></span>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="addContactModal" class="modal fade" tabindex="-1" aria-labelledby="addOwnerModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <form id="addContactForm" class="modal-content needs-validation" novalidate>
            <div class="modal-header">
                <h5 class="modal-title" id="addContactModalLabel">Add Contact</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-6">
                        <input type="hidden" name="contact_type_id" value="3" id="contact_type_id">
                        <div class="row">
                            @if(auth()->user()->hasRole("Agent"))
                                <input type="hidden" name="agent_id" value="{{ auth()->id() }}">
                            @else
                                <div class="col-4">Agent*</div>
                                <div class="col-8">
                                    <select name='agent_id' id="contact_agent_id" class="form-control select2 inputs" required>
                                        <option selected disabled>Select agent</option>
                                        @foreach ($agents ?? [] as $agent)
                                            <option
                                                value="{{ $agent->id }}">{{ $agent->first_name }} {{ $agent->last_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <div class="col-4 mt-2">First Name*</div>
                            <div class="col-8 mt-2">
                                <input type="text" class="form-control inputs" name="first_name"
                                       id="first_name"
                                       placeholder="First name" required>
                            </div>
                            <div class="col-4 mt-2">Last Name*</div>
                            <div class="col-8 mt-2">
                                <input type="text" class="form-control inputs" name="last_name"
                                       id="last_name"
                                       placeholder="Last name" required>
                            </div>
                            <div class="col-4 mt-2">Mobile*</div>
                            <div class="col-8 mt-2">
                                <input type="text" class="form-control inputs" name="mobile" id="mobile"
                                       placeholder="mobile" required>
                            </div>
                            <div class="col-4 mt-2">Whatsapp</div>
                            <div class="col-8 mt-2">
                                <input type="text" class="form-control inputs" name="whatsapp" id="whatsapp"
                                       placeholder="Whatsapp number">
                            </div>
                            <div class="col-4 mt-2">Address</div>
                            <div class="col-8 mt-2">
                                <input type="text" class="form-control inputs" name="address" id="address"
                                       placeholder="address">
                            </div>
                            <div class="col-4 mt-2">Email*</div>
                            <div class="col-8 mt-2">
                                <input type="email" class="form-control inputs" name="email" id="email"
                                       placeholder="Email address" required>
                            </div>
                            <div class="col-4 mt-2">Gender</div>
                            <div class="col-8 mt-2">
                                <select class="form-control select2 inputs" name="gender" id="gender">
                                    <option selected disabled>Select gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            <div class="col-4 mt-2">Date of birth</div>
                            <div class="col-8 mt-2">
                                <input type="date" class="form-control inputs" data-provider="flatpickr"
                                       data-date-format="Y-m-d" name="dob" id="dob">
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="row">
                            <div class="col-4 mt-2">Nationality</div>
                            <div class="col-8 mt-2">
                                <select class="form-control select2 inputs" name="country_id"
                                        id="country_id">
                                    <option selected disabled>Select nationality</option>
                                    @foreach ($countries ?? [] as $country)
                                        <option value="{{$country->id}}">{{$country->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-4 mt-2">Passport</div>
                            <div class="col-8 mt-2">
                                <input type="text" class="form-control inputs" name="passport" id="passport"
                                       placeholder="Passport">
                            </div>
                            <div class="col-4 mt-2">Passport Expiry</div>
                            <div class="col-8 mt-2">
                                <input type="date" class="form-control inputs" data-provider="flatpickr"
                                       data-date-format="Y-m-d" name="passport_expiry" id="passport_expiry">
                            </div>
                            <div class="col-4 mt-2">Phone</div>
                            <div class="col-8 mt-2">
                                <input type="text" class="form-control inputs" name="phone" id="phone"
                                       placeholder="Phone">
                            </div>
                            <div class="col-4 mt-2">Company</div>
                            <div class="col-8 mt-2">
                                <input type="text" class="form-control inputs" name="company" id="company"
                                       placeholder="company">
                            </div>
                            <div class="col-4 mt-2">Website</div>
                            <div class="col-8 mt-2">
                                <input type="text" class="form-control inputs" name="website" id="website"
                                       placeholder="website">
                            </div>

                            <div class="col-4 mt-2">Contact Source</div>
                            <div class="col-8 mt-2">
                                <select class="form-control select2 inputs" name="contact_source_id"
                                        id="contact_source_id">
                                    <option selected disabled>Select contact source</option>
                                    @foreach($contactSources ?? [] as $src)
                                        <option value="{{$src->id}}">{{$src->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary ">Save</button>
            </div>
        </form>
    </div>
</div>

<div id="duplicateModal" class="modal fade" tabindex="-1" aria-labelledby="duplicateModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <form id="duplicateForm" class="modal-content needs-validation" novalidate>
            <div class="modal-header">
                <h5 class="modal-title" id="duplicateModalLabel">Duplicate Listing</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-4">Copy to*</div>
                    <div class="col-8">
                        <select name='for' id="for" class="form-control select2" required>
                            @foreach (\App\Models\Property::FOR_SELECT ?? [] as $key => $value)
                                <option
                                    value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-4"></div>
                    <div class="col-8">
                        <div class="form-check form-switch form-switch-success">
                            <input class="form-check-input" type="checkbox" name="copy_images"
                                   value="on" role="switch"
                                   id="duplicate-copy-image">
                            <label class="form-check-label"
                                   for="duplicate-copy-image">
                                Copy Images
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary ">Save</button>
            </div>
        </form>
    </div>
</div>
<div id="publishRequestModal" class="modal fade" tabindex="-1" aria-labelledby="publishRequestModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <form id="publishRequestForm" class="modal-content needs-validation" novalidate>
            <input type="hidden" id="request_type" name="request_type">
            <div class="modal-header">
                <h5 class="modal-title" id="publishRequestModalLabel">Request </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body">
                <div class="row mt-4">
                    <div class="col-2">Note / Reason *</div>
                    <div class="col-10">
                        <textarea name="request_note" id="request_note" class="form-control" placeholder="Enter request note" required></textarea>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" name="request_consent" type="checkbox" id="request_consent" required>
                            <label class="form-check-label" for="request_consent" id="request_consent_txt">
                                I hereby mention that all the details filled and images, Title Deed, Owner's Passport copy and Signed Listing Form provided are 100% genuine and I hold the responsibility for the correctness of above-mentioned particulars.
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary ">Send Request</button>
            </div>
        </form>
    </div>
</div>
