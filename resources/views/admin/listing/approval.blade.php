@extends('admin.layouts.master')
@section('title')
    Property changes pending approval | Communities
@endsection
@section('style')
    <!--datatable css-->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css"/>
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/themes/classic.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"
          type="text/css"/>
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title mb-0 ">Property Approval</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Listing</a></li>
                            <li class="breadcrumb-item active">Pending changes</li>
                        </ol>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="form-check form-switch form-switch-right form-switch-md">
                                @can("Communities Create")
                                    <button type="button" id="approve-button" class="btn btn-success add-btn">
                                        <i class="ri-add-line align-bottom me-1"></i> Approve
                                    </button>
                                @endcan
                                <button type="submit" class="btn btn-success d-none" id="saveBtn">Save</button>

                                <button type="button" class="btn btn-light d-none" id="closeBtn">Close</button>

                                    <button type="button" id="deleteBtn" class="btn btn-danger d-none">Reject</button>

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
                        <div class="row">
                           <div class="col-lg-6" >
                            <div class="row" id="oldData">

                            </div>
                           </div>
                           <div class="col-lg-6" >
                            <div class="row" id="newData">

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
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <table id="datatable" class="table align-middle" style="width:100%">
                        <thead>
                        <tr>
                            <th scope="col" style="width: 50px;">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="checkAll" value="option"
                                           data-com.bitwarden.browser.user-edited="yes">
                                </div>
                            </th>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Region</th>
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

        {{-- <script src=" "></script> --}}


    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="{{ URL::asset('assets/js/pages/select2.init.js') }}"></script>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{env("GOOGLE_MAP_KEY")}}&libraries=places&callback=Function.prototype"></script>
    <script>
        let isUpdate = false;





        $(document).on("click", "#closeBtn", function () {
            toggleForm(false)
        })



        function toggleForm(show) {
            if (show == undefined) {
                $("#form-section").toggleClass("d-none")
                return
            }
            if (show) {
                $("#form-section").removeClass("d-none");
            } else {
                $("#saveBtn, #closeBtn, #editBtn").addClass("d-none");
                $("#newBtn").removeClass("d-none");
                $("#form-section").addClass("d-none");
            }
        }

        $(document).on("click", "#approve-button", function (){
            $("#submitBtn").trigger("click")
        })

        $("#checkAll").change(function () {
            $(".selectable-checkbox").prop("checked", this.checked)
        })

        function viewData(id) {
            @can("Communities View")
                isUpdate = true;
            $(".inputs").prop("disabled", true)

            $("#mainForm").trigger("reset");
            $("#id").val(id);
            $('#mainForm').removeClass('was-validated');
            $("#saveBtn").addClass("d-none")
            $("#editBtn, #deleteBtn, #newBtn, #closeBtn").removeClass("d-none")
            $.ajax({
                url: "{{route('admin.property.approval.get')}}",
                type: 'GET',
                dataType: 'json',
                data: {id},
                success: function (result) {
                    if (result.status == '1') {
                        $('#id').val(result.data.id);
                        if (result.data) {
                            console.log(result.data.new_data);
                            let html = '';
                            let htmlOld = '';
                            //let newData = JSON.parse(result.data.new_data);
                            for (const [key, value] of Object.entries(result.data.modified_data)) {
                                html+=`<div class="col-lg-4 mt-2">
                                <label for="name" class="form-label">${key}</label>
                            </div>
                            <div class="col-lg-8 mt-2">
                                ${value}
                            </div>`;
                            }
                            // Object.keys(result.data.modified_data).map((index, value) => {
                            //    console.log(index +' - '+value)
                            //    })
                            $("#newData").html(html)
                            Object.keys(result.data.old_data).map((index, value) => {
                               console.log(index +' - '+value)
                               htmlOld+=`<div class="col-lg-4 mt-2">
                                <label for="name" class="form-label">${index}</label>
                            </div>
                            <div class="col-lg-8 mt-2">
                                ${value}
                            </div>`;})
                            $("#oldData").html(htmlOld)
                        }
                        toggleForm(true);
                        scrollTop();
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

        $(document).on('click', '#editBtn', function (event) {
            @can("Communities Edit")
            editData($('#id').val());
            @endcan
        });

        $(document).on("keyup", "#name", function () {
            $("#path_name").val($(this).val() + ", " + $("option:selected", "#region_id").text());
        })

        $(document).on("change", "#region_id", function () {
            $("#path_name").val($("#name").val() + ", " + $("option:selected", this).text());
        })

        function editData(id) {
            @can("Communities Edit")
                isUpdate = true;
            $("#mainForm").trigger("reset");
            $(".inputs").prop("disabled", false)
            $("#id").val(id);
            $("#region_id").val("").trigger("change")
            $('#mainForm').removeClass('was-validated');
            $("#saveBtn, #closeBtn").removeClass("d-none")
            $("#editBtn, #deleteBtn").addClass("d-none")
            $.ajax({
                url: "{{route('admin.location.communities.get')}}",
                type: 'GET',
                dataType: 'json',
                data: {id},
                success: function (result) {
                    if (result.status == '1') {
                        if (result.data) {
                            Object.keys(result.data).map((index) => {
                                $(`#${index}`).val(result.data[index]).trigger("change")
                            })
                            $("#statusCheckbox").prop("checked", result.data.status)
                        }
                        toggleForm(true);
                        scrollTop()
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


        let dataTable = $('#datatable').DataTable({
            "order": [[0, "DESC"]],
            language: {
                searchPlaceholder: "Search communities"
            },
            responsive: true,
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "{{route('admin.property.approval.dt')}}",
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
                    "data": "title",
                    render: function (type, row, data, meta) {
                        return `<a href="javascript:void(0)" class="text-${data.color_code ? data.color_code : 'primary'}" onclick="viewData('` + data.id + `')">[` + data.approvalable?.ref_no +`] `+ data.approvalable?.title + `</a>`;
                    }
                },
                {
                    "targets": 3,
                    orderable: true,
                    render: function (type, row, data, meta) {
                        return `${data.region ? data.region.name : "-"}`;
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
                        return data.state;
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
                               </div>`;
                    }
                }
            ]
        });

        $('#datatable tbody').on('change', 'input.status-change', function () {
            // change property category status based on button class and taking id from button data attribute
            let id = $(this).data('id');
            let action = $(this).data('do');
            let formData = {
                id: id,
                action: action
            }
            $.ajax({
                type: "POST",
                url: "{{route('admin.location.communities.status')}}",
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
        });


        function deleteMultiple() {
            @can("Communities Delete")
            $("#form-section").addClass("d-none");
            let ids = [];
            $(".selectable-checkbox").map((index, item) => {
                item.checked ? ids.push(item.value) : []
            })
            if (ids.length > 0) {
                swal({
                    title: "Attention!!",
                    text: "You are trying to delete multiple communities. Once deleted, you will not be able to use these community!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                    .then((willDelete) => {
                        if (willDelete) {
                            $.ajax({
                                url: "{{route('admin.location.communities.multi.delete')}}",
                                type: 'POST',
                                dataType: 'json',
                                data: {ids},
                                success: function (result) {
                                    if (result.status == '1') {
                                        showToastify(result.msg, "success")
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

        $(document).on('submit', '#mainForm', function (event) {
            $("#submitBtn").prop('disabled', true);
            event.preventDefault();
            var formData = new FormData($(this)[0]);
            $.ajax({
                url: isUpdate ? "{{route('admin.location.communities.update')}}" : "{{route('admin.location.communities.store')}}",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function (result) {
                    if (result.status == '1') {
                        showToastify(result.msg, "success")
                        $("#submitBtn").prop('disabled', false); //enable submit button
                        $('#mainForm').trigger('reset'); //
                        toggleForm(false)
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


        $(document).on('click', '#deleteBtn', function (event) {
            @can("Communities Delete")
            deleteData($('#id').val());
            @endcan
        });

        $(document).on('click', '.delete-link', function (event) {
            @can("Communities Delete")
            let id = $(this).data('id');
            deleteData(id);
            @endcan
        });

        function deleteData(id) {
            @can("Communities Delete")
            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to use this community!",
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            })
                .then((willDelete) => {
                    if (willDelete) { // on confirm delete the category
                        $.ajax({
                            url: "{{route('admin.location.communities.delete')}}",
                            type: 'POST',
                            dataType: 'json',
                            data: {id},
                            success: function (result) {
                                if (result.status == '1') { //if successfully deleted
                                    showToastify("Community has been deleted successfully", "success")
                                    dataTable.row().draw(); // redraw the datatable show we don't need to reload the page to see the changes
                                    $('#form-section').addClass('d-none');
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
          $("#sidebarLocations").addClass('collapse show');
        $("#sidebarCommunities").addClass('active');
    </script>

@endsection
