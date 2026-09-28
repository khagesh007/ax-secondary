<div id="addListingModal" class="modal zoomIn" tabindex="-1" aria-labelledby="addOwnerModalLabel" aria-hidden="true"
    style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-soft-danger">
                <h5 class="modal-title" id="addListingModalLabel">Convert to Listing Lead</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body ">
                <div class="row">


                    <div class="col-xl-12">
                        <div class="">

                            <div class="card-body">



                                <div class="step-arrow-nav mb-4 non-clickable">

                                    <ul class="nav nav-pills custom-nav nav-justified" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="steparrow-gen-info-tab"
                                                data-bs-toggle="pill" data-bs-target="#steparrow-gen-info"
                                                type="button" role="tab" aria-controls="steparrow-gen-info"
                                                aria-selected="true">Owner Details</button>
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
                                <form class=" form-steps needs-validation" novalidate autocomplete="off"
                                    id="leadToListingForm">
                                    @csrf
                                    <div class="tab-content">
                                        <div class="tab-pane fade  show active" id="steparrow-gen-info" role="tabpanel"
                                            aria-labelledby="steparrow-gen-info-tab">

                                            <div>
                                                <div class="row">

                                                    <input type="hidden" name="lead_ref" id="lead_ref" class="">
                                                    <input type="hidden" name="listing_telesales_id" class=""
                                                        id="listing_telesales_id">
                                                    <input type="hidden" name="listing_source_id" class=""
                                                        id="listing_source_id">
                                                    <input type="hidden" name="listing_medium_id" class=""
                                                        id="listing_medium_id">
                                                    <input type="hidden" name="listing_type_id" id="listing_type_id" class="">


                                                    <div class="col-lg-6 d-none">
                                                        <div class="mb-3">
                                                            <label class="form-label" for="listing_agent_id">Agent</label>
                                                            @if(auth()->user()->hasRole(['Agent']))
                                                            <input type="hidden" name="agent_id" id="listing_agent_id" value="{{auth()->user()->id}}">
                                                            @else
                                                            <select class="form-control select2 resetFields" name="agent_id" id="listing_agent_id">
                                                                <option value=""></option>
                                                                @foreach ($agents ?? [] as $src)
                                                                    <option value="{{ $src->id }}">{{ $src->first_name }} {{ $src->last_name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @endif
                                                            <div class="invalid-feedback">Please enter Agent</div>
                                                        </div>
                                                    </div>



                                                    <div class="col-lg-6">
                                                        <div class="mb-3">
                                                            <label class="form-label" for="listing_first_name">First
                                                                Name*</label>
                                                            <input type="text" class="form-control contact_req resetFields"
                                                                id="listing_first_name" placeholder="Enter First name"
                                                                required name="first_name">
                                                            <div class="invalid-feedback">Please enter First name</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="mb-3">
                                                            <label class="form-label" for="listing_last_name">Last
                                                                Name</label>
                                                            <input type="text" class="form-control resetFields"
                                                                id="listing_last_name" placeholder="Enter Last name"
                                                                name="last_name">
                                                            <div class="invalid-feedback">Please enter Last name</div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6">
                                                        <div class="mb-3">
                                                            <label class="form-label"
                                                                for="listing_mobile">Mobile*</label>
                                                            <input type="mobile" class="form-control resetFields contact_req"
                                                                id="listing_mobile" placeholder="Enter mobile"
                                                                required name="mobile">
                                                            <div class="invalid-feedback">Please enter an mobile
                                                                address</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="mb-3">
                                                            <label class="form-label"
                                                                for="listing_email">Email</label>
                                                            <input type="email" class="form-control resetFields"
                                                                id="listing_email" placeholder="Enter email"
                                                                name="email">
                                                            <div class="invalid-feedback">Please enter an email address
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="d-flex align-items-start gap-3 mt-4">
                                                <button type="button"
                                                    class="btn btn-success btn-label right ms-auto ownerInfo checkDuplicate action-save"
                                                    data-nexttab="steparrow-description-info-tab"><i
                                                        class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>Next</button>
                                            </div>
                                        </div>
                                        <!-- end tab pane -->

                                        <div class="tab-pane fade" id="steparrow-description-info" role="tabpanel"
                                            aria-labelledby="steparrow-description-info-tab">
                                            {{-- <input type="hidden" name="owner_id" id="listing_owner_id">
                                            <input type="hidden" name="listing_lead_id" id="listing_lead_id_2"> --}}
                                            {{-- <input type="hidden" name="agent_id" id="listing_agent_id"> --}}

                                            <div>
                                                <div>

                                                    <div class="row">

                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label class="form-label"
                                                                    for="property_category_id">Property Type*</label>
                                                                <select class="form-control resetFields select2 property_req checkDuplicate"
                                                                    id="listing_property_category_id"
                                                                    name="property_category_id">
                                                                    <option value=""></option>

                                                                    @foreach ($categories ?? [] as $cat)
                                                                        <option value="{{ $cat->id }}">
                                                                            {{ $cat->title }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <div class="invalid-feedback">Please enter Property
                                                                    Type</div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label class="form-label"
                                                                    for="listing_for">Purpose*</label>
                                                                <select name='for' id="listing_for"
                                                                    class="form-control property_req resetFields select2 checkDuplicate">
                                                                    <option></option>
                                                                    @foreach (\App\Models\Lead::FOR_SELECT ?? [] as $key => $value)
                                                                        @if ($key == 'Unspecified')
                                                                        @else
                                                                            <option value="{{ $key }}"
                                                                                {{ $value == 'Residential for sale' ? 'selected' : null }}>
                                                                                {{ $value }}
                                                                            </option>
                                                                        @endif
                                                                    @endforeach
                                                                </select>
                                                                <div class="invalid-feedback">Please enter Purpose
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label class="form-label"
                                                                    for="listing_location_type">Location*</label>
                                                                <input type="hidden" name="listing_location_type"
                                                                    id="listing_location_type"
                                                                    class="location_type checkDuplicate">
                                                                <select
                                                                    class="form-control resetFields property_req select2 location_id location_id2 checkDuplicate"
                                                                    id="listing_location_id"
                                                                    name="listing_location_id">
                                                                    <option value=""></option>
                                                                </select>
                                                                <div id="listing_location_address"
                                                                    class="location_address">
                                                                </div>
                                                                <div class="invalid-feedback">Please enter Location
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label class="form-label" for="listing_unit_no">Unit
                                                                    No*</label>
                                                                <input type="text"
                                                                    class="form-control resetFields checkDuplicate property_req"
                                                                    id="listing_unit_no" placeholder="Enter Unit No"
                                                                    name="unit_no">
                                                                <div class="invalid-feedback">Please enter Unit No
                                                                </div>
                                                            </div>
                                                        </div>






                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label class="form-label"
                                                                    for="listing_bed_id">Bed</label>
                                                                <select class="form-control resetFields select2 checkDuplicate"
                                                                    name="bed_id" id="listing_bed_id">
                                                                    <option value=""></option>

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
                                                                <label class="form-label"
                                                                    for="listing_bath_id">Bath</label>
                                                                <select class="form-control resetFields select2 checkDuplicate"
                                                                    name="bath_id" id="listing_bath_id">
                                                                    <option value=""></option>

                                                                    @foreach ($baths ?? [] as $bath)
                                                                        <option value="{{ $bath->id }}">
                                                                            {{ $bath->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <div class="invalid-feedback">Please enter Bed</div>
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-12">
                                                            <div class="mb-3">
                                                                <label class="form-label"
                                                                    for="listing_bath_id">Note</label>
                                                                <textarea class="form-control resetFields" id="listing_note" placeholder="Enter Note" name="note"></textarea>

                                                                <div class="invalid-feedback">Please enter Note</div>
                                                            </div>
                                                        </div>


                                                        <div class="row text-end" id="duplicationMessage">
                                                            <div class="col-md-12">
                                                                <span class="text-danger"
                                                                    id="showDuplicationMessage"></span>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-start gap-3 mt-4">
                                                <button type="button" class="btn btn-light btn-label previestab"
                                                    data-previous="steparrow-gen-info-tab"><i
                                                        class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i>
                                                    Previous</button>

                                                <button type="submit" id="saveListingForm"
                                                    class="btn btn-success btn-label right ms-auto propertyInfo saveListing action-save"
                                                    data-nexttab="pills-experience-tab"><i
                                                        class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>Submit</button>
                                            </div>
                                        </div>
                                        <!-- end tab pane -->

                                        <div class="tab-pane fade" id="pills-experience" role="tabpanel">
                                            <div class="text-center">

                                                <div class="avatar-md mt-5 mb-4 mx-auto">
                                                    <div
                                                        class="avatar-title bg-light text-success display-4 rounded-circle">
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
                            <!-- end card body -->
                        </div>
                        <!-- end card -->
                    </div>
                    <!-- end col -->
                </div>
            </div>

        </div>
    </div>
</div>

@section('page_scripts')
    <script>
        $('#listing_agent_id').select2({
            placeholder: 'Select Agent',
            allowClear: true,
            dropdownParent: $('#addListingModal'),
        });

        $('#listing_location_id').select2({
            placeholder: 'Select Location',
            allowClear: true,
            dropdownParent: $('#addListingModal'),
        });

        $('#listing_property_category_id').select2({
            placeholder: 'Select Property Type',
            allowClear: true,
            dropdownParent: $('#addListingModal'),
        });

        $('#listing_for').select2({
            placeholder: 'Select Property For',
            allowClear: true,
            dropdownParent: $('#addListingModal'),
        });
        $('#listing_bath_id').select2({
            placeholder: 'Select Baths',
            allowClear: true,
            dropdownParent: $('#addListingModal'),
        });

        $('#listing_property_category_id').select2({
            placeholder: 'Select Property Type',
            allowClear: true,
            dropdownParent: $('#addListingModal'),
        });


        $('#listing_bed_id').select2({
            placeholder: 'Select Beds',
            allowClear: true,
            dropdownParent: $('#addListingModal'),
        });
        $('#listing_for').select2({
            placeholder: 'Select Purpose',
            allowClear: true,
            dropdownParent: $('#addListingModal'),
        });
        $('.checkDuplicate').change(function() {
            checkDuplicate();

        })

         let listing_mobile_Input = initPhoneValidator("listing_mobile", "full_listing_mobile")

        //


        $('.location_id').on('select2:select', function(e) {
            $(".location_address").html("")
            $(".location_type").val("")
            var data = e.params.data;
            // console.log(data.long);
            let lat = data.lat;
            let long = data.long;
            let path = data.path;
            let alternate_path_name = data.alternate_path_name;
            let type = data.type;
            if (path != null) {
                $(".location_address").html(
                    `<a class="view-in-map" href="javascript:void(0)" data-path="${path}" data-lat="${lat}" data-long="${long}">${path} </a>  ${alternate_path_name != null ? '<i class="ri  ri-information-line" data-bs-toggle="tooltip" data-bs-placement="right" title="'+alternate_path_name+'"></i>': ""}`
                );
            }
            $(".location_type").val(type)
        });

        function locationSelect2(id) {
            $(".location_address").html("");
            $(".location_type").val("");

            $('.location_id').select2({
                placeholder: 'Tower, community, locality',
                ajax: {
                    url: '{{ route('ajax.sub.community-listing-leads.select2') }}?id=' + id,
                    dataType: 'json',
                    type: 'GET',
                    processResults({
                        data
                    }) {
                        let options = '';
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    text: `${item.text}`,
                                    description: `${item.path}`,
                                    id: item.id,
                                    'lat': item.lat,
                                    'long': item.long,
                                    'path': item.path,
                                    'alternate_path_name': item.alternate_path_name,
                                    'type': item.type
                                }
                            })
                        };
                    }
                },
                templateResult: formatResult, // Add templateResult function
                escapeMarkup: function(markup) {
                    return markup;
                }
            });
        }


        function formatResult(result) {
            if (!result.id) {
                return result.text;
            }

            let $result = $('<div>');
            $result.text(result.text);
            $result.append('<div class="description">' + result.description +
                '</div>'); // Use 'description' as HTML content
            return $result;
        }


        $('#addListingModal .location_id').select2({
            placeholder: 'Tower, community, locality',
            dropdownParent: $("#addListingModal"),
            ajax: {
                url: '{{ route('ajax.sub.community-listing-leads.select2') }}?id=' + id,
                dataType: 'json',
                type: 'GET',
                processResults({
                    data
                }) {
                    let options = '';
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: `${item.text}`,
                                description: `${item.path}`,
                                id: item.id,
                                'lat': item.lat,
                                'long': item.long,
                                'path': item.path,
                                'alternate_path_name': item.alternate_path_name,
                                'type': item.type
                            }
                        })
                    };
                }
            },
            templateResult: formatResult, // Add templateResult function
            escapeMarkup: function(markup) {
                return markup;
            }
        });

        $('.ownerInfo').click(function() {
            allValid = true;
            $('.contact_req').each(function() {
                var input = $(this);
                var value = input.val();

                if (value === null || value === '') {
                    input.addClass('is-invalid');
                    // Show the error message
                    input.siblings('.invalid-feedback').text('This field is required.');
                    allValid = false;
                } else {
                    input.removeClass('is-invalid');
                    input.siblings('.invalid-feedback').text('');
                }
            });

            if (allValid) {
                $('#steparrow-gen-info').removeClass('show active');
                $('#steparrow-description-info').addClass('show active');
                $('#steparrow-gen-info-tab').removeClass('active');
                $('#steparrow-gen-info-tab').addClass('done');
                $('#steparrow-description-info-tab').addClass('active');
                checkDuplicate();
            }

        });

        $('.propertyInfo').click(function() {
            allValid2 = true;
            $('#leadToListingForm .property_req').each(function() {
                var input = $(this);
                var value = input.val();
                if (value === null || value === '') {
                    input.addClass('is-invalid');
                    // Show the error message
                    input.siblings('.invalid-feedback').text('This field is required.');
                    allValid2 = false;
                } else {
                    input.removeClass('is-invalid');
                    input.siblings('.invalid-feedback').text('');
                }
            });
        });

        function resetModal() {
            $('#listing_owner_id').val('');
            $('#steparrow-gen-info').addClass('show active');
            $('#steparrow-description-info').removeClass('show active');
            $('#pills-experience-tab').removeClass('active');

            $('#steparrow-gen-info-tab').addClass('active');
            $('#steparrow-gen-info-tab').removeClass('done');
            $('#pills-experience').removeClass('show active');

            $('#steparrow-description-info-tab').removeClass('active done');
        }


        function checkDuplicate() {
            var unit_no = $('#listing_unit_no').val();
            var listing_property_category_id = $('#listing_property_category_id').val();
            var listing_bed_id = $('#listing_bed_id').val();
            var listing_bath_id = $('#listing_bath_id').val();
            var location_id = $('#listing_location_id').val();
            var location_type = $('#listing_location_type').val();
            var location_for = $('#listing_for').val();

            if (unit_no && listing_property_category_id && listing_bed_id && listing_bath_id && location_for && location_id && location_type) {
                $.ajax({
                    url: "{{ route('listings.leads.leadToListingCheckDuplicate') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        location_id: location_id,
                        location_type: location_type,
                        unit_no: unit_no,
                        bath_id: listing_bath_id,
                        bed_id: listing_bed_id,
                        property_category_id: listing_property_category_id,
                        for: location_for,
                    },
                    success: function(result) {
                        isDuplicate = true;
                        if (result.status == '1') {
                            if (result.data) {
                                $('#showDuplicationMessage').html(result.message);
                                $('#duplicationMessage').removeClass('d-none');
                                $('.saveListing').prop('disabled', true);
                                isDuplicate = true;
                                // alert('Making true')
                            }
                        } else {
                            $('#showDuplicationMessage').html('');
                            $('#duplicationMessage').addClass('d-none');
                            $('.saveListing').prop('disabled', false);
                            isDuplicate = false;
                            // alert('Making false')

                        }
                    },
                    error: function(result) {
                        console.log(result);
                    }
                });
            }
        }
    </script>
@endsection
