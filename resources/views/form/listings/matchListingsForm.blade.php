@extends('layouts.master-without-nav')
@section('title')
    Match Listing Form
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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"
          type="text/css" />

    <link rel="stylesheet" href="{{ asset('assets/libs/nouislider/nouislider.min.css') }}">
@endsection

@section('content')
    @include('admin.partials.loader')

    <nav class="navbar-menu d-none"></nav>
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

        <div class="auth-page-content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center mt-2">
                            <img src="{{ asset('assets/images/logo/ax-short-logo.png') }}" alt="">
                        </div>

                    </div>
                </div>
                <div class="row  justify-content-center formDiv">
                    <div class="col-md-8">
                        <div class="card mt-4">

                            <div class="card-body p-4">
                                <div class="p-2 mt-2 " id="formdiv">
                                    <form class="needs-validation" novalidate id="mainForm">
                                        <input type="hidden" id="location_id" name="location_id">
                                        <input type="hidden" id="location_type" name="location_type">
                                        <div class="row">
                                            <div class="col-md-12 text-center text-primary">
                                                <h2>Match Listings</h2>
                                                <hr>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="validationCustom02" class="form-label">Location</label>
                                                <input type="text" class="form-control inputs updateItem"  id="location"
                                                       name="location">
                                                <ul class="list-group list-hm" style="display: none;"></ul>
                                                <span id="clear-location" class="fa fa-times mr-3 my-auto"
                                                      style="display: none; height: auto"></span>
                                            </div>

                                            <div class="col-md-12 mt-3">
                                                <label for="price" class="mb-2">Price</label>
                                                <div id="price-range-slider"></div>
                                                <div class="mt-2">
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <input type="text" class="form-control inputs updateItem"
                                                                   data-name="min_price" id="min_price"
                                                                   name="min_price">
                                                            <small>Minimum</small>
                                                        </div>
                                                        <div class="col-6">
                                                            <input type="text" class="form-control inputs updateItem"
                                                                   data-name="max_price" id="max_price"
                                                                   name="max_price">
                                                            <small>Maximum</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Bedrooms</label>
                                                <div class="hstack gap-2 flex-wrap">
                                                    @foreach($beds ?? [] as $bed)
                                                        <input type="radio" class="btn-check" name="bed_id" id="bed-{{ $bed->id }}">
                                                        <label class="btn btn-sm btn-outline-primary material-shadow" for="bed-{{ $bed->id }}">{{ $bed->name }}</label>
                                                    @endforeach
                                                </div>
                                            </div>

                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Baths</label>
                                                <div class="hstack gap-2 flex-wrap">
                                                    @foreach($baths ?? [] as $bath)
                                                        <input type="radio" class="btn-check" name="bath_id" id="bath-{{ $bath->id }}">
                                                        <label class="btn btn-sm btn-outline-primary material-shadow" for="bath-{{ $bath->id }}">{{ $bath->name }}</label>
                                                    @endforeach
                                                </div>
                                            </div>

                                            <div class="col-md-12 mt-3">
                                                <label for="price" class="mb-2">Size</label>
                                                <div id="size-range-slider"></div>
                                                <div class="mt-2">
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <input type="text" class="form-control inputs updateItem"
                                                                   data-name="min_size" id="min_size"
                                                                   name="min_size">
                                                            <small>Minimum</small>
                                                        </div>
                                                        <div class="col-6">
                                                            <input type="text" class="form-control inputs updateItem"
                                                                   data-name="max_size" id="max_size"
                                                                   name="max_size">
                                                            <small>Maximum</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-3 text-end">
                                            <div class="col-12">
                                                <button type="button" class="btn btn-primary"
                                                        id="search-button">Search</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
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
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
    <script src="{{ asset('assets/js/helpers.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ URL::asset('assets/libs/nouislider/nouislider.min.js') }}"></script>
    <script>
        let priceRangeSlider = document.getElementById('price-range-slider');
        if (priceRangeSlider)
            noUiSlider.create(priceRangeSlider, {
                start: [0, Number("{{ $maxPrice ?? 1500000 }}")],
                connect: true,
                range: {
                    'min': 0,
                    'max': Number("{{ $maxPrice ?? 1500000 }}")
                }
            });

        let minPrice = document.getElementById('min_price');
        let maxPrice = document.getElementById('max_price');
        if (maxPrice && priceRangeSlider) {
            priceRangeSlider.noUiSlider.on('update', function (values, handle) {
                let value = values[handle];

                if (handle) {
                    maxPrice.value = value;
                } else {
                    minPrice.value = Math.round(value);
                }
            });

            minPrice.addEventListener('change', function () {
                priceRangeSlider.noUiSlider.set([this.value, null]);
            });

            maxPrice.addEventListener('change', function () {
                priceRangeSlider.noUiSlider.set([null, this.value]);
            });
        }

        let sizeRangeSlider = document.getElementById('size-range-slider');
        if (sizeRangeSlider)
            noUiSlider.create(sizeRangeSlider, {
                start: [0, Number("{{ $maxSize ?? 15000 }}")],
                connect: true,
                range: {
                    'min': 0,
                    'max': Number("{{ $maxSize ?? 15000 }}")
                }
            });

        let minSize = document.getElementById('min_size');
        let maxSize = document.getElementById('max_size');
        if (maxSize && sizeRangeSlider) {
            sizeRangeSlider.noUiSlider.on('update', function (values, handle) {
                let value = values[handle];

                if (handle) {
                    maxSize.value = value;
                } else {
                    minSize.value = Math.round(value);
                }
            });

            minSize.addEventListener('change', function () {
                sizeRangeSlider.noUiSlider.set([this.value, null]);
            });

            maxSize.addEventListener('change', function () {
                sizeRangeSlider.noUiSlider.set([null, this.value]);
            });
        }

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
        });


        $(document).on('keyup', '#location' ,function() {
            let query = $('#location').val();
            $('#detail').html('');

            if (query.length >= 3) {
                $('.list-group').slideDown();
                $('#clear-location').show();
                $.ajax({
                    url: "{{ route('front.auth.search_location') }}",
                    method: "GET",
                    data: 'keyword=' + $(this).val(),
                    success: function(data) {
                        let output = '';

                        $.each(data, function(key, value) {
                            let txt = String(value.name).replace(new RegExp(query,
                                "gi"), "<span class='marktext' >$&</span>");
                            output +=
                                '<li class="list-group-item list-hm-item contsearch"><a p_txt="' +
                                value.name + '" p_type="' + value.type +
                                '"p_value="' + value.id +
                                '" href="javascript:void(0)" class="gsearch" style="color:#000;text-decoration:none;"> ' +
                                txt + '<br><small> ' + value.path_name +
                                '</small></a></li>';

                        });

                        $('.list-group').html(output);
                    }
                })
            }
            if (query.length == 0) {
                $('.list-group').slideUp();
                $('.list-group').html('');
                $('#clear-location').hide();
                $('#location_id').val('');
                $('#location_type').val('');
                $("#location").val('');

                $('#search_prop_form').submit();

            }
        });

        $("#clear-location").click(function() {

            $('.list-group').slideUp();
            $('.list-group').html('');
            $('#clear-location').hide();
            $('#location_id').val('');
            $('#location_type').val('');
            $("#location").val('');
            $('#search_prop_form').submit();

        });

        $(document).on('click', '.gsearch', function() {
            $('#location_id').val($(this).attr("p_value"));
            $('#location_type').val($(this).attr("p_type"));

            $('#location').val($(this).attr("p_txt"));
            $('.list-group').slideUp();
        });
    </script>
@endsection
