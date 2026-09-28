@extends('admin.layouts.master')
@section('title')
    Commission Slab
@endsection
@section('style')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" type="text/css" />
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script> --}}
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title mb-0 ">Document Library Type</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Documents</a></li>
                            <li class="breadcrumb-item active">Library Type</li>
                        </ol>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="form-check form-switch form-switch-right form-switch-md">
                            @can('Sources Create')
                                <button type="button" data-bs-toggle="modal" data-bs-target="#addModal"
                                        class="btn btn-success float-right action-add addModal" style="float-right" id="newBtn">
                                    <i class="ri-add-line align-middle me-1"></i> Add new
                                </button>
                            @endcan
                            @can('Sources Delete')
                                <button class="btn btn-soft-danger action-delete d-none" onclick="deleteMultiple()"><i
                                        class="ri-delete-bin-2-line"></i></button>
                            @endcan
                            <button id="editBtn" type="button" class="btn btn-info action-edit d-none">Edit</button>
                            <button id="addSlabLimits" type="button" class="btn btn-info action-edit d-none">Add Slab
                                Limit</button>
                            <button class="btn btn-soft-danger action-delete d-none" id="deleteBtn"><i
                                    class="ri-delete-bin-2-line"></i></button>
                            <button type="button" id="closeBtn" class="btn btn-light action-close d-none"
                                    data-bs-dismiss="modal">Close</button>
                            <button id="saveBtn" type="submit" class="btn btn-primary action-save d-none">Submit</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="collapse slideIn" id="collapseContactForm">
                <div class="">
                    <div class=" row">
                        <div>
                            <form id="mainForm" class="needs-validation" novalidate>

                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row g-3">
                                                <div class="col-6 row">
                                                    <div class="col-4 mt-2">
                                                        <label for="title" class="form-label">Name*</label>
                                                    </div>
                                                    <div class="col-8 mt-2">
                                                        <input type="hidden" name="id" id="id" value="">
                                                        <input type="text" class="form-control inputs" id="name"
                                                               name="name" placeholder="Enter source name" required>
                                                        <div class="invalid-feedback">
                                                            Please enter the name.
                                                        </div>
                                                    </div>
                                                    <div class="col-4 mt-2">
                                                        <label for="title" class="form-label">Description*</label>
                                                    </div>
                                                    <div class="col-8 mt-2">
                                                <textarea class="form-control inputs" id="description" placeholder="Enter description" name="description" rows="6"
                                                          maxlength="2000"></textarea>
                                                        <div class="invalid-feedback">
                                                            Please enter the description.
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="col-6 row">
                                                    <div class="col-4 mt-2">
                                                        <label for="order" class="form-label"> Order</label>
                                                    </div>
                                                    <div class="col-8 mt-2">
                                                        <input type="number" class="form-control inputs" id="order"
                                                               name="order" placeholder="Enter source order">
                                                        <div class="invalid-feedback">
                                                            Please enter the source order.
                                                        </div>
                                                    </div>
                                                    <div class="col-4 mt-2">
                                                        <label for="title" class="form-label">Icon</label>
                                                    </div>
                                                    <div class="col-8 mt-2">
                                                        <input type="text" class="form-control inputs" id="icon"
                                                               name="icon" placeholder="ri line">
                                                        <div class="invalid-feedback">
                                                            Please enter the icon class.
                                                        </div>
                                                    </div>
                                                    <!--end col-->
                                                    <div class="col-4 mt-2">
                                                        <label for="badge" class="form-label"> Badge</label>
                                                    </div>
                                                    <div class="col-8 mt-2">
                                                        <select id="statusBadge"
                                                                class="form-select form-select-transparent commonSelect2 inputs"
                                                                name="status_badge" data-placeholder="Select Badge">
                                                            @include('admin.partials.badgeoptions')
                                                        </select>
                                                        <div class="invalid-feedback">
                                                            Please enter the status Badge.
                                                        </div>
                                                    </div>
                                                    <div class="col-4 mt-2">
                                                        <label for="thumbnail" class="form-label">Thumbnail</label>
                                                    </div>
                                                    <div class="col-8 mt-2">
                                                        <div class="mb-2  d-none" id="thumbnaildiv">
                                                            <img src="" alt="Image" id="thumb-img"
                                                                 class="img-thumbnail img-responsive" style="max-width:50px">
                                                            <i class="ri-delete-bin-5-fill text-danger ms-2 generalDelete"
                                                               data-id="" id="thumbnail_id" data-key=""
                                                               data-model="App\Models\CommissionSlab"></i>
                                                        </div>
                                                        <input type="file" class="form-control" id="thumbnail"
                                                               name="thumbnail">
                                                        <div class="invalid-feedback">
                                                            Please enter the Thumbnail.
                                                        </div>
                                                    </div>
                                                    <div class="col-4 mt-2">
                                                        <label for="thumbnail" class="form-label">Image</label>
                                                    </div>
                                                    <div class="col-8 mt-2">
                                                        <div class="mb-2  d-none" id="imagediv">
                                                            <img src="" alt="Image" id="image-img"
                                                                 class="img-thumbnail img-responsive" style="max-width:50px">
                                                            <i class="ri-delete-bin-5-fill text-danger ms-2 generalDelete"
                                                               data-id="" data-model="App\Models\Source" data-key=""
                                                               id="image_id"></i>
                                                        </div>
                                                        <input type="file" class="form-control" id="image"
                                                               name="image">
                                                        <div class="invalid-feedback">
                                                            Please enter the Status image.
                                                        </div>
                                                    </div>
                                                    <!--end col-->
                                                    <!--end col-->
                                                    <div class="col-4 mt-2">
                                                        <label class="form-check-label" for="statusCheckbox">Status</label>
                                                    </div>
                                                    <div class="col-8 mt-2">
                                                        <div class="form-check form-switch form-switch-lg form-switch-success"
                                                             dir="ltr">
                                                            <input type="checkbox" name="status" class="form-check-input"
                                                                   id="statusCheckbox" checked>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                </div>
                                <!--end col-->
                                <button type="submit" class="d-none" id="submitBtn"></button>

                                <!--end col-->
                            </form>

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

                    <table id="documentsLibTypeDt" class="table align-middle" style="width-100%">
                        <thead class="table-light">
                        <tr class="text-uppercase">
                            <th scope="col" style="width-50px;">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="checkAll" value="option"
                                           data-com.bitwarden.browser.user-edited="yes">
                                </div>
                            </th>
                            <th>ID</th>
                            <th>Thumbnail</th>
                            <th>Name</th>
                            <th>Updated</th>
                            <th>Created</th>
                            <th>Status</th>

                        </tr>
                        </thead>
                        <tbody class="form-check-all">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @include('admin.commission_slabs.includes.modals')
