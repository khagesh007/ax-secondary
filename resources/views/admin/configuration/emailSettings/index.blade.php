@extends('admin.layouts.master')
@section('title')
    Email Settings
@endsection
@section('style')
    <link href="{{ URL::asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css"/>
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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="{{ asset("assets/libs/tagify/tagify.css") }}" rel="stylesheet"
          type="text/css"/>
@endsection
@section('content')
    <form class="row needs-validation" id="mainForm" novalidate>
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title mb-0 ">Email Settings</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Configuration</a></li>
                            <li class="breadcrumb-item active">Email Settings</li>
                        </ol>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="form-check form-switch form-switch-right form-switch-md">
                            @can("Email Settings Create")
                                <button type="button" id="newBtn" class="btn btn-danger add-btn action-add">
                                    <i class="ri-add-line align-bottom me-1"></i> Add New
                                </button>
                            @endcan
                            <button type="submit" class="btn btn-success d-none action-save" id="saveBtn">Save</button>
                            @can("Email Settings Edit")
                                <button type="button" class="btn btn-primary d-none action-edit" id="editBtn">Edit</button>
                            @endcan
                            @can("Email Settings Delete")
                                <button type="button" class="btn btn-danger d-none action-delete" id="deleteBtn">Delete</button>
                            @endcan
                            <button type="button" class="btn btn-light d-none action-close" id="closeBtn">Close</button>
                            {{-- @can("Email Settings Delete")
                                <button type="button" class="btn btn-soft-danger action-delete" id="multiDeleteBtn"
                                        onClick="deleteMultiple()"><i
                                        class="ri-delete-bin-2-line"></i></button>
                            @endcan --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="collapse slideIn" id="collapseContactForm">
                <div class="card">
                    <div class="card-body">
                        <div>
                            <div class="row">

                                <div class="col-2">Title*</div>
                                <div class="col-4">
                                    <input type="hidden" id="id" name="id">
                                    <select name='title' id="title"
                                            class="form-control select2 inputs" required>
                                        <option value="" disabled selected>Select</option>
                                        @foreach (\App\Models\EmailSetting::TITLES ?? [] as $title)
                                            <option
                                                value="{{ $title }}">{{ $title }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">
                                        Please choose title.
                                    </div>
                                </div>

                                <div class="col-2 mt-2">CC</div>
                                <div class="col-4 mt-2">
                                    <input type="text" class="form-control inputs" name="cc"
                                           id="cc"
                                           placeholder="Enter cc">
                                    <div class="invalid-feedback">
                                        Please enter cc.
                                    </div>
                                </div>

                                <div class="col-2 mt-2">BCC</div>
                                <div class="col-4 mt-2">
                                    <input type="text" class="form-control inputs" name="bcc"
                                           id="bcc"
                                           placeholder="Enter bcc">
                                    <div class="invalid-feedback">
                                        Please enter bcc.
                                    </div>
                                </div>

                                <div class="col-2 mt-2">Reply To</div>
                                <div class="col-4 mt-2">
                                    <input type="text" class="form-control inputs" name="reply_to"
                                           id="reply_to"
                                           placeholder="Enter reply to">
                                    <div class="invalid-feedback">
                                        Please enter reply to.
                                    </div>
                                </div>
                                <div class="col-2 mt-2">
                                    <label for="roles" class="form-label">Role</label>
                                </div>
                                <div class="col-4 mt-2">
                                    <select name='roles[]' id="roles" class="form-control select2 inputs"
                                            multiple>
                                        @foreach($roles ?? [] as $role)
                                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">
                                        Please choose role.
                                    </div>
                                </div>

                                <div class="col-2 mt-2">Status</div>
                                <div class="col-4 mt-2">
                                    <div class="form-check form-switch form-switch-lg form-switch-success"
                                         dir="ltr">
                                        <input type="checkbox" name="status" class="form-check-input"
                                               id="statusCheckbox"
                                               checked>
                                        <label class="form-check-label" for="statusCheckbox"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="row">
        <div class="col-xxl-12">
            <div class="card" id="contactList">
                <div class="card-body">
                    <table id="contacts-table" class="table align-middle" style="width:100%">
                        <thead class="">
                            <tr class="table-light2 text-uppercase">
                            <th scope="col" style="width: 50px;">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="checkAll" value="option"
                                           data-com.bitwarden.browser.user-edited="yes">
                                </div>
                            </th>
                            <th>ID</th>
                            <th>Title</th>
                            <th>CC</th>
                            <th>BCC</th>
                            <th>Reply To</th>
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

    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="{{ asset("assets/libs/tagify/tagify.min.js") }}"></script>
        {{-- <script src=" "></script> --}}


    <script>
        var isUpdate = false;

        var ccInput = document.querySelector("#cc");
        var bccInput = document.querySelector("#bcc");

        var emailPattenRegex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/

        let ccTagify = null;
        let bccTagify = null;


        const initTagify = () => {
            if (ccTagify != null) {
                ccTagify.destroy()
                bccTagify.destroy()
            }

            ccTagify = new Tagify(ccInput, {
                pattern: emailPattenRegex,
            });
            bccTagify = new Tagify(bccInput, {
                pattern: emailPattenRegex,
            });
        }

        initTagify()

        function toggleForm(show) {
            if (show == undefined) {
                $("#collapseContactForm").toggleClass("show")
                return
            }
            if (show) {
                $("#collapseContactForm").addClass("show");
            } else {
                $("#saveBtn, #closeBtn, #editBtn, #deleteBtn").addClass("d-none");
                $("#newBtn, #multiDeleteBtn").removeClass("d-none");
                $("#collapseContactForm").removeClass("show");
            }
        }

        $("#checkAll").change(function () {
            $(".selectable-checkbox").prop("checked", this.checked)
        })

        $("#newBtn").click(function () {
            @can("Email Settings Create")
            isUpdate = false;
            $(".inputs").prop("disabled", false);
            $("select.inputs").val("").trigger("change");
            $("#mainForm").trigger("reset")
            $("#id").val("")
            $("#saveBtn, #closeBtn").removeClass("d-none");
            $("#newBtn, #editBtn, #deleteBtn, #multiDeleteBtn").addClass("d-none");
            toggleForm(true)
            @endcan
        });

        $(document).on("click", "#editBtn", function () {
            @can("Email Settings Edit")
            $(".inputs").prop("disabled", false)
            editData($('#id').val());
            $("#saveBtn, #closeBtn").removeClass("d-none");
            $("#newBtn, #editBtn, #deleteBtn, #multiDeleteBtn").addClass("d-none");
            @endcan
        });

        $(document).on('click', '.edit-link', function (event) {
            @can("Email Settings Edit")
            $(".inputs").prop("disabled", false)
            let id = $(this).data('id');
            editData(id);
            @endcan
        });

        $(document).on("click", "#closeBtn", function () {
            toggleForm(false)
        })

        $(document).on('click', '#deleteBtn', function (event) {
            @can("Email Settings Delete")
            deleteData($('#id').val());
            @endcan
        });

        function editData(id) {
            @can("Email Settings Edit")
                isUpdate = true;
            toggleForm(true);
            resetForm()
            getSetting(id)
            $(".inputs").prop("disabled", false)
            $("#id").val(id)
            $("#saveBtn, #closeBtn").removeClass("d-none");
            $("#newBtn, #editBtn, #deleteBtn, #multiDeleteBtn").addClass("d-none");
            scrollTop()
            @endcan
        }

        function getSetting(id) {
            $.ajax({
                url: "{{ route("admin.configuration.email-settings.get") }}",
                type: "get",
                data: {id: id},
                dataType: "json",
                success: function (result) {
                    if (result.status == '1') {
                        Object.keys(result.data).map((index) => {
                            $(`#${index}`).val(result.data[index]).trigger("change")
                        })
                        $("#statusCheckbox").prop("checked", result.data.status)
                        let roles = [];
                        result.data.roles?.map(item => {
                            roles.push(item.id);
                        });
                        $("#roles").val(roles).trigger("change");
                        toggleForm(true)
                        scrollTop();
                    }
                }
            });
        }

        $(document).on('submit', '#mainForm', function (event) {
            $("#saveBtn").prop('disabled', true);
            event.preventDefault();
            var formData = new FormData($(this)[0]);
            let bcc = $("#bcc").val().match(/([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z0-9_-]+)/gi);
            let cc = $("#cc").val().match(/([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z0-9_-]+)/gi);
            formData.append("bcc", bcc ? bcc : "")
            formData.append("cc", cc ? cc : "")
            $.ajax({
                url: isUpdate ? "{{route('admin.configuration.email-settings.update')}}" : "{{route('admin.configuration.email-settings.store')}}",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function (result) {
                    $("#saveBtn").prop('disabled', false); //enable submit button
                    if (result.status == '1') {
                        resetForm()
                        showToastify(result.msg, "success")
                        toggleForm(false)
                        dataTable.row().draw(); // redraw the datatable show we don't need to reload the page to see the changes
                        isUpdate = false; // set isUpdate to false so that when we try to add new category it will be create request
                    } else if (result.status == '2') {
                        $("#saveBtn").prop('disabled', false);
                        let error_message = '';
                        $.each(result.errors, function (key, value) {
                            error_message += '' + key + ': ' + value + ' | ';
                        });
                        showToastify(error_message, "error")
                    } else {
                        $("#saveBtn").prop('disabled', false);
                        swal("Oops!", result.msg, "error");
                    }
                },
                error: function (jqXhr, json, errorThrown) {
                    $("#saveBtn").prop('disabled', false);
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

        let dataTable = $('#contacts-table').DataTable({ //datatable initialization
            "order": [
                [0, "DESC"]
            ],
            language: {
                searchPlaceholder: "Search email settings"
            },

            dom: 'Bfrtip',
            buttons: [{
                extend: 'colvis',
                titleAttr: 'Show/Hide',
                text: '<i class="colvis" style="font-size:09px">Show / Hide</i>',

            }],
            createdRow: function(row, data, index) {
                $('#contacts-table tbody').css('cursor', 'pointer');

            },
            responsive: true,
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "{{route('admin.configuration.email-settings.dt')}}",
                type: "post",
                error: function () {
                    $(".users-error").html("");
                    $("#contacts-table").append('<tbody class="users-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
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
                    data: 'title',
                    name: 'title',
                },
                {
                    data: "cc",
                    name: "cc",
                },
                {
                    data: "bcc",
                    name: "bcc",
                },
                {
                    data: "reply_to",
                    name: "reply_to",
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
                {
                    "targets": 0,
                    orderable: true,
                    "data": "id",
                    render: function (type, row, data, meta) {
                        return `<div class="form-check"><input class="form-check-input selectable-checkbox" type="checkbox" name="chk_child" value="${data.id}"></div>`;
                    }
                },
                {
                    targets: 2,
                    orderable: false,
                    visible: true,
                    render: function (type, row, data, meta) {
                        return `<a href="javascript:void(0)" class="text-primary" onclick="viewData('` + data.id + `')">` + data.title + `</a>`;
                    }
                },
                {
                    targets: 6,
                    orderable: false,
                    visible: true,
                    render: function (type, row, data, meta) {
                        let disabled = true;
                        @can("Email Settings Edit")
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

        function resetForm() {
            $("#mainForm").removeClass("was-validated");
            $("#mainForm").trigger("reset");
            $(".inputs").val("").trigger("change");
            initTagify()
        }

        function viewData(id) {
            @can("Email Settings View")
            resetForm()
            getSetting(id)
            $(".inputs").prop("disabled", true)
            $("#id").val(id)
            $("#saveBtn, #newBtn, #multiDeleteBtn").addClass("d-none");
            $("#editBtn, #closeBtn, #deleteBtn").removeClass("d-none");
            toggleForm(true)
            scrollTop()
            @endcan
        }

        $('#contacts-table tbody').on('change', 'input.status-change', function () {
            // change property category status based on button class and taking id from button data attribute
            @can("Email Settings Edit")
            let id = $(this).data('id');
            let action = $(this).data('do');
            let formData = {
                id: id,
                action: action
            }
            $.ajax({
                type: "POST",
                url: "{{route('admin.configuration.email-settings.status')}}",
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


        function deleteData(id) {
            @can("Email Settings Delete")
            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to use this email setting details!",
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            })
                .then((willDelete) => {
                    if (willDelete) { // on confirm delete the category
                        $.ajax({
                            url: "{{route('admin.configuration.email-settings.delete')}}",
                            type: 'POST',
                            dataType: 'json',
                            data: {id},
                            success: function (result) {
                                if (result.status == '1') { //if successfully deleted
                                    showToastify(result.msg, "success")
                                    dataTable.row().draw(); // redraw the datatable show we don't need to reload the page to see the changes
                                    toggleForm(false)
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

        function deleteMultiple() {
            @can("Email Settings Delete")
            let ids = [];
            $(".selectable-checkbox").map((index, item) => {
                item.checked ? ids.push(item.value) : []
            })
            if (ids.length > 0) {
                swal({
                    title: "Attention!!",
                    text: "You are trying to delete multiple settings. Once deleted, you will not be able to use these settings!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                    .then((willDelete) => {
                        if (willDelete) {
                            $.ajax({
                                url: "{{route('admin.configuration.email-settings.multi.delete')}}",
                                type: 'POST',
                                dataType: 'json',
                                data: {ids},
                                success: function (result) {
                                    if (result.status == '1') {
                                        swal(result.msg, {
                                            icon: "success",
                                        });
                                        $('.multi_options').remove();

                                        dataTable.row().draw();
                                        
                                        toggleForm(false)
                                    } else {
                                        swal("Whoops", result.msg, "error");
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
                      
                    @can('Email Settings Delete')
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
                if ($cell.index() === 6 || $cell.index() === 7) {
                    // console.log($cell);
                } else {
                    viewData(data.id);
                    highlightRow($(this));

                }
            } // end if
        }); // table row click

        dataTable.on('draw.dt', function() {
            $(".chk_child").prop("checked", false);
            $('.multi_options').remove();
            $('#checkAll').prop("checked", false);
        });
         $("#sidebarConfiguration").addClass('collapse show');
        $("#sidebarConfigEmail").addClass('active');
        $("#configActive").addClass('active');
    </script>
@endsection
