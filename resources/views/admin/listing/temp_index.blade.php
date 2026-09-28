@extends('admin.layouts.master', ['layout' => 'vertical'])
@section('title')
    Temp Listings
@endsection
@section('style')
    <link href="{{ URL::asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="{{ URL::asset('assets/libs/lightbox2/css/lightbox.min.css') }}" />
    <link href="{{ URL::asset('assets/libs/dropzone/dropzone.min.css') }}" rel="stylesheet">

    <style>
        #reraDateRange span i {
            font-size: 14px !important;
            margin-right: 2px;
            vertical-align: text-top;
        }

        .card {
            position: relative;
        }

        .event-type-badge {
            display: none;
        }

        .card:hover .event-type-badge {
            display: block;
        }

        .iframe {
            width: 100%;
            max-height: 2000px;
            min-height: 550px;
        }

        .iframecontainer {
            width: 100%;
            height: 100%;
            overflow: scroll;
        }

        .containerbox {
            position: relative;
            overflow: hidden;
            width: 100%;
            padding-top: 56.25%;
            /* 16:9 Aspect Ratio (divide 9 by 16 = 0.5625) */
        }

        /* Then style the iframe to fit in the container div with full height and width */
        .responsive-iframe {
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
            width: 100%;
            height: 100%;
        }

        .nav-pills .nav-link.active,
        .nav-pills .show>.nav-link {
            background-color: #d3a188 !important;
            color: #000;
        }
    </style>
@endsection

@section('content')
    @include('admin.partials.loader')


    <form id="mainForm" class="row needs-validation" novalidate>
        <div class="col-lg-12" id="top-bar">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title mb-0 ">Temp Listings</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Property</a></li>
                            <li class="breadcrumb-item d-none" id="ref_no_area">
                                <div class="d-flex justify-content-start align-items-center">
                                    <span id="ref_no"></span>
                                    <a href="javascript:void(0)" onclick="copyToClipboard('#ref_no')" class="text-print"
                                        title="Copy to clipboard"> <i class="ri-clipboard-line mx-lg-2"
                                            style="font-size: 14px"></i> </a>
                                </div>
                            </li>
                        </ol>
                    </div>

                    <div class="flex-shrink-0">
                        <div class="form-check form-switch form-switch-right form-switch-md">
                            <button type="submit" class="btn btn-success d-none action-save" id="saveBtn">Save</button>
                            
                           
                            @if (auth()->user()->hasRole(['Admin', 'Super Admin', 'Admin - Abu Dhabi']))
                                <button type="button" id="viewPendingChanges" class="btn btn-danger d-none"
                                    title="Check pending changes">
                                    <i class=" ri-alert-line align-bottom me-1"></i>
                                </button>
                            @endif
                            <button type="button" class="btn btn-light d-none action-close" id="closeBtn">Close</button>
                            {{-- @can('Properties Delete')
                                <button type="button" class="btn btn-soft-danger" id="multiDeleteBtn"
                                        onClick="deleteMultiple()"><i
                                        class="ri-delete-bin-2-line"></i></button>
                            @endcan --}}
                           
                            <button type="button" id="dropdownMenuLink1" data-bs-toggle="dropdown" aria-expanded="false"
                                class="btn btn-soft-info d-none action-more dropdown-toggle">More</button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink1">
                               
                                
                                @can('Properties Delete')
                                    <li class="own_property d-none">
                                        <hr>
                                        <a class="dropdown-item" href="javascript:void(0)" id="deleteBtn"
                                            title="Soft Delete">
                                            <i class="ri-delete-bin-2-line align-bottom me-1 fs-20"></i> Delete
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </div>
                    </div>

                   
                </div>
            </div>
        </div>
        <div class="col-12">
            @include('admin.listing.includes.temp_form')
            @include('admin.listing.includes.passwords')
        </div>
    </form>
    @includeIf('admin.listing.includes.filterForm')
    <div class="row d-none">
        <div class="col-xxl-3 col-sm-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="fw-medium text-muted mb-0">Total Property</p>
                            <h2 class="mt-4 ff-secondary fw-semibold"><span class="counter-value"
                                    data-target="234">0</span>k</h2>
                            <p class="mb-0 text-muted"><span class="badge bg-light text-success mb-0">
                                    <i class="ri-arrow-up-line align-middle"></i> 17.32 %
                                </span> vs. previous month</p>
                        </div>
                        <div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-info rounded-circle fs-4">
                                    <i class="ri-ticket-2-line"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div><!-- end card body -->
            </div> <!-- end card-->
        </div>
        <!--end col-->
        <div class="col-xxl-3 col-sm-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="fw-medium text-muted mb-0">Pending Verification</p>
                            <h2 class="mt-4 ff-secondary fw-semibold"><span class="counter-value"
                                    data-target="64.5">0</span>k</h2>
                            <p class="mb-0 text-muted"><span class="badge bg-light text-danger mb-0">
                                    <i class="ri-arrow-down-line align-middle"></i> 0.87 %
                                </span> vs. previous month</p>
                        </div>
                        <div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-warning rounded-circle fs-4">
                                    <i class="mdi mdi-timer-sand"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div><!-- end card body -->
            </div>
        </div>
        <!--end col-->
        <div class="col-xxl-3 col-sm-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="fw-medium text-muted mb-0">Rented Property</p>
                            <h2 class="mt-4 ff-secondary fw-semibold"><span class="counter-value"
                                    data-target="116.21">0</span>K</h2>
                            <p class="mb-0 text-muted"><span class="badge bg-light text-danger mb-0">
                                    <i class="ri-arrow-down-line align-middle"></i> 2.52 %
                                </span> vs. previous month</p>
                        </div>
                        <div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-success rounded-circle fs-4">
                                    <i class="ri-checkbox-circle-line"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div><!-- end card body -->
            </div>
        </div>
        <!--end col-->
        <div class="col-xxl-3 col-sm-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="fw-medium text-muted mb-0">Expired Property</p>
                            <h2 class="mt-4 ff-secondary fw-semibold"><span class="counter-value"
                                    data-target="14.84">0</span>%</h2>
                            <p class="mb-0 text-muted"><span class="badge bg-light text-success mb-0">
                                    <i class="ri-arrow-up-line align-middle"></i> 0.63 %
                                </span> vs. previous month</p>
                        </div>
                        <div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-danger rounded-circle fs-4">
                                    <i class="ri-delete-bin-line"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div><!-- end card body -->
            </div>
        </div>
        <!--end col-->
    </div>
    <!--end row-->

<div class="row mb-2">
    <div class="col-lg-12">
        <div class="table-sticky-header-container" id="table-sticky-header">
            
            <ul class="nav nav-tabs nav-tabs-custom nav-success mb-1 no-border justify-content-end float-right"
                role="tablist" style="float:right;">
                <li class="nav-item" role="presentation">
                    <button type="button"
                        class="btn btn-sm btn-soft-secondary waves-effect btn-m w-100 action-filter mt-1"
                        onclick="toggleAdvanceSearch();"><i class="ri-equalizer-fill "></i>
                        Advanced Filters
                    </button>
                </li>
            </ul>
        </div>
    </div>
</div>
    <div class="row">
        <div class="col-lg-12">

            <div class="card" id="tasksList">
                <div class="card-body">

                    <div class="table-responsive mb-4">
                        @can('Properties List')
                            <table id="dataTable" class="table align-middle" style="width:100%">
                                <thead class="">
                                    <tr class="text-uppercase table-light2 ">
                                        <th scope="col" style="width: 10px;">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="checkAll"
                                                    value="option" data-com.bitwarden.browser.user-edited="yes">
                                            </div>
                                        </th>
                                        <th>ID</th>
                                        <th>Ref</th>
                                        <th>Unit</th>
                                        <th>Type</th>
                                        <th>Category</th>
                                        <th>Rera Permit No.</th>
                                        <th>Rera Validity </th>
                                        <th width="15%">Location</th>
                                        <th>Beds</th>
                                        <th>Price</th>
                                        <th>Agent</th>
                                        <th>Updated by</th>
                                        <th>Updated</th>
                                        <th width="10%">Portals</th>
                                        
                                        <th>Status</th>
                                        <th>Created at</th>
                                        <th>Published at</th>
                                       


                                    </tr>
                                </thead>
                            </table>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
    @includeIf('admin.listing.includes.modals')
    @include('admin.events.include.modal')
    <div id="shootFormDiv"></div>
    <div id="eventModalsDiv"></div>
    <div id="vueWidgetApp">
        <listing-widget-index></listing-widget-index>
    </div>
