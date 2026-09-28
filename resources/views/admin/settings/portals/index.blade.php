@extends('admin.layouts.master')
@section('title')
    Portals
@endsection
@section('style')
    <!--datatable css-->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css"/>
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css"/>
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
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title mb-0 ">Portal List</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Settings</a></li>
                            <li class="breadcrumb-item active">Portal</li>
                        </ol>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="form-check form-switch form-switch-right form-switch-md">
                            @can("Portals Create")
                                <button type="button" data-bs-toggle="modal"
                                        data-bs-target="#addModal" class="btn btn-success float-right addModal action-add"
                                        style="float-right">
                                    <i class="ri-add-line align-middle me-1"></i> Add new
                                </button>
                            @endcan
                            {{-- @can("Portals Delete")
                                <button class="btn btn-soft-danger action-delete" onclick="deleteMultiple()"><i
                                        class="ri-delete-bin-2-line"></i></button>
                            @endcan --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <table id="categoryDatatable" class="table align-middle" style="width-100%">
                        <thead class="">
                            <tr class="table-light2 text-uppercase">
                            <th scope="col" style="width: 50px;">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="checkAll" value="option"
                                           data-com.bitwarden.browser.user-edited="yes">
                                </div>
                            </th>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Short Name</th>
                            <th>XML URL</th>
                            <th>Updated By</th>
                            <th>Updated At</th>
                            <th>Created By</th>
                            <th>Created At</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody class="form-check-all">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Grids in modals -->
    <div class="modal zoomIn" id="addModal" aria-labelledby="addModalLabel" data-bs-backdrop="static"
         data-bs-keyboard="false" tabindex="-1" aria-modal="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header card-header pb-2">
                    <h5 class="modal-title" id="addModalLabel"><span id="modal_title">Add </span> Portal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="mainForm" class="needs-validation" novalidate>
                        <div class="row g-3">
                            <div class="col-4">
                                <label for="name" class="form-label">Name*</label>
                            </div>
                            <div class="col-8">
                                <input type="hidden" name="id" id="id" value="">
                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter name"
                                       required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-4">
                                <label for="short_name" class="form-label">Short Name*</label>
                            </div>
                            <div class="col-8">
                                <input type="text" class="form-control" id="short_name" name="short_name"
                                       placeholder="Enter short name" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-4">
                                <label for="quota" class="form-label">Quota</label>
                            </div>
                            <div class="col-8">
                                <input type="number" class="form-control" id="quota" name="quota"
                                       placeholder="Enter quota">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-4">
                                <label for="quota" class="form-label">Portal token</label>
                            </div>
                            <div class="col-8">
                                <input type="text" class="form-control" id="token" name="token"
                                       placeholder="Enter token">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-4">
                                <label for="quota" class="form-label">Portal token AD</label>
                            </div>
                            <div class="col-8">
                                <input type="text" class="form-control" id="token_abu_dhabi" name="token_abu_dhabi"
                                       placeholder="Enter token for Abu Dhabi">
                                <div class="invalid-feedback"></div>
                            </div>


                            <div class="col-4">
                                <label for="quota" class="form-label">Portal token Commercial</label>
                            </div>
                            <div class="col-8">
                                <input type="text" class="form-control" id="token_commercial" name="token_commercial"
                                       placeholder="Enter token for Commercial">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-4">
                                <label for="thumbnail" class="form-label">Thumbnail</label>
                            </div>
                            <div class="col-8">
                                <input type="file" class="form-control" id="thumbnail" name="thumbnail">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-4">
                                <label for="image" class="form-label">Image</label>
                            </div>
                            <div class="col-8">
                                <input type="file" class="form-control" id="image" name="image">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-4">
                                <label class="form-check-label" for="statusCheckbox">Status</label>
                            </div>
                            <div class="col-8">
                                <div class="form-check form-switch form-switch-lg form-switch-success" dir="ltr">
                                    <input type="checkbox" name="status" class="form-check-input" id="statusCheckbox"
                                           checked>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="hstack gap-2 justify-content-end">
                                    <button type="button" class="btn btn-light action-close" data-bs-dismiss="modal">Close</button>
                                    <button id="submitBtn" type="submit" class="btn btn-primary action-save">Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @can("Portals View")
    <div class="modal zoomIn" id="viewModal" tabindex="-1" aria-labelledby="addModalLabel" data-bs-backdrop="static"
         data-bs-keyboard="false" tabindex="-1" aria-modal="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header card-header pb-2">
                    <h5 class="modal-title" id="addModalLabel">View Portal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-4">
                            <label for="view-name" class="form-label">Name:</label>
                        </div>
                        <div class="col-8" id="view-name"></div>
                        <div class="col-4">
                            <label for="view-short_name" class="form-label">Short Name:</label>
                        </div>
                        <div class="col-8" id="view-short_name"></div>
                        <div class="col-4">
                            <label for="view-quota" class="form-label">Quota:</label>
                        </div>
                        <div class="col-8" id="view-quota"></div>
                        <div class="col-4">
                            <label for="view-thumbnail" class="form-label">Thumbnail:</label>
                        </div>
                        <div class="col-8" id="view-thumbnail"></div>
                        <div class="col-4">
                            <label for="view-image" class="form-label">Image:</label>
                        </div>
                        <div class="col-8" id="view-image"></div>
                        <div class="col-4">
                            <label for="view-xml_url" class="form-label">XML URL:</label>
                        </div>
                        <div class="col-8" id="view-xml_url"></div>
                        <div class="col-4">
                            <label class="form-check-label" for="view-status">Status:</label>
                        </div>
                        <div class="col-8" id="view-status"></div>
                        <!--end col-->
                        <div class="col-lg-12">
                            <div class="hstack gap-2 justify-content-end">
                                <button id="deleteBtn" class="btn btn-danger action-delete">Delete</button>
                                <button id="editBtn" class="btn btn-info action-edit">Edit</button>
                                <button type="button" class="btn btn-light action-close" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                        <!--end col-->
                    </div>
                    <!--end row-->
                </div>
            </div>
        </div>
    </div>
    @endcan
@endsection
@section('script')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.colVis.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>
        {{-- <script src=" "></script> --}}

    <script>
        let isUpdate = false;

        let propertyFinderApiRoute = "{{ route("api.xml.pf") }}";
        let dubizzleApiRoute = "{{ route("api.xml.bayut_dubizzle") }}";
        let bayutApiRoute = "{{ route("api.xml.bayut_dubizzle") }}";
        let privateSiteApiRoute = "{{ route("api.xml.privatesite") }}";
        let genericPortalApiRoute = "{{ route("api.xml.genericportal") }}";
        let rightMovePortalApiRoute = "{{ route("api.xml.rightmove") }}";
        let houzaPortalApiRoute = "{{ route("api.xml.houza") }}";
        let jamesEditionPortalApiRoute = "{{ route("api.xml.jamesedition") }}";
        let yandexPortalApiRoute = "{{ route("api.xml.yandex") }}";
        let emiratesestatePortalApiRoute = "{{ route("api.xml.emiratesestate") }}";

        !function () { // form validation used from theme
            "use strict";
            window.addEventListener("load", (function () {
                var t = document.getElementsByClassName("needs-validation");
                t && Array.prototype.filter.call(t, (function (t) {
                    t.addEventListener("submit", (function (e) {
                        !1 === t.checkValidity() && (e.preventDefault(), e.stopPropagation()), t.classList.add("was-validated")
                    }), !1)
                }))
            }), !1)
        }();

        let dataTable = $('#categoryDatatable').DataTable({ //datatable initialization
            "order": [[0, "DESC"]],
            language: {
                searchPlaceholder: "Search portals"
            },
            createdRow: function(row, data, index) {
                $('#categoryDatatable tbody').css('cursor', 'pointer');

            },
            dom: 'Bfrtip',
            buttons: [{
                extend: 'colvis',
                titleAttr: 'Show/Hide',
                text: '<i class="colvis" style="font-size:09px">Show / Hide</i>',

            }],
            responsive: true,
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "{{route('admin.settings.portals.dt')}}",
                type: "post",
                error: function () {
                    $(".users-error").html("");
                    $("#dataTable").append('<tbody class="users-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                    $("#dataTable_processing").css("display", "none");
                }
            },

            "aoColumns": [
                {
                    data: null,
                    orderable: false,
                },
                {
                    data: 'id',
                    name: 'id',
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'short_name',
                    name: 'short_name',
                },
                {
                    data: 'xml_url',
                    name: 'xml_url',
                },
                {
                    data: null,
                    name: 'updated_by_id',
                    searchable: false
                },
                {
                    data: null,
                    name: 'updated_at',
                },
                {
                    data: null,
                    name: 'created_by_id',
                    searchable: false
                },
                {
                    data: null,
                    name: 'created_at',
                },
                {
                    data: null,
                    orderable: false,
                }
                , {
                    data: null,
                    orderable: false,
                }
            ],
            "columnDefs": [
                { //manupulate a table column
                    "targets": 0,
                    orderable: true,
                    "data": "id",
                    render: function (type, row, data, meta) {
                        return `<div class="form-check">
                                               <input class="form-check-input selectable-checkbox" type="checkbox" name="chk_child" value="` + data.id + `">
                                           </div>`;
                    }
                },
                {
                    "targets": 1,
                    orderable: true,
                    "data": "id",
                    render: function (type, row, data, meta) {
                        return data.id;
                    }
                },

                {
                    "targets": 2,
                    orderable: true,
                    render: function (type, row, data, meta) {
                        return `<a href="javascript:void(0)" class="text-primary" onclick="viewCategory('` + data.id + `')">` + data.name + `</a>`;
                    }
                },
                {
                    "targets": 4,
                    orderable: true,
                    render: function (type, row, data, meta) {
                        if (!data.xml_url) return "-";
                        if (data.name == "Property Finder") return `<a href="${propertyFinderApiRoute}?key=${data.xml_url}" target="_blank">${propertyFinderApiRoute}?key=${data.xml_url}</a>`;
                        if (data.name == "Dubizzle") return `<a href="${dubizzleApiRoute}?key=${data.xml_url}" target="_blank">${dubizzleApiRoute}?key=${data.xml_url}</a>`;
                        if (data.name == "Bayut") return `<a href="${bayutApiRoute}?key=${data.xml_url}" target="_blank">${bayutApiRoute}?key=${data.xml_url}</a>`;
                        if (data.name == "Private Site") return `<a href="${privateSiteApiRoute}?key=${data.xml_url}" target="_blank">${privateSiteApiRoute}?key=${data.xml_url}</a>`;
                        // if (data.name == "Private Site") return `<a href="${privateSiteApiRoute}?key=${data.xml_url}" target="_blank">${privateSiteApiRoute}?key=${data.xml_url}</a>`;
                        if (data.name == "Generic Portal") return `<a href="${genericPortalApiRoute}?key=${data.xml_url}" target="_blank">${genericPortalApiRoute}?key=${data.xml_url}</a>`;
                        if (data.name == "Right Move") return `<a href="${rightMovePortalApiRoute}?key=${data.xml_url}" target="_blank">${rightMovePortalApiRoute}?key=${data.xml_url}</a>`;
                        if (data.name == "Houza") return `<a href="${houzaPortalApiRoute}?key=${data.xml_url}" target="_blank">${houzaPortalApiRoute}?key=${data.xml_url}</a>`;
                        if (data.name == "James Edition") return `<a href="${jamesEditionPortalApiRoute}?key=${data.xml_url}" target="_blank">${jamesEditionPortalApiRoute}?key=${data.xml_url}</a>`;
                        if (data.name == "Emirates.Estate") return `<a href="${emiratesestatePortalApiRoute}?key=${data.xml_url}" target="_blank">${emiratesestatePortalApiRoute}?key=${data.xml_url}</a>`;
                        if (data.name == "Yandex") return `<a href="${yandexPortalApiRoute}?key=${data.xml_url}" target="_blank">${yandexPortalApiRoute}?key=${data.xml_url}</a>`;
                        return `${data.xml_url}`;
                    }
                },
                {
                    "targets": 5,
                    orderable: true,
                    render: function (type, row, data, meta) {
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
                    render: function (type, row, data, meta) {
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
                    render: function (type, row, data, meta) {
                        if (!data.created_by) return "-";
                        return `<div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-2">
                                        <img src="${data.created_by.thumb_url}" alt="" class="avatar-xs rounded-circle">
                                    </div>
                                    <div class="flex-grow-1">${data.created_by.name}</div>
                                </div>`;
                    }
                },
                {
                    "targets": 8,
                    orderable: true,
                    render: function (type, row, data, meta) {
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
                    render: function (type, row, data, meta) {
                        let disabled = true;
                        @can("Portals Edit")
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
                    targets: -1,
                    orderable: false,
                    visible: true,
                    render: function (type, row, data, meta) {
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


        $('#categoryDatatable tbody').on('change', 'input.status-change', function () {
            @can("Portals Edit")
            let id = $(this).data('id');
            let action = $(this).data('do');
            let formData = {
                id: id,
                action: action
            }
            $.ajax({
                type: "POST",
                url: "{{route('admin.settings.portals.status')}}",
                data: formData,
                dataType: 'json',
                success: function (data) {
                    if (data.status == '1') {
                        showToastify(data.msg, "success")
                        dataTable.row().draw();
                    }
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });
            @endcan
        });


        $(document).on('click', '.addModal', function (event) {
            @can("Portals Create")
            $('#modal_title').html('Add');
            $('#mainForm').trigger('reset');
            $('#mainForm').removeClass('was-validated');
            @endcan
        });


        $(document).on('click', '#deleteBtn', function (event) {
            @can("Portals Delete")
            deleteData($('#id').val());
            @endcan
        });

        $(document).on('click', '#editBtn', function (event) {
            @can("Portals Edit")
            $('#viewModal').modal('hide');
            editData($('#id').val());
            @endcan
        });


        $(document).on('click', '.edit-link', function (event) { //edit link click event it will fetch single cateogry details and put it on form
            @can("Portals Edit")
            let id = $(this).data('id');
            $('#id').val(id);
            editData(id);
            @endcan
        });
        $(document).on('click', '.delete-link', function (event) {
            @can("Portals Delete")
            let id = $(this).data('id');
            deleteData(id);
            @endcan
        });

        $(document).on('submit', '#mainForm', function (event) {
            $("#submitBtn").prop('disabled', true);
            event.preventDefault();
            var formData = new FormData($(this)[0]);
            $.ajax({
                url: isUpdate ? "{{route('admin.settings.portals.update')}}" : "{{route('admin.settings.portals.store')}}",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function (result) {
                    if (result.status == '1') {
                        showToastify(result.msg, "success")

                        $('#addModal').modal('hide');  //hide form modal
                        $("#submitBtn").prop('disabled', false); //enable submit button
                        $('#mainForm').trigger('reset'); //
                        dataTable.row().draw(); // redraw the datatable show we don't need to reload the page to see the changes
                        isUpdate = false; // set isUpdate to false so that when we try to add new category it will be create request

                    } else if (result.status == '2') {
                        $("#submitBtn").prop('disabled', false);
                        let error_message = '';

                        $.each(result.errors, function (key, value) {
                            error_message += '' + key + ': ' + value + ' | ';
                        });
                        showToastify(error_message, "error")
                    } else {
                        $("#submitBtn").prop('disabled', false);
                        showToastify(result.msg, "error")
                    }
                },
                error: function (jqXhr, json, errorThrown) {
                    $("#submitBtn").prop('disabled', false);
                    let data = jqXhr.responseJSON;
                    if (data.errors) {
                        $.each(data.errors, function (index, item) {
                            showToastify(item[0], "error")

                        })
                    }
                    if (jqXhr.status == 500 || jqXhr.status == 400) {
                        showToastify(data.message, "error")
                    }
                },
            });
        });

        function deleteMultiple() {
            @can("Portals Delete")
            let ids = [];
            $(".selectable-checkbox").map((index, item) => {
                item.checked ? ids.push(item.value) : []
            })
            if (ids.length > 0) {
                swal({
                    title: "Attention!!",
                    text: "You are trying to delete multiple portals. Once deleted, you will not be able to use these portals!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                    .then((willDelete) => {
                        if (willDelete) {
                            $.ajax({
                                url: "{{route('admin.settings.portals.multi.delete')}}",
                                type: 'POST',
                                dataType: 'json',
                                data: {ids},
                                success: function (result) {
                                    if (result.status == '1') {
                                        showToastify(result.msg, "success")
                                        $('.multi_options').remove();
                                        dataTable.row().draw();
                                    } else {
                                        showToastify(result.msg, "error")
                                    }
                                },
                                error: function (result) {
                                    console.log(result);
                                }
                            });
                            document.getElementById("checkAll").checked = !1
                        }
                    });
            } else {
                showToastify("Please select at least one checkbox", "error")
            }
            @endcan
        }

        function viewCategory(id) {
            @can("Portals View")
            $("#view-image, #view-thumbnail").html(``)
            $.ajax({
                url: "{{route('admin.settings.portals.get')}}",
                type: 'GET',
                dataType: 'json',
                data: {id},
                success: function (result) {
                    if (result.status == '1') {
                        if (result.data) {
                            Object.keys(result.data).map((index) => {
                                $(`#view-${index}`).html(result.data[index])
                            })
                            if (result.data.thumbnail) {
                                $("#view-thumbnail").html(`<img src="${result.data.thumbnail}" height="20px" width="20px" />`)
                            }
                            if (result.data.image) {
                                $("#view-image").html(`<img src="${result.data.image}" height="100px" width="100px" />`)
                            }
                            let data = result.data;
                            if (data.name == "Property Finder"){
                                $("#view-xml_url").html(`<span>${propertyFinderApiRoute}?key=${data.xml_url}</span>`)
                            }
                            if (data.name == "Dubizzle"){
                                $("#view-xml_url").html(`<span>${dubizzleApiRoute}?key=${data.xml_url}</span><br><span>${dubizzleHourlyApiRoute}?key=${data.xml_url}</span>`)
                            }
                            if (data.name == "Bayut"){
                                $("#view-xml_url").html(`<span>${bayutApiRoute}?key=${data.xml_url}</span>`)
                            }
                        }
                        $('#id').val(result.data.id);
                        if (result.data.status) {
                            $("#view-status").html('<span class="badge rounded-pill bg-success">Active</span>');
                        } else {
                            $("#view-status").html('<span class="badge rounded-pill bg-danger">Inactive</span>');
                        }
                        $('#viewModal').modal('show');
                    } else {
                        showToastify(result.msg, "error")
                    }
                },
                error: function (result) {
                    console.log(result);
                }
            });
            @endcan
        }

        $("#checkAll").change(function () {
            $(".selectable-checkbox").prop("checked", this.checked)
        })

        function deleteData(id) {
            @can("Portals Delete")
            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to use this portal!",
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            })
                .then((willDelete) => {
                    if (willDelete) { // on confirm delete the category
                        $.ajax({
                            url: "{{route('admin.settings.portals.delete')}}",
                            type: 'POST',
                            dataType: 'json',
                            data: {id},
                            success: function (result) {
                                if (result.status == '1') { //if successfully deleted
                                    showToastify(result.msg, "success")
                                    dataTable.row().draw(); // redraw the datatable show we don't need to reload the page to see the changes
                                    $('#viewModal').modal('hide');
                                } else { // something went wrong on backend
                                    swal("Whoops", result.msg, "error");
                                }
                            },
                            error: function (result) {
                                console.log(result);
                            }
                        });
                    }
                });
            @endcan
        }


        function editData(id) {
            @can("Portals Edit")
            isUpdate = true; // set isUpdate to true so that we can identify that this is update request
            $.ajax({
                url: "{{route('admin.settings.portals.get')}}",
                type: 'GET',
                dataType: 'json',
                data: {id},
                success: function (result) {
                    if (result.status == '1') {
                        $('#mainForm').trigger('reset');
                        $('#name').val(result.data.name)
                        $('#short_name').val(result.data.short_name)
                        $('#quota').val(result.data.quota)
                        $('#xml_url').val(result.data.xml_url)
                        $('#token').val(result.data.token)
                        $('#token_abu_dhabi').val(result.data.token_abu_dhabi)
                        $('#token_commercial').val(result.data.token_commercial)
                        $('#id').val(result.data.id)
                        if (result.data.status) {
                            document.getElementById("statusCheckbox").checked = true;
                        } else {
                            document.getElementById("statusCheckbox").checked = false;
                        }
                        $('#addModal').modal('show');
                        $('#mainForm').removeClass('was-validated');
                        $('#modal_title').html('Edit');
                    } else {
                        showToastify(result.msg, "error")
                    }
                },
                error: function (result) {
                    console.log(result);
                }
            });
            @endcan
        }

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

                    @can('Portals Delete')
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
        // viewCategory

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
                if ($cell.index() === 9 || $cell.index() === 10 || $cell.index() === 4) {
                    // console.log($cell);
                } else {
                    viewCategory(data.id);
                    highlightRow($(this));

                }
            } // end if
        }); // table row click

        dataTable.on('draw.dt', function() {
            $(".chk_child").prop("checked", false);
            $('.multi_options').remove();
            $('#checkAll').prop("checked", false);
        });
         $("#sidebarSettings").addClass('collapse show');
        $("#sidebarPortals").addClass('active');
        $("#portalSettingsActive").addClass('active');

    </script>
@endsection
