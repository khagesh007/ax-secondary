@extends('layouts.master-without-nav')
@section('title')
    Lead Form
@endsection
@section('meta_description','Lead Generation Form')
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
                                                <h1>Lead Form</h1>
                                            </div>
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <label for="name" class="form-label required">Full name</label>
                                                <input type="text" class="form-control inputs" id="name"
                                                    name="name" value="" required placeholder="John Doe">
                                                <div class="valid-feedback">
                                                    Looks good!
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="phone" class="form-label required">Mobile <small>example:+971585756178</small></label>
                                                <input type="tel" class="form-control inputs" name="mobile"
                                                    id="mobile" required placeholder="Enter mobile" autocomplete="off"
                                                    data-intl-tel-input-id="0">

                                                    <div class="text-danger mobileValidation">

                                                    </div>

                                                <div class="valid-feedback">
                                                    Please enter a valid phone number.
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <label for="email" class="form-label required">Email</label>
                                                <input type="email" name="email" class="form-control inputs" id="email"
                                                    value="" required placeholder="email@website.com">
                                                <div class="valid-feedback">
                                                   Please enter a valid email.
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="calls" class="form-label">Call Recording</label>
                                                <input class="form-control inputs" id="formSizeDefault" type="file" name="related_file">

                                                <div class="invalid-feedback">
                                                    Please enter a valid File
                                                </div>
                                            </div>
                                        </div>




                                        {{-- <div class="row mt-3">
                                            <div class="col-md-12">
                                                <label for="category" class="form-label">Category</label>
                                            </div>

                                            <div class="col-md-2">
                                                <div class="form-check">
                                                    <input type="radio" class="form-check-input inputs" id="rent"
                                                        name="radio-stacked" required="">
                                                    <label class="form-check-label" for="rent">Rent</label>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-check">
                                                    <input type="radio" class="form-check-input inputs" id="sale"
                                                        name="radio-stacked" required="">
                                                    <label class="form-check-label" for="sale">Sale</label>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-check">
                                                    <input type="radio" class="form-check-input inputs" id="rent_sale"
                                                        name="radio-stacked" required="">
                                                    <label class="form-check-label" for="rent_sale">Rent/Sale</label>

                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-check">
                                                    <input type="radio" class="form-check-input inputs" id="commercial"
                                                        name="radio-stacked" required="">
                                                    <label class="form-check-label" for="commercial">Commercial</label>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-check">
                                                    <input type="radio" class="form-check-input inputs" id="buyer"
                                                        name="radio-stacked" required="">
                                                    <label class="form-check-label" for="buyer">Buyer</label>
                                                </div>
                                            </div>
                                        </div> --}}



                                        {{-- <div class="row mt-3">
                                            <div class="col-md-12">
                                                <label for="type" class="form-label">Property Type</label>
                                            </div>

                                            <div class="col-md-2">
                                                <div class="form-check">
                                                    <input type="radio" class="form-check-input inputs inputs" id="studio"
                                                        name="radio-stacked" required="" >
                                                    <label class="form-check-label" for="studio">Studio</label>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-check">
                                                    <input type="radio" class="form-check-input inputs" id="apartment"
                                                        name="radio-stacked" required="">
                                                    <label class="form-check-label" for="apartment">Apartment</label>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-check">
                                                    <input type="radio" class="form-check-input inputs" id="villa"
                                                        name="radio-stacked" required="">
                                                    <label class="form-check-label" for="villa">Villa</label>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-check">
                                                    <input type="radio" class="form-check-input inputs" id="townhouse"
                                                        name="radio-stacked" required="">
                                                    <label class="form-check-label" for="townhouse">Townhouse</label>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-check">
                                                    <input type="radio" class="form-check-input inputs" id="plot"
                                                        name="radio-stacked" required="">
                                                    <label class="form-check-label" for="plot">Plot</label>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-check">
                                                    <input type="radio" class="form-check-input inputs" id="building"
                                                        name="radio-stacked" required="">
                                                    <label class="form-check-label" for="building">Building</label>
                                                </div>
                                            </div>
                                        </div> --}}
                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <label for="validationCustom02" class="form-label">Community</label>
                                                <select class="inputsCriteria form-control inputs" multiple id="communities"
                                                    name="communities[]"></select>

                                            </div>

                                            <div class="col-md-6">
                                                <label for="validationCustom02" class="form-label">Sub Community</label>
                                                <input type="hidden" name="locations" id="locations">
                                                <select class="select2 inputsCriteria inputs" multiple
                                                    id="sub_communities" name="sub_communities[]"></select>

                                            </div>
                                        </div>

                                        <div class="row mt-3">


                                            <div class="col-md-6">
                                                <label for="validationCustom02" class="form-label">Min Bedrooms</label>
                                                <select name="min_bed_id" id="min_bed_id" class="form-control inputs"
                                                    data-name="min_bed_id" data-calc_value="0">
                                                    <option value="">- Select -</option>
                                                    @foreach ($beds as $bed)
                                                        <option value="{{ $bed->id }}">{{ $bed->name }}</option>
                                                    @endforeach
                                                </select>

                                                <div class="invalid-feedback">
                                                    Please enter a valid phone number.
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="validationCustom02" class="form-label">Max Bedrooms</label>
                                                <select name="max_bed_id" id="max_bed_id" class="form-control inputs"
                                                    data-name="max_bed_id" data-calc_value="0">
                                                    <option value="">- Select -</option>
                                                    @foreach ($beds as $bed)
                                                    <option value="{{ $bed->id }}">{{ $bed->name }}</option>
                                                @endforeach
                                                </select>

                                                <div class="invalid-feedback">
                                                    Please enter a valid phone number.
                                                </div>
                                            </div>


                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-md-12">
                                                <label for="description" class="form-label">Description</label>
                                                <textarea class="form-control inputs" id="formSizeDefault" name="description"
                                                    placeholder="Description for the property" rows="4"></textarea>
                                                <div class="invalid-feedback">
                                                    Please enter a valid phone number.
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-4">
                                            <button class="btn btn-success w-100" id="saveBtn" type="button">Submit
                                                From</button>
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
        $('#min_bed_id').select2({
            placeholder: 'Select Bedrooms',
            allowClear: true
        });
        $('#max_bed_id').select2({
            placeholder: 'Select Bedrooms',
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

        let mobileInput = initPhoneValidator()
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
                            swal("Success!", result.msg, "success");

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