@endsection
@section('script')
    @include('admin.partials.datatable')

    <script src="{{ URL::asset('assets/libs/dropzone/dropzone.min.js') }}"></script>
    {{-- <script src=" "></script> --}}

    <script src="{{ URL::asset('/assets/libs/glightbox/glightbox.min.js') }}"></script>
    <script src="{{ URL::asset('assets/libs/lightbox2/js/lightbox.min.js') }}"></script>
    <script src="{{ URL::asset('assets/libs/jquery.priceformat/jquery.priceformat.min.js') }}"></script>
    <script src="{{ URL::asset('assets/libs/cleave.js/cleave.js.min.js') }}"></script>
    <script src="{{ URL::asset('assets/libs/fg-emoji-picker/fg-emoji-picker.min.js') }}"></script>


    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-sortablejs@latest/jquery-sortable.js"></script>

    <script type="text/javascript"
        src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAP_KEY') }}&libraries=places&callback=Function.prototype">
    </script>
    <script src="{{ URL::asset('assets/js/pages/listing.js') }}"></script>
    <script src="{{ asset('vue/js/vueWidgets.js') }}"></script>
    <script>
        //event/calendar related urls
        let currentPage = "Listing";
        let isLive = false;
        let allowedListing = true;
        let getEventUrl = "{{ route('admin.events.get') }}";
        let currentUserRole = "{{ auth()->user()->roles->first()->name }}";
        let currentUserId = "{{ auth()->user()->id }}";
        let eventCreateUrl = "{{ route('admin.events.store') }}";
        let eventUpdateUrl = "{{ route('admin.events.update') }}";
        let getAllEventsType = "{{ route('admin.events.type.get.all') }}";
        let eventObj = {};
        let isEventPage = false;
        let contactSelect2url = "{{ route('ajax.contacts.select2') }}";
        let leadSelect2url = "{{ route('ajax.leads.select2') }}";
        let eventPropertyDetailsUrl = "{{ route('admin.temp-listing.properties.get') }}";
        let getAttendeesUrl = "{{ route('ajax.getUsersSelect2') }}";
        let propertySelect2 = "{{ route('ajax.properties.select2') }}";
        let getEventModalUrl = "{{ route('admin.events.calendar.form.modal') }}";
        let previewUrl = null;
        let printPage = null;
        //shoot job
        let shootFormMode = 'create';
        let isShootUpdate = false;
        let shootFormUrl = "{{ route('admin.jobs.shoot.store') }}";
        let getShootFormModalUrl = "{{ route('admin.jobs.shoot.form') }}";
        let agentSelect2Url = "{{ route('ajax.getUsersSelect2') }}";
        let getPropertiesSelect2 = "{{ route('ajax.properties.select2') }}";
        let shootPage = "";
        var propertyData = {};
        let filtersDataUd = '';
    </script>
    <script src="{{ URL::asset('/assets/js/plugins/event.js') }}?v={{ time() }}"></script>
    <script src="{{ URL::asset('/assets/js/pages/job-shoot.js') }}"></script>
    <script>
        let galleryArr = [];
        let charLimit = 2000;
        let fullyLoaded = false;
        let checkChanges = false;
        let mode = 'view';
        let own_property = false;
        let existingContact = null;
        let existingContactDetails = null;
        let form_submitted = 0;
        var emojiPicker = new FgEmojiPicker({
            trigger: [".emoji-btn"],
            removeOnSelection: false,
            closeButton: true,
            position: ["top", "right"],
            preFetch: true,
            dir: "assets/js/pages/plugins/json",
            insertInto: document.querySelector(".chat-input"),
        });

        // emojiPicker position
        var emojiBtn = document.getElementById("emoji-btn");
        emojiBtn.addEventListener("click", function() {
            setTimeout(function() {
                var fgEmojiPicker = document.getElementsByClassName("fg-emoji-picker")[0];
                if (fgEmojiPicker) {
                    var leftEmoji = window.getComputedStyle(fgEmojiPicker) ? window.getComputedStyle(
                        fgEmojiPicker).getPropertyValue("left") : "";
                    if (leftEmoji) {
                        leftEmoji = leftEmoji.replace("px", "");
                        leftEmoji = leftEmoji - 40 + "px";
                        fgEmojiPicker.style.left = leftEmoji;
                    }
                }
            }, 0);
        });

        function initPriceInput(price = "") {
            $("#price").val(price).trigger("change");
            new Cleave('#price', {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand'
            });
            $("#down_payment_price").val(price).trigger("change");
            new Cleave('#down_payment_price', {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand'
            });
        }

        initPriceInput()

        function initMap(lat, long, title) {
            const myLatLng = {
                lat: lat,
                lng: long
            };
            const map = new google.maps.Map(document.getElementById("google-map"), {
                zoom: 15,
                center: myLatLng,
            });

            new google.maps.Marker({
                position: myLatLng,
                map,
                title: title,
            });
            $("#mapModal").modal("show");
        }

        $(document).on("click", ".view-in-map", function() {
            let lat = $(this).data("lat");
            let long = $(this).data("long");
            let path = $(this).data("path");
            initMap(lat, long, path)
        })

        function createShootRequest() {
            getShootModal($("#agent_id").val(), $("#id").val());
        }
        $(document).on("click", "#exportBtn", function() {
            $("#exportForm").removeClass("was-validated");
            $("#exportModal").modal("show")
        })


        let previousStatustwo = '';
        $('#publish_status').on('select2:selecting', function(evt) {
            previousStatustwo = $('#publish_status').val();
        });
        $('#publish_status').on('select2:select', function(evt) {
            if ($('#publish_status').val() == 'UNPUBLISHED') {
                $('#unpublishedRequest').modal("show")
            }
        });


        // $(document).on("change", "#publish_status", function() {
        //     if (mode == 'edit') {
        //         if ($(this).val() == 'UNPUBLISHED') {
        //             $('#unpublishedRequest').modal("show")
        //         }
        //     }
        // })

        $('#preFillOption').select2({
            placeholder: 'Select any reason',
            allowClear: true,
            closeOnSelect: true,
        });


        $(document).on("change", "#preFillOption", function() {
            var option = $(this).find('option:selected').val();
            if (option == 'Other') {
                $('#unpublish_note').val('')
                $('#unpublish_note').removeClass('d-none');
                $('#unpublish_note').focus();
            } else {
                $('#unpublish_note').val(option)
                $('#unpublish_note').addClass('d-none');
            }
        });


        $(document).on("click", "#saveUnpublished", function() {
            $('#screen_loader').removeClass('d-none');

            var clear_permit_number = 0;
            if ($('#clear_permit_number').is(':checked')) {
                clear_permit_number = 1;
            }


            let unpublish_note = $('#unpublish_note').val();
            if (unpublish_note == "") {
                showToastify("Please Add Unpublish note.", "error")
                return;
            } else {
                let property_id = $("#id").val();
                if (property_id) {
                    $.ajax({
                        url: "{{ route('admin.listing.properties.unpublish.request.admin') }}",
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            property_id: property_id,
                            unpublish_note: unpublish_note,
                            clear_permit_number: clear_permit_number
                        },
                        success: function(result) {
                            $('#screen_loader').addClass('d-none');
                            if (result.status == '1') {
                                $('#unpublish_note').val('');
                                $('#preFillOption').val('').trigger('change');
                                showToastify(result.msg, "success")
                                $('#unpublishedRequest').modal("hide")
                                viewProperty(property_id);
                            } else {
                                showToastify(result.msg, "error")
                                $('#screen_loader').addClass('d-none');
                            }

                        },
                        error: function(jqXhr, json, errorThrown) {
                            $('#screen_loader').addClass('d-none');

                            let data = jqXhr.responseJSON;
                            if (data.errors) {
                                $.each(data.errors, function(index, item) {
                                    showToastify(item[0], "error")
                                })
                            }
                            if (jqXhr.status == 500 || jqXhr.status == 400) {
                                showToastify(data.message, "error")
                            }
                            // $('#screen_loader').addClass('d-none');

                        },
                    });
                }

                // Ajax Form goes here
            }


        })


        $(document).on("change", "#select-all-export", function() {
            $(`.export-columns`).prop("checked", this.checked).trigger("change");
        })

        function decodedFilterData(filtersData) {
            return {
                agents_operator: filtersData?.search_agents_operator,
                agents: filtersData?.search_agents,
                refer_operator: filtersData?.search_refered_by_operator,
                refers: filtersData?.search_refered_by,
                managers_operator: filtersData?.search_managers_operator,
                managers: filtersData?.search_managers,
                owners: filtersData?.search_owners,
                owners_operator: filtersData?.search_owners_operator,
                categories: filtersData?.search_categories,
                categories_operator: filtersData?.search_categories_operator,
                beds: filtersData?.search_beds,
                beds_operator: filtersData?.search_beds_operator,
                baths: filtersData?.search_baths,
                baths_operator: filtersData?.search_baths_operator,
                type: filtersData?.search_type,
                type_operator: filtersData?.search_type_operator,
                telesales: filtersData?.filter_telesales,
                telesales_operator: filtersData?.filter_telesales_operator,
                unit_no: filtersData?.search_unit_no,
                unit_no_operator: filtersData?.search_unit_no_operator,
                communities: filtersData?.search_communities,
                communities_operator: filtersData?.search_communities_operator,
                locations: filtersData?.search_locations,
                locations_operator: filtersData?.search_locations_operator,
                sub_communities: filtersData?.sub_communities,

                search_statuses: filtersData?.search_statuses,
                publish_statuses_operator: filtersData?.search_statuses_operator,

                property_request_statuses: filtersData?.search_property_request_statuses,
                property_request_statuses_operator: filtersData?.search_property_request_statuses_operator,
                updated_by: filtersData?.search_updated_by,
                updated_by_operator: filtersData?.search_updated_by_operator,
                for: filtersData?.search_for,
                for_operator: filtersData?.search_for_operator,
                rera_permit_number: filtersData?.search_permit_no,
                rera_permit_number_operator: filtersData?.search_permit_no_operator,
                all_listings: all_listing_val,
                properties: filtersData?.search_properties,
                price_from: filtersData?.search_price_from,
                price_to: filtersData?.search_price_to,
                // publish_statuses: filtersData?.search_publish_statuses,
                // publish_statuses_operator: filtersData?.search_publish_statuses_operator,
                state: filtersData?.search_state,
                state_operator: filtersData?.search_state_operator,
                portals: filtersData?.search_portals,
                portals_operator: filtersData?.search_portals_operator,
                completion_status: filtersData?.search_completion_status,
                completion_status_operator: filtersData?.search_completion_status_operator,
                created_from: filtersData?.search_created_from,
                created_from_operator: filtersData?.search_created_from_operator,
                updated_at: filtersData?.search_updated_at,
                updated_at_operator: filtersData?.search_updated_at_operator,
                published_date: filtersData?.search_published_date,
                published_date_operator: filtersData?.search_published_date_operator,
                rented_until: filtersData?.search_rented_until,
                rented_until_operator: filtersData?.search_rented_until_operator,
                rera_start: filtersData?.search_rera_start,
                rera_start_operator: filtersData?.search_rera_start_operator,
                rera_end: filtersData?.search_rera_end,
                rera_end_operator: filtersData?.search_rera_end_operator,
                next_available_from: filtersData?.search_next_available_from,
                next_available_from_operator: filtersData?.search_next_available_from_operator,
                search_contract_dates: filtersData?.search_contract_dates,
                search_contract_dates_operator: filtersData?.search_contract_dates_operator,
                contract_search_type: filtersData?.contract_search_type,
                hot: filtersData?.search_hot,
                refered: filtersData?.search_refered,
                deleted: filtersData?.search_deleted,
                need_to_archive: filtersData?.search_need_to_archive,
                exclusive: filtersData?.search_exclusive,
                republish: filtersData?.search_republish_requested,
                search_without_contract: filtersData?.search_without_contract,
                search_permit: filtersData?.search_permit,
                is_live: filtersData?.search_is_live,
            };
        }

        $(document).on("submit", "#exportForm", function(event) {
            event.preventDefault();
            let isSelected = $(".export-columns:checked").length > 0;
            if (!isSelected) {
                showToastify("Please select at least one column.", "error");
                return;
            }
            let exportRoute = "{{ route('admin.listing.properties.export-excel') }}";
            let columns = $(this).serialize();
            let filtersDataDecoded = decodedFilterData(filtersData);
            filtersDataDecoded = objectToQueryString(filtersDataDecoded)
            window.open(`${exportRoute}?${columns}&${filtersDataDecoded}`);

            $("#exportForm").trigger("reset");
            $("#exportModal").modal("hide");
            return;
        })

        $(document).on("click", "#add-more-documents-btn", function() {
            let length = $(".other_documents").length;
            $("#more-documents").append(`<div class="col-lg-4 mt-2 other_documents-${length + 1}">
                                            <input type="text" id="other_documents-${length + 1}-title" name="other_documents[${length + 1}][title]"
                                                   class="form-control inputs" placeholder="Enter document title s" required>
                                            <input type="hidden" name="other_documents_old_id[${length + 1}][old_id]" value="">
                                        </div>
                                        <div class="col-lg-8 mt-2 other_documents-${length + 1}">
                                            <input type="file" id="other_documents-${length + 1}-file" name="other_documents[${length + 1}][file]"
                                                   class="form-control inputs other_documents" required accept="application/pdf,image/png, image/jpeg">

                                            <a href="javascript:void(0)" class="remove-other-documents" data-docid="other_documents-${length + 1}">Remove</a>

                                            <button type="button" class="extra_docs btn btn-outline-success d-none mt-1 btn-sm waves-effect waves-light" data-name="other_documents-${length + 1}-title" data-id="other_documents-password-${length + 1}-file"><i class="mdi-eye-arrow-left-outline mdi" ></i></button>

                                        </div>

                                        <div class="col-lg-4 d-none mt-2 other_documents-${length + 1}">
                                            <label for="password" class="form-label">Password</label>
                                        </div>

                                        <div class="other_documents-${length + 1} d-none">
                                            <input type="hidden"  id="other_documents-password-${length + 1}-file" name="other_documents[${length + 1}][password]"
                                                   class="form-control inputs other_documents ">
                                        </div>
                                        `)
        })
        $(document).on("click", ".remove-other-documents", function() {
            let id = $(this).data("docid");
            $(`.${id}`).remove()
        })
        var all_listing_val = false;

        $(document).ready(function() {
            // this function opens the

            let isNewTab = false;
            setTimeout(function() {

                var open_ref_id = "{{ Request('ref_number') }}";
                if (open_ref_id) {
                    isNewTab = true;
                    $('.dataTables_filter .form-control').val(open_ref_id).trigger('keyup');
                }

            }, 500);


            setTimeout(function() {

                if (isNewTab) {
                    $('#dataTable tbody tr:eq(0)').click();
                }

            }, 1000);

            $('#owner_id').change(function() {
                let id = $(this).val();
                if (id) {

                    let name = $(this).find('option:selected').text();
                    let name1 = name.substring(0, name.indexOf("<"));
                    let link = "{{ route('admin.work.contacts.index') }}?contact=" + name;
                    let html = `<a href="${link}" target="_blank" title="View Owner" id="viewOwner"
                                   class=" waves-effect waves-light"> View Owner</a>`;

                    $('#owners_detail_link').html(html);
                } else {
                    $('#owners_detail_link').html('');
                }

            });


            setTimeout(function() {
                $('@if (!auth()->user()->hasRole(['Admin', 'Super Admin', 'Head of Secondary', 'CEO']))<div class="btn-group all_listings_dropdown" role="group" aria-label="Basic radio toggle button group"><input type="radio" class="btn-check all_listing_btn" value="my_listings" name="btnradio" id="btnradio1" checked=""><label class="btn btn-sm btn-outline-secondary mb-0" for="btnradio1">My Listings</label><input type="radio" class="btn-check all_listing_btn" name="btnradio" id="btnradio2" value="all_listings"><label class="btn btn-sm btn-outline-secondary mb-0" for="btnradio2">All Listings</label></div>@endif')
                    .insertBefore('.dt-buttons');

            }, 800);

            $(document).on("click", ".all_listing_btn", function() {
                // $(this).toggleClass("active");
                if ($(this).val() == "all_listings") {
                    //filtersData.all_listings = true;
                    console.log('all_listings')
                    all_listing_val = true
                    $("#search_unit_no").val('');
                    $("#search_unit_no").prop("disabled", true);
                    $('#duplication_reason option[value="Relist (Older than 3 months)"]').prop('disabled',
                        true);


                } else {
                    console.log('not all_listings')

                    //filtersData.all_listings = false;
                    all_listing_val = false
                    $("#search_unit_no").val('');
                    $("#search_unit_no").prop("disabled", false);
                    $('#duplication_reason option[value="Relist (Older than 3 months)"]').prop('disabled',
                        false);

                }
                dataTable.ajax.reload();
            });

            $(document).on("click", "#all_listing", function() {
                // $(this).toggleClass("active");

                if ($(this).hasClass("active")) {
                    var val = 1;

                } else {
                    var val = 0;

                }

                $.ajax({
                    url: "{{ route('admin.listing.getAgents') }}",
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        val: val
                    },
                    success: function(data) {

                        if (data) {
                            var html = '';
                            $.each(data.agents, function(key, value) {
                                html += '<option value="' + value.id + '">' + value
                                    .name + '</option>';
                            });
                            $('#search_agents').html(html);
                            $('#search_agents').trigger('change');
                        } else {
                            $('#search_agents').html(
                                '<option value="">No Agent Found</option>');

                        }
                    }

                });

            });


        })



        function activityChangeDataFormat(data) {
            let html = '';
            for (const [key, value] of Object.entries(data)) {

                if (key == 'created_at' || key == 'updated_at') {
                    html += key + ' - ' + moment(value).format("DD/MM/YY HH:mm") + '<br>';

                }
                if (key == 'path' || key == 'Path') {
                    html += 'Image' + ' - ' + '<a href="' + value + '" target="_blank">View</a>' + '<br>'

                }
                if (key == 'Temp Image' || key == 'temp_image') {
                    html += ''

                } else {
                    html += key + ' - ' + (value != null ? value : '') + '<br>';

                }
            }
            return html;
        }
        $(document).on('click', ".show-hiddenLog", function() {
            let eleid = $(this).data('id');
            console.log(eleid);
            if ($("#hiddenLog" + eleid).hasClass('d-none')) {
                $("#hiddenLog" + eleid).removeClass('d-none');
            } else {
                $("#hiddenLog" + eleid).addClass('d-none');
            }

        });

        function getActivityLog(id) {
            $('#no_activity').addClass('d-none');
            $('#activity-log-div2').html('');
            $('#activity-log-div_Placeholder').removeClass('d-none');
            $("#activity-log-div").removeClass("d-none")
            $("#activity-log-div").find(".user-activity-timeline").html("");
            fullyLoaded = true;
            $.ajax({
                url: "{{ route('admin.listing.properties.activity.log') }}",
                type: 'GET',
                dataType: 'html',
                data: {
                    subject_model: "Property",
                    subject_id: id
                },
                success: function(result) {
                    $('#activity-log-div_Placeholder').addClass('d-none');
                    $('#activity-log-div2').html('');
                    $('#activity-log-div2').html(result);
                    let element = document.getElementById("activity-log-div2");
                    element.scrollTop = element.scrollHeight
                    fullyLoaded = true;
                },
                error: function(result) {
                    console.log(result);
                }
            });
        }
        // calendar events related code block
        function addEvent(type) {
            eventFormMode = "create";
            let agent = isSelect2Initialized($("#agent_id")) ? $("#agent_id").select2("data")[0] :
                null; //$("#agent_id").select2("data")[0];
            let contact = $("#owner_id").select2("data")[0];
            let location = $("#location_id").select2("data")[0];
            let data = {
                date: moment().format("YYYY-MM-DD HH:mm:ss"),
                type: type,
                contact: {
                    id: contact?.id || "",
                    name: contact?.text || "",
                    email: "",
                    phone: "",
                },
                user: {
                    id: $("#agent_id").val(),
                    name: agent?.text || ""
                },
                property: {
                    id: $("#id").val(),
                    title: '(' + $("#ref_no").html() + ') ' + $("#title").val(),
                    ref_no: $("#ref_no").html(),
                    location: location?.text || "",
                },
            };
            getEventModal(data);
        }
        // calendar events related code block end
        $(document).on("click", "#bulk-assign-button", function() {
            @can('Properties Assign Agents')
                $("#assignAgentsForm").removeClass("was-validated")
                $("#assignAgentsForm").trigger("reset");
                let agents = [];
                dataObj?.agents.map(item => {
                    agents.push(item.id);
                })
                $("#assign_agents").val(agents).trigger("change")
                $("#assignAgentsModal").modal("show");
            @endcan
        })
        $(document).on("click", "#saveRePublishBtn", function() {
            $('#republishRequestModal').modal('show');
            $("#portal-data-div-agent").html('');
            $('#pre-data-spinner').removeClass('d-none');
            event.preventDefault();
            var id = $("#id").val();
            $.ajax({
                url: "{{ route('ajax.fetchPortalMetaDataAgent') }}",
                type: 'POST',
                dataType: 'html',
                data: {
                    id: id,
                },
                success: function(result) {
                    $('#pre-data-spinner').addClass('d-none');
                    $('#portal-data-div-agent').html(result);

                },
                error: function(jqXhr, json, errorThrown) {
                    let data = jqXhr.responseJSON;
                    if (data.errors) {
                        $.each(data.errors, function(index, item) {
                            showToastify(item[0], "error")
                        })
                    }
                    if (jqXhr.status == 500 || jqXhr.status == 400) {
                        showToastify(data.message, "error")
                    }
                    $('#pre-data-spinner').addClass('d-none');

                },
            });




        })

        $(document).on("change", "#contract_expiry_date", function() {
            var contractStartDate = $("#contract_issue_date").val();
            var contractExpiryDate = $(this).val();
            if (contractStartDate && contractExpiryDate) {
                var startDate = new Date(contractStartDate);
                var expiryDate = new Date(contractExpiryDate);
                var monthDifference = (expiryDate.getFullYear() - startDate.getFullYear()) * 12;
                monthDifference += expiryDate.getMonth() - startDate.getMonth();

                if (monthDifference > 6) {
                    $('#date-validations').text(
                        'The difference between contract start date and expiry date should not exceed 6 months.'
                    );
                    $('#saveRepublishRequest').attr('disabled', true)
                } else {
                    $('#date-validations').text('')
                    $('#saveRepublishRequest').attr('disabled', false)
                }
            }
        });



        $(document).on("click", "#showRepublishRequests", function() {
            $('#showRepublishRequests').addClass('d-none');
            $('#showRepublishRequests-placeholder').removeClass('d-none');
            console.log('Will show republish requests');
            var id = $("#id").val();
            $.ajax({
                url: "{{ route('ajax.getRepublishRequestsData') }}",
                type: 'GET',
                dataType: 'html',
                data: {
                    id: id
                },
                success: function(result) {
                    $('#appendRequest').html(result);
                    $('#republishRequestModalApproval').modal('show');
                    $('#request_status').select2();
                    $('#showRepublishRequests').removeClass('d-none');
                    $('#showRepublishRequests-placeholder').addClass('d-none');
                },
                error: function(jqXhr, json, errorThrown) {
                    let data = jqXhr.responseJSON;
                    if (data.errors) {
                        $.each(data.errors, function(index, item) {
                            showToastify(item[0], "error")
                        })
                    }
                    if (jqXhr.status == 500 || jqXhr.status == 400) {
                        showToastify(data.message, "error")
                    }
                    $('#showRepublishRequests').removeClass('d-none');
                    $('#showRepublishRequests-placeholder').addClass('d-none');
                },
            });
        })



        $(document).on("click", ".border-top-dashed", function(event) {
            event.preventDefault();
            if ($(this).hasClass('show')) {
                $(this).removeClass('show');
            } else {
                $(this).addClass('show');
            }
        });

        $(document).on("click", ".fetch-portal-updated-data", function(event) {
            event.preventDefault();
            var id = $("#id").val();
            var portal_id = $(this).data('portal');
            $.ajax({
                url: "{{ route('ajax.updatePortalMeta') }}",
                type: 'POST',
                dataType: 'html',
                data: {
                    id: id,
                    portal_id: portal_id
                },
                success: function(result) {
                    if (portal_id == 'Property Finder') {
                        $('#pf-meta-data-div').html(result);
                    }
                    if (portal_id == 'Bayut') {
                        $('#bayut-meta-data-div').html(result);
                    }
                    if (portal_id == 'Dubizzle') {
                        $('#dubizzle-meta-data-div').html(result);
                    }

                    $('#showRepublishRequests').removeClass('d-none');
                    $('#showRepublishRequests-placeholder').addClass('d-none');
                },
                error: function(jqXhr, json, errorThrown) {
                    let data = jqXhr.responseJSON;
                    if (data.errors) {
                        $.each(data.errors, function(index, item) {
                            showToastify(item[0], "error")
                        })
                    }
                    if (jqXhr.status == 500 || jqXhr.status == 400) {
                        showToastify(data.message, "error")
                    }
                    $('#showRepublishRequests').removeClass('d-none');
                    $('#showRepublishRequests-placeholder').addClass('d-none');
                },
            });
        });

        $(document).on("click", ".fetch-all-portal-updated-data", function(event) {
            $('#loader-div').removeClass('d-none')
            $('#portal-data-div').addClass('blur-content');
            $('.fetch-all-portal-updated-data').addClass('d-none');
            $('#fetch-all-portal-updated-data-placeholder').removeClass('d-none');
            event.preventDefault();
            var id = $("#id").val();
            $.ajax({
                url: "{{ route('ajax.fetchPortalMetaData') }}",
                type: 'POST',
                dataType: 'html',
                data: {
                    id: id,
                },
                success: function(result) {
                    $('#loader-div').addClass('d-none')
                    $('#portal-data-div').removeClass('blur-content');
                    $('#portal-data-div').html(result);
                    $('.fetch-all-portal-updated-data').removeClass('d-none');
                    $('#fetch-all-portal-updated-data-placeholder').addClass('d-none');
                },
                error: function(jqXhr, json, errorThrown) {
                    let data = jqXhr.responseJSON;
                    if (data.errors) {
                        $.each(data.errors, function(index, item) {
                            showToastify(item[0], "error")
                        })
                    }
                    if (jqXhr.status == 500 || jqXhr.status == 400) {
                        showToastify(data.message, "error")
                    }
                    $('#loader-div').addClass('d-none')
                    $('#portal-data-div').removeClass('blur-content');
                    $('.fetch-all-portal-updated-data').removeClass('d-none');
                    $('#fetch-all-portal-updated-data-placeholder').addClass('d-none');
                },
            });
        });





        $(document).on("submit", "#republishRequestFormSubmission", function(event) {
            event.preventDefault();
            $('#saveRepublishRequestSubmission').addClass('d-none');
            $('#saveRepublishRequestSubmission-placeholder').removeClass('d-none');
            let id = $("#id").val();
            var formData = new FormData($(this)[0]);
            formData.append("id", id);

            $.ajax({
                url: "{{ route('ajax.republishRequestResponse') }}",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function(result) {
                    $('#republishRequestModalApproval').modal('hide');
                    showToastify(result.msg, "success")
                    getProperty(result.id);
                    $('#saveRepublishRequestSubmission').removeClass('d-none');
                    $('#saveRepublishRequestSubmission-placeholder').addClass('d-none');
                    getRepublishRequests();
                },
                error: function(jqXhr, json, errorThrown) {
                    let data = jqXhr.responseJSON;
                    if (data.errors) {
                        $.each(data.errors, function(index, item) {
                            showToastify(item[0], "error")
                        })
                    }
                    if (jqXhr.status == 500 || jqXhr.status == 400) {
                        showToastify(data.message, "error")
                    }
                    $('#saveRepublishRequestSubmission').removeClass('d-none');
                    $('#saveRepublishRequestSubmission-placeholder').addClass('d-none');
                    getRepublishRequests();
                },
            });


        })


        function getRepublishRequests() {
            var id = $("#id").val();
            $.ajax({
                url: "{{ route('ajax.getRepublishRequests') }}",
                type: 'GET',
                dataType: 'json',
                data: {
                    id: id
                },
                success: function(result) {
                    if (result.status == '1') {
                        $('#showRepublishRequests').removeClass('d-none');
                        $('#saveRePublishBtn').addClass('d-none');
                    } else {
                        $('#showRepublishRequests').addClass('d-none');
                        $('#saveRePublishBtn').removeClass('d-none');
                    }
                },
                error: function(jqXhr, json, errorThrown) {
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
        }

        $(document).on("submit", "#fetchGoogleDrivePhotos", function(event) {

            event.preventDefault();
            $('#fetched-images-div').html('');
            $('#fetch-image-btn').addClass('d-none');
            $('#fetch-image-btn-placeholder').removeClass('d-none');

            let id = $("#id").val();
            var formData = new FormData($(this)[0]);
            formData.append("id", id);

            $.ajax({
                url: "{{ route('admin.google.fetch.images') }}",
                type: 'POST',
                dataType: 'html',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function(result) {
                    $('#fetched-images-div').html(result);
                    showToastify('Images Fetched', "success")
                    $('#fetch-image-btn').removeClass('d-none');
                    $('#fetch-image-btn-placeholder').addClass('d-none');
                    if (result.status == '0') {
                        $('#fetched-images-div').html('No images found');
                        $('#fetch-image-btn').addClass('d-none');
                        $('#fetch-image-btn-placeholder').removeClass('d-none');
                    }
                },
                error: function(jqXhr, json, errorThrown) {
                    let data = jqXhr.responseJSON;
                    if (data.errors) {
                        $.each(data.errors, function(index, item) {
                            showToastify(item[0], "error")
                        })
                    }
                    if (jqXhr.status == 500 || jqXhr.status == 400) {
                        showToastify(data.message, "error")
                    }
                    $('#fetch-image-btn').removeClass('d-none');
                    $('#fetch-image-btn-placeholder').addClass('d-none');

                },
            });


        })


        $(document).on("click", "#attach-images-from-drive_old", function(event) {
            event.preventDefault();
            $('#attach-images-from-drive').addClass('d-none');
            $('#attach-images-from-drive-placeholder').removeClass('d-none');

            var images = [];
            $("input[name='gallery_image[]']:checked").each(function() {
                images.push($(this).val());
            });
            if (images.length == 0) {
                showToastify('Please select images to attach', "error")
                return;
            }
            var id = $("#id").val();
            $.ajax({
                url: "{{ route('admin.google.attach.images') }}",
                type: 'POST',
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id,
                    images: images,
                },
                success: function(result) {
                    if (result.status == '1') {
                        showToastify('Images attached', "success")
                        getPhotos();
                        $('#attach-images-from-drive').removeClass('d-none');
                        $('#attach-images-from-drive-placeholder').addClass('d-none');
                        $('#uploadPhotosModal').modal('hide');
                        $('#fetched-images-div').html('');
                        $('#folder_id').val('');
                    }
                },
                progress: function(event) {
                    console.log(event);
                    if (event.lengthComputable) {
                        var progress = (event.loaded / event.total) * 100;
                        console.log(progress);
                    }
                },
                error: function(jqXhr, json, errorThrown) {
                    let data = jqXhr.responseJSON;
                    if (data.errors) {
                        $.each(data.errors, function(index, item) {
                            showToastify(item[0], "error")
                        })
                    }
                    if (jqXhr.status == 500 || jqXhr.status == 400) {
                        showToastify(data.message, "error")
                    }
                    $('#attach-images-from-drive').removeClass('d-none');
                    $('#attach-images-from-drive-placeholder').addClass('d-none');


                },
            });
        });
        $(document).on("click", "#attach-images-from-drive_old_v2", function(event) {
            event.preventDefault();
            $('#attach-images-from-drive').addClass('d-none');
            $('#attach-images-from-drive-placeholder').removeClass('d-none');

            var images = [];
            $("input[name='gallery_image[]']:checked").each(function() {
                images.push($(this).val());
            });
            if (images.length == 0) {
                showToastify('Please select images to attach', "error");
                return;
            }

            var id = $("#id").val();

            // Track progress manually
            var processedCount = 0;
            var totalCount = images.length;

            // Recursive function to attach images one by one
            function attachNextImage(index) {
                if (index >= totalCount) {
                    // All images processed
                    showToastify('Images attached', "success");
                    getPhotos();
                    $('#attach-images-from-drive').removeClass('d-none');
                    $('#attach-images-from-drive-placeholder').addClass('d-none');
                    $('#uploadPhotosModal').modal('hide');
                    $('#fetched-images-div').html('');
                    $('#folder_id').val('');
                    return;
                }

                var imageId = images[index];
                $.ajax({
                    url: "{{ route('admin.google.attach.images') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: id,
                        images: [imageId], // Send one image at a time
                    },
                    success: function(result) {
                        if (result.status == '1') {
                            processedCount++;
                            // Calculate progress
                            var progress = (processedCount / totalCount) * 100;
                            // Update your progress bar here using 'progress'

                            // Attach next image
                            attachNextImage(index + 1);
                        }
                    },
                    error: function(jqXhr, json, errorThrown) {
                        let data = jqXhr.responseJSON;
                        if (data.errors) {
                            $.each(data.errors, function(index, item) {
                                showToastify(item[0], "error");
                            });
                        }
                        if (jqXhr.status == 500 || jqXhr.status == 400) {
                            showToastify(data.message, "error");
                        }
                        $('#attach-images-from-drive').removeClass('d-none');
                        $('#attach-images-from-drive-placeholder').addClass('d-none');
                    },
                });
            }

            // Start attaching images
            attachNextImage(0);
        });

        $(document).on("click", "#attach-images-from-drive", function(event) {
            event.preventDefault();
            $('.folder-image-div').addClass('blurred-background');
            $('#attach-images-from-drive').addClass('d-none');
            $('#attach-images-from-drive-placeholder').removeClass('d-none');
            $('#image-uploader-progress-bar').css('width', '2%').attr('aria-valuenow', 2).text('2%');


            var images = [];
            $("input[name='gallery_image[]']:checked").each(function() {
                images.push($(this).val());
            });
            if (images.length == 0) {
                showToastify('Please select images to attach', "error")
                return;
            }

            var id = $("#id").val();

            // Track progress manually
            var processedCount = 0;
            var totalCount = images.length;

            // Recursive function to attach images one by one
            function attachNextImage(index) {
                if (index >= totalCount) {
                    // All images processed
                    showToastify('Images attached', "success");
                    $('#image-uploader-progress-bar').css('width', '0%').attr('aria-valuenow', 0).text('0%');
                    $('.folder-image-div').removeClass('blurred-background');
                    getPhotos();
                    $('#attach-images-from-drive').removeClass('d-none');
                    $('#attach-images-from-drive-placeholder').addClass('d-none');
                    $('#uploadPhotosModal').modal('hide');
                    $('#fetched-images-div').html('');
                    $('#folder_id').val('');
                    return;
                }

                var imageId = images[index];
                $.ajax({
                    url: "{{ route('admin.google.attach.images') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: id,
                        images: [imageId], // Send one image at a time
                    },
                    success: function(result) {
                        if (result.status == '1') {
                            processedCount++;
                            // Calculate progress
                            var progress = (processedCount / totalCount) * 100;
                            // Update progress bar
                            $('#image-uploader-progress-bar').css('width', progress + '%').attr(
                                'aria-valuenow', progress).text(progress.toFixed(2) + '%');

                            // Attach next image
                            attachNextImage(index + 1);
                        }
                    },
                    error: function(jqXhr, json, errorThrown) {
                        let data = jqXhr.responseJSON;
                        if (data.errors) {
                            $.each(data.errors, function(index, item) {
                                showToastify(item[0], "error")
                            })
                        }
                        if (jqXhr.status == 500 || jqXhr.status == 400) {
                            showToastify(data.message, "error")
                        }
                        $('#attach-images-from-drive').removeClass('d-none');
                        $('#attach-images-from-drive-placeholder').addClass('d-none');
                    },
                });
            }

            // Start attaching images
            attachNextImage(0);
        });


        $(document).on("click", "#attach-images-from-drive-select-all", function(event) {
            event.preventDefault();
            var $galleryImages = $("input[name='gallery_image[]']");
            var allChecked = true;

            $galleryImages.each(function() {
                if (!$(this).prop('checked') || $(this).prop('indeterminate')) {
                    allChecked = false;
                    return false; // exit each loop
                }
            });

            if (allChecked) {
                // If all are checked, uncheck them
                $galleryImages.prop('checked', false).prop('indeterminate', false);
            } else {
                // If some or none are checked, check them all
                $galleryImages.prop('checked', true).prop('indeterminate', false);
            }
        });


        $(document).on("change", "#request_status", function() {
            let value = $("option:selected", this).val();
            if (value == 'REPUBLISH_APPROVED') {
                $('#resquest-response-text').text('Approve');
                $('#contract_note').val('');
                $('.contract_note_div').addClass('d-none');
                $('#contract_note').attr('required', false);
            }
            if (value == 'REPUBLISH_REJECTED') {
                $('#resquest-response-text').text('Reject');
                $('#contract_note').val('');
                $('.contract_note_div').removeClass('d-none');
                $('#contract_note').attr('required', true);
            } else {
                $('#resquest-response-text').text('Update');
                $('#contract_note').val('');
                $('.contract_note_div').addClass('d-none');
                $('#contract_note').attr('required', false);

            }
        });

        $(document).on("submit", "#republishRequestForm", function(event) {
            event.preventDefault();
            $('#saveRepublishRequest').addClass('d-none');
            $('#saveRepublishRequest-placeholder').removeClass('d-none');
            let id = $("#id").val();
            var formData = new FormData($(this)[0]);
            formData.append("id", id);
            $.ajax({
                url: "{{ route('admin.listing.properties.republishRequest') }}",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function(result) {

                    $('#republishRequestModal').modal('hide');
                    showToastify('Request for republish submitted.', "success")

                    $("#republishRequestForm").trigger("reset");
                    $('#saveRepublishRequest').removeClass('d-none');
                    $('#saveRepublishRequest-placeholder').addClass('d-none');
                    getRepublishRequests();



                },
                error: function(jqXhr, json, errorThrown) {
                    $(buttons).prop("disabled", false);
                    let data = jqXhr.responseJSON;
                    if (data.errors) {
                        $.each(data.errors, function(index, item) {
                            showToastify(item[0], "error")
                        })
                    }
                    if (jqXhr.status == 500 || jqXhr.status == 400) {
                        showToastify(data.message, "error")
                    }
                    $('#saveRepublishRequest').removeClass('d-none');
                    $('#saveRepublishRequest-placeholder').addClass('d-none');

                },
            });
        })




        $(document).on("submit", "#assignAgentsForm", function(event) {
            event.preventDefault();
            let id = $("#id").val();
            var formData = new FormData($(this)[0]);
            formData.append("id", id);
            let buttons = $(this).find("button");
            $(buttons).prop("disabled", true);
            $.ajax({
                url: "{{ route('admin.listing.properties.assign-agents') }}",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function(result) {

                    $(buttons).prop("disabled", false);
                    if (result.status == '1') {
                        showToastify(result.msg, "success")
                        dataTable.row().order([0, 'DESC']).draw();
                        $("#assignAgentsModal").modal("hide");
                    } else if (result.status == '2') {
                        let error_message = '';
                        $.each(result.errors, function(key, value) {
                            error_message += '' + key + ': ' + value + ' | ';
                        });
                        showToastify(error_message, "error")
                    } else {
                        showToastify(result.msg, "error")
                    }

                },
                error: function(jqXhr, json, errorThrown) {
                    $(buttons).prop("disabled", false);
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
        })

        $(document).on("click", "#duplicate-button", function() {
            @can('Properties Duplicate')

                $("#duplicateForm").trigger("reset");
                $("#duplicate_for").val("").trigger("change")
                $("#duplicateModal").modal("show");
            @endcan
        })


        $(document).on("change", "#duplication_reason", function() {
            let value = $("option:selected", this).val();
        })
        $(document).on("change", "#property_status_id", function() {
            let value = $("option:selected", this).val();
            if (["5", "7", "10", "11"].indexOf(value) !== -1) {
                $("#rented-area").removeClass("d-none")
            } else {
                $("#rented_at, #rented_until, #next_available, #remind").val("").trigger("change")
                $("#rented-area").addClass("d-none")
            }
        })
        $(document).on("change", "#property_category_id", function() {
            let value = $("option:selected", this).val();
            if (["2"].indexOf(value) !== -1) {
                $("#villa_type_fields").removeClass("d-none")
            } else {
                $("#villa_type").val("").trigger("change")
                $("#villa_type_fields").addClass("d-none")
            }
        })
        $(document).on("click", "#publish-request-button", function(event) {
            $("#publishRequestForm").trigger("reset");
            $("#publishRequestForm").removeClass("was-validated");


            $("#publishRequestModal").modal("show");
            $("#request_consent_txt").html(
                "I hereby mention that all the details filled and images, Title Deed, Owner's Passport copy and Signed Listing Form provided are 100% genuine and I hold the responsibility for the correctness of above-mentioned particulars."
            );

            $('#form-consent').removeClass('d-none');
            $('#request_consent').prop('checked', false);

            $("#request_type").val("publish")
        })

        $(document).on("click", "#unpublish-request-button", function(event) {
            $("#publishRequestForm").trigger("reset");
            $("#publishRequestForm").removeClass("was-validated");
            $("#publishRequestModal").modal("show");
            $("#request_consent_txt").html("I hereby confirm that the listing no longer exists.");
            $('#form-consent').addClass('d-none');
            $('#request_consent').prop('checked', true);
            $("#request_type").val("unpublish")
        })



        // $(document).on("click", "#publishPocketBtn", function(event) {
        //     $("#publish_status").val('POCKET').trigger("change");
        //     validationForPocket()
        //      event.preventDefault()
        //     document.getElementById('mainForm').submit();
        // })

        // $(document).on("click", "#saveBtn", function(event) {
        //     validationForDraft();
        // })

        $("#exclusive").change(function() {

            if ($(this).val() == '1') {
                $("#exlusiveDateRow").removeClass('d-none');
            } else {
                $("#exlusiveDateRow").addClass('d-none');
            }
        });
        $("#exclusive_dates").flatpickr({
            mode: "range",
            allowInput: false,
            dateFormat: "d-m-Y",
            defaultDate: ["01-03-2023", "10-03-2023"],

        });

        $("#contract_end").flatpickr({
            allowInput: false,
            dateFormat: "d-m-Y",
        });
        $("#contract_start").flatpickr({
            allowInput: false,
            dateFormat: "d-m-Y",

        });
        $(document).on("submit", "#publishRequestForm", function(event) {
            event.preventDefault();
            // $('#screen_loader').removeClass('d-none');
            let id = $("#id").val();
            let type = $("#request_type").val()
            var formData = new FormData($(this)[0]);
            formData.append("id", id);
            let buttons = $(this).find("button");
            $(buttons).prop("disabled", true);
            $.ajax({
                url: type == "publish" ? "{{ route('admin.listing.properties.publish-request') }}" :
                    "{{ route('admin.listing.properties.unpublish-request') }}",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function(result) {

                    $(buttons).prop("disabled", false);
                    if (result.status == '1') {
                        showToastify(result.msg, "success");
                        dataTable.row().order([0, 'DESC']).draw();
                        $("#publishRequestModal").modal("hide");
                        viewProperty(id)
                    } else if (result.status == '2') {
                        let error_message = '';
                        $.each(result.errors, function(key, value) {
                            error_message += '' + key + ': ' + value + ' | ';
                        });
                        showToastify(error_message, "error")
                    } else {
                        showToastify(result.msg, "error")
                    }
                    //$('#screen_loader').addClass('d-none');

                },
                error: function(jqXhr, json, errorThrown) {
                    $(buttons).prop("disabled", false);
                    let data = jqXhr.responseJSON;
                    if (data.errors) {
                        $.each(data.errors, function(index, item) {
                            showToastify(item[0], "error")
                        })
                    }
                    if (jqXhr.status == 500 || jqXhr.status == 400) {
                        showToastify(data.message, "error")
                    }
                    // $('#screen_loader').addClass('d-none');

                },
            });
        })

        $(document).on("submit", "#duplicateForm", function(event) {
            @can('Properties Duplicate')
                $('#screen_loader').removeClass('d-none');

                event.preventDefault();
                let buttons = $(this).find("button");
                $("#copy_listing_btn").text("Please Wait");
                $(buttons).prop('disabled', true);
                var formData = new FormData($(this)[0]);
                formData.append("id", $("#id").val());
                $.ajax({
                    url: "{{ route('admin.listing.properties.duplicate') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: formData,
                    cache: false,
                    processData: false,
                    contentType: false,
                    success: function(result) {
                        $(buttons).prop('disabled', false);
                        $("#copy_listing_btn").text("Save");
                        $('#duplication_reason').val('').trigger('change');
                        $('#duplicate_for').val('').trigger('change');
                        $('#duplicate_agent_id').val('').trigger('change');

                        if (result.status == '1') {
                            showToastify(result.msg, "success")
                            $("#duplicateModal").modal("hide")
                            dataTable.row().order([0, 'DESC']).draw();
                        } else if (result.status == '2') {
                            let error_message = '';
                            $.each(result.errors, function(key, value) {
                                error_message += '' + key + ': ' + value + ' | ';
                            });
                            showToastify(error_message, "error")
                        } else {
                            showToastify(result.msg, "error")
                        }
                        $('#screen_loader').addClass('d-none');

                    },
                    error: function(jqXhr, json, errorThrown) {
                        $(buttons).prop('disabled', false);
                        $("#copy_listing_btn").text("Save");
                        let data = jqXhr.responseJSON;
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
            @endcan
        })

        $(document).on("click", ".statusFilter", function() {
            $('#search_republish_requested').attr('checked', false);

            let status = $(this).data("status");
            if ($("#append-here").has("#search_statuses").length > 0) {

                $('#search_statuses').val(status).trigger('change');
                $("#search_statuses option:selected").removeAttr("selected");
                $("#search_statuses option[value='" + status + "']").attr("selected", true);
                $("#search_statuses").trigger("change");
                $("#searchSearchBtn").trigger("click");
            } else {
                $('#applicator').val('statuses').trigger('change');
                setTimeout(function() {
                    $('#search_statuses').val(status).trigger('change');
                    $("#search_statuses option:selected").removeAttr("selected");
                    $("#search_statuses option[value='" + status + "']").attr("selected", true);
                    $("#search_statuses").trigger("change");
                    $("#searchSearchBtn").trigger("click");
                }, 1000);
            }



            // let status = $(this).data("status");
            // $("#search_publish_statuses option:selected").removeAttr("selected");
            // $("#search_publish_statuses option[value='" + status + "']").attr("selected", true);
            // $("#search_publish_statuses").trigger("change");
            // $("#search_is_live").val('').trigger("change");
            // $("#search_state").val('').trigger("change")
            // $("#searchSearchBtn").trigger("click");
        });

        $(document).on("click", "#statusFilterRepublish", function() {
            if ($('#search_republish_requested').is(':checked')) {
                $('#search_republish_requested').attr('checked', false);
            } else {
                $('#search_republish_requested').attr('checked', true);
            }
            $("#searchSearchBtn").trigger("click");
        })

        $(document).on("click", ".statusFilterClear", function() {
            $("#search_statuses option:selected").removeAttr("selected").trigger("change");
            $("#search_statuses").val('').trigger("change");
            $("#search_state").val('').trigger("change");
            $("#search_is_live").val('').trigger("change");
            $('#search_republish_requested').attr('checked', false);
            $("#searchSearchBtn").trigger("click");
        });

        $(document).on("click", ".statusFilterLive", function() {
            let is_live = true;
            $("#search_statuses").val('').trigger("change");
            $("#search_is_live").val(is_live).trigger("change")
            $("#searchSearchBtn").trigger("click");
        });

        $(document).on("click", "#saveAndSearch", function() {
            $('#saveAndSearchModal').modal('show');
        });
        $(document).on("click", "#saveAndSearchBtn", function() {
            let input_name = $('#search_name_input').val();
            $('#save_filter_name').val(input_name);
            $('#saveAndSearchModal').modal('hide');
            $('#search_name_input').val('');
            $('#searchSearchBtn').trigger('click');
        });
        $(document).on("click", "#saveAndSearchCloseBtn", function() {
            $('#search_name_input').val('');
            $('#save_filter_name').val('');
            $('#saveAndSearchModal').modal('hide');
        });





        $(document).on("click", "#saveArchive", function(event) {

            $('#screen_loader').removeClass('d-none');

            event.preventDefault();
            let buttons = $(this).find("button");
            $("#copy_listing_btn").text("Please Wait");
            $(buttons).prop('disabled', true);
            var id = $("#archive_lead_id").val();
            var archive_note = $("#archive_note").val();
            $.ajax({
                url: "{{ route('admin.listing.properties.archive') }}",
                type: 'POST',
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id,
                    archive_note: archive_note,
                },
                success: function(result) {
                    $('#screen_loader').addClass('d-none');

                    if (result.status == '1') {
                        showToastify(result.msg, "success");
                        dataTable.row().order([0, 'DESC']).draw();
                        $('#archiveModal').modal('hide');
                        viewProperty(id)
                    } else if (result.status == '2') {
                        $('#archiveModal').modal('hide');
                        let error_message = '';
                        $.each(result.errors, function(key, value) {
                            error_message += '' + key + ': ' + value + ' | ';
                        });
                        showToastify(error_message, "error")
                    } else {
                        showToastify(result.msg, "error")
                    }

                },
                error: function(jqXhr, json, errorThrown) {
                    $(buttons).prop("disabled", false);
                    $('#screen_loader').addClass('d-none');

                    let data = jqXhr.responseJSON;
                    if (data.errors) {
                        $.each(data.errors, function(index, item) {
                            showToastify(item[0], "error")
                        })
                    }
                    if (jqXhr.status == 500 || jqXhr.status == 400) {
                        showToastify(data.message, "error")
                    }
                    // $('#screen_loader').addClass('d-none');

                },
            });

        })

        $('#photos-gallery').sortable({
            group: 'list',
            animation: 200,
            ghostClass: 'ghost',
            cursor: 'move',
            tolerance: 'pointer',
            onSort: function(event, ui) {
                let oldIndex = event.oldIndex;
                let newIndex = event.newIndex;
                let tmp = galleryArr[oldIndex]
                galleryArr[oldIndex] = galleryArr[newIndex]
                galleryArr[newIndex] = tmp
                @can('Change Media Order')
                    reOrderPhotos()
                @endcan

            },
        });

        function reOrderPhotos() {
            let formData = new FormData();
            var items = [] // Array to contain array of ids
            // will get all children
            var getChild = $(".scrollable-gallery").children();
            // loop over them
            getChild.each(function(i, v) {
                // push in items array, an array of data-id
                items.push(($(v).data('id')))
            })
            formData.append("ids", items);
            formData.append("property_id", $("#id").val());
            $.ajax({
                url: "{{ route('admin.listing.properties.re-order-photos') }}",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function(result) {
                    if (result.status != '1') {
                        getPhotos()
                        let publishStatus = $("#publish_status").val();
                        let propertyId = $("#id").val();
                        if (publishStatus == "PUBLISHED" && mode == "view") {
                            getProperty(propertyId);
                        }
                    }
                },
                error: function(jqXhr, json, errorThrown) {
                    $("#saveBtn").prop('disabled', false);
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
        }

        $('#title').maxlength({
            threshold: 15,
            placement: 'bottom-right',
            warningClass: "valid-tooltip",
            limitReachedClass: "invalid-tooltip",
        })


        $('#description').maxlength({
            threshold: 2000,
            placement: 'bottom-right',
            warningClass: "valid-tooltip",
            limitReachedClass: "invalid-tooltip",
        })

        function setRefNo(refNo = "") {
            $("#ref_no").text(refNo)
            if (refNo == "" || refNo == undefined) {
                $("#ref_no_area").addClass("d-none")
            } else {
                $("#ref_no_area").removeClass("d-none")
            }
        }

        function formatAgent(e) {
            let img = $(e.element).data('image');
            let status = $(e.element).data('status');
            let onlineClass = "online"
            if (!status) {
                onlineClass = "away";
            }
            return e.id ? $('<div class="d-flex align-items-center"><div class="flex-shrink-0 chat-user-img ' +
                onlineClass + ' align-self-center me-2 ms-0"><div class="avatar-xxs mb-2"> <img src="' + img +
                '" class="rounded-circle img-fluid userprofile" alt=""><span class="user-status"></span> </div></div><div class="flex-grow-1 overflow-hidden"><p class="text-truncate mb-0">' +
                e.text + '</p></div></div>') : e.text;
        }



        function formatAgent2(e) {
            let img = e.image;
            let status = e.status;
            let onlineClass = "online"
            if (!status) {
                onlineClass = "away";
            }

            return e.id ? $('<div class="d-flex align-items-center"><div class="flex-shrink-0 chat-user-img ' +
                onlineClass + ' align-self-center me-2 ms-0"><div class="avatar-xxs mb-2"> <img src="' + img +
                '" class="rounded-circle img-fluid userprofile" alt=""><span class="user-status"></span> </div></div><div class="flex-grow-1 overflow-hidden"><p class="text-truncate mb-0">' +
                e.text + '</p></div></div>') : e.text;
        }

        function formatAgentView(e) {
            let img = $(e.element).data('image');
            let status = $(e.element).data('status');
            let onlineClass = "online"
            if (!status) {
                onlineClass = "away";
            }
            return (e.text ?? "No Name")
        }

        function formatOwner(e) {
            let own_mobile = $(e.element).data('mobile');

            return e.id ? $('<span><img src="https://ui-avatars.com/api/?name=' + e.text +
                '&color=7F9CF5&background=random" class="img-flag rounded-circle" height="18" /> ' + e.text +
                "</span> <span class='small ml-5 d-inline-block'> <i class='mdi mdi-phone'></i>" + own_mobile +
                "</span>") : e.text;


        }


        $(document).ready(function() {
            $("#payment_method, #for").change(function() {
                if (($("#payment_method").val() == 'installments' || $("#payment_method").val() ==
                        'cash,installments') && ($("#for").val() == "Residential for sale" || $("#for")
                        .val() == "Commercial for sale")) {
                    $("#down_payment_price_container").removeClass("d-none");
                    $("#down_payment_price").prop("required", true);

                } else {
                    $("#down_payment_price").prop("required", false);
                    $("#down_payment_price_container").addClass("d-none");
                }
            })



            $('#search_permit').select2({
                placeholder: 'Select permit type',
                allowClear: true,
            });
            if ($("#agent_id").hasClass("select2")) {
                $('#agent_id').select2({
                    placeholder: 'Select agent',
                    allowClear: true,
                    templateResult: formatAgent,
                    templateSelection: formatAgent,

                });
            }
            if ($("#refered_by").hasClass("select2")) {
                $('#refered_by').select2({
                    placeholder: 'Select agent',
                    allowClear: true,
                    templateResult: formatAgent,
                    templateSelection: formatAgent,

                });
            }

            if ($("#duplicate_agent_id").hasClass("select2")) {
                $('#duplicate_agent_id').select2({
                    placeholder: 'Select agent',
                    allowClear: true,
                    templateResult: formatAgent,
                    templateSelection: formatAgent,
                    dropdownParent: $('#duplicateModal')
                });
            }


            $('#owner_id').select2({
                placeholder: 'Select owner',
                allowClear: true,
                templateResult: formatOwner,
                templateSelection: formatOwner
            });

            $('#view').select2({
                placeholder: 'Select view',
                allowClear: false,
                closeOnSelect: false,
                multiple: true,
            });
            $('#villa_type').select2({
                placeholder: 'Select villa type',
                allowClear: false,
                closeOnSelect: false,
                multiple: true,
            });

            $('#for').select2({
                placeholder: 'Select property category',
                allowClear: true
            });
            $('#payment_method').select2({
                placeholder: 'Select payment method',
                allowClear: true
            });

            $('#property_category_id').select2({
                placeholder: 'Select property type',
                allowClear: true
            });

            $('#bed_id').select2({
                placeholder: 'Select beds',
                allowClear: true
            });

            $('#bath_id').select2({
                placeholder: 'Select baths',
                allowClear: true
            });

            // $('#location_id').select2({
            //     placeholder: 'Select location',
            //     allowClear: true
            // });

            $('#completion_status').select2({
                placeholder: 'Select Completion',
            });




            $('#furnished').select2({
                placeholder: 'Select furnishing type',
                allowClear: true
            });


            $('#developer_id').select2({
                placeholder: 'Select developer',
                allowClear: true
            });

            $('#cheques').select2({
                placeholder: 'Select cheques',
                allowClear: true
            });

            $('#property_status_id').select2({
                placeholder: 'Select property status',
                allowClear: true
            });

            if ($("#contact_agent_id").hasClass("select2")) {
                $('#contact_agent_id').select2({
                    placeholder: 'Select agent',
                    allowClear: true,
                    dropdownParent: $('#addContactModal')
                });
            }

            $('#gender').select2({
                placeholder: 'Select gender',
                allowClear: true,
                dropdownParent: $('#addContactModal')
            });

            $('#country_id').select2({
                placeholder: 'Select nationality',
                allowClear: true,
                dropdownParent: $('#addContactModal')
            });

            $('#contact_source_id').select2({
                placeholder: 'Select contact source',
                allowClear: true,
                dropdownParent: $('#addContactModal')
            });

            $('#duplicate_for').select2({
                placeholder: 'Select copy to',
                allowClear: true,
                dropdownParent: $('#duplicateModal')
            });
            $('#duplication_reason').select2({
                placeholder: 'Select Duplicate Reason',
                allowClear: true,
                dropdownParent: $('#duplicateModal')
            });

            $('#assign_agents').select2({
                placeholder: 'Select agents',
                allowClear: true,
                closeOnSelect: false,
                dropdownParent: $('#assignAgentsModal')
            });

            // $('#show_price_iframe').select2({
            //     placeholder: 'Select Option',
            //     allowClear: true,
            //     closeOnSelect: true,
            //     dropdownParent: $('#generateBrochureModal')
            // });

            $('#post_title').select2({
                placeholder: 'Select Option',
                allowClear: true,
                closeOnSelect: false,
                dropdownParent: $('#generatePost')
            });


            $('#show_profile_post').select2({
                placeholder: 'Select Option',
                allowClear: true,
                closeOnSelect: false,
                dropdownParent: $('#generatePost')
            });

            $('#show_profile_iframe').select2({
                placeholder: 'Select Option',
                allowClear: true,
                closeOnSelect: true,
                dropdownParent: $('#generateBrochureModal')
            });



            // $('#profile_user_iframe').select2({
            //     placeholder: 'Select Option',
            //     allowClear: true,
            //     closeOnSelect: true,
            //     dropdownParent: $('#generateBrochureModal')
            // });




            $('#show_price_post').select2({
                placeholder: 'Select Option',
                allowClear: true,
                closeOnSelect: false,
                dropdownParent: $('#generatePost')
            });


            $('#search_agents').select2({
                placeholder: 'Select agents',
                allowClear: true,
                closeOnSelect: false,
                templateResult: formatAgent
            });

            $('#filter_telesales').select2({
                placeholder: 'Select Telesales',
                allowClear: true,
                closeOnSelect: false,
                templateResult: formatAgent
            });
            // $('#search_price_from').select2({
            //     placeholder: 'Select Min Price',
            //     allowClear: true,
            //     closeOnSelect: true,
            //     tags: true
            // });
            // $('#search_price_to').select2({
            //     placeholder: 'Select Max Price',
            //     allowClear: true,
            //     closeOnSelect: true,
            //     tags: true
            // });

            $('#search_owners').select2({
                placeholder: 'Select owners',
                allowClear: true,
                closeOnSelect: false,
            });

            $('#search_categories').select2({
                placeholder: 'Select types',
                allowClear: true,
                closeOnSelect: false,
            });
            $('#search_furnishing').select2({
                placeholder: 'Select Furnishing',
                allowClear: true,
                closeOnSelect: false,
            });


            $('#search_beds').select2({
                placeholder: 'Select beds',
                allowClear: true,
                closeOnSelect: false,
            });

            $('#search_baths').select2({
                placeholder: 'Select baths',
                allowClear: true,
                closeOnSelect: false,
            });

            $('#search_property_statuses').select2({
                placeholder: 'Select property Request statuses',
                allowClear: true,
                closeOnSelect: false,
            });

            $('#search_property_request_statuses').select2({
                placeholder: 'Select property statuses',
                allowClear: true,
                closeOnSelect: false,
            });

            $('#search_for').select2({
                placeholder: 'Select categories',
                allowClear: true,
                closeOnSelect: false,
            });

            $('#search_publish_statuses').select2({
                placeholder: 'Select publish statuses',
                allowClear: true,
                closeOnSelect: false,
            });

            $('#search_portals').select2({
                placeholder: 'Select portals',
                allowClear: true,
                closeOnSelect: false,
            });



            $('#search_completion_status').select2({
                placeholder: 'Select Completion Status',
                allowClear: true,
                closeOnSelect: false,
            });
            $('#search_state').select2({
                placeholder: 'Select State',
                allowClear: true,
                closeOnSelect: false,
            });
            $('#search_communities').select2({
                placeholder: 'Select communities',
                allowClear: true,
                closeOnSelect: false,
            });

            $('#search_regions').select2({
                placeholder: 'Select Regions',
                allowClear: true,
                closeOnSelect: false,
            });

            $("#completion_status").change(function() {
                if ($(this).val() == 'Off-Plan Primary' || $(this).val() == 'Off-Plan Secondary') {
                    $("#developerDiv").removeClass('d-none');
                } else {
                    $("#developerDiv").addClass('d-none');
                }
            })

            $(".operator-select").change(function() {
                let op_val = $(this).val();
                let op_id = $(this).attr('id');
                op_id = op_id.replace('_operator', '');

                if (op_val == "whereEmpty" || op_val == "whereNotEmpty") {
                    $("#" + op_id + "").val('').trigger('change');
                }


                //console.log(op_val);
                //console.log(op_id);

            })

            // $('#search_communities').select2({
            //     placeholder: 'Select Communities',
            //     allowClear: true,
            //     closeOnSelect: false,
            //     ajax: {
            //         url: "{{ route('ajax.community.region.select2') }}",
            //         dataType: 'json',
            //         type: 'GET',
            //         data: function(term, page) {
            //             return {
            //                 q: term,
            //                 regions: $("#search_regions").val(),
            //                 page_limit: 10
            //             };
            //         },
            //         processResults: function(data) {
            //             return {
            //                 results: $.map(data, function(item) {
            //                     return {
            //                         text: `${item.name}`,
            //                         id: item.id,
            //                         'lat': item.lat,
            //                         'long': item.long,
            //                         'path': item.path,
            //                         'alternate_path_name': item.alternate_path_name,
            //                         'type': item.type
            //                     }
            //                 })

            //             };
            //         },

            //     }
            // });


            // $('#serch_regions').change(function() {
            //     $.ajax({
            //         url: "{{ route('admin.work.contacts.assign-agents') }}",
            //         type: 'POST',
            //         dataType: 'json',
            //         data: {
            //             _token: "{{ csrf_token() }}",
            //             region_id: $(this).val(),
            //         },
            //         success: function(result) {
            //             // re initialize select 2 of select_communities

            //         }
            //     })
            // })

            $('#search_communities').select2({
                placeholder: 'Select Communities',
                allowClear: true,
                closeOnSelect: false,
                ajax: {
                    url: "{{ route('ajax.community.region.select2') }}",
                    dataType: 'json',
                    type: 'GET',
                    data: function(term, page) {
                        return {
                            q: term,
                            regions: $("#search_regions").val(),
                            page_limit: 10
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    text: `${item.name}`,
                                    id: item.id,
                                    'lat': item.lat,
                                    'long': item.long,
                                    'path': item.path,
                                    'alternate_path_name': item.alternate_path_name,
                                    'type': item.type
                                }
                            })

                        };
                    },

                }
            });



            $('#search_locations').select2({
                placeholder: 'Select Locations',
                allowClear: true,
                closeOnSelect: false,
                ajax: {
                    url: '{{ route('ajax.sub-community.property.select2') }}',
                    dataType: 'json',
                    type: 'GET',
                    data: function(term, page) {
                        return {
                            q: term, // search term
                            communities: $("#search_communities").val(),
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
                                    'alternate_path_name': item.alternate_path_name,
                                    'type': item.type
                                }
                            })
                        }
                    }
                }
            });
            let s_sub_communities = [];
            let s_properties = [];
            $('#search_locations').on('select2:select', function(e) {
                $("#search_sub_communities").val("")
                $("#search_properties").val("")
                let data = e.params.data;
                let lat = data.lat;
                let long = data.long;
                let path = data.path;
                let alternate_path_name = data.alternate_path_name;
                let type = data.type;
                if (data.type == 'sub_community') {
                    s_sub_communities.push(data.id);
                } else {
                    s_properties.push(data.id);
                }
                $("#search_sub_communities").val(s_sub_communities)
                $("#search_properties").val(s_properties)


            });
            $('#search_locations').on("select2:unselect", function(e) {
                let data = e.params.data;
                if (data.type == 'sub_community') {
                    let index = s_sub_communities.indexOf(parseInt(data.id));
                    if (index > -1) { // only splice array when item is found
                        s_sub_communities.splice(index, 1); // 2nd parameter means remove one item only
                    }
                } else {
                    let index = s_properties.indexOf(parseInt(data.id));
                    if (index > -1) { // only splice array when item is found
                        s_properties.splice(index, 1); // 2nd parameter means remove one item only
                    }
                }
                $("#search_sub_communities").val(s_sub_communities)
                $("#search_properties").val(s_properties)

            });

        });


        $(document).on("click", "#addContact", function() {
            @can('Contacts Create')
                $("#addContactForm select").val("").trigger("change")
                $("#addContactForm").trigger("reset")
                $("#addContactForm").removeClass("was-validated")
                let agent_id = $("#agent_id").val();
                $("#contact_agent_id").val(agent_id).trigger("change");
                $("#contact_type_id").val(3).trigger("change");
                $("#addContactModal").modal({
                    backdrop: 'static',
                    keyboard: false
                })
                $("#addContactModal").modal("show")
            @endcan
        })

        $(document).on("click", "#uploadPhotos", function() {
            $('#watermark').prop('checked', true);
            $('div.dz-complete').remove();
            $("#uploadPhotosModal").modal("show")
            $('#folder_id').val('');
            $('#fetched-images-div').html('');
        })

        let dataObj = {};
        let filtersData = [];

        let mobileInput = initPhoneValidator('mobile', 'full_mobile')
        var input = document.querySelector("#mobile");
        var iti = window.intlTelInput(input, {
            initialCountry: "ae",
        });

        input.addEventListener("input", function() {
            var selectedCountryData = iti.getSelectedCountryData();
            var countryCode = selectedCountryData.dialCode;
            $('input[name="mobile_countrycode"]').val(countryCode);
        });

        input.addEventListener("countrychange", function() {
            var selectedCountryData = iti.getSelectedCountryData();
            var countryCode = selectedCountryData.dialCode;
            $('input[name="mobile_countrycode"]').val(countryCode);
        });

        $('#mobile').on('input change', function() {
            var isValid = iti.isValidNumber();
            var fullMobile = isValid ? iti.getNumber() : iti.getNumber();

            $('input[name="full_mobile"]').val(fullMobile);
            $('input[name="mobile"]').val(fullMobile);
        });



        let phoneInput = initPhoneValidator("phone", "full_phone")

        var input2 = document.querySelector("#phone");
        var iti2 = window.intlTelInput(input2, {
            initialCountry: "ae",
        });

        input2.addEventListener("input", function() {
            var selectedCountryData = iti2.getSelectedCountryData();
            var countryCode = selectedCountryData.dialCode;
            $('input[name="phone_countrycode"]').val(countryCode);
        });

        input2.addEventListener("countrychange", function() {
            var selectedCountryData = iti2.getSelectedCountryData();
            var countryCode = selectedCountryData.dialCode;
            $('input[name="phone_countrycode"]').val(countryCode);
        });

        $('#phone').on('input change', function() {
            var isValid = iti2.isValidNumber();
            var fullPhone = isValid ? iti2.getNumber() : iti2.getNumber();

            $('input[name="full_phone"]').val(fullPhone);
            $('input[name="phone"]').val(fullPhone);
        });



        let locations = locationSelect2("");

        function checkContactExistence(value, column, model, id = "null") {
            let agent_id = $("#contact_agent_id").val();
            $(`#${column}-exists`).html("")
            $(`#${column}`).removeClass("is-invalid")
            $(`#${column}`).next(".invalid-feedback").text(`enter a valid ${column}.`);
            // Removed agent id from below ajax to resolve a problem. If this makes issue pass the agent_id to below line data: {value, column, model, id},
            if (value.length > 0) {
                $.ajax({
                    url: "{{ route('ajax.is-value-unique') }}",
                    type: "get",
                    data: {
                        value,
                        column,
                        model,
                        id
                    },
                    success: function(result) {
                        if (!result.data) {
                            // $(`#${column}`).addClass("is-invalid")
                            // $(`#${column}`).next(".invalid-feedback").text(`The ${column} has already been taken.`)
                            // $(`#${column}-exists`).html(`This lead already exists <a href="javascript:void(0)" data-id="${result.id}" class="text-primary" id="request-access-${column}">request access</a>`)
                            // console.log(result);
                            // showToastify(`The ${column} has already been taken.`, "error")
                            existingContact = result.id;
                            existingContactDetails = result.existing_data;
                        }
                    }
                });
            }
        }
        // Commented code to remove the errors

        // $(document).on("change", "#contact_agent_id", function () {
        //     console.log("on change of contact agent id");
        //     checkContactExistence(mobileInput.getNumber(), "mobile", "Contact")
        //     checkContactExistence($("#email").val(), "email", "Contact")
        // })

        // $(document).on("change", "#email", function() {
        //     checkContactExistence($(this).val(), "email", "Contact")
        // })

        $(document).on("change", "#mobile", function() {
            $("#whatsapp").val(mobileInput.getNumber()).trigger("change");
            checkContactExistence(mobileInput.getNumber(), "mobile", "Contact")
        })

        $(document).on('change', '#open_filter', function() {
            let filter_id = $(this).val();
            $.ajax({
                url: "{{ route('admin.listing.properties.filterOpener') }}",
                type: 'POST',
                dataType: 'json',
                data: {
                    filter_id: filter_id,
                },
                success: function(result) {
                    let serializedData = JSON.stringify(result);
                    console.log(serializedData)
                    let decodedData = decodeURIComponent(serializedData);
                    console.log(decodedData)
                    filtersData = JSON.parse(decodedData);


                    dataTable.ajax.reload();
                }
            });
        })

        $(document).on("submit", "#searchForm", function(e) {
            e.preventDefault();
            filtersData = $("#searchForm").serialize();
            // filtersDataUd = filtersData
            filtersData = urldecode(filtersData);


            filtersData = QueryStringToJSON(filtersData);
            // console.table(filtersData);
            console.log(filtersData);
            dataTable.ajax.reload();
        })

        $(document).on('submit', '#addContactForm', function(event) {
            existingContact = null;
            existingContactDetails = null;
            checkContactExistence(mobileInput.getNumber(), "mobile", "Contact");
            @can('Contacts Create')
                var form = $(this);
                var formData = new FormData($(this)[0]);
                let buttons = $("#addContactForm button");
                $(buttons).prop('disabled', true);
                event.preventDefault();
                $("#mobile").val(mobileInput.getNumber())
                $("#phone").val(phoneInput.getNumber())
                let contact_agent_id = $("#contact_agent_id").val();
                @if (auth()->user()->hasRole('Agent'))
                    let id = {{ auth()->id() }};
                    $("#agent_id_for_owner").val(id);
                    contact_agent_id = id;
                @endif
                setTimeout(function() {

                    if (existingContact != null) {
                        var content = document.createElement('div');
                        content.innerHTML = "This owner already exists with name <strong>" +
                            existingContactDetails.first_name +
                            " " + existingContactDetails.last_name + "</strong>";

                        Swal.fire({

                                html: '<div class="mt-3"><div class="mt-4 pt-2 fs-15 mx-5"><h4>Contact Already Exists!</h4><p class="text-muted mx-4 mb-0">The contact named <strong>' +
                                    existingContactDetails.first_name +
                                    " " + existingContactDetails.last_name +
                                    '</strong> already exists. Would you like to access it?</p></div></div>',
                                showCancelButton: !0,
                                confirmButtonClass: "btn btn-danger w-xs me-2 mb-1",
                                confirmButtonText: "Yes, I want to access!",
                                cancelButtonClass: "btn btn-soft-success w-xs mb-1",
                                buttonsStyling: !1,
                                showCloseButton: !0


                            })
                            .then(function(t) {

                                if (t.isConfirmed) { // on confirm delete the category
                                    $.ajax({
                                        url: "{{ route('admin.work.contacts.assign-agents') }}",
                                        type: 'POST',
                                        dataType: 'json',
                                        data: {
                                            id: existingContact,
                                            agents: [contact_agent_id],
                                            add_new: true,
                                            formData: form.serializeArray()
                                        },
                                        success: function(result) {
                                            if (result.status ==
                                                '1') { //if successfully deleted
                                                showToastify(result.msg, "success")
                                                $(buttons).prop('disabled',
                                                    false); //enable submit button
                                                $("#addContactForm").find("select, input")
                                                    .val("")
                                                    .trigger("reset")
                                                $("#addContactForm").trigger("reset")
                                                $("#addContactModal").modal("hide")
                                                let agent_id = "";
                                                agent_id = $("#agent_id").val();
                                                getOwners(agent_id, result.owner.id)
                                                existingContact = null;
                                                existingContactDetails = null;

                                            } else { // something went wrong on backend
                                                showToastify(result.msg, "error")
                                                $(buttons).prop('disabled',
                                                    false); //enable submit button
                                                $("#addContactForm").find("select, input")
                                                    .val("")
                                                    .trigger("reset")
                                                $("#addContactForm").trigger("reset")
                                                $("#addContactModal").modal("hide")
                                                existingContact = null;
                                                existingContactDetails = null;
                                            }
                                        },
                                        error: function(result) {
                                            $(buttons).prop('disabled',
                                                false); //enable submit button
                                            existingContact = null;
                                            existingContactDetails = null;
                                        }
                                    });
                                } else {
                                    $(buttons).prop('disabled', false); //enable submit button
                                }



                            })




                    } else {
                        $('#screen_loader').removeClass('d-none');

                        $.ajax({
                            url: "{{ route('admin.work.contacts.store') }}",
                            type: 'POST',
                            dataType: 'json',
                            data: formData,
                            cache: false,
                            processData: false,
                            contentType: false,
                            success: function(result) {
                                if (result.status == '1') {
                                    showToastify(result.msg, "success")
                                    $(buttons).prop('disabled', false); //enable submit button
                                    $("#addContactForm").find("select, input").val("").trigger(
                                        "reset")
                                    $("#addContactForm").trigger("reset")
                                    $("#addContactModal").modal("hide")
                                    let agent_id = "";
                                    agent_id = $("#agent_id").val();
                                    getOwners(agent_id, result.id)
                                } else if (result.status == '2') {
                                    $(buttons).prop('disabled', false);
                                    let error_message = '';
                                    $.each(result.errors, function(key, value) {
                                        error_message += '' + key + ': ' + value +
                                            ' | ';
                                    });
                                    showToastify(error_message, "error")
                                } else {
                                    $(buttons).prop('disabled', false);
                                    showToastify(result.msg, "error")
                                }
                                $('#screen_loader').addClass('d-none');

                            },
                            error: function(jqXhr, json, errorThrown) {
                                $(buttons).prop('disabled', false);
                                let data = jqXhr.responseJSON;
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
                }, 500);
            @endcan
        });

        $(document).on("click", ".select-portal", function() {
            let id = $(this).data("id");
            let selected = [];
            $("#search_portals option:selected").map((ind, el) => {
                selected.push($(el).val())
            });
            const index = selected.indexOf(id.toString());
            if (index == -1) {
                selected.push(id);
            } else {
                selected.splice(index, 1);
            }
            $(this).toggleClass("filters-selected");
            $("#search_portals").val(selected).trigger("change")
            $("#searchSearchBtn").trigger("click")
        })

        $(document).on("click", ".select-publish-status", function() {
            let status = $(this).data("status");
            if (status == "PUBLISHED") {
                if ($(this).find("i").hasClass("ri-checkbox-circle-line")) {
                    $(this).find("i").removeClass("ri-checkbox-circle-line").addClass("ri-checkbox-circle-fill")
                } else {
                    $(this).find("i").removeClass("ri-checkbox-circle-fill").addClass("ri-checkbox-circle-line")
                }
            }

            if (status == "DRAFT") {
                if ($(this).find("i").hasClass("ri-edit-circle-line")) {
                    $(this).find("i").removeClass("ri-edit-circle-line").addClass("ri-edit-circle-fill")
                } else {
                    $(this).find("i").removeClass("ri-edit-circle-fill").addClass("ri-edit-circle-line")
                }
            }

            if (status == "UNPUBLISHED") {
                if ($(this).find("i").hasClass("ri-close-circle-line")) {
                    $(this).find("i").removeClass("ri-close-circle-line").addClass("ri-close-circle-fill")
                } else {
                    $(this).find("i").removeClass("ri-close-circle-fill").addClass("ri-close-circle-line")
                }
            }
            let selected = [];
            $("#search_publish_statuses option:selected").map((ind, el) => {
                selected.push($(el).val())
            })
            const index = selected.indexOf(status);
            if (index == -1) {
                selected.push(status);
            } else {
                selected.splice(index, 1);
            }
            $("#search_publish_statuses").val(selected).trigger("change")
            $("#searchSearchBtn").trigger("click")
        })

        $(document).on("click", ".select-publish-status_state_approved", function() {
            let status = $(this).data("status");
            let state = 'PENDING';
            if (status == "PUBLISHED") {
                if ($(this).find("i").hasClass("bx-check-shield")) {
                    $(this).find("i").removeClass("bx-check-shield").addClass(" bxs-check-shield")
                } else {
                    $(this).find("i").removeClass("bxs-check-shield").addClass(" bx-check-shield")

                }
            }

            let selected = [];
            $("#search_publish_statuses option:selected").map((ind, el) => {
                selected.push($(el).val())
            })
            // select #search_state with value APPROVED

            $("#search_state").val(state).trigger("change")

            const index = selected.indexOf(status);
            if (index == -1) {
                selected.push(status);
            } else {
                selected.splice(index, 1);
                $("#search_state").val('').trigger("change")

            }
            $("#search_publish_statuses").val(selected).trigger("change")
            $("#searchSearchBtn").trigger("click")
        })

        //


        $(document).on("click", "#clearSearchBtn", function() {
            $(".search-inputs").val(null).trigger("change");
            // $("input[type='checkbox'].search-inputs").prop("checked", false).trigger("change");
            // Get the Select2 instance for the element with class .search-inputs
            var select2Instance = $(".search-inputs").data("select2");

            // Deselect all options
            select2Instance.val(null).trigger("change");

            // Uncheck checkboxes with the class .search-inputs
            $("input[type='checkbox'].search-inputs").prop("checked", false).trigger("change");

            $("#searchSearchBtn").trigger("click")
        })





        $(document).on("click", "#refreshTableButton", function() {
            $("#searchSearchBtn").trigger("click")
        })








        let dropzone = new Dropzone('#photos-dropzone', {


            url: '{{ route('admin.listing.properties.upload.photo') }}',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            acceptedFiles: ".jpeg,.jpg,.png",
            addRemoveLinks: true,
            timeout: 50000,
            paramName: "file", // The name that will be used to transfer the file
            maxFilesize: 3, // MB
            dictDefaultMessage: 'Drag images here to upload, or click to select images',
            init: function() {
                this.on("success", function(file, response) {
                    let res = JSON.parse(response)
                    file.media_id = res.data.id;
                    photoCounter++;
                    $("#photo_counter").text(photoCounter);
                    const element = document.getElementById("messagePhotos");
                    element.innerHTML = "Upload more photos";
                    $("#messagePhotosContainer").show();
                    $("#messagePhotosIcon").hide();

                });
            },
            sending: function(file, xhr, formData) {

                formData.append('temp_id', $('#temp_id').val());
                formData.append('property_id', $('#id').val());
                if ($('#watermark').is(':checked')) {
                    formData.append('watermark', $('#watermark').val());
                }


            },
            removedfile: function(file) {
                let media_id = file.media_id;
                removePhoto(media_id);
                let propertyId = $('#id').val();
                if (propertyId && mode == "view") {
                    getProperty(propertyId);
                }
                var fileRef;
                return (fileRef = file.previewElement) != null ? fileRef.parentNode.removeChild(file
                    .previewElement) : void 0;
            },
            success: function(file, response) {
                $(file.previewElement).find('.dz-success-mark').css('opacity', '1');
                let propertyId = $("#id").val();

                if (propertyId && mode == "view") {
                    getProperty(propertyId);
                }
            },
            error: function(file, response) {
                console.log("error: ", response);
                $(file.previewElement).find('.dz-error-mark').css('opacity', '1');
                $(file.previewElement).find('.dz-error-message').css('opacity', '1').css('display', 'block');
                $(file.previewElement).find('.dz-error-message').text(response);
                return false;
            }
        });

        $(document).on("click", ".delete-photo", function() {
            @can('Delete Media')
                $(this).text('Deleting ...').css('color', 'red');
                removePhoto($(this).data("id"), this);
                console.log($(this).data("id"));
                photoCounter--;
                $("#photo_counter").text(photoCounter);
            @endcan
        })
        $(document).on("click", "#downloadBulkWatermark_", function() {
            let id = $('#id').val();

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                type: 'POST',
                url: "{{ route('admin.listing.download.bulk.watermarked.images') }}",
                data: {
                    id
                },
                success: function(response) {
                    // Create a temporary link to download the file
                    console.log('Download complete');

                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        })




        $(document).on("click", "#downloadNotForMarketing", function() {
            $('#screen_loader').removeClass('d-none');
            let id = $('#id').val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                type: 'POST',
                url: "{{ route('admin.listing.download.bulk.notForMarketing.images') }}",
                data: {
                    id
                },
                success: function(response) {
                    $('#screen_loader').addClass('d-none');
                    console.log(response.downloadUrl);
                    var downloadUrl = response.downloadUrl;
                    var zipName = response.zipName;
                    var link = document.createElement('a');
                    link.href = downloadUrl;
                    link.download = zipName;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                },
                error: function(xhr, status, error) {
                    $('#screen_loader').addClass('d-none');
                    console.log(error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong!',
                    })
                }
            });

        })


        $(document).on("click", "#downloadBulkDocuments", function() {
            $('#screen_loader').removeClass('d-none');

            let id = $('#id').val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                type: 'POST',
                url: '{{ route('admin.listing.download.bulk.documents') }}',
                data: {
                    id
                },
                success: function(response) {
                    $('#screen_loader').addClass('d-none');

                    console.log(response.downloadUrl);
                    var downloadUrl = response.downloadUrl;
                    var zipName = response.zipName;
                    var link = document.createElement('a');
                    link.href = downloadUrl;
                    link.download = zipName;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                },
                error: function(xhr, status, error) {
                    $('#screen_loader').addClass('d-none');
                    console.log(error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong!',
                    })
                }
            });

        })

        $(document).on("click", "#downloadBulkWatermark", function() {
            $('#screen_loader').removeClass('d-none');
            let id = $('#id').val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                type: 'POST',
                url: "{{ route('admin.listing.download.bulk.watermarked.images') }}",
                data: {
                    id
                },
                success: function(response) {
                    $('#screen_loader').addClass('d-none');

                    console.log(response.downloadUrl);
                    var downloadUrl = response.downloadUrl;
                    var zipName = response.zipName;
                    var link = document.createElement('a');
                    link.href = downloadUrl;
                    link.download = zipName;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                },
                error: function(xhr, status, error) {
                    $('#screen_loader').addClass('d-none');
                    console.log(error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong!',
                    })
                }
            });

        })


        $(document).on("click", "#downloadBulkwithoutWatermark", function() {
            $('#screen_loader').removeClass('d-none');

            let id = $('#id').val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                type: 'POST',
                url: '{{ route('admin.listing.download.bulk.withoutwatermarked.images') }}',
                data: {
                    id
                },
                success: function(response) {
                    $('#screen_loader').addClass('d-none');

                    console.log(response.downloadUrl);
                    var downloadUrl = response.downloadUrl;
                    var zipName = response.zipName;
                    var link = document.createElement('a');
                    link.href = downloadUrl;
                    link.download = zipName;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                },
                error: function(xhr, status, error) {
                    $('#screen_loader').addClass('d-none');
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong!',
                    })
                }
            });

        })


        $(document).on("click", ".delete-floorplan", function() {
            $(this).text('Deleting ...').css('color', 'red');
            removePhoto($(this).data("id"), this);
            $('#floor_plan_file').html("");
        })

        $(document).on("click", ".deleteBTN_doc", function() {
            $(this).text('Deleting ...').css('color', 'red');
            // alert('hellofellow')
            // alert($(this).data("input"));
            // alert($(this).data("id")+$(this).data("document"));
            removePhoto($(this).data("id"), this);
            $('#' + $(this).data("input")).val("");
            $('#' + $(this).data("document")).html("");
        })

        $(document).on("click", ".deleteBTNExtraDoc", function() {
            $(this).text('Deleting ...').css('color', 'red');
            removePhoto($(this).data("id"), this);
            $('.' + 'extraDoc_id_' + $(this).data("id")).val("");
            $('.' + 'extraDoc_id_' + $(this).data("id")).remove();

        })


        function removePhoto(media_id, removeElement = null) {

            galleryArr = jQuery.grep(galleryArr, function(value) {
                return value != media_id;
            });
            const remove = () => {
                $(removeElement).closest(".photo-element").remove();

            }

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                type: 'POST',
                url: "{{ route('admin.listing.properties.remove.photo') }}",
                data: {
                    media_id
                },
                success: function(response) {
                    if (response.status == 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: response.msg,
                        })
                    } else {
                        if (removeElement != null) {
                            remove()
                        }
                        let propertyId = $("#id").val();
                        if (propertyId && mode == "view") {
                            dataTable.ajax.reload(null, false);

                            // dataTable.draw();
                        }
                        console.log("File has been successfully removed!!");
                    }

                },
                error: function(e) {
                    console.log(e);
                }
            });
        }

        var isUpdate = false;

        function toggleAdvanceSearch() {
            $("#collapseFilterForm").toggleClass("show")
        }

        function makeFirstOptionSelected() {
            $("select.inputs").each((index, item) => {
                let id = $(item).attr("id")
                if (id != "features") {
                    $("option:first", item).prop('selected', 'selected').trigger('change');
                }
            })
        }


        $(document).on("change", ".image-checkbox", function() {
            var number = $(".image-checkbox:checked").length;
            $('#selection_count').text(number);
        })
        $(document).on("click", "#attach-images-from-drive-select-all", function() {
            var number = $(".image-checkbox:checked").length;
            $('#selection_count').text(number);
        })

        $(document).on("click", "#expand-modal-images", function() {
            var $modalDialog = $('#image-upload-modal').find('.modal-dialog');
            if ($('#image-upload-modal').hasClass('modal-fullscreen')) {
                $('#image-upload-modal').removeClass('modal-fullscreen').addClass('modal-lg');
                $('#expand-modal-images').html('<i class="ri-fullscreen-line"></i> Expand')
                $('.expanded-images').addClass('col-md-3');
                $('.expanded-images').removeClass('col-md-2');

            } else {
                $('#image-upload-modal').removeClass('modal-lg').addClass('modal-fullscreen');
                $('#expand-modal-images').html('<i class=" ri-fullscreen-exit-line"></i> Exit Expand')
                $('.expanded-images').removeClass('col-md-3');
                $('.expanded-images').addClass('col-md-2');
            }
        });



        $(document).on("click", "#newBtn", function() {
            $("#is_live_status").html("");
            $("#publishPocketBtn").removeClass("d-none");
            $('#eventsLog').addClass('d-none');
            $('#messageTab').addClass('d-none');
            $(".meta-data-accordion").html('');
            $(".meta-data-accordion").removeClass('show');
            $(".metaDataBtn").addClass('collapsed');
            $(".metaDataBtn").addClass('d-none');
            $("#photo_counter").text(0);
            $("#document_counter").text(0);
            $('.ContractDaysPlacing').html("");
            $('#watermark').prop('checked');
            $('#savePublishBtn').removeClass('d-none');
            $('#archiveBtn').addClass('d-none');

            @can('Properties Create')

                $('#change-layout').addClass('col-xl-5');
                $('#change-layout').removeClass('col-xl-8');
                $('#actionTab,#portalsTab,#messageTab').removeClass('d-none');


                $('.own_property').removeClass('d-none');
                $('#screen_loader').removeClass('d-none');
                $('.bulkDownload').addClass('d-none');
                $('#divNotes').addClass('d-none');
                $('#users-conversation').html("");
                $("#addContact").removeClass("d-none")
                $("#add-more-documents-btn").show();
                setRefNo()
                fullyLoaded = false;
                $('#mainForm').removeClass('was-validated');
                $('#state_div').hide();
                $("#checkRera").addClass('d-none'); //hide permit check button
                $("#reraDateRange").html("");
                $("#qr_link_file").html("");
                $("#location_address").html("")
                $("#location_type").val("")
                makeFirstOptionSelected();
                toggleDocumentInput(true);
                $("#temp_id").val(Date.now() + '-' + (Date.now().toString(36) + Math.random().toString(36))
                    .toUpperCase());
                $("#id").val("")
                isUpdate = false;
                dataObj = {};
                $(".inputs").prop("disabled", false)
                $('#view').val("").trigger("change");
                $("#mainForm").trigger("reset")
                resetDocumentLinks();
                $("#saveBtn, #closeBtn, #actionButton").removeClass("d-none");

                $("#newBtn, #editBtn, #deleteBtn, #hardDeleteBtn #multiDeleteBtn, #dropdownMenuLink1, #view-edit-mode")
                    .addClass("d-none");
                toggleForm(true)
                $("#activity-log-div").addClass("d-none")
                $("#more-documents").html("")
                mode = 'create';
                $('#screen_loader').addClass('d-none');

                @if (auth()->user()->hasRole('Agent'))
                    $('.portalBtns').attr('disabled', 'disabled');
                @else
                    $('.portalBtns').removeAttr('disabled');
                @endif
            @endcan
        });

        $(document).on("click", "#editBtn", function() {
            @can('Properties Edit')
                $('#mainForm').resetForm();
                fullyLoaded = false;
                $("#temp_id").val("");
                $(".inputs").prop("disabled", false)
                $('#mainForm').removeClass('was-validated');
                $("#price_on_application").prop("disabled", false)
                editData($('#id').val());

                checkPermission();
                // $("#actionButton").addClass("d-none");

                toggleForm(true)
            @endcan

        });





        $(document).on("click", "#closeBtn", function() {
            toggleForm(false)
            $("#actionButton").addClass("d-none");
            $("#add-more-documents-btn").hide();
            $(".admin_approval_buttons").addClass("d-none")
            mode = 'view';
            // dataTable.row().order([0, 'DESC']).draw();
            dataTable.ajax.reload(null, false);

        })

        $(document).on('click', '.edit-link', function(
            event) { //edit link click event it will fetch single cateogry details and put it on form
            @can('Properties Edit')
                $(".inputs").prop("disabled", false)
                let id = $(this).data('id');
                editData(id);
                $("#saveBtn, #closeBtn, #actionButton").removeClass("d-none");
                $("#newBtn, #editBtn, #deleteBtn, #hardDeleteBtn #multiDeleteBtn, #dropdownMenuLink1").addClass(
                    "d-none");
                toggleForm(true)
            @endcan
        });
        $(document).on("click", "#viewPendingChanges", function() {
            $("#pendingChangesModal").modal('show');
            viewPendingData($('#id').val());
        });
        $(document).on("click", ".pcmc", function() {
            $("#refuseRevisionChanges").val('');
            $("#pendingChangesModal").modal('hide');
        });
        $(document).on("click", ".pcma", function() {
            $("#refuseRevisionChanges").val('');
            $("#pendingChangesModal").modal('hide');
            let approval_ids = $("#pendingDataId").val();
            let action = 'approved';
            let propertyId = $("#id").val();
            var formData = new FormData();
            formData.append("approval_ids", approval_ids);
            formData.append("action", action);
            formData.append("id", propertyId);

            actionOnPendingData(formData);
        });
        $(document).on("click", ".pcmr", function() {
            $("#pendingChangesModal").modal('hide');


            $("#refuseReason").val('');
            $("#refuseAttachment").val('');

            $("#refusePropertyModal").modal('show');
            $("#refuseRevisionChanges").val(1);
        });

        function actionOnPendingData(formData) {
            let property = $("#id").val();
            $.ajax({
                url: "{{ route('admin.property.approval.property.action') }}",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function(result) {
                    if (result.status == '1') {
                        showToastify(result.msg, "success")
                        viewProperty(property);
                        // dataTable.draw();
                        dataTable.ajax.reload(null, false);

                    } else {
                        showToastify(result.msg, "error")
                    }
                    $('#refusePropertyButton').prop('disabled', false);
                    $('#refuseBtn').removeClass('d-none')
                    $('#refuseLoading').addClass('d-none')
                    $("#refusePropertyModal").modal('hide');
                    $("#refuseRevisionChanges").val('');
                },
                error: function(result) {
                    console.log(result);
                    $("#refuseRevisionChanges").val('');
                }
            });
        }

        function htmlForPendingDataOld(obj) {
            console.log(obj)
            let html = '';
            for (const [key, value] of Object.entries(obj)) {
                old_price = value;
                old_price_price = value;
                console.log(key)

                let cls = key == 'Description' ?
                    `<div class="overflow-scroll p-3 mb-0 bg-light col-lg-8 mt-2" style="height: 180px;">` :
                    `<div class="col-lg-8 mt-2">`;
                html += `<div class="col-lg-4 mt-2">
                                <label for="name" class="form-label">${key}</label>
                            </div>${cls}
                                ${value ?? ''}</div>`;

            }
            return html;
        }

        function htmlForPendingData(obj) {
            console.log(obj)
            let html = '';
            for (const [key, value] of Object.entries(obj)) {
                console.log(key)
                let varchr = "";

                if ((key == 'Down Payment Price') && old_price == 0) {
                    old_price = value;
                } else {
                    // get the percentage difference between the old and new price
                    if (key == 'Down Payment Price') {
                        if (old_price) {
                            let percentage = ((value - old_price) / old_price) * 100;
                            varchr =
                                `<span class="badge bg-${percentage > 0 ? 'success' : 'danger'}"><i class='ri ri-arrow-${percentage > 0 ? 'up' : 'down'}-fill'></i>${percentage.toFixed(2)}%</span>`;
                        }
                    }
                }
                if (key == 'Price' && old_price_price == 0) {
                    old_price_price = value;
                } else {
                    // get the percentage difference between the old and new price
                    if (key == 'Price') {
                        if (old_price_price) {
                            let percentagetwo = ((value.replace(/,/g, '') - old_price_price.replace(/,/g, '')) /
                                old_price_price.replace(/,/g, '')) * 100;
                            varchr =
                                `<span class="badge bg-${percentagetwo > 0 ? 'success' : 'danger'}"><i class='ri ri-arrow-${percentagetwo > 0 ? 'up' : 'down'}-fill'></i> ${percentagetwo.toFixed(2)}%</span>`;
                        }
                    }
                }





                let cls = key == 'Description' ?
                    `<div class="overflow-scroll p-3 mb-0 bg-light col-lg-8 mt-2" style="height: 180px;">` :
                    `<div class="col-lg-8 mt-2">`;
                html += `<div class="col-lg-4 mt-2">
                                <label for="name" class="form-label">${key}</label>
                            </div>${cls}
                                ${value ?? ''} ${varchr}</div>`;

            }
            return html;
        }

        let old_price = 0;
        let old_price_price = 0;

        function showChangesOnModal(data) {
            old_price = 0;
            console.log(data)
            let html = '';
            let htmlOld = '';
            //let newData = JSON.parse(result.data.new_data);

            htmlOld = htmlForPendingDataOld(data.old_data);
            $("#pendingListOld").append(htmlOld)

            html = htmlForPendingData(data.modified_data);
            $("#pendingListNew").append(html)

            console.log(html)
            html = '';
            htmlOld = '';


            if (data.pricing != null) {
                old_price_price = 0;
                console.log(data.pricing.modified_data)
                htmlOld = htmlForPendingDataOld(data.pricing.old_data);
                $("#pendingListOld").append(htmlOld)

                html = htmlForPendingData(data.pricing.modified_data);
                $("#pendingListNew").append(html)

            }


        }

        function showPendingChangesOnForm(data) {
            for (const [key, value] of Object.entries(data.new_data)) {
                if (key == 'view') {
                    val = JSON.parse(value);
                    $(`#${key}`).val(val).trigger("change");
                } else {
                    $(`#${key}`).val(value).trigger("change");
                }
            }
        }

        function viewPendingData(id) {
            $("#pfApprovalData").html('');
            $("#pfApprovalDataContainer").addClass('d-none');
            $("#bayutApprovalDataContainer").addClass('d-none');
            $("#dubizzleApprovalDataContainer").addClass('d-none');
            $("#bayutApprovalData").html('');
            $("#dubizzleApprovalData").html('');
            $(".admin_approval_buttons").addClass("d-none");
            $("#approveProperty").addClass("d-none");
            $("#refuseProperty").addClass("d-none");
            $.ajax({
                url: "{{ route('admin.property.approval.get.property') }}",
                type: 'GET',
                dataType: 'json',
                data: {
                    id
                },
                success: function(result) {
                    if (result.status == '1') {
                        $("#pendingListNew").html('');
                        $("#pendingListOld").html('');
                        let approvalids = [];
                        for (const [key, value] of Object.entries(result.data)) {
                            approvalids.push(value.id);
                            showChangesOnModal(value);
                            @if (!auth()->user()->hasRole(['Super Admin']))
                                showPendingChangesOnForm(value);
                            @endif
                        }
                        $('#pendingDataId').val(approvalids);
                        $("#viewPendingChanges").removeClass('d-none');

                        if (result.metaData) {
                            let meta = result.metaData;

                            if (meta?.property_finder?.length > 0 && result?.is_live == 1) {
                                $("#pfApprovalDataContainer").removeClass('d-none');
                                $("#pfApprovalData").html(meta.property_finder);
                            }
                            if (meta?.bayut?.length > 0 && result?.is_live == 1) {
                                $("#bayutApprovalData").html(meta.bayut);
                                $("#bayutApprovalDataContainer").removeClass('d-none');
                            }
                            if (meta?.dubizzle?.length > 0 && result?.is_live == 1) {
                                $("#dubizzleApprovalData").html(meta.dubizzle);
                                $("#dubizzleApprovalDataContainer").removeClass('d-none');
                            }
                        }
                        // if(result.media_changed){

                        // }


                    } else {
                        $("#viewPendingChanges").addClass('d-none')
                        if ($("#publish_status").val() == 'PENDING') {
                            if (result?.request_type == "PUBLISHED") {
                                $("#approveProperty").text("Approve & Publish");

                            } else if (result?.request_type == "POCKET") {
                                $("#approveProperty").text("Approve as Pocket");

                            }
                            $(".admin_approval_buttons").removeClass("d-none");
                            $("#refuseProperty").removeClass("d-none");
                            $("#approveProperty").removeClass("d-none");
                        } else {
                            $(".admin_approval_buttons").addClass("d-none");
                            $("#approveProperty").addClass("d-none");
                            $("#refuseProperty").addClass("d-none");
                        }
                        //showToastify(result.msg, "error")
                    }
                    if (result?.media_changed.length != 0) {
                        console.log(result?.media_changed)
                        let mediaChangedMessage = "";
                        result.media_changed.forEach(function(item) {
                            let count = item.count;
                            let pluralSuffix = count > 1 ? "s" : "";
                            let verb = count > 1 ? "were" : "was";
                            let documentTitle = item.title != null ? item.title : "image" +
                                pluralSuffix;

                            mediaChangedMessage += count + " " + documentTitle + " " + verb + " " + item
                                .media_changed.toLowerCase();
                            mediaChangedMessage += "<br>";
                        });
                        let html = `<div class="alert alert-danger alert-dismissible fade show mb-xl-0 p-2" role="alert">
                                    <strong> Property Media Changed! </strong> <br>${mediaChangedMessage}

                                </div>`;
                        $("#media_changed_container").html(html);
                    } else {
                        $("#media_changed_container").html("");
                    }
                },
                error: function(result) {
                    console.log(result);
                }
            });

        }
        $("#checkAll").change(function() {
            $(".selectable-checkbox").prop("checked", this.checked)
        })
        $(document).on('change', 'tbody tr .selectable-checkbox', function() {

            $(this).parents('tr').toggleClass('selected');
            var checked = $(this).is(':checked');
            if (!checked) {
                $(this).prop('checked', false);
            } else {
                $(this).prop('checked', true);
            }
            @if (!auth()->user()->hasRole('Agent'))
                updateMultiOption();
            @endif
        });

        $("#for").on("change", function() {
            $("#cheques").val("").trigger("change");
            let isRent = ["Residential for rent", "Commercial for rent"].indexOf($(this).val()) != -1;
            if (isRent) {
                $("#cheques-section").removeClass("d-none");
                $('#completion_status option[value="Off-Plan Primary"]').prop('disabled', true);
                $('#completion_status option[value="Off-Plan Secondary"]').prop('disabled', true);
                $('#completion_status option[value="Ready Primary"]').prop('disabled', false);
                $('#completion_status option[value="Ready Secondary"]').prop('disabled', false);


                @can('Properties Hide Price')
                    $('#hideprice-section').addClass("d-none");
                @endcan
            } else {
                $("#cheques-section").addClass("d-none");
                $('#completion_status option[value="Off-Plan Primary"]').prop('disabled', false);
                $('#completion_status option[value="Off-Plan Secondary"]').prop('disabled', false);
                $('#completion_status option[value="Ready Primary"]').prop('disabled', false);
                $('#completion_status option[value="Ready Secondary"]').prop('disabled', false);
                @can('Properties Hide Price')
                    $('#hideprice-section').removeClass("d-none");
                @endcan
            }

            checkListingDuplication();
        })

        $(document).on("change", "#location_id", function() {
            $("#location_address").html("")
            $("#location_type").val("")


            // let selectedData = $(this).select2('data')[0];
            let lat = $('#location_id option:selected').attr('data-lat')
            let long = $('#location_id option:selected').attr('data-long')
            let path = $('#location_id option:selected').attr('data-path')
            let alternate_path_name = $('#location_id option:selected').attr('data-alternate_path_name')
            let type = $('#location_id option:selected').attr('data-type')



            //lat = lat == 'undefined' ? selectedData.lat : '';
            // selectedData = JSON.parse(selectedData);
            // console.log(selectedData)
            if (lat && long) {
                $("#location_address").html(
                    `<a class="view-in-map" href="javascript:void(0)" data-path="${path}" data-lat="${lat}" data-long="${long}">${path}  </a> ${alternate_path_name!= "null" ? '<i class="ri  ri-information-line" title="'+alternate_path_name+'"></i>': ""}`
                );
                $("#location_type").val(type)
            }

            checkListingDuplication();
        })
        $("#search_created_from").change(function() {
            console.log($(this).val());
        });
        $('#location_id').on('select2:select', function(e) {
            $("#location_address").html("")
            $("#location_type").val("")
            var data = e.params.data;
            // console.log(data.long);
            let lat = data.lat;
            let long = data.long;
            let path = data.path;
            let alternate_path_name = data.alternate_path_name;
            let type = data.type;
            console.log(alternate_path_name);
            if (path != null) {
                $("#location_address").html(
                    `<a class="view-in-map" href="javascript:void(0)" data-path="${path}" data-lat="${lat}" data-long="${long}">${path} </a>  ${alternate_path_name != null ? '<i class="ri  ri-information-line" data-bs-toggle="tooltip" data-bs-placement="right" title="'+alternate_path_name+'"></i>': ""}`
                );
            }
            $("#location_type").val(type)
        });

        function locationSelect2(id) {
            $("#location_address").html("");
            $("#location_type").val("");
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
                                    id: `${item.id+"?"+item.type}`,
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



        $(document).on("change", "#agent_id", function() {
            let user_id = $(this).val();
            let abuDhabiAdmin = "{{ auth()->user()->hasRole('Admin - Abu Dhabi') ? true : false }}";
            $("#agent_container").html("");
            // $('#screen_loader').removeClass('d-none');
            $("#location_id").val("").trigger("change");
            $("#location_id").html("<option value='' selected disabled>Select location</option>");
            let property_id = dataObj.id;
            if (user_id || abuDhabiAdmin) {

                if (user_id) {
                    let viewDetails =
                        `<a class='userDetails' style="cursor:pointer;" data-agent-id=${user_id}>View Details</a>`;
                    $("#agent_container").append(viewDetails);
                }

                $.ajax({
                    url: "{{ route('ajax.user-sub-community-properties') }}?property_id=" + property_id,
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        user_id: user_id
                    },
                    success: function(result) {
                        let locationSelected = dataObj?.community_property_id || dataObj
                            ?.sub_community_id;
                        locationSelect2(dataObj.id);
                        let options = "";
                        $("#location_id").html("");
                        result?.data.map(item => {
                            if (item.community_properties.length > 0) {
                                options +=
                                    `<option value="${item.id}?sub_community" data-type="sub_community" data-lat="${item.latitude}" data-long="${item.longitude}" data-path="${item.path_name}" data-alternate_path_name="${item.alternate_path_name}">${item.path_name}</option>`
                                item.community_properties?.map(item1 => {
                                    options +=
                                        `<option value="${item1.id}?community_property" data-type="community_property" data-lat="${item1.latitude}" data-long="${item1.longitude}" data-path="${item1.path_name}" data-alternate_path_name="${item.alternate_path_name}">${item1.path_name}</option>`
                                })
                            } else {
                                options +=
                                    `<option value="${item.id}?sub_community" data-type="sub_community" data-lat="${item.latitude}" data-long="${item.longitude}" data-path="${item.path_name}" data-alternate_path_name="${item.alternate_path_name}">${item.path_name}</option>`
                            }
                        })

                        $("#location_id").append(options)
                        let location = "";
                        if (dataObj?.community_property_id) {
                            location = dataObj?.community_property_id + "?community_property";
                        } else {
                            location = dataObj?.sub_community_id + "?sub_community";
                        }
                        $("#location_id").val(location).trigger("change")
                        //  $("#location_address").html(`<a class="view-in-map" href="javascript:void(0)" data-path="${path}" data-lat="${lat}" data-long="${long}">${path}</a>`);
                        //  $("#location_type").val(type)

                        // locationSelect2();
                        // $('#screen_loader').addClass('d-none');

                    },
                    error: function(result) {
                        console.log(result);
                        // $('#screen_loader').addClass('d-none');

                    }
                });
            }
        })

        $(document).on("click", "#addNoteChatButton", function() {
            console.log("clicked")
            @can('Property Notes Create')
                let id = $("#id").val();

                if (id == "") {
                    showToastify("Please save record before adding a note.", "error")
                    return;
                }
                let note = $("#chat-input").val();

                let image = $('#mediaInput').prop('files')[0];
                if (note == "") {
                    showToastify("Please enter note.", "error")
                    return;
                }
                var formData = new FormData();
                formData.append("property_id", id)
                formData.append("note", note)
                formData.append("image", image)
                $('#messegeLoading').removeClass('d-none');
                $('#messageSend').addClass('d-none');
                $('#addNoteChatButton').attr('disabled', true);

                $.ajax({
                    url: "{{ route('admin.listing.properties.add.note') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: formData,
                    cache: false,
                    processData: false,
                    contentType: false,
                    success: function(result) {
                        if (result.status == '1') {
                            showToastify(result.msg, "success")
                            $("#note").val("").trigger("change");
                            $('#mediaInput').val('');
                            $("#chat-input").val("").trigger("change");
                            getPropertyNotes()
                            // remove #blah image
                            $('#showWhenImage').addClass('d-none');
                            $('#blah').attr('src', '');
                        }
                        if (result.status == '0') {
                            showToastify(result.msg, "error");
                            $("#note").val("").trigger("change");
                            $('#mediaInput').val('');
                            $("#chat-input").val("").trigger("change");
                        }
                        $('#messegeLoading').addClass('d-none');
                        $('#messageSend').removeClass('d-none');
                        $('#addNoteChatButton').attr('disabled', false);

                    },
                    error: function(jqXhr, json, errorThrown) {
                        let data = jqXhr.responseJSON;
                        if (data.errors) {
                            $.each(data.errors, function(index, item) {
                                showToastify(item[0], "error")
                            })
                        }
                        if (jqXhr.status == 500 || jqXhr.status == 400) {
                            showToastify(data.msg, "error")
                        }
                        $('#messegeLoading').addClass('d-none');
                        $('#messageSend').removeClass('d-none');
                        $('#addNoteChatButton').attr('disabled', false);

                    },
                });
            @endcan
        })

        $(document).ready(function() {
            $("#mediaInput").change(function() {
                readURL(this);
            });

            $('#showInactiveAgentsContacts').change(function() {
                let inactive_agent = false;
                if ($('#showInactiveAgentsContacts').is(":checked")) {
                    inactive_agent = true;
                }

                $.ajax({
                    url: "{{ route('ajax.getAllAgents') }}",
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        property_id: $("#id").val(),
                        inactive_agent: inactive_agent
                    },
                    success: function(result) {
                        let options = "<option value='' selected disabled>Select sds</option>";
                        result?.data.map(item => {

                            item.last_name = item.last_name == null ? "" : item
                                .last_name;
                            name = item.first_name + " " + item.last_name;

                            options += `  <option data-image="${ item.thumbnail }" data-status="${ item.status }"
                                            value="${ item.id }">
                                            ${ item.first_name }
                                            ${ item.last_name }</option>`
                        })
                        $("#agent_id").html(options)
                        // $("#agent_id").val(owner_id || dataObj?.owner_id).trigger("change");
                    },
                    error: function(result) {
                        console.log(result);
                    }
                });
            })
        })






        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#blah').attr('src', e.target.result);
                    $('#showWhenImage').removeClass('d-none');

                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        function getOwners(agent_id, owner_id = "") {
            $("#owner_id").val("").trigger("change");
            $("#owner_id").html(`<option value='' selected disabled>Select owner</option>`);

            if (agent_id) {
                $.ajax({
                    url: "{{ route('ajax.owners') }}",
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        agent_id: agent_id,
                    },
                    success: function(result) {
                        let options = "<option value='' selected disabled>Select owner</option>";
                        result?.data.map(item => {

                            item.last_name = item.last_name == null ? "" : item.last_name;
                            name = item.first_name + " " + item.last_name;

                            options +=
                                `<option data-mobile="${item.mobile}" value="${item.id}">${name}</option>`
                        })
                        $("#owner_id").html(options)
                        $("#owner_id").val(owner_id || dataObj?.owner_id).trigger("change");
                    },
                    error: function(result) {
                        console.log(result);
                    }
                });
            }
        }

        function getPropertyNotes() {
            @can('Property Notes List')
                let property_id = $("#id").val();
                $("#users-conversation").html("");
                $.ajax({
                    url: "{{ route('ajax.property-notes') }}",
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        property_id
                    },
                    success: function(result) {
                        $('#messageTab').removeClass('d-none');
                        let chat = "";
                        let user_id = {{ auth()->user()->id }};
                        result?.data.map(item => {
                            let date = moment(item.created_at).format("DD/MM/YY HH:mm")
                            let time = moment(item.created_at).format("HH:mm")
                            let imageUrl =
                                `${item.created_by ? item.created_by.thumb_url : "{{ URL::asset('assets/images/users/avatar-8.jpg') }}"}`;
                            let user_image = "";
                            if (item.created_by.id != user_id) {
                                user_image = `<div data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="bottom" aria-label="${item.created_by ? item.created_by.name : "System"}" data-bs-original-title="${item.created_by ? item.created_by.name : "System"}" title="${item.created_by ? item.created_by.name : "System"}">
                                        <div class='chat-avatar'><img src="${imageUrl}" alt=""></div>
                                    </div>`;
                            }
                            let imageData = '';
                            if (item.url) {
                                imageData = `<div class="popup-img d-inline-block mb-1" id="1"">
                                    <a data-lightbox="user-avatar_${item.id}" href="${item.url}">
                                                        <img class="img-thumbnail rounded border" src="${item.url}" data-lightbox="${item.url}">
                                                        </a>
                                                    </div>`;
                            }
                            chat += `<li class="chat-list ${item.created_by.id == user_id ? "right" : "left"}">
                                        <div class="conversation-list">
                                            ${user_image}
                                            <div class="user-chat-content">

                                                ${imageData}

                                                <div class="ctext-wrap">

                                                    <div class="ctext-wrap-content" id="1">
                                                        <p class="mb-0 ctext-content">${item.note}</p>
                                                    </div>

                                                </div>
                                                <div class="conversation-name">
                                                    <span class="d-none name"><small>${item.created_by ? item.created_by.name : "System"}</small></span>
                                                    <small class="text-muted time">${date}</small>
                                                </div>
                                                <span class="text-success check-message-icon"><i class="bx bx-check-double"></i></span>
                                            </div>
                                        </div>
                                    </li>`;

                        })

                        $("#users-conversation").html(chat)
                        $('#chat_container').scrollTop($('#chat_container')[0].scrollHeight);

                        setTimeout(function() {

                            let chatElement = document.getElementById("chat_container");
                            chatElement.scrollTop = chatElement.scrollHeight
                        }, 100);

                    },
                    error: function(result) {
                        console.log(result);
                    }
                });
            @endcan
        }

        function getAgentPortalCounts(agent_id) {
            $(".agent-portals-counts").removeClass("text-danger").html("");
            if (agent_id) {
                $.ajax({
                    url: "{{ route('ajax.agent-allocations') }}",
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        agent_id
                    },
                    success: function(result) {
                        result.data.map(item => {
                            if (!isUpdate) {
                                // Replace the below line with the following comment to enable the portal quota system
                                // if (item.allocation > item.count) {
                                if (true) {
                                    $(`#portal-switch-${item.id}`).prop("disabled", false);
                                } else {
                                    $(`#portal-switch-${item.id}`).prop("disabled", true);
                                }
                            } else {
                                const checked = $(`#portal-switch-${item.id}`).is(':checked');
                                if (true) {
                                    $(`#portal-switch-${item.id}`).prop("disabled", false);
                                } else {
                                    $(`#portal-switch-${item.id}`).prop("disabled", true);
                                }
                            }
                            $(`#agent-portal-${item.id}-count`).html(`${item.count}/${item.allocation}`)
                        })

                        if (mode == 'view') {
                            $('.portal-switchs').prop('disabled', true);
                        } else {
                            $('.portal-switchs').prop('disabled', false);

                            @if (auth()->user()->hasRole('Agent'))
                                $('.portalBtns').attr('disabled', 'disabled');
                            @else
                                $('.portalBtns').removeAttr('disabled');
                            @endif
                        }




                    },
                    error: function(result) {
                        console.log(result);
                    }
                });
            }
        }

        $(document).on("change", "#agent_id", function() {
            let agent_id = $(this).val();
            getOwners(agent_id)
            getAgentPortalCounts(agent_id)
        })

        // $(document).ready(function() {
        //     $('#signed_listing_form ,#for').change(function() {
        //         var signedListingFormValue = $(this).val();
        //         var passwordField = $('#signed_listing_form_password');

        //         if (signedListingFormValue && $('#for').val() === 'Residential for sale') {
        //             passwordField.attr('required', 'true');
        //             $('#makeRequired').html('<span class="text-danger">*</span>')
        //         } else {
        //             passwordField.removeAttr('required');
        //             $('#makeRequired').html('')
        //         }
        //     });
        // });

        $(document).ready(function() {
            $('#signed_listing_form').change(function() {
                var signedListingFormFile = $(this).prop('files')[0];
                var passwordField = $('#signed_listing_form_password');

                if (signedListingFormFile && $('#for').val() === 'Residential for sale') {
                    passwordField.prop('required', true);
                    $('#makeRequired').html('<span class="text-danger">*</span>');
                    // passwordField.addClass('border-danger');
                } else {
                    passwordField.prop('required', false);
                    $('#makeRequired').html('');
                    // passwordField.removeClass('border-danger');

                }
            });

            $('#for').change(function() {
                $('#signed_listing_form').trigger('change');
            });

            // $('#signed_listing_form_password').keyup(function() {
            //     if ($(this).val()) {
            //         $(this).removeClass('border-danger');
            //     }else{
            //         $(this).addClass('border-danger');
            //     }
            // });

        });

        function toggleForm(show) {

            if (show == undefined) {
                $("#collapseUserForm").toggleClass("show")
                return
            }
            if (show) {
                $("#collapseUserForm").addClass("show");
            } else {
                $("#saveBtn, #closeBtn, #editBtn, #deleteBtn, #savePublishBtn, #dropdownMenuLink1, #viewPendingChanges, #hardDeleteBtn, #actionButton")
                    .addClass("d-none");
                $("#newBtn, #multiDeleteBtn").removeClass("d-none");
                $("#collapseUserForm").removeClass("show");
                $("#photos-gallery").html("")
                setRefNo()
            }
        }
        $("#search_price_from").change(function() {
            let value = $(this).val();
            $("#search_price_to").attr("min", value);
        });
        $(document).on('submit', '#mainForm', function(event) {
            $('#screen_loader').removeClass('d-none');
            $("#saveBtn").prop('disabled', true);
            // $("#actionButton").hide();
            event.preventDefault();
            var formData = new FormData($(this)[0]);
            // console.table(formData)
            $.ajax({
                url: isUpdate ? "{{ route('admin.listing.properties.update') }}" :
                    "{{ route('admin.listing.properties.store') }}",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function(result) {

                    if (result.status == '1') {
                        showToastify(result.msg, "success")
                        $('#mainForm').resetForm();
                        $("#saveBtn").prop('disabled', false); //enable submit button
                        $("select.inputs").val("").trigger("change")
                        form_submitted = 1;
                        viewProperty(result.id)
                        //dataTable.row().order([0, 'DESC']).draw(); // redraw the datatable show we don't need to reload the page to see the changes
                        // dataTable.draw('page');
                        // refresh current page
                        dataTable.ajax.reload(null, false);

                        isUpdate =
                            true; // set isUpdate to false so that when we try to add new category it will be create request
                    } else if (result.status == '2') {
                        $("#saveBtn").prop('disabled', false);
                        let error_message = '';

                        $.each(result.errors, function(key, value) {
                            error_message += '' + key + ': ' + value + ' | ';
                        });
                        // showToastify(error_message, "error")
                    } else {
                        $("#saveBtn").prop('disabled', false);
                        showToastify(result.msg, "error")
                    }
                    // alert(result.msg)
                    $('#screen_loader').addClass('d-none');
                    $('#isPublishRequest').val('0');
                    $('#isPocketRequest').val('0');


                },
                error: function(jqXhr, json, errorThrown) {
                    let data = jqXhr;

                    $("#saveBtn").prop('disabled', false);
                    if (data.responseJSON.errors) {
                        $.each(data.responseJSON.errors, function(index, item) {
                            showToastify(item[0], "error")
                        })
                    }

                    if (jqXhr.status == 500 || jqXhr.status == 400 || jqXhr.status == 422) {
                        if (data.responseJSON.msg) {
                            showToastify(data.responseJSON.msg, "error")
                        }
                    }

                    if (jqXhr.responseJSON.message == 'The contract dates field is required.') {

                        $('.nav-item').removeClass('active');
                        $('.nav-link').removeClass('active');
                        $('.docsTab').addClass('active');
                        $('#docsTab').attr('aria-selected', 'true');

                        $('.tab-pane').removeClass('active');
                        $('#documents-tab').addClass('active');
                        $('html, body').animate({
                            scrollTop: $("#documents-tab").offset().top
                        }, 1000);
                    }
                    // console.log(json);
                    // console.log(jqXhr.responseJSON.message);
                    // console.log(errorThrown);

                    // if(data.responseJSON.msg){
                    //     showToastify(data.responseJSON.msg, "error")
                    // }
                    $('#screen_loader').addClass('d-none');
                    $('#isPublishRequest').val('0');
                    $('#isPocketRequest').val('0');

                },
            });
        });

        let dataTable = $('#dataTable').DataTable({ //datatable initialization
            "order": [
                [13, "DESC"]
            ],
            'lengthMenu': [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],
            dom: `<'row'<'col-sm-6 text-left'B> <'col-sm-6 text-right'f>>
            <'row'<'col-sm-12'tr>>
            <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>`,
            buttons: [{
                    extend: 'colvis',
                    titleAttr: 'Show/Hide',
                    text: '<i class="colvis" style="font-size:09px">Show / Hide</i>',

                },
                @can('Properties Edit')
                    {

                        extend: 'csvHtml5',
                        title: 'AX Secondary - Listings',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                @endcan
                @can('Properties Export')
                    {
                        text: 'Export Filtered',
                        action: function(e, dt, node, config) {
                            $("#exportBtn").trigger('click')
                        }
                    },
                @endcan
            ],
            language: {
                searchPlaceholder: "Search listing"
            },
            createdRow: function(row, data, index) {
                $('#dataTable tbody').css('cursor', 'pointer');

            },
            scrollX: true,
            // responsive: true,
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "{{ route('admin.temp-listing.properties.dt') }}",
                type: "post",
                data: function(d) {

                    d.agents_operator = filtersData?.search_agents_operator;
                    d.agents = filtersData?.search_agents;

                    d.refer_operator = filtersData?.search_refered_by_operator;
                    d.refers = filtersData?.search_refered_by;

                    d.managers_operator = filtersData?.search_managers_operator;
                    d.managers = filtersData?.search_managers;


                    d.id_operator = filtersData?.search_id_find_operator;
                    d.id = filtersData?.search_id_find;

                    d.ref_no_operator = filtersData?.search_ref_no_operator;
                    d.ref_no = filtersData?.search_ref_no;

                    d.updated_by = filtersData?.search_updated_by;
                    d.updated_by_operator = filtersData?.search_updated_by_operator;

                    d.owners = filtersData?.search_owners;
                    d.owners_operator = filtersData?.search_owners_operator;

                    d.regions = filtersData?.search_regions;
                    d.regions_operator = filtersData?.search_regions_operator;

                    d.beds = filtersData?.search_beds;
                    d.beds_operator = filtersData?.search_beds_operator;

                    d.baths = filtersData?.search_baths;
                    d.baths_operator = filtersData?.search_baths_operator;

                    d.created_from = filtersData?.search_created_from;
                    d.created_from_operator = filtersData?.search_created_from_operator;

                    d.updated_at = filtersData?.search_update_from;
                    d.updated_at_operator = filtersData?.search_update_from_operator;

                    d.exclusive_start = filtersData?.search_exclusive_start;
                    d.exclusive_start_operator = filtersData?.search_exclusive_start_operator;

                    d.exclusive_end = filtersData?.search_exclusive_end;
                    d.exclusive_end_operator = filtersData?.search_exclusive_end_operator;





                    d.categories = filtersData?.search_categories;
                    d.categories_operator = filtersData?.search_categories_operator;



                    d.type = filtersData?.search_unit_type;
                    d.type_operator = filtersData?.search_unit_type_operator;

                    d.telesales = filtersData?.filter_telesales;
                    d.telesales_operator = filtersData?.filter_telesales_operator;




                    d.unit_no = filtersData?.search_unit_no;
                    d.unit_no_operator = filtersData?.search_unit_no_operator;


                    d.communities = filtersData?.search_communities;
                    d.communities_operator = filtersData?.search_communities_operator;

                    d.locations = filtersData?.search_locations;
                    d.locations_operator = filtersData?.search_locations_operator;

                    d.sub_communities = filtersData?.sub_communities;

                    d.property_statuses = filtersData?.search_property_statuses;
                    d.property_statuses_operator = filtersData?.search_property_statuses_operator;

                    d.property_request_statuses = filtersData?.search_property_request_status;
                    d.property_request_statuses_operator = filtersData?.search_property_request_status_operator;




                    d.for = filtersData?.search_for;
                    d.for_operator = filtersData?.search_for_operator;

                    d.rera_permit_number = filtersData?.search_permit_no
                    d.rera_permit_number_operator = filtersData?.search_permit_no_operator

                    d.all_listings = all_listing_val;
                    d.properties = filtersData?.search_properties;

                    d.price_from = filtersData?.search_price_from;
                    d.price_to = filtersData?.search_price_to;

                    d.search_statuses = filtersData?.search_statuses;
                    d.publish_statuses_operator = filtersData?.search_statuses_operator;

                    d.state = filtersData?.search_state;
                    d.state_operator = filtersData?.search_state_operator;

                    d.portals = filtersData?.search_portals;
                    d.portals_operator = filtersData?.search_portals_operator;
                    // d.featured = filtersData?.search_featured;
                    d.completion_status = filtersData?.search_completion_status;
                    d.completion_status_operator = filtersData?.search_completion_status_operator;



                    d.published_date = filtersData?.search_published_date;
                    d.published_date_operator = filtersData?.search_published_date_operator;

                    d.rented_until = filtersData?.search_rented_until;
                    d.rented_until_operator = filtersData?.search_rented_until_operator;

                    d.rera_start = filtersData?.search_rera_start;
                    d.rera_start_operator = filtersData?.search_rera_start_operator;

                    d.furnishing = filtersData?.search_furnishing;
                    d.furnishing_operator = filtersData?.search_furnishing_operator;



                    d.rera_end = filtersData?.search_rera_end;
                    d.rera_end_operator = filtersData?.search_rera_end_operator;





                    d.next_available_from = filtersData?.search_next_available_from;
                    d.next_available_from_operator = filtersData?.search_next_available_from_operator;

                    d.search_contract_dates = filtersData?.search_contract_dates;
                    d.search_contract_dates_operator = filtersData?.search_contract_dates_operator;

                    d.search_property_plot_size_from = filtersData?.search_property_plot_size_from;
                    d.search_property_plot_size_to = filtersData?.search_property_plot_size_to;
                    d.search_property_plot_size_operator = filtersData?.search_property_plot_size_operator;

                    d.search_property_build_up_area_from = filtersData?.search_property_build_up_area_from;
                    d.search_property_build_up_area_to = filtersData?.search_property_build_up_area_to;

                    d.search_property_build_up_area_operator = filtersData
                        ?.search_property_build_up_area_operator;


                    d.contract_search_type = filtersData?.contract_search_type;
                    // d.created_to = filtersData?.search_created_to;
                    d.hot = filtersData?.search_hot;
                    d.refered = filtersData?.search_refered;
                    d.deleted = filtersData?.search_deleted;
                    d.need_to_archive = filtersData?.search_need_to_archive;
                    d.exclusive = filtersData?.search_exclusive;
                    d.republish = filtersData?.search_republish_requested;

                    d.public_access = filtersData?.public_access;
                    d.savefilter_name = filtersData?.save_filter_name;

                    d.search_without_contract = filtersData?.search_without_contract;
                    d.search_permit = filtersData?.search_permit_type;
                    d.is_live = filtersData?.search_is_live;
                },
                error: function() {
                    $(".users-error").html("");
                    $("#dataTable").append(
                        '<tbody class="users-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>'
                    );
                    $("#dataTable_processing").css("display", "none");
                }
            },
            fnInitComplete: function(oSettings, json) {
                let first = $("#dataTable_wrapper .row div:first");
                first.addClass("col-md-5").removeClass("col-md-6")
                $(first).next().addClass("col-md-7").removeClass("col-md-6")
                $(".dataTables_filter label").addClass(
                    "d-flex justify-between-between align-items-center gap-2")
                $(`@includeIf('admin.listing.includes.filterButtons')`).insertAfter(".dataTables_filter input")
            },

            "aoColumns": [{
                    data: null,
                    orderable: false,
                },
                {
                    data: 'id',
                    name: 'id',
                    visible: false
                },
                {
                    data: 'ref_no',
                    name: 'ref_no',

                }, {
                    data: "unit_no",
                    name: "unit_no",
                },
                {
                    data: "property_category_id",
                    name: "property_category_id",
                },
                {
                    data: null,
                    orderable: true,
                },
                {
                    data: "sub_community_id",
                    name: "sub_community_id",
                    orderable: false,
                },

                {
                    data: "bed_id",
                    name: "bed_id",
                },
                {
                    data: null, //price
                    orderable: false,
                },
                {
                    data: 'agent_id',
                    name: 'agent_id',
                },
                {
                    data: 'updated_by_id',
                    name: 'updated_by_id',
                },
                {
                    data: 'updated_at',
                    name: 'updated_at',
                },
                {
                    data: null,
                    orderable: false,
                },
                {
                    data: null,
                    orderable: true,
                },
                {
                    data: null,
                    orderable: false,
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                },
                {
                    data: 'published_date',
                    name: 'published_date',
                },

                {
                    data: null,
                    orderable: false,
                },

            ],
            "columnDefs": [{
                    "targets": 0,
                    orderable: true,
                    render: function(type, row, data, meta) {
                        return `<div class="form-check"><input class="form-check-input selectable-checkbox" type="checkbox" name="chk_child" value="${data.id}"></div>`;
                    }
                },
                {
                    "targets": 1,
                    orderable: true,
                    render: function(type, row, data, meta) {
                        return data.id;
                    }
                },

                {
                    "targets": 2,
                    orderable: true,
                    render: function(type, row, data, meta) {
                        text = data?.for?.replace(/\s/g, "-");
                        return `<span title="` + data.for+
                            `"><a href="javascript:void(0)"  class="font-12 badge badge-border ` + text +
                            `">` + data.ref_no + `</a></span>`;
                    }
                },
                {
                    "targets": 4,
                    orderable: true,
                    render: function(type, row, data, meta) {
                        return data.property_category ? data.property_category.title : "-";
                    }
                },
                {
                    "targets": 5,
                    orderable: true,
                    visible: false,
                    // replace data.for spaces with -


                    render: function(type, row, data, meta) {
                        text = data?.for?.replace(/\s/g, "-");
                        return '<span class="badge ' + text + ' badge-border ">' + data.for+'</span>';
                    }
                },
                {
                    "targets": 6,
                    orderable: false,
                    visible: false,
                    render: function(type, row, data, meta) {
                        return data.rera_permit_number;
                    }
                },

                {
                    "targets": 7,
                    orderable: false,
                    visible: false,
                    render: function(type, row, data, meta) {
                        if (data.rera_issue_date && data.rera_expiry_date) {
                            // condition to check if rera issue date is greater than current date
                            var currentDate = moment();
                            var expiryDate = moment(data.rera_expiry_date);
                            var daysDifference = expiryDate.diff(currentDate, 'days');


                            if (expiryDate.isAfter(moment().add(15, 'days'))) {
                                return '<span class="badge badge-soft-success badge-border">' + moment(data
                                    .rera_expiry_date).format('ll') + '</span>';
                            } else if (daysDifference <= 15 && daysDifference >= 0) {
                                return '<span class="badge badge-soft-warning badge-border">' + moment(data
                                    .rera_expiry_date).format('ll') + '</span>';
                            } else {
                                return '<span class="badge badge-soft-danger badge-border">' + moment(data
                                    .rera_expiry_date).format('ll') + '</span>';
                            }

                            // condition to check that expiry data is 15 days less than current date

                        } else {
                            return '-';
                        }
                    }
                },
                {
                    "targets": 8,
                    orderable: true,
                    render: function(type, row, data, meta) {
                        if (data.community_property) {
                            let pathName = data.community_property.path_name;
                            return '<p class="datatableLocation mb-0"><strong>' + pathName.split(/,(.+)/)[
                                0] + '</strong>,' + pathName.split(/,(.+)/)[1] + '</p>';
                        }
                        if (data.sub_community) {
                            let pathName = data.sub_community.path_name;
                            return '<p class="datatableLocation mb-0"><strong>' + pathName.split(/,(.+)/)[
                                0] + '</strong>,' + pathName.split(/,(.+)/)[1] + '</p>';
                        }

                        return "-";
                    }
                }, {
                    "targets": 9,
                    orderable: true,
                    render: function(type, row, data, meta) {
                        return data.bed ? data.bed.name : "-";
                    }
                },
                {
                    "targets": 10,
                    orderable: true,
                    render: function(type, row, data, meta) {
                        return data.price && data.price ? data.price.toString().replace(
                            /\B(?=(\d{3})+(?!\d))/g, ",") : "-";
                    }
                },
                {
                    "targets": 11,
                    orderable: false,
                    render: function(type, row, data, meta) {
                        if (!data.agent) return "-";
                        return `<div class="d-flex align-items-center userDetails" data-agent-id="${data.agent.id}" title="${data.agent.name}">
                                    <div class="flex-shrink-0 me-2">
                                        <img src="${data.agent.thumb_url}" alt="" class="avatar-xs rounded-circle">
                                    </div>
                                    <div class="flex-grow-1">${data.agent.first_name}<p title="Created at" class="mb-0"><small>${moment(data.created_at).fromNow()}</small></p></div>
                                </div>`;
                    }
                },
                {
                    "targets": 12,
                    orderable: true,
                    visible: false,
                    render: function(type, row, data, meta) {
                        if (!data.updated_by) return "-";
                        return `<div class="d-flex align-items-center userDetails" data-agent-id="${data.updated_by.id}"  title="${data.updated_by.name}">
                                    <div class="flex-shrink-0 me-2">
                                        <img src="${data.updated_by.thumb_url}" alt="" class="avatar-xs rounded-circle">
                                    </div>
                                    <div class="flex-grow-1">${data.updated_by.first_name}<p title="Updated at" class="mb-0"><small>${moment(data.updated_at).fromNow()}</small></p></div>
                                </div>`;
                    }
                }, {
                    "targets": 13,
                    orderable: true,
                    render: function(type, row, data, meta) {
                        if (!data.updated_by)
                            return `<div class="flex-grow-1"><p class="mb-0"><small>${moment(data.updated_at).fromNow()}</small></p></div>`;
                        return `<div class="d-flex align-items-center userDetails" data-agent-id="${data.updated_by.id}"  title="${data.updated_by.name}">
                                    <div class="flex-shrink-0 me-2">
                                        <img src="${data.updated_by.thumb_url}" alt="" class="avatar-xs rounded-circle">
                                    </div>
                                    <div class="flex-grow-1">${data.updated_by.first_name}<p class="mb-0"><small>${moment(data.updated_at).fromNow()}</small></p></div>
                                </div>`;
                    }
                }, {
                    "targets": 14, //portals
                    orderable: true,
                    render: function(type, row, data, meta) {
                        let portals = "";
                        data?.portals.map(item => {
                            if (item.portal) {
                                portals += `<a href="javascript:void(0)" class="remove-portal-button" data-id="${data.id}" data-portal="${item.portal_id}" title="${item.portal.name}">
                                        <img src="${item.portal.thumbnail}" alt="" style="height: 20px; width: 20px">
                                    </a>`;
                            }
                        })
                        return portals;
                    }
                },

                {
                    targets: 15,
                    orderable: false,
                    visible: true,
                    render: function(type, row, data, meta) {
                        let url = "{{ route('admin.temp-listing.index') }}" + "?ref_number=" + data.ref_no;
                        let new_tab = '<a title="Open Listing in New Tab" target="_blank" href="' + url +
                            '"><span class="ri-external-link-line"></span></a>';

                        let publishStatus = createPublishStatusBadge(data.publish_status);
                        let propertyStatus = data.property_status ? data.property_status.title : "";


                        var icons = new Map([
                            ['APPROVED', 'bx bx-check-circle text-success'],
                            ['REFUSED', 'bx bx-x-circle text-danger'],
                            ['PENDING', 'bx bx-hourglass text-warning'],
                        ]);



                        return `<div title="${data.publish_status}" class="d-flex justify-content-between gap-2 align-items-center">
                            <span class="btn btn-soft-${data.publish_status == "PUBLISHED" ? "success" : (data.publish_status == "DRAFT" ? "primary" : data.publish_status == "PENDING" ? "warning" : data.publish_status == "POCKET" ? "secondary" : "danger")} btn-label btn-sm rounded-pill pl-12">
                                ${data.publish_status}${data.is_live ? "<span class='is_live'></span>":""}
                            </span>
                            ${new_tab}
                        </div>`;

                        /*return `<div class="d-flex justify-content-between gap-2 align-items-center">
                        <div class="text-center">${publishStatus}<br>
                            <span>${propertyStatus}</span>
                        </div>${new_tab}
                    </div>`;*/


                    }
                }, {
                    "targets": 16,
                    orderable: false,
                    visible: false,
                    render: function(type, row, data, meta) {
                        if (data.created_at == null) {
                            return '-';
                        } else {
                            return moment(data.created_at).format('DD/MM/YYYY hh:mm:ss');
                        }
                    }
                }, {
                    "targets": 17,
                    orderable: false,
                    visible: false,
                    render: function(type, row, data, meta) {
                        if (data.published_date == null) {
                            return '-';
                        } else {
                            return '<p class="mb-0" title="' + moment(data.published_date).format(
                                    "DD/MM/YYYY hh:mm:ss") + '">' + moment(data.published_date).fromNow() +
                                '</p>';

                        }
                    }
                }
                //                 , {
                //                     "targets": 18,
                //                     orderable: false,
                //                     visible: false,
                //                     render: function(type, row, data, meta) {
                //                         if (data.contract_number == null) {
                //                             return '-';
                //                         } else {
                //                             return data.contract_number;
                //
                //                         }
                //                     }
                //                 }
            ]
        });


        $(document).on("click", "#deletedMedia", function() {
            let property_id = $('#id').val();
            $.ajax({
                url: "{{ route('ajax.getDeletedDocuments') }}",
                type: 'POST',
                dataType: 'html',
                data: {
                    property_id,
                },
                success: function(result) {
                    $('#deleted_documents').html(result);
                },
                error: function(result) {
                    console.log(result);
                }
            });
        })

        $(document).on("click", ".remove-portal-button", function() {
            return;
            @can('Properties Edit')
                let property_id = $(this).data("id");
                let portal_id = $(this).data("portal");

                $.ajax({
                    url: "{{ route('admin.listing.properties.remove.portal') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        property_id,
                        portal_id
                    },
                    success: function(result) {
                        dataTable.row().draw();
                    },
                    error: function(result) {
                        console.log(result);
                    }
                });
            @endcan
        })


        // if #save_filter is checked the show .filversaver
        $(document).on("change", "#save_filter", function() {
            if ($(this).is(":checked")) {
                $(".filter_saver").removeClass("d-none");
            } else {
                $(".filter_saver").addClass("d-none");
            }
        });


        dataTable.on('click', 'tbody tr', function(e) {

            var $cell = $(e.target).closest('td'),
                msg;

            if ($cell.index() != 0) {
                var data = dataTable.row(this).data();
                //
                if ($cell.index() === 11 || $cell.index() === 12 || $cell.index() === 7 || $cell.index() === 8 ||
                    $cell.index() === 16 || $cell.index() === 10) {
                    // console.log($cell);
                } else {
                    if (mode == 'create' || mode == 'edit') {
                        var r = confirm("Are you sure you want to discard the changes?");
                        if (r == true) {
                            viewProperty(data.id);
                            highlightRow($(this));

                        } else {

                        }

                    } else {
                        viewProperty(data.id);
                        highlightRow($(this));
                    }
                }
            } // end if

        }); // table row click



        $(document).on("change", "#search_without_contract", function() {
            if ($(this).is(":checked")) {
                $('#search_contract_dates').val('');
                $('#search_contract_dates').attr('disabled', true);
                $('#search_contract_dates').attr('readonly', false);
            } else {
                $('#search_contract_dates').attr('disabled', false);
                $('#search_contract_dates').attr('readonly', false);
            }

        })


        $(document).on("click", ".toggle-hot-button", function() {
            @can('Properties Edit')
                let property_id = $(this).data("id");
                $.ajax({
                    url: "{{ route('admin.listing.properties.toggle.hot') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        property_id
                    },
                    success: function(result) {
                        dataTable.row().order([10, 'DESC']).draw();
                    },
                    error: function(result) {
                        console.log(result);
                    }
                });
            @endcan
        })

        $(document).on("click", "#saveNewDocument", function() {
            let property_id = $('#id').val();
            let document_name = $('#document_type_name').val();
            let document_id = $('#document_to_be_restored').val();
            $.ajax({
                url: "{{ route('ajax.updatePropertyDocument') }}",
                type: 'POST',
                dataType: 'json',
                data: {
                    property_id,
                    document_name,
                    document_id
                },
                success: function(result) {
                    $('#document_type_name').val('');
                    $('#document_to_be_restored').val('');
                    $('#chooseDocument').modal('hide');
                    showToastify(result.msg, "success")
                    getPropertyDocuments();
                },
                error: function(result) {
                    console.log(result);
                }
            });
        })

        $(document).ready(function() {
            // $(document).on('click', '.viewPassword', function() {
            //     var passwordSpan = $(this).next('.password');
            //     passwordSpan.toggleClass('d-none');
            //     $(this).toggleClass('d-none');
            // });


            $(document).on('click', '.changeDocument', function() {
                let data_id = $(this).data('id');
                let data_title = $(this).data('title');
                $('#document_to_be_restored').val(data_id);
                $('#new_title').text(data_title);
                $('#chooseDocument').modal('show');
            });



            $(document).on('click', '#addPassword_title_deed', function() {
                $("#owner_passport_copy_password_div").addClass('d-none');
                $("#title_deed_password_div").removeClass('d-none');
                $("#signed_listing_form_password_div").addClass('d-none');
                $('#whichPassword').text('Title Deed')
                $('#passwordModal').modal('show');
            });




            $(document).on('click', '#addPassword_owner_passport_copy_file', function() {
                $("#owner_passport_copy_password_div").removeClass('d-none');
                $("#title_deed_password_div").addClass('d-none');
                $("#signed_listing_form_password_div").addClass('d-none');
                $('#whichPassword').text('Passport Copy')
                $('#passwordModal').modal('show');
            });


            $(document).on('click', '#addPassword_signed_listing_form_file', function() {
                $("#owner_passport_copy_password_div").addClass('d-none');
                $("#title_deed_password_div").addClass('d-none');
                $("#signed_listing_form_password_div").removeClass('d-none');
                $('#whichPassword').text('Signed Listing')
                $('#passwordModal').modal('show');
            });


            $(document).on("click", ".showPasswordExtraDoc", function() {
                const passwordSpan = $(this).closest("#title_deed_filess").find("span.password");
                const inputfor = $(this).closest("#title_deed_filess").find("span.password").data('input');
                const password = passwordSpan.text().trim();
                const dataInputValue = passwordSpan.attr("data-input");
                const dataInputName = passwordSpan.attr("data-name");
                // alert(dataInputValue);
                $('#inputFor').val(inputfor);
                $('#extra_field_password').val(password);
                $('.extrafieldPassword').text(dataInputName);
                $('#extraPasswordModal').modal('show');
            });



            // if(isUpdate == false){
            $('#title_deed').change(function() {
                if ($('#title_deed_file').html() == '') {
                    $('#title_deed_file').html(`
                        <a href="javascript:void(0)" type="button" class="viewPassword mt-1"> <button type="button" class="btn btn-outline-success btn-sm waves-effect waves-light" title="View Password" id="addPassword_title_deed"> <i class="mdi-eye-arrow-left-outline mdi" ></i></button></a>
                        `)
                }
            })

            $('#owner_passport_copy').change(function() {
                if ($('#owner_passport_copy_file').html() == '') {
                    $('#owner_passport_copy_file').html(`
                        <a href="javascript:void(0)" type="button" class="viewPassword mt-1"> <button type="button" class="btn btn-outline-success btn-sm waves-effect waves-light" title="View Password" id="addPassword_owner_passport_copy_file"> <i class="mdi-eye-arrow-left-outline mdi" ></i></button></a>
                        `)
                }
            })


            $('#signed_listing_form').change(function() {
                if ($('#signed_listing_form_file').html() == '') {
                    $('#signed_listing_form_file').html(`
                        <a href="javascript:void(0)" type="button" class="viewPassword mt-1"> <button type="button" class="btn btn-outline-success btn-sm waves-effect waves-light d-none" title="View Password" id="addPassword_signed_listing_form_file"> <i class="mdi-eye-arrow-left-outline mdi" ></i></button></a>
                        `)
                }
                // } else {
                //     $('#signed_listing_form_file').html(``);
                // }
            })


            $(document).on('click', '.extra_docs', function() {
                var _title = $(this).data('name');
                $('.extrafieldPassword').text('');
                title = $('#' + _title).val();
                var id = $(this).data('id');
                var preValue = $('#' + id).val();
                $('#inputFor').val(id);
                $('#extra_field_password').val(preValue);
                $('.extrafieldPassword').text(title);
                $('#extraPasswordModal').modal('show');
            });

            //zeroFello
            $(document).on('click', '#extraPassInputs', function() {
                var id = $('#extra_field_password').val();
                var inputfor = $('#inputFor').val();
                $('#' + inputfor).val(id);
            });



            // }





        });

        function getProperty(id) {
            $('#collapseUserForm').css('visibility', 'hidden');
            $('#eventsLog').addClass('d-none');
            $(".meta-data-accordion").html('');
            $(".meta-data-accordion").removeClass('show');
            $(".metaDataBtn").addClass('d-none');
            $(".metaDataBtn").addClass('collapsed');
            $('#contract_start').val('');
            $('#contract_end').val('');
            $('.ContractDaysPlacing').html('');
            fullyLoaded = false;
            makeFirstOptionSelected();
            $("#more-documents").html("")
            $.ajax({
                url: "{{ route('admin.temp-listing.properties.get') }}",
                type: 'GET',
                dataType: 'json',
                data: {
                    id
                },
                success: function(result) {
                    if (result.status == '1') {


                        $('.own_property').removeClass('d-none');
                        $('#watermark').prop('checked');
                        $('#id').val(result.data.id);
                        $('#StatusTimeline').html('');
                        $('#activity-log-div2').html('');
                        $('#no_activity').removeClass('d-none');
                        $('#no_timeline').removeClass('d-none');

                        $('.metaDataBtn').data("property-id", id);
                        $('.will-effect-status').text('');
                        if (result.data) {
                            own_property = result.data.my_property;
                            Object.keys(result.data).map((index) => {
                                $(`#${index}`).val(result.data[index]).trigger("change")
                            })
                            $('#change-layout').addClass('col-xl-5');
                            $('#change-layout').removeClass('col-xl-8');
                            $('#actionTab,#portalsTab,#messageTab').removeClass('d-none');

                            if (!own_property && result.data.publish_status == 'POCKET') {

                                $('#change-layout').removeClass('col-xl-5');
                                $('#change-layout').addClass('col-xl-8');
                                $('#actionTab,#portalsTab,#messageTab').addClass('d-none');

                            }
                            $('#messageTab').removeClass('d-none');

                            if (result.data.publish_status == 'UNPUBLISHED') {
                                $('#unit_number').attr('disabled', true);
                            }

                            let publishedLive = ""
                            if (result.data.is_live) {
                                $('.dlt-btn').addClass('d-none');
                                isLive = true;
                                publishedLive =
                                    `<span class="btn btn-soft-success btn-label btn-sm rounded-pill pl-12">Live</span>`;
                                $('#is_live_listing').val('1');

                            } else {
                                $('.dlt-btn').removeClass('d-none');
                                isLive = false;
                                publishedLive = "";
                                $('#is_live_listing').val('0');
                            }
                            $("#is_live_status").html(publishedLive);


                            $('.bulkDownload').attr('data-ref_no', result.data.ref_no);



                            $('#divNotes').removeClass('d-none');
                            $("#exclusive_dates").flatpickr({
                                mode: "range",
                                allowInput: false,
                                dateFormat: "d-m-Y",
                                defaultDate: [result.data.exclusive_start, result.data.exclusive_end],

                            });
                            $("#contract_start").flatpickr({
                                allowInput: false,
                                dateFormat: "d-m-Y",
                                defaultDate: [result.data.contract_start],

                            });

                            $("#contract_end").flatpickr({
                                allowInput: false,
                                dateFormat: "d-m-Y",
                                defaultDate: [result.data.contract_end],
                                minDate: result.data.contract_start,
                            });
                            // duplicate-check-tab
                            if(result.data.is_duplicated){
                                document.getElementById('is_duplicated').checked = true;
                                $('#duplicate-check-tab').removeClass('d-none');
                                let badge  = result.data.is_duplicated ? `<span class="badge badge-soft-danger badge-border">Duplicated</span>` : '';
                                $('.is_duplicated').html(badge);
                                $('.duplicated_from').html(result.data.duplicated_from);
                                $('.duplicate_reason').html(result.data.duplicate_reason);
                            }else{
                                document.getElementById('is_duplicated').checked = false;
                                $('#duplicate-check-tab').addClass('d-none');
                                $('.is_duplicated').html('<span class="badge badge-soft-success badge-border">Not Duplicated</span>');
                                $('.duplicated_from').html('-');
                                $('.duplicate_reason').html('-');
                            }

                            let publishStatuses = ["DRAFT", "REFUSED", "UNPUBLISHED", "POCKET"];
                            if (publishStatuses.includes(result.data.publish_status)) {
                                $("#actionButton").removeClass("d-none");
                            } else {
                                $("#actionButton").addClass("d-none");
                            }
                            $("#checkRera").removeClass('d-none');

                            if (result.data.qr_link != null) {
                                $("#qr_link_file").html(`<a data-lightbox="qr-gallery" href="` + result.data
                                    .qr_link + `">
                                        <img src="` + result.data.qr_link + `" style="width: 37px;" alt="rera qr img"/>
                                    </a>`);
                            } else {
                                $("#qr_link_file").html('');
                            }
                            $("#reraDateRange").html("");
                            if (result.data.rera_issue_date != null && result.data.rera_expiry_date != null) {
                                if (moment(result.data.rera_expiry_date)
                                    .isAfter()) { //checking if date is in future
                                    $("#reraDateRange").html(
                                        `<span class="text-success"><i class="bx bx-check-shield"></i>` +
                                        moment(result.data.rera_issue_date).format("DD/MM/YYYY") + ` - ` +
                                        moment(result.data.rera_expiry_date).format("DD/MM/YYYY") +
                                        `</span>`);
                                } else {
                                    $("#reraDateRange").html(
                                        `<span class="text-danger"><i class="bx bx-shield-x"></i>` + moment(
                                            result.data.rera_issue_date).format("DD/MM/YYYY") + ` - ` +
                                        moment(result.data.rera_expiry_date).format("DD/MM/YYYY") +
                                        `</span>`);
                                }
                            }
                            checkExpiry(result.data.contract_end);
                            $(".form-check-input.inputs").prop("checked", false)
                            result.data?.portals.map(item => {
                                $(`#portal-switch-${item.portal_id}`).prop("checked", true);

                                if (result.data.is_live) {
                                    $(`#metaDataBtn-${item.portal_id}`).removeClass('d-none');
                                }

                            })
                            if (result.data.price && result.data.price.price_on_application) {
                                $(`#price_on_application`).prop("checked", true)
                            } else {
                                $(`#price_on_application`).prop("checked", false)
                            }

                            if (result.data.editable) {
                                $("#editBtn").css("visibility", "visible");

                                // $("#owner_id").show();
                            } else {
                                $("#editBtn").css("visibility", "hidden");
                                // $("#owner_id").hide();
                            }


                            result.data?.amenities.map(item => {
                                $(`#feature-${item.property_amenity_id}`).prop("checked", true).trigger(
                                    "change")
                            })


                            initPriceInput(result.data?.price?.price)

                            $("#property_status_id").val(result.data.property_status_id).trigger("change");
                            $("#property_category_id").val(result.data.property_category_id).trigger("change");

                            if (result.data.publish_status == 'UNPUBLISHED') {
                                $('#admin_unpublish_reason').text(result.data.admin_unpublish_reason);
                            } else {
                                $('#admin_unpublish_reason').text('');
                            }
                            if (result.data.is_live && result.data.publish_status == 'PUBLISHED' && mode != 'create') {
                                $('#for').attr('disabled', true);
                                $('#property_category_id').attr('disabled', true);
                                $('#unit_no').attr('disabled', true);


                            } else {
                                $('#for').attr('disabled', false);
                                $('#property_category_id').attr('disabled', false);
                                $('#unit_no').attr('disabled', false);

                            }
                            if (mode == 'view') {
                                $('#for').attr('disabled', true);
                                $('#property_category_id').attr('disabled', true);
                                $('#unit_no').attr('disabled', true);

                            }


                            @if (auth()->user()->hasRole('Agent')){
                                if(mode=='edit' || mode=='view'){
                                    $('#refered_by').attr('disabled', true);
                                }else{
                                    $('#refered_by').attr('disabled', false);
                                }
                            }@else{
                                $('#refered_by').attr('disabled', false);
                            }

                            @endif
                            if(mode=='view'){
                                $('#refered_by').attr('disabled', true);
                            }

                            @if (auth()->user()->hasRole('Admin'))
                                $('#unit_no').attr('disabled', false);
                                $('#for').attr('disabled', false);
                                $('#property_category_id').attr('disabled', false);
                            @endif
                            if ((result.data.publish_status === 'UNPUBLISHED' || result.data.publish_status ===
                                    'PUBLISHED') &&
                                ($('#contract_start').val() !== '' && $('#contract_end').val() !== '')) {
                                // Get the dates from the input fields
                                var givenDateStr = $('#contract_end').val();
                                if (givenDateStr.trim() === "") {
                                    // Contract end date is empty, so no need to show anything
                                    return;
                                }
                                var givenDateArr = givenDateStr.split("-");
                                if (givenDateArr.length !== 3) {

                                }
                                var givenDate = new Date(givenDateArr[2], givenDateArr[1] - 1, givenDateArr[0]);
                                var today = new Date();
                                var timeDifference = givenDate - today;
                                var daysDifference = Math.ceil(timeDifference / (1000 * 60 * 60 * 24));
                                if (daysDifference < 30) {
                                    $('#saveRePublishBtn-li').removeClass('d-none');
                                } else {
                                    $('#saveRePublishBtn-li').addClass('d-none');
                                }
                            } else {
                                $('#saveRePublishBtn-li').addClass('d-none');
                            }


                            previewUrl = "{{ route('admin.listing.properties.view', ['property' => 1]) }}";
                            previewUrl = previewUrl.slice(0, -1);
                            $("#preview-button").attr("href", `${previewUrl}${result.data.ref_no}`)
                            printPage = "{{ route('admin.listing.properties.print', ['property' => 1]) }}";
                            printPage = printPage.slice(0, -1);
                            advertisementPage =
                                "{{ route('admin.listing.properties.listing-advertisement', ['property' => 1]) }}";
                            advertisementPage = advertisementPage.slice(0, -1);
                            $("#print-button").attr("href", `${printPage}${result.data.ref_no}`);
                            $("#advertisement-button").attr("href", `${advertisementPage}${result.data.ref_no}`)
                            dataObj = result.data;
                            $("#agent_id").trigger("change");

                            // Logic to refuse a property changes starts here
                            // if (result.data.publish_status == 'PENDING' && (result.data.is_live ))
                            // {
                            //     $("#refuseProperty").removeClass("d-none");
                            //     $("#approveProperty").removeClass("d-none");
                            // } else {
                            //     $("#approveProperty").addClass("d-none");
                            //     $("#refuseProperty").addClass("d-none");
                            // }
                            // Logic to refuse a property changes ends here
                            $("#deal-create-button").attr("href",
                                "{{ route('admin.deals_revamped.index') }}?property_id=" +
                                result.data.id + "&agent_id=" + result.data.agent_id);
                            $('.printables').addClass('d-none');
                            setRefNo(result.data.ref_no)
                            renderCreatedUpdated(result.data)
                            viewPendingData(id)
                            toggleForm(true)
                            if (form_submitted != 1) {
                                if(mode!='edit'){
                                    scrollTop();
                                }

                            } else {

                                form_submitted = 0;
                            }

                            viewEvents('property_id', id)
                            getRepublishRequests();
                            // code removed from here
                            $("#select2-agent_id-container").find(".select2-selection__placeholder").text(result
                                .data?.agent?.name);
                            if (result.data.editable) {
                                $(".owner_container").show();
                                $("#unpublish-request-button").show();
                            } else {
                                $(".owner_container").hide();
                                $("#unpublish-request-button").hide();
                            }
                            @if (auth()->user()->hasRole('Agent'))
                                if (result.data.editable == false) {
                                    let html = `<div class="row">
                                        <div class="col-lg-4 col-md-4 mt-2">
                                            <label for="title" class="form-label"> Agent</label>
                                            </div>
                                        <div class="col-lg-8 col-md-8 mt-2">
                                        <input type="text" class="form-control inputs"  name="agent_name" id="agent_name" placeholder="Agent Name" disabled="true" value="${result.data.agent.name}">
                                        </div>
                                        </div>`;
                                    $(".agent_container").html(html);
                                } else {
                                    $(".agent_container").html('');
                                }
                            @endif

                            if (dataObj.publish_status == "DRAFT" || dataObj.publish_status == "UNPUBLISHED" ||
                                dataObj.publish_status == "REFUSED") {
                                $("#publish-request-button").closest("li").removeClass("d-none")
                                $("#savePublishBtn").removeClass("d-none")

                                $('#hardDeleteBtn').removeClass('d-none');
                            }
                            if (dataObj.publish_status == "PUBLISHED") {
                                $("#unpublish-request-button").closest("li").removeClass("d-none")
                                $('#hardDeleteBtn').addClass('d-none');
                                $("#savePublishBtn").addClass("d-none")

                                // remove only when .photo-element count is 3 or more

                                $('.printables').removeClass('d-none');


                            }
                            if (dataObj.publish_status == "POCKET") {
                                $("#savePublishBtn").removeClass("d-none")
                                $("#publishPocketBtn").addClass("d-none");


                                $('.printables').removeClass('d-none');

                            } else {
                                $("#publishPocketBtn").removeClass("d-none");
                            }

                            if (dataObj.publish_status == "PENDING") {

                                $('#printpage').removeClass('d-none');

                            }



                            $('#state_div').show();
                            //$("#formPropertyStatusText").html(getStateBadge(dataObj.state));

                            var icons = new Map([
                                ['APPROVED', 'bx bx-check-circle text-success'],
                                ['REFUSED', 'bx bx-x-circle text-danger'],
                                ['PENDING', 'bx bx-hourglass text-warning'],
                            ]);

                            // let propertyStateStatus =
                            //     `<div class="d-flex justify-content-between gap-2 align-items-center"> <span class="btn btn-soft-${dataObj.publish_status == "PUBLISHED" ? "success" : (dataObj.publish_status == "DRAFT" ? "primary" : dataObj.publish_status == "PENDING" ? "warning" : dataObj.publish_status == "POCKET" ? "secondary" : "danger")} btn-label btn-sm rounded-pill${dataObj.state ? '' : ' pl-12'}">${dataObj.state ? `<i class="${icons.get(dataObj.state)} label-icon align-middle rounded-pill fs-16 me-1"></i>` : ''}${dataObj.publish_status}</span></div>`;



                            //$("#formPropertyStatusText").html(propertyStateStatus);

                            // if(dataObj.state =='PENDING'){
                            //     $("#viewPendingChanges").removeClass('d-none');
                            // }else{
                            //     $("#viewPendingChanges").addClass('d-none');
                            // }

                            var url = "{{ route('admin.temp-listing.index') }}" + "?ref_number=" + result.data
                                .ref_no;
                            // console.log(url);
                            window.history.pushState('data', '', url);


                        }
                        checkPermission();
                        getPropertyDayCount();
                        getPermissionToPublish();


                        // $('#collapseUserForm').css('visibility', 'visible');
                    } else {
                        showToastify(result.msg, "error")
                    }
                    //$('#screen_loader').addClass('d-none');
                    $('#collapseUserForm').css('visibility', 'visible');
                },
                error: function(result) {
                    console.log(result);
                    //$('#screen_loader').addClass('d-none');

                }
            });
        }




        function getPropertyDocuments() {
            resetDocumentLinks();

            renderDownloadZipInputs();

            let temp_id = $("#temp_id").val();
            let property_id = $("#id").val();
            galleryArr = [];
            let document_count = 0;
            $("#document_counter").text(document_count);

            if (property_id != "") {
                $.ajax({
                    url: "{{ route('ajax.temp-property-documents') }}",
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        temp_id,
                        property_id
                    },
                    success: function(result) {
                        let data = result.data;

                        renderDownloadZipInputs(data);

                        if (data.floor_plan) {
                            // console.log(data.floor_plan);
                            $("#floor_plan_file").html(
                                `<img src="${data.floor_plan.thumbnail}" /><br><span class="text-center"><a href="${data.floor_plan.path}" target="_blank">Download</a> | <a href="javascript:void(0)" type="button" data-id="${data.floor_plan.id}" class="mt-1 delete-floorplan">Delete </a></span>`
                            )
                        }

                        if (data.title_deed) {
                            document_count++;
                            $('#title_deed_password').val(data.title_deed.password);
                            $("#title_deed_file").html(
                                `<a href="${data.title_deed.path}" class="text-success" target="_blank"><button type="button" class="btn btn-outline-primary btn-sm waves-effect waves-light" title="View Document"><i class="mdi mdi-file-eye"></i></button></a>
                                <a href="javascript:void(0)" type="button" data-id="${data.title_deed.id}" class="mt-1 deleteBTN_doc d-none" data-document="title_deed_file"  data-input="title_deed_password" >  <button type="button" class="btn btn-outline-danger dlt-btn btn-sm waves-effect waves-light" title="View Password"> <i class="mdi mdi-delete-outline"></i> </button> </a>
                                <a href="javascript:void(0)" type="button" class="viewPassword  d-none"> <button type="button" class="btn btn-outline-success btn-sm waves-effect waves-light" title="View Password" id="addPassword_title_deed"> <i class="mdi-eye-arrow-left-outline mdi" ></i></button></a>
                                <span class="d-none password">| <i class="mdi mdi-form-textbox-password"></i>: ${data.title_deed.password}</span>`
                            )
                        }

                        if (data.owner_passport_copy) {
                            document_count++;

                            $('#owner_passport_copy_password').val(data.owner_passport_copy.password);
                            $("#owner_passport_copy_file").html(

                                `<a href="${data.owner_passport_copy.path}" target="_blank"><button type="button" class="btn btn-outline-primary btn-sm waves-effect waves-light" title="View Document"><i class="mdi mdi-file-eye"></i></button></a>
                                <a href="javascript:void(0)" type="button" data-id="${data.owner_passport_copy.id}" class="mt-1 deleteBTN_doc d-none" data-input="owner_passport_copy_password" data-document="owner_passport_copy_file" >  <button type="button" class="btn btn-outline-danger dlt-btn btn-sm waves-effect waves-light" title="View Password"> <i class="mdi mdi-delete-outline"></i> </button> </a>
                                <a href="javascript:void(0)" type="button" class="viewPassword  d-none"> <button type="button" class="btn btn-outline-success btn-sm waves-effect waves-light" title="View Password" id="addPassword_owner_passport_copy_file"> <i class="mdi-eye-arrow-left-outline mdi" ></i></button></a>
                                <span class="d-none password">| <i class="mdi mdi-form-textbox-password"></i>: ${data.owner_passport_copy.password}</span>`
                            )
                        }

                        if (data.signed_listing_form) {

                            document_count++;
                            var password = data?.signed_listing_form?.password !== null ? data
                                .signed_listing_form.password : '';

                            $('#signed_listing_form_password').val(data.signed_listing_form.password);
                            $("#signed_listing_form_file").html(
                                `<a href="${data.signed_listing_form.path}" target="_blank"><button type="button" class="btn btn-outline-primary btn-sm waves-effect waves-light" title="View Document"><i class="mdi mdi-file-eye"></i></button></a>
                                <a href="javascript:void(0)" type="button" data-id="${data.signed_listing_form.id}" class="mt-1 deleteBTN_doc d-none" data-document="signed_listing_form_file"   data-input="signed_listing_form_password" > <button type="button" class="btn btn-outline-danger dlt-btn btn-sm waves-effect waves-light" title="View Password"> <i class="mdi mdi-delete-outline"></i> </button> </a>
                                <a href="javascript:void(0)" type="button" class="viewPassword d-none"> <button type="button" class="btn btn-outline-success btn-sm waves-effect waves-light d-none" style="display:none" title="View Password" id="addPassword_signed_listing_form_files"> <i class="mdi-eye-arrow-left-outline mdi" ></i></button></a>
                                <span class=" password d-none"><i class="mdi mdi-form-textbox-password d-none"></i>: ${data.signed_listing_form.password}</span> <label class="ms-2 form-label">Password<span id="makeRequired"></span></label> <input placeholder="Password" type="text" class="form-control form-control-disable inputs" style="border:1px solid #ced4da;width:50%!important;display: inline-flex;"  name="signed_listing_form_password" id="signed_listing_form_password" value="${password}">`
                            )
                        } else {
                            $("#signed_listing_form_file").html(
                                `<span class=" password d-none"><i class="mdi mdi-form-textbox-password d-none"></i>:</span> <label class="ms-2 mt-2 form-label">Password <span id="makeRequired"></span></label> <input placeholder="Password" type="text" class="mt-2 form-control inputs" style="border:1px solid #ced4da;width:50%!important;display: inline-flex;"  name="signed_listing_form_password" id="signed_listing_form_password" value="">`
                            );
                        }
                        let otherDocuments = "";
                        let disabled = mode == "view" ? "disabled" : "";
                        if (disabled) {
                            $("#add-more-documents-btn").hide();
                        } else {
                            $("#add-more-documents-btn").show();
                        }

                        let documentLength = $(".other_documents").length;
                        // alert(mode)

                        if (mode == 'view') {
                            data.other_documents?.map(item => {
                                documentLength++;
                                document_count++;



                                otherDocuments += `

                            <div class="row">
                                <div class="col-lg-4 mt-2">
                                    <label for="title_deed" class="form-label">${item.title}</label>
                                </div>
                                <div class="col-lg-8 mt-2">

                                <div id="title_deed_filess" class="extraDoc_id_${item.id}">
                                <a href="${item.path}" target="_blank"><button type="button" class="btn btn-outline-primary btn-sm waves-effect waves-light" title="View Document"><i class="mdi mdi-file-eye"></i></button></a>
                                <a href="javascript:void(0)" type="button" data-id="${item.id}" class="mt-1 deleteBTNExtraDoc d-none" data-document="signed_listing_form_file"   data-input="signed_listing_form_password" > <button type="button" class="btn dlt-btn btn-outline-danger btn-sm waves-effect waves-light" title="View Password"> <i class="mdi mdi-delete-outline"></i> </button> </a>
                                <a href="javascript:void(0)" type="button" class="viewPassword d-none"><button type="button" class="showPasswordExtraDoc btn btn-outline-success btn-sm waves-effect waves-light" title="View Password" id=""> <i class="mdi-eye-arrow-left-outline mdi" ></i></button></a>
                                <span class="password d-none"  data-input="other_documents-password-${documentLength}-password" data-name="${item.title}">${item?.password?item?.password:''}</span>
                                </div>


                                </div>
                            </div>




                            `
                            })


                        } else if (mode == 'edit') {
                            data.other_documents?.map(item => {
                                documentLength++;
                                document_count++;



                                otherDocuments += `<div class="col-lg-4 mt-2 extraDoc_id_${item.id}">
                                            <input type="text" id="other_documents-${documentLength}-title" name="other_documents[${documentLength}][title]"
                                                   class="form-control doc-disabled" placeholder="Enter document titlte" value="${item.title}" ${disabled}>
                                            <input type="hidden" name="other_documents_old_id[${documentLength}][old_id]" value="${item.id}">
                                        </div>
                                        <div class="col-lg-8 mt-2 other_documents-${documentLength}  extraDoc_id_${item.id}">
                                            <input type="file" id="other_documents-${documentLength}-file" name="other_documents[${documentLength}][file]"
                                                   class="form-control other_documents doc-disabled" accept="application/pdf,image/png, image/jpeg" ${disabled}>
                                                <div class="d-none"><a href="${item.path}" target="_blank">View Document</a></div>

                                            <input type="hidden" id="other_documents-password-${documentLength}-password" name="other_documents[${documentLength}][password]"
                                                   class="form-control inputs other_documents  extraDoc_id_${item.id}" value="${item?.password?item?.password:''}">
                                                <div class="mt-1 d-none"><p href="${item.path}" target="_blank">Password: ${item.password}</p></div>

                                                <div id="title_deed_filess" class="extraDoc_id_${item.id}" >

<a href="${item.path}" target="_blank"><button type="button" class="btn btn-outline-primary btn-sm waves-effect waves-light" title="View Document"><i class="mdi mdi-file-eye"></i></button></a>
<a href="javascript:void(0)" type="button" data-id="${item.id}" class="mt-1 deleteBTNExtraDoc d-none" data-document="signed_listing_form_file"   data-input="signed_listing_form_password" > <button type="button" class="btn btn-outline-danger btn-sm waves-effect waves-light" title="View Password"> <i class="mdi mdi-delete-outline"></i> </button> </a>
<a href="javascript:void(0)" type="button" class="viewPassword d-none"><button type="button" class="showPasswordExtraDoc btn btn-outline-success btn-sm waves-effect waves-light d-none" title="View Password" id=""> <i class="mdi-eye-arrow-left-outline mdi" ></i></button></a>
<span class="password d-none" data-input="other_documents-password-${documentLength}-password" data-name="${item.title}">${item?.password?item?.password:''}</span>
</div>
                                        </div>`
                            })
                        }





                        let folderMedia = "";

                        data.folder_media?.map(item => {
                            document_count++;
                            otherDocuments += `<div class="row">
                                        <div class="col-lg-4 mt-2">
                                            <label for="title_deed" class="form-label">${item.title}</label>
                                        </div>
                                        <div class="col-lg-8 mt-2">
                                        <div id="title_deed_filess" class="extraDoc_id_${item.id}">
                                            <a href="${item.path}" target="_blank"><button type="button" class="btn btn-outline-primary btn-sm waves-effect waves-light" title="View Document"><i class="mdi mdi-file-eye"></i></button></a>
                                            <a href="javascript:void(0)" type="button" data-id="${item.id}" class="mt-1 deleteBTNExtraDoc d-none" data-document="signed_listing_form_file"   data-input="signed_listing_form_password" > <button type="button" class="btn btn-outline-danger btn-sm waves-effect waves-light" title="View Password"> <i class="mdi mdi-delete-outline"></i> </button> </a>
                                        </div>
                                        </div>
                                    </div>`;

                        })

                        $("#document_counter").text(document_count);
                        $("#more-documents").html(otherDocuments);



                    },
                    error: function(result) {
                        console.log(result);
                    }
                });
            }
        }


        $.fn.resetForm = function() {
            $(this).each(function() {
                this.reset();
                $('input[type=file]', this).val('');
            });
        }


        function resetDocumentLinks() {
            $("#title_deed_file").html("");
            $("#owner_passport_copy_file").html("");
            $("#signed_listing_form_file").html("");
            $("#floor_plan_file").html("");
        }




        function toggleDocumentInput(isShow) {

            if (isShow) {
                $('.other_documents').show();
                $("#title_deed").show();
                $("#owner_passport_copy").show();
                $("#signed_listing_form").show();

                // $("#title_deed_password").show();
                // $("#owner_passport_copy_password").show();
                // $("#signed_listing_form_password").show();


            } else {
                $('.other_documents').hide();
                $("#title_deed").hide();
                $("#owner_passport_copy").hide();
                $("#signed_listing_form").hide();

                // $("#title_deed_password").hide();
                // $("#owner_passport_copy_password").hide();
                // $("#signed_listing_form_password").hide();
            }

        }

        function renderCreatedUpdated(data) {
            $("#view-edit-mode").removeClass("d-none");
            if (data.created_by) {
                $('#created_by_span').text(data.created_by.first_name + ' ' + moment(data.created_at).format(
                    'DD-MM-YYYY hh:mm:ss'))
            }
            if (data.updated_by) {
                $('#updated_by_span').text(data.updated_by.first_name + ' ' + moment(data.updated_at).format(
                    'DD-MM-YYYY hh:mm:ss'))
            }
            //$("#view-edit-mode>.card-body").html(div);
        }

        function getStateBadge(status) {
            if (status == 'PENDING') {
                return `<span class="badge badge-pill bg-warning" data-key="t-hot">Pending</span>`;
            } else if (status == 'APPROVED') {
                return `<span class="badge badge-pill bg-success" data-key="t-hot">Approved</span>`;
            } else if (status == 'REFUSED') {
                return `<span class="badge badge-pill bg-danger" data-key="t-hot">Refused</span>`;
            } else {
                return '-';
            }
        }
        // Meta data showing from here
        $(".metaDataBtn").click(function() {
            let property_id = $(this).data("property-id");
            let portal_slug = $(this).data("portal-slug");
            let container = $(this).data("bs-target");
            let collapsed = $(this).hasClass('collapsed');
            if (!collapsed) {
                let html = `<div style="width:100%; text-align:center;"><div class="spinner-border text-info text-center" role="status">
                                <span class="sr-only">Loading...</span>
                            </div></div>`;
                $(container).html(html);
                $.ajax({
                    url: "{{ route('ajax.getPfMetaHtml') }}",
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        property_id,
                        portal_slug
                    },
                    success: function(result) {
                        if (result.status == '1') { //if successfully found the data
                            $(container).html(result.metaHtml);

                        } else { // something went wrong on backend

                            //$(container).html('');
                            $(container).html(
                                '<div class="alert alert-danger alert-dismissible fade show mb-xl-0 p-1" role="alert">Not data found!</div>'
                            );
                        }
                    },
                    error: function(jqXhr, json, errorThrown) {
                        $(container).html(
                            '<div class="alert alert-danger alert-dismissible fade show mb-xl-0 p-1" role="alert">Not data found!</div>'
                        );
                        let data = jqXhr.responseJSON;
                        showToastify(data.msg, "error");
                    }
                });
            }
        });




        function viewProperty(id) {
            $("#refuseRevisionChanges").val('');
            $('#screen_loader').removeClass('d-none');
            $('.bulkDownload').addClass('d-none');

            $("#viewPendingChanges").addClass('d-none');
            // $("#actionButton").addClass('d-none');


            $("#publish-request-button").closest("li").addClass("d-none")
            $(this).closest("tr").addClass("table-active")

            $("#unpublish-request-button").closest("li").addClass("d-none")
            $('#price_on_application').prop("disabled", true)
            @can('Properties View')
                isUpdate = true;
                fullyLoaded = false;
                checkChanges = false;
                $("#addContact").addClass("d-none")
                $("#id").val(id)
                getProperty(id)
                $(".inputs").prop("disabled", true)
                $("#refered_by").attr("disabled", true)
                $("#temp_id").val("")

                setTimeout(function() { //calls click event after a certain time
                    getPhotos();
                }, 800);

                // jquery timeout function

                getPropertyDocuments()

                getPropertyNotes()
                mode = 'view';
                $("#saveBtn, #savePublishBtn, #newBtn, #multiDeleteBtn").addClass("d-none");
                // alert($('#publish_status').find(":selected").val())
                //


                $(".doc-disabled").prop("disabled", true);
                // Not used Code
                // if ($('#publish_status').val() == 'DRAFT') {
                //     $("#actionButton").removeClass("d-none");

                // } else {
                //     $("#actionButton").addClass("d-none");
                // }
                // Not Used Code
                let timeInterval = setInterval(() => {
                    if (fullyLoaded) {
                        $("#editBtn, #closeBtn, #deleteBtn, #dropdownMenuLink1,#archiveBtn").removeClass("d-none");
                        $('#archiveBtn').attr('data-id', id);
                        clearInterval(timeInterval);

                        toggleDocumentInput(false);
                        $('.form-control-sm-disable').prop("disabled", true)
                    }
                }, 500);
            @endcan
            $('#screen_loader').addClass('d-none');

        }

        function viewEvent(id) {
            showEventViewModal(id);
        }

        function viewEvents(key, id, type = 'All') {
            $.ajax({
                url: "{{ route('admin.listing.properties.getEvents') }}",
                type: 'GET',
                dataType: 'json',
                data: {
                    key: key,
                    id: id,
                    type: type
                },
                success: function(result) {

                    $('#eventsLog').removeClass('d-none');
                    let logs = "";
                    let html = '<ul class="list-group list-group-flush border-dashed">';

                    let data = result?.data;
                    if (typeof data !== 'undefined' && data.length > 0) {

                        data.map(item => {
                            // let date = moment(item.created_at).fromNow()
                            // let start_time = moment(item.start_time).fromNow()
                            // let end_time = moment(item.end_time).fromNow()
                            let date = item.start_time != null ? moment(item?.start_time).format(
                                "DD MMM YYYY") : "";
                            let start_time = item.start_time != null ? moment(item.start_time).format(
                                "hh:mm A") : "";
                            let end_time = item.end_time != null ? moment(item.end_time).format(
                                    "hh:mm A") :
                                "";

                            let start_date_date = item.start_time ? moment(item.start_time).format(
                                "DD") : "";

                            let start_date_day = item.start_time ? moment(item.start_time).format(
                                "ddd") : "";


                            if (moment().isSame(moment(item.start_time), 'day')) {
                                dateColor = 'bg-soft-danger';
                            } else if (moment() < moment(item.start_time)) {
                                dateColor = 'bg-soft-warning';
                            } else {
                                dateColor = 'bg-light';

                            }


                            html += `
                            <li class="list-group-item ps-0 cursor-pointer event${item.id}" onclick="viewEvent('${item.id}')">
                                <div class="row align-items-center g-3">
                                    <div class="col-auto">
                                        <div class="avatar-sm p-1 py-2 h-auto bg-light ${dateColor} rounded-3" title="${date}">
                                            <div class="text-center">
                                                <h5 class="mb-0">${start_date_date || ""}</h5>
                                                <div class="text-muted">${start_date_day || ""}</div>
                                            </div>
                                        </div>
                                        <div class="event-type">
                                                    <span class="badge ${item?.type?.badge}">${item?.type?.name}</span>
                                                </div>
                                    </div>
                                    <div class="col">
                                        <h5 class="text-muted mt-0 mb-1 fs-13">${start_time || ""} - ${end_time || ""}</h5>
                                        <a href="javascript:;" class="text-reset fs-14 mb-0">${item.title}</a>
                                    </div>

                                </div>
                            </li>`;
                            $("#upcoming-event-list").html(html);
                        })

                    } else {
                        $('#upcoming-event-list').html(`<div class="card-body">
                                    <div class="d-flex mb-2 text-center">
                                        <div class="flex-grow-1">
                                            <lord-icon src="{{ asset('assets/json/icons/common-event.json') }}" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:125px;height:125px"> </lord-icon>
                                        </div>


                                    </div>
                                    <h3 class="mb-2 text-center"><span class="counter-value text-muted text-center" data-target="97685">No Events</span><small class="text-muted fs-13"></small></h3>
                                    <div class="text-center"><a href="javascript:void(0)" class="btn btn-primary btn-sm text-center" onclick="addEvent('Viewing')">Add Event</a></div>
                                    <h6 class="text-muted mb-0"></h6>
                                </div>`);

                    }

                    //$("#upcoming-event-list").html(html);

                    let element = document.getElementById("activities");
                    element.scrollTop = element.scrollHeight

                    fullyLoaded = true;


                },
                error: function(result) {

                    console.log(result);
                }
            });
        }





        $('#archiveBtn').click(function() {
            let id = $(this).attr('data-id');
            $('#archive_lead_id').val(id);
            $('#archiveModal').modal('show');
        })
        $("#refuseProperty").click(function() {
            $("#refuseReason").val("");
            $("#refuseRevisionChanges").val("");
            $("#refusePropertyModal").modal("show");
        });

        $("#approveProperty").click(function() {
            $('#duplicated-property-check').modal('show');
        })



        $("#publish-request-btn").click(function() {
            $('#approveProperty').prop('disabled', true);
            let propertyId = $("#id").val();
            console.log(propertyId)
            let duplicate = false;
            if ($('#is_duplicated').is(':checked')) {
                duplicate = true;
            }

            $.ajax({
                url: "{{ route('admin.listing.properties.approveProperty') }}",
                type: 'POST',
                dataType: 'json',
                data: {
                    id: propertyId,
                    duplicate: duplicate
                },
                cache: false,
                success: function(result) {
                    $('#duplicated-property-check').modal('hide');
                    if (result.status == '1') { //if successfully deleted
                        showToastify(result.msg, "success")
                        $("#approveProperty").addClass("d-none");
                        getProperty(propertyId);
                        dataTable.ajax.reload(null, false);
                    } else { // something went wrong on backend
                        showToastify(result.msg, "error")
                    }
                    $('#approveProperty').prop('disabled', false);
                    getPropertyNotes()

                },
                error: function(result) {
                    console.log(result);
                    $('#approveProperty').prop('disabled', false);
                }
            });
        });

        @can('Properties Refuse')
            $(document).on("submit", "#refusePropertyForm", function(event) {
                event.preventDefault();
                $('#refusePropertyButton').prop('disabled', true);
                $('#refuseBtn').addClass('d-none')
                $('#refuseLoading').removeClass('d-none')
                let propertyId = $("#id").val();
                let refuseRevision = $("#refuseRevisionChanges").val();
                var formData = new FormData($(this)[0]);
                formData.append("id", propertyId);
                if (refuseRevision) {
                    let approval_ids = $("#pendingDataId").val();
                    let action = 'rejected';
                    formData.append("approval_ids", approval_ids);
                    formData.append("action", action);
                    formData.append("id", propertyId);
                    actionOnPendingData(formData);
                } else {
                    $.ajax({
                        url: "{{ route('admin.listing.properties.refuseProperty') }}",
                        type: 'POST',
                        dataType: 'json',
                        data: formData,
                        cache: false,
                        processData: false,
                        contentType: false,
                        success: function(result) {
                            if (result.status == '1') { //if successfully deleted
                                showToastify(result.msg, "success")
                                $("#refusePropertyModal").modal("hide");
                                $("#refuseProperty").addClass("d-none");
                                getProperty(propertyId);
                                dataTable.ajax.reload();
                            } else { // something went wrong on backend
                                showToastify(result.msg, "error")
                                $("#refusePropertyModal").modal("hide");
                            }
                            $('#refusePropertyButton').prop('disabled', false);
                            $('#refuseBtn').removeClass('d-none')
                            $('#refuseLoading').addClass('d-none')
                            getPropertyNotes()

                        },
                        error: function(result) {
                            console.log(result);
                            $('#refusePropertyButton').prop('disabled', false);
                            $('#refuseBtn').removeClass('d-none')
                            $('#refuseLoading').addClass('d-none')

                        }
                    });
                }
            });
        @endcan

        function editData(id) {
            @can('Properties Edit')
                $('.bulkDownload').addClass('d-none');
                $("#temp_id").val("");
                $("#addContact").removeClass("d-none")
                isUpdate = true;
                fullyLoaded = false;
                mode = 'edit';

                getProperty(id);

                getPhotos();

                toggleDocumentInput(true);
                getPropertyNotes()

                $('.form-control-sm').prop("disabled", false)

                // toggleDocumentInput(true);
                @can('Properties Status Change')
                    $("#property_status_id").prop("disabled", false)
                @else
                    $("#property_status_id").prop("disabled", true)
                @endcan
                @can('Properties Publish Status Change')
                    $("#publish_status").prop("disabled", false)
                @else
                    $("#publish_status").prop("disabled", true)
                @endcan

                getPropertyDocuments()
                $("#newBtn, #editBtn, #deleteBtn, #hardDeleteBtn #multiDeleteBtn, #dropdownMenuLink1").addClass("d-none");
                $('#savePublishBtn').removeClass('d-none');
                let timeInterval = setInterval(() => {
                    if (fullyLoaded) {
                        $('.deleteBTN_doc').removeClass('d-none');
                        $('.deleteBTNExtraDoc').removeClass('d-none');

                        $('.addPasswordBtn').removeClass('d-none');
                        $("#saveBtn, #closeBtn").removeClass("d-none");

                        if ($('#publish_status').val() == 'DRAFT' || $('#publish_status').val() == "POCKET" || $(
                                '#publish_status').val() == "REFUSED") {
                            $("#actionButton").removeClass("d-none");
                        } else {
                            $("#actionButton").addClass("d-none");
                        }

                        if ($('#publish_status option:selected').val() == 'UNPUBLISHED') {
                            $('#unit_no').attr('readonly', true);
                        }

                        clearInterval(timeInterval);
                        checkChanges = true;
                    }
                }, 500);
                @if (auth()->user()->hasRole('Agent'))
                    $('.portalBtns').attr('disabled', true);
                @else
                    $('.portalBtns').attr('disabled', false);
                @endif
            @endcan
        }
        userRole = "{{ auth()->user()->roles[0]->name }}";


        $(document).on('click', '#deleteBtn', function(event) {
            Swal.fire({
                html: '<div class="mt-3"><lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon><div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you Sure ?</h4><p class="text-muted mx-4 mb-0">Are you Sure You want to Delete this Listing ?</p></div></div>',


                showCancelButton: true,
                cancelButtonText: 'Cancel',
                cancelButtonClass: 'btn btn-warning action-close w-xs',

                showCloseButton: true,

                showDenyButton: (userRole === 'Super Admin'),
                denyButtonClass: 'btn btn-danger  w-xs me-2 hardDelete',
                denyButtonText: 'Hard Delete',

                confirmButtonText: 'Delete',
                confirmButtonClass: 'btn btn-danger action-delete  w-xs me-2',

                buttonsStyling: false,

            }).then(function(result) {
                if (result.isConfirmed) {
                    // alert($('#id').val());
                    deleteData($('#id').val(), false);
                    // Swal.fire({
                    //     title: 'Deleted!',
                    //     icon: 'success',
                    //     confirmButtonClass: 'btn btn-primary w-xs',
                    //     buttonsStyling: false,
                    // })
                } else if (result.isDenied) {
                    // alert($('#id').val());
                    deleteData($('#id').val(), true);
                    // Swal.fire({
                    //     title: 'Hard Deleted',
                    //     icon: 'info',
                    //     confirmButtonClass: 'btn btn-primary w-xs',
                    //     buttonsStyling: false,
                    // })
                }
            })

            // $('#deleteBtn').click(function() {
            //     $('.hardDelete').addClass('d-none');
            // })


            @can('Properties Delete')
                // alert($('#id').val())
                $('#listing_id').val($('#id').val());
                // $('#deleteModal').modal('show');
            @endcan
        });

        $(document).on('click', '#hardDeleteBtn', function(event) {
            @can('Properties Delete')
                deleteData($('#id').val(), true);
            @endcan
        });


        $(document).on('click', '.delete-link', function(event) {
            @can('Properties Delete')
                let id = $(this).data('id');
                deleteData(id, false);
            @endcan
        });


        $(document).on('click', '#btn_pics_fullscreen', function(event) {
            console.log('yes');
            $('#imgFullDiv').toggleClass('fullscreen');
            $('.photo-element').toggleClass('photo-element-bg-screen');

            //$('.scrollable-gallery').css('height',  $('.scrollable-gallery').css('height')=='345px'?'600px':'345px');
        });


        $(document).on('click', '#btn_features_fullscreen', function(event) {
            $('#featureFullDiv').toggleClass('fullscreen');
            $(".single-feature").toggleClass("col-6 col-3");
            //$('.scrollable-features').css('height',  $('.scrollable-features').css('height')=='129px'?'600px':'129px');
        });

        $(document).on('click', '#btn_description_fullscreen', function(event) {
            console.log('yes');
            $('#descriptionFullDiv').toggleClass('fullscreen');
            $('#description').attr('rows', $('#description').attr('rows') == 10 ? 30 : 10);
        });





        function deleteData(id, hard) {
            @can('Properties Delete')
                // alert(id+' '+hard);
                $.ajax({
                    url: "{{ route('admin.temp-listing.properties.delete') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        id,
                        hard
                    },
                    success: function(result) {
                        if (result.status == '1') { //if successfully deleted
                            showToastify(result.msg, "success")
                            dataTable.row().order([0, 'DESC']).draw();
                            toggleForm(false)
                            // redraw the datatable show we don't need to reload the page to see the changes
                            $('#viewModal').modal('hide');
                        } else { // something went wrong on backend
                            showToastify(result.msg, "error")
                        }
                    },
                    error: function(result) {
                        console.log(result);
                    }
                });


                // Swal.fire({

                //             html: '<div class="mt-3"><lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon><div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you Sure ?</h4><p class="text-muted mx-4 mb-0">Are you Sure You want to Delete this Record?</p></div></div>',
                //                 showCancelButton: !0,
                //                 confirmButtonClass: "btn btn-danger w-xs me-2 mb-1",
                //                 confirmButtonText: "Yes, Delete Record",
                //                 cancelButtonClass: "btn btn-soft-success w-xs mb-1",
                //                 buttonsStyling: !1,
                //                 showCloseButton: !0
                //         })
                //         .then(function(t) {
                //               if (t.isConfirmed) { // on confirm delete the category
                //               $.ajax({
                //                     url: "{{ route('admin.listing.properties.delete') }}",
                //                     type: 'POST',
                //                     dataType: 'json',
                //                     data: {
                //                         id,
                //                         hard
                //                     },
                //                     success: function(result) {
                //                         if (result.status == '1') { //if successfully deleted
                //                             showToastify(result.msg, "success")
                //                             dataTable.row().order([10, 'DESC']).draw();
                //                             toggleForm(false)
                //                                  // redraw the datatable show we don't need to reload the page to see the changes
                //                             $('#viewModal').modal('hide');
                //                         } else { // something went wrong on backend
                //                             showToastify(result.msg, "error")
                //                         }
                //                     },
                //                     error: function(result) {
                //                         console.log(result);
                //                     }
                //                 });
                //           }
                //         })
            @endcan
        }

        $('#uploadPhotosModal').on('hidden.bs.modal', function(e) {
            getPhotos()
        })
        let photoCounter = 0;



        function getPhotos() {
            $('.downloadWithWaterMark').addClass('d-none');
            $('.downloadBulkwithoutWatermark').addClass('d-none');

            let temp_id = $("#temp_id").val();
            let property_id = $("#id").val();
            photoCounter = 0;
            $("#photo_counter").text(photoCounter);
            galleryArr = [];
            if (temp_id !== "" || property_id !== "") {
                $.ajax({
                    url: "{{ route('ajax.temp-property-photos') }}",
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        temp_id,
                        property_id
                    },
                    success: function(result) {
                        let photos = "";
                        let options = "";
                        let delete_media = "";
                        result.data?.map((item) => {
                            photoCounter++;
                            galleryArr.push(item.id);
                            if (own_property) {
                                delete_media = `
                                @can('Bulk Delete Images')
              <div class="photo-checkbox">
                <input type="checkbox" value="${item.id}" name="media_id[]" class="delete_media_ids">
              </div>
              @endcan
              @can('Delete Media')
              <a href="javascript:void(0)" type="button" data-id="${item.id}" class="mt-1 delete-photo">Delete</a>
            @endcan`;
                                $('.own_property').removeClass('d-none');
                                $('.downloadWithWaterMark').removeClass('d-none');
                                $('.downloadBulkwithoutWatermark').removeClass('d-none');
                            } else {
                                $('.own_property').addClass('d-none');
                                $('.downloadWithWaterMark').removeClass('d-none');
                                $('.downloadBulkwithoutWatermark').removeClass('d-none');

                            }
                            photos += `<div class="photo-element"  @can('Change Media Order') data-id="${item.id}"  @endcan>
            <div class="text-center">
              <div class="image-wrapper">
                <a href="${item.path}" data-lightbox="listing-gallery">
                  <img height="100%" width="100%" src="${item.thumbnail}" alt="">
                </a>
              </div>
              ${delete_media}
            </div>
          </div>`;
                            options +=
                                `<option value="${item.id}" data-image="${item.thumbnail }">${item.id}</option>`;


                        });
                        if (photoCounter > 0) {
                            $('.bulkDownload').removeClass('d-none');
                        }
                        $("#photos-gallery").html(photos);
                        $("#photo_counter").text(photoCounter);
                        checkPermission();
                        $("#image_1_iframe").html(options);
                        $("#image_3_iframe").html(options);
                        $("#image_2_iframe").html(options);
                        $("#image_4_iframe").html(options);
                        $("#image_5_iframe").html(options);
                        $("#image_6_iframe").html(options);
                        $("#image_7_iframe").html(options);
                        $("#image_8_iframe").html(options);



                        $('#image_1_iframe,#image_2_iframe ,#image_3_iframe,#image_4_iframe,#image_5_iframe,#image_6_iframe,#image_7_iframe,#image_8_iframe')
                            .select2({
                                placeholder: 'image_1_iframe agents',
                                allowClear: true,
                                closeOnSelect: true,
                                templateResult: formatImageOptions,
                                templateSelection: formatImageOptions,
                            });
                        //#image_1_iframe select 1st option
                        $("#image_1_iframe").val($("#image_1_iframe option:first").val()).trigger("change");

                        $("#image_2_iframe").val($("#image_2_iframe option:nth-child(2)").val()).trigger(
                            "change");

                        $("#image_3_iframe").val($("#image_3_iframe option:nth-child(3)").val()).trigger(
                            "change");

                        $("#image_4_iframe").val($("#image_4_iframe option:nth-child(1)").val()).trigger(
                            "change");
                        $("#image_5_iframe").val($("#image_5_iframe option:nth-child(1)").val()).trigger(
                            "change");
                        $("#image_6_iframe").val($("#image_6_iframe option:nth-child(2)").val()).trigger(
                            "change");
                        $("#image_7_iframe").val($("#image_7_iframe option:nth-child(3)").val()).trigger(
                            "change");
                        $("#image_8_iframe").val($("#image_7_iframe option:nth-child(4)").val()).trigger(
                            "change");

                        if ($('.photo-element').length < 3) {
                            $('.showCriteria').text('( 3 images Req ) ')
                            $('.printables').css('pointer-events', 'none');
                        } else {
                            $('.showCriteria').text('')
                            $('.printables').css('pointer-events', 'auto');
                        }

                        if ($('#publish_status').val() == 'PENDING') {
                            @if (auth()->user()->hasRole('Agent'))
                                $('.delete_media_ids').prop('disabled', true);
                                $('.delete-photo').addClass('d-none');
                            @endif
                        } else {
                            $('.delete_media_ids').prop('disabled', false);
                            $('.delete-photo').removeClass('d-none');
                        }

                        @if ((auth()->user()->region_id == 2 || auth()->user()->parent_id == 699) && auth()->user()->hasRole('Agent'))
                            $('.delete-photo').addClass('d-none');
                        @endif


                    },
                    error: function(result) {
                        console.log(result);
                        $("#photos-gallery").html("");
                    }
                });
            }
        }

        function formatImageOptions(e) {
            let img = $(e.element).data('image');
            let onlineClass = "online"

            return e.id ? $('<div class="d-flex align-items-center"><div class="flex-shrink-0 chat-user-img ' +
                onlineClass + ' align-self-center me-2 ms-0"><div class="avatar-xxs mb-2"> <img src="' + img +
                '" class="img-fluid userprofile"" alt=""></div></div><div class="flex-grow-1 overflow-hidden"><p class="text-truncate mb-0">' +
                e.text + '</p></div></div>') : e.text;
        }

        $(document).on('change', '.size-input', function(e) {
            // check if #size-converter is checked or not if checked then convert the value to sqft
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
            // check if #size-converter is checked or not if checked then convert the value to sqft
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


        // Code added from Leads Module for Bulk Assign
        $(document).on('change', '#checkAll', function(e) {
            dataTable.$('tr').toggleClass('selected');
            @if (!auth()->user()->hasRole('Agent'))
                updateMultiOption();
            @endif
        });
        @can('Properties Assign Agents')
            $(document).on('click', '#bulk_reassign_btn', function() {
                $("#bulkReassignPropertiesModal").modal({
                    backdrop: 'static',
                    keyboard: false
                })
                $('#bulk_properties_agent_id').val('').trigger('change');
                $('#bulkReassignPropertiesModal').modal('show');
            });
            $('#bulk_properties_agent_id').select2({
                placeholder: 'Select agents',
                allowClear: true,
                templateResult: formatAgent,
                templateSelection: formatAgent,
                dropdownParent: $('#bulkReassignPropertiesModal')
            });
        @endcan
        @if (!auth()->user()->hasRole('Agent'))
            function updateMultiOption() {
                var countCheckedCheckboxes = $('input[name="chk_child"]:checked').length;
                console.log(countCheckedCheckboxes);
                $('#bulk_properties_count').html(countCheckedCheckboxes);
                $('#count_selected').html(countCheckedCheckboxes);
                {{-- if (countCheckedCheckboxes >= 1) {
                    if ($('.multi_options').length === 0) {
                        $(`<div class="multi_options" style="display:inline-block; margin-left:10px;">
                          <div class="btn-group">
                            <button class="btn btn-primary btn-sm dropdown-toggle action-bulk" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Bulk Actions
                            </button>
                            <div class="dropdown-menu">
                                 @can('Properties Delete')
                                 <a class="dropdown-item" href="javascript:;" onclick="deleteMultiple()">Delete</a>
                                 @endcan
                            </div>
                        </div>
                   </div>`).insertAfter('.dt-buttons');
                    }
                } else {
                    $('.multi_options').remove();
                } --}}
                // To enable bulk assign use the below code instead of above
                if (countCheckedCheckboxes >= 1) {
                    if ($('.multi_options').length === 0) {
                        $(`<div class="multi_options" style="display:inline-block; margin-left:10px;">
                          <div class="btn-group">
                            <button class="btn btn-primary btn-sm dropdown-toggle action-bulk" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Bulk Actions
                            </button>
                            <div class="dropdown-menu">
                                
                                 @can('Properties Delete')
                                 <a class="dropdown-item" href="javascript:;" onclick="deleteMultiple()">Delete</a>
                                 @endcan
                            </div>
                        </div>
                   </div>`).insertAfter('.dt-buttons');
                    }
                } else {
                    $('.multi_options').remove();
                }
            }
        @endif
        @can('Properties Assign Agents')
            // Agent Re assign commented on Hishmat's instruction
            $(document).on("click", "#bulkPropertiesSubmitBtn", function() {

                let selected_agent_bulk = $('#bulk_properties_agent_id').val();

                if (selected_agent_bulk == "") {
                    showToastify("Please select agent.", "error")
                    return;
                } else {
                    if (ids_array = [], document.getElementsByName("chk_child").forEach((function(e) {
                            if (1 == e.checked) {
                                var t = e.value;
                                ids_array.push(t)

                            }
                        })), "undefined" != typeof ids_array && ids_array.length > 0) {
                        $.ajax({
                            url: "{{ route('admin.listing.properties.bulk.assign.agents') }}",
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                ids: ids_array,
                                agent_id: selected_agent_bulk
                            },
                            success: function(result) {
                                if (result.status == '1') {
                                    showToastify(result.msg, "success")
                                    $("#bulkReassignPropertiesForm").trigger("reset")
                                    $("#bulkReassignPropertiesModal").modal("hide")
                                    $("#checkAll").prop("checked", this.checked)
                                    document.getElementById("checkAll").checked = !1
                                    $('.multi_options').remove();
                                    dataTable.row().draw();
                                } else {
                                    showToastify(result.msg, "error")
                                }
                            },
                            error: function(jqXhr, json, errorThrown) {
                                let data = jqXhr.responseJSON;
                                showToastify(data.msg, "error");
                            }
                        });
                    }

                    // Ajax Form goes here
                }


            })
        @endcan
        // Leads Code End here
        function deleteMultiple() {
            @can('Bulk Delete Listings')
                let ids = [];
                $(".selectable-checkbox").map((index, item) => {
                    item.checked ? ids.push(item.value) : []
                })
                if (ids.length > 0) {

                    Swal.fire({

                            html: '<div class="mt-3"><lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon><div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you Sure ?</h4><p class="text-muted mx-4 mb-0">Are you Sure You want to Delete ' +
                                ids.length + ' Record(s)?</p></div></div>',
                            showCancelButton: !0,
                            confirmButtonClass: "btn btn-danger w-xs me-2 mb-1",
                            confirmButtonText: "Yes, Delete  " + ids.length + " Record(s)",
                            cancelButtonClass: "btn btn-soft-success w-xs mb-1",
                            buttonsStyling: !1,
                            showCloseButton: !0
                        })
                        .then(function(t) {
                            if (t.isConfirmed) { // on confirm delete the category
                                $.ajax({
                                    url: "{{ route('admin.listing.properties.multi.delete') }}",
                                    type: 'POST',
                                    dataType: 'json',
                                    data: {
                                        ids
                                    },
                                    success: function(result) {
                                        if (result.status == '1') { //if successfully deleted
                                            showToastify(result.msg, "success")
                                            dataTable.row().draw();
                                            // redraw the datatable show we don't need to reload the page to see the changes
                                            $('#viewModal').modal('hide');
                                        } else { // something went wrong on backend
                                            showToastify(result.msg, "error")
                                        }
                                    },
                                    error: function(result) {
                                        console.log(result);
                                    }
                                });
                            }
                        })


                } else {
                    showToastify("Please select at least one checkbox", "error")
                }
            @endcan
        }
        $(document).on("change", "#search_agents", function() {
            let agents = $(this).val();
            $("#search_owners").val("").trigger("change");
            if (agents) {
                $.ajax({
                    url: "{{ route('ajax.owners') }}",
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        agents
                    },
                    success: function(result) {
                        let options = "";
                        result?.data.map(item => {
                            options +=
                                `<option value="${item.id}">${item.first_name} ${item.first_name}<${item.mobile}></option>`
                        })
                        $("#search_owners").html(options)
                        $("#search_owners").val("").trigger("change");
                    },
                    error: function(result) {
                        console.log(result);
                    }
                });
            }
        });






        $("#sidebarListings").addClass('active');

        // if #contract_start date is selected and #contract_end is empty then set #contract_end date to 2 months from #contract_start date
        $(document).on('change', '#contract_start', function() {
            if ($('#contract_end').val() == '') {
                let givenDateStr = $(this).val();
                const [day, month, year] = givenDateStr.split("-");
                const dateObj = new Date(`${year}-${month}-${day}`);
                dateObj.setMonth(dateObj.getMonth() + 2);
                const resultDateStr =
                    `${dateObj.getDate().toString().padStart(2, '0')}-${(dateObj.getMonth() + 1).toString().padStart(2, '0')}-${dateObj.getFullYear()}`;
                // let date = moment($(this).val()).add(2, 'months').format('DD-MM-YYYY');
                $('#contract_end').val(resultDateStr);
                $("#contract_end").flatpickr({
                    allowInput: false,
                    dateFormat: "d-m-Y",
                    minDate: givenDateStr, // Use 'date' instead of 'this.value'
                });
            } else {
                let givenDateStr = $(this).val();
                const [day, month, year] = givenDateStr.split("-");
                const dateObj = new Date(`${year}-${month}-${day}`);
                dateObj.setMonth(dateObj.getMonth() + 2);
                const resultDateStr =
                    `${dateObj.getDate().toString().padStart(2, '0')}-${(dateObj.getMonth() + 1).toString().padStart(2, '0')}-${dateObj.getFullYear()}`;
                // let date = moment($(this).val()).add(2, 'months').format('DD-MM-YYYY');
                // $('#contract_end').val(resultDateStr);
                $("#contract_end").flatpickr({
                    allowInput: false,
                    dateFormat: "d-m-Y",
                    minDate: resultDateStr, // Use 'date' instead of 'this.value'
                });


            }
            // if contract_start is empty then set contract_end to empty
            if ($(this).val() == '') {

                $('#contract_end').val('');
            }
        });



        function highlightRow(row) {
            row.removeClass('selected');
            dataTable.$('tr.selected').removeClass('selected');
            row.addClass('selected');
        } // highlightRow



        function validationForPublish() {
            $('#title').prop('required', true);
            $('#description').prop('required', true);
            $('#owner_id').prop('required', true);
            $('#bed_id').prop('required', true);
            $('#bath_id').prop('required', true);
            $('#built_up_area').prop('required', true);
            $('#parking').prop('required', true);
            $('#furnished').prop('required', true);
            $('#price').prop('required', true);
            $('#description').attr({
                minlength: "740",
                maxlength: "2000"
            });

        }

        function validationForPocket(event) {
            $('#title').prop('required', true);
            $('#description').prop('required', true);
            $('#owner_id').prop('required', true);
            $('#bed_id').prop('required', true);
            $('#bath_id').prop('required', true);
            $('#built_up_area').prop('required', true);
            $('#parking').prop('required', true);
            $('#furnished').prop('required', true);
            $('#price').prop('required', true);
            $('#description').attr({
                minlength: "740",
                maxlength: "2000"
            });
            $('#isPocketRequest').val('1');
            $('#isPublishRequest').val('0');

        }

        function validationForDraft() {
            $('#title').prop('required', false);
            $('#description').prop('required', false);
            $('#owner_id').prop('required', true);
            $('#bed_id').prop('required', false);
            $('#price').prop('required', true);
            $('#bath_id').prop('required', false);
            $('#built_up_area').prop('required', false);
            $('#parking').prop('required', false);
            $('#furnished').prop('required', false);
            $('#description').attr({
                minlength: "0",
                maxlength: "2000"
            });
        }



        $(document).on("select2:open", () => {
            document.querySelector(".select2-container--open .select2-search__field").focus()
        })
        window.onscroll = function() {
            myFunction()
            scrollFunction();
        };

        // Get the navbar
        var navbar = document.getElementById("top-bar");
        var tableHeader = document.getElementById("table-sticky-header");

        // Get the offset position of the navbar
        var sticky = navbar.offsetTop;

        // Add the sticky class to the navbar when you reach its scroll position. Remove "sticky" when you leave the scroll position
        function myFunction() {
            if (window.pageYOffset >= sticky) {
                navbar.classList.add("sticky-header");
                tableHeader.classList.add("sticky-table-header");
            } else {
                navbar.classList.remove("sticky-header");
                tableHeader.classList.remove("sticky-table-header");
            }
        }


        function checkExpiry(date) {

            var givenDateStr = $('#contract_end').val();

            // Check if the contract_end field is empty
            if (givenDateStr.trim() === "") {
                // Contract end date is empty, so no need to show anything
                return;
            }

            var givenDateArr = givenDateStr.split("-"); // Split the date string into day, month, and year parts

            // Check if the date string contains three parts (day, month, year)
            if (givenDateArr.length !== 3) {
                $("#result").text("Invalid date format for 'contract_end' input.");
                console.log("Invalid date format for 'contract_end' input.");
                return;
            }

            // Construct the givenDate object manually
            var givenDate = new Date(givenDateArr[2], givenDateArr[1] - 1, givenDateArr[0]);

            // Check if givenDate is a valid date
            if (isNaN(givenDate)) {
                $("#result").text("Invalid date format for 'contract_end' input.");
                console.log("Invalid date format for 'contract_end' input.");
                return;
            }

            // Today's date
            var today = new Date();

            // Calculate the time difference in milliseconds
            var timeDifference = givenDate - today;

            // Check if the given date is in the past
            if (timeDifference < 0) {
                $("#result").text("Contract Expired.");
                console.log("Contract Expired.");
                return;
            }

            // Convert milliseconds to days
            var daysDifference = Math.ceil(timeDifference / (1000 * 60 * 60 * 24));

            if (daysDifference <= 0 || isNaN(daysDifference)) {

                var apnd =
                    '<span class="kt-badge  kt-badge--danger kt-badge--inline kt-badge--pill text-danger">Contract Expired.</span>';
                $('.ContractDaysPlacing').html(apnd);

            } else if (daysDifference >= 1 && daysDifference <= 14) {
                var apnd =
                    '<span class="kt-badge  kt-badge--danger kt-badge--inline kt-badge--pill text-danger">Contract Expires in ' +
                    daysDifference + ' day(s).</span>';
                $('.ContractDaysPlacing').html(apnd);
            } else if (daysDifference >= 15) {
                var apnd =
                    '<span class="kt-badge  kt-badge--success kt-badge--inline kt-badge--pill text-success">Contract will expire in ' +
                    daysDifference + ' days. </span>';
                $('.ContractDaysPlacing').html(apnd);
            }
        }



        $(document).on("click", "#savePublishBtn", function(event) {
            event.preventDefault();

            @can('Properties Edit')
                $('#screen_loader').removeClass('d-none');
                fullyLoaded = false;
                $("#temp_id").val("");
                $(".inputs").prop("disabled", false)
                $('#mainForm').removeClass('was-validated');
                $("#price_on_application").prop("disabled", false)
                if (mode == 'view') {
                    editData($('#id').val());
                    toggleForm(true)
                }
            @endcan
            // if any invalid feedback then scroll to it

            setTimeout(function() {
                $('#isPublishRequest').val('1');
                $('#isPocketRequest').val('0');
                validationForPublish();
                $('#screen_loader').addClass('d-none');
                // alert($('#owner_id').val())
                // $.ajax({
                //     type: "POST",
                //     url: "",
                //     data: formData,
                //     dataType: 'json',
                //     success: function(data) {
                //         if (data.status == '1') {
                //             showToastify("Contact Type status updated successfully", "success")
                //             dataTable.row().draw();
                //         }
                //     },
                //     error: function(data) {
                //         console.log('Error:', data);
                //     }
                // });

                $("#saveBtn").trigger("click"); //
            }, 3000);
        })



        $(document).on("click", "#publishPocketBtn", function(event) {
            event.preventDefault();

            @can('Properties Edit')
                $('#screen_loader').removeClass('d-none');

                fullyLoaded = false;
                $("#temp_id").val("");
                $(".inputs").prop("disabled", false)
                $('#mainForm').removeClass('was-validated');
                $("#price_on_application").prop("disabled", false)
                if (mode == 'view') {
                    editData($('#id').val());
                    toggleForm(true)
                }
            @endcan
            validationForPocket();
            setTimeout(function() {
                $("#publish_status").val('POCKET').trigger("change");
                $("#saveBtn").trigger("click");
                $('#screen_loader').addClass('d-none');
            }, 2000);
        })

        $('#publish_status').change(function() {

            if ($('#publish_status').val() == 'PUBLISHED') {
                $('#rera_permit_number').prop('required', true);
            } else {
                $('#rera_permit_number').prop('required', false);

            }
        })
        @if (auth()->user()->can('Listing Duplicate Check'))
            let previousStatus = '';
            $('#publish_status').on('select2:selecting', function(evt) {
                previousStatus = $('#publish_status').val();
            });
            $('#publish_status').on('select2:select', function(evt) {
                if ($('#publish_status').val() == 'PUBLISHED') {
                    $("#checkDuplicateProperty").trigger("click");
                }
            });
        @endif

        $('#publish_status').change(function() {
            if (($('#publish_status').val() == 'PUBLISHED' || $('#publish_status').val() == 'PENDING') && (mode ==
                    'edit' || mode == 'create')) {
                $('#isPublishRequest').val('0');
                $('#isPocketRequest').val('0');
                validationForPublish();

            } else if ($('#publish_status').val() == 'POCKET' && (mode == 'edit' || mode == 'create')) {
                $('#isPublishRequest').val('0');
                $('#isPocketRequest').val('0');
                validationForPocket();
            } else {
                $('#isPublishRequest').val('0');
                $('#isPocketRequest').val('0');
                validationForDraft();
            }
        })

        dataTable.on('draw.dt', function() {
            $(".chk_child").prop("checked", false);
            $('.multi_options').remove();
            $('#checkAll').prop("checked", false);
        });

        function textColorBasedOnScore(percent) {
            if (percent > 70) {
                return 'text-danger';
            } else if (percent > 50) {
                return 'text-warning';
            } else {
                return 'text-success';
            }

        }

        function bgColorBasedOnScore(percent) {
            if (percent > 80) {
                return 'bg-danger';
            } else if (percent > 50) {
                return 'bg-warning';
            } else {
                return 'bg-success';
            }

        }

        function drawTdDuplicateModal(item) {
            let html = '';
            let statusBadge = createPublishStatusBadge(item.publish_status);
            let url = "{{ route('admin.temp-listing.index') }}" + "?ref_number=" + item.ref_no;
            html = `<tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-2">
                                      <a href="${url}" target="_blank" class="text-reset">   <img src="${item?.first_image?.thumbnail}" alt="" class="avatar-xs rounded"></a>
                                    </div>

                                </div>
</td>
                                <td>
                                 <div>
                                        <h6 class="fs-14 my-1 fw-medium">
                                            <a href="${url}" target="_blank" >${item?.ref_no} </a>

                                        </h6>
                                        <span class="text-muted">${item?.title}</span><br>
                                        <span class="text-muted fs-12">${item?.community_property?.path_name ?? item?.sub_community?.path_name}</span>
                                    </div>

                            </td>
                            <td>
                                <p class="mb-0">Unit no.: ${item.unit_no}</p>
                                <span class="text-muted">${item.for}</span>

                            </td>
                                <td>
                                <span class="text-muted fs-12">Created at: ${moment(item?.created_at).format('DD/MM/YYYY hh:mm:ss')}</span>
                            <br>
                                <span class="text-muted fs-12">Published at:${(item?.publish_date !=null ? moment(item?.publish_date).format('DD/MM/YYYY hh:mm:ss') : "")}</span>


                            </td>
                            <td>
                                <div class="d-flex align-items-center" title="Rona Rahim">
                                    <div class="flex-shrink-0 me-2">
                                        <img src="${item?.agent?.image_url}" alt="" class="avatar-xs rounded-circle">
                                    </div>
                                    <div class="flex-grow-1">${item?.agent?.full_name}</div>
                                </div>
                            </td>

                            <td>
                                ${statusBadge}
                            </td>
                            <td>
                                <h5 class="fs-14 mb-0 align-middle">${item.duplicate_score}%

</h5>
<br>
<div class="progress animated-progress custom-progress">
    <div class="progress-bar ${bgColorBasedOnScore(item.duplicate_score)}" role="progressbar" style="width: ${item.duplicate_score}%" aria-valuenow="${item.duplicate_score}" aria-valuemin="0" aria-valuemax="100"></div>
</div>
                            </td>
                        </tr>`;


            return html;
        }

        function drawTdOrignalModal(item) {
            let html = '';
            let statusBadge = createPublishStatusBadge(item.publish_status);
            let url = "{{ route('admin.temp-listing.index') }}" + "?ref_number=" + item.ref_no;
            html = `
                            <tr style="position:sticky; background-color:#ddfaeb;"><td>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-2">
                                      <a href="${url}" target="_blank" class="text-reset">   <img src="${item?.first_image?.thumbnail}" alt="" class="avatar-xs rounded"></a>
                                    </div>

                                </div>
                                </td>
                                <td>
                                 <div>
                                        <h6 class="fs-14 my-1 fw-medium">
                                            <a href="${url}" target="_blank" class="text-reset">${item?.ref_no} </a>

                                        </h6>
                                        <span class="text-muted">${item?.title}</span><br>
                                        <span class="text-muted fs-12">${item?.sub_community?.path_name ?? ''}</span>
                                    </div>
                            </td>
                            <td>
                                <p class="mb-0">Unit no.: ${item.unit_no}</p>
                                <span class="text-muted">${item.for}</span>

                            </td>
                                <td>
                                <span class="text-muted fs-12">Created at: ${moment(item?.created_at).format('DD/MM/YYYY hh:mm:ss')}</span>
                            <br>
                                <span class="text-muted fs-12">Published at: ${(item?.publish_date !=null ? moment(item?.publish_date).format('DD/MM/YYYY hh:mm:ss') : "")}</span>


                            </td>
                            <td>
                                <div class="d-flex align-items-center" title="Rona Rahim">
                                    <div class="flex-shrink-0 me-2">
                                        <img src="${item?.agent?.image_url}" alt="" class="avatar-xs rounded-circle">
                                    </div>
                                    <div class="flex-grow-1">${item?.agent?.full_name}</div>
                                </div>
                            </td><td>
                               ${statusBadge}
                            </td><td></td><tr>`;


            return html;
        }
        @if (auth()->user()->can('Listing Duplicate Check'))
            $("#checkDuplicateProperty").click(function() {
                let property_id = $("#id").val();
                $("#duplicatePropertyList").html("");
                $("#duplicatePropertyListHead").html("");
                $.ajax({
                    url: "{{ route('admin.listing.properties.get.duplicate') }}",
                    type: "POST",
                    data: {
                        property_id: property_id,
                    },
                    success: function(response) {
                        if (response.status == 1) {
                            let html = '';
                            $("#checkDuplicateListingModal").modal('show');
                            html = drawTdOrignalModal(response.property);
                            $("#duplicatePropertyList").html(html);
                            html = '';
                            response.data.map((item, index) => {
                                html += drawTdDuplicateModal(item);
                            });
                            $("#duplicatePropertyList").append(html);
                        } else {
                            showToastify(response.message, "success")
                        }
                    }
                });
            });
        @endif

        @if (auth()->user()->can('Generate Bulk Rera Check'))
            $("#bulkReraCheckReport").click(function() {
                $.ajax({
                    url: "{{ route('ajax.bulkReraCheck') }}",
                    type: "POST",
                    data: {
                        'token': 'h38db83bbsioi883bbs93bhjksf83bbse03'
                    },
                    success: function(response) {

                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(errorThrown);
                        console.log(jqXHR.responseJSON);
                        toastr.error(jqXHR.responseJSON.message);
                        $('#duplicate_listing_alert').html('');
                    }
                });
            });
        @endif



        $('#generateSocialMediaPost').click(function() {
            let ref_no = $('#ref_no').val();
            let pdfUrl = "{{ route('generatePOST', ':ref_no') }}";
            pdfUrl = pdfUrl.replace(':ref_no', ref_no);
            let iframeSrc = pdfUrl;
            $('#postIframe').attr('src', iframeSrc);

            $('#location_info').val($('.view-in-map').text());
            $('#generatePost').modal('show');
        });


        $('#generateBrochure').click(function() {
            // get select features[]
            let features = [];
            $.each($("input[name='features[]']:checked"), function() {
                features.push($(this).attr('data-title'));
            });

            // now append in .appendFeatures div
            let html = '';
            features.map((item, index) => {
                html += ` <div class="single-feature">
                            <div class="form-check amenities-form-check"> <input class="btn-check regenerateIframe form-check-input checkSixCount" name="pdf_features[]"
                                type="checkbox" value="${item}"
                                    id="pdf_feature-${index}" autocomplete="off">
                                    <label class="btn btn-amenities-primary btn-custom btn-primary" for="pdf_feature-${index}"> ${item}
                                            </label></div></div>`;
            });
            $('.appendFeatures').html(html);
            // select first 9 features
            $('.checkSixCount').slice(0, 6).prop('checked', true);


            // get #description and append in #PDF_description input
            let description = $('#description').val();
            $('#PDF_description').val(description);

            let count = description.length;
            $('#descriptionCount').text(count);

            if (count > 700) {
                $('#descriptionCount').removeClass('text-success');

                $('#descriptionCount').addClass('text-danger');
                $('#genPDF').text('Description is too long Or Missing 9 Amenities');
                $('#genPDF').attr('disabled', true);
            } else {
                $('#descriptionCount').removeClass('text-danger');
                $('#descriptionCount').addClass('text-success');
                $('#genPDF').text('Generate');

                $('#genPDF').attr('disabled', false);
            }


            let ref_no = $('#ref_no').val();
            let pdfUrl = "{{ route('generatePDF', ':ref_no') }}";
            pdfUrl = pdfUrl.replace(':ref_no', ref_no);
            let iframeSrc = pdfUrl;
            $('#pdfIframe').attr('src', iframeSrc);
            $('#countAmenities').text($('.checkSixCount:checked').length + '/9');
            regenerateIframe();
            $('#generateBrochureModal').modal('show');
        });


        $('#generateNewBrochure').click(function() {
            // get select features[]
            let features = [];
            $.each($("input[name='features[]']:checked"), function() {
                features.push($(this).attr('data-title'));
            });

            // now append in .appendFeatures div
            let html = '';
            features.map((item, index) => {
                html += ` <div class="single-feature">
                            <div class="form-check amenities-form-check"> <input class="btn-check regenerateNewIframe form-check-input checkNewSixCount" name="pdf_new_features[]"
                                type="checkbox" value="${item}"
                                    id="pdf_feature-${index}-new" autocomplete="off">
                                    <label class="btn btn-amenities-primary btn-custom btn-primary" for="pdf_feature-${index}-new"> ${item}
                                            </label></div></div>`;
            });
            $('.appendNewFeatures').html(html);
            // select first 9 features
            $('.checkNewSixCount').slice(0, 6).prop('checked', true);


            // get #description and append in #PDF_description input
            let description = $('#description').val();
            $('#PDF_new_description').val(description);

            let count = description.length;
            $('#newDescriptionCount').text(count);

            if (count > 700) {
                $('#newDescriptionCount').removeClass('text-success');

                $('#newDescriptionCount').addClass('text-danger');
                $('#genPDF').text('Description is too long Or Missing 9 Amenities');
                $('#genPDF').attr('disabled', true);
            } else {
                $('#newDescriptionCount').removeClass('text-danger');
                $('#newDescriptionCount').addClass('text-success');
                $('#genPDF').text('Generate');

                $('#genPDF').attr('disabled', false);
            }


            let ref_no = $('#ref_no').val();
            let pdfUrl = "{{ route('generateBrochure', ':ref_no') }}";
            pdfUrl = pdfUrl.replace(':ref_no', ref_no);
            let iframeSrc = pdfUrl;
            $('#pdfNewIframe').attr('src', iframeSrc);
            $('#countNewAmenities').text($('.checkNewSixCount:checked').length + '/8');
            regenerateNewIframe();
            $('#generateNewBrochureModal').modal('show');
        });



        $('#PDF_description').keyup(function() {
            let count = $(this).val().length;
            let checkSix = $('.checkSixCount:checked').length;


            if (count <= 700 && checkSix == 6) {
                $('#descriptionCount').removeClass('text-danger');
                $('#descriptionCount').addClass('text-success');
                $('#genPDF').text('Generate');
                $('#genPDF').attr('disabled', false);

            } else {
                $('#descriptionCount').removeClass('text-success');
                $('#descriptionCount').addClass('text-danger');
                $('#genPDF').text('Generate');
                $('#genPDF').text('Description is too long Or Missing 9 Amenities');

                $('#genPDF').attr('disabled', true);

            }
            if (count <= 700) {
                $('#descriptionCount').removeClass('text-danger');
                $('#descriptionCount').addClass('text-success');
            } else {
                $('#descriptionCount').removeClass('text-success');
                $('#descriptionCount').addClass('text-danger');
            }
            $('#descriptionCount').text(count);
            regenerateIframe()
        })



        $('.regenerateIframe').change(function() {
            regenerateIframe()
        });
        $('.regenerateNewIframe').change(function() {
            // get value of input(checkbox) id="show_new_price_iframe"
            // let show_price = $('#show_new_price_iframe').val();
            // alert(show_price);

            regenerateNewIframe()
        });



        $('.regeneratePost').change(function() {
            regeneratePost()
        });

        function regenerateIframe() {

            let description = $('#PDF_description').val();
            description = description.replace(/&/g, "and");
            let show_price = $('#show_price_iframe').val();


            let show_profile = 'yes';
            let profile_user = $("input[name='profile_user_iframe']:checked").val();

            if ($('#show_price_iframe').is(':checked')) {
                show_price = 'true';
            } else {
                show_price = 'false';
            }

            let features = [];
            $.each($("input[name='pdf_features[]']:checked"), function() {
                features.push($(this).val());
            });
            let ref_no = $('#ref_no').val();
            // $('#pdfIframe').attr('src', "{{ route('generatePDF', 'axc-3964690') }}?description=" + description+"&features=" + features+'&show_price=' + show_price);

            let image1 = $('#image_1_iframe').val();
            let image2 = $('#image_2_iframe').val();
            let image3 = $('#image_3_iframe').val();


            let pdfUrl = "{{ route('generatePDF', ':ref_no') }}";

            if (image1 && image2 && image3) {
                pdfUrl = pdfUrl.replace(':ref_no', ref_no);
                let iframeSrc = pdfUrl + "?type=pdf&description=" + description + "&features=" + features + '&show_price=' +
                    show_price + '&image_1=' + image1 + '&image_2=' + image2 + '&image_3=' + image3 + '&show_profile=' +
                    show_profile + '&profile_user=' + profile_user;
                $('#pdfIframe').attr('src', iframeSrc);
            }



            // check PDF_description length and features length
            let count = $('#PDF_description').val().length;
            let checkSix = $('.checkSixCount:checked').length;
            if (count <= 700 && checkSix == 6) {
                $('#descriptionCount').removeClass('text-danger');
                $('#descriptionCount').addClass('text-success');
                $('#genPDF').text('Generate');
                $('#genPDF').attr('disabled', false);
            }


        }

        function regenerateNewIframe() {

            console.log('regenerateNewIframe');
            description = 'lorem';

            let show_profile = 'yes';
            let profile_user = $("input[name='profile_new_user_iframe']:checked").val();
            let  show_prices = 'true';
            console.log($('#show_new_price_iframe').is(':checked'));
            if ($('#show_new_price_iframe').is(':checked')) {
                show_prices = 'true';
            } else {
                show_prices = 'true';
            }

            let features = [];
            $.each($("input[name='pdf_new_features[]']:checked"), function() {
                features.push($(this).val());
            });
            let ref_no = $('#ref_no').val();
            // $('#pdfIframe').attr('src', "{{ route('generatePDF', 'axc-3964690') }}?description=" + description+"&features=" + features+'&show_price=' + show_price);

            let image1 = $('#image_5_iframe').val();
            let image2 = $('#image_6_iframe').val();
            let image3 = $('#image_7_iframe').val();
            let image4 = $('#image_8_iframe').val();


            let pdfUrl = "{{ route('generateBrochure', ':ref_no') }}";

            if (image1 && image2 && image3) {
                pdfUrl = pdfUrl.replace(':ref_no', ref_no);
                let iframeSrc = pdfUrl +
                "?type=pdf&description=" + description
                + "&features=" + features
                + '&show_price=' + show_prices
                + '&image_1=' + image1
                + '&image_2=' + image2
                + '&image_3=' + image3
                + '&image_4=' + image4
                + '&show_profile=' + show_profile
                + '&profile_user=' + profile_user;
                $('#pdfNewIframe').attr('src', iframeSrc);
            }



            // check PDF_description length and features length
            let count = $('#PDF_description').val().length;
            let checkSix = $('.checkSixCount:checked').length;
            if (count <= 700 && checkSix == 6) {
                $('#descriptionCount').removeClass('text-danger');
                $('#descriptionCount').addClass('text-success');
                $('#genNewPDF').text('Generate');
                $('#genNewPDF').attr('disabled', false);
            }


        }

        function regeneratePost() {

            let show_price = $('#show_price_post').val();
            let show_profile = $('#show_profile_post').val();
            let ref_no = $('#ref_no').val();
            let image4 = $('#image_4_iframe').val();

            let title = $('#post_title').val();
            let pretext = $('#post_title option:selected').attr('data-pretext');
            let posttext = $('#post_title option:selected').attr('data-posttext');
            let location = $('#location_info').val();
            let pdfUrl = "{{ route('generatePOST', ':ref_no') }}";
            if (image4) {
                pdfUrl = pdfUrl.replace(':ref_no', ref_no);
                let iframeSrc = pdfUrl + "?type=post" + '&show_price=' + show_price + '&show_profile=' + show_profile +
                    '&pretext=' + pretext + '&posttext=' + posttext + '&image=' + image4 + '&location=' + location;
                $('#postIframe').attr('src', iframeSrc);
            }



        }
        $(document).on('change', '.checkSixCount', function() {
            $('#countAmenities').text($('.checkSixCount:checked').length + '/6');
            if ($('.checkSixCount:checked').length == 6) {

                $('.checkSixCount:not(:checked)').attr('disabled', true);
                let features = [];
                $.each($("input[name='pdf_features[]']:checked"), function() {
                    features.push($(this).val());
                });
                regenerateIframe()
                let url = window.location.href;
                if ($('#PDF_description').val().length <= 700) {
                    $('#genPDF').attr('disabled', false);
                }
                // $('#genPDF').attr('disabled', false);
            } else {
                regenerateIframe()
                $('.checkSixCount').attr('disabled', false);
                $('#genPDF').text('Description is too long Or Missing 6 Amenities');
                $('#genPDF').attr('disabled', true);
            }
        });


        $(document).on('change', '.checkNewSixCount', function() {
            $('#countNewAmenities').text($('.checkNewSixCount:checked').length + '/8');
            if ($('.checkNewSixCount:checked').length == 8) {

                $('.checkNewSixCount:not(:checked)').attr('disabled', true);
                let features = [];
                $.each($("input[name='pdf_new_features[]']:checked"), function() {
                    features.push($(this).val());
                });
                $('#genNewPDF').text('Generate');
                $('#genNewPDF').attr('disabled', false);

                regenerateNewIframe()
                let url = window.location.href;

                // $('#genPDF').attr('disabled', false);
            } else {
                regenerateNewIframe()
                $('.checkNewSixCount').attr('disabled', false);
                $('#genNewPDF').text('Amenities are missing');
                $('#genNewPDF').attr('disabled', true);
            }
        });

        $('#search_updated_by').select2({
            placeholder: 'Search Users',
            minimumInputLength: 0,
            allowClear: true,
            ajax: {
                url: "{{ route('ajax.userAutoComplete') }}",
                dataType: 'json',
                delay: 100,
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: item.first_name + ' ' + item.last_name,
                                id: item.id,
                                image: item.thumb_url,
                                status: item.status,

                            }
                        })
                    };
                },
                cache: true
            },
            templateResult: formatAgent2,
        });




        $('#gen').click(function() {
            let show_price = $('#show_price_post').val();
            let show_profile = $('#show_profile_post').val();
            let ref_no = $('#ref_no').val();
            let image4 = $('#image_4_iframe').val();

            let title = $('#post_title').val();
            let pretext = $('#post_title option:selected').attr('data-pretext');
            let posttext = $('#post_title option:selected').attr('data-posttext');
            let location = $('#location_info').val();

            let pdfUrl = "{{ route('generatePOST', ':ref_no') }}";
            let url = window.location.href;
            let ref_number = url.split('?')[1].split('=')[1];

            let new_url = printPage + ref_number + "?type=post" + '&show_price=' + show_price + '&show_profile=' +
                show_profile + '&pretext=' + pretext + '&posttext=' + posttext + '&image=' + image4 + '&location=' +
                location;
            console.log(new_url)

            // if (image4) {
            //     pdfUrl = pdfUrl.replace(':ref_no', ref_no);
            //     let iframeSrc = pdfUrl + "?type=post"+ '&show_price=' + show_price+'&show_profile='+show_profile +'&pretext='+pretext+'&posttext='+posttext + '&image=' + image4+'&location='+location;
            //     $('#postIframe').attr('src', iframeSrc);
            // }

            $('#generatePost').modal('hide');
            window.open(new_url, '_blank');
        });

        $('#genPDF').click(function() {

            let description = $('#PDF_description').val();
            description = description.replace(/&/g, "and");


            let show_price = $('#show_price_iframe').val();
            let show_profile = 'yes';
            let profile_user = $("input[name='profile_user_iframe']:checked").val();
            if ($('#show_price_iframe').is(':checked')) {
                show_price = 'true';
            } else {
                show_price = 'false';
            }
            let features = [];
            $.each($("input[name='pdf_features[]']:checked"), function() {
                features.push($(this).val());
            });
            let url = window.location.href;
 
            let ref_number = url.split('?')[1].split('=')[1];

            let image1 = $('#image_1_iframe').val();
            let image2 = $('#image_2_iframe').val();
            let image3 = $('#image_3_iframe').val();

            let new_url = printPage + ref_number + '?type=pdf&description=' + description + '&features=' +
                features + '&show_price=' + show_price + '&image_1=' + image1 + '&image_2=' + image2 + '&image_3=' +
                image3 + '&show_profile=' + show_profile + '&profile_user=' + profile_user;
            console.log(new_url)
            $('#generateBrochureModal').modal('hide');
            window.open(new_url, '_blank');
        });

        $('#genNewPDF').click(function() {


            description = "lorem";


            let show_price = $('#show_new_price_iframe').val();
            let show_profile = 'yes';
            let profile_user = $("input[name='profile_new_user_iframe']:checked").val();

            if ($('#show_new_price_iframe').is(':checked')) {
                show_price = 'true';
            } else {
                show_price = 'false';
            }
            let features = [];
            $.each($("input[name='pdf_new_features[]']:checked"), function() {
                features.push($(this).val());
            });
            let url = window.location.href;
            console.log(printPage)
            let ref_number = url.split('?')[1].split('=')[1];

            let image1 = $('#image_5_iframe').val();
            let image2 = $('#image_6_iframe').val();
            let image3 = $('#image_7_iframe').val();
            let image4 = $('#image_8_iframe').val();


            console.log(printPage);
            let new_url = printPage + ref_number + '/new-brocher?type=pdf&show_price='+show_price+'&description=' + description +
                  '&features=' + features
                + '&image_1=' + image1
                + '&image_2=' + image2
                + '&image_3=' + image3
                + '&image_4=' + image4
                + '&show_profile=' + show_profile
                + '&profile_user=' + profile_user;
            console.log(new_url)
            $('#generateNewBrochureModal').modal('hide');
            console.log(new_url)
            window.open(new_url, '_blank');
        });

        $('#trimDescription').click(function() {

            let description = $('#description').val();
            let trimDescription = description.substring(0, 700);
            $('#PDF_description').val(trimDescription);
            $('#descriptionCount').text(trimDescription.length);
            $('#descriptionCount').removeClass('text-danger');
            $('#descriptionCount').addClass('text-success');



            if ($('#PDF_description').val().length <= 700 && $('.checkSixCount:checked').length == 6) {
                $('#genPDF').attr('disabled', false);
                $('#genPDF').text('Generate');
            } else {
                $('#genPDF').attr('disabled', true);
            }

            regenerateIframe();
        });




        let reraValidityCheckUrl = "{{ route('admin.listing.properties.rera.validity.check') }}";
        $('#checkRera').on('click', function() {

            let inputs = $("#rera_permit_number").val();
            if (inputs.length == 0) {
                $("#reraDateRange").html(`<span class="text-danger">RERA Permit Number is required</span>`);
                return false;
            }
            $(this).html(`<span class="d-flex align-items-center">
            <span class="spinner-border flex-shrink-0" role="status">
                <span class="visually-hidden"></span>
            </span></span>`);
            let formData = {
                id: $("#id").val(),
                rera_number: inputs
            }
            $.ajax({
                type: "POST",
                url: reraValidityCheckUrl,
                data: formData,
                dataType: 'json',
                success: function(result) {
                    $('#checkRera').html(`<i class="mdi mdi-eye-arrow-right"></i>`);
                    if (result.status == '1') {
                        if (result.data.start_date != null && result.data.end_date != null) {
                            if (moment(result.data.end_date)
                                .isAfter()) { //checking if date is in future
                                $("#reraDateRange").html(
                                    `<span class="text-success"><i class="bx bx-check-shield"></i>` +
                                    moment(result.data.start_date).format("DD/MM/YYYY") + ` - ` +
                                    moment(result.data.end_date).format("DD/MM/YYYY") + `</span>`);
                            } else {
                                $("#reraDateRange").html(
                                    `<span class="text-danger"><i class="bx bx-shield-x"></i>` +
                                    moment(result.data.start_date).format("DD/MM/YYYY") + ` - ` +
                                    moment(result.data.end_date).format("DD/MM/YYYY") + `</span>`);
                            }
                        }
                        if (result.data.qr_link != null) {
                            $("#qr_link").val(result.data.qr_link);
                            $("#qr_link_file").html(`<a data-lightbox="listing-gallery" href="` + result
                                .data.qr_link + `">
                                        <img src="` + result.data.qr_link + `" style="width: 37px;" alt="rera qr img"/>
                                    </a>`);
                        } else {
                            $("#qr_link_file").html('');
                        }
                        showToastify('RERA information fetched successfully', "success")

                    } else {
                        $("#reraDateRange").html(
                            `<span class="text-danger">The RERA permit could not be located or is invalid.</span>`
                        );
                    }
                },
                error: function(data) {
                    $('#checkRera').html(`<i class="mdi mdi-eye-arrow-right"></i>`);
                    console.log('Error:', data);
                }
            });

        });


        $('#unit_no').change(function() {

            checkListingDuplication();
        })

        $(document).ready(function() {
            $('#deleteImage').click(function() {
                // Reset mediaInput image source
                $('#mediaInput').val('');

                // Add d-none class to showWhenImage
                $('#showWhenImage').addClass('d-none');

                // Reset #blah image source
                $('#blah').attr('src', '');

            })
        })


        function checkListingDuplication() {
            $('#duplicate_listing_alert').html('');

            if (mode != 'view') {

                setTimeout(function() {

                    var c_unit_no = $('#unit_no').val();
                    var c_location_id = $('#location_id').val();
                    var c_location_type = $('#location_type').val();
                    var c_for = $('#for').val();
                    var c_id = $('#id').val();


                    if (c_unit_no && c_location_id && c_location_type && c_for) {

                        $.ajax({
                            url: "{{ route('ajax.check_duplicate_listing') }}",
                            type: 'POST',
                            dataType: "JSON",
                            data: {
                                "_token": "{{ csrf_token() }}",
                                "unit_no": c_unit_no,
                                "location_id": c_location_id,
                                "location_type": c_location_type,
                                "for": c_for,
                                "id": c_id

                            },

                            success: function(response) {
                                if (response['data']) {

                                    $('#duplicate_listing_alert').html(
                                        `<div class="alert alert-danger alert-dismissible fade show mb-xl-0 p-2" role="alert">
                                    <strong> Duplicate Listing! </strong> <br>Listing with identical details already exists under ` +
                                        response[
                                            'data']['agent']['name'] + ` (` + response[
                                            'data']['ref_no'] + `)
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>`);

                                }


                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                console.log(errorThrown);
                                console.log(jqXHR.responseJSON);
                                toastr.error(jqXHR.responseJSON.message);
                                $('#duplicate_listing_alert').html('');
                            }


                        });
                    } else {

                    }

                }, 100);



            } // end if



        }

        function checkPermission() {

            if ($('#unit_no').val() == '-' || $('#unit_no').val() == '') {

                $('.delete-photo').addClass('d-none');
            } else {

                $('.delete-photo').removeClass('d-none');
            }

        }

        $(document).on('click', function() {
            $('.delete_media_ids').change(function() {

                var checkedCount = $('.delete_media_ids:checked').length;
                if (checkedCount == 0) {
                    $('#deleteImages').addClass('d-none');
                    $('#selectAllOptions').addClass('d-none');
                } else {
                    $('#deleteImages').removeClass('d-none');
                    $('#selectAllOptions').removeClass('d-none');
                }
            });
        });

        $(document).on('click', function() {
            $('#selectAllOptions').click(function() {

                selectAllOptions();
            });
        })

        function selectAllOptions() {
            $('input.delete_media_ids').prop('checked', true);
        }


        function deleteMultipleImages() {
            @can('Bulk Delete Images')
                var ids_array = [];
                $('input.delete_media_ids:checked').each(function() {
                    var id = $(this).val();
                    ids_array.push(id);
                });

                if (ids_array.length > 0) {




                    Swal.fire({
                        html: '<div class="mt-3"><lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon><div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you Sure ?</h4><p class="text-muted mx-4 mb-0">Are you Sure You want to Delete this Listing ?</p></div></div>',
                        showCancelButton: !0,
                        confirmButtonClass: "btn btn-danger w-xs me-2 mb-1",
                        confirmButtonText: "Yes, Delete It!",
                        cancelButtonClass: "btn btn-soft-success w-xs mb-1",
                        buttonsStyling: !1,
                        showCloseButton: !0
                    }).then(function(t) {

                        if (t.isConfirmed) { // on confirm delete the category
                            $('#photos-gallery').addClass('non-clickable2');
                            $.ajax({
                                url: "{{ route('admin.listing.properties.multi.delete.images') }}",
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    ids: ids_array
                                },
                                success: function(result) {
                                    if (result.status == '1') {

                                        $('input.delete_media_ids:checked').each(function() {
                                            $(this).closest(".photo-element").remove();
                                        });

                                        showToastify(result.msg, "success")
                                        $('#deleteImages').addClass('d-none');
                                        $('#photos-gallery').removeClass('non-clickable2')

                                        var count = $('.photo-element').length;
                                        $('#photo_counter').text(count);
                                        if (count == 0) {
                                            $('#downloadBulkWatermark ,#downloadBulkwithoutWatermark, #selectAllOptions')
                                                .addClass('d-none');
                                        }
                                        dataTable.row().draw();
                                    } else {
                                        swal("Whoops", result.msg, "error");
                                        showToastify(result.msg, "error")
                                    }
                                },
                                error: function(result) {
                                    console.log(result);
                                }
                            });
                        }
                    })




                } else {
                    showToastify("Please select at least one checkbox", "error");
                }
            @endcan
        }


        $(document).on("change", ".effect-publish", function() {

            let publish_status = $("#is_live_listing").val();


            getChangeInListing($(this).attr('name'), $(this).val(), $("#id").val())

        })
        /* // getChangeInListing($(this).attr('name'), $(this).val(), $("#id").val())
            // let inputName = $(this).attr('name');
            // let change = '<small><small>⚠️ Changing '+ processString(inputName) +' may effect your Property Status.</small></small>'
            // $(this).closest('.effect-publish-div').find('.will-effect-status').html(change);
        */

        function processString(inputString) {
            var withoutUnderscores = inputString.replace(/[_-]/g, ' ');
            var withoutId = withoutUnderscores.replace(/id$/, '');
            var capitalized = withoutId.charAt(0).toUpperCase() + withoutId.slice(1);

            return capitalized;
        }

        function getChangeInListing(key, value, id) {
            if (key != null && value != null && id != null && fullyLoaded) {

                $.ajax({
                    url: "{{ route('admin.listing.properties.getChangeInListing') }}",
                    type: 'POST',
                    data: {
                        key: key,
                        value: value,
                        id: id
                    },

                    success: function(result) {
                        if (result.status == '1') {
                            let change = '<small><small>⚠️ Changing ' + processString(key) +
                                ' may effect your Property Status.</small></small>'
                            $(`input[name=${key}]`).closest('.effect-publish-div').find('.will-effect-status')
                                .html(change);
                            $(`select[name=${key}]`).closest('.effect-publish-div').find('.will-effect-status')
                                .html(change);
                            checkChanges = false;
                        } else {
                            $(`input[name=${key}]`).closest('.effect-publish-div').find('.will-effect-status')
                                .html('');
                            $(`select[name=${key}]`).closest('.effect-publish-div').find('.will-effect-status')
                                .html('');
                            checkChanges = true;
                        }
                    },
                    error: function(jqXhr, json, errorThrown) {
                        console.log(json)
                    },
                });
            }

        }

        $('#getStatusTimeline').click(function() {
            let id = $('#id').val();
            getPropertyTimeline(id);
        })


        $('#getActivityLog').click(function() {
            let id = $('#id').val();
            getActivityLog(id);
        })


        function getPropertyTimeline(id) {
            $('#StatusTimeline').html('');
            $('#no_timeline').addClass('d-none');
            $('#accordionPlaceholder').removeClass('d-none');
            $.ajax({
                url: "{{ route('admin.listing.properties.getPropertyTimeline') }}",
                type: 'POST',
                dataType: 'html',
                data: {
                    id: id,
                },

                success: function(result) {
                    $('#accordionPlaceholder').addClass('d-none');
                    $('#StatusTimeline').html(result);
                },
                error: function(jqXhr, json, errorThrown) {
                    console.log(json)
                },
            });
        }


        function editMarkingFormA() {
            let ref_number = $("#ref_no").text();
            let editUrl = "{{ route('admin.documents.index', 'file_slug=marketing-form-a-leasing-fillable') }}";
            editUrl += `&property_ref_no=${ref_number}`;
            window.open(editUrl, '_blank').focus();
        }

        $(document).on("click", "#downloadRequestBulkDocuments", function() {
            $("#downloadRequestBulkDocumentsForm").trigger("reset")
            $("#downloadRequestBulkDocumentsModal").modal("show");
        })

        $(document).on("submit", "#downloadRequestBulkDocumentsForm", function(event) {
            event.preventDefault();
            // $('#screen_loader').removeClass('d-none');
            let id = $("#id").val();
            console.log("id: ", id);

            let formData = new FormData($(this)[0]);

            formData.append("id", id)

            let buttons = $(this).find("button");
            $(buttons).prop("disabled", true);
            $.ajax({
                url: "{{ route('admin.listing.properties.requestDownloadZip') }}",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function(result) {

                    $(buttons).prop("disabled", false);
                    if (result.status == '1') {
                        showToastify(result.msg, "success");
                        $("#downloadRequestBulkDocumentsModal").modal("hide");
                        if (result.downloadUrl) {
                            window.open(result.downloadUrl, "_BLANK")
                        }
                    } else if (result.status == '2') {
                        let error_message = '';
                        $.each(result.errors, function(key, value) {
                            error_message += '' + key + ': ' + value + ' | ';
                        });
                        showToastify(error_message, "error")
                    } else {
                        showToastify(result.msg, "error")
                    }
                    //$('#screen_loader').addClass('d-none');

                },
                error: function(jqXhr, json, errorThrown) {
                    $(buttons).prop("disabled", false);
                    let data = jqXhr.responseJSON;
                    if (data.errors) {
                        $.each(data.errors, function(index, item) {
                            showToastify(item[0], "error")
                        })
                    }
                    if (jqXhr.status == 500 || jqXhr.status == 400) {
                        showToastify(data.message, "error")
                    }
                    // $('#screen_loader').addClass('d-none');

                },
            });
        })

        function renderDownloadZipInputs(data) {
            $("#download-request-zip-inputs").html("");
            let inputs = "";
            if (data?.floor_plan) {
                inputs += `<div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="property_media[]" value="${data.floor_plan.id}" id="download-${data.floor_plan.slug}">
                                        <label class="form-check-label" for="download-${data.floor_plan.slug}">
                                        Download ${data.floor_plan.title}
                                        </label>
                                    </div>
                                </div>`
            }
            if (data?.title_deed) {
                inputs += `<div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="property_media[]" value="${data.title_deed.id}" id="download-${data.title_deed.slug}">
                                        <label class="form-check-label" for="download-${data.title_deed.slug}">
                                        Download ${data.title_deed.title}
                                        </label>
                                    </div>
                                </div>`
            }
            if (data?.owner_passport_copy) {
                inputs += `<div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="property_media[]" value="${data.owner_passport_copy.id}" id="download-${data.owner_passport_copy.slug}">
                                        <label class="form-check-label" for="download-${data.owner_passport_copy.slug}">
                                        Download ${data.owner_passport_copy.title}
                                        </label>
                                    </div>
                                </div>`
            }
            if (data?.signed_listing_form) {
                inputs += `<div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="property_media[]" value="${data.signed_listing_form.id}" id="download-${data.signed_listing_form.slug}">
                                        <label class="form-check-label" for="download-${data.signed_listing_form.slug}">
                                        Download ${data.signed_listing_form.title}
                                        </label>
                                    </div>
                                </div>`
            }
            if (data?.other_documents) {
                data.other_documents?.map(item => {
                    inputs += `<div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="property_media[]" value="${item.id}" id="download-${item.slug}-${item.id}">
                                        <label class="form-check-label" for="download-${item.slug}-${item.id}">
                                        Download ${item.title}
                                        </label>
                                    </div>
                                </div>`
                })
            }

            inputs += `<div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="photos" id="download-photos">
                                        <label class="form-check-label" for="download-photos">
                                        Download Photos
                                        </label>
                                    </div>
                                </div>`;

            let marketingFormAdded = false;
            data?.folder_media?.map(item => {
                if (item.type == "marketing-form-a-leasing-fillable") {
                    if (marketingFormAdded == false) {
                        let ref_number = $("#ref_no").text();
                        let editUrl =
                            "{{ route('admin.documents.index', 'file_slug=marketing-form-a-leasing-fillable') }}";
                        editUrl += `&property_ref_no=${ref_number}`;
                        inputs += `<div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="marketing-form-a-leasing-fillable" id="download-marketing-form-a-leasing-fillable">
                                        <label class="form-check-label" for="download-marketing-form-a-leasing-fillable">
                                        Download Marketing Form A - Leasing Fillable
                                        </label>
                                        <a id="edit-download-marketing-form-a-leasing-fillable" href="${editUrl}" target="_blank">Edit</a>
                                    </div>
                                </div>`;
                        marketingFormAdded = true;
                    }

                    return;
                } else {
                    inputs += `<div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="folder_media[]" value="${item.id}" id="download-${item.slug}-${item.id}">
                                        <label class="form-check-label" for="download-${item.slug}-${item.id}">
                                        Download ${item.title}
                                        </label>
                                    </div>
                                </div>`;
                }
            })

            $("#download-request-zip-inputs").html(inputs)
        }

        $('#sidebarListings').addClass('active');





        function getPropertyDayCount() {
            let id = $('#id').val();
            let consider = $('.all_listing_btn').val();
            if (consider == "all_listings") {

            } else {
                $.ajax({
                    url: "{{ route('ajax.getNoOfDaysListingWasPublished') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        id: id,
                    },
                    success: function(result) {

                        if (result.days > 90) {
                            $('#duplication_reason option[value="Relist (Older than 3 months)"]').prop(
                                'disabled', false);
                        } else {
                            $('#duplication_reason option[value="Relist (Older than 3 months)"]').prop(
                                'disabled', true);
                        }
                    },
                    error: function(jqXhr, json, errorThrown) {
                        console.log(json)
                    },
                });
            }
        }

        function getPermissionToPublish() {
            let agent_id = $('#agent_id').val();

                $.ajax({
                    url: "{{ route('ajax.getPermissionToPublish') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        agent_id: agent_id,
                    },
                    success: function(result) {
                        if (result.status == '1') {
                            if(result.permission==1){
                                $('#savePublishBtn').prop('disabled', true);
                                $('#publish_status').find('option[value="PUBLISHED"]').prop('disabled', true);
                                $("#allowed-limit-reached").addClass('d-none');
                                allowedListing = true;
                            }else{
                                $('#savePublishBtn').prop('disabled', true);
                                $('#publish_status').find('option[value="PUBLISHED"]').prop('disabled', false);
                                allowedListing = false;
                                $("#allowed-limit-reached").removeClass('d-none');
                            }
                        }

                    },
                    error: function(jqXhr, json, errorThrown) {
                        console.log(json)
                    },
                });

        }

    </script>
    @yield('pagejs')
    @stack('script-bottom')
@endsection