@endsection
@section('script')
    @include('admin.partials.datatable')

    {{-- <script src=" "></script> --}}


    <script>
        let isUpdate = false;
        let isUpdateLimit = false;

        $(document).ready(function () {
            $("#commission_category").select2({
                placeholder: 'Select Commission Category',
                dropdownParent: $('#mainForm'),
                allowClear: true,
            });
            $(".commonSelect2").select2({
                dropdownParent: $('#mainForm'),
                placeholder: function () {
                    $(this).data('placeholder');
                },
                allowClear: true,

            });
            $("#role_id").select2({
                placeholder: 'Select Role',
                allowClear: true,
                closeOnSelect: true,
                dropdownParent: $('#mainForm'),

            });
            $("#user_id").getAgentSelect2('#mainForm');
            //select2 on select event
            $("#role_id").on('select2:select', function (e) {
                $("#user_id").val('').trigger('change');
            });
            $("#user_id").on('select2:select', function (e) {
                $("#role_id").val('').trigger('change');
            });

            $('#statusBadge').select2({
                placeholder: "Select",
                minimumResultsForSearch: Infinity,
                templateSelection: optionFormat,
                templateResult: optionFormat
            });
        });
        let dataTable = $('#documentsLibTypeDt').DataTable({
            responsive: true,
            searchDelay: 500,
            processing: true,
            serverSide: true,
            dom: 'Bfrtip',
            buttons: [{
                extend: 'colvis',
                titleAttr: 'Show/Hide',
                text: '<i class="colvis" style="font-size:09px">Show / Hide</i>',
            }],

            order: [
                [3, "desc"]
            ],
            lengthMenu: [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],
            dom: `<'row'<'col-sm-6 text-left'B> <'col-sm-6 text-right'f>>
            <'row'<'col-sm-12'tr>>
            <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>`,
            ajax: {
                url: "{{ route('admin.documents.type.dt') }}",
                type: "post",
                error: function() {
                    $(".users-error").html("");
                    $("#dataTable").append(
                        '<tbody class="users-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>'
                    );
                    $("#dataTable_processing").css("display", "none");
                }
            },
            columns: [

                {
                    data: function(data) {
                        return `<div class="form-check"><input class="form-check-input selectable-checkbox" type="checkbox" name="chk_child" value="${data.id}"></div>`;
                    },
                    searchable: false,
                    sortable: false
                },
                {
                    data: 'id',
                    name: 'id',
                    searchable: false,
                    sortable: false
                },

                {
                    data: function(data) {
                        return '<img src="' + data.thumb_url + '" class="avatar-xs rounded-circle" alt="">';
                    },
                    name: 'thumbnail',
                    searchable: false,
                    sortable: false
                },

                {
                    data: 'name',
                    name: 'name',
                },


                {
                    sortable: true,
                    data: function(data) {
                        if (!data.updated_by)
                            return `<div class="flex-grow-1"><p class="mb-0"><small>${moment(data.updated_at).fromNow()}</small></p></div>`;
                        return `<div class="d-flex align-items-center userDetails" data-agent-id="${data.updated_by.id}"  title="${data.updated_by.name}">
                                    <div class="flex-shrink-0 me-2">
                                        <img src="${data.updated_by.thumb_url}" alt="" class="avatar-xs rounded-circle">
                                    </div>
                                    <div class="flex-grow-1">${data.updated_by.first_name}<p class="mb-0"><small>${moment(data.updated_at).fromNow()}</small></p></div>
                                </div>`;
                    },
                    name: 'updated_at',


                },
                {
                    sortable: true,
                    data: function(data) {
                        if (!data.created_by)
                            return `<div class="flex-grow-1"><p class="mb-0"><small>${moment(data.created_by).fromNow()}</small></p></div>`;
                        return `<div class="d-flex align-items-center userDetails" data-agent-id="${data.created_by.id}"  title="${data.created_by.name}">
                                    <div class="flex-shrink-0 me-2">
                                        <img src="${data.created_by.thumb_url}" alt="" class="avatar-xs rounded-circle">
                                    </div>
                                    <div class="flex-grow-1">${data.created_by.first_name}<p class="mb-0"><small>${moment(data.created_at).fromNow()}</small></p></div>
                                </div>`;
                    },
                    name: 'created_at',
                },
                {

                    orderable: false,
                    visible: true,
                    /*This will hide particular column*/
                    data: function(data) {
                        let disabled = true;
                        @can('Commission Slab Edit')
                            disabled = false;
                        @endcan
                        if (data.status == '1') {
                            return `<div  class="form-check form-switch form-switch-lg form-switch-success" dir="ltr">
                                        <input ${disabled ? "disabled" : ""} type="checkbox" data-id="${data.id}" data-do="0" name="status" class="form-check-input status" checked></div>`;
                        } else {
                            return `<div  class="form-check form-switch form-switch-lg form-switch-success" dir="ltr">
                                        <input ${disabled ? "disabled" : ""} data-id="${data.id}" data-do="1" type="checkbox" name="status" class="form-check-input status"></div>`;
                        }
                    },
                    name: 'status',
                },
            ],
        }); // end datatable init


        dataTable.on('draw.dt', function() {
            $(".chk_child").prop("checked", false);
            $('.multi_options').remove();
            $('#checkAll').prop("checked", false);
        });





        $('#documentsLibTypeDt tbody').on('change', 'input.status', function() {
            // change property category status based on button class and taking id from button data attribute
            @can('Sources Edit')
            let id = $(this).data('id');
            let action = $(this).data('do');
            let formData = {
                id: id,
                action: action
            }
            $.ajax({
                type: "POST",
                url: "{{ route('admin.documents.type.status') }}",
                data: formData,
                dataType: 'json',
                success: function(data) {
                    if (data.status == '1') {
                        showToastify("Status updated successfully", "success")
                        dataTable.row().draw();
                    }
                },
                error: function(data) {
                    console.log('Error:', data);
                }
            });
            @endcan
        });





        $(document).on('click', '#deleteBtn', function(event) {
            @can('Commission Slab Delete')
            deleteData($('#id').val());
            @endcan
        });


        $(document).on('click', '#editBtn', function(event) {
            @can('Sources Edit')
            $('#newBtn').addClass('d-none');
            $('#saveBtn').removeClass('d-none');
            $('#addSlabLimits').addClass('d-none');
            editData($('#id').val());
            @endcan
        });


        $(document).on('click', '.edit-link', function(
            event) { //edit link click event it will fetch single cateogry details and put it on form
            @can('Sources Edit')
            let id = $(this).data('id');
            $('#id').val(id);
            editData(id);
            @endcan
        });


        $(document).on('click', '.delete-link', function(event) {
            @can('Commission Slab Delete')
            let id = $(this).data('id');
            deleteData(id);
            @endcan
        });


        function editData(id) {
            @can('Sources Edit')
                isUpdate = true; // set isUpdate to true so that we can identify that this is update request
            $.ajax({
                url: "{{ route('admin.documents.type.get') }}",
                type: 'GET',
                dataType: 'json',
                data: {
                    id
                },
                success: function(result) {
                    if (result.status == '1') {
                        $('#editBtn').addClass('d-none');
                        $('#submitBtn').removeClass('d-none');
                        $('#mainForm').trigger('reset');
                        dataObj = result.data;
                        Object.keys(result.data).map((index) => {
                            if (index == 'image' || index == 'thumbnail') {
                                // $(`#${index}`).val(result.data[index]).trigger("change")
                            } else {
                                $(`#${index}`).val(result.data[index]).trigger("change")

                            }
                        })

                        $('.inputs').attr('disabled', false);

                        // if(result.data.image){
                        //     alert('result.data.image')
                        // }
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
            @can('Commission Slab Delete')
            new swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to use this document type!",
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            })
                .then((willDelete) => {
                    if (willDelete) { // on confirm delete the category
                        $.ajax({
                            url: "{{ route('admin.documents.type.delete') }}",
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                ids: [id]
                            },
                            success: function(result) {
                                if (result.status == '1') { //if successfully deleted
                                    showToastify("Type deleted successfully", "success");
                                    $("#closeBtn").trigger("click");
                                    dataTable.row()
                                        .draw(); // redraw the datatable show we don't need to reload the page to see the changes

                                } else { // something went wrong on backend
                                    showToastify(result.msg, "error")
                                }
                            },
                            error: function(result) {
                                console.log(result);
                            }
                        });
                    } else { //if delete confirmation is cancelled
                        swal("Contact source is safe!");
                    }
                });
            @endcan
        }

        $(document).on('submit', '#mainForm', function(event) {
            $("#submitBtn").prop('disabled', true);
            event.preventDefault();
            var formData = new FormData($(this)[0]);
            $.ajax({
                url: isUpdate ? "{{ route('admin.documents.type.update') }}" :
                    "{{ route('admin.documents.type.store') }}",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function(result) {
                    if (result.status == '1') {
                        showToastify(result.msg, "success")
                        $('.inputs').attr('disabled', true);
                        $("#submitBtn").prop('disabled', false);
                        //  viewData(result.data.id);
                        $("#closeBtn").trigger("click");
                        dataTable.row()
                            .draw(); // redraw the datatable show we don't need to reload the page to see the changes
                        isUpdate =
                            false; // set isUpdate to false so that when we try to add new category it will be create request
                    } else if (result.status == '2') {
                        $("#submitBtn").prop('disabled', false);
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
            if (ids_array = [], document.getElementsByName("chk_child").forEach((function(e) {
                if (1 == e.checked) {
                    var t = e.value;
                    ids_array.push(t)

                }
            })), "undefined" != typeof ids_array && ids_array.length > 0) {

               new swal({
                    title: "Attention!!",
                    text: "You are trying to delete multiple Slabs. Once deleted, you will not be able to use these Slabs!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                    .then((willDelete) => {
                        if (willDelete) {
                            $.ajax({
                                url: "{{ route('admin.documents.type.delete') }}",
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
                                        swal("Whoops", result.msg, "error");
                                        showToastify(result.msg, "error")
                                    }
                                },
                                error: function(result) {
                                    console.log(result);
                                }
                            });
                            document.getElementById("checkAll").checked = !1
                        } else {
                            showToastify("data is safe!", "error")
                        }
                    });

            } else {
                showToastify("Please select at least one checkbox", "error")
            }

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

        $(document).on("click", "#closeBtn", function() {
            toggleForm(false)
            mode = 'view';
            $('#dropdownMenuLink1').addClass('d-none');
            $('#addSlabLimits').addClass('d-none');


        })

        function toggleForm(show) {
            if (show == undefined) {
                $("#collapseContactForm").toggleClass("show")
                $('html, body').animate({
                    scrollTop: 0
                }, 'fast');
                return
            }
            if (show) {
                $("#collapseContactForm").addClass("show");
                $('html, body').animate({
                    scrollTop: 0
                }, 'fast');
            } else {
                $("#saveBtn, #closeBtn, #editBtn, #bulkAssignBtn").addClass("d-none");
                $("#newBtn").removeClass("d-none");
                $("#collapseContactForm").removeClass("show");
            }
        }

        $(document).on("click", "#saveBtn", function() {
            $("#submitBtn").trigger("click")
        })

        function viewData(id) {
            @can('Commission Slab View')
            $.ajax({
                url: "{{ route('admin.documents.type.get') }}",
                type: 'GET',
                dataType: 'json',
                data: {
                    id
                },
                success: function(result) {

                    if (result.status == '1') {
                        $('#editBtn').removeClass('d-none');
                        $('#saveBtn').addClass('d-none');
                        $('#closeBtn').removeClass('d-none');
                        $('#deleteBtn').removeClass('d-none');
                        $('#mainForm').trigger('reset');
                        if(result.data.commission_category == 'Direct'){
                            $('#addSlabLimits').removeClass('d-none');
                        }else{
                            $('#addSlabLimits').addClass('d-none');
                        }
                        toggleForm(true)
                        dataObj = result.data;
                        Object.keys(result.data).map((index) => {
                            if (index == 'image' || index == 'thumbnail') {} else {
                                $(`#${index}`).val(result.data[index]).trigger("change")
                            }
                        })


                        if (result.data.status) {
                            document.getElementById("statusCheckbox").checked = true;
                        } else {
                            document.getElementById("statusCheckbox").checked = false;
                        }

                        $('.inputs').attr('disabled', true);
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



        $('#newBtn').click(function() {
            isUpdate = false;
            mode = 'create';
            $('#newBtn').addClass('d-none');

            $(".inputs").prop("disabled", false)
            $(".inputs").val("").trigger("change")
            $(".inputs").removeClass("is-invalid")
            toggleForm(true)
            $('#saveBtn').removeClass('d-none');
            $('#editBtn').addClass('d-none');
            $('#closeBtn').removeClass('d-none');
            $('#addSlabLimits').addClass('d-none');
            $('#appendLimits').html('')

        });

        $(document).ready(function() {
            $('.slabEdit').click(function() {
                let id = $(this).data('id');
                alert(id)
            })
        })

        function getSlabLimitss(id) {
            $.ajax({
                url: "{{ route('admin.work.commission.slab.getSlabLimits') }}",
                type: 'GET',
                dataType: 'html',
                data: {
                    id
                },
                success: function(result) {
                    if (result) {
                        $('#appendLimits').html(result)
                    } else {
                        $('#appendLimits').html('')
                    }

                }
            });
        }

        function drawSlabLimits(result) {

            let row = '';  let counter = 0;
            if (result.status == '1') {
                if (result.data.commission_limits.length > 0) {
                    $.each(result.data.commission_limits, function (key, item) {
                        row += ` <tr>
                                            <td>${item.id}</td>
                                                                        <td>${item.from_value+" - "+ item.to_value}</td>
                                                                        <td>${item.commission_type}</td>
                                                                        <td>${item.commission_value}</td>
                                                                        <td>
                                                                            <button type="button" class="slabEdit btn"
                                                                                    data-id="${item.id}" data-lower="${item.from_value}"
                                                                                    data-higher="${item.to_value}" data-slab="${item.commission_slab_id}"
                                                                                    data-commission_type="${item.commission_type}"
                                                                                    data-commission_value="${item.commission_value}"><span
                                                                                    class="badge badge-soft-success"
                                                                                    data-id="" data-lower=""
                                                                                    data-higher="" data-slab="">Edit<i
                                                                                        class="ri-arrow-right-up-line align-bottom"></i></span>
                                                                            </button>
                                                                        <button type="button" class=" btn"
                                                                                    data-id="${item.id}" data-lower="${item.from_value}"
                                                                                    data-higher="${item.to_value}" data-slab="${item.commission_slab_id}"
                                                                                    data-commission_type="${item.commission_type}"
                                                                                    data-commission_value="${item.commission_value}"><span
                                                                                    class="badge badge-soft-danger"
                                                                                    data-id="" data-lower=""
                                                                                    data-higher="" data-slab="">Delete</span>
                                                                            </button>
                                                                        </td>
                                                                    </tr>`;
                    })
                }
                $("#slabLimitBody").html(row)
            }



            // if (result.status == '31') {
            //     if (result.data.commission_limits.length > 0) {
            //         var $table = $('<table class="table table-nowrap">').attr('id', 'myTable');
            //         var $thead = $('<thead>').appendTo($table);
            //         var $headerRow = $('<tr>').appendTo($thead);
            //         $('<th>').text('ID').appendTo($headerRow);
            //         $('<th>').text('Range').appendTo($headerRow);
            //         $('<th>').text('Type').appendTo($headerRow);
            //
            //         $('<th>').text('Value').appendTo($headerRow);
            //         $('<th>').text('Actions').appendTo($headerRow);
            //         var $tbody = $('<tbody>').appendTo($table);
            //         let counter = 0;
            //         $.each(result.data.commission_limits, function(key, item) {
            //
            //
            //             var $row = $('<tr>').appendTo($tbody);
            //             $('<td>').text(item.id).appendTo($row);
            //             $('<td>').text(item.from_value+" - "+ item.to_value).appendTo($row);
            //             $('<td>').text(item.commission_type).appendTo($row);
            //             $('<td>').text(item.commission_value).appendTo($row);
            //             var $linkCell = $('<td>').appendTo($row);
            //
            //             var $icon = $(
            //                 '<i class="ri-arrow-right-up-line align-bottom"></i>');
            //             var $badge = $(
            //                 '<span class="badge badge-soft-success" data-id="" data-lower="" data-higher="" data-slab="">Edit</span>'
            //             ).append($icon);
            //
            //             $('<button type="button" class="slabEdit btn">')
            //                 .html($badge)
            //                 .attr('data-id', item.id)
            //                 .attr('data-lower', item.from_value)
            //                 .attr('data-higher', item.to_value)
            //                 .attr('data-slab', item.commission_slab_id)
            //                 .attr('data-commission_type', item.commission_type)
            //                 .attr('data-commission_value', item.commission_value)
            //                 .appendTo($linkCell)
            //                 .click(function() {
            //                     var id = $(this).data('id');
            //                     var from_value = $(this).data('lower');
            //                     var to_value = $(this).data('higher');
            //                     var commission_slab_id = $(this).data('slab');
            //                     $('#slab_id').val(commission_slab_id);
            //                     $('#from_value').val(from_value);
            //                     $('#to_value').val(to_value);
            //                     $('#commission_limit_id').val(id);
            //                     $('#limit_commission_value').val($(this).data('commission_value'));
            //                     $('#limit_commission_type').val($(this).data('commission_type'));
            //                     $('#addSlabLimits').removeClass('d-none');
            //                     isUpdateLimit = true;
            //                     $("#slabLimit").modal('show');
            //                 });
            //
            //             counter++;
            //         });
            //
            //         $('#tableContainer').html($table);
            //         $('#tableContainerDiv').removeClass('d-none');
            //         if (counter > 1) {
            //             $('#tableContainerDiv').removeClass('d-none');
            //
            //         } else {
            //             $('#tableContainerDiv').addClass('d-none');
            //
            //         }
            //     } else {
            //         $('#tableContainerDiv').addClass('d-none');
            //     }
            // } else {
            //     showToastify(result.msg, "error")
            // }
        }

        function getSlabLimits(id) {

            $.ajax({
                url: "{{ route('admin.work.commission.slab.getSlabLimits') }}",
                type: 'GET',
                dataType: 'json',
                data: {
                    id
                },
                success: function(result) {
                    // drawSlabLimits(result)
                    if (result.status == '1') {

                        drawSlabLimits(result)
                        return '';

                        if (result.data.commission_limits != null) {

                            var $table = $('<table class="table table-nowrap">').attr('id', 'myTable');
                            var $thead = $('<thead>').appendTo($table);
                            var $headerRow = $('<tr>').appendTo($thead);
                            $('<th>').text('ID').appendTo($headerRow);
                            $('<th>').text('Range').appendTo($headerRow);
                            $('<th>').text('Type').appendTo($headerRow);

                            $('<th>').text('Value').appendTo($headerRow);
                            $('<th>').text('Actions').appendTo($headerRow);
                            var $tbody = $('<tbody>').appendTo($table);
                            let counter = 0;
                            $.each(result.data.commission_limits, function(key, item) {


                                var $row = $('<tr>').appendTo($tbody);
                                $('<td>').text(item.id).appendTo($row);
                                $('<td>').text(item.from_value+" - "+ item.to_value).appendTo($row);
                                $('<td>').text(item.commission_type).appendTo($row);
                                $('<td>').text(item.commission_value).appendTo($row);
                                var $linkCell = $('<td>').appendTo($row);

                                var $icon = $(
                                    '<i class="ri-arrow-right-up-line align-bottom"></i>');
                                var $badge = $(
                                    '<span class="badge badge-soft-success" data-id="" data-lower="" data-higher="" data-slab="">Edit</span>'
                                ).append($icon);

                                $('<button type="button" class="slabEdit btn">')
                                    .html($badge)
                                    .attr('data-id', item.id)
                                    .attr('data-lower', item.from_value)
                                    .attr('data-higher', item.to_value)
                                    .attr('data-slab', item.commission_slab_id)
                                    .attr('data-commission_type', item.commission_type)
                                    .attr('data-commission_value', item.commission_value)
                                    .appendTo($linkCell)
                                    .click(function() {
                                        var id = $(this).data('id');
                                        var from_value = $(this).data('lower');
                                        var to_value = $(this).data('higher');
                                        var commission_slab_id = $(this).data('slab');
                                        $('#slab_id').val(commission_slab_id);
                                        $('#from_value').val(from_value);
                                        $('#to_value').val(to_value);
                                        $('#commission_limit_id').val(id);
                                        $('#limit_commission_value').val($(this).data('commission_value'));
                                        $('#limit_commission_type').val($(this).data('commission_type'));
                                        $('#addSlabLimits').removeClass('d-none');
                                        isUpdateLimit = true;
                                        $("#slabLimit").modal('show');
                                    });
                                //add delete button


                                counter++;
                            });

                            $('#tableContainer').html($table);
                            $('#tableContainerDiv').removeClass('d-none');
                            if (counter > 0) {
                                $('#tableContainerDiv').removeClass('d-none');

                            } else {
                                $('#tableContainerDiv').addClass('d-none');

                            }
                        } else {
                            $('#tableContainerDiv').addClass('d-none');
                        }
                    } else {
                        showToastify(result.msg, "error")
                    }
                },
                error: function(result) {
                    console.log(result);
                }
            });

        }


        $('.slabLimitEdit').click(function() {
            alert('dd')
            let id = $(this).data('id');
            alert(id)
        })




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
                if ($cell.index() === 6 || $cell.index() === 7 || $cell.index() === 8) {
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

                    @can('Sources Delete')
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



        $('#addSlabLimits').click(function() {
            let id = $('#id').val();
            // alert(id)
            isUpdateLimit = false;
            $('#slabItemForm').trigger('reset');
            $('#slab_id').val(id);
            $('#slabLimit').modal('show');
        })



        $(document).on('submit', '#slabItemForm', function(event) {
            $("#submitForm").prop('disabled', true);
            event.preventDefault();
            var formData = new FormData($(this)[0]);
            // isUpdateLimit
            $.ajax({
                url: isUpdateLimit ? "{{ route('admin.work.commission.slab.slab.item.update') }}" :
                    "{{ route('admin.work.commission.slab.slab.item.create') }}",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function(result) {
                    if (result.status == '1') {
                        showToastify(result.msg, "success")
                        $('.inputs').attr('disabled', true);
                        $("#submitForm").prop('disabled', false);
                        $('#slabLimit').modal('hide');
                        getSlabLimits(formData.get('commission_slab_id'))
                        // drawSlabLimits(result)
                        dataTable.row().draw();
                    } else if (result.status == '2') {
                        $("#submitForm").prop('disabled', false);
                        $.each(result.errors, function(index, item) {
                            showToastify(item[0], "error")
                        })

                    } else {
                        $("#submitForm").prop('disabled', false);
                        showToastify(result.msg, "error")
                    }
                },
                error: function(jqXhr, json, errorThrown) {
                    let data = jqXhr.responseJSON;
                    $("#submitForm").prop('disabled', false);
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
        $("#commission_dropdown").addClass('collapse show');
        $("#commissionActive").addClass('active');
        $("#commissionSlabSidebar").addClass('active');

        //
    </script>
@endsection
