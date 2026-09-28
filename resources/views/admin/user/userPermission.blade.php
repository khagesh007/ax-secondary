@extends('admin.layouts.master')
@section('title')
    User Permission Management - Roles
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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"
          type="text/css"/>
@endsection
@section('content')
    <form class="row" id="mainForm">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title mb-0 ">User Permissions</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">User Management</a></li>
                            <li class="breadcrumb-item">User List</li>
                            <li class="breadcrumb-item active">User Permissions</li>
                        </ol>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="form-check form-switch form-switch-right form-switch-md">
                            @can('Users Change Permission')
                            <button type="submit" class="btn btn-success action-save" id="saveBtn">Save</button>
                            @endcan

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="card">

            </div>
        </div>

        <div class="collapse slideIn show" id="formSection">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header align-items-center ">
                            <div class="row">
                                <div class="col-4 d-flex">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0"><img src="{{$user->thumb_url}}" alt="" class="avatar-xs rounded"></div><div class="flex-grow-1 ms-2 name">
                                                <a href="javascript:void(0)" class="text-primary" onclick="viewUser('280')">{{$user->full_name}}</a></div></div>
                                    </div>
                                </div>
                                <div class="col-8">
                                    <div class="row">
                                        <div class="col-4 mt-2">
                                            <label for="name" class="form-label">Roles *</label>
                                        </div>
                                        <div class="col-8 mt-2">
                                            <select name='roles[]' id="roles" class="form-control select2 inputs"
                                                    multiple>
                                                @foreach ($roles ?? [] as $role)
                                                    @if($user->hasRole($role->name))
                                                        <option value="{{ $role->id }}" selected>{{ $role->name }}</option>
                                                    @else

                                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>



                        </div>
                        <div class="card-body">
                            <div>
                                <input type="hidden" id="user_id" name="user_id" value="{{$user->id}}">

                                <div class="row">


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
                                            @foreach($permissions ?? [] as $group => $groups)
                                                @php $groupSlug = slugify($group) @endphp
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-start align-items-center gap-2">
                                                            <h5><strong>{{ $group }}</strong> </h5>
                                                            <div class="form-check">
                                                                <input class="form-check-input inputs permission-groups" value="{{ $groupSlug }}" type="checkbox" id="{{ $groupSlug }}-group">
                                                                <label class="form-check-label" for="{{ $groupSlug }}-group">
                                                                    Select All
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            @foreach($groups as $permission)
                                                                <div class="col-3">
                                                                    <div class="form-check">
                                                                        <input class="form-check-input inputs {{ $groupSlug }}-permission" name="permissions[]" type="checkbox" value="{{ $permission->id }}" id="permission-{{ $permission->id }}">
                                                                        <label class="form-check-label" for="permission-{{ $permission->id }}">
                                                                            {{ $permission->name }}
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
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
                <div class="card-body d-none">
                    <table id="categoryDatatable" class="table align-middle" style="width:100%">
                        <thead class="table-light">
                        <tr>
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

        $(document).on("change", "#select-all-permissions", function (){
            $(".permission-groups").prop("checked", this.checked).trigger("change");
        })

        $(document).on("change", ".permission-groups", function (){
            let group = $(this).val();
            $(`.${group}-permission`).prop("checked", this.checked).trigger("change");
        })

        $("#newBtn").click(function () {
            @can("Roles Create")
            $("#id").val("")
            isUpdate = false;
            $(".inputs").prop("disabled", false)
            $("select.inputs").val("").trigger("change");
            $("#mainForm").trigger("reset")
            $("#saveBtn, #closeBtn").removeClass("d-none");
            $("#newBtn, #editBtn, #deleteBtn, #multiDeleteBtn").addClass("d-none");
            toggleForm(true)
            @endcan
        });

        $(document).on("click", "#editBtn", function () {
            @can("Roles Edit")
            $(".inputs").prop("disabled", false)
            editData($('#id').val());
            $("#saveBtn, #closeBtn").removeClass("d-none");
            $("#newBtn, #editBtn, #deleteBtn, #multiDeleteBtn").addClass("d-none");
            toggleForm(true)
            @endcan
        });





        $(document).on('click', '.edit-link', function (event) { //edit link click event it will fetch single cateogry details and put it on form
            @can("Roles Edit")
            $(".inputs").prop("disabled", false)
            let id = $(this).data('id');
            editData(id);
            $("#saveBtn, #closeBtn").removeClass("d-none");
            $("#newBtn, #editBtn, #deleteBtn, #multiDeleteBtn").addClass("d-none");
            toggleForm(true)
            @endcan
        });

        $("#checkAll").change(function () {
            $(".selectable-checkbox").prop("checked", this.checked)
        })
        $("#roles").change(function (){
            let ids = $(this).val();

            getRolePermissions(ids)
        })
        //On dom ready trigger #roles change event
        $(document).ready(function (){
            getUserPermissions("{{$user->id}}")
        })

        function getRolePermissions(id) {
            $(".permission-groups").prop("checked", false).trigger("change")
            $.ajax({
                url: "{{route('admin.user.users.role.permissions')}}",
                type: 'POST',
                dataType: 'json',
                data: {
                    id:id,
                    user_id : "{{ $user->id }}",

                },
                success: function (result) {
                    if (result.status == '1') {
                        $('#id').val(result.data.id);
                        let data = result.data
                        if (result.data) {

                            data.map(item => {
                                $(`#permission-${item.id}`).prop("checked", true).trigger("change")
                            })
                        }


                    } else {
                        showToastify(result.msg, "error")
                    }
                },
                error: function (result) {
                    console.log(result);
                }
            });
        }
        function getUserPermissions(id) {
            $(".permission-groups").prop("checked", false).trigger("change")
            $.ajax({
                url: "{{route('admin.user.users.get.permissions')}}",
                type: 'POST',
                dataType: 'json',
                data: {
                    id:id,
                    user_id : "{{ $user->id }}",

                },
                success: function (result) {
                    if (result.status == '1') {
                        $('#id').val(result.data.id);
                        let data = result.data

                        if (data.length > 0) {
                            data.map(item => {
                                $(`#permission-${item.id}`).prop("checked", true).trigger("change")
                            })
                        }else{
                            $("#roles").trigger("change");
                        }

                    } else {
                        showToastify(result.msg, "error")
                    }
                },
                error: function (result) {
                    console.log(result);
                }
            });
        }



        $(document).on('click', '#editBtn', function (event) {
            @can("Roles Edit")
            editData($('#id').val());
            @endcan
        });

        function editData(id) {
            @can("Roles Edit")
            $("#id").val(id);
            isUpdate = true;
            getRolePermissions(id);
            $("#deleteBtn, #editBtn, #newBtn, #multiDeleteBtn").addClass("d-none");
            $("#saveBtn, #closeBtn").removeClass("d-none");
            @endcan
        }

        @can('Users Change Permission')
        $(document).on('submit', '#mainForm', function (event) {
            $("#saveBtn").prop('disabled', true);
            event.preventDefault();
            let formData = new FormData($(this)[0]);
            $.ajax({
                url:  "{{route('admin.user.users.update-permissions')}}",
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
        @endcan





        $('#userActive').addClass('active');
        $('#sidbarUsers').addClass('active');
        $("#sidebarAuth").addClass('collapse show');




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

                    @can('Roles Delete')
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














        $('#userActive').addClass('active');

    </script>
@endsection
