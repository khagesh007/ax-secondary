@extends('admin.layouts.master')
@section('title')
    Lead Sub Status
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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"
        type="text/css" />
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title mb-0 ">Lead Sub Status List</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Property</a></li>
                            <li class="breadcrumb-item active">Lead Sub Status</li>
                        </ol>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="form-check form-switch form-switch-right form-switch-md">
                            @can('Lead Sub Statuses Create')
                                <button type="button" data-bs-toggle="modal" data-bs-target="#addModal"
                                    class="btn btn-success float-right addModal action-add" style="float-right">
                                    <i class="ri-add-line align-middle me-1"></i> Add new
                                </button>
                            @endcan
                            @can('Lead Sub Statuses Delete')
                                <button class="btn btn-soft-danger action-delete d-none" onclick="deleteMultiple()"><i
                                        class="ri-delete-bin-2-line"></i></button>
                            @endcan
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
                        <thead class="table-light">
                            <tr>
                                <th scope="col" style="width: 50px;">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="checkAll" value="option"
                                            data-com.bitwarden.browser.user-edited="yes">
                                    </div>
                                </th>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Status Name</th>
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
                    <h5 class="modal-title" id="addModalLabel"><span id="modal_title">Add </span> Lead Sub Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="mainForm" class="needs-validation" novalidate>
                        <div class="row g-3">
                            <div class="col-4">
                                <label for="name" class="form-label">Sub Status Name*</label>
                            </div>
                            <div class="col-8">
                                <input type="hidden" name="id" id="id" value="">
                                <input type="text" class="form-control" id="name" name="name"
                                    placeholder="Enter sub status name" required>
                                <div class="invalid-feedback">
                                    Please enter the lead sub status name.
                                </div>
                            </div>
                            <div class="col-4">
                                <label for="lead_status_id" class="form-label">Lead Status*</label>
                            </div>
                            <div class="col-8">
                                <select name="lead_status_id" id="lead_status_id" class="form-control" required>
                                    <option value=""></option>
                                    @foreach ($statuses ?? [] as $status)
                                        <option value="{{ $status->id }}">{{ $status->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">
                                    Please choose status.
                                </div>
                            </div>
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
                                <select name="status_badge" id="statusBadge" class="form-control">
                                    {{-- <option value="badge rounded-pill bg-primary" selected><span
                                            class="badge rounded-pill bg-primary">Primary</span></option>
                                    <option value="badge rounded-pill bg-secondary"><span
                                            class="badge rounded-pill bg-secondary">Secondary</span></option>
                                    <option value="badge rounded-pill bg-success"><span
                                            class="badge rounded-pill bg-success">Success</span></option>
                                    <option value="badge rounded-pill bg-info"><span
                                            class="badge rounded-pill bg-info">Info</span></option>
                                    <option value="badge rounded-pill bg-warning"><span
                                            class="badge rounded-pill bg-warning">Warning</span></option> --}}
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
                                    placeholder="Enter  order" >
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
                                        data-model="App\Models\LeadSubStatus" data-key="" id="thumbnail_id"></i>
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
                                        data-model="App\Models\LeadSubStatus" data-key="" id="image_id"></i>
                                </div>

                                <input type="file" class="form-control" id="image" name="image">
                                <div class="invalid-feedback">
                                    Please enter the Status image.
                                </div>
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
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                    <button id="submitBtn" type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @can('Lead Sub Statuses View')
        <div class="modal zoomIn" id="viewModal" tabindex="-1" aria-labelledby="addModalLabel" data-bs-backdrop="static"
            data-bs-keyboard="false" tabindex="-1" aria-modal="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header card-header pb-2">
                        <h5 class="modal-title" id="addModalLabel">Lead Sub Status</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-4">
                                <label for="view-name" class="form-label">Name:</label>
                            </div>
                            <div class="col-8" id="view-name"></div>
                            <div class="col-4">
                                <label for="view-lead-status" class="form-label">Lead Status:</label>
                            </div>
                            <div class="col-8" id="view-lead-status"></div>

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
                            <div class="col-8" id="view-status"></div>
                            <div class="col-lg-12">
                                <div class="hstack gap-2 justify-content-end">
                                    <button id="deleteBtn" class="btn btn-danger action-delete">Delete</button>
                                    <button id="editBtn" class="btn btn-info action-edit">Edit</button>
                                    <button type="button" class="btn btn-light action-close"
                                        data-bs-dismiss="modal">Close</button>
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

        $('#lead_status_id').select2({
            placeholder: 'Select status',
            dropdownParent: $('#addModal')
        });

        let dataTable = $('#categoryDatatable').DataTable({ //datatable initialization
            "order": [
                [0, "DESC"]
            ],
            language: {
                searchPlaceholder: "Search sub statuses"
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
                url: "{{ route('admin.lead.sub.statuses.dt') }}",
                type: "post",
                error: function() {
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
                    data: 'name'
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
                }, {
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
                    "data": "name",
                    render: function(type, row, data, meta) {
                        return `<a href="javascript:void(0)" class="text-primary" onclick="viewCategory('` +
                            data.id + `')">` + data.name + `</a>`;
                    }
                },
                {
                    "targets": 3,
                    orderable: true,
                    "data": "name",
                    render: function(type, row, data, meta) {
                        if (data.lead_status != null) {
                            return data.lead_status.name;
                        } else {
                            return '-';
                        }
                    }
                },
                {
                    "targets": 4,
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
                    "targets": 5,
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
                    "targets": 6,
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
                    "targets": 7,
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
                    targets: 8,
                    orderable: false,
                    visible: true,
                    /*This will hide particular column*/
                    render: function(type, row, data, meta) {
                        let disabled = true;
                        @can('Lead Sub Statuses Edit')
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
                    targets: 9,
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

        $('#statusBadge').select2({
            placeholder: 'Select Badge',
            dropdownParent: $('#addModal')
        });

        $('#categoryDatatable tbody').on('change', 'input.status-change', function() {
            @can('Lead Sub Statuses Edit')
                // change property category status based on button class and taking id from button data attribute
                let id = $(this).data('id');
                let action = $(this).data('do');
                console.log(id);
                let formData = {
                    id: id,
                    action: action
                }
                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.lead.sub.statuses.status') }}",
                    data: formData,
                    dataType: 'json',
                    success: function(data) {
                        if (data.status == '1') {
                            showToastify("Sub status updated successfully", "success")
                            dataTable.row().draw();
                        }
                    },
                    error: function(data) {
                        console.log('Error:', data);
                    }
                });
            @endcan
        });

        $(document).on('click', '.addModal', function(event) {
            @can('Lead Sub Statuses Create')
                isUpdate = false;
                //  initColorPicker("#color-picker-parent", "#color_code")
                $('#modal_title').html('Add');
                $("#id").val("")
                $('#mainForm').trigger('reset');
                $('#mainForm').removeClass('was-validated');
                $("#lead_status_id").val("").trigger("change")
            @endcan
        });


        $(document).on('click', '#deleteBtn', function(event) {
            @can('Lead Sub Statuses Delete')
                deleteData($('#id').val());
            @endcan
        });

        $(document).on('click', '#editBtn', function(event) {
            @can('Lead Sub Statuses Edit')
                $('#viewModal').modal('hide');
                editData($('#id').val());
            @endcan
        });


        $(document).on('click', '.edit-link', function(
        event) { //edit link click event it will fetch single cateogry details and put it on form
            @can('Lead Sub Statuses Edit')
                let id = $(this).data('id');
                $('#id').val(id);
                editData(id);
            @endcan
        });

        $(document).on('click', '.delete-link', function(event) {
            @can('Lead Sub Statuses Delete')
                let id = $(this).data('id');
                deleteData(id);
            @endcan
        });

        $(document).on('submit', '#mainForm', function(event) {
            $("#submitBtn").prop('disabled', true);
            event.preventDefault();
            var formData = new FormData($(this)[0]);
            $.ajax({
                url: isUpdate ? "{{ route('admin.lead.sub.statuses.update') }}" :
                    "{{ route('admin.lead.sub.statuses.store') }}",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function(result) {
                    if (result.status == '1') {
                        showToastify(result.msg, "success")
                        $('#addModal').modal('hide'); //hide form modal
                        $("#submitBtn").prop('disabled', false); //enable submit button
                        $('#mainForm').trigger('reset'); //
                        dataTable.row().draw(); // redraw the datatable show we don't need to reload the page to see the changes
                        isUpdate =false; // set isUpdate to false so that when we try to add new category it will be create request
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

        function deleteMultiple() {
            @can('Lead Sub Statuses Delete')
                let ids = [];
                $(".selectable-checkbox").map((index, item) => {
                    item.checked ? ids.push(item.value) : []
                })
                if (ids.length > 0) {
                    swal({
                            title: "Attention!!",
                            text: "You are trying to delete multiple sub Statuses. Once deleted, you will not be able to use these lead sub status!",
                            icon: "warning",
                            buttons: true,
                            dangerMode: true,
                        })
                        .then((willDelete) => {
                            if (willDelete) {
                                $.ajax({
                                    url: "{{ route('admin.lead.sub.statuses.multi.delete') }}",
                                    type: 'POST',
                                    dataType: 'json',
                                    data: {
                                        ids
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
                                document.getElementById("checkAll").checked = !1
                            }
                        });
                } else {
                    showToastify("Please select at least one checkbox", "error")
                }
            @endcan
        }

        function viewCategory(id) {
            @can('Lead Sub Statuses View')
                $.ajax({
                    url: "{{ route('admin.lead.sub.statuses.get') }}",
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        id
                    },
                    success: function(result) {
                        if (result.status == '1') {
                            let data = result.data;
                            $('#id').val(result.data.id);
                            if (result.data) {
                                Object.keys(result.data).map((index) => {
                                    $(`#view-${index}`).html(result.data[index])
                                })
                                $("#view-lead-status").html(result.data.lead_status ? result.data.lead_status
                                    .name : "")
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
                                $("#view-status").html(
                                    '<span class="badge rounded-pill bg-success">Active</span>');
                            } else {
                                $("#view-status").html(
                                    '<span class="badge rounded-pill bg-danger">Inactive</span>');
                            }
                            $('#viewModal').modal('show');
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

        $("#checkAll").change(function() {
            $(".selectable-checkbox").prop("checked", this.checked)
        })


        function deleteData(id) {
            @can('Lead Sub Statuses Delete')
                swal({
                        title: "Are you sure?",
                        text: "Once deleted, you will not be able to use this lead sub status!",
                        icon: 'warning',
                        buttons: true,
                        dangerMode: true,
                    })
                    .then((willDelete) => {
                        if (willDelete) { // on confirm delete the category
                            $.ajax({
                                url: "{{ route('admin.lead.sub.statuses.delete') }}",
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    id
                                },
                                success: function(result) {
                                    if (result.status == '1') { //if successfully deleted
                                        showToastify("Sub status has been deleted successfully", "success")
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
                    });
            @endcan
        }


        function editData(id) {
            @can('Lead Sub Statuses Edit')
                isUpdate = true; // set isUpdate to true so that we can identify that this is update request
                $.ajax({
                    url: "{{ route('admin.lead.sub.statuses.get') }}",
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        id
                    },
                    success: function(result) {
                        if (result.status == '1') {
                            let data = result.data;
                            $('#mainForm').trigger('reset');
                            $('#name').val(result.data.name)
                            $('#id').val(result.data.id)
                            $('#statusBadge').val(result.data.badge).trigger('change')
                            $('#order').val(result.data.order)
                            $('#icon').val(result.data.icon)
                            if (result.data.status) {
                                document.getElementById("statusCheckbox").checked = true;
                            } else {
                                document.getElementById("statusCheckbox").checked = false;
                            }


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


                            $("#lead_status_id").val(result.data.lead_status_id).trigger("change")
                            $('#addModal').modal('show');
                            $('#mainForm').removeClass('was-validated');
                            $('#modal_title').html('Edit');
                           // redraw the datatable show we don't need to reload the page to see the changes                        
                        } else {
                            showToastify(result.msg, "error")
                        }
                        dataTable.row().draw(); 
                    },
                    error: function(result) {
                        console.log(result);
                    }
                });
            @endcan
        }

        dataTable.on('click', 'tbody tr', function(e) {
            var $cell = $(e.target).closest('td'),
                msg;
            //console.log('Index ==========='+$cell.index());
            if ($cell.index() != 0) {
                var data = dataTable.row(this).data();
                //
                if ($cell.index() === 8 || $cell.index() === 9) {
                    // console.log($cell);
                } else {
                    viewCategory(data.id);
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
                      
                    @can('Lead Sub Statuses Delete')
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
        $("#leadParameterSidebarList").addClass('collapse show');
        $("#sidebarLeadSubStatus").addClass('active');
        $("#leadParameterSidebar").addClass('active');

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
                    viewCategory(data.id);
                    highlightRow($(this));
                }
            } // end if
        }); // table row click


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
                    dataTable.row().draw(); // redraw the datatable show we don't need to reload the page to see the changes                        
                },
                error: function(result) {
                    console.log(result);
                }
            })
        });

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

        $('#statusBadge').select2({
            placeholder: "Select",
            minimumResultsForSearch: Infinity,
            templateSelection: optionFormat,
            templateResult: optionFormat
        });
       
    </script>
@endsection
