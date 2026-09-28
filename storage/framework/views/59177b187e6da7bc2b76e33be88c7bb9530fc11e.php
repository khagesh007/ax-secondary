<?php $__env->startSection('title'); ?>
    User Management - Roles
<?php $__env->stopSection(); ?>

<?php $__env->startSection('style'); ?>
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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"
          type="text/css"/>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <form class="row" id="mainForm">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title mb-0 ">Role List</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">User Management</a></li>
                            <li class="breadcrumb-item active">Role List</li>
                        </ol>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="form-check form-switch form-switch-right form-switch-md">
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check("Roles List")): ?>
                                <button type="button" id="newBtn" class="btn btn-danger add-btn action-add">
                                    <i class="ri-add-line align-bottom me-1"></i> Add New
                                </button>
                            <?php endif; ?>
                            <button type="submit" class="btn btn-success d-none action-save" id="saveBtn">Save</button>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check("Roles Edit")): ?>
                                <button type="button" class="btn btn-primary d-none action-edit" id="editBtn">Edit</button>
                            <?php endif; ?>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check("Roles Delete")): ?>
                                <button type="button" class="btn btn-danger d-none action-delete" id="deleteBtn">Delete</button>
                            <?php endif; ?>
                            <button type="button" class="btn btn-light d-none action-close" id="closeBtn">Close</button>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="collapse slideIn" id="formSection">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div>
                                <input type="hidden" id="id" name="id">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-4 mt-2">
                                                <label for="name" class="form-label">Role Name*</label>
                                            </div>
                                            <div class="col-8 mt-2">
                                                <input type="text" class="form-control inputs" name="name" id="name"
                                                       placeholder="Enter role name" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 mt-2">
                                        <div class="d-flex justify-content-start align-items-center gap-2">
                                            <h5>Permissions</h5>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="" id="select-all-permissions">
                                                <label class="form-check-label" for="select-all-permissions">
                                                    Select all
                                                </label>
                                            </div>
                                        </div>
                                        <div>
                                            <?php $__currentLoopData = $permissions ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group => $groups): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php $groupSlug = slugify($group) ?>
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-start align-items-center gap-2">
                                                            <h5><strong><?php echo e($group); ?></strong> </h5>
                                                            <div class="form-check">
                                                                <input class="form-check-input inputs permission-groups" value="<?php echo e($groupSlug); ?>" type="checkbox" id="<?php echo e($groupSlug); ?>-group">
                                                                <label class="form-check-label" for="<?php echo e($groupSlug); ?>-group">
                                                                    Select All
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <div class="col-3">
                                                                    <div class="form-check">
                                                                        <input class="form-check-input inputs <?php echo e($groupSlug); ?>-permission" name="permissions[]" type="checkbox" value="<?php echo e($permission->id); ?>" id="permission-<?php echo e($permission->id); ?>">
                                                                        <label class="form-check-label" for="permission-<?php echo e($permission->id); ?>">
                                                                            <?php echo e($permission->name); ?>

                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row d-none">

                <div class="col-md-12 col-sm-12 addHide">
                    <div class="card">

                        <div class="card-body mt-0 form-steps">
                            <ul class="nav nav-tabs nav-tabs-custom nav-success nav-justified mb-0"
                                role="tablist">




                                <li class="nav-item addHide">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#activityLogTab"
                                        role="tab" aria-selected="false">
                                        <i class="mdi mdi-history align-middle"></i>
                                        Activity Log</a>
                                </li>


                            </ul>
                        </div>


                        <div class="tab-content text-muted">





                            <div class="tab-pane active" id="activityLogTab" role="tabpanel">
                                <div id="activityLogDiv">
                                    <div class="">
                                        <div class=" mt-0">

                                            <div
                                                class="acitivity-timeline profile-timeline scrollable-notes scrollable-notes-reduce-height">
                                            </div>
                                            <div class="d-none p-lg-2" id="view-edit-mode">
                                                <small class="mb-0 text-muted">
                                                    <span id="created_at" class="d-none">Created:</span> <span
                                                        id="created_by_span"></span>
                                                    <br><span id="updated_at" class="d-none">Updated:</span> <span
                                                        id="updated_by_span"> </span></small>
                                            </div>
                                        </div>
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
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <table id="categoryDatatable" class="table align-middle" style="width:100%">
                        <thead class="">
                            <tr class="text-uppercase table-light2">
                            <th scope="col" style="width: 50px;">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="checkAll" value="option"
                                           data-com.bitwarden.browser.user-edited="yes">
                                </div>
                            </th>
                            <th>ID</th>
                            <th>Role</th>
                            <th>Updated At</th>
                            <th>Guard</th>
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
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.colVis.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>

        

    <script>
        let isUpdate = false;

        $(document).on("change", "#select-all-permissions", function (){
            $(".permission-groups").prop("checked", this.checked).trigger("change");
        })

        $(document).on("change", ".permission-groups", function (){
            let group = $(this).val();
            $(`.${group}-permission`).prop("checked", this.checked).trigger("change");
        })

        $("#newBtn").click(function () {
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check("Roles Create")): ?>
            $("#id").val("")
            isUpdate = false;
            $(".inputs").prop("disabled", false)
            $("select.inputs").val("").trigger("change");
            $("#mainForm").trigger("reset")
            $("#saveBtn, #closeBtn").removeClass("d-none");
            $("#newBtn, #editBtn, #deleteBtn, #multiDeleteBtn").addClass("d-none");
            toggleForm(true)
            <?php endif; ?>
        });

        $(document).on("click", "#editBtn", function () {
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check("Roles Edit")): ?>
            $(".inputs").prop("disabled", false)
            editData($('#id').val());
            $("#saveBtn, #closeBtn").removeClass("d-none");
            $("#newBtn, #editBtn, #deleteBtn, #multiDeleteBtn").addClass("d-none");
            toggleForm(true)
            <?php endif; ?>
        });

        $(document).on("click", "#closeBtn", function () {
            toggleForm(false)
        })

        function toggleForm(show) {
            if (show == undefined) {
                $("#formSection").toggleClass("show")
                return
            }
            if (show) {
                $("#formSection").addClass("show");
            } else {
                $("#saveBtn, #closeBtn, #editBtn, #deleteBtn").addClass("d-none");
                $("#newBtn, #multiDeleteBtn").removeClass("d-none");
                $("#formSection").removeClass("show");
            }
        }

        $(document).on('click', '.edit-link', function (event) { //edit link click event it will fetch single cateogry details and put it on form
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check("Roles Edit")): ?>
            $(".inputs").prop("disabled", false)
            let id = $(this).data('id');
            editData(id);
            $("#saveBtn, #closeBtn").removeClass("d-none");
            $("#newBtn, #editBtn, #deleteBtn, #multiDeleteBtn").addClass("d-none");
            toggleForm(true)
            <?php endif; ?>
        });

        $("#checkAll").change(function () {
            $(".selectable-checkbox").prop("checked", this.checked)
        })

        function getRole(id) {
            $(".permission-groups").prop("checked", false).trigger("change")
            $.ajax({
                url: "<?php echo e(route('admin.user.roles.get')); ?>",
                type: 'GET',
                dataType: 'json',
                data: {id},
                success: function (result) {
                    if (result.status == '1') {
                        $('#id').val(result.data.id);
                        let data = result.data
                        if (result.data) {
                            Object.keys(result.data).map((index) => {
                                $(`#${index}`).val(result.data[index]).trigger("change")
                            })
                            data?.permissions.map(item => {
                                $(`#permission-${item.id}`).prop("checked", true).trigger("change")
                            })
                            // getActivityLog(id)

                        }
                        toggleForm(true)
                    } else {
                        showToastify(result.msg, "error")
                    }
                },
                error: function (result) {
                    console.log(result);
                }
            });
        }

        function viewUser(id) {
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check("Roles View")): ?>
                isUpdate = false;
            getRole(id)
            $(".inputs").prop("disabled", true)
            $("#id").val(id)
            $("#saveBtn, #newBtn, #multiDeleteBtn").addClass("d-none");
            $("#editBtn, #closeBtn, #deleteBtn").removeClass("d-none");
            <?php endif; ?>
        }

        $(document).on('click', '#editBtn', function (event) {
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check("Roles Edit")): ?>
            editData($('#id').val());
            <?php endif; ?>
        });

        function editData(id) {
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check("Roles Edit")): ?>
            $("#id").val(id);
            isUpdate = true;
            getRole(id);
            $("#deleteBtn, #editBtn, #newBtn, #multiDeleteBtn").addClass("d-none");
            $("#saveBtn, #closeBtn").removeClass("d-none");
            <?php endif; ?>
        }

        let dataTable = $('#categoryDatatable').DataTable({ //datatable initialization
            "order": [[0, "DESC"]],
            language: {
                searchPlaceholder: "Search roles"
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
                url: "<?php echo e(route('admin.user.roles.dt')); ?>",
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
                    name: 'name',
                },
                {
                    data: 'updated_at',
                    name: 'updated_at',
                },
                {
                    data: 'guard_name',
                    name: 'guard_name',
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
                    render: function (type, row, data, meta) {
                        return `<div class="form-check"><input class="form-check-input selectable-checkbox" type="checkbox" name="chk_child" value="${data.id}"></div>`;
                    }
                },
                {
                    "targets": 1,
                    orderable: true,
                    render: function (type, row, data, meta) {
                        return data.id;
                    }
                },
                {
                    "targets": 2,
                    orderable: true,
                    render: function (type, row, data, meta) {
                        return `<a href="javascript:void(0)" class="text-primary" onclick="viewUser('` + data.id + `')">` + data.name + `</a>`;
                    }
                },
                {
                    "targets": 3,
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

        $(document).on('submit', '#mainForm', function (event) {
            $("#saveBtn").prop('disabled', true);
            event.preventDefault();
            var formData = new FormData($(this)[0]);
            $.ajax({
                url: isUpdate ? "<?php echo e(route('admin.user.roles.update')); ?>" : "<?php echo e(route('admin.user.roles.store')); ?>",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function (result) {
                    if (result.status == '1') {
                        showToastify(result.msg, "success")
                        $("#saveBtn").prop('disabled', false); //enable submit button
                        $('#mainForm').trigger('reset'); //
                        $("select.inputs").val("").trigger("change")
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
                        $("#submitBtn").prop('disabled', false);
                        showToastify(result.msg, "error")
                    }
                },
                error: function (jqXhr, json, errorThrown) {
                  sx
                },
            });
        });


        $(document).on('click', '#deleteBtn', function (event) {
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check("Roles Delete")): ?>
            deleteData($('#id').val());
            <?php endif; ?>
        });

        $(document).on('click', '.delete-link', function (event) {
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check("Roles Delete")): ?>
            let id = $(this).data('id');
            deleteData(id);
            <?php endif; ?>
        });



        function deleteData(id) {
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check("Roles Delete")): ?>
                Swal.fire({

                        html: '<div class="mt-3"><lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon><div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you Sure ?</h4><p class="text-muted mx-4 mb-0">Are you Sure You want to Delete this Role ?</p></div></div>',
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
                                url: "<?php echo e(route('admin.user.roles.delete')); ?>",
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    id
                                },
                                success: function(result) {
                                    if (result.status == '1') { //if successfully deleted
                                        showToastify("Role has been deleted successfully", "success")
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
            <?php endif; ?>
        }


        function getActivityLog(id) {
            $("#activityLogDiv").find(".acitivity-timeline").html("");
            $.ajax({
                url: "<?php echo e(route('admin.user.roles.activity.log')); ?>",
                type: 'GET',
                dataType: 'json',
                data: {
                    subject_model: "Role",
                    subject_id: id
                },
                success: function(result) {
                    let logs = "";
                    result?.data.map(item => {
                        let date = moment(item.created_at).fromNow()
                        let imageUrl =
                            `${item.causer ? item.causer.thumb_url : "<?php echo e(URL::asset('assets/images/users/multi-user.jpg')); ?>"}`;
                        logs += `<div class="accordion-item border-0">
                                  <div class="accordion-header" id="heading6">
                                  <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapse${item.id}" aria-expanded="false">

                                      <div class="d-flex">
                                        <div class="flex-shrink-0">
                                          <img src="${imageUrl}" alt="" class="avatar-xs rounded-circle">
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                          <h6 class="fs-14 mb-1"> ${item.causer ? item.causer.name :"System"}  </h6>
                                          <small class="text-muted">${item.description} - <small class="mb-0 text-muted">${date}</small> </small>
                                        </div>
                                      </div>
                                    </a>
                                  </div>
                                  <div id="collapse${item.id}" class="accordion-collapse collapse hide" aria-labelledby="heading6" data-bs-parent="#accordion${item.id}">
                                    <div class="accordion-body ms-2 ps-5 pt-2">
                                    <div class="code-view" id="hiddenLog${item.id}">
                                 <span class="language-markup">
                                    <small class="text-muted"><strong>New data:</strong> <br>${activityChangeDataFormat(item.properties.attributes)}<br><strong>Old Data:</strong><br>${activityChangeDataFormat(item.properties.old)} </small></span></div> </div>
                                          </div>
                                        </div>`;
                    })

                    $("#activityLogDiv").find(".acitivity-timeline").html(logs);
                },
                error: function(result) {
                    console.log(result);
                }
            });
        }

        function deleteMultiple() {
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check("Roles Delete")): ?>
                if (ids_array = [], document.getElementsByName("chk_child").forEach((function(e) {
                        if (1 == e.checked) {
                            var t = e.value;
                            ids_array.push(t)

                        }
                    })), "undefined" != typeof ids_array && ids_array.length > 0) {
                    var content = document.createElement('div');
                    content.innerHTML = "You are about to delete <b>" + ids_array.length +
                        "</b> Role(s). Once deleted, you will not be able to use them!";
                    Swal.fire({

                            html: '<div class="mt-3"><lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon><div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you Sure ?</h4><p class="text-muted mx-4 mb-0">Are you Sure You want to Delete ' +
                                ids_array.length + ' Role(s)?</p></div></div>',
                            showCancelButton: !0,
                            confirmButtonClass: "btn btn-danger w-xs me-2 mb-1",
                            confirmButtonText: "Yes, Delete " + ids_array.length + " Role(s).",
                            cancelButtonClass: "btn btn-soft-success w-xs mb-1",
                            buttonsStyling: !1,
                            showCloseButton: !0


                        })
                        .then(function(t) {

                            if (t.isConfirmed) { // on confirm delete the category
                                $.ajax({
                                    url: "<?php echo e(route('admin.user.roles.multi.delete')); ?>",
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
            <?php endif; ?>
        }

           $("#sidebarAuth").addClass('collapse show');
        $("#sidebarRoles").addClass('active');



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

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Roles Delete')): ?>
                    <a class="dropdown-item" href="javascript:;" onclick="deleteMultiple()">Delete</a>
                    <?php endif; ?>
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
                if ($cell.index() === 5|| $cell.index() === 2 ) {
                    // console.log($cell);
                } else {
                    viewUser(data.id);
                    highlightRow($(this));

                }
            } // end if
        }); // table row click


        dataTable.on('draw.dt', function() {
            $(".chk_child").prop("checked", false);
            $('.multi_options').remove();
            $('#checkAll').prop("checked", false);
        });
        $('#userActive').addClass('active');

    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/user/role.blade.php ENDPATH**/ ?>