@extends('admin.layouts.master')
@section('title')
    Property bath
@endsection
@section('style')
    <!--datatable css-->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css"/>
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css"/>
         <!--datatable responsive css-->
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet"
          type="text/css"/>
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
                        <h4 class="card-title mb-0 ">Property Bath List</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Property</a></li>
                            <li class="breadcrumb-item active">Bath</li>
                        </ol>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="form-check form-switch form-switch-right form-switch-md">
                            @can("Baths Create")
                                <button type="button" data-bs-toggle="modal"
                                        data-bs-target="#addModal" class="btn btn-success float-right addModal action-add"
                                        style="float-right">
                                    <i class="ri-add-line align-middle me-1"></i> Add new
                                </button>
                            @endcan
                            @can("Baths Delete")
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
                    <table id="bathDatatable" class="table align-middle" style="width-100%">
                        <thead class="">
                            <tr class="text-uppercase table-light2">
                            <th scope="col" style="width: 50px;">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="checkAll" value="option"
                                           data-com.bitwarden.browser.user-edited="yes">
                                </div>
                            </th>
                            <th>ID</th>
                            <th>Bath Name</th>
                            <th>Order</th>
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
                    <h5 class="modal-title" id="addModalLabel"><span id="modal_title">Add </span> Property Bath</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="mainForm" class="needs-validation" novalidate>
                        <div class="row g-3">
                            <div class="col-4">
                                <label for="title" class="form-label">Bath Title*</label>
                            </div>
                            <div class="col-8">
                                <input type="hidden" name="id" id="id" value="">
                                <input type="text" class="form-control" id="name" name="name"
                                       placeholder="Enter title" required>
                                <div class="invalid-feedback">
                                    Please enter the Bath name.
                                </div>
                            </div>
                            <div class="col-4">
                                <label for="title" class="form-label">Order*</label>
                            </div>
                            <div class="col-8">
                                <input type="text" class="form-control" id="order" name="order"
                                       placeholder="Enter order" required>
                                <div class="invalid-feedback">
                                    Please enter the order.
                                </div>
                            </div>
                            <div class="col-12">
                                <h5>Portals</h5>
                            </div>
                            @foreach($portals ?? [] as $portal)
                                <div class="col-4 mt-2">
                                    <label for="portal-{{ $portal->id }}" class="form-label">{{ $portal->name }}</label>
                                </div>
                                <div class="col-8 mt-2">
                                    <input type="text" class="form-control portal-title" id="portal-{{ $portal->id }}"
                                           name="portals[{{$portal->id}}][title]"
                                           placeholder="{{ $portal->name }} title" required>
                                    <div class="invalid-feedback">
                                        Please enter the {{ $portal->name }} title.
                                    </div>
                                </div>
                            @endforeach
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
    @can("Baths View")
        <div class="modal zoomIn" id="viewModal" tabindex="-1" aria-labelledby="addModalLabel" data-bs-backdrop="static"
             data-bs-keyboard="false" tabindex="-1" aria-modal="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header card-header pb-2">
                        <h5 class="modal-title" id="addModalLabel">View Property Bath</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-4">
                                <label for="view-name" class="form-label">Type Title:</label>
                            </div>
                            <div class="col-8" id="view-name"></div>
                            <div class="col-4">
                                <label for="view-order" class="form-label">Order:</label>
                            </div>
                            <div class="col-8" id="view-order"></div>

                            <div class="col-4">
                                <label class="form-check-label" for="statusCheckbox">Status:</label>
                            </div>
                            <div class="col-8" id="view-status"></div>
                            <div class="col-12">
                                <h5>Portals</h5>
                            </div>
                            <div class="col-12 row" id="view-portals"></div>

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

        $(document).on("keyup", "#name", function () {
            if (!isUpdate) {
                $(".portal-title").val($(this).val())
            }
        })

        let dataTable = $('#bathDatatable').DataTable({ //datatable initialization
            "order": [[3, "asc"]],
            language: {
                searchPlaceholder: "Search property bath"
            },
            createdRow: function(row, data, index) {
                $('#bathDatatable tbody').css('cursor', 'pointer');

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
                url: "{{route('admin.property.bath.dt')}}",
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
                }
                , {
                    data: 'name'

                }
                , {
                    data: 'order'

                }
                , {
                    data: null,
                    orderable: false,
                }
                , {
                    data: null,
                    orderable: false,

                }
                , {
                    data: null,
                    orderable: false,
                }
                , {
                    data: null,
                    orderable: false,
                }
                , {
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
                        return `<div class="form-check"><input class="form-check-input selectable-checkbox" type="checkbox" name="chk_child" value="${data.id}"></div>`;
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
                        return `<a href="javascript:void(0)" class="text-primary" onclick="viewBath('` + data.id + `')">` + data.name + `</a>`;
                    }
                },
                {
                    "targets": 3,
                    orderable: true,
                    "data": "order",
                    render: function (type, row, data, meta) {
                        return data.order;
                    }
                },
                {
                    "targets": 4,
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
                    "targets": 5,
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
                    "targets": 6,
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
                    "targets": 7,
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
                    targets: 8,
                    orderable: false,
                    visible: true,
                    /*This will hide particular column*/
                    render: function (type, row, data, meta) {
                        let disabled = true;
                        @can("Baths Edit")
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


        $('#bathDatatable tbody').on('change', 'input.status-change', function () {
            @can("Baths Edit")
            // change property category status based on button class and taking id from button data attribute
            let id = $(this).data('id');
            let action = $(this).data('do');
            let formData = {
                id: id,
                action: action
            }
            $.ajax({
                type: "POST",
                url: "{{route('admin.property.bath.status')}}",
                data: formData,
                dataType: 'json',
                success: function (data) {
                    if (data.status == '1') {
                        showToastify("Property bath status updated successfully", "success")
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
            @can("Baths Create")
                isUpdate = false;
            $('#modal_title').html('Add');
            $("#id").val("")
            $('#mainForm').trigger('reset');
            $('#mainForm').removeClass('was-validated');
            @endcan
        });


        $(document).on('click', '#deleteBtn', function (event) {
            @can("Baths Delete")
            deleteData($('#id').val());
            @endcan
        });

        $(document).on('click', '#editBtn', function (event) {
            @can("Baths Edit")
            $('#viewModal').modal('hide');
            editData($('#id').val());
            @endcan
        });


        $(document).on('click', '.edit-link', function (event) {
            @can("Baths Edit")//edit link click event it will fetch single cateogry details and put it on form
            let id = $(this).data('id');
            $('#id').val(id);
            editData(id);
            @endcan
        });

        $(document).on('click', '.delete-link', function (event) {
            @can("Baths Delete")
            let id = $(this).data('id');
            deleteData(id);
            @endcan
        });

        $(document).on('submit', '#mainForm', function (event) {
            $("#submitBtn").prop('disabled', true);
            event.preventDefault();
            var formData = new FormData($(this)[0]);
            $.ajax({
                url: isUpdate ? "{{route('admin.property.bath.update')}}" : "{{route('admin.property.bath.store')}}",
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
            @can("Baths Delete")
                if (ids_array = [], document.getElementsByName("chk_child").forEach((function(e) {
                        if (1 == e.checked) {
                            var t = e.value;
                            ids_array.push(t)

                        }
                    })), "undefined" != typeof ids_array && ids_array.length > 0) {
                    var content = document.createElement('div');
                    content.innerHTML = "You are about to delete <b>" + ids_array.length +
                        "</b> Features. Once deleted, you will not be able to use them!";
                    Swal.fire({

                            html: '<div class="mt-3"><lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon><div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you Sure ?</h4><p class="text-muted mx-4 mb-0">Are you Sure You want to Delete ' +
                                ids_array.length + ' Bath(s)?</p></div></div>',
                            showCancelButton: !0,
                            confirmButtonClass: "btn btn-danger w-xs me-2 mb-1",
                            confirmButtonText: "Yes, Delete " + ids_array.length + " Bath(s).",
                            cancelButtonClass: "btn btn-soft-success w-xs mb-1",
                            buttonsStyling: !1,
                            showCloseButton: !0


                        })
                        .then(function(t) {

                            if (t.isConfirmed) { // on confirm delete the category
                                $.ajax({
                                    url: "{{route('admin.property.bath.multi.delete')}}",
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


        function viewBath(id) {
            @can("Baths View")
            $.ajax({
                url: "{{route('admin.property.bath.get')}}",
                type: 'GET',
                dataType: 'json',
                data: {id},
                success: function (result) {
                    if (result.status == '1') {
                        $('#id').val(result.data.id);
                        $("#view-portals").html("")

                        if (result.data) {
                            Object.keys(result.data).map((index) => {
                                $(`#view-${index}`).html(result.data[index])
                            })
                            result?.data?.portals.map(item => {

                                let portal = `<div class="col-4"><label class="form-label">${item?.portal?.name}:</label></div><div class="col-8">${item.name}</div>`
                                $("#view-portals").append(portal)
                            })
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


        $("#checkAll").change(function () {
            $(".selectable-checkbox").prop("checked", this.checked)
        })

        function deleteData(id) {
            @can("Baths Delete")
                Swal.fire({

                        html: '<div class="mt-3"><lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon><div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you Sure ?</h4><p class="text-muted mx-4 mb-0">Are you Sure You want to Delete this Bath ?</p></div></div>',
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
                                url: "{{route('admin.property.bath.delete')}}",
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    id
                                },
                                success: function(result) {
                                    if (result.status == '1') { //if successfully deleted
                                        showToastify("Bath has been deleted successfully", "success")
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



        function editData(id) {
            @can("Baths Edit")
                isUpdate = true; // set isUpdate to true so that we can identify that this is update request
            $.ajax({
                url: "{{route('admin.property.bath.get')}}",
                type: 'GET',
                dataType: 'json',
                data: {id},
                success: function (result) {
                    if (result.status == '1') {
                        $('#mainForm').trigger('reset');
                        $('#name').val(result.data.name);
                        $('#order').val(result.data.order);
                        $('#id').val(result.data.id)
                        result.data?.portals.map(item => {
                            $(`#portal-${item.portal_id}`).val(item.name);
                        })
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
                if ($cell.index() === 8 || $cell.index() === 9) {
                    // console.log($cell);
                } else {
                    viewBath(data.id);
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

                    @can('Baths Delete')
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
 $("#sidebarParameters").addClass('collapse show');
        $("#sidebarBaths").addClass('active');
        $("#propertyManagementSidebar").addClass('active');
    </script>
@endsection
