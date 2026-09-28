@extends('layouts.master-without-nav')
@section('title')
    Listing Lead
@endsection
@section('meta_description', 'Lead Generation Form')
@section('css')
    <link href="{{ URL::asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <!--datatable responsive css-->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet"
        type="text/css" />
    <link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
    <style type="text/css">
        div.dataTables_wrapper div.dataTables_filter input {
            border-color: var(--vz-input-border);
            background-color: var(--vz-input-bg);
            color: var(--vz-body-color);
            line-height: 1.5;
            padding: 0.5rem 0.9rem 0.5rem 1.025rem;
            border-radius: 0.25rem;
            font-size: .8125rem;
        }
    </style>

    {{-- <meta name="csrf-token" content="{{ csrf_token() }}"> --}}
    <link rel="stylesheet" href="{{ asset('assets/libs/intl-tel-input/css/intlTelInput.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"
        type="text/css" />
@endsection

@section('content')
    @include('admin.partials.loader')

    <div class="auth-page-wrapper pt-2">
        <!-- auth page bg -->
        <div class="auth-one-bg-position auth-one-bg" id="auth-particles">
            <div class="bg-overlay"></div>

            <div class="shape">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink"
                    viewBox="0 0 1440 120">
                    <path d="M 0,36 C 144,53.6 432,123.2 720,124 C 1008,124.8 1296,56.8 1440,40L1440 140L0 140z"></path>
                </svg>
            </div>
        </div>

        <!-- auth page content -->
        <div class="auth-page-content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center mt-2">
                            <img src="{{ asset('assets/images/logo/ax-short-logo.png') }}" alt="">
                        </div>

                    </div>
                </div>
                <!-- end row -->

                <div class="row  justify-content-center formDiv">
                    <div class="col-md-8">
                        <div class="card mt-4">

                            <div class="card-body p-4">
                                <div class="text-center mt-2">

                                </div>
                                <div class="p-2 mt-1">
                                    <form class="needs-validation" novalidate id="mainForm">

                                        <input type="hidden" name="source_id" value="{{ app()->request->input('source') }}">
                                        <input type="hidden" name="medium_id" value="{{ app()->request->input('medium') }}">
                                        <input type="hidden" name="inhouse" value="{{ $inhouse }}" >
                                        <input type="hidden" name="user_id" value="{{ app()->request->input('user') }}">

                                        <div class="row">
                                            <div class="col-md-12 text-center text-primary">
                                                <h2>Create Listing Lead</h2>
                                                @if ($inhouse == 1)
                                                    <h2>(In-House)</h1>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="row mt-5">
                                            <div class="col-12">
                                                <h5>Owner Details</h5>
                                            </div>

                                            <div class="col-md-6 mt-3">
                                                <label for="name" class="form-label">First name <span
                                                        class="required">*</span></label>
                                                <input type="text" class="form-control inputs" id="name"
                                                    name="first_name" value="" required placeholder="John">

                                            </div>
                                            <div class="col-md-6 mt-3">
                                                <label for="name" class="form-label">Last name</label>
                                                <input type="text" class="form-control inputs" id="name"
                                                    name="last_name" value="" placeholder="Doe">

                                            </div>
                                            <div class="col-md-6 mt-3">
                                                <label for="mobile" class="form-label">Mobile <span
                                                        class="required">*</span><small>example:+971585756178</small></label>
                                                <input type="tel" class="form-control inputs checkDuplicate" name="mobile"
                                                    id="mobile" required placeholder="Enter mobile" autocomplete="off"
                                                    data-intl-tel-input-id="0">

                                                <div class="text-danger mobileValidation">

                                                </div>

                                            </div>

                                            <div class="col-md-6 mt-3">
                                                <label for="email" class="form-label">Email</label>
                                                <input type="email" name="email" class="form-control inputs"
                                                    id="email" value="" placeholder="email@website.com">

                                            </div>

                                        </div>



                                        {{-- <div class="row mt-5">
                                            <div class="col-12">
                                                <h5>General</h5>
                                            </div>
                                            <div class="col-md-6 mt-3">
                                                <label for="validationCustom02" class="form-label">Source <span class="required">*</span></label>
                                                <select name="source_id" id="source_id" class="form-control inputs"
                                                    data-name="source_id" data-calc_value="0" required>
                                                    @foreach ($sources ?? [] as $source)
                                                        <option value="{{ $source->id }}">{{ $source->name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback">
                                                    Please select Source.
                                                </div>
                                            </div>





                                            <div class="col-md-6 mt-3">
                                                <label for="validationCustom02" class="form-label">Medium <span class="required">*</span></label>
                                                <select name="medium_id" id="medium_id" class="form-control inputs"
                                                    data-name="medium_id" data-calc_value="0" required>

                                                    @foreach ($mediums as $medium)
                                                        <option value="{{ $medium->id }}">{{ $medium->name }}</option>
                                                    @endforeach
                                                </select>

                                                <div class="invalid-feedback">
                                                    Please select Medium.
                                                </div>
                                            </div>


                                        </div> --}}






                                        <div class="row mt-5">
                                            <div class="col-12">
                                                <h5>Property Details</h5>
                                            </div>


                                            <div class="col-md-6 mt-3">
                                                <label for="property_category_id" class="form-label">Type<span
                                                        class="required">*</span></label>
                                                <select class="form-control select2 inputs" id="property_category_id"
                                                    name="property_category_id" required>
                                                    <option></option>

                                                    @foreach ($categories ?? [] as $cat)
                                                        <option value="{{ $cat->id }}">{{ $cat->title }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-6 mt-3">
                                                <label for="location_id" class="form-label">Location <span
                                                        class="required">*</span></label>
                                                <input type="hidden" name="location_type" id="location_type">
                                                <select class="form-control select2 inputs checkDuplicate" id="location_id"
                                                    name="location_id" required>
                                                    <option value=""></option>
                                                </select>
                                                <div id="location_address"></div>
                                            </div>

                                            {{-- <div class="col-md-6 mt-3">
                                                <label for="title" class="form-label">Title <span class="required">*</span></label>
                                                <input type="text" class="form-control inputs" id="title"
                                                    name="title" value="" required placeholder="Property Title">
                                                <div class="valid-feedback">
                                                    Looks good!
                                                </div>
                                            </div> --}}

                                            <div class="col-md-6 mt-3">
                                                <label for="validationCustom02" class="form-label">Purpose <span
                                                        class="required">*</span></label>
                                                <select name='for' id="for" class="form-control select2 inputs checkDuplicate"
                                                    required>
                                                    <option></option>

                                                    @foreach (\App\Models\Lead::FOR_SELECT ?? [] as $key => $value)
                                                        @if ($key == 'Unspecified')
                                                        @else
                                                            <option value="{{ $key }}">{{ $value }}
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                </select>

                                                <div class="invalid-feedback">
                                                    Please select Purpose.
                                                </div>
                                            </div>
                                            <div class="col-md-6 mt-3">
                                                <label for="name" class="form-label">Unit No @if ($inhouse == 0)
                                                        <span class="required">*</span>
                                                    @endif
                                                </label>
                                                <input type="text" class="form-control inputs checkDuplicate" id="unit_no"
                                                    name="unit_no" value=""
                                                    @if ($inhouse == 0) required @endif
                                                    placeholder="Unit Number">

                                            </div>


                                            <div class="col-md-6 mt-3">
                                                <label for="validationCustom02" class="form-label">Beds @if ($inhouse == 0)
                                                        <span class="required">*</span>
                                                    @endif
                                                </label>
                                                <select name="bed_id" id="bed_id" class="form-control inputs"
                                                    data-name="bed_id" data-calc_value="0"
                                                    @if ($inhouse == 0) required @endif>
                                                    <option></option>

                                                    @foreach ($beds as $bed)
                                                        <option value="{{ $bed->id }}">{{ $bed->name }}</option>
                                                    @endforeach
                                                </select>

                                                <div class="invalid-feedback">
                                                    Please select Bed. </div>
                                            </div>

                                            <div class="col-md-6 mt-3">
                                                <label for="validationCustom02" class="form-label">Baths @if ($inhouse == 0)
                                                        <span class="required">*</span>
                                                    @endif
                                                </label>
                                                <select name="bath_id" id="bath_id" class="form-control inputs"
                                                    data-name="bath_id" data-calc_value="0"
                                                    @if ($inhouse == 0) required @endif>
                                                    <option></option>
                                                    @foreach ($baths as $bath)
                                                        <option value="{{ $bath->id }}">{{ $bath->name }}</option>
                                                    @endforeach
                                                </select>

                                                <div class="invalid-feedback">
                                                    Please select Bath.
                                                </div>
                                            </div>







                                            <div class="col-md-12 mt-3">
                                                <label for="recording_file" class="form-label">Recording File <small>audio
                                                        file only</small>
                                                    @if ($inhouse == 0)
                                                        <span class="required">*</span>
                                                    @endif
                                                </label>
                                                <input type="file" class="form-control inputs" id="recording_file"
                                                    name="recording_file"  @if ($inhouse == 0)required  @endif>
                                            </div>





                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-md-12">
                                                <label for="description" class="form-label">Note <span
                                                        class="required">*</span></label>
                                                <textarea class="form-control inputs" required id="formSizeDefault" name="note"
                                                    placeholder="Note for the property" rows="4"></textarea>
                                                <div class="invalid-feedback">
                                                    Please write Note for the property.
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row text-center" id="duplicationMessage">
                                            <div class="col-md-12">
                                                <span class="text-danger"
                                                    id="showDuplicationMessage"></span>
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <button class="btn btn-success w-100" id="saveBtn" type="button">Submit
                                                Form</button>
                                            <button type="submit" class="d-none" id="submitBtn"></button>
                                        </div>
                                    </form>



                                </div>
                            </div>
                            <!-- end card body -->
                        </div>
                        <!-- end card -->



                    </div>
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </div>
        <!-- end auth page content -->

        <!-- footer -->
        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <p class="mb-0 text-muted">&copy;
                                <script>
                                    document.write(new Date().getFullYear())
                                </script> {{ getApp('name') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- end Footer -->
    </div>
@endsection
@section('script')
    <script src="{{ URL::asset('assets/libs/particles.js/particles.js.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/pages/particles.app.js') }}"></script>
    <script src="{{ asset('assets/libs/intl-tel-input/js/intlTelInput.min.js') }}"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
    <script src="{{ asset('assets/js/helpers.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $('#category').select2({
            placeholder: 'Property Type',
            allowClear: true
        });
        $('#bed_id').select2({
            placeholder: 'Select Bedrooms',
            allowClear: true
        });
        $('#bath_id').select2({
            placeholder: 'Select Bathrooms',
            allowClear: true
        });

        $('#source_id').select2({
            placeholder: 'Select Source',
            allowClear: true
        });
        $('#medium_id').select2({
            placeholder: 'Select Medium',
            allowClear: true
        });
        $('#for').select2({
            placeholder: 'Select property category',
            allowClear: true
        });
        $('#property_category_id').select2({
            placeholder: 'Select property Type',
            allowClear: true
        });



        let locations = locationSelect2("");

        $(document).on("click", "#saveBtn", function() {
            $("#submitBtn").trigger("click")
        })

        $(document).ready(function() {
            $('.checkDuplicate').change(function() {
                checkDuplicate();
            })
        });

        let mobileInput = initPhoneValidator()


        var input = document.querySelector("#mobile");
        var iti = window.intlTelInput(input);
        iti.setCountry("ae");

        input.addEventListener("countrychange", function() {
            var selectedCountryData = iti.getSelectedCountryData();
            var countryCode = selectedCountryData.dialCode;
            var countryFlag = selectedCountryData.iso2;


            if (iti.isValidNumber()) {

                $('#saveBtn').prop('disabled', false);
                $(".mobileValidation").html("");
                $("#mobile").removeClass("is-invalid");
                $('input[name="full_mobile"]').val(iti.getNumber());
            } else {

                $('#saveBtn').prop('disabled', true);
                $(".mobileValidation").html("Please enter a valid Mobile number with Country Code");
                $("#mobile").addClass("is-invalid");
            }
        });

        $('#mobile').change(function() {

            if (mobileInput.isValidNumber()) {
                $('input[name="full_mobile"]').val(iti.getNumber());
                $('#saveBtn').prop('disabled', false);
            } else {
                $('#saveBtn').prop('disabled', true);
            }
        })

        $(document).on('submit', '#mainForm', function(event) {
            // $("#saveBtn").prop('disabled', true);

            $('#screen_loader').removeClass('d-none');
            event.preventDefault();


            var formData = new FormData($("#mainForm")[0]);

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                url: "{{ route('api.thirdparty.lead.listing.store') }}",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function(result) {
                    $('#screen_loader').addClass('d-none');
                    if (result.status == '1') {

                        // $('.formDiv').addClass('d-none');
                        showToastify(result.msg, "success");

                        $("#saveBtn").prop('disabled', false); //enable submit button
                        // $(".inputs").val("").trigger("change")
                        $(".inputs").removeClass("is-invalid")
                        $('.invalid-feedback').html('')


                        $('#mainForm').trigger("reset");
                        $('#mainForm').find('.form-control').removeClass('is-invalid');
                        $('.invalid-feedback').css('display', 'none');
                        $('.inputs').removeClass('is-invalid');
                        // reset form
                    } else if (result.status == '2') {
                        console.table(result);
                        $("#saveBtn").prop('disabled', false);
                        let error_message = '';

                        $.each(result.errors, function(key, value) {
                            error_message += '' + key + ': ' + value + ' | ';
                        });
                        showToastify(error_message, "error")
                    } else {
                        $("#saveBtn").prop('disabled', false);
                        swal("Oops!", result.msg, "error");
                        showToastify(result.msg, "error")
                    }
                    $('#location_id').val(null).trigger('change');
                    $('#location_address').html('');
                    $('.inputs').val('').trigger('change');
                    $('#mainForm').trigger('reset');
                    $('.inputs').removeClass('is-invalid');
                },
                error: function(jqXhr, json, errorThrown) {
                    $("#saveBtn").prop('disabled', false);
                    let data = jqXhr.responseJSON;
                    console.table(data);
                    if (data.errors) {
                        $.each(data.errors, function(index, item) {
                            showToastify(item[0], "error")
                        })
                    }
                    if (jqXhr.status == 500 || jqXhr.status == 400) {
                        showToastify(data.message, "error")
                    }
                    if (data.message == "CSRF token mismatch.") {
                        showToastify('CSRF Token Mismatch. Please refresh the page.', 'error');
                    }
                    $('#screen_loader').addClass('d-none');

                },
            });




        });

        function clearValidationStyling() {

            $('#mainForm').classList.remove('was-validated');
        }

        $(document).on("change", "#location_id", function() {
            $("#location_address").html("")
            $("#location_type").val("")
            // let selectedData = $(this).select2('data')[0];
            let lat = $('#location_id option:selected').attr('data-lat')
            let long = $('#location_id option:selected').attr('data-long')
            let path = $('#location_id option:selected').attr('data-path')
            let type = $('#location_id option:selected').attr('data-type')
            //lat = lat == 'undefined' ? selectedData.lat : '';
            // selectedData = JSON.parse(selectedData);
            // console.log(selectedData)
            if (lat && long) {
                $("#location_address").html(
                    `<a class="view-in-map" href="javascript:void(0)" data-path="${path}" data-lat="${lat}" data-long="${long}">${path}</a>`
                );
                $("#location_type").val(type)
            }
        })
        $('#location_id').on('select2:select', function(e) {
            $("#location_address").html("")
            $("#location_type").val("")
            var data = e.params.data;
            // console.log(data.long);
            let lat = data.lat;
            let long = data.long;
            let path = data.path;
            let type = data.type;
            if (path != null) {
                $("#location_address").html(
                    `<a class="view-in-map" href="javascript:void(0)" data-path="${path}" data-lat="${lat}" data-long="${long}">${path}</a>`
                );
            }
            $("#location_type").val(type)
        });

        function locationSelect2(id) {
            $("#location_address").html("")
            $("#location_type").val("")
            $('#location_id').select2({
                placeholder: 'Tower, community, locality',
                ajax: {
                    url: '{{ route('ajax.sub.community-properties.select2') }}?id=' + id,
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
                        }
                    }
                },
                templateResult: formatResult, // Add templateResult function
                escapeMarkup: function(markup) {
                    return markup;
                },
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

        $('#location_id').on('select2:select', function(e) {
            $("#location_address").html("")
            $("#location_type").val("")
            var data = e.params.data;
            // console.log(data.long);
            let lat = data.lat;
            let long = data.long;
            let path = data.path;
            let type = data.type;
            if (path != null) {
                $("#location_address").html(
                    `<a class="view-in-map" href="javascript:void(0)" data-path="${path}" data-lat="${lat}" data-long="${long}">${path}</a>`
                );
            }
            $("#location_type").val(type)
        });


        function checkDuplicate() {
            var mobile = $('#mobile').val();
            var unit_no = $('#unit_no').val();
            var location_id = $('#location_id').val();
            var location_type = $('#location_type').val();
            var location_for = $('#for').val();
            if (unit_no && location_for && location_id && mobile) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    },
                    url: "{{ route('ajax.externalListingLeadDuplicateCheck') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        location_id: location_id,
                        location_type: location_type,
                        unit_no: unit_no,
                        for: location_for,
                        mobile: mobile
                    },
                    success: function(result) {
                        isDuplicate = true;
                        if (result.status == '1') {
                            if (result.data) {
                                $('#showDuplicationMessage').html(result.message);
                                $('#duplicationMessage').removeClass('d-none');
                                // $('#saveBtn').prop('disabled', true);
                                isDuplicate = true;
                                // alert('Making true')
                            }
                        } else {
                            $('#showDuplicationMessage').html('');
                            $('#duplicationMessage').addClass('d-none');
                            // $('#saveBtn').prop('disabled', false);
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
