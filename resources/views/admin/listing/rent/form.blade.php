<div class="collapse slideIn" id="collapseUserForm">
    <div class="row">
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body form-steps">
                    <input type="hidden" name="id" id="id">
                    <input type="hidden" name="temp_id" id="temp_id">
                    @if(auth()->user()->hasRole("Agent"))
                        <input type="hidden" name="agent_id" id="agent_id" value="{{ auth()->id() }}">
                    @else
                        <div class="row">
                            <div class="col-lg-4 col-md-4 mt-2">
                                <label for="agent_id" class="form-label"> Agent*</label>
                            </div>
                            <div class="col-lg-8 col-md-8 mt-2">
                                <select class="form-select select2 inputs" name="agent_id" id="agent_id" required>
                                    <option value=""></option>
                                    @foreach ($agents ?? [] as $agent)
                                        <option
                                            value="{{ $agent->id }}">{{ $agent->first_name }} {{ $agent->last_name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">
                                    Please choose an agent.
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-lg-4 col-md-4 mt-2">
                            <label for="owner_id" class="form-label"> Owner*</label>
                        </div>
                        <div class="col-lg-6 col-md-6 mt-2">
                            <select class="form-select select2 inputs" name="owner_id" id="owner_id" required>
                                <option value=""></option>
                                @foreach ($owners ?? [] as $owner)
                                    <option
                                        value="{{ $owner->id }}">{{ $owner->first_name }} {{ $owner->last_name }}
                                        <{{ $owner->mobile }}>
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">
                                Please choose an owner.
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2  mt-2">
                            @can("Contacts Create")
                                <button type="button" title="Add New Owner" id="addContact"
                                        class="btn btn-outline-default btn-icon waves-effect waves-light"><i
                                        class="ri-add-circle-fill"></i></button>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body form-steps">
                    <div class="row">
                        <div class="col-lg-4 col-md-4">
                            <label for="property_category_id" class="form-label"> Category*</label>
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
                                Please choose a category.
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
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body form-steps">
                    <div class="row">
                        <div class="col-lg-4 col-md-4 mt-2">
                            <label for="sub_community_id" class="form-label"> Sub Community*</label>
                        </div>
                        <div class="col-lg-8 col-md-8 mt-2">
                            <select class="form-control select2 inputs" id="sub_community_id"
                                    name="sub_community_id" required>
                                @foreach ($subCommunities ?? [] as $comm)
                                    <option value="{{ $comm->id }}">{{ $comm->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">
                                Please choose a sub community.
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 mt-2">
                            <label for="community_property_id" class="form-label"> Property</label>
                        </div>
                        <div class="col-lg-8 col-md-8 mt-2">
                            <select class="form-control select2 inputs" id="community_property_id"
                                    name="community_property_id">
                                <option value=""></option>
                            </select>
                            <div class="invalid-feedback">
                                Please choose a property.
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 mt-2">
                            <label for="unit_no" class="form-label">Unit #*</label>
                        </div>
                        <div class="col-lg-8 col-md-8 mt-2">
                            <input class="form-control inputs" id="unit_no" name="unit_no"
                                   placeholder="Unit No." required/>
                            <div class="invalid-feedback">
                                Please enter unit no.
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 mt-2">
                            <label for="built_up_area" class="form-label">Built-up area*</label>
                        </div>
                        <div class="col-lg-8 col-md-8 mt-2">
                            <input  type="number" step="0.01"  class="form-control inputs" id="built_up_area" name="built_up_area"
                                   placeholder="Built-up area" required/>
                            <div class="invalid-feedback">
                                Please enter build-up area.
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 mt-2">
                            <label for="built_up_area" class="form-label">Furnished*</label>
                        </div>
                        <div class="col-lg-8 col-md-8 mt-2">
                            <select class="form-control select2 inputs" id="furnished" name="furnished" required>
                                <option></option>
                                <option value="FURNISHED">Furnished</option>
                                <option value="SEMI_FURNISHED">Semi Furnished</option>
                                <option value="UNFURNISHED">Unfurnished</option>
                            </select>
                            <div class="invalid-feedback">
                                Please choose a furnished type.
                            </div>
                        </div>
                        @unlessrole('Agent')
                        <div class="col-lg-4 col-md-4 mt-2">
                            <label for="rera_permit_number" class="form-label">RERA Permit No</label>
                        </div>
                        <div class="col-lg-8 col-md-8 mt-2">
                            <input class="form-control inputs" id="rera_permit_number"
                                   name="rera_permit_number"
                                   placeholder="RERA Permit No"/>
                            <div class="invalid-feedback">
                                Please enter RERA permit no.
                            </div>
                        </div>
                        @endunlessrole
                        <div class="col-lg-4 col-md-4 mt-2">
                            <label for="parking" class="form-label">Parkings*</label>
                        </div>
                        <div class="col-lg-8 col-md-8 mt-2">
                            <input class="form-control inputs" id="parking" name="parking"
                                   placeholder="Number of Parking Slots" required/>
                            <div class="invalid-feedback">
                                Please enter parking.
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 mt-2">
                            <label for="view" class="form-label">View</label>
                        </div>
                        <div class="col-lg-8 col-md-8 mt-2">
                            <input class="form-control inputs" id="view" name="view"
                                   placeholder="Enter View"/>
                            <div class="invalid-feedback">
                                Please enter view.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card d-none" id="activity-log-div">
                <div class="card-body">
                    <div class="card-title">Activities</div>
                    <div class="acitivity-timeline scrollable-notes"></div>
                </div>
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
                            <input type="text" class="form-control inputs" maxlength="60" name="title"
                                   id="title"
                                   placeholder="Enter Title" required>
                            <div class="invalid-feedback">
                                Please enter title.
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2 col-md-2 mt-2">
                            <label for="community_id" class="form-label"> Price*</label>
                        </div>
                        <div class="col-lg-6  col-md-6 mt-2">
                            <input type="number" class="form-control inputs" name="price" id="price"
                                   placeholder="Enter Price"
                                   onkeyup="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                   required>
                            <div class="invalid-feedback">
                                Please enter price.
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 mt-2">
                            <select class="form-control select2 inputs" id="cheques" name="cheques">
                                <option value=""></option>
                                <option value="1 Cheque">1 Cheque</option>
                                <option value="2 Cheques">2 Cheques</option>
                                <option value="3 Cheques">3 Cheques</option>
                                <option value="4 Cheques">4 Cheques</option>
                                <option value="5 Cheques">5 Cheques</option>
                                <option value="6 Cheques">6 Cheques</option>
                                <option value="7 Cheques">7 Cheques</option>
                                <option value="8 Cheques">8 Cheques</option>
                                <option value="9 Cheques">9 Cheques</option>
                                <option value="10 Cheques">10 Cheques</option>
                                <option value="11 Cheques">11 Cheques</option>
                                <option value="12 Cheques">12 Cheques</option>
                            </select>
                            <div class="invalid-feedback">
                                Please choose a no. of cheques.
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2 col-md-2 mt-2">
                            <label for="title" class="form-label"> Detail*</label>
                        </div>
                        <div class="col-lg-10  col-md-10 mt-2">
                     <textarea class="form-control inputs summernote" id="description"
                               placeholder="Enter description" name="description"
                               rows="4"></textarea>
                            <div class="invalid-feedback">
                                Please enter description.
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-2 col-md-2 mt-2">
                            <label for="title" class="form-label"> Features</label>
                        </div>
                        <div class="col-lg-10 col-md-10 mt-2">
                            <div class="row scrollable-features">
                                @foreach($features ?? [] as $feature)
                                    <div class="col-6">
                                        <div class="form-check">
                                            <input class="form-check-input inputs" name="features[]" type="checkbox" value="{{ $feature->id }}" id="feature-{{ $feature->id }}">
                                            <label class="form-check-label2" for="feature-{{ $feature->id }}">
                                                {{ $feature->title }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body form-steps">
                    <label for="formFile" class="form-label">Media</label>
                    <ul class="nav nav-tabs mb-3" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#photos-tab" role="tab"
                               aria-selected="false">
                                Photos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#documents-tab" role="tab"
                               aria-selected="false">
                                Documents
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#floor-plan-tab" role="tab"
                               aria-selected="false">
                                Floor Plan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#other-media-tab" role="tab"
                               aria-selected="false">
                                Other Media
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content text-muted">
                        <div class="tab-pane active" id="photos-tab" role="tabpanel">
                            <button type="button" id="uploadPhotos" class="btn btn-sm btn-soft-secondary waves-effect">Upload
                                Photos
                            </button>
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
                                    <input type="file" id="title_deed" name="title_deed" class="form-control inputs">
                                    <div id="title_deed_file"></div>
                                </div>
                                <div class="col-lg-4 mt-2">
                                    <label for="owner_passport_copy" class="form-label">Owner's Passport Copy*</label>
                                </div>
                                <div class="col-lg-8 mt-2">
                                    <input type="file" id="owner_passport_copy" name="owner_passport_copy"
                                           class="form-control inputs">
                                    <div id="owner_passport_copy_file"></div>
                                </div>
                                <div class="col-lg-4 mt-2">
                                    <label for="signed_listing_form" class="form-label">Signed Listing Form*</label>
                                </div>
                                <div class="col-lg-8 mt-2">
                                    <input type="file" id="signed_listing_form" name="signed_listing_form"
                                           class="form-control inputs">
                                    <div id="signed_listing_form_file"></div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="floor-plan-tab" role="tabpanel">
                            <input type="file" name="floor_plan" id="floor_plan" class="form-control inputs">
                            <div id="floor_plan_file" class="mt-2"></div>
                        </div>
                        <div class="tab-pane" id="other-media-tab" role="tabpanel">
                            <div class="row">
                                <div class="col-lg-4 mt-2">
                                    <label for="video_link" class="form-label">YouTube video link</label>
                                </div>
                                <div class="col-lg-8 mt-2">
                                    <input type="text" id="video_link" name="video_link" class="form-control inputs"
                                           placeholder="Enter YouTube video link">
                                </div>
                                <div class="col-lg-4 mt-2">
                                    <label for="video_360_link" class="form-label">360 virtual tour link</label>
                                </div>
                                <div class="col-lg-8 mt-2">
                                    <input type="text" id="video_360_link" name="video_360_link" class="form-control inputs"
                                           placeholder="Enter 360 virtual tour link">
                                </div>
                                <div class="col-lg-4 mt-2">
                                    <label for="qr_link" class="form-label">QR code link</label>
                                </div>
                                <div class="col-lg-8 mt-2">
                                    <input type="text" id="qr_link" name="qr_link" class="form-control inputs"
                                           placeholder="Enter QR code link">
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
                <div class="card-body form-steps">
                    <div class="row">
                        <div class="col-lg-5 col-md-5 mt-2">
                            <label for="publish_status" class="form-label"> Publish Status*</label>
                        </div>
                        <div class="col-lg-7 col-lg-7 mt-2">
                            <select class="form-select select2 inputs" id="publish_status"
                                    name="publish_status" required>
                                    <option value="DRAFT" @if(!auth()->user()->can("Property Draft")) disabled @endif>Draft</option>
                                    <option value="IN_REVIEW" @if(!auth()->user()->can("Property In Review")) disabled @endif>In Review</option>
                                    <option value="PUBLISHED" @if(!auth()->user()->can("Property Publish")) disabled @endif>Published</option>
                                    <option value="UNPUBLISHED" @if(!auth()->user()->can("Property Un-publish")) disabled @endif>Unpublished</option>
                            </select>
                            <div class="invalid-feedback">
                                Please choose publish status.
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-5 col-md-5 mt-2">
                            <label for="property_status_id" class="form-label"> Property Status*</label>
                        </div>
                        <div class="col-lg-7 col-md-7 mt-2">
                            <select class="form-select select2 inputs" name="property_status_id"
                                    id="property_status_id">
                                <option value=""></option>
                                @foreach ($status as $st)
                                    <option value="{{$st->id}}">{{$st->title}}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">
                                Please choose property status.
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
                                       placeholder="Rented at">
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
                                <input type="date" class="form-control inputs" id="rented_until" name="rented_until" data-provider="flatpickr"
                                       data-date-format="Y-m-d"
                                       placeholder="Rented until">
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
                                       data-provider="flatpickr"
                                       data-date-format="Y-m-d"
                                       name="next_available" placeholder="Next available">
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
                                <select class="form-select select2 inputs" name="remind"
                                        id="remind">
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
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                            <div class="invalid-feedback">
                                Please choose exclusive or not.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body form-steps">
                    <label for="formFile" class="form-label">Portals</label>
                    @foreach ($portals as $portal)
                        <div class="form-check form-switch form-switch-success">
                            <input class="form-check-input inputs" type="checkbox" name="portals[]"
                                   value="{{ $portal->id }}" role="switch"
                                   id="portal-switch-{{$portal->id}}">
                            <label class="form-check-label2"
                                   for="portal-switch-{{$portal->id}}">
                                <img src="{{ $portal->thumbnail }}" alt="" style="height: 20px; width: 20px">
                                {{$portal->name}}
                                <span id="agent-portal-{{$portal->id}}-count" class="agent-portals-counts"></span>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="card">
                <div class="card-body form-steps">
                    <div class="row">
                        <label for="formFile" class="form-label">Notes</label>
                        <div data-simplebar class="px-3 mb-2 scrollable-notes" id="notes">
                        </div>
                        <div class="row align-items-center">
                            <div class="col-9">
                                <label class="visually-hidden"
                                       for="inlineFormInputGroupUsername">Enter</label>
                                <div class="input-group">
                           <textarea class="form-control" placeholder="Enter Notes"
                                     rows="2" id="note"></textarea>
                                </div>
                            </div>
                            <div class="col-1">
                                <button type="button" id="addNoteButton" class="btn btn-soft-secondary waves-effect">
                                    Save
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card d-none" id="view-edit-mode">
                <div class="card-body form-steps" style="font-size: 11px"></div>
            </div>
        </div>
    </div>
</div>
