<?php $__env->startSection('title'); ?>
    Call Logs
<?php $__env->stopSection(); ?>
<?php $__env->startSection('style'); ?>
    <!--datatable css-->
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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" type="text/css" />

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/themes/classic.min.css" />
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title mb-0 ">Calls</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Call logs</a></li>
                        </ol>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="form-check form-switch form-switch-right form-switch-md">

                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Call Logs Delete')): ?>
                                <button class="btn btn-soft-danger action-delete d-none" id="action-delete"
                                    onclick="deleteMultiple()"><i class="ri-delete-bin-2-line"></i></button>
                            <?php endif; ?>
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
                            <tr class="text-uppercase table-light2">
                                <th scope="col" style="width: 50px;">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="checkAll" value="option"
                                            data-com.bitwarden.browser.user-edited="yes">
                                    </div>
                                </th>

                                <th>-</th>
                                <th>Call </th>
                                <th>Called at</th>
                                <th>Agent</th>
                                <th width='5%'>Duration</th>
                                <th width='30%'>Recording</th>
                                <th>Created At</th>

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

    </div>
    <div class="modal zoomIn" id="viewModal" tabindex="-1" aria-labelledby="addModalLabel" data-bs-backdrop="static"
        data-bs-keyboard="false" tabindex="-1" aria-modal="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header card-header pb-2">
                    <h5 class="modal-title" id="addModalLabel">Call Log</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="row">
                            <div class="col-lg-6 col-md-6 ">
                                <div class="col-4">
                                    <label for="view-lead_id" class="form-label">Lead:</label>
                                </div>
                                <div class="col-8" id="view-lead_id"></div>
                            </div>
                            <div class="col-lg-6 col-md-6 ">
                                <div class="col-4">
                                    <label for="view-lead_follow_up_id" class="form-label">Lead follow up id:</label>
                                </div>
                                <div class="col-8" id="view-lead_follow_up_id"></div>
                            </div>
                        </div>




                        <div class="col-4">
                            <label for="view-contact_id" class="form-label">Contact:</label>
                        </div>
                        <div class="col-8" id="view-contact_id"></div>


                        <div class="col-4">
                            <label for="view-contact_follow_up_id" class="form-label">Contact follow:</label>
                        </div>
                        <div class="col-8" id="view-contact_follow_up_id"></div>

                        <div class="col-4">
                            <label for="view-client_number" class="form-label">Client Number:</label>
                        </div>
                        <div class="col-8" id="view-client_number"></div>

                        <div class="col-4">
                            <label for="view-call_type" class="form-label">Call Type:</label>
                        </div>
                        <div class="col-8" id="view-call_type"></div>

                        <div class="col-4">
                            <label for="view-user_id" class="form-label">Agent:</label>
                        </div>
                        <div class="col-8" id="view-user_id"></div>

                        <div class="col-4">
                            <label for="view-call_started_at" class="form-label">Call starting time:</label>
                        </div>
                        <div class="col-8" id="view-call_started_at"></div>

                        <div class="col-4">
                            <label for="view-call_answered_at" class="form-label">Call Answered at:</label>
                        </div>
                        <div class="col-8" id="view-call_answered_at"></div>

                        <div class="col-4">
                            <label for="view-call_duration" class="form-label">Call duration:</label>
                        </div>
                        <div class="col-8" id="view-call_duration"></div>

                        <div class="col-4">
                            <label for="view-call_status" class="form-label">Call status:</label>
                        </div>
                        <div class="col-8" id="view-call_status"></div>


                        <div class="col-lg-12">
                            <div class="hstack gap-2 justify-content-end">
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Call Logs Delete')): ?>
                                    <button id="deleteBtn" class="btn btn-danger action-delete">Delete</button>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Call Logs Edit')): ?>
                                    <button id="editBtn" class="btn btn-info action-add d-none">Create Lead</button>
                                <?php endif; ?>

                                <button type="button" class="btn btn-light action-close"
                                    data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('admin.calls.include.modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <?php echo $__env->make('admin.partials.datatable', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    


    <script>
        let isUpdate = false;
        let lead_sub_status_url = "<?php echo e(route('ajax.lead-sub-statuses')); ?>";
        let agent_sub_communities = "<?php echo e(route('ajax.user-sub-communities')); ?>";
        let mobileInput = initPhoneValidator()
        let phoneInput = initPhoneValidator("mobile", "full_mobile")
        let defaultStatusId = 1;
        let defaultSubStatusId = 4;
        let openAddModal = false;
        $('#for').select2({
            placeholder: 'Select an option',
            allowClear: true
        })
        /*New Datatable*/
        dataTable = $('#categoryDatatable').DataTable({
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
                url: "<?php echo e(route('admin.calls.dt')); ?>",
                type: "post",
                error: function() {
                    $(".users-error").html("");
                    $("#dataTable").append(
                        '<tbody class="users-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>'
                    );
                    $("#dataTable_processing").css("display", "none");
                }
            },
            createdRow: function(row, data, index) {
                $('#categoryDatatable tbody').css('cursor', 'pointer');
                // write function for dividing .callDuration into hours, minutes and seconds
                var seconds = data.call_duration;
                var hours = Math.floor(seconds / 3600);
                seconds %= 3600;
                var minutes = Math.floor(seconds / 60);
                seconds %= 60;
                var time = minutes + ":" + seconds;
                $(row).find('.callDuration').html(time);
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
                    data: function(data) {

                        if (data.call_type == 'OUTGOING') {
                            return `<div class="avatar-xs me-3" title="OUTGOING" data-bs-toggle="tooltip" data-bs-placement="top">
                                                <span class="avatar-title bg-soft-info text-info rounded-circle fs-16">
                                                    <i class="mdi mdi-phone-outgoing" ></i>
                                                </span>
                                            </div>`;
                        } else if (data.call_type == 'INCOMING') {
                            return `<div class="avatar-xs me-3" title="INCOMING" data-bs-toggle="tooltip" data-bs-placement="top">
                                                <span class="avatar-title bg-soft-success text-success rounded-circle fs-16">
                                                    <i class="mdi mdi-phone-incoming" ></i>
                                                </span>
                                            </div>`;
                        } else if (data.call_type == 'RECORDING') {
                            return `<div class="avatar-xs me-3" title="RECORDING" data-bs-toggle="tooltip" data-bs-placement="top">
                                                <span class="avatar-title bg-soft-primary text-primary rounded-circle fs-16">
                                                    <i class="mdi mdi-phone-sync" ></i>
                                                </span>
                                            </div>`;
                        } else if (data.call_type == 'MISSED') {
                            return `<div class="avatar-xs me-3" title="MISSED" data-bs-toggle="tooltip" data-bs-placement="top">
                                                <span class="avatar-title bg-soft-primary text-primary rounded-circle fs-16">
                                                    <i class="mdi mdi-phone-missed" ></i>
                                                </span>
                                            </div>`;
                        }


                    },
                    name: 'call_type',
                    searchable: true
                },

                {
                    data: function(data) {

                        if (data.leads[0]) {
                            let name = data.leads[0] ? data.leads[0]?.first_name : 'New Lead';
                            return `<div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-1" title="Lead" data-bs-toggle="tooltip" data-bs-placement="top">
                                        <div class="avatar-xs me-3">
                                        <span class="avatar-title bg-soft-success text-success fs-15 rounded">
                                                    <i class="mdi mdi-account-cash"></i>
                                                </span>
                                    </div>  </div>
                                    <div class="flex-grow-1"><span class="edit-link" data-id="` + data.id + `" >${name} <br> ${data.client_number}</span></div>
                                </div>`;
                        } else if (data.contact_id) {
                            let name = data.contact ? data.contact.first_name : 'New<br>';
                            let last = data.contact ? data.contact.last_name : '';
                            return `<div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-1" title="Contact" data-bs-toggle="tooltip" data-bs-placement="top">
                                        <div class="avatar-xs me-3">
                                        <span class="avatar-title bg-soft-info text-info fs-15 rounded">
                                                    <i class="mdi mdi-contacts"></i>
                                                </span>
                                    </div>  </div>
                                    <div class="flex-grow-1">${name} ${last} <br>${data.client_number}</div>
                                </div>`;
                        } else {
                            return `<div class="d-flex align-items-center" title="Client Number" data-bs-toggle="tooltip" data-bs-placement="top">
                                    <div class="flex-grow-1"><span class="edit-link" data-id="` + data.id + `" >${data.client_number}</span></div>
                                </div>`;
                        }


                    },
                    name: 'client_number',
                    searchable: true,
                    sortable: true
                },


                {
                    data: function(data) {

                        if (data.created_at == null) {
                            return '-';
                        } else {
                            return moment(data.call_started_at).format('DD/MM/YYYY hh:mm:ss');
                        }

                    },
                    name: 'created_at',
                    searchable: true,
                    sortable: true
                },
                {
                    data: function(data) {
                        //return data.updated_by ? data.updated_by.first_name :'';
                        if (!data.user_id) return "-";
                        return `<div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-2">
                                        <img src="${data.user.thumb_url}" alt="" class="avatar-xs rounded-circle">
                                    </div>
                                    <div class="flex-grow-1">${data.user.name}</div>
                                </div>`;
                    },
                    name: 'user_id',
                    searchable: false,
                    sortable: false
                },
                {
                    data: function(data) {

                        return `<a href="javascript:void(0)" class="text-${data.color_code ? data.color_code : 'primary'}" onclick="editData('` +
                            data.id + `')"><span class="callDuration">` + data.call_duration +
                            `</span></a>`;
                    },
                    name: 'call_duration',
                    searchable: false,
                    sortable: false
                },
                {
                    data: function(data) {

                        if (data.call_duration != 0) {


                            if (data.call_recording_url) {
                                return `<audio style="width: 100%" src="${data.call_recording_url}" controls></audio>`;
                            } else {
                                return '-';
                            }

                        } else {

                            return '-';
                        }

                    },
                    name: 'call_recording_url',
                    searchable: false,
                    sortable: false

                },

                {
                    data: function(data) {
                        return `<div class="flex-grow-1"><p title="Created at" class="mb-0"><small>${moment(data.created_at).fromNow()}</small></p></div>
`
                    },
                    name: 'created_at',
                    searchable: false,
                    sortable: false

                }



            ],
        }); // end datatable init

        $('#property_id').select2({
            placeholder: 'Select Property',
            allowClear: true,
            closeOnSelect: true,
            dropdownParent: $('#addModal')
        })
        $('#furnished').select2({
            placeholder: 'Select furnishing type',
            allowClear: true,
            closeOnSelect: true,
            dropdownParent: $('#addModal')
        })


        $('#categoryDatatable tbody').on('change', 'input.status-change', function() {
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Call Logs Edit')): ?>
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
                    url: "<?php echo e(route('admin.location.region.status')); ?>",
                    data: formData,
                    dataType: 'json',
                    success: function(data) {
                        if (data.status == '1') {
                            showToastify(data.msg, "success")
                            dataTable.row().draw();
                        }
                    },
                    error: function(data) {
                        console.log('Error:', data);
                    }
                });
            <?php endif; ?>
        });


        $(document).on('click', '.addModal', function(event) {
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Call Logs Create')): ?>
                isUpdate = false;
                $('#modal_title').html('Add');
                $("#id").val("")
                $('#mainForm').trigger('reset');
                $('#mainForm').removeClass('was-validated');
            <?php endif; ?>
        });


        $(document).on('click', '#deleteBtn', function(event) {
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Call Logs Delete')): ?>
                deleteData($('#id').val());
            <?php endif; ?>
        });

        $(document).on('click', '#editBtn', function(event) {
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Call Logs Delete')): ?>
                $('#viewModal').modal('hide');
                editData($('#id').val());
            <?php endif; ?>
        });


        $(document).on('click', '.edit-link', function(
            event) { //edit link click event it will fetch single cateogry details and put it on form
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Call Logs Edit')): ?>
                let id = $(this).data('id');
                $('#id').val(id);
                // editData(id);
            <?php endif; ?>
        });

        $(document).on('click', '.delete-link', function(event) {
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Call Logs Edit')): ?>
                let id = $(this).data('id');
                deleteData(id);
            <?php endif; ?>
        });

        $(document).on('submit', '#mainForm', function(event) {
            $("#submitBtn").prop('disabled', true);
            event.preventDefault();
            var formData = new FormData($(this)[0]);
            $.ajax({
                url: "<?php echo e(route('admin.calls.updateLead')); ?>",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function(result) {
                    if (result.status == '1') {
                        showToastify(result.msg, "success")

                        // $('#addModal').modal('hide'); //hide form modal
                        $("#submitBtn").prop('disabled', false); //enable submit button
                        // $('#mainForm').trigger('reset'); //
                        dataTable.row()
                            .draw(); // redraw the datatable show we don't need to reload the page to see the changes
                        isUpdate =
                            false; // set isUpdate to false so that when we try to add new category it will be create request
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
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Call Logs Delete')): ?>
                let ids = [];
                $(".selectable-checkbox").map((index, item) => {
                    item.checked ? ids.push(item.value) : []
                })
                if (ids.length > 0) {


                    Swal.fire({

                            html: '<div class="mt-3"><lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon><div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you Sure ?</h4><p class="text-muted mx-4 mb-0">Are you Sure You want to Delete ' +
                                ids.length + ' Call Log(s)?</p></div></div>',
                            showCancelButton: !0,
                            confirmButtonClass: "btn btn-danger w-xs me-2 mb-1",
                            confirmButtonText: "Yes, Delete " + ids.length + "  Call Log(s).",
                            cancelButtonClass: "btn btn-soft-success w-xs mb-1",
                            buttonsStyling: !1,
                            showCloseButton: !0


                        })
                        .then(function(t) {

                            if (t.isConfirmed) { // on confirm delete the category
                                $.ajax({
                                    url: "<?php echo e(route('admin.calls.multidelete')); ?>",
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
                            }
                        })




                    // var content = document.createElement('div');
                    // content.innerHTML = "You are trying to delete  <b>" + ids.length +
                    //     "</b>  Call Log(s). Once deleted, you will not be able to recover it.";
                    // swal({
                    //         title: "Are you sure?",
                    //         content: content,
                    //         buttons: true,
                    //         dangerMode: true,
                    //         showCancelButton: true,
                    //         confirmButtonColor: "#51d28c",
                    //         cancelButtonColor: "#f34e4e",
                    //         confirmButtonText: "Yes, delete it!"
                    //     })
                    //     .then((willDelete) => {
                    //         if (willDelete) {
                    //             $.ajax({
                    //                 url: "<?php echo e(route('admin.calls.multidelete')); ?>",
                    //                 type: 'POST',
                    //                 dataType: 'json',
                    //                 data: {
                    //                     ids
                    //                 },
                    //                 success: function(result) {
                    //                     if (result.status == '1') {
                    //                         showToastify(result.msg, "success")
                    //                         $('.multi_options').remove();

                    //                         dataTable.row().draw();
                    //                     } else {
                    //                         showToastify(result.msg, "error")
                    //                     }
                    //                 },
                    //                 error: function(result) {
                    //                     console.log(result);
                    //                 }
                    //             });
                    //             document.getElementById("checkAll").checked = !1
                    //         }
                    //     });
                } else {
                    showToastify("Please select at least one checkbox", "error")
                }
            <?php endif; ?>
        }

        function viewCategory(id) {
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Call Logs View')): ?>
                $.ajax({
                    url: "<?php echo e(route('admin.calls.get')); ?>",
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

                                    if (result.data[index]) {
                                        $(`#view-${index}`).html(result.data[index])
                                        console.log(index + ' ' + result.data[index])
                                    } else {
                                        $(`#view-${index}`).html('-');
                                        console.log(index)
                                    }

                                    if (index == 'lead_id' && result.data[index] == null) {
                                        $('#editBtn').removeClass('d-none');
                                    }


                                })
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
            <?php endif; ?>
        }

        function highlightRow(row) {
            row.removeClass('selected');
            dataTable.$('tr.selected').removeClass('selected');
            row.addClass('selected');
        } // highlightRow



        function getProperties() {
            $("#property_id").val("").trigger("change");
            $("#property_id").html("");
            let id = $("#agent_id").val();
            let lead_for = $("#for").val();
            if (id) {
                $.ajax({
                    url: "<?php echo e(route('ajax.properties')); ?>",
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        agent_id: id,
                        for: lead_for
                    },
                    success: function(result) {
                        let options = "";
                        result?.data.map(item => {
                            options +=`<option value="${item.id}">${item.ref_no} - ${item.property_category?.title || ""} - ${item.unit_no || "s"} - ${item.location}</option>`;
                        })

                        $("#property_id").html(options);
                        // $("#property_id").val(dataObj?.property_id).trigger("change");
                    },
                    error: function(result) {
                        console.log(result);
                    }
                });
            }
        }

        dataTable.on('click', 'tbody tr', function(e) {
            var $cell = $(e.target).closest('td'),
                msg;
            //console.log('Index ==========='+$cell.index());
            if ($cell.index() != 0) {
                var data = dataTable.row(this).data();
                //
                if ($cell.index() === 6) {
                    // console.log($cell);
                } else {
                    editData(data.id);
                    highlightRow($(this));

                }
            } // end if
        });
        // table row click

        $(document).ready(function() {
            $('#categoryDatatable_filter input').attr("placeholder", "Search...");

            $('#categoryDatatable_filter input').after(` <a href="javascript:void(0)" class="text-secondary" title="Refresh table"
           id="refreshTableButton">
            <i class="bx bx-refresh mb-1 me-1 align-bottom" style="font-size: 25px"></i>
        </a>`);
        });

        $(document).on("click", "#refreshTableButton", function() {
            dataTable.row().draw();
        })

        $("#checkAll").change(function() {
            $(".selectable-checkbox").prop("checked", this.checked)
        });

        function deleteData(id) {
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Call Logs Delete')): ?>



                Swal.fire({

                        html: '<div class="mt-3"><lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon><div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you Sure ?</h4><p class="text-muted mx-4 mb-0">Are you Sure You want to Delete ' +
                            ids_array.length + ' Lead(s)?</p></div></div>',
                        showCancelButton: !0,
                        confirmButtonClass: "btn btn-danger w-xs me-2 mb-1",
                        confirmButtonText: "Yes, Delete  Record(s).",
                        cancelButtonClass: "btn btn-soft-success w-xs mb-1",
                        buttonsStyling: !1,
                        showCloseButton: !0


                    })
                    .then(function(t) {

                        if (t.isConfirmed) { // on confirm delete the category
                            $.ajax({
                                url: "<?php echo e(route('admin.location.region.delete')); ?>",
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
            <?php endif; ?>
        }
        // $('#min_price').select2({
        //     placeholder: 'Min',
        //     allowClear: true,
        //     closeOnSelect: false,
        //     dropdownParent: $('#criteriaForm')
        // });
        // $('#max_price').select2({
        //     placeholder: 'Max',
        //     allowClear: true,
        //     closeOnSelect: false,
        //     dropdownParent: $('#criteriaForm')
        // });


        function editData(id) {
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Call Logs Edit')): ?>
                isUpdate = true; // set isUpdate to true so that we can identify that this is update request
                $.ajax({
                    url: "<?php echo e(route('admin.calls.get')); ?>",
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        id
                    },
                    success: function(result) {
                        $('#mainForm').trigger("reset");
                        console.table(result)
                        if (result.status == '1') {

                            $('#sub_communities').val('').trigger('change');
                            $('#property_id').val('').trigger('change');

                            $('#TablecontainerDiv').addClass('d-none');
                            $('#Tablecontainer').html('');
                            $('#mainForm').trigger('reset');
                            $('#id').val(result.data.id)
                            $('#call_log_id').val(result.data.id)

                            if (result.data.call_recording_url) {
                                $('#call_rec').html(
                                    `<audio style="width: 50%;height:25px" src="${result.data.call_recording_url}" controls></audio>`
                                );
                            }


                            $('#edit-phone').val(result.data.client_number)

                            if (result.data) {
                                $('.inputsCriteria').val('');
                                Object.keys(result.data).map((index) => {
                                    if (result.data[index]) {
                                        $(`#edit-${index}`).html(result.data[index])
                                        // console.log(index + ' ' + result.data[index])
                                    } else {
                                        $(`#edit-${index}`).val('');
                                        // console.log(index)
                                    }
                                })

                                var seconds = result.data.call_duration;
                                var hours = Math.floor(seconds / 3600);
                                seconds %= 3600;
                                var minutes = Math.floor(seconds / 60);
                                seconds %= 60;
                                var time = minutes + ":" + seconds;

                                if (result.data.user) {
                                    $('.viewAgent').attr('data-id', result.data.user.id);
                                    $('#agent_detail').html(`
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-2">
                                                <img src="${result.data.user.thumb_url}" alt="" class="avatar-xs rounded-circle">
                                            </div>
                                            <div class="flex-grow-1">${result.data.user.name}</div>
                                        </div>`)
                                    $('#agent_id').val(result.data.user.id);
                                } else {
                                    $('#viewAgent_id').addClass('d-none');
                                    $('#agent_detail').html('');
                                }


                                $(`.lead_id`).val(result.data.lead_id);
                                $('#idSpan').addClass('d-none');
                                if (result.data.leads[0]) {
                                    getProperties();
                                    $('#modal_title').html('Update Lead');
                                    $('#idSpan').removeClass('d-none');
                                    $('#leadID').html(result.data.leads[0].id);

                                    $('.viewLead').attr('data-id', result.data.leads[0].id);
                                    $('.no_lead').removeClass('d-none');

                                    $(`.lead_id`).val(result.data.leads[0].id);
                                    // alert(result.data.lead.id);
                                    $('#criteria_lead_id').val(result.data.leads[0].id);

                                    if (result.data.leads[0].first_name == 'New') {
                                        $(`#edit-first_name`).val('')
                                        $(`#edit-last_name`).val('')
                                    } else {
                                        $(`#edit-first_name`).val(result.data.leads[0].first_name)
                                        $(`#edit-last_name`).val(result.data.leads[0].last_name)
                                    }
                                    $(`#near_number_first_name`).html(result.data.leads[0].first_name)
                                    $(`#mobile`).val(result.data.leads[0].mobile)
                                    $(`#edit-lead_id`).val(result.data.leads[0].id)
                                    $('#type_id').val(result.data.leads[0].lead_type_id).trigger('change');
                                    $('#lead_status_id').val(result.data.leads[0].lead_status_id).trigger(
                                        'change');

                                    Object.keys(result.data.leads[0]).map((index) => {
                                        $(`#${index}`).val(result.data.leads[0][index]).trigger("change")
                                    });

                                    // if (result.data?.leads[0].property) {
                                    //     let item = result.data?.leads[0].property;
                                    //     let option =
                                    //         `<option value="${item.id}">${item.ref_no} - ${item.property_category?.title || ""} - ${item.unit_no || "s"} - ${item.location}</option>`;
                                    //     $("#property_id").html(option);
                                    // }
                                    //

                                    $('#type_id').val(result.data.leads[0].type_id).trigger('change');
                                    $('#lead_sub_status_id').val(result.data.leads[0].lead_sub_status_id)
                                        .trigger('change');


                                    var $table = $('<table class="table">').attr('id', 'myTable');
                                    var $thead = $('<thead>').appendTo($table);
                                    var $headerRow = $('<tr>').appendTo($thead);
                                    $('<th>').text('Name').appendTo($headerRow);
                                    $('<th>').text('Email').appendTo($headerRow);
                                    $('<th>').text('Mobile').appendTo($headerRow);
                                    $('<th>').text('Action').appendTo($headerRow);
                                    var $tbody = $('<tbody>').appendTo($table);
                                    let counter = 0;
                                    $.each(result.data.leads, function(key, item) {

                                        if (key == 0) {
                                            counter++;
                                            return true;
                                        }

                                        var $row = $('<tr>').appendTo($tbody);
                                        $('<td>').text(item.first_name + ' ' + item.last_name).appendTo(
                                            $row);
                                        $('<td>').text(item.email).appendTo($row);
                                        $('<td>').text(item.mobile).appendTo($row);
                                        var $linkCell = $('<td>').appendTo($row);
                                        $('<a>').html(
                                            ` <span class="badge badge-soft-success" data-id="">View Lead <i class="ri-arrow-right-up-line align-bottom"></i></span>`
                                        ).attr({
                                            'href': 'work/leads?id=' + item.id,
                                            'target': '_blank'
                                        }).appendTo($linkCell);
                                        counter++;
                                    });




                                    // Create table rows and columns



                                    $('#Tablecontainer').append($table);
                                    $('#TablecontainerDiv').removeClass('d-none');



                                    if (counter > 1) {
                                        $('#TablecontainerDiv').removeClass('d-none');

                                    } else {
                                        $('#TablecontainerDiv').addClass('d-none');

                                    }

                                    $('#viewLead_id').removeClass('d-none');
                                } else {
                                    $('#viewLead_id').addClass('d-none');
                                    $('.no_lead').addClass('d-none');

                                }
                                if (result.data.contact) {
                                    $('.viewContact').attr('data-id', result.data.contact.id);
                                    $('#modal_title').html('View Contact');
                                    $('#viewContact_id').removeClass('d-none');


                                } else {
                                    $('#viewContact_id').addClass('d-none');
                                    $('#modal_title').html('View Details');
                                }

                                if (result.data.call_type) {
                                    if (result.data.call_type == 'INCOMING') {
                                        $('#view-call-type').html(`<div class="avatar-xs ">
                                                <span class="avatar-title bg-soft-success text-success rounded-circle fs-16">
                                                    <i class="mdi mdi-phone-incoming"></i>
                                                </span>
                                            </div>`)
                                    }
                                    if (result.data.call_type == 'OUTGOING') {
                                        $('#view-call-type').html(`<div class="avatar-xs ">
                                                <span class="avatar-title bg-soft-info text-info rounded-circle fs-16">
                                                    <i class="mdi mdi-phone-outgoing"></i>
                                                </span>
                                            </div>`)
                                    }
                                    if (result.data.call_type == 'RECORDING') {
                                        $('#view-call-type').html(`<div class="avatar-xs ">
                                                <span class="avatar-title bg-soft-primary text-primary rounded-circle fs-16">
                                                    <i class="mdi mdi-phone-sync"></i>
                                                </span>
                                            </div>`)
                                    }
                                    if (result.data.call_type == 'MISSED') {
                                        $('#view-call-type').html(`<div class="avatar-xs ">
                                                <span class="avatar-title bg-soft-primary text-primary rounded-circle fs-16">
                                                    <i class="mdi mdi-phone-missed"></i>
                                                </span>
                                            </div>`)
                                    }

                                }

                                if (result.data.call_started_at) {
                                    var inputDate = result.data.call_started_at;
                                    var date = new Date(inputDate);
                                    var formattedDate = ("0" + date.getDate()).slice(-2) + "/" + ("0" + (date
                                            .getMonth() + 1)).slice(-2) + "/" + date.getFullYear() + " " + (
                                            "0" + date.getHours()).slice(-2) + ":" + ("0" + date.getMinutes())
                                        .slice(-2) + ":" + ("0" + date.getSeconds()).slice(-2);
                                    $('#call_log_info').html('Call started at: ' + formattedDate);
                                }
                                console.log(result.data.lead_id);
                                $("#criteriaForm").val('').trigger("reset");
                                $('#leadCirteriaSaveBtn').attr('disabled', true);

                                if (result.data.leads[0]?.criteria != null) {
                                    toggleCirteriaForm(false);
                                    $("#criteriaForm").trigger("reset");
                                    $("#id, #criteria_lead_id").val(result.data?.leads[0]?.id)
                                    // alert(result.data.lead.criteria.id);
                                    Object.keys(result.data?.leads[0]?.criteria).map((index) => {
                                        if (index != "id") {
                                            $(`#${index}`).val(result.data?.leads[0]?.criteria[index])
                                                .trigger("change")
                                        }
                                        if (index == "communities") {
                                            let communities = result.data?.leads[0]?.criteria[
                                                'communities'];
                                            let text
                                            $.each(communities, function(key, value) {
                                                text += value + " ";
                                            });
                                            let locations = result.data?.leads[0]?.criteria?.locations;
                                            let subCom = [];
                                            let locationTypes = [];
                                            let cmb = '';

                                            function changeSubCommunites(data) {
                                                $('#sub_communities').val(data).trigger('change');
                                                toggleCirteriaForm(true)
                                            };
                                            if (jQuery.isEmptyObject(locations)) {
                                                toggleCirteriaForm(true)
                                            }
                                            $.each(locations, function(key, value) {
                                                if (value.parentable_type ==
                                                    'App\\Models\\Community') {
                                                    $('#communities').find("option[value='" +
                                                        value.parentable_id + "']").remove();
                                                    var newOption = new Option(value?.parentable
                                                        ?.path_name, value.parentable_id,
                                                        true, true
                                                    );
                                                    $('#communities').append(newOption).trigger(
                                                        'change');
                                                }
                                                if (value.parentable_type ==
                                                    'App\\Models\\SubCommunity') {
                                                    let cmbid = value.parentable_id +
                                                        ':sub_community';
                                                    if ($('#sub_communities').find(
                                                            "option[value='" +
                                                            cmbid + "']").length) {
                                                        $('#sub_communities').val(value
                                                                .parentable_id)
                                                            .trigger('change');
                                                    } else {
                                                        var newOption = new Option(value
                                                            ?.parentable
                                                            ?.path_name, cmbid, true, true);
                                                        $('#sub_communities').append(newOption)
                                                            .trigger(
                                                                'change');
                                                    }

                                                    subCom.push(cmbid);
                                                    $("#sub_communities").append(
                                                        '<option data-type="sub_community" value="' +
                                                        value.parentable_id +
                                                        '" selected="true">' + value
                                                        ?.parentable?.path_name +
                                                        '</option>');

                                                }
                                                if (value.parentable_type ==
                                                    'App\\Models\\CommunityProperty') {
                                                    let cmbid = value.parentable_id +
                                                        ':community_property';
                                                    if ($('#sub_communities').find(
                                                            "option[value='" +
                                                            cmbid + "']").length) {
                                                        $('#sub_communities').val(cmbid)
                                                            .trigger(
                                                                'change');
                                                    } else {
                                                        var newOption = new Option(value
                                                            ?.parentable
                                                            ?.path_name, cmbid, true, true);
                                                        $('#sub_communities').append(newOption)
                                                            .trigger(
                                                                'change');
                                                    }
                                                    //  $("#sub_communities").append('<option data-type="community_property" value="'+value.parentable_id+'" selected="true">'+value?.parentable?.path_name+'</option>');
                                                    subCom.push(cmbid);


                                                }
                                                setTimeout(function() {
                                                        changeSubCommunites(subCom)
                                                        $('#property_id').val(result.data.leads[0].property_id).trigger('change');

                                                    },
                                                    800

                                                );

                                                //selecting sub-community after 3 second due to ajax call




                                            });

                                        }
                                    });


                                    $("#min_price").val(result.data?.leads[0].criteria?.min_price).trigger(
                                        "change");
                                    if (!$('#min_price').find("option[value='" + result.data?.leads[0].criteria
                                            ?.min_price + "']").length) {
                                        var newOption = new Option(result.data?.leads[0].criteria?.min_price,
                                            result.data?.criteria?.min_price, true, true);
                                        $('#min_price').append(newOption).trigger('change');

                                    }



                                    $("#max_price").val(result.data?.leads[0].criteria?.max_price).trigger(
                                        "change");
                                    if (!$('#max_price').find("option[value='" + result.data?.leads[0].criteria
                                            ?.max_price + "']").length) {
                                        var newOption = new Option(result.data?.leads[0].criteria?.max_price,
                                            result.data.leads[0].criteria?.max_price, true, true);

                                        $('#max_price').append(newOption).trigger('change');
                                    }


                                    // setTimeout(function() {
                                    //     toggleCirteriaForm(true)
                                    // }, 3000);
                                    // setTimeout(function() {
                                    //         $('#leadCirteriaSaveBtn').attr('disabled', false);
                                    //     },
                                    //     3000
                                    // );

                                } else {
                                    $("#criteriaForm").trigger("reset");
                                    setTimeout(function() {
                                            $('#leadCirteriaSaveBtn').attr('disabled', false);
                                        },
                                        1000
                                    );
                                    toggleCirteriaForm(true)
                                }
                            }

                            $('#criteriaForm').addClass('d-none');
                            $('#mainForm').removeClass('d-none');
                            openAddModal = true;
                            mobileInput = initPhoneValidator()


                            $('#addModal').modal('show');

                            $('#mainForm').removeClass('was-validated');

                        } else {
                            showToastify(result.msg, "error")
                        }
                    },
                    error: function(result) {
                        console.log(result);
                    }
                });
            <?php endif; ?>
        }

        $('.CloseAddModal').click(function() {
            $('#addModal').modal('hide');
            openAddModal = false;
        })

        function toggleCirteriaForm(flag) {
            if (flag) {
                $("#criteriaTabLoader").addClass('d-none');
                $("#criteriaFormDiv").removeClass("d-none");
            } else {
                $("#criteriaTabLoader").removeClass('d-none');
                $("#criteriaFormDiv").addClass("d-none");
            }
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
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Leads Archive')): ?>
                                <button type="button" class="dropdown-item" id="archiveBtn" data-id="">Archive</button>
                                <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Call Logs Delete')): ?>
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

        dataTable.on('draw.dt', function() {
            $(".chk_child").prop("checked", false);
            $('.multi_options').remove();
            $('#checkAll').prop("checked", false);
        });


        $('#callLog').addClass('active');
        $(document).on("change", "#lead_status_id", function() {
            let lead_status_id = $(this).val();
            $("#lead_sub_status_id").val("").trigger("change");
            $("#lead_sub_status_id").html("");
            if (lead_status_id) {
                $.ajax({
                    url: "<?php echo e(route('ajax.lead-sub-statuses')); ?>",
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        lead_status_id
                    },
                    success: function(result) {
                        // console.table(result)
                        let options = "";
                        result?.data.map(item => {
                            options += `<option value="${item.id}">${item.name}</option>`
                        })
                        $("#lead_sub_status_id").html(options)
                        // alert(options)
                        // $("#lead_sub_status_id").val(dataObj?.lead_sub_status_id || defaultSubStatusId).trigger("change")
                    },
                    error: function(result) {
                        console.log(result);
                    }
                });
            }

        });


        $(document).on("submit", "#criteriaForm", function(e) {
            $("#leadCirteriaSaveBtn").prop('disabled', true);
            e.preventDefault();
            // alert($('.lead_id').val());
            var formData = new FormData($("#criteriaForm")[0]);
            console.table(formData)
            $.ajax({
                url: isUpdate ? "<?php echo e(route('admin.work.leads.store.criteria')); ?>" :
                    "<?php echo e(route('admin.work.leads.store.criteria')); ?>",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function(result) {
                    if (result.status == '1') {
                        showToastify(result.msg, "success")
                        $("#leadCirteriaSaveBtn").prop('disabled', false); //enable submit button
                    } else if (result.status == '2') {
                        $("#leadCirteriaSaveBtn").prop('disabled', false);
                        let error_message = '';
                        $.each(result.errors, function(key, value) {
                            error_message += '' + key + ': ' + value + ' | ';
                        });
                        showToastify(error_message, "error")
                    } else {
                        $("#leadCirteriaSaveBtn").prop('disabled', false);
                        showToastify(result.msg, "error")
                    }
                    // $('#addModal').modal('hide'); //hide form modal
                },
                error: function(jqXhr, json, errorThrown) {
                    $("#leadCirteriaSaveBtn").prop('disabled', false);
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
            })
        });

        function locationSelect2() {

            $('#communities').select2({
                placeholder: 'Community',
                allowClear: true,
                closeOnSelect: false,
                ajax: {
                    url: '<?php echo e(route('ajax.community.select2')); ?>',
                    dataType: 'json',
                    type: 'GET',
                    processResults({
                        data
                    }) {

                        return {
                            results: $.map(data, function(item) {
                                return {
                                    text: `${item.path_name}`,
                                    id: item.id

                                }
                            })
                        }
                    }
                }
            });

            $('#sub_communities').select2({
                placeholder: 'Tower, community, locality',
                allowClear: true,
                closeOnSelect: false,
                ajax: {
                    url: '<?php echo e(route('ajax.sub-community.property.select2')); ?>',
                    dataType: 'json',
                    data: function(term, page) {
                        return {
                            q: term, // search term
                            communities: $("#communities").val(),
                            page_limit: 10
                        };
                    },
                    processResults({
                        data
                    }) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    text: `${item.text}`,
                                    id: item.id + ':' + item.type,
                                    'lat': item.lat,
                                    'long': item.long,
                                    'path': item.path,
                                    'type': item.type
                                }
                            })
                        }
                    }
                }
            });

        }

        locationSelect2();
        $(document).ready(function() {
            $('.viewAgent').click(function() {
                event.preventDefault(); // prevent the link from following the href

                var url = '/user/users?id=' + $(this).data('id');
                window.open(url, '_blank'); // open the link in a new tab

                //    location.replace('/user/users?id='+$(this).attr('data-id'),'_blank');
            })
        });
        $(document).ready(function() {
            $('.viewLead').click(function() {
                var url = '/work/leads?id=' + $(this).data('id');
                window.open(url, '_blank'); // open the link in a new tab

                //    location.replace('/work/leads?id='+$(this).attr('data-id'));
            })
        });
        $(document).ready(function() {
            $('.viewContact').click(function() {
                var url = '/work/contacts?contact=' + $(this).data('id');
                window.open(url, '_blank'); // open the link in a new tab

                //    location.replace('/work/leads?id='+$(this).attr('data-id'));
            })
        });

        const locations = [];

        $('.add_critera').click(function() {
            $('#modal_title').html('Add Lead Criteria');
            $('#criteriaForm').removeClass('d-none');
            $('#mainForm').addClass('d-none');
        });

        $('.lead_info').click(function() {
            $('#modal_title').html('Update Lead');

            $('#criteriaForm').addClass('d-none');
            $('#mainForm').removeClass('d-none');
        });


        $(document).on("click", "#archiveBtn", function() {
            let id = $(this).attr('data-id');
            $('#archive_lead_id').val(id);
            $('#archiveModal').modal('show');

        })

        $('#preFillOption').select2({
            placeholder: 'Select agent',
            allowClear: true
        });
        $(document).ready(function() {
            $('#preFillOption').change(function() {
                // get selected value of option
                var option = $(this).find('option:selected').val();
                if (option == 'Other') {
                    $('#archive_note').val('')
                    $('#archive_note').removeClass('d-none');
                    $('#archive_note').focus();
                } else {
                    $('#archive_note').val(option)
                    $('#archive_note').addClass('d-none');
                }
            });
        })
        $(document).on("click", "#saveArchive", function() {
            $('#screen_loader').removeClass('d-none');
            let archive_note = $('#archive_note').val();
            if (archive_note == "") {
                showToastify("Please Add Archive note.", "error")
                return;
            } else {
                if (ids_array = [], document.getElementsByName("chk_child").forEach((function(e) {
                        if (1 == e.checked) {
                            var t = e.value;
                            ids_array.push(t)
                        }
                    })), "undefined" != typeof ids_array && ids_array.length > 0) {
                    $.ajax({
                        url: "<?php echo e(route('admin.calls.archive')); ?>",
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            ids: ids_array,
                            archive_note: archive_note,
                        },
                        success: function(result) {
                            $('#screen_loader').addClass('d-none');
                            if (result.status == '1') {
                                showToastify(result.msg, "success")
                                $("#archive_note").val("")
                                $("#archiveModal").modal("hide")
                                $("#checkAll").prop("checked", this.checked)
                                document.getElementById("checkAll").checked = !1
                                dataTable.row().draw();
                                $('.multi_options').remove();

                                toggleCirteriaForm(false);
                                $("#addOnAgentDiv, #newBtn, #closeBtn, #followUpMenusTab, .addHide,#dropdownMenuLink1,#saveBtn")
                                    .addClass("d-none");
                                $("#collapseContactForm").removeClass("show");
                                $("#editBtn, #bulkAssignBtn").addClass("d-none");
                                $("#newBtn").removeClass("d-none");

                                $(".inputs").prop("disabled", false);
                                $(".inputs").prop("readonly", true);
                                $("#mainForm").trigger("reset")
                                $(".inputs").val("").trigger("change")
                                $(".inputs").removeClass("is-invalid")

                            } else {
                                showToastify(result.msg, "error")
                            }

                        },
                        error: function(jqXhr, json, errorThrown) {
                            $('button').prop("disabled", false);
                            $('#screen_loader').addClass('d-none');

                            let data = jqXhr.responseJSON;
                            if (data.errors) {
                                $.each(data.errors, function(index, item) {
                                    showToastify(item[0], "error")
                                })
                            }
                            if (jqXhr.status == 500 || jqXhr.status == 400) {
                                showToastify(data.message, "error")
                            }
                            // $('#screen_loader').addClass('d-none');

                        },
                    });
                }

                // Ajax Form goes here
            }
        })

        //


        $(document).on('click', '#archiveLeadBtn', function(event) {
            $("input[name='chk_child']").each(function() {
                $(this).prop('checked', false);
            });
            $("input[name='chk_child']").each(function() {
                if ($(this).val() == $('#call_log_id').val()) {
                    $(this).prop('checked', true);
                    $('#addModal').modal('hide');
                    $('#archiveModal').modal('show');
                }
            });
        });


        // reopen leadpopup on click of close archive modal

        $(document).on('click', '#closeArchiveModal', function(event) {
            //opan add modal
            if (openAddModal == true) {
                $('#addModal').modal('show');
            }

        });



        $('#lead_sub_status_id').select2({
            placeholder: 'Select Lead Sub Status',
            allowClear: true,
            closeOnSelect: false,
            dropdownParent: $('#mainForm')
        });

        $('#type_id').select2({
            placeholder: 'Select Lead Type',
            allowClear: true,
            closeOnSelect: false,
            dropdownParent: $('#mainForm')
        });
        $('#source_id').select2({
            placeholder: 'Select Lead Source',
            allowClear: true,
            closeOnSelect: false,
            dropdownParent: $('#mainForm')
        });

        $('#min_bed_id').select2({
            placeholder: 'Min',
            allowClear: true,
            closeOnSelect: false,
            dropdownParent: $('#addModal')
        });

        $('#max_bed_id').select2({
            placeholder: 'Max',
            allowClear: true,
            closeOnSelect: false,
            dropdownParent: $('#addModal')
        });

        $('#min_bath_id').select2({
            placeholder: 'Min',
            allowClear: true,
            closeOnSelect: false,
            dropdownParent: $('#addModal')
        });
        $('#max_bath_id').select2({
            placeholder: 'Max',
            allowClear: true,
            closeOnSelect: false,
            dropdownParent: $('#addModal')
        });



        $('#lead_status_id').select2({
            placeholder: 'Select Status',
            allowClear: true,
            closeOnSelect: false,
            dropdownParent: $('#mainForm')
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/calls/index.blade.php ENDPATH**/ ?>