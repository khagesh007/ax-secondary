@extends('admin.layouts.master')
@section('title')
    Jobs
@endsection
@section('style')
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
    <form class="row needs-validation" id="mainForm" novalidate>
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title mb-0 ">Jobs</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Jobs</a></li>
                            <li class="breadcrumb-item active">Jobs</li>
                        </ol>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="form-check form-switch form-switch-right form-switch-md">
                            @can('Jobs Create')
                                <button type="button" id="newBtn" class="btn btn-danger add-btn action-add">
                                    <i class="ri-add-line align-bottom me-1"></i> Add New
                                </button>
                            @endcan
                            <button type="submit" class="btn btn-success d-none action-save" id="saveBtn">Save</button>
                            @can('Jobs Edit')
                                <button type="button" class="btn btn-primary d-none action-edit" id="editBtn">Edit</button>
                            @endcan
                            @can('Jobs Delete')
                                <button type="button" class="btn btn-danger d-none action-delete"
                                    id="deleteBtn">Delete</button>
                            @endcan
                            <button type="button" class="btn btn-light d-none action-close" id="closeBtn">Close</button>
                            {{-- @can('Jobs Delete')
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
                <input type="hidden" id="id" name="id">
                <div class="card">
                    <div class="card-body">
                        <div>
                            <div class="row">
                                <div class="col-4">
                                    <div class="row">
                                        <div class="col-4">Requested By*</div>
                                        <div class="col-8">
                                            <select name='requested_by_id' id="requested_by_id"
                                                class="form-control select2 inputs" required>
                                                <option value="" disabled selected></option>
                                                @foreach ($agents ?? [] as $agent)
                                                    <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">
                                                Please choose requested by.
                                            </div>
                                        </div>
                                        <div class="col-4 mt-2">Job Type*</div>
                                        <div class="col-8 mt-2">

                                            <select name='type' id="type" class="form-control select2 inputs"
                                                required>
                                                <option value="" disabled selected></option>
                                                @foreach (\App\Models\Job::TYPE_SELECT ?? [] as $type)
                                                    <option value="{{ $type }}">{{ $type }}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">
                                                Please choose job type.
                                            </div>
                                        </div>
                                        <div class="col-4 mt-2 listings d-none">Listing*</div>
                                        <div class="col-8 mt-2 listings d-none">
                                            <select name='property_id' id="property_id" class="form-control select2 inputs">
                                                <option value="" disabled selected></option>
                                            </select>
                                            <div class="invalid-feedback">
                                                Please choose listing.
                                            </div>
                                        </div>

                                        <div class="col-4 mt-2">Job Description</div>
                                        <div class="col-8 mt-2">
                                            <textarea class="form-control inputs summernote" id="description" placeholder="Enter description" name="description"
                                                rows="4"></textarea>
                                            <div class="invalid-feedback">
                                                Please enter description.
                                            </div>
                                        </div>

                                        <div class="col-4 mt-2">Priority*</div>
                                        <div class="col-8 mt-2">
                                            <select name='priority' id="priority" class="form-control select2 inputs"
                                                required>
                                                <option value="" disabled selected></option>
                                                @foreach (\App\Models\Job::PRIORITY_SELECT ?? [] as $priority)
                                                    <option value="{{ $priority }}">{{ $priority }}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">
                                                Please choose priority.
                                            </div>
                                        </div>
                                        <div class="col-4 mt-2">Job deadline date</div>
                                        <div class="col-8 mt-2">
                                            <input type="text" class="form-control inputs" name="deadline_date"
                                                id="deadline_date" placeholder="Enter deadline date"
                                                data-provider="flatpickr" data-date-format="y-m-d" data-enable-time
                                                readonly>
                                            <div class="invalid-feedback">
                                                Please enter deadline date.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="row">
                                        <div class="col-4 mt-2">Assigned to</div>
                                        <div class="col-8 mt-2">
                                            <select class="form-control select2 inputs" name="assigned_to_id"
                                                id="assigned_to_id">
                                                <option value="" disabled selected></option>
                                                @foreach ($agents ?? [] as $agent)
                                                    <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">
                                                Please choose assigned to.
                                            </div>
                                        </div>
                                        <div class="col-4 mt-2 approved_by_area d-none">Approved by</div>
                                        <div class="col-8 mt-2 approved_by_area d-none">
                                            <select class="form-control select2 inputs" name="approved_by_id"
                                                id="approved_by_id">
                                                <option value="" disabled selected></option>
                                                @foreach ($agents ?? [] as $agent)
                                                    <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">
                                                Please choose assigned to.
                                            </div>
                                        </div>
                                        <div class="col-4 mt-2 job_status_area d-none">Job Status</div>
                                        <div class="col-8 mt-2 job_status_area d-none">
                                            <select class="form-control select2 inputs" name="job_status"
                                                id="job_status">
                                                <option value="" disabled selected></option>
                                                @foreach (\App\Models\Job::JOB_STATUS_SELECT ?? [] as $status)
                                                    <option value="{{ $status }}">{{ $status }}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">
                                                Please choose job status.
                                            </div>
                                        </div>
                                        <div class="col-4 mt-2 d-none request_status_area">Request Status</div>
                                        <div class="col-8 mt-2 d-none request_status_area">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <button type="button" id="approve-button" class="btn btn-success action-approve">
                                                    <i class="ri-check-line align-bottom me-1"></i> Approve
                                                </button>
                                                <button type="button" id="reject-button" class="btn btn-danger action-reject">
                                                    <i class="ri-close-line align-bottom me-1"></i> Reject
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <ul class="nav nav-tabs nav-border-top nav-border-top-primary mb-3"
                                                role="tablist">
                                                <li class="nav-item ">
                                                    <a class="nav-link waves-effect waves-light active"
                                                        data-bs-toggle="tab" href="#notesTab" role="tab"
                                                        aria-selected="true" onclick="scrollAtBottom('notes');">Notes</a>
                                                </li>
                                                <li class="nav-item addHide">
                                                    <a class="nav-link waves-effect waves-light" data-bs-toggle="tab"
                                                        href="#activityLogTab" role="tab"
                                                        aria-selected="false">Activity Log</a>
                                                </li>
                                            </ul>
                                            <div class="tab-content text-muted">
                                                <div class="tab-pane active" id="notesTab" role="tabpanel">
                                                    <div class="row">
                                                        <div data-simplebar class="px-3 mb-2 scrollable-notes"
                                                            id="notes">
                                                        </div>
                                                        <div class="row align-items-center">
                                                            <div class="col-9">
                                                                <label class="visually-hidden"
                                                                    for="inlineFormInputGroupUsername">Enter</label>
                                                                <div class="input-group">
                                                                    <textarea class="form-control" placeholder="Enter notes" rows="2" id="note"></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="col-1">
                                                                <button type="button" id="addNoteButton"
                                                                    class="btn btn-soft-secondary waves-effect action-save">Save
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="tab-pane" id="activityLogTab" role="tabpanel">
                                                    <div class="col-12" id="activityLogDiv">
                                                        <div class="">
                                                            <div class="card-body">
                                                                <div class="acitivity-timeline scrollable-notes"></div>
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
                    </div>
                </div>
            </div>
        </div>
    </form>
    @includeIf('admin.jobs.include.filterForm')
    <div class="row">
        <div class="col-xxl-12">
            <div class="card" id="contactList">
                <div class="card-body">

                    <table id="contacts-table" class="table align-middle" style="width:100%">
                        <thead>
                            <tr class="text-uppercase table-light2">

                                <th scope="col" style="width: 50px;">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="checkAll" value="option"
                                            data-com.bitwarden.browser.user-edited="yes">
                                    </div>
                                </th>
                                <th>ID</th>
                                <th>Type</th>
                                <th>Requested By</th>
                                <th>Assigned To</th>
                                <th>Approved By</th>
                                <th>Deadline</th>
                                <th>Priority</th>
                                <th>Created by</th>
                                <th>Updated at</th>
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
        {{-- <script src=" "></script> --}}


    <script>
        var isUpdate = false;
        let dataObj = {};

        $("#requested_by_id").select2({
            placeholder: "Select requested by",
            allowClear: true,
        })

        $("#type").select2({
            placeholder: "Select type",
            allowClear: true,
        })

        $("#priority").select2({
            placeholder: "Select priority",
            allowClear: true,
        })

        $("#assigned_to_id").select2({
            placeholder: "Select assigned to",
            allowClear: true,
        })

        $("#approved_by_id").select2({
            placeholder: "Select approved by",
            allowClear: true,
        })

        $("#job_status").select2({
            placeholder: "Select job status",
            allowClear: true,
        })

        $("#property_id").select2({
            placeholder: "Select listing",
            allowClear: true,
        })

        $(document).on("change", "#type", function() {
            let showListings = ["Photos/Video Shoot", "Photo Editing", "Video Editing"].indexOf($(this).val()) !== -
                1;
            if (showListings) {
                $(".listings").removeClass("d-none")
            } else {
                $(".listings").addClass("d-none")
            }
        })

        function getProperties() {
            return true;
            $("#property_id").val("").trigger("change");
            $("#property_id").html("");
            $.ajax({
                url: "{{ route('ajax.properties') }}",
                type: 'GET',
                dataType: 'json',
                data: {},
                success: function(result) {
                    let options = "<option value='' disabled selected></option>";
                    result?.data.map(item => {
                        options +=
                            `<option value="${item.id}">[${item.ref_no}] - (Beds: ${item.bed?.name || "" }) - (Unit: ${item.unit_no })</option>`;
                    })
                    $("#property_id").html(options);
                    $("#property_id").val(dataObj?.property_id).trigger("change");
                },
                error: function(result) {
                    console.log(result);
                }
            });
        }
        getProperties()

        let filtersData = [];
        $(document).on("submit", "#searchForm", function(e) {
            e.preventDefault();
            filtersData = $("#searchForm").serialize();
            filtersData = urldecode(filtersData);
            filtersData = QueryStringToJSON(filtersData);
            dataTable.ajax.reload();
        })

        $(document).on("click", "#clearSearchBtn", function() {
            $(".search-inputs").val("").trigger("change");
            $("input[type='checkbox'].search-inputs").prop("checked", false).trigger("change");
            $("#searchSearchBtn").trigger("click")
        })

        $(document).on("click", "#refreshTableButton", function() {
            $("#searchSearchBtn").trigger("click")
        })

        function toggleAdvanceSearch() {
            $("#collapseFilterForm").toggleClass("show")
        }

        function getActivityLog(id) {
            $("#activityLogDiv").find(".acitivity-timeline").html("");
            $.ajax({
                url: "{{ route('ajax.activity-log') }}",
                type: 'GET',
                dataType: 'json',
                data: {
                    subject_model: "Job",
                    subject_id: id
                },
                success: function(result) {
                    let logs = "";
                    result?.data.map(item => {
                        let date = moment(item.created_at).fromNow()
                        let imageUrl =
                            `${item.causer ? item.causer.thumb_url : "{{ URL::asset('assets/images/users/avatar-8.jpg') }}"}`;
                        logs += `<div class="acitivity-item py-3 d-flex">
                                    <div class="flex-shrink-0">
                                        <img src="${imageUrl}" alt=""
                                             class="avatar-xs rounded-circle acitivity-avatar shadow" />
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">${item.causer.name}</h6>
                                        <p class="text-muted mb-2">${item.description}</p>
                                        <small class="mb-0 text-muted">${date}</small>
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



        function getJobNotes() {
            @can('Job Notes List')
                let job_id = $("#id").val();
                $("#notes").html("");
                $.ajax({
                    url: "{{ route('ajax.job-notes') }}",
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        job_id
                    },
                    success: function(result) {
                        let notes = "";
                        result?.data.map(item => {
                            let date = moment(item.created_at).format("DD/MM/YY HH:mm")
                            let imageUrl =
                                `${item.created_by ? item.created_by.thumb_url : "{{ URL::asset('assets/images/users/avatar-8.jpg') }}"}`;
                            notes += `<div class="d-flex mb-4">
                                        <div class="flex-shrink-0">
                                            <img src="${imageUrl}" alt=""
                                                 class="avatar-xs rounded-circle"/>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h5 class="fs-13"><a href="javascript:void(0)">${item.created_by ? item.created_by.name : "System"}</a> <small
                                                    class="text-muted">${date}</small></h5>
                                            <p class="text-muted">${item.note}</p>
                                        </div>
                                    </div>`
                        })
                        $("#notes").html(notes)
                        let element = document.getElementById("notes");
                        element.scrollTop = element.scrollHeight
                    },
                    error: function(result) {
                        console.log(result);
                    }
                });
            @endcan
        }

        $(document).on("click", "#addNoteButton", function() {
            @can('Job Notes Create')
                let id = $("#id").val();

                if (id == "") {
                    showToastify("Please save record before adding a note.", "error")
                    return;
                }

                let note = $("#note").val();
                if (note == "") {
                    return;
                }

                var formData = new FormData();
                formData.append("job_id", id)
                formData.append("note", note)
                $.ajax({
                    url: "{{ route('admin.jobs.add-note') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: formData,
                    cache: false,
                    processData: false,
                    contentType: false,
                    success: function(result) {
                        if (result.status == '1') {
                            showToastify(result.msg, "success")
                            $("#note").val("").trigger("change");
                            getJobNotes()
                        }
                    },
                    error: function(jqXhr, json, errorThrown) {
                        let data = jqXhr.responseJSON;
                        if (data.errors) {
                            $.each(data.errors, function(index, item) {
                                showToastify(item[0], "error")
                            })
                        }
                        if (jqXhr.status == 500 || jqXhr.status == 400) {
                            showToastify(data.msg, "error")
                        }
                    },
                });
            @endcan
        })

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

        $("#checkAll").change(function() {
            $(".selectable-checkbox").prop("checked", this.checked)
        })

        $("#newBtn").click(function() {
            @can('Jobs Create')
                dataObj = {}
                isUpdate = false;
                $(".inputs").prop("disabled", false);
                $("select.inputs").val("").trigger("change");
                $("#mainForm").trigger("reset")
                $("#mainForm").removeClass("was-validated")
                $("#id").val("")
                $("#saveBtn, #closeBtn").removeClass("d-none");
                $("#newBtn, #editBtn, #deleteBtn, #multiDeleteBtn").addClass("d-none");
                $(".approved_by_area, .job_status_area, .request_status_area").addClass("d-none")
                toggleForm(true)
            @endcan
        });

        $(document).on("click", "#editBtn", function() {
            @can('Jobs Edit')
                $(".inputs").prop("disabled", false)
                editData($('#id').val());
                $("#saveBtn, #closeBtn").removeClass("d-none");
                $("#newBtn, #editBtn, #deleteBtn, #multiDeleteBtn").addClass("d-none");
            @endcan
        });

        $(document).on('click', '.edit-link', function(event) {
            @can('Jobs Edit')
                $(".inputs").prop("disabled", false)
                let id = $(this).data('id');
                editData(id);
            @endcan
        });

        $(document).on("click", "#closeBtn", function() {
            toggleForm(false)
        })

        $(document).on('click', '#deleteBtn', function(event) {
            @can('Jobs Delete')
                deleteData($('#id').val());
            @endcan
        });

        function editData(id) {
            @can('Jobs Edit')
                isUpdate = true;
                toggleForm(true)
                getJob(id);
                $("#saveBtn, #closeBtn").removeClass("d-none");
                $("#newBtn, #editBtn, #deleteBtn, #multiDeleteBtn").addClass("d-none");
            @endcan
        }

        function getJob(id) {
            $.ajax({
                url: "{{ route('admin.jobs.get') }}",
                type: "get",
                data: {
                    id: id
                },
                success: function(result) {
                    if (result.status == '1') {
                        dataObj = result.data
                        Object.keys(result.data).map((index) => {
                            $(`#${index}`).val(result.data[index]).trigger("change")
                        })

                        if (dataObj.job_status == "Created") {
                            $(".request_status_area").removeClass("d-none")
                            $(".approved_by_area, .job_status_area").addClass("d-none")
                        } else {
                            $(".request_status_area").addClass("d-none")
                            $(".approved_by_area, .job_status_area").removeClass("d-none")
                        }
                        getJobNotes()
                        toggleForm(true)
                        scrollTop();
                        getActivityLog(id)
                    }
                }
            });
        }

        $(document).on('submit', '#mainForm', function(event) {
            $("#saveBtn").prop('disabled', true);
            event.preventDefault();
            var formData = new FormData($(this)[0]);
            $.ajax({
                url: isUpdate ? "{{ route('admin.jobs.update') }}" : "{{ route('admin.jobs.store') }}",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function(result) {
                    if (result.status == '1') {
                        showToastify(result.msg, "success")
                        $("#saveBtn").prop('disabled', false); //enable submit button
                        $('#mainForm').trigger('reset'); //
                        $("select.inputs").val("").trigger("change")
                        toggleForm(false)
                        dataTable.row()
                    .draw(); // redraw the datatable show we don't need to reload the page to see the changes
                        isUpdate =
                        false; // set isUpdate to false so that when we try to add new category it will be create request
                    } else if (result.status == '2') {
                        $("#saveBtn").prop('disabled', false);
                        let error_message = '';

                        $.each(result.errors, function(key, value) {
                            error_message += '' + key + ': ' + value + ' | ';
                        });
                        showToastify(error_message, "error")
                    } else {
                        $("#saveBtn").prop('disabled', false);
                        showToastify(result.msg, "error")
                    }
                },
                error: function(jqXhr, json, errorThrown) {
                    $("#saveBtn").prop('disabled', false);
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

        $(document).on("click", "#approve-button", function() {
            @can('Jobs Approve')
                let id = $("#id").val();
                swal({
                        title: "Are you sure?",
                        text: "Want to approve",
                        icon: 'warning',
                        buttons: true,
                        dangerMode: true,
                    })
                    .then((willDelete) => {
                        if (willDelete) { // on confirm delete the category
                            $("#approve-button").prop("disabled", true);
                            $.ajax({
                                url: "{{ route('admin.jobs.status') }}",
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    id,
                                    status: "Approved"
                                },
                                success: function(result) {
                                    $("#approve-button").prop("disabled", false);
                                    if (result.status == '1') { //if successfully deleted
                                        showToastify(result.msg, "success")
                                        dataTable.row()
                                    .draw(); // redraw the datatable show we don't need to reload the page to see the changes
                                        toggleForm(false)
                                    } else { // something went wrong on backend
                                        showToastify(result.msg, "error")
                                    }
                                },
                                error: function(result) {
                                    $("#approve-button").prop("disabled", false);
                                    console.log(result);
                                }
                            });
                        }
                    });
            @endcan
        })

        $(document).on("click", "#reject-button", function() {
            @can('Jobs Reject')
                let id = $("#id").val();
                swal({
                        title: "Are you sure?",
                        text: "Want to reject",
                        icon: 'warning',
                        buttons: true,
                        dangerMode: true,
                    })
                    .then((willDelete) => {
                        if (willDelete) { // on confirm delete the category
                            $("#reject-button").prop("disabled", true);
                            $.ajax({
                                url: "{{ route('admin.jobs.status') }}",
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    id,
                                    status: "Canceled"
                                },
                                success: function(result) {
                                    $("#reject-button").prop("disabled", false);
                                    if (result.status == '1') { //if successfully deleted
                                        showToastify(result.msg, "success")
                                        dataTable.row()
                                    .draw(); // redraw the datatable show we don't need to reload the page to see the changes
                                        toggleForm(false)
                                    } else { // something went wrong on backend
                                        showToastify(result.msg, "error")
                                    }
                                },
                                error: function(result) {
                                    $("#reject-button").prop("disabled", false);
                                    console.log(result);
                                }
                            });
                        }
                    });
            @endcan
        })

        let dataTable = $('#contacts-table').DataTable({ //datatable initialization
            "order": [
                [0, "DESC"]
            ],
            language: {
                searchPlaceholder: "Search jobs"
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
                url: "{{ route('admin.jobs.dt') }}",
                type: "post",
                data: function(d) {
                    d.requested_by = filtersData?.search_requested_by;
                    d.types = filtersData?.search_types;
                    d.priorities = filtersData?.search_priorities;
                    d.assigned_to = filtersData?.search_assigned_to;
                    d.approved_by = filtersData?.search_approved_by;
                    d.statuses = filtersData?.search_statuses;
                    d.created_from = filtersData?.search_created_from;
                    d.created_to = filtersData?.search_created_to;
                    d.deleted = filtersData?.search_deleted;
                },
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
                $(`@includeIf('admin.jobs.include.filterButtons')`).insertAfter(".dataTables_filter input")
            },

            "aoColumns": [{
                    data: null,
                    orderable: false,
                }, {
                    data: 'id',
                    name: 'id',
                }, {
                    data: 'type',
                    name: 'type',
                }, {
                    data: 'requested_by_id',
                    name: 'requested_by_id',
                }, {
                    data: 'assigned_to_id',
                    name: 'assigned_to_id',
                }, {
                    data: 'approved_by_id',
                    name: 'approved_by_id',
                }, {
                    data: 'deadline_date',
                    name: 'deadline_date',
                }, {
                    data: 'priority',
                    name: 'priority',
                }, {
                    data: 'created_by_id',
                    name: 'created_by_id',
                }, {
                    data: 'updated_at',
                    name: 'updated_at',
                },
                {
                    data: 'job_status',
                    name: 'job_status',
                }, {
                    data: null,
                    orderable: false,
                }
            ],
            "columnDefs": [{
                    "targets": 0,
                    render: function(type, row, data, meta) {
                        return `<div class="form-check">
                                            <input class="form-check-input selectable-checkbox" type="checkbox" name="chk_child" value="` +
                            data.id + `">
                                </div>`;
                    }
                },
                {
                    "targets": 2,
                    render: function(type, row, data, meta) {
                        return `<a href="javascript:void(0)" class="text-primary" onclick="viewData('` +
                            data.id + `')">` + data.type + `</a>`;
                    }
                },
                {
                    "targets": 3,
                    render: function(type, row, data, meta) {
                        if (!data.requested_by) return "-";
                        return `<div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-2">
                                        <img src="${data.requested_by.thumb_url}" alt="" class="avatar-xs rounded-circle">
                                    </div>
                                    <div class="flex-grow-1">${data.requested_by.name}</div>
                                </div>`;
                    }
                },
                {
                    "targets": 4,
                    render: function(type, row, data, meta) {
                        if (!data.assigned_to) return "-";
                        return `<div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-2">
                                        <img src="${data.assigned_to.thumb_url}" alt="" class="avatar-xs rounded-circle">
                                    </div>
                                    <div class="flex-grow-1">${data.assigned_to.name}</div>
                                </div>`;
                    }
                },
                {
                    "targets": 5,
                    render: function(type, row, data, meta) {
                        if (!data.approved_by) return "-";
                        return `<div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-2">
                                        <img src="${data.approved_by.thumb_url}" alt="" class="avatar-xs rounded-circle">
                                    </div>
                                    <div class="flex-grow-1">${data.approved_by.name}</div>
                                </div>`;
                    }
                }, {
                    "targets": 6,
                    render: function(type, row, data, meta) {
                        if (data.deadline_date == null) {
                            return '-';
                        } else {
                            return moment(data.deadline_date).format('MM/DD/YYYY hh:mm:ss');
                        }
                    }
                }, {
                    "targets": 7,
                    render: function(type, row, data, meta) {
                        return data.priority || "-";
                    }
                }, {
                    targets: 8,
                    visible: true,
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
                    targets: 9,
                    visible: true,
                    render: function(type, row, data, meta) {
                        if (data.updated_at == null) {
                            return '-';
                        } else {
                            return moment(data.updated_at).format('MM/DD/YYYY hh:mm:ss');
                        }
                    }
                }, {
                    targets: 10,
                    visible: true,
                    render: function(type, row, data, meta) {
                        return data.job_status || "-";
                    }
                },
                {
                    targets: 11,
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

        function viewData(id) {
            @can('Jobs View')
                $(".inputs").prop("disabled", true)
                $("#id").val(id)
                $("#saveBtn, #newBtn, #multiDeleteBtn").addClass("d-none");
                $("#editBtn, #closeBtn, #deleteBtn").removeClass("d-none");
                getJob(id)
                scrollTop()
            @endcan
        }

        function deleteData(id) {
            @can('Jobs Delete')
                swal({
                        title: "Are you sure?",
                        text: "Once deleted, you will not be able to use this job details!",
                        icon: 'warning',
                        buttons: true,
                        dangerMode: true,
                    })
                    .then((willDelete) => {
                        if (willDelete) { // on confirm delete the category
                            $.ajax({
                                url: "{{ route('admin.jobs.delete') }}",
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    id
                                },
                                success: function(result) {
                                    if (result.status == '1') { //if successfully deleted
                                        showToastify(result.msg, "success")
                                        dataTable.row()
                                    .draw(); // redraw the datatable show we don't need to reload the page to see the changes
                                        toggleForm(false)
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

        function deleteMultiple() {
            @can('Jobs Delete')
                let ids = [];
                $(".selectable-checkbox").map((index, item) => {
                    item.checked ? ids.push(item.value) : []
                })
                if (ids.length > 0) {
                    swal({
                            title: "Attention!!",
                            text: "You are trying to delete multiple jobs. Once deleted, you will not be able to use these jobs!",
                            icon: "warning",
                            buttons: true,
                            dangerMode: true,
                        })
                        .then((willDelete) => {
                            if (willDelete) {
                                $.ajax({
                                    url: "{{ route('admin.jobs.multi.delete') }}",
                                    type: 'POST',
                                    dataType: 'json',
                                    data: {
                                        ids
                                    },
                                    success: function(result) {
                                        if (result.status == '1') {
                                            showToastify(result.msg, "success")
                                            dataTable.row().draw();
                                            toggleForm(false)
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

                    @can('Contacts Delete')
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
                if ($cell.index() === 9 || $cell.index() === 10) {
                    // console.log($cell);
                } else {
                    viewData(data.id);
                    highlightRow($(this));

                }
            } // end if
        }); // table row click

        $("#sidebarJobs").addClass('collapse show');
        $('#jobActive').addClass('active');
        $("#jobSidebar").addClass('active');
    </script>
@endsection
