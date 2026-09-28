@extends('layouts.master-without-nav')
@section('title')
    Search Listing Form
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
                                    <ul class="nav nav-tabs nav-justified mb-3" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link active" data-bs-toggle="tab" href="#search-tab" role="tab" aria-selected="false" tabindex="-1">
                                                Search My Listings
                                            </a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link" data-bs-toggle="tab" href="#create-tab" role="tab" aria-selected="true">
                                                Create a Listing
                                            </a>
                                        </li>
                                    </ul>
                                    <div class="tab-content text-muted">
                                        <div class="tab-pane active" id="search-tab" role="tabpanel">
                                            <div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <select name="" id="property_id" class="form-control select2">
                                                            <option value="">Select Listing</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-12 mt-3 d-none" id="property-details">
                                                        <div class="text-end">
                                                            <a href="javascript:void(0)" id="send-to-telegram-button">Send to Telegram</a>
                                                        </div>
                                                        <div>
                                                            <h4 id="show-title"></h4>
                                                            <p class="pxp-sp-top-address pxp-text-light" id="show-path">-</p>
                                                            <div class="d-flex">
                                                                <div class="pxp-sp-top-price mt-md-0" style="font-size:32px;" id="show-price"></div>
                                                            </div>
                                                            <div class="col-12">
                                                                <div class="pxp-single-property-section">
                                                                    <h5>Key Details</h5>
                                                                    <div class="row">
                                                                        <div class="col-6">
                                                                            <dl>
                                                                                <dt>Property Type</dt>
                                                                                <dd id="show-property_type">-</dd>
                                                                                <dt>Baths</dt>
                                                                                <dd id="show-bath">-</dd>
                                                                                <dt>Parking Spaces</dt>
                                                                                <dd id="show-parking">-</dd>
                                                                            </dl>
                                                                        </div>
                                                                        <div class="col-6">
                                                                            <dl>
                                                                                <dt>Beds</dt>
                                                                                <dd id="show-bed">-</dd>
                                                                                <dt>Size</dt>
                                                                                <dd id="show-built_up_area">-</dd>
                                                                                <dt>Reference #</dt>
                                                                                <dd id="show-ref_no">-</dd>
                                                                            </dl>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="pxp-single-property-section mt-4 mt-md-5">
                                                                    <h3>Overview</h3>
                                                                    <div class="mt-3 mt-md-4" id="show-description">-</div>
                                                                </div>

                                                                <div class="pxp-single-property-section mt-4 mt-md-5">
                                                                    <h3>Amenities</h3>
                                                                    <div class="row mt-3 mt-md-4" id="show-amenities">-</div>
                                                                </div>

                                                                <div class="pxp-single-property-section mt-4 mt-md-5">
                                                                    <h3>Location Details</h3>
                                                                    <div class="row">
                                                                        <div class="col-6">
                                                                            <dl>
                                                                                <dt>Tower/Property</dt>
                                                                                <dd id="show-community_property">-</dd>
                                                                                <dt>Community</dt>
                                                                                <dd id="show-community">-</dd>
                                                                            </dl>
                                                                        </div>
                                                                        <div class="col-6">
                                                                            <dl>
                                                                                <dt>Sub community</dt>
                                                                                <dd id="show-sub_community">-</dd>
                                                                                <dt>State/City</dt>
                                                                                <dd>Dubai</dd>
                                                                            </dl>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="create-tab" role="tabpanel">
                                            <form id="create-form" class="needs-validation" novalidate>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <label for="for" class="form-label">Category*</label>
                                                        <select name="for" id="for" class="form-control select2" required>
                                                            <option value="">Select property category</option>
                                                            @foreach(\App\Models\Property::FOR_SELECT ?? [] as $category)
                                                                <option value="{{ $category }}">{{ $category }}</option>
                                                                @endforeach
                                                        </select>
                                                        <span class="invalid-feedback">
                                                            Please select property category.
                                                        </span>
                                                    </div>
                                                    <div class="col-md-12 mt-3">
                                                        <label for="property_category_id" class="form-label">Type*</label>
                                                        <select name="property_category_id" id="property_category_id" class="form-control select2" required>
                                                            <option value="">Select property category</option>
                                                            @foreach($propertyCategories ?? [] as $category)
                                                                <option value="{{ $category->id }}">{{ $category->title }}</option>
                                                                @endforeach
                                                        </select>
                                                        <span class="invalid-feedback">
                                                            Please select property category.
                                                        </span>
                                                    </div>
                                                    <div class="col-md-12 mt-3">
                                                        <label for="bed_id" class="form-label">Beds*</label>
                                                        <select name="bed_id" id="bed_id" class="form-control select2">
                                                            <option value="">Select beds</option>
                                                            @foreach($beds ?? [] as $bed)
                                                                <option value="{{ $bed->id }}">{{ $bed->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <span class="invalid-feedback">
                                                            Please select beds.
                                                        </span>
                                                    </div>
                                                    <div class="col-md-12 mt-3">
                                                        <label for="bath_id" class="form-label">Baths*</label>
                                                        <select name="bath_id" id="bath_id" class="form-control select2">
                                                            <option value="">Select Baths</option>
                                                            @foreach($baths ?? [] as $bath)
                                                                <option value="{{ $bath->id }}">{{ $bath->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <span class="invalid-feedback">
                                                            Please select Baths.
                                                        </span>
                                                    </div>
                                                    <div class="col-md-12 mt-3">
                                                        <label for="location_id" class="form-label">Location*</label>
                                                        <input type="hidden" id="location_type" name="location_type">
                                                        <input type="hidden" id="location_id" name="location_id">
                                                        <select id="location" class="form-control select2" required>
                                                            <option value="">Select location</option>
                                                        </select>
                                                        <span class="invalid-feedback">
                                                            Please select location.
                                                        </span>
                                                    </div>
                                                    <div class="col-md-12 mt-3">
                                                        <div class="d-flex justify-content-start align-items-center gap-3">
                                                            <label for="built_up_area" class="form-label">Size(<span id="">Sqft</span>)*</label>
                                                            <div class="flex-shrink-0">
                                                                <div class="form-check form-switch">
                                                                    <input class="form-check-input" type="checkbox" role="switch" id="size-converter" name="size-converter">
                                                                    <label class="form-check-label" for="size-converter">Sqm</label>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-lg-6 col-md-6 mt-2 ">
                                                                <input type="number" step="0.01" class="size-input form-control inputs effect-publish" id="built_up_area" name="built_up_area" placeholder="Enter BUA">
                                                                <div class="invalid-feedback">
                                                                    Please enter build-up area.
                                                                </div>

                                                            </div>
                                                            <div class="col-lg-6 col-md-6 mt-2">
                                                                <input type="number" step="0.01" class="size-input form-control inputs" id="plot_size" name="plot_size" placeholder="Enter plot size">
                                                                <div class="invalid-feedback">
                                                                    Please enter plot size.
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 mt-3">
                                                        <label for="parking" class="form-label">Parkings*</label>
                                                        <input type="number" class="form-control inputs" id="parking"
                                                               name="parking" min="0" required>
                                                        <span class="invalid-feedback">
                                                            Please enter parking.
                                                        </span>
                                                    </div>
                                                    <div class="col-md-12 mt-3">
                                                        <label for="view" class="form-label">Views*</label>
                                                        <select name="view[]" id="view" class="form-control select2" multiple required>
                                                            @foreach(\App\Models\Property::VIEWS ?? [] as $key => $value)
                                                                <option value="{{ $key }}">{{ $value }}</option>
                                                            @endforeach
                                                        </select>
                                                        <span class="invalid-feedback">
                                                            Please select views.
                                                        </span>
                                                    </div>

                                                    <div class="col-md-12 mt-3">
                                                        <label for="title" class="form-label">Title*</label>
                                                        <input type="text" class="form-control inputs"  id="title"
                                                               name="title" required>
                                                        <span class="invalid-feedback">
                                                            Please enter title.
                                                        </span>
                                                    </div>
                                                    <div class="col-md-12 mt-3">
                                                        <label for="price" class="form-label">Price*</label>
                                                        <input type="number" class="form-control inputs"  id="price"
                                                               name="price" required>
                                                        <span class="invalid-feedback">
                                                            Please enter price.
                                                        </span>
                                                    </div>
                                                    <div class="col-md-12 mt-3">
                                                        <label for="description" class="form-label">Description*</label>
                                                        <textarea name="description" class="form-control inputs" id="description" rows="10" maxlength="2000"></textarea>
                                                        <span class="invalid-feedback">
                                                            Please enter description 750-2000 characters.
                                                        </span>
                                                    </div>
                                                    <div class="col-md-12 mt-3">
                                                        <label for="furnished" class="form-label">Furnishing*</label>
                                                        <select id="furnished" name="furnished" class="form-control select2" required>
                                                            <option value="">Select furnishing</option>
                                                            @foreach(\App\Models\Property::FURNISHED_SELECT as $key => $value)
                                                                <option value="{{ $key }}">{{ $value }}</option>
                                                            @endforeach
                                                        </select>
                                                        <span class="invalid-feedback">
                                                            Please select furnishing.
                                                        </span>
                                                    </div>
                                                    <div class="col-md-12 mt-3">
                                                        <label for="amenities" class="form-label">Amenities</label>
                                                        <div class="scrollable-features">
                                                            @foreach ($features ?? [] as $feature)
                                                                <div class="single-feature">
                                                                    <div class="form-check amenities-form-check">
                                                                        <input class="btn-check form-check-input inputs" name="features[]"
                                                                               type="checkbox" value="{{ $feature->id }}"
                                                                               data-title="{{ $feature->title }}" id="feature-{{ $feature->id }}"
                                                                               autocomplete="off">
                                                                        <label class="btn btn-amenities-primary btn-custom btn-primary"
                                                                               for="feature-{{ $feature->id }}">
                                                                            {{ $feature->title }}
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12 mt-3 text-end">
                                                        <button class="btn btn-primary"
                                                                id="create-button">Create</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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
        $(".select2").select2();
        $('#property_id').select2({
            placeholder: 'Select a listing',
            allowClear: true,
            ajax: {
                url: "{{ route('api.searchMyListing') }}",
                data: function (params) {
                    return {
                        search_keywords: params.term,
                        telegram_chat_id: "{{ request("telegram_chat_id") }}"
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.data?.map(item => ({
                            id: item.id,
                            text: `${item.publish_status} ${item.ref_no} ${item.location} ${item.for}`
                        }))
                    };
                }
            }
        });

        $(document).on("change", "#property_id", function (){
            const id = $(this).val();
            $("#property-details").addClass("d-none");
            if (id){
                getPropertyDetails(id)
            }
        })

        function getPropertyDetails(id){
            if (id){
                $.ajax({
                    url: "{{ route('api.listingDetail') }}",
                    method: "GET",
                    data: {
                        id
                    },
                    success: function(response) {
                        const {data} = response;
                        $("#show-price").text(`AED ${data?.price?.price || "-"}`);
                        $("#show-title").text(data?.title || "-");
                        $("#show-path").text(data?.community_property?.path_name || data?.sub_community?.path_name || "-");
                        $("#show-property_type").text(data?.property_category?.title || "-");
                        $("#show-bed").text(data?.bed?.name || "-");
                        $("#show-bath").text(data?.bath?.name || "-");
                        $("#show-built_up_area").text(data?.built_up_area || "-");
                        $("#show-parking").text(data?.parking || "-");
                        $("#show-ref_no").text(data?.ref_no || "-");
                        $("#show-description").html(data?.description || "-");

                        $("#show-community_property").text(data?.community_property?.name || "-");
                        $("#show-community").text(data?.community?.name || "-");
                        $("#show-sub_community").text(data?.sub_community?.name || "-");

                        $("#property-details").removeClass("d-none");
                    },
                })
            }
        }

        $("#location_type").val("");
        $('#location').select2({
            placeholder: 'Tower, community, locality',
            allowClear: true,
            ajax: {
                url: '{{ route('ajax.sub.community-properties.select2') }}',
                dataType: 'json',
                type: 'GET',
                processResults({
                                   data
                               }) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: `${item.text}`,
                                description: `${item.path}`,
                                id: `${item.id+"?"+item.type}`,
                                type: item.type
                            }
                        })
                    };
                }
            },
            templateResult: function (result) {
                let $result = $('<div>');
                $result.text(result.text);
                $result.append('<div class="description">' + result.description +
                    '</div>');
                return $result;
            },
            escapeMarkup: function(markup) {
                return markup;
            }
        });

        $(document).on("change", "#location", function() {
            let [id, type] = $(this).val().split('?');
            $("#location_id").val(id);
            $("#location_type").val(type);
        })

        $(document).on('change', '.size-input', function(e) {
            if ($('#size-converter').is(':checked')) {
                let value = $(this).val();
                if (value) {
                    let convertedValue = value * 10.7639;
                    convertedValue = convertedValue.toFixed(2);
                    $(this).val(convertedValue);
                    showToastify('Converted Sqm to Sqft.', 'success');
                }
            }
        });

        $(document).on('change', ' #size-converter', function(e) {
            if ($('#size-converter').is(':checked')) {
                let value = $('#built_up_area').val();
                if (value) {
                    let convertedValue = value / 10.7639;
                    convertedValue = convertedValue.toFixed(2);
                    $('#built_up_area').val(convertedValue);
                    $('#size_unit').text('Sqft');
                }
                let value_plot = $('#plot_size').val();
                if (value_plot) {
                    let convertedValue_plot = value_plot / 10.7639;
                    convertedValue_plot = convertedValue_plot.toFixed(2);
                    $('#plot_size').val(convertedValue_plot);
                    showToastify('Sqft to sqm', 'success');
                }

            } else {
                let value = $('#built_up_area').val();
                if (value) {
                    let convertedValue = value * 10.7639;
                    convertedValue = convertedValue.toFixed(2);
                    $('#built_up_area').val(convertedValue);
                    $('#size_unit').text('Sqm');
                }

                let value_plot = $('#plot_size').val();
                if (value_plot) {
                    let convertedValue_plot = value_plot * 10.7639;
                    convertedValue_plot = convertedValue_plot.toFixed(2);
                    $('#plot_size').val(convertedValue_plot);

                }
            }
        });

        $(document).on("click", "#send-to-telegram-button", function (){
            const id = $("#property_id").val();
            if (id){
                alert("Sent")
            }
        })


        $(document).on('submit', '#create-form', function(event) {
            $("#create-button").prop('disabled', true);
            event.preventDefault();

            let formData = new FormData($("#create-form")[0]);

            formData.append("telegram_chat_id",  "{{ request('telegram_chat_id') }}")

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                url: "{{ route('api.createListing') }}",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function(result) {
                    if (result.status){
                        showToastify(result.message, "success")
                    }else{
                        showToastify(result.message, "error")
                    }
                    $("#create-button").prop('disabled', false);

                },
                error: function(jqXhr, json, errorThrown) {
                    $("#create-button").prop('disabled', false);
                    let data = jqXhr.responseJSON;
                    if (data.errors) {
                        $.each(data.errors, function(index, item) {
                            showToastify(item[0], "error")
                        })
                    }
                    if (jqXhr.status == 500 || jqXhr.status == 400) {
                        showToastify(data.message, "error")
                    }
                },
            });
        });
    </script>
@endsection
