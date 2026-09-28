@extends('admin.layouts.master')
@section('title')
    Location | Sub Communities
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
                        <h4 class="card-title mb-0 ">Sub Communities</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Location</a></li>
                            <li class="breadcrumb-item active">Sub Communities</li>
                        </ol>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="form-check form-switch form-switch-right form-switch-md">
                            @can('Sub Communities Create')
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

                            @can('Sub Communities Edit')
                                <button type="button" id="editBtn" class="btn btn-info d-none action-edit">Edit</button>
                            @endcan
                            <button type="button" class="btn btn-light d-none action-close" id="closeBtn">Close</button>
                            @can('Sub Communities Delete')
                                <button type="button" id="deleteBtn"
                                    class="btn btn-danger d-none action-delete">Delete</button>
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
                        <input type="hidden" id="module" name="module" value="sub_community">
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
                                        <div class="invalid-feedback">
                                            Please choose a region.
                                        </div>
                                    </div>
                                    <div class="col-lg-4 mt-2">
                                        <label for="community_id" class="form-label">Community *</label>
                                    </div>
                                    <div class="col-lg-8 mt-2">
                                        <select class="form-control select2 inputs" id="community_id" name="community_id"
                                            required>
                                            <option value="" disabled selected></option>
                                        </select>
                                        <div class="invalid-feedback">
                                            Please choose a community.
                                        </div>
                                    </div>

                                    <div class="col-lg-4 mt-2">
                                        <label for="name" class="form-label">Sub Community Name*</label>
                                    </div>
                                    <div class="col-lg-8 mt-2">
                                        <input type="text" class="form-control inputs" id="name" name="name"
                                            placeholder="Enter sub community name" required>
                                        <div class="invalid-feedback">
                                            Please enter sub community name.
                                        </div>
                                    </div>



                                    <div class="col-lg-4 mt-2">
                                        <label for="description" class="form-label">Sub Community Description</label>
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
                                        <div class="invalid-feedback">
                                            Please enter full path name.
                                        </div>
                                    </div>

                                    <hr class="mt-4">
                                    <div class="col-lg-4 mt-2">
                                        <label for="alternate_name" class="form-label">Alternate Name (Bayut)</label>
                                    </div>
                                    <div class="col-lg-8 mt-2">
                                        <input type="text" class="form-control inputs" id="alternate_name"
                                            name="alternate_name" placeholder="Enter alternate name for Bayut">
                                        <div class="invalid-feedback">
                                            Please enter altrnate sub community name for Bayut
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
                                        <div class="invalid-feedback">
                                            Please enter latitude.
                                        </div>
                                    </div>

                                    <div class="col-lg-4 mt-2">
                                        <label for="longitude" class="form-label">Map Longitude* </label>
                                    </div>
                                    <div class="col-lg-8 mt-2">
                                        <input type="text" class="form-control inputs" id="longitude"
                                            name="longitude" placeholder="Enter map longitude" required>
                                        <div class="invalid-feedback">
                                            Please enter longitude.
                                        </div>
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
                                        <button type="button" id="searchAtPf" class="btn btn-sm btn-light waves-effect waves-light">
                                            <i class="ri-search-line align-bottom me-1"></i> Search at Property Finder</button>
                                    </div>
                                    <div class="col-lg-12 mt-2">
                                        <div id="google-map" style="height: 300px"></div>
                                    </div>



                                </div>
                            </div>

                        </div>

                        <div class="text-end">
                            <button type="submit" id="submitBtn"
                                class="btn btn-primary d-none action-save">Save</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    @include('admin.location.includes.filterForm')
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
        let isUpdate = false;
        let dataObj = {};
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


        $("#newBtn").click(function() {
            @can('Sub Communities Create')
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
            @can('Sub Communities Edit')
                let id = $(this).data('id');
                $('#id').val(id);
                editData(id);
            @endcan
        });

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
            $.ajax({
                url: "{{ route('ajax.getCommunityDetails') }}",
                type: "get",
                data: {
                    community,
                },
                success: function(result) {
                    console.log(result);
                    let regionId = result?.region?.id;
                    if (regionId) {
                        $("#region_id").val(result.region.id).trigger("change");
                    } else {
                        $("#region_id").val(1).trigger("change");
                    }
                    //$("#region_id").val(result.region.id).trigger("change");
                    // timeout function
                    setTimeout(function() {
                        var communityValue = result.id ?? null;
                        var communityText = result.name;

                        // Create a new option element
                        if (communityValue) {
                            var newOption = new Option(communityText, communityValue, true,
                                true);
                            $('#community_id').append(newOption).trigger('change');

                            // Select the new option
                            $('#community_id').val(communityValue).trigger('change');
                        }

                        $("#name").val(data.sub_community);
                        $("#name").trigger("keyup");
                        $("#latitude").val(data.latitude);
                        $("#latitude").trigger("keyup");
                        $("#longitude").val(data.longitude);
                        $("#longitude").trigger("keyup");
                    }, 1000);

                }
            });

        });

        // $(document).on("change", "#search_location", function(){
        //     let val = $(this).val();
        //     if(val){
        //         var selectedOption = $(this).find("option:selected");
        //         let latitude = $("option:selected", this).attr('data-latitude');
        //         let longitude = $("option:selected", this).attr('data-longitude');
        //         let community = $("option:selected", this).attr('data-community');
        //         let region = $("option:selected", this).data('city');
        //         var regionSelect = document.getElementById("region_id");
        //         console.log(region);
        //         console.log("outside Loop");
        //         for (var i = 0; i < regionSelect.options.length; i++) {
        //             console.log(regionSelect.options[i].text);

        //             if (regionSelect.options[i].text === region) {
        //                 regionSelect.selectedIndex = i;
        //                 break; // Exit the loop once the option is found
        //             }
        //         }

        //     }
        // })

        $(document).on("click", "#saveBtn", function() {
            $("#submitBtn").trigger("click")
        })

        $("#checkAll").change(function() {
            $(".selectable-checkbox").prop("checked", this.checked)
        })

        function viewData(id) {
            @can('Sub Communities View')
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
                    url: "{{ route('admin.location.sub-communities.get') }}",
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        id
                    },
                    success: function(result) {
                        if (result.status == '1') {
                            dataObj = result.data;
                            $('#id').val(result.data.id);
                            if (result.data) {
                                Object.keys(result.data).map((index) => {
                                    $(`#${index}`).val(result.data[index]).trigger("change")
                                })
                                $("#statusCheckbox").prop("checked", result.data.status)
                            }
                            toggleForm(true);
                            scrollTop()
                        } else {
                            swal("Whoops", result.msg, "error");
                        }
                    },
                    error: function(result) {
                        console.log(result);
                    }
                });
            @endcan
        }

        $(document).on('click', '#editBtn', function(event) {
            @can('Sub Communities Edit')
                editData($('#id').val());
                $('#newBtn').addClass('d-none');
            @endcan
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
                        sub_community: true
                    };
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: `${item.sub_community}, ${item.community}, ${item.city}`,
                                id: item.sub_community,
                                'latitude': item.latitude,
                                'longitude': item.longitude,
                                'community': item.community,
                                'sub_community': item.sub_community,
                                'city': item.city,
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

        function editData(id) {
            @can('Sub Communities Edit')
                isUpdate = true;
                $(".inputs").prop("disabled", false)
                $("#mainForm").trigger("reset");
                $("#id").val(id);
                $("#region_id").val("").trigger("change")
                $("#community_id").val("").trigger("change")
                $('#mainForm').removeClass('was-validated');
                $("#saveBtn, #closeBtn").removeClass("d-none")
                $("#editBtn, #deleteBtn, #search_location_btn").addClass("d-none")
                $.ajax({
                    url: "{{ route('admin.location.sub-communities.get') }}",
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        id
                    },
                    success: function(result) {
                        if (result.status == '1') {
                            dataObj = result.data;
                            if (result.data) {
                                Object.keys(result.data).map((index) => {
                                    $(`#${index}`).val(result.data[index]).trigger("change")
                                })
                                $("#statusCheckbox").prop("checked", result.data.status)
                            }
                            toggleForm(true);
                            scrollTop()
                        } else {
                            Swal.fire("Whoops", result.msg, "error");
                        }
                    },
                    error: function(result) {
                        console.log(result);
                    }
                });
            @endcan
        }

        $(document).on("keyup", "#name", function() {
            $("#path_name").val($(this).val() + ", " + $("option:selected", $("#community_id")).attr('data-name') +
                ", " + $(
                    "option:selected", "#region_id").text());
        })


        $(document).on("keyup", "#alternate_name", function() {
            if ($(this).val()) {
                let comm_name = $("option:selected", $("#community_id")).attr('data-alternate_name') ? $(
                    "option:selected", $("#community_id")).attr('data-alternate_name') : $("option:selected", $(
                    "#community_id")).attr('data-name');

                $("#alternate_path_name").val($(this).val() + ", " + comm_name + ", " + $(
                    "option:selected", "#region_id").text());

            } else {
                $("#alternate_path_name").val('');
            }




        })

        $(document).on("change", "#region_id", function() {
            $("#path_name").val($("#name").val() + ", " + $("option:selected", "#community_id").attr('data-name') +
                ", " + $(
                    "option:selected", this).text());
        })

        $(document).on("change", "#community_id", function() {
            $("#path_name").val($("#name").val() + ", " + $("option:selected", this).attr('data-name') + ", " + $(
                "option:selected", "#region_id").text());
        })




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
                url: "{{ route('admin.location.sub-communities.dt') }}",
                type: "post",
                data: function(d) {
                    d.search_sub_communities = filtersData?.search_sub_communities;
                    d.search_communities = filtersData?.search_communities;
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
                $(`@includeIf('admin.location.includes.community_property.filterButtons')`).insertAfter(".dataTables_filter input")
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
                    data: 'alternate_name',
                    name: 'alternate_name',
                }, {
                    data: 'community_id',
                    name: 'community_id',
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
                        return `<a href="javascript:void(0)" class="text-${data.color_code ? data.color_code : 'primary'}" onclick="viewData('` +
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
                        return `${data.community ? (data.community.alternate_name ? data.community.name+' ('+ data.community.alternate_name+')' : data.community.name ) : "-"}`;
                    }
                },
                {
                    "targets": 5,
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
                    "targets": 6,
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
                    "targets": 7,
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
                    "targets": 8,
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
                    targets: 9,
                    orderable: false,
                    visible: true,
                    /*This will hide particular column*/
                    render: function(type, row, data, meta) {
                        let disabled = true;
                        @can('Sub Communities Edit')
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
                    targets: 10,
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
            @can('Sub Communities Edit')
                let id = $(this).data('id');
                let action = $(this).data('do');
                let formData = {
                    id: id,
                    action: action
                }
                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.location.sub-communities.status') }}",
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
            @can('Sub Communities Delete')
                if (ids_array = [], document.getElementsByName("chk_child").forEach((function(e) {
                        if (1 == e.checked) {
                            var t = e.value;
                            ids_array.push(t)

                        }
                    })), "undefined" != typeof ids_array && ids_array.length > 0) {
                    var content = document.createElement('div');
                    content.innerHTML = "You are about to delete <b>" + ids_array.length +
                        " </b>  Sub Communities. Once deleted, you will not be able to use them!";
                    Swal.fire({

                            html: '<div class="mt-3"><lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon><div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you Sure ?</h4><p class="text-muted mx-4 mb-0">Are you Sure You want to Delete ' +
                                ids_array.length + ' Sub Community(s)?</p></div></div>',
                            showCancelButton: !0,
                            confirmButtonClass: "btn btn-danger w-xs me-2 mb-1",
                            confirmButtonText: "Yes, Delete " + ids_array.length + " Sub Community(s).",
                            cancelButtonClass: "btn btn-soft-success w-xs mb-1",
                            buttonsStyling: !1,
                            showCloseButton: !0


                        })
                        .then(function(t) {

                            if (t.isConfirmed) { // on confirm delete the category
                                $.ajax({
                                    url: "{{ route('admin.location.sub-communities.multi.delete') }}",
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
                        $("#community_id").html(options)
                        $("#community_id").val(dataObj?.community_id).trigger("change")
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
                url: isUpdate ? "{{ route('admin.location.sub-communities.update') }}" :
                    "{{ route('admin.location.sub-communities.store') }}",
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
            @can('Sub Communities Delete')
                deleteData($('#id').val());
            @endcan
        });

        $(document).on('click', '.delete-link', function(event) {
            @can('Sub Communities Delete')
                let id = $(this).data('id');
                deleteData(id);
            @endcan
        });



        function deleteData(id) {
            @can('Sub Communities Delete')
                Swal.fire({

                        html: '<div class="mt-3"><lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon><div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you Sure ?</h4><p class="text-muted mx-4 mb-0">Are you Sure You want to Delete this Sub Community ?</p></div></div>',
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
                                url: "{{ route('admin.location.sub-communities.delete') }}",
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    id
                                },
                                success: function(result) {
                                    if (result.status == '1') { //if successfully deleted
                                        showToastify("Sub Community has been deleted successfully",
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

        function deleteMultiple() {
            @can('Sub Communities Delete')
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
                                ids_array.length + ' Sub Community?</p></div></div>',
                            showCancelButton: !0,
                            confirmButtonClass: "btn btn-danger w-xs me-2 mb-1",
                            confirmButtonText: "Yes, Delete " + ids_array.length + " Sub Community(s).",
                            cancelButtonClass: "btn btn-soft-success w-xs mb-1",
                            buttonsStyling: !1,
                            showCloseButton: !0


                        })
                        .then(function(t) {

                            if (t.isConfirmed) { // on confirm delete the category
                                $.ajax({
                                    url: "{{ route('admin.location.sub-communities.delete') }}",
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
                if ($cell.index() === 9 || $cell.index() === 10) {
                    // console.log($cell);
                } else {
                    viewData(data.id);
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
                            <button class="btn btn-primary btn-sm dropdown-toggle action-bulk" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Bulk Actions
                            </button>
                            <div class="dropdown-menu">

                    @can('Sub Communities Delete')
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

        $("#sidebarSubCommunity").addClass('active');

        $(document).on("select2:open", () => {
            document.querySelector(".select2-container--open .select2-search__field").focus()
        })
    </script>
    @include('admin.location.includes.pfSearch')
@endsection
