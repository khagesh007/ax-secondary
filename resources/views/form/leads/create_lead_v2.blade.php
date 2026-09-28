@extends('layouts.master-without-nav')
@section('title')
    Lead Form
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
                                <div class="p-2 mt-4 ">
                                    <form class="needs-validation" novalidate id="mainForm">

                                        <input type="hidden" name="source" value="{{ app()->request->input('source') }}">
                                        <div class="row">
                                            <div class="col-md-12 text-center text-primary">
                                                <h2>Lead Form</h2>
                                            </div>
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <label for="first_name" class="form-label required">First name
                                                    <span>*</span></label>
                                                <input type="text" class="form-control inputs" id="first_name"
                                                    name="first_name" value="" required placeholder="John">

                                            </div>
                                            <div class="col-md-6">
                                                <label for="last_name" class="form-label required">Last name
                                                    </label>
                                                <input type="text" class="form-control inputs" id="last_name"
                                                    name="last_name" value="" placeholder=" Doe">
                                            </div>

                                            <div class="col-md-6 mt-4">
                                                <label for="phone" class="form-label required">Mobile
                                                    <span>*</span><small>example:+971585756178</small></label>
                                                <input type="tel" class="form-control inputs" name="mobile"
                                                    id="mobile" required placeholder="Enter mobile" autocomplete="off"
                                                    data-intl-tel-input-id="0">

                                                <div class="text-danger mobileValidation">

                                                </div>

                                                <div class="valid-feedback">
                                                    Please enter a valid phone number.
                                                </div>
                                            </div>

                                            <div class="col-md-6 mt-4">
                                                <label for="email" class="form-label required">Email</label>
                                                <input type="email" name="email" class="form-control inputs"
                                                    id="email" value=""  placeholder="email@website.com">
                                                <div class="valid-feedback">

                                                </div>
                                            </div>
                                            <div class="col-md-6 mt-4">
                                                <label for="ref_no" class="form-label required">Ref No</label>
                                                <input type="text" name="ref_no" class="form-control inputs"
                                                    id="ref_no" value=""  placeholder="axc-123123">
                                                <div class="valid-feedback">

                                                </div>
                                            </div>
                                            <div class="col-md-6 mt-4">
                                                <label for="email" class="form-label required">Purpose
                                                    <span>*</span></label>
                                                <select name='for' id="for" class="form-control select2 inputs"
                                                    required>
                                                    @foreach (\App\Models\Lead::FOR_SELECT ?? [] as $key => $value)
                                                        @if (
                                                            $key == 'Unspecified')
                                                        @else
                                                            <option value="{{ $key }}">{{ $value }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                                <div class="valid-feedback">
                                                    Please enter a purpose.
                                                </div>
                                            </div>


                                        </div>

                                        <div class="row  mt-2 align-items-center">

                                            <div class="col-md-6 col-sm-12 mt-2">
                                                <label for="calls" class="form-label">Community</label>

                                                <select class="select2 inputsCriteria inputs" multiple id="communities"
                                                    name="communities[]"></select>
                                            </div>

                                            <div class="col-md-6 col-sm-12 mt-2">
                                                <label for="calls" class="form-label">Location</label>

                                                <input type="hidden" name="locations" id="locations">
                                                <select class="select2 inputsCriteria inputs" multiple
                                                    id="sub_communities" name="sub_communities[]"></select>
                                            </div>

                                        </div>

                                        <div class="row  mt-2 align-items-center">
                                            <div class="col-md-6">
                                                <label for="bed" class="form-label">Beds</label>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <select class="form-control inputsCriteria select2 inputs"
                                                            name="min_bed_id" id="min_bed_id">
                                                            <option value="" selected disabled>Select</option>
                                                            @foreach ($beds as $bed)
                                                                <option value="{{ $bed->id }}">
                                                                    {{ $bed->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <select class="form-control inputsCriteria select2 inputs"
                                                            name="max_bed_id" id="max_bed_id">
                                                            <option value="" selected disabled>Select</option>
                                                            @foreach ($beds as $bed)
                                                                <option value="{{ $bed->id }}">
                                                                    {{ $bed->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="bath" class="form-label">Baths</label>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <select class="form-control inputsCriteria select2 inputs"
                                                            name="min_bath_id" id="min_bath_id">
                                                            <option value="" selected disabled>Select</option>
                                                            @foreach ($baths as $bath)
                                                                <option value="{{ $bath->id }}">
                                                                    {{ $bath->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <select class="form-control inputsCriteria select2 inputs"
                                                            name="max_bath_id" id="max_bath_id">
                                                            <option value="" selected disabled>Select</option>
                                                            @foreach ($baths as $bath)
                                                                <option value="{{ $bath->id }}">
                                                                    {{ $bath->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>



                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <label for="bath" class="form-label">Budget</label>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <select class="form-control inputsCriteria select2 inputs"
                                                            name="min_price" id="min_price">
                                                            <option value="" selected disabled>Select</option>

                                                         <option value="10000">10,000</option>
                                                         <option value="20000">20,000</option>
                                                         <option value="30000">30,000</option>
                                                         <option value="40000">40,000</option>
                                                         <option value="50000">50,000</option>
                                                         <option value="60000">60,000</option>
                                                         <option value="850000">85,000</option>
                                                         <option value="110000">110,000</option>
                                                         <option value="135000">135,000</option>
                                                         <option value="160000">160,000</option>
                                                         <option value="185000">185,000</option>
                                                         <option value="210000">210,000</option>
                                                         <option value="235000">235,000</option>
                                                         <option value="260000">260,000</option>
                                                         <option value="285000">285,000</option>
                                                         <option value="310000">310,000</option>
                                                         <option value="335000">335,000</option>
                                                         <option value="360000">360,000</option>
                                                         <option value="410000">410,000</option>
                                                         <option value="460000">460,000</option>
                                                         <option value="510000">510,000</option>
                                                         <option value="560000">560,000</option>
                                                         <option value="610000">610,000</option>
                                                         <option value="660000">660,000</option>
                                                         <option value="710000">710,000</option>
                                                         <option value="760000">760,000</option>
                                                         <option value="810000">810,000</option>
                                                         <option value="860000">860,000</option>
                                                         <option value="910000">910,000</option>
                                                     </select>

                                                    </div>
                                                    <div class="col-md-6">
                                                        <select class="form-control inputsCriteria select2 inputs"
                                                            name="max_price" id="max_price">
                                                            <option value="" selected disabled>Select</option>
                                                            <option value="10000">10,000</option>
                                                            <option value="20000">20,000</option>
                                                            <option value="30000">30,000</option>
                                                            <option value="40000">40,000</option>
                                                            <option value="50000">50,000</option>
                                                            <option value="60000">60,000</option>
                                                            <option value="850000">85,000</option>
                                                            <option value="110000">110,000</option>
                                                            <option value="135000">135,000</option>
                                                            <option value="160000">160,000</option>
                                                            <option value="185000">185,000</option>
                                                            <option value="210000">210,000</option>
                                                            <option value="235000">235,000</option>
                                                            <option value="260000">260,000</option>
                                                            <option value="285000">285,000</option>
                                                            <option value="310000">310,000</option>
                                                            <option value="335000">335,000</option>
                                                            <option value="360000">360,000</option>
                                                            <option value="410000">410,000</option>
                                                            <option value="460000">460,000</option>
                                                            <option value="510000">510,000</option>
                                                            <option value="560000">560,000</option>
                                                            <option value="610000">610,000</option>
                                                            <option value="660000">660,000</option>
                                                            <option value="710000">710,000</option>
                                                            <option value="760000">760,000</option>
                                                            <option value="810000">810,000</option>
                                                            <option value="860000">860,000</option>
                                                            <option value="910000">910,000</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="description" class="form-label">Description</label>
                                                <textarea class="form-control inputs" id="formSizeDefault" name="description"
                                                    placeholder="Description for the property" rows="3"></textarea>
                                                <div class="invalid-feedback">
                                                    Please enter a valid phone number.
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-2">
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
        $('#min_price').select2({
            placeholder: 'Min Price',
            allowClear: true,
            closeOnSelect: true,
        });
        $('#max_price').select2({
            placeholder: 'Max Price',
            allowClear: true

        });

        $('#min_bed_id').select2({
            placeholder: 'Select Bedrooms',
            allowClear: true
        });
        $('#max_bed_id').select2({
            placeholder: 'Select Bedrooms',
            allowClear: true
        });



        $('#min_bath_id').select2({
            placeholder: 'Select Bathrooms',
            allowClear: true
        });
        $('#max_bath_id').select2({
            placeholder: 'Select Bathroom',
            allowClear: true
        });

        $('#for').select2({
            placeholder: 'Select Purpose',
            allowClear: true
        });

        function locationSelect2() {
            $('#communities').select2({
                placeholder: 'Location',
                allowClear: true,
                closeOnSelect: false,
                ajax: {
                    url: '{{ route('ajax.community.select2') }}',
                    dataType: 'json',
                    type: 'GET',
                    processResults({
                        data
                    }) {

                        return {
                            results: $.map(data, function(item) {
                                return {
                                    text: `${item.path_name}`,
                                    id: item.id
                                }
                            })
                        }
                    }
                }
            });

            $('#sub_communities').select2({
                placeholder: 'Tower, community, locality',
                allowClear: true,
                closeOnSelect: false,

                ajax: {

                    url: '{{ route('ajax.sub-community.property.select2') }}',
                    dataType: 'json',
                    data: function(term, page) {
                        return {
                            q: term, // search term
                            communities: $("#communities").val(),
                            page_limit: 10
                        };
                    },
                    processResults({
                        data
                    }) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    text: `${item.text}`,
                                    id: item.id,
                                    'lat': item.lat,
                                    'long': item.long,
                                    'path': item.path,
                                    'type': item.type
                                }
                            })
                        }
                    }
                }
            });
        }

        locationSelect2();


        const locations = [];
        $('#sub_communities').on('select2:select', function(e) {
            $("#location_address").html("")
            $("#location_type").val("")
            var data = e.params.data;
            // console.log(data.long);
            let lat = data.lat;
            let long = data.long;
            let path = data.path;
            let type = data.type;
            let id = data.id;
            let cmb = data.id + ':' + data.type;
            locations.push(cmb);
            $("#locations").val(locations);
        });

        $('#sub_communities').on("select2:unselect", function(e) {
            var data = e.params.data;
            let type = data.type;
            let id = data.id;
            let cmb = data.id + ':' + data.type;
            const index = locations.indexOf(cmb);
            if (index > -1) { // only splice array when item is found
                locations.splice(index, 1); // 2nd parameter means remove one item only
            }
            $("#locations").val(locations);

        });


        $(document).on("click", "#saveBtn", function() {
            $("#submitBtn").trigger("click")
        })

        let mobileInput = initPhoneValidator();


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
            $("#saveBtn").prop('disabled', true);
            $('#screen_loader').removeClass('d-none');
            event.preventDefault();

            function save() {
                var formData = new FormData($("#mainForm")[0]);
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    },
                    url: "{{ route('api.thirdparty.submit_lead') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: formData,
                    cache: false,
                    processData: false,
                    contentType: false,
                    success: function(result) {
                        $('#screen_loader').addClass('d-none');
                        if (result.status == '1') {
                            showToastify(result.msg, "success")
                            // $('.formDiv').addClass('d-none');

                            $("#saveBtn").prop('disabled', false); //enable submit button
                            $('#mainForm').trigger('reset'); //
                            $(".inputs").val("").trigger("change")
                            $(".inputs").removeClass("is-invalid")
                            $("select.inputs").val("").trigger("change")
                            viewData(result.id)
                            dataTable.row().order([10, 'DESC']).draw();
                            isUpdate =
                                true; // set isUpdate to false so that when we try to add new category it will be create request
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
                        $('#screen_loader').addClass('d-none');

                    },
                });
            }

            let status = $("#lead_status_id").val()
            let sub_status = $("#lead_sub_status_id").val()
            if (status == 2 && sub_status == 2) {
                swal({
                        title: "Note",
                        text: "Note: This Lead will be converted into contact. You will not be able to Edit this lead anymore!",
                        icon: 'warning',
                        buttons: true,
                        dangerMode: true,
                    })
                    .then((willDelete) => {
                        if (willDelete) { // on confirm delete the category
                            save()
                        }
                    });
            } else {
                save();
            }

        });
    </script>
@endsection
