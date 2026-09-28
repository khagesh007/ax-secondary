@extends('admin.layouts.master')
@section('title')
    Developers MetaData
@endsection
@section('style')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" type="text/css" />

    <style>
        .center-tabs {
            display: flex;
            justify-content: center;
        }

        h2 {
            color: var(--primary-text);
            background: -webkit-linear-gradient(#b7846a, #f27a46);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        h5 {
            color: var(--primary-text);
            background: -webkit-linear-gradient(#b7846a, #f27a46);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        table tr td {
            padding: 0px !important
        }
    </style>
@endsection
@section('content')
    @if (session()->has('message'))
        <div class="alert alert-success" id="alertMessage">
            {{ session()->get('message') }}
        </div>
    @endif

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title mb-0">Offplan </h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Import</a></li>
                            <li class="breadcrumb-item active">Offplan Properties</li>
                        </ol>
                    </div>


                </div>
            </div>
        </div>
    </div>
    <form action="{{ route('jobs.public.importPropertiesPOST') }}" method="post">
        @csrf
        <div class="row">
            <div class="col-md-12 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class=" text-center">
                            <h3 class="">
                                Import Properties
                            </h3>
                            <hr>
                        </div>
                        <div class="row">
                            <div class="col-md-3" id="developer_col">
                                <select class="form-control select2 inputs" name="developer_id" id="developer_id">
                                    <option selected disabled value="">Select Developer</option>
                                    @foreach ($developers ?? [] as $devloper)
                                        <option value="{{ $devloper->id }}"> {{ $devloper->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"> Please choose Developer. </div>
                            </div>

                            <div class="col-md-3" id="project_col">
                                <select class="form-control select2 inputs" name="project_id" id="project_id">
                                    <option selected disabled value="">Select Project</option>

                                </select>
                                <div class="invalid-feedback"> Please choose Project.</div>
                            </div>

                        </div>


                    </div>
                </div>
            </div>
        </div>



        <div id="propertiesTable" class="d-none">

        </div>

        <input type="hidden" id="typeMapping" name="typeMapping">
        <input type="hidden" id="bedMapping" name="bedMapping">

        <input type="hidden" id="locationMapping" name="locationMapping">
    </form>
@endsection
@section('script')
    @include('admin.partials.datatable')

    <script>
        let s_sub_communities = [];
        let s_properties = [];
        let typeMapping = [];
        let bedMapping = [];
        let locationMapping = [];
        $(document).ready(function() {
            $('#developer_id').change(function() {
                let developer_id = $(this).val();
                if (developer_id) {
                    $.ajax({
                        url: "{{ route('jobs.public.fetchProjects') }}",
                        type: "POST",
                        data: {
                            developer_id: developer_id,
                            _token: "{{ csrf_token() }}",
                        },
                        success: function(response) {
                            if (response) {

                                if (response.status == 1) {
                                    let data = response.data;
                                    $('#project_id').empty();
                                    $('#project_id').append(
                                        '<option selected disabled value="">Select Project</option>'
                                    );
                                    $.each(data, function(key, value) {
                                        $('#project_id').append('<option value="' +
                                            value.id + '">' + value.project_name +
                                            '</option>');
                                    });
                                }

                            } else {
                                $('#project_id').empty();
                            }
                        },
                        error: function(xhr) {
                            console.log(xhr);
                        }
                    });
                }
            });

            $('#project_id').change(function() {
                $('#propertiesTable').addClass('d-none');
                let project_id = $(this).val();
                let developer_id = $('#developer_id').val();
                if (project_id) {
                    $.ajax({
                        url: "{{ route('jobs.public.fetchListing') }}",
                        type: "POST",
                        data: {
                            developer_id: developer_id,
                            project_id: project_id,
                            _token: "{{ csrf_token() }}",
                        },
                        success: function(response) {
                            if (response) {
                                $('#propertiesTable').removeClass('d-none');
                                $('#propertiesTable').empty();
                                $('#propertiesTable').append(response);
                                $('#developersTable').DataTable({
                                    "order": [
                                        [0, "desc"]
                                    ]
                                });

                                $('.search_locations').select2({
                                    placeholder: 'Select locations',
                                    allowClear: true,
                                    closeOnSelect: true,

                                    ajax: {
                                        url: '{{ route('ajax.sub-community.property.select2') }}',
                                        dataType: 'json',
                                        type: 'GET',
                                        data: function(term, page) {
                                            return {
                                                q: term, // search term
                                                communities: $(
                                                        "#search_communities")
                                                    .val(),
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
                                                        'alternate_path_name': item
                                                            .alternate_path_name,
                                                        'type': item.type
                                                    }
                                                })
                                            }
                                        }
                                    }
                                });

                                $('.select22').select2({
                                    placeholder: 'Select Option',
                                    allowClear: false,
                                    closeOnSelect: true,
                                });

                            }
                        },
                        error: function(xhr) {
                            console.log(xhr);
                            $('#propertiesTable').addClass('d-none');

                        }
                    });
                }
            })




            $(document).on('select2:select', '.search_locations', function(e) {
                $("#search_sub_communities").val("");
                $("#search_properties").val("");


                let text = $(this).val();
                let data = e.params.data;
                let maplocation = $('#maplocation option:selected').val();
                let maplocationtext = $(this).text();
                let lat = data.lat;
                let long = data.long;
                let path = data.path;
                let alternate_path_name = data.alternate_path_name;
                let type = data.type;

                let locationMappingItem = {
                    title: maplocation,
                    community: null,
                    sub_community: null,
                    community_property: null
                };

                if (data.type == 'sub_community') {
                    locationMappingItem.sub_community = data.id;
                } else {
                    locationMappingItem.community_property = data.id;
                }

                locationMapping.push(locationMappingItem);

                $('#locationMapping').val(JSON.stringify(locationMapping));
                $('#maplocation option[value="' + maplocation + '"]').remove();

                if (data.type == 'sub_community') {
                    s_sub_communities.push(data.id);
                } else {
                    s_properties.push(data.id);
                }

                $("#search_sub_communities").val(s_sub_communities);
                $("#search_properties").val(s_properties);

                let _text = '<div class="mt-2 rmDiv">Mapping: <b><span>' + maplocation +
                    '</span></b> with <b><span>' + maplocationtext +
                    '</span></b> <span class="ms-2"><i class="mdi-delete-circle mdi text-danger deSelectLocation" data-location="' +
                    maplocation + '"></i></span></div>'

                $('#appendLocation').append(_text)
                $(this).val(null).trigger('change');
            });


            $(document).on('change', '#check-all-input', function(e) {
                let checked = $(this).is(':checked');
                if (checked) {
                    $('.check-all').prop('checked', true);
                } else {
                    $('.check-all').prop('checked', false);
                }
            });



            $(document).on('click', '.deSelectLocation', function(e) {
                var location = $(this).data("location");
                var newOption = $("<option></option>").attr("value", location).text(location);
                $("#maplocation").append(newOption);

                locationMapping = locationMapping.filter(function(item) {
                    return item.title != location;
                });




                $('#locationMapping').val(JSON.stringify(locationMapping));
                $(this).closest('.rmDiv').remove();
            });


            $(document).on('select2:unselect', '.search_locations', function(e) {
                let data = e.params.data;
                let text = $(this).text();
                let maplocation = $('#maplocations option:selected').val();

                if (data.type == 'sub_community') {
                    let index = s_sub_communities.indexOf(parseInt(
                        data.id));
                    if (index > -
                        1) {
                        s_sub_communities.splice(index,
                            1);
                    }
                } else {
                    let index = s_properties.indexOf(parseInt(data
                        .id));
                    if (index > -
                        1) { // only splice array when item is found
                        s_properties.splice(index,
                            1); // 2nd parameter means remove one item only
                    }
                }
                //
                // $('#maplocations').val();

                locationMapping.push({
                    title: text,
                    community: null,
                    sub_community: s_sub_communities,
                    community_property: s_properties,
                })
                console.log(locationMapping)
                $('#locationMapping').val(JSON.stringify(locationMapping))



                // $("#search_sub_communities").val(s_sub_communities)
                // $("#search_properties").val(s_properties)

            });


            $(document).on('change', '#saveType', function(e) {
                let mapwith = $('#saveType option:selected').val();
                let mapWithText = $('#saveType option:selected').text();
                mapWithText = mapWithText.trim();

                let mapType = $('#mapType option:selected').val();
                if (mapwith && mapWithText && mapType) {
                    $('#mapType option[value="' + mapType + '"]').remove();
                    let text = '<div class="mt-2 rmDiv">Mapping: <b><span>' + mapType +
                        '</span></b> with <b><span>' + mapWithText +
                        '</span></b> <span class="ms-2"><i class="mdi-delete-circle mdi text-danger deSelectType" data-mapType="' +
                        mapType + '" data-mapWithText="' + mapWithText + '"></i></span></div>'

                    $('#appendType').append(text)
                    typeMapping.push({
                        [mapType]: mapWithText
                    })
                    $('#typeMapping').val(JSON.stringify(typeMapping))
                    console.log(bedMapping);
                    $(this).val(null).trigger('change');

                }
            });


            $(document).on('click', '.deSelectType', function(e) {
                var mapType = $(this).data("maptype");
                var mapWithText = $(this).data("mapwithtext");
                console.log(mapType);
                var newOption = $("<option></option>").attr("value", mapType).text(mapType);
                $("#mapType").append(newOption);
                $(this).closest('.rmDiv').remove();
                typeMapping = typeMapping.filter(function(item) {
                    return Object.keys(item)[0] != mapType;
                });
                $('#typeMapping').val(JSON.stringify(typeMapping))
            });



            $(document).on('change', '#saveBed', function(e) {
                let mapwith = $('#saveBed option:selected').val();
                let mapWithText = $('#saveBed option:selected').text();
                let mapBed = $('#mapBed option:selected').val();

                if (mapwith && mapWithText && mapBed) {

                    $('#mapBed option[value="' + mapBed + '"]').remove();
                    // let text = 'Mapping :' + mapBed + " with " + mapWithText;

                    let text = '<div class="mt-2 rmDiv">Mapping: <b><span>' + mapBed +
                        '</span></b> with <b><span>' + mapWithText +
                        '</span></b> <span class="ms-2"><i class="mdi-delete-circle mdi text-danger deSelectBed" data-mapType="' +
                        mapBed + '" data-mapWithText="' + mapWithText + '"></i></span></div>'

                    $('#appendBed').append(text)
                    bedMapping.push({
                        [mapBed]: mapwith
                    })
                    $('#bedMapping').val(JSON.stringify(bedMapping))
                    console.log(bedMapping);
                    $(this).val(null).trigger('change');
                }
            });



            $(document).on('click', '.deSelectBed', function(e) {
                var mapsBed = $(this).data("maptype");
                var mapWithText = $(this).data("mapWithText"); // Corrected variable name
                var newOption = $("<option></option>").attr("value", mapsBed).text(mapsBed);
                $("#mapBed").append(newOption);

                bedMapping = bedMapping.filter(function(item) {
                    return Object.keys(item)[0] !=
                        mapsBed;
                });
                $('#bedMapping').val(JSON.stringify(bedMapping));
                $(this).closest('.rmDiv').remove();
            });


            // alertMessage fade out after 5 seconds
            setTimeout(function() {
                $('#alertMessage').fadeOut('fast');
            }, 5000);


            $(document).on('click', '#submitBtn', function(e) {
                $(this).addClass('d-none');
                $('#submitBtnPlaceholder').removeClass('d-none');

            })


        })
    </script>
@endsection
