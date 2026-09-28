<div class="collapse slideIn" id="collapseUserForm">
    <div class="row">
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body form-steps">
                    <input type="hidden" name="id" id="id">

                    <input type="hidden" name="temp_id" id="temp_id">
                    <input type="hidden" name="isPublishRequest" id="isPublishRequest" value="0">
                    <input type="hidden" name="isPocketRequest" id="isPocketRequest">
                    @if (auth()->user()->hasRole('Agent'))
                        <input type="hidden" name="agent_id" id="agent_id" value="{{ auth()->id() }}">
                        <div class="agent_container">

                        </div>
                    @else
                        <div class="row">
                            <div class="col-lg-4 col-md-4 mt-2">
                                <label for="agent_id" class="form-label"> Agent*</label>
                            </div>
                            <div class="col-lg-8 col-md-8 mt-2">
                                <select class="form-select select2 inputs" name="agent_id" id="agent_id" required>
                                    <option value=""></option>
                                    @foreach ($agents ?? [] as $agent)
                                        <option value="{{ $agent->id }}">{{ $agent->full_name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">
                                    Please choose an agent.
                                </div>
                                <div id="agent_container"></div>
                            </div>

                        </div>
                    @endif
                    <div class="row owner_container">
                        <div class="col-lg-4 col-md-4 mt-2">
                            <label for="owner_id" class="form-label"> Owner*</label>
                        </div>
                        <div class="col-lg-6 col-md-6 mt-2">
                            <select class="form-select select2 inputs" name="owner_id" id="owner_id" required>
                                <option value=""></option>
                                @foreach ($owners ?? [] as $owner)
                                    <option value="{{ $owner->id }}">{{ $owner->first_name }} {{ $owner->last_name }}
                                        <{{ $owner->mobile }}>
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">
                                Please choose an owner.
                            </div>
                            <div id="owners_detail_link">

                            </div>
                            @can('Contacts Create')
                                <a href="javascript:void(0)" title="Add New Owner" id="addContact"
                                   class=" waves-effect waves-light"><i class="ri-add-circle-fill"></i>Add new owner</a>
                            @endcan
                        </div>
                        <div class="col-md-2 col-sm-6 mt-2" id="call_now_button"> @canany([
                                                 'Dial my lead',
                                                 'Dial
                                                 other lead',
                                                 ])
                                <a href="javascript:void(0);" title="Call Now" id="start-call-button"
                                   class="avatar-title bg-info text-white fs-15 rounded ">
                                    <i class="ri-phone-line"></i>
                                </a>
                                <a href="javascript:void(0);" title="Calling" id="calling-button"
                                   class="avatar-title bg-soft-success text-success fs-15 rounded d-none">
                                                     <span class="d-flex align-items-center">
                                                         <span class="spinner-grow flex-shrink-0" role="status">
                                                             <span class="visually-hidden">Loading...</span>
                                                         </span>
                                                     </span>
                                </a>
                            @endcanany
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body form-steps">
                    <div class="row">
                        <div class="col-lg-4 col-md-4">
                            <label for="for" class="form-label"> Category*</label>
                        </div>
                        <div class="col-lg-8 col-md-8 mt-2">
                            <select class="form-control select2 inputs" id="for" name="for" required>
                                <option value=""></option>
                                @foreach (\App\Models\Property::FOR_SELECT ?? [] as $for)
                                    <option value="{{ $for }}">{{ $for }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">
                                Please choose a category.
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <label for="property_category_id" class="form-label"> Type*</label>
                        </div>
                        <div class="col-lg-8 col-md-8 mt-2">
                            <select class="form-control select2 inputs" id="property_category_id"
                                    name="property_category_id" required>
                                <option value=""></option>
                                @foreach ($categories ?? [] as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->title }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">
                                Please choose a type.
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
                                        @foreach (\App\Models\Property::VILLA_TYPE ?? [] as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 mt-2">
                            <label for="bed_id" class="form-label"> Beds</label>
                        </div>
                        <div class="col-lg-8 col-md-8 mt-2">
                            <select class="form-control select2 inputs" id="bed_id" name="bed_id">
                                <option value=""></option>
                                @foreach ($beds ?? [] as $bed)
                                    <option value="{{ $bed->id }}">{{ $bed->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">
                                Please choose a bed.
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 mt-2">
                            <label for="bath_id" class="form-label"> Baths</label>
                        </div>
                        <div class="col-lg-8 col-md-8 mt-2">
                            <select class="form-control select2 inputs" id="bath_id" name="bath_id">
                                <option value=""></option>
                                @foreach ($baths ?? [] as $bath)
                                    <option value="{{ $bath->id }}">{{ $bath->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">
                                Please choose a bath.
                            </div>
                        </div>


                        <div class="col-lg-4 col-md-4 mt-2">
                            <label for="location_id" class="form-label"> Location*</label>
                        </div>
                        <div class="col-lg-8 col-md-8 mt-2">
                            <input type="hidden" name="location_type" id="location_type">
                            <select class="form-control select2 inputs" id="location_id" name="location_id" required>
                                <option value=""></option>
                                {{-- @foreach ($sub_communities ?? [] as $subCommunity)
                                            <option value="{{ $subCommunity->id }}" data-type="sub_community" data-lat="{{ $subCommunity->latitude }}" data-long="{{ $subCommunity->longitude }}" data-path="{{ $subCommunity->path_name }}">{{ $subCommunity->path_name }}</option>
                                        @endforeach --}}
                            </select>
                            <div class="invalid-feedback">
                                Please choose a location.
                            </div>
                            <div id="location_address"></div>
                        </div>


                        <div class="col-lg-4 col-md-4 mt-2">
                            <label for="unit_no" class="form-label">Unit #*</label>
                        </div>
                        <div class="col-lg-8 col-md-8 mt-2">
                            <input class="form-control inputs" id="unit_no" name="unit_no"
                                   placeholder="Enter unit #" required/>
                            <div class="invalid-feedback">
                                Please enter unit no.
                            </div>
                            <div id="duplicate_listing_alert">


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
                            <label for="built_up_area" class="form-label">Size(sqft)*</label>
                        </div>
                        <div class="col-lg-4 col-md-8 mt-2">
                            <input type="number" step="0.01" class="form-control inputs" id="built_up_area"
                                   name="built_up_area" placeholder="Enter BUA"/>
                            <div class="invalid-feedback">
                                Please enter build-up area.
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-8 mt-2">
                            <input type="number" step="0.01" class="form-control inputs" id="plot_size"
                                   name="plot_size" placeholder="Enter plot size"/>
                            <div class="invalid-feedback">
                                Please enter plot size.
                            </div>
                        </div>


                        <div class="col-lg-4 col-md-4 mt-2">
                            <label for="parking" class="form-label">Parkings*</label>
                        </div>
                        <div class="col-lg-8 col-md-8 mt-2">
                            <input type="number" class="form-control inputs" id="parking" name="parking"
                                   placeholder="Enter number of parking slots"/>
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
                                @foreach (\App\Models\Property::VIEWS ?? [] as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach

                            </select>
                        </div>


                    </div>
                </div>
            </div>

            @unlessrole('Agent')
            <div class="card">
                <div class="card-header p-05rem">
                    <h6 class="card-title mb-0">RERA Validity </h6>
                </div>
                <div class="card-body form-steps">
                    <div class="row">

                        <div class="col-lg-4 col-md-4 mt-2">
                            <label for="rera_permit_number" class="form-label">RERA Permit No.(Trakheesi)</label>
                        </div>
                        <div class="col-lg-8 col-md-8 mt-2">
                            <div class="input-group">
                                <input type="text" class="form-control" id="rera_permit_number"
                                       name="rera_permit_number" placeholder="Enter RERA permit #"
                                       aria-label="Enter RERA permit #" aria-describedby="button-addon2">
                                @if (auth()->user()->can('Listing Rera Check'))
                                    <button class="btn btn-primary btn-icon waves-effect waves-light d-none btn-load"
                                            type="button" title="Validate RERA Permit Number" id="checkRera">
                                        <i class="mdi mdi-eye-arrow-right"></i>

                                    </button>
                                @endif
                            </div>
                            <div class=" fs-12 mt-1" id="reraDateRange">

                            </div>
                            <div class="invalid-feedback">
                                Please enter RERA permit no.
                            </div>
                        </div>


                    </div>
                </div>
            </div>
            @endunlessrole

            {{-- <div class="card" id="eventsLog">

                <div class="card-body mt-0 form-steps">
                    <ul class="nav nav-tabs nav-pills nav-custom nav-custom-light arrow-navtabs bg-light"
                        role="tablist">

                        <li class="nav-item active">


                            <a class="nav-link active" data-bs-toggle="tab" href="#notesTab" role="tab"
                               aria-selected="true" onclick="scrollAtBottom('notes');">
                                <i class="mdi mdi-note-text align-middle"></i>
                                Events</a>
                        </li>
                        <li class="nav-item addHide">
                            <a class="nav-link" data-bs-toggle="tab" href="#activityLogTab" role="tab"
                               aria-selected="false">
                                <i class="mdi mdi-history align-middle"></i>
                                Activity Log</a>
                        </li>
                    </ul>
                </div>


                <div class="tab-content text-muted">
                    <div class="tab-pane active" id="notesTab" role="tabpanel">

                        <div class="" id="events_card">

                            <div class="card-header p-05rem">

                                <div class="pe-2 me-n1 mb-3" data-simplebar="init" style="height: 200px">
                                    <div class="simplebar-wrapper" style="margin: 0px -8px 0px 0px;">
                                        <div class="simplebar-height-auto-observer-wrapper">
                                            <div class="simplebar-height-auto-observer"></div>
                                        </div>
                                        <div class="simplebar-mask">
                                            <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
                                                <div class="simplebar-content-wrapper" tabindex="0" role="region"
                                                     aria-label="scrollable content"
                                                     style="height: 100%; overflow: hidden scroll;">
                                                    <div class="simplebar-content" style="padding: 0px 8px 0px 0px;">
                                                        <div id="upcoming-event-list">


                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="simplebar-placeholder" style="width: auto; height: 2511px;"></div>
                                    </div>
                                    <div class="simplebar-track simplebar-horizontal" style="visibility: hidden;">
                                        <div class="simplebar-scrollbar" style="width: 0px; display: none;"></div>
                                    </div>
                                    <div class="simplebar-track simplebar-vertical" style="visibility: visible;">
                                        <div class="simplebar-scrollbar"
                                             style="height: 63px; display: block; transform: translate3d(0px, 0px, 0px);"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="activityLogTab" role="tabpanel">
                        <div class=" d-none" id="activity-log-div">

                            <div class="card-header p-05rem">
                                <h6 class="card-title mb-0">Activities </h6>
                            </div>

                            <div class="profile-timeline user-activity-timeline scrollable-notes" id="activities">

                            </div>

                            <div class="d-none p-lg-2" id="view-edit-mode">
                                <small class="mb-0 text-muted">Created: <span id="created_by_span"></span><br>Updated:
                                    <span
                                        id="updated_by_span"> </span></small>
                            </div>

                        </div>
                    </div>
                </div>

            </div> --}}
<div id="eventWidgetArea">

</div>

        </div>
        <div class="col-xl-5">
            <div class="card">
                <div class="card-body form-steps">
                    <div class="row">
                        <div class="col-lg-2 col-md-2 mt-2">
                            <label for="title" class="form-label"> Title*</label>
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
                        <div class="col-lg-6  col-md-6 mt-2">
                            <input type="text" class="form-control inputs" name="price" id="price"
                                   placeholder="Enter price" {{--                                   onkeyup="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" --}}>
                            <div class="invalid-feedback">
                                Please enter price.
                            </div>
                        </div>
                        @can('Properties Hide Price')
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
                        @endcan
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
                    <div class="row" id="descriptionFullDiv">
                        <div class="col-lg-2 col-md-2 mt-2">
                            <label for="title" class="form-label"> Description*</label>
                            <button type="button" class="btn btn-default btn-sm" id="btn_description_fullscreen"
                                    title="Full Screen" aria-label="Full Screen"><i
                                    class="note-icon-arrows-alt"></i></button>
                        </div>
                        <div class="col-lg-10  col-md-10 mt-2">
                            <textarea class="form-control inputs" id="description" placeholder="Enter description"
                                      name="description"
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
                                @foreach ($features ?? [] as $feature)
                                    <div class="single-feature">
                                        <div class="form-check amenities-form-check">
                                            <input class="btn-check form-check-input inputs" name="features[]"
                                                   type="checkbox" value="{{ $feature->id }}"
                                                   id="feature-{{ $feature->id }}" autocomplete="off">
                                            <label class="btn btn-amenities-primary btn-custom btn-primary"
                                                   for="feature-{{ $feature->id }}">
                                                {{ $feature->title }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
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

                <div class="card-body mt-0 form-steps" id="imgFullDiv">
                    <ul class="nav nav-tabs nav-pills nav-custom nav-custom-light arrow-navtabs bg-light mb-3"
                        role="tablist">
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
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#documents-tab" role="tab"
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
                                Floor Plan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#other-media-tab" role="tab"
                               aria-selected="false">
                                <i class="mdi mdi-youtube-subscription"></i>
                                Other Media
                            </a>
                        </li>

                        <li class="nav-item">
                            <button type="button" class="btn btn-default btn-sm" id="btn_pics_fullscreen"
                                    title="Full Screen" aria-label="Full Screen"><i
                                    class="note-icon-arrows-alt"></i></button>
                        </li>


                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="photos-tab" role="tabpanel">
                            @can('Add Media')
                                <button type="button" id="uploadPhotos" class="btn btn-sm btn-primary waves-effect ">
                                    <i class="ri-upload-2-fill label-icon align-middle "></i>
                                    Upload Photos
                                </button>
                            @endcan



                            @can('Download Bulk With Watermark')
                                <button type="button" id="downloadBulkWatermark"
                                        class="btn btn-sm btn-soft-secondary waves-effect action-more bulkDownload"
                                        data-ref_no="{{ request()->ref_number }}">
                                    <i class="ri-download-2-line label-icon align-middle "></i>
                                    Download All
                                </button>
                            @endcan

                            @can('Download Bulk Without Watermark')
                                <button type="button" id="downloadBulkwithoutWatermark"
                                        class="btn btn-sm btn-soft-secondary waves-effect mt-1 action-export bulkDownload"
                                        data-ref_no="{{ request()->ref_number }}">
                                    <i class="ri-download-2-line label-icon align-middle "></i>
                                    Download Originals
                                </button>
                            @endcan


                            <div class="mt-2">
                                <div class="scrollable-gallery" id="photos-gallery"></div>
                            </div>
                        </div>
                        <div class="tab-pane " id="documents-tab" role="tabpanel">
                            <div class="row">
                                <div class="col-lg-4 mt-2">
                                    <label for="title_deed" class="form-label">Title Deed*</label>
                                </div>
                                <div class="col-lg-8 mt-2">
                                    <input type="file" id="title_deed" title="Choose" name="title_deed"
                                           class="form-control inputs" accept="application/pdf,image/png, image/jpeg">
                                    <div id="title_deed_file"></div>
                                </div>
                                <div class="col-lg-4 mt-2">
                                    <label for="owner_passport_copy" class="form-label">Owner's Passport/EID*</label>
                                </div>
                                <div class="col-lg-8 mt-2">
                                    <input type="file" id="owner_passport_copy" name="owner_passport_copy"
                                           class="form-control inputs" accept="application/pdf,image/png, image/jpeg">
                                    <div id="owner_passport_copy_file"></div>
                                </div>
                                <div class="col-lg-4 mt-2">
                                    <label for="signed_listing_form" class="form-label">Contract A/Leasing
                                        Form/NOC*</label>
                                </div>
                                <div class="col-lg-8 mt-2">
                                    <input type="file" id="signed_listing_form" name="signed_listing_form"
                                           class="form-control inputs" accept="application/pdf,image/png, image/jpeg">
                                    <div id="signed_listing_form_file"></div>
                                </div>

                                <div class="col-lg-4 mt-2">
                                    <label for="contract_date" class="form-label"> Contract Duration</label>
                                </div>
                                <div class="col-lg-8 mt-2">
                                    <input type="text" class="form-control inputs" id="contract_dates"
                                           autocomplete="off" name="contract_dates" placeholder="Select date range">
                                    {{-- <span class="text-danger">Your Contract Is Expiring in <span class="no_of_days">2</span> day(s).</span> --}}
                                    <div class="ContractDaysPlacing mt-2"></div>
                                    <div class="invalid-feedback">
                                        Please select Contract date range.
                                    </div>
                                </div>


                                <div class="col-lg-12">
                                    <button type="button" class="btn btn-sm btn-success" id="add-more-documents-btn"
                                            style="display: none;">Add more
                                    </button>
                                    <div class="row" id="more-documents"></div>
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
                    </div>
                </div>
            </div>

        </div>
        <!-- end col -->
        <div class="col-xl-3">
            <div class="card">
                <div class="card-header p-05rem">
                    <h6 class="card-title mb-0">Actions </h6>
                </div>

                <div class="card-body form-steps">
                    <div class="row" id="state_div">

                        <div class="col-lg-5 col-md-5 mt-2">
                            <label for="publish_status" class="form-label"> State</label>
                        </div>
                        <div class="col-lg-4 col-lg-4 mt-2" id="formPropertyStatusText">


                        </div>
                        <div class="col-lg-3 col-lg-3 ">
                            @if (auth()->user()->can('Listing Duplicate Check'))
                                <button type="button" id="checkDuplicateProperty"
                                        class="btn btn-sm btn-soft-danger text-danger action-delete"
                                        title="Check Duplicate Listing">
                                    <i class="mdi mdi-content-duplicate"></i>
                                </button>
                            @endif
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-lg-5 col-md-5 mt-2">
                            <label for="publish_status" class="form-label"> Publish Status*</label>
                        </div>
                        <div class="col-lg-7 col-lg-7 mt-2">
                            <select class="form-select select2 inputs" id="publish_status" name="publish_status"
                                    required>
                                <option value="DRAFT" @if (!auth()->user()->can('Properties Draft')) disabled @endif>
                                    Draft
                                </option>
                                <option value="PENDING"
                                        @if (!auth()->user()->can('Properties Pending')) disabled @endif>Pending
                                </option>
                                <option value="PUBLISHED"
                                        @if (!auth()->user()->can('Properties Publish')) disabled @endif>Published
                                </option>
                                <option value="POCKET" @if (!auth()->user()->can('Properties Pocket')) disabled @endif>
                                    Pocket
                                </option>
                                <option value="UNPUBLISHED"
                                        @if (!auth()->user()->can('Properties Un-publish')) disabled @endif>
                                    Unpublished
                                </option>
                                <option value="ARCHIVED"
                                        @if (!auth()->user()->can('Properties Archive')) disabled @endif>Archived
                                </option>
                                <option value="REFUSED" @if (!auth()->user()->can('Properties Refuse')) disabled @endif>
                                    Refused
                                </option>
                            </select>
                            <div class="invalid-feedback">
                                Please choose publish status.
                            </div>
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
                                @foreach (\App\Models\Property::COMPLETION_STATUS_SELECT as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
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
                                @foreach ($developers as $dev)
                                    <option value="{{ $dev->id }}">{{ $dev->short_name }}</option>
                                @endforeach
                            </select>

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
                                @foreach ($status as $st)
                                    <option value="{{ $st->id }}">{{ $st->title }}</option>
                                @endforeach
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
                            <input type="date" class="form-control inputs" id="availability_date"
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
                                    @foreach (\App\Models\Property::REMIND_SELECT as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
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


            <div class="card">
                <div class="card-header p-05rem">
                    <h6 class="card-title mb-0">Portals </h6>
                </div>
                <div class="card-body form-steps">
                    @foreach ($portals as $portal)
                        <div class="form-check form-switch form-switch-success accordion accordion-flush">
                            <input class="form-check-input inputs portal-switchs" type="checkbox" name="portals[]"
                                   value="{{ $portal->id }}" role="switch" id="portal-switch-{{ $portal->id }}">
                            <label class="form-check-label2" for="portal-switch-{{ $portal->id }}">
                                <img src="{{ $portal->thumbnail }}" alt=""
                                     style="height: 20px; width: 20px">
                                {{ $portal->name }}
                            </label>

                            <button class="accordion-button collapsed metaDataBtn"
                                    id="metaDataBtn-{{ $portal->id }}" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#{{ $portal->slug }}-container"
                                    data-portal-name="{{ $portal->name }}" data-property-id=""
                                    data-portal-slug="{{ $portal->slug }}" aria-expanded="true"
                                    aria-controls="flush-collapseOne"
                                    style="width: 40px;float: right; padding: 2px 14px 5px 9px;"></button>

                            <div id="{{ $portal->slug }}-container"
                                 class="accordion-collapse meta-data-accordion collapse">

                            </div>

                        </div>
                    @endforeach
                </div>
            </div>
            {{-- Notes chat start from here --}}
            <div class="card" id="messageTab">
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
                                                                    <p class="text-truncate text-muted fs-14 mb-0 userStatus">
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                {{-- Chat buttons can be added here --}}
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
                                        <div class="row g-0 align-items-center">
                                            <div class="col-auto">
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
                                            <div class="col">
                                                <div class="chat-input-feedback">
                                                    Please Enter a Message
                                                </div>
                                                <textarea class="form-control chat-input" id="chat-input" rows="1"
                                                          placeholder="Enter a note"></textarea>
                                            </div>
                                            <div class="col-auto">
                                                <div class="chat-input-links ms-2">
                                                    <div class="links-list-item">
                                                        <button type="button"
                                                                class="btn btn-success chat-send waves-effect waves-light"
                                                                id="addNoteChatButton">
                                                            <i class="ri-send-plane-2-fill align-bottom"></i>
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
