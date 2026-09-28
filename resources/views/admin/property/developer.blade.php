@extends('admin.layouts.master')
@section('title')
    Developers
@endsection
@section('style')
    <!--datatable css-->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet"
        type="text/css" />
    <link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />

    {{-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script> --}}

    <!--datatable responsive css-->
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet"
        type="text/css" />
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title mb-0 ">Developers List</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">General</a></li>
                            <li class="breadcrumb-item active">Source</li>
                        </ol>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="form-check form-switch form-switch-right form-switch-md">
                            @can('Developer Create')
                                {{-- <button type="button" data-bs-toggle="modal" data-bs-target="#addModal"
                                    class="btn btn-success float-right action-add" style="float-right" id="newBtn">
                                    <i class="ri-add-line align-middle me-1"></i> Add new
                                </button> --}}

                                <button type="button" data-bs-toggle="modal" data-bs-target="#addModal"
                                class="btn btn-success float-right action-add" style="float-right" id="newBtn">
                                    <i class="ri-add-line align-middle me-1"></i> Add new
                                </button>
                            @endcan
                            @can('Developer Delete')
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

                    <table id="datatable" class="table align-middle" style="width-100%">
                        <thead class="">
                            <tr class="text-uppercase table-light2">
                                <th scope="col" style="width-50px;">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="checkAll" value="option"
                                            data-com.bitwarden.browser.user-edited="yes">
                                    </div>
                                </th>
                                <th>ID</th>
                                <th>Short Name</th>
                                <th>Full Name</th>
                                <th>Email ID</th>
                                <th>Contact</th>
                                <th>Website</th>
                                <th>Types</th>
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

    <div class="modal zoomIn" id="addModal" tabindex="-1" role="dialog"  aria-labelledby="addModalLabel"  data-bs-backdrop="static" data-keyboard="false" aria-modal="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel"><span id="modal_title">Add </span> Developer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="mainForm" class="needs-validation" novalidate>
                        <div class="row g-3">
                            <div class="col-4">
                                <label for="title" class="form-label"> Short Name*</label>
                            </div>
                            <div class="col-8">
                                <input type="hidden" name="id" id="id" value="">
                                <input type="text" class="form-control" id="short_name" name="short_name"
                                    placeholder="Enter short name" required>
                                <div class="invalid-feedback">
                                    Please enter the Short name.
                                </div>
                            </div>


                            <div class="col-4">

                                <label for="title" class="form-label">Full Name</label>
                            </div>
                            <div class="col-8">
                                <input type="text" class="form-control" id="full_name" name="full_name"
                                    placeholder="ri line">
                                <div class="invalid-feedback">
                                    Please enter the Full name.
                                </div>
                            </div>


                            <div class="col-4">

                                <label for="title" class="form-label">Description</label>
                            </div>
                            <div class="col-8">
                                <input type="text" class="form-control" id="description" name="description"
                                    placeholder="Lorem Ipsum">
                                <div class="invalid-feedback">
                                    Please enter the description.
                                </div>
                            </div>


                            <div class="col-4">

                                <label for="title" class="form-label">Contact Person</label>
                            </div>
                            <div class="col-8">
                                <input type="text" class="form-control" id="contact_person" name="contact_person"
                                    placeholder="Contact Person">
                                <div class="invalid-feedback">
                                    Please enter the contact person.
                                </div>
                            </div>

                            <div class="col-4">

                                <label for="title" class="form-label">phone</label>
                            </div>
                            <div class="col-8">
                                <input type="text" class="form-control" id="phone" name="phone"
                                    placeholder="971----">
                                <div class="invalid-feedback">
                                    Please enter the phone.
                                </div>
                            </div>

                            <div class="col-4">

                                <label for="title" class="form-label">email</label>
                            </div>
                            <div class="col-8">
                                <input type="text" class="form-control" id="email" name="email"
                                    placeholder="abc@axcapital.ae">
                                <div class="invalid-feedback">
                                    Please enter the email.
                                </div>
                            </div>

                            <div class="col-4">

                                <label for="title" class="form-label">website</label>
                            </div>
                            <div class="col-8">
                                <input type="text" class="form-control" id="website" name="website"
                                    placeholder="https://www.axcapital.ae">
                                <div class="invalid-feedback">
                                    Please enter the website.
                                </div>
                            </div>
                            <!--end col-->

                            <div class="col-4">
                                <label for="logo_path" class="form-label">Logo</label>
                            </div>
                            <div class="col-8">


                                <div class="mb-2  d-none" id="logo_pathdiv">
                                    <img src="" alt="Image" id="logo_path-img"
                                        class="img-logo_path img-responsive" style="max-width:50px">
                                    <i class="ri-delete-bin-5-fill text-danger ms-2 generalDelete" data-id=""
                                        id="logo_path_id" data-key="" data-model="App\Models\"></i>
                                </div>



                                <input type="file" class="form-control" id="logo_path" name="logo_path">
                                <div class="invalid-feedback">
                                    Please upload the  Logo.
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



                        </div>
                        <!--end col-->

                        <div class="col-lg-12">
                            <div class="hstack gap-2 justify-content-end">
                                <button type="button" class="btn btn-light action-close"
                                    data-bs-dismiss="modal">Close</button>
                                <button id="submitBtn" type="submit" class="btn btn-primary action-save">Submit</button>
                            </div>
                        </div>
                        <!--end col-->
                    </form>
                </div>
                <!--end row-->

            </div>
        </div>
    </div>
    @can('Developer View')
        <div class="modal zoomIn" id="viewModal" tabindex="-1" aria-labelledby="addModalLabel" data-bs-backdrop="static"
            data-bs-keyboard="false" tabindex="-1" aria-modal="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header card-header pb-2">
                        <h5 class="modal-title" id="addModalLabel">View Developer</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-4">
                                <label for="view-short_name" class="form-label">Short Name:</label>
                            </div>
                            <div class="col-8" id="view-short_name"></div>
                            <div class="col-4">
                                <label class="form-check-label" for="full_name">Full Name:</label>
                            </div>
                            <div class="col-8" id="view-full_name">
                            </div>

                            <div class="col-4">
                                <label class="form-check-label" for="description">description:</label>
                            </div>
                            <div class="col-8" id="view-description">
                            </div>


                            <div class="col-4">
                                <label class="form-check-label" for="contact_person">Contact Person:</label>
                            </div>
                            <div class="col-8" id="view-contact_person">
                            </div>



                            <div class="col-4">
                                <label class="form-check-label" for="phone">phone:</label>
                            </div>
                            <div class="col-8" id="view-phone">
                            </div>

                            <div class="col-4">
                                <label class="form-check-label" for="email">email:</label>
                            </div>
                            <div class="col-8" id="view-email">
                            </div>
                            <div class="col-4">
                                <label class="form-check-label" for="website">website:</label>
                            </div>
                            <div class="col-8" id="view-website">
                            </div>

                            <div class="col-4 view_logo_path">
                                <label class="form-check-label" for="logo_pathCheckbox">logo_path:</label>
                            </div>
                            <div class="col-8 view_logo_path" id="view-logo_path">
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


        let dataTable = $('#datatable').DataTable({ //datatable initialization
            "order": [
                [0, "DESC"]
            ],
            dom: 'Bfrtip',
            buttons: [{
                extend: 'colvis',
                titleAttr: 'Show/Hide',
                text: '<i class="colvis" style="font-size:09px">Show / Hide</i>',

            }],
            language: {
                searchPlaceholder: "Search Developers"
            },
            createdRow: function(row, data, index) {
                $('#datatable tbody').css('cursor', 'pointer');

            },
            responsive: true,
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "{{ route('admin.property.developers.dataTable') }}",
                type: "post",
                data: function(d) {},
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
                $(`@includeIf('admin.contact.include.filterButtons')`).insertAfter(".dataTables_filter input")
            },

            "aoColumns": [{
                data: null,
                orderable: false,
            }, {
                data: 'id',
                name: 'id',
            }, {
                data: 'short_name',
                name: 'short_name',
            }, {
                data: 'full_name',
                name: 'full_name',
                orderable: false,
            }, {
                data: 'email',
                name: 'email',
                orderable: false,
            }, {
                data: 'phone',
                name: 'phone',
                orderable: false,
            }, {
                data: 'website',
                name: 'website',
                orderable: false,
            }, {
                data: null,
                name: null,
                orderable: false,
            }, {
                data: null,
                orderable: false,
            }, {
                data: null,
                orderable: false,
            }],
            "columnDefs": [{
                    "targets": 0,
                    orderable: true,
                    render: function(type, row, data, meta) {
                        return `<div class="form-check">
                                                <input class="form-check-input selectable-checkbox" type="checkbox" name="chk_child" value="` +
                            data.id + `">
                                            </div>`;
                    }
                },
                {
                    "targets": 2,
                    orderable: true,
                    render: function(type, row, data, meta) {
                        return `<a href="javascript:void(0)" class="text-primary" onclick="viewContact('` +
                            data.id + `')">` + data.short_name + `</a>`;
                    }
                },
                {
                    "targets": 7,
                    orderable: true,
                    render: function(type, row, data, meta) {

                        return '';
                    }
                },
                {
                    targets: 8,
                    orderable: false,
                    visible: true,
                    /*This will hide particular column*/
                    render: function(type, row, data, meta) {
                        let disabled = true;
                        @can('Contacts Edit')
                            disabled = false
                        @endcan

                        if (data.status == '1') {
                            return `<div  class="form-check form-switch form-switch-lg form-switch-success" dir="ltr">
                                        <input ${disabled ? "disabled" : ""} type="checkbox" data-id="${data.id}" data-do="0" name="status" class="form-check-input status-change" checked></div>`;
                        } else {
                            return `<div  class="form-check form-switch form-switch-lg form-switch-success " dir="ltr">
                                        <input ${disabled ? "disabled" : ""} data-id="${data.id}" data-do="1" type="checkbox" name="status" class="form-check-input status-change"></div>`;
                        }
                    }
                },
                {
                    targets: 9,
                    orderable: false,
                    visible: true,
                    render: function(type, row, data, meta) {
                        return `<div class="dropdown d-inline-block">
                                    <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ri-more-fill align-middle"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a href="javascript:void(0)" data-id="` + data.id + `" class="dropdown-item edit-item-btn edit-link"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit</a></li>
                                        <li>
                                            <a href="javascript:void(0)" onclick="deleteData(` + data.id +
                            `)" data-id="` + data.id + `" class="dropdown-item remove-item-btn delete-link">
                                                <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete
                                            </a>
                                        </li>
                                    </ul>
                                </div>`
                    }
                }
            ]
        });


        dataTable.on('draw.dt', function() {
            $(".chk_child").prop("checked", false);
            $('.multi_options').remove();
            $('#checkAll').prop("checked", false);
        });

        $('#sourceDatatable tbody').on('change', 'input.status-change', function() {
            // change property category status based on button class and taking id from button data attribute
            @can('Developer Edit')
                let id = $(this).data('id');
                let action = $(this).data('do');
                let formData = {
                    id: id,
                    action: action
                }
                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.general.source.status') }}",
                    data: formData,
                    dataType: 'json',
                    success: function(data) {
                        if (data.status == '1') {
                            showToastify("Contact source status updated successfully", "success")
                            dataTable.row().draw();
                        }
                    },
                    error: function(data) {
                        console.log('Error:', data);
                    }
                });
            @endcan
        });


        $('#sourceDatatable tbody').on('change', 'input.status-lead', function() {
            // change property category status based on button class and taking id from button data attribute
            @can('Developer Edit')
                let id = $(this).data('id');
                let action = $(this).data('do');
                let formData = {
                    id: id,
                    action: action
                }
                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.general.source.lead') }}",
                    data: formData,
                    dataType: 'json',
                    success: function(data) {
                        if (data.status == '1') {
                            showToastify("Source Lead Status updated successfully", "success")
                            dataTable.row().draw();
                        }
                    },
                    error: function(data) {
                        console.log('Error:', data);
                    }
                });
            @endcan
        });
        $('#sourceDatatable tbody').on('change', 'input.status-contact', function() {
            // change property category status based on button class and taking id from button data attribute
            @can('Developer Edit')
                let id = $(this).data('id');
                let action = $(this).data('do');
                let formData = {
                    id: id,
                    action: action
                }
                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.general.source.contact') }}",
                    data: formData,
                    dataType: 'json',
                    success: function(data) {
                        if (data.status == '1') {
                            showToastify("Source Contact Status updated successfully", "success")
                            dataTable.row().draw();
                        }
                    },
                    error: function(data) {
                        console.log('Error:', data);
                    }
                });
            @endcan
        });

        $(document).on('click', '.addModal', function (event) {
            @can("Transaction Types Create")
                isUpdate = false;
            //  initColorPicker("#color-picker-parent", "#color_code")
            $('#modal_title').html('Add');
            $("#id").val("")
            $('#mainForm').trigger('reset');
            $('#mainForm').removeClass('was-validated');
            @endcan
        });

        $(document).on('click', '#deleteBtn', function(event) {
            @can('Developer Delete')
                deleteData($('#id').val());
            @endcan
        });


        $(document).on('click', '#editBtn', function(event) {
            @can('Developer Edit')
            $('#mainForm').trigger("reset");

                $('#viewModal').modal('hide');
                editData($('#id').val());
            @endcan
        });


        $(document).on('click', '.edit-link', function(
            event) { //edit link click event it will fetch single cateogry details and put it on form
            @can('Developer Edit')
                let id = $(this).data('id');
                $('#id').val(id);
                editData(id);
            @endcan
        });


        $(document).on('click', '.delete-link', function(event) {
            @can('Developer Delete')
                let id = $(this).data('id');
                deleteData(id);
            @endcan
        });


        function editData(id) {
            @can('Developer Edit')
                isUpdate = true; // set isUpdate to true so that we can identify that this is update request
                $.ajax({
                    url: "{{ route('admin.property.developers.get.data') }}",
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        id
                    },
                    success: function(result) {
                        if (result.status == '1') {
                            $('#mainForm').trigger('reset');
                            $('#short_name').val(result.data.short_name)
                            $('#id').val(result.data.id)
                            $('#full_name').val(result.data.full_name)
                            $('#contact_person').val(result.data.contact_person)
                            $('#phone').val(result.data.phone)
                            $('#email').val(result.data.email)
                            $('#website').val(result.data.website)

                            if (result.data.logo_path) {
                                $('#logo_pathdiv').removeClass('d-none')
                                $('#logo_path-img').attr('src', result.data.logo_path)
                                $('#logo_path_id').attr('data-id', result.data.id)
                                $('#logo_path_id').attr('data-key', 'thumbnail')

                            } else {
                                $('#thumbnaildiv').addClass('d-none')
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
                    error: function(result) {
                        console.log(result);
                    }
                });
            @endcan
        }

        // this Delete function




        function deleteData(id) {
            @can('Developer Delete')
                Swal.fire({

                        html: '<div class="mt-3"><lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon><div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you Sure ?</h4><p class="text-muted mx-4 mb-0">Are you Sure You want to Delete this Developer ?</p></div></div>',
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
                                url: "{{ route('admin.property.developers.delete') }}",
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    id
                                },
                                success: function(result) {
                                    if (result.status == '1') { //if successfully deleted
                                        showToastify("Developer has been deleted successfully", "success")
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


        $(document).on('submit', '#mainForm', function(event) {
            $("#submitBtn").prop('disabled', true);
            event.preventDefault();
            var formData = new FormData($(this)[0]);
            $.ajax({
                url: isUpdate ? "{{ route('admin.property.developers.update') }}" :
                    "{{ route('admin.property.developers.store') }}",
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
                        dataTable.row()
                            .draw(); // redraw the datatable show we don't need to reload the page to see the changes
                        isUpdate =
                            false; // set isUpdate to false so that when we try to add new category it will be create request
                    } else if (result.status == '2') {
                        $("#submitBtn").prop('disabled', false);
                        // showToastify(result.msg, "error")
                        //    console.table(result.errors)
                        $.each(result.errors, function(index, item) {

                            showToastify(item[0], "error")
                        })

                    } else {

                        $("#submitBtn").prop('disabled', false);
                        showToastify(result.msg, "error")
                    }
                },
                error: function(jqXhr, json, errorThrown) {
                    let data = jqXhr.responseJSON;
                    $("#submitBtn").prop('disabled', false);
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
            @can('Developers Delete')
                if (ids_array = [], document.getElementsByName("chk_child").forEach((function(e) {
                        if (1 == e.checked) {
                            var t = e.value;
                            ids_array.push(t)

                        }
                    })), "undefined" != typeof ids_array && ids_array.length > 0) {
                    var content = document.createElement('div');
                    content.innerHTML = "You are about to delete <b>" + ids_array.length +
                        "</b> Developers. Once deleted, you will not be able to use them!";
                    Swal.fire({

                            html: '<div class="mt-3"><lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon><div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you Sure ?</h4><p class="text-muted mx-4 mb-0">Are you Sure You want to Delete ' +
                                ids_array.length + ' Developer(s)?</p></div></div>',
                            showCancelButton: !0,
                            confirmButtonClass: "btn btn-danger w-xs me-2 mb-1",
                            confirmButtonText: "Yes, Delete " + ids_array.length + " Developer(s).",
                            cancelButtonClass: "btn btn-soft-success w-xs mb-1",
                            buttonsStyling: !1,
                            showCloseButton: !0


                        })
                        .then(function(t) {

                            if (t.isConfirmed) { // on confirm delete the category
                                $.ajax({
                                    url: "{{ route('admin.property.developers.delete.multiple') }}",
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




        // checkbox select all functionality
        var checkAll = document.getElementById("checkAll");
        checkAll && (checkAll.onclick = function() {
            var e = document.querySelectorAll('.form-check-all input[name="chk_child"]');
            1 == checkAll.checked ? e.forEach((function(e) {
                e.checked = !0
                //e.closest("tr").classList.add("table-active")
            })) : e.forEach((function(e) {
                e.checked = !1
                //e.closest("tr").classList.remove("table-active")
            }))
        });

        function viewSource(id) {
            @can('Developer View')
                $.ajax({
                    url: "{{ route('admin.property.developers.get.data') }}",
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        id
                    },
                    success: function(result) {
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
                            if (result.data.badge) {
                                $("#view-badge").html(
                                    '<span class="' + result.data.badge + '">Badge</span>');
                            }
                            if (result.data.thumbnail) {
                                $('.view_thumbnail').removeClass('d-none')
                                $("#view-thumbnail").html('<img src="' + result.data.thumbnail +
                                    '" class="img-fluid" width="50" height="50">');
                            } else {
                                $('.view_thumbnail').addClass('d-none')
                            }



                            if (result.data.logo_path) {
                                // alert(result.data.logo_path)
                                $('.view_logo_path').removeClass('d-none')
                                $("#view_logo_path").html('<img src="' + result.data.logo_path +
                                    '" class="img-fluid" width="50" height="50">');
                            } else {
                                $('.view_logo_path').addClass('d-none')
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


        $('#newBtn').click(function() {

            $('#mainForm').trigger("reset");
            $('#thumbnaildiv').addClass('d-none');

        });
        $('#thumbnail_id__Extra').click(function() {
            let source_id = $(this).data('id');
            $.ajax({
                url: "{{ route('admin.general.source.delete.thumbnail') }}",
                type: 'POST',
                dataType: 'json',
                data: {
                    id: source_id
                },
                success: function(result) {

                    editData(source_id)
                    dataTable.row()
                        .draw(); // redraw the datatable show we don't need to reload the page to see the changes
                    // set isUpdate to false so that when we try to add new category it will be create request

                },
                error: function(result) {
                    console.log(result);
                }
            })
        });


        /**
         * 1st parameter is Model path
         * 2nd Parameter is Record ID
         * 3rd Parameter is Module name
         * 4th Parameter is Datatable name = Nullable
         * 5th Parameter is return URL = Nullable
         */

        function deleteDataGeneral(action_title, model, id, reload_dt = null, redirect_url = null) {

            console.log(id);
            let title = "Are you sure you want to " + action_title + " this Record?";

            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-brand',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: true,
            })

            swalWithBootstrapButtons.fire({
                title: title,
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel!',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {

                    $.ajax({
                        url: "{{ route('admin.general.delete.delete_general') }}",
                        type: 'POST',
                        dataType: "JSON",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            "id": id,
                            "model": model
                        }
                    }).done(function(response) {
                        if (response.success) {
                            showToastify('Record Deleted Successfully', "success")
                            dataTable.row().draw(); //

                            if (reload_dt != null) {
                                dataTable.row().draw();

                            }
                            dataTable.row().draw();
                        }
                        if (response.error) {

                            showToastify(response.message, "Error")
                            dataTable.row().draw(); //

                        } else if (response.error_related_data) {

                            // console.log(response.error_related_data);
                            let ul = '<ul class="delete_related_dta kt-nav">';
                            $.each(response.error_related_data, function(key, value) {
                                ul += '<li class="kt-nav__item" style="list-decoration:none">' +
                                    key + ': ' + value + '</li>';

                            });

                            ul += '</ul>';

                            swalWithBootstrapButtons.fire({
                                title: "Can't Delete! Related Data Exists",
                                html: ul,
                                showCancelButton: true,
                                reverseButtons: true,
                                confirmButtonText: 'Delete all related data!',
                            }).then((result) => {
                                if (result.value) {

                                    $.ajax({
                                        url: "{{ route('admin.general.delete.delete_listing_hard') }}",
                                        type: 'POST',
                                        dataType: "JSON",
                                        data: {
                                            "_token": "{{ csrf_token() }}",
                                            "data_id": id,
                                            "model": model
                                        }
                                    }).done(function(response) {
                                        dataTable.row().draw(); //
                                        showToastify(response.message, "success")

                                    });


                                } // end if
                            }) // end then

                        } // end else
                        // swal("Deleted!",response.message, "success")
                    });


                }
            })


        }

        $(document).on('click', '.delete-links', function($ths) {
            /**
             * 1st parameter is Model path
             * 2nd Parameter is Record ID
             * 3rd Parameter is Module name
             * 4th Parameter is Datatable name = Nullable
             * 5th Parameter is return URL = Nullable
             */
            deleteDataGeneral('Delete', $(this).attr('data-modal'), this.id, $('#kt_table_pt'), null);
        });



        $(document).on('click', '.delete_record_top', function($ths) {
            /**
             * 1st parameter is Model path
             * 2nd Parameter is Record ID
             * 3rd Parameter is Module name
             * 4th Parameter is Datatable name = Nullable
             * 5th Parameter is return URL = Nullable
             */
            deleteDataGeneral('Delete', $('#modal_name').val(), $('#id_for_delete').val(), $('#kt_table_pt'), null);
        });

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
                if ($cell.index() === 8 || $cell.index() === 9 || $cell.index() === 10 || $cell.index() === 11) {
                    // console.log($cell);
                } else {
                    viewSource(data.id);
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

                    @can('Developer Delete')
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
        $("#sidebarDevelopers").addClass('active');
        $("#propertyManagementSidebar").addClass('active');

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


    </script>
@endsection
