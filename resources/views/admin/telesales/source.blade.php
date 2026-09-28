@extends('admin.layouts.master')
@section('title')
    Lead Sources
@endsection
@section('style')
    <!--datatable css-->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css"/>
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
                        <h4 class="card-title mb-0 ">Lead Sources List</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Lead</a></li>
                            <li class="breadcrumb-item active">Sources</li>
                        </ol>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="form-check form-switch form-switch-right form-switch-md">
                            @can("Lead Sources Create")
                                <button type="button" data-bs-toggle="modal" data-bs-target="#addModal"
                                        class="btn btn-success float-right" style="float-right">
                                    <i class="ri-add-line align-middle me-1"></i> Add new
                                </button>
                            @endcan
                            @can("Lead Sources Delete")
                                <button class="btn btn-soft-danger" onclick="deleteMultiple()"><i
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
                    <table id="sourceDatatable" class="table align-middle" style="width:100%">
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
    <div class="modal zoomIn" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-modal="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel"><span id="modal_title">Add </span> Lead Sources</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="mainForm" class="needs-validation" novalidate>
                        <div class="row g-3">
                            <div class="col-4">

                                <label for="title" class="form-label">Source Name*</label>
                            </div>
                            <div class="col-8">
                                <input type="hidden" name="id" id="id" value="">
                                <input type="text" class="form-control" id="name" name="name"
                                       placeholder="Enter sources name"
                                       required>
                                <div class="invalid-feedback">
                                    Please enter the sources name.
                                </div>
                            </div>
                            <!--end col-->
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
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                    <button id="submitBtn" type="submit" class="btn btn-primary">Submit</button>
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
    @can("Lead Sources View")
        <div class="modal zoomIn" id="viewModal" tabindex="-1" aria-labelledby="addModalLabel" data-bs-backdrop="static"
             data-bs-keyboard="false" tabindex="-1" aria-modal="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header card-header pb-2">
                        <h5 class="modal-title" id="addModalLabel">View Lead Source</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-4">
                                <label for="view-name" class="form-label">Source Name:</label>
                            </div>
                            <div class="col-8" id="view-name"></div>

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

        {{-- <script src=" "></script> --}}


    <script>
        let isUpdate = false;

        let dataTable = $('#sourceDatatable').DataTable({ //datatable initialization
            "order": [
                [0, "DESC"]
            ],
            language: {
                searchPlaceholder: "Search Lead Sources"
            },
            responsive: true,
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "{{route('admin.lead.source.dt')}}",
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
                                     <input class="form-check-input" type="checkbox" name="chk_child" value="` + data.id + `">
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
                        return `<a href="javascript:void(0)" class="text-primary" onclick="viewSource('` + data.id + `')">` + data.name + `</a>`;
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
                        @can("Lead Sources Edit")
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


        $('#sourceDatatable tbody').on('change', 'input.status-change', function () {
            // change lead source  based on button class and taking id from button data attribute
            @can("Lead Sources Edit")
            let id = $(this).data('id');
            let action = $(this).data('do');
            let formData = {
                id: id,
                action: action
            }
            $.ajax({
                type: "POST",
                url: "{{route('admin.lead.source.status')}}",
                data: formData,
                dataType: 'json',
                success: function (data) {
                    if (data.status == '1') {
                        showToastify("Lead source updated successfully", "success")
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
            @can("Lead Sources Create")
            $('#modal_title').html('Add');
            $('#mainForm').trigger('reset');
            $('#mainForm').removeClass('was-validated');
            @endcan
        });

        $(document).on('click', '#deleteBtn', function (event) {
            @can("Lead Sources Delete")
            deleteData($('#id').val());
            @endcan
        });


        $(document).on('click', '#editBtn', function (event) {
            @can("Lead Sources Edit")
            $('#viewModal').modal('hide');
            editData($('#id').val());
            @endcan
        });


        $(document).on('click', '.edit-link', function (event) { //edit link click event it will fetch single cateogry details and put it on form
            @can("Lead Sources Edit")
            let id = $(this).data('id');
            $('#id').val(id);
            editData(id);
            @endcan
        });


        $(document).on('click', '.delete-link', function (event) {
            @can("Lead Sources Delete")
            let id = $(this).data('id');
            deleteData(id);
            @endcan
        });


        function editData(id) {
            @can("Lead Sources Edit")
                isUpdate = true; // set isUpdate to true so that we can identify that this is update request
            $.ajax({
                url: "{{route('admin.lead.source.get')}}",
                type: 'GET',
                dataType: 'json',
                data: {id},
                success: function (result) {
                    if (result.status == '1') {
                        $('#mainForm').trigger('reset');
                        $('#name').val(result.data.name)
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

        // this Delete function

        function deleteData(id) {
            @can("Lead Sources Delete")
            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to use this Source!",
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            })
                .then((willDelete) => {
                    if (willDelete) { // on confirm delete the category
                        $.ajax({
                            url: "{{route('admin.lead.source.delete')}}",
                            type: 'POST',
                            dataType: 'json',
                            data: {id},
                            success: function (result) {
                                if (result.status == '1') { //if successfully deleted
                                    showToastify("Source has been deleted successfully", "success")
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
                url: isUpdate ? "{{route('admin.lead.source.update')}}" : "{{route('admin.lead.source.store')}}",
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
            @can("Lead Sources Delete")
            if (ids_array = [], document.getElementsByName("chk_child").forEach((function (e) {
                if (1 == e.checked) {
                    var t = e.value;
                    ids_array.push(t)

                }
            })), "undefined" != typeof ids_array && ids_array.length > 0) {
                swal({
                    title: "Attention!!",
                    text: "You are source to delete multiple source. Once deleted, you will not be able to use these source!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                    .then((willDelete) => {
                        if (willDelete) {
                            $.ajax({
                                url: "{{route('admin.lead.source.multi.delete')}}",
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
            var e = document.querySelectorAll('.form-check-all input[type="checkbox"]');
            1 == checkAll.checked ? e.forEach((function (e) {
                e.checked = !0
                //e.closest("tr").classList.add("table-active")
            })) : e.forEach((function (e) {
                e.checked = !1
                //e.closest("tr").classList.remove("table-active")
            }))
        });

        function viewSource(id) {
            @can("Lead Sources View")
            $.ajax({
                url: "{{route('admin.lead.source.get')}}",
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
        dataTable.on('click', 'tbody tr', function(e) {
            var $cell = $(e.target).closest('td'),
                msg;
            //console.log('Index ==========='+$cell.index());
            if ($cell.index() != 0) {
                var data = dataTable.row(this).data();
                //
                if ($cell.index() === 11 || $cell.index() === 12) {
                    // console.log($cell);
                } else {
                    viewSource(data.id);
                }
            } // end if
        }); // table row click
        dataTable.on('draw.dt', function() {
            $(".chk_child").prop("checked", false);
            $('.multi_options').remove();
            $('#checkAll').prop("checked", false);
        });
         $("#leadParameterSidebarList").addClass('collapse show');
        $("#sidebarLeadSource").addClass('active');
    </script>

@endsection
