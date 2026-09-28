@extends('admin.layouts.master')
@section('title')
    Property Deals Status
@endsection
@section('style')
    <!--datatable css-->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css"/>
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css"/>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <!--datatable responsive css-->
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet"
          type="text/css"/>
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title mb-0 ">Deals Status List</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Deals</a></li>
                            <li class="breadcrumb-item active">Status</li>
                        </ol>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="form-check form-switch form-switch-right form-switch-md">
                            @can("Deal Statuses Create")
                                <button type="button" data-bs-toggle="modal" data-bs-target="#addModal"
                                        class="btn btn-success float-right action-add addModal" style="float-right">
                                    <i class="ri-add-line align-middle me-1"></i> Add new
                                </button>
                            @endcan
                            {{-- @can("Deal Statuses Delete")
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
                    <table id="statusDatatable" class="table align-middle" style="width:100%">
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
    <div class="modal zoomIn" id="addModal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="addModalLabel" aria-modal="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel"><span id="modal_title">Add </span> Deals Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="mainForm" class="needs-validation" novalidate>
                        <div class="row g-3">
                            <div class="col-4">

                                <label for="title" class="form-label">Name*</label>
                            </div>
                            <div class="col-8">
                                <input type="hidden" name="id" id="id" value="">
                                <input type="text" class="form-control" id="name" name="name"
                                       placeholder="Enter status name"
                                       required>
                                <div class="invalid-feedback">
                                    Please enter the status name.
                                </div>
                            </div>
                            <!--end col-->

                            <div class="col-4">

                                <label for="title" class="form-label">Icon</label>
                            </div>
                            <div class="col-8">
                                <input type="text" class="form-control" id="icon" name="icon"
                                    placeholder="ri line">
                                <div class="invalid-feedback">
                                    Please enter the icon class.
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-4">
                                <label for="badge" class="form-label"> Badge</label>
                            </div>
                            <div class="col-8">
                                <select id="statusBadge" class="form-select form-select-transparent" name="status_badge"
                                    data-placeholder="...">
                                    @include('admin.partials.badgeoptions')
                                </select>
                                <div class="invalid-feedback">
                                    Please enter the status Badge.
                                </div>
                            </div>

                            <div class="col-4">
                                <label for="order" class="form-label"> Order</label>
                            </div>
                            <div class="col-8">
                                <input type="number" class="form-control" id="order" name="order"
                                    placeholder="Enter order">
                                <div class="invalid-feedback">
                                    Please enter the source order.
                                </div>
                            </div>

                            <div class="col-4">
                                <label for="thumbnail" class="form-label">Thumbnail</label>
                            </div>
                            <div class="col-8">


                                <div class="mb-2  d-none" id="thumbnaildiv">
                                    <img src="" alt="Image" id="thumb-img"
                                        class="img-thumbnail img-responsive" style="max-width:50px">
                                    <i class="ri-delete-bin-5-fill text-danger ms-2 generalDelete" data-id=""
                                        data-model="App\Models\DealStatus" data-key="" id="thumbnail_id"></i>
                                </div>



                                <input type="file" class="form-control" id="thumbnail" name="thumbnail">
                                <div class="invalid-feedback">
                                    Please enter the Status Thumbnail.
                                </div>
                            </div>

                            <div class="col-4">
                                <label for="thumbnail" class="form-label">Image</label>
                            </div>
                            <div class="col-8">
                                <div class="mb-2  d-none" id="imagediv">
                                    <img src="" alt="Image" id="image-img"
                                        class="img-thumbnail img-responsive" style="max-width:50px">
                                    <i class="ri-delete-bin-5-fill text-danger ms-2 generalDelete" data-id=""
                                        data-model="App\Models\DealStatus" data-key="" id="image_id"></i>
                                </div>

                                <input type="file" class="form-control" id="image" name="image">
                                <div class="invalid-feedback">
                                    Please enter the Status image.
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-4">
                                <label class="form-check-label" for="statusCheckbox">Status</label>
                            </div>
                            <div class="col-8">
                                <div class="form-check form-switch form-switch-lg form-switch-success" dir="ltr">
                                    <input type="checkbox" name="status" class="form-check-input" id="statusCheckbox"
                                           checked>
                                </div>
                            </div>
                            <!--end col-->

                            <div class="col-lg-12">
                                <div class="hstack gap-2 justify-content-end">
                                    <button type="button" class="btn btn-light action-close" data-bs-dismiss="modal">Close</button>
                                    <button id="submitBtn" type="submit" class="btn btn-primary action-save">Submit</button>
                                </div>
                            </div>
                            <!--end col-->
                        </div>
                        <!--end row-->
                    </form>
                </div>
            </div>
        </div>
    </div>
    @can("Deal Statuses View")
        <div class="modal zoomIn" id="viewModal" tabindex="-1" aria-labelledby="addModalLabel" data-bs-backdrop="static"
             data-bs-keyboard="false" tabindex="-1" aria-modal="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header card-header pb-2">
                        <h5 class="modal-title" id="addModalLabel">View Deals Status</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-4">
                                <label for="view-name" class="form-label">Status Name:</label>
                            </div>
                            <div class="col-8" id="view-name"></div>
                            <div class="col-4">
                                <label class="form-check-label" for="statusCheckbox">Order:</label>
                            </div>
                            <div class="col-8" id="view-order">
                            </div>

                            <div class="col-4">
                                <label class="form-check-label" for="statusCheckbox">Icon class:</label>
                            </div>
                            <div class="col-8" id="view-icon">
                            </div>

                            <div class="col-4 view_thumbnail">
                                <label class="form-check-label" for="thumbnailCheckbox">Thumbnail:</label>
                            </div>
                            <div class="col-8 view_thumbnail" id="view-thumbnail">
                            </div>

                            <div class="col-4 view_image">
                                <label class="form-check-label" for="view_image">Image:</label>
                            </div>
                            <div class="col-8 view_image" id="view_image">
                            </div>

                            <div class="col-4">
                                <label class="form-check-label" for="badge">badge:</label>
                            </div>
                            <div class="col-8" id="view-badge">
                            </div>


                            <div class="col-4">
                                <label class="form-check-label" for="statusCheckbox">Status:</label>
                            </div>
                            <div class="col-8" id="view-status">
                            </div>
                            <div class="col-lg-12">
                                <div class="hstack gap-2 justify-content-end">
                                    <button id="deleteBtn" class="btn btn-danger action-delete">Delete</button>
                                    <button id="editBtn" class="btn btn-info action-edit">Edit</button>
                                    <button type="button" class="btn btn-light action-close" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
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

        let dataTable = $('#statusDatatable').DataTable({ //datatable initialization
            "order": [
                [0, "DESC"]
            ],
            language: {
                searchPlaceholder: "Search Deal Status"
            },
            createdRow: function(row, data, index) {
                $('#statusDatatable tbody').css('cursor', 'pointer');

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
                url: "{{route('admin.deals.statuses.dt')}}",
                type: "post",
                error: function () {
                    $(".users-error").html("");
                    $("#dataTable").append(
                        '<tbody class="users-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>'
                    );
                    $("#dataTable_processing").css("display", "none");
                }
            },

            "aoColumns": [{
                data: null,
                orderable: false,
            },
                {
                    data: 'id',
                    name: 'id',
                }, {
                    data: 'name',
                    name: 'name',
                }, {
                    data: null,
                    orderable: false,
                }, {
                    data: null,
                    orderable: false,

                }, {
                    data: null,
                    orderable: false,
                }, {
                    data: null,
                    orderable: false,
                }, {
                    data: null,
                    orderable: false,
                }, {
                    data: null,
                    orderable: false,
                }
            ],
            "columnDefs": [{ //manupulate a table column
                "targets": 0,
                orderable: true,
                "data": "id",
                render: function (type, row, data, meta) {
                    return `<div class="form-check">
                                                        <input class="form-check-input selectable-checkbox" type="checkbox" name="chk_child" value="` +
                        data.id + `">
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
                    "data": "name",
                    render: function (type, row, data, meta) {
                        return `<a href="javascript:void(0)" class="text-primary" onclick="viewStatus('` + data.id + `')">` + data.name + `</a>`;
                    }
                },
                {
                    "targets": 3,
                    orderable: true,
                    "data": "id",
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
                    "targets": 4,
                    orderable: true,
                    "data": "id",
                    render: function (type, row, data, meta) {
                        if (data.updated_at == null) {
                            return '-';
                        } else {
                            return moment(data.updated_at).format('MM/DD/YYYY hh:mm:ss');
                        }
                    }

                },
                {
                    "targets": 5,
                    orderable: true,
                    "data": "id",
                    render: function (type, row, data, meta) {
                        if (!data.created_by) return "-";
                        return `<div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-2">
                                        <img src="${data.created_by.thumb_url}" alt="" class="avatar-xs rounded-circle">
                                    </div>
                                    <div class="flex-grow-1">${data.created_by.name}</div>
                                </div>`;
                    }
                }, {
                    "targets": 6,
                    orderable: true,
                    "data": "id",
                    render: function (type, row, data, meta) {
                        if (data.created_at == null) {
                            return '-';
                        } else {
                            return moment(data.created_at).format('MM/DD/YYYY hh:mm:ss');
                        }
                    }

                },
                {
                    targets: 7,
                    orderable: false,
                    visible: true,
                    /*This will hide particular column*/
                    render: function (type, row, data, meta) {
                        let disabled = true;
                        @can("Deal Statuses Edit")
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
                    targets: 8,
                    orderable: false,
                    visible: true,
                    /*This will hide particular column*/
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


        $('#statusDatatable tbody').on('change', 'input.status-change', function () {
            // change Deals source  based on button class and taking id from button data attribute
            @can("Deal Statuses Edit")
            let id = $(this).data('id');
            let action = $(this).data('do');
            let formData = {
                id: id,
                action: action
            }
            $.ajax({
                type: "POST",
                url: "{{route('admin.deals.statuses.status')}}",
                data: formData,
                dataType: 'json',
                success: function (data) {
                    if (data.status == '1') {
                        showToastify("Deal status updated successfully", "success")
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
            @can("Deal Statuses Create")
                isUpdate = false;
            //  initColorPicker("#color-picker-parent", "#color_code")
            $('#modal_title').html('Add');
            $("#id").val("")
            $('#mainForm').trigger('reset');
            $('#mainForm').removeClass('was-validated');
            @endcan
        });


        $(document).on('click', '#deleteBtn', function (event) {
            @can("Deal Statuses Delete")
            deleteData($('#id').val());
            @endcan
        });


        $(document).on('click', '#editBtn', function (event) {
            @can("Deal Statuses Edit")
            $('#viewModal').modal('hide');
            editData($('#id').val());
            @endcan
        });


        $(document).on('click', '.edit-link', function (event) { //edit link click event it will fetch single cateogry details and put it on form
            @can("Deal Statuses Edit")
            let id = $(this).data('id');
            $('#id').val(id);
            editData(id);
            @endcan
        });


        $(document).on('click', '.delete-link', function (event) {
            @can("Deal Statuses Delete")
            let id = $(this).data('id');
            deleteData(id);
            @endcan
        });


        function editData(id) {
            @can("Deal Statuses Edit")
                isUpdate = true; // set isUpdate to true so that we can identify that this is update request
            $.ajax({
                url: "{{route('admin.deals.statuses.get')}}",
                type: 'GET',
                dataType: 'json',
                data: {
                    id
                },
                success: function (result) {
                    if (result.status == '1') {
                        $('#mainForm').trigger('reset');
                        $('#name').val(result.data.name)
                        $('#id').val(result.data.id)
                            $('#order').val(result.data.order)
                            $('#icon').val(result.data.icon)
                            $('#statusBadge').val(result.data.badge).trigger('change')
                            if (result.data.thumbnail) {
                                $('#thumbnaildiv').removeClass('d-none')
                                $('#thumb-img').attr('src', result.data.thumbnail)
                                $('#thumbnail_id').attr('data-id', result.data.id)
                                $('#thumbnail_id').attr('data-key', 'thumbnail')

                            } else {
                                $('#thumbnaildiv').addClass('d-none')
                            }

                            if (result.data.image) {
                                $('#imagediv').removeClass('d-none')
                                $('#image-img').attr('src', result.data.image)
                                $('#image_id').attr('data-id', result.data.id)
                                $('#image_id').attr('data-key', 'image')
                            } else {
                                $('#imagediv').addClass('d-none')
                            }


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


        // this Delete function
        function deleteData(id) {
            @can("Deal Statuses Delete")
            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to use this Status!",
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            })
                .then((willDelete) => {
                    if (willDelete) { // on confirm delete the category
                        $.ajax({
                            url: "{{route('admin.deals.statuses.delete')}}",
                            type: 'POST',
                            dataType: 'json',
                            data: {id},
                            success: function (result) {
                                if (result.status == '1') { //if successfully deleted
                                    showToastify("Status has been deleted successfully", "success")
                                    dataTable.row().draw(); // redraw the datatable show we don't need to reload the page to see the changes
                                    $('#viewModal').modal('hide');
                                } else { // something went wrong on backend
                                    showToastify(result.msg, "error")
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


        $(document).on('submit', '#mainForm', function (event) {
            $("#submitBtn").prop('disabled', true);
            event.preventDefault();
            var formData = new FormData($(this)[0]);
            $.ajax({
                url: isUpdate ? "{{route('admin.deals.statuses.update')}}" : "{{route('admin.deals.statuses.store')}}",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function (result) {
                    if (result.status == '1') {
                        showToastify(result.msg, "success")

                        $('#addModal').modal('hide'); //hide form modal
                        $("#submitBtn").prop('disabled', false); //enable submit button
                        $('#mainForm').trigger('reset'); //
                        dataTable.row().draw(); // redraw the datatable show we don't need to reload the page to see the changes
                        isUpdate = false; // set isUpdate to false so that when we try to add new source it will be create request
                    } else if (result.status == '2') {
                        $("#submitBtn").prop('disabled', false);
                    } else {
                        $("#submitBtn").prop('disabled', false);
                        showToastify(result.msg, "error")
                    }
                },
                error: function (jqXhr, json, errorThrown) {
                    let data = jqXhr.responseJSON;
                    $("#submitBtn").prop('disabled', false);

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

        $('#statusBadge').select2({
            placeholder: 'Select Badge',
            dropdownParent: $('#addModal')
        });

        function deleteMultiple() {
            @can("Deal Statuses Delete")
            if (ids_array = [], document.getElementsByName("chk_child").forEach((function (e) {
                if (1 == e.checked) {
                    var t = e.value;
                    ids_array.push(t)

                }
            })), "undefined" != typeof ids_array && ids_array.length > 0) {

                swal({
                    title: "Attention!!",
                    text: "You are deals status to delete multiple source. Once deleted, you will not be able to use these status!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                    .then((willDelete) => {
                        if (willDelete) {
                            $.ajax({
                                url: "{{route('admin.deals.statuses.multi.delete')}}",
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    ids: ids_array
                                },
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

            } else{
                showToastify("Please select at least one checkbox", "error")
            }
            @endcan
        }

        // checkbox select all functionality
        var checkAll = document.getElementById("checkAll");
        checkAll && (checkAll.onclick = function () {
            var e = document.querySelectorAll('.form-check-all input[name="chk_child"]');
            1 == checkAll.checked ? e.forEach((function (e) {
                e.checked = !0
                //e.closest("tr").classList.add("table-active")
            })) : e.forEach((function (e) {
                e.checked = !1
                //e.closest("tr").classList.remove("table-active")
            }))
        });

        function viewStatus(id) {
            @can("Deal Statuses View")
            $.ajax({
                url: "{{route('admin.deals.statuses.get')}}",
                type: 'GET',
                dataType: 'json',
                data: {id},
                success: function (result) {
                    if (result.status == '1') {
                        $('#id').val(result.data.id);

                        if (result.data) {
                            Object.keys(result.data).map((index) => {
                                $(`#view-${index}`).html(result.data[index])
                            })
                        }
                        if (result.data.order) {
                                $("#view-order").html(result.data.order);
                            }
                            if (result.data.icon) {
                                $("#view-icon").html('<i class="' + result.data.icon + '"></i>');
                            }

                            if (result.data.thumbnail) {
                                $('.view_thumbnail').removeClass('d-none')
                                $("#view-thumbnail").html('<img src="' + result.data.thumbnail +
                                    '" class="img-fluid" width="50" height="50">');
                            } else {
                                $('.view_thumbnail').addClass('d-none')
                            }
                            if (result.data.image) {
                                // alert(result.data.image)
                                $('.view_image').removeClass('d-none')
                                $("#view_image").html('<img src="' + result.data.image +
                                    '" class="img-fluid" width="50" height="50">');
                            } else {
                                $('.view_image').addClass('d-none')
                            }


                            if (result.data.badge) {
                                $("#view-badge").html(
                                    '<span class="' + result.data.badge + '">Badge</span>');
                            }

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
                if ($cell.index() === 7 || $cell.index() === 8) {
                    // console.log($cell);
                } else {
                    viewStatus(data.id);
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
                      
                    @can('Deal Statuses Delete')
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
        $("#dealParameterSidebar").addClass('collapse show');
        $("#sidebarDealStatus").addClass('active');
        $("#dealParameterSidebarLayout").addClass('active');
        const optionFormat = (item) => {
            if (!item.id) {
                return item.text;
            }

            var span = document.createElement('span');
            var template = '';

            template += '<span value="1" class="badge fw-bolder ';
            template += '' + item.element.getAttribute('data-kt-rich-content-class') + '">';
            template += '' + item.text + '</span>';


            span.innerHTML = template;

            return $(span);
        }

        $('.generalDelete').click(function() {
            let source_id = $(this).data('id');
            let key = $(this).data('key');
            let model = $(this).data('model');

            $.ajax({
                url: "{{ route('admin.general.paramerter.delete.general') }}",
                type: 'POST',
                dataType: 'json',
                data: {
                    id: source_id,
                    key: key,
                    model: model
                },
                success: function(result) {

                    editData(source_id)
                    dataTable.row()
                        .draw(); // redraw the datatable show we don't need to reload the page to see the changes                        
                },
                error: function(result) {
                    console.log(result);
                }
            })
        });

        $('#statusBadge').select2({
            placeholder: "Select",
            minimumResultsForSearch: Infinity,
            templateSelection: optionFormat,
            templateResult: optionFormat
        });
        $('select[name="status_badge"]').val($('<div>').html(data.color_code).text()).prop('selected', true).trigger(
            'change');
    </script>

@endsection
