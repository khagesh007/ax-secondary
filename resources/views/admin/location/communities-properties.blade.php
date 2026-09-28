@extends('admin.layouts.master')
@section('title')
    Location | Community Properties
@endsection
@section('style')
    <!--datatable css-->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet"
        type="text/css" />
    <link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
    <!--datatable responsive css-->
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet"
        type="text/css" />
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

        .scrollable-div {
            max-height: 229px;
            padding: 10px;
            resize: both;
            overflow: auto;
            /* width: 100%; */
        }

        #ppImageDiv img {
            max-width: 50px;
        }

        #map {
            height: 400px;
        }

        .pac-container {
            z-index: 10000 !important;
        }

        .btn-sm-loc {
            border-radius: .2rem !important;
            font-size: .6rem !important;
            padding: 5px !important;
        }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/themes/classic.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"
        type="text/css" />
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title mb-0 ">Community Properties</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Location</a></li>
                            <li class="breadcrumb-item active">Community Properties</li>
                        </ol>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="form-check form-switch form-switch-right form-switch-md">
                            @can('Community Properties Create')
                                <button type="button" id="newBtn" class="btn btn-danger add-btn action-add">
                                    <i class="ri-add-line align-bottom me-1"></i> Add New
                                </button>
                            @endcan
                            <button type="submit" class="btn btn-success d-none action-save" id="saveBtn">Save</button>
                            <div class="d-none" id="search_location_btn" style="float:left; margin: 0 5px;">
                                <select class="form-control select2 select2-hidden-accessible" id="search_location"
                                    name="search_location">
                                    <option value="" disabled="" selected=""
                                        data-select2-id="select2-data-10-zebd">Search Location</option>
                                </select>
                            </div>
                            @can('Community Properties Edit')
                                <button type="button" id="editBtn" class="btn btn-info d-none action-edit">Edit</button>
                            @endcan
                            <button type="button" class="btn btn-light d-none action-close" id="closeBtn">Close</button>
                            @can('Community Properties Delete')
                                <button type="button" id="deleteBtn"
                                    class="btn btn-danger d-none action-delete">Delete</button>
                            @endcan
                            @can('Community Properties Delete')
                                <button class="btn btn-soft-danger action-delete d-none" onclick="deleteMultiple()"><i
                                        class="ri-delete-bin-2-line"></i></button>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row d-none" id="form-section">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form id="mainForm" class="needs-validation" novalidate>
                        <input type="hidden" id="id" name="id">
                        <input type="hidden" id="module" name="module" value="community_property">
                        <div class="row">
                            <div class="col-6">
                                <div class="row mb-3">
                                    <div class="col-lg-4">
                                        <label for="region_id" class="form-label">Emirate*</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <select class="form-control select2 inputs" id="region_id" name="region_id"
                                            required>
                                            <option value="" disabled selected></option>
                                            @foreach ($regions ?? [] as $region)
                                                <option value="{{ $region->id }}">{{ $region->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-lg-4 mt-2">
                                        <label for="community_id" class="form-label">Community *</label>
                                    </div>
                                    <div class="col-lg-8 mt-2">
                                        <select class="form-control select2 inputs" id="community_id" name="community_id"
                                            required>
                                            <option value="" disabled selected></option>
                                        </select>
                                    </div>

                                    <div class="col-lg-4 mt-2">
                                        <label for="name" class="form-label">Sub Community*</label>
                                    </div>
                                    <div class="col-lg-8 mt-2">
                                        <select class="form-control select2 inputs" id="sub_community_id"
                                            name="sub_community_id" required>
                                            <option value="" disabled selected></option>
                                        </select>
                                    </div>

                                    <div class="col-lg-4 mt-2">
                                        <label for="name" class="form-label">Property Name</label>
                                    </div>
                                    <div class="col-lg-8 mt-2">
                                        <input type="text" class="form-control inputs" id="name" name="name"
                                            placeholder="Enter property name" required>
                                    </div>






                                    <div class="col-lg-4 mt-2">
                                        <label for="description" class="form-label">Description</label>
                                    </div>
                                    <div class="col-lg-8 mt-2">
                                        <textarea class="form-control inputs" id="description" name="description" rows="3"
                                            placeholder="Enter description"></textarea>
                                    </div>

                                    <div class="col-lg-4 mt-2">
                                        <label for="path_name" class="form-label">Full Path Name*</label>
                                    </div>
                                    <div class="col-lg-8 mt-2">
                                        <input type="text" class="form-control inputs" id="path_name"
                                            name="path_name" placeholder="Enter full path name" required>
                                    </div>
                                    <div class="col-lg-4 mt-2">
                                        <label for="nameInput" class="form-label">Use on default</label>
                                    </div>
                                    <div class="col-lg-8 mt-2">
                                        <div class="form-check form-switch form-switch-lg form-switch-success"
                                            dir="ltr">
                                            <input type="checkbox" name="default_use" class="form-check-input"
                                                id="default_useCheckbox" checked>
                                            <label class="form-check-label" for="default_useCheckbox"></label>
                                        </div>
                                    </div>
                                    <hr class="mt-2">
                                    <div class="col-lg-4 mt-2">
                                        <label for="path_name" class="form-label">Property Finder*</label>
                                    </div>
                                    <div class="col-lg-8 mt-2">
                                        <div class="d-flex gap-2">
                                            <input type="text" class="form-control inputs" id="pf_id"
                                                   name="pf_id" placeholder="PF Location ID" style="width: 40%;">

                                            <button type="button" id="searchAtPf" class="btn btn-sm btn-light waves-effect waves-light" style="width: 60%;">
                                                <i class="ri-search-line align-bottom me-1"></i> Search at Property Finder
                                            </button>
                                        </div>

                                        <div class="mt-2">
                                            <input type="text" class="form-control" id="pf_path"
                                                   name="pf_path" placeholder="PF Full Path" readonly>
                                        </div>
                                    </div>
                                    <hr class="mt-2">
                                    <div class="col-lg-4 mt-2">
                                        <label for="alternate_name" class="form-label">Alternate Name (Bayut)</label>
                                    </div>
                                    <div class="col-lg-8 mt-2">
                                        <input type="text" class="form-control inputs" id="alternate_name"
                                            name="alternate_name" placeholder="Enter alternate name for Bayut">
                                        <div class="invalid-feedback">
                                            Please enter altrnate community property/building name for Bayut
                                        </div>
                                    </div>


                                    <div class="col-lg-4 mt-2">
                                        <label for="alternate_path_name" class="form-label">Alternate Path (Bayut)</label>
                                    </div>
                                    <div class="col-lg-8 mt-2">
                                        <input type="text" class="form-control inputs" id="alternate_path_name"
                                            name="alternate_path_name"
                                            placeholder="Enter alternate full path name for Bayut">
                                        <div class="invalid-feedback">
                                            Please enter alternate full path name.
                                        </div>
                                    </div>

                                    <div class="col-lg-4 mt-2">
                                        <label for="nameInput" class="form-label">Use on alternate(Bayut)</label>
                                    </div>
                                    <div class="col-lg-8 mt-2">
                                        <div class="form-check form-switch form-switch-lg form-switch-success"
                                            dir="ltr">
                                            <input type="checkbox" name="alternate_use" class="form-check-input"
                                                id="alternate_useCheckbox" checked>
                                            <label class="form-check-label" for="alternate_useCheckbox"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="row mb-3">

                                    <div class="col-lg-4 mt-2">
                                        <label for="latitude" class="form-label">Map Latitude* </label>
                                    </div>
                                    <div class="col-lg-8 mt-2">
                                        <input type="text" class="form-control inputs" id="latitude" name="latitude"
                                            placeholder="Enter map latitude" required>
                                    </div>

                                    <div class="col-lg-4 mt-2">
                                        <label for="longitude" class="form-label">Map Longitude* </label>
                                    </div>
                                    <div class="col-lg-8 mt-2">
                                        <input type="text" class="form-control inputs" id="longitude"
                                            name="longitude" placeholder="Enter map longitude" required>
                                    </div>

                                    <div class="col-lg-4 mt-2">
                                        <label for="nameInput" class="form-label">Status</label>
                                    </div>
                                    <div class="col-lg-8 mt-2">
                                        <div class="form-check form-switch form-switch-lg form-switch-success"
                                            dir="ltr">
                                            <input type="checkbox" name="status" class="form-check-input"
                                                id="statusCheckbox" checked>
                                            <label class="form-check-label" for="statusCheckbox"></label>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 mt-2">

                                    </div>
                                    <div class="col-lg-12 mt-2">
                                        <div id="google-map" style="height: 300px"></div>

                                    </div>

                                </div>
                            </div>

                        </div>

                        <div class="text-end">
                            <button type="submit" id="submitBtn" class="btn btn-primary d-none">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @include('admin.location.includes.community_property.filterForm')

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <table id="communities-table" class="table align-middle" style="width:100%">
                        <thead>
                            <tr class="text-uppercase table-light2">

                                <th scope="col" style="width: 50px;">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="checkAll" value="option"
                                            data-com.bitwarden.browser.user-edited="yes">
                                    </div>
                                </th>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Alternate</th>
                                <th>Sub Community</th>
                                <th>Community</th>
                                <th>Updated By</th>
                                <th>Updated At</th>
                                <th>Created By</th>
                                <th>Created At</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.colVis.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>

    {{-- <script src=" "></script> --}}


    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="{{ URL::asset('assets/js/pages/select2.init.js') }}"></script>
    <script type="text/javascript"
        src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAP_KEY') }}&libraries=places&callback=Function.prototype">
    </script>
    <script>
        let dataObj = {};
        let isUpdate = false;
        let filtersData = [];
        let mapMarker;

        function initMap(lat, long, title = "") {
            const myLatLng = {
                lat: lat,
                lng: long
            };
            const map = new google.maps.Map(document.getElementById("google-map"), {
                zoom: 18,
                center: myLatLng,
            });

            mapMarker = new google.maps.Marker({
                position: myLatLng,
                map,
                title: title,
            });

            map.addListener("click", (e) => {
                placeMarker(e.latLng);
            });
        }

        function placeMarker(location) {
            if (mapMarker) {
                mapMarker.setPosition(location);
            } else {
                mapMarker = new google.maps.Marker({
                    position: location,
                    map: map
                });
            }
            $("#latitude").val(mapMarker.getPosition().lat())
            $("#longitude").val(mapMarker.getPosition().lng())
        }

        $(document).on("change", "#latitude, #longitude", function() {
            let latitude = $("#latitude").val();
            let longitude = $("#longitude").val();
            let path = $("#path_name").val();
            if (latitude && longitude) {
                initMap(parseFloat(latitude), parseFloat(longitude), path)
            }
        })

        $("#region_id").select2({
            placeholder: 'Select region',
            allowClear: true
        })

        $("#community_id").select2({
            placeholder: 'Select community',
            allowClear: true
        })

        $("#sub_community_id").select2({
            placeholder: 'Select sub community',
            allowClear: true
        })

        $("#newBtn").click(function() {
            @can('Community Properties Create')
                isUpdate = false;
                initMap(25.2048, 55.2708, "Dubai")
                $('#mainForm').trigger('reset');
                $('#mainForm').removeClass('was-validated');
                $(".inputs").prop("disabled", false)
                $(".inputs").val("").trigger("change")
                $(".inputs").removeClass("is-invalid")
                $("#id").val("")
                $("#saveBtn, #closeBtn, #search_location_btn").removeClass("d-none");
                $("#newBtn, #editBtn, #deleteBtn").addClass("d-none");
                toggleForm(true)
            @endcan
        });

        $(document).on("click", "#closeBtn", function() {
            toggleForm(false)
        })

        $(document).on('click', '.edit-link', function(
            event) { //edit link click event it will fetch single cateogry details and put it on form
            @can('Community Properties Edit')
                let id = $(this).data('id');
                $('#id').val(id);
                editData(id);
            @endcan
        });

        function toggleForm(show) {
            if (show == undefined) {
                $("#form-section").toggleClass("d-none")
                return
            }
            if (show) {
                $("#form-section").removeClass("d-none");
            } else {
                $("#saveBtn, #closeBtn, #editBtn ,#search_location_btn").addClass("d-none");
                $("#newBtn").removeClass("d-none");
                $("#form-section").addClass("d-none");
            }
        }

        $('#search_location').on('select2:select', function(e) {
            var data = e.params.data;
            var latitude = data.latitude;
            var longitude = data.longitude;
            var region = data.city;
            var community = data.community;
            var sub_community = data.sub_community;

            console.log(data);
            $.ajax({
                url: "{{ route('ajax.getSubCommunityDetails') }}",
                type: "get",
                data: {
                    sub_community,
                },
                success: function(result) {
                    console.log(result);
                    let regionId = result?.region?.id;
                    if (regionId) {
                        $("#region_id").val(result.region.id).trigger("change");
                    } else {
                        $("#region_id").val(1).trigger("change");
                    }
                    setTimeout(function() {

                        var communityValue = result.community?.id ?? null;
                        var communityText = result.community?.name ?? null;
                        if (communityValue) {
                            var newOption = new Option(communityText, communityValue, true,
                                true);
                            $('#community_id').append(newOption).trigger('change');

                            // Select the new option
                            $('#community_id').val(communityValue).trigger('change');
                        }


                        $("#name").val(data.tower);
                        $("#name").trigger("keyup");
                        $("#latitude").val(data.latitude);
                        $("#latitude").trigger("keyup");
                        $("#longitude").val(data.longitude);
                        $("#longitude").trigger("keyup");
                    }, 1000);
                    setTimeout(function() {
                        var subCommunityValue = result.id ?? null;
                        var subCommunityText = result.name;

                        // Create a new option element
                        if (subCommunityValue) {
                            var newOption = new Option(subCommunityText, subCommunityValue,
                                true, true);
                            $('#sub_community_id').append(newOption).trigger('change');

                            // Select the new option
                            $('#sub_community_id').val(subCommunityValue).trigger('change');
                        }
                    }, 2000);

                }
            });

        });
        $('#search_location').select2({
            placeholder: 'Select Location',
            allowClear: true,
            closeOnSelect: true,
            ajax: {
                url: "{{ route('ajax.fetchPfLocationData') }}",
                dataType: 'json',
                type: 'GET',
                delay: 250,
                data: function(term, page) {
                    return {
                        search: term,
                        community_property: true
                    };
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: `${item.tower}, ${item.sub_community}, ${item.community}, ${item.city}`,
                                id: item.sub_community,
                                'latitude': item.latitude,
                                'longitude': item.longitude,
                                'community': item.community,
                                'sub_community': item.sub_community,
                                'city': item.city,
                                'tower': item.tower,
                                'path': item.path,
                                'alternate_path_name': item.alternate_path_name,
                                'type': item.type
                            }
                        })

                    };
                },

            },
            minimumInputLength: 3
        });

        $(document).on("click", "#saveBtn", function() {
            $("#submitBtn").trigger("click")
        })

        $("#checkAll").change(function() {
            $(".selectable-checkbox").prop("checked", this.checked)
        })

        function viewProperty(id) {
            @can('Community Properties View')
                isUpdate = true;
                $(".inputs").prop("disabled", true)
                $("#mainForm").trigger("reset");
                $("#id").val(id);
                $("#region_id").val("").trigger("change")
                $("#community_id").val("").trigger("change")
                $('#mainForm').removeClass('was-validated');
                $("#saveBtn").addClass("d-none")
                $("#editBtn, #deleteBtn, #newBtn, #closeBtn").removeClass("d-none")

                $.ajax({
                    url: "{{ route('admin.location.community-properties.get') }}",
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        id
                    },
                    success: function(result) {
                        if (result.status == '1') {
                            $('#id').val(result.data.id);
                            if (result.data) {
                                dataObj = result.data;
                                Object.keys(result.data).map((index) => {
                                    $(`#${index}`).val(result.data[index]).trigger("change")
                                })
                                let portal_ids = result.data.portal_ids ? JSON.parse(result.data.portal_ids) : [];
                                $("#pf_id").val(portal_ids?.property_finder_id ?? '');
                                $("#pf_path").val(portal_ids?.pf_path ?? '');

                                $("#statusCheckbox").prop("checked", result.data.status)
                                $("#alternate_useCheckbox").prop("checked", result.data.alternate_use)
                                $("#default_useCheckbox").prop("checked", result.data.default_use)
                            }
                            toggleForm(true);
                            scrollTop()
                            $('#newBtn').addClass('d-none');

                        } else {
                            showToastify(result.msg, "error")
                        }
                    },
                    error: function(result) {
                        console.log(result);
                    }
                });
            @endcan
        }

        $(document).on('click', '#editBtn', function(event) {
            @can('Community Properties Edit')
                editData($('#id').val());
                $('#newBtn').addClass('d-none');
            @endcan
        });


        function editData(id) {
            @can('Community Properties Edit')
                isUpdate = true;
                $(".inputs").prop("disabled", false)
                $("#mainForm").trigger("reset");
                $("#id").val(id);
                $("#region_id").val("").trigger("change")
                $("#community_id").val("").trigger("change")
                $("#sub_community_id").val("").trigger("change")
                $('#mainForm').removeClass('was-validated');
                $("#saveBtn, #closeBtn").removeClass("d-none")
                $("#editBtn, #deleteBtn, #search_location_btn").addClass("d-none")
                $.ajax({
                    url: "{{ route('admin.location.community-properties.get') }}",
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        id
                    },
                    success: function(result) {
                        if (result.status == '1') {
                            if (result.data) {
                                dataObj = result.data;
                                Object.keys(result.data).map((index) => {
                                    $(`#${index}`).val(result.data[index]).trigger("change")
                                })
                                let portal_ids = result.data.portal_ids ? JSON.parse(result.data.portal_ids) : [];
                                $("#pf_id").val(portal_ids?.property_finder_id ?? '');
                                $("#pf_path").val(portal_ids?.pf_path ?? '');
                                $("#statusCheckbox").prop("checked", result.data.status)
                                $("#alternate_useCheckbox").prop("checked", result.data.alternate_use)
                                $("#default_useCheckbox").prop("checked", result.data.default_use)
                            }
                            toggleForm(true);
                            scrollTop()
                        } else {
                            showToastify(result.msg, "error")
                        }
                    },
                    error: function(result) {
                        console.log(result);
                    }
                });
            @endcan
        }

        $(document).on("keyup", "#name", function() {
            $("#path_name").val($(this).val() + ", " + $("option:selected", $("#sub_community_id")).attr(
                    'data-name') + ", " +
                $("option:selected", $("#community_id")).attr('data-name') + ", " + $("option:selected",
                    "#region_id")
                .text());


        });

        $(document).on("keyup", "#alternate_name", function() {
            if ($(this).val()) {
                let comm_name = $("option:selected", $("#community_id")).attr('data-alternate_name') ? $(
                    "option:selected", $("#community_id")).attr('data-alternate_name') : $("option:selected", $(
                    "#community_id")).attr('data-name');


                let sub_comm_name = $("option:selected", $("#sub_community_id")).attr('data-alternate_name') ? $(
                    "option:selected", $("#sub_community_id")).attr('data-alternate_name') : $(
                    "option:selected", $("#sub_community_id")).attr('data-name');


                $("#alternate_path_name").val($(this).val() + ", " + sub_comm_name + ", " + comm_name + ", " + $(
                    "option:selected", "#region_id").text());

            } else {
                $("#alternate_path_name").val('');
            }




        })



        $(document).on("change", "#region_id", function() {
            $("#path_name").val($("#name").val() + ", " + $("option:selected", $("#sub_community_id")).text() +
                ", " + $("option:selected", "#community_id").attr('data-name') + ", " + $("option:selected",
                    this).text());
        })

        $(document).on("change", "#community_id", function() {
            $("#path_name").val($("#name").val() + ", " + $("option:selected", $("#sub_community_id")).attr(
                    'data-name') +
                ", " + $("option:selected", this).attr('data-name') + ", " + $("option:selected", "#region_id")
                .text());
        })
        $(document).on("change", "#sub_community_id", function() {
            $("#path_name").val($("#name").val() + ", " + $("option:selected", $("#sub_community_id")).attr(
                    'data-name') +
                ", " + $("option:selected", "#community_id").attr('data-name') + ", " + $("option:selected",
                    "#region_id")
                .text());
        })

        $('#search_communities').select2({
            placeholder: 'Select Community',
            allowClear: true,
            closeOnSelect: false,
            ajax: {
                url: "{{ route('ajax.getCommunities') }}",
                dataType: 'json',
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                id: `${item.id}`,
                                text: `${item.name}`,
                            }
                        })

                    };
                },

            }
        });

        $('#_search_sub_communities').select2({
            placeholder: 'Select Community',
            allowClear: true,
            closeOnSelect: false,
            ajax: {
                url: "{{ route('ajax.getSubCommunities') }}",
                dataType: 'json',
                type: 'GET',
                data:{
                    community_id: function(){
                        return $('#search_communities').val();
                    }
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                id: `${item.id}`,
                                text: `${item.name}`,
                            }
                        })

                    };
                },

            }
        });

        $('#search_sub_communities').select2({
                placeholder: 'Tower, community, locality',
                allowClear: true,
                closeOnSelect: false,
                ajax: {
                    url: "{{ route('ajax.sub-community.property.select2') }}",
                    dataType: 'json',
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
                                    'type': item.type
                                }
                            })
                        }
                    }
                }
            });

        let dataTable = $('#communities-table').DataTable({
            "order": [
                [0, "DESC"]
            ],
            language: {
                searchPlaceholder: "Search Sub Commnunities"
            },

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

            }],
            createdRow: function(row, data, index) {
                $('#communities-table tbody').css('cursor', 'pointer');

            },
            pageLength: 10,
            responsive: true,
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "{{ route('admin.location.community-properties.dt') }}",
                type: "post",
                data: function(d) {
                    d.search_sub_communities = filtersData?.search_sub_communities;
                    d.search_communities = filtersData?.search_communities;
                    d.search_community_property = filtersData?.search_community_property;
                },
                error: function() {
                    $(".users-error").html("");
                    $("#dataTable").append(
                        '<tbody class="users-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>'
                    );
                    $("#dataTable_processing").css("display", "none");
                },

            },
            fnInitComplete: function(oSettings, json) {
                let first = $("#dataTable_wrapper .row div:first");
                first.addClass("col-md-5").removeClass("col-md-6")
                $(first).next().addClass("col-md-7").removeClass("col-md-6")
                $(".dataTables_filter label").addClass(
                    "d-flex justify-between-between align-items-center gap-2")
                $(`@includeIf('admin.location.includes.filterButtons')`).insertAfter(".dataTables_filter input")
            },

            "aoColumns": [{
                    data: null,
                    orderable: false,
                },
                {
                    data: 'id',
                    name: 'id',
                },
                {
                    data: 'name',
                    name: 'name',
                },
                {
                    data: 'region_id',
                    name: 'region_id',
                }, {
                    data: 'community_id',
                    name: 'community_id',
                }, {
                    data: 'sub_community_id',
                    name: 'sub_community_id',
                },
                {
                    data: null,
                    orderable: false,
                },
                {
                    data: null,
                    orderable: false,

                },
                {
                    data: null,
                    orderable: false,
                },
                {
                    data: null,
                    orderable: false,
                },
                {
                    data: null,
                    orderable: false,
                },
                {
                    data: null,
                    orderable: false,
                }
            ],
            "columnDefs": [{ //manupulate a table column
                    "targets": 0,
                    orderable: true,
                    "data": "id",
                    render: function(type, row, data, meta) {
                        return `<div class="form-check"><input class="form-check-input selectable-checkbox" type="checkbox" name="chk_child" value="${data.id}"></div>`;
                    }
                },
                {
                    "targets": 1,
                    orderable: true,
                    "data": "id",
                    render: function(type, row, data, meta) {
                        return data.id;
                    }
                },

                {
                    "targets": 2,
                    orderable: true,
                    "data": "title",
                    render: function(type, row, data, meta) {
                        return `<a href="javascript:void(0)" class="text-${data.color_code ? data.color_code : 'primary'}" onclick="viewProperty('` +
                            data.id + `')">` + data.name + `</a>`;
                    }
                },
                {
                    "targets": 3,
                    orderable: true,
                    render: function(type, row, data, meta) {
                        return `${data.alternate_name ? data.alternate_name : "-"}`;
                    }
                },


                {
                    "targets": 4,
                    orderable: true,
                    render: function(type, row, data, meta) {
                        return `${data.sub_community ? (data.sub_community.alternate_name ? data.sub_community.name+' ('+ data.sub_community.alternate_name+')' : data.sub_community.name ) : "-"}`;
                    }
                },



                {
                    "targets": 5,
                    orderable: true,
                    render: function(type, row, data, meta) {
                        return `${data.community ? (data.community.alternate_name ? data.community.name+' ('+ data.community.alternate_name+')' : data.community.name ) : "-"}`;
                    }
                },


                {
                    "targets": 6,
                    orderable: true,
                    "data": "id",
                    render: function(type, row, data, meta) {
                        if (!data.updated_by) return "-";
                        return `<div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-2">
                                        <img src="${data.updated_by.thumb_url}" alt="" class="avatar-xs rounded-circle">
                                    </div>
                                    <div class="flex-grow-1">${data.updated_by.name}</div>
                                </div>`;
                    }
                },
                {
                    "targets": 7,
                    orderable: true,
                    "data": "id",
                    render: function(type, row, data, meta) {
                        if (data.updated_at == null) {
                            return '-';
                        } else {
                            return moment(data.updated_at).format('MM/DD/YYYY hh:mm:ss');
                        }
                    }
                },
                {
                    "targets": 8,
                    orderable: true,
                    "data": "id",
                    render: function(type, row, data, meta) {
                        if (!data.created_by) return "-";
                        return `<div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-2">
                                        <img src="${data.created_by.thumb_url}" alt="" class="avatar-xs rounded-circle">
                                    </div>
                                    <div class="flex-grow-1">${data.created_by.name}</div>
                                </div>`;
                    }
                }, {
                    "targets": 9,
                    orderable: true,
                    "data": "id",
                    render: function(type, row, data, meta) {
                        if (data.created_at == null) {
                            return '-';
                        } else {
                            return moment(data.created_at).format('MM/DD/YYYY hh:mm:ss');
                        }
                    }

                },
                {
                    targets: 10,
                    orderable: false,
                    visible: true,
                    /*This will hide particular column*/
                    render: function(type, row, data, meta) {
                        let disabled = true;
                        @can('Community Properties Edit')
                            disabled = false;
                        @endcan
                        if (data.status == '1') {
                            return `<div  class="form-check form-switch form-switch-lg form-switch-success" dir="ltr">
                                        <input ${disabled ? "disabled" : ""} type="checkbox" data-id="${data.id}" data-do="0" name="status" class="form-check-input status-change" checked></div>`;
                        } else {
                            return `<div  class="form-check form-switch form-switch-lg form-switch-success" dir="ltr">
                                        <input ${disabled ? "disabled" : ""} data-id="${data.id}" data-do="1" type="checkbox" name="status" class="form-check-input status-change"></div>`;
                        }
                    }
                },
                {
                    targets: 11,
                    orderable: false,
                    visible: true,
                    /*This will hide particular column*/
                    render: function(type, row, data, meta) {
                        return `<div class="dropdown d-inline-block">
                                                       <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                           <i class="ri-more-fill align-middle"></i>
                                                       </button>
                                                       <ul class="dropdown-menu dropdown-menu-end">
                                                           <li><a data-id="` + data.id + `" class="dropdown-item edit-item-btn edit-link"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit</a></li>
                                                           <li>
                                                               <a  data-id="` + data.id + `" class="dropdown-item remove-item-btn delete-link">
                                                                   <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete
                                                               </a>
                                                           </li>
                                                       </ul>
                                                   </div>`
                    }
                }
            ]
        });



        $('#communities-table tbody').on('change', 'input.status-change', function() {
            @can('Community Properties Edit')
                let id = $(this).data('id');
                let action = $(this).data('do');
                let formData = {
                    id: id,
                    action: action
                }
                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.location.community-properties.status') }}",
                    data: formData,
                    dataType: 'json',
                    success: function(data) {
                        if (data.status == '1') {
                            showToastify(data.msg, "success")
                            dataTable.row().draw();
                        }
                    },
                    error: function(data) {
                        console.log('Error:', data);
                    }
                });
            @endcan
        });




        $(document).on("submit", "#searchForm", function(e) {
            e.preventDefault();
            filtersData = $("#searchForm").serialize();
            filtersData = urldecode(filtersData);
            filtersData = QueryStringToJSON(filtersData);

            dataTable.ajax.reload();
        })


        $(document).on("click", "#clearSearchBtn", function() {
            $("#search_status").prop("checked", false);
            filtersData.search_status = null;
            $(".search-inputs").val("").trigger("change");
            $("input[type='checkbox'].search-inputs").prop("checked", false).trigger("change");
            //$("#searchSearchBtn").trigger("click")
            $("#search_active").val('1');
            $("#search_inactive").val('0');
            dataTable.ajax.reload();
        })

        $(document).on("click", "#refreshTableButton", function() {
            $("#searchSearchBtn").trigger("click")
        })

        function toggleAdvanceSearch() {
            $("#collapseFilterForm").toggleClass("show")
        }


        function deleteMultiple() {
            @can('Community Properties Delete')
                if (ids_array = [], document.getElementsByName("chk_child").forEach((function(e) {
                        if (1 == e.checked) {
                            var t = e.value;
                            ids_array.push(t)

                        }
                    })), "undefined" != typeof ids_array && ids_array.length > 0) {
                    var content = document.createElement('div');
                    content.innerHTML = "You are about to delete <b>" + ids_array.length +
                        "</b> Communities. Once deleted, you will not be able to use them!";
                    Swal.fire({

                            html: '<div class="mt-3"><lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon><div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you Sure ?</h4><p class="text-muted mx-4 mb-0">Are you Sure You want to Delete ' +
                                ids_array.length + ' Community Property(s)?</p></div></div>',
                            showCancelButton: !0,
                            confirmButtonClass: "btn btn-danger w-xs me-2 mb-1",
                            confirmButtonText: "Yes, Delete " + ids_array.length + " Community Property(s).",
                            cancelButtonClass: "btn btn-soft-success w-xs mb-1",
                            buttonsStyling: !1,
                            showCloseButton: !0


                        })
                        .then(function(t) {

                            if (t.isConfirmed) { // on confirm delete the category
                                $.ajax({
                                    url: "{{ route('admin.location.community-properties.multi.delete') }}",
                                    type: 'POST',
                                    dataType: 'json',
                                    data: {
                                        ids: ids_array
                                    },
                                    success: function(result) {
                                        if (result.status == '1') {
                                            showToastify(result.msg, "success")
                                            $('.multi_options').remove();
                                            dataTable.row().draw();
                                        } else {
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



        $(document).on("change", "#region_id", function() {
            let region_id = $("option:selected", this).val();
            $("#community_id").val("").trigger("change");
            $("#community_id").html("");
            if (region_id) {
                $.ajax({
                    url: "{{ route('ajax.communities') }}",
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        region_id
                    },


                    success: function(result) {
                        let options = "<option value='' disabled selected></option>";

                        result?.data.map(item => {
                            let alter_name = '';
                            if (item.alternate_name) {
                                alter_name = `(${item.alternate_name})`;
                            }
                            options +=
                                `<option value="${item.id}" data-name="${item.name}" data-alternate_name="${item.alternate_name ? item.alternate_name : ''}">${item.name} ${alter_name}</option>`;
                        })


                        $("#community_id").html(options);
                        $("#community_id").val(dataObj?.community_id || "").trigger("change");
                    },
                    error: function(result) {
                        console.log(result);
                    }
                });
            }

        })
        $(document).on("change", "#community_id", function() {
            let community_id = $("option:selected", this).val();
            $("#sub_community_id").val("").trigger("change");
            $("#sub_community_id").html("");
            if (community_id) {
                $.ajax({
                    url: "{{ route('ajax.sub-communities') }}",
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        community_id
                    },
                    success: function(result) {


                        let options = "<option value='' disabled selected></option>";


                        result?.data.map(item => {
                            let alter_name = '';
                            if (item.alternate_name) {
                                alter_name = `(${item.alternate_name})`;
                            }
                            options +=
                                `<option value="${item.id}" data-name="${item.name}" data-alternate_name="${item.alternate_name ? item.alternate_name : ''}">${item.name} ${alter_name}</option>`;
                        });




                        $("#sub_community_id").html(options)
                        $("#sub_community_id").val(dataObj?.sub_community_id || "").trigger("change");
                    },
                    error: function(result) {
                        console.log(result);
                    }
                });
            }

        })

        $(document).on('submit', '#mainForm', function(event) {
            $("#submitBtn").prop('disabled', true);
            event.preventDefault();
            var formData = new FormData($(this)[0]);
            $.ajax({
                url: isUpdate ? "{{ route('admin.location.community-properties.update') }}" :
                    "{{ route('admin.location.community-properties.store') }}",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function(result) {
                    if (result.status == '1') {
                        showToastify(result.msg, "success")
                        $("#submitBtn").prop('disabled', false); //enable submit button
                        $('#mainForm').trigger('reset'); //
                        toggleForm(false)
                        dataTable.row()
                            .draw(); // redraw the datatable show we don't need to reload the page to see the changes
                        isUpdate =
                            false; // set isUpdate to false so that when we try to add new category it will be create request
                    } else if (result.status == '2') {
                        $("#submitBtn").prop('disabled', false);
                        let error_message = '';

                        $.each(result.errors, function(key, value) {
                            error_message += '' + key + ': ' + value + ' | ';
                        });
                        showToastify(error_message, "error")
                    } else {
                        $("#submitBtn").prop('disabled', false);
                        showToastify(result.msg, "error")
                    }
                },
                error: function(jqXhr, json, errorThrown) {
                    $("#submitBtn").prop('disabled', false);
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


        $(document).on('click', '#deleteBtn', function(event) {
            @can('Community Properties Delete')
                deleteData($('#id').val());
            @endcan
        });

        $(document).on('click', '.delete-link', function(event) {
            @can('Community Properties Delete')
                let id = $(this).data('id');
                deleteData(id);
            @endcan
        });




        function deleteData(id) {
            @can('Community Properties Delete')
                Swal.fire({

                        html: '<div class="mt-3"><lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon><div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you Sure ?</h4><p class="text-muted mx-4 mb-0">Are you Sure You want to Delete this Community Property ?</p></div></div>',
                        showCancelButton: !0,
                        confirmButtonClass: "btn btn-danger w-xs me-2 mb-1",
                        confirmButtonText: "Yes, Delete It!",
                        cancelButtonClass: "btn btn-soft-success w-xs mb-1",
                        buttonsStyling: !1,
                        showCloseButton: !0


                    })
                    .then(function(t) {

                        if (t.isConfirmed) { // on confirm delete the category
                            $.ajax({
                                url: "{{ route('admin.location.community-properties.delete') }}",
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    id
                                },
                                success: function(result) {
                                    if (result.status == '1') { //if successfully deleted
                                        showToastify("Community Property has been deleted successfully",
                                            "success")
                                        dataTable.row()
                                            .draw(); // redraw the datatable show we don't need to reload the page to see the changes
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
            @endcan
        }

        function highlightRow(row) {
            row.removeClass('selected');
            dataTable.$('tr.selected').removeClass('selected');
            row.addClass('selected');
        } // highlightRow


        dataTable.on('click', 'tbody tr', function(e) {
            var $cell = $(e.target).closest('td'),
                msg;
            //console.log('Index ==========='+$cell.index());
            if ($cell.index() != 0) {
                var data = dataTable.row(this).data();
                //
                if ($cell.index() === 11 || $cell.index() === 10) {
                    // console.log($cell);
                } else {
                    viewProperty(data.id);
                    highlightRow($(this));

                }
            } // end if
        }); // table row click

        function updateMultiOption() {
            var countCheckedCheckboxes = $('input[name="chk_child"]:checked').length;
            console.log(countCheckedCheckboxes);
            $('#bulk_leads_count').html(countCheckedCheckboxes);
            $('#count_selected').html(countCheckedCheckboxes);
            if (countCheckedCheckboxes >= 1) {
                if ($('.multi_options').length === 0) {
                    $(`<div class="multi_options" style="display:inline-block; margin-left:10px;">
                          <div class="btn-group">
                            <button class="btn btn-primary btn-sm dropdown-toggle  action-bulk" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Bulk Actions
                            </button>
                            <div class="dropdown-menu">

                    @can('Community Properties Delete')
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
        $("#checkAll").change(function() {
            $(".selectable-checkbox").prop("checked", this.checked)
            updateMultiOption();
        })
        $(document).on('change', 'tbody tr .selectable-checkbox', function() {

            $(this).parents('tr').toggleClass('selected');
            var checked = $(this).is(':checked');
            if (!checked) {
                $(this).prop('checked', false);
            } else {
                $(this).prop('checked', true);
            }
            updateMultiOption();
        });

        dataTable.on('draw.dt', function() {
            $(".chk_child").prop("checked", false);
            $('.multi_options').remove();
            $('#checkAll').prop("checked", false);
        });
        $("#sidebarLocations").addClass('collapse show');
        $('#locationActive').addClass('active');

        $("#sidebarCommunityProp").addClass('active');
        $(document).on("select2:open", () => {
            document.querySelector(".select2-container--open .select2-search__field").focus()
        })
    </script>
    @include('admin.location.includes.pfSearch')
@endsection
